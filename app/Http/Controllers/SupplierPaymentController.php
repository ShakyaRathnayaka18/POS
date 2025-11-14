<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethodEnum;
use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Models\SupplierCredit;
use App\Models\SupplierPayment;
use App\Services\SupplierPaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierPaymentController extends Controller
{
    public function __construct(protected SupplierPaymentService $paymentService) {}

    public function index(Request $request): View
    {
        $query = SupplierPayment::with(['supplier', 'supplierCredit', 'processedBy']);

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('from_date')) {
            $query->where('payment_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->where('payment_date', '<=', $request->to_date);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        return view('supplier-payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $credit = null;

        if ($request->filled('credit_id')) {
            $credit = SupplierCredit::with('supplier')->findOrFail($request->credit_id);
        }

        $paymentMethods = PaymentMethodEnum::cases();

        return view('supplier-payments.create', compact('credit', 'paymentMethods'));
    }

    public function store(StoreSupplierPaymentRequest $request)
    {
        try {
            $payment = $this->paymentService->processPayment($request->validated());

            return redirect()
                ->route('supplier-credits.show', $payment->supplier_credit_id)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Error processing payment: '.$e->getMessage());
        }
    }

    public function show(SupplierPayment $supplierPayment): View
    {
        $supplierPayment->load(['supplier', 'supplierCredit', 'processedBy']);

        return view('supplier-payments.show', compact('supplierPayment'));
    }
}
