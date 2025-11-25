<?php

namespace App\Observers;

use App\Models\JournalEntry;
use App\Models\PayrollPeriod;
use App\Services\TransactionIntegrationService;
use Exception;
use Illuminate\Support\Facades\Log;

class PayrollPeriodObserver
{
    public function __construct(protected TransactionIntegrationService $transactionIntegrationService) {}

    /**
     * Handle the PayrollPeriod "updated" event.
     */
    public function updated(PayrollPeriod $payrollPeriod): void
    {
        try {
            // When status changes to 'approved', create accrual journal entry
            if ($payrollPeriod->wasChanged('status') && $payrollPeriod->status === 'approved') {
                // Check if accrual entry already exists
                $accrualExists = JournalEntry::where('reference_type', PayrollPeriod::class)
                    ->where('reference_id', $payrollPeriod->id)
                    ->where('description', 'like', '%Payroll accrual%')
                    ->exists();

                if (! $accrualExists) {
                    $this->transactionIntegrationService->createPayrollAccrualEntry($payrollPeriod);
                }
            }

            // When status changes to 'paid', create payment journal entry
            if ($payrollPeriod->wasChanged('status') && $payrollPeriod->status === 'paid') {
                // Check if payment entry already exists
                $paymentExists = JournalEntry::where('reference_type', PayrollPeriod::class)
                    ->where('reference_id', $payrollPeriod->id)
                    ->where('description', 'like', '%Payroll payment%')
                    ->exists();

                if (! $paymentExists) {
                    $this->transactionIntegrationService->createPayrollPaymentEntry($payrollPeriod);
                }
            }
        } catch (Exception $e) {
            Log::error('Failed to create journal entry for payroll period: '.$e->getMessage(), [
                'payroll_period_id' => $payrollPeriod->id,
                'status' => $payrollPeriod->status,
            ]);
        }
    }
}
