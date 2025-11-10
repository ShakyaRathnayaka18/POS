<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesReturnService
{
    public function generateReturnNumber(): string
    {
        $lastReturn = SalesReturn::orderBy('id', 'desc')->first();
        $nextId = $lastReturn ? (int)substr($lastReturn->return_number, 4) + 1 : 1;
        return 'SLR-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createSalesReturn(array $returnData, array $items): SalesReturn
    {
        return DB::transaction(function () use ($returnData, $items) {
            $salesReturn = SalesReturn::create($returnData);

            foreach ($items as $item) {
                $salesReturn->items()->create($item);

                if ($item['restore_to_stock'] && $item['condition'] === 'Good') {
                    $stock = Stock::find($item['stock_id']);
                    if ($stock) {
                        $stock->increment('available_quantity', $item['quantity_returned']);
                    }
                }
            }

            return $salesReturn->load('items', 'sale');
        });
    }

    public function processRefund(SalesReturn $return, string $refundMethod, float $refundAmount): bool
    {
        return $return->update([
            'status' => 'Refunded',
            'refund_method' => $refundMethod,
            'refund_amount' => $refundAmount,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);
    }

    public function cancelReturn(SalesReturn $return): bool
    {
        return DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                if ($item->restore_to_stock && $item->condition === 'Good') {
                    $stock = $item->stock;
                    if ($stock) {
                        $stock->decrement('available_quantity', $item->quantity_returned);
                    }
                }
            }
            return $return->update(['status' => 'Cancelled']);
        });
    }

    public function getReturnableItemsForSale(int $saleId): array
    {
        $sale = Sale::with('items.product')->findOrFail($saleId);
        
        $returnedItems = DB::table('sales_return_items')
            ->join('sales_returns', 'sales_return_items.sales_return_id', '=', 'sales_returns.id')
            ->where('sales_returns.sale_id', $saleId)
            ->where('sales_returns.status', '!=', 'Cancelled')
            ->select('sales_return_items.sale_item_id', DB::raw('SUM(sales_return_items.quantity_returned) as total_returned'))
            ->groupBy('sales_return_items.sale_item_id')
            ->get()
            ->keyBy('sale_item_id');

        $returnableItems = [];
        foreach ($sale->items as $item) {
            $returnedQty = $returnedItems[$item->id]->total_returned ?? 0;
            $returnableQty = $item->quantity - $returnedQty;

            if ($returnableQty > 0) {
                $returnableItems[] = [
                    'sale_item_id' => $item->id,
                    'product_name' => $item->product->product_name,
                    'sku' => $item->product->sku,
                    'quantity_sold' => $item->quantity,
                    'selling_price' => $item->price,
                    'tax' => $item->tax,
                    'stock_id' => $item->stock_id,
                    'product_id' => $item->product_id,
                    'quantity_returnable' => $returnableQty,
                ];
            }
        }

        return $returnableItems;
    }
}