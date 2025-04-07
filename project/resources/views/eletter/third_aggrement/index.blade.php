@extends('adminlte::page')
@section('title', 'Third Party Agreement')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Third Party Agreement</h5>
        <div class="card-tools">
          <button type="button" action="new" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Third Party Agreement</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="tag_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Category</th>
            <th data-priority="7">Regional</th>
            <th>Branch</th>
            <th>Department</th>
            <th data-priority="5">Effective Date</th>
            <th data-priority="6">Expired Date</th>
            <th>Notes</th>
            <th data-priority="1" class="text text-center" width="300">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>

<div class="modal fade" id="modal_form_tag"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="tagForm">
          {{ csrf_field() }}
          <!-- <div id="modal_detail"></div> -->
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
                  <label class="col-sm-4 col-form-label"><span id="date_start">Effective</span> Date <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="effective_dateError">
                      <strong></strong>
                    </span>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label"> <span id="date_end">Expired</span> Date <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <input autocomplete="off" name="expired_date" id="expired_date" class="form-control form-control-sm" style="width: 100%;">
                    <span class="invalid-feedback d-block" role="alert" id="expired_dateError">
                      <strong></strong>
                    </span>
                  </div>
                </div>
				<div class="row">
                  <label class="col-sm-4 col-form-label">Addendum Number</label>
                  <div class="col-sm-8">
                    <input autocomplete="off" name="number_notes" id="number_notes" class="form-control form-control-sm" style="width: 100%;" readonly>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="row">
                  <label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <select name="id_region" id="id_region" class="form-control form-control-sm select_opsi" style="width: 100%;">
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_regionError">
                      <strong></strong>
                    </span>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <select name="id_branch" id="id_branch" class="form-control form-control-sm select_opsi" style="width: 100%;">
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_branchError">
                      <strong></strong>
                    </span>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-4 col-form-label">Departement <sup class="text text-danger">*</sup></label>
                  <div class="col-sm-8">
                    <select name="id_dept" id="id_dept" class="form-control form-control-sm select_opsi" style="width: 100%;">
                    </select>
                    <span class="invalid-feedback d-block" role="alert" id="id_deptError">
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
            </div>
          </div>
          <!-- View -->
          <div id="formView" style="display: none;">
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
			  <div class="col-lg-4">
                <div class="form-group">
                  <label id="">Addendum Number</label>
                </div>
              </div>
              <div class="col-lg-1">
                <div class="form-group">
                  <label>:</label>
                </div>
              </div>
              <div class="col-lg-7">
                <div class="form-group">
                  <span id="number_notes_view" class="view"></span>
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
  #tag_table td:nth-child(12) {
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
$(document).on('change', '#id_category', function (event, istrigger) {
	show_loading();
    if(!istrigger){
		if($("#id_category option:selected").text() == 'Addendum atau Amandemen Perjanjian lain-lain'){
			$('#number_notes').attr('readonly',false);
		}
		else{
			$('#number_notes').attr('readonly',true);
		}
	hide_loading();	
	}
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
  $(function () {
    $('#tag_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,  
      ajax: {
        url: "{{ route('tag.index') }}",
        data: {id_url: global_url_server},      
        error: function (jqXHR, textStatus, errorThrown) {
          $('#tag_table').DataTable().ajax.reload();
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
        { data: 'dec_category', name: 'dec_category' },
        { data: 'dc_region', name: 'dc_region' },
        { data: 'dec_branch', name: 'dec_branch' },
        { data: 'dc_dept', name: 'dc_dept' },
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
    $.getJSON("{{ url('e-letter/employee_agreement/third_aggrement/get_data') }}", function(response) {
      $(".select_opsi").empty();
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
      });
      $.each(response.region, function(key, value_region) {
        $("#id_region").append('<option value="' + value_region.id_region + '">'+ value_region.description +'</option>');
      });
      $.each(response.dept, function(key, value_dept) {
        $("#id_dept").append('<option value="' + value_dept.id_dept + '">'+ value_dept.description +'</option>');
      });
      $(".select_opsi").val(null).trigger('change');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  $(document).ready(function() {
    get_data();
  });
  function date_picker() {
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
  }
  function resetForm() {
    $("#tagForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#tagForm input").removeClass("is-invalid");
    $("#tagForm select").removeClass("custom-select");
  }
  function form_modal(actionType) {
    $("#modal_form_tag").modal('show');
    hide_loading();
    $(".modal-title").html('');
    if (actionType != 'view') {
      show_loading();
      $(".modal-title").html('Form Third Party Agreement');
      $(".action").show();
    }else{
      $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
      $(".action").hide();
    }
    if (actionType != 'view') {
      document.getElementById('formCRUD').style.display = "block";          
      document.getElementById('formView').style.display = "none";          
    }else{
      document.getElementById('formCRUD').style.display = "none"; 
      document.getElementById('formView').style.display = "block";          
    }
    date_picker();
    if (actionType == 'new') {
      resetForm();
      hide_loading();
      $(".select_opsi").select2({
        allowClear: true,
        placeholder: ":. FILTER OPTION .:"
      });
      document.getElementById('id_category').removeAttribute('readonly');
      document.getElementById('id_dept').removeAttribute('readonly');
      document.getElementById('id_region').removeAttribute('readonly');
    }else if (actionType == 'edit'){
      resetForm();
      $(".select_opsi").select2();
      document.getElementById('id_category').setAttribute('readonly','readonly');
      document.getElementById('id_dept').setAttribute('readonly','readonly');
      document.getElementById('id_region').setAttribute('readonly','readonly');
    }   
  }
  var actionType = "";
  $(".new").click(function() {
    actionType = $(this).attr('action');
    urlAjax = "{{route('save.tag')}}";
    form_modal(actionType);
  });
  function change_region(regionID) {
    if (regionID) {
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/employee_agreement/third_aggrement/change_region')}}"+"?id_region="+regionID,
        data : {id_url:global_url_server},
        success: function(response) {
          if (response) {
            hide_loading();
            $.each(response, function(key, value) {
              $("#id_branch").append('<option value="' + value.id_branch + '">'+ value.description +'</option>');
            });
            $("#id_branch").val(sessionStorage.getItem('id_branch')).trigger('change');
          }else{
            $("#id_branch").empty();
          }
        },
        error: function(response) {
          change_region(regionID);
        }
      });
    }else{
      $("#id_branch").empty();
    }
  }
  $(document).on('change','#id_region',function() {
    var regionID = $(this).val();
    $("#id_branch").empty();
    if (regionID) {
      change_region(regionID);
    }
  });
  $(function () {
    $('#tagForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#tagForm input").removeClass("is-invalid");
      $("#tagForm select").removeClass("custom-select");
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
            $("#tagForm")[0].reset();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_tag').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#tag_table').DataTable().ajax.reload();
          }else {
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Something went wrong! '+response.message
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
  function get_edit(letterID) {
    if (letterID) {
      $.getJSON("{{url('e-letter/employee_agreement/third_aggrement/getEdit')}}"+"/"+letterID, function(response) {
        if (response) {
          hide_loading();
          $.each(response, function(key, value) {
            $("#id_letter").val(value.id_letter);
            $("#date").val(value.date);
            $("#number_notes").val(value.remark_1);
            $("#notes").val(value.notes);
            $("#effective_date").val(value.effective_date);
            $("#expired_date").val(value.expired_date);
            $("#id_category").val(value.id_category).trigger('change');
            $("#id_dept").val(value.id_dept).trigger('change');
            $("#id_region").append('<option value="'+value.id_region+'">' + value.dc_region +'</option>').val(value.id_region).trigger('change');
            sessionStorage.setItem("id_branch", value.id_branch);
          });
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        get_edit(letterID);
      });
    }else{
      $("#modal_form_tag").modal('hide');
    }
  }
  $(document).on('click', '.btn-edit', function() {
    urlAjax = "{{route('edit.tag')}}";
    var letterID = $(this).attr('more_id');
    actionType = $(this).attr('action');
    sessionStorage.clear();
    get_edit(letterID);
    form_modal(actionType);
  });
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function formatDateToIndonesian(dateString) {
    var formattedDate = moment(dateString).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(letterID) {
   if (letterID) {
    $.getJSON("{{url('e-letter/employee_agreement/third_aggrement/getEdit')}}"+"/"+letterID, function(response) {
      if (response) {
        $.each(response, function(key, value) {
          $(".modal-title").html(value.reference_number);
          $("#letter_date_view").html(formatDateToIndonesian(value.date));
          $("#category_view").html(value.category);
          $("#effective_view").html(formatDateToIndonesian(value.effective_date));
          $("#expired_view").html(formatDateToIndonesian(value.expired_date));
          $("#department_view").html(value.dc_dept);
          $("#regional_view").html(value.dc_region);
          $("#branch_view").html(value.dec_branch);
          $("#number_notes_view").html(value.remark_1);
          $("#notes_view").html(value.notes);
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_view(letterID);
    });
  }else{
    $("#modal_form_tag").modal('hide');
  }
}
$(document).on('click', '.btn-view', function() {
  var letterID = $(this).attr('more_id');
  var actionType = $(this).attr('action');
  get_view(letterID);
  form_modal(actionType);
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
        url: "{{url('e-letter/employee_agreement/third_aggrement/destroy')}}"+"/"+id_letter,
        success:function(data)
        {
          setTimeout(function(){
            swal({
              title: "Data Deleted!",
              icon: "success"
            });
            $('#tag_table').DataTable().ajax.reload();         
          }, 50);
        }
      })
    }
  });
});
</script>
@endsection