@extends('adminlte::page')
@section('title', 'Settlement Travel')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
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
    .is-invalid {
        display: block!important;
    }

    .settlement-notes {
        word-wrap: break-word;
    }

    .settlement-qty {
        width: 70px!important;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Settlement Travel
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                {{-- <div class="card-tools">
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Official Travel</button>
                </div> --}}
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                {{-- <button id="addCashAdvance" type="button" class="btn btn-xs btn-success float-right">Add Declaration</button> --}}
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="settlementtravel_table" style="width: 100%;">
                    <thead>
                        <tr>
                        <!-- <th></th> -->
                        <th></th>
                        <th data-priority="7">No.</th>
                        <th data-priority="2">Reference Number</th>
                        <th data-priority="5">Letter Date</th>
                        <th>Travel Type</th>
                        <!-- <th>Request by</th> -->
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th data-priority="8">Destination</th>
                        <th data-priority="3">Chief Approval</th>
                        <th data-priority="4">Finance Approval</th>
                        <th data-priority="7">Submitted</th>
                        <th>Have Cash Advance</th>
                        <th>Settlement Clearance</th>
                        <th data-priority="1" align="center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="attachment-settlement-modal" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1675;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document" style="max-width:100%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Attachment</h5>
            </div>
            <div class="modal-body">
                <embed height="700" width="1000" type="application/pdf" src="#" id="attachment-settlement-pdf">
                    <p>PDF cannot be displayed.</p>
                </embed>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_settlement" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
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
                                <span class="invalid-feedback start-end-error" role="alert" id="settlement_x_start_endError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="hidden" name="settlement[0][id_settlement]" class="id-settlement">
                                <select name="settlement[0][product]" class="settlement-product form-control form-control-sm">
                                    <option selected></option>
                                </select>
                                <span class="invalid-feedback product-error" role="alert" id="settlement_x_productError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][region]" class="settlement-region form-control form-control-sm" style="width:100%">
                                    {{-- <option selected></option> --}}
                                </select>
                                <span class="invalid-feedback region-error" role="alert" id="settlement_x_regionError">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][branch]" class="settlement-branch form-control form-control-sm" style="width:100%" readonly>
                                    {{-- <option selected></option> --}}
                                </select>
                                <span class="invalid-feedback branch-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][description]" class="settlement-description form-control form-control-sm" style="text-transform:uppercase;width:100px;">
                                <span class="invalid-feedback description-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][qty]" class="settlement-qty form-control form-control-sm">
                                <span class="invalid-feedback qty-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][unit]" class="settlement-unit form-control form-control-sm" style="width:100%;">
                                </select>
                                <span class="invalid-feedback unit-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="settlement[0][currency]" class="settlement-currency form-control form-control-sm" style="width:170px;">
                                </select>
                                <span class="invalid-feedback currency-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="settlement[0][currency_rate]" class="settlement-currency-rate form-control form-control-sm" style="width:100px" readonly>
                                <span class="invalid-feedback currency-rate-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td style="min-width:140px;">
                                <input type="text" name="settlement[0][price]" class="settlement-price form-control form-control-sm" style="width:100px;float:left;">
                                <div style="width: 10%; float: right; display: none;" class="tooltip-budget">
                                    <a href="javascript:void(0)" tabindex="0" class="help_input" role="button" data-toggle="popover" data-trigger="focus" title="" 
                                        data-content="tooltip_budget" 
                                        id="cashadvance_0_help" data-original-title="Budget Information"><small id="cashadvance_0_help"><i class="fas fa-question-circle fa-lg"></i></small></a>
                                </div>
                                <span class="invalid-feedback price-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][total]" class="settlement-total form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback total-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            {{-- <td style="width:180px;">
                                <input type="file" name="settlement[0][attachment]" class="settlement-attachment form-control-file" style="width:180px;">
                                <span class="settlement-attachment-download" style="display:none;">
                                    <a href="#" target="_blank"></a>
                                </span>
                                <span class="invalid-feedback attachment-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td> --}}
                            <td>
                                <input type="text" name="settlement[0][chief_approval_price]" class="settlement-chief-approval-price form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback chief-approval-price-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][chief_total_approval]" class="settlement-chief-total-approval form-control form-control-sm" style="width:100px" disabled>
                                <span class="invalid-feedback chief-total-approval-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][approval_price]" class="settlement-approval-price form-control form-control-sm" style="width:100px;" disabled>
                                <span class="invalid-feedback approval-price-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="text" name="settlement[0][total_approval]" class="settlement-total-approval form-control form-control-sm" style="width:100px;" disabled>
                                <span class="invalid-feedback total-approval-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <p class="settlement-notes" style="width:120px"></p>
                                {{-- <input type="text" name="settlement[0][notes]" class="settlement-notes form-control form-control-sm" style="width:100px" readonly> --}}
                                <span class="invalid-feedback notes-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td class="settlement-action"></td>
                        </tr>
                        <tr id="table-refund-sample">
                            <td class="refund-no"></td>
                            <td>
                                <input type="hidden" name="refund[0][id_refund]" class="id-refund">
                                <select name="refund[0][bank_from]" class="form-control form-control-sm refund-bank-from">
                                </select>
                                <span class="invalid-feedback refund-bank-from-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="refund[0][bank_account]" class="form-control form-control-sm refund-bank-account">
                                <span class="invalid-feedback refund-bank-account-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <select name="refund[0][bank_to]" class="form-control form-control-sm refund-bank-to" style="width:200px;">
                                </select>
                                <span class="invalid-feedback refund-bank-to-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input name="refund[0][amount]" class="form-control form-control-sm refund-amount">
                                <span class="invalid-feedback refund-amount-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
                            <td>
                                <input type="file" name="refund[0][attachment]" class="form-control-file form-control-sm refund-attachment">
                                <span class="invalid-feedback refund-attachment-error" role="alert">
                                    <strong></strong>
                                </span>
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
                                    <input disabled id="total_travel_request_amount" name="total_travel_request_amount" class="form-control form-control-sm total-travel-request-amount" style="width: 100%;">
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
                                <label class="col-sm-4 col-form-label">Max Clearing Date</label>
                                <div class="col-sm-8">
                                    <input id="maximum_clearing_date" name="maximum_clearing_date" class="form-control form-control-sm payment-date" disabled style="width: 100%;">
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
                                    <select readonly id="currency_settlement" name="currency_settlement" class="form-control form-control-sm currency-settlement" style="width: 100%;">
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
                                <label class="col-sm-4 col-form-label">Total Base Currency Tax</label>
                                <div class="col-sm-8">
                                    <input disabled id="total_base_currency_tax" name="total_base_currency_tax" class="form-control form-control-sm total-base-currency-tax" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="total_base_currency_taxError">
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
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-8">
                                    <div class="custom-file" style="cursor:pointer;">
                                        <input type="file" name="attachment_settlement" class="custom-file-input" id="attachment_settlement" accept="application/pdf">
                                        <label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Format PDF, Max. 5 MB</i></label>
                                    </div>
                                    <span class="invalid-feedback" role="alert" id="attachment_settlementError">
                                        <strong></strong>
                                    </span>
                                    <button type="button" class="btn btn-sm btn-primary mt-1" id="view-attachment-settlement" style="display:none;">View</button>
                                </div>
                            </div>
                            <div class="row hidden" id="billing_letter_row">
                                <label class="col-sm-4 col-form-label">Billing Letter</label>
                                <div class="col-sm-8">
                                    <div id="billing_letter_container"></div>
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
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
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
                                    <button type="button" role="button" class="btn btn-sm btn-primary mb-2" style="float: right;" id="button-add-settlement">
                                        <i class="fa fa-plus"></i> Add Settlement
                                    </button>
                                    <h4 class="mx-2 mt-3">Settlement</h4>
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
                                                    <th style="width:90px;">Qty</th>
                                                    <th style="width:75px;">Unit</th>
                                                    <th style="width:120px;">Currency</th>
                                                    <th>Currency Rate</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    {{-- <th>Attachment</th> --}}
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
                                                    <th>Product</th>
                                                    <th>Region</th>
                                                    <th>Branch</th>
                                                    <th>Description</th>
                                                    <th style="width:90px;">Qty</th>
                                                    <th style="width:75px;">Unit</th>
                                                    <th style="width:120px;">Currency</th>
                                                    <th>Currency Rate</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                    {{-- <th>Attachment</th> --}}
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
                                </div>
                                <div class="tab-pane" id="tab-pane-3">
                                    {{-- Refund --}}
                                    <button type="button" role="button" class="btn btn-sm btn-primary mb-2" style="float: right;" id="button-add-refund">
                                        <i class="fa fa-plus"></i> Add Refund
                                    </button>
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
                    <input type="hidden" name="submit_type" id="submit-type">
                </form>
            </div>
            <div class="modal-footer">
                <div style="width:70%" class="float-left">
                    <b id="settlement-total-footer"></b>
                </div>
                <button class="btn btn-sm btn-info action_submit" id="submitForm" name="submitForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit"></span></button>&nbsp;
                <button class="btn btn-sm btn-success action" name="saveForm" id="saveForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action_save"></span></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- <div class="modal fade" id="modal_form_cashadvance" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                    <input disabled id="currency_settlement" name="currency_settlement" class="form-control form-control-sm currency-settlement" style="width: 100%;">
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
                <button class="btn btn-sm btn-info action_submit" id="submitCashAdvanceForm" name="submitCashAdvanceForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit_cash_advance"></span></button>&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}
