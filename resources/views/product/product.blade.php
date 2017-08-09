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
                <div class="box box-intro rtl-text box-description">
                    {{ $product->description }}
                </div>
            </div>
            <div class="box box-detail">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-file-text" style="color:green"></i>
                    <h3 class="box-title">
                        @lang('common_lang.Comments_Summary')
                    </h3>
                    <div class="pull-left">
                        <b><a href="{{ route("CommentController.viewList", $product->id) }}">@lang("common_lang.Comments_List")</a>
                            | </b>
                        <b><a class="gold-text"
                              href="{{ route("ProductController.viewGoldSummaryRecommendation", $product->id) }}">@lang("common_lang.Gold_Summary_Suggestion")</a></b>
                    </div>
                </div>
                <div id="summary-box" class="box box-body list-wrapper black-text">
                    <div>
                        <select class="form-control prussian-blue" name="aspect_id">
                            <option value="0">@lang("common_lang.Select_an_aspect")</option>
                            @foreach($aspects as $aspect)
                                <option value="{{ $aspect->id }}">{!! $aspect->title !!} </option>
                            @endforeach
                        </select>
                    </div>
                    @foreach($aspects as $aspect)
                        <div id="aspect-{{$aspect->id}}" class="box box-default aspect-summary-box hidden">
                            <div class="black-text cursor-hand" style="text-align: center">
                                <b>@lang('common_lang.keywords')</b> :
                                <small> {{ $aspect->keywords }} </small>
                            </div>
                            <div class="box-body box-summary">
                                <h4>@lang("common_lang.Gold_Summary")</h4>
                                <ul class="gold list-style-circle text-list">
                                    @foreach($goldSummaries[$aspect->id] as $goldSentence)
                                        <li>
                                                <span class="rtl-text" data-toggle="tooltip" data-placement="top"
                                                      title="{{ $goldSentence->comment_text }}">{{ $goldSentence->text }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div>
                                    <h4>@lang("common_lang.Automated_Summary")</h4>
                                    <div>
                                        <select class="form-control prussian-blue" name="method_id"
                                                data-aspect-id="{{ $aspect->id }}">
                                            <option value="0">@lang("common_lang.Select_a_method")</option>
                                            @foreach($methods as $method)
                                                <option value="{{ $method->id }}">{!! $method->alias !!} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="list-wrapper">
                                        <ul class="automated-summary-list list-style-circle text-list sky-blue">
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="application/javascript">
    $(document).ready(function () {

        $("#summary-box").on("change", "select[name='aspect_id']",
            function () {
                var aspectId = $(this).val();
                var aspectBoxId = "#aspect-" + aspectId;
                var aspectBox = $(aspectBoxId);

                $(".aspect-summary-box").addClass("hidden");

                if (aspectId == "0") {
                    return false;
                }

                aspectBox.removeClass("hidden");
            }
        );

        $("#summary-box").on("change", "select[name='method_id']",
            function () {
                var aspectId = $(this).data("aspect-id");
                var summaryList = $(this).parent("div").siblings("div.list-wrapper").children(".automated-summary-list");
                var methodId = $(this).val();
                summaryList.text("");

                if (methodId == "0") {
                    return false;
                }

                $.ajax({
                    url: "{{ route('SummaryController.getList') }}",
                    data: {
                        aspectId: aspectId,
                        methodId: methodId,
                        productId: "{{ $product->id  }}",
                        "_token": "{{ csrf_token() }}"
                    },
                    type: "POST"
                })
                    .done(function (result) {
                        var list = '';
                        $.each(result, function (key, sentence) {
                            list += "<li>";
                            list += "<span class='rtl-text' data-toggle='tooltip' data-placement='top' title='" + sentence.comment_text + "'>";
                            list += sentence.text;
                            list += "</span></li>";
                        });
                        summaryList.append(list);
                    });

            }
        );

    });
</script>
@endpush