@extends('adminlte::page')
@section('title', 'Master Interview')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Interview</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Master Interview</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="interview_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Interview Type</th>
						<th>Description</th>
						<th>Module Type</th>
						<th>Status</th>
						<th data-priority="1" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_interview"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="interviewForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Interview</h5>
                    <button type="button" class="close" onclick="on_close_modal()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
									<input name="id_recruitment_question_group" id="id_recruitment_question_group" type="hidden">	
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Interview Type</label>
								<div class="col-sm-8">
									<select name="rec_stage" id="rec_stage" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									<span class="invalid-feedback" role="alert" id="rec_stageError">
										<strong></strong>
									</span>
								</div>
							</div>																					
                        </div>
						<div class="col-md-6">		
							<div class="row">
								<label class="col-sm-4 col-form-label">Module Type</label>
								<div class="col-sm-8">
									<select name="module_type" id="module_type" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									<span class="invalid-feedback" role="alert" id="module_typeError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm select2" style="width:100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
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
                            <ul class="nav nav-tabs" id="tab_question" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Question Detail <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_question_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_question_detail"><span class="fas fa-plus"></span> Add Question</button>
						                    <button type="button" class="new_answer pull-right btn btn-xs btn-sm btn-success" style="margin-right:10px;"><span class="fas fa-gear"></span> Manage Master Answer</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:350px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_question" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th width=20>No.</th>
                                                        <th width=20>Sequence</th>
                                                        <th style="min-width:150px;text-align:center;">Competency Name</th>
                                                        <th style="min-width:150px;text-align:center;">Group</th>
                                                        <th style="white-space:nowrap;min-width:150px;text-align:center;">Type</th>
                                                        <th style="white-space:nowrap;min-width:200px;text-align:center;">Question</th>
                                                        <th style="white-space:nowrap;min-width:150px;text-align:center;">Answer</th>
                                                        <th style="min-width:150px;text-align:center;">Note</th>
                                                        <th style="text-align:center;">Action</th>
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
                <table id="sample_table_question">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>   
						 <td>				
								<input name="question[0][id_recruitment_question]" id="question_0_id_recruitment_question" type="hidden" class="form-control form-control-sm id_recruitment_question_input">
                                <input type="text" name="question[0][sequence]" id="question_0_sequence" class="form-control form-control-sm sequence_input">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="question_0_sequenceError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>						
                                <input type="text" name="question[0][competency_group]" id="question_0_competency_group" class="form-control form-control-sm competency_group_input">
                                <span class="invalid-feedback competency_group_input_error" role="alert" id="question_0_competency_groupError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>						
                                <input type="text" name="question[0][category_group]" id="question_0_category_group" class="form-control form-control-sm category_group_input">
                                <span class="invalid-feedback category_group_input_error" role="alert" id="question_0_category_groupError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>
                                <select name="question[0][question_type]" id="question_0_question_type" class="form-control form-control-sm select2 question_type_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback question_type_input_error" role="alert" id="0_question_typeError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>																	
                                <textarea type="text" name="question[0][question]" id="question_0_question" class="form-control form-control-sm question_input" style="height:50px;"></textarea>
                                <span class="invalid-feedback question_input_error" role="alert" id="question_0_questionError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>																	
                                <select name="question[0][suggested_answer][]" id="question_0_suggested_answer" class="form-control form-control-sm select2 suggested_answer_input" style="width:100%;" multiple="multiple"></select>
                                <span class="invalid-feedback suggested_answer_input_error" role="alert" id="question_0_suggested_answerError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>						
                                <textarea type="text" name="question[0][note]" id="question_0_note" class="form-control form-control-sm note_input" style="height:50px;"></textarea>
                                <span class="invalid-feedback note_input_error" role="alert" id="question_0_noteError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td style="width:100px;vertical-align:middle;">
							<center>
								<button type="button" class="add-record btn btn-xs btn-primary" data-id="0"  id="question_0_add-record" onclick="browse_answer(0)"><span class="far fa-list-alt"></span></button>&nbsp;
								<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"><span class="far fa-trash-alt"></span></button>
							</center>
						</td>                       
                    </tr>
                </table>
            </div>
		</div>
	</div>
</div>

