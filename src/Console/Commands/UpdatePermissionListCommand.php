<?php namespace Aliukevicius\LaravelRbac\Console\Commands;

use Aliukevicius\LaravelRbac\Services\PermissionService;
use Illuminate\Console\Command;

class UpdatePermissionListCommand extends  Command {

    protected $permissionService;

    /**
     * Console command name
     *
     * @var string
     */
    protected $name = 'laravel-rbac:update-permission-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update available permission list';

    public function __construct(PermissionService $permissionService)
    {
        parent::__construct();

        $this->permissionService = $permissionService;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function fire()
    {
        $this->permissionService->updatePermissionList();

        $this->info('Permission list updated');

        return true;
    }
}