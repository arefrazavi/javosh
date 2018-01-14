@extends('Centaur::layout')

@section('title', trans('user.Register'))

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading prussian-blue">
                <h4 class="panel-title">@lang('user.Register')</h4>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.register.attempt') }}">
                <fieldset>
                    <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                        <input class="form-control" placeholder="@lang('user.first_name')" name="first_name" type="text" value="{{ old('first_name') }}">
                        {!! ($errors->has('first_name') ? $errors->first('first_name', '<p class="text-danger">:message</p>') : '') !!}
                    </div>

                    <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                        <input class="form-control" placeholder="@lang('user.last_name')" name="last_name" type="text" value="{{ old('last_name') }}">
                        {!! ($errors->has('last_name') ? $errors->first('last_name', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                        <input class="form-control ltr-field" placeholder="@lang('user.email')" name="email" type="text" value="{{ old('email') }}">
                        {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group  {{ ($errors->has('password')) ? 'has-error' : '' }}">
                        <input class="form-control ltr-field" placeholder="@lang('user.password')" name="password" type="password">
                        {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group  {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
                        <input class="form-control ltr-field" placeholder="@lang('user.password_confirmation')" name="password_confirmation" type="password">
                        {!! ($errors->has('password_confirmation') ? $errors->first('password_confirmation', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input class="btn btn-lg btn-gold btn-block" type="submit" value="@lang('user.Sign_Up')">
                </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@stop