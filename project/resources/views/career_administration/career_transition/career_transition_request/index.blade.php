@extends('adminlte::page')
@section('title', 'Career Transition Request')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Career Transition Request</h5>
                <div class="card-tools">
                    <button type="button" class="execute btn btn-sm btn-success"><i class="far fa-play-circle"></i> Execute</button>
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Career Transition Request</button>
                </div>
            </div>
       
			 <div class="card-body">
			 <div class="alert alert-warning alert-dismissible" style="background:#fffcf5;">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  Setelah <b><i>Save Career Request</i></b>, Klik button <b><i>Submit</i></b> <span class="btn-info btn-sm" title="Submit" style="padding:0 8px 2px 7px;"><span class="fas fa-paper-plane" style="font-size:10px;"></span></span> untuk memproses <i>Request</i> Anda
             </div>
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="career_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						<thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th style="white-space:nowrap;">Reference Number</th>
                            <th width=150>NIK</th>
                            <th width=150>Name</th>
							<th style="white-space:nowrap;">Career Transition</th>
							<th style="white-space:nowrap;">Transaction Type</th>
							<th style="white-space:nowrap;">Employee Status</th>
							<th style="white-space:nowrap;">Company Destination</th>
							<th style="white-space:nowrap;">Position Detail</th>
							<th style="white-space:nowrap;">Position Routing</th>
							<th style="white-space:nowrap;">Job Grade</th>
							<th style="white-space:nowrap;">Job Status</th>
							<th style="white-space:nowrap;">Location</th>
							<th data-priority="3" style="white-space:nowrap;">Effective/Terminate Date</th>
							<th style="white-space:nowrap;">Expired Date</th>
							<th style="white-space:nowrap;">Attachment</th>
							<th style="white-space:nowrap;">Note Revised</th>
							<th style="white-space:nowrap;">Note Rejected</th>
							<th data-priority="1">Approval Status</th>
                            <th data-priority="2" width=250>Action</th>
                        </tr>
                    </thead> 
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_career" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="careerForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Career Transition Request</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 500px;overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            
							<div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
									<input name="id_career_transaction" id="id_career_transaction" type="hidden">
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
									<input name="code_transaction_type" id="code_transaction_type" type="hidden">
                                    <select name="id_transaction_type" id="id_transaction_type" class="transaction_type form-control form-control-sm select2" style="width: 100%;">
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
                                <label class="col-sm-4 col-form-label">Current Company</label>
								<div class="col-sm-8">
									<div class="is-loading">
										<input id="old_company" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_company" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Employment Status</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input name="id_old_employment_status" id="id_old_employment_status" type="hidden">
										<input id="old_employment_status" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_employment_status" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Department</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_department" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_department" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Position Route</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_position_routing" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_position_routing" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Position Detail</label>
                                <div class="col-sm-8">
									<div class="is-loading">
                                    <!-- input name="id_old_position_detail" id="id_old_position_detail" type="hidden" -->
                                    <!-- input id="old_position_detail" class="form-control form-control-sm" style="width: 100%;" disabled -->
									 <select name="id_old_position_detail" id="id_old_position_detail" class="form-control form-control-sm select2 form-select" style="width: 100%;">
									</select>
									<span id="load_id_old_position_detail" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>							
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Direct Supervisor</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_spv" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_spv" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Principal</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_principal" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_principal" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Job Grade</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_job_grade" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_job_grade" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Job Status</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_job_status" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_job_status" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Location</label>
                                <div class="col-sm-8">
									<div class="is-loading">
										<input id="old_location" class="form-control form-control-sm" style="width: 100%;" readonly>
										<span id="load_old_location" class="spinner-border spinner-border-sm" style="display:none;"></span>
									</div>
                                </div>
                            </div>
							<div id="en_app">
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
										<select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
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
							</div>
							
                        </div>
						
						<div class="col-md-6" style="margin-bottom:0px;">							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm" readonly>
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>              
						
							<div class="row">
                                <label class="col-sm-4 col-form-label" id="eff_date">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						<div id="movement">
							<div class="row">
								<label class="col-sm-4 col-form-label">Expired Date</label>
                                <div class="col-sm-8">
                                   <input name="expired_date" id="expired_date" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="expired_dateError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
									<select name="id_company_destination" id="id_company_destination" class="form-control form-control-sm select2" style="width: 100%;">
									</select>    
                                </div>
                            </div>
                        </div>						
						<div id="emp_status">
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Employment Status</label>
                                <div class="col-sm-8">
									<select name="id_employment_status" id="id_employment_status" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									 <span class="invalid-feedback" role="alert" id="id_employment_statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div id="emp_entity_move">
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Work Hours</label>
                                <div class="col-sm-8">
									<select name="id_new_shift_group" id="id_new_shift_group" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									 <span class="invalid-feedback" role="alert" id="id_new_shift_groupError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Work Time Zone</label>
                                <div class="col-sm-8">
									<select name="id_new_timezone" id="id_new_timezone" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									 <span class="invalid-feedback" role="alert" id="id_new_timezoneError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div id="movement2">
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Position Route</label>
                                <div class="col-sm-8">
									<div class="input-group">
										<input name="id_position_routing" id="id_position_routing" type="hidden">
										<input name="id_position_detail" id="id_position_detail" type="hidden">
										<input type="text" id="new_position_routing" onclick="browse_job()" class="form-control form-control-sm"style="border-radius: 5px 0 0 5px;">
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-group" style="font-size:10px;color:#dc3545;" role="alert" id="position_detailError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Department</label>
                                <div class="col-sm-8">
									<input name="id_dept" id="id_dept" type="hidden">
									<input id="new_department" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Direct Supervisor</label>
                                <div class="col-sm-8">
									<input id="new_spv" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Principal</label>
                                <div class="col-sm-8">
									<input id="new_principal" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
							
							
							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">New Position Detail</label>
                                <div class="col-sm-8">
									<input name="id_position_detail" id="id_position_detail" type="hidden">
									<input id="new_position_detail" class="form-control form-control-sm" style="width: 100%;" readonly>								
                                </div>
                            </div -->		
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee Replacement</label>
                                <div class="col-sm-8">
									<input id="id_employee2" name="id_employee2" type="hidden">
									<input id="emp_replace" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>							
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Job Grade</label>
                                <div class="col-sm-8">
									<input name="id_job_grade" id="id_job_grade" type="hidden">
                                    <input id="new_job_grade" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Job Status</label>
                                <div class="col-sm-8">
									<input name="id_job_status" id="id_job_status" type="hidden">
                                    <input id="new_job_status" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">New Location</label>
                                <div class="col-sm-8">
									<input name="id_location" id="id_location" type="hidden">
                                    <input id="new_location" class="form-control form-control-sm" style="width: 100%;" readonly>
                                </div>
                            </div>
                        </div>
							
						<div id="terminate">
							<div class="row">
								<label class="col-sm-4 col-form-label">Request Resign Date</label>
                                <div class="col-sm-8">
                                   <input name="request_resign_date" id="request_resign_date" class="form-control form-control-sm">
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Reason Category</label>
								<div class="col-sm-8">
                                    <select name="resign_category" id="resign_category" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="resign_categoryError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Reason Description</label>
								<div class="col-sm-8">
                                    <select name="id_terminate_reason" id="id_terminate_reason" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_terminate_reasonError">
                                        <strong></strong>
                                    </span>
                                </div>
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
									<label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Max 2 MB</i></label>
								</div>
								
							   </div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Reco Form</label>
                                <div class="col-sm-8">
									<div class="input-group">
										<input name="id_recommendation_header" id="id_recommendation_header" type="hidden">
										<input type="text" id="reco_header" onclick="browse_reco()" class="form-control form-control-sm"style="border-radius: 5px 0 0 5px;">
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-group" style="font-size:10px;color:#dc3545;" role="alert" id="position_detailError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                        </div>						
                       <div class="col-md-12" style="margin-top:10px;">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Note/Reason</label>
                                <div class="col-sm-12">
                                    <textarea name="remark" id="remark" class="form-control form-control-sm" rows="4" placeholder="Max 200 Char"></textarea>
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>
                                </div>
							</div>	
                        </div>			
                    </div>
                </div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-info btn-sm " id="save_and_submit"></button>&nbsp;
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> 
                </div>
                <!-- div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div -->
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

