<?php

namespace App\Services;

use App\Models\FifoDetail;
use App\Models\FinancialTransaction;
use App\Models\InventoryCard;
use App\Models\OutgoingTransaction;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\RejectItem;
use Illuminate\Support\Facades\DB;

class FifoService
{
    /**
     * Process an outgoing transaction using FIFO method.
     *
     * Deducts stock from the oldest active batches first, records
     * FIFO detail lines, updates inventory card, and posts to cashflow.
     *
     * @throws \Exception when stock is insufficient
     */
    public function processOutgoing(
        Product $product,
        int     $quantity,
        float   $sellPrice,
        int     $userId,
        string  $transactionDate,
        ?string $referenceNumber = null,
        ?string $notes = null
    ): OutgoingTransaction {
        return DB::transaction(function () use (
            $product, $quantity, $sellPrice, $userId,
            $transactionDate, $referenceNumber, $notes
        ) {
            // Validate stock availability
            if ($product->stock_total < $quantity) {
                throw new \Exception(
                    "Stok tidak cukup. Tersedia: {$product->stock_total}, diminta: {$quantity}."
                );
            }

            $code = OutgoingTransaction::generateCode();
            $totalRevenue = $quantity * $sellPrice;

            $outgoing = OutgoingTransaction::create([
                'transaction_code' => $code,
                'product_id'       => $product->id,
                'user_id'          => $userId,
                'quantity'         => $quantity,
                'sell_price'       => $sellPrice,
                'total_hpp'        => 0, // calculated below
                'total_revenue'    => $totalRevenue,
                'gross_profit'     => 0, // calculated below
                'transaction_date' => $transactionDate,
                'reference_number' => $referenceNumber,
                'notes'            => $notes,
            ]);

            $remaining  = $quantity;
            $totalHpp   = 0.0;

            // Fetch oldest active batches with remaining stock (FIFO order)
            $batches = ProductBatch::where('product_id', $product->id)
                ->where('status', 'active')
                ->where('qty_remaining', '>', 0)
                ->orderBy('received_date')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $take        = min($remaining, $batch->qty_remaining);
                $subtotalHpp = $take * $batch->cost_price;
                $totalHpp   += $subtotalHpp;

                // Record which batch contributed which qty
                FifoDetail::create([
                    'outgoing_transaction_id' => $outgoing->id,
                    'product_batch_id'        => $batch->id,
                    'qty_taken'               => $take,
                    'cost_price'              => $batch->cost_price,
                    'subtotal_hpp'            => $subtotalHpp,
                ]);

                // Deduct from batch
                $batch->qty_remaining -= $take;
                if ($batch->qty_remaining === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $remaining -= $take;
            }

            // Update totals on the outgoing transaction
            $grossProfit = $totalRevenue - $totalHpp;
            $outgoing->update([
                'total_hpp'    => $totalHpp,
                'gross_profit' => $grossProfit,
            ]);

            // Decrement product stock total
            $product->decrement('stock_total', $quantity);
            $newBalance = $product->fresh()->stock_total;

            // Append to inventory card (ledger)
            InventoryCard::create([
                'product_id'       => $product->id,
                'product_batch_id' => null,
                'transaction_date' => $transactionDate,
                'transaction_type' => 'outgoing',
                'reference_code'   => $code,
                'qty_in'           => 0,
                'qty_out'          => $quantity,
                'balance'          => $newBalance,
                'cost_price'       => $totalHpp / $quantity, // weighted avg for display
                'notes'            => $notes,
            ]);

            // Post cash-in to financial journal
            FinancialTransaction::create([
                'transaction_code' => FinancialTransaction::generateCode(),
                'user_id'          => $userId,
                'type'             => 'cash_in',
                'category'         => 'sales',
                'reference_code'   => $code,
                'amount'           => $totalRevenue,
                'description'      => "Penjualan {$product->name} ({$quantity} {$product->unit})",
                'transaction_date' => $transactionDate,
            ]);

            return $outgoing->fresh(['fifoDetails.batch', 'product']);
        });
    }

