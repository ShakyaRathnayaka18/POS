<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    /**
     * Generate unique adjustment number
     * Format: SA-YYYYMMDD-####
     */
    public function generateAdjustmentNumber(): string
    {
        $date = now()->format('Ymd');
        $prefix = "SA-{$date}-";

        $lastAdjustment = StockAdjustment::where('adjustment_number', 'like', "{$prefix}%")
            ->orderBy('adjustment_number', 'desc')
            ->first();

        if ($lastAdjustment) {
            $lastNumber = (int) substr($lastAdjustment->adjustment_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create stock adjustment (pending approval)
     */
    public function createAdjustment(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            $stock = Stock::with(['product', 'batch'])->findOrFail($data['stock_id']);

            // Calculate values
            $quantityBefore = $stock->available_quantity;
            $quantityAdjusted = abs($data['quantity_adjusted']);

            if ($data['type'] === 'increase') {
                $quantityAfter = $quantityBefore + $quantityAdjusted;
            } else {
                if ($quantityBefore < $quantityAdjusted) {
                    throw new \Exception("Cannot decrease more than available quantity ({$quantityBefore})");
                }
                $quantityAfter = $quantityBefore - $quantityAdjusted;
            }

            $totalValue = $quantityAdjusted * $stock->cost_price;

            // Create adjustment record
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $this->generateAdjustmentNumber(),
                'stock_id' => $stock->id,
                'product_id' => $stock->product_id,
                'batch_id' => $stock->batch_id,
                'type' => $data['type'],
                'quantity_before' => $quantityBefore,
                'quantity_adjusted' => $quantityAdjusted,
                'quantity_after' => $quantityAfter,
                'cost_price' => $stock->cost_price,
                'total_value' => $totalValue,
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
                'adjustment_date' => $data['adjustment_date'] ?? now(),
                'status' => 'pending',
            ]);

            return $adjustment->fresh(['stock.product', 'stock.batch', 'creator']);
        });
    }

    /**
     * Approve adjustment and update stock
     */
    public function approveAdjustment(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            if (! $adjustment->isPending()) {
                throw new \Exception('Only pending adjustments can be approved');
            }

            $stock = $adjustment->stock;

            // Update stock quantities
            if ($adjustment->isIncrease()) {
                $stock->increment('quantity', $adjustment->quantity_adjusted);
                $stock->increment('available_quantity', $adjustment->quantity_adjusted);
            } else {
                $stock->decrement('quantity', $adjustment->quantity_adjusted);
                $stock->decrement('available_quantity', $adjustment->quantity_adjusted);
            }

            // Update adjustment status
            $adjustment->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });
    }

    /**
     * Reject adjustment
     */
    public function rejectAdjustment(StockAdjustment $adjustment): void
    {
        if (! $adjustment->isPending()) {
            throw new \Exception('Only pending adjustments can be rejected');
        }

        $adjustment->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    }
}
