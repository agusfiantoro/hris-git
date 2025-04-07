<script type="text/javascript">
  $(".select_opsi_edit").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $("#id_position_routing_chief_edit").select2();
  $('#date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $("#date_edit").parent().children('span').children('button').attr('disabled',true);
  $('#effective_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#expired_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $('#remark_1_edit').on('keyup', function() {
    $(this).val($(this).val().toUpperCase());
  });
  function get_edit(letterID) {
   if (letterID) {
    $.getJSON("{{ url('e-letter/decree/internship_certificate/get_edit') }}"+"/"+letterID, function(response) {
      if (response) {
        $.each(response, function(key, value) {
          $("#date_edit").val(value.date);
          $("#id_letter").val(value.id_letter);
          $("#id_category_edit").val(value.id_category).trigger('change');
          $("#id_dept_edit").val(value.id_dept).trigger('change');
          $("#remark_1_edit").val(value.remark_1);
          $("#remark_2_edit").val(value.remark_2).trigger('change');
          $("#remark_3_edit").val(value.remark_3);
          $("#remark_4_edit").val(value.remark_4);
          $("#remark_5_edit").val(value.remark_5).trigger('change');
          $("#effective_date_edit").val(value.effective_date);
          $("#expired_date_edit").val(value.expired_date);
          $("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
          $("#notes_edit").val(value.notes);
          $("#email_edit").val(value.email);
        });
      }
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_edit(letterID);
    });
  }else{
    $("#modal_form_ski_edit").modal('hide');
  }
}
$(document).on('click','.btn-edit',function() {
  show_loading();
  var letterID = $(this).attr('more_id');
  $("#skiFormEdit")[0].reset();
  $(".select_opsi_edit").val(null).trigger('change');
  $(".invalid-feedback").children("strong").text("");
  $("#skiFormEdit input").removeClass("is-invalid");
  $("#skiFormEdit select").removeClass("custom-select");
  $("#skiFormEdit textarea").removeClass("is-invalid");
  $("#modal_form_ski_edit").modal('show');
  if (letterID) {
    get_edit(letterID);
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
    // $("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
    if (employeeID) {
      change_chief_edit(employeeID);
    }
  });
$(function () {
  $('#skiFormEdit').submit(function (e) {
    e.preventDefault();
    document.querySelector(".action_edit").disabled=true;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#skiFormEdit input").removeClass("is-invalid");
    $("#skiFormEdit select").removeClass("custom-select");
    $("#skiFormEdit textarea").removeClass("is-invalid");
    $.ajax({
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      url: "{{ route('edit.ski') }}",
      data: formData,
      success: function (response) {
        document.querySelector(".action_edit").disabled=false;
        if (response.status == 'true') {
          $("#skiFormEdit")[0].reset();
          $(".select_opsi_edit").val(null).trigger('change');
          $('#modal_form_ski_edit').modal('hide');
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