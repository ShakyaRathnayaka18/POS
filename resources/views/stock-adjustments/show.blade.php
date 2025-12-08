@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ $stockAdjustment->adjustment_number }}
                @if($stockAdjustment->type === 'increase')
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 ml-2">
                        <i class="fas fa-arrow-up mr-1"></i>Increase
                    </span>
                @else
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400 ml-2">
                        <i class="fas fa-arrow-down mr-1"></i>Decrease
                    </span>
                @endif
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Stock Adjustment Details</p>
        </div>
        <a href="{{ route('stock-adjustments.index') }}"
            class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Status & Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Current Status</p>
                    @if($stockAdjustment->status === 'pending')
                        <span class="px-4 py-2 inline-flex text-base leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-400">
                            <i class="fas fa-clock mr-2"></i>Pending Approval
                        </span>
                    @elseif($stockAdjustment->status === 'approved')
                        <span class="px-4 py-2 inline-flex text-base leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400">
                            <i class="fas fa-check mr-2"></i>Approved
                        </span>
                    @else
                        <span class="px-4 py-2 inline-flex text-base leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-400">
                            <i class="fas fa-times mr-2"></i>Rejected
                        </span>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            @can('manage stock adjustments')
                @if($stockAdjustment->status === 'pending')
                    <div class="flex gap-3">
                        <form action="{{ route('stock-adjustments.approve', $stockAdjustment) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to approve this adjustment? This will update the stock quantity and create accounting entries.')"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded transition-colors">
                                <i class="fas fa-check mr-2"></i>Approve
                            </button>
                        </form>
                        <form action="{{ route('stock-adjustments.reject', $stockAdjustment) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" onclick="return confirm('Are you sure you want to reject this adjustment?')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded transition-colors">
                                <i class="fas fa-times mr-2"></i>Reject
                            </button>
                        </form>
                    </div>
                @endif
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column (2/3 width on large screens) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Adjustment Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2"></i>Adjustment Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Adjustment Number</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $stockAdjustment->adjustment_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Adjustment Date</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->adjustment_date->format('F d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Created By</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->creator->name }}</p>
                    </div>
                    @if($stockAdjustment->approved_by)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $stockAdjustment->status === 'approved' ? 'Approved' : 'Rejected' }} By
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->approver->name }}</p>
                    </div>
                    @endif
                    @if($stockAdjustment->approved_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $stockAdjustment->status === 'approved' ? 'Approved' : 'Rejected' }} At
                        </label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->approved_at->format('F d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product & Stock Details -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-box mr-2"></i>Product & Stock Details
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Product Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ $stockAdjustment->product->product_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Category</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->product->category->category_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Brand</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->product->brand->brand_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Batch Number</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $stockAdjustment->batch->batch_number }}</p>
                    </div>
                    @if($stockAdjustment->stock->batch->expiry_date)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Batch Expiry</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->stock->batch->expiry_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                    @if($stockAdjustment->stock->batch->goodReceiveNote)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">GRN Reference</label>
                        <p class="mt-1 text-sm">
                            <a href="{{ route('good-receive-notes.show', $stockAdjustment->stock->batch->goodReceiveNote) }}"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $stockAdjustment->stock->batch->goodReceiveNote->grn_number }}
                            </a>
                        </p>
                    </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Current Available Quantity</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white font-semibold">{{ number_format($stockAdjustment->stock->available_quantity, 4) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Selling Price</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">LKR {{ number_format($stockAdjustment->stock->selling_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Reason & Notes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-comment-alt mr-2"></i>Reason & Notes
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Reason</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            <i class="fas fa-tag mr-2"></i>{{ $stockAdjustment->reason }}
                        </p>
                    </div>
                    @if($stockAdjustment->notes)
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Additional Notes</label>
                        <p class="mt-1 text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-900 p-3 rounded">{{ $stockAdjustment->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Journal Entry (if approved) -->
            @if($stockAdjustment->status === 'approved' && $stockAdjustment->journalEntry)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-book mr-2"></i>Accounting Entry
                </h2>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Entry Date</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->journalEntry->entry_date->format('Y-m-d') }}</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Description</label>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $stockAdjustment->journalEntry->description }}</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Debit</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($stockAdjustment->journalEntry->lines as $line)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                                    {{ $line->account->account_code }} - {{ $line->account->account_name }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $line->debit_amount > 0 ? 'LKR ' . number_format($line->debit_amount, 2) : '-' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white">
                                    {{ $line->credit_amount > 0 ? 'LKR ' . number_format($line->credit_amount, 2) : '-' }}
                                </td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50 dark:bg-gray-900 font-bold">
                                <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">Total</td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white">
                                    LKR {{ number_format($stockAdjustment->journalEntry->lines->sum('debit_amount'), 2) }}
                                </td>
                                <td class="px-4 py-2 text-sm text-right text-gray-900 dark:text-white">
                                    LKR {{ number_format($stockAdjustment->journalEntry->lines->sum('credit_amount'), 2) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column (1/3 width on large screens) -->
        <div class="space-y-6">
            <!-- Quantity & Financial Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-calculator mr-2"></i>Summary
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quantity Before</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($stockAdjustment->quantity_before, 4) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Adjustment</span>
                        <span class="text-sm font-semibold {{ $stockAdjustment->type === 'increase' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $stockAdjustment->type === 'increase' ? '+' : '-' }}{{ number_format($stockAdjustment->quantity_adjusted, 4) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Quantity After</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($stockAdjustment->quantity_after, 4) }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">LKR {{ number_format($stockAdjustment->cost_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-base font-semibold text-gray-800 dark:text-white">Total Value Impact</span>
                        <span class="text-lg font-bold {{ $stockAdjustment->type === 'increase' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $stockAdjustment->type === 'increase' ? '+' : '-' }}LKR {{ number_format($stockAdjustment->total_value, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    <i class="fas fa-history mr-2"></i>Timeline
                </h2>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Created</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stockAdjustment->created_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $stockAdjustment->creator->name }}</p>
                        </div>
                    </div>

                    @if($stockAdjustment->approved_at)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 {{ $stockAdjustment->status === 'approved' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }} rounded-full flex items-center justify-center">
                                <i class="fas {{ $stockAdjustment->status === 'approved' ? 'fa-check text-green-600 dark:text-green-400' : 'fa-times text-red-600 dark:text-red-400' }} text-xs"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $stockAdjustment->status === 'approved' ? 'Approved' : 'Rejected' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $stockAdjustment->approved_at->format('M d, Y h:i A') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">by {{ $stockAdjustment->approver->name }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
