@extends('adminlte::page')
@section('title', 'Notification Letter')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Notification Letter
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Notification Letter</button>
        </div>
      </div>
      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="notifletter_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Category</th>
            <th data-priority="5">Name</th>
            <th>Department</th>
            <th>Division</th>
            <th>Region</th>
            <th data-priority="6">Position</th>
            <th data-priority="7">End Date</th>
            <th>Alamat</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="modal_form_notifletter"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="notifletterForm" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="row" id="actionForm">
           <div class="col-md-6">
            <div class="row">
              <input type="hidden" id="id_letter" name="id_letter">
              <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                <span class="invalid-feedback d-block" role="alert" id="id_categoryError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
               <input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
               <span class="invalid-feedback d-block" role="alert" id="dateError">
                <strong></strong>
              </span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
              <select id="id_employee" name="id_employee" class="form-control form-control-sm select_opsi" style="width: 100%;">
              </select>
              <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                <strong></strong>
              </span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label">PKWT Number <sup class="text text-danger validate"></sup></label>
            <div class="col-sm-8">
             <div class="input-group">            
              <input autocomplete="off" name="remark_1" id="remark_1" class="form-control form-control-sm" style="width: 100%;">
              <div class="input-group-append">
                <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
              </div>
            </div>
            <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
              <strong></strong>
            </span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">PKWT Date <sup class="text text-danger validate"></sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" name="remark_3" id="remark_3" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="remark_3Error">
              <strong></strong>
            </span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="emailError">
              <strong></strong>
            </span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">End Date <sup class="text text-danger validate"></sup></label>
          <div class="col-sm-8">
            <input autocomplete="off" name="expired_date" id="expired_date" class="form-control date_change form-control-sm" style="width: 100%;">
            <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
              <strong></strong>
            </span>
          </div>
        </div>
        <div class="row">
          <label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
          <div class="col-sm-8">
           <select id="id_employee_chief" name="id_employee_chief" class="form-control form-control-sm select_opsi" style="width: 100%;">
           </select>
           <span class="invalid-feedback d-block" role="alert" id="id_employee_chiefError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="remark_2" name="remark_2" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
          <span class="invalid-feedback d-block" role="alert" id="remark_2Error">
            <strong></strong>
          </span>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="row">
        <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_region" name="id_region" class="form-control form-control-sm select_search" style="width: 100%;" readonly></select>
          <span class="invalid-feedback d-block" role="alert" id="id_regionError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_branch" name="id_branch" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <span class="invalid-feedback d-block" role="alert" id="id_branchError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_position_detail" name="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_dept" name="id_dept" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <span class="invalid-feedback d-block" role="alert" id="id_deptError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_job_grade" name="id_job_grade" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Division <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_principal" name="id_principal[]" multiple="" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <span class="invalid-feedback d-block" role="alert" id="id_principalError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_employment_status" name="id_employment_status" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
          </select>
          <input autocomplete="off" hidden="" name="status_input" id="status_input" class="form-control form-control-sm" style="width: 100%;">
          <span class="invalid-feedback d-block" role="alert" id="id_employment_statusError">
            <strong></strong>
          </span>
          <span class="invalid-feedback d-block" role="alert" id="status_inputError">
            <strong></strong>
          </span>
        </div>
      </div>
      <div class="row">
        <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
        <div class="col-sm-8">
          <select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
          <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
            <strong></strong>
          </span>
        </div>
      </div>
    </div>
    <div class="col-lg-12 mt-1">
      <label>Alamat <sup class="text text-danger">*</sup></label>
      <textarea class="form-control" name="remark_4" id="remark_4" rows="4"></textarea>
      <span class="invalid-feedback d-block" role="alert" id="remark_4Error">
        <strong></strong>
      </span>
    </div>
  </div>
  <div class="row" id="viewForm">
    <div class="row">
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Reference Number</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="reference_number_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Name</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="name_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Email</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="email_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Category</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="category_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Letter Date</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="date_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Department</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="department_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Regional</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="regional_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Branch</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="branch_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Position</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="position_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Division</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="division_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Job Grade</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="job_grade_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Status</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="status_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">End Date</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="expired_view" class="view"></span>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Alamat</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="alamat_view" class="view"></span>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Pemberi Keputusan</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="employee_chief_view" class="view"></span>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="form-group">
          <label id="">Jabatan Pemberi Keputusan</label>
        </div>
      </div>
      <div class="col-lg-1">
        <div class="form-group">
          <label>:</label>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="form-group">
          <span id="employee_routing_chief_view" class="view"></span>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
 <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
  <button type="submit" class="btn btn-sm btn-success action"></button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>
