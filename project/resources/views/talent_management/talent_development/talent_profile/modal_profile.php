<div class="row">
	<div class="col-12">
		<div class="card card-success card-outline">
			<div class="card-body">
				<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">General Profile</div>
				<div class="row">
					<div align="center" class="col-md-2"> 
						<div class="widget-user-image" style="margin:0 0 10px -20px;position: relative;top: 20px;">
							<img id="attach_photo" class="img-circle elevation-2" style="height:160px;width:150px;border-radius:20px;">
							<i id="no_image" style="font-size:160px;display:none;" class="fa fa-user"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<label class="col-sm-4 col-form-label">Name / NIK</label>
							<div class="col-sm-8">
								<input id="emp_name" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Department</label>
							<div class="col-sm-8">
								<input id="dept" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Position</label>
							<div class="col-sm-8">
								<input id="pos" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Employment Status</label>
							<div class="col-sm-8">
								<input id="emp_status" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Join Date</label>
							<div class="col-sm-8">
								<input id="join_date_profile" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Length of Service</label>
							<div class="col-sm-8">
								<input id="work_dur" class="form-control form-control-sm" readonly>	
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<label class="col-sm-4 col-form-label">Region</label>
							<div class="col-sm-8">
								<input id="region" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Branch</label>
							<div class="col-sm-8">
								<input id="branch" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Grade</label>
							<div class="col-sm-8">
								<input id="grade" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Division</label>
							<div class="col-sm-8">
								<input id="division" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Direct Supervisor</label>
							<div class="col-sm-8">
								<input id="direct_spv" class="form-control form-control-sm" readonly>	
							</div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Immediate Manager</label>
							<div class="col-sm-8">
								<input id="immediate_mgr" class="form-control form-control-sm" readonly>	
							</div>
						</div>
					</div>				
				</div>
			</div>
		</div>
		
		<div class="card card-success card-outline">
			<div class="card-body">
				<div class="row">
					 <div class="col-md-12">
						<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist" style="font-size:16px;font-weight:bold;">
							<li class="nav-item">
								<a class="nav-link active" id="link_tab_rec-demo" data-toggle="pill" href="#rec-demo" role="tab" aria-controls="link_tab_rec-demo" aria-selected="true">Demographics <span class="error-tab text-red"></span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="link_tab_rec-work" data-toggle="pill" href="#rec-work" role="tab" aria-controls="link_tab_rec-work" aria-selected="true">Career History <span class="error-tab text-red"></span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="link_tab_rec-learn" data-toggle="pill" href="#rec-learn" role="tab" aria-controls="link_tab_rec-learn" aria-selected="true">Learning History <span class="error-tab text-red"></span></a>
							</li>							
							<li class="nav-item">
								<a class="nav-link" id="link_tab_rec-career" data-toggle="pill" href="#rec-career" role="tab" aria-controls="link_tab_rec-career" aria-selected="true">Career Aspiration <span class="error-tab text-red"></span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="link_tab_rec-talent" data-toggle="pill" href="#rec-talent" role="tab" aria-controls="link_tab_rec-talent" aria-selected="true">Talent Insight <span class="error-tab text-red"></span></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="link_tab_rec-committe" data-toggle="pill" href="#rec-committe" role="tab" aria-controls="link_tab_rec-committe" aria-selected="true">Committee Note <span class="error-tab text-red"></span></a>
							</li>							
						</ul>
						<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
							<div class="tab-pane fade show active" id="rec-demo" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
								<div class="row">				
									<div class="col-md-12">
										<div class="row">
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Date of Birth</span>
													<div class="inner justify-content-center text-center">
														<b id="date_birth" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>	
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Age</span>
													<div class="inner justify-content-center text-center">
														<b id="age_profile" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Place of Birth</span>
													<div class="inner justify-content-center text-center">
														<b id="place_birth" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Marital Status</span>
													<div class="inner justify-content-center text-center">
														<b id="marital_status" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978 ;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">PTKP Status</span>
													<div class="inner justify-content-center text-center">
														<b id="ptkp_status" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>	
											<div class="col-lg-2 col-12">
												<div class="small-box card_employee_info" style="color:white !important;background:#797978 ;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Last Education</span>
													<div class="inner justify-content-center text-center">
														<b id="edu_profile" style="font-size:18px;">-</b>
													</div>
												</div>
											</div>					
										</div>
									</div>
								</div>		
								<hr>							
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Education</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_edu" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th style="white-space:nowrap;">Major</th>
													<th style="white-space:nowrap;">University/School</th>
													<th style="white-space:nowrap;">Education Level</th>
													<th style="white-space:nowrap;">City</th>
													<th style="white-space:nowrap;">Start Year</th>
													<th style="white-space:nowrap;">End Year</th>
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>		
								<hr>							
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Achievement (Award)</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_award" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th style="white-space:nowrap;">Award Name</th>
													<th style="white-space:nowrap;">Award Number</th>
													<th style="white-space:nowrap;">Certificate Number</th>
													<th style="white-space:nowrap;">Reference Date</th>
													<th style="white-space:nowrap;">Effective Date</th>
													<th style="white-space:nowrap;">Expired Date</th>
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>			
								<hr>
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Disciplinary Sanction</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_sp" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th style="white-space:nowrap;">SP Number</th>
													<th style="white-space:nowrap;">SP Name</th>
													<th style="white-space:nowrap;">Effective Date</th>
													<th style="white-space:nowrap;">Expired Date</th>
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>	
								
							</div>
							
							<div class="tab-pane fade" id="rec-learn" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                            <br/>
									<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Certification</div>
									<div class="inner justify-content-center text-center">
										<div class="col-md-12">
											<table style="width:100%;" id="table_cert_profile" class="table table-striped table-bordered table-hover datatable">
												<thead>
													<tr>
														<th style="white-space:nowrap;">No.</th>
														<th style="white-space:nowrap;">Certification Name</th>
														<th style="white-space:nowrap;">Certified By</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>
									<hr>
									<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Courses</div>
									<div class="inner justify-content-center text-center">
										<div class="col-md-12">
											<table style="width:100%;" id="table_training_profile" class="table table-striped table-bordered table-hover datatable">
												<thead>
													<tr>
														<th style="white-space:nowrap;">No.</th>
														<th style="white-space:nowrap;">Program</th>
														<th style="white-space:nowrap;">Courses</th>
														<th style="white-space:nowrap;">Score</th>														
														<th style="white-space:nowrap;">Status</th>
														<th style="white-space:nowrap;">Hit/Miss</th>
														<th style="white-space:nowrap;">Rating</th>
														<th style="white-space:nowrap;">Training Date</th>
													</tr>
												</thead>
											</table>
										</div>
									</div>									
							</div>
							
							<div class="tab-pane fade" id="rec-work" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                            <br/>									
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Career</div>
								<div class="row">
									<div class="col-lg-12 col-12">
										<table style="width:100%;" id="table_career_profile" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th>Career Number</th>
													<th>Career Transition</th>
													<th>Transaction Type</th>
													<th>Employee Status</th>
													<th>Position</th>
													<th>Grade</th>
													<th>Effective Date</th>
													<th>Expired Date</th>
													<th>Duration</th>
													<th>Company</th>
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>
								<hr>
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Working Experience</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_working" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th style="white-space:nowrap;">Company Name</th>
													<th style="white-space:nowrap;">Position</th>
													<th style="white-space:nowrap;">Period</th>
													<th style="white-space:nowrap;">City</th>
												</tr>
											</thead>
										</table>
									</div>	
								</div>	
							</div>
							<div class="tab-pane fade" id="rec-career" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                            <br/>
								<div class="inner">
									<div class="col-md-12">
										<table style="width:100%;" id="table_aspiration" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr align="center">
													<th style="width:150px;">Period</th>
													<th style="width:250px;">Question</th>
													<th>Answer</th>
												</tr>
											</thead>
										</table>
									</div>
								</div>					
							</div>
							<div class="tab-pane fade" id="rec-talent" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                            <br/>	
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Talent History (Talent)</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_history_talent" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th data-priority="1" style="white-space:nowrap;">No.</th>
													<th data-priority="7">Projected Position</th>
													<th data-priority="2">Matrix Box</th>
													<th data-priority="3">Matrix Name</th>
													<th data-priority="4">Period</th>
													<th data-priority="5">KPI Average</th>
													<th data-priority="6">Rating</th>
													<th>Potencies</th>
													<th>Competencies</th>
													<th>Eligibility Status</th>											
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>				
								<hr>
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Talent History (Successor)</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_history_successor" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th data-priority="1" style="white-space:nowrap;">No.</th>
													<th data-priority="7">Projected Position</th>
													<th data-priority="2">Matrix Box</th>
													<th data-priority="3">Matrix Name</th>
													<th data-priority="4">Period</th>
													<th data-priority="5">KPI Average</th>
													<th data-priority="6">Rating</th>
													<th>Potencies</th>
													<th>Competencies</th>
													<th>Eligibility Status</th>											
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div>				
								<hr>
								<div class="row">
									<div class="col-md-6">	
										<div class="card-body">
											<div id="box_" class="small-box card_employee_info" style="color:white !important;">
												<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Current HAV Matrix</span>
												<div class="justify-content-center text-center" style="padding:10px;">
													<b id="talent_box" style="font-size:20px;">-</b>
													<b id="talent_desc" style="font-size:16px;"></b>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="card-body">
											<div class="small-box card_employee_info grey" style="color:white !important;">
												<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Readiness</span>
												<div class="justify-content-center text-center" style="padding:10px;">
													<b align="center" id="talent_ready" style="font-size:20px;">-</b>
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr>
								<!-- div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Projected Position</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;" id="table_pro_pos" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th style="white-space:nowrap;">No.</th>
													<th style="white-space:nowrap;">Projected Position</th>
													<th style="white-space:nowrap;">Period</th>
												</tr>
											</thead>                                              
										</table>								
									</div>
								</div -->
								<div style="font-size:18px;font-weight:bold;margin-bottom:10px;">Performance</div>
								<div class="row">				
									<div class="col-md-12">
										<div class="row">
											<div class="col-lg-4 col-12">
												<div class="small-box card_employee_info green" style="color:white !important;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Annual Rating (<span id="last_year">-</span>)</span>
													<div class="inner justify-content-center text-center">
														<b align="center" id="last_rating" style="font-size:25px;">-</b>
													</div>
												</div>
											</div>
											<div class="col-lg-4 col-12">
												<div class="small-box card_employee_info grey" style="color:white !important;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Annual Rating (<span id="last_year_2">-</span>)</span>
													<div class="inner justify-content-center text-center">
														<b align="center" id="last_rating_2" style="font-size:25px;">-</b>
													</div>
												</div>
											</div>
											<div class="col-lg-4 col-12">
												<div class="small-box card_employee_info grey" style="color:white !important;">
													<span href="#" class="small-box-footer text-bold" style="font-size:18px;">Annual Rating (<span id="last_year_3">-</span>)</span>
													<div class="inner justify-content-center text-center">
														<b align="center" id="last_rating_3" style="font-size:25px;">-</b>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12 col-12 inner justify-content-center text-center">
										<table style="width:100%;font-size:14px;" id="table_kpi_list" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr>
													<th data-priority="1" rowspan="2" style="white-space:nowrap;vertical-align:middle;">No.</th>
													<th data-priority="2" rowspan="2" style="vertical-align:middle;">Period</th>
													<th data-priority="3" rowspan="2" style="vertical-align:middle;">KPI Average (%)</th>
													<th colspan="12" align="center" style="vertical-align:middle;">Months (%)</th>
												</tr>
												<tr>
													<th data-priority="4">Jan</th>
													<th data-priority="5">Feb</th>
													<th data-priority="6">Mar</th>
													<th data-priority="7">Apr</th>
													<th data-priority="8">Mei</th>
													<th data-priority="9">Jun</th>
													<th data-priority="10">Jul</th>
													<th data-priority="11">Ags</th>
													<th data-priority="12">Sep</th>
													<th data-priority="13">Okt</th>
													<th data-priority="14">Nov</th>
													<th data-priority="15">Des</th>
												
											</thead>                                              
										</table>								
									</div>
								</div>							
							</div>
							
							<div class="tab-pane fade" id="rec-committe" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                            <br/>
								<div class="row">
									<div class="col-md-12" style="margin-bottom: 10px">
										<div class="pull-left" style="font-size:16px;font-weight:bold;">Flight Risk <b id="risk_error" class="detail_error text-red"></b></div>
										<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_risk"><span class="fas fa-plus"></span> Add Data Flight Risk</button>
									</div>
									<div class="col-md-12">
										<table id="table_rec_risk" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr align="center">
													<th style="white-space:nowrap;">No.</th>
													<th>Projected Position</th>
													<th style="white-space:nowrap;">Category</th>
													<th style="width:200px;">Committee Date</th>
													<th style="white-space:nowrap;">Activity</th>
													<th style="width:500px;">Committee Note</th>
													<th style="white-space:nowrap;">Action</th>
												</tr>
											</thead>
											<tbody id="table_rec_risk_body">
											</tbody>
										</table>
										<div class="col-sm-12">
											<span class="table-invalid-feedback text-red" role="alert" id="table_rec_riskError">
												<strong></strong>
											</span>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-12" style="margin-bottom: 10px">
										<div class="pull-left" style="font-size:16px;font-weight:bold;">Development Plan <b id="plan_error" class="detail_error text-red"></b></div>
										<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_plan"><span class="fas fa-plus"></span> Add Data Development Plan</button>
									</div>
									<div class="col-md-12">
										<table id="table_rec_plan" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
											<thead>
												<tr align="center">
													<th style="white-space:nowrap;">No.</th>
													<th>Projected Position</th>
													<th style="white-space:nowrap;">Category</th>
													<th style="width:200px;">Committee Date</th>
													<th style="white-space:nowrap;">Activity</th>
													<th style="width:500px;">Committee Note</th>
													<th style="white-space:nowrap;">Action</th>
												</tr>
											</thead>
											<tbody id="table_rec_plan_body">
											</tbody>
										</table>
										<div class="col-sm-12">
											<span class="table-invalid-feedback text-red" role="alert" id="table_rec_planError">
												<strong></strong>
											</span>
										</div>
									</div>
								</div>	
								<div class="modal-footer">
									<button type="submit" class="submit_button btn btn-sm btn-info" id="submit_button"><i class="fas fa-paper-plane"></i> Submit</button>
								</div>
							</div>						
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>

