<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCard extends Model
{
    protected $fillable = [
        'product_id', 'product_batch_id', 'transaction_date',
        'transaction_type', 'reference_code', 'qty_in', 'qty_out',
        'balance', 'cost_price', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'date',
            'cost_price'       => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }

    public function getTransactionTypeLabelAttribute(): string
    {
        return match($this->transaction_type) {
            'incoming'   => 'Barang Masuk',
            'outgoing'   => 'Barang Keluar',
            'reject'     => 'Barang Reject',
            'adjustment' => 'Penyesuaian',
            default      => $this->transaction_type,
        };
    }
}