@include('eletter/notif_letter/pkwt_number/pkwt')
@endsection
@section('css')
<style type="text/css">
  .modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 9999;
    visibility: hidden;
  }
  .modal-body {
    position: relative;
  }
  .modal.show .modal-loading {
    visibility: visible;
  }

  #notifletter_table td:nth-child(14) {
    text-align: center;
  }
  select[hidden].select2-hidden-accessible + .select2-container {
    display: none;
  }
  select[hidden].select2-hidden-accessible + .select2-container .select2-selection {
    display: none;
  }
  select[readonly].select2-hidden-accessible + .select2-container {
    pointer-events: none;
    touch-action: none;
  }
  select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
    background: #e8ebed;
    box-shadow: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection_clear {
    display: none;
  }

  .custom-select:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  *:focus{
    outline:0px;
  }
</style>
@endsection

@section('scripts')
<script type="text/javascript">
  $(function () {
    $('#notifletter_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('index.notif_letter') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#notifletter_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {   
        data: 'id_letter',
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'reference_number', name: 'reference_number' },
      { data: 'date', name: 'date' },
      { data: 'category', name: 'category' },
      {
        data: 'name',
        name: 'name'
      },
      {
        data: 'dec_dept',
        name: 'dec_dept'
      },
      { data: 'id_principal', name: 'id_principal' },
      { data: 'dec_region', name: 'dec_region' },
      {
        data: 'dec_position',
        name: 'dec_position'
      },
      { data: 'expired_date', name: 'expired_date' },
      { data: 'remark_4', name: 'remark_4' },
      { data: 'action', name: 'action', orderable: false, className: 'space' }
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
        if(access_print == 0){
          $(row).find('.btn-print').css('display', 'none');
        } 
      },
    });
  }); 
  function show_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "block";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "none";
      ModalBody[i].style.background = 'white';
      ModalBody[i].style.opacity = '0.4';
    }
  }
  function hide_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "none";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "auto";
      ModalBody[i].style.background = "transparent";
      ModalBody[i].style.opacity = '1';
    }
  }
  $('#expired_date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#remark_3').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  var ajaxUrl = "";
  function get_data() {
    $.ajax({
      method: "GET",
      url : "{{url('e-letter/company_letter/notif_letter/get_data')}}",
      success: function (response) {
        $(".select_search").select2();
        $("#id_employee").select2({
          placeholder: ".: FILTER OPTION :.",
          data: response.user
        }).val(null).trigger('change');
        $("#id_category").select2({
          placeholder: ".: FILTER OPTION :.",
          data: response.category.map(function(item) {
            return {
              id: item.id,
              text: item.text,
              code: item.code
            }
          })
        }).val(null).trigger('change');
        $("#remark_2").select2({
          placeholder: ".: FILTER OPTION :.",
          data: response.location
        }).val(null).trigger('change');
        // $(".select_opsi").val(null).trigger('change');
      },
      error: function(response) {
       get_data(); 
     }
   });
  }
  $(document).ready(function() {
    get_data();
    get_chief();
  });
  function get_chief() {
    $.ajax({
      method: "GET",
      url : "{{url('e-letter/get-chief/employee')}}",
      success: function (response) {
        $("#id_position_routing_chief").select2();
        $("#id_employee_chief").select2({
          placeholder: ".: FILTER OPTION :.",
          data: response.map(function(item) {
            return {id: item.id_employee, text: item.name};
          })
        });
        $(".select_opsi").val(null).trigger('change');
      },
      error: function(response) {
        get_chief(); 
      }
    });
  }
  $(".new").click(function() {
    $("#notifletterForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $("#id_employee").removeAttr('readonly');
    $("#id_category").removeAttr('readonly');
    $("#date").parent().children('span').children('button').attr('disabled',false);
    $("#date").attr('disabled',false);
    ajaxUrl = "{{route('save.notif_letter')}}"
    $(".action").show();
    $(".invalid-feedback").children("strong").text("");
    $("#notifletterForm input").removeClass("is-invalid");
    $("#notifletterForm select").removeClass("custom-select");
    $("#notifletterForm textarea").removeClass("is-invalid");
    $(".action").html('<i class="fas fa-save"></i> Save');
    $(".action").removeClass('btn-primary')
    $(".action").addClass('btn-success')
    $("#actionForm").show();
    $("#viewForm").hide();
    $(".modal-title").html('Form Notification Letter');
    $("#modal_form_notifletter").modal('show');
  });
  function change_employee(employeeID) {
   $.ajax({
    method: "GET",
    url : "{{url('e-letter/company_letter/notif_letter/change_employee')}}",
    data: {id_employee: employeeID},
    success: function (response) {
      hide_loading();
      $.each(response, function(key, value_data) {
        $("#id_region").append('<option value="'+value_data.id_region+'">' + value_data.dec_region +'</option>');
        $("#id_branch").append('<option value="'+value_data.id_branch+'">' + value_data.dec_branch +'</option>');
        $("#id_position_detail").append('<option value="'+value_data.id_routing+'">' + value_data.dec_position +'</option>');
        $("#id_job_grade").append('<option value="'+value_data.id_job_grade+'">' + value_data.dec_job_grade +'</option>');
        $("#id_employment_status").append('<option value="' + value_data.id_employment_status + '">'+ value_data.status +'</option>');
        $("#id_dept").append('<option value="'+value_data.id_dept+'">' + value_data.dec_dept +'</option>');
        $("#id_principal").append('<option selected value="'+value_data.dec_principal+','+'">' + value_data.dec_principal +'</option>');
      });
    },
    error: function(response) {
      change_employee(employeeID); 
    }
  });
 }
 $(document).on('change','#id_employee',function() {
  var employeeID = $(this).val();
  if (employeeID) {
    $(".select_search").empty();
    show_loading();
    change_employee(employeeID); 
  }else{
    $(".select_search").empty();
  }
});
 function change_position_chief(employeeID) {
  if (employeeID) {
    show_loading();
    $.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
      if (response) {
        hide_loading();
        $("#id_position_routing_chief").empty();
        $("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
      } else {
        $("#id_position_routing_chief").empty();
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      change_position_chief(employeeID);
    });
  } else {
    $("#id_position_routing_chief").empty();
  }
}
$(document).on('change','#id_employee_chief',function() {
  var employeeID = $(this).val();
  $("#id_position_routing_chief").empty();
  if (employeeID) {
    change_position_chief(employeeID);
  }
});
$(function () {
  $('#notifletterForm').submit(function (e) {
    e.preventDefault();
    document.querySelector(".action").disabled=true;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#notifletterForm input").removeClass("is-invalid");
    $("#notifletterForm select").removeClass("custom-select");
    $("#notifletterForm textarea").removeClass("is-invalid");
    $.ajax({
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      url: ajaxUrl,
      data: formData,
      success: function (response) {
        document.querySelector(".action").disabled=false;
        if (response.status == 'true') {
          $("#notifletterForm")[0].reset();
          $('#modal_form_notifletter').modal('hide');
          swal({
            icon: 'success',
            title: 'Success',
            text: response.message
          });
          $('#notifletter_table').DataTable().ajax.reload();
        }else {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! ['+response.message+']'
          });
        }
      },
      error: function (response) {
        document.querySelector(".action").disabled=false;
        if (response.status === 422) {
          let errors = response.responseJSON.errors;
          Object.keys(errors).forEach(function (key) {
            $("#" + key).addClass("is-invalid");
            $("select[id='" + key + "']").addClass("custom-select");
            $("#" + key + "Error").children("strong").text(errors[key][0]);
          });
        }
        else {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! ['+response.message+']'
          });
        }
      }
    });
  });
});
$("#id_category").change(function() {
  categoryID = $(this).val();
  if (categoryID) {
    var selectedOptionCategory = $(this).select2('data')[0];
    if (selectedOptionCategory) {
      var codeCategory = selectedOptionCategory.code;
      if (codeCategory == 'SPB-PHK') {
        $("#filter").attr('disabled',true);
        $(".validate").html('');
        $("#remark_1").val('');
        $("#remark_1").attr('disabled',true);
        $("#remark_3").attr('disabled',true);
        $("#remark_3").parent().children('span').children('button').attr('disabled',true);
        $("#remark_3").val('');
        $("#expired_date").attr('disabled',true);
        $("#expired_date").parent().children('span').children('button').attr('disabled',true);
        $("#expired_date").val('');
      }else{
        $("#filter").attr('disabled',false);
        $(".validate").html('*');
        $("#remark_1").attr('disabled',false);
        $("#remark_3").attr('disabled',false);
        $("#remark_3").parent().children('span').children('button').attr('disabled',false);
        $("#remark_3").val('');
        $("#expired_date").attr('disabled',false);
        $("#expired_date").parent().children('span').children('button').attr('disabled',false);
        $("#expired_date").val('');
      }
    }
  }else{
    $("#filter").attr('disabled',true);
    $(".validate").html('*');
    $("#remark_1").attr('disabled',true);
    $("#remark_1").val('');
    $("#remark_3").val('');
    $("#remark_3").attr('disabled',true);
    $("#remark_3").parent().children('span').children('button').attr('disabled',true);
    $("#expired_date").attr('disabled',true);
    $("#expired_date").parent().children('span').children('button').attr('disabled',true);
    $("#expired_date").val('');
  }
});
function get_edit(letterID) {
  $.ajax({
    type: "GET",
    url: "{{url('e-letter/company_letter/notif_letter/get_edit')}}"+"/"+letterID,
    success: function(response) {
      if (response) {
        hide_loading();
        $.each(response, function(key, value) {
          $("#date").parent().children('span').children('button').attr('disabled',true);
          $("#date").attr('disabled',true).val(value.date);
          $("#id_category").attr('readonly','readonly');
          $("#id_category").val(value.id_category).trigger('change');
          $("#remark_1").val(value.remark_1);
          $("#remark_3").val(value.remark_3);
          $("#date").val(value.date);
          $("#id_letter").val(value.id_letter);
          $("#expired_date").val(value.expired_date);
          $("#remark_4").val(value.remark_4);
          $("#email").val(value.email);
          $("#remark_2").val(value.remark_2).trigger('change');
          $("#id_employee_chief").val(value.id_employee_chief).trigger('change');
          $("#id_employee").val(value.id_employee).trigger('change');
          $("#id_employee").attr('readonly','readonly');
        });
      }
    },
    error: function(response) {
      get_edit(letterID);
    }
  });
}
$(document).ready(function() {
  $(document).on('click', '.btn-edit', function() {
    show_loading();
    var letterID = $(this).attr('more_id');
    $("#notifletterForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    ajaxUrl="{{route('update.notif_letter')}}";
    $(".action").show();
    $(".invalid-feedback").children("strong").text("");
    $("#notifletterForm input").removeClass("is-invalid");
    $("#notifletterForm select").removeClass("custom-select");
    $("#notifletterForm textarea").removeClass("is-invalid");
    $(".action").html('<i class="fas fa-edit"></i> Update');
    $(".action").removeClass('btn-success')
    $(".action").addClass('btn-primary')
    $("#actionForm").show();
    $("#viewForm").hide();
    $(".modal-title").html('Form Notification Letter');
    $("#modal_form_notifletter").modal('show');
    if (letterID) {
      get_edit(letterID);
    }
  });
});
$(document).on('click', '.btn-del', function (event) {
  letterID = $(this).attr('more_id');
  event.preventDefault();
  swal({
    title: 'Are you sure?',
    text: 'This record and it`s details will be permanantly deleted!',
    icon: 'warning',
    buttons: true,
    dangerMode: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then(function(value) {
    if (value) {
      $.ajax({
        method: "GET",
        url: "{{url('e-letter/company_letter/notif_letter/destroy')}}"+"/"+letterID,
        success:function(response)
        {
          if (response.status == 'true') {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#notifletter_table').DataTable().ajax.reload();         
            }, 50);
          }else{
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Something went wrong! ['+response.message+']'
            });
          }
        },
        error: function(response) {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! ['+response.message+']'
          });
        }
      })
    }
  });
});
$(document).on('click','.btn-mail',function() {
  var id = $(this).attr('more_id');
  var token = $(this).attr('more_token');
  var type = $(this).attr('more_type');
  $("#mail_send-"+id).html('<div class="spinner-border spinner-border-sm" role="status"></div>');
  document.getElementById('mail_send-'+id).disabled=true;
  if (token) {
    $.ajax({
      type: "GET",
      url: "{{url('e-letter/notif/send-mail')}}"+"/"+token+"-"+id+"?type="+type,
      success: function(response) {
        if (response.status == 'success') {
          swal({
            title: "Email Success!",
            icon: "success"
          });
          document.getElementById('mail_send-'+id).disabled=false;
          $("#mail_send-"+id).html('<span class="fa fa-envelope"></span>');
        }else if(response.status == 'email_null'){
         document.getElementById('mail_send-'+id).disabled=false;
         $("#mail_send-"+id).html('<span class="fa fa-envelope"></span>');
         swal({
          icon: 'warning',
          type: 'warning',
          title: 'Oops...',
          text: response.message
        });
       }else{
        document.getElementById('mail_send-'+id).disabled=false;
        $("#mail_send-"+id).html('<span class="fa fa-envelope"></span>');
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! ['+response.message+']'
        });
      }
    },
    error: function (response) {
      document.getElementById('mail_send-'+id).disabled=false;
      $("#mail_send-"+id).html('<span class="fa fa-envelope"></span>');
      swal({
        icon: 'error',
        title: 'Oops...',
        dangerMode: true,
        text: 'Something went wrong! ['+response.message+']'
      });
    }
  });
  }
});
moment.updateLocale('id', {
  weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
});
function TanggalIndonesia(string) {
  var formattedDate = moment(string).format('dddd, D MMMM YYYY');
  return formattedDate;
}
function get_view(letterID) {
  if (letterID) {
    $.ajax({
      type: "GET",
      url: "{{url('e-letter/company_letter/notif_letter/get_edit')}}"+"/"+letterID,
      success: function(response) {
        if (response) {
          $(".view").html('');
          $.each(response, function(key, value) {
            var name = value.name;
            var dec_dept = value.dec_dept;
            var dec_branch = value.dec_branch;
            var dec_position = value.dec_position;
            var id_principal = value.id_principal;
            var dec_job_grade = value.dec_job_grade;
            var status = value.status;
            $(".modal-title").html(value.reference_number);
            $("#reference_number_view").html(value.reference_number);
            $("#name_view").html(name);
            $("#email_view").html(value.email);
            $("#category_view").html(value.dec_category);
            $("#date_view").html(TanggalIndonesia(value.date));
            $("#department_view").html(dec_dept);
            $("#regional_view").html(value.dec_region);
            $("#branch_view").html(dec_branch);
            $("#position_view").html(dec_position);
            $("#division_view").html(value.id_principal);
            $("#job_grade_view").html(dec_job_grade);
            $("#status_view").html(status);
            if (value.expired_date != null) {
              $("#expired_view").html(TanggalIndonesia(value.expired_date));
            }
            $("#alamat_view").html(value.remark_4);
            $("#employee_chief_view").html(value.name_chief);
            $("#employee_routing_chief_view").html(value.position_chief);
          });
        }
      },
      error: function(response) {
        get_view(letterID);
      }
    });
  }
}
$(document).on('click','.btn-view',function() {
  $("#modal_form_notifletter").modal('show');
  $(".action").hide();
  $("#actionForm").hide();
  $("#viewForm").show();
  $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
  $(".modal-title").html('');
  var letterID = $(this).attr('more_id');
  if (letterID) {
    get_view(letterID);
  }
});
$(document).ready(function() {
  $("#filter").click(function () {
    var employeeID = $("#id_employee").val();
    if (employeeID) {
      $("#modal_view_pkwt").modal('show');
      if ($.fn.DataTable.isDataTable('#table_view_pkwt')) {
        $('#table_view_pkwt').DataTable().destroy();
      }
      $('#table_view_pkwt').DataTable({
        processing: true,
        pageLength: 10,
        responsive: true,
        autoWidth: false,
        ajax: {
          url: "{{ url('e-letter/company_letter/notif_letter/pkwt-number') }}",
          data : {id_url:global_url_server, id_employee:employeeID},
          error: function (jqXHR, textStatus, errorThrown) {
            $('#table_view_pkwt').DataTable().ajax.reload();
          }
        },
        columns: [
        {
          defaultContent: '',
          orderable: false,
        },
        {
          defaultContent: '',
          orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'reference_number', name: 'reference_number' },
        { data: 'date', name: 'date' },
        { data: 'expired_date', name: 'expired_date' },
        {
          data: 'action',
          name: 'action',
          orderable: false,
          render: function (data, type, row) {
            return data;
          }
        },
        ]
      });
    }else{
      alert('Select Name');
    }
  });
});
$("#modal_view_pkwt").on('hidden.bs.modal', function () {
  if ($.fn.DataTable.isDataTable('#table_view_employee')) {
    $('#table_view_pkwt').DataTable().destroy();
  }
});
$(document).on('click','.choose_number', function() {
  var NumberPkwt = $(this).attr('more_number');
  var DatePkwt = $(this).attr('more_date');
  var EndDatePkwt = $(this).attr('more_end');
  var electronicID = $(this).attr('more_id');
  if (electronicID) {
    $("#modal_view_pkwt").modal('hide');
    $("#remark_1").val('');
    $("#remark_3").val('');
    $("#expired_date").val('');
    show_loading();
    setTimeout(function() {
      hide_loading();
      $("#remark_1").val(NumberPkwt);
      $("#remark_3").val(DatePkwt);
      $("#expired_date").val(EndDatePkwt);
    }, 400);
  }
});
</script>
@endsection