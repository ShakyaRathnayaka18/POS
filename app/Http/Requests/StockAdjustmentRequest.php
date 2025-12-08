<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // For now, allow all authenticated users. Update with proper permission later
        return true;
    }

    public function rules(): array
    {
        return [
            'stock_id' => 'required|exists:stocks,id',
            'type' => 'required|in:increase,decrease',
            'quantity_adjusted' => 'required|numeric|min:0.0001|max:99999.9999',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'adjustment_date' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'stock_id.required' => 'Stock selection is required',
            'stock_id.exists' => 'Selected stock does not exist',
            'type.required' => 'Adjustment type is required',
            'type.in' => 'Adjustment type must be increase or decrease',
            'quantity_adjusted.required' => 'Adjustment quantity is required',
            'quantity_adjusted.numeric' => 'Quantity must be a number',
            'quantity_adjusted.min' => 'Quantity must be greater than 0',
            'quantity_adjusted.max' => 'Quantity cannot exceed 99999.9999',
            'reason.required' => 'Adjustment reason is required',
        ];
    }
}
