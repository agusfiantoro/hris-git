<div class="row">
	<label class="col-sm-4 col-form-label">Your Location</label>
	<div class="col-md-8">
		<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
		<button type="button" onClick="drawMap()" class="btn btn-md btn-success" ><i class="fas fa-map-marked-alt fa-lg"></i></button>		
	</div>
	<div id="showMap" class="col-md-12" align="center" style="display:none;margin-top:20px;">
		<div id="mapContainer" style="width:400px;height:250px;"></div>
		<center>
			<span class="gps text-red">Please Allow Location / Enable GPS</span><br>
			<button type="button" id="btnLocation" onClick="showLocation()" class="btn btn-primary" disabled>Confirm Location</button>
			<button id="btnLocation_loading" class="btn btn-primary" style="display:none;">
				<i class="fa fa-spinner fa-pulse"></i>
			</button>
		</center>
	</div>
	<div id="second" class="col-md-12" align="center" style="display:none;">
		<div id="my_camera"></div>
		<input type=button value="Take Photo" class="btn btn-md btn-success" onClick="take_snapshot()" style="margin-top:10px;">
		<input type="hidden" name="image_photo" class="image-tag">
	</div>		
	<div id="three" class="col-md-12" align="center" style="display:none;margin-top:10px;">
		<div id="results" align="center"></div>
	</div>
</div>
<div class="row" style="margin-bottom:20px;">
	<div class="col-md-12" align="center">
		<div id="image_photo"></div>
		<span class="invalid-feedback" role="alert" id="image_photoError" style="display:none;">
			<strong></strong>
		</span>
		<div id="longlat"></div>
		<span class="invalid-feedback" role="alert" id="longlatError">
			<strong></strong>
		</span>	
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
	
	mapboxAccessToken = '{!! $mapboxToken !!}';
	zone                = "<?= @$attendance[0]->schedule_employee_timezone ?>".replace(/\s/g, '');
	lock_location       = "<?= @$attendance[0]->lock_gps_location==true ? 1 : 0 ?>";
	map_timezone        = convertZone(zone);
	location_name		= "<?= @$attendance[0]->address_location ?? 'BORWITA'; ?>";
	lat_work = "<?= @$attendance[0]->lat_loc ?? ''; ?>";
	lng_work = "<?= @$attendance[0]->lng_loc ?? ''; ?>";
});
</script>