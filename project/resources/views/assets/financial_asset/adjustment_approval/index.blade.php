@extends('adminlte::page')
@section('title', 'Asset Adjustment Approval')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #99a9b4;
        box-shadow: none;
    }
    .select2-container--open {
        z-index: 1650
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

    legend {
        font-size: 10pt;
    }

    .error {
        color: #ff0000;
        font-weight: 600;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Asset Adjustment Approval
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button>
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Adjustment Request</button> --}}
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="assetGroupTable" style="width: 100%;">
                    <thead>
                        <tr>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAdd" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Add Asset Adjustment Request</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_adjustment_header" id="id_adjustment_header" class="pk">
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="reference_number">Reference Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="reference_number" id="reference_number" class="form-control form-control-sm store-change" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="description">Description</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm store-change" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="adjustment_date">Adjustment Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="adjustment_date" id="adjustment_date" class="form-control form-control-sm store-change date" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_employee">Request By</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_employee" id="id_employee" class="form-control form-control-sm" style="width:100%" disabled>
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_period">Period Date</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_period" id="id_period" class="form-control form-control-sm" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="document_status">Document Status</label>
                                </div>
                                <div class="col cont">
                                    <span name="document_status" id="document_status">
                                    </span>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="status">Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="status" id="status" class="form-control form-control-sm" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                <div class="col cont">
                                    <label for="revaluation_flag">Revaluation</label>
                                </div>
                                <div class="col cont">
                                    <div class="d-flex">
                                        <input type="checkbox" name="revaluation_flag" id="revaluation_flag" class="form-control form-control-sm" style="width:100%">
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="mt-5" style="overflow-x: auto">
                        <div class="pull-right mb-2">
                            <button type="button" class="btn btn-sm btn-primary" id="addDetail"><i class="fa fa-plus"></i> Add Detail</button>
                        </div>
                        <table id="assignedEmployeeTable" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width:150px;">Asset</th>
                                    <th style="width:150px;">Current Cost</th>
                                    <th style="width:150px;">Adjusted Cost</th>
                                    <th>Account</th>
                                    <th style="width:150px;">Counterpart Account</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="assignedEmployeeTableBody"></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success approve"><i class="fas fa-check"></i> Approve</span>
                <button type="button" class="btn btn-sm btn-danger reject"><i class="fas fa-times"></i> Reject</span>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_id_adjustment_detail cont">
                <input type="hidden" name="detail[][id_adjustment_detail]" class="detail-id_adjustment_detail-input">
                <select name="detail[][id_asset]" class="form-control form-control-sm store-value detail-id_asset-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_current_cost cont">
                <input name="detail[][current_cost]" class="form-control form-control-sm store-value detail-current_cost-input money">
                <span class="error"></span>
            </td>
            <td class="detail_id_adjusted_cost cont">
                <input name="detail[][adjusted_cost]" class="form-control form-control-sm store-value detail-adjusted_cost-input money">
                <span class="error"></span>
            </td>
            <td class="detail_id_account cont">
                <select name="detail[][id_account]" class="form-control form-control-sm store-value detail-id_account-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_counterpart_account cont">
                <select name="detail[][id_counterpart_account]" class="form-control form-control-sm store-value detail-id_counterpart_account-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_status cont">
                <select name="detail[][status]" class="form-control form-control-sm store-value detail-status-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_action cont"></td>
        </tr>
    </tbody>
