@extends('layouts.master-admin')
@section('title', trans('common.Categories_List'))

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="category-list-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>@lang('common.Id')</th>
                                <th>@lang('common.Title')</th>
                                <th>@lang('common.ProductsCount')</th>
                                <th>@lang('common.CommentsCount')</th>
                                <th>@lang('common.Parent_Title')</th>
                                <th>@lang('common.Aspects_List')</th>
                                <th>@lang('common.Products_List')</th>
                            </tr>
                            </thead>
                            <tfoot>
                            <tr>
                                <th>@lang('common.Id')</th>
                                <th>@lang('common.Title')</th>
                                <th>@lang('common.ProductsCount')</th>
                                <th>@lang('common.CommentsCount')</th>
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
        $("#category-list-table").DataTable({
            "language": {
                "emptyTable": "No data available in table",
                "lengthMenu": "@lang('common.Show_Entries_No') _MENU_ ",
                "zeroRecords": "@lang('common.Nothing_found')",
                "info": "@lang('common.Showing_Page') _PAGE_ @lang('common.of') _PAGES_",
                "infoEmpty": "No records available",
                "loadingRecords": "@lang('common.loadingRecords')",
                "processing": "@lang('common.Processing...')",
                "search": "@lang('common.Search')",
                "paginate": {
                    "first": "@lang('common.First')",
                    "last": "@lang('common.Last')",
                    "next": "@lang('common.Next')",
                    "previous": "@lang('common.Previous')"
                },
                "aria": {
                    "sortAscending": ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: '{!! route('CategoryController.getList') !!}',
                method: 'POST',
                data: {
                    "datatable": true,
                    "_token": "{{ csrf_token() }}"
                },
            },
            columns: [
                {data: 'id', class: 'rtl-text', name: 'id'},
                {data: 'alias', class: 'rtl-text', name: 'alias'},
                {data: "productsCount", name: 'productsCount'},
                {data: "commentsCount", name: 'commentsCount'},
                {data: "parent_alias", class: 'rtl-text', name: 'parent_alias'},
                {data: "id",
                    render: function (id) {
                        var aspectListRoute = '{{route("AspectController.viewList", "id")}}';
                        aspectListRoute = aspectListRoute.replace("id", id);
                        var button = '<a class="btn btn-info" title="@lang('common.Aspects_List')" href="'+ aspectListRoute +'"><i class="fa fa-cubes"></i> @lang('common.Aspects_List')</a>';

                        return button;
                    }
                },
                {data: "id",
                    render: function (id) {
                        var productListRoute = '{{route("ProductController.viewList", "id")}}';
                        productListRoute = productListRoute.replace("id", id);
                        var button = '<a class="btn btn-primary" title="@lang('common.Products_List')" href="'+ productListRoute +'"><i class="fa fa-tags"></i> @lang('common.Products_List')</a>';

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