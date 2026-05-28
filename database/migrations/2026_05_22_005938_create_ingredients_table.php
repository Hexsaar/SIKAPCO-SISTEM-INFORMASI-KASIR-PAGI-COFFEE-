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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('stock', 10, 2)->default(0);
            $table->string('unit')->default('kg'); // kg, gram, liter, ml, pcs, dll
            $table->decimal('min_stock', 10, 2)->default(0); // stok minimum untuk peringatan
            $table->decimal('price_per_unit', 10, 2)->default(0); // harga per satuan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};
