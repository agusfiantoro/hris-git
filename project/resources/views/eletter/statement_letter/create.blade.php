<div class="modal fade text-left" id="modal_form_sk" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="myModalLabel1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Form Employee Movement Decree (SK)</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form id="skForm" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" id="date" name="date" class="form-control form-control-sm value_select_search_old" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="dateError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                      <div class="row">
                        <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_employee" name="id_employee" class="form-control form-control-sm select_opsi" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Reference Number Career <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                         <div class="input-group">
                            <select id="id_career_transaction" style="width: 88%;" name="id_career_transaction" aria-describedby="basic-addon2" class="form-control form-control-sm select_search_old" readonly>
                            </select>
                            <div class="input-group-append">
                                <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                            </div>
                            <span class="invalid-feedback d-block" role="alert" id="id_career_transactionError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12 text-left">
                <div class="form-group">
                    <label><b>Old Data</b></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Region <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_region_old" name="id_region" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Branch <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_branch_old" name="id_branch" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Location <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_location_old" name="id_location" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_locationError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Area <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <input autocomplete="off" name="remark_8" id="remark_8" class="form-control form-control-sm" style="width: 100%;">
                        <span class="invalid-feedback d-block" role="alert" id="remark_8Error">
                            <strong></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Position <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_position_detail_old" name="id_position_detail" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Department <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_dept_old" name="id_dept" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Old Division <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_principal_old" name="id_principal[]" multiple="multiple" class="form-control form-control-sm select_search_old readonly" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_principalError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-lg-12 text-left">
                <div class="form-group">
                    <label><b>New Data</b></label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <label class="col-sm-4 col-form-label">New Region <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_region_new" name="id_region_new" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_region_newError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">New Branch <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_branch_new" name="id_branch_new" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_branch_newError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">New Location <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_location_new" name="id_location_new" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_location_newError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">New Area <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <input autocomplete="off" name="remark_9" id="remark_9" class="form-control form-control-sm" style="width: 100%;">
                        <span class="invalid-feedback d-block" role="alert" id="remark_9Error">
                            <strong></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
             <div class="row">
                <label class="col-sm-4 col-form-label">New Position <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                   <select id="id_position_detail_new" name="id_position_detail_new" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
                   </select>
                   <span class="invalid-feedback d-block" role="alert" id="id_position_detail_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Department <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
               <select id="id_dept_new" name="id_dept_new" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
               </select>
               <span class="invalid-feedback d-block" role="alert" id="id_dept_newError">
                <strong></strong>
            </span>
        </div>
    </div>

    <div class="row">
        <label class="col-sm-4 col-form-label">New Division <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_principal_new" name="id_principal_new[]" multiple="multiple" class="form-control form-control-sm select_search_new" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_principal_newError">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
</div>
<div class="row mt-3">
  <div class="col-md-6">
      <div class="row">
        <label class="col-sm-4 col-form-label">Effective Date <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <input autocomplete="off" id="effective_date" name="effective_date" class="form-control value_select_search_old form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
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
            <select id="remark_1" name="remark_1" class="form-control form-control-sm select_opsi" style="width: 100%;">
            </select>
            <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Grade <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
         <select id="id_job_grade" name="id_job_grade" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
         </select>
         <span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
            <strong></strong>
        </span>
    </div>
</div>
<div class="row">
    <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
    <div class="col-sm-8">
        <select id="id_employment_status" name="id_employment_status" class="form-control form-control-sm select_search_old" style="width: 100%;" readonly>
        </select>
        <span class="invalid-feedback d-block" role="alert" id="id_employment_statusError">
            <strong></strong>
        </span>
    </div>
</div>
<div class="row">
    <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
    <div class="col-sm-8">
      <select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly>
      </select>
      <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
        <strong></strong>
    </span>
</div>
</div>
</div>
</div>
<div class="row">
    <div class="col-xl-12">
        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="4" id="notes" name="notes"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="notesError">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
 <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
    <button class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>