<div class="row">
	<div class="col-md-12">
		<div class="row">
			<label class="col-sm-2">Activity </label>
			<label>:</label>
			<div class="col-sm-9">
				<div id="act_text"></div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2">Evidence </label>
			<label>:</label>
			<div class="col-sm-9">
				<div id="evi_text"></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<table style="width:100%;font-size:14px;" id="table_doc" class="table table-striped table-bordered table-hover datatable">
			<thead>	
				<tr class="text text-center">
					<th data-priority="1">No</th>
					<th data-priority="2">Document</th>
					<th data-priority="4" >Note</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	id_task_activity_answer = {!! $id_task_activity_answer !!};
	type = '{!! $type !!}';
	score = '{!! $score !!}';
	
	get_score(id_task_activity_answer).then(function(res) {
		$('#score').val(score).trigger('change');
	});
	get_doc(id_task_activity_answer,type);
	$("#table_doc_processing").css("background","white");
	$("#table_doc_processing").css("color","black");
	
	$('#id_task_activity_answer').val(id_task_activity_answer);
});

function get_doc(id_task_activity_answer) {
	let myData = {
		id_task_activity_answer: id_task_activity_answer,
	};
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_doc').DataTable({	
		processing: true,
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('task_management/missions/missions_review/get_doc') ?>",
			data : myData,
			error: function (jqXHR, textStatus, errorThrown) {
				$('#table_doc').DataTable().ajax.reload();
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center'},
			{data : "attachment", name: 'attachment' , className: 'text-center',
				render: function ( data, type, row ) { 
					let img_path = "<?= url('project/storage/app/public/task/document/') ?>";
					let img_folder  = row.nik_employee;
					let img_file  = data;
					
					show_in = `<a href="${img_path}/${img_folder}/${img_file}" target="_blank"><b>Download</b></a>`;
					return show_in;
				} 
			},
			{data : "notes", name: 'notes'},
		]
	});
}

</script>