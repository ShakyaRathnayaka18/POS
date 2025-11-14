<?php

namespace App\Observers;

use App\Models\SupplierPayment;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class SupplierPaymentObserver
{
    public function __construct(protected TransactionIntegrationService $transactionIntegrationService) {}

    /**
     * Handle the SupplierPayment "created" event.
     */
    public function created(SupplierPayment $supplierPayment): void
    {
        try {
            // Create journal entry when supplier payment is created
            $this->transactionIntegrationService->createSupplierPaymentJournalEntry($supplierPayment);
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for supplier payment: '.$e->getMessage(), [
                'payment_id' => $supplierPayment->id,
                'payment_number' => $supplierPayment->payment_number,
            ]);
        }
    }
}
