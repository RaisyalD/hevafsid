<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();   // TRX-IN-2024-001
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('product_batch_id')->constrained()->onDelete('restrict');
            $table->foreignId('user_id')->constrained()->onDelete('restrict'); // who input
            $table->integer('quantity');
            $table->decimal('cost_price', 15, 2);           // harga modal per unit
            $table->decimal('total_cost', 15, 2);           // qty * cost_price
            $table->date('received_date');
            $table->string('invoice_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_transactions');
    }
};
