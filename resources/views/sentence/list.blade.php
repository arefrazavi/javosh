@extends('layouts.master-admin')
@section('title', trans('common_lang.Sentences_List'))
@section('previous_page', trans('common_lang.Back_To'). " " . trans('common_lang.Comments_List'))
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-info">
                <div class="box box-header">
                    <h5> @lang('common_lang.Text') @lang('common_lang.Comment')</h5>
                <div class="box-body"> {{ $comment->text }}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default" id="box-sentences">
                <div class="box-body">
                    <table id="sentence-list-table" class="table table-responsive" cellspacing="1" width="100%">
                        <thead>
                        <tr>
                            <th>@lang('common_lang.Id')</th>
                            <th>@lang('common_lang.Text')</th>
                            <th>@lang('common_lang.Aspect_Selection_For_Gold_Standard')</th>
                            <th>@lang('common_lang.Polarity')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>text</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="form-horizontal block">
                    <button class="btn btn-success center-block btn-apply-changes">@lang('common_lang.Apply_Changes')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    var aspects = {!! json_encode($aspects) !!};

    $(function () {
        $("#sentence-list-table").DataTable({
            "language": {
                "emptyTable": "No data available in table",
                "lengthMenu": "@lang('common_lang.Show_Entries_No') _MENU_ ",
                "zeroRecords": "@lang('common_lang.Nothing_found')",
                "info": "@lang('common_lang.Showing_Page') _PAGE_ @lang('common_lang.of') _PAGES_",
                "infoEmpty": "@lang("common_lang.No_Records_Available")",
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
                },
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('SentenceController.getList') !!}',
                method: 'POST',
                data: {
                    "datatable": true,
                    'commentId': "{{ $comment->id }}",
                    "_token": "{{ csrf_token() }}"
                },
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'text', class: 'rtl-text', searchable: true, name: 'text'},
                {
                    data: {aspect_id: "aspect_id", id: "id"},
                    render: function (data) {
                        var selectField = "<select data-sentence-id=" + data.id + " name='aspect_id' class='form-control'>";
                        selectField += "<option value='0'>@lang('common_lang.Select_an_aspect')</option>";
                        $.each(aspects, function (key, aspect) {
                            var selected = '';
                            if (aspect.id == data.aspect_id) {
                                selected = 'selected';
                            }
                            selectField += "<option " + selected + " value='" + aspect.id + "'> " + aspect.title + " </option>";

                        });
                        selectField += "</select>";

                        return selectField;
                    }
                },
                {
                    data: {polarity: "polarity", id: "id"},
                    render: function (data) {
                        var selectField = "<select data-sentence-id=" + data.id + " name='polarity' class='form-control'>";
                        var polarities = {
                            "-1": "@lang('common_lang.Negative')",
                            "0": "@lang('common_lang.Neutral')",
                            "1": "@lang('common_lang.Positive')"
                        };
                        $.each(polarities, function (polarity, text) {
                            var selected = '';
                            if (polarity == data.polarity) {
                                selected = 'selected';
                            }
                            selectField += "<option " + selected + " value='" + polarity + "'>" + text + "</option>";
                        });
                        selectField += "</select>";

                        return selectField;
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
    });

    $(document).ready(function () {
        $("#box-sentences").on("click", ".btn-apply-changes", function (e) {
            var sentences = [];
            $("select[name='aspect_id']").each(function () {
                var aspectId = $(this).val();
                var sentenceId = $(this).data('sentence-id');
                var polarity = $(this).parent('td').siblings('td').children("select[name='polarity']").val();
                var sentence = [sentenceId, aspectId, polarity];
                sentences.push(sentence);
            });
            console.log(sentences);
            $.ajax({
                url: "{{ route('SentenceController.updateGoldSentences') }}",
                data: {
                    sentences: sentences,
                    "_token": "{{ csrf_token() }}"
                },
                type: "POST"
            })
                .done(function (result) {
                    alert(result.message);
                });
        });
    });
</script>
@endpush