<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    /**
     * Display a listing of employees.
     */
    public function index(Request $request): View
    {
        $query = Employee::with('user')
            ->orderBy('employee_number');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        // Search by name or employee number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $employees = $query->paginate(15);
        $departments = $this->employeeService->getDepartments();

        return view('employees.index', compact('employees', 'departments'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create(): View
    {
        $users = User::whereDoesntHave('employee')->get();
        $departments = $this->employeeService->getDepartments();
        $positions = $this->employeeService->getPositions();

        return view('employees.create', compact('users', 'departments', 'positions'));
    }

    /**
     * Store a newly created employee.
     */
    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());

            return redirect()
                ->route('employees.show', $employee)
                ->with('success', 'Employee created successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create employee: '.$e->getMessage());
        }
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): View
    {
        $employee->load(['user', 'payrollEntries' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee): View
    {
        $users = User::whereDoesntHave('employee')
            ->orWhere('id', $employee->user_id)
            ->get();
        $departments = $this->employeeService->getDepartments();
        $positions = $this->employeeService->getPositions();

        return view('employees.edit', compact('employee', 'users', 'departments', 'positions'));
    }

    /**
     * Update the specified employee.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        try {
            $this->employeeService->updateEmployee($employee->id, $request->validated());

            return redirect()
                ->route('employees.show', $employee)
                ->with('success', 'Employee updated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update employee: '.$e->getMessage());
        }
    }

    /**
     * Remove the specified employee (soft delete).
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            if ($employee->status === 'active') {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete an active employee. Please terminate first.');
            }

            $employee->delete();

            return redirect()
                ->route('employees.index')
                ->with('success', 'Employee deleted successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete employee: '.$e->getMessage());
        }
    }

    /**
     * Terminate an employee.
     */
    public function terminate(Request $request, Employee $employee): RedirectResponse
    {
        $request->validate([
            'termination_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        try {
            $this->employeeService->terminateEmployee(
                $employee->id,
                $request->termination_date,
                $request->notes
            );

            return redirect()
                ->route('employees.show', $employee)
                ->with('success', 'Employee terminated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to terminate employee: '.$e->getMessage());
        }
    }

    /**
     * Reactivate a terminated employee.
     */
    public function reactivate(Employee $employee): RedirectResponse
    {
        try {
            $this->employeeService->reactivateEmployee($employee->id);

            return redirect()
                ->route('employees.show', $employee)
                ->with('success', 'Employee reactivated successfully.');
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to reactivate employee: '.$e->getMessage());
        }
    }
}
