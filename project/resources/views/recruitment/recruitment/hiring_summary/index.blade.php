@extends('adminlte::page')
@section('title', 'Hiring Summary')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
			 <div class="card-header">
                <h5 class="card-title">Hiring Summary</h5>
            </div>
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="hiring_request_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th data-priority="2">No</th>
						<th>Reference Number</th>
						<th data-priority="3">Request By</th>
						<th>Recruitment Source</th>
						<th data-priority="9">Request Type</th>
						<th data-priority="5">Request Position</th>
						<th data-priority="10">Region</th>
						<th data-priority="8">Branch</th>
						<th data-priority="7">Request Total</th>
						<th data-priority="6">FPK Status</th>
						<th data-priority="4">Web Posting</th>
						<th data-priority="1" style="text-align:center;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_hiring"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="hiringForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Hiring Request</h5>
                    <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request By</label>
                                <div class="col-sm-8">
									<input name="id_hiring_request_header" id="id_hiring_request_header" type="hidden">	
									<input type="text" id="emp_name" class="form-control form-control-sm" readonly>	
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Position</label>
								<div class="col-sm-8">
									<input type="text" id="emp_pos_req" class="form-control form-control-sm" readonly>
								</div>
							</div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Recruitment Source</label>
                                <div class="col-sm-8">
									<input type="text" id="rec_source" class="form-control form-control-sm" readonly>
                                </div>
                            </div>		
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Type</label>
                                <div class="col-sm-8">
									<input type="text" id="req_type" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Request Reason (Notes)</label>
								<div class="col-sm-8">
									<textarea id="reason_notes" class="form-control form-control-sm" readonly></textarea>
								</div>
							</div>
							<div class="row" style="margin-top:4px;">
                                <label class="col-sm-4 col-form-label">Company Type</label>
                                <div class="col-sm-8">
									<input type="text" id="com_type" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Position</label>
                                <div class="col-sm-8">
									<input type="text" id="pos_req" class="form-control form-control-sm" readonly>								
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                                <div class="col-sm-8">
									<input type="text" id="branch" class="form-control form-control-sm" readonly>								
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Grade</label>
								<div class="col-sm-8">
									<input type="text" id="grade_req" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">SLA</label>
								<div class="col-sm-2">
									<input type="text" id="sla" class="form-control form-control-sm" readonly>
								</div>
								<label class="col-sm-6 col-form-label">Day(s)</label>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Target Date</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" id="target_date" class="form-control form-control-sm" readonly>
										<div class="input-group-append">
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
									</div>
								</div>
							</div>			
							<div style="border:2px solid #28a745;padding:5px;border-radius:5px;margin-top:8px;">
								<div class="row">
									<label class="col-sm-4 col-form-label">Publish Web </label>
									<div class="col-sm-2">
										<input type="checkbox" name="is_web_posting" id="is_web_posting" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Request Status</label>
									<div class="col-sm-8">
										<select name="req_status" id="req_status" class="form-control form-control-sm select2" style="width: 100%;">
										</select>  									
									</div>
								</div>
                            </div>
							
                        </div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Skill Notes</label>
								<div class="col-sm-8">
									<textarea id="skill_notes" name="skill_notes" class="form-control form-control-sm" readonly></textarea>
								</div>
							</div>
							<div class="row" style="margin-top:4px;">
                                <label class="col-sm-4 col-form-label">Contract Duration</label>
									<div class="col-sm-2" style="float:left;">
										<input type="text" id="pkwt_duration" class="form-control form-control-sm" readonly>	
									</div>
									<label class="col-sm-6 col-form-label">Month(s)</label>									
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
										<input type="text" id="effective_date" class="form-control form-control-sm" readonly>
										<div class="input-group-append">
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
									</div>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">HR Recruitment</label>
								<div class="col-sm-8">
									<select name="cc_email[]" id="cc_email" class="form-control form-control-sm select2" data-placeholder="Select HR ..." style="width: 100%;" multiple="multiple">
                                    </select>
									<span class="invalid-feedback" role="alert" id="cc_emailError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Hierarchy Approval</label>
								<div class="col-sm-8">
									<input type="text" id="app_hierarchy" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Approved By</label>
								<div class="col-sm-8">
									<input type="text" id="approval_name" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Status</label>
                                <div class="col-sm-8">
									<input type="text" id="desc_app_status" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Request Date</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" id="request_date" class="form-control form-control-sm" readonly>
										<div class="input-group-append">
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Approval Date</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" id="approval_date" class="form-control form-control-sm" readonly>
										<div class="input-group-append">
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Enable Recommendation Employee</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" id="have_recommended_employee" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;" disabled>
                                </div>
                            </div>							
						</div>
						<div class="col-md-12" style="margin-top:10px;">
							<div class="row">
								<label class="col-sm-12 col-form-label">Job Description</label>
								<div class="col-sm-12">
									<textarea id="job_description_detail" class="form-control summernote" disabled></textarea>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-12 col-form-label">Requirement</label>
								<div class="col-sm-12">
									<textarea id="skill_requirement" class="form-control summernote" disabled></textarea>
								</div>
							</div>
						</div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Position Detail<span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item" id="tab_reco">
                                    <a class="nav-link" id="link_tab_rec-reco" data-toggle="pill" href="#rec-reco" role="tab" aria-controls="link_tab_rec-reco" aria-selected="true">Employee Recommendation<span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
                               <div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail"><span class="fas fa-plus"></span> Add Position Detail</button>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_rec_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Position Detail</th>
                                                        <th style="white-space:nowrap;">Employee Replacement</th>
                                                        <th style="white-space:nowrap;">Location</th>
                                                        <th style="white-space:nowrap;">Status Hiring</th>
                                                        <th style="width:250px;">Notes</th>
                                                        <th style="white-space:nowrap;">Update Date</th>
                                                        <th style="white-space:nowrap;">Candidate Name</th>
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
                                <div class="tab-pane fade" id="rec-reco" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_reco"><span class="fas fa-plus"></span> Add Employee Recommendation</button>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_rec_reco" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Employee Name</th>
                                                        <th style="white-space:nowrap;">Position Detail</th>
                                                        <th style="white-space:nowrap;">Location</th>
                                                        <th style="white-space:nowrap;">Company</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_rec_reco_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_rec_recoError">
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
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			    <div style="display:none;">
					<table id="sample_table_rec">
						<tr id="">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
									<input id="hiring_0_id_hiring_request_detail" type="hidden" class="form-control form-control-sm id_hiring_request_detail_input">	
									<div id="hiring_0_pos_detail" class="pos_detail_input"></div>
							</td>
							<td>	
									<div align="center" id="hiring_0_replace_name" class="replace_name_input"></div>
							</td>
							<td>
									<div id="hiring_0_loc_detail" class="loc_detail_input"></div>
							</td>
							<td>
									<div align="center" id="hiring_0_status_hiring" class="status_hiring_input"></div>
							</td>
							<td>
									<p align="justify" id="hiring_0_notes" class="notes_input"></p>
							</td>
							<td>
									<div align="center" id="hiring_0_date_notes" class="date_notes_input"></div>
							</td>
							<td>
									<div align="center" id="hiring_0_name_candidate" class="name_candidate_input"></div>
							</td>				
						</tr>
					</table>
					<table id="sample_table_rec_reco">
						<tr id="">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
									<input id="reco_0_id_hiring_request_recommendation" type="hidden" class="form-control form-control-sm id_hiring_request_recommendation_input">
									<div id="reco_0_emp_reco" class="emp_reco_input"></div>
							</td>
							<td>
									<div id="reco_0_pos_reco" class="pos_reco_input"></div>
							</td>
							<td>
									<div id="reco_0_loc_reco" class="loc_reco_input"></div>
							</td>
							<td>
									<div id="reco_0_com_reco" class="com_reco_input"></div>
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
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_rec_detail = 0;
let global_id_rec_reco = 0;

