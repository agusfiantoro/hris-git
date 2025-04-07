@extends('adminlte::page')
@section('title', 'Event Management')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Event Management</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Event</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="event_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
                        <th></th>
						<th></th>
						<th>No</th>
						<th>Event Name</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Type</th>
						<th>Notes</th>
                        <th>Created By</th>
                        <th>Responsible By</th>
                        <th>Venue</th>
                        <th>Public</th>
                        <th>Status</th>
						<th data-priority="2" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_event"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="eventForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Add Event</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Event Name</label>
								<div class="col-sm-8">
                                    <input type="hidden" name="id_event_management" id="id_event_management" class="form-control form-control-sm">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm datepicker" autocomplete="off">
                                    <span class="invalid-feedback" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
								<div class="col-sm-8">
                                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm datepicker" autocomplete="off">
									<span class="invalid-feedback" role="alert" id="end_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Timezone</label>
                                <div class="col-sm-8">
                                    <select name="id_timezone" id="id_timezone" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_timezoneError">
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
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attendees Limit</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" name="limit_registration" id="limit_registration" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="limit_registrationError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Public Registration</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" name="public" id="public" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="publicError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row end_registration" style="display:none;">
                                <label class="col-sm-4 col-form-label">End Date Registration</label>
                                <div class="col-sm-8">
                                    <input name="end_date_registration" id="end_date_registration" class="form-control form-control-sm datepicker" autocomplete="off">
                                    <span class="invalid-feedback" role="alert" id="end_date_registrationError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row end_registration" style="margin-bottom:5px;display:none;">
                                <label class="col-md-4 col-form-label">Attachment</label>
                                <div class="col-md-8">
                                    <div class="custom-file">
                                        <input type="file" name="attachment" class="custom-file-input" id="attachment">
                                        <span class="invalid-feedback" role="alert" id="attachmentError">
                                            <strong></strong>
                                        </span>
                                        <label class="custom-file-label" for="customFile"><i>Browse File</i></label>
                                    </div>
                                    <br>
                                    <img id="attachment_preview" alt="" style="width: 150px; height: 100px;display: none;">
                                    <span class="font-italic text-primary" id="attachmentFile"></span>
                                </div>
                            </div>
                            <div class="row end_registration" style="display:none;">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea style="max-height:30px;" class="form-control form-control-sm summernote" id="long_description" name="long_description" ></textarea>
                                    <span class="invalid-feedback" role="alert" id="long_descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
								<div class="col-sm-8">
                                    <select name="id_event_type" id="id_event_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_event_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Created By</label>
                                <div class="col-sm-8">
                                    <select name="organized_by" id="organized_by" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="organized_byError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Responsible By</label>
                                <div class="col-sm-8">
                                    <select name="responsible_by" id="responsible_by" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="responsible_byError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Venue</label>
                                <div class="col-sm-8">
                                    <input type="text" name="venue" id="venue" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="venueError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Room</label>
                                <div class="col-sm-8">
                                    <select name="event_room" id="event_room" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="event_roomError">
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
					</div>
                    <div class="row tab">
                        <div class="col-md-12">
			            <hr/>
                            <ul class="nav nav-tabs" id="tab_menu_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_event_program" data-toggle="tab" href="#tab_event_program" role="tab" aria-controls="link_tab_event_program" aria-selected="true">Program<span class="error-tab text-red"></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="link_tab_attendees" data-toggle="tab" href="#tab_attendees" role="tab" aria-controls="link_tab_attendees" aria-selected="true">Attendees<span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="" style="font-size:12px">
                                <div class="tab-pane fade active show" id="tab_event_program" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row justify-content-end">
                                        <div class="col-md-2 pull-right" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-sm btn-primary btn-rounded" id="new_event"><span class="fas fa-plus"></span> Add Program</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_event" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="">No.</th>
                                                        <th style="min-width: 250px;">Name</th>
                                                        <th style="min-width: 170px;">Start Date</th>
                                                        <th style="min-width: 170px;">End Date</th>
                                                        <th style="min-width: 90px;">Pass Score</th>
                                                        <th style="width:30px;">Max. Attendee</th>
                                                        <th style="min-width: 150px;">Status</th>
                                                        <th style="min-width: 100px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body_event">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_eventError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="tab_attendees" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row justify-content-end">
                                        <div class="col-sm-2 pull-right">
                                            <input type="text" id="search_attendee" class="form-control form-control-sm" placeholder="Search Attendee Name" />
                                        </div>
                                        <div class="col-sm-2 pull-right">
                                            <input type="text" id="search_email" class="form-control form-control-sm" placeholder="Search Email" />
                                        </div>
                                        <div class="col-md-2 pull-right" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-sm btn-primary btn-rounded" id="new_attendees"><span class="fas fa-plus"></span> Add Attendees</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;overflow-x: scroll;">
                                            <table id="table_attendees" class="display table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th>No.</th>
                                                        <th>Booked By</th>
                                                        <th style="min-width: 200px;">Name</th>
                                                        <th style="min-width: 170px;">Start Date</th>
                                                        <th style="min-width: 170px;">End Date</th>
                                                        <th style="min-width: 250px;">Attendee Name</th>
                                                        <th style="min-width: 200px;">ID Card</th>
                                                        <th style="min-width: 200px;">Email</th>
                                                        <th style="min-width: 200px;">Phone</th>
                                                        <th>Invited By</th>
                                                        <th style="min-width: 150px;">Inactive Attendee</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body_attendees">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_attendeesError">
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
                <table id="sample_table_event">
                    <tr id="">
                        <td>
							<span class="sn text-center" style="vertical-align:middle;"></span>
                            <input name="event[0][id_event_program]" id="event_0_id_event_program" type="hidden" class="form-control form-control-sm id_event_program_input">
						</td>
                        <td>
                            <input name="event[0][description]" id="event_0_description" type="text" class="form-control form-control-sm description_input">
                            <span class="invalid-feedback description_input_error" role="alert" id="event_0_descriptionError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="event[0][start_date]" id="event_0_start_date" type="text" class="form-control form-control-sm start_date_input" autocomplete="off">
                            <span class="invalid-feedback start_date_input_error" role="alert" id="event_0_start_dateError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="event[0][end_date]" id="event_0_end_date" type="text" class="form-control form-control-sm end_date_input" autocomplete="off">
                            <span class="invalid-feedback end_date_input_error" role="alert" id="event_0_end_dateError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="event[0][pass_scores]" id="event_0_pass_scores" type="text" class="form-control form-control-sm pass_scores_input">
                            <span class="invalid-feedback pass_scores_input_error" role="alert" id="event_0_pass_scoresError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="event[0][maximum]" id="event_0_maximum" type="text" class="form-control form-control-sm maximum_input">
                            <span class="invalid-feedback maximum_input_error" role="alert" id="event_0_maximumError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <select name="event[0][status]" id="event_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback status_input_error" role="alert" id="event_0_statusError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
							<center>
                                <a href="javascript:;" class="save-record-event btn btn-xs btn-primary" data-id="0" title="Save & Set to Attendees"><span class="fas fa-check" style="margin-bottom: 5px;"></span></a>
								<button type="button" class="delete-record-event btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
							</center>
						</td>
                    </tr>
                </table>

                <table id="sample_table_attendees">
                    <tr id="">
                        <td>
                            <span class="sn text-center" style="vertical-align:middle;"></span>
                        </td>
                        <td>
                            <input name="attendee[0][id_event_attendees]" id="attendee_0_id_event_attendees" type="hidden" class="form-control form-control-sm id_event_attendees_input">
                            <select name="attendee[0][booked_by]" id="attendee_0_booked_by" class="form-control form-control-sm select2 booked_by_input" data-id="0" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback booked_by_input_error" role="alert" id="attendee_0_booked_byError">
                                <strong></strong>
                            </span>     
                        </td>
                        <td>
                            <select name="attendee[0][id_event_program][]" id="attendee_0_id_event_program" class="form-control form-control-sm select2 id_event_program_input" multiple="multiple" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback id_event_program_input_error" role="alert" id="attendee_0_id_event_programError">
                                <strong></strong>
                            </span> 
                        </td>
                        <td>
                            <input name="attendee[0][start_date]" id="attendee_0_start_date" type="text" class="form-control form-control-sm start_date_input" autocomplete="off">
                            <span class="invalid-feedback start_date_input_error" role="alert" id="attendee_0_start_dateError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="attendee[0][end_date]" id="attendee_0_end_date" type="text" class="form-control form-control-sm end_date_input" autocomplete="off">
                            <span class="invalid-feedback end_date_input_error" role="alert" id="attendee_0_end_dateError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="attendee[0][attendee_name]" id="attendee_0_attendee_name" type="text" class="form-control form-control-sm attendee_name_input">
                            <span class="invalid-feedback attendee_name_input_error" role="alert" id="attendee_0_attendee_nameError">
                                <strong></strong>
                            </span>  
                        </td>
                        <td>
                            <input name="attendee[0][national_identity_card]" id="attendee_0_national_identity_card" type="text" class="form-control form-control-sm national_identity_card_input">
                            <span class="invalid-feedback national_identity_card_input_error" role="alert" id="attendee_0_national_identity_cardError">
                                <strong></strong>
                            </span>  
                        </td>
                        <td>
                            <input name="attendee[0][attendee_email]" id="attendee_0_attendee_email" type="text" class="form-control form-control-sm attendee_email_input">
                            <span class="invalid-feedback attendee_email_input_error" role="alert" id="attendee_0_attendee_emailError">
                                <strong></strong>
                            </span>  
                        </td>
                        <td>
                            <input name="attendee[0][attendee_phone]" id="attendee_0_attendee_phone" type="text" class="form-control form-control-sm attendee_phone_input">
                            <span class="invalid-feedback attendee_phone_input_error" role="alert" id="attendee_0_attendee_phoneError">
                                <strong></strong>
                            </span>  
                        </td>
                        <td>
                            <select name="attendee[0][invited_by]" id="attendee_0_invited_by" class="form-control form-control-sm select2 invited_by_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback invited_by_input_error" role="alert" id="attendee_0_invited_byError">
                                <strong></strong>
                            </span>     
                        </td>
                        <td>
                            <input name="attendee[0][status]" id="attendee_0_status" type="checkbox" class="form-control form-control-sm status_input">
                            <span class="invalid-feedback status_input_error" role="alert" id="attendee_0_statusError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <center>
                                <button type="button" class="delete-record-attendees btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
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

