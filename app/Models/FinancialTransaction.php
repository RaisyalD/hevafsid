<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code', 'user_id', 'type', 'category',
        'reference_code', 'amount', 'description', 'transaction_date',
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateCode(): string
    {
        $prefix = 'FIN-' . now()->format('Ym') . '-';
        $last   = static::where('transaction_code', 'like', $prefix . '%')
            ->orderByDesc('transaction_code')
            ->value('transaction_code');

        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'cash_in' ? 'Kas Masuk' : 'Kas Keluar';
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'sales'        => 'Penjualan',
            'purchase'     => 'Pembelian',
            'reject_loss'  => 'Kerugian Reject',
            'operational'  => 'Operasional',
            default        => 'Lainnya',
        };
    }
}
