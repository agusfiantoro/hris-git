@extends('adminlte::page')
@section('title', 'Missions')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Missions</h5>
      </div>
      <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>	
			<br>
			<br>
			<table id="missions_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr>
				<th data-priority="1" width=10></th>
				<th data-priority="3" width=10>No</th>
				<th data-priority="5">Task</th>
				<th data-priority="4" width=150>Activity</th>
				<th data-priority="9" width=100>Detail Activity</th>
				<th data-priority="6" width=150>Evidence</th>
				<th data-priority="7">Type</th>
				<th data-priority="10" width=80>Start Date</th>
				<th data-priority="11" width=80>End Date</th>
				<!-- th data-priority="12">Multiple Attachment</th -->
				<th data-priority="8">Submit</th>
				<th data-priority="2" width=100 class="text text-center">Action</th>
			  </tr>
			</thead>
		  </table>
    </div>
  </div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-md">
	<div id="modal_second"></div>
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="uploadForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-success btn-sm" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</form>
    </div>
  </div>
</div>
<!-- div style="display:none;">
	<div id="sample_photo">
		<div style="margin-bottom:20px;">
			<div class="row first_input" id="first_0">
				<label class="col-sm-4 col-form-label">Take Your Photo</label>
				<div class="col-md-8">
					<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
					<button type="button" onClick="showCam(0)" class="btn btn-md btn-success showCam" ><i class="fa fa-camera fa-lg"></i></button>
					<br/>		
				</div>        
			</div>
			<div class="row second_input" id="second_0" style="display:none;">
				<div class="col-md-12" align="center">
					<div id="my_camera_0" class="my_camera" style="margin-bottom:20px;"></div>
					<input type="button" value="Take Photo" class="btn btn-md btn-success take_photo" onClick="take_snapshot(0)" style="margin-top:0px;">
					<input type="hidden" name="ph[0][image_photo]" class="image-tag">
				</div>		
			</div>
			<div class="row three_input" id="three_0" style="display:none;margin-top:10px;">
				<div class="col-md-12" align="center">
					<div id="results_0" class="res" align="center"></div>
				</div>		
			</div>
			<div class="row">
				<div class="col-md-12" align="center">
					<div id="ph_0_image_photo" class="image_photo_input"></div>
					<span class="invalid-feedback image_photo_input_error" role="alert" id="ph_0_image_photoError">
						<strong></strong>
					</span>	
				</div>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-4 col-form-label">Description</label>
			<div class="col-sm-8">
				<textarea class="form-control form-control-sm desc_input" id="ph_0_desc" rows="3" name="ph[0][desc]"></textarea>
				<span class="invalid-feedback desc_input_error" role="alert" id="ph_0_descError">
					<strong></strong>
				</span>
			</div>
		</div>
		<hr>
	</div>
</div -->
@endsection

@section('css')
<link href='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.css' rel='stylesheet' />
<style type="text/css">
  .modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 9999;
    visibility: hidden;
  }
  .modal{
		overflow:auto !important;
	}
  .modal-body {
    position: relative;
  }
  .modal.show .modal-loading {
    visibility: visible;
  }
  select[readonly].select2-hidden-accessible + .select2-container {
    pointer-events: none;
    touch-action: none;
  }
  select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
    background: #e8ebed;
    box-shadow: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
    display: none;
  }

 .is-invalid:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  
  .readonly-checkbox {
    pointer-events: none; /* Mencegah interaksi mouse */
    opacity: 0.5; /* Mengurangi kejelasan untuk memberikan efek tidak aktif */
  }
  
  td.text-center{
	text-align:center;
  }
  ul > li.text-center{
	text-align:left!important;
  }
  td.text-width{
	width:30px;
	text-align:center;
  }
  td.text-score{
	vertical-align:middle;
	text-align:center;
	font-size:16px;
	font-weight:bold;
 }
 li.text-score{
	vertical-align:middle;
	text-align:left;
	font-size:16px;
	font-weight:bold;
 }
 .swal-green {
	color:#00000;
	font-weight:bold;
 }
 
.dtfc-fixed-left{
	z-index:10;
}

td.dis{
	pointer-events:none;
}

table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before, table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before{
	left: 12px;
}
table.dataTable > tbody > tr.child span.dtr-title {
  width: 110px;
}

</style>
@endsection

@section('scripts')
<script src="{{ asset('vendor/bootstrap/js/id-id.js') }}"></script>
<script type="text/javascript" src="<?= asset('vendor/webcam/webcam.js') ?>"></script>
<script src='https://api.mapbox.com/mapbox-gl-js/v2.3.1/mapbox-gl.js'></script>
<script type="text/javascript">
let global_id_object_activity = null;
let global_type = null;
let global_multi_attach = null;
let global_next = null;

var img                 = null;
let lat                 = null;
let lng                 = null;
let v_lat               = null;
let v_lng               = null;
let zone		        = null;
let lock_location       = null;
let map_timezone        = null;
let mapboxAccessToken   = null;
let step_now            = 0;   
let map_place           = '';
let attendance_time     = '';
let server_hour_now     = 0;
let server_minute_now   = 0;
let getDistance         = 0;	 
let location_name 		= '';
let lat_work 			= 0;
let lng_work 			= 0;

/*
let global_id_photo = 0;
$(document).on('click', '#new_rec_photo', function (event) {
	var content = jQuery('#sample_photo'),
			size = global_id_photo++,
			element = null,
			element = content.clone();
	element.attr('id','rec_photo-'+size);
	element.find('.desc_input').attr('id', 'ph_' + size + '_desc');
	element.find('.desc_input').attr('name', 'ph[' + size + '][desc]'); 
	element.find('.desc_input_error').attr('id', 'ph_' + size + '_descError');
	
	element.find('.my_camera').attr('id', 'my_camera_'+size);
	element.find('.take_photo').attr('onClick','take_snapshot('+size+')');
	element.find('.image-tag').attr('name', 'ph[' + size + '][image_photo]'); 
	
	element.find('.res').attr('id', 'results_'+size);
	element.find('.image_photo_input').attr('id', 'ph_' + size + '_image_photo');
	element.find('.image_photo_input_error').attr('id', 'ph_' + size + '_image_photoError');
	
	element.find('.showCam').attr('onClick','showCam('+size+')');
	
	element.find('.first_input').attr('id', 'first_'+size);
	element.find('.second_input').attr('id', 'second_'+size);
	element.find('.three_input').attr('id', 'three_'+size);
	
	element.appendTo('#tbody_photo');
});
*/

function onSubmit(id_task_activity_answer) {	
	swal({
		title: 'Are you sure?',
        text: 'This record will be submit!',
		icon: 'warning',
		 buttons: {
			cancel: {
			  text: "No",
			  value: false,
			  visible: true,
			  className: "",
			  closeModal: true,
			},
			confirm: {
			  text: "Yes",
			  value: true,
			  visible: true,
			  className: "",
			  closeModal: true
			}
		  }							 
	}).then(function(value) {
        if (value) {	 
			$.ajax({
				url: "{{ route('missions.submit_check') }}",
				method: "GET",
				data: {
					id_task_activity_answer: id_task_activity_answer,
				},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function(response) {
					if (response.status == 'true') {	
					   swal({
							icon: 'success',
							title: 'Success',
							text: response.message,
						});
						$('#missions_table').DataTable().ajax.reload();
					}
					else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: response.message
						});
					}
				},
				complete: function(){
					$('#loader').addClass('hidden');
				},
				error: function(response) {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: response.message,
					});
				}

			});
		}
    });
 }
 	
	
