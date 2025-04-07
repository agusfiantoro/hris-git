@extends('adminlte::page')
@section('title', 'Daily Employment Contract (PKH)')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Daily Employment Contract (PKH)</h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add PKH</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="freelance_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th>Name</th>
            <th>Category</th>
            <th data-priority="6">Position</th>
            <th data-priority="7">Branch</th>
            <th>Department</th>
            <th>Regional</th>
            <th data-priority="4">Start Date</th>
            <th data-priority="5">End Date</th>
            <th>Keterangan</th>
            <th>Created By</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>

@include('eletter/freelance_work_agreement/create')
@include('eletter/freelance_work_agreement/update')
@include('eletter/freelance_work_agreement/view')

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

  #freelance_table td:nth-child(16) {
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
@include('eletter/freelance_work_agreement/js/js-create')
@include('eletter/freelance_work_agreement/js/js-update')
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
    $.getJSON("{{url('e-letter/employee_agreement/freelance_work_agreement/get_data_new')}}", function(response) {
      $(".select_search").empty();
      $(".select_edit").empty();
      if (response.position) {
        $.each(response.position, function(key, value_position) {
          $("#id_position_detail").append('<option value="' + value_position.id_routing + '">' + value_position.description +'</option>');
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
      // swal({
      //   icon: 'error',
      //   title: 'Oops...',
      //   dangerMode: true,
      //   text: 'Something went wrong! [Unknown Error]'
      // });
      // $("#modal_view").modal('hide');
    });
  }
  $(document).ready(function() {
    get_data();
  });

  $(function () {
    $('#freelance_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('pkhl.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#freelance_table').DataTable().ajax.reload();
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
        { data: 'remark_1', name: 'remark_1' },
        { data: 'dec_category', name: 'dec_category' },
        { data: 'dc_position', name: 'dc_position' },
        { data: 'dec_branch', name: 'dec_branch' },
        { data: 'dc_dept', name: 'dc_dept' },
        { data: 'dc_region', name: 'dc_region' },
        { data: 'effective_date', name: 'effective_date' },
        { data: 'expired_date', name: 'expired_date' },
        { data: 'notes', name: 'notes' },
        { data: 'company_cd', name: 'company_cd' },
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
        url: "{{url('e-letter/freelance_work_agreement/delete')}}/"+id_letter,
        success:function(data)
        {
          setTimeout(function(){
            swal({
              title: "Data Deleted!",
              icon: "success"
            });
            $('#confirmModal').modal('hide');
            $('#freelance_table').DataTable().ajax.reload();         
          }, 50);
        }
      })
     }
   });
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
      $.getJSON("{{url('e-letter/freelance_work_agreement/get_edit')}}"+"/"+letterID, function(response) {
        if (response) {
          $(".view").html('');
          $.each(response, function(key, value) {
            $("#title_view").html(value.reference_number);
            $("#letter_date_view").html(formatDateToIndonesian(value.date));
            $("#category_view").html(value.category);
            $("#effective_view").html(formatDateToIndonesian(value.effective_date));
            if (value.expired_date != null) {
              $("#expired_view").html(formatDateToIndonesian(value.expired_date));
            }
            $("#name_view").html(value.remark_1);
            $("#company_view").html(value.company_name);
            $("#position_view").html(value.dec_position);
            $("#department_view").html(value.dec_dept);
            $("#grade_view").html(value.dec_job_grade);
            $("#regional_view").html(value.dec_region);
            $("#branch_view").html(value.dec_branch);
            $("#location_view").html(value.dec_location);
            $("#email_view").html(value.email);
            $("#notes_view").html(value.notes);
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
  $(document).on('click','.btn-view',function() {
    var letterID = $(this).attr('more_id');
    $("#modal_view").modal('show');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    $("#title_view").html('');
    if (letterID) {
      get_view(letterID);
    }
  });
</script>
@endsection