@extends('adminlte::page')
@section('title', 'Asset Reinstatement')

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
                <h5 class="card-title">Asset Reinstatement
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Reinstate Request</button>
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
                <h5 class="modal-title" id="modalEditTitle">Add Asset Reinstatement Request</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_reinstate_header" id="id_reinstate_header" class="pk">
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
                                    <label for="id_retirement_header">Retirement Source</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_retirement_header_source" id="id_retirement_header_source" class="form-control form-control-sm" style="width:100%;">
                                    </select>
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
                                    <label for="request_date">Request Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="request_date" id="request_date" class="form-control form-control-sm store-change date" style="width:100%" disabled>
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
                                                <th style="width:150px;">Asset Number</th>
                                                <th style="width:150px;">Branch</th>
                                                <th style="width:150px;">Location</th>
                                                <th>Room</th>
                                                <th style="width:150px;">Unit Assigned</th>
                                                <th>Effective Date</th>
                                                <th>Status</th>
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
                <button type="button" class="btn btn-sm btn-success btn-transfer" id="transferJournal"><i class="fas fa-play"></i> <span>Transfer Journal</span></button>&nbsp;
                <button type="button" class="btn btn-sm btn-primary text-white" id="viewJournal"><i class="fa fa-file-text-o "></i> <span>View Journal</span></button>&nbsp;
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
            <td class="detail_id_retirement_detail_source cont">
                <input type="hidden" name="detail[][id_reinstate_detail]" class="detail-id_reinstate_detail-input">
                <select name="detail[][id_retirement_detail_source]" class="form-control form-control-sm store-value detail-id_retirement_detail_source-input" style="width:200px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_branch_destination cont">
                <select name="detail[][id_branch_destination]" disabled class="form-control form-control-sm store-value detail-id_branch_destination-input" style="width:150px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_location_destination cont">
                <select name="detail[][id_location_destination]" disabled class="form-control form-control-sm store-value detail-id_location_destination-input" style="width:150px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_asset_location_destination cont">
                <select name="detail[][id_asset_location_destination]" disabled class="form-control form-control-sm store-value detail-id_asset_location_destination-input" style="width:200px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_unit_assigned cont">
                <input type="number" name="detail[][unit_assigned]" disabled class="form-control form-control-sm store-value detail-unit_assigned-input" style="width:80px;">
                <span class="error"></span>
            </td>
            <td class="detail_effective_date cont">
                <input name="detail[][effective_date]" class="form-control form-control-sm store-value detail-effective_date-input date" style="width:100px;">
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

    let periods, assets, employees, idEmployee, branches, retirementHeaders, retirementDetails, employeeApprovals = [];
    let toModify = ['id_retirement_detail_source', 'id_reinstate_detail', 'id_employee_destination', 'unit_assigned', 'id_branch_destination', 'id_location_destination', 'id_asset_location_destination', 'effective_date', 'status'];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    $('#modalAdd').find('#status').empty().select2({
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
            url: "{{ route('assets.reinstatement.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                employees = res.data.employees;
                idEmployee = res.data.id_employee;
                branches = res.data.branches;
                assets = res.data.assets;
                periods = res.data.periods;
                retirementHeaders = res.data.retirement_headers;
                employeeApprovals = res.data.approval_hierarchy;
                if(modifyDependentElements) {
                    $('#modalAdd').find('#id_period').empty().prepend('<option></option>').select2({
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
        $('#modalEditTitle').text('Add Asset Reinstatement Request');
        $('#addRetirementDetail, .btn-submit').show();
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
        $('#modalAdd').find('#status').val('A').trigger('change');
        $('#transferJournal, #viewJournal').hide();
        $('#modalAdd').find('#description, #id_retirement_header_source, #id_period, #status').attr('readonly', false);
        $('#approvalTableBody').empty();
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

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset Reinstatement Request');
        $('#addRetirementDetail').hide();
        $.ajax({
            url: "{{ route('assets.reinstatement.get_edit') }}",
            data: {
                id_reinstate_header: $(this).attr('id-reinstate-header'),
            },
            beforeSend: () => {
                getInitData(true);
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
                $('#viewJournal').attr('journal-id', res.data.id_je_header);
                $('#transferJournal').attr('id-header', res.data.id_reinstate_header);
                if(res.data.id_je_header) {
                    $('#viewJournal').show();
                    $('#transferJournal').hide();
                } else {
                    $('#viewJournal').hide();
                    $('#transferJournal').hide();
                    if(res.data.document_status.code == 'Approved') {
                        $('#transferJournal').show();
                    }
                }
                $('#modalAdd').find('#description, #id_retirement_header_source, #id_period, #status').attr('readonly', false);
                $('.btn-submit').show();
                if(res.data.document_status.code != 'New') {
                    $('#modalAdd').find('#description, #id_retirement_header_source, #id_period, #status').attr('readonly', true);
                    $('.btn-submit').hide();
                }
                // $('#id_retirement_header_source').trigger('change');
                $('#document_status').text(res.data.document_status.description);
                if(res.data.document_status.code == 'Approved') {
                    $('#document_status').addClass('badge badge-sm badge-success');
                } else {
                    $('#document_status').removeClass('badge badge-sm badge-success');
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
                res.data.details?.forEach((detail) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_reinstate_detail-input').val(detail.id_reinstate_detail);
                    clone.find('.detail-effective_date-input').val(detail.effective_date);
                    clone.find('.detail-id_retirement_detail_source-input').val(detail.id_retirement_detail_source).trigger('change').attr('default-value', detail.id_retirement_detail_source);
                    clone.find('.detail-id_employee_destination-input').val(detail.id_employee_destination).trigger('change');
                    clone.find('.detail-unit_assigned-input').val(detail.unit_assigned).trigger('change');
                    clone.find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
                    clone.find('.detail-id_location_destination-input').val(detail.id_location_destination).trigger('change').attr('default-value', detail.id_location_destination);
                    clone.find('.detail-id_asset_location_destination-input').val(detail.id_asset_location_destination).trigger('change').attr('default-value', detail.id_asset_location_destination);;
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    if(res.data.document_status.code != 'New') {
                        clone.find('input, select').attr('disabled', true);
                    }
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                $('#assignedEmployeeTableBody > tr').each((index, element) => {
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

    $(document).on('click', '#viewJournal', function() {
        loadEditJournal($(this).attr('journal-id'), '#journalModal');
    });

    $(document).on('click', '#transferJournal', function() {
        $.ajax({
            url: "{{ route('assets.reinstatement.generate_journal') }}",
            data: {
                id_reinstate_header: $(this).attr('id-header'),
                _token: '{{ csrf_token() }}',
            },
            type: 'POST',
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#loader').addClass('hidden');
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                });
            },
            error: (err) => {
                $('#loader').addClass('hidden');
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON.message,
                });
            }
        })
    });

    $(document).on('click', '#addRetirementDetail', function() {
        if(!$('#id_retirement_header_source').val()) {
            swal({
                icon: 'error',
                text: 'Please select Retirement Source first!',
            });
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
        clone.find('.detail-id_retirement_detail_source-input').empty().prepend('<option></option>').select2({ 
            data: retirementDetails,
            allowClear: true,
            placeholder: 'Select Asset Number',
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

    $(document).on('change', '#id_retirement_header_source', function() {
        $.ajax({
            url: "{{ route('assets.reinstatement.get_data') }}",
            data: {
                id_retirement_header_source: $(this).val(),
            },
            success: (res) => {
                retirementDetails = res.data.retirement_details;
                $('#assignedEmployeeTableBody')
                    .find('select.detail-id_retirement_detail_source-input')
                    .each((_i, element) => {
                        $(element)
                            .empty()
                            .select2({
                                data: res.data.retirement_details
                            })
                            .trigger('change');
                        
                        // if($('#id_reinstate_header').val()) {
                        //     $(element).val($(element).attr('default-value')).trigger('change').next(".select2-container").hide();
                        //     $(element).parent().find('span').remove();
                        //     $(element).parent().append(`<span>${$('select.detail-id_retirement_detail_source-input').find(':selected').text()}</span>`)
                        // }
                    });
            },
        });
    });

    $(document).on('change', '.detail-id_retirement_detail_source-input', function() {
        if(!retirementDetails) return;
        retirementDetails.forEach((detail) => {
            if(detail.id == $(this).val()) {
                $(this).closest('tr').find('.detail-unit_assigned-input').val(detail.unit_assigned);
                $(this).closest('tr').find('.detail-id_asset_location_destination-input').attr('default-value', detail.id_asset_location_destination).css('width', '100%');
                $(this).closest('tr').find('.detail-id_location_destination-input').attr('default-value', detail.id_location_destination);
                $(this).closest('tr').find('.detail-id_branch_destination-input').val(detail.id_branch_destination).trigger('change');
            }
        })
    });

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
            url: "{{ route('assets.reinstatement.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: () => {
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
            url: "{{route('assets.reinstatement')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'reference_number', name: 'reference_number', title: 'Reference Number'},
            { data: 'request_date', name: 'request_date', title: 'Request Date'},
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
                        return `<a href="#" onclick="loadEditJournal(${data}, '#journalModal')">${row.journal_header.reference_number}</a>`;
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
                    return `<span class="btn btn-primary edit" id-reinstate-header="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@include('accounting.general_ledger.journal_entry.detail', ['disableFields' => true])
@endsection