<?php

namespace App\Http\Controllers;

use App\Enums\PermissionsEnum;
use App\Services\RoutePermissionService;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct(protected RoutePermissionService $routePermissionService) {}

    /**
     * Display a listing of the permissions grouped by module
     */
    public function index()
    {
        $groupedPermissions = PermissionsEnum::grouped();
        $allPermissions = Permission::with('roles')->get()->keyBy('name');
        $routes = $this->routePermissionService->getGroupedRoutes();

        return view('permissions.index', compact('groupedPermissions', 'allPermissions', 'routes'));
    }

    /**
     * Sync route permissions
     */
    public function syncRoutes()
    {
        $result = $this->routePermissionService->syncRoutePermissions();

        $message = 'Route permissions synced! ';
        $message .= 'Created: '.count($result['created']).', ';
        $message .= 'Existing: '.count($result['existing']).', ';
        $message .= 'Total: '.$result['total'];

        return redirect()->route('permissions.index')
            ->with('success', $message);
    }
}
