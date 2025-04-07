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
		<table style="width:100%;font-size:14px;" id="table_photo" class="table table-striped table-bordered table-hover datatable">
			<thead>	
				<tr class="text text-center">
					<th data-priority="1">No</th>
					<th data-priority="2">Photo</th>
					<th data-priority="5">Longitude</th>
					<th data-priority="6">Latitude</th>
					<th data-priority="3">Address</th>
					<th data-priority="4">Note</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<!-- The Modal -->
<div id="myShow" class="modal_show">
	<img class="modal-content-show" id="img01">
	<div id="caption"></div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	id_task_activity_answer = {!! $id_task_activity_answer !!};
	type = '{!! $type !!}';
	score = '{!! $score !!}';
	
	get_score(id_task_activity_answer).then(function(res) {
		$('#score').val(score).trigger('change');
	});
	get_photo(id_task_activity_answer,type);
	$("#table_photo_processing").css("background","white");
	$("#table_photo_processing").css("color","black");
	
	$('#id_task_activity_answer').val(id_task_activity_answer);
});

function get_photo(id_task_activity_answer) {
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
	
	$('#table_photo').DataTable({	
		processing: true,
		destroy:true,
		responsive: true,
		ajax: {
			url: "<?= url('task_management/missions/missions_review/get_photo') ?>",
			data : myData,
			error: function (jqXHR, textStatus, errorThrown) {
				$('#table_photo').DataTable().ajax.reload();
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center'},
			{data : "attachment", name: 'attachment' , className: 'text-center',
				render: function ( data, type, row ) { 
					let img_path = "<?= url('project/storage/app/public/task/photo/') ?>";
					let img_folder  = row.nik_employee;
					let img_file  = data;
					let created_date  = row.creation_date;
					
					show_in = `<img id="myImg" onclick="showImage('${img_path}/${img_folder}/${img_file}')" src="${img_path}/${img_folder}/${img_file}" style="width:150px;" /><br>${created_date}`;
					return show_in;
				} 
			},
			{data : "longitude", name: 'longitude', className: 'text-center'},
			{data : "latitude", name: 'latitude', className: 'text-center'},
			{data : "address_description", name: 'address_description', className: 'text-center'},
			{data : "notes", name: 'notes'},
		]
	});
	
}

function showImage(imagePath) {
    $('#myShow').modal('show');
    $('#myShow img').attr('src', imagePath);
}
</script>