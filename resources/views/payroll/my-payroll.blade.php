@extends('layouts.app')

@section('title', 'My Payroll')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">My Payroll History</h1>
    </div>

    <!-- Employee Info -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Employee Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Employee Number</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->employee_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Employment Type</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ ucfirst($employee->employment_type) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->department ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Payroll History -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Payroll History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Regular Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Overtime Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPF (8%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($entries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $entry->payrollPeriod->period_start->format('M d, Y') }}<br>
                                to {{ $entry->payrollPeriod->period_end->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ number_format($entry->regular_hours, 2) }} hrs
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ number_format($entry->getTotalOvertimeHours(), 2) }} hrs
                                @if($entry->overtime_hours > 0 || $entry->overtime_hours_2x > 0)
                                    <br>
                                    <span class="text-xs text-gray-400">
                                        (1.5x: {{ number_format($entry->overtime_hours, 2) }},
                                        2x: {{ number_format($entry->overtime_hours_2x, 2) }})
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($entry->gross_pay, 2) }}
                                @if($entry->base_amount > 0)
                                    <br>
                                    <span class="text-xs text-gray-400">
                                        Base: {{ number_format($entry->base_amount, 2) }}
                                        @if($entry->overtime_amount > 0 || $entry->overtime_amount_2x > 0)
                                            + OT: {{ number_format($entry->overtime_amount + $entry->overtime_amount_2x, 2) }}
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                LKR {{ number_format($entry->epf_employee, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600 dark:text-green-400">
                                LKR {{ number_format($entry->net_pay, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-white
                                    @if($entry->status === 'pending') bg-yellow-500
                                    @elseif($entry->status === 'approved') bg-green-500
                                    @elseif($entry->status === 'paid') bg-blue-500
                                    @endif">
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No payroll history found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($entries->count() > 0)
        <!-- Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                        <i class="fas fa-clock text-2xl text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Hours (Last 12 Periods)</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($entries->sum('regular_hours') + $entries->sum('overtime_hours') + $entries->sum('overtime_hours_2x'), 2) }} hrs</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                        <i class="fas fa-money-bill-wave text-2xl text-green-600 dark:text-green-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Gross Pay</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($entries->sum('gross_pay'), 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                        <i class="fas fa-hand-holding-usd text-2xl text-purple-600 dark:text-purple-300"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Net Pay</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($entries->sum('net_pay'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
