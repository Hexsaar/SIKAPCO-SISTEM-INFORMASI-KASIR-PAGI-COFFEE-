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
        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('subtotal_amount', 12, 2)->after('total_amount')->nullable();
            $table->decimal('total_item_discount', 12, 2)->after('subtotal_amount')->default(0);
            $table->decimal('global_discount_percent', 5, 2)->after('total_item_discount')->default(0);
            $table->decimal('global_discount_amount', 12, 2)->after('global_discount_percent')->default(0);
            $table->text('notes')->after('items')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal_amount',
                'total_item_discount', 
                'global_discount_percent',
                'global_discount_amount',
                'notes'
            ]);
        });
    }
};
