<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->get();
        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);
        $validated['po_number'] = 'PO-' . date('Y') . '-' . str_pad(PurchaseOrder::max('id') + 1, 3, '0', STR_PAD_LEFT);
        // Calculate total if not provided
        $validated['subtotal'] = $request->input('subtotal', 0);
        $validated['tax'] = $request->input('tax', 0);
        $validated['shipping'] = $request->input('shipping', 0);
        $validated['total'] = $validated['subtotal'] + $validated['tax'] + $validated['shipping'];
        $purchaseOrder = PurchaseOrder::create($validated);
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $suppliers = Supplier::all();
        $products = Product::all();
        return view('purchase-orders.create', compact('purchaseOrder', 'suppliers', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'expected_date' => 'nullable|date',
            'notes' => 'nullable|string',
            'subtotal' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'shipping' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);
        $validated['subtotal'] = $request->input('subtotal', 0);
        $validated['tax'] = $request->input('tax', 0);
        $validated['shipping'] = $request->input('shipping', 0);
        $validated['total'] = $validated['subtotal'] + $validated['tax'] + $validated['shipping'];
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->update($validated);
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($id);
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')->with('success', 'Purchase Order deleted successfully.');
    }
}
