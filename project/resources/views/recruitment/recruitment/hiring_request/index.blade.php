@extends('adminlte::page')
@section('title', 'Hiring Request')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Form Hiring Request</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Hiring Request</button>
                </div>
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
						<th>No</th>
						<th data-priority="2">Reference Number</th>
						<th data-priority="3">Request By</th>
						<th>Recruitment Source</th>
						<th data-priority="8">Request Type</th>
						<th data-priority="5">Request Position</th>
						<th data-priority="10">Region</th>
						<th data-priority="9">Branch</th>
						<th data-priority="7">Request Total</th>
						<th data-priority="6">FPK Status</th>
						<th data-priority="4">Approval Status</th>
						<th data-priority="11">Approval By</th>
						<th>Note Revised</th>
						<th>Note Rejected</th>
						<th data-priority="1" style="text-align:center;white-space:nowrap;" width=150>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_hiring"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="card modal-content">
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
                                    <select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Position</label>
								<div class="col-sm-8">
									<input type="hidden" id="id_position_request" name="id_position_request">
									<input type="hidden" id="id_location_request" name="id_location_request">
									<input type="text" id="by_pos" class="form-control form-control-sm" readonly>
								</div>
							</div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Recruitment Source</label>
                                <div class="col-sm-8">
									<select name="rec_source" id="rec_source" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="rec_sourceError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>		
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Type</label>
                                <div class="col-sm-8">
									<select name="req_type" id="req_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="req_typeError">
                                        <strong></strong>
                                    </span>   									
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Request Reason (Notes)</label>
								<div class="col-sm-8">
									<textarea id="reason_notes" name="reason_notes" class="form-control form-control-sm"></textarea>
									 <span class="invalid-feedback" role="alert" id="reason_notesError">
                                        <strong></strong>
                                    </span>
								</div>
							</div>
							<div class="row" style="margin-top:4px;">
                                <label class="col-sm-4 col-form-label">Company Type</label>
                                <div class="col-sm-8">
									<select name="com_type" id="com_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="com_typeError">
                                        <strong></strong>
                                    </span>   									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Position</label>
                                <div class="col-sm-8">
									<select name="pos_req" id="pos_req" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
									<input type="hidden" id="pos_length" name="pos_length">
                                    <span class="invalid-feedback" role="alert" id="pos_reqError">
                                        <strong></strong>
                                    </span>   									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<select name="id_branch" id="id_branch" class="form-control form-control-sm select2" style="width: 100%;">
										</select>		
										<span id="load_id_branch" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
																									
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
							<div id="target" class="row" style="display:none;">
								<label class="col-sm-4 col-form-label">Target Date</label>
								<div id="tar_date" class="col-sm-8">
									<input type="text" id="target_date" class="form-control form-control-sm" readonly>
								</div>
							</div>
							
                        </div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Skill Notes</label>
								<div class="col-sm-8">
									<textarea id="skill_notes" name="skill_notes" class="form-control form-control-sm"></textarea>
								</div>
							</div>
							<div class="row" style="margin-top:4px;">
                                <label class="col-sm-4 col-form-label">Contract Duration</label>
									<div class="col-sm-2" style="float:left;">
										<select name="pkwt_duration" id="pkwt_duration" class="form-control form-control-sm select2" style="width:100%;">
										</select>									   
									</div>
									<label class="col-sm-6 col-form-label">Month(s)</label>										
										<label class="col-sm-4" style="margin-top:-8px;"></label>
										<span class="col-sm-8 invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="pkwt_durationError">
												<strong></strong>
										</span>			
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
									<div class="input-group">
										<input type="text" name="effective_date" id="effective_date" class="form-control form-control-sm" readonly>
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
                                    <select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<!--div class="row">
                                <label class="col-sm-4 col-form-label">Approved By</label>
								<div class="col-sm-8">
                                    <select name="id_approval_request" id="id_approval_request" class="form-control form-control-sm select2" style="width: 100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_approval_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div -->
							<div class="row">
								<label class="col-sm-4 col-form-label">Approved By</label>
								<div class="col-sm-8">
									<div class="is-loading">
										<input type="hidden" id="id_approval_request" name="id_approval_request" class="form-control form-control-sm">
										<input type="text" id="approval_name" class="form-control form-control-sm" readonly>
										<span id="load_old_approval_name" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
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
							<div id="app_date" class="row" style="display:none;">
								<label class="col-sm-4 col-form-label">Approval Date</label>
								<div id="appr_date" class="col-sm-8">
									<input type="text" id="approval_date" class="form-control form-control-sm" readonly>
								</div>
							</div>
							<div id="enable_reco" class="row">
                                <label class="col-sm-4 col-form-label">Enable Recommendation Employee</label>
                                <div class="col-sm-2">
                                    <input type="checkbox" name="have_recommended_employee" id="have_recommended_employee" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="have_recommended_employeeError">
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
                                    <a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Position Detail <span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item" id="tab_reco">
                                    <a class="nav-link" id="link_tab_rec-reco" data-toggle="pill" href="#rec-reco" role="tab" aria-controls="link_tab_rec-reco" aria-selected="true">Employee Recommendation <span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-status" data-toggle="pill" href="#menu-status" role="tab" aria-controls="link_tab_menu-status" aria-selected="true">List Approval Status<span class="error-tab text-red"></span></a>
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
                                                        <th style="white-space:nowrap;">Direct Supervisor</th>
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
								<div class="tab-pane fade" style="font-size:14px" id="menu-status" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">                                      
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_approve_status_detail" class="responsive table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
														<th style="white-space:nowrap;">No.</th>
														<th style="white-space:nowrap;">Sequence</th>
														<th data-priority="1" style="white-space:nowrap;">Approval Name</th>
														<th data-priority="3" style="white-space:nowrap;">Approval Status</th>                                                       
														<th data-priority="2" align="center" style="width:50px;">Approval Execute</th>
													</tr>
                                                </thead>                                            
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_status_detailError">
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
					<button type="submit" class="btn btn-info btn-sm " id="save_and_submit"><i class="fas fa-paper-plane"></i></button>&nbsp;
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save as Draft</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			    <div style="display:none;">
					<table id="sample_table_rec">
						<tr id="">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
									<input name="hiring[0][id_hiring_request_detail]" id="hiring_0_id_hiring_request_detail" type="hidden" class="form-control form-control-sm id_hiring_request_detail_input">
									<div class="is-loading">
										<select name="hiring[0][id_position_detail_request]" id="hiring_0_id_position_detail_request" class="form-control form-control-sm select2 id_position_detail_request_input" style="width: 100%;"></select>
										<span id="load_pos_detail_0" class="spinner-border spinner-border-sm load_pos_detail_input" style="display:none;"></span>
										
										<span class="invalid-feedback id_position_detail_request_input_error" role="alert" id="hiring_0_id_position_detail_requestError">
											<strong></strong>
										</span>
									</div>
																								
							</td>
							<td>
									<input type="hidden" name="hiring[0][id_employee_replacement]" id="hiring_0_id_employee_replacement" class="form-control form-control-sm id_employee_replacement_input">
									<input type="text" id="hiring_0_replace_name" class="form-control form-control-sm replace_name_input" readonly>
							</td>
							<td>
									<input type="hidden" name="hiring[0][id_location]" id="hiring_0_id_location" class="form-control form-control-sm id_location_input">
									<input type="text" id="hiring_0_location" class="form-control form-control-sm location_input" readonly>
							</td>
							<td>
									<input type="text" id="hiring_0_direct" class="form-control form-control-sm direct_input" readonly>
							</td>				
						<td>
						<center>
							<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
					<table id="sample_table_rec_reco">
						<tr id="">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
									<input name="reco[0][id_hiring_request_recommendation]" id="reco_0_id_hiring_request_recommendation" type="hidden" class="form-control form-control-sm id_hiring_request_recommendation_input">		
									<select name="reco[0][id_employee_recommendation]" id="reco_0_id_employee_recommendation" class="form-control form-control-sm select2 id_employee_recommendation_input" style="width: 100%;"></select>
									<span class="invalid-feedback id_employee_recommendation_input_error" role="alert" id="reco_0_id_employee_recommendationError">
										<strong></strong>
									</span>							
							</td>
							<td>
									<input type="hidden" name="reco[0][id_position_detail_reco]" id="reco_0_id_position_detail_reco" class="form-control form-control-sm id_position_detail_reco_input">
									<input type="text" id="reco_0_position_name" class="form-control form-control-sm position_name_input" readonly>
							</td>
							<td>
									<input type="hidden" name="reco[0][id_location_reco]" id="reco_0_id_location_reco" class="form-control form-control-sm id_location_reco_input">
									<input type="text" id="reco_0_location_reco" class="form-control form-control-sm location_reco_input" readonly>
							</td>
							<td>
									<input type="text" id="reco_0_company_reco" class="form-control form-control-sm company_reco_input" readonly>
							</td>				
						<td>
						<center>
							<button type="button" class="delete-reco btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
				</div>
       

    </div>
