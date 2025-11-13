<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\PayrollEntry;
use App\Models\PayrollPeriod;
use App\Models\Shift;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class PayrollService
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    /**
     * Create a new payroll period.
     */
    public function createPayrollPeriod(string $periodStart, string $periodEnd, ?string $notes = null): PayrollPeriod
    {
        // Validate dates
        $start = Carbon::parse($periodStart);
        $end = Carbon::parse($periodEnd);

        if ($end->lt($start)) {
            throw new Exception('Period end date must be after start date.');
        }

        // Check for overlapping periods
        $overlapping = PayrollPeriod::where(function ($query) use ($start, $end) {
            $query->whereBetween('period_start', [$start, $end])
                ->orWhereBetween('period_end', [$start, $end])
                ->orWhere(function ($q) use ($start, $end) {
                    $q->where('period_start', '<=', $start)
                        ->where('period_end', '>=', $end);
                });
        })->exists();

        if ($overlapping) {
            throw new Exception('A payroll period already exists for this date range.');
        }

        return DB::transaction(function () use ($periodStart, $periodEnd, $notes) {
            return PayrollPeriod::create([
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'status' => 'draft',
                'notes' => $notes,
            ]);
        });
    }

    /**
     * Generate payroll entries for all active employees in a period.
     */
    public function generateEntries(int $periodId): int
    {
        $period = PayrollPeriod::findOrFail($periodId);

        if (! $period->canBeProcessed()) {
            throw new Exception('Payroll period cannot be processed in its current status.');
        }

        return DB::transaction(function () use ($period) {
            $employees = Employee::active()->get();
            $count = 0;

            foreach ($employees as $employee) {
                // Check if entry already exists
                $exists = PayrollEntry::where('payroll_period_id', $period->id)
                    ->where('employee_id', $employee->id)
                    ->exists();

                if (! $exists) {
                    PayrollEntry::create([
                        'payroll_period_id' => $period->id,
                        'employee_id' => $employee->id,
                        'status' => 'pending',
                    ]);
                    $count++;
                }
            }

            return $count;
        });
    }

    /**
     * Calculate payroll for a specific entry based on shifts.
     */
    public function calculateEntry(int $entryId): PayrollEntry
    {
        $entry = PayrollEntry::with(['employee', 'payrollPeriod'])->findOrFail($entryId);
        $period = $entry->payrollPeriod;
        $employee = $entry->employee;

        if (! $employee) {
            throw new Exception('Employee not found for this payroll entry.');
        }

        return DB::transaction(function () use ($entry, $period, $employee) {
            // Get approved shifts for the employee in this period
            $shifts = Shift::where('user_id', $employee->user_id)
                ->where('status', 'approved')
                ->whereBetween('clock_in_at', [$period->period_start, $period->period_end->endOfDay()])
                ->whereNotNull('clock_out_at')
                ->get();

            // Calculate total hours
            $totalHours = 0;
            $shiftIds = [];

            foreach ($shifts as $shift) {
                $hours = $shift->calculateTotalHours();
                $totalHours += $hours;
                $shiftIds[] = $shift->id;
            }

            // Calculate overtime (Sri Lankan standard: 45hrs/week = 9hrs/day for 5-day week)
            // For simplicity, we'll use 8 hrs/day as regular, rest as OT
            $workingDays = $shifts->count();
            $regularHoursLimit = $workingDays * 8;

            $regularHours = min($totalHours, $regularHoursLimit);
            $overtimeHours = max(0, $totalHours - $regularHoursLimit);

            // Classify overtime (weekday vs weekend/holiday)
            $overtimeHours1_5x = 0;
            $overtimeHours2x = 0;

            foreach ($shifts as $shift) {
                $shiftHours = $shift->calculateTotalHours();
                $dayOfWeek = Carbon::parse($shift->clock_in_at)->dayOfWeek;

                // Weekend = Saturday(6) or Sunday(0)
                if ($dayOfWeek === 0 || $dayOfWeek === 6) {
                    $overtimeHours2x += $shiftHours;
                } elseif ($shiftHours > 8) {
                    // Weekday overtime (over 8 hours)
                    $overtimeHours1_5x += ($shiftHours - 8);
                }
            }

            // Calculate pay
            if ($employee->isSalaried()) {
                // Salaried: Calculate monthly pay based on worked days
                $baseAmount = (float) $employee->base_salary;
                $overtimeRate = $baseAmount / 240; // Monthly salary / 240 hours
            } else {
                // Hourly: Calculate based on hours worked
                $hourlyRate = (float) $employee->hourly_rate;
                $baseAmount = $regularHours * $hourlyRate;
                $overtimeRate = $hourlyRate;
            }

            $overtimeAmount1_5x = $overtimeHours1_5x * $overtimeRate * 1.5;
            $overtimeAmount2x = $overtimeHours2x * $overtimeRate * 2;
            $grossPay = $baseAmount + $overtimeAmount1_5x + $overtimeAmount2x;

            // Calculate EPF and ETF
            $epfEmployee = $this->employeeService->calculateEPFEmployee($grossPay);
            $epfEmployer = $this->employeeService->calculateEPFEmployer($grossPay);
            $etfEmployer = $this->employeeService->calculateETF($grossPay);

            // Calculate net pay
            $netPay = $grossPay - $epfEmployee;

            // Update entry
            $entry->update([
                'regular_hours' => round($regularHours, 2),
                'overtime_hours' => round($overtimeHours1_5x, 2),
                'overtime_hours_2x' => round($overtimeHours2x, 2),
                'base_amount' => round($baseAmount, 2),
                'overtime_amount' => round($overtimeAmount1_5x, 2),
                'overtime_amount_2x' => round($overtimeAmount2x, 2),
                'gross_pay' => round($grossPay, 2),
                'epf_employee' => $epfEmployee,
                'epf_employer' => $epfEmployer,
                'etf_employer' => $etfEmployer,
                'net_pay' => round($netPay, 2),
            ]);

            // Sync shifts
            $entry->shifts()->sync($shiftIds);

            return $entry->fresh();
        });
    }

    /**
     * Process entire payroll period (calculate all entries).
     */
    public function processPeriod(int $periodId, int $userId): PayrollPeriod
    {
        $period = PayrollPeriod::with('payrollEntries')->findOrFail($periodId);

        if (! $period->canBeProcessed()) {
            throw new Exception('Payroll period cannot be processed in its current status.');
        }

        return DB::transaction(function () use ($period, $userId) {
            // Generate entries if they don't exist
            if ($period->payrollEntries->isEmpty()) {
                $this->generateEntries($period->id);
                $period->refresh();
            }

            // Calculate each entry
            foreach ($period->payrollEntries as $entry) {
                $this->calculateEntry($entry->id);
            }

            // Update period status
            $period->update([
                'status' => 'processing',
                'processed_by' => $userId,
                'processed_at' => now(),
            ]);

            return $period->fresh();
        });
    }

    /**
     * Approve a payroll period.
     */
    public function approvePeriod(int $periodId, int $userId): PayrollPeriod
    {
        $period = PayrollPeriod::findOrFail($periodId);

        if (! $period->canBeApproved()) {
            throw new Exception('Payroll period cannot be approved in its current status.');
        }

        return DB::transaction(function () use ($period, $userId) {
            $period->update([
                'status' => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            // Update all entries to approved
            $period->payrollEntries()->update(['status' => 'approved']);

            return $period->fresh();
        });
    }

    /**
     * Mark payroll period as paid.
     */
    public function markAsPaid(int $periodId): PayrollPeriod
    {
        $period = PayrollPeriod::findOrFail($periodId);

        if (! $period->isApproved()) {
            throw new Exception('Only approved payroll periods can be marked as paid.');
        }

        return DB::transaction(function () use ($period) {
            $period->update(['status' => 'paid']);
            $period->payrollEntries()->update(['status' => 'paid']);

            return $period->fresh();
        });
    }

    /**
     * Get payroll summary for a period.
     */
    public function getPeriodSummary(int $periodId): array
    {
        $period = PayrollPeriod::with('payrollEntries.employee')->findOrFail($periodId);

        $entries = $period->payrollEntries;

        return [
            'period' => $period,
            'employee_count' => $entries->count(),
            'total_regular_hours' => $entries->sum('regular_hours'),
            'total_overtime_hours' => $entries->sum('overtime_hours') + $entries->sum('overtime_hours_2x'),
            'total_gross_pay' => $entries->sum('gross_pay'),
            'total_epf_employee' => $entries->sum('epf_employee'),
            'total_epf_employer' => $entries->sum('epf_employer'),
            'total_etf_employer' => $entries->sum('etf_employer'),
            'total_deductions' => $entries->sum('epf_employee') + $entries->sum('other_deductions'),
            'total_net_pay' => $entries->sum('net_pay'),
            'total_employer_cost' => $entries->sum('gross_pay') + $entries->sum('epf_employer') + $entries->sum('etf_employer'),
        ];
    }

    /**
     * Get employee payroll history.
     */
    public function getEmployeeHistory(int $employeeId, int $limit = 12)
    {
        return PayrollEntry::where('employee_id', $employeeId)
            ->with('payrollPeriod')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Delete a draft payroll period.
     */
    public function deletePeriod(int $periodId): bool
    {
        $period = PayrollPeriod::findOrFail($periodId);

        if (! $period->isDraft()) {
            throw new Exception('Only draft payroll periods can be deleted.');
        }

        return DB::transaction(function () use ($period) {
            // Delete all entries (cascade will handle shift associations)
            $period->payrollEntries()->delete();

            return $period->delete();
        });
    }
}
