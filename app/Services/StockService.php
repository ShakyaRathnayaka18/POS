<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Get available stock for a product using FIFO (First In First Out).
     * Prioritizes FOC (Free of Charge) stock first to maximize profit.
     */
    public function getAvailableStockFIFO(int $productId, float $requestedQuantity): Collection
    {
        return Stock::where('product_id', $productId)
            ->where('available_quantity', '>', 0)
            ->with('batch')
            ->orderBy('cost_price', 'asc') // FOC (cost_price = 0) first
            ->orderBy('created_at', 'asc') // Then FIFO within same cost type
            ->get()
            ->filter(function ($stock) use (&$requestedQuantity) {
                if ($requestedQuantity <= 0) {
                    return false;
                }
                $requestedQuantity -= $stock->available_quantity;

                return true;
            });
    }

    /**
     * Allocate stock for a sale using FIFO method.
     * Supports decimal quantities for products sold by weight/volume.
     */
    public function allocateStock(int $productId, float $quantity): array
    {
        $stocks = $this->getAvailableStockFIFO($productId, $quantity);
        $allocations = [];
        $remainingQuantity = $quantity;

        foreach ($stocks as $stock) {
            if ($remainingQuantity <= 0) {
                break;
            }

            $allocatedQuantity = min((float) $stock->available_quantity, $remainingQuantity);

            $allocations[] = [
                'stock_id' => $stock->id,
                'batch_id' => $stock->batch_id,
                'quantity' => $allocatedQuantity,
                'cost_price' => $stock->cost_price,
                'selling_price' => $stock->selling_price,
                'tax' => $stock->tax,
            ];

            $remainingQuantity -= $allocatedQuantity;
        }

        // Use small threshold for floating point comparison
        if ($remainingQuantity > 0.0001) {
            throw new \Exception("Insufficient stock available. Required: {$quantity}, Available: ".($quantity - $remainingQuantity));
        }

        return $allocations;
    }

    /**
     * Deduct allocated stock quantities.
     */
    public function deductStock(array $allocations): void
    {
        DB::transaction(function () use ($allocations) {
            foreach ($allocations as $allocation) {
                Stock::where('id', $allocation['stock_id'])
                    ->decrement('available_quantity', $allocation['quantity']);
            }
        });
    }

    /**
     * Get total available quantity for a product across all batches.
     */
    public function getProductAvailableQuantity(int $productId): float
    {
        return (float) Stock::where('product_id', $productId)
            ->sum('available_quantity');
    }

    /**
     * Get total stock value for a product.
     */
    public function getProductStockValue(int $productId): float
    {
        return Stock::where('product_id', $productId)
            ->selectRaw('SUM(available_quantity * cost_price) as total_value')
            ->value('total_value') ?? 0;
    }

    /**
     * Get low stock products based on minimum_stock threshold.
     */
    public function getLowStockProducts(): Collection
    {
        return Product::with(['stocks', 'category', 'brand'])
            ->get()
            ->filter(function ($product) {
                $availableQuantity = $product->stocks->sum('available_quantity');

                return $availableQuantity <= $product->minimum_stock && $availableQuantity > 0;
            });
    }

    /**
     * Get out of stock products.
     */
    public function getOutOfStockProducts(): Collection
    {
        return Product::with(['category', 'brand'])
            ->whereDoesntHave('stocks', function ($query) {
                $query->where('available_quantity', '>', 0);
            })
            ->get();
    }
}
