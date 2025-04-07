@extends('adminlte::page')
@section('title', 'External Letter')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">External Letter
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add External Letter</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="ext_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th data-priority="6">No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Date</th>
            <th data-priority="4">Category</th>
            <th data-priority="5">Branch</th>
           <!--  <th>Department</th>
            <th>Region</th>
            <th>Branch</th> -->
            <th data-priority="5">Keterangan</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
<!-- Form Modal -->
<div class="modal fade" id="modal_form_ext"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Form External Letter</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="extForm" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
                <div class="col-sm-8">
                 <select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                 <input type="" id="id_letter" hidden="" name="id_letter">
                 <span class="invalid-feedback d-block" role="alert" id="id_categoryError"><strong></strong></span>
               </div>
             </div>
             <div class="row">
              <label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
              <div class="col-sm-8">
                <input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
                <span class="invalid-feedback d-block" role="alert" id="dateError"><strong></strong></span>
              </div>
            </div>
          </div>
         <!--  <div class="col-md-6">
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
            <label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
              <select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
              <span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
                <strong></strong>
              </span>
            </div>
          </div>
        </div> -->
        <div class="col-md-6">
          <div class="row">
            <label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
              <input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
              <span class="invalid-feedback d-block" role="alert" id="emailError"><strong></strong></span>
            </div>
          </div>
          <div class="row">
            <label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
            <div class="col-sm-8">
             <select id="id_branch" name="id_branch" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
             <span class="invalid-feedback d-block" role="alert" id="id_categoryError"><strong></strong></span>
           </div>
         </div>
       </div>
     </div>
     <div class="row">
      <div class="col-xl-12">
        <div class="form-group">
          <label>Keterangan</label>
          <textarea class="form-control" rows="4" name="notes" id="notes"></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="modal-loading" id="modal-loading" style="display: none;">
   <span class="fa fa-spinner fa-spin fa-3x"></span>
 </div>
 <div class="modal-footer">
  <button class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
