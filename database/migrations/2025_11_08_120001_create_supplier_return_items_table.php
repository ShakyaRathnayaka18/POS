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
        Schema::create('supplier_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_return_id')->constrained('supplier_returns')->onDelete('cascade');
            $table->foreignId('stock_id')->constrained('stocks');
            $table->foreignId('product_id')->constrained('products');
            $table->foreignId('batch_id')->constrained('batches');
            $table->integer('quantity_returned');
            $table->decimal('cost_price', 10, 2);
            $table->decimal('tax', 5, 2)->default(0.00);
            $table->decimal('item_total', 10, 2);
            $table->string('condition', 191)->default('Damaged');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('supplier_return_id');
            $table->index('stock_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_return_items');
    }
};
