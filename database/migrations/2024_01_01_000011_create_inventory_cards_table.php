<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ledger-style running balance per product — append-only.
        Schema::create('inventory_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('product_batch_id')->nullable()->constrained()->onDelete('set null');
            $table->date('transaction_date');
            $table->enum('transaction_type', ['incoming', 'outgoing', 'reject', 'adjustment']);
            $table->string('reference_code');               // links to TRX-IN/OUT/REJ code
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('balance');                     // running stock balance
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_cards');
    }
};
