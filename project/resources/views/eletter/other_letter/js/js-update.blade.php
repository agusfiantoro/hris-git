<script type="text/javascript">
  $(".select_opsi_edit").select2({
    // allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $(".select_search_edit").select2();
  $("#id_position_routing_chief_edit").select2();
  $("#id_position_routing_chief_view_edit").select2();
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
  function get_edit(letterID) {
    if (letterID) {
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/company_letter/other_letter/get_edit')}}"+"/"+letterID,
        success: function(response) {
          if (response) {
            hide_loading();
            $.each(response, function(key, value) {
              $("#id_category_edit").val(value.id_category).trigger('change');
              $("#date_edit").val(value.date);
              $("#id_letter").val(value.id_letter);
              $("#expired_date_edit").val(value.expired_date);
              $("#effective_date_edit").val(value.effective_date);
              $("#notes_edit").val(value.notes);
              $("#email_edit").val(value.email);
              $("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
              if (value.name == null) {
                var nameEmployee = value.name !== null ? value.name : '-';
              }else{
                var nameEmployee = value.name +' ('+value.nik_employee+')';
              }
              if (value.remark_3 != null) {
                $("#remark_3_edit").prop('checked',true);
              }
              if (value.remark_4 != null) {
                $("#remark_4_edit").prop('checked',true);
              }
              if (value.remark_5 != null) {
                $("#remark_5_edit").prop('checked',true);
              }
              $("#id_employee_edit").append('<option value="' + nameEmployee + '">'+ nameEmployee +'</option>');
              var decRegion = value.dec_region !== null ? value.dec_region : '-';
              var decBranch = value.dec_branch !== null ? value.dec_branch : '-';
              var decPosition = value.dec_position !== null ? value.dec_position : '-';
              var decDept = value.dec_dept !== null ? value.dec_dept : '-';
              var decLocation = value.dec_location !== null ? value.dec_location : '-';
              var decDivisi = value.id_principal !== null ? value.id_principal : '-';
              var decGrade = value.dec_job_grade !== null ? value.dec_job_grade : '-';
              var decStatus = value.status !== null ? value.status : '-';
              $("#id_dept_edit").append('<option value="' + decDept + '">'+ decDept +'</option>');
              $("#id_region_edit").append('<option value="' + decRegion + '">'+ decRegion +'</option>');
              $("#id_branch_edit").append('<option value="' + decBranch + '">'+ decBranch +'</option>');
              $("#id_position_detail_edit").append('<option value="' + decPosition + '">'+ decPosition +'</option>');
              $("#id_job_grade_edit").append('<option value="' + decGrade + '">'+ decGrade +'</option>');
              $("#id_principal_edit").append('<option selected value="' + decDivisi + '">'+ decDivisi +'</option>');
              $("#id_employment_status_edit").append('<option value="' + decStatus + '">'+ decStatus +'</option>');
              $("#remark_1_edit").val(value.remark_1);
              $("#remark_2_edit").val(value.remark_2);
            });
          }
        },
        error: function(response) {
          get_edit(letterID);
          // swal({
          //   icon: 'error',
          //   title: 'Oops...',
          //   dangerMode: true,
          //   text: 'Something went wrong! [Unknown Error]'
          // });
          // $("#modal_form_other_edit").modal('hide');
        }
      });
    }
  }
  $(document).on('click','.btn-edit',function() {
    var letterID = $(this).attr('more_id');
    $("#otherFormEdit")[0].reset();
    $(".select_opsi_edit").val(null).trigger('change');
    $(".select_search_edit").empty();
    $(".invalid-feedback").children("strong").text("");
    $("#otherFormEdit input").removeClass("is-invalid");
    $("#otherFormEdit select").removeClass("custom-select");
    $("#otherFormEdit textarea").removeClass("is-invalid");
    $("#modal_form_other_edit").modal('show');
    show_loading();
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
    if (employeeID) {
      change_chief_edit(employeeID);
    }
  });
  $(function () {
    $('#otherFormEdit').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action_edit").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#otherFormEdit input").removeClass("is-invalid");
      $("#otherFormEdit select").removeClass("custom-select");
      $("#otherFormEdit textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('edit.other') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action_edit").disabled=false;
          if (response.status == 'true') {
            $("#status").val(null).trigger('change');
            $("#otherFormEdit")[0].reset();
            $('#modal_form_other_edit').modal('hide');
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