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
        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->onDelete('cascade');
            $table->foreignId('sale_item_id')->constrained('sale_items');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('product_id')->constrained('products');
            $table->integer('quantity_returned');
            $table->decimal('selling_price', 10, 2);
            $table->decimal('tax', 5, 2)->default(0.00);
            $table->decimal('item_total', 10, 2);
            $table->string('condition', 191)->default('Good');
            $table->boolean('restore_to_stock')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sales_return_id');
            $table->index('sale_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
    }
};
