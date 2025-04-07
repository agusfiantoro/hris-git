<div class="modal fade text-left" id="modal_form_im" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="myModalLabel1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Form Internal Memo</h5>
            <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
         <form method="POST" id="imForm" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                 <div class="row">
                    <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                    <div class="col-sm-8">
                       <select id="id_category" name="id_category" class="form-control-sm select_opsi" style="width: 100%;">
                       </select>
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
        </div>
        <div class="col-md-6">
         <div class="row">
            <label class="col-sm-3 col-form-label">With Career &nbsp;
                <input type="checkbox" disabled="" style="width: 20px;height: 20px;padding-top: 2px;" name="remark_8" value="Rehire" id="remark_8">
            </label>
            <label class="col-sm-2 col-form-label">Name <sup class="text text-danger">*</sup></label>
            <div class="col-sm-7">
             <select id="id_career_transaction" hidden="" class="form-control-sm" name="id_career_transaction"></select>
             <div class="input-group">
                 <select id="id_employee" name="id_employee" class="form-control-sm select_opsi" style="width: 83%;">
                 </select>
                 <div class="input-group-append">
                    <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                </div>
            </div>
            <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Cc <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
           <input autocomplete="off" name="remark_6" id="remark_6" class="form-control form-control-sm" style="width: 100%;">
           <span class="invalid-feedback d-block" role="alert" id="remark_6Error">
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
            <select id="id_region_old" name="id_region" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Old Branch <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_branch_old" name="id_branch" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
            </select>
            <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Old Location <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
            <select id="id_location_old" name="id_location" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
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
        <select id="id_position_detail_old" name="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
        </select>
        <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
            <strong></strong>
        </span>
    </div>
</div>
<div class="row">
    <label class="col-sm-4 col-form-label">Old Department <sup class="text text-danger">*</sup></label>
    <div class="col-sm-8">
        <select id="id_dept_old" name="id_dept" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
        </select>
        <span class="invalid-feedback d-block" role="alert" id="id_deptError">
            <strong></strong>
        </span>
    </div>
</div>

<div class="row">
    <label class="col-sm-4 col-form-label">Old Division <sup class="text text-danger">*</sup></label>
    <div class="col-sm-8">
        <select id="id_principal_old" name="id_principal[]" multiple="multiple" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
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
                <select id="id_region_new" name="id_region_new" class="form-control form-control-sm select_search_new" style="width: 100%;">
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_region_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Branch <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_branch_new" name="id_branch_new" class="form-control form-control-sm select_search_new" style="width: 100%;">
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_branch_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Location <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_location_new" name="id_location_new" class="form-control form-control-sm select_search_new" style="width: 100%;">
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_location_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="row">
            <input type="" name="id_position_routing" id="id_position_routing" hidden="">
            <label class="col-sm-4 col-form-label">New Position <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
                <select id="id_position_detail_new" name="id_position_detail_new" class="form-control form-control-sm select_search_new" style="width: 100%;">
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_position_detail_newError">
                    <strong></strong>
                </span>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-4 col-form-label">New Department <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_dept_new" name="id_dept_new" class="form-control form-control-sm select_search_new" style="width: 100%;">
             </select>
             <span class="invalid-feedback d-block" role="alert" id="id_dept_newError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">New Division <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
         <select id="id_principal_new" name="id_principal_new[]" multiple="multiple" class="form-control form-control-sm select_search_new" style="width: 100%;">
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
             <input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
             <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">End Date</label>
        <div class="col-sm-8">
           <input autocomplete="off" name="expired_date" id="expired_date" class="form-control form-control-sm" style="width: 100%;">
           <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
            <strong></strong>
        </span>
    </div>
</div>
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Penanggung Jawab</label>
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
</div>
<div class="col-md-6">
    <div class="row">
        <label class="col-sm-4 col-form-label">Masa Penilaian</label>
        <div class="col-sm-8">
            <input autocomplete="off" name="remark_4" id="remark_4" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_4Error">
                <strong></strong>
            </span>
        </div>
    </div>
    <div class="row">
        <label class="col-sm-4 col-form-label">Penilai </label>
        <div class="col-sm-8">
          <input autocomplete="off" name="remark_5" id="remark_5" class="form-control form-control-sm" style="width: 100%;">
          <span class="invalid-feedback d-block" role="alert" id="remark_5Error">
            <strong></strong>
        </span>
    </div>
</div>
</div>
<div class="col-md-6">
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

<div class="row mt-3" id="tugas_tunjangan">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tugas Tanggung Jawab</label>
            <textarea class="summernote" rows="5" id="remark_2" name="remark_2"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="remark_2Error">
                <strong></strong>
            </span>
        </div>
    </div>
    <!-- div class="col-md-6">
        <div class="form-group">
            <label>Tunjangan</label>
            <textarea class="summernote" rows="5" id="remark_3" name="remark_3"></textarea>
            <span class="invalid-feedback d-block" role="alert" id="remark_3Error">
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
    <button type="submit" class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>