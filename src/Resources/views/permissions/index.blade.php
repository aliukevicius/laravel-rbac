@extends('aliukevicius/laravelRbac::templates.main')

@section('content')
<form action="{{ \URL::action('\\' . \Config::get('laravel-rbac.permissionController') . '@savePermissions') }}" method="POST">
    <input type="hidden" name="_token" value="{!! csrf_token(); !!}">
    <div class="panel panel-default">
        <div class="panel-heading">@lang('aliukevicius/laravelRbac::lang.permissions.indexPageTitle')</div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th></th>
                    @foreach($roles as $r)
                        <th class="text-center">{{ $r->name }}</th>
                    @endforeach
                </tr>
                </thead>

                @foreach($permissions as $controllerName => $pList)
                    <tr class="info">
                        <td colspan="{{ $roleCount + 1 }}">{{ $controllerName }}</td>
                    </tr>

                    @foreach($pList as $actionName => $permissionId)
                        <tr>
                            <td>{{ $actionName }}</td>

                            @foreach($roles as $r)
                                <td class="text-center">
                                    <input
                                            type="checkbox"
                                            name="permissions[{{ $r->id }}][{{ $permissionId }}]"
                                            value="{{ $permissionId }}"
                                            <?= isset($rolePermissions[$r->id][$permissionId]) ? 'checked' : '' ?>
                                            />
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </table>


        </div>
    </div>

    <a href="{{ \URL::action('\\' . \Config::get('laravel-rbac.permissionController') . '@updatePermissionList') }}" class="btn btn-primary">
        <i class="glyphicon glyphicon-refresh"></i> @lang('aliukevicius/laravelRbac::lang.permission.refreshPermissionListBtn')
    </a>

    <button type="submit" class="btn btn-success pull-right">
        <i class="glyphicon glyphicon-ok"></i> @lang('aliukevicius/laravelRbac::lang.permission.savePermissionsBtn')
    </button>
</form>

@stop