<div id="browseModalanswer" class="modal fade" role="dialog" style="z-index:1060;background-color: rgb(0, 0, 0, 0.5);">
 <div class="modal-dialog modal-md">
  <div class="modal-content"> 
   <div class="card-body">
			<form method="post" id="answer_detailForm">
                {{ csrf_field() }}
                <div class="modal-header">
				 <h5 class="modal-title">Manage Answer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-12">                          							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-8">								
									<input name="id_recruitment_question" id="id_recruitment_question" type="hidden">
                                    <input type="text" name="sequence" id="sequence" class="form-control form-control-sm" readonly>
                                    <span class="invalid-feedback" role="alert" id="sequenceError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Question</label>
                                <div class="col-sm-8">
									<input name="note" id="note" type="hidden">
                                    <input type="text" name="question" id="question" class="form-control form-control-sm" readonly>
                                    <span class="invalid-feedback" role="alert" id="questionError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<hr>
							<table id="table_detail_answer" class="table table-striped table-bordered table-hover datatable">
								<thead>
									<tr>
										<th width=200>Answer</th>
										<th style="white-space:nowrap;">Correct Answer</th>
										<th style="white-space:nowrap;">Score Answer</th>
									</tr>
								</thead>
								<tbody id="table_detail_answer_body">
								</tbody>
							</table>										
						</div>
					</div>
				<br>
					<button style="float:right;" type="button" class="add-answer pull-right btn btn-sm btn-success" id="save_button_survey_answer" onClick="check(0)"><i class="fas fa-save"></i> Save</button>
					<!-- button type="submit" class="btn btn-sm btn-success" id="save_button_survey_answer"><i class="fas fa-save"></i> Save</button -->

				<br>
				<hr>
                </div>				
			</form>
			<div style="display:none;">
                <table id="sample_table_detail_answer">
                    <tr id="">
						<td>	
							<input name="answer[0][id_recruitment_question_answer]" id="answer_0_id_recruitment_question_answer" type="hidden" class="form-control form-control-sm id_recruitment_question_answer_input">
							<input name="answer[0][id_recruitment_answer]" id="answer_0_id_recruitment_answer" type="hidden" class="form-control form-control-sm id_recruitment_answer_input">
                            <input type="text" name="answer[0][answer]" id="answer_0_answer" class="form-control form-control-sm answer_input" readonly>
                        </td>		
						<td>																	
                            <input type="checkbox" name="answer[0][correct]" id="answer_0_correct" class="form-control form-control-sm correct_input" style="height:20px;margin-top:5px;">
                        </td>	
						<td>																	
                            <input type="text" name="answer[0][score]" id="answer_0_score" class="form-control form-control-sm score_input">
                        </td>					                
                    </tr>
                </table>			
			</div>
	</div>
 </div>
    </div>
</div>	


<div class="modal fade" id="modal_form_answer"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 50% !important;">
        <div class="modal-content">
            <form method="post" id="answerForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Manage Master Answer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-12">   
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-2">
                                    <input name="id_recruitment_answer" id="id_recruitment_answer" type="hidden">
                                    <input type="text" name="sequence" id="sequence" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="sequenceError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							<div class="row">
                                <label class="col-sm-4 col-form-label">Description Answer</label>
                                <div class="col-sm-8">
                                    <input class="form-control form-control-sm" name="description_answer" id="description_answer" >
                                    <span class="invalid-feedback" role="alert" id="description_answerError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>		

							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status_answer" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>	
							
					</div>
					
                </div>
				<br>
				<button style="float:right;" type="submit" class="pull-right btn btn-sm btn-success" id="save_button_answer"><i class="fas fa-save"></i> Save</button>
                <a style="float:right;color: white; margin-right: 10px;" class="pull-right btn btn-sm btn-danger reset"><i class="fas fa-refresh"></i> Reset</a> 
				<br>
				<hr>
                </div>				
			</form>
			<div class="card-body" style="margin-top:-40px;">
                <table id="answer_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th>No</th>
                            <th>Sequence</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
			<div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>              
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
@section('scripts')
<script type="text/javascript">
let global_question_type = "";
let global_id_recruitment_question_group = "";
let global_id_recruitment_answer = "";
let global_answer = [];
let question_type = [];
let global_id_question_header = "";
let global_id_answer_header = [];
let global_id_question_detail = 0;

function on_close_modal() {
    window.location.reload();
}

