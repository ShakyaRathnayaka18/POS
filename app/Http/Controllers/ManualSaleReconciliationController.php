<?php

namespace App\Http\Controllers;

use App\Models\ManualSale;
use App\Services\ManualSaleReconciliationService;
use Exception;
use Illuminate\Http\Request;

class ManualSaleReconciliationController extends Controller
{
    public function __construct(
        protected ManualSaleReconciliationService $reconciliationService
    ) {}

    /**
     * Display reconciliation dashboard (list of pending manual sales)
     */
    public function index()
    {
        $pendingManualSales = $this->reconciliationService->getPendingManualSales();

        return view('manual-sales.reconciliation.index', compact('pendingManualSales'));
    }

    /**
     * Display reconciliation interface for a specific manual sale
     */
    public function show(ManualSale $manualSale)
    {
        if (! $manualSale->isPending()) {
            return redirect()->route('manual-sales.reconciliation.index')
                ->with('error', 'This manual sale has already been reconciled or cancelled.');
        }

        $manualSale->load(['items', 'user']);
        $progress = $this->reconciliationService->getReconciliationProgress($manualSale);

        return view('manual-sales.reconciliation.show', compact('manualSale', 'progress'));
    }

    /**
     * API: Search product by barcode
     */
    public function searchProductByBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string',
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $product = $this->reconciliationService->findProductByBarcode($request->barcode);

        if (! $product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found with this barcode.',
            ], 404);
        }

        // Check stock availability
        $hasStock = $this->reconciliationService->validateStockAvailability(
            $product->id,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'category' => $product->category?->category_name,
                'brand' => $product->brand?->brand_name,
                'has_stock' => $hasStock,
            ],
        ]);
    }

    /**
     * Complete reconciliation - convert manual sale to regular sale
     */
    public function reconcile(ManualSale $manualSale, Request $request)
    {
        if (! $manualSale->isPending()) {
            return back()->with('error', 'This manual sale has already been reconciled or cancelled.');
        }

        $request->validate([
            'matched_products' => 'required|array',
            'matched_products.*.product_id' => 'required|exists:products,id',
            'matched_products.*.stock_id' => 'nullable|exists:stocks,id',
        ]);

        try {
            // Restructure matched products array to use item IDs as keys
            $matchedProducts = [];
            foreach ($request->matched_products as $match) {
                $matchedProducts[$match['item_id']] = [
                    'product_id' => $match['product_id'],
                    'stock_id' => $match['stock_id'] ?? null,
                ];
            }

            $sale = $this->reconciliationService->reconcileManualSale($manualSale, $matchedProducts);

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Manual sale reconciled successfully. Regular sale created: '.$sale->sale_number);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
