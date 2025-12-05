<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVendorCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'product_id' => 'required|exists:products,id',
            'vendor_product_code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('product_supplier')->where(function ($query) {
                    return $query->where('supplier_id', $this->supplier_id)
                        ->where('product_id', $this->product_id);
                }),
            ],
            'is_preferred' => 'nullable|boolean',
            'lead_time_days' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'supplier_id.required' => 'Please select a supplier.',
            'supplier_id.exists' => 'The selected supplier does not exist.',
            'product_id.required' => 'Please select a product.',
            'product_id.exists' => 'The selected product does not exist.',
            'vendor_product_code.required' => 'Vendor product code is required.',
            'vendor_product_code.unique' => 'This vendor code already exists for this supplier-product combination.',
            'lead_time_days.integer' => 'Lead time must be a whole number.',
            'lead_time_days.min' => 'Lead time cannot be negative.',
        ];
    }
}
