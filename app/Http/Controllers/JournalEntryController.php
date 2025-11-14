<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsEnum;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use Exception;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function __construct(protected JournalEntryService $journalEntryService) {}

    public function index(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);
        $status = $request->get('status');

        $journalEntries = $this->journalEntryService->getEntriesForPeriod($year, $month, $status);

        return view('journal-entries.index', compact('journalEntries', 'year', 'month', 'status'));
    }

    public function create()
    {
        if (! auth()->user()->can(PermissionsEnum::CREATE_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        $accounts = Account::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('journal-entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::CREATE_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        $validated = $request->validate([
            'entry_date' => 'required|date',
            'description' => 'required|string',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit_amount' => 'required|numeric|min:0',
            'lines.*.credit_amount' => 'required|numeric|min:0',
            'lines.*.description' => 'nullable|string',
        ]);

        try {
            $this->journalEntryService->createJournalEntry($validated);

            return redirect()->route('journal-entries.index')
                ->with('success', 'Journal entry created successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show(JournalEntry $journalEntry)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        $journalEntry->load(['lines.account', 'creator', 'approver', 'voider']);

        return view('journal-entries.show', compact('journalEntry'));
    }

    public function post(JournalEntry $journalEntry)
    {
        if (! auth()->user()->can(PermissionsEnum::POST_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        try {
            $this->journalEntryService->postJournalEntry($journalEntry);

            return redirect()->back()
                ->with('success', 'Journal entry posted successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    public function void(JournalEntry $journalEntry)
    {
        if (! auth()->user()->can(PermissionsEnum::VOID_JOURNAL_ENTRIES->value)) {
            abort(403);
        }

        try {
            $this->journalEntryService->voidJournalEntry($journalEntry);

            return redirect()->back()
                ->with('success', 'Journal entry voided successfully.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
