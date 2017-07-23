@extends('layouts.master-admin')
@section('title', trans('common_lang.Dashboard'))
@section('content')
    <div class='row'>
        <div class='col-md-6'>
                <a class="block" href="{{ route("ProductController.viewList", 4) }}">
                    <div><img src="{{ asset('images/cellphones.jpg') }}" class="img-responsive img-bordered" /></div>
                    <p class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Mobile')</p>
                </a>
        </div><!-- /.col -->
        <div class='col-md-6'>
                <a class="block" href="{{ route("ProductController.viewList", 6) }}">
                    <div><img src="{{ asset('images/laptops.jpg') }}" class="img-responsive img-bordered" /></div>
                    <p class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Laptop')</p>
                </a>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