</div>     

<style type="text/css">
.green {
	background-color : #54b556;
}
.yellow {
	background-color : #c4c106;
}
.blue {
	background-color : #007ca2;
}
.grey {
	background-color : #797978;;
}
.red {
	background-color : #ef5454;
}

td.text-center{
	text-align:center;
}
td.data_justify{
	text-align:justify;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	get_risk();
	get_plan();
	get_projected();
	
	profile_id_employee = <?php echo $global_emp; ?>;
	profile_nik_employee = <?php echo "'".$global_nik."'"; ?>;
	get_cert_profile(profile_id_employee);
	get_training(profile_id_employee,profile_nik_employee);
	get_pro_position(profile_id_employee,profile_nik_employee);
	get_history_talent(profile_id_employee,profile_nik_employee,'P');
	get_history_successor(profile_id_employee,profile_nik_employee,'S');
	get_working(profile_id_employee,profile_nik_employee);
	get_last_rating(profile_id_employee,profile_nik_employee);
	get_old_rating(profile_id_employee,profile_nik_employee);
	get_kpi_list(profile_id_employee,profile_nik_employee);
	get_edu(profile_id_employee,profile_nik_employee);
	get_award(profile_id_employee,profile_nik_employee);
	get_sp(profile_id_employee,profile_nik_employee);
	get_aspiration(profile_id_employee,profile_nik_employee);
	
//	get_projected().then(function(value) {
       	get_editProfile(profile_id_employee,profile_nik_employee);	
//    });

});

