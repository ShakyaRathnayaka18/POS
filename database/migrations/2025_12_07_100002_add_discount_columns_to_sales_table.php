<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal_before_discount', 15, 2)
                ->default(0)
                ->after('subtotal');
            $table->decimal('total_discount', 15, 2)
                ->default(0)
                ->after('subtotal_before_discount');
            $table->enum('sale_level_discount_type', ['percentage', 'fixed_amount', 'none'])
                ->default('none')
                ->after('total_discount');
            $table->decimal('sale_level_discount_value', 10, 2)
                ->default(0)
                ->after('sale_level_discount_type');
            $table->decimal('sale_level_discount_amount', 15, 2)
                ->default(0)
                ->after('sale_level_discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'subtotal_before_discount',
                'total_discount',
                'sale_level_discount_type',
                'sale_level_discount_value',
                'sale_level_discount_amount',
            ]);
        });
    }
};
