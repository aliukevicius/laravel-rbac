<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@lang('aliukevicius/laravelRbac::lang.pageTitle')</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    @section('styles')

    @show
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#laravel-rbac-nav">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span class="navbar-brand">Laravel RBAC</span>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="laravel-rbac-nav">
            <ul class="nav navbar-nav">
                <li class="<?= strpos(Route::currentRouteAction(), Config::get('laravel-rbac.roleController')) !== false ? 'active' : '' ?>">
                    <a href="{{ \URL::action('\\' . \Config::get('laravel-rbac.roleController') . '@index') }}">
                        @lang('aliukevicius/laravelRbac::lang.role.indexPageTitle')
                    </a>
                </li>
                <li class="<?= strpos(Route::currentRouteAction(), Config::get('laravel-rbac.permissionController')) !== false ? 'active' : '' ?>">
                    <a href="{{ \URL::action('\\' . \Config::get('laravel-rbac.permissionController') . '@index') }}">
                        @lang('aliukevicius/laravelRbac::lang.permissions.indexPageTitle')
                    </a>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<div class="container">
    @include('aliukevicius/laravelRbac::_partials.messages')
    @yield('content')
</div>

<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
@section('footerJs')

@show
</body>
</html>