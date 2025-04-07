@extends('adminlte::page')
@section('title', 'Master Leave Type')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Leave Type</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Leave Type</button>
                </div>
            </div>

            <div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="leave_type_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Leave Code</th>
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

<div class="modal fade" id="modal_form_leave_type"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="leave_typeForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Leave Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
				
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Leave Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="leave_code" id="leave_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="leave_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Day Type</label>
                                <div class="col-sm-8">
                                    <select name="leave_day_type" id="leave_day_type" class="form-control form-control-sm">
                                        <option value="Full Day">Full Day</option>
                                        <option value="Half Day">Half Day</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="leave_day_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Day Count</label>
                                <div class="col-sm-8">
                                    <select name="day_count" id="day_count" class="form-control form-control-sm">
                                        <option value="Work Day">Work Day</option>
                                        <option value="Calendar Day">Calendar Day</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="day_countError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Deduct Leave</label>
                                <div class="col-sm-8">
									<input type="checkbox" name="deduct_leave" id="deduct_leave" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="deduct_leaveError">
                                        <strong></strong>
                                    </span>
                                </div>
								
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Period Entitlement</label>
                                <div class="col-sm-8">
                                    <select name="leave_entitlement_period_start" id="leave_entitlement_period_start" class="form-control form-control-sm">
                                        <option value="Join Date">Join Date</option>
                                        <option value="Permanent Date">Permanent Date</option>
                                        <option value="Specific Date">Specific Date</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="leave_entitlement_period_startError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Carry Over Leave Balance</label>
                                <div class="col-sm-2" style="margin-right:45px;">
									<input type="checkbox" name="carry_over_to_next_entitlement" id="carry_over_to_next_entitlement" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">                                                       
                                    <span class="invalid-feedback" role="alert" id="carry_over_to_next_entitlementError">
                                        <strong></strong>
                                    </span>
                                </div>
  
								 <label class="col-sm-3 col-form-label">Limit Carry Over</label>
								  <div class="col-sm-2 ml-auto">
									<input type="checkbox" name="limit_carry_over" id="limit_carry_over" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="limit_carry_overError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Enable Minus</label>
                                <div class="col-sm-2" style="margin-right:45px;">
 									<input type="checkbox" name="enable_minus_leave" id="enable_minus_leave" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="enable_minus_leaveError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
                                <label class="col-sm-3 col-form-label">Maximum Minus</label>
                                <div class="col-sm-2 ml-auto">
                                    <input name="max_minus_leave" id="max_minus_leave" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="max_minus_leaveError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Day Limit For Submit Request</label>
                                <div class="col-sm-2">
                                    <input name="day_limit_submit_request" id="day_limit_submit_request" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="day_limit_submit_requestError">
                                        <strong></strong>
                                    </span>
                                </div> 
								<label class="col-form-label">Days</label>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Valid End Period</label>
                                <div class="col-sm-2">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="leave_valid_end_period" id="leave_valid_end_period" class="form-control form-control-sm" value="1">
                                    <span class="invalid-feedback" role="alert" id="leave_valid_end_periodError">
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
                                <label class="col-sm-4 col-form-label">Required attachment</label>
                                <div class="col-sm-8">
  									<input type="checkbox" name="req_attachment" id="req_attachment" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="req_attachmentError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Repeat Period</label>
                                <div class="col-sm-1">
  									<input type="checkbox" name="repeat_period" id="repeat_period" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="repeat_periodError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
                                <div class="col-sm-2">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="repeat_period_number" id="repeat_period_number" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="repeat_period_numberError">
                                        <strong></strong>
                                    </span>   
                                </div>
									<label class="col-form-label">Months</label>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Available After</label>
                                <div class="col-sm-4">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="available_leave_after" id="available_leave_after" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="available_leave_afterError">
                                        <strong></strong>
                                    </span>   
                                </div>
								<label class="col-form-label">Month From Join Date</label>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Max Carry Over to Bring Forward</label>
                                <div class="col-sm-8">
                                    <input type="text" name="max_carry_over" id="max_carry_over" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="max_carry_overError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grace Period Request</label>
                                <div class="col-sm-8">
                                    <input type="text" name="grace_period_req" id="grace_period_req" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="grace_period_reqError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">If Over Day Limit</label>
                                <div class="col-sm-8">
                                    <select name="if_over_day_limit" id="if_over_day_limit" class="form-control form-control-sm">
                                        <option value="Warning">Warning</option>
                                        <option value="Can't Submit">Can't Submit</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="if_over_day_limitError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
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
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="formModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">
  <div class="modal-content">
   <div class="modal-header">
         <h4 class="modal-title"></h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 <form id="update_form" class="form-horizontal" method="POST">
					@csrf
					<div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Leave Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="leave_code" id="leave_code_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="leave_code_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Day Type</label>
                                <div class="col-sm-8">
                                    <select name="leave_day_type" id="leave_day_type_edit" class="form-control form-control-sm">
                                        <option value="Full Day">Full Day</option>
                                        <option value="Half Day">Half Day</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="leave_day_type_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Day Count</label>
                                <div class="col-sm-8">
                                    <select name="day_count" id="day_count_edit" class="form-control form-control-sm">
                                        <option value="Work Day">Work Day</option>
                                        <option value="Calendar Day">Calendar Day</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="day_count_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Deduct Leave</label>
                                <div class="col-sm-8">
									<input type="checkbox" name="deduct_leave" id="deduct_leave_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="deduct_leave_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Period Entitlement</label>
                                <div class="col-sm-8">
                                    <select name="leave_entitlement_period_start" id="leave_entitlement_period_start_edit" class="form-control form-control-sm">
                                        <option value="Join Date">Join Date</option>
                                        <option value="Permanent Date">Permanent Date</option>
                                        <option value="Specific Date">Specific Date</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="leave_entitlement_period_start_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Carry Over Leave Balance</label>
                                <div class="col-sm-2" style="margin-right:45px;">
 									<input type="checkbox" name="carry_over_to_next_entitlement" id="carry_over_to_next_entitlement_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="carry_over_to_next_entitlement_editError">
                                        <strong></strong>
                                    </span>
                                </div>
  
								 <label class="col-sm-3 col-form-label">Limit Carry Over</label>
								  <div class="col-sm-2 ml-auto">
								  <input type="checkbox" name="limit_carry_over" id="limit_carry_over_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">                              
                                    <span class="invalid-feedback" role="alert" id="limit_carry_over_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Enable Minus</label>
                                <div class="col-sm-2" style="margin-right:45px;">
									<input type="checkbox" name="enable_minus_leave" id="enable_minus_leave_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">                                  
                                    <span class="invalid-feedback" role="alert" id="enable_minus_leave_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
                                <label class="col-sm-3 col-form-label">Maximum Minus</label>
                                <div class="col-sm-2 ml-auto">
                                    <input name="max_minus_leave" id="max_minus_leave_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="max_minus_leave_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Day Limit For Submit Request</label>
                                <div class="col-sm-2">
                                    <input name="day_limit_submit_request" id="day_limit_submit_request_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="day_limit_submit_request_editError">
                                        <strong></strong>
                                    </span>
                                </div> 
								<label class="col-form-label">Days</label>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Leave Valid End Period</label>
                                <div class="col-sm-2">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="leave_valid_end_period" id="leave_valid_end_period_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="leave_valid_end_period_editError">
                                        <strong></strong>
                                    </span>
                                </div> 
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="description_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							 
                        </div>
                        <div class="col-md-6">
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company_edit" class="form-control form-control-sm select2"  style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Required attachment</label>
                                <div class="col-sm-8">
									<input type="checkbox" name="req_attachment" id="req_attachment_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="req_attachment_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Repeat Period</label>
                                <div class="col-sm-1">
									<input type="checkbox" name="repeat_period" id="repeat_period_edit" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">                               
                                    <span class="invalid-feedback" role="alert" id="repeat_period_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
                                <div class="col-sm-2">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="repeat_period_number" id="repeat_period_number_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="repeat_period_number_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
									<label class="col-form-label">Months</label>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Available After</label>
                                <div class="col-sm-4">
                                    <input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="available_leave_after" id="available_leave_after_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="available_leave_after_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
								<label class="col-form-label">Month From Join Date</label>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Max Carry Over to Bring Forward</label>
                                <div class="col-sm-8">
                                    <input type="text" name="max_carry_over" id="max_carry_over_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="max_carry_over_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grace Period Request</label>
                                <div class="col-sm-8">
                                    <input type="text" name="grace_period_req" id="grace_period_req_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="grace_period_req_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">If Over Day Limit</label>
                                <div class="col-sm-8">
                                    <select name="if_over_day_limit" id="if_over_day_limit_edit" class="form-control form-control-sm">
                                        <option value="Warning">Warning</option>
                                        <option value="Can't Submit">Can't Submit</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="if_over_day_limit_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status_edit" class="form-control form-control-sm">
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
                
				<div class="modal-footer">
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action_edit" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <button type="submit" name="action_button" id="action_button" class="btn btn-primary" value="edit"><i class="fas fa-edit"></i> Update</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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

