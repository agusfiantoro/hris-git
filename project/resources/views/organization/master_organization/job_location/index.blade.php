@extends('adminlte::page')
@section('title', 'Job Location')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Job Location</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Job Location</button>
                </div>
            </div>

			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="location_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>
						<th></th>
						<th></th>
						<th>No</th>
						<th>Location Code</th>
						<th data-priority="2">Description</th>
						<th>Address Location</th>
						<th data-priority="3">Longitude</th>
						<th data-priority="4">Latitude</th>
						<th>Branch</th>
						<th>Company</th>
						<th>Status</th>
						<th data-priority="1" style="text-align:center;" width=150>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_location"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="locationForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Job Location</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Location Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="location_code" id="location_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="location_codeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                               <div class="col-sm-8">
                                    <select name="id_branch" id="branch" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="branchError">
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
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Address Location</label>
                                <div class="col-sm-8">
                                    <textarea name="address_location" id="address_location" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="address_locationError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6" style="margin-top:5px">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Longitude</label>
                                <div class="col-sm-8">
                                    <input type="text" name="longitude" id="longitude" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="longitudeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                             <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
									  <select id="select2status" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						 <div class="col-md-6" style="margin-top:5px">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Latitude</label>
                                <div class="col-sm-8">
                                    <input type="text" name="latitude" id="latitude" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="latitudeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                       <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
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
                                <label class="col-sm-4 col-form-label">Location Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="location_code" id="location_code_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="location_code_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                               <div class="col-sm-8">
                                    <select name="id_branch" id="branch_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="branch_editError">
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
                                <label class="col-sm-4 col-form-label">Inactive Date</label>
                                <div class="col-sm-8">
                                    <input name="inactive_date" id="datepicker_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="inactive_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <textarea name="description" id="description_edit" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="description_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Address Location</label>
                                <div class="col-sm-8">
                                    <textarea name="address_location" id="address_location_edit" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="address_location_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6" style="margin-top:5px">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Longitude</label>
                                <div class="col-sm-8">
                                    <input type="text" name="longitude" id="longitude_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="longitude_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                             <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
									  <select id="select2status_edit" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						 <div class="col-md-6" style="margin-top:5px">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Latitude</label>
                                <div class="col-sm-8">
                                    <input type="text" name="latitude" id="latitude_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="latitude_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                       <div class="col-md-6"></div>
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
<script>
	
function get_branch(){
	$.getJSON('<?= url('organization/master_organization/job_location/get_branch') ?>', function (data) {
            $('#branch').select2({
                data: data,
            });
			$('#branch_edit').select2({
                data: data,
            });

        }).fail(function (data) { // Call failed
            get_branch();
		});	
}
function get_company(){
	$.getJSON('<?= url('organization/master_organization/job_location/get_company') ?>', function (data) {
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
$('#select2status').select2();
$('#select2status_edit').select2();

		get_company();
		get_branch();
});

$(document).on('click', '.new', function () {
            $("#locationForm")[0].reset();
            $("#locationForm .modal-title").html("<span class='fas fa-plus'></span> Form Job Location");
            $(".invalid-feedback").children("strong").text("");
            $("#locationForm input").removeClass("is-invalid");
            $("#locationForm textarea").removeClass("is-invalid");
            $('#modal_form_location').modal('show');
        });

    $(function () {
        $('#locationForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#locationForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					url: "{{ route('location.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_location').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#location_table').DataTable().ajax.reload();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error Not Save]'
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
							 $("#location_code").addClass("is-invalid");
                              $("#location_codeError").children("strong").text('The Location Code has already been taken.');
                        } else {
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

$(document).ready(function(){

    $('#location_table').DataTable({
        processing: true,
		responsive: true,
		pageLength:50,
        ajax: {
		    url: "{{ route('location.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#location_table').DataTable().ajax.reload();
				}
		  },
        columns: [
		 {   // Detail Responsive
                    defaultContent: '',
                    orderable: false
                },
		{   // Checkbox select column
                data: 'id_location',
                defaultContent: '',
                orderable: false
            },
		{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'location_code', name: 'location_code' },
            { data: 'description', name: 'description' },
            { data: 'address_location', name: 'address_location' },
            { data: 'longitude', name: 'longitude' },
            { data: 'latitude', name: 'latitude' },
            { data: 'desc_branch', name: 'desc_branch' },
            { data: 'company_name', name: 'company_name' },
			{ data: 'status', name: 'status' },
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
   action_url = "{{ route('location.update') }}";
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
	$('#location_table').DataTable().ajax.reload();

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
			$("#location_code_edit").addClass("is-invalid");
            $("#location_code_editError").children("strong").text('The Location Code has already been taken.');
		}
	}
  });
 });

 $(document).on('click', '.edit', function(){
  var id_location = $(this).attr('id');
  $('#form_result').html('');
	 $(".invalid-feedback").children("strong").text("");
	$("#update_form input").removeClass("is-invalid");
	$("#update_form textarea").removeClass("is-invalid");

  $.ajax({
   url :"job_location/edit/"+id_location,
   dataType:"json",
   success:function(data)
   {
    $('#location_code_edit').val(data.result.location_code);
    $('#description_edit').val(data.result.description);
    $('#address_location_edit').val(data.result.address_location);
    $('#longitude_edit').val(data.result.longitude);
    $('#latitude_edit').val(data.result.latitude);
	$('#branch_edit').val(data.result.id_branch).trigger('change');
    $('#id_company_edit').val(data.result.id_company).trigger('change');
    $('#datepicker_edit').val(data.result.inactive_date);
    $('#hidden_id').val(id_location);
    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action_edit').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });


$(document).on('click', '.delete', function (event) {
	id_location = $(this).attr('id');
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
			   url:"job_location/destroy/"+id_location,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#location_table').DataTable().ajax.reload();
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
