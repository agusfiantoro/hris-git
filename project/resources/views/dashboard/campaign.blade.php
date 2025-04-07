    <div class="row">
        <div class="col-12">        
            <div class="row">
                @foreach($getCampaign as $k => $val)
                <div class="col-md-6">
                    <a href="javascript:;" attachment_path="{{ $val->attachment_path }}" link="{{ $val->link }}" class="showCampaign"><img class="img-rounded " style="width:100%;height:230px;margin-bottom:8px;" src="{{ $val->image_poster_path }}"/></a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCampaign"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 div_campaign"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="div_linkCampaign"></div>
                    <button class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
