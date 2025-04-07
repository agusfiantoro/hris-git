@extends('adminlte::page')
@section('title', 'Master API Key')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master API Key</h5>
            </div>

            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
                <table id="datatable_api" style="width:100%;" class="table table-striped table-bordered table-hover datatable">

                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_api_key"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_api_key_form">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master General Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" readonly>
                                    <span class="invalid-feedback" role="alert" id="nameError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Url</label>
                                <div class="col-sm-8">
                                    <input type="text" name="url" id="url" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="urlError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">User</label>
                                <div class="col-sm-8">
                                    <input type="text" name="user" id="user" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="userError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Password</label>
                                <div class="col-sm-8">
                                    <input type="text" name="password" id="password" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="passwordError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Key</label>
                                <div class="col-sm-8">
                                    <input type="text" name="key" id="key" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="keyError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Token</label>
                                <div class="col-sm-8">
                                    <textarea name="token" id="token" class="form-control form-control-sm" style="height:80px;"></textarea>
                                    <!-- <input type="text" name="token" id="token" class="form-control form-control-sm"> -->
                                    <span class="invalid-feedback" role="alert" id="tokenError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 1</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_1" id="attribute_1" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_1Error">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 2</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_2" id="attribute_2" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_2Error">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 3</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_3" id="attribute_3" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_3Error">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 4</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_4" id="attribute_4" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_4Error">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 5</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_5" id="attribute_5" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_5Error">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attribute 6</label>
                                <div class="col-sm-8">
                                    <input type="text" name="attribute_6" id="attribute_6" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="attribute_6Error">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('css')
<style type="text/css">
    .modal-lg, .modal-xl {
        max-width: 90% !important;
    }
    table.dataTable tbody td.wrap-text {
        word-break: break-word;
        vertical-align: top;
    }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_master_api_key = "";

    $(document).on('click', '.edit', function () {
        let id_master_api_key = $(this).attr('id');
        global_id_master_api_key = id_master_api_key;
        $("#master_api_key_form")[0].reset();
        $("#master_api_key_form .modal-title").html("<span class='fas fa-edit'></span> Edit Master Api Key");
        $(".invalid-feedback").children("strong").text("");
        $("#master_api_key_form input").removeClass("is-invalid");
        $("#master_api_key_form textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "<?= url('general_setting/company_setting/master_api_key/get_detail_master_api_key') ?>",
            method: "GET",
            data: {id_master_api_key: id_master_api_key},
            success: function (response) {
                $('#id').val(response.id);
                $('#name').val(response.name);
                $('#url').val(response.url);
                $('#user').val(response.user);
                $('#password').val(response.password);
                $('#key').val(response.key);
                $('#token').val(response.token);
                $('#attribute_1').val(response.attribute_1);
                $('#attribute_2').val(response.attribute_2);
                $('#attribute_3').val(response.attribute_3);
                $('#attribute_4').val(response.attribute_4);
                $('#attribute_5').val(response.attribute_5);
                $('#attribute_6').val(response.attribute_6);
            },
            error: function (xhr) {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                });
            }
        });
        $('#modal_form_master_api_key').modal('show');
    });

    $(document).ready(function () {
        loadDatatable()

		$('#advanced').click(function(){
			$('.cf').select2({width:'100%'});
			if($("#cf").css('display') == 'none'){
				$("#cf").show("slow");
			}
			else {
				$("#cf").hide("slow");
			}		
		});

        $('#master_api_key_form').submit(function (e) {
            e.preventDefault();
            $("#master_api_key_form :input").attr('disabled', false);
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#master_api_key_form input").removeClass("is-invalid");
            $("#master_api_key_form textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
                url: '<?= url('general_setting/company_setting/master_api_key/save_master_api_key') ?>',
                data: formData,
                success: function (response) {
                    if (response.status == 'false') {
                        swal({
                            icon: "error",
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + response.message + ']'
                        });
                    } else if (response.status == 'true') {
                        $('#modal_form_master_api_key').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        loadDatatable()
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [Unknown Error]'
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function (key) {
                            var key_temp = key.replaceAll(".", "_");
                            $("#" + key_temp).addClass("is-invalid");
                            $("#" + key_temp + "Error").children("strong").text(errors[key][0].replaceAll("general_data_detail.", ""));
                        });
                    } else {
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                        });
                    }
                }
            });
        });
    });

    function loadDatatable() {
        $('#datatable_api').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            destroy:true,
            ajax: {
                url: "<?= url('general_setting/company_setting/master_api_key/get_data') ?>",
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#datatable_api').DataTable().ajax.reload();
                }
            },
            columnDefs:[{targets:[5], class:"wrap-text"}],
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', title: 'No'},
                {data: 'name', title: 'Name'},
                {data: 'url', title: 'Url'},
                {data: 'attribute_1', title: 'Attribute 1'},
                {data: 'token', title: 'Token'},
                {data: 'action', title: 'Action', orderable: false, 
                    render: function (data, type, row) {
                        let act = `<button type="button" name="edit" id="${row.id}" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> `;
                        return act;
                    }
                },
            ]
        });
    }
</script>
@endsection