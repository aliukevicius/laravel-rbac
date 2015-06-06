<?php namespace Aliukevicius\LaravelRbac\Services;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Application;

class ActiveUserService {

    /** @var PermissionService */
    protected $permissionService;

    /** @var RoleService */
    protected $roleService;

    /** @var Guard */
    protected $guard;

    /** @var \Illuminate\Foundation\Application  */
    protected $app;

    /** @var array Array of routes which are protected */
    protected $protectedRoutes;

    /** Logged in user object */
    protected $user;

    /** @var array of user roles */
    protected $userRoles;

    /** @var array of user permissions */
    protected $userPermissions;

    public function __construct(
        Guard $guard,
        PermissionService $permissionService,
        RoleService $roleService,
        Application $app
    )
    {
        $this->guard = $guard;
        $this->app = $app;
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;

        $this->initUserData();
    }

    /**
     * Initialise user data
     */
    protected function initUserData()
    {
        // First we need to make sure that user is authenticated.
        if ($this->isAuthenticated()) {
            $this->user = $this->guard->user();
        }
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated()
    {
        return $this->guard->check();
    }

    /**
     * Check if user hash role using role name
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName)
    {
        if ($this->isAuthenticated() === false) {
            return false;
        }

        $roles = $this->getUserRoles();

        return isset($roles[$roleName]);
    }

    /**
     * Check if user has role using role ID
     *
     * @param int $roleId
     * @return bool
     */
    public function hasRoleById($roleId)
    {
        if ($this->isAuthenticated() === false) {
            return false;
        }

        $roles = $this->getUserRoles();

        return in_array($roleId, $roles);
    }

    /**
     * Returns true if user has permission and false if user doesn't have permission
     *
     * @param string $routeName eg: roles.index
     * @return bool
     */
    public function checkPermissionByRouteName($routeName)
    {
        $protectedRoutes = $this->getProtectedRoutes();

        // check if route is protected
        if (isset($protectedRoutes[$routeName])) {

            $userPermissions = $this->getPermissions();

            // check if user has permission for this route
            if (isset($userPermissions['route_names']) && in_array($routeName, $userPermissions['route_names'])) {
                return true;
            }

            return false;
        }

        return true; // if route isn't in protected route list then user should have access to it
    }

    /**
     * Returns true if user has permission and false if user doesn't have permission
     *
     * @param string $routeActionName eg: Aliukevicius\LaravelRbac\Controllers\RoleController@index
     * @return bool
     */
    public function checkPermissionByRouteActionName($routeActionName)
    {
        $protectedRoutes = $this->getProtectedRoutes();

        // check if route is protected
        if (in_array($routeActionName, $protectedRoutes)) {

            $userPermissions = $this->getPermissions();

            // check if user has permission for this route
            if (isset($userPermissions['route_action_names']) && in_array($routeActionName, $userPermissions['route_action_names'])) {
                return true;
            }

            return false;
        }

        return true; // if route isn't in protected route list then user should have access to it
    }

    /**
     * Get routes protected by permissions
     *
     * @return array
     */
    protected function getProtectedRoutes()
    {
        if ($this->protectedRoutes === null) {
            $this->protectedRoutes = [];

            $routeList = $this->permissionService->getProtectedRouteList();

            if (count($routeList) > 0) {
                $this->protectedRoutes = array_column($routeList, 'route_action_name', 'route_name');
            }
        }

        return $this->protectedRoutes;
    }

    /**
     * Get logged in users object or null if user isn't authenticated
     *
     * @return mixed
     */
    public function getUser()
    {
        if ($this->isAuthenticated() === false) {
            return null;
        }

        if ($this->user === null) {
            $this->user = $this->guard->user();
        }

        return $this->user;
    }

    /**
     * Get logged in users ID or null if user isn't authenticated
     *
     * @return int|null
     */
    public function getUserId()
    {
        if ($this->isAuthenticated() === false) {
            return null;
        }

        return $this->getUser()->id;
    }

    /**
     * Get logged in users roles
     *
     * Returns array role_name => role_id
     *
     * @return array
     */
    public function getUserRoles()
    {
        if ($this->isAuthenticated() === false) {
            return [];
        }

        if ($this->userRoles === null) {
            $this->userRoles = [];

            // get roles for logged in user
            $roleList = $this->roleService->getUserRoles($this->getUserId());

            foreach ($roleList as $r) {
                $this->userRoles[$r->name] = $r->id;
            }
        }

        return $this->userRoles;
    }

    /**
     * Get user permissions
     *
     * @return array
     */
    protected function getPermissions()
    {
        if ($this->isAuthenticated() === false) {
            return [];
        }

        if ($this->userPermissions === null) {
            // get user permissions
            $permissions = $this->permissionService->getUserPermissions($this->getUserId());

            foreach ($permissions as $p) {
                $this->userPermissions['route_names'][] = $p->route_name;
                $this->userPermissions['route_action_names'][] = $p->route_action_name;
            }

            if ($this->userPermissions === null) {
                return [];
            }
        }

        return $this->userPermissions;
    }
}