@endsection


@section('scripts')
<script type="text/javascript">
let global_id_leave_type = "";
function get_company(){
	 $.getJSON('<?= url('time_attendance/leave_setting/leave_type/get_company') ?>', function (data) {
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
	$('#datepicker').datepicker({
				uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
			});	
	$('#datepicker_edit').datepicker({
				uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
			});	
	
	$('#leave_day_type').select2({width: '100%' });	
	$('#day_count').select2({width: '100%' });	
//	$('#deduct_leave').select2({width: '100%' });	
	$('#leave_entitlement_period_start').select2({width: '100%' });	
//	$('#carry_over_to_next_entitlement').select2({width: '100%' });	
//	$('#limit_carry_over').select2({width: '100%' });	
//	$('#enable_minus_leave').select2({width: '100%' });	
//	$('#req_attachment').select2({width: '100%' });	
//	$('#repeat_period').select2({width: '100%' });	
	$('#if_over_day_limit').select2({width: '100%' });	
	$('#select2status').select2({width: '100%' });	
	
	$('#leave_day_type_edit').select2({width: '100%' });	
	$('#day_count_edit').select2({width: '100%' });	
//	$('#deduct_leave_edit').select2({width: '100%' });	
	$('#leave_entitlement_period_start_edit').select2({width: '100%' });	
//	$('#carry_over_to_next_entitlement_edit').select2({width: '100%' });	
//	$('#limit_carry_over_edit').select2({width: '100%' });	
//	$('#enable_minus_leave_edit').select2({width: '100%' });	
//	$('#req_attachment_edit').select2({width: '100%' });	
//	$('#repeat_period_edit').select2({width: '100%' });	
	$('#if_over_day_limit_edit').select2({width: '100%' });	
	$('#select2status_edit').select2({width: '100%' });
	
	get_company();
    });

    $(document).on('click', '.new', function () {
        global_id_leave_type = "";
        $("#leave_typeForm")[0].reset();
        $("#leave_typeForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Leave Type");
        $(".invalid-feedback").children("strong").text("");
        $("#leave_typeForm input").removeClass("is-invalid");
        $("#leave_typeForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_leave_type').modal('show');
    });
	
	 $(function () {
        $('#leave_typeForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#leave_typeForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					url: "{{ route('leave_type.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_leave_type').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#leave_type_table').DataTable().ajax.reload();
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
					/*	else if (response.status === 500) {
							 $("#leave_code").addClass("is-invalid");
                              $("#leave_codeError").children("strong").text('The Leave Code has already been taken.');
                        }
					*/
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



    $(document).ready(function () {
        $('#leave_type_table').DataTable({
            processing: true,
       //     serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('time_attendance/leave_setting/leave_type/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#leave_type_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                    data: 'id_leave_type',
					defaultContent: '',
					orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'leave_code', name: 'leave_code'},
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
		
	$('#update_form').on('submit', function(event){
	  event.preventDefault();
	  var action_url = '';

	  if($('#action_edit').val() == 'Edit')
	  {
	   action_url = "{{ route('leave_type.update') }}";
	  }
	  $(".invalid-feedback").children("strong").text("");
	  $("#update_form input").removeClass("is-invalid");
	   $.ajax({
	   url: action_url,
	   method:"POST",
	   data:$(this).serialize(),
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
		$('#leave_type_table').DataTable().ajax.reload();

		}
		$('#formModal').modal('hide');
	   },
		error: function (response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#" + key + "_edit").addClass("is-invalid");
					$("#" + key + "_editError").children("strong").text(errors[key][0]);
				});
			} 
		/*	else if (response.status === 500) {
				$("#leave_code_edit").addClass("is-invalid");
				$("#leave_code_editError").children("strong").text('The Leave Code has already been taken.');
			}
		*/
		}
	  });
	 });

	$(document).on('click', '.edit', function(){
	  var id_leave_type = $(this).attr('id');
	  $('#form_result').html('');
	  $.ajax({
	   url :"leave_type/edit/"+id_leave_type,
	   dataType:"json",
	   success:function(data)
	   {
		$('#leave_code_edit').val(data.result.leave_code);
		if (data.result.deduct_leave == 1) {
			$('#deduct_leave_edit').prop('checked', true);
		} else {
			$('#deduct_leave_edit').prop('checked', false);
		}
		$('#day_count_edit').val(data.result.day_count);
		$('#leave_day_type_edit').val(data.result.leave_day_type);
		if (data.result.repeat_period == 1) {
			$('#repeat_period_edit').prop('checked', true);
		} else {
			$('#repeat_period_edit').prop('checked', false);
		}
		$('#repeat_period_number_edit').val(data.result.repeat_period_number);
		$('#leave_entitlement_period_start_edit').val(data.result.leave_entitlement_period_start);
		$('#available_leave_after_edit').val(data.result.available_leave_after);
		$('#repeated_edit').val(data.result.repeated);
		$('#leave_valid_end_period_edit').val(data.result.leave_valid_end_period);
		$('#grace_period_req_edit').val(data.result.grace_period_req);
		if (data.result.carry_over_to_next_entitlement == 1) {
			$('#carry_over_to_next_entitlement_edit').prop('checked', true);
		} else {
			$('#carry_over_to_next_entitlement_edit').prop('checked', false);
		}
		if (data.result.limit_carry_over == 1) {
			$('#limit_carry_over_edit').prop('checked', true);
		} else {
			$('#limit_carry_over_edit').prop('checked', false);
		}
		$('#max_carry_over_edit').val(data.result.max_carry_over);
		if (data.result.enable_minus_leave == 1) {
			$('#enable_minus_leave_edit').prop('checked', true);
		} else {
			$('#enable_minus_leave_edit').prop('checked', false);
		}
		$('#max_minus_leave_edit').val(data.result.max_minus_leave);
		$('#day_limit_submit_request_edit').val(data.result.day_limit_submit_request);
		$('#if_over_day_limit_edit').val(data.result.if_over_day_limit);
		if (data.result.req_attachment == 1) {
			$('#req_attachment_edit').prop('checked', true);
		} else {
			$('#req_attachment_edit').prop('checked', false);
		}
		
		$('#description_edit').val(data.result.description);
		$('#id_company_edit').val(data.result.id_company);
		$('#datepicker_edit').val(data.result.inactive_date);
		$('#select2status_edit').val(data.result.status);
		$('#hidden_id').val(id_leave_type);
		$('.modal-title').text('Edit Record');
		$('#action_button').val('Edit');
		$('#action_edit').val('Edit');
		$('#formModal').modal('show');
	   }
	  })
	 });

	$(document).on('click', '.delete', function (event) {
		id_leave_type = $(this).attr('id');
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
				   url:"leave_type/destroy/"+id_leave_type,
				   success:function(data)
				   {
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 $('#leave_type_table').DataTable().ajax.reload();				 
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
</script>
@endsection