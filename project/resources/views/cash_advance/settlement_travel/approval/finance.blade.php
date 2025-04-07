@extends('adminlte::page')
@section('title', 'Settlement Travel Approval Finance')

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
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Settlement Travel Approval {{ @$fas ? "FAS" : "Finance" }}
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    @if($page == "settlement_clearing")
                    <select name="status" id="status" class="form-control form-control-sm" style="width:125px;">
                    </select>
                    <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button>
                    @elseif($page == "expense_payment_finance")
                    <div class="row mr-2">
                        <input id="filter_date" class="form-control form-control-sm col" style="width:225px" value="{{ $startDate }}">
                        <div class="mx-1 col">
                            <select name="is_paid_by_finance" id="is_paid_by_finance" class="form-control form-control-sm" style="width:100px;">
                            </select>
                        </div>
                        <button type="button" class="new btn btn-sm btn-success col" onclick="$('#is_paid_by_finance').trigger('change')"><i class="fas fa-filter"></i> Filter</button>
                    </div>
                    @endif
                    
                    {{-- <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Official Travel</button> --}}
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="settlementtravel_table" style="width: 100%;">
                    <thead>
                        <tr>
                        <!-- <th></th> -->
                        <th></th>
                        <th data-priority="4">No.</th>
                        <th data-priority="2">Reference Number</th>
                        <th data-priority="3">Transaction Date</th>
                        <th>Request By</th>
                        <th>Transaction Type</th>
                        <th>Notes</th>
                        <th>Total Cash Request</th>
                        <th>Total Settlement</th>
                        <th>Refund Date</th>
                        <th>Refund Amount</th>
                        <th>Total Payment</th>
                        <th>Submit</th>
                        <th>Paid by HR</th>
                        <th>Approved by Chief</th>
                        <th>Approved by Finance</th>
                        <th>Settlement Status</th>
                        <th>Payment Status</th>
                        <th data-priority="1" align="center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="attachment-settlement-modal" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1675;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attachment</h5>
            </div>
            <div class="modal-body">
                <embed height="700" width="1000" type="application/pdf" src="#" id="attachment-settlement-pdf">
                    {{-- <p>PDF cannot be displayed.</p> --}}
                </embed>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_apply_payment" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Apply Payment</h5>
            </div>
            <div class="modal-body">
                <form method="POST" id="applyPaymentForm" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_cash_payment" id="apply_payment_id_cash_payment">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Available Amount</label>
                                <div class="col-sm-8">
                                    <span id="payment_settlement_available_amount"></span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Settlement</label>
                                <div class="col-sm-8">
                                    <select id="payment_settlement" multiple name="payment_settlement" class="form-control form-control-sm payment_settlement" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="payment_settlementError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Total to be Applied</label>
                                <div class="col-sm-8">
                                    <span id="payment_settlement_total_applied"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-info btn-submit-apply-payment" id="submitApplyPayment" name="submitApplyPayment" value="submit"><i class="fas fa-paper-plane"></i> Apply</button>&nbsp;
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_settlement" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1500;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width:100%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Declaration Form</h5>
            </div>
            <div class="modal-body">
                {{-- Request --}}
                <table class="hidden">
                    <tbody>
                        <tr id="table-request-sample">
                            <td class="request-no"></td>
                            <td>
                                <input type="text" name="request[0][product]" class="request-product form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][description]" class="request-description form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][notes]" class="request-notes form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][qty]" class="request-qty form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][price]" class="request-price form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][total]" class="request-total form-control form-control-sm" readonly>
                            </td>
                            <td class="request-action"></td>
                        </tr>
                        <tr id="table-settlement-sample">
                            <td class="settlement-no"></td>
                            <td>
                                <input name="settlement[0][start_end]" class="settlement-start-end form-control form-control-sm" style="width:125px;">
                                <span class="invalid-feedback start-end-error" role="alert" id="settlement_0_start_endError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="hidden" name="settlement[0][id_settlement]" class="id-settlement">
                                <select name="settlement[0][product]" class="settlement-product form-control form-control-sm">
                                    <option selected></option>
                                <select>
                                <span class="invalid-feedback product-error" role="alert" id="settlement_0_productError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][region]" class="settlement-region form-control form-control-sm" style="width:100%">
                                    {{-- <option selected></option> --}}
                                <select>
                                <span class="invalid-feedback region-error" role="alert" id="settlement_0_regionError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][branch]" class="settlement-branch form-control form-control-sm" style="width:100%" readonly>
                                    {{-- <option selected></option> --}}
                                <select>
                                <span class="invalid-feedback branch-error" role="alert" id="settlement_0_branchError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-description"></p>
                                {{-- <input type="text" name="settlement[0][description]" class="settlement-description form-control form-control-sm" style="text-transform:uppercase;width:150px;"> --}}
                                <span class="invalid-feedback description-error" role="alert" id="settlement_0_descriptionError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-qty"></p>
                                {{-- <input type="text" name="settlement[0][qty]" class="settlement-qty form-control form-control-sm" style="width:70px;"> --}}
                                <span class="invalid-feedback qty-error" role="alert" id="settlement_0_qtyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][unit]" class="settlement-unit form-control form-control-sm" style="width:100%;">
                                </select>
                                <span class="invalid-feedback unit-error" role="alert" id="settlement_0_unitError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][currency]" class="settlement-currency form-control form-control-sm" style="width:170px;">
                                </select>
                                <span class="invalid-feedback currency-error" role="alert" id="settlement_0_currencyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-currency-rate" style="width:100px"></p>
                                {{-- <input name="settlement[0][currency_rate]" class="settlement-currency-rate form-control form-control-sm" style="width:100px;" readonly> --}}
                                <span class="invalid-feedback currency-rate-error" role="alert" id="settlement_0_currency_rateError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-price" style="width:100px"></p>
                                {{-- <input type="text" name="settlement[0][price]" class="settlement-price form-control form-control-sm" style="width:100px"> --}}
                                <span class="invalid-feedback price-error" role="alert" id="settlement_0_priceError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-total" style="width:100px"></p>
                                {{-- <input type="text" name="settlement[0][total]" class="settlement-total form-control form-control-sm" style="width:100px" disabled> --}}
                                <span class="invalid-feedback total-error" role="alert" id="settlement_0_totalError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-total-base-currency-amount" style="width:100px"></p>
                                {{-- <input type="text" name="settlement[0][total]" class="settlement-total form-control form-control-sm" style="width:100px" disabled> --}}
                                <span class="invalid-feedback settlement-total-base-currency-amount-error" role="alert" id="settlement_0_total_base_currency_amountError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="">
                                <span class="settlement-attachment-download" style="display:none;">
                                    <a href="#" target="_blank"></a>
                                </span>
                                <span class="invalid-feedback attachment-error" role="alert" id="settlement_0_attachmentError">
                                    <strong></strong>
                                </span>
                            </td>
                            {{-- <td>
                                <p class="settlement-budget"></p>
                                <span class="invalid-feedback budget-error" role="alert" id="settlement_0_budgetError">
                                    <strong></strong>
                                </span>
                            </td> --}}
                            <td>
                                <input type="text" name="settlement[0][chief_approval_price]" class="settlement-chief-approval-price form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback chief-approval-price-error" role="alert" id="settlement_0_chief_approval_priceError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][chief_total_approval]" class="settlement-chief-total-approval form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback chief-total-approval-error" role="alert" id="settlement_0_chief_total_approvalError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][approval_price]" class="settlement-approval-price form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback approval-price-error" role="alert" id="settlement_0_approval_priceError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][total_approval]" class="settlement-total-approval form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback total-approval-error" role="alert" id="settlement_0_total_approvalError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][notes]" class="settlement-notes form-control form-control-sm" style="width:150px" readonly>
                                <span class="invalid-feedback notes-error" role="alert" id="settlement_0_notesError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="settlement-action"></td>
                        </tr>
                        <tr id="table-settlement-summary-sample">
                            <td>
                                <span class="summary-no"></span>
                            </td>
                            <td>
                                <span class="summary-date"></span>
                            </td>
                            <td>
                                <span class="summary-description"></span>
                            </td>
                            <td>
                                <span class="summary-budget"></span>
                            </td>
                            <td>
                                <span class="summary-total-expense"></span>
                            </td>
                            <td>
                                <span class="summary-chief-approval"></span>
                            </td>
                            <td>
                                <span class="summary-finance-approval"></span>
                            </td>
                            <td>
                                <span class="summary-final-approval"></span>
                            </td>
                        </tr>
                        <tr id="table-refund-sample">
                            <td class="refund-no"></td>
                            <td>
                                <input type="hidden" name="refund[0][id_refund]" class="id-refund">
                                <select name="refund[0][bank_from]" class="form-control form-control-sm refund-bank-from">
                                </select>
                            </td>
                            <td>
                                <input name="refund[0][bank_account]" class="form-control form-control-sm refund-bank-account">
                            </td>
                            <td>
                                <select name="refund[0][bank_to]" class="form-control form-control-sm refund-bank-to" style="width:200px;">
                                </select>
                            </td>
                            <td>
                                <input name="refund[0][amount]" class="form-control form-control-sm refund-amount">
                            </td>
                            <td>
                                <span class="refund-download" style="display:hidden;">
                                    <a href="#" target="_blank"></a>
                                </span>
                            </td>
                            <td class="refund-action"></td>
                        </tr>
                        <tr id="table-payment-sample">
                            <td class="payment-no"></td>
                            <td>
                                <input type="hidden" name="payment[0][id_payment]" class="id-payment">
                                <input readonly name="payment[0][cash_advance_ref]" class="form-control form-control-sm payment-cash-advance-ref" style="width:175px;">
                            </td>
                            <td>
                                <select name="payment[0][bank_from_account]" class="form-control form-control-sm payment-bank-from-account" style="width:175px;">
                                </select>
                                <span class="invalid-feedback" role="alert" id="payment_0x_bank_from_accountError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="hidden" name="payment[0][id_employee_bank_to]" class="payment-id-employee-bank-to">
                                <span class="payment-bank-to-account">
                                </span>
                                <span class="invalid-feedback" role="alert" id="payment_0x_id_employee_bank_toError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="payment[0][notes]" class="form-control form-control-sm payment-notes" style="width:175px">
                                <span class="invalid-feedback" role="alert" id="payment_0x_notesError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="payment[0][currency]" class="form-control form-control-sm payment-currency" style="width:100%">
                                </select>
                                <span class="invalid-feedback" role="alert" id="payment_0x_currencyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="payment[0][currency_rate]" class="form-control form-control-sm payment-currency-rate" style="width:100px;">
                                <span class="invalid-feedback" role="alert" id="payment_0x_currency_rateError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="payment[0][total_amount]" class="form-control form-control-sm payment-total-amount" style="width:100px">
                                <span class="invalid-feedback" role="alert" id="payment_0x_total_amountError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="payment[0][total_applied_amount]" class="form-control form-control-sm payment-total-applied-amount" style="width:100px">
                            </td>
                            <td>
                                <input type="file" name="payment[0][attachment]" class="form-control-file form-control-sm payment-attachment" style="width:120px">
                                <span class="payment-download" style="display:hidden;">
                                    <a href="#" target="_blank"></a>
                                </span>
                            </td>
                            <td>
                                <select name="payment[0][payment_status]" class="form-control form-control-sm payment-status">
                                </select>
                            </td>
                            <td class="payment-action"></td>
                        </tr>
                    </tbody>
                </table>
                <form method="POST" id="travelForm" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Cash Advance</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Cash Advance</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" hidden="" id="id_cash_advance" name="id_cash_advance">
                                        <input disabled id="cash_advance_reference" name="cash_advance_reference" class="form-control form-control-sm reference-number" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="cash_advance_referenceError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Official Travel</label>
                                    <div class="col-sm-8">
                                        <input type="hidden" hidden="" id="id_official_travel" name="id_official_travel">
                                        <input disabled id="reference_number" name="reference_number" class="form-control form-control-sm reference-number" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="reference_numberError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Transaction Date</label>
                                    <div class="col-sm-8">
                                        <input disabled id="transaction_date" name="transaction_date" class="form-control form-control-sm letter-date" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="letter_dateError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Department</label>
                                    <div class="col-sm-8">
                                        <select disabled id="department" name="department" class="form-control form-control-sm department" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="departmentError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Position Detail</label>
                                    <div class="col-sm-8">
                                        <select disabled id="position_detail" name="position_detail" class="form-control form-control-sm position_detail" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="position_detailError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Request By</label>
                                    <div class="col-sm-8">
                                        <select disabled id="request_by" name="request_by" class="form-control form-control-sm request_by" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="request_byError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Notes</label>
                                    <div class="col-sm-8">
                                        <textarea disabled class="form-control form-control-sm cash-advance-notes" id="cash_advance_notes" rows="4" name="vash_advance_notes" style="text-transform: uppercase;"></textarea>
                                        <span class="invalid-feedback" role="alert" id="cash_advance_notesError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Expense Request</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Request Currency</label>
                                    <div class="col-sm-8">
                                        <select disabled id="request_currency" name="request_currency" class="form-control form-control-sm request_currency" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="request_currencyError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Request Total Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="request_total_amount" name="request_total_amount" class="form-control form-control-sm request_total_amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="request_total_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Total Cash Request Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="total_cash_request_amount" name="total_cash_request_amount" class="form-control form-control-sm total-cash-request-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="total_cash_request_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Total Travel Request Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="total_travel_request_amount" name="total_travel_request_amount" class="form-control form-control-sm total-travel-request-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="total_travel_request_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Approval</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Approval Hierarchy</label>
                                    <div class="col-sm-8">
                                        <select disabled id="approval_hierarchy" name="approval_hierarchy" class="form-control form-control-sm approval-hierarchy" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="approval_hierarchyError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Approval By</label>
                                    <div class="col-sm-8">
                                        <select disabled id="approval_by" name="approval_by" class="form-control form-control-sm approval_by" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="approval_byError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Approval Status</label>
                                    <div class="col-sm-8">
                                        <div id="approval_status_container"></div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="col-md-6">
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Payment</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Payment Currency</label>
                                    <div class="col-sm-8">
                                        <select disabled id="payment_currency" name="payment_currency" class="form-control form-control-sm payment-currency" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="payment_currencyError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Payment Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="payment_amount" name="payment_amount" class="form-control form-control-sm payment-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="payment_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Payment Status</label>
                                    <div class="col-sm-8">
                                        <select disabled id="payment_status" name="payment_status" class="form-control form-control-sm payment-status" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="payment_statusError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Settlement</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label" id="label-currency-settlement">Currency Settlement</label>
                                    <div class="col-sm-8">
                                        <select readonly id="currency_settlement" name="currency_settlement" class="form-control form-control-sm currency-settlement" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="currency_settlementError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label" id="label-total-settlement">Total Settlement</label>
                                    <div class="col-sm-8">
                                        <input disabled id="total_settlement" name="total_settlement" class="form-control form-control-sm total-settlement" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="total_settlementError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Difference Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="difference_amount" name="difference_amount" class="form-control form-control-sm difference-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="difference_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Max Settlement Date</label>
                                    <div class="col-sm-8">
                                        <input id="maximum_clearing_date" name="maximum_clearing_date" class="form-control form-control-sm payment-date" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="maximum_clearing_dateError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label" id="label-settlement-status">Settlement Status</label>
                                    <div class="col-sm-8">
                                        <select disabled id="settlement_status" name="settlement_status" class="form-control form-control-sm settlement-status" style="width: 100%;">
                                        </select>
                                        <span class="invalid-feedback" role="alert" id="settlement_statusError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Validation Status</label>
                                    <div class="col-sm-8">
                                        <div id="validation_status_container"></div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Tax</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Total Base Currency Tax</label>
                                    <div class="col-sm-8">
                                        <input disabled id="total_base_currency_tax_amount" name="total_base_currency_tax_amount" class="form-control form-control-sm total_base_currency_tax_amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="total_base_currency_tax_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Total Taxed Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="total_taxed_amount" name="total_taxed_amount" class="form-control form-control-sm total-taxed-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="total_taxed_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="border rounded px-2 pb-2">
                                <legend class="w-auto">Clearing</legend>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Accounting Date</label>
                                    <div class="col-sm-8">
                                        <input id="accounting_date" name="accounting_date" class="form-control form-control-sm accounting-date" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="accounting_dateError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Clearing Amount</label>
                                    <div class="col-sm-8">
                                        <input disabled id="clearing_amount" name="clearing_amount" class="form-control form-control-sm clearing-amount" style="width: 100%;">
                                        <span class="invalid-feedback" role="alert" id="clearing_amountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Clearing Account</label>
                                    <div class="col-sm-8">
                                        <select name="id_clearing_account" id="id_clearing_account"  class="form-control form-control-sm id-clearing-account" readonly style="width: 100%;"></select>
                                        <span class="invalid-feedback" role="alert" id="id_clearing_accountError">
                                            <strong></strong>
                                        </span>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Attachment</label>
                                    <div class="col-sm-8">
                                        <input type="file" name="attachment_settlement" class="attachment-settlement form-control-file" style="width:180px;">
                                        <span class="invalid-feedback" role="alert" id="attachment_settlementError">
                                            <strong></strong>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-primary" id="view-attachment-settlement" style="display:none;">View</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-4 col-form-label">Billing Letter</label>
                                    <div class="col-sm-8">
                                        <div id="billing_letter_container"></div>
                                    </div>
                                </div>
                                <div class="row" style="display:none;" id="manual-clearing">
                                    <label class="col-sm-4 col-form-label">Manual Clearing</label>
                                    <div class="col-sm-8">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="manualClearing()">Mark as Clear</button>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <div class="row mt-5" id="rows_table_cash_advance">
                        <div class="col-xl-12" id="tab_detail">
                          <div class="nav nav-tabs justify-content-left mb-4">
                            <a class="nav-item nav-link active" id="tab-request" data-toggle="tab" href="#tab-pane-1">Request
                                <span class="error-tab text-red hidden">Error</span>
                            </a>
                            <a class="nav-item nav-link" id="tab-settlement" data-toggle="tab" href="#tab-pane-2">Settlement
                                <span class="error-tab text-red hidden">Error</span>
                            </a>
                            <a class="nav-item nav-link" id="tab-refund" data-toggle="tab" href="#tab-pane-3">Refund
                                <span class="error-tab text-red hidden">Error</span>
                            </a>
                            <a class="nav-item nav-link" id="tab-payment" data-toggle="tab" href="#tab-pane-4">Payment
                                <span class="error-tab text-red hidden">Error</span>
                            </a>
                          </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab-pane-1">
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Category</th>
                                                    <th>Description</th>
                                                    <th>Notes</th>
                                                    <th style="width:50px;">Qty</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_request">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-pane-2">
                                    {{-- Settlement --}}
                                    <input type="hidden" id="id_settlement_del" hidden name="id_settlement_del">
                                    <h4 class="mx-2">Settlement</h4>
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Region</th>
                                                    <th>Branch</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    <th style="width:120px;">Currency</th>
                                                    <th>Currency Rate</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Total Base Currency Amount</th>
                                                    <th>Attachment</th>
                                                    {{-- <th>Budget</th> --}}
                                                    <th>Chief Approval Price</th>
                                                    <th>Chief Total Approval</th>
                                                    <th>Finance Approval Price</th>
                                                    <th>Total Approval</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_settlement">
                                            </tbody>
                                        </table>
                                    </div>
                                    <h4 class="mx-2 mt-3 transaco-table">Transport & Accommodation</h4>
                                    <div class="table-responsive col-md-12 transaco-table"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Date</th>
                                                    <th>Category</th>
                                                    <th>Region</th>
                                                    <th>Branch</th>
                                                    <th>Description</th>
                                                    <th>Qty</th>
                                                    <th>Unit</th>
                                                    <th style="width:120px;">Currency</th>
                                                    <th>Currency Rate</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Total Base Currency Amount</th>
                                                    <th>Attachment</th>
                                                    <th>Chief Approval Price</th>
                                                    <th>Chief Total Approval</th>
                                                    <th>Finance Approval Price</th>
                                                    <th>Total Approval</th>
                                                    <th>Notes</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_transport_accommodation">
                                            </tbody>
                                        </table>
                                    </div>
                                    <h4 class="mx-2 mt-3 settlement-summary-table">Settlement Summary</h4>
                                    <div class="table-responsive col-md-12 settlement-summary-table"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Settlement Date</th>
                                                    <th>Category</th>
                                                    <th>Budget</th>
                                                    <th>Total Settlement Amount</th>
                                                    <th>Chief Approval Price</th>
                                                    <th>Finance Approval Price</th>
                                                    <th>Final Approval</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_settlement_summary">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-pane-3">
                                    {{-- Refund --}}
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Bank From</th>
                                                    <th>Bank Account</th>
                                                    <th>Bank To</th>
                                                    <th>Amount</th>
                                                    <th>Attachment</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_refund">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab-pane-4">
                                    {{-- Payment --}}
                                    <button type="button" role="button" class="btn btn-sm btn-primary mb-2" style="float: right;" id="button-add-payment">
                                        <i class="fa fa-plus"></i> Add Payment
                                    </button>
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Cash Advance</th>
                                                    <th>Bank From</th>
                                                    <th>Bank To</th>
                                                    <th>Notes</th>
                                                    <th>Currency</th>
                                                    <th>Currency Rate</th>
                                                    <th>Total Amount</th>
                                                    <th>Total Applied Amount</th>
                                                    <th>Attachment</th>
                                                    <th>Payment Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_payment">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div style="width:70%" class="float-left col">
                    <b id="settlement-total-footer"></b>
                    <b id="chief-approved-settlement-total-footer"></b>
                    <b id="finance-approved-settlement-total-footer"></b>
                </div>
                <button class="btn btn-sm btn-info action_submit" id="submitForm" name="submitForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit"></span></button>&nbsp;
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
var global_products = [];
var global_currencies = [];
var global_company_currency = null;
var global_employee_bank = [];
var global_company_bank = [];
var global_company_allowed_bank = [];
var applied_payments = [];
var current_id_cash_advance = null;
var current_payment_bank_to = null;
var dataTable = null;
var total_visible_settlement = 0;
var chief_approved_total_visible_settlement = 0;
var finance_approved_total_visible_settlement = 0;
$('#label_button_action_submit').text("Save & Submit Payment");
$('#label_button_action_submit_cash_advance').text("Save");

