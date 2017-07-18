@extends('layouts.master-admin')
@section('title', trans('common_lang.Gold_Summary_Suggestion'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-info rtl-text box-description">
                <h5> <i class="fa fa-info-circle"></i> @lang('common_lang.Title') @lang('common_lang.Product')</h5>
                <div class="box-body"> <a href="{{route("ProductController.viewProduct", $product->id)}}"> {{ $product->title }} </a> </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="box-header" style="cursor: move;">
                <div class="form-group">
                    <select class="form-control gold" id="aspect_id" name="aspect_id">
                        <option value="">@lang("common_lang.Select_an_aspect")</option>
                        @foreach($aspects as $aspect)
                            <option value="{{ $aspect->id }}">{!! $aspect->title !!}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="box box-default">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="sentence-list-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th class="hidden">@lang('common_lang.af')</th>
                                <th class="hidden">@lang('common_lang.Text')</th>
                                <th class="hidden">@lang('common_lang.Aspect_Selection_For_Gold_Standard')</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    var aspectId = 0;
    $(document).ready(function () {
        $("select[name='aspect_id']").on("change",
            function () {
                var table = $('#sentence-list-table').DataTable();
                table.destroy();
                aspectId = $(this).val();
                $("#sentence-list-table").DataTable({
                    "language": {
                        "emptyTable": "No data available in table",
                        "lengthMenu": "@lang('common_lang.Show_Entries_No') _MENU_ ",
                        "zeroRecords": "@lang('common_lang.Nothing_found')",
                        "info": "@lang('common_lang.Showing_Page') _PAGE_ @lang('common_lang.of') _PAGES_",
                        "infoEmpty": "No records available",
                        "loadingRecords": "@lang('common_lang.loadingRecords')",
                        "processing": "@lang('common_lang.Processing...')",
                        "search": "@lang('common_lang.Search')",
                        "paginate": {
                            "first": "@lang('common_lang.First')",
                            "last": "@lang('common_lang.Last')",
                            "next": "@lang('common_lang.Next')",
                            "previous": "@lang('common_lang.Previous')"
                        },
                        "aria": {
                            "sortAscending": ": activate to sort column ascending",
                            "sortDescending": ": activate to sort column descending"
                        }
                    },
                    order: [[0, 'desc']],

                    searching: false,
                    serverSide: true,
                    scrollY: 600,
                    info: false,
                    scroller: {
                        loadingIndicator: true
                    },
                    ajax: {
                        url: '{!! route('ProductController.getGoldSummaryRecommendation') !!}',
                        method: 'POST',
                        data: {
                            "datatable": true,
                            "aspectId": aspectId,
                            "productId": "{{ $product->id }}",
                            "_token": "{{ csrf_token() }}"
                        },
                    },
                    columns: [
                        {data: 'weighted_aspect_freq', name: 'weighted_aspect_freq', "visible": false,},
                        {data: {text: 'text', comment_text: 'comment_text'}, class: 'rtl-text',
                            render: function(data) {
                                var output = '<span class="rtl-text" data-toggle="tooltip" data-placement="top" title="' + data.comment_text + '">';
                                output += data.text + '</span>';
                                return output;
                            }
                        },
                        {
                            data: {id: "id", aspect_id: "aspect_id"}, class: "suggestBtnCol",
                            render: function (data) {
                                var suggestBtns;
                                var startWrapper = "<div data-sentence-id='" + data.id + "'> ";
                                var positiveSuggestBtn = "<i title='@lang('common_lang.Positive')' data-polarity = '1' class='fa fa-plus-circle gold-suggest gold-suggest-disabled'></i> ";
                                var neutralSuggestBtn = "<i title='@lang('common_lang.Neutral')' data-polarity = '0' class='fa fa-dot-circle-o gold-suggest gold-suggest-disabled'></i> ";
                                var negativeSuggestBtn = "<i title='@lang('common_lang.Negative')' data-polarity = '-1' class='fa fa-minus-circle gold-suggest gold-suggest-disabled'></i> ";
                                var endWrapper = "</div>";
                                if (data.aspect_id == aspectId) {
                                    switch (data.polarity){
                                        case "1":
                                            positiveSuggestBtn = "<i title='@lang('common_lang.Positive') 'data-polarity = '1'  class='fa fa-plus-circle gold-suggest gold-suggest-enabled'></i> ";
                                            break;
                                        case "0":
                                            neutralSuggestBtn = "<i title='@lang('common_lang.Neutral')' data-polarity = '0' class='fa fa-dot-circle-o gold-suggest gold-suggest-enabled'></i> ";
                                            break;
                                        case "-1":
                                            negativeSuggestBtn = "<i title='@lang('common_lang.Negative')' data-polarity = '-1' class='fa fa-minus-circle gold-suggest gold-suggest-enabled'></i> ";
                                            break;
                                    }
                                }
                                suggestBtns = startWrapper + positiveSuggestBtn + neutralSuggestBtn + negativeSuggestBtn + endWrapper;

                                return suggestBtns;
                            }
                        }
                    ],
                    initComplete: function () {
                        this.api().columns().every(function () {
                            var column = this;
                            var input = document.createElement("input");
                            input.setAttribute('class', 'form-control');
                            $(input).appendTo($(column.footer()).empty())
                                .on('change', function () {
                                    column.search($(this).val()).draw();
                                });
                        });
                    }
                });
            }
        );

        $("#sentence-list-table").on("click", ".gold-suggest", function (e) {
            var targetSuggestBtn = $(this);
            var previousGoldSibling = targetSuggestBtn.siblings(".gold-suggest.gold-suggest-enabled");
            var sentenceId = targetSuggestBtn.parent('div').data('sentence-id');
            var polarity = targetSuggestBtn.data('polarity');
            var goldRequest = {'sentenceId': sentenceId, 'aspectId': aspectId, 'polarity': polarity, 'action': 0};
            if ($(this).hasClass("gold-suggest-disabled")) {
                goldRequest.action = 1; //1 = Add
            }
            console.log(goldRequest);
            $.ajax({
                url: "{{ route('SentenceController.updateSentenceGoldStatus') }}",
                data: {
                    goldRequest: goldRequest,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST"
            })
                .done(function (result) {
                    if (result.success) {
                        if (goldRequest.action) {
                            previousGoldSibling.removeClass("gold-suggest-enabled");
                            previousGoldSibling.addClass("gold-suggest-disabled");
                        }
                        targetSuggestBtn.toggleClass("gold-suggest-disabled");
                        targetSuggestBtn.toggleClass("gold-suggest-enabled");
                    } else {
                        alert(result.message);
                    }
                });
        });
    });

</script>
@endpush