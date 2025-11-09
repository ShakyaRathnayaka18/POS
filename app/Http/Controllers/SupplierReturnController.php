<?php

namespace App\Http\Controllers;

use App\Models\GoodReceiveNote;
use App\Models\SupplierReturn;
use App\Services\SupplierReturnService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierReturnController extends Controller
{
    protected SupplierReturnService $returnService;

    public function __construct(SupplierReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index()
    {
        $returns = SupplierReturn::with('supplier', 'goodReceiveNote')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('returns.supplier-returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $grn = null;
        if ($request->has('grn_id')) {
            $grn = GoodReceiveNote::with('supplier')->find($request->grn_id);
        }

        $grns = GoodReceiveNote::with('supplier')->get();
        $returnNumber = $this->returnService->generateReturnNumber();

        return view('returns.supplier-returns.create', compact('returnNumber', 'grn', 'grns'));
    }

    public function getReturnableStock(GoodReceiveNote $grn)
    {
        $stock = $this->returnService->getReturnableStockForGrn($grn->id);
        return response()->json($stock);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'return_number' => 'required|unique:supplier_returns',
            'good_receive_note_id' => 'required|exists:good_receive_notes,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'return_date' => 'required|date',
            'return_reason' => 'required|string|max:191',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.stock_id' => 'required|exists:stocks,id',
            'items.*.quantity_returned' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.condition' => 'required|in:Damaged,Defective,Wrong Item,Overstocked',
            'items.*.notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        $totalTax = 0;

        foreach ($validated['items'] as $item) {
            $itemTotal = $item['quantity_returned'] * $item['cost_price'];
            $subtotal += $itemTotal;
            $totalTax += $itemTotal * (($item['tax'] ?? 0) / 100);
        }

        $returnData = [
            'return_number' => $validated['return_number'],
            'good_receive_note_id' => $validated['good_receive_note_id'],
            'supplier_id' => $validated['supplier_id'],
            'return_date' => $validated['return_date'],
            'return_reason' => $validated['return_reason'],
            'notes' => $validated['notes'],
            'subtotal' => $subtotal,
            'tax' => $totalTax,
            'adjustment' => 0,
            'total' => $subtotal + $totalTax,
            'status' => 'Pending',
            'created_by' => Auth::id(),
        ];

        try {
            $this->returnService->createSupplierReturn($returnData, $validated['items']);
            return redirect()->route('supplier-returns.index')->with('success', 'Supplier return created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load('supplier', 'goodReceiveNote', 'items.product', 'items.batch', 'items.stock', 'createdBy', 'approvedBy');
        return view('returns.supplier-returns.show', compact('supplierReturn'));
    }

    public function approve(SupplierReturn $supplierReturn)
    {
        $this->returnService->approveReturn($supplierReturn, Auth::id());
        return back()->with('success', 'Return approved successfully.');
    }



    public function complete(SupplierReturn $supplierReturn)
    {
        $this->returnService->completeReturn($supplierReturn);
        return back()->with('success', 'Return marked as complete.');
    }

    public function cancel(SupplierReturn $supplierReturn)
    {
        $this->returnService->cancelReturn($supplierReturn);
        return back()->with('success', 'Return cancelled and stock restored.');
    }
}