<div class="row">
		<div class="col-md-6">                       
			<div class="row">
				<label class="col-sm-4 col-form-label">Batch Name</label>
				<div class="col-sm-8">
					<input type="hidden" id="id_summary_batch" name="id_summary_batch">
					<input type="text" id="batch_name" name="batch_name" class="form-control form-control-sm">
					<span class="invalid-feedback" style="font-size:11px;color:#dc3545;" role="alert" id="batch_nameError">
						<strong></strong>
					</span>
				</div>
			</div>							
			
			<div class="row">
				<label class="col-sm-4 col-form-label">Start Date</label>
				<div class="col-sm-8">
					<input name="start_date" id="start_date" class="form-control form-control-sm">
					<span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="start_dateError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">End Date</label>
				<div class="col-sm-8">
					<input name="end_date" id="end_date" class="form-control form-control-sm">
					<span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="end_dateError">
						<strong></strong>
					</span>
				</div>
			</div>
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">Location</label>
				<div class="col-sm-8">
					<input type="text" name="location" id="location" class="form-control form-control-sm">
					<span class="invalid-feedback" role="alert" id="locationError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Access Branch</label>
				<div class="col-sm-8">
				   <select name="id_branch" id="id_branch" class="form-control form-control-sm select2" style="width: 100%;">
					</select>
					<span class="invalid-feedback" role="alert" id="id_branchError">
						<strong></strong>
					</span>
				</div>
			</div>
									
		</div>							
	</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#start_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});	
		$('#end_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});	
		getBranch().then(function(res) {
			$('#id_branch').prepend('<option selected></option>').select2({
				data: res,
				placeholder: "Select Access Branch ...",
				allowClear: true,
			});
		});
						
});	

</script>