function generatePDF() {
    // Choose the element id which you want to export.
    var element = document.querySelector('.modal-content');
    // element.style.width = '1200px';
    // element.style.height = '900px';
    var opt = {
        margin:       0.5,
        filename:     'settlement_'+new Date().getTime()+'.pdf',
        image:        { type: 'jpeg', quality: 1 },
        html2canvas:  { scale: 1 },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait',precision: '12' }
    };
    // choose the element and pass it to html2pdf() function and call the save() on it to save as pdf.
    html2pdf().set(opt).from(element).save();
}

moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
});
function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D/MM/YYYY');
return formattedDate;
}
function formatRupiah(amount) {
    if(amount === 0 || amount === '0') return `${amount}`;
    if (!amount) {
        return '';
    }   
    return amount.toLocaleString('id-ID');
}
function unformatRupiah(rupiah) {
    if (!rupiah) {
        return 0;
    }
    return parseInt(rupiah.replace(/,|\./g, ''));
}
$(function () {
    $('#filter_date').daterangepicker({
        // autoUpdateInput: false,
        // autoApply: false,
        // minDate: moment().startOf('day'),
        // singleDatePicker: true,
        locale: {
            cancelLabel: 'Reset',
            format: 'YYYY-MM-DD',
            separator: ' to '
        }
    });
    // .on('apply.daterangepicker',function(ev, picker) {
    //     var startDate = picker.startDate;
    //     var endDate = picker.endDate;
    //     var request_by = $("#request_by").val(); 
    // });
    dataTable = $('#settlementtravel_table').DataTable({
      processing: true,
      pageLength: 50,
      columnDefs: [
      {
        orderable: false,
        // className: 'select-checkbox',
        targets: 0
      }
      ],
      responsive: true,  
      ajax: {
        url:    @if($page == "settlement_clearing") `{{ $dataUrl }}?status=not_clear`
                @elseif($page == "expense_payment_finance") `{{ $dataUrl }}?is_paid_by_finance=FALSE`
                @endif
        ,
        error: function (jqXHR, textStatus, errorThrown) {
          $('#settlementtravel_table').DataTable().ajax.reload();
        }
      },  
      columns: [
      {
        defaultContent: '',
        orderable: false
      },
      // {   
      //   data: 'id_official_travel',
      //   defaultContent: '',
      //   orderable: false
      // },
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'reference_number', name: 'reference_number' },
      { 
        data: 'transaction_date', 
        name: 'transaction_date', 
        render: function (data, type, row) {
          return TanggalIndonesia(data);
        }  
      },
      { data: 'name_employee', name: 'name_employee' },
      { data: 'remark_transaction_type', name: 'remark_transaction_type' },
        { data: 'reason_notes', name: 'reason_notes' },
        { data: 'total_cash_request_amount', name: 'total_cash_request_amount' },
        { data: 'total_base_currency_settlement_amount', name: 'total_base_currency_settlement_amount' },
        { data: 'refund_date', name: 'refund_date' },
        { data: 'refund_amount', name: 'refund_amount' },
        { 
            data: 'total_base_currency_payment_amount', 
            name: 'total_base_currency_payment_amount',
            render: function(data) {
                return `Rp${formatRupiah(parseInt(data))}`;
            }
        },
        { data: 'is_validate', name: 'is_validate', render: function ( data, type, row ) {
                if(data == true) {
                    return '<input type="checkbox" checked="true" disabled>';
                } else {
                    return '<input type="checkbox" checked="false" disabled>';
                }
            }
        },
        { 
            data: 'is_paid_by_hr', 
            name: 'is_paid_by_hr', 
            render: (data) => {
                if(data) {
                    return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Paid</span>';
                }
                return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Paid</span>';
            }
        },
        { 
            data: 'is_approved_by_chief', 
            name: 'is_approved_by_chief', 
            render: (data) => {
                if(data) {
                    return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Approved</span>';
                } else if(data == null) {
                    return '<span class="badge badge-secondary" style="padding:5px;font-size:12px;">Pending Approval</span>';
                }
                return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Approved</span>';
            }
        },
        { 
            data: 'is_approved_by_finance', 
            name: 'is_approved_by_finance', 
            render: (data) => {
                if(data) {
                    return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Approved</span>';
                } else if(data == null) {
                    return '<span class="badge badge-secondary" style="padding:5px;font-size:12px;">Pending Approval</span>';
                }
                return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Approved</span>';
            }
        },
		{ data: 'settlement_status', name: 'settlement_status', className: 'text-center', render: function ( data, type, row ) {	
				if(data == 'Clear'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Cleared</span>';
				}
				else if(data == 'Not_Clear'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Clear</span>';
				}
				else{
					return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
				}
			}
		},
        { data: 'is_paid_by_finance', name: 'is_paid_by_finance', className: 'text-center', render: function ( data, type, row ) {	
				if(row.is_paid_by_finance == false || row.is_paid_by_finance == "false"){
					return '<span class="badge badge-secondary" style="padding:5px;font-size:12px;">Not Paid</span>';
				}
				else if(row.is_paid_by_finance == true || row.is_paid_by_finance == "true"){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Paid</span>';
				}
				else{
					return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
				}
			}
		},
        { data: 'action', name: 'action', orderable: false, className: 'space' }
        ],
        rowCallback: function(row, data, index){
          if(access_create == 0){
            $(row).find('.new').css('display', 'none');
          } 
          if(access_edit == 0){
            $(row).find('.btn-edit').css('display', 'none');
          }   
          if(access_delete == 0){
            $(row).find('.btn-del').css('display', 'none');
          }
        },
      });
  });


