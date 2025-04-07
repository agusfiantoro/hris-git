@extends('adminlte::page')
@section('title', 'Training Scores')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card card-danger card-outline">
			<div class="card-header">
				<h5 class="card-title">Training Scores</h5>
				<div class="card-tools">
					{{-- <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Quiz</button> --}}
				</div>
			</div>

			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br><br>
				<table id="table_score" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					<thead>
						<tr>
							<th></th>
							<th>No</th>
							<th>Program</th>
							<th>Date</th>
							<th>Organized By</th>
                            <th>Venue</th>
							<th>Has Essay</th>
							<th data-priority="2" style="text-align:center;" width=100>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_score" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<form method="post" id="scoreForm">
				{{ csrf_field() }}
				<div class="modal-header">
					<h5 class="modal-title">Form Score</h5>
					<button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Program Name</label>
								<div class="col-sm-8">
									<input name="id_survey_header" id="id_survey_header" type="hidden">
									<input type="text" name="description" id="description" class="form-control form-control-sm" disabled>
									<span class="invalid-feedback" role="alert" id="descriptionError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Start-End Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_end_date" id="start_end_date" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="start_end_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Organized By</label>
								<div class="col-sm-8">
                                    <input type="text" name="organized_by" id="organized_by" class="form-control form-control-sm" disabled>
									<span class="invalid-feedback" role="alert" id="organized_byError">
										<strong></strong>
									</span>
								</div>
							</div>
                            {{-- <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div> --}}
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-md-12">
							<ul class="nav nav-tabs" id="tab_course" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Essay Answers<span class="error-tab text-red"></span></a>
								</li>

							</ul>
							<div class="tab-content" id="tab_question_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12 float-right" style="margin-bottom: 10px">
                                            <select name="filter_course" id="filter_course" class="form-control form-control-sm" multiple></select>
                                            <select name="filter_employee" id="filter_employee" class="form-control form-control-sm" multiple></select>
                                            <button type="button" class="pull-right btn btn-xs btn-success" id="modal_apply_filter"><span class="fas fa-filter"></span> Filter</button>
						                    <!-- <button type="button" class="new_answer pull-right btn btn-xs btn-sm btn-success" style="margin-right:10px;"><span class="fas fa-gear"></span> Manage Master Answer</button> -->
                                        </div>
                                        <div class="col-md-12" style="max-height:550px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_answers" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th width=20>No.</th>
                                                        <th width=20>Course</th>
                                                        <th style="white-space:nowrap;min-width:100px;">Description</th>
                                                        <th style="white-space:nowrap;min-width:175px;">Employee</th>
                                                        <th style="white-space:nowrap;min-width:250px;">Question</th>
                                                        <th style="white-space:nowrap;min-width:250px;">Answer</th>
														<th style="white-space:nowrap;min-width:100px;">Score</th>
                                                        <th style="">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_answers_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_answersError">
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
					<button type="button" class="btn btn-sm btn-secondary" onclick="on_close_modal()" data-dismiss="modal">Close</button>

				</div>
			</form>
			<div style="display:none;">
				<table id="">
					<tr id="sample_answers">
						<td><span class="sn" style="vertical-align:middle;"></span></td>
						<td>
							<input name="answer[0][id_survey_user]" id="answer_0_id_survey_user" type="hidden" class="form-control form-control-sm id_survey_user_input">
							<span class="course_name"></span>
							<span class="invalid-feedback sequence_input_error" role="alert" id="answer_0_sequenceError">
								<strong></strong>
							</span>
						</td>
						<td>
							<span class="course_description"></span>
							<span class="invalid-feedback question_type_input_error" role="alert" id="question_0_question_typeError">
								<strong></strong>
							</span>
						</td>
                        <td>
							<span class="quiz_employee"></span>
                            <span class="invalid-feedback question_input_error" role="alert" id="question_0_questionError">
                                <strong></strong>
                            </span>		
						</td>
						<td>
							<span class="quiz_question"></span>
                            <span class="invalid-feedback question_input_error" role="alert" id="question_0_questionError">
                                <strong></strong>
                            </span>		
						</td>
						<td>
							<span class="quiz_answer"></span>
						</td>
						<td>
							<input type="number" name="answer[0][essay_score]" id="answer_0_essay_score" class="form-control form-control-sm essay_score">
							<span class="invalid-feedback note_input_error" role="alert" id="answer_0_essay_scoreError">
								<strong></strong>
							</span>
						</td>
						<td style="width:100px;vertical-align:middle;">
							<center>
								<!-- <button type="button" class="add-record btn btn-xs btn-primary" data-id="0" title="Save Answer" onclick="browse_answer(0)"><span class="far fa-list-alt"></span></button> -->
								{{-- <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"><span class="far fa-trash-alt"></span></button> --}}
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

@endsection

@section('css')
<style type="text/css">
	select[readonly].select2-hidden-accessible+.select2-container {
		pointer-events: none;
		touch-action: none;
	}

	select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
		background: #eee;
		box-shadow: none;
	}

	select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
	select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
		display: none;
	}
</style>
@stop

