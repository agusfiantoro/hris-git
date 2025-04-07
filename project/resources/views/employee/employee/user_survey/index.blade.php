@extends('adminlte::page')
@section('title', 'Manage Survey')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Manage Survey</h5>        
            </div>
            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="emp_survey_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Description</th>
                            <th>Request By</th>
                            <th>Question Type</th>
                            <th>Survey Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_emp_survey"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="emp_surveyForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Employee Survey</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
					
					</div>
					<hr/>
                </div>
                <div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>              
                </div>
            </form>
		</div>
	</div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">
let global_question_type = "";
let global_id_survey_header = "";
let global_id_question_header = "";
let global_id_answer_header = [];
let global_id_answer = "";
let global_id_question_detail = 0;
let global_answer = [];
//var formAnswerData = [];
 	
    $(function () {
		$(document).on('click', '.new', function () {
            global_id_survey_header = "";
            $("#emp_surveyForm")[0].reset();
            $("#table_question_body").html("");
            $("#emp_surveyForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Employee Survey");
            $(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#emp_surveyForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            $('#modal_form_emp_survey').modal('show');
        });
				
		$('#emp_surveyForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();	
            $(".invalid-feedback").children("strong").text("");
            $("#emp_surveyForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
			
				$.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
					url: global_id_survey_header == '' ? "{{ route('employee_survey.save') }}" : "{{ route('employee_survey.update') }}",				
					data: formData,				
                    success: function (response) {						
                        if (response.status == 'true') {
							 $('#modal_form_emp_survey').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
								   }
								);
	                        $('#emp_survey_table').DataTable().ajax.reload();							
							
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    },
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_question").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
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
    });


$(document).ready(function(){
	$('#emp_survey_table').DataTable({
            processing: true,
        //    serverSide: true,
            scrollY: true,
            ajax: {
				url: "{{ route('employee_survey.index') }}",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#emp_survey_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'description', name: 'description'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'question_type', name: 'question_type'},
                {data: 'survey_type', name: 'survey_type'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
						
					if(access_create == 0){
						$('.new').css('display', 'none');
					}	
					if(access_edit == 0){
						$('.edit').css('display', 'none');
					}		
					if(access_delete == 0){
						$('.delete').css('display', 'none');
					}
					if(access_print == 0){
						$('.print').css('display', 'none');
					}		
                        return data;
                    }
                },
            ]
        });

	refresh_data();
 
$(document).on('click', '.delete_answer', function (event) {
	id_answer = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, delete it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"master_employee_survey/destroy_answer/"+id_answer,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 get_answer();
				 $('#answer_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
				});
				}, 50);
			   }
			  })
        }
    });
});
 
$(document).on('click', '.delete', function (event) {
	id_survey_header = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, delete it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"master_employee_survey/destroy/"+id_survey_header,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
			//	 $('#emp_survey_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Deleted!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						location.reload();
				});
				}, 50);
			   }
			  })
        }
    });
});
 
});

function refresh_data() {
        /**************** Load Menu dropdown **************************/
		
		$('#select2status').select2({width:'100%'});
		$('#select2status_answer').select2({width:'100%'});
		$('#daterange').daterangepicker({
				uiLibrary: 'bootstrap4',
				autoApply: true,
				opens: 'center',
					locale: {
					  format: 'YYYY-MM-DD',
					  separator: '   to   ',
					  closeText: 'Clear',
					},
				}, function(start, end, label) {
				$("#start_date").val(start.format('YYYY-MM-DD'));
				$("#end_date").val(end.format('YYYY-MM-DD'));				
				});			
	
		get_company();
		get_employee();
		get_question_type();
		get_survey_type();
		get_answer();
	
        $('#emp_survey_table').DataTable().ajax.reload();
}

function get_survey_type() {
		$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_survey_type') ?>', function (data) {
            $('#id_survey_type').select2({
                data: data,
            });
			
        }).fail(function (data) { // Call failed
            get_survey_type();
        });
}
function get_question_type() {
		$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_question_type') ?>', function (data) {
            $('#id_question_type').select2({
                data: data,
            }).on('change', function (e) {
				global_question_type = $(this).select2('data')[0].code;
				if($(this).select2('data')[0].code == 'Essay'){							
					$('.add-record').css("display","none");
				//	$('#save_button').css("display","");
					$('#table_question').find('.suggested_answer_input').each(function (i, obj) {
                            $('#' + obj.id).select2({disabled: true});
                        });
				}
				else{
					$('.add-record').css("display","");
				//	$('#save_button').css("display","none");
					$('#table_question').find('.suggested_answer_input').each(function (i, obj) {
                            $('#' + obj.id).select2({disabled: false});
                        });
				}				
			}).trigger('change');
			
        }).fail(function (data) { // Call failed
            get_question_type();
        });
}
function get_employee() {
		$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_employee') ?>', function (data) {
            $('#id_employee_request').select2({
                data: data,
            });
			
        }).fail(function (data) { // Call failed
            get_employee();
        });
}
function get_company() {
		$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });
			$('#company_answer').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_company();
        });	
}
function get_answer(){
		$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_answer') ?>', function (data) {
            global_answer = data;
        }).fail(function (data) { // Call failed
            get_answer();
		});
	}
</script>
@endsection