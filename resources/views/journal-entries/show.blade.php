@extends('layouts.app')

@section('title', 'Journal Entry Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Journal Entry Details</h1>
        <div class="flex items-center space-x-3">
            <a href="{{ route('journal-entries.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <!-- Entry Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Entry Number</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $journalEntry->entry_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Entry Date</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $journalEntry->entry_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full
                    @if($journalEntry->status === 'posted') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                    @elseif($journalEntry->status === 'void') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400
                    @endif">
                    {{ ucfirst($journalEntry->status) }}
                </span>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Description</p>
            <p class="text-gray-900 dark:text-white">{{ $journalEntry->description }}</p>
        </div>

        @if($journalEntry->reference_type && $journalEntry->reference_id)
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <i class="fas fa-link mr-2"></i>
                <strong>Reference:</strong> {{ class_basename($journalEntry->reference_type) }} #{{ $journalEntry->reference_id }}
            </p>
        </div>
        @endif
    </div>

    <!-- Journal Entry Lines -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Journal Entry Lines</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Line #
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Account Code
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Account Name
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Debit
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Credit
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    @php
                        $totalDebits = 0;
                        $totalCredits = 0;
                    @endphp
                    @foreach($journalEntry->lines->sortBy('line_number') as $line)
                        @php
                            $totalDebits += $line->debit_amount;
                            $totalCredits += $line->credit_amount;
                        @endphp
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $line->line_number }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $line->account->account_code }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                <a href="{{ route('accounts.show', $line->account) }}"
                                   class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                    {{ $line->account->account_name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $line->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                @if($line->debit_amount > 0)
                                    {{ number_format($line->debit_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                @if($line->credit_amount > 0)
                                    {{ number_format($line->credit_amount, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-blue-50 dark:bg-blue-900/20">
                    <tr class="font-bold">
                        <td colspan="4" class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                            Total
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                            LKR {{ number_format($totalDebits, 2) }}
                        </td>
                        <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                            LKR {{ number_format($totalCredits, 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Balance Check -->
        <div class="mt-6 p-4 rounded {{ round($totalDebits, 2) === round($totalCredits, 2) ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }}">
            <div class="flex items-center">
                <i class="fas fa-{{ round($totalDebits, 2) === round($totalCredits, 2) ? 'check-circle text-green-600' : 'exclamation-circle text-red-600' }} text-xl mr-3"></i>
                <span class="text-gray-900 dark:text-white font-medium">
                    {{ round($totalDebits, 2) === round($totalCredits, 2) ? 'Entry is balanced! Total Debits = Total Credits' : 'WARNING: Entry is NOT balanced!' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Audit Information -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Audit Information</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Created By</p>
                <p class="text-gray-900 dark:text-white">{{ $journalEntry->creator->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $journalEntry->created_at->format('M d, Y h:i A') }}</p>
            </div>

            @if($journalEntry->status === 'posted' && $journalEntry->approver)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Posted By</p>
                <p class="text-gray-900 dark:text-white">{{ $journalEntry->approver->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $journalEntry->posted_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            @if($journalEntry->status === 'void' && $journalEntry->voider)
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Voided By</p>
                <p class="text-gray-900 dark:text-white">{{ $journalEntry->voider->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $journalEntry->voided_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h2>

        <div class="flex flex-wrap gap-3">
            @if($journalEntry->status === 'draft')
                @can('post journal entries')
                <form action="{{ route('journal-entries.post', $journalEntry) }}" method="POST" onsubmit="return confirm('Are you sure you want to post this journal entry?');">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        <i class="fas fa-check mr-2"></i>Post Entry
                    </button>
                </form>
                @endcan
            @endif

            @if($journalEntry->status === 'posted')
                @can('void journal entries')
                <form action="{{ route('journal-entries.void', $journalEntry) }}" method="POST" onsubmit="return confirm('Are you sure you want to void this journal entry? This will create a reversal entry.');">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        <i class="fas fa-ban mr-2"></i>Void Entry
                    </button>
                </form>
                @endcan
            @endif

            <a href="{{ route('journal-entries.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .bg-white, .bg-white * { visibility: visible; }
    .bg-white { position: absolute; left: 0; top: 0; width: 100%; }
    button { display: none !important; }
    form { display: none !important; }
    a { display: none !important; }
}
</style>
@endsection
