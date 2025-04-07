@extends('adminlte::page')
@section('title', 'Award')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Award</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Award</button>
                </div>
            </div>
       
			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="award_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Reference Number</th>
						<th>Award Name</th>
						<th>Award Number</th>
						<th data-priority="2">NIK</th>
						<th data-priority="1">Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Principal</th>
                        <th>Region</th>
                        <th>Branch</th>
						<th>Effective Date</th>
						<th>Attachment</th>
						<th>Status</th>
						<th data-priority="3" width=200>Action</th>
					  </tr>
					 </thead>
					 <tbody>
					</tbody>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_award"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="awardForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Award</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Award Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remark" id="remark" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Award Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="award_letter_number" id="award_letter_number" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="award_letter_numberError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Certificate Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="award_certificate_number" id="award_certificate_number" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="award_certificate_numberError">
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
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-3">
									  <select id="attachment_type" name="attachment_type" class="form-control form-control-sm">
										<option value="image">Image</option>
										<option value="pdf">PDF</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="attachment_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
							   <div class="col-md-5">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB</i></label>
								</div>
								
							   </div>
							</div>
                           
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
							<div class="row">
                                <label class="col-sm-3 col-form-label">Company</label>
								<div class="col-sm-9">
                                    <select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Reference Date</label>
                                <div class="col-sm-9">
                                    <input name="reference_date" id="reference_date" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-3 col-form-label">Effective Date</label>
                                <div class="col-sm-3">
                                    <input name="effective_date" id="effective_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
								<label class="col-sm-3 col-form-label" style="text-align:right;">Expired Date</label>
                                <div class="col-sm-3">
                                   <input name="expired_date" id="expired_date" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="expired_dateError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							 <div class="row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
									  <select id="select2status" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                        </div>
						
						<div class="col-md-12">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="description_name" id="description_name"></textarea>
									</div> 
								</div> 
                            </div>
                        </div>
                       <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
					<button type="submit" id ="publish" class="btn btn-success"><i class="fas fa-save"></i>  Save & <i class="fas fa-bullhorn"></i> Publish to Announcement</button>&nbsp;
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
	
<div id="formModal" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
  <div class="modal-content">
   <div class="modal-header">
         <h4 class="modal-title"></h4>
         <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 <form id="update_form" class="form-horizontal" method="POST">
					@csrf
					<div class="row">
                        <div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Award Number</label>
                                <div class="col-sm-8">
									<input type="hidden" name="reference_number" id="reference_number_edit" readonly="readonly">
                                    <input type="text" name="award_letter_number" id="award_letter_number_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="award_letter_number_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Certificate Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="award_certificate_number" id="award_certificate_number_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="award_certificate_number_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
								<div class="col-sm-8">
                                    <select name="id_employee" id="id_employee_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Remark</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remark" id="remark_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="remarkError_edit">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment Type</label>
                                <div class="col-sm-2">
									  <select id="attachment_type_edit" name="attachment_type" class="form-control form-control-sm">
										<option value="image">Image</option>
										<option value="pdf">PDF</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="attachment_type_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
							   <label class="col-md-2" style="margin-top:5px;">Attachment</label>
							   <div class="col-md-4">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="customFile">
								  <input type="hidden" id="attachment_name" name="attachment_name">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<a id="attachment_edit" href="#" target="_blank">Download File</a>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB</i></label>
								</div>
								
							   </div>
							</div>                          
                        </div>
					
						<div class="col-md-6" style="margin-bottom:30px;">
							<div class="row">
                                <label class="col-sm-3 col-form-label">Company</label>
								<div class="col-sm-9">
                                    <select name="id_company" id="company_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                            <div class="row">
                                <label class="col-sm-3 col-form-label">Reference Date</label>
                                <div class="col-sm-9">
                                    <input name="reference_date" id="reference_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-3 col-form-label">Effective Date</label>
                                <div class="col-sm-3">
                                    <input name="effective_date" id="effective_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
								<label class="col-sm-3 col-form-label" style="text-align:right;">Expired Date</label>
                                <div class="col-sm-3">
                                   <input name="expired_date" id="expired_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="expired_date_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							 <div class="row">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
									  <select id="select2status_edit" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="status_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>														
                        </div>
						
						<div class="col-md-12">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="description_name" id="description_name_edit"></textarea>
									</div> 
								</div> 
                            </div>
                        </div>
                       <div class="col-md-6"></div>
                    </div>
               
					<div class="modal-footer">
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action_edit" />
                 <input type="hidden" name="id_transaction" id="id_transaction" />
                 <button type="button" class="announ btn btn-success"><i class="fas fa-bullhorn"></i>  Publish to Announcement</button>&nbsp;
                 <button type="submit" name="action_button" id="action_button" class="btn btn-primary" value="edit"><i class="fas fa-edit"></i> Update</button>
                </div>
                <button type="button" onclick="javascript:window.location.reload()" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
					
				</form>   	 
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