    /**
     * Process incoming goods: create a new FIFO batch and record the inventory card entry.
     */
    public function processIncoming(
        Product  $product,
        int      $quantity,
        float    $costPrice,
        int      $supplierId,
        int      $userId,
        string   $receivedDate,
        ?string  $invoiceNumber = null,
        ?string  $notes = null
    ): array {
        return DB::transaction(function () use (
            $product, $quantity, $costPrice, $supplierId,
            $userId, $receivedDate, $invoiceNumber, $notes
        ) {
            $batchCode = ProductBatch::generateBatchCode();

            // Create new FIFO batch
            $batch = ProductBatch::create([
                'product_id'    => $product->id,
                'batch_code'    => $batchCode,
                'qty_initial'   => $quantity,
                'qty_remaining' => $quantity,
                'cost_price'    => $costPrice,
                'received_date' => $receivedDate,
                'status'        => 'active',
            ]);

            $txCode = \App\Models\IncomingTransaction::generateCode();

            $incoming = \App\Models\IncomingTransaction::create([
                'transaction_code' => $txCode,
                'product_id'       => $product->id,
                'supplier_id'      => $supplierId ?: null,
                'product_batch_id' => $batch->id,
                'user_id'          => $userId,
                'quantity'         => $quantity,
                'cost_price'       => $costPrice,
                'total_cost'       => $quantity * $costPrice,
                'received_date'    => $receivedDate,
                'invoice_number'   => $invoiceNumber,
                'notes'            => $notes,
            ]);

            // Increment product stock total
            $product->increment('stock_total', $quantity);
            $newBalance = $product->fresh()->stock_total;

            // Append to inventory card
            InventoryCard::create([
                'product_id'       => $product->id,
                'product_batch_id' => $batch->id,
                'transaction_date' => $receivedDate,
                'transaction_type' => 'incoming',
                'reference_code'   => $txCode,
                'qty_in'           => $quantity,
                'qty_out'          => 0,
                'balance'          => $newBalance,
                'cost_price'       => $costPrice,
                'notes'            => $notes,
            ]);

            // Post cash-out (purchase cost) to financial journal
            FinancialTransaction::create([
                'transaction_code' => FinancialTransaction::generateCode(),
                'user_id'          => $userId,
                'type'             => 'cash_out',
                'category'         => 'purchase',
                'reference_code'   => $txCode,
                'amount'           => $quantity * $costPrice,
                'description'      => "Pembelian {$product->name} ({$quantity} {$product->unit})",
                'transaction_date' => $receivedDate,
            ]);

            return ['batch' => $batch, 'incoming' => $incoming];
        });
    }

    /**
     * Process a reject item: deduct stock (preferring specified batch) and record loss.
     */
    public function processReject(
        Product       $product,
        int           $quantity,
        ?ProductBatch $batch,
        string        $rejectType,
        string        $reason,
        int           $userId,
        string        $rejectDate
    ): RejectItem {
        return DB::transaction(function () use (
            $product, $quantity, $batch, $rejectType, $reason, $userId, $rejectDate
        ) {
            // Determine cost price from the batch or product default
            $costPrice = $batch ? $batch->cost_price : $product->default_cost_price;

            $reject = RejectItem::create([
                'reject_code'      => RejectItem::generateCode(),
                'product_id'       => $product->id,
                'product_batch_id' => $batch?->id,
                'user_id'          => $userId,
                'quantity'         => $quantity,
                'cost_price'       => $costPrice,
                'total_loss'       => $quantity * $costPrice,
                'reject_type'      => $rejectType,
                'reason'           => $reason,
                'reject_date'      => $rejectDate,
            ]);

            // Deduct from specified batch or oldest active batch
            if ($batch) {
                $batch->qty_remaining = max(0, $batch->qty_remaining - $quantity);
                if ($batch->qty_remaining === 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();
            }

            // Decrement product stock total
            $product->decrement('stock_total', $quantity);
            $newBalance = $product->fresh()->stock_total;

            // Append to inventory card
            InventoryCard::create([
                'product_id'       => $product->id,
                'product_batch_id' => $batch?->id,
                'transaction_date' => $rejectDate,
                'transaction_type' => 'reject',
                'reference_code'   => $reject->reject_code,
                'qty_in'           => 0,
                'qty_out'          => $quantity,
                'balance'          => $newBalance,
                'cost_price'       => $costPrice,
                'notes'            => $reason,
            ]);

            // Post loss to financial journal
            FinancialTransaction::create([
                'transaction_code' => FinancialTransaction::generateCode(),
                'user_id'          => $userId,
                'type'             => 'cash_out',
                'category'         => 'reject_loss',
                'reference_code'   => $reject->reject_code,
                'amount'           => $reject->total_loss,
                'description'      => "Kerugian reject {$product->name}: {$reason}",
                'transaction_date' => $rejectDate,
            ]);

            return $reject;
        });
    }

    /**
     * Calculate the current FIFO valuation (total cost of all active batches) for a product.
     */
    public function getStockValuation(Product $product): float
    {
        return (float) ProductBatch::where('product_id', $product->id)
            ->where('status', 'active')
            ->selectRaw('SUM(qty_remaining * cost_price) as valuation')
            ->value('valuation') ?? 0;
    }
}