function changeTotal(element) {
    let id = $(element.target).attr('id').split("-")[2];
    let qty = $('#settlement-qty-'+id);
    let price = $('#settlement-price-'+id);
    $(`#settlement-total-${id}`).val(formatRupiah(unformatRupiah(price.val())*qty.val()));
}

// $(document).on('change', '.settlement-price', (element) => {
//     changeTotal(element)
// });
$(document).on('change', '.settlement-qty', (element) => {
    changeTotal(element)
});
$(document).on('change', '.settlement-currency-rate', (element) => {
    changeTotal(element)
});
$(document).on('change', '.settlement-currency', (element) => {
    changeTotal(element)
});

function handleValidationError(errors) {
    Object.keys(errors).forEach(function (key) {
        var key_temp = key.replaceAll(".", "_");
        $("#" + key_temp).addClass("is-invalid");
        $("#" + key_temp + "Error").parent().find('input, select').addClass('is-invalid');
        let text = errors[key][0].split(".");
        if(text.length > 2) {
            text = text[2].charAt(0).toUpperCase() + text[2].slice(1);
        } else {
            text = errors[key][0];
        }
        $("#" + key_temp + "Error").children("strong").text(text.replaceAll("_", " "));
    });
}

$(document).on("submit", '#travelForm', function (event) {
    let formData = $('#travelForm').serialize();
    event.preventDefault();
    $.ajax({
        url: `{{ route('settlementtravel.payment.update') }}`,
        type: 'POST',
        dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: function (data, status)
        {
            $('#loader').addClass('hidden');
            swal({
                title: "Success!",
                text: data.message,
                icon: "success",
            }).then(() => {
                // $('#modal_form_settlement').modal('hide');
                loadModalData(current_id_cash_advance);
                dataTable.ajax.reload();
            });
        },
        error: function (xhr, desc, err)
        {
            $('#loader').addClass('hidden');
            swal({
                title: "Error!",
                text: xhr.responseJSON.message,
                icon: "error",
            });
            if (xhr.status === 422) {
                handleValidationError(xhr.responseJSON.errors);
            } else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+xhr.responseJSON.message
                });
            }
        }
    });   
});

$('#modal_form_settlement').on('click', '#submitForm', function () {
    $('#travelForm').submit();
})

$(document).on('click', '.settlement-delete', function () {
    $(this).parent().closest('tr').remove();
    var more_id = jQuery(this).attr('settlement-id');
    var currentValues = $("#id_settlement_del").val();
    if (currentValues) {
        if (more_id) {
            $("#id_settlement_del").val(currentValues + ',' + more_id);
        }
    } else {
        $("#id_settlement_del").val(more_id);
    }
    $('#table_settlement tr').each(function (index) {
        $(this).find('.settlement-no').text(index + 1);
    });
    return true;
});

$(document).on('click', '.refund-delete', function () {
    $(this).parent().closest('tr').remove();
    // var more_id = jQuery(this).attr('more_id');
    // var currentValues = $("#id_settlement_del").val();
    // if (currentValues) {
    //     if (more_id) {
    //         $("#id_settlement_del").val(currentValues + ',' + more_id);
    //     }
    // } else {
    //     $("#id_settlement_del").val(more_id);
    // }
    $('#table_refund tr').each(function (index) {
        $(this).find('.refund-no').text(index + 1);
    });
    return true;
});

$(document).on('click', '.payment-delete', function () {
    $(this).parent().closest('tr').remove();
    // var more_id = jQuery(this).attr('settlement-id');
    // var currentValues = $("#id_settlement_del").val();
    // if (currentValues) {
    //     if (more_id) {
    //         $("#id_settlement_del").val(currentValues + ',' + more_id);
    //     }
    // } else {
    //     $("#id_settlement_del").val(more_id);
    // }
    $('#table_payment tr').each(function (index) {
        $(this).find('.payment-no').text(index + 1);
    });
    return true;
});

$(document).on('change', '.settlement-region', function () {
    if($(this).val()) {
        $.ajax({
            url: "{{ route('settlementtravel.get_branch') }}",
            data: {
                id_region: $(this).val()
            },
            success: (response) => {
                $(this).parent().parent().find('.settlement-branch').empty();
                $(this).parent().parent().find('.settlement-branch').select2({
                    data: response
                });
                // console.log($(this).attr('id-branch'))
                if($(this).attr('id-branch')) {
                    $(this).parent().parent().find('.settlement-branch')
                        .val($(this).attr("id-branch"))
                        .trigger('change')
                        .attr('readonly', true);
                } else {
                    $(this).parent().parent().find('.settlement-branch').attr('readonly', false);
                }
            }
        });
    }
});

$(document).on('change', '.settlement-currency', function() {
    if(Number.parseInt($(this).val()) != global_company_currency.id) {
        let index = $(this).attr('id').split("-")[2];
        $('#settlement-currencyrate-'+index).attr('readonly', false);
    } else {
        let index = $(this).attr('id').split("-")[2];
        $('#settlement-currencyrate-'+index).attr('readonly', true);
        $('#settlement-currencyrate-'+index).val(1);
    }
    if($(this).parent().parent().find('.id-settlement').val()) {
        $(this).parent().parent().find('.settlement-currency-rate').attr('readonly', true);
    }
});

$(document).on('change', '.payment-currency', function() {
    if($(this).attr('id')) {
        if(Number.parseInt($(this).val()) != global_company_currency.id) {
            let index = $(this).attr('id').split("-")[2];
            $(`#payment-currencyrate-${index}`).attr('readonly', false);
        } else {
            let index = $(this).attr('id').split("-")[2];
            $(`#payment-currencyrate-${index}`).attr('readonly', true);
            $(`#payment-currencyrate-${index}`).val(1);
        }
    }
    if($(this).parent().parent().find('.id-payment').val()) {
        $(this).parent().parent().find('.payment-currency-rate').attr('readonly', true);
    } 
});

$(document).on('keyup', '.payment-total-amount', function () {
    $(this).val(formatRupiah(parseInt(unformatRupiah($(this).val()))));
});
$(document).on('keyup', '.payment-total-applied-amount', function () {
    $(this).val(formatRupiah(parseInt(unformatRupiah($(this).val()))));
});

$(document).on('keyup', '.settlement-approval-price', function () {
    $(this).val(formatRupiah(parseInt(unformatRupiah($(this).val()))));
    let approval_amount = unformatRupiah($(this).val());
    let qty = $(this).parent().parent().find('.settlement-qty').text();
    let rate = unformatRupiah($(this).parent().parent().find('.settlement-currency-rate').text());
    let product_max_price = unformatRupiah($(this).parent().parent().find('.settlement-budget').text());
    if(approval_amount*rate > product_max_price) {
        $(this).addClass('text-danger').addClass('font-weight-bold');
    } else {
        $(this).removeClass('text-danger').removeClass('font-weight-bold');
    }
    $(this).parent().parent().find('.settlement-total-approval').val(formatRupiah(parseInt(approval_amount*qty*rate)));
});

