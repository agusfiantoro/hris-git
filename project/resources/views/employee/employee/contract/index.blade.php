@extends('adminlte::page')
@section('title', 'Contract')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Contract</h5>
                <div class="card-tools">
                    <button onclick="return false;" id="show_attachment" class="btn btn-sm btn-primary"><i class="fas fa-document"></i> Contract Attachment</button>
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Contract</button>
                </div>
            </div>
       
			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="contract_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>					   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Contract Number</th>
						<th>NIK</th>
						<th data-priority="2">Employee Name</th>
						<th>Category</th>
						<th>Effective Date</th>
						<th>Expired Date</th>
						<th>Attachment</th>
                        <th>Status</th>
						<th data-priority="1">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_contract"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="contractForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Contract</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- div class="row">
                                <label class="col-sm-4 col-form-label">Contract Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="contract_number" id="contract_number" readonly="readonly" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="contract_numberError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div -->
							<div class="row">
                                <label class="col-sm-4 col-form-label">Contract Type</label>
								<div class="col-sm-8">
                                    <select name="contract_type" id="contract_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Contract Number</label>
								 <div class="col-sm-8">
									<div class="input-group">
									<?php  if(session('company_type') != 'corporate'){ ?>
										 <input name="contract_number" id="contract_number" type="text" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;">
									<?php }
									else{
										?>
										<input name="contract_number" id="contract_number" type="text" onclick="browse_table()" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;">
									<?php 	
									}
									?>
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-group"  style="font-size:10px;color:#dc3545;" role="alert" id="contract_numberError">
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
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Position Route</label>
                                <div class="col-sm-8">
                                    <input id="position_routing" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Position Detail</label>
                                <div class="col-sm-8">
                                    <input id="position_detail" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Grade</label>
                                <div class="col-sm-8">
                                    <input id="job_grade" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Status</label>
                                <div class="col-sm-8">
                                    <input id="job_status" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Location</label>
                                <div class="col-sm-8">
                                    <input id="location" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
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
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
						
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
                                    <select name="id_contract_category" id="id_contract_category" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_contract_categoryError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Working Time</label>
								<div class="col-sm-8">
                                    <select name="id_working_schedule" id="id_working_schedule" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_working_scheduleError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>                   
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Expired Date</label>
                                <div class="col-sm-8">
                                   <input name="expired_date" id="expired_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="expired_dateError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Notice Period</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notice_period" id="notice_period" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="notice_periodError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Salary Structure</label>
                                <div class="col-sm-8">
                                    <input type="text" name="id_salary_structure" id="id_salary_structure" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="id_salary_structureError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Payroll Schedule</label>
                                <div class="col-sm-8">
									  <select id="schedule_payroll" name="schedule_payroll" class="form-control form-control-sm" style="width: 100%;">
										<option value="M">Months</option>
										<option value="W">Weeks</option>
										<option value="D">Days</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="schedule_payrollError">
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
									<img id="attach" src="#" width="70px" height="60px" style="display:none;margin-bottom:60px;">
								  <label class="custom-file-label" for="customFile"><i>Max 2 MB</i></label>
								</div>
							   </div>
							</div>
                        </div>						
					
                       <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
	
<div class="modal fade" id="formModal"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                <label class="col-sm-4 col-form-label">Contract Number</label>
                                <div class="col-sm-8">
                                    <input type="text" name="contract_number" id="contract_number_edit" readonly="readonly" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="contract_number_editError">
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
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Position Route</label>
                                <div class="col-sm-8">
                                    <input id="position_routing_edit" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Position Detail</label>
                                <div class="col-sm-8">
                                    <input id="position_detail_edit" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Grade</label>
                                <div class="col-sm-8">
                                    <input id="job_grade_edit" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Status</label>
                                <div class="col-sm-8">
                                    <input id="job_status_edit" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Location</label>
                                <div class="col-sm-8">
                                    <input id="location_edit" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">														
							<div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
                                    <select name="id_contract_category" id="id_contract_category_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_contract_category_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Working Time</label>
								<div class="col-sm-8">
                                    <select name="id_working_schedule" id="id_working_schedule_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_working_schedule_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							                    
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Expired Date</label>
                                <div class="col-sm-8">
                                   <input name="expired_date" id="expired_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="expired_date_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Notice Period</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notice_period" id="notice_period_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="notice_period_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Salary Structure</label>
                                <div class="col-sm-8">
                                    <input type="text" name="id_salary_structure" id="id_salary_structure_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="id_salary_structure_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                           
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Payroll Schedule</label>
                                <div class="col-sm-8">
									  <select id="schedule_payroll_edit" name="schedule_payroll" class="form-control form-control-sm" style="width: 100%;">
										<option value="M">Months</option>
										<option value="W">Weeks</option>
										<option value="D">Days</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="schedule_payroll_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>

							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>							
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<img id="attachment_edit" src="#" width="70px" height="60px">
									<label class="custom-file-label" for="customFile"><i>Max 1 MB (pdf,jpg,jpeg,png)</i></label>
								</div>
								
							   </div>
							</div -->
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-3">
									  <select id="attachment_type_edit" name="attachment_type" class="form-control form-control-sm">
										<option value="image">Image</option>
										<option value="pdf">PDF</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="attachment_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                           
							   <div class="col-md-5">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment_edit">
								  <span class="invalid-feedback" role="alert" id="attachment_editError">
                                        <strong></strong>
                                    </span>
									<img id="attach_edit" src="#" width="70px" height="60px" style="display:none;margin-bottom:60px;">
								  <label class="custom-file-label" for="customFile"><i>Max 2 MB</i></label>
								</div>
							   </div>
							</div>

                        </div>						
					
                       <div class="col-md-6"></div>
                    </div>
					
					<div class="modal-footer">
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action_edit" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
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

