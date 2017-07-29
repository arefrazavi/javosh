<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <meta name="google-site-verification" content="e02PAFaPzEXO676NK3hafXtKZ-pvuomVwfs-H8KB5b4">

    <title> @lang("common_lang.Javosh") - @yield('title')</title>

    <link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/bootstrap-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/bootstrap-theme-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/font-awesome.min.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/bootstrap/css/ionicons.min.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/dist/css/AdminLTE-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("admin-lte/dist/css/skins/_all-skins-rtl.css") }}">
    <link rel="stylesheet" href="{{ asset("css/jquery.dataTables.1.10.15.min.css") }}">
    <link rel="stylesheet" href="{{ asset("css/responsive.dataTables.min.css") }}">
    <link rel="stylesheet" href="{{ asset("css/javosh.css") }}">
    <link rel="stylesheet" href="{{ asset("css/scroller.dataTables.css") }}">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page commentfile:// -->
    <!--[if lt IE 9]>
    <script src="{{ asset('admin-lte/bootstrap/js/respond.min.js') }}"></script>
    <script src="{{ asset('admin-lte/bootstrap/js/html5shiv.min.js') }}"></script>
    <![endif]-->
</head>
<body class="hold-transition skin-black sidebar-mini sidebar-collapse">
<div class="wrapper">
    <!-- Header -->
@include('include.header')
<!-- Sidebar -->
@include('include.sidebar')
<!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="row">
                <div class="col-lg-10">
                    <h4>
                        @yield('title')
                        <small>@yield('description')</small>
                    </h4>
                </div>
                <div class="col-lg-2">
                    <!-- You can dynamically generate breadcrumbs here -->
                    <ol class="breadcrumb">
                        <a class="previous-page-link" href="{{ url()->previous() }}" class="">
                            @yield('previous_page', trans("common_lang.Back_To_Previous_Page"))
                            <span class="fa fa-chevron-left"></span>
                        </a>
                    </ol>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <!-- Your Page Content Here -->
            @yield('content')
        </section><!-- /.content -->
    </div><!-- /.content-wrapper -->
    <!-- Footer -->
    @include('include.footer')
</div><!-- ./wrapper -->

<!-- Admin LTE REQUIRED JS SCRIPTS -->
<script src="{{ asset ("admin-lte/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script src="{{ asset ("admin-lte/bootstrap/js/bootstrap-rtl.js") }}" type="text/javascript"></script>
<script src="{{ asset ("admin-lte/dist/js/modified-app.js") }}" type="text/javascript"></script>

<!-- DataTables REQUIRED JS SCRIPTS -->
<script src="{{ asset ("js/jquery.dataTables.1.10.15.min.js") }}"></script>
<script src="{{ asset ("js/dataTables.responsive.min.js") }}"></script>
<script src="{{ asset ("js/dataTables.scroller.min.js") }}"></script>
<!-- Bootstrap JavaScript -->
<!-- App scripts -->

<script type="application/javascript">
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    })

</script>

@stack('scripts')
</body>
</html>
