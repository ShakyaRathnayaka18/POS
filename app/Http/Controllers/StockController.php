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

        // Search by product name, SKU, or batch number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            })->orWhereHas('batch', function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%");
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
}
