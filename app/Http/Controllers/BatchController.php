<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Services\BatchService;
use Illuminate\Http\Request;

class BatchController extends Controller
{
    public function __construct(
        protected BatchService $batchService
    ) {}

    /**
     * Display a listing of batches.
     */
    public function index(Request $request)
    {
        $query = Batch::with(['goodReceiveNote.supplier', 'stocks.product']);

        // Filter by product
        if ($request->has('product_id')) {
            $query->whereHas('stocks', function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            });
        }

        // Filter by supplier
        if ($request->has('supplier_id')) {
            $query->whereHas('goodReceiveNote', function ($q) use ($request) {
                $q->where('supplier_id', $request->supplier_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'expired') {
                $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '<', now());
            } elseif ($request->status === 'expiring_soon') {
                $query->whereNotNull('expiry_date')
                    ->whereDate('expiry_date', '>=', now())
                    ->whereDate('expiry_date', '<=', now()->addDays(30));
            }
        }

        $batches = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('batches.index', compact('batches'));
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch)
    {
        $batch->load(['goodReceiveNote.supplier', 'stocks.product']);

        $totalQuantity = $this->batchService->calculateBatchTotalQuantity($batch);
        $availableQuantity = $this->batchService->calculateBatchAvailableQuantity($batch);
        $batchValue = $this->batchService->getBatchValue($batch);

        return view('batches.show', compact('batch', 'totalQuantity', 'availableQuantity', 'batchValue'));
    }

    /**
     * Show expiring batches.
     */
    public function expiring()
    {
        $expiringBatches = $this->batchService->getExpiringSoonBatches(30);
        $expiredBatches = $this->batchService->getExpiredBatches();

        return view('batches.expiring', compact('expiringBatches', 'expiredBatches'));
    }
}
