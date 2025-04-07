<script type="text/javascript">
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
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
  $('#remark_1').on('keyup', function() {
    $(this).val($(this).val().toUpperCase());
  });
  $("#id_position_routing_chief").select2();
  $(".new").click(function() {
    $("#modal_form_ski").modal('show');
    $("#skiForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#skiForm input").removeClass("is-invalid");
    $("#skiForm select").removeClass("custom-select");
    $("#skiForm textarea").removeClass("is-invalid");
    hide_loading();
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
    $('#skiForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#skiForm input").removeClass("is-invalid");
      $("#skiForm select").removeClass("custom-select");
      $("#skiForm textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.ski') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#skiForm")[0].reset();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_ski').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#ski_table').DataTable().ajax.reload();
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