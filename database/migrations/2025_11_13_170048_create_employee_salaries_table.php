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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->decimal('basic_salary', 15, 2); 
            $table->decimal('epf', 15, 2); 
            $table->decimal('etf', 15, 2); 
            $table->decimal('ot_hours', 5, 2)->default(0); 
            $table->decimal('ot_rate', 10, 2)->default(0); 
            $table->decimal('total_salary', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};
