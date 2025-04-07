@extends('adminlte::page')
@section('title', 'Dashboard Management')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard Management</h1>
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

<!-- <section class="content pb-3">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-12">
                <div class="small-box bg-info card_employee_info">
                    <a href="#" class="small-box-footer text-bold">Department</a>
                    <div class="inner justify-content-center text-center">
                        <div id="department" style="font-size:20px; ">
                            <i id="loading_department" class="fa fa-spinner fa-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-12">
                <div class="small-box bg-success card_employee_info">
                    <a href="#" class="small-box-footer text-bold">Join</a>
                    <div class="inner justify-content-center text-center">
                        <div id="join" style="font-size:20px; ">
                            <i id="loading_join" class="fa fa-spinner fa-pulse"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12" >
                <div class="small-box bg-orange card_employee_info" style="color:white !important;">
                    <a href="#" class="small-box-footer text-bold" style="color:white !important;">Employee Request</a>
                    <div class="inner justify-content-center text-center">
                        <div class="row">
                            <div class="col-md-12" id="employee_request" style="font-size:15px;"></div>
                        </div>
                        <div style="margin-top: 5px;">
                            <button onclick="location.href='<?= route('employee_request.index') ?>'" id="btnRequest" class="btn btn-sm btn-success">Add Request</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-12">
                <div class="small-box bg-danger card_employee_info">
                    <a href="#" class="small-box-footer text-bold">Attendance</a>
                    <div class="inner justify-content-center text-center">
                        <div class="row">
                            <div class="col-md-6 text-bold" id="checkin" style="font-size:17px;"><i id="loading_checkin" class="fa fa-spinner fa-pulse"></i></div>
                            <div class="col-md-6 text-bold" id="checkout" style="font-size:17px;"><i id="loading_checkout" class="fa fa-spinner fa-pulse"></i></div>
                        </div>
                        <div style="margin-top: 5px;">
                            <button id="btnRecord" class="btn btn-sm btn-success">Record Time</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> -->

