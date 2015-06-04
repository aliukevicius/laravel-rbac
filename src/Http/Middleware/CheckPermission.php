<?php namespace Aliukevicius\LaravelRbac\Http\Middleware;

use Closure;

use Illuminate\Foundation\Application;
use Illuminate\Config\Repository as Config;

class CheckPermission {

    protected $activeUser;

    protected $checkPermissions;

    public function __construct(Application $app, Config $config)
    {
        $this->activeUser = $app->make('Aliukevicius\LaravelRbac\ActiveUser');
        $this->checkPermissions = $config->get('laravel-rbac.routePermissionChecking');
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