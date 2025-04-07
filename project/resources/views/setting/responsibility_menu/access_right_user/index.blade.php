@extends('adminlte::page')
@section('title', 'Access Right User')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Access Right User</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add User</button>
                </div>
            </div>
       
			<div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Select Employee :</label>
                    <div class="col-md-4">
                        <select id="employee" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12 text-right">
                        <button onclick="return false;" id="search" class="btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>

                <div class="div_datatable" style="display:none;"> 
				    <button id="advanced" type="button" class="btn btn-default">Advanced Search</button><br><br>
					<table id="user_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th>No</th>
						<th>Username</th>
						<th>Email</th>
						<th>Desciption Name</th>
						<th>Status</th>
						<th>Assigned Company</th>
						<th style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
                </div>
			</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <section class="content">
            <div class="container-fluid h-100">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <div class="card card-row card-gray-dark collapsed-card collaps_assign">
                            <div class="card-header">
                                <h3 class="card-title">Assign Menu to User</h3>
                                <div class="card-tools">
                                  <button type="button" id="collaps_assign" class="btn bg-gray-dark btn-sm" data-card-widget="collapse">
                                    <i class="fas fa-plus collaps_assign_icon"></i>
                                  </button>
                                </div>
                            </div>
                            <div class="card-body" >
                                <div class="card-body">
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Menu :</label>
                                        <div class="col-md-10">
                                            <select id="menu" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label class="col-md-2 col-form-label">Action By :</label>
                                        <div class="col-md-3">
                                            <select id="action_by" class="form-control form-control-sm select2 action_by" style="width: 100%;">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row" >
                                        <label class="col-md-2 col-form-label">In Company :</label>
                                        <div class="col-md-3">
                                            <select id="company" class="form-control form-control-sm select2 " multiple="multiple" style="width: 100%;">
                                            </select>
                                            <span class="invalid-feedback" role="alert" id="companyError">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div id="by_access_group">
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label">Access Group :</label>
                                            <div class="col-md-3">
                                                <select id="access_group" class="form-control form-control-sm select2 " style="width: 100%;">
                                                </select>
                                                <span class="invalid-feedback" role="alert" id="access_groupError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="form-group row div_grade" >
                                            <label class="col-md-2 col-form-label">Job Grade :</label>
                                            <div class="col-md-3">
                                                <select id="grade" class="form-control form-control-sm select2 " multiple="multiple" style="width: 100%;">
                                                </select>
                                                <span class="invalid-feedback" role="alert" id="gradeError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label">Except User :</label>
                                            <div class="col-md-10">
                                                <select id="except_user" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="by_user">
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label">User :</label>
                                            <div class="col-md-10">
                                                <select id="assign_user" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-md-2"></div>

                                        <div class="col-md-1">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="btn btn-block btn-outline-success answer_label">
                                                        <input type="radio" name="action" autocomplete="off" value="insert" class="action" checked> Insert 
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <label class="btn btn-block btn-outline-success answer_label">
                                                        <input type="radio" name="action" autocomplete="off" value="delete" class="action" > Delete
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group row">
                                        <div class="col-md-12 text-right">
                                            <button type="button" id="submit_assign" class="btn btn-lg btn-success" ><i class="fas fa-document"></i> Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modal_form_user"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="userForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Access Right User</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-md-4 col-form-label">User Name</label>
								<div class="col-md-8">
                                    <input type="hidden" name="id_user" id="id_user" class="form-control form-control-sm">
                                    <input type="text" name="user_name" id="user_name" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="user_nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-md-4 col-form-label">Password</label>
                                <div class="col-md-8">
                                    <input type="password" name="password" id="password" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="passwordError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-md-4 col-form-label">Email</label>
								<div class="col-md-8">
                                    <input type="text" name="email" id="email" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="emailError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							 <div class="row">
                                <label class="col-md-4 col-form-label">Description Name</label>
                                <div class="col-md-8">
                                    <input type="text" name="description_name" id="description_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="description_nameError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-md-4 col-form-label">Default Company</label>
								<div class="col-md-8">
                                    <select name="default_company" id="default_company" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="default_companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-md-4 col-form-label">Assigned Company</label>
								<div class="col-md-8">
                                    <select name="assigned_company[]" id="assigned_company" class="form-control form-control-sm select2" data-placeholder="Select Assigned Company" style="width: 100%;" multiple="multiple">
									</select>
										<span class="invalid-feedback" role="alert" id="assigned_companyError">
											<strong></strong>
										</span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-md-4 col-form-label">Region</label>
                                <div class="col-md-8">
                                    <select name="region[]" id="region" class="form-control form-control-sm select2" data-placeholder="Select Region" style="width: 100%;" multiple="multiple">
                                    </select>
                                        <span class="invalid-feedback" role="alert" id="regionError">
                                            <strong></strong>
                                        </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-md-4 col-form-label">Status</label>
                                <div class="col-md-8">
                                    <select name="status" id="select2status" class="form-control form-control-sm select2" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-md-4 col-form-label">Default Access</label>
								<div class="col-md-8">
                                    <select name="default_access" id="default_access" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="default_accessError">
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
                                    <a class="nav-link active" id="link_tab_menu-details" data-toggle="pill" href="#menu-details" role="tab" aria-controls="link_tab_menu-details" aria-selected="true">User Access Role <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_menu_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="menu-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row justify-content-end">
                                        <div class="col-md-3 pull-right" style="margin-bottom: 20px;">
                                            <input type="text" id="search_menu" class="form-control" />
                                        </div>
                                        <div class="col-md-2 pull-right" >
                                            <button type="button" class="pull-right btn btn-lg btn-primary" id="new_menu_detail"><span class="fas fa-plus"></span> Add Access Role</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_menu_detail" style="width:1800px;"  class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;width:10px;">Sequence</th>
                                                        <th style="white-space:nowrap;">Menu Name</th>
                                                        <th style="white-space:nowrap;width:150px;">Description</th>                                                       
                                                        <th style="white-space:nowrap;width:150px;">Company</th>                                                       
                                                        <th style="white-space:nowrap;width:250px;">Branch</th>                                                       
                                                        <th style="white-space:nowrap;width:100px;">Start Date</th>                                                       
                                                        <th style="white-space:nowrap;width:100px;">End Date</th>                                                       
                                                        <th style="white-space:nowrap;">Can<br>Create</th>                                                       
                                                        <th style="white-space:nowrap;">Can<br>Update</th>                                                       
                                                        <th style="white-space:nowrap;">Can<br>Delete</th>                                                       
                                                        <th style="white-space:nowrap;">Can<br>Print</th>                                                       
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_menu_body">
                                                </tbody>
                                            </table>
                                            <div class="col-md-12">
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
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			<div style="display:none;">
                <table id="sample_table_menu">
                    <tr id="">
                        <td>
							<span class="sn" style="vertical-align:middle;"></span>
						</td>
                        <td>
								<input name="responsibility_user[0][id_user_responsibility]" id="responsibility_user_0_id_user_responsibility" type="hidden" class="form-control form-control-sm id_user_responsibility_input">
                                <input type="text" name="responsibility_user[0][sequence]" id="responsibility_user_0_sequence" class="form-control form-control-sm sequence_input">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="responsibility_user_0_sequenceError">
                                    <strong></strong>
                                </span>					
                        </td>
                        <td>
                            <div style="width:110%;" class="input-group-append">
                                <select name="responsibility_user[0][id_menu]" id="responsibility_user_0_id_menu" class="form-control form-control-sm select2 id_menu_input" style="width: 85%;"></select>
                                <span class="invalid-feedback id_menu_input_error" role="alert" id="responsibility_user_0_id_menuError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
						<td>
                                <input type="text" name="responsibility_user[0][description]" id="responsibility_user_0_description" class="form-control form-control-sm description_input" style="width:150px;">
                                <span class="invalid-feedback description_input_error" role="alert" id="responsibility_user_0_descriptionError">
                                    <strong></strong>
                                </span>					
                        </td>
						 <td>
                                <select name="responsibility_user[0][id_company]" id="responsibility_user_0_id_company" class="form-control form-control-sm select2 id_company_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_company_input_error" role="alert" id="responsibility_user_0_id_companyError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>																	
                                <select name="responsibility_user[0][branch][]" id="responsibility_user_0_branch" class="form-control form-control-sm select2 branch_input" style="width:100%;" multiple="multiple"></select>
                                <span class="invalid-feedback branch_input_error" role="alert" id="responsibility_user_0_branchError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>
                                <input type="text" name="responsibility_user[0][start_date]" id="responsibility_user_0_start_date" class="form-control form-control-sm start_date_input" style="width:120px;">
                                <span class="invalid-feedback start_date_input_error" role="alert" id="responsibility_user_0_start_dateError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>
                                <input type="text" name="responsibility_user[0][end_date]" id="responsibility_user_0_end_date" class="form-control form-control-sm end_date_input" style="width:120px;">
                                <span class="invalid-feedback end_date_input_error" role="alert" id="responsibility_user_0_end_dateError">
                                    <strong></strong>
                                </span>					
                        </td>
						
						<td>
                            <input type="checkbox" name="responsibility_user[0][can_create]" id="responsibility_user_0_can_create" class="form-control form-control-sm can_create_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback can_create_input_error" role="alert" id="responsibility_user_0_can_createError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility_user[0][can_update]" id="responsibility_user_0_can_update" class="form-control form-control-sm can_update_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback can_update_input_error" role="alert" id="responsibility_user_0_can_updateError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility_user[0][can_delete]" id="responsibility_user_0_can_delete" class="form-control form-control-sm can_delete_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback can_delete_input_error" role="alert" id="responsibility_user_0_can_deleteError">
                                <strong></strong>
                            </span>
                        </td>
						<td>
                            <input type="checkbox" name="responsibility_user[0][can_print]" id="responsibility_user_0_can_print" class="form-control form-control-sm can_print_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback can_print_input_error" role="alert" id="responsibility_user_0_can_printError">
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
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_user = "";
let global_default_access = "";
let global_assign = "";
let global_id_user_responsibility = 0;
let global_responsibility_name = [];
let global_menu = [];
let global_region_branch = [];
let global_branch = [];
let global_company = [];
let global_additional_menu = [];
let global_additional_menu_id_company = [];
let global_current_menu = [];
let default_access = [];
let x = [];
let today = new Date().toISOString().slice(0, 10)
let regionFromCustom = {};
let listBranchByCompany = {};
let listCompany = {};
let allEmployee = {!! $allEmployee !!};
let listMenu = {};
let allMenu = [];
let allCompany = [];
let allGrade = [];
let allEmployeeByCompany = [];
let actionBy = [
    { id: 'access_group', text: 'Access Group' },
    { id: 'user', text: 'User' },
    { id: 'access_group_without_grade', text: 'Access Group without job grade' },
];

    $('#employee').select2({
        placeholder: "Cari Karyawan",
        data: allEmployee,
        allowClear: true,
    });

    $('#action_by').select2({
        placeholder: "Action By",
        data: actionBy,
    });

    $(function () {	
			
		$(document).on('click', '.new', function () {
            global_id_user = "";
            $("#userForm")[0].reset();
            $("#assigned_company").val('').trigger('change');
            $("#region").val('').trigger('change');
            $("#table_menu_body").html("");
            $("#userForm .modal-title").html("<span class='fas fa-plus'></span> Form Access Right User");
            $(".invalid-feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#userForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');			

					get_default_access();
					get_default_company();

            $('#modal_form_user').modal('show');
        });
		
		 $('#userForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();

            $(".invalid-feedback").children("strong").text("");
            $("#userForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_user == '' ? "{{ route('responsibility_user.save') }}" : "{{ route('responsibility_user.update') }}",
                    data: formData,
					beforeSend: function () {
                        $('#loader').removeClass('hidden');
                    },
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_user').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(ok => {
								location.reload();
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
					complete: function(){
                        $('#loader').addClass('hidden')
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


    $(document).on('click', '#new_menu_detail', function (event) {
        newDetail(event)
    });

	$(document).on('click', '.delete-record', function () {
        var id = jQuery(this).attr('data-id');
        var targetDiv = jQuery(this).attr('targetDiv');
        jQuery('#rec-' + id).remove();
        $('#table_menu_body tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
            $(this).find('.sequence_input').val(index + 1);

        });
        return true;
    });

    $(document).on('change', '.id_menu_input', function (event, autofill) {
        // if($(this).select2('data').length > 0){
            if (event.originalEvent !== undefined || autofill == undefined){ // change by human
                let id_row = $(this).attr('id-delete');
                get_menu_desc($(this).val(), `#responsibility_user_${id_row}_description`);
            } 
        // }
    });


    $(document).on('click', '.edit', function () {
        let id_user = $(this).attr('id');
        global_id_user = id_user;
        $("#userForm")[0].reset();
        $("#table_menu_body").html("");
        $("#userForm .modal-title").html("<span class='fas fa-edit'></span> Edit Access Right User");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#userForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');
		get_default_company_edit();
        get_default_access();

		$('#default_access').prepend('<option selected></option>').select2({
            data: default_access,
            placeholder: 'Select Default Access'
			// disabled:true
        });
		
        $.ajax({
            url: "<?= url('setting/responsibility_menu/access_right_user/get_user_responsibility') ?>",
            method: "GET",
            data: {id_user: id_user},
			beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (response) {
                global_id_user_responsibility = 0;			
                global_assign = response.assigned_company;
                global_additional_menu = response.additional_menu;
                global_additional_menu_id_company = response.additional_menu_id_company;
                global_default_access = response.access_group;

				$.each(response.user_responsibility, function (i, item) {
                    $('#new_menu_detail').trigger('click');
                });
                
                $('#id_user').val(response.id_user).trigger('change');
                $('#user_name').val(response.user_name).trigger('change');
           //     $('#password').val(response.password).trigger('change');
                $('#email').val(response.email).trigger('change');
                $('#description_name').val(response.description_name).trigger('change');
                $('#default_company').val(response.default_company).trigger('change');
                $('#assigned_company').val(response.assigned_company).trigger('change', [true]);
                $('#default_access').val(response.access_group).trigger('change', [true]);
                $('#select2status').val(response.status).trigger('change');
         //       setTimeout(function () {
                    $('#table_menu_body tr').each(function (index) {
                        global_current_menu.push({
                            "id_menu": response.user_responsibility[index].id_menu, 
                            "id_company": response.user_responsibility[index].id_company, 
                            "id_user_responsibility": response.user_responsibility[index].id_user_responsibility
                        });

                        $(this).find('span.sn').html(index + 1);
                        $(this).find('.id_user_responsibility_input').val(response.user_responsibility[index].id_user_responsibility);
                        $(this).find('.sequence_input').val(response.user_responsibility[index].sequence);

                        let thisIdMenu = response.user_responsibility[index].id_menu;
                        let elementMenu = `#responsibility_user_${index}_id_menu`;
                        showMenu(elementMenu, thisIdMenu, index);

                        $(this).find('.description_input').val(response.user_responsibility[index].description);
                        $(this).find('.id_company_input').val(response.user_responsibility[index].id_company).trigger('change', [true]);

                        let element_branch = `#responsibility_user_${index}_branch`;
                        showBranchByCompany(element_branch, response.user_responsibility[index].id_company, response.user_responsibility[index].branch);
                        // $(this).find('.branch_input').val(response.user_responsibility[index].branch).trigger('change');

                        $(this).find('.start_date_input').val(response.user_responsibility[index].start_date).trigger('change');
                        $(this).find('.end_date_input').val(response.user_responsibility[index].end_date).trigger('change');

						if (response.user_responsibility[index].can_create == 1) {
                            $(this).find('.can_create_input').prop('checked', true);
                        } else {
                            $(this).find('.can_create_input').prop('checked', false);
                        }
						if (response.user_responsibility[index].can_update == 1) {
                            $(this).find('.can_update_input').prop('checked', true);
                        } else {
                            $(this).find('.can_update_input').prop('checked', false);
                        }
						if (response.user_responsibility[index].can_delete == 1) {
                            $(this).find('.can_delete_input').prop('checked', true);
                        } else {
                            $(this).find('.can_delete_input').prop('checked', false);
                        }
						if (response.user_responsibility[index].can_print == 1) {
                            $(this).find('.can_print_input').prop('checked', true);
                        } else {
                            $(this).find('.can_print_input').prop('checked', false);
                        }                            
                    });                       
            //    }, 3000);				
			},
            complete: function(){
                $('#loader').addClass('hidden')
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
	
        $('#modal_form_user').modal('show');
    });

});

$(document).ready(function(){
    get_datatable('employee_null_id_user')
    checkCollapse()

	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}		
	});

	let selected_action = $('#action_by').find(':selected').val();
    show_action(selected_action)
	refresh_data();


    $(document).on('change', '.action_by', function (event) {
        let action = $(this).val();
        show_action(action)
    });

    $(document).on('click', '.delete', function (event) {
    	id_user = $(this).attr('id');
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
    			   url:"access_right_user/destroy/"+id_user,
                   beforeSend: function () {
                        $('#loader').removeClass('hidden');
                    },
    			   success:function(data)
    			   {
                    if (data.status == 'true') {
                        setTimeout(function(){
                         $('#confirmModal').modal('hide');
                         $('#user_table').DataTable().ajax.reload();                 
                            swal({
                                title: "Data Deleted!",
                                  icon: "success",
                                   buttons: {confirm : {className:'btn-success'},},
                                }).then(ok => {
                                    location.reload();
                            });
                        }, 50);
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: data.message
                        });
                    }
    			   },
                   complete: function(){
                        $('#loader').addClass('hidden')
                    },
    			  })
            }
        });
    });

});

