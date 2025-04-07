<div class="modal fade" id="modalAdd" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index: 1600;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditTitle">Asset Transfer Request</h5>
            </div>
            <div class="modal-body">
                <form id="assetGroupForm" method="post">
                    <div class="row">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_transfer_header" id="id_transfer_header" class="pk">
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="reference_number">Reference Number</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="reference_number" id="reference_number" class="form-control form-control-sm store-change" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="description">Description</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm store-change" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="id_employee">Request By</label>
                                </div>
                                <div class="col cont">
                                    <select name="id_employee" id="id_employee" class="form-control form-control-sm" style="width:100%" disabled>
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col cont">
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="request_date">Request Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="request_date" id="request_date" class="form-control form-control-sm store-change date" style="width:100%" disabled>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="need_date">Need Date</label>
                                </div>
                                <div class="col cont">
                                    <input type="text" name="need_date" id="need_date" class="form-control form-control-sm store-change date" style="width:100%">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="document_status">Document Status</label>
                                </div>
                                <div class="col cont">
                                    <span name="document_status" id="document_status">
                                    </span>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col cont">
                                    <label for="status">Status</label>
                                </div>
                                <div class="col cont">
                                    <select name="status" id="status" class="form-control form-control-sm" style="width:100%">
                                    </select>
                                    <span class="error"></span>
                                </div>
                            </div>
                            {{-- <div class="row mb-2">
                                <div class="col cont">
                                    <label for="revaluation_flag">Revaluation</label>
                                </div>
                                <div class="col cont">
                                    <div class="d-flex">
                                        <input type="checkbox" name="revaluation_flag" id="revaluation_flag" class="form-control form-control-sm" style="width:100%">
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="mt-5" style="overflow-x: auto">
                        <div class="pull-right mb-2">
                            <button type="button" class="btn btn-sm btn-primary" id="addTransferDetail"><i class="fa fa-plus"></i> Add Detail</button>
                        </div>
                        <table id="assignedEmployeeTable" class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th style="width:150px;">Asset</th>
                                    <th style="width:150px;">Branch</th>
                                    <th style="width:150px;">Location</th>
                                    <th>Room</th>
                                    <th style="width:150px;">Employee</th>
                                    <th style="width:150px;">Unit Assigned</th>
                                    <th>Effective Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="assignedEmployeeTableBody"></tbody>
                        </table>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-sm btn-success btn-submit" id="submitApply" name="submitApply" value="submit"><i class="fas fa-save"></i> <span id="saveLabel">Save</span></button>&nbsp; --}}
                <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
                <button type="button" class="btn btn-sm btn-success approve"><i class="fas fa-check"></i> Approve</span>
                <button type="button" class="btn btn-sm btn-danger reject"><i class="fas fa-times"></i> Reject</span>
                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<table id="sampleDetailTable" style="display:none;">
    <tbody>
        <tr>
            <td class="detail_no"></td>
            <td class="detail_id_asset cont">
                <select name="detail[][id_asset]" class="form-control form-control-sm store-value detail-id_asset-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_branch_destination cont">
                <input type="hidden" name="detail[][id_transfer_detail]" class="detail-id_transfer_detail-input">
                <select name="detail[][id_branch_destination]" class="form-control form-control-sm store-value detail-id_branch_destination-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_location_destination cont">
                <select name="detail[][id_location_destination]" class="form-control form-control-sm store-value detail-id_location_destination-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_asset_location_destination cont">
                <select name="detail[][id_asset_location_destination]" class="form-control form-control-sm store-value detail-id_asset_location_destination-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_id_employee_destination cont">
                <select name="detail[][id_employee_destination]" class="form-control form-control-sm store-value detail-id_employee_destination-input"></select>
                <span class="error"></span>
            </td>
            <td class="detail_unit_assigned cont">
                <input type="number" name="detail[][unit_assigned]" class="form-control form-control-sm store-value detail-unit_assigned-input" style="width:80px;">
                <span class="error"></span>
            </td>
            <td class="detail_effective_date cont">
                <input name="detail[][effective_date]" class="form-control form-control-sm store-value detail-effective_date-input date" style="width:80px;">
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

<script>
    $('#status').empty().select2({
        data: status,
    });
    function getInitData(modifyDependentElements = true, idHeader) {
        $.ajax({
            url: "{{ route('assets.transfer.get_data') }}",
            beforeSend: () => {
                $('.new, .edit').attr('disabled', true);
            },
            success: (res) => {
                modalTransfer(idHeader);
                $('.new, .edit').attr('disabled', false);
                $('#modalAdd').find('input:checkbox').trigger('change');
                employees = res.data.employees;
                idEmployee = res.data.id_employee;
                branches = res.data.branches;
                assets = res.data.assets;
                if(modifyDependentElements) {
                    $('#id_employee').empty().prepend('<option></option>').select2({
                        data: res.data.employees,
                        allowClear: true,
                        placeholder: 'Select Request By'
                    }).val(res.data.id_employee).trigger('change');
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
        clone.find('.detail-id_asset-input').empty().prepend('<option></option>').select2({ 
            data: assets,
            allowClear: true,
            placeholder: 'Select Asset',
        });
        clone.find('.detail-id_employee_destination-input').empty().prepend('<option></option>').select2({ 
            data: employees,
            allowClear: true,
            placeholder: 'Select Employee',
        });
        clone.find('.detail-id_branch_destination-input').empty().prepend('<option></option>').select2({ 
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
</script>