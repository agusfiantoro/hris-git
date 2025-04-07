@extends('adminlte::page')
@section('title', 'Internal Agreement')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Internal Agreement</h5>
        <div class="card-tools">
          <button type="button" action="new" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Internal Agreement</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="pi_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th data-priority="8">No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Name</th>
            <th data-priority="5">Category</th>
            <th data-priority="6">Position</th>
            <th>Job Grade</th>
            <th>Location</th>
            <th>Branch</th>
            <th>Department</th>
            <th data-priority="7">Regional</th>
            <th>Start / Effective Date</th>
            <th>End / Expired Date</th>
            <th>Notes</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="modal_form_pi"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="piForm">
          {{ csrf_field() }}
          <div id="formCRUD" style="display: none;">
            <div class="row">
              <div class="col-md-6">
                <div class="row">
                  <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <input type="hidden" id="id_letter" name="id_letter" hidden="">
                    <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;">
                    </select>
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
                  <label class="col-sm-4 col-form-label">Status Employee <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <select id="remark_1" name="remark_1" class="form-control form-control-sm select_opsi" style="width: 100%;">
                      <option value="Active">Active</option>
                      <option value="Inactive">Inactive</option>
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="remark_1Error">
                      <strong></strong>
                    </span>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <div class="input-group">
                     <select id="id_employee" name="id_employee" class="form-control-sm select_search" style="width: 88%;" readonly>
                     </select>
                     <div class="input-group-append">
                      <button disabled="" type="button" class="input-group-text btn-sm btn bg-success btn-success text-white filter" id="filter"><i class="far fa-list-alt"></i></button>
                    </div>
                  </div>
                  <span class="invalid-feedback d-block" role="alert" id="id_employeeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label"><span id="date_start">Effective</span> Date <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label"> <span id="date_end">Expired</span> Date</label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="expired_date" id="expired_date" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Notes <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="notes" id="notes" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="notesError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
             <div class="row">
              <label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_position_detail" id="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Departement <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_dept" id="id_dept" class="form-control form-control-sm select_disabled select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_deptError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_job_grade" id="id_job_grade" class="form-control form-control-sm select_disabled select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_branch" id="id_branch" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Location <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_location" id="id_location" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_locationError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <select name="id_region" id="id_region" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
                </select>
                <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                  <strong></strong>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- view -->
      <div id="formView">
        <div class="row">
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
              <span id="letter_date_view" class="view"></span>
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
              <label id="">Region</label>
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
              <label id="">Grade</label>
            </div>
          </div>
          <div class="col-lg-1">
            <div class="form-group">
              <label>:</label>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="form-group">
              <span id="grade_view" class="view"></span>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label id="">Location</label>
            </div>
          </div>
          <div class="col-lg-1">
            <div class="form-group">
              <label>:</label>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="form-group">
              <span id="location_view" class="view"></span>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label id=""><span id="date_start_view">Effective</span> Date</label>
            </div>
          </div>
          <div class="col-lg-1">
            <div class="form-group">
              <label>:</label>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="form-group">
              <span id="effective_view" class="view"></span>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="form-group">
              <label id=""><span id="date_end_view">Expired</span> Date</label>
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
              <label id="">Notes</label>
            </div>
          </div>
          <div class="col-lg-1">
            <div class="form-group">
              <label>:</label>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="form-group">
              <span id="notes_view" class="view"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-loading" id="modal-loading" style="display: none;">
     <span class="fa fa-spinner fa-spin fa-3x"></span>
   </div>
   <div class="modal-footer">
    <button type="submit" class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
  </div>
