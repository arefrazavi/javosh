<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="google-site-verification" content="e02PAFaPzEXO676NK3hafXtKZ-pvuomVwfs-H8KB5b4">
    <meta name="description" content="@lang('common_lang.description_meta')">
    <meta name="keywords" content="@lang('common_lang.keywords_meta')">
    <meta name="author" content="@lang('common_lang.author_meta')">
    <title> @lang("common_lang.Javosh") (@lang('common_lang.Summarization')) - @yield('title')</title>
    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/gif" sizes="16x16">

    <!-- Bootstrap - Latest compiled and minified CSS -->
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/bootstrap-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/bootstrap-theme-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/dist/css/AdminLTE-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/dist/css/skins/_all-skins-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("css/javosh.css") }}">
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body class="ivory-black">
@include("include.google-analytics")
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand logo" href="/"><img src="{{ asset('images/Javosh.png') }}"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-left">
                @if (Sentinel::check())
                    <li><p class="navbar-text">{{ Sentinel::getUser()->email }}</p></li>
                    <li><a href="{{ route('auth.logout') }}">@lang('common_lang.Logout')</a></li>
                @else
                    <li><a href="{{ route('auth.login.form') }}">@lang('common_lang.Login')</a></li>
                    <li><a href="{{ route('auth.register.form') }}">@lang('common_lang.Register')</a></li>
                    <li><a href="{{ route("help") }}"><span> @lang("common_lang.Help") </span></a>
                    </li>
                @endif
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<div class="container">
    @include('Centaur::notifications')
    @yield('content')
</div>
<div class="centaur-footer">
    @include('include.footer')
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified Bootstrap JavaScript -->
<script src="{{ asset ("admin-lte/bootstrap/js/bootstrap-rtl.js") }}" type="text/javascript"></script>
<!-- Restfulizer.js - A tool for simulating put,patch and delete requests -->
<script src="{{ asset('restfulizer.js') }}"></script>
<script src="{{ asset ("js/common.js") }}"></script>
</body>
</html>