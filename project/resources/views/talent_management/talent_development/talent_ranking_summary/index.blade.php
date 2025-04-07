@extends('adminlte::page')
@section('title', 'Talent Matrix')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Talent Matrix</h5>
				<div class="card-tools">
                </div>
            </div>      
			<div class="card-body">
				
				<div class="row">
					<div class="col-xl-12">
					  <div class="nav nav-tabs justify-content-left mb-4">
						<a class="nav-item nav-link active" data-toggle="tab" href="#tab-panel-filter"><b>Filter Talent Matrix (9 Boxs)</b>
						</a>
						<a class="nav-item nav-link" data-toggle="tab" href="#tab-panel-gen"><b>Generate Talent Progress</b>
						</a>
					  </div>
					</div>
						<div class="tab-content col-12">               
							<div class="tab-pane fade show active" id="tab-panel-filter">
								<div class="row">
									<div class="col-md-6">
										<div class="row">
											<label class="col-sm-4 col-form-label">Talent Reco Name</label>
											<div class="col-sm-8">
												<select id="fil_name" class="form-control form-control-sm select2" data-placeholder="Select Reco Name ..." style="width: 100%;" multiple="multiple"></select>
												<span class="invalid-feedback" role="alert" id="fil_nameError">
													<strong></strong>
												</span>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-form-label">Talent Type</label>
											<div class="col-sm-8">
												<select id="fil_type" class="form-control form-control-sm select2" style="width: 100%;"></select>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-form-label">Talent Period</label>
											<div class="col-sm-8">
												<select id="fil_period" class="form-control form-control-sm select2" style="width: 100%;"></select>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-form-label">Projected Position</label>
											<div class="col-sm-8">
												<select id="fil_pro" class="form-control form-control-sm select2" data-placeholder="Select Projected Position ..." style="width: 100%;" multiple="multiple"></select>
											</div>
										</div>
																								
									</div>	
									<div class="col-md-6">
										<div class="row">
											<label class="col-sm-4 col-form-label">Region</label>
											<div class="col-sm-8">
												<select id="fil_reg" class="form-control form-control-sm select2" data-placeholder="Select Region ..." style="width: 100%;" multiple="multiple"></select>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-form-label">Grade</label>
											<div class="col-sm-8">
												<select id="fil_grade" class="form-control form-control-sm select2" data-placeholder="Select Grade ..." style="width: 100%;" multiple="multiple"></select>
											</div>
										</div>		
										<div class="row">
											<label class="col-sm-4 col-form-label">Department</label>
											<div class="col-sm-8">
												<select id="fil_dept" class="form-control form-control-sm select2" data-placeholder="Select Department ..." style="width: 100%;" multiple="multiple"></select>
											</div>
										</div>
										<div class="row">
											<label class="col-sm-4 col-form-label">Principal</label>
											<div class="col-sm-8">
												<select id="fil_principal" class="form-control form-control-sm select2" data-placeholder="Select Principal ..." style="width: 100%;" multiple="multiple"></select>
											</div>
										</div>						
									</div>	
								</div>
								<br>
								<div class="modal-footer">
									<button onclick="return false;" id="search_fil" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
									<button onclick="return false;" id="reset_fil" class="btn btn-sm btn-secondary" > Reset</button>
								</div>
								
								<div class="row">
									<div class="col-xl-12">
									  <div class="nav nav-tabs justify-content-left mb-4">
										<a class="nav-item nav-link active" data-toggle="tab" href="#tab-panel-hav"><b>HAV Matrix (9 Boxs)</b>
										</a>
										<a id="link_tab_menu-name" class="nav-item nav-link" data-toggle="tab" href="#tab-panel-detail" aria-controls="link_tab_menu-name"><b>Detail List Name</b>
										</a>
									  </div>
									</div>
										<div class="tab-content col-12">               
											<div class="tab-pane fade show active" id="tab-panel-hav">
												  <div class="row">
														<div class="col-lg-2 col-12">
															<p align="right" class="status-y" style="">H</p>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_4" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_4" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 4</a>
																<div class="inner">
																	<h3 id="count_box_4" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>								
																	<h6 align="right" id="talent_box_4" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_2" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_2" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 2</a>
																<div class="inner">
																	<h3 id="count_box_2" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_2" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_1" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_1" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 1</a>
																<div class="inner">
																	<h3 id="count_box_1" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_1" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-1 col-12"></div>
													
														<div class="col-lg-1 col-12">
															<div align="center" style="float:left;-webkit-transform: rotate(-90deg); -moz-transform: rotate(-90deg);font-size:25px;font-weight:bold;vertical-align:middle;line-height: 1.0;">Performance Axis</div>
														</div>
														<div class="col-lg-1 col-12">
															<div class="status-y" style="float:right;">M</div>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_7" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_7" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 7</a>
																<div class="inner">
																	<h3 id="count_box_7" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_7" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_5" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_5" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 5</a>
																<div class="inner">
																	<h3 id="count_box_5" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_5" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_3" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_3" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 3</a>
																<div class="inner">
																	<h3 id="count_box_3" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_3" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-1 col-12"></div>
										
														<div class="col-lg-2 col-12">
															<p align="right" class="status-y">L</p>
														</div>
														<div class="col-lg-3 col-12">
															<div id="box_9" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_9" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 9</a>
																<div class="inner">
																	<h3 id="count_box_9" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_9" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12" >
															<div id="box_8" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_8" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 8</a>
																<div class="inner">
																	<h3 id="count_box_8" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_8" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-3 col-12" >
															<div id="box_6" class="small-box" style="color:white !important;cursor:pointer;">
																<a id="name_box_6" href="#" class="small-box-footer text-bold" style="color:white !important;font-size:18px;">BOX 6</a>
																<div class="inner">
																	<h3 id="count_box_6" style="line-height:0.5;">0 </h3>
																	<h6>Employees</h6>
																	<h6 align="right" id="talent_box_6" style="font-weight:bold;">-</h6>
																</div>
															</div>
														</div>
														<div class="col-lg-1 col-12"></div>
													</div>
													
													<div class="row" style="margin-top:-15px;">
														<div class="col-lg-2 col-12">
														</div>
														<div class="col-lg-3 col-12">
															<div align="center" class="status-x">L</div>
														</div>
														<div class="col-lg-3 col-12" >
															<div align="center" class="status-x">M</div>
															<div align="center" style="font-size:25px;font-weight:bold;">Potential Axis</div>
														</div>
														<div class="col-lg-3 col-12" >
															<div align="center" class="status-x">H</div>
														</div>
														<div class="col-lg-1 col-12"></div>
													</div>		
											</div>
											<div class="tab-pane" id="tab-panel-detail">
												<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
												<br>
												<br>
												<table id="talent_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
												 <thead>
												  <tr>		
													<th></th>
													<th data-priority="3"></th>
													<th data-priority="2">No</th>
													<th>Employee Name</th>
													<th>Matrix Box</th>
													<th>Matrix Name</th>
													<th>KPI</th>
													<th>Rating</th>
													<th>Potencies</th>
													<th>Competencies</th>
													<th>Readiness</th>
													<th>Engagement Level</th>
													<th>Eligibilty Status</th>
													<th data-priority="1">Action</th>
												  </tr>
												 </thead>
												</table>				 
											</div>
									  </div>
								</div>						
							</div>
														
							
							<div class="tab-pane" id="tab-panel-gen">
									<div class="row" style="padding:20px;">
										<div class="col-md-5">
											<div class="row">
												<label class="col-sm-4 col-form-label">Talent Reco Name</label>
												<div class="col-sm-8">
													<select id="reco_name" class="form-control form-control-sm select2" data-placeholder="Select Reco Name ..." style="width: 100%;" multiple="multiple"></select>
													<span class="invalid-feedback" role="alert" id="reco_nameError">
														<strong></strong>
													</span>
												</div>
											</div>
										</div>
										<div class="col-md-1">
										</div>
										<div class="col-md-4">
											<div class="row">
												<label class="col-sm-4 col-form-label">Period Date</label>
												<div class="col-sm-8">
													<input id="reco_date" class="form-control form-control-sm">
													<span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="reco_dateError">
														<strong></strong>
													</span>
												</div>
											</div>
										</div>
										<div class="col-md-2">
											<button onclick="return false;" id="gen_talent" class="btn btn-sm btn-success" ><i class="fa fa-refresh"></i> Generate Talent</button>
										</div>
									</div>
							</div>
					  </div>
				</div>
					
			</div>
        </div>
	</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-xl">
  <div id="modal_second"></div>
    <!-- Modal content-->
    <div class="modal-content">
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
	</div>
  </div>
