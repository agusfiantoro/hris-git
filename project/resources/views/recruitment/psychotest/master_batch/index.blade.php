@extends('adminlte::page')
@section('title', 'Master Batch')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Form Master Batch</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Master Batch</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default pull-left">Advanced Search</button>
				<button type="button" class="btn btn-sm btn-primary pull-right" onclick="load_detail()"><i class="fa fa-list-alt"></i> List Participant</button>
					<br>
					<br>
					<table id="batch_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th data-priority="2">Batch Code</th>
						<th data-priority="3">Name</th>
						<th data-priority="4">Location</th>
						<th>Total Participant</th>
						<th data-priority="5">Start Date</th>
						<th data-priority="6">End Date</th>						
						<th>Status</th>						
						<th data-priority="1" style="text-align:center;white-space:nowrap;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_batch"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="batchForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Batch</h5>
                    <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Batch Name</label>
                                <div class="col-sm-8">
									<input name="id_batch" id="id_batch" type="hidden">	
									<input type="text" name="batch_name" id="batch_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="batch_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>							
							<div class="row">
								<label class="col-sm-4 col-form-label">Location</label>
								<div class="col-sm-8">
									<input type="text" name="location" id="location" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="locationError">
                                        <strong></strong>
                                    </span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input name="start_date" id="start_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
                                <div class="col-sm-8">
                                    <input name="end_date" id="end_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="end_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                        </div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Region HR</label>
                                <div class="col-sm-8">
                                   <select name="id_region" id="id_region" class="form-control form-control-sm select2" style="width: 100%;" readonly>
									</select>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Access Branch</label>
                                <div class="col-sm-8">
                                   <select name="id_branch" id="id_branch" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
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
                            <ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Participant <span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tab_rec_detail_content" style="font-size:12px;">
                               <div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail"><span class="fas fa-plus"></span> Add Participant</button>
											<button type="button" id="upload" class="new_upload pull-right btn btn-xs btn-primary" style="margin-right:10px;"><i class="fas fa-upload"></i> Import Participant</button>
                                        </div>
                                        <div class="col-md-12" style="overflow:auto;">
                                            <table id="table_rec_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Source Type</th>
                                                        <th>Participant Name</th>
                                                        <th style="white-space:nowrap;">DISC</th>
                                                        <th style="white-space:nowrap;">Papicostick</th>
                                                        <th style="white-space:nowrap;">Kraepelin</th>
                                                        <th style="white-space:nowrap;">BCT</th>
														<th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_rec_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_rec_detailError">
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
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			    <div style="display:none;">
					<table id="sample_table_rec">
						<tr id="">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
									<input name="participant[0][id_batch_participant]" id="participant_0_id_batch_participant" type="hidden" class="form-control form-control-sm id_batch_participant_input">		
									<select name="participant[0][source_type]" id="participant_0_source_type" class="form-control form-control-sm select2 source_type_input" style="width: 100%;"></select>
									<span class="invalid-feedback source_type_input_error" role="alert" id="participant_0_source_typeError">
										<strong></strong>
									</span>							
							</td>
							<td>
									<div class="is-loading">
											<select name="participant[0][id_candidate_emp]" id="participant_0_id_candidate_emp" class="form-control form-control-sm select2 id_candidate_emp_input" style="width: 100%;"></select>
											<span id="load_can_emp_0" class="spinner-border spinner-border-sm load_can_emp_input" style="display:none;"></span>
											<span class="invalid-feedback id_candidate_emp_input_error" role="alert" id="participant_0_id_candidate_empError">
												<strong></strong>
											</span>
									</div>
									<input name="participant[0][id_position_routing]" id="participant_0_id_position_routing" type="hidden" class="form-control form-control-sm id_position_routing_input">
									
							</td>
							<td style="vertical-align:middle;">
									<div align="center" id="participant_0_disc" class="disc_input"></div>
							</td>
							<td style="vertical-align:middle;">
									<div align="center" id="participant_0_papicostick" class="papicostick_input"></div>
							</td>
							<td style="vertical-align:middle;">
									<div align="center" id="participant_0_kraepelin" class="kraepelin_input"></div>
							</td>
							<td style="vertical-align:middle;">
									<div align="center" id="participant_0_bct" class="bct_input"></div>
							</td>
							<td>
								 <select name="participant[0][status]" id="participant_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;" readonly>
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
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
<div id="uploadModal" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1055;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Import Participant</h2>
                <button type="button" id="close_upload" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body card">
                <form id="upload_form">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Upload File :</label>
                    <div class="col-md-5">
                        <div class="custom-file">
                            <input type="file" name="attachment" class="custom-file-input" id="attachment">
                            <span class="invalid-feedback" role="alert" id="attachmentError"></span>
                            <label class="custom-file-label"><i>File (.xlsx / .xls)</i></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="submit_upload" class="btn btn-lg btn-success" ><i class="fas fa-upload"></i> Import</button>
                    </div>
                </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
	<div id="modal_second"></div>
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">List Participant</h5>
			<button type="button" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody" style="height:500px;overflow:auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default advclose" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection
@section('css')
<style type="text/css">  
	.checkfield, input[readonly]{
        pointer-events: none;
        touch-action: none;
		background: #e9ecef;
		box-shadow: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #e9ecef;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
	.no-wrap{
		white-space:nowrap;
	}
	.align-center{
		text-align:center;
		width:0px;
	}
	.modal{
		overflow:auto !important;
	}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_new = "";
let global_id_batch = "";
let global_id_rec_detail = 0;
let listParticipantInternal = [];
let listParticipantExternal = [];
let listParticipantInternalEdit = [];
let listParticipantExternalEdit = [];
//let global_id_region = "";

function load_detail(){
	 $("#contentBody").html('');
	 $('#fil_type').empty();
    $.ajax({
		url: "{{ route('batch.review') }}",
		method: "GET",
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(result){
			//alert("success"+result);
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});
}

const get_filter = async () => {
    try {
		$('#fil_type').select2({
			data:source_type
		});
    } 
	catch (error) {
    }	
}

$(function () {	

	$(document).on('click', '.new', function () {
			global_new = "new";
		//	$('#loader').removeClass('hidden');
            global_id_batch = "";
            $("#batchForm")[0].reset();
            $("#table_rec_body").html("");
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $(".feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#batchForm input").removeClass("is-invalid");
			$("#edit_button").css("display","none");
			getRegion().then(function(res) {
				$('#id_region').select2({
					data: res,
				});
			});
			
		//	start();
			
            $('#modal_form_batch').modal('show');
        });
		
		$(document).on('click', '.edit', function(){
		  let id_batch = $(this).attr('id');
		   $("#batchForm")[0].reset();
			$(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");
			$("#batchForm input").removeClass("is-invalid");
			$("#batchForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Batch");
			$("#save_button").css("display","none");
						
			$.ajax({
				url: "<?= url('recruitment/psychotest/master_batch/get_batch_edit') ?>",
				method: "GET",
				data: {id_batch: id_batch},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
					global_id_rec_detail = 0;
					if(response.participant.length == 0){
						$('#loader').addClass('hidden');
					}
					$.each(response.participant, function (i, item) {
                        $('#new_rec_detail').trigger('click');								
                    });	
					 $('#id_batch').val(response.id_batch).trigger('change');
					 $('#batch_name').val(response.batch_name).trigger('change');
					 $('#location').val(response.location).trigger('change');
					 $('#start_date').val(response.start_date).trigger('change');
					 $('#end_date').val(response.end_date).trigger('change');
					 $('#id_branch').val(response.id_branch).trigger('change');
					 $('#select2status').val(response.status).trigger('change');
					 getRegionEdit().then(function(res) {
						$('#id_region').select2({
							data: res,
						});
						$('#id_region').val(response.id_region).trigger('change');
					});
					
						 setTimeout(function () {
							 callerPosDetail();
	
							function get_posDetail_edit(){
								return new Promise((resolve,reject)=>{
									$('#table_rec_body tr').each(function (index) {							
										$(this).find('span.sn').html(index + 1);
										if(response.participant[index].summary == 1){
											$(this).find('.delete-record').css('display', 'none');
											$('#start_date').attr('readonly',true);
											$('#start_date').parent().children('span').children('button').attr('disabled', true);
										}									
										$(this).find('.id_batch_participant_input').val(response.participant[index].id_batch_participant);	
									//	console.log($(this).find('.source_type_input'));
										$(this).find('.source_type_input').val(response.participant[index].source_type).trigger('change',[true]);
										$(this).find('.source_type_input').attr('readonly',true);
										if(response.participant[index].status_disc == 1){
											$(this).find('.disc_input').html('<span class="badge badge-success" style="padding:5px;font-size:12px;">Completed</span>');
										}
										else{
											$(this).find('.disc_input').html('<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Started</span>');
										}										
										if(response.participant[index].status_papi == 1){
											$(this).find('.papicostick_input').html('<span class="badge badge-success" style="padding:5px;font-size:12px;">Completed</span>');
										}
										else{
											$(this).find('.papicostick_input').html('<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Started</span>');
										}
										if(response.participant[index].status_kraepelin == 1){
											$(this).find('.kraepelin_input').html('<span class="badge badge-success" style="padding:5px;font-size:12px;">Completed</span>');
										}
										else{
											$(this).find('.kraepelin_input').html('<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Started</span>');
										}
										if(response.participant[index].status_bct == 1){
											$(this).find('.bct_input').html('<span class="badge badge-success" style="padding:5px;font-size:12px;">Completed</span>');
										}
										else{
											$(this).find('.bct_input').html('<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Started</span>');
										}										
										$(this).find('.status_input').val(response.participant[index].status).trigger('change');
										$(this).find('.status_input').attr('readonly',true);;
									 });
									resolve();
								});
							}
							
							async function callerPosDetail(){
									await get_posDetail_edit();	
									setTimeout(function () {
										$('#table_rec_body tr').each(function (index) {		
											$(this).find('span.sn').html(index + 1);
											if(response.participant[index].source_type == 'Internal'){
												getParticipantInternalEdit(response.participant[index].id_employee,$(this).find('.id_candidate_emp_input'),response.participant[index].id_position_routing,$(this).find('.id_position_routing_input'),$(this).find('.load_can_emp_input')).then(function(res) {
											});													
											}
											else if(response.participant[index].source_type == 'External'){
													getParticipantExternalEdit(response.participant[index].id_candidate,$(this).find('.id_candidate_emp_input'),$(this).find('.load_can_emp_input')).then(function(res) {
													});	
											}	
											$(this).find('.id_candidate_emp_input').attr('readonly',true);
										 });	  
									}, 1000);
								}							  
						}, 500);												
                },
				complete: function(){
				//	$('#loader').addClass('hidden');
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
	
		$('#modal_form_batch').modal('show');
	});
	
		
		$(document).on('click', '#new_rec_detail', function () {
            var content = jQuery('#sample_table_rec tr'),
                    size = global_id_rec_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
			element.attr('id_record', size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_batch_participant_input').attr('id', 'participant_' + size + '_id_batch_participant');
            element.find('.id_batch_participant_input').attr('name', 'participant[' + size + '][id_batch_participant]');
			
			element.find('.source_type_input').attr('id', 'participant_' + size + '_source_type');
            element.find('.source_type_input').attr('name', 'participant[' + size + '][source_type]');
			
			element.find('.source_type_input_error').attr('id', 'participant_' + size + '_source_typeError');
            element.find('.source_type_input').prepend('<option selected></option>').select2({
				placeholder: "Source Type ...",
                data: source_type,
            });
			
			
			element.find('.id_candidate_emp_input').attr('id', 'participant_' + size + '_id_candidate_emp');
            element.find('.id_candidate_emp_input').attr('name', 'participant[' + size + '][id_candidate_emp]');
			element.find('.id_candidate_emp_input_error').attr('id', 'participant_' + size + '_id_candidate_empError');
			
			element.find('.load_can_emp_input').attr('id', 'load_can_emp_' +size);
			
			element.find('.id_position_routing_input').attr('id', 'participant_' + size + '_id_position_routing');
            element.find('.id_position_routing_input').attr('name', 'participant[' + size + '][id_position_routing]');
           
			element.find('.disc_input').attr('id', 'participant_' + size + '_disc');
			element.find('.papicostick_input').attr('id', 'participant_' + size + '_papicostick');
			element.find('.kraepelin_input').attr('id', 'participant_' + size + '_kraepelin');
			element.find('.bct_input').attr('id', 'participant_' + size + '_bct');
			
			element.find('.status_input').attr('id', 'participant_' + size + '_status');
            element.find('.status_input').attr('name', 'participant[' + size + '][status]');
			element.find('.status_input').select2();
						
            element.appendTo('#table_rec_body');
			 $('#table_rec_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
            $('#table_rec_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		
	var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('batch.save') }}";
			$(this).closest(".card").find("batchForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('batch.update') }}";
			$(this).closest(".card").find("batchForm").submit();
		  });	
	
	 $('#batchForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#batchForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");

            let dups = [];
            // TODO Iterate table for duplicates before submit
            // $('#table_rec_body').each(function (element) {
            //     console.log(element) 
            // })

                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: AjaxUrl,
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
                                $('#modal_form_batch').modal('hide');
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
                                text: 'Something went wrong! '+response.message,
                            });
                        }
                    },
					complete: function(){
						$('#loader').addClass('hidden');
					},
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            let duplicates = [];
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_rec_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
                                if(key.includes(".id_candidate_emp") && key.includes("participant.")) {
                                    let row = Number.parseInt(key.split(".")[1])+1;
                                    duplicates.push(row);
                                }
                            });
                            if(duplicates.length > 0) {
                                swal({
                                    icon: 'error',
                                    title: 'Oops...',
                                    dangerMode: true,
                                    text: `Participant No. ${duplicates} has a duplicate value!`
                                });
                            }
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

	$('#upload').click(function(){
        $('#uploadModal').modal('show');
        $('.modal-backdrop').css('z-index','1053');		
    });

    $('#submit_upload').click(function(){
        upload();
    });

    $('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html('<i>'+file.name+'</i>');
    });	
	
	$('#close_upload').click(function(){
        $('.modal-backdrop').css('z-index','1049');
        $('#modal_form_batch').css('overflow', 'none');
    });
	
    $('#batch_table').DataTable({
        processing: true,
		responsive: true,
        ajax: {
			url: "{{ route('batch.index') }}",
			data: {id_url: global_url_server},	
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#batch_table').DataTable().ajax.reload();
				}
			},
		rowCallback: function(row, data, index){
				if(access_create == 0){
					$(row).find('.new').css('display', 'none');
				}	
				if(access_edit == 0){
					$(row).find('.edit').css('display', 'none');
				}		
				if(access_delete == 0){
					$(row).find('.delete').css('display', 'none');
				}
				if(access_print == 0){
					$(row).find('.print').css('display', 'none');
				}	
			  },
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_batch',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'batch_code', name: 'batch_code', className: 'no-wrap'},          
            { data: 'batch_name', name: 'batch_name' },
            { data: 'location', name: 'location' },
            { data: 'count_participant', name: 'count_participant', className: 'align-center'},
            { data: 'start_date', name: 'start_date' },
            { data: 'end_date', name: 'end_date' },
            { data: 'status', name: 'status', className: 'align-center'},
           	{ data: 'action', name: 'action', orderable: false, className: 'align-center no-wrap', render: function ( data, type, row ) {	
					return data;
				} 
			},
        ],
		"fnInitComplete": function (oSettings) {
	   		$('#batch_table_wrapper .column-filter-widget:eq(8)').find("select option:contains('A')").attr('selected','selected').change();
		}
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

function upload(){
    var formData = new FormData($('#upload_form')[0]);
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "<?= url('recruitment/psychotest/master_batch/upload_review') ?>",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            if (response.status == 'true') {				
					$('#uploadModal').modal('hide');
					$('.modal-backdrop').css('z-index','1049');	
					if(response.data.length > 0){
						$.each(response.data, function (i, item) {
							$('#new_rec_detail').trigger('click');								
						});	
						$('#table_rec_body tr').each(function (index) {		
							$(this).find('span.sn').html(index + 1);
							$(this).find('.source_type_input').val(response.data[index].source_type).trigger('change',[true]);
							$(this).find('.id_candidate_emp_input').val(response.data[index].id_participant).trigger('change');
						});	
					}
		            $('#loader').addClass('hidden')
            } else {
                if(response.status == 'false'){
                    swal({
                        icon: 'error',
                        dangerMode: true,
                        content: {
                            element: "div",
                            attributes: {
                                innerText: response.message,
                                className: "swal-red",
                            },
                        },
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! '+response.message,
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                }
            }
        },
        error: function (response) {
            $('#loader').addClass('hidden')
            if (response.status === 422) {
                let errors = response.responseJSON;
                let err = "";
                Object.keys(errors).forEach(function (key) {
                    var key_temp = key.replaceAll(".", "_");
                    $("#" + key_temp).addClass("is-invalid");
                    $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
                    var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
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
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
            else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+response.statusText,
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
        }
    });                 
}   


function refresh_data() {
	source_type = [
			{
				id: 'Internal',
				text: 'Internal'
			},
			{
				id: 'External',
				text: 'External'
			},
		];
	
	getParticipantInternal().then(function(res) {
		listParticipantInternal = res;
	});
	getParticipantExternal().then(function(res) {
		listParticipantExternal = res;
	});
	
	getBranch().then(function(res) {
		$('#id_branch').select2({
			data: res,
		});
	});
			
	$('#start_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	$('#end_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	
	$('#select2status').select2({width:'100%'});
		
}

const getRegion = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_region_batch') ?>',
            dataType: 'json',
            success: function (res) {
			//	console.log(res);
            }
        });
        return result;
    } catch (error) {
        getRegion();
    }
}

const getRegionEdit = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_region_batch_edit') ?>',
            dataType: 'json',
            success: function (res) {
			//	console.log(res);
            }
        });
        return result;
    } catch (error) {
        getRegionEdit();
    }
}

