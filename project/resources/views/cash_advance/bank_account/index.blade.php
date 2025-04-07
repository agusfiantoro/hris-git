@extends('adminlte::page')
@section('title', 'Master Bank Account')

@section('content')
<style>
    .hidden {
        display: none;
    }
    input[readonly] {
        background: #e8ebed;
        box-shadow: none;
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
    .is-invalid {
        display: block!important;
    }

    .settlement-notes {
        word-wrap: break-word;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Bank Account
                </h5>
                <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
                <div class="card-tools">
                    <button type="button" more_type="New" class="new btn button-add new btn-sm btn-success"><i class="fas fa-plus"></i> Add Bank Account</button>
                </div>
            </div>
            <div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
                {{-- <button id="addCashAdvance" type="button" class="btn btn-xs btn-success float-right">Add Declaration</button> --}}
                <br>
                <br>
                <table class="table table-hover table-bordered table-striped" id="settlementtravel_table" style="width: 100%;">
                </table>
            </div>
        </div>
    </div>
</div>
<div id="bankAccountModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <!-- Modal content-->
      <div class="modal-content">
          <form method="POST" id="bankAccountForm">
              {{ csrf_field() }}
              <div class="modal-header">
                  <h5 id="modal-title" class="modal-title">Bank Account</h5>
                  <button type="button" class="close"  data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
            <div class="modal-body" id="contentBody">
                <div class="row">						
                    <div class="col-md-6">
                        <input type="hidden" name="id_bank_account" id="id_bank_account">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Bank</label>
                            <div class="col-sm-8">
                                <select name="id_bank" id="id_bank" class="form-control form-control-sm select2" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback" role="alert" id="id_bankError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Branch</label>
                            <div class="col-sm-8">
                                <select name="id_branch" id="id_branch" class="form-control form-control-sm select2" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback" role="alert" id="id_branchError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Account Number</label>
                            <div class="col-sm-8">
                                <input name="account_number" id="account_number" class="form-control form-control-sm select2" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="account_numberError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Branch Name</label>
                            <div class="col-sm-8">
                                <input name="branch_name" id="branch_name" class="form-control form-control-sm select2" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="branch_nameError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">						
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Account Name</label>
                            <div class="col-sm-8">
                                <input name="account_name" id="account_name" class="form-control form-control-sm select2" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="account_nameError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Bank Type</label>
                            <div class="col-sm-8">
                                <select name="bank_type" id="bank_type" class="form-control form-control-sm select2" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback" role="alert" id="bank_typeError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Default</label>
                            <div class="col-sm-8">
                                <input type="checkbox" name="is_default" id="is_default" class="form-control form-control-sm" style="width: 100%;">
                                <span class="invalid-feedback" role="alert" id="is_defaultError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <label class="col-sm-4 col-form-label">Status</label>
                            <div class="col-sm-8">
                                <select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback" role="alert" id="statusError">
                                    <strong></strong>
                                </span>                                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
              <button type="submit" class="edit_master btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
              <button type="button" class="btn btn-default"  data-dismiss="modal">Close</button>
            </div>
          </form>  
      </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
$('#label_button_action_submit').text("Submit");
$('#label_button_action_save').text("Save Draft");
let branches, banks = [];

moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
});
function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D/MM/YYYY');
return formattedDate;
}
function formatRupiah(amount) {
    if(amount === 0 || amount === '0') return `${amount}`;
    if (!amount) {
        return '';
    }   
    return amount.toLocaleString('id-ID');
}
function unformatRupiah(rupiah) {
    if (!rupiah) {
        return 0;
    }
    return parseInt(rupiah.replace(/,|\./g, ''));
}

$('#status').select2({
    data: [
        { id: 'A', text: 'Active' },
        { id: 'I', text: 'Inactive' }
    ],
});

$('#bank_type').select2({
    data: [
        { id: 'Company', text: 'Company' },
        { id: 'Partner', text: 'Partner' }
    ],
});

function getInitData() {
    $.ajax({
        url: "{{ route('master_bank_account.get_data') }}",
        success: (res) => {
            branches = res.data.branches;
            banks = res.data.banks;
            $('#id_branch').empty().prepend('<option></option>').select2({
                data: res.data.branches,
                allowClear: true,
                placeholder: 'Select Branch'
            });
            $('#id_bank').empty().prepend('<option></option>').select2({
                data: res.data.banks,
                allowClear: true,
                placeholder: 'Select Bank'
            });
        }
    });
}
getInitData();

