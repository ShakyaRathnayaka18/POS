@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Employees</h1>
        @can('create employees')
            <a href="{{ route('employees.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>Add Employee
            </a>
        @endcan
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Filters</h2>
        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select
                    name="status"
                    id="status"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>

            <div>
                <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Employment Type</label>
                <select
                    name="employment_type"
                    id="employment_type"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="">All Types</option>
                    <option value="hourly" {{ request('employment_type') == 'hourly' ? 'selected' : '' }}>Hourly</option>
                    <option value="salaried" {{ request('employment_type') == 'salaried' ? 'selected' : '' }}>Salaried</option>
                </select>
            </div>

            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="{{ request('search') }}"
                    placeholder="Name, Number, EPF..."
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-filter mr-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Employees Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Employee #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Position</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rate/Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $employee->employee_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $employee->getFullName() }}
                            @if($employee->user)
                                <br>
                                <span class="text-xs text-gray-400">{{ $employee->user->email }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $employee->department ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ $employee->position ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            {{ ucfirst($employee->employment_type) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                            @if($employee->isSalaried())
                                LKR {{ number_format($employee->base_salary, 2) }}/mo
                            @else
                                LKR {{ number_format($employee->hourly_rate, 2) }}/hr
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full text-white
                                @if($employee->status === 'active') bg-green-500
                                @elseif($employee->status === 'terminated') bg-red-500
                                @elseif($employee->status === 'suspended') bg-yellow-500
                                @endif">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('employees.show', $employee) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 mr-3">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @can('edit employees')
                                <a href="{{ route('employees.edit', $employee) }}" class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 mr-3">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endcan
                            @can('delete employees')
                                @if($employee->isActive())
                                    <form action="{{ route('employees.terminate', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Terminate this employee?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400">
                                            <i class="fas fa-user-times"></i> Terminate
                                        </button>
                                    </form>
                                @elseif($employee->status === 'terminated')
                                    <form action="{{ route('employees.reactivate', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Reactivate this employee?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 dark:text-green-400">
                                            <i class="fas fa-user-check"></i> Reactivate
                                        </button>
                                    </form>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No employees found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($employees->hasPages())
        <div class="mt-6">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection
