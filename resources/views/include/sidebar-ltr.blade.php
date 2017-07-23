<!-- Left side column. contains the logo and sidebar -->
<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- Sidebar user panel (optional) -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ asset("admin-lte/dist/img/user-img.png") }}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>{{ Sentinel::getUser()->first_name }} {{ Sentinel::getUser()->last_name }}</p>
                <!-- Status -->
                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>

        <ul class="sidebar-menu">
            <!-- Optionally, you can add icons to the links -->
            <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                <a href="/dashboard">
                    <i class="fa fa-tachometer"></i><span>Dashboard</span>
                </a>
            </li>
            <!-- ACL menu -->
            <li class="treeview {{ Request::is('users*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>User</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('users*') || Request::is('roles*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}">Users</a>
                    </li>
                    <li class="{{ Request::is('roles*') ? 'active' : '' }}">
                        <a href="{{ route('roles.index') }}">Roles</a>
                    </li>
                </ul>
            </li>
            <!-- End ACL menu -->
            <!-- Product Menu -->
            <li class="treeview {{ Request::is('product*') || Request::is('roles*') ? 'active' : '' }}">
                <a href="#">
                    <i class="fa fa-users"></i>
                    <span>Product</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('product*') || Request::is('product*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('product/list*') ? 'active' : '' }}">
                        <a href="{{ route('ProductController.viewList') }}">Product List</a>
                    </li>
                    <li class="{{ Request::is('product/upload-panel') ? 'active' : '' }}">
                        <a href="{{ route('ProductController.viewUploadPanel') }}">Upload Panel</a>
                    </li>
                </ul>
            </li>
            <!-- Comment menu -->
            <li class="treeview {{ Request::is('comment*') || Request::is('comment*') ? 'active' : '' }}">
                <a href="#">
                    <i class="glyphicon glyphicon-home"></i>
                    <span>Comment</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('comment*') || Request::is('comment*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('sentence/upload*') ? 'active' : '' }}">
                        <a href="{{ route('CommentController.index') }}">Upload Panel</a>
                    </li>
                </ul>
            </li>
            <!-- Sentence menu -->
            <li class="treeview {{ Request::is('sentence*') || Request::is('sentence*') ? 'active' : '' }}">
                <a href="#">
                    <i class="glyphicon glyphicon-home"></i>
                    <span>Sentence</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('sentence*') || Request::is('sentence*') ? 'menu-open' : '' }}">
                </ul>
            </li>
            <!-- Word menu -->
            <li class="treeview {{ Request::is('word*') || Request::is('word*') ? 'active' : '' }}">
                <a href="#">
                    <i class="glyphicon glyphicon-home"></i>
                    <span>Word</span>
                    <span class="pull-right-container"><i class="fa fa-angle-left pull-right "></i></span>
                </a>
                <ul class="treeview-menu {{ Request::is('word*') || Request::is('word*') ? 'menu-open' : '' }}">
                    <li class="{{ Request::is('word/word-manager*') ? 'active' : '' }}">
                        <a href="{{ route('WordController.viewWordManagerPanel') }}">Word Manager Panel</a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>