<div id="browseModalIm" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="card-body">
					<table align="center" id="bro_table_im" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr align="center">
						<th>No.</th>
						<th>Letter Date</th>
						<th>Letter Number</th>
						<th>Employee Name</th>
						<th>NIK</th>
						<th>Type</th>
						<th>Effective Date</th>
						<th>Expired Date</th>
						<th width=50>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	
<div id="ModalContract" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="card-body">
					<table style="width:100%" align="center" id="bro_table_contract" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr align="center">
						<th>No.</th>
						<th>Letter Date</th>
						<th>Letter Number</th>
						<th>Employee Name</th>
						<th>Area</th>
						<th>Description</th>
						<th>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	
<div id="browseModal" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="card-body">
					<table align="center" id="bro_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr align="center">
						<th>No.</th>
						<th>Letter Date</th>
						<th>Letter Number</th>
						<th>Employee Name</th>
						<th>NIK</th>
						<th>Type</th>
						<th>Description</th>
						<th width=50>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	

<div id="browseModaljob" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-xl">
  <div class="modal-content"> 
   <div class="modal-header">
		<h5 class="modal-title">Search Position</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
    </div>
   <div class="card-body">
					<table id="bro_table_job" style="width:100%;" class="display nowrap table table-striped table-hover datatable">
					 <thead>
					  <tr>
					   <th></th>
					   <th data-priority="2" style="white-space:nowrap;">Position Detail</th>
					   <th data-priority="3" style="white-space:nowrap;">Position Route</th>
					   <th data-priority="4" style="white-space:nowrap;">Branch</th>
					   <th style="white-space:nowrap;">Location</th>
					   <th style="white-space:nowrap;">Principal</th>
					   <th style="white-space:nowrap;">Supervisor Position</th>
					   <th data-priority="5" style="white-space:nowrap;">Direct Supervisor</th>
					   <th style="white-space:nowrap;">Department</th>
					   <th style="white-space:nowrap;">Assign Company</th>
					   <th data-priority="6" style="white-space:nowrap;">Replace Employee</th>
					   <th style="white-space:nowrap;">Transition Type</th>
					   <th data-priority="7" style="white-space:nowrap;">Effective Date</th>
					   <th data-priority="1">Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	
<div id="browseModalreco" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-xl">
  <div class="modal-content"> 
   <div class="modal-header">
		<h5 class="modal-title">Search Recommendation Form</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
    </div>
   <div class="card-body">
			<table id="bro_table_reco" style="width:100%;" class="display table table-striped table-hover datatable">
			 <thead>
			  <tr align="center">
			   <th></th>
			   <th data-priority="2" style="white-space:nowrap;">Reference Number</th>
			   <th data-priority="3" style="width:150px;">Name (NIK)</th>
			   <th data-priority="4">Current Position</th>
			   <th data-priority="5">New Position</th>
			   <th data-priority="6" style="white-space:nowrap;">Category</th>
			   <th data-priority="7" style="white-space:nowrap;">Effective Date</th>
			   <th data-priority="1">Action</th>
			  </tr>
			 </thead>
			 <tbody></tbody>
			</table>
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
		background: #eee;
		box-shadow: none;
    }
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
// let global_career_type = "";
let global_career_rehire = null;
let global_employee = [];
let global_company = 0;
let global_emp = 0;
let global_category = "";
let global_new = "";
let global_type = "";
let global_expired = "";
let global_old_position = 0;
let global_old_company = 0;
let global_default_rehire = "";
let global_terminate_reason = "";
//let global_career = "";

function checkpos(id,company) {	
		  $.ajax({
		   url: "<?= url('career_administration/career_transition/career_transition_request/checkpos') . '?jobid=' ?>" + id +"<?= '&company='?>"+company,
		   dataType:"json",
		   success:function(data)
		   {
		//	console.log(data.result.id_dept);
			$('#job_position').val(data.result.job_position).trigger('change');
			$('#id_dept').val(data.result.id_dept).trigger('change');
			$('#new_department').val(data.result.department).trigger('change');
			$('#new_spv').val(data.result.name_supervisor).trigger('change');
			$('#new_principal').val(data.result.principal).trigger('change');
			$('#id_position_routing').val(data.result.id_position_routing).trigger('change');
			$('#new_position_routing').val(data.result.position_routing).trigger('change');
			$('#id_position_detail').val(data.result.id_position_detail).trigger('change');
			$('#new_position_detail').val(data.result.position_detail).trigger('change');
			$('#id_employee2').val(data.result.id_employee).trigger('change');
			$('#emp_replace').val(data.result.name).trigger('change');
			$('#id_job_grade').val(data.result.id_job_grade).trigger('change');
			$('#new_job_grade').val(data.result.job_grade).trigger('change');
			$('#id_job_status').val(data.result.id_job_status).trigger('change');
			$('#new_job_status').val(data.result.job_status).trigger('change');
			$('#id_location').val(data.result.id_location).trigger('change');
			$('#new_location').val(data.result.location).trigger('change');
			$("#browseModaljob").modal('hide');
		   }
		  })
}

function checkreco(id) {	
		  $.ajax({
		   url: "<?= url('career_administration/career_transition/career_transition_request/checkreco') . '?recoid=' ?>" + id,
		   dataType:"json",
		   success:function(data)
		   {
			$('#reco_header').val(data.result.reference_number).trigger('change');
			$('#id_recommendation_header').val(data.result.id_recommendation_header).trigger('change');
			$("#browseModalreco").modal('hide');
		   }
		  })
}

