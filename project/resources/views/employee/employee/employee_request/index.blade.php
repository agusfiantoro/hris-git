@extends('adminlte::page')
@section('title', 'Employee Request')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Request</h5>
                <div class="card-tools">
                    <button type="button" name="add_new" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Employee Request</button>
                </div>
            </div>

            <div class="card-body">
			<div class="alert alert-warning alert-dismissible" style="background:#fffcf5;">
                  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                  <li style="padding-bottom: 10px;">
                    Jika form request <b><i>hanya disimpan</i></b>, pilih <span class="btn-success btn-sm" style="padding:0 8px 2px 7px;"><span class="fas fa-save" style="font-size:10px;"></span> Save as draft</span> 
                    , selanjutnya klik <span class="btn-info btn-sm" style="padding:0 8px 2px 7px;"><span class="fas fa-paper-plane" style="font-size:10px;"></span></span> <b><i>jika ingin diproses</i></b></li>
                  <li>Jika form request <b><i>langsung diproses</i></b>, pilih <span class="btn-info btn-sm" style="padding:0 8px 2px 7px;"><span class="fas fa-paper-plane" style="font-size:10px;"></span> Save & Submit</span></li>
             </div>
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="employee_request_table" class="display nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Reference Number</th>
                            <th>Request By</th>
                            <th data-priority="1">Request Type</th>
                            <th>Note Revised</th>
                            <th>Note Rejected</th>
                            <th>Reference Req Cancel</th>
                            <th data-priority="3" style="white-space:nowrap;">Approval Status</th>
                            <th data-priority="2" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
				
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_employee_request"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="card modal-content">
            <form method="post" id="employee_requestForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Employee Request</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 450px;overflow-y: auto;">
                    <div class="row">
						<div class="col-md-6">                           
							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">Reference Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="reference_number" id="reference_number" value="{{ $codeid }}" readonly="readonly" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_numberError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div -->
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request By</label>
								<div class="col-sm-8">
									<input name="id_request_header" id="id_request_header" type="hidden">
									<input name="request_cancel" id="request_cancel" type="hidden">
                                    <select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Type</label>
								<div id="id_request_type_new" class="col-sm-8">
                                    <select name="id_request_type" id="id_request_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>									
                                    <span class="invalid-feedback" role="alert" id="id_request_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
								<div id="id_request_type_cancel" class="col-sm-8" style="display: none;">
									<input id="type_cancel" type="text" class="form-control form-control-sm" disabled>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Type</label>
								<div class="col-sm-8">
                                    <select name="id_leave_type" id="id_leave_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_leave_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Overtime Type</label>
								<div class="col-sm-8">
                                    <select name="id_overtime_type" id="id_overtime_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_overtime_typeError">
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
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-3" style="display:none;">
									  <select id="attachment_type" name="attachment_type" class="form-control form-control-sm select2">
										<option value="image">Image</option>
										<option value="pdf">PDF</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="attachment_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
								 <div class="col-sm-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input p-0" id="attachment" onchange="uploadFile()">
  								  <div style="font-size:10px;margin-top:-5px;font-family:arial;"><i>jpg,jpeg,png(No Max) / pdf(Max 1 Mb)</i></div>
								  <input id="file_name" name="file_name" type="hidden">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<img id="attach" src="#" width="70px" height="60px" style="display:none;margin-bottom:60px;">
									<a id="attach_pdf" href="#" width="70px" height="60px" style="display:none;margin-bottom:60px;" target="_blank">Download File</a>
								  <label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Select File</i></label>
								</div>
								<progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
								  <label id="status_bar"></label>
								  <b id="loaded_n_total" class="text-success"></b>
							   </div>							   
                            </div>
                         
							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>							
							   <div class="col-md-8">
								<div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input form-control form-control-sm" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB (pdf,doc,docx,jpg,jpeg,png)</i></label>
								</div>
								
							   </div>
							</div -->
							
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
                                <label class="col-sm-4 col-form-label">Need Delegate Approval</label>
								<div class="col-sm-8">
									<input type="checkbox" name="delegate_approval" id="delegate_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="delegate_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Enable Approval</label>
								<div class="col-sm-8">
									<input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
									<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" hidden>
										<input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
										<input name="end_date" id="end_date" class="form-control form-control-sm" hidden>
                                    <span class="invalid-feedback" role="alert" id="enable_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">Enable Approval</label>
                                <div class="col-sm-8">
                                    <select name="enable_approval" id="enable_approval" class="form-control form-control-sm">
                                        <option value="0">False</option>
                                        <option value="1">True</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="enable_approvalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div -->
							<!-- div class="row">
								<label class="col-sm-4 col-form-label">Start Date to End Date</label>

								<div class="col-sm-8">
									<div class="input-group">
										<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" hidden>
										<input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
										<input name="end_date" id="end_date" class="form-control form-control-sm" hidden>										
										<div class="input-group-append">
												<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
									</div>
									
								</div>
							</div -->
							
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
							<div id="id_approval_status_cancel" class="row">
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
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_employee_request_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-request" data-toggle="pill" href="#menu-request" role="tab" aria-controls="link_tab_menu-request" aria-selected="true">List Request<span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-status" data-toggle="pill" href="#menu-status" role="tab" aria-controls="link_tab_menu-status" aria-selected="true">List Approval Status<span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_employee_request_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-request" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px;">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_request_detail" hidden><span class="fas fa-plus"></span> Add Request Detail</button>
                                        </div>
                                        <div class="col-md-12"  style="overflow-y: scroll;">
                                            <table id="table_employee_request" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th scope="col" style="white-space:nowrap;">No.</th>
                                                        <th scope="col">Employee</th>
                                                        <th scope="col" style="width:80px;">Leave Balance</th>
                                                        <th scope="col" style="width:250px;">Request Start Date </th>
                                                        <th scope="col" style="width:250px;">Request End Date</th>
                                                        <th scope="col" style="width:100px;">Days Type</th>
                                                        <th scope="col" align="center" style="width:100px;">Qty Days (Leave/CDO)</th>
                                                        <!-- th style="width:350px;">Actual Start to End Date</th -->
                                                        <!-- th scope="col">Delegation To</th -->
														<!-- th style="white-space:nowrap;">Action</th -->
                                                    </tr>
                                                </thead>
                                                <tbody id="table_employee_request_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_employee_requestError">
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
                    <span class="text-bold text-danger leave_balance_expired" style="display:none;">Your Leave Quota is expired for this date</span>
                    <button type="submit" class="btn btn-info btn-sm " id="save_and_submit"></button>&nbsp;
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save as draft</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> 
                </div>
                <!-- div class="modal-footer">
					<button type="submit" class="submit_approve btn btn-sm btn-info" id="submit_button"><i class="fas fa-paper-plane"></i> Submit</button>&nbsp;
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>         
                </div -->
            </form>
			     <div style="display:none;">
                <table id="sample_table_employee_request">
                    <tr id="">
                        <td data-label="No."><span class="sn"></span></td>              
                        <td scope="row" data-label="Employee">
								<input name="emprequest[0][id_request_detail]" id="emprequest_0_id_request_detail" type="hidden" class="form-control form-control-sm id_request_detail_input">
                                <select name="emprequest[0][id_employee]" id="emprequest_0_id_employee" class="form-control form-control-sm select2 id_employee_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_employee_input_error" role="alert" id="emprequest_0_id_employeeError">
                                    <strong></strong>
                                </span>
                        </td>
						
						<td data-label="Leave Balance" align="center">
								<div id="load_head_balance">
									<input type="text" id="emprequest_0_leave_balance" class="form-control form-control-sm leave_balance_input" style="font-weight:bold;color:red;" disabled>
								</div>
								<br>
								<div id="load_leave_balance" style="display:none;">
									<span class="spinner-border spinner-border-sm"></span>
								</div>
                        </td>
						<td data-label="Request Start Date">		
								<div class="input-group">									
										<input name="emprequest[0][request_start_to]" id="emprequest_0_request_start_to" class="form-control form-control-sm request_start_to_input" autocomplete="off" maxlength="0">
										<input name="emprequest[0][actual_start_to]" id="emprequest_0_actual_start_to" class="form-control form-control-sm actual_start_to_input" hidden>
										<div class="input-group-append">										
											<a href="#" class="clear_start_date input-group-text">
												<i class="fa fa-times" style="font-size:12px;"></i>
											 </a>
										</div>
										<div class="input-group-append">										
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
										
								</div>
								<span class="actual">Actual Time In :</span>
								<div>
									<span id="load_time_in" class="spinner-border spinner-border-sm" style="display:none;"></span>
									<b id="time_in"></b>
								</div>
								<span class="invalid-feedback request_start_to_input_error" role="alert" id="emprequest_0_request_start_toError">
                                    <strong></strong>
                                </span>
                        </td>
						<td data-label="Request End Date">		
								<div class="input-group">										
										<input name="emprequest[0][request_end_to]" id="emprequest_0_request_end_to" class="form-control form-control-sm request_end_to_input" autocomplete="off" maxlength="0">
										<input name="emprequest[0][actual_end_to]" id="emprequest_0_actual_end_to" class="form-control form-control-sm actual_end_to_input" hidden>
										<div class="input-group-append">										
											<a href="#" class="clear_end_date input-group-text">
												<i class="fa fa-times" style="font-size:12px;"></i>
											 </a>
										</div>
										<div class="input-group-append">
											<span class="input-group-text far fa-calendar form-control-sm"></span>
										</div>
								</div>
								
									<span class="actual">Actual Time Out :</span>
								<div>	
									<span id="load_time_out" class="spinner-border spinner-border-sm" style="display:none;"></span>
									<b id="time_out"></b>
								</div>
								<span class="invalid-feedback request_end_to_input_error" role="alert" id="emprequest_0_request_end_toError">
                                    <strong></strong>
                                </span>
                        </td>
						<td data-label="Days Type">								
                                <select name="emprequest[0][day_type]" id="emprequest_0_day_type" class="form-control form-control-sm select2 day_type_input" style="width: 100%;"></select>
                                <span class="invalid-feedback day_type_input_error" role="alert" id="emprequest_0_day_typeError">
                                    <strong></strong>
                                </span>
                        </td>
						<td data-label="Qty Days (Leave/CDO)" align="center">
								<div id="load_head_qty">
									<input type="number" name="emprequest[0][qty_days]" id="emprequest_0_qty_days" class="form-control form-control-sm qty_days_input" style="font-weight:bold;color:blue;" readonly>
								</div>
								<br>
								<div id="load_qty_days" style="display:none;">
									<span class="spinner-border spinner-border-sm"></span>
								</div>
                        </td>
						
						<!-- td>		
								<div class="input-group">
										<input name="emprequest[0][actual_daterange]" id="emprequest_0_actual_daterange" type="daterange" class="form-control form-control-sm actual_daterange_input">
										<input name="emprequest[0][actual_start_to]" id="emprequest_0_actual_start_to" class="form-control form-control-sm actual_start_to_input" hidden>
										<input name="emprequest[0][actual_end_to]" id="emprequest_0_actual_end_to" class="form-control form-control-sm actual_end_to_input" hidden>
										<div class="input-group-append">
												<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
								</div>
								<span class="invalid-feedback actual_daterange_error" role="alert" id="emprequest_0_actual_daterangeError">
                                    <strong></strong>
                                </span>
                        </td -->
						
						<!-- td data-label="Delegation To">								
                                <select name="emprequest[0][id_employee_delegation]" id="emprequest_0_id_employee_delegation" class="form-control form-control-sm select2 id_employee_delegation_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_employee_delegation_input_error" role="alert" id="emprequest_0_id_employee_delegationError">
                                    <strong></strong>
                                </span>
                        </td -->
                        <td>
                    <!-- center>
                        <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center -->
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
    .btn-request-cancel {
        color: #fff;
        background-color: #ff6a79;
        box-shadow: none;
    }
	.btn-request-cancel:hover {
        color: #fff;
        background-color: #f04253;
        box-shadow: none;
    }
    .modal-lg, .modal-xl {
        max-width: 90% !important;
    }
	.checkfield, input[readonly]{
        pointer-events: none;
        touch-action: none;
		background: #e8ebed;
		box-shadow: none;
    }
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
	

