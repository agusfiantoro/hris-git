<div class="modal fade" id="modal_form_ext"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form method="POST" id="extForm" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-header">
          <h5 class="modal-title">Form External Letter / EXT</h5>
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
                 <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                 <input type="" id="category_code" hidden="" name="category_code" value="-">
                 <span class="invalid-feedback d-block" role="alert" id="id_categoryError"><strong></strong></span>
               </div>
             </div>
             <div class="row">
              <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="dateError"><strong></strong></span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
               <select id="id_region" name="id_region" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
               <span class="invalid-feedback d-block" role="alert" id="id_regionError"><strong></strong></span>
             </div>
           </div>
           <div class="row">
            <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_branch" name="id_branch" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
             <span class="invalid-feedback d-block" role="alert" id="id_branchError"><strong></strong></span>
           </div>
         </div>
         
       </div>
       <div class="col-md-6">
        <div class="row">
          <label class="col-sm-4 col-form-label">Lain-lain <sup class="text text-danger" id="branch_required"></sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" disabled="" name="remark_1" id="remark_1" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_1Error"><strong></strong></span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="emailError"><strong></strong></span>
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
    </div>
    <div class="col-md-6">
      <div class="row">
        <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
         <select id="id_dept" name="id_dept" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
         <span class="invalid-feedback d-block" role="alert" id="id_deptError"><strong></strong></span>
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
  <div class="col-md-6">
  </div>
</div>
<div class="row">
  <div class="col-xl-12">
    <div class="form-group">
      <label>Keterangan</label>
      <textarea class="form-control" rows="4" name="notes" id="notes"></textarea>
    </div>
  </div>
</div>
</div>
<div class="modal-footer">
  <button class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>