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
        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 191)->unique();
            $table->foreignId('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('customer_name', 191)->nullable();
            $table->string('customer_phone', 191)->nullable();
            $table->date('return_date');
            $table->string('return_reason', 191);
            $table->text('notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0.00);
            $table->decimal('tax', 15, 2)->default(0.00);
            $table->decimal('total', 15, 2)->default(0.00);
            $table->decimal('refund_amount', 15, 2)->default(0.00);
            $table->string('refund_method', 191)->nullable();
            $table->string('status', 191)->default('Pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index('sale_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_returns');
    }
};
