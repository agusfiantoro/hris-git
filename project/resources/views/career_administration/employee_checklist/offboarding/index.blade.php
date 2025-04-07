@extends('adminlte::page')
@section('title', 'Off Boarding')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Off Boarding</h5>
                <div class="card-tools">
					<button type="button" class="btn btn-sm btn-primary" onclick="loadreport()"><i class="fas fa-file"></i> Report</button>
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Off Boarding</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="checklist_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
						<thead>
							<tr>				   
								<th></th>
								<th></th>
								<th data-priority="7">No</th>
								<th data-priority="2">NIK</th>
								<th data-priority="3">Name</th>
								<th data-priority="5">Checklist</th>
								<th data-priority="4">Effective Date</th>
								<th data-priority="6">Completed</th>
								<th>Status Doc</th>
								<th data-priority="1" width=80>Action</th>
							</tr>
						</thead>
					</table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_checklist"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="checklistForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Off Boarding</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
                                <div class="col-sm-8">
                                    <select name="id_employee" id="id_employee" class="select2 form-control form-control-sm" style="width:100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Checklist Type</label>
                                <div class="col-sm-8">
									<input name="id_checklist_employee" id="id_checklist_employee" type="hidden">
                                    <select name="id_checklist" id="id_checklist" class="select2 form-control form-control-sm" style="width:100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="id_checklistError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remark" id="remark" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
							<div class="row">
                                <label class="col-md-4 col-form-label">Assigned Position</label>
								<div class="col-md-8">
                                    <select name="assigned_hr[]" id="assigned_hr" class="form-control form-control-sm select2" data-placeholder="Select Assigned Position" style="width: 100%;" multiple="multiple">
									</select>
										<span class="invalid-feedback" role="alert" id="assigned_hrError">
											<strong></strong>
										</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Compensation (Rp)</label>
                                <div class="col-sm-8">
                                    <input type="text" name="compen" id="compen" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="compenError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>							
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<label class="custom-file-label" style="font-size:12px;"><i>Max 2 MB</i></label>
								</div>
								
							   </div>
							</div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Report Off Boarding</h5>
			<button type="button" onclick="javascript:window.location.reload()" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-default advclose" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

@endsection

@section('css')
<style type="text/css">
	td.text-approve{
		text-align:center;
	}
	select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #e9ecef;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_checklist = "";
let checklistTypeExit = [];
let checklistTypeKw = [];
let checklistTypeAll = [];
let typeClick = '';

