@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
    <style>
        table td, table td * {
            vertical-align: top;
        }

        #announcement_table thead, #announcement_table tbody { display: block; }
        #announcement_table tbody {
            height: 267px;       
            overflow-y: auto;
            overflow-x: hidden;
        }

        #companypolicy_table thead, #companypolicy_table tbody { display: block; }
        #companypolicy_table tbody {
            height: 200px;       
            overflow-y: auto;
            overflow-x: hidden;
        }

        .mapboxgl-control-container{
            /*display: none;*/
        }
    </style>

    <div class="modal fade" id="page-1"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 text-left" style="font-size:16px">
                                    <i class="nav-icon fas fa-close close_camera" style="cursor: pointer"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <center>
                                        <div id="webcamContainer" class="webcamContainer"></div>
                                    </center>
                                </div>
                                <div class="col-md-12">
                                    <center>
                                        <button id="btnCapture" class="btn btn-primary" disabled>
                                            Take a Snapshot
                                        </button>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="page-2"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 text-left" style="font-size:16px">
                                    <i class="nav-icon fas fa-close close_camera" style="cursor: pointer"></i>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <center>
                                        <div id="mapContainer" style="width:100%; height:500px;"></div>
                                    </center>
                                </div>
                                <div class="col-md-12">&nbsp;</div>
                                <div class="col-md-12">
                                    <center>
                                        <button id="btnLocation" class="btn btn-primary" disabled>
                                            Confirm Location
                                        </button>
                                    </center>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div id="page-1" class="row gx-0 my-2 big_page" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-left" style="font-size:16px">
                            <i class="nav-icon fas fa-close close_camera" style="cursor: pointer"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <div id="webcamContainer" class="webcamContainer"></div>
                            </center>
                        </div>
                        <div class="col-md-12">
                            <center>
                                <button id="btnCapture" class="btn btn-primary" disabled>
                                    Take a Snapshot
                                </button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- <div id="page-2" class="row gx-0 my-2 big_page" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 text-left" style="font-size:16px">
                            <i class="nav-icon fas fa-close close_camera" style="cursor: pointer"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <center>
                                <div id="mapContainer" style="width:100%; height:500px;"></div>
                            </center>
                        </div>
                        <div class="col-md-12">&nbsp;</div>
                        <div class="col-md-12">
                            <center>
                                <button id="btnLocation" class="btn btn-primary" disabled>
                                    Confirm Location
                                </button>
                            </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <div id="dialogConfirmSave" class="modal">
        <div class="modal-content p-3 p-md-4 p-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-left" style="font-size:16px">
                        <i class="nav-icon fas fa-close close_camera" style="cursor: pointer"></i>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <center>
                            <img id="avatar" class="rounded" style="max-width: 100%; height: 100px" src="">
                        </center>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-center">
                        <h4>{{(@$attendance[0]->name)}}</h4>
                        <h5>{{(@$attendance[0]->position_detail)}}</h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <h4> <?= Session::get('shift') ?> Time</4>
                            <span class="attendanceTime green-text" id="dialogConfirmSave_date"></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <center>
                        <button id="btnSaveAttendance" class="btn btn-primary">
                            Save Attendance
                        </button>
                        
                        <button id="btnSaveAttendance_loading" class="btn btn-primary" style="display:none;">
                            <i class="fa fa-spinner fa-pulse"></i>
                        </button>
                    </center>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="dialogConfirmUpdate"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content p-3 p-md-4 p-lg-5" >
                <div class="container">
                    <div class="row">
                        <div class="col-12 align-items-center">
                            <center><h2>You already recorded your end time.</h2></center>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 align-items-center">
                            <center><h2>Do you want to update it?</h2></center>
                        </div>
                    </div>
                    <div class="row mt-12" style="padding-top:100px;">
                        <div class="col-6 align-items-center">
                            <center><button id="btnCancel" class="btn btn-primary">
                                Cancel
                            </button></center>
                        </div>
                        <div class="col-6 align-items-center">
                            <center><button id="btnUpdate" onclick="startAttendance()" class="btn btn-primary">
                                Update time
                            </button></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-left:50px;">
        <div class="col-lg-5">
            <center><h2></h2></center>
            <div class="row">
                <div class="col-lg-6">
                    <!-- small box -->
                    <div class="small-box bg-info" style="background-color:brown !important;">
                        <a href="javascript:void(0)" class="small-box-footer"><b>Department</b></a>
                        <div class="inner" style="height: 112px;">
                        <center>
                            <p>
                                <div id="department" style="font-size:20px;"><b><i id="loading_department" class="fa fa-spinner fa-pulse"></i></b></div>
                            </p>
                        </center>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <a href="javascript:void(0)" class="small-box-footer"><b>Age</b></a>
                        <div class="inner" style="height: 112px;">
                        <center>
                            <p>
                                <div id="age" style="font-size:30px;"><b><i id="loading_age" class="fa fa-spinner fa-pulse"></i></b></div>
                            </p>
                        </center>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <!-- small box -->
                    <div class="small-box bg-danger" style="background-color:deeppink !important;">
                        <a href="javascript:void(0)" class="small-box-footer"><b>Join</b></a>
                        <div class="inner" style="height: 112px;">
                        <center>
                            <p>
                                <div id="join"><i id="loading_join" class="fa fa-spinner fa-pulse"></i></div>
                            </p>
                        </center>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <a href="javascript:void(0)" class="small-box-footer"><b>Attendance</b></a>
                        <div class="inner" style="height: 112px;">
                        <center style="margin-top: -10px;">
                            <p>
                                <div id="checkin"><i id="loading_checkin" class="fa fa-spinner fa-pulse"></i></div>
                                <div id="checkout"><i id="loading_checkout" class="fa fa-spinner fa-pulse"></i></div>
                                <div style="margin-top: 5px;"><button id="btnRecord" class="btn btn-sm btn-default">Record Time</button></div>
                            </p>
                        </center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-1">
        </div>
        <div class="col-lg-5">
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:darkblue !important;"><b>Company Policy</b></a>
                <div class="inner" style="background-color:white !important;height: 317px;">
                    <p>
                        <div id="announcement" style="font-size:20px;"><i id="loading_announcement" class="fa fa-spinner fa-pulse"></i></div>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-left:50px;">
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-4" style="padding-right: 0px;">
                    <!-- small box -->
                    <div class="small-box" style="background-color:white !important;color:orange;height:209px;">
                        <div class="inner">

                        <center style="margin-top: 80px;"><p><b>Leave Request</b></p></center>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" style="padding-left: 0px;">
                    <!-- small box -->
                    <div class="small-box bg-info" style="background-color:orange !important;height:209px;margin-right:20px;">
                        <div class="inner">

                        <center style="margin-top: 70px;"><h3 id="leaverequest"><i id="loading_leaverequest" class="fa fa-spinner fa-pulse"></i></h3></center>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" style="padding-left: 0px;">
                     <div class="row" style="margin-left: -27px;margin-bottom:-20px;margin-top:10px;">
                        <div class="col-lg-6" style="padding: 0px;">
                            <!-- small box -->
                            <div class="small-box" style="background-color:white !important;color:blue;">
                                <div class="inner">

                                <center style="margin-top: 10px;"><p><b>This Month</b></p></center>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" style="padding-left: 0px;">
                            <!-- small box -->
                            <div class="small-box bg-info" style="background-color:blue !important;height:95px;">
                                <div class="inner">

                                <center style="margin-top: 10px;"><h3 id="leaverequest_thismonth"><i id="loading_leaverequest_thismonth" class="fa fa-spinner fa-pulse"></i></h3></center>
                                </div>
                            </div>
                        </div>
                     </div>
                     <div class="row" style="margin-left: -27px;">
                        <div class="col-lg-6" style="padding: 0px;">
                            <!-- small box -->
                            <div class="small-box" style="background-color:white !important;color:purple;">
                                <div class="inner">

                                <center style="margin-top: 10px;"><p><b>This Year</b></p></center>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" style="padding-left: 0px;">
                            <!-- small box -->
                            <div class="small-box bg-info" style="background-color:purple !important;height:95px;">
                                <div class="inner">

                                <center style="margin-top: 10px;"><h3 id="leaverequest_thisyear"><i id="loading_leaverequest_thisyear" class="fa fa-spinner fa-pulse"></i></h3></center>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6" style="padding-right: 0px;">
                    <!-- small box -->
                    <div class="small-box" style="background-color:white !important;color:darkred;height:50px;">
                        <div class="inner">

                        <center><p><b>Leave Balance</b></p></center>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" style="padding-left: 0px;">
                    <!-- small box -->
                    <div class="small-box bg-info" style="background-color:darkred !important;height:50px;">
                        <div class="inner">

                        <center style="margin-top: -7px;"><h3 id="leavebalance"><i id="loading_leavebalance" class="fa fa-spinner fa-pulse"></i></h3></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-1">
        </div>
        <div class="col-lg-5">
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:purple !important;"><b>Announcement</b></a>
                <div class="inner" style="background-color:white !important;height: 255px;">
                    <p>
                        <div id="companypolicy" style="font-size:20px;"><i id="loading_companypolicy" class="fa fa-spinner fa-pulse"></i></div>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-left:50px;">
        <div class="col-lg-5">
            <!-- small box -->
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:darkorange !important;"><b>Work Days History</b></a>
                <div class="inner" style="background-color:white !important;height: 255px;">
                    <p>
                        <div id="workdayshistory" style="font-size:15px;"><i id="loading_workdayshistory" class="fa fa-spinner fa-pulse"></i></div>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-1">
        </div>
        <div class="col-lg-5">
            <!-- small box -->
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:red !important;"><b>Upcoming Event</b></a>
                <div class="inner" style="background-color:white !important;height: 255px;">
                    <p>
                        <div id="upcomingevent" style="font-size:15px;"><i id="loading_upcomingevent" class="fa fa-spinner fa-pulse"></i></div>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-left:50px;">
        <div class="col-lg-11">
            <!-- small box -->
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:grey !important;"><b>Structure Hierarchy</b></a>
                <div class="inner" style="background-color:white !important;">
                   <div class="defchart" id="defchart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-left:50px;">
        <div class="col-lg-11">
            <!-- small box -->
            <div class="small-box bg-default">
                <a href="javascript:void(0)" class="small-box-footer" style="background-color:blue !important;"><b>The Documents Need Your Approval</b></a>
                <div class="inner" style="background-color:white !important;">
                    <table id="employee_approval_table" style="width:100%;" class="display nowrap table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>                   
                                <th></th>
                                <!-- <th></th> -->
                                <th>No</th>
                                <th>Reference Number</th>
                                <th>Request By</th>
                                <th>Request Name</th>
                                <th>Transaction Name</th>
                                <th>Description</th>
                                <th>Approval Name</th>
                                <th data-priority="1">Approval Status</th>
                                <th data-priority="2" width=200>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_form_announcement">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <form method="POST" id="announcementForm">
                    {{ csrf_field() }}
        
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Reference Number</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="reference_number" id="reference_number" class="form-control form-control-sm" readonly="readonly">
                                        <span class="invalid-feedback" role="alert" id="reference_numberError">
                                            <strong></strong>
                                        </span>                                    
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Description</label>
                                    <div class="col-sm-8">
                                        <input type="text" name="description" id="description" class="form-control form-control-sm" readonly="readonly">
                                        <span class="invalid-feedback" role="alert" id="descriptionError">
                                            <strong></strong>
                                        </span>                                    
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Request By</label>
                                    <div class="col-sm-8">
                                        <select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="id_employee_requestError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Announcement Type</label>
                                    <div class="col-sm-8">
                                        <select name="id_anouncement_type" id="id_anouncement_type" class="form-control form-control-sm select2" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="id_anouncement_typeError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6" style="margin-bottom:20px;">
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Company</label>
                                    <div class="col-sm-8">
                                        <select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="companyError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Enable Approval</label>
                                    <div class="col-sm-8">
                                        <input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                        <span class="invalid-feedback" role="alert" id="publishedError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Start Date to End Date</label>                               
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm"/>
                                            <input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
                                            <input name="end_date" id="end_date" class="form-control form-control-sm" hidden>                                       
                                            <div class="input-group-append">
                                                    <span class="input-group-text far fa-calendar form-control-sm"></span>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <div class="col-md-12">
                                <div class="row">                             
                                    <label class="col-sm-2 col-form-label">Announcement Content</label>
                                    <div class="col-sm-10">
                                        <div class="form-group">
                                            <textarea class="summernote" name="content_letter" id="content_letter"></textarea>
                                        </div> 
                                    </div> 
                                </div>
                            </div>
                           <div class="col-md-6"></div>
                        </div>
                    </div>
                 
                </form>
            </div>
        </div>
    </div>

    @if(@$attendance[0]->actual_time_in != null)
        {{ Session::put('shift', "End") }}
    @elseif(@$attendance[0]->actual_time_in == null)
        {{ Session::put('shift', "Start") }}
    @endif

    @if(@$attendance[0]->actual_time_out != null)
        {{ Session::put('shift', "End") }}
    @elseif(@$attendance[0]->actual_time_out == null)
    @endif

