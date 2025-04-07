@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-2"></div>
            <div class="col-sm-4 justify-content-end">
                <div class="input-group input-group-sm">
                    <select id="allEmployee" class="form-control form-control-sm " style="height: 100%;" ></select>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
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

        .card_employee_info {
            min-height: 110px;
        }
		.btn-outline-success {
			color: #393a39;
			border-color: #28a745;
		}
		.answer_label {
			margin-bottom: 0px;
		}
    </style>

    @if($getCampaign->count() > 0)
        
    @endif

    <section class="content pb-3">
        <div class="container-fluid h-100">
            <div class="row">
                <div class="col-lg-12 col-12">
                    <div class="card card-row card-gray-dark collapsed-card collaps_user_information">
                        <div class="card-header">
                            <h3 class="card-title">User Information & Attendance</h3>
                            <div class="card-tools">
                                <button type="button" id="collaps_user_information" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_user_information_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-2 col-12" >
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
                                                <div class="col-md-12" id="employee_request" style="font-size:13px;"></div>
                                            </div>
                                            <div style="margin-top: 5px;">
                                                <button onclick="location.href='<?= route('employee_request.index') ?>'" id="btnRequest" class="btn btn-sm btn-success">Add Request</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-12">
                                    <div class="small-box bg-danger">
                                        <a href="#" class="small-box-footer text-bold">Attendance</a>
                                        <div class="inner justify-content-center text-center">
                                            <div class="row">
                                                <div class="col-md-6 text-bold" id="checkin" style="font-size:17px;"><i id="loading_checkin" class="fa fa-spinner fa-pulse"></i></div>
                                                <div class="col-md-6 text-bold" id="checkout" style="font-size:17px;"><i id="loading_checkout" class="fa fa-spinner fa-pulse"></i></div>
                                            </div>
                                            
                                            <div style="margin-top: 5px;">
                                                @if($haveAttendanceMenu)
                                                    <button id="btnRecord" class="btn btn-sm btn-success">Record Time</button>
                                                @else
                                                    <button class="btn btn-sm btn-danger" style="visibility:hidden;" disabled>Not Allowed</button>   
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="card card-primary card-tabs">
                                        <div class="card-header p-0 pt-1">
                                            <div class="card-tools">
                                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_all_attendance_status" style="margin-right:20px;">Show All Detail Status</button>
                                            </div>
                                            <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active text-bold" id="attendance_per_month-tab" data-toggle="pill" href="#attendance_per_month" role="tab" aria-controls="attendance_per_month" aria-selected="true">Attendance this month</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link text-bold" id="attendance_per_year-tab" data-toggle="pill" href="#attendance_per_year" role="tab" aria-controls="attendance_per_year" aria-selected="false">Attendance this year</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card-body">
                                            <div class="tab-content" id="custom-tabs-two-tabContent">
                                                <div class="tab-pane fade show active" id="attendance_per_month" role="tabpanel" aria-labelledby="attendance_per_month-tab">
                                                    <div class="row">
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="javascript:;" class="small-box-footer text-bold show_attendance_status" attendance_status="abs" >ABS </a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="abs_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">ANL</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="anl_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">NSI</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="nsi_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">NSO</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="nso_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">OFF</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="off_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">PRS</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="prs_month"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="attendance_per_year" role="tabpanel" aria-labelledby="attendance_per_year-tab">
                                                    <div class="row">
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">ABS</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="abs_year"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">ANL</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="anl_year"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">NSI</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="nsi_year"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">NSO</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="nso_year"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">OFF</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="off_year"> - </h3>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2 col-12">
                                                            <div class="small-box bg-light">
                                                                <a href="#" class="small-box-footer text-bold">PRS</a>
                                                                <div class="inner justify-content-center text-center">
                                                                    <h3 id="prs_year"> - </h3>
                                                                </div>
                                                            </div>
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
                <div class="col-lg-6 col-12">
                    <div class="card card-row card-lightblue ">
                        <div class="card-header">
                            <h3 class="card-title">Announcement</h3>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-success" onclick="location.href='<?= url($url_announcement)?>' "> More Info</button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-y: scroll; height:200px;">
                            <div class="inner justify-content-center">
                                <div id="announcement" style="font-size:20px;"><i id="loading_announcement" class="fa fa-spinner fa-pulse"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="card card-row card-warning">
                        <div class="card-header">
                            <h3 class="card-title">Company Policy</h3>
                            <div class="card-tools">
                                <button class="btn btn-sm btn-success" onclick="location.href='<?= url($url_announcement)?>' ">More Info</button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-y: scroll; height:200px;">
                            <div class="inner justify-content-center">
                                <div id="companypolicy" style="font-size:20px;"><i id="loading_companypolicy" class="fa fa-spinner fa-pulse"></i></div>
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
                    <div class="card card-row card-gray-dark collapsed-card collaps_news">
                        <div class="card-header">
                            <h3 class="card-title">News</h3>
                            <div class="card-tools">
                                <button type="button" id="collaps_news" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_news_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body" style="height:auto;">
                            <div class="row" id="news" >
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
                    <div class="card card-row card-gray-dark collapsed-card collaps_leave">
                        <div class="card-header">
                            <h3 class="card-title">Annual Leave Information</h3>
                            <div class="card-tools">
                                <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modal_all_leave" style="margin-right:20px;">More Info</button>
                                <button type="button" id="collaps_leave" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_leave_icon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-12 col-12 d-flex justify-content-center mb-3">
                                    <h3 class="card-title" style="font-size:20px;">Annual Leave</h3>
                                </div>

                                <div class="col-lg-3 col-12">
                                    <div class="small-box bg-light">
                                        <a href="#" class="small-box-footer text-bold">Leave Request</a>
                                        <div class="inner justify-content-center text-center">
                                            <h3 id="leaverequest">
                                                <i id="loading_leaverequest" class="fa fa-spinner fa-pulse"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="small-box bg-light">
                                        <a href="#" class="small-box-footer text-bold">This Month</a>
                                        <div class="inner justify-content-center text-center">
                                            <h3 id="leaverequest_thismonth">
                                                <i id="loading_leaverequest_thismonth" class="fa fa-spinner fa-pulse"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="small-box bg-light">
                                        <a href="#" class="small-box-footer text-bold">This Year</a>
                                        <div class="inner justify-content-center text-center">
                                            <h3 id="leaverequest_thisyear">
                                                <i id="loading_leaverequest_thisyear" class="fa fa-spinner fa-pulse"></i>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-12">
                                    <div class="small-box bg-light">
                                        <a href="#" class="small-box-footer text-bold">Leave Balance</a>
                                        <div class="inner justify-content-center text-center">
                                            <h3 id="leavebalance">
                                                <i id="loading_leavebalance" class="fa fa-spinner fa-pulse"></i>
                                            </h3>
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

    <section class="content pb-3">
        <div class="container-fluid h-100">
            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card card-row card-gray-dark collapsed-card collaps_workdays">
                        <div class="card-header">
                            <h3 class="card-title">Work Days History</h3>
                            <div class="card-tools">
                              <button type="button" id="collaps_workdays" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_workdays_icon"></i>
                              </button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-y: scroll; height:350px;">
                            <div class="inner justify-content-center" id="workdays_history">
                                <?php
                                    if(!is_null(json_encode(@$attendance))){
                                        echo view('dashboard.workdays', compact('attendance'))->render(); 
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12">
                    <div class="card card-row card-gray-dark collapsed-card collaps_event">
                        <div class="card-header">
                            <h3 class="card-title">Course / Event</h3>
                            <div class="card-tools">
                              <button type="button" id="collaps_event" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_event_icon"></i>
                              </button>
                            </div>
                        </div>
                        <div class="card-body" style="overflow-y: scroll; overflow-x: scroll; height:350px;">
                            <div class="inner justify-content-center">
                                <div id="upcomingevent" style="font-size:15px;"><i id="loading_upcomingevent" class="fa fa-spinner fa-pulse"></i></div>
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
                    <div class="card card-row card-gray-dark collapsed-card collaps_hirarki">
                        <div class="card-header">
                            <h3 class="card-title">Structure Hierarchy</h3>
                            <div class="card-tools">
                              <button type="button" id="collaps_hirarki" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                <i class="fas fa-plus collaps_hirarki_icon"></i>
                              </button>
                            </div>
                        </div>
                        <div class="card-body" >
                            <div class="inner justify-content-center">
                               <div class="defchart" id="defchart"></div>
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
                    <h4 class="modal-title">All Leave</h4>
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

@include('employee.employee.announcement.popup')

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
		.callout a:hover {
		  color: #2c5699;
		  font-weight:bold;
		}
		a:link {
		  text-decoration: none;
		  color:#4b70ab;
		}
		a:visited {
		  text-decoration: none;
		}
    </style>
@stop

@section('scripts')
    <script type="text/javascript" src="https://dabeng.github.io/OrgChart/js/jquery.mockjax.min.js"></script>
    <script src="{{ asset('vendor/orgchart/js/jquery.orgchart.js') }}"></script>
    <script>
		let avatar = "{{ asset('public/global/img/avatar.jpg') }}";		
        let allEmployee = {!! $allEmployee !!}
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
            $("#loading_workdayshistory").hide();
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

        const leave_information = async () => {
            let result;
            try {
                result = await $.ajax({
                    type: 'GET',
                    url: "<?= url('dashboard/leave_information') ?>",
                    dataType: 'json',
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    success: function (result) {
                    }
                });
                return result;
            } catch (error) {
                leave_information();
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
                // hierarchy();
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
        attendance_per_month()
        attendance_per_year()
        checkCollapse()

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

            if(result.all_leave != null){
                $("#all_leave").html('');
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
            }
        });

        hierarchy().then(datasource => {
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
              'createNode': function($node, data) {
                 
                     let actual_time_in = data.actual_time_in;
                     let actual_time_out = data.actual_time_out;
                     let att_level = data.att_level;
                     let lng_in = data.current_longitude_in;
                     let lat_in = data.current_latitude_in;
                     let lng_out = data.current_longitude_out;
                     let lat_out = data.current_latitude_out;
                     
                     if(actual_time_in != null){
                         time_in = actual_time_in.split(' ')[1].slice(0, 5);
                         if(lng_in != null && lat_in !== null){
                             maps_time_in = '<span style="font-size:13px;color:#529f88;cursor: pointer;" onclick=showMap('+lng_in+','+lat_in+')><br><span style="font-size:17px;margin-right:3px;" class="fa fa-map-marker"></span>'+time_in+'</span>';
                         }
                         else{
                             maps_time_in = '<span style="font-size:13px;color:#529f88;"><br>'+time_in+'</span>';
                         }              
                     }
                     else{
                         time_in = '--:--';
                         maps_time_in = '<span style="font-size:13px;color:#529f88;"><br>'+time_in+'</span>';
                     }
                    
                     if(actual_time_out != null){
                         time_out = actual_time_out.split(' ')[1].slice(0, 5);
                         if(lng_out != null && lat_out !== null){
                             maps_time_out = '<span style="font-size:13px;color:#529f88;cursor: pointer;" onclick=showMap('+lng_out+','+lat_out+')><br><span style="font-size:17px;margin-right:3px;" class="fa fa-map-marker"></span>'+time_out+'</span>';
                         }
                         else{
                             maps_time_out = '<span style="font-size:13px;color:#529f88;"><br>'+time_out+'</span>';
                         }                   
                     }
                     else{
                         time_out = '--:--';
                         maps_time_out = '<span style="font-size:13px;color:#529f88;"><br>'+time_out+'</span>';
                     }
      
                     let img_profile = data.image_attachment;

                    var secondMenuIcon = $('<i></i>', {
                      click: function() {
                        $(this).siblings('.second-menu').toggle();
                      }
                    });
                    img_profile = img_profile.replaceAll(" ", "%20");
                    if(att_level != 0){
                        if(lng_in != null && lat_in != null || lng_out != null && lat_out != null){
                            var secondMenu = '<div class="second-menu" style="font-size:11px;padding:0 10px 0 10px;">';
                            secondMenu += '<b style="float:left;">Check In '+maps_time_in+'</b>';
                            secondMenu += '<img style="margin-left:5px;" class="avatar" src='+img_profile+'>';
                            secondMenu += '<b style="float:right;">Check Out '+maps_time_out+'</b>';
                            secondMenu += '</div>';
                        }
                        else{
                            var secondMenu = '<div class="second-menu" style="font-size:11px;padding:0 10px 0 10px;">';
                            secondMenu += '<b style="float:left;">Check In '+maps_time_in+'</b>';
                            secondMenu += '<img style="margin-left:5px;" class="avatar" src='+img_profile+'>';
                            secondMenu += '<b style="float:right;">Check Out '+maps_time_out+'</b>';
                            secondMenu += '</div>';         
                        }                       
                    }
                    else{
                        var secondMenu = '<div class="second-menu">';
                        secondMenu += '<img class="avatar" src='+img_profile+'>';
                        secondMenu += '</div>';
                    }
                    $node.append(secondMenuIcon).append(secondMenu);
                    
                  }              
            });
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
                    word_companypolicy += `<li class="callout callout-warning" style="padding:8px;font-size:16px;">
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
            let eventAvailable = '';

            if(result != ''){
                $.each(result, function(i, val){
                    let buttonStart = '';
                    let buttonSHow = '';

                    buttonStart = val.event_category=='Learning' ? 'Start' : 'Join Event';
                    buttonShow = val.event_category=='Learning' ? 'Show' : '';

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

                    if(val.status_join == 'available' || val.status_join == 'Unconfirm'){
                        action = `<div class="text-right"><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm text-white" title="Start" type="${val.event_category}" >${buttonStart}</a></div>`;
                    } else if(val.status_join == 'done'){
                        action = `<div class="text-right"><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm text-white" title="Join" type="${val.event_category}" >${buttonShow}</a></div>`;
                    } else if(val.status_join == 'expired'){
                        action = ``;
                    }

                    if(val.status_join != 'done'){
                        if(val.status_join == 'available'){
                            if(val.event_category=='Learning'){
                                courseAvailable += `
                                    <div class="row mt-2 mb-4" style="width:100%;">
                                    <div class="col-md-4">${schedule_date}</div>
                                    <div class="col-md-5">${val.program} (${val.course})</div>
                                    <div class="col-md-2">${status_event}</div>
                                    <div class="col-md-1">${action}</div>
                                    </div>
                                `;
                            } else {
                                eventAvailable += `
                                    <div class="row mt-2 mb-4" style="width:100%;">
                                    <div class="col-md-4">${schedule_date}</div>
                                    <div class="col-md-5">${val.program} (${val.course})</div>
                                    <div class="col-md-2">${status_event}</div>
                                    <div class="col-md-1">${action}</div>
                                    </div>
                                `;
                            }
                        } else {
                            if(val.event_category=='Learning'){
                                courseNotAllowed += `
                                    <div class="row mt-2 mb-4" style="width:100%;">
                                    <div class="col-md-4">${schedule_date}</div>
                                    <div class="col-md-5">${val.program} (${val.course})</div>
                                    <div class="col-md-2">${status_event}</div>
                                    <div class="col-md-1">${action}</div>
                                    </div>
                                `;
                            } else {
                                eventAvailable += `
                                    <div class="row mt-2 mb-4" style="width:100%;">
                                    <div class="col-md-4">${schedule_date}</div>
                                    <div class="col-md-5">${val.program} (${val.course})</div>
                                    <div class="col-md-2">${status_event}</div>
                                    <div class="col-md-1">${action}</div>
                                    </div>
                                `;
                            }
                        }
                    }
                });
                word_upcomingevent += `
                    <h4 class="text-primary text-center mb-4">Course</h4>
                    ${courseAvailable}
                    ${courseNotAllowed}
                `;

                if(eventAvailable != ''){
                    word_upcomingevent += `
                        <hr><h4 class="text-primary text-center mb-4">Event</h4><div class="mt-5">${eventAvailable}</div>
                    `;
                }
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
            
            if(viewNews != ''){
                let splide = new Splide(`#main-carousel`, {
                    heightRatio : 0.4,
                }).mount();
            }
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

        function checkCollapse() {
            let collapse = ['collaps_user_information', 'collaps_leave', 'collaps_news', 'collaps_workdays', 'collaps_event', 'collaps_hirarki', 'collaps_birthday'];
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

        $("#collaps_user_information, #collaps_leave, #collaps_news, #collaps_workdays, #collaps_event, #collaps_hirarki, #collaps_birthday").click(function () {
            let id = $(this).attr('id');
            if(localStorage.getItem(id)){
                localStorage.removeItem(id);
            } else {
                localStorage.setItem(id, 'true');
            }
        });

		function showMap(lng, lat) {
            drawMaping(lng, lat).then(res => {
                $("#maps_loc").modal('show');
            });
        }

        function numberFormat (n) {
            return n.toLocaleString("id-ID");;
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
    </script>

    @if(!is_null(@$attendance) && $haveAttendanceMenu)
    @include('time_attendance.attendance.attendance', ['employee' => $employee])
    @endif
@stop