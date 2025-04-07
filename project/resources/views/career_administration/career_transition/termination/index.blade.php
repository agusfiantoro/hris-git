@extends('adminlte::page')
@section('title', 'Termination')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Termination</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Termination</button>
                </div>
            </div>
       
			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="termination_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						<thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th style="white-space:nowrap;">Reference Number</th>
                            <th width=150>Name (NIK)</th>
							<th style="white-space:nowrap;">Career Transition</th>
							<th style="white-space:nowrap;">Transaction Type</th>
							<th style="white-space:nowrap;">Employee Status</th>
							<th style="white-space:nowrap;">Position Detail</th>
							<th style="white-space:nowrap;">Position Routing</th>
							<th style="white-space:nowrap;">Job Grade</th>
							<th style="white-space:nowrap;">Job Status</th>
							<th style="white-space:nowrap;">Location</th>
							<th style="white-space:nowrap;">Resign Date</th>
							<th style="white-space:nowrap;">Attachment</th>
							<th data-priority="1">Approval Status</th>
                            <th data-priority="2" width=280>Action</th>
                        </tr>
                    </thead> 
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_termination" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="terminationForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Termination</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            
							<div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
									<input name="id_termination" id="id_termination" type="hidden">
                                    <select name="id_transition_category" id="id_transition_category" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
									 <span class="invalid-feedback" role="alert" id="id_transition_categoryError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
								<div class="col-sm-8">
                                    <select name="id_transaction_type" id="id_transaction_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
									 <span class="invalid-feedback" role="alert" id="id_transaction_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
								<div class="col-sm-8">
                                    <select name="id_employee" id="id_employee" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employement Status</label>
                                <div class="col-sm-8">
                                    <input name="id_employment_status" id="id_employment_status" type="hidden" class="form-control form-control-sm">
                                    <input id="employment_status" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <input id="department" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Position Route</label>
                                <div class="col-sm-8">
                                   <input name="id_position_routing" id="id_position_routing" type="hidden" class="form-control form-control-sm">
                                   <input id="position_routing" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Position Detail</label>
                                <div class="col-sm-8">
									<select name="id_position_detail" id="id_position_detail" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
                                </div>
                            </div>							
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Job Grade</label>
                                <div class="col-sm-8">
                                    <input name="id_job_grade" id="id_job_grade" type="hidden" class="form-control form-control-sm">
                                    <input id="job_grade" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Job Status</label>
                                <div class="col-sm-8">
									<input name="id_job_status" id="id_job_status" type="hidden" class="form-control form-control-sm" >
									<input id="job_status" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Location</label>
                                <div class="col-sm-8">
                                    <input name="id_location" id="id_location" type="hidden" class="form-control form-control-sm">
                                    <input id="location" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Enable Approval</label>
								<div class="col-sm-8">
									<input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="enable_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Hierarchy Approval</label>
								<div class="col-sm-8">
                                    <select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Status</label>
                                <div class="col-sm-8">
                                    <select name="id_approval_status" id="id_approval_status" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
                                    <span class="invalid-feedback" role="alert" id="id_approval_statusError">
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
							<div class="row">
                                <label class="col-sm-4 col-form-label">Resign Date</label>
                                <div class="col-sm-4">
                                    <input name="effective_resign_date" id="effective_resign_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_resign_dateError">
                                        <strong></strong>
                                    </span>
                                </div>								
                            </div>
							<div class="row" style="margin-bottom:8px;">
                                <label class="col-sm-4 col-form-label">Reason</label>
                                <div class="col-sm-8">
                                    <textarea name="remark" id="remark" class="form-control form-control-sm" rows="4" placeholder="Max 200 Char"></textarea>
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>								
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>							
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Max 1 MB</i></label>
								</div>
								
							   </div>
							</div>
													
                        </div>	
					
                       <div class="col-md-6"></div>
                    </div>
                </div>
				<div class="modal-footer">
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> 
                </div>
               
            </form>
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
@section('css')
<style type="text/css">  
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
	
