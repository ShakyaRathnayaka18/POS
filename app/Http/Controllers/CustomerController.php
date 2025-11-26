<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerCredit;
use App\Models\CustomerPayment;
use App\Services\CustomerCreditService;
use App\Services\CustomerPaymentService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerCreditService $customerCreditService,
        protected CustomerPaymentService $customerPaymentService
    ) {}

    /**
     * Display the consolidated customer management view
     */
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'customers');

        $customers = Customer::with(['customerCredits' => function ($query) {
            $query->whereNotIn('status', ['paid']);
        }])->latest()->get();

        $credits = CustomerCredit::with(['customer', 'sale'])
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        $payments = CustomerPayment::with(['customer', 'customerCredit'])
            ->when($request->customer_id, fn ($q) => $q->where('customer_id', $request->customer_id))
            ->latest()
            ->paginate(20);

        $unpaidCredits = CustomerCredit::whereNotIn('status', ['paid'])
            ->with('customer')
            ->orderBy('due_date')
            ->get();

        return view('customer-credits.index', compact('customers', 'credits', 'payments', 'unpaidCredits', 'tab'));
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['customer_number'] = Customer::generateCustomerNumber();
        $validated['is_active'] = true;

        $customer = Customer::create($validated);

        return redirect()->route('customers.index', ['tab' => 'customers'])
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Update the specified customer
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'credit_limit' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $customer->update($validated);

        return redirect()->route('customers.index', ['tab' => 'customers'])
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer
     */
    public function destroy(Customer $customer)
    {
        if ($customer->customerCredits()->whereNotIn('status', ['paid'])->exists()) {
            return redirect()->route('customers.index', ['tab' => 'customers'])
                ->with('error', 'Cannot delete customer with outstanding credits.');
        }

        $customer->delete();

        return redirect()->route('customers.index', ['tab' => 'customers'])
            ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Process a payment against a customer credit
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'customer_credit_id' => ['required', 'exists:customer_credits,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'in:cash,bank_transfer,check,card'],
            'payment_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->customerPaymentService->processPayment($validated);

            return redirect()->route('customers.index', ['tab' => 'payments'])
                ->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('customers.index', ['tab' => 'payments'])
                ->with('error', $e->getMessage());
        }
    }
}
