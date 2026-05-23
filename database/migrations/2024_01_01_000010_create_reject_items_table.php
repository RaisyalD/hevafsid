<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reject_items', function (Blueprint $table) {
            $table->id();
            $table->string('reject_code')->unique();        // REJ-2024-001
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->foreignId('product_batch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('cost_price', 15, 2)->default(0); // HPP of rejected items
            $table->decimal('total_loss', 15, 2)->default(0);  // qty * cost_price
            $table->enum('reject_type', ['damaged', 'defective', 'expired', 'lost', 'other']);
            $table->text('reason');
            $table->date('reject_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reject_items');
    }
};
