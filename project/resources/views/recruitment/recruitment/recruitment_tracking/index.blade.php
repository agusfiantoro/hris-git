@extends('adminlte::page')
@section('title', 'Recruitment Tracking')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Recruitment Tracking</h5>
            </div>      
			<div class="card-body">
				<div class="form-group row">              
					<label class="col-sm-1 col-form-label">Filter :</label>
					<div class="col-sm-3">
						<select id="fil_can" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-4">
						<select id="fil_job" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_can" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
						<button onclick="return false;" id="reset_can" class="btn btn-sm btn-secondary" > Reset</button>
					</div>	
				</div>
				<div class="boards overflow-auto p-0" id="boardsContainer"></div>
			</div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="detailCanForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 class="detail_can modal-title">Detail Candidate</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</form>  
    </div>
  </div>
</div>
@endsection
@section('css')
<link rel="stylesheet" href="{{ asset('vendor/kanban/style.css') }}">
<style type="text/css">
.link-kanban{
	background:#f0f2fb;
}
.link-kanban:hover{
	background:#dde3f9;
}

</style>
@stop
@section('scripts')
<script>if (typeof module === 'object') {window.module = module; module = undefined;}</script>
    <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script>if (window.module) module = window.module;</script>
<script type="text/javascript">

//variables		
let cardBeignDragged;
let dropzones = document.querySelectorAll('.dropzone');
let priorities;
let dataColors = [];
let global_resTrack = [];
let colors;
let id_applied_track;
let code_status = "";
let id_candidate = 0;
let id_applied = 0;
let param_can = localStorage.getItem("param_can");
let param_job = localStorage.getItem("param_job");
let global_sum_sequence = 0;
let global_sum_obs = 0;
let global_sequence = 0;
let global_id_stage = 0;
let global_code_stage = "";

rec_status = [
	{
		id: 'Review',
		text: 'Review'
	},
	{
		id: 'Pass',
		text: 'Pass'
	},
	{
		id: 'Failed',
		text: 'Failed'
	},
];

let dataCards = {
    config:{
        maxid:0
    },
    cards:[]
};
// let theme="light";
//initialize
const filter_candidate = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/recruitment_tracking/filter_candidate') ?>',
			data: {id_url: global_url_server},
            dataType: 'json',
            success: function (res) {
				$('#fil_can').prepend('<option selected></option>').select2({
					placeholder: "Select Candidate ...",
					data: res,
					allowClear: true,
				});
				if(param_can != null){
					$('#fil_can').val(param_can).trigger('change');
				}
            },
        });
        return result;
    } catch (error) {
        filter_candidate();
    }	
}

const filter_job = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/recruitment_tracking/filter_job') ?>',
			data: {id_url: global_url_server},
            dataType: 'json',
            success: function (res) {
				$('#fil_job').prepend('<option selected></option>').select2({
					placeholder: "Select Position ...",
					data: res,
					allowClear: true,
				});
				if(param_job != null){
					$('#fil_job').val(param_job).trigger('change');
				}
            },
        });
        return result;
    } catch (error) {
        filter_job();
    }	
}

const get_status_tracking = async (id_applied_candidate,id_hiring_request_header) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/recruitment_tracking/get_status_tracking') ?>',
			data: {id_url: global_url_server,id_applied_candidate:id_applied_candidate,id_hiring_request_header:id_hiring_request_header},
            dataType: 'json',
            success: function (res) {
				dataColors = res;
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
    //    get_status_tracking();
    }	
}

const get_tracking = async (id_applied_candidate,id_hiring_request_header) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/recruitment_tracking/get_tracking') ?>',
			data: {id_url: global_url_server,id_applied_candidate:id_applied_candidate,id_hiring_request_header:id_hiring_request_header},
            dataType: 'json',
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (resTrack) {
				global_resTrack = resTrack;
            }
        });
        return result;
    } catch (error) {
     //   get_tracking();
    }	
}

$(document).on('click', '#search_can', function () {
	localStorage.removeItem("param_can");
	localStorage.removeItem("param_job");
	if($("#fil_can").val() == '' && $("#fil_job").val() == ''){
	//	getReady(null)
		window.location.reload();
	//	console.log($("#fil_can").val());
	}
	else if($("#fil_can").val() != '' && $("#fil_job").val() != ''){
		localStorage.setItem("param_can", $("#fil_can").val());
		localStorage.setItem("param_job", $("#fil_job").val());
		window.location.reload();
	}
	else if($("#fil_can").val() != ''){
		localStorage.setItem("param_can", $("#fil_can").val());
		window.location.reload();
	}
	else if($("#fil_job").val() != ''){
		localStorage.setItem("param_job", $("#fil_job").val());
		window.location.reload();
	}
});