</div>

<div class="modal fade" id="modal_profile"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
		<form method="POST" id="committeeForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="title_profile" class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="contentProfile"></div>
		</form>
				<div style="display:none;">
					<table id="sample_table_risk">
						<tr id="" style="font-size:14px;">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
								<input name="risk[0][id_talent_profile_note]" id="risk_0_id_talent_profile_note" type="hidden" class="id_talent_profile_note_input">
								<select name="risk[0][id_routing]" id="risk_0_id_routing" class="form-control form-control-sm select2 id_routing_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_routing_input_error" role="alert" id="risk_0_id_routingError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="risk[0][id_category]" id="risk_0_id_category" class="form-control form-control-sm select2 id_category_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_category_input_error" role="alert" id="risk_0_id_categoryError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<div class="input-group">
								  <input type="text" autocomplete="off" name="risk[0][committee_date]" id="risk_0_committee_date" class="form-control form-control-sm committee_date_input " style="width:100%;">
								  <div class="input-group-append">
									<span class="input-group-text" style="font-size: 1.05rem;">
									  <i class="fas fa-calendar"></i>
									</span>
								  </div>
								</div>
								<span class="invalid-date committee_date_input_error" style="font-size:11px;color:#dc3545;" role="alert" id="risk_0_committee_dateError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="risk[0][id_activity]" id="risk_0_id_activity" class="form-control form-control-sm select2 id_activity_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_activity_input_error" role="alert" id="risk_0_id_activityError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<textarea name="risk[0][committee_note]" id="risk_0_committee_note" rows="3" class="form-control form-control-sm committee_note_input"></textarea>
								<span class="invalid-feedback committee_note_input_error" role="alert" id="risk_0_committee_noteError">
									<strong></strong>
								</span>	
							</td>
							
						<td style="white-space:nowrap;">
						<center>
							<button type="button" class="add-record-risk btn btn-xs btn-success save" data-id="0" onclick="risk_save(0)" title="Save" style="margin-right:5px;"><span class="fas fa-save"></span></button>
							<button type="button" id="risk_0_del_rec" class="delete-record-risk btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
				</div> 
				
				<div style="display:none;">
					<table id="sample_table_plan">
						<tr id="" style="font-size:14px;">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
								<input name="plan[0][id_talent_profile_note_plan]" id="plan_0_id_talent_profile_note_plan" type="hidden" class="id_talent_profile_note_plan_input">
								<select name="plan[0][id_routing_plan]" id="plan_0_id_routing_plan" class="form-control form-control-sm select2 id_routing_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_routing_plan_input_error" role="alert" id="plan_0_id_routing_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="plan[0][id_category_plan]" id="plan_0_id_category_plan" class="form-control form-control-sm select2 id_category_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_category_plan_input_error" role="alert" id="plan_0_id_category_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<div class="input-group">
								  <input type="text" autocomplete="off" name="plan[0][committee_date_plan]" id="plan_0_committee_date_plan" class="form-control form-control-sm committee_date_plan_input " style="width:100%;">
								  <div class="input-group-append">
									<span class="input-group-text" style="font-size: 1.05rem;">
									  <i class="fas fa-calendar"></i>
									</span>
								  </div>
								</div>
								<span class="invalid-date committee_date_plan_input_error" style="font-size:11px;color:#dc3545;" role="alert" id="plan_0_committee_date_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="plan[0][id_activity_plan]" id="plan_0_id_activity_plan" class="form-control form-control-sm select2 id_activity_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_activity_plan_input_error" role="alert" id="plan_0_id_activity_planError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<textarea name="plan[0][committee_note_plan]" id="plan_0_committee_note_plan" rows="3" class="form-control form-control-sm committee_note_plan_input"></textarea>
								<span class="invalid-feedback committee_note_plan_input_error" role="alert" id="plan_0_committee_note_planError">
									<strong></strong>
								</span>	
							</td>
							
						<td style="white-space:nowrap;">
						<center>
							<button type="button" class="add-record-plan btn btn-xs btn-success save-plan" data-id="0" onclick="plan_save(0)" title="Save" style="margin-right:5px;"><span class="fas fa-save"></span></button>
							<button type="button" id="plan_0_del_rec_plan" class="delete-record-plan btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
				</div> 
			
        </div>
	</div>
