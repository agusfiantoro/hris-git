@extends('adminlte::page')

@section('title', 'Master Shift Group')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Shift Group</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success add" ><i class="fas fa-plus"></i> Create Shift Group</button>
                </div>
            </div>
            <div class="card-body">
                <table id="shift_group_table" class="table table-striped table-bordered table-hover nowrap datatable" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Shift Group Code</th>
                            <th>Description</th>
                            <th>Overtime Based On</th>
                            <th>Total Days</th>
                            <th>Automatic Absence</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modalshiftgroup" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title form_title">Create Shift Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Shift Code :</label>
                    <div class="col-sm-4">
                        <input type="text" id="shift_code" class="form-control" placeholder="Shift Code" value="">
                        <input type="hidden" id="id_shiftgroup" value="">
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Status :</label>
                    <div class="col-sm-4">
                        <select id="status" class="form-control" style="width:100%;">
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Description :</label>
                    <div class="col-sm-4">
                        <textarea class="form-control" id="description" style="width:100%;" placeholder="Description...."></textarea>
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Automatic Absence :</label>
                    <div class="col-sm-4">
                        <select id="automaticabsence" class="form-control" style="width:100%;">
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Overtime Based On :</label>
                    <div class="col-sm-4">
                        <select id="overtimebasedon" class="form-control" style="width:100%;">
                            <option selected value="R">Request</option>
                            <option value="A">Attendance</option>
                            <option value="R&A">Request & Attendance</option>
                        </select>
                    </div>
                    
                    <label class="col-sm-2 col-form-label">Total Days :</label>
                    <div class="col-sm-2">
                        <input type="text" id="totaldays" class="form-control" placeholder="Total Days" onkeypress="return onlyNumberKey(event)" value="">
                    </div>
                    <div class="col-sm-1">
                        <font class="form-control" style="border: 1px solid transparent;">Days</font>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Checkout Nextday :</label>
                    <div class="col-sm-4">
                        <select id="checkoutnextday" class="form-control" style="width:100%;">
                            <option value="1">Yes</option>
                            <option selected value="0">No</option>
                        </select>
                    </div>

                    <label class="col-sm-2 col-form-label">Lock Gps Location :</label>
                    <div class="col-sm-4">
                        <select id="lock_gps_location" class="form-control" style="width:100%;"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs" id="tab_leave_detail" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Shift Group Detail<span class="error-tab text-red"></span></a>
                            </li>
                        </ul>
                        <div class="tab-content" id="tab_shift_group_detail_content" style="font-size:12px">
                            <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                <br/>
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 10px">
                                        <button type="button" class="pull-right btn btn-xs btn-primary" id="new_shift_group_detail" value="1"><span class="fas fa-plus"></span> Add Shift Group Detail</button>
                                        <button type="button" class="pull-right btn btn-xs btn-primary" id="append_shift_group_detail" style="display:none;"><span class="fas fa-plus"></span> Add Shift Group Detail</button>
                                    </div>
                                    <div class="col-md-12" style="overflow-y: scroll">
                                        <table id="table_shift_group_detail" class="table table-striped table-bordered table-hover datatable">
                                            <thead>
                                                <tr>
                                                    <th style="white-space:nowrap;width:10%;">Days</th>
                                                    <th style="white-space:nowrap;">Shift</th>
                                                    <th style="white-space:nowrap;">Status</th>                                                       
                                                    <th style="white-space:nowrap;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_shift_group_body">
                                                
                                            </tbody>
                                        </table>
                                        <div class="col-sm-12">
                                            <span class="table-invalid-feedback text-red" role="alert" id="table_shift_group_detailError">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="simpanshiftgroup" value="add"><i class="fas fa-save"></i> Save</button>&nbsp;
                <button type="button" class="btn btn-sm btn-success" id="loadingsimpanshiftgroup" style="display:none;"><i class="fa fa-spinner fa-pulse"></i></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var global_id_shift_group_header = "";
    var global_id_shift_group_detail = 0;
    var global_id_shift_group_type = [];
    var datadailyshift;
    var counterdetail=0;
    
    let list_status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    let list_checkout_nextday = [
        { id: '1', text: 'Yes' },
        { id: '0', text: 'No' },
    ];

    let list_auto_absence = [
        { id: '1', text: 'Yes' },
        { id: '0', text: 'No' },
    ];

    let list_overtime_absence = [
        { id: 'R', text: 'Request' },
        { id: 'A', text: 'Attendance' },
        { id: 'R&A', text: 'Request & Attendance' },
    ];

    let list_status_lock = [
        { id: '0', text: 'No' },
        { id: '1', text: 'Yes' },
    ];

    let list_daily_shift = []; 
    $.each(<?= json_encode($datadailyshift) ?>, function (i, item) {
        let desc = item.description;
        list_daily_shift.push({id: item.id_shift, text:desc});
    });

    $('#status').select2({
        placeholder: "Select Status",
        data: list_status,
        allowClear: true,
    }); 
    $('#automaticabsence').select2({
        placeholder: "Select",
        data: list_auto_absence,
        allowClear: true,
    }); 
    $('#overtimebasedon').select2({
        placeholder: "Select",
        data: list_overtime_absence,
        allowClear: true,
    });
    $('#checkoutnextday').select2({
        placeholder: "Select",
        data: list_checkout_nextday,
        allowClear: true,
    });
    $('#lock_gps_location').select2({
        placeholder: "Select Lock Status",
        data: list_status_lock,
        allowClear: true,
    });

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

    function addDetail(day, next=null) {
        let table_length = $(`#table_shift_group_body tr`).length;
        if(next != null && table_length > 0){
            day = table_length + day;
        }
        let listDetail = `<tr id="shiftgroupdetail_${day}" day="${day}">           
                            <td>
                                <input type="number" name="detail_days" class="form-control detail_days" onkeypress="return onlyNumberKey(event)" value="${day}" disabled>
                            </td>
                            <td>                                                
                                <select class="form-control detail_shift" name="detail_shift" style="width:100%;">
                                </select>
                            </td>
                            <td>
                                <select class="form-control detail_status" name="detail_status" style="width:100%;">
                                </select>
                            </td>
                            <td>
                                <center><button type="button" class="delete-record btn btn-xs btn-danger deleteshiftgroupdetail" id="deleteshiftgroupdetail_${day}"><span class="far fa-trash-alt"></span></button></center>
                            </td>
                        </tr>`;
        $("#table_shift_group_body").append(listDetail);

        $(`#table_shift_group_body tr#shiftgroupdetail_${day} select.detail_shift`).select2({
            placeholder: "Select Shift",
            data: list_daily_shift,
            allowClear: true,
        });
        $(`#table_shift_group_body tr#shiftgroupdetail_${day} select.detail_status`).select2({
            placeholder: "Select Status",
            data: list_status,
            allowClear: true,
        });
    }

    $('#shift_group_table').DataTable({
        columnDefs: [ {
            orderable: true,
            className: '',
            targets: 0
        } ],
        order: [[ 0, "asc" ]],
        scrollY: true,
        scrollX: true,
        ajax: {
            url: "{{ route('shift_group_getdata') }}",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            type: "POST",    
        },
        columns: [
            { "data": 'DT_RowIndex'},
            { "data": "shiftgroup_code" },
            { "data": "description" },
            { "data": "overtime_based_on" },
            { "data": "total_days" },
            { "data": "automatic_absence" },
            { "data": "action" }
        ]
    });
    
    $("#modalshiftgroup").on("hidden.bs.modal", function () {
        $("#table_shift_group_body").html('');
        counterdetail=0;
        $("#new_shift_group_detail").show();
    });
    
    $(document).on('click', '#new_shift_group_detail', function () {
        $(".form_title").html('Create Shift Group');
        $("#append_shift_group_detail").hide();
        $("#new_shift_group_detail").show();

        var detail_shift_dropdown = "";
        var totaldays = $("#totaldays").val();
        
        if(totaldays.trim()=="")
        {
            alert("Total Days is still blank, please fill it first");
        }
        else if(counterdetail==totaldays)
        {
            alert("Maximum row of total days");
        }
        else
        {
            counterdetail = totaldays;
            $.each(<?= json_encode($datadailyshift) ?>, function(i, item) {
                detail_shift_dropdown+=`<option value="`+item['id_shift']+`">`+item['description']+`</option>`;
            });
            
            for (let i = 0; i < totaldays; i++) {
                var j=i+1
                addDetail(j);
            }
            
            var counter = parseInt($("#new_shift_group_detail").val())+parseInt(1);
            $("#new_shift_group_detail").val(counter);
            $("#new_shift_group_detail").hide();
        }
    });
    
    $(document).on('click', '#append_shift_group_detail', function () {
        addDetail(1, 'next');
    });


    $(document).on("change", "#total_days", function () {
        $("#table_shift_group_body").html('');
    });
    
    $(document).on("click", ".deleteshiftgroupdetail", function () {
        var myobj = document.getElementById("shiftgroupdetail_"+$(this).attr('id').substring(23));
        myobj.remove();
    });
    
    $(document).on("click", "#simpanshiftgroup", function () {
        $("#simpanshiftgroup").hide();
        $("#loadingsimpanshiftgroup").show();
        
        var detail_days=new Array;
        var detail_shift=new Array;
        var detail_status=new Array;
        
        if($("#simpanshiftgroup").val()=="edit")
        {
            var id_shiftgroup = $("#id_shiftgroup").val();
        }
        
        var shift_code = $("#shift_code").val();
        var status = $("#status").val();
        var description = $("#description").val();
        var automaticabsence = $("#automaticabsence").val();
        var overtimebasedon = $("#overtimebasedon").val();
        var totaldays = $("#totaldays").val();
        var checkoutnextday = $("#checkoutnextday").val();
        var lock_gps_location = $("#lock_gps_location").val();

        $("input[name=detail_days]").each(function(){
            detail_days.push($(this).val().trim());
        });
        
        $("select[name=detail_shift]").each(function(){
            detail_shift.push($(this).val().trim());
        });
        
        $("select[name=detail_status]").each(function(){
            detail_status.push($(this).val().trim());
        });
        
        var details="";
        $.each(detail_days, function( i, item ) {
            if(detail_days[i].trim()=="" || detail_shift[i].trim()=="" || detail_status[i].trim()=="")
            {
                details="0";
                return;
            }
            else
            {
                details="1";
                return;
            }
        });
        
        if(details=="0" || detail_days.length==0 || detail_shift.length==0 || detail_status.length==0)
        {
            alert("Please complete the detail form");
            $("#simpanshiftgroup").show();
            $("#loadingsimpanshiftgroup").hide();
        }
        else if(shift_code.trim()=="" || description.trim()=="" || totaldays.trim()=="")
        {
            alert("Please complete the blank form first");
            $("#simpanshiftgroup").show();
            $("#loadingsimpanshiftgroup").hide();
        }
        else
        {
            let url;
            let param = {
                shift_code: btoa(shift_code),
                status: btoa(status),
                description: btoa(description),
                automaticabsence: btoa(automaticabsence),
                overtimebasedon: btoa(overtimebasedon),
                totaldays: btoa(totaldays),
                checkoutnextday: btoa(checkoutnextday),
                detail_days: btoa(JSON.stringify(detail_days)),
                detail_shift: btoa(JSON.stringify(detail_shift)),
                detail_status: btoa(JSON.stringify(detail_status)),
                lock_gps_location: lock_gps_location
            };

            if($("#simpanshiftgroup").val()=="add"){
                url = "{{ route('shift_group_create') }}";
            } else {
                url = "{{ route('shift_group_editsave') }}";
                param['id_shiftgroup'] = btoa(id_shiftgroup);
            }

            $.ajax({
                url: url,  
                method:"POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                data: param,
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
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                    $('#shift_group_table').DataTable().ajax.reload();
                    $("#modalshiftgroup").modal('hide');
                    $("#simpanshiftgroup").show();
                    $("#loadingsimpanshiftgroup").hide();
                },
                error:function(msg){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [Unknown Error]'
                    });
                    $("#simpanshiftgroup").show();
                    $("#loadingsimpanshiftgroup").hide();
                },
                complete: function(){
                    $('#loader').addClass('hidden')
                },
            })
        }
    });
    
    $(document).on("click", ".add", function () {
        $("#modalshiftgroup").modal('show');
        $("#append_shift_group_detail").hide();
        $("#new_shift_group_detail").show();
        $("#shift_code").val('');
        $("#description").html('');
        $("#totaldays").val('');
    });

    $(document).on("click", ".edit", function () {
        var id_shiftgroup = $(this).attr('id').substring(5);
        $(".form_title").html('Edit Shift Group');
        $("#append_shift_group_detail").show();
        $("#new_shift_group_detail").hide();

        $.ajax({
            url :"{{ route('shift_group_edit') }}",  
            method:"POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            data: {
                id_shiftgroup: btoa(id_shiftgroup)
            },
            dataType: 'json',
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },  
            complete: function(){
                $('#loader').addClass('hidden');
            },
            success:function(msg){
                $("#table_shift_group_body").html('');
                // $("#new_shift_group_detail").hide();
                
                var detail_status_dropdown = "";
                var j = 1;

                let master = msg.master;
                let detail = msg.detail;
                let checkoutnextday = master.allow_checkout_nextdays == true ? '1' : '0';
                let automaticabsence = master.automatic_absence == true ? '1' : '0';
                let lock_gps_location = master.lock_gps_location == true ? '1' : '0';


                $("#id_shiftgroup").val(id_shiftgroup);
                $("#shift_code").val(master.shiftgroup_code);
                $("#totaldays").val(master.total_days);
                $("#automaticabsence").val(automaticabsence).trigger('change');
                $("#status").val(master.status).trigger('change');
                $("#overtimebasedon").val(master.overtime_based_on).trigger('change');
                $("#lock_gps_location").val(lock_gps_location).trigger('change');
                $("#checkoutnextday").val(checkoutnextday).trigger('change');
                $("#description").html('');
                $("#description").html(master.description);
                
                if(detail.length > 0){
                    let arr_day = [];
                    $.each(detail, function( i, item ) {
                        let day = item.day;
                        let id_shift = item.id_shift;
                        let status = item.status;
                        addDetail(day)

                        $(`#table_shift_group_body tr#shiftgroupdetail_${day} select.detail_shift`).val(id_shift).trigger('change');
                        $(`#table_shift_group_body tr#shiftgroupdetail_${day} select.detail_status`).val(status).trigger('change');
                    });
                }       

                $("#simpanshiftgroup").val('edit');
                $("#modalshiftgroup").modal('show');
            },
            error:function(msg){
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Mohon reload ulang'
                });
            }
        })
    });
    
    $(document).on("click", ".delete", function () {
        if (confirm('Are you sure you want to delete this record from database?')) {
            var id_shiftgroup = $(this).attr('id').substring(7);
            
            $.ajax({
                url :"{{ route('shift_group_delete') }}",  
                method:"POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                data: {
                    id_shiftgroup: btoa(id_shiftgroup)
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
                    $('#shift_group_table').DataTable().ajax.reload();
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