$(document).ready(function(){
	$('#missions_table').DataTable({
		responsive: true,
		processing: true,
		pageLength: 10,
	//	scrollX: true,
	//	scrollCollapse: true,
	//	fixedColumns: {
	//		right: 2,
	//		left: 3,
	//	},
		columnDefs:false,
		ajax: {
			url: "{{ route('missions.get_list') }}",
			data:{type: 'web'},
			error: function (jqXHR, textStatus, errorThrown) {
			  $('#missions_table').DataTable().ajax.reload();
			}
		  },	
		columns: [
		  {
			defaultContent: '',
			orderable: false, className: 'text-center'
		  },
		  { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center dis'},
		  { data: 'task', name: 'task' },
		  { data: 'activity', name: 'activity' },
		  { data: 'random_act', name: 'random_act', className: 'text-center', render: function ( data, type, row ) {
				if( data == null){
						return '-';
				}
				else {
						return data;
				}
			} 
		  },
		  { data: 'target_evidence', name: 'target_evidence' },
		  { data: 'evidence_type', name: 'evidence_type', className: 'text-center', render: function ( data, type, row ) {
				if( data == 'Photo'){
						return 'Photo (No Lock Location)';
				}
				else if( data == 'GPS'){
						return 'Photo (Lock Location)';
				}
				else {
						return data;
				}
			} 
		  },
		  { data: 'start_date', name: 'start_date' },
		  { data: 'end_date', name: 'end_date' },
		/*  { data: 'multiple_attachment_flag', name: 'multiple_attachment_flag', className: 'text-center', render: function ( data, type, row ) {	
				if(data == true){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Yes</span>';
				}
				else{
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">No</span>';
				}
			}
		  },
		*/
		  { data: 'status', name: 'status', className: 'text-center', render: function ( data, type, row ) {	
				if(data == true){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Yes</span>';
				}
				else{
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">No</span>';
				}
			}
		  },
		  { data: 'action', name: 'action', orderable: false, className: 'text-center' }
		],
		"fnInitComplete": function (oSettings) {
			$('#missions_table').find("tbody tr td.dtr-control").attr('onclick','det()');
			
		},
		rowCallback: function(row, data, index){
			if(data['status'] == false && data['completion_date'] == null){
				$(row).find('.btn-submit').css('display', 'none');
			}
			else if(data['status'] == true && data['completion_date'] != null ){
				$(row).find('.btn-answer').css('display', 'none');
				$(row).find('.btn-submit').css('display', 'none');
			}
			else{
				$(row).find('.btn-answer').show();
				$(row).find('.btn-submit').show();
			}
      },
	
    });
	
	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}		
	});
	
	
}); 

function det(){
	setTimeout(function () {
			$('#missions_table > tbody > tr.child > td.child > ul > li').addClass('row');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-title').addClass('col-4');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-data').addClass('col');
	}, 500);
}

$('#uploadForm').submit(function (e) {
	e.preventDefault();
//	let formData = $(this).serializeArray();
	var formData = new FormData(this);
	formData.append("type", global_type);
	if(global_type == 'Photo' || global_type == 'GPS'){
		if(lat != null && lng != null){		
			formData.append("longlat", true);
			formData.append("lat", lat);
			formData.append("lng", lng);
			formData.append("map_place", map_place);
			$('#image_photoError').show();
		}
		else{
			formData.append("longlat", '');
		}	
	}
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$("#uploadForm input").removeClass("is-invalid");
	$("#uploadForm textarea").removeClass("is-invalid");
	$("#uploadForm div").removeClass("is-invalid");
	$(".error-tab").html("");
		$.ajax({
			type: 'POST',
			headers: {
				'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			},
			enctype: 'multipart/form-data',
			cache: false,
			processData: false, // important
			contentType: false, // important
			url: "{{ route('missions.save') }}",
			data: formData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function (response) {
				if (response.status == 'true') {
					if(global_multi_attach == 'false'){
						$('#myModal').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#missions_table').DataTable().ajax.reload();
					}
					else{
						$('#myModal').modal('hide');
						swal({
							title: 'Next Photo ?',
							icon: 'warning',
							closeOnClickOutside: false,
							 buttons: {
								cancel: {
								  text: "No",
								  value: false,
								  visible: true,
								  className: "",
								  closeModal: true,
								},
								confirm: {
								  text: "Yes",
								  value: true,
								  visible: true,
								  className: "",
								  closeModal: true
								}
							  }							 
						}).then(function(value){
							if (value) {
								global_next = 1;
								loadedit(id_task_activity_answer,global_type,global_id_object_activity,global_multi_attach,global_next);
							}
							else{
								$('#missions_table').DataTable().ajax.reload();
							}
						});
					}
				} else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: `Something went wrong! [${response.message}]`
					});
				}
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (response) {
				if (response.status === 422) {
					let errors = response.responseJSON.errors;
					Object.keys(errors).forEach(function (key) {
						var key_temp = key.replaceAll(".", "_");
						$("#" + key_temp).addClass("is-invalid");
						$("#" + key_temp + "Error").children("strong").text(errors[key][0]);																
					});
				}
				else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [Unknown Error]'
					});
				}
			}
		});
	
});

