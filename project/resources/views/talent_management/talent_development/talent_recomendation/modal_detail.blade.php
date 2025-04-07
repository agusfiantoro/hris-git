<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Recommendation Name</label>
						<div class="col-sm-8">
							<input id="notes" name="notes" class="form-control form-control-sm">
							 <span class="invalid-feedback" role="alert" id="notesError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Requested By</label>
						<div class="col-sm-8">
							<input name="id_talent_recommendation_header" id="id_talent_recommendation_header" type="hidden">	
							<select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
							</select>
							<span class="invalid-feedback" role="alert" id="id_employee_requestError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Projected Position</label>
						<div class="col-sm-8">
							<select name="projected_pos" id="projected_pos" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="projected_posError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Type</label>
						<div class="col-sm-8">
							<select name="rec_type" id="rec_type" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="rec_typeError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Period Date</label>
						<div class="col-sm-8">
							<input name="period_date" id="period_date" class="form-control form-control-sm">
							<span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="period_dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Status</label>
						<div class="col-sm-8">
							<select name="status" id="status" class="form-control form-control-sm" style="width:100%">
							</select>
							<span class="invalid-status" style="font-size:11px;color:#dc3545;" role="alert" id="statusError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">	
					<div class="row">
						<label class="col-sm-4 col-form-label">Source Grade</label>
						<div class="col-sm-8">
							<select name="source_grade[]" id="source_grade" class="form-control form-control-sm select2" data-placeholder="Select Source Grade ..." style="width: 100%;" multiple="multiple">
							</select>
							<span class="invalid-feedback" role="alert" id="source_gradeError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Source Position</label>
						<div class="col-sm-8">
							<select name="source_pos[]" id="source_pos" class="form-control form-control-sm select2" data-placeholder="Select Source Position ..." style="width: 100%;" multiple="multiple">
                            </select>
							<span class="invalid-feedback" role="alert" id="source_posError">
								<strong></strong>
							</span>                                    
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Source Region</label>
						<div class="col-sm-8">
						   <select name="source_region" id="source_region" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="source_regionError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Source Branch</label>
						<div class="col-sm-8">
						   <select name="source_branch[]" id="source_branch" class="form-control form-control-sm select2" data-placeholder="Select Source Branch ..." style="width: 100%;" multiple="multiple">
							</select>
							<span class="invalid-feedback" role="alert" id="source_branchError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Hiring Request (FPK)</label>
						<div class="col-sm-8">
							<select name="fpk" id="fpk" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="fpkError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">BEES Period</label>
						<div class="col-sm-8">
							<select name="survey" id="survey" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="surveyError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row" style="margin-top:20px;">
						<label class="col-sm-4 col-form-label"></label>
						<div class="col-sm-8">
							<button type="button" class="browse_talent pull-right btn btn-sm btn-success"><i class="fas fa-list-alt"></i> Browse Talent List</button>
							<button type="button" id="import_talent" class="import_talent pull-right btn btn-sm btn-success mr-1"><i class="fa fa-upload"></i> Import</button>
							<button type="button" id="export_talent" class="export_talent pull-right btn btn-sm btn-success mr-1"><i class="fa fa-file-excel-o"></i> Export</button>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-md-12">
		<ul class="nav nav-tabs" id="tab_emp_detail" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="link_tab_emp-details" data-toggle="pill" href="#emp-details" role="tab" aria-controls="link_tab_emp-details" aria-selected="true">Employee Talent Recommendation <span class="error-tab text-red"></span></a>
			</li>
		</ul>
		<div class="tab-content" id="tab_emp_detail_content" style="font-size:12px;">
		   <div class="tab-pane fade show active" id="emp-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
				<br/>
				<div class="row">
					<div class="col-md-12" style="margin-bottom: 10px">
						<button type="button" class="pull-right btn btn-xs btn-primary" id="new_emp_detail" style="display:none;"><span class="fas fa-plus"></span> Add</button>
					</div>
					<div class="col-md-12 fixTableHead">					
						<table id="table_emp_detail" style="width:1300px;" class="table table-striped table-bordered table-hover datatable">
							<thead>
								<tr align="center">
									<th>No.</th>
									<th>NIK</th>
									<th>Name</th>
									<th style="width:150px;">Position</th>
									<!-- th style="width:50px;">Department</th -->
									<th>Job Grade</th>
									<!-- th>Region</th -->
									<th>Branch</th>
									<th>Rating</th>
									<th style="width:120px;">Period</th>
									<th>Average KPI</th>
									<th style="width:180px;">Competencies</th>
									<th>Potencies (Psychogram)</th>
									<th>Psych. Department and Job Grade</th>
									<th style="width:120px;">Batch Assign Date</th>
									<th>SP</th>
									<th>KPK</th>
									<th>Engagement Level</th>
									<th style="width:150px;">Batch Name</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody id="table_emp_body"  style="overflow:auto;">
							</tbody>
						</table>

						<div class="col-sm-12">
							<span class="table-invalid-feedback text-red" role="alert" id="table_emp_detailError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
			</div>
		   
		</div>
		
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	
	id_talent = {!! $global_talent !!};
	$('#export_talent').attr('id-talent', id_talent);
	$('#import_talent').attr('id-talent', id_talent);
		
	$('#rec_type').prepend('<option selected></option>').select2({
		data: rec_type,
		placeholder: "Select Type ...",
		allowClear: true,
	});

	$('#status').empty().select2({
		data: [
			{ id: 'A', text: 'Active' },
			{ id: 'I', text: 'Inactive' },
		],
		allowClear: false
	});
	
	
	$('#period_date').attr('disabled',true);
	$('#period_date').parent().children('span').children('button').attr('disabled', true);
			
		get_employee_by();
		get_projected();
		get_grade();
		get_source_pos();
		get_region();
		get_branch(null).then(function(res) {
			$('#source_branch').select2({
				data: res,
			});
		});
		
		get_fpk();
		get_survey();
		get_conclusion();		
	if(id_talent != 0){
	//	console.log(id_talent);
		get_edit(id_talent);
	}

});	