function browse_job() {
	$('#browseModaljob').modal('show');	
	
	var table = $("#bro_table_job").DataTable({
		scrollY: "400px",
		scrollX: true,
		fixedColumns:   {
            left: 0,
            right: 1
        },
		responsive: true,
          paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          autoWidth: true,
		  columnDefs:false,
		  dom: '<"toolbar">frtip',
          columns : [
		  {
                defaultContent: '',
				orderable: false,
				},
        //    { data : 'job_position' },
            { data : 'position_detail' },
            { data : 'position_routing' },
            { data : 'branch' },
            { data : 'location' },
            { data : 'principal' },
            { data : 'parent_position_detail' },
            { data : 'name_supervisor' },
            { data : 'department' },
            { data : 'assign_company' },
            { data : 'name' },
            { data : 'trans_type' },
            { data : 'effective_date' },
            { data : 'action'},
          ],    
          ajax: {
            type: 'GET',
   			url: "<?= url('career_administration/career_transition/career_transition_request/browse_job') . '?company=' ?>" + global_company,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#bro_table_job').DataTable().ajax.reload();
				},
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
            //      'job_position'   :json[i]['job_position'],
                  'position_detail'   : json[i]['position_detail'],
                  'position_routing'   : json[i]['position_routing'],
                  'branch'   : json[i]['branch'],
                  'location'   : json[i]['location'],
                  'principal'   : json[i]['principal'],
                  'parent_position_detail'   : json[i]['parent_position_detail'],
                  'name_supervisor'   : json[i]['name_supervisor'],
                  'department'   : json[i]['department'],
                  'assign_company'   : json[i]['assign_company'],
                  'name'   : json[i]['name'],
                  'trans_type'   : json[i]['trans_type'],
                  'effective_date'   : json[i]['effective_date'],
                  'action'   : '<button type="button" name="check" id="'+json[i]['id_position_detail']+'" class="btn btn-success btn-sm" title="Check" onClick="checkpos('+json[i]['id_position_detail']+','+global_company+')"><i class="fa fa-check-square-o" aria-hidden="true"></i></button>',
                })
                no++;
              }
              return return_data;
            }
          }
      });
//	alert("tes");
}

function browse_reco() {
	var id_employee = $('#id_employee').val();
	if(id_employee == ""){
		swal({
			icon: 'error',
			dangerMode: true,
			content: {
				element: "div",
				attributes: {
					innerText: 'Please Select Employee',
					className: "swal-red",
				},
			},
		});
	}
	else{
		$('#browseModalreco').modal('show');	
		
		var table = $("#bro_table_reco").DataTable({
			scrollY: "400px",
			scrollX: true,
			fixedColumns:   {
				left: 0,
				right: 1
			},
		//	responsive: true,
			  paging: true,
			  pageLength:10,
			  destroy:true,
			  lengthChange: true,
			  searching: true,
			  ordering: true,
			  info: true,
			  autoWidth: true,
			  columnDefs:false,
			  dom: '<"toolbar">frtip',
			  columns : [
			  {
					defaultContent: '',
					orderable: false,
					},
				{ data : 'reference_number' },
				{ data : 'name' },
				{ data : 'old_position' },
				{ data : 'new_position' },
				{ data : 'cat' },
				{ data : 'effective_date' },
				{ data : 'action'},
			  ],    
			  ajax: {
				type: 'GET',
				url: "<?= url('career_administration/career_transition/career_transition_request/browse_reco') . '?id_employee='?>"+id_employee +"<?= '&cat='?>"+global_category,
				error: function (jqXHR, textStatus, errorThrown) {
					//	$('#bro_table_reco').DataTable().ajax.reload();
					},
				dataType: 'JSON',
				dataSrc : function (json) {
				  var return_data = new Array();
				  var no=1;
				  for(var i=0;i< json.length; i++){
					return_data.push({
					  'reference_number'   : json[i]['reference_number'],
					  'name'   : json[i]['name'],
					  'old_position'   : json[i]['old_position'],
					  'new_position'   : json[i]['new_position'],
					  'cat'   : json[i]['cat'],
					  'effective_date'   : json[i]['effective_date'],
					  'action'   : '<button type="button" name="check_reco" id="'+json[i]['id_recommendation_header']+'" class="btn btn-success btn-sm" title="Check" onClick="checkreco('+json[i]['id_recommendation_header']+','+global_company+')"><i class="fa fa-check-square-o" aria-hidden="true"></i></button>',
					})
					no++;
				  }
				  return return_data;
				}
			  }
		  });
	}
}

$(document).on('click', '.execute', function () {
			$.ajax({
                url: "<?= url('career_administration/career_transition/career_transition_request/transition') ?>",
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success:function(data){
					if(data.status == 'true'){
						swal({
							title: "Data Execute!",
							icon: "success",
							buttons: {confirm : {className:'btn-success'},},
						}).then(ok => {
							$('#career_table').DataTable().ajax.reload();	
						});
					} else {
						swal({
							icon: 'error',
							dangerMode: true,
							content: {
								element: "div",
								attributes: {
									innerText: data.message,
									className: "swal-red",
								},
							},
						});
					}
				},
				complete: function(){
					$('#loader').addClass('hidden');
				},
			});
        });
		
$(document).on('click', '.new', function () {
//	global_career = "";
			global_new = "new";
			get_career_category().then(function(res) {
				$('#loader').addClass('hidden');
			});
            $("#careerForm")[0].reset();
            $("#careerForm .modal-title").html("<span class='fas fa-plus'></span> Form Career Transition Request");
            $(".invalid-feedback").children("strong").text("");
            $("#careerForm input").removeClass("is-invalid");
            $("#careerForm select").removeClass("is-invalid");
            $("#careerForm textarea").removeClass("is-invalid");
			$("#edit_button").css("display","none");
			$("#submit_button").css("display","none");     
			$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').addClass('addForm');
			$('#id_approval_status').val('').trigger('change');		
			$('#modal_form_career').modal('show');			
        });

