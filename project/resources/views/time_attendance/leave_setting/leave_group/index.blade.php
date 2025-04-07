@extends('adminlte::page')
@section('title', 'Master Leave Group')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Leave Group</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Leave Group</button>
                </div>
            </div>

            <div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="leave_group_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Leave Group Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_leave_group"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="leave_groupForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Leave Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">
                            
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Group</label>
                                <div class="col-sm-8">
									<input name="id_leave_header" id="id_leave_header" type="hidden">		
                                    <input type="text" name="leave_group_name" id="leave_group_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="leave_group_nameError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
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
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 
						</div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_leave_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Leave Detail<span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_leave_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_leave_detail"><span class="fas fa-plus"></span> Add Leave Detail</button>
                                        </div>
                                        <div class="col-md-12" style="overflow-y: scroll">
                                            <table id="table_leave_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Leave Type</th>
                                                        <th style="white-space:nowrap;">Leave Quota</th>
                                                        <th style="white-space:nowrap;">Status</th>                                                       
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_leave_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_leave_detailError">
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
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			     <div style="display:none;">
                <table id="sample_table_leave">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="leave[0][id_leave_detail]" id="leave_0_id_leave_detail" type="hidden" class="form-control form-control-sm id_leave_detail_input">
                                <select name="leave[0][id_leave_type]" id="leave_0_id_leave_type" class="form-control form-control-sm select2 id_leave_type_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_leave_type_input_error" role="alert" id="leave_0_id_leave_typeError">
                                    <strong></strong>
                                </span>
                        </td>
						 <td>									
                                <input type="text" name="leave[0][leave_quota]" id="leave_0_leave_quota" class="form-control form-control-sm leave_quota_input">
                                <span class="invalid-feedback leave_quota_input_error" role="alert" id="leave_0_leave_quotaError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>
								 <select name="leave[0][status]" id="leave_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="leave_0_statusError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                    <center>
                        <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
            </div>
		</div>
	</div>
</div>
<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
<script type="text/javascript">
let global_id_leave_header = "";
let global_id_leave_detail = 0;
let global_id_leave_type = [];

