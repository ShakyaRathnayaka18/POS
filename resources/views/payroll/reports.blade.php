@extends('layouts.app')

@section('title', 'Payroll Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Payroll Reports</h1>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Filter by Date Range</h2>
        <form action="{{ route('payroll.reports') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                <input
                    type="date"
                    name="from_date"
                    id="from_date"
                    value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                <input
                    type="date"
                    name="to_date"
                    id="to_date"
                    value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                    <i class="fas fa-money-bill-wave text-2xl text-green-600 dark:text-green-300"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Gross Pay</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_gross_pay'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <i class="fas fa-hand-holding-usd text-2xl text-blue-600 dark:text-blue-300"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Net Pay</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_net_pay'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                    <i class="fas fa-building text-2xl text-purple-600 dark:text-purple-300"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Employer Contributions</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_employer_contributions'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Employee Contributions</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">EPF (8%):</span>
                    <span class="font-semibold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_epf_employee'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Employer Contributions</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">EPF (12%):</span>
                    <span class="font-semibold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_epf_employer'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">ETF (3%):</span>
                    <span class="font-semibold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_etf'], 2) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Total Cost</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Net Pay:</span>
                    <span class="font-semibold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_net_pay'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Employer Contribution:</span>
                    <span class="font-semibold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_employer_contributions'], 2) }}</span>
                </div>
                <hr class="border-gray-300 dark:border-gray-600">
                <div class="flex justify-between text-lg">
                    <span class="font-bold text-gray-800 dark:text-white">Grand Total:</span>
                    <span class="font-bold text-green-600 dark:text-green-400">LKR {{ number_format($summary['total_net_pay'] + $summary['total_employer_contributions'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Periods Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Payroll Periods</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPF Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPF Employer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ETF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($periods as $period)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $period->period_start->format('M d, Y') }}<br>to {{ $period->period_end->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-white
                                    @if($period->status === 'draft') bg-gray-500
                                    @elseif($period->status === 'processing') bg-yellow-500
                                    @elseif($period->status === 'approved') bg-green-500
                                    @elseif($period->status === 'paid') bg-blue-500
                                    @endif">
                                    {{ ucfirst($period->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                LKR {{ number_format($period->getTotalGrossPay(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                LKR {{ number_format($period->getTotalEPFEmployee(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                LKR {{ number_format($period->getTotalEPFEmployer(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                LKR {{ number_format($period->getTotalETF(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                LKR {{ number_format($period->getTotalNetPay(), 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('payroll.show', $period) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No payroll periods found for the selected date range
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