</div>
</div>
</div>
<!-- View -->
<div class="modal fade text-left" id="modal_view" data-backdrop="static" tabindex="-1" role="dialog"
aria-labelledby="myModalLabel1" aria-hidden="true">
<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="title_view"></h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
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
            <label id="">Keterangan</label>
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
   <!--      <div class="col-lg-4">
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
            <span id="nama_pemberi_view" class="view"></span>
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
            <span id="jabatan_pemberi_view" class="view"></span>
          </div>
        </div> -->
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
        Close
      </button>
    </div>
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

  #ext_table td:nth-child(9) {
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
<!-- Js Create -->
<script type="text/javascript">
  // $(".select_opsi").select2({
  //   allowClear: true,
  //   placeholder: ':. FILTER OPTION .:'
  // });
  $('#date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $("#id_position_routing_chief").select2();
  var urlAjax = "";
  function resetForm() {
    $("#extForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#extForm input").removeClass("is-invalid");
    $("#extForm select").removeClass("custom-select");
  }
  $(".new").click(function() {
    resetForm();
    $("#modal_form_ext").modal('show');
    $("#date").parent().children('span').children('button').attr('disabled',false);
    $("#date").prop('disabled',false);
    sessionStorage.clear();
    $(".select_opsi").select2({
      allowClear: true,
      placeholder: ':. FILTER OPTION .:'
    });
    hide_loading();
    urlAjax = "{{route('save.ext')}}";    
  });
  function get_edit(letterID) {
    if (letterID) {
      $.getJSON("{{ url('e-letter/company_letter/external_letter/get_edit') }}"+"/"+letterID, function(response) {
        if (response.data) {
          $("#modal_form_ext").modal('show');
          $.each(response.data, function(key, value) {
            $("#id_letter").val(value.id_letter);
            $("#id_category").val(value.id_category).trigger('change');
            $("#id_branch").val(value.id_branch).trigger('change');
            $("#id_category").select2();
            $("#id_branch").select2({
             allowClear: true,
             placeholder: ':. FILTER OPTION .:'
           });
            document.getElementById('id_category').setAttribute('readonly','readonly');
            $("#date").parent().children('span').children('button').attr('disabled',true);
            $("#date").prop('disabled',true);
            $("#date").val(value.date);
            // $("#remark_1").val(value.remark_1);
            $("#email").val(value.email);
            $("#notes").val(value.notes);
            // $("#id_employee_chief").val(value.id_employee_chief).trigger('change');
            // sessionStorage.setItem("selectedOption", value.remark_1);
          });
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        get_edit(letterID);
      });
    }else{
      $("#modal_form_ext").modal('hide');
    }
  }
  $(document).on('click','.btn-edit',function() {
    var letterID = $(this).attr('more_id');
    resetForm();
    urlAjax = "{{route('edit.ext')}}";
    sessionStorage.clear();
    if (letterID) {
      get_edit(letterID);
    }
  });
  // function change_region(regionID) {
  //   if (regionID) {
  //     show_loading();
  //     $.ajax({
  //       type: "GET",
  //       url: "{{url('e-letter/company_letter/external_letter/get_branch')}}",
  //       data : {id_url:global_url_server,region_id:regionID},
  //       success: function(response) {
  //         if (response) {
  //           hide_loading();
  //           $("#id_branch").empty();
  //           $.each(response, function(key, value) {
  //             $("#id_branch").append('<option value="' + value.id_branch + '">'+ value.description +'</option>');
  //           });
  //           $("#id_branch").val(sessionStorage.getItem('id_branch')).trigger('change');
  //         }else{
  //           $("#id_branch").empty();
  //         }
  //       },
  //       error: function(response) {
  //         change_region(regionID);
  //       }
  //     });
  //   }else{
  //     $("#id_branch").empty();
  //   }
  // }
  // $(document).on('change','#id_region',function() {
  //   var regionID = $(this).val();
  //   $("#id_branch").empty();
  //   if (regionID) {
  //     change_region(regionID);
  //   }
  // });
  // function change_chief(employeeID) {
  //   if (employeeID) {
  //     show_loading();
  //     $.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
  //       if (response) {
  //         hide_loading();
  //         $("#id_position_routing_chief").empty();
  //         $("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
  //       } else {
  //         $("#id_position_routing_chief").empty();
  //       }
  //     }).fail(function(jqXHR, textStatus, errorThrown) {
  //       change_chief(employeeID);
  //     });
  //   } else {
  //     $("#id_position_routing_chief").empty();
  //   }
  // }
  // $(document).on('change','#id_employee_chief',function() {
  //   var employeeID = $(this).val();
  //   $("#id_position_routing_chief").empty();
  //   if (employeeID) {
  //     change_chief(employeeID);
  //   }
  // });
  $(function () {
    $('#extForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#extForm input").removeClass("is-invalid");
      $("#extForm select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url : urlAjax,
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#extForm")[0].reset();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_ext').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#ext_table').DataTable().ajax.reload();
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
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(letterID) {
    $.getJSON("{{ url('e-letter/company_letter/external_letter/get_edit') }}"+"/"+letterID, function(response) {
      if (response.data) {
        $.each(response.data, function(key, value) {
          $(".view").html('');
          $("#title_view").html(value.reference_number);
          $("#letter_date_view").html(TanggalIndonesia(value.date));
          $("#category_view").html(value.category);
          $("#email_view").html(value.email);
          $("#notes_view").html(value.notes);
          $("#branch_view").html(value.dec_branch);
          // $("#nama_pemberi_view").html(value.nama_pemberi);
          // $("#jabatan_pemberi_view").html(value.jabatan_pemberi);
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_view(letterID);
    });
  }
  $(document).on('click','.btn-view',function() {
    var letterID = $(this).attr('more_id');
    $("#modal_view").modal('show');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    $("#title_view").html('');
    if (letterID) {
      get_view(letterID);
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
          url: "{{url('e-letter/company_letter/external_letter/delete')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#ext_table').DataTable().ajax.reload();         
            }, 50);
          }
        })
      }
    });
  });
</script>
<!-- index -->
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
  function get_data() {
    $.getJSON("{{ url('e-letter/company_letter/external_letter/get_data') }}",{id_url:global_url_server}, function(response) {
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '" data-attribute="'+value_category.code+'">'+ value_category.description +'</option>');
      });
      $.each(response.branch, function(key, value_branch) {
        $("#id_branch").append('<option value="' + value_branch.id_branch + '">'+ value_branch.description +'</option>');
      });
      // $.each(response.employee, function(key, value_employee) {
      //   $("#id_employee_chief").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
      // });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  $(document).ready(function() {
    get_data();
  });
  $(function () {
    $('#ext_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('ext.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#ext_table').DataTable().ajax.reload();
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
      // { data: 'dec_dept', name: 'dec_dept' },
      // { data: 'dec_region', name: 'dec_region' },
      { data: 'dec_branch', name: 'dec_branch' },
      { data: 'notes', name: 'notes' },
      { data: 'action', name: 'action', orderable: false, className: 'space'
    }
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