<div class="modal fade" id="modal_form_freelance"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="" id="freelanceForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Daily Employment Contract (PKH)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                 <div class="row">
                    <div class="col-md-6">
                       <!--  <div class="row">
                            <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                               <select id="id_category" name="id_category" class="form-control form-control-sm select_search" style="width: 100%;">
                               </select>
                               <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                                <strong></strong>
                            </span>
                        </div>
                    </div> -->
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
                        <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
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
                        <!-- Effective and Expired -->
                        <label class="col-sm-4 col-form-label">Start Date - End Date <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                                <strong></strong>
                            </span>
                            <sup class="text text-danger"><i class="fa fa-info-circle"></i> dalam 1x kontrak tidak boleh lebih dari 21 hari (setiap kontrak di beri jeda)</sup>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Resign Contract <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <input autocomplete="off" name="notes" id="notes" class="form-control form-control-sm" style="width: 100%;">
                            <span class="invalid-feedback d-block" role="alert" id="notesError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_position_detail" id="id_position_detail" class="form-control form-control-sm" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Departement <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_dept" id="id_dept" class="form-control form-control-sm select_disabled" style="width: 100%;" readonly>
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_job_grade" id="id_job_grade" class="select_disabled form-control form-control-sm" style="width: 100%;" readonly>
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_branch" id="id_branch" class="form-control form-control-sm select_search" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Location <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_location" id="id_location" class="form-control form-control-sm select_search" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_locationError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
                        <div class="col-sm-8">
                            <select name="id_region" id="id_region" class="form-control form-control-sm select_disabled" style="width: 100%;" readonly>
                            </select>
                            <span class="invalid-feedback d-block" role="alert" id="id_regionError">
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
        <button type="submit" class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>
</div>
</div>
</div>