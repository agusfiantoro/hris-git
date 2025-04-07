@extends('adminlte::page')
@section('title', 'Master General Data')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master General Data</h5>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="general_data_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>General Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_general_data"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_generaldataForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master General Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">General Type</label>
                                <div class="col-sm-8">
                                    <input type="text" name="general_type" id="general_type" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="general_typeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm" disabled>
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Relation</label>
                                <div class="col-sm-8">
                                    <input name="relation" id="relation" class="form-control form-control-sm" disabled>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-md-12" style="margin-bottom: 10px">
                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_master_general_data_detail"><span class="fas fa-plus"></span> Add Master General Data</button>
                        </div>
                        <div class="col-md-12">
                            <table id="general_data_detail_table" class="table table-striped table-bordered table-hover datatable">
                                <thead>
                                    <tr>
                                        <th style="white-space:nowrap;">No</th>
                                        <th style="white-space:nowrap;">Sequence</th>
                                        <th style="white-space:nowrap;">Code</th>
                                        <th style="white-space:nowrap;">Description</th>
                                        <th style="white-space:nowrap;" class="relation-th">Relation</th>
                                        <th style="white-space:nowrap;">Active</th>
                                        <th style="text-align:center;" width=100>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="general_data_detail_table_body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
            <div style="display:none;">
                <table id="sample_general_data_detail_table">
                    <tr id="">
                        <td><span class="sn"></span>.</td>
                        <td>
                            <div style="width:100%">
                                <input type="hidden" name="general_data_detail[0][id_general_data]" id="general_data_detail_0_id_general_data" class="form-control form-control-sm id_general_data_input">
                                <input type="text" name="general_data_detail[0][sequence]" id="general_data_detail_0_sequence" class="form-control form-control-sm sequence_input">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="general_data_detail_0_sequenceError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:100%">
                                <input type="text" name="general_data_detail[0][code]" id="general_data_detail_0_code" class="form-control form-control-sm code_input">
                                <span class="invalid-feedback code_input_error" role="alert" id="general_data_detail_0_codeError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:100%">
                                <input type="text" name="general_data_detail[0][description]" id="general_data_detail_0_description" class="form-control form-control-sm description_input">
                                <span class="invalid-feedback description_input_error" role="alert" id="general_data_detail_0_descriptionError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td class="relation-td">
                            <div style="width:100%">
                                <select name="general_data_detail[0][relation]" id="general_data_detail_0_relation" class="form-control form-control-sm relation_input">
                                </select>
                                <span class="invalid-feedback relation_input_error" role="alert" id="general_data_detail_0_relationError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="width:100%">
                                <input type="checkbox" name="general_data_detail[0][active]" id="general_data_detail_0_active" class="active_input">
                                <span class="invalid-feedback active_input_error" role="alert" id="detail_position_route_0_activeError">
                                    <strong></strong>
                                </span>
                            </div>
                        </td>
                        <td>
                    <center>
                        <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="fas fa-trash"></span></button>
                        <button type="button" class="generate-all-company btn btn-xs btn-primary" data-id="0" title="Generate general data to all company"><span class="fas fa-play"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

@endsection

