@extends('layouts.app')

@section('title', 'Balance Sheet')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Balance Sheet</h1>
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
    </div>

    <!-- Period Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('reports.balance-sheet') }}" method="GET" class="flex items-end space-x-4">
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

    <!-- Balance Sheet -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Balance Sheet</h2>
            <p class="text-gray-600 dark:text-gray-400">As of {{ $statement['period'] }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Assets -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Assets</h3>
                @foreach($statement['assets']['accounts'] as $account)
                    @if($account->balance && $account->balance->closing_balance > 0)
                    <div class="flex justify-between py-1 pl-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($account->balance->closing_balance, 2) }}</span>
                    </div>
                    @endif
                @endforeach
                <div class="flex justify-between py-3 font-bold border-t-2 border-gray-300 dark:border-gray-600 mt-3 bg-blue-50 dark:bg-blue-900/20 px-2 rounded">
                    <span class="text-gray-900 dark:text-white">Total Assets</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($statement['assets']['total'], 2) }}</span>
                </div>
            </div>

            <!-- Liabilities & Equity -->
            <div>
                <!-- Liabilities -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Liabilities</h3>
                @foreach($statement['liabilities']['accounts'] as $account)
                    @if($account->balance && $account->balance->closing_balance > 0)
                    <div class="flex justify-between py-1 pl-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($account->balance->closing_balance, 2) }}</span>
                    </div>
                    @endif
                @endforeach
                <div class="flex justify-between py-2 font-semibold border-t border-gray-300 dark:border-gray-600 mt-2">
                    <span class="text-gray-900 dark:text-white">Total Liabilities</span>
                    <span class="text-gray-900 dark:text-white">{{ number_format($statement['liabilities']['total'], 2) }}</span>
                </div>

                <!-- Equity -->
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 mt-6 border-b-2 border-gray-300 dark:border-gray-600 pb-2">Equity</h3>
                @foreach($statement['equity']['accounts'] as $account)
                    @if($account->balance && $account->balance->closing_balance > 0)
                    <div class="flex justify-between py-1 pl-4">
                        <span class="text-gray-700 dark:text-gray-300">{{ $account->account_name }}</span>
                        <span class="text-gray-900 dark:text-white font-medium">{{ number_format($account->balance->closing_balance, 2) }}</span>
                    </div>
                    @endif
                @endforeach
                <div class="flex justify-between py-2 font-semibold border-t border-gray-300 dark:border-gray-600 mt-2">
                    <span class="text-gray-900 dark:text-white">Total Equity</span>
                    <span class="text-gray-900 dark:text-white">{{ number_format($statement['equity']['total'], 2) }}</span>
                </div>

                <div class="flex justify-between py-3 font-bold border-t-2 border-gray-300 dark:border-gray-600 mt-3 bg-blue-50 dark:bg-blue-900/20 px-2 rounded">
                    <span class="text-gray-900 dark:text-white">Total Liabilities & Equity</span>
                    <span class="text-gray-900 dark:text-white">LKR {{ number_format($statement['total_liabilities_equity'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Balance Check -->
        <div class="mt-8 p-4 rounded {{ $statement['balance_check'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <div class="flex items-center">
                <i class="fas fa-{{ $statement['balance_check'] ? 'check-circle text-green-600' : 'exclamation-circle text-red-600' }} text-xl mr-3"></i>
                <span class="text-gray-900 dark:text-white font-medium">
                    {{ $statement['balance_check'] ? 'Balance Sheet is balanced!' : 'WARNING: Balance Sheet is NOT balanced!' }}
                </span>
            </div>
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
