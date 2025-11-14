@extends('layouts.app')

@section('title', 'Create Account')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h1>
        <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('accounts.store') }}" method="POST">
        @csrf

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Account Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Account Code -->
                <div>
                    <label for="account_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Account Code <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="account_code" name="account_code" value="{{ old('account_code') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('account_code') border-red-500 @enderror"
                           placeholder="e.g., 1110"
                           required>
                    @error('account_code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Account code ranges: 1000-1999 (Assets), 2000-2999 (Liabilities), 3000-3999 (Equity), 4000-4999 (Revenue), 5000-9999 (Expenses)
                    </p>
                </div>

                <!-- Account Name -->
                <div>
                    <label for="account_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Account Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="account_name" name="account_name" value="{{ old('account_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('account_name') border-red-500 @enderror"
                           placeholder="e.g., Cash in Hand"
                           required>
                    @error('account_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Type -->
                <div>
                    <label for="account_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Account Type <span class="text-red-500">*</span>
                    </label>
                    <select id="account_type_id" name="account_type_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('account_type_id') border-red-500 @enderror"
                            required>
                        <option value="">Select Account Type...</option>
                        @foreach($accountTypes as $type)
                            <option value="{{ $type->id }}" {{ old('account_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} ({{ ucfirst($type->normal_balance) }} balance)
                            </option>
                        @endforeach
                    </select>
                    @error('account_type_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Parent Account -->
                <div>
                    <label for="parent_account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Parent Account (Optional)
                    </label>
                    <select id="parent_account_id" name="parent_account_id"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('parent_account_id') border-red-500 @enderror">
                        <option value="">None</option>
                        @foreach($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}" {{ old('parent_account_id') == $parentAccount->id ? 'selected' : '' }}>
                                {{ $parentAccount->account_code }} - {{ $parentAccount->account_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_account_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Select a parent account if this is a sub-account
                    </p>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Description (Optional)
                    </label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror"
                              placeholder="Enter account description...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Inactive accounts cannot be used in new journal entries
                    </p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('accounts.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-save mr-2"></i>Create Account
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
