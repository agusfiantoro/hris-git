<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Name</label>
						<div class="col-sm-8">
							<input id="id_recommendation_header" name="id_recommendation_header" type="hidden">
							<select name="id_employee" id="id_employee" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							 <span class="invalid-feedback" role="alert" id="id_employeeError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<input id="id_position_detail" name="id_position_detail" type="hidden">
							<input autocomplete="off" id="position" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Grade</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="grade" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="dept" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Region</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="region" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Branch</label>
						<div class="col-sm-8">
							<input id="id_location" name="id_location" type="hidden">
							<input autocomplete="off" id="branch" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Location</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="location" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Principal</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="principal" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Employment Status</label>
						<div class="col-sm-8">
							<input id="id_employment_status" name="id_employment_status" type="hidden">
							<input autocomplete="off" id="emp_status" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">Career Period</label>
						<div class="col-sm-3" style="flex: 0 0 29%;max-width: 29%;">
							<input autocomplete="off" id="start_date" name="start_date" class="form-control form-control-sm" style="width: 100%;">
						</div>
						<label class="col-sm-1 col-form-label" style="text-align:center;">-</label>
						<div class="col-sm-3" style="flex: 0 0 29%;max-width: 29%;">
							<input autocomplete="off" id="end_date" name="end_date" class="form-control form-control-sm" style="width: 100%;">
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">Evaluation Period</label>
						<div class="col-sm-3">
							<select name="month_period" id="month_period" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="month_periodError">
								<strong></strong>
							</span>
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">Hierarchy Approval</label>
						<div class="col-sm-8">
							<div class="is-loading">
								<select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
								<span class="invalid-feedback" role="alert" id="id_approvalError">
									<strong></strong>
								</span>
								<span id="load_id_approval" class="spinner-border spinner-border-sm" style="display:none;"></span>
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
				</div>
			</div>
			<hr/>
			<!-- div class="row" style="margin-bottom:10px;">
				<div class="col-md-12" style="margin-top:10px;margin-bottom:5px;">Mengajukan Penempatan Karyawan Tersebut Dengan :</div>
			</div -->			
			<div class="row">
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Category</label>
						<div class="col-sm-8">
							<select name="id_transition_category" id="id_transition_category" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							 <span class="invalid-feedback" role="alert" id="id_transition_categoryError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Type</label>
						<div class="col-sm-7">
							<div class="is-loading">
								<select name="id_transition_type" id="id_transition_type" class="form-control form-control-sm select2" style="width: 100%;">
								</select>
								 <span class="invalid-feedback" role="alert" id="id_transition_typeError">
									<strong></strong>
								</span>
								<span id="load_id_type" class="spinner-border spinner-border-sm" style="display:none;"></span>
							</div>
						</div>
						<div class="col-sm-1">
							<a style="vertical-align:middle;padding-top:10px;" href="javascript:void(0)" tabindex="0" class="help_input" role="button" data-toggle="popover" data-trigger="focus" title="Notes" data-content="-">
								<small><i class="fas fa-question-circle fa-lg" style="font-size:18px;"></i></small>
							</a>
						</div>
					</div>
					<div class="row" id="eff_date">
						<label class="col-sm-4 col-form-label">Effective Date</label>
						<div class="col-sm-8">
							<input name="effective_date" id="effective_date" class="form-control form-control-sm">
							<span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row" id="ex_date">
						<label class="col-sm-4 col-form-label">Expired Date</label>
						<div class="col-sm-8">
							<input name="expired_date" id="expired_date" class="form-control form-control-sm" disabled>
							<span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="expired_dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row" id="dur" style="display:none;">
						<label class="col-sm-4 col-form-label">Duration</label>
						<div class="col-sm-3">
							<input autocomplete="off" name="duration" id="duration" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
						<label class="col-sm-2 col-form-label">Month(s)</label>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">With Reco Details</label>
						<div class="col-sm-2">
						  <input type="checkbox" name="with_reco" id="with_reco" class="text mt-2" style="width: 20px;height: 20px;">
						  <span class="invalid-feedback" role="alert" id="Error">
							<strong></strong>
						  </span>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<!-- div class="row">
						<label class="col-sm-4 col-form-label">New Position</label>
						<div class="col-sm-8">
							<select name="id_position" id="id_position" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							 <span class="invalid-feedback" role="alert" id="id_positionError">
								<strong></strong>
							</span>
						</div>
					</div -->
					<div class="row">
						<label class="col-sm-4 col-form-label">New Position</label>
						<div class="col-sm-7">
							<div class="input-group">
								<input name="id_new_position_detail" id="id_new_position_detail" type="hidden">
								<input type="text" name="new_position_routing" id="new_position_routing" placeholder="Select Position ..." class="get_new_position form-control form-control-sm"style="border-radius: 5px 0 0 5px;">
								<div class="input-group-append">
									<a href="#" class="clear_pos input-group-text">
										<i class="fa fa-times" style="font-size:12px;"></i>
									</a>
									<span class="input-group-text far fa-list-alt form-control-sm"></span>
								</div>
							</div>
							<span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="new_position_routingError">
								<strong></strong>
							</span>
						</div>
						<div class="col-sm-1">
							<a style="vertical-align:middle;padding-top:10px;" href="javascript:void(0)" tabindex="0" class="help_pos_input" role="button" data-toggle="popover" data-trigger="focus" title="Notes" data-content="-">
								<small><i class="fas fa-question-circle fa-lg" style="font-size:18px;"></i></small>
							</a>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">New Department</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="new_dept" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">New Grade</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="new_grade" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">New Branch</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="new_branch" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">New Location</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="new_location" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">New Principal</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="new_principal" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Recipient Manager</label>
						<div class="col-sm-8">
							<select name="id_new_mgr" id="id_new_mgr" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row" id="id_reco_check" style="display:none;">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-quanti" data-toggle="pill" href="#rec-quanti" role="tab" aria-controls="link_tab_rec-quanti" aria-selected="true">Quantitative Review <span class="error-tab text-red"></span></a>
						</li>
						<li class="nav-item" id="tab_reco">
							<a class="nav-link" id="link_tab_rec-quali" data-toggle="pill" href="#rec-quali" role="tab" aria-controls="link_tab_rec-quali" aria-selected="true">Qualitative Review <span class="error-tab text-red"></span></a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-summary" data-toggle="pill" href="#rec-summary" role="tab" aria-controls="link_tab_menu-summary" aria-selected="true">Summary Review <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-quanti" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_quanti" style="margin-left:10px;"><span class="fas fa-plus"></span> Add Item KPI</button>
									<button type="button" class="pull-right btn btn-xs btn-success" id="new_gen_quanti" style="color:white;font-weight:bold;display:none;"><span class="fa fa-refresh"></span> Generate KPI Detail (Tipe Lurus)</button>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_quanti" style="width:2000px" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:14px;">
												<th rowspan="2" style="width:10px;">No.</th>
												<th rowspan="2" style="width:180px;">Measurement</th>
												<th colspan="4" id="m_one">1 Month Ago</th>
												<th colspan="4" id="m_two">2 Month Ago</th>
												<th colspan="4" id="m_three">3 Month Ago</th>
												<th colspan="4" id="m_four">4 Month Ago</th>
												<th colspan="4" id="m_five">5 Month Ago</th>
												<th colspan="4" id="m_six">6 Month Ago</th>
												<th rowspan="2" style="width:80px;">Action</th>
											</tr>
											<tr align="center">										
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
												<th style="width:80px;">Bobot</th>
												<th style="width:70px;">Obj.</th>
												<th style="width:70px;">Ach.</th>
												<th style="width:80px;">Point</th>
											</tr>
										</thead>
										<tbody id="table_rec_quanti_body" style="display:none;"></tbody>
										<tfoot id="table_rec_quanti_foot">
											<tr align="center" style="font-size:15px;">
												<th colspan="2">Total Point</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_one" name="bobot_one" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_oneError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_one" name="tot_one" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>											
													</div>
												</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_two" name="bobot_two" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_twoError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_two" name="tot_two" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
														
													</div>
												</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_three" name="bobot_three" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_threeError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_three" name="tot_three" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													</div>
												</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_four" name="bobot_four" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_fourError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_four" name="tot_four" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													</div>
												</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_five" name="bobot_five" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_fiveError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_five" name="tot_five" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													</div>
												</th>
												<th>
													<input autocomplete="off" type="text" id="bobot_six" name="bobot_six" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													<span class="invalid-feedback" role="alert" id="bobot_sixError">
														<strong></strong>
													</span>
												</th>
												<th class="sum_tot" colspan="3">
													<div class="col-sm-6">
														<input autocomplete="off" type="text" id="tot_six" name="tot_six" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:15px;" readonly>
													</div>
												</th>
												<th>-</th>
											</tr>
										</tfoot>
									</table>
									<div class="col-sm-12">
										<span class="table-invalid-feedback text-red" role="alert" id="table_rec_quantiError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="col-md-12" style="margin-top:10px">
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<label class="col-sm-4 col-form-label" style="font-size:15px;">Average Score KPI (%)</label>
												<div class="col-sm-2">
													<input autocomplete="off" id="total_kpi" name="total_kpi" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:16px;" readonly>
												</div>
												<label class="col-sm-4" style="padding-top:3px;text-align:center;">
													<span id="status_kpi" class="status_kpi" style="padding:5px 20px 5px 20px;font-size:16px;display:none;">-</span>
												</label>
											</div>
										</div>										
									</div>
									<i><u>Syarat Kelulusan secara Quantitative:</u> Minimal Average Score KPI = 85%</i>
								</div>
							</div>
						</div>
						<div class="tab-pane fade show" id="rec-quali" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_quali"><span class="fas fa-plus"></span> Add 360 Feedback</button>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_quali" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:14px;">
												<th style="width:30px;">No.</th>
												<th style="width:250px;">Appraisers</th>
												<th style="width:100px;">Type</th>
												<th>Score</th>
												<th>Submitted</th>
												<th style="width:100px;">Action</th>
											</tr>
										</thead>
										<tbody id="table_rec_quali_body">
										</tbody>
									</table>
									<div class="col-sm-12">
										<span class="table-invalid-feedback text-red" role="alert" id="table_rec_qualiError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="col-md-12" style="margin-top:10px">
									<div class="row">
										<div class="col-md-6">
											<div class="row">
												<label class="col-sm-5 col-form-label" style="font-size:15px;">Total Score (360 Feedback)</label>
												<div class="col-sm-2">
													<input autocomplete="off" id="total_360" name="total_360" class="form-control form-control-sm" style="width: 100%;text-align:center;font-weight:bold;font-size:16px;" readonly>
												</div>
											</div>
										</div>										
									</div>
								</div>
								<div class="col-md-12" id="sum_qualitative" style="display:none;">
									<table style="width:100%;font-size:14px;" id="table_sum_qualitative" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center">
												<th rowspan="2" style="width:10px;vertical-align: middle;padding: 0.5rem;">No.</th>
												<th rowspan="2" style="width:600px;vertical-align: middle;padding: 0.5rem;">Appraisal Area</th>
												<th colspan="5" style="padding: 0.5rem;">Number of Appraisers (People)</th>
												<th rowspan="2" style="width:50px;vertical-align: middle;border-left: 1px solid #dee2e6;padding: 0.5rem;">Average (Level)</th>
											</tr>
											<tr align="center">
												<th style="width:30px;padding: 0.5rem;">Level 5</th>
												<th style="width:30px;padding: 0.5rem;">Level 4</th>
												<th style="width:30px;padding: 0.5rem;">Level 3</th>
												<th style="width:30px;padding: 0.5rem;">Level 2</th>
												<th style="width:30px;padding: 0.5rem;">Level 1</th>
											</tr>
										</thead>
									</table>
									<i>Level 5 = Baik Sekali, Level 4 = Baik, Level 3 = Sedang, Level 2 = Kurang, Level 1 = Kurang Sekali</i>
								</div>
							</div>
						</div>
						<div class="tab-pane fade show" id="rec-summary" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row" id="tab_summary" style="font-size:14px;">
								<div class="col-md-12">
									<textarea id="notes_summary" name="notes_summary" placeholder="OVERALL REVIEW (berdasarkan hasil kerja yang dicapai)" class="form-control form-control-sm" rows="4"></textarea>
									<span class="invalid-feedback" role="alert" id="notes_summaryError">
										<strong></strong>
									</span>
								</div>
								<div class="col-md-12" style="margin-top:10px;margin-bottom:5px;">Berdasarkan Quantitative dan Qualitative Result maka karyawan tersebut dinyatakan : </div>
								<div class="col-md-6">
									<div class="row">
										<label class="col-sm-4 col-form-label">Decision</label>
										<div class="col-sm-8">
											<select name="decision" id="decision" class="form-control form-control-sm select2" style="width: 100%;">
											</select>
											<span class="invalid-feedback" role="alert" id="decisionError">
												<strong></strong>
											</span>
										</div>
									</div>									
								</div>
								<div class="col-md-12">
									<div class="row">
										<label class="col-sm-2 col-form-label">Effective / Expired Date</label>
										<div class="col-sm-2"> 
											<input id="des_eff" class="form-control form-control-sm" readonly>
										</div>
										<label class="col-form-label"> / </label>
										<div class="col-sm-2">
											<input id="des_exp" class="form-control form-control-sm" readonly>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">

