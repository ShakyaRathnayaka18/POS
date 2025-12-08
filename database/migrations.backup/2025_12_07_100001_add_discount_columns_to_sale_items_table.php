<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'fixed_amount', 'none'])
                ->default('none')
                ->after('price');
            $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_value');
            $table->foreignId('discount_id')
                ->nullable()
                ->after('discount_amount')
                ->constrained()
                ->onDelete('set null');
            $table->foreignId('discount_approved_by')
                ->nullable()
                ->after('discount_id')
                ->constrained('users')
                ->onDelete('set null');
            $table->decimal('price_before_discount', 10, 2)
                ->nullable()
                ->after('discount_approved_by');
            $table->decimal('subtotal_before_discount', 10, 2)
                ->nullable()
                ->after('price_before_discount');
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $table->dropForeign(['discount_id']);
            $table->dropForeign(['discount_approved_by']);
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'discount_amount',
                'discount_id',
                'discount_approved_by',
                'price_before_discount',
                'subtotal_before_discount',
            ]);
        });
    }
};
