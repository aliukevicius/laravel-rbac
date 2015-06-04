<?php namespace Aliukevicius\LaravelRbac\Services;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\DatabaseManager;

class RoleService {

    protected $roleModel;

    /** @var \Illuminate\Database\Connection  */
    protected $db;

    /** @var  \Illuminate\Foundation\Application*/
    protected $app;

    protected $config;

    public function __construct(DatabaseManager $db, Application $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config;
        $this->db = $db;

        $this->roleModel = $this->app->make($this->config->get('laravel-rbac.roleModel'));

    }

    /**
     * Attach roles to user
     *
     * @param int|array $roleIds
     * @param int       $userId
     * @return $this
     */
    public function attachRole($userId, $roleIds)
    {
        $roleIds = (array) $roleIds;

        $userRoles = $this->getUserRoles($userId);

        $savedRoles =  [];

        foreach ($userRoles as $r) {
            $savedRoles[] = $r->id;
        }

        // remove roles which user already has
        $addRoles = array_diff($roleIds, $savedRoles);

        $insertRoles = [];
        foreach ($addRoles as $r) {
            $insertRoles[] = ['user_id' => $userId, 'role_id' => $r];
        }

        if (count($insertRoles) > 0) {
            $this->db->table('user_role')->insert($insertRoles);
        }

        return $this;
    }

    /**
     * Attach roles to user
     *
     * @param int    $userId
     * @param string|array $roleName
     * @return $this
     */
    public function attachRoleByName($userId, $roleName)
    {
        $rolesFromDb = $this->getRolesByNames($roleName);

        $roleIds = [];
        foreach ($rolesFromDb as $r) {
            $roleIds[] = $r->id;
        }

        return $this->attachRole($userId, $roleIds);
    }

    /**
     * Remove roles from user
     *
     * @param int|array $roleIds
     * @param int       $userId
     * @return $this
     */
    public function detachRole($roleIds, $userId)
    {
        $roleIds = (array) $roleIds;

        if (count($roleIds) > 0) {
            $this->db->table('user_role')
                ->where('user_id', '=', $userId)
                ->whereIn('role_id', $roleIds)
                ->delete();
        }

        return $this;
    }

    /**
     * Get active permissions for each role
     *
     * @return array
     */
    public function getRolePermissions()
    {
        $rpList = [];

        $result = $this->db->table('role_permission')->select()->get();

        foreach ($result as $rp) {
            $rpList[$rp->role_id][$rp->permission_id] = $rp->permission_id;
        }

        return $rpList;
    }

    /**
     * Get roles assigned to user
     *
     * @param $userId
     * @return array|static[]
     */
    public function getUserRoles($userId)
    {
        return $this->db->table('user_role as ur')
            ->select([
                'r.id',
                'r.name',
            ])
            ->join('roles as r', 'r.id', '=', 'ur.role_id')
            ->where('ur.user_id', '=', $userId)
            ->get();
    }

    /**
     * Get roles by names
     *
     * @param string|array $names
     */
    public function getRolesByNames($names)
    {
        $names = (array) $names;
        return $this->roleModel->whereIn('name', $names)->get();
    }


}