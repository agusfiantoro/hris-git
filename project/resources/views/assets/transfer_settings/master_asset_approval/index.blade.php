@extends('adminlte::page')
@section('title', 'Master Approval Hierarchy')

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
                <h5 class="card-title">Master Approval Hierarchy
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    {{-- <button type="button" class="new btn btn-sm btn-success" onclick="dataTable.ajax.reload()"><i class="fas fa-filter"></i> Filter</button> --}}
                    <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Transfer Approval</button>
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
                <h5 class="modal-title" id="modalEditTitle">Add Approval Hierarchy</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_approval" id="id_approval" class="pk">
                        <div class="col">
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
                                    <label for="hierarchy_type">Hierarchy Type</label>
                                </div>
                                <div class="col">
                                    <select name="hierarchy_type" id="hierarchy_type" class="form-control form-control-sm store-change" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_approval_doc_type">Approval Doc Type</label>
                                </div>
                                <div class="col">
                                    <select name="id_approval_doc_type" id="id_approval_doc_type" class="form-control form-control-sm store-change" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="approval_mode">Approval Mode</label>
                                </div>
                                <div class="col">
                                    <select name="approval_mode" id="approval_mode" class="form-control form-control-sm store-change" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="note">Note</label>
                                </div>
                                <div class="col">
                                    <input type="text" name="note" id="note" class="form-control form-control-sm">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <label for="id_job_grade">Grade User</label>
                                </div>
                                <div class="col">
                                    <select name="id_job_grade" id="id_job_grade" class="form-control form-control-sm store-change" style="width:100%">
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
                                    <label for="is_auto_approved">Auto Approve</label>
                                </div>
                                <div class="col">
                                    <input type="checkbox" name="is_auto_approved" id="is_auto_approved" class="form-control form-control-sm" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5">
                        <div class="pull-right mb-2">
                            <button type="button" class="btn btn-sm btn-primary" id="addDetail"><i class="fa fa-plus"></i>Add Approval</button>
                        </div>
                        <table id="approvalDetailTable" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width:75px;">Sequence</th>
                                    <th style="width:150px;">Employee</th>
                                    <th>Approval Mode</th>
                                    <th>Limit</th>
                                    <th>Note</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="approvalDetailTableBody"></tbody>
                        </table>
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
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_sequence">
                <input type="hidden" name="detail[][id_approval_detail]" class="detail-id_approval_detail-input">
                <input type="number" name="detail[][sequence]" class="form-control form-control-sm detail-sequence-input">
            </td>
            <td class="detail_id_employee">
                <select name="detail[][id_employee]" class="form-control form-control-sm store-value detail-id_employee-input"></select>
            </td>
            <td class="detail_approval_mode">
                <select name="detail[][id_approval_mode]" class="form-control form-control-sm store-value detail-id_approval_mode-input"></select>
            </td>
            <td class="detail_limit">
                <input type="number" name="detail[][limit]" class="form-control form-control-sm detail-limit-input">
            </td>
            <td class="detail_note">
                <input type="text" name="detail[][note]" class="form-control form-control-sm detail-note-input">
            </td>
            <td class="detail_status">
                <select name="detail[][status]" class="form-control form-control-sm store-value detail-status-input"></select>
            </td>
            <td class="detail_action"></td>
        </tr>
    </tbody>
</table>
@endsection
@section('scripts')
<script type="text/javascript">
    @include('assets.advanced_search')

    let jobGrades = [];
    let approvalDocTypes = [];
    let hierarchyTypes = [
        { id: 'Organization', text: 'Organization' },
        { id: 'Custom', text: 'Custom' },
        { id: 'Combine', text: 'Combine' },
    ];
    let approvalModes = [
        { id: 'Conditional', text: 'Conditional' },
        { id: 'Limitation', text: 'Limitation' },
    ];
    let employees = [];
    let detailApprovalModes = [];
    let status = [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' },
    ];
    let toModify = ['id_approval_detail', 'sequence', 'id_employee', 'id_approval_mode', 'limit', 'note', 'status'];

    $('#status').empty().select2({
        data: status,
    });

    $('#hierarchy_type').empty().select2({
        data: hierarchyTypes,
        placeholder: 'Select Hierarchy Type'
    });
    $('#approval_mode').empty().select2({
        data: approvalModes,
        placeholder: 'Select Approval Mode'
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
            url: "{{ route('assets.master_approval.get_data') }}",
            success: (res) => {
                jobGrades = res.data.job_grade;
                approvalDocTypes = res.data.approval_doc_type;
                detailApprovalModes = res.data.approval_mode;
                employees = res.data.employee;
                if(modifyDependentElements) {
                    $('#id_approval_doc_type').empty().prepend('<option></option>').select2({
                        data: res.data.approval_doc_type,
                        allowClear: true,
                        placeholder: 'Select Approval Type'
                    });
                    $('#id_job_grade').empty().prepend('<option></option>').select2({
                        data: res.data.job_grade,
                        allowClear: true,
                        placeholder: 'Select Job Grade'
                    });
                    if($('#id_approval_doc_type').attr('default-value')) {
                        $('#id_approval_doc_type').val($('#id_approval_doc_type').attr('default-value')).trigger('change');
                    }
                    if($('#id_job_grade').attr('default-value')) {
                        $('#id_job_grade').val($('#id_job_grade').attr('default-value')).trigger('change');
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
        $('#modalEditTitle').text('Add Approval Hierarchy');
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
        $('#modalEditTitle').text('Edit Approval Hierarchy');
        $.ajax({
            url: "{{ route('assets.master_approval.get_edit') }}",
            data: {
                id_approval: $(this).attr('id-approval'),
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
            url: "{{ route('assets.master_approval.save') }}",
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
            url: "{{route('assets.master_approval')}}"
        },
        columnDefs: [
            {
                orderable: false,
                targets: 0
            }
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
            { data: 'approval_document_type.description', name: 'approval_type', title: 'Approval Type' },
            { 
                data: 'id_job_grade', 
                name: 'job_grade', 
                title: 'Job Grade', 
                render: (data, type, row) => {
                    if(row.job_grade) return row.job_grade.description;
                    else return '';
                } 
            },
            { data: 'description', name: 'description', title: 'Description' },
            { data: 'hierarchy_type', name: 'hierarchy_type', title: 'Hierarchy Type' },
            { data: 'approval_mode', name: 'approval_mode', title: 'Approval Mode' },
            { data: 'note', name: 'note', title: 'Note' },
            { data: 'status', name: 'status', title: 'Status' },
            { 
                data: 'action', 
                name: 'action', 
                title: 'Action',
                render: (data, type, row) => {
                    return `<span class="btn btn-primary edit" id-approval="${data}"><i class="fas fa-edit"></i></span>`;
                }
            }
        ]
    });
</script>
@endsection