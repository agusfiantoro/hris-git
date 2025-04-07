<div class="row">
    <div class="col-12">
            <div class="card-body">
                <div class="row">
					<div align="center" class="col-md-2"> 
						<div class="widget-user-image" style="margin-bottom:10px;">
							<img id="can_attach" class="img-circle elevation-2" style="height:150px;width:150px;border-radius:999px;" alt="User Avatar">
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<label class="col-sm-4 col-form-label">Candidate Name</label>
                            <div class="col-sm-8">
								<input id="can_name" class="form-control form-control-sm" readonly>	
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Email</label>
                            <div class="col-sm-8">
								<input id="can_mail" class="form-control form-control-sm" readonly>	
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Mobile Phone</label>
                            <div class="col-sm-8">
								<input id="can_mobile" class="form-control form-control-sm" readonly>	
                            </div>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Interest Position</label>
                            <div class="col-sm-8">
								<input id="can_dept" class="form-control form-control-sm" readonly>	
                            </div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="row">
							<label class="col-sm-4 col-form-label">Curriculum Vitae (CV)</label>
                            <label class="col-sm-8 col-form-label">
								<a id="can_cv" href="#" target="_blank" style="font-size:14px;">Download (CV)</a>
                            </label>
						</div>
						<div class="row">
							<label class="col-sm-4 col-form-label">Application Letter</label>
                            <label class="col-sm-8 col-form-label">
								<a id="can_app_letter" href="#" target="_blank" style="font-size:14px;">Download (Application Letter)</a>
                            </label>
						</div>
					</div>
				</div>
            </div>
		<div style="padding:10px;">
			<div class="card card-success card-outline">
					<div class="card-body">
						<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Applied Job</div>
						<div class="row">
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-4 col-form-label">Job Position</label>
									<div class="col-sm-8">
										<input id="id_candidate" name="id_candidate" type="hidden">
										<input id="id_applied" name="id_applied" type="hidden">
										<select name="pos_req" id="pos_req" class="form-control form-control-sm select2" style="width: 100%;">
										</select>
										<span class="invalid-feedback" role="alert" id="pos_reqError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Ref Number (FPK)</label>
									<div class="col-sm-8">
										<input id="ref_number" class="form-control form-control-sm" readonly>	
									</div>
								</div>		
								<div class="row">
									<label class="col-sm-4 col-form-label">Date Applied</label>
									<div class="col-sm-8">
										<input id="can_date_applied" class="form-control form-control-sm" readonly>	
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Branch</label>
									<div class="col-sm-8">
										<input id="can_branch" class="form-control form-control-sm" readonly>	
									</div>
								</div>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-3 col-form-label">Rec. Stage</label>
									<div class="col-sm-8">
										<select name="can_stage" id="can_stage" class="form-control form-control-sm select2" style="width: 100%;">
										</select>
										<span class="invalid-feedback" role="alert" id="can_stageError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-3 col-form-label">Status</label>
									<div class="col-sm-8">
										<select name="can_status" id="can_status" class="form-control form-control-sm select2" style="width: 100%;">
										</select>
										<span class="invalid-feedback" role="alert" id="can_statusError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-3 col-form-label">Offering Date</label>
									<div class="col-sm-8">
										<input type="text" name="offering_date" id="offering_date" class="form-control form-control-sm">
										<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="offering_dateError">
											<strong></strong>
										</span>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-3 col-form-label">Join Date</label>
									<div class="col-sm-8">
										<input type="text" name="join_date" id="join_date" class="form-control form-control-sm">
										<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="join_dateError">
											<strong></strong>
										</span>
									</div>
								</div>
							</div>
							<div class="col-md-1"></div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<div style="font-weight:bold;">Notes</div>
								<textarea id="add_note" name="add_note" class="form-control form-control-sm" style="height:80px;"></textarea>
							</div>
						</div>
					</div>
			</div>			
			<div class="row">
                <div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_employee_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_menu-personal" data-toggle="pill" href="#menu-personal" role="tab" aria-controls="link_tab_menu-personal" aria-selected="true">Personal Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-edu" data-toggle="pill" href="#menu-edu" role="tab" aria-controls="link_tab_menu-edu" aria-selected="true">Education Information</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-family" data-toggle="pill" href="#menu-family" role="tab" aria-controls="link_tab_menu-family" aria-selected="true">Family Information</a>
						</li>								
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-ex" data-toggle="pill" href="#menu-ex" role="tab" aria-controls="link_tab_menu-ex" aria-selected="true">Work Experience</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-int" data-toggle="pill" href="#menu-int" role="tab" aria-controls="link_tab_menu-int" aria-selected="true">Interview Result</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-psy" data-toggle="pill" href="#menu-psy" role="tab" aria-controls="link_tab_menu-psy" aria-selected="true">Psychotest</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="link_tab_menu-others" data-toggle="pill" href="#menu-others" role="tab" aria-controls="link_tab_menu-others" aria-selected="true">Others Information</a>
						</li>
					</ul>
					<div class="tab-content" id="tab_candidate_content" style="font-size:12px">
						<div class="tab-pane fade show active" style="font-size:14px" id="menu-personal" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-6">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Private Contact</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Current Address</label>
										<div class="col-sm-8">
											<textarea id="can_current" class="form-control form-control-sm" readonly></textarea>
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">Religion</label>
										<div class="col-sm-8">
											<input id="can_religion" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Marital Status</label>
										<div class="col-sm-8">
											<input id="can_marital" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Tax Number/No. NPWP</label>
										<div class="col-sm-8">
											<input id="can_npwp" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Driving License/No. SIM</label>
										<div class="col-sm-8">
											<input id="can_sim" class="form-control form-control-sm" readonly>	
										</div>
									</div>									
									<div class="row">
										<label class="col-sm-4 col-form-label">Emergency Contact</label>
										<div class="col-sm-8">
											<input id="can_em_contact" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Emergency Phone</label>
										<div class="col-sm-8">
											<input id="can_em_phone" class="form-control form-control-sm" readonly>	
										</div>
									</div>								
								</div>
								<div class="col-md-6">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Citizenship</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">ID Number/KTP</label>
										<div class="col-sm-8">
											<input id="can_ktp" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">KTP Address</label>
										<div class="col-sm-8">
											<textarea id="can_ktp_address" class="form-control form-control-sm" readonly></textarea>
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">Place of Birth</label>
										<div class="col-sm-8">
											<input id="can_place_birth" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Date of Birth</label>
										<div class="col-sm-8">
											<input id="can_date_birth" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Gender</label>
										<div class="col-sm-8">
											<input id="can_gender" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row">
										<label class="col-sm-4 col-form-label">Nationality</label>
										<div class="col-sm-8">
											<input id="can_national" class="form-control form-control-sm" readonly>	
										</div>
									</div>
								</div>
							</div>								
							<div class="row">
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin: 10px 0 5px 0;">About Me</div>
									<textarea id="about_me" class="form-control form-control-sm" style="height:120px;" readonly></textarea>
								</div>
							</div>
							
							<div class="card card-warning card-outline" style="margin-top:20px;">
								<div class="card-body">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Media Social</div>
									<div class="row">
										<div class="col-md-4">
											<div class="row">
												<label class="col-sm-4 col-form-label">Instagram</label>
												<div class="col-sm-8">
													<input id="can_ig" class="form-control form-control-sm" readonly>	
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Twitter</label>
												<div class="col-sm-8">
													<input id="can_twitter" class="form-control form-control-sm" readonly>	
												</div>
											</div>
										</div>
										<div class="col-md-1"></div>
										<div class="col-md-4">
											<div class="row">
												<label class="col-sm-4 col-form-label">LinkedIn</label>
												<div class="col-sm-8">
													<input id="can_li" class="form-control form-control-sm" readonly>	
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Facebook</label>
												<div class="col-sm-8">
													<input id="can_fb" class="form-control form-control-sm" readonly>	
												</div>
											</div>
										</div>
										<div class="col-md-3"></div>
									</div>
								</div>
							</div>						
						</div>							
					
						<div class="tab-pane fade" style="font-size:14px" id="menu-edu" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Last Education</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<label class="col-sm-4 col-form-label">Level Education</label>
										<div class="col-sm-8">
											<input id="edu_level" class="form-control form-control-sm" readonly>
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">University/School Name</label>
										<div class="col-sm-8">
											<input id="edu_univ" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">Major</label>
										<div class="col-sm-8">
											<input id="edu_major" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">City</label>
										<div class="col-sm-8">
											<input id="edu_city" class="form-control form-control-sm" readonly>	
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<label class="col-sm-4 col-form-label">From Date</label>
										<div class="col-sm-8">
											<input id="edu_from" class="form-control form-control-sm" readonly>
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">To Date</label>
										<div class="col-sm-8">
											<input id="edu_to" class="form-control form-control-sm" readonly>	
										</div>
									</div>
									<div class="row" style="margin-top:4px;">
										<label class="col-sm-4 col-form-label">IPK/Scores</label>
										<div class="col-sm-8">
											<input id="edu_ipk" class="form-control form-control-sm" readonly>	
										</div>
									</div>
								</div>
							</div>
						</div>
					
						<div class="tab-pane fade" style="font-size:14px" id="menu-family" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-12">
									<table style="width:100%;" id="table_family" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="width:10px;">No.</th>
												<th style="white-space:nowrap;">Relationship</th>
												<th style="white-space:nowrap;">Name</th>
												<th style="white-space:nowrap;">Gender</th>
												<th style="white-space:nowrap;">Birthdate</th>
												<th style="white-space:nowrap;">Mobile Phone</th>
												<th style="white-space:nowrap;">Address</th>
												<th style="white-space:nowrap;">Last Education</th>
												<th style="white-space:nowrap;">Working</th>
											</tr>
										</thead>
										<tbody id="table_family_body">
										</tbody>									  
									</table>
								</div>
							</div>
						</div>
						
						<div class="tab-pane fade" style="font-size:14px" id="menu-ex" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Experience</div>
								</div>
								<div class="col-12">
									<table style="width:100%;" id="table_ex" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="width:10px;">No.</th>
												<th style="white-space:nowrap;">Job Position</th>
												<th style="white-space:nowrap;">Company</th>
												<th style="white-space:nowrap;">From Date</th>
												<th style="white-space:nowrap;">To Date</th>
												<th>Reason Out</th>
												<th style="white-space:nowrap;">Salary</th>
											</tr>
										</thead>
										<tbody id="table_ex_body">
										</tbody>									  
									</table>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Skill</div>
								</div>
								<div class="col-12">
									<table style="width:100%;" id="table_skill" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="width:10px;">No.</th>
												<th style="white-space:nowrap;">Skill Name</th>
												<th style="white-space:nowrap;">Level</th>
											</tr>
										</thead>
										<tbody id="table_skill_body">
										</tbody>									  
									</table>
								</div>
							</div>
							<br>
							<div class="row">	
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Training & Certification</div>
								</div>
								<div class="col-12">
									<table style="width:100%;" id="table_cert" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="width:10px;">No.</th>
												<th style="white-space:nowrap;">Certification Name</th>
												<th style="white-space:nowrap;">Certified By</th>
												<th style="white-space:nowrap;">Issued Year</th>
												<th style="white-space:nowrap;">Validity Period</th>
											</tr>
										</thead>
										<tbody id="table_cert_body">
										</tbody>									  
									</table>
								</div>
							</div>
						</div>
						
						<div class="tab-pane fade" style="font-size:14px" id="menu-int" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-12">
									<table style="width:100%;" id="table_interview_result" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="width:10px;">No.</th>
												<th style="white-space:nowrap;">Interview Type</th>
												<th style="white-space:nowrap;">Interview Date</th>
												<th style="white-space:nowrap;">Score</th>
												<th style="white-space:nowrap;">Conclusion</th>
												<th>Notes</th>
											</tr>
										</thead>
										<tbody id="table_interview_body">
										</tbody>									  
									</table>
								</div>
							</div>
						</div>
					
						<div class="tab-pane fade" style="font-size:14px" id="menu-psy" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12">
									<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Psychotest Result</div>
								</div>
								<div class="col-12">
									<table style="width:100%;" id="table_psy_result" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr>
												<th style="white-space:nowrap;">Batch</th>
												<th style="white-space:nowrap;">Date Test</th>
												<th style="white-space:nowrap;">DISC Profile</th>
												<th style="white-space:nowrap;">PAPI Kostick</th>
												<th style="white-space:nowrap;">Kraepelin</th>
												<th style="white-space:nowrap;">BCT</th>
												<th style="white-space:nowrap;">Conclusion</th>
												<th style="white-space:nowrap;">Psychogram</th>
											</tr>
										</thead>
										<tbody id="table_psy_body">
										</tbody>									  
									</table>
								</div>
							</div>
						</div>
					
						<div class="tab-pane fade" style="font-size:14px" id="menu-others" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">1. Berikan contoh sebuah masalah yang pernah Anda selesaikan. Jelaskan bagaimana reaksi Anda, Keputusan apa yang telah Anda buat, dan bagaimana cara Anda menyelesaikan masalah tersebut ?</div>
									<textarea id="info_1" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">2. Berikan contoh sebuah ide Anda yang telah terwujud serta jelaskan kesulitan apa yang Anda alami dan hasil apa yang telah Anda capai terkait ide tersebut !</div>
									<textarea id="info_2" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">3. Apakah Anda bersedia ditempatkan di seluruh unit kerja (di luar tempat yang Anda tinggali saat ini) ?</div>
									<textarea id="info_3" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">4. Apakah Anda pernah menderita sakit keras ? Jika pernah, sakit keras apa dan kapan ?</div>
									<textarea id="info_4" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">5. Apakah Anda pernah melamar dalam kurun waktu 1 Tahun ini ? Jika pernah, mohon sebutkan waktunya, posisi yang dilamar dan tahapan seleksi terakhir !</div>
									<textarea id="info_5" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">6. Darimana Anda mendapatkan informasi lowongan kerja ?</div>
									<textarea id="info_6" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">7. Kapan Anda bisa mulai bekerja apabila diterima ?</div>
									<textarea id="info_7" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
								</div>
								<div class="col-md-12" style="margin-bottom:10px;">
									<div align="justify">8. Berapa gaji yang anda harapkan ?</div>
									<textarea id="info_8" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
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
	id_candidate = {!! $global_id_candidate !!};
	id_applied = {!! $global_id_applied !!};
	id_dept = {!! $global_id_dept !!};
	id_job_grade = {!! $global_id_job_grade !!};
	id_batch = {!! $global_id_batch !!};
	
	get_pos();
	
	$('#offering_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	$('#join_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	$('#interview_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	
	$('#can_status').prepend('<option selected></option>').select2({
		placeholder: "Select Status ...",
		data: rec_status,
	});
	
	get_edit(id_candidate,id_applied);
});

function get_edit(id_candidate,id_applied) {
	$('#offering_date').attr('disabled',true);
	$('#join_date').attr('disabled',true);
	
	$('#offering_date').parent().children('span').children('button').attr('disabled', true);
	$('#join_date').parent().children('span').children('button').attr('disabled', true);
						
		$.ajax({
			url: "<?= url('recruitment/recruitment/candidate/get_edit_detail') ?>",
            method: "GET",
            data: {id_candidate: id_candidate,id_applied: id_applied},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				document.getElementById("can_attach").src = 'https://career.borwita.co.id/storage/candidate_photo/'+response.photo_candidate;
				$('#id_candidate').val(id_candidate).trigger('change');
				$('#id_applied').val(id_applied).trigger('change');
				$('#can_name').val(response.name).trigger('change');
				$('#can_mail').val(response.email).trigger('change');
				$('#can_mobile').val(response.mobile_phone).trigger('change');
				$('#can_dept').val(response.dept).trigger('change');
				$('#can_cv').attr('href','https://career.borwita.co.id/storage/curriculum_vitae/'+ response.cv_upload);
				$('#can_app_letter').attr('href','https://career.borwita.co.id/storage/application_letter/'+ response.app_letter_upload);
				if(response.applied.length > 0 ){
				//	console.log(response.applied[0]);
					global_code_stage = response.applied[0].code_status;
					if(response.applied[0].status == 'Pass'){
						get_stage_applied(response.applied[0].sequence,response.applied[0].id_candidate_status).then(function(res) {
							setTimeout(function () {
								$('#can_stage').val(response.applied[0].id_candidate_status).trigger('change',[true]);
							}, 500);
						});
					}
					else {
						get_stage_preview(response.applied[0].sequence).then(function(res) {
							setTimeout(function () {
								$('#can_stage').val(response.applied[0].id_candidate_status).trigger('change',[true]);
							}, 500);
						});
					}
					
					if(response.applied[0].code_status == 'HIR' && response.applied[0].status == 'Pass'){
						setTimeout(function () {
							$("#detailCanForm select#pos_req").attr("readonly", true);
							$("#detailCanForm select#can_stage").attr("readonly", true);
							$("#detailCanForm select#can_status").attr("readonly", true);
							$("#detailCanForm input#offering_date").attr("readonly",true);
							$("#offering_date").parent().children('span').children('button').attr("disabled", true);
						}, 1000);
					}	
					else if(response.hired_date != null && response.join_date != null){
						$('#myModal').modal('hide');
							swal({
								icon: 'error',
								title: 'Oops...',
								dangerMode: true,
								text: 'Candidate has been Hired in Other Position',
							}).then(function(){ 
								$('#candidate_table').DataTable().ajax.reload();
								$('#list_candidate_table').DataTable().ajax.reload();
							   }
							);
					}																		
					get_pos_done(response.applied[0].id_hiring_request_header).then(function(res) {
						setTimeout(function () {
							$('#pos_req').val(response.applied[0].id_hiring_request_header).trigger('change');
						}, 500);
					}); 
										 		
					$('#ref_number').val(response.applied[0].reference_number).trigger('change');
					$('#can_date_applied').val(moment(response.applied[0].applied_date).format('DD MMM YYYY')).trigger('change');
					global_sum_sequence = response.applied[0].sequence + 1;
					if(response.applied[0].code_status == 'PSY' || response.applied[0].code_status == 'REF'){
						global_sum_obs = response.applied[0].sequence + 2;
					}
					global_sequence = response.applied[0].sequence;
					global_id_stage = response.applied[0].id_candidate_status;
					
					$('#can_status').val(response.applied[0].status).trigger('change');
					$('#can_branch').val(response.applied[0].branch).trigger('change');
					$('#offering_date').val(response.hired_date).trigger('change');
					$('#join_date').val(response.join_date).trigger('change');
					$('#add_note').val(response.additional_note).trigger('change');
				}
				else{
					get_stage();
				}
				$('#can_current').val(response.address_home).trigger('change');
				$('#can_religion').val(response.religion).trigger('change');
				$('#can_marital').val(response.marital).trigger('change');
				$('#can_npwp').val(response.taxpayer_identification_number).trigger('change');
				$('#can_sim').val(response.driving_license_number).trigger('change');
				$('#can_em_contact').val(response.emergency_contact).trigger('change');
				$('#can_em_phone').val(response.emergency_phone).trigger('change');
				
				$('#can_ktp').val(response.identification_number).trigger('change');
				$('#can_ktp_address').val(response.idcard_address).trigger('change');
				$('#can_place_birth').val(response.place_of_birth).trigger('change');
				$('#can_date_birth').val(response.date_of_birth).trigger('change');
				if(response.gender == 'M'){
					$('#can_gender').val('Male').trigger('change');
				}
				else if(response.gender == 'F'){
					$('#can_gender').val('Female').trigger('change');
				}
				$('#can_national').val(response.country).trigger('change');
				
				$('#about_me').val(response.about_me).trigger('change');

				$('#can_ig').val(response.link_instagram).trigger('change');
				$('#can_twitter').val(response.link_twitter).trigger('change');
				$('#can_li').val(response.link_linkedin).trigger('change');
				$('#can_fb').val(response.link_facebook).trigger('change');
				
				$('#info_1').val(response.info_1).trigger('change');
				$('#info_2').val(response.info_2).trigger('change');
				$('#info_3').val(response.info_3).trigger('change');
				$('#info_4').val(response.info_4).trigger('change');
				$('#info_5').val(response.info_5).trigger('change');
				$('#info_6').val(response.info_6).trigger('change');
				$('#info_7').val(response.info_7).trigger('change');
				$('#info_8').val(response.info_8).trigger('change');
				
				if(response.edu.length > 0 ){
					$('#edu_level').val(response.edu[0].edu_level).trigger('change');
					$('#edu_univ').val(response.edu[0].education_name).trigger('change');
					$('#edu_major').val(response.edu[0].major).trigger('change');
					$('#edu_city').val(response.edu[0].education_city).trigger('change');
					$('#edu_ipk').val(response.edu[0].grade_point_average).trigger('change');
					$('#edu_from').val(response.edu[0].start_year).trigger('change');
					$('#edu_to').val(response.edu[0].end_year).trigger('change');
				}
				
				get_family(id_candidate);
				get_ex(id_candidate);
				get_skill(id_candidate);
				get_cert(id_candidate);
				get_interview(id_candidate,id_applied);
				get_psychotest(id_candidate,id_applied,id_dept,id_job_grade,id_batch);
				
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

function get_stage(){		
		$.getJSON('<?= url('recruitment/recruitment/candidate/get_stage') ?>', function (data) {			
			let code_view = 0;
			let arr_view = [];
			$.each(data, function(idx, item) {
				 if(item.code == 'DBS' || item.code == 'DRF' || item.code == 'SHL'){
					code_view = item.id;
					arr_view.push(code_view);
				 }
			});
			$('#can_stage').prepend('<option selected></option>').select2({
				placeholder: "Select Recruitment Stage ...",
				data: data,
			}).on('change', function (e) {
				$('#can_stage option').attr('disabled',true);
				var aaList = $("option", e.target);
				$.each(aaList, function(idx, item) {
					$.each(arr_view, function(i, val) {
						$("option[value='"+val+"']").attr('disabled',false);																							
					});
				});		
			}).trigger('change');	
				
		}).fail(function (data) { // Call failed
            get_stage();
		});					
}

function get_family(id_candidate) {	
	let myData = {
		id_candidate: id_candidate,
	};
	$('#table_family').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_family') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_family').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'relationship', name: 'relationship', render: function ( data, type, row ) {
					return data.charAt(0).toUpperCase() + data.slice(1);				
				}
			},
			{data: 'family_name', name: 'family_name'},
			{data: 'gender', name: 'gender', render: function ( data, type, row ) {
					if(data == 'M'){
							return 'Male';
					}
					else if(data == 'F'){
							return 'Female';
					}					
				} 
			},
			{data: 'birthdate', name: 'birthdate'},
			{data: 'mobile_phone', name: 'mobile_phone'},
			{data: 'address_home', name: 'address_home', render: function ( data, type, row ) {				
					return data;
				} 
			},
			{data: 'last_edu', name: 'last_edu'},
			{data: 'current_works', name: 'current_works'},
			
		]
	});
}

