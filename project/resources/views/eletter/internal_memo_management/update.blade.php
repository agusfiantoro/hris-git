<div class="modal fade" id="modal_form_imm_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Circular Letter (SED)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="immFormEdit" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <input type="hidden" hidden="" id="id_letter" name="id_letter">
                                <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <input autocomplete="off" disabled="" name="date" id="date_edit" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback d-block" role="alert" id="date_editError">
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
                                <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select id="id_dept_edit" name="id_dept" class="form-control form-control-sm select_search_edit" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback d-block" role="alert" id="id_dept_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                            <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <select id="id_region_edit" name="id_region" class="form-control form-control-sm select_disabled_edit" style="width: 100%;" readonly>
                                </select>
                                <span class="invalid-feedback d-block" role="alert" id="id_region_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <select id="id_branch_edit" name="id_branch" class="form-control form-control-sm select_search_edit" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback d-block" role="alert" id="id_branch_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                           <!--  <div class="row">
                                <label class="col-sm-4 col-form-label">Company <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select id="id_company_edit" class="form-control form-control-sm select_disabled_edit" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback d-block" role="alert" id="id_company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div> -->
                            
                           <!--  <div class="row">
                                <label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                   <select id="id_employee_chief_edit" name="id_employee_chief" class="form-control form-control-sm select_search_edit" style="width: 100%;">
                                   </select>
                                   <span class="invalid-feedback d-block" role="alert" id="id_employee_chief_editError">
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
                        </div> -->
                    </div>
                    <div class="col-lg-12">
                        <div class="row">
                            <label class="col-sm-12 col-form-label">Keterangan <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-12">
                                <textarea class="form-control" id="keterangan_edit" name="keterangan" rows="4"></textarea>
                                <span class="invalid-feedback d-block" role="alert" id="keterangan_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
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