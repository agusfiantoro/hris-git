<div class="modal fade" id="modal_form_ml" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Master Of Letter</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <form method="POST" id="mlForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Sequence <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" type="number" min="1" readonly="" name="sequence" id="sequence" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="sequenceError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Code <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="code" id="code" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="codeError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Description <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <input autocomplete="off" name="description" id="description" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="descriptionError">
                                    <strong></strong>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
                            <div class="col-sm-8">
                                <select id="status" name="status" class="form-control form-control-sm" style="width: 100%;">
                                    <option value="A">Active</option>
                                    <option value="I">Inactive</option>
                                </select>
                                <span class="invalid-feedback" role="alert" id="statusError">
                                    <strong></strong>
                                </span>
                            </div>
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