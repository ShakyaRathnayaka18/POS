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

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'common_category_id' => 'required|exists:categories,id',
            'common_brand_id' => 'nullable|exists:brands,id',
            'common_base_unit' => 'nullable|string|max:20',
            'common_purchase_unit' => 'nullable|string|max:20',
            'common_conversion_factor' => 'nullable|numeric|min:0.0001',
            'common_allow_decimal_sales' => 'nullable|boolean',
            'common_supplier_id' => 'nullable|exists:suppliers,id',
            'common_auto_generate_vendor_code' => 'nullable|boolean',
            'products' => 'required|array|min:1',
            'products.*.name' => 'required|string|max:255',
            'products.*.unit' => 'nullable|string',
            'products.*.initial_stock' => 'nullable|integer|min:0',
            'products.*.min_stock' => 'nullable|integer|min:0',
            'products.*.max_stock' => 'nullable|integer|min:0',
            'products.*.description' => 'nullable|string',
        ]);

        $count = 0;

        // Prepare common unit config
        $baseUnit = $request->common_base_unit ?? 'pcs';
        $purchaseUnit = $request->common_purchase_unit;
        $conversionFactor = $request->common_conversion_factor ?? 1;
        $allowDecimal = $request->boolean('common_allow_decimal_sales');

        $supplier = null;
        if ($request->filled('common_supplier_id')) {
            $supplier = Supplier::find($request->common_supplier_id);
        }

        foreach ($request->products as $productData) {
            if (empty($productData['name'])) continue;

            // Use common unit config, fallback to per-row unit if common is not set (legacy behavior support) but prioritize common
            // Actually, we should prioritize the common settings if they were part of the form submission.
            // Since we added them to the form, they will be present.

            // Note: If the user didn't touch the common unit dropdowns, they might default to 'pcs'.
            // The per-row 'unit' might differ. However, to keep it consistent with "Unit Configuration Unit Configuration" request, 
            // we apply the detailed config to all.

            $product = Product::create([
                'product_name' => $productData['name'],
                'category_id' => $request->common_category_id,
                'brand_id' => $request->common_brand_id,
                'unit' => $baseUnit, // Set primary unit to base unit
                'base_unit' => $baseUnit,
                'purchase_unit' => $purchaseUnit,
                'conversion_factor' => $conversionFactor,
                'allow_decimal_sales' => $allowDecimal,
                'initial_stock' => $productData['initial_stock'] ?? 0,
                'minimum_stock' => $productData['min_stock'] ?? 0,
                'maximum_stock' => $productData['max_stock'] ?? 0,
                'description' => $productData['description'] ?? null,
            ]);

            if ($supplier) {
                $vendorCode = null;
                if ($request->boolean('common_auto_generate_vendor_code')) {
                    // Check if method exists, otherwise assume null or handle gracefully
                    if (method_exists(\App\Http\Controllers\VendorCodeController::class, 'generateVendorCode')) {
                        $vendorCode = VendorCodeController::generateVendorCode($supplier, $product);
                    }
                }

                $product->suppliers()->attach($supplier->id, [
                    'vendor_product_code' => $vendorCode,
                    'is_preferred' => false,
                    'lead_time_days' => null,
                ]);
            }

            $count++;
        }

        return redirect()->route('products.index')->with('success', "$count products added successfully.");
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku,' . $product->id,
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
