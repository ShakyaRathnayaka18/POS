@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold text-gray-800 dark:text-white mb-6">Create Supplier Return</h1>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <form action="{{ route('supplier-returns.store') }}" method="POST" id="supplier-return-form">
                @csrf
                <!-- Section 1: Return Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="return_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Number</label>
                        <input type="text" name="return_number" id="return_number" value="{{ $returnNumber }}" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="grn_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Good Receive Note (GRN)</label>
                        <select name="good_receive_note_id" id="grn_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option value="">Select a GRN</option>
                            @foreach($grns as $grn)
                                <option value="{{ $grn->id }}" data-supplier-id="{{ $grn->supplier->id }}" data-supplier-name="{{ $grn->supplier->company_name }}">#{{ $grn->grn_number }} - {{ $grn->supplier->company_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                        <input type="text" id="supplier_name" readonly
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                        <input type="hidden" name="supplier_id" id="supplier_id">
                    </div>
                    <div>
                        <label for="return_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Date</label>
                        <input type="date" name="return_date" id="return_date" value="{{ now()->toDateString() }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                    </div>
                    <div>
                        <label for="return_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Return Reason</label>
                        <select name="return_reason" id="return_reason"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                            <option>Damaged</option>
                            <option>Wrong Item</option>
                            <option>Defective</option>
                            <option>Overstocked</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"></textarea>
                    </div>
                </div>

                <!-- Section 2: Items to Return -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Items to Return</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Available Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Return Qty</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost Price</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Condition</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Notes</th>
                                <th scope="col" class="relative px-6 py-3">
                                    <span class="sr-only">Remove</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="return-items-body" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Items will be added here via JavaScript -->
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="add-item-btn" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add Item
                    </button>
                </div>

                <!-- Section 3: Totals -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <div class="md:col-start-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtotal</label>
                        <p id="subtotal" class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">$0.00</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
                        <p id="total" class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">$0.00</p>
                    </div>
                </div>

                <!-- Section 4: Actions -->
                <div class="flex justify-end">
                    <a href="{{ route('supplier-returns.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancel</a>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Create Return</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle dynamic item adding and calculations will go here
    </script>
@endsection
