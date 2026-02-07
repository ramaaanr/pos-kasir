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
        Schema::create('sale_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_item_id')->constrained('sale_items')->cascadeOnDelete();
            $table->foreignId('product_batch_id')->constrained('product_batches');
            $table->integer('qty_base');
            $table->bigInteger('harga_beli_per_unit'); // Internal tracking for margin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_batches');
    }
};
