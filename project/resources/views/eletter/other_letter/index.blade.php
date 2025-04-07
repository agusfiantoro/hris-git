@extends('adminlte::page')
@section('title', 'Other Letter')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Other Letter</h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Other Letter</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="other_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th data-priority="7">No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th>NIK</th>
            <th data-priority="4">Name</th>
			<th>Position</th>
            <th data-priority="6" width="200">Notes</th>
            <th data-priority="5">Category</th>
            <th>Effective Date</th>
            <th>End Date</th>
            <th data-priority="7">Department</th>
            <th>Region</th>          
            <th>Division</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@include('eletter/other_letter/create')
@include('eletter/other_letter/update')
@include('eletter/other_letter/view')

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
  #other_table td:nth-child(16) {
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
@include('eletter/other_letter/js/js-create')
@include('eletter/other_letter/js/js-update')
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
    $.getJSON("{{ url('e-letter/company_letter/other_letter/get_data') }}", function(response) {
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
        $("#id_category_edit").append('<option value="' + value_category.id_general_data + '">'+ value_category.description +'</option>');
      });
      $.each(response.employee_chief, function(key, value_chief) {
        $("#id_employee_chief").append('<option value="' + value_chief.id_employee + '">'+ value_chief.name +'</option>');
        $("#id_employee_chief_edit").append('<option value="' + value_chief.id_employee + '">'+ value_chief.name +'</option>');
      });
      $.each(response.region, function(key, value_region) {
        $("#id_region").append('<option value="' + value_region.id_region + '">'+ value_region.description +'</option>');
      });
      // $.each(response.department, function(key, value_dept) {
      //   $("#id_dept").append('<option value="' + value_dept.id_dept + '">'+ value_dept.description +'</option>');
      // });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_data();
    });
  }
  function get_dept() {
    $.getJSON("{{ url('e-letter/company_letter/other_letter/get_data') }}", function(response) {
      $.each(response.department, function(key, value_dept) {
        $("#id_dept").append('<option value="' + value_dept.id_dept + '">'+ value_dept.description +'</option>');
      });
      $("#id_dept").val(null).trigger('change');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_dept();
    });
  }
  $(document).ready(function() {
    get_data();
    get_dept();
  });
  $(function () {
    $('#other_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('other.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#other_table').DataTable().ajax.reload();
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
        data: 'dec_position', 
        name: 'dec_position', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }
      },
      { 
        data: 'notes', 
        name: 'notes', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }
      },
      { data: 'category', name: 'category' },
      { 
        data: 'effective_date', 
        name: 'effective_date', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }
      },
      { 
        data: 'expired_date', 
        name: 'expired_date', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }
      },
      { data: 'dec_dept', name: 'dec_dept' },
      { data: 'dec_region', name: 'dec_region' },
	  { 
        data: 'id_principal', 
        name: 'id_principal', 
        render: function (data, type, row) {
          if (data === null) {
            return '-';
          } else {
            return data;
          }
        }
      },     
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
      url: "{{url('e-letter/company_letter/other_letter/get_edit')}}"+"/"+id_letter,
      success: function(response) {
        $(".view").html('');
        if (response) {
          $.each(response, function(key, value) {
            $("#title_view").html(value.reference_number);
            $("#name_view").html(value.name);
            $("#nik_view").html(value.nik_employee);
            $("#email_view").html(value.email);
            $("#category_view").html(value.category);
            if (value.date != null) {
              $("#date_view").html(TanggalIndonesia(value.date));
            }
            $("#reference_number_view").html(value.reference_number);
            $("#department_view").html(value.dec_dept);
            $("#regional_view").html(value.dec_region);
            $("#branch_view").html(value.dec_branch);
            $("#position_view").html(value.dec_position);
            $("#division_view").html(value.id_principal);
            $("#job_grade_view").html(value.dec_job_grade);
            $("#status_view").html(value.status);
            if (value.effective_date != null) {
              $("#effective_view").html(TanggalIndonesia(value.effective_date));
            }
            if (value.expired_date != null) {
              $("#end_view").html(TanggalIndonesia(value.expired_date));
            }
            $("#employee_chief_view").html(value.nama_menyetujui);
            $("#employee_routing_chief_view").html(value.jabatan_menyetujui);
            $("#mengetahui_view").html(value.remark_1);
            $("#jabatan_mengetahui_view").html(value.remark_2);
            $("#notes_view").html(value.notes);
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
  $(document).on('click','.btn-view',function() {
    $("#modal_view").modal('show');
    var id_letter = $(this).attr('more_id');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    $("#title_view").html('');
    if (id_letter) {
      get_view(id_letter);
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
          url: "{{url('e-letter/company_letter/other_letter/destroy')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#other_table').DataTable().ajax.reload();         
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

