<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseCategoryRequest extends FormRequest
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
        $categoryId = $this->route('expenseCategory')?->id;

        return [
            'category_name' => [
                'required',
                'string',
                'max:255',
                'unique:expense_categories,category_name,'.$categoryId,
            ],
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'category_name.required' => 'Category name is required.',
            'category_name.unique' => 'This category name already exists.',
        ];
    }
}
