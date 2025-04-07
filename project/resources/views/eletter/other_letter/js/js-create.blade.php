<script type="text/javascript">
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $(".select_search").select2();
  $("#id_position_routing_chief").select2();
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
  function get_employee() {
    $.getJSON("{{ url('e-letter/company_letter/other_letter/get_data') }}",{id_url:global_url_server}, function(response) {
      $("#id_employee").empty();
      $.each(response.employee, function(key, value_employee) {
        $("#id_employee").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +' ('+value_employee.nik_employee+')'+'</option>');
      });
      $("#id_employee").val(null).trigger('change');
    }).fail(function(jqXHR, textStatus, errorThrown) {
     get_employee();
   });
  }
  $(document).ready(function() {
    $(".new").click(function() {
      get_employee();
      $("#otherForm")[0].reset();
      $(".select_search").empty();
      $(".select_opsi").val(null).trigger('change');
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      $("#otherForm textarea").removeClass("is-invalid");
      $('#remark_3').prop('checked', false).trigger('change');
      $('#remark_4').prop('checked', false).trigger('change');
      $("#modal_form_other").modal('show');
      hide_loading();
      $("#id_region").select2();
      $("#id_dept").select2();
      $("#id_branch").select2();
      document.getElementById('id_region').setAttribute('readonly','readonly');
      document.getElementById('id_dept').setAttribute('readonly','readonly');
      document.getElementById('id_branch').setAttribute('readonly','readonly');
    });
  });
  $(document).on('change', '#remark_3', function () {
    var byname = document.getElementById('remark_3');
    var by_dprg = document.getElementById('remark_5');
    if (byname.checked) {
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      $('#remark_4').prop('checked', false);
      $('#remark_5').prop('checked', false);
      $(".validasi_label").html('<sup class="text text-danger">*</sup>');
      $("#validasi_label_dept").html('<sup class="text text-danger">*</sup>');
      document.getElementById('id_employee').disabled=false;
      $("#id_employee").val(null).trigger('change');
      // 
      $("#id_region").select2();
      $("#id_branch").select2();
      $("#id_dept").select2();
      document.getElementById('id_region').setAttribute('readonly','readonly');
      document.getElementById('id_branch').setAttribute('readonly','readonly');
      document.getElementById('id_dept').setAttribute('readonly','readonly');
      $("#id_region").val(null).trigger('change');
      $("#id_dept").val(null).trigger('change');
    }else{
      $("#id_dept").empty();
      get_dept();
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      $(".validasi_label").html('');
      $("#validasi_label_dept").html('<sup class="text text-danger">*</sup>');
      $('#remark_4').prop('checked', false);
      $('#remark_5').prop('checked', false);
      document.getElementById('id_employee').disabled=true;
      $("#id_employee").val(null).trigger('change');
      if (by_dprg.checked) {
        $("#id_region").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        $("#id_branch").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        $("#id_dept").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        document.getElementById('id_region').removeAttribute('readonly');
        document.getElementById('id_branch').removeAttribute('readonly');
        document.getElementById('id_dept').removeAttribute('readonly');
      }else{
        $("#id_region").select2();
        $("#id_branch").select2();
        $("#id_dept").select2();
        document.getElementById('id_region').setAttribute('readonly','readonly');
        document.getElementById('id_branch').setAttribute('readonly','readonly');
        document.getElementById('id_dept').setAttribute('readonly','readonly');
      }
      $("#id_region").val(null).trigger('change');
      $("#id_dept").val(null).trigger('change');
    }
  });
  $(document).on('change', '#remark_5', function () {
    var remark_5 = document.getElementById('remark_5');
    var byname = document.getElementById('remark_3');
    if (remark_5.checked) {
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      if ($("#remark_3").prop('checked')) {
        $("#id_region").select2();
        $("#id_branch").select2();
        $("#id_dept").select2();
        document.getElementById('id_region').setAttribute('readonly','readonly');
        document.getElementById('id_branch').setAttribute('readonly','readonly');
        document.getElementById('id_dept').setAttribute('readonly','readonly');
      }else{
        $("#id_region").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        $("#id_branch").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        $("#id_dept").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        document.getElementById('id_region').removeAttribute('readonly');
        document.getElementById('id_branch').removeAttribute('readonly');
        document.getElementById('id_dept').removeAttribute('readonly');
      }
    }else{
      if ($("#remark_3").prop('checked')) {

      }else{
        $("#id_region").val(null).trigger('change');
      }
      $("#id_region").select2();
      $("#id_branch").select2();
      $("#id_dept").select2();
      document.getElementById('id_region').setAttribute('readonly','readonly');
      document.getElementById('id_branch').setAttribute('readonly','readonly');
      document.getElementById('id_dept').setAttribute('readonly','readonly');
    }
  });
  $(document).on('change', '#remark_4', function () {
    var cek_byname = document.getElementById('remark_3');
    var remark_4 = document.getElementById('remark_4');
    if (remark_4.checked) {
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      if (cek_byname.checked) {
        $("#id_dept").select2();
        // $("#id_dept").val(null).trigger('change');
        // $("#validasi_label_dept").html('');
        $("#validasi_label_dept").html('<sup>*</sup>');
        document.getElementById('id_dept').setAttribute('readonly','readonly');
      }else{
        $("#id_dept").select2();
        $("#id_dept").val(null).trigger('change');
        // $("#validasi_label_dept").html('');
        $("#validasi_label_dept").html('');
        document.getElementById('id_dept').setAttribute('readonly','readonly');
      }
    }else{
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      if (cek_byname.checked) {
        $("#id_dept").select2();
        // $("#id_dept").val(null).trigger('change');
        $("#validasi_label_dept").html('<sup>*</sup>');
        document.getElementById('id_dept').setAttribute('readonly','readonly');
      }else{
        $("#id_dept").select2({
          allowClear: true,
          placeholder: ':. FILTER OPTION .:'
        });
        $("#validasi_label_dept").html('<sup>*</sup>');
        // $("#id_dept").val(null).trigger('change');
        document.getElementById('id_dept').removeAttribute('readonly');
      }
    }
  });
  function change_employee(employeeID) {
    if (employeeID) {
      show_loading();
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: "{{ url('e-letter/company_letter/other_letter/change_employee') }}"+"?employee_id="+employeeID,
        success: function(response) {
          if (response) {
            hide_loading();
            $(".select_search").empty();
            $("#id_region").val(null).trigger('change');
            $("#id_dept").val(null).trigger('change');
            $.each(response, function(key, value_data) {
              $("#id_region").val(value_data.id_region).trigger('change');
              $("#id_branch").append('<option value="'+value_data.id_branch+'">' + value_data.dec_branch +'</option>').val(value_data.id_branch).trigger('change');
              $("#id_position_detail").append('<option value="'+value_data.id_position_detail+'">' + value_data.dec_position +'</option>');
              $("#id_job_grade").append('<option value="'+value_data.id_job_grade+'">' + value_data.dec_job_grade +'</option>');
              $("#id_dept").append('<option value="'+value_data.id_dept+'">' + value_data.dec_dept +'</option>').val(value_data.id_dept).trigger('change');
              $("#id_principal").append('<option selected value="'+value_data.dec_principal+'">' + value_data.dec_principal +'</option>');
              $("#id_employment_status").append('<option value="'+value_data.id_employment_status+'">' + value_data.status +'</option>');
            });
          } else {
            alert('data tidak ditemukan')
            $(".select_search").empty();
          }
        },
        error: function(response) {
          // change_employee(employeeID);
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! [Unknown Error]'
          });
          hide_loading();
          $("#id_employee").val(null).trigger('change');
        }
      });
    }else{
      $(".select_search").empty();
    }
  }
  $(document).on('change','#id_employee',function() {
    var employeeID = $(this).val();
    $(".select_search").empty();
    $("#id_region").val(null).trigger('change');
    $("#id_dept").val(null).trigger('change');
    if (employeeID) {
      change_employee(employeeID);
    }
  });
  function change_region(regionID) {
    if (regionID) {
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/company_letter/other_letter/get_branch')}}"+"?region_id="+regionID,
        data : {id_url:global_url_server},
        success: function(response) {
          if (response) {
            hide_loading();
            $.each(response, function(key, value) {
              $("#id_branch").append('<option value="' + value.id_branch + '">'+ value.description +'</option>');
            });
          }else{
            $("#id_branch").empty();
          }
        },
        error: function(response) {
          change_region(regionID);
          // swal({
          //   icon: 'error',
          //   title: 'Oops...',
          //   dangerMode: true,
          //   text: 'Something went wrong! [Unknown Error]'
          // });
          // $("#id_region").val(null).trigger('change');
        }
      });
    }else{
      $("#id_branch").empty();
    }
  }
  $(document).on('change','#id_region',function() {
    var regionID = $(this).val();
    $("#id_branch").empty();
    if (regionID) {
      change_region(regionID);
    }
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
        change_chief(employeeID);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // hide_loading();
        // $("#id_employee_chief").val(null).trigger('change');
      });
    } else {
      $("#id_position_routing_chief").empty();
    }
  }
  $(document).on('change','#id_employee_chief',function() {
    var employeeID = $(this).val();
    $("#id_position_routing_chief").empty();
    if (employeeID) {
      change_chief(employeeID);
    }
  });
  $(function () {
    $('#otherForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#otherForm input").removeClass("is-invalid");
      $("#otherForm select").removeClass("custom-select");
      $("#otherForm textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.other') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $('#other_table').DataTable().ajax.reload();
            $("#modal_form_other").modal('hide');
            $("#otherForm")[0].reset();
            $(".select_search").empty();
            $(".select_opsi").val(null).trigger('change');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#other_table').DataTable().ajax.reload();
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
</script>