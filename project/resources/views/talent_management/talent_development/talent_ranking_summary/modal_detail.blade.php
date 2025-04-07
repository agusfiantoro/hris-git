<button id="adv_modal" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="view_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
					<th data-priority="12"></th>
					<th data-priority="1"></th>
					<th data-priority="2">No</th>
					<th data-priority="3">Employee Name</th>
					<th data-priority="4">Matrix Box</th>
					<th data-priority="5">Matrix Name</th>
					<th data-priority="6">KPI</th>
					<th data-priority="7">Rating</th>
					<th data-priority="8">Potencies</th>
					<th data-priority="9">Competencies</th>
					<th data-priority="10">Readiness</th>
					<th data-priority="13">Engagement Level</th>
					<th data-priority="11">Eligibilty Status</th>
				  </tr>
				 </thead>
				</table>
<script type="text/javascript">
$(document).ready(function(){
	code_box = '{!! $global_box !!}';
	param_fil_name = '{!! $fil_name !!}';
	param_fil_type = '{!! $fil_type !!}';
	param_fil_period = '{!! $fil_period !!}';
	param_fil_pro = '{!! $fil_pro !!}';
	param_fil_reg = '{!! $fil_reg !!}';
	param_fil_grade = '{!! $fil_grade !!}';
	param_fil_dept = '{!! $fil_dept !!}';
	param_fil_principal = '{!! $fil_principal !!}';

	extendDatatable().then(function() {
		get_box_matrix(code_box,param_fil_name,param_fil_type,param_fil_period,param_fil_pro,param_fil_reg,param_fil_grade,param_fil_dept,param_fil_principal);
	});	
	
	$("#view_table_processing").css("background","white");
	$("#view_table_processing i.fa-spinner").css("margin-top","100px");
	$("#view_table_processing").css("color","black");
});	
</script>