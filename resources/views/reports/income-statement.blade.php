@extends('layouts.app')

@section('title', 'Income Statement')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Income Statement (Profit & Loss)</h1>
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
    </div>

    <!-- Period Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('reports.income-statement') }}" method="GET" class="flex items-end space-x-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="month" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                Generate Report
            </button>
        </form>
    </div>

    <!-- Income Statement -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Income Statement</h2>
            <p class="text-gray-600 dark:text-gray-400">For the Period Ending {{ $statement['period'] }}</p>
        </div>

        <!-- Revenue Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Revenue</h3>
            @foreach($statement['revenue']['accounts'] as $account)
                @if($account->balance && $account->balance->closing_balance > 0)
                <div class="flex justify-between py-1 pl-4">
                    <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                    <span class="text-gray-900 dark:text-white font-medium">LKR {{ number_format($account->balance->closing_balance, 2) }}</span>
                </div>
                @endif
            @endforeach
            <div class="flex justify-between py-2 font-bold border-t border-gray-300 dark:border-gray-600 mt-2">
                <span class="text-gray-900 dark:text-white">Total Revenue</span>
                <span class="text-gray-900 dark:text-white">LKR {{ number_format($statement['revenue']['total'], 2) }}</span>
            </div>
        </div>

        <!-- Cost of Goods Sold -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Cost of Goods Sold</h3>
            @foreach($statement['cogs']['accounts'] as $account)
                @if($account->balance && $account->balance->closing_balance > 0)
                <div class="flex justify-between py-1 pl-4">
                    <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                    <span class="text-gray-900 dark:text-white font-medium">LKR {{ number_format($account->balance->closing_balance, 2) }}</span>
                </div>
                @endif
            @endforeach
            <div class="flex justify-between py-2 font-bold border-t border-gray-300 dark:border-gray-600 mt-2">
                <span class="text-gray-900 dark:text-white">Total COGS</span>
                <span class="text-gray-900 dark:text-white">LKR {{ number_format($statement['cogs']['total'], 2) }}</span>
            </div>
        </div>

        <!-- Gross Profit -->
        <div class="flex justify-between py-3 font-bold text-lg bg-blue-50 dark:bg-blue-900/20 px-4 rounded mb-6">
            <span class="text-gray-900 dark:text-white">Gross Profit</span>
            <span class="text-{{ $statement['gross_profit'] >= 0 ? 'green' : 'red' }}-600">
                LKR {{ number_format($statement['gross_profit'], 2) }}
            </span>
        </div>

        <!-- Operating Expenses -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Operating Expenses</h3>
            @foreach($statement['operating_expenses']['accounts'] as $account)
                @if($account->balance && $account->balance->closing_balance > 0)
                <div class="flex justify-between py-1 pl-4">
                    <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                    <span class="text-gray-900 dark:text-white font-medium">LKR {{ number_format($account->balance->closing_balance, 2) }}</span>
                </div>
                @endif
            @endforeach
            <div class="flex justify-between py-2 font-bold border-t border-gray-300 dark:border-gray-600 mt-2">
                <span class="text-gray-900 dark:text-white">Total Operating Expenses</span>
                <span class="text-gray-900 dark:text-white">LKR {{ number_format($statement['operating_expenses']['total'], 2) }}</span>
            </div>
        </div>

        <!-- Net Income -->
        <div class="flex justify-between py-4 font-bold text-xl bg-{{ $statement['net_income'] >= 0 ? 'green' : 'red' }}-50 dark:bg-{{ $statement['net_income'] >= 0 ? 'green' : 'red' }}-900/20 px-4 rounded">
            <span class="text-gray-900 dark:text-white">Net Income</span>
            <span class="text-{{ $statement['net_income'] >= 0 ? 'green' : 'red' }}-600">
                LKR {{ number_format($statement['net_income'], 2) }}
            </span>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .bg-white, .bg-white * { visibility: visible; }
    .bg-white { position: absolute; left: 0; top: 0; width: 100%; }
    button { display: none !important; }
}
</style>
@endsection
