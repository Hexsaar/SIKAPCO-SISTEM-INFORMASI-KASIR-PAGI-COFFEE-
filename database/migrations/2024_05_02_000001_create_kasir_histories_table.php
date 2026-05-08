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
        Schema::create('kasir_histories', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('payment_method', ['cash', 'qris']);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('cash_received', 12, 2)->nullable();
            $table->decimal('change_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->json('items'); // Store items as JSON
            $table->integer('items_count');
            $table->enum('status', ['completed', 'cancelled', 'refunded'])->default('completed');
            $table->timestamps();
            
            $table->index(['created_at', 'user_id']);
            $table->index('payment_method');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasir_histories');
    }
};
