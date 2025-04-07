@extends('adminlte::page')
@section('title', 'Course')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Course</h5>
                <div class="card-tools">
                    <button type="button" class="btn-upload-course btn btn-sm btn-success"><i class="fas fa-upload"></i> Upload Course</button>
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Course</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="course_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>			
                        <th></th>
						<th></th>
						<th>No</th>
						<th>Course Name</th>
						<!-- <th>Duration</th> -->
						<th>Notes</th>
						<th>Status</th>
						<th data-priority="2" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="courseForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Course</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Course Name</label>
								<div class="col-sm-8">
                                    <input type="hidden" name="id_course_header" id="id_course_header" class="form-control form-control-sm">
                                    <input type="text" name="course_name" id="course_name" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="course_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notes" id="notes" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="notesError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Duration</label>
                                <div class="col-sm-8">
                                    <input name="duration_time" id="duration_time" class="form-control form-control-sm timepicker">
                                    <span class="invalid-feedback" role="alert" id="duration_timeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_menu_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_course" data-toggle="tab" href="#tab_detail_description" role="tab" aria-controls="link_tab_course" aria-selected="true">Course Program <span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="" style="font-size:12px">
                                <div class="tab-pane fade show active" id="tab_course" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_detail"><span class="fas fa-plus"></span> Add Detail</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="">No.</th>
                                                        <th style="width: 180px;">Course Type</th>
                                                        <th style="width: 50px;">Sequence</th>
                                                        <th style="width: 250px;">Content Course</th>
                                                        <th style="width: 200px;">Notes</th>
                                                        <th style="width: 200px;">Number of Question</th>
                                                        <th style="min-width: 100px;">Weight Scale</th>
                                                        <th style="width: 135px;">Status</th>
                                                        <th style="width: 100px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_detailError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="on_close_modal()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			<div style="display:none;">
                <table id="table_detail_child">
                    <tr id="">
                        <td>
							<span class="sn text-center" style="vertical-align:middle;"></span>
						</td>
                        <td>
								<input name="course[0][id_course_detail]" id="0_id_course_detail" type="hidden" class="form-control form-control-sm id_course_input">
                                <select name="course[0][course_type]" id="course_0_course_type" data-id_course_type="0" class="form-control form-control-sm select2 course_type_input course_type_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback course_type_input_error" role="alert" id="0_course_typeError">
                                    <strong></strong>
                                </span>				
                        </td>
                        <td>
                                <input name="course[0][sequence]" id="course_0_sequence" type="text" class="form-control form-control-sm sequence_input text-center">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="0_sequenceError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                                <select name="course[0][program]" id="course_0_program" class="form-control form-control-sm select2 program_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback program_input_error" role="alert" id="0_programError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                                <input name="course[0][notes]" id="course_0_notes" type="text" class="form-control form-control-sm notes_input">
                                <span class="invalid-feedback notes_input_error" role="alert" id="0_notesError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                            <div class="list_question_view" data-question="0">
                                <select name="course[0][question_view]" id="course_0_question_view" class="form-control form-control-sm select2 question_view_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback question_view_input_error" role="alert" id="0_question_viewError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div class="list_weight" data-question="0">
                                <input name="course[0][weight]" id="course_0_weight" type="text" class="form-control form-control-sm weight_input">
                                <span class="invalid-feedback weight_input_error" role="alert" id="0_weightError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                                <select name="course[0][status]" id="course_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="0_statusError">
                                    <strong></strong>
                                </span>
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


<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="courseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="fas fa-plus"></span> Batch Upload EPSTP Course Form
                </h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form method="post" id="courseEpstpForm">
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Attachment</label>
                        <div class="col-sm-8">
                            {{ csrf_field() }}
                            <input type="file" name="attachment" id="course_attachment" class="form-control-file form-control-sm">
                            <span class="invalid-feedback" role="alert" id="course_attachmentError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
             <button type="button" name="import_button" id="import_button" class="btn btn-success" onclick="$('#courseEpstpForm').submit()">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="loadingModal" class="loading fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">              
            </div>
            <div class="modal-body">
               <i class="fa fa-refresh fa-pulse"></i>
            </div>
        </div>
    </div>
</div>

