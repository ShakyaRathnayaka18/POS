<?php

namespace App\Http\Controllers;

use App\Models\GoodReceiveNote;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\GoodReceiveNoteService;
use Illuminate\Http\Request;

class GoodReceiveNoteController extends Controller
{
    public function __construct(
        protected GoodReceiveNoteService $grnService
    ) {}

    /**
     * Display a listing of GRNs.
     */
    public function index()
    {
        $grns = GoodReceiveNote::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('good-receive-notes.index', compact('grns'));
    }

    /**
     * Show the form for creating a new GRN.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::with(['category', 'brand'])->get();
        $grnNumber = $this->grnService->generateGrnNumber();

        return view('good-receive-notes.create', compact('suppliers', 'products', 'grnNumber'));
    }

    /**
     * Store a newly created GRN.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'grn_number' => 'required|unique:good_receive_notes',
            'supplier_id' => 'required|exists:suppliers,id',
            'received_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'required|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.barcode' => 'nullable|string|unique:batches,barcode',
            'items.*.manufacture_date' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date|after_or_equal:items.*.manufacture_date',
        ]);

        $grnData = [
            'grn_number' => $validated['grn_number'],
            'supplier_id' => $validated['supplier_id'],
            'received_date' => $validated['received_date'],
            'notes' => $validated['notes'] ?? null,
            'subtotal' => 0,
            'tax' => 0,
            'shipping' => 0,
            'total' => 0,
            'status' => 'Received',
        ];

        // Calculate totals
        foreach ($validated['items'] as $item) {
            $itemTotal = $item['cost_price'] * $item['quantity'];
            $itemTax = $itemTotal * ($item['tax'] ?? 0) / 100;
            $grnData['subtotal'] += $itemTotal;
            $grnData['tax'] += $itemTax;
        }

        $grnData['total'] = $grnData['subtotal'] + $grnData['tax'];

        $grn = $this->grnService->createGrnWithBatches($grnData, $validated['items']);

        return redirect()->route('good-receive-notes.show', $grn)
            ->with('success', 'Good Receive Note created successfully!');
    }

    /**
     * Display the specified GRN.
     */
    public function show(GoodReceiveNote $goodReceiveNote)
    {
        $goodReceiveNote->load(['supplier', 'batches.stocks.product']);

        return view('good-receive-notes.show', compact('goodReceiveNote'));
    }
}
