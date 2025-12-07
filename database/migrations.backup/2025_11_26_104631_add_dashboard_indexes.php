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
        // Sales indexes for date-based queries
        Schema::table('sales', function (Blueprint $table) {
            $table->index('created_at');
            $table->index(['created_at', 'total']);
            $table->index('user_id');
        });

        // Sale items for stock movement analysis
        Schema::table('sale_items', function (Blueprint $table) {
            $table->index('product_id');
            $table->index(['product_id', 'quantity']);
        });

        // Batches for expiry tracking
        Schema::table('batches', function (Blueprint $table) {
            $table->index('expiry_date');
        });

        // Customer credits for financial metrics
        Schema::table('customer_credits', function (Blueprint $table) {
            $table->index('status');
            // Note: due_date index already exists from create_customer_credits_table migration
            $table->index(['status', 'outstanding_amount']);
        });

        // Supplier credits for financial metrics
        Schema::table('supplier_credits', function (Blueprint $table) {
            $table->index('status');
            $table->index(['status', 'outstanding_amount']);
        });

        // Customers for active count
        Schema::table('customers', function (Blueprint $table) {
            $table->index('is_active');
        });

        // Shifts for active shifts
        Schema::table('shifts', function (Blueprint $table) {
            $table->index('status');
            $table->index(['status', 'user_id']);
        });

        // Stocks for out of stock check
        Schema::table('stocks', function (Blueprint $table) {
            $table->index('available_quantity');
            $table->index(['product_id', 'available_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['created_at', 'total']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['product_id', 'quantity']);
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['expiry_date']);
        });

        Schema::table('customer_credits', function (Blueprint $table) {
            $table->dropIndex(['status']);
            // Note: due_date index not dropped here as it's from create_customer_credits_table migration
            $table->dropIndex(['status', 'outstanding_amount']);
        });

        Schema::table('supplier_credits', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'outstanding_amount']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['status', 'user_id']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex(['available_quantity']);
            $table->dropIndex(['product_id', 'available_quantity']);
        });
    }
};