@media screen and (max-width: 800px) {
  table#table_employee_request {
    border: 0;
	max-width:100%;
  }

  table#table_employee_request thead {
    border: none;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    max-width:100%;
  }
  
  table#table_employee_request tr {
    border-bottom: 3px solid #ddd;
    display: block;
    margin-bottom: .625em;
	background:white;
  }
  
  table#table_employee_request td {
    border-bottom: 1px solid #ddd;
    display: block;
    text-align: left;
  }
  
  table#table_employee_request td::before {  
    content: attr(data-label);
    float: left;
    font-weight: bold;
  }
  
  table#table_employee_request td::before, .sn {  
   margin:0 0 5px 10px;
  }
  
  table#table_employee_request td:last-child {
    border-bottom: 0;
  }
  table#table_employee_request select.select2-hidden-accessible + .select2-container .select2-selection {
        font-size:12px;
    }
  table#table_employee_request input {
        font-size:12px;
    }
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_code = "";
let global_leave_code = "";
let global_id_request_header = "";
let global_id_request_detail = 0;
let global_employee = [];
let global_day_type = [];
let global_employee_delegation = [];
let global_approval_status = 0;
let global_employee_by = 0;
let global_start_workdays = "";
let global_end_workdays = "";
let global_classname = "";
let global_id_days = "";
let global_leave_type_restrict_by;
let lb = 0;
let qd = 0;
let qty = 0;
let qty_mat = 0;
// let global_day_count = 0;

document.addEventListener('contextmenu', (e) => e.preventDefault());

function ctrlShiftKey(e, keyCode) {
  return e.ctrlKey && e.shiftKey && e.keyCode === keyCode.charCodeAt(0);
}

document.onkeydown = (e) => {
  // Disable F12, Ctrl + Shift + I, Ctrl + Shift + J, Ctrl + U
  if (
    event.keyCode === 123 ||
    ctrlShiftKey(e, 'I') ||
    ctrlShiftKey(e, 'J') ||
    ctrlShiftKey(e, 'C') ||
    (e.ctrlKey && e.keyCode === 'U'.charCodeAt(0))
  )
    return false;
};

function _(el) {
  return document.getElementById(el);
}

