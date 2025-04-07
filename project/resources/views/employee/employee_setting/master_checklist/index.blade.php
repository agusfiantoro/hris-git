@extends('adminlte::page')
@section('title', 'Master Checklist')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Checklist</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Checklist</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="checklist_table"  style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Checklist Type</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_checklist"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_checklistForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Checklist</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Checklist Type</label>
                                <div class="col-sm-8">
									<input name="id_checklist" id="id_checklist" type="hidden">
                                    <select name="checklist_type" id="checklist_type" class="form-control form-control-sm">
                                        <option value="Onboarding">On Boarding</option>
                                        <option value="Offboarding">Off Boarding</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="checklist_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="document_name" id="document_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="document_nameError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
@endsection


@section('scripts')
<script type="text/javascript">
    let global_id_checklist = "";
    
    $(document).on('click', '.new', function () {
        global_id_checklist = "";
        $("#master_checklistForm")[0].reset();
        $("#master_checklistForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Checklist");
        $(".invalid-feedback").children("strong").text("");
        $("#master_checklistForm input").removeClass("is-invalid");
        $("#master_checklistForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_master_checklist').modal('show');
    });

    $(document).on('click', '.edit', function () {
        let id_checklist = $(this).attr('id');
        global_id_checklist = id_checklist;
        $("#master_checklistForm")[0].reset();
        $("#master_checklistForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Checklist");
        $(".invalid-feedback").children("strong").text("");
        $("#master_checklistForm input").removeClass("is-invalid");
        $("#master_checklistForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');
        
        $.ajax({
            url: "<?= url('employee/employee_setting/master_checklist/get_detail_master_checklist') ?>",
            method: "GET",
            data: {id_checklist: id_checklist},
            success: function (response) {
				$('#id_checklist').val(response.id_checklist).trigger('change');
                $('#checklist_type').val(response.checklist_type).trigger('change');
                $('#document_name').val(response.document_name).trigger('change');
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
        $('#modal_form_master_checklist').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
        let id_checklist = $(this).attr('id');
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
                $.ajax({
                    url: "<?= url('employee/employee_setting/master_checklist/destroy') ?>",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id_checklist: id_checklist},
                    success: function (response) {
                        setTimeout(function(){
						 $('#confirmModal').modal('hide');
						 $('#checklist_table').DataTable().ajax.reload();				 
						 swal({
							title: "Data Deleted!",
							  icon: "success",
							   buttons: {confirm : {className:'btn-success'},},
							}).then(ok => {
								location.reload();
							});
						}, 50);
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

        });
    });

    $(document).ready(function () {
		$('#checklist_type').select2({width:'100%'});
		$('#status').select2({width:'100%'});
        $('#checklist_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('employee/employee_setting/master_checklist/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#checklist_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
					defaultContent: '',
					orderable: false,
				},
				{   // Checkbox select column
					data: 'id_checklist',
					defaultContent: '',
					orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'checklist_type', name: 'checklist_type'},
                {data: 'document_name', name: 'document_name'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
                        return data;
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

        $('#master_checklistForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#master_checklistForm input").removeClass("is-invalid");
            $("#master_checklistForm textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
				url: global_id_checklist == '' ? "{{ route('checklist.save') }}" : "{{ route('checklist.update') }}",
                data: formData,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                   if (response.status == 'true') {
                        $('#modal_form_master_checklist').modal('hide');
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
				complete: function(){
					$('#loader').addClass('hidden')
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
        });
    });

    function refresh_data() {
        $('#checklist_table').DataTable().ajax.reload();
    }
</script>
@endsection