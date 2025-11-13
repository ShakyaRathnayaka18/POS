@extends('layouts.app')

@section('title', 'Edit Employee')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                <i class="fas fa-arrow-left mr-2"></i>Back to Employees
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                Edit Employee: {{ $employee->employee_number }}
            </h2>

            <form action="{{ route('employees.update', $employee) }}" method="POST" x-data="employeeForm()">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Link to User (Optional)
                            </label>
                            <select
                                name="user_id"
                                id="user_id"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('user_id') border-red-500 @enderror">
                                <option value="">-- No User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $employee->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Hire Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                name="hire_date"
                                id="hire_date"
                                value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('hire_date') border-red-500 @enderror">
                            @error('hire_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="termination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Termination Date
                            </label>
                            <input
                                type="date"
                                name="termination_date"
                                id="termination_date"
                                value="{{ old('termination_date', $employee->termination_date?->format('Y-m-d')) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('termination_date') border-red-500 @enderror">
                            @error('termination_date')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="status"
                                id="status"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                <option value="suspended" {{ old('status', $employee->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Department
                            </label>
                            <input
                                type="text"
                                name="department"
                                id="department"
                                value="{{ old('department', $employee->department) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('department') border-red-500 @enderror">
                            @error('department')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Position
                            </label>
                            <input
                                type="text"
                                name="position"
                                id="position"
                                value="{{ old('position', $employee->position) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('position') border-red-500 @enderror">
                            @error('position')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Employment Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Employment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Employment Type <span class="text-red-500">*</span>
                            </label>
                            <select
                                name="employment_type"
                                id="employment_type"
                                x-model="employmentType"
                                required
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('employment_type') border-red-500 @enderror">
                                <option value="">-- Select Type --</option>
                                <option value="hourly" {{ old('employment_type', $employee->employment_type) == 'hourly' ? 'selected' : '' }}>Hourly</option>
                                <option value="salaried" {{ old('employment_type', $employee->employment_type) == 'salaried' ? 'selected' : '' }}>Salaried</option>
                            </select>
                            @error('employment_type')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="employmentType === 'hourly'">
                            <label for="hourly_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Hourly Rate (LKR) <span class="text-red-500" x-show="employmentType === 'hourly'">*</span>
                            </label>
                            <input
                                type="number"
                                name="hourly_rate"
                                id="hourly_rate"
                                value="{{ old('hourly_rate', $employee->hourly_rate) }}"
                                step="0.01"
                                :required="employmentType === 'hourly'"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('hourly_rate') border-red-500 @enderror">
                            @error('hourly_rate')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="employmentType === 'salaried'">
                            <label for="base_salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Base Salary (LKR) <span class="text-red-500" x-show="employmentType === 'salaried'">*</span>
                            </label>
                            <input
                                type="number"
                                name="base_salary"
                                id="base_salary"
                                value="{{ old('base_salary', $employee->base_salary) }}"
                                step="0.01"
                                :required="employmentType === 'salaried'"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('base_salary') border-red-500 @enderror">
                            @error('base_salary')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="employmentType === 'salaried'">
                            <label for="pay_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Pay Frequency
                            </label>
                            <select
                                name="pay_frequency"
                                id="pay_frequency"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('pay_frequency') border-red-500 @enderror">
                                <option value="monthly" {{ old('pay_frequency', $employee->pay_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="biweekly" {{ old('pay_frequency', $employee->pay_frequency) == 'biweekly' ? 'selected' : '' }}>Biweekly</option>
                                <option value="weekly" {{ old('pay_frequency', $employee->pay_frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                            </select>
                            @error('pay_frequency')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- EPF and Banking -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">EPF & Banking Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="epf_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                EPF Number
                            </label>
                            <input
                                type="text"
                                name="epf_number"
                                id="epf_number"
                                value="{{ old('epf_number', $employee->epf_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('epf_number') border-red-500 @enderror">
                            @error('epf_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Bank Name
                            </label>
                            <input
                                type="text"
                                name="bank_name"
                                id="bank_name"
                                value="{{ old('bank_name', $employee->bank_name) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('bank_name') border-red-500 @enderror">
                            @error('bank_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_account_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Bank Account Number
                            </label>
                            <input
                                type="text"
                                name="bank_account_number"
                                id="bank_account_number"
                                value="{{ old('bank_account_number', $employee->bank_account_number) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('bank_account_number') border-red-500 @enderror">
                            @error('bank_account_number')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Bank Branch
                            </label>
                            <input
                                type="text"
                                name="bank_branch"
                                id="bank_branch"
                                value="{{ old('bank_branch', $employee->bank_branch) }}"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('bank_branch') border-red-500 @enderror">
                            @error('bank_branch')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Notes
                    </label>
                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('notes') border-red-500 @enderror">{{ old('notes', $employee->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Update Employee
                    </button>
                    <a href="{{ route('employees.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function employeeForm() {
        return {
            employmentType: '{{ old('employment_type', $employee->employment_type) }}'
        }
    }
</script>
@endsection
