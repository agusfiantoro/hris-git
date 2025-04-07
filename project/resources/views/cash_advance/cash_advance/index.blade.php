@extends('adminlte::page')
@section('title', 'Cash Advance')

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
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Cash Advance
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    <button type="button" more_type="New" class="new btn button-add new btn-sm btn-success"><i class="fas fa-plus"></i> Add Cash Advance</button>
                </div>
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
                        {{-- <th>Travel Type</th> --}}
                        <!-- <th>Request by</th> -->
                        <th data-priority="4">Reason</th>
                        <th data-priority="3">Approval Status</th>
                        <th>Settlement Clearance</th>
                        <th data-priority="1" align="center">Action</th>
                        </tr>
                    </thead>
                </table>
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
                                <select name="request[0][product]" class="request-product form-control form-control-sm" readonly>
                                </select>
                            </td>
                            <td>
                                <select name="request[0][branch]" class="request-branch form-control form-control-sm" readonly>
                                </select>
                            </td>
                            <td>
                              <input type="text" name="request[0][start_end_date]" class="request-startend form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][description]" class="request-description form-control form-control-sm" readonly>
                            </td>
                            <td>
                                <input type="text" name="request[0][notes]" class="request-notes form-control form-control-sm" readonly>
                            </td>
                            <td>
                              <input type="text" name="request[0][currencyrate]" class="request-currencyrate form-control form-control-sm" readonly>
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
                            <td>
                                <input type="text" name="settlement[0][price]" class="settlement-price form-control form-control-sm" style="width:100px">
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
                            <td style="width:180px;">
                                <input type="file" name="settlement[0][attachment]" class="settlement-attachment form-control-file" style="width:180px;">
                                <span class="settlement-attachment-download" style="display:none;">
                                    <a href="#" target="_blank"></a>
                                </span>
                                <span class="invalid-feedback attachment-error" role="alert">
                                    <strong></strong>
                                </span>
                            </td>
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
                                <label class="col-sm-4 col-form-label">Reference No.</label>
                                <div class="col-sm-8" id="container-cash-advance-reference">
                                    <input type="hidden" hidden="" id="id_cash_advance" name="id_cash_advance">
                                    <input disabled id="cash_advance_reference" name="cash_advance_reference" class="form-control form-control-sm reference-number" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="cash_advance_referenceError">
                                        <strong></strong>
                                    </span>
                                </div>
                                <div class="col-sm-8 hidden" id="container-cash-advance-type">
                                  <select id="cash_advance_type" name="cash_advance_type" class="form-control form-control-sm cash-advance-type" style="width: 100%;">
                                  </select>
                              </div>
                            </div>
                            {{-- <div class="row">
                                <label class="col-sm-4 col-form-label">Official Travel</label>
                                <div class="col-sm-8">
                                    <input type="hidden" hidden="" id="id_official_travel" name="id_official_travel">
                                    <input disabled id="reference_number" name="reference_number" class="form-control form-control-sm reference-number" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="reference_numberError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div> --}}
                            <div class="row">
                              <label class="col-sm-4 col-form-label">Region</label>
                              <div class="col-sm-8">
                                  <select disabled id="region" name="id_region" class="form-control form-control-sm region" style="width: 100%;">
                                  </select>
                                  <span class="invalid-feedback" role="alert" id="regionError">
                                      <strong></strong>
                                  </span>
                              </div>
                            </div>
                            <div class="row">
                              <label class="col-sm-4 col-form-label">Start-End Date</label>
                              <div class="col-sm-8">
                                  <input id="start_end_date" name="start_end_date" class="form-control form-control-sm start-end-date" style="width: 100%;">
                                  <span class="invalid-feedback" role="alert" id="start_end_dateError">
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
                                    <select disabled id="department" name="id_dept" class="form-control form-control-sm department" style="width: 100%;">
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
                                    <select readonly id="request_currency" name="request_currency" class="form-control form-control-sm request_currency" style="width: 100%;">
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
                                    <textarea disabled class="form-control form-control-sm cash-advance-notes" id="cash_advance_notes" rows="4" name="cash_advance_notes" style="text-transform: uppercase;"></textarea>
                                    <span class="invalid-feedback" role="alert" id="cash_advance_notesError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Approval Hierarchy</label>
                                <div class="col-sm-8">
                                    <select readonly id="approval_hierarchy" name="approval_hierarchy" class="form-control form-control-sm approval-hierarchy" style="width: 100%;">
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
                                    <button type="button" role="button" class="btn btn-sm btn-primary mb-2" style="float: right;" id="button-add-expense-request">
                                      <i class="fa fa-plus"></i> Add Expense Request
                                    </button>
                                    <div class="table-responsive col-md-12"  style="overflow:auto;">
                                        <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                                            <thead>
                                                <tr>
                                                    <th style="width:50px;">No.</th>
                                                    <th>Category</th>
                                                    <th>Branch</th>
                                                    <th>Start - End Date</th>
                                                    <th>Description</th>
                                                    <th>Notes</th>
                                                    <th>Currency Rate</th>
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
                                                    <th>Attachment</th>
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
<script type="text/javascript">
var global_products = [];
var global_branches = [];
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
var global_locked_price = [];
$('#label_button_action_submit').text("Submit");
$('#label_button_action_save').text("Save Draft");


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
        url: "{{ route('cash_advance') }}",
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
		{ data: 'reason_notes', name: 'reason_notes' },
        { data: 'approval_desc', name: 'approval_desc', className: 'text-center', render: function ( data, type, row ) {	
				if(data == 'Approved'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(data == 'Cancel'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(data == 'Partial_Approved'){
					return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(data == 'Rejected'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(data == 'Revised'){
					return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
                else if(data == null || data == 'null') {
                    return '<span class="badge badge-info" style="padding:5px;font-size:12px;">Draft</span>';
                }
				else{
					return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
				}
			}
		},
        {
            data: 'settlement_status',
            name: 'settlement_status',
            className: 'text-center',
            render: function(data, type, row) {
                if(row.settlement_status == "Clear") {
                    return `<span class="badge badge-success" style="padding:5px;font-size:12px;">Cleared</span>`;
                } else if(row.settlement_status == "Not_Clear") {
                    return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Not Clear</span>';
                }
                return `<span class="badge badge-secondary" style="padding:5px;font-size:12px;">${row.settlement_status}</span>`;
                
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

$(document).on('change', '#region', function() {
  $('#table_request').children().each(function (i, element) {
    $(element).find('.request-branch').empty().prepend('<option></option>').select2({
      placeholder: 'Select Category',
      data: global_branches.filter((branch) => branch.id_region == $('#region').val()),
      allowClear: true,
    });
  })
})

$(document).on('click', '#button-add-expense-request', () => {
  if($('#start_end_date').val() == '' || !$('#start_end_date').val()) {
    swal({
        icon: 'error',
        text: 'Cash Advance Start-End Date is required before adding new expense request.'
    });
    return;
  }
  let clone = $('#table-request-sample').clone();
  clone.find('input').attr('readonly', false);
  clone.find('select').attr('readonly', false);
  clone.find('.request-price').attr('disabled', false);
  clone.find('.request-price').attr('readonly', true);
  clone.find('.request-total').attr('disabled', true);
  clone.find('.request-description').attr('disabled', true);
  clone.find('.request-qty').attr('readonly', true);
  clone.find('.request-product').prepend('<option></option>').select2({
    placeholder: 'Select Category',
    data: global_products,
    allowClear: true,
  }).css('width', '100%');
  clone.find('.request-branch').prepend('<option></option>').select2({
    placeholder: 'Select Branch',
    data: global_branches.filter((branch) => branch.id_region == $('#region').val()),
    allowClear: true,
  }).css('width', '100%');
  let rangeDate = $('#start_end_date').val().split(" to ");
  clone.find('.request-startend').daterangepicker({
    drops: 'up',
    autoUpdateInput: false,
    minDate: rangeDate.length > 0 ? rangeDate[0] : new Date(),
    maxDate: rangeDate.length > 0 ? rangeDate[1] : new Date(),
    locale: {
      cancelLabel: 'Reset',
      format: 'YYYY-MM-DD',
      separator: ' to '
    }
  }).on('apply.daterangepicker', function(ev, picker) {
    var startDate = picker.startDate;
    var endDate = picker.endDate;
    var timeDiff = endDate - startDate;
    var daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));
    let productCode = null;
    global_products.forEach((product) => {
      if(product.id == $(this).closest('tr').find('.request-product').val()) {
        productCode = product.code;
      }
    });
    if(productCode != "CSM0000003") {
        daysDiff += 1;
    }
    if(timeDiff < 1) {
        daysDiff = 0;
    }
    clone.find('.request-qty').val(daysDiff).trigger('change');
    $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
  }).on('cancel.daterangepicker', function() {
    $(this).val('');
    element.find('.lama_menginap_input').val('');
  }).on('keydown.daterangepicker',function(e) {
    e.preventDefault();
  });
  clone.find('.request-action').html(`
    <button type="button" class="btn btn-danger expense-request-delete"><i class="fas fa-trash"></i></button>
  `);
  if($('#request_currency').val() == global_company_currency) {
    clone.find('.request-currencyrate').val(1).attr('readonly', true);
  } else {
    clone.find('.request-currencyrate').val(1).attr('readonly', false);
  }
  clone.appendTo('#table_request');
  $('#table_request tr').each(function (index) {
      $(this).find('.request-no').text(index + 1);
      $(this).find('.request-product').attr('name', `request[${index}][product]`);
      $(this).find('.request-branch').attr('name', `request[${index}][branch]`);
      $(this).find('.request-startend').attr('name', `request[${index}][start_end_date]`);
      $(this).find('.request-notes').attr('name', `request[${index}][notes]`);
      $(this).find('.request-currencyrate').attr('name', `request[${index}][currencyrate]`).attr('id', `request-currencyrate-${index}`);
      $(this).find('.request-qty').attr('name', `request[${index}][qty]`).attr('id', `request-qty-${index}`);
      $(this).find('.request-price').attr('id', `request-price-${index}`).attr('name', `request[${index}][price]`);
      $(this).find('.request-total').attr('id', `request-total-${index}`);
  });
});

$(document).on('click', '#button-add-settlement', () => {
    if($('#cash_advance_type').val() == 'cash_advance') {
      if(!$('#id_cash_advance').val() || $('#id_cash_advance').val() == '') {
        return swal({
          icon: 'error',
          text: 'You can only add settlement at this stage if type is "Reimburse".'
        });
      }
    }


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
    clone.find('.settlement-start-end').daterangepicker({
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
        $(element).find('.settlement-region').trigger('change');
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

function changeTotal(element, type = 'settlement') {
    let id = $(element.target).attr('id').split("-")[2];
    let qty = $('#'+type+'-qty-'+id).val();
    let price = unformatRupiah($('#'+type+'-price-'+id).val());
    let currencyRate = unformatRupiah($('#'+type+'-currencyrate-'+id).val());
    $(`#${type}-total-${id}`).val(global_company_currency.currency_symbol?global_company_currency.currency_symbol:''+formatRupiah(price*qty*currencyRate));

    let priceval = $('#'+type+'-price-'+id).val();
    // console.log('#settlement-price-'+id, priceval?priceval.toString().replace(".", ""):"", parseInt($('#settlement-price-'+id).val()));
    $('#'+type+'-price-'+id).val(formatRupiah(parseInt(priceval?priceval.toString().replace(".", ""):priceval)));
}

$(document).on('change', '.request-product', function() {
    $(this).parent().parent().find('.request-price').attr('readonly', false);
    global_locked_price.forEach((lock) => {
        if(lock.id_product == $(this).val()) {
            $(this).parent().parent().find('.request-price').attr('readonly', true);
        }
    });
    if($(this).parent().parent().find('.request-branch').val()) {
        let id_region = null;
        let code_product = null;
        let qty = $(this).parent().parent().find('.request-qty').val();
        global_branches.forEach((branch) => {
        if(branch.id == $(this).parent().parent().find('.request-branch').val()) {
            id_region = branch.id_region;
        }
        });
        global_products.forEach((product) => {
        if(product.id == $(this).val()) {
            code_product = product.code;
        }
        });
        $.ajax({
        url: "{{route('change_region_cashadvance')}}",
        data: {
            id_region,
            code_product
        },
        success: (res) => {
            if(res.length > 0) {
            $(this).parent().parent().find('.request-price').val(formatRupiah(Number.parseInt(res[0].max_price)))
            }
        }
        })
    }
});
$(document).on('change', '.request-branch', function() {
  if($(this).parent().parent().find('.request-product').val()) {
    let id_region = null;
    let code_product = null;
    global_branches.forEach((branch) => {
      if(branch.id == $(this).val()) {
        id_region = branch.id_region;
      }
    });
    global_products.forEach((product) => {
      if(product.id == $(this).parent().parent().find('.request-product').val()) {
        code_product = product.code;
      }
    });
    $.ajax({
      url: "{{route('change_region_cashadvance')}}",
      data: {
        id_region,
        code_product
      },
      success: (res) => {
        if(res.length > 0) {
          $(this).parent().parent().find('.request-price').val(formatRupiah(Number.parseInt(res[0].max_price)))
        }
      }
    })
  }
});
$(document).on('change', '.request-qty', (element) => {
    changeTotal(element, 'request')
});
$(document).on('change', '.request-price', (element) => {
    changeTotal(element, 'request')
});
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
        console.log(key_temp);
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
        url: `{{ route('cash_advance.update') }}`,
        type: 'POST',
        dataType: "JSON",
        data: new FormData(this),
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
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
    $('#submit-type').val('submit');
    $('#travelForm').submit();
});
$('#modal_form_settlement').on('click', '#saveForm', () => {
    $('#submit-type').val('draft');
    $('#travelForm').submit();
});

$(document).on('click', '.expense-request-delete', function () {
    $(this).parent().closest('tr').remove();
    $('#table_request tr').each(function (index) {
        $(this).find('.request-no').text(index + 1);
    });
    return true;
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
    global_products.forEach((product) => {
        if(product.id == $(this).val()) {
            $(this).attr('product-code', product.code);
        }
    });
    // $(this).parent().parent().find('.settlement-start-end').trigger('apply.daterangepicker');
    calculateQty($(this).parent().parent());
    
});

$(document).on('click', '.button-add', function() {
    $('#region').attr('disabled', false).closest('.row').css('display', 'flex');
    $('#button-add-expense-request').css('display', 'block');
    $('#tab-refund').addClass('hidden');
    $('#tab-payment').addClass('hidden');
    $('#start_end_date').attr('disabled', false).val('');
    $('#id_cash_advance').val('');
    $('#submitForm').css('display', 'block');
    $('#saveForm').css('display', 'block');
    $('#cash_advance_notes').attr('disabled', false);
    $('#container-cash-advance-reference').addClass('hidden');
    $('#container-cash-advance-type').removeClass('hidden');
    $('#cash_advance_type').empty().select2({
      data: [
        {id: 'cash_advance', text: 'Cash Advance'},
        {id: 'reimburse', text: 'Reimburse'}
      ]
    });
    $('#start_end_date').daterangepicker({
        drops: 'down',
        autoUpdateInput: false,
        // minDate: global_official_travel_start,
        // maxDate: global_official_travel_end,
        // singleDatePicker: true,
        locale: {
          cancelLabel: 'Reset',
          format: 'YYYY-MM-DD',
          separator: ' to '
        }
    }).on('apply.daterangepicker', function (ev, picker) {
      $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
    }).on('cancel.daterangepicker', function() {
      $(this).val('');
    }).on('keydown.daterangepicker',function(e) {
      e.preventDefault();
    });
    $('#table_request').empty();
    $('#table_settlement').empty();
    $('#table_transport_accommodation').empty();
    $('#table_refund').empty();
    $('#table_payment').empty();

    getFormData();
})

function getFormData() {
  $.ajax({
    url: "{{route('cash_advance.get_form_data')}}",
    success: (res) => {
      global_products = res.categories;
      global_branches = res.branch;
      global_regions = res.region;
      global_currencies = res.currency;
      global_company_currency = res.default_currency_id;
      global_locked_price = res.locked_price;
      $('#transaction_date').val('');
      $('#approval_by').empty().select2({
        data: [{id: res.approval.id_employee_approval, text: res.approval.name}]
      }).attr('disabled', false).attr('readonly', true);
      $('#department').empty().select2({
        data: [{id: res.employee.id_dept, text: res.employee.dept}]
      }).attr('disabled', false).attr('readonly', true);
      $('#region').prepend('<option></option>').select2({
        data: res.region,
        placeholder: 'Select Region',
        allowClear: true,
      }).attr('disabled', false);
      $('#position_detail').empty().select2({
        data: [{id: res.employee.id_position, text: res.employee.position}]
      });
      $('#request_by').empty().select2({
        data: [{id: res.employee.id_employee, text: res.employee.name}]
      });
      $('#request_currency').empty().select2({
        data: res.currency
      }).val(res.default_currency_id).trigger('change');//.attr('disabled', false);
      $('#approval_hierarchy').empty().select2({
        data: [res.approval.hierarchy]
      });
      $('#modal_form_settlement').modal('show');
    }
  })
}

$(document).on('click', '.button-view', function() {
    let idCashAdvance = $(this).attr('id-cash-advance');
    getFormData();
    $('#tab-refund').removeClass('hidden');
    $('#tab-payment').removeClass('hidden');
    $('#region').attr('disabled', true).closest('.row').css('display', 'none');
    $('#button-add-expense-request').css('display', 'none');
    settlementData = [];
    $('#container-cash-advance-reference').removeClass('hidden');
    $('#container-cash-advance-type').addClass('hidden');
    $('#submitForm').css('display', 'block');
    $('#saveForm').css('display', 'block');
    $('#button-add-settlement').css('display', 'block');
    $('#modal_form_settlement').modal('show');
    $('#billing_letter_row').addClass('hidden');
    $.ajax({
        url: '{{ route("cash_advance.get_data") }}',
        data: {
            id_cash_advance: idCashAdvance
        },
        beforeSend: () => {
            $('#loader').removeClass('hidden');
            $('#approval_by').empty();
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
            global_official_travel_start = null;
            global_official_travel_end = null;
            $('#start_end_date').attr('disabled', true).val(response.start_end_date);
            $('#id_cash_advance').val(response.cash_advance.id_cash_advance);
            $('#cash_advance_reference').val(response.cash_advance.reference_number);
            // $('#request_total_amount').val(formatRupiah(parseInt(response.official_travel.hca_total_expense_request_amount)));
            // $('#reference_number').val(response.cash_advance.reference_number);
            $('#transaction_date').val(response.cash_advance.transaction_date);
            $('#cash_advance_notes').val(response.cash_advance.reason_notes);
            $('#accounting_date').val(response.cash_advance.accounting_date);
            $('#clearing_amount').val(response.cash_advance.clearing_amount);
            $('#payment_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_payment_amount)));
            $('#total_settlement').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_settlement_amount)));
            $('#total_taxed_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_tax_amount)));
            $('#total_cash_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_cash_request_amount)));
            $('#request_total_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_expense_request_amount)));
            $('#clearing_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_clearing_amount)));
            $('#difference_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_difference_amount)));
            $('#total_base_currency_tax').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_base_currency_tax_amount)));
            $('#maximum_clearing_date').val(response.cash_advance.maximum_clearing_date);
            $('#total_travel_request_amount').val(global_company_currency.currency_symbol+' '+formatRupiah(parseInt(response.cash_advance.total_travel_request_amount)));
            $('#payment_currency').empty().prepend('<option><option>').select2({
                data: response.currencies.all
            });
            $('#settlement_status').empty().select2({
                data: [
                    {id: "Not_Clear", text: "Not Clear"},
                    {id: "Clear", text: "Cleared"}
                ]
            }).val(response.cash_advance.settlement_status).trigger('change');
            if(response.cash_advance.settlement_status == "Clear") {
                $('#button-add-settlement').css('display', 'none');
            }
            $('#request_currency').empty().select2({
                data: response.currencies.all
            }).val(response.cash_advance.id_currency_cash_advance).trigger('change');
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
            }).val(response.cash_advance.payment_status).trigger('change');
            
            if(response.cash_advance.approval_desc == "Approved") {
                $('#approval_status_container').html("<span class='badge badge-success'>Approved</span>");
            } else if(response.cash_advance.approval_desc == "Rejected") {
                $('#approval_status_container').html("<span class='badge badge-danger'>Rejected</span>");
            } else if(response.cash_advance.approval_desc == null) {
                $('#approval_status_container').html("<span class='badge badge-info'>Draft</span>");
            } else {
                $('#approval_status_container').html("<b>"+response.cash_advance.approval_desc+"</b>")
            }
            if(response.cash_advance.is_validate) {
                $('#validation_status_container').html("<span class='badge badge-success'>Valid</span>");
            } else {
                $('#validation_status_container').html("<span class='badge badge-danger'>Invalid</span>");
            }
            if(response.cash_advance.start_refund_date) {
                $('#billing_letter_row').removeClass('hidden');
                $('#billing_letter_container').html(`
                        <button type="button" onClick="getBillingLetter(${response.official_travel.id_cash_advance})" class="btn btn-sm btn-primary">Download</button>
                    `);
            }

            $('#position_detail').empty().select2({
                data: [
                    {id: null, text: response.employee.desc_position}
                ]
            });
            $('#approval_hierarchy').empty().select2({
                data: [
                    response.approval.hierarchy
                ]
            });
            $('#approval_by').empty().select2({
                data: [
                    {id: response.approval.id_detail_chief, text: response.approval.name}
                ]
            });
            $('#request_by').empty().select2({
                data: [
                    {id: response.employee.id_employee, text: response.employee.name}
                ]
            });
            $('#department').empty().select2({
                data: [
                    {id: response.employee.desc_dept, text: response.employee.desc_dept}
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
            
            response.expense_request.forEach((data, i) => {
                let index = i+1;
                let notes = data.notes.split(";");
                let clone = $('#table-request-sample').clone();
                clone.find('.request-no').attr('id', 'request-no-' + i);
                clone.find('.request-no').text(index);
                clone.find('.request-product').attr('id', 'request-product-' + i);
                clone.find('.request-product').attr('name', `request[${i}][product]`);
                clone.find('.request-product').select2({
                  data: response.products
                });
                clone.find('.request-product').val(data.id_product).trigger('change');
                clone.find('.request-branch').select2({
                  data: global_branches
                }).val(data.id_branch).attr('name', `request[${i}][branch]`).trigger('change');
                clone.find('.request-startend').attr('name', `request[${i}][start_end_date]`).val(data.description.replace(";", ""));
                clone.find('.request-currencyrate').attr('name', `request[${i}][currencyrate]`).val(data.currency_rate);
                clone.find('.request-description').attr('id', 'request-description-' + i);
                clone.find('.request-description').attr('name', `request[${i}][description]`);
                clone.find('.request-description').val(data.description);
                clone.find('.request-notes').attr('id', 'request-notes-' + i);
                clone.find('.request-notes').attr('name', `request[${i}][notes]`);
                clone.find('.request-notes').val(data.notes);
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

                    if(!settlement.is_validate && !response.cash_advance.is_validate) {
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
                        $(element).find('.settlement-product, .settlement-region, .settlement-branch, .settlement-qty, .settlement-unit, .settlement-currency, .settlement-price, .settlement-attachment')
                        .attr('readonly', false).attr('disabled', false);
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

                    if(!settlement.is_validate && !response.cash_advance.is_validate) {
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
                        $(element).find('.settlement-product, .settlement-region, .settlement-branch, .settlement-qty, .settlement-unit, .settlement-currency, .settlement-price, .settlement-attachment')
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