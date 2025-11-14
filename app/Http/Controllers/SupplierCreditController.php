<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\SupplierCredit;
use App\Services\SupplierCreditService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierCreditController extends Controller
{
    public function __construct(protected SupplierCreditService $creditService) {}

    public function index(Request $request): View
    {
        $query = SupplierCredit::with(['supplier', 'goodReceiveNote', 'createdBy']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->where('invoice_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('invoice_date', '<=', $request->to_date);
        }

        $credits = $query->orderBy('created_at', 'desc')->paginate(15);

        $suppliers = Supplier::orderBy('company_name')->get();

        $totalOutstanding = SupplierCredit::whereNotIn('status', ['paid'])->sum('outstanding_amount');
        $overdueCount = SupplierCredit::overdue()->count();
        $dueSoonCount = SupplierCredit::dueSoon()->count();

        return view('supplier-credits.index', compact(
            'credits',
            'suppliers',
            'totalOutstanding',
            'overdueCount',
            'dueSoonCount'
        ));
    }

    public function show(SupplierCredit $supplierCredit): View
    {
        $supplierCredit->load(['supplier', 'goodReceiveNote', 'payments.processedBy', 'createdBy']);

        return view('supplier-credits.show', compact('supplierCredit'));
    }
}
