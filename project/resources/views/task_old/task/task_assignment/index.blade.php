@extends('adminlte::page')
@section('title', 'Task Assignment')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Task Assignment</h5>
		<div class="card-tools">
			<button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Task</button>
		</div>
      </div>
      <div class="card-body">
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
				<th data-priority="4">Department</th>
				<th data-priority="5">Grade</th>
				<th data-priority="6">Position</th>
				<th data-priority="7">Status</th>		
				<th data-priority="1" width="150" class="text text-center">Action</th>
			  </tr>
			</thead>
		  </table>
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
		  <div class="modal-body" id="contentBody" style="height:550px;overflow-y: auto;">
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
						<input name="task[0][id_task_activity]" id="task_0_id_task_activity" type="hidden" class="form-control form-control-sm id_task_activity_input">
						<input name="task[0][id_act]" id="task_0_id_act" type="hidden" class="form-control form-control-sm id_act_input">
						<!-- select name="task[0][id_task]" id="task_0_id_task" class="form-control form-control-sm select2 id_task_input" style="width: 180px;font-size:12px;"></select>
						<span class="invalid-feedback id_task_input_error" role="alert" id="task_0_id_taskError">
							<strong></strong>
						</span -->	
						<input name="task[0][id_task]" id="task_0_id_task" type="hidden" class="form-control form-control-sm id_task_input">
						<span id="task_0_task_text" class="task_text_input"></span>
					</td>
					<td>
							<select name="task[0][random_type]" id="task_0_random_type" class="form-control form-control-sm select2 random_type_input" style="width: 100%;font-size:10px;"></select>
							<span class="invalid-feedback random_type_input_error" role="alert" id="task_0_random_typeError">
								<strong></strong>
							</span>	
					</td>
					<td>
						<div class="is-loading">
							<!-- select name="task[0][id_activity]" id="task_0_id_activity" class="form-control form-control-sm select2 id_activity_input" style="width: 180px;font-size:12px;"></select>
							<span class="invalid-feedback id_activity_input_error" role="alert" id="task_0_id_activityError">
								<strong></strong>
							</span -->	
							<span id="load_act_0" class="spinner-border spinner-border-sm load_act_input" style="display:none;"></span>
							<input name="task[0][id_activity]" id="task_0_id_activity" type="hidden" class="form-control form-control-sm id_activity_input">
							<span id="task_0_activity_text" class="activity_text_input"></span>
						</div>
					</td>
					<td align="justify">
						<span id="task_0_evidence" class="evidence_input"></span>
					</td>
					<td align="center">
						<span id="task_0_type" class="type_input"></span>
					</td>
					<td align="center">
						<span id="task_0_cycle" class="cycle_input"></span>
					</td>
					<td align="center">
						<span id="task_0_time" class="time_input"></span>
					</td>
					<td align="center">
						<span id="task_0_score" class="score_input"></span>
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
<div class="modal fade" id="modal_browse" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1055;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task List</h5>
                    <button type="button" onclick="second_close()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentList">						   
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
<script src="{{ asset('vendor/bootstrap/js/id-id.js') }}"></script>
<script type="text/javascript">
let global_id_rec_detail = 0;
let global_id_task = [];
let global_id_activity = [];
let global_departments = [];
//let global_id_position = [];
let global_random_type = [];
let global_z = [];
let global_dept = null;
let global_grade = '';

function on_close_modal() {
	$("#myModal").modal('hide'); 
}

function on_close_modal_position() {
	$("#browseModaljob").modal('hide'); 
}

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".invalid-date").children("div").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#taskForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Assignment Task");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
    $.ajax({
			url: "{{ route('task.modal_detail') }}",
			data:{
				global_task:0,
			},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 			
		}
	});
}

