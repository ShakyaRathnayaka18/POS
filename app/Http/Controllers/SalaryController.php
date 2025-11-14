<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalaryController extends Controller
{
    // Display all salary records
    public function index()
    {
        // Check if the user is authenticated and has the 'admin' or 'superadmin' role
        $user = Auth::user();
        // dd($user);
        // $user->role = 'superadmin';
        if (!$user || !in_array($user->name, ['admin', 'superadmin'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        // Fetch all salary records with the related user (cashier) details
        $salaries = EmployeeSalary::with('user')->get();
        return view('salary.show', compact('salaries'));
    }

    // Show the form for creating a new salary record
    // Controller: SalaryController.php
    public function create()
    {
        // Check if the user is authenticated and has the 'admin' or 'superadmin' role
        $user = Auth::user();
        if (!$user || !in_array($user->name, ['admin', 'superadmin'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        // Fetch all users excluding 'admin' and 'superadmin' by name for the dropdown list
        $users = User::whereNotIn('name', ['admin', 'superadmin'])->get();

        // Default values
        $epfRate = 8;  // Default EPF rate 8%
        $etfRate = 3;  // Default ETF rate 3%
        $basicSalary = 20000; // Default Basic Salary
        $otRate = 2000; // Default OT Rate
        $otHours = 0;   // Default OT Hours (no dynamic clock-in/out, so hardcoded to 0)

        // Server-side salary calculation
        $epf = $basicSalary * ($epfRate / 100);
        $etf = $basicSalary * ($etfRate / 100);
        $otAmount = $otHours * $otRate;
        $totalSalary = $basicSalary + $otAmount - $epf - $etf;

        // Pass these values to the view
        return view('salary.index', compact('users', 'epfRate', 'etfRate', 'basicSalary', 'otRate', 'otHours', 'totalSalary'));
    }

    // Store a new salary record
    public function store(Request $request)
    {
        // Check if the user is authenticated and has the 'admin' or 'superadmin' role
        $user = Auth::user();
        if (!$user || !in_array($user->name, ['admin', 'superadmin'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        // Validate incoming data
        $request->validate([
            'user_id' => 'required|exists:users,id',  // Ensure the user exists in the database
            'basic_salary' => 'required|numeric',    // Basic salary is required and should be numeric
            'ot_hours' => 'required|numeric',        // OT hours are required and should be numeric
            'ot_rate' => 'required|numeric',         // OT rate is required and should be numeric
            'epf_rate' => 'required|numeric',        // EPF rate is required and should be numeric
            'etf_rate' => 'required|numeric',        // ETF rate is required and should be numeric
        ]);

        // Find the user and calculate EPF and ETF
        $user = User::findOrFail($request->user_id); // Find the user based on provided ID
        $epf = $request->basic_salary * ($request->epf_rate / 100); // Calculate EPF
        $etf = $request->basic_salary * ($request->etf_rate / 100); // Calculate ETF

        // Create the salary record in the database
        $salary = EmployeeSalary::create([
            'user_id' => $request->user_id,
            'basic_salary' => $request->basic_salary,
            'ot_hours' => $request->ot_hours,
            'ot_rate' => $request->ot_rate,
            'epf' => $epf,
            'etf' => $etf,
        ]);

        // Calculate total salary based on the formula
        $totalSalary = $salary->basic_salary + ($salary->ot_hours * $salary->ot_rate) - $epf - $etf;

        // Update the total salary and save the record
        $salary->total_salary = $totalSalary;
        $salary->save();

        // Redirect to the salary index page with a success message
        return redirect()->route('salary.index')->with('success', 'Salary record created successfully.');
    }

    // Show the form for editing an existing salary record
    public function edit($id)
    {
        // Check if the user is authenticated and has the 'admin' or 'superadmin' role
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        // Find the salary record and fetch 'cashier' users for the dropdown
        $salary = EmployeeSalary::findOrFail($id);
        $users = User::where('role', 'cashier')->get();
        return view('salary.edit', compact('salary', 'users'));
    }

    // Update an existing salary record
    public function update(Request $request, $id)
    {
        // Check if the user is authenticated and has the 'admin' or 'superadmin' role
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin'])) {
            return redirect()->route('cashier.dashboard')->with('error', 'You do not have permission to access this page.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',  // Ensure the user exists
            'basic_salary' => 'required|numeric',
            'ot_hours' => 'required|numeric',
            'ot_rate' => 'required|numeric',
            'epf_rate' => 'required|numeric',
            'etf_rate' => 'required|numeric',
        ]);

        // Find the salary record and update it
        $salary = EmployeeSalary::findOrFail($id);
        $user = User::findOrFail($request->user_id);

        $epf = $request->basic_salary * ($request->epf_rate / 100);
        $etf = $request->basic_salary * ($request->etf_rate / 100);

        $salary->user_id = $request->user_id;
        $salary->basic_salary = $request->basic_salary;
        $salary->ot_hours = $request->ot_hours;
        $salary->ot_rate = $request->ot_rate;
        $salary->epf = $epf;
        $salary->etf = $etf;

        // Recalculate total salary
        $salary->total_salary = $salary->basic_salary + ($salary->ot_hours * $salary->ot_rate) + ($salary->epf + $salary->etf);
        $salary->save();

        return redirect()->route('salary.index')->with('success', 'Salary record updated successfully.');
    }
}