$(function () {	
	
	$(document).on('click', '.edit', function(){
		  let id_hiring_request_header = $(this).attr('id');
		   $("#hiringForm")[0].reset();
			$(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");
			$("#hiringForm input").removeClass("is-invalid");
			$("#hiringForm .modal-title").html("<span class='fas fa-edit'></span> Edit Hiring Request");
			$('#req_status').select2({
				 data: req_status,
			});
			$.ajax({
				url: "<?= url('recruitment/recruitment/hiring_summary/get_summary_edit') ?>",
				method: "GET",
				data: {id_hiring_request_header: id_hiring_request_header,
						id_url: global_url_server},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
				//	console.log(response);
					global_id_rec_detail = 0;
					$.each(response.hiring, function (i, item) {
                        $('#new_rec_detail').trigger('click');
                    });
					
					if(response.code_app_status == 'Approved'){		
					   $('#id_hiring_request_header').val(response.id_hiring_request_header).trigger('change');		
					   
					   $('#emp_name').val(response.emp_name).trigger('change');
					   $('#emp_pos_req').val(response.emp_pos_req).trigger('change');
					   $('#rec_source').val(response.recruitment_source).trigger('change');
					   $('#req_type').val(response.request_type).trigger('change');
					   $('#com_type').val(response.assigned_to).trigger('change');					   
					   $('#reason_notes').val(response.reason_notes).trigger('change');
					   $('#pos_req').val(response.pos_req).trigger('change');
					   $('#branch').val(response.branch).trigger('change');
					   $('#grade_req').val(response.grade_req).trigger('change');
					   $('#sla').val(response.sla).trigger('change');
					   $("#target_date").val(response.target_date).trigger('change');					   
					   $('#skill_notes').val(response.skill_notes).trigger('change');
					   $('#pkwt_duration').val(response.pkwt_duration).trigger('change');  
					   $('#effective_date').val(response.effective_date).trigger('change');
					   $('#cc_email').val(response.cc_email).trigger('change');
					   $('#app_hierarchy').val(response.app_hierarchy).trigger('change');
					   $('#approval_name').val(response.approval_name).trigger('change');
					   $('#desc_app_status').val(response.desc_app_status).trigger('change');
					   $("#request_date").val(response.request_date).trigger('change');
					   $("#approval_date").val(response.approval_date).trigger('change');
					   $("#req_status").val(response.hiring_request_status).trigger('change');
					   $('#job_description_detail').summernote("code", response.job_description_detail);
					   $('#job_description_detail').summernote('disable');
					   $('#skill_requirement').summernote("code", response.skill_requirement);
					   $('#skill_requirement').summernote('disable');
					   
					   if(response.access == true){
						   $('#is_web_posting').attr("readonly", true);
						   $('#is_web_posting').prop("readonly", true);
					   }
					  
					   if(response.is_web_posting == true){
							$('#is_web_posting').prop('checked', true);
						}
						else{
							$('#is_web_posting').prop('checked', false);
						}
						
					   if(response.hiring_request_status == 'D'){
							$("#req_status").attr("readonly", true);
							$("#req_status").prop("readonly", true);
					   }
					   
						$("#new_rec_detail").hide();
						$("#new_rec_reco").hide();		

						
						 setTimeout(function () {
							$('#table_rec_body tr').each(function (index) {
								$(this).find('span.sn').html(index + 1);
								$(this).find('.id_hiring_request_detail_input').val(response.hiring[index].id_hiring_request_detail);
								$(this).find('.pos_detail_input').html(response.hiring[index].pos_detail);
								$(this).find('.replace_name_input').html(response.hiring[index].replace_name);
								$(this).find('.loc_detail_input').html(response.hiring[index].loc_detail);
								
								if(response.hiring[index].hiring_status == 'Hiring'){
									$(this).find('.status_hiring_input').html('<span class="badge badge-warning" style="padding:5px;font-size:12px;">On Progress</span>');
								}
								else if(response.hiring[index].hiring_status == 'Hired'){
									$(this).find('.status_hiring_input').html('<span class="badge badge-success" style="padding:5px;font-size:12px;">Hired</span>');
								}	
								$(this).find('.notes_input').html(response.hiring[index].notes);
								$(this).find('.date_notes_input').html(response.hiring[index].update_date_notes);
								$(this).find('.name_candidate_input').html(response.hiring[index].name_candidate);
							 });	  
						}, 1000);
								
					   if(response.have_recommended_employee == true){
							$('#have_recommended_employee').prop('checked', true).change();
							global_id_rec_reco = 0;
							$.each(response.reco, function (i, item) {
								$('#new_rec_reco').trigger('click');
							});
							
							setTimeout(function () {
								$('#table_rec_reco_body tr').each(function (index) {
									$(this).find('span.sn').html(index + 1);
									$(this).find('.id_hiring_request_recommendation_input').val(response.reco[index].id_hiring_request_recommendation);
									$(this).find('.emp_reco_input').html(response.reco[index].emp_reco);
									$(this).find('.pos_reco_input').html(response.reco[index].pos_reco);
									$(this).find('.loc_reco_input').html(response.reco[index].loc_reco);
									$(this).find('.com_reco_input').html(response.reco[index].com_reco);
								 });	  
							}, 1000);
					   }
					   else {
							$('#link_tab_rec-reco').removeClass('active');
							$('#rec-reco').hide();
							$('#tab_reco').hide();							
						}
					}		  
                },
				complete: function(){
					$('#loader').addClass('hidden');
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
	
		$('#modal_form_hiring').modal('show');
	});
	
			
	$(document).on('click', '#new_rec_detail', function () {
            var content = jQuery('#sample_table_rec tr'),
                    size = global_id_rec_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.id_hiring_request_detail_input').attr('id', 'hiring_' + size + '_id_hiring_request_detail');			
			element.find('.pos_detail_input').attr('id', 'hiring_' + size + '_pos_detail');
			element.find('.replace_name_input').attr('id', 'hiring_' + size + '_replace_name');
			element.find('.loc_detail_input').attr('id', 'hiring_' + size + '_loc_detail');
			element.find('.status_hiring_input').attr('id', 'hiring_' + size + '_status_hiring');
			element.find('.notes_input').attr('id', 'hiring_' + size + '_notes');
			element.find('.date_notes_input').attr('id', 'hiring_' + size + '_date_notes');
			element.find('.name_candidate_input').attr('id', 'hiring_' + size + '_name_candidate');
						
            element.appendTo('#table_rec_body');
			 $('#table_rec_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '#new_rec_reco', function () {
            var content = jQuery('#sample_table_rec_reco tr'),
                    size = global_id_rec_reco++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_reco-'+size);
            element.find('.id_hiring_request_recommendation_input').attr('id', 'reco_' + size + '_id_hiring_request_recommendation');			
			element.find('.emp_reco_input').attr('id', 'reco_' + size + '_emp_reco');
			element.find('.pos_reco_input').attr('id', 'reco_' + size + '_pos_reco');
			element.find('.loc_reco_input').attr('id', 'reco_' + size + '_loc_reco');
			element.find('.com_reco_input').attr('id', 'reco_' + size + '_com_reco');
									
            element.appendTo('#table_rec_reco_body');
			 $('#table_rec_reco_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
	
	 $('#hiringForm').submit(function (e) {
            e.preventDefault();          
            let formData = $(this).serializeArray();			
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('summary.update_summary') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							$('#modal_form_hiring').modal('hide');
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
                });
            
        });

});		
		
$(document).ready(function(){
	get_hr_email();
	
	req_status = [
			{
				id: 'O',
				text: 'Open'
			},
			{
				id: 'C',
				text: 'Cancel'
			},
			{
				id: 'P',
				text: 'Partial Close'
			},
			{
				id: 'D',
				text: 'Close'
			},
		];
	
    $('#hiring_request_table').DataTable({
        processing: true,
		responsive: true,
        ajax: {
			url: "{{ route('summary.index') }}",
		//	url: "<?= url('recruitment/recruitment/hiring_summary/') . '?id_url=' ?>" + global_url_server,
			data: {id_url: global_url_server},	
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#hiring_request_table').DataTable().ajax.reload();
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

				if(data['code_app_status'] == 'Approved'){
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.delete').css('display', 'none');
				}
			  },
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_hiring_request_header',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'reference_number', name: 'reference_number', className: 'no-wrap'},          
            { data: 'emp_name', name: 'emp_name' },
            { data: 'recruitment_source', name: 'recruitment_source' },
            { data: 'request_type', name: 'request_type' },
            { data: 'route_name', name: 'route_name' },
            { data: 'region', name: 'region' },
            { data: 'branch', name: 'branch' },
            { data: 'count_req', name: 'count_req', className: 'text-center'},
            { data: 'hiring_request_status', name: 'hiring_request_status', className: 'text-center', render: function ( data, type, row ) {
					if(row.hiring_request_status == 'D'){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Close</span>';
					}
					else if(row.hiring_request_status == 'C'){
							return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Cancel</span>';
					}
					else if(row.hiring_request_status == 'P'){
							return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">Partial Close</span>';
					}
					else if(row.hiring_request_status == 'O'){
							return '<span  class="badge badge-info" style="padding:5px;font-size:12px;">Open</span>';
					}
				} 
			},
            { data: 'is_web_posting', name: 'is_web_posting', className: 'text-center', render: function ( data, type, row ) {	
					if(row.is_web_posting == true){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">YES</span>';
					}
					else{
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
					}
				}
			},
			{ data: 'action', name: 'action', orderable: false, className: 'no-wrap', render: function ( data, type, row ) {	
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
});
function get_hr_email(){		
		$.getJSON('<?= url('recruitment/recruitment/hiring_request/get_hr_email') ?>', function (data) {
			$('#cc_email').select2({
				data: data,
				disabled:true,
				});
		}).fail(function (data) { // Call failed
            get_hr_email();
		});					
}
</script>
@endsection