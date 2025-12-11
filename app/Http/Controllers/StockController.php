<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added DB for raw sum

class StockController extends Controller
{
    protected StockAdjustmentService $adjustmentService;

    public function __construct(
        StockAdjustmentService $adjustmentService
    ) {
        $this->adjustmentService = $adjustmentService;
    }

    /**
     * Display a listing of stocks.
     */
    public function index(Request $request)
    {
        // Aggregate stocks by product_id and batch_id to combine FOC and non-FOC
        $query = Stock::query()
            ->select('product_id', 'batch_id')
            ->selectRaw('MAX(id) as id')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('SUM(available_quantity) as total_available_quantity')
            ->selectRaw('MAX(CASE WHEN cost_price > 0 THEN cost_price END) as cost_price')
            ->selectRaw('MAX(CASE WHEN cost_price > 0 THEN selling_price END) as selling_price')
            ->selectRaw('SUM(CASE WHEN cost_price = 0 THEN quantity ELSE 0 END) as foc_quantity')
            ->selectRaw('SUM(CASE WHEN cost_price = 0 THEN available_quantity ELSE 0 END) as foc_available_quantity')
            ->selectRaw('SUM(CASE WHEN cost_price > 0 THEN quantity ELSE 0 END) as paid_quantity')
            ->selectRaw('SUM(CASE WHEN cost_price > 0 THEN available_quantity ELSE 0 END) as paid_available_quantity')
            ->groupBy('product_id', 'batch_id');

        // Filter by category
        $selectedCategoryId = null;
        if ($request->filled('category_id')) {
            $selectedCategoryId = $request->category_id;
            $query->whereHas('product', function ($q) use ($selectedCategoryId) {
                $q->where('category_id', $selectedCategoryId);
            });
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by status (adjusted for aggregated view)
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'out_of_stock':
                    $query->havingRaw('SUM(available_quantity) = 0');
                    break;
                case 'low_stock':
                    $query->havingRaw('SUM(available_quantity) <= (SUM(quantity) / 2)')
                        ->havingRaw('SUM(available_quantity) > 0');
                    break;
                case 'in_stock':
                    $query->havingRaw('SUM(available_quantity) > 0');
                    break;
            }
        }