function loadModalData(idCashAdvance) {
    let fas = "{{ @$fas ? 'true' : 'false' }}";
    $.ajax({
        url: '{{ route("settlementtravel.approval.data") }}',
        data: {
            id_cash_advance: idCashAdvance,
            type: "finance",
            ignore_is_validate: 1,
            fas: fas
        },
        beforeSend: () => {
            total_visible_settlement = 0;
            chief_approved_total_visible_settlement = 0;
            finance_approved_total_visible_settlement = 0;
            $('#table_settlement_summary').empty();
            $('#loader').removeClass('hidden');
        },
        error: (err) => {
            $('#loader').addClass('hidden');
            $('#modal_form_settlement').modal('hide');
            swal({
                icon: 'error',
                title: 'Error',
                message: err.responseJSON.message,
            });
        },
        success: (response) => {
            global_products = response.products;
            global_currencies = response.currencies.all;
            global_company_currency = response.currencies.company;
            global_employee_bank = response.bank;
            global_company_bank = response.company_bank;
            global_company_allowed_bank = response.company_allowed_bank;
            current_payment_bank_to = response.payment_bank_to;

            if(response.cash_advance.attachment_settlement) {
                $('.attachment-settlement').hide();
                $('#view-attachment-settlement').attr('path', `/project/storage/app/public/${response.cash_advance.attachment_settlement}`)
                $('#view-attachment-settlement').show();
            } else {
                $('.attachment-settlement').show();
                $('#view-attachment-settlement').hide();
            }
            // let settlementLabel = 'Settlement';
            // if(response.cash_advance.total_base_currency_settlement_amount < 1) {
            //     settlementLabel = 'Expense Request';
            // }
            // $('#label-settlement-status').text(settlementLabel+' Status');
            // $('#label-currency-settlement').text('Currency '+settlementLabel);
            // $('#label-total-settlement').text('Total '+settlementLabel);
            $('#id_cash_advance').val(response.cash_advance.id_cash_advance);
            $('#cash_advance_reference').val(response.cash_advance.reference_number);
            $('#request_total_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.hca_total_expense_request_amount)));
            $('#total_cash_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_cash_request_amount)));
            $('#total_travel_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_travel_request_amount)));
            $('#reference_number').val(response.cash_advance.travel_reference_number);
            $('#transaction_date').val(response.cash_advance.transaction_date);
            $('#cash_advance_notes').val(response.cash_advance.reason_notes);
            $('#clearing_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_clearing_amount))).attr('disabled', false);
            $('#payment_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_payment_amount)));
            $('#total_settlement').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_settlement_amount)));
            $('#total_base_currency_tax_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_tax_amount)));
            $('#difference_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_difference_amount)));
            $('#maximum_clearing_date').val(response.cash_advance.maximum_clearing_date).attr('disabled', true);
            $('#total_taxed_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_tax_amount)));

            if(response.cash_advance.settlement_status != 'Clear') {
                $('#manual-clearing').show();
            } else {
                $('#manual-clearing').hide();
            }

            if(response.cash_advance.total_base_currency_difference_amount > 0 || response.cash_advance.start_refund_date != null) {
                if(response.cash_advance.start_refund_date) {
                    $('#billing_letter_container').html(`
                        <button type="button" onClick="getBillingLetter(${response.cash_advance.id_cash_advance})" class="btn btn-sm btn-primary">Download</button>
                    `);
                } else {
                    $('#billing_letter_container').html(`
                        <button type="button" onClick="sendBillingLetter(${response.cash_advance.id_cash_advance})" class="btn btn-sm btn-primary" {{ @$fas ? 'disabled="true"' : '' }}>Send Billing Letter</button>
                    `);
                }
            } else {
                $('#billing_letter_container').html(``);
            }

            $('#accounting_date, #maximum_clearing_date').daterangepicker({
                drops: 'down',
                autoUpdateInput: false,
                singleDatePicker: true,
                locale: {
                    cancelLabel: 'Reset',
                    format: 'YYYY-MM-DD',
                    separator: ' to '
                }
            }).on('apply.daterangepicker', function(ev, picker) {
                // $(this).val(picker.startDate.format('MM/DD/YYYY');
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            }).on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
            $('#accounting_date').val(response.cash_advance.accounting_date);

            $('#id_clearing_account').empty().prepend('<option></option>').select2({
                data: response.master_chart_account,
                allowClear: true,
                placeholder: 'Select Clearing Account'
            }).val(response.cash_advance.id_clearing_account).trigger('change').attr('readonly', false);

            $('#payment_currency').empty().prepend('<option><option>').select2({
                data: response.currencies.all
            });
            $('#settlement_status').empty().select2({
                data: [
                    {id: "Not_Clear", text: "Not Clear"},
                    {id: "Clear", text: "Clear"},
                ]
            }).val(response.cash_advance.settlement_status).trigger('change');
            $('#request_currency').empty().select2({
                data: response.currencies.all
            }).val(response.cash_advance.id_currency_cash_advance).trigger('change');
            $('#payment_status').empty().select2({
                data: [
                    {id: "Not_Paid", text: "Not Paid"},
                    {id: "Pending", text: "Pending"},
                    {id: "Hold", text: "Hold"},
                    {id: "Paid", text: "Paid"},
                    {id: "Cancel", text: "Cancel"}
                ]
            }).val(response.cash_advance.payment_status).trigger('change');
            
            if(response.cash_advance.hca_approval_status == "Approved") {
                $('#approval_status_container').html("<span class='badge badge-success'>Approved</span>");
            } else if(response.cash_advance.hca_approval_status == "Rejected") {
                $('#approval_status_container').html("<span class='badge badge-danger'>Rejected</span>");
            } else {
                $('#approval_status_container').html("<b>"+response.cash_advance.hca_approval_status+"</b>")
            }

            if(response.cash_advance.is_validate) {
                $('#validation_status_container').html("<span class='badge badge-success'>Valid</span>");
            } else {
                $('#validation_status_container').html("<span class='badge badge-danger'>Invalid</span>");
            }

            $('#department').empty().select2({
                data: [
                    {id: response.cash_advance.id_department, text: response.cash_advance.department_description}
                ]
            });
            $('#position_detail').empty().select2({
                data: [
                    {id: null, text: response.cash_advance.position_detail}
                ]
            });
            $('#approval_by').empty().select2({
                data: [
                    {id: response.cash_advance.id_approval_request, text: response.cash_advance.name_approval_request}
                ]
            });
            $('#request_by').empty().select2({
                data: [
                    {id: response.cash_advance.id_employee, text: response.cash_advance.name_employee}
                ]
            });
            $('#approval_hierarchy').empty().select2({
                data: [
                    {id: response.cash_advance.id_approval, text: response.cash_advance.approval_description}
                ],
            })
            
            $('#table_request').empty();
            $('#table_settlement').empty();
            $('#table_transport_accommodation').empty();
            $('#table_refund').empty();
            $('#table_payment').empty();

            if(response.transport_accommodation.length < 1) {
                $('.transaco-table').addClass('hidden');
            } else {
                $('.transaco-table').removeClass('hidden');
            }

            if(response.settlement_summary.length < 1) {
                $('.settlement-summary-table').addClass('hidden');
            } else {
                $('.settlement-summary-table').removeClass('hidden');
            }

            response.data.forEach((data, i) => {
                let index = i+1;
                let notes = data.notes.split(";");
                let clone = $('#table-request-sample').clone();
                clone.find('.request-no').attr('id', 'request-no-' + i);
                clone.find('.request-no').text(index);
                clone.find('.request-product').attr('id', 'request-product-' + i);
                clone.find('.request-product').attr('name', `request[${i}][product]`);
                clone.find('.request-product').val(data.desc_product);
                clone.find('.request-description').attr('id', 'request-description-' + i);
                clone.find('.request-description').attr('name', `request[${i}][description]`);
                clone.find('.request-description').val(notes[1]+' '+notes[2]);
                clone.find('.request-notes').attr('id', 'request-notes-' + i);
                clone.find('.request-notes').attr('name', `request[${i}][notes]`);
                clone.find('.request-notes').val(notes[0]);
                clone.find('.request-qty').attr('id', 'request-qty-' + index);
                clone.find('.request-qty').attr('name', `request[${i}][qty]`);
                clone.find('.request-qty').val(data.qty);
                clone.find('.request-price').attr('id', 'request-price-' + index);
                clone.find('.request-price').attr('name', `request[${i}][price]`);
                clone.find('.request-price').val(formatRupiah(data.unit_price));
                clone.find('.request-total').attr('id', 'request-total-' + index);
                clone.find('.request-total').attr('name', `request[${i}][total]`);
                clone.find('.request-total').val(formatRupiah(data.total_amount));
                clone.appendTo('#table_request');
            });

            response.settlement.forEach((settlement, i) => {
                total_visible_settlement += parseInt(settlement.total_amount);
                if(settlement.is_verified_by_chief !== false) {
                    chief_approved_total_visible_settlement += parseInt(settlement.approval_price_by_chief * settlement.qty * settlement.currency_rate_settlement_expense);
                }
                if(settlement.is_verified_by_finance !== false) {
                    finance_approved_total_visible_settlement += parseInt(settlement.approval_price_by_finance * settlement.qty * settlement.currency_rate_settlement_expense);
                }
                $('#currency_settlement').select2({
                    data: global_currencies
                }).val(settlement.id_currency_settlement_expense).trigger('change').attr('disabled', true);

                let clone = $('#table-settlement-sample').clone();
                clone.appendTo('#table_settlement');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('input').attr('readonly', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    $(element).find('select').prop('disabled', true);
                    $(element).find('.id-settlement').val(settlement.id_settlement_expense);
                    $(element).find('.id-settlement').attr('name', `settlement[${i}][id_settlement]`);
                    $(element).find('.id-settlement').attr('approval', settlement.is_verified_by_chief == true ? "true" : settlement.is_verified_by_chief == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('finance-approval', settlement.is_verified_by_finance == true ? "true" : settlement.is_verified_by_finance == false ? "false" : "null");
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(index);
                    $(element).find('.settlement-start-end').attr('id', 'settlement-startend-' + i);
                    $(element).find('.settlement-start-end').attr('name', `settlement[${i}][start_end]`);
                    $(element).find('.settlement-start-end').val(settlement.settlement_date);
                    $(element).find('.settlement-product').attr('id', 'settlement-product-' + i);
                    $(element).find('.settlement-product').attr('name', `settlement[${i}][product]`);
                    $(element).find('.product-error').attr('id', `settlement_${i}_productError`);
                    $(element).find('.settlement-region').attr('id', 'settlement-region-' + i);
                    $(element).find('.settlement-region').attr('name', `settlement[${i}][region]`);
                    $(element).find('.region-error').attr('id', `settlement_${i}_regionError`);
                    $(element).find('.settlement-branch').attr('id', 'settlement-branch-' + i);
                    $(element).find('.settlement-branch').attr('name', `settlement[${i}][branch]`);
                    $(element).find('.branch-error').attr('id', `settlement_${i}_branchError`);
                    $(element).find('.settlement-description').attr('id', 'settlement-description-' + i);
                    $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
                    $(element).find('.settlement-description').text(settlement.description);
                    $(element).find('.description-error').attr('id', `settlement_${i}_descriptionError`);
                    $(element).find('.settlement-notes').attr('id', 'settlement-notes-' + i);
                    $(element).find('.settlement-notes').attr('name', `settlement[${i}][notes]`);
                    $(element).find('.settlement-notes').val(settlement.notes);
                    $(element).find('.settlement-qty').attr('id', 'settlement-qty-' + i);
                    $(element).find('.settlement-qty').attr('name', `settlement[${i}][qty]`);
                    $(element).find('.settlement-qty').text(settlement.qty);
                    $(element).find('.qty-error').attr('id', `settlement_${i}_qtyError`);
                    $(element).find('.settlement-unit').attr('id', 'settlement-unit-' + i);
                    $(element).find('.settlement-unit').attr('name', `settlement[${i}][unit]`);
                    $(element).find('.settlement-unit').val(settlement.qty);
                    $(element).find('.unit-error').attr('id', `settlement_${i}_unitError`);
                    $(element).find('.settlement-currency').attr('id', 'settlement-currency-' + i);
                    $(element).find('.settlement-currency').attr('name', `settlement[${i}][currency]`);
                    $(element).find('.settlement-currency-rate').attr('id', 'settlement-currencyrate-' + i);
                    $(element).find('.settlement-currency-rate').attr('name', `settlement[${i}][currency_rate]`);
                    $(element).find('.settlement-price').attr('id', 'settlement-price-' + i);
                    $(element).find('.settlement-price').attr('name', `settlement[${i}][price]`);
                    $(element).find('.settlement-price').text(formatRupiah(parseInt(settlement.unit_price)));
                    $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
                    $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
                    $(element).find('.settlement-total').text(formatRupiah(parseInt(settlement.total_amount)));
                    $(element).find('.settlement-total-base-currency-amount').attr('id', 'settlement-total-base-currency-amount-' + i);
                    $(element).find('.settlement-total-base-currency-amount').text(response.currencies.company.currency_symbol + formatRupiah(parseInt(settlement.total_base_currency_amount)));
                    $(element).find('.settlement-chief-approval-price').attr('id', 'settlement-chiefapprovalprice-' + i);
                    $(element).find('.settlement-chief-approval-price').attr('name', `settlement[${i}][chief_approval_price]`);
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
                    $(element).find('.settlement-chief-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-chief-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-chief-total-approval').val(formatRupiah(parseInt(settlement.approval_price_by_chief * settlement.qty * settlement.currency_rate_settlement_expense)));
                    $(element).find('.settlement-approval-price').attr('id', 'settlement-approvalprice-' + i);
                    $(element).find('.settlement-approval-price').attr('name', `settlement[${i}][approval_price]`);
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
                    // if(settlement.approval_price_by_finance > settlement.product_max_price) {
                    //     $(element).find('.settlement-approval-price').addClass('text-danger').addClass('font-weight-bold');
                    // }
                    $(element).find('.settlement-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
                    // $(element).find('.settlement-budget').text(formatRupiah(parseInt(settlement.product_max_price)));
                    $(element).find('.settlement-attachment').attr('id', 'settlement-attachment-' + i);
                    $(element).find('.settlement-attachment').attr('name', `settlement[${i}][attachment]`);
                    if(settlement.attachment) {
                        $(element).find('.settlement-attachment-download').css('display', 'block');
                        $(element).find('.settlement-attachment-download').children().attr('href', '/project/storage/app/public/'+settlement.attachment);
                        $(element).find('.settlement-attachment-download').children().text('Download');
                    }
                    $(element).find('.settlement-unit').select2({
                        data: [{id: settlement.id_uom, text: settlement.desc_uom}]
                    }).val(settlement.id_uom).trigger('change').attr('readonly', true).css('width', '75px');
                    $(element).find('.settlement-product').css('width', '140px');
                    $(element).find('.settlement-product').select2({
                        data: response.full_products,
                        placeholder: "Select Product",
                        allowClear: true
                    }).val(settlement.id_product).trigger('change');
                    $(element).find('.settlement-region').select2({
                        data: response.regions
                    }).attr('id-branch', settlement.id_branch).attr('id-region', settlement.id_region).css('width', '100px');
                    $(element).find('.settlement-region').val(settlement.id_region).trigger('change');
                    $(element).find('.settlement-currency').select2({
                        data: global_currencies
                    }).val(settlement.id_currency_settlement_expense).trigger('change');
                    $(element).find('.settlement-currency-rate').text(formatRupiah(parseInt(settlement.currency_rate_settlement_expense)));
                })
                $('#table_settlement').children().each((i, element) => {
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(i+1);
                    $(element).find('.settlement-currency').trigger('change');

                    if(!$(element).find('.id-settlement').attr('approval')) {
                        $(element).find('.settlement-action').html(`
                            <button class="btn btn-sm btn-success"><span class="fas fa-check"></span></button>
                        `);
                    } else if($(element).find('.id-settlement').attr('approval') == "false" || $(element).find('.id-settlement').attr('finance-approval') == "false") {
                        $(element).find('.settlement-action').html(`<span class="badge badge-danger">Rejected</span>`);
                    } else if($(element).find('.id-settlement').attr('approval') == "true") {
                        let badge = `<span class="badge badge-success">Approved by Chief</span>`;
                        if($(element).find('.id-settlement').attr('finance-approval') == "true") {
                            badge += `<span class="badge badge-success">Approved by Finance</span>`
                            if(fas == 'false') {
                                badge += `<button type="button" class="btn btn-sm btn-info mr-2 mb-2 button-action-settlement" action="revise" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-calendar"></span> Revise</button>`;
                            }
                        } else if($(element).find('.id-settlement').attr('finance-approval') == "false") {
                            badge += `<span class="badge badge-danger">Rejected by Finance</span>`
                        } else if($(element).find('.id-settlement').attr('finance-approval') == "null") {
                            $(element).find('.settlement-notes').attr('readonly', false);
                            badge += `
                                <button type="button" class="btn btn-sm btn-success mr-2 mb-2 button-action-settlement" action="approve" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-check"></span> Approve</button>
                                <button type="button" class="btn btn-sm btn-info mr-2 mb-2 button-action-settlement" action="revise" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-calendar"></span> Revise</button>
                                <button type="button" class="btn btn-sm btn-danger button-action-settlement" action="reject" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-times"></span> Reject</button>
                            `;
                            $(element).find('.settlement-approval-price').attr('disabled', false).attr('readonly', false);
                            $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(unformatRupiah($(element).find('.settlement-chief-approval-price').val())))).trigger('change');
                        }
                        $(element).find('.settlement-action').html(badge);
                    } else if($(element).find('.id-settlement').attr('approval') == "null") {
                        $(element).find('.settlement-action').html(`<span class="badge badge-warning">Pending Chief Approval</span>`);
                    }  
                    // else {
                    //     $(element).find('.settlement-notes').attr('readonly', false);
                    //     $(element).find('.settlement-action').html(`
                    //         <button type="button" class="btn btn-sm btn-success mr-2 mb-2 button-action-settlement" action="approve" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-check"></span> Approve</button>
                    //         <button type="button" class="btn btn-sm btn-danger button-action-settlement" action="reject" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-times"></span> Reject</button>
                    //     `);
                    // }
                });
                
                if(i+1 == response.settlement.length) {
                    $('#tab-settlement').trigger('click');
                }
            })

            response.transport_accommodation.forEach((settlement, i) => {
                $('#currency_settlement').select2({
                    data: global_currencies
                }).val(settlement.id_currency_settlement_expense).trigger('change').attr('disabled', true);

                let clone = $('#table-settlement-sample').clone();
                if(settlement.is_paid_by_hr) {
                    clone.find('.settlement-action').html(`<span class="badge badge-success">Purchased by HR</span>`);
                }
                clone.appendTo('#table_transport_accommodation');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('input').attr('readonly', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    $(element).find('select').prop('disabled', true);
                    $(element).find('.id-settlement').val(settlement.id_settlement_expense);
                    $(element).find('.id-settlement').attr('name', `settlement[${i}][id_settlement]`);
                    $(element).find('.id-settlement').attr('approval', settlement.is_verified_by_chief == true ? "true" : settlement.is_verified_by_chief == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('finance-approval', settlement.is_verified_by_finance == true ? "true" : settlement.is_verified_by_finance == false ? "false" : "null");
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(index);
                    $(element).find('.settlement-start-end').attr('id', 'settlement-startend-' + i);
                    $(element).find('.settlement-start-end').attr('name', `settlement[${i}][start_end]`);
                    $(element).find('.settlement-start-end').val(settlement.settlement_date);
                    $(element).find('.settlement-product').attr('id', 'settlement-product-' + i);
                    $(element).find('.settlement-product').attr('name', `settlement[${i}][product]`);
                    $(element).find('.product-error').attr('id', `settlement_${i}_productError`);
                    $(element).find('.settlement-region').attr('id', 'settlement-region-' + i);
                    $(element).find('.settlement-region').attr('name', `settlement[${i}][region]`);
                    $(element).find('.region-error').attr('id', `settlement_${i}_regionError`);
                    $(element).find('.settlement-branch').attr('id', 'settlement-branch-' + i);
                    $(element).find('.settlement-branch').attr('name', `settlement[${i}][branch]`);
                    $(element).find('.branch-error').attr('id', `settlement_${i}_branchError`);
                    $(element).find('.settlement-description').attr('id', 'settlement-description-' + i);
                    $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
                    $(element).find('.settlement-description').text(settlement.description);
                    $(element).find('.description-error').attr('id', `settlement_${i}_descriptionError`);
                    $(element).find('.settlement-notes').attr('id', 'settlement-notes-' + i);
                    $(element).find('.settlement-notes').attr('name', `settlement[${i}][notes]`);
                    $(element).find('.settlement-notes').val(settlement.notes);
                    $(element).find('.settlement-qty').attr('id', 'settlement-qty-' + i);
                    $(element).find('.settlement-qty').attr('name', `settlement[${i}][qty]`);
                    $(element).find('.settlement-qty').text(settlement.qty);
                    $(element).find('.qty-error').attr('id', `settlement_${i}_qtyError`);
                    $(element).find('.settlement-unit').attr('id', 'settlement-unit-' + i);
                    $(element).find('.settlement-unit').attr('name', `settlement[${i}][unit]`);
                    $(element).find('.settlement-unit').val(settlement.qty);
                    $(element).find('.unit-error').attr('id', `settlement_${i}_unitError`);
                    $(element).find('.settlement-currency').attr('id', 'settlement-currency-' + i);
                    $(element).find('.settlement-currency').attr('name', `settlement[${i}][currency]`);
                    $(element).find('.settlement-currency-rate').attr('id', 'settlement-currencyrate-' + i);
                    $(element).find('.settlement-currency-rate').attr('name', `settlement[${i}][currency_rate]`);
                    $(element).find('.settlement-price').attr('id', 'settlement-price-' + i);
                    $(element).find('.settlement-price').attr('name', `settlement[${i}][price]`);
                    $(element).find('.settlement-price').text(formatRupiah(parseInt(settlement.unit_price)));
                    $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
                    $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
                    $(element).find('.settlement-total').text(formatRupiah(parseInt(settlement.total_amount)));
                    $(element).find('.settlement-total-base-currency-amount').attr('id', 'settlement-total-base-currency-amount-' + i);
                    $(element).find('.settlement-total-base-currency-amount').text(response.currencies.company.currency_symbol + formatRupiah(parseInt(settlement.total_base_currency_amount)));
                    $(element).find('.settlement-chief-approval-price').attr('id', 'settlement-chiefapprovalprice-' + i);
                    $(element).find('.settlement-chief-approval-price').attr('name', `settlement[${i}][chief_approval_price]`);
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
                    $(element).find('.settlement-chief-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-chief-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-chief-total-approval').val(formatRupiah(parseInt(settlement.approval_price_by_chief * settlement.qty * settlement.currency_rate_settlement_expense)));
                    $(element).find('.settlement-approval-price').attr('id', 'settlement-approvalprice-' + i);
                    $(element).find('.settlement-approval-price').attr('name', `settlement[${i}][approval_price]`);
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
                    let approval_amount = settlement.approval_price_by_finance > 0 ? settlement.approval_price_by_finance : settlement.approval_price_by_chief ? settlement.approval_price_by_chief : settlement.unit_price;
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(approval_amount))).trigger('change');
                    // if(approval_amount > settlement.product_max_price) {
                    //     $(element).find('.settlement-approval-price').addClass('text-danger').addClass('font-weight-bold');
                    // }
                    $(element).find('.settlement-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
                    // $(element).find('.settlement-budget').text(formatRupiah(parseInt(settlement.product_max_price)));
                    $(element).find('.settlement-attachment').attr('id', 'settlement-attachment-' + i);
                    $(element).find('.settlement-attachment').attr('name', `settlement[${i}][attachment]`);
                    if(settlement.attachment) {
                        $(element).find('.settlement-attachment-download').css('display', 'block');
                        $(element).find('.settlement-attachment-download').children().attr('href', '/project/storage/app/public/'+settlement.attachment);
                        $(element).find('.settlement-attachment-download').children().text('Download');
                    }
                    $(element).find('.settlement-unit').select2({
                        data: [{id: settlement.id_uom, text: settlement.desc_uom}]
                    }).val(settlement.id_uom).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-product').css('width', '100%');
                    $(element).find('.settlement-product').select2({
                        data: response.full_products,
                        placeholder: "Select Product",
                        allowClear: true
                    }).val(settlement.id_product).trigger('change');
                    $(element).find('.settlement-region').select2({
                        data: response.regions
                    }).attr('id-branch', settlement.id_branch).attr('id-region', settlement.id_region);
                    $(element).find('.settlement-region').val(settlement.id_region).trigger('change');
                    $(element).find('.settlement-currency').select2({
                        data: global_currencies
                    }).val(settlement.id_currency_settlement_expense).trigger('change');
                    $(element).find('.settlement-currency-rate').text(formatRupiah(parseInt(settlement.currency_rate_settlement_expense)));
                })
                $('#table_transport_accommodation').children().each((i, element) => {
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(i+1);
                    $(element).find('.settlement-currency').trigger('change');
                });
            })

            response.settlement_summary.forEach((summary, i) => {
                let clone = $('#table-settlement-summary-sample').clone();

                clone.appendTo('#table_settlement_summary');
                $(clone).children().each((j, element) => {
                    $(element).find('.summary-no').text(i+1);
                    $(element).find('.summary-date').text(moment(summary.settlement_date).format('D MMMM YYYY'));
                    $(element).find('.summary-description').text(summary.description);
                    $(element).find('.summary-budget').text(formatRupiah(parseInt(summary.budget_price)));
                    $(element).find('.summary-chief-approval').text(formatRupiah(parseInt(summary.approval_by_chief)));
                    $(element).find('.summary-finance-approval').text(formatRupiah(parseInt(summary.approval_by_finance)));
                    $(element).find('.summary-final-approval').text(formatRupiah(parseInt(summary.final_approval)));
                    $(element).find('.summary-total-expense').text(formatRupiah(parseInt(summary.total_settlement_expense)));
                });
            });

            response.refund.forEach((refund, i) => {
                let clone = $('#table-refund-sample').clone();
                clone.appendTo('#table_refund');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('.id-refund').val(refund.id_cash_refund);
                    $(element).find('.id-refund').attr('name', `refund[${i}][id_refund]`);
                    $(element).find('.id-refund').attr('finance-approval', refund.is_verified ? "true" : "false");
                    $(element).find('.id-refund').attr('is-paid', refund.is_paid ? "true" : "false");
                    $(element).find('.id-refund').attr('payment-status', refund.payment_status);
                    $(element).find('.refund-no').attr('id', 'refund-no-' + i);
                    $(element).find('.refund-no').text(index);
                    $(element).find('.refund-bank-from').attr('name', `refund[${i}][bank_from]`);
                    $(element).find('.refund-bank-from').select2({
                        data: global_employee_bank
                    }).val(refund.id_bank_from);
                    $(element).find('.refund-bank-account').attr('name', `refund[${i}][bank_account]`);
                    $(element).find('.refund-bank-account').val(refund.bank_from_account);
                    $(element).find('.refund-bank-to').attr('name', `refund[${i}][bank_to]`);
                    $(element).find('.refund-bank-to').select2({
                        data: global_company_bank
                    }).val(refund.id_bank_to_account).trigger('change');
                    $(element).find('.refund-amount').attr('name', `refund[${i}][amount]`);
                    $(element).find('.refund-amount').val(formatRupiah(parseInt(refund.total_amount)));
                    $(element).find('.refund-attachment').attr('name', `refund[${i}][attachment]`);

                    if(refund.attachment) {
                        $(element).find('.refund-download').css('display', 'block');
                        $(element).find('.refund-download').children().attr('href', '/project/storage/app/public/'+refund.attachment);
                        $(element).find('.refund-download').children().text('Download');
                    }
                });
                $('#table_refund').children().each((i, element) => {
                    $(element).find('input').attr('readonly', true);
                    $(element).find('select').attr('disabled', true);
                    $(element).find('.refund-no').attr('id', 'refund-no-' + i);
                    $(element).find('.refund-no').text(i+1);

                    $(element).find('.refund-action').html(`
                        <b>${$(element).find('.id-refund').attr('payment-status')}</b>
                    `);

                    if($(element).find('.id-refund').attr('finance-approval') == "true") {
                        if($(element).find('.id-refund').attr('is-paid') == "true") {

                        } else {
                            $(element).find('.refund-action').html(`
                                <select class="refund-payment-status" id-refund="${$(element).find('.id-refund').val()}" style="width:60%"></select>
                                <button class="btn btn-sm btn-info button-refund-payment-status" type="button">Save</button>
                            `);
                        }
                    } else {
                        $(element).find('.refund-action').html(`
                            <select class="refund-payment-status" id-refund="${$(element).find('.id-refund').val()}" style="width:60%"></select>
                            <button class="btn btn-sm btn-info button-refund-payment-status" type="button">Save</button>
                        `);
                    }
                    $(element).find('.refund-payment-status').select2({
                        data: [
                            {id: "Not_Paid", text: "Not Paid"},
                            {id: "Partially_Paid", text: "Partially Paid"},
                            {id: "Paid", text: "Paid"}
                        ]
                    }).val($(element).find('.id-refund').attr('payment-status')).trigger('change');
                    // $(element).find('.refund-action').html(`
                    //     <button type="button" class="btn btn-danger refund-delete" id="refund-delete-${i}" refund-id="${$(element).find('.id-refund').val()}"><i class="fas fa-trash"></i></button>
                    // `);
                });
            });

            response.payment.forEach((payment, i) => {
                $('#payment_currency').val(payment.id_currency_cash_payment).trigger('change');

                let clone = $('#table-payment-sample').clone();
                clone.appendTo('#table_payment');
                clone.find('.payment-id-employee-bank-to').val(payment.id_employee_bank_to);
                if(payment.can_apply_payment) {
                    clone.find('.payment-action').html(`
                        <button type='button' class="btn btn-sm btn-primary btn-apply-payment" available-payment-amount="${payment.total_amount - payment.total_applied_amount}" payment-id="${payment.id_cash_payment}">Apply</button>
                    `);
                } else {
                    clone.find('input').attr('readonly', true);
                    clone.find('select').attr('readonly', true);
                    clone.find('.payment-status').attr('readonly', false);
                }
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('.id-payment').val(payment.id_cash_payment);
                    $(element).find('.id-payment').attr('name', `payment[${i}][id_payment]`);
                    $(element).find('.payment-cash-advance-ref').val(payment.reference_number);
                    $(element).find('.payment-cash-advance-ref').attr('name', `payment[${i}][cash_advance_ref]`);
                    $(element).find('.payment-bank-from').val(payment.bank_from);
                    $(element).find('.payment-bank-from').attr('name', `payment[${i}][bank_from]`);
                    $(element).find('.payment-bank-from-account').select2({
                        data: global_company_bank
                    }).val(payment.id_bank_from_account).trigger('change');
                    $(element).find('.payment-bank-from-account').attr('name', `payment[${i}][bank_from_account]`);
                    $(element).find('.payment-id-employee-bank-to').attr('name', `payment[${i}][id_employee_bank_to]`);
                    $(element).find('.payment-bank-to-account').text(`${payment.employee_bank_account ? `${payment.employee_bank_name} ${payment.employee_bank_account}` : '-'}`);
                    $(element).find('.payment-notes').val(payment.notes);
                    $(element).find('.payment-notes').attr('name', `payment[${i}][notes]`);
                    $(element).find('.payment-currency').select2({
                        data: global_currencies
                    }).val(payment.id_currency_cash_payment).trigger('change');
                    $(element).find('.payment-currency').attr('name', `payment[${i}][currency]`);
                    $(element).find('.payment-currency').attr('id', `payment-currency-${i}`);
                    $(element).find('.payment-currency-rate').val(payment.currency_rate_payment);
                    $(element).find('.payment-currency-rate').attr('name', `payment[${i}][currency_rate]`);
                    $(element).find('.payment-currency-rate').attr('id', `payment-currencyrate-${i}`);
                    $(element).find('.payment-total-amount').val(formatRupiah(parseInt(payment.total_amount)));
                    $(element).find('.payment-total-amount').attr('name', `payment[${i}][total_amount]`);
                    $(element).find('.payment-total-applied-amount').val(formatRupiah(parseInt(payment.total_applied_amount)));
                    $(element).find('.payment-total-applied-amount').attr('name', `payment[${i}][total_applied_amount]`);
                    $(element).find('.payment-total-applied-amount').attr('readonly', true);
                    $(element).find('.payment-status').select2({
                        data: [
                            {id: "Paid", text: "Paid"},
                            {id: "Not_Paid", text: "Not Paid"},
                            // {id: "Partially_Paid", text: "Partially Paid"},
                        ]
                    }).val(payment.payment_status).trigger('change');
                    $(element).find('.payment-status').attr('name', `payment[${i}][payment_status]`);
                    if(payment.attachment) {
                        $(element).find('.payment-download').css('display', 'block');
                        $(element).find('.payment-download').children().attr('href', '/project/storage/app/public/'+payment.attachment);
                        $(element).find('.payment-download').children().text('Download');
                    }
                });
                $('#table_payment').children().each((i, element) => {
                    $(element).find('.payment-no').attr('id', 'payment-no-' + i);
                    $(element).find('.payment-no').text(i+1);
                    $(element).find('.payment-currency').trigger('change');
                    $(element).find('.invalid-feedback').each((j, errElement) => {
                        $(errElement).attr('id', $(errElement).attr('id').replaceAll('0x', i));
                    });
                });
            });
            $('#loader').addClass('hidden');
        }
    });
}

