<script type="text/javascript">
  function get_employee() {
   $.getJSON("{{ url('e-letter/decree/statement_letter/get_data') }}",{id_url:global_url_server}, function(response) {
    $("#id_employee").empty();
    $.each(response.employee, function(key, value_employee) {
      $("#id_employee").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +' ('+ value_employee.nik_employee+')'+'</option>');
    });
    $("#id_employee").val(null).trigger('change');
  }).fail(function(jqXHR, textStatus, errorThrown) {
   get_employee();
 });
}
$(document).ready(function() {
  $(".new").click(function() {
   get_employee();
   $("#skForm")[0].reset();
   $(".select_search_old").empty();
   $(".select_search_new").empty();
   $(".select_opsi").val(null).trigger('change');
   $(".invalid-feedback").children("strong").text("");
   $("#skForm input").removeClass("is-invalid");
   $("#skForm select").removeClass("custom-select");
   $("#skForm textarea").removeClass("is-invalid");
   $("#modal_form_sk").modal('show');
   hide_loading();
 });
});
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
$(".select_search_old").select2();
$(".select_search_new").select2();
$("#id_position_routing_chief").select2();
$(document).on('change','#id_career_transaction',function() {
  var idCareer = $(this).val();
  if (idCareer) {
  }else{
    $(".select_search_old").empty();
    $(".select_search_new").empty();
    $("#id_principal_new").empty();
    $("#id_career_transaction").empty();
  }
});
$(document).on('change','#id_employee',function() {
  var employeeID = $(this).val();
  document.getElementById('filter').disabled=true;
  $(".select_search_old").empty();
  $(".select_search_new").empty();
  if (employeeID) {
    document.getElementById('filter').disabled=false;
  }else{
    $("#id_career_transaction").empty();
    $(".select_search_old").empty();
    $(".select_search_new").empty();
    $("#id_principal_new").empty();
    document.getElementById('filter').disabled=true;
  }
});
$("#filter").click(function () {
  var employeeID = $("#id_employee").val();
  var categoryID = $("#id_category").val();
  if (employeeID && categoryID) {
    const selectElement = document.getElementById('id_category');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const moreCategory = selectedOption.getAttribute('data-attribute');
    $("#modal_view_career").modal('show');
    if ($.fn.DataTable.isDataTable('#table_view_career')) {
      $('#table_view_career').DataTable().destroy();
    }
    $('#table_view_career').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      autoWidth: false,
      ajax: {
        url: "{{ url('e-letter/decree/statement_letter/get_career') }}?employee_id="+employeeID+"&category_type="+moreCategory,
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#table_view_career').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex' },
      { data: 'reference_number', name: 'reference_number' },
      { data: 'name', name: 'name' },
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'type', name: 'type' },
      { 
        data: 'dec_position_old', name: 'dec_position_old', 
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      { 
        data: 'dec_branch_old', name: 'dec_branch_old', 
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      {
        data: 'dec_job_grade_old', name: 'dec_job_grade_old',
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      { 
        data: 'dec_position_new', name: 'dec_position_new',
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      { 
        data: 'dec_branch_new', name: 'dec_branch_new',
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      {
        data: 'dec_job_grade_new', name: 'dec_job_grade_new',
        render: function (data, type, row) {
          return data !== null ? data : '-';
        }
      },
      { data: 'status', name: 'status' },
      {
        data: 'action',
        name: 'action',
        orderable: false,
        render: function (data, type, row) {
          return data;
        }
      },
      ]
    });
  }else{
    alert('Select Category and Employee');
  }
});
$("#modal_view_career").on('hidden.bs.modal', function () {
  if ($.fn.DataTable.isDataTable('#table_view_career')) {
    $('#table_view_career').DataTable().destroy();
  }
});
function change_employee(careerID) {
  if (careerID) {
    show_loading();
    $.ajax({
      type: "GET",
      url: "{{url('e-letter/decree/statement_letter/change_career')}}"+"?id_career_transaction="+careerID,
      success: function(response) {
        if (response) {
          hide_loading();
          $("#id_career_transaction").empty();
          $(".select_search_old").empty();
          $(".select_search_new").empty();
          $.each(response, function(key, value) {
            if (value.dec_job_grade_old == null)
            {
              var idJobGrade = value.id_job_grade_new;
              var decJobGrade = value.dec_job_grade_new;
            }
            else if(value.dec_job_grade_new == null)
            {
             var idJobGrade = value.id_job_grade_old;
             var decJobGrade = value.dec_job_grade_old;
           }
           else if(value.dec_job_grade_old != null && value.dec_job_grade_new != null)
           {
            var idJobGrade = value.id_job_grade_new;
            var decJobGrade = value.dec_job_grade_new;
          }
          $("#id_career_transaction").append('<option value="' + value.id_career_transaction + '">'+ value.reference_number +'</option>');
          $("#id_job_grade").append('<option value="' + idJobGrade + '">'+ decJobGrade +'</option>');
          $("#id_employment_status").append('<option value="' + value.id_employment_status + '">'+ value.status +'</option>');
          var decRegionOld = value.dec_region_old !== null ? value.dec_region_old : '-';
          var decBranchOld = value.dec_branch_old !== null ? value.dec_branch_old : '-';
          var decPositionOld = value.dec_position_old !== null ? value.dec_position_old : '-';
          var decDeptOld = value.dec_dept_old !== null ? value.dec_dept_old : '-';
          var decLocationOld = value.dec_location_old !== null ? value.dec_location_old : '-';
          var decDivisiOld = value.dec_divisi_old !== null ? value.dec_divisi_old : '-';
          $("#id_region_old").append('<option value="' + value.id_region_old + '">'+ decRegionOld +'</option>');
          $("#id_branch_old").append('<option value="' + value.id_branch_old + '">'+ decBranchOld +'</option>');
          $("#id_position_detail_old").append('<option value="' + value.id_position_detail_old + '">'+ decPositionOld +'</option>');
          $("#id_dept_old").append('<option value="' + value.id_dept_old + '">'+ decDeptOld +'</option>');
          $("#id_location_old").append('<option value="'+value.id_location_old+'">'+ decLocationOld +'</option>');
          $("#id_principal_old").append('<option selected value="' + decDivisiOld + '">'+ decDivisiOld +'</option>');
          var decRegionNew = value.dec_region_new !== null ? value.dec_region_new : '-';
          var decBranchNew = value.dec_branch_new !== null ? value.dec_branch_new : '-';
          var decPositionNew = value.dec_position_new !== null ? value.dec_position_new : '-';
          var decDeptNew = value.dec_dept_new !== null ? value.dec_dept_new : '-';
          var decLocationNew = value.dec_location_new !== null ? value.dec_location_new : '-';
          var decDivisiNew = value.dec_divisi_new !== null ? value.dec_divisi_new : '-';
          $("#id_region_new").append('<option value="'+value.id_region_new+'">'+ decRegionNew +'</option>');
          $("#id_branch_new").append('<option value="'+value.id_branch_new+'">'+ decBranchNew +'</option>');
          $("#id_position_detail_new").append('<option value="'+value.id_position_detail_new+'">'+ decPositionNew +'</option>');
          $("#id_dept_new").append('<option value="'+value.id_dept_new+'">'+ decDeptNew +'</option>');
          $("#id_location_new").append('<option value="'+value.id_location_new+'">'+ decLocationNew +'</option>');
          $("#id_principal_new").append('<option selected value="'+value.id_division_new+'">'+ decDivisiNew +'</option>');
        });
        }
      },
      error: function(response) {
        change_employee(careerID);
      }
    });
  }
}
$(document).on('click','.choose_career',function() {
  var careerID = $(this).attr('more_id');
  $(".select_search_old").empty();
  $(".select_search_new").empty();
  $("#id_principal_new").empty();
  $("#modal_view_career").modal('hide');
  $("#id_career_transaction").empty();
  if (careerID) {
    change_employee(careerID);
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
      hide_loading();
      change_chief(employeeID);
      // swal({
      //   icon: 'error',
      //   title: 'Oops...',
      //   dangerMode: true,
      //   text: 'Something went wrong! [' + errorThrown + ']'
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
  $('#skForm').submit(function (e) {
    e.preventDefault();
    document.querySelector(".action").disabled=true;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#skForm input").removeClass("is-invalid");
    $("#skForm select").removeClass("custom-select");
    $("#skForm textarea").removeClass("is-invalid");
    $.ajax({
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      url: "{{ route('save.sk') }}",
      data: formData,
      success: function (response) {
        document.querySelector(".action").disabled=false;
        if (response.status == 'true') {
          $("#skForm")[0].reset();
          $(".select_search_old").empty();
          $(".select_search_new").empty();
          $(".select_opsi").val(null).trigger('change');
          $('#modal_form_sk').modal('hide');
          $("#id_principal_new").empty();
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