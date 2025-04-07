<div class="modal fade" id="modal_form_ski"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="skiForm" enctype="multipart/form-data">
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
                                <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Letter Date<sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="dateError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="remark_1" id="remark_1" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Universitas <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" id="remark_3" name="remark_3" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="remark_3Error">
                                    <strong></strong>
                                </span>
                            </div>
                        </div> 
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Jurusan <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" id="remark_4" name="remark_4" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback d-block" role="alert" id="remark_4Error">
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
                         <select id="remark_5" name="remark_5" class="form-control form-control-sm select_opsi" style="width: 100%;">
                         </select>
                         <span class="invalid-feedback d-block" role="alert" id="remark_5Error">
                            <strong></strong>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
             <div class="row">
                <label class="col-sm-4 col-form-label">Start Date <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <input autocomplete="off" name="expired_date" id="expired_date" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
                        <strong></strong>
                    </span>
                </div>
            </div> 
            <div class="row">
                <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_dept" name="id_dept" class="form-control form-control-sm select_opsi" style="width: 100%;">
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="remark_2" name="remark_2" class="form-control form-control-sm select_opsi" style="width: 100%;">
                        <option selected="" value="Mahasiswa">Mahasiswa</option>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="remark_2Error">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Email Atasan</label>
                <div class="col-sm-8">
                    <input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="emailError">
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