function get_ex(id_candidate) {		
	let myData = {
		id_candidate: id_candidate,
	};
	$('#table_ex').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_ex') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_ex').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'position_name', name: 'position_name'},
			{data: 'company_name', name: 'company_name'},
			{data: 'start_year', name: 'start_year', render: function ( data, type, row ) {
					return moment(data).format('MMM YYYY');				
				}
			},
			{data: 'end_year', name: 'end_year', render: function ( data, type, row ) {
					if(data != null){
						return moment(data).format('MMM YYYY');				
					}
					else{
						return "Now";
					}
				}
			},
			{data: 'reason_out', name: 'reason_out'},
			{data: 'salary', name: 'salary', render: function ( data, type, row ) {
					if(row.salary_type == 'N'){
						return data+',- Nett';
					}
					else if(row.salary_type == 'G'){
						return data+',- Gross';
					}
					else {
						return data+',-'
					}
				}
			},
		]
	});
}

function get_skill(id_candidate) {				
	let myData = {
		id_candidate: id_candidate,
	};
	$('#table_skill').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_skill') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_skill').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'skill_name', name: 'skill_name'},
			{data: 'skill_level', name: 'skill_level', render: function ( data, type, row ) {
					if(data == 5){
						return data+' (Expert)';
					}
					else if(data == 4){
						return data+' (Proficient)';
					}
					else if(data == 3){
						return data+' (Intermediate)';
					}
					else if(data == 2){
						return data+' (Advanced Beginner)';
					}
					else {
						return data+' (Basic)'
					}
				}
			},
		]
	});
}

function get_cert(id_candidate) {			
	let myData = {
		id_candidate: id_candidate,
	};
	$('#table_cert').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_cert') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_cert').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'certification_name', name: 'certification_name'},
			{data: 'certified_by', name: 'certified_by'},
			{data: 'years_issued', name: 'years_issued'},
			{data: 'validity_period', name: 'validity_period'},		
		]
	});
}

function get_interview(id_candidate,id_applied) {			
	let myData = {
		id_candidate: id_candidate,
		id_applied: id_applied,
	};
	$('#table_interview_result').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_interview') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_interview_result').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'interview_type', name: 'interview_type'},
			{data: 'interview_date', name: 'interview_date'},
			{data: 'total_score', name: 'total_score'},
			{data: 'conclusion', name: 'conclusion'},
			{data: 'notes', name: 'notes'},
		]
	});
}

function get_psychotest(id_candidate,id_applied,id_dept,id_job_grade,id_batch) {			
	let urlWebCareer = 'https://career.borwita.co.id';
	let myData = {
		id_candidate: id_candidate,
		id_applied: id_applied,
		id_dept: id_dept,
		id_job_grade: id_job_grade,
		id_batch: id_batch,
	};
	$('#table_psy_result').DataTable({	
		destroy:true,
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
		ajax: {
			url: "<?= url('recruitment/recruitment/candidate/get_psychotest') ?>",
			data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_psy_result').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'batch', name: 'batch'},
			{data: 'test_date', name: 'test_date'},
			{data: 'disc', name: 'disc', className: 'text-score th-text-score', render: function ( data, type, row ) {
					if(data != ""){
						let downloadDisc = `${urlWebCareer}/assessment/result?test=disc&id_batch=${row.id_batch}&id_user_assessment=${row.id_candidate}&user_type=candidate`;
						return `<a class="badge badge-success" style="padding:5px;font-size:12px;" href="${downloadDisc}" target="_blank">${data}</a>`;
					}
					else{
						return '';
					}
				}
			},
			{data: 'papi', name: 'papi', className: 'text-score th-text-score', render: function ( data, type, row ) {
					if(data == true){
						let downloadPapi = `${urlWebCareer}/assessment/result?test=papi&id_batch=${row.id_batch}&id_user_assessment=${row.id_candidate}&user_type=candidate`;
						return `<a class="badge badge-success" style="padding:5px;font-size:12px;" href="${downloadPapi}" target="_blank">YES</a>`;
					}
					else if(data == false){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
					}
					else{
						return '';
					}
				} 
			},
			{data: 'kraepelin', name: 'kraepelin', className: 'text-score th-text-score', render: function ( data, type, row ) {
					if(data == true){
						let downloadKraepelin = `${urlWebCareer}/assessment/result?test=kraepelin&id_batch=${row.id_batch}&id_user_assessment=${row.id_candidate}&user_type=candidate`;
						return `<a class="badge badge-success" style="padding:5px;font-size:12px;" href="${downloadKraepelin}" target="_blank">YES</a>`;
					}
					else if(data == false){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
					}
					else{
						return '';
					}
				} 
			},
			{data: 'bct', name: 'bct', className: 'text-score th-text-score', render: function ( data, type, row ) {
					if(data == true){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">YES</span>';
					}
					else if(data == false){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
					}
					else{
						return '';
					}
				} 
			},
			{data: 'conclusion', name: 'conclusion', className: 'text-score th-text-score', render: function ( data, type, row ) {
					if( row.disarankan == null && row.dipertimbangkan == null && row.tidak_disarankan == null){
						return '';
					}
					else if(row.disarankan != ""){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Recommended (Direkomendasikan)</span>';
					}
					else if(row.dipertimbangkan != ""){
							return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">Considered (Dipertimbangkan)</span>';
					}
					else if(row.tidak_disarankan != ""){
							return '<span  class="badge badge-danger" style="padding:5px;font-size:12px;">Not Recommended (Tidak Direkomendasikan)</span>';
					}
					else{
						return '';
					}
				} 
			},
			{ data: 'action', name: 'action', responsivePriority: 1, orderable: false, className: 'th-text-score', render: function ( data, type, row ) {	
					if( row.disc_status == true && row.papi == true && row.kraepelin == true && row.bct == true){
						return '<div align="center">'+data+'</div>';
					}
					else{
						return '';
					}
				} 
			},
		]
	});
}

</script>
