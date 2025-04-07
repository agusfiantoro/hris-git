<script type="text/javascript">
  $("#status_edit").select2({
    allowClear: true,
    placeholder: ":. FILTER OPTION .:"
  });
  $(document).on('click','.btn-edit',function() {
    var idGeneralData = $(this).attr('more_id');
    $("#status_edit").val(null).trigger('change');
    $("#mlFormEdit")[0].reset();
    $(".invalid-feedback").children("strong").text("");
    $("#mlFormEdit input").removeClass("is-invalid");
    $("#mlFormEdit select").removeClass("custom-select");
    $("#modal_form_ml_edit").modal('show');
    if (idGeneralData) {
      $.getJSON("{{ url('e-letter/master_letter/master_letter/get_edit') }}"+"/"+idGeneralData, function(response) {
        if (response) {
          $.each(response, function(key, value) {
            $("#id_general_data").val(value.id_general_data);
            $("#sequence_edit").val(value.sequence);
            $("#code_edit").val(value.code);
            $("#description_edit").val(value.description);
            $("#status_edit").val(value.status).trigger('change');
          });
        }
      }).fail(function(jqXHR, textStatus, errorThrown) {
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! [Unknown Error]'
        });
        $("#modal_form_ml_edit").modal('hide');
      });
    }
  });

  $(function () {
    $('#mlFormEdit').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_edit").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#mlFormEdit input").removeClass("is-invalid");
      $("#mlFormEdit select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('edit.ml') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_edit").disabled=false;
          if (response.status == 'true') {
            $("#status").val(null).trigger('change');
            $("#mlFormEdit")[0].reset();
            $('#modal_form_ml_edit').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#ml_table').DataTable().ajax.reload();
          }else if(response.status == 'sama'){
            swal({
              icon: 'warning',
              type: 'warning',
              title: 'Warning',
              text: response.message
            });
          }
          else {
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