$(document).on('click', '.edit', function(){
	let id_career_transaction = $(this).attr('id');
	$("#careerForm")[0].reset();
	$("#careerForm .modal-title").html("<span class='fas fa-edit'></span> Edit Career Transition Request");
	$(".invalid-feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$(".error-tab").html("");
	$("#careerForm input").removeClass("is-invalid");
	$("#save_button").css("display","none");
	get_career_category().then(function(res) {	
		$.ajax({
                url: "<?= url('career_administration/career_transition/career_transition_request/get_career_edit') ?>",
                method: "GET",
                data: {id_career_transaction: id_career_transaction},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},				
                success: function (response) {		
						global_company = response.id_company_destination;
						$('#id_career_transaction').val(response.id_career_transaction).trigger('change');
						$('#id_transition_category').attr('readonly',true);
						$('#id_transition_category').val(response.id_transition_category).trigger('change');
						$('#transaction_number').val(response.transaction_number).trigger('change');
						$('#remark').val(response.remark).trigger('change');                
						$('#effective_date').val(response.effective_date).trigger('change');
						$('#request_resign_date').val(response.request_resign_date).trigger('change');
					 //   $('#attachment').val(response.attachment).trigger('change');
														
						$('#select2status').val(response.status);
						$('#id_approval_status').val(response.id_approval_status).trigger('change');
						if(response.category_code == 'Entity_Movement' || response.transaction_type_code == 'Temporary Assignment'){
							get_company(response.category_code).then(function(res) {
								$('#id_company_destination').val(response.id_company_destination).trigger('change',[true]);
								get_employment_status_edit(global_company).then(function(res) {
									$('#id_employment_status').val(response.id_employment_status).trigger('change');             
								});
								get_shift(global_company).then(function(res) {
									$('#id_new_shift_group').val(response.id_new_shift_group).trigger('change');             
								});
								get_timezone(global_company).then(function(res) {
									$('#id_new_timezone').val(response.id_new_timezone).trigger('change');             
								});
							});
						}
						else{
							get_company_session();
							$('#id_company_destination').val(response.id_company_destination).trigger('change',[true]);
							get_employment_status_edit(global_company).then(function(res) {
								$('#id_employment_status').val(response.id_employment_status).trigger('change');             
							});
						}
					
						setTimeout(function () {
							if(response.enable_approval == 1){
								$('#enable_approval').prop('checked', true);
							//	$('#id_approval').select2({disabled: false});
							//	console.log(response.code);
								$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_hierachy') . '?code=' ?>'+response.code+'<?= '&emp='?>'+response.id_employee, function (data) {
									$('#id_approval').select2({
										placeholder: "Select Hierarchy Approval ...",
										data: data,
										disabled: false,
										});
									$('#id_approval').val(response.id_approval).trigger('change');
									});											   
							}
							
						}, 3000);
						
						setTimeout(function () {
							callerStart();
							function get_emp_trans(){
								return new Promise((resolve,reject)=>{
									$('#id_transaction_type').val(response.id_transaction_type).trigger('change',[true]);
									$('#id_transaction_type').attr('readonly',true);
									resolve();
								});
							}

							async function callerStart(){
								await get_emp_trans();
								setTimeout(function () {
									$('#id_employee').val(response.id_employee).trigger('change');	
								}, 2000);
							}	
							
							$('#id_old_employment_status').val(response.id_old_employment_status).trigger('change');
							$('#id_old_position_detail').val(response.id_old_position_detail);
						}, 3000);
						setTimeout(function () {
							global_expired = response.expired_date;
							if(response.transaction_type_code != 'Termination'){
								checkpos(response.id_position_detail,response.id_company);
							}
							$('#expired_date').val(response.expired_date).trigger('change');
						/*
							$('#id_position_detail').val(response.id_position_detail).trigger('change');
							$('#job_position').val(response.job_position).trigger('change');
							$('#id_dept').val(response.id_dept).trigger('change');
							$('#new_department').val(response.department).trigger('change');
							$('#new_spv').val(response.name_supervisor).trigger('change');
							$('#new_principal').val(response.principal).trigger('change');
							$('#id_position_routing').val(response.id_position_routing).trigger('change');
							$('#new_position_routing').val(response.position_routing).trigger('change');
							$('#new_position_detail').val(response.position_detail).trigger('change');
							$('#id_employee2').val(response.id_employee).trigger('change');
							$('#emp_replace').val(response.name).trigger('change');
							$('#id_job_grade').val(response.id_job_grade).trigger('change');
							$('#new_job_grade').val(response.job_grade).trigger('change');
							$('#id_job_status').val(response.id_job_status).trigger('change');
							$('#new_job_status').val(response.job_status).trigger('change');
							$('#id_location').val(response.id_location).trigger('change');
							*/
							$('#new_location').val(response.location).trigger('change');
							$('#resign_category').val(response.resign_category).trigger('change');
							get_terminate_reason(global_terminate_reason).then(function(res) {
								$('#id_terminate_reason').val(response.id_terminate_reason).trigger('change');								
							});
						/*	get_employment_status_edit(global_company).then(function(res) {
								$('#id_employment_status').val(response.id_employment_status).trigger('change');             
							});
						*/
							$('#reco_header').val(response.ref_number_reco).trigger('change');
							$('#id_recommendation_header').val(response.id_recommendation_header).trigger('change');
						}, 3500);
						if(response.code_status == 'Request_Approval' || response.code_status == 'Partial_Approved' || response.code_status == 'Approved'){					
							setTimeout(function () {
								$("#careerForm input").attr("readonly", true);
								$("#careerForm input").prop("disabled", true);
								$("#careerForm select").prop("disabled", true);
								$("#careerForm textarea").prop("disabled", true);
								$(".input-group-append").css("display","none");
							//	$('#id_approval').select2({disabled: true});
								$("#edit_button").css("display","none");
								$("#save_and_submit").hide().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
								$("#submit_button").css("display","none");
							}, 2000);
						} else {
							$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
						}
                },
				complete: function(){
				//	setTimeout(function () {
				//		$('#loader').addClass('hidden');
				//	}, 4000)
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
        });	
			
		$('#modal_form_career').modal('show');
		return false;
 });


    $(function () {
		var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('career.save') }}";
			$(this).closest(".card").find("careerForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('career.update') }}";
			$(this).closest(".card").find("careerForm").submit();
		  });
		
	  
        $('#careerForm').submit(function (e) {
            e.preventDefault();

            let thisButtonId = e.originalEvent.submitter.id;
            if(thisButtonId == 'save_and_submit'){
                let addForm = $("#save_and_submit").hasClass('addForm');
                if(addForm == true){
                    AjaxUrl = "{{ route('career.save') }}";
                } else {
                    AjaxUrl = "{{ route('career.update') }}";
                }
            }

			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $(".invalid-group").children("strong").text("");
            $("#careerForm input").removeClass("is-invalid");
            $("#careerForm select").removeClass("is-invalid");
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
				//	url: "{{ route('career.save') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							let thisIdCareerTransaction = response.data;

                        	if(thisButtonId == 'save_and_submit'){
                                $.ajax({
                                	url:"career_transition_request/submit_approve/"+thisIdCareerTransaction,
                                    success: function(resp) {
                                        $('#modal_form_career').modal('hide');
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
                            } else {
                                $('#modal_form_career').modal('hide');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
								   location.reload();
								   }
								);
                            }
                        } else if(response.status == 'false_date') {
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
						else {
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
						
                    }
                });
            
        });

    });

		

