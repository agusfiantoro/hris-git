<script type="text/javascript">
  $(".select_search_edit").select2();
  $(".select_opsi_edit").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  // $("#id_employment_status_edit").select2({
  //   allowClear: true,
  // });
  $('#date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#effective_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#expired_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $("#effective_date_edit").parent().children('span').children('button').attr('disabled',true);
  function get_edit(id_letter) {
    $.ajax({
      type: "GET",
      url: "{{url('e-letter/company_letter/warning_letter/get_edit')}}"+"/"+id_letter,
      success: function(response) {
        if (response) {
          $("#modal_form_swp_edit").modal('show');
          $.each(response, function(key, value) {
            $("#id_category_edit").val(value.id_category).trigger('change');
            $("#date_edit").val(value.date);
            $("#id_letter").val(value.id_letter);
            $("#expired_date_edit").val(value.expired_date);
            $("#effective_date_edit").val(value.effective_date);
            $("#id_category_edit").val(value.id_category).trigger('change');
            $("#id_employment_status_edit").val(value.id_employment_status).trigger('change');
            $("#remark_5_edit").val(value.remark_5).trigger('change');
            $("#keterangan_edit").val(value.notes);
            $("#email_edit").val(value.email);
            $("#nik_employee_edit").val(value.nik_employee);
            $("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
            $("#id_employee_edit").append('<option value="' + value.name + '">'+ value.name +' ('+value.nik_employee+')'+'</option>');
            $("#id_dept_edit").append('<option value="' + value.dec_dept + '">'+ value.dec_dept +'</option>');
            $("#id_region_edit").append('<option value="' + value.dec_region + '">'+ value.dec_region +'</option>');
            $("#id_branch_edit").append('<option value="' + value.dec_branch + '">'+ value.dec_branch +'</option>');
            $("#id_position_detail_edit").append('<option value="' + value.dec_position + '">'+ value.dec_position +'</option>');
            $("#id_job_grade_edit").append('<option value="' + value.dec_job_grade + '">'+ value.dec_job_grade +'</option>');
            $("#id_principal_edit").append('<option selected value="' + value.id_principal + '">'+ value.id_principal +'</option>');
            $("#id_employment_status_edit").append('<option value="' + value.status + '">'+ value.status +'</option>');
            $("#remark_1_edit").val(value.remark_1);
            $("#remark_2_edit").val(value.remark_2);
            $("#remark_3_edit").val(value.remark_3);
            $("#remark_4_edit").val(value.remark_4);
          });
        }
      },
      error: function(response) {
        get_edit(id_letter);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // $("#modal_form_swp_edit").modal('hide');
      }
    });
  }
  $(document).on('click', '.btn-edit', function() {
    var id_letter = $(this).attr('more_id');
    $("#swpFormEdit")[0].reset();
    // $(".select_search_edit").append('<option><span class="sr-only">Loading...</span></option>');
    $(".select_search_edit").empty();
    $(".select_opsi_edit").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#swpFormEdit input").removeClass("is-invalid");
    $("#swpFormEdit select").removeClass("custom-select");
    $("#swpFormEdit textarea").removeClass("is-invalid");
    if (id_letter) {
      get_edit(id_letter);
    }else{
      $("#modal_form_swp_edit").hide();
    }
  });
  function change_chief_edit(employeeID) {
    if (employeeID) {
      show_loading();
      $.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
        if (response) {
          hide_loading();
          $("#id_position_routing_chief_edit").empty();
          $("#id_position_routing_chief_edit").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
        } else {
          $("#id_position_routing_chief_edit").empty();
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        change_chief_edit(employeeID);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // hide_loading();
        // $("#id_employee_chief_edit").val(null).trigger('change');
      });
    } else {
      $("#id_position_routing_chief_edit").empty();
    }
  }
  $(document).on('change','#id_employee_chief_edit',function() {
    var employeeID = $(this).val();
    $("#id_position_routing_chief_edit").empty();
    if (employeeID) {
      change_chief_edit(employeeID);
    }
  });
  $(function () {
    $('#swpFormEdit').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_edit").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#swpFormEdit input").removeClass("is-invalid");
      $("#swpFormEdit select").removeClass("custom-select");
      $("#swpFormEdit textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('edit.sp') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_edit").disabled=false;
          if (response.status == 'true') {
            $("#swpFormEdit")[0].reset();
            // $(".select_search_edit").val(null).trigger('change');
            $(".select_search_edit").empty();
            $("#id_category_edit").val(null).trigger('change');
            $("#id_employee_chief_edit").val(null).trigger('change');
            $("#id_position_routing_chief_edit").empty();
            $('#modal_form_swp_edit').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: 'Stern Warning Letter Edit Succesfully !!'
            });
            // window.location.reload();
            $('#swp_table').DataTable().ajax.reload();
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
          document.querySelector(".action_edit").disabled=false;
          if (response.status === 422) {
            let errors = response.responseJSON.errors;
            Object.keys(errors).forEach(function (key) {
              $("#" + key + "_edit").addClass("is-invalid");
              $("select[id='" + key + "_edit" + "']").addClass("custom-select");
              $("#" + key + "_editError").children("strong").text(errors[key][0]);
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