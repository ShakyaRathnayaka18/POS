@extends('layouts.app')

@section('title', 'Edit Expense')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Expense</h1>
        <p class="text-gray-600 dark:text-gray-400">Expense Number: <span class="font-semibold">{{ $expense->expense_number }}</span></p>
    </div>

    <form action="{{ route('expenses.update', $expense) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Expense Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="expense_category_id" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                {{ (old('expense_category_id', $expense->expense_category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Expense Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $expense->title) }}" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Amount (LKR) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="amount" value="{{ old('amount', $expense->amount) }}" 
                        step="0.01" min="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Expense Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="expense_date" 
                        value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" 
                        required max="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Payment Method <span class="text-red-500">*</span>
                    </label>
                    <select name="payment_method" required
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                        <option value="">Select Method</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->value }}" 
                                {{ (old('payment_method', $expense->payment_method->value) == $method->value) ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $method->value)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reference Number -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Reference Number
                    </label>
                    <input type="text" name="reference_number" 
                        value="{{ old('reference_number', $expense->reference_number) }}"
                        placeholder="Cheque #, Transaction ID, etc."
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    @error('reference_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">{{ old('description', $expense->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Receipt (PDF or Image, max 5MB)
                </label>
                @if($expense->receipt_path)
                    <div class="mb-2">
                        <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank"
                            class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-sm">
                            <i class="fas fa-file mr-1"></i>Current Receipt
                        </a>
                    </div>
                @endif
                <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty to keep current receipt</p>
                @error('receipt')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Additional Notes
                </label>
                <textarea name="notes" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">{{ old('notes', $expense->notes) }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('expenses.show', $expense) }}"
                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                Update Expense
            </button>
        </div>
    </form>
</div>
@endsection
