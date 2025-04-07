@extends('adminlte::page')

@section('title', 'Generate Attendance')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Generate Attendance</h5>
                <div class="card-tools">
                    <!-- <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modalshiftgroup"><i class="fas fa-plus"></i> Create Shift Group</button> -->
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Employee Name :</label>
                    <div class="col-md-4">
                        <select id="employeename" class="form-control form-control-sm select2" style="height: 100%;" ></select>
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
                        <button type="button" id="generateattendance" class="new btn btn-lg btn-success" style="padding:0.5rem 2rem;"><i class="fas fa-refresh"></i> Generate Attendance</button>
                        <button type="button" id="loadinggenerateattendance" class="btn btn-lg btn-success" style="padding:0.5rem 2rem;display:none;"><i class="fas fa-spinner fa-pulse"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

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

    $(document).ready(function(){
        daterange();

        const get_employee = new Promise(function(res) {
            $.ajax({
                url: "{{ route('getemployeename') }}",
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                method: "POST",
                dataType: 'json',
                data: {search: null},
                success:function(msg)
                {
                    let option = [];
                    $.each(msg.data, function (i, item) {
                        option.push({id: item.id_employee, text:item.name+' ('+item.nik_employee+') ('+item.status+')'});
                    });
                    res(option)
                }
            })
        });

        get_employee.then(function(value) {
            $('#employeename').prepend('<option selected></option>').select2({
                placeholder: "Select Employee",
                data: value,
                allowClear: true,
            });
        });

    });

    $(document).on("click", "#generateattendance", function () {
        var startdate = $("#startdate").val();
        var enddate = $("#enddate").val();

        if($("#employeename").val() == ''){
            // swal({
            //     icon: 'error',
            //     title: 'Please Select Employee',
            //     text: ' ',
            //     buttons: false,
            //     timer:1500,
            // });
            // return false;
        }

        if(startdate=="" || enddate==""){
            swal({
                icon: 'error',
                title: 'Please fill Start date and End date',
                text: ' ',
                buttons: false,
                timer:1500,
            });
            return false;
        }
        
        $.ajax({
            url :"{{ route('generateattendance') }}",  
            method:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data: {
                employeename: $("#employeename").val() == '' ? null : $("#employeename").children("option:selected").text(),
                id_employee: $("#employeename").val() == '' ? null : $("#employeename").children("option:selected").val(),
                startdate: startdate,
                enddate: enddate
            },
            beforeSend: function () {
                $("#generateattendance").hide();
                $("#loadinggenerateattendance").show();
            },
            success:function(msg){ 
                if (msg.status == 'true') {
                    swal({
                        icon: 'success',
                        title: 'Success generate attendance',
                        text: ' ',
                        buttons: false,
                        timer:2000,
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! '+msg.message,
                        buttons: false,
                        timer:1500,
                    });
                }
                $("#generateattendance").show();
                $("#loadinggenerateattendance").hide();
            },
            error:function(msg){return;
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong! '+msg.message,
                    buttons: false,
                    timer:1500,
                });
                $("#generateattendance").show();
                $("#loadinggenerateattendance").hide();
            }
        })
    });
</script>
@endsection
