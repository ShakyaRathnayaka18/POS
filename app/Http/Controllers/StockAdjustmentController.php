<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockAdjustmentRequest;
use App\Models\Stock;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function __construct(
        protected StockAdjustmentService $adjustmentService
    ) {}

    /**
     * Display list of stock adjustments
     */
    public function index(Request $request)
    {
        $query = StockAdjustment::with(['stock.product', 'stock.batch', 'creator', 'approver'])
            ->latest('adjustment_date');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('adjustment_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('adjustment_date', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('adjustment_number', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where('product_name', 'like', "%{$search}%");
                    });
            });
        }

        $adjustments = $query->paginate(20);

        // Stats
        $stats = [
            'total' => StockAdjustment::count(),
            'pending' => StockAdjustment::where('status', 'pending')->count(),
            'approved' => StockAdjustment::where('status', 'approved')->count(),
            'rejected' => StockAdjustment::where('status', 'rejected')->count(),
            'total_value_increase' => StockAdjustment::where('status', 'approved')
                ->where('type', 'increase')
                ->sum('total_value'),
            'total_value_decrease' => StockAdjustment::where('status', 'approved')
                ->where('type', 'decrease')
                ->sum('total_value'),
        ];

        return view('stock-adjustments.index', compact('adjustments', 'stats'));
    }

    /**
     * Show create form
     */
    public function create(Request $request)
    {
        $stock = null;
        if ($request->filled('stock_id')) {
            $stock = Stock::with(['product', 'batch'])->find($request->stock_id);
        }

        return view('stock-adjustments.create', compact('stock'));
    }

    /**
     * Store new adjustment
     */
    public function store(StockAdjustmentRequest $request)
    {
        try {
            $adjustment = $this->adjustmentService->createAdjustment($request->validated());

            return redirect()->route('stock-adjustments.show', $adjustment)
                ->with('success', "Stock adjustment {$adjustment->adjustment_number} created successfully and pending approval.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create adjustment: '.$e->getMessage());
        }
    }

    /**
     * Display adjustment details
     */
    public function show(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load([
            'stock.product.category',
            'stock.product.brand',
            'stock.batch.goodReceiveNote.supplier',
            'creator',
            'approver',
            'journalEntry.lines.account',
        ]);

        return view('stock-adjustments.show', compact('stockAdjustment'));
    }

    /**
     * Approve adjustment
     */
    public function approve(StockAdjustment $stockAdjustment)
    {
        try {
            $this->adjustmentService->approveAdjustment($stockAdjustment);

            return redirect()->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve adjustment: '.$e->getMessage());
        }
    }

    /**
     * Reject adjustment
     */
    public function reject(StockAdjustment $stockAdjustment)
    {
        try {
            $this->adjustmentService->rejectAdjustment($stockAdjustment);

            return redirect()->route('stock-adjustments.show', $stockAdjustment)
                ->with('success', 'Stock adjustment rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to reject adjustment: '.$e->getMessage());
        }
    }
}
