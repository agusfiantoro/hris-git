@extends('adminlte::page')
@section('title', 'Dashboard Asset')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard Asset</h1>
            </div>
            <div class="col-sm-2"></div>
            <div class="col-sm-4 justify-content-end">
                <div class="input-group input-group-sm">
                    <select id="data-view" class="form-control form-control-sm " style="height: 100%;" ></select>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
    }
    .select2-container--open {
        z-index: 1650
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

    legend {
        font-size: 10pt;
    }

    .error {
        color: #ff0000;
        font-weight: 600;
    }
</style>
<section class="content pb-3">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="card card-row card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">Asset Summary</h3>
                        <div class="card-tools">
                            <button type="button" id="collaps_user_information" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                            <i class="fas fa-minus collaps_user_information_icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row container">
                                    <select class="chart-types" id="chart-type-primary" style="width:100%"></select>
                                </div>
                                <div>
                                    <canvas id="primaryChart" style="height: 40vh;"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row container">
                                    <select class="chart-types" id="chart-type-secondary" style="width:100%"></select>
                                </div>
                                <div>
                                    <canvas id="secondaryChart" style="height: 40vh;"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-12" >
                                <div class="small-box bg-info switch-chart" chart="total_asset_qty" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Yearly Asset Qty</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total_asset_qty" style="font-size:20px; ">
                                            <i id="loading_total_asset_qty" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-danger switch-chart" chart="total_outstanding_assets_by_group" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Total Outstanding Assets</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total_outstanding_assets_by_group" style="font-size:20px; ">
                                            <i id="loading_total-net-book" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-success switch-chart" chart="total_assets_by_group" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Total Asset Qty</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total_assets_by_group" style="font-size:20px; ">
                                            <i id="loading_total_assets_by_group" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-lg-3 col-12">
                                <div class="small-box bg-primary switch-chart" chart="total_original_cost_by_group" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Assets by Group and Branch</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total-original-cost" style="font-size:20px; ">
                                            <i id="loading_total-original-cost" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card card-row card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">Assets by Group and Branch</h3>
                        <div class="card-tools">
                            <button type="button" id="collaps_user_information" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                            <i class="fas fa-minus collaps_user_information_icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <button id="advanced" type="button" class="btn btn-default mb-3">Advanced Search</button>
                        <table id="assets_group_branch" class="table table-hover table-bordered table-striped"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script type="text/javascript">
    @include('assets.advanced_search')

    const chartContext = document.getElementById('primaryChart');
    const secondaryChartContext = document.getElementById('secondaryChart');
    let primaryChartCurrentKey = null;
    let secondaryChartCurrentKey = null;
    let totalMonthlyPurchasedAssets = [];
    let chartDataY = [];
    let chartDataX = [];
    let secondaryChartDataY = [];
    let secondaryChartDataX = [];
    let primaryChart = null;
    let secondaryChart = null;
    let chartConfig = {
        type: 'bar',
        data: {
            labels: [],
            datasets: []
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    }
    let chartMap = [];

    $('#status').empty().prepend().select2({
        data: [
            { id: 'A', text: 'Active' },
            { id: 'I', text: 'Inactive' },
        ],
    });

    $('.chart-types').select2({
        data: [
            { id: 'bar', text: 'Bar' },
            { id: 'line', text: 'Line' },
            { id: 'pie', text: 'Pie' },
            { id: 'radar', text: 'Radar' },
            // { id: 'scatter', text: 'Scatter' },
        ]
    }).val('bar').trigger('change');

    const switchChart = (chartJsInstance, context, dataKey = null, chartType = 'bar') => {
        if($(context).attr('id') == 'primaryChart') {
            primaryChartCurrentKey = dataKey;
        } else {
            secondaryChartCurrentKey = dataKey;
        }
        if(chartJsInstance) {
            chartJsInstance.destroy();
        }
        let config = {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }
        config.type = chartType;
        if(dataKey) {
            let x = [];
            let y = [];
            let chartData = chartMap.filter((map) => map.key === dataKey);
            config.data.datasets = [];
            if(chartData[0].axis > 1) {
                yArray = [];
                // multiaxis chart
                chartData = Object.entries(chartData[0])[1][1];
                x = [];
                // Axis loop
                Object.entries(chartData).forEach((axisData) => {
                    // label (legend)
                    let label = axisData[0];
                    y = [];
                    // n-th axis dataset loop
                    Object.entries(axisData[1]).forEach((row) => {
                        // only insert data that didn't exist on previous iteration (axis)
                        if(!x.includes(Object.values(row[1])[0])) {
                            x.push(Object.values(row[1])[0]);
                        }
                        // Inserts the data aligned to the label's index
                        y[x.indexOf(Object.values(row[1])[0])] = Object.values(row[1])[Object.values(row[1]).length-1];
                        
                    });
                    config.data.datasets.push({
                        label: label,
                        data: y,
                        borderWidth: 1,
                    });
                })
                config.data.labels = x;
            } else {
                //single axis chart
                chartData = Object.entries(chartData[0])[1][1];
                Object.entries(chartData).forEach((row) => {
                    x.push(Object.values(row[1])[0]);
                    y.push(Object.values(row[1])[Object.values(row[1]).length-1]);
                });
                config.data.datasets.push({
                    label: dataKey.replaceAll("_", " "),
                    data: y,
                    borderWidth: 1,
                });
            }
            config.data.labels = x;
        }
        
        return new Chart(context, config);
        
    }

    const getStats = () => {
        $.ajax({
            url: "{{ route('assets.dashboard.asset.get_stats') }}",
            success: (res) => {
                chartMap = [];
                Object.entries(res.data).forEach((chartData) => {
                    if(chartData[0] == 'total_asset_qty') {
                        chartMap.push({
                            key: chartData[0],
                            value: chartData[1],
                            axis: 2,
                        })
                        return;
                    }
                    chartMap.push({
                        key: chartData[0],
                        value: chartData[1],
                        axis: 1,
                    });
                });
                
                if(res.data.total_asset_qty[moment().format('YYYY')]) {
                    let totalQtyThisYear = 0;
                    Object.values(res.data.total_asset_qty[moment().format('YYYY')]).forEach((assetGroup) => {
                        totalQtyThisYear += assetGroup.total_quantity_asset_permonth;
                    });
                    $('#total_asset_qty').html(totalQtyThisYear);
                }

                if(res.data.total_outstanding_assets_by_group) {
                    let totalOutstanding = 0;
                    res.data.total_outstanding_assets_by_group.forEach((outstandingAsset) => {
                        totalOutstanding += outstandingAsset.count;
                    })
                    $('#total_outstanding_assets_by_group').html(totalOutstanding);
                }

                if(res.data.total_assets_by_group) {
                    let totalAssets = 0;
                    res.data.total_assets_by_group.forEach((asset) => {
                        totalAssets += asset.total_quantity_asset_pergroup;
                    })
                    $('#total_assets_by_group').html(totalAssets);
                }

                if(res.data.total_assets_by_group_and_branch) {
                    assetsGroupBranchTable.clear();
                    res.data.total_assets_by_group_and_branch.forEach((row) => {
                        console.log(row)
                        assetsGroupBranchTable.rows.add([row]).draw();
                    })
                }

                primaryChart = switchChart(primaryChart, chartContext, "total_outstanding_assets_by_group", $('#chart-type-primary').val());
                secondaryChart = switchChart(secondaryChart, secondaryChartContext, "total_assets_by_group", $('#chart-type-secondary').val());
            }
        })
    }

    getStats();

    $(document).on('click', '.switch-chart', function() {
        let chartKey = $(this).attr('chart');
        let chartType = $(`#chart-type-${$(this).attr('context')}`).val();
        if($(this).attr('context') == 'primary') {
            primaryChart = switchChart(primaryChart, chartContext, chartKey, chartType);
        } else {
            secondaryChart = switchChart(secondaryChart, secondaryChartContext, chartKey, chartType);
        }
    });

    $(document).on('change', '.chart-types', function() {
        if($(this).attr('id') == 'chart-type-primary') {
            primaryChart = switchChart(primaryChart, chartContext, primaryChartCurrentKey, $(this).val());
        } else {
            secondaryChart = switchChart(secondaryChart, secondaryChartContext, secondaryChartCurrentKey, $(this).val());
        }
    });

    let assetsGroupBranchTable = $('#assets_group_branch').DataTable({
        processing: true,
        pageLength: 50,
        columnDefs: [
        ],
        responsive: true,
        columns: [
            { data: 'asset_group', name: 'asset_group', title: 'Asset Group'},
            { data: 'branch', name: 'branch', title: 'Branch'},
            { data: 'count', name: 'count', title: 'Count' },
        ],
    });

</script>
@endsection