function loadedit(id_task_management){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".invalid-date").children("div").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#taskForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Assignment Task");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");
    $.ajax({
			url: "{{ route('task.modal_detail') }}",
			data:{
				global_task:id_task_management,
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
			element.attr('id_record', size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_task_activity_input').attr('id', 'task_' + size + '_id_task_activity');
            element.find('.id_task_activity_input').attr('name', 'task[' + size + '][id_task_activity]'); 
			
			element.find('.id_act_input').attr('id', 'task_' + size + '_id_act');
            element.find('.id_act_input').attr('name', 'task[' + size + '][id_act]'); 
			
			element.find('.id_task_input').attr('id', 'task_' + size + '_id_task');
            element.find('.id_task_input').attr('name', 'task[' + size + '][id_task]');
		//	element.find('.id_task_input_error').attr('id', 'task_' + size + '_id_taskError');
		/*	element.find('.id_task_input').prepend('<option selected></option>').select2({
                placeholder: "Select Task ...",
				data: global_id_task,
				allowClear: true,
            });
		*/	
		
			element.find('.random_type_input').attr('id', 'task_' + size + '_random_type');
            element.find('.random_type_input').attr('name', 'task[' + size + '][random_type]');
			element.find('.random_type_input_error').attr('id', 'task_' + size + '_random_typeError');
			element.find('.random_type_input').prepend('<option selected></option>').select2({
                placeholder: "Select Status ...",
				data: global_random_type,
				allowClear: true,
            });
			
			element.find('.load_act_input').attr('id', 'load_act_' +size);

			element.find('.id_activity_input').attr('id', 'task_' + size + '_id_activity');
            element.find('.id_activity_input').attr('name', 'task[' + size + '][id_activity]');
        //   element.find('.id_activity_input_error').attr('id', 'task_' + size + '_id_activityError');
        /*  element.find('.id_activity_input').prepend('<option selected></option>').select2({
                placeholder: "Select Activity ...",
				data: global_id_activity,
            });
		*/	
			element.find('.task_text_input').attr('id', 'task_' + size + '_task_text');
			element.find('.activity_text_input').attr('id', 'task_' + size + '_activity_text');
			element.find('.evidence_input').attr('id', 'task_' + size + '_evidence');
			element.find('.type_input').attr('id', 'task_' + size + '_type');
			element.find('.cycle_input').attr('id', 'task_' + size + '_cycle');
			element.find('.time_input').attr('id', 'task_' + size + '_time');
			element.find('.score_input').attr('id', 'task_' + size + '_score');
			
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
	AjaxUrl = "{{ route('task.save') }}";
	$(this).closest(".card").find("taskForm").submit();
  });

  $(".edit_button").on("click",function(){
	AjaxUrl = "{{ route('task.update') }}";
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
					loadedit(response.id_task_management);
					$('#task_table').DataTable().ajax.reload();
					
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
   
	$('#task_table').DataTable({
		processing: true,
		pageLength: 10,
		responsive: true,     
	ajax: {
        url: "{{ route('task.index') }}",
        error: function (jqXHR, textStatus, errorThrown) {
          $('#task_table').DataTable().ajax.reload();
        }
      },	
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {   
        data: 'id_task_management',
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'space text-center'},
      { data: 'description', name: 'description' },
      { data: 'dept', name: 'dept' },
      { data: 'grade', name: 'grade' },
      { data: 'position', name: 'position' },
      { data: 'status', name: 'status', className: 'space text-center' },
      { data: 'action', name: 'action', orderable: false, className: 'space text-center' }
      ],
      rowCallback: function(row, data, index){
        if(access_create == 0){
          $(row).find('.new').css('display', 'none');
        } 
        if(access_edit == 0){
          $(row).find('.btn-edit').css('display', 'none');
        }   
        if(access_delete == 0){
          $(row).find('.btn-del').css('display', 'none');
        }
        if(access_print == 0){
          $(row).find('.btn-view').css('display', 'none');
        } 
		
      },
    });
	
}	


$(document).ready(function(){
	  	  
	get_datatable();
	
	global_random_type = [
		{
			id: 'Automatic',
			text: 'Automatic'
		},
		{
			id: 'Manual',
			text: 'Manual'
		},
	];
}); 

const get_dept = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_dept') ?>',
            dataType: 'json',
			beforeSend: function () {
				$('#load_id_dept').show();
			},
            success: function (res) {
              global_departments = res;
              $('#id_dept').prepend('<option></option>').select2({
                data: res,
                allowClear: true,
              });
            },
			complete: function(){
				$('#load_id_dept').hide();
			},
        });
        return result;
    } catch (error) {
        get_dept();
    }	
}

$(document).on('change', '#id_dept', function (event, istrigger) {  
    if(!istrigger){
		global_dept = $(this).select2('val');
		get_grade($(this).select2('val'));
		get_respon($(this).select2('val'));
	}
});

const get_grade = async (id_dept) => {
	let result;
	let myData = {
		id_dept: id_dept,
	};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_grade') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_grade').empty();
				$('#id_pos').empty();
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
			},
        });
        return result;
    } catch (error) {
        get_grade(id_dept);
    }	
} 

$(document).on('change', '#id_grade', function (event, istrigger) {  
    if(!istrigger){
		global_grade = $(this).select2('val');
		get_position($('#id_dept').select2('val'),$(this).select2('val'));
	}
});