const getBranch = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_branch_batch') ?>',
			data: {id_url: global_url_server},
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
        getBranch();
    }
}

/*
const get_participant = async (res) => {
	listParticipant = [];
	$.each(res, function (i, item) {
		listParticipant.push({'id':item.id, 'text':item.text, 'id_position_routing':item.id_position_routing});
	});
}

const fillName = async (idRecord, id_source_type, id_participant=null, score=null) => {
    let result;
    try {
        let thisIdParticipant = `#participant_${idRecord}_id_candidate_emp`;
        let thisIdSource = `#participant_${idRecord}_source_type`;

        $(thisIdParticipant).html('');
        $(thisIdSource).val(id_source_type).trigger('change', ['trigger']);
        if(id_source_type != null){
            $(thisIdParticipant).select2({
                placeholder: "Select Participant",
                allowClear: true,
                data: listParticipant
            });
        }
        if(id_participant != null){
            $(thisIdParticipant).val(id_participant).trigger('change', ['trigger']);
        } 
		if(score == null){
            score = $(thisIdParticipant).select2('data')[0].id_position_routing;
        }
		 fillScore(idRecord, id_participant, score);
    } catch (error) {
        
    }
}
*/
const fillScore = async (idRecord, id_participant, score=null) => {
    let result;
    try {
        let thisScore = `#participant_${idRecord}_id_position_routing`;
        $(thisScore).val('');
        if(score != null){
            $(thisScore).val(score);
        }
    } catch (error) {
        
    }
}

