@extends('adminlte::page')
@section('title', 'Master Task')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Master Task</h5>
		<div class="card-tools">
			<button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Task</button>
		</div>
      </div>
      <div class="card-body">
		<div class="form-group row" id="id_dept_search">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-2">
						<label class="col-form-label">Department</label>
					</div>
					<div class="col-md-6">
						<div class="">
							<select id="dept_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
							<span class="invalid-feedback" role="alert" id="dept_searchError">
								<strong></strong>
							</span>
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
			<table id="task_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr>
				<th></th>
				<th></th>
				<th data-priority="2">No</th>
				<th data-priority="3">Description</th>
				<th data-priority="6">Type</th>
				<th data-priority="4">Department</th>
				<th data-priority="5">Grade</th>
				<th data-priority="7">Status</th>		
				<th data-priority="1" width="150" class="text text-center">Action</th>
			  </tr>
			</thead>
		  </table>
		</div>
    </div>
  </div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-xl">
	<div id="modal_second"></div>
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="taskForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody" style="overflow-y: auto;">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="save_button btn btn-success btn-sm " id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
			<button type="submit" class="edit_button btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>
		 <div style="display:none;">
			<table id="sample_table_rec">
				<tr id="" style="font-size:12px;">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
						<input name="task[0][id_activity]" id="task_0_id_activity" type="hidden" class="form-control form-control-sm id_activity_input">
						<textarea name="task[0][activity]" id="task_0_activity" class="form-control form-control-sm activity_input" rows="2"></textarea>
						<span class="invalid-feedback activity_input_error" role="alert" id="task_0_activityError">
							<strong></strong>
						</span>
					</td>
					<td>
						<input name="task[0][seq]" id="task_0_seq" class="form-control form-control-sm seq_input">
						<span class="invalid-feedback seq_input_error" role="alert" id="task_0_seqError">
							<strong></strong>
						</span>
					</td>
					<td>
						<input name="task[0][notes]" id="task_0_notes" class="form-control form-control-sm notes_input">
						<span class="invalid-feedback notes_input_error" role="alert" id="task_0_notesError">
							<strong></strong>
						</span>
					</td>
					<td>
						<textarea name="task[0][evidence]" id="task_0_evidence" class="form-control form-control-sm evidence_input" rows="2"></textarea>
						<span class="invalid-feedback evidence_input_error" role="alert" id="task_0_evidenceError">
							<strong></strong>
						</span>
					</td>
					<td>
						<select name="task[0][evidence_type]" id="task_0_evidence_type" class="form-control form-control-sm select2 evidence_type_input" style="width: 100%;"></select>
						<span class="invalid-feedback evidence_type_input_error" role="alert" id="task_0_evidence_typeError">
							<strong></strong>
						</span>	
					</td>
					<td>
						<select name="task[0][task_cycle]" id="task_0_task_cycle" class="form-control form-control-sm select2 task_cycle_input" style="width: 100%;"></select>
						<span class="invalid-feedback task_cycle_input_error" role="alert" id="task_0_task_cycleError">
							<strong></strong>
						</span>	
					</td>
					<td>
						<input autocomplete="off" readonly="readonly" name="task[0][start_time]" id="task_0_start_time" class="form-control form-control-sm start_time_input" style="width:100%;background-color:#fff;">
						<span class="invalid-feedback d-block start_time_input_error" role="alert" id="task_0_start_timeError">
						  <strong></strong>
						</span>
					</td>
					<td>
						<input autocomplete="off" readonly="readonly" name="task[0][end_time]" id="task_0_end_time" class="form-control form-control-sm end_time_input" style="width:100%;background-color:#fff;">
						<span class="invalid-feedback d-block end_time_input_error" role="alert" id="task_0_end_timeError">
						  <strong></strong>
						</span>
					</td>
					<td align="center">
					  <center>
						<input type="checkbox" id="task_0_multi_attach" style="width: 20px;height: 20px;" name="task[0][multi_attach]" class="multi_attach_input">
					  </center>
					</td>
					<td>
						<input name="task[0][link_question]" id="task_0_link_question" class="form-control form-control-sm link_question_input">
					</td>
					<td>
						<input name="task[0][photo_question]" id="task_0_photo_question" class="form-control form-control-sm photo_question_input">
					</td>
					<td align="center">
					  <center>
						<input type="checkbox" id="task_0_random_object" style="width: 20px;height: 20px;" name="task[0][random_object]" class="random_object_input">
					  </center>
					</td>
					<td align="center">
					  <center>
						<input type="checkbox" id="task_0_ans_flag" style="width: 20px;height: 20px;" name="task[0][ans_flag]" class="ans_flag_input">
					  </center>
					</td>
					<td>
						<select name="task[0][ans_type]" id="task_0_ans_type" class="form-control form-control-sm select2 ans_type_input" style="width: 100%;"></select>
					</td>
					<td>
						<input name="task[0][min_range]" id="task_0_min_range" class="form-control form-control-sm min_range_input">
					</td>
					<td>
						<input name="task[0][max_range]" id="task_0_max_range" class="form-control form-control-sm max_range_input">
					</td>
					<td>
						<input name="task[0][dic_correct]" id="task_0_dic_correct" class="form-control form-control-sm dic_correct_input">
					</td>
					<td>
						<input name="task[0][max_score]" id="task_0_max_score" class="form-control form-control-sm max_score_input">
						<span class="invalid-feedback max_score_input_error" role="alert" id="task_0_max_scoreError">
							<strong></strong>
						</span>
					</td>
					<td align="center">
					  <center>
						<input type="checkbox" id="task_0_status" style="width: 20px;height: 20px;" name="task[0][status]" class="status_input">
						<span class="invalid-feedback status_input_error" role="alert" id="task_0_statusError">
							<strong></strong>
						</span>
					  </center>
					</td>
					<td>
						<center>
							<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
						</center>
					</td>
					
				</tr>
			</table>
		</div>     
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

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, .select2-hidden-accessible + .select2-container .select2-selection__clear {
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
 
 .select2-container > span.select2-dropdown > span.select2-results > ul[id*="select2-task_"][id$="_random_type-results"]{
	 font-size:12px;!important;
 }
 
 [id*="select2-task_"][id$="_random_type-container"]{
	 font-size:12px;!important;
 }

</style>
@endsection

@section('scripts')
<script type="text/javascript">
let global_id_rec_detail = 0;
let global_id_task = [];
let global_task_type = [];
let global_evidence_type = [];
let global_task_cycle = [];
let global_answer_type = [];
let global_dept = null;
let global_dept_search = null;
let global_grade = '';

function on_close_modal() {
	$("#myModal").modal('hide'); 
}


function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".invalid-date").children("div").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#taskForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Master Task");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
    $.ajax({
			url: "{{ route('master_task.modal_detail') }}",
			data:{
				global_task:0,
			},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 			
		}
	});
}

