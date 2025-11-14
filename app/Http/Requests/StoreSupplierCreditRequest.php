<?php

namespace App\Http\Requests;

use App\Enums\CreditTermsEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierCreditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'good_receive_note_id' => ['required', 'exists:good_receive_notes,id'],
            'invoice_number' => ['nullable', 'string', 'max:255'],
            'invoice_date' => ['required', 'date'],
            'credit_terms' => ['required', Rule::enum(CreditTermsEnum::class)],
            'original_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'good_receive_note_id.required' => 'GRN reference is required.',
            'good_receive_note_id.exists' => 'The selected GRN does not exist.',
            'invoice_date.required' => 'Invoice date is required.',
            'invoice_date.date' => 'Invoice date must be a valid date.',
            'credit_terms.required' => 'Please select credit terms.',
            'original_amount.required' => 'Amount is required.',
            'original_amount.numeric' => 'Amount must be a number.',
            'original_amount.min' => 'Amount must be greater than zero.',
        ];
    }
}
