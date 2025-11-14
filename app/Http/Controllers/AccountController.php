<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsEnum;
use App\Models\Account;
use App\Models\AccountType;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_CHART_OF_ACCOUNTS->value)) {
            abort(403);
        }

        $accounts = Account::with(['accountType', 'parentAccount'])
            ->orderBy('account_code')
            ->get();

        $accountTypes = AccountType::all();

        return view('accounts.index', compact('accounts', 'accountTypes'));
    }

    public function create()
    {
        if (! auth()->user()->can(PermissionsEnum::CREATE_ACCOUNTS->value)) {
            abort(403);
        }

        $accountTypes = AccountType::all();
        $parentAccounts = Account::orderBy('account_code')->get();

        return view('accounts.create', compact('accountTypes', 'parentAccounts'));
    }

    public function store(Request $request)
    {
        if (! auth()->user()->can(PermissionsEnum::CREATE_ACCOUNTS->value)) {
            abort(403);
        }

        $validated = $request->validate([
            'account_code' => 'required|string|unique:accounts,account_code',
            'account_name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Account::create($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account created successfully.');
    }

    public function show(Account $account)
    {
        if (! auth()->user()->can(PermissionsEnum::VIEW_CHART_OF_ACCOUNTS->value)) {
            abort(403);
        }

        $account->load(['accountType', 'parentAccount', 'subAccounts']);

        return view('accounts.show', compact('account'));
    }

    public function edit(Account $account)
    {
        if (! auth()->user()->can(PermissionsEnum::EDIT_ACCOUNTS->value)) {
            abort(403);
        }

        $accountTypes = AccountType::all();
        $parentAccounts = Account::where('id', '!=', $account->id)->orderBy('account_code')->get();

        return view('accounts.edit', compact('account', 'accountTypes', 'parentAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        if (! auth()->user()->can(PermissionsEnum::EDIT_ACCOUNTS->value)) {
            abort(403);
        }

        $validated = $request->validate([
            'account_code' => 'required|string|unique:accounts,account_code,'.$account->id,
            'account_name' => 'required|string|max:255',
            'account_type_id' => 'required|exists:account_types,id',
            'parent_account_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $account->update($validated);

        return redirect()->route('accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        if (! auth()->user()->can(PermissionsEnum::DELETE_ACCOUNTS->value)) {
            abort(403);
        }

        // Check if account has journal entries
        if ($account->journalEntryLines()->exists()) {
            return redirect()->route('accounts.index')
                ->with('error', 'Cannot delete account with existing journal entries.');
        }

        $account->delete();

        return redirect()->route('accounts.index')
            ->with('success', 'Account deleted successfully.');
    }
}
