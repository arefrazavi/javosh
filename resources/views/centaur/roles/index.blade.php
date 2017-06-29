@extends('layouts.master-admin')

@section('title', trans('user.Roles'))

@section('content')
    <div class="page-header">
        <div class='btn-toolbar pull-left'>
            <a class="btn btn-primary btn-lg" href="{{ route('roles.create') }}">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                @lang('user.Create_Role')
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Permissions</th>
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->slug }}</td>
                                <td>{{ implode(", ", array_keys($role->permissions)) }}</td>
                                <td>
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-default">
                                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                                        @lang('common_lang.Edit')
                                    </a>
                                    <a href="{{ route('roles.destroy', $role->id) }}" class="btn btn-danger" data-method="delete" data-token="{{ csrf_token() }}">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        @lang('common_lang.Delete')
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop