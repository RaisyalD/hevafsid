<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'batch_code', 'qty_initial', 'qty_remaining',
        'cost_price', 'received_date', 'expiry_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_price'    => 'decimal:2',
            'received_date' => 'date',
            'expiry_date'   => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function incomingTransaction(): HasMany
    {
        return $this->hasMany(IncomingTransaction::class);
    }

    public function fifoDetails(): HasMany
    {
        return $this->hasMany(FifoDetail::class);
    }

    public function rejectItems(): HasMany
    {
        return $this->hasMany(RejectItem::class);
    }

    public function isDepleted(): bool
    {
        return $this->qty_remaining === 0;
    }

    public function getUsedQtyAttribute(): int
    {
        return $this->qty_initial - $this->qty_remaining;
    }

    // Generate unique batch code: BATCH-YYYYMM-SEQUENCE
    public static function generateBatchCode(): string
    {
        $prefix = 'BATCH-' . now()->format('Ym') . '-';
        $last   = static::where('batch_code', 'like', $prefix . '%')
            ->orderByDesc('batch_code')
            ->value('batch_code');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
