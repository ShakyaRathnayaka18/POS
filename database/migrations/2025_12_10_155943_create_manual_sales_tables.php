<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_sales', function (Blueprint $table) {
            $table->id();
            $table->string('manual_sale_number')->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedBigInteger('shift_id')->nullable();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();

            // Financial columns
            $table->decimal('subtotal', 15, 2);
            $table->decimal('subtotal_before_discount', 15, 2)->default(0);
            $table->decimal('tax', 15, 2);
            $table->decimal('total', 15, 2);
            $table->decimal('total_discount', 15, 2)->default(0);

            // Sale-level discount columns (for future use)
            $table->enum('sale_level_discount_type', ['percentage', 'fixed_amount', 'none'])->default('none');
            $table->decimal('sale_level_discount_value', 10, 2)->default(0);
            $table->decimal('sale_level_discount_amount', 10, 2)->default(0);

            // Payment information
            $table->string('payment_method');
            $table->decimal('amount_received', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();

            // Status and reconciliation tracking
            $table->enum('status', ['pending', 'reconciled', 'cancelled'])->default('pending');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('converted_sale_id')->nullable()->constrained('sales')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('shift_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::create('manual_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manual_sale_id')->constrained('manual_sales')->onDelete('cascade');

            // Manual entry fields
            $table->string('product_name');
            $table->string('entered_barcode')->nullable();

            // Pricing and quantity
            $table->decimal('quantity', 10, 2);
            $table->decimal('price', 10, 2);
            $table->decimal('price_before_discount', 10, 2)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('subtotal_before_discount', 10, 2)->nullable();
            $table->decimal('tax', 10, 2);
            $table->decimal('total', 10, 2);

            // Discount information
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'none'])->default('none');
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);

            // Reconciliation tracking
            $table->boolean('is_reconciled')->default(false);
            $table->foreignId('reconciled_product_id')->nullable()->constrained('products')->onDelete('set null');
            $table->foreignId('reconciled_stock_id')->nullable()->constrained('stocks')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('is_reconciled');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_sale_items');
        Schema::dropIfExists('manual_sales');
    }
};
