## Install

Via Composer
``` 
composer require aliukevicius/laravel-rbac
```

Add Service Provider to `config/app.php` in `providers` section
```php
'Aliukevicius\LaravelRbac\RbacServiceProvider',
```

Add Service Provider to `config/app.php` in `aliases` section
```php
'ActiveUser' => 'Aliukevicius\LaravelRbac\Facades\ActiveUser',
```

Run 
``` 
php artisan vendor:publish
php artisan laravel-rbac:create-migrations
php artisan migrate
``` 

Add `checkPermission` middleware for the routes on which permissions checking should be enabled.

```php
Route::group(['middleware' => 'checkPermission'], function(){
...
});
```



