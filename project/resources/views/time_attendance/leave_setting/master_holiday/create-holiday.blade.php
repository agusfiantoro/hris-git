@extends('layouts.holiday_navigation')

@section('title', 'Holidays')

@section('content')
    <h1>Holidays</h1>
    
        <div class="cardContent my-2 px-2 px-lg-5 py-2 py-lg-4">
            
            <div class="row mb-3 gx-0">
                <div class="col-6">
                    <h2>Create Master Holidays</h2>
                </div>

                <div class="col-6 col-md-2 offset-0 offset-md-4">
                    <button id="btnGenerate" class="btn btn-primary">
                        Generate
                    </button>
                </div>
            </div>
            <div class="row mb-3 gx-0">
                <label for="holiday_name" class="col-12 col-md-1 col-form-label">Holiday Name</label>
                <div class="col-12 col-md-5">
                    <input type="text" id="holiday_name" name="holiday_name" class="form-control" placeholder="Holiday Name">
                </div>

                <label for="status" class="col-12 col-md-1 offset-0 offset-md-1 col-form-label">Status</label>
                <div class="col-12 col-md-4">
                    <select id="status" name="status" class="form-select" aria-label="status">
                        <option selected value="A">Active</option>
                        <option value="I">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3 gx-0">
                <label for="start_date" class="col-12 col-md-1 col-form-label">Start Date</label>
                <div class="col-12 col-md-2">
                    <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Start Date">
                </div>

                <label for="end_date" class="col-12 col-md-1 offset-0 col-form-label ps-0 ps-md-1 ps-lg-2 ps-xl-4">End Date</label>
                <div class="col-12 col-md-2">
                    <input type="date" id="end_date" name="end_date" class="form-control" placeholder="End Date">
                </div>

                <label for="id_company" class="col-12 col-md-1 offset-0 offset-md-1 col-form-label">Company</label>
                <div class="col-12 col-md-4">
                    <select id="id_company" name="id_company" class="form-select" aria-label="id_company">
                        @for ($i = 0; $i < count($company); $i++)
                        <option @if($i == 0) selected @endif value="{{ $company[$i]->id_company }}">{{ $company[$i]->description }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="row mb-3 gx-0">
                <label for="holiday_type" class="col-12 col-md-1 col-form-label">Holiday Type</label>
                <div class="col-12 col-md-5">
                    <select id="holiday_type" name="holiday_type" class="form-select" aria-label="holiday_type">
                        <option selected value="P">Public</option>
                        <option value="C">Corporate</option>
                    </select>
                </div>

                <label for="recurring_every_year" class="col-12 col-md-1 offset-0 offset-md-1 col-form-label">Recurring Year</label>
                <div class="col-12 col-md-4">
                    <select id="recurring_every_year" name="recurring_every_year" class="form-select" aria-label="recurring_every_year">
                        <option selected value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>

            <div id="specific_container" class="row mb-3 gx-0" style="display:none;">

                <label for="specific_type" class="col-12 col-md-1 col-form-label">Specific Type</label>
                <div class="col-12 col-md-5">
                    <select id="specific_type" name="specific_type" class="form-select" aria-label="specific_type">
                        @for ($i = 0; $i < count($specific_type); $i++)
                        <option @if($i == 0) selected @endif value="{{ $specific_type[$i]->id_general_data }}">{{ $specific_type[$i]->description }}</option>
                        @endfor
                    </select>
                </div>

                <div class="row mb-3 gx-0">
                    <div class="col-6 col-md-2 offset-6 offset-md-10">
                        <button id="btnAddLine" class="btn btn-primary">
                            Add Line
                        </button>
                    </div>
                </div>

                <table class="table table-striped table-hover" id="table_detail">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th id="table_header">Regional</th>
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

    <div id="dialogAddLine" class="modal">
        <div class="modal-content p-3 p-md-4 p-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-8 offset-2 align-items-center">
                        <h2 id="dialog_title">Add Line</h2>
                    </div>
                    <div class="col-1 offset-1">
                        <i class="material-icons modal-close" id="btnClose" class="">close</i>
                    </div>
                </div>

                <table class="table table-striped table-hover table-add" id="regional_table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Regional</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($region); $i++)
                        <tr class="add_line_row" id="regional-{{ $region[$i]->id_region }}" name="{{ $region[$i]->description }}" status="{{ $region[$i]->status }}">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $region[$i]->description }}</td>
                        </tr>
                        @endfor
                    </tbody>
                
                </table>

                <table class="table table-striped table-hover table-add" id="branch_table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Branch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($branch); $i++)
                        <tr class="add_line_row" id="branch-{{ $branch[$i]->id_branch }}" name="{{ $branch[$i]->description }}" status="{{ $branch[$i]->status }}">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $branch[$i]->description }}</td>
                        </tr>
                        @endfor
                    </tbody>
                
                </table>

                <table class="table table-striped table-hover table-add" id="location_table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for ($i = 0; $i < count($location); $i++)
                        <tr class="add_line_row" id="location-{{ $location[$i]->id_location }}" name="{{ $location[$i]->description }}" status="{{ $location[$i]->status }}">
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $location[$i]->description }}</td>
                        </tr>
                        @endfor
                    </tbody>
                
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>

