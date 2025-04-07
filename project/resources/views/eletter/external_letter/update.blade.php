<div class="modal fade" id="modal_form_ext_edit"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <form method="POST" id="extFormEdit" enctype="multipart/form-data">
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
                <input type="hidden" hidden="" id="id_letter" name="id_letter">
                <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                 <select id="id_category_edit" name="id_category" class="form-control form-control-sm select_opsi_edit" style="width: 100%;"></select>
                 <input type="" id="category_code_edit" hidden="" name="category_code" value="-">
                 <span class="invalid-feedback d-block" role="alert" id="id_category_editError"><strong></strong></span>
               </div>
             </div>
             <!--   -->
             <div class="row">
              <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <input autocomplete="off" name="date" disabled="" id="date_edit" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="date_editError"><strong></strong></span>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="row">
              <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
               <select id="id_region_edit" name="id_region" disabled="" class="form-control form-control-sm select_disabled_edit" style="width: 100%;" readonly></select>
               <span class="invalid-feedback d-block" role="alert" id="id_region_editError"><strong></strong></span>
             </div>
           </div>
           <div class="row">
            <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_dept_edit" name="id_dept" disabled="" class="form-control form-control-sm select_disabled_edit" style="width: 100%;" readonly></select>
             <span class="invalid-feedback d-block" role="alert" id="id_dept_editError"><strong></strong></span>
           </div>
         </div>
       </div>
       <div class="col-md-6">
        <div class="row">
          <label class="col-sm-4 col-form-label">Lain-lain <sup class="text text-danger" id="branch_required_edit"></sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" disabled="" name="remark_1" id="remark_1_edit" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_1_editError"><strong></strong></span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" name="email" id="email_edit" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="email_editError"><strong></strong></span>
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
    </div>
    <div class="col-md-6">
     <div class="row">
      <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
      <div class="col-sm-8">
       <select id="id_branch_edit" name="id_branch" class="form-control form-control-sm select_opsi_edit" style="width: 100%;"></select>
       <span class="invalid-feedback d-block" role="alert" id="id_branch_editError"><strong></strong></span>
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
<div class="col-md-6">
</div>
</div>
<div class="row">
  <div class="col-xl-12">
    <div class="form-group">
      <label>Keterangan</label>
      <textarea class="form-control" rows="4" name="notes" id="notes_edit"></textarea>
    </div>
  </div>
</div>
</div>
<div class="modal-footer">
  <button class="btn btn-sm btn-success action_edit"><i class="fas fa-save"></i> Save</button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>