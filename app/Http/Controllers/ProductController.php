<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
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

        $products = $query->get();
        $categories = Category::all();
        $brands = Brand::all();
        $filters = $request->only(['search', 'category_id', 'brand_id', 'status']);

        return view('products.index', compact('products', 'categories', 'brands', 'filters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products,sku',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit' => 'nullable|string',
            'initial_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'product_image' => 'nullable|image|max:2048',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'vendor_product_code' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('product_image')) {
            $validated['product_image'] = $request->file('product_image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // If supplier_id is provided, create the product-supplier relationship
        if ($request->filled('supplier_id')) {
            $product->suppliers()->attach($request->supplier_id, [
                'vendor_product_code' => $request->vendor_product_code ?? $product->sku,
                'vendor_cost_price' => 0,
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
                    'vendor_product_code' => $request->vendor_product_code ?? $product->sku,
                    'vendor_cost_price' => 0,
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
            'initial_stock' => 'nullable|integer',
            'minimum_stock' => 'nullable|integer',
            'maximum_stock' => 'nullable|integer',
            'product_image' => 'nullable|image|max:2048',
        ]);
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
