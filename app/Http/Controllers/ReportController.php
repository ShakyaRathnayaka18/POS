<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsEnum;
use App\Models\Account;
use App\Services\FinancialStatementService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(protected FinancialStatementService $financialStatementService) {}

    public function incomeStatement(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_INCOME_STATEMENT->value)) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $statement = $this->financialStatementService->generateIncomeStatement($year, $month);

        return view('reports.income-statement', compact('statement', 'year', 'month'));
    }

    public function balanceSheet(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_BALANCE_SHEET->value)) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $statement = $this->financialStatementService->generateBalanceSheet($year, $month);

        return view('reports.balance-sheet', compact('statement', 'year', 'month'));
    }

    public function trialBalance(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_TRIAL_BALANCE->value)) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $trialBalance = $this->financialStatementService->generateTrialBalance($year, $month);

        return view('reports.trial-balance', compact('trialBalance', 'year', 'month'));
    }

    public function generalLedger(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_GENERAL_LEDGER->value)) {
            abort(403);
        }

        $accountId = $request->get('account_id');
        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $accounts = Account::where('is_active', true)->orderBy('account_code')->get();

        $ledger = null;
        if ($accountId) {
            $ledger = $this->financialStatementService->generateGeneralLedger($accountId, $year, $month);
        }

        return view('reports.general-ledger', compact('ledger', 'accounts', 'accountId', 'year', 'month'));
    }
}
