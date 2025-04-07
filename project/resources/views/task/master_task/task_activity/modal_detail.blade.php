<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Description</label>
						<div class="col-sm-8">
							<input type="hidden" name="id_task" id="id_task">
							<input autocomplete="off" name="desc" id="desc" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-feedback d-block" role="alert" id="descError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Notes</label>
						<div class="col-sm-8">
							<input autocomplete="off" name="note" id="note" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-feedback d-block" role="alert" id="noteError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<div class="is-loading">
								<select name="id_dept" id="id_dept" class="form-control form-control-sm select2" data-placeholder="Select Department ..." style="width: 100%;">
								</select>
								<span class="invalid-feedback" role="alert" id="id_deptError">
									<strong></strong>
								</span>
								<span id="load_id_dept" class="spinner-border spinner-border-sm" style="display:none;"></span>
							</div>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Grade</label>
						<div class="col-sm-8">
							<div class="is-loading">
								<select name="id_grade" id="id_grade" class="form-control form-control-sm select2" data-placeholder="Select Grade ..." style="width: 100%;">
								</select>
								<span class="invalid-feedback" role="alert" id="id_gradeError">
									<strong></strong>
								</span>
								<span id="load_id_grade" class="spinner-border spinner-border-sm" style="display:none;"></span>
							</div>
						</div>
					</div>
					
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Task Type</label>
						<div class="col-sm-8">
							<select name="task_type" id="task_type" class="form-control form-control-sm select2" data-placeholder="Select Type ..." style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="task_typeError">
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
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Activity <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail"><span class="fas fa-plus"></span> Add Activity</button>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_detail" style="width:2600px" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:13px;">
												<th style="white-space:nowrap;">No.</th>
												<th style="width:280px;">Activity</th>
												<th style="width:60px;">Seq.</th>
												<th style="width:150px;">Notes</th>
												<th style="width:250px;">Evidence</th>
												<th>Type</th>
												<th>Task Cycle</th>
												<th style="width:120px;">Start Time</th>
												<th style="width:120px;">End Time</th>
												<th>Multi Attach</th>
												<th>Link Question Task</th>
												<th>Photo Question Task</th>
												<th>Random Object</th>
												<th>Master Answer</th>
												<th>Answer Type</th>
												<th>Min. Range</th>
												<th>Max. Range</th>
												<th>Dic. Correct Answer</th>
												<th style="width:50px;">Score</th>
												<th>Status</th>
												<th style="width:80px;">Action</th>
											</tr>
										</thead>
										<tbody id="table_rec_body">
										</tbody>
									</table>
									<div class="col-sm-12">
										<span class="table-invalid-feedback text-red" role="alert" id="table_rec_detailError">
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
	</div>
</div>
<script type="text/javascript">

$(document).ready(function(){
	id_task = {!! $global_task !!};
	
	get_dept();
	$('#id_dept').prepend('<option></option>').select2();
	$('#id_grade').prepend('<option></option>').select2();
	if(id_task != 0){
		get_edit(id_task);
	}
	
	$('#task_type').prepend('<option></option>').select2({
		data: global_task_type,
		allowClear: true,
	});

	$('#select2status').select2({width:'100%'});	
});	

function second_close(){
	$('.modal-backdrop').css('z-index','1049');
    $('#myModal').css('overflow', 'none');
//	$("#modal_second").removeClass("modal-backdrop fade show");
}

function get_edit(id_task) {
	$.ajax({
		url: "<?= url('task_management/missions/master_task/get_edit') ?>",
		method: "GET",
		data: {
			id_task: id_task,
		},
		success: function (response) {
			$('#id_task').val(response.id_task).trigger('change');
			$('#desc').val(response.description).trigger('change');
			$('#note').val(response.notes).trigger('change');
			$('#task_type').val(response.task_type).trigger('change');
			$('#select2status').val(response.status).trigger('change');
									
			get_dept().then(function(res) {
				$('#id_dept').val(response.id_department).trigger('change');
				get_grade(response.id_department).then(function(res) {
					$('#id_grade').val(response.job_class_group).trigger('change');
				});
			});
			
				global_id_rec_detail = 0;
				$.each(response.res, function (i, item) {
					$('#new_rec_detail').trigger('click');								
				});			

				setTimeout(function () {		
					$('#table_rec_body tr').each(function (index) {
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_activity_input').val(response.res[index].id_activity).trigger('change');
						$(this).find('.activity_input').val(response.res[index].activity).trigger('change');
						$(this).find('.seq_input').val(response.res[index].sequence).trigger('change');
						$(this).find('.notes_input').val(response.res[index].notes).trigger('change');
						$(this).find('.evidence_input').val(response.res[index].target_evidence).trigger('change');
						$(this).find('.evidence_type_input').val(response.res[index].evidence_type).trigger('change');
						$(this).find('.task_cycle_input').val(response.res[index].task_cycle).trigger('change');
						$(this).find('.start_time_input').val(response.res[index].start_time).trigger('change');
						$(this).find('.end_time_input').val(response.res[index].end_time).trigger('change');
						if (response.res[index].multiple_attachment_flag == true) {
						  $(this).find('.multi_attach_input').prop('checked',true);
						}else{
						  $(this).find('.multi_attach_input').prop('checked',false);
						}
						$(this).find('.link_question_input').val(response.res[index].link_question_task).trigger('change');
						$(this).find('.photo_question_input').val(response.res[index].photo_question_task).trigger('change');
						if (response.res[index].random_object_flag == true) {
						  $(this).find('.random_object_input').prop('checked',true);
						}else{
						  $(this).find('.random_object_input').prop('checked',false);
						}
						if (response.res[index].master_answer_flag == true) {
						  $(this).find('.ans_flag_input').prop('checked',true);
						}else{
						  $(this).find('.ans_flag_input').prop('checked',false);
						}
						$(this).find('.ans_type_input').val(response.res[index].answer_type).trigger('change');
						$(this).find('.min_range_input').val(response.res[index].minimum_range).trigger('change');
						$(this).find('.max_range_input').val(response.res[index].maximum_range).trigger('change');
						$(this).find('.dic_correct_input').val(response.res[index].dictionary_correct_answer).trigger('change');
						$(this).find('.max_score_input').val(response.res[index].maximum_score).trigger('change');
						if (response.res[index].status == 'A') {
						  $(this).find('.status_input').prop('checked',true);
						}else{
						  $(this).find('.status_input').prop('checked',false);
						}
					});	
				}, 500);	

		},
		complete: function(){
		//	$('#loader').addClass('hidden');
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