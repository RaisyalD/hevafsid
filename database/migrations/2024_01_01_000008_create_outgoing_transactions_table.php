<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outgoing_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();   // TRX-OUT-2024-001
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('sell_price', 15, 2);           // harga jual per unit
            $table->decimal('total_hpp', 15, 2)->default(0); // auto-calculated FIFO HPP
            $table->decimal('total_revenue', 15, 2);        // qty * sell_price
            $table->decimal('gross_profit', 15, 2)->default(0); // revenue - hpp
            $table->date('transaction_date');
            $table->string('reference_number')->nullable(); // nota penjualan
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outgoing_transactions');
    }
};
