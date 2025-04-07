<div class="modal fade" id="journalModal" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Journal Entry</h5>
            </div>
            <div class="modal-body">
                <form id="journalEntryForm" method="post">
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
                                    <label for="je_id_period">Period</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_period" id="je_id_period" class="form-control form-control-sm posted-disable" style="width:100%">
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
                                    <label for="je_status">Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="status" id="je_status" class="form-control form-control-sm posted-disable" style="width:100%">
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
                            @if(!isset($disableFields) || !$disableFields)
                                <button type="button" class="btn btn-sm btn-primary" id="addJournalLine"><i class="fa fa-plus"></i> Add Detail</button>
                            @endif
                        </div>
                        <table id="journalLinesTable" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
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
                            <tbody id="journalLinesTableBody"></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-sm btn-info btn-post"><i class="fas fa-paper-plane"></i> Post</span>
                <button type="button" class="btn btn-sm btn-success btn-update"><i class="fas fa-save"></i> Update</span>
                <button type="button" class="btn btn-sm btn-danger btn-cancel"><i class="fas fa-times"></i> Cancel</span> --}}
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleDetailTableJournal" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_account_number"></td>
            <td class="detail_id_account cont">
                <select name="detail[][id_account]" class="form-control form-control-sm store-value detail-id_account-input posted-disable" style="width:200px"></select>
                <span class="error"></span>
            </td>
            <td class="detail_description cont">
                <input name="detail[][description]" class="form-control form-control-sm store-value detail-description-input posted-disable" style="width:100px">
                <span class="error"></span>
            </td>
            <td class="detail_id_currency cont">
                <select name="detail[][id_currency]" class="form-control form-control-sm store-value detail-id_currency-input posted-disable" style="width:100px"></select>
                <span class="error"></span>
            </td>
            <td class="detail_entered_debit_amount cont">
                <input name="detail[][entered_debit_amount]" class="form-control form-control-sm store-value detail-entered_debit_amount-input money posted-disable" style="width:100px">
                <span class="error"></span>
            </td>
            <td class="detail_entered_credit_amount cont">
                <input name="detail[][entered_credit_amount]" class="form-control form-control-sm store-value detail-entered_credit_amount-input money posted-disable" style="width:100px">
                <span class="error"></span>
            </td>
            <td class="detail_base_currency_debit_amount cont">
                <input name="detail[][base_currency_debit_amount]" class="form-control form-control-sm store-value detail-base_currency_debit_amount-input money posted-disable" style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_base_currency_credit_amount cont">
                <input name="detail[][base_currency_credit_amount]" class="form-control form-control-sm store-value detail-base_currency_credit_amount-input money posted-disable" style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_status cont">
                <select name="detail[][status]" class="form-control form-control-sm store-value detail-status-input posted-disable"></select>
                <span class="error"></span>
            </td>
            <td class="detail_action cont"></td>
        </tr>
    </tbody>
