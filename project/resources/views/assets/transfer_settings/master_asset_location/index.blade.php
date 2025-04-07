@extends('adminlte::page')
@section('title', 'Master Asset Location')

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
                <h5 class="card-title">Master Asset Location
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Asset Location</button>
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="depreciationMethodTable" style="width: 100%;">
                    <thead>
                        <tr>
                            {{-- <th></th> --}}
                            <th data-priority="4">No.</th>
                            <th data-priority="2">Code</th>
                            <th>Location</th>
                            <th>Branch</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th data-priority="1" align="center">Action</th>
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
                <h5 class="modal-title" id="modalEditTitle">Add Asset Location</h5>
            </div>
            <div class="modal-body">
                <form id="assetLocationForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_asset_location" id="id_asset_location">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="location_code">Code</label>
                            </div>
                            <div class="col">
                                <input type="text" name="location_code" id="location_code" class="form-control form-control-sm">
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
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_branch">Branch</label>
                            </div>
                            <div class="col">
                                <select name="id_branch" id="id_branch" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_location">Location</label>
                            </div>
                            <div class="col">
                                <select name="id_location" id="id_location" class="form-control form-control-sm" style="width:100%">
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

    function getLocation(idBranch = null, defaultValue = null) {
        $.ajax({
            // async: false,
            url: `{{ route('assets.master_asset_location.get_locations') }}?id_branch=${idBranch ? idBranch : ""}`,
            success: (res) => {
                if(idBranch) {
                    $('#id_location')
                    .empty()
                    .prepend('<option></option>')
                    .select2({
                        allowClear: true,
                        placeholder: 'Select Location',
                        data: res.data
                    });
                    if(defaultValue) {
                        $('#id_location').val(defaultValue).trigger('change');
                    }
                } else {
                    $('#id_branch')
                    .empty()
                    .prepend('<option></option>')
                    .select2({
                        allowClear: true,
                        placeholder: 'Select Branch',
                        data: res.data
                    });
                    if(defaultValue) {
                        $('#id_branch').val(defaultValue).trigger('change');
                    }
                }
                return res.data
            },
            error: (err) => {
                swal({
                    icon: 'error',
                    text: 'Error',
                    text: err.responseJSON.message
                })
                return [];
            }
        });
    }

    $(document).on('change', '#id_depreciation_method', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Save');
        } else {
            $('#saveLabel').text('Update');
        }
    });

    getLocation();

    $(document).on('change', '#id_branch', function() {
        if(!$(this).val()) {
            return;
        }
        getLocation($(this).val(), $('#id_location').attr('fetch-val'));
    });

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Add Asset Location');
        $('.error').text('');
        $('#assetLocationForm')[0].reset();
        $('#id_asset_location, #id_branch').val(null).trigger('change');
        $('#status').val('A');
        $('#id_location, #status').trigger('change');
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Asset Location');
        $.ajax({
            processing: true,
            url: "{{ route('assets.master_asset_location.get_edit') }}",
            data: {
                id_asset_location: $(this).attr('id-asset-location'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                $('#id_branch').attr('fetch-val', res.data.id_branch);
                $('#id_location').attr('fetch-val', res.data.id_location);
                getLocation(null, res.data.id_branch)
                $('#location_code').val(res.data.location_code);
                $('#description').val(res.data.description);
                $('#id_branch').val(res.data.id_branch).trigger('change');
                $('#status').val(res.data.status).trigger('change');
                $('#id_asset_location').val(res.data.id_asset_location).trigger('change');
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
        $('#assetLocationForm').submit();
    });

    $('#assetLocationForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('assets.master_asset_location.save') }}",
            type: 'POST',
            data: $(this).serialize(),
            beforeSend: () => {
                $('#loader').removeClass('hidden');
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
                        $(`#${obj[0]}`).parent().find('.error').text(obj[1][0]);
                    });
                }
            },
            complete: () => {
                $('#loader').addClass('hidden');
            }
        })
    });

    $('#depreciationMethodTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.master_asset_location')}}"
        },
        columnDefs: [
            {
                orderable: false,
                targets: 0
            }
        ],
        columns: [
            // { data: 'id_asset_location', orderable: false },
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'location_code', name: 'location_code' },
            { data: 'location.description', name: 'location.description' },
            { data: 'branch.description', name: 'branch.description' },
            { data: 'description', name: 'description' },
            { data: 'status', name: 'status' },
            { 
                data: 'action', 
                name: 'action', 
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-asset-location="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection