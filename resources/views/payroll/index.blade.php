@extends('layouts.app')

@section('title', 'Payroll Periods')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Payroll Periods</h1>
        @can('process payroll')
            <a href="{{ route('payroll.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Create New Period
            </a>
        @endcan
    </div>

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
                                <form action="{{ route('payroll.mark-as-paid', $period) }}" method="POST" class="inline">
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
@endsection
