<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Box Name</label>
						<div class="col-sm-8">
							<input type="hidden" name="id_talent_matrix" id="id_talent_matrix">
							<input type="text" name="name_talent" id="name_talent" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="name_talentError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Matrix Group</label>
						<div class="col-sm-8">
							<select name="group_matrix" id="group_matrix" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="group_matrixError">
								<strong></strong>
							</span>                                    
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Job Grade</label>
						<div class="col-sm-8">
							<select name="job_grade" id="job_grade" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="job_gradeError">
								<strong></strong>
							</span>                                    
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Rating</label>
						<div class="col-sm-8">
							<select name="rating" id="rating" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="ratingError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Readiness</label>
						<div class="col-sm-8">
							<input type="text" name="readyness" id="readyness" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="readynessError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">KPI Min</label>
						<div class="col-sm-8">
							<input type="text" name="kpi_value_min" id="kpi_value_min" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="kpi_value_minError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">KPI Max</label>
						<div class="col-sm-8">
							<input type="text" name="kpi_value_max" id="kpi_value_max" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="kpi_value_maxError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Potencies</label>
						<div class="col-sm-8">
							<select name="psychogram" id="psychogram" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="psychogramError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Competencies</label>
						<div class="col-sm-8">
							<select name="bei" id="bei" class="form-control form-control-sm select2" style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="beiError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Color</label>
						<div class="col-sm-8">
							<input type="text" name="color" id="color" class="form-control form-control-sm">
							<span class="invalid-feedback" role="alert" id="colorError">
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
	
	$('#rating').select2().attr('readonly',true);
	
//	get_rating();	
	if(id_talent != 0){
		get_edit(id_talent);
	}
	else{
		get_grade();
		get_group_matrix();
		get_conclusion();
	}
});	

function get_edit(id_talent) {
	$.ajax({
		url: "<?= url('talent_management/master_talent_setting/master_talent_rating/get_talent_edit') ?>",
		method: "GET",
		data: {id_talent: id_talent},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			$('#id_talent_matrix').val(response.id_talent_matrix).trigger('change');
			$('#name_talent').val(response.name).trigger('change');
			get_group_matrix().then(function(res) {
				$('#group_matrix').val(response.id_group_matrix).trigger('change'); 
			});
			get_grade().then(function(res) {
			//	$('#rating').val(response.id_grade_promotion).trigger('change');
				$('#job_grade').val(response.id_job_grade).trigger('change',[true]);
				get_rating(response.id_job_grade).then(function(res) {
					$('#rating').val(response.id_grade_promotion).trigger('change');
				});
			});
			$('#kpi_value_min').val(response.kpi_value_min).trigger('change');
			$('#kpi_value_max').val(response.kpi_value_max).trigger('change');
			get_conclusion().then(function(res) {
				$('#psychogram').val(response.id_conclusion_psychotest).trigger('change');
				$('#bei').val(response.id_conclusion_assessment).trigger('change');
			});
			$('#readyness').val(response.readyness).trigger('change');
			$('#color').val(response.color).trigger('change');
		},
		complete: function(){
			$('#loader').addClass('hidden');
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