$(document).on('keyup', '#search_menu', function (event) {
    var text = $(this).val();
    $('#table_menu_body tr').each(function () {
        let index = $(this).attr('id').split("rec-")[1];
        if(text != ''){
            $(`#rec-${index}`).hide();
            let filter_element = $(this).find(`span#select2-responsibility_user_${index}_id_menu-container:contains(${text})`).text().substring(1);
            if(filter_element != ''){
                $(`#rec-${index}`).show();
            }
        } else {
            $(`#rec-${index}`).show();
        }
    });
});

$(document).on('click', '#search', function () {
    get_datatable()
});

$(document).on('click', '#submit_assign', function () {
    let menu            = $(`#menu`).val();
    let selected_action = $('#action_by').find(':selected').val();
    let access_group    = $(`#access_group`).val();
    let grade           = $(`#grade`).val();
    let company         = $(`#company`).val();
    let except_user     = $(`#except_user`).val();
    let assign_user     = $(`#assign_user`).val();
    let action          = $('input[name="action"]:checked').val();
    let errMessage      = '';

    if(menu == ''){
        errMessage += 'Please select Menu \n';
    }

    if(selected_action == 'user' && assign_user == ''){
        errMessage += 'Please select User \n';
    } 
    else if(selected_action != 'user' && access_group == ''){
        errMessage += 'Please select Access Group \n';
    }

    if(errMessage != ''){
        swal({
            icon: 'warning',
            title: 'Warning',
            dangerMode: true,
            text: errMessage
        });
        return false;
    }

    let param = {menu:menu, action_by:selected_action, access_group:access_group, grade:grade, company:company, except_user:except_user, assign_user:assign_user, action:action};
    $.ajax({
        url: '<?= url('setting/responsibility_menu/assignMenu') ?>',
        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
        method: "POST",
        data: param,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (res) {
            if (res.status == 'true') {
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: res.message
                });
            }
        },
        complete: function(){
            $('#loader').addClass('hidden')
        },
    });

});

function refresh_data() {
        /**************** Load Menu dropdown **************************/
	get_menu_name();
    get_menu().then(list => {
        $.each(list, function (i, val) {
            menu_name = `${val.menu_name} (${val.responsibility}) (${val.status}) (${val.address_menu})`;
            allMenu.push({id:val.id_menu, text:menu_name});
        }); 
        $('#menu').select2({
            data: allMenu,
            placeholder: 'Select Menu'
        });
    });

    get_grade().then(list => {
        $.each(list, function (i, val) {
            if(val.job_class_group == null || val.job_class_group == ''){
                grade_group = '';
            } else {
                grade_group = ` - (${val.job_class_group})`;
            }
            name_grade = `${val.description}${grade_group}`
            allGrade.push({id:val.id_job_grade, text:name_grade});
        }); 
        $('#grade').select2({
            data: allGrade,
            placeholder: 'Select Grade'
        });
    });

    getCompany().then(list => {
        $.each(list, function (i, val) {
            allCompany.push({id:val.id, text:val.text});
        }); 
        $('#company').select2({
            data: allCompany,
            placeholder: 'Select Company'
        });
    });

    getUserByCompany().then(list => {
        $.each(list, function (i, val) {
            user = `${val.name} (${val.user_name}) (${val.status})`
            allEmployeeByCompany.push({id:val.id_user, text:user});
        }); 
        $('#except_user').select2({
            placeholder: "Except User",
            data: allEmployeeByCompany,
            allowClear: true,
        });
        $('#assign_user').select2({
            placeholder: "Choose User",
            data: allEmployeeByCompany,
            allowClear: true,
        });
    });

    // get_region();
    getRegionFromCustomReport()
	// get_branch();
    getBranchByCompany()
	default_access = [
		{id: 'Default_User', text: 'Default User'},
		{id: 'Default_Manager', text: 'Default Manager'},
		{id: 'Default_Administrator', text: 'Default Administrator'},
	];
	
	$('#select2status').select2();	
    $('#access_group').prepend('<option selected></option>').select2({
        data: default_access,
        placeholder: 'Select Access Group',
    });
}

