@extends('adminlte::page')
@section('title', 'Career Transition History')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
           <div class="card-header">
                <h5 class="card-title">Career Transition History</h5>             
            </div>
			 <div class="card-body">
				<div class="form-group row">              
					<!-- <label class="col-md-2 col-form-label">Show by date type :</label> -->
					{{-- <div class="col-md-5">
						<select id="employee_search" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
					</div> --}}
					<div class="col-md-2">
						<select id="date_type" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
							<input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
							<input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
							<div class="input-group-append">
								<span class="input-group-text far fa-calendar form-control-sm"></span>
							</div>
						</div>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_created" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>										
	            </div>
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
			<div id="range_date" class="col-md-12" style="display:none;">
				<div class="card card-danger card-outline card-outline-tabs">
				  <div class="card-header p-0 border-bottom-0">
					<ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
					  <li class="nav-item">
						<a class="nav-link active" id="custom-tabs-four-effective-tab" data-toggle="pill" href="#custom-tabs-four-effective" role="tab" aria-controls="custom-tabs-four-effective" aria-selected="true">Filter Effective Date</a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="custom-tabs-four-created-tab" data-toggle="pill" href="#custom-tabs-four-created" role="tab" aria-controls="custom-tabs-four-created" aria-selected="false">Filter Created Date</a>
					  </li>
					</ul>
				  </div>
				  <div class="card-body">
					<div class="tab-content" id="custom-tabs-four-tabContent">
					  <div class="tab-pane fade show active" id="custom-tabs-four-effective" role="tabpanel" aria-labelledby="custom-tabs-four-effective-tab">
						 <div class="row">
								<div class="col-md-6">
									<div class="row">
										<label class="col-sm-4 col-form-label">Min Effective Date:</label>
										 <div class="col-sm-8">
											<input type="text" id="min_effective" name="min_effective">
										 </div>
									</div>
								</div> 
								<div class="col-md-6">
									<div class="row">
										 <label class="col-sm-4 col-form-label">Max Effective Date:</label>
										 <div class="col-sm-8">
											<input type="text" id="max_effective" name="max_effective">
										 </div>
									</div>
								</div> 
							</div>  
						</div>
					  <div class="tab-pane fade" id="custom-tabs-four-created" role="tabpanel" aria-labelledby="custom-tabs-four-created-tab">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<label class="col-sm-4 col-form-label">Min Created Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="min_created" name="min_created">
									 </div>
								</div>
							</div> 
							<div class="col-md-6">
								<div class="row">
									 <label class="col-sm-4 col-form-label">Max Created Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="max_created" name="max_created">
									 </div>
								</div>
							</div> 
						</div>
					  </div>
					 
					</div>
				  </div>
				  <!-- /.card -->
				</div>
			</div>

					<table id="career_table" style="width:1200px;" class="table table-striped table-bordered table-hover datatable">
						<thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th style="white-space:nowrap;">Reference Number</th>
                            <th style="white-space:nowrap;">Letter Number</th>
                            <th style="white-space:nowrap;">Created Date</th>
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
							<th style="white-space:nowrap;">Effective/Terminate Date</th>
							<th style="white-space:nowrap;">Expired Date</th>
							<th style="white-space:nowrap;">Attachment</th>
							<th style="white-space:nowrap;">Attachment Letter</th>
							<th style="white-space:nowrap;">Request Resign Date</th>
							<th style="white-space:nowrap;">Reason Category</th>
							<th style="white-space:nowrap;">Reason Description</th>
							<th style="white-space:nowrap;">Note</th>
							<th data-priority="1" style="white-space:nowrap;">Approval Status</th>
							<th style="white-space:nowrap;">Ref.Number Reco</th>
                            <th data-priority="2" width=120>Action</th>
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
                    <h5 class="modal-title">Edit Career Transition History</h5>
                    <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 450px;overflow-y: auto;">
                    <div class="row">
                        <div class="col-md-6">
                            
							<div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
									<input name="id_career_transaction" id="id_career_transaction" type="hidden">
                                    <input id="transition_category" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
								<div class="col-sm-8">
                                    <input id="transaction_type" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Letter Number</label>
								 <div class="col-sm-8">
									<div class="input-group">
										 <!-- input name="transaction_number" id="transaction_number" type="text" onclick="browse_table()" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;" readonly -->
										 <input name="transaction_number" id="transaction_number" type="text" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;" readonly>
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-group" style="font-size:10px;color:#dc3545;" role="alert" id="transaction_numberError">
                                        <strong></strong>
                                    </span>
								</div>
                            </div>
							
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
								<div class="col-sm-8">
                                    <input id="name" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                                    <input id="id_employee" name="id_employee" type="hidden">
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Current Company</label>
								<div class="col-sm-8">
                                    <input id="old_company" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Employement Status</label>
                                <div class="col-sm-8">
                                    <input id="old_employment_status" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Department</label>
                                <div class="col-sm-8">
                                    <input id="old_dept" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Position Route</label>
                                <div class="col-sm-8">
                                    <input id="old_routing" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Position Detail</label>
                                <div class="col-sm-8">
									 <input id="old_position_detail" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>							
							
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Job Grade</label>
                                <div class="col-sm-8">
                                    <input id="old_job_grade" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Job Status</label>
                                <div class="col-sm-8">
                                    <input id="old_job_status" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
							<div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Current Location</label>
                                <div class="col-sm-8">
                                    <input id="old_location" class="form-control form-control-sm" style="width: 100%;" disabled>
                                </div>
                            </div>
                        </div>
						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Status</label>
                                <div class="col-sm-8">
                                     <input id="code_status" class="form-control form-control-sm" style="width: 100%;" disabled>  								
                                </div>
                            </div>		
							<div class="row">
                                <label class="col-sm-4 col-form-label" id="eff_date">Effective Date</label>
                                <div class="col-sm-3">
                                    <input id="effective_date" class="form-control form-control-sm" disabled>                                    
                                </div>
								<label class="col-form-label">Expired Date</label>
                                <div class="col-sm-3" style="flex: 0 0 26%;max-width: 26%;">
                                   <input id="expired_date" class="form-control form-control-sm" disabled>                                    
                                </div>
                            </div>
							<div id="terminate">
								<div class="row">
									<label class="col-sm-4 col-form-label">Reason Category</label>
									<div class="col-sm-8">
									   <input id="resign_category" class="form-control form-control-sm" disabled>                                    
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Reason Description</label>
									<div class="col-sm-8">
									   <input id="terminate_reason" class="form-control form-control-sm" disabled>                                    
									</div>
								</div>
							</div>
							<div id="movement">
								<div class="row">
									<label class="col-sm-4 col-form-label">Assign Company</label>
									<div class="col-sm-8">
									   <input id="company_destination" class="form-control form-control-sm" disabled>                                    
									</div>
								</div>
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Employement Status</label>
									<div class="col-sm-8">
										<input id="employment_status" class="form-control form-control-sm" disabled>
									</div>
								</div>
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Department</label>
									<div class="col-sm-8">
										<input id="dept" class="form-control form-control-sm" disabled>
									</div>
								</div>
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Position Route</label>
									<div class="col-sm-8">
										<input id="routing" class="form-control form-control-sm" disabled>
									</div>
								</div>
								
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Position Detail</label>
									<div class="col-sm-8">
										<input id="position_detail" class="form-control form-control-sm" disabled>
									</div>
								</div>		
								
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Job Grade</label>
									<div class="col-sm-8">
										<input id="job_grade" class="form-control form-control-sm" style="width: 100%;" disabled>
									</div>
								</div>
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Job Status</label>
									<div class="col-sm-8">
										<input id="job_status" class="form-control form-control-sm" style="width: 100%;" disabled>
									</div>
								</div>
								<div class="form-group-sm row">
									<label class="col-sm-4 col-form-label">New Location</label>
									<div class="col-sm-8">
										<input id="location" class="form-control form-control-sm" style="width: 100%;" disabled>
									</div>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>							
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <input type="hidden" name="file_name" id="file_name">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Max 2 MB</i></label>
								</div>
								<a id="attach" href="#" target="_blank" style="font-size:14px;"><b>Download File</b></a>								
							   </div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment Letter</label>							
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="attachment_letter" class="custom-file-input" id="attachment_letter">
								  <input type="hidden" name="file_name_letter" id="file_name_letter">
								  <span class="invalid-feedback" role="alert" id="attachment_letterError">
                                        <strong></strong>
                                    </span>
									<label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Max 2 MB</i></label>
								</div>
								<a id="attach_letter" href="#" target="_blank" style="font-size:14px;"><b>Download File</b></a>								
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
                                    <textarea name="remark" id="remark" class="form-control form-control-sm" rows="4" placeholder="Max 200 Char" disabled></textarea>
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>
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
var minEffectiveDate, maxEffectiveDate;
var minCreatedDate, maxCreatedDate;
let servercareer = "{{ $servercareer }}";
let global_date_start = null;
let global_date_end = null;
let global_category = "";

