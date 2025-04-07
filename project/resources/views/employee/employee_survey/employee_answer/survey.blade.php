<style>
    .sticky-title {
        position: sticky;
        position: -webkit-sticky;
        top: 0;
        background-color: #fff;
        z-index: 1800;
    }
</style>
<div class="modal fade" id="modal_classroom"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="courseForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header sticky-title">
                    <h5 class="modal-title className">Class</h5>
                    <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button> -->
                </div>
                <div class="modal-body">
                    <div class="" id="class_quiz" aria-labelledby="class_quiz-tab"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="skip_survey">Skip</button>&nbsp;
                    <button type="submit" class="btn btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>
                </div>
            </form>
       
        </div>
    </div>
</div>

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
    .kotak_sequence {
        border: 2px solid #000;
        align-items: center;
    }
    .kotak_sequence span {
        background-color: #c6d8eb!important;
        display: flex;
        display: -ms-flexbox;
/*        position: relative;*/
        width: 35px;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
        align-items: center;
    }
    .kotak_sequence_selected span {
        background-color: #164473!important;
        position: relative;
        width: 35px;
        color: #fff;
        text-align: center;
        vertical-align: middle;
    }
	.btn-outline-success {
		color: #393a39;
		border-color: #28a745;
	}
</style>
@stop

