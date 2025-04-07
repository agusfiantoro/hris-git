<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Description</label>
						<div class="col-sm-8">
							<input type="hidden" name="id_task_management" id="id_task_management">
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
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<div class="is-loading">
								<select name="id_pos[]" id="id_pos" class="form-control form-control-sm select2" data-placeholder="Select Position ..." style="width: 100%;" multiple="multiple">
								</select>
								<!-- select name="id_pos" id="id_pos" class="form-control form-control-sm select2" data-placeholder="Select Position ..." style="width: 100%;">
								</select -->
								<span class="invalid-feedback" role="alert" id="id_posError">
									<strong></strong>
								</span>
								<span id="load_id_pos" class="spinner-border spinner-border-sm" style="display:none;"></span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Period Date</label>
						<div class="col-sm-3" style="flex: 0 0 29%;max-width: 29%;">
							<input autocomplete="off" id="start_date" name="start_date" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-date" style="font-size:10px;color:#dc3545;font-weight:bold;" role="alert" id="start_dateError">
								<div></div>
							</span>
						</div>
						<label class="col-sm-1 col-form-label" style="text-align:center;">-</label>
						<div class="col-sm-3" style="flex: 0 0 29%;max-width: 29%;">
							<input autocomplete="off" id="end_date" name="end_date" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-date" style="font-size:10px;color:#dc3545;font-weight:bold;" role="alert" id="end_dateError">
								<div></div>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Responsible By</label>
						<div class="col-sm-8">
							<select name="id_respon" id="id_respon" class="form-control form-control-sm select2" data-placeholder="Select Employee ..." style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="id_responError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Managed By</label>
						<div class="col-sm-8">
							<select name="id_managed" id="id_managed" class="form-control form-control-sm select2" style="width: 100%;" readonly>
							</select>
							<span class="invalid-feedback" role="alert" id="id_managedError">
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
					<div class="row" style="margin-top:20px;">
						<label class="col-sm-4 col-form-label"></label>
						<div class="col-sm-8">
							<button type="button" class="browse_task pull-right btn btn-sm btn-success"><i class="fas fa-list-alt"></i> Browse Master Task</button>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Transactions <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail" style="display:none;"><span class="fas fa-plus"></span> Add Task</button>
									<button type="button" id="new_gen" class="new_gen pull-right btn btn-xs btn-success" style="margin-right:10px;"><i class="fa fa-refresh"></i> Generate</button>
								</div>
								<div class="col-md-12">
									<table id="table_rec_detail" style="width:100%" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:13px;">
												<th style="white-space:nowrap;">No.</th>
												<th style="width:180px;">Task</th>
												<th>Activity Status</th>
												<th style="width:180px;">Activity</th>
												<th style="width:180px;">Evidence</th>
												<th style="white-space:nowrap;">Type</th>
												<th>Task Cycle</th>
												<th>Task Time</th>
												<th style="width:50px;">Score</th>
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
	get_employee_by();
	$('#id_dept').prepend('<option></option>').select2();
	$('#id_grade').prepend('<option></option>').select2();
	$('#id_pos').prepend('<option></option>').select2();
	$('#id_respon').prepend('<option></option>').select2();
	if(id_task != 0){
		get_edit(id_task);
	}

	$('#select2status').select2({width:'100%'});	
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
		});
	});	
	
	$('#end_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
		
});	

function second_close(){
	$('.modal-backdrop').css('z-index','1049');
    $('#myModal').css('overflow', 'none');
//	$("#modal_second").removeClass("modal-backdrop fade show");
}

$(document).on('click', '.browse_task', function (e) {
	$("#contentList").html('');
	$("#modal_second").addClass("modal-backdrop fade show");
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".invalid-date").children("div").text("");
	$("#taskForm input").removeClass("is-invalid");
	$("#taskForm select").removeClass("is-invalid");
	$("#taskForm textarea").removeClass("is-invalid");
	$(".table-invalid-feedback").children("strong").text(""); 
	$(".error-tab").html("");
		$.ajax({
			url: "<?= url('task_management/missions/task_assignment/get_validate') ?>",
			type: 'GET',
			headers: {
				Accept: "application/json",
			},
			data: {
			//	id_pos: global_id_position,
				id_dept: global_dept,
				id_grade: global_grade,
			},		
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function (response) {
				if (response.status == 'true') {
					 $.ajax({
							url: "{{ route('task.modal_list') }}",
							data:{
							//	global_id_position: global_id_position					
								global_dept: global_dept,
								global_grade: global_grade,
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

function get_edit(id_task) {
	$.ajax({
		url: "<?= url('task_management/missions/task_assignment/get_edit') ?>",
		method: "GET",
		data: {
			id_task_management: id_task,
		},
		success: function (response) {
			$('#new_gen').attr('onclick','genTask('+response.id_task_management+')');
			$('#id_task_management').val(response.id_task_management).trigger('change');
			$('#desc').val(response.description).trigger('change');
			$('#note').val(response.notes).trigger('change');						
			$('#start_date').val(response.start_date).trigger('change');
			$('#end_date').val(response.end_date).trigger('change');
			$('#id_respon').val(response.id_responsible_by).trigger('change');
			$('#id_managed').val(response.id_managed_by).trigger('change');
			$('#select2status').val(response.status).trigger('change');
									
			get_dept().then(function(res) {
				$('#id_dept').val(response.id_department).trigger('change');
				get_grade(response.id_department).then(function(res) {
					$('#id_grade').val(response.job_class_group).trigger('change');
					get_position(response.id_department,response.job_class_group).then(function(res) {
						$('#id_pos').val(response.id_position_routing).trigger('change');
						$('#loader').addClass('hidden');
					});
				});
			});
			
				global_id_rec_detail = 0;
				$.each(response.res, function (i, item) {
					$('#new_rec_detail').trigger('click');								
				});			

				setTimeout(function () {		
					$('#table_rec_body tr').each(function (index) {
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_task_activity_input').val(response.res[index].id_task_activity);
						$(this).find('.id_act_input').attr('id_activity',response.res[index].id_activity);
						$(this).find('.id_task_input').val(response.res[index].id_task).trigger('change');
						$(this).find('.task_text_input').html(response.res[index].task_text);
						$(this).find('.random_type_input').val(response.res[index].random_type).trigger('change');
						$(this).find('.id_activity_input').val(response.res[index].id_activity).trigger('change');
						$(this).find('.activity_text_input').html(response.res[index].activity_text);
						$(this).find('.evidence_input').html(response.res[index].evidence);
						$(this).find('.type_input').html(response.res[index].type);
						$(this).find('.cycle_input').html(response.res[index].cycle);
						$(this).find('.time_input').html(response.res[index].time);
						$(this).find('.score_input').html(response.res[index].score);
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