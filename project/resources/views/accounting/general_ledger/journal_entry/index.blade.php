@extends('adminlte::page')
@section('title', 'Journal Entry')

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
                <h5 class="card-title">Journal Entry
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
<div class="modal fade" id="modalAdd" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Asset Transfer Request</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_je_header" id="id_je_header" class="pk">
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
                                    <input type="text" name="description" id="description" class="form-control form-control-sm store-change posted-disable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_period">Period</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_period" id="id_period" class="form-control form-control-sm posted-disable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="accounting_date">Accounting Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="accounting_date" id="accounting_date" class="form-control form-control-sm store-change date posted-disable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="posted_date">Posted Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="posted_date" id="posted_date" class="form-control form-control-sm store-change date" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="reverse_flag">Reverse Flag</label>
                                </div>
                                <div class="col cont">
                                    <input type="checkbox" name="reverse_flag" id="reverse_flag" class="form-control form-control-sm store-change" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_reverse_je_header">Reverse Reference</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_reverse_je_header" id="id_reverse_je_header" class="form-control form-control-sm" style="width:100%" disabled>
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="document_status">Document Status</label>
                                </div>
                                <div class="col cont document_status">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_je_source">Journal Source</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_je_source" id="id_je_source" class="form-control form-control-sm posted-disable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_je_category">Journal Category</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_je_category" id="id_je_category" class="form-control form-control-sm posted-disable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_currency">Currency</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_currency" id="id_currency" class="form-control form-control-sm posted-disable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="currency_rate">Currency Rate</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="currency_rate" id="currency_rate" class="form-control form-control-sm store-change money posted-disable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="total_debit_amount">Total Debit Amount</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="total_debit_amount" id="total_debit_amount" class="form-control form-control-sm store-change money" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="total_credit_amount">Total Credit Amount</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="total_credit_amount" id="total_credit_amount" class="form-control form-control-sm store-change money" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="total_base_currency_debit_amount">Total Base Curr. Debit Amount</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="total_base_currency_debit_amount" id="total_base_currency_debit_amount" class="form-control form-control-sm store-change money" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="total_base_currency_credit_amount">Total Base Curr. Credit Amount</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="total_base_currency_credit_amount" id="total_base_currency_credit_amount" class="form-control form-control-sm store-change money" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="status">Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="status" id="status" class="form-control form-control-sm posted-disable" style="width:100%">
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
                            <button type="button" class="btn btn-sm btn-primary" id="addTransferDetail"><i class="fa fa-plus"></i> Add Detail</button>
                        </div>
                        <table id="assignedEmployeeTable" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Sequence</th>
                                    <th>Account Number</th>
                                    <th style="width:150px;">Account Name</th>
                                    <th style="width:150px;">Description</th>
                                    <th>Currency</th>
                                    <th style="width:150px;">Debit</th>
                                    <th style="width:150px;">Credit</th>
                                    <th style="width:150px;">Currency Debit</th>
                                    <th style="width:150px;">Currency Credit</th>
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
                {{-- <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-save"></i> <span id="saveLabel">Save</span></button>&nbsp; --}}
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-info btn-post"><i class="fas fa-paper-plane"></i> Post</span>
                <button type="button" class="btn btn-sm btn-success btn-update"><i class="fas fa-save"></i> Update</span>
                <button type="button" class="btn btn-sm btn-danger btn-cancel"><i class="fas fa-times"></i> Cancel</span>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_sequence">
                <input type="number" name="detail[][sequence]" class="form-control form-control-sm detail-sequence-input">
            </td>
            <td class="detail_account_number"></td>
            <td class="detail_id_account cont">
                <input type="hidden" name="detail[][id_je_lines]" class="detail-id_je_lines-input">
                <select name="detail[][id_account]" class="form-control form-control-sm store-value detail-id_account-input" style="width:200px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_description cont">
                <input name="detail[][description]" class="form-control form-control-sm store-value detail-description-input" style="width:150px;">
                <span class="error"></span>
            </td>
            <td class="detail_id_currency cont">
                <select name="detail[][id_currency]" class="form-control form-control-sm store-value detail-id_currency-input" disabled></select>
                <span class="error"></span>
            </td>
            <td class="detail_entered_debit_amount cont">
                <input name="detail[][entered_debit_amount]" class="form-control form-control-sm store-value detail-entered_debit_amount-input money" style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_entered_credit_amount cont">
                <input name="detail[][entered_credit_amount]" class="form-control form-control-sm store-value detail-entered_credit_amount-input money" style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_base_currency_debit_amount cont">
                <input name="detail[][base_currency_debit_amount]" class="form-control form-control-sm store-value detail-base_currency_debit_amount-input money" disabled style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_base_currency_credit_amount cont">
                <input name="detail[][base_currency_credit_amount]" class="form-control form-control-sm store-value detail-base_currency_credit_amount-input money" disabled style="width:100px;">
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

    let journalCategories, accounts, journalSources, currencies, assets, employees, idEmployee, branches = [];
    let toModify = ['id_asset', 'id_transfer_detail', 'entered_credit_amount', 'base_currency_debit_amount', 'id_branch_destination', 'id_currency', 'entered_debit_amount', 'base_currency_credit_amount', 'status'];
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

    const urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null){
            return null;
        } else {
            return results[1] || 0;
        }
    }

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('accounting.gl_je.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                branches = res.data.branches;
                journalCategories = res.data.journal_categories;
                journalSources = res.data.journal_sources;
                accounts = res.data.accounts;
                currencies = res.data.currencies;
                if(modifyDependentElements) {
                    $('#id_period').empty().prepend('<option></option>').select2({
                        data: res.data.periods,
                        allowClear: true,
                        placeholder: 'Select Period'
                    }).trigger('change');
                    $('#id_je_source').empty().prepend('<option></option>').select2({
                        data: res.data.journal_sources,
                        allowClear: true,
                        placeholder: 'Select Journal Source'
                    }).trigger('change');
                    $('#id_je_category').empty().prepend('<option></option>').select2({
                        data: res.data.journal_categories,
                        allowClear: true,
                        placeholder: 'Select Journal Categories'
                    }).trigger('change');
                    $('#id_currency').empty().prepend('<option></option>').select2({
                        data: res.data.currencies,
                        allowClear: true,
                        placeholder: 'Select Currency'
                    }).trigger('change');
                    $('#id_reverse_je_header').empty().prepend('<option></option>').select2({
                        data: res.data.journal_entry_headers,
                        allowClear: true,
                        placeholder: 'Select Reverse Reference'
                    }).trigger('change');
                    $('#modalAdd').find('select').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    });
                    $('#modalAdd').find('.date').daterangepicker({
                        singleDatePicker: true,
                        autoApply: false,
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
                
                if(urlParam('id')) {
                    loadEdit(urlParam('id'), '#modalAdd');
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

    function cloneDetailRow(append = false, appendTarget = null) {
        let clone = $('#sampleDetailTable').find('tr').clone();
        clone.find('.date').daterangepicker({
            singleDatePicker: true,
            autoApply: false,
            // autoUpdateInput: false,
            locale: {
                format: 'YYYY-MM-DD'
            }
        }).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
        clone.find('.detail-id_account-input').empty().prepend('<option></option>').select2({ 
            data: accounts,
            allowClear: true,
            placeholder: 'Select Asset',
        });
        clone.find('.detail-id_currency-input').empty().prepend('<option></option>').select2({ 
            data: currencies,
            allowClear: true,
            placeholder: 'Select Currency',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    function loadEdit(idJeHeader, modalSelector = null) {
        $('#modalEditTitle').text('Edit Journal Entry');
        $('#addTransferDetail').hide();
        $.ajax({
            url: "{{ route('accounting.gl_je.get_edit') }}",
            data: {
                id_je_header: idJeHeader,
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
                $('.btn-post, .btn-cancel, .btn-update').attr('id-je-header', res.data.id_je_header);
                if(res.data.document_status == 'Posted' || res.data.document_status == 'Cancel') {
                    $('.btn-post, .btn-update, .btn-cancel').hide();
                    $('#modalAdd').find('.posted-disable').prop('disabled', true);
                } else {
                    $('.btn-post, .btn-update, .btn-cancel').show();
                    $('#modalAdd').find('.posted-disable').prop('disabled', false);
                }

                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                // $('#modalAdd').find('select').trigger('change');
                $('.document_status').text(res.data.document_status);
                res.data.lines?.forEach((detail, i) => {
                    let clone = cloneDetailRow(false);
                    accounts.forEach((account) => {
                        if(account.id == detail.id_account) {
                            clone.find('.detail_account_number').text(account.account_number);
                        }
                    });
                    clone.find('.detail-id_je_lines-input').val(detail.id_je_lines)
                    clone.find('.detail-sequence-input').val(detail.sequence)
                    clone.find('.detail-id_transfer_detail-input').val(detail.id_asset)
                    clone.find('.detail-base_currency_credit_amount-input').val(detail.base_currency_credit_amount);
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-entered_credit_amount-input').val(detail.entered_credit_amount).trigger('change');
                    clone.find('.detail-base_currency_debit_amount-input').val(detail.base_currency_debit_amount).trigger('change');
                    clone.find('.detail-description-input').val(detail.description);
                    clone.find('.detail-id_currency-input').val(res.data.id_currency).trigger('change').attr('default-value', res.data.id_currency);
                    clone.find('.detail-entered_debit_amount-input').val(detail.entered_debit_amount);
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    if(res.data.document_status != 'Draft') {
                        clone.find('input, select').attr('readonly', true);
                    } else {
                        clone.find('input, select').attr('readonly', false);
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

                if(modalSelector) {
                    $(modalSelector).modal('show');
                }
            },
            error: handleError
        })
    }

    $(document).on('click', '.edit', function() {
        loadEdit($(this).attr('id-je-header'))
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

    $(document).on('change', '.store-change', function() {
        $(this).attr('default-value', $(this).val());
    });

    $(document).on('change', '.detail-id_account-input', function() {
        if(!$(this).val()) {
            $(this).closest('tr').find('.detail_account_number').text('');
            return;
        }
        accounts.forEach((account) => {
            if(account.id == $(this).val()) {
                $(this).closest('tr').find('.detail_account_number').text(account.account_number);
            }
        });
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

    $(document).on('click', '.btn-update', function() {
        $.ajax({
            url: "{{ route('accounting.gl_je.update') }}",
            type: "POST",
            data: $('#assetGroupForm').serialize(),
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#loader').addClass('hidden');
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                });
                $('#modalAdd').modal('hide');
            },
            error: (err) => {
                $('#loader').addClass('hidden');
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON?.message ? err.responseJSON.message : 'An unknown error occured.',
                });
            }
        })
    });

    $(document).on('click', '.btn-post', function() {
        $.ajax({
            url: "{{ route('accounting.gl_je.post') }}",
            type: "POST",
            data: $('#assetGroupForm').serialize(),
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#loader').addClass('hidden');
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                });
                $('#modalAdd').modal('hide');
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

    $(document).on('click', '.btn-cancel', function() {
        $.ajax({
            url: "{{ route('accounting.gl_je.cancel') }}",
            type: "POST",
            data: $('#assetGroupForm').serialize(),
            success: (res) => {
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message,
                });
                $('#modalAdd').modal('hide');
            },
            error: (err) => {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON.message,
                });
            }
        })
    });

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('accounting.gl_je')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { 
                data: 'id_je_source', 
                name: 'source', 
                title: 'Source',
                render: (data, type, row) => row.source?.source_name ? row.source?.source_name : '-',
            },
            { data: 'reference_number', name: 'reference_number', title: 'Reference Number'},
            { data: 'transaction_reference_number', name: 'transaction_reference_number', title: 'Transaction Ref. No.'},
            { 
                data: 'category_name', 
                name: 'category', 
                title: 'Category',
                // render: (data, type, row) => row.category?.category_name ? row.category?.category_name : '-',
            },
            { 
                data: 'period_name', 
                name: 'period', 
                title: 'Period',
                // render: (data, type, row) => row.period?.description ? row.period?.description : '-',
            },
            { 
                data: 'total_debit_amount', 
                name: 'total_debit_amount', 
                title: 'Total Debit Amount',
                render: (data) => Intl.NumberFormat('id-ID').format(data),
            },
            { 
                data: 'total_credit_amount', 
                name: 'total_credit_amount', 
                title: 'Total Credit Amount',
                render: (data) => Intl.NumberFormat('id-ID').format(data),
            },
            { data: 'description', name: 'note', title: 'Description'},
            { 
                data: 'document_status', 
                name: 'document_status', 
                title: 'Document Status', 
            },
            { data: 'status', name: 'status', title: 'Status'},
            { 
                data: 'id_je_header', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    let button = '';
                    if(row.document_status == 'Draft') {
                        button += `<span class="btn btn-sm btn-primary edit" id-je-header="${data}"><i class="fas fa-edit" style="color:#fff;"></i></span>`;
                    } else {
                        button += `<span class="btn btn-sm btn-warning edit" id-je-header="${data}"><i class="fas fa-eye" style="color:#fff;"></i></span>`;
                    }
                    return button;
                }
            }
        ]
    });
</script>
@endsection