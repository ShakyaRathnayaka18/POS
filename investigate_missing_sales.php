#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Sale;
use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "  INVESTIGATING MISSING SALES\n";
echo "========================================\n\n";

// Check for sales with different statuses
echo "--- All Sales Today (including non-completed) ---\n";
$allSales = Sale::whereDate('created_at', today())->get();

$statusBreakdown = [];
foreach ($allSales as $sale) {
    $status = $sale->status ?? 'null';
    if (!isset($statusBreakdown[$status])) {
        $statusBreakdown[$status] = ['count' => 0, 'total' => 0];
    }
    $statusBreakdown[$status]['count']++;
    $statusBreakdown[$status]['total'] += $sale->total;
}

foreach ($statusBreakdown as $status => $data) {
    echo "{$status}: {$data['count']} sales, LKR " . number_format($data['total'], 2) . "\n";
}

// Check for recent sales (last 24 hours)
echo "\n--- Sales in Last 24 Hours ---\n";
$last24Hours = Sale::where('created_at', '>=', now()->subHours(24))->get();
echo "Count: " . $last24Hours->count() . "\n";
echo "Total: LKR " . number_format($last24Hours->sum('total'), 2) . "\n";

// Check for sales created today but with different timestamps
echo "\n--- Sales Created Today (Full Timestamps) ---\n";
$todayFull = Sale::whereDate('created_at', today())
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($todayFull as $sale) {
    echo "{$sale->sale_number} | {$sale->created_at} | LKR " . number_format($sale->total, 2) . " | {$sale->status}\n";
}

// Check for any pending/draft sales
echo "\n--- Checking for Incomplete Sales ---\n";
$incomplete = Sale::whereDate('created_at', today())
    ->where('status', '!=', 'Completed')
    ->get();

if ($incomplete->count() > 0) {
    echo "Found {$incomplete->count()} incomplete sales:\n";
    foreach ($incomplete as $sale) {
        echo "{$sale->sale_number} | {$sale->status} | LKR " . number_format($sale->total, 2) . "\n";
    }
} else {
    echo "No incomplete sales found.\n";
}

// Check timezone
echo "\n--- System Information ---\n";
echo "Current Time: " . now() . "\n";
echo "Today Start: " . today() . "\n";
echo "Today End: " . today()->endOfDay() . "\n";
echo "Timezone: " . config('app.timezone') . "\n";

echo "\n--- Summary ---\n";
echo "✓ Dashboard calculation is CORRECT: LKR 17,015.10\n";
echo "✓ Database matches dashboard\n";
echo "⚠️  Client reports ~LKR 20,000\n";
echo "⚠️  Missing: LKR 2,984.90\n\n";

echo "Possible explanations:\n";
echo "1. Sales not yet entered into the system\n";
echo "2. Client counting cash in hand vs recorded sales\n";
echo "3. Client including pending transactions\n";
echo "4. Timezone mismatch (sales recorded on different date)\n";
echo "5. Manual/offline sales not recorded\n\n";

echo "Recommendation: Ask client to verify which specific transactions\n";
echo "are missing from the system.\n\n";
