<script type="text/javascript">
  function get_employee() {
    $.getJSON("{{ url('e-letter/decree/termination_letter/get_data') }}",{id_url:global_url_server}, function(response) {
      $("#id_employee").empty();
      $.each(response.employee, function(key, value_employee) {
        $("#id_employee").append('<option value="' + value_employee.id_employee + '" more_id="'+value_employee.status+'">'+ value_employee.name + ' / ' + value_employee.nik_employee +' ('+value_employee.status+')'+'</option>');
      });
      $("#id_employee").val(null).trigger('change');
    }).fail(function(jqXHR, textStatus, errorThrown) {
      get_employee();
    });
  }
  $(document).ready(function() {
    $(".new").click(function() {
      get_employee();
      $("#skpForm")[0].reset();
      $(".select_search").empty();
      $(".select_opsi").val(null).trigger('change');
      $(".invalid-feedback").children("strong").text("");
      $("#skpForm input").removeClass("is-invalid");
      $("#skpForm select").removeClass("custom-select");
      $("#skpForm textarea").removeClass("is-invalid");
      $("#modal_form_skp").modal('show');
      hide_loading();
    });
  });
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: ':. FILTER OPTION .:'
  });
  $(".select_search").select2();
  $("#id_position_routing_chief").select2();
  $('#date').datepicker({
    uiLibrary: 'bootstrap4',
    format: 'yyyy-mm-dd',
  });
  // $(document).on('change','#id_employee',function() {
  //   var employeeID = $(this).val();
  //   $("#status_employee").val('');
  //   $(".select_search").empty();
  //   $("#nik").val('');
  //   document.getElementById('filter').disabled=true;
  //   if (employeeID) {
  //     document.getElementById('filter').disabled=false;
  //     const selectElement = document.getElementById('id_employee');
  //     const selectedOption = selectElement.options[selectElement.selectedIndex];
  //     const moreId = selectedOption.getAttribute('more_id');
  //     $("#status_employee").val(`${moreId}`);
  //   }else{
  //     $(".select_search").empty();
  //     $(".value_select_search").val('');
  //   }
  // });
  // $("#filter").click(function () {
  //   var employeeID = $("#id_employee").val();
  //   var employeeStatus = $("#status_employee").val();
  //   if (employeeID) {
  //     $("#modal_view_career").modal('show');
  //     if ($.fn.DataTable.isDataTable('#table_view_career')) {
  //       $('#table_view_career').DataTable().destroy();
  //     }
  //     $('#table_view_career').DataTable({
  //       processing: true,
  //       pageLength: 10,
  //       responsive: true,
  //       autoWidth: false,
  //       ajax: {
  //         url: "{{ url('e-letter/decree/termination_letter/get_career') }}?id_employee="+employeeID+"&status_employee="+employeeStatus,
  //         error: function (jqXHR, textStatus, errorThrown) {
  //           $('#table_view_career').DataTable().ajax.reload();
  //         }
  //       },
  //       columns: [
  //       {
  //         defaultContent: '',
  //         orderable: false,
  //       },
  //       {
  //         defaultContent: '',
  //         orderable: false
  //       },
  //       { data: 'DT_RowIndex', name: 'DT_RowIndex' },
  //       { data: 'name', name: 'name' },
  //       { data: 'nik_employee', name: 'nik_employee' },
  //       { data: 'dec_position', name: 'dec_position' },
  //       { data: 'dec_dept', name: 'dec_dept' },
  //       { data: 'dec_region', name: 'dec_region' },
  //       { data: 'dec_branch', name: 'dec_branch' },
  //       { data: 'status_employee', name: 'status_employee' },
  //       {
  //         data: 'action',
  //         name: 'action',
  //         orderable: false,
  //         render: function (data, type, row) {
  //           return data;
  //         }
  //       },
  //       ]
  //     });
  //   }
  // });
  // $("#modal_view_career").on('hidden.bs.modal', function () {
  //   if ($.fn.DataTable.isDataTable('#table_view_career')) {
  //     $('#table_view_career').DataTable().destroy();
  //   }
  // });
  function change_employee(employeeID, employeeStatus) {
    if (employeeID) {
      show_loading();
      $.ajax({
        type: "GET",
        url: "{{url('e-letter/decree/termination_letter/change_career')}}"+"?id_employee="+employeeID+"&status_employee="+employeeStatus,
        success: function(response) {
          if (response) {
            hide_loading();
            $(".select_search").empty();
            $(".value_select_search").val('');
            $.each(response, function(key, value) {
              $("#id_region").append('<option value="' + value.id_region + '">'+ value.dec_region +'</option>');
              $("#id_dept").append('<option value="' + value.id_dept + '">'+ value.dec_dept +'</option>');
              $("#id_branch").append('<option value="' + value.id_branch + '">'+ value.dec_branch +'</option>');
              $("#id_position_detail").append('<option value="' + value.id_position_detail + '">'+ value.dec_position_routing +'</option>');
            });
          }
        },
        error: function(response) {
          hide_loading();
          change_employee(employeeID, employeeStatus);
          // swal({
          //   icon: 'error',
          //   title: 'Oops...',
          //   dangerMode: true,
          //   text: 'Something went wrong! [Unknown Error]'
          // });
          // hide_loading();
          // $("#id_employee").val(null).trigger('change');
        }
      });
    }else{
      $(".select_search").empty();
      $(".value_select_search").val('');
    }
  }
  $(document).on('change','#id_employee',function() {
    var employeeID = $("#id_employee").val();
    $(".select_search").empty();
    $("#nik").val('');
    $(".value_select_search").val('');
    if (employeeID) {
      const selectElement = document.getElementById('id_employee');
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const moreId = selectedOption.getAttribute('more_id');
      var employeeStatus = `${moreId}`;
      change_employee(employeeID, employeeStatus);
    }
  });
  // function change_chief(employeeID) {
  //   if (employeeID) {
  //     show_loading();
  //     $.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
  //       if (response) {
  //         hide_loading();
  //         $("#id_position_routing_chief").empty();
  //         $("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
  //       } else {
  //         $("#id_position_routing_chief").empty();
  //       }
  //     }).fail(function(jqXHR, textStatus, errorThrown) {
  //       change_chief(employeeID);
  //       // swal({
  //       //   icon: 'error',
  //       //   title: 'Oops...',
  //       //   dangerMode: true,
  //       //   text: 'Something went wrong! [Unknown Error]'
  //       // });
  //       // hide_loading();
  //       // $("#id_employee_chief").val(null).trigger('change');
  //     });
  //   } else {
  //     $("#id_position_routing_chief").empty();
  //   }
  // }
  // $(document).on('change','#id_employee_chief',function() {
  //   var employeeID = $(this).val();
  //   $("#id_position_routing_chief").empty();
  //   // $("#id_position_routing_chief").append('<option value=""><span class="sr-only">Loading...</span></option>');
  //   if (employeeID) {
  //     change_chief(employeeID);
  //   }
  // });
  $(function () {
    $('#skpForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#skpForm input").removeClass("is-invalid");
      $("#skpForm select").removeClass("custom-select");
      $("#skpForm textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url: "{{ route('save.skp') }}",
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#skpForm")[0].reset();
            $(".select_search").empty();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_skp').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#skp_table').DataTable().ajax.reload();
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