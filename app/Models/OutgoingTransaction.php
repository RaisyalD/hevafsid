<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OutgoingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'product_id', 'user_id', 'quantity',
        'sell_price', 'total_hpp', 'total_revenue', 'gross_profit',
        'transaction_date', 'reference_number', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'sell_price'       => 'decimal:2',
            'total_hpp'        => 'decimal:2',
            'total_revenue'    => 'decimal:2',
            'gross_profit'     => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fifoDetails(): HasMany
    {
        return $this->hasMany(FifoDetail::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'TRX-OUT-' . now()->format('Ym') . '-';
        $last   = static::where('transaction_code', 'like', $prefix . '%')
            ->orderByDesc('transaction_code')
            ->value('transaction_code');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