</div>

@endsection
@section('css')
<style type="text/css">
	.modal-lg {
        max-width: 90% !important;
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
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
	.custom-select:valid + .select2 .select2-selection{
	  border-color: #dc3545!important;
	}
	*:focus{
	  outline:0px;
	}
	.status-x{
		font-size:30px;
		font-weight:bold;
		font-style: italic;
		color:#8a8686;
	}
	.status-y{
		font-size:30px;
		font-weight:bold;
		font-style: italic;
		margin-top:50px;
		color:#8a8686;
	}
	.green {
		background-color : #54b556;
	}
	.yellow {
		background-color : #c4c106;
	}
	.blue {
		background-color : #007ca2;
	}
	.orange {
		background-color : #e36a04;;
	}
	.red {
		background-color : #ef5454;
	}
	td.text-middle{
		vertical-align:middle;
		text-align:center;
	}
	td.text-center{
		text-align:center;
	}
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	th.th-text-score{
		width:10px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
	th.th-text-date{
		width:80px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
</style>
@stop
@section('scripts')
<script src="{{ asset('vendor/datatables/js/dataTables.rowsGroup.js') }}"></script>
<script type="text/javascript">
let global_box = "";

let param_id_reco_name = '';
let param_reco_date = '';

let param_fil_name = '';
let param_fil_type = '';
let param_fil_period = '';
let param_fil_pro = '';
let param_fil_reg = '';
let param_fil_grade = '';
let param_fil_dept = '';
let param_fil_principal = '';

let global_category = [];
let global_activity = [];
let global_position = [];
let global_category_plan = [];
let global_activity_plan = [];

let global_id_rec_risk = 0;
let global_id_rec_plan = 0;
let global_id_talent_profile_note = "";
let global_employee = 0;
let global_nik = "";

$(document).ready(function(){
	$('#box_1').addClass('green');
	$('#box_2').addClass('yellow');
	$('#box_3').addClass('yellow');
	$('#box_4').addClass('blue');
	$('#box_5').addClass('yellow');
	$('#box_6').addClass('blue');
	$('#box_7').addClass('blue');
	$('#box_8').addClass('red');
	$('#box_9').addClass('red');
	
	get_reco_name();
	filter_projected();
	filter_region();
	filter_grade();
	filter_dept();
	filter_principal();
	filter_type();
	
	$('#fil_period').prepend('<option selected></option>').select2({
		placeholder: "Select Period ...",
	});
/*	
	filter_type().then(function() {
		filter_period($("#fil_type").val()).then(function() {
			param_fil_name = $("#fil_name").val();
			param_fil_type = $("#fil_type").val();
			param_fil_period = $("#fil_period").val();
			param_fil_pro = $("#fil_pro").val();
			param_fil_reg = $("#fil_reg").val();
			param_fil_grade = $("#fil_grade").val();
			param_fil_dept = $("#fil_dept").val();
			param_fil_principal = $("#fil_principal").val();
			get_box(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
			$('#link_tab_menu-name').click(function(){		
				extendDatatable().then(function() {
					all_talent(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
				});	
			}); 
		}); 
	}); 
	*/
	$('#reco_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
//	get_box(param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
		
});

	const all_talent = async (param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal) => {	
		let myDataAll = {
			fil_type: param_fil_type,
			fil_period: param_fil_period,
			fil_name: param_fil_name == '' ? null : param_fil_name.join(','),
			fil_pro: param_fil_pro == '' ? null : param_fil_pro.join(','),
			fil_reg: param_fil_reg == '' ? null : param_fil_reg.join(','),
			fil_grade: param_fil_grade == '' ? null : param_fil_grade.join(','),
			fil_dept: param_fil_dept == '' ? null : param_fil_dept.join(','),
			fil_principal: param_fil_principal == '' ? null : param_fil_principal.join(','),
		};
		let t_info = $('#talent_table').DataTable({
				destroy:true,
				processing: true,
				responsive: true,
				pageLength: 10,
				ajax: {
					url: "{{ route('matrix.index') }}",
					data: myDataAll,
					error: function (jqXHR, textStatus, errorThrown) {
					//		$('#talent_table').DataTable().ajax.reload();
						}
				  },
				columns: [
					{   // Detail Responsive
						data: '',
						defaultContent: '',
						orderable: false
					},
					{   // Checkbox select column
					data: 'id_talent_recommendation_detail',
					orderable: false,
					},
					{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
					{ data: 'emp_name', name: 'emp_name' },
					{ data: 'matrix_box', name: 'matrix_box' },
					{ data: 'matrix_name', name: 'matrix_name' },
					{ data: 'kpi_average', name: 'kpi_average' },
					{ data: 'rating', name: 'rating' },
					{ data: 'potencies', name: 'potencies' },
					{ data: 'competencies', name: 'competencies' },
					{ data: 'readyness', name: 'readyness' },
					{ data: 'engagement_level', name: 'engagement_level' },
					{ data: 'eligible_status', name: 'eligible_status' },
					{data: 'action', name: 'action', className:'space' ,orderable: false, render: function (data, type, row) {					
							return data;
						}
					},
				],
			 
			});
		/*	$("#searchbox").keyup(function() {
				t_info.fnFilter(this.value);
			});
		*/
		/*	t_info.on('order.dt search.dt', function () {
		        let i = 1;
		        t_info.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
		            this.data(i++);
		        });
		    }).draw();
		*/
	}
	
$('#advanced').click(function(){
	$('.cf').select2({width:'100%'});
	if($("#cf").css('display') == 'none'){
		$("#cf").show("slow");
	}
	else {
		$("#cf").hide("slow");
	}		
});
			
	const filter_type = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_type') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_type').select2({
						data: res,
					});
				},
			});
			return result;
		} catch (error) {
			filter_type();
		}	
	}
	
	const filter_period = async (idReco) => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_period') ?>',
				data:{idReco:idReco},
				dataType: 'json',
				success: function (res) {
					$('#fil_period').select2({
						placeholder: "Select Period ...",
						data: res,
						allowClear: true,						
					});
				},
			});
			return result;
		} catch (error) {
			filter_period(idReco);
		}	
	}
	
