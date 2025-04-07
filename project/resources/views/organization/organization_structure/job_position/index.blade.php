@extends('adminlte::page')

@section('title', 'Job Position')
@section('plugins.Select2', true)

@section('content_header')
<!-- div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Job Position</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">Organization Structure</li>
            <li class="breadcrumb-item">Organization Structure</li>
            <li class="breadcrumb-item active">Job Position</li>
        </ol>
    </div>
</div -->
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">Form Job Position</h3>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Position</button>
                </div>
            </div>
            <div class="card-body">
				<!-- div class="clearfix">				
					<form id="job_positionImport" method="post">
						{{csrf_field()}}
						<div class="input-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" name="imported_file"/>
								<label class="custom-file-label">Choose file</label>	
							</div>
							<button style="margin-left: 10px;" class="btn btn-info" type="submit">Import</button>
							<div class="progress">
									<div class="bar"></div >
									<div class="percent">0%</div >
								</div>
						</div>
					</form>
				
				</div -->
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                        <table id="table_job_position" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Job Position</th>
									<th>Department</th>
									<th>Superior Position</th>
									<th>Company</th>
                                    <th>Status</th>
                                    <th style="width:100px;">Action</th>
                                </tr>
                            </thead>
                        </table>
                   			
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_form_job_position"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="job_positionForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Job Position</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Job Position</label>
                                <div class="col-sm-8">
                                    <input type="text" name="job_position" id="job_position" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="job_positionError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
                                <div class="col-sm-8">
                                    <select name="company" id="company" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <select name="department" id="department" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="departmentError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Superior Position</label>
                                <div class="col-sm-8">
                                    <select name="superior_position" id="superior_position" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="superior_positionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <label class="col-sm-12 col-form-label">Job Description</label>
                                <div class="col-sm-12">
                                    <textarea name="job_description" id="job_description" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="job_descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="margin-top: 10px">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
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

@stop

@section('css')
<style type="text/css">
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
	.progress { position:relative; width:100%;height:20px;font-size:14px; }
        .bar { background-color: #33bf53; width:0%; height:30px; }
        .percent { position:absolute; display:inline-block; left:45%; color: #040608;}
		
	
</style>
@stop

@section('scripts')
<script type="text/javascript">

    let global_id_position = "";
	
	$(".custom-file-input").on("change", function() {
	  var fileName = $(this).val().split("\\").pop();
	  $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
	 $(function () {
        $('#job_positionImport').submit(function (e) {
            e.preventDefault();
        //    let formData = $(this).serializeArray();
			var formData = new FormData(this);
			var bar = $('.bar');
			var percent = $('.percent');
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
                    url: '<?= url('organization/organization_structure/job_position/import') ?>',
                    data: formData,
					 beforeSend: function() {
						var percentVal = 'Waiting Import...<i class="fas fa-sync-alt fa-spin"></i>';
						bar.width(percentVal)
						percent.html(percentVal);
					},					      
                    success: function (response) {
                        if (response.status == 'true') {
							var percentVal = '100%';
							bar.width(percentVal)
							percent.html(percentVal);
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        refresh_data();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    },
					
					error: function () {                      
							var percentVal = 'Error';
							bar.width(percentVal)
							percent.html(percentVal);
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                    }
                });
            
        });

    });


    $(function () {
        $('#job_positionForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#job_positionForm input").removeClass("is-invalid");
            $("#job_positionForm textarea").removeClass("is-invalid");
            if (global_id_position == "") {
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
                    url: '<?= url('organization/organization_structure/job_position/save_position') ?>',
                    data: formData,
                    success: function (response) {
                        if (response.status == 'false') {
                            swal({
                                icon: "error",
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + response.message + ']'
                            });
                        } else if (response.status == 'true') {
                            $('#modal_form_job_position').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            refresh_data();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                            });
                        }
                    }
                });
            }else{
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
                    url: '<?= url('organization/organization_structure/job_position/save_position?id_position=') ?>' + global_id_position,
                    data: formData,
                    success: function (response) {
                        if (response.status == 'false') {
                            swal({
                                icon: "error",
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + response.message + ']'
                            });
                        } else if (response.status == 'true') {
                            $('#modal_form_job_position').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                            refresh_data();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                            });
                        }
                    }
                });
            }
        });

        $(document).on('click', '.new', function () {
            global_id_position = "";
            $("#job_positionForm")[0].reset();
            $("#job_positionForm .modal-title").html("<span class='fas fa-plus'></span> Form Job Position");
            $(".invalid-feedback").children("strong").text("");
            $("#job_positionForm input").removeClass("is-invalid");
            $("#job_positionForm textarea").removeClass("is-invalid");
            $('#save_button').attr('class','btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            $('#modal_form_job_position').modal('show');
        });

        $(document).on('click', '.edit', function () {
            let id_position = $(this).attr('id');
            global_id_position = id_position;
            $("#job_positionForm")[0].reset();
            $("#job_positionForm .modal-title").html("<span class='fas fa-edit'></span> Edit Job Position");
            $(".invalid-feedback").children("strong").text("");
            $("#job_positionForm input").removeClass("is-invalid");
            $("#job_positionForm textarea").removeClass("is-invalid");
            $('#save_button').attr('class','btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');
            $.ajax({
                url: "<?= url('organization/organization_structure/job_position/get_detail_position') ?>",
                method: "GET",
                data: {id_position: id_position},
                success: function (response) {
                    $('#job_position').val(response.description);
                    $('#company').val(response.id_company).trigger('change');
                    $('#department').val(response.id_dept).trigger('change');
                    $('#superior_position').val(response.parent_id_position).trigger('change');
                    $('#job_description').val(response.job_description);
                    $('#status').val(response.status);
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
            $('#modal_form_job_position').modal('show');
        });

        $(document).on('click', '.delete', function (event) {
            let id_position = $(this).attr('id');
            event.preventDefault();
            swal({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanantly deleted!',
                icon: 'warning',
                buttons: true,
                dangerMode: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then(function (value) {
                if (value) {
                    $.ajax({
                        url: "<?= url('organization/organization_structure/job_position/destroy_position') ?>",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id_position: id_position},
                        success: function (response) {
                            if (response.status == 'false') {
                                swal({
                                    icon: "error",
                                    title: 'Oops...',
                                    dangerMode: true,
                                    text: 'Something went wrong! [' + response.message + ']'
                                });
                            } else if (response.status == 'true') {
                                $('#modal_form_job_position').modal('hide');
                                swal({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message
                                });
                                refresh_data();
                            } else {
                                swal({
                                    icon: 'error',
                                    title: 'Oops...',
                                    dangerMode: true,
                                    text: 'Something went wrong! [Unknown Error]'
                                });
                            }
                        },
                        error: function (xhr) {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                            });
                        }
                    })
                }
            });
        });

    });
    $(document).ready(function () {
		     
		  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
		  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
		  let deleteButton = {
			text: deleteButtonTrans,
			url: '<?= url('organization/organization_structure/job_position/mass_destroy') ?>',
			className: 'btn-danger',
			action: function (e, dt, node, config) {
			  var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
				  return $(entry).data('entry-id')
			  });
				
			if (ids.length === 0) {
				alert('{{ trans('global.datatables.zero_selected') }}')

				return
			  }
		   
			  if (confirm('{{ trans('global.areYouSure') }}')) {
				$.ajax({
					method: 'POST',
					url: config.url,
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: { ids: ids},
					beforeSend: function() {
						$("#overlay").fadeIn();
					},
					success:function(data)
					   {
						setTimeout(function(){
						 $('#confirmModal').modal('hide');
						 $('#table_job_position').DataTable().ajax.reload();				 
						 swal({
							title: "Data Delete Selected!",
							  icon: "success",
							   buttons: {confirm : {className:'btn-success'},},
							}).then(ok => {
						});
						}, 100);
					   },
					   complete: function(){
						$("#overlay").fadeOut();
					},
					   })
			  }
			}
		  }
		
		let statusButton = {
			text: 'Change Status',
			url: '<?= url('organization/organization_structure/job_position/mass_inactive') ?>',
			className: 'btn btn-dark',
			action: function (e, dt, node, config) {
			  var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
				  return $(entry).data('entry-id')
			  });
				
			if (ids.length === 0) {
				alert('{{ trans('global.datatables.zero_selected') }}')

				return
			  }
		   
			  if (confirm('{{ trans('global.areYouSure') }}')) {
				$.ajax({
					method: 'POST',
					url: config.url,
					headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
					data: { ids: ids},
					beforeSend: function() {
						$("#overlay").fadeIn();
					},
					success:function(data)
					   {
						setTimeout(function(){
						 $('#confirmModal').modal('hide');
						 $('#table_job_position').DataTable().ajax.reload();				 
						 swal({
							title: "Data Update Status Selected!",
							  icon: "success",
							   buttons: {confirm : {className:'btn-success'},},
							}).then(ok => {
						});
						}, 100);
					   },
					complete: function(){
						$("#overlay").fadeOut();
					},
				})
			  }
			}
		  }
		
		dtButtons.push(statusButton)
		dtButtons.push(deleteButton)
		
      $('#table_job_position').DataTable({
			buttons: dtButtons,
            processing: true,
        //    serverSide: true,			
			scrollX: true,
			ajax: {
			   url: '<?= url('organization/organization_structure/job_position/get_data') ?>',
			   error: function (jqXHR, textStatus, errorThrown) {
						$('#table_job_position').DataTable().ajax.reload();
					}
		  },
       //     ajax: '<?= url('organization/organization_structure/job_position/get_data') ?>',
			createdRow: function( row, data, dataIndex ) {
			  $(row).attr('data-entry-id', data['id_position']);
		  },
            columns: [
			{   // Checkbox select column
                data: 'id_position',
                defaultContent: '',
                orderable: false
            },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'description', name: 'description',width:'150'},
				{data: 'department', name: 'department',width:'100'},
				{data: 'superior_position', name: 'superior_position'},
				{data: 'company_name', name: 'company_name'},
                {data: 'status', name: 'status'},
				{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {
					if(access_create == 0){
						$('.new').css('display', 'none');
					}	
					if(access_edit == 0){
						$('.edit').css('display', 'none');
					}		
					if(access_delete == 0){
						$('.delete').css('display', 'none');
					}
					if(access_print == 0){
						$('.print').css('display', 'none');
					}										
					return data;
				} 
			},
            ]
        })
		
		
		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
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
		
        refresh_data();
    });

    function refresh_data() {        
		get_company();
		get_department();
		get_superior_position();       
        $('#table_job_position').DataTable().ajax.reload();
    }
	
	function get_superior_position(){
		 $.getJSON('<?= url('organization/organization_structure/job_position/get_superior_position') ?>', function (data) {
            $('#superior_position').select2({
                data: data
            });
        }).fail(function (data) { // Call failed
            get_superior_position();
		});
	}
	function get_department(){
		$.getJSON('<?= url('organization/organization_structure/job_position/get_department') ?>', function (data) {
            $('#department').select2({
                data: data
            });
        }).fail(function (data) { // Call failed
            get_department();
		});
	}
	function get_company(){
		$.getJSON('<?= url('organization/organization_structure/job_position/get_company') ?>', function (data) {
            $('#company').select2({
                data: data
            });
        }).fail(function (data) { // Call failed
            get_company();
		});
	}

</script>
@stop