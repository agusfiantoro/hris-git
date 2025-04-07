@extends('adminlte::page')

@section('title', 'Master Holidays')

@section('content')
<style>
    .modal { overflow: auto !important; }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Holidays</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modal_create_holiday_group"><i class="fas fa-plus"></i> Create Holiday</button>
                </div>
            </div>

            <div class="card-body">
                <table id="master_holiday_table" class="table table-striped table-bordered table-hover datatable">
                </table>
            </div>
        </div>
    </div>
</div>

<div id="modal_create_holiday_group" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Create Holiday</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="javascript:void(0)" name="body_modal_create_holiday_group" id="body_modal_create_holiday_group" >
                <div class="form-group row">
                    <label for="holiday_name" class="col-sm-2 col-form-label">Holiday Name :</label>
                    <div class="col-sm-4">
                        <input type="text" id="holiday_name" name="holiday_name" class="form-control" placeholder="Holiday Name">
                    </div>
                    
                    <label for="status" class="col-sm-2 col-form-label">Status :</label>
                    <div class="col-sm-4">
                        <select id="holiday_status" name="holiday_status" class="form-control select2" style="width: 100%;" >
                            
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="start_date" class="col-sm-2 col-form-label">Range Date :</label>
                    <div class="col-sm-4">
                        <div class="input-group">
                            <input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
                            <input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
                            <input name="end_date" id="end_date" class="form-control form-control-sm" hidden>
                            <div class="input-group-append">
                                <span class="input-group-text far fa-calendar form-control-sm"></span>
                            </div>
                            <span class="invalid-feedback" role="alert" id="daterangeError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>

                    <label for="id_company" class="col-sm-2 col-form-label">Company :</label>
                    <div class="col-sm-4">
                        <input type="hidden" id="company" value="">
                        <select id="id_company" name="id_company" class="form-control select2" style="width: 100%;">
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="holiday_type" class="col-sm-2 col-form-label">Holiday Type :</label>
                    <div class="col-sm-4">
                        <select id="holiday_type" name="holiday_type" class="form-control select2" style="width: 100%;">
                            
                        </select>
                    </div>

                    <label for="recurring_every_year" class="col-sm-2 col-form-label">Recurring Year :</label>
                    <div class="col-sm-4">
                        <select id="recurring_every_year" name="recurring_every_year" class="form-control select2" style="width: 100%;" >
                            
                        </select>
                    </div>
                </div>
                
                <div class="form-group row" id="specific_container" style="display:none;">
                    <label for="specific_type" class="col-sm-2 col-form-label">Specific Type</label>
                    <input type="hidden" value="" id="holidays">
                    <input type="hidden" value="" id="line_list">
                    <input type="hidden" value="" id="regional">
                    <input type="hidden" value="" id="branch">
                    <input type="hidden" value="" id="location">
                    <input type="hidden" value="" id="line_holidays">
                    <input type="hidden" value="" id="specific_type_options">
                    <div class="col-sm-4">
                        <select id="specific_type" name="specific_type" class="form-control" style="width: 100%;" aria-label="specific_type">
                            
                        </select>
                    </div>
                    <div class="col-sm-4">
                    </div>
                    <div class="col-sm-12">
                        <div class="row mb-3 gx-0">
                            <div class="col-6 col-md-2 offset-6 offset-md-10">
                                <button href="javascript:void(0)" id="btn_add_line" class="btn btn-primary" data-toggle="modal" data-target="#dialog_add_line">
                                    Add Line
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="table_detail">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th id="table_add_line_header">Regional</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                            
                                </tbody>
                        
                            </table>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-success" id="btn_save"><i class="fas fa-save"></i> Save</button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>

<div id="dialog_confirm" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xs" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Delete Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-12 align-items-center">
                        <h2>Are you sure you want to delete this holiday?</h2>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-danger" id="btn_delete" value=""><i class="fa fa-trash"></i> Delete</button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancel</button>
            
            </div>
        </div>
    </div>
</div>

