<?php namespace Aliukevicius\LaravelRbac\Services;

use Illuminate\Database\DatabaseManager;
use Illuminate\Routing\Router;
use Aliukevicius\LaravelRbac\Models\Permission;

class PermissionService {

    /** @var \Illuminate\Database\Connection  */
    protected $db;

    /** @var Permission */
    protected $permissionModel;

    /** @var string Permission table name */
    protected $permissionTable;

    /** @var Router */
    protected $router;

    /** @var array Route list protected by checkPermission middleware  */
    protected $protectedRouteList;


    public function __construct(DatabaseManager $db, Permission $permission, Router $router)
    {
        $this->permissionModel = $permission;

        $this->permissionTable = $permission->getTable();

        $this->db = $db;

        $this->router = $router;
    }

    /**
     * Get permission list grouped by controller name
     *
     * @return array
     */
    public function getGroupedByControllerPermissions()
    {
        $result = $this->db->table($this->permissionTable)->select()->get();

        $permissionList = [];

        foreach ($result as $p) {
            $permissionList[$p->controller_name][$p->controller_action_name] = $p->id;
        }

        return $permissionList;
    }

    /**
     * Update available permission list
     *
     * @return $this
     */
    public function updatePermissionList()
    {
        $protectedRouteList = $this->getProtectedRouteList();

        $permissionListInDb = $this->permissionModel->all()->toArray();

        $insertPermissions = array_udiff($protectedRouteList, $permissionListInDb, [$this, 'comparePermissions']);
        $deletePermissions = array_udiff($permissionListInDb, $protectedRouteList, [$this, 'comparePermissions']);

        if (count($deletePermissions) > 0) {
            // remove role_permission connections
            $this->db->table('role_permission')->whereIn('permission_id', array_column($deletePermissions, 'id'))->delete();
            // remove permissions
            $this->db->table('permissions')->whereIn('id', array_column($deletePermissions, 'id'))->delete();
        }

        if (count($insertPermissions) > 0) {
            $this->db->table('permissions')->insert($insertPermissions);
        }
    }

    /**
     * Get route list protected by checkPermission middleware
     *
     * @return array
     */
    public function getProtectedRouteList()
    {
        if (is_null($this->protectedRouteList)) {
            $this->protectedRouteList = [];

            $routeList = $this->router->getRoutes();

            /** @var \Illuminate\Routing\Route $route */
            foreach ($routeList as $route) {

                // we need only routes which has permission checking middleware
                if (in_array('checkPermission', $route->middleware()) === false) {
                    continue;
                }

                if ($route->getActionName() == 'Closure') { // permissions and closures doesn't work together
                    continue;
                }

                $routeName = $route->getName();

                if (empty($routeName)) { // route name should not be empty
                    continue;
                }

                $this->protectedRouteList[$routeName] = [
                    'route_action_name' => $route->getActionName(),
                    'route_name' => $routeName,
                    'controller_name' => $this->extractControllerName($route->getActionName()),
                    'controller_action_name' => $this->extractControllerActionName($route->getActionName()),
                ];
            }
        }

        return $this->protectedRouteList;
    }

    /**
     * Save permission changes to DB
     *
     * @param $permissions
     */
    public function savePermissions($permissions)
    {
        $spList = $this->db->table('role_permission')->select()->get();

        $savedPermissionList = [];

        foreach ($spList as $p) {
            $savedPermissionList[] =  ['role_id' => $p->role_id, 'permission_id' => $p->permission_id];
        }

        $rolePermissionList = [];

        foreach ($permissions as $roleId => $permissionList) {
            foreach ($permissionList as $permissionId) {
                $rolePermissionList[] = ['role_id' => $roleId, 'permission_id' => $permissionId];
            }
        }

        $addPermissions = array_udiff($rolePermissionList, $savedPermissionList, [$this, 'compareSavedPermissions']);
        $removePermissions = array_udiff($savedPermissionList, $rolePermissionList, [$this, 'compareSavedPermissions']);

        if (count($addPermissions)) {
            $this->db->table('role_permission')->insert($addPermissions);
        }

        if (count($removePermissions) > 0) {
            foreach ($removePermissions as $r) {
                $this->db->table('role_permission')
                    ->where('role_id', '=', $r['role_id'])
                    ->where('permission_id', '=', $r['permission_id'])
                    ->delete();
            }
        }

    }

    /**
     * Permission comparison method
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function compareSavedPermissions($a, $b)
    {
        return strcmp($a['permission_id'].$a['role_id'], $b['permission_id'].$b['role_id']);
    }

    /**
     * Extract controller name from route ActionName
     *
     * @param $routeActionName
     * @return string
     */
    protected function extractControllerName($routeActionName)
    {
        $start = strrpos($routeActionName, '\\') + 1;
        $length = strpos($routeActionName, '@') - $start;

        $controllerActionName = substr($routeActionName, $start, $length);

        return $controllerActionName;
    }

    /**
     * Extract controller action name from route Action name
     *
     * @param $routeActionName
     * @return string
     */
    protected function extractControllerActionName($routeActionName)
    {
        $controllerActionName = substr($routeActionName, strpos($routeActionName, '@') + 1);

        return $controllerActionName;
    }

    /**
     * Permission difference comparison method
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function comparePermissions($a, $b)
    {
        return  strcmp($a['route_action_name'].$a['route_name'].$a['controller_name'].$a['controller_action_name'] ,
            $b['route_action_name'].$b['route_name'].$b['controller_name'].$b['controller_action_name']);
    }

    /**
     * Get permissions available to user
     *
     * @param $userId
     * @return array|static[]
     */
    public function getUserPermissions($userId)
    {
        return $this->db->table('user_role as ur')
            ->select([
                'p.route_name',
                'p.route_action_name',
                'p.id'
            ])
            ->join('role_permission as rp', 'rp.role_id', '=', 'ur.role_id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('ur.user_id', '=', $userId)
            ->get();
    }
}