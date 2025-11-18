@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center mb-6">
        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">POS Dashboard</h1>
            <p class="text-gray-500 text-sm">Monitor your sales and inventory performance</p>
        </div>
    </div>

    <!-- Today's Sales Card -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-1">Today's Sales</p>
                <h2 class="text-4xl font-bold text-white">Rs. {{ number_format($todaySales, 2) }}</h2>
            </div>
            <div class="bg-white bg-opacity-20 rounded-xl p-4">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Top 10 Best Selling Products -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Top 10 Best Selling Products</h2>
        </div>

        <div class="bg-gray-50 rounded-xl p-6">
            <canvas id="topSellingChart" style="max-height: 400px;"></canvas>
        </div>
    </div>

    <!-- Cost vs Selling Price -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center mb-6">
            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-bold text-gray-800">Cost vs Selling Price</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="text-left py-4 px-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Product Name</th>
                        <th class="text-right py-4 px-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Cost Price</th>
                        <th class="text-right py-4 px-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Selling Price</th>
                        <th class="text-right py-4 px-4 text-sm font-semibold text-gray-600 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($costVsPrice as $stock)
                    @php
                    $margin = $stock->selling_price - $stock->cost_price;
                    $marginPercent = $stock->cost_price > 0 ? (($margin / $stock->cost_price) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-4 px-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">{{ substr($stock->product->product_name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $stock->product->product_name }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $stock->product->sku ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                Rs. {{ number_format($stock->cost_price, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                Rs. {{ number_format($stock->selling_price, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="flex items-center justify-end">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $marginPercent > 30 ? 'bg-green-100 text-green-800' : ($marginPercent > 15 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ number_format($marginPercent, 1) }}%
                                </span>
                                @if($marginPercent > 30)
                                <svg class="w-5 h-5 text-green-600 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for the chart
        const topSellingData = @json($topSelling);

        const labels = topSellingData.map(item => item.product.product_name);
        const data = topSellingData.map(item => item.total_sold);

        // Create gradient
        const ctx = document.getElementById('topSellingChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(34, 197, 94, 0.8)');
        gradient.addColorStop(1, 'rgba(34, 197, 94, 0.2)');

        // Create the chart
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: data,
                    backgroundColor: gradient,
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: 'rgba(34, 197, 94, 0.5)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Sold: ' + context.parsed.y + ' units';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#6B7280'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            color: '#6B7280',
                            maxRotation: 45,
                            minRotation: 45
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection