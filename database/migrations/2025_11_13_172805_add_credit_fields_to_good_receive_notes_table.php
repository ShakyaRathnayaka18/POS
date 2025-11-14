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
            $table->string('payment_type')->default('cash')->after('notes');
            $table->boolean('is_credit')->default(false)->after('payment_type');
            $table->foreignId('supplier_credit_id')->nullable()->after('is_credit')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_receive_notes', function (Blueprint $table) {
            $table->dropForeign(['supplier_credit_id']);
            $table->dropColumn(['payment_type', 'is_credit', 'supplier_credit_id']);
        });
    }
};
