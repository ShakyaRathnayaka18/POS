<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\SupplierReturn;
use App\Models\SupplierReturnItem;
use Illuminate\Support\Facades\DB;
use Exception;

class SupplierReturnService
{
    public function generateReturnNumber(): string
    {
        $lastReturn = SupplierReturn::orderBy('id', 'desc')->first();
        $nextId = $lastReturn ? (int)substr($lastReturn->return_number, 3) + 1 : 1;
        return 'SR-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    public function createSupplierReturn(array $returnData, array $items): SupplierReturn
    {
        return DB::transaction(function () use ($returnData, $items) {
            $supplierReturn = SupplierReturn::create($returnData);

            foreach ($items as $item) {
                $stock = Stock::findOrFail($item['stock_id']);

                if ($stock->available_quantity < $item['quantity_returned']) {
                    throw new Exception('Not enough available quantity for stock ID: ' . $stock->id);
                }

                $itemTotal = $item['quantity_returned'] * $item['cost_price'];

                $supplierReturn->items()->create([
                    'stock_id' => $stock->id,
                    'product_id' => $stock->product_id,
                    'batch_id' => $stock->batch_id,
                    'quantity_returned' => $item['quantity_returned'],
                    'cost_price' => $item['cost_price'],
                    'tax' => $item['tax'] ?? $stock->tax,
                    'item_total' => $itemTotal,
                    'condition' => $item['condition'] ?? 'Damaged',
                    'notes' => $item['notes'] ?? null,
                ]);

                // Decrement stock quantities
                $stock->decrement('available_quantity', $item['quantity_returned']);
                $stock->decrement('quantity', $item['quantity_returned']);
            }

            return $supplierReturn->load('items', 'supplier', 'goodReceiveNote');
        });
    }

    public function approveReturn(SupplierReturn $return, int $approvedBy): bool
    {
        return $return->update([
            'status' => 'Approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function completeReturn(SupplierReturn $return): bool
    {
        return $return->update(['status' => 'Completed']);
    }

    public function cancelReturn(SupplierReturn $return): bool
    {
        return DB::transaction(function () use ($return) {
            foreach ($return->items as $item) {
                $stock = $item->stock;
                if ($stock) {
                    $stock->increment('available_quantity', $item->quantity_returned);
                    $stock->increment('quantity', $item->quantity_returned);
                }
            }
            return $return->update(['status' => 'Cancelled']);
        });
    }

    public function getReturnableStockForGrn(int $grnId): array
    {
        return Stock::whereHas('batch', function ($query) use ($grnId) {
            $query->where('good_receive_note_id', $grnId);
        })
        ->where('available_quantity', '>', 0)
        ->with(['product', 'batch'])
        ->get()
        ->toArray();
    }

    public function returnEntireBatch(int $batchId, array $returnData): SupplierReturn
    {
        $stocks = Stock::where('batch_id', $batchId)->where('available_quantity', '>', 0)->get();
        
        $items = $stocks->map(function ($stock) {
            return [
                'stock_id' => $stock->id,
                'quantity_returned' => $stock->available_quantity,
                'cost_price' => $stock->cost_price,
                'tax' => $stock->tax,
                'condition' => 'Damaged', // Default condition
                'notes' => 'Full batch return.',
            ];
        })->toArray();

        if (empty($items)) {
            throw new Exception('No returnable stock found for this batch.');
        }

        return $this->createSupplierReturn($returnData, $items);
    }
}