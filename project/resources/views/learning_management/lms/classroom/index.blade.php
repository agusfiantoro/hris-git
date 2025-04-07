@extends('adminlte::page')
@section('title', 'Individual Development Program')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Individual Development Program</h5>
            </div>
       
			<div class="card-body" style="overflow-y: scroll;overflow-x: scroll;">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="event_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>			
                        <th></th>
						<th></th>
						<th>No</th>
						<th>Month</th>
						<th>Schedule Date</th>
						<!-- <th>Responsible By</th> -->
						<th>Program</th>
						<th>Course</th>
                        <th>Notes</th>
                        <th>Venue</th>
                        <th>Completion Date</th>
                        <th>Status</th>
                        {{-- <th>Score</th> --}}
                        <th>Result</th>
						<th data-priority="2" style="text-align:center;">Action</th>
					  </tr>
					 </thead>
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
                    <h5 class="modal-title className">Class</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="class_materi-tab" data-toggle="pill" href="#class_materi" role="tab" aria-controls="class_materi" aria-selected="true">Materi</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="class_quiz-tab" data-toggle="pill" href="#class_quiz" role="tab" aria-controls="class_quiz" aria-selected="false">Quiz</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="custom-content-below-tabContent">
                        <div class="tab-pane fade show active" id="class_materi" role="tabpanel" aria-labelledby="class_materi-tab">
                        </div>

                        <div class="tab-pane fade" id="class_quiz" role="tabpanel" aria-labelledby="class_quiz-tab">
                        </div>
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
.answer_label {
    margin-bottom: 0px;
}
li.select2-results__option strong.select2-results__group:hover {
  background-color: #6a6a6a;
  color:#fff;
  cursor: pointer;
}
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];

    $(function () {	
		$('.summernote').summernote({
            height:300,
        });

        $('#event_table').DataTable({
            processing: true,
            // serverSide: true,
            ajax: {
                url: "{{ route('classroom.index') }}",
                error: function (jqXHR, textStatus, errorThrown) {
                        $('#event_table').DataTable().ajax.reload();
                    }
              },
            // order: [[10, 'asc']],
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
                {   // Checkbox select column
                    data: 'id_event_program',
                    defaultContent: '',
                    orderable: false
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                { data: 'start_date', name: 'start_date', 
                    render: function ( data, type, row ) {  
                        if(row.epstp_schedule) return moment(row.epstp_schedule).format("MMM-YYYY");
                        if(row.start_date_attendee != null){
                            start = moment(row.start_date_attendee, 'YYYY-MM-DD');
                        } else {
                            start = moment(row.start_date, 'YYYY-MM-DD');
                        }
                        return moment(start, 'YYYY-mm-dd HH:mm').format("MMM-YYYY");
                    }
                },
                { data: 'end_date', name: 'end_date' , 
                    render: function ( data, type, row ) {  
                        if(row.epstp_schedule) return moment(row.epstp_schedule).format("DD-MMM-YYYY");
                        if(row.start_date_attendee != null){
                            let startAttendee = moment(row.start_date_attendee);
                            let startCourse = moment(row.start_date);
                            if(startAttendee > startCourse) {
                                start = startAttendee;
                            } else {
                                start = startCourse;
                            }
                        } else {
                            start = moment(row.start_date, 'YYYY-MM-DD');
                        }
                        if(row.end_date_attendee != null){
                            end = moment(row.end_date_attendee, 'YYYY-MM-DD');
                            let endAttendee = moment(row.end_date_attendee);
                            let endCourse = moment(row.end_date);
                            if(endAttendee >= endCourse) {
                                end = endCourse;
                            } else {
                                end = endAttendee;
                            }
                        } else {
                            end = moment(row.end_date, 'YYYY-MM-DD');
                        }
                        // let start_time = moment(new Date(row.start_date), 'HH:mm').format("HH:mm");
                        // let end_time = moment(new Date(row.end_date), 'HH:mm').format("HH:mm");
                        let start_time = '';
                        let end_time = '';
                        let schedule_date = '';

                        if(start.format("MM-DD") == end.format("MM-DD")){
                            schedule_date = `${start.format("DD-MMM-YYYY")} ~ ${start_time} - ${end_time}`;
                        } else {
                            schedule_date = `${start.format("DD-MMM-YYYY")} ${start_time} ~ ${end.format("DD-MMM-YYYY")} ${end_time}`;
                        }
                        return schedule_date;
                    }
                },
                // { data: 'name', name: 'name' },
                { data: 'event_name', name: 'event_name' },
                { data: 'course_name', name: 'course_name' },
                { data: 'notes', name: 'notes' },
                { data: 'venue', name: 'venue' },
                { data: 'answered_date', name: 'answered_date' },
                { data: 'status', name: 'status', 
                    render: function ( data, type, row ) {  
                        if(row.status_join == 'available'){
                            return `<center><span class="btn btn-primary btn-xs">Available</span></center>`;
                        } else if(row.status_join == 'done'){
                            return `<center><span class="btn btn-success btn-xs">Done</span></center>`;
                        } else if(row.status_join == 'expired'){
                            return `<center><span class="btn btn-secondary btn-xs">Missed</span></center>`;
                        }
                    } 
                },
                // { data: 'score', name: 'score' },
                { data: 'result', name: 'result', render: function ( data, type, row ) {  
                    if(row.result == 'Pass') {
                        return `<center><span class="badge badge-success">Pass</span></center>`;
                    } else if(row.result == 'Fail') {
                        return `<center><span class="badge badge-danger">Fail</span></center>`;
                    } else if(row.result == 'No Data') {
                        return '';
                    } else {
                        return `<center><span class="badge badge-secondary">${row.result}</span></center>`;
                    }
                }},
                { data: 'action', name: 'action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let url_join = '{{ url("learning_management/lms/class_room/class") }}?c='+row.action;
                        if(row.status_join == 'available'){
                            return `<center><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm" title="Start" >Start</a></center>`;
                        } else if(row.status_join == 'done'){
                            return `<center><a href="${url_join}" target="_blank" class="join btn btn-success btn-sm" title="Show" >Show</a></center>`;
                        } else if(row.status_join == 'expired'){
                            return ``;
                        }
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
        
        $('#courseForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#courseForm')[0]);

            $.ajax({
                type: 'POST',
                headers: { Accept: "application/json", },
                contentType:false,
                processData:false,
                cache: false,
                url: "{{ route('classroom.save') }}",
                data: formData,
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    if (response.status == 'true') {
                        $('#modal_classroom').modal('hide');
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
                            text: response.message,
                            dangerMode: true,
                        });
                    }
                },
                complete: function(){
                    $('#loader').addClass('hidden')
                },
                error: function (response) {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong! [Unknown Error]',
                        dangerMode: true,
                    });
                }
            });
        });

    });

    $(document).on('click', 'a.join', function () {
        $('#modal_class').modal('show');
    });

    $(document).on('click', '.answer', function () {
        let data_page = $(this).attr("data-page");
        let id_question = $(this).attr("data-question");
        choose_answer(data_page, id_question);
    });

    $(document).on('keyup', 'textarea.answer', function () {
        
    });

    $(document).on('click', 'a.page', function () {
        let data_page = $(this).attr("data-page");
        choose_page(data_page);
    });

    $(document).on('click', 'a.page_materi', function () {
        let data_page = $(this).attr("data-page");
        choose_page_materi(data_page);
    });

    $(document).on('click', '.delete', function (event) {
    	id_content_learning = $(this).attr('id');
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
                    url: "{{ route('content_attachment.destroy') }}",
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    method: "POST",
                    data: {id_content_learning: id_content_learning},
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

    $(document).on('click', '.value_next, .value_before', function () {
        let value = $(this).attr("value");
        showContent(value)
    });
    
    function classroom(id_class) {
        $('#modal_classroom').modal('show');
        $.ajax({
            type: 'GET',
            url: '{{ url("learning_management/lms/class_room/join") }}/'+id_class,
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                $('#class_materi').html('');
                $('#class_quiz').html('');

                if(response.status == 'false'){
                    let no_access = `<div class="row">
                                        <div class="col-md-12">
                                            <center><h2>${response.message}</h2></center>
                                        </div>
                                    </div>`;
                    $('.className').html('Course : ');
                    $('#class_materi').html(no_access);
                    $('#class_quiz').html(no_access);
                    $('#save_button').hide(); 
                } else {
                    $('.className').html(`Course : ${response.data.class}`);
                    $('#class_materi').html(show_materi(response.data.materi));
                    $('#class_quiz').html(show_quiz(response.data.quiz, response.data.quiz_status));

                    if(response.data.materi.length > 0){
                        $('#save_button').hide(); 
                        choose_page_materi(1)
                    }
                    if(response.data.quiz.length > 0){
                        if(response.data.quiz_status != 'done'){
                            $('.summernote').summernote({
                                height:300,
                                callbacks: {
                                    onKeyup: function(e) {
                                        let answer = $('.summernote').val().replace(/<\/?[^>]+(>|$)/g, "");
                                        let data_page = $(this).attr("data-page");
                                        let id_question = $(this).attr("data-question");
                                        choose_answer_essay(data_page, id_question, answer);
                                    }
                                }
                            });
                            $('#save_button').show(); 
                            choose_page(1)
                        } else {
                            $('#save_button').hide(); 
                        }
                    }
                }
            },
            complete: function(){
                $('#loader').addClass('hidden')
            },
            error: function (response) {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong! [Unknown Error]',
                    dangerMode: true,
                });
            }
        });
    }

    function choose_answer(page, id_question) {
        $('a.page').each(function (index) {
            if($(this).attr('data-page') == page){
                let checked = $(`input[name="answer_${id_question}[]"]`).filter(':checked');
                if(checked.length > 0){
                    $(`a.page#page_${page}`).removeClass('bg-gradient-secondary');
                    $(`a.page#page_${page}`).addClass('bg-gradient-primary');
                } else {
                    $(`a.page#page_${page}`).removeClass('bg-gradient-primary');
                    $(`a.page#page_${page}`).addClass('bg-gradient-secondary');
                }
            }
        });
    }

    function choose_answer_essay(page, id_question, answer) {
        $('a.page').each(function (index) {
            if($(this).attr('data-page') == page){
                if(answer != ''){
                    $(`a.page#page_${page}`).removeClass('bg-gradient-secondary');
                    $(`a.page#page_${page}`).addClass('bg-gradient-primary');
                } else {
                    $(`a.page#page_${page}`).removeClass('bg-gradient-primary');
                    $(`a.page#page_${page}`).addClass('bg-gradient-secondary');
                }
            }
        });
    }

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

    function show_materi(data) {
        let materi          = '';
        let content_materi  = '';
        let i_materi        = 1;
        let page            = '';
        let result          = '';

        if(data.length > 0){
            for (let item of data) {
                let description = item.description==null ? '-' : item.description;
                let link        = item.link==null ? '-' : item.link;

                if(item.type == 'Description' || item.type == 'Presentation'){
                    content_materi = `<h5>${description}</h5><br>`;
                } else {
                    content_materi = `<center><iframe width="620" height="250" src="${link}"></iframe></center><br>`;
                }

                materi += `<div class="materi" id="materi_${i_materi}">
                                <h4 class="text-center text-primary">${item.course_name}</h4>
                                <h5>${i_materi}.</h5>
                                <div class="row">
                                    <div class="col-md-12">
                                        ${content_materi}
                                    </div>
                                </div>
                            </div>`;

                page += `<a class="btn btn-sm bg-gradient-secondary mb-1 mr-1 text-white font-weight-bold page_materi" id="page_materi_${i_materi}" data-page="${i_materi}" >${i_materi}</a>`;

                i_materi++;
            }
        } else {
            materi += `<div class="row">
                            <div class="col-md-12">
                                Tidak ada materi pada program ini
                            </div>
                        </div>`;
            page += '';
        }

        result = `${materi} <hr>
                <div class="row justify-content-center">
                ${page}
                </div>`;

        return result;
    }

    function show_quiz(data, status) {

        let quiz                = '';
        let content_quiz        = '';
        let i_quiz              = 1;
        let page                = '';
        let result              = '';
        let id_survey_header    = '';

        if(data.length > 0){
            if(status != 'done'){
                for (let item of data) {
                    id_survey_header    = item.id_survey_header;
                    let answer_type     = item.type;
                    let id_question     = item.id_survey_question;
                    let all_answer      = '';

                    if(answer_type != 'Essay'){
                        for (let item_answer of item.answer) {
                            let id_answer   = item_answer.id_survey_answer;
                            let reff        = item_answer.reference;

                            if(item_answer.image == null){
                                answer = item_answer.answer;
                            } else {
                                answer = `<center><img src="${item_answer.image}" style="height:200px;" ></center><br>${item_answer.answer}`;
                            }

                            if(answer_type == 'Multiple_Answer' ){
                                type = 'checkbox';
                            } else {
                                type = 'radio';
                            }

                            all_answer += `<div class="col-md-12 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text font-weight-bold">${reff}</span>
                                                        <label class="btn btn-block btn-outline-success answer_label">
                                                            <input type="${type}" name="answer_${id_question}[]" autocomplete="off" class="answer" data-page="${i_quiz}" data-question="${id_question}" value="${id_answer}" > ${answer}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>`;
                        }
                    } else {
                        all_answer += `<div class="col-md-12 mb-2">
                                            <textarea class="summernote answer form-control form-control-sm" name="answer_${id_question}[]" data-page="${i_quiz}" data-question="${id_question}" ></textarea>
                                        </div>`;
                    }
                    quiz += `<div class="question" id="question_${i_quiz}">
                                <h4>${i_quiz}.</h4>
                                <h5>${item.question}</h5><br>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <div class="row">
                                                ${all_answer}
                                                <input hidden value="${id_question}" name="id_question[]" >
                                                <input hidden value="${answer_type}" name="answer_type_${id_question}" >
                                                <input hidden value="${id_survey_header}" name="id_survey_header" >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>`;

                    page += `<a class="btn btn-sm bg-gradient-secondary mb-1 mr-1 text-white font-weight-bold page" id="page_${i_quiz}" data-page="${i_quiz}" >${i_quiz}</a>`;

                    i_quiz++;
                } 
            } else {
                quiz += `<div class="row">
                            <div class="col-md-12"><br>
                                <div class="alert alert-success alert-dismissible">
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                                    Quiz telah dijawab
                                </div>
                            </div>
                        </div>`;
                page += '';
            }
        } else {
            quiz += `<div class="row">
                            <div class="col-md-12">
                                Tidak ada quiz pada program ini
                            </div>
                        </div>`;
            page += '';
        }

        result = `${quiz} <hr>
                <div class="row justify-content-center">
                ${page}
                </div>`;

        return result;
    }

</script>
@endsection