@endsection

@section('css')
<style type="text/css">	
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
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
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_event_management = "";
    let global_id_event_program = "";
    let global_select_program = [];
    let global_select_employee = [];
    let global_select_course = [];
    let global_select_checklist = [];
    let global_id_event = 0;
    let global_id_attendees = 0;
    let global_id_user_session = 0;
    let global_all_program = [];
    let global_all_room = [];
    let startDateAwal;
    let endDateAwal;
    let x = [];
    let listEmployeeHR = [];
    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];
    let today = new Date().toISOString().slice(0, 10)

    $(function () {	
        bsCustomFileInput.init();

        get_event_type();
        get_timezone();
        get_employee().then(res => {
            $('.get_employee, booked_by_input, invited_by_input, #organized_by').prepend('<option selected></option>').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: global_select_employee,
            });
            $('#managed_by').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: global_select_employee,
            });
        });
        get_course();
        get_checklist();
        getEmployeeHR()
        get_room().then(res => {
            $('#event_room').select2({
                placeholder: "Select Room",
                allowClear: true,
                data: global_all_room,
            });
        });

        $('.datepicker').each(function(){
            thisId = $(this).attr('id');
            if(thisId=='start_date' || thisId=='end_date'){
                $(`#${thisId}`).datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd',
                }).on("change", function(e) {
                    let str_start = moment($(`#start_date`).val(), 'YYYY-MM-DD').valueOf();
                    let str_end = moment($(`#end_date`).val(), 'YYYY-MM-DD').valueOf();

                    if(($(`#start_date`).val()!='' && $(`#end_date`).val()!='') && str_end < str_start){
                        $(`#start_date`).val(startDateAwal)
                        $(`#end_date`).val(endDateAwal)
                        swal({
                            icon: 'error',
                            title: '',
                            dangerMode: true,
                            text: 'Start date must be less than end date',
                            timer: 1800
                        });
                    }
                    startDateAwal = $(`#start_date`).val();
                    endDateAwal = $(`#end_date`).val();

                    if(startDateAwal != '' && endDateAwal != ''){
                        assignDetailDateByHeader(startDateAwal, endDateAwal)
                    }
                });
            } else {
                $(this).datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd',
                });
            }
        });

		$('.summernote').summernote({
            height:120,
        });

        $('#event_table').DataTable({
            processing: true,
            responsive: true,
            // serverSide: true,
            ajax: {
                url: "{{ url('event_management/event/event_management') }}",
                error: function (jqXHR, textStatus, errorThrown) {
                        $('#event_table').DataTable().ajax.reload();
                    }
              },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
                {   // Checkbox select column
                    data: 'id_event_management',
                    defaultContent: '',
                    orderable: false
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'event', name: 'event' },
                { data: 'start_date', name: 'start_date' , 
                    render: function ( data, type, row ) {  
                        let start = moment(row.start_date, 'YYYY-MM-DD');
                        return start.format("DD-MMM-YYYY");
                    }
                },
                { data: 'end_date', name: 'end_date' , 
                    render: function ( data, type, row ) {  
                        let end = moment(row.end_date, 'YYYY-MM-DD');
                        return end.format("DD-MMM-YYYY");
                    }
                },
                { data: 'type', name: 'type' },
                { data: 'notes', name: 'notes' },
                { data: 'organizer', name: 'organizer' },
                { data: 'responsible', name: 'responsible' },
                { data: 'venue', name: 'venue' },
                { data: 'public', name: 'public', 
                    render: function ( data, type, row ) {  
                        if(data=='0' || data==false || data==null){
                            published = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
                        } else {
                            published = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
                        }
                        return published;
                    }
                },
                { data: 'status', name: 'status' },
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
        
        $('#eventForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#eventForm')[0]);
            let urlForm = '';

            if(global_id_event_management == ''){
                urlForm = "{{ url('event_management/event/event_management/save') }}";
            } else {
                urlForm = "{{ url('event_management/event/event_management/update') }}";
                formData.append('id_event_management', global_id_event_management);
            }

            $(".invalid-feedback").children("strong").text("");
            $("#eventForm input").removeClass("is-invalid");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: { Accept: "application/json", },
                contentType:false,
                cache: false,
                processData:false,
                url: urlForm,
                data: formData,
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    if (response.status == 'true') {
                         $('#modal_form_event').modal('hide');
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
                        let firstObject = Object.entries(errors)[0][0];
                        $(`#modal_form_event`).animate({scrollTop: $(`#${firstObject}Error`).offset().top +'px'}, 1000);
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

        $('#limit_registration').click(function(){
            check_limit_registration()
        });
        

    });

    $(document).on('click', '.new', function () {
        run_in_modal('new')
        global_id_event_management = "";
        $("#eventForm")[0].reset();
        $("#table_body_event").html("");
        $("#table_body_attendees").html("");
        $("#eventForm .modal-title").html("<span class='fas fa-plus'></span> Add Event");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#eventForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');  
        $('#attachment_preview').attr("src", '').hide();
        $('#modal_form_event').modal('show');
    });
    
    $(document).on('change', '#attachment', function (event) {
        const file = this.files[0];
        if (file) {
            if(file.type == 'image/png' || file.type == 'image/jpeg' || file.type == 'image/gif'){
                let reader = new FileReader();
                reader.onload = function (event) {
                    $("#attachment_preview").attr("src", event.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                swal({
                    icon: 'error',
                    title: '',
                    dangerMode: true,
                    text: 'Please upload Image file',
                    timer: 1800
                });
                $("#attachment").val('');
                $(".custom-file-label").html('&nbsp;');
                return false;
            }
        }
    });

    $(document).on('click', '#new_event', function (e, fromTrigger) {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        let str_start = moment(startDate, 'YYYY-MM-DD').valueOf();
        let str_end = moment(endDate, 'YYYY-MM-DD').valueOf();

        if(fromTrigger!='trigger'){ 
            //kondisi yg berasal selain dari klik edit program
            if(startDate == '' || endDate == ''){
                swal({
                    icon: 'error',
                    title: '',
                    dangerMode: true,
                    text: 'Fill start and end date first',
                    timer: 1500
                });
                return false;
            }
            if(str_end < str_start){
                swal({
                    icon: 'error',
                    title: '',
                    dangerMode: true,
                    text: 'Start date must be less than end date',
                    timer: 1800
                });
                return false;
            }
        }

        var content = jQuery('#sample_table_event tr'),
            size = global_id_event++,
            element = null,
            element = content.clone();
        element.attr('id','rec-event-'+size);
        element.find('.delete-record-event').attr('data-id', size);
        element.find('.save-record-event').attr('data-id', size);

        element.find('.id_event_program_input').attr('id', 'event_' + size + '_id_event_program');
        element.find('.id_event_program_input').attr('name', 'event[' + size + '][id_event_program]');

        element.find('.id_course_header_input').attr('id', 'event_' + size + '_id_course_header');
        element.find('.id_course_header_input').attr('name', 'event[' + size + '][id_course_header]');
        element.find('.id_course_header_input_error').attr('id', 'event_' + size + '_id_course_headerError');
        element.find('.id_course_header_input').prepend('<option selected></option>').select2({
            placeholder: "Select Course",
            allowClear: true,
            data: global_select_course
        });

        element.find('.id_checklist_input').attr('id', 'event_' + size + '_id_checklist');
        element.find('.id_checklist_input').attr('name', 'event[' + size + '][id_checklist]');
        element.find('.id_checklist_input_error').attr('id', 'event_' + size + '_id_checklistError');
        element.find('.id_checklist_input').prepend('<option selected></option>').select2({
            placeholder: "Select Checklist",
            allowClear: true,
            data: global_select_checklist
        });

        element.find('.description_input').attr('id', 'event_' + size + '_description');
        element.find('.description_input').attr('name', 'event[' + size + '][description]');
        element.find('.description_input_error').attr('id', 'event_' + size + '_descriptionError');

        element.find('.start_date_input').attr('id', 'event_' + size + '_start_date');
        element.find('.start_date_input').attr('name', 'event[' + size + '][start_date]');
        element.find('.start_date_input_error').attr('id', 'event_' + size + '_start_dateError');
        element.find('.start_date_input').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            minDate: startDate,
            maxDate: endDate,
        });

        element.find('.end_date_input').attr('id', 'event_' + size + '_end_date');
        element.find('.end_date_input').attr('name', 'event[' + size + '][end_date]');
        element.find('.end_date_input_error').attr('id', 'event_' + size + '_end_dateError');
        element.find('.end_date_input').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            minDate: startDate,
            maxDate: endDate,
        });

        element.find('.pass_scores_input').attr('id', 'event_' + size + '_pass_scores');
        element.find('.pass_scores_input').attr('name', 'event[' + size + '][pass_scores]');
        element.find('.pass_scores_input_error').attr('id', 'event_' + size + '_pass_scoresError');

        element.find('.maximum_input').attr('id', 'event_' + size + '_maximum');
        element.find('.maximum_input').attr('name', 'event[' + size + '][maximum]');
        element.find('.maximum_input_error').attr('id', 'event_' + size + '_maximumError');

        element.find('.status_input').attr('id', 'event_' + size + '_status');
        element.find('.status_input').attr('name', 'event[' + size + '][status]');
        element.find('.status_input_error').attr('id', 'event_' + size + '_statusError');
        element.find('.status_input').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });
        
        element.appendTo('#table_body_event');
        $('#table_body_event tr').each(function (index) {
            $(this).find('td:eq(0)').addClass('text-center');
            $(this).find('span.sn').html(index + 1);
        });
        check_limit_registration()
    });

    $(document).on('click', '.delete-record-event', function () {
        let counter = $(this).attr('data-id');
        let id_event_program = $(`#event_${counter}_id_event_program`).val();
        destroy_event_program(counter, id_event_program);
    });

    $(document).on('click', '.save-record-event', function () {
        let counter = $(this).attr('data-id');
        save_event(counter);
    });

    $(document).on('click', '#new_attendees', function (e, fromTrigger) {
        let startDate = $('#start_date').val();
        let endDate = $('#end_date').val();
        let str_start = moment(startDate, 'YYYY-MM-DD').valueOf();
        let str_end = moment(endDate, 'YYYY-MM-DD').valueOf();

        if(fromTrigger!='trigger'){ 
            //kondisi yg berasal selain dari klik edit program
            if(startDate == '' || endDate == ''){
                swal({
                    icon: 'error',
                    title: '',
                    dangerMode: true,
                    text: 'Fill start and end date first',
                    timer: 1500
                });
                return false;
            }
            if(str_end < str_start){
                swal({
                    icon: 'error',
                    title: '',
                    dangerMode: true,
                    text: 'Start date must be less than end date',
                    timer: 1800
                });
                return false;
            }
        }

        var content = jQuery('#sample_table_attendees tr'),
                size = global_id_attendees++,
                element = null,
                element = content.clone();
        element.attr('id','rec-attendees-'+size);
        element.find('.delete-record-attendees').attr('data-id', size);
        
        element.find('.id_event_attendees_input').attr('id', 'attendee_' + size + '_id_event_attendees');
        element.find('.id_event_attendees_input').attr('name', 'attendee[' + size + '][id_event_attendees]');

        element.find('.booked_by_input').attr('id', 'attendee_' + size + '_booked_by');
        element.find('.booked_by_input').attr('name', 'attendee[' + size + '][booked_by]');
        element.find('.booked_by_input').attr('data-id', size);
        element.find('.booked_by_input').addClass('booked_by_input');
        element.find('.booked_by_input_error').attr('id', 'attendee_' + size + '_booked_byError');
        element.find('.booked_by_input').prepend('<option selected></option>').select2({
            placeholder: "Select Employee",
            allowClear: true,
            data: global_select_employee
        });

        element.find('.id_event_program_input').attr('id', 'attendee_' + size + '_id_event_program');
        element.find('.id_event_program_input').attr('name', 'attendee[' + size + '][id_event_program][]');
        element.find('.id_event_program_input_error').attr('id', 'attendee_' + size + '_id_event_programError');
        element.find('.id_event_program_input').select2({
            placeholder: "Select Course",
            allowClear: true,
            data: global_select_program
        });

        element.find('.start_date_input').attr('id', 'attendee_' + size + '_start_date');
        element.find('.start_date_input').attr('name', 'attendee[' + size + '][start_date]');
        element.find('.start_date_input_error').attr('id', 'attendee_' + size + '_start_dateError');
        element.find('.start_date_input').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            minDate: startDate,
            maxDate: endDate,
        });

        element.find('.end_date_input').attr('id', 'attendee_' + size + '_end_date');
        element.find('.end_date_input').attr('name', 'attendee[' + size + '][end_date]');
        element.find('.end_date_input_error').attr('id', 'attendee_' + size + '_end_dateError');
        element.find('.end_date_input').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            minDate: startDate,
            maxDate: endDate,
        });

        element.find('.attendee_name_input').attr('id', 'attendee_' + size + '_attendee_name');
        element.find('.attendee_name_input').attr('name', 'attendee[' + size + '][attendee_name]');
        element.find('.attendee_name_input_error').attr('id', 'attendee_' + size + '_attendee_nameError');

        element.find('.national_identity_card_input').attr('id', 'attendee_' + size + '_national_identity_card');
        element.find('.national_identity_card_input').attr('name', 'attendee[' + size + '][national_identity_card]');
        element.find('.national_identity_card_input_error').attr('id', 'attendee_' + size + '_national_identity_cardError');

        element.find('.attendee_email_input').attr('id', 'attendee_' + size + '_attendee_email');
        element.find('.attendee_email_input').attr('name', 'attendee[' + size + '][attendee_email]');
        element.find('.attendee_email_input_error').attr('id', 'attendee_' + size + '_attendee_emailError');

        element.find('.attendee_phone_input').attr('id', 'attendee_' + size + '_attendee_phone');
        element.find('.attendee_phone_input').attr('name', 'attendee[' + size + '][attendee_phone]');
        element.find('.attendee_phone_input_error').attr('id', 'attendee_' + size + '_attendee_phoneError');

        
        // element.find('#attendee_' + size + '_id_event_program').val(global_all_program).trigger('change');


        element.find('.invited_by_input').attr('id', 'attendee_' + size + '_invited_by');
        element.find('.invited_by_input').attr('name', 'attendee[' + size + '][invited_by]');
        element.find('.invited_by_input_error').attr('id', 'attendee_' + size + '_invited_byError');
        element.find('.invited_by_input').prepend('<option selected></option>').select2({
            placeholder: "Select Employee",
            allowClear: true,
            data: global_select_employee
        });
        element.find('#attendee_' + size + '_invited_by').val($('#responsible_by').find(':selected').val()).trigger('change');


        element.find('.status_input').attr('id', 'attendee_' + size + '_status');
        element.find('.status_input').attr('name', 'attendee[' + size + '][status]');
        element.find('.status_input_error').attr('id', 'attendee_' + size + '_statusError');
        
        element.appendTo('#table_body_attendees');
        $('#table_body_attendees tr').each(function (index) {
            $(this).find('td:eq(0)').addClass('text-center');
            $(this).find('span.sn').html(index + 1);
        });
    });

    $(document).on('click', '.delete-record-attendees', function () {
        var id = jQuery(this).attr('data-id');
        var targetDiv = jQuery(this).attr('targetDiv');
        jQuery('#rec-attendees-' + id).remove();
        $('#table_body_attendees tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
        return true;
    });


    $(document).on('click', '.edit', function () {
        run_in_modal('edit')

        let id_event_management = $(this).attr('id');
        global_id_event_management = id_event_management;
        $("#eventForm")[0].reset();
        $("#table_body_event").html("");
        $("#table_body_attendees").html("");
        $("#eventForm .modal-title").html("<span class='fas fa-edit'></span> Edit Event");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#eventForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#attachment_preview').hide();
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "{{ url('event_management/event/event_management/get_event_edit') }}",
            method: "GET",
            data: {id_event_management: id_event_management},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (res) {
                let dataEvent           = res.event;
                global_select_program   = res.program;
                global_all_program      = res.all_id_program;
                global_id_event         = 0;                   
                global_id_attendees     = 0;

                if(res.allow_save == false){
                    $('#new_event').hide();
                    $('#new_attendees').hide();
                    $('#save_button').hide();
                    $('.save-record-event').hide();
                    $('.delete-record-event').hide();
                }

                //value start_date dan end_date harus ditaruh lebih dulu
                $('#start_date').val(dataEvent.start_date);
                $('#end_date').val(dataEvent.end_date);

                $.each(dataEvent.event, function (i, item) {
                    $('#new_event').trigger('click', ['trigger']);
                });
                $.each(dataEvent.attendee, function (i, item) {
                    $('#new_attendees').click();
                });

                $('#id_event_management').val(dataEvent.id_event_management);
                $('#description').val(dataEvent.description);
                
                $('#id_timezone').val(dataEvent.id_timezone).trigger('change');
                if(dataEvent.limit_registration != null){
                    $('#limit_registration').attr('checked', true);
                } else {
                    $('#limit_registration').removeAttr('checked');
                }
                $('#end_date_registration').val(dataEvent.end_date_registration);
                $('#id_event_type').val(dataEvent.id_event_type).trigger('change');
                if(dataEvent.attachment != null){
                    $('#attachment_preview').attr(`src`,dataEvent.attachment_path).html(`<a href="${dataEvent.attachment_path}" target="_blank"><img src="${dataEvent.attachment_path}"></a>`).show();
                } else {
                    $('#attachment_preview').attr(`src`,'').html('').hide();
                }
                $('#organized_by').val(dataEvent.organized_by).attr('readonly', true).trigger('change');
                if(res.allow_save == false){
                    $('#responsible_by').val(dataEvent.responsible_by).attr('readonly', true).trigger('change');
                    $('#managed_by').val(dataEvent.managed_by).attr('readonly', true).trigger('change');
                } else {
                    $('#responsible_by').val(dataEvent.responsible_by).trigger('change');
                    $('#managed_by').val(dataEvent.managed_by).trigger('change');
                }
                $('#venue').val(dataEvent.venue);
                $('#notes').val(dataEvent.notes);
                $('#status').val(dataEvent.status).trigger('change');
                if(dataEvent.public == 1) {
                    $('#public').prop('checked', true);
                } else {
                    $('#public').prop('checked', false);
                }
                $("#long_description").summernote("code", dataEvent.long_description);
                checkPublic()
                
                $('#table_body_event tr').each(function (index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find(`#event_${index}_id_event_program`).val(dataEvent.event[index].id_event_program).trigger('change');
                    $(this).find(`#event_${index}_id_course_header`).val(dataEvent.event[index].id_course_header).trigger('change');
                    $(this).find(`#event_${index}_id_checklist`).val(dataEvent.event[index].id_checklist).trigger('change');
                    $(this).find(`#event_${index}_description`).val(dataEvent.event[index].description);
                    $(this).find(`#event_${index}_start_date`).val(moment(dataEvent.event[index].start_date).format('YYYY-MM-DD'));
                    $(this).find(`#event_${index}_end_date`).val(moment(dataEvent.event[index].end_date).format('YYYY-MM-DD'));
                    $(this).find(`#event_${index}_pass_scores`).val(dataEvent.event[index].pass_scores);
                    $(this).find(`#event_${index}_maximum`).val(dataEvent.event[index].maximum);
                    $(this).find(`#event_${index}_status`).val(dataEvent.event[index].status).trigger('change');

                });

                $('#table_body_attendees tr').each(function (index) {
                    $(this).find('span.sn').html(index + 1);
                    // $(this).find(`#attendee_${index}_id_event_attendees`).val(dataEvent.attendee[index].id_event_attendees);
                    $(this).find(`#attendee_${index}_booked_by`).val(dataEvent.attendee[index].booked_by).trigger('change',['trigger']);
                    $(this).find(`#attendee_${index}_id_event_program`).val(dataEvent.attendee[index].event_program).trigger('change');

                    if(dataEvent.attendee[index].start_date != null){
                        $(this).find(`#attendee_${index}_start_date`).val(moment(dataEvent.attendee[index].start_date).format('YYYY-MM-DD'));
                    }
                    if(dataEvent.attendee[index].end_date != null){
                        $(this).find(`#attendee_${index}_end_date`).val(moment(dataEvent.attendee[index].end_date).format('YYYY-MM-DD'));
                    }
                    $(this).find(`#attendee_${index}_attendee_name`).val(dataEvent.attendee[index].attendee_name).attr('value', dataEvent.attendee[index].attendee_name);
                    $(this).find(`#attendee_${index}_national_identity_card`).val(dataEvent.attendee[index].national_identity_card);
                    $(this).find(`#attendee_${index}_attendee_email`).val(dataEvent.attendee[index].attendee_email).attr('value', dataEvent.attendee[index].attendee_email);
                    $(this).find(`#attendee_${index}_attendee_phone`).val(dataEvent.attendee[index].attendee_phone);
                    $(this).find(`#attendee_${index}_invited_by`).val(dataEvent.attendee[index].invited_by).trigger('change');

                    if(dataEvent.attendee[index].status == 'Cancel'){
                        $(this).find(`#attendee_${index}_status`).prop('checked', true);
                    } else {
                        $(this).find(`#attendee_${index}_status`).prop('checked', false);
                    }

                    $(this).find(`.delete-record-attendees[data-id="${index}"]`).hide();
                });
                
                check_limit_registration()
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
    
        $('#modal_form_event').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
    	id_event_management = $(this).attr('id');
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
                    url: "{{ url('event_management/event/event_management/destroy') }}",
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    method: "POST",
                    data: {id_event_management: id_event_management},
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

    $(document).on('change', '.booked_by_input', function (event, trigger='') {
        let counter = $(this).attr('data-id');
        let value   = $(this).val();
        if(trigger != 'trigger'){
            if(!(value == '' || value == null)){
                get_employee_detail(counter, value);
            }
        }
    });

    function run_in_modal(stage='') {
        $('#status').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        }); 

        $('#responsible_by').select2({
            placeholder: "Select Employee",
            allowClear: true,
            data: listEmployeeHR
        }); 

        get_user_by_session().then(res => {
            global_id_user_session = (res.length > 0) ? res[0].id_employee : 1;
            if(stage=='new'){
                $('#organized_by').val(global_id_user_session).attr('readonly', true).trigger('change');
            }
        });
    }

    function on_close_modal() {
        // $('#content_table').DataTable().ajax.reload();
        global_id_event_management = '';
        window.location.reload();
    }

    function get_timezone() {
        $.getJSON('<?= url('event_management/event/event_management/get_timezone') ?>', function (data) {
            $('#id_timezone').prepend('<option selected></option>').select2({
                placeholder: "Select Timezone",
                data: data,
                allowClear: true,
            });
        }).fail(function (data) { // Call failed
            get_timezone();
        }); 
    }   

    function get_event_type() {
        $.getJSON('<?= url('event_management/event/event_management/get_event_type') ?>', function (data) {
            $('#id_event_type').prepend('<option selected></option>').select2({
                placeholder: "Select Type",
                data: data,
                allowClear: true,
            });
        }).fail(function (data) { // Call failed
            get_event_type();
        }); 
    }   

    function save_event(counter) {
        let eventForm = $('#eventForm').serializeArray();
        global_id_event_program = $(`#event_${counter}_id_event_program`).val();

        if(global_id_event_program == '' && global_id_event_management == ''){
            eventForm.push({name:'counter', value: counter});
        } else {
            eventForm.push({name:'counter', value: counter});
            eventForm.push({name:'id_event_program', value: global_id_event_program});
            eventForm.push({name:'id_event_management', value: global_id_event_management});
        }

        $(".invalid-feedback").children("strong").text("");
        $("#eventForm input").removeClass("is-invalid");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
            
        $.ajax({
            url: "{{ url('event_management/event/event_management/save_event') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: eventForm,
            success:function(res)
            {
                global_id_event_management = res.data.global_id_event_management;
                global_select_program = res.data.global_select_program;
                global_all_program = res.data.all_id_event_program;

                $('#table_body_attendees tr').each(function (index) {
                    selectedCourse = $(this).find(`.id_event_program_input`).val();
                    $(this).find(`.id_event_program_input`).html('');
                    $(this).find(`.id_event_program_input`).select2({
                        placeholder: "Select Course",
                        allowClear: true,
                        data: global_select_program
                    });
                    $(`#attendee_${index}_id_event_program`).val(selectedCourse).trigger('change')
                });

                $(`#event_${counter}_id_event_program`).val(res.data.id_event_program);
                
                swal({
                    icon: 'success',
                    title: "Success",
                    text: res.message,
                    buttons: false,
                    timer: 1200
                });

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
                            $("#link_tab_event_program").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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

    function destroy_event_program(counter, id_event_program) {
        let dataSend = {'id_event_program' : id_event_program, 'id_event_management' : global_id_event_management};

        $.ajax({
            url: "{{ url('event_management/event/event_management/destroy_event') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: dataSend,
            success:function(res)
            {
                if(res.status == 'false'){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: res.message
                    });
                } else {
                    $('#rec-event-'+counter).remove();
                    global_select_program = res.data.global_select_program;
                    global_all_program = res.data.all_id_event_program;

                    $('#table_body_attendees tr').each(function (index) {
                        $(this).find(`.id_event_program_input`).html('');
                        $(this).find(`.id_event_program_input`).select2({
                            placeholder: "Select Course",
                            allowClear: true,
                            data: global_select_program
                        });
                        $(`#attendee_${index}_id_event_program`).val(global_all_program).trigger('change')
                    });
                }
            },
            error: function(response) {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! [Unknown Error]'
                });
            }
        })
    }   

    function check_limit_registration() {
        if($('#limit_registration').is(":checked")){
            $('#table_body_event tr').each(function (index) {
                $(this).find('.maximum_input').removeAttr('disabled');
                $(this).find('.maximum_input').attr('name', `event[${index}][maximum]`);
            });
        } else {
            $('#table_body_event tr').each(function (index) {
                $(this).find('.maximum_input').attr('disabled', true);
                $(this).find('.maximum_input').removeAttr('name');
            });
        }
    }

    function get_employee_detail(counter, value) {
        $.getJSON('<?= url('event_management/event/event_management/get_employee_detail') ?>'+'/'+ value, function (res) {
            $(`#attendee_${counter}_attendee_name`).val(res[0].name).attr('value', res[0].name);
            $(`#attendee_${counter}_national_identity_card`).val(res[0].identification_number);
            $(`#attendee_${counter}_attendee_email`).val(res[0].work_mail).attr('value', res[0].work_mail);
            $(`#attendee_${counter}_attendee_phone`).val(res[0].mobile_phone);
        }).fail(function (fail) { // Call failed
            get_employee_detail(counter, value);
        }); 
    }   

    const get_employee = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/get_employee') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.name} (${val.nik_employee})`;
                        global_select_employee.push({id:val.id_employee, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_employee();
        }
    }

    const get_user_by_session = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/get_user_by_session') ?>',
                method: "GET",
                success: function (res) {
                },
            });
            return result;
        } catch (error) {
            get_user_by_session();
        }
    }

    const getEmployeeHR = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/getEmployeeHR') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.name} (${val.nik_employee})`;
                        listEmployeeHR.push({id:val.id_employee, text:name});
                    });
                },
            });
            return result;
        } catch (error) {
            getEmployeeHR();
        }
    }

    const get_course = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/get_course') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.course_name}`;
                        global_select_course.push({id:val.id_course_header, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_course();
        }
    }

    const get_checklist = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/get_checklist') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.checklist_type}`;
                        global_select_checklist.push({id:val.id_checklist, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_checklist();
        }
    }

    const get_room = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('event_management/event/event_management/get_room') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.description} (${val.location})`;
                        global_all_room.push({id:val.id_event_room, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_room();
        }
    }

    $(document).on('keyup', '#search_attendee', function (event) {
        var text = $(this).val();
        let count = 0;
        $('#table_body_attendees tr').each(function () {
            let index = $(this).attr('id').split("rec-attendees-")[1];
            if(text != ''){
                $(`#rec-attendees-${index}`).hide();              
                let filter_element = $(this).find('input#attendee_'+index+'_attendee_name[value*="'+text+'"]').val();

                if(filter_element != undefined){
                    count = count + 1;
                    $(`#rec-attendees-${index}`).show();
                }
            } else {
                $(`#rec-attendees-${index}`).show();
            }
        });
    });

    $(document).on('keyup', '#search_email', function (event) {
        var text = $(this).val();
        let count = 0;
        $('#table_body_attendees tr').each(function () {
            let index = $(this).attr('id').split("rec-attendees-")[1];
            if(text != ''){
                $(`#rec-attendees-${index}`).hide();              
                let filter_element = $(this).find('input#attendee_'+index+'_attendee_email[value*="'+text+'"]').val();

                if(filter_element != undefined){
                    count = count + 1;
                    $(`#rec-attendees-${index}`).show();
                }
            } else {
                $(`#rec-attendees-${index}`).show();
            }
        });
    });
    
    const assignDetailDateByHeader = (startDate, endDate) => {
        $('#table_body_event tr').each(function (index) {
            thisStartDate = $(`#event_${index}_start_date`).val();
            thisEndDate = $(`#event_${index}_end_date`).val();
            $(`#event_${index}_start_date`).datepicker('destroy');
            $(`#event_${index}_end_date`).datepicker('destroy');

            $(`#event_${index}_start_date`).datepicker({ 
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                minDate: startDate, 
                maxDate: endDate, 
            }).val(thisStartDate);

            $(`#event_${index}_end_date`).datepicker({ 
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                minDate: startDate, 
                maxDate: endDate, 
            }).val(thisEndDate);
        });

        $('#table_body_attendees tr').each(function (index) {
            thisStartDate = $(`#attendee_${index}_start_date`).val();
            thisEndDate = $(`#attendee_${index}_end_date`).val();
            $(`#attendee_${index}_start_date`).datepicker('destroy');
            $(`#attendee_${index}_end_date`).datepicker('destroy');

            $(`#attendee_${index}_start_date`).datepicker({ 
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                minDate: startDate, 
                maxDate: endDate, 
            }).val(thisStartDate);

            $(`#attendee_${index}_end_date`).datepicker({ 
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                minDate: startDate, 
                maxDate: endDate, 
            }).val(thisEndDate);
        });
    }

    $(document).on('click', '#public', function (event) {
        checkPublic()
    });

    const checkPublic = () => {
        if($('#public').is(":checked")){
            $('.end_registration').show()
        } else {
            $('.end_registration').hide()
        }
    }

</script>
@endsection