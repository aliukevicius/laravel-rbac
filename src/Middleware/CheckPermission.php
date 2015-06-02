<?php namespace Aliukevicius\LaravelRbac\Middleware;

use Closure;

class CheckPermission {

    protected $activeUser;

    protected $checkPermissions;

    public function __construct()
    {
        $this->activeUser = \App::make('Aliukevicius\LaravelRbac\ActiveUser');
        $this->checkPermissions = \Config::get('laravel-rbac.routePermissionChecking');
    }


    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->activeUser->canAccessRoute($request->route()->getName()) === false && $this->checkPermissions) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }

}