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
        // 1. Customers
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('no_hp')->nullable();
            $table->bigInteger('total_debt')->default(0);
            $table->timestamps();
        });

        // 2. Sales (Transactions)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users'); // Cashier
            $table->bigInteger('total');
            $table->bigInteger('total_paid')->default(0);
            $table->string('payment_method'); // 'cash', 'debt', 'partial'
            $table->string('status'); // 'draft', 'completed', 'cancelled'
            $table->timestamps();
        });

        // 3. Sale Items
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            // Nullable for Phase 1 (Drafts), populated in Phase 2
            $table->foreignId('product_batch_id')->nullable()->constrained('product_batches');
            
            $table->integer('qty_base'); // Stored in base unit
            
            // Snapshots (Immutable)
            $table->string('unit_label');
            $table->integer('unit_multiplier');
            $table->bigInteger('harga_jual_per_unit');
            
            $table->bigInteger('subtotal');
            
            // Note: harga_beli is EXCLUDED for safety
            
            $table->timestamps();
        });

        // 4. Sale Payments (Debt/Partial Log)
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->bigInteger('amount');
            $table->timestamp('paid_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('customers');
    }
};