$(document).ready(function(){
bsCustomFileInput.init();
    $('#career_table').DataTable({
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
            //    url: "{{ route('career.index') }}",
				url: "<?= url('career_administration/career_transition/career_transition_request/') . '?id_url=' ?>" + global_url_server,
				error: function (jqXHR, textStatus, errorThrown) {
					$('#career_table').DataTable().ajax.reload();
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
					$(row).find('.edit').css('display', 'none');
				}
				if(data['code_app_status'] == 'Revised'){
				//	$(row).find('td:eq(20)').css('background', '#60b8f8');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Rejected'){
				//	$(row).find('td:eq(20)').css('background', '#fe8590');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.edit').css('display', 'none');
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
                data: 'id_career_transaction',
                defaultContent: '',
                orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'reference_number', name: 'reference_number'},
				{data: 'nik_employee', name: 'nik_employee'},
				{data: 'name', name: 'name'},
				{data: 'transition_category', name: 'transition_category'},
				{data: 'transaction_type', name: 'transaction_type'},
				{data: 'employment_status', name: 'employment_status'},
				{data: 'company_destination', name: 'company_destination'},
				{data: 'position_detail', name: 'position_detail'},
				{data: 'position_routing', name: 'position_routing'},
				{data: 'job_grade', name: 'job_grade'},
				{data: 'job_status', name: 'job_status'},
				{data: 'location', name: 'location'},
				{data: 'effective_date', name: 'effective_date',responsivePriority: 3,},
				{data: 'expired_date', name: 'expired_date'},
				{data: 'attachment', name: 'attachment',render: function ( data, type, row ) {
					let return_ = '';
					if(row['attachment_custom'] != ''){
						return_ = '<a href="'+row['attachment_custom']+'" target="_blank">Download File</a>';
					}
					return return_;

					
					// if(data == null){
					// 	return "";
					// }	
					// else{
					// 	return '<a href="../../project/storage/app/public/upload/career/'+ row['id_employee'] +'/'+data+'" target="_blank">Download File</a>';
					// }
				  }
				},
				{data: 'note_revised', name: 'note_revised'},
				{data: 'note_rejected', name: 'note_rejected'},
				{data: 'desc_app_status', name: 'desc_app_status', className: 'text-center', render: function ( data, type, row ) {	
						if(row.code_app_status == 'Cancel'){
							return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_app_status == 'Partial_Approved'){
							return '<span class="badge badge-warning" style="padding:5px;color:white;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_app_status == 'Rejected'){
							return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_app_status == 'Revised'){
							return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
							return '<span class="badge" style="font-size:12px;">'+data+'</span>';
						}
					} 
				},
                {data: 'action', name: 'action', className:'space', orderable: false, render: function (data, type, row) {
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
	id_career_transaction = $(this).attr('id');
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
			   url:"career_transition_request/submit_approve/"+id_career_transaction,
			   beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#career_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Submited!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#career_table').DataTable().ajax.reload();	
					});
				}, 50);
			   },
			   complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});

$(document).on('click', '.cancel', function (event) {
	id_career_transaction = $(this).attr('id');
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
			   url:"career_transition_request/cancel/"+id_career_transaction,
			    beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#career_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#career_table').DataTable().ajax.reload();
					});
				}, 50);
			   },
			    complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});


$(document).on('click', '.delete', function (event) {
	id_career_transaction = $(this).attr('id');
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
			   url:"career_transition_request/destroy/"+id_career_transaction,
			    beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#career_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#career_table').DataTable().ajax.reload();
					});
				}, 50);
			   },
			    complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});

function refresh_data() {
$('#id_old_position_detail').select2();
$('#effective_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#expired_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#request_resign_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#enable_approval, .checkfield').attr('readonly', true);	

resign_category = [
			{
				id: 'R',
				text: 'R'
			},
			{
				id: 'UR',
				text: 'UR'
			},
		];
		
	//	get_employee();
	//	get_position();
	//	get_career_category();
	//	get_employment_status();
		get_approval_status();
	//	get_company();
	//	get_career_type_select();
			
	$('#id_approval').select2({disabled: true});
	$('#enable_approval:checkbox').on('change', function (e) {
		if(this.checked){
			$('#id_approval').select2({disabled: false});
			$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_hierachy') . '?code=Career_Request&emp='?>'+global_emp, function (data) {
				$('#id_approval').select2({
					placeholder: "Select Hierarchy Approval ...",
					data: data,
					});
				});	
			get_approval_status();
		}
		else{
			$('#id_approval_status').val('').trigger('change');
			$('#id_approval').select2({disabled: true});
			$('#id_approval').empty();
		}
		
	});
		$('#select2status').select2({width:'100%'});
}

function get_employee(global_career_rehire){
//	console.log(global_url_server);
	$('#id_employee').empty();
	$('#id_old_position_detail').empty();
	$('#id_old_employment_status').val('');
	$('#old_employment_status').val('');
	$('#old_position_routing').val('');
	$('#old_department').val('');
	$('#old_spv').val('');
	$('#old_principal').val('');
	$('#old_job_grade').val('');
	$('#old_job_status').val('');
	$('#old_location').val('');
	$('#old_company').val('');
	$('#enable_approval').prop('checked', false);
	$('#id_employee').empty();
	$('#id_approval').empty();
	$('#id_approval').select2({disabled: true});
	$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_employee'). '?rehire='  ?>'+ global_career_rehire +"<?= '&id_url='?>"+global_url_server, function (data) {
        $('#id_employee').prepend('<option selected></option>').select2({
					placeholder: "Select Employee ..",
					allowClear: true,
					data: data,
				}).on('change', function (e) {
				//	console.log(global_default_rehire);
					if(global_expired == ""){
						$('#expired_date').val($(this).select2('data')[0].expired_date);
					}
					if($(this).select2('data')[0].id != "" && $(this).select2('data')[0].id_position_detail != null){
					//	console.log($(this).select2('data')[0].id_employee);
						$("#load_id_old_position_detail").css("display","inline");
						$("#load_old_company").css("display","inline");
						$("#load_old_employment_status").css("display","inline");
						$("#load_old_position_routing").css("display","inline");
						$("#load_old_department").css("display","inline");
						$("#load_old_spv").css("display","inline");
						$("#load_old_principal").css("display","inline");
						$("#load_old_job_grade").css("display","inline");
						$("#load_old_job_status").css("display","inline");
						$("#load_old_location").css("display","inline");
						$('#id_old_position_detail').empty();
							$('#id_old_employment_status').val('');
							$('#old_employment_status').val('');
							$('#old_position_routing').val('');
							$('#old_department').val('');
							$('#old_spv').val('');
							$('#old_principal').val('');
							$('#old_job_grade').val('');
							$('#old_job_status').val('');
							$('#old_location').val('');
							$('#old_company').val('');
							$('#enable_approval').prop('checked', false);
							$('#id_approval').empty();
							$('#id_approval').select2({disabled: true});
							global_emp = $(this).select2('data')[0].id_employee;
							$('#enable_approval').prop('checked', true).change();
								$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_position') . '?id_employee=' ?>' +global_emp , function (data) {
									if(data.length > 0){	
									$('#id_old_position_detail').select2({
										data: data,
									});	
									$("#load_id_old_position_detail").hide();
								}
								else{
									$("#load_id_old_position_detail").hide();
									$("#load_old_company").hide();
									$("#load_old_employment_status").hide();
									$("#load_old_position_routing").hide();
									$("#load_old_department").hide();
									$("#load_old_spv").hide();
									$("#load_old_principal").hide();
									$("#load_old_job_grade").hide();
									$("#load_old_job_status").hide();
									$("#load_old_location").hide();
								}
									get_position_detail($('#id_old_position_detail').find("option:first-child").val());					
								});
					}
					else if(global_default_rehire == 'Rehire_Employee'){
						global_old_position = $(this).select2('data')[0].id_old_position_detail;
						global_old_company = $(this).select2('data')[0].id_company;
						$("#load_id_old_position_detail").hide();
						$("#load_old_company").hide();
						$("#load_old_employment_status").hide();
						$("#load_old_position_routing").hide();
						$("#load_old_department").hide();
						$("#load_old_spv").hide();
						$("#load_old_principal").hide();
						$("#load_old_job_grade").hide();
						$("#load_old_job_status").hide();
						$("#load_old_location").hide();
						$('#id_old_position_detail').empty();
							$('#id_old_employment_status').val('');
							$('#old_employment_status').val('');
							$('#old_position_routing').val('');
							$('#old_department').val('');
							$('#old_spv').val('');
							$('#old_principal').val('');
							$('#old_job_grade').val('');
							$('#old_job_status').val('');
							$('#old_location').val('');
							$('#old_company').val('');
							$('#enable_approval').prop('checked', false);
							$('#id_approval').empty();
							$('#id_approval').select2({disabled: true});
						checkpos(global_old_position,global_old_company);
					}
					else{
						$("#load_id_old_position_detail").hide();
						$("#load_old_company").hide();
						$("#load_old_employment_status").hide();
						$("#load_old_position_routing").hide();
						$("#load_old_department").hide();
						$("#load_old_spv").hide();
						$("#load_old_principal").hide();
						$("#load_old_job_grade").hide();
						$("#load_old_job_status").hide();
						$("#load_old_location").hide();
						$('#id_old_position_detail').empty();
							$('#id_old_employment_status').val('');
							$('#old_employment_status').val('');
							$('#old_position_routing').val('');
							$('#old_department').val('');
							$('#old_spv').val('');
							$('#old_principal').val('');
							$('#old_job_grade').val('');
							$('#old_job_status').val('');
							$('#old_location').val('');
							$('#old_company').val('');
							$('#enable_approval').prop('checked', false);
							$('#id_approval').empty();
							$('#id_approval').select2({disabled: true});
					}
					if(global_type == 'Employment Status Changes' || global_type == 'Pass Orientation' || global_type == 'Failed Orientation'){
						$('#expired_date').val(global_expired);
					}
				});   
        }).fail(function (data) { // Call failed
            get_employee(global_career_rehire);
		});	
}

