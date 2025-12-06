<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorCodeRequest;
use App\Http\Requests\UpdateVendorCodeRequest;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorCodeController extends Controller
{
    /**
     * Generate a vendor code from supplier name and product SKU.
     * Format: {First 3 letters of supplier}-{Numeric part of SKU}
     * Example: Supplier "SMACK" + SKU "SKU-000020" = "SMK-000020"
     */
    public static function generateVendorCode(Supplier $supplier, Product $product): string
    {
        // Get first 3 letters of supplier name (letters only), uppercase
        $supplierPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $supplier->company_name), 0, 3));

        // Extract numeric part from SKU (e.g., "SKU-000020" → "000020")
        preg_match('/(\d+)$/', $product->sku, $matches);
        $numericPart = $matches[1] ?? '000000';

        return $supplierPrefix.'-'.$numericPart;
    }

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
        $brands = Brand::orderBy('brand_name')->get();

        return view('vendor-codes.index', compact(
            'vendorCodes',
            'products',
            'suppliers',
            'brands',
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

        // Auto-generate vendor code if checkbox is checked
        $vendorCode = $request->boolean('auto_generate')
            ? self::generateVendorCode($supplier, $product)
            : $request->vendor_product_code;

        $product->suppliers()->attach($supplier->id, [
            'vendor_product_code' => $vendorCode,
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

    /**
     * Get products that don't have vendor codes for a specific supplier.
     * Optionally filter by brand_id.
     */
    public function getProductsWithoutVendorCodes(Request $request)
    {
        $supplierId = $request->supplier_id;
        $brandId = $request->brand_id;

        if (! $supplierId) {
            return response()->json([]);
        }

        $query = Product::whereDoesntHave('suppliers', function ($q) use ($supplierId) {
            $q->where('supplier_id', $supplierId);
        });

        // Filter by brand if provided
        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        $products = $query->orderBy('product_name')->get(['id', 'product_name', 'sku']);

        return response()->json($products);
    }

    /**
     * Bulk sync vendor codes for multiple products.
     */
    public function bulkSync(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $supplier = Supplier::findOrFail($validated['supplier_id']);
        $count = 0;

        foreach ($validated['product_ids'] as $productId) {
            $product = Product::find($productId);

            // Skip if mapping already exists
            if ($product->suppliers()->where('supplier_id', $supplier->id)->exists()) {
                continue;
            }

            $vendorCode = self::generateVendorCode($supplier, $product);

            $product->suppliers()->attach($supplier->id, [
                'vendor_product_code' => $vendorCode,
                'is_preferred' => false,
                'lead_time_days' => null,
            ]);

            $count++;
        }

        return redirect()->route('vendor-codes.index')
            ->with('success', "Successfully created {$count} vendor code mappings.");
    }
}