@section('css')
<style type="text/css">
    .modal-lg, .modal-xl {
        max-width: 90% !important;
    }
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_general_type = "";
    let global_id_master_general_data_detail = 0;

    $(document).on('click', '#new_master_general_data_detail', function () {
        var content = jQuery('#sample_general_data_detail_table tr'),
                size = global_id_master_general_data_detail++,
                element = null,
                element = content.clone();

        element.attr('id', 'rec-' + size);
        // element.find('.delete-record').attr('data-id', size);
        element.find('.delete-record').attr({
            "data-id": size,
            "id": "id-delete-master-general-data"+size,
            "value": size
        });

        element.find('.generate-all-company').attr({
            "data-id": size,
            "id": "id-generate-master-general-data"+size,
            "value": size
        });
        
        // id_general_type
        element.find('.id_general_data_input').attr('id', 'general_data_detail_' + size + '_id_general_data');
        element.find('.id_general_data_input').attr('name', 'general_data_detail[' + size + '][id_general_data]');
        
        element.find('.sequence_input').attr('id', 'general_data_detail_' + size + '_sequence');
        element.find('.sequence_input').attr('name', 'general_data_detail[' + size + '][sequence]');
        element.find('.sequence_input_error').attr('id', 'general_data_detail_' + size + '_sequenceError');
        
        element.find('.code_input').attr('id', 'general_data_detail_' + size + '_code');
        element.find('.code_input').attr('name', 'general_data_detail[' + size + '][code]');
        element.find('.code_input_error').attr('id', 'general_data_detail_' + size + '_codeError');
        
        element.find('.description_input').attr('id', 'general_data_detail_' + size + '_description');
        element.find('.description_input').attr('name', 'general_data_detail[' + size + '][description]');
        element.find('.description_input_error').attr('id', 'general_data_detail_' + size + '_descriptionError');

        element.find('.relation_input').attr('id', 'general_data_detail_' + size + '_relation');
        element.find('.relation_input').attr('name', 'general_data_detail[' + size + '][relation]');
        element.find('.relation_input_error').attr('id', 'general_data_detail_' + size + '_relationError');

        element.find('.active_input').attr('id', 'general_data_detail_' + size + '_active');
        element.find('.active_input').attr('name', 'general_data_detail[' + size + '][active]');
        element.find('.active_input_error').attr('id', 'general_data_detail_' + size + '_activeError');
                
        element.appendTo('#general_data_detail_table_body');
        $('#general_data_detail_table_body tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
    });

    $(document).on('click', '.delete-record', function () {
        var id = jQuery(this).attr('data-id');
        var targetDiv = jQuery(this).attr('targetDiv');
        jQuery('#rec-' + id).remove();
        $('#general_data_detail_table_body tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });

        return true;
    });

    $(document).on('click', '.generate-all-company', function () {
        let idGeneralData = $(this).parent().parent().parent().find('.id_general_data_input').val();
        let code = $(this).parent().parent().parent().find('.code_input').val();

        $.ajax({
            url: "{{ route('mgd.generate') }}",
            data: {
                id_general_data: idGeneralData,
                code
            },
            success: (res) => {
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                })
            },
            error: (res) => {
                swal({
                    title: 'Error',
                    icon: 'error',
                    dangerMode: true,
                    text: res.message
                })
            }
        })

        return true;
    });

    $(document).on('click', '.edit', function () {
        let id_general_type = $(this).attr('id');
        let parent_code = $(this).attr('parent');
        global_id_general_type = id_general_type;
        $("#master_generaldataForm")[0].reset();
        $("#general_data_detail_table_body").html("");
        $("#master_generaldataForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master General Data");
        $(".invalid-feedback").children("strong").text("");
        $("#master_generaldataForm input").removeClass("is-invalid");
        $("#master_generaldataForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "<?= url('general_setting/company_setting/master_general_data/get_detail_master_general_data') ?>",
            method: "GET",
            data: {id_general_type: id_general_type, parent_id: parent_code},
            success: function (response) {
                global_id_master_general_data_detail = 0;
                $.each(response.detail, function (i, item) {
                    $('#new_master_general_data_detail').trigger('click');
                });
                    
                $('#general_type').val(response.master.general_type);
                $('#description').val(response.master.description);
                $('#status').val(response.master.status);
                $('#relation').val(response.master.relation_description);

                // $('.relation-th').css('display', 'block');
                setTimeout(function () {
                    $('#general_data_detail_table_body tr').each(function (index) {
                        // if(!response.parent_relation_options) {
                        //     console.log(!response.parent_relation_options)
                        //     $(this).find('.relation-td').css('display', 'hidden');
                        //     $('.relation-th').css('display', 'hidden');
                        // }
                        if(response.detail[index].restrict_by=="User")
                        {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_general_data_input').val(response.detail[index].id_general_data);
                            $(this).find('.sequence_input').val(response.detail[index].sequence);
                            $(this).find('.code_input').val(response.detail[index].code);
                            $(this).find('.description_input').val(response.detail[index].description);
                            $(this).find('.relation_input').select2({
                                data: response.parent_relation_options
                            });
                            if (response.detail[index].status == 'A') {
                                $(this).find('.active_input').prop('checked', true);
                            } else {
                                $(this).find('.active_input').prop('checked', false);
                            }
                        }
                        else
                        {
                            $("#id-delete-master-general-data"+index).attr("disabled", true);
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_general_data_input').val(response.detail[index].id_general_data).attr("disabled", true);
                            $(this).find('.sequence_input').val(response.detail[index].sequence).attr("disabled", true);
                            $(this).find('.code_input').val(response.detail[index].code).attr("disabled", true);
                            $(this).find('.description_input').val(response.detail[index].description).attr("disabled", true);
                            $(this).find('.relation_input').select2({
                                data: response.parent_relation_options,
                                width: '100%'
                            });
                            if (response.detail[index].status == 'A') {
                                $(this).find('.active_input').prop('checked', true).attr("disabled", true);
                            } else {
                                $(this).find('.active_input').prop('checked', false).attr("disabled", true);
                            }
                        }
                        $(this).find('.relation_input').val(response.detail[index].relation_to_id_general_data).trigger('change');
                    });
                }, 1000);
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
        $('#modal_form_master_general_data').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
        let id_general_type = $(this).attr('id');
        event.preventDefault();
        swal({
            title: 'Are you sure?',
            text: 'This record and it`s details will be permanantly deleted!',
            icon: 'warning',
            buttons: true,
            dangerMode: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then(function (value) {

        });
    });

    $(document).ready(function () {
        $('#general_data_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('general_setting/company_setting/master_general_data/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#general_data_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'general_type', name: 'general_type'},
                {data: 'description', name: 'description'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
                        return data;
                    }
                },
            ]
        });
		$('#advanced').click(function(){
			$('.cf').select2({width:'100%'});
			if($("#cf").css('display') == 'none'){
				$("#cf").show("slow");
			}
			else {
				$("#cf").hide("slow");
			}		
		});

        $('#master_generaldataForm').submit(function (e) {
            e.preventDefault();
            $("#master_generaldataForm :input").attr('disabled', false);
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#master_generaldataForm input").removeClass("is-invalid");
            $("#master_generaldataForm textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
                url: '<?= url('general_setting/company_setting/master_general_data/save_master_general_data?id_general_type=') ?>' + global_id_general_type,
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
                        $('#modal_form_master_general_data').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        refresh_data();
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

    function refresh_data() {
        $('#general_data_table').DataTable().ajax.reload();
    }
</script>
@endsection