function second_close(){
	$('.modal-backdrop').css('z-index','1049');
    $('#myModal').css('overflow', 'none');
//	$("#modal_second").removeClass("modal-backdrop fade show");
}

$(document).on('click', '.browse_talent', function (e) {
	$("#contentList").html('');
	$("#modal_second").addClass("modal-backdrop fade show");
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
	$("select").removeClass("custom-select");
	$("input").removeClass("is-invalid");
	global_period_date = $('#period_date').val();
		$.ajax({
			url: "<?= url('talent_management/talent_development/talent_recomendation/get_validate') ?>",
			type: 'GET',
			headers: {
				Accept: "application/json",
			},
			data: {
			//	projected_pos: global_projected,
				rec_type: global_type,
				period_date: global_period_date,
				source_pos: global_source,
				fpk: global_fpk,
				survey: global_survey,
			},		
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function (response) {
				if (response.status == 'true') {
					 $.ajax({
							url: "{{ route('talent_reco.modal_list') }}",
							data:{
								global_period_date: global_period_date,
								global_source: global_source,
								global_source_grade: global_source_grade,
								global_source_region: global_source_region,
								global_source_branch: global_source_branch,
								global_fpk: global_fpk,
								global_survey: global_survey
							},
							success: function(result){
						//    $('.modal-backdrop').css('z-index','1053');		
							$("#contentList").html(result);
							$("#modal_browse").modal('show'); 
							
						}
					});
				}
				else {
					$('#loader').addClass('hidden');
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong!',
					});
				}
				
			},
			complete: function(){
			//	$('#loader').addClass('hidden');
				$("#modal_second").removeClass("modal-backdrop fade show");
			},		
			error: function (response) {
				$('#loader').addClass('hidden');
				$("#modal_second").removeClass("modal-backdrop fade show");
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("select[id='" + key + "']").addClass("custom-select");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
				});
				
			}
		});	

}); 

