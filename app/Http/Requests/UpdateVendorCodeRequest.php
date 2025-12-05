<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorCodeRequest extends FormRequest
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
            'vendor_product_code' => [
                'required',
                'string',
                'max:255',
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
            'vendor_product_code.required' => 'Vendor product code is required.',
            'vendor_product_code.max' => 'Vendor product code cannot exceed 255 characters.',
            'lead_time_days.integer' => 'Lead time must be a whole number.',
            'lead_time_days.min' => 'Lead time cannot be negative.',
        ];
    }
}