function uploadFile() {
	var file = _("attachment").files[0];
	file_type = file.type;
	split_file = file_type.split('/')[0];
if(split_file == 'image'){	
  // alert(file.name+" | "+file.size+" | "+file.type);
  var formdata = new FormData();
  var get_image = $("#file_name").val();
  formdata.append("attachment", file);
  formdata.append("file_name", get_image);
//  console.log(formdata);
  $.ajax({
	xhr: function() {
		var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(event) {
			//	_("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
				var percent = (event.loaded / event.total) * 100;
				_("progressBar").value = Math.round(percent);
				_("status_bar").innerHTML = Math.round(percent) + "% uploaded... please wait";
				}, false);

				xhr.addEventListener("error", function(event) {
					_("status_bar").innerHTML = "Upload Failed";
				}, false);
				
				xhr.addEventListener("abort", function(event) {
					_("status_bar").innerHTML = "Upload Abort";
				}, false);
			
			return xhr;
		  },
		  url: "{{ route('employee_request.upload') }}",
		  type: 'POST',
		  headers: {
			'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			},
			enctype: 'multipart/form-data',
		  cache: false,
		  processData: false, // important
		  contentType: false, // important
		  dataType : 'json',
		  data: formdata,
		  success: function(result) {
				$('#status_bar').html('');	
				$('#loaded_n_total').html(result.message);
				document.getElementById("attach").src = result.path;
				$('#file_name').val(result.image_name);
				$('#attach').css('display','inline');
			}
		});
	}
}

    $(function () {	
		$(document).on('click', '.new', function () {
            global_id_request_header = "";
			global_classname = $(this).attr('name');
            $("#employee_requestForm")[0].reset();
            $("#table_employee_request_body").html("");
            $("#employee_requestForm .modal-title").html("<span class='fas fa-plus'></span> Form Employee Request");
            $(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#employee_requestForm input").removeClass("is-invalid");
		//	$('#save_button').attr('class', 'btn btn-sm btn-success');
         //   $('#save_button').html('<i class="fas fa-save"></i> Save');
			$("#edit_button").css("display","none");
			$("#submit_button").css("display","none");
            $("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').addClass('addForm');
			
			$('#enable_approval').prop('checked', true).change();
			$('#delegate_approval, .checkfield').attr('readonly', true);
			$('#enable_approval, .checkfield').attr('readonly', true);
			$.each([1], function (i, item) {
					$('#new_employee_request_detail').trigger('click');
				});
			$('#request_cancel').val(global_classname);
			global_approval_status.forEach((approval_status) => {
				if(approval_status.code == 'New') {
					$('#id_approval_status').val(approval_status.id).trigger('change');
				}
			})
            $('#modal_form_employee_request').modal('show');		

       });
	      
		
		var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('employee_request.save') }}";
			$(this).closest(".card").find("employee_requestForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('employee_request.update') }}";
			$(this).closest(".card").find("employee_requestForm").submit();
		  });
		
		 $('#employee_requestForm').submit(function (e) {
            e.preventDefault();

            let thisButtonId = e.originalEvent.submitter.id;
            if(thisButtonId == 'save_and_submit'){
                let addForm = $("#save_and_submit").hasClass('addForm');
                if(addForm == true){
                    AjaxUrl = "{{ route('employee_request.save') }}";
                } else {
                    AjaxUrl = "{{ route('employee_request.update') }}";
                }
            }

			var formData = new FormData(this);
            $(".invalid-feedback").children("strong").text("");
            $("#employee_requestForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
				//	url: global_id_request_header == '' ? "{{ route('employee_request.save') }}" : "{{ route('employee_request.update') }}",
					url: AjaxUrl,
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
						$('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
							lb = $('#' + obj.id).val();
						});
						$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
							qd = $('#' + obj.id).val();
						});
						if(lb == "" && global_code == 'Attendance_Correction'){
							return true;
						}
						else if(global_classname != 'req_cancel'){
							if(parseFloat(lb) < parseFloat(qd)){
								swal({
									icon: 'error',
									title: 'Oops...',
									dangerMode: true,
									text: 'Leave Request Over Balance'
								}).then(function(){ 
								   $('#loader').addClass('hidden');
								   }
								);							
								return false;
							}
						}
						else if(qd == 0 && global_code == 'Change_Day_off'){
							swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Request Failed'
                            }).then(function(){ 
							   $('#loader').addClass('hidden');
							   }
							);							
							return false;
						}
						else{
							return true;
						}
					},
                    success: function (response) {
                        if (response.status == 'true') {
							let thisIdRequestHeader = response.data;

                            if(thisButtonId == 'save_and_submit'){
                                $.ajax({
                                    url: "employee_request/submit_approve/" + thisIdRequestHeader,
                                    success: function(resp) {
                                        var formDataSubmit = resp.data;
                                        $.ajax({
                                            type: 'POST',
                                            headers: {
                                                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                                            },
                                            url: "{{ route('mail.new_request') }}",
                                            data: {source: formDataSubmit},
                                        });
                                        $('#modal_form_employee_request').modal('hide');
                                        // $('#employee_request_table').DataTable().ajax.reload();
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
                                $('#modal_form_employee_request').modal('hide');
                                swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message
                                }).then(function(){ 
                                    location.reload();
                                });
                                $('#employee_request_table').DataTable().ajax.reload();
                            }
                        } 
                        else if(response.status = 'false_balance'){
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
                                text: 'Something went wrong! ['+response.message+']'
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
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_employee_request_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
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
                                 text: 'Something went wrong! ['+response.statusText+']'
                            });
                        }
                    }
                });
            
        });


        $(document).on('click', '#new_employee_request_detail', function () {
            var content = jQuery('#sample_table_employee_request tr'),
                    size = global_id_request_detail++,
               //     size = 1,
                    element = null,
                    element = content.clone();
					
            element.attr('id','rec-'+size);
        //    element.find('.delete-record').attr('data-id', size);
            element.find('.id_request_detail_input').attr('id', 'emprequest_' + size + '_id_request_detail');
            element.find('.id_request_detail_input').attr('name', 'emprequest[' + size + '][id_request_detail]');
			
			element.find('.id_employee_input').attr('id', 'emprequest_' + size + '_id_employee');
            element.find('.id_employee_input').attr('name', 'emprequest[' + size + '][id_employee]');
            element.find('.id_employee_input_error').attr('id', 'emprequest_' + size + '_id_employeeError');
            element.find('.id_employee_input').select2({
                data: global_employee
            }).attr('readonly', true).on('change', function (e) {
				if(global_code == 'Leave_Request'){
					if(global_employee.length > 0){
						element.find('.leave_balance_input').val($(this).select2('data')[0].leave_quota).trigger('change');
					}
				}
				else{
					element.find('.leave_balance_input').val('').trigger('change');
				}
            }).trigger('change');

			element.find('.leave_balance_input').attr('id', 'emprequest_' + size + '_leave_balance');
			
			
            element.find('.clear_start_date').attr('onclick', 'clear_start_date('+size+')');
            element.find('.clear_end_date').attr('onclick', 'clear_end_date('+size+')');
			
		//	console.log(global_classname);
			if(global_classname != 'req_cancel'){
				
				element.find('.request_start_to_input').attr('id', 'emprequest_' + size + '_request_start_to');
				element.find('.request_start_to_input').attr('name', 'emprequest[' + size + '][request_start_to]');
				element.find('.request_start_to_input_error').attr('id', 'emprequest_' + size + '_request_start_toError');
				
				element.find('.request_start_to_input').keydown(function(e) {
				/*	if (e.keyCode == 8 || e.keyCode == 46) {				
					 return true;
					}
					else{
				*/
						return false;
				//	}
				});
				element.find('.request_start_to_input').dateRangePicker({
					startOfWeek: 'monday',
					format: 'YYYY-MM-DD',
					autoClose: false,
					startDate: moment().subtract(7, 'days').format('YYYY-MM-DD'),
					endDate: global_end_workdays,
					setValue: function(s,s1,s2) {		
						split_start = s1.split(' ')[0];
						split_end = s2.split(' ')[0];
					
						$('#emprequest_' + size + '_request_start_to').val(s1);
						$('#emprequest_' + size + '_request_end_to').val(s2);
						if(global_code == "Leave_Request"){									
							get_count_days(split_start,split_end);
						}
						else if(global_code == "Change_Day_off"){
							get_count_days_od(split_start,split_end);
						}								
					}
				});	
				
				element.find('.actual_start_to_input').attr('id', 'emprequest_' + size + '_actual_start_to');
				element.find('.actual_start_to_input').attr('name', 'emprequest[' + size + '][actual_start_to]');
				
				element.find('.request_end_to_input').attr('id', 'emprequest_' + size + '_request_end_to');
				element.find('.request_end_to_input').attr('name', 'emprequest[' + size + '][request_end_to]');
				element.find('.request_end_to_input_error').attr('id', 'emprequest_' + size + '_request_end_toError');
				
				element.find('.request_end_to_input').keydown(function(e) {
				/*	if (e.keyCode == 8 || e.keyCode == 46) {				
					 return true;
					}
					else{
				*/
						return false;
				//	}
				});
				element.find('.request_end_to_input').dateRangePicker({
					startOfWeek: 'monday',
					format: 'YYYY-MM-DD',
					autoClose: false,
					startDate: moment().subtract(7, 'days').format('YYYY-MM-DD'),
					endDate: global_end_workdays,
					
					setValue: function(s,s1,s2) {		
						split_start = s1.split(' ')[0];
						split_end = s2.split(' ')[0];
					
						$('#emprequest_' + size + '_request_start_to').val(s1);
						$('#emprequest_' + size + '_request_end_to').val(s2);
						if(global_code == "Leave_Request"){									
							get_count_days(split_start,split_end);
						}
						else if(global_code == "Change_Day_off"){
							get_count_days_od(split_start,split_end);
						}								
						}
				});
				
				element.find('.actual_end_to_input').attr('id', 'emprequest_' + size + '_actual_end_to');
				element.find('.actual_end_to_input').attr('name', 'emprequest[' + size + '][actual_end_to]');
			//	get_workdays();
				
				element.find('.day_type_input').attr('id', 'emprequest_' + size + '_day_type');
				element.find('.day_type_input').attr('name', 'emprequest[' + size + '][day_type]');
				element.find('.day_type_input').select2({
					data: global_day_type
				}).on('change', function (e) {
				//	element.find('.qty_days_input').val('0');
					global_id_days = $(this).val();
				//	console.log(qty);
				if(global_leave_code == 'MAT' || global_leave_code == 'MIS'){
					if(global_id_days == 'Full_Day'){
						element.find('.qty_days_input').val(qty_mat);
					}
					else{
						element.find('.qty_days_input').val(parseInt(qty_mat) * 0.5);
					}
				}
				else{
					if(global_id_days == 'Full_Day'){
						element.find('.qty_days_input').val(qty);
					}
					else{
						element.find('.qty_days_input').val(parseInt(qty) * 0.5);
					}
				}
				//	console.log($(this).val());
				}).trigger('change');	
			
				element.find('.qty_days_input').attr('id', 'emprequest_' + size + '_qty_days');
		//	}, 3000);
			}
			
			/*
			element.find('.actual_daterange_input').attr('id', 'emprequest_' + size + '_actual_daterange');
			element.find('.actual_daterange_input_error').attr('id', 'emprequest_' + size + '_actual_daterangeError');
			element.find('.actual_daterange_input').dateRangePicker({
				startOfWeek: 'monday',
				separator : ' to ',
				format: 'YYYY-MM-DD HH:mm',
				autoClose: false,
				time: {
					enabled: true
				},
				defaultTime: moment().startOf('day').toDate(),
				defaultEndTime: moment().endOf('day').toDate(),				
				setValue: function(s,s1,s2)
					{
						$('#emprequest_' + size + '_actual_start_to').val(s1);
						$('#emprequest_' + size + '_actual_end_to').val(s2);
						this.value = s;
					}
			});			
			*/			
		/*	element.find('.id_employee_delegation_input').attr('id', 'emprequest_' + size + '_id_employee_delegation');
            element.find('.id_employee_delegation_input').attr('name', 'emprequest[' + size + '][id_employee_delegation]');
            element.find('.id_employee_delegation_input_error').attr('id', 'emprequest_' + size + '_id_employee_delegationError');
			if($('#delegate_approval:checkbox').is(':checked') == false){
				element.find('.id_employee_delegation_input').select2({
					disabled: true,
				});
			}
			else{
				element.find('.id_employee_delegation_input').select2({
					disabled: false,
					data: global_employee_delegation
				});
			}
		*/	
			/*
			if(global_code == 'Overtime_Request')
			{
				element.find('.actual_daterange_input').attr('disabled', false);
				element.find('.actual_daterange_input').parent().children('span').children('button').attr('disabled', false);
			}
			else{
				element.find('.actual_daterange_input').attr('disabled', true);
				element.find('.actual_daterange_input').parent().children('span').children('button').attr('disabled', true);
			}
			*/
            element.appendTo('#table_employee_request_body');
			 $('#table_employee_request_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

/*	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_employee_request_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
*/
	$(document).on('click', '.edit, .req_cancel', function () {
            let id_request_header = $(this).attr('id');
			global_classname = $(this).attr('name');
            global_id_request_header = id_request_header;
            $("#employee_requestForm")[0].reset();
            $("#table_employee_request_body").html("");
            if(global_classname == 'view'){
                $("#employee_requestForm .modal-title").html("<span class='fas fa-edit'></span> View Employee Request");
            } else {
                $("#employee_requestForm .modal-title").html("<span class='fas fa-edit'></span> Edit Employee Request");
            }
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#employee_requestForm input").removeClass("is-invalid");
			$("#save_button").css("display","none");
            $("#edit_button").css("display","none");
			$('#delegate_approval, .checkfield').attr('readonly', true);
			
			$('#request_cancel').val(global_classname);
		/*	$(document.body).delegate('[type="checkbox"][readonly="readonly"]', 'click', function(e) {
				e.preventDefault();
			});
		*/
            $.ajax({
                url: "<?= url('employee/employee/employee_request/get_request_edit') ?>",
                method: "GET",
                data: {id_request_header: id_request_header},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                    global_id_request_detail = 0;
					 $.each(response.emprequest, function (i, item) {
                        $('#new_employee_request_detail').trigger('click');
                    });
				//	console.log(response);

					if(response.code_status == 'Request_Approval' || response.code_status == 'Approved' || response.code_status == 'Partial_Approved' || response.code_status == 'Cancel'){
						setTimeout(function () {
							$('.checkfield').attr('readonly', true);
							$("#employee_requestForm input").attr('readonly', true);
							$("#sample_table_employee_request input").attr('readonly', true);			
							$("#employee_requestForm select").attr('readonly', true);
							$("#sample_table_employee_request select").attr('readonly', true);
							$("#new_employee_request_detail").css("display","none");
							$('.request_start_to_input').attr('readonly', true);
							$('.request_end_to_input').attr('readonly', true);
							$(".input-group-append").css("display","none");
							$('#daterange').attr('readonly', true);
						//	$('#id_approval').select2({disabled: true});
							
                            $("#save_and_submit").hide().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');

							$("#submit_button").css("display","none");
							$(".delete-record").css("display","none");
							if(global_classname == 'req_cancel'){
								$("#employee_requestForm .modal-title").html("<span class='fa fa-window-close'></span> Cancel Employee Request");
								$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Cancel & Submit').addClass('addForm');
								$('#request_cancel').val(global_classname);
								$('#note').attr('readonly', false);
									$("#id_request_type_new").css("display","none");
									$("#id_request_type_cancel").css("display","inline");
									$("#id_approval_status_cancel").css("display","none");	
								setTimeout(function () {		
									$('#note').val('').trigger('change');
									$('#type_cancel').val('Cancel Leave').trigger('change');
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
								}, 1000);
							}
						}, 2000);
						setTimeout(function () {
							$('#id_leave_type').attr("readonly", true);
						}, 4000);	
                        $(document).ajaxStop(function() {
                            if(response.code_status == 'Approved'){
                                $('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
                                    $('#' + obj.id).val(response.leave_balance_remaining_this_request);
                                });
                            }
                        });
					} else {
                        if(response.code != 'Attendance_Correction'){
                            if(response.leave_balance_status_this_request == 'A'){
                                $("#edit_button").css("display","block");
                                $("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                            } else {
                                $(".leave_balance_expired").css("display","block");
                                $("#save_and_submit").hide().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                            }
                            $(document).ajaxStop(function() {
                                $('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
                                    let selected_request_type = $('#id_request_type').find(":selected").text();
                                    let selected_leave_type = $('#id_leave_type').find(":selected").text();

                                    if(selected_request_type != 'Attendance Correction' && response.description == selected_leave_type){
                                        $('#' + obj.id).val(response.leave_balance_remaining_this_request);
                                    }
                                });
                            });
                        } else {
                            $("#edit_button").css("display","block");
                            $("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                        }
                    } 
					
                    $('#id_request_header').val(response.id_request_header).trigger('change');
                    $('#reference_number').val(response.reference_number).trigger('change');
                    $('#id_employee_request').val(response.id_employee_request).trigger('change');
					$('#start_date').val(response.start_date).trigger('change');
                    $('#end_date').val(response.end_date).trigger('change');
                    $('#note').val(response.note).trigger('change');
					$('#enable_approval, .checkfield').attr('readonly', true);
				//	$('#enable_approval:checkbox').on('change', function (e) {
					
						setTimeout(function () {				
							if(response.enable_approval == 1){		
									$('#enable_approval').prop('checked', true);
								//	$('#enable_approval').attr('readonly', 'readonly');									
								//	$('#id_approval').select2({disabled: false});
								if(global_classname != 'req_cancel'){	
									if(response.start_date == null && response.end_date == null){
										//	$('#id_approval').select2({disabled: false});
											$('#daterange').attr('readonly', true);
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
										//	$('#id_approval').select2({disabled: false});
											$('#daterange').attr('readonly', true);
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
								}
								
							}
																
						}, 1000);
					
						setTimeout(function () {
							if(response.delegate_approval == 1){	
									$('#delegate_approval').prop('checked', true);	
								//	setTimeout(function () {
											$.getJSON('<?= url('employee/employee/employee_request/get_employee_delegate') ?>', function (data) {
												global_employee_delegation = data;
												$('#table_employee_request').find('.id_employee_delegation_input').each(function (i, obj) {
													$('#' + obj.id).empty();
													$('#' + obj.id).select2({
														disabled: false,
														data: global_employee_delegation
													});
												});
											});
								//	}, 1000);
								}
						}, 1500);
				//	}).trigger('change');
					
					$.getJSON('<?= url('employee/employee/employee_request/get_hierachy') . '?code=' ?>' + response.code, function (data) {
						$('#id_approval').select2({
							allowClear: true,
							data: data,
							});
						});		
									
					
                    setTimeout(function () {
                        $('#table_employee_request_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_request_detail_input').val(response.emprequest[index].id_request_detail);
                            $(this).find('.id_employee_input').val(response.emprequest[index].id_employee).trigger('change');
							
							if(global_code != 'Attendance_Correction'){
								$(this).find('.request_start_to_input').val(response.emprequest[index].request_start_to.split(' ')[0]).trigger('change');
								$(this).find('.request_end_to_input').val(response.emprequest[index].request_end_to.split(' ')[0]).trigger('change');
							}
							else{
								$(this).find('.request_start_to_input').val(response.emprequest[index].request_start_to).trigger('change');
								$(this).find('.request_end_to_input').val(response.emprequest[index].request_end_to).trigger('change');
							}
							
						//	get_workdays();
											
							$(this).find('.day_type_input').val(response.emprequest[index].day_type).trigger('change');
							$(this).find('.qty_days_input').val(response.emprequest[index].qty_days).trigger('change');
						//	$(this).find('.actual_daterange_input').val(response.emprequest[index].actual_start_to + ' to ' + response.emprequest[index].actual_end_to);
							$(this).find('.actual_start_to_input').val(response.emprequest[index].actual_start_to).trigger('change');
                            $(this).find('.actual_end_to_input').val(response.emprequest[index].actual_end_to).trigger('change');
							$(this).find('.id_employee_delegation_input').val(response.emprequest[index].id_employee_delegation).trigger('change');     
							
                        });     
					$('#id_leave_type').val(response.id_leave_type).trigger('change');
                    }, 3000);
						
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
								url: "<?= url('employee/employee/employee_request/index_status').'?id_request_header='?>"+response.id_request_header+"<?= '&id_approval='?>"+response.id_approval,							
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
					
                    if(response.code_status == 'Revised'){
					   $('#id_request_type').val(response.id_request_type).attr('readonly', true).trigger('change');
                    } else {
                       $('#id_request_type').val(response.id_request_type).trigger('change'); 
                    }
                //    $('#attachment').val(response.attachment).trigger('change');
                    if(response.attachment_type != null){
                        if(response.attachment_type == 'image'){
                            if(response.attachment != null){
                                $('#attach').css('display','inline');
                            }
                            document.getElementById("attach").src = "data:image;base64,"+response.attachment;
                        }
                        else if(response.attachment_type == 'pdf'){
                            if(response.attachment != null){
                                $('#attach_pdf').css('display','inline');
                            }                       
                            document.getElementById("attach_pdf").href = "data:application/pdf;base64,"+response.attachment;
                        }  
                    } else {
                        if(response.attachment != null){
                            let file = response.storagePath;
                            let extension = file.match(/\.([^\./\?]+)($|\?)/)[1];
                            if(extension == 'pdf'){
                                $('#attach_pdf').css('display','inline').attr('href', file);
                            } else {
                                $('#attach').css('display','inline').attr('src', file);
                            }
                        }
                    }
					                 
                    $('#attachment_type').val(response.attachment_type).trigger('change');
                    $('#id_approval').val(response.id_approval).trigger('change');
                    $('#id_approval_status').val(response.id_approval_status).trigger('change');
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#select2status').val(response.status).trigger('change');
                    $('#id_overtime_type').val(response.id_overtime_type).trigger('change');														
                },
                complete: function(){
					setTimeout(function () {
						$('#loader').addClass('hidden');
					}, 4000)
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
					
            $('#modal_form_employee_request').modal('show');
	
        });

    });
$(document).ready(function(){
	
	bsCustomFileInput.init();
	
	$('#employee_request_table').DataTable({
            processing: true,
       //     serverSide: true,
        //    scrollY: true,
			responsive: true,
            ajax: {
                url: "{{ route('employee_request.index') }}",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#employee_request_table').DataTable().ajax.reload();
				}
            },
			rowCallback: function(row, data, index){
			//	$(row).find('td:eq(8)').css('font-weight', 'bold');
				if(data['code_app_status'] == 'New'){
					$(row).find('.req_cancel').css('display', 'none');
					if(data['code_request_type'] == 'Cancel_Leave'){
						$(row).find('.edit').css('display', 'none');
					}
				}
				if(data['code_app_status'] == 'Approved'){
				//	$(row).find('td:eq(8)').css('background', '#90fca4');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					if(data['code_request_type'] != 'Leave_Request' && data['code_request_type'] != 'Change_Day_off'){
						$(row).find('.req_cancel').css('display', 'none');
					}
					if(data['leave_code'] == 'ANL' && data['bal_status'] == 'I'){
						$(row).find('.req_cancel').css('display', 'none');
					}
					if(data['enable_cancel'] == 1){
						$(row).find('.req_cancel').css('display', 'none');
					}
					if(data['id_approval'] == null){
						$(row).find('.req_cancel').css('display', 'none');
					}
					
				}
				if(data['code_app_status'] == 'Request_Approval'){
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.req_cancel').css('display', 'none');
				/*	if(data['code_request_type'] == 'Cancel_Leave'){
						$(row).find('.edit').css('display', 'none');
					}
				*/
				}
				if(data['code_app_status'] == 'Cancel'){
				//	$(row).find('td:eq(18)').css('float', 'right');
				//	$(row).find('td:eq(8)').css('background', '#dc3545');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.req_cancel').css('display', 'none');
					if(data['code_request_type'] == 'Cancel_Leave'){
						$(row).find('.edit').css('display', 'none');
					}
				}
								
				if(data['code_app_status'] == 'Revised'){
				//	$(row).find('td:eq(8)').css('background', '#60b8f8');
					$(row).find('.req_cancel').css('display', 'none');
				}
				if(data['code_app_status'] == 'Rejected'){
				//	$(row).find('td:eq(8)').css('background', '#fe8590');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.edit').css('display', 'none');
					$(row).find('.req_cancel').css('display', 'none');
				}
				if(data['code_app_status'] == 'Partial_Approved'){
				//	$(row).find('td:eq(8)').css('background', '#ffdf7e');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.req_cancel').css('display', 'none');
					if(data['code_request_type'] == 'Cancel_Leave'){
						$(row).find('.edit').css('display', 'none');
					}
				}
			  },
            columns: [
				{
                defaultContent: '',
				orderable: false,
				},
                {   // Checkbox select column
                data: 'id_request_header',
                defaultContent: '',
                orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'reference_number', name: 'reference_number'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'desc_request_type', name: 'desc_request_type'},
                {data: 'note_revised', name: 'note_revised'},
                {data: 'note_rejected', name: 'note_rejected'},
                {data: 'reference_number_cancel', name: 'reference_number_cancel'},
                {data: 'desc_app_status', name: 'desc_app_status', className: 'text-center', render: function ( data, type, row ) {	
						if(row.code_app_status == 'Approved'){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_app_status == 'Cancel'){
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
 
});


$(document).on('click', '.submit_approve', function (event) {
	id_request_header = $(this).attr('id');
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
			   url:"employee_request/submit_approve/"+id_request_header,
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
                            url: "{{ route('mail.new_request') }}",
                            data: {
                                source: formData
                            },
                        });
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                            $('#employee_request_table').DataTable().ajax.reload();
                            swal({
                                title: "Data Submited!",
                                icon: "success",
                                buttons: {
                                    confirm: {
                                        className: 'btn-success'
                                    },
                                },
                            }).then(ok => {
                                $('#employee_request_table').DataTable().ajax.reload();
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
	id_request_header = $(this).attr('id');
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
			   url:"employee_request/cancel/"+id_request_header,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#employee_request_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#employee_request_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});


$(document).on('click', '.delete', function (event) {
	id_request_header = $(this).attr('id');
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
			   url:"employee_request/destroy/"+id_request_header,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#employee_request_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					});
				}, 50);
			   }
			  })
        }
    });
});
 
function refresh_data() {
	
		$('#select2status').select2({width:'100%'});
		$('#attachment_type').prepend('<option selected></option>').select2({placeholder: "Select Type ...",allowClear: true,width:'100%'});
	//	$('.new').attr('disabled', true);
		global_day_type = [
			{
				id: 'Full_Day',
				text: 'Full Day'
			},
			{
				id: 'Half_Day1',
				text: 'Half Day (Awal)'
			},	
			{
				id: 'Half_Day2',
				text: 'Half Day (Akhir)'
			}			
		];
		
		function get_employee_by(){
			return new Promise((resolve,reject)=>{
				$.getJSON('<?= url('employee/employee/employee_request/get_employee_by') ?>', function (data) {
					$('#id_employee_request').select2({
						data: data,
					});
					global_employee_by = data[0].id_employee;
				}).done(function() {
					resolve();
				}).fail(function (data) { // Call failed
					get_employee_by();
				});			
			});
		}

		async function callerStart(){
			await get_employee_by();
				get_request_type();
				get_company();
				get_approval_status();
		}

		callerStart();

        $('#employee_request_table').DataTable().ajax.reload();
				
    }
	
async function get_approval(){
		$('#enable_approval').prop('checked', true).change();
		$('#enable_approval:checkbox').on('change', function (e) {
			// $('#loader').removeClass('hidden');
				if(this.checked){
					var today = new Date().toISOString().split('T')[0];
					$("#start_date").val(today);
					$("#end_date").val(today);
					//	$('#id_approval').select2({disabled: false});
						$.getJSON('<?= url('employee/employee/employee_request/get_hierachy') . '?code=' ?>' + global_code, function (data) {
						$('#id_approval').select2({
							placeholder: "No Hierarchy Approval ...",
							allowClear: true,
							data: data,
							});
						// $('#loader').addClass('hidden');
						});	
						$('#daterange').attr('readonly', true);
						$('#daterange').daterangepicker({
						uiLibrary: 'bootstrap4',
						autoApply: true,
						opens: 'center',
						locale: {
						  format: 'YYYY-MM-DD',
						  separator: '   to   ',
						  closeText: 'Clear',
						},
						minDate: new Date()
						}, function(start, end, label) {
						$("#start_date").val(start.format('YYYY-MM-DD'));
						$("#end_date").val(end.format('YYYY-MM-DD'));						
						});	
				}
				else {
					$('#daterange').val('');
					$('#daterange').attr('disabled', true);
					$('#id_approval').val('');
				//	$('#id_approval').select2({disabled: true});
				}
			});
}

async function showLoading() {
	$('#loader').removeClass('hidden');
}

function get_request_type(){
  $.getJSON('<?= url('employee/employee/employee_request/get_request_type') ?>', function (data) {
			let req_type = 0;
			 $.each(data, function(idx, item) {
				 if(item.code == 'Cancel_Leave'){
					req_type = item.id;
				 }																	
			});	
			$('#id_request_type').select2({
                data: data,
            }).on('change', function (e) {
				var aaList = $("option", e.target);
					 $.each(aaList, function(idx, item) {
							$("option[value='"+req_type+"']").attr('disabled',true);																	
					});	
				$('#enable_approval').prop('checked', false);
				$('#daterange').val('');
				$('#daterange').attr('disabled', true);
				$('#id_approval').empty();
			//	$('#id_approval').select2({disabled: true});
				if($(this).select2('data')[0].code == 'Cancel_Leave'){
					global_code = 'Leave_Request';		
				}
				else{
					global_code = $(this).select2('data')[0].code;
				}
					showLoading()
					.then(() => {
						$('#loader').removeClass('hidden');
						get_employee_req();
						get_approval();
					})
					.finally(() => {
						get_workdays()
						// $('#loader').addClass('hidden');
					});

				if($(this).select2('data')[0].code == "Leave_Request" || $(this).select2('data')[0].code == "Cancel_Leave"){
					setTimeout(function () {
						$('#table_employee_request').find('.daterange_input').each(function (i, obj) {
							$('#' + obj.id).val('');
						});
						$('#table_employee_request').find('.day_type_input').each(function (i, obj) {
							$('#' + obj.id).attr('disabled', false);
							$('#' + obj.id).select2({
								data: global_day_type
							});
						});					
						$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
							$('#' + obj.id).val('0');
						});
						$('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
							$('#' + obj.id).val('');
						});
						get_leave_type();
						$('#delegate_approval').attr('disabled', false);	
						$('#delegate_approval:checkbox').on('change', function (e) {
							if(this.checked){
								$('#loader').removeClass('hidden');
								$.getJSON('<?= url('employee/employee/employee_request/get_employee_delegate') ?>', function (data) {
									global_employee_delegation = data;
									$('#table_employee_request').find('.id_employee_delegation_input').each(function (i, obj) {
										$('#' + obj.id).empty();
										$('#' + obj.id).select2({
											disabled: false,
											data: global_employee_delegation
										});
									});
								}).done(function () {
									$('#loader').addClass('hidden');
								});
							}
							else{
							//	alert("Tes");
								//	global_employee_delegation = data;
									$('#table_employee_request').find('.id_employee_delegation_input').each(function (i, obj) {
											$('#' + obj.id).empty();
											$('#' + obj.id).select2({
												disabled: true,
											//	data: global_employee_delegation
											});
									});
							}
						});	
						/*
						$('#table_employee_request').find('.actual_daterange_input').each(function (i, obj) {
								$('#' + obj.id).attr('disabled', true);
								$('#' + obj.id).parent().children('span').children('button').attr('disabled', true);						
						});	
						*/
					}, 1000);
				}			
				
				else{
					$('#table_employee_request').find('.daterange_input').each(function (i, obj) {
							$('#' + obj.id).val('');
						});
					$('#table_employee_request').find('.day_type_input').each(function (i, obj) {
							$('#' + obj.id).empty();
							$('#' + obj.id).attr('disabled', true);
						});
					$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
							$('#' + obj.id).val('0');
						});
					$('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
							$('#' + obj.id).val('');
						});
					$('#id_leave_type').empty();
					$('#id_overtime_type').empty();
					$('#id_leave_type').attr("readonly", true);	
				//	$('#id_leave_type').select2({disabled: true});	
					$('#id_overtime_type').select2({disabled: true});		
					$('#delegate_approval').prop('checked', false);
					$('#delegate_approval').attr('disabled', true);
					$('#table_employee_request').find('.id_employee_delegation_input').each(function (i, obj) {
							$('#' + obj.id).empty();
							$('#' + obj.id).select2({
								disabled: true,
							//	data: global_employee_delegation
							});
					});
					
				}
				
            }).trigger('change');
		//	$('.new').attr('disabled', false);
        }).fail(function (data) { // Call failed
            get_request_type();
		});		
}	