</form>
</div>
</div>
</div>
@include('eletter/internal_agreement/employee/index')
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

  #pi_table td:nth-child(17) {
    text-align: center;
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
  $(function () {
    $('#pi_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,  
      ajax: {
        url: "{{ route('pi.index') }}",
        data: {id_url: global_url_server},      
        error: function (jqXHR, textStatus, errorThrown) {
          $('#pi_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
        {   // Checkbox select column
          data: 'id_letter',
          defaultContent: '',
          orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
        { data: 'reference_number', name: 'reference_number' },
        { data: 'date', name: 'date' },
        { 
          data: 'name',
          name: 'name'
        },
        { data: 'dec_category', name: 'dec_category' },
        { data: 'dc_position', name: 'dc_position' },
        { data: 'dec_job_grade', name: 'dec_job_grade' },
        { data: 'dec_location', name: 'dec_location' },
        { data: 'dec_branch', name: 'dec_branch' },
        { data: 'dc_dept', name: 'dc_dept' },
        { data: 'dc_region', name: 'dc_region' },
        { data: 'effective_date', name: 'effective_date' },
        { data: 'expired_date', name: 'expired_date' },
        { data: 'notes', name: 'notes' },
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
        }
      });
  });  
  function get_data() {
    $.getJSON("{{ url('e-letter/employee_agreement/internal_agreement/get_data') }}", function(response) {
      $.each(response, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  $(document).ready(function() {
    get_data();
  });
  $('#date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#effective_date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#expired_date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  var urlAjax = "";
  function resetForm() {
    $("#piForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#piForm input").removeClass("is-invalid");
    $("#piForm select").removeClass("custom-select");
  }
  function change_status(statusEmployee) {
    $(document).on('change','#remark_1',function() {
      var statusEmployee = $(this).val();
      if (statusEmployee) {
        $(".select_search").empty();
        document.getElementById('filter').disabled=false;
      }else{
        $(".select_search").empty();
        document.getElementById('filter').disabled=true;
      }
    });
  }
  function form_modal(action) {
    resetForm();
    $("#modal_form_pi").modal('show');
    $(".modal-title").html('');
    if (action == 'view') {
      $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
      document.getElementById('formCRUD').style.display = "none";
      document.getElementById('formView').style.display = "block";
      $(".action").hide();
    }else{
      $(".action").show();
      $(".modal-title").html('Form Internal Agreement');
      document.getElementById('formCRUD').style.display = "block";
      document.getElementById('formView').style.display = "none";
    }
    if (action == 'new') {
      var statusEmployee = $("#remark_1").val();
      change_status(statusEmployee);
      document.getElementById('id_category').removeAttribute('readonly');
      document.getElementById('remark_1').removeAttribute('readonly');
      $("#date").parent().children('span').children('button').attr('disabled',false);
      $("#date").prop('disabled',false);
      $(".select_opsi").select2({
        allowClear: true,
        placeholder: ':. FILTER OPTION .:'
      });
      $(".select_search").select2();
      $(".select_search").empty();
      $(".select_opsi").val(null).trigger('change');
    }else if (action == 'edit'){
      $("#date").parent().children('span').children('button').attr('disabled',true);
      $("#date").prop('disabled',true);
      $(".select_opsi").select2();
      $(".select_search").select2();
      $(".select_search").empty();
    }
  }
  $(".new").click(function() {
    var action = $(this).attr('action');
    urlAjax = "{{route('save.pi')}}";
    form_modal(action);
    $("#filter").attr('disabled',true);
  });
  $(document).ready(function() {
    $("#filter").click(function () {
      const moreStatus = $("#remark_1").val();
      if (moreStatus) {
        $("#modal_view_employee").modal('show');
        if ($.fn.DataTable.isDataTable('#table_view_employee')) {
          $('#table_view_employee').DataTable().destroy();
        }
        $('#table_view_employee').DataTable({
          processing: true,
          pageLength: 10,
          responsive: true,
          autoWidth: false,
          ajax: {
            url: "{{ url('e-letter/employee_agreement/internal_agreement/get_employee') }}",
            data : {id_url:global_url_server,status:moreStatus},
            error: function (jqXHR, textStatus, errorThrown) {
              $('#table_view_employee').DataTable().ajax.reload();
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
          { data: 'name', name: 'name' },
          { data: 'nik_employee', name: 'nik_employee' },
          { data: 'dec_position', name: 'dec_position' },
          { data: 'join_date', name: 'join_date' },
          { 
            data: 'resign_date', 
            name: 'resign_date',
            render: function (data, type, row) {
              if (data === null) {
                return '-';
              } else {
                return data;
              }
            }
          },
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
        alert('Select Status');
      }
    });
  });
  $("#modal_view_employee").on('hidden.bs.modal', function () {
    if ($.fn.DataTable.isDataTable('#table_view_employee')) {
      $('#table_view_employee').DataTable().destroy();
    }
  });
  function change_employee(employeeStatus, employeeID) {
    if (employeeID) {
      $("#modal_view_employee").modal('hide');
      $("#id_employee").empty();
      $(".select_search").empty();
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/employee_agreement/internal_agreement/change_employee')}}",
        data: {employee_status:employeeStatus, employee_id:employeeID},
        success: function(response) {
          if (response) {
            hide_loading();
            $.each(response, function(key, value) {
              $("#id_employee").append('<option value="' + value.id_employee + '">'+ value.name +' ('+value.nik_employee+')'+'</option>');
              $("#id_position_detail").append('<option value="'+value.id_routing+'">'+value.dec_position+'</option>');
              $("#id_region").append('<option value="'+value.id_region+'">'+value.dec_region+'</option>');
              $("#id_location").append('<option value="'+value.id_location+'">'+value.dec_location+'</option>');
              $("#id_branch").append('<option value="'+value.id_branch+'">'+value.dec_branch+'</option>');
              $("#id_dept").append('<option value="'+value.id_dept+'">'+value.dec_dept+'</option>');
              $("#id_job_grade").append('<option value="'+value.id_job_grade+'">'+value.dec_job_grade+'</option>');
            });
          }else{
            $("#id_employee").empty();
          }
        },
        error: function(response) {
          hide_loading();
          swal({
           icon: 'error',
           title: 'Oops...',
           dangerMode: true,
           text: 'Something went wrong! [Unknown Error]'
         });
        }
      });
    }else{
      $(".select_search").empty();
    }
  }
  $(document).on('click','.choose_employee',function() {
    var employeeStatus = $(this).attr('more_status');
    var employeeID = $(this).attr('more_id');
    if (employeeID) {
      change_employee(employeeStatus, employeeID);
    }
  });
  $(function () {
    $('#piForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#piForm input").removeClass("is-invalid");
      $("#piForm select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: urlAjax,
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#piForm")[0].reset();
            $(".select_search").empty();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_pi').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#pi_table').DataTable().ajax.reload();
          }
        },
        error: function (response, textStatus, errorThrown) {
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
  function get_edit(letterID, action) {
    if (letterID) {
      form_modal(action);
      $.getJSON("{{url('e-letter/employee_agreement/internal_agreement/get_edit')}}"+"/"+letterID, function(response) {
        if (response) {
          hide_loading();
          $.each(response, function(key, value) {
            $("#id_letter").val(value.id_letter);
            $("#date").val(value.date);
            $("#notes").val(value.notes);
            $("#effective_date").val(value.effective_date);
            $("#expired_date").val(value.expired_date);
            $("#id_category").val(value.id_category).trigger('change');
            document.getElementById('id_category').setAttribute('readonly','readonly');
            $("#remark_1").val(value.remark_1).trigger('change');
            document.getElementById('remark_1').setAttribute('readonly','readonly');
            $("#id_employee").append('<option value="' + value.id_employee + '">'+ value.name +' ('+value.nik_employee+')'+'</option>');
            $("#id_position_detail").append('<option value="'+value.dec_position+'">' + value.dec_position +'</option>');
            $("#id_dept").append('<option value="'+value.dec_dept+'">' + value.dec_dept +'</option>');
            $("#id_job_grade").append('<option value="'+value.dec_job_grade+'">' + value.dec_job_grade +'</option>');
            $("#id_branch").append('<option value="'+value.dec_branch+'">' + value.dec_branch +'</option>');
            $("#id_region").append('<option value="'+value.dec_region+'">' + value.dec_region +'</option>');
            $("#id_location").append('<option value="'+value.dec_location+'">' + value.dec_location +'</option>');
            $("#filter").attr('disabled',true);
          });
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        get_edit(letterID, action);
      });
    }else{
      $("#modal_form_pi").modal('hide');
    }
  }
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function formatDateToIndonesian(dateString) {
    var formattedDate = moment(dateString).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(letterID, action) {
   if (letterID) {
    form_modal(action);
    $.getJSON("{{url('e-letter/employee_agreement/internal_agreement/get_edit')}}"+"/"+letterID, function(response) {
      if (response) {
        $.each(response, function(key, value) {
          $(".modal-title").html(value.reference_number);
          $("#letter_date_view").html(formatDateToIndonesian(value.date));
          $("#category_view").html(value.category);
          $("#effective_view").html(formatDateToIndonesian(value.effective_date));
          $("#expired_view").html(formatDateToIndonesian(value.expired_date));
          $("#name_view").html(value.remark_1);
          $("#position_view").html(value.dec_position);
          $("#department_view").html(value.dec_dept);
          $("#grade_view").html(value.dec_job_grade);
          $("#regional_view").html(value.dec_region);
          $("#branch_view").html(value.dec_branch);
          $("#location_view").html(value.dec_location);
          $("#notes_view").html(value.notes);
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_view(letterID, action);
    });
  }else{
    $("#modal_form_pi").modal('hide');
  }
}
$(document).on('click', '.btn-edit', function() {
  show_loading();
  var letterID = $(this).attr('more_id');
  var action = $(this).attr('action');
  urlAjax = "{{route('edit.pi')}}";
  if (letterID) {
    get_edit(letterID, action);
  }
});
$(document).on('click', '.btn-view', function() {
  var letterID = $(this).attr('more_id');
  var action = $(this).attr('action');
  if (letterID) {
    get_view(letterID, action);
  }
});
$(document).on('click', '.btn-del', function (event) {
  id_letter = $(this).attr('more_id');
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
      url: "{{url('e-letter/employee_agreement/internal_agreement/destroy')}}/"+id_letter,
      success:function(data)
      {
        setTimeout(function(){
          swal({
            title: "Data Deleted!",
            icon: "success"
          });
          $('#confirmModal').modal('hide');
          $('#pi_table').DataTable().ajax.reload();         
        }, 50);
      }
    })
   }
 });
});
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