<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use App\Services\WeightedBarcodeService;
use Exception;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct(protected SaleService $saleService) {}

    /**
     * Display sales history
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'items'])
            ->orderBy('created_at', 'desc');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Payment method filter
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Cashier filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by sale number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $sales = $query->paginate(15)->withQueryString();

        // Get all users for cashier filter
        $users = \App\Models\User::orderBy('name')->get();

        return view('sales.index', compact('sales', 'users'));
    }

    /**
     * Process a new sale
     */
    public function store(StoreSaleRequest $request)
    {
        try {
            $saleData = [
                'sale_number' => $this->saleService->generateSaleNumber(),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
                'sale_discount' => $request->sale_discount ?? null,
            ];

            if (strtolower($request->payment_method) === 'cash') {
                $saleData['amount_received'] = $request->input('amountReceived');
                $saleData['change_amount'] = $request->input('changeAmount');
            }

            $sale = $this->saleService->processSale($saleData, $request->items, $request->credit_terms);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully.',
                    'sale' => [
                        'id' => $sale->id,
                        'sale_number' => $sale->sale_number,
                        'total' => $sale->total,

                        'amount_received' => $sale->amount_received,
                        'change_amount' => $sale->change_amount,
                    ],
                ]);
            }

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Sale completed successfully.');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display sale details / receipt
     */
    public function show(Sale $sale)
    {
        $sale->load(['items.product', 'items.stock.batch', 'user']);

        return view('sales.receipt', compact('sale'));
    }

    /**
     * API: Search products
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        // Check if this is a weighted barcode (11 digits)
        $weightedBarcodeService = app(WeightedBarcodeService::class);

        if ($weightedBarcodeService->isWeightedBarcode($search)) {
            return $this->handleWeightedBarcodeSearch($search, $weightedBarcodeService);
        }

        // Regular product search
        $products = Product::with(['category', 'brand', 'availableStocks.batch'])
            ->where(function ($query) use ($search) {
                $query->whereHas('product', function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('batch', function ($q) use ($search) {
                    $q->where('barcode', 'like', "%{$search}%");
                });
            })
            ->limit(30)
            ->get();

        $results = $stocks->map(function ($stock) {
            $product = $stock->product;
            
            return [
                'id' => $product->id,
                'stock_id' => $stock->id,
                'batch_number' => $stock->batch->batch_number,
                'product_name' => $product->product_name . " (" . $stock->batch->batch_number . ")",
                'sku' => $product->sku,
                'barcode' => $stock->batch->barcode,
                'category' => $product->category?->cat_name,
                'brand' => $product->brand?->brand_name,
                'selling_price' => $stock->selling_price,
                'tax' => $stock->tax,
                'available_quantity' => $stock->available_quantity,
                'in_stock' => $stock->available_quantity > 0,
                'unit' => $product->unit,
                'base_unit' => $product->base_unit,
                'allow_decimal_sales' => $product->allow_decimal_sales,
                'is_weighted' => $product->is_weighted,
            ];
        });

        return response()->json($results);
    }

    /**
     * Handle weighted barcode search (11 digits: 6 product code + 5 weight in grams)
     */
    protected function handleWeightedBarcodeSearch(string $barcode, WeightedBarcodeService $service)
    {
        $parsed = $service->parseWeightedBarcode($barcode);

        if (! $parsed) {
            return response()->json([
                'error' => 'Invalid weighted barcode format',
            ], 422);
        }

        $product = $service->findProductByCode($parsed['product_code']);

        if (! $product) {
            return response()->json([
                'error' => "Weighted product not found with code: {$parsed['product_code']}",
            ], 404);
        }

        $availability = $this->saleService->getProductAvailability($product->id);
        $weightKg = $service->gramsToKg($parsed['weight_grams']);

        // Check if sufficient stock available
        if ($availability['available_quantity'] < $parsed['weight_grams']) {
            return response()->json([
                'error' => "Insufficient stock for {$product->product_name}",
                'requested_kg' => $weightKg,
                'available_kg' => round($availability['available_quantity'] / 1000, 3),
            ], 422);
        }

        // Return product with embedded weight information
        return response()->json([[
            'id' => $product->id,
            'product_name' => $product->product_name.' - '.$weightKg.'kg',
            'sku' => $product->sku,
            'barcode' => $barcode,
            'category' => $product->category?->cat_name,
            'brand' => $product->brand?->brand_name,
            'selling_price' => $availability['selling_price'],
            'tax' => $availability['tax'],
            'available_quantity' => $availability['available_quantity'],
            'in_stock' => true,
            'unit' => 'kg',
            'base_unit' => 'g',
            'allow_decimal_sales' => true,
            'is_weighted' => true,
            'weight_kg' => $weightKg,
            'quantity' => $parsed['weight_grams'],
            'original_product_name' => $product->product_name,
        ]]);
    }

    /**
     * API: Get product stock availability
     */
    public function getProductStock(int $productId)
    {
        try {
            $product = Product::with(['availableStocks.batch'])->findOrFail($productId);
            $availability = $this->saleService->getProductAvailability($productId);

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'unit' => $product->unit,
                ],
                'availability' => $availability,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
