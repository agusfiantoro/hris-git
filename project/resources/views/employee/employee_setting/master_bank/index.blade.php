@extends('adminlte::page')
@section('title', 'Master Bank')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Bank</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Bank</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="bank_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Bank Code</th>
                            <th>Transfer Code</th>
                            <th>Bank Name</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_bank"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_bankForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Bank</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Bank Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="bank_code" id="bank_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="bank_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Bank Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="bank_name" id="bank_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="bank_nameError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Transfer Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="transfer_code" id="transfer_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="transfer_codeError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
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


@section('scripts')
<script type="text/javascript">
    let global_id_bank = "";
    
    $(document).on('click', '.new', function () {
        global_id_bank = "";
        $("#master_bankForm")[0].reset();
        $("#master_bankForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Bank");
        $(".invalid-feedback").children("strong").text("");
        $("#master_bankForm input").removeClass("is-invalid");
        $("#master_bankForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_master_bank').modal('show');
    });

    $(document).on('click', '.edit', function () {
        let id_bank = $(this).attr('id');
        global_id_bank = id_bank;
        $("#master_bankForm")[0].reset();
        $("#master_bankForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Bank");
        $(".invalid-feedback").children("strong").text("");
        $("#master_bankForm input").removeClass("is-invalid");
        $("#master_bankForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "<?= url('employee/employee_setting/master_bank/get_detail_master_bank') ?>",
            method: "GET",
            data: {id_bank: id_bank},
            success: function (response) {
                $('#bank_code').val(response.bank_code);
                $('#bank_name').val(response.bank_name);
                $('#transfer_code').val(response.transfer_code);
                $('#status').val(response.status);
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
        $('#modal_form_master_bank').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
        let id_bank = $(this).attr('id');
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
            if (value) {
                $.ajax({
                    url: "<?= url('employee/employee_setting/master_bank/destroy_master_bank') ?>",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id_bank: id_bank},
                    success: function (response) {
                        if (response.status == 'false') {
                            swal({
                                icon: "error",
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [' + response.message + ']'
                            });
                        } else if (response.status == 'true') {
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
                        swal({
                            icon: 'error',
                            title: 'Oops...',
                            dangerMode: true,
                            text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                        });
                    }
                })
            }

        });
    });

    $(document).ready(function () {
        $('#bank_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('employee/employee_setting/master_bank/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#bank_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'bank_code', name: 'bank_code'},
                {data: 'transfer_code', name: 'transfer_code'},
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

        $('#master_bankForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#master_bankForm input").removeClass("is-invalid");
            $("#master_bankForm textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
                url: '<?= url('employee/employee_setting/master_bank/save_master_bank?id_bank=') ?>' + global_id_bank,
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
                        $('#modal_form_master_bank').modal('hide');
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
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "Error").children("strong").text(errors[key][0]);
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
        $('#bank_table').DataTable().ajax.reload();
    }
</script>
@endsection