<div style="margin-bottom:20px;">
	<div class="row" id="first">
		<label class="col-sm-4 col-form-label">Take Your Photo</label>
		<div class="col-md-8">
			<input type="hidden" name="id_activity" id="id_activity">
			<button type="button" onClick="showCam()" class="btn btn-md btn-success" ><i class="fa fa-camera fa-lg"></i></button>
			<br/>		
		</div>        
	</div>
	<div class="row" id="second" style="display:none;">
		<div class="col-md-12" align="center">
			<div id="my_camera" style="margin-bottom:20px;"></div>
			<input type="button" value="Take Photo" class="btn btn-md btn-success" onClick="take_snapshot()" style="margin-top:0px;">
			<input type="hidden" name="image_photo" class="image-tag">
		</div>		
	</div>
	<div class="row" id="three" style="display:none;margin-top:10px;">
		<div class="col-md-12" align="center">
			<div id="results" align="center"></div>
		</div>		
	</div>
	<div class="row">
		<div class="col-md-12" align="center">
			<div id="image_photo"></div>
			<span class="invalid-feedback" role="alert" id="image_photoError">
				<strong></strong>
			</span>	
		</div>
	</div>
</div>
<div class="row" style="padding-right:40px;">
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
	id_activity = {!! $id_activity !!};
	$('#id_activity').val(id_activity);
});	
</script>	