</div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
			<div class="modal-header">
				<h5 class="detail_tracking modal-title">Info Tracking Candidate</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
			<button onclick="return false;" class="btn btn-default pull-left advanced_can_group">Advanced Search</button><br><br>
			<div id="table_div_info"></div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
	td.text-middle{
		vertical-align:middle;
		text-align:center;
	}
	th.th-text-score{
		width:10px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_new = "";
let global_view = "";
let global_id_hiring_request_header = "";
let global_id_rec_detail = 0;
let global_id_rec_reco = 0;
let global_route_name = "";
let global_id_routing = 0;
let global_id_pos_detail = 0;
let global_job_class_group = "";
let global_com_type = "";
let global_req_type = "";
let global_contract_duration = [];
let global_pos_detail = [];
let global_emp_reco = [];
let global_id_employee = 0;
let global_code_status = "";
let global_approval_date = "";
let global_target_date = "";

let currentPositionRequest = [];
let currentBranch = [];
let currentHierarchyApproval = [];
let currentApprovalRequest = [];

$(function () {	

	$(document).on('click', '.new', function () {
			global_new = "new";
			$('#loader').removeClass('hidden');
            global_id_hiring_request_header = "";
            $("#hiringForm")[0].reset();
            $("#rec_source").empty();
        //    $("#req_type").empty();
            $("#hiring_request_status").empty();
        //    $("#com_type").empty();
            $("#table_rec_body").html("");
            $("#hiringForm .modal-title").html("<span class='fas fa-plus'></span> Form Hiring Request");
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $(".feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#hiringForm input").removeClass("is-invalid");
			$('#tab_reco').css('display','none');
			$("#edit_button").css("display","none");
			$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').addClass('addForm');
			start();
			$('#rec_source').select2({
				 data: rec_source,
			}).on('change', function (e) {
				if($(this).select2('data')[0].id == 'EXTERNAL'){
					$('#enable_reco').hide();
					$('#have_recommended_employee').prop('checked', false).change();
				}
				else{
					$('#enable_reco').show();
					get_reco_check();
				}
			}).trigger('change');
            $('#effective_date').val(moment().format("YYYY-MM-DD"));
            $('#modal_form_hiring').modal('show');
        });
		
	$(document).on('click', '.edit', function(){
		  let id_hiring_request_header = $(this).attr('id');
		   $("#hiringForm")[0].reset();
			$(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");
			$("#hiringForm input").removeClass("is-invalid");
			$("#save_button").css("display","none");
		//	global_param = response.id_position_routing_request;
		//	start();
			$('#rec_source').select2({
				 data: rec_source,
			}).on('change', function (e) {
				if($(this).select2('data')[0].id == 'EXTERNAL'){
					$('#enable_reco').hide();
					$('#have_recommended_employee').prop('checked', false).change();
				}
				else{
					$('#enable_reco').show();
				}
			}).trigger('change');
			
			$('#req_type').select2({
					data: req_type,
				});
			
			$('#com_type').select2({
				data: com_type,
			});
			
		//	get_reco_check();

			$.ajax({
				url: "<?= url('recruitment/recruitment/hiring_request/get_hiring_edit') ?>",
				method: "GET",
				data: {id_hiring_request_header: id_hiring_request_header},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
				//	console.log(response);
					global_id_rec_detail = 0;
					$.each(response.hiring, function (i, item) {
                        $('#new_rec_detail').trigger('click');
                    });
					
					$('#id_employee_request').select2({
						data: [{id:response.id,text:response.text}],
					});	
					let id_position_detail_employee_request = response.id_position_detail_employee_request;	
					let request_type = response.request_type;
					let assigned_to = response.assigned_to;
					let id_position_routing_request = response.id_position_routing_request;
					let id_branch = response.id_branch;
					currentPositionRequest = response.request_position;
					currentBranch = response.branch;
					currentHierarchyApproval = response.hierarchy_approval;
					currentApprovalRequest = response.approval_request;

					
					global_approval_date = moment(response.approval_date);
					global_target_date = moment(response.target_date);
					global_code_status = response.code_app_status;
					
					if(response.code_app_status == 'Request_Approval' || response.code_app_status == 'Approved' || response.code_app_status == 'Partial_Approved' || response.code_app_status == 'Cancel' || response.code_app_status == 'Rejected'){
						global_view = "view";
						setTimeout(function () {
							$("#hiringForm .modal-title").html("<span class='fas fa-eye'></span> View Hiring Request");
							$("#hiringForm input").attr("disabled", true);
							$("#hiringForm input").prop("disabled", true);
							$("#hiringForm select").prop("disabled", true);
							$("#hiringForm textarea").prop("disabled", true);
							$(".input-group-append").css("display","none");
							$("#new_rec_detail").hide();
							$("#new_rec_reco").hide();
							$(".delete-record").hide();
							$(".delete-reco").hide();
							$("#save_and_submit").hide();
							$("#edit_button").hide();
						//	$('#eff_date').html('<div class="input-group"><input type="text" id="effective_date" class="form-control form-control-sm" readonly><div class="input-group-append"><span class="input-group-text far fa-calendar form-control-sm"></span></div></div>');
							if(response.code_app_status == 'Approved'){
								$("#target").show();
								$("#app_date").show();
								$("#target_date").val(response.target_date);
								$("#approval_date").val(response.approval_date);
							}
						}, 1500);
					}
					else{
						$("#hiringForm .modal-title").html("<span class='fas fa-edit'></span> Edit Hiring Request");
						$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
						
						
					}
					
					callerTriggerPosDetail();
						
					function get_trigger_pos_edit(){
						return new Promise((resolve,reject)=>{
							start_edit(id_position_detail_employee_request,request_type,assigned_to,id_position_routing_request,id_branch,response.hiring,response.id_approval);		
							resolve();
						});
					}
					
					$('#id_hiring_request_header').val(response.id_hiring_request_header).trigger('change');
				
				   $('#id_position_request').val(response.id_position_detail_employee_request).trigger('change');
				   $('#id_location_request').val(response.id_location_employee_request).trigger('change');
				   $('#rec_source').val(response.recruitment_source).trigger('change');
				   $('#reason_notes').val(response.reason_notes).trigger('change');
				   $('#skill_notes').val(response.skill_notes).trigger('change');
				   $('#pkwt_duration').val(response.pkwt_duration).trigger('change');
				   $('#cc_email').val(response.cc_email).trigger('change');
				   $('#effective_date').val(response.effective_date).trigger('change');
				//   $('#id_approval').val(response.id_approval).trigger('change');
                   $('#id_approval_request').val(response.id_approval_request).trigger('change');
                   $('#id_approval_status').val(response.id_approval_status).trigger('change');
				   
				   $('#link_tab_menu-status').click(function(){
							$.extend( true, $.fn.dataTable.defaults, {
							 columnDefs:false,
							 paging:false,
							 searching:false,
							 destroy: true,
							 dom: '<"toolbar">frtip',
							} );
		
							$('#table_approve_status_detail').DataTable({
							processing: true,
							serverSide: true,
							ajax: {
								url: "<?= url('employee/employee/employee_request/index_status').'?id_request_header='?>"+response.id_hiring_request_header+"<?= '&id_approval='?>"+response.id_approval,							
							},
							columns: [
						
								{data: 'DT_RowIndex', name: 'DT_RowIndex'},
								{data: 'sequence', name: 'sequence'},
								{data: 'name', name: 'name'},
								{data: 'code', name: 'code'},
								{data: 'execute', name: 'execute'},
							]
						});
					});
				   				  
					async function callerTriggerPosDetail(){
							await get_trigger_pos_edit();	
							 setTimeout(function () {
								$('#table_rec_body tr').each(function (index) {
									$(this).find('span.sn').html(index + 1);
									$(this).find('.id_hiring_request_detail_input').val(response.hiring[index].id_hiring_request_detail);
									$(this).find('.id_position_detail_request_input').val(response.hiring[index].id_position_detail_request).trigger('change');
								 });	  
							}, 1000);						
						}
										  
				   if(response.have_recommended_employee == true){
					callerStartCheck();
										
					function get_reco_edit(){
						$('#have_recommended_employee').prop('checked', true).change();
						global_id_rec_reco = 0;
						$.each(response.reco, function (i, item) {
							$('#new_rec_reco').trigger('click');
						});
						
						return new Promise((resolve,reject)=>{							
								$.ajax({
								url: "<?= url('recruitment/recruitment/hiring_request/get_emp_reco') ?>",
								method: "GET",
								success: function (response) {
									global_emp_reco = response;
									$('#table_rec_reco').find('.id_employee_recommendation_input').each(function (i, obj) {
										$('#' + obj.id).empty();
										$('#' + obj.id).select2({
											data: global_emp_reco
										});
										if(global_emp_reco.length > 0){
											$('#table_rec_reco').find('.id_position_detail_reco_input').each(function (i, obj) {
												$('#' + obj.id).val(response[0].id_position_detail_reco).trigger('change');
											});
											$('#table_rec_reco').find('.position_name_input').each(function (i, obj) {
												$('#' + obj.id).val(response[0].position_name).trigger('change');
											});
											$('#table_rec_reco').find('.id_location_reco_input').each(function (i, obj) {
												$('#' + obj.id).val(response[0].id_location_reco).trigger('change');
											});
											$('#table_rec_reco').find('.location_reco_input').each(function (i, obj) {
												$('#' + obj.id).val(response[0].location_reco).trigger('change');
											});
											$('#table_rec_reco').find('.company_reco_input').each(function (i, obj) {
												$('#' + obj.id).val(response[0].company_reco).trigger('change');
											});
										}
									});
								},
								complete: function(){
									resolve();
								},
							});
						});
					}

					async function callerStartCheck(){
						await get_reco_edit();						   	
						setTimeout(function () {
							$('#table_rec_reco_body tr').each(function (index) {
							//	console.log(response.reco[index].id_employee_recommendation);
								$(this).find('span.sn').html(index + 1);
								$(this).find('.id_hiring_request_recommendation_input').val(response.reco[index].id_hiring_request_recommendation);
								$(this).find('.id_employee_recommendation_input').val(response.reco[index].id_employee_recommendation).trigger('change');
							 });	  
						}, 2000);														
					}
					
					 $('#have_recommended_employee:checkbox').on('change', function (e) {
						if(this.checked){
							$('#link_tab_rec-details').removeClass('active');
							$('#link_tab_rec-reco').addClass('active');
							$('#rec-reco').addClass('show active');
							$('#rec-details').removeClass('show active');
							$('#rec-reco').show();
							$('#tab_reco').show();	
							
						$('#table_rec_reco_body tr').each(function (index) {
							$(this).find('span.sn').html(index + 1);
							$(this).find('.id_hiring_request_recommendation_input').val(response.reco[index].id_hiring_request_recommendation);
							$(this).find('.id_employee_recommendation_input').val(response.reco[index].id_employee_recommendation).trigger('change');
						 });
										 
						}
						else {
							$('#link_tab_rec-details').addClass('active');
							$('#link_tab_rec-reco').removeClass('active');
							$('#rec-details').addClass('show active');
							$('#rec-reco').hide();
							$('#tab_reco').hide();							
						}
					});
                   				   
				   }
				   
				   else{
					   $('#tab_reco').css('display','none');
					   get_reco_check();
				   }
				   
				  
                },
			/*	complete: function(){
					$('#loader').addClass('hidden');
				},
			*/
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
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_hiring_request_detail_input').attr('id', 'hiring_' + size + '_id_hiring_request_detail');
            element.find('.id_hiring_request_detail_input').attr('name', 'hiring[' + size + '][id_hiring_request_detail]');
						
			element.find('.load_pos_detail_input').attr('id', 'load_pos_detail_' +size);
			
			element.find('.id_position_detail_request_input').attr('id', 'hiring_' + size + '_id_position_detail_request');
            element.find('.id_position_detail_request_input').attr('name', 'hiring[' + size + '][id_position_detail_request]');
            element.find('.id_position_detail_request_input_error').attr('id', 'hiring_' + size + '_id_position_detail_requestError');
            element.find('.id_position_detail_request_input').prepend('<option selected></option>').select2({
                placeholder: "Select Position Detail ...",
                data: global_pos_detail,
            }).on('change', function (e) {
				if(global_pos_detail.length > 0){
					element.find('.id_employee_replacement_input').val($(this).select2('data')[0].id_employee).trigger('change');
					element.find('.id_location_input').val($(this).select2('data')[0].id_location).trigger('change');
					element.find('.replace_name_input').val($(this).select2('data')[0].name).trigger('change');
					element.find('.location_input').val($(this).select2('data')[0].location).trigger('change');
					element.find('.direct_input').val($(this).select2('data')[0].name_supervisor).trigger('change');
				}
             });
			
			element.find('.replace_name_input').attr('id', 'hiring_' + size + '_replace_name');
            element.find('.id_employee_replacement_input').attr('name', 'hiring[' + size + '][id_employee_replacement]');
            element.find('.id_location_input').attr('name', 'hiring[' + size + '][id_location]');
			element.find('.location_input').attr('id', 'hiring_' + size + '_location');
			element.find('.direct_input').attr('id', 'hiring_' + size + '_direct');
			

						
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
	
	$(document).on('click', '#new_rec_reco', function () {
            var content = jQuery('#sample_table_rec_reco tr'),
                    size = global_id_rec_reco++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_reco-'+size);
            element.find('.delete-reco').attr('data-id', size);
            element.find('.id_hiring_request_recommendation_input').attr('id', 'reco_' + size + '_id_hiring_request_recommendation');
            element.find('.id_hiring_request_recommendation_input').attr('name', 'reco[' + size + '][id_hiring_request_recommendation]');
			
			element.find('.id_employee_recommendation_input').attr('id', 'reco_' + size + '_id_employee_recommendation');
            element.find('.id_employee_recommendation_input').attr('name', 'reco[' + size + '][id_employee_recommendation]');
            element.find('.id_employee_recommendation_input_error').attr('id', 'reco_' + size + '_id_employee_recommendationError');
            element.find('.id_employee_recommendation_input').select2({
                placeholder: "Select Employee Name",
                data: global_emp_reco
            }).on('change', function (e) {
				if(global_emp_reco.length > 0){
					element.find('.id_position_detail_reco_input').val($(this).select2('data')[0].id_position_detail_reco).trigger('change');
					element.find('.position_name_input').val($(this).select2('data')[0].position_name).trigger('change');
					element.find('.id_location_reco_input').val($(this).select2('data')[0].id_location_reco).trigger('change');
					element.find('.location_reco_input').val($(this).select2('data')[0].location_reco).trigger('change');
					element.find('.company_reco_input').val($(this).select2('data')[0].company_reco).trigger('change');
				}
             }).trigger('change');

			element.find('.position_name_input').attr('id', 'reco_' + size + '_position_name');
            element.find('.id_position_detail_reco_input').attr('name', 'reco[' + size + '][id_position_detail_reco]');
            element.find('.id_location_reco_input').attr('name', 'reco[' + size + '][id_location_reco]');
			element.find('.location_reco_input').attr('id', 'reco_' + size + '_location_reco');
			element.find('.company_reco_input').attr('id', 'reco_' + size + '_company_reco');
									
            element.appendTo('#table_rec_reco_body');
			 $('#table_rec_reco_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-reco', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec_reco-' + id).remove();
            $('#table_rec_reco_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
	var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('hiring.save') }}";
			$(this).closest(".card").find("hiringForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('hiring.update') }}";
			$(this).closest(".card").find("hiringForm").submit();
		  });	
	
	 $('#hiringForm').submit(function (e) {
            e.preventDefault();
			
			let thisButtonId = e.originalEvent.submitter.id;
            if(thisButtonId == 'save_and_submit'){
                let addForm = $("#save_and_submit").hasClass('addForm');
                if(addForm == true){
                    AjaxUrl = "{{ route('hiring.save') }}";
                } else {
                    AjaxUrl = "{{ route('hiring.update') }}";
                }
            }
            let formData = $(this).serializeArray();			
	
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#hiringForm input").removeClass("is-invalid");
            $("#hiringForm textarea").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
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
							let thisIdHiring = response.data;
							
							if(thisButtonId == 'save_and_submit'){
                                $.ajax({
                                	url:"hiring_request/submit_approve/"+thisIdHiring,
                                    success: function(resp) {
										var formDataSubmit = resp.data;
                                        $.ajax({
                                            type: 'POST',
                                            headers: {
                                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            url: "{{ route('mail.new_fpk') }}",
                                            data: {source: formDataSubmit},
                                        });
                                        $('#modal_form_hiring').modal('hide');
                                        swal({
                                            title: "Data Submited!",
                                            icon: "success",
                                            buttons: {
                                                confirm: {
                                                    className: 'btn-success'
                                                },
                                            },
                                        }).then(ok => { location.reload(); });
                                    },
                                    complete: function() {
                                        $('#loader').addClass('hidden');
                                    },
                                })
                            }
							else {
                                $('#modal_form_hiring').modal('hide');
								$('#loader').addClass('hidden');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
								   location.reload();
								   }
								);
                            }
                        } else {
							$('#loader').addClass('hidden');
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
                            });
                        }
                    },
					complete: function(){
				//		$('#loader').addClass('hidden');
					},
					error: function (response) {
						$('#loader').addClass('hidden');
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_rec_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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

});		
		
$(document).ready(function(){
    $('#hiring_request_table').DataTable({
        processing: true,
		responsive: true,
        ajax: {
			url: "{{ route('hiring.index') }}",
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
				if(data['code_app_status'] == 'Request_Approval'){
				//	$(row).find('td:eq(20)').css('background', '#1db8d0');
					$(row).find('.submit_approve').css('display', 'none');
				}
				if(data['code_app_status'] == 'Cancel'){
				//	$(row).find('td:eq(20)').css('background', '#dc3545');
				//	$(row).find('td:eq(18)').css('float', 'right');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				//	$(row).find('.edit').css('display', 'none');
				}
				if(data['code_app_status'] == 'Revised'){
				//	$(row).find('td:eq(20)').css('background', '#60b8f8');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Rejected'){
				//	$(row).find('td:eq(20)').css('background', '#fe8590');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				//	$(row).find('.edit').css('display', 'none');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Partial_Approved'){
				//	$(row).find('td:eq(20)').css('background', '#ffdf7e');
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
            { data: 'desc_app_status', name: 'desc_app_status', className: 'text-center', render: function ( data, type, row ) {	
					if(row.code_app_status == 'Approved'){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else if(row.code_app_status == 'Cancel'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else if(row.code_app_status == 'Partial_Approved'){
						return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else if(row.code_app_status == 'Rejected'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else if(row.code_app_status == 'Revised'){
						return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else{
						return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
					}
				}
			},
			{ data: 'name_approval', name: 'name_approval' },
			{ data: 'note_revised', name: 'note_revised' },
			{ data: 'note_rejected', name: 'note_rejected' },
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
	
	refresh_data();

});

	
function refresh_data() {
	rec_source = [
			{
				id: 'EXTERNAL',
				text: 'EXTERNAL'
			},
			{
				id: 'INTERNAL',
				text: 'INTERNAL'
			},
			
			{
				id: 'COMBINE',
				text: 'COMBINE'
			},
		];
	
	req_type = [
			{
				id: 'ADDITIONAL',
				text: 'ADDITIONAL'
			},
			{
				id: 'REPLACEMENT',
				text: 'REPLACEMENT'
			},
		];
	
	com_type = [
			{
				id: 'Corporate',
				text: 'Organik'
			},
		/*	{
				id: 'OS',
				text: 'OS'
			},
		*/
		];
	pkwt_duration = [
			{id: '1',text: '1'},{id: '2',text: '2'},{id: '3',text: '3'},{id: '4',text: '4'},{id: '5',text: '5'},{id: '6',text: '6'},{id: '12',text: '12'},{id: '24',text: '24'},
		];
	
	$('#pkwt_duration').select2({
		data: pkwt_duration,
	});
	
	get_hr_email().then(function(value) {
	//	$('#cc_email').val(["recruitment@borwita.co.id"]).trigger('change');
    });
	get_approval_status();
	
}

function start(){
	function get_employee_by(){
			return new Promise((resolve,reject)=>{
				$.getJSON('<?= url('recruitment/recruitment/hiring_request/get_employee_by') ?>', function (data) {
					$('#id_employee_request').select2({
						data: data,
					});
					if(data.length > 0){
						global_id_employee = data[0].id;
						global_id_pos_detail = data[0].id_position_detail;
						global_id_routing = data[0].id_routing;
						global_route_name = data[0].route_name;
						global_job_class_group = data[0].job_class_group;
						
						$('#by_pos').val(data[0].route_name);
						$('#id_position_request').val(data[0].id_position_detail);
						$('#id_location_request').val(data[0].id_location);
					}
					else{
						 swal({
								title: "Nothing Position",
								icon: "error",
								buttons: {
									confirm: {
										className: 'btn-danger'
									},
								},
							}).then(ok => {
								window.location.reload();
							});
					}
					get_hierachy_fpk(global_id_employee);
					
				}).done(function() {
					resolve();
				}).fail(function (data) { // Call failed
					get_employee_by();
				});			
			});
		}

		async function callerStart(){
			await get_employee_by();			
			$('#req_type').select2({
					data: req_type,
				});
			/*	.on('change', function (e) {
					$('#loader').removeClass('hidden');
					global_req_type = $(this).select2('val');
					get_pos_by(global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type);
				}).trigger('change');	
			*/	
			$('#com_type').select2({
				data: com_type,
			});
		/*	.on('change', function (e) {
				$('#loader').removeClass('hidden');
				global_com_type = $(this).select2('val');
				get_pos_by(global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type);
			}).trigger('change');
		*/	
			global_com_type = $('#com_type').find(':selected').val();
			global_req_type = $('#req_type').find(':selected').val();
			get_pos_by(global_id_pos_detail,global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type);
		}

		callerStart();
}

$(document).on('change', '#req_type', function (event, istrigger) {  
    if(!istrigger){
		$('#loader').removeClass('hidden');
		$('#grade_req').val('');
		$('#sla').val('');
		$('#id_branch').empty();
		$('#table_rec_detail').find('.id_position_detail_request_input').each(function (i, obj) {
				$('#' + obj.id).empty();
				$('#' + obj.id).prepend('<option selected></option>').select2({
					placeholder: "Select Position Detail ...",
					data: [],
				});
		});
		$('#table_rec_detail').find('.id_employee_replacement_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.replace_name_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.id_location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.direct_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		global_req_type = $(this).select2('val');
		get_pos_by(global_id_pos_detail,global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type);
	}
});

$(document).on('change', '#com_type', function (event, istrigger) {  
    if(!istrigger){
		$('#loader').removeClass('hidden');	
		$('#grade_req').val('');
		$('#sla').val('');
		$('#id_branch').empty();
		$('#table_rec_detail').find('.id_position_detail_request_input').each(function (i, obj) {
				$('#' + obj.id).empty();
				$('#' + obj.id).prepend('<option selected></option>').select2({
					placeholder: "Select Position Detail ...",
					data: [],
				});
		});
		$('#table_rec_detail').find('.id_employee_replacement_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.replace_name_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.id_location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.direct_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		global_com_type = $(this).select2('val');
		get_pos_by(global_id_pos_detail,global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type);
	}
});

function get_pos_by(global_id_pos_detail,global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type,param_pos=false,param_branch=false,res_hiring=false){
	let myData = {
			id_pos_detail: global_id_pos_detail,
			id_routing: global_id_routing,
			job_class_group: global_job_class_group,
			route_name: global_route_name,
			name_com_type: global_com_type,
			name_req_type: global_req_type,
		};
	
	$.ajax({
		url: "<?= url('recruitment/recruitment/hiring_request/get_pos_by') ?>",
		method: "GET",
		data: myData,
		beforeSend: function () {
			$('#pos_req').empty();	
		},
		success: function (response) {
		//	console.log(response,param_pos,"a");
			$('#id_branch').prepend('<option selected></option>').select2({
				placeholder: "Select Branch ...",
			});	
		//	console.log(param_pos);
			get_pos_route(response,param_pos,param_branch,res_hiring);
		},
		error: function (xhr) {
			swal({
				icon: 'error',
				title: 'Oops...',
				dangerMode: true,
				text: 'You are not Allowed'
			});
		},
		complete: function(){
			if(global_new == 'new'){
				$('#loader').addClass('hidden');
			}
		},
	});
}

function start_pos_detail(param_pos,param_branch,res_hiring){	
	callerPosDetail(param_pos,param_branch,res_hiring).then(function() {
		get_branch(param_pos,global_req_type,param_branch).then(function() {
			$('#id_branch').val(param_branch).trigger('change', [true]);
		});
		get_pos_detail(param_pos,param_branch,global_req_type,res_hiring);
	});
	
	function get_posDetail_edit(param_pos,param_branch,res_hiring){
		return new Promise((resolve,reject)=>{
		//	console.log(param_branch);
			$('#pos_req').val(param_pos).trigger('change', [true]);	
			get_sla(param_pos);
			resolve();
		});
	}
	
	async function callerPosDetail(param_pos,param_branch,res_hiring){
			await get_posDetail_edit(param_pos,param_branch,res_hiring);	
		}
}

function get_pos_route(res,param_pos=false,param_branch=false,res_hiring=false){
//	console.log(res);
	$('#pos_req').prepend('<option selected></option>').select2({
		placeholder: "Select Request Position ...",
		data: res.concat(currentPositionRequest),
		allowClear: true,
	});	
//	console.log(param_pos,"b");		
	if(param_branch != false){
		start_pos_detail(param_pos,param_branch,res_hiring);		
	}
}

$(document).on('change', '#pos_req', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		$('#grade_req').val('');
		$('#sla').val('');
		$('#id_branch').empty();
		
		$('#table_rec_detail').find('.id_position_detail_request_input').each(function (i, obj) {
			$('#' + obj.id).empty();
		});
		$('#table_rec_detail').find('.id_employee_replacement_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.replace_name_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.id_location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.direct_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		get_sla($(this).select2('val'));
		get_branch($(this).select2('val'),global_req_type,$(this).select2('data')[0].id_branch);
//		get_pos_detail($(this).select2('val'),global_req_type);
	}
});

const get_branch = async (id_route,global_req_type,id_branch) => {
	let result;
	let myData = {
		id_route: id_route,
		name_req_type: global_req_type,
		id_branch: id_branch,
	};	
    try {
        result = await $.ajax({
           url: "<?= url('recruitment/recruitment/hiring_request/get_branch') ?>",
			method: "GET",
			data: myData,
			beforeSend: function () {
				$("#load_id_branch").show();
			//	$('#grade_req').val('');
			//	$('#sla').val('');
			},
			success: function (response) {
			//	console.log(id_route);
				$('#id_branch').prepend('<option selected></option>').select2({
					placeholder: "Select Branch ...",
					data: response.concat(currentBranch),
					allowClear: true,
				});	
			//	get_pos_detail($(this).select2('val'),global_req_type);
			},
			complete: function(){
				$("#load_id_branch").hide();
			},
        });
        return result;
    } catch (error) {
    }	
}

$(document).on('change', '#id_branch', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		$('#table_rec_detail').find('.id_employee_replacement_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.replace_name_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.id_location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.location_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_rec_detail').find('.direct_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		var id_routing = $(this).select2('data')[0].id_position_routing;
	//	var id_routing = 'null';
		get_pos_detail(id_routing,$(this).select2('val'),global_req_type);
	}
});

function get_sla(route_sla){
	let myData = {
			route_sla: route_sla,
		};	
	$.ajax({
		url: "<?= url('recruitment/recruitment/hiring_request/get_sla') ?>",
		method: "GET",
		data: myData,
		beforeSend: function () {
			$('#grade_req').val('');
			$('#sla').val('');
		},
		success: function (response) {
			$('#grade_req').val(response.grade_req);
			$('#sla').val(response.sla);
		/*	if(global_code_status != 'Approved'){
				$('#sla').val(response.sla);
			}
			else{
				$('#sla').val(global_target_date.diff(global_approval_date, 'days'));
			}
		*/
		},
		complete: function(){
			$('#loader').addClass('hidden')
		},
	});
}

function get_pos_detail(id_route,id_branch,global_req_type,res_hiring=false){
	let myData = {
			id_route: id_route,
			id_branch: id_branch,
			name_req_type: global_req_type,
			status_view: global_view,
		};	
	$.ajax({
		url: "<?= url('recruitment/recruitment/hiring_request/get_pos_detail') ?>",
		method: "GET",
		data: myData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
			$('#table_rec_detail').find('.load_pos_detail_input').each(function (i, obj) {
				$('#' + obj.id).show();
			});
		},
		success: function (response) {			
			global_pos_detail = response;
			$('#pos_length').val(response.length);
			$('#table_rec_detail').find('.id_position_detail_request_input').each(function (i, obj) {
				$('#' + obj.id).empty();
				$('#' + obj.id).prepend('<option selected></option>').select2({
					placeholder: "Select Position Detail ...",
					data: global_pos_detail,
				});
				
				if(res_hiring != false){
					setTimeout(function () {
						$('#' + obj.id).val(res_hiring[i].id_position_detail_request).trigger('change');
					}, 500);
				}
			
				/*
				if(global_pos_detail.length > 0){
					$('#table_rec_detail').find('.id_employee_replacement_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].id_employee).trigger('change');
					});
					$('#table_rec_detail').find('.replace_name_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].name).trigger('change');
					});
					$('#table_rec_detail').find('.location_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].location).trigger('change');
					});
				}
				*/
			});
			
		
		},
		complete: function(){
			$('#loader').addClass('hidden');
			setTimeout(function () {
				$('#table_rec_detail').find('.load_pos_detail_input').each(function (i, obj) {
					$('#' + obj.id).hide();
				});
			}, 1000);
		},
	});
}

function get_emp_reco(){
	$.ajax({
		url: "<?= url('recruitment/recruitment/hiring_request/get_emp_reco') ?>",
		method: "GET",
		beforeSend: function () {
			$('#loader').removeClass('hidden');		
		},
		success: function (response) {
			global_emp_reco = response;
			$('#table_rec_reco').find('.id_employee_recommendation_input').each(function (i, obj) {
				$('#' + obj.id).empty();
				$('#' + obj.id).select2({
					data: global_emp_reco
				});
				if(global_emp_reco.length > 0){
					$('#table_rec_reco').find('.id_position_detail_reco_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].id_position_detail_reco).trigger('change');
					});
					$('#table_rec_reco').find('.position_name_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].position_name).trigger('change');
					});
					$('#table_rec_reco').find('.id_location_reco_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].id_location_reco).trigger('change');
					});
					$('#table_rec_reco').find('.location_reco_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].location_reco).trigger('change');
					});
					$('#table_rec_reco').find('.company_reco_input').each(function (i, obj) {
						$('#' + obj.id).val(response[0].company_reco).trigger('change');
					});
				}
			});
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});
}

function get_approval_status(){		
		$.getJSON('<?= url('recruitment/recruitment/hiring_request/get_approval_status') ?>', function (data) {
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
		});					
}

const get_hr_email = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/hiring_request/get_hr_email') ?>',
            method: "GET",
            success: function (res) {
			$('#cc_email').select2({
				data: res,
			});
            },
        });
        return result;
    } catch (error) {
        get_hr_email();
    }
}

function start_edit(id_pos_detail,req_type,com_type,id_position_routing_request,id_branch,res_hiring,id_approval){

	callerStartEdit(id_pos_detail,req_type,com_type,id_position_routing_request,id_branch,res_hiring,id_approval);

	function get_emp_edit(id_pos_detail,req_type,com_type){
			return new Promise((resolve,reject)=>{
				let param_emp = {
					id_position_detail: id_pos_detail,
				};
				
				$.ajax({
					url: "<?= url('recruitment/recruitment/hiring_request/get_emp_edit') ?>",
					method: "GET",
					data: param_emp,		
					success: function (res) {
						 $('#com_type').val(com_type).trigger('change',[true]);
						 $('#req_type').val(req_type).trigger('change',[true]);
						global_id_employee = res.id_employee;
						global_id_pos_detail = res.id_position_detail;
						global_id_routing = res.id_routing;
						global_route_name = res.route_name;
						global_job_class_group =res.job_class_group;
						
						$('#by_pos').val(res.route_name);
					//	$('#id_position_request').val(data[0].id_position_detail);
					//	$('#id_location_request').val(data[0].id_location);
					//	setTimeout(function () {
					//		get_hierachy_fpk(global_id_employee);
					//	}, 2000);
					},
					complete: function(){
						resolve();
					},
					
				});
			});
		}

		async function callerStartEdit(id_pos_detail,req_type,com_type,id_position_routing_request,id_branch,res_hiring,id_approval){
			await get_emp_edit(id_pos_detail,req_type,com_type);	
			
			get_hierachy_fpk(global_id_employee).then(function() {
				setTimeout(function () {
					$('#id_approval').val(id_approval).trigger('change');
				}, 500);
			});
			
			global_com_type = com_type;
			global_req_type = req_type;
		//	console.log(id_position_routing_request,"c");
			get_pos_by(global_id_pos_detail,global_id_routing,global_job_class_group,global_route_name,global_com_type,global_req_type,id_position_routing_request,id_branch,res_hiring);
			
		//	setTimeout(function () {
				
		//	}, 3000);
		}

}

const get_hierachy_fpk = async (global_id_employee) => {
	let result;
	let myParam = {
		id_employee: global_id_employee,
	};
    try {
        result = await $.ajax({
           url: "<?= url('recruitment/recruitment/hiring_request/get_hierachy_fpk') ?>",
			method: "GET",
			data: myParam,
			success: function (response) {
				$('#id_approval').select2({
					placeholder: "No Hierarchy Approval ...",
					data: response.concat(currentHierarchyApproval),
				}).on('change', function (e) {
					let myId = {
						id_approve: $(this).select2('data')[0].id,
					};
					$.ajax({
						url: "<?= url('recruitment/recruitment/hiring_request/get_hierachy_approver') ?>",
						method: "GET",
						data: myId,
						beforeSend: function () {
							$("#load_old_approval_name").show();
							$('#id_approval_request').val('');
							$('#approval_name').val('');
						},
						success: function (res) {
							$('#id_approval_request').val(res[0].emp_approval);
							$('#approval_name').val(res[0].name_employee);
							if(!res[0].emp_approval || !res[0].name_employee) {
								$('#id_approval_request').val(currentApprovalRequest[0].id);
								$('#approval_name').val(currentApprovalRequest[0].text);
							}
						},
						complete: function(){
							$("#load_old_approval_name").hide();
						},					
					});				
				}).trigger('change');	
			},
        });
        return result;
    } catch (error) {
    }	
}

function get_reco_check(){
	$('#have_recommended_employee:checkbox').on('change', function (e) {
	//	$('#loader').removeClass('hidden');
			if(this.checked){
				$('#link_tab_rec-details').removeClass('active');
				$('#link_tab_rec-reco').addClass('active');
				$('#rec-reco').addClass('show active');
				$('#rec-details').removeClass('show active');
				$('#rec-reco').show();
				$('#tab_reco').show();	
				
				get_emp_reco();
			}
			else {
				$('#link_tab_rec-details').addClass('active');
				$('#link_tab_rec-reco').removeClass('active');
				$('#rec-details').addClass('show active');
				$('#rec-reco').hide();
				$('#tab_reco').hide();
			}
		});
}

$(document).on('click', '.submit_approve', function (event) {
	id_hiring_header = $(this).attr('id');
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
			   url:"hiring_request/submit_approve/"+id_hiring_header,
			   beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(response){
                    if(response.status == 'true'){
                        var formData = response.data;
                        $.ajax({
                            type: 'POST',
                            headers: {
                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('mail.new_fpk') }}",
                            data: {
                                source: formData
                            },
                        });
					
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                            $('#hiring_request_table').DataTable().ajax.reload();
                            swal({
                                title: "Data Submited!",
                                icon: "success",
                                buttons: {
                                    confirm: {
                                        className: 'btn-success'
                                    },
                                },
                            }).then(ok => {
                                $('#hiring_request_table').DataTable().ajax.reload();
                            });
                        }, 50);
                    } else {
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
                        });
                    }
			   },
			   complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});

