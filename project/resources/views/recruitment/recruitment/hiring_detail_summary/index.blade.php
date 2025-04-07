@extends('adminlte::page')
@section('title', 'Hiring Detail Summary')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
			 <div class="card-header">
                <h5 class="card-title">Hiring Detail Summary</h5>
            </div>
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="hiring_request_table" style="width:1200px;" class="display table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th>No</th>
						<th>Request Position Detail</th>
						<th>Location</th>
						<th style="width:150px;">Replace Name</th>
						<th>Request Type</th>
						<th>Request By</th>
						<th>Reference Number</th>
						<th>SLA Days</th>
						<th>SLA Status</th>
						<th>Status</th>
						<th style="width:150px;text-align:center;">Notes</th>
						<th style="width:50px;text-align:center;">Update Date</th>
						<th style="width:100px;">Candidate Name</th>
						<th style="text-align:center;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_hiring"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="hiringForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h6 class="modal-title"></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
							<div class="row">
                                <label class="col-sm-2 col-form-label">Notes</label>
                                <div class="col-sm-10">
									<input name="id_hiring_request_detail" id="id_hiring_request_detail" type="hidden">	
									<textarea class="summernote" name="notes" id="notes"></textarea>
									<i>Untuk Space Kebawah (Shift+Enter) </i>
                                </div>
                            </div>
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

@endsection
@section('css')
<style type="text/css"> 
.dtfc-fixed-left{
z-index:10;
}
.no-wrap{
	white-space:nowrap;
}
.notes-width{
	width:150px;
	text-align:justify;
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_hiring_request_detail = 0;

$('#hiringForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();			
	
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#hiringForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('detail_summary.update_detail') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_hiring').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#hiring_request_table').DataTable().ajax.reload();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    },
					
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);																
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


 $(document).on('click', '.edit', function () {
		global_id_hiring_request_detail = $(this).attr('id');
		$("#hiringForm")[0].reset();
		$('.summernote').summernote('reset');
		get_edit(global_id_hiring_request_detail);
		
		$('#modal_form_hiring').modal('show');
   });
   
   function get_edit(global_id_hiring_request_detail) {
		$.ajax({
			url: "<?= url('recruitment/recruitment/hiring_detail_summary/get_detail_summary_edit') ?>",
            method: "GET",
            data: {id_hiring_request_detail: global_id_hiring_request_detail},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				$("#hiringForm .modal-title").html('Edit Notes Request ('+response.position_detail+')');
				$('#id_hiring_request_detail').val(response.id_hiring_request_detail).trigger('change');
			//	$('#notes').val(response.notes).trigger('change');
				$("#notes").summernote("code", response.notes);
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
	}
		
$(document).ready(function(){
	$('.summernote').summernote({
		height:100,
		toolbar: [
			['font', ['bold', 'italic', 'underline', 'clear']],
			['fontname', ['fontname']],
			['color', ['color']],
			['height', ['height']],
			['view', ['fullscreen', 'codeview']],
			['help', ['help']]
		  ],
	});
		
    var t = $('#hiring_request_table').DataTable({
        processing: true,
	//	responsive: true,
		columnDefs: false,
		select: {
		  style:    'multi+shift',
		  selector: 'td:nth-child(1)'
		},
		scrollX: true,
		scrollCollapse: true,
		fixedColumns: {
			right: 5,
		},
        ajax: {
			url: "{{ route('detail_summary.index') }}",
			data: {id_url: global_url_server},	
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#hiring_request_table').DataTable().ajax.reload();
				}
			},
		rowCallback: function(row, data, index){
				if(access_create == 0){
					$(row).find('.new').css('display', 'none');
				}	
				if(access_edit == 0){
					$(row).find('.edit').css('display', 'none');
				}		
				if(access_delete == 0){
					$(row).find('.delete').css('display', 'none');
				}
				if(access_print == 0){
					$(row).find('.print').css('display', 'none');
				}	
			  },
        columns: [
			{   // Checkbox select column
			data: 'id_hiring_request_detail',
			orderable: false,
			targets: 0,
			render: function(data, type, row, meta){            
					  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
				   return data;
				},
			checkboxes: {
				   selectRow: true,
				   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
				}
			},
            {defaultContent: '',orderable: false},
            { data: 'position_detail', name: 'position_detail', className: 'no-wrap' },
            { data: 'location', name: 'location'},
            { data: 'emp_replace', name: 'emp_replace'},
            { data: 'request_type', name: 'request_type' },
			{ data: 'emp_name', name: 'emp_name'},
            { data: 'reference_number', name: 'reference_number', className: 'no-wrap'},
            { data: 'sla_days', name: 'sla_days', className: 'text-center', render: function ( data, type, row ) {
					if(row.sla_days == null && row.sla_sign == 'minus'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Off Track</span>';
					}
					else if(row.sla_days == null && row.sla_sign == 'plus'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">On Track</span>';
					}
					else if(row.sla_days < 0){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else if(row.sla_days >= 0){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
					}
					else{
						return '-';
					}
					
				}
			},
            { data: 'sla_status', name: 'sla_status', className: 'text-center', render: function ( data, type, row ) {
					if(row.sla_status == 'unmeet'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Unmeet</span>';
					}
					else if(row.sla_status == 'meet'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Meet</span>';
					}
					else{
						return '-';
					}
				}
			},
			{ data: 'hiring_status', name: 'hiring_status', className: 'text-center', render: function ( data, type, row ) {
					if(row.hiring_status == 'Hiring'){
							return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">On Progress</span>';
					}
					else if(row.hiring_status == 'Hired'){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Hired</span>';
					}
				} 
			},
			{ data: 'notes', name: 'notes', className: 'notes-width', render: function ( data, type, row ) {		
				let val = $("<span>").html(data).text();
				return val;
				} 
			},
            { data: 'update_date_notes', name: 'update_date_notes', render: function ( data, type, row ) {	
					if(data != null){
						return '<div align="center">'+moment(data).format('DD MMM YYYY')+'<br> '+moment(data).format('hh:mm:ss')+'<br> ('+row.created_by+')</div>';
					}
					else{
						return '';
					}
				} 
			},
			{ data: 'name_candidate', name: 'name_candidate'},
			{ data: 'action', name: 'action', orderable: false, className: 'no-wrap', render: function ( data, type, row ) {	
					return data;
				} 
			},
        ]
    });
	
	t.on('order.dt search.dt', function () {
        let i = 1;
        t.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
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