/*	$(document).on('change', '#fil_type', function (event, istrigger) {  
		$('#fil_period').empty();
		if(!istrigger){
			filter_period($(this).select2('val'));	
		}
	});
*/	
	$(document).on('change', '#fil_name', function (event, istrigger) {  
		$('#fil_period').empty();
		if(!istrigger){
			filter_period($(this).select2('val'));	
		}
	});
	
	const filter_projected = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_projected') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_pro').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			filter_projected();
		}	
	}
	
	const filter_region = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_region') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_reg').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			filter_region();
		}	
	}
	
	const filter_grade = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_grade') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_grade').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			filter_grade();
		}	
	}
	
	const filter_dept = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_dept') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_dept').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			filter_dept();
		}	
	}
	
	const filter_principal = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/filter_principal') ?>',
				dataType: 'json',
				success: function (res) {
					$('#fil_principal').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			filter_principal();
		}	
	}
	
	const get_reco_name = async () => {
		let result;
		try {
			result = await $.ajax({
				url: '<?= url('talent_management/talent_development/talent_ranking_summary/get_reco_name') ?>',
				dataType: 'json',
				success: function (res) {
					$('#reco_name').select2({
						data: res,
						allowClear: true,
					});
					$('#fil_name').select2({
						data: res,
						allowClear: true,
					});
				},
			});
			return result;
		} catch (error) {
			get_reco_name();
		}	
	}
	
	
$(document).on('click', '#gen_talent', function () {	
	param_id_reco_name = $("#reco_name").val();
	param_reco_date = $("#reco_date").val();
	swal({
        title: 'Are you sure?',
        text: 'This Data will be Generated!',
        icon: 'warning',
       buttons: true,
		  confirmButtonText: 'Yes, Generate it!'
    }).then(function(value) {
		if (value) {
				gen_box_talent(param_id_reco_name,param_reco_date);			
			}
		});
});

$(document).on('click', '#search_fil', function () {	
	param_fil_name = $("#fil_name").val();
	param_fil_type = $("#fil_type").val();
	param_fil_period = $("#fil_period").val();
	param_fil_pro = $("#fil_pro").val();
	param_fil_reg = $("#fil_reg").val();
	param_fil_grade = $("#fil_grade").val();
	param_fil_dept = $("#fil_dept").val();
	param_fil_principal = $("#fil_principal").val();
	get_box(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
	$('#link_tab_menu-name').click(function(){		
		extendDatatable().then(function() {
			all_talent(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
		});	
	}); 
	all_talent(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
});

$(document).on('click', '#reset_fil', function () {
//	param_fil_type = $("#fil_type").val();
	param_fil_type = null;
	param_fil_period = $("#fil_period").val();
	param_fil_name = '';
	param_fil_pro = '';
	param_fil_reg = '';
	param_fil_grade = '';
	param_fil_dept = '';
	param_fil_principal = '';
	$('#fil_name').empty();
	$("#fil_period").empty();
	$('#fil_pro').empty();
	$('#fil_reg').empty();
	$('#fil_grade').empty();
	$('#fil_dept').empty();
	$('#fil_principal').empty();
	get_reco_name();
	filter_projected();
	filter_region();
	filter_grade();
	filter_dept();
	filter_principal();
	get_box(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
	$('#link_tab_menu-name').click(function(){		
		extendDatatable().then(function() {
			all_talent(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
		});	
	}); 
	all_talent(param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
});
	

function loadmodal(code_box,name_box,param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal){
	if(name_box != 'null'){
		var nameBox = "("+name_box+")";
	}
	else{
		var nameBox = "";
	}
	$("#contentBody").html('');
	$("#modal-title").html("Matrix Box "+nameBox);
	let myParam = {
		global_box:code_box,
		fil_name: param_fil_name,
		fil_type: param_fil_type,
		fil_period: param_fil_period,
		fil_pro: param_fil_pro,
		fil_reg: param_fil_reg,
		fil_grade: param_fil_grade,
		fil_dept: param_fil_dept,
		fil_principal: param_fil_principal,
	};
    $.ajax({
			url: "{{ route('matrix.modal_detail') }}",
		//	data:{global_box:code_box},
			data: myParam,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(result){
				$("#contentBody").html(result);
				$("#myModal").modal('show'); 
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
	});
}

const gen_box_talent = async (param_id_reco_name,param_reco_date) => {
	let result;
	let myData = {		
		reco_name: param_id_reco_name == '' ? null : param_id_reco_name.join(','),
		reco_date: param_reco_date == '' ? null : param_reco_date,
	};
    try {
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("select").removeClass("custom-select");
		$("input").removeClass("is-invalid");
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_ranking_summary/gen_box_talent') ?>',
			data: myData,
            dataType: 'json',
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
				if (res.status == 'true') {
						swal({
							icon: 'success',
							title: 'Success',
							text: res.message
						}).then(function(){ 
						   location.reload();
						   }
						);
				} else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! '+res.message,
					});
				}
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (res) {
				if (res.status === 422) {
					$('#loader').addClass('hidden');
					let errors = res.responseJSON.errors;
					Object.keys(errors).forEach(function (key) {
						var key_temp = key.replaceAll(".", "_");
						$("#" + key_temp).addClass("is-invalid");
						$("select[id='" + key + "']").addClass("custom-select");
						$("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
					});
				}
				else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! '+res.message,
					});
				}
			}
        });
        return result;
    } catch (error) {
     //   get_box();
    }	
}

