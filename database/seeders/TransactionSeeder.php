<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\FifoService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $fifo      = app(FifoService::class);
        $adminId   = User::whereHas('role', fn($q) => $q->where('name', 'admin_gudang'))->value('id')
                   ?? User::first()->id;
        $products  = Product::all();
        $suppliers = Supplier::all();

        // ── Incoming: 1 batch per product, ~30 days ago ──────────────────
        foreach ($products as $product) {
            $date = Carbon::now()->subDays(rand(25, 30))->toDateString();

            $fifo->processIncoming(
                product:       $product,
                quantity:      rand(20, 50),
                costPrice:     $product->default_cost_price,
                supplierId:    $suppliers->random()->id,
                userId:        $adminId,
                receivedDate:  $date,
                invoiceNumber: 'INV-' . strtoupper(substr($product->sku, -3)) . '-' . rand(1000, 9999),
            );
        }

        // ── Outgoing: simulate sales over last 30 days ───────────────────
        for ($daysAgo = 29; $daysAgo >= 0; $daysAgo--) {
            $salesCount = rand(0, 2);
            if ($salesCount === 0) continue;

            $date = Carbon::now()->subDays($daysAgo)->toDateString();

            for ($i = 0; $i < $salesCount; $i++) {
                $product = $products->random()->fresh();
                if ($product->stock_total < 1) continue;

                $maxQty    = min($product->stock_total, rand(1, 3));
                $sellPrice = $product->sell_price;

                try {
                    $fifo->processOutgoing(
                        product:         $product,
                        quantity:        $maxQty,
                        sellPrice:       $sellPrice,
                        userId:          $adminId,
                        transactionDate: $date,
                        referenceNumber: 'NOTA-' . rand(1000, 9999),
                    );
                } catch (\Exception $e) {
                    // Skip if stock exhausted
                }
            }
        }

        $this->command->info('✅ Transactions seeded successfully!');
    }
}