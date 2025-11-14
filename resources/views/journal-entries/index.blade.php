@extends('layouts.app')

@section('title', 'Journal Entries')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Journal Entries</h1>
        @can('create journal entries')
        <a href="{{ route('journal-entries.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
            <i class="fas fa-plus mr-2"></i>Create Journal Entry
        </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <form action="{{ route('journal-entries.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Year</label>
                <select name="year" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Month</label>
                <select name="month" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="posted" {{ $status == 'posted' ? 'selected' : '' }}>Posted</option>
                    <option value="void" {{ $status == 'void' ? 'selected' : '' }}>Void</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Entry Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                @forelse($journalEntries as $entry)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                        {{ $entry->entry_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $entry->entry_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                        {{ \Illuminate\Support\Str::limit($entry->description, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($entry->status == 'draft') bg-yellow-100 text-yellow-800
                            @elseif($entry->status == 'posted') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($entry->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        {{ $entry->creator->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('journal-entries.show', $entry) }}" class="text-primary-600 hover:text-primary-900 dark:text-primary-400">
                            <i class="fas fa-eye"></i>
                        </a>
                        @if($entry->status == 'draft')
                            @can('post journal entries')
                            <form action="{{ route('journal-entries.post', $entry) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to post this journal entry?')" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                    <i class="fas fa-check"></i>
                                </button>
                            </form>
                            @endcan
                        @endif
                        @if($entry->status == 'posted')
                            @can('void journal entries')
                            <form action="{{ route('journal-entries.void', $entry) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" onclick="return confirm('Are you sure you want to void this journal entry?')" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </form>
                            @endcan
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        No journal entries found for this period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