</style>
@stop
@section('scripts')
<script>
let global_career_type = "";
let global_company = 0;
let global_career = "";
	
$(document).on('click', '.new', function () {
	global_career = "";
            $("#terminationForm")[0].reset();
            $("#terminationForm .modal-title").html("<span class='fas fa-plus'></span> Form Termination");
            $(".invalid-feedback").children("strong").text("");
            $("#terminationForm input").removeClass("is-invalid");
            $("#terminationForm select").removeClass("is-invalid");
            $("#terminationForm textarea").removeClass("is-invalid");
			$("#edit_button").css("display","none");
			$("#submit_button").css("display","none");
            $('#modal_form_termination').modal('show');
			$('#id_approval_status').val('').trigger('change');					
        });

$(document).on('click', '.edit', function(){
  let id_termination = $(this).attr('id');
   $("#terminationForm")[0].reset();
	$("#terminationForm .modal-title").html("<span class='fas fa-edit'></span> Edit Termination");
	$(".invalid-feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$(".error-tab").html("");
	$("#terminationForm input").removeClass("is-invalid");
	$("#save_button").css("display","none");	
		$.ajax({
                url: "<?= url('career_administration/career_transition/termination/get_career_edit') ?>",
                method: "GET",
                data: {id_termination: id_termination},
				beforeSend: function () {
						 swal({
							title: "Editing...",
							text: "Please wait",
							icon: "https://career.borwita.co.id/themes/borwita/assets/img/loader.gif",
							buttons: false,      
							closeOnClickOutside: false,
							timer:3000,
						});
					},
				
                success: function (response) {					
                    $('#id_termination').val(response.id_termination).trigger('change');
                    $('#id_employee').val(response.id_employee).trigger('change');
                    $('#id_transition_category').val(response.id_transition_category).trigger('change');
					$('#id_employment_status').val(response.id_employment_status).trigger('change');
                    $('#id_position_detail').val(response.id_position_detail).trigger('change');
                    $('#id_position_routing').val(response.id_position_routing).trigger('change');
                    $('#id_job_grade').val(response.id_job_grade).trigger('change');
                    $('#id_job_status').val(response.id_job_status).trigger('change');
                    $('#id_location').val(response.id_location).trigger('change');
                    $('#remark').val(response.remark).trigger('change');
                    $('#effective_resign_date').val(response.effective_resign_date).trigger('change');
                    $('#expired_date').val(response.expired_date).trigger('change');
                    $('#attachment').val(response.attachment).trigger('change');
                 			
						if(response.enable_approval == 1){
							$('#enable_approval').prop('checked', true);
							$('#id_approval').select2({disabled: false});
							$.getJSON('<?= url('career_administration/career_transition/termination/get_hierachy') . '?code=Termination_Request' ?>', function (data) {
								$('#id_approval').select2({
									placeholder: "Select Hierarchy Approval ...",
									allowClear: true,
									data: data,
									});
								$('#id_approval').val(response.id_approval).trigger('change');
								});											   
						}
					setTimeout(function () {
						$('#id_transaction_type').val(response.id_transaction_type).trigger('change');
					}, 2000);
                    $('#select2status').val(response.status);
                    $('#id_approval_status').val(response.id_approval_status).trigger('change');
					
					if(response.code_status == 'Request_Approval' || response.code_status == 'Approved'){					
						setTimeout(function () {
							$("#terminationForm input").prop("disabled", true);			
							$("#terminationForm select").prop("disabled", true);
							$("#terminationForm textarea").prop("disabled", true);
							$(".input-group-append").css("display","none");
							$('#id_approval').select2({disabled: true});
							$("#edit_button").css("display","none");
							$("#submit_button").css("display","none");
						}, 1000);
					}
					
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
			
		$('#modal_form_termination').modal('show');
		return false;
 });


    $(function () {
		var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('termination.save') }}";
			$(this).closest(".card").find("terminationForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('termination.update') }}";
			$(this).closest(".card").find("terminationForm").submit();
		  });
		
	  
        $('#terminationForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $(".invalid-group").children("strong").text("");
            $("#terminationForm input").removeClass("is-invalid");
            $("#terminationForm select").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
					url: AjaxUrl,
				//	url: "{{ route('termination.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_termination').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
								   }
								);
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
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
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

    });

		

$(document).ready(function(){
bsCustomFileInput.init();
    $('#termination_table').DataTable({
            processing: true,
		//	serverSide: true,
       //     scrollY: true,
       //     scrollX: true,
			pageLength: 10,
			responsive: true,
		/*	fixedColumns: {
				rightColumns: 1,
			},
		*/
		    ajax: {
                url: "{{ route('termination.index') }}",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#termination_table').DataTable().ajax.reload();
				}
            },
			
			rowCallback: function(row, data, index){
				if(data['code_app_status'] == 'Approved'){
					$(row).find('td:eq(17)').css('background', '#90fca4');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				}
				if(data['code_app_status'] == 'Request_Approval'){
					$(row).find('.submit_approve').css('display', 'none');
				}
				if(data['code_app_status'] == 'Cancel'){
				//	$(row).find('td:eq(18)').css('float', 'right');
					$(row).find('.cancel').css('display', 'none');
				}
			  },
            columns: [
				{
                defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
                data: 'id_termination',
                defaultContent: '',
                orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'reference_number', name: 'reference_number'},
				{data: function (data, type, dataToSet) {
					return data.name + ' (' + data.nik_employee + ')';
				}},
				{data: 'transition_category', name: 'transition_category'},
				{data: 'transaction_type', name: 'transaction_type'},
				{data: 'employment_status', name: 'employment_status'},
				{data: 'position_detail', name: 'position_detail'},
				{data: 'position_routing', name: 'position_routing'},
				{data: 'job_grade', name: 'job_grade'},
				{data: 'job_status', name: 'job_status'},
				{data: 'location', name: 'location'},
				{data: 'effective_resign_date', name: 'effective_resign_date'},
				{data: 'attachment', name: 'attachment'},
				{data: 'desc_app_status', name: 'desc_app_status'},
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
            ],
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

});

$(document).on('click', '.submit_approve', function (event) {
	id_termination = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This Data will be Submited!',
        icon: 'warning',
       buttons: true,
		  confirmButtonText: 'Yes, Submit it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"termination/submit_approve/"+id_termination,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#termination_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Submited!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#termination_table').DataTable().ajax.reload();	
					});
				}, 50);
			   }
			  })
        }
    });
});

$(document).on('click', '.cancel', function (event) {
	id_termination = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This Data will be Cancel!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, Cancel it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"termination/cancel/"+id_termination,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#termination_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#termination_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});

/*
$(document).on('click', '.delete', function (event) {
	id_termination = $(this).attr('id');
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
			   url:"termination/destroy/"+id_termination,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#termination_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#termination_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});
*/

function refresh_data() {
$('#id_position_detail').select2();
$('#effective_resign_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	

		get_employee();
		get_position();
		get_career_category();
		get_approval_status();
		get_career_type_select();
			
	$('#id_approval').select2({disabled: true});	
	$('#enable_approval:checkbox').on('change', function (e) {
		if(this.checked){
			$('#id_approval').select2({disabled: false});
			$.getJSON('<?= url('career_administration/career_transition/termination/get_hierachy') . '?code=Termination_Request' ?>', function (data) {
				$('#id_approval').select2({
					placeholder: "Select Hierarchy Approval ...",
					allowClear: true,
					data: data,
					});
				});	
			get_approval_status();
		}
		else{
			$('#id_approval_status').val('').trigger('change');
			$('#id_approval').select2({disabled: true});
			$('#id_approval').val('');
			$('#id_approval').select2({disabled: true});
		}
		
	});
		$('#select2status').select2({width:'100%'});
}

function get_employee(){		
	$.getJSON('<?= url('career_administration/career_transition/termination/get_employee') ?>', function (data) {
            $('#id_employee').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ..",
				allowClear: true,
                data: data,
            }).on('change', function (e) { 
				$('#id_position_detail').empty();
					$('#employment_status').val('');
					$('#position_routing').val('');
					$('#department').val('');
					$('#job_grade').val('');
					$('#job_status').val('');
					$('#location').val('');
					var_pos = $(this).select2('data')[0].id_employee;
						$.getJSON('<?= url('career_administration/career_transition/termination/get_position') . '?id_employee=' ?>' +var_pos , function (data) {
							 $('#id_position_detail').select2({
								data: data,
							});
							get_position_detail($('#id_position_detail').find("option:first-child").val());					
						});	
            });
        }).fail(function (data) { // Call failed
            get_employee();
		});	
}

function get_position(){
	$('#id_position_detail').on('change', function (e) {
			get_position_detail($(this).val());
		});		
}
function get_position_detail(pos_det){
	$.getJSON('<?= url('career_administration/career_transition/termination/get_position_detail') . '?id_position_detail='?>'+pos_det, function (data) {
							
		$('#id_employment_status').val(data[0].id_employment_status);
		$('#employment_status').val(data[0].employment_status);
	//	$('#id_old_position_detail').val(data[0].id_position_detail);
	//	$('#old_position_detail').val(data[0].position_detail);
		$('#id_position_routing').val(data[0].id_routing);
		$('#position_routing').val(data[0].position_routing);
		$('#department').val(data[0].department);
		$('#id_job_grade').val(data[0].id_job_grade);
		$('#job_grade').val(data[0].job_grade);
		$('#id_job_status').val(data[0].id_job_status);
		$('#job_status').val(data[0].job_status);
		$('#id_location').val(data[0].id_location);
		$('#location').val(data[0].work_location);
	});
	return false;
}
function get_career_category(){
	$.getJSON('<?= url('career_administration/career_transition/termination/get_career_category') ?>', function (data) {
            $('#id_transition_category').select2({
                data: data,
            }).on('change', function (e) { 
				if($(this).select2('data')[0].code == 'Termination'){
					$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_career_type') . '?id=' ?>' + $(this).select2('data')[0].id, function (data) {
						$('#id_transaction_type').empty();
						$('#transaction_number').val('');
						$('#id_transaction_type').select2({
							data: data,
						});
					});	
					get_career_type($('#id_transition_category').find(":selected").val(),$(this).select2('data')[0].code);
				}
				
            }).trigger('change');
    }).fail(function (data) { // Call failed
            get_career_category();
    });	
}

function get_career_type(car_type){
	$.getJSON('<?= url('career_administration/career_transition/termination/get_career_type') . '?id=' ?>' + car_type, function (data) {		
						$('#id_transaction_type').empty();
						$('#transaction_number').val('');
						$('#id_transaction_type').select2({
							data: data,
						});
						 str = $('#id_transaction_type').select2('data')[0].text;
						 global_career_type = str.replace(/\s+/g,'_');  
				//	console.log($('#id_transaction_type').select2('data')[0].id);						 
					}).fail(function (data) { // Call failed
						get_career_type(car_type);
					});	
}


function get_career_type_select(){
	$('#id_transaction_type').empty();
	$('#id_transaction_type').on('change', function (e) {
		$('#transaction_number').val('');
		str = $(this).select2('data')[0].text;
		global_career_type = str.replace(/\s+/g,'_');
		});
}


function get_approval_status() {
	$.getJSON('<?= url('career_administration/career_transition/termination/get_approval_status') ?>', function (data) {
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
        });	
}	


</script>

@endsection