function get_editProfile(id_employee,nik_employee) {
		$.ajax({
			url: "<?= url('talent_management/talent_development/talent_profile/get_editProfile') ?>",
            method: "GET",
            data: {id_employee: id_employee},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				get_emp_career_history(response.profile.identification_number);
				$('#id_employee').val(id_employee).trigger('change');
				$('#emp_name').val(response.profile.name).trigger('change');
				$('#dept').val(response.profile.department).trigger('change');
				$('#pos').val(response.profile.position_routing).trigger('change');
				$('#emp_status').val(response.profile.employment_status).trigger('change');
				$('#join_date_profile').val(moment(response.profile.join_date).format('DD MMM YYYY')).trigger('change');
				$('#region').val(response.profile.regional).trigger('change');
				$('#branch').val(response.profile.branch).trigger('change');
				$('#grade').val(response.profile.job_grade).trigger('change');
				$('#division').val(response.profile.principal).trigger('change');
				$('#work_dur').val(response.profile.duration).trigger('change');
				$('#direct_spv').val(response.profile.parent_emp_name).trigger('change');
				$('#immediate_mgr').val(response.profile.indirect_emp_name).trigger('change');
				$('#date_birth').html(moment(response.profile.birthdate).format('DD MMM YYYY'));
				$('#age_profile').html(response.profile.age);
				$('#place_birth').html(response.profile.place_of_birth);
				$('#marital_status').html(response.profile.marital);
				$('#ptkp_status').html(response.profile.ptkp_status);
				if(response.profile.edu_level != null){
					$('#edu_profile').html(response.profile.edu_level);
				}
				else{
					$('#edu_profile').html("-");
				}
				
				if(response.profile.box != null){
					if(response.profile.box == 'BOX 1'){
						$('#box_').addClass('green');
					}
					else if(response.profile.box == 'BOX 2' || response.profile.box == 'BOX 3' || response.profile.box == 'BOX 5'){
						$('#box_').addClass('yellow');
					}				
					else if(response.profile.box == 'BOX 4' || response.profile.box == 'BOX 6' || response.profile.box == 'BOX 7'){
						$('#box_').addClass('blue');
					}
					else if(response.profile.box == 'BOX 8' || response.profile.box == 'BOX 9'){
						$('#box_').addClass('red');
					}
					else{
						$('#box_').addClass('grey');
					}
					$('#talent_box').html(response.profile.box);
					$('#talent_desc').html("("+response.profile.desc_box+")");
				}
				else{
					$('#box_').addClass('grey');
					$('#talent_box').html("-");
					$('#talent_desc').html("");
				}
				if(response.profile.readyness != null){
					$('#talent_ready').html(response.profile.readyness);
				}
				else{
					$('#talent_ready').html("-");
				}
				if(response.profile.image_attachment != null){
					$('#no_image').css('display','none');
					if(response.profile.image_attachment.length > 1000){
						document.getElementById("attach_photo").src = "data:image;base64,"+response.profile.image_attachment;	
					}
					else{
					//	console.log(response.profile.filePath+"/"+response.profile.image_attachment);
						document.getElementById("attach_photo").src = response.profile.filePath+"/"+response.profile.image_attachment;
					}
				}
				else{
					$('#attach_photo').css('display','none');
					$('#no_image').css('display','inline');
				}
				
				setTimeout(function () {
					global_id_rec_risk = 0;
					global_id_rec_plan = 0;
					$.each(response.comit_risk, function (i, item) {
						$('#new_rec_risk').trigger('click');				
					});
					$.each(response.comit_plan, function (i, item) {
						$('#new_rec_plan').trigger('click');
					});
					$('#table_rec_risk_body tr').each(function (index) {
					//	$(this).find('.delete-record-risk').hide();
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_category_input').val(response.comit_risk[index].id_category_profile).trigger('change',[true]);
						$(this).find('.id_talent_profile_note_input').val(response.comit_risk[index].id_talent_profile_note);
						$(this).find('.id_routing_input').val(response.comit_risk[index].id_position_routing).trigger('change');
						$(this).find('.committee_date_input').val(response.comit_risk[index].talent_commite_date).trigger('change');
						get_activity(response.comit_risk[index].id_category_profile,index,response.comit_risk[index].code,response.comit_risk[index].id_activity).then(function(value) {
						});
						$(this).find('.committee_note_input').val(response.comit_risk[index].notes).trigger('change');
						if(response.comit_risk[index].is_submitted_flag == 1){
							$(this).find('.id_category_input').attr('readonly',true);
							$(this).find('.id_talent_profile_note_input').attr('readonly',true);
							$(this).find('.id_routing_input').attr('readonly',true);
							$(this).find('.committee_date_input').attr('readonly',true).css('pointer-events','none');
							$(this).find('.id_activity_input').attr('readonly',true);							
							$(this).find('.committee_note_input').attr('readonly',true);
							$(this).find('.add-record-risk').hide();
							$(this).find('.delete-record-risk').hide();
						}
					});	  
					
					$('#table_rec_plan_body tr').each(function (index,item) {							
					//	$(this).find('.delete-record-plan').hide();
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_category_plan_input').val(response.comit_plan[index].id_category_profile).trigger('change',[true]);
						$(this).find('.id_talent_profile_note_plan_input').val(response.comit_plan[index].id_talent_profile_note);
						$(this).find('.id_routing_plan_input').val(response.comit_plan[index].id_position_routing).trigger('change');
						$(this).find('.committee_date_plan_input').val(response.comit_plan[index].talent_commite_date).trigger('change');
						get_activity(response.comit_plan[index].id_category_profile,index,response.comit_plan[index].code,response.comit_plan[index].id_activity).then(function(value) {
						});
						$(this).find('.committee_note_plan_input').val(response.comit_plan[index].notes).trigger('change');
						if(response.comit_plan[index].is_submitted_flag == 1){
							$(this).find('.id_category_plan_input').attr('readonly',true);
							$(this).find('.id_talent_profile_note_plan_input').attr('readonly',true);
							$(this).find('.id_routing_plan_input').attr('readonly',true);
							$(this).find('.committee_date_plan_input').attr('readonly',true).css('pointer-events','none');
							$(this).find('.id_activity_plan_input').attr('readonly',true);
							$(this).find('.committee_note_plan_input').attr('readonly',true);
							$(this).find('.add-record-plan').hide();
							$(this).find('.delete-record-plan').hide();
						}
					});	  
					
				}, 1000);
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
	}

function get_cert_profile(id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_cert_profile').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_cert_profile') . '?id_employee=' ?>" + id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_cert_profile').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'certification_name', name: 'certification_name'},
			{data: 'certified_by', name: 'certified_by'},		
		]
	});
}

