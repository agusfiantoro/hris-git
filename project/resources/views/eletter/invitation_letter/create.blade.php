<div class="modal fade" id="modal_form_supa"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Summon Letter (SUPA)</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <form method="POST" id="supaForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                               <div class="input-group">
                                  <select id="id_category" style="width: 87%;" name="id_category" aria-describedby="basic-addon2" class="form-control">
                                  </select>
                                  <input type="" id="category_code" hidden="" name="">
                                  <div class="input-group-append">
                                    <button disabled="" type="button" class="input-group-text btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                                </div>
                                <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                                    <strong></strong>
                                </span>
                            </div>
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
                        <label class="col-sm-4 col-form-label">Name</label>
                        <div class="col-sm-8">
                            <select id="id_employee_view" disabled="" class="form-control form-control-sm select_opsi" style="width: 100%;">
                            </select>
                            <input type="" id="id_employee" hidden="" class="value_select_search" name="id_employee">
                            <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Effective Date <sup class="text text-danger">*</sup></label>
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
                <label class="col-sm-4 col-form-label">Nama Pengirim <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                 <select id="id_employee_chief" name="id_employee_chief" class="form-control form-control-sm select_opsi" style="width: 100%;">
                 </select>
                 <span class="invalid-feedback d-block" role="alert" id="id_employee_chiefError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Nama HRBP/Atasan <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <input autocomplete="off" name="remark_1" id="remark_1" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
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
            <label class="col-sm-4 col-form-label">Hari <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <input autocomplete="off" name="hari" id="hari" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="hariError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Tanggal Panggil <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <input autocomplete="off" name="tanggal_panggil" id="tanggal_panggil" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="tanggal_panggilError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Tempat Panggil <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <input autocomplete="off" name="remark_5" id="remark_5" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="remark_5Error">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="remark_8" name="remark_8" class="form-control form-control-sm select_opsi" style="width: 100%;">
             </select>
             <span class="invalid-feedback d-block" role="alert" id="remark_8Error">
                <strong></strong>
            </span>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_region" name="id_region" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
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
            <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_position_detail" name="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_dept" name="id_dept" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Jabatan Pengirim <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
            <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Jabatan HRBP/Atasan <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_2" id="remark_2" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_2Error">
                <strong></strong>
            </span>
        </div>
    </div> 
    <div class="row">
        <label class="col-sm-4 col-form-label">Alamat <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_3" id="remark_3" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_3Error">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Kota <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_6" id="remark_6" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_6Error">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Waktu <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <input autocomplete="off" type="time" name="waktu" id="waktu" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="waktuError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">No SUPA 1</label>
        <div class="col-sm-8">
            <input autocomplete="off" id="no_supa_1" readonly="" class="form-control form-control-sm" style="width: 100%;">
            <input type="" hidden="" id="remark_7" name="remark_7">
            <span class="invalid-feedback d-block" role="alert" id="remark_7Error">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Zona <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
         <select id="zona" name="zona" class="form-control form-control-sm select_opsi" style="width: 100%;">
             <option value="WIB">WIB</option>
             <option value="WITA">WITA</option>
             <option value="WIT">WIT</option>
         </select>
         <span class="invalid-feedback d-block" role="alert" id="zonaError">
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
    <button class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>