function resCam(){
	if(window.matchMedia("(max-width: 767px)").matches){
		var width = 350;
		var res_width = 300;
		var height = 320;
		
		var dest_width = 1600;
		var dest_height = 1800;
		
	}
	else{
		var width = 320;
		var res_width = 320;
		var height = 250;
		
		var dest_width = 1800;
		var dest_height = 1600;
	}
	
	return {width,height,dest_width,dest_height,res_width}
}

function showCam() {
	
	$(".image-tag").val(null);
	Webcam.set({
		width: resCam().width,
		height: resCam().height,
		dest_width: resCam().dest_width,
		dest_height: resCam().dest_height,
		image_format: "jpeg",
		jpeg_quality: 90,
		force_flash: false,
		flip_horiz: false,
		fps: 45,
		constraints: {
			facingMode: "environment",
		}
	});
	Webcam.attach('#my_camera');
	Webcam.on('live', function () {
		$("#first").hide();
		$("#second").show();
		$("#three").show();
	});
}
	
function take_snapshot() {
	Webcam.snap(function(data_uri) {
		img = data_uri;
		$(".image-tag").val(data_uri);
		document.getElementById('results').innerHTML = '<img src="'+data_uri+'" width="'+resCam().res_width+'" height="'+resCam().height+'" />';
		$("#first").show();
		$("#second").hide();
		$("#three").show();
	});
	Webcam.reset();
}

function showLocation() {
	$("#btnLocation").hide();
	$("#btnLocation_loading").show();

	checkConnection().then(res => {
		if(res=='on'){
			lat = v_lat;
			lng = v_lng;
			getPlaceTimezone(lng, lat).then(res => {
				zone = res.timezone.properties.TZID;
				nextStep(1);
			})
		} 
	});
}

const checkConnection = async () => {
	const result = await window.navigator.onLine ? 'on' : 'off' ;
	return result;
}

const nextStep = (step_sekarang) => {
	checkConnection().then(res => {
		if(res=='on'){
			if(step_sekarang==0){ modeOnline(1) }
			if(step_sekarang==1){ modeOnline(2) }
		}
	});
}

const getPlaceTimezone = async (lng, lat) => {
	let result;
	let _token = "<?= csrf_token() ?>";
	try {
		try {
			result = await $.ajax({
				type: 'POST',
				url: "<?= url('/time_attendance/getPlaceTimezoneMapbox') ?>",
				dataType: 'json',
				headers: {'X-CSRF-TOKEN': _token},
				data: {
					lng: lng,
					lat: lat,
					zone_workdays: convertZone(zone)
				},
				success: function (resp) {
					if(resp.result == false){
						alert('Location can\'t read by server, please try again');
						location.reload();
					} 
					map_place = resp.place.place_name;
					map_timezone = resp.timezone;
				},
				error: function (jqXHR, exception) {
					map_timezone = map_timezone
					map_place = '';
				},
			});
			return result;
		} catch (error) {
			if(error.status == 419){
				swal({
					title: 'Please refresh page',
				}).then(function(){ 
					location.reload();
				});
			}
		}
	} catch (error) {
		getPlaceTimezone(lng, lat);
	}
}

