<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kasir
            $table->string('transaction_number')->unique(); // TRX-20260317-001
            $table->decimal('total_amount', 12, 2);
            $table->decimal('cash_received', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->string('payment_method'); // CASH, QRIS
            $table->json('items'); // Detail items yang dibeli
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index('transaction_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