$(document).on('click', '#reset_can', function () {
	localStorage.removeItem("param_can");
	localStorage.removeItem("param_job");
	window.location.reload();
});
	
$(document).ready(()=>{

getReady(param_can,param_job);

filter_candidate();
filter_job();

document.addEventListener("visibilitychange", function(event) {
        //UNTUK mendeteksi jika user berpindah2 tab
   //    window.location.reload();	
});

});

const getReady = async (id_applied_candidate,id_hiring_request_header) => {
	get_tracking(id_applied_candidate,id_hiring_request_header).then(function(resTrack) {
	//	console.log(global_resTrack);
		get_status_tracking(id_applied_candidate,id_hiring_request_header).then(function(res) {
		//	 colors = res;
			 initializeBoards();
			if(global_resTrack){
				dataCards = global_resTrack;
				initializeComponents(dataCards);
			}
			initializeCards();
		});  
		
    });  
}
	
//functions
function initializeBoards(){ 	
    dataColors.forEach(item=>{
        let htmlString = `
        <div class="board">
				<div class="kanbanCard_header" style="border-bottom: 1px solid rgba(0,0,0,.125);">
					<div class="card ${item.color}" style="margin-bottom:10px;">
						<h3 class="text-center" style="border-bottom: 1px solid rgba(0,0,0,.125);">
							${item.title.toUpperCase()}
						</h3>
						<div class="text-center" style="font-size:13px;padding:5px;">Total Candidate 
							<h6><b id="tot_${item.color}" >${item.total}</b></h6>
							<!-- span id="load_total_${item.color}" class="spinner-border spinner-border-sm" style="display:none;"></span -->
						</div>
					</div>
				</div>
			<div class="dropzone" id="${item.color}" ondragover="allowDrop(event)"></div>
        </div>
        `
        $("#boardsContainer").append(htmlString)
    });
    let dropzones = document.querySelectorAll('.dropzone');
    dropzones.forEach(dropzone=>{
        dropzone.addEventListener('dragover', dragover);
        dropzone.addEventListener('dragleave', dragleave);
        dropzone.addEventListener('drop', drop);
    });
}

function allowDrop(ev) {
  ev.preventDefault();
}

function initializeCards(){
    cards = document.querySelectorAll('.kanbanCard');
    
    cards.forEach(card=>{
        card.addEventListener('dragstart', dragstart);
        card.addEventListener('dragend', dragend);
    });
	$('#loader').addClass('hidden');
}

function initializeComponents(dataArray){
    //create all the stored cards and put inside of the todo area
    dataArray.cards.forEach(card=>{
        appendComponents(card);
    })
}

function appendComponents(card){
	let selected = "";
	let op = "";
	$.each(rec_status, function (i, item) {
		selected = (card.status == item.id) ? 'selected' : '';
		op += '<option value="'+item.id+'" '+selected+'>'+item.id+'</option>';
	});
    //creates new card inside of the todo area
    let htmlString = `
        <!-- div style="cursor:move;" id=${card.id.toString()} class="kanbanCard ${card.position}" draggable="true" -->
        <div id=${card.id.toString()} class="kanbanCard ${card.position}" draggable="false">
            <div class="content description">   
				<div align="center" class="link-kanban" style="cursor:pointer;padding:5px;border-radius:5px;" onclick="loadprofile(${card.id_candidate},${card.id.toString()},'${card.title}',${card.id_dept},${card.id_job_grade},${card.id_batch})">
					<div class="widget-user-image" style="margin-bottom:10px;">
						<img class="img-circle elevation-2" src="<?=url('https://career.borwita.co.id/storage/candidate_photo/')?>${card.photo_candidate}" style="height:70px;width:70px;border-radius:999px;" alt="User Avatar">
					</div>
					<div style="font-size:14px;border-bottom: 1px solid rgba(0,0,0,.125);"><b>${card.title}</b></div>
					<div style="font-size:13px;margin-bottom:10px;">${card.major} - ${card.level}</div>
				</div>
				<div style="font-size:13px;">Last Company : ${card.company_name}</div>
				<div style="font-size:13px;">Last Position : ${card.position_name}</div>
				<div style="font-size:13px;margin-bottom:10px;">Work Duration : ${card.work_duration}</div>
				<div style="font-size:14px;"><b>Applied Position : ${card.description}</b></div>
				<div style="font-size:14px;"><b>Branch : ${card.branch}</b></div>
				<div class="row" style="margin-top:10px;">
					<div class="col-md-12">
						<div class="row">
							<label class="col-sm-3 col-form-label">Status</label>
							<div class="col-sm-9">
								<select name="can_status" id="stat_${card.id.toString()}" class="can_status select2 form-control form-control-sm" disabled>
									${op}
								</select>
							</div>
						</div>
					</div> 						
                </div>
            </div>
        </div>
    `
    $(`#${card.position}`).append(htmlString);
	$(`#stat_${card.id.toString()}`).select2({width:'100%'});
    priorities = document.querySelectorAll(".priority");
}

