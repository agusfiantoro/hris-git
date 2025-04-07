@extends('adminlte::page')
@section('title', 'Missions Review')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Missions Review</h5>
      </div>
      <div class="card-body">
		<div class="form-group row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-3">
						<div class="">
							<select id="emp_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="">
							<select id="cycle_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
							<input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
							<input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
							<div class="input-group-append">
								<span class="input-group-text far fa-calendar form-control-sm"></span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>
				</div>
			</div>	
		</div>
		<div class="div_datatable" style="display:none;"> 
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>	
			<br>
			<br>
			<table id="missions_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr class="text text-center">
				<th data-priority="1"></th>
				<th data-priority="1" width=10></th>
				<th data-priority="3" width=10>No</th>
				<th data-priority="4">Name</th>
				<th data-priority="5">NIK</th>
				<th data-priority="6">Task</th>
				<th data-priority="7" width=150>Activity</th>
				<th data-priority="13" width=150>Evidence</th>
				<th data-priority="9">Type</th>
				<th data-priority="10" width=100>Start Date</th>
				<th data-priority="11" width=100>End Date</th>
				<th data-priority="12" width=100>Submit Date</th>
				<th data-priority="8" width=80>Score Answer</th>
				<th data-priority="2" width=100>Action</th>
			  </tr>
			</thead>
		  </table>
		</div>
    </div>
  </div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-lg">
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
		  <div class="modal-body" id="contentBody" style="max-height:450px;overflow:auto;">
		  </div>
		  <div class="modal-footer">
			<div class="col-12">
				<div class="row">
					<div class="col-md-9">
						<div class="row">
							<label class="col-sm-2 col-form-label">Score :</label>
							<div class="col-sm-3">
								<input type="hidden" name="id_task_activity_answer" id="id_task_activity_answer">
								<select name="score" id="score" class="form-control form-control-sm select2" style="width: 100%;">
								</select>
								<span class="invalid-feedback" role="alert" id="scoreError">
									<strong></strong>
								</span>
							</div>
							<div class="col-sm-6">
								<textarea class="form-control form-control-sm" id="desc_review" rows="3" placeholder="Notes..." name="desc_review"></textarea>
								<span class="invalid-feedback" role="alert" id="desc_reviewError">
									<strong></strong>
								</span>
								<span class="invalid-feedback" role="alert" id="desc_reviewError">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="pull-right">
							<button type="submit" class="btn btn-success btn-sm" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
							<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
						</div>
					</div>
				</div>
			</div>
		  </div>
		</form>
    </div>
  </div>
</div>
@endsection

@section('css')
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
	width:100px;
	text-align:center;
  }
  td.text-score{
	text-align:center;
	font-size:15px;
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


#myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

#myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal_show {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content-show {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

/* Caption of Modal Image */
#caption {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
  text-align: center;
  color: #ccc;
  padding: 10px 0;
  height: 150px;
}

/* Add Animation */
.modal-content-show, #caption {  
  -webkit-animation-name: zoom;
  -webkit-animation-duration: 0.6s;
  animation-name: zoom;
  animation-duration: 0.6s;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content-show {
    width: 100%;
  }
}
</style>
@endsection

@section('scripts')
<script type="text/javascript">
let global_task_cycle = [];
let global_date_start = null;
let global_date_end = null;

$(document).ready(function(){
	
	daterange();
	get_search_emp();
	global_task_cycle = [
		{
			id: 'Daily',
			text: 'Daily'
		},
		{
			id: 'Weekly',
			text: 'Weekly'
		},
		{
			id: 'Monthly',
			text: 'Monthly'
		},
		{
			id: 'Yearly',
			text: 'Yearly'
		},
	];
	
	$('#cycle_search').prepend('<option></option>').select2({
		placeholder: "Select Cycle ...",
		data: global_task_cycle,
		allowClear: true,
	});
	
}); 


const get_search_emp = async () => {
	let result;
    try {
        result = await $.ajax({
           url: "<?= url('task_management/missions/missions_review/get_search_emp') ?>",
            dataType: 'json',
			beforeSend: function () {
			},
            success: function (res) {
				$('#emp_search').prepend('<option></option>').select2({
					data: res,
					allowClear: true,
					placeholder:'Select Employee',
				});
            },
			complete: function(){
				//$('#load_id_pos').hide();
			},
        });
        return result;
    } catch (error) {
     //   get_score(id_task_activity_answer);
    }	
} 	

$(function() {
	$('#daterange').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
		global_date_start = '';
		global_date_end = '';
  	});
});