const getParticipantInternal = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_participant') ?>',
           data: {
					id_source_type:'Internal',
					id_url: global_url_server
			},
            dataType: 'json',
            success: function (res) {
			//	console.log(res);
            }
        });
        return result;
    } catch (error) {
        getParticipantInternal();
    }
}

const getParticipantExternal = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_participant') ?>',
            data: {
					id_source_type:'External',
					id_url: global_url_server
			},
            dataType: 'json',
            success: function (res) {
			//	console.log(res);
            }
        });
        return result;
    } catch (error) {
        getParticipantExternal();
    }
}

const getParticipantInternalEdit = async (id_employee,element,id_position_routing,element_routing,element_load) => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_participant_edit') ?>',
            data: {
				id_source_type:'Internal',
				id_employee:id_employee
			},
            dataType: 'json',
			beforeSend: function () {
				element_load.each(function (i, obj) {
					$('#' + obj.id).show();
				});
			},
            success: function (res) {
				element.select2({
					placeholder: "Select Participant",
					allowClear: true,
					data: res
				});
				element.val(id_employee).trigger('change');	
				element_routing.val(id_position_routing).trigger('change');
            },
			complete: function(){
				element_load.each(function (i, obj) {
					$('#' + obj.id).hide();
				});
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
      //  getParticipantInternalEdit();
    }
}

