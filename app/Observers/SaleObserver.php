<?php

namespace App\Observers;

use App\Models\Sale;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class SaleObserver
{
    public function __construct(protected TransactionIntegrationService $transactionIntegrationService) {}

    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Don't create journal entry on "created" - it fires before sale items exist
        // Journal entry creation happens in SaleService after transaction commits
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        try {
            // If sale status changed to completed, create journal entry
            if ($sale->wasChanged('status') && strtolower($sale->status) === 'completed') {
                $this->transactionIntegrationService->createSaleJournalEntry($sale);
            }
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for updated sale: '.$e->getMessage(), [
                'sale_id' => $sale->id,
                'sale_number' => $sale->sale_number,
            ]);
        }
    }
}
