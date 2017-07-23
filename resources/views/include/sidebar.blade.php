<!-- right side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-right image">
                <img src="{{ asset("admin-lte/dist/img/user-img.png") }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-right info">
                <p>{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i>@lang('common_lang.Online')</a>
            </div>
        </div>

        <ul class="sidebar-menu">
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="/dashboard">
                    <i class="fa fa-tachometer"></i><span>@lang('common_lang.Dashboard')</span>
                </a>
            </li>
            <!-- ACL menu -->
            @if(Sentinel::getUser()->inRole('administrator'))
            <li class="treeview {{ Request::is('users*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>@lang('user.User')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('users*') || Request::is('roles*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">@lang('user.Users')</a>
                    </li>
                    <li class="{{ Request::is('roles*') ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}">@lang('user.Roles')</a>
                    </li>
                </ul>
            </li>
            @endif
            <!-- End ACL menu -->
            <!-- Category Menu -->
            <li class="treeview {{ Request::is('category*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-tags"></i>
                    <span>@lang('common_lang.Category')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left"></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('category*') || Request::is('category*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('category/list*') ? 'active' : '' }}">
                        <a href="{{ route('CategoryController.viewList') }}">@lang('common_lang.Categories_List')</a>
                    </li>
                </ul>
            </li>
            <!-- Aspect Menu -->
            <li class="treeview {{ Request::is('aspect*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-cubes"></i>
                    <span>@lang('common_lang.Aspect')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left"></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('aspect*') || Request::is('aspect*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('aspect/list*') ? 'active' : '' }}">
                        <a href="{{ route('AspectController.viewList') }}">@lang('common_lang.Aspects_List')</a>
                    </li>
                </ul>
            </li>
            <!-- Product Menu -->
            <li class="treeview {{ Request::is('product*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-product-hunt"></i>
                    <span>@lang('common_lang.Product')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left"></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('product*') || Request::is('product*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('product/list*') ? 'active' : '' }}">
                        <a href="{{ route('ProductController.viewList') }}">@lang('common_lang.Products_List')</a>
                    </li>
                    @if(Sentinel::getUser()->inRole('administrator'))
                    <li class="{{ Request::is('product/upload-panel') ? 'active' : '' }}">
                        <a href="{{ route('ProductController.viewUploadPanel') }}">@lang('common_lang.Products_Manager_Panel')</a>
                    </li>
                    @endif
                </ul>
            </li>
            <!-- Comment menu -->
            @if(Sentinel::getUser()->inRole('administrator'))
            <li class="treeview {{ Request::is('comment*') || Request::is('comment*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-comments"></i>
                    <span>@lang('common_lang.Comment')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('comment*') || Request::is('comment*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('sentence/upload*') ? 'active' : '' }}">
                        <a href="{{ route('CommentController.index') }}">@lang('common_lang.Comments_Manager_Panel')</a>
                    </li>
                </ul>
            </li>
            @endif
            <!-- Sentence menu -->
            @if(Sentinel::getUser()->inRole('administrator'))
            <li class="treeview {{ Request::is('sentence*') || Request::is('sentence*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-file-text"></i>
                    <span>@lang('common_lang.Sentence')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('sentence*') || Request::is('sentence*') ? 'menu-open' : '' }}">
                </ul>
            </li>

            <!-- Word menu -->
            <li class="treeview {{ Request::is('word*') || Request::is('word*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-file-word-o"></i>
                    <span>@lang('common_lang.Word')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('word*') || Request::is('word*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('word/word-manager*') ? 'active' : '' }}">
                        <a href="{{ route('WordController.viewWordManagerPanel') }}">@lang('common_lang.Words_Manager_Panel')</a>
                    </li>
                    <li class="{{ Request::is('word/list*') ? 'active' : '' }}">
                        <a href="{{ route('WordController.viewList') }}">@lang('common_lang.Words_List')</a>
                    </li>
                </ul>
            </li>
            @endif
            <!-- Result menu -->
            @if(Sentinel::getUser()->inRole('administrator'))
            <li class="treeview {{ Request::is('statistics*') || Request::is('statistics*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-bar-chart"></i>
                    <span>@lang('common_lang.Statistics')</span>
                    <span class="pull-left-container"><i class="fa fa-angle-left pull-left "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('statistics*') || Request::is('statistics*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('statistics/evaluation_results') ? 'active' : '' }}">
                        <a href="{{ route('StatisticsController.viewResults') }}">@lang('common_lang.Evaluation_Results')</a>
                    </li>
                </ul>
            </li>
            @endif
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>