function get_position(){
	$('#id_old_position_detail').on('change', function (e) {
			get_position_detail($(this).val());
		});		
}
function get_position_detail(pos_det){
	$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_position_detail') . '?id_position_detail='?>'+pos_det, function (data) {
		$('#id_old_employment_status').val(data[0].id_employment_status);
		$('#old_employment_status').val(data[0].employment_status);
	//	$('#id_old_position_detail').val(data[0].id_position_detail);
	//	$('#old_position_detail').val(data[0].position_detail);
		$('#old_position_routing').val(data[0].position_routing);
		$('#old_department').val(data[0].department);
		$('#old_spv').val(data[0].parent_emp_name);
		$('#old_principal').val(data[0].principal);
		$('#old_job_grade').val(data[0].job_grade);
		$('#old_job_status').val(data[0].job_status);
		$('#old_location').val(data[0].work_location);
		$('#old_company').val(data[0].company);
		$("#load_old_company").hide();
		$("#load_old_employment_status").hide();
		$("#load_old_position_routing").hide();
		$("#load_old_department").hide();
		$("#load_old_spv").hide();
		$("#load_old_principal").hide();
		$("#load_old_job_grade").hide();
		$("#load_old_job_status").hide();
		$("#load_old_location").hide();
		if(global_new == 'new'){
			$('#id_employment_status').val(data[0].id_employment_status).trigger('change');
		}
		
		$('#loader').addClass('hidden');
					
	}).fail(function (data) { // Call failed
            get_position_detail(pos_det);
	});
	return false;
}

