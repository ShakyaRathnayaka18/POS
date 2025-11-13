<?php

namespace App\Services;

use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    /**
     * Create a new employee.
     */
    public function createEmployee(array $data): Employee
    {
        return DB::transaction(function () use ($data) {
            // Generate employee number if not provided
            if (! isset($data['employee_number'])) {
                $data['employee_number'] = Employee::generateEmployeeNumber();
            }

            // Validate employment type has corresponding rate/salary
            $this->validateEmployeeData($data);

            return Employee::create($data);
        });
    }

    /**
     * Update an existing employee.
     */
    public function updateEmployee(int $employeeId, array $data): Employee
    {
        $employee = Employee::findOrFail($employeeId);

        return DB::transaction(function () use ($employee, $data) {
            // Validate employment type has corresponding rate/salary
            $this->validateEmployeeData($data);

            $employee->update($data);

            return $employee->fresh();
        });
    }

    /**
     * Terminate an employee.
     */
    public function terminateEmployee(int $employeeId, string $terminationDate, ?string $notes = null): Employee
    {
        $employee = Employee::findOrFail($employeeId);

        if ($employee->status === 'terminated') {
            throw new Exception('Employee is already terminated.');
        }

        return DB::transaction(function () use ($employee, $terminationDate, $notes) {
            $employee->update([
                'status' => 'terminated',
                'termination_date' => $terminationDate,
                'notes' => $notes ? ($employee->notes ? $employee->notes."\n\n".$notes : $notes) : $employee->notes,
            ]);

            return $employee->fresh();
        });
    }

    /**
     * Reactivate a terminated employee.
     */
    public function reactivateEmployee(int $employeeId): Employee
    {
        $employee = Employee::findOrFail($employeeId);

        if ($employee->status !== 'terminated') {
            throw new Exception('Only terminated employees can be reactivated.');
        }

        return DB::transaction(function () use ($employee) {
            $employee->update([
                'status' => 'active',
                'termination_date' => null,
            ]);

            return $employee->fresh();
        });
    }

    /**
     * Get employee by user ID.
     */
    public function getEmployeeByUserId(int $userId): ?Employee
    {
        return Employee::where('user_id', $userId)->first();
    }

    /**
     * Get all active employees.
     */
    public function getActiveEmployees()
    {
        return Employee::with('user')
            ->active()
            ->orderBy('employee_number')
            ->get();
    }

    /**
     * Get employees by department.
     */
    public function getEmployeesByDepartment(?string $department = null)
    {
        $query = Employee::with('user')->active();

        if ($department) {
            $query->where('department', $department);
        }

        return $query->orderBy('department')->orderBy('employee_number')->get();
    }

    /**
     * Calculate monthly EPF employee contribution (8%).
     */
    public function calculateEPFEmployee(float $grossPay): float
    {
        return round($grossPay * 0.08, 2);
    }

    /**
     * Calculate monthly EPF employer contribution (12%).
     */
    public function calculateEPFEmployer(float $grossPay): float
    {
        return round($grossPay * 0.12, 2);
    }

    /**
     * Calculate monthly ETF employer contribution (3%).
     */
    public function calculateETF(float $grossPay): float
    {
        return round($grossPay * 0.03, 2);
    }

    /**
     * Calculate total employer contribution (EPF 12% + ETF 3% = 15%).
     */
    public function calculateTotalEmployerContribution(float $grossPay): float
    {
        return round($this->calculateEPFEmployer($grossPay) + $this->calculateETF($grossPay), 2);
    }

    /**
     * Validate employee data based on employment type.
     */
    protected function validateEmployeeData(array $data): void
    {
        $employmentType = $data['employment_type'] ?? null;

        if ($employmentType === 'hourly' && empty($data['hourly_rate'])) {
            throw new Exception('Hourly rate is required for hourly employees.');
        }

        if ($employmentType === 'salaried' && empty($data['base_salary'])) {
            throw new Exception('Base salary is required for salaried employees.');
        }
    }

    /**
     * Get all unique departments.
     */
    public function getDepartments(): array
    {
        return Employee::active()
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department')
            ->toArray();
    }

    /**
     * Get all unique positions.
     */
    public function getPositions(): array
    {
        return Employee::active()
            ->whereNotNull('position')
            ->distinct()
            ->pluck('position')
            ->toArray();
    }
}
