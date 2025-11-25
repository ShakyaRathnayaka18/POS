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
        Schema::create('payroll_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('ot_weekday_multiplier', 5, 2)->default(1.5);
            $table->decimal('ot_weekend_multiplier', 5, 2)->default(2.0);
            $table->decimal('daily_hours_threshold', 5, 2)->default(8.0);
            $table->decimal('epf_employee_percentage', 5, 2)->default(8.0);
            $table->decimal('epf_employer_percentage', 5, 2)->default(12.0);
            $table->decimal('etf_employer_percentage', 5, 2)->default(3.0);
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_settings');
    }
};
