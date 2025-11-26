<?php

namespace App\Console\Commands;

use App\Enums\PermissionsEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync permissions from PermissionsEnum to database';

    public function handle(): int
    {
        $this->info('Syncing permissions from PermissionsEnum...');

        $enumPermissions = PermissionsEnum::values();
        $created = 0;
        $existing = 0;

        foreach ($enumPermissions as $permissionName) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionName],
                ['guard_name' => 'web']
            );

            if ($permission->wasRecentlyCreated) {
                $created++;
                $this->line("  <fg=green>✓</> Created: {$permissionName}");
            } else {
                $existing++;
            }
        }

        $this->newLine();
        $this->info('Sync complete!');
        $this->line("  <fg=green>{$created}</> permissions created");
        $this->line("  <fg=yellow>{$existing}</> permissions already existed");

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $this->line('  <fg=cyan>✓</> Permission cache cleared');

        return self::SUCCESS;
    }
}
