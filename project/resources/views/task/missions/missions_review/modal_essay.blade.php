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
<br>
<div class="row">
	<label class="col-sm-2">Description </label>
	<label>:</label>
	<div class="col-sm-9">
		<div id="note_essay" align="justify"></div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	id_task_activity_answer = {!! $id_task_activity_answer !!};
	score = '{!! $score !!}';
	
	get_score(id_task_activity_answer).then(function(res) {
		$('#score').val(score).trigger('change');
	});
	get_essay(id_task_activity_answer);
	$('#id_task_activity_answer').val(id_task_activity_answer);
});	

function get_essay(id_task_activity_answer) {
	$.ajax({
		url: "<?= url('task_management/missions/missions_review/get_essay') ?>",
		method: "GET",
		data: {
			id_task_activity_answer: id_task_activity_answer,
		},
		success: function (response) {	
			if(response.length > 0){
				$('#note_essay').html(response[0].essay_answer);
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function (xhr) {
			swal({
				icon: 'error',
				title: 'Oops...',
				dangerMode: true,
				text: 'Something went wrong!'
			});
		}
	});
}
</script>