<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FifoDetail extends Model
{
    protected $fillable = [
        'outgoing_transaction_id', 'product_batch_id',
        'qty_taken', 'cost_price', 'subtotal_hpp',
    ];

    protected function casts(): array
    {
        return [
            'cost_price'    => 'decimal:2',
            'subtotal_hpp'  => 'decimal:2',
        ];
    }

    public function outgoingTransaction(): BelongsTo
    {
        return $this->belongsTo(OutgoingTransaction::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ProductBatch::class, 'product_batch_id');
    }
}
