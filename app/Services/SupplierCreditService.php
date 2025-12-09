<?php

namespace App\Services;

use App\Enums\CreditStatusEnum;
use App\Enums\CreditTermsEnum;
use App\Models\GoodReceiveNote;
use App\Models\Supplier;
use App\Models\SupplierCredit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SupplierCreditService
{
    public function createCreditFromGrn(GoodReceiveNote $grn, array $data): SupplierCredit
    {
        return DB::transaction(function () use ($grn, $data) {
            $supplier = $grn->supplier;

            $creditTerms = CreditTermsEnum::from($data['credit_terms']);
            $invoiceDate = Carbon::parse($data['invoice_date'] ?? now());
            $dueDate = $this->calculateDueDate($invoiceDate, $creditTerms);

            $this->checkCreditLimit($supplier, (float) $grn->total);

            $credit = SupplierCredit::create([
                'credit_number' => SupplierCredit::generateCreditNumber(),
                'supplier_id' => $supplier->id,
                'good_receive_note_id' => $grn->id,
                'invoice_number' => $data['invoice_number'] ?? null,
                'invoice_date' => $invoiceDate,
                'due_date' => $dueDate,
                'credit_terms' => $creditTerms,
                'credit_days' => $creditTerms->getDays(),
                'original_amount' => $grn->total,
                'paid_amount' => 0,
                'outstanding_amount' => $grn->total,
                'status' => CreditStatusEnum::PENDING,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $grn->update([
                'payment_type' => 'credit',
                'is_credit' => true,
                'supplier_credit_id' => $credit->id,
            ]);

            $supplier->increment('current_credit_used', (float) $grn->total);

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

    public function updateCreditStatus(SupplierCredit $credit): void
    {
        $credit->updateStatus();
    }

    public function getOutstandingBalance(Supplier $supplier): float
    {
        return $supplier->supplierCredits()
            ->whereNotIn('status', [CreditStatusEnum::PAID])
            ->sum('outstanding_amount');
    }

    public function getCreditAging(?Supplier $supplier = null): array
    {
        $query = SupplierCredit::query()
            ->whereNotIn('status', [CreditStatusEnum::PAID]);

        if ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }

        $credits = $query->with('supplier')->get();

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

    public function checkCreditLimit(Supplier $supplier, float $amount): bool
    {
        // Check if supplier has credit facility (credit limit > 0)
        // We allow the transaction even if limit is 0 or exceeded, as per user request
        if ($supplier->credit_limit <= 0) {
            return true;
        }

        $availableCredit = $supplier->available_credit;

        if ($amount > $availableCredit) {
            // Allow transaction effectively overriding the limit
            return true;
        }

        return true;
    }

    public function generateCreditNumber(): string
    {
        return SupplierCredit::generateCreditNumber();
    }

    public function getUpcomingDueCredits(int $days = 7): Collection
    {
        return SupplierCredit::dueSoon($days)
            ->with(['supplier', 'goodReceiveNote'])
            ->orderBy('due_date')
            ->get();
    }

    public function getOverdueCredits(): Collection
    {
        return SupplierCredit::overdue()
            ->with(['supplier', 'goodReceiveNote'])
            ->orderBy('due_date')
            ->get();
    }
}