function get_leave_type(){
	$.getJSON('<?= url('employee/employee/employee_request/get_leave_type') . '?id_employee=' ?>' + global_employee_by, function (data) {
						$('#id_overtime_type').empty();
						$('#id_leave_type').select2({
							data: data
						}).on('change', function (e) {						
							$("#load_head_balance").css("display","none");
							$("#load_leave_balance").css("display","inline");
							$("#load_head_qty").css("display","none");
							$("#load_qty_days").css("display","inline");
							$('#id_leave_type').attr("readonly", true);
								if($('#id_leave_type').val() != null){
									global_leave_code = $(this).select2('data')[0].leave_code;
									if(global_classname == "add_new"){
										get_workdays();
									}
									$.getJSON('<?= url('employee/employee/employee_request/get_emp_leave') . '?id=' ?>' + $(this).select2('data')[0].id, function (data) {
									//	console.log(data);
										if(data.length > 0){
												global_employee = data;
                                                global_leave_type_restrict_by = data[0].restrict_by;
												$('#table_employee_request').find('.id_employee_input').each(function (i, obj) {
													$('#' + obj.id).empty();
													$('#' + obj.id).select2({
													//	allowClear: true,
														data: global_employee
													});
												});
												$('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
													$('#' + obj.id).val(data[0].leave_quota);
													$('#' + obj.id).show();
												});	
													
												$('#id_leave_type').attr("readonly", false);		
												$("#load_leave_balance").hide();
												$("#load_head_balance").show();
												
												$("#load_qty_days").hide();
												$("#load_head_qty").show();
											}
										else{
											global_employee = data;
                                            global_leave_type_restrict_by = '';
											$('#table_employee_request').find('.id_employee_input').each(function (i, obj) {
												$('#' + obj.id).empty();
											});
											$('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
													$('#' + obj.id).val(0);
											});	
											$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
													$('#' + obj.id).val(0);
											});										
										}
										
										});	
									}
								else{
									$("#load_leave_balance").hide();
									$("#load_head_balance").show();
									$("#load_qty_days").hide();
									$("#load_head_qty").show();
								}
									
						}).trigger('change');
						$('#id_leave_type').attr("readonly", false);
						$('#id_overtime_type').select2({disabled: true});
						
					}).fail(function (data) { // Call failed
						get_leave_type();
					});	
					
}

