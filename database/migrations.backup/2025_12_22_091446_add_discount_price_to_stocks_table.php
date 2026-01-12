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
        if (!Schema::hasColumn('stocks', 'discount_price')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->decimal('discount_price', 15, 2)->nullable()->default(0)->after('selling_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('stocks', 'discount_price')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropColumn('discount_price');
            });
        }
    }
};
