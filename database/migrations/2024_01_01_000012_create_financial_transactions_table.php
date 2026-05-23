<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();   // FIN-2024-001
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['cash_in', 'cash_out']);
            $table->enum('category', ['sales', 'purchase', 'reject_loss', 'operational', 'other']);
            $table->string('reference_code')->nullable();   // links to TRX-OUT or REJ code
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->date('transaction_date');
            $table->timestamps();

            $table->index(['type', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