const getParticipantExternalEdit = async (id_candidate,element,element_load) => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_participant_edit') ?>',
            data: {
				id_source_type:'External',
				id_candidate:id_candidate
			},
            dataType: 'json',
			beforeSend: function () {
				element_load.each(function (i, obj) {
					$('#' + obj.id).show();
				});
			},
            success: function (res) {
				element.select2({
					placeholder: "Select Participant",
					allowClear: true,
					data: res
				});
				element.val(id_candidate).trigger('change');	
            },
			complete: function(){
				element_load.each(function (i, obj) {
					$('#' + obj.id).hide();
				});
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
    //    getParticipantExternalEdit();
    }
}

$(document).on('change', '.source_type_input', function (event, triggered) {
    let idRecord = $(this).parent().parent().attr('id_record');
//    let id_source_type = null;
	let thisIdParticipant = `#participant_${idRecord}_id_candidate_emp`;
	let valueScore = null;
	
	$(thisIdParticipant).empty();
    if (triggered != 'trigger'){ // change by human
        if($(this).select2('data').length > 0){						
			if($(this).val() == 'Internal'){
			//	if(global_new == 'new'){
					$(thisIdParticipant).select2({
						placeholder: "Select Participant",
						allowClear: true,
						data: listParticipantInternal
					});				
			//	}
				valueScore = $(thisIdParticipant).select2('data')[0].id_position_routing;
				id_participant = $(thisIdParticipant).select2('data')[0].id;
			}
			else if($(this).val() == 'External'){
					$(thisIdParticipant).select2({
						placeholder: "Select Participant",
						allowClear: true,
						data: listParticipantExternal
					});	

			//	id_participant = $(thisIdParticipant).select2('data')[0].id;
				
			}
			
		//	fillScore(idRecord, id_participant, valueScore);
			
        //    id_source_type = $(this).val();
		//	 getParticipant(id_source_type).then(function(res) {
		//		get_participant(res).then(function(result) {
		//	        fillName(idRecord, id_source_type);
		//		});
		//	});	
        }
    }
});


$(document).on('change', '.id_candidate_emp_input', function (e, triggered) {
	let id_participant = $(this).val();
    let idRecord = $(this).parent().parent().attr('id_record');
    let valueScore = null;
//	console.log($(this).select2('data'));
    if (triggered != 'trigger'){ // change by human
        if($(this).select2('data').length > 0){
            valueScore = $(this).select2('data')[0].id_position_routing;			
        }
        fillScore(idRecord, id_participant, valueScore);
    }
});

$(document).on('click', '.delete', function (event) {
	id_batch = $(this).attr('id');
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
			   url:"master_batch/destroy/"+id_batch,
			   success:function(response)
			   {
				setTimeout(function(){				 
					if (response.status == 'true') {
							$('#modal_form_batch').modal('hide');
							swal({
								icon: 'success',
								title: 'Data Deleted!',
								text: response.message
							}).then(function(){ 
							   $('#confirmModal').modal('hide');
								$('#batch_table').DataTable().ajax.reload();		
							   }
							);
					} else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! '+response.message,
						});
					}
				}, 50);
			   },
			   error: function (response) {                      
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
});
</script>
@endsection