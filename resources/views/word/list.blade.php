@extends('layouts.master-admin')
@section('title', trans('common.Words_List'))
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box box-default">
                <div class="box-header ui-sortable-handle" style="cursor: move;">
                    <i class="fa fa-list" style="color:green"></i>

                    <h3 class="box-title">@lang('common.Words_List')</h3>
                </div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="word-list-table" class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th>@lang('common.Id')</th>
                                <th>@lang('common.Word')</th>
                                <th>@lang('common.Count')</th>
                                <th>@lang('common.Pos_Tag')</th>
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
    $(function () {
        $("#word-list-table").DataTable({
            "language": {
                "emptyTable": "No data available in table",
                "lengthMenu": "@lang('common.Show_Entries_No') _MENU_ ",
                "zeroRecords": "@lang('common.Nothing_found')",
                "info": "",
                "infoEmpty": "@lang("common.No_Records_Available")",
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
                },
            },
            ordering: false,
            searching: false,
            serverSide: true,
            scrollY: 600,
            scroller: {
                loadingIndicator: true
            },
            ajax: {
                url: '{!! route('WordController.getList') !!}',
                method: 'POST',
                data: {
                    "datatable": true,
                    "_token": "{{ csrf_token() }}"
                },
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'value', searchable: true, name: 'text'},
                {data: 'count', searchable: true, name: 'count'},
                {data: 'pos_tag', searchable: true, name: 'pos_tag'},
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