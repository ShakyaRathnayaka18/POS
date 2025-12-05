<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
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
            ];

            $sale = $this->saleService->processSale($saleData, $request->items, $request->credit_terms);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale completed successfully.',
                    'sale' => [
                        'id' => $sale->id,
                        'sale_number' => $sale->sale_number,
                        'total' => $sale->total,
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

        $products = Product::with(['category', 'brand', 'availableStocks.batch'])
            ->where(function ($query) use ($search) {
                $query->where('product_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhereHas('availableStocks.batch', function ($q) use ($search) {
                        $q->where('barcode', 'like', "%{$search}%");
                    });
            })
            ->whereHas('availableStocks', function ($q) {
                $q->where('available_quantity', '>', 0);
            })
            ->limit(20)
            ->get();

        $results = $products->map(function ($product) {
            $availability = $this->saleService->getProductAvailability($product->id);

            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'barcode' => $product->availableStocks->first()?->batch?->barcode,
                'category' => $product->category?->cat_name,
                'brand' => $product->brand?->brand_name,
                'selling_price' => $availability['selling_price'],
                'tax' => $availability['tax'],
                'available_quantity' => $availability['available_quantity'],
                'in_stock' => $availability['in_stock'],
                'unit' => $product->unit,
                'base_unit' => $product->base_unit,
                'allow_decimal_sales' => $product->allow_decimal_sales,
            ];
        });

        return response()->json($results);
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