const get_box = async (param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal) => {
	let result;
	let myData = {
		fil_type: param_fil_type == '' ? null : param_fil_type,
		fil_period: param_fil_period == '' ? null : param_fil_period,
		fil_name: param_fil_name == '' ? null : param_fil_name.join(','),
		fil_pro: param_fil_pro == '' ? null : param_fil_pro.join(','),
		fil_reg: param_fil_reg == '' ? null : param_fil_reg.join(','),
		fil_grade: param_fil_grade == '' ? null : param_fil_grade.join(','),
		fil_dept: param_fil_dept == '' ? null : param_fil_dept.join(','),
		fil_principal: param_fil_principal == '' ? null : param_fil_principal.join(','),
	};
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_ranking_summary/get_box') ?>',
			data: myData,
            dataType: 'json',
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
				$.each(res, function (i, item) {
					if(item.code == 'BOX1'){
						$('#box_1').attr('onclick','loadmodal("'+item.code+'","BOX 1","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_1').html(item.count_emp);
						}
						else{
							$('#count_box_1').html('0');
						}
						$('#talent_box_1').html(item.matrix_name);
					}
					else if(item.code == 'BOX2'){
						$('#box_2').attr('onclick','loadmodal("'+item.code+'","BOX 2","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_2').html(item.count_emp);
						}
						else{
							$('#count_box_2').html('0');
						}
						$('#talent_box_2').html(item.matrix_name);
					}
					else if(item.code == 'BOX3'){
						$('#box_3').attr('onclick','loadmodal("'+item.code+'","BOX 3","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_3').html(item.count_emp);
						}
						else{
							$('#count_box_3').html('0');
						}
						$('#talent_box_3').html(item.matrix_name);
					}
					else if(item.code == 'BOX4'){
						$('#box_4').attr('onclick','loadmodal("'+item.code+'","BOX 4","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_4').html(item.count_emp);							
						}
						else{
							$('#count_box_4').html('0');
						}
						$('#talent_box_4').html(item.matrix_name);
					}
					else if(item.code == 'BOX5'){
						$('#box_5').attr('onclick','loadmodal("'+item.code+'","BOX 5","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_5').html(item.count_emp);
						}
						else{
							$('#count_box_5').html('0');
						}
						$('#talent_box_5').html(item.matrix_name);
					}
					else if(item.code == 'BOX6'){
						$('#box_6').attr('onclick','loadmodal("'+item.code+'","BOX 6","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_6').html(item.count_emp);
						}
						else{
							$('#count_box_6').html('0');
						}
						$('#talent_box_6').html(item.matrix_name);
					}
					else if(item.code == 'BOX7'){
						$('#box_7').attr('onclick','loadmodal("'+item.code+'","BOX 7","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_7').html(item.count_emp);
						}
						else{
							$('#count_box_7').html('0');
						}
						$('#talent_box_7').html(item.matrix_name);
					}
					else if(item.code == 'BOX8'){
						$('#box_8').attr('onclick','loadmodal("'+item.code+'","BOX 8","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_8').html(item.count_emp);
						}
						else{
							$('#count_box_8').html('0');
						}
						$('#talent_box_8').html(item.matrix_name);
					}
					else if(item.code == 'BOX9'){
						$('#box_9').attr('onclick','loadmodal("'+item.code+'","BOX 9","'+param_fil_name+'","'+param_fil_type+'","'+param_fil_period+'","'+param_fil_pro+'","'+param_fil_reg+'","'+param_fil_grade+'","'+param_fil_dept+'","'+param_fil_principal+'")');
						if(item.count_emp != null){
							$('#count_box_9').html(item.count_emp);
						}
						else{
							$('#count_box_9').html('0');
						}
						$('#talent_box_9').html(item.matrix_name);
					}
					
				});
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
     //   get_box();
    }	
}

function get_box_matrix(code_box,param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal) {
	let myData = {
		code_box: code_box,
		fil_type: param_fil_type,
		fil_period: param_fil_period,
		fil_name: param_fil_name == '' ? null : param_fil_name,
		fil_pro: param_fil_pro == '' ? null : param_fil_pro,
		fil_reg: param_fil_reg == '' ? null : param_fil_reg,
		fil_grade: param_fil_grade == '' ? null : param_fil_grade,
		fil_dept: param_fil_dept == '' ? null : param_fil_dept,
		fil_principal: param_fil_principal == '' ? null : param_fil_principal,
	};
	$('#view_table').DataTable({	
		destroy:true,
		processing: true,
        responsive: true,
		ajax: {
			url: "{{ route('matrix.index_modal') }}",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#view_table').DataTable().ajax.reload();
				}
		},	
		 columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_talent_recommendation_detail',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'emp_name', name: 'emp_name' },
    			{ data: 'matrix_box', name: 'matrix_box' },
    			{ data: 'matrix_name', name: 'matrix_name' },
    			{ data: 'kpi_average', name: 'kpi_average' },
    			{ data: 'rating', name: 'rating' },
    			{ data: 'potencies', name: 'potencies' },
    			{ data: 'competencies', name: 'competencies' },
    			{ data: 'readyness', name: 'readyness' },
    			{ data: 'engagement_level', name: 'engagement_level' },
    			{ data: 'eligible_status', name: 'eligible_status' },
            ],
	});
	
		$('#adv_modal').click(function(){
			if($(".view_table").css('display') == 'none'){
				$(".view_table").show("slow");
			}
			else {
				$(".view_table").hide("slow");
			}		
		});
}

const get_profile = async (id_emp,nik_emp) => {
	let result;
	global_employee = id_emp;
	global_nik = nik_emp;
	$("#contentProfile").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");			
	$("#title_profile").html('Talent Profile Card');
    try {
        result = await $.ajax({
			url: "{{ route('talent_profile.modal_talent_profile') }}",
			data:{
				global_emp:id_emp,
				global_nik:nik_emp,
			},
			success: function(result){
				$("#contentProfile").html(result);
				$("#modal_profile").modal('show');
			}
        });
        return result;
    } catch (error) {
        get_profile(id_emp,nik_emp);
    }	
}

