@extends('adminlte::page')
@section('title', 'Report')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Report</h5>
                <div class="card-tools">
                    <button class="btn btn-sm btn-success" id="downloadMultiple"><i class="fa fa-arrow-down"></i> Download Multiple Programs</button>
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
						<th>Program Name</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th>Type</th>
						<th>Notes</th>
                        <th>Organizer</th>
                        <th>Responsible By</th>
                        <th>Venue</th>
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
                    <h5 class="modal-title">Result Course Program</h5>
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
                                    <input type="hidden" name="id_event_management" id="id_event_management" class="form-control form-control-sm" disabled>
                                    <input type="text" name="description" id="description" class="form-control form-control-sm" disabled>
									<span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm " autocomplete="off" disabled>
                                    <span class="invalid-feedback" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
								<div class="col-sm-8">
                                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm " autocomplete="off" disabled>
									<span class="invalid-feedback" role="alert" id="end_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Timezone</label>
                                <div class="col-sm-8">
                                    <select name="id_timezone" id="id_timezone" class="form-control form-control-sm select2" style="width: 100%;" disabled>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_timezoneError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Limit Registration</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" name="limit_registration" id="limit_registration" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;" disabled>
                                    <span class="invalid-feedback" role="alert" id="limit_registrationError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">End Date Registration</label>
                                <div class="col-sm-8">
                                    <input name="end_date_registration" id="end_date_registration" class="form-control form-control-sm " autocomplete="off" disabled>
                                    <span class="invalid-feedback" role="alert" id="end_date_registrationError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
								<div class="col-sm-8">
                                    <select name="id_event_type" id="id_event_type" class="form-control form-control-sm select2" style="width: 100%;" disabled>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_event_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Organizer</label>
                                <div class="col-sm-8">
                                    <select name="organized_by" id="organized_by" class="form-control form-control-sm select2 get_employee" style="width: 100%;" disabled>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="organized_byError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Responsible By</label>
                                <div class="col-sm-8">
                                    <select name="responsible_by" id="responsible_by" class="form-control form-control-sm select2 get_employee" style="width: 100%;" disabled>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="responsible_byError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Venue</label>
                                <div class="col-sm-8">
                                    <input type="text" name="venue" id="venue" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="venueError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notes" id="notes" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="notesError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;" disabled>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom:5px;">
                                <label class="col-md-4 col-form-label">Attachment</label>
                                <div class="col-md-8">
                                    <div class="custom-file">
                                        <span class="font-italic text-primary" id="attachmentFile"></span>
                                    </div>
                                </div>
                            </div>
						</div>
					</div>
                    <div class="row tab">
                        <div class="col-md-12">
			            <hr/>
                            <ul class="nav nav-tabs" id="tab_menu_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_attendees" data-toggle="tab" href="#tab_attendees" role="tab" aria-controls="link_tab_attendees" aria-selected="true">Course<span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="" style="font-size:12px">
                                <div class="tab-pane active show fade " id="tab_attendees" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_course_" class="display table table-striped table-bordered table-hover datatable">
                                            </table>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </form>
			<div style="display:none; ">
                <table id="sample_table_attendees">
                    <tr id="">
                        <td>
                            <span class="sn text-center" style="vertical-align:middle;"></span>
                        </td>
                        <td>
                                <input name="attendee[0][attendee_name]" id="attendee_0_attendee_name" type="text" class="form-control form-control-sm attendee_name_input" disabled>
                                <span class="invalid-feedback attendee_name_input_error" role="alert" id="attendee_0_attendee_nameError">
                                    <strong></strong>
                                </span>  
                        </td>
                        <td>
                                <input name="attendee[0][national_identity_card]" id="attendee_0_national_identity_card" type="text" class="form-control form-control-sm national_identity_card_input" disabled>
                                <span class="invalid-feedback national_identity_card_input_error" role="alert" id="attendee_0_national_identity_cardError">
                                    <strong></strong>
                                </span>  
                        </td>
                        <td>
                                <input name="attendee[0][attendee_email]" id="attendee_0_attendee_email" type="text" class="form-control form-control-sm attendee_email_input" disabled>
                                <span class="invalid-feedback attendee_email_input_error" role="alert" id="attendee_0_attendee_emailError">
                                    <strong></strong>
                                </span>  
                        </td>
                        <td>
                                <input name="attendee[0][attendee_phone]" id="attendee_0_attendee_phone" type="text" class="form-control form-control-sm attendee_phone_input" disabled>
                                <span class="invalid-feedback attendee_phone_input_error" role="alert" id="attendee_0_attendee_phoneError">
                                    <strong></strong>
                                </span>  
                        </td>
                        <td>
                                <select name="attendee[0][id_event_program]" id="attendee_0_id_event_program" class="form-control form-control-sm select2 id_event_program_input" style="width: 100%;" disabled>
                                </select>
                                <span class="invalid-feedback id_event_program_input_error" role="alert" id="attendee_0_id_event_programError">
                                    <strong></strong>
                                </span> 
                        </td>
                        <td>
                                <input name="attendee[0][skor]" id="attendee_0_skor" type="text" class="form-control form-control-sm skor_input" disabled>
                                <span class="invalid-feedback skor_input_error" role="alert" id="attendee_0_skorError">
                                    <strong></strong>
                                </span>     
                        </td>
                        <td>
                            <center>
                                <button type="button" class="btn btn-xs btn-primary show_result" data-id="0" id="attendee_0_show"><i class="fa fa-arrow-right"></i></button>
                            </center>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_classroom"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="courseForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title className">Result</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="class_quiz-tab" data-toggle="pill" href="#class_quiz" role="tab" aria-controls="class_quiz" aria-selected="false">Quiz</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="custom-content-below-tabContent">
                        <div class="tab-pane fade show active" id="class_quiz" role="tabpanel" aria-labelledby="class_quiz-tab">
                        </div>
                    </div>
                </div>
            </form>
       
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
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_event_management = "";
    let global_id_event_program = "";
    let global_select_program = "";
    let global_select_employee = [];
    let global_select_course = "";
    let global_select_checklist = "";
    let global_id_event = 0;
    let global_id_attendees = 0;
    let global_id_user_session = 0;
    let x = [];
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

        $('#status').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });

        $('.datepicker').each(function(){
            $(this).datepicker({
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
            });
        });

		$('.summernote').summernote({
            height:300,
        });

        $('#event_table').DataTable({
            processing: true,
            responsive: true,
            // serverSide: true,
            ajax: {
                url: "{{ route('summary_event.index') }}",
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
                        return start.format("DD-MMM-YYYY")
                    } 
                },
                { data: 'end_date', name: 'end_date' , 
                    render: function ( data, type, row ) {  
                        let end = moment(row.end_date, 'YYYY-MM-DD');
                        return end.format("DD-MMM-YYYY")
                    } 
                },
                { data: 'type', name: 'type' },
                { data: 'notes', name: 'notes' },
                { data: 'organizer', name: 'organizer' },
                { data: 'responsible', name: 'responsible' },
                { data: 'venue', name: 'venue' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let detail = `<a href="javascript:;" onclick="classroom(${row.action})" class="join btn btn-success btn-sm" title="Show Detail" ><span class="fas fa-list fa-lg"></span></a>`;
                        let download = `&nbsp; <a href="javascript:;" class="btn btn-success btn-sm download_result" data-program="${row.id_event_management}" data-id="" title="Download"><i class="fa fa-arrow-down"></i></a>`;
                        return detail + download;
                    } 
                },
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
                urlForm = "{{ route('event_course.save') }}";
            } else {
                urlForm = "{{ route('event_course.update') }}";
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
        run_in_modal()
        global_id_event_management = "";
        $("#eventForm")[0].reset();
        $("#table_body_event").html("");
        $("#table_body_attendees").html("");
        $("#eventForm .modal-title").html("<span class='fas fa-plus'></span> Result Course Program");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#eventForm input").removeClass("is-invalid");
        $('#modal_form_event').modal('show');
    });

    $(document).on('click', 'a.page', function () {
        let data_page = $(this).attr("data-page");
        choose_page(data_page);
    });

    $(document).on('click', 'a.page_materi', function () {
        let data_page = $(this).attr("data-page");
        choose_page_materi(data_page);
    });

    function choose_page(number) {
        $(".question").hide();
        $(`#question_${number}`).show();
    }

    function choose_page_materi(number) {
        $(".materi").hide();
        $(`#materi_${number}`).show();
        $(`a.page_materi#page_materi_${number}`).removeClass('bg-gradient-secondary');
        $(`a.page_materi#page_materi_${number}`).addClass('bg-gradient-primary');
    }

    function new_attendees() {
        var content = jQuery('#sample_table_attendees tr'),
                size = global_id_attendees++,
                element = null,
                element = content.clone();
        element.attr('id','rec-attendees-'+size);
        element.find('.delete-record-attendees').attr('data-id', size);

        element.find('.booked_by_input').attr('id', 'attendee_' + size + '_booked_by');
        element.find('.booked_by_input').attr('data-id', size);
        element.find('.booked_by_input').addClass('booked_by_input');
        element.find('.booked_by_input').prepend('<option selected></option>').select2({
            placeholder: "Select Employee",
            allowClear: true,
            data: global_select_employee
        });

        element.find('.attendee_name_input').attr('id', 'attendee_' + size + '_attendee_name');

        element.find('.national_identity_card_input').attr('id', 'attendee_' + size + '_national_identity_card');

        element.find('.attendee_email_input').attr('id', 'attendee_' + size + '_attendee_email');

        element.find('.attendee_phone_input').attr('id', 'attendee_' + size + '_attendee_phone');

        element.find('.id_event_program_input').attr('id', 'attendee_' + size + '_id_event_program');
        element.find('.id_event_program_input').prepend('<option selected></option>').select2({
            placeholder: "Select Program",
            allowClear: true,
            data: global_select_program
        });

        element.find('.show_attendee').attr('id', 'attendee_' + size + '_skor');

        element.find('.show_attendee').attr('id', 'attendee_' + size + '_show');

        element.appendTo('#table_body_attendees');
        $('#table_body_attendees tr').each(function (index) {
            $(this).find('td:eq(0)').addClass('text-center');
            $(this).find('span.sn').html(index + 1);
        });
    }

    function get_course_by_program(idEventManagement) {
        $.ajax({
            url: "{{ url('learning_management/lms/summary_event/get_course') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json"},
            data: {id_event_management:idEventManagement},
            type: "GET",
            success: function (res) {
                $('#table_course_').html('');
                let myTable = ``;
                     
                myTable += `<thead><tr><th style="font-size:15px;">No</th>
                    <th style="font-size:15px;">Course</th>
                    <th style="font-size:15px;">Description</th>
                    <th style="font-size:15px;">Start Date</th>
                    <th style="font-size:15px;">End Date</th>
                    <th style="font-size:15px;">Action</th></tr></thead><tbody>`;
                $.each(res.data, function (i, item) {
                    myTable += `<tr>
                        <td style="font-size:15px;">${item.DT_RowIndex}</td>
                        <td style="font-size:15px;">${item.course_name}</td>
                        <td style="font-size:15px;">${item.description}</td>
                        <td style="font-size:15px;">${item.start_date}</td>
                        <td style="font-size:15px;">${item.end_date}</td>
                        <td style="font-size:15px;"><center><a href="javascript:;" class="btn btn-success btn-sm download_detail" data-program="${item.id_event_management}" data-id="${item.id_event_program}" title="Download"><i class="fa fa-arrow-down"></i></a></center></td>
                    </tr>`;
                });

                myTable += `</tbody>`;
                $('#table_course_').html(myTable);
            },
            error: function (err) {
                get_course_by_program(idEventManagement)
            }
        });

    }

    function classroom(id_event_management) {
        // run_in_modal()

        global_id_event_management = id_event_management;
        $("#eventForm")[0].reset();
        $("#table_body_event").html("");
        $("#table_body_attendees").html("");
        $("#eventForm .modal-title").html("<span class='fas fa-edit'></span> Result Course Program");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#eventForm input").removeClass("is-invalid");

        $.ajax({
            url: "{{ route('summary_event.get_event_result') }}",
            method: "GET",
            data: {id_event_management: id_event_management},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (res) {
                let dataEvent           = res.event;
                global_select_program   = res.program;
                global_id_event         = 0;                   
                global_id_attendees     = 0;

                $.each(dataEvent.attendee, function (i, item) {
                    new_attendees();
                });

                $('#id_event_management').val(dataEvent.id_event_management);
                $('#description').val(dataEvent.description);
                $('#start_date').val(dataEvent.start_date);
                $('#end_date').val(dataEvent.end_date);
                $('#id_timezone').val(dataEvent.id_timezone).trigger('change');
                if(dataEvent.limit_registration != null){
                    $('#limit_registration').attr('checked', true);
                } else {
                    $('#limit_registration').removeAttr('checked');
                }
                $('#end_date_registration').val(dataEvent.end_date_registration);
                $('#id_event_type').val(dataEvent.id_event_type).trigger('change');
                if(dataEvent.attachment != null){
                    let pathFile = '<?=url("learning_management/download_attachment")?>/'+dataEvent.attachment;
                    $('#attachmentFile').html(`<strong><a href="${pathFile}" target="_blank">${dataEvent.attachment}</a></strong>`);
                } else {
                    $('#attachmentFile').html('');
                }
                $('#organized_by').val(dataEvent.organized_by).trigger('change');
                $('#responsible_by').val(dataEvent.responsible_by).trigger('change');
                $('#venue').val(dataEvent.venue);
                $('#notes').val(dataEvent.notes);
                $('#status').val(dataEvent.status).trigger('change');

                get_course_by_program(dataEvent.id_event_management)

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
    }

    $(document).on('click', '.show_result', function (event) {
        let id_employee         = $(this).attr("id_employee");
        let id_survey_header    = $(this).attr("id_survey_header");
        $('#modal_classroom').modal('show');
        $('#class_quiz').html('');
        $('.className').html('Result');

        $.ajax({
            url: "{{ route('summary_event.get_course_result') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: {id_survey_header: id_survey_header, id_employee: id_employee},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                if(response.status == 'false'){
                    let no_access = `<div class="row">
                                        <div class="col-md-12">
                                            <center><h2>${response.message}</h2></center>
                                        </div>
                                    </div>`;
                    $('#class_quiz').html(no_access);
                } else {
                    $('.className').html(`${response.data.name} (${response.data.id_number}), Score = ${response.data.score}`);
                    let all_result = '';

                    if(response.data.result.length > 0){
                        $(response.data.result).each(function (i, val) {
                            all_result += `<div class="row">
                                                <div class="col-md-12">
                                                    <h5>${i+1}. ${val.question}</h5>
                                                    <div style="text-indent:2%;font-weight:bold;color:blue;">${val.answer}</div>
                                                </div>
                                            </div>`;
                            i++;
                        });
                    }
                    $('#class_quiz').html(all_result);
                }
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
    });

    $(document).on('change', '.booked_by_input', function (event) {
        let counter = $(this).attr('data-id');
        let value   = $(this).val();
        get_employee_detail(counter, value);
    });

    $(document).on('click', '.download_detail', function (event) {
		let urlDownload = "{{ url('learning_management/lms/summary_event/download_detail') }}";
        let id_event_program = $(this).attr('data-id');
        let idEventManagement = $(this).attr('data-program');
        let q_param = {
            'id_event_management' : idEventManagement,
            'id_event_program' : id_event_program,
        };
        let param = objectToQueryString(q_param);
        window.open(urlDownload+'?'+param, '_blank');

    });

    function run_in_modal() {
         

        get_user_by_session().then(function(value) {
            global_id_user_session = value[0].id;
            $('.get_employee').val(global_id_user_session).trigger('change');
        });

        get_course().then(function(value) {
            global_select_course = value;
        });

        get_checklist().then(function(value) {
            global_select_checklist = value;
        });
    }

    function on_close_modal() {
        // $('#content_table').DataTable().ajax.reload();
        global_id_event_management = '';
        window.location.reload();
    }

    function get_timezone() {
        $.getJSON('<?= url('learning_management/lms/event_course/get_timezone') ?>', function (data) {
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
        $.getJSON('<?= url('learning_management/lms/event_course/get_event_type') ?>', function (data) {
            $('#id_event_type').prepend('<option selected></option>').select2({
                placeholder: "Select Type",
                data: data,
                allowClear: true,
            });
        }).fail(function (data) { // Call failed
            get_event_type();
        }); 
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
        $.getJSON('<?= url('learning_management/lms/event_course/get_employee_detail') ?>'+'/'+ value, function (res) {
            $(`#attendee_${counter}_attendee_name`).val(res[0].name);
            $(`#attendee_${counter}_national_identity_card`).val(res[0].nik_employee);
            $(`#attendee_${counter}_attendee_email`).val(res[0].private_mail);
            $(`#attendee_${counter}_attendee_phone`).val(res[0].mobile_phone);
        }).fail(function (fail) { // Call failed
            get_employee_detail(counter, value);
        }); 
    }   

    const get_employee = async () => {
        let result;
        result = await $.ajax({
            url: "{{ route('event_course.get_employee') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            type: "GET",
            success: function (res) {
            },
            error: function (err) {
                get_employee()
            }
        });
        return result;
    }

    const get_course = async () => {
        let result;
        result = await $.ajax({
            url: "{{ route('event_course.get_course') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            type: "GET",
            success: function (res) {
            },
            error: function (err) {
                get_course()
            }
        });
        return result;
    }

    const get_checklist = async () => {
        let result;
        result = await $.ajax({
            url: "{{ route('event_course.get_checklist') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            type: "GET",
            success: function (res) {
            },
            error: function (err) {
                get_checklist()
            }
        });
        return result;
    }

    const get_user_by_session = async () => {
        let result;
        result = await $.ajax({
            url: "{{ route('event_course.get_user_by_session') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            type: "GET",
            success: function (res) {
            },
            error: function (err) {
                get_user_by_session()
            }
        });
        return result;
    }

    get_employee().then(function(res) {
        $.each(res, function (i, val) {
            name = `${val.name} (${val.nik_employee})`;
            global_select_employee.push({id:val.id_employee, text:name});
        }); 
        $('.get_employee, invited_by_input').select2({
            placeholder: "Select Employee",
            data: global_select_employee,
        });
    });

    function objectToQueryString(obj) {
        var str = [];
        for (var p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
        return str.join("&");
    }

    $(document).on('click', '.download_result', function () {
        let urlDownload = "{{ url('learning_management/lms/summary_event/download') }}";
        let id_event_program = $(this).attr('data-id');
        let idEventManagement = $(this).attr('data-program');
        let q_param = {
            'id_event_management' : idEventManagement,
            'id_event_program' : id_event_program,
        };
        let param = objectToQueryString(q_param);
        window.open(urlDownload+'?'+param, '_blank');
    });

    $(document).on('click', '#downloadMultiple', function() {
        var oTable = $('#event_table').dataTable();
        var rowcollection =  oTable.$(".dt-checkboxes:checked", {"page": "all"});
        let idEventManagements = [];
        rowcollection.each(function(index,elem){
            var checkbox_value = $(elem).val();
            idEventManagements.push($(elem).parent().parent().parent().find('.download_result').attr('data-program'));
        });
        if(idEventManagements.length < 1) {
            return alert('Select at least 1 program!');
        }
        idEventManagements = idEventManagements.join(',');
        window.open("{{ route('summary_event.download_multiple') }}?id_event_management="+idEventManagements, '_blank');
            
    });

    $(document).on('click', '#downloadMultiple', function() {

    });

</script>
@endsection