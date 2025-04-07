@extends('adminlte::page')
@section('title', 'Master Insurance')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Insurance</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Insurance</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="insurance_table" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Insurance Code</th>
                            <th>Insurance Class</th>
                            <th>Insurance Name</th>
                            <th>Address</th>
                            <th>Bill Amount</th>
                            <th>Status</th>
                            <th style="text-align:center;" width=100>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_insurance"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_insuranceForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Insurance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Insurance Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="insurance_code" id="insurance_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="insurance_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Insurance Name</label>
                                <div class="col-sm-8">
                                    <input type="text" name="insurance_name" id="insurance_name" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="insurance_nameError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Insurance Class</label>
                                <div class="col-sm-8">
                                    <input type="text" name="insurance_class" id="insurance_class" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="insurance_classError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Bill Amount</label>
                                <div class="col-sm-8">
                                    <input type="text" name="bill_amount" id="bill_amount" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="bill_amountError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <label class="col-sm-12 col-form-label">Address</label>
                                <div class="col-sm-12">
                                    <textarea name="address" id="address" class="form-control form-control-sm" rows="4"></textarea>
                                    <span class="invalid-feedback" role="alert" id="addressError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" style="margin-top: 10px">
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
    let global_id_master_insurance = "";

    $(document).on('click', '.new', function () {
        global_id_master_insurance = "";
        $("#master_insuranceForm")[0].reset();
        $("#master_insuranceForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Insurance");
        $(".invalid-feedback").children("strong").text("");
        $("#master_insuranceForm input").removeClass("is-invalid");
        $("#master_insuranceForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');
        $('#modal_form_master_insurance').modal('show');
    });

    $(document).on('click', '.edit', function () {
        let id_master_insurance = $(this).attr('id');
        global_id_master_insurance = id_master_insurance;
        $("#master_insuranceForm")[0].reset();
        $("#master_insuranceForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Insurance");
        $(".invalid-feedback").children("strong").text("");
        $("#master_insuranceForm input").removeClass("is-invalid");
        $("#master_insuranceForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "<?= url('employee/employee_setting/master_insurance/get_detail_master_insurance') ?>",
            method: "GET",
            data: {id_master_insurance: id_master_insurance},
            success: function (response) {
                $('#insurance_code').val(response.insurance_code);
                $('#insurance_name').val(response.insurance_name);
                $('#insurance_class').val(response.insurance_class);
                $('#bill_amount').val(response.bill_amount);
                $('#address').val(response.address);
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
        $('#modal_form_master_insurance').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
        let id_master_insurance = $(this).attr('id');
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
                    url: "<?= url('employee/employee_setting/master_insurance/destroy_master_insurance') ?>",
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {id_master_insurance: id_master_insurance},
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
                });
            }
        });
    });

    $(document).ready(function () {
        $('#insurance_table').DataTable({
            processing: true,
            serverSide: true,
            scrollY: true,
            ajax: {
                url: "<?= url('employee/employee_setting/master_insurance/get_data') ?>",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#insurance_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    orderable: false
                },
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'insurance_code', name: 'insurance_code'},
                {data: 'insurance_class', name: 'insurance_class'},
                {data: 'description', name: 'description'},
                {data: 'insurance_address', name: 'insurance_address'},
                {data: 'bill_amount', name: 'bill_amount'},
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

        $('#master_insuranceForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $("#master_insuranceForm input").removeClass("is-invalid");
            $("#master_insuranceForm textarea").removeClass("is-invalid");

            $.ajax({
                method: "POST",
                headers: {
                    Accept: "application/json"
                },
                url: '<?= url('employee/employee_setting/master_insurance/save_master_insurance?id_master_insurance=') ?>' + global_id_master_insurance,
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
                        $('#modal_form_master_insurance').modal('hide');
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
        $('#insurance_table').DataTable().ajax.reload();
    }
</script>
@endsection