async function get_employee_req(){
	 $.getJSON('<?= url('employee/employee/employee_request/get_employee') ?>', function (data) {
				global_employee = data;
				$('#table_employee_request').find('.id_employee_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).select2({
					//	allowClear: true,
						data: global_employee
					});
				});
				return data;
			}).fail(function (data) { // Call failed
				get_employee_req();
			});	
}

function get_hierachy(){
	$.getJSON('<?= url('employee/employee/employee_request/get_hierachy') . '?code=' ?>' + global_code, function (data) {
			$('#id_approval').select2({
				placeholder: "No Hierarchy Approval ...",
				allowClear: true,
				data: data,
				});
			}).fail(function (data) { // Call failed
				get_hierachy();
			});		
}

function get_overtime_type(){
		$.getJSON('<?= url('employee/employee/employee_request/get_overtime_type') ?>', function (data) {
			$('#id_leave_type').empty();
			$('#id_overtime_type').select2({
				data: data
			});
			$('#id_overtime_type').select2({disabled: false});	
			$('#id_leave_type').select2({disabled: true});									
		}).fail(function (data) { // Call failed
            get_overtime_type();
		});	
}
function get_company(){
		$.getJSON('<?= url('employee/employee/employee_request/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });			
        }).fail(function (data) { // Call failed
            get_company();
		});	
}	

