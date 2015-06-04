<?php

return [

    'routeUrlPrefix'            => '', //route prefix for all package routes

    'rolesPerPage'              => 10, // how many roles to display in one page

    'routePermissionChecking'   => true, // change to false if route permission checking should be turned off

    'roleController'            => 'Aliukevicius\LaravelRbac\Http\Controllers\RoleController',
    'roleModel'                 => 'Aliukevicius\LaravelRbac\Models\Role',
    'permissionController'      => 'Aliukevicius\LaravelRbac\Http\Controllers\PermissionController',

    // class which is available through ActiveUser facade
    'activeUserService'         => 'Aliukevicius\LaravelRbac\Services\ActiveUserService',

    // class for global "checkPermission" middleware
    'checkPermissionMiddleware' => 'Aliukevicius\LaravelRbac\Http\Middleware\CheckPermission'
];