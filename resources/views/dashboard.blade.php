@extends('layouts.master-admin')
@section('title', trans('common_lang.Dashboard'))
@section('content')
    <div class='row'>
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div>
                <a class="block text-center" href="{{ route("ProductController.viewList", ['categoryId' => 4]) }}">
                    <div class="cat-img-wrapper img-bordered">
                        <img src="{{ asset('images/cellphones.jpg') }}" class="img-responsive cat-img"/>
                    </div>
                    <h4 class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Mobile')</h4>
                </a>
            </div>
            <div class="center-block text-center btn-lucky-wrapper">
                <a class="btn btn-lucky"
                   href="{{ route("ProductController.viewList", ['categoryId' => 4, 'limit' => 10]) }}">
                    @lang('common_lang.Summarize_10_lucky_products')
                </a>
            </div>
        </div><!-- /.col -->
        <div class='col-md-6 col-sm-6 col-xs-12'>
            <div>
                <a class="block text-center" href="{{ route("ProductController.viewList", ['categoryId' => 6]) }}">
                    <div class="cat-img-wrapper img-bordered">
                        <img src="{{ asset('images/laptops.jpg') }}" class="img-responsive cat-img"/>
                    </div>
                    <h4 class="text-center">@lang('common_lang.Products_List') @lang('common_lang.Category') @lang('common_lang.Laptop')</h4>
                </a>
            </div>
            <div class="center-block text-center btn-lucky-wrapper">
                <a class="btn btn-lucky"
                   href="{{ route("ProductController.viewList", ['categoryId' => 6, 'limit' => 10]) }}">
                    @lang('common_lang.Summarize_10_lucky_products')
                </a>
            </div>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