<div id="duplicateCourseModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title-duplicate">Duplicate Course</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col">
						<label>Course Name</label>
					</div>
					<div class="col">
						<input type="hidden" name="id_course_header" id="duplicateIdCourseHeader">
						<input type="text" name="course_name" id="duplicateCourseName" style="width:200px;" class="form-control form-control-sm">
					</div>
				</div>
				<div class="row mt-1">
					<div class="col">
						<label>Company</label>
					</div>
					<div class="col">
						<select name="id_company" id="duplicateCourseCompany" style="width:200px;"></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="duplicateCourse" class="btn btn-primary">OK</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</div>
	</div>
</div>

@endsection

@section('css')
<style type="text/css">	
    .modal { overflow: auto !important; }
li.select2-results__option strong.select2-results__group:hover {
  background-color: #6a6a6a;
  color:#fff;
  cursor: pointer;
}
.without::-webkit-datetime-edit-ampm-field {
   display: none;
}
input[type="time"]::-webkit-calendar-picker-indicator { background: none; display:none; }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_course_header = "";
    let global_id_detail = 0;
    let global_course_materi = [];
    let global_course_quiz = [];
    let global_course_quiz_pretest = [];
    let global_course_quiz_posttest = [];
    let global_course_quiz_remidial = [];
    let x = [];
    let today = new Date().toISOString().slice(0, 10)
    let course_type = [
        {id: 'Materi',text: 'Materi'},
        {id: 'Quiz_Pretest',text: 'Quiz Pre-Test'},
        {id: 'Quiz_Posttest',text: 'Quiz Post-Test'},
        {id: 'Quiz_Remidial',text: 'Quiz Remidial'},
        {id: 'Quiz',text: 'Quiz'},
    ];
    let status = [
        {id: 'A',text: 'Active'},
        {id: 'I',text: 'Inactive'},
    ];
    let listQuestion = [
        {id: 1,text: '  1  '},
        {id: 3,text: '  3  '},
        {id: 5,text: '  5  '},
        {id: 10,text: '  10  '},
        {id: 15,text: '  15  '},
        {id: 20,text: '  20  '},
        {id: 25,text: '  25  '},
    ];

    $(function () {	
        get_company_all();
		$('.summernote').summernote({
            height:300,
        });

        $('.timepicker').timepicker({
            uiLibrary: 'bootstrap4',
            format: 'HH:MM',
            mode: '24hr'
        }); 

    	$(document).on('click', '.new', function () {
            run_in_modal();
            global_id_course_header = "";
            $('.summernote').summernote('reset');
            $("#courseForm")[0].reset();
            $("#company").val('').trigger('change');
            $("#table_body").html("");
            $("#courseForm .modal-title").html("<span class='fas fa-plus'></span> Form Course");
            $(".invalid-feedback").children("strong").text("");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#courseForm input").removeClass("is-invalid");
    		$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');			

            $('#modal_form').modal('show');
        });
    		
    	$('#courseForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#courseForm')[0]);

            $(".invalid-feedback").children("strong").text("");
            $("#courseForm input").removeClass("is-invalid");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: {
                    Accept: "application/json",
                },
                contentType:false,
                cache: false,
                processData:false,
    			url: global_id_course_header == '' ? "{{ route('course.save') }}" : "{{ route('course.update') }}",
                data: formData,
    			beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    if (response.status == 'true') {
    					 $('#modal_form').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(ok => {
                            window.location.reload();
    					});
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
                    $('#loader').addClass('hidden')
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
    							$("#tab_menu_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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

        $(document).on('click', '#new_detail', function () {
            var content = jQuery('#table_detail_child tr'),
                    size = global_id_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
    		
    		element.find('.id_course_input').attr('id', `course_${size}_id_course_detail`);
            element.find('.id_course_input').attr('name', `course[${size}][id_course_detail]`);

            element.find('.course_type_input').attr('id', `course_${size}_course_type`);
            element.find('.course_type_input').attr('name', `course[${size}][course_type]`);
            element.find('.course_type_input').attr('id_course_type', size);
            element.find('.course_type_input_error').attr('id', `course_${size}_course_typeError`);
            element.find('.course_type_input').select2({
                placeholder: "Select Course Type",
                allowClear: true,
                data: course_type
            });

            element.find('.sequence_input').attr('id', `course_${size}_sequence`);
            element.find('.sequence_input').attr('name', `course[${size}][sequence]`);
            element.find('.sequence_input_error').attr('id', `course_${size}_sequenceError`);

    		element.find('.program_input').attr('id', `course_${size}_program`);
            element.find('.program_input').attr('name', `course[${size}][program]`);
            element.find('.program_input_error').attr('id', `course_${size}_programError`);
            // element.find('.program_input').prepend('<option selected></option>').select2({
            element.find('.program_input').select2({
                placeholder: "Select Content",
                allowClear: true,
            });

            element.find('.notes_input').attr('id', `course_${size}_notes`);
            element.find('.notes_input').attr('name', `course[${size}][notes]`);
            element.find('.notes_input_error').attr('id', `course_${size}_notesError`);

            element.find('.list_question_view').attr('data-question', size);
            element.find('.question_view_input').attr('id', `course_${size}_question_view`);
            element.find('.question_view_input').attr('name', `course[${size}][question_view]`);
            element.find('.question_view_input_error').attr('id', `course_${size}_question_viewError`);
            element.find('.question_view_input').select2({
                placeholder: "Select Number",
                allowClear: true,
                data: listQuestion
            });

            element.find('.list_weight').attr('data-question', size);
            element.find('.weight_input').attr('id', `course_${size}_weight`);
            element.find('.weight_input').attr('name', `course[${size}][weight]`);
            element.find('.weight_input_error').attr('id', `course_${size}_weightError`);

            element.find('.status_input').attr('id', `course_${size}_status`);
            element.find('.status_input').attr('name', `course[${size}][status]`);
            element.find('.status_input_error').attr('id', `course_${size}_statusError`);
            element.find('.status_input').select2({
                placeholder: "Select Status",
                allowClear: true,
                data: status
            });
    		
            element.appendTo('#table_body');

            let this_course_type        = element.find(`#course_${size}_course_type`).val();
            let this_element_program    = `#course_${size}_program`;
            if(this_course_type == 'Materi'){
                get_program(this_course_type, this_element_program);
                setBobotByCourseType(this_course_type, size)
            } else {
                get_program(this_course_type, this_element_program);
                setBobotByCourseType(this_course_type, size)
            }

    		$('#table_body tr').each(function (index) {
                $(this).find('td:eq(0)').addClass('text-center');
                $(this).find('span.sn').html(index + 1);
                $(this).find('input.sequence_input').val(index + 1);
            });
        });

    	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
                $(this).find('input.sequence_input').val(index + 1);
            });
            return true;
        });

        $(document).on('click', '.edit', function () {
            run_in_modal();

            let id_course_header = $(this).attr('id');
            global_id_course_header = id_course_header;
            $("#courseForm")[0].reset();
            $("#table_body").html("");
            $("#courseForm .modal-title").html("<span class='fas fa-edit'></span> Form Course");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#courseForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "{{ route('course.get_course_detail') }}",
                method: "GET",
                data: {id_course_header: id_course_header},
    			beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    global_id_detail = 0;					
                    $.each(response.course, function (i, item) {
                        $('#new_detail').trigger('click');
                    });
                    let duration = response.duration_time==null ? '' : moment(response.duration_time, 'HH:mm').format("HH:mm");
                    
                    $('#id_course_header').val(response.id_course_header);
                    $('#course_name').val(response.course_name);
                    $('#notes').val(response.notes);
                    $('#duration_time').val(duration);
                    $('#status').val(response.status).trigger('change');

                    $('#table_body tr').each(function (index) {
                        $(this).find('span.sn').html(index + 1);
                        let element_program = `#course_${index}_program`;
                        let this_index      = response.course[index];

                        $(this).find(`#course_${index}_id_course_detail`).val(this_index.id_course_detail);
                        $(this).find(`#course_${index}_course_type`).val(this_index.course_type).trigger('change');
                        $(this).find(`#course_${index}_sequence`).val(this_index.sequence);
                        $(this).find(`#course_${index}_notes`).val(this_index.notes);
                        $(this).find(`#course_${index}_status`).val(this_index.status).trigger('change');

                        if(this_index.course_type == 'Materi'){
                            get_program(this_index.course_type, element_program, this_index.id_content_learning);
                            setBobotByCourseType(this_index.course_type, index)
                        } else {
                            get_program(this_index.course_type, element_program, this_index.id_survey_header);
                            setBobotByCourseType(this_index.course_type, index)
                            $(this).find(`#course_${index}_question_view`).val(this_index.question_view).trigger('change');
                            $(this).find(`#course_${index}_weight`).val(this_index.weight);
                        }
                        $(this).find(`.delete-record[data-id="${index}"]`).hide();
                    });
    			},
                complete: function(){
                    $('#loader').addClass('hidden')
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
    	
            $('#modal_form').modal('show');
        });

    });

    $(document).ready(function(){
        get_select_materi()
        get_select_quiz()

        $('#course_table').DataTable({
            processing: true,
            responsive: true,
        //    serverSide: true,
            ajax: {
                url: "{{ route('course.index') }}",
                error: function (jqXHR, textStatus, errorThrown) {
					$('#course_table').DataTable().ajax.reload();
				}
    		},
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_course_header',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
    			{ data: 'course_name', name: 'course_name'},
                { data: 'notes', name: 'notes'},
    			// { data: 'duration_time', name: 'duration_time', 
                //     render: function ( data, type, row ) { 
                //         if(data==null){
                //             return '';
                //         } else {
                //             return moment(row.duration_time, 'HH:mm').format("HH:mm");
                //         }
                //     }
                // },
    			{ data: 'status', name: 'status'},
    			{ data: 'action', name: 'action', orderable: false },
            ],
            "rowCallback": function(row, val, index) {

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

        $(document).on('click', '.delete', function (event) {
        	id_course_header = $(this).attr('id');
            event.preventDefault();
            swal({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanently deleted!',
                icon: 'warning',
                buttons: true,
        		dangerMode: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
        		confirmButtonText: 'Yes, delete it!'
            }).then(function(value) {
                if (value) {
                    $.ajax({
                        url: "{{ route('course.destroy') }}",
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        method: "POST",
                        data: {id_course_header: id_course_header},
        			    success:function(data)
                        {
        				    setTimeout(function(){
        				    $('#confirmModal').modal('hide');
        				    swal({
            					title: "Data Deleted!",
            					icon: "success",
            					buttons: {confirm : {className:'btn-success'},},
            					}).then(ok => {
                                    window.location.reload();
            				    });
        				    }, 50);
                        }
                    })
                }
            });
        });
        
        $(document).on("change", "td .course_type_input", function(e)  { 
            let id_course_type = $(this).attr('id_course_type');
            let type = $(this).val();
            let element_name = `#course_${id_course_type}_program`;
            get_program(type, element_name);
            setBobotByCourseType(type, id_course_type)
        });

    });

    const get_select_materi = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/lms/course/get_course_materi') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        course_name = `${val.content_name} (${val.content_attachment_type})`;
                        global_course_materi.push({id:val.id_content_learning, text:course_name, notes:val.notes});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_select_materi();
        }
    }

    const get_select_quiz = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/lms/course/get_course_quiz') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res.quiz, function (i, val) {
                        quiz_name = `${val.quiz}`;
                        global_course_quiz.push({id:val.id_survey_header, text:quiz_name, notes:val.notes});
                    }); 
                    $.each(res.quiz_pretest, function (i, val) {
                        quiz_name = `${val.quiz}`;
                        global_course_quiz_pretest.push({id:val.id_survey_header, text:quiz_name, notes:val.notes});
                    }); 
                    $.each(res.quiz_posttest, function (i, val) {
                        quiz_name = `${val.quiz}`;
                        global_course_quiz_posttest.push({id:val.id_survey_header, text:quiz_name, notes:val.notes});
                    }); 
                    $.each(res.quiz_remidial, function (i, val) {
                        quiz_name = `${val.quiz}`;
                        global_course_quiz_remidial.push({id:val.id_survey_header, text:quiz_name, notes:val.notes});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_select_quiz();
        }
    }

    function run_in_modal() {
		$('#status').select2({
            placeholder: "Select Status",
            data: status,
            allowClear: true,
        });	
        $('#course_table').DataTable().ajax.reload();
    }

    function on_close_modal() {
        $('#course_table').DataTable().ajax.reload();
        global_id_course_header = '';
    }

    function get_program(type, element_name, value='') {
        if(type=='Materi'){
            get_course_materi(element_name, value)
        } else {
            get_course_quiz(type, element_name, value)
        } 
    } 

    function get_course_materi(element, value='') {
        $(element).html('').prepend('<option selected></option>').select2({
            placeholder: "Select Content",
            data: global_course_materi,
            allowClear: true,
        });
        if(value!=''){
            $(element).val(value).trigger('change');
        }
    } 

    function get_course_quiz(type, element, value='') {
        let dropdownData = [];
        if(type=='Quiz_Pretest'){
            dropdownData = global_course_quiz_pretest;
            thisPlaceHolder = 'Select Quiz Pre-Test';
        } else if(type=='Quiz_Posttest'){
            dropdownData = global_course_quiz_posttest;
            thisPlaceHolder = 'Select Quiz Post-Test';
        } else if(type=='Quiz_Remidial'){
            dropdownData = global_course_quiz_remidial;
            thisPlaceHolder = 'Select Quiz Remidial';
        } else {
            dropdownData = global_course_quiz;
            thisPlaceHolder = 'Select Quiz';
        }
        
        $(element).html('').prepend('<option selected></option>').select2({
            placeholder: thisPlaceHolder,
            data: dropdownData,
            allowClear: true,
        });
        if(value!=''){
            $(element).val(value).trigger('change');
        }
    }   

    function clear_course_type(element) {
        $(element).html('').prepend('<option selected></option>').select2({
            placeholder: "Select Content",
            allowClear: true,
        });
    } 

    function setBobotByCourseType(type, question) {
        if(type=='Materi'){
            $(`.list_question_view[data-question="${question}"]`).hide();
            $(`.list_weight[data-question="${question}"]`).hide();
        } else {
            $(`.list_question_view[data-question="${question}"]`).show();
            $(`.list_weight[data-question="${question}"]`).show();
        }
    } 

    $(document).on('click', '.btn-upload-course', function () {
        $('#courseModal').modal('show');
    });

    $(document).on('submit', '#courseEpstpForm', function(event) {
        event.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: "{{ route('event_course.submit') }}",
            type: 'POST',
            dataType: "JSON",
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: function (data, status) {
                $('#loader').addClass('hidden');
                swal({
                    title: "Success!",
                    text: data.message,
                    icon: "success",
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(error) {
                $('#loader').addClass('hidden');
                swal({
                    title: "Error!",
                    text: error.responseJSON.message,
                    icon: "error",
                });
            }
        });
    });

    $(document).on('change', '.program_input', function() {
        let type = $(this).parent().parent().find('.course_type_input').val()
        if($(this).parent().parent().find('.notes_input').val() == '') {
            let notes = '';
            if(type == 'Quiz_Pretest') {
                global_course_quiz_pretest.forEach((val) => {
                    if(val.id == $(this).val()) {
                        notes = val.notes;
                    }
                })
            } else if(type == 'Quiz_Posttest') {
                global_course_quiz_posttest.forEach((val) => {
                    if(val.id == $(this).val()) {
                        notes = val.notes;
                    }
                })
            } else if(type == 'Quiz_Remidial') {
                global_course_quiz_remidial.forEach((val) => {
                    if(val.id == $(this).val()) {
                        notes = val.notes;
                    }
                })
            } else if(type == 'Quiz') {
                global_course_quiz.forEach((val) => {
                    if(val.id == $(this).val()) {
                        notes = val.notes;
                    }
                })
            } else if(type == 'Materi') {
                global_course_materi.forEach((val) => {
                    if(val.id == $(this).val()) {
                        notes = val.notes;
                    }
                })
            }
            $(this).parent().parent().find('.notes_input').val(notes);
        }
    })

    function get_company_all() {
		$.ajax({
			url: "{{route('quiz.get_company_all')}}",
			success: (res) => {
				$('#duplicateCourseCompany').select2({
					data: res,
				});
			}
		})
	}

    $(document).on('click', '.duplicate', function() {
		let id_course_header = $(this).attr('id-course-header');
		let description = $(this).attr('course-name');

		$('#duplicateIdCourseHeader').val(id_course_header);
		$('#duplicateCourseName').val(description + ' (Copy)');
		$('#duplicateCourseModal').modal('show');
	});

	$(document).on('click', '#duplicateCourse', function() {
		let id_course_header = $('#duplicateIdCourseHeader').val();
		let description = $('#duplicateCourseName').val();
		let id_company = $('#duplicateCourseCompany').val();
		$.ajax({
			url: "{{route('course.duplicate')}}",
            type: 'POST',
			data: {
                _token: "{{ csrf_token() }}",
				id_course_header,
				course_name: description,
				id_company
			},
			beforeSend: () => {
				$('#loader').removeClass('hidden');
			},
			complete: () => {
				$('#loader').addClass('hidden');
			},
			success: (res) => {
				swal({
					icon: 'success',
					title: 'Success',
					text: res.message
				}).then(() => {
					$('#course_table').DataTable().ajax.reload();
				});
				$('#duplicateCourseModal').modal('hide');
			},
			error: (err) => {
				swal({
					icon: 'error',
					text: err.responseJSON.message
				})
			}
		})
	})

    $(document).on('click', '.download-report', function() {
        window.open("{{ route('summary_event.download_course_report') }}?id_course_header="+$(this).data('course'));
    });
</script>
@endsection