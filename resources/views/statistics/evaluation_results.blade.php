@extends('layouts.master-admin')
@section('title', trans('common.Evaluation_Results'))
@section('content')
    <div class="row">
        <div class="col-lg-12">
            @foreach($charts as $chart)
                <div class="box box-default">
                    <div class="box-header ui-sortable-handle" style="cursor: move;">
                        <i class="fa fa-info-circle"></i>
                        <h3 class="box-title rtl-text">{{ $chart['category'] }}</h3>
                    </div>
                    <div class="box box-info rtl-text box-description">
                        @foreach($chart['measures'] as $measureChart)
                            <div class="box box-default">
                                <h4 class="box-header aspect-header ltr-text">{{ $measureChart['measureTitle'] }}</h4>
                                <div class="box-body box-summary">
                                    <canvas id="{{  $measureChart['chartId'] }}" width="300" height="100"></canvas>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>
@endsection

@push('scripts')

<script src="{{ asset ("js/Chart.js") }}"></script>
<script src="{{ asset ("js/Chart.bundle.js") }}" type="text/javascript"></script>


<script>
    var charts = {!! json_encode($charts) !!};

    //console.log(charts);

    $.each(charts, function (categoryId, chart) {
        var labels = new Array();
        $.each(chart.aspectTitles, function (aspectId, title) {
            labels.push(title);
        });

        $.each(chart.measures, function (measureId, measure) {
            var chartId = measure.chartId;
            var chartTitle = measure.measureTitle;
            var datasets = new Array();
            $.each(measure.datasets, function (key, dataset) {
                var data = new Array();
                var backgroundColor = new Array();
                $.each(dataset.data, function (key, value) {
                    data.push(value);
                });
                $.each(dataset.backgroundColor, function (key, color) {
                    backgroundColor.push(color);
                });

                var datasetObj = {
                    label: dataset.label,
                    data: data,
                    backgroundColor: backgroundColor,
                };
                datasets.push(datasetObj);
            });

            var chartElement = document.getElementById(chartId);
            var chartJs = new Chart(chartElement, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets,
                    options: {
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                        }
                    }
                }
            });
        });
    });
</script>
@endpush