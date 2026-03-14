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
            $table->string('input_unit_name')->nullable()->after('product_id')->comment('Satuan yang dipilih saat input stok');
            $table->decimal('qty_masuk_original', 15, 2)->nullable()->after('input_unit_name')->comment('Jumlah qty dalam satuan input');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropColumn(['input_unit_name', 'qty_masuk_original']);
        });
    }
};