function convertTimezone(time, zone) {
	let zone_ = zone.toLowerCase();
	let time_ = parseInt(time);
	let data = {
		'asia/jakarta' : time_,
		'asia/makassar' : time_ + 1,
		'asia/jayapura' : time_ + 2,
		'wib' : time_,
		'wita' : time_ + 1,
		'wit' : time_ + 2,
	}
	return data[zone_];
}

function convertZone(zone) {
	let zone_ = zone.toLowerCase();
	let data = {
		'asia/jakarta' : 'WIB',
		'asia/makassar' : 'WITA',
		'asia/jayapura' : 'WIT',
		'wib' : 'Asia/Jakarta',
		'wita' : 'Asia/Makassar',
		'wit' : 'Asia/Jayapura',
	}
	return data[zone_];
}

function modeOnline(step) {
	if(step == 1){
		step_now    = step;
		drawMap();
		$("#second").hide();
		$("#three").hide();
		$("#showMap").show();
	} else if(step == 2){
		step_now    = step;
		$("#results").html('');
		showCam();
		$("#second").show();
		$("#three").show();
		$("#showMap").hide();
		$("#btnLocation_loading").hide();
	} 
}

function loadedit(id_task_activity_answer,type,id_object_activity,multi_attach,global_next=null){
	$('#save_button').html('');
	global_multi_attach = multi_attach;
	global_id_object_activity = id_object_activity;
	global_type = type;
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#uploadForm input").removeClass("is-invalid");
	if(global_multi_attach == 'true'){
		$('#save_button').html('<i class="fas fa-save"></i> Next');
	}
	else{
		$('#save_button').html('<i class="fas fa-save"></i> Save');
	}
	if(type == 'Document'){
		$("#modal-title").html("Upload File");
	}
	else if(type == 'Photo'){
		$("#modal-title").html("Photo (No Lock Location)");
	}
	else if(type == 'Essay'){
		$("#modal-title").html("Essay");
	}
	else if(type == 'GPS'){
		$("#modal-title").html("Photo (Lock Location)");
	}
	else if(type == 'Video'){
		$("#modal-title").html("Upload Video");
	}
    $.ajax({
		url: "{{ route('missions.modal_upload') }}",
		data:{
			id_task_activity_answer:id_task_activity_answer,
			type:type,
			id_object_activity:id_object_activity,
			next:global_next,
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(result){
			if (result.status == 'true') {
				$("#contentBody").html(result.view);
				$("#myModal").modal('show'); 	
			}
			else {
				swal({
					icon: 'success',
					content: {
						element: "div",
						attributes: {
							innerText: result.message,
							className: "swal-green",
						},
					},
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});
}	

function getDistanceFromLatLon(lat1, lon1, lat2, lon2, unit) {
	if ((lat1 == lat2) && (lon1 == lon2)) {
		return 0;
	}
	else {
		var radlat1 = Math.PI * lat1/180;
		var radlat2 = Math.PI * lat2/180;
		var theta = lon1-lon2;
		var radtheta = Math.PI * theta/180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		if (dist > 1) {
			dist = 1;
		}
		dist = Math.acos(dist);
		dist = dist * 180/Math.PI;
		dist = dist * 60 * 1.1515;
		if (unit=="K") { dist = dist * 1.609344 } // Kilometer
		if (unit=="Meters") { dist = (dist * 1.609344) * 1000 } // Kilometer
		if (unit=="N") { dist = dist * 0.8684 }
		return dist; //default if unit == '' is miles
	}
}
		
function drawMap() {
	$("#showMap").show();
	$("#second").hide();
	$("#three").hide();
	$("#btnLocation").show();
	lng_borwita_pusat = 112.6963636;
	lat_borwita_pusat = -7.3537962;
	lng_borwita_pusat = '';
	lat_borwita_pusat = '';

	lng_marker = lng_borwita_pusat;
	lat_marker = lat_borwita_pusat;
	mapboxgl.accessToken = mapboxAccessToken;
	var map = new mapboxgl.Map({
		container: 'mapContainer',
		style: 'mapbox://styles/mapbox/streets-v11',
		center: [116.816356, -2.116069], // center adalah cakupan Indonesia
		zoom: 4,
		interactive: true,
	});
	var geolocate = new mapboxgl.GeolocateControl({
		positionOptions: {enableHighAccuracy: true},
		showAccuracyCircle: true,
	})

	geolocate.on('geolocate', function (userlocation) {
		v_lat = userlocation.coords.latitude;
		v_lng = userlocation.coords.longitude;
		radiusInMeters = 250;

		map.flyTo({center:[v_lng, v_lat], zoom:14 });  //set zoom

	//	let location_name = "<?= @$attendance[0]->address_location ?? 'BORWITA'; ?>";
	//	const lat_work = "<?= @$attendance[0]->lat_loc ?? ''; ?>";
	//	const lng_work = "<?= @$attendance[0]->lng_loc ?? ''; ?>";

		if(lat_work == '' || lng_work == ''){
			location_name = 'BORWITA HEAD OFFICE';
		}
		lat_marker = lat_work == '' ? lat_borwita_pusat : lat_work;
		lng_marker = lng_work == '' ? lng_borwita_pusat : lng_work;


		// ===== UNTUK MENAMPILKAN MARKER LOKASI BORWITA BY USER WORK LOCATION =====
		const marker_location = new mapboxgl.Marker({color:'#fe4909'})
			.setLngLat([lng_marker, lat_marker])
			.setPopup(new mapboxgl.Popup({offset: 25})
			.setHTML(`<br><p class="text-bold">${location_name}</p>`))
			.addTo(map);

		// ===== UNTUK MENAMPILKAN RADIUS =====
		map.addSource("polygon", {
			"type": "geojson",
			"data": {
				"type": "FeatureCollection",
				"features": [{
					"type": "Feature",
					"geometry": {"type": "Point", "coordinates": [lng_marker, lat_marker]}
				}]
			}
		});
		map.addLayer({
			"id": "polygon",
			"type": "circle",
			"source": "polygon",
			"paint": {
				"circle-radius": {
					stops: [ [0, 0], [20, metersToPixelsAtMaxZoom(radiusInMeters, lat_marker)] ], base: 2
				},
				"circle-color": "#223b53",
				"circle-opacity": 0.6
			}
		});
		if(lock_location=='1' && global_type == 'GPS'){
			getDistance = getDistanceFromLatLon(lat_marker, lng_marker, v_lat, v_lng, 'Meters');
			if(getDistance > radiusInMeters){
				//Lokasi absen diluar radius
				$(".gps").html('Your Location is out of area');
				$(".gps").show();
				$("#btnLocation").prop('disabled', true);
			} else {
				//Lokasi absen didalam radius
				$(".gps").html('Please Allow Location / Enable GPS');
				$(".gps").hide();
				$("#btnLocation").prop('disabled', false);
			}
		} else {
			//kondisi normal sudah mendapatkan location
			$(".gps").html('Please Allow Location / Enable GPS');
			$(".gps").hide();
			$("#btnLocation").prop('disabled', false);
		}
	});

	map.on('load', () => {
		map.resize();
		geolocate.trigger();
	});

	const metersToPixelsAtMaxZoom = (meters, latitude) => meters / 0.075 / Math.cos(latitude * Math.PI / 180);

	map.on('idle', () => {
		// $("#btnLocation").prop('disabled', false);
	})

	map.addControl(geolocate);
	map.addControl(new mapboxgl.NavigationControl());
}
	
</script>  
@endsection