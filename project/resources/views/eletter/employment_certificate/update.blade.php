<div class="modal fade" id="modal_form_skk_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="skkFormEdit" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Employment Certificates / SKK</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                     <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <select id="id_category_edit" name="id_category" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                                </select>
                                <input type="hidden" hidden="" id="id_letter" name="id_letter">
                                <span class="invalid-feedback d-block" role="alert" id="id_category_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="date" disabled="" id="date_edit" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="date_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                            </select>
                            <div class="input-group">
                                <select id="id_employee_edit" name="id_employee" class="form-control-sm select_search_edit" style="width: 88%;" readonly>
                                </select>
                                <div class="input-group-append">
                                    <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                                </div>
                            </div>
                            <span class="invalid-feedback d-block" role="alert" id="id_employee_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                          <input autocomplete="off" name="email" id="email_edit" class="form-control form-control-sm" style="width: 100%;">
                          <span class="invalid-feedback d-block" role="alert" id="email_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Join Date <sup class="text text-danger" class="validate_exp_edit">*</sup></label>
                    <div class="col-sm-8">
                       <input autocomplete="off" readonly="" name="effective_date" id="effective_date_edit" class="form-control form-control-sm" style="width: 100%;">
                       <span class="invalid-feedback d-block" role="alert" id="effective_date_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger" class="validate_exp_edit"></sup></label>
                <div class="col-sm-8">
                    <input autocomplete="off" readonly="" name="expired_date" id="expired_date_edit" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="expired_date_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                   <select id="id_employee_chief_edit" name="id_employee_chief" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                   </select>
                   <span class="invalid-feedback d-block" role="alert" id="id_employee_chief_editError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="remark_2_edit" name="remark_2" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                </select>
                <span class="invalid-feedback d-block" role="alert" id="remark_2_editError">
                    <strong></strong>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_region_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_branch_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_position_detail_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_dept_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_job_grade_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Division <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_principal_edit" disabled="" multiple="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_employment_status_edit" name="id_employment_status" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_employment_status_editError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_position_routing_chief_edit" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
             <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chief_editError">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
</div>
<div class="row" id="row_keterangan_edit">
    <div class="col-lg-12">
        <label>Keperluan <sup class="text text-danger">*</sup></label>
        <textarea class="form-control" name="keterangan" id="keterangan_edit" rows="4"></textarea>
        <span class="invalid-feedback d-block" role="alert" id="keterangan_editError">
            <strong></strong>
        </span>
    </div>
</div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
 <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-success action_edit"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>