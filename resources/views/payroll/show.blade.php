@extends('layouts.app')

@section('title', 'Payroll Period Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Payroll Period: {{ $payroll->period_start->format('M d, Y') }} - {{ $payroll->period_end->format('M d, Y') }}
        </h1>
        <a href="{{ route('payroll.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Period Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <i class="fas fa-users text-2xl text-blue-600 dark:text-blue-300"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employees</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $summary['employee_count'] }}</p>
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
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_gross_pay'], 2) }}</p>
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
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">LKR {{ number_format($summary['total_net_pay'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full
                    @if($payroll->status === 'draft') bg-gray-100 dark:bg-gray-700
                    @elseif($payroll->status === 'processing') bg-yellow-100 dark:bg-yellow-900
                    @elseif($payroll->status === 'approved') bg-green-100 dark:bg-green-900
                    @elseif($payroll->status === 'paid') bg-blue-100 dark:bg-blue-900
                    @endif mr-4">
                    <i class="fas fa-clipboard-check text-2xl
                        @if($payroll->status === 'draft') text-gray-600 dark:text-gray-300
                        @elseif($payroll->status === 'processing') text-yellow-600 dark:text-yellow-300
                        @elseif($payroll->status === 'approved') text-green-600 dark:text-green-300
                        @elseif($payroll->status === 'paid') text-blue-600 dark:text-blue-300
                        @endif"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ ucfirst($payroll->status) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Actions</h2>
        <div class="flex flex-wrap gap-3">
            @can('process payroll')
                @if($payroll->canBeProcessed())
                    <form action="{{ route('payroll.process', $payroll) }}" method="POST" onsubmit="return confirm('Process this payroll period? This will calculate pay for all active employees.');">
                        @csrf
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-calculator mr-2"></i>Process Payroll
                        </button>
                    </form>
                @endif
            @endcan

            @can('approve payroll')
                @if($payroll->canBeApproved())
                    <form action="{{ route('payroll.approve', $payroll) }}" method="POST" onsubmit="return confirm('Approve this payroll period?');">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-check mr-2"></i>Approve Payroll
                        </button>
                    </form>
                @endif
            @endcan

            @if($payroll->isApproved())
                <form action="{{ route('payroll.mark-paid', $payroll) }}" method="POST" onsubmit="return confirm('Mark this payroll as paid?');">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-money-bill-wave mr-2"></i>Mark as Paid
                    </button>
                </form>
            @endif

            <a href="{{ route('payroll.export', $payroll) }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-download mr-2"></i>Export CSV
            </a>

            @if($payroll->isDraft())
                <form action="{{ route('payroll.destroy', $payroll) }}" method="POST" onsubmit="return confirm('Delete this draft payroll period?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Payroll Entries -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Payroll Entries</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Regular Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OT (1.5x)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">OT (2x)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">EPF (8%)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payroll->payrollEntries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $entry->employee->getFullName() }}
                                <br>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $entry->employee->employee_number }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ number_format($entry->regular_hours, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ number_format($entry->overtime_hours, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                {{ number_format($entry->overtime_hours_2x, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                LKR {{ number_format($entry->gross_pay, 2) }}
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
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No payroll entries found. Process the payroll to generate entries.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($payroll->payrollEntries->count() > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">TOTALS</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ number_format($summary['total_regular_hours'], 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white" colspan="2">{{ number_format($summary['total_overtime_hours'], 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">LKR {{ number_format($summary['total_gross_pay'], 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">LKR {{ number_format($summary['total_epf_employee'], 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600 dark:text-green-400">LKR {{ number_format($summary['total_net_pay'], 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Employer Contributions -->
    @if($payroll->payrollEntries->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Employer Contributions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">EPF Employer (12%)</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">LKR {{ number_format($summary['total_epf_employer'], 2) }}</p>
                </div>
                <div class="p-4 bg-purple-50 dark:bg-purple-900 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">ETF Employer (3%)</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-300">LKR {{ number_format($summary['total_etf_employer'], 2) }}</p>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Employer Cost</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-300">LKR {{ number_format($summary['total_employer_cost'], 2) }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
