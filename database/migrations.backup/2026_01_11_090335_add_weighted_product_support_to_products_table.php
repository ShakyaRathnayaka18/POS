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
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_weighted')->default(false)->after('allow_decimal_sales');
            $table->string('weighted_product_code', 6)->nullable()->unique()->after('is_weighted');
            $table->string('pricing_type')->default('unit')->after('weighted_product_code');

            $table->index('is_weighted');
            $table->index('weighted_product_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_weighted']);
            $table->dropIndex(['weighted_product_code']);
            $table->dropColumn(['is_weighted', 'weighted_product_code', 'pricing_type']);
        });
    }
};