</table>
<script>
    let je_branches, je_journalCategories, je_journalSources, je_accounts, je_currencies = [];

    let je_status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];

    $('#journalModal').find('#je_status').empty().select2({
        data: je_status,
    });

    function getInitDataJournal(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('accounting.gl_je.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#journalModal').find('input:checkbox').trigger('change');
                je_branches = res.data.branches;
                je_journalCategories = res.data.journal_categories;
                je_journalSources = res.data.journal_sources;
                je_accounts = res.data.accounts;
                je_currencies = res.data.currencies;
                if(modifyDependentElements) {
                    $('#journalModal').find('#je_id_period').empty().prepend('<option></option>').select2({
                        data: res.data.periods,
                        allowClear: true,
                        placeholder: 'Select Period'
                    }).trigger('change');
                    $('#journalModal').find('#id_je_source').empty().prepend('<option></option>').select2({
                        data: res.data.journal_sources,
                        allowClear: true,
                        placeholder: 'Select Journal Source'
                    }).trigger('change');
                    $('#journalModal').find('#id_je_category').empty().prepend('<option></option>').select2({
                        data: res.data.journal_categories,
                        allowClear: true,
                        placeholder: 'Select Journal Categories'
                    }).trigger('change');
                    $('#journalModal').find('#id_currency').empty().prepend('<option></option>').select2({
                        data: res.data.currencies,
                        allowClear: true,
                        placeholder: 'Select Currency'
                    }).trigger('change');
                    $('#journalModal').find('#id_reverse_je_header').empty().prepend('<option></option>').select2({
                        data: res.data.journal_entry_headers,
                        allowClear: true,
                        placeholder: 'Select Reverse Reference'
                    }).trigger('change');
                    $('#journalModal').find('select').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    });
                    $('#journalModal').find('.date').daterangepicker({
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
            },
            error: handleError
        });
    }

    getInitDataJournal(true);

    function cloneDetailRowJournal(append = false, appendTarget = null) {
        let clone = $('#sampleDetailTableJournal').find('tr').clone();
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
            data: je_accounts,
            allowClear: true,
            placeholder: 'Select Asset',
        });
        clone.find('.detail-id_currency-input').empty().prepend('<option></option>').select2({ 
            data: je_currencies,
            allowClear: true,
            placeholder: 'Select Currency',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    function loadEditJournal(idJeHeader, modalSelector = null) {
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

                Object.keys(res.data).forEach((key) => {
                    $('#journalModal').find(`#${key}, #je_${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                // $('#journalModal').find('select').trigger('change');
                $('#journalModal').find('.document_status').text(res.data.document_status);
                res.data.lines?.forEach((detail, i) => {
                    let clone = cloneDetailRowJournal(false);
                    je_accounts.forEach((account) => {
                        if(account.id == detail.id_account) {
                            clone.find('.detail_account_number').text(account.account_number);
                        }
                    });
                    clone.find('.detail-id_transfer_detail-input').val(detail.id_asset)
                    clone.find('.detail-base_currency_credit_amount-input').val(detail.base_currency_credit_amount);
                    clone.find('.detail-id_account-input').val(detail.id_account).trigger('change');
                    clone.find('.detail-entered_credit_amount-input').val(detail.entered_credit_amount).trigger('change');
                    clone.find('.detail-base_currency_debit_amount-input').val(detail.base_currency_debit_amount).trigger('change');
                    clone.find('.detail-description-input').val(detail.description);
                    clone.find('.detail-id_currency-input').val(res.data.id_currency).trigger('change').attr('default-value', res.data.id_currency);
                    clone.find('.detail-entered_debit_amount-input').val(detail.entered_debit_amount);
                    clone.find('.detail-status-input').empty().select2({ data: status }).val(detail.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#journalLinesTableBody');
                });
                $('#journalLinesTableBody > tr').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                    $(element).find('input, select').each((_i, inputField) => {
                        $(inputField).attr('name', $(inputField).attr('name').replaceAll('[]', `[${index}]`));
                    })
                })
                $('#journalModal').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })

                if(res.data.document_status == 'Posted' || res.data.document_status == 'Cancel') {
                    $('.btn-post, .btn-update, .btn-cancel').hide();
                    $('#journalModal').find('.posted-disable').prop('disabled', true);
                } else {
                    $('.btn-post, .btn-update, .btn-cancel').show();
                    $('#journalModal').find('.posted-disable').prop('disabled', false);
                }

                $('#journalModal').find('select, .pk, .money').trigger('change');

                @if(isset($disableFields) && $disableFields)
                    $('#journalModal').find('select, input').attr('disabled', true);
                @endif

                if(modalSelector) {
                    $(modalSelector).modal('show');
                }
            },
            error: handleError
        })
    }

    function showAccountNumber(element) {
        if(!$(element).val()) {
            $(element).closest('tr').find('.detail_account_number').text('');
            return;
        }
        je_accounts.forEach((account) => {
            if(account.id == $(this).val()) {
                $(this).closest('tr').find('.detail_account_number').text(account.account_number);
            }
        });
    }
</script>