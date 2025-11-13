<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('edit employees');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee');

        return [
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('employees', 'user_id')->ignore($employeeId)],
            'hire_date' => ['required', 'date'],
            'termination_date' => ['nullable', 'date', 'after_or_equal:hire_date'],
            'employment_type' => ['required', 'in:hourly,salaried'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0', 'max:999999.99', 'required_if:employment_type,hourly'],
            'base_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999.99', 'required_if:employment_type,salaried'],
            'pay_frequency' => ['required', 'in:weekly,biweekly,monthly'],
            'department' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'epf_number' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:active,terminated,suspended'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom error messages for validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'user_id.unique' => 'This user is already assigned to another employee.',
            'hire_date.required' => 'Hire date is required.',
            'hire_date.date' => 'Hire date must be a valid date.',
            'termination_date.date' => 'Termination date must be a valid date.',
            'termination_date.after_or_equal' => 'Termination date must be on or after hire date.',
            'employment_type.required' => 'Employment type is required.',
            'employment_type.in' => 'Employment type must be either hourly or salaried.',
            'hourly_rate.required_if' => 'Hourly rate is required for hourly employees.',
            'hourly_rate.numeric' => 'Hourly rate must be a valid number.',
            'hourly_rate.min' => 'Hourly rate cannot be negative.',
            'hourly_rate.max' => 'Hourly rate is too large.',
            'base_salary.required_if' => 'Base salary is required for salaried employees.',
            'base_salary.numeric' => 'Base salary must be a valid number.',
            'base_salary.min' => 'Base salary cannot be negative.',
            'base_salary.max' => 'Base salary is too large.',
            'pay_frequency.required' => 'Pay frequency is required.',
            'pay_frequency.in' => 'Pay frequency must be weekly, biweekly, or monthly.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active, terminated, or suspended.',
        ];
    }
}
