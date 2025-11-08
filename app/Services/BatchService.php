<?php

namespace App\Services;

use App\Models\Batch;
use Illuminate\Support\Collection;

class BatchService
{
    public function getBatchesByProduct(int $productId): Collection
    {
        return Batch::whereHas('stocks', function ($query) use ($productId) {
            $query->where('product_id', $productId);
        })->with(['stocks' => function ($query) use ($productId) {
            $query->where('product_id', $productId);
        }, 'goodReceiveNote.supplier'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getExpiringSoonBatches(int $days = 30): Collection
    {
        return Batch::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days))
            ->whereDate('expiry_date', '>=', now())
            ->with(['stocks.product', 'goodReceiveNote.supplier'])
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    public function getExpiredBatches(): Collection
    {
        return Batch::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now())
            ->with(['stocks.product', 'goodReceiveNote.supplier'])
            ->orderBy('expiry_date', 'desc')
            ->get();
    }

    public function getBatchWithStocks(int $batchId): ?Batch
    {
        return Batch::with(['stocks.product', 'goodReceiveNote.supplier'])
            ->find($batchId);
    }

    public function calculateBatchTotalQuantity(Batch $batch): int
    {
        return $batch->stocks()->sum('quantity');
    }

    public function calculateBatchAvailableQuantity(Batch $batch): int
    {
        return $batch->stocks()->sum('available_quantity');
    }

    public function getBatchValue(Batch $batch): float
    {
        return $batch->stocks()->selectRaw('SUM(available_quantity * cost_price) as total_value')
            ->value('total_value') ?? 0;
    }
}
