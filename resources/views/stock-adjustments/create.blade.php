@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">
            <i class="fas fa-plus-circle mr-2"></i>Create Stock Adjustment
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Stock Adjustments &gt; Create</p>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded relative mb-6" role="alert">
            <strong class="font-bold">Please correct the following errors:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('stock-adjustments.store') }}" method="POST" id="adjustmentForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column (2/3 width) - Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Pre-selected Stock Info (if stock is provided) -->
                @if(isset($stock))
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-300 mb-3">
                            <i class="fas fa-info-circle mr-2"></i>Selected Stock
                        </h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-blue-700 dark:text-blue-400 font-medium">Product:</span>
                                <span class="text-blue-900 dark:text-blue-200">{{ $stock->product->product_name }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-400 font-medium">Batch:</span>
                                <span class="text-blue-900 dark:text-blue-200">{{ $stock->batch->batch_number }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-400 font-medium">Available Qty:</span>
                                <span class="text-blue-900 dark:text-blue-200 font-semibold">{{ number_format($stock->available_quantity, 4) }}</span>
                            </div>
                            <div>
                                <span class="text-blue-700 dark:text-blue-400 font-medium">Cost Price:</span>
                                <span class="text-blue-900 dark:text-blue-200">LKR {{ number_format($stock->cost_price, 2) }}</span>
                            </div>
                        </div>
                        <input type="hidden" name="stock_id" value="{{ $stock->id }}" id="stock_id">
                    </div>
                @endif

                <!-- Stock Selection (if not pre-selected) -->
                @if(!isset($stock))
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                            <i class="fas fa-box mr-2"></i>Select Stock
                        </h2>
                        <div>
                            <label for="stock_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Stock Item <span class="text-red-500">*</span>
                            </label>
                            <select name="stock_id" id="stock_id" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                onchange="fetchStockDetails(this.value)">
                                <option value="">Select a stock item...</option>
                                @foreach($stocks as $stockItem)
                                    <option value="{{ $stockItem->id }}"
                                        data-available="{{ $stockItem->available_quantity }}"
                                        data-cost="{{ $stockItem->cost_price }}"
                                        {{ old('stock_id') == $stockItem->id ? 'selected' : '' }}>
                                        {{ $stockItem->product->product_name }} - Batch: {{ $stockItem->batch->batch_number }} (Available: {{ number_format($stockItem->available_quantity, 4) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('stock_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                @endif

                <!-- Adjustment Details -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-edit mr-2"></i>Adjustment Details
                    </h2>

                    <!-- Adjustment Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Adjustment Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ old('type') === 'increase' || !old('type') ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-green-300' }}">
                                <input type="radio" name="type" value="increase" required
                                    class="mr-3 w-4 h-4 text-green-600"
                                    {{ old('type') === 'increase' || !old('type') ? 'checked' : '' }}
                                    onchange="updateTypeIndicator()">
                                <div class="flex items-center">
                                    <i class="fas fa-arrow-up text-green-600 dark:text-green-400 text-xl mr-2"></i>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Increase</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Add stock quantity</p>
                                    </div>
                                </div>
                            </label>

                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ old('type') === 'decrease' ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600 hover:border-red-300' }}">
                                <input type="radio" name="type" value="decrease" required
                                    class="mr-3 w-4 h-4 text-red-600"
                                    {{ old('type') === 'decrease' ? 'checked' : '' }}
                                    onchange="updateTypeIndicator()">
                                <div class="flex items-center">
                                    <i class="fas fa-arrow-down text-red-600 dark:text-red-400 text-xl mr-2"></i>
                                    <div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Decrease</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Reduce stock quantity</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Quantity -->
                        <div>
                            <label for="quantity_adjusted" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Adjustment Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="quantity_adjusted" id="quantity_adjusted" required
                                min="0.0001" max="99999.9999" step="0.0001"
                                value="{{ old('quantity_adjusted') }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                oninput="calculateSummary()">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="availableQtyHint">
                                Enter positive value only (decimal up to 4 places)
                            </p>
                            @error('quantity_adjusted')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Reason <span class="text-red-500">*</span>
                            </label>
                            <select name="reason" id="reason" required
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select reason...</option>
                                <option value="Damage" {{ old('reason') === 'Damage' ? 'selected' : '' }}>Damage</option>
                                <option value="Theft/Loss" {{ old('reason') === 'Theft/Loss' ? 'selected' : '' }}>Theft/Loss</option>
                                <option value="Recount" {{ old('reason') === 'Recount' ? 'selected' : '' }}>Physical Recount</option>
                                <option value="Return to Supplier" {{ old('reason') === 'Return to Supplier' ? 'selected' : '' }}>Return to Supplier</option>
                                <option value="Found Item" {{ old('reason') === 'Found Item' ? 'selected' : '' }}>Found Item</option>
                                <option value="Expired" {{ old('reason') === 'Expired' ? 'selected' : '' }}>Expired</option>
                                <option value="Other" {{ old('reason') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('reason')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Adjustment Date -->
                        <div>
                            <label for="adjustment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Adjustment Date (Optional)
                            </label>
                            <input type="date" name="adjustment_date" id="adjustment_date"
                                value="{{ old('adjustment_date', date('Y-m-d')) }}"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Defaults to today if not specified</p>
                            @error('adjustment_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Additional Notes (Optional)
                        </label>
                        <textarea name="notes" id="notes" rows="3" maxlength="1000"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                            placeholder="Add any additional information about this adjustment...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('stock-adjustments.index') }}"
                        class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" id="submitBtn"
                        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded transition-colors">
                        <i class="fas fa-save mr-2"></i>Create Adjustment
                    </button>
                </div>
            </div>

            <!-- Right Column (1/3 width) - Summary Panel -->
            <div class="space-y-6">
                <!-- Summary Card (Sticky on desktop) -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 lg:sticky lg:top-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">
                        <i class="fas fa-calculator mr-2"></i>Adjustment Summary
                    </h3>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Current Quantity</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" id="summary_current">-</span>
                        </div>

                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Adjustment</span>
                            <span class="text-sm font-semibold" id="summary_adjustment">-</span>
                        </div>

                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">New Quantity</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" id="summary_new">-</span>
                        </div>

                        <div class="flex justify-between items-center pb-2 border-b border-gray-200 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white" id="summary_cost">-</span>
                        </div>

                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base font-semibold text-gray-800 dark:text-white">Total Value Impact</span>
                            <span class="text-lg font-bold" id="summary_total_value">-</span>
                        </div>
                    </div>

                    <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-md p-3">
                        <p class="text-xs text-yellow-800 dark:text-yellow-300">
                            <i class="fas fa-info-circle mr-1"></i>
                            This adjustment will be pending approval before affecting stock levels and accounting.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Store stock data
let stockData = {
    @if(isset($stock))
        available: {{ $stock->available_quantity }},
        cost: {{ $stock->cost_price }}
    @else
        available: 0,
        cost: 0
    @endif
};

@if(!isset($stock))
// Fetch stock details when selection changes
function fetchStockDetails(stockId) {
    if (!stockId) {
        stockData.available = 0;
        stockData.cost = 0;
        calculateSummary();
        return;
    }

    const option = document.querySelector(`#stock_id option[value="${stockId}"]`);
    if (option) {
        stockData.available = parseFloat(option.dataset.available) || 0;
        stockData.cost = parseFloat(option.dataset.cost) || 0;
        calculateSummary();
    }
}
@endif

// Update type indicator styling
function updateTypeIndicator() {
    const increaseRadio = document.querySelector('input[name="type"][value="increase"]');
    const decreaseRadio = document.querySelector('input[name="type"][value="decrease"]');
    const increaseLabel = increaseRadio.closest('label');
    const decreaseLabel = decreaseRadio.closest('label');

    if (increaseRadio.checked) {
        increaseLabel.classList.add('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
        increaseLabel.classList.remove('border-gray-300', 'dark:border-gray-600');
        decreaseLabel.classList.remove('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
        decreaseLabel.classList.add('border-gray-300', 'dark:border-gray-600');
    } else if (decreaseRadio.checked) {
        decreaseLabel.classList.add('border-red-500', 'bg-red-50', 'dark:bg-red-900/20');
        decreaseLabel.classList.remove('border-gray-300', 'dark:border-gray-600');
        increaseLabel.classList.remove('border-green-500', 'bg-green-50', 'dark:bg-green-900/20');
        increaseLabel.classList.add('border-gray-300', 'dark:border-gray-600');
    }

    calculateSummary();
}

// Calculate and update summary
function calculateSummary() {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    const quantity = parseFloat(document.getElementById('quantity_adjusted').value) || 0;

    // Update summary panel
    const currentQty = stockData.available;
    const costPrice = stockData.cost;

    document.getElementById('summary_current').textContent = currentQty > 0 ? currentQty.toFixed(4) : '-';
    document.getElementById('summary_cost').textContent = costPrice > 0 ? `LKR ${costPrice.toFixed(2)}` : '-';

    if (quantity > 0 && currentQty > 0 && type) {
        const isIncrease = type === 'increase';
        const newQty = isIncrease ? currentQty + quantity : currentQty - quantity;
        const totalValue = quantity * costPrice;

        // Update adjustment display
        const adjustmentEl = document.getElementById('summary_adjustment');
        adjustmentEl.textContent = `${isIncrease ? '+' : '-'}${quantity.toFixed(4)}`;
        adjustmentEl.className = `text-sm font-semibold ${isIncrease ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`;

        // Update new quantity
        document.getElementById('summary_new').textContent = newQty.toFixed(4);

        // Update total value
        const totalValueEl = document.getElementById('summary_total_value');
        totalValueEl.textContent = `${isIncrease ? '+' : '-'}LKR ${totalValue.toFixed(2)}`;
        totalValueEl.className = `text-lg font-bold ${isIncrease ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'}`;

        // Update hint text
        if (!isIncrease && quantity > currentQty) {
            document.getElementById('availableQtyHint').innerHTML = '<span class="text-red-600 dark:text-red-400"><i class="fas fa-exclamation-triangle mr-1"></i>Cannot decrease more than available quantity!</span>';
            document.getElementById('submitBtn').disabled = true;
        } else {
            document.getElementById('availableQtyHint').innerHTML = `Available: ${currentQty.toFixed(4)}`;
            document.getElementById('submitBtn').disabled = false;
        }
    } else {
        document.getElementById('summary_adjustment').textContent = '-';
        document.getElementById('summary_new').textContent = '-';
        document.getElementById('summary_total_value').textContent = '-';
        document.getElementById('availableQtyHint').textContent = 'Enter positive value only (decimal up to 4 places)';
    }
}

// Form validation before submit
document.getElementById('adjustmentForm').addEventListener('submit', function(e) {
    const type = document.querySelector('input[name="type"]:checked')?.value;
    const quantity = parseFloat(document.getElementById('quantity_adjusted').value) || 0;

    if (type === 'decrease' && quantity > stockData.available) {
        e.preventDefault();
        alert('Cannot decrease more than the available quantity!');
        return false;
    }

    return confirm('Are you sure you want to create this stock adjustment? It will be pending approval before taking effect.');
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateSummary();
});
</script>
@endsection
