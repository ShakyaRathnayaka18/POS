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
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->after('quantity_returned');
            $table->decimal('tax', 15, 2)->default(0.00)->after('price');
            $table->decimal('item_total', 15, 2)->after('tax');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_return_items', function (Blueprint $table) {
            $table->dropColumn(['price', 'tax', 'item_total']);
        });
    }
};
