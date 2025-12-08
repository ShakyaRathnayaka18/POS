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
            $table->string('base_unit')->default('pcs')->after('unit');
            $table->string('purchase_unit')->nullable()->after('base_unit');
            $table->decimal('conversion_factor', 10, 4)->default(1)->after('purchase_unit');
            $table->boolean('allow_decimal_sales')->default(false)->after('conversion_factor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['base_unit', 'purchase_unit', 'conversion_factor', 'allow_decimal_sales']);
        });
    }
};