$(document).on('change', '.can_status', function (event, istrigger) {  
    if(!istrigger){
		let idx = $(this).attr('id');
		let id_stat = idx.split('stat_')[1];
		let can_status = $(this).val();
		$.ajax({
			type: 'POST',
			headers: {
				Accept: "application/json",
			},
			url: "{{ route('candidate.update_status') }}",
			data: {id_applied_candidate:id_stat,
				   can_status:can_status,
				   _token:"{{ csrf_token() }}"},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function (response) {
				if (response.status == 'true') {
					swal({
						icon: 'success',
						title: 'Success',
						text: response.message
					});
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
			error: function (response) {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			}
		}); 
	}
});

function removeClasses(cardBeignDragged, color){

    cardBeignDragged.classList.remove('DRF');
    cardBeignDragged.classList.remove('SHL');
    cardBeignDragged.classList.remove('PRE');
    cardBeignDragged.classList.remove('PSY');
    cardBeignDragged.classList.remove('BEI');
    cardBeignDragged.classList.remove('OBS');
    cardBeignDragged.classList.remove('REF');
    cardBeignDragged.classList.remove('OFL');
   
    cardBeignDragged.classList.add(color);
    position(cardBeignDragged, color);
}

function save(id,color){
	id_applied_track.push(id);
	colors.push(color);        
}

function position(cardBeignDragged, color){
    const index = dataCards.cards.findIndex(card => card.id === parseInt(cardBeignDragged.id));
    dataCards.cards[index].position = color;
    save(parseInt(cardBeignDragged.id),color);
}

//cards
function dragstart({target}){
	id_applied_track = [];
	colors = [];
	code_status = $(this).attr('class').split(' ')[1];
    dropzones.forEach( dropzone=>dropzone.classList.add('highlight'));
    this.classList.add('is-dragging');
}


function dragend(){
    dropzones.forEach( dropzone=>dropzone.classList.remove('highlight'));
    this.classList.remove('is-dragging');
}

function dragover({target}){	
    this.classList.add('over');
    cardBeignDragged = document.querySelector('.is-dragging');
    if(this.id ==="DRF"){
        removeClasses(cardBeignDragged, "DRF");       
    }
    else if(this.id ==="SHL"){
        removeClasses(cardBeignDragged, "SHL");
    }
    else if(this.id ==="PRE"){
        removeClasses(cardBeignDragged, "PRE");
    }
    else if(this.id ==="PSY"){
        removeClasses(cardBeignDragged, "PSY");
    }
    else if(this.id ==="BEI"){
        removeClasses(cardBeignDragged, "BEI");
    }
    else if(this.id ==="OBS"){
        removeClasses(cardBeignDragged, "OBS");
    }
	else if(this.id ==="REF"){
        removeClasses(cardBeignDragged, "REF");
    }
	else if(this.id ==="OFL"){
        removeClasses(cardBeignDragged, "OFL");
    }
    this.appendChild(cardBeignDragged);
}

function dragleave(){ 
    this.classList.remove('over');
	$('.dropzone').removeClass('over');
}

function drop(){
	var id_status = id_applied_track.pop();
	var col_status = colors.pop();
	let arr_color = [];
	
	arr_color.push(code_status,col_status);
	if(code_status != col_status){
		$.ajax({
			type: 'POST',
			headers: {
				Accept: "application/json",
			},
			url: "{{ route('candidate.update') }}",
			data: {id_applied_candidate:id_status,
				   candidate_status:col_status,
				   _token:"{{ csrf_token() }}"},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function (response) {
				if (response.status == 'true') {
					swal({
						icon: 'success',
						title: response.name_can,
						text: response.message
					});
					$.each(arr_color, function (i, item) {
						console.log(item);
						get_color(item).then(function(res) {
							$('#tot_'+item).html(res.total);
						});  	
					});
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
			error: function (response) {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			}
		}); 
	}
	this.classList.remove('over');
}

const get_color = async (item) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/recruitment_tracking/get_total') ?>',
			data: {color: item,id_url: global_url_server},
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
    }	
}

function loadprofile(id_candidate,id_applied_candidate,name,id_dept,id_job_grade,id_batch){
	if(id_dept == null && id_job_grade == null){
		id_dept = "null";
		id_job_grade = "null";
	}
	if(id_batch == null){
		id_batch = "null";
	}
	$("#contentBody").html('');
    $.ajax({
		url: "{{ route('candidate_data.modal_detail') }}",
		data:{global_id_candidate:id_candidate,
				global_id_applied:id_applied_candidate,
				global_id_dept:id_dept,
				global_id_job_grade:id_job_grade,
				global_id_batch:id_batch},
		success: function(result){
        $(".detail_can").html('Detail Candidate ('+name+')');
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }
	});
}

function get_pos(){		
		$.getJSON('<?= url('recruitment/recruitment/candidate/get_pos') ?>', function (data) {
			$('#pos_req').prepend('<option selected></option>').select2({
				placeholder: "Select Job Position ...",
				data: data,
				allowClear: true,
			});
		}).fail(function (data) { // Call failed
            get_pos();
		});					
}

const get_pos_done = async (id_hiring_header) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_pos_done') ?>',
			data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
				$('#pos_req').prepend('<option selected></option>').select2({
					placeholder: "Select Job Position ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_pos_done(id_hiring_header);
    }	
}

$(document).on('change', '#pos_req', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		$('#ref_number').val('');
		$('#can_branch').val('');
		
		get_pos_change($(this).select2('val'));	
	}
});

