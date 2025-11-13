<?php

namespace App\Console\Commands;

use App\Services\RoutePermissionService;
use Illuminate\Console\Command;

class SyncRoutePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically sync route permissions with the database';

    public function __construct(protected RoutePermissionService $routePermissionService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Syncing route permissions...');

        $result = $this->routePermissionService->syncRoutePermissions();

        if (count($result['created']) > 0) {
            $this->info('Created '.count($result['created']).' new permissions:');
            foreach ($result['created'] as $permission) {
                $this->line('  - '.$permission);
            }
        }

        if (count($result['existing']) > 0) {
            $this->comment('Found '.count($result['existing']).' existing permissions');
        }

        $this->info('Total routes processed: '.$result['total']);
        $this->newLine();
        $this->info('Route permissions synced successfully!');

        return self::SUCCESS;
    }
}
