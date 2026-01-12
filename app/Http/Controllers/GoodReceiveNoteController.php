<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\GoodReceiveNote;
use App\Models\Supplier;
use App\Services\GoodReceiveNoteService;
use App\Services\SupplierCreditService;
use Illuminate\Http\Request;

class GoodReceiveNoteController extends Controller
{
    public function __construct(
        protected GoodReceiveNoteService $grnService,
        protected SupplierCreditService $creditService
    ) {}

    /**
     * Display a listing of GRNs.
     */
    public function index(Request $request)
    {
        $query = GoodReceiveNote::with('supplier')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $grns = $query->paginate(15)
            ->withQueryString();

        $suppliers = Supplier::orderBy('company_name')->get();

        return view('good-receive-notes.index', compact('grns', 'suppliers'));
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
                    'category' => $product->category?->cat_name,
                    'brand' => $product->brand?->brand_name,
                    'vendor_product_code' => $product->pivot->vendor_product_code,
                    'lead_time_days' => $product->pivot->lead_time_days,
                    'base_unit' => $product->base_unit,
                    'purchase_unit' => $product->purchase_unit,
                    'conversion_factor' => $product->conversion_factor,
                    'allow_decimal_sales' => $product->allow_decimal_sales,
                ];
            });

        return response()->json($products);
    }

    /**
     * Get supplier credit information.
     */
    public function getSupplierCreditInfo(Supplier $supplier)
    {
        return response()->json([
            'company_name' => $supplier->company_name,
            'credit_limit' => $supplier->credit_limit,
            'current_credit_used' => $supplier->current_credit_used,
            'available_credit' => $supplier->available_credit,
        ]);
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
            'invoice_number' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'payment_type' => 'required|in:cash,credit',
            'credit_terms' => 'required_if:payment_type,credit',
            'notes' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.cost_price' => 'required|numeric|min:0',
            'items.*.selling_price' => 'nullable|numeric|min:0',
            'items.*.tax' => 'nullable|numeric|min:0',
            'items.*.barcode' => 'nullable|string|unique:batches,barcode',
            'items.*.manufacture_date' => 'nullable|date',
            'items.*.expiry_date' => 'nullable|date|after_or_equal:items.*.manufacture_date',
        ]);

        $grnData = [
            'grn_number' => $validated['grn_number'],
            'supplier_id' => $validated['supplier_id'],
            'received_date' => $validated['received_date'],
            'invoice_number' => $validated['invoice_number'],
            'invoice_date' => $validated['invoice_date'],
            'payment_type' => $validated['payment_type'],
            'is_credit' => $validated['payment_type'] === 'credit',
            'notes' => $validated['notes'] ?? null,
            'subtotal_before_discount' => 0,
            'discount' => $validated['discount'] ?? 0,
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
            $grnData['subtotal_before_discount'] += $itemTotal;
            $grnData['tax'] += $itemTax;
        }

        // Apply discount
        $grnData['subtotal'] = $grnData['subtotal_before_discount'] - $grnData['discount'];
        $grnData['total'] = $grnData['subtotal'] + $grnData['tax'] + $grnData['shipping'];

        try {
            $grn = $this->grnService->createGrnWithBatches($grnData, $validated['items']);

            // If payment type is credit, create supplier credit
            if ($validated['payment_type'] === 'credit') {
                $creditData = [
                    'invoice_number' => $validated['invoice_number'],
                    'invoice_date' => $validated['invoice_date'],
                    'credit_terms' => $validated['credit_terms'],
                ];

                $this->creditService->createCreditFromGrn($grn, $creditData);
            }

            return redirect()->route('good-receive-notes.show', $grn)
                ->with('success', 'Good Receive Note created successfully!'.
                    ($validated['payment_type'] === 'credit' ? ' Supplier credit has been recorded.' : ''));
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error creating GRN: '.$e->getMessage());
        }
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