const get_career_category = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_career_category') ?>',
            dataType: 'json',
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},	
            success:function(data){
				$('#id_transition_category').select2({
					data: data,
				}).on('change', function (e) {
					if(global_new == 'new'){
						$('#job_position').val('').trigger('change');
						$('#id_dept').val('').trigger('change');
						$('#new_department').val('').trigger('change');
						$('#new_spv').val('').trigger('change');
						$('#new_principal').val('').trigger('change');
						$('#id_position_routing').val('').trigger('change');
						$('#new_position_routing').val('').trigger('change');
						$('#id_position_detail').val('').trigger('change');
						$('#new_position_detail').val('').trigger('change');
						$('#id_employee2').val('').trigger('change');
						$('#emp_replace').val('').trigger('change');
						$('#id_job_grade').val('').trigger('change');
						$('#new_job_grade').val('').trigger('change');
						$('#id_job_status').val('').trigger('change');
						$('#new_job_status').val('').trigger('change');
						$('#id_location').val('').trigger('change');
						$('#new_location').val('').trigger('change');
					}
				//	console.log($(this).select2('data')[0].code);
					global_category = $(this).select2('data')[0].code;
					if($(this).select2('data')[0].code == 'Entity_Movement'){
						$('#effective_date').parent().children('span').children('button').attr('disabled', false);
						$('#transaction_number').attr('disabled', true);
						$("#movement").css("display","inline");
						$("#emp_status").css("display","inline");
						$("#movement2").css("display","inline");
						$("#terminate").css("display","none");
						$("#en_app").css("display","inline");
						$("#emp_entity_move").css("display","inline");
						get_career_type($(this).select2('data')[0].id).then(function(data) {
							let entity_type = 0;
							let join_type = 0;
							 $.each(data, function(idx, item) {
								 if(item.text == 'Entity Movement'){
									entity_type = item.id;
								 }
								 if(item.text == 'Join'){
									join_type = item.id;
								 }																	
							});	
							
							$('#id_transaction_type').empty();
							$('#transaction_number').val('');
							$('#id_transaction_type').select2({
								data: data,
							});
							$("#id_transaction_type").val(entity_type);
							$('#id_transaction_type').select2({
								data: data,
							}).on('change', function (e) {
								global_type = $(this).select2('data')[0].text;
								 var aaList = $("option", e.target);
								 $.each(aaList, function(idx, item) {
									$("option[value='"+join_type+"']").attr('disabled',true);																	
								});					
								$('#transaction_number').attr('disabled', true);
							}).trigger('change');
						});
						global_career_rehire = 'Entity_Movement';
						$('#code_transaction_type').val(global_career_rehire);
						get_employee(null);
											
						$("#save_and_submit").show();
					}
					else if($(this).select2('data')[0].code == 'Movement'){
						$("#eff_date").html('Effective Date');
						$('#effective_date').parent().children('span').children('button').attr('disabled', false);
						$('#transaction_number').attr('disabled', true);
						$("#movement").css("display","inline");
						$("#emp_status").css("display","inline");
						$("#movement2").css("display","inline");
						$("#terminate").css("display","none");
						$("#en_app").css("display","inline");
						$("#emp_entity_move").css("display","none");
						$('#new_position_routing').attr('disabled', false);						
						get_career_type($(this).select2('data')[0].id).then(function(data) {						
							$('#id_transaction_type').empty();
							$('#transaction_number').val('');
							$('#id_transaction_type').select2({
								data: data,
							}).on('change', function (e) {							
							if(global_category == 'Movement'){
							/*	get_company_session().then(function(res) {
									get_employment_status(global_company);
								});
							*/
								global_type = $(this).select2('data')[0].text;
							//	console.log(global_type);
								if($(this).select2('data')[0].text == 'Employment Status Changes' || $(this).select2('data')[0].text == 'Pass Orientation'){
									if(global_new == 'new'){
										$('#job_position').val('').trigger('change');
										$('#id_dept').val('').trigger('change');
										$('#new_department').val('').trigger('change');
										$('#new_spv').val('').trigger('change');
										$('#new_principal').val('').trigger('change');
										$('#id_position_routing').val('').trigger('change');
										$('#new_position_routing').val('').trigger('change');
										$('#id_position_detail').val('').trigger('change');
										$('#new_position_detail').val('').trigger('change');
										$('#id_employee2').val('').trigger('change');
										$('#emp_replace').val('').trigger('change');
										$('#id_job_grade').val('').trigger('change');
										$('#new_job_grade').val('').trigger('change');
										$('#id_job_status').val('').trigger('change');
										$('#new_job_status').val('').trigger('change');
										$('#id_location').val('').trigger('change');
										$('#new_location').val('').trigger('change');
										if($('#id_employment_status').find(':selected').text() == 'Permanent'){
											$('#expired_date').val('').trigger('change');
										}
									}
									$('#new_position_routing').attr('disabled', true);
									$("#movement2").css("display","none");
								}
								else if($(this).select2('data')[0].text == 'Pass RPK'){
									if(global_new == 'new'){
										$('#job_position').val('').trigger('change');
										$('#id_dept').val('').trigger('change');
										$('#new_department').val('').trigger('change');
										$('#new_spv').val('').trigger('change');
										$('#new_principal').val('').trigger('change');
										$('#id_position_routing').val('').trigger('change');
										$('#new_position_routing').val('').trigger('change');
										$('#id_position_detail').val('').trigger('change');
										$('#new_position_detail').val('').trigger('change');
										$('#id_employee2').val('').trigger('change');
										$('#emp_replace').val('').trigger('change');
										$('#id_job_grade').val('').trigger('change');
										$('#new_job_grade').val('').trigger('change');
										$('#id_job_status').val('').trigger('change');
										$('#new_job_status').val('').trigger('change');
										$('#id_location').val('').trigger('change');
										$('#new_location').val('').trigger('change');
									}
									$('#new_position_routing').attr('disabled', true);
									$("#movement2").css("display","none");
								}
								else if($(this).select2('data')[0].text == 'Temporary Assignment'){
									$('#new_position_routing').attr('disabled', false);
									$("#movement2").css("display","inline");
								/*	setTimeout(function () {						
										get_company(global_category).then(function(res) {
										});
									}, 1000);
								*/	
								}
								else{
									$('#new_position_routing').attr('disabled', false);
									$("#movement2").css("display","inline");
								}					
							}
							
							}).trigger('change');
						});
						
						global_career_rehire = null;
						$('#code_transaction_type').val(global_career_rehire);
						get_employee(null);
						$("#save_and_submit").show();
						//console.log($('#id_transition_category').find(':selected').val());
					//	get_career_type($('#id_transition_category').find(":selected").val(),$(this).select2('data')[0].code);
					}
					else if($(this).select2('data')[0].code == 'Join'){
						$("#eff_date").html('Effective Date');
						$("#movement").css("display","inline");
						$("#movement2").css("display","inline");
						$("#terminate").css("display","none");
						$("#en_app").css("display","none");
						$("#emp_entity_move").css("display","none");
						$("#emp_status").css("display","inline");
						$('#new_position_routing').attr('disabled', false);
					//	console.log($(this).select2('data')[0].code);
						$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_career_type') . '?id=' ?>' + $(this).select2('data')[0].id +"<?= '&var_type='?>"+ $(this).select2('data')[0].code, function (data) {
							let rehire_type = 0;
							let new_type = 0;
							 $.each(data, function(idx, item) {
								 if(item.text == 'Rehire Employee'){
									rehire_type = item.id;
								 }
								 if(item.text == 'New Employee'){
									new_type = item.id;
								 }																	
							});	
						//	console.log(trans_type);
							$('#id_transaction_type').empty();
							$('#transaction_number').val('');
							$('#id_transaction_type').select2({
								data: data,
							});
							$("#id_transaction_type").val(rehire_type);
							$('#id_transaction_type').select2({
								data: data,
							}).on('change', function (e) {	
								global_type = $(this).select2('data')[0].text;
								 var aaList = $("option", e.target);
								 $.each(aaList, function(idx, item) {
									//	$("option[value='88']").attr('selected',true).trigger('change');
										$("option[value='"+new_type+"']").attr('disabled',true);																	
								});					
								rehire = $('#id_transaction_type').select2('data')[0].text;
								global_career_rehire = rehire.replace(/\s+/g,'_');
								global_default_rehire = rehire.replace(/\s+/g,'_');
								if(global_career_rehire == 'Rehire_Employee'){
									$("#id_employment_status").css("display","inline");
									$('#effective_date').parent().children('span').children('button').attr('disabled', false);
									$('#code_transaction_type').val(global_career_rehire);
								/*	get_company_session().then(function(res) {
										get_employment_status(global_company);
									});
								*/
									get_employee(global_career_rehire);
								}
								$('#transaction_number').attr('disabled', true);
								
							}).trigger('change');						
						});
						$("#save_and_submit").hide();
					//	get_career_type($('#id_transition_category').find(":selected").val(),$(this).select2('data')[0].code);
					}
					else if($(this).select2('data')[0].code == 'Termination'){
						if(global_new == 'new'){
							$('#expired_date').val('').trigger('change');
							$('#job_position').val('').trigger('change');
							$('#id_dept').val('').trigger('change');
							$('#new_department').val('').trigger('change');
							$('#new_spv').val('').trigger('change');
							$('#new_principal').val('').trigger('change');
							$('#id_position_routing').val('').trigger('change');
							$('#new_position_routing').val('').trigger('change');
							$('#id_position_detail').val('').trigger('change');
							$('#new_position_detail').val('').trigger('change');
							$('#id_employee2').val('').trigger('change');
							$('#emp_replace').val('').trigger('change');
							$('#id_job_grade').val('').trigger('change');
							$('#new_job_grade').val('').trigger('change');
							$('#id_job_status').val('').trigger('change');
							$('#new_job_status').val('').trigger('change');
							$('#id_location').val('').trigger('change');
							$('#new_location').val('').trigger('change');
						}
						get_resign_category();
						$("#eff_date").html('Terminate Date');
						$('#effective_date').parent().children('span').children('button').attr('disabled', false);
						$('#transaction_number').attr('disabled', true);
						$("#movement").css("display","none");
						$("#emp_status").css("display","none");
						$("#movement2").css("display","none");
						$("#emp_entity_move").css("display","none");
						$("#terminate").css("display","inline");
						$("#en_app").css("display","inline");
						
						get_career_type($(this).select2('data')[0].id).then(function(data) {
							$('#id_transaction_type').empty();
							$('#transaction_number').val('');
							$('#id_transaction_type').select2({
								data: data,
							}).on('change', function (e) {
								global_type = $(this).select2('data')[0].text;
								if(global_category == 'Termination'){
									global_terminate_reason = $(this).select2('data')[0].text;
									if(global_new == 'new'){
										get_terminate_reason($(this).select2('data')[0].text);
									}
								}
							}).trigger('change');
						});
						
						global_career_rehire = 'Termination';
						$('#code_transaction_type').val(global_career_rehire);
						
						get_employee(null);
						$("#save_and_submit").show();
					//	get_career_type($('#id_transition_category').find(":selected").val(),$(this).select2('data')[0].code);
					}
					
				}).trigger('change');
			},
			complete: function(){
		//		$('#loader').addClass('hidden');
			},			
	   });
        return result;
    } catch (error) {
        get_career_category();
    }	
}

