<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('sku')->unique();
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->integer('initial_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('maximum_stock')->default(0);
            $table->string('product_image')->nullable();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->string('unit')->default('pcs');
            $table->timestamps();
        });

        // Generate item codes for existing products
        DB::table('products')->orderBy('id')->get()->each(function ($product, $index) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['item_code' => 'ITEM-'.str_pad($index + 1, 5, '0', STR_PAD_LEFT)]);
        });

        // Make item_code unique after populating
        Schema::table('products', function (Blueprint $table) {
            $table->string('item_code')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