let list_date_type = [
	{ id: 'effective_date', text: 'Effective Date' },
    { id: 'creation_date', text: 'Created Date' },
];

$('#date_type').select2({
    placeholder: "Select Date Type",
    data: list_date_type,
});

$(function() {
	$('#daterange').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
		global_date_start = '';
		global_date_end = '';
  	});
});

function daterange(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(7, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
	global_date_start = start;
	global_date_end = end;
	// console.log(global_date_start, global_date_end);
    $('#daterange').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: false,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
            closeText: 'Clear',
        },
        startDate: start, 
        endDate: end,
    }, function(start, end, label) {
        $("#startdate").val(start.format('YYYY-MM-DD'));
        $("#enddate").val(end.format('YYYY-MM-DD'));
		global_date_start = start.format('YYYY-MM-DD');
		global_date_end = end.format('YYYY-MM-DD');
    });

    if($("#startdate").val()=='' || $("#enddate").val()==''){
        $("#startdate").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#enddate").val(moment().format('YYYY-MM-DD'));
		global_date_start = moment().subtract(7, 'days').format('YYYY-MM-DD');
        global_date_end = moment().format('YYYY-MM-DD');
    }
	// console.log(global_date_start, global_date_end)
}

async function getEmployeeByAccessGroup() {
    let status = ['A','I'];
  	let currentPath = "<?= \Request::path() ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('employee/get_employee_by_status_and_access_group') ?>'+'?status='+status+'&path_menu='+currentPath, function (res) { 
        });
        return result;
    } catch (error) {
        getEmployeeByAccessGroup();
    }
}

