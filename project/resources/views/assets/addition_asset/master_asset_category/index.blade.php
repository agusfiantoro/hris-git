@extends('adminlte::page')
@section('title', 'Master Asset Category')

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
                <h5 class="card-title">Master Asset Category
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Asset Category</button>
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="depreciationMethodTable" style="width: 100%;">
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
                <h5 class="modal-title" id="modalEditTitle">Add Asset Category</h5>
            </div>
            <div class="modal-body">
                <form id="assetCategoryForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_asset_category" id="id_asset_category">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="asset_category_code">Code</label>
                            </div>
                            <div class="col">
                                <input type="text" name="asset_category_code" id="asset_category_code" class="form-control form-control-sm">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="description">Description</label>
                            </div>
                            <div class="col">
                                <input type="text" name="description" id="description" class="form-control form-control-sm">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="category_type">Category Type</label>
                            </div>
                            <div class="col">
                                <select name="category_type" id="category_type" class="form-control form-control-sm bind-visibility" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="ownership">Ownership</label>
                            </div>
                            <div class="col">
                                <select name="ownership" id="ownership" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="property_type">Property Type</label>
                            </div>
                            <div class="col">
                                <select type="text" name="property_type" id="property_type" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="asset_classification">Asset Classification</label>
                            </div>
                            <div class="col">
                                <select type="text" name="asset_classification" id="asset_classification" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="physical_inventory_flag">Physical Inventory</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" name="physical_inventory_flag" id="physical_inventory_flag" class="form-control form-control-sm pull-left">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="depreciation_flag">Depreciates</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" name="depreciation_flag" id="depreciation_flag" class="form-control form-control-sm pull-left bind-visibility">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_method">Depreciation Method</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_method" id="id_depreciation_method" class="form-control form-control-sm bind-visibility-depreciation_flag bind-visibility-true-depreciation_flag" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="life_in_month">Life</label>
                            </div>
                            <div class="col">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" name="life_in_month" id="life_in_month" class="form-control form-control-sm">
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
                            <div class="col">
                                <label for="status">Status</label>
                            </div>
                            <div class="col">
                                <select name="status" id="status" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_asset_cost_account">Asset Cost Account</label>
                            </div>
                            <div class="col">
                                <select name="id_asset_cost_account" id="id_asset_cost_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-Capitalized-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_asset_clearing_account">Asset Clearing Account</label>
                            </div>
                            <div class="col">
                                <select name="id_asset_clearing_account" id="id_asset_clearing_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-Capitalized-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_cip_cost_account">CIP Cost Account</label>
                            </div>
                            <div class="col">
                                <select name="id_cip_cost_account" id="id_cip_cost_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-CIP-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_cip_clearing_account">CIP Clearing Account</label>
                            </div>
                            <div class="col">
                                <select name="id_cip_clearing_account" id="id_cip_clearing_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-CIP-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_expense_cost_account">Expense Cost Account</label>
                            </div>
                            <div class="col">
                                <select name="id_expense_cost_account" id="id_expense_cost_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-Expense-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_expense_clearing_account">Expense Clearing Account</label>
                            </div>
                            <div class="col">
                                <select name="id_expense_clearing_account" id="id_expense_clearing_account" class="form-control form-control-sm mca bind-visibility-category_type bind-visibility-Expense-category_type" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_expense_account">Depreciation Expense Account</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_expense_account" id="id_depreciation_expense_account" class="form-control form-control-sm mca bind-visibility-depreciation_flag bind-visibility-true-depreciation_flag" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_reserve_account">Depreciation Reserve Account</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_reserve_account" id="id_depreciation_reserve_account" class="form-control form-control-sm mca bind-visibility-depreciation_flag bind-visibility-true-depreciation_flag" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_revaluation_amortization_account">Revaluation Amortization Account</label>
                            </div>
                            <div class="col">
                                <select name="id_revaluation_amortization_account" id="id_revaluation_amortization_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_revaluation_reserve_account">Revaluation Reserve Account</label>
                            </div>
                            <div class="col">
                                <select name="id_revaluation_reserve_account" id="id_revaluation_reserve_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_impairment_expense_account">Impairment Expense Account</label>
                            </div>
                            <div class="col">
                                <select name="id_impairment_expense_account" id="id_impairment_expense_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_impairment_reserve_account">Impairment Reserve Account</label>
                            </div>
                            <div class="col">
                                <select name="id_impairment_reserve_account" id="id_impairment_reserve_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_proceeds_sale_gain_or_loss_account">Proceeds Gain/Loss Account</label>
                            </div>
                            <div class="col">
                                <select name="id_proceeds_sale_gain_or_loss_account" id="id_proceeds_sale_gain_or_loss_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_proceeds_sale_clearing_account">Proceeds Sale Clearing Account</label>
                            </div>
                            <div class="col">
                                <select name="id_proceeds_sale_clearing_account" id="id_proceeds_sale_clearing_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_cost_removal_gain_or_loss_account">Cost Removal Gain/Loss Account</label>
                            </div>
                            <div class="col">
                                <select name="id_cost_removal_gain_or_loss_account" id="id_cost_removal_gain_or_loss_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_cost_removal_clearing_account">Cost Removal Clearing Account</label>
                            </div>
                            <div class="col">
                                <select name="id_cost_removal_clearing_account" id="id_cost_removal_clearing_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_retired_gain_or_loss_account">Retired Gain/Loss Account</label>
                            </div>
                            <div class="col">
                                <select name="id_retired_gain_or_loss_account" id="id_retired_gain_or_loss_account" class="form-control form-control-sm mca" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-save"></i> <span id="saveLabel">Save</span></button>&nbsp;
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    @include('assets.advanced_search')

    let assetClassifications = [
        { id: 'Tangible', text: 'Tangible' },
        { id: 'Intangible', text: 'Intangible' },
    ];
    let propertyTypes = [
        { id: 'Private', text: 'Private' },
        { id: 'Commercial', text: 'Commercial' },
    ];
    let ownerships = [
        { id: 'Owned', text: 'Owned' },
        { id: 'Leased', text: 'Leased' },
    ];
    let categoryTypes = [
        { id: 'Capitalized', text: 'Capitalized' },
        { id: 'CIP', text: 'CIP' },
        { id: 'Expense', text: 'Expense' },
    ];
    let chartAccounts = [];
    let depreciationMethod = [];
    let revaluationFlag = false;
    let impairmentFlag = false;

    function getAccounts(modifyDependentDropdowns = false) {
        $.ajax({
            url: "{{ route('assets.master_asset_category.get_accounts') }}",
            success: (res) => {
                revaluationFlag = res.data.config_settings.revaluation_flag;
                impairmentFlag = res.data.config_settings.impairment_flag;
                chartAccounts = res.data.accounts;
                depreciationMethod = res.data.depreciation_methods;
                console.log(revaluationFlag, impairmentFlag);
                if(revaluationFlag) {
                    $('#id_revaluation_amortization_account, #id_revaluation_reserve_account')
                        .closest('.row')
                        .show();
                } else {
                    $('#id_revaluation_amortization_account, #id_revaluation_reserve_account')
                        .closest('.row')
                        .hide();
                }

                if(impairmentFlag) {
                    $('#id_impairment_expense_account, #id_impairment_reserve_account')
                        .closest('.row')
                        .show();
                } else {
                    $('#id_impairment_expense_account, #id_impairment_reserve_account')
                        .closest('.row')
                        .hide();
                }
                if(modifyDependentDropdowns) {
                    $('#id_depreciation_method').empty().prepend('<option></option>').select2({
                        allowClear: true,
                        placeholder: 'Select Depreciation Method',
                        data: res.data.depreciation_methods,
                    });
                    if($('#id_depreciation_method').attr('default-value')) {
                        $('#id_depreciation_method').val($('#id_depreciation_method').attr('default-value')).trigger('change');
                    }
                    $('.mca').each((_i, element) => {
                        $(element).empty().prepend('<option></option>').select2({
                            allowClear: true,
                            placeholder: 'Select Account',
                            data: res.data.accounts,
                        });
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    })
                }
            },
            error: (err) => {
                swal({
                    icon: 'error',
                    title: 'Error Obtaining Chart Accounts',
                    text: err.responseJSON.message,
                });
            }
        });
    }

    $('#category_type').empty().prepend('<option></option>').select2({
        allowClear: true,
        placeholder: 'Select Category Type',
        data: categoryTypes,
    });
    $('#ownership').empty().prepend('<option></option>').select2({
        allowClear: true,
        placeholder: 'Select Ownership',
        data: ownerships,
    });
    $('#property_type').empty().prepend('<option></option>').select2({
        allowClear: true,
        placeholder: 'Select Property Type',
        data: propertyTypes,
    });
    $('#asset_classification').empty().prepend('<option></option>').select2({
        allowClear: true,
        placeholder: 'Select Asset Classification',
        data: assetClassifications,
    });

    getAccounts(true);

    $('#status').empty().prepend().select2({
        data: [
            { id: 'A', text: 'Active' },
            { id: 'I', text: 'Inactive' },
        ],
    });

    $(document).on('change', '#id_asset_category', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('change', '.bind-visibility', function() {
        let val = $(this).val();
        if($(this).prop('type') == 'checkbox') {
            val = $(this).is(':checked') ? "true" : "false";
        }
        $(document).find(`.bind-visibility-${$(this).attr('id')}`).closest('.row').addClass('hidden');
        $(document).find(`.bind-visibility-${val}-${$(this).attr('id')}`).closest('.row').removeClass('hidden');
    });

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Asset Category');
        $('.error').text('');
        $('#assetCategoryForm')[0].reset();
        $('#modalAdd').find('select').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#status').val('A').trigger('change');
        $('.bind-visibility').trigger('change');
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset Category');
        $.ajax({
            url: "{{ route('assets.master_asset_category.get_edit') }}",
            data: {
                id_asset_category: $(this).attr('id-asset-category'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]);
                });
                $('#physical_inventory_flag').attr('checked', res.data.physical_inventory_flag);
                $('#depreciation_flag').attr('checked', res.data.depreciation_flag);
                $('.bind-visibility').trigger('change');
                $('#modalAdd').find('select').trigger('change');
            },
            error: (err) => {
                swal({
                    title: 'Error',
                    icon: 'error',
                    text: err.responseJSON.message
                });
            }
        })
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#submitApply', function() {
        $('#assetCategoryForm').submit();
    });

    $('#assetCategoryForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.master_asset_category.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: () => {
                $('#loader').removeClass('hidden');
                $('.error').empty();
            },
            success: (res) => {
                $('#modalAdd').modal('hide');
                $('#depreciationMethodTable').DataTable().ajax.reload();
                swal({
                    icon: 'success',
                    title: 'Success',
                    text: res.message
                });
            },
            error: (err) => {
                swal({
                    icon: 'error',
                    title: 'Error',
                    text: err.responseJSON.message
                });
                if(err.status == 422) {
                    Object.entries(err.responseJSON.errors).forEach((obj) => {
                        $(`#${obj[0]}`).closest('.col').find('.error').text(obj[1][0]);
                    });
                }
            },
            complete: () => {
                $('#loader').addClass('hidden');
            }
        })
    });

    $(document).on('change', '.mca', function() {
        $(this).attr('default-value', $(this).val());
    });

    $('#depreciationMethodTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.master_asset_category')}}"
        },
        columnDefs: [
            {
                orderable: false,
                targets: 0
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'asset_category_code', name: 'asset_category_code', title: 'Code' },
            { data: 'description', name: 'description', title: 'Description' },
            { data: 'category_type', name: 'category_type', title: 'Category Type' },
            { data: 'ownership', name: 'ownership', title: 'Ownership' },
            { data: 'property_type', name: 'property_type', title: 'Property Type' },
            { data: 'asset_classification', name: 'asset_classification', title: 'Asset Classification' },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-asset-category="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection