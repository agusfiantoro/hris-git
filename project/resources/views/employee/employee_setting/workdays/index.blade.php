@extends('adminlte::page')

@section('title', 'Work Days')

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
                        @if(Request::segment(3)=="attendance_list")
                        <h5 class="card-title">Attendance List</h5>
                        @else
                        <h5 class="card-title">Work Days</h5>
                        @endif
                        <div class="card-tools">
                            <!-- <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modalshiftgroup"><i class="fas fa-plus"></i> Create Shift Group</button> -->
                        </div>
                    </div>
                    @if(Request::segment(3)=="attendance_list")
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Employee Name :</label>
                            <div class="col-md-4">
                                <select id="employeename" class="form-control form-control-sm select2" multiple="multiple" style="height: 100%;" ></select>
                                <!-- <input type="hidden" id="id_shiftgroup" value=""> -->
                            </div>
                            
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
                        </div>
                        <div class="form-group row">
                            <div class="col-md-12 text-right">
                                <button type="button" id="custom" class="new btn btn-lg btn-primary" ><i class="fas fa-arrow-down"></i> Summary Excel</button>
                                <button type="button" id="search" class="new btn btn-lg btn-success" style="padding:0.5rem 2rem;"><i class="fas fa-search"></i> Search</button>
                                <button type="button" id="loadingsearch" class="btn btn-lg btn-success" style="padding:0.5rem 2rem;display:none;"><i class="fas fa-spinner fa-pulse"></i></button>
                            </div>
                        </div>
                    </div>
                    @else
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
                            <label class="col-md-2 col-form-label">Employee Name :</label>
                            <div class="col-md-4">
                                <select id="employeename" class="form-control form-control-sm select2 employee" multiple="multiple" style="width: 100%;"></select>
                                <!-- <input type="hidden" id="id_shiftgroup" value=""> -->
                            </div>
                            
                            <label class="col-md-2 col-form-label">Branch :</label>
                            <div class="col-md-4">
                                <select id="branch" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>
                        </div>
                        <div class="form-group row">
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

                            <label class="col-md-2 col-form-label">Location :</label>
                            <div class="col-md-4">
                                <select id="location" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                            </div>

                        </div>
           
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label">Department :</label>
                            <div class="col-md-4">
                                <select id="department" class="form-control select2" style="width: 100%;"></select>
                            </div>
                            
                        </div>

                        <div class="form-group row">
                            <div class="col-md-12 text-right">
                                <button type="button" id="custom" class="new btn btn-lg btn-primary" ><i class="fas fa-arrow-down"></i> Summary Excel</button>
                                <button type="button" id="search_photo" class="btn btn-lg btn-warning" ><i class="fas fa-search"></i> Search Photo</button>
                                <button type="button" id="search" class="new btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                                <button type="button" id="loadingsearch_" class="btn btn-lg btn-success" style="padding:0.5rem 2rem;display:none;"><i class="fas fa-spinner fa-pulse"></i></button>
                            </div>
                        </div>
                    </div>
                    @endif
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

