@extends('layouts.master-admin')
@section('title', trans('common_lang.Admin_Panel'))
@section('content')
    <div class='row'>
        <div class='col-md-6'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('common_lang.Most_Visited') @lang('common_lang.Pages')</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <ul>
                       <li><a href="{{ route('CategoryController.viewList') }}">@lang('common_lang.Categories_List')</a></li>
                       <li><a href="{{ route('ProductController.viewList') }}">@lang('common_lang.Products_List')</a></li>
                    </ul>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