        // Search by product name, SKU, batch number, or barcode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('product_name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                })->orWhereHas('batch', function ($batchQuery) use ($search) {
                    $batchQuery->where('batch_number', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%");
                });
            });
        }

        $stocks = $query->latest('id')->paginate(15);

        // Load relationships for the aggregated results
        $stocks->getCollection()->transform(function ($stock) {
            $fullStock = Stock::with(['product', 'batch.goodReceiveNote.supplier'])->find($stock->id);
            if ($fullStock) {
                $stock->product = $fullStock->product;
                $stock->batch = $fullStock->batch;
            }
            return $stock;
        });

        // Calculate stats (aggregated)
        $totalStocks = Stock::select('product_id', 'batch_id')
            ->groupBy('product_id', 'batch_id')
            ->get()
            ->count();

        $totalValue = Stock::where('cost_price', '>', 0)
            ->sum(DB::raw('cost_price * available_quantity'));

        $outOfStock = Stock::select('product_id', 'batch_id')
            ->groupBy('product_id', 'batch_id')
            ->havingRaw('SUM(available_quantity) = 0')
            ->get()
            ->count();

        $lowStock = Stock::select('product_id', 'batch_id')
            ->groupBy('product_id', 'batch_id')
            ->havingRaw('SUM(available_quantity) <= (SUM(quantity) / 2)')
            ->havingRaw('SUM(available_quantity) > 0')
            ->get()
            ->count();

        $products = Product::orderBy('product_name')->get();
        $categories = Category::orderBy('cat_name')->get();

        // Calculate category-specific stats only if category is selected
        $categoryStats = null;
        $selectedCategory = null;

        if ($selectedCategoryId) {
            $selectedCategory = Category::find($selectedCategoryId);

            // Get all products in this category
            $categoryProducts = Product::where('category_id', $selectedCategoryId)->pluck('id');

            // Calculate stats for selected category
            $categoryTotalProducts = $categoryProducts->count();

            $categoryTotalStocks = Stock::select('product_id', 'batch_id')
                ->whereIn('product_id', $categoryProducts)
                ->groupBy('product_id', 'batch_id')
                ->get()
                ->count();

            $categoryTotalValue = Stock::where('cost_price', '>', 0)
                ->whereIn('product_id', $categoryProducts)
                ->sum(DB::raw('cost_price * available_quantity'));

            $categoryOutOfStock = Stock::select('product_id', 'batch_id')
                ->whereIn('product_id', $categoryProducts)
                ->groupBy('product_id', 'batch_id')
                ->havingRaw('SUM(available_quantity) = 0')
                ->get()
                ->count();

            $categoryLowStock = Stock::select('product_id', 'batch_id')
                ->whereIn('product_id', $categoryProducts)
                ->groupBy('product_id', 'batch_id')
                ->havingRaw('SUM(available_quantity) <= (SUM(quantity) / 2)')
                ->havingRaw('SUM(available_quantity) > 0')
                ->get()
                ->count();

            $categoryInStock = $categoryTotalStocks - $categoryOutOfStock;

            $categoryBrandIds = Product::where('category_id', $selectedCategoryId)
                ->whereNotNull('brand_id')
                ->distinct()
                ->pluck('brand_id');

            $categoryBrands = \App\Models\Brand::whereIn('id', $categoryBrandIds)
                ->orderBy('brand_name')
                ->get()
                ->map(function ($brand) use ($selectedCategoryId) {
                    // Get products for this brand within the selected category
                    $brandProductIds = Product::where('category_id', $selectedCategoryId)
                        ->where('brand_id', $brand->id)
                        ->pluck('id');

                    // Calculate stats
                    $brand->total_products = $brandProductIds->count();

                    $brand->total_stocks = Stock::select('product_id', 'batch_id')
                        ->whereIn('product_id', $brandProductIds)
                        ->groupBy('product_id', 'batch_id')
                        ->get()
                        ->count();

                    $brand->out_of_stock = Stock::select('product_id', 'batch_id')
                        ->whereIn('product_id', $brandProductIds)
                        ->groupBy('product_id', 'batch_id')
                        ->havingRaw('SUM(available_quantity) = 0')
                        ->get()
                        ->count();

                    $brand->low_stock = Stock::select('product_id', 'batch_id')
                        ->whereIn('product_id', $brandProductIds)
                        ->groupBy('product_id', 'batch_id')
                        ->havingRaw('SUM(available_quantity) <= (SUM(quantity) / 2)')
                        ->havingRaw('SUM(available_quantity) > 0')
                        ->get()
                        ->count();

                    $brand->in_stock = $brand->total_stocks - $brand->out_of_stock;

                    return $brand;
                });

            $categoryStats = [
                'total_products' => $categoryTotalProducts,
                'total_stocks' => $categoryTotalStocks,
                'total_value' => $categoryTotalValue,
                'out_of_stock' => $categoryOutOfStock,
                'low_stock' => $categoryLowStock,
                'in_stock' => $categoryInStock,
                'brands' => $categoryBrands,
            ];
        }

        return view('stocks.index', compact(
            'stocks',
            'products',
            'categories',
            'totalStocks',
            'totalValue',
            'outOfStock',
            'lowStock',
            'categoryStats',
            'selectedCategory'
        ));
    }

    /**
     * Display the specified stock.
     */
    public function show(Stock $stock)
    {
        $stock->load(['product.category', 'product.brand', 'batch.goodReceiveNote.supplier']);

        $totalValue = $stock->cost_price * $stock->available_quantity;
        $potentialRevenue = $stock->selling_price * $stock->available_quantity;
        $profitMargin = $stock->selling_price - $stock->cost_price;
        $profitPercentage = $stock->cost_price > 0 ? (($profitMargin / $stock->cost_price) * 100) : 0;

        $currentPage = request()->query('page', 1);

        return view('stocks.show', compact('stock', 'totalValue', 'potentialRevenue', 'profitMargin', 'profitPercentage', 'currentPage'));
    }

    /**
     * Update the barcode for the stock's batch.
     */
    public function updateBarcode(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'barcode' => 'nullable|string|max:255|unique:batches,barcode,' . $stock->batch_id,
        ]);

        $currentPage = $request->input('current_page', 1);

        $stock->batch->update([
            'barcode' => $validated['barcode'],
        ]);

        return redirect()->route('stocks.show', ['stock' => $stock->id, 'page' => $currentPage])
            ->with('success', 'Barcode updated successfully.');
    }

    /**
     * Update stock details (cost price, selling price, barcode, and optionally quantity adjustment).
     */
    public function update(Request $request, Stock $stock)
    {
        // Uncomment if needed:
        // if ($stock->isFoc()) {
        //     return redirect()->back()
        //         ->with('error', 'FOC (Free of Charge) stocks cannot be edited. Only non-FOC stocks can be modified.');
        // }

        // *** 1. VALIDATION ***
        $validated = $request->validate([
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:255|unique:batches,barcode,' . $stock->batch_id,

            // Validation for adjustment fields
            'quantity_adjustment' => 'nullable|numeric|sometimes',
            'adjustment_reason' => 'required_with:quantity_adjustment|nullable|string|max:255',
            'adjustment_notes' => 'nullable|string|max:1000',
        ]);

        // Setup state variables
        $adjustmentQuantity = (float)($validated['quantity_adjustment'] ?? 0);
        $currentPage = $request->input('current_page', 1);
        $hasQuantityAdjustment = $adjustmentQuantity !== 0.0;
        $successMessage = 'Stock updated successfully.';

        // If quantity_adjustment is present, enforce required reason
        if ($hasQuantityAdjustment && empty($validated['adjustment_reason'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Adjustment reason is required when adjusting quantity.');
        }

        // *** 2. PRICE/BARCODE UPDATE & AUDIT ***
        $oldCostPrice = $stock->cost_price;
        $oldSellingPrice = $stock->selling_price;
        $oldBarcode = $stock->batch->barcode;
        $changes = [];
        $priceOrBarcodeUpdated = false;

        // Update stock-level fields
        $stock->update([
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
        ]);

        // Update batch-level barcode if changed
        if ($validated['barcode'] !== $oldBarcode) {
            $stock->batch->update(['barcode' => $validated['barcode']]);
        }

        // Build audit trail for Price/Barcode changes
        if ($oldCostPrice != $validated['cost_price']) {
            $changes[] = "Cost Price: LKR {$oldCostPrice} → LKR {$validated['cost_price']}";
            $priceOrBarcodeUpdated = true;
        }
        if ($oldSellingPrice != $validated['selling_price']) {
            $changes[] = "Selling Price: LKR {$oldSellingPrice} → LKR {$validated['selling_price']}";
            $priceOrBarcodeUpdated = true;
        }
        if ($validated['barcode'] !== $oldBarcode) {
            $changes[] = "Barcode: {$oldBarcode} → {$validated['barcode']}";
            $priceOrBarcodeUpdated = true;
        }

        // *** 3. QUANTITY ADJUSTMENT LOGIC ***
        if ($hasQuantityAdjustment) {
            try {
                $type = $adjustmentQuantity > 0 ? 'increase' : 'decrease';

                // Use float to preserve decimal precision, but format for display if needed
                $absoluteQuantity = abs($adjustmentQuantity);

                $adjustment = $this->adjustmentService->createAdjustment([
                    'stock_id' => $stock->id,
                    'quantity_adjusted' => $absoluteQuantity,
                    'type' => $type,
                    'reason' => $validated['adjustment_reason'],
                    'notes' => $validated['adjustment_notes'] ?? null,
                ]);

                // Auto-approve the adjustment since this is a direct edit
                $this->adjustmentService->approveAdjustment($adjustment);

                // Append adjustment to changes log
                $sign = $adjustmentQuantity > 0 ? '+' : '';
                $changes[] = "Quantity Adjustment: {$sign}{$adjustmentQuantity} (Reason: {$validated['adjustment_reason']})";
                if (!empty($validated['adjustment_notes'])) {
                    $changes[] = "Adjustment Notes: {$validated['adjustment_notes']}";
                }

                // Set the custom success message
                if ($priceOrBarcodeUpdated) {
                    $successMessage = 'Price/Barcode updated and Quantity adjusted successfully.';
                } else {
                    $successMessage = 'Quantity adjusted successfully.';
                }
            } catch (\Exception $e) {
                // If the adjustment fails, notify the user and return immediately
                // We should still log the price changes if any occurred
                if (! empty($changes)) {
                    // Log what succeeded (Price/Barcode)
                    $userName = auth()->user()->name;
                    $timestamp = now()->format('Y-m-d H:i:s');
                    $auditNote = "\n[{$timestamp}] {$userName}: " . implode(', ', $changes);
                    $stock->batch->update(['notes' => ($stock->batch->notes ?? '') . $auditNote]);
                }

                $warningMessage = ($priceOrBarcodeUpdated ? 'Price/Barcode updated, but ' : '') . 'Quantity adjustment failed: ' . $e->getMessage();
                return redirect()->route('stocks.index', ['page' => $currentPage])->with('warning', $warningMessage);
            }
        }

        // Apply audit trail to Batch/GRN notes
        if (! empty($changes)) {
            $userName = auth()->user()->name;
            $timestamp = now()->format('Y-m-d H:i:s');
            $auditNote = "\n[{$timestamp}] {$userName}: " . implode(', ', $changes);

            // Append note to Batch
            $stock->batch->update([
                'notes' => ($stock->batch->notes ?? '') . $auditNote,
            ]);

            // Append note to GRN
            $grn = $stock->batch->goodReceiveNote;
            if ($grn) {
                $grn->update([
                    'notes' => ($grn->notes ?? '') . $auditNote . " (Stock ID: {$stock->id}, Product: {$stock->product->product_name})",
                ]);
            }
        }

        // *** 4. FINAL REDIRECT ***
        return redirect()->route('stocks.index', ['page' => $currentPage])
            ->with('success', $successMessage);
    }
}