<div id="attachmentModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Attachment</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body card">
                <div class="form-group row">
                    <label class="col-sm-1 col-form-label">Employee :</label>
                    <div class="col-sm-5">
                        <select id="employee" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>

                    <label class="col-sm-2 col-form-label">Contract Number :</label>
                    <div class="col-sm-4">
                        <select id="contract_number_attachment" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button onclick="return false;" id="search_attachment" class="btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
                <div class="div_datatable" style="display:none; overflow-x: scroll;"> 
                    <button onclick="return false;" class="btn btn-default advanced_attachment">Advanced Search</button><br><br>
                    <table id="attachment_table" class="table table-striped table-bordered table-hover datatable"></table>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection
@section('css')
<style type="text/css">
    .swal-red {
        color:#f27474;
		font-weight:bold;
    }
</style>
@stop
@section('scripts')

<script>
let global_contract_type = "";
let employee_contract = <?= json_encode($employee_contract) ?>;
let contract_number = <?= json_encode($contract_number) ?>; 

$('#employee').select2({
    placeholder: "Cari Karyawan",
    data: employee_contract,
    allowClear: true,
});

$('#contract_number_attachment').select2({
    placeholder: "Cari Nomor Kontrak",
    data: contract_number,
    allowClear: true,
});

function browse_table() {
	$('#browseModal').modal('show');
	$("#bro_table").DataTable({
		scrollY: "400px",
	//	scrollX: true,
		paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          autoWidth: true,
		  columnDefs:false,		
          columns : [
            { data : 'no'},
            { data : 'tgl_surat' },
            { data : 'no_surat' },
            { data : 'name' },
            { data : 'area' },
            { data : 'keterangan' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
			url: "<?= url('employee/employee/contract/browse') . '?contract=' ?>" + global_contract_type,
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'no'          : '<center>'+no+'</center>',
                  'tgl_surat'   : '<center>'+json[i]['tgl_surat']+'</center>',
                  'no_surat'   : '<center>'+json[i]['no_surat']+'</center>',
                  'name'   : '<center>'+json[i]['name']+'</center>',
                  'area'   : '<center>'+json[i]['area']+'</center>',
                  'keterangan'   : '<center>'+json[i]['keterangan']+'</center>',
                  'action'   : '<center><button type="button" name="check" class="btn btn-success btn-sm" title="Check" onClick="check('+json[i]['id']+')"><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
      });
}
function check(id) {	
		//  console.log(id);
		  $.ajax({
			url: "<?= url('employee/employee/contract/checkid') . '?contract=' ?>" + global_contract_type +"<?= '&id=' ?>"+id,
		   dataType:"json",
		   success:function(data)
		   {
			$("#contract_number").val(data.result[0].no_surat);
			$("#browseModal").modal('hide');
		   }
		  })
}

function get_employee(){
	$.getJSON('<?= url('employee/employee/contract/get_employee') ?>', function (data) {
            $('#id_employee').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ..",
				allowClear: true,
                data: data,
            }).on('change', function (e) {
						$('#position_routing').val('');
						$('#position_detail').val('');
						$('#job_grade').val('');
						$('#job_status').val('');
						$('#location').val('');		
                        $('#effective_date').val('');
                        $('#expired_date').val('');

				if($(this).select2('data')[0].id != ''){									
					$.getJSON('<?= url('employee/employee/contract/get_contract_category') . '?id_employment_status='  ?>' + $(this).select2('data')[0].id_employment_status, function (data) {
						$('#id_contract_category').empty();
						$('#id_contract_category').select2({
							data: data,
						});					
					});
					$.getJSON('<?= url('employee/employee/contract/get_shift_group') . '?id_shift_group='  ?>' + $(this).select2('data')[0].id_shift_group,
					function (data) {
							$('#id_working_schedule').empty();
							$('#id_working_schedule').select2({
								data: data,
							});
					});	
					$.getJSON('<?= url('employee/employee/contract/get_position') . '?id_employee=' ?>' + $(this).select2('data')[0].id_employee, function (data) {					
						$('#position_routing').val(data[0].position_routing);
						$('#position_detail').val(data[0].position_detail);
						$('#job_grade').val(data[0].job_grade);
						$('#job_status').val(data[0].job_status);
						$('#location').val(data[0].work_location);
						
					});
                    $.getJSON('<?= url('employee/employee/contract/get_employee_detail').'?id_employee=' ?>' + $(this).select2('data')[0].id_employee, function (data) {    
                        $('#effective_date').val(data.join_date);
                        $('#expired_date').val(data.expired_date);
                    });
				}
            }).trigger('change');			
        }).fail(function (data) { // Call failed
            get_employee();
		});	
}