const get_position = async (id_dept,id_grade) => {
	let result;
	let myData = {
		id_dept: id_dept,
		id_grade: id_grade,
	};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_position') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_pos').empty();
				$('#load_id_pos').show();
			},
            success: function (res) {
				$('#id_pos').prepend('<option></option>').select2({
					data: res,
					allowClear: true,
				});
            },
			complete: function(){
				$('#load_id_pos').hide();
			},
        });
        return result;
    } catch (error) {
        get_position(id_dept,id_grade);
    }	
} 

/*
$(document).on('change', '#id_pos', function (event, istrigger) {  
    if(!istrigger){
//		global_id_position = $(this).select2('val');
	//	get_task($(this).select2('val'));
	}
});
*/

const get_respon = async (id_dept) => {
	let result;
	let myData = {
		id_dept: id_dept,
	};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_respon') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_respon').empty();
			},
            success: function (res) {
				$('#id_respon').select2({
					data: res,
				});
            },
        });
        return result;
    } catch (error) {
     //   get_branch();
    }	
} 

const get_employee_by = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_employee_by') ?>',
            dataType: 'json',
            success: function (res) {
              $('#id_managed').select2({
                data: res,
              }).trigger('change');
            },
        });
        return result;
    } catch (error) {
     //   get_employee_by();
    }	
}