$(document).on('click', '.button-view', function() {
    let idCashAdvance = $(this).attr('id-cash-advance');
    current_id_cash_advance = idCashAdvance;
    $('#modal_form_settlement').modal('show');
    loadModalData(idCashAdvance);
});

let current_settlement_total_list = [];
$(document).on('click', '.btn-apply-payment', function() {
    let idPayment = $(this).attr('payment-id');
    let availableAmount = $(this).attr('available-payment-amount');
    $('#apply_payment_id_cash_payment').val(idPayment);
    $.ajax({
        url: "{{ route('settlementtravel.unpaid') }}",
        data: {
            id_cash_advance: current_id_cash_advance,
            type: "finance"
        },
        beforeSend: () => {
            $('#loader').removeClass('hidden');
            $('#payment_settlement').empty();
        },
        success: (response) => {
            current_settlement_total_list = response;
            $('#loader').addClass('hidden');
            $('#modal_form_apply_payment').modal('show');
            $('#payment_settlement_available_amount').text(formatRupiah(parseInt(availableAmount)));
            $('#payment_settlement').select2({
                data: response
            })
        },
        error: () => {
            $('#loader').addClass('hidden');
        }
    })
});

$(document).on('change', '#payment_settlement', function () {
    // $('#payment_settlement_total_applied').text();
    // console.log($(this).val());
    let total = 0;
    $(this).val().forEach((settlement_id) => {
        current_settlement_total_list.forEach((settlement) => {
            if(parseInt(settlement_id) == settlement.id) {
                total += parseInt(settlement.total_base_currency_amount)
            }
        })
    })
    $('#payment_settlement_total_applied').text(formatRupiah(total));
    if(total > unformatRupiah($('#payment_settlement_available_amount').text())) {
        $('.btn-submit-apply-payment').hide();
        $('#payment_settlement_total_applied').addClass('text-danger');
    } else {
        $('.btn-submit-apply-payment').show();
        $('#payment_settlement_total_applied').removeClass('text-danger');
    }
})

