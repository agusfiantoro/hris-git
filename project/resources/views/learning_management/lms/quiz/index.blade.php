@extends('adminlte::page')
@section('title', 'Quiz')

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card card-danger card-outline">
			<div class="card-header">
				<h5 class="card-title">Quiz</h5>
				<div class="card-tools">
					<button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Quiz</button>
				</div>
			</div>

			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br><br>
				<table id="table_quiz" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					<thead>
						<tr>
							<th></th>
							<th></th>
							<th>No</th>
							<th>Quiz Name</th>
							<th>Notes</th>
							<th>Request By</th>
							<!-- <th>Start Date</th>
							<th>End Date</th> -->
							<!-- <th>Published</th> -->
							<th>Status</th>
							<th data-priority="2" style="text-align:center;" width=100>Action</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="modal_quiz" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<form method="post" id="quizForm">
				{{ csrf_field() }}
				<div class="modal-header">
					<h5 class="modal-title">Form Quiz</h5>
					<button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Quiz Name</label>
								<div class="col-sm-8">
									<input name="id_survey_header" id="id_survey_header" type="hidden">
									<input type="text" name="description" id="description" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="descriptionError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
                                <div class="col-sm-8">
                                    <select name="id_survey_type" id="id_survey_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_survey_typeError">
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
                            <!-- <div class="row">
                                <label class="col-sm-4 col-form-label">Start Date to End Date</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
                                        <input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
                                        <input name="end_date" id="end_date" class="form-control form-control-sm" hidden>
                                        <div class="input-group-append">
                                            <span class="input-group-text far fa-calendar form-control-sm"></span>
                                        </div>
                                        <span class="invalid-feedback" role="alert" id="daterangeError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </div> -->
							<!-- <div class="row">
								<label class="col-sm-4 col-form-label">Published</label>
								<div class="col-sm-8">
									<input type="checkbox" name="published" id="published" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
									<span class="invalid-feedback" role="alert" id="publishedError">
										<strong></strong>
									</span>
								</div>
							</div> -->
							<div class="row">
								<label class="col-sm-4 col-form-label">Request By</label>
								<div class="col-sm-8">
									<select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									<span class="invalid-feedback" role="alert" id="id_employee_requestError">
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
					<hr />
					<div class="row">
						<div class="col-md-12">
							<ul class="nav nav-tabs" id="tab_question" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Question Detail<span class="error-tab text-red"></span></a>
								</li>

							</ul>
							<div class="tab-content" id="tab_question_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_question_detail"><span class="fas fa-plus"></span> Add Question</button>
						                    <!-- <button type="button" class="new_answer pull-right btn btn-xs btn-sm btn-success" style="margin-right:10px;"><span class="fas fa-gear"></span> Manage Master Answer</button> -->
                                        </div>
                                        <div class="col-md-12" style="max-height:550px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_question" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th width=20>No.</th>
                                                        <th width=20>Sequence</th>
                                                        <th style="white-space:nowrap;min-width:150px;">Type</th>
                                                        <th style="white-space:nowrap;min-width:200px;">Question</th>
                                                        <th style="white-space:nowrap;min-width:450px;">Answer</th>
														<th style="white-space:nowrap;min-width:140px;">Status</th>
                                                        <th style="min-width:150px;">Note</th>
                                                        <th style="">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_question_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_questionError">
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
				<table id="sample_question">
					<tr id="">
						<td><span class="sn" style="vertical-align:middle;"></span></td>
						<td>
							<input name="question[0][id_survey_question]" id="question_0_id_survey_question" type="hidden" class="form-control form-control-sm id_survey_question_input">
							<input type="text" name="question[0][sequence]" id="question_0_sequence" class="form-control form-control-sm sequence_input">
							<span class="invalid-feedback sequence_input_error" role="alert" id="question_0_sequenceError">
								<strong></strong>
							</span>
						</td>
						<td>
							<select name="question[0][question_type][]" id="question_0_question_type" class="form-control form-control-sm select2 question_type_input" style="width:100%;" count-answer="0"></select>
							<span class="invalid-feedback question_type_input_error" role="alert" id="question_0_question_typeError">
								<strong></strong>
							</span>
						</td>
						<td>
							<textarea type="text" name="question[0][question]" id="question_0_question" class="form-control form-control-sm question_input" style="height:150px;"></textarea>
                            <span class="invalid-feedback question_input_error" role="alert" id="question_0_questionError">
                                <strong></strong>
                            </span>		
						</td>
						<td>
							<!-- <select name="question[0][suggested_answer][]" id="question_0_suggested_answer" class="form-control form-control-sm select2 suggested_answer_input" style="width:100%;" multiple="multiple"></select> -->
							<div class="list_answer" data-question="0">
								<input hidden name="question[0][id_answer][]" class="hidden_id_answer" id="id_answer_0_0">
								<div style="display: inline-flex;margin-bottom:5px;" class="tool_answer_" id="tool_answer_0_0">
									<select type="text" class="form-control form-control-sm select2 answer_status" name="question[0][answer_status][]" style="width:30%;" id="question_0_0_answer_status"></select>
									<span class="invalid-feedback answer_status_input_error" role="alert" id="question_0_0_answer_statusError"></span>

									<input type="text" name="question[0][score][]" id="question_0_0_score" class="form-control form-control-sm score_input" style="width:20%;" placeholder="Score">
									&nbsp; Correct Answer : <input type="checkbox" name="question[0][corrected_answer][]" id="question_0_0_corrected_answer" class="form-control form-control-sm corrected_answer_input" value="0" style="width:20%;">
								</div>
								<div style="display: inline-flex;margin-bottom:15px;" class="answer_" id="answer_0_0">
									<textarea type="text" name="question[0][suggested_answer][]" id="question_0_suggested_answer_0" class="form-control form-control-sm suggested_answer_input" data-answer="0"></textarea>
									<button type="button" class="delete_sub_answer btn btn-xs btn-danger btn-rounded" data-question="0" data-answer="0" title="Delete Answer"><span class="far fa-trash-alt"></span></button>
								</div>
								<span class="invalid-feedback suggested_answer_input_error" role="alert" id="question_0_0_suggested_answerError"></span>
							</div>
							<button style="" type="button" class="add_answer btn btn-xs btn-success btn-rounded" data-question="0" last-answer="0" title="Add Answer"><span class="fas fa-plus"></span></button>
						</div>

						</td>
						<td>
							<select name="question[0][question_status][]" id="question_0_question_status" class="form-control form-control-sm select2 question_status_input" style="width:100%;" ></select>
							<span class="invalid-feedback question_status_input_error" role="alert" id="question_0_question_statusError">
								<strong></strong>
							</span>
						</td>
						<td>
							<input type="text" name="question[0][note]" id="question_0_note" class="form-control form-control-sm note_input">
							<span class="invalid-feedback note_input_error" role="alert" id="question_0_noteError">
								<strong></strong>
							</span>
						</td>
						<td style="width:100px;vertical-align:middle;">
							<center>
								<!-- <button type="button" class="add-record btn btn-xs btn-primary" data-id="0" title="Save Answer" onclick="browse_answer(0)"><span class="far fa-list-alt"></span></button> -->
								<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"><span class="far fa-trash-alt"></span></button>
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

