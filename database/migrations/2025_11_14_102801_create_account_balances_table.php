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
        Schema::create('account_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->integer('fiscal_year');
            $table->integer('fiscal_period');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('debit_total', 15, 2)->default(0);
            $table->decimal('credit_total', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['account_id', 'fiscal_year', 'fiscal_period']);
            $table->index(['fiscal_year', 'fiscal_period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
