@extends('adminlte::page')
@section('title', $param_source == 'mass_depreciation' ? 'Mass Depreciation' : 'Depreciation History')

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
                <h5 class="card-title">
                    {{ $param_source == 'mass_depreciation' ? 'Mass Depreciation' : 'Depreciation History' }}
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    @if($param_source == 'mass_depreciation')
                        <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Generate Mass Depreciation</button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="col-8">
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label for="id_asset_category">Asset Category</label>
                        </div>
                        <div class="col-sm-6">
                            <select name="id_asset_category" id="id_asset_category" class="form-control form-control-sm" style="width:100%"></select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label for="id_asset">Asset Number</label>
                        </div>
                        <div class="col-sm-6">
                            <select name="id_asset" id="id_asset" class="form-control form-control-sm" style="width:100%"></select>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4">
                            <label for="id_period">Period</label>
                        </div>
                        <div class="col-sm-6">
                            <select name="id_period" id="id_period" class="form-control form-control-sm" style="width:100%"></select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-sm btn-success" onclick="search()">Search</button>
                        </div>
                    </div>
                </div>
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="depreciationMethodTable" style="width: 100%;">
                    <thead>
                        <tr>
                        <!-- <th></th> -->
                        {{-- <th data-priority="4">No.</th>
                        <th data-priority="2">Code</th>
                        <th>Rule</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th data-priority="1" align="center">Action</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modalGenerate" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalGenerateTitle">Generate Mass Depreciation</h5>
            </div>
            <div class="modal-body">
                <form id="generateForm" method="post" class="row">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_depreciation_method" id="id_depreciation_method">
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_gen_period">Period</label>
                            </div>
                            <div class="col">
                                <select name="id_period" id="id_gen_period" class="form-control form-control-sm" style="width:100%">
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
                                <label for="asset_number">Asset Number</label>
                            </div>
                            <div class="col">
                                <input name="asset_number" id="asset_number" class="form-control form-control-sm" style="width:100%">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_account">Depreciation Account</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_account" id="id_depreciation_account" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="depreciation_period">Period</label>
                            </div>
                            <div class="col">
                                <select name="id_period" id="depreciation_period" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_depreciation_reserve_account">Depreciation Reserve Account</label>
                            </div>
                            <div class="col">
                                <select name="id_depreciation_reserve_account" id="id_depreciation_reserve_account" class="form-control form-control-sm" style="width:100%">
                                </select>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="depreciation_value">Depreciation Value</label>
                            </div>
                            <div class="col">
                                <input name="depreciation_value" id="depreciation_value" class="form-control form-control-sm" style="width:100%">
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="row mb-2">
                            <div class="col">
                                <label for="depreciation_date">Depreciation Date</label>
                            </div>
                            <div class="col">
                                <input name="depreciation_date" id="depreciation_date" class="form-control form-control-sm" style="width:100%">
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="is_posted_flag">Posted</label>
                            </div>
                            <div class="col">
                                <div class="is-posted"></div>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="gl_transfer_flag">Transfer</label>
                            </div>
                            <div class="col">
                                <div class="transfer-flag"></div>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="id_je_header">Journal Entry</label>
                            </div>
                            <div class="col">
                                <div class="journal-entry"></div>
                                <span class="error"></span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label for="retired_date">Retired Date</label>
                            </div>
                            <div class="col">
                                <input name="retired_date" id="retired_date" class="form-control form-control-sm" style="width:100%">
                                <span class="error"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-play"></i> <span id="saveLabel">Generate</span></button>&nbsp; --}}
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

    let periods, generatePeriods, companies, assets, assetCategories, accounts = [];

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

    $(document).on('change', '#id_asset_category', function() {
        $.ajax({
            url: `{{ route('assets.depreciation_history.get_data') }}?id_asset_category=${$(this).val()}`,
            success: (res) => {
                $('#id_asset').empty().prepend('<option></option>').select2({
                    data: res.data.assets,
                    placeholder: 'Select Asset',
                    allowClear: true,
                });
            }
        });
    });

    $(document).on('change', '#id_asset', function() {
        $.ajax({
            url: `{{ route('assets.depreciation_history.get_data') }}?id_asset=${$(this).val()}`,
            success: (res) => {
                $('#id_asset_category').empty().prepend('<option></option>').select2({
                    data: res.data.asset_categories,
                    placeholder: 'Select Asset Category',
                    allowClear: true,
                });
            }
        });
    });

    function getInitData() {
        $.ajax({
            url: "{{ route('assets.mass_depreciation.get_data') }}",
            success: (res) => {
                generatePeriods = res.data.periods;
                companies = res.data.company;
                $('#id_gen_period').select2({
                    data: res.data.periods,
                    placeholder: 'Select Period',
                    allowClear: true,
                });
                $('#id_company').select2({
                    data: res.data.company,
                }).attr('readonly', true);
            }
        })
        $.ajax({
            url: "{{ route('assets.depreciation_history.get_data') }}",
            success: (res) => {
                periods = res.data.periods;
                assets = res.data.assets;
                assetCategories = res.data.asset_categories;
                accounts = res.data.accounts;
                $('#id_period, #depreciation_period').empty().prepend('<option></option>').select2({
                    data: res.data.periods,
                    placeholder: 'Select Period',
                    allowClear: true,
                });
                $('#id_asset').empty().prepend('<option></option>').select2({
                    data: res.data.assets,
                    placeholder: 'Select Asset',
                    allowClear: true,
                });
                $('#id_asset_category').empty().prepend('<option></option>').select2({
                    data: res.data.asset_categories,
                    placeholder: 'Select Asset Category',
                    allowClear: true,
                });
                $('#id_depreciation_account, #id_depreciation_reserve_account').empty().prepend('<option></option>').select2({
                    data: res.data.accounts,
                    placeholder: 'Select Account',
                    allowClear: true,
                });
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
        $('#modalGenerate').modal('show');
    });

    $(document).on('click', '.edit', function() {
        $('#modalEditTitle').text('Depreciation Detail');
        $.ajax({
            url: "{{ route('assets.depreciation_history.get_edit') }}",
            data: {
                id_depreciation_detail: $(this).attr('id-depreciation-detail'),
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
                    $('#modalAdd').find(`#${key}`).val(res.data[key]).attr('disabled', true);
                });
                $('#depreciation_period').val(res.data.id_period).trigger('change').attr('disabled', true);
                if(res.data.is_posted_flag) {
                    $('.is-posted').html('<span class="badge badge-sm badge-success">Yes</span>');
                } else {
                    $('.is-posted').html('<span class="badge badge-sm badge-secondary">No</span>');
                }
                if(res.data.gl_transfer_flag) {
                    $('.transfer-flag').html('<span class="badge badge-sm badge-success">Yes</span>');
                } else {
                    $('.transfer-flag').html('<span class="badge badge-sm badge-secondary">No</span>');
                }
                if(res.data.je_header) {
                    $('.journal-entry').html(`<a href="{{route('accounting.gl_je')}}?id=${res.data.id_je_header}">${res.data.je_header.reference_number}</a>`);
                } else {
                    $('.journal-entry').html('-');
                }
                $('#description').val(res.data.description);
                $('#depreciation_rule').val(res.data.depreciation_rule).trigger('change');
                $('#status').val(res.data.status).trigger('change');
                $('#id_depreciation_method').val(res.data.id_depreciation_method).trigger('change');
                $('#modalAdd').find(`select`).trigger('change');
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
        $('#generateForm').submit();
    });

    $('#generateForm').submit(function(e) {
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
                        $('#modalGenerate').modal('hide');
                        $('#id_period').val($('#id_gen_period').val()).trigger('change');
                        search();
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

    function search() {
        let url = `{{route('assets.depreciation_history')}}?source={{$param_source}}&id_period=${$('#id_period').val()}&id_asset_category=${$('#id_asset_category').val()}&id_asset=${$('#id_asset').val()}`;
        generateTable(url);
    }

    function generateTable(url) {
        if($.fn.DataTable.isDataTable('#depreciationMethodTable')) {
            $('#depreciationMethodTable').DataTable().ajax.url(url)
            $('#depreciationMethodTable').DataTable().ajax.reload();
            return;
        }
        $('#depreciationMethodTable').DataTable({
            responsive: true,
            ajax: {
                url: url,
                beforeSend: () => {
                    $('#loader').removeClass('hidden');
                },
                complete: () => {
                    $('#loader').addClass('hidden');
                }
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: 0
                }
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
                { 
                    data: 'asset_number', 
                    name: 'asset_number', 
                    title: 'Asset Number'
                },
                { data: 'depreciation_date', name: 'depreciation_date', title: 'Depreciation Date' },
                { data: 'period_description', name: 'period', title: 'Period' },
                { data: 'depreciation_value', name: 'depreciation_value', title: 'Depreciation Value' },
                { data: 'is_posted_flag', name: 'is_posted_flag', title: 'Posted', render: (data) => data ? 'Yes' : 'No' },
                { data: 'gl_transfer_flag', name: 'gl_transfer_flag', title: 'Transfer Flag', render: (data) => data ? 'Yes' : 'No' },
                { 
                    data: 'id_je_header', 
                    name: 'id_je_header', 
                    title: 'Journal Entry', 
                    render: (data, type, row) => {
                        if(row.je_reference_number) {
                            return `<a href="{{route('accounting.gl_je')}}?id=${data}">${row.je_reference_number}</a>`;
                        } else return '-';
                    } 
                },
                { data: 'status', name: 'status', title: 'Status' },
                { 
                    data: 'action', 
                    name: 'action', 
                    title: 'Action',
                    render: (data, type, row) => {
                        return `<span class="btn btn-warning text-white edit" id-depreciation-detail="${data}"><i class="fas fa-eye"></i></span>`;
                    }
                }
            ]
        });
    }
</script>
@endsection