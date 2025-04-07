@extends('adminlte::page')
@section('title', 'Master Category KPI')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Category KPI Onboarding</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Category Kpi</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="content_table" class="table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
                    <th></th>
					<th></th>
					<th>No</th>
					<th>Category Name</th>
					<th>Kpi Weight</th>
					<th>Status</th>
					<th data-priority="2" style="text-align:center;" width=100>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_content"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="contentForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Kpi</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Kpi Category Name</label>
								<div class="col-sm-8">
                                    <input type="hidden" name="id_kpi_category" id="id_kpi_category" class="form-control form-control-sm">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Weight</label>
                                <div class="col-sm-8">
                                    <input type="number" name="weight_kpi" id="weight_kpi" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="weight_kpiError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
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
						</div>
					</div>
                    
                </div>
                <div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="on_close_modal()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			<div style="display:none;">
                <table id="sample_table_attachment">
                    <tr id="">
                        <td>
							<span class="sn text-center" style="vertical-align:middle;"></span>
						</td>
                        <td>
								<input name="content_attachment[0][id_attachment_content]" id="content_attachment_0_id_attachment_content" type="hidden" class="form-control form-control-sm id_attachment_input">
                                <input name="content_attachment[0][attachment]" id="content_attachment_0_attachment" type="file" class="form-control form-control-sm attachment_input" style="height: 37px;">
                                <span class="invalid-feedback attachment_input_error" role="alert" id="content_attachment_0_attachmentError">
                                    <strong></strong>
                                </span>					
                        </td>
                        <td>
                                <select name="content_attachment[0][status_attachment]" id="content_attachment_0_status_attachment" class="form-control form-control-sm select2 status_attachment_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback status_attachment_input_error" role="alert" id="content_attachment_0_status_attachmentError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
							<center>
								<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
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

</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_kpi_category = "";
    let global_id_attachment = 0;
    let x = [];
    let today = new Date().toISOString().slice(0, 10)
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

    	$(document).on('click', '.new', function () {
            run_in_modal()
            global_id_kpi_category = "";
            $('.summernote').summernote('reset');
            $("#contentForm")[0].reset();
            $("#table_body").html("");
            $("#contentForm .modal-title").html("<span class='fas fa-plus'></span> Form Kpi");
            $(".invalid-feedback").children("strong").text("");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#contentForm input").removeClass("is-invalid");
    		$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');			

            $('#modal_form_content').modal('show');
        });
    		
    	$('#contentForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#contentForm')[0]);

            $(".invalid-feedback").children("strong").text("");
            $("#contentForm input").removeClass("is-invalid");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: {Accept: "application/json"},
                contentType:false,
                cache: false,
                processData:false,
    			url: global_id_kpi_category == '' ? "{{ route('master_kpi.save') }}" : "{{ route('master_kpi.update') }}",
                data: formData,
    			beforeSend: function () {
                    $('#overlay').show();
    			},
                success: function (response) {
                    $('#overlay').hide();

                    if (response.status == 'true') {
    					$('#modal_form_content').modal('hide');
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


    	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

        $(document).on('click', '.edit', function () {
            run_in_modal()

            let id_kpi_category = $(this).attr('id');
            global_id_kpi_category = id_kpi_category;
            $("#contentForm")[0].reset();
            $("#table_body").html("");
            $("#contentForm .modal-title").html("<span class='fas fa-edit'></span> Form Kpi");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#contentForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "{{ route('master_kpi.get_master_kpi') }}",
                method: "GET",
                data: {id_kpi_category: id_kpi_category},
    			beforeSend: function () {
                    $('#loader').removeClass('hidden');
    			},
                success: function (response) {
                    $('#loader').addClass('hidden')

                    global_id_attachment = 0;					
                    $('#id_kpi_category').val(response.id_kpi_category);
                    $('#description').val(response.description);
                    $('#weight_kpi').val(response.weight_kpi);
                    $('#status').val(response.status).trigger('change');
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
    	
            $('#modal_form_content').modal('show');
        });

        $('#content_type').on('change',function(){
            let type = $(this).val();
            switch_content(type);
        })

    });

    $(document).ready(function(){

        $('#content_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('master_kpi.index') }}",
    		    error: function (jqXHR, textStatus, errorThrown) {
    					$('#content_table').DataTable().ajax.reload();
    				}
    		  },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_kpi_category',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex'},
    			{ data: 'description'},
    			{ data: 'weight_kpi'},
    			{ data: 'status'},
    			{ data: 'action', name: 'action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let _edit = `<button type="button" id="${row.action}" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> `;
                        let _delete = `&nbsp;&nbsp;<button type="button" id="${row.action}" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>`;
                        return _edit + _delete;
                    } 
                },
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
    	
        $(document).on('click', '.delete', function (event) {
        	id_kpi_category = $(this).attr('id');
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
                        url: "{{ route('master_kpi.destroy') }}",
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        method: "POST",
                        data: {id_kpi_category: id_kpi_category},
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
    });

    function run_in_modal() {
        /**************** Load Menu dropdown **************************/
		$('#status').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });	
    }

    function on_close_modal() {
        $('#content_table').DataTable().ajax.reload();
    }

</script>
@endsection