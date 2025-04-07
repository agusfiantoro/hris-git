@extends('adminlte::page')
@section('title', 'Settlement Travel Approval')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Settlement Travel Approval
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    <select name="approved" id="approved" class="form-control form-control-sm" style="width:125px;">
                    </select>
                    <button type="button" class="new btn btn-sm btn-success" onclick="$('#settlementtravel_table').DataTable().ajax.reload()"><i class="fas fa-filter"></i> Filter</button>
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
                        <!-- <th>Request by</th> -->
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
<div class="modal fade" id="modal_form_settlement" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                <p class="settlement-description" style="text-transform:uppercase;"></p>
                                {{-- <input type="text" name="settlement[0][description]" class="settlement-description form-control form-control-sm" style="text-transform:uppercase;"> --}}
                                <span class="invalid-feedback description-error" role="alert" id="settlement_0_descriptionError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-qty"></p>
                                {{-- <input type="text" name="settlement[0][qty]" class="settlement-qty form-control form-control-sm"> --}}
                                <span class="invalid-feedback qty-error" role="alert" id="settlement_0_qtyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="hideable">
                                <select name="settlement[0][unit]" class="settlement-unit form-control form-control-sm" style="width:100%;">
                                </select>
                                <span class="invalid-feedback qty-error" role="alert" id="settlement_0_qtyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="hideable">
                                <select name="settlement[0][currency]" class="settlement-currency form-control form-control-sm" style="width:170px;">
                                </select>
                                <span class="invalid-feedback currency-error" role="alert" id="settlement_0_currencyError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="hideable">
                                <p class="settlement-currency-rate"></p>
                                {{-- <input name="settlement[0][currency_rate]" class="settlement-currency-rate form-control form-control-sm" style="width:100%" readonly> --}}
                                <span class="invalid-feedback currency-rate-error" role="alert" id="settlement_0_currency_rateError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-price" style="width:100%;"></p>
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
                            {{-- <td class="">
                                <span class="settlement-attachment-download" style="display:none;">
                                    <a href="#" target="_blank"></a>
                                </span>
                                <span class="invalid-feedback attachment-error" role="alert" id="settlement_0_attachmentError">
                                    <strong></strong>
                                </span>
                            </td> --}}
                            {{-- <td>
                                <input type="text" name="settlement[0][budget]" class="settlement-budget form-control form-control-sm" style="width:100px" disabled>
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
                                <input type="text" name="settlement[0][chief_total_approval]" class="settlement-chief-total-approval form-control form-control-sm" style="width:100px" readonly>
                                <span class="invalid-feedback chief-total-approval-error" role="alert" id="settlement_0_chief_total_approvalError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="hideable">
                                <input type="text" name="settlement[0][approval_price]" class="settlement-approval-price form-control form-control-sm" style="width:100px">
                                <span class="invalid-feedback approval-price-error" role="alert" id="settlement_0_approval_priceError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="hideable">
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
                                <input readonly name="payment[0][cash_advance_ref]" class="form-control form-control-sm payment-cash-advance-ref">
                            </td>
                            <td>
                                <select name="payment[0][bank_from_account]" class="form-control form-control-sm payment-bank-from-account">
                                </select>
                            </td>
                            <td>
                                <input name="payment[0][notes]" class="form-control form-control-sm payment-notes">
                            </td>
                            <td>
                                <select name="payment[0][currency]" class="form-control form-control-sm payment-currency" id="payment-currency-0">
                                </select>
                            </td>
                            <td>
                                <input name="payment[0][currency_rate]" class="form-control form-control-sm payment-currency-rate" id="payment-currencyrate-0">
                            </td>
                            <td>
                                <input name="payment[0][total_amount]" class="form-control form-control-sm payment-total-amount">
                            </td>
                            <td>
                                <input name="payment[0][total_applied_amount]" class="form-control form-control-sm payment-total-applied-amount">
                            </td>
                            <td>
                                {{-- <input type="file" name="payment[0][attachment]" class="form-control-file form-control-sm payment-attachment"> --}}
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
                                    <input disabled id="total_cash_request_amount" name="total_cash_request_amount" class="form-control form-control-sm total-cash-request-amount" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="total_travel_request_amountError">
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
                        </div>
                        <div class="col-md-6">
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
                                <label class="col-sm-4 col-form-label">Accounting Date</label>
                                <div class="col-sm-8">
                                    <input disabled id="accounting_date" name="accounting_date" class="form-control form-control-sm accounting-date" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="accounting_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
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
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Currency Settlement</label>
                                <div class="col-sm-8">
                                    <select disabled id="currency_settlement" name="currency_settlement" class="form-control form-control-sm currency-settlement" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="currency_settlementError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Total Settlement</label>
                                <div class="col-sm-8">
                                    <input disabled id="total_settlement" name="total_settlement" class="form-control form-control-sm total-settlement" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="total_settlementError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Total Untaxed Amount</label>
                                <div class="col-sm-8">
                                    <input disabled id="total_base_currency_tax_amount" name="total_base_currency_tax_amount" class="form-control form-control-sm total-base-currency-tax-amount" style="width: 100%;">
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
                                <label class="col-sm-4 col-form-label">Clearing Amount</label>
                                <div class="col-sm-8">
                                    <input disabled id="clearing_amount" name="clearing_amount" class="form-control form-control-sm clearing-amount" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="clearing_amountError">
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
                                <label class="col-sm-4 col-form-label">Settlement Status</label>
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
                                    <h4 class="mx-2 mt-3">Settlement</h4>
                                    {{-- <button type="button" class="btn btn-sm btn-default mx-2 mb-1" id="toggleSettlementColumn" state="off" style="float: right;">Show Details Column</button> --}}
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:100%" id="settlement-table">
                                            
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
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    <th>Chief Approval Price</th>
                                                    <th>Finance Approval Price</th>
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
                                    {{-- <button type="button" role="button" disabled class="btn btn-sm btn-primary mb-2" style="float: right;" id="button-add-refund">
                                        <i class="fa fa-plus"></i> Add Payment
                                    </button> --}}
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Cash Advance</th>
                                                    <th>Bank From</th>
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
                <div style="width:90%" class="float-left">
                    <b id="settlement-total-footer"></b>
                </div>
                <button class="btn btn-sm btn-info action_submit" id="submitForm" name="submitForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit"></span></button>&nbsp;
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_cashadvance" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Cash Advance</h5>
            </div>
            <div class="modal-body">
                <form method="POST" id="cashAdvanceForm" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Official Travel</label>
                                <div class="col-sm-8">
                                    <select disabled id="id_official_travel" name="id_official_travel" class="form-control form-control-sm id_official_travel" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_official_travelError">
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
                                <label class="col-sm-4 col-form-label">Request Currency</label>
                                <div class="col-sm-8">
                                    <input disabled id="request_currency" name="request_currency" class="form-control form-control-sm request_currency" style="width: 100%;">
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
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <textarea disabled class="form-control form-control-sm cash-advance-notes" id="cash_advance_notes" rows="4" name="vash_advance_notes" style="text-transform: uppercase;"></textarea>
                                    <span class="invalid-feedback" role="alert" id="cash_advance_notesError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
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
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Payment Date</label>
                                <div class="col-sm-8">
                                    <input disabled id="payment_date" name="payment_date" class="form-control form-control-sm payment-date" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="payment_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Accounting Date</label>
                                <div class="col-sm-8">
                                    <input disabled id="accounting_date" name="accounting_date" class="form-control form-control-sm accounting-date" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="accounting_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
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
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Currency Settlement</label>
                                <div class="col-sm-8">
                                    <select disabled id="currency_settlement" name="currency_settlement" class="form-control form-control-sm currency-settlement" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="currency_settlementError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Total Settlement</label>
                                <div class="col-sm-8">
                                    <input disabled id="total_settlement" name="total_settlement" class="form-control form-control-sm total-settlement" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="total_settlementError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Total Untaxed Amount</label>
                                <div class="col-sm-8">
                                    <input disabled id="total_untaxed_amount" name="total_untaxed_amount" class="form-control form-control-sm total-untaxed-amount" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="total_untaxed_amountError">
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
                                <label class="col-sm-4 col-form-label">Clearing Amount</label>
                                <div class="col-sm-8">
                                    <input disabled id="clearing_amount" name="clearing_amount" class="form-control form-control-sm clearing-amount" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="clearing_amountError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-8">
                                    <input type="file" disabled id="cash_advance_attachment" name="cash_advance_attachment" class="form-control-file form-control-sm cash-advance-attachment" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="cash_advance_attachmentError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Settlement Status</label>
                                <div class="col-sm-8">
                                    <select disabled id="settlement_status" name="settlement_status" class="form-control form-control-sm settlement-status" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="settlement_statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button class="btn btn-sm btn-info action_submit" id="submitCashAdvanceForm" name="submitCashAdvanceForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit_cash_advance"></span></button>&nbsp; --}}
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
var current_id_cash_advance = null;
var total_visible_settlement = 0;
var current_settlement_page = 0;
$('#label_button_action_submit').text("Save");
$('#label_button_action_submit_cash_advance').text("Save");
$('#submitForm').addClass("hidden");

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

function getSettlements(idCashAdvance, page) {
    let pageLength = 20;
    $('#settlement-table').DataTable({
        pageLength: pageLength,
        displayStart: page*pageLength,
        processing: true,
        serverSide: true,
        destroy: true,
        ajax: {
            url: "{{ route('settlementtravel.get-settlements') }}",
            data: {
                id_cash_advance: idCashAdvance,
            },
            error: function (jqXHR, textStatus, errorThrown) {
                $('#settlement-table').DataTable().ajax.reload();
            },
        },
        columns: [
            {
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No' },
            { data: 'settlement_date', name: 'Date', title: 'Date' },
            { data: 'product_description', title: 'Category' },
            { data: 'region', title: 'Region' },
            { data: 'desc_branch', title: 'Branch' },
            { data: 'description', title: 'Description' },
            { data: 'qty', title: 'Qty' },
            { data: 'unit_price', title: 'Price', render: (data) => Intl.NumberFormat('id-ID').format(data) },
            { data: 'total_amount', title: 'Total', render: (data) => Intl.NumberFormat('id-ID').format(data) },
            { 
                data: 'approval_price_by_chief', 
                title: 'Chief Approval Price',
                render: function(data, type, row) {
                    if(row.is_verified_by_chief == null) {
                        return `<input type="text" id="settlement-${row.id_settlement_expense}-approval-price" class="form-control form-control-sm settlement-chief-approval-price" value="${data > 0 ? data : row.unit_price}">`
                    }
                    return Intl.NumberFormat('id-ID').format(data);
                }
            },
            { 
                data: 'approval_price_by_finance', 
                title: 'Finance Approval Price',
                render: (data) => Intl.NumberFormat('id-ID').format(data)     
            },
            { 
                data: 'notes', 
                title: 'Notes', 
                render: function(data, type, row) {
                    if(row.is_verified_by_chief == null) {
                        return `<input type="text" id="settlement-${row.id_settlement_expense}-notes" class="form-control form-control-sm" value="${data ? data : ''}">`
                    }
                    return data;
                } 
            },
            { 
                data: 'id_settlement_expense', 
                title: 'Action', 
                render: function (data, type, row) {
                    if(row.is_verified_by_chief == false || row.is_verified_by_finance == false) {
                        return `<span class="badge badge-danger">Rejected</span>`;
                    } else if(row.is_verified_by_chief == true) {
                        let badge = `<span class="badge badge-success">Approved by Chief</span>`;
                        if(row.is_verified_by_finance == true) {
                            badge += `<span class="badge badge-success">Approved by Finance</span>`
                            
                        }
                        return badge;
                    }  else {
                        // $(element).find('.settlement-notes').attr('readonly', false);
                        return `
                            <button type="button" class="btn btn-sm btn-success mr-2 mb-2 button-action-settlement" action="approve" id-settlement="${data}"><span class="fas fa-check"></span> Approve</button>
                            <button type="button" class="btn btn-sm btn-info button-action-settlement" action="revise" id-settlement="${data}"><span class="fas fa-calendar"></span> Revise</button>
                            <button type="button" class="btn btn-sm btn-danger button-action-settlement" action="reject" id-settlement="${data}"><span class="fas fa-times"></span> Reject</button>
                        `;
                    }
                }
            },
        ],
    })
}

$(function () {
    $('#settlementtravel_table').DataTable({
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
        url: "{{ route('settlementtravel.approval.chief') }}",
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
        { data: 'payment_status', name: 'payment_status', className: 'text-center', render: function ( data, type, row ) {	
				if(row.payment_status == 'Not_Paid'){
					return '<span class="badge badge-secondary" style="padding:5px;font-size:12px;">Not Paid</span>';
				}
				else if(row.code_app_status == 'Approved'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(row.code_app_status == 'Hold'){
					return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
                else if(row.code_app_status == 'Pending'){
					return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
                else if(row.code_app_status == 'Cancel'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
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

$('#approved').select2({
    data: [
        { id: 'true', text: 'Approved'},
        { id: 'false', text: 'Not Approved' }
    ]
}).val('false').trigger('change');

$(document).on('change', '#approved', function () {
    $('#settlementtravel_table').DataTable().ajax.url(`{{ route('settlementtravel.approval.chief') }}?approved=${$(this).val()}`).load();
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
// $(document).on('change', '.settlement-qty', (element) => {
//     changeTotal(element)
// });
// $(document).on('change', '.settlement-currency-rate', (element) => {
//     changeTotal(element)
// });
// $(document).on('change', '.settlement-currency', (element) => {
//     changeTotal(element)
// });

function handleValidationError(errors) {
    Object.keys(errors).forEach(function (key) {
        var key_temp = key.replaceAll(".", "_");
        // console.log(key_temp);
        $("#" + key_temp).addClass("is-invalid");
        $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
    });
}

$(document).on('click', '#toggleSettlementColumn', function() {
    if($(this).attr('state') == 'off') {
        $(this).text('Hide Details Column');
        $(this).attr('state', 'on');
        $('.hideable').show();
    } else {
        $(this).text('Show Details Column');
        $(this).attr('state', 'off');
        $('.hideable').hide();
    }
});
$('.hideable').hide();

$(document).on("submit", '#travelForm', function (event) {
    let formData = $('#travelForm').serialize();
    event.preventDefault();
    $.ajax({
        url: `{{ route('settlementtravel.update') }}`,
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
                window.location.reload();
            });
        },
        error: function (xhr, desc, err)
        {
            $('#loader').addClass('hidden');
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

$(document).on('change', '.settlement-chief-approval-price', function () {
    $(this).val(formatRupiah(parseInt(unformatRupiah($(this).val()))));
    let approval_amount = unformatRupiah($(this).val());
    let qty = $(this).parent().parent().find('.settlement-qty').text();
    let product_max_price = unformatRupiah($(this).parent().parent().find('.settlement-budget').val());
    let rate = unformatRupiah($(this).parent().parent().find('.settlement-currency-rate').text());
    if(approval_amount > product_max_price) {
        $(this).addClass('text-danger');
    } else {
        $(this).removeClass('text-danger');
    }
    $(this).parent().parent().find('.settlement-chief-total-approval').val(formatRupiah(parseInt(approval_amount * qty * rate)));
});

function loadModalData(idCashAdvance) {

    $.ajax({
        url: '{{ route("settlementtravel.approval.data") }}',
        data: {
            id_cash_advance: idCashAdvance,
            type: "chief",
            ignore_is_validate: 1
        },
        beforeSend: () => {
            total_visible_settlement = 0;
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

            if(response.cash_advance.attachment_settlement) {
                $('.attachment-settlement').hide();
                $('#view-attachment-settlement').attr('path', `/project/storage/app/public/${response.cash_advance.attachment_settlement}`)
                $('#view-attachment-settlement').show();
            } else {
                $('.attachment-settlement').show();
                $('#view-attachment-settlement').hide();
            }

            $('#id_cash_advance').val(response.cash_advance.id_cash_advance);
            $('#cash_advance_reference').val(response.cash_advance.reference_number);
            $('#request_total_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.hca_total_expense_request_amount)));
            $('#total_cash_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_cash_request_amount)));
            $('#total_travel_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_travel_request_amount)));
            $('#reference_number').val(response.cash_advance.travel_reference_number);
            $('#transaction_date').val(response.cash_advance.transaction_date);
            $('#cash_advance_notes').val(response.cash_advance.reason_notes);
            $('#clearing_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_clearing_amount)));
            $('#payment_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_payment_amount)));
            $('#total_settlement').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_settlement_amount)));
            $('#total_base_currency_tax_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_tax_amount)));
            $('#difference_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_difference_amount)));
            $('#total_taxed_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_tax_amount)));
            $('#maximum_clearing_date').val(response.cash_advance.maximum_clearing_date).attr('disabled', true);

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
            $('#request_currency').empty().select2({
                data: response.currencies.all
            }).val(response.cash_advance.id_currency_cash_advance).trigger('change');
            $('#settlement_status').empty().select2({
                data: [
                    {id: "Not_Clear", text: "Not Clear"},
                    {id: "Clear", text: "Clear"}
                ]
            }).val(response.cash_advance.settlement_status).trigger('change');
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
            $('#table_refund').empty();
            $('#table_payment').empty();
            $('#table_settlement_summary').empty();

            if(response.transport_accommodation.length < 1) {
                $('.transaco-table').addClass('hidden');
            } else {
                $('.transaco-table').removeClass('hidden');
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

            getSettlements(response.cash_advance.id_cash_advance, current_settlement_page);
            // response.settlement.forEach((settlement, i) => {
            //     total_visible_settlement += parseInt(settlement.total_amount);
                
            //     $('#currency_settlement').select2({
            //         data: global_currencies
            //     }).val(settlement.id_currency_settlement_expense).trigger('change').attr('disabled', true);

            //     let clone = $('#table-settlement-sample').clone();
            //     clone.appendTo('#table_settlement');
            //     $(clone).children().each((j, element) => {
            //         let index = i+1;
            //         $(element).find('input').attr('readonly', true);
            //         $(element).find('input[type=file]').attr('disabled', true);
            //         $(element).find('select').prop('disabled', true);
            //         $(element).find('.id-settlement').val(settlement.id_settlement_expense);
            //         $(element).find('.id-settlement').attr('name', `settlement[${i}][id_settlement]`);
            //         $(element).find('.id-settlement').attr('approval', settlement.is_verified_by_chief == true ? "true" : settlement.is_verified_by_chief == false ? "false" : "null");
            //         $(element).find('.id-settlement').attr('finance-approval', settlement.is_verified_by_finance == true ? "true" : settlement.is_verified_by_finance == false ? "false" : "null");
            //         $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
            //         $(element).find('.settlement-no').text(index);
            //         $(element).find('.settlement-start-end').attr('id', 'settlement-startend-' + i);
            //         $(element).find('.settlement-start-end').attr('name', `settlement[${i}][start_end]`);
            //         $(element).find('.settlement-start-end').val(settlement.settlement_date);
            //         $(element).find('.settlement-product').attr('id', 'settlement-product-' + i);
            //         $(element).find('.settlement-product').attr('name', `settlement[${i}][product]`);
            //         $(element).find('.product-error').attr('id', `settlement_${i}_productError`);
            //         $(element).find('.settlement-region').attr('id', 'settlement-region-' + i);
            //         $(element).find('.settlement-region').attr('name', `settlement[${i}][region]`);
            //         $(element).find('.region-error').attr('id', `settlement_${i}_regionError`);
            //         $(element).find('.settlement-branch').attr('id', 'settlement-branch-' + i);
            //         $(element).find('.settlement-branch').attr('name', `settlement[${i}][branch]`);
            //         $(element).find('.branch-error').attr('id', `settlement_${i}_branchError`);
            //         $(element).find('.settlement-description').attr('id', 'settlement-description-' + i);
            //         // $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
            //         $(element).find('.settlement-description').text(settlement.description);
            //         $(element).find('.description-error').attr('id', `settlement_${i}_descriptionError`);
            //         $(element).find('.settlement-notes').attr('id', 'settlement-notes-' + i);
            //         $(element).find('.settlement-notes').attr('name', `settlement[${i}][notes]`);
            //         $(element).find('.settlement-notes').val(settlement.notes);
            //         $(element).find('.settlement-qty').attr('id', 'settlement-qty-' + i);
            //         $(element).find('.settlement-qty').attr('name', `settlement[${i}][qty]`);
            //         $(element).find('.settlement-qty').text(settlement.qty);
            //         $(element).find('.qty-error').attr('id', `settlement_${i}_qtyError`);
            //         $(element).find('.settlement-unit').attr('id', 'settlement-unit-' + i);
            //         $(element).find('.settlement-unit').attr('name', `settlement[${i}][unit]`);
            //         $(element).find('.settlement-unit').val(settlement.qty);
            //         $(element).find('.unit-error').attr('id', `settlement_${i}_unitError`);
            //         $(element).find('.settlement-currency').attr('id', 'settlement-currency-' + i);
            //         $(element).find('.settlement-currency').attr('name', `settlement[${i}][currency]`);
            //         $(element).find('.settlement-currency-rate').attr('id', 'settlement-currencyrate-' + i);
            //         $(element).find('.settlement-currency-rate').attr('name', `settlement[${i}][currency_rate]`);
            //         $(element).find('.settlement-chief-approval-price').attr('id', 'settlement-chiefapprovalprice-' + i);
            //         $(element).find('.settlement-chief-approval-price').attr('name', `settlement[${i}][chief_approval_price]`);
            //         $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
            //         $(element).find('.settlement-price').attr('id', 'settlement-price-' + i);
            //         $(element).find('.settlement-price').attr('name', `settlement[${i}][price]`);
            //         $(element).find('.settlement-price').text(formatRupiah(parseInt(settlement.unit_price)));
            //         $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
            //         $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
            //         $(element).find('.settlement-total').text(response.currencies.company.currency_symbol + formatRupiah(parseInt(settlement.total_base_currency_amount)));
            //         $(element).find('.settlement-budget').val(formatRupiah(parseInt(settlement.product_max_price)));
            //         $(element).find('.settlement-approval-price').attr('id', 'settlement-approvalprice-' + i);
            //         $(element).find('.settlement-approval-price').attr('name', `settlement[${i}][approval_price]`);
            //         $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
            //         $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief))).trigger('change');
            //         if(settlement.is_verified_by_chief == null) {
            //             $(element).find('.settlement-chief-approval-price').attr('readonly', false).attr('disabled', false);
            //             $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief > 0 ? settlement.approval_price_by_chief : settlement.unit_price)))
            //         }
            //         if(unformatRupiah($(element).find('.settlement-chief-approval-price').val()) > settlement.product_max_price) {
            //             $(element).find('.settlement-chief-approval-price').addClass('text-danger');
            //         }
            //         $(element).find('.settlement-total-approval').attr('id', 'settlement-totalapproval-' + i);
            //         $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
            //         $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
            //         $(element).find('.settlement-attachment').attr('id', 'settlement-attachment-' + i);
            //         $(element).find('.settlement-attachment').attr('name', `settlement[${i}][attachment]`);
            //         if(settlement.attachment) {
            //             $(element).find('.settlement-attachment-download').css('display', 'block');
            //             $(element).find('.settlement-attachment-download').children().attr('href', '/project/storage/app/public/'+settlement.attachment);
            //             $(element).find('.settlement-attachment-download').children().text('Download');
            //         }
            //         $(element).find('.settlement-unit').select2({
            //             data: [{id: settlement.id_uom, text: settlement.desc_uom}]
            //         }).val(settlement.id_uom).trigger('change').attr('readonly', true);
            //         $(element).find('.settlement-product').css('width', '100%');
            //         $(element).find('.settlement-product').select2({
            //             data: global_products,
            //             placeholder: "Select Product",
            //             allowClear: true
            //         }).val(settlement.id_product).trigger('change');
            //         $(element).find('.settlement-region').select2({
            //             data: response.regions
            //         }).attr('id-branch', settlement.id_branch).attr('id-region', settlement.id_region);
            //         $(element).find('.settlement-region').val(settlement.id_region).trigger('change');
            //         $(element).find('.settlement-currency').select2({
            //             data: global_currencies
            //         }).val(settlement.id_currency_settlement_expense).trigger('change');
            //         $(element).find('.settlement-currency-rate').text(formatRupiah(parseInt(settlement.currency_rate_settlement_expense)));
            //     })
            //     $('#table_settlement').children().each((i, element) => {
            //         $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
            //         $(element).find('.settlement-no').text(i+1);
            //         $(element).find('.settlement-currency').trigger('change');

            //         if(!$(element).find('.id-settlement').attr('approval')) {
            //             $(element).find('.settlement-action').html(`
            //                 <button class="btn btn-sm btn-success"><span class="fas fa-check"></span></button>
            //             `);
            //         } else if($(element).find('.id-settlement').attr('approval') == "false" || $(element).find('.id-settlement').attr('finance-approval') == "false") {
            //             $(element).find('.settlement-action').html(`<span class="badge badge-danger">Rejected</span>`);
            //         } else if($(element).find('.id-settlement').attr('approval') == "true") {
            //             let badge = `<span class="badge badge-success">Approved by Chief</span>`;
            //             if($(element).find('.id-settlement').attr('finance-approval') == "true") {
            //                 badge += `<span class="badge badge-success">Approved by Finance</span>`
                            
            //             }
            //             $(element).find('.settlement-action').html(badge);
            //         }  else {
            //             $(element).find('.settlement-notes').attr('readonly', false);
            //             $(element).find('.settlement-action').html(`
            //                 <button type="button" class="btn btn-sm btn-success mr-2 mb-2 button-action-settlement" action="approve" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-check"></span> Approve</button>
            //                 <button type="button" class="btn btn-sm btn-info button-action-settlement" action="revise" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-calendar"></span> Revise</button>
            //                 <button type="button" class="btn btn-sm btn-danger button-action-settlement" action="reject" id-settlement="${$(element).find('.id-settlement').val()}"><span class="fas fa-times"></span> Reject</button>
            //             `);
            //         }
            //     });

            //     if(i+1 == response.settlement.length) {
            //         $('#tab-settlement').trigger('click');
            //     }
            // })

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
                    // $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
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
                    $(element).find('.settlement-chief-approval-price').attr('id', 'settlement-chiefapprovalprice-' + i);
                    $(element).find('.settlement-chief-approval-price').attr('name', `settlement[${i}][chief_approval_price]`);
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
                    $(element).find('.settlement-price').attr('id', 'settlement-price-' + i);
                    $(element).find('.settlement-price').attr('name', `settlement[${i}][price]`);
                    $(element).find('.settlement-price').text(formatRupiah(parseInt(settlement.unit_price)));
                    $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
                    $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
                    $(element).find('.settlement-total').text(response.currencies.company.currency_symbol + formatRupiah(parseInt(settlement.total_base_currency_amount)));
                    $(element).find('.settlement-budget').val(formatRupiah(parseInt(settlement.product_max_price)));
                    $(element).find('.settlement-approval-price').attr('id', 'settlement-approvalprice-' + i);
                    $(element).find('.settlement-approval-price').attr('name', `settlement[${i}][approval_price]`);
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
                    if(settlement.is_verified_by_chief == null) {
                        $(element).find('.settlement-chief-approval-price').attr('readonly', false).attr('disabled', false);
                        $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief ? settlement.approval_price_by_chief : settlement.unit_price)))
                    }
                    let approval_amount = settlement.approval_price_by_chief > 0 ? settlement.approval_price_by_chief : settlement.unit_price * settlement.currency_rate_settlement_expense;
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(approval_amount))).trigger('change');
                    if(approval_amount > settlement.product_max_price) {
                        $(element).find('.settlement-chief-approval-price').addClass('text-danger');
                    }
                    $(element).find('.settlement-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
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
                        data: global_products,
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

            response.refund.forEach((refund, i) => {
                let clone = $('#table-refund-sample').clone();
                clone.appendTo('#table_refund');
                // console.log(refund);
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('.id-refund').val(refund.id_cash_refund);
                    $(element).find('.id-refund').attr('name', `refund[${i}][id_refund]`);
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
                    $(element).find('.refund-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.refund-no').text(i+1);
                    // $(element).find('.refund-action').html(`
                    //     <button type="button" class="btn btn-danger refund-delete" id="refund-delete-${i}" refund-id="${$(element).find('.id-refund').val()}"><i class="fas fa-trash"></i></button>
                    // `);
                });
            });

            response.payment.forEach((payment, i) => {
                $('#payment_currency').select2({
                    data: global_currencies
                }).val(payment.id_currency_cash_payment).trigger('change').attr('disabled', true);

                let clone = $('#table-payment-sample').clone();
                clone.appendTo('#table_payment');
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
                    $(element).find('.payment-notes').val(payment.notes);
                    $(element).find('.payment-notes').attr('name', `payment[${i}][notes]`);
                    $(element).find('.payment-currency').select2({
                        data: global_currencies
                    }).val(payment.id_currency_cash_payment).trigger('change');
                    $(element).find('.payment-currency').attr('name', `payment[${i}][currency]`);
                    $(element).find('.payment-currency').attr('id', `payment-currency-${i}`);
                    $(element).find('.payment-currency-rate').val(payment.currency_rate_payment);
                    $(element).find('.payment-currency-rate').attr('name', `payment[${i}][currency_rate]`);
                    $(element).find('.payment-currency-rate').attr('id', `payment-curencyrate-${i}`);
                    $(element).find('.payment-total-amount').val(payment.total_amount);
                    $(element).find('.payment-total-amount').attr('name', `payment[${i}][total_amount]`);
                    $(element).find('.payment-total-applied-amount').val(payment.total_applied_amount);
                    $(element).find('.payment-total-applied-amount').attr('name', `payment[${i}][total_applied_amount]`);
                    $(element).find('.payment-status').select2({
                        data: [
                            {id: "Not_Paid", text: "Not Paid"},
                            // {id: "Partially_Paid", text: "Partially Paid"},
                            {id: "Paid", text: "Paid"}
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
                    $(element).find('input').attr('disabled', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    $(element).find('select').attr('disabled', true);
                    $(element).find('.payment-no').attr('id', 'payment-no-' + i);
                    $(element).find('.payment-no').text(i+1);
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
    current_settlement_page = 0;
    loadModalData(idCashAdvance);
});

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
                window.location.reload();
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

function approveOrReject(id_settlement_expense, action, notes, approval_price) {
    current_settlement_page = $('#settlement-table').DataTable().page();
    $.ajax({
        url: "{{ url('cash_advance/cash_advance/settlement_approval/approval') }}",
        data: {
            id_settlement_expense,
            action,
            approval_price,
            notes,
            _token: "{{ csrf_token() }}"
        },
        method: "POST",
        beforeSend: () => {
            $('#loader').removeClass('hidden');
        },
        success: (data) => {
            $('#loader').addClass('hidden');
            // console.log(data);
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
    // console.log($(this).parent().parent().find('.settlement-notes').val())
    let idSettlement = $(this).attr('id-settlement');
    let action = $(this).attr('action');
    let notes = $(`#settlement-${idSettlement}-notes`).val();
    let approvalPrice = unformatRupiah($(`#settlement-${idSettlement}-approval-price`).val());

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

    
})

$(document).on('click', '.nav-item', function () {
    if($(this).attr('id') == "tab-settlement") {
        $('#settlement-total-footer').text('Total Settlement: ' + Intl.NumberFormat('id', {
            currency: 'IDR',
            style: 'currency'
        }).format(total_visible_settlement));
    } else {
        $('#settlement-total-footer').empty();
    }
});

$(document).on('click', '#view-attachment-settlement', function() {
    // $('#attachment-settlement-pdf').attr('src', $(this).attr('path'));
    // $('#attachment-settlement-modal').modal('show');
    window.open(`${$(this).attr('path')}`, "_blank");
})

$('#advanced').click(function(){
  $('.cf').select2({width:'100%'});
  if($("#cf").css('display') == 'none'){
    $("#cf").show("slow");
  }
  else {
    $("#cf").hide("slow");
  }   
});

</script>


@endsection