<script type="text/javascript">
  $(".select_search_edit").select2();
  $(".select_opsi_edit").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $('#tanggal_panggil_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#expired_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#effective_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $("#date_edit").parent().children('span').children('button').attr('disabled',true);
  $("#date_edit").prop('disabled',true);
  function get_edit(letterID) {
    if (letterID) {
      $.getJSON("{{ url('e-letter/invitation_letter/get_edit') }}"+"/"+letterID, function(response) {
        $("#modal_form_supa_edit").modal('show');
        if (response.data) {
          $("#id_employee_edit").append('<option value="">'+response.data.name+' ('+response.data.nik_employee+')'+'</option>');
          $("#id_category_edit").append('<option value="">'+ response.data.category_code +'</option>');
          $("#id_dept_view_edit").append('<option value="">'+ response.data.dec_dept +'</option>');
          $("#id_region_view_edit").append('<option value="">'+ response.data.dec_region +'</option>');
          $("#id_branch_view_edit").append('<option value="">'+ response.data.dec_branch +'</option>');
          $("#id_position_detail_view_edit").append('<option value="">'+ response.data.dec_position +'</option>');
          $("#id_letter").val(response.data.id_letter);
          $("#effective_date_edit").val(response.data.effective_date);
          $("#expired_date_edit").val(response.data.expired_date);
          $("#date_edit").val(response.data.date);
          $("#email_edit").val(response.data.email);
          $("#remark_1_edit").val(response.data.remark_1);
          $("#remark_2_edit").val(response.data.remark_2);
          $("#remark_5_edit").val(response.data.remark_5);
          $("#remark_6_edit").val(response.data.remark_6);
          $("#remark_3_edit").val(response.data.remark_3);
          $("#remark_7_edit").val(response.data.remark_7);
          $("#remark_8_edit").val(response.data.remark_8).trigger('change');
          $("#no_supa_1_edit").val(response.no_supa_1);
          $("#hari_edit").val(response.hari);
          $("#tanggal_panggil_edit").val(response.tanggal_panggil);
          $("#zona_edit").val(response.zona).trigger('change');
          $("#waktu_edit").val(response.waktu);
          $("#id_employee_chief_edit").val(response.data.id_employee_chief).trigger('change');
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        get_edit(letterID);
        // swal({
        //   icon: 'error',
        //   title: 'Oops...',
        //   dangerMode: true,
        //   text: 'Something went wrong! [Unknown Error]'
        // });
        // $("#modal_form_supa_edit").modal('hide');
      });
    }else{
      $("#modal_form_supa_edit").modal('hide');
    }
  }
  $(document).on('click','.btn-edit',function() {
    var letterID = $(this).attr('more_id');
    $("#supaFormEdit")[0].reset();
    $(".select_search_edit").empty();
    $(".select_opsi_edit").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#supaFormEdit input").removeClass("is-invalid");
    $("#supaFormEdit select").removeClass("custom-select");
    if (letterID) {
      get_edit(letterID);
    }
  });
  $("#id_position_routing_chief_edit").select2();
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
    // $("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
    if (employeeID) {
      change_chief_edit(employeeID);
    }
  });
  $(function () {
    $('#supaFormEdit').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_edit").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#supaFormEdit input").removeClass("is-invalid");
      $("#supaFormEdit select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('edit.supa') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_edit").disabled=false;
          if (response.status == 'true') {
            $("#supaFormEdit")[0].reset();
            $('#modal_form_supa_edit').modal('hide');
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