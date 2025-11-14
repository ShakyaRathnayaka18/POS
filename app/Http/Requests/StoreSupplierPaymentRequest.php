<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethodEnum;
use App\Models\SupplierCredit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_credit_id' => ['required', 'exists:supplier_credits,id'],
            'payment_date' => ['required', 'date'],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
                function ($attribute, $value, $fail) {
                    $credit = SupplierCredit::find($this->supplier_credit_id);
                    if ($credit && $value > $credit->outstanding_amount) {
                        $fail('Payment amount cannot exceed outstanding balance of LKR '.number_format($credit->outstanding_amount, 2));
                    }
                },
            ],
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_credit_id.required' => 'Please select a credit to pay.',
            'supplier_credit_id.exists' => 'The selected credit does not exist.',
            'payment_date.required' => 'Payment date is required.',
            'payment_date.date' => 'Payment date must be a valid date.',
            'amount.required' => 'Payment amount is required.',
            'amount.numeric' => 'Payment amount must be a number.',
            'amount.min' => 'Payment amount must be greater than zero.',
            'payment_method.required' => 'Please select a payment method.',
        ];
    }
}