function get_working(id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_working').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_working') . '?id_employee=' ?>" + id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_working').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'company_name', name: 'company_name'},
			{data: 'position_name', name: 'position_name'},
			{data: 'period', name: 'period', className: 'text-center'},
			{data: 'company_city', name: 'company_city'},
		]
	});
}

function get_training(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_training_profile').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_training') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_training_profile').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'program_name', name: 'program_name'},
			{data: 'course_name', name: 'course_name'},
			{data: 'user_score', name: 'user_score'},
			{data: 'status', name: 'status', className: 'text-center', render: function ( data, type, row ) {	
					if(row.status == 'Pass'){
						return '<span class="badge badge-success" style="padding:6px;font-size:13px;">'+data+'</span>';
					}
					else{
						return '<span class="badge badge-danger" style="padding:6px;color:white;font-size:13px;">'+data+'</span>';
					}
				} 
			},	
			{data: 'hit_miss', name: 'hit_miss'},
			{data: 'rating', name: 'rating'},
			{data: 'creation_date', name: 'creation_date'},
		]
	});
}

function get_emp_career_history(id_number) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_career_profile').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_emp_career_history') . '?id_number=' ?>" + id_number,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_career_profile').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'reference_number', name: 'reference_number'},
			{data: 'transition_category', name: 'transition_category'},
			{data: 'transaction_type', name: 'transaction_type'},
			{data: 'employment_status', name: 'employment_status'},
			{data: 'position_routing', name: 'position_routing'},
			{data: 'job_grade', name: 'job_grade'},
			{data: 'effective_date', name: 'effective_date'},
			{data: 'expired_date', name: 'expired_date'},
			{data: 'duration', name: 'duration',render: function ( data, type, row ) {
				if(data == '00:00:00'){
					return "-";
				}	
				else{
					return data;
				}
			  }
			},
			{data: 'company_name', name: 'company_name'},
		]
	});
}