$(document).ready(function(){
	
	id_recommendation_header = '{!! $id_recommendation_header !!}';
	type = '{!! $type !!}';
	sum = '{!! $sum !!}';
	
	//get_hierachy(type,id_recommendation_header,id_location=null);
	
	get_approval_status();
	get_category();
	get_new_status();
	get_month_period();
	get_decision();
//	get_appraiser();
	
	if(id_recommendation_header != 0){
		get_edit(id_recommendation_header,type);
		if(sum == 'summary'){
			list_approval(id_recommendation_header,'Form_Reco');
		}
	}
	else{
		get_employee(id_employee=null,type);
		get_reco_check(type,datenow=null);
	}
	
	$('#id_transition_type').prepend('<option selected></option>').select2({
		placeholder: "Select Type ...",
	});
	
	$('#id_new_mgr').prepend('<option selected></option>').select2({
		placeholder: "Select Recipient Manager ...",
	});
	
	$('#start_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}).on('change', function (e) {
		$('#end_date').datepicker('destroy');
		$('#end_date').addClass('form-control-sm');
		$('#end_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
			minDate: $('#start_date').val(),
		}).on('change', function (e) {
			var startDate = moment($('#start_date').val());
			var endDate = moment($('#end_date').val());
			var duration = moment.duration(endDate.diff(startDate));
			var monthPeriod = parseInt(duration.asDays()/30);
			if(monthPeriod >= 3 && monthPeriod <= 6 ){
				$('#month_period').val(monthPeriod).trigger('change');	
			}
			else{
				$('#month_period').val(null).trigger('change');	
			}
			
		});
	});	
	
	$('#end_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	
	$('#effective_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}).on('change', function (e) {
		if($('#effective_date').val() == '' || $('#effective_date').val() == 'NaN-NaN-NaN'){
			$('#des_eff').val('');
		}
		else{
			$('#des_eff').val(moment($('#effective_date').val()).format('DD MMM YYYY')).trigger('change');
		}	
		$('#des_exp').val('');		
		$('#expired_date').datepicker('destroy');
		$('#expired_date').addClass('form-control-sm');
		$('#expired_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
			minDate: $('#effective_date').val(),
		}).on('change', function (e) {
			if($('#expired_date').val() == '' || $('#expired_date').val() == 'NaN-NaN-NaN'){
				$('#des_exp').val('');
			}
			else{
				$('#des_exp').val(moment($('#expired_date').val()).format('DD MMM YYYY')).trigger('change');
			}		
		});
	});
	
	$('#expired_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	setTimeout(function () {
		$('#expired_date').parent().children('span').children('button').attr('disabled',true);
		$("#with_reco").addClass('readonly-checkbox');
	}, 600);
	
	var note_pos = 'Jika ada perubahan Posisi, Field <i><b>New Position</b></i> wajib di isi';
	$('.help_pos_input').attr('data-content', note_pos);
	
	$('[data-toggle="popover"]').popover({html : true});
	
});

