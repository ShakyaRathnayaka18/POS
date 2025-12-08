<?php

namespace App\Observers;

use App\Models\StockAdjustment;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class StockAdjustmentObserver
{
    public function __construct(
        protected TransactionIntegrationService $transactionIntegrationService
    ) {}

    /**
     * Handle the StockAdjustment "updated" event.
     * Create journal entry when status changes to 'approved'
     */
    public function updated(StockAdjustment $adjustment): void
    {
        // Only create journal entry when status changes to approved
        if ($adjustment->wasChanged('status') && $adjustment->isApproved()) {
            try {
                $this->transactionIntegrationService->createStockAdjustmentJournalEntry($adjustment);
            } catch (Exception $e) {
                Log::error('Failed to create journal entry for stock adjustment: '.$e->getMessage(), [
                    'adjustment_id' => $adjustment->id,
                    'adjustment_number' => $adjustment->adjustment_number,
                ]);
            }
        }
    }
}