function get_pos_change(id_hiring_header){
	let myData = {
			id_hiring_header: id_hiring_header,
		};	
	$.ajax({
		url: "<?= url('recruitment/recruitment/candidate/get_pos_change') ?>",
		method: "GET",
		data: myData,
		beforeSend: function () {
			$('#ref_number').val('');
			$('#can_branch').val('');
		},
		success: function (response) {
			$('#ref_number').val(response.ref_number);
			$('#can_branch').val(response.branch);
		}
	});
}

const get_stage_applied = async (seq,id_status) => {
//	console.log(seq);
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_stage') ?>',
		//	data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
		//		console.log(res);
				let code_seq = 0;
				let arr = [];
				$.each(res, function(idx, item) {
					 if(item.sequence == seq || item.sequence == global_sum_sequence || item.sequence == global_sum_obs){
						code_seq = item.id;
						arr.push(code_seq);
					 }
				});
								
				$('#can_stage').prepend('<option selected></option>').select2({
					placeholder: "Select Recruitment Stage ...",
					data: res,
				}).on('change', function (e) {
					$('#can_stage option').attr('disabled',true);
					var aaList = $("option", e.target);
					$.each(aaList, function(idx, item) {
						$.each(arr, function(i, val) {
							$("option[value='"+val+"']").attr('disabled',false);																							
						});	
					});	
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_stage_applied(seq,id_status);
    }	
}

const get_stage_preview = async (seq) => {
//	console.log(seq);
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_stage') ?>',
		//	data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
		//		console.log(res);
				let code_dbs = 0;
				let code_seq = 0;
				$.each(res, function(idx, item) {
					 if(item.sequence == seq){
						code_seq = item.id;
					 }
					 else if(item.code == 'DBS'){
						code_dbs = item.id;
					}
				});
								
				$('#can_stage').prepend('<option selected></option>').select2({
					placeholder: "Select Recruitment Stage ...",
					data: res,
				}).on('change', function (e) {
					$('#can_stage option').attr('disabled',true);
					var aaList = $("option", e.target);
					$.each(aaList, function(idx, item) {
						$("option[value='"+code_seq+"']").attr('disabled',false);
						$("option[value='"+code_dbs+"']").attr('disabled',false);
					});	
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_stage_preview(seq);
    }	
}
		
$(document).on('change', '#can_stage', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
	//	$('#offering_date').val('');
	//	$('#join_date').val('');
		
		if($(this).select2('data')[0].id !== ""){
			global_code_stage = $(this).select2('data')[0].code;
			if($(this).select2('data')[0].code == 'OFL' && $('#can_status').val() == 'Pass'){
				$('#offering_date').attr('disabled',false);
				$('#offering_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#join_date').attr('disabled',true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
			else if($(this).select2('data')[0].code == 'HIR'  && $('#can_status').val() == 'Pass'){
				$('#join_date').attr('disabled',false);
				$('#join_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#offering_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
			}
			else{
				$('#offering_date').attr('disabled',true);
				$('#join_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
		}
	}
});

$(document).on('change', '#can_status', function (event, istrigger) {  
    if(!istrigger){	
		if($(this).select2('data')[0].id !== ""){
			if(global_code_stage == 'OFL' && $(this).select2('data')[0].id == 'Pass'){
				$('#offering_date').attr('disabled',false);
				$('#offering_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#join_date').attr('disabled',true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
			else if(global_code_stage == 'HIR' && $(this).select2('data')[0].id == 'Pass'){
				$('#join_date').attr('disabled',false);
				$('#join_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#offering_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
			}
			else{
				$('#offering_date').attr('disabled',true);
				$('#join_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
		}
	}
});


$('#detailCanForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#detailCanForm input").removeClass("is-invalid");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('candidate_data.update') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
                                $('#myModal').modal('hide');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
									window.location.reload();
								   }
								);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
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

</script>
@endsection