<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display a listing of stocks.
     */
    public function index(Request $request)
    {
        $query = Stock::with(['product', 'batch.goodReceiveNote.supplier']);

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'out_of_stock':
                    $query->where('available_quantity', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('available_quantity', '<=', \DB::raw('quantity / 2'))
                        ->where('available_quantity', '>', 0);
                    break;
                case 'in_stock':
                    $query->where('available_quantity', '>', 0);
                    break;
            }
        }

        // Search by product name, SKU, batch number, or barcode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })->orWhereHas('batch', function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $stocks = $query->latest()->paginate(15);

        // Calculate stats
        $totalStocks = Stock::count();
        $totalValue = Stock::sum(\DB::raw('cost_price * available_quantity'));
        $outOfStock = Stock::where('available_quantity', 0)->count();
        $lowStock = Stock::whereColumn('available_quantity', '<=', \DB::raw('quantity / 2'))
            ->where('available_quantity', '>', 0)
            ->count();

        $products = Product::orderBy('product_name')->get();

        return view('stocks.index', compact('stocks', 'products', 'totalStocks', 'totalValue', 'outOfStock', 'lowStock'));
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

        return view('stocks.show', compact('stock', 'totalValue', 'potentialRevenue', 'profitMargin', 'profitPercentage'));
    }

    /**
     * Update the barcode for the stock's batch.
     */
    public function updateBarcode(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'barcode' => 'nullable|string|max:255|unique:batches,barcode,'.$stock->batch_id,
        ]);

        $stock->batch->update([
            'barcode' => $validated['barcode'],
        ]);

        return redirect()->route('stocks.show', $stock)
            ->with('success', 'Barcode updated successfully.');
    }

    /**
     * Update stock details (cost price, selling price, barcode).
     */
    public function update(Request $request, Stock $stock)
    {
        $validated = $request->validate([
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:255|unique:batches,barcode,'.$stock->batch_id,
        ]);

        // Track old values for audit
        $oldCostPrice = $stock->cost_price;
        $oldSellingPrice = $stock->selling_price;
        $oldBarcode = $stock->batch->barcode;

        // Update stock-level fields
        $stock->update([
            'cost_price' => $validated['cost_price'],
            'selling_price' => $validated['selling_price'],
        ]);

        // Update batch-level barcode if changed
        if ($validated['barcode'] !== $oldBarcode) {
            $stock->batch->update(['barcode' => $validated['barcode']]);
        }

        // Add audit trail to Batch notes
        $userName = auth()->user()->name;
        $timestamp = now()->format('Y-m-d H:i:s');
        $changes = [];

        if ($oldCostPrice != $validated['cost_price']) {
            $changes[] = "Cost Price: LKR {$oldCostPrice} → LKR {$validated['cost_price']}";
        }
        if ($oldSellingPrice != $validated['selling_price']) {
            $changes[] = "Selling Price: LKR {$oldSellingPrice} → LKR {$validated['selling_price']}";
        }
        if ($validated['barcode'] !== $oldBarcode) {
            $changes[] = "Barcode: {$oldBarcode} → {$validated['barcode']}";
        }

        if (!empty($changes)) {
            $auditNote = "\n[{$timestamp}] {$userName}: ".implode(', ', $changes);
            $stock->batch->update([
                'notes' => $stock->batch->notes.$auditNote,
            ]);

            // Also add to GRN notes
            $grn = $stock->batch->goodReceiveNote;
            $grn->update([
                'notes' => $grn->notes.$auditNote." (Stock ID: {$stock->id}, Product: {$stock->product->product_name})",
            ]);
        }

        return redirect()->route('stocks.index')
            ->with('success', 'Stock updated successfully.');
    }
}
