@extends('adminlte::page')
@section('title', 'Master Approval Hierarchy')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Approval Hierarchy</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Approval Hierarchy</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="approval_table" class="display nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Hierarchy Name</th>
                            <th>Hierarchy Type</th>
                            <th>Status</th>
                            <th data-priority="2" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_approval"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="approvalForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Approval Hierarchy</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">
                            							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Hierarchy Name</label>
                                <div class="col-sm-8">
									<input name="id_approval" id="id_approval" type="hidden">	
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Hierarchy Type</label>
                                <div class="col-sm-8">
                                    <select name="hierarchy_type" id="hierarchy_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Doc Type</label>
								<div class="col-sm-8">
                                    <select name="id_approval_doc_type" id="id_approval_doc_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_approval_doc_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Mode</label>
								<div class="col-sm-8">
                                    <select name="app_mode" id="app_mode" class="form-control form-control-sm select2" style="width: 100%;">
										<option value="AND_OR">AND/OR</option>
                                        <option value="Limitation">Limitation</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="app_modeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Note</label>
                                <div class="col-sm-8">
                                    <input type="text" name="note" id="note" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="noteError">
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
                                <label class="col-sm-4 col-form-label">Enable Limit</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" name="enable_limit" id="enable_limit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="enable_limitError">
                                        <strong></strong>
                                    </span>
                                </div>
								<label class="col-sm-1 col-form-label text-right">Limit</label>
                                <div class="col-sm-5 pull-right">
                                    <input type="text" name="limit" id="limit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="limitError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm select2">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grade User</label>
								<div class="col-sm-8">
                                    <select name="id_job_grade" id="id_job_grade" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
						</div>
					</div>
					<hr/>
                    <div class="row" id="app_detail">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_approval_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Approval Detail<span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_approval_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_approval_detail"><span class="fas fa-plus"></span> Add Approval Detail</button>
                                        </div>
                                        <div class="col-md-12" style="overflow-y: scroll">
                                            <table id="table_approval_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="width:50px;">No.</th>
                                                        <th style="width:50px;">Sequence</th>
                                                        <th style="min-width:300px;">Position Detail</th>
                                                        <th style="min-width:220px;">Employee</th>
                                                        <th style="white-space:nowrap;">Approval Mode</th>
                                                        <th style="white-space:nowrap;">Limit</th>
                                                        <th style="white-space:nowrap;">Note</th>
                                                        <th style="white-space:nowrap;">Status</th>                                                       
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_approval_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_approval_detailError">
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
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			     <div style="display:none;">
                <table id="sample_table_approval">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>   
						 <td>				
								<input name="approval[0][id_approval_detail]" id="approval_0_id_approval_detail" type="hidden" class="form-control form-control-sm id_approval_detail_input">								
                                <input type="text" name="approval[0][sequence]" id="approval_0_sequence" class="form-control form-control-sm sequence_input">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="approval_0_sequenceError">
                                    <strong></strong>
                                </span>					
                        </td>
                      
						<td>
                                <select name="approval[0][id_position_detail]" id="approval_0_id_position_detail" class="form-control form-control-sm select2 id_position_detail_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_position_detail_input_error" role="alert" id="approval_0_id_position_detailError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <input type="text" id="approval_0_employee_name" class="form-control form-control-sm employee_name_input" disabled>                              		
                        </td>
						<td>
                                <select name="approval[0][id_approval_mode]" id="approval_0_id_approval_mode" class="form-control form-control-sm select2 id_approval_mode_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_approval_mode_input_error" role="alert" id="approval_0_id_approval_modeError">
                                    <strong></strong>
                                </span>
                        </td>
						
						
						<td>																	
                                <input type="text" name="approval[0][limit]" id="approval_0_limit" class="form-control form-control-sm limit_input">
                                <span class="invalid-feedback limit_input_error" role="alert" id="approval_0_limitError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>																	
                                <input type="text" name="approval[0][note]" id="approval_0_note" class="form-control form-control-sm note_input">
                                <span class="invalid-feedback note_input_error" role="alert" id="approval_0_noteError">
                                    <strong></strong>
                                </span>					
                        </td>
						
						<td>
								 <select name="approval[0][status]" id="approval_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="approval_0_statusError">
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
let global_id_approval = "";
let global_id_approval_detail = 0;
//let global_id_employee = [];
let global_id_position_detail = [];
let global_id_approval_mode = [];

    $(function () {	

		$(document).on('click', '.new', function () {
            global_id_approval = "";
            $("#approvalForm")[0].reset();
            $("#table_approval_body").html("");
            $("#approvalForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Approval Hierarchy");
            $(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#approvalForm input").removeClass("is-invalid");
            $("#approvalForm select").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            $('#modal_form_approval').modal('show');
        });
		
		 $('#approvalForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#approvalForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_approval == '' ? "{{ route('approval_hierarchy.save') }}" : "{{ route('approval_hierarchy.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_approval').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
								   }
								);
	                        $('#approval_table').DataTable().ajax.reload();
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
									$("#tab_approval_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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


        $(document).on('click', '#new_approval_detail', function () {
            var content = jQuery('#sample_table_approval tr'),
                    size = global_id_approval_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_approval_detail_input').attr('id', 'approval_' + size + '_id_approval_detail');
            element.find('.id_approval_detail_input').attr('name', 'approval[' + size + '][id_approval_detail]');
			
			element.find('.sequence_input').attr('id', 'approval_' + size + '_sequence');
            element.find('.sequence_input').attr('name', 'approval[' + size + '][sequence]');
            element.find('.sequence_input_error').attr('id', 'approval_' + size + '_sequenceError');
			
			element.find('.id_position_detail_input').attr('id', 'approval_' + size + '_id_position_detail');
            element.find('.id_position_detail_input').attr('name', 'approval[' + size + '][id_position_detail]');
            element.find('.id_position_detail_input_error').attr('id', 'approval_' + size + '_id_position_detailError');
            element.find('.id_position_detail_input').select2({
                placeholder: "Select Position Detail",
                allowClear: true,
                data: global_id_position_detail
            }).on('change', function (e) {
				if(global_id_position_detail.length > 1){
					element.find('.employee_name_input').val($(this).select2('data')[0].employee_name).trigger('change');
				}
            }).trigger('change');

			element.find('.employee_name_input').attr('id', 'approval_' + size + '_employee_name');
        //    element.find('.id_position_detail_input').val('').trigger('change');
			
			element.find('.id_approval_mode_input').attr('id', 'approval_' + size + '_id_approval_mode');
            element.find('.id_approval_mode_input').attr('name', 'approval[' + size + '][id_approval_mode]');
            element.find('.id_approval_mode_input_error').attr('id', 'approval_' + size + '_id_approval_modeError');
            element.find('.id_approval_mode_input').select2({
                placeholder: "Select Approval Mode",
                allowClear: true,
                data: global_id_approval_mode
            });
            element.find('.id_approval_mode_input').val('').trigger('change');

			element.find('.limit_input').attr('id', 'approval_' + size + '_limit');
            element.find('.limit_input').attr('name', 'approval[' + size + '][limit]');
            element.find('.limit_input_error').attr('id', 'approval_' + size + '_limitError');
			
			element.find('.note_input').attr('id', 'approval_' + size + '_note');
            element.find('.note_input').attr('name', 'approval[' + size + '][note]');
            element.find('.note_input_error').attr('id', 'approval_' + size + '_noteError');
			
            element.find('.status_input').attr('id', 'approval_' + size + '_status');
            element.find('.status_input').attr('name', 'approval[' + size + '][status]');
            element.find('.status_input_error').attr('id', 'approval_' + size + '_statusError');
            element.find('.status_input').select2();
			
            element.appendTo('#table_approval_body');
			 $('#table_approval_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_approval_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

  $(document).on('click', '.edit', function () {
            let id_approval = $(this).attr('id');
            global_id_approval = id_approval;
            $("#approvalForm")[0].reset();
            $("#table_approval_body").html("");
            $("#approvalForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Approval Hierarchy");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#approvalForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "<?= url('employee/employee_setting/approval_hierarchy/get_approval_edit') ?>",
                method: "GET",
                data: {id_approval: id_approval},
                success: function (response) {
                    global_id_approval_detail = 0;
					 $.each(response.approval, function (i, item) {
                        $('#new_approval_detail').trigger('click');
                    });
                    
                    $('#id_approval').val(response.id_approval).trigger('change');
                    $('#description').val(response.description).trigger('change');
                    $('#hierarchy_type').val(response.hierarchy_type).trigger('change');
                    $('#app_mode').val(response.approval_mode).trigger('change');
                    $('#id_approval_doc_type').val(response.id_approval_doc_type).trigger('change');
					if(response.enable_limit == 1){
						$('#enable_limit').prop('checked', true);
					}
					else{
						$('#enable_limit').prop('checked', false);
					}
                //    $('#enable_limit').val(response.enable_limit).trigger('change');
                    $('#limit').val(response.limit_header).trigger('change');
                    $('#note').val(response.note_header).trigger('change');
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#select2status').val(response.status_header).trigger('change');
                    $('#id_job_grade').val(response.id_job_grade).trigger('change');

                    setTimeout(function () {
                        $('#table_approval_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_approval_detail_input').val(response.approval[index].id_approval_detail);
                            $(this).find('.sequence_input').val(response.approval[index].sequence);
                        //    $(this).find('.id_employee_input').val(response.approval[index].id_employee).trigger('change');
                            $(this).find('.id_position_detail_input').val(response.approval[index].id_position_detail).trigger('change');
                            $(this).find('.id_approval_mode_input').val(response.approval[index].id_approval_mode).trigger('change');
                            $(this).find('.limit_input').val(response.approval[index].limit);
                            $(this).find('.note_input').val(response.approval[index].note);
							$(this).find('.status_input').val(response.approval[index].status).trigger('change');
                            
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

            $('#modal_form_approval').modal('show');
        });

    });


$(document).ready(function(){
	
	$('#approval_table').DataTable({
            processing: true,
        //    serverSide: true,
        //    scrollY: true,
			responsive: true,
            ajax: {
				url: "{{ route('approval_hierarchy.index') }}",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#approval_table').DataTable().ajax.reload();
				}
            },
            columns: [
				{
                defaultContent: '',
				orderable: false,
				},
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'description', name: 'description'},
                {data: 'hierarchy_type', name: 'hierarchy_type'},
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
 /*
  $('#update_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  if($('#action_edit').val() == 'Edit')
  {
   action_url = "{{ route('approval_hierarchy.update') }}";
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
	$('#approval_table').DataTable().ajax.reload();

    }
	$('#formModal').modal('hide');
   }
  });
 });
*/
$(document).on('click', '.delete', function (event) {
	id_approval = $(this).attr('id');
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
			   url:"approval_hierarchy/destroy/"+id_approval,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#approval_table').DataTable().ajax.reload();				 
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
		$('#app_mode').select2({width:'100%'});
		hierarchy_type = [
			{
				id: 'Organization',
				text: 'Organization'
			},
			{
				id: 'Combine',
				text: 'Combine'
			},
			{
				id: 'Custom',
				text: 'Custom'
			},
			
		];	
		
		get_company();
		get_hierarchy();
		get_approval_doc();
	//	get_employee();
		get_position_detail();
		get_approval_mode();
		get_grade();
	
        $('#approval_table').DataTable().ajax.reload();
    }

function get_hierarchy(){
	$('#hierarchy_type').select2({
        data: hierarchy_type,
    }).on('change', function (e) {	
		if($(this).val() != 'Organization'){
			$("#app_detail").css("display","inline");
		}
		else{
			$("#app_detail").css("display","none");
		}			
    }).trigger('change');
}

function get_approval_mode(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_approval_mode') ?>', function (data) {
            global_id_approval_mode = data;
        }).fail(function (data) { // Call failed
            get_approval_mode();
		});
}
/*
function get_employee(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_employee') ?>', function (data) {
            global_id_employee = data;
        }).fail(function (data) { // Call failed
            get_employee();
		});		
}
*/
function get_position_detail(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_position_detail') ?>', function (data) {
            global_id_position_detail = data;
			$('#table_approval_detail').find('.employee_name_input').each(function (i, obj) {
				$('#' + obj.id).val(data[0].employee_name);
			});	
        }).fail(function (data) { // Call failed
            get_position_detail();
		});		
}
function get_approval_doc(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_approval_doc') ?>', function (data) {
            $('#id_approval_doc_type').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_approval_doc();
		});			
}	
function get_company(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_company();
		});	
}
function get_grade(){
	$.getJSON('<?= url('employee/employee_setting/approval_hierarchy/get_grade') ?>', function (data) {
            $('#id_job_grade').prepend('<option selected></option>').select2({
				placeholder: "Select Grade ...",
                data: data,
				allowClear: true,
            });
        }).fail(function (data) { // Call failed
            get_grade();
		});	
}	
</script>
@endsection