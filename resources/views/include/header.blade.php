<!-- Main Header -->
<header class="main-header">

    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><img class="img-circle" style="width: 30px; height:30px"
                                     src="{{ asset('images/Javosh.png') }}"></span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo"><img style="width: 27px; height:40px"
                                src="{{ asset('images/Javosh.png') }}"> @lang("common_lang.avosh")</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar left Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                <!-- Notifications Menu -->
                <li>
                    <!-- Menu toggle button -->
                    <a href="{{ route("help") }}">
                        <span> @lang("common_lang.Help") </span>
                    </a>
                </li>
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        <img src="{{ asset("admin-lte/dist/img/user-img.png") }}" class="user-image" alt="User Image">
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span class="hidden-xs">{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            <img src="{{ asset("admin-lte/dist/img/user-img.png") }}" class="img-circle"
                                 alt="User Image">

                            <p>
                                {{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}
                            </p>
                        </li>
                        <!-- Menu Footer-->
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ route('users.edit', Sentinel::getUser()->id) }}"
                                   class="btn btn-default btn-flat">@lang('user.Profile')</a>
                            </div>
                            <div class="pull-left">
                                <a href="{{ route('auth.logout') }}"
                                   class="btn btn-default btn-flat">@lang('user.Log_Out')</a>
                            </div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>