function get_edit(id_talent) {
	$.ajax({
		url: "<?= url('talent_management/talent_development/talent_recomendation/get_talent_edit') ?>",
		method: "GET",
		data: {id_talent: id_talent},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			global_id_emp_detail = 0;
			$.each(response.emp, function (i, item) {
				$('#new_emp_detail').trigger('click');								
			});		
		//	console.log(response);	
			setTimeout(function () {				
				$('#id_talent_recommendation_header').val(response.id_talent_recommendation_header).trigger('change');
				$('#id_employee_request').val(response.id_employee_request).trigger('change');
				$('#projected_pos').val(response.id_position_routing).trigger('change');
				$('#rec_type').val(response.type).trigger('change');
				$('#period_date').val(response.period_date).trigger('change');
				$('#survey').val(response.id_survey_header).trigger('change',[true]);
				$('#fpk').val(response.id_hiring_request_header).trigger('change',[true]);
				$('#notes').val(response.notes).trigger('change');
				$('#status').val(response.status).trigger('change');
				
				$('#table_emp_body tr').each(function (index) {
					$(this).find('span.sn').html(index + 1);

					if(!response.emp[index].id_user_assessment || !response.emp[index].id_batch_assessment) {
						$(this).find('.btn-generate-psychogram').css('display', 'none');
					}
					$(this).find('.btn-generate-psychogram').attr('source', response.emp[index].source_assessment);
					$(this).find('.btn-generate-psychogram').attr('id-employee', response.emp[index].id_user_assessment);
					$(this).find('.btn-generate-psychogram').attr('id-candidate', response.emp[index].id_user_assessment);
					$(this).find('.btn-generate-psychogram').attr('id-batch', response.emp[index].id_batch_assessment);
					$(this).find('.batch_name_input').html(response.emp[index].batch_name);

					$(this).find('.id_talent_recommendation_detail_input').val(response.emp[index].id_talent_recommendation_detail).trigger('change');
				//	$(this).find('.id_employee_input').val(response.emp[index].id_employee).trigger('change');
					$(this).find('.id_employee_input').attr('id_employee',response.emp[index].id_employee);
					$(this).find('.nik_employee_input').html(response.emp[index].nik_employee);
					$(this).find('.name_employee_input').html(response.emp[index].name_employee);
					$(this).find('.position_route_input').html(response.emp[index].position_route);
					$(this).find('.job_grade_input').html(response.emp[index].job_grade);
					$(this).find('.branch_input').html(response.emp[index].branch);
					$(this).find('.final_rating_input').html(response.emp[index].final_rating);
					$(this).find('.name_kpi_desc_input').html(response.emp[index].kpi_desc);
					$(this).find('.psych_id_dept').val(response.emp[index].id_dept_psychogram);
					$(this).find('.psych_id_job_grade').val(response.emp[index].id_job_grade_psychogram);
					if(response.emp[index].psychogram_dept) {
						$(this).find('.psych_dept_input').text(`${response.emp[index].psychogram_dept} (${response.emp[index].psychogram_jobgrade})`);
					}
					$(this).find('.psych_dateassign_input').text(response.emp[index].batch_assign_date);
					if(response.emp[index].kpi_average != null ){
						$(this).find('.kpi_average_input').html(parseFloat(response.emp[index].kpi_average).toFixed(2));
					}
					else{
						$(this).find('.kpi_average_input').html('');
					}
					$(this).find('.competencies_input').prepend('<option selected></option>').select2({
						placeholder: "Select Competencies ...",
						data: global_competencies,
						allowClear: true
					});
					$(this).find('.competencies_input').val(response.emp[index].competencies).trigger('change');
					$(this).find('.id_potencies_input').val(response.emp[index].id_potencies);
					
					if(response.emp[index].potencies == 'Not Recommended'){
						$(this).find('.potencies_input').html(response.emp[index].potencies).addClass("badge-danger");
					}
					else if(response.emp[index].potencies == 'Considered'){
						$(this).find('.potencies_input').html(response.emp[index].potencies).addClass("badge-warning").css('color','white');
					}
					else if(response.emp[index].potencies == 'Recommended'){
						$(this).find('.potencies_input').html(response.emp[index].potencies).addClass("badge-success");
					}
					else{
						$(this).find('.potencies_input').html(response.emp[index].potencies);
					}
					if(response.emp[index].sp == true){
						$(this).find('.sp_input').html('Yes').addClass("badge-danger");
					}
					else{
						$(this).find('.sp_input').html('No').addClass("badge-success");
					}
					
					if(response.emp[index].kpk == true){
						$(this).find('.kpk_input').html('Yes').addClass("badge-danger");
					}
					else{
						$(this).find('.kpk_input').html('No').addClass("badge-success");
					}
					
					$(this).find('.eng_level_input').html(response.emp[index].eng_level);
					$(this).find('.batch_name_input').html(response.emp[index].batch_name);
					
				 });
			 }, 1000);
		},
		complete: function(){
			$('#loader').addClass('hidden');
			setTimeout(function () {
				$('#rec_type').attr('readonly',true);
				$('#period_date').attr('disabled',true);
				$('#period_date').parent().children('span').children('button').attr('disabled', true);
			}, 2000);
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
};

</script>