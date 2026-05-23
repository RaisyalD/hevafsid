<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RejectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reject_code', 'product_id', 'product_batch_id', 'user_id',
        'quantity', 'cost_price', 'total_loss', 'reject_type',
        'reason', 'reject_date',
    ];

    protected function casts(): array
    {
        return [
            'cost_price'  => 'decimal:2',
            'total_loss'  => 'decimal:2',
            'reject_date' => 'date',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'REJ-' . now()->format('Ym') . '-';
        $last   = static::where('reject_code', 'like', $prefix . '%')
            ->orderByDesc('reject_code')
            ->value('reject_code');

        $seq = $last ? (int) substr($last, -3) + 1 : 1;
        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getRejectTypeLabelAttribute(): string
    {
        return match($this->reject_type) {
            'damaged'   => 'Rusak',
            'defective' => 'Cacat Produksi',
            'expired'   => 'Kadaluarsa',
            'lost'      => 'Hilang',
            default     => 'Lainnya',
        };
    }
}
