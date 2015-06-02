<?php namespace Aliukevicius\LaravelRbac\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

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

    public function permissions()
    {
        return $this->belongsToMany(
            'Aliukevicius\LaravelRbac\Models\Permission',
            'role_permission',
            'role_id',
            'permission_id'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            \Config::get('auth.model'),
            'user_role',
            'role_id',
            'user_id'
        );
    }
}