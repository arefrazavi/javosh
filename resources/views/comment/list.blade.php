@extends('layouts.master-admin')
@section('title', trans('common_lang.Comments_List'))
@section('previous_page', trans('common_lang.Back_To'). " " . trans('common_lang.Products_List'))

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <div class="box box-intro">
                <div class="box box-header">
                    <h5> @lang('common_lang.Title') @lang('common_lang.Product')</h5>
                    <div class="box-body"> <a href="{{route("ProductController.viewProduct", $product->id)}}"> {{ $product->title }} </a> </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default" id="box-comments">
                <div class="box-body">
                    <table id="comment-list-table" class="table table-responsive" cellspacing="1" width="100%">
                        <thead>
                        <tr>
                            <th>@lang('common_lang.Id')</th>
                            <th>@lang('common_lang.Text')</th>
                            <th>@lang('common_lang.Positive_Points')</th>
                            <th>@lang('common_lang.Negative_Points')</th>
                            <th>@lang('common_lang.Sentences')</th>
                        </tr>
                        </thead>
                        <tfoot>
                        <tr>
                            <th>Id</th>
                            <th>text</th>
                            <th>Positive Points</th>
                            <th>Negative Points</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        $("#comment-list-table").DataTable({
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
                },
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('CommentController.getList') !!}',
                method: 'POST',
                data: {
                    "datatable": true,
                    'productId': "{{ $product->id }}",
                    "_token": "{{ csrf_token() }}"
                },
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'text', class: 'rtl-text', width: '40%', searchable: true, name: 'text'},
                {data: 'positive_points', class: 'rtl-text', searchable: true, name: 'positive_points'},
                {data: 'negative_points', class: 'rtl-text', searchable: true, name: 'negative_points'},
                {
                    data: "id",
                    render: function (id) {
                        var sentenceListRoute = '{{route("SentenceController.viewList", "id")}}';
                        sentenceListRoute = sentenceListRoute.replace("id", id);
                        var button = '<a class="btn btn-primary" title="Show comments list" href="' + sentenceListRoute + '">@lang('common_lang.Sentences_List')</a>';

                        return button;
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
        $("#box-comments").on("click", ".btn-apply-changes", function (e) {
            var comments = [];
            $("select[name='gold-selected']").each(function () {
                var goldStandard = $(this).val();
                var commentId = $(this).data('comment-id');
                var sentimentPolarity = $(this).parent('td').siblings('td').children("select[name='sentiment-polarity']").val();
                var comment = [commentId, goldStandard, sentimentPolarity];
                comments.push(comment);
            });
            $.ajax({
                url: "{{ route('CommentController.updateComments') }}",
                data: {
                    comments: comments,
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