$(document).on("submit", '#cashAdvanceForm', function (event) {
    let formData = $('#cashAdvanceForm').serialize();
    event.preventDefault();
    $.ajax({
        url: `{{ route('settlementtravel.add_cash_advance') }}`,
        type: 'POST',
        dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: function (data, status) {
            $('#loader').addClass('hidden');
            swal({
                title: "Success!",
                text: data.message,
                icon: "success",
            }).then(() => {
                loadModalData(current_id_cash_advance);
            });
        },
        error: function(error) {
            $('#loader').addClass('hidden');
            swal({
                title: "Error!",
                text: error,
                icon: "error",
            });
        }
    })
});

$(document).on('click', '#submitCashAdvanceForm', function () {
    $('#cashAdvanceForm').submit();
});

$(document).on("click", '#submitApplyPayment', function (event) {
    event.preventDefault();
    $.ajax({
        url: `{{ route('settlementtravel.apply-payment') }}`,
        type: 'POST',
        // dataType: "JSON",
        // data: new FormData($('#applyPaymentForm')[0]),
        data: {
            _token: '{{ csrf_token() }}',
            id_cash_payment: $('#apply_payment_id_cash_payment').val(),
            payment_settlement: $('#payment_settlement').val()
        },
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: function (data, status) {
            $('#loader').addClass('hidden');
            swal({
                title: "Success!",
                text: data.message,
                icon: "success",
            }).then(() => {
                $('#modal_form_apply_payment').modal('hide');
                loadModalData(current_id_cash_advance);
            });
        },
        error: function(error) {
            $('#loader').addClass('hidden');
            swal({
                title: "Error!",
                text: error,
                icon: "error",
            });
        }
    })
});

