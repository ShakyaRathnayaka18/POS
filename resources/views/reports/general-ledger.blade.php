@extends('layouts.app')

@section('title', 'General Ledger')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">General Ledger</h1>
        @if($ledger)
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
        @endif
    </div>

    <!-- Account and Period Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('reports.general-ledger') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Account</label>
                <select name="account_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required>
                    <option value="">Select an account...</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ $accountId == $account->id ? 'selected' : '' }}>
                            {{ $account->account_code }} - {{ $account->account_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="month" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                    Generate Report
                </button>
            </div>
        </form>
    </div>

    @if($ledger)
    <!-- General Ledger Report -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <!-- Account Header -->
        <div class="mb-8 pb-6 border-b-2 border-gray-300 dark:border-gray-600">
            <div class="text-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">General Ledger</h2>
                <p class="text-gray-600 dark:text-gray-400">For the Period: {{ $ledger['period'] }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Account Code</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ledger['account']['account_code'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Account Name</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ledger['account']['account_name'] }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Account Type</p>
                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $ledger['account']['account_type'] }}</p>
                </div>
            </div>
        </div>

        <!-- Opening Balance -->
        <div class="mb-4 bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
            <div class="flex justify-between items-center">
                <span class="font-semibold text-gray-900 dark:text-white">Opening Balance</span>
                <span class="font-bold text-lg text-gray-900 dark:text-white">
                    LKR {{ number_format($ledger['opening_balance'], 2) }}
                </span>
            </div>
        </div>

        <!-- Transactions Table -->
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
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Balance
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse($ledger['transactions'] as $transaction)
                    <tr>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($transaction['date'])->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                            <a href="{{ route('journal-entries.show', $transaction['journal_entry_id']) }}"
                               class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                {{ $transaction['entry_number'] }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            {{ $transaction['description'] }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                            @if($transaction['debit_amount'] > 0)
                                {{ number_format($transaction['debit_amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                            @if($transaction['credit_amount'] > 0)
                                {{ number_format($transaction['credit_amount'], 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-gray-900 dark:text-white">
                            {{ number_format($transaction['running_balance'], 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                            No transactions found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-700 dark:text-gray-300">Total Debits:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">LKR {{ number_format($ledger['total_debits'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-700 dark:text-gray-300">Total Credits:</span>
                    <span class="font-semibold text-gray-900 dark:text-white">LKR {{ number_format($ledger['total_credits'], 2) }}</span>
                </div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-900 dark:text-white">Closing Balance:</span>
                    <span class="font-bold text-xl text-gray-900 dark:text-white">
                        LKR {{ number_format($ledger['closing_balance'], 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- No Account Selected Message -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12">
        <div class="text-center">
            <i class="fas fa-file-alt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Account Selected</h3>
            <p class="text-gray-600 dark:text-gray-400">Please select an account from the dropdown above to view its general ledger.</p>
        </div>
    </div>
    @endif
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .bg-white, .bg-white * { visibility: visible; }
    .bg-white { position: absolute; left: 0; top: 0; width: 100%; }
    button { display: none !important; }
    form { display: none !important; }
}
</style>
@endsection
