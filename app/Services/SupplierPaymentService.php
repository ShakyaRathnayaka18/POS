<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Supplier;
use App\Models\SupplierCredit;
use App\Models\SupplierPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SupplierPaymentService
{
    public function processPayment(array $data): SupplierPayment
    {
        return DB::transaction(function () use ($data) {
            $credit = SupplierCredit::findOrFail($data['supplier_credit_id']);

            $this->validatePaymentAmount($credit, $data['amount']);

            $payment = SupplierPayment::create([
                'payment_number' => SupplierPayment::generatePaymentNumber(),
                'supplier_id' => $credit->supplier_id,
                'supplier_credit_id' => $credit->id,
                'payment_date' => Carbon::parse($data['payment_date']),
                'amount' => $data['amount'],
                'payment_method' => PaymentMethodEnum::from($data['payment_method']),
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'processed_by' => auth()->id(),
            ]);

            $this->allocatePaymentToCredit($credit, $data['amount']);

            return $payment;
        });
    }

    public function allocatePaymentToCredit(SupplierCredit $credit, float $amount): void
    {
        $credit->paid_amount += $amount;
        $credit->outstanding_amount -= $amount;

        if ($credit->outstanding_amount < 0) {
            $credit->outstanding_amount = 0;
        }

        $credit->save();
        $credit->updateStatus();

        $credit->supplier->decrement('current_credit_used', $amount);
    }

    protected function validatePaymentAmount(SupplierCredit $credit, float $amount): void
    {
        if ($amount <= 0) {
            throw new \Exception('Payment amount must be greater than zero.');
        }

        if ($amount > $credit->outstanding_amount) {
            throw new \Exception(
                'Payment amount (LKR '.number_format($amount, 2).') exceeds outstanding balance (LKR '.
                number_format($credit->outstanding_amount, 2).')'
            );
        }
    }

    public function generatePaymentNumber(): string
    {
        return SupplierPayment::generatePaymentNumber();
    }

    public function getPaymentHistory(?Supplier $supplier = null, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = SupplierPayment::query()
            ->with(['supplier', 'supplierCredit', 'processedBy']);

        if ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }

        if ($from) {
            $query->where('payment_date', '>=', $from);
        }

        if ($to) {
            $query->where('payment_date', '<=', $to);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function getTotalPaidToSupplier(Supplier $supplier, ?Carbon $from = null, ?Carbon $to = null): float
    {
        $query = $supplier->supplierPayments();

        if ($from) {
            $query->where('payment_date', '>=', $from);
        }

        if ($to) {
            $query->where('payment_date', '<=', $to);
        }

        return $query->sum('amount');
    }
}
