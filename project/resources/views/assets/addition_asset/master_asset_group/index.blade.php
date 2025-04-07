@extends('adminlte::page')
@section('title', 'Master Asset Group')

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
                <h5 class="card-title">Master Asset Group
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Asset Group</button>
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
                <h5 class="modal-title" id="modalEditTitle">Add Asset Group</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_asset_group" id="id_asset_group">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="asset_group_code">Code</label>
                            </div>
                            <div class="col">
                                <input type="text" name="asset_group_code" id="asset_group_code" class="form-control form-control-sm">
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
                                <label for="note">Note</label>
                            </div>
                            <div class="col">
                                <input type="text" name="note" id="note" class="form-control form-control-sm">
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="status">Depreciation Flag</label>
                            </div>
                            <div class="col">
                                <input type="checkbox" name="depreciation_flag" id="depreciation_flag">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_method">Depreciation Method</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_method" id="id_depreciation_method" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col cont">
                                <label for="life_in_month">Life</label>
                            </div>
                            <div class="col cont">
                                <div class="input-group input-group-sm">
                                    <input type="number" min="0" name="life_in_month" id="life_in_month" class="form-control form-control-sm non-editable">
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
                                <label for="salvage_type">Salvage Type</label>
                            </div>
                            <div class="col">
                                <select name="salvage_type" id="salvage_type" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="salvage_value">Salvage Value</label>
                            </div>
                            <div class="col">
                                <input type="text" name="salvage_value" id="salvage_value" class="form-control form-control-sm">
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

    $('#status').empty().prepend().select2({
        data: [
            { id: 'A', text: 'Active' },
            { id: 'I', text: 'Inactive' },
        ],
    });

    $('#salvage_type').empty().prepend('<option></option>').select2({
        placeholder: 'Select Salvage Type',
        allowClear: true,
        data: [
            { id: 'Percent', text: 'Percent' },
            { id: 'Amount', text: 'Amount' },
        ],
    });

    function getInitData() {
        $.ajax({
            url: "{{ route('assets.master_asset_group.get_data') }}",
            success: (res) => {
                $('#id_depreciation_method').select2({
                    placeholder: 'Select Depreciation Method',
                    allowClear: true,
                    data: res.depreciation_methods,
                })
            }
        })
    }
    getInitData();

    $(document).on('change', '#id_asset_group', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Asset Group');
        $('.error').text('');
        $('#assetGroupForm')[0].reset();
        $('#modalAdd').find('select').each((_i, element) => {
            $(element).val(null).trigger('change');
        });
        $('#modalAdd').find('input[type=checkbox]').each((_i, element) => {
            $(element).attr('checked', false);
        });
        $('#status').val('A').trigger('change');
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset Group');
        $.ajax({
            url: "{{ route('assets.master_asset_group.get_edit') }}",
            data: {
                id_asset_group: $(this).attr('id-asset-group'),
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
                if($('#depreciation_flag').is(':checked') != res.data.depreciation_flag) {
                    $('#depreciation_flag').trigger('click');
                }
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
        $('#assetGroupForm').submit();
    });

    $(document).on('change', '#depreciation_flag', function() {
        if($(this).is(':checked')) {
            $('#id_depreciation_method').closest('.row').show();
        } else {
            $('#id_depreciation_method').closest('.row').hide();
        }
    });

    $('#depreciation_flag').trigger('change');

    $('#assetGroupForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.master_asset_group.save') }}",
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

    $('#assetGroupTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.master_asset_group')}}"
        },
        columnDefs: [
            {
                orderable: false,
                targets: 0
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'asset_group_code', name: 'asset_group_code', title: 'Code' },
            { data: 'description', name: 'description', title: 'Description' },
            { data: 'note', name: 'note', title: 'Note' },
            { 
                data: 'depreciation_flag', 
                name: 'depreciation_flag', 
                title: 'Depreciation Flag',
                render: (data) => {
                    return data ? 'Yes' : 'No';
                }
            },
            { data: 'depreciation_method', name: 'depreciation_method', title: 'Depreciation Method' },
            { data: 'life_in_month', name: 'life_in_month', title: 'Life (Month)' },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-asset-group="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection