<?php

namespace App\Services;

use App\Enums\ShiftStatusEnum;
use App\Models\Sale;
use App\Models\Shift;
use Exception;
use Illuminate\Support\Facades\DB;

class ShiftService
{
    /**
     * Clock in a user and start a new shift.
     */
    public function clockIn(int $userId, ?float $openingCash = null, ?string $notes = null): Shift
    {
        // Check if user already has an active shift
        $activeShift = $this->getCurrentActiveShift($userId);
        if ($activeShift) {
            throw new Exception('You already have an active shift. Please clock out first.');
        }

        return DB::transaction(function () use ($userId, $openingCash, $notes) {
            return Shift::create([
                'user_id' => $userId,
                'shift_number' => $this->generateShiftNumber(),
                'clock_in_at' => now(),
                'opening_cash' => $openingCash,
                'notes' => $notes,
                'status' => ShiftStatusEnum::ACTIVE,
            ]);
        });
    }

    /**
     * Clock out a user and complete their shift.
     */
    public function clockOut(int $shiftId, ?float $closingCash = null, ?string $notes = null): Shift
    {
        $shift = Shift::findOrFail($shiftId);

        if (! $shift->isActive()) {
            throw new Exception('This shift is not active and cannot be clocked out.');
        }

        return DB::transaction(function () use ($shift, $closingCash, $notes) {
            // Calculate shift summary
            $summary = $this->calculateShiftSummary($shift->id);

            // Update shift with clock out information
            $shift->update([
                'clock_out_at' => now(),
                'closing_cash' => $closingCash,
                'expected_cash' => $summary['expected_cash'],
                'cash_difference' => $closingCash !== null && $summary['expected_cash'] !== null
                    ? round($closingCash - $summary['expected_cash'], 2)
                    : null,
                'total_sales' => $summary['total_sales'],
                'total_sales_count' => $summary['total_sales_count'],
                'notes' => $notes ? ($shift->notes ? $shift->notes."\n\n".$notes : $notes) : $shift->notes,
                'status' => ShiftStatusEnum::COMPLETED,
            ]);

            return $shift->fresh();
        });
    }

    /**
     * Get the current active shift for a user.
     */
    public function getCurrentActiveShift(int $userId): ?Shift
    {
        return Shift::where('user_id', $userId)
            ->where('status', ShiftStatusEnum::ACTIVE)
            ->first();
    }

    /**
     * Calculate shift summary (sales totals, cash expected, etc.).
     */
    public function calculateShiftSummary(int $shiftId): array
    {
        $shift = Shift::with('sales')->findOrFail($shiftId);

        $totalSales = $shift->sales->sum('total');
        $totalSalesCount = $shift->sales->count();

        // Calculate expected cash (opening cash + cash sales)
        $cashSales = $shift->sales->where('payment_method', 'Cash')->sum('total');
        $expectedCash = ($shift->opening_cash ?? 0) + $cashSales;

        return [
            'total_sales' => round($totalSales, 2),
            'total_sales_count' => $totalSalesCount,
            'expected_cash' => round($expectedCash, 2),
            'cash_sales' => round($cashSales, 2),
            'card_sales' => round($shift->sales->where('payment_method', 'Card')->sum('total'), 2),
            'credit_sales' => round($shift->sales->where('payment_method', 'Credit')->sum('total'), 2),
        ];
    }

    /**
     * Link a sale to an active shift.
     */
    public function linkSaleToShift(int $saleId, int $shiftId): void
    {
        $sale = Sale::findOrFail($saleId);
        $shift = Shift::findOrFail($shiftId);

        if (! $shift->isActive()) {
            throw new Exception('Cannot link sale to a completed shift.');
        }

        $sale->update(['shift_id' => $shiftId]);
    }

    /**
     * Approve a completed shift (manager/admin only).
     */
    public function approveShift(int $shiftId): Shift
    {
        $shift = Shift::findOrFail($shiftId);

        if ($shift->status !== ShiftStatusEnum::COMPLETED) {
            throw new Exception('Only completed shifts can be approved.');
        }

        $shift->update(['status' => ShiftStatusEnum::APPROVED]);

        return $shift->fresh();
    }

    /**
     * Generate a unique shift number.
     */
    protected function generateShiftNumber(): string
    {
        $prefix = 'SH';
        $date = now()->format('Ymd');

        // Get the last shift number for today
        $lastShift = Shift::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        if ($lastShift && preg_match('/'.$prefix.$date.'(\d{4})/', $lastShift->shift_number, $matches)) {
            $sequence = intval($matches[1]) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix.$date.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Validate that user has only one active shift.
     */
    public function validateSingleActiveShift(int $userId): bool
    {
        $activeShiftCount = Shift::where('user_id', $userId)
            ->where('status', ShiftStatusEnum::ACTIVE)
            ->count();

        return $activeShiftCount <= 1;
    }

    /**
     * Get shift statistics for reporting.
     */
    public function getShiftStatistics(int $shiftId): array
    {
        $summary = $this->calculateShiftSummary($shiftId);
        $shift = Shift::findOrFail($shiftId);

        return array_merge($summary, [
            'total_hours' => $shift->calculateTotalHours(),
            'formatted_duration' => $shift->getFormattedDuration(),
            'sales_per_hour' => $shift->calculateTotalHours() > 0
                ? round($summary['total_sales'] / $shift->calculateTotalHours(), 2)
                : 0,
        ]);
    }
}
