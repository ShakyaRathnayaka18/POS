#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\DashboardService;

$service = new DashboardService();
$result = $service->getProfitMargin();

echo "\n";
echo "========================================\n";
echo "  CURRENT PROFIT MARGIN VALUES\n";
echo "========================================\n";
echo "\n";
echo "Profit Margin: " . $result['percentage'] . "%\n";
echo "Gross Profit Amount: LKR " . number_format($result['amount'], 2) . "\n";
echo "\n";

if ($result['percentage'] < 0) {
    echo "⚠️  WARNING: Negative profit margin detected!\n";
    echo "This means your COGS is higher than your revenue.\n";
    echo "\n";
    echo "Possible reasons:\n";
    echo "1. Products are being sold below cost price\n";
    echo "2. Stock cost prices are set too high\n";
    echo "3. Missing revenue journal entries\n";
    echo "\n";
    echo "Run 'php diagnose_profit_margin.php' for detailed analysis.\n";
} elseif ($result['percentage'] == 0 && $result['amount'] == 0) {
    echo "ℹ️  No revenue data found.\n";
} else {
    echo "✓ Profit margin is positive - business is profitable!\n";
}

echo "\n";
