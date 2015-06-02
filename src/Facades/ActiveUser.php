<?php namespace Aliukevicius\LaravelRbac\Facades;

use Illuminate\Support\Facades\Facade;

class ActiveUser extends Facade  {

    protected static function getFacadeAccessor() { return 'facade.laravel-rbac.active-user'; }

}