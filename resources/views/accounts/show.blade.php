@extends('layouts.app')

@section('title', 'Account Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Account Details</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            @can('edit accounts')
            <a href="{{ route('accounts.edit', $account) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            @endcan
        </div>
    </div>

    <!-- Account Information -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account Code</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $account->account_code }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account Name</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $account->account_name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account Type</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($account->accountType->name === 'Asset') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                    @elseif($account->accountType->name === 'Liability') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                    @elseif($account->accountType->name === 'Equity') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                    @elseif($account->accountType->name === 'Revenue') bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400
                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                    @endif">
                    {{ $account->accountType->name }}
                </span>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Normal Balance: {{ ucfirst($account->accountType->normal_balance) }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    {{ $account->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }}">
                    {{ $account->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            @if($account->parentAccount)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Parent Account</p>
                <a href="{{ route('accounts.show', $account->parentAccount) }}"
                   class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                    {{ $account->parentAccount->account_code }} - {{ $account->parentAccount->account_name }}
                </a>
            </div>
            @endif

            @if($account->description)
            <div class="md:col-span-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
                <p class="text-gray-900 dark:text-white">{{ $account->description }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Sub-Accounts -->
    @if($account->subAccounts->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Sub-Accounts</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Account Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Account Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($account->subAccounts as $subAccount)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $subAccount->account_code }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $subAccount->account_name }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $subAccount->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400' }}">
                                {{ $subAccount->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('accounts.show', $subAccount) }}"
                               class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Current Balance -->
    @php
        $currentBalance = $account->balances()
            ->where('fiscal_year', now()->year)
            ->where('fiscal_period', now()->month)
            ->first();
    @endphp
    @if($currentBalance)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Current Period Balance</h2>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Opening Balance</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    LKR {{ number_format($currentBalance->opening_balance, 2) }}
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Debits</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    LKR {{ number_format($currentBalance->debit_total, 2) }}
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Credits</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    LKR {{ number_format($currentBalance->credit_total, 2) }}
                </p>
            </div>

            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Closing Balance</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white">
                    LKR {{ number_format($currentBalance->closing_balance, 2) }}
                </p>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('reports.general-ledger', ['account_id' => $account->id, 'year' => now()->year, 'month' => now()->month]) }}"
               class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 font-medium">
                <i class="fas fa-file-alt mr-2"></i>View General Ledger
            </a>
        </div>
    </div>
    @endif

    <!-- Recent Transactions -->
    @php
        $recentTransactions = $account->journalEntryLines()
            ->whereHas('journalEntry', function($query) {
                $query->where('status', 'posted');
            })
            ->with(['journalEntry'])
            ->latest()
            ->limit(10)
            ->get();
    @endphp
    @if($recentTransactions->count() > 0)
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Transactions</h2>
            <a href="{{ route('reports.general-ledger', ['account_id' => $account->id]) }}"
               class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-sm">
                View All
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Entry Number
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Debit
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credit
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($recentTransactions as $line)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ $line->journalEntry->entry_date->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <a href="{{ route('journal-entries.show', $line->journalEntry) }}"
                               class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                {{ $line->journalEntry->entry_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            {{ Str::limit($line->description ?? $line->journalEntry->description, 40) }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                            @if($line->debit_amount > 0)
                                {{ number_format($line->debit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                            @if($line->credit_amount > 0)
                                {{ number_format($line->credit_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

        <div class="flex flex-wrap gap-3">
            @can('edit accounts')
            <a href="{{ route('accounts.edit', $account) }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                <i class="fas fa-edit mr-2"></i>Edit Account
            </a>
            @endcan

            @can('delete accounts')
            <form action="{{ route('accounts.destroy', $account) }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to delete this account? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete Account
                </button>
            </form>
            @endcan

            <a href="{{ route('accounts.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
</div>
@endsection
