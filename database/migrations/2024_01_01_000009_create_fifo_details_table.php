<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Each outgoing transaction may consume multiple batches (FIFO).
        // This table records exactly which batch contributed how many units.
        Schema::create('fifo_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outgoing_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_batch_id')->constrained()->onDelete('restrict');
            $table->integer('qty_taken');               // qty deducted from this batch
            $table->decimal('cost_price', 15, 2);       // cost_price from that batch
            $table->decimal('subtotal_hpp', 15, 2);     // qty_taken * cost_price
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fifo_details');
    }
};
