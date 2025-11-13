<?php

namespace Database\Seeders;

use App\Enums\PermissionsEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create all permissions from enum (or find if they already exist)
        foreach (PermissionsEnum::cases() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission->value, 'guard_name' => 'web'],
                ['name' => $permission->value, 'guard_name' => 'web']
            );
        }

        $this->command->info('Created '.count(PermissionsEnum::cases()).' permissions successfully!');
    }
}
