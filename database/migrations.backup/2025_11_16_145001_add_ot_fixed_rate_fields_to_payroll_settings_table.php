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
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->enum('ot_calculation_mode', ['multiplier', 'fixed_rate'])->default('multiplier')->after('daily_hours_threshold');
            $table->decimal('ot_weekday_fixed_rate', 10, 2)->nullable()->after('ot_calculation_mode');
            $table->decimal('ot_weekend_fixed_rate', 10, 2)->nullable()->after('ot_weekday_fixed_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payroll_settings', function (Blueprint $table) {
            $table->dropColumn(['ot_calculation_mode', 'ot_weekday_fixed_rate', 'ot_weekend_fixed_rate']);
        });
    }
};
