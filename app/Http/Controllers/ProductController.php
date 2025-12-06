<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand']);

        // Search by product name or SKU
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%$search%")
                    ->orWhere('sku', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by brand
        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->input('brand_id'));
        }

        // Filter by stock status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'in_stock') {
                $query->where('initial_stock', '>', 0);
            } elseif ($status === 'low_stock') {
                $query->whereColumn('initial_stock', '<=', 'minimum_stock');
            } elseif ($status === 'out_of_stock') {
                $query->where('initial_stock', '<=', 0);
            }
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();
        $filters = $request->only(['search', 'category_id', 'brand_id', 'status']);

        return view('products.index', compact('products', 'categories', 'brands', 'filters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit' => 'nullable|string',
            'base_unit' => 'nullable|string|max:20',
            'purchase_unit' => 'nullable|string|max:20',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'allow_decimal_sales' => 'nullable|boolean',
            'initial_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'product_image' => 'nullable|image|max:2048',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'vendor_product_code' => 'nullable|string|max:255',
            'auto_generate_vendor_code' => 'nullable|boolean',
        ]);

        // If supplier is selected, require either manual vendor code or auto-generate
        if ($request->filled('supplier_id')) {
            $hasManualCode = $request->filled('vendor_product_code');
            $autoGenerate = $request->boolean('auto_generate_vendor_code');

            if (! $hasManualCode && ! $autoGenerate) {
                return back()->withErrors([
                    'vendor_product_code' => 'Please enter a vendor product code or check "Auto-generate vendor code".',
                ])->withInput();
            }
        }

        // Set defaults for unit conversion fields
        $validated['base_unit'] = $validated['base_unit'] ?? $validated['unit'] ?? 'pcs';
        $validated['conversion_factor'] = $validated['conversion_factor'] ?? 1;
        $validated['allow_decimal_sales'] = $request->boolean('allow_decimal_sales');

        if ($request->hasFile('product_image')) {
            $validated['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // If supplier_id is provided, create the product-supplier relationship
        if ($request->filled('supplier_id')) {
            $supplier = Supplier::find($request->supplier_id);

            // Auto-generate vendor code if checkbox is checked, otherwise use manual entry
            $vendorCode = $request->boolean('auto_generate_vendor_code')
                ? VendorCodeController::generateVendorCode($supplier, $product)
                : $request->vendor_product_code;

            $product->suppliers()->attach($request->supplier_id, [
                'vendor_product_code' => $vendorCode,
                'is_preferred' => false,
                'lead_time_days' => null,
            ]);
        }

        // Handle AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'product' => [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'vendor_product_code' => $request->vendor_product_code,
                ],
            ]);
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,'.$product->id,
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit' => 'nullable|string',
            'base_unit' => 'nullable|string|max:20',
            'purchase_unit' => 'nullable|string|max:20',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'allow_decimal_sales' => 'nullable|boolean',
            'initial_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'product_image' => 'nullable|image|max:2048',
        ]);

        // Handle unit conversion fields
        $validated['allow_decimal_sales'] = $request->boolean('allow_decimal_sales');
        if (isset($validated['conversion_factor'])) {
            $validated['conversion_factor'] = (float) $validated['conversion_factor'];
        }

        if ($request->hasFile('product_image')) {
            $validated['product_image'] = $request->file('product_image')->store('products', 'public');
        }
        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
