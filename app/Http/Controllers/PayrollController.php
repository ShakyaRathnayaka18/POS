<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Services\PayrollService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(protected PayrollService $payrollService) {}

    /**
     * Display all payroll periods.
     */
    public function index(Request $request): View
    {
        $query = PayrollPeriod::with(['processedBy', 'approvedBy'])
            ->orderBy('period_start', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $periods = $query->paginate(15);

        return view('payroll.index', compact('periods'));
    }

    /**
     * Show form to create a new payroll period.
     */
    public function create(): View
    {
        return view('payroll.create');
    }

    /**
     * Store a new payroll period.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after:period_start'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $period = $this->payrollService->createPayrollPeriod(
                $request->period_start,
                $request->period_end,
                $request->notes
            );

            return redirect()
                ->route('payroll.show', $period)
                ->with('success', 'Payroll period created successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create payroll period: '.$e->getMessage());
        }
    }

    /**
     * Display a specific payroll period with entries.
     */
    public function show(PayrollPeriod $payroll): View
    {
        $payroll->load(['payrollEntries.employee.user', 'processedBy', 'approvedBy']);
        $summary = $this->payrollService->getPeriodSummary($payroll->id);

        return view('payroll.show', compact('payroll', 'summary'));
    }

    /**
     * Process a payroll period (calculate all entries).
     */
    public function process(PayrollPeriod $payroll): RedirectResponse
    {
        try {
            $this->payrollService->processPeriod($payroll->id, auth()->id());

            return redirect()
                ->route('payroll.show', $payroll)
                ->with('success', 'Payroll period processed successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to process payroll: '.$e->getMessage());
        }
    }

    /**
     * Approve a payroll period.
     */
    public function approve(PayrollPeriod $payroll): RedirectResponse
    {
        try {
            $this->payrollService->approvePeriod($payroll->id, auth()->id());

            return redirect()
                ->route('payroll.show', $payroll)
                ->with('success', 'Payroll period approved successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to approve payroll: '.$e->getMessage());
        }
    }

    /**
     * Mark payroll period as paid.
     */
    public function markAsPaid(PayrollPeriod $payroll): RedirectResponse
    {
        try {
            $this->payrollService->markAsPaid($payroll->id);

            return redirect()
                ->route('payroll.show', $payroll)
                ->with('success', 'Payroll period marked as paid.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to mark as paid: '.$e->getMessage());
        }
    }

    /**
     * Delete a draft payroll period.
     */
    public function destroy(PayrollPeriod $payroll): RedirectResponse
    {
        try {
            $this->payrollService->deletePeriod($payroll->id);

            return redirect()
                ->route('payroll.index')
                ->with('success', 'Payroll period deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete payroll period: '.$e->getMessage());
        }
    }

    /**
     * Show employee's own payroll history.
     */
    public function myPayroll(): View
    {
        $user = auth()->user();
        $employee = $user->employee;

        if (! $employee) {
            abort(404, 'Employee profile not found.');
        }

        $entries = $this->payrollService->getEmployeeHistory($employee->id);

        return view('payroll.my-payroll', compact('employee', 'entries'));
    }

    /**
     * Display payroll reports.
     */
    public function reports(Request $request): View
    {
        $query = PayrollPeriod::with('payrollEntries');

        if ($request->filled('from_date')) {
            $query->where('period_start', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('period_end', '<=', $request->to_date);
        }

        $periods = $query->orderBy('period_start', 'desc')->get();

        $totalGrossPay = 0;
        $totalNetPay = 0;
        $totalEPFEmployee = 0;
        $totalEPFEmployer = 0;
        $totalETF = 0;

        foreach ($periods as $period) {
            $totalGrossPay += $period->getTotalGrossPay();
            $totalNetPay += $period->getTotalNetPay();
            $totalEPFEmployee += $period->getTotalEPFEmployee();
            $totalEPFEmployer += $period->getTotalEPFEmployer();
            $totalETF += $period->getTotalETF();
        }

        $summary = [
            'total_gross_pay' => $totalGrossPay,
            'total_net_pay' => $totalNetPay,
            'total_epf_employee' => $totalEPFEmployee,
            'total_epf_employer' => $totalEPFEmployer,
            'total_etf' => $totalETF,
            'total_employer_contributions' => $totalEPFEmployer + $totalETF,
        ];

        return view('payroll.reports', compact('periods', 'summary'));
    }

    /**
     * Export payroll data (CSV).
     */
    public function export(PayrollPeriod $payroll)
    {
        $payroll->load('payrollEntries.employee.user');

        $filename = 'payroll_'.$payroll->period_start->format('Y-m-d').'_to_'.$payroll->period_end->format('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payroll) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'Employee Number',
                'Employee Name',
                'Regular Hours',
                'OT Hours (1.5x)',
                'OT Hours (2x)',
                'Base Amount',
                'OT Amount (1.5x)',
                'OT Amount (2x)',
                'Gross Pay',
                'EPF Employee (8%)',
                'EPF Employer (12%)',
                'ETF Employer (3%)',
                'Net Pay',
                'Status',
            ]);

            // Data rows
            foreach ($payroll->payrollEntries as $entry) {
                fputcsv($file, [
                    $entry->employee->employee_number,
                    $entry->employee->getFullName(),
                    $entry->regular_hours,
                    $entry->overtime_hours,
                    $entry->overtime_hours_2x,
                    $entry->base_amount,
                    $entry->overtime_amount,
                    $entry->overtime_amount_2x,
                    $entry->gross_pay,
                    $entry->epf_employee,
                    $entry->epf_employer,
                    $entry->etf_employer,
                    $entry->net_pay,
                    $entry->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
