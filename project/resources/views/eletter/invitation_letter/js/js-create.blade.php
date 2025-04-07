<script type="text/javascript">
  function get_employee() {
    $.getJSON("{{ url('e-letter/company_letter/invitation_letter/get_data') }}",{id_url:global_url_server}, function(response) {
      $("#id_employee_view").empty();
      $.each(response.supa1, function(key, value_supa1) {
        $("#id_employee_view").append('<option value="' + value_supa1.id_employee + '">'+value_supa1.nama_karyawan+' ('+value_supa1.nik_employee+')'+'</option>');
      });
      $("#id_employee_view").val(null).trigger('change');
    }).fail(function(jqXHR, textStatus, errorThrown) {
     get_employee();
   });
  }
  $(document).ready(function() {
    $(".new").click(function() {
      $("#supaForm")[0].reset();
      $(".select_search").empty();
      $(".select_opsi").val(null).trigger('change');
      $("#id_category").val(null).trigger('change');
      $(".invalid-feedback").children("strong").text("");
      $("#supaForm input").removeClass("is-invalid");
      $("#supaForm select").removeClass("custom-select");
      get_employee();
      $("#modal_form_supa").modal('show');
      hide_loading();
    });
  });
  $("#id_category").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $("#id_position_routing_chief").select2();
  $(".select_search").select2();
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $('#tanggal_panggil').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
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
  $(document).on('change','#id_category',function() {
    var categoryID = $(this).val();
    if (categoryID) {
     var selectedOption = this.options[this.selectedIndex];
     var selectedAttribute = selectedOption.getAttribute('data-attribute');
     if (selectedAttribute == 'SUPA1') {
      document.getElementById('id_employee_view').disabled=false;
      document.getElementById('filter').disabled=true;
      $(".select_search").empty();
      $(".value_select_search").val('');
      $("#id_employee_view").append('<option selected="" hidden="" value="">:. FILTER OPTION .:</option>');
    }else{
      document.getElementById('filter').disabled=false;
      document.getElementById('id_employee_view').disabled=true;
      $(".select_search").empty();
      $(".value_select_search").val('');
      $("#id_employee_view").append('<option selected="" hidden="" value="">:. FILTER OPTION .:</option>');
    }
    $("#category_code").val(selectedAttribute);
    $("#no_supa_1").val('');
    $("#remark_7").val('');
    $("#remark_6").val('');
    $("#remark_3").val('');
    $("#remark_5").val('');
  }else{
    $(".select_search").empty();
    $(".value_select_search").val('');
    $("#id_employee_view").append('<option selected="" hidden="" value="">:. FILTER OPTION .:</option>');
    document.getElementById('filter').disabled=true;
    document.getElementById('id_employee_view').disabled=true;
    $("#no_supa_1").val('');
    $("#remark_7").val('');
    $("#remark_6").val('');
    $("#remark_3").val('');
    $("#remark_5").val('');
  }
});
  $(document).on('click','#filter',function() {
    var category_code = $("#category_code").val();
    if(category_code == 'SUPA2'){
      $("#modal_view_supa2").modal('show');
    }else{
      document.getElementById('filter').disabled=true;
      alert('Data tidak ditemukan, pilih Kategori terlebih dahulu');
    }
  });
  function change_supa2(letterID) {
    if (letterID) {
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/company_letter/invitation_letter/select_supa_2')}}"+"?letter_id="+letterID,
        success: function(response) {
          if (response) {
            hide_loading();
            $.each(response, function(key, value){
              $(".select_search").empty();
              $("#id_employee_view").val(value.id_employee).trigger('change');
              $("#id_region").append('<option value="'+value.id_region+'">'+value.dec_region+'</option>');
              $("#id_branch").append('<option value="'+value.id_branch+'">'+value.dec_branch+'</option>');
              $("#id_dept").append('<option value="'+value.id_dept+'">'+value.dec_dept+'</option>');
              $("#id_position_detail").append('<option value="'+value.id_routing+'">'+value.dec_position+'</option>');

              $("#id_employee").val(value.id_employee);
              $("#no_supa_1").val(value.reference_number);
              $("#remark_7").val(value.date);
              $("#remark_6").val(value.remark_6);
              $("#remark_3").val(value.remark_3);
              $("#remark_5").val(value.remark_5);
            });
          }
        },
        error: function(response) {
          change_supa2(letterID);
          // swal({
          //   icon: 'error',
          //   title: 'Oops...',
          //   dangerMode: true,
          //   text: 'Something went wrong! [Unknown Error]'
          // });
          // hide_loading();
          // $(".select_search").empty();
        }
      });
    }
  }
  $(document).on('click','.select_supa_2',function() {
    var letterID = $(this).attr('more_id');
    document.getElementById('id_employee_view').disabled=true;
    $(".value_select_search").val('');
    $(".select_search").empty();
    $("#modal_view_supa2").modal('hide');
    if (letterID) {
      change_supa2(letterID);
    }
  });
  // change supa 1
  function change_employee(employeeID) {
    if (employeeID) {
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/company_letter/invitation_letter/select_supa_1')}}"+"?employee_id="+employeeID,
        success: function(response) {
          if (response) {
            hide_loading();
            $(".select_search").empty();
            $("#id_employee_view").val(response.nama_karyawan);
            $("#id_region").append('<option value="'+response.id_region+'">'+response.dec_region+'</option>');
            $("#id_branch").append('<option value="'+response.id_branch+'">'+response.dec_branch+'</option>');
            $("#id_dept").append('<option value="'+response.id_dept+'">'+response.dec_dept+'</option>');
            $("#id_position_detail").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
            $("#id_employee").val(response.id_employee);
          }
        },
        error: function(response) {
          change_employee(employeeID);
          // swal({
          //   icon: 'error',
          //   title: 'Oops...',
          //   dangerMode: true,
          //   text: 'Something went wrong! [Unknown Error]'
          // });
          // hide_loading();
          // $("#id_employee_view").val(null).trigger('change');
        }
      });
    }else{
      $(".select_search").empty();
      $(".value_select_search").val('');
    }
  }
  $(document).on('change','#id_employee_view',function() {
    var employeeID = $(this).val();
    $(".value_select_search").val('');
    $(".select_search").empty();
    if (employeeID) {}
      change_employee(employeeID);
  });
  function change_chief(employeeID) {
    if (employeeID) {
      show_loading();
      $.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
        if (response) {
          hide_loading();
          $("#id_position_routing_chief").empty();
          $("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
        } else {
          $("#id_position_routing_chief").empty();
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! [Unknown Error]'
        });
        hide_loading();
        $("#id_employee_chief").val(null).trigger('change');
      });
    } else {
      $("#id_position_routing_chief").empty();
    }
  }
  $(document).on('change','#id_employee_chief',function() {
    var employeeID = $(this).val();
    $("#id_position_routing_chief").empty();
    // $("#id_position_routing_chief").append('<option value=""><span class="sr-only">Loading...</span></option>');
    if (employeeID) {
      change_chief(employeeID);
    }
  });
  $(function () {
    $('#supaForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#supaForm input").removeClass("is-invalid");
      $("#supaForm select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.supa') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#supaForm")[0].reset();
            $(".select_search").empty();
            $("#id_category").val(null).trigger('change');
            $("#id_employee_chief").val(null).trigger('change');
            $("#id_position_routing_chief").empty();
            $('#modal_form_supa').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#supa_table').DataTable().ajax.reload();
            $('#supa2_table').DataTable().ajax.reload();
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
  // $(function () {
    $(document).ready(function() {
      $('#supa2_table').DataTable({
        processing: true,
        pageLength: 10,
        responsive: true, 
        autoWidth: false,       
        ajax: {
          url: "{{ route('supa2.data') }}",
          data : {id_url:global_url_server},
          error: function (jqXHR, textStatus, errorThrown) {
            $('#supa2_table').DataTable().ajax.reload();
          }
        },
        columns: [
        {
          defaultContent: '',
          orderable: false,
        },
        {   
          defaultContent: '',
          orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'nik_employee', name: 'nik_employee' },
        { data: 'name', name: 'name' },
        { data: 'reference_number', name: 'reference_number' },
        { data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {                                      
          return data;
        } 
      },
      ]
    });
    });  

  </script>
<!--  -->