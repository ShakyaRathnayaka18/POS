<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Http\Controllers\SaleController;

$controller = app(SaleController::class);

echo "Testing Search API with include_out_of_stock...\n";

// Mock Request
$request = Request::create('/api/products/search', 'GET', [
    'q' => '4792210100781',
    'include_out_of_stock' => '1'
]);

$response = $controller->searchProducts($request);
$data = $response->getData(true);

echo "Found " . count($data) . " results.\n";
foreach ($data as $item) {
    echo " - Product: {$item['product_name']}\n";
    echo " - Stock: {$item['available_quantity']}\n";
    echo " - In Stock: " . ($item['in_stock'] ? 'Yes' : 'No') . "\n";
}
