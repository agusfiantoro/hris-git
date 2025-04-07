<div class="row" style="margin-bottom:10px;">
	<label class="col-sm-4 col-form-label">Upload File :</label>
	<div class="col-md-8">
		<div class="custom-file">
			<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
			<input type="file" name="attachment" class="custom-file-input" id="attachment">
			<label class="custom-file-label" for="customFile" style="font-size:13px;"><i>Select File</i></label>	
			<div style="font-size:12px;margin-top:-2px;font-family:arial;"><i>Excel / PDF (Max 2 Mb)</i></div>
			<span class="invalid-feedback" role="alert" id="attachmentError">
				<strong></strong>
			</span>
		</div>
	</div>
</div>
<div class="row">
	<label class="col-sm-4 col-form-label">Description</label>
	<div class="col-sm-8">
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
	
	bsCustomFileInput.init();
});
</script>	