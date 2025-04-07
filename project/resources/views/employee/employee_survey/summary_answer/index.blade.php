@extends('adminlte::page')
@section('title', 'Employee Survey Management')

@section('content')

<div class="row" id="survey_management">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Survey Management</h5>
            </div>
       
			<div class="card-body">
				<button type="button" class="btn btn-default advanced_survey">Advanced Search</button>
					<br>
					<br>
					<table id="survey_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>		
                        <th></th>
                        <th></th>
                        <th>No</th>
                        <th>Description</th>
                        <th>Request By</th>
                        <th>Survey Type</th>
                        <th>With Score</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th data-priority="2" style="text-align:center;">Action</th>	
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="row" id="result_survey" style="display:none;">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title title_result">Employee Survey Result</h5>
            </div>
            
            <div class="modal-body">
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
                    <th>Score</th>
                    <th>Created at</th>
                    <th data-priority="2" style="text-align:center;">Action</th>    
                  </tr>
                 </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_result_all"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title survey_name">Result</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- <div class="modal-body">
                <table id="result_table" class="table table-striped table-bordered table-hover datatable">
                 <thead>
                  <tr>      
                    <th></th>
                    <th></th>
                    <th>No</th>
                    <th>Employee</th>
                    <th>NIK</th>
                    <th>Score</th>
                    <th>Created at</th>
                    <th data-priority="2" style="text-align:center;">Action</th>    
                  </tr>
                 </thead>
                </table>
            </div> -->
       
        </div>
    </div>
</div>

<div class="modal fade" id="modal_result_employee"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                            <a class="nav-link active" id="class_quiz-tab" data-toggle="pill" href="#class_quiz" role="tab" aria-controls="class_quiz" aria-selected="false">Survey</a>
                        </li>
                    </ul>

                    <div class="tab-content" id="custom-content-below-tabContent">
                        <div class="tab-pane fade show active" id="class_quiz" role="tabpanel" aria-labelledby="class_quiz-tab" style="padding-top: 10px;">
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
    let id_survey = "<?= @$survey ?>";
    let surveyName = "<?= @$surveyName ?>";

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

        if(id_survey != '0'){
            $('#result_survey').show();
            $('#survey_management').hide();
            $('.title_result').html(surveyName);
            $('#result_table').DataTable({
                processing: true,
            //    serverSide: true,
                scrollY: true,
                destroy:true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('employee_survey_management.get_survey') }}",
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    method: "POST",
                    data: {id_survey_header: id_survey},
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
                    {data: 'total_score', render: function ( data, type, row ) {  
                            let score = '';
                            if(row.with_score == 1 || row.with_score == true){
                                score = row.total_score;
                            }
                            return score;
                        } 
                    },
                    {data: 'creation_date'},
                    { data: 'action', name: 'action', orderable: false, 
                        render: function ( data, type, row ) {  
                            let action = '';
                            action = `<center><a href="javascript:;" id_survey_header="${row.id_survey_header}" id_employee="${row.id_employee}" class="show_result btn btn-success btn-sm rounded" title="Show Result" >Show</a></center>`;
                            return action;
                        } 
                    },
                ],
                "rowCallback": function(row, val, index) {
                    let survey = val.survey_name;
                    $('.survey_name').html(`Survey : ${survey} `);
                },
            });
        } else {
            $('#result_survey').hide();
            $('#survey_management').show();
            $('#survey_table').DataTable({
                processing: true,
            //    serverSide: true,
                scrollY: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('employee_survey_management.index') }}",
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
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'description', name: 'description'},
                    {data: 'employee_name', name: 'employee_name'},
                    {data: 'survey_type', name: 'survey_type'},
                    {data: 'with_score', name: 'with_score',
                        render: function ( data, type, row ) {  
                            return scoring(row.with_score);
                        } 
                    },
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    { data: 'action', name: 'action', orderable: false, 
                        render: function ( data, type, row ) {  
                            let action = '';
                            let show_result = `<center><a href="javascript:;" id_survey_header="${row.id_survey_header}" class="show_survey btn btn-success btn-sm rounded" title="Show Result" >Result</a></center> &nbsp; `;
                            let download_result = `<center><a href="javascript:;" id_survey_header="${row.id_survey_header}" class="summary btn btn-primary btn-sm rounded" title="Download Result" ><i class="fa fa-download" aria-hidden="true"></i></a></center>`;
                            return download_result;
                        } 
                    },
                ],
            });
        }

    });

    
    $(document).on('click', '.show_survey', function (event) {
        let id_survey_header = $(this).attr('id_survey_header');
        let res = {id : id_survey_header};
        let param = objectToQueryString(res);
        let url = "{{ url('employee/employee/employee_survey_management') }}";
        window.open(url+'?'+param, '_blank');

        // $('#modal_result_all').modal('show');
    });

    $(document).on("click", ".advanced_survey", function () {
        $('.cf').select2({width:'100%'});
        if($(".survey_table").css('display') == 'none'){
            $(".survey_table").show("slow");
        }
        else {
            $(".survey_table").hide("slow");
        }   
    });

    $(document).on("click", ".advanced_result", function () {
        $('.cf').select2({width:'100%'});
        if($(".result_table").css('display') == 'none'){
            $(".result_table").show("slow");
        }
        else {
            $(".result_table").hide("slow");
        }   
    });

    $(document).on('click', '.show_result', function (event) {
        let id_survey_header    = $(this).attr("id_survey_header");
        let id_employee         = $(this).attr("id_employee");
        $('#modal_result_employee').modal('show');
        $('#class_quiz').html('');
        $('.className').html('Result');

        $.ajax({
            url: "{{ route('employee_survey_management.get_result') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: {id_survey_header: id_survey_header, id_employee:id_employee},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                $('#loader').addClass('hidden')
                
                if(response.status == 'false'){
                    let no_access = `<div class="row">
                                        <div class="col-md-12">
                                            <center><h2>${response.message}</h2></center>
                                        </div>
                                    </div>`;
                    $('#class_quiz').html(no_access);
                } else {
                    if(response.data.show_score == true || response.data.show_score == 1){
                        $('.className').html(`${response.data.name} (${response.data.nik_employee}), Score = ${response.data.score}`);
                    } else {
                        $('.className').html(`${response.data.name} (${response.data.nik_employee})`);
                    }
                    let all_result = '';

                    if(response.data.result.length > 0){
                        $(response.data.result).each(function (i, val) {
                            all_result += `<div class="row">
                                                <div class="col-md-12">
                                                    <h5>${i+1}. ${val.question}</h5>
                                                    <div style="text-indent:2%;font-weight:bold;color:blue;font-size:18px;">${val.answer}</div>
                                                </div>
                                            </div>`;
                            i++;
                        });
                    }
                    $('#class_quiz').html(all_result);
                }
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