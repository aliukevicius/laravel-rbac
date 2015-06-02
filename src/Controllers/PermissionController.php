<?php namespace Aliukevicius\LaravelRbac\Controllers; 

use Aliukevicius\LaravelRbac\Services\PermissionService;
use Aliukevicius\LaravelRbac\Models\Permission;

class PermissionController extends Controller {

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $roleModel;

    /** @var Permission */
    protected $permissionModel;

    /** @var PermissionService */
    protected $permissionService;

    public function __construct(PermissionService $permissionService, Permission $permission)
    {
        $this->roleModel = \App::make(\Config::get('laravel-rbac.roleModel'));
        $this->permissionModel = $permission;
        $this->permissionService = $permissionService;
    }

    public function index()
    {
        $roles = $this->roleModel->all();
        $permissions = $this->permissionService->getGroupedByControllerPermissions();

        $roleCount = count($roles);

        $rolePermissions = $this->permissionService->getRolePermissions();;

        $activePermissions = $this->permissionService->getRolePermissions();

        return view('aliukevicius/laravelRbac::permissions.index', compact(
            'roles',
            'permissions',
            'activePermissions',
            'roleCount',
            'rolePermissions'
        ));
    }

    public function updatePermissionList()
    {
        $this->permissionService->updatePermissionList();

        return \Redirect::to(\URL::action('\\' . \Config::get('laravel-rbac.permissionController') . '@index'));
    }

    public function savePermissions()
    {
        $permissions = \Input::get('permissions', []);

        $this->permissionService->savePermissions($permissions);

        return \Redirect::to(\URL::action('\\' . \Config::get('laravel-rbac.permissionController') . '@index'));
    }
}