@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.min.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
<script type="text/javascript">
var global_products = [];
var global_currencies = [];
var global_company_currency = null;
var global_employee_bank = [];
var global_employee_bank_account = [];
var global_employee_default_bank_account = null;
var global_company_bank = [];
var settlementData = [];
var global_regions = [];
var global_official_travel_start = null;
var global_official_travel_end = null;
var total_visible_settlement = 0;
$('#label_button_action_submit').text("Submit");
$('#label_button_action_save').text("Save Draft");

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
        url: "{{ route('settlementtravel.index') }}",
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
        data: 'letter_date', 
        name: 'letter_date', 
        render: function (data, type, row) {
          return TanggalIndonesia(data);
        }  
      },
      { data: 'category', name: 'category' },
      // { 
      //   data: 'name', 
      //   name: 'name', 
      //   render: function (data, type, row) {
      //     return data;
      //   }
      // },
      {
        data: 'start_date',
        name: 'start_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
            var startDate = new Date(data);
            return TanggalIndonesia(startDate.toISOString().split('T')[0]);
          }
          return data;
        }
      },
      {
        data: 'end_date',
        name: 'end_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
            var startDate = new Date(data);
            return TanggalIndonesia(startDate.toISOString().split('T')[0]);
          }
          return data;
        }
      },
        // { data: 'location_from', name: 'location_from' },
        { data: 'location_to', name: 'location_to' },
        { data: 'is_approved_by_chief_desc', name: 'is_approved_by_chief', className: 'text-center' },
        { data: 'is_approved_by_finance_desc', name: 'is_approved_by_finance', className: 'text-center' },
        { data: 'submitted', name: 'is_validate', className: 'text-center' },
        {
            data: 'is_have_cash_advance',
            name: 'is_have_cash_advance',
            className: 'text-center',
            render: function(data, type, row) {
                if(row.is_have_cash_advance) {
                    return `<span class="badge badge-success" style="padding:5px;font-size:12px;">Yes</span>`;
                }
                return `<span class="badge badge-danger" style="padding:5px;font-size:12px;">No</span>`;
            }
        },
        {
            data: 'hca_settlement_status',
            name: 'hca_settlement_status',
            className: 'text-center',
            render: function(data, type, row) {
                if(row.hca_settlement_status == "Clear") {
                    return `<span class="badge badge-success" style="padding:5px;font-size:12px;">Cleared</span>`;
                } else if(row.hca_settlement_status == "Not_Clear") {
                    return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Clear</span>';
                }
                return `<span class="badge badge-secondary" style="padding:5px;font-size:12px;">${row.hca_settlement_status}</span>`;
                
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

function calculateQty(element) {
    // let startEnd = element.find('.settlement-start-end').val();
    // let productCode = element.find('.settlement-product').attr('product-code');

    // if(!productCode || !startEnd) return;
    // let start = moment(startEnd.split(" to ")[0], 'YYYY-MM-DD');
    // let end = moment(startEnd.split(" to ")[1], 'YYYY-MM-DD');
    // let timeDiff = end.diff(start, 'days');
    // if(productCode != "CSM0000003") {
    //     timeDiff += 1;
    // }
    // if(timeDiff < 1) {
    //     element.find('.settlement-qty').val(0).attr('readonly', false);
    //     return;
    // }
    // if(["CSM0000001", "CSM0000003"].includes(productCode)) {
    //     element.find('.settlement-qty').val(timeDiff).attr('readonly', true);
    // }
}

$(document).on('click', '#button-add-settlement', () => {
    let clone = $('#table-settlement-sample').clone();
    clone.find('.settlement-region').select2({
        data: global_regions
    }).trigger('change');

    clone.find('.settlement-product').css('width', '100%');
    clone.find('.settlement-product').select2({
        data: global_products,
        placeholder: "Select Product",
        allowClear: true
    });
    clone.find('.settlement-currency').select2({
        data: global_currencies
    }).val($('#currency_settlement').val()).trigger('change');
    clone.find('.settlement-currency-rate').val(1);
    clone.find('.settlement-start-end').val(global_official_travel_start).daterangepicker({
        drops: 'up',
        autoUpdateInput: true,
        minDate: global_official_travel_start,
        maxDate: global_official_travel_end,
        singleDatePicker: true,
        locale: {
        cancelLabel: 'Reset',
        format: 'YYYY-MM-DD',
        separator: ' to '
        }
    }).on('apply.daterangepicker', function (ev, picker) {
        let startDate = picker.startDate;
        let endDate = picker.endDate;
        let timeDiff = endDate - startDate;
        calculateQty($(element));
    });
    clone.appendTo('#table_settlement');
    $('#table_settlement').children().each((i, element) => {
        let index = i+1;
        $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
        $(element).find('.settlement-no').text(index);
        $(element).find('.id-settlement').attr('name', `settlement[${i}][id_settlement]`);
        $(element).find('.settlement-start-end').attr('id', 'settlement-startend-' + i);
        $(element).find('.settlement-start-end').attr('name', `settlement[${i}][start_end]`);
        $(element).find('.settlement-product').attr('id', 'settlement-product-' + i);
        $(element).find('.settlement-product').attr('name', `settlement[${i}][product]`);
        $(element).find('.product-error').attr('id', `settlement_${i}_productError`);
        $(element).find('.settlement-region').attr('id', `settlement-region-${i}`);
        $(element).find('.settlement-region').attr('name', `settlement[${i}][region]`);
        $(element).find('.region-error').attr('id', `settlement_${i}_regionError`);
        $(element).find('.settlement-branch').attr('id', `settlement-branch-${i}`);
        $(element).find('.settlement-branch').attr('name', `settlement[${i}][branch]`);
        $(element).find('.branch-error').attr('id', `settlement_${i}_branchError`);
        $(element).find('.settlement-description').attr('id', 'settlement-description-' + i);
        $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
        $(element).find('.description-error').attr('id', `settlement_${i}_descriptionError`);
        $(element).find('.settlement-notes').attr('id', 'settlement-notes-' + i);
        $(element).find('.settlement-notes').attr('name', `settlement[${i}][notes]`);
        $(element).find('.settlement-qty').attr('id', 'settlement-qty-' + i);
        $(element).find('.settlement-qty').attr('name', `settlement[${i}][qty]`);
        $(element).find('.qty-error').attr('id', `settlement_${i}_qtyError`);
        $(element).find('.settlement-unit').attr('id', 'settlement-unit-' + i);
        $(element).find('.settlement-unit').attr('name', `settlement[${i}][unit]`);
        $(element).find('.unit-error').attr('id', `settlement_${i}_unitError`);
        $(element).find('.settlement-currency').attr('id', 'settlement-currency-' + i);
        $(element).find('.settlement-currency').attr('name', `settlement[${i}][currency]`);
        $(element).find('.settlement-currency').attr('readonly', true);
        $(element).find('.settlement-currency-rate').attr('id', 'settlement-currencyrate-' + i);
        $(element).find('.settlement-currency-rate').attr('name', `settlement[${i}][currency_rate]`);
        $(element).find('.settlement-price').attr('id', 'settlement-price-' + i);
        $(element).find('.settlement-price').attr('name', `settlement[${i}][price]`);
        $(element).find('.price-error').attr('id', `settlement_${i}_priceError`);
        $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
        $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
        $(element).find('.settlement-attachment').attr('id', 'settlement-attachment-' + i);
        $(element).find('.settlement-attachment').attr('name', `settlement[${i}][attachment]`);
        $(element).find('.attachment-error').attr('id', `settlement_${i}_attachmentError`);
        if(!$(element).find('.id-settlement').attr('approval')) {
            $(element).find('.settlement-action').html(`
                <button type="button" class="btn btn-danger settlement-delete" id="settlement-delete-${i}"><i class="fas fa-trash"></i></button>
            `);
        }
        if(i == $('#table_settlement').children().length-1) {
            $(element).find('.settlement-region').trigger('change');
        }
    })
});

$(document).on('click', '#button-add-refund', () => {
    let clone = $('#table-refund-sample').clone();
    clone.find('.refund-bank-from').select2({
        data: global_employee_bank
    });
    if(global_employee_default_bank_account) {
        clone.find('.refund-bank-account').val(global_employee_default_bank_account.bank_account);
        clone.find('.refund-bank-from').val(global_employee_default_bank_account.id_bank).trigger('change');
    }
    clone.appendTo('#table_refund');
    $('#table_refund').children().each((i, element) => {
        let index = i+1;
        $(element).find('.refund-no').attr('id', 'refund-no-' + i);
        $(element).find('.refund-no').text(index);
        $(element).find('.id-refund').attr('name', `refund[${i}][id_refund]`)
        // $(element).find('.refund-id-settlement').select2({
        //     data: settlementData
        // });
        $(element).find('.refund-bank-from').attr('id', 'refund_bankfrom_' + i);
        $(element).find('.refund-bank-from').attr('name', `refund[${i}][bank_from]`);
        $(element).find('.refund-bank-from-error').attr('id', `refund_${i}_bank_fromError`);
        $(element).find('.refund-bank-to').attr('id', 'refund_bankto_' + i);
        $(element).find('.refund-bank-to').attr('name', `refund[${i}][bank_to]`);
        $(element).find('.refund-bank-to-error').attr('id', `refund_${i}_bank_toError`);
        $(element).find('.refund-bank-account').attr('id', 'refund_bankaccount_' + i);
        $(element).find('.refund-bank-account').attr('name', `refund[${i}][bank_account]`);
        $(element).find('.refund-bank-account-error').attr('id', `refund_${i}_bank_accountError`);
        $(element).find('.refund-amount').attr('id', 'refund_amount_' + i);
        $(element).find('.refund-amount').attr('name', `refund[${i}][amount]`);
        $(element).find('.refund-amount-error').attr('id', `refund_${i}_amountError`);
        $(element).find('.refund-attachment').attr('id', 'refund_attachment_' + i);
        $(element).find('.refund-attachment').attr('name', `refund[${i}][attachment]`);
        $(element).find('.refund-attachment-error').attr('id', `refund_${i}_attachmentError`);
        if(!$(element).find('.id-refund').val()) {
            $(element).find('.refund-action').html(`
                <button type="button" class="btn btn-danger refund-delete" id="refund-delete-${i}"><i class="fas fa-trash"></i></button>
            `);
        } else {

        }
        

        $(element).find('.refund-bank-to').select2({
            data: global_company_bank
        })
        $(element).find('.refund-id-settlement').attr('name', `refund[${i}][id_settlement]`);
        $(element).find('.refund-id-settlement').select2({
            data: settlementData
        });
    })
});

function changeTotal(element) {
    let id = $(element.target).attr('id').split("-")[2];
    let qty = $('#settlement-qty-'+id).val();
    let price = unformatRupiah($('#settlement-price-'+id).val());
    let currencyRate = unformatRupiah($('#settlement-currencyrate-'+id).val());
    $(`#settlement-total-${id}`).val(global_company_currency.currency_symbol+formatRupiah(price*qty*currencyRate));

    let priceval = $('#settlement-price-'+id).val();
    // console.log('#settlement-price-'+id, priceval?priceval.toString().replace(".", ""):"", parseInt($('#settlement-price-'+id).val()));
    $('#settlement-price-'+id).val(formatRupiah(parseInt(priceval?priceval.toString().replaceAll(".", ""):priceval)));
}

$(document).on('change', '.settlement-price', (element) => {
    changeTotal(element)
});
$(document).on('change', '.settlement-qty', (element) => {
    changeTotal(element)
});
$(document).on('change', '.settlement-currency-rate', (element) => {
    changeTotal(element)
});
$(document).on('change', '.settlement-currency', (element) => {
    changeTotal(element)
});
$(document).on('change', '#currency_settlement', (element) => {
    $('tbody#table_settlement > tr > td > .settlement-currency').each(function () {
        $(this).val($('#currency_settlement').val()).trigger('change');
    });
});
$(document).on('change', '.refund-amount', (element) => {
    let amountval = $(element.target).val();
    $(element.target).val(formatRupiah(parseInt(amountval?amountval.toString().replace(".", ""):amountval)));
});

function handleValidationError(errors) {
    Object.keys(errors).forEach(function (key) {
        var key_temp = key.replaceAll(".", "_");
        $("#" + key_temp + "Error").addClass("is-invalid");
        $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
    });
}

$(document).on("submit", '#travelForm', function (event) {
    // let formData = $('#travelForm').serialize();
    let formData = new FormData(this)
    // console.log(formData)
    event.preventDefault();
    $.ajax({
        url: `{{ route('settlementtravel.update') }}`,
        type: 'POST',
        dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        beforeSend: () => {
            $('#loader').removeClass('hidden');
            $(".is-invalid").removeClass("is-invalid");
            $(".is-invalid").html('<strong></strong>');
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
                let content = document.createElement('div');
                let div = document.createElement('div');
                div.style.color = '#f00';
                div.style.fontWeight = '600';
                div.innerText = `You haven't filled the following input:`;
                content.appendChild(div)
                content.appendChild(document.createElement('br'));
                Object.keys(xhr.responseJSON.errors).forEach(function (key) {
                    let div = document.createElement('div');
                    div.style.color = '#f00';
                    div.style.fontWeight = '600';
                    let row = key.split('.')[1] ? `at row ${parseInt(key.split('.')[1])+1}` : '';
                    div.innerText = `${xhr.responseJSON.errors[key][0]} ${row}`;
                    content.appendChild(div)
                });
                
                console.log(content)
                swal({
                    icon: 'error',
                    title: 'Error',
                    content: content,
                })
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
    $('#submit-type').val('submit');
    $('#travelForm').submit();
});
$('#modal_form_settlement').on('click', '#saveForm', () => {
    $('#submit-type').val('draft');
    $('#travelForm').submit();
});

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

$(document).on('change', '.settlement-region', function () {
    // console.log($(this).val())
    if($(this).val()) {
        $.ajax({
            url: "{{ route('settlementtravel.get_branch') }}",
            data: {
                id_region: $(this).val()
            },
            success: (response) => {
                $(this).closest('tr').find('.settlement-branch').empty();
                $(this).closest('tr').find('.settlement-branch').select2({
                    data: response
                });
                // console.log($(this).attr('id-branch'))
                if($(this).attr('id-branch')) {
                    $(this).closest('tr').find('.settlement-branch')
                        .val($(this).attr("id-branch"))
                        .trigger('change')
                        .attr('readonly', true);
                    if($(this).attr('draft-unlock')) {
                        $(this).closest('tr').find('.settlement-branch').attr('readonly', false);
                    }
                } else {
                    $(this).closest('tr').find('.settlement-branch').attr('readonly', false);
                }
            }
        });
    }
});

function getProductUom() {
    let data = [];
    let ids = [];
    global_products.forEach((product, i) => {
        if(!ids.find((element) => element == product.id_uom)) {
            data.push({id: product.id_uom, text: product.desc_uom})
            ids.push(product.id_uom);
        }
    })
    return data;
}

$(document).on('change', '.settlement-product', function () {
    $(this).parent().parent().find('.settlement-unit').select2({
        data: getProductUom()
    });
    let selectedProduct= null;
    global_products.forEach((product) => {
        if(product.id == $(this).val()) {
            selectedProduct = product;
            $(this).attr('product-code', product.code);
        }
    });
    // $(this).parent().parent().find('.settlement-start-end').trigger('apply.daterangepicker');
    calculateQty($(this).parent().parent());

    $(this).closest('tr').find('.settlement-price').css('width', '100%');
    $(this).closest('tr').find('.tooltip-budget').hide();
    // $(this).closest('tr').find('.tooltip-budget > a').popover('disable');
    
    // let idRegion = $(this).closest('tr').find('.settlement-region').val();
    // let idBranch = $(this).closest('tr').find('.settlement-branch').val();
    // let element = $(this);
    // if(selectedProduct && idRegion && idBranch) {
    //     $.ajax({
    //         url: '{{ route("settlementtravel.get_data") }}',
    //         data: {
    //             id_product: selectedProduct.id,
    //             id_region: idRegion,
    //             id_branch: idBranch,
    //         },
    //         success: (res) => {
    //             $(element).closest('tr').find('.settlement-price').css('width', '88%').css('min-width', '88px');
    //             $(element).closest('tr').find('.tooltip-budget').show();
    //             $(element).closest('tr').find('.tooltip-budget > a').popover();
    //             $(element).closest('tr').find('.tooltip-budget > a').attr('data-content', `${res.max_price ? (`Budget for ${selectedProduct.text} is ${formatRupiah(parseInt(res.max_price))}/${selectedProduct.desc_uom}. ${selectedProduct.long_description ? 'Additional Note: '+selectedProduct.long_description : ''}`) : selectedProduct.long_description}`);
    //         }
    //     })
    // }
    
});

$(document).on('click', '#view-attachment-settlement', function() {
    // $('#attachment-settlement-pdf').attr('src', $(this).attr('path'));
    // $('#attachment-settlement-modal').modal('show');
    window.open(`${$(this).attr('path')}`, "_blank");
})

$(document).on('click', '.button-view', function() {
    let idOfficialTravel = $(this).attr('id-official-travel');
    settlementData = [];
    $('#submitForm').css('display', 'block');
    $('#saveForm').css('display', 'block');
    $('#button-add-settlement').css('display', 'block');
    $('#modal_form_settlement').modal('show');
    $('#billing_letter_row').addClass('hidden');
    $.ajax({
        url: '{{ route("settlementtravel.get_data") }}',
        data: {
            id_official_travel: idOfficialTravel
        },
        beforeSend: () => {
            total_visible_settlement = 0;
            $('#loader').removeClass('hidden');
            $('#approval_by').empty();
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
            global_regions= response.regions;
            global_employee_bank_account = response.employee_bank;
            response.employee_bank.forEach((bank) => {
                if(bank.default_bank) {
                    global_employee_default_bank_account = bank;
                }
            });
            global_official_travel_start = response.official_travel.start_date;
            global_official_travel_end = response.official_travel.end_date;
            if(response.official_travel.attachment_settlement) {
                $('#view-attachment-settlement').attr('path', `/project/storage/app/public/${response.official_travel.attachment_settlement}`)
                $('#view-attachment-settlement').show();
            } else {
                $('#view-attachment-settlement').hide();
            }
            if(response.official_travel.is_validate) {
                $('#attachment_settlement').hide();
                $('#attachment_settlement').parent().hide();
            } else {
                $('#attachment_settlement').show();
                $('#attachment_settlement').parent().show();
            }
            $('#id_cash_advance').val(response.official_travel.id_cash_advance);
            $('#cash_advance_reference').val(response.official_travel.cash_advance_reference);
            // $('#request_total_amount').val(formatRupiah(parseInt(response.official_travel.hca_total_expense_request_amount)));
            $('#reference_number').val(response.official_travel.reference_number);
            $('#transaction_date').val(response.official_travel.transaction_date);
            $('#cash_advance_notes').val(response.official_travel.cash_advance_notes);
            $('#accounting_date').val(response.official_travel.accounting_date);
            $('#clearing_amount').val(response.official_travel.clearing_amount);
            $('#payment_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_base_currency_payment_amount)));
            $('#total_settlement').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_base_currency_settlement_amount)));
            $('#total_taxed_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_tax_amount)));
            $('#total_cash_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_cash_request_amount)));
            $('#request_total_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.hca_total_expense_request_amount)));
            $('#clearing_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_base_currency_clearing_amount)));
            $('#difference_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_base_currency_difference_amount)));
            $('#total_base_currency_tax').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_base_currency_tax_amount)));
            $('#maximum_clearing_date').val(response.official_travel.maximum_clearing_date);
            $('#total_travel_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.official_travel.total_travel_request_amount)));
            $('#payment_currency').empty().prepend('<option><option>').select2({
                data: response.currencies.all
            });
            $('#settlement_status').empty().select2({
                data: [
                    {id: "Not_Clear", text: "Not Clear"},
                    {id: "Clear", text: "Cleared"}
                ]
            }).val(response.official_travel.hca_settlement_status).trigger('change');
            if(response.official_travel.hca_settlement_status == "Clear" && response.official_travel.hca_is_validate) {
                $('#button-add-settlement').css('display', 'none');
            }
            $('#request_currency').empty().select2({
                data: response.currencies.all
            }).val(response.official_travel.id_currency_cash_advance).trigger('change');
            $('#currency_settlement').empty().select2({
                data: global_currencies
            }).val(global_company_currency.id).trigger('change').attr('readonly', false);
            $('#payment_status').empty().select2({
                data: [
                    {id: "Not_Paid", text: "Not Paid"},
                    {id: "Pending", text: "Pending"},
                    {id: "Hold", text: "Hold"},
                    {id: "Paid", text: "Paid"},
                    {id: "Cancel", text: "Cancel"}
                ]
            }).val(response.official_travel.payment_status).trigger('change');
            
            if(response.official_travel.hca_approval_status == "Approved") {
                $('#approval_status_container').html("<span class='badge badge-success'>Approved</span>");
            } else if(response.official_travel.hca_approval_status == "Rejected") {
                $('#approval_status_container').html("<span class='badge badge-danger'>Rejected</span>");
            } else {
                $('#approval_status_container').html("<b>"+response.official_travel.hca_approval_status+"</b>")
            }
            if(response.official_travel.hca_is_validate) {
                $('#validation_status_container').html("<span class='badge badge-success'>Valid</span>");
            } else {
                $('#validation_status_container').html("<span class='badge badge-danger'>Invalid</span>");
            }
            if(response.official_travel.start_refund_date) {
                $('#billing_letter_row').removeClass('hidden');
                $('#billing_letter_container').html(`
                        <button type="button" onClick="getBillingLetter(${response.official_travel.id_cash_advance})" class="btn btn-sm btn-primary">Download</button>
                    `);
            }

            $('#position_detail').empty().select2({
                data: [
                    {id: null, text: response.official_travel.position_detail}
                ]
            });
            $('#approval_hierarchy').empty().select2({
                data: [
                    {id: response.official_travel.id_approval, text: response.official_travel.approval_description}
                ]
            });
            $('#approval_by').empty().select2({
                data: [
                    {id: response.official_travel.id_approval_request, text: response.official_travel.name_approval_request}
                ]
            });
            $('#request_by').empty().select2({
                data: [
                    {id: response.official_travel.id_employee, text: response.official_travel.name_employee}
                ]
            });
            $('#department').empty().select2({
                data: [
                    {id: response.official_travel.id_department, text: response.official_travel.department_description}
                ]
            });

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

            if(response.official_travel.is_validate) {
                $('#currency_settlement').attr('readonly', true);
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
                
                if(settlement.is_validate) {
                    // $('#submitForm').css('display', 'none');
                    $('#saveForm').css('display', 'none');
                    $('#currency_settlement').attr('readonly', true);
                }
                let clone = $('#table-settlement-sample').clone();
                clone.appendTo('#table_settlement');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('input').attr('readonly', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    // $('select option:not(selected)').prop('disabled', true);
                    $(element).find('.id-settlement').val(settlement.id_settlement_expense);
                    $(element).find('.id-settlement').attr('name', `settlement[${i}][id_settlement]`);
                    $(element).find('.id-settlement').attr('approval', settlement.is_verified_by_chief == true ? "true" : settlement.is_verified_by_chief == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('finance-approval', settlement.is_verified_by_finance == true ? "true" : settlement.is_verified_by_finance == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('is-validate', settlement.is_validate ? "true" : "false");
                    $(element).find('.id-settlement').attr('show-action', settlement.approved_by_chief === null && settlement.approved_by_finance === null && settlement.attachment === null ? "false" : "true");
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(index);
                    $(element).find('.settlement-start-end').attr('id', 'settlement-startend-' + i);
                    $(element).find('.settlement-start-end').attr('name', `settlement[${i}][start_end]`);
                    $(element).find('.settlement-start-end').val(settlement.settlement_date);
                    $(element).find('.start-end-error').attr('id', `settlement_${i}_start_endError`);
                    $(element).find('.settlement-product').attr('id', 'settlement-product-' + i);
                    $(element).find('.settlement-product').attr('name', `settlement[${i}][product]`);
                    $(element).find('.product-error').attr('id', `settlement_${i}_productError`);
                    $(element).find('.settlement-region').attr('id', `settlement-region-${i}`);
                    $(element).find('.settlement-region').attr('name', `settlement[${i}][region]`);
                    $(element).find('.settlement-region').attr('id-branch', settlement.id_branch);
                    $(element).find('.region-error').attr('id', `settlement_${i}_regionError`);
                    $(element).find('.settlement-branch').attr('id', `settlement-branch-${i}`);
                    $(element).find('.settlement-branch').attr('name', `settlement[${i}][branch]`);
                    $(element).find('.branch-error').attr('id', `settlement_${i}_branchError`);
                    $(element).find('.settlement-description').attr('id', 'settlement-description-' + i);
                    $(element).find('.settlement-description').attr('name', `settlement[${i}][description]`);
                    $(element).find('.settlement-description').val(settlement.description);
                    $(element).find('.description-error').attr('id', `settlement_${i}_descriptionError`);
                    $(element).find('.settlement-notes').attr('id', 'settlement-notes-' + i);
                    $(element).find('.settlement-notes').attr('name', `settlement[${i}][notes]`);
                    $(element).find('.settlement-notes').text(settlement.notes);
                    $(element).find('.settlement-qty').attr('id', 'settlement-qty-' + i);
                    $(element).find('.settlement-qty').attr('name', `settlement[${i}][qty]`);
                    $(element).find('.settlement-qty').val(settlement.qty);
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
                    $(element).find('.settlement-price').val(settlement.unit_price);
                    $(element).find('.settlement-total').attr('id', 'settlement-total-' + i);
                    $(element).find('.settlement-total').attr('name', `settlement[${i}][total]`);
                    $(element).find('.settlement-chief-approval-price').attr('id', 'settlement-chiefapprovalprice-' + i);
                    $(element).find('.settlement-chief-approval-price').attr('name', `settlement[${i}][chief_approval_price]`);
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
                    $(element).find('.settlement-chief-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-chief-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-chief-total-approval').val(formatRupiah(parseInt(settlement.approval_price_by_chief * settlement.qty * settlement.currency_rate_settlement_expense)));
                    $(element).find('.settlement-approval-price').attr('id', 'settlement-approvalprice-' + i);
                    $(element).find('.settlement-approval-price').attr('name', `settlement[${i}][approval_price]`);
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
                    $(element).find('.settlement-total-approval').attr('id', 'settlement-totalapproval-' + i);
                    $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
                    $(element).find('.settlement-attachment').attr('id', 'settlement-attachment-' + i);
                    $(element).find('.settlement-attachment').attr('name', `settlement[${i}][attachment]`);
                    $(element).find('.attachment-error').attr('id', `settlement_${i}_attachmentError`);
                    if(settlement.attachment) {
                        $(element).find('.settlement-attachment-download').css('display', 'block');
                        $(element).find('.settlement-attachment-download').children().attr('href', '/project/storage/app/public/'+settlement.attachment);
                        $(element).find('.settlement-attachment-download').children().text('Download');
                    }
                    $(element).find('.settlement-unit').select2({
                        data: [{id: settlement.id_uom, text: settlement.desc_uom}]
                    }).val(settlement.id_uom).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-region').select2({
                        data: response.regions
                    }).val(settlement.id_region).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-branch').select2({
                        data: []
                    }).val(settlement.id_branch).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-product').css('width', '100%');
                    $(element).find('.settlement-product').select2({
                        data: response.full_products,
                        placeholder: "Select Product",
                        allowClear: true
                    }).val(settlement.id_product).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-currency').select2({
                        data: global_currencies
                    }).val(settlement.id_currency_settlement_expense).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-currency-rate').val(settlement.currency_rate_settlement_expense);

                    if(!settlement.is_validate && !response.official_travel.hca_is_validate) {
                        $(element).find('.settlement-start-end').attr('readonly', false).daterangepicker({
                            drops: 'up',
                            autoUpdateInput: false,
                            minDate: global_official_travel_start,
                            maxDate: global_official_travel_end,
                            singleDatePicker: true,
                            locale: {
                            cancelLabel: 'Reset',
                            format: 'YYYY-MM-DD',
                            separator: ' to '
                            }
                        }).on('apply.daterangepicker', function (ev, picker) {
                            $(this).val(picker.startDate.format('YYYY-MM-DD'));
                        });
                        $(element).find('.settlement-attachment').attr('readonly', false).attr('disabled', false);
                        $(element).find('.settlement-product, .settlement-region, .settlement-branch, .settlement-qty, .settlement-unit, .settlement-currency, .settlement-price, .settlement-attachment, .settlement-description')
                        .attr('readonly', false).attr('disabled', false).attr('draft-unlock', true);
                    }
                })
                $('#table_settlement').children().each((i, element) => {
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(i+1);
                    $(element).find('.settlement-currency').trigger('change');
                    
                    if(!$(element).find('.id-settlement').attr('approval') || $(element).find('.id-settlement').attr('is-validate') == "false") {
                        let badge = '';
                        if($(element).find('.id-settlement').attr('is-validate') == "false") {
                            badge += `<span class="badge badge-info">Revise</span>`
                        }
                        if($(element).find('.id-settlement').attr('show-action') == "false") {
                            badge = `<span class="badge badge-success">Purchased by HR</span>`
                        }
                        $(element).find('.settlement-action').html(`
                            <!--<button type="button" class="btn btn-danger settlement-delete" id="settlement-delete-${i}" settlement-id="${$(element).find('.id-settlement').val()}"><i class="fas fa-trash"></i></button>
                            -->
                            ${badge}
                        `);
                    } else if($(element).find('.id-settlement').attr('approval') == "false" || $(element).find('.id-settlement').attr('finance-approval') == "false") {
                        $(element).find('.settlement-action').html(`<span class="badge badge-danger">Rejected</span>`);
                    } else if($(element).find('.id-settlement').attr('approval') == "true") {
                        let badge = `<span class="badge badge-success">Approved by Chief</span>`;
                        if($(element).find('.id-settlement').attr('finance-approval') == "true") {
                            badge += `<span class="badge badge-success">Approved by Finance</span>`
                            
                        }
                        $(element).find('.settlement-action').html(badge);
                    }  else {
                        $(element).find('.settlement-action').html(`<span class="badge badge-warning">Pending Approval</span>`);
                    }
                    
                });

                if(i+1 == response.settlement.length) {
                    $('#tab-settlement').trigger('click');
                }
            })

            response.transport_accommodation.forEach((settlement, i) => {
                let clone = $('#table-settlement-sample').clone();
                if(settlement.is_paid_by_hr) {
                    clone.find('.settlement-action').html(`<span class="badge badge-success">Purchased by HR</span>`);
                }
                clone.appendTo('#table_transport_accommodation');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('input').attr('disabled', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    // $('select option:not(selected)').prop('disabled', true);
                    $(element).find('.id-settlement').val(settlement.id_settlement_expense);
                    $(element).find('.id-settlement').attr('name', `transaco[${i}][id_settlement]`);
                    $(element).find('.id-settlement').attr('approval', settlement.is_verified_by_chief == true ? "true" : settlement.is_verified_by_chief == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('finance-approval', settlement.is_verified_by_finance == true ? "true" : settlement.is_verified_by_finance == false ? "false" : "null");
                    $(element).find('.id-settlement').attr('is-validate', settlement.is_validate ? "true" : "false");
                    $(element).find('.id-settlement').attr('show-action', settlement.approved_by_chief === null && settlement.approved_by_finance === null && settlement.attachment === null ? "false" : "true");
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(index);
                    $(element).find('.settlement-start-end').attr('id', 'transaco-startend-' + i);
                    $(element).find('.settlement-start-end').attr('name', `transaco[${i}][start_end]`);
                    $(element).find('.settlement-start-end').val(settlement.settlement_date);
                    $(element).find('.start-end-error').attr('id', `transaco_${i}_start_endError`);
                    $(element).find('.settlement-product').attr('id', 'transaco-product-' + i);
                    $(element).find('.settlement-product').attr('name', `transaco[${i}][product]`);
                    $(element).find('.product-error').attr('id', `transaco_${i}_productError`);
                    $(element).find('.settlement-region').attr('id', `transaco-region-${i}`);
                    $(element).find('.settlement-region').attr('name', `transaco[${i}][region]`);
                    $(element).find('.settlement-region').attr('id-branch', settlement.id_branch);
                    $(element).find('.region-error').attr('id', `transaco_${i}_regionError`);
                    $(element).find('.settlement-branch').attr('id', `transaco-branch-${i}`);
                    $(element).find('.settlement-branch').attr('name', `transaco[${i}][branch]`);
                    $(element).find('.branch-error').attr('id', `transaco_${i}_branchError`);
                    $(element).find('.settlement-description').attr('id', 'transaco-description-' + i);
                    $(element).find('.settlement-description').attr('name', `transaco[${i}][description]`);
                    $(element).find('.settlement-description').val(settlement.description);
                    $(element).find('.description-error').attr('id', `transaco${i}_descriptionError`);
                    $(element).find('.settlement-notes').attr('id', 'transaco-notes-' + i);
                    $(element).find('.settlement-notes').attr('name', `transaco[${i}][notes]`);
                    $(element).find('.settlement-notes').text(settlement.notes);
                    $(element).find('.settlement-qty').attr('id', 'transaco-qty-' + i);
                    $(element).find('.settlement-qty').attr('name', `transaco[${i}][qty]`);
                    $(element).find('.settlement-qty').val(settlement.qty);
                    $(element).find('.qty-error').attr('id', `transaco${i}_qtyError`);
                    $(element).find('.settlement-unit').attr('id', 'transaco-unit-' + i);
                    $(element).find('.settlement-unit').attr('name', `transaco[${i}][unit]`);
                    $(element).find('.settlement-unit').val(settlement.qty);
                    $(element).find('.unit-error').attr('id', `transaco${i}_unitError`);
                    $(element).find('.settlement-currency').attr('id', 'transaco-currency-' + i);
                    $(element).find('.settlement-currency').attr('name', `transaco[${i}][currency]`);
                    $(element).find('.settlement-currency-rate').attr('id', 'transaco-currencyrate-' + i);
                    $(element).find('.settlement-currency-rate').attr('name', `transaco[${i}][currency_rate]`);
                    $(element).find('.settlement-price').attr('id', 'transaco-price-' + i);
                    $(element).find('.settlement-price').attr('name', `transaco[${i}][price]`);
                    $(element).find('.settlement-price').val(settlement.unit_price);
                    $(element).find('.settlement-total').attr('id', 'transaco-total-' + i);
                    $(element).find('.settlement-total').attr('name', `transaco[${i}][total]`);
                    $(element).find('.settlement-chief-approval-price').attr('id', 'transaco-chiefapprovalprice-' + i);
                    $(element).find('.settlement-chief-approval-price').attr('name', `transaco[${i}][chief_approval_price]`);
                    $(element).find('.settlement-chief-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_chief)));
                    $(element).find('.settlement-chief-total-approval').attr('id', 'transaco-totalapproval-' + i);
                    $(element).find('.settlement-chief-total-approval').attr('name', `transaco[${i}][total_approval]`);
                    $(element).find('.settlement-chief-total-approval').val(formatRupiah(parseInt(settlement.approval_price_by_chief * settlement.qty * settlement.currency_rate_settlement_expense)));
                    $(element).find('.settlement-approval-price').attr('id', 'transaco-approvalprice-' + i);
                    $(element).find('.settlement-approval-price').attr('name', `transaco[${i}][approval_price]`);
                    $(element).find('.settlement-approval-price').val(formatRupiah(parseInt(settlement.approval_price_by_finance)));
                    $(element).find('.settlement-total-approval').attr('id', 'transaco-totalapproval-' + i);
                    $(element).find('.settlement-total-approval').attr('name', `settlement[${i}][total_approval]`);
                    $(element).find('.settlement-total-approval').val(formatRupiah(parseInt(settlement.total_approval_amount)));
                    $(element).find('.settlement-attachment').attr('id', 'transaco-attachment-' + i);
                    $(element).find('.settlement-attachment').attr('name', `transaco[${i}][attachment]`);
                    $(element).find('.attachment-error').attr('id', `transaco_${i}_attachmentError`);
                    if(settlement.attachment) {
                        $(element).find('.settlement-attachment-download').css('display', 'block');
                        $(element).find('.settlement-attachment-download').children().attr('href', '/project/storage/app/public/'+settlement.attachment);
                        $(element).find('.settlement-attachment-download').children().text('Download');
                    }
                    $(element).find('.settlement-unit').select2({
                        data: [{id: settlement.id_uom, text: settlement.desc_uom}]
                    }).val(settlement.id_uom).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-region').select2({
                        data: response.regions
                    }).val(settlement.id_region).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-branch').select2({
                        data: []
                    }).val(settlement.id_branch).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-product').css('width', '100%');
                    $(element).find('.settlement-product').select2({
                        data: response.full_products,
                        placeholder: "Select Product",
                        allowClear: true
                    }).val(settlement.id_product).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-currency').select2({
                        data: global_currencies
                    }).val(settlement.id_currency_settlement_expense).trigger('change').attr('readonly', true);
                    $(element).find('.settlement-currency-rate').val(settlement.currency_rate_settlement_expense);

                    if(!settlement.is_validate && !response.official_travel.hca_is_validate) {
                        $(element).find('.settlement-start-end').attr('readonly', false).daterangepicker({
                            drops: 'up',
                            autoUpdateInput: false,
                            minDate: global_official_travel_start,
                            maxDate: global_official_travel_end,
                            singleDatePicker: true,
                            locale: {
                            cancelLabel: 'Reset',
                            format: 'YYYY-MM-DD',
                            separator: ' to '
                            }
                        }).on('apply.daterangepicker', function (ev, picker) {
                            $(this).val(picker.startDate.format('YYYY-MM-DD'));
                        });
                        $(element).find('.settlement-attachment').attr('readonly', true).attr('disabled', true);
                        $(element).find('.settlement-product, .settlement-region, .settlement-branch, .settlement-qty, .settlement-unit, .settlement-price, .settlement-attachment')
                        .attr('readonly', false).attr('disabled', false);
                    }
                })
                $('#table_settlement').children().each((i, element) => {
                    $(element).find('.settlement-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.settlement-no').text(i+1);
                    $(element).find('.settlement-currency').trigger('change');
                    
                    // if(!$(element).find('.id-settlement').attr('approval') || $(element).find('.id-settlement').attr('is-validate') == "false") {
                    //     let badge = '';
                    //     if($(element).find('.id-settlement').attr('is-validate') == "false") {
                    //         badge += `<span class="badge badge-info">Revise</span>`
                    //     }
                    //     if($(element).find('.id-settlement').attr('show-action') == "false") {
                    //         badge = `<span class="badge badge-success">Purchased by HR</span>`
                    //     }
                    //     $(element).find('.settlement-action').html(`
                    //         <!--<button type="button" class="btn btn-danger settlement-delete" id="settlement-delete-${i}" settlement-id="${$(element).find('.id-settlement').val()}"><i class="fas fa-trash"></i></button>
                    //         -->
                    //         ${badge}
                    //     `);
                    // } else if($(element).find('.id-settlement').attr('approval') == "false" || $(element).find('.id-settlement').attr('finance-approval') == "false") {
                    //     $(element).find('.settlement-action').html(`<span class="badge badge-danger">Rejected</span>`);
                    // } else if($(element).find('.id-settlement').attr('approval') == "true") {
                    //     let badge = `<span class="badge badge-success">Approved by Chief</span>`;
                    //     if($(element).find('.id-settlement').attr('finance-approval') == "true") {
                    //         badge += `<span class="badge badge-success">Approved by Finance</span>`
                            
                    //     }
                    //     $(element).find('.settlement-action').html(badge);
                    // }  else {
                    //     $(element).find('.settlement-action').html(`<span class="badge badge-warning">Pending Approval</span>`);
                    // }
                    
                });
            })

            response.refund.forEach((refund, i) => {
                let clone = $('#table-refund-sample').clone();
                clone.appendTo('#table_refund');
                $(clone).children().each((j, element) => {
                    let index = i+1;
                    $(element).find('.id-refund').val(refund.id_cash_refund);
                    $(element).find('.id-refund').attr('name', `refund[${i}][id_refund]`);
                    $(element).find('.id-refund').attr('finance-approval', refund.is_verified);
                    $(element).find('.id-refund').attr('payment-status', refund.payment_status);
                    $(element).find('.refund-no').attr('id', 'refund-no-' + i);
                    $(element).find('.refund-no').text(index);
                    $(element).find('.refund-bank-from').attr('name', `refund[${i}][bank_from]`);
                    $(element).find('.refund-bank-from').select2({
                        data: global_employee_bank
                    }).val(refund.id_bank_from).attr('readonly', true);
                    $(element).find('.refund-bank-from-error').attr('id', `refund_${i}_bank_fromError`);
                    $(element).find('.refund-bank-account').attr('name', `refund[${i}][bank_account]`);
                    $(element).find('.refund-bank-account').val(refund.bank_from_account);
                    $(element).find('.refund-bank-account-error').attr('id', `refund_${i}_bank_accountError`);
                    $(element).find('.refund-bank-to').attr('name', `refund[${i}][bank_to]`);
                    $(element).find('.refund-bank-to').select2({
                        data: global_company_bank
                    }).val(refund.id_bank_to_account).trigger('change').attr('readonly', true);
                    $(element).find('.refund-bank-to-error').attr('id', `refund_${i}_bank_toError`);
                    $(element).find('.refund-amount').attr('name', `refund[${i}][amount]`);
                    $(element).find('.refund-amount').val(formatRupiah(parseInt(refund.total_amount)));
                    $(element).find('.refund-amount-error').attr('id', `refund_${i}_amountError`);
                    $(element).find('.refund-attachment').attr('name', `refund[${i}][attachment]`);
                    $(element).find('.refund-attachment-error').attr('id', `refund_${i}_attachmentError`);
                    if(refund.attachment) {
                        $(element).find('.refund-download').css('display', 'block');
                        $(element).find('.refund-download').children().attr('href', '/project/storage/app/public/'+refund.attachment);
                        $(element).find('.refund-download').children().text('Download');
                    }
                });
                $('#table_refund').children().each((i, element) => {
                    $(element).find('input').attr('readonly', true);
                    $(element).find('input[type=file]').attr('disabled', true);
                    // $(element).find('select').props('disabled', true);
                    $(element).find('.refund-no').attr('id', 'settlement-no-' + i);
                    $(element).find('.refund-no').text(i+1);
                    badge = ``;
                    if($(element).find('.id-refund').attr('finance-approval')) {
                        if($(element).find('.id-refund').attr('finance-approval') == "true") {
                            badge += `<span class="badge badge-sm badge-success">Approved by Finance</span>`
                        } else {
                            badge += `<span class="badge badge-sm badge-warning">Pending Approval</span>`
                        }

                        if($(element).find('.id-refund').attr('payment-status') == "Not_Paid") {
                            badge += `<span class="badge badge-sm badge-secondary">Not Paid</span>`
                        } else if($(element).find('.id-refund').attr('payment-status') == "Partially_Paid") {
                            badge += `<span class="badge badge-sm badge-warning">Partially Paid</span>`;
                        } else if($(element).find('.id-refund').attr('payment-status') == "Paid") {
                            badge += `<span class="badge badge-sm badge-success">Paid</span>`;
                        } else {
                            badge += `<b>${$(element).find('.id-refund').attr('payment-status')}</b>`;
                        }

                        $(element).find('.refund-action').html(badge);
                    }
                    // $(element).find('.refund-action').html(`
                    //     <button type="button" class="btn btn-danger refund-delete" id="refund-delete-${i}" refund-id="${$(element).find('.id-refund').val()}"><i class="fas fa-trash"></i></button>
                    // `);
                    // $(element).find('.refund-id-settlement').select2({
                    //     data: settlementData
                    // });
                });
            });

            response.payment.forEach((payment, i) => {
                $('#payment_currency').val(payment.id_currency_cash_payment).trigger('change');
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
                    $('#payment_currency').val(payment.id_currency_cash_payment).trigger('change');
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
});

