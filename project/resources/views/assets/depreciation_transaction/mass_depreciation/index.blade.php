@extends('adminlte::page')
@section('title', 'Mass Depreciation')

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
                <h5 class="card-title">Mass Depreciation
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Generate Mass Depreciation</button>
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="depreciationMethodTable" style="width: 100%;">
                    <thead>
                        <tr>
                        <!-- <th></th> -->
                        <th data-priority="4">No.</th>
                        <th data-priority="2">Code</th>
                        <th>Rule</th>
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
                <h5 class="modal-title" id="modalEditTitle">Generate Mass Depreciation</h5>
            </div>
            <div class="modal-body">
                <form id="depreciationMethodForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_depreciation_method" id="id_depreciation_method">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_period">Period</label>
                            </div>
                            <div class="col">
                                <select name="id_period" id="id_period" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_company">Company</label>
                            </div>
                            <div class="col">
                                <select name="id_company" id="id_company" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-play"></i> <span id="saveLabel">Generate</span></button>&nbsp;
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

    let periods, companies = [];

    let depreciationRules = [
        { id: 'STL', text: 'Straight Line' },
        { id: 'DEGRESSIVE', text: 'Degressive' },
        { id: 'DOUBLE', text: 'Double Decline' },
    ];
    $('#depreciation_rule').empty().prepend('<option></option>').select2({
        data: depreciationRules,
        placeholder: 'Select Depreciation Rule',
        allowClear: true,
    });
    $('#status').empty().prepend().select2({
        data: [
            { id: 'A', text: 'Active' },
            { id: 'I', text: 'Inactive' },
        ],
    });

    function getInitData() {
        $.ajax({
            url: "{{ route('assets.mass_depreciation.get_data') }}",
            success: (res) => {
                periods = res.data.periods;
                companies = res.data.company;
                $('#id_period').select2({
                    data: res.data.periods,
                    placeholder: 'Select Period',
                    allowClear: true,
                });
                $('#id_company').select2({
                    data: res.data.company,
                }).attr('readonly', true);
            }
        })
    }

    getInitData();

    function generate() {
        $.ajax({
            url: "{{ route('assets.mass_depreciation.generate') }}",
            type: 'POST',

        })
    }

    $(document).on('change', '#id_depreciation_method', function() {
        if($(this).val() == '') {
            $('#saveLabel').text('Generate');
        } else {
            $('#saveLabel').text('Update');
        }
    })

    $(document).on('click', '.new', function() {
        $('#modalEditTitle').text('Generate Mass Depreciation');
        $('.error').text('');
        $('#depreciationMethodForm')[0].reset();
        $('#id_depreciation_method').val(null).trigger('change');
        $('#status').val('A');
        $('#depreciation_rule, #status').trigger('change');
        $('#modalAdd').modal('show');
    });

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Edit Depreciation Method');
        $.ajax({
            url: "{{ route('assets.master_depreciation_method.get_edit') }}",
            data: {
                id_depreciation_method: $(this).attr('id-depreciation-method'),
            },
            beforeSend: () => {
                $('.error').text('');
                $('#loader').removeClass('hidden');
            },
            complete: () => {
                $('#loader').addClass('hidden');
            },
            success: (res) => {
                $('#depreciation_code').val(res.data.depreciation_code);
                $('#description').val(res.data.description);
                $('#depreciation_rule').val(res.data.depreciation_rule).trigger('change');
                $('#status').val(res.data.status).trigger('change');
                $('#id_depreciation_method').val(res.data.id_depreciation_method).trigger('change');
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
        $('#depreciationMethodForm').submit();
    });

    $('#depreciationMethodForm').submit(function(e) {
        e.preventDefault();
        swal({
            icon: 'warning',
            title: 'Generate Mass Depreciation?',
            text: 'Do you want to proceed to generate mass depreciation? This action is irreversible.',
            buttons: ['Cancel', 'Generate']
        }).then((confirm) => {
            if(confirm) {
                $.ajax({
                    url: "{{ route('assets.mass_depreciation.generate') }}",
                    data: $(this).serialize(),
                    type: 'POST',
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
            }
        })
    });

    $('#depreciationMethodTable').DataTable({
        responsive: true,
        ajax: {
            url: "{{route('assets.depreciation_history')}}?source=mass_depreciation"
        },
        columnDefs: [
            {
                orderable: false,
                targets: 0
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'depreciation_code', name: 'depreciation_code' },
            { data: 'depreciation_rule', name: 'depreciation_rule' },
            { data: 'description', name: 'description' },
            { data: 'status', name: 'status' },
            { 
                data: 'action', 
                name: 'action', 
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-depreciation-method="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection