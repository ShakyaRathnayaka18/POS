<?php

namespace App\Services;

use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;

class RoutePermissionService
{
    /**
     * Get all application routes excluding vendor and internal routes
     */
    public function getAllRoutes(): array
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();

            // Skip unnamed routes, vendor routes, and internal routes
            if (! $name || $this->shouldSkipRoute($name)) {
                continue;
            }

            $routes[] = [
                'name' => $name,
                'uri' => $route->uri(),
                'methods' => implode('|', $route->methods()),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        }

        return $routes;
    }

    /**
     * Get routes grouped by module
     */
    public function getGroupedRoutes(): array
    {
        $routes = $this->getAllRoutes();
        $grouped = [];

        foreach ($routes as $route) {
            $module = $this->extractModuleName($route['name']);
            $grouped[$module][] = $route;
        }

        ksort($grouped);

        return $grouped;
    }

    /**
     * Sync route permissions with database
     */
    public function syncRoutePermissions(): array
    {
        $routes = $this->getAllRoutes();
        $created = [];
        $existing = [];

        foreach ($routes as $route) {
            $permissionName = $this->suggestPermissionName($route['name']);

            $permission = Permission::firstOrCreate(
                ['name' => $permissionName, 'guard_name' => 'web']
            );

            if ($permission->wasRecentlyCreated) {
                $created[] = $permissionName;
            } else {
                $existing[] = $permissionName;
            }
        }

        return [
            'created' => $created,
            'existing' => $existing,
            'total' => count($routes),
        ];
    }

    /**
     * Suggest a human-readable permission name from route name
     */
    public function suggestPermissionName(string $routeName): string
    {
        // Convert route name to permission name
        // Example: categories.index -> view categories
        // Example: products.store -> create products
        // Example: sales.destroy -> delete sales

        $parts = explode('.', $routeName);

        if (count($parts) < 2) {
            return str_replace(['-', '_'], ' ', $routeName);
        }

        $resource = end($parts);
        array_pop($parts);
        $module = implode(' ', $parts);

        $action = match ($resource) {
            'index', 'show' => 'view',
            'create', 'store' => 'create',
            'edit', 'update' => 'edit',
            'destroy' => 'delete',
            default => $resource,
        };

        return "{$action} {$module}";
    }

    /**
     * Extract module name from route name
     */
    protected function extractModuleName(string $routeName): string
    {
        $parts = explode('.', $routeName);

        if (count($parts) === 1) {
            return 'General';
        }

        // Get the first part as module name
        $module = $parts[0];

        return ucwords(str_replace(['-', '_'], ' ', $module));
    }

    /**
     * Determine if a route should be skipped
     */
    protected function shouldSkipRoute(string $routeName): bool
    {
        $skipPatterns = [
            'generated::',
            'sanctum.',
            'ignition.',
            'debugbar.',
            'horizon.',
            'telescope.',
            'livewire.',
            '_ignition',
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_starts_with($routeName, $pattern)) {
                return true;
            }
        }

        return false;
    }
}
