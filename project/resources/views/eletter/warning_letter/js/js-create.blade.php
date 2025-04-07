<script type="text/javascript">
  $(".new").click(function() {
    $("#spForm")[0].reset();
    $(".select_search").empty();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#spForm input").removeClass("is-invalid");
    $("#spForm select").removeClass("custom-select");
    $("#spForm textarea").removeClass("is-invalid");
    $("#modal_form_sp").modal('show');
    hide_loading();
  });
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
  function get_employee(selectedAttribute, categoryID) {
    $.ajax({
      type: "GET",
      dataType: 'json',
      url: "{{ url('e-letter/company_letter/warning_letter/get_category') }}"+"?category_type="+selectedAttribute+"&category_id="+categoryID,
      data : {id_url:global_url_server},
      success: function(response) {
        if (response.employee) {
          hide_loading();
          $("#id_employee").empty();
          $.each(response.employee, function(key, value_employee) {
            $("#id_employee").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +' ('+value_employee.nik_employee+')'+'</option>');
          });
          $("#id_employee").val(null).trigger('change');
        } else {
          $("#id_employee").empty();
          $(".select_search").empty();
        }
      },
      error: function(response) {
        get_employee(selectedAttribute, categoryID);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // hide_loading();
        // $("#id_category").val(null).trigger('change');
      }
    });
  }
  $(document).on('change','#id_category',function() {
    var categoryID = $(this).val();
    $("#id_employee").empty();
    // $("#id_employee").append('<option><span class="sr-only">Loading...</span></option>');
    $(".select_search").empty();
    if (categoryID) {
      show_loading();
      document.getElementById('id_employee').disabled=false;
      var selectedOption = this.options[this.selectedIndex];
      var selectedAttribute = selectedOption.getAttribute('data-attribute');
      get_employee(selectedAttribute, categoryID);
    }else{
      document.getElementById('id_employee').disabled=true;
      $(".select_search").empty();
      $("#id_employee").empty();
    }
  });

  function change_employee(employeeID) {
    if (employeeID) {
      show_loading();
      $.ajax({
        type: "GET",
        dataType: 'json',
        url: "{{ url('e-letter/company_letter/warning_letter/change_employee') }}"+"?employee_id="+employeeID,
        success: function(response) {
          if (response.data) {
            hide_loading();
            $(".select_search").empty();
            $.each(response.data, function(key, value_data) {
              $("#id_region").append('<option value="'+value_data.id_region+'">' + value_data.dec_region +'</option>');
              $("#id_branch").append('<option value="'+value_data.id_branch+'">' + value_data.dec_branch +'</option>');
              $("#id_position_detail").append('<option value="'+value_data.id_routing+'">' + value_data.dec_position +'</option>');
              $("#id_job_grade").append('<option value="'+value_data.id_job_grade+'">' + value_data.dec_job_grade +'</option>');
              $("#id_dept").append('<option value="'+value_data.id_dept+'">' + value_data.dec_dept +'</option>');
              $("#id_principal").append('<option selected value="'+value_data.dec_principal+','+'">' + value_data.dec_principal +'</option>');
              $("#id_employment_status").append('<option value="'+value_data.id_employment_status+'">' + value_data.status +'</option>');
            });
          } else {
            alert('data tidak ditemukan')
            $(".select_search").empty();
            $("#id_principal").empty();
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
          // $("#id_employee").val(null).trigger('change');
        }
      });
    }else{
      $(".select_search").empty();
      $("#id_principal").empty();
    }
  }
  $(document).on('change','#id_employee',function() {
    var employeeID = $(this).val();
    $(".select_search").empty();
    $("#id_principal").empty();
    if (employeeID) {
      change_employee(employeeID);
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
    // $("#id_position_routing_chief").append('<option value=""><span class="sr-only">Loading...</span></option>');
    if (employeeID) {
      change_chief(employeeID);
    }
  });

  $(function () {
    $('#spForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#spForm input").removeClass("is-invalid");
      $("#spForm select").removeClass("custom-select");
      $("#spForm textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.sp') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#spForm")[0].reset();
            // $(".select_search").val(null).trigger('change');
            $(".select_search").empty();
            $("#id_category").val(null).trigger('change');
            $("#id_employee").val(null).trigger('change');
            $("#id_employee_chief").val(null).trigger('change');
            $("#id_position_routing_chief").empty();
            $('#modal_form_sp').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            // window.location.reload();
            $('#sp_table').DataTable().ajax.reload();
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