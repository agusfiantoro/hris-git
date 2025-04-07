@extends('adminlte::page')
@section('title', 'Master Employee Survey')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Employee Survey</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Employee Survey</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="emp_survey_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Description</th>
                            <th>Request By</th>
                            <th>Survey Type</th>
                            <th>With Score</th>
                            <!-- <th>Start Date</th>
                            <th>End Date</th> -->
                            <th>Published</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-duplicate-survey"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Duplicate Employee Survey</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <label class="col-sm-6 col-form-label">Destination Company</label>
                            <div class="col-sm-6">
                                <select name="duplicate_id_company" id="duplicate_id_company" style="width:100%"></select>
                            </div>
                        </div>
                    </div>
                </div>
                <p>
                    <i>
                        Duplicating a survey will generate a copy of the survey, survey questions, and survey answers in selected company. 
                        Do it with care and due diligence to avoid redundant data.
                    </i>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success" id="btn-duplicate"><i class="fas fa-copy"></i> Duplicate</button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_form_emp_survey"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="emp_surveyForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Employee Survey</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
									<input name="id_survey_header" id="id_survey_header" type="hidden">	
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
							
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <select name="department[]" id="department" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>						
							<div class="row">
                                <label class="col-sm-4 col-form-label">Region</label>
								<div class="col-sm-8">
                                    <select name="region[]" id="region" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="regionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                                <div class="col-sm-8">
                                    <select name="branch[]" id="branch" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="branchError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Job Grade</label>
                                <div class="col-sm-8">
                                    <select name="grade[]" id="grade" class="form-control form-control-sm select2 access_group" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="branchError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Principal</label>
                                <div class="col-sm-8">
                                    <select name="principal[]" id="principal" class="form-control form-control-sm select2 access_group" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="principalError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
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
                                <label class="col-sm-4 col-form-label">Survey Type</label>
                                <div class="col-sm-8">
                                    <select name="id_survey_type" id="id_survey_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_survey_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>      
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
                                <div class="col-sm-8">
                                    <select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row" style="display:none;">
								<label class="col-sm-4 col-form-label">Start Date to End Date</label>								
								<div class="col-sm-8">
									<div class="input-group">
										<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm"/>
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
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Published</label>
								<div class="col-sm-2">
									<input type="checkbox" name="published" id="published" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="publishedError">
                                        <strong></strong>
                                    </span>
                                </div>      
                                <label class="col-sm-5 col-form-label text-right" >With Score</label>
                                <div class="col-sm-1">
                                    <input type="checkbox" name="with_score" id="with_score" class="form-control form-control-sm float-right" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="with_scoreError">
                                        <strong></strong>
                                    </span>
                                </div>                             
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8 ">
                                    <select name="status" id="select2status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Cross Company(OS)</label>
								<div class="col-sm-2">
									<input type="checkbox" name="cross_com" id="cross_com" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="cross_comError">
                                        <strong></strong>
                                    </span>
                                </div>      
                            </div>
						</div>
                        <div class="col-md-12" style="margin-top:10px;">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-10">
                                    <textarea style="max-height:30px;" class="form-control form-control-sm summernote" id="notes" name="notes" ></textarea>
                                    <span class="invalid-feedback" role="alert" id="notesError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="col-md-12" style="margin-top:10px;">
                            <div class="row">
                                <label class="col-sm-2 col-form-label">Period</label>
                                <span class="invalid-feedback" role="alert" id="periodError">
                                    <strong></strong>
                                </span>
                                <div class="all_period col-sm-5"></div>
                                <div class="col-sm-5"><button onclick="return false;" class="pull-left btn btn-xs btn-success" id="add_period"><span class="fas fa-plus"></span> Add Period</button></div>
                            </div>
                        </div>
					</div>
					<hr/>
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
						                    <button type="button" class="new_answer pull-right btn btn-xs btn-sm btn-success" style="margin-right:10px;"><span class="fas fa-gear"></span> Manage Master Answer</button>
                                        </div>
                                        <div class="col-md-12" style="width:1200px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_question" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th width=20>No.</th>
                                                        <th width=20>Sequence</th>
                                                        <th style="white-space:nowrap;min-width:150px;">Type</th>
                                                        <th style="white-space:nowrap;min-width:150px;">Category</th>
                                                        <th style="white-space:nowrap;min-width:250px;">Question</th>
                                                        <th style="white-space:nowrap;min-width:150px;">Answer</th>
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
                <table id="sample_table_emp_survey">
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
                                <select name="question[0][question_type]" id="question_0_question_type" class="form-control form-control-sm select2 question_type_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback question_type_input_error" role="alert" id="0_question_typeError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                                <select name="question[0][category]" id="question_0_category" class="form-control form-control-sm select2 category_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback category_input_error" role="alert" id="0_categoryError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>																	
                                <textarea type="text" name="question[0][question]" id="question_0_question" class="form-control form-control-sm question_input" style="height:100px;"></textarea>
                                <span class="invalid-feedback question_input_error" role="alert" id="question_0_questionError">
                                    <strong></strong>
                                </span>		
								
								<div class="custom-file">
									 <input type="file" name="question[0][attachment]" id="question_0_attachment" class="custom-file-input form-control form-control-sm attachment_input" onchange="docfile(this,0)">
									 <span class="invalid-feedback attachment_input_error" role="alert" id="question_0_attachmentError">
											<strong></strong>
										</span>
									  <div id="question_0_docfile" class="custom-file-label doc_input"><i style="font-size:11px;">Max 150 kb</i></div>
									  <i style="font-size:11px;">File Type : jpg,jpeg,png</i>
								</div>
								 <img class="attach_input" id="question_0_attach" width="150px" style="display:none;"/>
                        </td>
						<td>																	
                                <select name="question[0][suggested_answer][]" id="question_0_suggested_answer" class="form-control form-control-sm select2 suggested_answer_input" style="width:100%;" multiple="multiple"></select>
                                <span class="invalid-feedback suggested_answer_input_error" role="alert" id="question_0_suggested_answerError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>						
                                <textarea type="text" name="question[0][note]" id="question_0_note" class="form-control form-control-sm note_input" style="height:100px;"></textarea>
                                <span class="invalid-feedback note_input_error" role="alert" id="question_0_noteError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td style="width:100px;vertical-align:middle;">
							<center>
								<button type="button" class="add-record btn btn-xs btn-primary" data-id="0" onclick="browse_answer(0)"><span class="far fa-list-alt"></span></button>&nbsp;
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
			<form method="post" id="answer_surveyForm">
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
									<input name="id_survey_question" id="id_survey_question" type="hidden">
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
							<table id="table_survey_answer" class="table table-striped table-bordered table-hover datatable">
								<thead>
									<tr>
										<th width=200>Answer</th>
										<th style="white-space:nowrap;">Correct Answer</th>
										<th style="white-space:nowrap;">Score Answer</th>
									</tr>
								</thead>
								<tbody id="table_survey_answer_body">
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
                <table id="sample_table_survey_answer">
                    <tr id="">
						<td>	
							<input name="answer[0][id_survey_answer]" id="answer_0_id_survey_answer" type="hidden" class="form-control form-control-sm id_survey_answer_input">
							<input name="answer[0][id_answer]" id="answer_0_id_answer" type="hidden" class="form-control form-control-sm id_answer_input">
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
                                <label class="col-sm-2 col-form-label">Code</label>
                                <div class="col-sm-10">
                                    <input name="id_answer" id="id_answer" type="hidden">   
                                    <input type="text" name="code_answer" id="code_answer" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="code_answerError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							<div class="row">
                                <label class="col-sm-2 col-form-label">Description Answer</label>
                                <div class="col-sm-10">
                                    <textarea style="max-height:30px;" class="form-control form-control-sm summernote" name="description_answer" id="description_answer" ></textarea>
                                    <span class="invalid-feedback" role="alert" id="description_answerError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>		

                            <div class="row">
                                <label class="col-sm-2 col-form-label">Image Answer</label>
                                <div class="col-md-8">
                                    <div class="custom-file">
                                        <input type="file" name="suggested_image" class="custom-file-input" id="suggested_image">
                                        <span class="invalid-feedback" role="alert" id="suggested_imageError"><strong></strong></span>
                                        <label class="custom-file-label"><i>File (.jpg / .png / .svg)</i></label>
                                    </div>
                                    <div id="div_image"></div><br>
                                </div>
                            </div>  				
							
							<div class="row">
                                <label class="col-sm-2 col-form-label">Status</label>
                                <div class="col-sm-10">
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
                            <th>Code</th>
                            <th>Description</th>
                            <th>Image</th>
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

