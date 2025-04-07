@extends('adminlte::page')
@section('title', 'Quick Additions')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
        pointer-events: none;
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

    .form-group.required label:after { 
        content:" *";
        color:red;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Quick Additions
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="import" class="import btn btn-sm btn-success"><i class="fas fa-upload"></i> Import Asset</button>
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Asset</button>
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
<div class="modal fade" id="modalImport" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1605;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Asset</h5>
            </div>
            <div class="modal-body">
                <form class="col" id="importForm">
                    {{ csrf_field() }}
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <label>Template</label>
                        </div>
                        <div class="col">
                            <button type="button" class="btn btn-sm btn-success" onClick="downloadTemplate()">
                                <i class="fa fa-file-excel"></i>
                                <span>Download</span>
                            </button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="import_attachment">Attachment</label>
                        </div>
                        <div class="col">
                            <input type="file" name="import_attachment" id="import_attachment" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary btn-submit-import">
                    <i class="fa fa-paper-plane"></i>
                    <span>Submit</span>
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalViewImage" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1610;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalViewImageTitle">Preview %note%</h5>
            </div>
            <div class="modal-body">
                <img src="#" alt="Image Asset" id="previewImageAsset">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalAdd" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Asset Quick Addition</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_asset" id="id_asset" class="pk">
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="asset_number">Asset Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="asset_number" id="asset_number" class="form-control form-control-sm store-change" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="description">Description</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="current_units">Units</label>
                                </div>
                                <div class="col cont">
                                    <input type="number" min="1" max="1" value="1" name="current_units" id="current_units" class="form-control form-control-sm store-change non-editable" style="width:100%" readonly>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="manufacture_name">Manufacture Name</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="manufacture_name" id="manufacture_name" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="model_number">Model Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="model_number" id="model_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="serial_number">Serial Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="serial_number" id="serial_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="tag_number">Tag Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="tag_number" id="tag_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="warranty_number">Warranty Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="warranty_number" id="warranty_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="warranty_date">Warranty Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="warranty_date" id="warranty_date" class="form-control form-control-sm store-change date non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="tax_expired_date">Tax Expired Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="tax_expired_date" id="tax_expired_date" class="form-control form-control-sm store-change date" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="receiving_date">Receiving Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="receiving_date" id="receiving_date" class="form-control form-control-sm store-change date" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="tax_expired_date">Property Type</label>
                                </div>
                                <div class="col cont">
                                    <select name="property_type" id="property_type" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="bought">Bought</label>
                                </div>
                                <div class="col cont">
                                    <select name="bought" id="bought" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="depreciation_flag">Depreciation Flag</label>
                                </div>
                                <div class="col cont">
                                    <input type="checkbox" name="depreciation_flag" id="depreciation_flag" class="form-control form-control-sm store-change non-editable hide-bound-elements" style="width:100%;width:20px;height:20px;">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_depreciation_method">Depreciation Method</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_depreciation_method" id="id_depreciation_method" class="form-control form-control-sm store-change dropdown bind-true bind-depreciation_flag non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="depreciation_start_date">Depreciation Start Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="depreciation_start_date" id="depreciation_start_date" class="form-control form-control-sm store-change date bind-true bind-depreciation_flag non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="life_in_month">Life</label>
                                </div>
                                <div class="col cont">
                                    <div class="input-group input-group-sm">
                                        <input type="number" min="0" name="life_in_month" id="life_in_month" class="form-control form-control-sm bind-true bind-depreciation_flag non-editable">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                Months
                                            </span>
                                        </div>
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="salvage_type">Salvage Type</label>
                                </div>
                                <div class="col cont">
                                    <select name="salvage_type" id="salvage_type" class="form-control form-control-sm store-change dropdown bind-true bind-depreciation_flag non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="salvage_value">Salvage Value</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="salvage_value" id="salvage_value" class="form-control form-control-sm store-change money bind-true bind-depreciation_flag non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="queue_process_status">Queue Process Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="queue_process_status" id="queue_process_status" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="id_asset_category">Asset Category</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_asset_category" id="id_asset_category" class="form-control form-control-sm store-change dropdown non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="asset_type">Asset Type</label>
                                </div>
                                <div class="col cont">
                                    <select name="asset_type" id="asset_type" class="form-control form-control-sm store-change dropdown non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="id_asset_group">Asset Group</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_asset_group" id="id_asset_group" class="form-control form-control-sm store-change dropdown non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="original_cost">Acquisition Cost</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="original_cost" id="original_cost" class="form-control form-control-sm store-change money non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="current_cost">Current Cost</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="current_cost" id="current_cost" class="form-control form-control-sm store-change money non-editable" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="adjusted_cost">Adjustment Cost</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="adjusted_cost" id="adjusted_cost" class="form-control form-control-sm store-change money non-editable" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="depreciation_cost">Depreciation Cost</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="depreciation_cost" id="depreciation_cost" class="form-control form-control-sm store-change money" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_parent_asset">Parent Asset</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_parent_asset" id="id_parent_asset" class="form-control form-control-sm" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="in_used_flag">In Used Flag</label>
                                </div>
                                <div class="col cont">
                                    <input type="checkbox" name="in_used_flag" id="in_used_flag" class="form-control form-control-sm store-change non-editable" style="width:100%;width:20px;height:20px;">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="partner_name">Partner Name</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="partner_name" id="partner_name" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="purchase_invoice_number">Purchase Invoice</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="purchase_invoice_number" id="purchase_invoice_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="project_number">Project Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="project_number" id="project_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="batch_number">Batch Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="batch_number" id="batch_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="receiving_number">Receiving Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="receiving_number" id="receiving_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="ownership">Ownership</label>
                                </div>
                                <div class="col cont">
                                    <select name="ownership" id="ownership" class="form-control form-control-sm non-editable" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 show-leased">
                                <div class="col cont">
                                    <label for="leased_number">Lease Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="leased_number" id="leased_number" class="form-control form-control-sm store-change non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 show-leased">
                                <div class="col cont">
                                    <label for="lease_effective_date">Lease Effective Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="lease_effective_date" id="lease_effective_date" class="form-control form-control-sm store-change date non-editable" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 show-leased">
                                <div class="col cont">
                                    <label for="lease_expired_date">Lease Expired Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="lease_expired_date" id="lease_expired_date" class="form-control form-control-sm store-change date" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 show-leased">
                                <div class="col cont">
                                    <label for="lease_contract_expired_date">Lease Contract Expired Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="lease_contract_expired_date" id="lease_contract_expired_date" class="form-control form-control-sm store-change date" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2 form-group required">
                                <div class="col cont">
                                    <label for="status">Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="status" id="status" class="form-control form-control-sm" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="row mt-5" id="rows_table_cash_advance">
                            <div class="col-xl-12" id="tab_detail">
                              <div class="nav nav-tabs justify-content-left mb-4">
                                <a class="nav-item nav-link active" id="tab-employee" data-toggle="tab" href="#tab-pane-1">Assigned Employee
                                    <span class="error-tab text-red hidden">Error</span>
                                </a>
                                <a class="nav-item nav-link" id="tab-image" data-toggle="tab" href="#tab-pane-2">Image Asset
                                    <span class="error-tab text-red hidden">Error</span>
                                </a>
                                <a class="nav-item nav-link" id="tab-notes" data-toggle="tab" href="#tab-pane-3">Additional Info
                                    <span class="error-tab text-red hidden">Error</span>
                                </a>
                              </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tab-pane-1">
                                        <div class="table-responsive col-md-12"  style="overflow:auto;">
                                            <div class="pull-right mb-2">
                                                <button type="button" class="btn btn-sm btn-primary" id="addAssignedEmployee"><i class="fa fa-plus"></i> Add Assigned Employee</button>
                                            </div>
                                            <table id="assignedEmployeeTable" class="table table-hover table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th style="width:150px;">Transaction Date</th>
                                                        <th style="width:150px;">Employee</th>
                                                        <th>Units</th>
                                                        <th style="width:150px;">Branch</th>
                                                        <th style="width:150px;">Location</th>
                                                        <th style="width:150px;">Room</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="assignedEmployeeTableBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab-pane-2">
                                        <div class="table-responsive col-md-12"  style="overflow:auto;">
                                            <div class="pull-right mb-2">
                                                <button type="button" class="btn btn-sm btn-primary" id="addImage"><i class="fa fa-plus"></i> Add Image</button>
                                            </div>
                                            <table id="imageTable" class="table table-hover table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th style="width:250px;">Attachment</th>
                                                        <th style="width:300px;">Note</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="imageTableBody"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tab-pane-3">
                                        <div class="table-responsive col-md-12"  style="overflow:auto;">
                                            <div class="pull-right mb-2">
                                                {{-- <button type="button" class="btn btn-sm btn-primary" id="addImage"><i class="fa fa-plus"></i> Add Info</button> --}}
                                            </div>
                                            <table id="notesTable" class="table table-hover table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th style="min-width:250px;">Name</th>
                                                        <th style="min-width:300px;">Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="notesTableBody">
                                                    <tr>
                                                        <td class="notes-no">1</td>
                                                        <td class="notes-key">Purchase Date</td>
                                                        <td>
                                                            <input name="purchase_date" id="purchase_date" class="form-control form-control-sm date">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="notes-no">2</td>
                                                        <td class="notes-key">Reference Number</td>
                                                        <td>
                                                            <input name="reference_number" id="reference_number" class="form-control form-control-sm">
                                                        </td>
                                                    </tr>
                                                    <tr class="notes-row">
                                                        <td class="notes-no">3</td>
                                                        <td class="notes-key">Notes 1</td>
                                                        <td class="notes-val">
                                                            <textarea name="notes_1" id="notes_1" cols="30" rows="4" class="form-control form-control-sm"></textarea>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary btn-post"><i class="fas fa-calculator"></i> Post</button>
                <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-save"></i> <span id="saveLabel">Save</span></button>&nbsp;
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_transaction_date cont">
                <input type="hidden" name="detail[][id_employee_assigned]" class="detail-id_employee_assigned-input">
                <input type="text" name="detail[][transaction_date]" class="form-control form-control-sm detail-transaction_date-input date" style="width:100px;">
                <span class="error"></span>
            </td>
            <td class="detail_id_employee cont">
                <select name="detail[][id_employee]" class="form-control form-control-sm store-value detail-id_employee-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_unit_assigned cont">
                <input type="number" name="detail[][unit_assigned]" class="form-control form-control-sm store-value detail-unit_assigned-input" style="width:80px;">
                <span class="error"></span>
            </td>
            <td class="detail_id_branch cont">
                <select name="detail[][id_branch]" class="form-control form-control-sm store-value detail-id_branch-input" style="width:150px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_location cont">
                <select name="detail[][id_location]" class="form-control form-control-sm store-value detail-id_location-input" style="width:150px;"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_asset_location cont">
                <select name="detail[][id_asset_location]" class="form-control form-control-sm store-value detail-id_asset_location-input" style="width:150px;"></select>
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
<table id="sampleImageTable" style="display:none;">
    <tbody>
        <tr>
            <td class="image_no"></td>
            <td class="image_attachment cont">
                <input type="hidden" name="image[][id_image_asset]" class="image-id_image_asset-input">
                <input type="file" name="image[][attachment]" class="form-control form-control-sm image-attachment-input">
                <span class="error"></span>
            </td>
            <td class="image_note cont">
                <input type="text" name="image[][note]" class="form-control form-control-sm image-note-input">
                <span class="error"></span>
            </td>
            <td class="image_status cont">
                <select name="image[][status]" class="form-control form-control-sm image-status-input">
                </select>
                <span class="error"></span>
            </td>
            <td class="image_action cont"></td>
        </tr>
    </tbody>
</table>
@endsection
@section('scripts')
<script type="text/javascript">
    @include('assets.advanced_search')

    let assetCategories, assetGroups, depreciationMethods, assets, employees, branches = [];
    let toModify = ['id_employee_assigned', 'transaction_date', 'id_employee', 'unit_assigned', 'id_branch', 'id_location', 'id_asset_location', 'status'];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let queueProcessStatus = [
        { id: 'New', text: 'New' },
        { id: 'Confirm', text: 'Confirm' },
        { id: 'Post', text: 'Post' },
    ];
    let assetTypes = [
        { id: 'Capitalized', text: 'Capitalized' },
        { id: 'CIP', text: 'CIP' },
        { id: 'Expense', text: 'Expense' },
    ];
    let propertyTypes = [
        { id: 'Private', text: 'Private' },
        { id: 'Commercial', text: 'Commercial' }
    ];
    let boughts = [
        { id: 'New', text: 'New' },
        { id: 'Used', text: 'Used' },
    ];
    let ownerships = [
        { id: 'Owned', text: 'Owned' },
        { id: 'Leased', text: 'Leased' },
    ];
    let salvageTypes = [
        { id: 'Amount', text: 'Amount' },
        { id: 'Percent', text: 'Percent' },
    ];

    $('#property_type').empty().prepend('<option></option>').select2({
        data: propertyTypes,
        placeholder: 'Select Property Type',
        allowClear: true
    });

    $('#bought').empty().prepend('<option></option>').select2({
        data: boughts,
        placeholder: 'Select Option',
        allowClear: true
    });

    $('#salvage_type').empty().prepend('<option></option>').select2({
        data: salvageTypes,
        placeholder: 'Select Salvage Type',
        allowClear: true
    });

    $('#ownership').empty().prepend('<option></option>').select2({
        data: ownerships,
        placeholder: 'Select Ownership Type',
        allowClear: true
    });

    $('#asset_type').empty().prepend('<option></option>').select2({
        data: assetTypes,
        placeholder: 'Select Asset Type',
        allowClear: true
    });

    $('#queue_process_status').empty().prepend('<option></option>').select2({
        data: queueProcessStatus,
        placeholder: 'Select Queue Process Status',
        allowClear: true
    }).attr('readonly', true);

    $('#status').empty().select2({
        data: status,
    });

    function handleError(jqAjaxErrorInstance) {
        swal({
            icon: 'error',
            title: 'Error',
            text: jqAjaxErrorInstance?.responseJSON?.message,
        });
        if(jqAjaxErrorInstance.status == 422) {
            Object.entries(jqAjaxErrorInstance.responseJSON.errors).forEach((obj) => {
                if(obj[0].includes('.')) {
                    let target = `${obj[0].replace(".", "[").replaceAll(".", "][")}]`;
                    $(`[name='${target}']`).closest('.cont').find('.error').text(obj[1][0]);
                } else {
                    $(`#${obj[0]}`).closest('.cont').find('.error').text(obj[1][0]);
                }
            });
        }
    }

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('assets.quick_addition.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                assetCategories = res.data.asset_categories;
                assetGroups = res.data.asset_groups;
                depreciationMethods = res.data.depreciation_methods;
                employees = res.data.employees;
                branches = res.data.branches;
                if(modifyDependentElements) {
                    $('#id_asset_category').empty().prepend('<option></option>').select2({
                        data: res.data.asset_categories,
                        allowClear: true,
                        placeholder: 'Select Asset Category'
                    });
                    $('#id_asset_group').empty().prepend('<option></option>').select2({
                        data: res.data.asset_groups,
                        allowClear: true,
                        placeholder: 'Select Asset Group'
                    });
                    $('#id_depreciation_method').empty().prepend('<option></option>').select2({
                        data: res.data.depreciation_methods,
                        allowClear: true,
                        placeholder: 'Select Depreciation Method'
                    });
                    $('#id_parent_asset').empty().prepend('<option></option>').select2({
                        data: res.data.assets,
                        allowClear: true,
                        placeholder: 'Select Parent Asset'
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

    function notes() {
        for(let i = 0; i < 7; ++i) {
            let clone = $($('#notesTableBody').find('.notes-row')[0]).clone();
            clone.find('.notes-no').text(i+4);
            clone.find('.notes-key').text(`Notes ${i+2}`);
            clone.find('.notes-val').find('textarea').attr('id', `notes_${i+2}`).attr('name', `notes_${i+2}`);
            clone.appendTo($('#notesTableBody'));
        }
    };
    notes();

    $(document).on('change', '#id_approval', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Quick Add Asset');
        $('#addAssignedEmployee').show();
        $('.btn-post').hide();
        $('.non-editable').attr('readonly', false);
        $('.error').text('');
        $('#assignedEmployeeTableBody, #imageTableBody').empty();
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select, .pk').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false).trigger('change');
        });
        $('#modalAdd').find('#in_used_flag').attr('checked', true).trigger('change');
        $('#queue_process_status').val('New').trigger('change');
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
        clone.find('.detail-id_employee-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_branch-input').empty().prepend('<option></option>').select2({ 
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

    function cloneImageRow(append = false, appendTarget = null) {
        let clone = $('#sampleImageTable').find('tr').clone();
        clone.find('.image-status-input').empty().select2({ data: status });
        clone.find('.image_action').html(`
            <button type="button" class="btn btn-sm btn-danger delete-row"><i class="fas fa-trash"></i></button>
        `);
        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    function getEdit(idAsset) {
        $.ajax({
            url: "{{ route('assets.quick_addition.get_edit') }}",
            data: {
                id_asset: idAsset,
            },
            beforeSend: () => {
                $('.error').text('');
                $('#assignedEmployeeTableBody, #imageTableBody').empty();
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                if(res.data.queue_process_status != 'New' || res.data.assigned_employee.length > 0) {
                    $('#addAssignedEmployee').hide();
                } else {
                    $('#addAssignedEmployee').show();
                }
                res.data.assigned_employee?.forEach((assignedEmployee) => {
                    let clone = cloneDetailRow(false);
                    clone.find('.detail-id_employee_assigned-input').val(assignedEmployee.id_employee_assigned);
                    clone.find('.detail-transaction_date-input').val(assignedEmployee.transaction_date);
                    if(assignedEmployee.id_employee) {
                        clone.find('.detail-id_employee-input').val(assignedEmployee.id_employee).trigger('change');
                    }
                    clone.find('.detail-unit_assigned-input').val(assignedEmployee.unit_assigned);
                    clone.find('.detail-id_branch-input').val(assignedEmployee.id_branch).attr('id-location', assignedEmployee.id_location).trigger('change');
                    clone.find('.detail-id_location-input').attr('id-asset-location', assignedEmployee.id_asset_location).val(assignedEmployee.id_location);
                    clone.find('.detail-id_asset_location-input').val(assignedEmployee.id_asset_location);
                    // clone.find('input, select').attr('disabled', true);
                    clone.appendTo('#assignedEmployeeTableBody');
                });
                res.data.image?.forEach((image, i) => {
                    let clone = cloneImageRow(false);
                    clone.find('.image-id_image_asset-input').val(image.id_image_asset);
                    clone.find('.image-attachment-input').hide().parent().append(`<button type="button" class="btn btn-warning btn-sm btn-view-image" path="/project/storage/app/public/${image.attachment}">View</button>`);
                    clone.find('.image-note-input').val(image.note);
                    clone.find('.image-status').val(image.status).trigger('change');
                    clone.find('input, select').attr('disabled', true);
                    clone.find('.image_action').empty();
                    clone.appendTo('#imageTableBody');
                });
                $('#imageTableBody').children().each((index, row) => {
                    $(row).find('.image_no').text(index+1);
                    ["id_image_asset", "attachment", "note", "status"].forEach((key) => {
                        $(row).find(`.image-${key}-input`).attr('name', `image[${index}][${key}]`);
                    });
                });
                $('#assignedEmployeeTableBody > tr').each((index, element) => {
                    $(element).find('.detail_no').text(index+1);
                    $(element).find('input, select').each((_inputInex, inputField) => {
                        $(inputField).attr('name', $(inputField).attr('name').replaceAll('[]', `[${index}]`));
                    })
                })
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                    if(!$(element).is(':checked')) {
                        if($(element).val() == "true") {
                            $(element).trigger('click');
                        }
                    } else {
                        if($(element).val() != "true") {
                            $(element).trigger('click');
                        }
                    }
                })
                $('#modalAdd').find('select, .pk, .money').trigger('change');
            },
            error: handleError
        })
    }

    function postAsset() {
        $.ajax({
            url: "{{ route('assets.quick_addition.post') }}",
            data: new FormData($('#assetGroupForm')[0]),
            dataType: "JSON",
            processData: false,
            contentType: false,
            type: 'POST',
            beforeSend: () => {
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                $('#modalAdd').modal('hide');
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Post Success',
                    text: res.message,
                });
                // getEdit($('#id_asset').val());
            },
            error: handleError
        })
    }

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset');
        $('.btn-post').show();
        // $('.non-editable').attr('readonly', true);
        getEdit($(this).attr('id-asset'));
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '.btn-post', function () {
        if($('#depreciation_flag').is(':checked') && $('#life_in_month').val() == '0') {
            swal({
                icon: 'warning',
                title: 'Confirm Submission',
                text: 'Asset life is 0. Are you sure you want to post this asset?',
                buttons: ['Cancel', 'Yes, Proceed to Post'],
            }).then((confirm) => {
                if(confirm) {
                    postAsset();
                }
            })
        } else {
            postAsset()
        }
    });

    $(document).on('click', '#addAssignedEmployee', function() {
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
        clone.find('.detail-id_employee-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_branch-input').empty().prepend('<option></option>').select2({ 
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

    $(document).on('click', '#addImage', function () {
        cloneImageRow(true, $('#imageTableBody'));
        $('#imageTableBody').children().each((index, row) => {
            $(row).find('.image_no').text(index+1);
            ["id_image_asset", "attachment", "note", "status"].forEach((key) => {
                $(row).find(`.image-${key}-input`).attr('name', `image[${index}][${key}]`);
            });
        });
    })

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
        if($('#depreciation_flag').is(':checked') != props.depreciation_flag) {
            $('#depreciation_flag').trigger('click');
        }
        $('#id_depreciation_method').val(props.id_depreciation_method).trigger('change');
        $('#life_in_month').val(props.life_in_month);
        $('#salvage_type').val(props.salvage_type).trigger('change');
        $('#salvage_value').val(props.salvage_value);
    });

    $(document).on('change', '#id_asset_category', function () {
        if(!$(this).val() || $('#id_asset').val() != "") {
            return;
        }

        assetCategories.forEach((assetCategory) => {
            if($(this).val() == assetCategory.id) {
                $('#asset_type').val(assetCategory.category_type).trigger('change');
            }
        });
    });

    $(document).on('click', '.btn-view-image', function() {
        $('#modalViewImageTitle').text('Preview '+$(this).attr('title'));
        $('#previewImageAsset').attr('src', $(this).attr('path'));
        $('#modalViewImage').modal('show');
    });

    $(document).on('change', '.detail-id_branch-input', function () {
        $.ajax({
            url: "{{ route('assets.quick_addition.get_data') }}",
            data: {
                id_branch: $(this).val(),
            },
            success: (res) => {
                console.log($(this).attr('id-location'));
                $(this).closest('tr').find('.detail-id_location-input').empty().prepend('<option></option>').select2({ 
                    data: res.data.locations,
                    allowClear: true,
                    placeholder: 'Select Location',
                });
                if($(this).attr('id-location')) {
                    $(this).closest('tr').find('.detail-id_location-input').val($(this).attr('id-location')).trigger('change')
                }
            }
        })
    });

    $(document).on('change', '.detail-id_location-input', function () {
        $.ajax({
            url: "{{ route('assets.quick_addition.get_data') }}",
            data: {
                id_location: $(this).val(),
            },
            success: (res) => {
                $(this).closest('tr').find('.detail-id_asset_location-input').empty().prepend('<option></option>').select2({ 
                    data: res.data.asset_locations,
                    allowClear: true,
                    placeholder: 'Select Asset Location',
                });
                if($(this).attr('id-asset-location')) {
                    $(this).closest('tr').find('.detail-id_asset_location-input').val($(this).attr('id-asset-location')).trigger('change')
                }
            }
        })
    });

    $(document).on('change', '#ownership', function() {
        if($(this).val() == "Leased") {
            $('.show-leased').show();
        } else {
            if(!$('.pk').val()) {
                $('.show-leased').find('input').val('');
            }
            $('.show-leased').hide();
        }
    });

    $(document).on('change', '#receiving_date', function() {
        if($(this).val()) {
            $.ajax({
                url: "{{ route('assets.quick_addition.get_data') }}",
                data: {
                    receiving_date: $(this).val(),
                },
                success: (res) => {
                    $('#depreciation_start_date').data('daterangepicker').setStartDate(res.data.depreciation_start_date);
                    $('#depreciation_start_date').data('daterangepicker').setEndDate(res.data.depreciation_start_date);
                }
            })
        }
    });

    $(document).on('click', '#submitApply', function() {
        $('#assetGroupForm').submit();
    });

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.quick_addition.save') }}",
            type: 'POST',
            data: new FormData($('#assetGroupForm')[0]),
            dataType: "JSON",
            processData: false,
            contentType: false,
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
            if($(this).hasClass('hide-bound-elements')) {
                $(`.bind-true.bind-${id}`).closest('.row').show();
                $(`.bind-false.bind-${id}`).closest('.row').hide();
            }
        } else {
            $(`.bind-true.bind-${id}`).attr('readonly', true);
            $(`.bind-false.bind-${id}`).attr('readonly', false);
            if($(this).hasClass('hide-bound-elements')) {
                $(`.bind-true.bind-${id}`).closest('.row').hide();
                $(`.bind-false.bind-${id}`).closest('.row').show();
            }
        }
    })

    $(document).on('click', '.import', function() {
        $('#modalImport').modal('show');
    });

    $(document).on('click', '.btn-submit-import', function() {
        $('#importForm').submit();
    });

    $(document).on('submit', '#importForm', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.quick_addition.import') }}",
            type: 'POST',
            data: new FormData($('#importForm')[0]),
            dataType: "JSON",
            processData: false,
            contentType: false,
            beforeSend: () => {
                $('.error').empty();
                $('#loader').removeClass('hidden');
            },
            success: (res) => {
                $('#modalImport').modal('hide');
                $('#importForm')[0].reset();
                $('#assetGroupTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            },
            error: (err) => {
                handleError(err);
            },
            complete: () => {
                $('#loader').addClass('hidden');
            }
        })
    });

    function downloadTemplate() {
        window.open("{{ url('project/storage/app/public/upload/templates/FA - Template Upload Asset.xlsx') }}", "_blank");
    }

    $('#assetGroupTable').DataTable({
        responsive: true,
        serverSide: true,
        ajax: {
            url: "{{route('assets.quick_addition')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.', searchable: false },
            { data: 'asset_number', name: 'asset_number', title: 'Asset Number'},
            { 
                data: 'id_asset_category', 
                name: 'id_asset_category', 
                title: 'Asset Category', 
                render: (data, type, row) => {
                    if(row.asset_category) return row.asset_category.description;
                    else return '-';
                } 
            },
            { 
                data: 'asset_type', 
                name: 'asset_type', 
                title: 'Asset Type', 
            },
            { 
                data: 'asset_group.description', 
                name: 'id_asset_group', 
                title: 'Asset Group', 
                render: (data, type, row) => {
                    if(row.asset_group) return row.asset_group.description;
                    else return '-';
                } 
            },
            { data: 'description', name: 'description', title: 'Description' },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: null, 
                name: 'action', 
                title: 'Action',
                searchable: false,
                orderable: false,
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-asset="${row.id_asset}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection