@extends('adminlte::page')
@section('title', 'Master Overtime Type')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Overtime Type</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Overtime Type</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="overtime_type_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Overtime Code</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_overtime_type"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="overtime_typeForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Overtime Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Overtime Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="overtime_code" id="overtime_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="overtime_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Minimum Time</label>
                                <div class="col-sm-8">
                                    <input name="minimum_time" id="minimum_time" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="minimum_timeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Multiple Value</label>
                                <div class="col-sm-8">
                                    <input name="multiple_value" id="multiple_value" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="multiple_valueError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                           
                             <div class="row">
                                <label class="col-sm-4 col-form-label">Maximum Time</label>
                                <div class="col-sm-8">
                                    <input name="maximum_time" id="maximum_time" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="maximum_timeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
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
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="formModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="modal-header">
         <h4 class="modal-title"></h4>
         <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 <form id="update_form" class="form-horizontal" method="POST">
					@csrf
					<div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Overtime Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="overtime_code" id="overtime_code_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="overtime_code_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Minimum Time</label>
                                <div class="col-sm-8">
                                    <input name="minimum_time" id="minimum_time_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="minimum_time_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Multiple Value</label>
                                <div class="col-sm-8">
                                    <input name="multiple_value" id="multiple_value_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="multiple_value_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="description_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                           
                             <div class="row">
                                <label class="col-sm-4 col-form-label">Maximum Time</label>
                                <div class="col-sm-8">
                                    <input name="maximum_time" id="maximum_time_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="maximum_time_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="select2status_edit" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
         
                    </div>

					<div class="modal-footer">
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action_edit" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <button type="submit" name="action_button" id="action_button" class="btn btn-primary" value="edit"><i class="fas fa-edit"></i> Update</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
					
				</form>   	 
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

@endsection

@section('scripts')
<script type="text/javascript">
let global_id_overtime_type = "";
function get_company(){
	 $.getJSON('<?= url('time_attendance/overtime/overtime_type/get_company') ?>', function (data) {
        $('#company').select2({
            data: data,
            disabled: true
        });
        $('#company_edit').select2({
            data: data,
            disabled: true
        });
    }).fail(function (data) { // Call failed
            get_company();
    });
        
}	
$(function () {
	$('#datepicker').datepicker({
				uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
			});	
	$('#datepicker_edit').datepicker({
				uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
			});	
	$('#minimum_time').timepicker({
				uiLibrary: 'bootstrap4',
				format: 'HH:MM',
				mode: '24hr'
			});	
	$('#maximum_time').timepicker({
				uiLibrary: 'bootstrap4',
				format: 'HH:MM',
				mode: '24hr'
			});	
	$('#minimum_time_edit').timepicker({
				uiLibrary: 'bootstrap4',
				format: 'HH:MM',
				mode: '24hr'
			});	
	$('#maximum_time_edit').timepicker({
				uiLibrary: 'bootstrap4',
				format: 'HH:MM',
				mode: '24hr'
			});	
	$('#select2status').select2();	
	$('#select2status_edit').select2();
		get_company();
    });

    $(document).on('click', '.new', function () {
	
        global_id_overtime_type = "";
        $("#overtime_typeForm")[0].reset();
        $("#overtime_typeForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Overtime Type");
        $(".invalid-feedback").children("strong").text("");
        $("#overtime_typeForm input").removeClass("is-invalid");
        $("#overtime_typeForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_overtime_type').modal('show');
    });
	
	 $(function () {
        $('#overtime_typeForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#overtime_typeForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					url: "{{ route('overtime_type.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_overtime_type').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#overtime_type_table').DataTable().ajax.reload();
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
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        }
						else if (response.status === 500) {
							 $("#overtime_code").addClass("is-invalid");
                              $("#overtime_codeError").children("strong").text('The Overtime Code has already been taken.');
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

    });


    $(document).ready(function () {
        $('#overtime_type_table').DataTable({
            processing: true,
         //   serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('time_attendance/overtime/overtime_type/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#overtime_type_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {   // Checkbox select column
					data: 'id_overtime_type',
					defaultContent: '',
					orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'overtime_code', name: 'overtime_code'},
                {data: 'description', name: 'description'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
						
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
		
	$('#update_form').on('submit', function(event){
	  event.preventDefault();
	  var action_url = '';

	  if($('#action_edit').val() == 'Edit')
	  {
	   action_url = "{{ route('overtime_type.update') }}";
	  }
	  $(".invalid-feedback").children("strong").text("");
	  $("#update_form input").removeClass("is-invalid");
	   $.ajax({
	   url: action_url,
	   method:"POST",
	   data:$(this).serialize(),
	   dataType:"json",
	   success:function(data)
	   {
		if(data.success)
		{
		swal({
			icon: 'success',
			title: 'Success',
			text: data.success
		});
		$('#overtime_type_table').DataTable().ajax.reload();

		}
		$('#formModal').modal('hide');
	   },
		error: function (response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#" + key + "_edit").addClass("is-invalid");
					$("#" + key + "_editError").children("strong").text(errors[key][0]);
				});
			} 
			else if (response.status === 500) {
				$("#overtime_code_edit").addClass("is-invalid");
				$("#overtime_code_editError").children("strong").text('The Overtime Code has already been taken.');
			}
		}
	  });
	 });

	$(document).on('click', '.edit', function(){
	  var id_overtime_type = $(this).attr('id');
	  $('#form_result').html('');
	  $.ajax({
	   url :"overtime_type/edit/"+id_overtime_type,
	   dataType:"json",
	   success:function(data)
	   {
		$('#overtime_code_edit').val(data.result.overtime_code);
		$('#minimum_time_edit').val(data.result.minimum_time.slice(0, 5));
		$('#maximum_time_edit').val(data.result.maximum_time.slice(0, 5));
		$('#multiple_value_edit').val(data.result.multiple_value);
		$('#description_edit').val(data.result.description);
		$('#id_company_edit').val(data.result.id_company);
		$('#datepicker_edit').val(data.result.inactive_date);
		$('#hidden_id').val(id_overtime_type);
		$('.modal-title').text('Edit Record');
		$('#action_button').val('Edit');
		$('#action_edit').val('Edit');
		$('#formModal').modal('show');
	   }
	  })
	 });

	$(document).on('click', '.delete', function (event) {
		id_overtime_type = $(this).attr('id');
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
		}).then(function(value) {
			if (value) {
				$.ajax({
				   url:"overtime_type/destroy/"+id_overtime_type,
				   success:function(data)
				   {
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 $('#overtime_type_table').DataTable().ajax.reload();				 
					 swal({
						title: "Data Deleted!",
						  icon: "success",
						   buttons: {confirm : {className:'btn-success'},},
						}).then(ok => {
					});
					}, 50);
				   }
				  })
			}
		});
	});

	
    });
</script>
@endsection