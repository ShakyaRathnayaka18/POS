<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Services\SalesReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesReturnController extends Controller
{
    protected SalesReturnService $returnService;

    public function __construct(SalesReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index()
    {
        $returns = SalesReturn::with('sale')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('returns.sales-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->has('sale_id')) {
            $sale = Sale::find($request->sale_id);
        }

        $sales = Sale::all();
        $returnNumber = $this->returnService->generateReturnNumber();

        return view('returns.sales-returns.create', compact('returnNumber', 'sale', 'sales'));
    }

    public function getReturnableItems(Sale $sale)
    {
        $items = $this->returnService->getReturnableItemsForSale($sale->id);
        return response()->json($items);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'return_number' => 'required|unique:sales_returns',
            'sale_id' => 'required|exists:sales,id',
            'customer_name' => 'nullable|string|max:191',
            'customer_phone' => 'nullable|string|max:191',
            'return_date' => 'required|date',
            'return_reason' => 'required|string|max:191',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.selling_price' => 'required|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.condition' => 'required|in:Good,Damaged,Defective,Used',
            'items.*.restore_to_stock' => 'required|boolean',
            'items.*.notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity_returned'] * $item['selling_price'];
            $subtotal += $itemTotal;
            $totalTax += $itemTotal * (($item['tax'] ?? 0) / 100);
        }

        $returnData = [
            'return_number' => $validated['return_number'],
            'sale_id' => $validated['sale_id'],
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'return_date' => $validated['return_date'],
            'return_reason' => $validated['return_reason'],
            'notes' => $validated['notes'],
            'subtotal' => $subtotal,
            'tax' => $totalTax,
            'total' => $subtotal + $totalTax,
            'refund_amount' => 0,
            'status' => 'Pending',
            'processed_by' => Auth::id(),
        ];

        $itemsData = array_map(function ($item) {
            $item['item_total'] = $item['quantity_returned'] * $item['selling_price'];
            return $item;
        }, $validated['items']);

        try {
            $this->returnService->createSalesReturn($returnData, $itemsData);
            return redirect()->route('sales-returns.index')->with('success', 'Sales return created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load('sale', 'items.product', 'processedBy');
        return view('returns.sales-returns.show', compact('salesReturn'));
    }

    public function processRefund(Request $request, SalesReturn $salesReturn)
    {
        $validated = $request->validate([
            'refund_method' => 'required|string|max:191',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $this->returnService->processRefund($salesReturn, $validated['refund_method'], $validated['refund_amount']);

        return back()->with('success', 'Refund processed successfully.');
    }

    public function cancel(SalesReturn $salesReturn)
    {
        $this->returnService->cancelReturn($salesReturn);
        return back()->with('success', 'Sales return cancelled.');
    }
}