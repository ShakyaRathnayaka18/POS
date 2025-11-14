@extends('layouts.app')

@section('title', 'Trial Balance')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Trial Balance</h1>
        <button onclick="window.print()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-print mr-2"></i>Print
        </button>
    </div>

    <!-- Period Selector -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('reports.trial-balance') }}" method="GET" class="flex items-end space-x-4">
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

    <!-- Trial Balance Report -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-8">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Trial Balance</h2>
            <p class="text-gray-600 dark:text-gray-400">As of {{ $trialBalance['period'] }}</p>
        </div>

        <!-- Trial Balance Table -->
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
                            Account Type
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Debit Balance
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credit Balance
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @foreach($trialBalance['accounts'] as $account)
                        @if($account['debit_balance'] > 0 || $account['credit_balance'] > 0)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $account['account_code'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $account['account_name'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $account['account_type'] }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                @if($account['debit_balance'] > 0)
                                    {{ number_format($account['debit_balance'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                @if($account['credit_balance'] > 0)
                                    {{ number_format($account['credit_balance'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50 dark:bg-blue-900/20">
                    <tr class="font-bold">
                        <td colspan="3" class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            Total
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                            LKR {{ number_format($trialBalance['total_debits'], 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                            LKR {{ number_format($trialBalance['total_credits'], 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Balance Check -->
        <div class="mt-8 p-4 rounded {{ $trialBalance['is_balanced'] ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <div class="flex items-center">
                <i class="fas fa-{{ $trialBalance['is_balanced'] ? 'check-circle text-green-600' : 'exclamation-circle text-red-600' }} text-xl mr-3"></i>
                <span class="text-gray-900 dark:text-white font-medium">
                    {{ $trialBalance['is_balanced'] ? 'Trial Balance is balanced! Total Debits = Total Credits' : 'WARNING: Trial Balance is NOT balanced!' }}
                </span>
            </div>
            @if(!$trialBalance['is_balanced'])
            <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                Difference: LKR {{ number_format(abs($trialBalance['total_debits'] - $trialBalance['total_credits']), 2) }}
            </div>
            @endif
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