function get_edit(id_recommendation_header,type) {
	$.ajax({
		url: "<?= url('employee/employee/recommendation_form/get_edit') ?>",
		method: "GET",
		data: {
			id_recommendation_header: id_recommendation_header,
			type:type,
		},
		success: function (response) {
			get_reco_check(type,moment(response.creation_date).format('YYYY-MM-DD'));
			$('#id_recommendation_header').val(response.id_recommendation_header).trigger('change');
			$('#id_employee').attr('readonly',true);
			get_employee(response.id_employee,type).then(function(res) {
				$('#id_employee').val(response.id_employee).trigger('change',[true]);
			});			
			$('#id_position_detail').val(response.id_position_detail).trigger('change');
			$('#position').val(response.routing).trigger('change');
			$('#grade').val(response.grade).trigger('change');
			$('#dept').val(response.dept).trigger('change');
			$('#region').val(response.region).trigger('change');
			$('#branch').val(response.branch).trigger('change');
			$('#location').val(response.location).trigger('change');
			$('#principal').val(response.principal).trigger('change');
			$('#id_employment_status').val(response.id_employment_status).trigger('change');
			$('#emp_status').val(response.emp_status).trigger('change');
			$('#start_date').val(response.start_date).trigger('change');
			$('#end_date').val(response.end_date).trigger('change');
			$('#month_period').val(response.period_evaluation).trigger('change');
			$('#id_approval').val(response.id_approval_hierarchy).trigger('change');
			$('#id_approval_status').val(response.id_approval_status).trigger('change');			
			$('#id_transition_category').val(response.id_transition_category).trigger('change',[true]);
			$('#id_transition_category').attr('readonly',true);
			$('#id_transition_type').attr('readonly',true);
			$('#effective_date').val(response.effective_date).trigger('change');
			$('#expired_date').val(response.expired_date).trigger('change');
			$('#duration').val(response.period_type).trigger('change');
			
			setTimeout(function () {
				get_hierachy(type,response.id_recommendation_header,response.id_location);
			}, 300);
											
			get_type(response.code_cat).then(function(res) {
				$('#id_transition_type').val(response.id_transition_type).trigger('change',[true]);
				if(response.code_type == 'Orientation' || response.code_type == 'Promotion' || response.code_type == 'Temporary Assignment'){
					if(type != 'view'){
						$('#expired_date').attr('disabled',false);
						$('#expired_date').parent().children('span').children('button').attr('disabled',false);
					}
					get_pro_ori();
				}
				else if(response.code_type == 'Terminate'){
					if(type != 'view'){
						$('#expired_date').attr('disabled',false);
						$('#expired_date').parent().children('span').children('button').attr('disabled',false);
					}
				}
				else{
					if(type != 'view'){
						$('#expired_date').attr('disabled',true);
						$('#expired_date').parent().children('span').children('button').attr('disabled',true);
						$('#dur').hide();
						$('#duration').attr('disabled',true);
					}
				}
				get_help(response.code_type);
			});
			
			$('#id_new_position_detail').val(response.id_new_position_detail).trigger('change');
			$('#new_position_routing').val(response.new_position).trigger('change');
			$('#new_dept').val(response.new_dept).trigger('change');
			$('#new_grade').val(response.new_grade).trigger('change');
			$('#new_branch').val(response.new_branch).trigger('change');
			$('#new_location').val(response.new_location).trigger('change');
			$('#new_principal').val(response.new_principal).trigger('change');
			if(response.id_new_position_detail != null){
				get_new_mgr(response.id_new_position_detail).then(function(res) {
					$('#id_new_mgr').val(response.id_new_chief_employee).trigger('change');
				});
			}			
			$("#with_reco").addClass('readonly-checkbox');
			if(response.reco_flag == true){
				$('#new_gen_quanti').show();
				$('#new_gen_quanti').attr('onclick','genKpi('+response.id_employee+')');
				$('#with_reco').prop('checked', true).change();
				global_id_quanti = 0;
				$.each(response.quanti, function (i, item) {
					$('#new_rec_quanti').trigger('click');								
				});	
				
				global_id_quali = 0;
				$.each(response.quali, function (i, item) {
					$('#new_rec_quali').trigger('click');								
				});			

				setTimeout(function () {
					$('#tot_one').val(response.average_kpi_1_month_ago).trigger('change');
					$('#tot_two').val(response.average_kpi_2_month_ago).trigger('change');
					$('#tot_three').val(response.average_kpi_3_month_ago).trigger('change');
					$('#tot_four').val(response.average_kpi_4_month_ago).trigger('change');
					$('#tot_five').val(response.average_kpi_5_month_ago).trigger('change');
					$('#tot_six').val(response.average_kpi_6_month_ago).trigger('change');
					$('#total_kpi').val(response.kpi_average_ap6m).trigger('change');
					$('#total_360').val(response.total_percent).trigger('change');
					if(response.kpi_average_ap6m >= 85){
						$('#status_kpi').show();
						$('#status_kpi').html('Pass').addClass("badge badge-success").removeClass('badge-danger');
					}
					else if(response.kpi_average_ap6m < 85 && response.kpi_average_ap6m != null){
						$('#status_kpi').show();
						$('#status_kpi').html('Not Pass').addClass("badge badge-danger").removeClass('badge-success');
					}
					else{
						$('#status_kpi').hide();
					}
					var bot_sum_1 = 0;
					var bot_sum_2 = 0;
					var bot_sum_3 = 0;
					var bot_sum_4 = 0;
					var bot_sum_5 = 0;
					var bot_sum_6 = 0;
					
					$('#table_rec_quanti_body tr').each(function (index) {
						if(type == 'view'){
							$(this).find('.delete-record-quanti').hide();
						}
						$(this).find('span.sn').html(index + 1);
						$(this).find('span.sn').attr('id','idx_quanti');
						$(this).find('.id_recommendation_quantitative_input').val(response.quanti[index].id_recommendation_quantitative);
						$(this).find('.desc_kpi_input').val(response.quanti[index].desc_kpi).trigger('change');
						$(this).find('.weight_1_input').val(response.quanti[index].weight_1).trigger('change');
						$(this).find('.obj_1_input').val(response.quanti[index].obj_1).trigger('change');
						$(this).find('.ach_1_input').val(response.quanti[index].ach_1).trigger('change');
						$(this).find('.idx_1_input').val(response.quanti[index].idx_1).trigger('change');
						$(this).find('.weight_2_input').val(response.quanti[index].weight_2).trigger('change');
						$(this).find('.obj_2_input').val(response.quanti[index].obj_2).trigger('change');
						$(this).find('.ach_2_input').val(response.quanti[index].ach_2).trigger('change');
						$(this).find('.idx_2_input').val(response.quanti[index].idx_2).trigger('change');
						$(this).find('.weight_3_input').val(response.quanti[index].weight_3).trigger('change');
						$(this).find('.obj_3_input').val(response.quanti[index].obj_3).trigger('change');
						$(this).find('.ach_3_input').val(response.quanti[index].ach_3).trigger('change');
						$(this).find('.idx_3_input').val(response.quanti[index].idx_3).trigger('change');
						$(this).find('.weight_4_input').val(response.quanti[index].weight_4).trigger('change');
						$(this).find('.obj_4_input').val(response.quanti[index].obj_4).trigger('change');
						$(this).find('.ach_4_input').val(response.quanti[index].ach_4).trigger('change');
						$(this).find('.idx_4_input').val(response.quanti[index].idx_4).trigger('change');
						$(this).find('.weight_5_input').val(response.quanti[index].weight_5).trigger('change');
						$(this).find('.obj_5_input').val(response.quanti[index].obj_5).trigger('change');
						$(this).find('.ach_5_input').val(response.quanti[index].ach_5).trigger('change');
						$(this).find('.idx_5_input').val(response.quanti[index].idx_5).trigger('change');
						$(this).find('.weight_6_input').val(response.quanti[index].weight_6).trigger('change');
						$(this).find('.obj_6_input').val(response.quanti[index].obj_6).trigger('change');
						$(this).find('.ach_6_input').val(response.quanti[index].ach_6).trigger('change');
						$(this).find('.idx_6_input').val(response.quanti[index].idx_6).trigger('change');
						
						if($(this).find('.weight_1_input').val() != ''){
							bot_sum_1 += parseFloat(response.quanti[index].weight_1);
						}
						if($(this).find('.weight_2_input').val() != ''){
							bot_sum_2 += parseFloat(response.quanti[index].weight_2);
						}
						if($(this).find('.weight_3_input').val() != ''){
							bot_sum_3 += parseFloat(response.quanti[index].weight_3);
						}
						if($(this).find('.weight_4_input').val() != ''){
							bot_sum_4 += parseFloat(response.quanti[index].weight_4);
						}
						if($(this).find('.weight_5_input').val() != ''){
							bot_sum_5 += parseFloat(response.quanti[index].weight_5);
						}
						if($(this).find('.weight_6_input').val() != ''){
							bot_sum_6 += parseFloat(response.quanti[index].weight_6);
						}
					});	
					
					$('#bobot_one').val(parseFloat(parseFloat(bot_sum_1).toFixed(2))).trigger('change');
					$('#bobot_two').val(parseFloat(parseFloat(bot_sum_2).toFixed(2))).trigger('change');
					$('#bobot_three').val(parseFloat(parseFloat(bot_sum_3).toFixed(2))).trigger('change');
					$('#bobot_four').val(parseFloat(parseFloat(bot_sum_4).toFixed(2))).trigger('change');
					$('#bobot_five').val(parseFloat(parseFloat(bot_sum_5).toFixed(2))).trigger('change');
					$('#bobot_six').val(parseFloat(parseFloat(bot_sum_6).toFixed(2))).trigger('change');
					
					if($('#bobot_one').val() == 'NaN' || $('#bobot_one').val() == 0){
						$('#bobot_one').val('').trigger('change');
					}
					if($('#bobot_two').val() == 'NaN' || $('#bobot_two').val() == 0){
						$('#bobot_two').val('').trigger('change');
					}
					if($('#bobot_three').val() == 'NaN' || $('#bobot_three').val() == 0){
						$('#bobot_three').val('').trigger('change');
					}
					if($('#bobot_four').val() == 'NaN' || $('#bobot_four').val() == 0){
						$('#bobot_four').val('').trigger('change');
					}
					if($('#bobot_five').val() == 'NaN' || $('#bobot_five').val() == 0){
						$('#bobot_five').val('').trigger('change');
					}
					if($('#bobot_six').val() == 'NaN' || $('#bobot_six').val() == 0){
						$('#bobot_six').val('').trigger('change');
					}
							
					$('#table_rec_quali_body tr').each(function (index) {
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_recommendation_qualitative_input').val(response.quali[index].id_recommendation_qualitative);
						$(this).find('.id_appraiser_input').val(response.quali[index].id_employee_appraisers).trigger('change');
						$(this).find('.appraisers_hierarchy_input').val(response.quali[index].appraisers_hierarchy).trigger('change');
						$(this).find('.desc_score_input').html(response.quali[index].subtotal_score);
						if(type == 'view'){
							$(this).find('.delete-record-quali').hide();
						}
						if(response.quali[index].submitted == true){
							$(this).find('.delete-record-quali').hide();
							$(this).find('.id_appraiser_input').attr('readonly',true);
							$(this).find('.appraisers_hierarchy_input').attr('readonly',true);
							$(this).find('.submitted_input').html('<span class="badge badge-success">YES</span>');
						}
						else{
							$(this).find('.submitted_input').html('<span class="badge badge-danger">NO</span>');
						}
					});	
				//	get_sumQualitative(response.id_employee,response.id_recommendation_header);
					$('#link_tab_rec-quali').click(function() {
						$('#sum_qualitative').show();
						get_sumQualitative(response.id_employee,response.id_recommendation_header);
						$("#table_sum_qualitative_processing").css("background","white");
						$("#table_sum_qualitative_processing").css("color","black");
					});
					
					$('#notes_summary').val(response.notes).trigger('change');
					$('#decision').val(response.id_decision_recommendation).trigger('change');
					$('#des_eff').val(response.effective_date).trigger('change');
					$('#des_exp').val(response.expired_date).trigger('change');
				}, 600);	
			}
			else{
				$("#save_and_draft").hide();
			}
			
			if(type == 'view'){
				$("#recoForm input").attr('disabled',true);
				$("#recoForm select").attr('readonly',true);
				$("#recoForm textarea").attr('disabled',true);
				$("#effective_date, #expired_date, #start_date, #end_date").parent().children('span').children('button').attr('disabled',true);
				$("#new_position_routing").parent().children('div').children('a.clear_pos ').css('pointer-events','none');
				$('#new_rec_quanti').hide();
				$('#new_rec_quali').hide();
				$('#new_gen_quanti').hide();
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
				text: 'Something went wrong!'
			});
		}
	});
};	

</script>