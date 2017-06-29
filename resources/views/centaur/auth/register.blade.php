@extends('Centaur::layout')

@section('title', trans('user.Register'))

@section('content')
<div class="row">
    <div class="col-md-4 col-md-offset-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">@lang('user.Register')</h4>
            </div>
            <div class="panel-body">
                <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.register.attempt') }}">
                <fieldset>
                    <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                        <input class="form-control" placeholder="@lang('user.Email')" name="email" type="text" value="{{ old('email') }}">
                        {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group  {{ ($errors->has('password')) ? 'has-error' : '' }}">
                        <input class="form-control" placeholder="@lang('user.Password')" name="password" type="password">
                        {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <div class="form-group  {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
                        <input class="form-control" placeholder="@lang('user.Confirm_Password')" name="password_confirmation" type="password">
                        {!! ($errors->has('password_confirmation') ? $errors->first('password_confirmation', '<p class="text-danger">:message</p>') : '') !!}
                    </div>
                    <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    <input class="btn btn-lg btn-primary btn-block" type="submit" value="@lang('user.Sign_Up')">
                </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>
@stop