<section class="content pb-3">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="card card-row card-gray-dark collapsed-card collaps_employee">
                    <div class="card-header">
                        <h3 class="card-title">Employee Information</h3>
                        <div class="card-tools">
                          <button type="button" id="collaps_employee" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                            <i class="fas fa-plus collaps_employee_icon"></i>
                          </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-12">
                                <div class="card card-primary card-tabs">
                                    <div class="card-header p-0 pt-1">
                                        <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link text-bold" id="employee_by_department-tab" data-toggle="pill" href="#employee_by_department" role="tab" aria-controls="employee_by_department" aria-selected="false">Total by department</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-bold" id="turnover_employee-tab" data-toggle="pill" href="#turnover_employee" role="tab" aria-controls="turnover_employee" aria-selected="false">Employee Turnover</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-bold" id="contract_expired-tab" data-toggle="pill" href="#contract_expired" role="tab" aria-controls="contract_expired" aria-selected="false">Contract warning</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link text-bold" id="employee_leave-tab" data-toggle="pill" href="#employee_leave" role="tab" aria-controls="employee_leave" aria-selected="false">Employee Leave</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body">
                                        <div class="tab-content" id="custom-tabs-two-tabContent">
                                            <div class="tab-pane fade show active" id="employee_by_department" role="tabpanel" aria-labelledby="employee_by_department-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="card-body" style="overflow-y: scroll; height:350px;">
                                                            <div class="inner justify-content-center">
                                                                <div id="all_employee_by_department" style="font-size:20px;"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="turnover_employee" role="tabpanel" aria-labelledby="turnover_employee-tab">
                                                <div class="row">
                                                    <div class="col-lg-2 col-12">
                                                        <div class="small-box bg-light">
                                                            <a href="#" class="small-box-footer text-bold ">Join this month</a>
                                                            <div class="inner justify-content-center text-center">
                                                                <h3 id="join_at_this_month">
                                                                    <i id="loading_join_at_this_month" class="fa fa-spinner fa-pulse"></i>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-12">
                                                        <div class="small-box bg-light">
                                                            <a href="#" class="small-box-footer text-bold ">Join this year</a>
                                                            <div class="inner justify-content-center text-center">
                                                                <h3 id="join_at_this_year">
                                                                    <i id="loading_join_at_this_year" class="fa fa-spinner fa-pulse"></i>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-12">
                                                        <div class="small-box bg-light">
                                                            <a href="#" class="small-box-footer text-bold ">Resign this month</a>
                                                            <div class="inner justify-content-center text-center">
                                                                <h3 id="resign_at_this_month">
                                                                    <i id="loading_resign_at_this_month" class="fa fa-spinner fa-pulse"></i>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-12">
                                                        <div class="small-box bg-light">
                                                            <a href="#" class="small-box-footer text-bold ">Resign this year</a>
                                                            <div class="inner justify-content-center text-center">
                                                                <h3 id="resign_at_this_year">
                                                                    <i id="loading_resign_at_this_year" class="fa fa-spinner fa-pulse"></i>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <div class="small-box bg-light">
                                                            <a href="#" class="small-box-footer text-bold ">Total Employee</a>
                                                            <div class="inner justify-content-center text-center">
                                                                <h3 id="total_employee">
                                                                    <i id="loading_total_employee" class="fa fa-spinner fa-pulse"></i>
                                                                </h3>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="contract_expired" role="tabpanel" aria-labelledby="contract_expired-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-default advanced_expired">Advanced Search</button><br><br>
                                                        <table id="table_contract_expired" class="table table-striped table-bordered table-hover datatable nowrap" ></table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="employee_leave" role="tabpanel" aria-labelledby="employee_leave-tab">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <button type="button" class="btn btn-default advanced_leave">Advanced Search</button><br><br>
                                                        <table id="table_employee_leave" class="table table-striped table-bordered table-hover datatable nowrap" >
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="card card-row card-gray-dark">
                    <div class="card-header">
                        <h3 class="card-title">Cash Advance Summary</h3>
                        <div class="card-tools">
                            <button type="button" id="collaps_user_information" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                            <i class="fas fa-minus collaps_user_information_icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div class="row container">
                                    <div class="col-md-6">
                                        <label for="chart-type-primary">Chart Type</label>
                                        <select class="chart-types" id="chart-type-primary" style="width:100%"></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="chart-interval">Interval</label>
                                        <select class="interval" id="chart-interval" style="width:100%"></select>
                                    </div>
                                </div>
                                <div>
                                    <i id="primaryChartSpinner" class="fa fa-spinner fa-pulse"></i>
                                    <canvas id="primaryChart" style="height: 60vh;"></canvas>
                                </div>
                            </div>
                            {{-- <div class="col-md-6">
                                <div class="row container">
                                    <select class="chart-types" id="chart-type-secondary" style="width:100%"></select>
                                </div>
                                <div>
                                    <canvas id="secondaryChart" style="height: 40vh;"></canvas>
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-12" >
                                <div class="small-box bg-info switch-chart" chart="per_month_by_dept" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Per Month By Dept</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="per_month_by_dept" style="font-size:20px; ">
                                            <i id="loading_per_month_by_dept" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-success switch-chart" chart="per_year_by_dept" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Per Year By Dept</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="per_year_by_dept" style="font-size:20px; ">
                                            <i id="loading_per_year_by_dept" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-primary switch-chart" chart="per_year" context="primary">
                                    <a href="#" class="small-box-footer text-bold">Per Year</a>
                                    <div class="inner justify-content-center text-center">
                                        <div id="per_year" style="font-size:20px; ">
                                            <i id="loading_per_year" class="fa fa-spinner fa-pulse"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-5" id="rows_table_cash_advance">
                            <div class="col-xl-12" id="tab_detail">
                                <div class="nav nav-tabs justify-content-left mb-4">
                                    <a class="nav-item nav-link active" id="tab-employee" data-toggle="tab" href="#tab-pane-1">Per Employee
                                        <span class="error-tab text-red hidden">Error</span>
                                    </a>
                                    <a class="nav-item nav-link" id="tab-employee-month" data-toggle="tab" href="#tab-pane-2">Per Employee Per Month
                                        <span class="error-tab text-red hidden">Error</span>
                                    </a>
                                    <a class="nav-item nav-link" id="tab-cash-advance-outstanding" data-toggle="tab" href="#tab-pane-3">Outstanding Settlement
                                        <span class="error-tab text-red hidden">Error</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-pane-1">
                                    <table id="cashAdvanceEmployee" class="table table-hover table-bordered table-striped" style="width:100%"></table>
                                </div>
                                <div class="tab-pane" id="tab-pane-2">
                                    <table id="cashAdvanceEmployeePerMonth" class="table table-hover table-bordered table-striped" style="width:100%"></table>
                                </div>
                                <div class="tab-pane" id="tab-pane-3">
                                    <table id="cashAdvanceOutstanding" class="table table-hover table-bordered table-striped" style="width:100%"></table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content pb-3">
    <div class="container-fluid h-100">
        <div class="row">
            <div class="col-lg-12 col-12">
                <div class="card card-row card-gray-dark collapsed-card collaps_birthday">
                    <div class="card-header">
                        <h3 class="card-title">Birthday</h3>
                        <div class="card-tools">
                          <button type="button" id="collaps_birthday" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                            <i class="fas fa-plus collaps_birthday_icon"></i>
                          </button>
                        </div>
                    </div>
                    <div class="card-body birthday" style="overflow-y: scroll; height:480px;">
                        <div class="inner justify-content-center">
                            <div id="birthday" style="font-size:20px;"><i id="loading_birthday" class="fa fa-spinner fa-pulse"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="modal_all_leave"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="title_all_leave">All Leave</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body" style="overflow-y: scroll; height:500px;">
                            <div class="inner justify-content-center">
                                <div id="all_leave" style="font-size:20px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="maps_loc"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 text-right" style="font-size:16px; padding-bottom: 25px;">
                        <i class="nav-icon fas fa-close " data-dismiss="modal" style="cursor: pointer"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <center><div id="mapContainer_loc" style="width:100%;height:400px;"></div></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_detail_employee"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Employee Detail</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card-body" >
                            <div class="inner justify-content-center">
                                <div id="detail_employee" style="font-size:20px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_all_attendance_status"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">All Attendance Status</h4>
            </div>
            <div class="modal-body">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active text-bold" id="attendance_per_month_status-tab" data-toggle="pill" href="#attendance_per_month_status" role="tab" aria-controls="attendance_per_month_status" aria-selected="true">Status this month</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-bold" id="attendance_per_year_status-tab" data-toggle="pill" href="#attendance_per_year_status" role="tab" aria-controls="attendance_per_year_status" aria-selected="true">Status this Year</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabsContent">
                        <div class="tab-pane fade show active" id="attendance_per_month_status" role="tabpanel" aria-labelledby="attendance_per_month_status-tab">
                        </div>
                        <div class="tab-pane fade" id="attendance_per_year_status" role="tabpanel" aria-labelledby="attendance_per_year_status-tab">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_register_course"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h4 class="modal-title">Register Program</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4 align="center" style="margin:0;">Are you sure?</h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-success" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success register_course" data-program="">Yes</button>
            </div>
        </div>
    </div>
</div>
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
    let cashAdvanceEmployee = [];
    let cashAdvanceEmployeePerMonth = [];
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
            // { id: 'pie', text: 'Pie' },
            // { id: 'radar', text: 'Radar' },
            // { id: 'scatter', text: 'Scatter' },
        ]
    }).val('bar').trigger('change');

    let chartIntervalScales = {
        months: [
            { id: 3, text: '3 Months' },
            { id: 6, text: '6 Months' },
            { id: 12, text: '1 Year' },
        ],
        years: [
            { id: 3, text: '3 Years' },
            { id: 5, text: '5 Years' },
        ]
    }
    let currentInterval = 3;
    $('#chart-interval').select2({
        data: chartIntervalScales.months
    }).val(currentInterval).trigger('change');

    $(document).on('change', '#chart-interval', function(e, skipGetStats) {
        if(skipGetStats) return;
        currentInterval = $(this).val();
        getStats($(this).attr("chart-key"));
    })

    const switchChart = (chartJsInstance, context, dataKey = null, chartType = 'bar') => {
        if($(context).attr('id') == 'primaryChart') {
            primaryChartCurrentKey = dataKey;
        } else {
            secondaryChartCurrentKey = dataKey;
        }
        if(chartJsInstance) {
            chartJsInstance.destroy();
        }

        let xCount = null;
        if(dataKey == "per_year") {
            xCount = 1;
        }

        $("#chart-interval").attr("chart-key", dataKey);
        if(dataKey === "per_month_by_dept") {
            $('#chart-interval').empty().select2({ data: chartIntervalScales.months }).val(currentInterval).trigger('change', [true]);
        } else if(dataKey === "per_year_by_dept" || dataKey == "per_year") {
            $('#chart-interval').empty().select2({ data: chartIntervalScales.years }).val(currentInterval).trigger('change', [true]);
        } else {
            $('#chart-interval').attr('readonly', true);
        }

        let config = {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: dataKey.replaceAll("_", " ").toUpperCase(),
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
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
            let y2 = [];
            let chartData = chartMap.filter((map) => map.key === dataKey);
            chartData = Object.entries(chartData[0])[1][1];
            Object.entries(chartData).forEach((row) => {
                x.push(Object.values(row[1])[0] + `${xCount != 1 ? ` ${Object.values(row[1])[1]}`: ''}`);
                y.push(Object.values(row[1])[Object.values(row[1]).length-2]);
                y2.push(Object.values(row[1])[Object.values(row[1]).length-1]);
            });
            
            let objectKeys = Object.entries(chartData[0]);
            config.data.labels = x.reverse();
            config.data.datasets = [{
                label: objectKeys[objectKeys.length-2][0].replaceAll('_', ' '),
                data: y.reverse(),
                type: 'line',
                borderWidth: 1,
            }, {
                label: objectKeys[objectKeys.length-1][0].replaceAll('_', ' '),
                data: y2.reverse(),
                borderWidth: 1,
            }];
        }
        return new Chart(context, config);
        
    }

    const getStats = (chartKey = "per_month_by_dept") => {
        $.ajax({
            url: "{{ url('dashboard/dashboard_management/dashboard_management/cash_advance_reports') }}",
            data: {
                interval: $('#chart-interval').val(),
            },
            beforeSend: () => {
                if(primaryChart) {
                    primaryChart.destroy();
                }
                $('#primaryChartSpinner').show();
                chartMap = [];
            },
            success: (res) => {
                Object.entries(res.data).forEach((chartData) => {
                    chartMap.push({
                        key: chartData[0],
                        value: chartData[1],
                    });
                });
                cashAdvanceEmployee = res.data.per_employee;
                cashAdvanceEmployeePerMonth = res.data.per_employee_per_month;
                let numberFormat = Intl.NumberFormat('id');
                $('#loading_per_month_by_dept').hide();
                $('#per_month_by_dept')
                    .html(0)
                    .css('font-size', '11px');
                let thisMonthByDeptPaymentSum = 0;
                let thisMonthByDeptSettlementSum = 0;
                let thisMonthByDept = res.data.per_month_by_dept.filter(perMonthByDept => {
                    return perMonthByDept.month === moment().format('YYYY-MM');
                });
                thisMonthByDept.forEach((dept) => {
                    thisMonthByDeptPaymentSum += Number.parseFloat(dept.total_payment);
                    thisMonthByDeptSettlementSum += Number.parseFloat(dept.total_settlement);
                })

                $('#per_month_by_dept').html(`Payment: ${numberFormat.format(thisMonthByDeptPaymentSum)}</br>Settlement: ${numberFormat.format(thisMonthByDeptSettlementSum)}`);
                
                $('#loading_per_year_by_dept').hide();
                $('#per_year_by_dept')
                    .html(0)
                    .css('font-size', '11px');
                let thisYearByDeptPaymentSum = 0;
                let thisYearByDeptSettlementSum = 0;
                let thisYearByDept = res.data.per_year_by_dept.filter(perYearByDept => {
                    return perYearByDept.year === moment().format('YYYY');
                });
                thisYearByDept.forEach((dept) => {
                    thisYearByDeptPaymentSum += Number.parseFloat(dept.total_payment);
                    thisYearByDeptSettlementSum += Number.parseFloat(dept.total_settlement);
                })

                $('#per_year_by_dept').html(`Payment: ${numberFormat.format(thisYearByDeptPaymentSum)}</br>Settlement: ${numberFormat.format(thisYearByDeptSettlementSum)}`);

                let paymentDifference = ((res.data.per_year[0].total_payment - res.data.per_year[1].total_payment) / res.data.per_year[1].total_payment) * 100;
                let settlementDifference = ((res.data.per_year[0].total_settlement - res.data.per_year[1].total_settlement) / res.data.per_year[1].total_settlement) * 100;
                $('#per_year')
                    .css('font-size', '11px')
                    .html(`
                        Payment: ${paymentDifference < 0 ? '<i class="fa fa-arrow-down"></i>' : '<i class="fa fa-arrow-up"></i>'} ${Math.abs(paymentDifference).toFixed(2)}%
                        </br>
                        Settlement: ${settlementDifference < 0 ? '<i class="fa fa-arrow-down"></i>' : '<i class="fa fa-arrow-up"></i>'} ${Math.abs(settlementDifference).toFixed(2)}%
                    `);
                // $('#per_employee')
                //     .html(`${res.data.per_employee[0].name}</br>(${res.data.per_employee[0].nik_employee})`)
                //     .css('font-size', '11px');
                
                // $('#per_employee_per_month')
                //     .html(`${res.data.per_employee_per_month[0].name}</br>(${res.data.per_employee_per_month[0].nik_employee})`)
                //     .css('font-size', '11px');

                primaryChart = switchChart(primaryChart, chartContext, chartKey, $('#chart-type-primary').val());
                // secondaryChart = switchChart(secondaryChart, secondaryChartContext, "total_net_book_by_group", $('#chart-type-secondary').val());
                // fillDataTableData('#cashAdvanceEmployee', cashAdvanceEmployee);
                // fillDataTableData('#cashAdvanceEmployeePerMonth', cashAdvanceEmployeePerMonth);
            },
            complete: () => {
                $('#primaryChartSpinner').hide();
            }
        })
    }

    getStats();

    function fillDataTableData(targetElement, data) {
        return;
        $(targetElement).DataTable().clear();
        $(targetElement).DataTable().rows.add(data).draw();
    }

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

    $(document).on('click', '.cash-advance-employee-modal', function() {
        $('#cashAdvanceEmployeePerMonth').parent().hide();
        $('#cashAdvanceEmployee').parent().show();
    });

    $(document).on('click', '.cash-advance-employee-month-modal', function() {
        $('#cashAdvanceEmployee').parent().hide();
        $('#cashAdvanceEmployeePerMonth').parent().show();
    });

    $('#cashAdvanceEmployee').DataTable({
        pageLength: 10,
        processing: true,
        ajax: {
            url: "{{ url('dashboard/dashboard_management/dashboard_management/cash_advance_reports') }}?data=per_employee&interval=1",
        },
        serverSide: true,
        columns: [
            {
                defaultContent: '',
				orderable: false,
            },
            {
                data: 'nik_employee',
                defaultContent: '',
                orderable: false,
                title: '',
			},
            { data: 'nik_employee', title: 'NIK' },
            { data: 'name', title: 'Name' },
            { data: 'region', title: 'Region' },
            { data: 'branch', title: 'Branch' },
            { data: 'department', title: 'Department' },
            { data: 'payment', title: 'Total Payment' },
            { data: 'settlement', title: 'Total Settlement' }
        ]
    });

    $('#cashAdvanceEmployeePerMonth').DataTable({
        pageLength: 10,
        processing: true,
        ajax: {
            url: "{{ url('dashboard/dashboard_management/dashboard_management/cash_advance_reports') }}?data=per_employee_per_month&interval=1",
        },
        serverSide: true,
        columns: [
            {
                defaultContent: '',
				orderable: false,
            },
            {
                data: 'nik_employee',
                defaultContent: '',
                orderable: false,
                title: '',
			},
            { data: 'month', title: 'Month' },
            { data: 'nik_employee', title: 'NIK' },
            { data: 'name', title: 'Name' },
            { data: 'region', title: 'Region' },
            { data: 'branch', title: 'Branch' },
            { data: 'department', title: 'Department' },
            { data: 'payment', title: 'Total Payment' },
            { data: 'settlement', title: 'Total Settlement' }
        ]
    });

    $('#cashAdvanceOutstanding').DataTable({
        pageLength: 10,
        processing: true,
        ajax: {
            url: "{{ url('dashboard/dashboard_management/dashboard_management/cash_advance_reports') }}?data=settlement_outstanding&interval=1",
        },
        serverSide: true,
        columns: [
            {
                defaultContent: '',
				orderable: false,
            },
            {
                data: 'nik_employee',
                defaultContent: '',
                orderable: false,
                title: '',
			},
            { data: 'nik_employee', title: 'NIK' },
            { data: 'name', title: 'Name' },
            { data: 'region', title: 'Region' },
            { data: 'branch', title: 'Branch' },
            { data: 'department', title: 'Department' },
            { data: 'payment', title: 'Total Payment' },
            { data: 'settlement', title: 'Total Settlement' },
            { data: 'notes', title: 'Notes', orderable: true },
        ]
    });

        let avatar = "{{ asset('public/global/img/avatar.jpg') }}";     
        let allEmployee = {!! $allEmployee !!}
        let tab_employee_leave = 0;
        let tab_employee_contract = 0;
        let dataAttendance = '<?= !is_null(@$attendance) ? 'ready' : 'null' ?>';
        let public_course_program_all = '';
        let id_employee = "{{ $id_employee }}";
        
        $('#allEmployee').prepend('<option selected></option>').select2({
            placeholder: "Cari Karyawan",
            data: allEmployee,
            allowClear: true,
        });
        
        if(dataAttendance != 'ready'){
            $('#btnRecord').attr('disabled',true);
            $('#btnRequest').attr('disabled',true);
        } else {
            $('#btnRecord').attr('disabled',false);
            $('#btnRequest').attr('disabled',false);
        }

        const getEmployee = async (id) => {
            let status;
            let result;
            try {
                result = await $.ajax({
                    url: '<?= url('dashboard/employee') ?>'+'/'+id,
                    dataType: 'json',
                    success: function (res) {
                    }
                });
                return result;
            } catch (error) {
                // getEmployee(id);
            }
        }

        const leave_information = async (id_employee=null) => {
            let result;
            try {
                result = await $.ajax({
                    type: 'GET',
                    url: "<?= url('dashboard/leave_information') ?>",
                    data: {id_employee:id_employee},
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    success: function (result) {
                        
                    }
                });
                return result;
            } catch (error) {
                leave_information(id_employee);
            }
        }

        const attendanceStatus = async () => {
            let result;
            try {
                result = await $.ajax({
                    type: 'GET',
                    url: "<?= url('dashboard/attendanceStatus') ?>",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    success: function (result) {
                    }
                });
                return result;
            } catch (error) {
                attendanceStatus();
            }
        }

        const hierarchy = async () => {
            let result;
            try {
                result = await $.ajax({
                    type: 'GET',
                    url: "<?= url('dashboard/hierarchy') ?>",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    success: function (res) {
                    }
                });
                return result;
            } catch (error) {
                hierarchy();
            }
        }
        
        const public_course_program = async () => {
            let result = {!! json_encode($public_course_program->original) !!}
            if(result != ''){
                $.each(result, function(i, val){
                    let programs_all = ``;
                    let showAttachment = ``;
                    let buttonRegister = ``;

                    $.each(val.programs, function(i_, val_){
                        programs_all += `<li class="list-group-item">
                                    <b>${val_.description}</b>
                                </li>`;
                    });

                    if(val.attachment_path != ''){
                        showAttachment = `<div class="text-left mb-3"><a href="${val.attachment_path}" data-toggle="lightbox"><img class="img-fluid" src="${val.attachment_path}"></a></div>`;
                    }

                    if(val.status_registered == true){
                        buttonRegister = `<a href="javascript:;" class="btn btn-block btn-outline-success text-success">
                                <b><i class="fas fa-check"></i> Registered</b>
                            </a>`;
                    } else {
                        buttonRegister = `<a href="javascript:;" class="btn btn-primary btn-block text-white register" data-program="${val.id_event_management}">
                                    <b>Register</b>
                                </a>`;
                    }

                    public_course_program_all += `<div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center mb-2">
                                <span class="text-black text-bold" style="font-size:18px;">${val.description}</span>
                            </div>
                            <div class="text-center mb-3">
                                <span class="text-danger text-bold">End Registration : ${val.end_date_registration_alias}</span>
                            </div>
                            <div class="text-left mb-1">
                                ${val.long_description}
                            </div>
                            ${showAttachment}
                            
                            <ul class="list-group list-group-unbordered mb-1">
                                ${programs_all}
                            </ul>
                            <div class="div_register" data-program="${val.id_event_management}">
                                ${buttonRegister}
                            </div>
                        </div>
                    </div>`;
                });
            }
        }

        attendanceStatus().then(result => {
            if(result.by_month != null){
                $("#attendance_per_month_status").html('');
                let all_status_month = "";
                all_status_month += `<table class="table table-striped"><tr>
                                    <th>Date</th>
                                    <th class="text-center">Status</th>
                                </tr>`;
                $.each(result.by_month, function(i, val){
                    if(val=='PRS' || val=='NSI' || val=='NSO'){
                        thisval = `<span class="text-bold text-success">${val}</span>`;
                    } else if(val=='ABS'){
                        thisval = `<span class="text-bold text-danger">${val}</span>`;
                    } else if(val=='OFF'){
                        thisval = `<span class="text-bold ">${val}</span>`;
                    } else {
                        thisval = `<span class="text-bold text-primary">${val}</span>`;
                    }

                    all_status_month += `<tr>
                                    <td>${i}</td>
                                    <td class="text-center">${thisval}</td>
                                </tr>`;
                });
                all_status_month += `</table>`;
                $("#attendance_per_month_status").html(all_status_month);
            }

            if(result.by_year != null){
                $("#attendance_per_year_status").html('');
                let all_status_year = '<div class="row"><div class="col-12" id="accordionYear">';
 
                $.each(result.by_year, function(i, val){
                    let allStatusMonthInYear = "";
                    if(val.data.length > 0){
                        allStatusMonthInYear += `<table class="table table-striped"><tr>
                                            <th>Date</th>
                                            <th class="text-center">Status</th>
                                        </tr>`;
                        $.each(val.data, function(k, item){
                            if(item.status=='PRS' || item.status=='NSI' || item.status=='NSO'){
                                thisvalYear = `<span class="text-bold text-success">${item.status}</span>`;
                            } else if(item.status=='ABS'){
                                thisvalYear = `<span class="text-bold text-danger">${item.status}</span>`;
                            } else if(item.status=='OFF'){
                                thisvalYear = `<span class="text-bold ">${item.status}</span>`;
                            } else {
                                thisvalYear = `<span class="text-bold text-primary">${item.status}</span>`;
                            }

                            allStatusMonthInYear += `<tr>
                                            <td>${item.date}</td>
                                            <td class="text-center">${thisvalYear}</td>
                                        </tr>`;
                        });
                        allStatusMonthInYear += `</table>`;
                    }

                    all_status_year += `<div class="card card-primary card-outline">
                                        <a class="d-block w-100" data-toggle="collapse" href="#collapse${i}">
                                            <div class="card-header">
                                                <h4 class="card-title w-100 text-bold text-black">
                                                    ${val.name}
                                                </h4>
                                            </div>
                                        </a>
                                        <div id="collapse${i}" class="collapse" data-parent="#accordionYear">
                                            <div class="card-body">
                                                ${allStatusMonthInYear}
                                            </div>
                                        </div>
                                    </div>`;
                });
                all_status_year += `</div></div>`;
                $("#attendance_per_year_status").html(all_status_year);
            }
        });
    
        public_course_program()//harus diletakkan sebelum upcoming_event()
        employee_information()
        announcement()
        company_policy()
        upcoming_event()
        news()
        checkCollapse()
        attendance_per_month()
        attendance_per_year()
        employee_by_department()
        employee_turnover()
        birthday()

        leave_information().then(result => {
            $("#loading_leavebalance").hide();
            $("#loading_leaverequest").hide();
            $("#loading_leaverequest_thismonth").hide();
            $("#loading_leaverequest_thisyear").hide();
            
            if(result.leave_request != null){
                $("#leaverequest").html(result.leave_request.total_request);
                $("#leaverequest_thismonth").html(result.leave_request.this_month);
                $("#leaverequest_thisyear").html(result.leave_request.this_year);
            } else {
                $("#leaverequest").html('-');
                $("#leaverequest_thismonth").html('-');
                $("#leaverequest_thisyear").html('-');
            }

            if(result.leave_balance != null){
                $("#leavebalance").html(result.leave_balance.leave_quota);
            } else {
                $("#leavebalance").html('-');
            }
        });

        $('#employee_approval_table').DataTable({
            processing: true,
            // scrollY: true,
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            responsive: true,
            ajax: {
                url: "{{ route('emp_approval.index') }}",
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#employee_approval_table').DataTable().ajax.reload();
                }
            },      
             columns: [
                {
                defaultContent: '',
                orderable: false,
                },
                /* {   // Checkbox select column
                data: 'id_approval_transaction',
                defaultContent: '',
                orderable: false
                }, */
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'reference_number', name: 'reference_number'},
                {data: 'name', name: 'name'},
                {data: 'source_transaction_type', name: 'source_transaction_type', render: function ( data, type, row ) {   
                        source = data.replace('_',' ');
                        return source;
                    }},
                {data: 'request_group', name: 'request_group'},
                {data: 'request_type', name: 'request_type'},
                {data: 'approval_name', name: 'approval_name'},
                {data: 'approval_status', name: 'approval_status'},
                {data: 'action', name: 'action'},
            ]
        });

        $(document).on('click', '.view_announcement', function(){
            let id_announcement = $(this).attr('id');
            showAnnouncement(id_announcement)
        });

        $(document).on('click', '.view_companypolicy', function(){
            let id_announcement = $(this).attr('id');
            showAnnouncement(id_announcement)
        });
        
        $(document).on('click', '[data-toggle="lightbox"]', function(event) {
          event.preventDefault();
          $(this).ekkoLightbox({
            alwaysShowClose: true
          });
        });
        
        $(document).on('click', '.register', function(){
            let id_event_management = $(this).data('program');
            $(`.register_course`).attr('data-program', id_event_management);
            $(`#modal_register_course`).modal('show');
        });

        $(document).on('click', '.register_course', function(){
            let id_event_management = $(this).data('program');
            $(`.register_course`).attr('data-program', id_event_management);
            
            $.ajax({
                type: 'POST',
                url: "{{ url('learning_management/lms/event_course/register') }}",
                dataType: 'json',
                data: {id_event_management:id_event_management, id_employee:id_employee},
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                success: function (res) {
                    $(`#modal_register_course`).modal('hide');
                    if(res.status == true){
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: 'Register Success'
                        });
                        $(`.div_register[data-program="${id_event_management}"]`).html(`
                            <a href="javascript:;" class="btn btn-block btn-outline-success text-success">
                                <b><i class="fas fa-check"></i> Registered</b>
                            </a>
                        `);
                    } else {
                        swal({
                            icon: 'error',
                            dangerMode: true,
                            content: {
                                element: "div",
                                attributes: {
                                    innerText: res.message,
                                    className: "swal-red",
                                },
                            },
                        });
                    }
                }
            });
        });
        
        $(document).on("change", "#allEmployee", function () {
            let selected = $(this).select2("val");
            let html;
            if(selected != ''){
                getEmployee(selected).then(function(res) {
                    let name = res.name;
                    let nik = res.nik_employee;
                    let homebase = res.home_base;
                    let mail = res.work_mail == null ? '-' : res.work_mail;
                    let phone = res.work_phone == null ? '-' : res.work_phone;
                    let region = res.region == null ? '-' : res.region;
                    let position = res.position_routing;
                    let avatar = res.image_attachment;

                    html = `<div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                      <img class="profile-user-img img-fluid img-circle"
                                           src="${avatar}">
                                    </div>

                                    <h2 class="profile-username text-center">${name}</h2>
                                    <h4 class="text-center text-success">${nik}</h4>
                                    <h4 class="text-center text-danger">${position}</h4>
                                    <br>
                                    <ul class="list-group list-group-unbordered mb-3">
                                      <li class="list-group-item">
                                        <b>Work Mail</b> <a class="float-right">${mail}</a>
                                      </li>
                                      <li class="list-group-item">
                                        <b>Work Phone</b> <a class="float-right">${phone}</a>
                                      </li>
                                      <li class="list-group-item">
                                        <b>Region</b> <a class="float-right">${region}</a>
                                      </li>
                                    </ul>
                                </div>
                            </div>`;
                    
                    $('#detail_employee').html(html);
                });

                $('#modal_detail_employee').modal('show');
            }
        });

        function employee_information() {
            let result = {!! json_encode($employee_information->original) !!}
            let request = {!! json_encode($request_type) !!}
            let employee_request = '';

            if(request.length > 0){
                $.each(request, function(i, val){
                    if(i != request.length-1){
                        employee_request += `${val.description}, `;
                    } else {
                        employee_request += `${val.description}`;
                    }
                });
            }

            $("#employee_request").html(employee_request);
            $("#loading_department").hide();
            // $("#loading_age").hide();
            $("#loading_join").hide();
            $("#loading_checkin").hide();
            $("#loading_checkout").hide();

            if(Object.keys(result).length > 0){
                if(result != ''){
                    $("#department").html(result.department);
                    // $("#age").html(result.age.substring(0,result.age.indexOf(' Years')));
                    $("#join").html(result.period_of_employment.replace(/&/g,'<br>'));

                    if(!result.actual_time_in){
                        $("#checkin").html('Check in: --:--');
                    } else {
                        $("#checkin").html('Check in: <b style="color:#85f0d0;font-size:18px;">'+result.actual_time_in.substring(11,16)+'</b>');
                    }

                    if(!result.actual_time_out){
                        $("#checkout").html('Check out: --:--');
                    } else {
                        $("#checkout").html('Check out: <b style="color:#85f0d0;font-size:18px;">'+result.actual_time_out.substring(11,16)+'</b>');
                    }
                }
            } else {
                $("#department").html('');
                $("#join").html('');
                $("#checkin").html('Check in: --:--');
                $("#checkout").html('Check out: --:--');
            }
        }

        function announcement() {
            let result = {!! json_encode($announcement->original) !!}
            $("#loading_announcement").hide();
            $("#announcement").html('');
            var word_announcement = "";
            if(result != ''){
                word_announcement += '<ul class="list-unstyled">';
                $.each(result, function(i, val){
                    word_announcement += `<li class="callout callout-info" style="padding:8px;font-size:16px;">
                        <a href="javascript:;" id="${val.id_announcement}" class="view_announcement">${val.description}</a>
                    </li>`;
                });
                word_announcement += '</ul>';
            }
            $("#announcement").html(word_announcement);
        }

        function company_policy() {
            let result = {!! json_encode($company_policy->original) !!}
            $("#loading_companypolicy").hide();
            $("#companypolicy").html('');
            var word_companypolicy = "";
            if(result != ''){
                word_companypolicy += '<ul class="list-unstyled">';
                $.each(result, function(i, val){
                    word_companypolicy += `<li class="callout callout-info" style="padding:8px;font-size:16px;">
                        <a href="javascript:;" id="${val.id_announcement}" class="view_companypolicy">${val.description}</a>
                    </li>`;
                });
                word_companypolicy += '</ul>';
            }
            $("#companypolicy").html(word_companypolicy);
        }

        function upcoming_event() {
            let result = {!! json_encode($upcoming_event->original) !!}

            $("#loading_upcomingevent").hide();
            $("#upcomingevent").html('');
            var word_upcomingevent = "";
            let courseAvailable = '';
            let courseNotAllowed = '';

            if(result != ''){
                $.each(result, function(i, val){
                    if(val.start_date_attendee != null){
                        start = moment(val.start_date_attendee, 'YYYY-MM-DD');
                    } else {
                        start = moment(val.start_date, 'YYYY-MM-DD');
                    }
                    if(val.end_date_attendee != null){
                        end = moment(val.end_date_attendee, 'YYYY-MM-DD');
                    } else {
                        end = moment(val.end_date, 'YYYY-MM-DD');
                    }

                    let schedule_date = `${start.format("DD-MMM-YYYY")} ~ ${end.format("DD-MMM-YYYY")}`;
                    let url_join = '{{ url("learning_management/lms/class_room/class") }}?c='+val.id_event_program;

                    if(val.status_join == 'done'){
                        status_event = `<div class="text-left"><span class="text-success text-bold">Done</span></div>`;
                    } else if(val.status_join == 'expired'){
                        status_event = `<div class="text-left"><span class="text-secondary text-bold">Not Allowed</span></div>`;
                    } else {
                        status_event = `<div class="text-left"><span class="text-primary text-bold">Available</span></div>`;
                    }

                    if(val.status_join == 'available'){
                        action = `<div class="text-right"><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm text-white" title="Start" >Start</a></div>`;
                    } else if(val.status_join == 'done'){
                        action = `<div class="text-right"><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm text-white" title="Join" >Show</a></div>`;
                    } else if(val.status_join == 'expired'){
                        action = ``;
                    }

                    if(val.status_join != 'done'){
                        if(val.status_join == 'available'){
                            courseAvailable += `
                                <div class="row mt-2 mb-4" style="width:100%;">
                                <div class="col-md-4">${schedule_date}</div>
                                <div class="col-md-5">${val.program} (${val.course})</div>
                                <div class="col-md-2">${status_event}</div>
                                <div class="col-md-1">${action}</div>
                                </div>
                            `;
                        } else {
                            courseNotAllowed += `
                                <div class="row mt-2 mb-4" style="width:100%;">
                                <div class="col-md-4">${schedule_date}</div>
                                <div class="col-md-5">${val.program} (${val.course})</div>
                                <div class="col-md-2">${status_event}</div>
                                <div class="col-md-1">${action}</div>
                                </div>
                            `;
                        }
                    }
                });
                word_upcomingevent += `
                    ${courseAvailable}
                    ${courseNotAllowed}
                `;
            }

            if(public_course_program_all != ''){
                word_upcomingevent += `<div class="mt-5">
                    <h4 class="text-primary text-center mb-4">Public Course</h4>
                    ${public_course_program_all}
                </div>`;
            }
            $("#upcomingevent").html(word_upcomingevent);
        }

        function news() {
            let result = {!! json_encode($news->original) !!}
            let viewNews = "";
            let contentSlide = '';
            if(result != ''){
                $.each(result, function(i, val){
                    if(val.image_poster_path != null){
                        contentSlide += `<li class="splide__slide"><a href="${val.image_poster_path}" data-toggle="lightbox"><img class="img-fluid" src="${val.image_poster_path}" style="width:100%;object-fit: cover;"></a></li>`;
                    }
                });
                viewNews += `<section id="main-carousel" class="splide col-md-12">
                    <div class="splide__track">
                        <ul class="splide__list">
                        ${contentSlide}
                        </ul>
                    </div>
                </section>`;
            }
            $("#news").html(viewNews);
            
            // if(viewNews != ''){
            //     let splide = new Splide(`#main-carousel`, {
            //         heightRatio : 0.4,
            //     }).mount();
            // }
        }

        function attendance_per_month() {
            let result = {!! json_encode($attendance_per_month->original) !!}
            if(result != null){
                $.each(result, function(i, val){
                    if(val.attendance_status != null){
                        // $(`#loading_${val.attendance_status.toLowerCase()}_month`).hide();
                        $(`#${val.attendance_status.toLowerCase()}_month`).html(numberFormat(val.total));
                    }
                });
            }
        }

        function attendance_per_year() {
            let result = {!! json_encode($attendance_per_year->original) !!}
            if(result != null){
                $.each(result, function(i, val){
                    if(val.attendance_status != null){
                        // $(`#loading_${val.attendance_status.toLowerCase()}_year`).hide();
                        $(`#${val.attendance_status.toLowerCase()}_year`).html(numberFormat(val.total));
                    }
                });
            }
        }

        function employee_by_department() {
            let uri = "{{ Request::path() }}";
            $.ajax({
                url: "<?= url('dashboard/employee_by_department') ?>",
                async: true,
                method: "GET",
                data: {uri:uri},
                success: function (result) {
                    if(result != null){
                        $("#all_employee_by_department").html('');
                        var all_employee_by_department = "";
                        all_employee_by_department += `<table class="table table-striped"><tr>
                                            <th class="text-center">Department</th>
                                            <th class="text-center">Total</th>
                                        </tr>`;
                        $.each(result, function(i, val){
                            all_employee_by_department += `<tr>
                                            <td class="text-center">${val.department}</td>
                                            <td class="text-center">${val.count}</td>
                                        </tr>`;
                        });
                        all_employee_by_department += `</table>`;
                        $("#all_employee_by_department").html(all_employee_by_department);
                    }
                },
                error: function (xhr) {
                    employee_by_department()
                }
            });
        }


        function employee_turnover() {
            let result = {!! json_encode($employee_turnover->original) !!}
            if(result != null){
                for (param in result) {
                    for (column in result[param]) {
                        $(`#loading_${column}`).hide();
                        $(`#${column}`).html(numberFormat(result[param][column]));
                    }
                }
            }
        }

        function contract_expired() {
            let t_ = $('#table_contract_expired').DataTable({
                processing: true,
                serverSide: false,
                // responsive: true,
                destroy: true,
                ajax: {
                    "url": "{{ route('dashboard.contract_expired') }}",
                    "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                columns: [
                    { data: 'DT_RowIndex', title: 'No'},
                    {   // Checkbox select column
                        defaultContent: '',
                        orderable: false
                    },
                    { data : "name", title: 'Name'},
                    { data : "nik_employee", title: 'Employee Number' },
                    { data : "home_base", title: 'Homebase' },
                    { data : "contract_number", title: 'Contract Number' },
                    { data : "contract_status", title: 'Status' },
                    { data : "work_mail", title: 'Work Mail' },
                    { data : "join_date", title: 'Join Date' },
                    { data : "expired_date", title: 'Expired Date' },
                    { data : "identification_number", title: 'ID Number' },
                    { data : "effective_date", title: 'Effective Date' },
                    { data : "notice_period", title: 'Notice Period' },

                ],
                // order: [[ 14, "desc" ], [ 1, "asc" ]],
                // scrollY: true,
                scrollX: true,
                lengthMenu: [
                    [10, 30, 100, 200, 500, 1000, -1],
                    [10, 30, 100, 200, 500, 1000, 'All']
                ],
            });
        }

        function employee_leave() {
            let uri = "{{ Request::path() }}";
            let t = $('#table_employee_leave').DataTable({
                processing: true,
                serverSide: false,
                // responsive: true,
                destroy: true,
                ajax: {
                    "url": "{{ route('dashboard.getEmployeeByAccessGroup') }}",
                    "data": {uri:uri},
                    "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                },
                columns: [
                    { data: 'DT_RowIndex', title: 'No'},
                    {   // Checkbox select column
                        defaultContent: '',
                        orderable: false
                    },
                    { data : "name", title: 'Name'},
                    { data : "nik_employee", title: 'Employee Number' },
                    { data : "status", title: 'Status' },
                    { data : "position", title: 'Position' },
                    { data : "region", title: 'Region' },
                    { data : "branch", title: 'Branch' },
                    { data : "leave_quota", title: 'Quota' },
                    { data : "used_leave", title: 'Used' },
                    { data : "remaining_annual_leave", title: 'Remaining Annual Leave' },
                    { data : "effective_date", title: 'Effective Date' , 
                        render: function ( data, type, row ) {  
                            return moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        } 
                    },
                    { data : "expired_date", title: 'Expired Date', 
                        render: function ( data, type, row ) {  
                            return moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        } 
                    },
                    // { data : 'action', title: 'Action', orderable: false, 
                    //     render: function ( data, type, row ) {  
                    //         return `<center><a href="javascript:;" onclick="show_leave(${row.id_employee})" class="btn btn-warning btn-xs" title="Show" ><i class="fa fa-eye"></i></a></center>`;
                    //     } 
                    // },
                ],
                // order: [[ 14, "desc" ], [ 1, "asc" ]],
                // scrollY: true,
                scrollX: true,
                lengthMenu: [
                    [10, 30, 100, 200, 500, 1000, -1],
                    [10, 30, 100, 200, 500, 1000, 'All']
                ],
                "fnInitComplete": function (oSettings) {
                   $('#table_employee_leave_wrapper .column-filter-widget:eq(4)').find("select option:contains('A')").attr('selected','selected').change();
                }
            });
        }

        function birthday() {
            let avatar = "{{ asset('public/global/img/avatar.jpg') }}";
            let image = '';
            let uri = "{{ Request::path() }}";

            $.ajax({
                url: "<?= url('dashboard/birthday') ?>",
                async: true,
                method: "GET",
                data: {uri:uri},
                success: function (result) {
                    $("#loading_birthday").hide();
                    $("#birthday").html('');
                    var birthday = "";
                    if(result.length > 0){
                        birthday += '<div class="row">';
                        $.each(result, function(i, val){
                            let img_profile = val.image_attachment;
                            if(img_profile == null || img_profile == ''){
                                image = avatar;
                            } else {
                                image = "data:image;base64,"+img_profile;
                            }

                            birthday += `<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
                                            <div class="card bg-light d-flex flex-fill">
                                                <div class="card-header border-bottom-0">
                                                  <h5>${val.name}</h5>
                                                </div>
                                                <div class="card-body pt-0">
                                                    <div class="row">
                                                        <div class="col-7">
                                                            <h3 class="lead text-primary"><b>${val.position}</b></h3>
                                                            <h5 class="text-success"><b>${moment(val.birthdate).format('DD MMMM')}</b> </h5> 
                                                        </div>
                                                        <div class="col-5 text-center">
                                                            <img src="${image}" alt="user-avatar" class="img-circle img-fluid">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <h5 class="text-danger"><b>${val.region}</b></h5> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                        });
                        birthday += '</div>';
                    } else {
                        $(".birthday").css('height', '90px');
                        birthday = `<div class="row">
                                        <div class="col-md-12">
                                            <center><h4>No data available</h4></center>
                                        </div>
                                    </div>`;
                    }
                    $("#birthday").html(birthday);
                },
                error: function (xhr) {
                    birthday()
                }
            });
        }

        function show_leave(id_employee=null) {
            leave_information(id_employee).then(result => {
                $("#modal_all_leave").modal('show');
                $("#all_leave").html('');
                if(result.all_leave.length > 0){
                    $("#title_all_leave").html(`${result.employee.name} (${result.employee.nik_employee})`);
                    var all_leave = "";
                    all_leave += `<table class="table table-striped"><tr>
                                        <th>Type</th>
                                        <th class="text-center">Remaining</th>
                                        <th class="text-center">Balance</th>
                                    </tr>`;
                    $.each(result.all_leave, function(i, val){
                        eff = `<span class="text-success">${val.effective}</span>`;
                        exp = `<span class="text-danger">${val.expired}</span>`;

                        all_leave += `<tr>
                                        <td>${val.type} <br>(${eff} - ${exp})</td>
                                        <td class="text-center">${val.remaining}</td>
                                        <td class="text-center">${val.balance}</td>
                                    </tr>`;
                    });
                    all_leave += `</table>`;
                    $("#all_leave").html(all_leave);
                } else {
                    $("#title_all_leave").html('All Leave');
                }
            });
        }
        
        function checkCollapse() {
            let collapse = ['collaps_user_information', 'collaps_leave', 'collaps_news', 'collaps_workdays', 'collaps_event', 'collaps_hirarki', 'collaps_birthday', 'collaps_employee'];
            $.each(collapse, function(i, val){
                if(localStorage.getItem(val)){
                    $(`.${val}`).removeClass('collapsed-card');
                    $(`.${val}_icon`).removeClass('fa-plus').addClass('fa-minus');
                } else {
                    $(`.${val}`).addClass('collapsed-card');
                    $(`.${val}_icon`).removeClass('fa-minus').addClass('fa-plus');
                }
            });
        }

        $("#collaps_user_information, #collaps_leave, #collaps_news, #collaps_workdays, #collaps_event, #collaps_hirarki, #collaps_birthday, #collaps_employee").click(function () {
            let id = $(this).attr('id');
            if(localStorage.getItem(id)){
                localStorage.removeItem(id);
            } else {
                localStorage.setItem(id, 'true');
            }
        });

        function numberFormat (n) {
            return n.toLocaleString("id-ID");;
        }
		
		function showMap(lng, lat) {
            drawMaping(lng, lat).then(res => {
                $("#maps_loc").modal('show');
            });
        }

        const drawMaping = async (lng, lat) => {
            let token = "{{ $mapboxToken }}";

            mapboxgl.accessToken = token;
            var map = new mapboxgl.Map({
                container: 'mapContainer_loc',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [lng, lat], 
                zoom: 14,
                // interactive: false,
            });
            map.on('load', () => {
                 map.resize();
            });
            map.addControl(new mapboxgl.NavigationControl());

            let urlApiPlace = '<?= $mapboxGetPlace ?>';
            let urlPlace = `${urlApiPlace}${lng},${lat}.json?access_token=${token}`;
            const response = await fetch(urlPlace, {
                method: 'GET',
            });
            const place = await response.json();
            const marker = new mapboxgl.Marker({color: "#3FB1CE",})
                .setLngLat([lng, lat])
                .setPopup(
                    new mapboxgl.Popup({
                        offset: 25
                    }) // add popups
                    .setHTML(
                        `<span>${place.features[0].place_name}</span><br>
                        <span style="color:green;">Lat : ${lat}</span><br> 
                        <span style="color:red;">Lng : ${lng}</span>`
                    )
                )
                .addTo(map);
            return token;
        }

        $(document).on("click", ".advanced_leave", function () {
            $('.cf').select2({width:'100%'});
            if($(".table_employee_leave").css('display') == 'none'){
                $(".table_employee_leave").show("slow");
            }
            else {
                $(".table_employee_leave").hide("slow");
            }   
        });

        $(document).on("click", ".advanced_expired", function () {
            $('.cf').select2({width:'100%'});
            if($(".table_contract_expired").css('display') == 'none'){
                $(".table_contract_expired").show("slow");
            }
            else {
                $(".table_contract_expired").hide("slow");
            }   
        });

        $(document).on("click", "#employee_leave-tab", function () {
            if(tab_employee_leave < 1){
                employee_leave()
                tab_employee_leave += 1;
            }
        });

        $(document).on("click", "#contract_expired-tab", function () {
            if(tab_employee_contract < 1){
                contract_expired()
                tab_employee_contract += 1;
            }
        });

        

</script>
@endsection