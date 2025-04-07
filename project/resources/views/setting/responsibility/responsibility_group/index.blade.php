@extends('adminlte::page')
@section('title', 'Responsibility Group')


@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
   <div class="container" style="text-align: center">
		
      </div>
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Responsibility Group</h5>
                <div class="card-tools">

                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Responsibility Group</button>
                </div>
            </div>
     
			 <div class="card-body">
			 		<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>		
					<table id="responsibility_group_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>   
						<th></th>
						<th>No</th>
						<th>Responsibility Menu</th>
						<th>Sequence</th>
						<th>Company</th>
						<th>Status</th>
						<th>Icon</th>
						<th>Inactive Date</th>
						<th>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_responsibility_group"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="responsibility_groupForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Responsibility Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Responsibility Menu</label>
                                <div class="col-sm-8">
                                    <input type="text" name="responsibility_name" id="responsibility_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="responsibility_nameError">
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
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-8">
                                    <input type="text" name="sequence" id="sequence" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="sequenceError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Icon</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                    <input name="icon" id="icon" data-placement="bottom" class="form-control form-control-sm icp icp-auto" type="text" />
									<div class="input-group-append">
										<span class="input-group-text input-group-addon form-control-sm"></span>
									</div>
									</div>
                                    <span class="invalid-feedback" role="alert" id="iconError">
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
  <div class="modal-dialog modal-xl">
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
                                <label class="col-sm-4 col-form-label">Responsibilit Menu</label>
                                <div class="col-sm-8">
                                    <input type="text" name="responsibility_name" id="responsibility_name_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="responsibility_name_editError">
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
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-8">
                                    <input type="text" name="sequence" id="sequence_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="sequence_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Icon</label>
                                <div class="col-sm-8">
								 <div class="input-group">
                                    <input name="icon" id="icon_edit" data-placement="bottom" class="form-control form-control-sm icp icp-auto" type="text" />
									<div class="input-group-append">
										<span class="input-group-text input-group-addon form-control-sm"></span>
									</div>
								</div>
                                    <span class="invalid-feedback" role="alert" id="icon_editError">
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
function get_company(){
        $.getJSON('<?= url('setting/responsibility/responsibility_group/get_company') ?>', function (data) {
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
	 $('.icp-auto').iconpicker({
		 hideOnSelect: true,
	 });

 $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#datepicker_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });			
$('#select2status').select2();	
        get_company();
    });
	
$(document).on('click', '.new', function () {
            $("#responsibility_groupForm")[0].reset();
            $("#responsibility_groupForm .modal-title").html("<span class='fas fa-plus'></span> Form Responsibility Group");
            $(".invalid-feedback").children("strong").text("");
            $("#responsibility_groupForm input").removeClass("is-invalid");
            $("#responsibility_groupForm textarea").removeClass("is-invalid");
            $('#modal_form_responsibility_group').modal('show');
        });

    $(function () {
        $('#responsibility_groupForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#responsibility_groupForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					url: "{{ route('responsibility_group.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_responsibility_group').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#responsibility_group_table').DataTable().ajax.reload();
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

		

$(document).ready(function(){

    $('#responsibility_group_table').DataTable({
        processing: true,
     //   serverSide: true,
		scrollY:    true,        
        ajax: {
		   url: "{{ route('responsibility_group.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
						$('#responsibility_group_table').DataTable().ajax.reload();
					}
		  },
        columns: [
		{   // Checkbox select column
                data: null,
                defaultContent: '',
                orderable: false
            },
		{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'responsibility_name', name: 'responsibility_name' },
            { data: 'sequence', name: 'sequence' },
            { data: 'company_name', name: 'company_name' },
			{ data: 'status', name: 'status' },
			{ data: 'icon', name: 'icon', render: function ( data, type, row ) {
                        return '<center><span class="'+ data +' fa-lg"></span></center>';
                    } },
			{ data: 'inactive_date', name: 'inactive_date' },
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
   action_url = "{{ route('responsibility_group.update') }}";
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
	$('#responsibility_group_table').DataTable().ajax.reload();

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
			$("#region_code_edit").addClass("is-invalid");
            $("#region_code_editError").children("strong").text('The Branch Code has already been taken.');
		}
	}
  });
 });

 $(document).on('click', '.edit', function(){
  var id_responsibility_menu = $(this).attr('id');
  $('#form_result').html('');
  $.ajax({
   url :"responsibility_group/edit/"+id_responsibility_menu,
   dataType:"json",
   success:function(data)
   {
    $('#responsibility_name_edit').val(data.result.responsibility_name);
    $('#sequence_edit').val(data.result.sequence);
    $('#icon_edit').val(data.result.icon);
    $('#id_company_edit').val(data.result.id_company);
    $('#datepicker_edit').val(data.result.inactive_date);
	$('#select2status_edit').select2().val(data.result.status).trigger('change');
    $('#hidden_id').val(id_responsibility_menu);
    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action_edit').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });
 

$(document).on('click', '.delete', function (event) {
	id_responsibility_menu = $(this).attr('id');
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
			   url:"responsibility_group/destroy/"+id_responsibility_menu,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#responsibility_group_table').DataTable().ajax.reload();				 
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