@section('scripts')
<script type="text/javascript">
	var current_id_event_management = null;
    var current_courses = [];
    var current_employees = [];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    $('#table_score').DataTable({
        processing: true,
        responsive: true,
        //    serverSide: true,
        scrollY: true,
        ajax: {
            url: "{{ route('training_scores.index') }}",
            error: function(jqXHR, textStatus, errorThrown) {
                $('#table_score').DataTable().ajax.reload();
            }
        },
        columns: [
            {   // Detail Responsive
                data: '',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'description', name: 'description' },
            { data: 'start_date', name: 'start_date', render: function(data, type, row) {
                return `${row.start_date} to ${row.end_date}`;
            }},
            { data: 'organized_by', name: 'organized_by' },
            { data: 'venue', name: 'venue' },
            // { data: 'published', name: 'published', 
            //     render: function ( data, type, row ) {  
            //         return is_published(row.published);
            //     } 
            // },
            { data: 'has_essay', name: 'has_essay', render: function(data, type, row) {
                let badge = row.has_essay == true ? 'badge-success' : row.has_essay == false ? 'badge-danger' : 'font-bold';
                let text = row.has_essay == true ? 'Yes' : row.has_essay == false ? 'No' : row.has_essay;
                return `<span class="badge badge-sm ${badge}">${text}</span>`;
            }},
            { data: 'action', name: 'action', orderable: false },
        ],
        "rowCallback": function(row, val, index) {
            
        },
    });

    $(document).on('click', '#advanced', function() {
        $('.cf').select2({
            width: '100%'
        });
        if ($("#cf").css('display') == 'none') {
            $("#cf").show("slow");
        } else {
            $("#cf").hide("slow");
        }
    });

    function loadModal(idEventManagement, idCourseDetail = null, idEmployee = null) {
        current_id_event_management = idEventManagement;
        $.ajax({
            url: "{{ route('training_scores.get_data') }}",
            data: { 
                id_event_management: idEventManagement,
                id_course_detail: idCourseDetail,
                id_employee: idEmployee
            },
            beforeSend: () => {
                $('#loader').removeClass('#hidden');
            },
            error: () => loadModal(idEventManagement),
            success: (res) => {
                let pushCourse = current_courses.length < 1 ? true : false;
                let pushEmployee = current_employees.length < 1 ? true : false;
                if(pushCourse) {
                    $('#filter_course').empty().prepend('<option></option>');
                    current_courses = res.courses;
                }
                if(pushEmployee) {
                    $('#filter_employee').empty().prepend('<option></option>');
                    current_employees = res.employees;
                }
                $('#loader').addClass('#hidden');

                $('#description').val(res.data.description);
                $('#start_end_date').val(res.data.start_date+' to '+res.data.end_date);
                $('#organized_by').val(res.data.organized_by);

                $('#filter_course').select2({
                    allowClear: true,
                    placeholder: 'Select Course',
                    data: current_courses
                }).css('width', '300px');
                $('#filter_employee').select2({
                    allowClear: true,
                    placeholder: 'Select Employee',
                    data: current_employees
                }).css('width', '300px');

                $('#table_answers_body').empty();
                
                res.answers.forEach((answer, i) => {
                    let clone = $('#sample_answers').clone();
                    clone.addClass('cloned-row');
                    clone.find('.id_survey_user_input').val(answer.id_survey_user);
                    clone.find('.course_name').text(answer.course_name);
                    clone.find('.course_description').text(answer.description);
                    clone.find('.quiz_employee').text(answer.employee);
                    clone.find('.quiz_question').text(answer.question);
                    if(answer.code == 'Upload_Files') {
                        clone.find('.quiz_answer').html(`<button type="button" class="btn btn-sm btn-success btn-download" path="${answer.description_answer}"><i class="fas fa-download"></i></button>`);
                    } else {
                        clone.find('.quiz_answer').text(answer.description_answer);
                    }
                    clone.find('.essay_score').val(answer.essay_score);
                    $('#table_answers_body').append(clone);
                })

                $('#table_answers_body').find('.cloned-row').each((i, row) => {
                    $(row).find('.sn').text(i+1);
                    $(row).find('.id_survey_user_input').attr('name', `answer[${i}][id_survey_user]`);
                    $(row).find('.id_survey_user_input').attr('id', `answer_${i}_id_survey_user`);
                    $(row).find('.essay_score').attr('name', `answer[${i}][essay_score]`);
                    $(row).find('.essay_score').attr('id', `answer_${i}_essay_score`);
                })
                
                $('#modal_score').modal('show');
            }
        });
    }

    $(document).on('submit', '#scoreForm', function(event) {
        event.preventDefault();
        $.ajax({
            url: "{{ route('training_scores.save_score') }}",
            data: $(this).serialize(),
            type: 'POST',
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#loader').addClass('hidden');
                swal({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message
                }).then(() => {
                    on_close_modal('#scoreForm');
                    $('#table_score').DataTable().ajax.reload();
                })
            },
            error: (err) => {
                $('#loader').addClass('hidden');
                swal({
                    icon: 'error',
                    title: 'Error!',
                    dangerMode: true,
                    text: res.message
                });
            }
        })
    });

    $(document).on('click', '.btn-download', function() {
        let path = $(this).attr('path');
        window.open('/project/storage/app/public/'+path, '_blank');
    });

    $(document).on('click', '#modal_apply_filter', function() {
        loadModal(current_id_event_management, $('#filter_course').val(), $('#filter_employee').val());
    });

	function on_close_modal(element = null) {
        current_courses = [];
        current_employees = [];
        current_id_event_management = null;
        if(!element) $(this).closest('.modal').modal('hide');
        else $(element).closest('.modal').modal('hide');
    }

</script>
@endsection
