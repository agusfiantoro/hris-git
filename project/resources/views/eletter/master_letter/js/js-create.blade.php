<script type="text/javascript">
  $("#status").select2({
    allowClear: true,
    placeholder: ":. FILTER OPTION .:"
  });
  $(".new").click(function() {
    $("#mlForm")[0].reset();
    $("#status").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#mlForm input").removeClass("is-invalid");
    $("#mlForm select").removeClass("custom-select");
    $("#modal_form_ml").modal('show');
    $.getJSON("{{ url('e-letter/master_letter/master_letter/get_sequence') }}", function(response) {
      $("#sequence").val(response);
    }).fail(function(jqXHR, textStatus, errorThrown) {
      swal({
        icon: 'error',
        title: 'Oops...',
        dangerMode: true,
        text: 'Something went wrong! [Unknown Error]'
      });
      $("#modal_form_ml").modal('hide');
    });
  });
  $(function () {
    $('#mlForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#mlForm input").removeClass("is-invalid");
      $("#mlForm select").removeClass("custom-select");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.ml') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $('#ml_table').DataTable().ajax.reload();
            $("#modal_form_ml").modal('hide');
            $("#mlForm")[0].reset();
            $("#status").val(null).trigger('change');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
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