$(document).on('click', '#new_rec_risk', function () {
            var content = jQuery('#sample_table_risk tr'),
                    size = global_id_rec_risk++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-risk-'+size);
			element.find('.add-record-risk').attr('onclick', 'risk_save(' + size + ')');
			element.find('.add-record-risk').attr('data-id', size);
            element.find('.delete-record-risk').attr('data-id', size);
            element.find('.id_talent_profile_note_input').attr('id', 'risk_' + size + '_id_talent_profile_note');
            element.find('.id_talent_profile_note_input').attr('name', 'risk[' + size + '][id_talent_profile_note]');
			
			element.find('.id_routing_input').attr('id', 'risk_' + size + '_id_routing');
            element.find('.id_routing_input').attr('name', 'risk[' + size + '][id_routing]');
            element.find('.id_routing_input_error').attr('id', 'risk_' + size + '_id_routingError');
            element.find('.id_routing_input').prepend('<option selected></option>').select2({
                placeholder: "Select Projected Position ...",
                data: global_position,
            });

			element.find('.id_category_input').attr('id_risk', size);
			element.find('.id_category_input').attr('id', 'risk_' + size + '_id_category');
            element.find('.id_category_input').attr('name', 'risk[' + size + '][id_category]');
            element.find('.id_category_input_error').attr('id', 'risk_' + size + '_id_categoryError');
            element.find('.id_category_input').prepend('<option selected></option>').select2({
                placeholder: "Select Category ...",
                data: global_category,
            });
		/*	.on('change', function (e) {
				if(global_category.length > 0){
					get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
				}
            });
		*/	
			element.find('.committee_date_input').attr('id', 'risk_' + size + '_committee_date');
			element.find('.committee_date_input').attr('name', 'risk[' + size + '][committee_date]');
			element.find('.committee_date_input_error').attr('id', 'risk_' + size + '_committee_dateError');
			element.find('.committee_date_input').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				autoApply: true,
				locale: {
				  format: 'YYYY-MM-DD'
				}
			}).on('apply.daterangepicker', function(ev, picker) {
				var startDate = picker.startDate;
				var endDate = picker.endDate;
				$(this).val(startDate.format('YYYY-MM-DD'));
			}).on('cancel.daterangepicker', function() {
				$(this).val('');
			}).on('keydown.daterangepicker',function(e) {
				e.preventDefault();
			});
			
			element.find('.id_activity_input').attr('id', 'risk_' + size + '_id_activity');
            element.find('.id_activity_input').attr('name', 'risk[' + size + '][id_activity]');
            element.find('.id_activity_input_error').attr('id', 'risk_' + size + '_id_activityError');
            element.find('.id_activity_input').prepend('<option selected></option>').select2({
                placeholder: "Select Activity ...",
				allowClear: true,
            });
			
			element.find('.committee_note_input').attr('id', 'risk_' + size + '_committee_note');
			element.find('.committee_note_input').attr('name', 'risk[' + size + '][committee_note]');
			element.find('.committee_note_input_error').attr('id', 'risk_' + size + '_committee_noteError');
			
			element.find('.delete-record-risk').attr('id', 'risk_' + size + '_del_rec');
								
            element.appendTo('#table_rec_risk_body');
			 $('#table_rec_risk_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
	$(document).on('click', '.delete-record-risk', function () {
		var id = jQuery(this).attr('data-id');
		var id_del_risk = $("#risk_" + id + "_id_talent_profile_note").val();
		if(id_del_risk == ""){
			jQuery('#rec-risk-' + id).remove();
			$('#table_rec_risk_body tr').each(function (index) {				
				$(this).find('span.sn').html(index + 1);
			});
		//	return true;
		}
		else{
			swal({
				title: 'Are you sure?',
				text: 'This record will be Deleted!',
				icon: 'warning',
				 buttons: {
					cancel: {
					  text: "No",
					  value: false,
					  visible: true,
					  className: "",
					  closeModal: true,
					},
					confirm: {
					  text: "Yes",
					  value: true,
					  visible: true,
					  className: "",
					  closeModal: true
					}
				  }							 
			}).then(function(value) {
				if (value) {
					$.ajax({
						url: "{{route('committee.deleted')}}",
					//	type: 'POST',
						data:{id_talent_profile_note : id_del_risk},
						beforeSend: () => $('#loader').removeClass('hidden'),
						success: (res) => {
							swal({
								icon: 'success',
								title: 'Success',
								text: res.message
							})
						},
						error: (res) => {
							swal({
								icon: 'error',
								title: 'Error',
								text: res.message
							})
						},
						complete: () => {
							jQuery('#rec-risk-' + id).remove();
							$('#table_rec_risk_body tr').each(function (index) {				
								$(this).find('span.sn').html(index + 1);
							});
							
							$('#loader').addClass('hidden');						
						},
					})
				}
			});
		}
	return true;
	});

$(document).on('change', '.id_category_input', function (event, istrigger) { 
	var size = $(this).attr('id_risk');
	if(!istrigger){
		if(global_category.length > 0){
			get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
		}
	}
});	
	
function risk_save(counter) {	 
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$("#table_rec_risk_body tr td input").removeClass("is-invalid");
	$("#table_rec_risk_body tr td select").removeClass("is-invalid");
	$("#table_rec_risk_body tr td textarea").removeClass("is-invalid");
	$(".table-invalid-feedback").children("strong").text(""); 
	$(".error-tab").html("");
	let at_risk = [];
	let formData = {
        id_routing: $("#risk_" + counter + "_id_routing").val() == '' ? null : $("#risk_" + counter + "_id_routing").val(),
        id_category: $("#risk_" + counter + "_id_category").val() == '' ? null : $("#risk_" + counter + "_id_category").val(),
        committee_date: $("#risk_" + counter + "_committee_date").val() == '' ? null : $("#risk_" + counter + "_committee_date").val(),
        id_activity: $("#risk_" + counter + "_id_activity").val() == '' ? null : $("#risk_" + counter + "_id_activity").val(),
        committee_note: $("#risk_" + counter + "_committee_note").val() == '' ? null : $("#risk_" + counter + "_committee_note").val(),
    };
	at_risk[counter] = formData;
	global_id_talent_profile_note = $("#risk_" + counter + "_id_talent_profile_note").val();
	$.ajax({
	//	type: 'POST',
		headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: global_id_talent_profile_note == '' ? "<?= url('talent_management/talent_development/talent_profile/save') . '?counter=' ?>" + counter + "<?= '&id_employee='?>" + global_employee : "<?= url('talent_management/talent_development/talent_profile/update') . '?id_talent_profile_note=' ?>" + global_id_talent_profile_note + "<?= '&counter='?>" + counter,
		data: {risk:at_risk},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(response) {
		   if (response.status == 'true') {
			   $("#risk_" + counter + "_id_talent_profile_note").val(response.id_talent_profile_note);
				swal({
					icon: 'success',
					title: 'Success',
					text: response.message
				});
				
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: response.message
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function(response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					
				});
			} else {
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

$(document).on('click', '#new_rec_plan', function () {
            var content = jQuery('#sample_table_plan tr'),
                    size = global_id_rec_plan++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-plan-'+size);
			element.find('.add-record-plan').attr('onclick', 'plan_save(' + size + ')');
			element.find('.add-record-plan').attr('data-id', size);
            element.find('.delete-record-plan').attr('data-id', size);
            element.find('.id_talent_profile_note_plan_input').attr('id', 'plan_' + size + '_id_talent_profile_note_plan');
            element.find('.id_talent_profile_note_plan_input').attr('name', 'plan[' + size + '][id_talent_profile_note_plan]');
			
			element.find('.id_routing_plan_input').attr('id', 'plan_' + size + '_id_routing_plan');
            element.find('.id_routing_plan_input').attr('name', 'plan[' + size + '][id_routing_plan]');
            element.find('.id_routing_plan_input_error').attr('id', 'plan_' + size + '_id_routing_planError');
            element.find('.id_routing_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Projected Position ...",
                data: global_position,
            });
			
			element.find('.id_category_plan_input').attr('id_plan', size);
			element.find('.id_category_plan_input').attr('id', 'plan_' + size + '_id_category_plan');
            element.find('.id_category_plan_input').attr('name', 'plan[' + size + '][id_category_plan]');
            element.find('.id_category_plan_input_error').attr('id', 'plan_' + size + '_id_category_planError');
            element.find('.id_category_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Category ...",
                data: global_category_plan,
            });
		/*	.on('change', function (e) {
				if(global_category_plan.length > 0){
					get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
				}
            });
		*/	
			element.find('.committee_date_plan_input').attr('id', 'plan_' + size + '_committee_date_plan');
			element.find('.committee_date_plan_input').attr('name', 'plan[' + size + '][committee_date_plan]');
			element.find('.committee_date_plan_input_error').attr('id', 'plan_' + size + '_committee_date_planError');
			element.find('.committee_date_plan_input').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				autoApply: true,
				locale: {
				  format: 'YYYY-MM-DD'
				}
			}).on('apply.daterangepicker', function(ev, picker) {
				var startDate = picker.startDate;
				var endDate = picker.endDate;
				$(this).val(startDate.format('YYYY-MM-DD'));
			}).on('cancel.daterangepicker', function() {
				$(this).val('');
			}).on('keydown.daterangepicker',function(e) {
				e.preventDefault();
			});
			
			element.find('.id_activity_plan_input').attr('id', 'plan_' + size + '_id_activity_plan');
            element.find('.id_activity_plan_input').attr('name', 'plan[' + size + '][id_activity_plan]');
            element.find('.id_activity_plan_input_error').attr('id', 'plan_' + size + '_id_activity_planError');
		    element.find('.id_activity_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Activity ...",
				allowClear: true,
            });
		
			element.find('.committee_note_plan_input').attr('id', 'plan_' + size + '_committee_note_plan');
			element.find('.committee_note_plan_input').attr('name', 'plan[' + size + '][committee_note_plan]');
			element.find('.committee_note_plan_input_error').attr('id', 'plan_' + size + '_committee_note_planError');
			
			element.find('.delete-record-plan').attr('id', 'plan_' + size + '_del_rec_plan');
								
            element.appendTo('#table_rec_plan_body');
			 $('#table_rec_plan_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
/*	$(document).on('click', '.delete-record-plan', function () {
		var id = jQuery(this).attr('data-id');
		jQuery('#rec-plan-' + id).remove();
		$('#table_rec_plan_body tr').each(function (index) {				
			$(this).find('span.sn').html(index + 1);
		});
		return true;
	});
*/	
	$(document).on('click', '.delete-record-plan', function () {
		var id = jQuery(this).attr('data-id');
		var id_del_plan = $("#plan_" + id + "_id_talent_profile_note_plan").val();
		if(id_del_plan == ""){
			jQuery('#rec-plan-' + id).remove();
			$('#table_rec_plan_body tr').each(function (index) {				
				$(this).find('span.sn').html(index + 1);
			});
		//	return true;
		}
		else{
			swal({
				title: 'Are you sure?',
				text: 'This record will be Deleted!',
				icon: 'warning',
				 buttons: {
					cancel: {
					  text: "No",
					  value: false,
					  visible: true,
					  className: "",
					  closeModal: true,
					},
					confirm: {
					  text: "Yes",
					  value: true,
					  visible: true,
					  className: "",
					  closeModal: true
					}
				  }							 
			}).then(function(value) {
				if (value) {
					$.ajax({
						url: "{{route('committee.deleted')}}",
					//	type: 'POST',
						data:{id_talent_profile_note : id_del_plan},
						beforeSend: () => $('#loader').removeClass('hidden'),
						success: (res) => {
							swal({
								icon: 'success',
								title: 'Success',
								text: res.message
							})
						},
						error: (res) => {
							swal({
								icon: 'error',
								title: 'Error',
								text: res.message
							})
						},
						complete: () => {
							jQuery('#rec-plan-' + id).remove();
							$('#table_rec_plan_body tr').each(function (index) {				
								$(this).find('span.sn').html(index + 1);
							});						
							$('#loader').addClass('hidden');						
						},
					})
				}
			});
		}
	return true;
	});

	
$(document).on('change', '.id_category_plan_input', function (event, istrigger) { 
	var size = $(this).attr('id_plan');
	if(!istrigger){
		if(global_category.length > 0){
			get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
		}
	}
});		

function plan_save(counter) {	 
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$("#table_rec_plan_body tr td input").removeClass("is-invalid");
	$("#table_rec_plan_body tr td select").removeClass("is-invalid");
	$("#table_rec_plan_body tr td textarea").removeClass("is-invalid");
	$(".table-invalid-feedback").children("strong").text(""); 
	$(".error-tab").html("");
	let at_plan = [];
	let formData = {
        id_routing: $("#plan_" + counter + "_id_routing_plan").val() == '' ? null : $("#plan_" + counter + "_id_routing_plan").val(),
        id_category: $("#plan_" + counter + "_id_category_plan").val() == '' ? null : $("#plan_" + counter + "_id_category_plan").val(),
        committee_date: $("#plan_" + counter + "_committee_date_plan").val() == '' ? null : $("#plan_" + counter + "_committee_date_plan").val(),
        id_activity: $("#plan_" + counter + "_id_activity_plan").val() == '' ? null : $("#plan_" + counter + "_id_activity_plan").val(),
        committee_note: $("#plan_" + counter + "_committee_note_plan").val() == '' ? null : $("#plan_" + counter + "_committee_note_plan").val(),
    };
	global_id_talent_profile_note = $("#plan_" + counter + "_id_talent_profile_note_plan").val();
	at_plan[counter] = formData;
	$.ajax({
	//	type: 'POST',
		headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: global_id_talent_profile_note == '' ? "<?= url('talent_management/talent_development/talent_profile/save') . '?counter=' ?>" + counter + "<?= '&id_employee='?>" + global_employee : "<?= url('talent_management/talent_development/talent_profile/update') . '?id_talent_profile_note=' ?>" + global_id_talent_profile_note + "<?= '&counter='?>" + counter,
		data: {plan:at_plan},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(response) {
		   if (response.status == 'true') {
			   $("#plan_" + counter + "_id_talent_profile_note_plan").val(response.id_talent_profile_note);
				swal({
					icon: 'success',
					title: 'Success',
					text: response.message
				});
				
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: response.message
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function(response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					
				});
			} else {
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
	
	$('#committeeForm').submit(function (e) {
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$("#committeeForm input").removeClass("is-invalid");
		$("#committeeForm select").removeClass("is-invalid");
		$("#committeeForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text(""); 
		$("#risk_error").html("");
		$("#plan_error").html("");
		e.preventDefault();
		let formData = $(this).serializeArray();
		
		$.ajax({
			type: 'POST',
			headers: {
				Accept: "application/json",
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: "<?= url('talent_management/talent_development/talent_profile/submit') . '?id_employee=' ?>" + global_employee,
			data: formData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(response) {
			   if (response.status == 'true') {
					swal({
						icon: 'success',
						title: 'Submit Successfully',
						text: response.message
					});
												
				} else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: response.message
					});
				}
			},
			complete: function(){
				get_profile(global_employee,global_nik).then(function(value) {					
					$("#link_tab_rec-demo").removeClass("active");
					$("#rec-demo").removeClass("show").removeClass("active");
					$("#link_tab_rec-committe").addClass("active");
					$("#rec-committe").addClass("show").addClass("active");
				});
				$('#loader').addClass('hidden');
			},
			error: function(response) {
				if (response.status === 422) {
					let errors = response.responseJSON.errors;
					Object.keys(errors).forEach(function(key) {
						var key_temp = key.replaceAll(".", "_");
						$("#" + key_temp).addClass("is-invalid");
						$("#" + key_temp + "Error").children("strong").text(errors[key][0]);					
						var tab_id = $("#" + key_temp + "Error").parentsUntil('div').parent().parent().find(".detail_error").attr("id");
						if(tab_id == 'risk_error'){
							$("#risk_error").html("<i class='fas fa-exclamation-circle'></i> Required");
						}
						if(tab_id == 'plan_error'){
							$("#plan_error").html("<i class='fas fa-exclamation-circle'></i> Required");
						}
					});
				} else {
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

const get_projected = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_projected') ?>',
            dataType: 'json',
            success: function (res) {
				global_position = res;
            },
        });
        return result;
    } catch (error) {
        get_projected();
    }	
}

const get_risk = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_risk') ?>',
            dataType: 'json',
            success: function (res) {
				global_category = res;
            },
        });
        return result;
    } catch (error) {
        get_risk();
    }	
}

const get_plan = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_plan') ?>',
            dataType: 'json',
            success: function (res) {
				global_category_plan = res;
            },
        });
        return result;
    } catch (error) {
        get_plan();
    }	
}

const get_activity = async (idData,counter,code,getVal=null) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_activity') ?>',
			data: {id: idData},
            dataType: 'json',
            success: function (response) {
				if (response.length > 0) {
					if(code == 'Flight_Risk'){
						$("#risk_" + counter + "_id_activity").empty();
						$("#risk_" + counter + "_id_activity").select2({
							data: response
						});	
						if(getVal){
							$("#risk_" + counter + "_id_activity").val(getVal).trigger('change');
						}
					}
					else if(code == 'Talent_Note'){
						$("#plan_" + counter + "_id_activity_plan").empty();
						$("#plan_" + counter + "_id_activity_plan").select2({
							data: response
						});	
						if(getVal){
							$("#plan_" + counter + "_id_activity_plan").val(getVal).trigger('change');
						}
					}
				}	
            },
        });
        return result;
    } catch (error) {
        get_activity(idData,counter,code);
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

function get_pdf(id_employee,nik_employee,id_number) {
	let res = {
        id_employee: id_employee,
        nik_employee: nik_employee,
        id_number: id_number,
        pdf: true,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('talent_management/talent_development/talent_profile/download') }}";
    window.open(url+'?'+param, '_blank');
}
</script>
@endsection