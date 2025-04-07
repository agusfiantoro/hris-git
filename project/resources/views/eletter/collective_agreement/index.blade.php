@extends('adminlte::page')
@section('title', 'Collective Agreement (PB)')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Collective Agreement (PB)
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add PB</button>
        </div>
      </div>
      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="pb_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th data-priority="7">No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Name</th>
            <th>NIK</th>
            <th>Category</th>
            <th data-priority="5">Position</th>
            <th>Department</th>
            <th data-priority="6">Branch</th>
            <th>Region</th>
            <th>Notes</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@include('eletter/collective_agreement/create')
@include('eletter/collective_agreement/update')
@include('eletter/collective_agreement/view')
@include('eletter/collective_agreement/employee/index')

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

  #pb_table td:nth-child(14) {
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
@include('eletter/collective_agreement/js/js-create')
@include('eletter/collective_agreement/js/js-update')
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
    $('#pb_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('pb.index') }}",
        data: {id_url: global_url_server},      
        error: function (jqXHR, textStatus, errorThrown) {
          $('#pb_table').DataTable().ajax.reload();
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
      { 
        data: 'name', 
        name: 'name', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }  
      },
      { 
        data: 'nik_employee', 
        name: 'nik_employee', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }  
      },
      { data: 'category', name: 'category' },
      { data: 'dec_position', name: 'dec_position' },
      { data: 'dec_dept', name: 'dec_dept' },
      { data: 'dec_branch', name: 'dec_branch' },
      { data: 'dec_region', name: 'dec_region' },
      { data: 'notes', name: 'notes' },
      { data: 'action', name: 'action', orderable: false, className: 'space' 
      // render: function ( data, type, row ) {
      //   if(access_create == 0){
      //     $('.new').css('display', 'none');
      //   }   
      //   if(access_edit == 0){
      //     $('.edit').css('display', 'none');
      //   }       
      //   if(access_delete == 0){
      //     $('.delete').css('display', 'none');
      //   }
      //   if(access_print == 0){
      //     $('.print').css('display', 'none');
      //   }                                       
      //   return data;
      // } 
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
    }
  });
  });
  function get_data() {
   $.getJSON("{{ url('e-letter/employee_agreement/collective_agreement/get_data') }}", function(response) {
    $.each(response.category, function(key, value_category) {
      $("#id_category").append('<option value="' + value_category.id_general_data + '">'+value_category.description+'</option>');
      $("#id_category_edit").append('<option value="' + value_category.id_general_data + '">'+value_category.description+'</option>');
    });
  }).fail(function(jqXHR, textStatus, errorThrown) {
    get_data();
  });
}
// function get_chief() {
//   $.getJSON("{{ url('e-letter/get-chief/employee') }}", function(response) {
//     $.each(response, function(key, value) {
//       $("#id_employee_chief").append('<option value="' + value.id_employee + '">'+ value.name +'</option>');
//       $("#id_employee_chief_edit").append('<option value="' + value.id_employee + '">'+ value.name +'</option>');
//     });
//   }).fail(function(jqXHR, textStatus, errorThrown) {
//     get_chief();
//   });
// }
$(document).ready(function() {
  get_data();
  // get_chief();
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
    $.getJSON("{{ url('e-letter/employee_agreement/collective_agreement/get_edit') }}"+"/"+letterID, function(response) {
      if (response.data) {
        $(".view").html('');
        $("#title_view").html(response.data.reference_number);
        $("#letter_date_view").html(TanggalIndonesia(response.data.date));
        $("#category_view").html(response.data.category);
        $("#name_view").html(response.data.name);
        $("#nik_view").html(response.data.nik_employee);
        $("#company_view").html(response.data.company_name);
        $("#position_view").html(response.data.dec_position);
        $("#department_view").html(response.data.dec_dept);
        $("#regional_view").html(response.data.dec_region);
        $("#branch_view").html(response.data.dec_branch);
        $("#email_view").html(response.data.email);
        $("#notes_view").html(response.data.notes);
        // $("#nama_pengirim_view").html(response.data.nama_pengirim);
        // $("#jabatan_pengirim_view").html(response.data.jabatan_pengirim);
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      // swal({
      //   icon: 'error',
      //   title: 'Oops...',
      //   dangerMode: true,
      //   text: 'Something went wrong! [Unknown Error]'
      // });
      // $("#modal_view").modal('hide');
      get_view(letterID);
    });
  }
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
        url: "{{url('e-letter/collective_agreement/delete')}}"+"/"+id_letter,
        success:function(data)
        {
          setTimeout(function(){
            swal({
              title: "Data Deleted!",
              icon: "success"
            });
            $('#confirmModal').modal('hide');
            $('#pb_table').DataTable().ajax.reload();         
          }, 50);
        }
      })
    }
  });
});
</script>
@endsection