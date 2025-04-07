<div class="row">
		<div class="col-md-6">                       
			<div class="row">
				<label class="col-sm-4 col-form-label">Interview Type</label>
				<div class="col-sm-8">
					<input type="hidden" id="id_talent_recommendation_header_bei" name="id_talent_recommendation_header_bei">
					<input type="hidden" id="id_emp_bei" name="id_emp_bei">
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
<br>
<hr>
<table id="bei_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
 <thead>
  <tr>		
	<th style="z-index:5999;"></th>
	<th style="z-index:5999;">No</th>
	<th style="z-index:5999;">NIK</th>
	<th style="z-index:5999;">Name</th>
	<th style="z-index:5999;">Position</th>
	<th style="z-index:5999;">Grade</th>
	<th style="z-index:5999;">Conclusion</th>
  </tr>
 </thead>
</table>

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
		
	id_talent_header = <?php echo $global_talent_header; ?>;
	
//	console.log(id_talent_header);		
	get_list_bei(id_talent_header);

	$("#bei_table_processing").css("background","white");
	$("#bei_table_processing i.fa-spinner").css("margin-top","100px");
	$("#bei_table_processing").css("color","black");

});	

</script>