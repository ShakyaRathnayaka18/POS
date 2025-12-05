<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorCodeRequest;
use App\Http\Requests\UpdateVendorCodeRequest;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorCodeController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('product_supplier')
            ->join('products', 'product_supplier.product_id', '=', 'products.id')
            ->join('suppliers', 'product_supplier.supplier_id', '=', 'suppliers.id')
            ->select(
                'product_supplier.id',
                'products.id as product_id',
                'products.product_name',
                'products.sku as internal_sku',
                'suppliers.id as supplier_id',
                'suppliers.company_name',
                'product_supplier.vendor_product_code',
                'product_supplier.is_preferred',
                'product_supplier.lead_time_days'
            );

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_supplier.vendor_product_code', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%")
                    ->orWhere('suppliers.company_name', 'like', "%{$search}%")
                    ->orWhere('products.sku', 'like', "%{$search}%");
            });
        }

        // Supplier filter
        if ($request->filled('supplier_id')) {
            $query->where('product_supplier.supplier_id', $request->supplier_id);
        }

        // Product filter
        if ($request->filled('product_id')) {
            $query->where('product_supplier.product_id', $request->product_id);
        }

        // Preferred filter
        if ($request->filled('is_preferred')) {
            $query->where('product_supplier.is_preferred', $request->is_preferred);
        }

        $vendorCodes = $query->orderBy('suppliers.company_name')
            ->orderBy('products.product_name')
            ->paginate(15)
            ->withQueryString();

        // Stats
        $totalMappings = DB::table('product_supplier')->count();
        $preferredMappings = DB::table('product_supplier')->where('is_preferred', true)->count();
        $productsWithMappings = DB::table('product_supplier')->distinct('product_id')->count('product_id');
        $suppliersWithMappings = DB::table('product_supplier')->distinct('supplier_id')->count('supplier_id');

        // For dropdowns
        $products = Product::orderBy('product_name')->get();
        $suppliers = Supplier::orderBy('company_name')->get();

        return view('vendor-codes.index', compact(
            'vendorCodes',
            'products',
            'suppliers',
            'totalMappings',
            'preferredMappings',
            'productsWithMappings',
            'suppliersWithMappings'
        ));
    }

    public function store(StoreVendorCodeRequest $request)
    {
        $product = Product::findOrFail($request->product_id);
        $supplier = Supplier::findOrFail($request->supplier_id);

        $product->suppliers()->attach($supplier->id, [
            'vendor_product_code' => $request->vendor_product_code,
            'is_preferred' => $request->boolean('is_preferred'),
            'lead_time_days' => $request->lead_time_days,
        ]);

        return redirect()->route('vendor-codes.index')->with('success', 'Vendor code mapping created successfully.');
    }

    public function update(UpdateVendorCodeRequest $request, $id)
    {
        DB::table('product_supplier')
            ->where('id', $id)
            ->update([
                'vendor_product_code' => $request->vendor_product_code,
                'is_preferred' => $request->boolean('is_preferred'),
                'lead_time_days' => $request->lead_time_days,
                'updated_at' => now(),
            ]);

        return redirect()->route('vendor-codes.index')->with('success', 'Vendor code mapping updated successfully.');
    }

    public function destroy($id)
    {
        DB::table('product_supplier')->where('id', $id)->delete();

        return redirect()->route('vendor-codes.index')->with('success', 'Vendor code mapping deleted successfully.');
    }
}