/*
function get_employee_by(){
	$.getJSON('<?= url('employee/employee/employee_request/get_employee_by') ?>', function (data) {
            $('#id_employee_request').select2({
                data: data,
            });
			global_employee_by = data[0].id_employee;
			
        }).fail(function (data) { // Call failed
            get_employee_by();
		});	
}
*/
	
function get_approval_status(){		
		$.getJSON('<?= url('employee/employee/employee_request/get_approval_status') ?>', function (data) {
			global_approval_status = data;
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
		});					
}	
async function get_workdays(){
		$('#loader').removeClass('hidden');
		$.getJSON('<?= url('employee/employee/employee_request/get_workdays') . '?id_employee=' ?>' + global_employee_by+'<?= '&code='?>'+global_code, function (data) {
			global_start_workdays = data[0].min_date;
			global_end_workdays = data[0].max_date;
			
			if(global_code != 'Attendance_Correction'){
				if(global_leave_code == 'MAT' || global_leave_code == 'MIS' || global_leave_type_restrict_by == 'User'){
					global_end_workdays = false;
				}
				if(global_code != 'Change_Day_off'){
					global_start_workdays = moment().subtract(7, 'days').format('YYYY-MM-DD');
				}
				
				$('.actual').css('display','none');
				$('#time_in').html('');
				$('#time_out').html('');
				var max_days = 0;
				var time_days = false;
				var format_days = 'YYYY-MM-DD';
				var single_days = false;
				
				$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
				$('#' + obj.id).data('dateRangePicker').destroy();
				$('#' + obj.id).val('');
				$('#' + obj.id).keydown(function(e) {
				/*	if (e.keyCode == 8 || e.keyCode == 46) {				
					 return true;
					}
					else{
				*/
						return false;
				//	}
				});

				$('#' + obj.id).dateRangePicker({
					startOfWeek: 'monday',
				//	separator : '',
					format: format_days,
					autoClose: false,
					time: {
						enabled: time_days
					},
					startDate: global_start_workdays,
					endDate: global_end_workdays,
					maxDays: max_days,
					singleDate:single_days,
				/*	getValue: function()
					{
						if ($('#emprequest_' + i + '_request_start_to').val() && $('#emprequest_' + i + '_request_end_to').val())
							return $('#emprequest_' + i + '_request_start_to').val() + $('#emprequest_' + i + '_request_end_to').val();
						else
							return '';
					},
				*/
					setValue: function(s,s1,s2) {
								split_start = s1.split(' ')[0];
								split_end = s2.split(' ')[0];
							//	console.log(i);
								$('#emprequest_' + i + '_request_start_to').val(s1);
								$('#emprequest_' + i + '_request_end_to').val(s2);
								
								if(global_code == "Leave_Request"){	
									get_count_days(split_start,split_end);
								}
								else if(global_code == "Change_Day_off"){
									get_count_days_od(split_start,split_end);
								}														
						}
				});							
				});
				
				$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
				$('#' + obj.id).data('dateRangePicker').destroy();
				$('#' + obj.id).val('');
				
				$('#' + obj.id).keydown(function(e) {
				/*	if (e.keyCode == 8 || e.keyCode == 46) {				
					 return true;
					}
					else{
				*/
						return false;
				//	}
				});
			//	$('#emprequest_' + i + '_request_end_to').attr('readonly',true);
				$('#' + obj.id).dateRangePicker({
					startOfWeek: 'monday',
				//	separator : '',
					format: format_days,
					autoClose: false,
					time: {
						enabled: time_days
					},
					startDate: moment().subtract(7, 'days').format('YYYY-MM-DD'),
					endDate: global_end_workdays,
					maxDays: max_days,
					singleDate:single_days,
				/*	getValue: function()
					{
						if ($('#emprequest_' + i + '_request_start_to').val() && $('#emprequest_' + i + '_request_end_to').val())
							return $('#emprequest_' + i + '_request_start_to').val() + $('#emprequest_' + i + '_request_end_to').val();
						else
							return '';
					},
				*/
					setValue: function(s,s1,s2) {
								split_start = s1.split(' ')[0];
								split_end = s2.split(' ')[0];
							//	console.log(i);
								$('#emprequest_' + i + '_request_start_to').val(s1);
								$('#emprequest_' + i + '_request_end_to').val(s2);
								
								if(global_code == "Leave_Request"){									
									get_count_days(split_start,split_end);
								}
								else if(global_code == "Change_Day_off"){
									get_count_days_od(split_start,split_end);
								}														
						}
				});			
				
			});	
			}
			
			else{
				$('.actual').css('display','inline');
				var max_days = 1;
				var time_days = true;
				var format_days = 'YYYY-MM-DD HH:mm';
				var single_days = true;
				
					$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
					$('#' + obj.id).data('dateRangePicker').destroy();
					$('#' + obj.id).val('');
					$('#' + obj.id).keydown(function(e) {
					/*	if (e.keyCode == 8 || e.keyCode == 46) {				
							return true;
						}
						else{
					*/
							return false;
					//	}
					});
					$('#' + obj.id).dateRangePicker({
						startOfWeek: 'monday',
						format: format_days,
						autoClose: false,
						time: {
							enabled: time_days
						},
						startDate: moment(global_end_workdays).subtract(5, 'days').format('YYYY-MM-DD'),
					//	startDate: global_start_workdays,
						endDate: global_end_workdays,
						maxDays: max_days,
						singleDate:single_days,
						beforeShowDay: function(t)
						{
							var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
							return [_class];
						},
						setValue: function(s,s1,s2) {
							
							split_start = s1.split(' ')[0];
							$('#emprequest_' + i + '_request_start_to').val(s1);
							$('#emprequest_' + i + '_request_end_to').val('');
							$('#time_out').html('');
							
							$('#emprequest_' + i + '_request_end_to').data('dateRangePicker').destroy();
							$('#emprequest_' + i + '_request_end_to').dateRangePicker({
								startOfWeek: 'monday',
								format: format_days,
								autoClose: false,
								time: {
									enabled: time_days
								},
								startDate: split_start,
								endDate: split_start,
								maxDays: max_days,
								singleDate:single_days,
								beforeShowDay: function(t)
								{
									var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
									return [_class];
								},
								setValue: function(s,s1,s2) {
									split_start_out = s1.split(' ')[0];
									$('#emprequest_' + i + '_request_end_to').val(s1);
									get_actual_time_out(split_start_out);
								},
							});	
								get_actual_time_in(split_start);
							}
						});			
					
					});
				
					$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
					$('#' + obj.id).data('dateRangePicker').destroy();
					$('#' + obj.id).val('');
					
					$('#' + obj.id).keydown(function(e) {
							/*	if (e.keyCode == 8 || e.keyCode == 46) {				
									return true;
								}
								else{
							*/
									return false;
							//	}
							});
					$('#' + obj.id).dateRangePicker({
						startOfWeek: 'monday',
						format: format_days,
						autoClose: false,
						time: {
							enabled: time_days
						},
						startDate: moment(global_end_workdays).subtract(5, 'days').format('YYYY-MM-DD'),
					//	startDate: global_start_workdays,
						endDate: global_end_workdays,
						maxDays: max_days,
						singleDate:single_days,
						beforeShowDay: function(t)
						{
							var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
							return [_class];
						},
						setValue: function(s,s1,s2) {
							split_start = s1.split(' ')[0];
							$('#emprequest_' + i + '_request_end_to').val(s1);
							$('#emprequest_' + i + '_request_start_to').val('');
							$('#time_in').html('');
							
							$('#emprequest_' + i + '_request_start_to').data('dateRangePicker').destroy();
							$('#emprequest_' + i + '_request_start_to').dateRangePicker({
								startOfWeek: 'monday',
								format: format_days,
								autoClose: false,
								time: {
									enabled: time_days
								},
								startDate: split_start,
								endDate: split_start,
								maxDays: max_days,
								singleDate:single_days,
								beforeShowDay: function(t)
								{
									var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
									return [_class];
								},
								setValue: function(s,s1,s2) {
									split_start_in = s1.split(' ')[0];
									$('#emprequest_' + i + '_request_start_to').val(s1);
									get_actual_time_in(split_start_in);
								},
							});				
							get_actual_time_out(split_start);
						}
					});	
					
				});			
			}
			$('#loader').addClass('hidden');	
		}).fail(function (data) { // Call failed
            get_workdays();
		});					
}	
function get_count_days(start,end){
		$("#load_head_qty").css("display","none");
		$("#load_qty_days").css("display","inline");
		if(global_leave_code == 'MAT' || global_leave_code == 'MIS' || global_leave_type_restrict_by == 'User'){
			const diffDays = (date, otherDate) => Math.ceil(Math.abs(date - otherDate) / (1000 * 60 * 60 * 24));
			var start_mat = new Date(split_start);
			var end_mat = new Date(split_end);
			qty_mat = diffDays(start_mat, end_mat.setDate(end_mat.getDate() + 1));
			if(global_id_days == 'Full_Day'){
				$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
				//	$('#' + obj.id).val(0);
					$('#' + obj.id).val(parseInt(qty_mat));
				});
			}
			else{
				$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
						$('#' + obj.id).val(parseInt(qty_mat) * 0.5);
					});
			}
			$("#load_qty_days").hide();
			$("#load_head_qty").show();
		}
		else{
			$.getJSON('<?= url('employee/employee/employee_request/get_count_days') . '?id_employee=' ?>' + global_employee_by+'<?= '&min_date='?>'+start+'<?= '&max_date='?>'+end, function (data) {
				qty = data.length;
			//	console.log(qty);
				if(global_id_days == 'Full_Day'){
					$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
						$('#' + obj.id).val(qty);
					});
				}
				else{
					$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
						$('#' + obj.id).val(parseInt(qty) * 0.5);
					});
				}
			$("#load_qty_days").hide();
			$("#load_head_qty").show();
			}).fail(function (data) { // Call failed
				get_count_days(start,end);
			});	
		}		
}
function get_count_days_od(start,end){		
		$.getJSON('<?= url('employee/employee/employee_request/get_count_days_od') . '?id_employee=' ?>' + global_employee_by+'<?= '&min_date='?>'+start+'<?= '&max_date='?>'+end, function (data) {
			$('#table_employee_request').find('.qty_days_input').each(function (i, obj) {
				$('#' + obj.id).val(data);
			});
		});					
}
function get_actual_time_in(current_dates){
		$('#time_in').html('');
		$("#load_time_in").show();
		$.getJSON('<?= url('employee/employee/employee_request/get_actual_time') . '?id_employee=' ?>' + global_employee_by+'<?= '&current_dates='?>'+current_dates, function (data) {
			$('#time_in').html(data[0].actual_time_in);
			$('#table_employee_request').find('.actual_start_to_input').each(function (i, obj) {
					$('#' + obj.id).val(data[0].actual_time_in);
			});
			
			$("#load_time_in").hide();
		}).fail(function (data) { // Call failed
            get_actual_time_in(current_dates);
		});					
}	
function get_actual_time_out(current_dates){
		$('#time_out').html('');
		$("#load_time_out").show();
		$.getJSON('<?= url('employee/employee/employee_request/get_actual_time') . '?id_employee=' ?>' + global_employee_by+'<?= '&current_dates='?>'+current_dates, function (data) {		
			$('#time_out').html(data[0].actual_time_out);
			$('#table_employee_request').find('.actual_end_to_input').each(function (i, obj) {
					$('#' + obj.id).val(data[0].actual_time_out);
			});
			
			$("#load_time_out").hide();
		}).fail(function (data) { // Call failed
            get_actual_time_out(current_dates);
		});					
}

