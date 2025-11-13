@extends('layouts.app')

@section('title', 'Employee Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            Employee: {{ $employee->employee_number }}
        </h1>
        <div class="flex gap-2">
            @can('edit employees')
                <a href="{{ route('employees.edit', $employee) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
            @endcan
            <a href="{{ route('employees.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Employee Status Badge -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full text-white
                    @if($employee->status === 'active') bg-green-500
                    @elseif($employee->status === 'terminated') bg-red-500
                    @elseif($employee->status === 'suspended') bg-yellow-500
                    @endif">
                    {{ ucfirst($employee->status) }}
                </span>
                @if($employee->termination_date)
                    <span class="ml-4 text-sm text-gray-600 dark:text-gray-400">
                        Terminated on {{ $employee->termination_date->format('M d, Y') }}
                    </span>
                @endif
            </div>
            <div>
                @can('delete employees')
                    @if($employee->isActive())
                        <form action="{{ route('employees.terminate', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this employee?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-user-times mr-2"></i>Terminate
                            </button>
                        </form>
                    @elseif($employee->status === 'terminated')
                        <form action="{{ route('employees.reactivate', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Reactivate this employee?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-user-check mr-2"></i>Reactivate
                            </button>
                        </form>
                    @endif
                @endcan
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Basic Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employee Number</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->employee_number }}</p>
                </div>
                @if($employee->user)
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Linked User</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $employee->user->name }}
                            <br>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $employee->user->email }}</span>
                        </p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Hire Date</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->hire_date?->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Department</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->department ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Position</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->position ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Employment Details -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Employment Details</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Employment Type</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ ucfirst($employee->employment_type) }}</p>
                </div>
                @if($employee->isSalaried())
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Base Salary</p>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">LKR {{ number_format($employee->base_salary, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Pay Frequency</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ ucfirst($employee->pay_frequency ?? 'Monthly') }}</p>
                    </div>
                @else
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Hourly Rate</p>
                        <p class="text-lg font-semibold text-green-600 dark:text-green-400">LKR {{ number_format($employee->hourly_rate, 2) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- EPF Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">EPF Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">EPF Number</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->epf_number ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Banking Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Banking Information</h2>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Bank Name</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->bank_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Account Number</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->bank_account_number ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Branch</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $employee->bank_branch ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes -->
    @if($employee->notes)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Notes</h2>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $employee->notes }}</p>
        </div>
    @endif

    <!-- Recent Payroll Entries -->
    @if($employee->payrollEntries->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mt-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Payroll History</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Gross Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Net Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($employee->payrollEntries->take(5) as $entry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $entry->payrollPeriod->period_start->format('M d') }} - {{ $entry->payrollPeriod->period_end->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    {{ number_format($entry->getTotalHours(), 2) }} hrs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                    LKR {{ number_format($entry->gross_pay, 2) }}
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
