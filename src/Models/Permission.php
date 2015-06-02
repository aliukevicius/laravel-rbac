<?php namespace Aliukevicius\LaravelRbac\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function roles()
    {
        return $this->belongsToMany(
            \Config::get('laravel-rbac.roleModel'),
            'role_permission',
            'permission_id',
            'role_id'
        );
    }

}