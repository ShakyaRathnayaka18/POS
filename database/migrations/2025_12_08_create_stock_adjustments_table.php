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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number')->unique(); // Format: SA-YYYYMMDD-####
            $table->foreignId('stock_id')->constrained('stocks')->onDelete('restrict');
            $table->foreignId('product_id')->constrained('products'); // Denormalized for querying
            $table->foreignId('batch_id')->constrained('batches'); // Denormalized for querying
            $table->enum('type', ['increase', 'decrease']);
            $table->decimal('quantity_before', 10, 4);
            $table->decimal('quantity_adjusted', 10, 4); // Positive for both increase/decrease
            $table->decimal('quantity_after', 10, 4);
            $table->decimal('cost_price', 10, 2); // From stock record
            $table->decimal('total_value', 10, 2); // quantity_adjusted × cost_price
            $table->string('reason'); // Dropdown: damage, theft, recount, return_to_supplier, found, etc.
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('adjustment_date');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
    }
};