function check(c, q) {
    var formAnswerData = $('#answer_detailForm').serializeArray();
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: global_id_answer_header == '' ? "<?= url('recruitment/personality_assessment/master_interview_question/save_interview_answer') . '?id_question=' ?>" + q : "<?= url('recruitment/personality_assessment/master_interview_question/update_interview_answer') . '?id_question=' ?>" + q + "<?= '&id_recruitment_question_answer='?>" + global_id_question_header,
        data: formAnswerData,
        success: function(response) {
            global_id_answer_header = response.answer;
            if (response.status == 'true') {
                $('#browseModalanswer').modal('hide');
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                })

            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! [Unknown Error]'
                });
            }
        },

    });


};

function browse_answer(counter) {
    $("#table_detail_answer_body").html("");
    $(".invalid-feedback").children("strong").text("");
    $("#interviewForm input").removeClass("is-invalid");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    global_id_question_header = $("#question_" + counter + "_id_recruitment_question").val();

    var formData = $('#interviewForm').serializeArray();
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: global_id_question_header == '' && global_id_recruitment_question_group == '' ? "<?= url('recruitment/personality_assessment/master_interview_question/save_interview') . '?counter=' ?>" + counter : "<?= url('recruitment/personality_assessment/master_interview_question/update_interview') . '?id_recruitment_question_group=' ?>" + global_id_recruitment_question_group + "<?= '&counter='?>" + counter + "<?= '&id_recruitment_question='?>" + global_id_question_header,
        data: formData,
        success: function(response) {
            global_id_recruitment_question_group = response.result.id_recruitment_question_group;
            if (global_id_question_header != '') {
                global_id_answer_header = response.answer;
            }
            $("#question_" + counter + "_id_recruitment_question").val(response.question.id_recruitment_question);

            if (response.status == 'true') {

                let length = $("#question_" + counter + "_suggested_answer").val().length;
                let x = $("#question_" + counter + "_suggested_answer").select2('data');
                let id_recruitment_question = $("#question_" + counter + "_id_recruitment_question").val();

                $("#id_recruitment_question").val($("#question_" + counter + "_id_recruitment_question").val());
                $("#sequence").val($("#question_" + counter + "_sequence").val());
                $("#question").val($("#question_" + counter + "_question").val());
                $("#note").val($("#question_" + counter + "_note").val());
                $(".add-answer").attr('onclick', 'check(' + counter + ',' + id_recruitment_question + ')');

                for (var i = 0; i < length; i++) {
                    var content = jQuery('#sample_table_detail_answer tr'),
                        element = null,
                        element = content.clone();
                    element.find('.id_recruitment_question_answer_input').attr('id', 'answer_' + i + '_id_recruitment_question_answer');
                    element.find('.id_recruitment_question_answer_input').attr('name', 'answer[' + i + '][id_recruitment_question_answer]');

                    element.find('.id_recruitment_answer_input').attr('id', 'answer_' + i + '_id_recruitment_answer');
                    element.find('.id_recruitment_answer_input').attr('name', 'answer[' + i + '][id_recruitment_answer]');

                    element.find('.answer_input').attr('id', 'answer_' + i + '_answer');
                    element.find('.answer_input').attr('name', 'answer[' + i + '][answer]');
                    element.find('.answer_input').css('font-weight', 'bold');

                    element.find('.correct_input').attr('id', 'answer_' + i + '_correct');
                    element.find('.correct_input').attr('name', 'answer[' + i + '][correct]');

                    element.find('.score_input').attr('id', 'answer_' + i + '_score');
                    element.find('.score_input').attr('name', 'answer[' + i + '][score]');

                    element.appendTo('#table_detail_answer_body');

                    $('#table_detail_answer_body tr').each(function(index) {
                        $(this).find('.id_recruitment_answer_input').val(x[index].id);
                        $(this).find('.answer_input').val(x[index].text);
                        //  $(this).find('.id_recruitment_question_answer_input').val(global_id_answer_header);
                    });
                    if (global_id_question_header != '') {
                        $.each(global_id_answer_header, function(key, val) {
                            $("#answer_" + key + "_id_recruitment_question_answer").val(val.id_recruitment_question_answer);
                            if (val.is_corrected_answer == 1) {
                                $("#answer_" + key + "_correct").prop('checked', true);
                            } else {
                                $("#answer_" + key + "_correct").prop('checked', false);
                            }
                            $("#answer_" + key + "_score").val(val.weight_score);
                        });
                    }
                }
                $('#browseModalanswer').modal('show');

            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! [Unknown Error]'
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

}


$(document).on('click', '.new', function() {
    global_id_recruitment_question_group = "";
    $("#interviewForm")[0].reset();
    $("#table_question_body").html("");
    $("#interviewForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Interview");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#interviewForm input").removeClass("is-invalid");
    $('#save_button').attr('class', 'btn btn-sm btn-success');
    $('#save_button').html('<i class="fas fa-save"></i> Save');
    $('#modal_form_interview').modal('show');
});

$(document).on('click', '.new_answer', function() {
    newForm();
});

function newForm() {
    global_id_recruitment_answer = "";
    $("#answerForm")[0].reset();
    $("#answerForm .modal-title").html("<span class='fas fa-gear'></span> Manage Master Answer");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#answerForm input").removeClass("is-invalid");
    $('#save_button_answer').attr('class', 'btn btn-sm btn-success');
    $('#save_button_answer').html('<i class="fas fa-save"></i> Save');
    $("#select2status_answer").val('A').trigger('change');
    $('#modal_form_answer').modal('show');
}

$(document).on('click', '#new_question_detail', function() {
    var content = jQuery('#sample_table_question tr'),
        size = global_id_question_detail++,
        element = null,
        element = content.clone();
    element.attr('id', 'rec-' + size);
    element.find('.delete-record').attr('data-id', size);
    element.find('.add-record').attr('onclick', 'browse_answer(' + size + ')');
    element.find('.add-record').attr('data-id', size);
    element.find('.add-record').attr('id', 'question_' + size + '_add-record');

    element.find('.id_recruitment_question_input').attr('id', 'question_' + size + '_id_recruitment_question');
    element.find('.id_recruitment_question_input').attr('name', 'question[' + size + '][id_recruitment_question]');

    element.find('.sequence_input').attr('id', 'question_' + size + '_sequence');
    element.find('.sequence_input').attr('name', 'question[' + size + '][sequence]');
    element.find('.sequence_input_error').attr('id', 'question_' + size + '_sequenceError');
	
	element.find('.competency_group_input').attr('id', 'question_' + size + '_competency_group');
    element.find('.competency_group_input').attr('name', 'question[' + size + '][competency_group]');
    element.find('.competency_group_input_error').attr('id', 'question_' + size + '_competency_groupError');
	
	element.find('.category_group_input').attr('id', 'question_' + size + '_category_group');
    element.find('.category_group_input').attr('name', 'question[' + size + '][category_group]');
    element.find('.category_group_input_error').attr('id', 'question_' + size + '_category_groupError');

    element.find('.question_type_input').attr('id', 'question_' + size + '_question_type');
    element.find('.question_type_input').attr('name', 'question[' + size + '][question_type]');
    element.find('.question_type_input').attr('id_question_type', size);
    element.find('.question_type_input').attr('size', size);
    element.find('.question_type_input_error').attr('id', 'question_' + size + '_question_typeError');
	element.find('.question_type_input').prepend('<option selected></option>').select2({
		placeholder: "Question Type ...",
		data: question_type,
	});
    element.find('.question_input').attr('id', 'question_' + size + '_question');
    element.find('.question_input').attr('name', 'question[' + size + '][question]');
    element.find('.question_input_error').attr('id', 'question_' + size + '_questionError');

    element.find('.suggested_answer_input').attr('id', 'question_' + size + '_suggested_answer');
    element.find('.suggested_answer_input').attr('name', 'question[' + size + '][suggested_answer][]');
    element.find('.suggested_answer_input_error').attr('id', 'question_' + size + '_suggested_answerError');

    if (global_question_type == 'Essay') {
        element.find('.add-record').hide();
        element.find('.suggested_answer_input').select2({
            disabled: true,
            width: '100%',
            data: global_answer
        });
    } else {
        element.find('.add-record').show();
        element.find('.suggested_answer_input').select2({
            disabled: false,
            width: '100%',
            data: global_answer
        });
    }
    element.find('.note_input').attr('id', 'question_' + size + '_note');
    element.find('.note_input').attr('name', 'question[' + size + '][note]');
    element.find('.note_input_error').attr('id', 'question_' + size + '_noteError');

    element.appendTo('#table_question_body');
    $('#table_question_body tr').each(function(index) {
        $(this).find('span.sn').html(index + 1);
        $(this).find('input.sequence_input').val(index + 1);
    });
});

$(document).on('click', '.delete-record', function() {
    var id = jQuery(this).attr('data-id');
    var targetDiv = jQuery(this).attr('targetDiv');
    var id_recruitment_question = $("#question_" + id + "_id_recruitment_question").val();
    if (id_recruitment_question != "") {
        // $.ajax({
        //     url: '<?= url('master_employee_survey/destroy_question') ?>/' + id_recruitment_question,
        //     success: function(data) {
                jQuery('#rec-' + id).remove();
        //     }
        // });
    } else {
        jQuery('#rec-' + id).remove();
    }
    $('#table_question_body tr').each(function(index) {
        $(this).find('span.sn').html(index + 1);
        $(this).find('input.sequence_input').val(index + 1);
    });
    return true;
});

$('#interviewForm').submit(function(e) {
    e.preventDefault();
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#interviewForm input").removeClass("is-invalid");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");

    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: global_id_recruitment_question_group == '' ? "{{ route('rec_group.save') }}" : "{{ route('rec_group.update') }}",
        data: formData,
        success: function(response) {
            if (response.status == 'true') {
                $('#modal_form_interview').modal('hide');
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                }).then(function() {
                    location.reload();
                });
                $('#interview_table').DataTable().ajax.reload();

            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: response.message
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
            }
			else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: response.message
                });
            }
        }

    });
});


