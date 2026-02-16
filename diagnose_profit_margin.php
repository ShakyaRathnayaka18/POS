#!/usr/bin/env php
<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Profit Margin Diagnostic Report ===" . PHP_EOL . PHP_EOL;

// Get revenue
$revenue = DB::table('journal_entry_lines')
    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
    ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
    ->where('journal_entries.status', 'posted')
    ->where('accounts.account_code', '4100')
    ->sum('journal_entry_lines.credit_amount');

// Get COGS debits
$cogsDebits = DB::table('journal_entry_lines')
    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
    ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
    ->where('journal_entries.status', 'posted')
    ->where('accounts.account_code', '5100')
    ->sum('journal_entry_lines.debit_amount');

// Get COGS credits
$cogsCredits = DB::table('journal_entry_lines')
    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
    ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
    ->where('journal_entries.status', 'posted')
    ->where('accounts.account_code', '5100')
    ->sum('journal_entry_lines.credit_amount');

$netCogs = $cogsDebits - $cogsCredits;
$grossProfit = $revenue - $netCogs;
$profitMargin = $revenue > 0 ? (($grossProfit / $revenue) * 100) : 0;

echo "Revenue (Account 4100 - Credits): LKR " . number_format($revenue, 2) . PHP_EOL;
echo "COGS Debits (Account 5100 - Debits): LKR " . number_format($cogsDebits, 2) . PHP_EOL;
echo "COGS Credits (Account 5100 - Credits): LKR " . number_format($cogsCredits, 2) . PHP_EOL;
echo "Net COGS: LKR " . number_format($netCogs, 2) . PHP_EOL;
echo "Gross Profit: LKR " . number_format($grossProfit, 2) . PHP_EOL;
echo "Profit Margin: " . number_format($profitMargin, 2) . "%" . PHP_EOL . PHP_EOL;

// Get sample journal entries for account 5100
echo "=== Sample COGS Journal Entries (Account 5100) ===" . PHP_EOL;
$sampleEntries = DB::table('journal_entry_lines')
    ->join('journal_entries', 'journal_entry_lines.journal_entry_id', '=', 'journal_entries.id')
    ->join('accounts', 'journal_entry_lines.account_id', '=', 'accounts.id')
    ->where('journal_entries.status', 'posted')
    ->where('accounts.account_code', '5100')
    ->select(
        'journal_entries.entry_date',
        'journal_entries.description',
        'journal_entry_lines.debit_amount',
        'journal_entry_lines.credit_amount',
        'journal_entry_lines.description as line_description'
    )
    ->orderBy('journal_entries.entry_date', 'desc')
    ->limit(10)
    ->get();

foreach ($sampleEntries as $entry) {
    echo "Date: {$entry->entry_date} | ";
    echo "Debit: " . number_format($entry->debit_amount, 2) . " | ";
    echo "Credit: " . number_format($entry->credit_amount, 2) . " | ";
    echo "Desc: {$entry->description} - {$entry->line_description}" . PHP_EOL;
}

echo PHP_EOL . "=== Analysis ===" . PHP_EOL;
if ($netCogs > $revenue) {
    echo "⚠️  WARNING: COGS is higher than Revenue!" . PHP_EOL;
    echo "This indicates products are being sold at a loss or there's a data issue." . PHP_EOL;
    echo "Possible causes:" . PHP_EOL;
    echo "1. Cost prices in stock records are higher than selling prices" . PHP_EOL;
    echo "2. Incorrect journal entries" . PHP_EOL;
    echo "3. Missing revenue entries" . PHP_EOL;
} else {
    echo "✓ COGS is lower than Revenue - this is normal." . PHP_EOL;
}