function loadreport(){
	$("#contentBody").html('');
    $.ajax({
		url: "{{ route('offboarding.report') }}",
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}

	$('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html('<i>'+file.name+'</i>');
    });
    
    $(document).on('click', '.new', function () {
		typeClick= 'new';
        global_id_checklist = "";
        $("#checklistForm")[0].reset();
		$("#id_checklist").empty();
		$('#id_employee').attr('readonly',false);
		$("#id_employee").val('').trigger('change');
		$("#assigned_hr").val('').trigger('change');
        $("#checklistForm .modal-title").html("<span class='fas fa-plus'></span> Form Off Boarding");
        $(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
        $("#checklistForm input").removeClass("is-invalid");
        $("#checklistForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
		$("label.custom-file-label").html('<i>Max 2 MB</i>');
		// get_type();
        $('#modal_form_checklist').modal('show');
    });

    $(document).on('click', '.edit', function () {
		typeClick= 'edit';
        let id_checklist_employee = $(this).attr('id');
        global_id_checklist = id_checklist_employee;
        $("#checklistForm")[0].reset();
        $("#checklistForm .modal-title").html("<span class='fas fa-edit'></span> Edit Off Boarding");
        $(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
        $("#checklistForm input").removeClass("is-invalid");
        $("#checklistForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');
		$("label.custom-file-label").html('<i>Max 2 MB</i>');
        // get_type();
		
        $.ajax({
            url: "<?= url('career_administration/employee_checklist/offboarding/get_detail_offboarding') ?>",
            method: "GET",
            data: {id_checklist_employee: id_checklist_employee},
            success: function (response) {
				get_employee('edit').then(function(value) {
					$('#id_checklist_employee').val(response.id_checklist_employee).trigger('change');
				//	setTimeout(function () {
					checkClearence(response.id_employee).then(function(value) {
						$('#id_checklist').val(response.id_checklist).trigger('change');
					});
				//	}, 3000);
					$('#id_employee').val(response.id_employee).trigger('change', [true]);
					$('#remark').val(response.remark).trigger('change');
					$('#assigned_hr').val(response.assigned_hr).trigger('change', [true]);
					$('#effective_date').val(response.effective_date).trigger('change');
					if(response.compensation_amount != null){
						$('#compen').val(format(response.compensation_amount)).trigger('change');
					}
					$('#status').val(response.status).trigger('change');
					$('#id_checklist').attr('readonly',true);
					$('#id_employee').attr('readonly',true);
				});
				
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
        $('#modal_form_checklist').modal('show');
    });

    $(document).ready(function () {
		
		get_employee('new');
		get_hr();

		$('#effective_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
		
		$('#status').select2({width:'100%'});
		
     var table =   $('#checklist_table').DataTable({
            processing: true,
			responsive: true,
			pageLength: 10,
			lengthMenu: [
					[10, 20, 30, 50, 100, 200, -1],
					[10, 20, 30, 50, 100, 200, 'All']
			],
            ajax: {
                url: "<?= url('career_administration/employee_checklist/offboarding/get_data') . '?id_url=' ?>" + global_url_server,
				error: function (jqXHR, textStatus, errorThrown) {
					$('#checklist_table').DataTable().ajax.reload();
				}
            },
            columns: [
				{
                defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
                data: 'id_checklist_employee',
                defaultContent: '',
                orderable: false
				},
                 {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'nik_employee', name: 'nik_employee'},
				{data: 'employee_name', name: 'employee_name'},
                {data: 'document_name', name: 'document_name'},
                {data: 'effective_date', name: 'effective_date'},
				{ data: 'completed', name: 'completed', className: 'text-approve', render: function ( data, type, row ) {	
						if(data == false){
							return '<span class="badge badge-danger">NO</span>';
						}
						else{
							return '<span class="badge badge-success">YES</span>';							
						}
					}
				},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
                        return data;
                    }
                }
            ],
			"fnInitComplete": function (oSettings) {
				$('#checklist_table_wrapper .column-filter-widget:eq(8)').find("select option:contains('A')").attr('selected','selected').change();
			}
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

        $('#checklistForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $(".invalid-feedback").children("strong").text("");
			$(".invalid-date").children("strong").text("");
            $("#checklistForm input").removeClass("is-invalid");
            $("#checklistForm textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
				enctype: 'multipart/form-data',
				processData: false,  // Important!
				contentType: false,
				cache: false,
				url: global_id_checklist == '' ? "{{ route('offboarding.save') }}" : "{{ route('offboarding.update') }}",
                data: formData,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                   if (response.status == 'true') {
                        $('#modal_form_checklist').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        refresh_data();
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
					$('#loader').addClass('hidden')
				},
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function (key) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "Error").children("strong").text(errors[key][0]);
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                        });
                    }
                }
            });
        });
    });

function refresh_data() {
    $('#checklist_table').DataTable().ajax.reload();
}

const get_type = async () => {
    try {
        let result;
        result = await $.ajax({
            url: "{{ url('career_administration/employee_checklist/offboarding/get_type') }}",
            method: "GET",
			beforeSend: function () {
				$('#id_checklist').empty();
			},
            success: function (res) {
                $.each(res, function (i, val) {
                    if(i==0){
                        checklistTypeExit.push({id:val.id, text:val.text});
						 $('#id_checklist').html('').select2({
							placeholder: "Select Type",
							data: checklistTypeExit,
						});
                    }
					else if(i==1){
						checklistTypeKw.push({id:val.id, text:val.text});
						 $('#id_checklist').html('').select2({
							placeholder: "Select Type",
							data: checklistTypeKw,
						});
					}
					checklistTypeAll.push({id:val.id, text:val.text});
                });               
            },
        });
        return result;
    } catch (error) {
        get_type();
    }
}

const checkClearence = async (idEmployee='') => {
    try {
        let result;
        let optionByEmployee = checklistTypeExit;
        let optionByEmployeeKw = checklistTypeKw;
        $('#id_checklist').html('');

        result = await $.ajax({
            url: "{{ url('career_administration/employee_checklist/offboarding/check_clearence') }}",
            method: "GET",
            data:{id_employee:idEmployee},
            success: function (res) {
                if(res.status == true && res.data != null){
					if(typeClick == 'edit'){
						optionByEmployee = checklistTypeAll;
					}
					else{
						optionByEmployee = optionByEmployeeKw;
					}
                }			
                $('#id_checklist').prepend('<option selected></option>').select2({
                    placeholder: "Select Type",
                    data: optionByEmployee,
                });
            },
        });
        return result;
    } catch (error) {
        checkClearence(idEmployee);
    }
}

$(document).ready(function() {
    get_type()
});

// function get_type() {
// 	$.getJSON('<?= url('career_administration/employee_checklist/offboarding/get_type') ?>', function (data) {
// 		$('#id_checklist').select2({
// 			data: data,
// 			});
// 	}).fail(function (data) { // Call failed
// 		get_type();
// 	});	
// }	

const get_employee = async (type) => {
    try {
        let result;
        result = await $.ajax({
            url: "{{ url('career_administration/employee_checklist/offboarding/get_employee') }}",
            method: "GET",
            data:{type:type},
			beforeSend: function () {
				$('#id_employee').empty();
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
                 $('#id_employee').prepend('<option selected></option>').select2({
					placeholder: "Select Employee ..",
					allowClear: true,
					data: res,
				});
            },
			complete: function(){
				$('#loader').addClass('hidden')
			},
        });
        return result;
    } catch (error) {
        get_employee(type);
    }
}

/*
function get_employee() {
	$.getJSON('<?= url('career_administration/employee_checklist/offboarding/get_employee') ?>', function (data) {
		 $('#id_employee').prepend('<option selected></option>').select2({
			placeholder: "Select Employee ..",
			allowClear: true,
			data: data,
		});
	}).fail(function (data) { // Call failed
		get_employee();
	});	
}	
*/

function get_hr() {
	$.getJSON('<?= url('career_administration/employee_checklist/offboarding/get_hr') ?>', function (data) {
		$('#assigned_hr').select2({
			data: data,
		});
	}).fail(function (data) { // Call failed
		get_hr();
	});	
}	

$(function(){
	  $("#compen").keyup(function(e){
		if($(this).val() != null){ 
			$(this).val(format($(this).val()));
		}
	  });
	});
 function format(num){
      var str = num.toString().replace(/[^0-9]+/g, ""), parts = false, output = [], i = 1, formatted = null;    
      str = str.split("").reverse();
      for(var j = 0, len = str.length; j < len; j++) {
        if(str[j] != ",") {
          output.push(str[j]);
          if(i%3 == 0 && j < (len - 1)) {
            output.push(",");
          }
          i++;
        }
      }
  formatted = output.reverse().join("");
  return("" + formatted + ((parts) ? "." + parts[1].substr(0, 2) : ""));
};	

    $(document).on('change', '#id_employee', function (e, fromTrigger) {
		$('#id_checklist').empty();
		$('#id_checklist').attr('readonly',false);
        let idEmployee = $(this).val();
        let valCheck = null;
        if(idEmployee!=''){ 
			if(typeClick == 'new'){
				checkClearence(idEmployee);
			}
        }
    });
	
	$(document).on('change', '#id_checklist', function (e, istrigger) {
		$.each(checklistTypeAll, function (i, val) {
			if($('#id_checklist').val() == val.id){
				let textType = val.text;
				if(textType == 'Exit Clearance'){
					$('#compen').attr('readonly',true);
				}
				else{
					$('#compen').attr('readonly',false);
				}
			}
		});
    });

</script>
@endsection