#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "  TODAY'S SALES ANALYSIS\n";
echo "========================================\n\n";

$today = now()->format('Y-m-d');
echo "Date: {$today}\n\n";

// Get today's sales
$todaysSales = Sale::whereDate('created_at', today())->get();
$totalFromModel = $todaysSales->sum('total');
$count = $todaysSales->count();

echo "--- Sales Summary ---\n";
echo "Total Sales Count: {$count}\n";
echo "Total Amount: LKR " . number_format($totalFromModel, 2) . "\n\n";

// Show individual sales
echo "--- Individual Sales ---\n";
if ($count > 0) {
    $grandTotal = 0;
    foreach ($todaysSales as $sale) {
        $paymentLabel = is_object($sale->payment_method) ? $sale->payment_method->label() : $sale->payment_method;
        echo sprintf(
            "%-15s | %8s | %12s | %-10s | %s\n",
            $sale->sale_number,
            $sale->created_at->format('H:i:s'),
            'LKR ' . number_format($sale->total, 2),
            $paymentLabel,
            $sale->status ?? 'N/A'
        );
        $grandTotal += $sale->total;
    }
    echo "\nGrand Total: LKR " . number_format($grandTotal, 2) . "\n";
} else {
    echo "No sales found for today.\n";
}

echo "\n--- Comparison ---\n";
echo "Dashboard Shows:  LKR 17,015.10\n";
echo "Database Total:   LKR " . number_format($totalFromModel, 2) . "\n";
echo "Client Reports:   LKR ~20,000.00\n";
echo "Missing Amount:   LKR " . number_format(20000 - $totalFromModel, 2) . "\n\n";

// Check if there are sales from yesterday that might be counted
$yesterday = now()->subDay();
$yesterdaySales = Sale::whereDate('created_at', $yesterday)->get();
echo "--- Yesterday's Sales (for reference) ---\n";
echo "Count: " . $yesterdaySales->count() . "\n";
echo "Total: LKR " . number_format($yesterdaySales->sum('total'), 2) . "\n\n";

// Check for sales around midnight
$aroundMidnight = Sale::whereBetween('created_at', [
    now()->startOfDay()->subHours(2),
    now()->startOfDay()->addHours(2)
])->get();

if ($aroundMidnight->count() > 0) {
    echo "--- Sales Around Midnight ---\n";
    foreach ($aroundMidnight as $sale) {
        echo "{$sale->sale_number} at {$sale->created_at} - LKR " . number_format($sale->total, 2) . "\n";
    }
    echo "\n";
}

echo "--- Possible Reasons for Discrepancy ---\n";
if ($totalFromModel < 20000) {
    echo "1. Some sales may not be recorded in the system yet\n";
    echo "2. Sales might be recorded with wrong date/time\n";
    echo "3. Client might be including pending/draft sales\n";
    echo "4. Client might be counting before-tax amounts differently\n";
}

echo "\n";
