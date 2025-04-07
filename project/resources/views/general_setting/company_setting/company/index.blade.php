@extends('adminlte::page')
@section('title', 'Company')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Company</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Company</button>
                </div>
            </div>
       
			<div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="company_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Company Code</th>
						<th>Company Name</th>
						<th>Company Type</th>
						<th>Description</th>
						<th>Address</th>
						<th>Company Phone</th>
						<th>Company Logo</th>
						<th>Administrator Email</th>
						<th>Currency</th>
						<th>Open Period</th>
						<th>Inactive Date</th>
						<th style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_company"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="companyForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Company</h5>
                    <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="company_code" id="company_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="company_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Name</label>
                                <div class="col-sm-8">
                                    <input name="company_name" id="company_name" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="company_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company Type</label>
                                <div class="col-sm-8">
                                    <select name="company_type" id="company_type" class="form-control form-control-sm select2" style="width: 100%;">
                                        <option value="corporate">Corporate</option>
                                        <option value="os">OS</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company Phone</label>
                                <div class="col-sm-8">
                                    <input type="text" name="company_phone" id="company_phone" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="company_phoneError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Registry</label>
                                <div class="col-sm-8">
                                    <input name="company_registry" id="company_registry" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="company_registryError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Open Period</label>
                                <div class="col-sm-8">
                                    <input type="text" name="open_period" id="open_period" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="open_periodError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							
                        </div>
						
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Address</label>
                                <div class="col-sm-8">
                                    <input name="address" id="address" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="addressError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Administrator Email</label>
                                <div class="col-sm-8">
                                    <input type="text" name="administrator_email" id="administrator_email" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="administrator_emailError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Currency</label>
								 <div class="col-sm-8">
									<div class="input-group">
										<input name="id_currency" id="currencyid0" type="hidden">
										 <input name="name" id="name0" type="text" onclick="browse_table(0)" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;">									
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-feedback" role="alert" id="id_currencyError">
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
							   <label class="col-md-4" style="margin-top:5px;">Company Logo</label>
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="company_logo" class="custom-file-input" id="company_logo">
								  <span class="invalid-feedback" role="alert" id="company_logoError">
                                        <strong></strong>
                                    </span>
								  <label class="custom-file-label" for="customFile"><i>Max 300kb</i></label>
								</div>
								
							   </div>
							</div>
                        </div>
						
                       <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
                </div>
            </form>
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
					   <th>Currency</th>
						<th>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	

<div id="formModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-xl">
  <div class="modal-content">
   <div class="modal-header">
         <h4 class="modal-title"></h4>
         <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 <form id="update_form" class="form-horizontal" method="POST" enctype="multipart/form-data">
					@csrf
					<div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="company_code" id="company_code_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="company_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Name</label>
                                <div class="col-sm-8">
                                    <input name="company_name" id="company_name_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="company_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company Type</label>
                                <div class="col-sm-8">
                                    <select name="company_type" id="company_type_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                        <option value="corporate">Corporate</option>
                                        <option value="os">OS</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company Phone</label>
                                <div class="col-sm-8">
                                    <input type="text" name="company_phone" id="company_phone_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="company_phoneError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Company Registry</label>
                                <div class="col-sm-8">
                                    <input name="company_registry" id="company_registry_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="company_registryError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Open Period</label>
                                <div class="col-sm-8">
                                    <input type="text" name="open_period" id="open_period_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="open_periodError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Address</label>
                                <div class="col-sm-8">
                                    <input name="address" id="address_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="addressError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-sm-4 col-form-label">Administrator Email</label>
                                <div class="col-sm-8">
                                    <input type="text" name="administrator_email" id="administrator_email_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="administrator_emailError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Currency</label>
								 <div class="col-sm-8">
									<div class="input-group">
										 <input id="currency_edit" type="text" maxlength="10" class="form-control form-control-sm browse_table_edit">
										<input name="id_currency" id="id_currency_edit" type="hidden">								
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm browse_table_edit"></span>
										</div>
									</div>
									<span class="invalid-feedback" role="alert" id="id_currencyError">
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
							   <label class="col-md-4" style="margin-top:5px;">Company Logo</label>
							   <div class="col-md-8">
								 <div class="custom-file">
								  <input type="file" name="company_logo" class="custom-file-input">
								  <span class="invalid-feedback" role="alert" id="company_logoError">
                                        <strong></strong>
                                    </span>
									<img id="company_logo_edit" src="#" width="150px" height="30px">
								  <label class="custom-file-label" for="customFile"><i>Max 300kb</i></label>
								</div>
								
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
                <button type="button" class="btn btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
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
$(document).ready(function () {
  bsCustomFileInput.init();
  
   $('#datepicker').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });
});

$(document).on('click', '.new', function () {
            $("#companyForm")[0].reset();
            $("#companyForm .modal-title").html("<span class='fas fa-plus'></span> Form Company");
            $(".invalid-feedback").children("strong").text("");
            $("#companyForm input").removeClass("is-invalid");
            $('#modal_form_company').modal('show');
        });

    $(function () {
        $('#companyForm').submit(function (e) {
            e.preventDefault();
        //    let formData = $(this).serializeArray();
			var formData = new FormData(this);
            $(".invalid-feedback").children("strong").text("");
            $("#companyForm input").removeClass("is-invalid");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
					url: "{{ route('company.save') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_company').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#company_table').DataTable().ajax.reload();
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


		
function check(i,id_currency) {
	
		//  var id = $(this).attr('id');
		  $.ajax({
		   url :"company/checkid/"+id_currency,
		   dataType:"json",
		   success:function(data)
		   {

			$("#currencyid"+i).val(data.result.id_currency);
			$("#name"+i).val(data.result.description);
			$("#browseModal").modal('hide');
		   }
		  })
}

function browse_table(counter) {
	$('#browseModal').modal('show');
	$("#bro_table").DataTable({
		destroy: true,
		scrollY: true,
		pageLength: 10,	
		columnDefs:false,
          columns : [
            { data : 'no' ,className: 'false'},
            { data : 'description' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
            url: "{{ route('company.browse') }}",
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'no'          : '<center>'+no+'</center>',
                  'description'   : '<center>'+json[i]['description']+'</center>',
                  'action'   : '<center><button type="button" name="check" id="'+json[i]['id_currency']+'" class="btn btn-success btn-sm" title="Check" onClick="check('+counter+','+json[i]['id_currency']+')"><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
      });

}


$(document).ready(function(){
	$('#company_type').select2();
	$('#company_type_edit').select2();
    $('#company_table').DataTable({
        processing: true,
        serverSide: true,
		fixedColumns: {
            leftColumns: 4,
            rightColumns: 1,
        },
        scrollX: true, 
        ajax: {
		   url: "{{ route('company.index') }}",
		   error: function (jqXHR, textStatus, errorThrown) {
					$('#company_table').DataTable().ajax.reload();
				}
		  },
        columns: [
			{
                defaultContent: '',
				orderable: false,
				},
			{   // Checkbox select column
                data: 'id_company',
                defaultContent: '',
                orderable: false
            },
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'company_code', name: 'company_code' },
            { data: 'company_name', name: 'company_name' },
            { data: 'company_type', name: 'company_type', render: function ( data, type, row ) {
					if(data == 'corporate'){
						return 'Corporate';
					} 
					else{
						return 'OS';
					}
				} 
			},
            { data: 'description', name: 'description' },
            { data: 'address', name: 'address' },
            { data: 'company_phone', name: 'company_phone' },
            { data: 'company_logo', name: 'company_logo', render: function ( data, type, row ) {
                        return '<img src="data:image;base64,'+ data + '" width="150px" height="30px">';
                    } 
			},
            { data: 'administrator_email', name: 'administrator_email' },
            { data: 'name', name: 'name' },
            { data: 'open_period', name: 'open_period' },
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

	

$('.browse_table_edit').click(function(){   
  $('#bro_table').DataTable({
	destroy: true,
	scrollY: true,
	pageLength: 10,
	columnDefs:false,
	columns: [
	{ data : 'no' ,className: 'false'},
    { data : 'description'},
    { data : 'action' },
		],
   ajax: {
            type: 'GET',
            url: "{{ route('company.browse') }}",
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
   action_url = "{{ route('company.update') }}";
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
	$('#company_table').DataTable().ajax.reload();

    }
	$('#formModal').modal('hide');
   }
  });
 });

 $(document).on('click', '.edit', function(){
	
	  $('#datepicker_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });
	
			 var id_company = $(this).attr('id');
			  $('#form_result').html('');
			  $.ajax({
			   url :"company/edit/"+id_company,
			   dataType:"json",
			   success:function(data)
			   {
			//	console.log(data);
				$('#company_code_edit').val(data.result.company_code);
				$('#company_name_edit').val(data.result.company_name);
				$('#company_type_edit').val(data.result.company_type);
				$('#description_edit').val(data.result.description);
				$('#address_edit').val(data.result.address);
				$('#company_phone_edit').val(data.result.company_phone);
				$('#company_registry_edit').val(data.result.company_registry);
				document.getElementById("company_logo_edit").src = "data:image;base64,"+data.result.company_logo;
				$('#administrator_email_edit').val(data.result.administrator_email);
				$('#company_email_edit').val(data.result.company_email);
				$('#open_period_edit').val(data.result.open_period);
				$('#datepicker_edit').val(data.result.inactive_date);
				$('#id_currency_edit').val(data.result.id_currency);
				$('#currency_edit').val(data.hasil.currency_desc);
				$('#hidden_id').val(id_company);
				$('.modal-title').text('Edit Record');
				$('#action_button').val('Edit');
				$('#action_edit').val('Edit');
				$('#formModal').modal('show');
			   }
			  })
 });
 

$(document).on('click', '.delete', function (event) {
	id_company = $(this).attr('id');
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
			   url:"company/destroy/"+id_company,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#company_table').DataTable().ajax.reload();				 
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
  var id_currency = $(this).attr('id');
  $.ajax({
   url :"company/checkid/"+id_currency,
   dataType:"json",
   success:function(data)
   {
    $('#id_currency_edit').val(data.result.id_currency);
    $('#currency_edit').val(data.result.description);
	$('#browseModal').modal('hide');
   }
  })
 });
 
});
</script>
@endsection