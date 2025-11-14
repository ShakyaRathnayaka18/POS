<?php

namespace App\Observers;

use App\Models\GoodReceiveNote;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class GoodReceiveNoteObserver
{
    public function __construct(protected TransactionIntegrationService $transactionIntegrationService) {}

    /**
     * Handle the GoodReceiveNote "created" event.
     */
    public function created(GoodReceiveNote $goodReceiveNote): void
    {
        try {
            // Create journal entry when GRN is created
            $this->transactionIntegrationService->createGRNJournalEntry($goodReceiveNote);
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for GRN: '.$e->getMessage(), [
                'grn_id' => $goodReceiveNote->id,
                'grn_number' => $goodReceiveNote->grn_number,
            ]);
        }
    }
}