</table>
@endsection
@section('scripts')
<script type="text/javascript">
    @include('assets.advanced_search')

    let periods, assets, employees, idEmployee, branches, accounts = [];
    let toModify = ['id_asset', 'current_cost', 'adjusted_cost', 'id_account', 'id_counterpart_account', 'id_adjustment_detail', 'status'];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    $('#status').empty().select2({
        data: status,
    });

    function handleError(jqAjaxErrorInstance) {
        swal({
            icon: 'error',
            title: 'Error',
            text: jqAjaxErrorInstance.responseJSON.message,
        });
    }

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('assets.adjustment.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                employees = res.data.employees;
                idEmployee = res.data.id_employee;
                assets = res.data.assets;
                periods = res.data.periods;
                accounts = res.data.accounts;
                if(modifyDependentElements) {
                    $('#id_period').empty().prepend('<option></option>').select2({
                        data: res.data.periods,
                        allowClear: true,
                        placeholder: 'Select Period'
                    });
                    $('#id_employee').empty().prepend('<option></option>').select2({
                        data: res.data.employees,
                        allowClear: true,
                        placeholder: 'Select Request By'
                    }).val(res.data.id_employee).trigger('change');
                    $('#id_retirement_header_source').empty().prepend('<option></option>').select2({
                        data: res.data.retirement_headers,
                        allowClear: true,
                        placeholder: 'Select Retirement Source'
                    });
                    $('#modalAdd').find('select').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    });
                    $('#modalAdd').find('.date').daterangepicker({
                        singleDatePicker: true,
                        autoApply: false,
                        showDropdowns: true,
                        // autoUpdateInput: false,
                        locale: {
                            format: 'YYYY-MM-DD'
                        }
                    }).on('apply.daterangepicker', function(ev, picker) {
                        $(this).val(picker.startDate.format('YYYY-MM-DD'));
                    }).on('cancel.daterangepicker', function(ev, picker) {
                        $(this).val('');
                    });
                }
            },
            error: handleError
        });
    }

    getInitData(true);

    $(document).on('change', '#id_approval', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Asset Adjustment Request');
        $('#addDetail').show();
        $('.error').text('');
        $('#assignedEmployeeTableBody').empty();
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select, .pk').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#id_employee').val(idEmployee).trigger('change');
        $('#document_status').empty();
        $('#adjustment_date').val(moment().format('YYYY-MM-DD'));
        $('#status').val('A').trigger('change');
        $('#modalAdd').modal('show');
    });

    function cloneDetailRow(append = false, appendTarget = null) {
        let clone = $('#sampleDetailTable').find('tr').clone();
        clone.find('.date').daterangepicker({
            singleDatePicker: true,
            autoApply: false,
            showDropdowns: true,
            // autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        clone.find('.detail-id_asset-input').empty().prepend('<option></option>').select2({ 
            data: assets,
            allowClear: true,
            placeholder: 'Select Asset',
        });
        clone.find('.detail-id_account-input, .detail-id_counterpart_account-input').empty().prepend('<option></option>').select2({ 
            data: accounts,
            allowClear: true,
            placeholder: 'Select Account',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Approval Asset Adjustment Request');
        $('#modalAdd').find('.approve').attr('id-adjustment-header', $(this).attr('id-adjustment-header'));
        $('#modalAdd').find('.reject').attr('id-adjustment-header', $(this).attr('id-adjustment-header'));
        $('#addDetail').hide();
        $.ajax({
            url: "{{ route('assets.adjustment.get_edit') }}",
            data: {
                id_adjustment_header: $(this).attr('id-adjustment-header'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#assignedEmployeeTableBody').empty();
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                $('#id_retirement_header_source').trigger('change');
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_adjustment_detail-input').val(detail.id_adjustment_detail)
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change').attr('default-value', detail.id_asset);
                    clone.find('.detail-current_cost-input').val(detail.current_cost).trigger('change');
                    clone.find('.detail-adjusted_cost-input').val(detail.adjusted_cost).trigger('change');
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-id_counterpart_account-input').val(detail.id_counterpart_account).trigger('change').attr('default-value', detail.id_counterpart_account);
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('readonly', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').children().each((index, row) => {
                    $(row).find('.detail_no').text(index+1);
                    toModify.forEach((key) => {
                        $(row).find(`.detail-${key}-input`).attr('name', `detail[${index}][${key}]`);
                    });
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').find('input, select').attr('disabled', true);
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#addDetail', function() {
        let clone = $('#sampleDetailTable').find('tr').clone();
        clone.find('.date').daterangepicker({
            singleDatePicker: true,
            autoApply: false,
            showDropdowns: true,
            // autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        clone.find('.detail-id_asset-input').empty().prepend('<option></option>').select2({
            data: assets,
            placeholder: 'Select Asset',
            allowClear: true
        });
        clone.find('.detail-id_account-input, .detail-id_counterpart_account-input').empty().prepend('<option></option>').select2({
            data: accounts,
            placeholder: 'Select Account',
            allowClear: true
        });
        clone.find('.detail-status-input').empty().select2({ data: status });
        clone.find('.detail_action').html(`
            <button type="button" class="btn btn-sm btn-danger delete-row"><i class="fas fa-trash"></i></button>
        `);

        clone.appendTo($('#assignedEmployeeTableBody'));
        $('#assignedEmployeeTableBody').children().each((index, row) => {
            $(row).find('.detail_no').text(index+1);
            if($(row).find('.detail-sequence-input').val() == '') {
                $(row).find('.detail-sequence-input').val(index+1);
            }
            toModify.forEach((key) => {
                $(row).find(`.detail-${key}-input`).attr('name', `detail[${index}][${key}]`);
            });
        })
    });

    $(document).on('click', '.delete-row', function() {
        $(this).closest('tr').remove();
        $('#assignedEmployeeTableBody').children().each((index, row) => {
            $(row).find('.detail_no').text(index+1);
        });
    });

    $(document).on('change', '#id_asset_group', function () {
        if(!$(this).val() || $('#id_asset').val() != "") {
            return;
        }
        let props = null;
        assetGroups.forEach((assetGroup) => {
            if($(this).val() == assetGroup.id) {
                props = assetGroup;
            }
        });
        if($('#depreciation_flag').is(':checked') != props.depreciation_flag) {
            $('#depreciation_flag').trigger('click');
        }
        $('#id_depreciation_method').val(props.id_depreciation_method).trigger('change');
        $('#life_in_month').val(props.life_in_month);
        $('#salvage_type').val(props.salvage_type).trigger('change');
        $('#salvage_value').val(props.salvage_value);
    });

    $(document).on('change', '.detail-id_branch_destination-input', function () {
        $.ajax({
            url: "{{ route('assets.adjustment.get_data') }}",
            data: {
                id_branch: $(this).val(),
            },
            success: (res) => {
                $(this).closest('tr').find('.detail-id_location_destination-input').empty().prepend('<option></option>').select2({ 
                    data: res.data.locations,
                    allowClear: true,
                    placeholder: 'Select Location',
                });
                if($(this).closest('tr').find('.detail-id_location_destination-input').attr('default-value')) {
                    $(this).closest('tr').find('.detail-id_location_destination-input').val(
                        $(this).closest('tr').find('.detail-id_location_destination-input').attr('default-value')
                    ).trigger('change');
                }
            }
        })
    });

    $(document).on('change', '.detail-id_location_destination-input', function () {
        $.ajax({
            url: "{{ route('assets.transfer.get_data') }}",
            data: {
                id_location: $(this).val(),
            },
            success: (res) => {
                $(this).closest('tr').find('.detail-id_asset_location_destination-input').empty().prepend('<option></option>').select2({ 
                    data: res.data.asset_locations,
                    allowClear: true,
                    placeholder: 'Select Asset Location',
                });
                if($(this).closest('tr').find('.detail-id_asset_location_destination-input').attr('default-value')) {
                    $(this).closest('tr').find('.detail-id_asset_location_destination-input').val(
                        $(this).closest('tr').find('.detail-id_asset_location_destination-input').attr('default-value')
                    ).trigger('change');
                }
            }
        })
    });

    $(document).on('change', '.detail-id_asset-input', function() {
        $.ajax({
            url: "{{ route('assets.adjustment.get_data') }}",
            data: {
                id_asset: $(this).val(),
            },
            success: (res) => {
                $(this)
                    .closest('tr')
                    .find('.detail-current_cost-input')
                    .attr('readonly', true)
                    .val(res.data.current_cost)
                    .trigger('change');
            }
        })
    });

    $(document).on('click', '#submitApply', function() {
        $('#assetGroupForm').submit();
    });

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.adjustment.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: () => {
                $('.error').empty();
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#modalAdd').modal('hide');
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            },
            error: (err) => {
                handleError(err);
                if(err.status == 422) {
                    Object.entries(err.responseJSON.errors).forEach((obj) => {
                        if(obj[0].includes('.')) {
                            let target = `${obj[0].replace(".", "[").replaceAll(".", "][")}]`;
                            console.log(target)
                            $(`[name='${target}']`).closest('.cont').find('.error').text(obj[1][0]);
                        } else {
                            $(`#${obj[0]}`).closest('.cont').find('.error').text(obj[1][0]);
                        }
                    });
                }
            },
            complete: () => {
                $('#loader').addClass('hidden');
            }
        })
    });

    $(document).on('change', '.store-change', function() {
        $(this).attr('default-value', $(this).val());
    });

    $(document).on('change', '.money', function () {
        let value = $(this).val();
        value = value.replace(/,|\./g, '').replace("Rp", "").trim();
        if(!value || isNaN(value)) {
            $(this).val(0);
            return;
        }
        let formatted  = Number.parseInt(value).toLocaleString('id-ID', { maximumSignificantDigit: 21 });
        $(this).val(formatted);
    });

    $(document).on('change', 'input:checkbox', function() {
        let id = $(this).attr('id');
        if($(this).is(':checked')) {
            $(`.bind-true.bind-${id}`).attr('readonly', false);
            $(`.bind-false.bind-${id}`).attr('readonly', true);
        } else {
            $(`.bind-true.bind-${id}`).attr('readonly', true);
            $(`.bind-false.bind-${id}`).attr('readonly', false);
        }
    })

    function approveOrReject(action, id) { 
        let url = action == 'approve' ? "{{ route('assets.adjustment_approval.approve') }}" : "{{ route('assets.adjustment_approval.reject') }}";
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id_adjustment_header: id,
            },
            success: (res) => {
                $('#modalAdd').modal('hide');
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                });
            },
            error: (err) => {
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'error',
                    title: 'Failed',
                    text: err.responseJSON.message,
                });
            }
        })
        
    }

    $(document).on('click', '.approve', function() {
        swal({
            icon: 'warning',
            title: 'Approve Request?',
            text: 'Do you want to approve this request?',
            buttons: ['No, Cancel', 'Yes, Approve']
        }).then((confirm) => {
            if(confirm) {
                approveOrReject('approve', $(this).attr('id-adjustment-header'));
            }
        })
    });

    $(document).on('click', '.reject', function() {
        swal({
            icon: 'warning',
            title: 'Reject Request?',
            text: 'Do you want to reject this request?',
            buttons: ['No, Cancel', 'Yes, Reject']
        }).then((confirm) => {
            if(confirm) {
                approveOrReject('reject', $(this).attr('id-adjustment-header'));
            }
        })
    });

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.adjustment_approval')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'reference_number', name: 'reference_number', title: 'Reference Number'},
            { data: 'request_start_date', name: 'adjustment_date', title: 'Adjustment Date'},
            { data: 'name', name: 'request_by', title: 'Request By'},
            { data: 'note', name: 'description', title: 'Description'},
            { 
                data: 'approval_status', 
                name: 'approval_status_description', 
                title: 'Document Status', 
            },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `
                        <span class="btn btn-success approve" id-adjustment-header="${data}"><i class="fas fa-check"></i></span>
                        <span class="btn btn-danger reject" id-adjustment-header="${data}"><i class="fas fa-times"></i></span>
                        <span class="btn btn-warning edit" id-adjustment-header="${data}"><i class="fas fa-eye" style="color:#fff;"></i></span>
                        `;
                }
            }
        ]
    });
</script>
@endsection