@if(Request::segment(3)!="attendance_list")
<div class="row">
    <div class="col-12">
        <section class="content">
            <div class="container-fluid h-100">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="card card-row card-gray-dark collapsed-card collaps_lock">
                            <div class="card-header">
                                <h3 class="card-title">Lock Employee Location</h3>
                                <div class="card-tools">
                                  <button type="button" id="collaps_lock" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-plus collaps_lock_icon"></i>
                                  </button>
                                </div>
                            </div>
                            <div class="card-body" style="margin-bottom: -80px;">
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label" style="display:none;">Employee Status :</label>
                                        <div class="col-md-4" style="display:none;">
                                            <select id="employee_status_lock" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                            </select>
                                        </div>
                                        <label class="col-md-2 col-form-label">Employee Name :</label>
                                        <div class="col-md-4">
                                            <select id="employeename_lock" class="form-control form-control-sm select2 employee" multiple="multiple" style="width: 100%;"></select>
                                            <!-- <input type="hidden" id="id_shiftgroup" value=""> -->
                                        </div>
                                        
                                        <label class="col-md-2 col-form-label">Regional :</label>
                                        <div class="col-md-4">
                                            <select id="regional_lock" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                        </div>

                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Department :</label>
                                        <div class="col-md-4">
                                            <select id="department_lock" class="form-control select2" style="width: 100%;"></select>
                                        </div>
                                        
                                        <label class="col-md-2 col-form-label">Branch :</label>
                                        <div class="col-md-4">
                                            <select id="branch_lock" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Lock Location :</label>
                                        <div class="col-md-4">
                                            <select id="lock_location" class="form-control select2" multiple="multiple" style="width: 100%;"></select>
                                        </div>

                                        <label class="col-md-2 col-form-label">Location :</label>
                                        <div class="col-md-4">
                                            <select id="location_lock" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                        </div>

                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" id="search_lock" class="new btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-default advanced_lock" style="display: none;">Advanced Search</button><br><br>
                                <table id="employee_lock" class="table table-striped table-bordered table-hover datatable"></table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endif

<div id="modalworkdays" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Update Work Days</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Actual Time In :</label>
                    <div class="col-md-4">
                        <input type="hidden" id="id_employee" value="">
                        <input type="hidden" id="id_workdays" value="">
                        <input type="datetime-local" id="actual_time_in" class="form-control" placeholder="Actual Time In" value="">
                    </div>
                    
                    <label class="col-md-2 col-form-label">Actual Time Out :</label>
                    <div class="col-md-4">
                        <input type="datetime-local" id="actual_time_out" class="form-control" placeholder="Actual Time Out" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="simpaneditworkdays" value="add"><i class="fas fa-save"></i> Save</button>&nbsp;
                <button type="button" class="btn btn-sm btn-success" id="loadingsimpaneditworkdays" style="display:none;"><i class="fa fa-spinner fa-pulse"></i></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="maps"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                        <center><div id="mapContainer" style="width:100%;height:400px;"></div></center>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="modalPhoto"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attendance Photo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-default advanced_photo">Advanced Search</button><br><br>
                        <table id="photo_table" class="table table-striped table-bordered table-hover datatable"></table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<link href="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css" type="text/css">
