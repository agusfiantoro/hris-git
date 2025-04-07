@extends('adminlte::page')
@section('title', 'Announcement Published')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Announcement Published</h5>              
            </div>
       
			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="announcement_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>					   
						<th></th>
						<th></th>
						<th style="white-space:nowrap;">No</th>
						<th style="white-space:nowrap;">Reference Number</th>
						<th>Description</th>
						<th style="white-space:nowrap;">Employee Request</th>
						<th style="white-space:nowrap;">Announcement Type</th>
						<th>Attachment</th>
						 <th data-priority="1" style="white-space:nowrap;" width=50>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

@include('employee.employee.announcement.popup')

@endsection

@section('scripts')

<script>
function get_employee() {
		$.getJSON('<?= url('employee/employee_setting/announcement/get_employee') ?>', function (data) {
            $('#id_employee_request').select2({
                data: data,
				disabled: true
            });
			
        }).fail(function (data) { // Call failed
            get_employee();
        });
	}
function get_company() {
 $.getJSON('<?= url('employee/employee_setting/announcement/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_company();
        });	
}	
function get_announcement_type() {
 $.getJSON('<?= url('employee/employee_setting/announcement/get_announcement_type') ?>', function (data) {
            $('#id_anouncement_type').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_announcement_type();
        });	
}	

$(function () {
	get_employee();
	get_company();
	get_announcement_type();
});

$(document).on('click', '.view', function(){
    let id_announcement = $(this).attr('id');
    showAnnouncement(id_announcement)
});

$(document).ready(function(){
    $('#announcement_table').DataTable({
        processing: true,
		responsive: true,
   //     serverSide: true,
        ajax: {
		   url: "{{ route('announ.index') }}",
		   error: function (jqXHR, textStatus, errorThrown) {
				$('#announcement_table').DataTable().ajax.reload();
            }
		  },
        columns: [
		{
                defaultContent: '',
				orderable: false,
			},
		{   // Checkbox select column
                data: 'id_announcement',
                defaultContent: '',
                orderable: false
            },
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'reference_number', name: 'reference_number'},
			{ data: 'description', name: 'description' },
			{ data: 'employee_name', name: 'employee_name' },
			{ data: 'announcement_type', name: 'announcement_type' },
			{ data: 'attachment', name: 'attachment',render: function ( data, type, row ) {	
					if(data == null){
						return "";
					}				
					else if(row['attachment_type']==null){
						return '<a href="../../project/storage/app/public/upload/announcement/'+data+'" target="_blank">Download File</a>';
					}
					else if(row['attachment_type']=='pdf'){
						return '<a download="'+Date.now()+'.pdf" href="data:application/pdf;base64,'+ data + '">Download File</a>';
					}
					else if(row['attachment_type']=='image'){
						return '<a download="'+Date.now()+'.jpg" href="data:image/jpg;base64,'+ data + '">Download File</a>';
					}
				} 
			},
			{ data: 'action', name: 'action' },
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
	
});

</script>

@endsection