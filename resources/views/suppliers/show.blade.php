@extends('layouts.app')

@section('title', 'Supplier Details')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('suppliers.index') }}"
                    class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $supplier->company_name }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('suppliers.edit', $supplier) }}"
                    class="text-[#2f85c3] dark:text-white border border-[#2f85c3] dark:border-white rounded px-4 py-2 transition-colors duration-200 hover:bg-[#2f85c3] hover:text-white dark:hover:bg-white dark:hover:text-[#2f85c3]">
                    <i class="fas fa-edit mr-2"></i>Edit Supplier
                </a>
            </div>
        </div>

        <!-- Supplier Information Card -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Supplier Information</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Company Name</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Business Type</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->business_type ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Tax ID</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->tax_id ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Contact Person</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->contact_person }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->phone }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Mobile</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->mobile ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Payment Terms</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $supplier->payment_terms ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Credit Limit</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($supplier->credit_limit)
                                LKR {{ number_format($supplier->credit_limit, 2) }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Vendor Mappings -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Product Vendor Mappings
                    <span class="ml-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                        ({{ $supplier->products->count() }} products)
                    </span>
                </h2>
            </div>
            <div class="overflow-x-auto">
                @if($supplier->products->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Product Details
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Internal SKU
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Vendor Code
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Lead Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                            @foreach($supplier->products as $product)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $product->product_name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $product->category?->cat_name }} | {{ $product->brand?->brand_name }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-mono">
                                            {{ $product->sku }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $product->pivot->vendor_product_code }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($product->pivot->lead_time_days)
                                            {{ $product->pivot->lead_time_days }} days
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($product->pivot->is_preferred)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                <i class="fas fa-star mr-1"></i>Preferred
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                Standard
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="px-6 py-12 text-center">
                        <i class="fas fa-box-open text-4xl text-gray-400 dark:text-gray-600 mb-4"></i>
                        <p class="text-gray-500 dark:text-gray-400">No products mapped to this supplier yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