$(function () {	   
    $(document).on('click', '.new', function () {
        global_id_leave_header = "";
        $("#leave_groupForm")[0].reset();
        $("#table_leave_body").html("");
        $("#leave_groupForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Leave Group");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#leave_groupForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_leave_group').modal('show');
    });
    
    $('#leave_groupForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        $(".invalid-feedback").children("strong").text("");
        $("#leave_groupForm input").removeClass("is-invalid");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: {
                    Accept: "application/json",
                },
                url: global_id_leave_header == '' ? "{{ route('leave_group.save') }}" : "{{ route('leave_group.update') }}",
                data: formData,
                success: function (response) {
                    if (response.status == 'true') {
                            $('#modal_form_leave_group').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        $('#leave_group_table').DataTable().ajax.reload();
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                },
                
                error: function (response) {
                    if (response.status === 422) {
                        let errors = response.responseJSON.errors;
                        Object.keys(errors).forEach(function (key) {
                            var key_temp = key.replaceAll(".", "_");
                            $("#" + key_temp).addClass("is-invalid");
                            $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
                                var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
                            if (tab_id != undefined) {
                                $("#tab_leave_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
                            }
                        });
                    }
                    else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                }
            });
        
    });


    $(document).on('click', '#new_leave_detail', function () {
        var content = jQuery('#sample_table_leave tr'),
                size = global_id_leave_detail++,
                element = null,
                element = content.clone();
        element.attr('id','rec-'+size);
        element.find('.delete-record').attr('data-id', size);
        element.find('.id_leave_detail_input').attr('id', 'leave_' + size + '_id_leave_detail');
        element.find('.id_leave_detail_input').attr('name', 'leave[' + size + '][id_leave_detail]');
        
        element.find('.id_leave_type_input').attr('id', 'leave_' + size + '_id_leave_type');
        element.find('.id_leave_type_input').attr('name', 'leave[' + size + '][id_leave_type]');
        element.find('.id_leave_type_input_error').attr('id', 'leave_' + size + '_id_leave_typeError');
        element.find('.id_leave_type_input').select2({
            placeholder: "Select Leave Type",
            allowClear: true,
            data: global_id_leave_type
        });
        element.find('.id_leave_type_input').val('').trigger('change');

        element.find('.leave_quota_input').attr('id', 'leave_' + size + '_leave_quota');
        element.find('.leave_quota_input').attr('name', 'leave[' + size + '][leave_quota]');
        element.find('.leave_quota_input_error').attr('id', 'leave_' + size + '_leave_quotaError');

        element.find('.status_input').attr('id', 'leave_' + size + '_status');
        element.find('.status_input').attr('name', 'leave[' + size + '][status]');
        element.find('.status_input_error').attr('id', 'leave_' + size + '_statusError');
        element.find('.status_input').select2();
        
        element.appendTo('#table_leave_body');
            $('#table_leave_body tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
    });

    $(document).on('click', '.delete-record', function () {
        var id = jQuery(this).attr('data-id');
        var targetDiv = jQuery(this).attr('targetDiv');
        jQuery('#rec-' + id).remove();
        $('#table_leave_body tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
        return true;
    });

    $(document).on('click', '.edit', function () {
        let id_leave_header = $(this).attr('id');
        global_id_leave_header = id_leave_header;
        $("#leave_groupForm")[0].reset();
        $("#table_leave_body").html("");
        $("#leave_groupForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Leave Group");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#leave_groupForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "<?= url('time_attendance/leave_setting/leave_group/get_leave_edit') ?>",
            method: "GET",
            data: {id_leave_header: id_leave_header},
            success: function (response) {
                global_id_leave_detail = 0;
                    $.each(response.leave, function (i, item) {
                    $('#new_leave_detail').trigger('click');
                });
                
                $('#id_leave_header').val(response.id_leave_header).trigger('change');
                $('#leave_group_name').val(response.leave_group_name).trigger('change');
                $('#description').val(response.description).trigger('change');
                $('#id_company').val(response.id_company).trigger('change');
                $('#select2status').val(response.status_header).trigger('change');

                setTimeout(function () {
                    $('#table_leave_body tr').each(function (index) {
                        $(this).find('span.sn').html(index + 1);
                        $(this).find('.id_leave_detail_input').val(response.leave[index].id_leave_detail);
                        $(this).find('.id_leave_type_input').val(response.leave[index].id_leave_type).trigger('change');
                        $(this).find('.leave_quota_input').val(response.leave[index].leave_quota);
                        $(this).find('.status_input').val(response.leave[index].status).trigger('change');
                        
                    });                       
                }, 500);
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

        $('#modal_form_leave_group').modal('show');
    });

});


$(document).ready(function(){
	
    $('#leave_group_table').DataTable({
        processing: true,
    //    serverSide: true,
        scrollY: true,
        ajax: {
            url: "<?= url('time_attendance/leave_setting/leave_group/get_data') ?>",
			 error: function (jqXHR, textStatus, errorThrown) {
					$('#leave_group_table').DataTable().ajax.reload();
				}
        },
        columns: [
            {
                data: null,
                defaultContent: '',
                orderable: false
            },
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'leave_group_name', name: 'leave_group_name'},
            {data: 'description', name: 'description'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
                    
                if(access_create == 0){
                    $('.new').css('display', 'none');
                }	
                if(access_edit == 0){
                    $('.edit').css('display', 'none');
                }		
                if(access_delete == 0){
                    $('.delete').css('display', 'none');
                }
                if(access_print == 0){
                    $('.print').css('display', 'none');
                }		
                    return data;
                }
            },
        ]
    });
	$('#advanced').click(function(){
			$('.cf').select2({width:'100%'});
			if($("#cf").css('display') == 'none'){
				$("#cf").show("slow");
			}
			else {
				$("#cf").hide("slow");
			}		
		});

	refresh_data();
 
    $('#update_form').on('submit', function(event){
        event.preventDefault();
        var action_url = '';

        if($('#action_edit').val() == 'Edit')
        {
        action_url = "{{ route('leave_group.update') }}";
        }
        $.ajax({
            enctype: 'multipart/form-data',
            processData: false,  // Important!
            contentType: false,
            cache: false,
            url: action_url,
            method:"POST",
            data:new FormData(this),
            //   data:$(this).serialize(),
            dataType:"json",
            success:function(data)
            {
                if(data.success)
                {
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: data.success
                    });
                    $('#leave_group_table').DataTable().ajax.reload();

                }
                $('#formModal').modal('hide');
            }
        });
    });

    $(document).on('click', '.delete', function (event) {
        id_leave_header = $(this).attr('id');
        event.preventDefault();
        swal({
            title: 'Are you sure?',
            text: 'This record and it`s details will be permanantly deleted!',
            icon: 'warning',
        buttons: true,
            dangerMode: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function(value) {
            if (value) {
                $.ajax({
                url:"leave_group/destroy/"+id_leave_header,
                success:function(data)
                {
                    setTimeout(function(){
                    $('#confirmModal').modal('hide');
                    $('#leave_group_table').DataTable().ajax.reload();				 
                    swal({
                        title: "Data Deleted!",
                        icon: "success",
                        buttons: {confirm : {className:'btn-success'},},
                        }).then(ok => {
                    });
                    }, 50);
                }
                })
            }
        });
    });
 
});

function refresh_data() {
    /**************** Load Menu dropdown **************************/
    $('#select2status').select2({width:'100%'});	
    get_company();
    get_leave_type();
    $('#leave_group_table').DataTable().ajax.reload();
}
function get_company(){
	 $.getJSON('<?= url('time_attendance/leave_setting/leave_group/get_company') ?>', function (data) {
        $('#company').select2({
            data: data,
            disabled: true
        });
        $('#company_edit').select2({
            data: data,
            disabled: true
        });
    }).fail(function (data) { // Call failed
            get_company();
    });      
}	
function get_leave_type(){
	$.getJSON('<?= url('time_attendance/leave_setting/leave_group/get_leave_type') ?>', function (data) {
        global_id_leave_type = data;
    }).fail(function (data) { // Call failed
            get_leave_type();
    });
}
</script>
@endsection