function get_pro_position(id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_pro_pos').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_pro_position') . '?id_employee=' ?>" + id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_pro_pos').DataTable().ajax.reload();
				}
		},	
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'pro_position', name: 'pro_position'},
			{data: 'period', name: 'period'},
		]
	});
}

function get_history_talent(id_employee,nik_employee,type) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_history_talent').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_history_talent') . '?id_employee=' ?>" + id_employee,
			data:{
				nik_employee : nik_employee,
				type : type
			},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_history_talent').DataTable().ajax.reload();
				}
		},	
		columns: [
		//	{defaultContent: '',orderable: false},
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'pro_position', name: 'pro_position'},
			{data: 'matrix_box', name: 'matrix_box'},
			{data: 'matrix_name', name: 'matrix_name'},
			{data: 'period', name: 'period'},
			{data: 'kpi_average', name: 'kpi_average'},
			{data: 'rating', name: 'rating'},
			{data: 'potencies', name: 'potencies'},
			{data: 'competencies', name: 'competencies'},
			{data: 'eligible_status', name: 'eligible_status'},
		]
	});
}

function get_history_successor(id_employee,nik_employee,type) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_history_successor').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_history_talent') . '?id_employee=' ?>" + id_employee,
			data:{
				nik_employee : nik_employee,
				type : type
			},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_history_successor').DataTable().ajax.reload();
				}
		},	
		columns: [
		//	{defaultContent: '',orderable: false},
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'pro_position', name: 'pro_position'},
			{data: 'matrix_box', name: 'matrix_box'},
			{data: 'matrix_name', name: 'matrix_name'},
			{data: 'period', name: 'period'},
			{data: 'kpi_average', name: 'kpi_average'},
			{data: 'rating', name: 'rating'},
			{data: 'potencies', name: 'potencies'},
			{data: 'competencies', name: 'competencies'},
			{data: 'eligible_status', name: 'eligible_status'},
		]
	});
}

