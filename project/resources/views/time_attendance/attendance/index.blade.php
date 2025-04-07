@extends('adminlte::page')
@section('title', 'Attendance')

@section('content')
<link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
<style type="text/css">
    .big_page{
        position: absolute;top: 0;left: 0;width: 100%;height: 100%;
    }
    .attendanceDay{
        font-size: 18px;font-weight: bold;text-align: center;
    }
    .attendanceTime{
        font-size: 20px;font-weight: bold;
    }
    .box-img-header{
        width: 40px;height: 40px;border-radius: 200px;background-color: #a5b0b7;overflow: hidden;
    }
    .img-header{
        touch-action: pan-y; user-select: none; -webkit-user-drag: none; -webkit-tap-highlight-color: rgba(0, 0, 0, 0);width: 100%;height: 100%;object-fit: cover;object-position: top;
    }
    .box-img{
        width: 40px;height: 40px;border-radius: 200px;background-color: #a5b0b7;overflow: hidden;display: block;margin-left: auto;margin-right: auto;margin-top: 10px;
    }
    @media (max-width: 483px) {
        #webcamContainer video {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
        #webcamContainer img {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
    }
    @media (max-width: 741px) {
        #webcamContainer video {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
        #webcamContainer img {
            /*max-width: 100%;max-height: 100%;*/
            /* margin-left: 40%;; */
        }
    }
    .alert_title {
        color:red;font-weight: bold;
    }
    .border-alert {
        border: 2px #ff0000 solid;
        animation: blink 1s;
        animation-iteration-count: infinite ;
    }
    @keyframes blink { 50% { border-color:#fff ; }  }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Attendance</h5>
                <div class="card-tools">
                    <button class="btn btn-sm btn-success" onclick="location.href='<?= url($url_home)?>' "><i class="fas fa-home"></i> Dashboard</button>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <div class="card card-widget widget-user">
                            <!-- <img src="{{url('public/global/img/attendance.png')}}" style=""></img> -->
                            <div class="widget-user-header" style="background: url(<?=url('public/global/img/attendance.png')?>) center center;background-size: cover; ">
                            </div>
                            <div class="widget-user-image" style="top:15%;">
                                <img class="img-circle elevation-2" src="{{ $avatar }}" style="height:100px;border-radius:10%;" alt="User Avatar">
                            </div>
                            <div class="col-md-12" >
                                <?php 
                                    if($absLastMonth['date'] != '' || $absCurrentMonth['date'] != ''){
                                        $paddingInfo = 'padding:85px 5px 15px;';
                                    } else {
                                        $paddingInfo = 'padding:60px 5px 15px;';
                                    }
                                ?>
                                <div class="row" style="<?= $paddingInfo ?>">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-3 ">
                                                <b>Name</b>
                                            </div>
                                            <div class="col-9 ">
                                                <b> : {{(@$attendance[0]->name)}}</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <b>Position</b>
                                            </div>
                                            <div class="col-9">
                                                <b> : {{(@$attendance[0]->position_routing)}}</b>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-3">
                                                <b>Location</b>
                                            </div>
                                            <div class="col-9">
                                                <b> : {{(@$attendance[0]->location)}}</b>
                                            </div>
                                        </div>
                                        <!-- div class="row">
                                            <div class="col-3">
                                                <b>Office Hour</b>
                                            </div>
                                            <div class="col-9">
                                                <b> : {{(@$attendance[0]->office_hour)}} {{(@$attendance[0]->schedule_employee_timezone)}}</b>
                                            </div>
                                        </div -->
                                    </div>
                                </div>

                                <?php if($absLastMonth['date'] != '' || $absCurrentMonth['date'] != ''){
                                    $thisTable = '<table class="table table-bordered table-dashed border-alert">';
                                        if($absCurrentMonth['date'] != ''){
                                            $thisTable .= '<tr><td>
                                                <div class="row">
                                                    <div class="col-12 alert_title" align="center">
                                                        Status ABS (tidak absen) di bulan '.$absCurrentMonth['month'].'
                                                    </div>
                                                    <div class="col-12 alert_content text-bold">
                                                        Tanggal : '.$absCurrentMonth['date'].'
                                                    </div>
                                                </div>
                                                </td></tr>';
                                        }
                                        if($absLastMonth['date'] != ''){
                                            $thisTable .= '<tr><td>
                                                <div class="row">
                                                    <div class="col-12 alert_title" align="center">
                                                        Status ABS (tidak absen) di bulan '.$absLastMonth['month'].'
                                                    </div>
                                                    <div class="col-12 alert_content text-bold">
                                                        Tanggal : '.$absLastMonth['date'].'
                                                    </div>
                                                </div>
                                                </td></tr>';
                                        }
                                    $thisTable .= '</table>';
                                    echo $thisTable;
                                } ?>

                                <table class="table table-bordered table-dashed">
                                    <tr>
                                        <th>
                                            <div class="row">
                                                <div class="col-12" align="center">
                                                    <span><b>Today</b></span></br>
													@if(@$attendance[0]->current_dates != null)
														{{ date('l, d F Y', strtotime(@$attendance[0]->current_dates)) }}
													@endif
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-2">
                                                    <div class="img-box box-img">
                                                        @if(!is_null(@$attendance[0]->image_attachment_in))
                                                            <center><img style="width:100%;" src="{{url('project/storage/app/public/images/'.@$attendance[0]->image_attachment_in)}}" alt=""></center>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <center><span class="attendanceLabel">Start Time</span>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span class="attendanceTime text-success">
                                                            @if(@$attendance[0]->actual_time_in != null)
                                                                {{ date('H:i', strtotime(@$attendance[0]->actual_time_in)) }}
                                                            @elseif(@$attendance[0]->actual_time_in == null)
                                                                -- : --
                                                            @endif
                                                            </span>
                                                        </div>
                                                    </div>
													<span class="attendanceLabel">{{(@$attendance[0]->current_employee_timezone)}}</span></center>
                                                </div>
                                                <div class="col-3">
                                                    <div class="img-box box-img" >
                                                        @if(!is_null(@$attendance[0]->image_attachment_out))
                                                            <center><img style="width:100%;" src="{{url('project/storage/app/public/images/'.@$attendance[0]->image_attachment_out)}}" alt=""></center>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <center><span class="attendanceLabel">End Time</span>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span class="attendanceTime text-danger">
                                                            @if(@$attendance[0]->actual_time_out != null)
                                                                {{ date('H:i', strtotime(@$attendance[0]->actual_time_out)) }}
                                                            @elseif(@$attendance[0]->actual_time_out == null)
                                                                -- : --
                                                            @endif
                                                            </span>
                                                        </div>
                                                    </div>
													<span class="attendanceLabel">{{(@$attendance[0]->current_employee_timezone)}}</span></center>
                                                </div>
                                            </div>

                                            @if(count(@$attendance) > 0)
                                            <div class="row">
                                                <div class="col-12">
                                                    &nbsp;&nbsp;
                                                </div>
                                                <div class="col-12">
                                                    <button id="btnRecord" class="btn btn-block btn-primary">
                                                        Record Time
                                                    </button>
                                                </div>
                                            </div>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-lg-6">
                        <div class="card">
                            <br/>
                            <div class="col-md-12">
                                <table class="table table-bordered table-dashed">
                                    <tr>
                                        <th>
                                            <div class="row">
                                                <div class="col-12" align="center">
                                                    <span><b>History</b></span>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                    
                                    @if(count(@$attendance) > 0)
                                    @for ($i = 1; $i < count(@$attendance); $i++)
                                    <tr>
                                        <td>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <center class="attendanceDay">@if(@$attendance[$i]->schedule_time_in != null)
                                                        {{ date('l, d F Y', strtotime(@$attendance[$i]->schedule_time_in)) }}
                                                        @else
                                                        -
                                                        @endif</center>
                                                </div>
                                                <div class="col-2">
                                                    <div class="img-box box-img" >
                                                        @if(!is_null(@$attendance[$i]->image_attachment_in))
                                                            <center><img style="width:100%;" src="{{url('project/storage/app/public/images/'.@$attendance[$i]->image_attachment_in)}}" alt=""></center>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <center><span class="attendanceLabel">Start Time</span>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span class="attendanceTime text-success">
                                                                @if(@$attendance[$i]->actual_time_in != null)
                                                                {{ date('H:i', strtotime(@$attendance[$i]->actual_time_in)) }}
                                                                @else
                                                                -- : --
                                                                @endif
                                                            </span>
                                                        </div>
                                                    </div>
													<span class="attendanceLabel">{{(@$attendance[$i]->current_employee_timezone)}}</span></center>
                                                </div>
                                                <div class="col-3">
                                                    <div class="img-box box-img">
                                                        @if(!is_null(@$attendance[$i]->image_attachment_out))
                                                            <center><img style="width:100%;" src="{{url('project/storage/app/public/images/'.@$attendance[$i]->image_attachment_out)}}" alt=""></center>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-4">
                                                    <center><span class="attendanceLabel">End Time</span>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <span class="attendanceTime text-danger">
                                                                @if(@$attendance[$i]->actual_time_out != null)
                                                                {{ date('H:i', strtotime(@$attendance[$i]->actual_time_out)) }}
                                                                @else
                                                                -- : --
                                                                @endif</span>
                                                        </div>
                                                    </div>
													<span class="attendanceLabel">{{(@$attendance[$i]->current_employee_timezone)}}</span></center>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endfor
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')

    @if(!is_null(@$attendance))
    @include('time_attendance.attendance.attendance', ['employee' => $employee])
    @endif

@endsection