function get_employee_edit(){
	$.getJSON('<?= url('employee/employee/contract/get_employee_edit') ?>', function (data) {		
			$('#id_employee_edit').select2({
                data: data,
            }).on('change', function (e) {
				if($(this).select2('data').length > 0){
						$('#position_routing_edit').val('');
						$('#position_detail_edit').val('');
						$('#job_grade_edit').val('');
						$('#job_status_edit').val('');
						$('#location_edit').val('');					
					$.getJSON('<?= url('employee/employee/contract/get_contract_category') . '?id_employment_status='  ?>' + $(this).select2('data')[0].id_employment_status, function (data) {
						$('#id_contract_category_edit').empty();					
						$('#id_contract_category_edit').select2({
							data: data,
						});
					});
					$.getJSON('<?= url('employee/employee/contract/get_shift_group') . '?id_shift_group='  ?>' + $(this).select2('data')[0].id_shift_group,
					function (data) {
							$('#id_working_schedule_edit').empty();
							$('#id_working_schedule_edit').select2({
								data: data,
							});
					});	
					$.getJSON('<?= url('employee/employee/contract/get_position') . '?id_employee=' ?>' + $(this).select2('data')[0].id_employee, function (data) {		
						if(data.length > 0){
							$('#position_routing_edit').val(data[0].position_routing);
							$('#position_detail_edit').val(data[0].position_detail);
							$('#job_grade_edit').val(data[0].job_grade);
							$('#job_status_edit').val(data[0].job_status);
							$('#location_edit').val(data[0].work_location);
						}
					});
				}
            }).trigger('change');
        }).fail(function (data) { // Call failed
            get_employee_edit();
		});	
}

