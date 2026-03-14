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
            $table->boolean('is_bonus')->default(false)->after('product_id');
            $table->string('bonus_note')->nullable()->after('is_bonus');
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->boolean('is_bonus_item')->default(false)->after('qty_base');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropColumn(['is_bonus', 'bonus_note']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropColumn('is_bonus_item');
        });
    }
};
