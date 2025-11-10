<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SalesReturn;
use App\Services\SalesReturnService;
use Exception;
use Illuminate\Http\Request;

class SalesReturnController extends Controller
{
    public function __construct(protected SalesReturnService $returnService) {}

    public function index()
    {
        $returns = SalesReturn::with(['sale', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('sales-returns.index', ['returns' => $returns]);
    }

    public function create(Request $request)
    {
        $sale = null;
        if ($request->has('sale_id')) {
            $sale = Sale::find($request->sale_id);
        }
        // In a real app, you'd likely have a search for sales, not load all of them.
        $sales = Sale::orderBy('created_at', 'desc')->limit(100)->get();
        $returnNumber = $this->returnService->generateReturnNumber();

        return view('sales-returns.create', compact('returnNumber', 'sale', 'sales'));
    }

    public function getReturnableItems(Sale $sale)
    {
        try {
            // Load customer data with the sale
            $sale->load('user');
            $items = $this->returnService->getReturnableItemsForSale($sale->id);

            return response()->json([
                'sale' => $sale,
                'items' => $items,
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'return_number' => 'required|unique:sales_returns,return_number',
            'sale_id' => 'required|exists:sales,id',
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
            'items.*.notes' => 'nullable|string',
        ]);

        try {
            $sale = Sale::findOrFail($validated['sale_id']);

            $subtotal = 0;
            $totalTax = 0;
            $itemsData = [];

            foreach ($validated['items'] as $itemData) {
                $itemTotal = $itemData['quantity_returned'] * $itemData['selling_price'];
                $subtotal += $itemTotal;
                $totalTax += $itemTotal * (($itemData['tax'] ?? 0) / 100);

                $itemData['item_total'] = $itemTotal;
                $itemData['price'] = $itemData['selling_price']; // Set price to selling_price for compatibility
                $itemData['restore_to_stock'] = ($itemData['condition'] === 'Good');
                $itemsData[] = $itemData;
            }

            $returnData = [
                'return_number' => $validated['return_number'],
                'sale_id' => $validated['sale_id'],
                'customer_name' => $sale->customer_name, // From original sale
                'customer_phone' => $sale->customer_phone, // From original sale
                'return_date' => $validated['return_date'],
                'return_reason' => $validated['return_reason'],
                'notes' => $validated['notes'],
                'subtotal' => $subtotal,
                'tax' => $totalTax,
                'total' => $subtotal + $totalTax,
                'status' => 'Pending',
            ];

            $salesReturn = $this->returnService->createSalesReturn($returnData, $itemsData);

            return redirect()->route('sales-returns.show', $salesReturn)
                ->with('success', 'Sales return created successfully.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(SalesReturn $salesReturn)
    {
        $salesReturn->load('sale', 'items.product', 'items.saleItem', 'processedBy');

        return view('sales-returns.show', compact('salesReturn'));
    }

    public function processRefund(Request $request, SalesReturn $salesReturn)
    {
        if ($salesReturn->status !== 'Pending' && $salesReturn->status !== 'Approved') {
            return back()->with('error', 'Only pending or approved returns can be refunded.');
        }

        $validated = $request->validate([
            'refund_method' => 'required|string|in:Cash,Card,Store Credit',
            'refund_amount' => 'required|numeric|min:0|max:'.$salesReturn->total,
        ]);

        try {
            $this->returnService->processRefund($salesReturn, $validated['refund_method'], $validated['refund_amount']);

            return back()->with('success', 'Refund processed successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(SalesReturn $salesReturn)
    {
        if ($salesReturn->status === 'Refunded' || $salesReturn->status === 'Completed') {
            return back()->with('error', 'Cannot cancel a return that has been refunded or completed.');
        }

        try {
            $this->returnService->cancelReturn($salesReturn);

            return back()->with('success', 'Sales return cancelled and stock restored.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