function get_last_rating(id_employee,nik_employee) {
	$.ajax({
		url: "<?= url('talent_management/talent_development/talent_profile/get_last_rating') ?>",
		method: "GET",
		data: {
			id_employee: id_employee,
			nik_employee : nik_employee
		},
		success: function (response) {
			if(response.length > 0){
				$('#last_year').html(response[0].year);
				$('#last_rating').html(response[0].final_rating);
			}
		},
	});
}

function get_old_rating(id_employee,nik_employee) {
	$.ajax({
		url: "<?= url('talent_management/talent_development/talent_profile/get_old_rating') ?>",
		method: "GET",
		data: {
			id_employee: id_employee,
			nik_employee : nik_employee
		},
		success: function (response) {
			if(response.length > 0){
				if(response.length == 1){
					$('#last_year_2').html(response[0].year);
					$('#last_rating_2').html(response[0].final_rating);
				}
				else if(response.length == 2){
					$('#last_year_2').html(response[0].year);
					$('#last_rating_2').html(response[0].final_rating);
					$('#last_year_3').html(response[1].year);
					$('#last_rating_3').html(response[1].final_rating);
				}
			}
		},
	});
}

function get_kpi_list(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_kpi_list').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_kpi_list') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_kpi_list').DataTable().ajax.reload();
				}
		},	
		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'period', name: 'period'},
			{data: 'average_prosentase', name: 'average_prosentase'},
			{data: 'jan', name: 'jan'},
			{data: 'feb', name: 'feb'},
			{data: 'mar', name: 'mar'},
			{data: 'apr', name: 'apr'},
			{data: 'mei', name: 'mei'},
			{data: 'jun', name: 'jun'},
			{data: 'jul', name: 'jul'},
			{data: 'ags', name: 'ags'},
			{data: 'sep', name: 'sep'},
			{data: 'okt', name: 'okt'},
			{data: 'nov', name: 'nov'},
			{data: 'des', name: 'des'},
		]
	});
}