$(document).on('click', '.button-refund-payment-status', function () {
    $.ajax({
        url: "{{ route('settlementtravel.approval.refund.finance-action') }}",
        data: {
            action: 'payment_status',
            payment_status: $(this).parent().find('.refund-payment-status').val(),
            id_refund: $(this).parent().find('.refund-payment-status').attr('id-refund'),
            _token: "{{ csrf_token() }}"
        },
        method: "POST",
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: () => {
            $('#loader').addClass('hidden');
            loadModalData(current_id_cash_advance);
        },
        error: () => {
            $('#loader').addClass('hidden');
            swal({
                title: "Error",
                icon: "error",
                text: "Error saving data!"
            });
        }
    })
})

function approveOrReject(id_settlement_expense, action, notes, approval_price) {
    $.ajax({
        url: '{{ route("settlementtravel.approval.finance-action") }}',
        data: {
            id_settlement_expense,
            action,
            notes,
            approval_price,
            _token: "{{ csrf_token() }}"
        },
        method: "POST",
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: (data) => {
            $('#loader').addClass('hidden');
            loadModalData(current_id_cash_advance)
        },
        error: (error) => {
            $('#loader').addClass('hidden');
            swal({
                icon: 'error',
                title: 'Error',
                text: error.responseJSON.message,
            });
        }
    })
}

$(document).on('click', '.button-action-settlement', function () {
    let idSettlement = $(this).attr('id-settlement');
    let action = $(this).attr('action');
    let notes = $(this).parent().parent().find('.settlement-notes').val();
    let approvalPrice = $(this).parent().parent().find('.settlement-approval-price').val();
    if(action == 'reject') {
        swal({
            icon: 'warning',
            title: 'Konfirmasi Reject',
            text: 'Apakah anda yakin untuk menolak klaim pengajuan ini?',
            buttons: ['Batal', 'Ya, Tolak'],
        }).then((confirm) => {
            if(!confirm) return;
            approveOrReject(idSettlement, action, notes, approvalPrice);
        })
    } else {
        approveOrReject(idSettlement, action, notes, approvalPrice);
    }
});

$(document).on('click', '#button-add-payment', function () {
    let clone = $('#table-payment-sample').clone();
    clone.find('.payment-bank-to-account').parent().html(`
        <select name="payment[0][bank_to_account]" class="form-control form-control-sm payment-bank-to-account" style="width:175px;">
        </select>
        <span class="invalid-feedback" role="alert" id="payment_0x_bank_to_accountError">
            <strong></strong>
        </span>
    `);
    clone.find('.payment-bank-to-account').prepend('<option></option>').select2({
        data: [{ id: current_payment_bank_to.id_bank_employee, text: `${current_payment_bank_to.bank_name} ${current_payment_bank_to.bank_account}` }],
        allowClear: true,
        placeholder: 'Select Bank Account'
    })
    clone.appendTo('#table_payment');
    $('#table_payment').children().each((i, element) => {
        let index = i+1;
        $(element).find('.payment-no').attr('id', 'payment-no-' + i);
        $(element).find('.payment-no').text(index);
        $(element).find('.id-payment').attr('name', `payment[${i}][id_payment]`);
        $(element).find('.payment-cash-advance-ref').val($('#cash_advance_reference').val());
        $(element).find('.payment-cash-advance-ref').attr('name', `payment[${i}][cash_advance_ref]`);
        $(element).find('.payment-notes').attr('name', `payment[${i}][notes]`);
        $(element).find('.payment-qty').attr('name', `payment[${i}][qty]`);
        $(element).find('.payment-price').attr('name', `payment[${i}][price]`);
        $(element).find('.payment-total').attr('name', `payment[${i}][total]`);
        $(element).find('.payment-bank-from-account').select2({
            data: global_company_allowed_bank,
        });
        $(element).find('.payment-bank-from-account').attr('name', `payment[${i}][bank_from_account]`);
        $(element).find('.payment-bank-to-account').attr('name', `payment[${i}][bank_to_account]`);
        $(element).find('.payment-currency').select2({
            data: global_currencies
        }).val(global_company_currency.id).trigger('change');
        $(element).find('.payment-currency').attr('id', `payment-currency-${i}`);
        $(element).find('.payment-currency').attr('name', `payment[${i}][currency]`);
        $(element).find('.payment-currency-rate').val(1);
        $(element).find('.payment-currency-rate').attr('id', `payment-currencyrate-${i}`);
        $(element).find('.payment-currency-rate').attr('name', `payment[${i}][currency_rate]`);
        $(element).find('.payment-total-amount').attr('name', `payment[${i}][total_amount]`);
        $(element).find('.payment-total-applied-amount').attr('name', `payment[${i}][total_applied_amount]`);
        $(element).find('.payment-total-applied-amount').attr('readonly', true);
        $(element).find('.payment-attachment').attr('name', `payment[${i}][attachment]`);
        $(element).find('.payment-status').attr('name', `payment[${i}][payment_status]`);
        $(element).find('.invalid-feedback').each((j, errElement) => {
            $(errElement).attr('id', $(errElement).attr('id').replaceAll('0x', i));
        });
        $(element).find('.payment-status').select2({
            data: [
                {id: "Paid", text: "Paid"},
                {id: "Not_Paid", text: "Not Paid"},
                // {id: "Partially_Paid", text: "Partially Paid"},
            ]
        });

        if(!$(element).find('.id-payment').val()) {
            $(element).find('.payment-action').html(`
                <button type="button" class="btn btn-danger payment-delete" id="payment-delete-${i}"><i class="fas fa-trash"></i></button>
            `);
        } else {

        }
    });
    
});
//
@if($page == "settlement_clearing")
    $('#status').select2({
        data: [
            { id: 'clear', text: 'Clear'},
            { id: 'not_clear', text: 'Not Clear' },
            { id: "all", text: "All" },
        ]
    }).val('not_clear').trigger('change');

    $(document).on('change', '#status', function () {
        dataTable.ajax.url(`{{ @$fas ? route('settlementtravel.approval.fas') : route('settlementtravel.approval.finance') }}?status=${$(this).val()}`).load();
    });
@elseif($page == "expense_payment_finance")
    $('#is_paid_by_finance').select2({
        data: [
            { id: 'FALSE', text: 'Unpaid' },
            { id: 'TRUE', text: 'Paid'},
        ]
    }).val('FALSE').trigger('change');

    $(document).on('change', '#is_paid_by_finance', function () {
        dataTable.ajax.url(`{{ route('settlementtravel.payment.finance') }}?is_paid_by_finance=${$(this).val()}&range=${$('#filter_date').val()}`).load();
    });
@endif
$(document).on('click', '.nav-item', function () {
    if($(this).attr('id') == "tab-settlement") {
        $('#settlement-total-footer').text('Total Settlement: ' + Intl.NumberFormat('id', {
            currency: 'IDR',
            style: 'currency'
        }).format(total_visible_settlement));
        $('#chief-approved-settlement-total-footer').text('Total Chief Approval: ' + Intl.NumberFormat('id', {
            currency: 'IDR',
            style: 'currency'
        }).format(chief_approved_total_visible_settlement));
        $('#finance-approved-settlement-total-footer').text('Total Finance Approval: ' + Intl.NumberFormat('id', {
            currency: 'IDR',
            style: 'currency'
        }).format(finance_approved_total_visible_settlement));
    } else {
        $('#settlement-total-footer').empty();
        $('#chief-approved-settlement-total-footer').empty();
        $('#finance-approved-settlement-total-footer').empty();
    }
});

function sendBillingLetter(idCashAdvance) {
    swal({
        title: 'Confirm Send Billing Letter',
        text: 'Employee will receive payment notification and due date will be counted from today.',
        buttons: ['Cancel', 'Send Letter']
    }).then((action) => {
        if(action === true) {
            $.ajax({
                url: "{{route('settlementtravel.send_bill')}}",
                type: 'POST',
                data: {
                    id_cash_advance: idCashAdvance,
                    _token: '{{csrf_token()}}'
                },
                beforeSend: () => $('#loader').removeClass('hidden'),
                success: (res) => {
                    $('#loader').addClass('hidden');
                    swal({
                        icon: 'success',
                        text: res.message,
                    }).then(() => {
                        loadModalData(idCashAdvance);
                    });
                },
                error: (err) => {
                    $('#loader').addClass('hidden');
                    swal({
                        icon: 'error',
                        text: err.responseJSON.message,
                    });
                }
            })
        }
    })
}

function getBillingLetter(idCashAdvance) {
    window.open(`{{route('settlementtravel.print_bill')}}?id_cash_advance=${idCashAdvance}`, Math.random());
}

$(document).on('click', '#view-attachment-settlement', function() {
    // $('#attachment-settlement-pdf').attr('src', $(this).attr('path'));
    // $('#attachment-settlement-modal').modal('show');
    window.open(`${$(this).attr('path')}`, "_blank");
})

function manualClearing() {
    $.ajax({
        url: "{{ route('settlementtravel.manual_clearing') }}",
        type: 'POST',
        data: {
            id_cash_advance: current_id_cash_advance,
            _token: "{{ csrf_token() }}",
        },
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: (res) => {
            $('#loader').addClass('hidden');
            loadModalData(current_id_cash_advance);
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
}

$('#advanced').click(function(){
  $('.cf').select2({width:'100%'});
  if($("#cf").css('display') == 'none'){
    $("#cf").show("slow");
  }
  else {
    $("#cf").hide("slow");
  }   
});

$(document).on('click', '.button-print', function () {
    let idCashAdvance = $(this).attr('id-cash-advance');
    let url = "{{route('settlementtravel.print')}}?id_cash_advance="+idCashAdvance;
    window.open(url, '_blank').focus();
})

// $(document).on('click', '.div_company_full', function (element) {
//     console.log('click', element.target, $(element.target));
//     console.log($(element.target).find('.select2-results__option'));
// })
</script>


@endsection