$(document).on('click', '.cancel', function (event) {
	id_hiring_header = $(this).attr('id');
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
			   url:"hiring_request/cancel/"+id_hiring_header,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#hiring_request_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#hiring_request_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});

$(document).on('click', '.info_track', function(){
	let id_hiring_request_header = $(this).attr('id');
//	let request_position = $(this).attr('job');
//	$(".detail_tracking").html("");
//	$(".detail_tracking").html('Info Tracking Candidate ('+request_position+')');
	$("#table_div_info").html("");	
	$("#table_div_info").html('<table id="info_tracking_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');

	let myData_info = {
		id_hiring_request_header: id_hiring_request_header,
	};
	
	let t_info = $('#info_tracking_table').DataTable({
			processing: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('hiring.info') }}",
				"data": myData_info,
				error: function (jqXHR, textStatus, errorThrown) {
						$('#info_tracking_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{   // Checkbox select column
				data: 'id_applied_stage_history',
				orderable: false,
				targets: 1,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{defaultContent: '', title: 'No.' ,orderable: false, responsivePriority: 1},
				{ data: 'name', title: 'Name', responsivePriority: 2},
				{ data: 'routing', title: 'Request Position', responsivePriority: 8},
				{ data: 'stage', title: 'Rec. Stage', responsivePriority: 3, render: function ( data, type, row ) {
						if(row.code == 'HIR'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code == 'OFL'){
								return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'status', title: 'Rec. Status', responsivePriority: 7, render: function ( data, type, row ) {
						if(row.status == 'Pass'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.status == 'Review'){
								return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.status == 'Failed'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'start_date', title: 'Start Date', responsivePriority: 4},
				{ data: 'end_date', title: 'End Date', responsivePriority: 5, render: function ( data, type, row ) {
						if(row.end_date == null){
								return '-';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'count_days', title: 'Duration (Days)', responsivePriority: 6, className: 'text-middle th-text-score'},
			],
		});		
		t_info.on('order.dt search.dt', function () {
        let i = 1;
        t_info.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
	$('#myModal').modal('show');
	
	$("#info_tracking_table_processing").css("background","white");
	$("#info_tracking_table_processing i.fa-spinner").css("margin-top","100px");
	$("#info_tracking_table_processing").css("color","black");
});

$(document).on("click", ".advanced_can_group", function () {
	$('.cf').select2({width:'100%'});
	if($(".info_tracking_table").css('display') == 'none'){
		$(".info_tracking_table").show("slow");
	}
	else {
		$(".info_tracking_table").hide("slow");
	}   
});	

</script>
@endsection