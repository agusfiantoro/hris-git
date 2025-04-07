@extends('adminlte::page')
@section('title', 'Employee Survey')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Survey</h5>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
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
                        <!-- <th>Question Type</th> -->
                        <th>Survey Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th data-priority="2" style="text-align:center;">Action</th>	
					  </tr>
					 </thead>
					</table>
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


@section('scripts')

<!-- File show survey dibuat terpisah karena agar digunakan juga di tempat lain yaitu : time_attendance/attendance/attendance karena utk proses tampil survey setelah absen -->
@include('employee.employee_survey.employee_answer.survey');

<script type="text/javascript">
    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];

    $(function () {	
        $('#survey_table').DataTable({
            processing: true,
        //    serverSide: true,
            scrollY: true,
            ajax: {
                url: "{{ route('employee_survey_answer.index') }}",
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
                // {data: 'question_type', name: 'question_type'},
                {data: 'survey_type', name: 'survey_type'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'status', name: 'status'},
                { data: 'action', name: 'action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let action = '';
                        if(row.status_survey == 'done'){
                            action = `<center><span class="btn-sm rounded btn-success" title="Selesai" >Done</span></center>`;
                        } else {
                            action = `<center><a href="javascript:;" onclick="show(${row.action})" class="join btn btn-primary btn-sm rounded" title="Join" >Show</a></center>`;
                        }
                        return action;
                    } 
                },
            ],
 
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
    });
    
</script>
@endsection