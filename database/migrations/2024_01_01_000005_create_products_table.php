<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('sku')->unique();                // PSH-FLR-001
            $table->string('barcode')->unique();            // auto-generated from SKU
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit')->default('pcs');         // pcs, dozen, lusin
            $table->decimal('sell_price', 15, 2)->default(0);
            $table->decimal('default_cost_price', 15, 2)->default(0);
            $table->integer('stock_total')->default(0);     // always updated on transaction
            $table->integer('min_stock')->default(5);       // low stock threshold
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
