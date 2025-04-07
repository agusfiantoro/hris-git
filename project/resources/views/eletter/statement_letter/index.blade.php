@extends('adminlte::page')
@section('title', 'Employee Movement Decree (SK)')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Employee Movement Decree (SK)
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add SK</button>
        </div>
      </div>
      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="sk_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="5">Name</th>
            <th>NIK</th>
            <th data-priority="4">Category</th>
            <th>Effective Date</th>
            <th>Position</th>
            <th data-priority="6">Department</th>
            <th>Region</th>
            <th>Location</th>
            <th>Area</th>
            <th>Email</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@include('eletter/statement_letter/create')
@include('eletter/statement_letter/update')
@include('eletter/statement_letter/career/career')
@include('eletter/statement_letter/view')

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
  #sk_table td:nth-child(16) {
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
@include('eletter/statement_letter/js/js-create')
@include('eletter/statement_letter/js/js-update')
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
    $('#sk_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('sk.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#sk_table').DataTable().ajax.reload();
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
      { data: 'category', name: 'category' },
      { data: 'effective_date', name: 'effective_date' },
      { data: 'dec_position_old', name: 'dec_position_old' },
      { data: 'dec_dept_old', name: 'dec_dept_old' },
      { data: 'dec_region_old', name: 'dec_region_old' },
      { data: 'dec_location_old', name: 'dec_location_old' },
      { data: 'remark_8', name: 'remark_8' },
      { data: 'email', name: 'email' },
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
      if(access_print == 0){
        $(row).find('.btn-print').css('display', 'none');
      } 
    },
  });
  });
  function get_data() {
    $.getJSON("{{ url('e-letter/decree/statement_letter/get_data') }}", function(response) {
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '" data-attribute="'+value_category.description+'">'+ value_category.description +'</option>');
      });
      $.each(response.location, function(key, value_location) {
        $("#remark_1").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
        $("#remark_1_edit").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  function get_chief() {
    $.getJSON("{{ url('e-letter/get-chief/employee') }}", function(response) {
      $.each(response, function(key, value) {
        $("#id_employee_chief").append('<option value="' + value.id_employee + '">'+ value.name +'</option>');
        $("#id_employee_chief_edit").append('<option value="' + value.id_employee + '">'+ value.name +'</option>');
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
     get_chief();
   });
  }
  $(document).ready(function() {
    get_data();
    get_chief();
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
          url: "{{url('e-letter/decree/statement_letter/delete')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              // $('#confirmModal').modal('hide');
              $('#sk_table').DataTable().ajax.reload();         
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
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMMM YYYY');
    return formattedDate;
  }
  function get_view(letterID) {
    if (letterID) {
      $.getJSON("{{ url('e-letter/decree/statement_letter/get_edit') }}"+"/"+letterID, function(response) {
        if (response.data) {
          $(".view").html('');
          $("#title_view").html(response.data.reference_number);
          $("#letter_date_view").html(TanggalIndonesia(response.data.date));
          $("#email_view").html(response.data.email);
          $("#notes_view").html(response.data.notes);
          $("#job_grade_view").html(response.data.dec_job_grade);
          $("#name_view").html(response.data.name);
          $("#nik_view").html(response.data.nik_employee);
          $("#category_view").html(response.data.category);
          $("#old_company_view").html(response.data.company_name);
          $("#new_company_view").html(response.data.company_name);
          $("#old_position_view").html(response.data.dec_position_old);
          $("#new_position_view").html(response.data.dec_position_new);
          $("#old_division_view").html(response.data.id_principal);
          $.each(response.principal, function(key, value_principal) {
            var decDivisionNew = value_principal.dec_division_new !== null ? value_principal.dec_division_new : '-';
            $("#new_division_view").html(decDivisionNew+' ');
          });
          $("#old_department_view").html(response.data.dec_dept_old);
          $("#new_department_view").html(response.data.dec_dept_new);
          $("#old_region_view").html(response.data.dec_region_old);
          $("#new_region_view").html(response.data.dec_region_new);
          $("#old_branch_view").html(response.data.dec_branch_old);
          $("#new_branch_view").html(response.data.dec_branch_new);
          $("#old_location_view").html(response.data.dec_location_old);
          $("#new_location_view").html(response.data.dec_location_new);
          $("#old_area_view").html(response.data.remark_8);
          $("#new_area_view").html(response.data.remark_9);
          $("#name_chief_view").html(response.data.name_chief);
          $("#position_chief_view").html(response.data.position_chief);

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
  $(document).on('click','.btn-view',function() {
    var letterID = $(this).attr('more_id');
    $("#modal_view").modal('show');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    $("#title_view").html('');
    if (letterID) {
      get_view(letterID);
    }
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
</script>
@endsection