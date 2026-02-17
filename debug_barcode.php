<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Batch;
use App\Models\Stock;

$barcode = '4792210100781';
echo "Searching for barcode: [$barcode]\n";

// Check all batches with this barcode
$batches = Batch::where('barcode', $barcode)->get();
echo "Found " . $batches->count() . " batches with this barcode.\n";

foreach ($batches as $batch) {
    echo "Batch ID: {$batch->id}\n";
    $stocks = Stock::where('batch_id', $batch->id)->get();
    echo "  - Stocks count: " . $stocks->count() . "\n";
    foreach ($stocks as $stock) {
        echo "    - Stock ID: {$stock->id}, Qty: {$stock->available_quantity}, Product ID: {$stock->product_id}\n";
    }
}

// Check stocks via query like Controller
echo "\nChecking via Stock query (ignoring qty > 0):\n";
$stocksQuery = Stock::whereHas('batch', function ($q) use ($barcode) {
    $q->where('barcode', $barcode);
})->get();

echo "Query found " . $stocksQuery->count() . " stocks.\n";
foreach ($stocksQuery as $stock) {
    echo "  - Stock ID: {$stock->id}, Qty: {$stock->available_quantity}, Product: {$stock->product->product_name}\n";
}
