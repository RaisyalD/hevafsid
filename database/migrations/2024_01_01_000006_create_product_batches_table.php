<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->string('batch_code')->unique();         // BATCH-2024-001
            $table->integer('qty_initial');                 // original qty when received
            $table->integer('qty_remaining');               // current remaining qty
            $table->decimal('cost_price', 15, 2);          // harga modal per unit for this batch
            $table->date('received_date');
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'depleted', 'expired'])->default('active');
            $table->timestamps();

            $table->index(['product_id', 'status', 'received_date']); // for fast FIFO query
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_batches');
    }
};
