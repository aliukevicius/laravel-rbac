<?php

use Mockery as m;

class ActiveUserServiceTest extends PHPUnit_Framework_TestCase
{
    protected $permissionService;
    protected $roleService;
    protected $app;
    protected $guard;


    protected function initDependencies($authenticated = true, $user = null)
    {
        $this->permissionService = m::mock('Aliukevicius\LaravelRbac\Services\PermissionService');
        $this->roleService = m::mock('Aliukevicius\LaravelRbac\Services\RoleService');
        $this->app = m::mock('Illuminate\Foundation\Application');
        $this->guard = m::mock('Illuminate\Contracts\Auth\Guard');

        $this->guard->shouldReceive('user')->andReturn($user);
        $this->guard->shouldReceive('check')->andReturn($authenticated);
    }

    protected function userRoles()
    {
        return [
            'Test' => 1,
            'authenticated' => 5,
            'Admin' => 10,
        ];
    }

    protected function userPermissions()
    {
        return [
            'route_names' => [
                'test.index',
                'test.permissions',
            ],
            'route_action_names' => [
                'Aliukevicius\LaravelRbac\Test@index',
                'Aliukevicius\LaravelRbac\Test@permissions',
            ]
        ];
    }

    protected function protectedRoutes()
    {
        return [
            'test.index' => 'Aliukevicius\LaravelRbac\Test@index',
            'test.permissions' => 'Aliukevicius\LaravelRbac\Test@permissions',
            'test.protected' => 'Aliukevicius\LaravelRbac\Test@protected',
        ];
    }

    protected function activeUserService()
    {
        return new \Aliukevicius\LaravelRbac\Services\ActiveUserService(
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        );
    }

    public function tearDown()
    {
        m::close();
    }

    public function testIsAuthenticated()
    {
        $this->initDependencies();
        $this->assertEquals(true, $this->activeUserService()->isAuthenticated());

        $this->initDependencies(false);
        $this->assertEquals(false, $this->activeUserService()->isAuthenticated());
    }

    public function testHasRole()
    {
        $this->initDependencies();
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getUserRoles]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ]);

        $userService->shouldReceive('getUserRoles')->andReturn($this->userRoles());

        $this->assertEquals(true, $userService->hasRole('Test'));
        $this->assertEquals(false, $userService->hasRole('test')); // Role matching is case sensitive
        $this->assertEquals(false, $userService->hasRole(''));
        $this->assertEquals(false, $userService->hasRole(null));


        $this->initDependencies(false);
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getUserRoles]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ]);

        $userService->shouldReceive('getUserRoles')->andReturn($this->userRoles());

        // if user isn't authenticated he shouldn't have any role
        $this->assertEquals(false, $userService->hasRole('Test'));
    }

    public function testHasRoleById()
    {
        $this->initDependencies();
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getUserRoles]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ]);

        $userService->shouldReceive('getUserRoles')->andReturn($this->userRoles());

        $this->assertEquals(true, $userService->hasRoleById(10));
        $this->assertEquals(true, $userService->hasRoleById('10'));
        $this->assertEquals(false, $userService->hasRoleById(11));
        $this->assertEquals(false, $userService->hasRoleById(''));
        $this->assertEquals(false, $userService->hasRoleById(null));


        $this->initDependencies(false);
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getUserRoles]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ]);

        $userService->shouldReceive('getUserRoles')->andReturn($this->userRoles());

        // if user isn't authenticated he shouldn't have any role
        $this->assertEquals(false, $userService->hasRoleById(10));
    }

    public function testCheckPermissionByRouteName()
    {
        $this->initDependencies();
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getPermissions, getProtectedRoutes]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ])->shouldAllowMockingProtectedMethods();

        $userService->shouldReceive('getPermissions')->andReturn($this->userPermissions());
        $userService->shouldReceive('getProtectedRoutes')->andReturn($this->protectedRoutes());

        $this->assertEquals(true, $userService->checkPermissionByRouteName('test.index'));
        // route is protected but it isn't among user permissions
        $this->assertEquals(false, $userService->checkPermissionByRouteName('test.protected'));
        // route isn't among protected routes so any user should have access to it
        $this->assertEquals(true, $userService->checkPermissionByRouteName('test.unprotected'));
        $this->assertEquals(true, $userService->checkPermissionByRouteName(''));
        $this->assertEquals(true, $userService->checkPermissionByRouteName(null));

    }

    public function testCheckPermissionByRouteActionName()
    {
        $this->initDependencies();
        $userService = m::mock('Aliukevicius\LaravelRbac\Services\ActiveUserService[getPermissions, getProtectedRoutes]', [
            $this->guard,
            $this->permissionService,
            $this->roleService,
            $this->app
        ])->shouldAllowMockingProtectedMethods();

        $userService->shouldReceive('getPermissions')->andReturn($this->userPermissions());
        $userService->shouldReceive('getProtectedRoutes')->andReturn($this->protectedRoutes());

        $this->assertEquals(true, $userService->checkPermissionByRouteActionName('Aliukevicius\LaravelRbac\Test@index'));
        // route is protected but it isn't among user permissions
        $this->assertEquals(false, $userService->checkPermissionByRouteActionName('Aliukevicius\LaravelRbac\Test@protected'));
        // route isn't among protected routes so any user should have access to it
        $this->assertEquals(true, $userService->checkPermissionByRouteActionName('Aliukevicius\LaravelRbac\Test@unprotected'));
        $this->assertEquals(true, $userService->checkPermissionByRouteActionName(''));
        $this->assertEquals(true, $userService->checkPermissionByRouteActionName(null));
    }

    public function testGetUser()
    {
        $user = new \stdClass();
        $user->id = 1;

        $this->initDependencies(true, $user);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals($user, $activeUserService->getUser());

        $this->initDependencies(false, $user);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals(null, $activeUserService->getUser());
    }

    public function testGetUserId()
    {
        $user = new \stdClass();
        $user->id = 1;

        $this->initDependencies(true, $user);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals($user->id, $activeUserService->getUserId());

        $this->initDependencies(false, $user);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals(null, $activeUserService->getUserId());
    }

    public function testGetUserRoles()
    {
        $roles = [
            'Test' => 1,
            'Authenticated' => 2,
            'Admin' => 3
        ];

        $roleList = [];

        foreach ($roles as $name => $id) {
            $r = new stdClass();
            $r->name = $name;
            $r->id = $id;

            $roleList[] = $r;
        }

        $user = new \stdClass();
        $user->id = 1;

        $this->initDependencies(true, $user);
        $this->roleService->shouldReceive('getUserRoles')->andReturn($roleList);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals($roles, $activeUserService->getUserRoles());

        $this->initDependencies(false);
        $this->roleService->shouldReceive('getUserRoles')->andReturn($roleList);
        $activeUserService = new \Aliukevicius\LaravelRbac\Services\ActiveUserService($this->guard, $this->permissionService, $this->roleService, $this->app);
        $this->assertEquals([], $activeUserService->getUserRoles());
    }
}