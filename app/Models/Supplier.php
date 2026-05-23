<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'contact_person', 'phone', 'email',
        'address', 'city', 'is_active', 'notes',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(IncomingTransaction::class);
    }

    public function getTotalPurchaseAttribute(): float
    {
        return $this->incomingTransactions()->sum('total_cost');
    }
}