/*
function get_shift_group(){
	$.getJSON('<?= url('employee/employee/contract/get_shift_group') ?>', function (data) {
            $('#id_working_schedule').select2({
                data: data,
            });
			$('#id_working_schedule_edit').select2({
                data: data,
            });
    }).fail(function (data) { // Call failed
            get_shift_group();
    });	
}
*/
function get_company(){
	 $.getJSON('<?= url('employee/employee/contract/get_company') ?>', function (data) {
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

	
$(document).ready(function () {

$('#attachment_type').prepend('<option selected></option>').select2({placeholder: "Select Type ...",allowClear: true,width:'100%'});

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
$('#schedule_payroll').select2();	
$('#schedule_payroll_edit').select2();		

		get_employee_edit();
	//	get_shift_group();
		get_company();
		
		contract_type = [
			{
				id: 'pkwt',
				text: 'PKWT'
			},
			{
				id: 'pkwtt',
				text: 'PKWTT'
			},
			{
				id: 'pkhl',
				text: 'PKHL'
			},
			{
				id: 'adendum',
				text: 'Adendum'
			},
			
		];
		$('#contract_type').select2({
                data: contract_type,
				placeholder: 'Select Contract Type'
            }).on('change', function (e) {
				$("#contract_number").val('');
				global_contract_type = $(this).val().toUpperCase();					
			//	console.log(global_contract_type);
			}).trigger('change');
    });

$(document).on('click', '.new', function () {
			$('.summernote').summernote('reset');
            $("#contractForm")[0].reset();
            $("#contractForm .modal-title").html("<span class='fas fa-plus'></span> Form Contract");
            $(".invalid-feedback").children("strong").text("");
            $("#contractForm input").removeClass("is-invalid");
            $("#contractForm textarea").removeClass("is-invalid");
			get_employee();
            $('#modal_form_contract').modal('show');
		
        });

    $(function () {
        $('#contractForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-group").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $("#contractForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
					url: "{{ route('contract.save') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_contract').modal('hide');
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
					complete: function(){
						$('#loader').addClass('hidden')
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
    $('#contract_table').DataTable({
        processing: true,
    //    serverSide: true,
		responsive: true,		
        ajax: {
		//   url: "{{ route('contract.index') }}",
			url: "<?= url('employee/employee/contract/') . '?id_url=' ?>" + global_url_server,
		   error: function (jqXHR, textStatus, errorThrown) {
				$('#contract_table').DataTable().ajax.reload();
            }
		  },
        columns: [
		{
                defaultContent: '',
				orderable: false,
				},
		{   // Checkbox select column
                data: 'id_contract',
                defaultContent: '',
                orderable: false
            },
		{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'contract_number', name: 'contract_number' },
			{ data: 'nik_employee', name: 'nik_employee' },
			{ data: 'employee_name', name: 'employee_name' },
			{ data: 'category', name: 'category' },
		//	{ data: 'working_time', name: 'working_time' },
			{ data: 'effective_date', name: 'effective_date' },
			{ data: 'expired_date', name: 'expired_date' },
			{ data: 'attachment_type', name: 'attachment_type',render: function ( data, type, row ) {	
					if(row['is_upload']==true && data == null){
						return 'Uploaded';
					}
					else if(row['attachment_type']=='pdf' || row['attachment_type']=='image'){
						return 'Uploaded';
					//	return '<a download="'+Date.now()+'.pdf" href="data:application/pdf;base64,'+ data + '">Download File</a>';
					}
					else{
						return "";
					}
				//	else if(row['attachment_type']=='pdf'){
				//		return 'Uploaded';
					//	return '<a download="'+Date.now()+'.pdf" href="data:application/pdf;base64,'+ data + '">Download File</a>';
				//	}
				//	else if(row['attachment_type']=='image'){
				//		return 'Uploaded';
					//	return '<a download="'+Date.now()+'.jpg" href="data:image;base64,'+ data + '">Download File</a>';
				//	}
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
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
        "fnInitComplete": function (oSettings) {
           $('#contract_table_wrapper .column-filter-widget:eq(10)').find("select option:contains('A')").attr('selected','selected').change();
           $('#contract_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
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
	
 
  $('#update_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  if($('#action_edit').val() == 'Edit')
  {
   action_url = "{{ route('contract.update') }}";
  }
  $(".invalid-feedback").children("strong").text("");
  $("#update_form input").removeClass("is-invalid");
  $("#update_form textarea").removeClass("is-invalid");

    $('#id_employee_edit').select2("enable");
    $('#id_contract_category_edit').select2("enable");
    $('#schedule_payroll_edit').select2("enable");
    $('#id_working_schedule_edit').select2("enable");

   $.ajax({
	enctype: 'multipart/form-data',
	processData: false,  // Important!
	contentType: false,
	cache: false,
   url: action_url,
   method:"POST",
   data:new FormData(this),
   dataType:"json",
   beforeSend: function () {
		$('#loader').removeClass('hidden');
	},
   success: function (response) {
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
			});
		}
	},
    complete: function(){
			$('#loader').addClass('hidden')
		},
	error: function (response) {
		if (response.status === 422) {
			let errors = response.responseJSON.errors;
			let err = "";
			Object.keys(errors).forEach(function (key) {
				$("#" + key + "_edit").addClass("is-invalid");
				$("#" + key + "_editError").children("strong").text(errors[key][0]);
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
/*	error: function (response) {
		if (response.status === 422) {
			let errors = response.responseJSON.errors;
			Object.keys(errors).forEach(function (key) {
				$("#" + key + "_edit").addClass("is-invalid");
				$("#" + key + "_editError").children("strong").text(errors[key][0]);
			});
		} 
	}
	*/
  });
 });

 $(document).on('click', '.edit', function(){
  $('#attachment_type_edit').prepend('<option selected></option>').select2({placeholder: "Select Type ...",allowClear: true,width:'100%'});
  var id_contract = $(this).attr('id');
 
  $.ajax({
   url :"contract/edit/"+id_contract,
   dataType:"json",
   success:function(data)
   {
	
    $('#contract_number_edit').val(data.result.contract_number);
    $('#id_employee_edit').val(data.result.id_employee).trigger('change').select2("enable",false);
    $('#effective_date_edit').val(data.result.effective_date);
    $('#expired_date_edit').val(data.result.expired_date);
    $('#reference_date_edit').val(data.result.reference_date);
 //   $('#attachment_type_edit').val(data.result.attachment_type);
    $('#attachment_edit').val("");
    $('.custom-file-label').html('<i>Max 2 MB</i>');
	document.getElementById("attachment_edit").src = "data:image;base64,"+data.result.attachment;
    $('#id_contract_category_edit').val(data.result.id_contract_category).trigger('change').select2("enable",false);
    $('#id_salary_structure_edit').val(data.result.id_salary_structure).trigger('change').attr("readonly",true);
    $('#notice_period_edit').val(data.result.notice_period).attr("readonly",true);
    $('#schedule_payroll_edit').val(data.result.schedule_payroll).select2("enable",false);
    $('#id_working_schedule_edit').val(data.result.id_working_schedule).trigger('change').select2("enable",false);
    $('#id_company_edit').val(data.result.id_company);
    $('#hidden_id').val(id_contract);
    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action_edit').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });
 

$(document).on('click', '.delete', function (event) {
	id_contract = $(this).attr('id');
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
			   url:"contract/destroy/"+id_contract,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#contract_table').DataTable().ajax.reload();				 
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

$(document).on('click', '#search_attachment', function () {
    $("#attachment_table").html("");
    get_datatable()
});
$(document).on('click', '#show_attachment', function () {
    $("#attachmentModal").modal('show');
});
$(document).on("click", ".advanced_attachment", function () {
    $('.cf').select2({width:'100%'});
    if($(".attachment_table").css('display') == 'none'){
        $(".attachment_table").show("slow");
    }
    else {
        $(".attachment_table").hide("slow");
    }   
});
const get_datatable = async () => {
    $(".div_datatable").show();
    let myData = {
        nik: $("#employee").val() == '' ? null : $("#employee").val(),
        id_contract: $("#contract_number_attachment").val() == '' ? null : $("#contract_number_attachment").val(),
    };
    let t = $('#attachment_table').DataTable({
        processing: true,
        serverSide: false,
        // responsive: true,
        destroy: true,
        ajax: {
            url: "<?= url('employee/employee/contract/attachment') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
        },
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
                orderable: false
            },
            {   // Checkbox select column
                data: 'id_employee',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No', orderable: false},
            { data: 'contract_number', title: 'Contract Number'},
            { data: 'nik_employee', title: 'NIK'},
            { data: 'name', title: 'Name'},
            { data: 'category', title: 'Category'},
            { data: 'working_time', title: 'Working Time'},
            { data: 'effective_date', title: 'Effective Date'},
            { data: 'expired_date', title: 'Expired Date'},
            { data: 'status', title: 'Status'},
            { data: 'attachment', title: 'Attachment', orderable: false, render: function ( data, type, row ) { 
                    let file_attachment = '';
                    if(row.attachment != null){
                        if(row.attachment_type == null){
                            let storage_path = "<?= url('project/storage/app/public/upload/contract') ?>";
                            let file_path   = `${storage_path}/${row.nik_employee}/`;

                            file_attachment =  `<a href="${file_path}${row.attachment}" target="_blank" class="btn btn-xs btn-primary">Download</a>`;
                        } else {
                            const type = {
                                'pdf' : {
                                    'data' : 'data:application/pdf;base64,'+row.attachment, 
                                    'name' : `${row.contract_number}.pdf`
                                },
                                'image' : {
                                    'data' : 'data:image;base64,'+row.attachment, 
                                    'name' : `${row.contract_number}.jpg`
                                },
                            };
                            file_attachment =  `<a download="${type[row.attachment_type]['name']}" href="${type[row.attachment_type]['data']}" class="btn btn-xs btn-primary contract_${row.id_contract}">Download</a>`;

                            // var z = document.createElement("a"); 
                            //     z.href = type[row.attachment_type]['data']; //Image Base64 Goes here
                            //     z.download = row.nik_employee+'_'+row.id_contract; //File name Here
                            //     z.click();
                        }
                    } 
                    return file_attachment;
                } 
            },
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
        "fnInitComplete": function (oSettings) {
            $('#attachment_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
            $('#attachment_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
            $('#attachment_table_wrapper .column-filter-widget:eq(11)').html('');
        },
    });
}  

</script>

@endsection