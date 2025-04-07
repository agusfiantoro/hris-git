@extends('adminlte::page')
@section('title', 'Asset Config Settings')

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
                <h5 class="card-title">Asset Config Settings
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Config</button>
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
                <h5 class="modal-title" id="modalEditTitle">Add Config Settings</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_config_setting" id="id_config_setting" class="pk">
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_je_source">Journal Source</label>
                                </div>
                                <div class="col">
                                    <select name="id_je_source" id="id_je_source" class="form-control form-control-sm store-change" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_asset_journal_category">Asset Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_asset_journal_category" id="id_asset_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_depreciation_journal_category">Depreciation Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_depreciation_journal_category" id="id_depreciation_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_adjustment_journal_category">Adjustment Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_adjustment_journal_category" id="id_adjustment_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_retirement_journal_category">Retirement Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_retirement_journal_category" id="id_retirement_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_revaluation_journal_category">Revaluation Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_revaluation_journal_category" id="id_revaluation_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_impairment_journal_category">Impairment Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_impairment_journal_category" id="id_impairment_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_reinstate_journal_category">Reinstate Journal</label>
                                </div>
                                <div class="col">
                                    <select name="id_reinstate_journal_category" id="id_reinstate_journal_category" class="form-control form-control-sm store-change je-cat" style="width:100%">
                                    </select>
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
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="revaluation_flag">Revaluation</label>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <input type="checkbox" name="revaluation_flag" id="revaluation_flag" class="form-control form-control-sm" style="width:100%;width:20px;height:20px;">
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="impairment_flag">Impairment</label>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <input type="checkbox" name="impairment_flag" id="impairment_flag" class="form-control form-control-sm" style="width:100%;width:20px;height:20px;">
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="next_month_depreciation_start">Next Month Depreciation Start</label>
                                </div>
                                <div class="col">
                                    <div class="d-flex">
                                        <input type="number" name="next_month_depreciation_start" id="next_month_depreciation_start" class="form-control form-control-sm">
                                    </div>
                                    <span class="error"></span>
                                </div>
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

    let jeCategories = [];
    let jeSources = [];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let toModify = ['id_approval_detail', 'sequence', 'id_employee', 'id_approval_mode', 'limit', 'note', 'status'];

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
            url: "{{ route('assets.config_settings.get_data') }}",
            success: (res) => {
                jeCategories = res.data.je_categories;
                jeSources = res.data.je_sources;
                if(modifyDependentElements) {
                    $('.je-cat').empty().prepend('<option></option>').select2({
                        data: res.data.je_categories,
                        allowClear: true,
                        placeholder: 'Select Journal Category'
                    });
                    $('#id_je_source').empty().prepend('<option></option>').select2({
                        data: res.data.je_sources,
                        allowClear: true,
                        placeholder: 'Select Source'
                    });
                    $('.je-cat').each((_i, element) => {
                        if($(element).attr('default-value')) {
                            $(element).val($(element).attr('default-value')).trigger('change');
                        }
                    })
                    if($('#id_je_source').attr('default-value')) {
                        $('#id_je_source').val($('#id_je_source').attr('default-value')).trigger('change');
                    }
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
        $('#modalEditTitle').text('Add Asset Configuration');
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
        $('#modalEditTitle').text('Edit Asset Configuration');
        $.ajax({
            url: "{{ route('assets.config_settings.get_edit') }}",
            data: {
                id_config_setting: $(this).attr('id-config-setting'),
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
                    $(`#${key}`).val(res.data[key]);
                });
                $('#modalAdd').find('input[type=checkbox]').each((_index, element) => {
                    $(element).attr('checked', $(element).val() == "true");
                })
                $('#modalAdd').find('select, .pk').trigger('change');

                res.data.approval_detail?.forEach((detail, i) => {
                    let clone = cloneDetailRow();
                    Object.keys(detail).forEach((key) => {
                        console.log(key, detail[key]);
                        clone.find(`.detail-${key}-input`)
                            .val(detail[key])
                            .attr('name', `detail[${i}][${key}]`)
                            .attr('default-value', detail[key]);
                    });
                    clone.appendTo($('#approvalDetailTableBody'));
                })

                
                $('#approvalDetailTableBody').children().each((index, row) => {
                    $(row).find('.detail_no').text(index+1);
                    $(row).find('select').trigger('change');
                    // $(row).find('.detail-sequence-input').val();
                    // toModify.forEach((key) => {
                    //     $(row).find(`.detail-${key}-input`).attr('name', `detail[${index}][${key}]`);
                    // });
                });
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
            url: "{{ route('assets.config_settings.save') }}",
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

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.config_settings')}}"
        },
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { 
                data: 'id_je_source', 
                name: 'id_je_source', 
                title: 'Journal Source', 
                render: (data, type, row) => {
                    if(row.je_source) return row.je_source.source_name;
                    else return '-';
                } 
            },
            { 
                data: 'id_asset_journal_category', 
                name: 'id_asset_journal_category', 
                title: 'Asset Journal', 
                render: (data, type, row) => {
                    if(row.asset_journal_category) return row.asset_journal_category.category_name;
                    else return '-';
                } 
            },
            { 
                data: 'id_depreciation_journal_category', 
                name: 'id_depreciation_journal_category', 
                title: 'Depreciation Journal', 
                render: (data, type, row) => {
                    if(row.depreciation_journal_category) return row.depreciation_journal_category.category_name;
                    else return '-';
                } 
            },
            { 
                data: 'id_adjustment_journal_category', 
                name: 'id_adjustment_journal_category', 
                title: 'Adjustment Journal', 
                render: (data, type, row) => {
                    if(row.adjustment_journal_category) return row.adjustment_journal_category.category_name;
                    else return '-';
                } 
            },
            { 
                data: 'id_retirement_journal_category', 
                name: 'id_retirement_journal_category', 
                title: 'Retirement Journal', 
                render: (data, type, row) => {
                    if(row.retirement_journal_category) return row.retirement_journal_category.category_name;
                    else return '-';
                } 
            },
            { 
                data: 'revaluation_flag', 
                name: 'revaluation_flag', 
                title: 'Revaluation',
                render: (data) => {
                    if(data) {
                        return `<span class="badge badge-sm badge-success">Yes</span>`
                    }
                    return `<span class="badge badge-sm badge-danger">No</span>`
                }
            },
            { 
                data: 'impairment_flag', 
                name: 'impairment_flag', 
                title: 'Impairment',
                render: (data) => {
                    if(data) {
                        return `<span class="badge badge-sm badge-success">Yes</span>`
                    }
                    return `<span class="badge badge-sm badge-danger">No</span>`
                }
            },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-config-setting="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection