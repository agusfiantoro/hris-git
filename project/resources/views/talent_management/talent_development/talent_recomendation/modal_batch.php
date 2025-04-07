<div class="row">
		<div class="col-md-6">                       
			<div class="row">
				<label class="col-sm-4 col-form-label">Batch Name</label>
				<div class="col-sm-8">
					<input type="hidden" id="id_talent_recommendation_header_batch" name="id_talent_recommendation_header_batch">
					<input type="hidden" id="id_emp_batch" name="id_emp_batch">
					<input type="text" id="batch_name" name="batch_name" class="form-control form-control-sm" readonly>
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
<br>
<hr>
<br>
<table id="batch_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
 <thead>
  <tr>		
	<th style="z-index:5999;"></th>
	<th style="z-index:5999;">No</th>
	<th data-priority="2" style="z-index:5999;">NIK</th>
	<th data-priority="1" style="z-index:5999;">Name</th>
	<th data-priority="1" style="z-index:5999;">Position</th>
	<th data-priority="1" style="z-index:5999;">Grade</th>
	<th data-priority="1" style="z-index:5999;">Psychotest Status</th>
	<th data-priority="1" style="z-index:5999;">Batch Name</th>
  </tr>
 </thead>
</table>

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
			
	id_talent_header = <?php echo $global_talent_header; ?>;
	name_batch = <?php echo json_encode($global_name_batch); ?>;	
	
	$("#batch_name").val(name_batch).trigger('change');
			
	get_list_batch(id_talent_header);

	$("#batch_table_processing").css("background","white");
	$("#batch_table_processing i.fa-spinner").css("margin-top","100px");
	$("#batch_table_processing").css("color","black");

});	

</script>