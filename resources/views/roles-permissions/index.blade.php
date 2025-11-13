@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Roles & Permissions Management</h1>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 dark:bg-green-900/20 dark:border-green-700 dark:text-green-400">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 dark:bg-red-900/20 dark:border-red-700 dark:text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Manage Roles</h2>
        @can('create roles')
        <a href="{{ route('roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-plus mr-2"></i>Add New Role
        </a>
        @endcan
    </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($roles as $role)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $role->name }}</h3>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        {{ $role->permissions_count }} permissions
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Users: <span class="font-semibold">{{ $role->users_count }}</span>
                    </p>
                </div>

                <div class="flex gap-2">
                    @can('edit roles')
                    <a href="{{ route('roles.edit', $role) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-center transition">
                        Edit
                    </a>
                    @endcan
                    @can('delete roles')
                    @if($role->name !== 'Super Admin')
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" onsubmit="return confirm('Are you sure? This will affect {{ $role->users_count }} user(s).');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition">
                            Delete
                        </button>
                    </form>
                    @endif
                    @endcan
                </div>
            </div>
            @endforeach
        </div>
</div>
@endsection
