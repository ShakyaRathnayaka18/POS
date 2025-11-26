<?php

namespace App\Services;

use App\Enums\CreditStatusEnum;
use App\Enums\CreditTermsEnum;
use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerCreditService
{
    public function createCreditFromSale(Sale $sale, array $data): CustomerCredit
    {
        return DB::transaction(function () use ($sale, $data) {
            $customer = $sale->customer;

            $creditTerms = CreditTermsEnum::from($data['credit_terms']);
            $invoiceDate = Carbon::parse($data['invoice_date'] ?? now());
            $dueDate = $this->calculateDueDate($invoiceDate, $creditTerms);

            $this->checkCreditLimit($customer, $sale->total);

            $credit = CustomerCredit::create([
                'credit_number' => CustomerCredit::generateCreditNumber(),
                'customer_id' => $customer->id,
                'sale_id' => $sale->id,
                'invoice_number' => $data['invoice_number'] ?? $sale->sale_number,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'credit_terms' => $creditTerms,
                'credit_days' => $creditTerms->getDays(),
                'original_amount' => $sale->total,
                'paid_amount' => 0,
                'outstanding_amount' => $sale->total,
                'status' => CreditStatusEnum::PENDING,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $customer->increment('current_credit_used', $sale->total);

            return $credit;
        });
    }

    public function calculateDueDate(Carbon $invoiceDate, CreditTermsEnum $creditTerms): Carbon
    {
        $days = $creditTerms->getDays();

        if ($days === 0) {
            return $invoiceDate;
        }

        return $invoiceDate->copy()->addDays($days);
    }

    public function updateCreditStatus(CustomerCredit $credit): void
    {
        $credit->updateStatus();
    }

    public function getOutstandingBalance(Customer $customer): float
    {
        return $customer->customerCredits()
            ->whereNotIn('status', [CreditStatusEnum::PAID])
            ->sum('outstanding_amount');
    }

    public function getCreditAging(?Customer $customer = null): array
    {
        $query = CustomerCredit::query()
            ->whereNotIn('status', [CreditStatusEnum::PAID]);

        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        $credits = $query->with('customer')->get();

        $aging = [
            'current' => ['amount' => 0, 'count' => 0, 'credits' => []],
            '1-30' => ['amount' => 0, 'count' => 0, 'credits' => []],
            '31-60' => ['amount' => 0, 'count' => 0, 'credits' => []],
            '61-90' => ['amount' => 0, 'count' => 0, 'credits' => []],
            '90+' => ['amount' => 0, 'count' => 0, 'credits' => []],
        ];

        foreach ($credits as $credit) {
            $daysOld = now()->diffInDays($credit->invoice_date);
            $bucket = $this->getAgingBucket($daysOld);

            $aging[$bucket]['amount'] += $credit->outstanding_amount;
            $aging[$bucket]['count']++;
            $aging[$bucket]['credits'][] = $credit;
        }

        return $aging;
    }

    protected function getAgingBucket(int $days): string
    {
        if ($days <= 30) {
            return 'current';
        }

        if ($days <= 60) {
            return '1-30';
        }

        if ($days <= 90) {
            return '31-60';
        }

        if ($days <= 120) {
            return '61-90';
        }

        return '90+';
    }

    public function checkCreditLimit(Customer $customer, float $amount): bool
    {
        $availableCredit = $customer->available_credit;

        if ($amount > $availableCredit) {
            throw new \Exception(
                'Customer credit limit exceeded. Available credit: LKR '.number_format($availableCredit, 2).
                ', Requested: LKR '.number_format($amount, 2)
            );
        }

        return true;
    }

    public function generateCreditNumber(): string
    {
        return CustomerCredit::generateCreditNumber();
    }

    public function getUpcomingDueCredits(int $days = 7): Collection
    {
        return CustomerCredit::dueSoon($days)
            ->with(['customer', 'sale'])
            ->orderBy('due_date')
            ->get();
    }

    public function getOverdueCredits(): Collection
    {
        return CustomerCredit::overdue()
            ->with(['customer', 'sale'])
            ->orderBy('due_date')
            ->get();
    }
}
