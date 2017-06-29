@extends('layouts.master-admin')
@section('title', $product->title)
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-info-circle"></i>
                    <h3 class="box-title rtl-text">@lang('common_lang.Product_Introduction')</h3>
                </div>
                <div class="box box-info rtl-text box-description">
                    {{ $product->description }}
                </div>
            </div>
            <div class="box box-success">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-file-text" style="color:green"></i>
                    <h3 class="box-title">
                        @lang('common_lang.Comments_Summary')
                    </h3>
                    <div class="pull-left">
                        <b><a href="{{ route("CommentController.viewList", $product->id) }}" >@lang("common.Comments_List")</a> | </b>
                        <b><a class="gold-text" href="{{ route("ProductController.viewGoldSummaryRecommendation", $product->id) }}">@lang("common.Gold_Summary_Suggestion")</a></b>
                    </div>
                </div>
                @foreach($aspects as $aspect)
                    <div class="box box-default">
                        <h4 class="box-header aspect-header cursor-hand" data-toggle="popover" data-placement="top" title="@lang('common_lang.keywords')" data-content="{{ $aspect->keywords }}">
                            <span> {{ $aspect->title }} </span>
                        </h4>
                        @foreach($summaries[$aspect->id] as $summary)
                            <div class="box-body box-summary">
                                <h4> {{ $summary['method']->alias }}</h4>
                                <ul>
                                    @foreach($summary['summary'] as $goldSentence)
                                        <li class="block">
                                                <span class="rtl-text" data-toggle="tooltip" data-placement="top"
                                                      title="{{ $goldSentence->comment_text }}">{{ $goldSentence->text }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@endpush