@extends('adminlte::page')
@section('title', 'Certificate of Employee (SKK)')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Certificate of Employee (SKK)
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add SKK</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="skk_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Letter Date</th>
            <th data-priority="4">Category</th>
            <th data-priority="7">Name</th>
            <th data-priority="5" class="text text-center">Publish</th>
            <th data-priority="6" class="text text-center">Receipt Attachment</th>
            <th data-priority="8">Publish Date</th>
            <th>Department</th>
            <th>Division</th>
            <th>Region</th>
            <th data-priority="10">Position</th>
            <th data-priority="8">Join Date</th>
            <th data-priority="9">End Date</th>
            <th>Keperluan</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@include('eletter/employment_certificate/create')
@include('eletter/employment_certificate/view')
@include('eletter/employment_certificate/publish')
@include('eletter/employment_certificate/employee/index')

@section('css')
<style type="text/css">
/*.modal-body {
  position: relative;
}
.modal-loading {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid black;
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 0.7s linear infinite;
  display: none;
}
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
  }*/
  .modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 50%;
    left: 50%;
    /*background-color: rgba(255, 255, 255, 0.5);*/
    z-index: 9999;
    visibility: hidden;
  }
  .modal-body {
    position: relative;
  }
  .modal.show .modal-loading {
    visibility: visible;
  }

  #skk_table td:nth-child(18) {
    text-align: center;
  }
  #skk_table td:nth-child(8) {
    text-align: center;
  }
  select[hidden].select2-hidden-accessible + .select2-container {
    display: none;
  }
  select[hidden].select2-hidden-accessible + .select2-container .select2-selection {
    display: none;
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
@include('eletter/employment_certificate/js/js-create')
<script type="text/javascript">
  $('#remark_1').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
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
  let global_id_region = [];
  function get_data() {
    $.getJSON("{{ url('e-letter/decree/employment_certificate/get_data') }}", function(response) {
      $.each(response.category, function(key, value_category) {
        $("#id_category").append('<option value="' + value_category.id_general_data + '" more_code="'+value_category.code+'">'+ value_category.description +'</option>');
      });
      $.each(response.location, function(key, value_location) {
        $("#remark_2").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
        $("#remark_2_edit").append('<option value="' + value_location.description + '">'+ value_location.description +'</option>');
      });
      $("#id_region").select2({
        data: response.region
      });
    }).fail(function(jqXHR, textStatus, errorThrown) {
     get_data();
   });
  }
  function get_chief() {
    $.getJSON("{{ url('e-letter/get-chief/employee') }}", function(response) {
      $.each(response, function(key, value_employee) {
       $("#id_employee_chief").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
       $("#id_employee_chief_edit").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +'</option>');
     });
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_chief();
    });
  }
  $(document).ready(function() {
    get_data();
    get_chief();
  });

  $(function () {
    $('#skk_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('skk.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#skk_table').DataTable().ajax.reload();
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
      // NAMA / COALESCE JIKA NAMA INPUT CUSTOM
      {
        data: 'name',
        name: 'name',
        render: function (data, type, row) {
          var dataArray = data.split(';');
          if (dataArray.length > 1) {
            return dataArray[0];
          }else{
            return data;
          }
        }
      },
      // ENd NAMA
      { 
        data: 'publish', 
        name: 'publish', 
        render: function (data, type, row) {
          if (data == true) {
            return '<span class="badge bg-success text-white justify-content-center">Yes</span>';
          }else{
            return '<span class="badge bg-danger text-white justify-content-center">No</span>';
          }
        }  
      },
      {
        data: 'remark_4',
        name: 'remark_4',
        render: function (data, type, row) {
          if (data == null) {
            return '';
          }else{
            return '<a style="font-size:13px;font-weight:bold;" href="{{asset("project/storage/app/public/upload/skk")}}/'+data+'" download="">Download File</a>'
          }
        }
      },
      { data: 'remark_1', name: 'remark_1' },
      {
        data: 'dec_dept',
        name: 'dec_dept',
        render: function (data, type, row) {
          var dataArray = data.split(';');
          if (dataArray.length > 1) {
            return dataArray[3];
          }else{
            return data;
          }
        }
      },
      { data: 'id_principal', name: 'id_principal' },
      { data: 'dec_region', name: 'dec_region' },
      {
        data: 'dec_position',
        name: 'dec_position',
        render: function (data, type, row) {
          var dataArray = data.split(';');
          if (dataArray.length > 3) {
            return dataArray[2];
          }else{
            return data;
          }
        }
      },
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
        if(access_print == 0){
          $(row).find('.btn-print').css('display', 'none');
        } 
      },
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
          url: "{{url('e-letter/decree/employment_certificate/delete')}}"+"/"+id_letter,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#confirmModal').modal('hide');
              $('#skk_table').DataTable().ajax.reload();         
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
  function get_view(letterID) {
    if (letterID) {
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/decree/employment_certificate/get_edit')}}"+"/"+letterID,
        success: function(response) {
          if (response.data) {
            $(".view").html('');
            $.each(response.data, function(key, value) {
              if (value.remark_3 == null) {
                var name = value.name;
                var dec_dept = value.dec_dept;
                var dec_branch = value.dec_branch;
                var dec_position = value.dec_position;
                var id_principal = value.id_principal;
                var dec_job_grade = value.dec_job_grade;
                var status = value.status;
              }else{
                var remark_3 = value.remark_3.split(';');
                var name = remark_3[0];
                var dec_branch = remark_3[1];
                var dec_position = remark_3[2];
                var dec_dept = remark_3[3];
                var dec_job_grade = remark_3[4];
                var status = remark_3[5];
              }
              $("#title_view").html(value.reference_number);
              $("#name_view").html(name);
              $("#email_view").html(value.email);
              $("#category_view").html(value.dec_category);
              $("#date_view").html(TanggalIndonesia(value.date));
              $("#reference_number_view").html(value.reference_number);
              $("#department_view").html(dec_dept);
              $("#regional_view").html(value.dec_region);
              $("#branch_view").html(dec_branch);
              $("#position_view").html(dec_position);
              $("#division_view").html(value.id_principal);
              $("#job_grade_view").html(dec_job_grade);
              $("#status_view").html(status);
              if (value.effective_date != null) {
                $("#join_view").html(TanggalIndonesia(value.effective_date));
              }
              if (value.expired_date != null) {
                $("#effective_view").html(TanggalIndonesia(value.expired_date));
              }
              $("#notes_view").html(value.notes);
              $("#employee_chief_view").html(value.name_chief);
              $("#employee_routing_chief_view").html(value.position_chief);
            });
          }
        },
        error: function(response) {
          get_view(letterID);
        }
      });
    }
  }
  $(document).on('click','.btn-view',function() {
    $("#modal_view").modal('show');
    $(".view").html('<div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div>');
    $("#title_view").html('');
    var letterID = $(this).attr('more_id');
    if (letterID) {
      get_view(letterID);
    }
  });
  function get_publish(letterID) {
    if (letterID) {
      $("#title-publish").html('');
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/decree/employment_certificate/get_edit')}}"+"/"+letterID,
        success: function(response) {
          hide_loading();
          if (response.data) {
            $.each(response.data, function(key, value) {
              if (value.remark_3 == null) {
                var name = value.name;
              }else{
                var remark_3 = value.remark_3.split(';');
                var name = remark_3[0];
              }
              $("#file_label").html('<i>Notes: File must be pdf.</i>')
              if (value.remark_4 != null) {
                $('.file-upload-document').css('display', 'block');
                $('.file-upload-document').attr('src', response.file);
                $(".action_publish").hide();
              }
              $("#title-publish").html('Publish ' +'('+name+')');
              if (value.publish == true) {
                $(".action_publish").hide();
                $("#remark_1").parent().children('span').children('button').attr('disabled',true);
                $("#remark_1").prop('disabled',true);
                $("#publish").prop('disabled',true);
                $("#publish").prop('checked',true);
                $("#remark_4").attr('disabled',true);
              }else{
                $(".action_publish").show();
                $("#remark_1").parent().children('span').children('button').attr('disabled',false);
                $("#remark_1").prop('disabled',false);
                $("#publish").prop('disabled',false);
                $("#publish").prop('checked',false);
                $("#remark_4").attr('disabled',false);
              }
              if (value.remark_4 == null) {
                $(".file-upload-input").css('display','none');
              }else{
                $(".file-upload-input").css('display','block');
              }
              $("#id_letter_publish").val(value.id_letter);
              $("#remark_1").val(value.remark_1);
            });
          }
        },
        error: function(response) {
          get_publish(letterID);
        }
      });
    }
  }
  $(document).on('click','.btn-publish',function() {
    var letterID = $(this).attr('more_id');
    show_loading();
    $("#file_label").html('')
    $("#modal_form_skk_publish").modal('show');
    $("#skkFormPublish")[0].reset();
    $(".invalid-feedback").children("strong").text("");
    $("#skkFormPublish input").removeClass("is-invalid");
    $('.file-upload-document').css('display', 'none');
    $("#remark_4").siblings(".custom-file-label").removeClass("selected").html('<i>Maximum 300kb.</i>');
    if (letterID) {
      get_publish(letterID);
    }
  });
  $(function () {
    $('#skkFormPublish').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_publish").disabled=true;
      let formData = new FormData(this);
      // $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#skkFormPublish input").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        contentType: false,
        processData: false,
        url: "{{ route('save.publish') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_publish").disabled=false;
          if (response.status == 'true') {
            $("#skkFormPublish")[0].reset();
            $('#modal_form_skk_publish').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#skk_table').DataTable().ajax.reload();
          }else {
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Something went wrong! ['+response.message+']'
            });
          }
        },
        error: function (response, textStatus, errorThrown) {
          document.querySelector(".action_publish").disabled=false;
          if (response.status === 422) {
            let errors = response.responseJSON.errors;
            Object.keys(errors).forEach(function (key) {
              $("#" + key).addClass("is-invalid");
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
  $('#advanced').click(function(){
    $('.cf').select2({width:'100%'});
    if($("#cf").css('display') == 'none'){
      $("#cf").show("slow");
    }
    else {
      $("#cf").hide("slow");
    }   
  });
//   $(".custom-file-input").on("change", function() {
//     // var fileName = $(this).val().split("\\").pop();
//     // if (fileName) {
//       // $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
//     // }else{
//     //   $(this).siblings(".custom-file-label").removeClass("selected").html('<i>Maximum 300kb.</i>');
//     //   $('.file-upload-document').css('display', 'none');
//   // }
// });
function readFile(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    if (input.files[0].size > 348576) {
      alert('File size should not exceed 300kb.');
      $(".custom-file-input").siblings(".custom-file-label").removeClass("selected").html('<i>Maximum 300kb.</i>');
      $('.file-upload-document').css('display', 'none');
    }else{
      reader.onload = function(e) {
        let cek = input.files[0].name;
        $('.image-title').html(input.files[0].name);
        if(cek.substr(-3) == 'pdf' || cek.substr(-3) == 'PDF'){
          $(".custom-file-input").siblings(".custom-file-label").addClass("selected").html(input.files[0].name);
          $('.file-upload-document').attr('src', e.target.result);
          $('.file-upload-document').css('display', 'block');
        }else{
          $('#remark_4').val('');
          $("#remark_4").siblings(".custom-file-label").removeClass("selected").html('<i>Maximum 300kb.</i>');
          $('.file-upload-document').css('display', 'none');
          alert('The file format you entered is not supported.')
        }
      };
    }
    reader.readAsDataURL(input.files[0]);
  }else{
    $(".custom-file-input").siblings(".custom-file-label").removeClass("selected").html('<i>Maximum 300kb.</i>');
    $('.file-upload-document').css('display', 'none');
  }
}
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