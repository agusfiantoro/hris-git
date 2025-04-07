<div class="modal fade" id="modal_form_supa_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Summon Letter (SUPA)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" id="supaFormEdit" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select id="id_category_edit" disabled="" style="width: 100%;" name="id_category" class="form-control form-control-sm select_search_edit" readonly>
                                    </select>
                                    <input type="" hidden="" id="id_letter" name="id_letter">
                                    <span class="invalid-feedback d-block" role="alert" id="id_category_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <input autocomplete="off" name="date" id="date_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                                    <span class="invalid-feedback d-block" role="alert" id="date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <select id="id_employee_edit" disabled="" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                    <input autocomplete="off" name="effective_date" id="effective_date_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                                    <span class="invalid-feedback d-block" role="alert" id="effective_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger">*</sup></label>
                                <div class="col-sm-8">
                                  <input autocomplete="off" name="expired_date" id="expired_date_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                                  <span class="invalid-feedback d-block" role="alert" id="expired_date_editError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Nama Pengirim <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                               <select id="id_employee_chief_edit" name="id_employee_chief" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                               </select>
                               <span class="invalid-feedback d-block" role="alert" id="id_employee_chief_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Nama HRBP/Atasan <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="remark_1" id="remark_1_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="remark_1_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="email" id="email_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="email_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Hari <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="hari" id="hari_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="hari_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Tanggal Panggil <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="tanggal_panggil" id="tanggal_panggil_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="tanggal_panggil_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Tempat Panggil <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="remark_5" id="remark_5_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="remark_5_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="remark_8_edit" name="remark_8" class="form-control form-control-sm select_opsi" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="remark_8_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_region_view_edit" disabled class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_branch_view_edit" disabled class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_position_detail_view_edit" disabled class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_dept_view_edit" disabled class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Jabatan Pengirim <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select id="id_position_routing_chief_edit" name="id_position_routing_chief" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly></select>
                            <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chief_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Jabatan HRBP/Atasan <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="remark_2" id="remark_2_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="remark_2_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Alamat <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="remark_3" id="remark_3_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="remark_3_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Kota <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="remark_6" id="remark_6_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="remark_6_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Waktu <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" type="time" name="waktu" id="waktu_edit" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="waktu_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">No SUPA 1</label>
                        <div class="col-sm-8">
                            <input autocomplete="off" id="no_supa_1_edit" readonly="" class="form-control form-control-sm value_select_search_edit" style="width: 100%;">
                            <input type="" hidden="" id="remark_7_edit" name="remark_7">
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Zona <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                           <select id="zona_edit" name="zona" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
                               <option value="WIB">WIB</option>
                               <option value="WITA">WITA</option>
                               <option value="WIT">WIT</option>
                           </select>
                           <span class="invalid-feedback d-block" role="alert" id="zona_editError">
                            <strong></strong>
                        </span>
                    </div>
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