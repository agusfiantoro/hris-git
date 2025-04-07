@extends('adminlte::page')
@section('title', 'Announcement')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Announcement</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Announcement</button>
                </div>
            </div>
			
			 <div class="card-body">
					<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="announcement_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>
					   <th></th>
						<th></th>
						<th>No</th>
						<th style="white-space:nowrap;">Reference Number</th>
						<th data-priority="3">Description</th>
						<th>Employee Request</th>
						<th>Announcement Type</th>
						<th>Attachment</th>
						<th data-priority="4">Published</th>
						<th>Status</th>
						<th data-priority="2"  style="white-space:nowrap;">Approval Status</th>
                        <th data-priority="1" style="display:inline;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_announcement"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="announcementForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Announcement</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Reference Number</label>
                                <div class="col-sm-8">
									<input name="id_announcement" id="id_announcement" type="hidden">
                                    <input type="text" name="reference_number" id="reference_number" value="{{ $codeid }}" readonly="readonly" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_numberError">
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
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request By</label>
								<div class="col-sm-8">
                                    <select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Announcement Type</label>
								<div class="col-sm-8">
                                    <select name="id_anouncement_type" id="id_anouncement_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_anouncement_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Published</label>
								<div class="col-sm-8">
									<input type="checkbox" name="published" id="published" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="publishedError">
                                        <strong></strong>
                                    </span>
                                </div>                             
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment Type</label>
                                <div class="col-sm-2">
									  <select id="attachment_type" name="attachment_type" class="form-control form-control-sm">
										<option value="image">Image</option>
										<option value="pdf">PDF</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="attachment_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
							   <label class="col-md-2" style="margin-top:5px;">Attachment</label>
							   <div class="col-md-4">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="customFile">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<a id="attachment_edit" href="#" target="_blank">Download File</a>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB</i></label>
								</div>
							   </div>
							</div>
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
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
                                <label class="col-sm-4 col-form-label">Enable Approval</label>
								<div class="col-sm-8">
									<input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="enable_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Start Date to End Date</label>								
								<div class="col-sm-8">
									<div class="input-group">
										<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm"/>
										<input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
										<input name="end_date" id="end_date" class="form-control form-control-sm" hidden>										
										<div class="input-group-append">
												<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
									</div>
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
                                <label class="col-sm-4 col-form-label">Approval Status</label>
                                <div class="col-sm-8">
                                    <select name="id_approval_status" id="id_approval_status" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
                                    <span class="invalid-feedback" role="alert" id="id_approval_statusError">
                                        <strong></strong>
                                    </span>   								
                                </div>
                            </div>						
                        </div>
						
						<div class="col-md-12">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Announcement Content</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="content_letter" id="content_letter"></textarea>
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
let global_id_announcement = "";

function get_employee() {
		$.getJSON('<?= url('employee/employee_setting/announcement/get_employee') ?>', function (data) {
            $('#id_employee_request').select2({
                data: data,
            });
			
        }).fail(function (data) { // Call failed
            get_employee();
        });
	}
function get_company() {
 $.getJSON('<?= url('employee/employee_setting/announcement/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_company();
        });	
}	
function get_announcement_type() {
 $.getJSON('<?= url('employee/employee_setting/announcement/get_announcement_type') ?>', function (data) {
            $('#id_anouncement_type').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_announcement_type();
        });	
}	
function get_approval_status() {
	$.getJSON('<?= url('employee/employee/employee_request/get_approval_status') ?>', function (data) {
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
        });	
}	
	
$(function () {
	$('.summernote').summernote({
		height:300,
	});

	get_employee();
	get_company();
	get_announcement_type();
	get_approval_status();

	$('#select2status').select2({width:'100%'});
	$('#attachment_type').select2({width:'100%'});	

	$('#id_approval').select2({disabled: true});
//	$('#daterange').attr('disabled', true);
	$('#enable_approval').attr('disabled', true);
	$('#enable_approval').prop('checked', false);
	
	$('#daterange').daterangepicker({
		uiLibrary: 'bootstrap4',
		autoApply: true,
		opens: 'center',
			locale: {
			  format: 'YYYY-MM-DD',
			  separator: '   to   ',
			  closeText: 'Clear',
			},
		}, function(start, end, label) {
		$("#start_date").val(start.format('YYYY-MM-DD'));
		$("#end_date").val(end.format('YYYY-MM-DD'));				
	});	
/*	
	$('#enable_approval:checkbox').on('change', function (e) {
		if(this.checked){
			$('#id_approval').select2({disabled: false});
			$('#published').attr('disabled', true);
			$('#published').prop('checked', false);
			var today = new Date().toISOString().split('T')[0];
					$("#start_date").val(today);
					$("#end_date").val(today);
			$.getJSON('<?= url('employee/employee_setting/announcement/get_hierachy') . '?code=Announcement_Request' ?>', function (data) {
				$('#id_approval').select2({
					placeholder: "Select Hierarchy Approval ...",
					data: data,
					});
				});	
			$('#daterange').attr('disabled', false);
			$('#daterange').daterangepicker({
				uiLibrary: 'bootstrap4',
				autoApply: true,
				opens: 'center',
					locale: {
					  format: 'YYYY-MM-DD',
					  separator: '   to   ',
					  closeText: 'Clear',
					},
				}, function(start, end, label) {
				$("#start_date").val(start.format('YYYY-MM-DD'));
				$("#end_date").val(end.format('YYYY-MM-DD'));				
				});	
				
			get_approval_status();
		}
		else{
			$('#id_approval_status').val('').trigger('change');
			$('#id_approval').select2({disabled: true});
			$('#id_approval').val('');
			$('#id_approval').select2({disabled: true});
			$('#daterange').attr('disabled', true);
			$('#daterange').val('');
			$('#daterange').attr('disabled', true);
			$('#published').attr('disabled', false);
		}
		
	});
*/	
$(document).on('click', '.new', function () {
			global_id_announcement = "";
			$('.summernote').summernote('reset');
            $("#announcementForm")[0].reset();
            $("#announcementForm .modal-title").html("<span class='fas fa-plus'></span> Form Announcement");
            $(".invalid-feedback").children("strong").text("");
            $("#announcementForm input").removeClass("is-invalid");
            $("#announcementForm textarea").removeClass("is-invalid");
			$("#edit_button").css("display","none");
			$("#submit_button").css("display","none");
			$('#attachment_edit').css("display","none");
		//	document.getElementById("attachment").style.display = "none";
            $('#modal_form_announcement').modal('show');
			$('#id_approval_status').val('').trigger('change');
        });

 $(document).on('click', '.edit', function(){
  let id_announcement = $(this).attr('id');
  global_id_announcement = id_announcement;
   $("#announcementForm")[0].reset();
	$("#announcementForm .modal-title").html("<span class='fas fa-edit'></span> Edit Announcement");
	$(".invalid-feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$(".error-tab").html("");
	$("#announcementForm input").removeClass("is-invalid");
	$("#save_button").css("display","none");
	$('#attachment_edit').css("display","inline");
//	document.getElementById("attachment").style.display = "inline";
			
  var fileInput = document.getElementById('customFile')
	fileInput.value = ''
	fileInput.dispatchEvent(new Event('change'))
		$.ajax({
                url: "<?= url('employee/employee_setting/announcement/get_announcement_edit') ?>",
                method: "GET",
                data: {id_announcement: id_announcement},
                success: function (response) {
                    $('#id_announcement').val(response.id_announcement).trigger('change');
                    $('#reference_number').val(response.reference_number).trigger('change');
                    $('#description').val(response.description).trigger('change');
                    $('#id_employee_request').val(response.id_employee_request).trigger('change');
					$('#start_date').val(response.start_date).trigger('change');
                    $('#end_date').val(response.end_date).trigger('change');					
                    $('#id_anouncement_type').val(response.id_anouncement_type).trigger('change');
                    $('#attachment_type').val(response.attachment_type).trigger('change');
                //    document.getElementById("attachment").src = "data:image;base64,"+response.attachment;
					if(response.attachment_type != null){
						$('#attachment_edit').css("display","inline");
						if(response.attachment_type == 'image'){
							document.getElementById("attachment_edit").href = "data:image/jpg;base64,"+response.attachment;
						}
						else if(response.attachment_type == 'pdf'){
							document.getElementById("attachment_edit").href = "data:application/pdf;base64,"+response.attachment;
						}
					}
					else if(response.attachment_type == null && response.attachment != null){
						$('#attachment_edit').css("display","inline");
						document.getElementById("attachment_edit").href = "../../project/storage/app/public/upload/announcement/"+response.attachment;	
					}
					else{
						$('#attachment_edit').css("display","none");
					}
					$("#content_letter").summernote("code", response.content_letter);
                    $('#id_company').val(response.id_company).trigger('change');

							if(response.start_date == null && response.end_date == null){
										$('#daterange').daterangepicker({
											uiLibrary: 'bootstrap4',
													autoApply: true,
													opens: 'center',
													locale: {
														  format: 'YYYY-MM-DD',
														  separator: '   to   ',
														  closeText: 'Clear',
														},
										}, function(start, end, label) {
											$("#start_date").val(start.format('YYYY-MM-DD'));
											$("#end_date").val(end.format('YYYY-MM-DD'));
															
										});	   
									}
									else{
										$('#daterange').daterangepicker({
											uiLibrary: 'bootstrap4',
													autoApply: true,
													opens: 'center',
													locale: {
														  format: 'YYYY-MM-DD',
														  separator: '   to   ',
														  closeText: 'Clear',
														},
											startDate: response.start_date, endDate: response.end_date 
										}, function(start, end, label) {
											$("#start_date").val(start.format('YYYY-MM-DD'));
											$("#end_date").val(end.format('YYYY-MM-DD'));
															
										});	   
									}
						
					if (response.published == 1) {
						$('#published').prop('checked', true);
					} else {
						$('#published').prop('checked', false);
					}	
                    $('#select2status').val(response.status);
                    $('#id_approval_status').val(response.id_approval_status).trigger('change');
                  					
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
		$('#modal_form_announcement').modal('show');
 });
		
		var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('announcement.save') }}";
			$(this).closest(".card").find("announcementForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('announcement.update') }}";
			$(this).closest(".card").find("announcementForm").submit();
		  });
		
        $('#announcementForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $("#announcementForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					//	url: global_id_announcement == '' ? "{{ route('announcement.save') }}" : "{{ route('announcement.update') }}",
					url: AjaxUrl,
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,			
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_announcement').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
								   }
								);
	                        $('#announcement_table').DataTable().ajax.reload();
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
							let err = "";
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
								err += errors[key][0]+"\n";
                            });
							swal({
								icon: 'error',
                                dangerMode: true,
								content: {
									element: "div",
									attributes: {
										innerText: err,
										className: "swal-red",
									},
								},
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

  var ta =  $('#announcement_table').DataTable({
        processing: true,
		responsive: true,        
        ajax: {
		   url: "{{ route('announcement.index') }}",
		   error: function (jqXHR, textStatus, errorThrown) {
				$('#announcement_table').DataTable().ajax.reload();
            }
		  },
		rowCallback: function(row, data, index){
				if(data['code_app_status'] == 'Approved'){
					$(row).find('td:eq(9)').css('background', '#90fca4');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				}
				if(data['code_app_status'] == 'Request_Approval'){
					$(row).find('.submit_approve').css('display', 'none');
					
				}
				if(data['code_app_status'] == 'Cancel'){
					$(row).find('.cancel').css('display', 'none');
				}
				if(data['enable_approval'] == 0){
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.cancel').css('display', 'none');
				}
				if(data['published'] == 1){
					$(row).find('td:eq(8)').css('background', '#90fca4');
				}
			  },
        columns: [
			{
                defaultContent: '',
				orderable: false,
			},
			{   // Checkbox select column
                data: 'id_announcement',
                defaultContent: '',
                orderable: false
            },
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'reference_number', name: 'reference_number'},
			{ data: 'description', name: 'description' },
			{ data: 'employee_name', name: 'employee_name' },
			{ data: 'announcement_type', name: 'announcement_type' },
			{ data: 'attachment', name: 'attachment',render: function ( data, type, row ) {	
					if(data == null){
						return "";
					}				
					else if(row['attachment_type']==null){
						return '<a href="../../project/storage/app/public/upload/announcement/'+data+'" target="_blank">Download File</a>';
					}
					else if(row['attachment_type']=='pdf'){
						return '<a download="'+Date.now()+'.pdf" href="data:application/pdf;base64,'+ data + '">Download File</a>';
					}
					else if(row['attachment_type']=='image'){
						return '<a download="'+Date.now()+'.jpg" href="data:image/jpg;base64,'+ data + '">Download File</a>';
					}
				} 
			},
			{ data: 'published', name: 'published' },
			{ data: 'status', name: 'status' },
			{ data: 'desc_app_status', name: 'desc_app_status' },
			{ data: 'action', name: 'action', className:'space', orderable: false, render: function ( data, type, row ) {
				
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

$(document).on('click', '.submit_approve', function (event) {
	id_announcement = $(this).attr('id');
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
			   url:"announcement/submit_approve/"+id_announcement,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#announcement_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Submited!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#announcement_table').DataTable().ajax.reload();	
					});
				}, 50);
			   }
			  })
        }
    });
});

$(document).on('click', '.cancel', function (event) {
	id_announcement = $(this).attr('id');
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
			   url:"announcement/cancel/"+id_announcement,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#announcement_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#announcement_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});
	

$(document).on('click', '.delete', function (event) {
	id_announcement = $(this).attr('id');
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
			   url:"announcement/destroy/"+id_announcement,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#announcement_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						location.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});

});

</script>

@endsection