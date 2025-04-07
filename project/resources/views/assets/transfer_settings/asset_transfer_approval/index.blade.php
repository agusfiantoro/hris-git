@extends('adminlte::page')
@section('title', 'Asset Approval')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
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
                <h5 class="card-title">Asset Approval
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    {{-- <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Transfer Request</button> --}}
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
<div id="modular-container">

</div>
@endsection
@section('scripts')
<script type="text/javascript">
    @include('assets.advanced_search')

    let assetCategories, assetGroups, depreciationMethods, assets, employees, idEmployee, branches, accounts, periods, retirementHeaders, retirementDetails = [];
    let toModify = ['id_asset', 'id_transfer_detail', 'id_employee_destination', 'unit_assigned', 'id_branch_destination', 'id_location_destination', 'id_asset_location_destination', 'effective_date', 'status'];
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

    $(document).on('change', '#id_approval', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Transfer Request');
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
        $('#request_date').val(moment().format('YYYY-MM-DD'));
        $('#modalAdd').modal('show');
    });


    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Transfer Request');
        $('#addTransferDetail').hide();
        $.ajax({
            url: "{{ route('assets.transfer.get_edit') }}",
            data: {
                id_transfer_header: $(this).attr('id-header'),
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
                $('#modalAdd').find('.approve').attr('id-header', $(this).attr('id-header'));
                $('#modalAdd').find('.reject').attr('id-header', $(this).attr('id-header'));
                $('#modalAdd').find('.approve').attr('source-transaction-type', $(this).attr('source-transaction-type'));
                $('#modalAdd').find('.reject').attr('source-transaction-type', $(this).attr('source-transaction-type'));
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_transfer_detail-input').val(detail.id_asset)
                    clone.find('.detail_effective_date').val(detail.effective_date).trigger('apply');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change');
                    clone.find('.detail-id_employee_destination-input').val(detail.id_employee_destination).trigger('change');
                    clone.find('.detail-unit_assigned-input').val(detail.unit_assigned).trigger('change');
                    clone.find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
                    clone.find('.detail-id_location_destination-input').val(detail.id_location_destination).trigger('change').attr('default-value', detail.id_location_destination);
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('input, select').attr('disabled', true);
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').find('input, select').attr('disabled', true);
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#addTransferDetail', function() {
        if($('#assignedEmployeeTableBody').children().length > 0) {
            return;
        }
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
        clone.find('.detail-id_employee_destination-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_branch_destination-input').empty().prepend('<option></option>').select2({ 
            data: branches,
            allowClear: true,
            placeholder: 'Select Branch',
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
        console.log(props);
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
            url: "{{ route('assets.transfer.get_data') }}",
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

    function modalAdjustment(idHeader) {
        $('#modalEditTitle').text('Approval Asset Adjustment Request');
        $('#addDetail').hide();
        $.ajax({
            url: "{{ route('assets.adjustment.get_edit') }}",
            data: {
                id_adjustment_header: idHeader,
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
    }

    function modalTransfer(idHeader) {
        $('#modalEditTitle').text('Edit Transfer Request');
        $('#addTransferDetail').hide();
        $.ajax({
            url: "{{ route('assets.transfer.get_edit') }}",
            data: {
                id_transfer_header: idHeader,
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
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_transfer_detail-input').val(detail.id_asset)
                    clone.find('.detail_effective_date').val(detail.effective_date).trigger('apply');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change');
                    clone.find('.detail-id_employee_destination-input').val(detail.id_employee_destination).trigger('change');
                    clone.find('.detail-unit_assigned-input').val(detail.unit_assigned).trigger('change');
                    clone.find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
                    clone.find('.detail-id_location_destination-input').val(detail.id_location_destination).trigger('change').attr('default-value', detail.id_location_destination);
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('input, select').attr('disabled', true);
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').find('input, select').attr('disabled', true);
        $('#modalAdd').modal('show');
    }

    function modalRetirement(idHeader) {
        $('#modalEditTitle').text('Edit Asset Retirement Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.retirement.get_edit') }}",
            data: {
                id_retirement_header: idHeader,
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
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_retirement_detail-input').val(detail.id_asset)
                    clone.find('.detail_effective_date').val(detail.effective_date).trigger('apply');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change');
                    clone.find('.detail-id_employee_destination-input').val(detail.id_employee_destination).trigger('change');
                    clone.find('.detail-unit_assigned-input').val(detail.unit_assigned).trigger('change');
                    clone.find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
                    clone.find('.detail-id_location_destination-input').val(detail.id_location_destination).trigger('change').attr('default-value', detail.id_location_destination);
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('input, select').prop('disabled', true);
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').modal('show');
    }

    function modalReinstate(idHeader) {
        $('#modalEditTitle').text('Asset Reinstatement Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.reinstatement.get_edit') }}",
            data: {
                id_reinstate_header: idHeader,
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
                $('#modalAdd').find('.approve').attr('id-reinstate-header', $(this).attr('id-reinstate-header'));
                $('#modalAdd').find('.reject').attr('id-reinstate-header', $(this).attr('id-reinstate-header'));
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
                    clone.find('.detail-id_reinstate_detail-input').val(detail.id_asset)
                    clone.find('.detail_effective_date').val(detail.effective_date).trigger('apply');
                    clone.find('.detail-id_retirement_detail_source-input').val(detail.id_retirement_detail_source).trigger('change').attr('default-value', detail.id_retirement_detail_source);
                    clone.find('.detail-id_employee_destination-input').val(detail.id_employee_destination).trigger('change');
                    clone.find('.detail-unit_assigned-input').val(detail.unit_assigned).trigger('change');
                    clone.find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
                    clone.find('.detail-id_location_destination-input').val(detail.id_location_destination).trigger('change').attr('default-value', detail.id_location_destination);
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('input, select').prop('disabled', true);
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').modal('show');
    }

    function modalRevaluation(idHeader) {
        $('#modalEditTitle').text('Edit Asset Revaluation Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.revaluations.get_edit') }}",
            data: {
                id_revaluation_header: idHeader,
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
                $('#modalAdd').find('.approve').attr('id-revaluation-header', $(this).attr('id-revaluation-header'));
                $('#modalAdd').find('.reject').attr('id-revaluation-header', $(this).attr('id-revaluation-header'));
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_revaluation_detail-input').val(detail.id_revaluation_detail)
                    clone.find('.detail-id_asset_category-input').val(detail.id_asset_category).trigger('change');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change').attr('default-value', detail.id_asset);
                    clone.find('.detail-revaluation_amount-input').val(detail.revaluation_amount).trigger('change');
                    clone.find('.detail-revaluation_percentage-input').val(detail.revaluation_percentage).trigger('change');
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-id_counterpart_account-input').val(detail.id_counterpart_account).trigger('change');
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
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
    }

    function modalImpairment(idHeader) {
        $('#modalEditTitle').text('Edit Asset Impairment Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.impairments.get_edit') }}",
            data: {
                id_impairment_header: idHeader,
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
                $('#modalAdd').find('.approve').attr('id-impairment-header', $(this).attr('id-impairment-header'));
                $('#modalAdd').find('.reject').attr('id-impairment-header', $(this).attr('id-impairment-header'));
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
                }
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_impairment_detail-input').val(detail.id_impairment_detail)
                    clone.find('.detail-id_asset_category-input').val(detail.id_asset_category).trigger('change');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change').attr('default-value', detail.id_asset);
                    clone.find('.detail-impairment_amount-input').val(detail.impairment_amount).trigger('change');
                    clone.find('.detail-impairment_percentage-input').val(detail.impairment_percentage).trigger('change');
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-id_counterpart_account-input').val(detail.id_counterpart_account).trigger('change');
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
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
    }

    function getModal(sourceTransactionType, idHeader) {
        $.ajax({
            url: "{{ route('assets.transfer_approval.get_modal') }}",
            data: {
                source_transaction_type: sourceTransactionType,
            },
            success: (res) => {
                $('#modular-container').html(res);
                $('#modalAdd').find('.approve')
                            .attr('id-header', idHeader)
                            .attr('source-transaction-type', sourceTransactionType);
                $('#modalAdd').find('.reject')
                            .attr('id-header', idHeader)
                            .attr('source-transaction-type', sourceTransactionType);
                getInitData(true, idHeader);
            }
        })
    }

    function approveOrReject(action, id, source) { 
        let url = action == 'approve' ? "{{ route('assets.transfer_approval.approve') }}" : "{{ route('assets.transfer_approval.reject') }}";
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                id_header: id,
                source_transaction_type: source
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
                approveOrReject('approve', $(this).attr('id-header'), $(this).attr('source-transaction-type'));
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
                approveOrReject('reject', $(this).attr('id-header'), $(this).attr('source-transaction-type'));
            }
        })
    });

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.transfer_approval')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'reference_number', name: 'reference_number', title: 'Reference Number'},
            { data: 'request_start_date', name: 'request_date', title: 'Request Date'},
            { data: 'request_group', name: 'request_group', title: 'Request Group'},
            { data: 'request_end_date', name: 'need_date', title: 'Need Date'},
            { data: 'name', name: 'request_by', title: 'Request By'},
            { data: 'note', name: 'note', title: 'Description'},
            { 
                data: 'approval_status', 
                name: 'approval_status', 
                title: 'Approval Status', 
            },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    if(!row.source_transaction_type) {
                        return '';
                    }
                    return `
                        <span class="btn btn-success btn-sm approve mt-1" source-transaction-type="${row.source_transaction_type}" id-header="${data}"><i class="fas fa-check"></i></span>
                        <span class="btn btn-danger btn-sm reject mt-1" source-transaction-type="${row.source_transaction_type}" id-header="${data}"><i class="fas fa-times"></i></span>
                        <span class="btn btn-warning btn-sm mt-1" onclick="getModal('${row.source_transaction_type}', ${data})" source-transaction-type="${row.source_transaction_type}" id-header="${data}"><i class="fas fa-eye" style="color:#fff;"></i></span>
                        `;
                }
            }
        ]
    });
</script>
@endsection