$(document).on('click', '#addCashAdvance', function () {
    $.ajax({
        url: '{{ route("settlementtravel.offtrav_nocashadvance") }}',
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (data) {
            $('#loader').addClass('hidden');

            $('select#id_official_travel').prepend('<option></option>').select2({
                data: data.official_travel,
                placeholder: "Pilih Official Travel",
                allowClear: true
            }).attr('disabled', false);

            $('#modal_form_cashadvance').modal('show');
        }
    });
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
    $('#submit-type').val('submit');
    $('#cashAdvanceForm').submit();
});

function getBillingLetter(idCashAdvance) {
    window.open(`{{route('settlementtravel.print_bill')}}?id_cash_advance=${idCashAdvance}`, Math.random());
}

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

$(document).on('change', '#attachment_settlement', function(element) {
    let file = $(this).prop('files')[0];
    $(this).parent().find('label').html(`<i>${file.name}</i>`);
    if(file.size > 5120000) {
        $('#attachment_settlementError').addClass('is-invalid');
        $('#attachment_settlementError').find('strong').text('File size exceeds 5 MB')
    } else {
        $('#attachment_settlementError').removeClass('is-invalid');
        $('#attachment_settlementError').find('strong').text('');
    }
});

$(document).on('click', '.button-print', function () {
    let idOfficialTravel = $(this).attr('id-official-travel');
    let url = "{{route('settlementtravel.print')}}?id_official_travel="+idOfficialTravel;
    window.open(url, '_blank').focus();
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