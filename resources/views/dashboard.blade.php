@extends('layouts.master-admin')
@section('title', trans('common.Admin_Panel'))
@section('content')
    <div class='row'>
        <div class='col-md-6'>
            <!-- Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">@lang('common.Most_Visited') @lang('common.Pages')</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                        <button class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <ul>
                       <li><a href="{{ route('CategoryController.viewList') }}">@lang('common.Categories_List')</a></li>
                       <li><a href="{{ route('ProductController.viewList') }}">@lang('common.Products_List')</a></li>
                    </ul>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection
