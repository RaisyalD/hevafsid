<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomingTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'product_id', 'supplier_id', 'product_batch_id',
        'user_id', 'quantity', 'cost_price', 'total_cost',
        'received_date', 'invoice_number', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'cost_price'    => 'decimal:2',
            'total_cost'    => 'decimal:2',
            'received_date' => 'date',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
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
        $prefix = 'TRX-IN-' . now()->format('Ym') . '-';
        $last   = static::where('transaction_code', 'like', $prefix . '%')
            ->orderByDesc('transaction_code')
            ->value('transaction_code');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
