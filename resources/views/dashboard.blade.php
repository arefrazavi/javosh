@extends('layouts.master-admin')
@section('title', trans('common_lang.Dashboard'))
@section('content')
    <div class='row'>
        <div class='col-md-6'>
            <a class="block" href="{{ route("ProductController.viewList", ['categoryId' => 4]) }}">
                <div><img src="{{ asset('images/cellphones.jpg') }}" class="img-responsive img-bordered"/></div>
                <h4 class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Mobile')</h4>
            </a>
            <a class="block alert alert-lucky text-center" href="{{ route("ProductController.viewList", ['categoryId' => 4, 'limit' => 10]) }}">
                <h5>@lang('common_lang.Summarize_10_lucky_products')</h5>
            </a>
        </div><!-- /.col -->
        <div class='col-md-6'>
            <a class="block" href="{{ route("ProductController.viewList", ['categoryId' => 6]) }}">
                <div><img src="{{ asset('images/laptops.jpg') }}" class="img-responsive img-bordered"/></div>
                <h4 class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Laptop')</h4>
            </a>

            <a class="block alert alert-lucky text-center" href="{{ route("ProductController.viewList", ['categoryId' => 4, 'limit' => 10]) }}">
                <h5>@lang('common_lang.Summarize_10_lucky_products')</h5>
            </a>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