function loadedit(id_task){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".invalid-date").children("div").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#taskForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Master Task");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");
    $.ajax({
			url: "{{ route('master_task.modal_detail') }}",
			data:{
				global_task:id_task,
			},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

$(document).on('click', '#new_rec_detail', function () {
            var content = jQuery('#sample_table_rec tr'),
                    size = global_id_rec_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
//			element.attr('id_record', size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_activity_input').attr('id', 'task_' + size + '_id_activity');
            element.find('.id_activity_input').attr('name', 'task[' + size + '][id_activity]'); 
			
			element.find('.activity_input').attr('id', 'task_' + size + '_activity');
            element.find('.activity_input').attr('name', 'task[' + size + '][activity]'); 
			element.find('.activity_input_error').attr('id', 'task_' + size + '_activityError');
			
			element.find('.seq_input').attr('id', 'task_' + size + '_seq');
            element.find('.seq_input').attr('name', 'task[' + size + '][seq]'); 
			element.find('.seq_input_error').attr('id', 'task_' + size + '_seqError');
			
			element.find('.notes_input').attr('id', 'task_' + size + '_notes');
            element.find('.notes_input').attr('name', 'task[' + size + '][notes]'); 
			element.find('.notes_input_error').attr('id', 'task_' + size + '_notesError');
			
			element.find('.evidence_input').attr('id', 'task_' + size + '_evidence');
            element.find('.evidence_input').attr('name', 'task[' + size + '][evidence]'); 
			element.find('.evidence_input_error').attr('id', 'task_' + size + '_evidenceError');
	
			element.find('.evidence_type_input').attr('id', 'task_' + size + '_evidence_type');
            element.find('.evidence_type_input').attr('name', 'task[' + size + '][evidence_type]');
			element.find('.evidence_type_input_error').attr('id', 'task_' + size + '_evidence_typeError');
			element.find('.evidence_type_input').prepend('<option selected></option>').select2({
                placeholder: "Select type ...",
				data: global_evidence_type,
				allowClear: true,
            });
			
			element.find('.task_cycle_input').attr('id', 'task_' + size + '_task_cycle');
            element.find('.task_cycle_input').attr('name', 'task[' + size + '][task_cycle]');
			element.find('.task_cycle_input_error').attr('id', 'task_' + size + '_task_cycleError');
			element.find('.task_cycle_input').prepend('<option selected></option>').select2({
                placeholder: "Select Cycle ...",
				data: global_task_cycle,
				allowClear: true,
            });
			
			element.find('.start_time_input').attr('id', 'task_' + size + '_start_time');
            element.find('.start_time_input').attr('name', 'task[' + size + '][start_time]'); 
			element.find('.start_time_input_error').attr('id', 'task_' + size + '_start_timeError');
			element.find('.start_time_input').timepicker({
			  uiLibrary: 'bootstrap4',
			  format: "HH:MM",
			  mode: '24hr'
			});
			
			element.find('.end_time_input').attr('id', 'task_' + size + '_end_time');
            element.find('.end_time_input').attr('name', 'task[' + size + '][end_time]'); 
			element.find('.end_time_input_error').attr('id', 'task_' + size + '_end_timeError');
			element.find('.end_time_input').timepicker({
			  uiLibrary: 'bootstrap4',
			  format: "HH:MM",
			  mode: '24hr'
			});
			
			element.find('.multi_attach_input').attr('id', 'task_' + size + '_multi_attach');
            element.find('.multi_attach_input').attr('name', 'task[' + size + '][multi_attach]'); 
			
			element.find('.link_question_input').attr('id', 'task_' + size + '_link_question');
            element.find('.link_question_input').attr('name', 'task[' + size + '][link_question]'); 
			
			element.find('.photo_question_input').attr('id', 'task_' + size + '_photo_question');
            element.find('.photo_question_input').attr('name', 'task[' + size + '][photo_question]'); 
			
			element.find('.random_object_input').attr('id', 'task_' + size + '_random_object');
            element.find('.random_object_input').attr('name', 'task[' + size + '][random_object]'); 
			
			element.find('.ans_flag_input').attr('id', 'task_' + size + '_ans_flag');
            element.find('.ans_flag_input').attr('name', 'task[' + size + '][ans_flag]'); 
			
			element.find('.ans_type_input').attr('id', 'task_' + size + '_ans_type');
            element.find('.ans_type_input').attr('name', 'task[' + size + '][ans_type]'); 
			element.find('.ans_type_input').prepend('<option selected></option>').select2({
                placeholder: "Select Answer Type ...",
				data: global_answer_type,
				allowClear: true,
            });
			
			element.find('.min_range_input').attr('id', 'task_' + size + '_min_range');
            element.find('.min_range_input').attr('name', 'task[' + size + '][min_range]'); 
			
			element.find('.max_range_input').attr('id', 'task_' + size + '_max_range');
            element.find('.max_range_input').attr('name', 'task[' + size + '][max_range]'); 
			
			element.find('.dic_correct_input').attr('id', 'task_' + size + '_dic_correct');
            element.find('.dic_correct_input').attr('name', 'task[' + size + '][dic_correct]'); 
			
			element.find('.max_score_input').attr('id', 'task_' + size + '_max_score');
            element.find('.max_score_input').attr('name', 'task[' + size + '][max_score]'); 
			
			element.find('.status_input').attr('id', 'task_' + size + '_status');
            element.find('.status_input').attr('name', 'task[' + size + '][status]'); 
					
            element.appendTo('#table_rec_body');
			 $('#table_rec_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });			
        });

		$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
            $('#table_rec_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });	
		

var AjaxUrl = "";
  $(".save_button").on("click",function(){
	AjaxUrl = "{{ route('master_task.save') }}";
	$(this).closest(".card").find("taskForm").submit();
  });

  $(".edit_button").on("click",function(){
	AjaxUrl = "{{ route('master_task.update') }}";
	$(this).closest(".card").find("taskForm").submit();
  });		
$('#taskForm').submit(function (e) {
		e.preventDefault();			
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".invalid-date").children("div").text("");
		$("#taskForm input").removeClass("is-invalid");
		$("#taskForm select").removeClass("is-invalid");
		$("#taskForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text(""); 
		$(".error-tab").html("");
		let formData = $(this).serializeArray();
		$.ajax({
			type: 'POST',
			headers: {
				Accept: "application/json",
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: AjaxUrl,
			data: formData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(response) {
			   if (response.status == 'true') {
				//   $('#myModal').modal('hide');
					swal({
						icon: 'success',
						title: 'Success',
						text: response.message,
						buttons: {
							confirm: {
								className: 'btn-success'
							},
						},
					});
				//	$('#task_table').DataTable().ajax.reload();
					
				} else {
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
				if (response.status === 422) {
					let errors = response.responseJSON.errors;
					Object.keys(errors).forEach(function(key) {
						var key_temp = key.replaceAll(".", "_");
						$("#" + key_temp).addClass("is-invalid");
						$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
						$("#" + key_temp + "Error").children("div").text(errors[key][0]);
						var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
						if (tab_id != undefined) {
							$("#tab_rec_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
						}
					});
				} else {
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

$(document).on('click', '#search', function () {
	$(".invalid-feedback").children("strong").text("");
	$("#id_dept_search select").removeClass("is-invalid");
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
	dept_search: $('#dept_search').val(),
};
	
	$('#task_table').DataTable({
		destroy:true,
		processing: true,
		pageLength: 10,
		responsive: true,     
	ajax: {
        url: "{{ route('master_task.index') }}",
		data : myData,
        error: function (jqXHR, textStatus, errorThrown) {
			let errors = jqXHR.responseJSON.errors;
			Object.keys(errors).forEach(function(key) {
				var key_temp = key.replaceAll(".", "_");
				$("#" + key_temp).addClass("is-invalid");
				$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
			});
			$('#task_table_processing').css('display','none');
			$('#task_table > tbody > tr > td.dataTables_empty').html('No data available in table');
        //  $('#task_table').DataTable().ajax.reload();
        }
      },	
      columns: [
		  {
			defaultContent: '',
			orderable: false,
		  },
		  {   
			data: 'id_task',
			defaultContent: '',
			orderable: false
		  },
		  { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'space text-center'},
		  { data: 'description', name: 'description' },
		  { data: 'task_type', name: 'task_type' },
		  { data: 'dept', name: 'dept' },
		  { data: 'grade', name: 'grade' },
		  { data: 'status', name: 'status', className: 'space text-center' },
		  { data: 'action', name: 'action', orderable: false, className: 'space text-center' }
      ],
    });
	
}	


$(document).ready(function(){
	  	  
//	get_datatable();
	$('#loader').removeClass('hidden');
	get_dept_search();
	
	global_task_type = [
		{
			id: 'Mandatory',
			text: 'Mandatory'
		},
		{
			id: 'Random',
			text: 'Random'
		}
	];
	
	global_evidence_type = [
		{
			id: 'Photo',
			text: 'Photo (No Lock Location)'
		},
		{
			id: 'GPS',
			text: 'Photo (Lock Location)'
		},
		{
			id: 'Document',
			text: 'Document'
		},
		{
			id: 'Essay',
			text: 'Essay'
		},
	];
	
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
	global_answer_type = [
		{
			id: 'Range',
			text: 'Range'
		},
		{
			id: 'Dictionary',
			text: 'Dictionary'
		}
	];
	
}); 

const get_dept_search = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/master_task/get_dept') ?>',
            dataType: 'json',
            success: function (res) {
				$('#dept_search').prepend('<option></option>').select2({
					placeholder: "Select Department ...",
					data: res,
					allowClear: true,
				});
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
     //   get_dept();
    }	
}

const get_dept = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/master_task/get_dept') ?>',
            dataType: 'json',
            success: function (res) {
				$('#id_dept').prepend('<option></option>').select2({
					data: res,
					allowClear: true,
				});
            },
			complete: function(){
			//	$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
     //   get_dept();
    }	
}

$(document).on('change', '#id_dept', function (event, istrigger) {  
    if(!istrigger){
		global_dept = $(this).select2('val');
		get_grade($(this).select2('val'));
	}
});

const get_grade = async (id_dept) => {
	let result;
	let myData = {
		id_dept: id_dept,
	};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/master_task/get_grade') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_grade').empty();
				$('#load_id_grade').show();
			},
            success: function (res) {
				$('#id_grade').prepend('<option></option>').select2({
					data: res,
					allowClear: true,
				});
            },
			complete: function(){
				$('#load_id_grade').hide();
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
        get_grade(id_dept);
    }	
} 

</script>  
@endsection