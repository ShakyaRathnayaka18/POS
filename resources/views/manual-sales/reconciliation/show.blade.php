@extends('layouts.app')

@section('title', 'Reconcile Manual Sale - ' . $manualSale->manual_sale_number)

@section('content')
    <div x-data="reconciliationInterface()" x-init="init()" class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-tasks mr-2"></i> Reconcile Manual Sale
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    Sale #: <span class="font-mono font-semibold">{{ $manualSale->manual_sale_number }}</span>
                </p>
            </div>
            <a href="{{ route('manual-sales.reconciliation.index') }}"
               class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-md font-medium">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Reconciliation Progress
                </h3>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    <span x-text="matchedCount"></span> / {{ $progress['total_items'] }} items matched
                </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4 dark:bg-gray-700">
                <div class="bg-yellow-500 h-4 rounded-full transition-all duration-300"
                     :style="`width: ${(matchedCount / {{ $progress['total_items'] }}) * 100}%`"></div>
            </div>
        </div>

        <!-- Sale Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Sale Information</h4>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Date:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $manualSale->created_at->format('M d, Y h:i A') }}</span></div>
                    <div><span class="text-gray-500">Cashier:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $manualSale->user->name }}</span></div>
                    <div><span class="text-gray-500">Payment:</span> <span class="font-medium text-gray-900 dark:text-white capitalize">{{ $manualSale->payment_method->value }}</span></div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Customer Information</h4>
                <div class="space-y-1 text-sm">
                    @if ($manualSale->customer_name)
                        <div><span class="text-gray-500">Name:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $manualSale->customer_name }}</span></div>
                        @if ($manualSale->customer_phone)
                            <div><span class="text-gray-500">Phone:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $manualSale->customer_phone }}</span></div>
                        @endif
                    @else
                        <p class="text-gray-400">Walk-in Customer</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Sale Totals</h4>
                <div class="space-y-1 text-sm">
                    <div><span class="text-gray-500">Subtotal:</span> <span class="font-medium text-gray-900 dark:text-white">LKR {{ number_format($manualSale->subtotal, 2) }}</span></div>
                    <div><span class="text-gray-500">Tax:</span> <span class="font-medium text-gray-900 dark:text-white">LKR {{ number_format($manualSale->tax, 2) }}</span></div>
                    <div class="pt-1 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-500">Total:</span> <span class="font-bold text-lg text-gray-900 dark:text-white">LKR {{ number_format($manualSale->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Items to Reconcile -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                    Items to Reconcile
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Manual Entry</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Barcode Scan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Matched Product</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($manualSale->items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->product_name }}</div>
                                    @if ($item->entered_barcode)
                                        <div class="text-xs text-gray-500 font-mono">Entered: {{ $item->entered_barcode }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">
                                    LKR {{ number_format($item->price, 2) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div x-show="!matches[{{ $item->id }}]">
                                        <input type="text"
                                               x-model="barcodeInputs[{{ $item->id }}]"
                                               @keydown.enter="searchProduct({{ $item->id }}, {{ $item->quantity }})"
                                               placeholder="Scan barcode..."
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-2 focus:ring-yellow-500 dark:bg-gray-700 dark:text-white text-sm">
                                    </div>
                                    <div x-show="matches[{{ $item->id }}]" class="text-sm text-green-600 dark:text-green-400">
                                        <i class="fas fa-check-circle mr-1"></i> Matched
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div x-show="!matches[{{ $item->id }}]" class="text-sm text-gray-400">
                                        <i class="fas fa-question-circle mr-1"></i> Not matched
                                    </div>
                                    <div x-show="matches[{{ $item->id }}]" class="text-sm">
                                        <div class="font-medium text-gray-900 dark:text-white" x-text="matches[{{ $item->id }}]?.product_name"></div>
                                        <div class="text-xs text-gray-500">
                                            <span x-text="matches[{{ $item->id }}]?.sku"></span> •
                                            <span x-text="matches[{{ $item->id }}]?.category"></span>
                                        </div>
                                        <div x-show="!matches[{{ $item->id }}]?.has_stock" class="text-xs text-red-600 mt-1">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Insufficient stock
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span x-show="!matches[{{ $item->id }}]" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <i class="fas fa-clock mr-1"></i> Pending
                                    </span>
                                    <span x-show="matches[{{ $item->id }}]" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-check mr-1"></i> Matched
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Error Message -->
        <div x-show="errorMessage" x-transition
             class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6"
             role="alert">
            <span x-text="errorMessage"></span>
        </div>

        <!-- Complete Reconciliation Button -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Ready to Complete?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        All items must be matched before you can complete reconciliation.
                    </p>
                </div>
                <button @click="completeReconciliation()"
                        :disabled="matchedCount < {{ $progress['total_items'] }} || isProcessing"
                        :class="matchedCount >= {{ $progress['total_items'] }} && !isProcessing ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-6 py-3 text-white rounded-md font-semibold transition-colors">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span x-show="!isProcessing">Complete Reconciliation</span>
                    <span x-show="isProcessing">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <script>
        function reconciliationInterface() {
            return {
                barcodeInputs: @json($manualSale->items->pluck('entered_barcode', 'id')->toArray()),
                matches: {},
                matchedCount: 0,
                errorMessage: '',
                isProcessing: false,

                init() {
                    // Initialize barcode inputs for items that already have barcodes
                    @foreach ($manualSale->items as $item)
                        @if ($item->entered_barcode)
                            // Auto-search if barcode was entered during manual sale
                            // this.searchProduct({{ $item->id }}, {{ $item->quantity }});
                        @endif
                    @endforeach
                },

                async searchProduct(itemId, quantity) {
                    const barcode = this.barcodeInputs[itemId];

                    if (!barcode || !barcode.trim()) {
                        this.errorMessage = 'Please enter a barcode';
                        return;
                    }

                    this.errorMessage = '';

                    try {
                        const response = await fetch('{{ route('manual-sales.reconciliation.search-product') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                barcode: barcode.trim(),
                                quantity: quantity
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Store match
                            this.matches[itemId] = data.product;
                            this.updateMatchedCount();

                            // Clear error
                            this.errorMessage = '';

                            // Show success notification
                            if (typeof toastr !== 'undefined') {
                                toastr.success(`Product matched: ${data.product.product_name}`);
                            }
                        } else {
                            this.errorMessage = data.message || 'Product not found with this barcode';
                        }
                    } catch (error) {
                        console.error('Search error:', error);
                        this.errorMessage = 'Error searching for product. Please try again.';
                    }
                },

                updateMatchedCount() {
                    this.matchedCount = Object.keys(this.matches).length;
                },

                async completeReconciliation() {
                    if (this.matchedCount < {{ $progress['total_items'] }}) {
                        this.errorMessage = 'Please match all items before completing reconciliation.';
                        return;
                    }

                    // Check for insufficient stock
                    const hasInsufficientStock = Object.values(this.matches).some(match => !match.has_stock);
                    if (hasInsufficientStock) {
                        if (!confirm('Some products have insufficient stock. Continue anyway? Stock will be deducted and may go negative.')) {
                            return;
                        }
                    }

                    this.isProcessing = true;
                    this.errorMessage = '';

                    try {
                        // Prepare matched products array
                        const matchedProducts = Object.entries(this.matches).map(([itemId, product]) => ({
                            item_id: parseInt(itemId),
                            product_id: product.id,
                            stock_id: null // Will be determined by SaleService using FIFO
                        }));

                        const response = await fetch('{{ route('manual-sales.reconciliation.reconcile', $manualSale) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                matched_products: matchedProducts
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Success - redirect will happen automatically
                            if (typeof toastr !== 'undefined') {
                                toastr.success('Reconciliation completed successfully!');
                            }
                            // The controller redirects, but in case it returns JSON:
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            this.errorMessage = data.message || 'Error completing reconciliation';
                            this.isProcessing = false;
                        }
                    } catch (error) {
                        console.error('Reconciliation error:', error);
                        this.errorMessage = 'Error completing reconciliation. Please try again.';
                        this.isProcessing = false;
                    }
                }
            };
        }
    </script>
@endsection
