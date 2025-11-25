@extends('layouts.app')

@section('title', 'Payroll Periods')

@section('content')
<div class="container mx-auto px-4 py-6" x-data="{ activeTab: '{{ request('tab', 'periods') }}' }">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Payroll Management</h1>
        @can('process payroll')
            <a href="{{ route('payroll.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Create New Period
            </a>
        @endcan
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8">
                <button
                    @click="activeTab = 'periods'"
                    :class="activeTab === 'periods' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fas fa-calendar-alt mr-2"></i>Payroll Periods
                </button>
                @can('process payroll')
                    <button
                        @click="activeTab = 'settings'"
                        :class="activeTab === 'settings' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        <i class="fas fa-cog mr-2"></i>Payroll Settings
                    </button>
                @endcan
            </nav>
        </div>
    </div>

    <!-- Periods Tab -->
    <div x-show="activeTab === 'periods'" x-transition>
        <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Filters</h2>
        <form action="{{ route('payroll.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select
                    name="status"
                    id="status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Payroll Periods Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employees</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Gross Pay</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Net Pay</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Processed By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($periods as $period)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $period->period_start->format('M d, Y') }} - {{ $period->period_end->format('M d, Y') }}
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
                            {{ $period->getEmployeeCount() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            LKR {{ number_format($period->getTotalGrossPay(), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            LKR {{ number_format($period->getTotalNetPay(), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $period->processedBy?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('payroll.show', $period) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @can('process payroll')
                                @if($period->canBeProcessed())
                                    <form action="{{ route('payroll.process', $period) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 mr-3">
                                            <i class="fas fa-calculator"></i> Process
                                        </button>
                                    </form>
                                @endif
                            @endcan
                            @can('approve payroll')
                                @if($period->canBeApproved())
                                    <form action="{{ route('payroll.approve', $period) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400 mr-3">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                @endif
                            @endcan
                            @if($period->isApproved())
                                <form action="{{ route('payroll.mark-paid', $period) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">
                                        <i class="fas fa-money-bill-wave"></i> Mark Paid
                                    </button>
                                </form>
                            @endif
                            @if($period->isDraft())
                                <form action="{{ route('payroll.destroy', $period) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this draft period?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No payroll periods found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($periods->hasPages())
        <div class="mt-6">
            {{ $periods->links() }}
        </div>
    @endif
    </div>

    <!-- Settings Tab -->
    @can('process payroll')
    <div x-show="activeTab === 'settings'" x-transition>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-white">Payroll Calculation Settings</h2>

            @if(session('settings_success'))
                <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('settings_success') }}</span>
                </div>
            @endif

            <form action="{{ route('payroll.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ otMode: '{{ old('ot_calculation_mode', $settings->ot_calculation_mode) }}' }">
                    <!-- Overtime Settings -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-clock text-blue-500 mr-2"></i>Overtime Settings
                        </h3>

                        <!-- OT Calculation Mode -->
                        <div class="ml-6 mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                OT Calculation Method
                            </label>
                            <div class="flex gap-6">
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="radio"
                                        name="ot_calculation_mode"
                                        value="multiplier"
                                        x-model="otMode"
                                        class="mr-2 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Multiplier (e.g., 1.5x, 2x)</span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="radio"
                                        name="ot_calculation_mode"
                                        value="fixed_rate"
                                        x-model="otMode"
                                        class="mr-2 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Fixed Hourly Rate (e.g., 1000 LKR/hour)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Multiplier Mode Fields -->
                        <div x-show="otMode === 'multiplier'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-6">
                            <div>
                                <label for="ot_weekday_multiplier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Weekday OT Multiplier
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="1.0"
                                    name="ot_weekday_multiplier"
                                    id="ot_weekday_multiplier"
                                    value="{{ old('ot_weekday_multiplier', $settings->ot_weekday_multiplier) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    :required="otMode === 'multiplier'">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">e.g., 1.5 = 150% of regular rate</p>
                            </div>

                            <div>
                                <label for="ot_weekend_multiplier" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Weekend OT Multiplier
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="1.0"
                                    name="ot_weekend_multiplier"
                                    id="ot_weekend_multiplier"
                                    value="{{ old('ot_weekend_multiplier', $settings->ot_weekend_multiplier) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    :required="otMode === 'multiplier'">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">e.g., 2.0 = 200% of regular rate</p>
                            </div>

                            <div>
                                <label for="daily_hours_threshold" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Daily Hours Threshold
                                </label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="1.0"
                                    name="daily_hours_threshold"
                                    id="daily_hours_threshold"
                                    value="{{ old('daily_hours_threshold', $settings->daily_hours_threshold) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hours before OT starts (e.g., 8.0)</p>
                            </div>
                        </div>

                        <!-- Fixed Rate Mode Fields -->
                        <div x-show="otMode === 'fixed_rate'" x-transition class="grid grid-cols-1 md:grid-cols-3 gap-4 ml-6">
                            <div>
                                <label for="ot_weekday_fixed_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Weekday OT Rate (LKR/hour)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="ot_weekday_fixed_rate"
                                    id="ot_weekday_fixed_rate"
                                    value="{{ old('ot_weekday_fixed_rate', $settings->ot_weekday_fixed_rate) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    :required="otMode === 'fixed_rate'">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">e.g., 1000 LKR per OT hour</p>
                            </div>

                            <div>
                                <label for="ot_weekend_fixed_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Weekend OT Rate (LKR/hour)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="ot_weekend_fixed_rate"
                                    id="ot_weekend_fixed_rate"
                                    value="{{ old('ot_weekend_fixed_rate', $settings->ot_weekend_fixed_rate) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    :required="otMode === 'fixed_rate'">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">e.g., 1500 LKR per OT hour</p>
                            </div>

                            <div>
                                <label for="daily_hours_threshold_fixed" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Daily Hours Threshold
                                </label>
                                <input
                                    type="number"
                                    step="0.5"
                                    min="1.0"
                                    name="daily_hours_threshold"
                                    id="daily_hours_threshold_fixed"
                                    value="{{ old('daily_hours_threshold', $settings->daily_hours_threshold) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Hours before OT starts (e.g., 8.0)</p>
                            </div>
                        </div>
                    </div>

                    <!-- EPF Settings -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-piggy-bank text-green-500 mr-2"></i>EPF (Employee Provident Fund)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-6">
                            <div>
                                <label for="epf_employee_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Employee Contribution (%)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    name="epf_employee_percentage"
                                    id="epf_employee_percentage"
                                    value="{{ old('epf_employee_percentage', $settings->epf_employee_percentage) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sri Lankan standard: 8%</p>
                            </div>

                            <div>
                                <label for="epf_employer_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Employer Contribution (%)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    name="epf_employer_percentage"
                                    id="epf_employer_percentage"
                                    value="{{ old('epf_employer_percentage', $settings->epf_employer_percentage) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sri Lankan standard: 12%</p>
                            </div>
                        </div>
                    </div>

                    <!-- ETF Settings -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center">
                            <i class="fas fa-landmark text-purple-500 mr-2"></i>ETF (Employee Trust Fund)
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 ml-6">
                            <div>
                                <label for="etf_employer_percentage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Employer Contribution (%)
                                </label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    name="etf_employer_percentage"
                                    id="etf_employer_percentage"
                                    value="{{ old('etf_employer_percentage', $settings->etf_employer_percentage) }}"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                    required>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sri Lankan standard: 3%</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 pt-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-2"></i>These settings will apply to all future payroll calculations.
                    </p>
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>

            @if($settings->updated_by)
                <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                    Last updated by {{ $settings->updatedBy->name }} on {{ $settings->updated_at->format('M d, Y \a\t h:i A') }}
                </div>
            @endif
        </div>
    </div>
    @endcan
</div>
@endsection
