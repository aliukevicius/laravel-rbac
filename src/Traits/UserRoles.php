<?php namespace Aliukevicius\LaravelRbac\Traits; 

trait UserRoles {

    public function roles()
    {
        return $this->belongsToMany(
            \Config::get('laravel-rbac.roleModel'),
            'user_role',
            'user_id',
            'role_id'
        );
    }
}