<script defer src="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js"></script>
<script defer src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js"></script>
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

    $(document).ready(function(){
        daterange();
        getGroup()
        checkCollapse()

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
            $('#department').prepend('<option selected></option>').select2({
                placeholder: "Select Department",
                data: value,
                allowClear: true,
            });
            $('#department_lock').prepend('<option selected></option>').select2({
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
            $('#regional_lock').select2({
                placeholder: "Select Regional",
                data: value,
                allowClear: true,
            });
        });

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
            get_employee_name('employee_status_lock', 'employeename_lock');
        }

        $('#branch').select2({
            placeholder: "Select Branch",
            data: [],
            allowClear: true,
        });
        $('#branch_lock').select2({
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
        $('#employee_status_lock').select2({
            placeholder: "Select Status",
            data: [{ id: 'A', text: 'Active' }],
            allowClear: true,
        });
        $('#lock_location').select2({
            placeholder: "Select Lock Status",
            data: list_status_lock,
            allowClear: true,
        });
        
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
                employeename: $("#employeename").val() == '' ? allNik : $("#employeename").val(),
                startdate: start,
                enddate: end,
                department: null,
                regional: null,
                location: null,
                branch: null,
                path_menu: '{{ $path_menu_param }}',
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
                employeename: $("#employeename").val() == '' ? null : $("#employeename").val(),
                startdate: start,
                department: $("#department").val() == '' ? null : $("#department").val(),
                enddate: end,
                regional: $("#regional").val() == '' ? null : $("#regional").val(),
                location: $("#location").val() == '' ? null : $("#location").val(),
                branch: thisBranch,
                path_menu: '{{ $path_menu_param }}',
            };
        }
        return myData;
    }

    async function parameter_lock() {
        let thisBranch = [];
        if(accessGroup == 'Default_Manager'){
            if($("#regional_lock").val().length < 1){
                if($("#branch_lock").val().length < 1){
                    thisBranch = JSON.parse('<?=json_encode($branchByManager)?>');
                } else {
                    thisBranch = $("#branch_lock").val();
                }
            } else {
                if($("#branch_lock").val().length < 1){
                    thisBranch = thisBranchByRegion;
                } else {
                    thisBranch = $("#branch_lock").val();
                }
            }
        } else {
            if($("#branch_lock").val().length < 1){
                thisBranch = thisBranchByRegion;
            } else {
                thisBranch = $("#branch_lock").val();
            }
        }

        var myData = {
            status: $("#employee_status_lock").val() == '' ? 'A' : $("#employee_status_lock").val(),
            employeename: $("#employeename_lock").val() == '' ? null : $("#employeename_lock").val(),
            department: $("#department_lock").val() == '' ? null : $("#department_lock").val(),
            regional: $("#regional_lock").val() == '' ? null : $("#regional_lock").val(),
            location: $("#location_lock").val() == '' ? null : $("#location_lock").val(),
            lock_location: $("#lock_location").val() == '' ? null : $("#lock_location").val(),
            branch: thisBranch,
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
                            let maps_checkin = `<span class="text-success">Checkin : </span><button onclick="showMap(${lng_in}, ${lat_in})" class="btn btn-success btn-sm" title="Maps" ${disabled_in}><span class="fa fa-map-marker"></span></button> &nbsp; Lat : ${lat_in}, Long : ${lng_in}`;

                            let lat_out = row.current_latitude_out;
                            let lng_out = row.current_longitude_out;
                            let disabled_out = (lat_out==null && lng_out==null) ? 'disabled' : '';
                            let maps_checkout = `<span class="text-danger">Checkout : </span><button onclick="showMap(${lng_out}, ${lat_out})" class="btn btn-danger btn-sm" title="Maps" ${disabled_out}><span class="fa fa-map-marker"></span></button> &nbsp; Lat : ${lat_out}, Long : ${lng_out}`;

                            let all = maps_checkin + ' | ' + maps_checkout;
                            return all;
                        } 
                    },
                    { data : "attendance_status", title: 'Attendance Status' },
                    { 
                        data: 'id_workdays',
                        title: 'Mobile Attendance',
                        render: (data, type, row) => {
                            if(row.is_mobile_attendance) {
                                return `<span class="badge badge-sm badge-success">Yes</span>`;
                            }
                            return '';
                        }
                    },
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

    function get_datatable_photo(url) {
        parameter(url).then(res => {
            let t = $('#photo_table').DataTable({
                processing: false,
                serverSide: false,
                // responsive: true,
                destroy: true,
                ajax: {
                    url: "<?= url('time_attendance/attendance_photo') ?>",
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
                    { data: 'DT_RowIndex', title: 'No'},
                    { data : "current_dates", title: 'Date'},
                    { data : 'name', title: 'Employee Name', 
                        render: function ( data, type, row ) { 
                            let name = row.name;
                            let nik = row.nik_employee;
                            let all = name+' ('+nik+')';
                            return all;
                        } 
                    },
                    { data : "department", title: 'Department' },
                    { data : "shift", title: 'Photo' , 
                        render: function ( data, type, row ) { 
                            let img_in  = row.image_attachment_in;
                            let img_out = row.image_attachment_out;
                            let img_path = "<?= url('project/storage/app/public/images/') ?>";
                            show_in     = `IN : -`;
                            show_out    = `OUT : -`;

                            if(img_in!=null){
                                show_in = `IN : <a href="${img_path}/${img_in}" target="_blank" ><img src="${img_path}/${img_in}" style="height:60px;" /></a>`;
                            }
                            if(img_out!=null){
                                show_out = `OUT : <a href="${img_path}/${img_out}" target="_blank" ><img src="${img_path}/${img_out}" style="height:60px;" /></a>`;
                            }
                            let all = show_in+'<br>'+show_out;
                            return all;
                        } 
                    },
                ],
                "fnInitComplete": function (oSettings) {
                   $('#photo_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
                   // $('#photo_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
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

        });
        $("#modalPhoto").modal('show');
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
    
    function showMap(lng, lat) {
        drawMap(lng, lat).then(res => {
            $("#maps").modal('show');
        });
    }

    const drawMap = async (lng, lat) => {
        let token = "{{ $mapboxToken }}";

        mapboxgl.accessToken = token;
        var map = new mapboxgl.Map({
            container: 'mapContainer',
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

    $("#regional").change(function() {
        let selected = $(this).select2("val");
        getBranchOption(selected, 'branch')
    });

    $("#branch").change(function() {
        let selected = $(this).select2("val");
        getLocationOption(selected, 'location');
    });

    $("#regional_lock").change(function() {
        let selected = $(this).select2("val");
        getBranchOption(selected, 'branch_lock')
    });

    $("#branch_lock").change(function() {
        let selected = $(this).select2("val");
        getLocationOption(selected, 'location_lock');
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

    $("#employee_status_lock").change(function() {
        getEmployeeByStatusAndAccessGroup("employee_status_lock").then(function(value) {
            global_select_employee = value;
            $('#employeename_lock').html('');
            $('#employeename_lock').select2({
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

    $(document).on("click", "#search", function () {
        let start = moment($('#startdate').val());
        let end = moment($('#enddate').val());
        let maxDays = 62;
        if(end.diff(start, 'days') > maxDays) {
            swal({
                icon: 'warning',
                title: 'Warning',
                text: 'Maximum range for workdays search is '+maxDays+' days!'
            });
        } else {
            $(".advanced_workdays").show();
            get_datatable(urlsegment);
        }
		
    });

    $(document).on("click", "#search_lock", function () {
        $(".advanced_lock").show();
        get_datatable_lock()
    });

    $(document).on("click", "#custom", function () {
        parameter(urlsegment).then(res => {
            let param = objectToQueryString(res);
            let url = "{{ url('time_attendance/_export') }}";
            window.open(url+'?'+param, '_blank');
        });
    });

    $(document).on("click", "#search_photo", function () {
        let start = moment($('#startdate').val());
        let end = moment($('#enddate').val());
        let maxDays = 62;
        if(end.diff(start, 'days') > maxDays) {
            swal({
                icon: 'warning',
                title: 'Warning',
                text: 'Maximum range for workdays search is '+maxDays+' days!'
            });
        } else {
            $("#photo_table").html('');
            get_datatable_photo(urlsegment)
        }
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
    
    $(document).on("click", ".edit", function () {
        $("#id_employee").val($(this).attr('id').substring(0,$(this).attr('id').indexOf('|')).substring($(this).attr('id').indexOf('_')+1));
        $("#id_workdays").val($(this).attr('id').substring($(this).attr('id').indexOf('|')+1));
        
        $("#modalworkdays").modal('show');
    });
    
    $(document).on("click", "#simpaneditworkdays", function () {
        $("#simpaneditworkdays").hide();
        $("#loadingsimpaneditworkdays").show();
        
        var id_employee = $("#id_employee").val();
        var id_workdays = $("#id_workdays").val();
        var actual_time_in = $("#actual_time_in").val().replace(/T/g, ' ');
        var actual_time_out = $("#actual_time_out").val().replace(/T/g, ' ');
        
        $.ajax({
            url :"{{ route('workdays_edit') }}",  
            method:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data: {
                id_employee: btoa(id_employee),
                id_workdays: btoa(id_workdays),
                actual_time_in: btoa(actual_time_in),
                actual_time_out: btoa(actual_time_out)
            },
            success:function(msg){ 
                if(msg=="0")
                {
                    alert('Success update data');
                }
                else
                {
                    alert('Failed update data');
                }
                
                document.getElementById("search").click();
                $("#modalworkdays").modal('hide');
                $("#simpaneditworkdays").show();
                $("#loadingsimpaneditworkdays").hide();
            },
            error:function(msg){
                alert('Error server !');
                $("#simpaneditworkdays").show();
                $("#loadingsimpaneditworkdays").hide();
            }
        })
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

    function get_datatable_lock() {
        parameter_lock().then(res => {
            let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
            let btnLock = {
                text: 'Lock',
                className: 'btn btn-success lock_location',
                action: function (e, dt, node, config) {
                    let id_employee = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).attr('id_employee')
                    });
                    if(id_employee.length > 0){
                        sendLockEmployee('lock', id_employee)
                    } else {
                        swal({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Please select employee first'
                        });
                    }
                }
            }
            let btnUnlock = {
                text: 'Unlock',
                className: 'btn btn-danger unlock_location',
                action: function (e, dt, node, config) {
                    let id_employee = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                        return $(entry).attr('id_employee')
                    });
                    if(id_employee.length > 0){
                        sendLockEmployee('unlock', id_employee)
                    } else {
                        swal({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'Please select employee first'
                        });
                    }
                }
            }
            
            dtButtons.push(btnLock)
            dtButtons.push(btnUnlock)

            let t = $('#employee_lock').DataTable({
                processing: true,
                serverSide: false,
                // responsive: true,
                destroy: true,
                ajax: {
                    "url": "<?= url('time_attendance/employee_lock_attendance')?>",
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
                        data: 'id_employee',
                        defaultContent: '',
                        orderable: false
                    },
                    { data: 'DT_RowIndex', title:'No'},
                    { data : 'name', title: 'Employee Name', 
                        render: function ( data, type, row ) { 
                            let name = row.name;
                            let nik = row.nik_employee;
                            let all = name+' ('+nik+')';
                            return all;
                        } 
                    },
                    { data : "department", title: 'Department' },
                    { data : "position", title: 'Position' },
                    { data : "region", title: 'Region' },
                    { data : "branch", title: 'Branch' },
                    { data : "address_location", title: 'Location' },
                    { data : "shift", title: 'Shift' },
                    { data : "lock_gps_location", title: 'Lock Location', 
                        render: function ( data, type, row ) {  
                            return is_locked(row.lock_gps_location);
                        } 
                    },
                ],
                createdRow: function( row, data, dataIndex ) {
                    $(row).attr('id_employee', data['id_employee']);
                },
                "fnInitComplete": function (oSettings) {
                   $('#employee_lock_wrapper .column-filter-widget:eq(0)').css('display','none').change();
                   $('#employee_lock_wrapper .column-filter-widget:eq(1)').css('display','none').change();
                   $('#employee_lock_wrapper .column-filter-widget:eq(2)').css('display','none').change();
                },

                order: [[ 1, "asc" ]],
                scrollY: true,
                scrollX: true,
                lengthMenu: [
                    [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, -1],
                    [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, 'All']
                ],
                buttons: dtButtons,
            });

        });
    }

    function sendLockEmployee(type, idEmployee) {
        let result;
        let _token  = "<?= csrf_token() ?>";
        try {
            result = $.ajax({
                type: 'POST',
                url: "<?= url('time_attendance/lockEmployeeAttendance') ?>",
                dataType: 'json',
                data: {
                    type: type,
                    id_employee: idEmployee,
                    _token: _token,
                },
                success: function (resp) {
                    if(resp.status == 'true'){
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: resp.message
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Error',
                            text: resp.message
                        });
                    }
                    get_datatable_lock()
                }
            });
            return result;
        } catch (error) {
            sendLockEmployee(type, idEmployee);
        }
    }

    function is_locked(status) {
        let locked;
        if(status=='0'){
            locked = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
        } else {
            locked = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
        }
        return locked;
    }

</script>
@endsection
