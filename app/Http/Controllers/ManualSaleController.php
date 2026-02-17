<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreManualSaleRequest;
use App\Models\ManualSale;
use App\Services\ManualSaleService;
use Exception;
use Illuminate\Http\Request;

class ManualSaleController extends Controller
{
    public function __construct(protected ManualSaleService $manualSaleService) {}

    /**
     * Display manual sales history
     */
    public function index(Request $request)
    {
        $query = ManualSale::with(['user', 'items'])
            ->orderBy('created_at', 'desc');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Cashier filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search by manual sale number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('manual_sale_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $manualSales = $query->paginate(15)->withQueryString();

        // Get all users for cashier filter
        $users = \App\Models\User::orderBy('name')->get();

        return view('manual-sales.index', compact('manualSales', 'users'));
    }

    /**
     * Process a new manual sale
     */
    public function store(StoreManualSaleRequest $request)
    {
        try {
            $saleData = [
                'manual_sale_number' => $this->manualSaleService->generateManualSaleNumber(),
                'user_id' => auth()->id() ?? 1,
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'payment_method' => $request->payment_method,
            ];

            if (strtolower($request->payment_method) === 'cash') {
                $saleData['amount_received'] = $request->input('amountReceived');
                $saleData['change_amount'] = $request->input('changeAmount');
            }

            $manualSale = $this->manualSaleService->processManualSale($saleData, $request->items);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Manual sale completed successfully.',
                    'manual_sale' => [
                        'id' => $manualSale->id,
                        'manual_sale_number' => $manualSale->manual_sale_number,
                        'total' => $manualSale->total,
                        'amount_received' => $manualSale->amount_received,
                        'change_amount' => $manualSale->change_amount,
                    ],
                ]);
            }

            return redirect()->route('manual-sales.show', $manualSale)
                ->with('success', 'Manual sale completed successfully.');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display manual sale details / receipt
     * Uses the same receipt view as regular sales with isManualSale flag
     */
    public function show(ManualSale $manualSale)
    {
        $manualSale->load(['items', 'user']);

        // Format manual sale to look like regular sale for the receipt view
        $sale = (object) [
            'id' => $manualSale->id,
            'sale_number' => $manualSale->manual_sale_number,
            'user' => $manualSale->user,
            'customer_name' => $manualSale->customer_name,
            'customer_phone' => $manualSale->customer_phone,
            'subtotal' => $manualSale->subtotal,
            'tax' => $manualSale->tax,
            'total' => $manualSale->total,
            'total_discount' => $manualSale->total_discount ?? 0,
            'payment_method' => $manualSale->payment_method,
            'amount_received' => $manualSale->amount_received,
            'change_amount' => $manualSale->change_amount,
            'created_at' => $manualSale->created_at,
            'items' => $manualSale->items->map(function ($item) {
                return (object) [
                    'product' => (object) [
                        'product_name' => $item->product_name,
                        'is_weighted' => false, // Manual items are assumed non-weighted for now
                    ],
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'price_before_discount' => $item->price_before_discount ?? $item->price,
                    'tax' => $item->tax,
                    'total' => $item->total,
                    'is_weighted' => false, // Also added here for good measure if accessed directly
                    'discount_type' => $item->discount_type ?? 'none',
                    'discount_amount' => $item->discount_amount ?? 0,
                ];
            }),
        ];

        return view('sales.receipt', [
            'sale' => $sale,
            'isManualSale' => true,
            'manualSaleStatus' => $manualSale->status,
        ]);
    }
}