@stop

@section('css')
    <!-- <link rel="stylesheet" href="/css/admin_custom.css"> -->
    <link rel="stylesheet" href="{{ asset('vendor/orgchart/css/jquery.orgchart.css') }}">
    <style type="text/css"> 
.defchart {
  text-align:center;
  overflow:auto;
  margin:20px;
  border:1px solid #dc3545;
}
</style>
@stop

@section('scripts')
<script type="text/javascript" src="https://dabeng.github.io/OrgChart/js/jquery.mockjax.min.js"></script>
    <script src="{{ asset('vendor/orgchart/js/jquery.orgchart.js') }}"></script>
    <script>
        var datasource = {!! json_encode($quechart) !!}
        $.mockjax({
        url: '/orgchart/initdata',
        responseText: datasource
      });
   $('#defchart').orgchart({
      'data' : '/orgchart/initdata',
      'nodeContent': 'title',
      'nodeID': 'id',
      'zoom': true,
      'zoominLimit': 2,
      'zoomoutLimit': 0,
    });

        $.ajax({
            type: 'POST',
            url: "<?= url('/home/dashboardinformation') ?>",
            dataType: 'json',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (result) {
                $("#loading_department").hide();
                $("#loading_age").hide();
                $("#loading_join").hide();
                $("#loading_checkin").hide();
                $("#loading_checkout").hide();
                $("#loading_announcement").hide();
                $("#loading_companypolicy").hide();
                $("#loading_upcomingevent").hide();
                $("#loading_workdayshistory").hide();
                $("#loading_leavebalance").hide();
                $("#loading_leaverequest").hide();
                $("#loading_leaverequest_thismonth").hide();
                $("#loading_leaverequest_thisyear").hide();

                if(result.data.employeeinformation!=null)
                {
                    $("#department").html(result.data.employeeinformation.department);
                    $("#age").html(result.data.employeeinformation.age.substring(0,result.data.employeeinformation.age.indexOf(' Years')));
                    $("#join").html(result.data.employeeinformation.period_of_employment.replace(/&/g,'<br>'));
                    if(!result.data.employeeinformation.actual_time_in)
                    {
                        $("#checkin").html('Check in: '+result.data.employeeinformation.actual_time_in);
                    }
                    else
                    {
                        $("#checkin").html('Check in: '+result.data.employeeinformation.actual_time_in.substring(11,16));
                    }

                    if(!result.data.employeeinformation.actual_time_out)
                    {
                        $("#checkout").html('Check out: '+result.data.employeeinformation.actual_time_out);
                    }
                    else
                    {
                        $("#checkout").html('Check out: '+result.data.employeeinformation.actual_time_out.substring(11,16));
                    }
                }

                if(result.data.leavebalance!=null)
                {
                    $("#leavebalance").html(result.data.leavebalance.leave_quota);
                }

                if(result.data.leaverequest!=null)
                {
                    $("#leaverequest").html(result.data.leaverequest.total_request);
                    $("#leaverequest_thismonth").html(result.data.leaverequest.this_month);
                    $("#leaverequest_thisyear").html(result.data.leaverequest.this_year);
                }

                $("#announcement").html('');
                var word_announcement = "";
                word_announcement += '<table id="announcement_table">';
                if(result.data.announcement!=null)
                {
                    $.each(result.data.announcement, function(i, val){
                        word_announcement += '<tr><td><font size="3%">-</font></td><td style="padding-left:10px;"><font size="3%"><a href="#" id="'+val.id_announcement+'" class="view_announcement">'+val.description+'</a</font></td></tr>';
                    });
                }
                word_announcement += '</table>';
                $("#announcement").html(word_announcement);

                $("#companypolicy").html('');
                var word_companypolicy = "";
                word_companypolicy += '<table id="companypolicy_table">';
                if(result.data.companypolicy!=null)
                {
                    $.each(result.data.companypolicy, function(i, val){
                        word_companypolicy += '<tr><td><font size="3%">-</font></td><td style="padding-left:10px;"><font size="3%">'+val.description+'</font></td></tr>';
                    });
                }
                word_companypolicy += '</table>';
                $("#companypolicy").html(word_companypolicy);

                $("#upcomingevent").html('');
                var word_upcomingevent = "";
                word_upcomingevent += '<table id="upcomingevent_table"><tr><th></th><th style="padding-left:10px;">Description</th><th>Date</th></tr>';
                if(result.data.upcomingevent!=null)
                {
                    $.each(result.data.upcomingevent, function(i, val){
                        word_upcomingevent += '<tr><td>-</td><td style="padding-left:10px;">'+val.description+'</td><td>'+val.start_date+'</td></tr>';
                    });
                }
                word_upcomingevent += '</table>';
                $("#upcomingevent").html(word_upcomingevent);

                $("#workdayshistory").html('');
                var word_workdayshistory = "";
                word_workdayshistory += '<table id="workdayshistory_table"><tr><th style="padding-right:50px;">Date</th><th style="padding-left:10px;">Day</th><th style="padding-left:30px;padding-right:50px;">In</th><th style="padding-left:30px;">Out</th></tr>';
                if(result.data.workdayshistory!=null)
                {
                    $.each(result.data.workdayshistory, function(i, val){
                        word_workdayshistory += '<tr><td>'+val.current_dates+'</td><td style="padding-left:10px;">'+val.day_type+'</td><td style="padding-left:30px;">'+val.actual_time_in+'</td><td style="padding-left:30px;">'+val.actual_time_out+'</td></tr>';
                    });
                }
                word_workdayshistory += '</table>';
                $("#workdayshistory").html(word_workdayshistory);
            },
            error: function (result) {
            },
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
                $.ajax({
                        url: "<?= url('employee/employee/announcement/get_announcement_edit') ?>",
                        method: "GET",
                        data: {id_announcement: id_announcement},
                        success: function (response) {
                            $('#reference_number').val(response.reference_number).trigger('change');
                            $('#description').val(response.description).trigger('change');
                            $('#id_employee_request').val(response.id_employee_request).trigger('change');
                            $('#start_date').val(response.start_date).trigger('change');
                            $('#end_date').val(response.end_date).trigger('change');                    
                            $('#id_anouncement_type').val(response.id_anouncement_type).trigger('change');
                            $("#content_letter").summernote("code", response.content_letter);
                            $('#id_company').val(response.id_company).trigger('change');
                            $('#enable_approval').attr('disabled', true);
                            $('#daterange').attr('disabled', true);                 
                                                $('#daterange').daterangepicker({
                                                    uiLibrary: 'bootstrap4',
                                                            autoApply: true,
                                                            opens: 'center',
                                                            locale: {
                                                                format: 'YYYY-MM-DD',
                                                                separator: '   to   ',
                                                                closeText: 'Clear',
                                                                },
                                                }, function(start, end, label) {
                                                    $("#start_date").val(start.format('YYYY-MM-DD'));
                                                    $("#end_date").val(end.format('YYYY-MM-DD'));
                                                                    
                                                }); 
                            $("#content_letter").summernote("code", response.content_letter);
                            $('.summernote').summernote('disable');
                        },
                        error: function (xhr) {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                            });
                        }
                    });
            $('#modal_form_announcement').modal('show');
        });
        
    </script>
    <script type="text/javascript" src="<?= asset('vendor/webcam/webcam.js') ?>"></script>
    <script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
    <script type="text/javascript">
    
        $(document).ready(function () {
            if(localStorage.getItem("dataabsen")!=null)
            {
                $.ajax({
                    type: 'POST',
                    url: "<?= url('/time_attendance/attendance/sinkron_absen_localstorage?id=') ?>"+JSON.parse(localStorage.getItem("dataabsen"))[0]['id_workdays'],
                    dataType: 'json',
                    data: {
                        id_workdays: JSON.parse(localStorage.getItem("dataabsen"))[0]['id_workdays'],
                        current_latitude: JSON.parse(localStorage.getItem("dataabsen"))[0]['current_latitude'],
                        current_longitude: JSON.parse(localStorage.getItem("dataabsen"))[0]['current_longitude'],
                        image_attachment: JSON.parse(localStorage.getItem("dataabsen"))[0]['image_attachment'],
                        shift: JSON.parse(localStorage.getItem("dataabsen"))[0]['shift'],
                        _token: JSON.parse(localStorage.getItem("dataabsen"))[0]['_token'],
                        mapboxAccessToken: JSON.parse(localStorage.getItem("dataabsen"))[0]['mapboxAccessToken'],
                        starttimeabsen: JSON.parse(localStorage.getItem("dataabsen"))[0]['start_time'],
                        endtimeabsen: JSON.parse(localStorage.getItem("dataabsen"))[0]['end_time']
                    },
                    success: function (result) {
                        localStorage.removeItem("dataabsen");
                    }
                });
            }
        })
    
        var page = 0;
        var img = null;
        var lat = 0;
        var lng = 0;
        var mapboxAccessToken = "pk.eyJ1IjoiZ2FicmllbGxhaGVudmlhbmkiLCJhIjoiY2tmbDN3dWR5MHl0cTJ3a3U4Zm4xNnh2MyJ9.B5QKOeDOHhJw4on1WZ6urw";
        var shift = "<?= Session::get('shift') ?>";
        $("#btnRecord").click(function () {
            let actual_time_out = "<?= @$attendance[0]->actual_time_out ?>";
            if (actual_time_out == null || actual_time_out == "" || actual_time_out == " ") {
                startAttendance();
            } else {
                // $("#dialogConfirmUpdate").show();
                $('#dialogConfirmUpdate').modal('show');
            }
        });
        function startAttendance() {
            changePage();
            showCam();
        }
        $(".close_camera").click(function () {
            location.reload();
        });
        $("#btnCancel").click(function () {
            // $("#dialogConfirmUpdate").hide();
            $('#dialogConfirmUpdate').modal('hide');

        });
        $("#btnCapture").click(function () {
            Webcam.snap(function (data_uri) {
                img = data_uri;
                changePage();
                Webcam.reset();
                setTimeout(function () {
                    drawMap();
                }, 1000);
            });
        });
        function showCam() {
            Webcam.set({
                width: 450,
                height: 550,
                image_forat: "jpeg",
                jpeg_quality: 90,
                force_flash: false,
                flip_horiz: true,
                fps: 45
            });
            Webcam.set("constraints", {
                optional: [{ minWidth: 0 }]
            });
            Webcam.on('live', function () {
                $("#btnCapture").prop('disabled', false);
            });
            Webcam.attach('#webcamContainer');
        }
    
        function changePage() {
            // $("#dialogConfirmUpdate").hide();
            $('#dialogConfirmUpdate').modal('hide');

            $("#page-" + page).modal('hide');
            // $("#page-" + page).hide();
            page++;
            $("#page-" + page).modal('show');
            // $("#page-" + page).show();
        }
    
        function drawMap() {
            mapboxgl.accessToken = mapboxAccessToken;
            var map = new mapboxgl.Map({
                container: 'mapContainer',
                style: 'mapbox://styles/mapbox/streets-v11',
                center: [0, 0],
                zoom: 12,
                interactive: false,
            });
            var geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {enableHighAccuracy: true},
                showAccuracyCircle: true,
            })
    
            geolocate.on('geolocate', function (userlocation) {
                lat = userlocation.coords.latitude;
                lng = userlocation.coords.longitude;
            });

            map.on('load', () => {
                map.resize();
                geolocate.trigger();
            });
            map.on('idle', () => {
                $("#btnLocation").prop('disabled', false);
            })
    
            map.addControl(geolocate);
            map.addControl(new mapboxgl.NavigationControl());
        }
    
        $("#btnLocation").click(function () {
            var dialogConfirmSave_date = new Date();
            $("#dialogConfirmSave").toggle();
            $("#dialogConfirmSave_date").html(((dialogConfirmSave_date.getHours() < 10)?"0":"") + (dialogConfirmSave_date.getHours())+':'+((dialogConfirmSave_date.getMinutes() < 10)?"0":"") + dialogConfirmSave_date.getMinutes());
            $("#avatar").attr('src', img)
        });
        function closeConfirmModal() {
            $("#dialogConfirmSave").toggle();
        }
    
        function getAddress() {
            return new Promise(function (resolve, reject) {
                var ajax = new XMLHttpRequest();
                ajax.open("GET", );
                ajax.send();
            });
        }
    
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $("#btnSaveAttendance").click(function () {
            var datenow = "<?= date('Y-m-d') ?>";
            var dataabsen = new Array;
            var valueabsen = {};
            var timenow = parseInt("<?= date('H') ?>");
            let _token = $('meta[name="csrf-token"]').attr('content');
            
            valueabsen["id_user"] = "<?= Session::get('id_user') ?>";
            valueabsen["date"] = datenow;
            
            if(timenow<12)
            {
                valueabsen["start_time"] = datenow.concat(" ", $("#dialogConfirmSave_date").html());
                valueabsen["end_time"] = "";
            }
            else if(timenow>=12)
            {
                valueabsen["start_time"] = "";
                valueabsen["end_time"] = datenow.concat(" ", $("#dialogConfirmSave_date").html());
            }
            valueabsen["id_workdays"] = "<?= @$attendance[0]->id_workdays ?>",
            valueabsen["current_latitude"] = lat,
            valueabsen["current_longitude"] = lng,
            valueabsen["image_attachment"] = img,
            valueabsen["shift"] = shift,
            valueabsen["_token"] = _token,
            valueabsen["mapboxAccessToken"] = mapboxAccessToken,
        
            dataabsen.push(valueabsen);
            localStorage.setItem("dataabsen",JSON.stringify(dataabsen));
            $("#btnSaveAttendance").hide();
            $("#btnSaveAttendance_loading").show();
            var formdata = new FormData();
            var id_workdays = "";
            
            $.ajax({
                type: 'POST',
                url: "<?= url('/time_attendance/attendance/update?id=' . @$attendance[0]->id_workdays) ?>",
                dataType: 'json',
                data: {
                    id_workdays: id_workdays,
                    current_latitude: lat,
                    current_longitude: lng,
                    image_attachment: img,
                    shift: shift,
                    _token: _token,
                    mapboxAccessToken: mapboxAccessToken,
                    starttimeabsen: valueabsen["start_time"],
                    endtimeabsen: valueabsen["end_time"]
                },
                success: function (result) {
                    localStorage.removeItem("dataabsen");
                    $("#btnSaveAttendance").show();
                    $("#btnSaveAttendance_loading").hide();
                    console.log(result['message']);
                    alert(result['message']);
                    location.reload();
                },
                error: function (result) {
                    $("#btnSaveAttendance").show();
                    $("#btnSaveAttendance_loading").hide();
                    console.log(result['responseJSON']['message']);
                    // alert(result['responseJSON']['message']);
                    alert("Your connection is unstable, attendance record has been recorded locally");
                },
            });
        });
    </script>
@stop