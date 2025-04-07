@extends('adminlte::page')

@section('title', 'Mass Leave Request')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Mass Leave Request</h5>
                <div class="card-tools">
                    <!-- <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modalshiftgroup"><i class="fas fa-plus"></i> Create Shift Group</button> -->
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row" >
                    <label class="col-md-2 col-form-label">Company :</label>
                    <div class="col-md-3">
                        <select id="company" class="form-control form-control-sm select2 " multiple="multiple" style="width: 100%;">
                        </select>
                        <span class="invalid-feedback" role="alert" id="companyError">
                            <strong></strong>
                        </span>
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
                <div class="form-group row" >
                    <label class="col-md-2 col-form-label">Note :</label>
                    <div class="col-md-9">
                        <input name="note" id="note" class="form-control form-control-sm">
                    </div>
                </div>
                <br>
                <div class="form-group row">
                    <div class="col-md-12 text-right">
                        {{-- <button type="button" id="execute" class="btn btn-lg btn-success mx-2" onclick="executeMassLeave()"><i class="fas fa-play"></i> Execute Mass Leave</button> --}}
                        <button type="button" id="send" class="btn btn-lg btn-success" ><i class="fas fa-document"></i> Create Mass Leave</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <button type="button" class="btn btn-default advanced_mass_leave_request">Advanced Search</button><br><br>
                <table id="mass_leave_request" class="table table-striped table-bordered table-hover datatable"></table>
            </div>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h3 align="center" style="margin:0;" class="text-primary">Data will create as an employee request for each selected company</h3><br>
                <h4 align="center" style="margin:0;">Are you sure ?</h4>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal" style="margin-right:20px;">Cancel</button>
                <button type="button" id="submit" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function executeMassLeave(id_mass_leave) {
        swal({
            title: "Execute?",
            text: "Do you want to execute all mass leave request?",
            buttons: ['Cancel', 'OK']
        }).then((confirmed) => {
            if(confirmed) {
                $.ajax({
                    url: '{{ route('massleaverequest.execute') }}',
                    method: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        execute_all: true,
                        id_mass_leave
                    },
                    beforeSend: function() {
                        $('#loader').removeClass('hidden');
                    },
                    success: function () {
                        $('#loader').addClass('hidden');
                        swal({
                            icon: "success",
                            title: "Execution Success"
                        }).then(() => {
                            window.location.reload();
                        })
                    },
                    error: function () {
                        $('#loader').addClass('hidden');
                        swal({
                            icon: "error",
                            title: "Execution Failed"
                        })
                    }
                });
            }
        })
    }

    $(document).on('click', '.execute-button', function() {
        executeMassLeave($(this).attr('mass-leave-request-id'));
    });

    $(document).ready(function(){
        daterange();
        getDatatableMassLeaveRequest();

        let optCompany = [];
        $.each(<?= $allCompany ?>, function (i, val) {
            optCompany.push({id:val.id_company, text:val.company_name});
        }); 
        $('#company').select2({
            data: optCompany,
            placeholder: 'Select Company'
        });

        $('.datepicker').each(function(){
            $(this).datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
            });
        });

    });

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

    function getDatatableMassLeaveRequest() {
        let t = $('#mass_leave_request').DataTable({
            processing: true,
            serverSide: false,
            // responsive: true,
            destroy: true,
            ajax: {
                "url": "<?= url('time_attendance/leaves/mass_leave_request')?>",
                "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                "data": {},
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
                { data : 'name', title: 'Created By', 
                    render: function ( data, type, row ) { 
                        let name = row.name;
                        let nik = row.nik_employee;
                        let all = name+' ('+nik+')';
                        return all;
                    } 
                },
                { data : "shift_group", title: 'Shift Group' },
                { data : "start_date", title: 'Start' },
                { data : "end_date", title: 'End' },
                { data : "qty_days", title: 'Qty Days' },
                { data : "note", title: 'Note' },
                { data : "executed", title: 'Executed', 
                    render: function ( data, type, row ) {  
                        return is_executed(row.executed);
                    } 
                },
                {
                    data: "action", title: "Action"
                }
            ],
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('id_mass_leave_request_history', data['id_mass_leave_request_history']);
            },
            "fnInitComplete": function (oSettings) {
               // $('#employee_lock_wrapper .column-filter-widget:eq(0)').css('display','none').change();
            },

            order: [[ 1, "asc" ]],
            scrollY: true,
            scrollX: true,
            lengthMenu: [
                [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, -1],
                [7, 14, 21, 28, 30, 31, 50, 100, 200, 500, 1000, 'All']
            ],
        });
    }

    function is_executed(status) {
        let executed;
        if(status==false){
            executed = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
        } else {
            executed = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
        }
        return executed;
    }

    $(document).on("click", "#send", function () {
        let company     = $(`#company`).val();
        let start       = $("#startdate").val();
        let end         = $('#enddate').val();
        let note        = $('#note').val();
        
        if(note=='' || company=='' || start=='' || end==''){
            alert("Please fill all field");
        }
        else{
            $("#confirmModal").modal('show');
        }
    });

    $(document).on("click", "#submit", function () {
        let company     = $(`#company`).val();
        let start       = $("#startdate").val();
        let end         = $('#enddate').val();
        let note        = $('#note').val();

        $.ajax({
            url :"{{ url('time_attendance/leaves/mass_leave_request/submit') }}",  
            method:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data: {
                company: company,
                start: start,
                end: end,
                note: note
            },
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success:function(msg){ 
                if (msg.status == 'true') {
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: msg.message
                    }).then(ok => {
                        location.reload();
                    });
                } else {
                    swal({
                        icon: 'error',
                        dangerMode: true,
                        content: {
                            element: "div",
                            attributes: {
                                innerText: msg.message,
                                className: "swal-red",
                            },
                        },
                    });
                }
                $("#confirmModal").hide();
            },
            error:function(msg){
            },
            complete:function(msg){
                $('#loader').addClass('hidden');
                $("#confirmModal").modal('hide');
            }
        })
    });

    $(document).on("click", ".advanced_mass_leave_request", function () {
        $('.cf').select2({width:'100%'});
        if($(".mass_leave_request").css('display') == 'none'){
            $(".mass_leave_request").show("slow");
        }
        else {
            $(".mass_leave_request").hide("slow");
        }   
    });

    
</script>
@endsection