function get_edu(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_edu').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_edu') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_edu').DataTable().ajax.reload();
				}
		},	
		columns: [
		//	{defaultContent: '',orderable: false},
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'major', name: 'major' },
			{ data: 'education_name', name: 'education_name' },
			{ data: 'level', name: 'level' },
			{ data: 'education_city', name: 'education_city' },
			{ data: 'start_year', name: 'start_year' },
			{ data: 'end_year', name: 'end_year' },
		]
	});
}

function get_award(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_award').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_award') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_award').DataTable().ajax.reload();
				}
		},	
		columns: [
		//	{defaultContent: '',orderable: false},
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'remark', name: 'remark' },
			{ data: 'award_letter_number', name: 'award_letter_number' },
			{ data: 'award_certificate_number', name: 'award_certificate_number' },
			{ data: 'reference_date', name: 'reference_date' },
			{ data: 'effective_date', name: 'effective_date' },
			{ data: 'expired_date', name: 'expired_date' },
		]
	});
}

function get_sp(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_sp').DataTable({	
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_sp') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_sp').DataTable().ajax.reload();
				}
		},	
		columns: [
		//	{defaultContent: '',orderable: false},
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'sp_number', name: 'sp_number' },
			{ data: 'sp_name', name: 'sp_name' },
			{ data: 'effective_date', name: 'effective_date' },
			{ data: 'expired_date', name: 'expired_date' },
		]
	});
}

function get_aspiration(id_employee,nik_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_aspiration').DataTable({	
		rowsGroup: [0],
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_aspiration') . '?id_employee=' ?>" + id_employee,
			data:{nik_employee : nik_employee},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_aspiration').DataTable().ajax.reload();
				}
		},	
		columns: [
			{ data: 'period', name: 'period', className: 'text-middle'},
			{ data: 'question', name: 'question', className: 'data_justify'},
			{ data: 'description_answer', name: 'description_answer', className: 'data_justify'},
			
		]
	});
}

</script>