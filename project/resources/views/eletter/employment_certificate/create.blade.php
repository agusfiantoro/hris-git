<div class="modal fade" id="modal_form_skk"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-srcollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Certificate of Employee (SKK)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <form method="POST" id="skkForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                   <div class="col-md-6">
                    <div class="row">
                        <input type="hidden" id="id_letter" name="id_letter">
                        <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_category" name="id_category" class="form-control form-control-sm" style="width: 100%;"></select>
                            <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                           <input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
                           <span class="invalid-feedback d-block" role="alert" id="dateError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">No Name</label>
                    <div class="col-sm-8">
                        <input type="checkbox" class="mt-1" value="true" id="no_name" name="no_name" style="width: 20px;height: 20px;">
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                       <div class="input-group">
                           <select id="id_employee" name="id_employee" class="form-control-sm select_search" style="width: 88%;" readonly>
                           </select>
                           <input autocomplete="off" hidden="" name="name_input" id="name_input" class="form-control form-control-sm" style="width: 100%;">
                           <div class="input-group-append">
                            <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                        </div>
                    </div>
                    <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                        <strong></strong>
                    </span>
                    <span class="invalid-feedback d-block" role="alert" id="name_inputError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="emailError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Join Date <sup class="text text-danger" id="validate_effective">*</sup></label>
                <div class="col-sm-8">
                   <input autocomplete="off" name="effective_date" readonly="" id="effective_date" class="form-control date_change form-control-sm" style="width: 100%;">
                   <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger" id="validate_exp"></sup></label>
            <div class="col-sm-8">
                <input autocomplete="off" name="expired_date" readonly="" id="expired_date" class="form-control date_change form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_employee_chief" name="id_employee_chief" class="form-control form-control-sm select_opsi" style="width: 100%;">
             </select>
             <span class="invalid-feedback d-block" role="alert" id="id_employee_chiefError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="remark_2" name="remark_2" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
            <span class="invalid-feedback d-block" role="alert" id="remark_2Error">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_region" name="id_region" class="form-control form-control-sm" style="width: 100%;" readonly></select>
            <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_branch" name="id_branch" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="branch_input" id="branch_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="branch_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_position_detail" name="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="position_detail_input" id="position_detail_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="position_detail_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_dept" name="id_dept" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="dept_input" id="dept_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="dept_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_job_grade" name="id_job_grade" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="job_grade_input" id="job_grade_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="job_grade_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Division <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_principal" name="id_principal[]" multiple="" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="principal_input" id="principal_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_principalError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="principal_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_employment_status" name="id_employment_status" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <input autocomplete="off" hidden="" name="status_input" id="status_input" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="id_employment_statusError">
                <strong></strong>
            </span>
            <span class="invalid-feedback d-block" role="alert" id="status_inputError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
            <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
</div>
<div class="row" id="row_keterangan">
    <div class="col-lg-12">
        <label>Keperluan <sup class="text text-danger">*</sup></label>
        <textarea class="form-control" name="keterangan" id="keterangan" rows="4"></textarea>
        <span class="invalid-feedback d-block" role="alert" id="keteranganError">
            <strong></strong>
        </span>
    </div>
</div>
<!-- <div class="modal-loading"></div> -->
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
   <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>