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
        Schema::table('products', function (Blueprint $table) {
            $table->string('item_code')->nullable()->after('sku');
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
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['item_code']);
            $table->dropColumn('item_code');
        });
    }
};