<div id="dialog_add_line" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Add Line</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-add-line" id="regional_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="checkall_regional"></th>
                                <th>No</th>
                                <th>Regional</th>
                            </tr>
                        </thead>
                        <tbody id="regional_table_data">
                            
                        </tbody>
                    
                    </table>

                    <table class="table table-striped table-hover table-add-line" id="branch_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="checkall_branch"></th>
                                <th>No</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody id="branch_table_data">
                            
                        </tbody>
                    
                    </table>

                    <table class="table table-striped table-hover table-add-line" id="location_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" class="checkall_location"></th>
                                <th>No</th>
                                <th>Location</th>
                            </tr>
                        </thead>
                        <tbody id="location_table_data">
                            
                        </tbody>
                    
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="save_addline" value="" onclick="save_addline()"><i class="fas fa-save"></i> Save</button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    var modal_create_holiday_group_originalModal = $("#modal_create_holiday_group").html();
    var form_type = 'add';
    var company;
    var specific_type;
    var selected_type = null;
    var line_list = new Array;
    var index = 1;
    var checkbox_addline=new Array;
    var holiday = null;
    var edit_id = null;
    var delete_id = null;
    let list_status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    let list_holiday = [
        { id: 'P', text: 'Public' },
        { id: 'C', text: 'Corporate' },
    ];

    let list_recurring = [
        { id: '1', text: 'Yes' },
        { id: '0', text: 'No' },
    ];

    $(document).ready(function(){
        $('#holiday_status').select2({
            placeholder: "Select Status",
            data: list_status,
            allowClear: true,
        }); 
        $('#holiday_type').select2({
            placeholder: "Select Type",
            data: list_holiday,
            allowClear: true,
        }); 
        $('#recurring_every_year').select2({
            placeholder: "Select",
            data: list_recurring,
            allowClear: true,
        }); 
        
        get_default_company()
        daterange();

        $('.datepicker').each(function(){
            $(this).datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
            });
        });

        var arr_company="";
        $.each(<?= json_encode(@$company) ?>, function(i, item) {
            arr_company+=`<option value="`+item['id_company']+`">`+item['description']+`</option>`;
        });
        // $("#id_company").html(arr_company);

        var arr_specific_type="";
        $.each(<?= json_encode(@$specific_type) ?>, function(i, item) {
            arr_specific_type+=`<option value="`+item['id_general_data']+`">`+item['description']+`</option>`;
            line_list.push({ name: item['description'].toLowerCase(), data: [] });
        });
        $("#specific_type").html(arr_specific_type).select2({allowClear: true});
        $("#specific_type_options").val(JSON.stringify(<?= json_encode(@$specific_type) ?>));
        $("#line_list").val(JSON.stringify(line_list));
        $("#regional").val(JSON.stringify(<?= json_encode(@$region) ?>));
        $("#branch").val(JSON.stringify(<?= json_encode(@$branch) ?>));
        $("#location").val(JSON.stringify(<?= json_encode(@$location) ?>));
        $("#company").val(JSON.stringify(<?= json_encode(@$company) ?>));
        $("#line_holidays").val(JSON.stringify(<?= json_encode(@$line_holidays) ?>));

        $('#master_holiday_table').DataTable({
            columnDefs: [ {
                orderable: true,
                className: '',
                targets: 0
            } ],
            scrollY: true,
            // order: [[ 0, "asc" ], [ 1, "asc" ]],
            ajax: {
                url: "<?= url('time_attendance/leave_setting/master_holiday/getdata') ?>",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                type: "post",
                dataSrc: function ( json ) {
                    // $("#holidays").val(JSON.stringify(json.data));
            
                    return json.data;
                }     
            },
            columns: [
                { "data": 'DT_RowIndex', title: ''},
                { "data": "holiday_name" , title: 'Holiday Name'},
                { "data": "start_date" , title: 'Start Date'},
                { "data": "end_date" , title: 'End Date'},
                { "data": "holiday_type" , title: 'Holiday Type'},
                { "data": "id_specific_type" , title: 'Specific type'},
                { "data": "recurring_every_year" , title: 'Recurring Every Year'},
                { "data": "company" , title: 'Company'},
                { "data": "status" , title: 'Status'},
                { "data": "actions" , title: 'Actions'}
            ]
        });
        
    });

    $('#holiday_type').change(function() {
        checkHolidayType();
    });
    
    function checkHolidayType() {
        if($('#holiday_type').val() == "P") {
            $('#specific_container').hide();
            selected_type = null;
        }
        else {
            $('#specific_container').show();
            selected_type = $('#specific_type option:selected').text();
        }
    }
    
    $('#specific_type').change(function() {
        selected_type = $('#specific_type option:selected').text();
        loadCurrentList();
    });
    
    function loadCurrentList(list=null) {
        $("#table_detail tbody").empty();
        $("#table_detail tbody").html('');

        index = 1;
        let current_list = [];

        if(list==null){
            current_list = getCurrentList();
        } else {
            current_list = list;
        }
        if(current_list != null) {
            for(i = 0; i < current_list.length; i++) {
                addRow(current_list[i]);
            }
            $('#table_add_line_header').text(selected_type);
        }
    }
    
    function getCurrentList() {
        if(selected_type == null) {
            return null;
        }
        else {
            // line_list=JSON.parse($("#line_list").val());
            return line_list.find(x => x.name === selected_type.toLowerCase()).data;
        }
    }
    
    function daterange(start_date='', end_date='') {
        let separator = '   to   ';
        let start = (start_date=='' || start_date==null) ? moment().format('YYYY-MM-DD') : start_date;
        let end = (end_date=='' || end_date==null) ? moment().format('YYYY-MM-DD') : end_date;

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
            $("#start_date").val(start.format('YYYY-MM-DD'));
            $("#end_date").val(end.format('YYYY-MM-DD'));
        });

        $("#start_date").val(start);
        $("#end_date").val(end);
    }

    function get_default_company(id_company='') {
        if(company == '' || company == null){
            $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_default_company') ?>', function (data) {  
                company = data;
                $('#id_company').select2({
                    data: data
                });
                if(id_company != ''){
                    $('#id_company').val(id_company).trigger('change');
                }
            }).fail(function (data) { // Call failed
                get_default_company();
            });
        } else {
            if(id_company != ''){
                $('#id_company').val(id_company).trigger('change');
            }
        }
    }
    $('#btn_add_line').click(function() {
        dialog_type = 'add';
        $("#save_addline").val(selected_type.toLowerCase());
        $('#dialog_title').text('Add Line');
        $('.table-add-line').hide();
        
        $('#table_add_line_header').text(selected_type);

        var arr_add_line = "";
        $.each(JSON.parse($("#"+selected_type.toLowerCase()).val()), function(i, item) {
            var counter_checkbox_addline=0;
            i=i+1;
            if(selected_type.toLowerCase()=="regional")
            {
                var selected_type_="region";
            }
            else
            {
                var selected_type_=selected_type;
            }
            $.each( checkbox_addline, function( check_i, check_item ) {
                if(item['id_'+selected_type_.toLowerCase()]==check_item)
                {
                    arr_add_line+=  `<tr class="line_row" id="`+selected_type.toLowerCase()+`_`+item['id_'+selected_type_.toLowerCase()]+`" name="`+item['description']+`" status="`+item['status']+`">
                                <td><input type="checkbox" class="check_`+selected_type.toLowerCase()+`" value="`+item['id_'+selected_type_.toLowerCase()]+`" checked></td>
                                <td>`+i+`</td>
                                <td>`+item['description']+` (${item['company_code']})</td>
                            </tr>`;
                    counter_checkbox_addline=1;
                }
            });
            if(counter_checkbox_addline=="0")
            {
                arr_add_line+=  `<tr class="line_row" id="`+selected_type.toLowerCase()+`_`+item['id_'+selected_type_.toLowerCase()]+`" name="`+item['description']+`" status="`+item['status']+`">
                                    <td><input type="checkbox" class="check_`+selected_type.toLowerCase()+`" value="`+item['id_'+selected_type_.toLowerCase()]+`"></td>
                                    <td>`+i+`</td>
                                    <td>`+item['description']+` (${item['company_code']})</td>
                                </tr>`;
            }
        });

        $("#"+selected_type.toLowerCase()+"_table_data").html(arr_add_line);
        $('#' + selected_type.toLowerCase() + '_table').show();
        
        $(document).on("click", ".checkall_"+selected_type.toLowerCase(), function () {
            if(this.checked)
            {
                $('.check_'+selected_type.toLowerCase()).not(this).prop('checked', true);
                $(".check_"+selected_type.toLowerCase()).each(function(){
                    checkbox_addline.push($(this).val());
                });
            }
            else
            {
                $('.check_'+selected_type.toLowerCase()).not(this).prop('checked', false);
                $(".check_"+selected_type.toLowerCase()).each(function(){
                    checkbox_addline=new Array;
                });
            }
        });
        
        $(document).on("click", ".check_"+selected_type.toLowerCase(), function () {
            if(this.checked)
            {
                checkbox_addline.push($(this).val());
            }
            else
            {
                for( var i = 0; i < checkbox_addline.length; i++){ 
                    if ( checkbox_addline[i] === $(this).val()) { 
                        checkbox_addline.splice(i, 1); 
                        i--; 
                    }
                }
            }
        });
        
        $("#dialog_add_line").modal('show');
    });
    
    function save_addline()
    {
        if(checkbox_addline.length=="0")
        {
            alert("Checkbox is empty");return;
        }
        
        $("#dialog_add_line").modal('hide');
        let current_list = getCurrentList();
        $.each( checkbox_addline, function( i, item ) {
            // let row_id = $(this).attr('id').split("_")[$(this).attr('id').split("_").length-1];
            let row_id = item;
    
            if(current_list.findIndex(x => x.id === row_id) == -1) {
                let line = {
                    id: row_id,
                    name: $("#"+selected_type.toLowerCase()+`_`+row_id).attr('name'),
                    status: $("#"+selected_type.toLowerCase()+`_`+row_id).attr('status')
                };
                if(dialog_type == 'add') {
                    current_list.push(line);
                    addRow(line);
                }
                else {
                    current_list[current_list.findIndex(x => x.id === edit_detail_id)] = line;
    
                    cell = $("#" + selected_type.toLowerCase() + "_detail_" + edit_detail_id).find('.table_detail_name');
                    if(cell.length > 0) {
                        $.each( cell, function( key, value ) {
                            id = value.id;
                            $('#' + id).html(line.name);
                        });
                    }
                    cell = $("#" + selected_type.toLowerCase() + "_detail_" + edit_detail_id).find('.table_detail_status');
                    if(cell.length > 0) {
                        $.each( cell, function( key, value ) {
                            id = value.id;
                            convert_status = 'Active';
                            if(line.status != 'A') {
                                convert_status = 'Inactive';
                            }
                            $('#' + id).html(convert_status);
                        });
                    }
    
                    $("#edit_detail_" + edit_detail_id).attr('id', 'edit_detail_' + line.id);
                    $("#delete_detail_" + edit_detail_id).attr('id', 'delete_detail_' + line.id);
                    $("#" + selected_type.toLowerCase() + "_detail_" + edit_detail_id).attr('id', selected_type.toLowerCase() + "_detail_" + line.id);
                    $("#num_detail_" + edit_detail_id).attr('id', 'num_detail_' + line.id);
                    $("#name_detail_" + edit_detail_id).attr('id', 'name_detail_' + line.id);
                    $("#status_detail_" + edit_detail_id).attr('id', 'status_detail_' + line.id);
                    $("#dialog_add_line").hide();
                }
            }
        });
    };

    $(document).on("click",".btn-delete-detail",function(event){
        var id = $(this).attr('id').substring(14);
        for( var i = 0; i < checkbox_addline.length; i++){ 
            if ( checkbox_addline[i] === id) { 
                checkbox_addline.splice(i, 1); 
                i--; 
            }
        }
        deleteRow($(this));
    });

    function addRow(line) {
        let status = "Active";
        if(line.status != "A") {
            status = "Inactive";
        }

        if(selected_type.toLowerCase()=="regional")
        {
            var selected_type_="region";
        }
        else
        {
            var selected_type_=selected_type;
        }

        $("#table_detail").find('tbody')
        .append($('<tr>')
            .attr('id', selected_type_.toLowerCase() + '_detail_' + line.id)
            .append($('<td>')
                .attr('id', 'num_detail_' + line.id)
                .attr('class', 'table_detail_number')
                .append(index)
            )
            .append($('<td>')
                .attr('id', 'name_detail_' + line.id)
                .attr('class', 'table_detail_name')
                .append(line.name)
            )
            .append($('<td>')
                .attr('id', 'status_detail_' + line.id)
                .attr('class', 'table_detail_status')
                .append(status)
            )
            .append($('<td>')
                .append($('<button>')
                    .attr('class', 'btn btn-sm btn-danger btn-delete-detail')
                    .attr('id', 'delete_detail_' + line.id)
                        .append($('<i>')
                        .attr('class', 'fa fa-trash')
                    )
                )
            )
        );
        index++;
    }

    function deleteRow(element) {
        element.parent().parent().remove();

        let current_list = getCurrentList();
        let delete_detail_id = element.attr('id').split("_")[element.attr('id').split("_").length-1];

        current_list.splice(current_list.findIndex(x => x.id === delete_detail_id), 1);

        cell = $("#table_detail").find('.table_detail_number');
        if(cell.length > 0) {
            $.each( cell, function( key, value ) {
	            id = value.id;
	            $('#' + id).html(key + 1);
                index = key + 2;
	        });
        }
	    else {
            index = 1;
        }
    }
    
    $(document).on("click","#btn_save",function(event){
        var holiday_name = $("#holiday_name").val();
        var holiday_type = $("#holiday_type").val();
        var status = $("#holiday_status").val();
        var recurring_every_year = $("#recurring_every_year").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var id_company = $("#id_company").val();
        var id_specific_type = null;
        var specific_type = null;
        var id_specific_list = null;
        var created_by = 1;
        var updated_by = 1;

        let _token   = $('meta[name="csrf-token"]').attr('content');

        if(holiday_name == null || holiday_name == '') {
            alert("Holiday name can't be empty");
            return;
        }
        if(end_date.trim()=="" || start_date.trim()=="") {
            alert("Start date and end date can't be empty");
            return;
        }

        if(holiday_type == "C") {
            id_specific_type = $("#specific_type").val();
            specific_type = selected_type.toLowerCase();
            id_specific_list = line_list.find(x => x.name === selected_type.toLowerCase()).data;

            if(id_specific_list.length == 0) {
                alert(selected_type + " can't be empty");
                return;
            }
        }

        if(form_type == "add") {
            $.ajax({
                type:'POST',
                url:"{{ route('holiday.store') }}",
                dataType: 'json',
                data:{
                    holiday_name:holiday_name,
                    holiday_type:holiday_type,
                    status:status,
                    recurring_every_year:recurring_every_year,
                    start_date:start_date,
                    end_date:end_date,
                    id_company:id_company,
                    id_specific_type:id_specific_type,
                    specific_type:specific_type,
                    id_specific_list:id_specific_list,
                    created_by:created_by,
                    _token: _token
                },
                success:function(result){
                   location.reload();
                },
                error:function(error){
                   console.log(error);
                },
            });
        }
        else if(form_type == "edit"){
            let url = "{{ route('holiday.update', ':id') }}";
            url = url.replace(':id', holiday.id_holiday);
            $.ajax({
                type:'POST',
                url:url,
                dataType: 'json',
                data:{
                    holiday_name:holiday_name,
                    holiday_type:holiday_type,
                    status:status,
                    recurring_every_year:recurring_every_year,
                    start_date:start_date,
                    end_date:end_date,
                    id_company:id_company,
                    id_specific_type:id_specific_type,
                    specific_type:specific_type,
                    id_specific_list:id_specific_list,
                    updated_by:updated_by,
                    _token: _token
                },
                success:function(response){
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: 'Holiday Saved succesfully'
                    }).then(function(){ 
                        location.reload();
                    });
                },
                error:function(error){
                   console.log(error);
                },
            });
        }
    });
    
    $(document).on("click",".btn-edit",function(event){
        form_type = 'edit';
        // edit_id = $(this).attr('id').split("_")[$(this).attr('id').split("_").length-1];
        edit_id = $(this).attr('id').substring(5);
        $.ajax({
            type:'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            url: "<?= url('time_attendance/leave_setting/master_holiday/getHolidayById') ?>",
            data:{id_holiday:edit_id},
            dataType: 'json',
            success:function(res){
                $("#holidays").val(JSON.stringify(res.data));
                holiday = JSON.parse($("#holidays").val()).find(x => x.id_holiday.toString() === edit_id);

                if(holiday) {
                    $("#holiday_name").val(holiday.holiday_name);
                    if(holiday.holiday_type=="P")
                    {
                        var edit_holiday_type=`<option selected value="P">Public</option><option value="C">Corporate</option>`;
                    }
                    else
                    {
                        var edit_holiday_type=`<option value="P">Public</option><option selected value="C">Corporate</option>`;
                    }
                    
                    if(holiday.status=="A")
                    {
                        var edit_holiday_status=`<option selected value="A">Active</option><option value="I">Inactive</option>`;
                    }
                    else
                    {
                        var edit_holiday_status=`<option value="A">Active</option><option selected value="I">Inactive</option>`;
                    }
                    
                    if(holiday.recurring_every_year==true)
                    {
                        var edit_holiday_recurring_every_year=`<option selected value="1">Yes</option><option value="0">No</option>`;
                    }
                    else
                    {
                        var edit_holiday_recurring_every_year=`<option value="1">Yes</option><option selected value="0">No</option>`;
                    }
                    
                    var edit_holiday_company="";
                    $.each(JSON.parse($("#company").val()), function(i, item) {
                        if(holiday.id_company==item['id_company'])
                        {
                            edit_holiday_company+=`<option selected value="`+item['id_company']+`">`+item['description']+`</option>`;
                        }
                        else
                        {
                            edit_holiday_company+=`<option value="`+item['id_company']+`">`+item['description']+`</option>`;
                        }
                    });
                    
                    daterange(holiday.start_date, holiday.end_date)
                    get_default_company(holiday.id_company)

                    $("#holiday_type").html(edit_holiday_type);
                    // $("#start_date").val(holiday.start_date);
                    // $("#end_date").val(holiday.end_date);
                    // $('#id_company').html(edit_holiday_company);
                    $("#recurring_every_year").html(edit_holiday_recurring_every_year);
                    $("#holiday_status").html(edit_holiday_status);
                    $('#form_title').text('Edit Holiday');
                    // console.log('myClone');return;
                    $("#modal_create_holiday_group").modal('show');
                    
                    checkHolidayType();
                    // loadCurrentList();
                    if(holiday.holiday_type == 'C') {
                        $("#specific_type").val(holiday.id_specific_type);
                        selected_type = JSON.parse($("#specific_type_options").val()).find(x => x.id_general_data.toString() == holiday.id_specific_type).description;
                        line_holiday = JSON.parse($("#line_holidays").val()).filter(x => x.id_holiday == holiday.id_holiday.toString());

                        let current_list = getCurrentList();
                        if(current_list.length > 0){
                            current_list = []; //reset row
                        }

                        line_holiday.forEach(function(item) {
                            let line = {
                                id: item['id_' + selected_type.toLowerCase()],
                                name: item[selected_type.toLowerCase()],
                                status: item[selected_type.toLowerCase() + '_status']
                            }
                            current_list.push(line);

                        });
                        loadCurrentList(current_list);
                    }
                }
            },
        });

    });
    
    $('#modal_create_holiday_group').on('hidden.bs.modal', function(e) {
        var checkbox_addline=new Array;
        $("#table_detail").find('tbody').html("");
        $(this).find('#body_modal_create_holiday_group')[0].reset();
    });

    $(document).on("click",".btn-delete",function(event){
        $("#btn_delete").val($(this).attr('id').substring(7));
        $('#dialog_confirm').modal('show');
    });
    
    $('#btn_delete').click(function() {
        let url = "{{ route('holiday.delete', ':id') }}";
        url = url.replace(':id', $(this).val());
        $.ajax({
            type:'DELETE',
            url: url,
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
            success:function(result){
               location.reload();
            },
            error:function(error){
               console.log(error);
            },
        });
    });
</script>
@endsection
