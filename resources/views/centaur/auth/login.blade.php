@extends('Centaur::layout')

@section('title', trans('user.Login'))

@section('content')
    <div class="row rtl-text">
        <div class="col-md-4 col-md-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading prussian-blue">
                    <h3 class="panel-title rtl-text" style="direction: rtl">@lang('common_lang.Login')</h3>
                </div>
                <div class="panel-body">
                    <form accept-charset="UTF-8" role="form" method="post" action="{{ route('auth.login.attempt') }}">
                        <fieldset>
                            <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                                <input class="form-control ltr-field" placeholder="@lang('user.email')" name="email" type="text"
                                       value="{{ old('email') }}">
                                {!! ($errors->has('email') ? $errors->first('email', '<p class="text-danger">:message</p>') : '') !!}
                            </div>
                            <div class="form-group  {{ ($errors->has('password')) ? 'has-error' : '' }}">
                                <input class="form-control ltr-field" placeholder="@lang('user.password')" name="password"
                                       type="password" value="">
                                {!! ($errors->has('password') ? $errors->first('password', '<p class="text-danger">:message</p>') : '') !!}
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox"
                                           value="true" {{ old('remember') == 'true' ? 'checked' : ''}}>
                                    <small> @lang('common_lang.Remember_Me') </small>
                                </label>
                            </div>
                            <div class="form-group">
                                <script src='https://www.google.com/recaptcha/api.js?hl=fa'></script>
                                {!! app('captcha')->display(); !!}
                                {!! ($errors->has('g-recaptcha-response') ? $errors->first('g-recaptcha-response', '<p class="text-danger">:message</p>') : '') !!}
                            </div>
                            <input name="_token" value="{{ csrf_token() }}" type="hidden">
                            <input class="btn btn-lg btn-gold btn-block" type="submit"
                                   value="@lang('common_lang.Login')">

                            <div style="margin-top:6px; margin-bottom:0">
                                <p style="margin: 10px 0; padding: 10px 0;">
                                    <b> <a href="{{ route('auth.register.form') }}">@lang('common_lang.Register')</a> </b>
                                </p>
                                <p><a href="{{ route('auth.password.request.form') }}" type="submit">@lang('common_lang.Forgot_Password')</a></p>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop