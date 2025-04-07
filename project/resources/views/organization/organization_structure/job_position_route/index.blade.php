@extends('adminlte::page')

@section('title', 'Job Position Route')

@section('content_header')
<!-- div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0 text-dark">Job Position Route</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">Organization Structure</li>
            <li class="breadcrumb-item">Organization Structure</li>
            <li class="breadcrumb-item active">Job Position Route</li>
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
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Position Route</button>
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table id="table_job_position_route" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Job Position</th>
                            <th>Position Route Name</th>
                            <th>Department</th>
                            <th>Expected Employees</th>
                            <th>Existing Employees</th>
                            <th>Unfulfilled Employees</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th data-priority="2" width=150>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal_form_job_position_route"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Job Position Route</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="job_position_routeForm">
                <div class="modal-body">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job</label>
                                <div class="col-sm-8">
                                    <select name="job_position" id="job_position" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="job_positionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Position Route Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="job_position_route" id="job_position_route" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="job_position_routeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Parent Position Route</label>
                                <div class="col-sm-8">
                                    <select name="parent_job" id="parent_job" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="parent_jobError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Level</label>
                                <div class="col-sm-8">
                                    <select name="grade" id="grade" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="gradeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Job Status</label>
                                <div class="col-sm-8">
                                    <select name="job_status" id="job_status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="jobstatusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Company</label>
                                <div class="col-sm-8">
                                    <select name="company" id="company" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <select name="department" id="department" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="departmentError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Default Cost Sharing</label>
                                <div class="col-sm-8">
                                    <select name="default_cost_sharing" id="default_cost_sharing" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="default_cost_sharingError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Expected Employees</label>
                                <div class="col-sm-8">
                                    <input type="text" name="expected_new_employee" id="expected_new_employee" class="form-control form-control-sm" value="0" readonly>
                                    <span class="invalid-feedback" role="alert" id="expected_new_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Existing Employees</label>
                                <div class="col-sm-8">
                                    <input type="text" name="existing_employee" id="existing_employee" class="form-control form-control-sm" value="0" readonly>
                                    <span class="invalid-feedback" role="alert" id="existing_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
                                <label class="col-sm-4 col-form-label">Unfulfilled Employees</label>
                                <div class="col-sm-8 col-form-label">
                                    <input type="text" name="total_forecasted_employee" id="total_forecasted_employee" class="form-control form-control-sm" value="0" readonly>
                                    <span class="invalid-feedback" role="alert" id="total_forecasted_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group-sm row">
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
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_position_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_position-details" data-toggle="pill" href="#position-details" role="tab" aria-controls="link_tab_position-details" aria-selected="true">Position Details <span class="error-tab text-red"></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="link_tab_job-descriptions" data-toggle="pill" href="#job-descriptions" role="tab" aria-controls="link_tab_job-descriptions" aria-selected="false">Job Description & Requirement<span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tab_position_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="position-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="col-sm-3" style="margin-bottom:-30px;">
                                           <div style="font-size:16px;">Showing <b class="showing"></b> Entries</div>
                                    </div>
                                    <div class="row justify-content-end">                                                                       
                                        <div class="col-sm-2 pull-right" style="margin-bottom: 20px;">
                                            <input type="text" id="search_menu_position" class="form-control form-control-sm" placeholder="Search Position Name" />
                                        </div>
                                        <div class="col-sm-2 pull-right">
                                            <input type="text" id="search_menu_employee" class="form-control form-control-sm" placeholder="Search Employee Name" />
                                        </div>
                                        <div class="col-sm-2 pull-right">
                                            <input type="text" id="search_menu_superior" class="form-control form-control-sm" placeholder="Search Superior Name" />
                                        </div>
                                        <div class="col-md-2 pull-right">
                                            <button type="button" class="pull-right btn btn-md btn-primary" id="new_position_detail"><span class="fas fa-plus"></span> Add Position Detail</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_job_position_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Position Route</th>
                                                        <th style="white-space:nowrap;">Position Detail Name</th>
                                                        <th style="white-space:nowrap;">Location</th>
                                                        <th style="white-space:nowrap;">Branch</th>
                                                        <th style="width:200px;">Principal</th>
                                                        <th style="white-space:nowrap;">Work Arround</th>
                                                        <th style="white-space:nowrap;">Cost Sharing</th>
                                                        <th style="white-space:nowrap;">Employee</th>
                                                        <th style="white-space:nowrap;">Superior Position</th>
                                                        <th style="white-space:nowrap;">Assign Company</th>
                                                        <th style="white-space:nowrap;">Inactive</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_job_position_detail_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_job_position_detailError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="job-descriptions" role="tabpanel" aria-labelledby="custom-content-below-profile-tab">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="col-sm-12 col-form-label">Job Description</label>
                                            <textarea class="form-control" name="detail_position_route_job_description" id="detail_position_route_job_description"></textarea>
                                            <span class="invalid-feedback" role="alert" id="detail_position_route_job_descriptionError">
                                                <strong></strong>
                                            </span>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label class="col-sm-12 col-form-label">Skill Requirement</label>
                                            <textarea class="form-control summernote" name="detail_position_route_skill_requirement" id="detail_position_route_skill_requirement"></textarea>
                                            <span class="invalid-feedback" role="alert" id="detail_position_route_skill_requirementError">
                                                <strong></strong>
                                            </span>
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
                <table id="sample_table_job_position_detail">
                    <tr id="">
                        <td><span class="sn"></span>.</td>
                        <td>
                            <div style="width:200px" class="input-group-append">
                                <select name="detail_position_route[0][id_position_routing]" id="detail_position_route_0_id_position_routing" class="form-control form-control-sm select2 id_position_routing_input" style="width: 85%;"></select>
                                <span class="invalid-feedback id_position_routing_input_error" role="alert" id="detail_position_route_0_id_position_routingError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:200px">
                                <input type="hidden" name="detail_position_route[0][id_position_detail]" id="detail_position_route_0_id_position_detail" class="form-control form-control-sm id_position_detail_input">
                                <input type="text" name="detail_position_route[0][position_detail_name]" id="detail_position_route_0_position_detail_name" class="form-control form-control-sm position_detail_name_input">
                                <span class="invalid-feedback position_detail_name_input_error" role="alert" id="detail_position_route_0_position_detail_nameError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:150px" class="input-group-append">
                                <select name="detail_position_route[0][location]" id="detail_position_route_0_location" class="form-control form-control-sm select2 location_input" style="width: 85%;"></select>
                                <span class="invalid-feedback location_input_error" role="alert" id="detail_position_route_0_locationError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:150px" class="input-group-append">
                                <select name="detail_position_route[0][branch_operating_unit]" id="detail_position_route_0_branch_operating_unit" class="form-control form-control-sm select2 branch_operating_unit_input" style="width: 85%;"></select>
                                <span class="invalid-feedback branch_operating_unit_input_error" role="alert" id="detail_position_route_0_branch_operating_unitError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <select name="detail_position_route[0][principal][]" id="detail_position_route_0_principal" class="form-control form-control-sm select2 principal_input" style="width: 100%;" multiple="multiple"></select>
                            <span class="invalid-feedback principal_input_error" role="alert" id="detail_position_route_0_principalError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <select name="detail_position_route[0][work_arround][]" id="detail_position_route_0_work_arround" class="form-control form-control-sm select2 work_arround_input" style="width: 100%;" multiple="multiple"></select>
                            <span class="invalid-feedback work_arround_input_error" role="alert" id="detail_position_route_0_work_arroundError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <select name="detail_position_route[0][cost_sharing][]" id="detail_position_route_0_cost_sharing" class="form-control form-control-sm select2 cost_sharing_input" style="width: 100%;" multiple="multiple"></select>
                            <span class="invalid-feedback cost_sharing_input_error" role="alert" id="detail_position_route_0_cost_sharingError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <div style="width:200px" class="input-group-append">
                                <select name="detail_position_route[0][employee]" id="detail_position_route_0_employee" class="form-control form-control-sm select2 employee_input" style="width: 85%;"></select>
                                <span class="invalid-feedback employee_input_error" role="alert" id="detail_position_route_0_employeeError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:300px" class="input-group-append">
                                <select name="detail_position_route[0][superior_position]" id="detail_position_route_0_superior_position" class="form-control form-control-sm select2 superior_position_input" style="width: 85%;"></select>
                                <span class="invalid-feedback superior_position_input_error" role="alert" id="detail_position_route_0_superior_positionError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:150px">
                                <select name="detail_position_route[0][assign_company]" id="detail_position_route_0_assign_company" class="form-control form-control-sm select2 assign_company_input" style="width: 85%;" id_element="0" ></select>
                                <span class="invalid-feedback assign_company_input_error" role="alert" id="detail_position_route_0_assign_companyError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <input type="checkbox" name="detail_position_route[0][inactive]" id="detail_position_route_0_inactive" class="form-control form-control-sm inactive_input">
                            <span class="invalid-feedback inactive_input_error" role="alert" id="detail_position_route_0_inactiveError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                    <center>
                        <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="fas fa-trash"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style type="text/css">
    .modal-lg, .modal-xl {
        max-width: 90% !important;
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
    let global_id_routing = "";
    let global_id_position_detail = 0;
    let global_location = [];
    let global_branch_operating_unit = [];
    let global_principal = [];
    let global_work_arround = [];
    let global_cost_sharing = [];
    let global_employee = [];
    let global_superior_position = [];
    let global_id_position_routing = [];
    let global_assign_company = [];
    let list_superior = [];
    let list_employee_custom = {};
    let list_superior_custom = {};
    let list_location_custom = {};
    let list_branch_custom = {};
    let list_position_routing_custom = {};

    $(document).on('keyup', '#search_menu_position', function (event) {
        var text = $(this).val();
        let count = 0;
        $('#table_job_position_detail_body tr').each(function () {
            let index = $(this).attr('id').split("rec-")[1];
            if(text != ''){
                $(`#rec-${index}`).hide();              
                let filter_element_pos = $(this).find('input#detail_position_route_'+index+'_position_detail_name[value*="'+text+'"]').val();

                if(filter_element_pos != undefined){
                    count = count + 1;
                    $(`#rec-${index}`).show();
                }
            } else {
                $(`#rec-${index}`).show();
            }
        });
        $('.showing').html(count);
    });
    
    $(document).on('keyup', '#search_menu_employee', function (event) {
        var text = $(this).val();
        $('#table_job_position_detail_body tr').each(function () {
            let index = $(this).attr('id').split("rec-")[1];
            if(text != ''){
                $(`#rec-${index}`).hide();              
                let filter_element_emp = $(this).find('span#select2-detail_position_route_'+index+'_employee-container:contains('+text+')').text().substring(1);
                if(filter_element_emp != ''){
                    $(`#rec-${index}`).show();
                }
            } else {
                $(`#rec-${index}`).show();
            }
        });
    });

    $(document).on('keyup', '#search_menu_superior', function (event) {
        var text = $(this).val();
        $('#table_job_position_detail_body tr').each(function () {
            let index = $(this).attr('id').split("rec-")[1];
            if(text != ''){
                $(`#rec-${index}`).hide();              
                let filter_element_emp = $(this).find('span#select2-detail_position_route_'+index+'_superior_position-container:contains('+text+')').text().substring(1);
                if(filter_element_emp != ''){
                    $(`#rec-${index}`).show();
                }
            } else {
                $(`#rec-${index}`).show();
            }
        });
    });

    $(document).on('change', '.employee_input', function (e, isTriggered) {
        // if($(this).select2('data').length > 0){
            if (!isTriggered){ // change by human
                get_existing_employee();
            } 
        // }
    });

    $(function () {
        $('.summernote').summernote({height:300});

        $('#job_position_routeForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $("#job_position_routeForm input").removeClass("is-invalid");
            $("#job_position_routeForm textarea").removeClass("is-invalid");
            $(".error-tab").html("");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
                url: global_id_routing == '' ? '<?= url('organization/organization_structure/job_position_route/save_position_route') ?>' : '<?= url('organization/organization_structure/job_position_route/save_update_position_route') . '?id_routing=' ?>' + global_id_routing,
                data: formData,
                beforeSend: function () {
                        $('#loader').removeClass('hidden');
                    },
                success: function (response) {
                    if (response.status == 'false') {
                        swal({
                            icon: "error",
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + response.message + ']'
                        });
                    } else if (response.status == 'true') {
                        $('#modal_form_job_position_route').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        loadTable()
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
                            var key_temp = key.replaceAll(".", "_");
                            $("#" + key_temp).addClass("is-invalid");
                            $("#" + key_temp + "Error").children("strong").text(errors[key][0].replaceAll("detail_position_route.", ""));
                            var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
                            if (tab_id != undefined) {
                                $("#tab_position_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
                            }
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                        });
                    }
                },
                complete: function(){
                    $('#loader').addClass('hidden')
                },
            });
        });


        $(document).on('click', '#new_position_detail', function (event) {
            var content = jQuery('#sample_table_job_position_detail tr'),
                    size = global_id_position_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id', 'rec-' + size);
            element.find('.delete-record').attr('data-id', size);

            element.find('.id_position_routing_input').attr('id', 'detail_position_route_' + size + '_id_position_routing');
            element.find('.id_position_routing_input').attr('name', 'detail_position_route[' + size + '][id_position_routing]');
            element.find('.id_position_routing_input_error').attr('id', 'detail_position_route_' + size + '_id_position_routingError');
            if (!(event.originalEvent === undefined)){ // change by human
                element.find('.id_position_routing_input').select2({
                    placeholder: "Select Position Route..",
                    allowClear: true,
                    data: global_id_position_routing
                });
                let id_pos_routing = global_id_routing != '' ? global_id_routing : '';
                element.find('.id_position_routing_input').val(id_pos_routing).trigger('change');
            } 

            element.find('.id_position_detail_input').attr('id', 'detail_position_route_' + size + '_id_position_detail');
            element.find('.id_position_detail_input').attr('name', 'detail_position_route[' + size + '][id_position_detail]');
            element.find('.position_detail_name_input').attr('id', 'detail_position_route_' + size + '_position_detail_name');
            element.find('.position_detail_name_input').attr('name', 'detail_position_route[' + size + '][position_detail_name]');
            element.find('.position_detail_name_input_error').attr('id', 'detail_position_route_' + size + '_position_detail_nameError');

            element.find('.location_input').attr('id', 'detail_position_route_' + size + '_location');
            element.find('.location_input').attr('name', 'detail_position_route[' + size + '][location]');
            element.find('.location_input_error').attr('id', 'detail_position_route_' + size + '_locationError');
            if (!(event.originalEvent === undefined)){ // change by human
                element.find('.location_input').select2({
                    placeholder: "Select Location ..",
                    allowClear: true,
                    data: global_location
                });
                element.find('.location_input').val('').trigger('change');
            } 
            /*.on('change', function (e) {
                
                $('#detail_position_route_' + size + '_work_arround').val([$(this).val()]).trigger('change');
            }).trigger('change');
            */

            element.find('.branch_operating_unit_input').attr('id', 'detail_position_route_' + size + '_branch_operating_unit');
            element.find('.branch_operating_unit_input').attr('name', 'detail_position_route[' + size + '][branch_operating_unit]');
            element.find('.branch_operating_unit_input_error').attr('id', 'detail_position_route_' + size + '_branch_operating_unitError');
            if (!(event.originalEvent === undefined)){ // change by human
                element.find('.branch_operating_unit_input').select2({
                    placeholder: "Select Branch ..",
                    allowClear: true,
                    data: global_branch_operating_unit
                });
                element.find('.branch_operating_unit_input').val('').trigger('change');
            } 

            element.find('.principal_input').attr('id', 'detail_position_route_' + size + '_principal');
            element.find('.principal_input').attr('name', 'detail_position_route[' + size + '][principal][]');
            element.find('.principal_input_error').attr('id', 'detail_position_route_' + size + '_principalError');
            element.find('.principal_input').select2({
                width:'150px',
                data: global_principal
            });

            element.find('.work_arround_input').attr('id', 'detail_position_route_' + size + '_work_arround');
            element.find('.work_arround_input').attr('name', 'detail_position_route[' + size + '][work_arround][]');
            element.find('.work_arround_input_error').attr('id', 'detail_position_route_' + size + '_work_arroundError');
            element.find('.work_arround_input').select2({
                width:'150px',
                data: global_work_arround
            });
            get_group_branch(element,size);
            
            element.find('.cost_sharing_input').attr('id', 'detail_position_route_' + size + '_cost_sharing');
            element.find('.cost_sharing_input').attr('name', 'detail_position_route[' + size + '][cost_sharing][]');
            element.find('.cost_sharing_input_error').attr('id', 'detail_position_route_' + size + '_cost_sharingError');
            element.find('.cost_sharing_input').select2({
                data: global_cost_sharing
            });
            // element.find('.cost_sharing_input').val([$('#default_cost_sharing').val()]).trigger('change');

            element.find('.employee_input').attr('id', 'detail_position_route_' + size + '_employee');
            element.find('.employee_input').attr('name', 'detail_position_route[' + size + '][employee]');
            element.find('.employee_input_error').attr('id', 'detail_position_route_' + size + '_employeeError');
            if (!(event.originalEvent === undefined)){ // change by human
                element.find('.employee_input').select2({
                    placeholder: "Select Employee",
                    allowClear: true,
                    data: global_employee
                });
                element.find('.employee_input').val('').trigger('change', [true]);
            } 

            element.find('.superior_position_input').attr('id', 'detail_position_route_' + size + '_superior_position');
            element.find('.superior_position_input').attr('name', 'detail_position_route[' + size + '][superior_position]');
            element.find('.superior_position_input_error').attr('id', 'detail_position_route_' + size + '_superior_positionError');
            if (!(event.originalEvent === undefined)){ // change by human
                element.find('.superior_position_input').select2({
                    placeholder: "Select Superior Position ..",
                    allowClear: true,
                    data: global_superior_position
                });
                element.find('.superior_position_input').val('').trigger('change');
            } 
            
            element.find('.assign_company_input').attr('id', 'detail_position_route_' + size + '_assign_company');
            element.find('.assign_company_input').attr('name', 'detail_position_route[' + size + '][assign_company]');
            element.find('.assign_company_input').attr('id_element', size);
            element.find('.assign_company_input_error').attr('id', 'detail_position_route_' + size + '_assign_companyError');
            element.find('.assign_company_input').select2({
                placeholder: "Select Company ..",
                allowClear: true,
                data: global_assign_company
            });
            element.find('.assign_company_input').val('').trigger('change');


            element.find('.inactive_input').attr('id', 'detail_position_route_' + size + '_inactive');
            element.find('.inactive_input').attr('name', 'detail_position_route[' + size + '][inactive]');
            element.find('.inactive_input_error').attr('id', 'detail_position_route_' + size + '_inactiveError');

            element.appendTo('#table_job_position_detail_body');
            let expected_new_employee = 0;
            $('#expected_new_employee').val(expected_new_employee);
            $('#table_job_position_detail_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
                if (!$(this).find('.inactive_input').is(':checked')) {
                    expected_new_employee++;
                    $('#expected_new_employee').val(expected_new_employee);             
                }           
            });
            var total_forecasted_employee = expected_new_employee - parseInt($('#existing_employee').val());
            $('#total_forecasted_employee').val(total_forecasted_employee);
        });

        $(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_job_position_detail_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            get_existing_employee();
            setTimeout(function () {
                $('#expected_new_employee').val((jQuery('#table_job_position_detail >tbody >tr').length));
                var total_forecasted_employee = (jQuery('#table_job_position_detail >tbody >tr').length) - parseInt($('#existing_employee').val());
                $('#total_forecasted_employee').val(total_forecasted_employee);
            }, 10);
            return true;
        });

        $(document).on('click', '.new', function () {
            global_id_routing = "";
            $("#job_position_routeForm")[0].reset();
            $("#table_job_position_detail_body").html("");
            $("#job_position_routeForm .modal-title").html("<span class='fas fa-plus'></span> Form Job Position Route");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#job_position_routeForm input").removeClass("is-invalid");
            $("#job_position_routeForm textarea").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            
            $('#job_position').trigger('change');
            $('#parent_job').trigger('change');
            $('#grade').trigger('change');
            $('#job_status').trigger('change');
            $('#company').trigger('change');
            $('#department').trigger('change');
            $('#default_cost_sharing').trigger('change');
            
            $('.summernote').summernote('reset');
            $('#modal_form_job_position_route').modal('show');
        });

        $(document).on('change', '.inactive_input', function () {
            let expected_new_employee = 0;
            $('#expected_new_employee').val(expected_new_employee);
            $('#table_job_position_detail_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
                if (!$(this).find('.inactive_input').is(':checked')) {
                    expected_new_employee++;
                    $('#expected_new_employee').val(expected_new_employee);
                }
            });
            var total_forecasted_employee = expected_new_employee - parseInt($('#existing_employee').val());
            $('#total_forecasted_employee').val(total_forecasted_employee);
        });

        $(document).on('click', '.edit', function () {
            let id_routing = $(this).attr('id');
            global_id_routing = id_routing;
            $("#job_position_routeForm")[0].reset();
            $("#table_job_position_detail_body").html("");
            $("#job_position_routeForm .modal-title").html("<span class='fas fa-edit'></span> Edit Job Position Route");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#job_position_routeForm input").removeClass("is-invalid");
            $("#job_position_routeForm textarea").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "<?= url('organization/organization_structure/job_position_route/get_detail_position_route') ?>",
                method: "GET",
                data: {id_routing: id_routing},
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    global_id_position_detail = 0;
                    $.each(response.detail_position_route, function (i, item) {
                        $('#new_position_detail').trigger('click');
                        $('#expected_new_employee').val(0);
                    });
                    
                    $('#job_position').val(response.id_position).trigger('change');
                    $('#job_position_route').val(response.description);
                    $('#parent_job').val(response.parent_id_routing).trigger('change');
                    $('#grade').val(response.id_job_grade).trigger('change');
                    $('#job_status').val(response.id_job_status).trigger('change');
                    $('#company').val(response.id_company).trigger('change');
                    $('#department').val(response.description).trigger('change');
                    $('#default_cost_sharing').val(response.default_cost_sharing).trigger('change');
                    $('#status').val(response.status);
                    $('#detail_position_route_job_description').val(response.job_description_detail);
                    $('#detail_position_route_skill_requirement').summernote("code", response.skill_requirement);

                    $('#table_job_position_detail_body tr').each(function (index) {
                        $(this).find('span.sn').html(index + 1);

                        let thisIdPositionRouting = response.detail_position_route[index].id_position_routing;
                        let element_position_routing = `#detail_position_route_${index}_id_position_routing`;
                        showPositionRouting(element_position_routing, thisIdPositionRouting, index)

                        $(this).find('.id_position_detail_input').val(response.detail_position_route[index].id_position_detail);
                        $(this).find('.position_detail_name_input').val(response.detail_position_route[index].position_detail_name);
                        $(this).find('.position_detail_name_input').attr('value',response.detail_position_route[index].position_detail_name);

                        let thisIdLocation = response.detail_position_route[index].location;
                        let element_location = `#detail_position_route_${index}_location`;
                        showLocation(element_location, thisIdLocation, index)

                        let thisIdBranch = response.detail_position_route[index].branch_operating_unit;
                        let element_branch = `#detail_position_route_${index}_branch_operating_unit`;
                        showBranch(element_branch, thisIdBranch, index)

                        $(this).find('.assign_company_input').val(response.detail_position_route[index].assign_company).trigger('change', [true]);

                        let assignedIdCompany = response.detail_position_route[index].assign_company;
                        let thisIdEmployee = response.detail_position_route[index].employee;
                        let element_employee = `#detail_position_route_${index}_employee`;

                        if(response.detail_position_route[index].assign_company != null){
                            showEmployeeWithAssigned(assignedIdCompany, element_employee, thisIdEmployee, index)
                        } else {
                            showEmployeeWithAssigned('', element_employee, thisIdEmployee, index)
                        }

                        let thisIdSuperior = response.detail_position_route[index].superior_position;
                        let element_superior = `#detail_position_route_${index}_superior_position`;
                        showSuperiorPosition(element_superior, thisIdSuperior, index)

                        $(this).find('.principal_input').val(response.detail_position_route[index].principal).trigger('change');
                        $(this).find('.work_arround_input').val(response.detail_position_route[index].work_arround).trigger('change', [true]);
                        $(this).find('.cost_sharing_input').val(response.detail_position_route[index].cost_sharing).trigger('change');
                        if (response.detail_position_route[index].inactive == 'I') {
                            $(this).find('.inactive_input').prop('checked', true);
                        } else {
                            $(this).find('.inactive_input').prop('checked', false);
                        }
                    });

                    $('#expected_new_employee').val(response.expected_employee);
                    $('#existing_employee').val(response.existing_employee);
                    var total_forecasted_employee = response.expected_employee - response.existing_employee;
                    $('#total_forecasted_employee').val(total_forecasted_employee);
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

            $('#modal_form_job_position_route').modal('show');
        });

        $(document).on('click', '.delete', function (event) {
            let id_routing = $(this).attr('id');
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
                        url: "<?= url('organization/organization_structure/job_position_route/destroy_position_route') ?>",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {id_routing: id_routing},
                        success: function (response) {
                            if (response.status == 'false') {
                                swal({
                                    icon: "error",
                                    title: 'Oops...',
                                    dangerMode: true,
                                    text: 'Something went wrong! [' + response.message + ']'
                                });
                            } else if (response.status == 'true') {
                                $('#modal_form_job_position_route').modal('hide');
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
        loadTable()
    });

    function loadTable() {
        $('#table_job_position_route').DataTable({
            processing: true,
            pageLength: 10,
            responsive: true,
            destroy:true,
            ajax: {
               url: '<?= url('organization/organization_structure/job_position_route/get_data') ?>',
               error: function (jqXHR, textStatus, errorThrown) {
                        $('#table_job_position_route').DataTable().ajax.reload();
                    }
            },
            columns: [
                {
                defaultContent: '',
                orderable: false,
                },
                {   // Checkbox select column
                    data: 'id_routing',
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'job_position', name: 'job_position'},
                {data: 'description', name: 'description'},
                {data: 'department_name', name: 'department_name'},
                {data: 'expected_employee', name: 'expected_employee',width:'10'},
                {data: 'existing_employee', name: 'existing_employee',width:'10'},
                {data: 'total_forecast_employee', name: 'total_forecast_employee',width:'10'},
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
    }

    function refresh_data() {
        /**************** Load detail dropdown **************************/       
        get_job_position();
        get_parent_job();
        get_grade();
        get_job_status();
        get_company();
        get_default_cost_sharing();
        get_location();
        get_branch_operating_unit();
        get_principal();
        get_work_arround();
        get_cost_sharing();
        get_employee();
        get_superior_position();
        // $('#table_job_position_route').DataTable().ajax.reload();
    }
    
    function get_job_position(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_job_position') ?>', function (data) {
            $('#job_position').select2({
                placeholder: "Select Job Position ..",
                allowClear: true,
                data: data
            }).on('change', function (e) {
                $.getJSON('<?= url('organization/organization_structure/job_position_route/get_department') . '?id_dept=' ?>' + $(this).select2('data')[0].id_dept, function (data) {
                    $('#department').empty();
                    $('#department').select2({
                        data: data
                    });
                });
            }).trigger('change');
        }).fail(function (data) { // Call failed
            get_job_position();
        });
    }
    function get_parent_job(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_parent_job') ?>', function (data) {
            $(data).each(function (i, val) {
                global_id_position_routing.push({id:val.id, text:val.text+' ('+val.position+')'});
            });
            // global_id_position_routing = data;
            $(global_id_position_routing).each(function (i, val) {
                list_position_routing_custom[val.id] = [global_id_position_routing[i]];
            });

            $('#parent_job').select2({
                placeholder: "Select Parent Job ..",
                allowClear: true,
                data: data
            });
            $('#parent_job').on('change', function (e) {
                if($('#parent_job').val() == null){
                    $('#table_job_position_detail').find('.superior_position_input').each(function (i, obj) {
                        $('#' + obj.id).empty();
                    });
                }
            });
        }).fail(function (data) { // Call failed
            get_parent_job();
        });
    }
    function get_grade(){
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_grade') ?>', function (data) {
            $('#grade').select2({
                placeholder: "Select Grade ..",
                allowClear: true,
                data: data
            }).trigger('change');
        }).fail(function (data) { // Call failed
            get_grade();
        });  
    }
    function get_job_status(){
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_job_status') ?>', function (data) {
            $('#job_status').select2({
                placeholder: "Select Job Status ..",
                allowClear: true,
                data: data
            }).trigger('change');
        }).fail(function (data) { // Call failed
            get_job_status();
        });       
    }

    $(document).on("change", "td .assign_company_input", function (e, isTriggered) {
        if (!isTriggered){ // change by human
            let id_element = $(this).attr('id_element');
            let assignedIdCompany = $(this).val();
            let element = `#detail_position_route_${id_element}_employee`;
            let id_employee = $(element).val();

            getEmployeeWithAssigned(assignedIdCompany, true).then(list => {
                if(id_employee!=null && list_employee_custom.hasOwnProperty(id_employee)){
                    // list.push(...list_employee_custom[id_employee]);
                }
                $(element).html('').prepend('<option></option>').select2({
                    placeholder: "Select Employee",
                    data: list,
                    allowClear: true,
                });
                if(id_employee!='' || id_employee!=null){
                    $(element).val(id_employee).trigger('change', [true]).attr('readonly', 'readonly');
                    $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editEmployeeWithAssigned${id_element}" onclick="editEmployeeWithAssigned('${element}', '${id_employee}', '${id_element}', '${assignedIdCompany}')" ></span>`));
                    $(element).val(id_employee).trigger('change', [true]).attr('readonly', false);
                    $(`span#editEmployeeWithAssigned${id_element}`).remove();
                }
            });

            // showEmployeeWithAssigned(assignedIdCompany, element_employee, '', id_element, true);
            get_existing_employee()
        }
    });

    $(document).on("click", "#table_job_position_detail_body tr", function (e, isTriggered) {
        let row_id = $(this).attr('id').split('rec-')[1];
        $(this).append(`<input type="hidden" value="${row_id}" name="row_id[${row_id}]"> `);
    });


    const getEmployeeWithAssigned = async (id_company='', vacant=false, id_employee='') => {
        try {
            let result;
            let param = {};
            if(id_company != ''){
                param.assignedIdCompany = id_company;
            } 
            if(vacant != false){
                param.vacant = true;
            } 
            param.id_employee = id_employee;
            result = await $.ajax({
                url: '<?= url('organization/organization_structure/job_position_route/get_employee') ?>',
                method: "GET",
                data: param,
                success: function (res) {
                },
            });
            return result;
        } catch (error) {
            getEmployeeWithAssigned(id_company, vacant);
        }
    }

    const get_superior_position = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('organization/organization_structure/job_position_route/get_superior_position') ?>',
                method: "GET",
                success: function (res) {
                    global_superior_position = res;
                    list_superior = res;
                    $(list_superior).each(function (i, val) {
                        list_superior_custom[val.id] = [list_superior[i]];
                    });
                },
            });
            return result;
        } catch (error) {
            get_superior_position();
        }
    }

    const showEmployeeWithAssigned = async (assignedIdCompany='', element='', id_employee='', index, triggered=false) => {
        if(assignedIdCompany==''){
            $(element).html('').prepend('<option></option>').select2({
                placeholder: "Select Employee",
                // data: global_employee,
                data: list_employee_custom[id_employee],
                allowClear: true,
            });
            $(element).val(id_employee).trigger('change', [true]).attr('readonly', 'readonly');
            $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editEmployeeWithAssigned${index}" onclick="editEmployeeWithAssigned('${element}', '${id_employee}', '${index}', '${assignedIdCompany}')" ></span>`));
            if(triggered){
                $(element).val(id_employee).trigger('change', [true]).attr('readonly', false);
                $(`span#editEmployeeWithAssigned${index}`).remove();
            }
        } else {
            await getEmployeeWithAssigned(assignedIdCompany).then(list => {
                $(element).html('').prepend('<option></option>').select2({
                    placeholder: "Select Employee",
                    data: list,
                    allowClear: true,
                });
                if(id_employee!='' || id_employee!=null){
                    $(element).val(id_employee).trigger('change', [true]).attr('readonly', 'readonly');
                    $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editEmployeeWithAssigned${index}" onclick="editEmployeeWithAssigned('${element}', ${id_employee}, '${index}', '${assignedIdCompany}')" ></span>`));
                    if(triggered){
                        $(element).val(id_employee).trigger('change', [true]).attr('readonly', false);
                        $(`span#editEmployeeWithAssigned${index}`).remove();
                    }
                }
            });
        }
    }   

    const editEmployeeWithAssigned = async (element='', id_employee=null, index, assignedIdCompany) => {
        getEmployeeWithAssigned(assignedIdCompany, true, id_employee).then(list => {
            if(id_employee!=null && list_employee_custom.hasOwnProperty(id_employee)){
                list.push(...list_employee_custom[id_employee]);
            }
            $(element).html('').prepend('<option></option>').select2({
                placeholder: "Select Employee",
                data: list,
                allowClear: true,
            });
            if(id_employee!='' || id_employee!=null){
                $(element).val(id_employee).trigger('change', [true]).attr('readonly', false);
            } else {
                $(element).val([]).trigger('change', [true]).attr('readonly', false);
            }
            $(`span#editEmployeeWithAssigned${index}`).remove();
        });
    }   

    const showSuperiorPosition = async (element='', id_superior='', index, allOption=false) => {
        let optionSuperior;
        if(id_superior!='' || id_superior!=null){
            if(allOption != false){
                optionSuperior = list_superior;
            } else {
                optionSuperior = list_superior_custom[id_superior];
            }
        } else {
            optionSuperior = list_superior;
        }
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Superior",
            data: optionSuperior,
            allowClear: true,
        });
        $(element).val(id_superior).trigger('change').attr('readonly', 'readonly');
        $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editSuperiorPosition${index}" onclick="editSuperiorPosition('${element}', '${id_superior}', '${index}')" ></span>`));
    } 

    const editSuperiorPosition = async (element='', id_superior='', index) => {
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Superior",
            data: list_superior,
            allowClear: true,
        });
        $(element).val(id_superior).trigger('change').attr('readonly', false);
        $(`span#editSuperiorPosition${index}`).remove();
    } 

    const showPositionRouting = async (element='', id_position_routing='', index, allOption=false) => {
        let optionPositionRouting;
        if(id_position_routing!='' || id_position_routing!=null){
            if(allOption != false){
                optionPositionRouting = list_position_routing_custom;
            } else {
                optionPositionRouting = list_position_routing_custom[id_position_routing];
            }
        } else {
            optionPositionRouting = list_position_routing_custom;
        }
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Position Routing",
            data: optionPositionRouting,
            allowClear: true,
        });
        $(element).val(id_position_routing).trigger('change').attr('readonly', 'readonly');
        $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editPositionRouting${index}" onclick="editPositionRouting('${element}', '${id_position_routing}', '${index}')" ></span>`));
    } 

    const editPositionRouting = async (element='', id_position_routing='', index) => {
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Position Routing",
            data: global_id_position_routing,
            allowClear: true,
        });
        $(element).val(id_position_routing).trigger('change').attr('readonly', false);
        $(`span#editPositionRouting${index}`).remove();
    }   

    const showLocation = async (element='', id_location='', index, allOption=false) => {
        if(id_location!='' || id_location!=null){
            if(allOption != false){
                optionLocation = global_location;
            } else {
                optionLocation = list_location_custom[id_location];
            }
        } else {
            optionLocation = global_location;
        }
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Location",
            data: optionLocation,
            allowClear: true,
        });
        $(element).val(id_location).trigger('change').attr('readonly', 'readonly');
        $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editLocation${index}" onclick="editLocation('${element}', '${id_location}', '${index}')" ></span>`));
    } 

    const editLocation = async (element='', id_location='', index) => {
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Location",
            data: global_location,
            allowClear: true,
        });
        $(element).val(id_location).trigger('change').attr('readonly', false);
        $(`span#editLocation${index}`).remove();
    }   

    const showBranch = async (element='', id_branch='', index, allOption=false) => {
        if(id_branch!='' || id_branch!=null){
            if(allOption != false){
                optionLocation = global_branch_operating_unit;
            } else {
                optionLocation = list_branch_custom[id_branch];
            }
        } else {
            optionLocation = global_branch_operating_unit;
        }
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Branch",
            data: optionLocation,
            allowClear: true,
        });
        $(element).val(id_branch).trigger('change').attr('readonly', 'readonly');
        $(element).parent().append($(`<span class="input-group-text fa fa-pencil form-control-sm" id="editBranch${index}" onclick="editBranch('${element}', '${id_branch}', '${index}')" ></span>`));
    }   

    const editBranch = async (element='', id_branch='', index) => {
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Branch",
            data: global_branch_operating_unit,
            allowClear: true,
        });
        $(element).val(id_branch).trigger('change').attr('readonly', false);
        $(`span#editBranch${index}`).remove();
    }   

    function get_company(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_company') ?>', function (data) {
             $('#company').select2({
                 placeholder: "Select Company ..",
                 allowClear: true,
                 data: data,
             });
         }).fail(function (data) { // Call failed
             get_company();
         });  
        
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_assign_company') ?>', function (data) {
            global_assign_company = data;
        }).fail(function (data) { // Call failed
            get_company();
        });  
    }
    function get_default_cost_sharing(){
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_default_cost_sharing') ?>', function (data) {
            $('#default_cost_sharing').select2({
                placeholder: "Select Default Cost Sharing ..",
                allowClear: true,
                data: data
            }).on('change', function (e) {
                $('#table_job_position_detail').find('.cost_sharing_input').each(function (i, obj) {
                    $('#' + obj.id).val([$('#default_cost_sharing').val()]).trigger('change');
                });
            }).trigger('change');
        }).fail(function (data) { // Call failed
            get_default_cost_sharing();
        });  
    }
    function get_location(){
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_location') ?>', function (data) {
            global_location = data;
            $(global_location).each(function (i, val) {
                list_location_custom[val.id] = [global_location[i]];
            });

        }).fail(function (data) { // Call failed
            get_location();
        });       
    }
    function get_branch_operating_unit(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_branch_operating_unit') ?>', function (data) {
            global_branch_operating_unit = data;
            $(global_branch_operating_unit).each(function (i, val) {
                list_branch_custom[val.id] = [global_branch_operating_unit[i]];
            });
        }).fail(function (data) { // Call failed
            get_branch_operating_unit();
        });       
    }
    function get_principal(){
        $.getJSON('<?= url('organization/organization_structure/job_position_route/get_principal') ?>', function (data) {
            global_principal = data;
        }).fail(function (data) { // Call failed
            get_principal();
        });
    }
    function get_work_arround(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_work_arround') ?>', function (data) {
            global_work_arround = data;
        }).fail(function (data) { // Call failed
            get_work_arround();
        });
    }
    function get_cost_sharing(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_cost_sharing') ?>', function (data) {
            global_cost_sharing = data;
        }).fail(function (data) { // Call failed
            get_cost_sharing();
        });
    }
    function get_employee(){
         $.getJSON('<?= url('organization/organization_structure/job_position_route/get_employee') ?>', function (data) {
            global_employee = data;
            $(global_employee).each(function (i, val) {
                list_employee_custom[val.id] = [global_employee[i]];
            });
            
        }).fail(function (data) { // Call failed
            get_employee();
        });
    }
    function get_existing_employee() {
        let existing_employee = $('#table_job_position_detail_body tr select.employee_input').filter(function(){return $(this).val() != null}).length;
        $('#existing_employee').val(existing_employee);
        
         var total_forecasted_employee = $('#expected_new_employee').val() - $('#existing_employee').val();
         $('#total_forecasted_employee').val(total_forecasted_employee);
    }
    function get_group_branch(element, i){
        element.find('.work_arround_input').on('select2:open', function(e, isTriggered) {
            if (!isTriggered){ // change by human
                $('#select2-detail_position_route_'+i+'_work_arround-results').on('click', function(event) {
                    event.stopPropagation();
                    var data = $(event.target).html();
                    var selectedOptionGroup = data.toString().trim();
                    var groupchildren = [];
                        for (var i = 0; i < global_work_arround.length; i++) {
                          if (selectedOptionGroup.toString() === global_work_arround[i].text.toString()) {
                            for (var j = 0; j < global_work_arround[i].children.length; j++) {
                              groupchildren.push(global_work_arround[i].children[j].id);
                            }
                          }
                        }
                    var options = [];
                    options = element.find('.work_arround_input').val();
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
                    element.find('.work_arround_input').val(options);
                    element.find('.work_arround_input').trigger('change'); // Notify any JS components that the value changed
                    element.find('.work_arround_input').select2('close');    

                });
            }
        });
    }

</script>
@stop