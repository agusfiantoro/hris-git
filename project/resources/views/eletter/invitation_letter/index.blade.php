@extends('adminlte::page')
@section('title', 'Summon Letter (SUPA)')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Summon Letter (SUPA)
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add SUPA</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="supa_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Name</th>
            <th>NIK</th>
            <th data-priority="5">Category</th>
            <th data-priority="8">Department</th>
            <th>Region</th>
            <th>Position</th>
            <th data-priority="6">Effective Date</th>
            <th data-priority="7">End Date</th>
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

@include('eletter/invitation_letter/create')
@include('eletter/invitation_letter/update')
@include('eletter/invitation_letter/view')
@include('eletter/invitation_letter/view_supa/supa2')

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
  #supa_table td:nth-child(15) {
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
@include('eletter/invitation_letter/js/js-create')
@include('eletter/invitation_letter/js/js-update')
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
    $.getJSON("{{ url('e-letter/company_letter/invitation_letter/get_data') }}", function(response) {
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '" data-attribute="'+value_category.code+'">'+ value_category.description +'</option>');
      });
      $.each(response.employee, function(key, value_employee) {
        $("#id_employee_chief").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
        $("#id_employee_chief_edit").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
      });
      $.each(response.location, function(key, value_location) {
        $("#remark_8").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
        $("#remark_8_edit").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  $(document).ready(function() {
    get_data();
  });
  $(function () {
    $('#supa_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('supa.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#supa_table').DataTable().ajax.reload();
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
      { data: 'dec_dept', name: 'dec_dept' },
      { data: 'dec_region', name: 'dec_region' },
      { data: 'dec_position', name: 'dec_position' },
      { data: 'effective_date', name: 'effective_date' },
      { data: 'expired_date', name: 'expired_date' },
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
          url: "{{url('e-letter/invitation_letter/delete')}}"+"/"+id_letter,
          success:function(data)
          {
            if (data.status == 'warning') {
              setTimeout(function(){
                swal({
                  title: "Data not Deleted!",
                  icon: "warning",
                  text: data.message
                });
              }, 50);
            }else if(data.status == 'success'){
             setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#confirmModal').modal('hide');
              $('#supa_table').DataTable().ajax.reload();         
              $('#supa2_table').DataTable().ajax.reload();         
            }, 50);
           }
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
  function get_view(letterID) {
    $.getJSON("{{ url('e-letter/invitation_letter/get_edit') }}"+"/"+letterID, function(response) {
      if (response.data) {
        $(".view").html('');
        $("#title_view").html(response.data.reference_number);
        $("#letter_date_view").html(TanggalIndonesia(response.data.date));
        $("#name_view").html(response.data.name);
        $("#nik_view_detail").html(response.data.nik_employee);
        $("#category_view").html(response.data.category_code);
        $("#company_view").html(response.data.company_name);
        $("#position_view").html(response.data.dec_position);
        $("#department_view").html(response.data.dec_dept);
        $("#regional_view").html(response.data.dec_region);
        $("#branch_view").html(response.data.dec_branch);
        $("#effective_view").html(TanggalIndonesia(response.data.effective_date));
        $("#end_view").html(TanggalIndonesia(response.data.expired_date));
        $("#alamat_view").html(response.data.remark_3);
        $("#kota_view").html(response.data.remark_6);
        $("#nama_atasan_view").html(response.data.remark_1);
        $("#jabatan_atasan_view").html(response.data.remark_2);
        $("#hari_view").html(response.hari);
        $("#tanggal_panggil_view").html(response.tanggal_panggil);
        $("#waktu_view").html(response.waktu+' '+response.zona);
        $("#tempat_view").html(response.data.remark_5);
        $("#no_supa1_view").html(response.no_supa_1);
        $("#email_view").html(response.data.email);
        $("#nama_pengirim_view").html(response.data.nama_pengirim);
        $("#jabatan_pengirim_view").html(response.data.jabatan_pengirim);
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