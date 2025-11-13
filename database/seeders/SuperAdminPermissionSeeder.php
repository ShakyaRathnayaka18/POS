<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum as P;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class SuperAdminPermissionSeeder extends Seeder
{
    /**
     * Assign all permissions to Super Admin role.
     */
    public function run(): void
    {
        $superAdmin = Role::findByName('Super Admin');

        // Get all permission values from the enum
        $allPermissions = P::values();

        // Assign all permissions to Super Admin
        $superAdmin->syncPermissions($allPermissions);

        $this->command->info('All permissions assigned to Super Admin role.');
    }
}
