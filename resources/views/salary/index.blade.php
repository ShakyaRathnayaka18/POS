@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8 px-4">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Add Employee Salary</h2>
            <p class="text-gray-600 mt-1">Fill in the details to calculate and save salary</p>
        </div>
        <a href="{{ route('salary.show') }}" class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white px-5 py-2.5 rounded-lg transition-colors shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Salary List
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white shadow-lg rounded-xl overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h3 class="text-white text-lg font-semibold">Salary Information</h3>
        </div>

        <form action="{{ route('salary.create') }}" method="POST" class="p-6" id="salaryForm">
            @csrf

            <!-- Employee Selection Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center mr-2 text-sm">1</span>
                    Employee Details
                </h4>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Cashier <span class="text-red-500">*</span>
                    </label>
                    <select name="user_id" id="user_id" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all">
                        <option value="" selected disabled>-- Choose a cashier --</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" data-ot-hours="{{ $user->ot_hours ?? 0 }}" data-user-name="{{ $user->name }}">
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Salary Components Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center mr-2 text-sm">2</span>
                    Salary Components
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Salary -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                </svg>
                                Basic Salary
                            </span>
                        </label>
                        <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary', $basicSalary) }}" step="0.01" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                    </div>

                    <!-- EPF Rate -->
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <label for="epf_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            EPF Rate (%)
                        </label>
                        <input type="number" name="epf_rate" id="epf_rate" value="{{ old('epf_rate', $epfRate) }}" step="0.01" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <p class="text-xs text-gray-500 mt-1">Default: 8%</p>
                    </div>

                    <!-- ETF Rate -->
                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <label for="etf_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            ETF Rate (%)
                        </label>
                        <input type="number" name="etf_rate" id="etf_rate" value="{{ old('etf_rate', $etfRate) }}" step="0.01" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all">
                        <p class="text-xs text-gray-500 mt-1">Default: 3%</p>
                    </div>
                </div>
            </div>

            <!-- Overtime Section -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center mr-2 text-sm">3</span>
                    Overtime Details
                </h4>

                <!-- OT Message (when no OT) -->
                <div id="ot_message_div" style="display: none;" class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <p id="ot_message" class="text-sm text-yellow-800 font-medium"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- OT Hours -->
                    <div id="ot_hours_div" class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <label for="ot_hours" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                OT Hours
                            </span>
                        </label>
                        <input type="text" name="ot_hours" id="ot_hours" value="{{ old('ot_hours', $otHours) }}" class="block w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg shadow-sm cursor-not-allowed" readonly>
                    </div>

                    <!-- OT Rate -->
                    <div id="ot_rate_div" class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                        <label for="ot_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            OT Rate (per hour)
                        </label>
                        <input type="number" name="ot_rate" id="ot_rate" value="{{ old('ot_rate', $otRate) }}" step="0.01" class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all">
                    </div>
                </div>
            </div>

            <!-- Total Salary Section -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                    <span class="bg-green-100 text-green-700 rounded-full w-8 h-8 flex items-center justify-center mr-2 text-sm">4</span>
                    Final Calculation
                </h4>
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-6 rounded-lg border-2 border-green-300">
                    <label for="total_salary" class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="flex items-center text-lg">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Total Salary (Net Pay)
                        </span>
                    </label>
                    <input type="text" name="total_salary" id="total_salary" value="{{ old('total_salary', $totalSalary) }}" class="block w-full px-4 py-4 text-2xl font-bold text-green-700 bg-white border-2 border-green-400 rounded-lg shadow-sm cursor-not-allowed" readonly>
                    <p class="text-xs text-gray-600 mt-2">Basic Salary + Overtime - EPF - ETF</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('salary.show') }}" class="inline-flex justify-center items-center bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg transition-colors">
                    Cancel
                </a>
                <button type="button" id="saveSalaryBtn" class="inline-flex justify-center items-center bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg transition-colors shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Salary
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmationModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-t-2xl px-6 py-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-white rounded-full p-2">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="ml-3 text-xl font-bold text-white">Confirm Salary Submission</h3>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="px-6 py-6">
            <p class="text-gray-700 text-base mb-4">
                Are you sure you want to save this salary information?
            </p>

            <!-- Salary Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 space-y-2">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Employee:</span>
                    <span class="text-sm font-semibold text-gray-800" id="modal_employee_name">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Basic Salary:</span>
                    <span class="text-sm font-semibold text-gray-800" id="modal_basic_salary">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">OT Amount:</span>
                    <span class="text-sm font-semibold text-gray-800" id="modal_ot_amount">-</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                    <span class="text-base font-medium text-gray-700">Total Salary:</span>
                    <span class="text-lg font-bold text-green-600" id="modal_total_salary">-</span>
                </div>
            </div>

            <div class="flex items-start bg-blue-50 border-l-4 border-blue-400 p-3 rounded">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <p class="text-xs text-blue-800">Please review the information carefully before confirming. This action will save the salary record.</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="bg-gray-50 px-6 py-4 rounded-b-2xl flex flex-col sm:flex-row gap-3 justify-end">
            <button type="button" id="cancelBtn" class="inline-flex justify-center items-center px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                Cancel
            </button>
            <button type="button" id="confirmBtn" class="inline-flex justify-center items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Yes, Save Salary
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Function to handle OT visibility and message based on selected user
    function handleOTVisibility() {
        const select = document.getElementById('user_id');
        const otHoursDiv = document.getElementById('ot_hours_div');
        const otRateDiv = document.getElementById('ot_rate_div');
        const otMessageDiv = document.getElementById('ot_message_div');
        const otMessage = document.getElementById('ot_message');
        const otHoursInput = document.getElementById('ot_hours');

        if (select.selectedIndex === 0) { // No user selected
            otHoursDiv.style.display = 'block';
            otRateDiv.style.display = 'block';
            otMessageDiv.style.display = 'none';
            otHoursInput.value = 0;
            calculateTotalSalary();
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const otHours = parseFloat(selectedOption.dataset.otHours) || 0;
        const userName = selectedOption.dataset.userName || 'Unknown';

        if (otHours > 0) {
            // Show OT fields
            otHoursDiv.style.display = 'block';
            otRateDiv.style.display = 'block';
            otMessageDiv.style.display = 'none';
            otHoursInput.value = otHours;
        } else {
            // Hide OT fields and show message
            otHoursDiv.style.display = 'none';
            otRateDiv.style.display = 'none';
            otMessageDiv.style.display = 'block';
            otMessage.textContent = `${userName} did not perform any overtime yet, so these fields are not applicable.`;
            otHoursInput.value = 0; // Ensure 0 for calculation
        }
        calculateTotalSalary(); // Recalculate after changes
    }

    // Calculate Total Salary Dynamically
    function calculateTotalSalary() {
        let basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
        let epfRate = parseFloat(document.getElementById('epf_rate').value) || 8; // Default 8%
        let etfRate = parseFloat(document.getElementById('etf_rate').value) || 3; // Default 3%
        let otRate = parseFloat(document.getElementById('ot_rate').value) || 2000; // Default OT Rate
        let otHours = parseFloat(document.getElementById('ot_hours').value) || 0;

        // EPF and ETF Calculations
        let epf = basicSalary * (epfRate / 100);
        let etf = basicSalary * (etfRate / 100);

        // OT Amount Calculation (will be 0 if hidden/no OT)
        let otAmount = otHours * otRate;

        // Total Salary Calculation (Basic Salary + OT - EPF - ETF)
        let totalSalary = basicSalary + otAmount - epf - etf;
        document.getElementById('total_salary').value = totalSalary.toFixed(2);
    }

    // Format currency for display
    function formatCurrency(amount) {
        return 'Rs. ' + parseFloat(amount).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    }

    // Show confirmation modal
    function showConfirmationModal() {
        const select = document.getElementById('user_id');

        // Validate user selection
        if (select.selectedIndex === 0) {
            alert('Please select a cashier before saving.');
            return;
        }

        const selectedOption = select.options[select.selectedIndex];
        const userName = selectedOption.dataset.userName || 'Unknown';
        const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
        const otRate = parseFloat(document.getElementById('ot_rate').value) || 2000;
        const otHours = parseFloat(document.getElementById('ot_hours').value) || 0;
        const otAmount = otHours * otRate;
        const totalSalary = document.getElementById('total_salary').value;

        // Update modal content
        document.getElementById('modal_employee_name').textContent = userName;
        document.getElementById('modal_basic_salary').textContent = formatCurrency(basicSalary);
        document.getElementById('modal_ot_amount').textContent = formatCurrency(otAmount);
        document.getElementById('modal_total_salary').textContent = formatCurrency(totalSalary);

        // Show modal
        document.getElementById('confirmationModal').classList.remove('hidden');
    }

    // Hide confirmation modal
    function hideConfirmationModal() {
        document.getElementById('confirmationModal').classList.add('hidden');
    }

    // Trigger OT visibility on user selection
    document.getElementById('user_id').addEventListener('change', handleOTVisibility);

    // Trigger calculation when editable fields change
    document.getElementById('epf_rate').addEventListener('input', calculateTotalSalary);
    document.getElementById('etf_rate').addEventListener('input', calculateTotalSalary);
    document.getElementById('ot_rate').addEventListener('input', calculateTotalSalary);
    document.getElementById('basic_salary').addEventListener('input', calculateTotalSalary);

    // Save Salary Button - Show Modal
    document.getElementById('saveSalaryBtn').addEventListener('click', showConfirmationModal);

    // Cancel Button - Hide Modal
    document.getElementById('cancelBtn').addEventListener('click', hideConfirmationModal);

    // Confirm Button - Submit Form
    document.getElementById('confirmBtn').addEventListener('click', function() {
        document.getElementById('salaryForm').submit();
    });

    // Close modal when clicking outside
    document.getElementById('confirmationModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideConfirmationModal();
        }
    });

    // Initial setup on page load
    window.addEventListener('load', function() {
        handleOTVisibility(); // Initial call (will show fields with 0 OT)
        calculateTotalSalary();
    });
</script>
@endpush