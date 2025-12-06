<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\GoodReceiveNote;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSearchController extends Controller
{
    /**
     * Perform a global search across multiple entities.
     */
    public function search(Request $request): JsonResponse
    {
        $query = trim($request->get('q', ''));

        // Require at least 2 characters
        if (strlen($query) < 2) {
            return response()->json([
                'query' => $query,
                'results' => [],
                'total' => 0,
            ]);
        }

        $limit = 10; // Max results per category
        $results = [];
        $user = auth()->user();

        // Search each entity based on user permissions
        if ($user->can('view products')) {
            $results['products'] = $this->searchProducts($query, $limit);
        }

        if ($user->can('view customers')) {
            $results['customers'] = $this->searchCustomers($query, $limit);
        }

        if ($user->can('view suppliers')) {
            $results['suppliers'] = $this->searchSuppliers($query, $limit);
        }

        if ($user->can('view sales')) {
            $results['sales'] = $this->searchSales($query, $limit);
        }

        if ($user->can('view stocks')) {
            $results['stocks'] = $this->searchStocks($query, $limit);
        }

        if ($user->can('view batches')) {
            $results['batches'] = $this->searchBatches($query, $limit);
        }

        if ($user->can('view grns')) {
            $results['grns'] = $this->searchGrns($query, $limit);
        }

        if ($user->can('view employees')) {
            $results['employees'] = $this->searchEmployees($query, $limit);
        }

        if ($user->can('view vendor codes')) {
            $results['vendor_codes'] = $this->searchVendorCodes($query, $limit);
        }

        // Calculate total results
        $total = collect($results)->flatten(1)->count();

        return response()->json([
            'query' => $query,
            'results' => $results,
            'total' => $total,
        ]);
    }

    /**
     * Search products by name, SKU, description, brand, category, or supplier.
     */
    private function searchProducts(string $query, int $limit): array
    {
        return Product::with(['category', 'brand', 'suppliers'])
            ->where(function ($q) use ($query) {
                $q->where('product_name', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhereHas('brand', function ($brandQuery) use ($query) {
                        $brandQuery->where('brand_name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('category', function ($categoryQuery) use ($query) {
                        $categoryQuery->where('cat_name', 'like', "%{$query}%");
                    })
                    ->orWhereHas('suppliers', function ($supplierQuery) use ($query) {
                        $supplierQuery->where('company_name', 'like', "%{$query}%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'type' => 'product',
                    'title' => $product->product_name,
                    'subtitle' => $product->sku,
                    'description' => $product->description ? substr($product->description, 0, 100) : null,
                    'meta' => [
                        'Category' => $product->category?->cat_name ?? 'N/A',
                        'Brand' => $product->brand?->brand_name ?? 'N/A',
                        'Stock' => $product->initial_stock ?? 0,
                        'Unit' => $product->unit ?? 'pcs',
                    ],
                    'url' => route('products.index', ['search' => $product->sku]),
                    'icon' => 'fa-box',
                    'color' => 'blue',
                ];
            })
            ->toArray();
    }

    /**
     * Search customers by name, email, phone, or customer number.
     */
    private function searchCustomers(string $query, int $limit): array
    {
        return Customer::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('mobile', 'like', "%{$query}%")
                ->orWhere('customer_number', 'like', "%{$query}%");
        })
            ->limit($limit)
            ->get()
            ->map(function ($customer) {
                return [
                    'id' => $customer->id,
                    'type' => 'customer',
                    'title' => $customer->name,
                    'subtitle' => $customer->customer_number ?? $customer->email,
                    'description' => $customer->phone ?? $customer->mobile,
                    'meta' => [
                        'Email' => $customer->email ?? 'N/A',
                        'Phone' => $customer->phone ?? $customer->mobile ?? 'N/A',
                        'Credit Limit' => number_format($customer->credit_limit ?? 0, 2),
                    ],
                    'url' => route('customers.index', ['search' => $customer->customer_number ?? $customer->name]),
                    'icon' => 'fa-user',
                    'color' => 'green',
                ];
            })
            ->toArray();
    }

    /**
     * Search suppliers by company name, contact person, or phone.
     */
    private function searchSuppliers(string $query, int $limit): array
    {
        return Supplier::where(function ($q) use ($query) {
            $q->where('company_name', 'like', "%{$query}%")
                ->orWhere('contact_person', 'like', "%{$query}%")
                ->orWhere('phone', 'like', "%{$query}%")
                ->orWhere('mobile', 'like', "%{$query}%");
        })
            ->limit($limit)
            ->get()
            ->map(function ($supplier) {
                return [
                    'id' => $supplier->id,
                    'type' => 'supplier',
                    'title' => $supplier->company_name,
                    'subtitle' => $supplier->contact_person,
                    'description' => $supplier->business_type,
                    'meta' => [
                        'Contact' => $supplier->contact_person ?? 'N/A',
                        'Phone' => $supplier->phone ?? $supplier->mobile ?? 'N/A',
                        'Credit Limit' => number_format($supplier->credit_limit ?? 0, 2),
                        'Credit Used' => number_format($supplier->current_credit_used ?? 0, 2),
                    ],
                    'url' => route('suppliers.show', $supplier->id),
                    'icon' => 'fa-truck',
                    'color' => 'purple',
                ];
            })
            ->toArray();
    }

    /**
     * Search sales by sale number, customer name, or phone.
     */
    private function searchSales(string $query, int $limit): array
    {
        return Sale::with('user')
            ->where(function ($q) use ($query) {
                $q->where('sale_number', 'like', "%{$query}%")
                    ->orWhere('customer_name', 'like', "%{$query}%")
                    ->orWhere('customer_phone', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'type' => 'sale',
                    'title' => $sale->sale_number,
                    'subtitle' => $sale->customer_name ?? 'Walk-in Customer',
                    'description' => $sale->created_at->format('M d, Y h:i A'),
                    'meta' => [
                        'Total' => number_format($sale->total_amount ?? 0, 2),
                        'Payment' => ucfirst($sale->payment_method ?? 'N/A'),
                        'Cashier' => $sale->user?->name ?? 'N/A',
                        'Date' => $sale->created_at->format('M d, Y'),
                    ],
                    'url' => route('sales.show', $sale->id),
                    'icon' => 'fa-receipt',
                    'color' => 'yellow',
                ];
            })
            ->toArray();
    }

    /**
     * Search stocks by product name/SKU or batch number.
     */
    private function searchStocks(string $query, int $limit): array
    {
        return Stock::with(['product', 'batch'])
            ->where(function ($q) use ($query) {
                $q->whereHas('product', function ($productQuery) use ($query) {
                    $productQuery->where('product_name', 'like', "%{$query}%")
                        ->orWhere('sku', 'like', "%{$query}%");
                })
                    ->orWhereHas('batch', function ($batchQuery) use ($query) {
                        $batchQuery->where('batch_number', 'like', "%{$query}%")
                            ->orWhere('barcode', 'like', "%{$query}%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'type' => 'stock',
                    'title' => $stock->product?->product_name ?? 'Unknown Product',
                    'subtitle' => $stock->batch?->batch_number ?? 'No Batch',
                    'description' => "Qty: {$stock->quantity} | Cost: ".number_format($stock->cost_price ?? 0, 2),
                    'meta' => [
                        'Product' => $stock->product?->product_name ?? 'N/A',
                        'SKU' => $stock->product?->sku ?? 'N/A',
                        'Batch' => $stock->batch?->batch_number ?? 'N/A',
                        'Quantity' => $stock->quantity ?? 0,
                        'Cost Price' => number_format($stock->cost_price ?? 0, 2),
                        'Selling Price' => number_format($stock->selling_price ?? 0, 2),
                    ],
                    'url' => route('stocks.show', $stock->id),
                    'icon' => 'fa-cubes',
                    'color' => 'indigo',
                ];
            })
            ->toArray();
    }

    /**
     * Search batches by batch number or barcode.
     */
    private function searchBatches(string $query, int $limit): array
    {
        return Batch::with('product')
            ->where(function ($q) use ($query) {
                $q->where('batch_number', 'like', "%{$query}%")
                    ->orWhere('barcode', 'like', "%{$query}%");
            })
            ->limit($limit)
            ->get()
            ->map(function ($batch) {
                return [
                    'id' => $batch->id,
                    'type' => 'batch',
                    'title' => $batch->batch_number,
                    'subtitle' => $batch->barcode ?? 'No Barcode',
                    'description' => $batch->product?->product_name,
                    'meta' => [
                        'Product' => $batch->product?->product_name ?? 'N/A',
                        'Barcode' => $batch->barcode ?? 'N/A',
                        'Expiry' => $batch->expiry_date?->format('M d, Y') ?? 'N/A',
                        'Mfg Date' => $batch->manufacturing_date?->format('M d, Y') ?? 'N/A',
                    ],
                    'url' => route('batches.show', $batch->id),
                    'icon' => 'fa-layer-group',
                    'color' => 'pink',
                ];
            })
            ->toArray();
    }

    /**
     * Search GRNs by GRN number or invoice number.
     */
    private function searchGrns(string $query, int $limit): array
    {
        return GoodReceiveNote::with('supplier')
            ->where(function ($q) use ($query) {
                $q->where('grn_number', 'like', "%{$query}%")
                    ->orWhere('invoice_number', 'like', "%{$query}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($query) {
                        $supplierQuery->where('company_name', 'like', "%{$query}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($grn) {
                return [
                    'id' => $grn->id,
                    'type' => 'grn',
                    'title' => $grn->grn_number,
                    'subtitle' => $grn->invoice_number ?? 'No Invoice',
                    'description' => $grn->supplier?->company_name,
                    'meta' => [
                        'Supplier' => $grn->supplier?->company_name ?? 'N/A',
                        'Invoice' => $grn->invoice_number ?? 'N/A',
                        'Total' => number_format($grn->total_amount ?? 0, 2),
                        'Date' => $grn->created_at?->format('M d, Y') ?? 'N/A',
                    ],
                    'url' => route('good-receive-notes.show', $grn->id),
                    'icon' => 'fa-file-invoice',
                    'color' => 'orange',
                ];
            })
            ->toArray();
    }

    /**
     * Search employees by employee number or name.
     */
    private function searchEmployees(string $query, int $limit): array
    {
        return Employee::with('user')
            ->where(function ($q) use ($query) {
                $q->where('employee_number', 'like', "%{$query}%")
                    ->orWhere('department', 'like', "%{$query}%")
                    ->orWhere('position', 'like', "%{$query}%")
                    ->orWhereHas('user', function ($userQuery) use ($query) {
                        $userQuery->where('name', 'like', "%{$query}%");
                    });
            })
            ->limit($limit)
            ->get()
            ->map(function ($employee) {
                return [
                    'id' => $employee->id,
                    'type' => 'employee',
                    'title' => $employee->user?->name ?? 'Unknown',
                    'subtitle' => $employee->employee_number,
                    'description' => $employee->position ?? $employee->department,
                    'meta' => [
                        'Employee #' => $employee->employee_number ?? 'N/A',
                        'Department' => $employee->department ?? 'N/A',
                        'Position' => $employee->position ?? 'N/A',
                        'Status' => ucfirst($employee->status ?? 'active'),
                    ],
                    'url' => route('employees.show', $employee->id),
                    'icon' => 'fa-id-badge',
                    'color' => 'teal',
                ];
            })
            ->toArray();
    }

    /**
     * Search vendor codes by vendor code, product, or supplier.
     */
    private function searchVendorCodes(string $query, int $limit): array
    {
        return DB::table('product_supplier')
            ->join('products', 'product_supplier.product_id', '=', 'products.id')
            ->join('suppliers', 'product_supplier.supplier_id', '=', 'suppliers.id')
            ->where(function ($q) use ($query) {
                $q->where('product_supplier.vendor_product_code', 'like', "%{$query}%")
                    ->orWhere('products.product_name', 'like', "%{$query}%")
                    ->orWhere('products.sku', 'like', "%{$query}%")
                    ->orWhere('suppliers.company_name', 'like', "%{$query}%");
            })
            ->select(
                'product_supplier.id',
                'product_supplier.vendor_product_code',
                'products.product_name',
                'products.sku',
                'suppliers.company_name'
            )
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'vendor_code',
                    'title' => $item->vendor_product_code,
                    'subtitle' => $item->product_name,
                    'description' => $item->company_name,
                    'meta' => [
                        'Vendor Code' => $item->vendor_product_code,
                        'Product' => $item->product_name,
                        'SKU' => $item->sku,
                        'Supplier' => $item->company_name,
                    ],
                    'url' => route('vendor-codes.index', ['search' => $item->vendor_product_code]),
                    'icon' => 'fa-barcode',
                    'color' => 'gray',
                ];
            })
            ->toArray();
    }
}
