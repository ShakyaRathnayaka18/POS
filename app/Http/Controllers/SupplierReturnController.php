<?php

namespace App\Http\Controllers;

use App\Models\GoodReceiveNote;
use App\Models\SupplierReturn;
use App\Services\SupplierReturnService;
use Exception;
use Illuminate\Http\Request;

class SupplierReturnController extends Controller
{
    public function __construct(protected SupplierReturnService $returnService) {}

    public function index()
    {
        $returns = SupplierReturn::with(['supplier', 'goodReceiveNote'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('supplier-returns.index', ['returns' => $returns]);
    }

    public function create(Request $request)
    {
        $grn = null;
        if ($request->has('grn_id')) {
            $grn = GoodReceiveNote::with('supplier')->find($request->grn_id);
        }

        // Load all GRNs with their suppliers for the dropdown
        $grns = GoodReceiveNote::with('supplier')
            ->orderBy('created_at', 'desc')
            ->get();

        $returnNumber = $this->returnService->generateReturnNumber();

        return view('supplier-returns.create', compact('returnNumber', 'grn', 'grns'));
    }

    public function getReturnableStock(GoodReceiveNote $grn)
    {
        try {
            $stock = $this->returnService->getReturnableStockForGrn($grn->id);

            return response()->json($stock);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'return_number' => 'required|unique:supplier_returns,return_number',
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

        try {
            $subtotal = 0;
            $totalTax = 0;

            foreach ($validated['items'] as $item) {
                $itemTotal = $item['quantity_returned'] * $item['cost_price'];
                $subtotal += $itemTotal;
                if (isset($item['tax'])) {
                    $totalTax += $itemTotal * ($item['tax'] / 100);
                }
            }

            $returnData = $validated;
            $returnData['subtotal'] = $subtotal;
            $returnData['tax'] = $totalTax;
            $returnData['adjustment'] = 0; // Default adjustment
            $returnData['total'] = $subtotal + $totalTax;
            $returnData['status'] = 'Pending';
            $returnData['created_by'] = auth()->id() ?? 1; // Default to 1 if no authentication

            $supplierReturn = $this->returnService->createSupplierReturn($returnData, $validated['items']);

            return redirect()->route('supplier-returns.show', $supplierReturn)
                ->with('success', 'Supplier return created successfully.');

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(SupplierReturn $supplierReturn)
    {
        $supplierReturn->load(
            'supplier',
            'goodReceiveNote',
            'items.product',
            'items.batch',
            'items.stock',
            'createdBy',
            'approvedBy'
        );

        return view('supplier-returns.show', compact('supplierReturn'));
    }

    public function approve(SupplierReturn $supplierReturn)
    {
        try {
            $approvedBy = auth()->id() ?? 1; // Default to 1 if no authentication
            $this->returnService->approveReturn($supplierReturn, $approvedBy);

            return back()->with('success', 'Return approved successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(SupplierReturn $supplierReturn)
    {
        try {
            $this->returnService->completeReturn($supplierReturn);

            return back()->with('success', 'Return marked as completed.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(SupplierReturn $supplierReturn)
    {
        try {
            $this->returnService->cancelReturn($supplierReturn);

            return back()->with('success', 'Return cancelled and stock restored.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
