@extends('adminlte::page')

@section('title', 'Employee Report')

@section('content')
<style>
    .modal { overflow: auto !important; }
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
</style>
<style type="text/css"> 

</style>
<div class="row">
    <div class="col-12">
        <section class="content">
            <div class="container-fluid h-100">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Employee Report</h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Employee Status :</label>
                            <div class="col-md-4">
                                <select id="employee_status" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                            
                            <label class="col-md-2 col-form-label">Regional :</label>
                            <div class="col-md-4">
                                <select id="regional" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <!-- <label class="col-md-2 col-form-label">Employee Name :</label>
                            <div class="col-md-4">
                                <select id="employeename" class="form-control form-control-sm select2 employee" multiple="multiple" style="width: 100%;"></select>
                            </div> -->
                            <label class="col-md-2 col-form-label">Start Date to End Date</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
                                    <input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
                                    <input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
                                    <div class="input-group-append">
                                        <span class="input-group-text far fa-calendar form-control-sm"></span>
                                    </div>
                                </div>
                            </div>
                            
                            <label class="col-md-2 col-form-label">Branch :</label>
                            <div class="col-md-4">
                                <select id="branch" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Lapkar Filter By :</label>
                            <div class="col-md-4">
                                <select id="filter_column" class="form-control form-control-sm select2" style="width: 100%;">
                                    <option value="effective_date">Effective Date</option>
                                    <option value="created_date">Created Date</option>
                                </select>
                            </div>

                            <label class="col-md-2 col-form-label">Location :</label>
                            <div class="col-md-4">
                                <select id="location" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                        </div>
           
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Department :</label>
                            <div class="col-md-4">
                                <select id="department" class="form-control select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                            
                            <label class="col-md-2 col-form-label">Company :</label>
                            <div class="col-md-4">
                                <select id="company" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 text-right">
                                <button type="button" id="download_attendance" class="btn btn-lg btn-primary" ><i class="fas fa-arrow-down"></i> Attendance</button>
                                <button type="button" id="download_lapkar" class="btn btn-lg btn-primary" ><i class="fas fa-arrow-down"></i> Lapkar</button>
                                <button type="button" id="download_headcount" class="btn btn-lg btn-primary" ><i class="fas fa-arrow-down"></i> Headcount</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row columnByGroup" style="display: none;">   
                            <div class="col-md-3">
                                <h4>Show column by group : </h4>
                            </div>
                            <div class="col-md-4">
                                <select name="columnByGroup" id="columnByGroup" class="form-control form-control-md select2" style="width:100%;">
                                </select>                           
                            </div>
                        </div>
                        <br>     
                        <button type="button" class="btn btn-default advanced_workdays" style="display: none;">Advanced Search</button><br><br>
                        <table id="workdays_table" class="table table-striped table-bordered table-hover datatable"></table>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <section class="content">
            <div class="container-fluid h-100">
                <div class="card card-danger card-outline">
                    <div class="card-header">
                        <h5 class="card-title">Summary Employee Atendance Chart</h5>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Employee Status :</label>
                            <div class="col-md-4">
                                <select id="chart_employee_status" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                </select>
                            </div>
                            
                            <label class="col-md-2 col-form-label">Regional :</label>
                            <div class="col-md-4">
                                <select id="chart_regional" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Employee Name :</label>
                            <div class="col-md-4">
                                <select id="chart_employeename" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                            <label class="col-md-2 col-form-label">Branch :</label>
                            <div class="col-md-4">
                                <select id="chart_branch" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                            
                        </div>
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Department :</label>
                            <div class="col-md-4">
                                <select id="chart_department" class="form-control select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                            <label class="col-md-2 col-form-label">Grade :</label>
                            <div class="col-md-4">
                                <select id="chart_grade" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                            <!-- <label class="col-md-2 col-form-label">Location :</label>
                            <div class="col-md-4">
                                <select id="location" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div> -->

                        </div>
           
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Company :</label>
                            <div class="col-md-4">
                                <select id="chart_company" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                            
                            <label class="col-md-2 col-form-label">Principle :</label>
                            <div class="col-md-4">
                                <select id="chart_principle" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Start Date to End Date</label>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input name="daterange" id="chart_daterange" type="daterange" class="form-control form-control-sm" />
                                    <input name="startdate" id="chart_startdate" class="form-control form-control-sm" hidden>
                                    <input name="enddate" id="chart_enddate" class="form-control form-control-sm" hidden>
                                    <div class="input-group-append">
                                        <span class="input-group-text far fa-calendar form-control-sm"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-8"></div>
                            <div class="col-md-2 text-right">
                                <button type="button" id="download_attendance_summary" class="btn btn-lg btn-info" ><i class="fas fa-arrow-down"></i> Summary</button>
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" id="chart_search" class="btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- <div class="inner d-flex justify-content-center text-center col-md-8">
                            <canvas id="pieChart" style="min-height: 350px; height: 350px; max-height: 350px; max-width: 100%;"></canvas>
                        </div> -->
                        <figure class="highcharts-figure">
                            <div id="pieChartHighchart"></div>
                        </figure>
                        <h5 class="text-center" id="total_employee"></h5>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