@section('css')
<style type="text/css">	
	.modal-xl {
        max-width: 90% !important;
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
	.modal{
		overflow:auto !important;
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
let global_answer = [];
let question_type = [];
let category = [];
let department = [];
let region = [];
let branch = [];
let allGrade = [];

//var formAnswerData = [];
function docfile(input,size) {
		var fileName = input.files[0].name;
		$('#question_' + size + '_docfile').html(fileName);
	}
	
function check(c, q) {
    var formAnswerData = $('#answer_surveyForm').serializeArray();
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: global_id_answer_header == '' ? "<?= url('employee/employee_setting/master_employee_survey/save_survey_answer') . '?id_question=' ?>" + q : "<?= url('employee/employee_setting/master_employee_survey/update_survey_answer') . '?id_question=' ?>" + q + "<?= '&id_survey_answer='?>" + global_id_question_header,
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
    $("#table_survey_answer_body").html("");
    $(".invalid-feedback").children("strong").text("");
    $("#emp_surveyForm input").removeClass("is-invalid");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    global_id_question_header = $("#question_" + counter + "_id_survey_question").val();

    var formData = $('#emp_surveyForm').serializeArray();
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: global_id_question_header == '' && global_id_survey_header == '' ? "<?= url('employee/employee_setting/master_employee_survey/save_survey') . '?counter=' ?>" + counter : "<?= url('employee/employee_setting/master_employee_survey/update_survey') . '?id_header=' ?>" + global_id_survey_header + "<?= '&counter='?>" + counter + "<?= '&id_survey_question='?>" + global_id_question_header,
        data: formData,
        success: function(response) {
            global_id_survey_header = response.result.id_survey_header;
            $('#id_survey_header').val(response.result.id_survey_header);
            if (global_id_question_header != '') {
                global_id_answer_header = response.answer;
            }
            $("#question_" + counter + "_id_survey_question").val(response.question.id_survey_question);

            if (response.status == 'true') {

                let length = $("#question_" + counter + "_suggested_answer").val().length;
                let x = $("#question_" + counter + "_suggested_answer").select2('data');
                let id_survey_question = $("#question_" + counter + "_id_survey_question").val();

                $("#id_survey_question").val($("#question_" + counter + "_id_survey_question").val());
                $("#sequence").val($("#question_" + counter + "_sequence").val());
                $("#question").val($("#question_" + counter + "_question").val());
                $("#attachment").val($("#question_" + counter + "_attachment").val());
                $("#note").val($("#question_" + counter + "_note").val());
                $(".add-answer").attr('onclick', 'check(' + counter + ',' + id_survey_question + ')');

                for (var i = 0; i < length; i++) {
                    var content = jQuery('#sample_table_survey_answer tr'),
                        element = null,
                        element = content.clone();
                    element.find('.id_survey_answer_input').attr('id', 'answer_' + i + '_id_survey_answer');
                    element.find('.id_survey_answer_input').attr('name', 'answer[' + i + '][id_survey_answer]');

                    element.find('.id_answer_input').attr('id', 'answer_' + i + '_id_answer');
                    element.find('.id_answer_input').attr('name', 'answer[' + i + '][id_answer]');

                    element.find('.answer_input').attr('id', 'answer_' + i + '_answer_input');
                    element.find('.answer_input').attr('name', 'answer[' + i + '][answer_input]');
                    element.find('.answer_input').css('font-weight', 'bold');

                    element.find('.correct_input').attr('id', 'answer_' + i + '_correct_input');
                    element.find('.correct_input').attr('name', 'answer[' + i + '][correct_input]');

                    element.find('.score_input').attr('id', 'answer_' + i + '_score_input');
                    element.find('.score_input').attr('name', 'answer[' + i + '][score_input]');

                    element.appendTo('#table_survey_answer_body');

                    $('#table_survey_answer_body tr').each(function(index) {
                        $(this).find('.id_answer_input').val(x[index].id);
                        $(this).find('.answer_input').val(x[index].text);
                        //  $(this).find('.id_survey_answer_input').val(global_id_answer_header);
                    });
                    if (global_id_question_header != '') {
                        $.each(global_id_answer_header, function(key, val) {
                            $("#answer_" + key + "_id_survey_answer").val(val.id_survey_answer);
                            if (val.is_corrected_answer == 1) {
                                $("#answer_" + key + "_correct_input").prop('checked', true);
                            } else {
                                $("#answer_" + key + "_correct_input").prop('checked', false);
                            }
                            $("#answer_" + key + "_score_input").val(val.score_answer);
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
    daterange();
    addPeriod('all_period');

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

$(document).on('click', '.new_answer', function() {
    newForm();
});

$('#emp_surveyForm').submit(function(e) {
    e.preventDefault();
 //   let formData = $(this).serializeArray();
    var formData =  new FormData(this);
    $(".invalid-feedback").children("strong").text("");
    $("#emp_surveyForm input").removeClass("is-invalid");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");

    $.ajax({
        method: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },       
		enctype: 'multipart/form-data',
		processData: false,  // Important!
		contentType: false,
		cache: false,
        url: global_id_survey_header == '' ? "{{ route('employee_survey.save') }}" : "{{ route('employee_survey.update') }}",
        data: formData,
		success: function(response) {
            if (response.status == 'true') {
                $('#modal_form_emp_survey').modal('hide');
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: response.message
                }).then(function() {
                    location.reload();
                });
                $('#emp_survey_table').DataTable().ajax.reload();

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
            } else {
                swal({
                    title: 'Please refresh page',
                }).then(function(){ 
                    // location.reload();
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
        url: global_id_answer == '' ? "{{ route('answer.save_answer') }}" : "{{ route('answer.update_answer') }}",
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
                $('#suggested_image').val('');
                $("label.custom-file-label").html('<i>File (.jpg / .png / .svg)</i>');
                $('#div_image').html('');
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
                    console.log($("#" + key + "Error").attr('class'));
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

$(document).on('click', '#new_question_detail', function() {
    var content = jQuery('#sample_table_emp_survey tr'),
        size = global_id_question_detail++,
        element = null,
        element = content.clone();
    element.attr('id', 'rec-' + size);
    element.find('.delete-record').attr('data-id', size);
    element.find('.add-record').attr('onclick', 'browse_answer(' + size + ')');
    element.find('.add-record').attr('data-id', size);

    element.find('.id_survey_question_input').attr('id', 'question_' + size + '_id_survey_question');
    element.find('.id_survey_question_input').attr('name', 'question[' + size + '][id_survey_question]');

    element.find('.sequence_input').attr('id', 'question_' + size + '_sequence');
    element.find('.sequence_input').attr('name', 'question[' + size + '][sequence]');
    element.find('.sequence_input_error').attr('id', 'question_' + size + '_sequenceError');

    element.find('.question_type_input').attr('id', `question_${size}_question_type`);
    element.find('.question_type_input').attr('name', `question[${size}][question_type]`);
    element.find('.question_type_input').attr('id_question_type', size);
    element.find('.question_type_input').attr('size', size);
    element.find('.question_type_input_error').attr('id', `question_${size}_question_typeError`);
    element.find('.question_type_input').select2({
        placeholder: "Select Question Type",
        // allowClear: true,
        data: question_type
    });

    element.find('.category_input').attr('id', `question_${size}_category`);
    element.find('.category_input').attr('name', `question[${size}][category]`);
    element.find('.category_input').attr('id_category', size);
    element.find('.category_input').attr('size', size);
    element.find('.category_input_error').attr('id', `question_${size}_categoryError`);
    element.find('.category_input').select2({
        placeholder: "Select Category",
        allowClear: true,
        data: category
    });

    element.find('.question_input').attr('id', 'question_' + size + '_question');
    element.find('.question_input').attr('name', 'question[' + size + '][question]');
    element.find('.question_input_error').attr('id', 'question_' + size + '_questionError');
	
	element.find('.attachment_input').attr('onchange', 'docfile(this,'+size+')');
	element.find('.attachment_input').attr('id', 'question_' + size + '_attachment');
	element.find('.attachment_input').attr('name', 'question[' + size + '][attachment]');
	element.find('.attachment_input_error').attr('id', 'question_' + size + '_attachmentError');
	element.find('.doc_input').attr('id', 'question_' + size + '_docfile');
	element.find('.attach_input').attr('id', 'question_' + size + '_attach');

    element.find('.suggested_answer_input').attr('id', 'question_' + size + '_suggested_answer');
    element.find('.suggested_answer_input').attr('name', 'question[' + size + '][suggested_answer][]');
    element.find('.suggested_answer_input_error').attr('id', 'question_' + size + '_suggested_answerError');

    if (global_question_type == 'Essay' || global_question_type == 'Upload_Files') {
        element.find('.add-record').css("display", "none");
        //  $('#save_button').css("display","");
        element.find('.suggested_answer_input').select2({
            disabled: true,
            width: '100%',
            data: global_answer
        });
    } else {
        element.find('.add-record').css("display", "");
        //  $('#save_button').css("display","none");
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
    var id_survey_question = $("#question_" + id + "_id_survey_question").val();
    if (id_survey_question != "") {
        // $.ajax({
        //     url: '<?= url('master_employee_survey/destroy_question') ?>/' + id_survey_question,
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

$(document).on('click', '.edit_answer', function() {
    let id_answer = $(this).attr('id');
    global_id_answer = id_answer;
    $("#answerForm")[0].reset();
    $("#answerForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Answer");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#answerForm input").removeClass("is-invalid");
    $('#save_button_answer').attr('class', 'btn btn-sm btn-primary');
    $('#save_button_answer').html('<i class="fas fa-edit"></i> Update');
    $("label.custom-file-label").html(`<i>File (.jpg / .png / .svg)</i>`);

    $.ajax({
        url: "<?= url('employee/employee_setting/master_employee_survey/get_edit_answer') ?>",
        method: "GET",
        data: {
            id_answer: id_answer
        },
        success: function(response) {
            $('#div_image').html('');
            if(response.suggested_image != null){
                $('#div_image').html(`<img src="${response.suggested_image}" style="height:70px;">`);
            }
            $('#id_answer').val(response.id_answer);
            $('#code_answer').val(response.code);
            $('#description_answer').summernote('code', response.description_answer);
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
$(document).on('click', '.edit', function() {
    let id_survey_header = $(this).attr('id');
    global_id_survey_header = id_survey_header;
    $("#emp_surveyForm")[0].reset();
    $("#table_question_body").html("");
    $("#emp_surveyForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Employee Survey");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#emp_surveyForm input").removeClass("is-invalid");
    $('#save_button').attr('class', 'btn btn-sm btn-primary');
    $('#save_button').html('<i class="fas fa-edit"></i> Update');

    $.ajax({
        url: "<?= url('employee/employee_setting/master_employee_survey/get_survey_edit') ?>",
        method: "GET",
        data: {
            id_survey_header: id_survey_header
        },
        success: function(response) {
            global_id_question_detail = 0;
            let id_department = '';
            let id_region = '';
            let id_branch = '';
            let id_job_grade = '';
            let id_principal = '';
            num_period = 0;

            $.each(response.question, function(i, item) {
                $('#new_question_detail').trigger('click');
            });
            if(response.id_department != null){
                if(response.id_department.indexOf(',') > -1){
                    id_department = response.id_department.split(',');
                } else {
                    id_department = [response.id_department];
                }
            }
            if(response.id_region != null){
                if(response.id_region.indexOf(',') > -1){
                    id_region = response.id_region.split(',');
                } else {
                    id_region = [response.id_region];
                }
            }
            if(response.id_branch != null){
                if(response.id_branch.indexOf(',') > -1){
                    id_branch = response.id_branch.split(',');
                } else {
                    id_branch = [response.id_branch];
                }
            }
            if(response.id_job_grade != null){
                if(response.id_job_grade.indexOf(',') > -1){
                    id_job_grade = response.id_job_grade.split(',');
                } else {
                    id_job_grade = [response.id_job_grade];
                }
            }
            if(response.id_principal != null){
                if(response.id_principal.indexOf(',') > -1){
                    id_principal = response.id_principal.split(',');
                } else {
                    id_principal = [response.id_principal];
                }
            }

            $('#department').val(id_department).trigger('change');
            get_region(id_region).then(function(value) {
                get_branch(id_region, id_branch);
            });
            $('#grade').val(id_job_grade).trigger('change');
            $('#principal').val(id_principal).trigger('change');

            $('#id_survey_header').val(response.id_survey_header).trigger('change');
            $('#description').val(response.description).trigger('change');
            $('#notes').summernote('code', response.notes);
            $('#id_employee_request').val(response.id_employee_request).trigger('change');
            $('#id_question_type').val(response.id_question_type).trigger('change');
            $('#id_survey_type').val(response.id_survey_type).trigger('change');
            $('#survey_category').val(response.survey_category).trigger('change');
            // $('#start_date').val(response.start_date).trigger('change');
            // $('#end_date').val(response.end_date).trigger('change');
            if (response.published == 1) {
                $('#published').prop('checked', true);
            } else {
                $('#published').prop('checked', false);
            }
            if (response.with_score == 1) {
                $('#with_score').prop('checked', true);
            } else {
                $('#with_score').prop('checked', false);
            }
			if (response.is_cross_company_os == 1) {
                $('#cross_com').prop('checked', true);
            } else {
                $('#cross_com').prop('checked', false);
            }
            $('#id_company').val(response.company_header).trigger('change');
            $('#select2status').val(response.status_header).trigger('change');

            // daterange(response.start_date, response.end_date)

            addPeriodFromEdit(response.period).then(ok => {
                $.each(response.period, function(i, item) {
                    let elementRange = `period_${i}`;
                    let elementStart = `startperiod_${i}`;
                    let elementEnd = `endperiod_${i}`;
                    let elementStatusPeriod = `statusperiod_${i}`;
                    let elementidSurveyHistory = `surveyhistory_${i}`;

                    daterangePeriod(elementRange, elementStart, elementEnd, elementidSurveyHistory, elementStatusPeriod, item.start, item.end, item.id_survey_history, item.status)
                });
            });

            setTimeout(function() {
                $('#table_question_body tr').each(function(index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find('.id_survey_question_input').val(response.question[index].id_survey_question);
                    $(this).find('.question_type_input').val(response.question[index].id_question_type).trigger('change');
                    $(this).find('.category_input').val(response.question[index].id_question_group).trigger('change');
                    $(this).find('.sequence_input').val(response.question[index].sequence);
                    $(this).find('.question_input').val(response.question[index].question);
				//	$(this).find('.attachment_input').val(response.question[index].attachment);
					if(response.question[index].attachment != null){
						$(this).find('.attach_input').show();
						$(this).find('.attach_input').attr("src","../../project/storage/app/public/upload/survey/"+global_id_survey_header+"/"+response.question[index].attachment);
					}
					else{
						$(this).find('.attach_input').hide();
						$(this).find('.attach_input').removeAttr('src');
					}                  
                    $(this).find('.suggested_answer_input').val(response.question[index].suggested_answer).trigger('change');
                    $(this).find('.note_input').val(response.question[index].note);
                });
            }, 500);
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

    $('#modal_form_emp_survey').modal('show');
});

$(document).ready(function(){
    $('.summernote').summernote({
        height:120,
    });
	$('#emp_survey_table').DataTable({
        processing: true,
        responsive: true,
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
            {
                data: null, defaultContent: '', orderable: false
            },
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'description', name: 'description'},
            {data: 'employee_name', name: 'employee_name'},
            {data: 'survey_type', name: 'survey_type'},
            {data: 'with_score', name: 'with_score',
                render: function ( data, type, row ) {  
                    return scoring(row.with_score);
                } 
            },
            // {data: 'start_date', name: 'start_date'},
            // {data: 'end_date', name: 'end_date'},
            {data: 'published', name: 'published', 
                render: function ( data, type, row ) {  
                    return is_published(row.published);
                } 
            },
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
    $('#answer_table').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ajax: {
            url: "{{ route('answer.index_answer') }}",
            error: function(jqXHR, textStatus, errorThrown) {
                $('#answer_table').DataTable().ajax.reload();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'code', name: 'code'},
            {data: 'description_answer', name: 'description_answer', 
                render: function ( data, type, row ) {  
                    return $("<div/>").html(row.description_answer).text();
                } 
            },
            {data: 'image', name: 'image',
                render: function ( data, type, row ) {  
                    let _return = '';
                    if(row.image != null){
                        _return = `<img src="${row.image}" style="height:70px;">`;
                    }
                    return _return;
                } 
            },             
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action'},
        ]
    });

    $('#suggested_image').change(function(){
        let file = $("#suggested_image")[0].files[0]; 
        $("label.custom-file-label").html(`<i>${file.name}</i>`);
    });

    refresh_data();
 
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
                    url: '<?= url('employee/employee_setting/master_employee_survey/destroy_answer') ?>/' + id_answer,
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
                    url: '<?= url('employee/employee_setting/master_employee_survey/destroy') ?>/' + id_survey_header,
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
    
    $(document).on('click', '.reset', function(event) {
        newForm()
        $('#div_image').html('');
        $("label.custom-file-label").html(`<i>File (.jpg / .png / .svg)</i>`);
    });

});

function refresh_data() {
    /**************** Load Menu dropdown **************************/
    $('#select2status').select2({
        width: '100%'
    });
    $('#select2status_answer').select2({
        width: '100%'
    });
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
    get_survey_type();
    get_answer();
    get_question_type().then(function(value) {
        global_question_type = value[0].code;
        question_type = value;
    });
    get_category().then(function(value) {
        category = value;
    });
    get_department();
    get_region();
    get_grade();
    get_principal();
    $('#branch').select2({
        placeholder: "Select Branch (Optional)",
        data: branch,
        allowClear: true,
    });
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

function get_employee() {
	$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_employee') ?>', function (data) {
        $('#id_employee_request').select2({
            placeholder: "Select Employee Request",
            data: data,
            allowClear: true,
        });
		
    }).fail(function (data) { // Call failed
        get_employee();
    });
}
function get_company() {
	$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_company') ?>', function (data) {
        let thisIdCompany = "{{ session('id_company')}}";
        $('#company').select2({
            data: data,
			disabled: true
        }).val(thisIdCompany).trigger('change');
        
		$('#company_answer').select2({
            data: data,
			disabled: true
        });
    }).fail(function (data) { // Call failed
        get_company();
    });	
    $.ajax({
        url: '<?= url('employee/employee_setting/master_employee_survey/get_company') ?>',
        data: {
            user_only: 'true',
        },
        success: (res) => {
            $('#duplicate_id_company').empty().prepend('<option></option>').select2({
                data: res,
                placeholder: 'Select Company',
                allowClear: true,
            })
        }
    })
}
function get_answer(){
	$.getJSON('<?= url('employee/employee_setting/master_employee_survey/get_answer') ?>', function (data) {
        global_answer = data;
    }).fail(function (data) { // Call failed
        get_answer();
	});
}

$(document).on('change', '.question_type_input', function() {
    let size = $(this).attr('size');
    let type = $(this).select2('data')[0].code;
    let elementForChange = `#question_${size}_suggested_answer`;
    set_type_answer(elementForChange, type);
});

$(document).on('change', '#region', function(e, isTrigger) {
    let selected = $(this).select2("val");
    if(!isTrigger){
        get_branch(selected);
    }
});

$(document).on('change', '#company', function() {
    let selected = $(this).select2("val");
    get_department(selected);
});

function daterange(start_date='', end_date='') {
    let separator = '   to   ';
    let start = (start_date=='' || start_date==null) ? moment().format('YYYY-MM-DD') : start_date;
    let end = (end_date=='' || end_date==null) ? moment().format('YYYY-MM-DD') : end_date;
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
    window.location.reload();
}

function is_published(status) {
    let published;
    if(status=='0' || status==false){
        published = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
    } else {
        published = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
    }
    return published;
}

const get_question_type = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('employee/employee_setting/master_employee_survey/get_question_type') ?>',
            method: "GET",
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_question_type();
    }
}

const get_category = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('employee/employee_setting/master_employee_survey/get_category') ?>',
            method: "GET",
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_category();
    }
}

function set_type_question(element, value='') {
    $(element).html('').prepend('<option selected></option>').select2({
        placeholder: "Select Type",
        data: question_type,
        allowClear: true,
    });
    if(value!=''){
        $(element).val(value).trigger('change');
    }
}  
function set_category(element, value='') {
    $(element).html('').prepend('<option selected></option>').select2({
        placeholder: "Select Category",
        data: question_type,
        allowClear: true,
    });
    if(value!=''){
        $(element).val(value).trigger('change');
    }
}   
function set_type_answer(element, type, value='') {
    if(type == 'Essay' || type == 'Upload_Files'){
        $(element).next(".select2-container").hide();
        $(element).closest('tr').find('.suggested_answer_input').attr('disabled', true);
        $(element).closest('tr').find('.add-record').css("display", "none");
    } else {
        $(element).next(".select2-container").show();
        $(element).closest('tr').find('.suggested_answer_input').attr('disabled', false);
        $(element).closest('tr').find('.add-record').css("display", "");
    }
}  
function scoring(status) {
    let return_;
    if(status=='0' || status==false || status==null){
        return_ = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
    } else {
        return_ = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
    }
    return return_;
}
async function get_department(id_company=null) {
    let result;
    let _token      = "<?= csrf_token() ?>";
    try {
        result = await $.ajax({
            type: 'GET',
            url: "<?= url('employee/employee_setting/master_employee_survey/get_department') ?>",
            dataType: 'json',
            data: {
                id_company: id_company,
            },
            success: function (resp) {
                let option = [];
                $.each(resp, function (i, item) {
                    option.push({id: item.id, text:item.text});
                });

                department = option;
                $('#department').html('');
                $('#department').select2({
                    placeholder: "Select Department (Optional)",
                    data: department,
                    allowClear: true,
                });
            }
        });
        return result;
    } catch (error) {
        get_department(id_company);
    }
}

async function get_region(selected='') {
    let result;
    let _token      = "<?= csrf_token() ?>";
    try {
        result = await $.ajax({
            type: 'GET',
            url: "<?= url('employee/employee_setting/master_employee_survey/get_region') ?>",
            dataType: 'json',
            success: function (option) {
                region = option;
                $('#region').html('');
                $('#region').select2({
                    placeholder: "Select Region (Optional)",
                    data: option,
                    allowClear: true,
                });
                if(selected != ''){
                    $('#region').val(selected).trigger('change', [true]);
                }
            }
        });
        return result;
    } catch (error) {
        get_region(selected);
    }
}

async function get_branch(id_region, selected='') {
    let result;
    let _token      = "<?= csrf_token() ?>";
    try {
        if(id_region == ''){ 
            $('#branch').html(''); 
        } else {
            result = await $.ajax({
                type: 'GET',
                url: "<?= url('employee/employee_setting/master_employee_survey/get_branch') ?>",
                dataType: 'json',
                data: {
                    id_region: id_region,
                },
                success: function (resp) {
                    let option = [];
                    thisBranchByRegion = [];
                    $.each(resp, function (i, item) {
                        // let desc = item.description + '('+ item.branch_code +')';
                        let desc = item.description;
                        option.push({id: item.id_branch, text:desc});
                        thisBranchByRegion.push(item.id_branch);
                    });

                    $('#branch').html('');
                    $('#branch').select2({
                        placeholder: "Select Branch",
                        data: option,
                        allowClear: true,
                    });
                    if(selected != ''){
                        $('#branch').val(selected).trigger('change');
                    }
                }
            });
        }
        return result;
    } catch (error) {
        get_branch(id_region, selected);
    }
}

const get_grade = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('setting/responsibility_menu/access_right_user/get_grade') ?>',
            method: "GET",
            success: function (res) {
                $.each(res, function (i, val) {
                    if(val.job_class_group == null || val.job_class_group == ''){
                        grade_group = '';
                    } else {
                        grade_group = ` - (${val.job_class_group})`;
                    }
                    name_grade = `${val.description}${grade_group}`
                    allGrade.push({id:val.id_job_grade, text:name_grade});
                }); 
                $('#grade').select2({
                    data: allGrade,
                    placeholder: 'Select Grade (Optional)'
                });
            },
        });
        return result;
    } catch (error) {
        get_grade();
    }
}

const get_principal = async () => {
    try {
        let result;
        result = await $.ajax({
            url: "{{ route('survey.get_principal') }}",
            method: "GET",
            success: function (res) {
                $('#principal').select2({
                    data: res,
                    placeholder: 'Select Principal (Optional)',
                    allowClear: true
                });
            },
        });
        return result;
    } catch (error) {
        get_grade();
    }
}

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

$(document).on('click', '#add_period', function() {
    addPeriod('all_period');
});

$(document).on('click', '.del_period', function() {
    let row = $(this).attr('row_period');
    $(`#rowPeriod_${row}`).remove();
});

function daterangePeriod(elementRange, elementStart, elementEnd, elementIdHistory, elementStatus, start_date='', end_date='', id_survey_history='', status_history='') {

    let separator = '   to   ';
    let start = (start_date=='' || start_date==null) ? moment() : start_date;
    let end = (end_date=='' || end_date==null) ? moment() : end_date;

    $(`#${elementRange}`).daterangepicker({
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
        $(`#${elementStart}`).val(start.format('YYYY-MM-DD'));
        $(`#${elementEnd}`).val(end.format('YYYY-MM-DD'));
    });
    
    if($(`#${elementStart}`).val()=='' || $(`#${elementEnd}`).val()==''){
        $(`#${elementStart}`).val(moment().format('YYYY-MM-DD'));
        $(`#${elementEnd}`).val(moment().format('YYYY-MM-DD'));
    } else {
        $(`#${elementStart}`).val(start);
        $(`#${elementEnd}`).val(end);
    }
    $(`#${elementIdHistory}`).val(id_survey_history);
    if(status_history!=''){
        $(`#${elementStatus}`).val(status_history).trigger('change');
    }

}

let num_period = 0;
const addPeriod = async (element, countFromEdit=false) => {
    try {
        let elementRange = `period_${num_period}`;
        let elementStart = `startperiod_${num_period}`;
        let elementEnd = `endperiod_${num_period}`;
        let elementStatusPeriod = `statusperiod_${num_period}`;
        let elementidSurveyHistory = `surveyhistory_${num_period}`;
        let inputStatus = ``;
        let del_button = ``;

        inputStatus = `<select name="statusperiod[${num_period}]" id="${elementStatusPeriod}" class="form-control form-control-sm">
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                                <span class="invalid-feedback" role="alert" id="${elementStatusPeriod}Error">
                                    <strong></strong>
                                </span>`;

        if(!countFromEdit){
            if(num_period > 0){
                del_button = `<div class="col-sm-1"><button onclick="return false;" class="del_period pull-right btn btn-xs btn-danger" row_period="${num_period}"><span class="fas fa-trash"></span></button></div>`;
            }
        }

        d = `<div id="rowPeriod_${num_period}">
                <div class="row" style="margin-bottom:5px;">
                    <div class="col-md-7">
                        <div class="input-group">
                            <input hidden name="id_survey_history[${num_period}]" id="${elementidSurveyHistory}" >
                            <input name="period[${num_period}]" id="${elementRange}" type="daterange" class="form-control form-control-sm"/>
                            <input name="startperiod[${num_period}]" id="${elementStart}" class="form-control form-control-sm" hidden>
                            <input name="endperiod[${num_period}]" id="${elementEnd}" class="form-control form-control-sm" hidden>                                       
                            <div class="input-group-append">
                                <span class="input-group-text far fa-calendar form-control-sm"></span>
                            </div>
                            <span class="invalid-feedback" role="alert" id="period_${num_period}Error">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        ${inputStatus}
                    </div>
                    ${del_button}
                </div>
            </div>`;
        $(`.${element}`).append(d);
        $(`#${elementStatusPeriod}`).select2({
            width: '100%'
        });
        daterangePeriod(elementRange, elementStart, elementEnd, elementidSurveyHistory, elementStatusPeriod)
        num_period += 1; 
    } catch (error) {
        console.log(error);
    }
};

async function addPeriodFromEdit(response) {
    $.each(response, function(i, item) {
        addPeriod('all_period', response.length);
    });
}

$(document).on('click', '.duplicate', function() {
    $('#duplicate_id_company').attr('id-survey-header', $(this).attr('id-survey-header'));
    $('#modal-duplicate-survey').modal('show');
});

$(document).on('click', '#btn-duplicate', function() {
    $.ajax({
        url: '{{ route("survey.duplicate_survey") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id_company: $('#duplicate_id_company').val(),
            id_survey_header: $('#duplicate_id_company').attr('id-survey-header'),
        },
        success: (res) => {
            swal({
                icon: 'success',
                title: 'Success',
                text: res.message,
            })
            $('#modal-duplicate-survey').modal('hide');
        },
        error: (err) => {
            swal({
                icon: 'error',
                title: 'Error',
                text: err.responseJSON.message,
            })
        }
    })
})

</script>
@endsection