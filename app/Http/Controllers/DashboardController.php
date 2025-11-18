<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Today's Sales
        $todaySales = Sale::whereDate('created_at', today())
            ->sum('total');

        // 2. Top Selling Items (Top 10)
        $topSelling = SaleItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_sold')
        )
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(10)
            ->with('product')
            ->get();

        // 3. Stock Levels (Each product and available quantity)
        // $stockLevels = Stock::with('product')
        //     ->select('product_id', 'available_quantity')
        //     ->get();

        // 4. Low Stock Items (Compare stocks.available_quantity < products.minimum_stock)
        // Note: Originally you compared to 'initial_stock', fixed to 'minimum_stock' (more realistic)
        // $lowStock = Product::with('stocks')
        //     ->whereHas('stocks', function ($q) {
        //         $q->whereColumn('available_quantity', '<', 'minimum_stock');
        //     })
        //     ->get();

        // 5. Inventory Value = available_quantity × cost_price
        // $inventoryValue = Stock::with('product')
        //     ->select(
        //         'product_id',
        //         DB::raw('(available_quantity * cost_price) as value')
        //     )
        //     ->get();

        // 6. Profit Potential = available_quantity × (selling_price - cost_price)
        // $profitPotential = Stock::with('product')
        //     ->select(
        //         'product_id',
        //         DB::raw('(available_quantity * (selling_price - cost_price)) as profit')
        //     )
        //     ->get();

        // 7. Category Stock Distribution
        // Each product may have multiple stock batches, so sum all:
        // $categoryStock = Category::with('products.stocks')
        //     ->get()
        //     ->map(function ($category) {
        //         return [
        //             'category_name' => $category->cat_name,
        //             'total_stock'   => $category->products->sum(
        //                 fn($p) => $p->stocks->sum('available_quantity')
        //             ),
        //         ];
        //     });

        // 8. Cost vs Selling Price Chart
        $costVsPrice = Stock::with('product')
            ->select('product_id', 'cost_price', 'selling_price')
            ->get();

        return view('startmenu.menue', compact(
            'todaySales',
            'topSelling',
            // 'stockLevels',
            // 'lowStock',
            // 'inventoryValue',
            // 'profitPotential',
            // 'categoryStock',
            'costVsPrice'
        ));
    }
}
