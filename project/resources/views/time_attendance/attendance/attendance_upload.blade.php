@extends('adminlte::page')

@section('title', 'Attendance Upload')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Attendance Upload</h5>
                <div class="card-tools">
                    <!-- <button type="button" class="new btn btn-sm btn-success" data-toggle="modal" data-target="#modalshiftgroup"><i class="fas fa-plus"></i> Create Shift Group</button> -->
                </div>
            </div>

            <div class="card-body">
                <form id="upload_form">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Upload File :</label>
                    <div class="col-md-5">
                        <div class="custom-file">
                            <input type="file" name="attachment" class="custom-file-input" id="attachment">
                            <span class="invalid-feedback" role="alert" id="attachmentError"></span>
                            <label class="custom-file-label"><i>File (.csv / .xlsx / .xls)</i></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="upload" class="new btn btn-lg btn-success" ><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </div>
                </form>
            </div>

        </div>
    </div>
</div>


@endsection

@section('scripts')

<script>
$(document).ready(function(){
    $('#upload').click(function(){
        upload()  
    });

    $('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html(`<i>${file.name}</i>`);
    });

    
});

function upload(){
    var formData = new FormData($('#upload_form')[0]);
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "<?= url('time_attendance/attendance_upload_file') ?>",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            $('#loader').addClass('hidden')
            if (response.status == 'true') {
                swal({
                    icon: 'success',
                    title: "Success",
                    text: response.message,
                }).then(function(){ 
                    location.reload();
                });
            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+response.message,
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
        },
        error: function (response) {
            $('#loader').addClass('hidden')
            if (response.status === 422) {
                let errors = response.responseJSON;
                let err = "";
                Object.keys(errors).forEach(function (key) {
                    var key_temp = key.replaceAll(".", "_");
                    $("#" + key_temp).addClass("is-invalid");
                    $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
                    var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
                    err += errors[key][0]+"\n";
                });
                swal({
                    icon: 'error',
                    dangerMode: true,
                    content: {
                        element: "div",
                        attributes: {
                            innerText: err,
                            className: "swal-red",
                        },
                    },
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
            else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+response.statusText,
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
        }
    });                 
}   
</script>
@endsection
