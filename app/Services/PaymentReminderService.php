<?php

namespace App\Services;

use App\Enums\CreditStatusEnum;
use App\Enums\ReminderStatusEnum;
use App\Models\PaymentReminder;
use App\Models\SupplierCredit;
use Illuminate\Database\Eloquent\Collection;

class PaymentReminderService
{
    public function generateReminders(): void
    {
        $this->createDueSoonReminders();
        $this->createOverdueReminders();
    }

    public function createDueSoonReminders(): void
    {
        $dueSoonDays = [7, 3, 1];

        foreach ($dueSoonDays as $days) {
            $credits = SupplierCredit::query()
                ->where('due_date', '=', now()->addDays($days)->toDateString())
                ->whereNotIn('status', [CreditStatusEnum::PAID])
                ->get();

            foreach ($credits as $credit) {
                if ($this->shouldCreateReminder($credit, 'due_soon', $days)) {
                    PaymentReminder::create([
                        'supplier_credit_id' => $credit->id,
                        'reminder_type' => 'due_soon',
                        'days_before_due' => $days,
                        'status' => ReminderStatusEnum::PENDING,
                    ]);
                }
            }
        }
    }

    public function createOverdueReminders(): void
    {
        $overdueDays = [1, 7, 30];

        foreach ($overdueDays as $days) {
            $credits = SupplierCredit::query()
                ->where('due_date', '=', now()->subDays($days)->toDateString())
                ->whereNotIn('status', [CreditStatusEnum::PAID])
                ->get();

            foreach ($credits as $credit) {
                if ($this->shouldCreateReminder($credit, 'overdue', null, $days)) {
                    PaymentReminder::create([
                        'supplier_credit_id' => $credit->id,
                        'reminder_type' => 'overdue',
                        'days_overdue' => $days,
                        'status' => ReminderStatusEnum::PENDING,
                    ]);
                }
            }
        }
    }

    protected function shouldCreateReminder(
        SupplierCredit $credit,
        string $type,
        ?int $daysBefore = null,
        ?int $daysOverdue = null
    ): bool {
        $query = PaymentReminder::query()
            ->where('supplier_credit_id', $credit->id)
            ->where('reminder_type', $type);

        if ($daysBefore !== null) {
            $query->where('days_before_due', $daysBefore);
        }

        if ($daysOverdue !== null) {
            $query->where('days_overdue', $daysOverdue);
        }

        return ! $query->exists();
    }

    public function sendReminder(PaymentReminder $reminder): void
    {
        try {
            // Here you would send email/notification
            // For now, we'll just mark it as sent
            // TODO: Implement actual notification sending

            $reminder->markAsSent();
        } catch (\Exception $e) {
            $reminder->markAsFailed();
            throw $e;
        }
    }

    public function getUpcomingDueCredits(int $days = 7): Collection
    {
        return SupplierCredit::query()
            ->where('due_date', '<=', now()->addDays($days))
            ->where('due_date', '>=', now())
            ->whereNotIn('status', [CreditStatusEnum::PAID])
            ->with(['supplier', 'goodReceiveNote'])
            ->orderBy('due_date')
            ->get();
    }

    public function getOverdueCredits(): Collection
    {
        return SupplierCredit::query()
            ->where('due_date', '<', now())
            ->whereNotIn('status', [CreditStatusEnum::PAID])
            ->with(['supplier', 'goodReceiveNote'])
            ->orderBy('due_date')
            ->get();
    }

    public function processPendingReminders(): void
    {
        $pendingReminders = PaymentReminder::pending()
            ->with('supplierCredit.supplier')
            ->get();

        foreach ($pendingReminders as $reminder) {
            $this->sendReminder($reminder);
        }
    }
}
