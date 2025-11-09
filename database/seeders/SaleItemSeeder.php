<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SaleItem;
use App\Models\Sale;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class SaleItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sales = Sale::whereDoesntHave('items')->get();
        
        foreach ($sales as $sale) {
            $saleSubtotal = 0;
            $saleTax = 0;

            // Each sale will have 1 to 3 items
            $numberOfItems = rand(1, 3);

            for ($i = 0; $i < $numberOfItems; $i++) {
                // Find a random stock item that has quantity
                $stock = Stock::where('available_quantity', '>', 0)->inRandomOrder()->first();

                if (!$stock) {
                    // No more stock available to sell
                    break;
                }

                // Sell a quantity between 1 and the available quantity (or a max of 2 to not deplete stock too fast)
                $quantityToSell = rand(1, min(2, $stock->available_quantity));

                $itemTotal = $quantityToSell * $stock->selling_price;
                $itemTax = $itemTotal * ($stock->tax / 100);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'quantity' => $quantityToSell,
                    'price' => $stock->selling_price,
                    'tax' => $stock->tax,
                    'item_total' => $itemTotal,
                ]);

                // Decrement stock
                $stock->decrement('available_quantity', $quantityToSell);

                $saleSubtotal += $itemTotal;
                $saleTax += $itemTax;
            }

            // Update the sale with calculated totals
            if ($sale->items()->count() > 0) {
                $sale->update([
                    'subtotal' => $saleSubtotal,
                    'tax' => $saleTax,
                    'total' => $saleSubtotal + $saleTax,
                ]);
            } else {
                // If no items could be added (e.g., no stock), delete the sale.
                $sale->delete();
            }
        }
    }
}