/*
const get_task = async (id_pos) => {
	let result;
	let myData = {
		id_pos: id_pos,
	};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_task') ?>',
            method: "GET",
			data: myData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
				global_id_task = res;
				$('#table_rec_detail').find('.id_task_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).prepend('<option selected></option>').select2({
						placeholder: "Select Task ...",
						data: global_id_task,
					});
				});
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},	
        });
        return result;
    } catch (error) {
    }	
}

$(document).on('change', '.id_task_input', function(event, istrigger) {
	if(!istrigger){
		let idTask= $(this).val();
		get_activity($(this),idTask);
	}	
}); 

const get_activity = async (element,id_task,edit=null) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_activity') ?>',
            dataType: 'json',
			data: {
			  id_task: id_task
			},
			beforeSend: function () {
				element.closest('tr').find('.load_act_input').each(function (i, obj) {
					$('#' + obj.id).show();
				});
			},
            success: (res) => {
				  global_id_activity = res;
				  if(edit != 'edit'){
						element.closest('tr').find('.id_activity_input').empty().prepend('<option selected></option>').select2({
						data: res,
						placeholder: "Select Activity ...",
						allowClear: true,
					});					
				  }
				  else{
					element.closest('tr').find('.id_activity_input').select2({
						data: res,
						placeholder: "Select Activity ...",
						allowClear: true,
					});
				  }
			},
			complete: function(){
				element.closest('tr').find('.load_act_input').each(function (i, obj) {
					$('#' + obj.id).hide();
				});
			},
        });
        return result;
    } catch (error) {
    }	
} 

$(document).on('change', '.id_activity_input', function(event, istrigger) {
	if(!istrigger){
		let idAct= $(this).val();
		get_detail_act($(this).closest('tr'),idAct);
	}	
});

const get_detail_act = async (element,id_activity) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/get_detail_act') ?>',
            dataType: 'json',
			data: {
			  id_activity: id_activity
			},	
            success: function (res) {
				element.find('.random_type_input').val(res[0].random_object_flag);
				element.find('.evidence_input').html(res[0].target_evidence);
				element.find('.type_input').html(res[0].evidence_type);
				element.find('.cycle_input').html(res[0].task_cycle);
				element.find('.time_input').html(res[0].time);
				element.find('.score_input').html(res[0].maximum_score);
            },
        });
        return result;
    } catch (error) {
    }	
} 
*/
const get_list_datatable = async () => {
	
		let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
	
		let btnSubmit = {
			text: 'Submit',
			className: 'btn btn-success btn-md',
			action: function (e, dt, node, config) {
				let id_task = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
					return $(entry).attr('id_task');
				});
				if(id_task.length > 0){
					$('#loader').removeClass('hidden');
					get_task_list(id_task).then(function(res) {
						let x = [];
						let z = [];
						if($('#table_rec_body tr').length == 0){
							$.each(res, function (i, item) {
								$('#new_rec_detail').trigger('click');
							});
						}
						else{						
							$('#table_rec_body tr').each(function (index) {
								siz_emp = $(this).find('.id_act_input').attr('id_activity');
								x.push(parseInt(siz_emp));												
							});
							$.each(res, function (i, item) {									
								if ($.inArray(parseInt(item.id_activity), x) == -1){
									$('#new_rec_detail').trigger('click');
								}
							});		
						}
						$('#table_rec_body tr').each(function (index) {
							siz_id = $(this).attr('id_record');
							z.push(siz_id);											
						});
						global_z = z.slice(-(res.length));
					//	get_task_list(id_task).then(function(res) {
							setTimeout(function () {
								$.each(global_z, function (i, item) {
									if ($.inArray(res[i].id_task, x) == -1){
										$('#task_'+item+'_id_task').val(res[i].id_task).trigger('change',[true]);
										$('#task_'+item+'_id_activity').val(res[i].id_activity).trigger('change',[true]);
										$('#task_'+item+'_id_act').attr('id_activity',res[i].id_activity).trigger('change',[true]);
										$('#task_'+item+'_task_text').html(res[i].name_task);
										$('#task_'+item+'_activity_text').html(res[i].activity);
										
										$('#task_'+item+'_evidence').html(res[i].target_evidence);
										$('#task_'+item+'_type').html(res[i].evidence_type);
										$('#task_'+item+'_cycle').html(res[i].task_cycle);
										$('#task_'+item+'_time').html(res[i].time);
										$('#task_'+item+'_score').html(res[i].maximum_score);
									/*	get_activity($('#task_'+item+'_id_task'),res[i].id_task).then(function(val) {
											$('#task_'+item+'_id_activity').val(res[i].id_activity).trigger('change',[true]);
											$('#task_'+item+'_id_act').attr('id_activity',res[i].id_activity);
											get_detail_act($('#task_'+item+'_id_activity').closest('tr'),res[i].id_activity).then(function(value) {
												
											}); 	
										});
									*/
									}
								}); 	
								$('#loader').addClass('hidden');
							}, 500);
					//	});  
						$("#modal_second").removeClass("modal-backdrop fade show");
						$("#modal_browse").modal('hide'); 
					});
				} else {
					swal({
						icon: 'warning',
						title: 'Warning',
						text: 'Please Select Task List'
					});
				}
			}
		}
	
		dtButtons.push(btnSubmit);
		let myData = {
		//	id_pos: id_position,
			id_dept: id_dept,
			id_grade: id_grade,
		};
		var t = $('#list_table').DataTable({
			buttons: dtButtons,
			processing: true,
			destroy: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				left: 4,
			},
			ajax: {
				url: "{{ route('task.list_task') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				complete:function(){
					$('#loader').addClass('hidden');
				},
				error: function (jqXHR, textStatus, errorThrown) {
					//	$('#list_table').DataTable().ajax.reload();
				}
			},
			createdRow: function( row, data, dataIndex ) {
			  $(row).attr('id_task', data['id_task']);
			//  $(row).addClass('mass_id');
			},
			
			columns: [
				{   // Checkbox select column
				data: 'id_task',
				orderable: false,
				targets: 0,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
				{ data: 'name_task', name: 'name_task'},
				{ data: 'notes', name: 'notes'},
				{ data: 'task_type', name: 'task_type', className: 'text-center'},
			],
			"fnInitComplete": function (oSettings) {
				$('#list_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
				$('#list_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
			//	$('.ok_red').css("pointer-events","none");
			}
		});
		t.on('order.dt search.dt', function () {
			let i = 1;
			t.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
				this.data(i++);
			});
		}).draw();
		
		$('#advanced_list').click(function(){
			if($(".list_table").css('display') == 'none'){
				$(".list_table").show("slow");
			}
			else {
				$(".list_table").hide("slow");
			}		
		});
		
	}	

const get_task_list = async (id_task) => {
	let result;
	let myData = {
		//	id_pos: id_position,
			id_task: id_task,
		};
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/task_assignment/task_assign') ?>',
            dataType: 'json',
			data: myData,
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
    //    get_fpk();
    }	
}

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(id_recommendation_header) {
	let res = {
        id_recommendation_header: id_recommendation_header,
    };
    let param = objectToQueryString(res);
//	let url = "{{ url('employee/employee/recommendation_form/download') }}";
    window.open(url+'?'+param, '_blank');
}

function genTask(id_task_management) {
	$.ajax({
		url: "<?= url('task_management/missions/task_assignment/gen_task') ?>",
		method: "GET",
        data: {
			id_task_management: id_task_management,
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			if (response.status == 'true') {
				
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
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
				text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
			});
		}
	});
}

</script>  
@endsection