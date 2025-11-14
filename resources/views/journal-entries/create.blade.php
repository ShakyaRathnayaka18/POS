@extends('layouts.app')

@section('title', 'Create Journal Entry')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Journal Entry</h1>
        <a href="{{ route('journal-entries.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
            <i class="fas fa-arrow-left mr-2"></i>Back to List
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('journal-entries.store') }}" method="POST" id="journalEntryForm">
        @csrf

        <!-- Entry Details -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Entry Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="entry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Entry Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="entry_date" name="entry_date" value="{{ old('entry_date', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('entry_date') border-red-500 @enderror"
                           required>
                    @error('entry_date')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white @error('description') border-red-500 @enderror"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Journal Entry Lines -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Journal Entry Lines</h2>
                <button type="button" onclick="addLine()" class="bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>Add Line
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Account
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
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody id="linesTableBody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        <!-- Lines will be added here dynamically -->
                    </tbody>
                    <tfoot class="bg-blue-50 dark:bg-blue-900/20">
                        <tr class="font-bold">
                            <td colspan="2" class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                Total
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                LKR <span id="totalDebits">0.00</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-right text-gray-900 dark:text-white">
                                LKR <span id="totalCredits">0.00</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Balance Status -->
            <div id="balanceStatus" class="mt-4 p-4 rounded hidden">
                <div class="flex items-center">
                    <i id="balanceIcon" class="text-xl mr-3"></i>
                    <span id="balanceMessage" class="font-medium"></span>
                </div>
            </div>

            @error('lines')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <a href="{{ route('journal-entries.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    Cancel
                </a>
                <button type="submit" id="submitButton" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-save mr-2"></i>Create Journal Entry
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let lineCounter = 0;
const accounts = @json($accounts);

function addLine() {
    lineCounter++;
    const tbody = document.getElementById('linesTableBody');
    const row = document.createElement('tr');
    row.id = `line-${lineCounter}`;
    row.innerHTML = `
        <td class="px-4 py-3">
            <select name="lines[${lineCounter}][account_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" required onchange="calculateTotals()">
                <option value="">Select Account...</option>
                ${accounts.map(account => `
                    <option value="${account.id}">${account.account_code} - ${account.account_name}</option>
                `).join('')}
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="text" name="lines[${lineCounter}][description]"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"
                   placeholder="Line description...">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="lines[${lineCounter}][debit_amount]" step="0.01" min="0" value="0"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-right"
                   onchange="handleAmountChange(${lineCounter}, 'debit')" required>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="lines[${lineCounter}][credit_amount]" step="0.01" min="0" value="0"
                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white text-right"
                   onchange="handleAmountChange(${lineCounter}, 'credit')" required>
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeLine(${lineCounter})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    calculateTotals();
}

function removeLine(lineId) {
    const row = document.getElementById(`line-${lineId}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

function handleAmountChange(lineId, type) {
    const debitInput = document.querySelector(`input[name="lines[${lineId}][debit_amount]"]`);
    const creditInput = document.querySelector(`input[name="lines[${lineId}][credit_amount]"]`);

    if (type === 'debit' && parseFloat(debitInput.value) > 0) {
        creditInput.value = '0';
    } else if (type === 'credit' && parseFloat(creditInput.value) > 0) {
        debitInput.value = '0';
    }

    calculateTotals();
}

function calculateTotals() {
    const debitInputs = document.querySelectorAll('input[name$="[debit_amount]"]');
    const creditInputs = document.querySelectorAll('input[name$="[credit_amount]"]');

    let totalDebits = 0;
    let totalCredits = 0;

    debitInputs.forEach(input => {
        totalDebits += parseFloat(input.value) || 0;
    });

    creditInputs.forEach(input => {
        totalCredits += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebits').textContent = totalDebits.toFixed(2);
    document.getElementById('totalCredits').textContent = totalCredits.toFixed(2);

    // Update balance status
    const balanceStatus = document.getElementById('balanceStatus');
    const balanceIcon = document.getElementById('balanceIcon');
    const balanceMessage = document.getElementById('balanceMessage');
    const submitButton = document.getElementById('submitButton');

    const lineCount = document.querySelectorAll('#linesTableBody tr').length;

    if (lineCount < 2) {
        balanceStatus.classList.remove('hidden');
        balanceStatus.className = 'mt-4 p-4 rounded bg-yellow-50 dark:bg-yellow-900/20';
        balanceIcon.className = 'fas fa-exclamation-triangle text-yellow-600 text-xl mr-3';
        balanceMessage.className = 'font-medium text-gray-900 dark:text-white';
        balanceMessage.textContent = 'At least 2 lines are required for a journal entry.';
        submitButton.disabled = true;
    } else if (totalDebits === 0 && totalCredits === 0) {
        balanceStatus.classList.remove('hidden');
        balanceStatus.className = 'mt-4 p-4 rounded bg-yellow-50 dark:bg-yellow-900/20';
        balanceIcon.className = 'fas fa-exclamation-triangle text-yellow-600 text-xl mr-3';
        balanceMessage.className = 'font-medium text-gray-900 dark:text-white';
        balanceMessage.textContent = 'Please enter debit and credit amounts.';
        submitButton.disabled = true;
    } else if (Math.abs(totalDebits - totalCredits) < 0.01) {
        balanceStatus.classList.remove('hidden');
        balanceStatus.className = 'mt-4 p-4 rounded bg-green-50 dark:bg-green-900/20';
        balanceIcon.className = 'fas fa-check-circle text-green-600 text-xl mr-3';
        balanceMessage.className = 'font-medium text-gray-900 dark:text-white';
        balanceMessage.textContent = 'Entry is balanced! Total Debits = Total Credits';
        submitButton.disabled = false;
    } else {
        balanceStatus.classList.remove('hidden');
        balanceStatus.className = 'mt-4 p-4 rounded bg-red-50 dark:bg-red-900/20';
        balanceIcon.className = 'fas fa-exclamation-circle text-red-600 text-xl mr-3';
        balanceMessage.className = 'font-medium text-gray-900 dark:text-white';
        balanceMessage.textContent = `Entry is NOT balanced! Difference: LKR ${Math.abs(totalDebits - totalCredits).toFixed(2)}`;
        submitButton.disabled = true;
    }
}

// Add initial 2 lines on page load
document.addEventListener('DOMContentLoaded', function() {
    addLine();
    addLine();
});
</script>
@endsection
