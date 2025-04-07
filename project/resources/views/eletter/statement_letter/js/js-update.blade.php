<script type="text/javascript">
  $(".select_opsi_edit").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $('#date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $("#date_edit").parent().children('span').children('button').attr('disabled',true);
  $('#effective_date_edit').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  $(".select_search_old_edit").select2({
    disabled : true
  });
  $(".select_search_new_edit").select2({
    disabled : true
  });
  $("#id_position_routing_chief_edit").select2();
  function get_edit(letterID) {
    if (letterID) {
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/decree/statement_letter/get_edit')}}"+"/"+letterID,
        success: function(response) {
          if (response.data) {
            $("#id_employee_chief_edit").val(response.data.id_employee_chief).trigger('change');
            $("#remark_1_edit").val(response.data.remark_1).trigger('change');
            $("#remark_8_edit").val(response.data.remark_8);
            $("#remark_9_edit").val(response.data.remark_9);
            $("#email_edit").val(response.data.email);
            $("#notes_edit").val(response.data.notes);
            $("#date_edit").val(response.data.date);
            $("#id_letter").val(response.data.id_letter);
            $("#effective_date_edit").val(response.data.effective_date);
            $("#id_category_edit").append('<option value="' + response.data.category + '">'+ response.data.category +'</option>');
            $("#id_career_transaction_edit").append('<option value="' + response.data.reference_number_career + '">'+ response.data.reference_number_career +'</option>');
            $("#id_employment_status_edit_view").append('<option value="' + response.data.status + '">'+ response.data.status +'</option>');
            $("#id_employment_status_edit").val(response.data.id_employment_status);
            $("#id_employee_edit").append('<option value="' + response.data.name + '">'+ response.data.name + ' (' + response.data.nik_employee +')'+'</option>');
            $("#id_category_edit").append('<option value="' + response.data.category + '">'+ response.data.category +'</option>');
            $("#id_job_grade_view_edit").append('<option value="' + response.data.dec_job_grade + '">'+ response.data.dec_job_grade +'</option>');
            var decRegionOld = response.data.dec_region_old !== null ? response.data.dec_region_old : '-';
            var decBranchOld = response.data.dec_branch_old !== null ? response.data.dec_branch_old : '-';
            var decPositionOld = response.data.dec_position_old !== null ? response.data.dec_position_old : '-';
            var decDeptOld = response.data.dec_dept_old !== null ? response.data.dec_dept_old : '-';
            var decLocationOld = response.data.dec_location_old !== null ? response.data.dec_location_old : '-';
            var decPrincipalOld = response.data.id_principal !== null ? response.data.id_principal : '-';
            $("#id_region_old_edit").append('<option value="' + decRegionOld + '">'+ decRegionOld +'</option>');
            $("#id_branch_old_edit").append('<option value="' + decBranchOld + '">'+ decBranchOld +'</option>');
            $("#id_position_detail_old_edit").append('<option value="' + decPositionOld + '">'+ decPositionOld +'</option>');
            $("#id_dept_old_edit").append('<option value="' + decDeptOld + '">'+ decDeptOld +'</option>');
            $("#id_location_old_edit").append('<option value="'+decLocationOld+'">'+ decLocationOld +'</option>');
            $("#id_principal_old_edit").append('<option selected value="'+decPrincipalOld+'">'+ decPrincipalOld +'</option>');
            var decRegionNew = response.data.dec_region_new !== null ? response.data.dec_region_new : '-';
            var decBranchNew = response.data.dec_branch_new !== null ? response.data.dec_branch_new : '-';
            var decPositionNew = response.data.dec_position_new !== null ? response.data.dec_position_new : '-';
            var decDeptNew = response.data.dec_dept_new !== null ? response.data.dec_dept_new : '-';
            var decLocationNew = response.data.dec_location_new !== null ? response.data.dec_location_new : '-';
            $("#id_region_new_view_edit").append('<option value="' + decRegionNew + '">'+ decRegionNew +'</option>');
            $("#id_branch_new_view_edit").append('<option value="' + decBranchNew + '">'+ decBranchNew +'</option>');
            $("#id_position_detail_new_view_edit").append('<option value="' + decPositionNew + '">'+ decPositionNew +'</option>');
            $("#id_dept_new_view_edit").append('<option value="' + decDeptNew + '">'+ decDeptNew +'</option>');
            $("#id_location_new_view_edit").append('<option value="'+decLocationNew+'">'+ decLocationNew +'</option>');
            $.each(response.principal, function(key, value_principal) {
              var decDivisionNew = value_principal.dec_division_new !== null ? value_principal.dec_division_new : '-';
              $("#id_principal_new_view_edit").append('<option selected value="'+decDivisionNew+'">'+ decDivisionNew +'</option>');
            });
          }
        },
        error: function(response) {
          get_edit(letterID);
        }
      });
}else{
  $("#modal_form_sk_edit").modal('hide');
}
}
$(document).on('click','.btn-edit',function() {
  show_loading();
  $("#skFormEdit")[0].reset();
  $(".select_search_old_edit").empty();
  $(".select_search_new_edit").empty();
  $(".select_opsi_edit").val(null).trigger('change');
  $(".invalid-feedback").children("strong").text("");
  $("#skFormEdit input").removeClass("is-invalid");
  $("#skFormEdit select").removeClass("custom-select");
  $("#skFormEdit textarea").removeClass("is-invalid");
  $("#modal_form_sk_edit").modal('show');
  var letterID = $(this).attr('more_id');
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
      hide_loading();
      change_chief_edit(employeeID);
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
  $('#skFormEdit').submit(function (e) {
    e.preventDefault();
    document.querySelector(".action_edit").disabled=true;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#skFormEdit input").removeClass("is-invalid");
    $("#skFormEdit select").removeClass("custom-select");
    $("#skFormEdit textarea").removeClass("is-invalid");
    $.ajax({
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      url: "{{ route('edit.sk') }}",
      data: formData,
      success: function (response) {
        document.querySelector(".action_edit").disabled=false;
        if (response.status == 'true') {
          $("#skFormEdit")[0].reset();
          $(".select_search_old_edit").empty();
          $(".select_search_new_edit").empty();
          $(".select_opsi_edit").val(null).trigger('change');
          $('#modal_form_sk_edit').modal('hide');
          swal({
            icon: 'success',
            title: 'Success',
            text: response.message
          });
          $('#sk_table').DataTable().ajax.reload();
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