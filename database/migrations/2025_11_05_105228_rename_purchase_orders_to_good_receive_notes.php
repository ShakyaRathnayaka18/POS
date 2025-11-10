<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('purchase_orders', 'good_receive_notes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('good_receive_notes', 'purchase_orders');
    }
};