const get_company = async (global_category) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_company') ?>',
			data:{
				type:global_category,
			},
            dataType: 'json',
			beforeSend: function () {
		//		$('#loader').removeClass('hidden');
			},
            success:function(res){
			//	console.log(data);
				$('#id_company_destination').empty();
				$('#id_company_destination').select2({
					data: res,
				}).on('change', function (e) {							
					global_company = $('#id_company_destination').select2('data')[0].id;
				}).trigger('change');
			},
			complete: function(){
		//		$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
        get_company(global_category);
    }	
}

const get_company_session = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_company_session') ?>',
			data:{
				id:global_company,
			},
            dataType: 'json',
			beforeSend: function () {
		//		$('#loader').removeClass('hidden');
			},
            success:function(res){
				$('#id_company_destination').empty();
				$('#id_company_destination').select2({
					data: res,
				});
			},
			complete: function(){
		//		$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
        get_company_session();
    }	
}

/*
$(document).on('change', '#id_transition_category', function (event, istrigger) {  
    if(!istrigger){
		if(global_new == 'new'){
			if(global_category == 'Entity_Movement'){
				get_company(global_category).then(function(res) {
					get_employment_status(global_company);
					get_shift(global_company);
					get_timezone(global_company);
				});
			}
			else{
				global_company = '<?= session("id_company"); ?>';
				get_company_session().then(function(res) {
					get_employment_status(global_company);
				});
			}
		}
	}
});
*/

$(document).on('change', '#id_transaction_type', function (event, istrigger) {  
    if(!istrigger){
		if(global_new == 'new'){
			if(global_type == 'Temporary Assignment'){
				get_company(global_category).then(function(res) {
					get_employment_status(global_company);
				});
			}
			else if(global_type == 'Entity Movement'){
				get_company(global_category).then(function(res) {
					get_employment_status(global_company);
					get_shift(global_company);
					get_timezone(global_company);
				});
			}				
			else{
				global_company = '<?= session("id_company"); ?>';
				get_company_session().then(function(res) {
					get_employment_status(global_company);
				});
			}
		}
	}
});

$(document).on('change', '#id_company_destination', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		get_employment_status($(this).select2('val'));
		get_shift($(this).select2('val'));
		get_timezone($(this).select2('val'));
	}
});

function get_employment_status(global_company){
	$.ajax({
		url: "<?= url('career_administration/career_transition/career_transition_request/get_employment_status') ?>",
		data:{
			id:global_company,
		},
		beforeSend: function () {
			$('#id_employment_status').empty();
		},
		success:function(data){
			$('#id_employment_status').empty();
			$('#id_employment_status').select2({
			placeholder: "Select Employment Status ..",
			data: data,
			}).on('change', function (e) {
				if($('#id_employment_status').select2('data')[0].text == 'Permanent' && $('#id_transaction_type').find(':selected').text() == 'Employment Status Changes' || $('#id_transaction_type').find(':selected').text() == 'Pass Orientation' || $('#id_transaction_type').find(':selected').text() == 'Failed Orientation'){
					$('#expired_date').val('').trigger('change');
				}
			});
		},
	});
}

const get_employment_status_edit = async (global_company) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_employment_status') ?>',
			data:{
				id:global_company,
			},
            dataType: 'json',
			beforeSend: function () {
				$('#id_employment_status').empty();
			},
            success:function(res){
				$('#id_employment_status').empty();
				$('#id_employment_status').select2({
				placeholder: "Select Employment Status ..",
				data: res,
				});
			},
        });
        return result;
    } catch (error) {
        get_employment_status_edit(global_company);
    }	
}

function get_approval_status() {
	$.getJSON('<?= url('career_administration/career_transition/career_transition_request/get_approval_status') ?>', function (data) {
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
        });	
}	

function get_resign_category(){
		$('#resign_category').select2({
            data: resign_category,
			placeholder: 'Select Reason Category'
        });
}

const get_career_type = async (id_terminate_reason) => {
	let result;
    try {
        result = await $.ajax({
			url: '<?= url('career_administration/career_transition/career_transition_request/get_career_type') ?>',
			data:{
				id: id_terminate_reason,
			},
			dataType: 'json',
			success:function(res){
			},
		});
        return result;
    } catch (error) {
        get_career_type(id_terminate_reason);
    }
}

const get_terminate_reason = async (code_terminate) => {
	let result;
    try {
        result = await $.ajax({
			url: '<?= url('career_administration/career_transition/career_transition_request/get_terminate_reason') ?>',
			data:{
				code: code_terminate,
			},
			dataType: 'json',
			success:function(res){
				$('#id_terminate_reason').empty();
				$('#id_terminate_reason').select2({
				data: res,
				});
			},
		});
        return result;
    } catch (error) {
        get_terminate_reason(code_terminate);
    }
}

const get_shift = async (global_company) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_shift') ?>',
			data:{
				id:global_company,
			},
            dataType: 'json',
			beforeSend: function () {
				$('#id_new_shift_group').empty();
			},
            success:function(res){
				$('#id_new_shift_group').empty();
				$('#id_new_shift_group').prepend('<option selected></option>').select2({
					data: res,
					allowClear:true,
					placeholder:'Select Work Hours'
				});	
			},
        });
        return result;
    } catch (error) {
        get_shift(global_company);
    }	
}

const get_timezone = async (global_company) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('career_administration/career_transition/career_transition_request/get_timezone') ?>',
			data:{
				id:global_company,
			},
            dataType: 'json',
			beforeSend: function () {
				$('#id_new_timezone').empty();
			},
            success:function(res){
				$('#id_new_timezone').empty();
				$('#id_new_timezone').prepend('<option selected></option>').select2({
					data: res,
					allowClear:true,
					placeholder:'Select Time Zone'
				});	
			},
        });
        return result;
    } catch (error) {
        get_timezone(global_company);
    }	
}

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(id_recommendation_header) {
	let res = {
        id_recommendation_header: id_recommendation_header,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('employee/employee/recommendation_form/download') }}";
    window.open(url+'?'+param, '_blank');
}
</script>

@endsection