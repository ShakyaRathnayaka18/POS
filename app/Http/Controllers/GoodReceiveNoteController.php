<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GoodReceiveNote;
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
        $categories = Category::all();
        $brands = Brand::all();
        $grnNumber = $this->grnService->generateGrnNumber();

        return view('good-receive-notes.create', compact('suppliers', 'categories', 'brands', 'grnNumber'));
    }

    /**
     * Get products for a specific supplier with vendor codes.
     */
    public function getSupplierProducts(Supplier $supplier)
    {
        $products = $supplier->products()
            ->with(['category', 'brand'])
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'item_code' => $product->item_code,
                    'category' => $product->category?->cat_name,
                    'brand' => $product->brand?->brand_name,
                    'vendor_product_code' => $product->pivot->vendor_product_code,
                    'vendor_cost_price' => $product->pivot->vendor_cost_price,
                    'lead_time_days' => $product->pivot->lead_time_days,
                ];
            });

        return response()->json($products);
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
