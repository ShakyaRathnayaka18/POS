<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsEnum;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::withCount('permissions', 'users')->get();

        // Get all permissions with their roles for the permissions tab
        $allPermissions = \Spatie\Permission\Models\Permission::with('roles')->get();

        // Auto-sync route permissions
        $routePermissionService = app(\App\Services\RoutePermissionService::class);
        $syncResult = $routePermissionService->syncRoutePermissions();
        $syncedCount = count($syncResult['created']);

        // Group permissions by module with role information
        $permissionsByRole = [];

        foreach ($allPermissions as $permission) {
            // Try to determine module from permission name
            $module = $this->getModuleFromPermission($permission->name);

            if (! isset($permissionsByRole[$module])) {
                $permissionsByRole[$module] = [];
            }

            $permissionsByRole[$module][] = [
                'name' => $permission->name,
                'roles' => $permission->roles->pluck('name')->toArray(),
            ];
        }

        // Sort permissions by module and then by name
        ksort($permissionsByRole);

        return view('roles-permissions.index', compact('roles', 'allPermissions', 'permissionsByRole', 'syncedCount'));
    }

    /**
     * Determine the module from permission name
     */
    protected function getModuleFromPermission(string $permissionName): string
    {
        // Extract module from permission name
        // Example: "view products" -> "Products"
        // Example: "create sales" -> "Sales"

        $parts = explode(' ', $permissionName);
        $lastPart = end($parts);

        // Capitalize and singularize/pluralize appropriately
        return ucfirst($lastPart);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = PermissionsEnum::grouped();

        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (! empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        return redirect()->route('roles-permissions.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = PermissionsEnum::grouped();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,'.$role->id],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        } else {
            $role->syncPermissions([]);
        }

        return redirect()->route('roles-permissions.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting Super Admin role
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles-permissions.index')
                ->with('error', 'Cannot delete Super Admin role.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->route('roles-permissions.index')
                ->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('roles-permissions.index')
            ->with('success', 'Role deleted successfully.');
    }
}
