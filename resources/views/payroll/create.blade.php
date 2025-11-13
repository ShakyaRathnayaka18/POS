@extends('layouts.app')

@section('title', 'Create Payroll Period')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Create Payroll Period</h1>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form action="{{ route('payroll.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Period Start Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="period_start"
                        id="period_start"
                        value="{{ old('period_start') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('period_start') border-red-500 @enderror">
                    @error('period_start')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Period End Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        name="period_end"
                        id="period_end"
                        value="{{ old('period_end') }}"
                        required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('period_end') border-red-500 @enderror">
                    @error('period_end')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Notes (Optional)
                </label>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button
                    type="submit"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                    <i class="fas fa-save mr-2"></i>Create Period
                </button>
                <a
                    href="{{ route('payroll.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                    <i class="fas fa-times mr-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
        <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-200 mb-2">
            <i class="fas fa-info-circle mr-2"></i>Information
        </h3>
        <ul class="text-sm text-blue-700 dark:text-blue-300 list-disc list-inside space-y-1">
            <li>The period will be created in "Draft" status</li>
            <li>After creation, you'll need to process the period to calculate payroll for all active employees</li>
            <li>Payroll periods cannot overlap with existing periods</li>
            <li>Processing will automatically calculate hours from approved shifts</li>
        </ul>
    </div>
</div>
@endsection
