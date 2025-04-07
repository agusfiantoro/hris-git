@extends('adminlte::page')
@section('title', 'Mass Additions')

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
                <h5 class="card-title">Mass Additions
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    {{-- <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Mass Addition</button> --}}
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
                <h5 class="modal-title" id="modalEditTitle">Asset Mass Addition</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_mass_addition" id="id_mass_addition" class="pk">
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_asset_category">Asset Category</label>
                                </div>
                                <div class="col">
                                    <select name="id_asset_category" id="id_asset_category" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="asset_type">Asset Type</label>
                                </div>
                                <div class="col">
                                    <select name="asset_type" id="asset_type" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_asset_group">Asset Group</label>
                                </div>
                                <div class="col">
                                    <select name="id_asset_group" id="id_asset_group" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="description">Description</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm store-change" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="original_cost">Original Cost</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="original_cost" id="original_cost" class="form-control form-control-sm store-change money" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="current_units">Units</label>
                                </div>
                                <div class="col">
                                    <input type="number" min="0" name="current_units" id="current_units" class="form-control form-control-sm store-change" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="queue_process_status">Queue Process Status</label>
                                </div>
                                <div class="col">
                                    <select name="queue_process_status" id="queue_process_status" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_depreciation_method">Depreciation Method</label>
                                </div>
                                <div class="col">
                                    <select name="id_depreciation_method" id="id_depreciation_method" class="form-control form-control-sm store-change dropdown" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="depreciation_start_date">Depreciation Start Date</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="depreciation_start_date" id="depreciation_start_date" class="form-control form-control-sm store-change date" style="width:100%">
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
                            {{-- <div class="row mb-2">
                                <div class="col">
                                    <label for="revaluation_flag">Revaluation</label>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <input type="checkbox" name="revaluation_flag" id="revaluation_flag" class="form-control form-control-sm" style="width:100%">
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-save"></i> <span id="saveLabel">Save</span></button>&nbsp; --}}
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

    let assetCategories, assetGroups, depreciationMethods = [];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let queueProcessStatus = [
        { id: 'New', text: 'New' },
        { id: 'Confirm', text: 'Confirm' },
        { id: 'Delete', text: 'Delete' },
    ];
    let assetTypes = [
        { id: 'Capitalized', text: 'Capitalized' },
        { id: 'CIP', text: 'CIP' },
        { id: 'Expense', text: 'Expense' },
    ];

    $('#asset_type').empty().prepend('<option></option>').select2({
        data: assetTypes,
        placeholder: 'Select Asset Type',
        allowClear: true
    });

    $('#queue_process_status').empty().prepend('<option></option>').select2({
        data: queueProcessStatus,
        placeholder: 'Select Queue Process Status',
        allowClear: true
    });

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

    function getInitData(modifyDependentElements = true) {
        $.ajax({
            url: "{{ route('assets.mass_addition.get_data') }}",
            success: (res) => {
                assetCategories = res.data.asset_categories;
                assetGroups = res.data.asset_groups;
                depreciationMethods = res.data.depreciation_methods;
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
                    $('#modalAdd').find('select').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    });
                    $('#modalAdd').find('.date').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        locale: {
                            format: 'YYYY-MM-DD'
                        }
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
        $('#modalEditTitle').text('Asset Mass Addition');
        $('.error').text('');
        $('#approvalDetailTableBody').empty();
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select, .pk').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#status').val('A').trigger('change');
        $('#modalAdd').modal('show');
    });

    function cloneDetailRow(append = false, appendTarget = null) {
        let clone = $('#sampleDetailTable').find('tr').clone();
        clone.find('.detail-id_employee-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_approval_mode-input').empty().prepend('<option></option>').select2({ 
            data: detailApprovalModes,
            allowClear: true,
            placeholder: 'Select Approval Mode',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        if(append) {
            clone.appendTo(appendTarget);
        }
        return clone;
    }

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Asset Mass Addition');
        $.ajax({
            url: "{{ route('assets.mass_addition.get_edit') }}",
            data: {
                id_mass_addition: $(this).attr('id-mass-addition'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#approvalDetailTableBody').empty();
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                Object.keys(res.data).forEach((key) => {
                    $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
                });
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('select, .pk, .money').trigger('change');
                $('#modalAdd').find('select, input').attr('disabled', true);
            },
            error: handleError
        })
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '#addDetail', function() {
        let clone = $('#sampleDetailTable').find('tr').clone();
        clone.find('.detail-id_employee-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_approval_mode-input').empty().prepend('<option></option>').select2({ 
            data: detailApprovalModes,
            allowClear: true,
            placeholder: 'Select Approval Mode',
        });
        clone.find('.detail-status-input').empty().select2({ data: status });

        clone.appendTo($('#approvalDetailTableBody'));
        $('#approvalDetailTableBody').children().each((index, row) => {
            $(row).find('.detail_no').text(index+1);
            if($(row).find('.detail-sequence-input').val() == '') {
                $(row).find('.detail-sequence-input').val(index+1);
            }
            toModify.forEach((key) => {
                $(row).find(`.detail-${key}-input`).attr('name', `detail[${index}][${key}]`);
            });
        })
    });

    $(document).on('click', '#submitApply', function() {
        $('#assetGroupForm').submit();
    });

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            // url: "",
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
                        $(`#${obj[0]}`).closest('.col').find('.error').text(obj[1][0]);
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

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.mass_addition')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
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
                data: 'id_asset_group', 
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
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-warning edit" id-mass-addition="${data}"><i class="fas fa-eye"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection