<?php

Route::group(['prefix' => Config::get('laravel-rbac.routeUrlPrefix'), 'middleware' => 'checkPermission'], function(){
    Route::resource('roles', Config::Get('laravel-rbac.roleController'));

    Route::get('permissions', [
        'as' => 'permissions.index',
        'uses' => Config::Get('laravel-rbac.permissionController') . '@index'
    ]);

    Route::get('permissions/update-permission-list', [
        'as' => 'permissions.updatePermissionList',
        'uses' => Config::Get('laravel-rbac.permissionController') . '@updatePermissionList'
    ]);

    Route::post('permissions/save-permissions', [
        'as' => 'permissions.savePermissions',
        'uses' => Config::Get('laravel-rbac.permissionController') . '@savePermissions'
    ]);
});


