<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class SalesReturnService
{
    public function generateReturnNumber()
    {
        $lastReturn = SalesReturn::orderBy('id', 'desc')->first();
        if (!$lastReturn) {
            return 'SLR-000001';
        }

        $lastNumber = (int) substr($lastReturn->return_number, 4);
        return 'SLR-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function createSalesReturn(array $returnData, array $items)
    {
        return DB::transaction(function () use ($returnData, $items) {
            $return = SalesReturn::create($returnData);

            foreach ($items as $item) {
                $returnItem = $return->items()->create($item);

                if ($returnItem->restore_to_stock && $returnItem->condition === 'Good') {
                    $stock = Stock::findOrFail($returnItem->stock_id);
                    $stock->increment('available_quantity', $returnItem->quantity_returned);
                }
            }

            return $return->load('items', 'sale');
        });
    }

    public function processRefund(SalesReturn $return, $refundMethod, $refundAmount)
    {
        $return->update([
            'status' => 'Refunded',
            'refund_method' => $refundMethod,
            'refund_amount' => $refundAmount,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ]);

        return true;
    }

    public function cancelReturn(SalesReturn $return)
    {
        return DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                if ($item->restore_to_stock && $item->condition === 'Good') {
                    $stock = Stock::findOrFail($item->stock_id);
                    $stock->decrement('available_quantity', $item->quantity_returned);
                }
            }

            $return->update(['status' => 'Cancelled']);
            return true;
        });
    }

    public function getReturnableItemsForSale($saleId)
    {
        $sale = Sale::with('items.product')->findOrFail($saleId);
        $returnedItems = SalesReturnItem::whereHas('salesReturn', function ($query) use ($saleId) {
            $query->where('sale_id', $saleId);
        })->get();

        $returnableItems = [];

        foreach ($sale->items as $item) {
            $returnedQty = $returnedItems->where('sale_item_id', $item->id)->sum('quantity_returned');
            $returnableQty = $item->quantity - $returnedQty;

            if ($returnableQty > 0) {
                $item->returnable_quantity = $returnableQty;
                $returnableItems[] = $item;
            }
        }

        return $returnableItems;
    }
}
