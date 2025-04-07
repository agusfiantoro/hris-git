<div class="modal fade" id="modal_form_ski_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="skiFormEdit" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Internship Notification Letter</h5>
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
                                <select id="id_category_edit" disabled="" name="id_category" class="form-control form-control-sm select_opsi_edit" style="width: 100%;" readonly>
                                </select>
                                <span class="invalid-feedback d-block" role="alert" id="id_category_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Letter Date<sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" disabled="" name="date" id="date_edit" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="date_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="remark_1" id="remark_1_edit" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="remark_1_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Universitas <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" id="remark_3_edit" name="remark_3" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="remark_3_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div> 
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Jurusan <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" id="remark_4_edit" name="remark_4" class="form-control form-control-sm" style="width: 100%;">
                                <input type="" id="id_letter" hidden="" name="id_letter">
                                <span class="invalid-feedback d-block" role="alert" id="remark_4_editError">
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
                               <select id="remark_5_edit" name="remark_5" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                               </select>
                               <span class="invalid-feedback d-block" role="alert" id="remark_5_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                 <div class="row">
                    <label class="col-sm-4 col-form-label">Start Date <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <input autocomplete="off" name="effective_date" id="effective_date_edit" class="form-control form-control-sm" style="width: 100%;">
                        <span class="invalid-feedback d-block" role="alert" id="effective_date_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <input autocomplete="off" name="expired_date" id="expired_date_edit" class="form-control form-control-sm" style="width: 100%;">
                        <span class="invalid-feedback d-block" role="alert" id="expired_date_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="id_dept_edit" disabled="" name="id_dept" class="form-control form-control-sm select_opsi_edit" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="id_dept_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                        <select id="remark_2_edit" name="remark_2" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Lulusan">Lulusan</option>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="remark_2_editError">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Email Atasan</label>
                    <div class="col-sm-8">
                        <input autocomplete="off" name="email" id="email_edit" class="form-control form-control-sm" style="width: 100%;">
                        <span class="invalid-feedback d-block" role="alert" id="email_editError">
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
    <div class="row">
        <div class="col-xl-12">
            <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" rows="4" id="notes_edit" name="notes"></textarea>
                <span class="invalid-feedback d-block" role="alert" id="notes_editError">
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
    <button class="btn btn-sm btn-success action_edit"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>
