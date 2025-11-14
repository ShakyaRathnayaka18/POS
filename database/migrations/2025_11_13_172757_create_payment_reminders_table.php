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
        Schema::create('payment_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_credit_id')->constrained()->cascadeOnDelete();
            $table->string('reminder_type');
            $table->integer('days_before_due')->nullable();
            $table->integer('days_overdue')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();

            $table->index(['supplier_credit_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_reminders');
    }
};
