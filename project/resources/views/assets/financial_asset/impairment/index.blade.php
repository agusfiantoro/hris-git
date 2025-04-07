@extends('adminlte::page')
@section('title', 'Asset Impairment')

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

    .loading-readonly-element {
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
                <h5 class="card-title">Asset Impairment
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Impairment Request</button>
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
                <h5 class="modal-title" id="modalEditTitle">Add Asset Impairment Request</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_impairment_header" id="id_impairment_header" class="pk">
                        <input type="hidden" name="is_submit" id="is_submit" class="submit-flag">
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
                                    <label for="revaluation_date">Impairment Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="impairment_date" id="revaluation_date" class="form-control form-control-sm store-change date" style="width:100%" disabled>
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
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="method">Method</label>
                                </div>
                                <div class="col cont">
                                    <select name="method" id="method" class="form-control form-control-sm" style="width:100%">
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
                        <div class="col-xl-12" id="tab_detail">
                            <div class="nav nav-tabs justify-content-left mb-4">
                              <a class="nav-item nav-link active" id="tab-detail" data-toggle="tab" href="#tab-pane-1">Detail
                                  <span class="error-tab text-red hidden">Error</span>
                              </a>
                              <a class="nav-item nav-link" id="tab-approval" data-toggle="tab" href="#tab-pane-2">List Approval
                                  <span class="error-tab text-red hidden">Error</span>
                              </a>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-pane-1">
                                    <div class="pull-right mb-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="addRetirementDetail"><i class="fa fa-plus"></i> Add Detail</button>
                                    </div>
                                    <table id="assignedEmployeeTable" class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th style="width:150px;" class="category-group">Asset Category</th>
                                                <th style="width:150px;" class="asset-group">Asset</th>
                                                <th style="width:150px;" class="asset-group">Impairment Amount</th>
                                                <th style="width:150px;" class="category-group">Impairment Percentage</th>
                                                <th style="width:150px;">Account</th>
                                                <th style="width:150px;">Counterpart Account</th>
                                                <th style="width:150px;">Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="assignedEmployeeTableBody"></tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="tab-pane-2">
                                    <table id="approvalTable" class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Sequence</th>
                                                <th>Approval Name</th>
                                                <th>Approval Status</th>
                                                <th>Approval Execute</th>
                                            </tr>
                                        </thead>
                                        <tbody id="approvalTableBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-info btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-paper-plane"></i> <span id="saveLabel">Submit</span></button>&nbsp;
                <button type="button" class="btn btn-sm btn-success btn-submit" id="submitDraft" name="submitDraft" value="draft"><i class="fas fa-save"></i> <span id="">Save as Draft</span></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleApprovalTable" style="display:none;">
    <tbody>
        <tr>
            <td class="approval_no"></td>
            <td class="approval_sequence"></td>
            <td class="approval_name"></td>
            <td class="approval_status"></td>
            <td class="approval_execute"></td>
        </tr>
    </tbody>
</table>
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_id_impairment_detail_source cont category-group">
                <input type="hidden" name="detail[][id_impairment_detail]" class="detail-id_impairment_detail-input">
                <select name="detail[][id_asset_category]" class="form-control form-control-sm store-value detail-id_asset_category-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_asset cont asset-group">
                <select name="detail[][id_asset]" class="form-control form-control-sm store-value detail-id_asset-input" style="width:100%"></select>
                <span class="error"></span>
            </td>
            <td class="detail_impairment_amount cont asset-group">
                <input name="detail[][impairment_amount]" class="form-control form-control-sm store-value detail-impairment_amount-input money">
                <span class="error"></span>
            </td>
            <td class="detail_impairment_percentage cont category-group">
                <input name="detail[][impairment_percentage]" class="form-control form-control-sm store-value detail-impairment_percentage-input">
                <span class="error"></span>
            </td>
            <td class="detail_id_account cont">
                <select name="detail[][id_account]" class="form-control form-control-sm store-value detail-id_account-input" readonly="true"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id__counterpart_account cont">
                <select name="detail[][id_counterpart_account]" class="form-control form-control-sm store-value detail-id_counterpart_account-input" readonly="true"></select>
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

    let periods, assets, assetCategories, employees, idEmployee, branches, retirementHeaders, accounts, employeeApprovals = [];
    let toModify = ['id_impairment_detail', 'id_asset_category', 'id_asset', 'impairment_amount', 'impairment_percentage', 'id_account', 'id_counterpart_account', 'status'];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let methods = [
        { id: 'category', text: 'Category' },
        { id: 'asset', text: 'Asset' },
    ];

    $('#status').empty().select2({
        data: status,
    });

    $('#method').empty().prepend('<option></option>').select2({
        data: methods,
        placeholder: 'Select Method',
        allowClear: true,
    })

    function handleError(jqAjaxErrorInstance) {
        swal({
            icon: 'error',
            title: 'Error',
            text: jqAjaxErrorInstance.responseJSON.message,
        });
    }

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('assets.impairments.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                employees = res.data.employees;
                idEmployee = res.data.id_employee;
                branches = res.data.branches;
                assetCategories = res.data.asset_categories;
                periods = res.data.periods;
                retirementHeaders = res.data.retirement_headers;
                accounts = res.data.accounts;
                assets = res.data.assets;
                employeeApprovals = res.data.approval_hierarchy;
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

    $(document).on('click', '.new', function() {
        $('#description, #id_period, #status, #method').attr('readonly', false);
        $('#modalEditTitle').text('Add Asset Impairment Request');
        $('#addRetirementDetail').show();
        $('.error').text('');
        $('#approvalTableBody, #assignedEmployeeTableBody').empty();
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select, .pk').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#id_employee').val(idEmployee).trigger('change');
        $('#document_status').empty();
        $('#revaluation_date').val(moment().format('YYYY-MM-DD'));
        $('#status').val('A').trigger('change');
        $('#submitApply').show();
        employeeApprovals.forEach((approval, index) => {
            let clone = $('#sampleApprovalTable').find('tr').clone();
            clone.find('.approval_no').text(index+1);
            clone.find('.approval_sequence').text(approval.sequence);
            clone.find('.approval_name').text(approval.name);
            clone.find('.approval_status').text(approval.approval_status);
            let approvalExecuteIconClass = '';
            if(approval.approval_execute) {
                approvalExecuteIconClass = 'fa fa-check-square-o text-success';
            } else if(approval.approval_execute === false) {
                approvalExecuteIconClass = 'fa fa-times-circle-o text-danger';
            }
            clone.find('.approval_execute').html(`<i class="${approvalExecuteIconClass}"></i>`);
            clone.appendTo('#approvalTableBody');
        });
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
        clone.find('.detail-id_asset_category-input').empty().prepend('<option></option>').select2({ 
            data: assetCategories,
            allowClear: true,
            placeholder: 'Select Asset Category',
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
        clone.find('.detail-id_branch_destination-input').empty().prepend('<option></option>').select2({ 
            data: branches,
            allowClear: true,
            placeholder: 'Select Branch',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset Impairment Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.impairments.get_edit') }}",
            data: {
                id_impairment_header: $(this).attr('id-impairment-header'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#assignedEmployeeTableBody').empty();
                $('#approvalTableBody').empty();
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
                $('#description, #id_period, #status, #method').attr('readonly', false);
                $('.btn-submit').show();
                if(res.data.document_status.code != 'New') {
                    $('#description, #id_period, #status, #method').attr('readonly', true);
                    $('.btn-submit').hide();
                }
                res.data.approval_transactions?.forEach((approval, index) => {
                    let clone = $('#sampleApprovalTable').find('tr').clone();
                    clone.find('.approval_no').text(index+1);
                    clone.find('.approval_sequence').text(approval.sequence);
                    clone.find('.approval_name').text(approval.approval_name);
                    clone.find('.approval_status').text(approval.approval_status);
                    let approvalExecuteIconClass = '';
                    if(approval.approval_execute) {
                        approvalExecuteIconClass = 'fa fa-check-square-o text-success';
                    } else if(approval.approval_execute === false) {
                        approvalExecuteIconClass = 'fa fa-times-circle-o text-danger';
                    }
                    clone.find('.approval_execute').html(`<i class="${approvalExecuteIconClass}"></i>`);
                    clone.appendTo('#approvalTableBody');
                });
                res.data.details?.forEach((detail, index) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_impairment_detail-input').val(detail.id_impairment_detail)
                    clone.find('.detail-id_asset_category-input').val(detail.id_asset_category).trigger('change');
                    clone.find('.detail-id_asset-input').val(detail.id_asset).trigger('change').attr('default-value', detail.id_asset);
                    clone.find('.detail-impairment_amount-input').val(detail.impairment_amount).trigger('change');
                    clone.find('.detail-impairment_percentage-input').val(detail.impairment_percentage).trigger('change');
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-id_counterpart_account-input').val(detail.id_counterpart_account).trigger('change');
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    if(res.data.document_status.code != 'New') {
                        clone.find('input, select').attr('readonly', true);
                    }
                    toModify.forEach((key) => {
                        clone.find(`.detail-${key}-input`).attr('name', `detail[${index}][${key}]`);
                    });
                    if(clone.find('.detail-id_asset-input').val()) {
                        $('#method').val('asset').trigger('change');
                    } else {
                        $('#method').val('category').trigger('change');
                    }
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                    $(element).find('input, select').each((_inputIndex, inputField) => {
                        $(inputField).attr('name', $(inputField).attr('name').replaceAll('[]', `[${index}]`));
                    })
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#addRetirementDetail', function() {
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
        clone.find('.detail-id_asset_category-input').empty().prepend('<option></option>').select2({ 
            data: assetCategories,
            allowClear: true,
            placeholder: 'Select Asset Category',
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
        clone.find('.detail-id_account-input, .detail-id_counterpart_account-input').empty().prepend('<option></option>').select2({ 
            data: accounts,
            allowClear: true,
            placeholder: 'Select Account',
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

    $(document).on('change', '.detail-id_asset_category-input', function () {
        if($(this).val()) {
            $(this).closest('tr').find('.detail-id_asset-input, .detail-impairment_amount-input').val('').trigger('change').attr('disabled', true);
            if(!$('#id_impairment_header').val()) {
                assetCategories.forEach((assetCategory) => {
                    if(assetCategory.id == $(this).val()) {
                        $(this).closest('tr').find('.detail-id_account-input').val(assetCategory.id_account).trigger('change');
                        $(this).closest('tr').find('.detail-id_counterpart_account-input').val(assetCategory.id_counterpart_account).trigger('change');
                    }
                })
            }
        } else {
            if(!$('#id_impairment_header').val()) {
                $(this).closest('tr').find('.detail-id_account-input').val(null).trigger('change');
                $(this).closest('tr').find('.detail-id_counterpart_account-input').val(null).trigger('change');
            }
            $(this).closest('tr').find('.detail-id_asset-input, .detail-impairment_amount-input').attr('disabled', false);
        }
    });

    $(document).on('change', '.detail-id_asset-input', function () {
        if($(this).val()) {
            if(!$('#id_impairment_header').val()) {
                $.ajax({
                    url: "{{ route('assets.impairments.get_data') }}",
                    data: {
                        id_asset: $(this).val(),
                    },
                    success: (res) => {
                        $(this).closest('tr').find('.detail-id_account-input').val(res.data.asset_category.id_account).trigger('change');
                        $(this).closest('tr').find('.detail-id_counterpart_account-input').val(res.data.asset_category.id_counterpart_account).trigger('change');
                    },
                    error: (err) => {
                        swal({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occured when obtaining account data for this asset: '+err.responseJSON.message,
                        });
                    }
                })
            }
            $(this).closest('tr').find('.detail-id_asset_category-input, .detail-impairment_percentage-input').val('').trigger('change').attr('disabled', true);
        } else {
            if(!$('#id_impairment_header').val()) {
                $(this).closest('tr').find('.detail-id_account-input').val(null).trigger('change');
                $(this).closest('tr').find('.detail-id_counterpart_account-input').val(null).trigger('change');
            }
            $(this).closest('tr').find('.detail-id_asset_category-input, .detail-impairment_percentage-input').attr('disabled', false);
        }
    });

    $(document).on('change', '.detail-id_branch_destination-input', function () {
        $.ajax({
            url: "{{ route('assets.impairments.get_data') }}",
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

    $(document).on('change', '.detail-id_retirement_detail_source-input', function() {
        retirementDetails.forEach((detail) => {
            if(detail.id == $(this).val()) {
                $(this).closest('tr').find('.detail-unit_assigned-input').val(detail.unit_assigned);
                $(this).closest('tr').find('.detail-id_asset_location_destination-input').attr('default-value', detail.id_asset_location_destination);
                $(this).closest('tr').find('.detail-id_location_destination-input').attr('default-value', detail.id_location_destination);
                $(this).closest('tr').find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
            }
        })
    });

    $(document).on('change', '#method', function() {
        if($(this).val() == 'category') {
            $('.detail-id_asset-input').val(null).trigger('change');
            $('.asset-group').hide();
            $('.category-group').show();
        } else if($(this).val() == 'asset') {
            $('.detail-id_asset_category-input').val(null).trigger('change');
            $('.asset-group').show();
            $('.category-group').hide();
        }
    })

    $(document).on('click', '#submitApply', function() {
        $('.submit-flag').val("true");
        $('#assetGroupForm').submit();
    });

    $(document).on('click', '#submitDraft', function() {
        $('.submit-flag').val("false");
        $('#assetGroupForm').submit();
    });

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.impairments.save') }}",
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

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.impairments')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'reference_number', name: 'reference_number', title: 'Reference Number'},
            { data: 'impairment_date', name: 'impairment_date', title: 'Impairment Date'},
            { data: 'impairment_method', name: 'impairment_method', title: 'Impairment Method'},
            { data: 'description', name: 'description', title: 'Description'},
            { 
                data: 'gl_transfer_flag', 
                name: 'gl_transfer_flag', 
                title: 'Transfer Flag', 
            },
            { 
                data: 'id_je_header', 
                name: 'id_je_header', 
                title: 'Journal Entry', 
                render: (data, type, row) => {
                    if(row.journal_header) {
                        return `<a href="{{route('accounting.gl_je')}}?id=${data}">${row.journal_header.reference_number}</a>`;
                    } else return '-';
                } 
            },
            { 
                data: 'id_approval', 
                name: 'id_approval', 
                title: 'Approval', 
                render: (data, type, row) => {
                    if(row.approval) return row.approval.description;
                    else return '-';
                } 
            },
            { 
                data: 'id_document_status', 
                name: 'id_document_status', 
                title: 'Document Status', 
                render: (data, type, row) => {
                    if(row.document_status) return row.document_status.description;
                    else return '-';
                } 
            },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-impairment-header="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection