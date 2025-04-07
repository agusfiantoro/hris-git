@extends('adminlte::page')
@section('title', 'Responsibility Menu')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Responsibility Menu</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Responsibility Menu</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="responsibility_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th>No</th>
						<th>Sequence</th>
						<th>Responsibility Menu</th>
						<th>Responsibility Name</th>
						<th>Address Menu</th>
						<th>Status</th>
						<th>Inactive Date</th>
						<th style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_responsibility"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="responsibilityForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Responsibility Menu</h5>
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
									<div class="input-group">
									<input name="id_responsibility" id="id_responsibility" type="hidden">		
									<input name="id_responsibility_menu" id="id_responsibility_menu" type="hidden">		
									<input name="responsibility_menu" id="responsibility_menu" type="text" onclick="browse_table()" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;">	
				                    <button type="button" onclick="browse_table()" class="input-group-text far fa-list-alt form-control-sm" style="border-radius: 0px 5px 5px 0px;"></button>
									<span class="invalid-feedback" role="alert" id="id_responsibility_menuError">
                                        <strong></strong>
                                    </span>	
									</div>
									
									
								</div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Responsibility Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="responsibility_name" id="responsibility_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="responsibility_nameError">
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
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-8">
                                    <input type="text" name="sequence" id="sequence" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="sequenceError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
						</div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_menu_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">Menu <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_menu_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_menu_detail"><span class="fas fa-plus"></span> Add Menu</button>
                                        </div>
                                        <div class="col-md-12" style="overflow-y: scroll">
                                            <table id="table_menu_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Menu Name</th>
                                                        <th style="white-space:nowrap;">Address Name</th>
                                                        <th style="white-space:nowrap;">Def User</th>
                                                        <th style="white-space:nowrap;">Def Manager</th>
                                                        <th style="white-space:nowrap;">Def Admin</th>
                                                        <th style="white-space:nowrap;">Status</th>                                                       
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_menu_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_menu_detailError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
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
			     <div style="display:none;">
                <table id="sample_table_menu">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td>
								<input name="responsibility[0][id_menu]" id="responsibility_0_id_menu" type="hidden" class="form-control form-control-sm id_menu_input">		
                                <input type="text" name="responsibility[0][menu_name]" id="responsibility_0_menu_name" class="form-control form-control-sm menu_name_input">
                                <span class="invalid-feedback menu_name_input_error" role="alert" id="responsibility_0_menu_nameError">
                                    <strong></strong>
                                </span>
						
                        </td>
                        <td>
                                <select name="responsibility[0][address_menu]" id="responsibility_0_address_menu" class="form-control form-control-sm select2 address_menu_input" style="width: 100%;"></select>
                                <span class="invalid-feedback address_menu_input_error" role="alert" id="responsibility_0_address_menuError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility[0][default_user]" id="responsibility_0_default_user" class="form-control form-control-sm default_user_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback default_user_input_error" role="alert" id="responsibility_0_default_userError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility[0][default_manager]" id="responsibility_0_default_manager" class="form-control form-control-sm default_manager_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback default_manager_input_error" role="alert" id="responsibility_0_default_managerError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility[0][default_administrator]" id="responsibility_0_default_administrator" class="form-control form-control-sm default_administrator_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback default_administrator_input_error" role="alert" id="responsibility_0_default_administratorError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
								 <select name="responsibility[0][status]" id="responsibility_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="responsibility_0_statusError">
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


<div id="browseModal" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="card-body">
					<table align="center" id="bro_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr align="center">
					   <th>No.</th>
					   <th>Responsibility Menu</th>
					   <th>Sequence</th>
					   <th>Company</th>
					   <th>Icon</th>
						<th>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
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

@endsection
@section('scripts')
<script type="text/javascript">
let global_id_responsibility = "";
let global_id_menu = 0;
let global_address_menu = [];

    $(function () {	
			
		$(document).on('click', '.new', function () {
            global_id_responsibility = "";
            $("#responsibilityForm")[0].reset();
            $("#table_menu_body").html("");
            $("#responsibilityForm .modal-title").html("<span class='fas fa-plus'></span> Form Responsibility Menu");
            $(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#responsibilityForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            $('#modal_form_responsibility').modal('show');
        });
		
		 $('#responsibilityForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#responsibilityForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_responsibility == '' ? "{{ route('responsibility.save') }}" : "{{ route('responsibility.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_responsibility').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#responsibility_table').DataTable().ajax.reload();
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
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_menu_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
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


        $(document).on('click', '#new_menu_detail', function () {
            var content = jQuery('#sample_table_menu tr'),
                    size = global_id_menu++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_menu_input').attr('id', 'responsibility_' + size + '_id_menu');
            element.find('.id_menu_input').attr('name', 'responsibility[' + size + '][id_menu]');
			
			element.find('.menu_name_input').attr('id', 'responsibility_' + size + '_menu_name');
            element.find('.menu_name_input').attr('name', 'responsibility[' + size + '][menu_name]');
            element.find('.menu_name_input_error').attr('id', 'responsibility_' + size + '_menu_nameError');

			element.find('.address_menu_input').attr('id', 'responsibility_' + size + '_address_menu');
            element.find('.address_menu_input').attr('name', 'responsibility[' + size + '][address_menu]');
            element.find('.address_menu_input_error').attr('id', 'responsibility_' + size + '_address_menuError');
            element.find('.address_menu_input').select2({
                placeholder: "Select Address Name",
                allowClear: true,
                data: global_address_menu
            });
            element.find('.address_menu_input').val('').trigger('change');
			
			element.find('.default_user_input').attr('id', 'responsibility_' + size + '_default_user');
            element.find('.default_user_input').attr('name', 'responsibility[' + size + '][default_user]');
            element.find('.default_user_input_error').attr('id', 'responsibility_' + size + '_default_userError');
			
			element.find('.default_manager_input').attr('id', 'responsibility_' + size + '_default_manager');
            element.find('.default_manager_input').attr('name', 'responsibility[' + size + '][default_manager]');
            element.find('.default_manager_input_error').attr('id', 'responsibility_' + size + '_default_managerError');
			
			element.find('.default_administrator_input').attr('id', 'responsibility_' + size + '_default_administrator');
            element.find('.default_administrator_input').attr('name', 'responsibility[' + size + '][default_administrator]');
            element.find('.default_administrator_input_error').attr('id', 'responsibility_' + size + '_default_administratorError');

            element.find('.status_input').attr('id', 'responsibility_' + size + '_status');
            element.find('.status_input').attr('name', 'responsibility[' + size + '][status]');
            element.find('.status_input_error').attr('id', 'responsibility_' + size + '_statusError');
            element.find('.status_input').select2();
			
            element.appendTo('#table_menu_body');
			 $('#table_menu_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_menu_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

  $(document).on('click', '.edit', function () {
            let id_responsibility = $(this).attr('id');
            global_id_responsibility = id_responsibility;
            $("#responsibilityForm")[0].reset();
            $("#table_menu_body").html("");
            $("#responsibilityForm .modal-title").html("<span class='fas fa-edit'></span> Edit Responsibility Menu");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#responsibilityForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "<?= url('setting/responsibility/responsibility_menu/get_responsibility') ?>",
                method: "GET",
                data: {id_responsibility: id_responsibility},
                success: function (response) {
                    global_id_menu = 0;
					 $.each(response.menu, function (i, item) {
                        $('#new_menu_detail').trigger('click');
                    });
                    
                    $('#id_responsibility').val(response.id_responsibility).trigger('change');
                    $('#id_responsibility_menu').val(response.id_responsibility_menu).trigger('change');
                    $('#responsibility_menu').val(response.responsibility_menu).trigger('change');
                    $('#responsibility_name').val(response.responsibility_name).trigger('change');
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#company_name').val(response.company_name).trigger('change');
                    $('#sequence').val(response.sequence).trigger('change');

                    setTimeout(function () {
                        $('#table_menu_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_menu_input').val(response.menu[index].id_menu);
                            $(this).find('.menu_name_input').val(response.menu[index].menu_name);
                            $(this).find('.address_menu_input').val(response.menu[index].address_menu).trigger('change');
							if (response.menu[index].default_user == 1) {
                                $(this).find('.default_user_input').prop('checked', true);
                            } else {
                                $(this).find('.default_user_input').prop('checked', false);
                            }
							if (response.menu[index].default_manager == 1) {
                                $(this).find('.default_manager_input').prop('checked', true);
                            } else {
                                $(this).find('.default_manager_input').prop('checked', false);
                            }
							if (response.menu[index].default_administrator == 1) {
                                $(this).find('.default_administrator_input').prop('checked', true);
                            } else {
                                $(this).find('.default_administrator_input').prop('checked', false);
                            }
                            $(this).find('.status_input').val(response.menu[index].status).trigger('change');
                            
                        });                       
                    }, 500);
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

            $('#modal_form_responsibility').modal('show');
        });

    });


		
function check(id_responsibility_menu) {
	
		//  var id = $(this).attr('id');
		  console.log(id_responsibility_menu);
		  $.ajax({
		   url :"responsibility_menu/checkid/"+id_responsibility_menu,
		   dataType:"json",
		   success:function(data)
		   {

			$("#id_responsibility_menu").val(data.result.id_responsibility_menu);
			$("#responsibility_menu").val(data.result.responsibility_name);
			$("#id_company").val(data.result.id_company);
			$("#company_name").val(data.com.company_name);
			$("#browseModal").modal('hide');
		   }
		  })
}

function browse_table() {
	$('#browseModal').modal('show');
	$("#bro_table").DataTable({
		destroy: true,
		scrollY: true,
		pageLength: 10,		
          columns : [
            { data : 'no' ,className: 'false'},
            { data : 'responsibility_name' },
            { data : 'sequence' },
            { data : 'company_name' },
            { data : 'icon' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
            url: "{{ route('responsibility.browse') }}",
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'no'          : '<center>'+no+'</center>',
                  'responsibility_name'   : '<center>'+json[i]['responsibility_name']+'</center>',
                  'sequence'   : '<center>'+json[i]['sequence']+'</center>',
                  'company_name'   : '<center>'+json[i]['company_name']+'</center>',
                  'icon'   : '<center>'+json[i]['icon']+'</center>',
                  'action'   : '<center><button type="button" name="check" id="'+json[i]['id_responsibility_menu']+'" class="btn btn-success btn-sm" title="Check" onClick="check('+json[i]['id_responsibility_menu']+')"><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
      });

}

$(document).ready(function(){
	
    $('#responsibility_table').DataTable({
        processing: true,
    //    serverSide: true,
	/*	fixedColumns: {
            leftColumns: 4,
            rightColumns: 1,
        },
	*/
    //    scrollX: true, 
        ajax: {
		   url: "{{ route('responsibility.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
						$('#responsibility_table').DataTable().ajax.reload();
					}
		  },
        columns: [
			{   // Checkbox select column
                data: null,
                defaultContent: '',
                orderable: false
            },
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'sequence', name: 'sequence' },
            { data: 'responsibility_menu', name: 'responsibility_menu' },
            { data: 'responsibility_name', name: 'responsibility_name' },
            { data: 'address_menu', name: 'address_menu' },
            { data: 'status', name: 'status' },
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
	
	refresh_data();
	
$('.browse_table_edit').click(function(){   
  $('#bro_table').DataTable({
	destroy: true,
	scrollY: true,
	pageLength: 10,
	columns: [
	{ data : 'no' ,className: 'false'},
    { data : 'description'},
    { data : 'action' },
		],
   ajax: {
            type: 'GET',
            url: "{{ route('responsibility.browse') }}",
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'no'          : '<center>'+no+'</center>',
                  'description'   : '<center>'+json[i]['description']+'</center>',
                  'action'   : '<center><button type="button" name="check_edit" id="'+json[i]['id_currency']+'" class="check_edit btn btn-success btn-sm" title="check_edit" ><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
 });

$('#browseModal').modal('show');
 });
 
  $('#update_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  if($('#action_edit').val() == 'Edit')
  {
   action_url = "{{ route('responsibility.update') }}";
  }
   $.ajax({
	enctype: 'multipart/form-data',
	processData: false,  // Important!
	contentType: false,
	cache: false,
   url: action_url,
   method:"POST",
   data:new FormData(this),
//   data:$(this).serialize(),
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
	$('#responsibility_table').DataTable().ajax.reload();

    }
	$('#formModal').modal('hide');
   }
  });
 });

$(document).on('click', '.delete', function (event) {
	id_responsibility = $(this).attr('id');
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
			   url:"responsibility_menu/destroy/"+id_responsibility,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#responsibility_table').DataTable().ajax.reload();				 
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

 $(document).on('click', '.check_edit', function(){
  var id_responsibility_menu = $(this).attr('id');
  $.ajax({
   url :"responsibility_menu/checkid/"+id_responsibility_menu,
   dataType:"json",
   success:function(data)
   {
    $('#id_responsibility_menu_edit').val(data.result.id_responsibility_menu);
    $('#responsibility_name_edit').val(data.result.responsibility_name);
	$('#browseModal').modal('hide');
   }
  })
 });
 
});

function refresh_data() {
        /**************** Load Menu dropdown **************************/     
		get_address_menu();
		get_company();	
        $('#responsibility_table').DataTable().ajax.reload();
    }
	
function get_address_menu(){
	 $.getJSON('<?= url('setting/responsibility/responsibility_menu/get_address_menu') ?>', function (data) {
            global_address_menu = data;
        }).fail(function (data) { // Call failed
            get_address_menu();
		});	
}
function get_company(){
		 $.getJSON('<?= url('setting/responsibility/responsibility_menu/get_company') ?>', function (data) {
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

</script>
@endsection