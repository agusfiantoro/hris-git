<!-- button id="advanced_list" type="button" class="btn btn-default">Advanced Search</button -->
<table id="list_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
 <thead>
  <tr>		
	<th style="z-index:5999;"></th>
	<th style="z-index:5999;">No</th>
	<th data-priority="1" style="z-index:5999;">Task</th>
	<th>Remark</th>
	<th>Type</th>
  </tr>
 </thead>
</table>

<script type="text/javascript">
$(document).ready(function(){
	id_dept = {!! json_encode($global_dept) !!}
	id_grade = {!! json_encode($global_grade) !!}
	
	get_list_datatable();
	$("#list_table_processing").css("background","white");
	$("#list_table_processing i.fa-spinner").css("margin-top","100px");
	$("#list_table_processing").css("color","black");
});	

</script>