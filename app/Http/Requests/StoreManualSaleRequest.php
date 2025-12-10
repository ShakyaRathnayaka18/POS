<?php

namespace App\Http\Requests;

use App\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'amount_received' => ['nullable', 'numeric', 'min:0'],

            // Manual items (no product_id, manually entered)
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.entered_barcode' => ['nullable', 'string', 'max:255'],
            'items.*.tax' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Discount fields (optional)
            'items.*.discount' => ['nullable', 'array'],
            'items.*.discount.type' => ['required_with:items.*.discount', Rule::in(['percentage', 'fixed_amount', 'none'])],
            'items.*.discount.value' => ['required_with:items.*.discount', 'numeric', 'min:0'],
            'items.*.discount.amount' => ['required_with:items.*.discount', 'numeric', 'min:0'],
            'items.*.discount.final_price' => ['required_with:items.*.discount', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'items.required' => 'Cart cannot be empty.',
            'items.min' => 'Cart must contain at least one item.',
            'items.*.product_name.required' => 'Product name is required for each item.',
            'items.*.price.required' => 'Price is required for each item.',
            'items.*.price.numeric' => 'Price must be a valid number.',
            'items.*.price.min' => 'Price must be greater than or equal to 0.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.numeric' => 'Quantity must be a valid number.',
            'items.*.quantity.min' => 'Quantity must be at least 0.01.',
        ];
    }
}
