@extends('adminlte::page')
@section('title', 'Individual Development Program')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/shaka-player@4.11.11/dist/controls.min.css" rel="stylesheet">
<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <div class="card-title">
                    <h5 class="modal-title className">Class :</h5>
                    <h5 class="modal-title courseName">Course :</h5>
                </div>
                <div class="card-tools">
                    
                </div>
            </div>
       
			<div class="card-body courseBody">
                <div class="row">
                    @if($message != '')
                    <div class="col-md-12 btn btn-rounded bg bg-danger">
                        {{ $message }}
                    </div>

                    @else

                    <div class="col-md-12 mb-3 text-center bg-gradient-danger callout callout-danger rounded">
                        <span class="font-weight-bold">The previous page cannot be viewed anymore, please read it carefully</span><br>
                        <span class="font-weight-bold"><i>(Harap dibaca dengan teliti, halaman sebelumnya tidak dapat dilihat kembali)</i></span>
                    </div>

                    <div class="col-md-12">
                        <form method="post" id="courseForm" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" name="id_event_management" id="id_event_management">
                            <input type="hidden" name="id_event_program" id="id_event_program">
                            <div class="row">
                                <div class="col-md-12 content_learning"></div>
                            </div>
                            <div class="page_content_learning d-flex justify-content-end"></div>
                        </form>
                    </div>
                    @endif
                </div>
			</div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12 content_page text-center">
                </div>
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
    .mypage{cursor: not-allowed;}
</style>
@stop

