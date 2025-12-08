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
        Schema::table('good_receive_notes', function (Blueprint $table) {
            $table->decimal('subtotal_before_discount', 15, 2)->default(0)->after('subtotal');
            $table->decimal('discount', 15, 2)->default(0)->after('shipping');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_receive_notes', function (Blueprint $table) {
            $table->dropColumn(['subtotal_before_discount', 'discount']);
        });
    }
};
