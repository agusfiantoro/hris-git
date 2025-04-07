<div class="modal fade" id="modal_form_skk_publish"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method="POST" id="skkFormPublish" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="title-publish"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Publish</label>
                                <div class="col-sm-8">
                                    <input type="" hidden="" id="id_letter_publish" name="id_letter">
                                    <input type="checkbox" style="width: 20px;height: 20px;" id="publish" name="publish">
                                    <span class="invalid-feedback d-block" role="alert" id="publishError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Publish Date</label>
                                <div class="col-sm-8">
                                    <input id="remark_1" autocomplete="off" class="form-control form-control-sm" name="remark_1">
                                    <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <!-- 300 kb file pdf -->
                           <!--  <div class="row mt-2">
                                <label class="col-sm-4 col-form-label">Upload</label>
                                <div class="col-sm-8">
                                    <input type="file" id="remark_4" accept="application/pdf" autocomplete="off" class="form-control form-control-sm custom-file-input" name="remark_4">
                                    <span class="invalid-feedback d-block" role="alert" id="remark_4Error">
                                        <strong></strong>
                                    </span>
                                    <span style="font-size: 12px;"><i>Notes: Maximum 300kb, file must be pdf.</i></span>
                                </div>
                            </div> -->
                            <div class="row mt-2 mb-3">
                                <label class="col-sm-4 col-form-label">Receipt Attachment</label>                           
                                <div class="col-md-8">
                                 <div class="custom-file">
                                  <input type="file" style="overflow: hidden;" height="40" name="remark_4" class="custom-file-input" id="remark_4" onchange="readFile(this);" accept="application/pdf">
                                  <span class="invalid-feedback" role="alert" id="remark_4Error">
                                    <strong></strong>
                                </span>
                                <label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Maximum 300kb.</i></label>
                                <span id="file_label"></span>
                            </div>
                            <embed src="#" style="display: none;" height="100" class="file-upload-document mt-5" width="80"></embed>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-loading" id="modal-loading" style="display: none;">
           <span class="fa fa-spinner fa-spin fa-3x"></span>
       </div>
       <div class="modal-footer">
        <button type="submit" class="btn btn-sm btn-success action_publish"><i class="fas fa-save"></i> Save</button>&nbsp;
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
    </div>
</form>
</div>
</div>
</div>