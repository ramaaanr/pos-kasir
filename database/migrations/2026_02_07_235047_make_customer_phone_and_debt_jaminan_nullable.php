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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('no_hp')->nullable()->change();
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->text('jaminan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('no_hp')->nullable(false)->change();
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->text('jaminan')->nullable(false)->change();
        });
    }
};
