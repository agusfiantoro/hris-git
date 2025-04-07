<button id="advanced_list" type="button" class="btn btn-default">Advanced Search</button>
<br>
<br>
<table id="list_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
 <thead>
  <tr>		
	<th style="z-index:5999;"></th>
	<th style="z-index:5999;">No</th>
	<th data-priority="2" style="z-index:5999;">NIK</th>
	<th data-priority="1" style="z-index:5999;">Name</th>
	<th>Position</th>
	<th>Grade</th>
	<th>Employment Status</th>
	<th>Period</th>
	<th data-priority="4">Average KPI</th>
	<th data-priority="3">Rating PA</th>
	<th data-priority="5">Potencies (Psychogram)</th>
	<th data-priority="6">Psychotest Date</th>
	<th>Psychogram Dept.</th>
	<th>Psychogram Job Grade</th>
	<th>Psychotest Source</th>
	<th data-priority="7">Potencies Status</th>
	<th>SP</th>
	<th>KPK</th>
	<th>Eligibility Status</th>
	<th>Engagement Level</th>
	<th>KPI 12 Month Ago</th>
	<th>KPI 11 Month Ago</th>
	<th>KPI 10 Month Ago</th>
	<th>KPI 9 Month Ago</th>
	<th>KPI 8 Month Ago</th>
	<th>KPI 7 Month Ago</th>
	<th>KPI 6 Month Ago</th>
	<th>KPI 5 Month Ago</th>
	<th>KPI 4 Month Ago</th>
	<th>KPI 3 Month Ago</th>
	<th>KPI 2 Month Ago</th>
	<th>KPI 1 Month Ago</th>
  </tr>
 </thead>
</table>

<script type="text/javascript">
$(document).ready(function(){
	period_date = {!! json_encode($global_period_date) !!}
	source_pos_list = {!! json_encode($global_source) !!}
	source_grade_list = {!! json_encode($global_source_grade) !!}
	source_region_list = {!! json_encode($global_source_region) !!}
	source_branch_list = {!! json_encode($global_source_branch) !!}
	fpk_list = {!! json_encode($global_fpk) !!}
	survey_list = {!! json_encode($global_survey) !!}
	
	get_datatable();
	$("#list_table_processing").css("background","white");
	$("#list_table_processing i.fa-spinner").css("margin-top","100px");
	$("#list_table_processing").css("color","black");
});	

</script>