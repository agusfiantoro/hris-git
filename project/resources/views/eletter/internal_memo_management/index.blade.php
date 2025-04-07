@extends('adminlte::page')
@section('title', 'Circular Letter (SED)')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- <img src="{{ asset('vendor/adminlte/dist/img/icon.ico') }}"> -->
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Circular Letter (SED)</h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add SED</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="imm_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <!-- <th>Company</th> -->
            <th>Department</th>
            <th data-priority="3">Regional</th>
            <th data-priority="4">Branch</th>
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
@include('eletter/internal_memo_management/create')
@include('eletter/internal_memo_management/update')
@include('eletter/internal_memo_management/view')

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
  #imm_table td:nth-child(10) {
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
@include('eletter/internal_memo_management/js/create-js')
@include('eletter/internal_memo_management/js/update-js')
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
    $('#imm_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('imm.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#imm_table').DataTable().ajax.reload();
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
      // { data: 'company_name', name: 'company_name' },
      { data: 'dec_dept', name: 'dec_dept' },
      { data: 'dec_region', name: 'dec_region' },
      { data: 'dec_branch', name: 'dec_branch' },
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
  function get_data() {
    $.getJSON("{{url('e-letter/internal_memo_management/get_data')}}", function(response) {
      $.each(response.region, function(key, value_region) {
        $("#id_region").append('<option value="' + value_region.id_region + '" data-attribute="region_new">'+ value_region.description +'</option>');
      });
      $.each(response.dept, function(key, value_dept) {
        $("#id_dept").append('<option value="' + value_dept.id_dept + '">'+ value_dept.description +'</option>');
        $("#id_dept_edit").append('<option value="' + value_dept.id_dept + '">'+ value_dept.description +'</option>');
      });
      // $.each(response.employee, function(key, value_employee) {
      //   $("#id_employee_chief").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
      //   $("#id_employee_chief_edit").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
      // });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  $(document).ready(function() {
    get_data();
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
          url: "{{url('e-letter/internal_memo/internal_memo_management/delete')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#confirmModal').modal('hide');
              $('#imm_table').DataTable().ajax.reload();         
            }, 50);
          }
        })
      }
    });
  });

  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(id_letter) {
    $.ajax({
      type: "GET",
      url: "{{url('e-letter/internal_memo/internal_memo_management/get_view')}}"+"/"+id_letter,
      success: function(response) {
        if (response) {
          $(".view").html('');
          $.each(response, function(key, value) {
            $("#title_view").html(value.reference_number);
            $("#reference_number_view").html(value.reference_number);
            $("#date_view").html(TanggalIndonesia(value.date));
            $("#company_view").html(value.company_name);
            $("#department_view").html(value.dec_dept);
            $("#regional_view").html(value.dec_region);
            $("#branch_view").html(value.dec_branch);
            $("#notes_view").html(value.notes);
            $("#email_view").html(value.email);
            // $("#employee_chief_view").html(value.name);
            // $("#employee_routing_chief_view").html(value.dec_position);
          });
        }
      },
      error: function(response) {
        get_view(id_letter);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // $("#modal_view").modal('hide');
      }
    });
  }
  $(document).on('click', '.btn-view', function() {
    var id_letter = $(this).attr('more_id');
    $("#modal_view").modal('show');
    $("#title_view").html('');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    if (id_letter) {
      get_view(id_letter);
    }
  });
</script>
@endsection