$('#answerForm').submit(function(e) {
    e.preventDefault();
    let formData = new FormData($('#answerForm')[0]);

    $(".invalid-feedback").children("strong").text("");
    $("#answerForm input").removeClass("is-invalid");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: global_id_recruitment_answer == '' ? "{{ route('answer_interview.save_answer') }}" : "{{ route('answer_interview.update_answer') }}",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData,
        success: function(response) {
            if (response.status == 'true') {
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                });
                get_answer();
                $('#answer_table').DataTable().ajax.reload();
            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: response.message
                });
            }
        },
        error: function(response) {
            if (response.status === 422) {
                let errors = response.responseJSON.errors;
                Object.keys(errors).forEach(function(key) {
                    $("#" + key).addClass("is-invalid");
                    $("#" + key + "Error").children("strong").text(errors[key][0]);
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

$(document).on('click', '.edit_answer', function() {
    let id_recruitment_answer = $(this).attr('id');
    global_id_recruitment_answer = id_recruitment_answer;
    $("#answerForm")[0].reset();
    $("#answerForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Answer");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#answerForm input").removeClass("is-invalid");
    $('#save_button_answer').attr('class', 'btn btn-sm btn-primary');
    $('#save_button_answer').html('<i class="fas fa-edit"></i> Update');
	
    $.ajax({
        url: "<?= url('recruitment/personality_assessment/master_interview_question/get_edit_answer') ?>",
        method: "GET",
        data: {
            id_recruitment_answer: id_recruitment_answer
        },
        success: function(response) {  
            $('#id_recruitment_answer').val(response.id_recruitment_answer);
            $('#sequence').val(response.sequence);
            $('#description_answer').val(response.description);
            $('#select2status_answer').val(response.status).trigger('change');
            $("#modal_form_answer").animate({ scrollTop: $("#description_answer").offset().top }, 500);
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
});

$(document).on('click', '.reset', function(event) {
	newForm()
});

$(document).on('click', '.edit', function() {
    let id_recruitment_question_group = $(this).attr('id');
    global_id_recruitment_question_group = id_recruitment_question_group;
    $("#interviewForm")[0].reset();
    $("#table_question_body").html("");
    $("#interviewForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Interview");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#interviewForm input").removeClass("is-invalid");
    $('#save_button').attr('class', 'btn btn-sm btn-primary');
    $('#save_button').html('<i class="fas fa-edit"></i> Update');

    $.ajax({
        url: "<?= url('recruitment/personality_assessment/master_interview_question/get_edit_interview') ?>",
        method: "GET",
        data: {
            id_recruitment_question_group: id_recruitment_question_group
        },
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
        success: function(response) {
            global_id_question_detail = 0;
         
            $.each(response.question, function(i, item) {
                $('#new_question_detail').trigger('click');
            });
            $('#id_recruitment_question_group').val(response.id_recruitment_question_group).trigger('change');
            $('#description').val(response.description).trigger('change');
            $('#rec_stage').val(response.id_question_group).trigger('change');
            $('#module_type').val(response.question_type).trigger('change');
            $('#id_company').val(response.company_header).trigger('change');
            $('#select2status').val(response.status_header).trigger('change');

            setTimeout(function() {
                $('#table_question_body tr').each(function(index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find('.id_recruitment_question_input').val(response.question[index].id_recruitment_question);
                    $(this).find('.question_type_input').val(response.question[index].id_question_type).trigger('change');
                    $(this).find('.sequence_input').val(response.question[index].sequence);
                    $(this).find('.competency_group_input').val(response.question[index].competency_group);
                    $(this).find('.category_group_input').val(response.question[index].category_group);
                    $(this).find('.question_input').val(response.question[index].question);
                    $(this).find('.suggested_answer_input').val(response.question[index].suggested_answer).trigger('change');
                    $(this).find('.note_input').val(response.question[index].notes);
                });
            }, 500);
        },
		complete: function(){
			$('#loader').addClass('hidden');
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

    $('#modal_form_interview').modal('show');
});

$(document).ready(function(){
	module_type = [
		{
			id: 'Recruitment',
			text: 'Recruitment'
		},
		{
			id: 'Talent',
			text: 'Talent'
		},
	];
		
    $('#interview_table').DataTable({
        processing: true,
		responsive: true,
		destroy: true,
        ajax: {
			url: "{{ route('rec_group.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#interview_table').DataTable().ajax.reload();
				}
			},
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_recruitment_question_group',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'rec_stage', name: 'rec_stage' },
            { data: 'description', name: 'description' },
            { data: 'question_type', name: 'question_type' },
            { data: 'status', name: 'status' },
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {	
					return data;
				} 
			},
        ]
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
		
	$.extend(true, $.fn.dataTable.defaults, {
        columnDefs: false,
        dom: "<'row'<'col-sm-6'l><'col-sm-5'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-4'i><'col-sm-4 text-center'><'col-sm-4'p>>",
    });
    $('#answer_table').DataTable({
        processing: true,
        destroy: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('rec_answer.index_answer') }}",
            error: function(jqXHR, textStatus, errorThrown) {
                $('#answer_table').DataTable().ajax.reload();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'sequence', name: 'sequence'},
            {data: 'description', name: 'description'},            
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'},
        ]
    });	
	
	refresh_data();

});

	
function refresh_data() {
		
	get_stage();
	get_answer();
	get_module();
	$('#select2status').select2({
		width: '100%'
	});
	$('#select2status_answer').select2({
        width: '100%'
    });
	 get_question_type().then(function(value) {
        global_question_type = value[0].code;
        question_type = value;
    });
}

function get_stage() {
	$.getJSON('<?= url('recruitment/personality_assessment/master_interview_question/get_stage') ?>', function (data) {
        $('#rec_stage').select2({
            data: data,
        });
		
    }).fail(function (data) { // Call failed
        get_stage();
    });
}

function get_answer(){
	$.getJSON('<?= url('recruitment/personality_assessment/master_interview_question/get_answer') ?>', function (data) {
        global_answer = data;
    }).fail(function (data) { // Call failed
        get_answer();
	});
}

const get_module = async () => {
    try {
		$('#module_type').select2({
			data:module_type
		});
    } 
	catch (error) {
    }	
}

const get_question_type = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('recruitment/personality_assessment/master_interview_question/get_question_type') ?>',
            method: "GET",
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_question_type();
    }
}

$(document).on('change', '.question_type_input', function() {
    let size = $(this).attr('size');
    let type = $(this).select2('data')[0].code;
    let elementForChange = `#question_${size}_suggested_answer`;
    let elementAdd = `#question_${size}_add-record`;
    set_type_answer(elementForChange,elementAdd, type);
});

function set_type_answer(element,elementAdd, type, value='') {
    if(type == 'Essay'){
        $(element).next(".select2-container").hide();
        $(elementAdd).hide();
    } else {
        $(element).next(".select2-container").show();
		$(elementAdd).show();
    }
}  

$(document).on('click', '.delete_answer', function(event) {
        id_recruitment_answer = $(this).attr('id');
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
                    url: '<?= url('recruitment/personality_assessment/master_interview_question/destroy_answer') ?>/' + id_recruitment_answer,
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

$(document).on('click', '.delete', function(event) {
        id_recruitment_question_group = $(this).attr('id');
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
                    url: '<?= url('recruitment/personality_assessment/master_interview_question/destroy') ?>/' + id_recruitment_question_group,
                    success: function(data) {
                        if(data.status == 'true'){
                            setTimeout(function() {
                                $('#confirmModal').modal('hide');
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
</script>
@endsection