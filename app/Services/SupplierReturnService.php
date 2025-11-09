<?php

namespace App\Services;

use App\Models\SupplierReturn;
use App\Models\SupplierReturnItem;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class SupplierReturnService
{
    public function generateReturnNumber()
    {
        $lastReturn = SupplierReturn::orderBy('id', 'desc')->first();
        if (!$lastReturn) {
            return 'SR-000001';
        }

        $lastNumber = (int) substr($lastReturn->return_number, 3);
        return 'SR-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function createSupplierReturn(array $returnData, array $items)
    {
        return DB::transaction(function () use ($returnData, $items) {
            $return = SupplierReturn::create($returnData);

            foreach ($items as $item) {
                $stock = Stock::findOrFail($item['stock_id']);

                if ($stock->available_quantity < $item['quantity_returned']) {
                    throw new \Exception('Not enough stock available for return.');
                }

                $item_total = $item['quantity_returned'] * $item['cost_price'];

                $return->items()->create([
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'batch_id' => $stock->batch_id,
                    'quantity_returned' => $item['quantity_returned'],
                    'cost_price' => $item['cost_price'],
                    'tax' => $item['tax'] ?? $stock->tax,
                    'item_total' => $item_total,
                    'condition' => $item['condition'] ?? 'Damaged',
                    'notes' => $item['notes'] ?? null,
                ]);

                $stock->decrement('available_quantity', $item['quantity_returned']);
                $stock->decrement('quantity', $item['quantity_returned']);
            }

            return $return->load('items', 'supplier', 'goodReceiveNote');
        });
    }

    public function approveReturn(SupplierReturn $return, $approvedBy)
    {
        $return->update([
            'status' => 'Approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function completeReturn(SupplierReturn $return)
    {
        $return->update(['status' => 'Completed']);
        return true;
    }

    public function cancelReturn(SupplierReturn $return)
    {
        return DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $stock = Stock::findOrFail($item->stock_id);
                $stock->increment('available_quantity', $item->quantity_returned);
                $stock->increment('quantity', $item->quantity_returned);
            }

            $return->update(['status' => 'Cancelled']);
            return true;
        });
    }

    public function getReturnableStockForGrn($grnId)
    {
        return Stock::whereHas('batch', function ($query) use ($grnId) {
            $query->where('good_receive_note_id', $grnId);
        })
            ->where('available_quantity', '>', 0)
            ->with('product', 'batch')
            ->get();
    }

    public function returnEntireBatch($batchId, array $returnData)
    {
        $stocks = Stock::where('batch_id', $batchId)->where('available_quantity', '>', 0)->get();
        $items = [];

        foreach ($stocks as $stock) {
            $items[] = [
                'stock_id' => $stock->id,
                'quantity_returned' => $stock->available_quantity,
                'cost_price' => $stock->cost_price,
                'tax' => $stock->tax,
                'condition' => 'Damaged', // Default condition
                'notes' => 'Full batch return',
            ];
        }

        return $this->createSupplierReturn($returnData, $items);
    }
}
