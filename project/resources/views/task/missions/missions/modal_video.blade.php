<div class="row" >
	<video align="center" id="myVideo" playsinline class="video-js vjs-default-skin">
	  <p class="vjs-no-js">
		To view this video please enable JavaScript, or consider upgrading to a
		web browser that
	  </p>
	</video>
	<div class="col-md-12" align="center">	
		<div id="video"></div>
		<span class="invalid-feedback" role="alert" id="videoError">
			<strong></strong>
		</span>
		<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
	</div>
</div>
<div align="center" style="margin-top:5px;">
	<button type="button" id="btnRecord" onClick="showRecord()" class="btn btn-primary" style="font-size:12px!important;display:none;"><span class="fa fa-circle" style="color:white;"></span> <b>Record</b></button>
	<button type="button" id="btnPause" onClick="showPause()" class="btn btn-warning" style="color:white;font-size:12px!important;display:none;"><span class="fas fa-pause-circle"></span> <b>Pause</b></button>
	<button type="button" id="btnResume" onClick="showResume()" class="btn btn-success" style="font-size:12px!important;display:none;"><span class="fas fa-play-circle" style="color:white;"></span> <b>Resume</b></button>
	<button type="button" id="btnStop" onClick="showStop()" class="btn btn-danger" style="font-size:12px!important;display:none;"><span class="fas fa-stop fa-sm" style="color:white;"></span> <b>Stop</b></button>
</div>
<br>
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
});	

newBack = {
   video: {
		facingMode: {
			exact: 'environment'
		//	exact: 'user'
		}
	},
};


var options = {
    controls: true,
    width: 520,
    height: 340,
    fluid: false,
    bigPlayButton: false,
    controlBar: {
        volumePanel: false,
    },
    plugins: {
        record: {
            audio: false,
            video:true,
            maxLength: 59,
            displayMilliseconds: false,
            debug: true
        },
    }
};

makePlayer();
//applyVideoWorkaround();

function makePlayer() {
	player = videojs('myVideo', options, function() {
	});
	
	player.on('click', function(e) { 
		$('#btnRecord').show();
	});
	player.on('touchstart', function(e) { 
		$('#btnRecord').show();
	});

	if($(window).width() <= 600){
		player.record().loadOptions(newBack);
	}
	// error handling
	player.on('deviceError', function() {
		console.warn('device error:', player.deviceErrorCode);
	});

	player.on('error', function(element, error) {
		console.error(error);
	});

	// user clicked the record button and started recording
	player.on('startRecord', function() {
		console.log('started recording!');
	});
	// user completed recording and stream is available
	player.on('finishRecord', function() {
		console.log('finished recording: ', player.recordedData);
	});
}
function showRecord() {
	player.record().start();
	$('#btnPause').show();
	$('#btnStop').show();
}
function showStop() {
	player.record().stop();
}
function showPause() {
	player.record().pause();
	$('#btnResume').show();
}
function showResume() {
	player.record().resume();
}
</script>