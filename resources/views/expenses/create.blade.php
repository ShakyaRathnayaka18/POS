@extends('layouts.app')

@section('title', 'Add Expense')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Expense</h1>
        <p class="text-gray-600 dark:text-gray-400">Expense Number: <span class="font-semibold">{{ $expenseNumber }}</span></p>
    </div>

    <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Expense Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="expense_category_id" id="expense_category_id" required
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('expense_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="openCategoryModal()" 
                            class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    @error('expense_category_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Expense Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required
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
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required
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
                    <input type="date" name="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                        max="{{ date('Y-m-d') }}"
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
                            <option value="{{ $method->value }}" {{ old('payment_method') == $method->value ? 'selected' : '' }}>
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
                    <input type="text" name="reference_number" value="{{ old('reference_number') }}"
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
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Receipt Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Receipt (PDF or Image, max 5MB)
                </label>
                <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
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
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('expenses.index') }}"
                class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit"
                class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                Submit Expense
            </button>
        </div>
    </form>
</div>

<!-- Add Category Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Add New Category</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category Name</label>
                    <input type="text" id="new_category_name"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white">
                    <p id="category_error" class="text-red-500 text-xs mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea id="new_category_description" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white"></textarea>
                </div>
                <div class="flex justify-end gap-2 mt-4">
                    <button onclick="closeCategoryModal()" type="button"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                        Cancel
                    </button>
                    <button onclick="saveCategory()" type="button"
                        class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                        Save Category
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openCategoryModal() {
    document.getElementById('categoryModal').classList.remove('hidden');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.add('hidden');
    document.getElementById('new_category_name').value = '';
    document.getElementById('new_category_description').value = '';
    document.getElementById('category_error').classList.add('hidden');
}

function saveCategory() {
    const name = document.getElementById('new_category_name').value;
    const description = document.getElementById('new_category_description').value;
    const errorEl = document.getElementById('category_error');

    if (!name) {
        errorEl.textContent = 'Category name is required';
        errorEl.classList.remove('hidden');
        return;
    }

    fetch('{{ route("expense-categories.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            category_name: name,
            description: description,
            is_active: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('expense_category_id');
            const option = new Option(data.category.category_name, data.category.id, true, true);
            select.add(option);
            closeCategoryModal();
        } else {
            errorEl.textContent = data.message;
            errorEl.classList.remove('hidden');
        }
    })
    .catch(error => {
        errorEl.textContent = 'Error creating category';
        errorEl.classList.remove('hidden');
    });
}
</script>
@endsection