<script defer src="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js"></script>
<script defer src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
<!-- CHART JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/emn178/chartjs-plugin-labels/src/chartjs-plugin-labels.js"></script>

<!-- HIGCHHART -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>



<script>
    var datadailyshift;
    var counterdetail=0;
    var urlsegment = "{{ Request::segment(3) }}";
    let global_select_employee = "";
    let thisBranchByRegion = [];
    let accessGroup = "{!! $accessGroup !!}";
    let list_status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let list_status_lock = [
        { id: '0', text: 'No' },
        { id: '1', text: 'Yes' },
    ];
    let allNik = [];
    let myChart=null;

    $(document).ready(function(){
        daterange();
        daterange_chart()
        getGroup()
        checkCollapse()
        getCompany()
        getGrade()
        getPrinciple()
        chart()

        const get_department = new Promise(function(res) {
            let option = [];
            $.each(<?= json_encode($getdepartment) ?>, function (i, item) {
                // let desc = item.description + '('+ item.department_code +')';
                let desc = item.description;
                option.push({id: item.id_dept, text:desc});
            });
            res(option)
        });

        const get_regional = new Promise(function(res) {
            let option = [];
            $.each(<?= json_encode($getregional) ?>, function (i, item) {
                // let desc = item.description + '('+ item.region_code +')';
                let desc = item.description;
                option.push({id: item.id_region, text:desc});
            });
            res(option)
        });

        get_department.then(function(value) {
            $('#department').select2({
                placeholder: "Select Department",
                data: value,
                allowClear: true,
            });
            $('#chart_department').select2({
                placeholder: "Select Department",
                data: value,
                allowClear: true,
            });
        });

        get_regional.then(function(value) {
            $('#regional').select2({
                placeholder: "Select Regional",
                data: value,
                allowClear: true,
            });
            $('#chart_regional').select2({
                placeholder: "Select Regional",
                data: value,
                allowClear: true,
            });
        });

        function getGrade() {
            let option = [];
            $.each(<?= json_encode($getGrade) ?>, function (i, item) {
                // let desc = item.description + '('+ item.region_code +')';
                let desc = item.description;
                option.push({id: item.description.toLowerCase(), text:desc});
            });
            $('#chart_grade').select2({
                placeholder: "Select Grade",
                data: option,
                allowClear: true,
            });
        }

        function getPrinciple() {
            let option = [];
            $.each(<?= json_encode($getPrinciple) ?>, function (i, item) {
                // let desc = item.description + '('+ item.region_code +')';
                let desc = item.description;
                option.push({id: item.principal_code.toLowerCase(), text:desc});
            });
            $('#chart_principle').select2({
                placeholder: "Select Principle",
                data: option,
                allowClear: true,
            });
        }

        function get_employee_name(idElementStatus, idElementEmployee='', nik='') {
            getEmployeeByStatusAndAccessGroup(idElementStatus).then(function(value) {
                global_select_employee = value;
                $(`#${idElementEmployee}`).html('');
                $(`#${idElementEmployee}`).select2({
                    placeholder: "Select Employee",
                    data: value,
                    allowClear: true,
                });
                if(nik != ''){
                    $(`#${idElementEmployee}`).val(nik).trigger('change');
                }
            });
        }

        function get_employee_with_subordinate(nik='') {
            getEmployeeWithSubordinate().then(function(value) {
                $.each(value, function (i, item) {
                    allNik.push(item.id);
                });

                global_select_employee = value;
                $('#employeename').html('');
                $('#employeename').select2({
                    placeholder: "Select Employee",
                    data: value,
                    allowClear: true,
                });
                if(nik != ''){
                    $('#employeename').val(nik).trigger('change');
                }
            });
        }

        

        if(urlsegment=="attendance_list"){
            var id_employee = "{!! $myIdEmployee !!}";
            var myNik = "{!! $myNik !!}";
            get_employee_with_subordinate(myNik)
        } else {
            get_employee_name('employee_status', 'employeename');
            get_employee_name('chart_employee_status', 'chart_employeename');
        }

        $('#branch').select2({
            placeholder: "Select Branch",
            data: [],
            allowClear: true,
        });
        $('#chart_branch').select2({
            placeholder: "Select Branch",
            data: [],
            allowClear: true,
        });
        $('#location').select2({
            placeholder: "Select Location",
            data: [],
            allowClear: true,
        });
        $('#location_lock').select2({
            placeholder: "Select Location",
            data: [],
            allowClear: true,
        });
        $('#employee_status').select2({
            placeholder: "Select Status",
            data: list_status,
            allowClear: true,
        });
        $('#chart_employee_status').select2({
            placeholder: "Select Status",
            data: list_status,
            allowClear: true,
        });
        $('#lock_location').select2({
            placeholder: "Select Lock Status",
            data: list_status_lock,
            allowClear: true,
        });
        $('#filter_column').select2();
    });
    
    async function parameter(url) {
        if(url=="attendance_list"){
            let start = '';
            
            if($("#startdate").val() == ''){
                start = "{{ date('Y-m-d', strtotime('-6 days', strtotime(date('Y-m-d')))) }}";
                $("#startdate").val(start);
            } else {
                start = $("#startdate").val();
            }

            if($("#enddate").val() == ''){
                end = "{{ date('Y-m-d') }}";
                $("#enddate").val(end);
            } else {
                end = $("#enddate").val();
            }
            
            var myData = {
                status: ['A'],
                // employeename: $("#employeename").val() == '' ? allNik : $("#employeename").val(),
                startdate: start,
                enddate: end,
                department: null,
                regional: null,
                location: null,
                branch: null,
                filter_column: $("#filter_column").val(),
                path_menu: '{{ $path_menu_param }}',
                id_company: $("#company").val()
            };
        } else { 
            let start = '';
            let end = '';
            let thisBranch = [];

            if($("#startdate").val() == ''){
                start = "{{ date('Y-m-d', strtotime('-6 days', strtotime(date('Y-m-d')))) }}";
                $("#startdate").val(start);
            } else {
                start = $("#startdate").val();
            }

            if($("#enddate").val() == ''){
                end = "{{ date('Y-m-d') }}";
                $("#enddate").val(end);
            } else {
                end = $("#enddate").val();
            }

            if(accessGroup == 'Default_Manager'){
                if($("#regional").val().length < 1){
                    if($("#branch").val().length < 1){
                        thisBranch = JSON.parse('<?=json_encode($branchByManager)?>');
                    } else {
                        thisBranch = $("#branch").val();
                    }
                } else {
                    thisBranch = $("#branch").val();
                }
            } else {
                thisBranch = $("#branch").val();
            }

            var myData = {
                status: $("#employee_status").val() == '' ? null : $("#employee_status").val(),
                // employeename: $("#employeename").val() == '' ? null : $("#employeename").val(),
                startdate: start,
                department: $("#department").val() == '' ? null : $("#department").val(),
                enddate: end,
                regional: $("#regional").val() == '' ? null : $("#regional").val(),
                location: $("#location").val() == '' ? null : $("#location").val(),
                branch: thisBranch,
                filter_column: $("#filter_column").val(),
                path_menu: '{{ $path_menu_param }}',
                id_company: $("#company").val()
            };
        }
        return myData;
    }

    async function parameter_chart() {
        let start = '';
        let end = '';
        let thisBranch = [];

        if($("#chart_startdate").val() == ''){
            start = "{{ date('Y-m-d', strtotime('-6 days', strtotime(date('Y-m-d')))) }}";
            $("#chart_startdate").val(start);
        } else {
            start = $("#chart_startdate").val();
        }

        if($("#chart_enddate").val() == ''){
            end = "{{ date('Y-m-d') }}";
            $("#chart_enddate").val(end);
        } else {
            end = $("#chart_enddate").val();
        }

        if(accessGroup == 'Default_Manager'){
            if($("#chart_regional").val().length < 1){
                if($("#chart_branch").val().length < 1){
                    thisBranch = JSON.parse('<?=json_encode($branchByManager)?>');
                } else {
                    thisBranch = $("#chart_branch").val();
                }
            } else {
                thisBranch = $("#chart_branch").val();
            }
        } else {
            thisBranch = $("#chart_branch").val();
        }

        var myData = {
            status: $("#chart_employee_status").val() == '' ? null : $("#chart_employee_status").val(),
            employeename: $("#chart_employeename").val() == '' ? null : $("#chart_employeename").val(),
            startdate: start,
            enddate: end,
            department: $("#chart_department").val() == '' ? null : $("#chart_department").val(),
            regional: $("#chart_regional").val() == '' ? null : $("#chart_regional").val(),
            branch: thisBranch,
            // location: $("#location").val() == '' ? null : $("#location").val(),
            principle: $("#chart_principle").val() == '' ? null : $("#chart_principle").val(),
            grade: $("#chart_grade").val() == '' ? null : $("#chart_grade").val(),
            path_menu: '{{ $path_menu_param }}',
            id_company: $("#chart_company").val()
        };
        return myData;
    }

    function get_datatable(url, groupColumn=null) {
        parameter(url).then(res => {
            let t = $('#workdays_table').DataTable({
                processing: true,
                serverSide: false,
                // responsive: true,
                destroy: true,
                ajax: {
                    "url": "{{ route('workdays_getdata') }}",
                    "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    "data": res,
                },
                columns: [
                    {   // Detail Responsive
                        data: '',
                        defaultContent: '',
                        orderable: false
                    },
                    {   // Checkbox select column
                        data: 'id_workdays',
                        defaultContent: '',
                        orderable: false
                    },
                    { data: 'DT_RowIndex'},
                    { data : "current_dates", title: 'Current Dates'},
                    { data : 'employee_name', title: 'Employee Name', 
                        render: function ( data, type, row ) { 
                            let name = row.employee_name;
                            let nik = row.user_name;
                            let all = name+' ('+nik+')';
                            return all;
                        } 
                    },
                    { data : "department", title: 'Department' },
                //    { data : "shift", title: 'Shift' },
                    { data : "actual_time_in", title: 'Actual Time In' },
                    { data : "actual_time_out", title: 'Actual Time Out' },
                    { data : "day_type", title: 'Day Type' },
                    { data : "schedule_time_in", title: 'Schedule Time In' },
                    { data : "late_in", title: 'Late In', 
                        render: function ( data, type, row ) { 
                            let sch_in = moment(row.schedule_time_in, 'YYYY-MM-DD HH:mm:ss').valueOf();
                            let act_in = moment(row.actual_time_in, 'YYYY-MM-DD HH:mm:ss').valueOf();
                            let late = '';
                            if(act_in > sch_in){
                                late = row.late_in;
                            } 
                            return late;
                        } 
                    },
                    { data : "schedule_time_out", title: 'Schedule Time Out' },
                    { data : "early_out", title: 'Early Out' },
                    { data : "current_employee_timezone", title: 'Current Employee Timezone' },
                    { data : "work_hours", title: 'Work Hours' },
                    { data : "overtime", title: 'Overtime' },
                    { data : "current_name_in", title: 'Current Name In' },
                    { data : "current_address_in", title: 'Current Address In' },
                    { data : "current_name_out", title: 'Current Name Out' },
                    { data : "current_address_out", title: 'Current Address Out' },
                    { data : "branch", title: 'Branch' },
                    { data : "location", title: 'Location' },
                    { data : "employee_status", title: 'Employee Status' },
                    { data : "note", title: 'Note' },
                    { data : 'note', title: 'Maps', name: 'action', width: 500, orderable: false, 
                        render: function ( data, type, row ) { 
                            let lat_in = row.current_latitude_in;
                            let lng_in = row.current_longitude_in;
                            let disabled_in = (lat_in==null && lng_in==null) ? 'disabled' : '';
                            let maps_checkin = `<span class="text-success">Checkin : </span> &nbsp; Lat : ${lat_in}, Long : ${lng_in}`;

                            let lat_out = row.current_latitude_out;
                            let lng_out = row.current_longitude_out;
                            let disabled_out = (lat_out==null && lng_out==null) ? 'disabled' : '';
                            let maps_checkout = `<span class="text-danger">Checkout : </span> &nbsp; Lat : ${lat_out}, Long : ${lng_out}`;

                            let all = maps_checkin + ' | ' + maps_checkout;
                            return all;
                        } 
                    },
                    { data : "attendance_status", title: 'Attendance Status' },
                ],
				"fnInitComplete": function (oSettings) {
    			   $('#workdays_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
    			   $('#workdays_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
    			},
                order: [[ 4, "asc" ], [ 3, "desc" ]],
                scrollY: true,
                scrollX: true,
                lengthMenu: [
                    [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, -1],
                    [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, 'All']
                ],
            });

            t.on('order.dt search.dt', function () {
                t.column(2, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            });

            if(groupColumn != null){
                let id_column = groupColumn.detail_column.split(',');
                for (var i = 0; i <= t.columns().header().length - 1; i++) {
                    if(id_column.includes(i.toString()) == true){
                        t.column(parseInt(i)).visible(true);
                    } else {
                        t.column(parseInt(i)).visible(false);
                    }
                }
            }
        });
        $(".columnByGroup").show();
    }


    function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
    }
    
    function onlyNumberKey(evt) {
        // Only ASCII character in that range allowed
        var ASCIICode = (evt.which) ? evt.which : evt.keyCode
        if (ASCIICode > 31 && (ASCIICode < 48 || ASCIICode > 57))
            return false;
        return true;
    }
    
    async function getBranch(id_region) {
        let result;
        let _token      = "<?= csrf_token() ?>";
        let path_menu   = "<?= $path_menu_param ?>";
        try {
            result = await $.ajax({
                type: 'POST',
                url: "{{ route('workdays_getBranch') }}",
                dataType: 'json',
                data: {
                    id_region: id_region,
                    path_menu: path_menu,
                    _token: _token,
                },
                success: function (resp) {
                }
            });
            return result;
        } catch (error) {
            getBranch(id_region);
        }
    }

    async function getBranchOption(id_region, id_element) {
        getBranch(id_region).then(function(resp) {
            let option = [];
            thisBranchByRegion = [];
            $.each(resp, function (i, item) {
                let desc = item.description;
                option.push({id: item.id_branch, text:desc});
                thisBranchByRegion.push(item.id_branch);
            });

            $(`#${id_element}`).html('');
            $(`#${id_element}`).select2({
                placeholder: "Select Branch",
                data: option,
                allowClear: true,
            });
        });
    }

    async function getLocation(id_branch) {
        let result;
        let _token      = "<?= csrf_token() ?>";
        try {
            result = await $.ajax({
                type: 'POST',
                url: "{{ route('workdays_getLocation') }}",
                dataType: 'json',
                data: {
                    id_branch: id_branch,
                    _token: _token,
                },
                success: function (resp) {
                }
            });
            return result;
        } catch (error) {
            getLocation(id_branch);
        }
    }

    async function getLocationOption(id_branch, id_element) {
        getLocation(id_branch).then(function(resp) {
            let option = [];
            $.each(resp, function (i, item) {
                let desc = item.description;
                option.push({id: item.id_location, text:desc});
            });
            $(`#${id_element}`).html('');
            $(`#${id_element}`).select2({
                placeholder: "Select Location",
                data: option,
                allowClear: true,
            });
        });
    }

    async function getGroup() {
        let path_menu   = "<?= $path_menu_param ?>";
        let result;
        try {
            result = await $.getJSON('<?= url('employee/employee_setting/custom_report/get_data_custom') . '?address=' ?>' + path_menu, function (res) { 
                $('#columnByGroup').prepend('<option selected></option>').select2({
                    placeholder: "Select Group",
                    allowClear: true,
                    data: res,
                }); 
            });
            return result;
        } catch (error) {
            getGroup();
        }
    }

    async function getColumnByGroup(id_group) {
        let result;
        try {
            result = await $.getJSON('<?= url('employee/employee_setting/custom_report/get_custom_edit').'?id_req_report=' ?>' + id_group, function (res) { 
            });
            return result;
        } catch (error) {
            getColumnByGroup(id_group);
        }
    }

    async function getEmployeeByStatusAndAccessGroup(id_element) {
        let status;

        if(urlsegment=="attendance_list"){
            status      = 'A';
        } else {
            status      = $(`#${id_element}`).val() == '' ? null : $(`#${id_element}`).val();
        }
        
        let path_menu   = "<?= $path_menu_param ?>";
        let result;
        try {
            result = await $.getJSON('<?= url('employee/get_employee_by_status_and_access_group') ?>'+'?status='+status+'&path_menu='+path_menu, function (res) { 
            });
            return result;
        } catch (error) {
            getEmployeeByStatusAndAccessGroup(id_element);
        }
    }

    async function getEmployeeWithSubordinate() {
        let status;
        let result;
        try {
            result = await $.getJSON('<?= url('employee/get_employee_with_subordinate') ?>', function (res) { 
            });
            return result;
        } catch (error) {
            getEmployeeWithSubordinate();
        }
    }

    function chart(dataReturn=null) {
        if(dataReturn != null){
            //====== UNTUK MODEL PLUGIN CHARTJS ===================
            // if(myChart!=null){
            //     myChart.destroy();
            // }

            // var idChart = document.getElementById("pieChart").getContext('2d');
            // myChart = new Chart(idChart, {
            //     type: 'pie',
            //     data: {
            //         labels: dataReturn.label,
            //         datasets: [{
            //            label: "Attendance Dashboard",
            //            data: dataReturn.data,
            //            backgroundColor: dataReturn.color,
            //            hoverOffset: 5
            //         }],
            //     },
            //     options: {   
            //         responsive: true,
            //         legend: {
            //             display: true,
            //             position: 'right'
            //         },
            //         plugins: {
            //           labels: {
            //             render: 'percentage',
            //             fontColor: function (data) {
            //               return 'black';
            //             },
            //             precision: 1
            //           }
            //         },
            //     },
            // });

            //====== UNTUK MODEL PLUGIN HIGHCHART ===================
			$('#pieChartHighchart').css('height','500px');
            Highcharts.chart('pieChartHighchart', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Dashboard Attendance',
                    align: 'center'
                },
                credits: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<span style="font-size: 1.2em"><b>{point.name}</b></span><br>' +
                                '<span style="font-size: 15px; opacity: 0.9">({point.percentage:.1f} %)</span>',
                            connectorColor: 'rgba(128,128,128,0.5)'
                        },
						showInLegend: true
                    }
                },
                series: [{
                    name: 'Percentage',
                    data: dataReturn.data_highchart
                }]
            });

        }
    }

    $("#regional").change(function() {
        let selected = $(this).select2("val");
        getBranchOption(selected, 'branch')
    });

    $("#branch").change(function() {
        let selected = $(this).select2("val");
        getLocationOption(selected, 'location');
    });

    $("#chart_regional").change(function() {
        let selected = $(this).select2("val");
        getBranchOption(selected, 'chart_branch')
    });

    $("#chart_branch").change(function() {
        let selected = $(this).select2("val");
        getLocationOption(selected, 'chart_location');
    });

    $("#employee_status").change(function() {
        getEmployeeByStatusAndAccessGroup("employee_status").then(function(value) {
            global_select_employee = value;
            $('#employeename').html('');
            $('#employeename').select2({
                placeholder: "Select Employee",
                data: value,
                allowClear: true,
            });
        });
    });

    $("#chart_employee_status").change(function() {
        getEmployeeByStatusAndAccessGroup("chart_employee_status").then(function(value) {
            global_select_employee = value;
            $('#chart_employeename').html('');
            $('#chart_employeename').select2({
                placeholder: "Select Employee",
                data: value,
                allowClear: true,
            });
        });
    });

    $(document).on("click", ".advanced_lock", function () {
        $('.cf').select2({width:'100%'});
        if($(".employee_lock").css('display') == 'none'){
            $(".employee_lock").show("slow");
        }
        else {
            $(".employee_lock").hide("slow");
        }   
    });

    $(document).on("click", ".advanced_workdays", function () {
        $('.cf').select2({width:'100%'});
        if($(".workdays_table").css('display') == 'none'){
            $(".workdays_table").show("slow");
        }
        else {
            $(".workdays_table").hide("slow");
        }   
    });

    $(document).on("click", ".advanced_photo", function () {
        $('.cf').select2({width:'100%'});
        if($(".photo_table").css('display') == 'none'){
            $(".photo_table").show("slow");
        }
        else {
            $(".photo_table").hide("slow");
        }   
    });

    $(document).on("click", "#download_attendance", function () {
        parameter(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('employee/employee/download_attendance') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    $(document).on("click", "#download_headcount", function () {
        parameter(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('employee/employee/download_headcount') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    $(document).on("click", "#download_lapkar", function () {
        parameter(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('employee/employee/download_lapkar') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    $(document).on("click", "#download_attendance_summary", function () {
        parameter_chart(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('employee/employee/download_attendance_summary') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    $(document).on("change", "#columnByGroup", function () {
        let id_group = $(this).children("option:selected").val();
        if(id_group != ''){
            getColumnByGroup(id_group).then(group => {
                get_datatable(urlsegment, group)
            });
        } else {
            get_datatable(urlsegment)
        }
    });

    $(document).on("click", "#chart_search", function () {
        parameter_chart().then(res => {
            $.ajax({
                url: "{{ url('employee/employee/chart_dashboard_attendance') }}",
                dataType: 'json',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                data: res,
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (resp) {
                    if(resp.status){
                        chart(resp.data)
                        $('#total_employee').html(`<b>Total Employee : ${resp.data.all_employee}</b>`);
                    }
                },
                complete: function(){
                    $('#loader').addClass('hidden');
                },
            });
            // let param = objectToQueryString(res);
            // let url = "{{ url('employee/employee/download_lapkar') }}";
            // window.open(url+'?'+param, '_blank');
        });
    });

    $('#employeename').on('change', function(e) {
        let id_employee = $(this).val();
        $('#employeename').val(id_employee);
    });

    $('#employeename').on('select2:open', function(e) {
        $('#select2-employeename-results').on('click', function(event) {
            event.stopPropagation();
            var data = $(event.target).html();
            var selectedOptionGroup = data.toString().trim();
            var groupchildren = [];
            for (var i = 0; i < global_select_employee.length; i++) {
                if (selectedOptionGroup.toString() === global_select_employee[i].text.toString()) {
                    for (var j = 0; j < global_select_employee[i].children.length; j++) {
                        groupchildren.push(global_select_employee[i].children[j].id);
                    }
                }
            }
            let options = [];
            options = $('#employeename').val();
            if (options === null || options === '') {
                options = [];
            }
            for (var i = 0; i < groupchildren.length; i++) {
                var count = 0;
                for (var j = 0; j < options.length; j++) {
                    if (options[j].toString() === groupchildren[i].toString()) {
                        count++;
                        break;
                    }
                }
                if (count === 0) {
                    options.push(groupchildren[i].toString());
                }
            }
            $('#employeename').val(options).trigger('change').select2('close');
        });
    });

    $("#collaps_lock").click(function () {
        let id = $(this).attr('id');
        if(localStorage.getItem(id)){
            localStorage.removeItem(id);
        } else {
            localStorage.setItem(id, 'true');
        }
    });

    $(document).on("click", ".lock", function () {
        parameter(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('time_attendance/_export') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    function objectToQueryString(obj) {
        var str = [];
        for (var p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
        return str.join("&");
    }

    function daterange(startdate='', enddate='') {
        let separator = '   to   ';
        let start = (startdate=='' || startdate==null) ? moment().format('YYYY-MM-DD') : startdate;
        let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;

        $('#daterange').daterangepicker({
            uiLibrary: 'bootstrap4',
            autoApply: true,
            opens: 'center',
            locale: {
                format: 'YYYY-MM-DD',
                separator: separator,
                closeText: 'Clear',
            },
            startDate: start, 
            endDate: end,
        }, function(start, end, label) {
            $("#startdate").val(start.format('YYYY-MM-DD'));
            $("#enddate").val(end.format('YYYY-MM-DD'));
        });

        if($("#startdate").val()=='' || $("#enddate").val()==''){
            $("#startdate").val(moment().format('YYYY-MM-DD'));
            $("#enddate").val(moment().format('YYYY-MM-DD'));
        } 
    }

    function daterange_chart(startdate='', enddate='') {
        let separator = '   to   ';
        let start = (startdate=='' || startdate==null) ? moment().format('YYYY-MM-DD') : startdate;
        let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;

        $('#chart_daterange').daterangepicker({
            uiLibrary: 'bootstrap4',
            autoApply: true,
            opens: 'center',
			drops: 'auto',
            locale: {
                format: 'YYYY-MM-DD',
                separator: separator,
                closeText: 'Clear',
            },
            startDate: start, 
            endDate: end,
        }, function(start, end, label) {
            $("#chart_startdate").val(start.format('YYYY-MM-DD'));
            $("#chart_enddate").val(end.format('YYYY-MM-DD'));
        });

        if($("#chart_startdate").val()=='' || $("#chart_enddate").val()==''){
            $("#chart_startdate").val(moment().format('YYYY-MM-DD'));
            $("#chart_enddate").val(moment().format('YYYY-MM-DD'));
        } 
    }

    function checkCollapse() {
        let collapse = ['collaps_lock'];
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

    async function getCompany(id_branch) {
        let result;
        let _token      = "<?= csrf_token() ?>";
        try {
            result = await $.ajax({
                url: "{{ url('getCompany') }}",
                dataType: 'json',
                data: {
                    _token: _token,
                },
                success: function (resp) {
                    $('#company').select2({
                        placeholder: "Select company",
                        data: resp,
                        allowClear: true,
                    });
                    $('#chart_company').select2({
                        placeholder: "Select company",
                        data: resp,
                        allowClear: true,
                    });
                }
            });
            return result;
        } catch (error) {
            getCompany(id_branch);
        }
    }

    async function chart_search() {
        let result;
        let _token      = "<?= csrf_token() ?>";
        try {
            result = await $.ajax({
                url: "{{ url('getCompany') }}",
                dataType: 'json',
                data: {
                    _token: _token,
                },
                success: function (resp) {
                    $('#company').select2({
                        placeholder: "Select company",
                        data: resp,
                        allowClear: true,
                    });
                    $('#chart_company').select2({
                        placeholder: "Select company",
                        data: resp,
                        allowClear: true,
                    });
                }
            });
            return result;
        } catch (error) {
            getCompany(id_branch);
        }
    }

</script>
@endsection
