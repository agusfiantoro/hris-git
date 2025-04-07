@extends('adminlte::page')

@section('title', 'Master Shift Daily')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Daily Shift</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modaldailyshift"><i class="fas fa-plus"></i> Create Daily Shift</button>
                </div>
            </div>
            <div class="card-body">
                <table id="shift_daily_table" class="table table-striped table-bordered table-hover nowrap datatable">
                    <thead>
                        <tr>
                            <!-- <th></th> -->
                            <th>No</th>
                            <th>Shift Code</th>
                            <th>Description</th>
                            <th>Day Type</th>
                            <th>Flexible</th>
                            <th>Working Time</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Start Break</th>
                            <th>End Break</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modaldailyshift" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Create Daily Shift</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Shift Code :</label>
                    <div class="col-sm-4">
                        <input type="text" id="shift_code" class="form-control" placeholder="Shift Code" value="">
                        <input type="hidden" id="id_shift" value="">
                        <!-- <textarea class="form-control" id="description" style="width:100%;" placeholder="Description...."></textarea> -->
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Status :</label>
                    <div class="col-sm-4">
                        <select id="status" class="form-control select2" style="width: 100%;">
                            <option selected value="A">Active</option>
                            <option value="I">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description:</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="description" style="width:100%;" placeholder="Description...."></textarea>
                    </div>

                    <label class="col-sm-2 col-form-label">Working Time :</label>
                    <div class="col-sm-4">
                        <input type="time" id="workingtime" class="form-control" placeholder="Working Time" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Day Type :</label>
                    <div class="col-sm-4">
                        <select id="daytype" class="form-control select2" style="width: 100%;">
                            <option selected value="WD">Week Day</option>
                            <option value="OD">Off Day</option>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Range Time :</label>
                    <div class="col-sm-2">
                        <input type="time" id="start_time" class="form-control" placeholder="Start Time" value="">
                    </div>
                    <div class="col-sm-2">
                        <input type="time" id="end_time" class="form-control" placeholder="End Time" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Flexible Type :</label>
                    <div class="col-sm-4"> 
                        <select id="flexibletype" class="form-control select2" style="width: 100%;">
                            <option selected value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Range Break :</label>
                    <div class="col-sm-2">
                        <input type="time" id="start_break" class="form-control" placeholder="Start Break" value="">
                    </div>
                    <div class="col-sm-2">
                        <input type="time" id="end_break" class="form-control" placeholder="End Break" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Overlap Days :</label>
                    <div class="col-sm-4"> 
                        <select id="overlap" class="form-control select2" style="width: 100%;">
                            <option value="1">Yes</option>
                            <option selected value="0">No</option>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Late in - Early Out :</label>
                    <div class="col-sm-2">
                        <input type="time" id="latein" class="form-control" placeholder="Late In" value="">
                    </div>
                    <div class="col-sm-2">
                        <input type="time" id="earlyout" class="form-control" placeholder="Early Out" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="simpandailyshift" value="add"><i class="fas fa-save"></i> Save</button>&nbsp;
                <button type="button" class="btn btn-sm btn-success" id="loadingsimpandailyshift" style="display:none;"><i class="fa fa-spinner fa-pulse"></i></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let list_status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    let list_day_type = [
        { id: 'WD', text: 'Week Day' },
        { id: 'OD', text: 'Off Day' },
    ];

    let list_flexible_type = [
        { id: '1', text: 'Yes' },
        { id: '0', text: 'No' },
    ];

    $('#status').select2({
        placeholder: "Select Status",
        data: list_status,
        allowClear: true,
    }); 
    $('#daytype').select2({
        placeholder: "Select Type",
        data: list_day_type,
        allowClear: true,
    }); 
    $('#flexibletype').select2({
        placeholder: "Select",
        data: list_flexible_type,
        allowClear: true,
    }); 
    $('#overlap').select2({
        placeholder: "Select",
        data: list_flexible_type,
        allowClear: true,
    }); 

    $('#shift_daily_table').DataTable({
        columnDefs: [ {
            orderable: true,
            className: '',
            targets: 0
        } ],
        order: [[ 0, "asc" ]],
        scrollY: true,
        scrollX: true,
        ajax: {
            url: "{{ route('daily_shift_getdata') }}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            type: "POST",   
        },
        columns: [
            // {   // Checkbox select column
            //     data: 'id_holiday',
            //     defaultContent: '',
            //     orderable: false
            // },
            { "data": 'DT_RowIndex'},
            { "data": "shift_code" },
            { "data": "description" },
            { "data": "day_type" },
            { "data": "flexible_shift" },
            { "data": "productive_work_time" },
            { "data": "start_time" },
            { "data": "end_time" },
            { "data": "start_break" },
            { "data": "end_break" },
            { "data": "status" },
            { "data": "action" }
        ]
    });
    
    $("#modaldailyshift").on("hidden.bs.modal", function () {
        var status='<option selected value="A">Active</option><option value="I">Inactive</option>';
        var flexible_shift='<option selected value="1">Yes</option><option value="0">No</option>';
        let overlap='<option value="1">Yes</option><option selected value="0">No</option>';
        var day_type='<option selected value="WD">Week Day</option><option value="OD">Off Day</option>';
        $("#status").html(status);
        $("#flexibletype").html(flexible_shift);
        $("#overlap").html(overlap);
        $("#daytype").html(day_type);
        $("#id_shift").val('');
        $("#shift_code").val('');
        $("#description").val('');
        $("#start_time").val('');
        $("#end_time").val('');
        $("#start_break").val('');
        $("#end_break").val('');
        $("#workingtime").val('');
        $("#latein").val('');
        $("#earlyout").val('');
    });
    
    $(document).on("click", "#simpandailyshift", function () {
        $("#simpandailyshift").hide();
        $("#loadingsimpandailyshift").show();
        
        if($("#simpandailyshift").val()=="edit")
        {
            var id_shift = $("#id_shift").val();
        }
        
        var shift_code = $("#shift_code").val();
        var description = $("#description").val();
        var status = $("#status").val();
        var daytype = $("#daytype").val();
        var start_time = $("#start_time").val();
        var end_time = $("#end_time").val();
        var flexibletype = $("#flexibletype").val();
        var overlap = $("#overlap").val();
        var start_break = $("#start_break").val();
        var end_break = $("#end_break").val();
        var workingtime = $("#workingtime").val();
        var latein = $("#latein").val();
        var earlyout = $("#earlyout").val();
        
        if(shift_code.trim()=="" || description.trim()=="" || status.trim()=="" || daytype.trim()=="" || start_time.trim()=="" || end_time.trim()=="" || flexibletype.trim()=="" || overlap.trim()=="" || workingtime.trim()=="" || latein.trim()=="" || earlyout.trim()=="")
        {
            alert("Please complete the blank form first");
            $("#simpandailyshift").show();
            $("#loadingsimpandailyshift").hide();
        }
        else
        {
            if($("#simpandailyshift").val()=="add")
            {
                $.ajax({
                    url :"{{ route('daily_shift_create') }}",  
                    method:"POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                    data: {
                        shift_code: btoa(shift_code),
                        description: btoa(description),
                        status: btoa(status),
                        daytype: btoa(daytype),
                        start_time: btoa(start_time),
                        end_time: btoa(end_time),
                        flexibletype: btoa(flexibletype),
                        overlap: btoa(overlap),
                        start_break: btoa(start_break),
                        end_break: btoa(end_break),
                        workingtime: btoa(workingtime),
                        latein: btoa(latein),
                        earlyout: btoa(earlyout)
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
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }

                        $('#shift_daily_table').DataTable().ajax.reload();
                        $("#modaldailyshift").modal('hide');
                        $("#simpandailyshift").show();
                        $("#loadingsimpandailyshift").hide();
                    },
                    error:function(msg){
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                        $("#simpandailyshift").show();
                        $("#loadingsimpandailyshift").hide();
                    }
                })
            }
            else if($("#simpandailyshift").val()=="edit")
            {
                $.ajax({
                    url :"{{ route('daily_shift_editsave') }}",  
                    method:"POST",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                    data: {
                        id_shift: btoa(id_shift),
                        shift_code: btoa(shift_code),
                        description: btoa(description),
                        status: btoa(status),
                        daytype: btoa(daytype),
                        start_time: btoa(start_time),
                        end_time: btoa(end_time),
                        flexibletype: btoa(flexibletype),
                        overlap: btoa(overlap),
                        start_break: btoa(start_break),
                        end_break: btoa(end_break),
                        workingtime: btoa(workingtime),
                        latein: btoa(latein),
                        earlyout: btoa(earlyout)
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
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }

                        $('#shift_daily_table').DataTable().ajax.reload();
                        $("#modaldailyshift").modal('hide');
                        $("#simpandailyshift").show();
                        $("#loadingsimpandailyshift").hide();
                    },
                    error:function(msg){
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                        $("#simpandailyshift").show();
                        $("#loadingsimpandailyshift").hide();
                    }
                })
            }
        }
    });
    
    $(document).on("click", ".edit", function () {
        var id_shift = $(this).attr('id').substring(5);
        
        $.ajax({
            url :"{{ route('daily_shift_edit') }}",  
            method:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data: {
                id_shift: btoa(id_shift)
            },
            success:function(msg){
                $.each(JSON.parse(msg), function( i, item ) {
                    $("#status").html('');
                    $("#flexibletype").html('');
                    $("#overlap").html('');
                    $("#daytype").html('');
                    
                    if(item['status']=="A")
                    {
                        var status='<option selected value="A">Active</option><option value="I">Inactive</option>';
                    }
                    else
                    {
                        var status='<option value="A">Active</option><option selected value="I">Inactive</option>';
                    }
                    
                    if(item['flexible_shift']=="1")
                    {
                        var flexible_shift='<option selected value="1">Yes</option><option value="0">No</option>';
                    }
                    else
                    {
                        var flexible_shift='<option value="1">Yes</option><option selected value="0">No</option>';
                    }

                    if(item['overlap']==true)
                    {
                        var overlap='<option selected value="1">Yes</option><option value="0">No</option>';
                    }
                    else
                    {
                        var overlap='<option value="1">Yes</option><option selected value="0">No</option>';
                    }
                    
                    if(item['day_type']=="WD")
                    {
                        var day_type='<option selected value="WD">Week Day</option><option value="OD">Off Day</option>';
                    }
                    else
                    {
                        var day_type='<option value="WD">Week Day</option><option selected value="OD">Off Day</option>';
                    }
                    
                    $("#status").html(status);
                    $("#flexibletype").html(flexible_shift);
                    $("#overlap").html(overlap);
                    $("#daytype").html(day_type);
                    
                    $("#id_shift").val(id_shift);
                    $("#shift_code").val(item['shift_code']);
                    $("#description").val(item['description']);
                    $("#start_time").val(item['start_time']);
                    $("#end_time").val(item['end_time']);
                    $("#start_break").val(item['start_break']);
                    $("#end_break").val(item['end_break']);
                    $("#workingtime").val(item['productive_work_time']);
                    $("#latein").val(item['late_in_max']);
                    $("#earlyout").val(item['early_out_max']);
                });
                
                $("#simpandailyshift").val('edit');
                $("#modaldailyshift").modal('show');
            },
            error:function(msg){
                alert('Error server !');
            }
        })
    });
    
    $(document).on("click", ".delete", function () {
        if (confirm('Are you sure you want to delete this record from database?')) {
            var id_shift = $(this).attr('id').substring(7);
            
            $.ajax({
                url :"{{ route('daily_shift_delete') }}",  
                method:"POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                data: {
                    id_shift: btoa(id_shift)
                },
                success:function(msg){
                    if (msg.status == 'true') {
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: msg.message
                        }).then(ok => {
                            // location.reload();
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                    $('#shift_daily_table').DataTable().ajax.reload();
                },
                error:function(msg){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [Unknown Error]'
                    });
                }
            })
        } 
    });
</script>
@endsection