<div id="duplicateQuizModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-dialog-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title-duplicate">Duplicate Quiz</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col">
						<label>Quiz Name</label>
					</div>
					<div class="col">
						<input type="hidden" name="id_survey_header" id="duplicateIdSurveyHeader">
						<input type="text" name="description" id="duplicateQuizName" style="width:200px;" class="form-control form-control-sm">
					</div>
				</div>
				<div class="row mt-1">
					<div class="col">
						<label>Quiz Type</label>
					</div>
					<div class="col">
						<select name="quiz_type" id="duplicateQuizType" style="width:200px;"></select>
					</div>
				</div>
				<div class="row mt-1">
					<div class="col">
						<label>Company</label>
					</div>
					<div class="col">
						<select name="id_company" id="duplicateQuizCompany" style="width:200px;"></select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="duplicateQuiz" class="btn btn-primary">OK</button>
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
	let global_question_type = "";
	let global_id_survey_header = "";
	let global_id_question_header = "";
	let global_id_answer_header = [];
	let global_id_answer = "";
	let global_id_question_detail = 0;
	let global_id_user_session = 0;
	let global_answer = [];
	let question_type = [];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

	$(function() {
        get_company();
		get_company_all();
        get_employee();
        get_survey_type();
        // get_answer();
        get_question_type().then(function(value) {
	        global_question_type = value[0].code;
	        question_type = value;
	    });

		$(document).on('click', '.new', function() {
            run_in_modal();
            daterange();
            get_reff_number();

			global_id_survey_header = "";
			$("#quizForm")[0].reset();
			$("#table_question_body").html("");
			$("#quizForm .modal-title").html("<span class='fas fa-plus'></span> Form Quiz");
			$(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");
			$("#quizForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
			$('#save_button').html('<i class="fas fa-save"></i> Save');
			$('#modal_quiz').modal('show');
			$('#id_employee_request').prepend('<option selected></option>').val(global_id_user_session).trigger('change');
		});

		$(document).on('click', '.new_answer', function() {
		    newForm();
		});

		$('#quizForm').submit(function(e) {
			e.preventDefault();
			let formData = $(this).serializeArray();
			formData.push({ name: 'question_length', value: $('#quizForm').find('.question_input').length });
			$(".invalid-feedback").children("strong").text("");
			$("#quizForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");

			swal({
				title: 'Confirm Save Quiz',
				text: `Do you want to save ${$('#quizForm').find('.question_input').length} questions?`,
				buttons: ['No', 'Yes'],
			}).then((confirm) => {
				if(!confirm) return;
				$.ajax({
					type: 'POST',
					headers: {
						Accept: "application/json",
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					// url: global_id_survey_header == '' ? "{{ url('learning_management/lms/quiz/save') }}" : "{{ url('learning_management/lms/quiz/update') }}",
					url: "{{ url('learning_management/lms/quiz/save') }}",
					data: formData,
					success: function(response) {
						if (response.status == 'true') {
							$('#modal_quiz').modal('hide');
							swal({
								icon: 'success',
								title: 'Success',
								text: response.message
							}).then(function() {
								location.reload();
							});
							$('#table_quiz').DataTable().ajax.reload();

						} else {
							swal({
								icon: 'error',
								title: 'Oops...',
								dangerMode: true,
								text: 'Something went wrong! [Unknown Error]'+response.message
							});
						}
					},
					error: function(response) {
						if (response.status === 422) {
							let errors = response.responseJSON.errors;
							Object.keys(errors).forEach(function(key) {
								var key_temp = key.replaceAll(".", "_");
								$("#" + key_temp).addClass("is-invalid");
								$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
								var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_question").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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
			})

			
		});

		$(document).on('click', '#new_question_detail', function() {
			var content = jQuery('#sample_question tr'),
				size = global_id_question_detail++,
				element = null,
				element = content.clone();
			element.attr('id', 'rec-' + size);
			element.find('.delete-record').attr('data-id', size);
			// element.find('.add-record').attr('onclick', 'browse_answer(' + size + ')');
			element.find('.add-record').attr('data-id', size);

			element.find('.id_survey_question_input')
				.attr('id', 'question_' + size + '_id_survey_question')
				.attr('name', 'question[' + size + '][id_survey_question]')

			element.find('.sequence_input')
				.attr('id', 'question_' + size + '_sequence')
				.attr('name', 'question[' + size + '][sequence]');
			element.find('.sequence_input_error').attr('id', 'question_' + size + '_sequenceError');

			element.find('.question_type_input')
				.attr('id', `question_${size}_question_type`)
				.attr('name', `question[${size}][question_type]`)
				.attr('id_question_type', size)
				.attr('size', size);
				
		    element.find('.question_type_input_error').attr('id', `question_${size}_question_typeError`);

		    // $(`#question_${size}_question_type`).val($(`#question_0_question_type option:selected`).val()).trigger('change');
		    element.find('.count_answer').attr('id', `count_answer_${size}`);
		    element.find('.id_answer_').attr('id', `id_answer_${size}_0`);
		    element.find('.tool_answer_').attr('id', `tool_answer_${size}_0`);
		    element.find('.answer_').attr('id', `answer_${size}_0`);
		    element.find('.hidden_id_answer').attr('id', `id_answer_${size}_0`);


			element.find('.question_input').attr('id', 'question_' + size + '_question');
			element.find('.question_input').attr('name', 'question[' + size + '][question]');
			element.find('.question_input_error').attr('id', 'question_' + size + '_questionError');

			element.find(`#question_${size}_0_answer_status`).select2({
		        placeholder: "Select Status",
		        data: status
		    });
			element.find('.suggested_answer_input').attr('id', 'question_' + size + '_suggested_answer');
			element.find('.suggested_answer_input').attr('name', 'question[' + size + '][suggested_answer][]');
			element.find('.suggested_answer_input_error').attr('id', 'question_' + size + '_suggested_answerError');
			element.find('.suggested_answer_input_error').attr('id', 'question_' + size + '_suggested_answerError');
			element.find('.answer_status_input_error').attr('id', 'question_' + size + '_answer_statusError');

			element.find('.list_answer').attr('data-question', size);
			element.find('.add_answer').attr('data-question', size);
			element.find('.add_answer').attr('data-question', size);
			element.find('.add_answer').attr('data-question', size);
			
			element.find('.delete_sub_answer').attr('data-question', size);
			element.find('.corrected_answer_input').attr('id', 'question_' + size + '_0_corrected_answer');

			if (global_question_type == 'Essay') {
				element.find('.add-record').css("display", "none");
			} else {
				element.find('.add-record').css("display", "");
				element.find('.suggested_answer_input').css({
					height:50,
					width:400
				});
			}

			element.find('.question_status_input').attr('id', `question_${size}_question_status`);
		    element.find('.question_status_input').attr('name', `question[${size}][question_status]`);
		    element.find('.question_status_input').attr('size', size);
		    element.find('.question_status_input_error').attr('id', `question_${size}_question_statusError`);
		    element.find('.question_status_input').select2({
		        placeholder: "Select Status",
		        allowClear: true,
		        data: status
		    });

			element.find('.note_input').attr('id', 'question_' + size + '_note');
			element.find('.note_input').attr('name', 'question[' + size + '][note]');
			element.find('.note_input_error').attr('id', 'question_' + size + '_noteError');

			element.appendTo('#table_question_body');
			$('#table_question_body tr').each(function(index) {
				$(this).find('span.sn').html(index + 1);
        		$(this).find('input.sequence_input').val(index + 1);
			});

			$(`.list_answer[data-question="${size}"]`).html('');
			elementAnswer(size, 0, true);

			$(`#question_${size}_question_type`).select2({
		        placeholder: "Select Question Type",
		        allowClear: true,
		        data: question_type,
		        templateSelection: function (data, container) {
				    $(data.element).attr('data-code', data.code);
				    return data.text;
				},
		    });

			if(size > 0){
				$(`#question_${size}_question_type`).val($(`#question_${size-1}_question_type option:selected`).val()).trigger('change');
				$(`#question_${size}_question_status`).val($(`#question_${size-1}_question_status option:selected`).val()).trigger('change');
		    }

		});

		$(document).on('click', '.delete-record', function() {
			var id = jQuery(this).attr('data-id');
			var targetDiv = jQuery(this).attr('targetDiv');
			var id_survey_question = $("#question_" + id + "_id_survey_question").val();
			if (id_survey_question != "") {
				jQuery('#rec-' + id).remove();
			} else {
				jQuery('#rec-' + id).remove();
			}
			$('#table_question_body tr').each(function(index) {
				$(this).find('span.sn').html(index + 1);
        		$(this).find('input.sequence_input').val(index + 1);
			});
			return true;
		});

		$(document).on('click', '.edit', function() {
            run_in_modal();

			let id_survey_header = $(this).attr('id');
			global_id_survey_header = id_survey_header;
			$("#quizForm")[0].reset();
			$("#table_question_body").html("");
			$("#quizForm .modal-title").html("<span class='fas fa-edit'></span> Edit Form Quiz");
			$(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
			$(".error-tab").html("");
			$("#quizForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-primary');
			$('#save_button').html('<i class="fas fa-edit"></i> Update');

			$.ajax({
				url: "<?= url('learning_management/lms/quiz/get_survey_edit') ?>",
				method: "GET",
				data: {
					id_survey_header: id_survey_header
				},
				success: function(response) {
					global_id_question_detail = 0;
					$.each(response.question, function(i, item) {
						$('#new_question_detail').trigger('click');
					});

					$('#id_survey_header').val(response.id_survey_header);
                    // $('#reference_number').val(response.reference_number);
					$('#description').val(response.description);
					$('#notes').val(response.notes);
					$('#id_employee_request').val(response.id_employee_request).trigger('change');
					$('#id_survey_type').val(response.id_survey_type).trigger('change');
					$('#survey_category').val(response.survey_category).trigger('change');
					// $('#start_date').val(response.start_date).trigger('change');
					// $('#end_date').val(response.end_date).trigger('change');
					if (response.published == 1) {
						$('#published').prop('checked', true);
					} else {
						$('#published').prop('checked', false);
					}
					$('#id_company').val(response.id_company).trigger('change');
					$('#status').val(response.status).trigger('change');

					// setTimeout(function() {
						$('#table_question_body tr').each(function(index) {
							$(this).find('span.sn').html(index + 1);
							$(this).find('.id_survey_question_input').val(response.question[index].id_survey_question);
							$(this).find('.sequence_input').val(response.question[index].sequence);
							$(this).find('.question_type_input').val(response.question[index].id_question_type).trigger('change');
							$(this).find('.question_input').val(response.question[index].question);

							fillAnswer(response, index).then(res => {
								$(response.question[index].answers).each(function(i, val) {
									$(`#id_answer_${index}_${i}`).val(val.id_survey_answer);
									$(`#question_${index}_${i}_answer_status`).val(val.answer_status).trigger('change');
									$(`#question_${index}_${i}_score`).val(val.score_answer);

									if(val.is_corrected_answer == true){
										$(`#question_${index}_${i}_corrected_answer`).prop('checked', true);
									} else {
										$(`#question_${index}_${i}_corrected_answer`).prop('checked', false);
									}
									$(`#question_${index}_suggested_answer_${i}`).html(val.description_answer);

									if(index==0){
										if(i>0){
											countIndexAnswer = response.question[index].answers.length-1;
											delThisElement = i+(countIndexAnswer);
											$(`#question_${index}_question_type`).attr('count-answer', countIndexAnswer);
											$(`#answer_${index}_${delThisElement}`).remove();
											$(`#tool_answer_${index}_${delThisElement}`).remove();
											$(`.add_answer[data-question="${index}"]`).attr('last-answer', countIndexAnswer);
										}
									}
								});
							});
							
							$(this).find('.question_status_input').val(response.question[index].question_status).trigger('change');
							$(this).find('.note_input').val(response.question[index].note);
							$(this).find(`.delete-record[data-id="${index}"]`).hide();
						});
					// }, 500);
				},
				error: function(xhr) {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
					});
				}
			});

			$('#modal_quiz').modal('show');
		});
	});
	
	$(document).on('change', '.question_type_input', function() {
	    let size = $(this).attr('size');
	    let type = $(this).select2('data')[0].code;
	    let elementForChange = `#question_${size}_suggested_answer`;
	    let countAnswer = parseInt($(this).attr('count-answer'));

	    set_type_answer(elementForChange, type, size);
	    for (let i = 0; i <= countAnswer; i++) {
	    	if(type=='Single_Answer'){
		  		$(`#question_${size}_${i}_corrected_answer`).attr('type', 'radio')
			} else {
		  		$(`#question_${size}_${i}_corrected_answer`).attr('type', 'checkbox')
	    	}

			if(type == 'Upload_Files') {
				$(`#question_${size}_${i}_corrected_answer`).closest('td').children().hide();
	    	} else {
				$(`#question_${size}_${i}_corrected_answer`).closest('td').children().show();
			}
		}
	});

	$(document).ready(function() {
		$('.summernote').summernote({
	        height:120,
	    });
		$('#table_quiz').DataTable({
			processing: true,
            responsive: true,
			//    serverSide: true,
			scrollY: true,
			ajax: {
				url: "{{ route('quiz.index') }}",
				error: function(jqXHR, textStatus, errorThrown) {
					$('#table_quiz').DataTable().ajax.reload();
				}
			},
			columns: [
				{   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
				{
					data: null, defaultContent: '', orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'description', name: 'description' },
				{ data: 'notes', name: 'notes' },
				{ data: 'name', name: 'name' },
				// { data: 'start_date', name: 'start_date' },
				// { data: 'end_date', name: 'end_date' },
                // { data: 'published', name: 'published', 
                //     render: function ( data, type, row ) {  
                //         return is_published(row.published);
                //     } 
                // },
				{ data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false },
			],
            "rowCallback": function(row, val, index) {
                
            },
		});

		$('#advanced').click(function() {
			$('.cf').select2({
				width: '100%'
			});
			if ($("#cf").css('display') == 'none') {
				$("#cf").show("slow");
			} else {
				$("#cf").hide("slow");
			}
		});

		$.extend(true, $.fn.dataTable.defaults, {
			columnDefs: false,
			dom: "<'row'<'col-sm-6'l><'col-sm-5'f>>" +
				"<'row'<'col-sm-12'tr>>" +
				"<'row'<'col-sm-4'i><'col-sm-4 text-center'><'col-sm-4'p>>",
		});

		$('#suggested_image').change(function(){
	        let file = $("#suggested_image")[0].files[0]; 
	        $("label.custom-file-label").html(`<i>${file.name}</i>`);
	    });

		$(document).on('click', '.delete_answer', function(event) {
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
						url:  '<?= url('learning_management/lms/quiz/destroy_answer') ?>'+'/'+ id_answer,
						success: function(data) {
							if(data.status == 'true'){
	                            setTimeout(function() {
	                                $('#confirmModal').modal('hide');
	                                get_answer();
	                                $('#answer_table').DataTable().ajax.reload();
	                                swal({
	                                    title: "Data Deleted!",
	                                    icon: "success",
	                                    buttons: {
	                                        confirm: {
	                                            className: 'btn-success'
	                                        },
	                                    },
	                                }).then(ok => {});
	                            }, 50);
	                        } 
	                        else {
	                            swal({
	                                icon: 'error',
	                                dangerMode: true,
	                                content: {
	                                    element: "div",
	                                    attributes: {
	                                        innerText: data.message,
	                                        className: "swal-red",
	                                    },
	                                },
	                            })
	                        }
						}
					})
				}
			});
		});

		$(document).on('click', '.delete', function(event) {
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
                        url: "{{ route('quiz.destroy') }}",
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        method: "POST",
                        data: {id_survey_header: id_survey_header},
						success: function(data) {
							if(data.status == 'true'){
	                            setTimeout(function() {
	                                $('#confirmModal').modal('hide');
	                                //   $('#emp_survey_table').DataTable().ajax.reload();               
	                                swal({
	                                    title: "Data Deleted!",
	                                    icon: "success",
	                                    buttons: {
	                                        confirm: {
	                                            className: 'btn-success'
	                                        },
	                                    },
	                                }).then(ok => {
	                                    location.reload();
	                                });
	                            }, 50);
	                        } 
	                        else {
	                            swal({
	                                icon: 'error',
	                                dangerMode: true,
	                                content: {
	                                    element: "div",
	                                    attributes: {
	                                        innerText: data.message,
	                                        className: "swal-red",
	                                    },
	                                },
	                            })
	                        }
						},
	                    error: function(response) {
	                        if (response.status === 422) {
	                            let errors = response.responseJSON.errors;
	                            Object.keys(errors).forEach(function(key) {
	                                var key_temp = key.replaceAll(".", "_");
	                                $("#" + key_temp).addClass("is-invalid");
	                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
	                                var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
	                                if (tab_id != undefined) {
	                                    $("#tab_question").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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
					})
				}
			});
		});

	});
	
	$(document).on('click', '.reset', function(event) {
        newForm()
        $('#div_image').html('');
        $("label.custom-file-label").html(`<i>File (.jpg / .png / .svg)</i>`);
    });

	function run_in_modal() {
        $('#status').select2({
            placeholder: "Select Status",
            data: status,
            allowClear: true,
        }); 
		$('#select2status_answer').select2({
            data: status,
			width: '100%'
		});
		$('#table_quiz').DataTable().ajax.reload();
	}

	function get_survey_type() {
		$.getJSON('<?= url('learning_management/lms/quiz/get_survey_type') ?>', function(data) {
			$('#id_survey_type').select2({
				data: data,
			});
		}).fail(function(data) { // Call failed
			get_survey_type();
		});
	}

	function set_type_answer(element, type, question='') {
	    if(type == 'Essay'){
	    	$(`.list_answer[data-question="${question}"]`).hide();
	    	$(`.add_answer[data-question="${question}"]`).hide();
	        // $(element).next(".select2-container").hide();
	    } else {
	    	$(`.list_answer[data-question="${question}"]`).show();
	    	$(`.add_answer[data-question="${question}"]`).show();
	        // $(element).next(".select2-container").show();
	    }
	}  

	const get_question_type = async () => {
	    try {
	        let result;
	        result = await $.ajax({
	            url: '<?= url('learning_management/lms/quiz/get_question_type') ?>',
	            method: "GET",
	            success: function (res) {
	            },
	        });
	        return result;
	    } catch (error) {
	        get_question_type();
	    }
	}

	function get_employee() {
        $.getJSON('<?= url('learning_management/lms/quiz/get_employee') ?>', function (data) {
            $('#id_employee_request').prepend('<option selected></option>').select2({
                placeholder: "Select Employee Request",
                data: data,
                allowClear: true,
            });
            get_user_by_session.then(function(value) {
            	if(value.length > 0){
	            	global_id_user_session = value[0].id;
            	} else {
	            	global_id_user_session = '';
            	}
	        });
        }).fail(function (data) { // Call failed
            get_employee();
        }); 
    } 

	function get_company() {
		$.getJSON('<?= url('learning_management/lms/quiz/get_company') ?>', function(data) {
			$('#company').select2({
				data: data,
				disabled: true
			});
			$('#company_answer').select2({
				data: data,
				disabled: true
			});
		}).fail(function(data) { // Call failed
			get_company();
		});
	}

	function get_company_all() {
		$.ajax({
			url: "{{route('quiz.get_company_all')}}",
			success: (res) => {
				$('#duplicateQuizCompany').select2({
					data: res,
				});
			}
		})
	}

	function get_answer() {
		$.getJSON('<?= url('learning_management/lms/quiz/get_answer') ?>', function(data) {
			global_answer = data;
		}).fail(function(data) { // Call failed
			get_answer();
		});
	}

    function get_reff_number() {
        $.getJSON("{{ route('quiz.get_quiz_reff') }}", function (data) {
            $('#reference_number').val(data.reff).attr('readonly',true);
        }).fail(function (data) { // Call failed
            get_reff_number();
        }); 
    }   

    function daterange(start_date='', end_date='') {
        let separator = '   to   ';
        let start = (start_date=='' || start_date==null) ? moment() : start_date;
        let end = (end_date=='' || end_date==null) ? moment() : end_date;
        $('#daterange').daterangepicker({
            uiLibrary: 'bootstrap4',
            autoApply: true,
            opens: 'center',
            locale: {
                format: 'YYYY-MM-DD',
                separator: separator,
                closeText: 'Clear',
            },
            startDate: start, 
            endDate: end,
        }, function(start, end, label) {
            $("#start_date").val(start.format('YYYY-MM-DD'));
            $("#end_date").val(end.format('YYYY-MM-DD'));
        });
        
        if($("#start_date").val()=='' || $("#end_date").val()==''){
            $("#start_date").val(moment().format('YYYY-MM-DD'));
            $("#end_date").val(moment().format('YYYY-MM-DD'));
        } 
    }

    function on_close_modal() {
        $('#content_table').DataTable().ajax.reload();
    }

    function is_published(status) {
        let published;
        if(status=='0'){
            published = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
        } else {
            published = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
        }
        return published;
    }

    const get_user_by_session = new Promise(function(res) {
        $.getJSON("{{ route('quiz.get_user_by_session') }}", function (data) {
            res(data)
        }).fail(function (fail) { // Call failed
            get_user_by_session();
        }); 
    });

	function newForm() {
	    global_id_answer = "";
	    $("#answerForm")[0].reset();
	    $("#answerForm .modal-title").html("<span class='fas fa-gear'></span> Manage Master Answer");
	    $('#description_answer').summernote('code', '');
	    $(".invalid-feedback").children("strong").text("");
	    $(".table-invalid-feedback").children("strong").text("");
	    $(".error-tab").html("");
	    $("#answerForm input").removeClass("is-invalid");
	    $('#save_button_answer').attr('class', 'btn btn-sm btn-success');
	    $('#save_button_answer').html('<i class="fas fa-save"></i> Save');
	    $("#select2status_answer").val('A').trigger('change');
	    $('#modal_form_answer').modal('show');
	}

	const elementAnswer = async (thisQuestion, newAnswer, fromEdit=false) => {
		let deleteAnswer = '';
		if(fromEdit == false){
			deleteAnswer = `<button type="button" class="delete_sub_answer btn btn-xs btn-danger btn-rounded" data-question="${thisQuestion}" data-answer="${newAnswer}" title="Delete Answer"><span class="far fa-trash-alt"></span></button>`;
		}

		let element = `
		<input hidden name="question[${thisQuestion}][id_answer][]" class="hidden_id_answer" id="id_answer_${thisQuestion}_${newAnswer}">
		<div style="display: inline-flex;margin-bottom:5px;" class="tool_answer_" id="tool_answer_${thisQuestion}_${newAnswer}">
			<select type="text" class="form-control form-control-sm select2 answer_status" name="question[${thisQuestion}][answer_status][]" style="width:30%;" id="question_${thisQuestion}_${newAnswer}_answer_status"></select>

			<input type="text" name="question[${thisQuestion}][score][]" id="question_${thisQuestion}_${newAnswer}_score" class="form-control form-control-sm score_input" placeholder="Score" style="width:20%;">
			&nbsp; Correct Answer : <input type="checkbox" name="question[${thisQuestion}][corrected_answer][]" id="question_${thisQuestion}_${newAnswer}_corrected_answer" value="${newAnswer}" class="form-control form-control-sm corrected_answer_input" style="width:20%;">
		</div>
	   	<div style="display: inline-flex;margin-bottom:15px;" class="answer_" id="answer_${thisQuestion}_${newAnswer}">
			<textarea type="text" name="question[${thisQuestion}][suggested_answer][]" id="question_${thisQuestion}_suggested_answer_${newAnswer}" class="form-control form-control-sm suggested_answer_input" data-answer="${newAnswer}" style="height:50px;width:400px;" style="display:inline-block;"></textarea>
			${deleteAnswer}
		</div>
		<span class="invalid-feedback suggested_answer_input_error" role="alert" id="question_${thisQuestion}_suggested_answer_${newAnswer}Error"></span>
		`;
		// console.log(element);
		$(`.list_answer[data-question="${thisQuestion}"]`).append(element);
		$(`.add_answer[data-question="${thisQuestion}"]`).attr('last-answer', newAnswer);
		$(`#question_${thisQuestion}_${newAnswer}_answer_status`).select2({
            placeholder: "Select Status",
            data: status,
        }); 

		let selectedQuestionType = $(`#question_${thisQuestion}_question_type option:selected`).data('code');
        for (let i = 0; i <= newAnswer; i++) {
	    	if(selectedQuestionType=='Single_Answer'){
		  		$(`#question_${thisQuestion}_${i}_corrected_answer`).attr('type', 'radio')
	    	} else {
		  		$(`#question_${thisQuestion}_${i}_corrected_answer`).attr('type', 'checkbox')
	    	}
		}
	}

	const fillAnswer = async (data, index) => {
		$(data.question[index].answers).each(function(i, v) {
			lengthThisAnswer = data.question[index].answers.length;
			if(lengthThisAnswer > 1){
				// if(index > 0){
					if(lengthThisAnswer-1 != i){
					// console.log(index, i, v);
						$(`.add_answer[data-question="${index}"]`).trigger('click',[true]);
					}
				// }
			}
		});
	}

	$(document).on('click', '.add_answer', function(e, isTriggered) {
		let thisQuestion = $(this).attr('data-question');
		let newAnswer = parseInt($(this).attr('last-answer')) + 1;
		$(`#question_${thisQuestion}_question_type`).attr('count-answer', newAnswer);

        if (isTriggered==true){ 
			elementAnswer(thisQuestion, newAnswer, true)
		} else {
			// change by human
			elementAnswer(thisQuestion, newAnswer)
		}
	});

	$(document).on('click', '.delete_sub_answer', function() {
	    let thisQuestion = $(this).attr('data-question');
		let thisAnswer = parseInt($(this).attr('data-answer'));

		$(`#question_${thisQuestion}_question_type`).attr('count-answer', thisAnswer);
		$(`#answer_${thisQuestion}_${thisAnswer}`).remove();
		$(`#tool_answer_${thisQuestion}_${thisAnswer}`).remove();
		$(`.add_answer[data-question="${thisQuestion}"]`).attr('last-answer', thisAnswer);
	});

	$(document).on('click', '.duplicate', function() {
		let id_survey_header = $(this).attr('id');
		let quiz_type = $(this).attr('q-type');
		let description = $(this).attr('q-title');

		$('#duplicateIdSurveyHeader').val(id_survey_header);
		$('#duplicateQuizName').val(description + ' (Copy)');
		$('#duplicateQuizType').empty().select2({
			data: [
				{id: 'quiz', text: 'Quiz'},
				{id: 'quiz_pretest', text: 'Quiz Pre-Test'},
				{id: 'quiz_posttest', text: 'Quiz Post-Test'},
				{id: 'quiz_remidial', text: 'Quiz Remidial'}
			]
		}).val(quiz_type).trigger('change');
		$('#duplicateQuizModal').modal('show');
	});

	$(document).on('click', '#duplicateQuiz', function() {
		let id_survey_header = $('#duplicateIdSurveyHeader').val();
		let quiz_type = $('#duplicateQuizType').val();
		let description = $('#duplicateQuizName').val();
		let id_company = $('#duplicateQuizCompany').val();
		$.ajax({
			url: "{{route('quiz.duplicate')}}",
			data: {
				id_survey_header,
				quiz_type,
				description,
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
					$('#table_quiz').DataTable().ajax.reload();
				});
				$('#duplicateQuizModal').modal('hide');
			},
			error: (err) => {
				swal({
					icon: 'error',
					text: err.responseJSON.message
				})
			}
		})
	})

</script>
@endsection