Date.prototype.toDateInputValue = (function() {
    var local = new Date(this);
    local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
    return local.toJSON().slice(0,10);
});

$(document).ready(function(){
    var prevSelectedType = 'Regional';
    var selectedType = null;
    var index = 1;
    var edit_id = -1;
    var dialog_type = 'add';
    var line_list = [];

    var specific_type_options = {!! str_replace("'", "\'", json_encode($specific_type)) !!};

    $.each(specific_type_options, function (index, value) {
        line_list.push({ name: value.description.toLowerCase(), data: [] });
    });
    
    $("[type='date']").val(new Date().toDateInputValue());

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#specific_type').change(function() {
        selectedType = $('#specific_type option:selected').text();

        if(prevSelectedType != selectedType) {
            $("#table_detail tbody").empty();

            index = 1;

            let current_list = line_list.find(x => x.name === selectedType.toLowerCase()).data;

            for(i = 0; i < current_list.length; i++) {
                addRow(current_list[i]);
            }
        
            $('#table_header').text(selectedType);

            prevSelectedType = selectedType;
        }
    });

    $('#holiday_type').change(function() {
        if($(this).val() == "P") {
            $('#specific_container').hide();
            selectedType = null;
        }
        else {
            $('#specific_container').show();
            selectedType = $('#specific_type option:selected').text();
        }
    });

    $('#btnAddLine').click(function() {
        dialog_type = 'add';
        $("#dialogAddLine").show();
        $('#dialog_title').text('Add Line');
        $('.table-add').hide();
        
        $('#table_header').text(selectedType);

        $('#' + selectedType.toLowerCase() + '_table').show();
    });

    $('#btnClose').click(function() {
        $("#dialogAddLine").hide();
    });

    $('.add_line_row').click(function() {
        let row_id = $(this).attr('id').split("-")[1];
        let current_list = line_list.find(x => x.name === selectedType.toLowerCase()).data;

        if(current_list.findIndex(x => x.id === row_id) == -1) {
            let line = {
                id: row_id,
                name: $(this).attr('name'),
                status: $(this).attr('status')
            }
            if(dialog_type == 'add') {
                current_list.push(line);
                addRow(line);
            }
            else {
                current_list[current_list.findIndex(x => x.id === edit_id)] = line;

                cell = $("#" + selectedType.toLowerCase() + "-detail-" + edit_id).find('.table-detail-name');
                if(cell.length > 0) {
                    $.each( cell, function( key, value ) {
	                    id = value.id;
	                    $('#' + id).html(line.name);
	                });
                }
                cell = $("#" + selectedType.toLowerCase() + "-detail-" + edit_id).find('.table-detail-status');
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

                $("#edit-" + edit_id).attr('id', 'edit-' + line.id);
                $("#delete-" + edit_id).attr('id', 'delete-' + line.id);
                $("#" + selectedType.toLowerCase() + "-detail-" + edit_id).attr('id', "#" + selectedType.toLowerCase() + "-detail-" + line.id);
                $("#num-" + edit_id).attr('id', 'num-' + line.id);
                $("#name-" + edit_id).attr('id', 'name-' + line.id);
                $("#status-" + edit_id).attr('id', 'status-' + line.id);
                $("#dialogAddLine").hide();
            }
        }
    });

    $(document).on("click",".btn-edit",function(event){
        dialog_type = 'edit';
        edit_id = $(this).attr('id').split("-")[1];
        $("#dialogAddLine").show();
        $('#dialog_title').text('Edit ' + $(this).attr('name'));
        $('.table-add').hide();
        
        $('#table_header').text(selectedType);

        $('#' + selectedType.toLowerCase() + '_table').show();
    });

    $(document).on("click",".btn-delete",function(event){
        deleteRow($(this));
    });

    function addRow(line) {
        let status = "Active";
        if(line.status != "A") {
            status = "Inactive";
        }

        $("#table_detail").find('tbody')
        .append($('<tr>')
            .attr('id', selectedType.toLowerCase() + '-detail-' + line.id)
            .append($('<td>')
                .attr('id', 'num-' + line.id)
                .attr('class', 'table-detail-number')
                .append(index)
            )
            .append($('<td>')
                .attr('id', 'name-' + line.id)
                .attr('class', 'table-detail-name')
                .append(line.name)
            )
            .append($('<td>')
                .attr('id', 'status-' + line.id)
                .attr('class', 'table-detail-status')
                .append(status)
            )
            .append($('<td>')
                .append($('<button>')
                    .attr('class', 'btn btn-green btn-sm btn-edit')
                    .attr('id', 'edit-' + line.id)
                    .attr('name', line.name)
                        .append($('<i>')
                        .attr('class', 'material-icons')
                        .text('edit')
                    )
                )
                .append($('<button>')
                    .attr('class', 'btn btn-red btn-sm btn-delete')
                    .attr('id', 'delete-' + line.id)
                        .append($('<i>')
                        .attr('class', 'material-icons')
                        .text('delete')
                    )
                )
            )
        );
        index++;
    }

    function deleteRow(element) {
        element.parent().parent().remove();

        let current_list = line_list.find(x => x.name === selectedType.toLowerCase()).data;
        let delete_id = element.attr('id').split("-")[1];

        current_list.splice(current_list.findIndex(x => x.id === delete_id), 1);

        cell = $("#table_detail").find('.table-detail-number');
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

    $('#btnGenerate').click(function() {
        var holiday_name = $("#holiday_name").val();
        var holiday_type = $("#holiday_type").val();
        var status = $("#status").val();
        var recurring_every_year = $("#recurring_every_year").val();
        var start_date = $("#start_date").val();
        var end_date = $("#end_date").val();
        var id_company = $("#id_company").val();
        var id_specific_type = null;
        var specific_type = null;
        var id_specific_list = null;
        var created_by = 1;

        let _token   = $('meta[name="csrf-token"]').attr('content');

        if(holiday_name == null || holiday_name == '') {
            alert("Holiday name can't be empty");
            return;
        }
        if(new Date(end_date) < new Date(start_date)) {
            alert("End date can't be smaller than start date");
            return;
        }

        if(holiday_type == "C") {
            id_specific_type = $("#specific_type").val();
            specific_type = selectedType.toLowerCase();
            id_specific_list = line_list.find(x => x.name === selectedType.toLowerCase()).data;

            if(id_specific_list.length == 0) {
                alert(selectedType + " can't be empty");
                return;
            }
        }

        console.log(holiday_name);
        console.log(holiday_type);
        console.log(status);
        console.log(recurring_every_year);
        console.log(start_date);
        console.log(end_date);
        console.log(id_company);
        console.log(id_specific_type);
        console.log(id_specific_list);
        console.log(selectedType);

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
    });

});
</script>
@endsection
