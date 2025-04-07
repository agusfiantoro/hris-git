<div class="modal fade" id="modal_form_other_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Other Letter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="otherFormEdit" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <input type="" hidden="" id="id_letter" name="id_letter">
                                <label class="col-sm-4 col-form-label">Category <span class="text text-danger">*</span></sup></label>
                                <div class="col-sm-8">
                                    <select id="id_category_edit" name="id_category" class="form-control form-control-sm select_opsi_edit" style="width: 100%;" readonly>
                                    </select>
                                    <!-- <span class="invalid-feedback d-block" role="alert" id="id_category_editError">
                                        <strong></strong>
                                    </span> -->
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Letter Date <span class="text text-danger">*</span></sup></label>
                                <div class="col-sm-8">
                                    <input autocomplete="off" disabled="" name="date" id="date_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback d-block" role="alert" id="date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">By Name</label>
                                <div class="col-sm-8">
                                    <input type="checkbox" disabled="" style="width: 20px;height: 20px;" name="remark_3" value="By Name" id="remark_3_edit">
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Name</sup></label>
                                <div class="col-sm-8">
                                    <select id="id_employee_edit" name="id_employee" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                                    </select>
                                    <!-- <span class="invalid-feedback d-block" role="alert" id="id_employee_editError">
                                        <strong></strong>
                                    </span> -->
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</sup></label>
                                <div class="col-sm-8">
                                 <input autocomplete="off" name="effective_date" id="effective_date_edit" class="form-control form-control-sm" style="width: 100%;">
                                 <span class="invalid-feedback d-block" role="alert" id="effective_date_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">End Date</sup></label>
                            <div class="col-sm-8">
                              <input autocomplete="off" name="expired_date" id="expired_date_edit" class="form-control form-control-sm" style="width: 100%;">
                              <span class="invalid-feedback d-block" role="alert" id="expired_date_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Email</sup></label>
                        <div class="col-sm-8">
                           <input autocomplete="off" name="email" id="email_edit" class="form-control form-control-sm" style="width: 100%;">
                           <span class="invalid-feedback d-block" role="alert" id="email_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Pemberi Keputusan</sup></label>
                    <div class="col-sm-8">
                       <select id="id_employee_chief_edit" name="id_employee_chief" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                       </select>
                       <span class="invalid-feedback d-block" role="alert" id="id_employee_chief_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">With Dept and Region</label>
                <div class="col-sm-8">
                    <input type="checkbox" disabled="" style="width: 20px;height: 20px;" name="remark_5" value="By Dept and Region" id="remark_5_edit">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">HRD Code</label>
                <div class="col-sm-8">
                    <input type="checkbox" disabled="" style="width: 20px;height: 20px;" name="remark_4" value="By HRD" id="remark_4_edit">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_region_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_region_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_branch_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_branch_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_dept_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_dept_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Position</sup></label>
                <div class="col-sm-8">
                    <select id="id_position_detail_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_position_detail_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Job Grade</sup></label>
                <div class="col-sm-8">
                    <select id="id_job_grade_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_job_grade_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Division</sup></label>
                <div class="col-sm-8">
                    <select id="id_principal_edit" multiple="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_principal_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Status</sup></label>
                <div class="col-sm-8">
                    <select id="id_employment_status_edit" name="id_employment_status" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_employment_status_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan</sup></label>
                <div class="col-sm-8">
                    <select id="id_position_routing_chief_edit" name="id_position_routing_chief" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly></select>
                    <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chief_editError">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 mt-2">
            <label>Notes</sup></label>
            <textarea class="form-control" name="notes" id="notes_edit" rows="4"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="notes_editError">
                <strong></strong>
            </span>
        </div>
        <div class="col-lg-6 mt-2">
            <label>Mengetahui</label>
            <input autocomplete="off" type="text" name="remark_1" id="remark_1_edit" class="form-control form-control-sm" style="width: 100%;">
        </div>
        <div class="col-lg-6 mt-2">
            <label>Jabatan Mengetahui</label>
            <input autocomplete="off" type="text" name="remark_2" id="remark_2_edit" class="form-control form-control-sm" style="width: 100%;">
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