<div class="modal fade" id="modal_form_announ"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width:50%;">
        <div class="modal-content">
            <form method="POST" id="announForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5>Publish Award to Announcement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                         <div class="col-md-12">
							<div class="row">
                                <label class="col-sm-5 col-form-label">Request By</label>
								<div class="col-sm-7">
                                    <select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-5 col-form-label">Announcement Type</label>
								<div class="col-sm-7">
                                    <select name="id_anouncement_type" id="id_anouncement_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_anouncement_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-5 col-form-label">Enable Approval</label>
								<div class="col-sm-7">
									<input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;" disabled>
                                    <span class="invalid-feedback" role="alert" id="publishedError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-5 col-form-label">Hierarchy Approval</label>
								<div class="col-sm-7">
                                    <select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-5 col-form-label">Published</label>
								<div class="col-sm-7">
									<input type="checkbox" name="published" id="published" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="publishedError">
                                        <strong></strong>
                                    </span>
                                </div>                             
                            </div>
						
                         </div>                           		             												
                    </div>
                </div>
                <div class="modal-footer">
					<button type="submit" class="submit_approve btn btn-sm btn-success" id="submit_button">Submit</button>&nbsp;					
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> 
                </div>
            </form>
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
        background: #e8ebed;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
</style>
@stop

@section('scripts')
<script>
let global_id_transaction = "";

function get_announcement_type() {
 $.getJSON('<?= url('career_administration/award_dicipline/awards/get_announcement_type') ?>', function (data) {
            $('#id_anouncement_type').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_announcement_type();
        });	
}	
function get_employee() {
	$.getJSON('<?= url('career_administration/award_dicipline/awards/get_employee') ?>', function (data) {
            $('#id_employee').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ...",
                data: data,
            });
			$('#id_employee_edit').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ...",
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_employee();
        });	
}	
function get_req_employee() {
	$.getJSON('<?= url('career_administration/award_dicipline/awards/get_req_employee') ?>', function (data) {
			$('#id_employee_request').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_req_employee();
        });	
}	
function get_company() {
	 $.getJSON('<?= url('career_administration/award_dicipline/awards/get_company') ?>', function (data) {
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
$(function () {
$('.summernote').summernote({
	height:300,
});

$('#reference_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#reference_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#effective_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#effective_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#expired_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#expired_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });			
$('#attachment_type').select2({width:'100%'});	
$('#attachment_type_edit').select2({width:'100%'});

$('#select2status').select2();	
$('#select2status_edit').select2();

		get_company();
		get_employee();
		get_req_employee();
		get_announcement_type();
		
		$('#id_approval').select2({disabled: true});	
		$('#published').prop('checked', true);
		$('#enable_approval').prop('checked', false);
	/*	$('#enable_approval:checkbox').on('change', function (e) {
		if(this.checked){
			$('#id_approval').select2({disabled: false});
			$('#published').attr('disabled', true);
			$('#published').prop('checked', false);
			$.getJSON('<?= url('employee/employee_setting/announcement/get_hierachy') . '?code=Announcement_Request' ?>', function (data) {
				$('#id_approval').select2({
					placeholder: "Select Hierarchy Approval ...",
					data: data,
					});
				});	
		}
		else{
			$('#id_approval').select2({disabled: true});
			$('#id_approval').val('');
			$('#id_approval').select2({disabled: true});
			$('#published').attr('disabled', false);
			$('#published').prop('checked', true);
		}
		
	});
	*/
				
});

$(document).on('click', '.announ', function () {
		$("#announForm")[0].reset();
			$('#published').prop('checked', true);
        $('#modal_form_announ').modal('show');
        });
		
$(document).on('click', '.new', function () {
			$('.summernote').summernote('reset');
            $("#awardForm")[0].reset();
            $("#awardForm .modal-title").html("<span class='fas fa-plus'></span> Form Award");
            $(".invalid-feedback").children("strong").text("");
            $("#awardForm input").removeClass("is-invalid");
            $("#awardForm textarea").removeClass("is-invalid");
			$('#published').prop('checked', true);
            $('#modal_form_award').modal('show');
        });

    $(function () {
        $('#awardForm').submit(function (e) {
            e.preventDefault();
		//	console.log(global_id_transaction);
			var publish = e.originalEvent.submitter.id; 
			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $("#awardForm input").removeClass("is-invalid");
			if(global_id_transaction == ""){
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
					url: "{{ route('award.save') }}",
				//	url: global_id_transaction == '' ? "{{ route('award.save') }}" : "{{ route('award.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							if(publish == ''){
								 $('#modal_form_award').modal('hide');
								swal({
									icon: 'success',
									title: 'Success',
									text: response.message
								}).then(function(){ 
									   location.reload();
									   }
									);
								$('#award_table').DataTable().ajax.reload();
							}
							else{
							//	console.log(response.result);
								$("#announForm")[0].reset();								
										$('#reference_number_edit').val(response.result.reference_number);
										$('#id_employee_edit').val(response.result.id_employee).trigger('change');
										$('#effective_date_edit').val(response.result.effective_date);
										$('#expired_date_edit').val(response.result.expired_date);
										$('#award_letter_number_edit').val(response.result.award_letter_number);
										$('#award_certificate_number_edit').val(response.result.award_certificate_number);
										$("#description_name_edit").summernote("code", response.result.description_name);
										$('#reference_date_edit').val(response.result.reference_date);
										$('#remark_edit').val(response.result.remark);
										$('#attachment_name').val(response.result.attachment).trigger('change');
										$('#attachment_type_edit').val(response.result.attachment_type).trigger('change');
										$('#select2status_edit').val(response.result.status).trigger('change');
										$('#id_company_edit').val(response.result.id_company);
										$('#id_transaction').val(response.result.id_transaction);
										global_id_transaction = response.result.id_transaction;
								
								$('#modal_form_announ').modal('show');
							}
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
            }
			else{
				$('.announ').trigger('click');
			}
        });

		$('#announForm').submit(function (e) {
            e.preventDefault();			
			var formData = $('#update_form').serializeArray();
		//	var formData = new FormData($('#update_form')[0]);
			let formDataAnnoun = $(this).serializeArray();        
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json",
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },						
					cache: false,
					url: "{{ route('award.save_announ') }}",
                    data: {award:formData,announ:formDataAnnoun},
              //      data: {'award':formData},
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_announ').modal('hide');
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
                        else if (response.status === 500) {
                             swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Data has been published in announcement'
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

    $('#award_table').DataTable({
        processing: true,
    //    serverSide: true,
		responsive: true,
        ajax: {
		   url: "{{ route('award.index') }}",
		   error: function (jqXHR, textStatus, errorThrown) {
				$('#award_table').DataTable().ajax.reload();
            }
		  },
        columns: [
		{
                defaultContent: '',
				orderable: false,
				},
		{   // Checkbox select column
                data: 'id_transaction',
                defaultContent: '',
                orderable: false
            },
		{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'reference_number', name: 'reference_number' },
			{ data: 'remark', name: 'remark' },
			{ data: 'award_letter_number', name: 'award_letter_number' },
			{ data: 'nik_employee', name: 'nik_employee' },
			{ data: 'employee_name', name: 'employee_name' },
            { data: 'position', name: 'position'},
            { data: 'department', name: 'department' },
            { data: 'principal', name: 'principal' },
            { data: 'region', name: 'region' },
            { data: 'branch', name: 'branch' },
			{ data: 'effective_date', name: 'effective_date' },
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
			{ data: 'status', name: 'status' },
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {
				
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
	
  $('#update_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';
  var formData = new FormData(this);

  if($('#action_edit').val() == 'Edit')
  {
   action_url = "{{ route('award.update') }}";
  }
  $(".invalid-feedback").children("strong").text("");
  $("#update_form input").removeClass("is-invalid");
  $("#update_form textarea").removeClass("is-invalid");
   $.ajax({
	enctype: 'multipart/form-data',
	processData: false,  // Important!
	contentType: false,
	cache: false,
   url: action_url,
   method:"POST",
   data:formData,
   dataType:"json",
   success:function(response)
   {
	if (response.status == 'true') {
		 $('#formModal').modal('hide');
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
		}).then(function(){ 
			   location.reload();
			   }
			);
	} 
   },
    error: function (response) {
		if (response.status === 422) {
			let errors = response.responseJSON.errors;
			Object.keys(errors).forEach(function (key) {
				$("#" + key + "_edit").addClass("is-invalid");
				$("#" + key + "_editError").children("strong").text(errors[key][0]);
			});
		} 
	}
  });
 });

 $(document).on('click', '.edit', function(){
  var id_transaction = $(this).attr('id');
  var fileInput = document.getElementById('customFile')
	fileInput.value = ''
	fileInput.dispatchEvent(new Event('change'))
  $.ajax({
   url :"awards/edit/"+id_transaction,
   dataType:"json",
   success:function(data)
   {
	   
    $('#reference_number_edit').val(data.result.reference_number);
    $('#id_employee_edit').val(data.result.id_employee).trigger('change');
    $('#effective_date_edit').val(data.result.effective_date);
    $('#expired_date_edit').val(data.result.expired_date);
    $('#award_letter_number_edit').val(data.result.award_letter_number);
    $('#award_certificate_number_edit').val(data.result.award_certificate_number);
	$("#description_name_edit").summernote("code", data.result.description_name);
    $('#reference_date_edit').val(data.result.reference_date);
    $('#attachment_name').val(data.result.attachment);
	if(data.result.attachment_type != null){
		$('#attachment_edit').css('display','inline');
		if(data.result.attachment_type == 'image'){
			document.getElementById("attachment_edit").href = "data:image/jpg;base64,"+data.result.attachment;
		}
		else if(data.result.attachment_type == 'pdf'){
			document.getElementById("attachment_edit").href = "data:application/pdf;base64,"+data.result.attachment;
		}
	}
	else if(data.result.attachment_type == null && data.result.attachment != null){
		$('#attachment_edit').css('display','inline');
		document.getElementById("attachment_edit").href = "../../project/storage/app/public/upload/announcement/"+data.result.attachment;	
	}
	else{
		$('#attachment_edit').css('display','none');
	}
    $('#remark_edit').val(data.result.remark);
    $('#attachment_type_edit').val(data.result.attachment_type).trigger('change');
    $('#select2status_edit').val(data.result.status).trigger('change');
    $('#id_company_edit').val(data.result.id_company);
    $('#id_transaction').val(id_transaction);
    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action_edit').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });
 

$(document).on('click', '.delete', function (event) {
	id_transaction = $(this).attr('id');
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
			   url:"awards/destroy/"+id_transaction,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#award_table').DataTable().ajax.reload();				 
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