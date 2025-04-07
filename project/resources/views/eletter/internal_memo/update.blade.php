<div class="modal fade" id="modal_form_im_edit"  data-backdrop="static" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Internal Memo</h5>
                <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
             <form method="POST" id="imFormEdit" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                             <select id="id_category_edit" name="id_category" class="form-control-sm select_opsi_edit" style="width: 100%;">
                             </select>
                             <span class="invalid-feedback d-block" role="alert" id="id_category_editError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="date" id="date_edit" disabled="" class="form-control form-control-sm" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-3 col-form-label">With Career &nbsp;
                        <input type="checkbox" disabled="" style="width: 20px;height: 20px;padding-top: 2px;" name="remark_8_edit" value="Rehire" id="remark_8_edit">
                    </label>
                    <label class="col-sm-2 col-form-label">Name <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-7">
                        <select id="id_employee_edit" disabled="" class="form-control-sm select_search_edit" style="width: 100%;" readonly>
                        </select>
                        <span class="invalid-feedback d-block" role="alert" id="">
                            <strong></strong>
                        </span>
                    </div>
                </div>
                <div class="row">
                    <label class="col-sm-4 col-form-label">Cc <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                     <input autocomplete="off" name="remark_6" id="remark_6_edit" class="form-control form-control-sm" style="width: 100%;">
                     <span class="invalid-feedback d-block" role="alert" id="remark_6_editError">
                        <strong></strong>
                    </span>
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
                    <select id="id_region_old_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Old Branch <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_branch_old_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                        <strong></strong>
                    </span>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-4 col-form-label">Old Location <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                    <select id="id_location_old_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_locationError">
                        <strong></strong>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
         <div class="row">
            <label class="col-sm-4 col-form-label">Old Position <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_position_detail_old_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Old Department <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_dept_old_edit" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Old Division <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_principal_old_edit" multiple="multiple" class="form-control form-control-sm select_search_edit" style="width: 100%;" readonly>
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
                <select id="id_region_new_edit" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_region_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Branch <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_branch_new_edit" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_branch_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Location <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_location_new_edit" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_location_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
       <div class="row">
        <label class="col-sm-4 col-form-label">New Position <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_position_detail_new_edit" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_position_detail_newError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">New Department <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_dept_new_edit" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_dept_newError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">New Division <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_principal_new_edit" multiple="multiple" class="form-control form-control-sm select_search_new_edit" style="width: 100%;" readonly>
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
                <input autocomplete="off" name="effective_date" id="effective_date_edit" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="effective_date_editError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">End Date</label>
            <div class="col-sm-8">
              <input autocomplete="off" name="expired_date" id="expired_date_edit" class="form-control form-control-sm" style="width: 100%;">
              <span class="invalid-feedback d-block" role="alert" id="expired_date_editError">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Penanggung Jawab</label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_1" id="remark_1_edit" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_1_editError">
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
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Masa Penilaian</label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_4" id="remark_4_edit" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_4_editError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Penilai</label>
        <div class="col-sm-8">
          <input autocomplete="off" name="remark_5" id="remark_5_edit" class="form-control form-control-sm" style="width: 100%;">
          <span class="invalid-feedback d-block" role="alert" id="remark_5_editError">
            <strong></strong>
        </span>
    </div>
</div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_employee_chief_edit" name="id_employee_chief" class="form-control form-control-sm select_opsi_edit" style="width: 100%;">
            </select>
            <input type="" id="id_letter" hidden="" name="id_letter">
            <span class="invalid-feedback d-block" role="alert" id="id_employee_chief_editError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
           <select id="id_position_routing_chief_edit" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly>
           </select>
           <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chief_editError">
            <strong></strong>
        </span>
    </div>
</div>
</div>
</div>

<div class="row mt-3" id="tugas_tunjangan_edit">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tugas Tanggung Jawab</label>
            <textarea class="summernote_edit" rows="5" id="remark_2_edit" name="remark_2"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="remark_2_editError">
                <strong></strong>
            </span>
        </div>
    </div>
    <!-- div class="col-md-6">
        <div class="form-group">
            <label>Tunjangan</label>
            <textarea class="summernote_edit" rows="5" id="remark_3_edit" name="remark_3"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="remark_3_editError">
                <strong></strong>
            </span>
        </div>
    </div -->
</div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
   <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-success action_edit"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>