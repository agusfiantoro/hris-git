@extends('adminlte::page')
@section('title', 'Employement Termination Decree (SK PHK)')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Employement Termination Decree (SK PHK)
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add SK PHK</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="skp_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Name</th>
            <th>NIK</th>
            <th data-priority="5">Position</th>
            <th>Branch</th>
            <th>Region</th>
            <th>Department</th>
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
@include('eletter/termination_letter/create')
@include('eletter/termination_letter/update')
@include('eletter/termination_letter/view')
@include('eletter/termination_letter/career/career')

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
  #skp_table td:nth-child(13) {
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
@include('eletter/termination_letter/js/js-create')
@include('eletter/termination_letter/js/js-update')
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
    $.getJSON("{{ url('e-letter/decree/termination_letter/get_data') }}", function(response) {
      // $.each(response.chief, function(key, value_chief) {
      //   $("#id_employee_chief").append('<option value="' + value_chief.id_employee + '">'+ value_chief.name +'</option>');   
      //   $("#id_employee_chief_edit").append('<option value="' + value_chief.id_employee + '">'+ value_chief.name +'</option>');  
      // });
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
        $("#id_category_edit").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
     get_data();
   });
  }
  $(document).ready(function() {
    get_data();
  });
  $(function () {
    $('#skp_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('skp.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#skp_table').DataTable().ajax.reload();
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
      { data: 'name', name: 'name' },
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'dec_position', name: 'dec_position' },
      { data: 'dec_branch', name: 'dec_branch' },
      { data: 'dec_region', name: 'dec_region' },
      { data: 'dec_dept', name: 'dec_dept' },
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
      // if(access_print == 0){
      //   $(row).find('.btn-print').css('display', 'none');
      // } 
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
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(letterID) {
    $.getJSON("{{ url('e-letter/decree/termination_letter/get_edit') }}"+"/"+letterID, function(response) {
      if (response) {
        $.each(response, function(key, value) {
          $(".view").html('');
          $("#title_view").html(value.reference_number);
          $("#letter_date_view").html(TanggalIndonesia(value.date));
          $("#name_view").html(value.name);
          $("#nik_view").html(value.nik_employee);
          $("#category_view").html(value.category);
          $("#company_view").html(value.company_name);
          $("#department_view").html(value.dec_dept);
          $("#region_view").html(value.dec_region);
          $("#branch_view").html(value.dec_branch);
          $("#position_view").html(value.dec_position);
          $("#email_view").html(value.email);
          $("#notes_view").html(value.notes);
          $("#name_chief_view").html(value.name_chief);
          $("#position_chief_view").html(value.position_chief);
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_view(letterID);
      // swal({
      //   icon: 'error',
      //   title: 'Oops...',
      //   dangerMode: true,
      //   text: 'Something went wrong! [Unknown Error]'
      // });
      // $("#modal_view").modal('hide');
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
          url: "{{url('e-letter/decree/termination_letter/destroy')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              // $('#confirmModal').modal('hide');
              $('#skp_table').DataTable().ajax.reload();         
            }, 50);
          }
        })
      }
    });
  });
</script>
@endsection