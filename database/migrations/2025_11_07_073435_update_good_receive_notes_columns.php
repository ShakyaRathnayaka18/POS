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
            // Rename po_number to grn_number
            $table->renameColumn('po_number', 'grn_number');

            // Rename expected_date to received_date
            $table->renameColumn('expected_date', 'received_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('good_receive_notes', function (Blueprint $table) {
            // Revert grn_number back to po_number
            $table->renameColumn('grn_number', 'po_number');

            // Revert received_date back to expected_date
            $table->renameColumn('received_date', 'expected_date');
        });
    }
};
