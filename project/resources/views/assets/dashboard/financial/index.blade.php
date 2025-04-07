@extends('adminlte::page')
@section('title', 'Dashboard Financial')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard Financial</h1>
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
                        <h3 class="card-title">Asset Financials Summary</h3>
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
                            <div class="col-lg-3 col-12" >
                                <div class="small-box bg-info switch-chart" chart="total_monthly_purchased_assets" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Asset Purchased This Month</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="assets-purchased-this-month" style="font-size:20px; ">
                                            <i id="loading_assets-purchased-this-month" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-success switch-chart" chart="total_net_book_by_group" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Total Net Book</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total-net-book" style="font-size:20px; ">
                                            <i id="loading_total-net-book" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-danger switch-chart" chart="monthly_depreciation_value" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Asset Depreciation This Month</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="asset-depreciation-this-month" style="font-size:20px; ">
                                            <i id="loading_asset-depreciation-this-month" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-primary switch-chart" chart="total_original_cost_by_group" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Total Acquisition Cost</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total-original-cost" style="font-size:20px; ">
                                            <i id="loading_total-original-cost" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-12" >
                                <div class="small-box bg-info switch-chart" chart="total_annually_purchased_assets" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Asset Purchased This Year</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="assets-purchased-this-year" style="font-size:20px; ">
                                            <i id="loading_assets-purchased-this-year" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-primary text-white" chart="total_asset_value" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Total Asset Value</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="total-asset-value" style="font-size:20px; ">
                                            <i id="loading_total-asset-value" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-danger switch-chart" chart="annual_depreciation_value" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Asset Depreciation This Year</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="asset-depreciation-this-year" style="font-size:20px; ">
                                            <i id="loading_asset-depreciation-this-year" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-12">
                                <div class="small-box bg-warning switch-chart text-white" chart="depreciation_by_group" context="secondary">
                                    <a href="#" class="small-box-footer text-bold">Depreciation by Group</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="depreciation-by-group" style="font-size:20px; ">
                                            <i id="loading_depreciation-by-group" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
            chartData = Object.entries(chartData[0])[1][1];
            Object.entries(chartData).forEach((row) => {
                x.push(Object.values(row[1])[0]);
                y.push(Object.values(row[1])[Object.values(row[1]).length-1]);
            });
            
            config.data.labels = x;
            config.data.datasets = [{
                label: dataKey.replaceAll("_", " "),
                data: y,
                borderWidth: 1,
            }];
        }
        return new Chart(context, config);
        
    }

    const getStats = () => {
        $.ajax({
            url: "{{ route('assets.dashboard.financial.get_stats') }}",
            success: (res) => {
                Object.entries(res.data).forEach((chartData) => {
                    chartMap.push({
                        key: chartData[0],
                        value: chartData[1],
                    });
                });
                totalMonthlyPurchasedAssets = res.data.total_monthly_purchased_assets.filter((totalPurchasedAsset) => {
                    return `${totalPurchasedAsset.year}-${totalPurchasedAsset.month}` == moment().format('YYYY-MM');
                });
                $('#loading_assets-purchased-this-month').hide();
                $('#assets-purchased-this-month').html(0);
                $('#asset-depreciation-this-month').text(0);
                $('#total-net-book').text(0);
                $('#total-original-cost').text(0);
                $('#assets-purchased-this-year').text(0);
                $('#asset-depreciation-this-year').text(0);
                $('#depreciation-by-group').text('-');
                $('#total-asset-value').text(0);
                if(totalMonthlyPurchasedAssets?.length > 0) {
                    $('#assets-purchased-this-month').html(Intl.NumberFormat('id').format(totalMonthlyPurchasedAssets[0].total_purchase_asset_permonth));
                }
                if(res.data.monthly_depreciation_value?.length > 0) {
                    $('#loading_asset-depreciation-this-month').hide();
                    let depreciation = Number.parseFloat(res.data.monthly_depreciation_value[res.data.monthly_depreciation_value.length-1].depreciation_value_per_month).toFixed(2);
                    $('#asset-depreciation-this-month').html(Intl.NumberFormat('id').format(depreciation));
                }
                if(res.data.total_net_book_by_group?.length > 0) {
                    let totalNetBook = 0;
                    res.data.total_net_book_by_group.forEach((netByGroup) => {
                        totalNetBook += Number.parseInt(netByGroup.total_netbook_asset_value);
                    })
                    $('#loading_total-net-book').hide();
                    $('#total-net-book').html(Intl.NumberFormat('id').format(totalNetBook));
                }
                if(res.data.total_original_cost_by_group?.length > 0) {
                    let totalOriginalCost = 0;
                    res.data.total_original_cost_by_group?.forEach((originalCostByGroup) => {
                        totalOriginalCost += Number.parseInt(originalCostByGroup.total_original_cost_asset_pergroup);
                    })
                    $('#loading_total-original-cost').hide();
                    $('#total-original-cost').html(Intl.NumberFormat('id').format(totalOriginalCost));
                }
                if(res.data.total_annually_purchased_assets?.length > 0) {
                    let totalPurchasedAssetThisYear = res.data.total_annually_purchased_assets.filter((totalPurchasedAsset) => {
                        return totalPurchasedAsset.year == moment().format('YYYY');
                    });
                    $('#loading_assets-purchased-this-year').hide();
                    $('#assets-purchased-this-year').html(Intl.NumberFormat('id').format(totalPurchasedAssetThisYear[0].total_purchase_asset_peryear));
                }
                if(res.data.annual_depreciation_value?.length > 0) {
                    let depreciationValueThisYear = res.data.annual_depreciation_value.filter((depreciation) => {
                        return depreciation.depreciation_year == moment().format('YYYY');
                    });
                    let depreciation = Number.parseFloat(depreciationValueThisYear[0].depreciation_value_per_year).toFixed(2);
                    $('#loading_asset-depreciation-this-year').hide();
                    $('#asset-depreciation-this-year').html(Intl.NumberFormat('id').format(depreciation));
                }
                if(res.data.depreciation_by_group?.length > 0) {
                    $('#loading_depreciation-by-group').hide();
                    $('#depreciation-by-group').html(`
                        <div class="d-flex flex-column" style="padding:0px;margin-top:-10px;margin-bottom:-8px;">
                            <span class="">${res.data.depreciation_by_group[0].asset_group}</span>
                            <span class="text-xs">Most Depreciated</span>
                        </div>
                    `);
                }
                if(res.data.total_asset_value?.length > 0) {
                    $('#loading_total-asset-value').hide();
                    $('#total-asset-value').html(Intl.NumberFormat('id').format(res.data.total_asset_value));
                }

                primaryChart = switchChart(primaryChart, chartContext, "total_monthly_purchased_assets", $('#chart-type-primary').val());
                secondaryChart = switchChart(secondaryChart, secondaryChartContext, "total_net_book_by_group", $('#chart-type-secondary').val());
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

</script>
@endsection