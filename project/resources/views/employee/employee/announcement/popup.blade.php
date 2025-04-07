<div class="modal fade" id="modal_form_announcement">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="description_announcement"></h4>
            </div>
            <div class="modal-body">             
                <div id="content_announcement"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="mr-auto float-left" id="attachment_announcement"></div>
                <button type="button" class="btn btn-sm btn-danger float-right" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showAnnouncement(id_announcement) {
    $('#loader').removeClass('hidden');
    $.ajax({
        url: "<?= url('employee/employee/announcement/get_announcement_edit') ?>",
        method: "GET",
        data: {id_announcement: id_announcement},
        success: function (response) {
            let attachment = '';

            if(response.attachment_type != null){
                const type = {
                    'pdf' : {'name' : Date.now() + '.pdf'},
                    'image' : {'name' : Date.now() + '.jpg'},
                };
                attachment =  `<a download="${type[response.attachment_type]['name']}" href="${response.this_attachment}" class="btn btn-xs btn-info" style="color:white;"><b>Download Lampiran</b></a>`;
            } else {
                if(response.attachment != null){
                    attachment =  `<a href="${response.this_attachment}" class="btn btn-xs btn-info" style="color:white;" target="_blank"><b>Download Lampiran</b></a>`;
                }
            }
 
            $('#description_announcement').html(response.description);
            $("#content_announcement").html(response.content_letter);
            $('#attachment_announcement').html(attachment);
            if(response.this_attachment == null){
                $('#attachment_announcement').html('');
            }
        },
        error: function (xhr) {
            swal({
                icon: 'error',
                title: 'Oops...',
                dangerMode: true,
                text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
            });
        },
        complete: function(){
            $('#loader').addClass('hidden');
        },
    });
    $('#modal_form_announcement').modal('show');
}

</script>