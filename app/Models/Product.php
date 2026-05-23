<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id', 'sku', 'barcode', 'name', 'description', 'unit',
        'sell_price', 'default_cost_price', 'stock_total', 'min_stock',
        'image', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sell_price'         => 'decimal:2',
            'default_cost_price' => 'decimal:2',
            'is_active'          => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(ProductBatch::class);
    }

    public function activeBatches(): HasMany
    {
        return $this->hasMany(ProductBatch::class)
            ->where('status', 'active')
            ->orderBy('received_date');
    }

    public function incomingTransactions(): HasMany
    {
        return $this->hasMany(IncomingTransaction::class);
    }

    public function outgoingTransactions(): HasMany
    {
        return $this->hasMany(OutgoingTransaction::class);
    }

    public function rejectItems(): HasMany
    {
        return $this->hasMany(RejectItem::class);
    }

    public function inventoryCards(): HasMany
    {
        return $this->hasMany(InventoryCard::class)->orderBy('transaction_date');
    }

    public function isLowStock(): bool
    {
        return $this->stock_total <= $this->min_stock;
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image
            ? asset('storage/' . $this->image)
            : asset('images/product-placeholder.png');
    }

    // Auto-generate SKU: CATEGORY_CODE-SEQUENCE e.g., PSH-FLR-001
    public static function generateSku(int $categoryId): string
    {
        $category = Category::find($categoryId);
        $prefix   = $category ? strtoupper(substr($category->code, 0, 3)) : 'PRD';
        $count    = static::where('category_id', $categoryId)->withTrashed()->count() + 1;
        return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}