function show_action(by='') {
    if(by == 'user'){
        $('#by_user').show();  
        $('#by_access_group').hide();  
    } else if(by == 'access_group_without_grade'){
        $('#by_user').hide();  
        $('#by_access_group').show(); 
        $('.div_grade').hide();  
    } else {
        $('#by_user').hide();  
        $('#by_access_group').show();  
        $('.div_grade').show();  
    }
}

function get_menu_name() {
	 $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_menu_name') ?>', function (data) {
        global_responsibility_name = data;

        $(global_responsibility_name).each(function (i, val) {
            listMenu[val.id] = [global_responsibility_name[i]];
        });

    }).fail(function (data) { // Call failed
        get_menu_name();
    });
}

const get_menu = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('setting/responsibility_menu/access_right_user/get_menu') ?>',
            method: "GET",
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_menu();
    }
}

const get_grade = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('setting/responsibility_menu/access_right_user/get_grade') ?>',
            method: "GET",
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_grade();
    }
}

function get_menu_desc(menu_desc, element) {
	  $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_menu_desc') . '?id=' ?>' + menu_desc, function (data) {
            if(data.length > 0){
               // $('#responsibility_user_' + size + '_description').val(data[0].text).trigger('change');
               $(element).val('');
               $(element).val(data[0].text);
            }
      }).fail(function (data) { // Call failed
        get_menu_desc(menu_desc,size);
    });
}

function get_default_company() {
	$.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_default_company') ?>', function (data) {			
		global_company = data;
		
        $('#default_company').select2({
            data: global_company
        });

		$('#assigned_company').select2({
            data: global_company
        }).on('change', function (e) {		
			global_assign = $(this).val();			
            change_access_role(global_assign, global_default_access)
        }).trigger('change');

		/*
		.on('change', function (e) {
            if($(this).select2('data').length > 0){
                $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_assigned_company') . '?id=' ?>' + $(this).val(), function (data) {
                    global_company = data;
                    $('#table_menu_detail').find('.id_company_input').each(function (i, obj) {
                        $('#' + obj.id).empty();
                        $('#' + obj.id).select2({
                            allowClear: true,
                            data: global_company
                        });
                    });
                });
            }
        }).trigger('change');
		*/
		
    }).fail(function (data) { // Call failed
        get_default_company();
    });
}

function get_default_company_edit() {
	$.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_default_company') ?>', function (data) {			
		global_company = data;
		
        $('#default_company').select2({
            data: global_company
        });

		$('#assigned_company').select2({
            data: global_company
        }).on('change', function (e, isTriggered) {
            // if($(this).select2('data').length > 0){
                if (!isTriggered){ // change by human
                    global_assign = $(this).val();   
                    change_access_role(global_assign, global_default_access)
                } 
            // }
        });

    }).fail(function (data) { // Call failed
        get_default_company_edit();
    });
}

function get_region() {
     $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_region') ?>', function (data) {
        $('#region').select2({
            data: data
        }).on('change', function (e) {
            if($(this).select2('data').length > 0){          
                 jQuery.ajax({
                    url: '<?= url('setting/responsibility_menu/access_right_user/get_region_branch') . '?id=' ?>' + $(this).val(),
                    dataType: "json",
                    success: function(res){
                        global_region_branch = [];
                        $.each(res, function (i) {
                           global_region_branch.push(res[i].id);
                        });                             
                        $('#table_menu_body').find('.branch_input').each(function (i, obj) {
                            $('#' + obj.id).val(global_region_branch).trigger('change');
                        });
                    }
                });                 
            }
            else{
                 $('#table_menu_body').find('.branch_input').each(function (i, obj) {
                    $('#' + obj.id).val([]).trigger('change');
                });
            }
        }).trigger('change');
    }).fail(function (data) { // Call failed
        get_region();
    });
}

const getBranchByCompany = async (id_company='') => {
    try {
        let result;
        let param;
        if(id_company != ''){
            thisIdCompany = id_company;
        } else {
            thisIdCompany = "<?= session('id_company') ?>";
        }
        result = await $.ajax({
            url: '<?= url('setting/responsibility_menu/access_right_user/get_branch') ?>',
            method: "GET",
            data: {id_company: thisIdCompany},
            success: function (res) {
                listBranchByCompany= res;
            },
        });
        return result;
    } catch (error) {
        getBranchByCompany(id_company);
    }
}

const getCompany = async (id_company='') => {
    try {
        let result;
        let param;
        if(id_company != ''){
            thisIdCompany = id_company;
        } else {
            thisIdCompany = "<?= session('id_company') ?>";
        }
        result = await $.ajax({
            url: '<?= url('getCompany') ?>',
            method: "GET",
            success: function (res) {
                listCompany = res;
            },
        });
        return result;
    } catch (error) {
        getCompany(id_company);
    }
}

const getUserByCompany = async () => {
    try {
        let result;
        let param;
        let company = $(`#company`).val();
        result = await $.ajax({
            url: '<?= url('setting/responsibility_menu/access_right_user/getUserByCompany') ?>',
            method: "GET",
            data: {id_company: company},
            success: function (res) {
                listCompany = res;
            },
        });
        return result;
    } catch (error) {
        getCompany();
    }
}
    
function get_branch() {
	 $.getJSON('<?= url('setting/responsibility_menu/access_right_user/get_branch') ?>', function (data) {
        if (typeof data === 'object' && !Array.isArray(data)){
            Object.keys(data).forEach(function(key) {
                global_branch.push(data[key])
            });
        } else {
            global_branch = data;
        }
    }).fail(function (data) { // Call failed
        get_branch();
    });
}

async function getRegionFromCustomReport() {
    let path_menu   = "<?= $path_menu ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('employee/employee_setting/custom_report/get_data_custom') . '?address=' ?>' + path_menu, function (res) { 
            if(res.length > 0){
                $(res).each(function (i, obj) {
                    let branchFromThisCustom = [];
                    $(res[i].detail_column.split(',')).each(function (index, val) {
                        branchFromThisCustom.push(val);
                    });   
                    regionFromCustom[res[i].id] = branchFromThisCustom;
                });        

                $('#region').select2({
                    placeholder: "Select Option",
                    data: res,
                    allowClear: true,
                });
            } else {
                $('#region').select2({
                    placeholder: "Select Option",
                    data: [],
                    allowClear: true,
                });
            }
        });
        return result;
    } catch (error) {
        getRegionFromCustomReport();
    }
}

$(document).on('change', 'td .id_company_input', function (event) {
    if (!(event.originalEvent === undefined)){ // change by human
        let id_company = $(this).val();
        let id_element = $(this).attr('size');
        let element_branch = `#responsibility_user_${id_element}_branch`;
        showBranchByCompany(element_branch, id_company);
    } 
});

const showBranchByCompany = async (element='', id_company='', value=[]) => {
    if(id_company==''){
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Branch",
            allowClear: true,
            data: listBranchByCompany,
        });
    } else {
        if(!(listBranchByCompany.hasOwnProperty(id_company))){
            getBranchByCompany(id_company).then(list => {
                $(element).html('').prepend('<option></option>').select2({
                    placeholder: "Select Branch",
                    data: list,
                    allowClear: true,
                });
                if(value.length > 0){
                    $(element).val(value).trigger('change', [true]);
                }
            });
        } else {
            if ($(element).data('select2')){
                $(element).select2('destroy');
            }
            $(element).html('').prepend('<option></option>').select2({
                placeholder: "Select Branch",
                data: listBranchByCompany,
                allowClear: true,
            });
            if(value.length > 0){
                $(element).val(value).trigger('change', [true]);
            }
        }
    }
}  

const get_datatable = async (is_null='') => {
    $(".div_datatable").show();

    let myData = {
        id_user: $("#employee").val() == '' ? null : $("#employee").val(),
        is_null: is_null == '' ? null : is_null,
    };

    let t = $('#user_table').DataTable({
        processing: true,
        serverSide: false,
        // responsive: true,
        destroy: true,
        ajax: {
            url: "{{ route('responsibility_user.index') }}",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {   // Checkbox select column
                data: 'id_user',
                defaultContent: '',
                orderable: false
            },
            { data: 'user_name', name: 'user_name'},
            { data: 'email', name: 'email'},
            { data: 'description_name', name: 'description_name'},
            { data: 'status', name: 'status'},
            { data: 'assigned_company', name: 'assigned_company', render: function ( data, type, row ) { 
                     return data;
                    } },
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
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
    });
}  

$('#region').on('select2:select select2:unselect', function(e) {
    if($(this).select2('data').length > 0){  
        let allSelectedRegion = $(this).select2('val');
        let allValueSelectedRegion = [];
        $('#table_menu_body tr').each(function (index) {
            let tr_id = $(this).attr('id').split("rec-")[1];
            $(allSelectedRegion).each(function (i, val) {
                allValueSelectedRegion.push(...regionFromCustom[val]);
            });
            $(this).find(`#responsibility_user_${tr_id}_branch`).val(allValueSelectedRegion).trigger('change', [true]);
        });  
    }
    else{
         $('#table_menu_body').find('.branch_input').each(function (i, obj) {
            $('#' + obj.id).val([]).trigger('change');
        });
    }
});

function get_default_access(){
		$('#default_access').prepend('<option selected></option>').select2({
            data: default_access,
			allowClear: true,
			placeholder: 'Select Default Access'
        }).on('change', function (e, isTriggered) {
            // if($(this).select2('data').length > 0){
                if (!isTriggered){ // change by human
        			global_default_access = $(this).val();			
                    change_access_role(global_assign, global_default_access)
                }
            // }
        });
}	
	
function get_group_branch(element, i, id_company){
	element.find('.branch_input').on('select2:open', function(e) {
    	$('#select2-responsibility_user_'+i+'_branch-results').on('click', function(event) {
    		event.stopPropagation();
    		var data = $(event.target).html();
    		var selectedOptionGroup = data.toString().trim();
    		var groupchildren = [];
			for (var i = 0; i < listBranchByCompany.length; i++) {
			  if (selectedOptionGroup.toString() === listBranchByCompany[i].text.toString()) {
				for (var j = 0; j < listBranchByCompany[i].children.length; j++) {
				  groupchildren.push(listBranchByCompany[i].children[j].id);
				}
			  }
			}
            var options = [];
            options = element.find('.branch_input').val();
            if (options === null || options === '') {
              options = [];
            }
            for (var i = 0; i < groupchildren.length; i++) {
              var count = 0;
              for (var j = 0; j < options.length; j++) {
                if (options[j].toString() === groupchildren[i].toString()) {
                  count++;
                  break;
                }
              }
              if (count === 0) {
                options.push(groupchildren[i].toString());
              }
            }
            element.find('.branch_input').val(options);
            element.find('.branch_input').trigger('change'); // Notify any JS components that the value changed
            element.find('.branch_input').select2('close');    
    	});
    });
}


function change_access_role(global_assign, global_default_access) {
    $('.delete-record').trigger('click');

    if(global_assign.length > 0){
        jQuery.ajax({
            url: "<?= url('setting/responsibility_menu/access_right_user/get_default_access') . '?code_default=' ?>" + global_default_access +"<?= '&id_company='?>"+global_assign,
            dataType: "json",
            beforeSend: function(){
                $('#loader').removeClass('hidden');
            },
            success: function(res){
                let id_menu_from_default = [];
                let id_user_responsibility = '';
                let id_user_responsibility_additional = '';
                let last_index = 0;
                $.each(res.menu, function (i, item) {  
                    newDetail('', 'access_role');
                    // $('#new_menu_detail').trigger('click', [true]);
                });
                $('#table_menu_body tr').each(function (index) {
                    let tr_id = $(this).attr('id').split("rec-")[1];

                    $(global_current_menu).each(function (i, item) {
                        if(item.id_menu == res.menu[index].id_menu && item.id_company == res.menu[index].id_company){
                            id_user_responsibility = item.id_user_responsibility;
                        }
                    });
                    id_menu_from_default.push(res.menu[index].id_menu);

                    $(this).find('span.sn').html(index + 1);
                    $(this).find('.sequence_input').val(index + 1);
                    $(this).find('.id_user_responsibility_input').val(id_user_responsibility);
                    $(this).find('.id_menu_input').val(res.menu[index].id_menu).trigger('change', [true]);
                    $(this).find('.id_company_input').val(res.menu[index].id_company).trigger('change', [true]);
                    $(this).find('.description_input').val(res.menu[index].menu_name);

                    let element_branch = `#responsibility_user_${tr_id}_branch`;
                    showBranchByCompany(element_branch, res.menu[index].id_company);

                    $(this).find('.can_create_input').prop('checked', true);                           
                    $(this).find('.can_update_input').prop('checked', true);
                    $(this).find('.can_delete_input').prop('checked', true);
                    $(this).find('.can_print_input').prop('checked', true);

                    last_index = index;
                    id_user_responsibility = '';
                });

                if(global_additional_menu.length > 0){
                    let i_global_additional_menu = 0;
                    $.each(global_additional_menu, function (i, item) {   
                        if(id_menu_from_default.includes(item)){
                            return true;
                        } 
                        newDetail('', 'access_role');
                    });
                    $('#table_menu_body tr').each(function (index_additional) {
                        let tr_id = $(this).attr('id').split("rec-")[1];

                        if(index_additional > last_index){
                            if(id_menu_from_default.includes(global_additional_menu[i_global_additional_menu])){
                                return true;
                            }
                            $(global_current_menu).each(function (i, item) {
                                if(item.id_menu == global_additional_menu[i_global_additional_menu]){
                                    id_user_responsibility_additional = item.id_user_responsibility;
                                }
                            });
                            $(this).find('span.sn').html(last_index + 2);
                            $(this).find('.sequence_input').val(last_index + 2);
                            $(this).find('.id_user_responsibility_input').val(id_user_responsibility_additional);
                            $(this).find('.id_menu_input').val(global_additional_menu[i_global_additional_menu]).trigger('change');
                            $(this).find('.id_company_input').val(global_additional_menu_id_company[i_global_additional_menu]).trigger('change');

                            let element_branch = `#responsibility_user_${tr_id}_branch`;
                            showBranchByCompany(element_branch, global_additional_menu_id_company[i_global_additional_menu]);

                            $(this).find('.can_create_input').prop('checked', true);                           
                            $(this).find('.can_update_input').prop('checked', true);
                            $(this).find('.can_delete_input').prop('checked', true);
                            $(this).find('.can_print_input').prop('checked', true);

                            i_global_additional_menu++;
                            last_index++;
                        }
                    });
                }
            },
            complete: function(){
                $('#loader').addClass('hidden')
            },
        });
    }
}

const showMenu = async (element='', idMenu='', index) => {
    let optionMenu = listMenu[idMenu];
    $(element).html('').prepend('<option></option>').select2({
        placeholder: "Select Menu",
        data: optionMenu,
        allowClear: true,
    });
    $(element).val(idMenu).trigger('change',['autofill']).attr('readonly', true);
    $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editMenu${index}" onclick="editMenu('${element}', '${idMenu}', '${index}')" ></span>`));
} 

const editMenu = async (element='', idMenu='', index) => {
    $(element).html('').prepend('<option></option>').select2({
        placeholder: "Select Menu",
        data: global_responsibility_name,
        allowClear: true,
    });
    $(element).val(idMenu).trigger('change').attr('readonly', false);
    $(`span#editMenu${index}`).remove();
}   

const newDetail = async (event='', from='') => {
    var content = jQuery('#sample_table_menu tr'),
            size = global_id_user_responsibility++,
            element = null,
            element = content.clone();
    element.attr('id','rec-'+size);
    element.find('.delete-record').attr('data-id', size);
    
    element.find('.id_user_responsibility_input').attr('id', 'responsibility_user_' + size + '_id_user_responsibility');
    element.find('.id_user_responsibility_input').attr('name', 'responsibility_user[' + size + '][id_user_responsibility]');
    
    element.find('.sequence_input').attr('id', 'responsibility_user_' + size + '_sequence');
    element.find('.sequence_input').attr('name', 'responsibility_user[' + size + '][sequence]');
    element.find('.sequence_input_error').attr('id', 'responsibility_user_' + size + '_sequenceError');

    element.find('.id_menu_input').attr('id', 'responsibility_user_' + size + '_id_menu');
    element.find('.id_menu_input').attr('name', 'responsibility_user[' + size + '][id_menu]');
    element.find('.id_menu_input').attr('id-delete', size);
    element.find('.id_menu_input_error').attr('id', 'responsibility_user_' + size + '_id_menuError');
    if(from=='access_role'){
        element.find('.id_menu_input').select2({
            placeholder: "Select Menu Name",
            allowClear: true,
            data: global_responsibility_name
        });
        element.find('.id_menu_input').val('').trigger('change', [true]);
    } else {
        if (!(event.originalEvent === undefined)){ // change by human
            element.find('.id_menu_input').select2({
                placeholder: "Select Menu Name",
                allowClear: true,
                data: global_responsibility_name
            });
            element.find('.id_menu_input').val('').trigger('change', [true]);
        } 
    }
    
    element.find('.description_input').attr('id', 'responsibility_user_' + size + '_description');
    element.find('.description_input').attr('name', 'responsibility_user[' + size + '][description]');
    element.find('.description_input_error').attr('id', 'responsibility_user_' + size + '_descriptionError');
    
    element.find('.id_company_input').attr('id', 'responsibility_user_' + size + '_id_company');
    element.find('.id_company_input').attr('name', 'responsibility_user[' + size + '][id_company]');
    element.find('.id_company_input').attr('size', size);
    element.find('.id_company_input_error').attr('id', 'responsibility_user_' + size + '_id_companyError');
    element.find('.id_company_input').select2({
        placeholder: "Select Company",
        allowClear: true,
        data: global_company
    });
    element.find('.id_company_input').val([$('#assigned_company').val()]).trigger('change', [true]);
    
    element.find('.branch_input').attr('id', 'responsibility_user_' + size + '_branch');
    element.find('.branch_input').attr('name', 'responsibility_user[' + size + '][branch][]');
    element.find('.branch_input_error').attr('id', 'responsibility_user_' + size + '_branchError');
    if (!(event.originalEvent === undefined)){ // change by human
        element.find('.branch_input').select2({
            placeholder: "Select Branch",
            width:'100%',
            allowClear: true,
            data: listBranchByCompany
        });
        get_group_branch(element, size, "<?= session('id_company') ?>");
    } 

    element.find('.start_date_input').attr('id', 'responsibility_user_' + size + '_start_date');
    element.find('.start_date_input').attr('name', 'responsibility_user[' + size + '][start_date]');
    element.find('.start_date_input_error').attr('id', 'responsibility_user_' + size + '_start_dateError');
    element.find('.start_date_input').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
    });
    element.find('.start_date_input').val(today).trigger('change');
    
    element.find('.end_date_input').attr('id', 'responsibility_user_' + size + '_end_date');
    element.find('.end_date_input').attr('name', 'responsibility_user[' + size + '][end_date]');
    element.find('.end_date_input_error').attr('id', 'responsibility_user_' + size + '_end_dateError');
    element.find('.end_date_input').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
    });
    element.find('.end_date_input').val('').trigger('change');
    
    element.find('.can_create_input').attr('id', 'responsibility_user_' + size + '_can_create');
    element.find('.can_create_input').attr('name', 'responsibility_user[' + size + '][can_create]');
    element.find('.can_create_input_error').attr('id', 'responsibility_user_' + size + '_can_createError');

    element.find('.can_update_input').attr('id', 'responsibility_user_' + size + '_can_update');
    element.find('.can_update_input').attr('name', 'responsibility_user[' + size + '][can_update]');
    element.find('.can_update_input_error').attr('id', 'responsibility_user_' + size + '_can_updateError');
    
    element.find('.can_delete_input').attr('id', 'responsibility_user_' + size + '_can_delete');
    element.find('.can_delete_input').attr('name', 'responsibility_user[' + size + '][can_delete]');
    element.find('.can_delete_input_error').attr('id', 'responsibility_user_' + size + '_can_deleteError');

    element.find('.can_print_input').attr('id', 'responsibility_user_' + size + '_can_print');
    element.find('.can_print_input').attr('name', 'responsibility_user[' + size + '][can_print]');
    element.find('.can_print_input_error').attr('id', 'responsibility_user_' + size + '_can_printError');

    element.appendTo('#table_menu_body');
    $('#table_menu_body tr').each(function (index) {
        $(this).find('span.sn').html(index + 1);
        $(this).find('.sequence_input').val(index + 1);
    });
}   

function checkCollapse() {
    let collapse = ['collaps_assign'];
    $.each(collapse, function(i, val){
        if(localStorage.getItem(val)){
            $(`.${val}`).removeClass('collapsed-card');
            $(`.${val}_icon`).removeClass('fa-plus').addClass('fa-minus');
        } else {
            $(`.${val}`).addClass('collapsed-card');
            $(`.${val}_icon`).removeClass('fa-minus').addClass('fa-plus');
        }
    });
}

$("#collaps_assign").click(function () {
    let id = $(this).attr('id');
    if(localStorage.getItem(id)){
        localStorage.removeItem(id);
    } else {
        localStorage.setItem(id, 'true');
    }
});

$(document).on('change', '#company', function (event) {
    $('#except_user').html('');
    $('#assign_user').html('');
    allEmployeeByCompany = [];
    getUserByCompany().then(list => {
        $.each(list, function (i, val) {
            user = `${val.name} (${val.user_name}) (${val.status})`
            allEmployeeByCompany.push({id:val.id_user, text:user});
        }); 
        $('#except_user').select2({
            placeholder: "Except User",
            data: allEmployeeByCompany,
            allowClear: true,
        });
        $('#assign_user').select2({
            placeholder: "Choose User",
            data: allEmployeeByCompany,
            allowClear: true,
        });
    });
});

</script>
@endsection