<script type="text/javascript">
    let sequenceAnswerAll = {};
    let sequenceAnswerByQuestion = {};

    $('.summernote').summernote({
        height:300,
    });

    $('#courseForm').submit(function (e) {
        e.preventDefault();
        let formData = new FormData($('#courseForm')[0]);
        $('#save_button').attr('disabled');
        
        $.ajax({
            type: 'POST',
            headers: { Accept: "application/json", 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            contentType:false,
            processData:false,
            cache: false,
            url: "{{ route('employee_survey_answer.save') }}",
            data: formData,
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                $('#loader').addClass('hidden')
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
                        dangerMode: true,
                        content: {
                            element: "div",
                            attributes: {
                                innerText: response.message,
                                className: "swal-red",
                            },
                        },
                    });
                }
            },
            error: function (response) {
                $('#loader').addClass('hidden')
                swal({
                    title: 'Please refresh page',
                }).then(function(){ 
                    location.reload();
                });
            }
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

    $(document).on('click', 'a.page', function () {
        let data_page = $(this).attr("data-page");
        choose_page(data_page);
    });

    $(document).on('click', 'a.page_materi', function () {
        let data_page = $(this).attr("data-page");
        choose_page_materi(data_page);
    });

    $(document).on('click', '.this_sequence_answer', function () {
        let id_question = $(this).attr("id_question");
        let id_answer = $(this).attr("id_answer");
        removeOrAddSelectedAnswer(id_question, id_answer).then(ok => {
            reorderSequenceAnswer(id_question);
        });
        
        return false;
    });

    $(document).on('click', '.choose_answer', function () {
        let idQuestion = $(this).attr('data-question');
        let idAnswer = $(this).attr('data-answer');
        let type = $(this).children().attr('type');

        if(type=='radio'){
            $(`label.answer_label[id_question="${idQuestion}"]`).removeClass('active');
        }
        if($(this).parent().hasClass('active')){
            $(this).parent().removeClass('active');
            $(`input[data-question='${idQuestion}'][data-answer='${idAnswer}']`).prop('checked', false);
        } else {
            $(this).parent().addClass('active');
            $(`input[data-question='${idQuestion}'][data-answer='${idAnswer}']`).prop('checked', true);
        }
    });

    $(document).on('change', '.attachment', function () {
        let thisId = $(this).attr('id');
        let file = $(`#${thisId}`)[0].files[0]; 
        $(`label.${thisId}`).html(`<i>${file.name}</i>`);
    });

    const reorderSequenceAnswer = (idQuestion) => {
        $(`.sequence_answer[id_question='${idQuestion}']`).html('&nbsp;');
        let arrSequence = sequenceAnswerAll[idQuestion];

        $(arrSequence).each(function (index, idAnswer) {
            $(`span.sequence_answer[id_question='${idQuestion}'][id_answer='${idAnswer}']`).html(index+1);
        });
        $(`input[name='sequence_answer_${idQuestion}']`).val('');
        $(`input[name='sequence_answer_${idQuestion}']`).val(arrSequence.join(","));
    }

    const removeOrAddSelectedAnswer = async (idQuestion, idAnswer) => {
        let checkSelected = sequenceAnswerAll[idQuestion].indexOf(idAnswer);
        if (checkSelected > -1){
            while ($.inArray(idAnswer, sequenceAnswerAll[idQuestion]) > -1) {
                sequenceAnswerAll[idQuestion].splice( $.inArray(idAnswer, sequenceAnswerAll[idQuestion]), 1 );
            }
            $(`label.answer_label[id_question='${idQuestion}'][id_answer='${idAnswer}']`).removeClass('active');
            $(`.sequence_answer[id_question='${idQuestion}'][id_answer='${idAnswer}']`).css({'background-color':'#e9ecef'})
            $(`input[data-question='${idQuestion}'][data-answer='${idAnswer}']`).prop('checked', false);
        } else {
            sequenceAnswerAll[idQuestion].push(idAnswer);
            $(`label.answer_label[id_question='${idQuestion}'][id_answer='${idAnswer}']`).addClass('active');
            $(`.sequence_answer[id_question='${idQuestion}'][id_answer='${idAnswer}']`).css({'background-color':'rgb(127 196 255)!important'})
            $(`input[data-question='${idQuestion}'][data-answer='${idAnswer}']`).prop('checked', true);
        }
    }

    const show = async (id_header) => {
        let result_show;
        try {
            result_show = await $.ajax({
                type: 'GET',
                url: '{{ url("employee/employee/employee_survey/") }}/'+id_header,
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    $('#loader').addClass('hidden')
                    $('#class_materi').html('');
                    $('#class_quiz').html('');

                    if(response.status == 'false'){

                        // let no_access = `<div class="row">
                        //                     <div class="col-md-12">
                        //                         <center><h2>${response.message}</h2></center>
                        //                     </div>
                        //                 </div>`;
                        // $('.className').html('Survey : ');
                        // $('#class_materi').html(no_access);
                        // $('#class_quiz').html(no_access);
                        // $('#save_button').hide(); 
                    } else {
                        $('#modal_classroom').modal('show');
                        $('.className').html(`${response.data.class}`);
                        $('#class_materi').html(show_materi(response.data.materi));
                        $('#class_quiz').html(show_notes(response.data.notes) +'<br>'+ show_quiz(response.data.quiz, response.data.quiz_status, response.data.category));
                        let thisEndDate = moment(response.data.end_date, 'YYYY-MM-DD').valueOf();
                        let thisToday   = moment("<?= date('Y-m-d') ?>", 'YYYY-MM-DD').valueOf();
                        
                        if(response.data.materi.length > 0){
                            $('#save_button').hide(); 
                            choose_page_materi(1)
                        }
                        if(response.data.quiz.length > 0){
                            if(response.data.quiz_status != 'done'){
                                //jika textarea summernote pake script yg summernote
                                // $('.summernote').summernote({
                                //     height:300,
                                //     callbacks: {
                                //         onKeyup: function(e) {
                                //             let answer = $('.summernote').val().replace(/<\/?[^>]+(>|$)/g, "");
                                //             let data_page = $(this).attr("data-page");
                                //             let id_question = $(this).attr("data-question");
                                //             choose_answer_essay(data_page, id_question, answer);
                                //         }
                                //     }
                                // });
                                $(document).on('keyup', '.answer', function () {
                                    let data_page = $(this).attr("data-page");
                                    let id_question = $(this).attr("data-question");
                                    choose_answer_essay(data_page, id_question, answer);
                                });
                                $('#save_button').show(); 
                                choose_page(1)
                            } else {
                                $('#save_button').hide(); 
                            }
                        }
                    }
                },
                error: function (err) {
                    $('#loader').addClass('hidden')
                }
            });
            return result_show;
        } catch (error) {
            show(id_header);
        }
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
        // $(".question").hide();
        // $(`#question_${number}`).show();
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

    function show_notes(data='') {
        let notes = '';
        if(data!=null){
            notes+= `${data}`;
        }
        return notes;
    }

    function show_quiz(data, status, category) {

        let quiz                = '';
        let content_quiz        = '';
        let i_quiz              = 1;
        let page                = '';
        let result              = '';
        let id_survey_header    = '';
        let category_question   = [];

        if(data.length > 0){
            if(status != 'done'){
                for (let item of data) {
                    id_survey_header    = item.id_survey_header;
                    let id_survey_history = item.id_survey_history;
                    let answer_type     = item.type;
                    let id_question     = item.id_survey_question;
                    let all_answer      = '';
                    let classThisAnswer      = '';
                    let divSequenceAnswer = '';
                    let elementInputSequenceAnswer = `<input type="hidden" name="sequence_answer_${id_question}" value=""/><input type="hidden" name="sequence_answer_count_${id_question}" value="${item.answer.length}"/>`;

                    if(answer_type == 'Essay'){
                        all_answer += `<div class="col-md-11 mb-2">
                                            <textarea rows="2" class="answer form-control form-control-sm" name="answer_${id_question}[]" data-page="${i_quiz}" data-question="${id_question}" ></textarea>
                                        </div>`;
                    } else if(answer_type == 'Upload_Files'){
                        all_answer += `<div class="col-md-11 mb-2">
                                <div class="form-group row">
                                    <div class="custom-file">
                                        <input type="file" name="answer_${id_question}[]" data-page="${i_quiz}" data-question="${id_question}" class="custom-file-input attachment" id="attachment_${id_question}">
                                        <span class="invalid-feedback" role="alert" id="attachment_${id_question}Error"></span>
                                        <label class="custom-file-label attachment_${id_question}"><i>File (pdf/jpg)</i></label>
                                    </div>
                                </div>
                            </div>`;
                    } else {
                        if(answer_type == 'Sequence_Answer'){
                            sequenceAnswerAll[id_question] = [];
                        }

                        for (let item_answer of item.answer) {
                            let id_answer = '';
                            let text_answer = `<span class="text-left"> &nbsp; ${item_answer.answer}</span>`;
                            let nameAnswer = '';
                            let reff       = '';
                            
                            if(item_answer.image == null){
                                answer = text_answer;
                            } else {
                                // Penempatan Image Answer Jika ada
                                answer = `<center><img class="form-control" src="${item_answer.image}" style="max-width:230px;max-height:95px;width: auto;height: auto;"></center><br>${text_answer}`;
                            }

                            if(answer_type == 'Sequence_Answer'){
                                id_answer = item_answer.answer_code;
                            } else {
                                id_answer = item_answer.id_survey_answer;
                            }

                            if(answer_type == 'Multiple_Answer'){
                                type        = 'checkbox';
                                nameAnswer  = `name="answer_${id_question}[]"`;
                                reff        = `<span class="input-group-text font-weight-bold">${item_answer.reference}</span>`;

                            } else if(answer_type == 'Sequence_Answer'){
                                classThisAnswer = 'this_sequence_answer';
                                type        = 'checkbox';
                                reff        = `<span class="input-group-text font-weight-bold sequence_answer ${classThisAnswer}" id_question="${id_question}" id_answer="${id_answer}"></span>`;
                            } else {
                                type        = 'radio';
                                nameAnswer  = `name="answer_${id_question}[]"`;
                                reff        = `<span class="input-group-text font-weight-bold">${item_answer.reference}</span>`;
                            }

                            all_answer += `<div class="col-md-12 mb-2">
                                    <div class="input-group">
                                        <div class="input-group-prepend ${classThisAnswer}" id_answer="${id_answer}" id_question="${id_question}">
                                            ${reff}
                                            <label class="btn btn-block btn-outline-success answer_label" id_answer="${id_answer}" id_question="${id_question}">
                                                <div class="icheck-success choose_answer" data-question="${id_question}" data-answer="${id_answer}">
                                                    <input type="${type}" ${nameAnswer} autocomplete="off" class="answer"  data-page="${i_quiz}" data-question="${id_question}" data-answer="${id_answer}" value="${id_answer}"> 
                                                    <label> ${answer} </label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>`;
                        }
                    }

                    if(item.category != null && category_question.indexOf(item.category) < 0){
                        // Penempatan Nama Kategori
                        if(category_question.length > 0){ 
                            quiz += `<br>`; 
                        }
                        quiz += `<h3 style="font-size:22px;color:red;">${item.category}.</h3><hr style="border:1px solid rgb(224 11 11);" size="5">`;
                        category_question.push(item.category);
                    }

                    quiz += `<h5 style="font-size:18px;" class="text-primary"><span style="color:black;">${i_quiz}.</span> ${item.question}</h5>`;
					if(item.img_question != null){
						 quiz += `<img src="../../project/storage/app/public/upload/survey/${item.id_survey_header}/${item.img_question}" style="max-width:250px;padding: 0 0 20px 20px;"/>`;
					}					
                    quiz += `<div class="row">
                                <div class="col-md-12" style="margin-left:10px;">
                                    <div class="btn-group-toggle">
                                        <div class="row">
                                            ${all_answer}
                                            ${elementInputSequenceAnswer}
                                            <input hidden value="${id_question}" name="id_question[]" >
                                            <input hidden value="${answer_type}" name="answer_type_${id_question}" >
                                            <input hidden value="${id_survey_header}" name="id_survey_header" >
                                            <input hidden value="${id_survey_history}" name="id_survey_history" >
                                        </div>
                                    </div>
                                </div>
                            </div><br>`;
                    // page += `<a class="btn btn-sm bg-gradient-secondary mb-1 mr-1 text-white font-weight-bold page" id="page_${i_quiz}" data-page="${i_quiz}" >${i_quiz}</a>`;

                    i_quiz++;
                } 
            } else {
                quiz += `<div class="row">
                            <div class="col-md-12"><br>
                                <div class="alert alert-success alert-dismissible">
                                    <h5><i class="icon fas fa-check"></i> Success!</h5>
                                    Survey telah dijawab
                                </div>
                            </div>
                        </div>`;
                page += '';
            }
        } else {
            quiz += `<div class="row">
                            <div class="col-md-12">
                                Tidak ada Survey pada program ini
                            </div>
                        </div>`;
            page += '';
        }
        // result = `<div class="question"> ${quiz} </div> <hr>
        //         <div class="row justify-content-center">
        //         ${page}
        //         </div>`;
        result = `<div class="question"> ${quiz} </div>`;

        return result;
    }

</script>