function loadModalData(idBankAccount = null) {
    if(!idBankAccount) {
        $('#save_button').show();
        $('#edit_button').hide();
        return;
    }
    $.ajax({
        url: "{{ route('master_bank_account.get_edit') }}",
        data: {
            id_bank_account: idBankAccount,
        },
        success: (res) => {
            $('#save_button').hide();
            $('#edit_button').show();
            Object.keys(res.data).forEach((key) => {
                $(`#${key}`).val(res.data[key]).attr('default-value', res.data[key]);
            });
            if(res.data.is_default) {
                if(!$('#is_default').is(':checked')) {
                    $('#is_default').trigger('click');
                }
            } else {
                if($('#is_default').is(':checked')) {
                    $('#is_default').trigger('click');
                }
            }
            $('#bankAccountModal').find('select').trigger('change');
            $('#loader').addClass('hidden');
        }
    })
}

$(document).on('click', '.new', function() {
    loadModalData();
    $('#bankAccountModal').modal('show');
});

$(document).on('click', '.edit', function() {
    $('#loader').removeClass('hidden');
    loadModalData($(this).attr('id-bank-account'));
    $('#bankAccountModal').modal('show');
});

// $(document).on('click', '#save_button, #edit_button', function() {
//     $('#bankAccountForm').submit();
// })

$(document).on('submit', '#bankAccountForm', function(e) {
    e.preventDefault();
    $.ajax({
        url: "{{ route('master_bank_account.save') }}",
        data: $('#bankAccountForm').serialize(),
        type: 'POST',
        beforeSend: () => {
            $('.is-invalid').removeClass('is-invalid');
        },
        success: (res) => {
            swal({
                icon: 'success',
                title: 'Success',
                text: res.message,
            });
            $('#bankAccountModal').modal('hide');
        },
        error: (err) => {
            handleValidationError(err.responseJSON.errors)
            swal({
                icon: 'error',
                title: 'Error',
                text: err.responseJSON.message,
                dangerMode: true,
            });
        }
    })
});

$(function () {
    $('#settlementtravel_table').DataTable({
      processing: true,
      pageLength: 50,
      columnDefs: [
        {
            orderable: false,
            // className: 'select-checkbox',
            targets: 0
        }
      ],
      responsive: true,  
      ajax: {
        url: "{{ route('master_bank_account') }}",
        error: function (jqXHR, textStatus, errorThrown) {
          $('#settlementtravel_table').DataTable().ajax.reload();
        }
      },  
      columns: [
        {
            defaultContent: '',
            orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex', title: 'No.'},
        { data: 'bank', name: 'bank', title: 'Bank' },
        { data: 'account_name', name: 'account_name', title: 'Account Name' },
        { data: 'account_number', name: 'account_number', title: 'Account Number' },
        { data: 'branch_name', name: 'branch_name', title: 'Branch Name' },
        { data: 'branch', name: 'branch', title: 'Branch Relation' },
        { data: 'bank_type', name: 'bank_type', title: 'Bank Type' },
        { data: 'status', name: 'status', title: 'Status' },
        { data: 'action', name: 'action', title: 'Action', orderable: false, className: 'space' }
      ],
      rowCallback: function(row, data, index){
        if(access_create == 0){
            $(row).find('.new').css('display', 'none');
        } 
        if(access_edit == 0){
            $(row).find('.btn-edit').css('display', 'none');
        }   
        if(access_delete == 0){
            $(row).find('.btn-del').css('display', 'none');
        }
      },
    });
  });

function handleValidationError(errors) {
    Object.keys(errors).forEach(function (key) {
        var key_temp = key.replaceAll(".", "_");
        $("#" + key_temp + "Error").addClass("is-invalid");
        $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
    });
}

$('#advanced').click(function(){
  $('.cf').select2({width:'100%'});
  if($("#cf").css('display') == 'none'){
    $("#cf").show("slow");
  }
  else {
    $("#cf").hide("slow");
  }   
});
</script>


@endsection