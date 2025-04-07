<div class="row">
	<div class="col-md-6">                       
		<div class="row">
			<label class="col-sm-4 col-form-label">Interview Type</label>
			<div class="col-sm-8">
				<input type="hidden" id="id_summary_bei" name="id_summary_bei">
				<select name="int_type" id="int_type" class="form-control form-control-sm select2" style="width: 100%;">
				</select>
				<span class="invalid-feedback" role="alert" id="int_typeError">
					<strong></strong>
				</span>
			</div>
		</div>							
	</div>
	<div class="col-md-6">
		<div class="row">
			<label class="col-sm-4 col-form-label">Interview Date</label>
			<div class="col-sm-8">
				<input name="int_date" id="int_date" class="form-control form-control-sm">
				<span class="invalid-date" style="font-size:11px;color:#dc3545;" role="alert" id="int_dateError">
					<strong></strong>
				</span>
			</div>
		</div>	
	</div>							
</div>

<script type="text/javascript">
$(document).ready(function(){
	$('#int_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	
	getTypeBei().then(function(res) {
		$('#int_type').prepend('<option selected></option>').select2({
			data: res,
			placeholder: "Select Interview Type ...",
			allowClear: true,
		});
	});
		
});	

</script>