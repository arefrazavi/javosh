<!DOCTYPE html>
<html>
<head>
    <title>Aref Crawler @yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap.css')  }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/bootstrap-theme.css')  }}">
    <script type="application/javascript" src="{{ URL::asset('js/bootstrap.js') }}"></script>
</head>
<body>
@section('sidebar')

@show

<div class="container">
    @yield('content')
</div>
</body>
</html>