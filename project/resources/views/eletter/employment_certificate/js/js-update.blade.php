<script type="text/javascript">
  $('#effective_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#expired_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $(".select_search_edit").select2();
  $("#id_position_routing_chief_edit").select2();
  $(".select_opsi_edit").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $("#date_edit").parent().children('span').children('button').attr('disabled',true);
  $("#effective_date_edit").parent().children('span').children('button').attr('disabled',true);
  $("#expired_date_edit").parent().children('span').children('button').attr('disabled',true);
  function get_edit(letterID) {
    if (letterID) {
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/decree/employment_certificate/get_edit')}}"+"/"+letterID,
        success: function(response) {
          if (response) {
            $.each(response, function(key, value) {
                // $("#id_category_edit").val(value.id_category).trigger('change');
                $("#date_edit").val(value.date);
                $("#id_letter").val(value.id_letter);
                $("#effective_date_edit").val(value.effective_date);
                $("#expired_date_edit").val(value.expired_date);
                $("#id_category_edit").val(value.id_category).trigger('change');
                $("#keterangan_edit").val(value.notes);
                $("#email_edit").val(value.email);
                // letter location
                $("#remark_2_edit").val(value.remark_2).trigger('change');
                // 
                $("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
                $("#id_employee_edit").append('<option value="' + value.id_employee + '">'+ value.name + ' / ' + value.nik_employee +' ('+value.status_employee+')'+'</option>');
                $("#id_category_edit").append('<option value="' + value.id_category + '" more_code="'+value.code+'">'+ value.dec_category +'</option>').val(value.id_category).trigger('change');
                $("#id_dept_edit").append('<option value="' + value.dec_dept + '">'+ value.dec_dept +'</option>');
                $("#id_region_edit").append('<option value="' + value.dec_region + '">'+ value.dec_region +'</option>');
                $("#id_branch_edit").append('<option value="' + value.dec_branch + '">'+ value.dec_branch +'</option>');
                $("#id_position_detail_edit").append('<option value="' + value.dec_position + '">'+ value.dec_position +'</option>');
                $("#id_job_grade_edit").append('<option value="' + value.dec_job_grade + '">'+ value.dec_job_grade +'</option>');
                $("#id_employment_status_edit").append('<option value="' + value.status + '">'+ value.status +'</option>');
                $("#id_principal_edit").append('<option selected value="' + value.id_principal + '">'+ value.id_principal +'</option>');
              });
          }
        },
        error: function(response) {
          get_edit(letterID);
        }
      });
    }else{
      $("#modal_form_skk_edit").modal('hide');
    }
  }
  $(document).ready(function() {
    $(document).on('click', '.btn-edit', function() {
      show_loading();
      var letterID = $(this).attr('more_id');
      $("#skkFormEdit")[0].reset();
      $(".select_search_edit").empty()
      $(".select_opsi_edit").val(null).trigger('change');
      $(".invalid-feedback").children("strong").text("");
      $("#skkFormEdit input").removeClass("is-invalid");
      $("#skkFormEdit select").removeClass("custom-select");
      $("#skkFormEdit textarea").removeClass("is-invalid");
      $("#modal_form_skk_edit").modal('show');
      if (letterID) {
        get_edit(letterID);
      }
    });
  });
  $(document).on('change','#id_category_edit',function() {
    var categoryID = $(this).val();
    if (categoryID) {
      const selectElement = document.getElementById('id_category_edit');
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const moreCategory = selectedOption.getAttribute('more_code');
      if (moreCategory == "PUB") {
        $("#row_keterangan_edit").show();
        $("#validate_exp_edit").html('');
        $("#expired_date_edit").prop('disabled',true);
        document.getElementById('expired_date_edit').readOnly=false;
      }else{
        $("#row_keterangan_edit").hide();
        $("#validate_exp_edit").html('*');
        $("#expired_date_edit").prop('disabled',false);
        document.getElementById('expired_date_edit').readOnly=true;
      }
    }
  });
  function chief_edit(employeeID) {
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
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [' + errorThrown + ']'
        // });
        // hide_loading();
        // $("#id_employee_chief_edit").val(null).trigger('change');
        hide_loading();
        chief_edit(employeeID);
      });
    } else {
      $("#id_position_routing_chief_edit").empty();
    }
  }
  $(document).on('change','#id_employee_chief_edit',function() {
    var employeeID = $(this).val();
    $("#id_position_routing_chief_edit").empty();
    // $("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
    if (employeeID) {
      chief_edit(employeeID);
    }
  });
  $(function () {
    $('#skkFormEdit').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_edit").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#skkFormEdit input").removeClass("is-invalid");
      $("#skkFormEdit select").removeClass("custom-select");
      $("#skkFormEdit textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('edit.skk') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_edit").disabled=false;
          if (response.status == 'true') {
            $("#skkFormEdit")[0].reset();
            $(".select_search_edit").val(null).trigger('change');
            $(".select_opsi_edit").val(null).trigger('change');
            $('#modal_form_skk_edit').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            // window.location.reload();
            $('#skk_table').DataTable().ajax.reload();
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
        else if (response.status === 500) {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! [Unknown Error]'
          });
        }
        else {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! [Unknown Error]'
          });
        }
      }
    });
    });
  });
</script>