@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('roles-permissions.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                <i class="fas fa-arrow-left mr-2"></i>Back to Roles & Permissions
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Edit Role: {{ $role->name }}</h2>

            <form action="{{ route('roles.update', $role) }}" method="POST" x-data="{ selectAll: {} }">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-100 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Assign Permissions</h3>
                    <div class="space-y-6">
                        @foreach($permissions as $module => $modulePermissions)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300">{{ $module }}</h4>
                                <label class="flex items-center text-sm text-blue-600 cursor-pointer">
                                    <input type="checkbox" x-model="selectAll['{{ $module }}']"
                                        @click="document.querySelectorAll('[data-module=\'{{ $module }}\']').forEach(el => el.checked = $event.target.checked)"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                                    Select All
                                </label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($modulePermissions as $permission)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->value }}"
                                        data-module="{{ $module }}"
                                        {{ in_array($permission->value, old('permissions', $rolePermissions)) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucwords(str_replace(['_', '-'], ' ', str_replace(strtolower($module), '', $permission->value))) }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        Update Role
                    </button>
                    <a href="{{ route('roles-permissions.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
