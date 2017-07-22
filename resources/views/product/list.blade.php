@extends('layouts.master-admin')
@section('title', trans('common_lang.Products_List'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="product-list-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>@lang('common_lang.Id')</th>
                                <th>@lang('common_lang.Title')</th>
                                <th>@lang('common_lang.Category')</th>
                                <th>@lang('common_lang.Gold_Summary')</th>
                                <th>@lang('common_lang.Comments')</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Category</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script type="text/javascript">
    $(function () {
        $("#product-list-table").DataTable({
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
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('ProductController.getList', $categoryId) !!}',
                method: 'POST',
                data: {
                    "datatable": true,
                    "categoryId": "{{$categoryId }}",
                    "_token": "{{ csrf_token() }}"
                },
            },
            columns: [
                {data: 'id', class: 'rtl-text', name: 'id'},
                {
                    data: {title: 'title', id: 'id'}, class: 'rtl-text text-wrap',
                    render: function (data) {
                        var commentListRoute = '{{route("ProductController.viewProduct", "id")}}';
                        commentListRoute = commentListRoute.replace("id", data.id);
                        return "<a href='" + commentListRoute + "'>" + data.title + "</a>";
                    }
                },
                {
                    data: {category: "category"}, class: 'rtl-text',
                    render: function (data) {
                        return "<a href='#'>" + "<span title='" + data.category.title + "'>" + data.category.alias + "</span>" + "</a>";
                    }
                },
                {
                    data: "id",
                    render: function (id) {
                        var suggestRoute = '{{route("ProductController.viewGoldSummaryRecommendation", "id")}}';
                        suggestRoute = suggestRoute.replace("id", id);
                        return "<a class='gold-text' href='" + suggestRoute + "'> @lang('common_lang.Suggest') </a>";
                    }
                },
                {
                    data: "id",
                    render: function (id) {
                        var commentListRoute = '{{route("CommentController.viewList", "id")}}';
                        commentListRoute = commentListRoute.replace("id", id);
                        var button = '<a class="btn btn-primary" title="Show comments list" href="' + commentListRoute + '">@lang('common_lang.Comments_List')</a>';

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

</script>
@endpush