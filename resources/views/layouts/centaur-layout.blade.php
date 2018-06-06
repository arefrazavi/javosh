@extends('layouts.master-admin')

@section("content")
<div class="container-fluid">
    @include('Centaur::notifications')
    @yield("inner-content")
</div>
@endsection

@push('scripts')
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- Latest compiled and minified Bootstrap JavaScript -->
<script src="{{ asset ("admin-lte/bootstrap/js/bootstrap-rtl.js") }}" type="text/javascript"></script>
<!-- Restfulizer.js - A tool for simulating put,patch and delete requests -->
<script src="{{ asset('restfulizer.js') }}"></script>
<script src="{{ asset ("js/common.js") }}"></script>
@endpush