@section('scripts')
<script src="https://www.youtube.com/iframe_api"></script>
<script src="https://cdn.jsdelivr.net/npm/shaka-player@4.11.11/dist/shaka-player.compiled.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/shaka-player@4.11.11/dist/shaka-player.ui.js"></script>
<script type="text/javascript">
    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];
    let classId = '';
    let allow = '{{ $message }}';
    let sequence_existing = 1;
    let next    = 1;
    let before  = 1;
    let sequenceAnswerAll = {};
    let sequenceAnswerByQuestion = {};

    let canInitPlayer = false;
    let manifestUri;

    async function initPlayer() {
        // Create a Player instance.
        const player = new shaka.Player();
        const videoElement = document.getElementById('shakaVideoPlayer');
        const contElement = document.getElementById('shakaContainer')
        const ui = new shaka.ui.Overlay(player, contElement, videoElement);
        await player.attach(videoElement);
        
        const config = {
            addSeekBar: false,
            castReceiverAppId: 'BBED8D28',
            controlPanelElements: ['play_pause', 'time_and_duration', 'volume', 'spacer', 'loop', 'airplay', 'rewind', 'fullscreen'],
            rewindRates: [-1, -2, -4, -8],
            seekBarColors: {
                base: 'rgba(255, 255, 255, 0.3)',
                buffered: 'rgba(255, 155, 155, 0.54)',
                played: 'rgb(255, 10, 10)',
            }
        };
        ui.configure(config);
        // const player = new shaka.Player(video);

        // Attach player to the window to make it easy to access in the JS console.
        window.player = player;

        // Listen for error events.
        player.addEventListener('error', onErrorEvent);
        // player.addEventListener('ended', onVideoFinished);

        // Try to load a manifest.
        // This is an asynchronous process.
        try {
            await player.load(manifestUri);
            // This runs if the asynchronous load is successful.
            // console.log('The video has now been loaded!');
        } catch (e) {
            // onError is executed if the asynchronous load fails.
            onError(e);
        }
    }

    function initShaka() {
        // Install built-in polyfills to patch browser incompatibilities.
        shaka.polyfill.installAll();

        // Check to see if the browser supports the basic APIs Shaka needs.
        if (shaka.Player.isBrowserSupported()) {
            // Everything looks good!
            // initPlayer();
            if(!canInitPlayer) {
                canInitPlayer = true;
            } else {
                initPlayer();
            }
        } else {
            // This browser does not have the minimum set of APIs we need.
            swal({
                icon: 'error',
                dangerMode: true,
                title: 'Video Player Error',
                text: 'Browser not supported!'
            });
        }
    }

    function onErrorEvent(event) {
    // Extract the shaka.util.Error object from the event.
        onError(event.detail);
    }

    function onError(error) {
    // Log the error.
        console.error('Error code', error.code, 'object', error);
    }

    document.addEventListener('DOMContentLoaded', initShaka);


    $(function () {	
        if(allow == ''){
            classId = "{{ Request::get('c') }}";
            classroom(classId);
        }

		$('.summernote').summernote({
            height:300,
        });

        $('#courseForm').submit(function (e) {
            e.preventDefault();
            
        });

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

    $(document).on('click', '.this_sequence_answer', function () {
        let id_question = $(this).attr("id_question");
        let id_answer = $(this).attr("id_answer");
        removeOrAddSelectedAnswer(id_question, id_answer).then(ok => {
            reorderSequenceAnswer(id_question);
        });
        
        return false;
    });

    $(document).on('click', '.value_next, .value_before', function () {
        let value = $(this).attr("value");
        let from = $(this).attr("from");
        let url = $(this).attr("url");

        if($(this).hasClass('value_next') && localStorage.lms_borwita) {
            let contents = JSON.parse(localStorage.lms_borwita);
            contents.forEach((content) => {
                if(content.sequence == Number.parseInt(from)) {
                    saveExistingCourse(content.id_course_detail);
                }
            })
            
        }

        if($(this).hasClass('show_confirm_popup')) {
            swal({
                icon: 'warning',
                title: 'Confirm Submission',
                text: 'Apakah anda yakin ingin menyelesaikan quiz ini?',
                buttons: ['Batal', 'Ya, Selesaikan Quiz']
            }).then((confirm) => {
                if(confirm) {
                    if(url != '' && url != undefined){
                        localStorage.removeItem("lms_borwita_existing_page");
                        window.location.href = url;
                    } else {
                        showContent(value, null, from)
                    }
                }
            })
        } else {
            if(url != '' && url != undefined){
                localStorage.removeItem("lms_borwita_existing_page");
                window.location.href = url;
            } else {
                showContent(value, null, from)
            }
        }
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

    const submitQuiz = (dataShow='', btnNext='', btnBefore='', fromPage='', toPage='', content='') => {
        let formData = new FormData($('#courseForm')[0]);
        
        result = $.ajax({
            type: 'POST',
            headers: { Accept: "application/json", 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            contentType:false,
            processData:false,
            cache: false,
            url: "{{ route('classroom.save') }}",
            data: formData,
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                $('#loader').addClass('hidden')
                if (response.status == 'true') {
                    if(dataShow.progress != 'done' && !dataShow.no_popup){
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(ok => {
                            if(fromPage == toPage){
                                //jika quiz adalah halaman terakhir maka langsung redirect ke daftar kelas jika berhasil submit
                                finishCourse(dataShow.id_course_detail);
                                saveExistingCourse(dataShow.id_course_detail)
                                localStorage.removeItem("lms_borwita_existing_page");
                                window.location.href = "{{ url('learning_management/lms/class_room') }}";
                                return false;
                            } else {
                                if(toPage == content.length){
                                    saveExistingCourse(dataShow.id_course_detail)
                                    finishCourse(dataShow.id_course_detail);
                                }
                                if(dataShow.type == 'materi'){
                                    $(".content_learning").html(show_materi(dataShow));
                                    if(dataShow.data.link && canInitPlayer) {
                                        initPlayer();
                                    }
                                    $(".page_content_learning").html(`${btnBefore} ${btnNext}`);
                                } else {
                                    $(".content_learning").html(show_quiz(dataShow));
                                    $(".page_content_learning").html(`${btnNext}`);
                                }
                            }
                        });
                    } else {
                        if(fromPage == toPage){
                            //jika quiz adalah halaman terakhir maka langsung redirect ke daftar kelas jika berhasil submit
                            finishCourse(dataShow.id_course_detail);
                            saveExistingCourse(dataShow.id_course_detail)
                            localStorage.removeItem("lms_borwita_existing_page");
                            window.location.href = "{{ url('learning_management/lms/class_room') }}";
                            return false;
                        } else {
                            if(toPage == content.length){
                                saveExistingCourse(dataShow.id_course_detail)
                                finishCourse(dataShow.id_course_detail);
                            }
                            if(dataShow.type == 'materi'){
                                $(".content_learning").html(show_materi(dataShow));
                                if(dataShow.data.link && canInitPlayer) {
                                    initPlayer();
                                }
                                $(".page_content_learning").html(`${btnBefore} ${btnNext}`);
                            } else {
                                $(".content_learning").html(show_quiz(dataShow));
                                $(".page_content_learning").html(`${btnNext}`);
                            }
                        }
                    }
                    
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

                    let contentBefore = content[parseInt(fromPage)-1];

                    if(contentBefore.progress == 'not_yet'){
                        bgPage = `bg-gradient-success`;
                        $(`.mypage[data-page="${contentBefore.sequence}"]`).removeClass('bg-gradient-primary').addClass(bgPage);
                    } 

                    if(content.length < parseInt(fromPage)){
                        let contentNext = content[parseInt(fromPage)];
                        if(contentNext.progress == 'not_yet'){
                            bgPage = `bg-gradient-secondary`;
                            $(`.mypage[data-page="${contentNext.sequence}"]`).removeClass('bg-gradient-success').addClass(bgPage);
                        }
                    }

                    let localStorageContentExisting = JSON.stringify({'id_course':contentBefore.id_course, 'sequence': contentBefore.sequence});
                    localStorage.setItem("lms_borwita_existing_page", localStorageContentExisting);
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
        return result;
    }

    function classroom(id_class) {
        $.ajax({
            type: 'GET',
            url: '{{ url("learning_management/lms/class_room/join") }}/'+id_class,
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                $('#class_materi').html('');
                $('#class_quiz').html('');
                $('#loader').addClass('hidden');
                $('#id_event_management').val(response.data.program.id_event_management);
                $('#id_event_program').val(response.data.program.id_event_program);

                if(response.status == 'false'){
                    let no_access = `<div class="row">
                                        <div class="col-md-12">
                                            <center><h2>${response.message}</h2></center>
                                        </div>
                                    </div>`;
                    $('.courseName').html('Course : ');
                    $('#class_materi').html(no_access);
                    $('#class_quiz').html(no_access);
                    $('#save_button').hide(); 
                } else {
                    let courseName = response.data.course;
                    let className = response.data.class;
                    let content = response.data.content;
                    let notes = response.data.notes;
                    let idCourse = response.data.id_course;
                    let idEmployee = response.data.id_employee;
                    let progress = response.data.progress;
                    sequence_existing = response.data.sequence_existing;

                    $('.courseName').html(`Course : ${courseName}`);
                    $('.className').html(`Class : ${className}`);

                    if(progress == 'done'){
                        let getExistingPageLocal = localStorage.getItem("lms_borwita_existing_page");
                        if(getExistingPageLocal != null){
                            existPage = JSON.parse(getExistingPageLocal);
                            if(existPage.id_course == idCourse){
                                showContent(existPage.sequence, content)
                                // $('.value_next').trigger('click');
                            } else {
                                showContent(1, content)
                            }
                        } else {
                            showContent(1, content)
                        }
                    } else {
                        showContent(sequence_existing, content)
                        // $('.value_next').trigger('click');
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

    const showContent = (sequence, content=null, fromPage=false) => {
        $('html, body').animate({scrollTop: $(`.courseBody`).offset().top}, 1000);
        sequence = parseInt(sequence);
        if(content==null){
            let getContentLocal = localStorage.getItem("lms_borwita");
            if(getContentLocal != null){
                content = JSON.parse(getContentLocal);
            } else {
                content =[];
            }
        } else {
            let localStorageContent = JSON.stringify(content);
            localStorage.setItem("lms_borwita", localStorageContent);
        }

        if(content.length > 0){
            let page = '';
            let i_page = 1;
            let bgPage;
            let currentPageIsQuiz = false;

            content.forEach((contentObj) => {
                if(contentObj.sequence == sequence && contentObj.type == 'quiz'){
                    currentPageIsQuiz = true;
                }
            })

            $(content).each(function (i, val) {
                if(val.sequence == sequence){
                    let valueBefore = sequence==1 ? 1 : sequence-1;
                    let valueNext = sequence==content.length ? sequence : sequence+1;
                    let btn_next = ``;
                    let btn_before = ``;
                    let thisPage = val.sequence;
                    let progress = val.progress;

                    if(valueNext == sequence){
                        //kondisi tombol quiz yg belum terjawab di halaman terakhir
                        labelSave = 'Save';
                    } else {
                        if(progress=='done'){
                            //kondisi tombol quiz yg sudah terjawab
                            labelSave = 'Next';
                        } else {
                            //kondisi tombol quiz yg belum terjawab yg selain halaman terakhir
                            labelSave = 'Save & Next';
                        }
                    }

                    if(sequence < content.length){
                        //Kondisi jika bukan Halaman terakhir
                        if(content[i+1].type == 'quiz'){
                            //kondisi jika halaman sebelumnya adalah quiz
                            if(val.type == 'quiz'){
                                //kondisi jika halaman saat ini adalah quiz (sehingga dalam kondisi disini, terdapat 2 pengecekan : 1 pengecekan type content sebelumnya dan content yg akan dimuat saat ini)
                                labelNext = labelSave;
                                btn_next = `<div><button onclick="return false;" class="btn btn-success value_next" value="${valueNext}" from="${thisPage}">${labelNext} <i class="fas fa-angle-right right"></i></button></div>`;
                            } else {
                                labelNext = 'Next to Quiz';
                                btn_next = `<div><button onclick="return false;" class="btn btn-success value_next" value="${valueNext}" from="${thisPage}">${labelNext} <i class="fas fa-angle-right right"></i></button></div>`;
                            }
                        } else {
                            //kondisi jika halaman sebelumnya bukan quiz
                            if(val.type == 'quiz'){
                                //kondisi jika halaman saat ini adalah quiz (sehingga dalam kondisi disini, terdapat 2 pengecekan : 1 pengecekan type content sebelumnya dan content yg akan dimuat saat ini)
                                labelNext = labelSave;
                                btn_next = `<div><button onclick="return false;" class="btn btn-success value_next" value="${valueNext}" from="${thisPage}">${labelNext} <i class="fas fa-angle-right right"></i></button></div>`;
                            } else {
                                labelNext = 'Next';
                                btn_next = `<div><button onclick="return false;" class="btn btn-success value_next" value="${valueNext}" from="${thisPage}">${labelNext} <i class="fas fa-angle-right right"></i></button></div>`;
                            }
                        }
                    } else {
                        //Kondisi jika Halaman terakhir
                        if(val.type == 'quiz'){
                            if(val.progress == 'done'){
                                labelNext = 'Back to Class';
                                backToClass = "{{ url('learning_management/lms/class_room') }}";
                                btn_next = `<div><button onclick="return false;" class="btn btn-danger value_next" value="${valueNext}" from="${thisPage}" url="${backToClass}"><i class="fas fa-angle-left"></i> ${labelNext} </button></div>`;
                            } else {
                                labelNext = labelSave;
                                btn_next = `<div><button onclick="return false;" class="btn btn-success value_next show_confirm_popup" value="${valueNext}" from="${thisPage}" url="">${labelNext}</button></div>`;
                            }
                        } else {
                            labelNext = 'Back to Class';
                            backToClass = "{{ url('learning_management/lms/class_room') }}";
                            btn_next = `<div><button onclick="return false;" class="btn btn-danger value_next" value="${valueNext}" from="${thisPage}" url="${backToClass}"><i class="fas fa-angle-left"></i> ${labelNext} </button></div>`;
                        }
                    }

                    if(sequence > 1){
                        fromPage = (fromPage!=false) ? parseInt(fromPage) : false;
                        if(valueNext == sequence){
                            if(content[i].type == 'quiz'){
                                if(fromPage==sequence){
                                    //Kondisi yang halaman terakhirnya yg berupa quiz dan menekan submit
                                    submitQuiz(val, btn_next, btn_before, fromPage, sequence, content);
                                } else if(content[i-1].type == 'quiz' && content[i-1].progress != 'done'){
                                    //Jika halaman sekarang quiz dan (sebelumnya juga quiz dan belum dijawab)
                                    submitQuiz(val, btn_next, btn_before, fromPage, sequence, content);
                                } else if(content[i].type == 'quiz'){
                                    //kondisi utk menampilkan haolaman saat ini
                                    // saveExistingCourse(val.id_course_detail)
                                    if(val.type == 'materi'){
                                        $(".content_learning").html(show_materi(val));
                                        if(val.data.link && canInitPlayer) {
                                            initPlayer();
                                        }
                                        $(".page_content_learning").html(`${btn_before} ${btn_next}`);
                                    } else {
                                        $(".content_learning").html(show_quiz(val));
                                        $(".page_content_learning").html(`${btn_next}`);
                                    }
                                }
                            } else {
                                if(content[i-1].type == 'quiz' && content[i-1].progress != 'done'){
                                    //Jika halaman sekarang bukan quiz dan (sebelumnya berupa quiz dan belum dijawab)
                                    submitQuiz(val, btn_next, btn_before, fromPage, sequence, content);
                                } else {
                                    //Kondisi halaman terakhir bukan quiz
                                    // saveExistingCourse(val.id_course_detail)
                                    finishCourse(val.id_course_detail);
                                    if(val.type == 'materi'){
                                        $(".content_learning").html(show_materi(val));
                                        if(val.data.link && canInitPlayer) {
                                            initPlayer();
                                        }
                                        $(".page_content_learning").html(`${btn_before} ${btn_next}`);
                                    } else {
                                        $(".content_learning").html(show_quiz(val));
                                        $(".page_content_learning").html(`${btn_next}`);
                                    }
                                }
                            }
                        } else {
                            if(content[i-1].type == 'quiz'){
                                //Jika yg tampil adalah quiz maka harus dilakukan pengecekan jawaban apakah sudah diisi
                                submitQuiz(val, btn_next, btn_before, fromPage, sequence, content);
                                // saveExistingCourse(val.id_course_detail)
                            } else {
                                // saveExistingCourse(val.id_course_detail)
                                if(val.type == 'materi'){
                                    $(".content_learning").html(show_materi(val));
                                    if(val.data.link && canInitPlayer) {
                                        initPlayer();
                                    }
                                    $(".page_content_learning").html(`${btn_before} ${btn_next}`);
                                } else {
                                    $(".content_learning").html(show_quiz(val));
                                    $(".page_content_learning").html(`${btn_next}`);
                                }
                            }
                        }
                    } else {
                        val.no_popup = true;
                        submitQuiz(val, btn_next, btn_before, fromPage, sequence, content);
                        // saveExistingCourse(val.id_course_detail)
                        if(val.type == 'materi'){
                            $(".content_learning").html(show_materi(val));
                            if(val.data.link && canInitPlayer) {
                                initPlayer();
                            }
                            $(".page_content_learning").html(`${btn_before} ${btn_next}`);
                        } else {
                            $(".content_learning").html(show_quiz(val));
                            $(".page_content_learning").html(`${btn_next}`);
                        }
                    }

                    let localStorageContentExisting = JSON.stringify({'id_course':val.id_course, 'sequence': sequence});
                    localStorage.setItem("lms_borwita_existing_page", localStorageContentExisting);
                }

                if(val.progress == 'not_yet'){
                    if(sequence == i_page){
                        bgPage = `bg-gradient-success`;
                    } else {
                        bgPage = `bg-gradient-secondary`;
                    }
                } else {
                    if(sequence == i_page){
                        bgPage = `bg-gradient-success`;
                    } else {
                        bgPage = `bg-gradient-secondary`;
                    }
                }

                let backAllowed = val.type == 'materi' && i_page <= sequence && !currentPageIsQuiz;
                page += `<a href="javascript:;" class="btn btn-sm ${bgPage} mr-1 text-white font-weight-bold ${backAllowed ? '' : 'mypage'}" data-page="${i_page}" ${backAllowed ? `onClick="showContent(${i_page})"` : ''} >${i_page}</a>`;
                i_page++;
                $(".content_page").html(page);

            });
        } else {
            $(".content_learning").html(`<div class="col-md-12 btn btn-rounded bg bg-danger"><h5>No content found in this course.</h5></div>`);
        }
    }

    function show_materi(data) {
        let materi          = '';
        let content_materi  = '';
        let i_materi        = 1;
        let page            = '';
        let result          = '';
        let item            = data.data;
        let show_content    = '';
        let content_lock    = `<h4 class="text-center text-black mb-3">Content is locked</h4><div class=" text-center mb-5"><svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" fill="currentColor" class="bi bi-file-earmark-lock" viewBox="0 0 16 16"> <path d="M10 7v1.076c.54.166 1 .597 1 1.224v2.4c0 .816-.781 1.3-1.5 1.3h-3c-.719 0-1.5-.484-1.5-1.3V9.3c0-.627.46-1.058 1-1.224V7a2 2 0 1 1 4 0zM7 7v1h2V7a1 1 0 0 0-2 0zM6 9.3v2.4c0 .042.02.107.105.175A.637.637 0 0 0 6.5 12h3a.64.64 0 0 0 .395-.125c.085-.068.105-.133.105-.175V9.3c0-.042-.02-.107-.105-.175A.637.637 0 0 0 9.5 9h-3a.637.637 0 0 0-.395.125C6.02 9.193 6 9.258 6 9.3z"/> <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/> </svg></div>`;

        if(item.course_name != ''){
            let description = item.description;
            let link        = item.link;
            let attachment  = item.attachment;

            if(description != null){
                content_materi += `<h5>${description}</h5><br>`;
            } 
            if(item.type == 'Link' && link != null){
                if(link.includes('youtube')) {
                    content_materi += `
                    <div class="timeline-body">
                        <div class="embed-responsive embed-responsive-16by9">
                            <iframe class="embed-responsive-item" style="padding:1rem;" src="${link}" allowfullscreen id="yt-embed-player"></iframe>
                        </div>
                    </div>`;
                } else if(link.includes('bench.borwita.co.id')) {
                    manifestUri = link;
                    content_materi += `
                    <div class="timeline-body">
                        <div class="embed-responsive embed-responsive-16by9">
                            <div data-shaka-player-container style="max-width:40em;position:unset;" data-shaka-player-cast-receiver-id="BBED8D28" id="shakaContainer">
                                <video id="shakaVideoPlayer" autoplay data-shaka-player>
                                </video>
                            </div>
                        </div>
                    </div>`;
                } else {
                    content_materi += `
                    <div class="timeline-body">
                        <h3>Unsupported source platform</h3>
                    </div>`;
                }
                // onYouTubeIframeAPIReady();
            }

            if(item.type == 'Presentation'){
                if(attachment != null){
                    let viwerJs = "{{ asset('vendor/ViewerJS/index.html?zoom=page-width#') }}../../";
                    let embed = `<iframe src="${viwerJs}${attachment}" width='100%' height='768' allowfullscreen webkitallowfullscreen></iframe>`;

                    content_materi += `<center><div class="row">
                        <div class="col-md-12 font-weight-bold mb-2"></div>
                        <div class="col-md-12 font-weight-bold">${embed}</div>
                        </center><br>`;
                }
            }
            
            if(item.allow == true){
                show_content = content_materi;
            } else {
                show_content = content_lock;
            }

            materi += `<div class="materi" id="materi_${i_materi}">
                            <h4 class="text-center text-primary mb-4">${item.course_name}</h4>
                            <div class="row">
                                <div class="col-md-12">
                                    ${show_content}
                                </div>
                            </div>
                        </div>`;

            i_materi++;
        } else {
            materi += `<h5 class="text-center mt-5 mb-5">No content found in this page.</h5>`;
            page += '';
        }

        result = `${materi} <hr>`;
        return result;
    }

    function show_quiz(data, status) {
        let quiz                = '';
        let content_quiz        = '';
        let i_quiz              = 1;
        let page                = '';
        let result              = '';
        let item                = data.data;
        let id_survey_header    = item.id_survey_header;
        let quiz_name           = item.quiz_name;

        if(item.data.length > 0){
            quiz += `<h4 class="text-center text-primary mb-4">${quiz_name}</h4>`;

            if(data.progress == 'done'){
                quiz += `<h4 class="text-center text-black mb-3">You have answered</h4><div class=" text-center mb-5"><svg class="svg-icon" style="width: 150px; height: 150px;vertical-align: middle;fill: currentColor;overflow: hidden;" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"><path d="M884.010667 653.013333a42.666667 42.666667 0 0 1-1.024 60.330667l-156.544 151.338667a112.64 112.64 0 0 1-155.818667 0l-88.32-85.333334a42.666667 42.666667 0 0 1 59.349333-61.354666l88.277334 85.333333a27.306667 27.306667 0 0 0 37.205333 0l156.544-151.338667a42.666667 42.666667 0 0 1 60.330667 1.024z" fill="#75C82B" /><path d="M170.666667 128a42.666667 42.666667 0 0 0-42.666667 42.666667v674.602666a42.666667 42.666667 0 0 0 42.666667 42.666667h261.973333a42.666667 42.666667 0 0 1 0 85.333333H170.666667a128 128 0 0 1-128-128V170.666667a128 128 0 0 1 128-128h554.325333a128 128 0 0 1 128 128v349.994666a42.666667 42.666667 0 0 1-85.333333 0V170.666667a42.666667 42.666667 0 0 0-42.666667-42.666667H170.666667z" fill="#75C82B" /><path d="M213.333333 341.333333a42.666667 42.666667 0 0 1 42.666667-42.666666h310.741333a42.666667 42.666667 0 1 1 0 85.333333H256a42.666667 42.666667 0 0 1-42.666667-42.666667zM213.333333 512a42.666667 42.666667 0 0 1 42.666667-42.666667h134.058667a42.666667 42.666667 0 1 1 0 85.333334H256a42.666667 42.666667 0 0 1-42.666667-42.666667z" fill="#75C82B" /></svg></div>`;
            } else {
                for (let item_detail of item.data) {
                    let answer_type     = item_detail.type;
                    let id_question     = item_detail.id_survey_question;
                    let all_answer      = '';
                    let classThisAnswer = '';
                    let divSequenceAnswer = '';
                    let elementInputSequenceAnswer = `<input type="hidden" name="sequence_answer_${id_question}" value=""/><input type="hidden" name="sequence_answer_count_${id_question}" value="${item_detail.answer.length}"/>`;

                    if(answer_type != 'Essay' && answer_type != 'Upload_Files'){
                        if(answer_type == 'Sequence_Answer'){
                            sequenceAnswerAll[id_question] = [];
                        }

                        for (let item_answer of item_detail.answer) {
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
                                id_answer = item_answer.id_survey_answer;
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
                            } else if(answer_type == 'Upload_Files') {
                                type        = 'file';
                                reff        = `<span class="input-group-text font-weight-bold">${item_answer.reference}</span>`;
                            } else {
                                type        = 'radio';
                                nameAnswer  = `name="answer_${id_question}[]"`;
                                reff        = `<span class="input-group-text font-weight-bold">${item_answer.reference}</span>`;
                            }

                            all_answer += `<div class="col-md-12 mb-2">
                                                <div class="input-group">
                                                    <div class="input-group-prepend ${classThisAnswer}" id_answer="${id_answer}" id_question="${id_question}">
                                                        ${reff}
                                                        <label class="btn btn-block btn-outline-success answer_label " id_answer="${id_answer}" id_question="${id_question}">
                                                            <div class="icheck-success choose_answer" data-question="${id_question}" data-answer="${id_answer}">
                                                                <input type="${type}" ${nameAnswer} autocomplete="off" class="answer" data-page="${i_quiz}" data-question="${id_question}" data-answer="${id_answer}" value="${id_answer}"> 
                                                                <label> ${answer} </label>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                </div>
                                            </div>`;
                        }
                    } else if(answer_type == 'Essay') {
                        all_answer += `<div class="col-md-11 mb-2">
                                            <textarea rows="2" class="answer form-control form-control-sm" name="answer_${id_question}[]" data-page="${i_quiz}" data-question="${id_question}" ></textarea>
                                        </div>`;
                    } else if(answer_type == 'Upload_Files') {
                        all_answer += `<div class="col-md-11 mb-2">
                                            <input type="file" class="form-control" name="answer_${id_question}" data-page="${i_quiz}" data-question="${id_question}">
                                        </div>`;
                    }

                    quiz += `<h5 style="font-size:18px;" class="text-black"><span class="text-primary">${i_quiz}.</span> ${item_detail.question}</h5><br>
                            <div class="row">
                                <div class="col-md-12" style="margin-left:10px;">
                                    <div ${answer_type!='Upload_Files' ? 'class="btn-group-toggle" data-toggle="buttons"' : ''}>
                                        <div class="row">
                                            ${all_answer}
                                            ${elementInputSequenceAnswer}
                                            <input hidden value="${id_question}" name="id_question[]" >
                                            <input hidden value="${answer_type}" name="answer_type_${id_question}" >
                                            <input hidden value="${id_survey_header}" name="id_survey_header">
                                            <input hidden value="${data.id_course_detail}" name="id_course_detail">
                                        </div>
                                    </div>
                                </div>
                            </div><br>`;
                    i_quiz++;
                } 
            }
        } else {
            quiz += `<h5 class="text-center mt-5 mb-5">No content found in this page.</h5>`;
            page += '';
        }
        result = `<div class="question"> ${quiz} </div>`;

        return result;
    }

    const saveExistingCourse = (idCourseDetail) => {
        $.ajax({
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            url: "{{ url('learning_management/lms/class_room/save-course') }}",
            type: "POST",
            data: {
                id_event_program: "{{ Request::get('c')}}",
                id_course_detail: idCourseDetail,
            },
            success: function (resp) {
            },
            error: function (jqXHR, exception) {
                saveExistingCourse(idCourseDetail)
            },
        });
    }

    const finishCourse = (idCourseDetail) => {
        $.ajax({
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            url: "{{ url('learning_management/lms/class_room/finish-course') }}",
            type: "POST",
            data: {
                id_event_program: "{{ Request::get('c')}}",
                id_course_detail: idCourseDetail,
            },
            success: function (resp) {
            },
            error: function (jqXHR, exception) {
                finishCourse(idCourseDetail)
            },
        });
    }

</script>
@endsection