function daterange(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(7, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
	global_date_start = start;
	global_date_end = end;
	// console.log(global_date_start, global_date_end);
    $('#daterange').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: false,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
            closeText: 'Clear',
        },
        startDate: start, 
        endDate: end,
    }, function(start, end, label) {
        $("#startdate").val(start.format('YYYY-MM-DD'));
        $("#enddate").val(end.format('YYYY-MM-DD'));
		global_date_start = start.format('YYYY-MM-DD');
		global_date_end = end.format('YYYY-MM-DD');
    });

    if($("#startdate").val()=='' || $("#enddate").val()==''){
        $("#startdate").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#enddate").val(moment().format('YYYY-MM-DD'));
		global_date_start = moment().subtract(7, 'days').format('YYYY-MM-DD');
        global_date_end = moment().format('YYYY-MM-DD');
    }
	// console.log(global_date_start, global_date_end)
}

$(document).on('click', '#search', function () {
    get_datatable();
	
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
	
const get_datatable = async () => {	
	$(".div_datatable").show();	
	
	let myData = {
		emp_search: $('#emp_search').val(),
		cycle_search: $('#cycle_search').val(),
		start_date: global_date_start,
		end_date: global_date_end,
	};	
	$('#missions_table').DataTable({
		destroy:true,
		responsive: true,
		processing: true,
		pageLength: 50,
		ajax: {
			url: "{{ route('missions_review.index') }}",
			data : {myData:myData},
			error: function (jqXHR, textStatus, errorThrown) {
			  $('#missions_table').DataTable().ajax.reload();
			}
		  },	
		columns: [
		  {
			defaultContent: '',
			orderable: false, className: 'text-center'
		  },
		  {   
			data: 'id_task_activity_answer',
			defaultContent: '',
			orderable: false
		  },
		  { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center dis'},
		  { data: 'name', name: 'name' },
		  { data: 'nik_employee', name: 'nik_employee' },
		  { data: 'task', name: 'task' },
		  { data: 'activity', name: 'activity' },
		  { data: 'target_evidence', name: 'target_evidence' },
		  { data: 'text_type', name: 'text_type', className: 'text-center'},
		  { data: 'start_date', name: 'start_date', className: 'text-width'},
		  { data: 'end_date', name: 'end_date', className: 'text-width'},
		  { data: 'completion_date', name: 'completion_date', className: 'text-width'},
		  { data: 'score_answer', name: 'score_answer', className: 'text-score'},
		  { data: 'action', name: 'action', orderable: false, className: 'text-center' }
		],
		"fnInitComplete": function (oSettings) {
			$('#missions_table').find("tbody tr td.dtr-control").attr('onclick','det()');
			
		}
    });
}
	

function det(){
	setTimeout(function () {
			$('#missions_table > tbody > tr.child > td.child > ul > li').addClass('row');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-title').addClass('col-2');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-data').addClass('col');
	}, 500);
}

$('#uploadForm').submit(function (e) {
	e.preventDefault();
	let formData = $(this).serializeArray();
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$("#uploadForm input").removeClass("is-invalid");
	$("#uploadForm select").removeClass("is-invalid");
	$("#uploadForm textarea").removeClass("is-invalid");
	$(".error-tab").html("");
		$.ajax({
			type: 'POST',
			headers: {
				'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			},
			url: "{{ route('missions_review.save') }}",
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
					});
					$('#missions_table').DataTable().ajax.reload();
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


function loadedit(id_task_activity_answer,type,score){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#uploadForm input").removeClass("is-invalid");

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
		$("#modal-title").html("Record Video");
	}
    $.ajax({
		url: "{{ route('missions_review.modal_upload') }}",
		data:{
			id_task_activity_answer:id_task_activity_answer,
			type:type,
			score:score,
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(result){
			if (result.status == 'true') {
				$("#contentBody").html(result.view);
				$("#myModal").modal('show'); 	
			}
		},
		complete: function(){
		//	$('#loader').addClass('hidden');
		},
	});
}	

const get_score = async (id_task_activity_answer) => {
	let result;
	let myData = {
		id_task_activity_answer: id_task_activity_answer,
	};
    try {
        result = await $.ajax({
           url: "<?= url('task_management/missions/missions_review/get_score') ?>",
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#score').empty();
				$('#desc_review').val('').trigger('change');
			},
            success: function (res) {
				$('#score').prepend('<option></option>').select2({
					data: res.score,
					allowClear: true,
					placeholder:'Select Score',
				});
				$('#desc_review').val(res.notes).trigger('change');
				$('#act_text').html(res.act_text);
				$('#evi_text').html(res.evi_text);
            },
			complete: function(){
				//$('#load_id_pos').hide();
			},
        });
        return result;
    } catch (error) {
     //   get_score(id_task_activity_answer);
    }	
} 

/*
function get_photo(id_task_activity_answer,type) {
	$.ajax({
		url: "<?= url('task_management/missions/missions_review/get_edit_photo') ?>",
		method: "GET",
		data: {
			id_task_activity_answer: id_task_activity_answer,
			type: type,
		},
		success: function (response) {	
			if(response.length > 0){
				$('#id_task_activity_answer').val(response[0].id_task_activity_answer).trigger('change');
			}
									
		},
		complete: function(){
		//	$('#loader').addClass('hidden');
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
};	
*/	
</script>  
@endsection