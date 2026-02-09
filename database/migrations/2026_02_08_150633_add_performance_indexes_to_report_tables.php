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
        Schema::table('product_batches', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {
            // We use foreignId for normalization if we want to query by product directly on this table
            $table->foreignId('product_id')->after('product_batch_id')->nullable()->constrained('products');
            $table->index('product_id');
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->index('customer_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->dropIndex(['customer_id']);
        });

        Schema::table('sale_item_batches', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
