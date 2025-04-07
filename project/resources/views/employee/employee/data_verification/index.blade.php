@extends('adminlte::page')
@section('title', 'Employee Data Verification')

@section('content')

<div class="row" id="result_survey" >
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title title_result">Employee Data Verification from Survey</h5>
            </div>
            
            <div class="modal-body">
                <div class="form-group row">              
                    <label class="col-sm-2 col-form-label">Search by Survey and Period:</label>
                    <div class="col-sm-3">
                        <select id="all_survey" class="form-control form-control-sm select2" style="width: 100%;"></select>
                    </div>
                    <div class="col-sm-3">
                        <select id="survey_period" class="form-control form-control-sm select2" style="width: 100%;"></select>
                    </div>
                    <div class="col-sm-2">
                        <button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>                                      
                </div>

                <button type="button" class="btn btn-default advanced_result">Advanced Search</button>
                <br><br>
                <table id="result_table" class="table table-striped table-bordered table-hover datatable">
                 <thead>
                  <tr>      
                    <th></th>
                    <th></th>
                    <th>No</th>
                    <th>Employee</th>
                    <th>NIK</th>
                    <th>Survey</th>
                    <th>Period</th>
                    <th>Data to Validate</th>
                    <th>Verification Status</th>
                    <th data-priority="2" style="text-align:center;">Action</th>    
                  </tr>
                 </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_verification" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="overflow-y: scroll;overflow-x: scroll;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="verifyForm">
                <div class="modal-header">
                    <h5 class="modal-title className">Data Verification</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body div_result" ></div>
                <div class="modal-footer">
                    <input id="verify_id_survey_header" hidden>
                    <input id="verify_id_survey_history" hidden>
                    <input id="verify_id_employee" hidden>
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button> 
                    <button type="submit" class="btn btn-success" id="submit_verify">Verify</button>&nbsp;        
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
    let id_survey = "";
    let surveyName = "";
    let allSurvey = [];
    let allSurveyPeriod = [];

    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];

    const getSurvey = async () => {
        try {
            let result;
            result = await $.ajax({
                url: "{{ url('general_setting/company_setting/hr_config_settings/get_data') }}",
                method: "GET",
                success: function (res) {
                    allSurvey = res.survey;
                    $(`#all_survey`).html('').prepend('<option selected></option>').select2({
                        placeholder: "Select Survey",
                        data: allSurvey,
                        allowClear: true,
                    });
                },
            });
            return result;
        } catch (error) {
            getSurvey();
        }
    }

    const getSurveyPeriod = async (idSurvey='') => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('general_setting/company_setting/hr_config_settings/getSurveyPeriod') ?>',
                method: "GET",
                data: {id_survey_header:idSurvey},
                success: function (res) {
                    allSurveyPeriod = [];
                    $.each(res, function (i, val) {
                        name = `${val.date_start} - ${val.date_end}`;
                        allSurveyPeriod.push({id:val.id_survey_history, text:name});
                    });

                    $(`#survey_period`).html('').select2({
                        placeholder: "Select Period",
                        data: allSurveyPeriod,
                        // allowClear: true,
                    });
                },
            });
            return result;
        } catch (error) {
            getSurveyPeriod(idSurvey);
        }
    }

    const submitVerify = async (idSurvey='') => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('general_setting/company_setting/hr_config_settings/getSurveyPeriod') ?>',
                method: "GET",
                data: {id_survey_header:idSurvey},
                success: function (res) {
                    allSurveyPeriod = [];
                    $.each(res, function (i, val) {
                        name = `${val.date_start} - ${val.date_end}`;
                        allSurveyPeriod.push({id:val.id_survey_history, text:name});
                    });

                    $(`#survey_period`).html('').select2({
                        placeholder: "Select Period",
                        data: allSurveyPeriod,
                        // allowClear: true,
                    });
                },
            });
            return result;
        } catch (error) {
            getSurveyPeriod(idSurvey);
        }
    }

    const getSurveyResult = () => {
        let selectedSurvey = $(`#all_survey option:selected`).val();
        let selectedPeriod = $(`#survey_period option:selected`).val();

        $('#result_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            destroy:true,
            pageLength: 10,
            ajax: {
                url: "{{ url('employee/employee/data_updates_verification') }}",
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                method: "GET",
                data: {id_survey_header: selectedSurvey, id_survey_history:selectedPeriod},
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#survey_table').DataTable().ajax.reload();
                }
            },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
                {   // Checkbox select column
                    data: 'id_survey_header',
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex'},
                {data: 'name'},
                {data: 'nik_employee'},
                {data: 'survey_name'},
                {data: 'start_date', orderable: false, 
                    render: function ( data, type, row ) {  
                        let action = `${row.start_date} ~ ${row.end_date}`;
                        return action;
                    } 
                },
                {data: 'data_count'},
                {data: 'is_processed', name: 'is_processed', orderable: false, 
                    render: function ( data, type, row ) {  
                        return scoring(row.is_processed);
                    } 
                },
                {data: 'action', name: 'action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let action = '';
                        action = `<center><a href="javascript:;" id_survey_header="${row.id_survey_header}" id_survey_history="${row.id_survey_history}" id_employee="${row.id_employee}" class="show_result btn btn-success btn-sm rounded" title="Show Result" >Show</a></center>`;
                        return action;
                    } 
                },
            ],
            "rowCallback": function(row, val, index) {
                let survey = val.survey_name;
                $('.survey_name').html(`Survey : ${survey} `);
            },
        });
    }

    $(document).ready(function() {
        getSurvey()
        getSurveyResult()
        $(`#survey_period`).html('').select2({
            placeholder: "Select Period",
            data: allSurveyPeriod,
        });
    });

    $(document).on('change', '#all_survey', function (e, fromTrigger) {
        let idSurvey = $(this).val();
        getSurveyPeriod(idSurvey);
    });
    
    $(document).on('click', '#search', function (event) {
        getSurveyResult()
    });

    $(document).on('click', '.show_result', function (event) {
        let id_survey_header    = $(this).attr("id_survey_header");
        let id_survey_history   = $(this).attr("id_survey_history");
        let id_employee         = $(this).attr("id_employee");

        $(`#verify_id_survey_header`).val('');
        $(`#verify_id_survey_history`).val('');
        $(`#verify_id_employee`).val('');
        $('#modal_verification').modal('show');

        $.ajax({
            url: "{{ url('employee/employee/detail_verification') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: {id_survey_header: id_survey_header, id_employee:id_employee, id_survey_history:id_survey_history},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (res) {
                $('#loader').addClass('hidden')
                let show = '';
                let showContent = '';
                let table = '';
                let tableContent = '';
                let identity = '';

                if(res.status == true){
                    $(`#verify_id_survey_header`).val(id_survey_header);
                    $(`#verify_id_survey_history`).val(id_survey_history);
                    $(`#verify_id_employee`).val(id_employee);

                    identity = `<h4 class="text-center">${res.data.identity.name} (${res.data.identity.nik_employee})</h4>`;

                    if(res.data.verify_status == true){
                        identity += `<br><div class="alert alert-success text-center"><h4>Verification is complete</h4></div>`;
                        $(`#submit_verify`).hide();
                    } else {
                        $(`#submit_verify`).show();
                        //Sementara hide dulu, karena proses submit verifiy ini masih perlu pengecekan berkali2. yg buat fitur ini keburu resign
                        // $(`#submit_verify`).hide();
                    }

                    show += `<table class="table table-striped table-bordered table-hover">`;
                    if(res.data.result.length > 0){
                        $.each(res.data.result, function (i, val) {
                            let thisDataExist = val.data_existing;
                            let thisActionType = val.action_type;
                            let thisNewData = '';
                            let thisAction = '';
                            let question_type = val.question_type;

                            if(question_type=='Single_Answer' || question_type=='Multiple_Answer'){
                                thisNewData = val.answer_code;
                            } else if(question_type=='Essay'){
                                thisNewData = val.answer_essay;
                            } else if(question_type=='Upload_Files'){
                                if(val.answer_attachment_path==null){
                                    thisNewData = `File Moved / Not Found `;
                                } else {
                                    if(val.answer_attachment_extension=='pdf'){
                                        let viwerJs = "{{ asset('vendor/ViewerJS/index.html?zoom=page-width#') }}../../";
                                        let embed = `<iframe src="${viwerJs}${val.answer_attachment_path}" width='100%' height='300' allowfullscreen webkitallowfullscreen></iframe>`;
                                        thisNewData = `${embed}`;
                                    } else {
                                        thisNewData = `<a href="${val.answer_attachment_path}" data-toggle="lightbox"><img src="${val.answer_attachment_path}" style="max-width:150px; object-fit: cover;"></a>`;
                                    }
                                }
                            }

                            tableContent += `<tr>
                                <td style="max-width:50px;">${i+1}</td>
                                <td style="max-width:150px;text-wrap:wrap;">${val.sequence}. ${val.question}</td>
                                <td>${thisDataExist}</td>
                                <td  style="max-width:100px;">${thisActionType}</td>
                                <td>${thisNewData}</td>
                            </tr>`;
                        });
                    } else {
                        tableContent += '<tr><th colspan="5">No Data to view</th></tr>';
                    }

                    show += `<table class="table table-striped table-bordered table-hover">
                            <tr>
                                <th>No</th>
                                <th>Survey Question</th>
                                <th>Data Existing</th>
                                <th>Action Type</th>
                                <th>User Answer</th>
                            </tr>
                            ${tableContent}
                        </table>`;
                } else {
                    show += `<div class="alert alert-danger alert-dismissible">
                                <h5> Failed </h5>
                                Data failed, please refresh
                            </div>`;
                }

                showContent = `<div class="row">
                    <div class="col-md-12">
                        ${identity}
                        ${show}
                    </div>
                </div>`;
                $('.div_result').html(showContent);
            },
            error: function (err) {
                $('#loader').addClass('hidden')
            }
        });
    });

    $(document).on("click", ".summary", function () {
        let id_survey_header = $(this).attr('id_survey_header');
        let res = {id_survey_header : id_survey_header};
        let param = objectToQueryString(res);
        let url = "{{ url('employee/summary_survey') }}";
        window.open(url+'?'+param, '_blank');
    });

    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
        event.preventDefault();
        $(this).ekkoLightbox({
            alwaysShowClose: true
        });
    });

    $(document).on('submit', '#verifyForm', function(e) {
        e.preventDefault();
        let verify_id_survey_header = $(`#verify_id_survey_header`).val();
        let verify_id_survey_history = $(`#verify_id_survey_history`).val();
        let verify_id_employee = $(`#verify_id_employee`).val();

        $.ajax({
            method: "POST",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            url: "{{ url('employee/employee/detail_verification/save') }}",
            data: {id_survey_header:verify_id_survey_header, id_survey_history:verify_id_survey_history, id_employee:verify_id_employee},
            success: function (res) {
                if (res.status == true) {
                    swal({
                        icon: 'success',
                        title: 'Success',
                        text: res.message
                    }).then(function(){ 
                        getSurveyResult()
                        $('#modal_verification').modal('hide');
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [Unknown Error]'
                    });
                    $('#modal_verification').modal('hide');
                }
            },
            error: function (response) {
                if (response.status === 422) {
                    let errors = response.responseJSON.errors;
                    Object.keys(errors).forEach(function (key) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "Error").children("strong").text(errors[key][0]);
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

    function objectToQueryString(obj) {
        var str = [];
        for (var p in obj)
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
        return str.join("&");
    }

    function scoring(status) {
        let return_;
        if(status=='0' || status==false){
            return_ = '<center><span class="btn-xs rounded btn-danger">No</span></center>';
        } else {
            return_ = '<center><span class="btn-xs rounded btn-success">Yes</span></center>';
        }
        return return_;
    }
</script>
@endsection