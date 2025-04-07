<div class="row">
	<label class="col-sm-4 col-form-label">Description</label>
	<div class="col-sm-8">
		<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
		<textarea class="form-control form-control-sm" id="desc" rows="3" name="desc"></textarea>
		<span class="invalid-feedback" role="alert" id="descError">
			<strong></strong>
		</span>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
	id_task_activity_answer = {!! $id_task_activity_answer !!};
	$('#id_task_activity_answer').val(id_task_activity_answer);
});	
</script>