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
        if (!Schema::hasTable('product_supplier')) {
            Schema::create('product_supplier', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
                $table->string('vendor_product_code')->index();
                $table->decimal('vendor_cost_price', 10, 2)->nullable();
                $table->boolean('is_preferred')->default(false);
                $table->integer('lead_time_days')->nullable();
                $table->timestamps();

                $table->unique(['supplier_id', 'vendor_product_code'], 'supplier_vendor_code_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