function clear_start_date(counter){
	if(global_code == 'Attendance_Correction'){
		var max_days = 1;
		var time_days = true;
		var format_days = 'YYYY-MM-DD HH:mm';
		var single_days = true;
		$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
			$('#time_in').html('');
						if($(this).val() == ''){
							$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
							$('#' + obj.id).data('dateRangePicker').destroy();
						//	$('#' + obj.id).val('');
							
							$('#' + obj.id).keydown(function(e) {
							/*	if (e.keyCode == 8 || e.keyCode == 46) {				
									return true;
								}
								else{
							*/		
									return false;
							//	}
							});
							
							$('#' + obj.id).dateRangePicker({
								startOfWeek: 'monday',
								format: format_days,
								autoClose: false,
								time: {
									enabled: time_days
								},
								startDate: moment(global_end_workdays).subtract(5, 'days').format('YYYY-MM-DD'),
							//	startDate: global_start_workdays,
								endDate: global_end_workdays,
								maxDays: max_days,
								singleDate:single_days,
								beforeShowDay: function(t)
								{
									var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
									return [_class];
								},
								setValue: function(s,s1,s2) {
									split_start = s1.split(' ')[0];
									$('#emprequest_' + i + '_request_end_to').val(s1);
									$('#emprequest_' + i + '_request_start_to').val('');
									$('#time_in').html('');
									
									$('#emprequest_' + i + '_request_start_to').data('dateRangePicker').destroy();
									$('#emprequest_' + i + '_request_start_to').dateRangePicker({
										startOfWeek: 'monday',
										format: format_days,
										autoClose: false,
										time: {
											enabled: time_days
										},
										startDate: split_start,
										endDate: split_start,
										maxDays: max_days,
										singleDate:single_days,
										beforeShowDay: function(t){
												var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
												return [_class];
											},
										setValue: function(s,s1,s2) {
											split_start_in = s1.split(' ')[0];
											$('#emprequest_' + i + '_request_start_to').val(s1);
											get_actual_time_in(split_start_in);
										},
										});	
										get_actual_time_out(split_start);
									}
								});	
						
							});	
						}			
		});	
	}
	else{
		$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
	}
}
function clear_end_date(counter){
	if(global_code == 'Attendance_Correction'){
		var max_days = 1;
		var time_days = true;
		var format_days = 'YYYY-MM-DD HH:mm';
		var single_days = true;
		$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
			$('#time_out').html('');
						if($(this).val() == ''){
							$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
							$('#' + obj.id).data('dateRangePicker').destroy();
						//	$('#' + obj.id).val('');
							$('#' + obj.id).dateRangePicker({
							startOfWeek: 'monday',
							format: format_days,
							autoClose: false,
							time: {
								enabled: time_days
							},
							startDate: moment(global_end_workdays).subtract(5, 'days').format('YYYY-MM-DD'),
						//	startDate: global_start_workdays,
							endDate: global_end_workdays,
							maxDays: max_days,
							singleDate:single_days,
							beforeShowDay: function(t)
							{
								var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
								return [_class];
							},
							setValue: function(s,s1,s2) {								
								split_start = s1.split(' ')[0];
								$('#emprequest_' + i + '_request_start_to').val(s1);
								$('#emprequest_' + i + '_request_end_to').val('');
								$('#time_out').html('');
								
								$('#emprequest_' + i + '_request_end_to').data('dateRangePicker').destroy();
								$('#emprequest_' + i + '_request_end_to').dateRangePicker({
									startOfWeek: 'monday',
									format: format_days,
									autoClose: false,
									time: {
										enabled: time_days
									},
									startDate: split_start,
									endDate: split_start,
									maxDays: max_days,
									singleDate:single_days,
									beforeShowDay: function(t)
									{
										var _class = $(".date-picker-wrapper").removeClass("date-picker-wrapper_leave");
										return [_class];
									},
									setValue: function(s,s1,s2) {
										split_start_out = s1.split(' ')[0];
										$('#emprequest_' + i + '_request_end_to').val(s1);
										get_actual_time_out(split_start_out);
									},
									});
									get_actual_time_in(split_start);
								}
							});
						});
					}			
		});	
	}
	else{
		$('#table_employee_request').find('.request_end_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
		$('#table_employee_request').find('.request_start_to_input').each(function (i, obj) {
			$('#' + obj.id).val('');
		});
	}
}
/*
function daysdifference(firstDate, secondDate){
    var startDay = new Date(firstDate);
    var endDay = new Date(secondDate);
    var millisBetween = startDay.getTime() - endDay.getTime();
    var days = millisBetween / (1000 * 3600 * 24);
    return Math.round(Math.abs(days))+1;
}
*/		
</script>
@endsection