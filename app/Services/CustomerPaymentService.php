<?php

namespace App\Services;

use App\Enums\PaymentMethodEnum;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\CustomerPayment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerPaymentService
{
    public function processPayment(array $data): CustomerPayment
    {
        return DB::transaction(function () use ($data) {
            $credit = CustomerCredit::findOrFail($data['customer_credit_id']);

            $this->validatePaymentAmount($credit, $data['amount']);

            $payment = CustomerPayment::create([
                'payment_number' => CustomerPayment::generatePaymentNumber(),
                'customer_id' => $credit->customer_id,
                'customer_credit_id' => $credit->id,
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

    public function allocatePaymentToCredit(CustomerCredit $credit, float $amount): void
    {
        $credit->paid_amount += $amount;
        $credit->outstanding_amount -= $amount;

        if ($credit->outstanding_amount < 0) {
            $credit->outstanding_amount = 0;
        }

        $credit->save();
        $credit->updateStatus();

        $credit->customer->decrement('current_credit_used', $amount);
    }

    protected function validatePaymentAmount(CustomerCredit $credit, float $amount): void
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
        return CustomerPayment::generatePaymentNumber();
    }

    public function getPaymentHistory(?Customer $customer = null, ?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $query = CustomerPayment::query()
            ->with(['customer', 'customerCredit', 'processedBy']);

        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        if ($from) {
            $query->where('payment_date', '>=', $from);
        }

        if ($to) {
            $query->where('payment_date', '<=', $to);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    public function getTotalReceivedFromCustomer(Customer $customer, ?Carbon $from = null, ?Carbon $to = null): float
    {
        $query = $customer->customerPayments();

        if ($from) {
            $query->where('payment_date', '>=', $from);
        }

        if ($to) {
            $query->where('payment_date', '<=', $to);
        }

        return $query->sum('amount');
    }
}