$(document).ready(function () {
	daterange();
	getEmployeeByAccessGroup().then(function(value) {
        $('#employee_search').html('');
        $('#employee_search').select2({
            placeholder: "Select Employee",
            data: value,
            allowClear: true,
        });
    });
});

function browse_table() {
//	console.log(global_career_type);
if(global_career_type == "Orientation" || global_career_type == "Failed_Orientation" || global_career_type == "Temporary_Assignment" || global_career_type == "Pass_RPK"){
	$('#browseModalIm').modal('show');
	$("#bro_table_im").DataTable({
		scrollY: "400px",
		scrollX: true,
		paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
		  columnDefs:false,	
			fixedColumns: {
				rightColumns: 1,
			},		  
          columns : [
            { data : 'no'},
            { data : 'tgl_surat' },
            { data : 'no_surat' },
            { data : 'name' },
            { data : 'nik' },
            { data : 'kategori' },
            { data : 'effective_date' },
            { data : 'end_date' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
			url: "<?= url('career_administration/career_transition/career_transition_request/browse') . '?career=' ?>" + global_career_type,
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
                  'nik'   : '<center>'+json[i]['nik']+'</center>',
                  'kategori'   : '<center>'+json[i]['kategori']+'</center>',
                  'effective_date'   : '<center>'+json[i]['effective_date']+'</center>',
                  'end_date'   : '<center>'+json[i]['end_date']+'</center>',
                  'action'   : '<center><button type="button" name="check" class="btn btn-success btn-sm" title="Check" onClick="check('+json[i]['id']+')"><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
      });	
	}
else if(global_career_type == "Employment_Status_Changes" || global_career_type == "New_Employee"){
	$('#ModalContract').modal('show');
	$("#bro_table_contract").DataTable({
		scrollY: "400px",
		scrollX: true,
		paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
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
			url: "<?= url('career_administration/career_transition/career_transition_request/browse') . '?career=' ?>" + global_career_type,
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
else{
		$('#browseModal').modal('show');
		$("#bro_table").DataTable({
		scrollY: "400px",
		scrollX: true,
		paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
		  columnDefs:false,	
			fixedColumns: {
				rightColumns: 1,
			},		  
          columns : [
            { data : 'no'},
            { data : 'tgl_surat' },
            { data : 'no_surat' },
            { data : 'name' },
            { data : 'nik' },
            { data : 'kategori' },
            { data : 'keterangan' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
			url: "<?= url('career_administration/career_transition/career_transition_request/browse') . '?career=' ?>" + global_career_type,
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
                  'nik'   : '<center>'+json[i]['nik']+'</center>',
                  'kategori'   : '<center>'+json[i]['kategori']+'</center>',
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
}
function check(id) {	
		//  console.log(id);
		  $.ajax({
			url: "<?= url('career_administration/career_transition/career_transition_request/checkid') . '?careerid=' ?>" + id+"<?= '&career='?>"+global_career_type,
		   dataType:"json",
		   success:function(data)
		   {
			$("#transaction_number").val(data.result[0].no_surat);
			$("#browseModalIm").modal('hide');
			$("#ModalContract").modal('hide');
			$("#browseModal").modal('hide');
		   }
		  })
}
		
$(document).on('click', '.view', function(){
  let id_career_transaction = $(this).attr('id');
   $("#careerForm")[0].reset();
	$("#careerForm .modal-title").html("<span class='far fa-edit'></span> Edit Career Transition History");
		$.ajax({
                url: "<?= url('career_administration/career_transition/career_transition_history/get_career_edit') ?>",
                method: "GET",
                data: {id_career_transaction: id_career_transaction},
                success: function (response) {
					if(response.transition_category == 'Termination'){
						$("#eff_date").html('Terminate Date');
						$("#terminate").css("display","inline");
						$("#movement").css("display","none");
					}
					else{
						$("#eff_date").html('Effective Date');
						$("#terminate").css("display","none");
						$("#movement").css("display","inline");
					}
					str = response.transaction_type;
					global_career_type = str.replace(/\s+/g,'_');
                    $('#id_career_transaction').val(response.id_career_transaction).trigger('change');
                    $('#transaction_number').val(response.transaction_number).trigger('change');
                    $('#name').val(response.name).trigger('change');
                    $('#id_employee').val(response.id_employee).trigger('change');
                    $('#transition_category').val(response.transition_category).trigger('change');
                    $('#old_employment_status').val(response.old_employment_status).trigger('change');
                    $('#employment_status').val(response.employment_status).trigger('change');
                    $('#old_position_detail').val(response.old_position_detail).trigger('change');
                    $('#remark').val(response.remark).trigger('change');
                    $('#old_company').val(response.old_company).trigger('change');
                    $('#company_destination').val(response.company_destination).trigger('change');
                    $('#effective_date').val(response.effective_date).trigger('change');
                    $('#expired_date').val(response.expired_date).trigger('change');
                    $('#file_name').val(response.attachment).trigger('change');
						if(response.attachment == null || response.attachment == ""){
							$('#attach').html('');
						}
						else{
							document.getElementById('attach').href = "../../project/storage/app/public/upload/career/"+response.nik_employee+"/"+response.attachment;
						}
					$('#file_name_letter').val(response.attachment_letter).trigger('change');
						if(response.attachment_letter == null || response.attachment_letter == ""){
							$('#attach_letter').html('');
						}
						else{
							document.getElementById('attach_letter').href = "../../project/storage/app/public/upload/career/"+response.nik_employee+"/"+response.attachment_letter;
						}
                    $('#resign_category').val(response.resign_category).trigger('change');
                    $('#terminate_reason').val(response.terminate_reason).trigger('change');
                   		
                    $('#code_status').val(response.code_status).trigger('change');
					$('#transaction_type').val(response.transaction_type).trigger('change');
					$('#old_dept').val(response.old_dept).trigger('change');
					$('#dept').val(response.dept).trigger('change');
					$('#old_routing').val(response.old_routing).trigger('change');                
					$('#routing').val(response.routing).trigger('change');                
	                $('#old_position_detail').val(response.old_position_detail).trigger('change');
	                $('#position_detail').val(response.position_detail).trigger('change');
	                $('#old_job_grade').val(response.old_job_grade).trigger('change');
	                $('#job_grade').val(response.job_grade).trigger('change');
	                $('#old_job_status').val(response.old_job_status).trigger('change');
	                $('#job_status').val(response.job_status).trigger('change');
	                $('#old_location').val(response.old_location).trigger('change');
	                $('#location').val(response.location).trigger('change');
					global_category = response.code_cat;
					$('#reco_header').val(response.ref_number_reco).trigger('change');
					$('#id_recommendation_header').val(response.id_recommendation_header).trigger('change');
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
			
		$('#modal_form_career').modal('show');
		return false;
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
			   url:"career_transition_history/cancel_career/"+id_career_transaction,
			    beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(response)
			   {
				  if (response.status == 'true') {
							$('#confirmModal').modal('hide');
							swal({
								icon: 'success',
								title: 'Data Cancel',
								text: response.message
							}).then(function(){ 
								$('#career_table').DataTable().ajax.reload();
							   }
							);
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

$(function () {
        $('#careerForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
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
					url: "{{ route('career_history.update') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
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

$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
		
        var minEffective = minEffectiveDate.val();
        var maxEffective = maxEffectiveDate.val();
        var dateEffective = data[17];
		
		var minCreated = minCreatedDate.val();
        var maxCreated = maxCreatedDate.val();
        var dateCreated = data[5];
		if(minCreated == "" && maxCreated == ""){
			if (
				( minEffective === '' && maxEffective === '' ) ||
				( minEffective === '' && dateEffective <= maxEffective ) ||
				( minEffective <= dateEffective   && maxEffective === '' ) ||
				( minEffective <= dateEffective   && dateEffective <= maxEffective )
			) {
				return true;
			}
			else{
				return false;
			}
		}
		
		if(minEffective == "" && maxEffective == ""){
			if (
				( minCreated === '' && maxCreated === '' ) ||
				( minCreated === '' && dateCreated <= maxCreated ) ||
				( minCreated <= dateCreated   && maxCreated === '' ) ||
				( minCreated <= dateCreated   && dateCreated <= maxCreated )
			) {
				return true;
			}
			else{
				return false;
			}
		}
    }
);

	minEffectiveDate = $('#min_effective').datepicker({
		uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
    });
    maxEffectiveDate = $('#max_effective').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	minCreatedDate = $('#min_created').datepicker({
		uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
    });
    maxCreatedDate = $('#max_created').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});

	let date = $('#daterange').val().split(" to ");
	
   var table = $('#career_table').DataTable({
            processing: true,
		//	serverSide: true,     
			pageLength: 10,
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				right: 1,
			},
		//	responsive: true,
		    // ajax: {
            // //    url: "{{ route('career_history.index') }}",
			// 	url: "<?= url('career_administration/career_transition/career_transition_history/') . '?id_url=' ?>" + $("#date_type").val() + `&start_date=${date[0]}&end_date=${date[1]}`,
			// 	error: function (jqXHR, textStatus, errorThrown) {
			// 	//	$('#career_table').DataTable().ajax.reload();
			// 	}
            // },
			
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
                {data: 'transaction_number', name: 'transaction_number', render: function(data, type, row, meta){
					str = row.transaction_type;
					var trans = str.replace(/\s+/g,'_');
					let link = `<div class="api" id_career="${row.id_career_transaction}"> <a href="javascript:;" onclick="apisurat('${row.code_transaction_type}', '${row.transaction_number}', '${row.id_career_transaction}')"><b>${row.transaction_number}</b></a></div>`;

					if(row.transaction_number == null){
						content = '';
					} else {
						content = link;
					}
					return content;

					// if(row.transaction_number == null){
					// 		data = '';
					// }
					// else if(trans == "Orientation" || trans == "Failed_Orientation" || trans == "Temporary_Assignment" || trans == "Pass_RPK"){
					// 		data = '<a href="https://hris.borwita.co.id/nosurat/index.php/im/pdf/'+row.id_surat+'?token='+row.token+'" target="_blank"><b>'+data+'</b></a>';
					// }
					// else if(row.token == ''){
					// 		data = row.transaction_number;
					// }
					// else{					
					// 		data = '<a href="https://hris.borwita.co.id/nosurat/index.php/sk/pdf/'+row.id_surat+'?token='+row.token+'" target="_blank"><b>'+data+'</b></a>';
					// }
					// return data;
				 }},
                {data: 'creation_date', name: 'creation_date'},
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
				{data: 'effective_date', name: 'effective_date'},
				{data: 'expired_date', name: 'expired_date'},
				{data: 'attachment', name: 'attachment',render: function ( data, type, row ) {
					if(row.attachment_custom == ''){
						return "";
					} else {
						return `<a href="${row.attachment_custom}" target="_blank">Download File</a>`;
					}
					
					// if(data == null){
					// 	return "";
					// }	
					// else{
					// 	return '<a href="../../project/storage/app/public/upload/career/'+ row['id_employee'] +'/'+data+'" target="_blank">Download File</a>';
					// }
				  }
				},
				{data: 'attachment_letter', name: 'attachment_letter',render: function ( data, type, row ) {
					if(row.attachment_custom_letter == ''){
						return "";
					} else {
						return `<a href="${row.attachment_custom_letter}" target="_blank">Download File</a>`;
					}
					
					// if(data == null){
					// 	return "";
					// }	
					// else{
					// 	return '<a href="../../project/storage/app/public/upload/career/'+ row['id_employee'] +'/'+data+'" target="_blank">Download File</a>';
					// }
				  }
				},
				{data: 'request_resign_date', name: 'request_resign_date'},
				{data: 'resign_category', name: 'resign_category'},
				{data: 'terminate_reason', name: 'terminate_reason'},
				{data: 'remark', name: 'remark'},
				{data: 'desc_app_status', name: 'desc_app_status'},
				{ data: 'ref_reco', name: 'ref_reco', render: function ( data, type, row ) {	
					if(row.ref_reco != null){
							return data;
					}
					else{
						return '-';
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
            ],
        });
	
	$('#min_effective, #max_effective').on('change', function () {
        table.draw();
    });
	
	$('#min_created, #max_created').on('change', function () {
        table.draw();
    });
	table.on('order.dt search.dt', function () {
        let i = 1;
        table.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();

	$(document).on('click', '#search_created', function () {
		// daterange(global_date_start, global_date_end);
		let date = $('#daterange').val().split(" to ");
		let host = "<?= url('career_administration/career_transition/career_transition_history/') . '?id_url=' ?>" + global_url_server
		let url = host + "&date_type=" + $("#date_type").val() + `&start_date=${date[0]}&end_date=${date[1]}`;
		table.ajax.url(url).load();
		// table.ajax.reload();
	});

	$('#search_created').trigger('click');
	
	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
			$("#range_date").show("slow");
		}
		else {
			$("#cf").hide("slow");
			$("#range_date").hide("slow");
		}		
	});

});



function apisurat(code_transaction_type, transaction_number, id_career) {
    let result;
    let _token      = "<?= csrf_token() ?>";
    try {
        $.ajax({
            type: 'GET',
            url: "<?= url('career_administration/career_transition/career_transition_history/api_surat') ?>",
            dataType: 'json',
            data: {
                transaction_number: transaction_number,
                transaction_type: code_transaction_type,
            },
            success: function (resp) {
            	if(resp.token != ''){
            		if(resp.type == "Orientation" || resp.type == "Failed_Orientation" || resp.type == "Temporary_Assignment" || resp.type == "Pass_RPK"){
	                	window.open(
						  `${servercareer}nosurat/index.php/im/pdf/${resp.id_surat}?token=${resp.token}`,
						  '_blank' 
						);
					} else {
						window.open(
						  `${servercareer}nosurat/index.php/sk/pdf/${resp.id_surat}?token=${resp.token}`,
						  '_blank' 
						);
					}
            	} else {
					$(`.api[id_career="${id_career}"]`).replaceWith(transaction_number);
            	}
            }
        });
    } catch (error) {
		console.log(code_transaction_type, transaction_number, id_career);
        // apisurat(code_transaction_type, transaction_number, id_career);
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
</script>

@endsection