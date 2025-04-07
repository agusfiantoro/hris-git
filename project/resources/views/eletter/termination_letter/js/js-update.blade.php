<script type="text/javascript">
	
	$(".select_opsi_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$(".select_search_edit").select2({
		// allowClear: true,
		// placeholder: ':. FILTER OPTION .:',
		disabled : true
	});
	$("#id_position_routing_chief_edit").select2();
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd'
	});
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	function get_edit(letterID) {
		if (letterID) {
			$.getJSON("{{ url('e-letter/decree/termination_letter/get_edit') }}"+"/"+letterID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						$("#id_category_edit").val(value.id_category).trigger('change');
						$("#date_edit").val(value.date);
						$("#id_letter").val(letterID);
						$("#email_edit").val(value.email);
						$("#notes_edit").val(value.notes);
						$("#id_employee_edit").append('<option value="' + value.name + '">'+ value.name + ' / ' + value.nik_employee +' ('+value.status+')'+'</option>');
						$("#id_company_edit").append('<option value="' + value.company_name + '">'+ value.company_name +'</option>');
						$("#id_region_edit").append('<option value="' + value.dec_region + '">'+ value.dec_region +'</option>');
						$("#id_branch_edit").append('<option value="' + value.dec_branch + '">'+ value.dec_branch +'</option>');
						$("#id_dept_edit").append('<option value="' + value.dec_dept + '">'+ value.dec_dept +'</option>');
						$("#id_position_detail_edit").append('<option value="' + value.dec_position + '">'+ value.dec_position +'</option>');
						$("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
					});
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				get_edit(letterID);
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// $("#modal_form_skp_edit").modal('hide');
			});
		}else{
			$("#modal_form_skp_edit").modal('hide');
		}
	}
	$(document).on('click','.btn-edit',function() {
		show_loading();
		var letterID = $(this).attr('more_id');
		$("#skpFormEdit")[0].reset();
		$(".select_search_edit").empty();
		$(".select_opsi_edit").val(null).trigger('change');
		$(".invalid-feedback").children("strong").text("");
		$("#skpFormEdit input").removeClass("is-invalid");
		$("#skpFormEdit select").removeClass("custom-select");
		$("#skpFormEdit textarea").removeClass("is-invalid");
		$("#modal_form_skp_edit").modal('show');
		if (letterID) {
			get_edit(letterID);
		}
	});
	// function change_chief_edit(employeeID) {
	// 	if (employeeID) {
	// 		show_loading();
	// 		$.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
	// 			if (response) {
	// 				hide_loading();
	// 				$("#id_position_routing_chief_edit").empty();
	// 				$("#id_position_routing_chief_edit").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
	// 			} else {
	// 				$("#id_position_routing_chief_edit").empty();
	// 			}
	// 		}).fail(function(jqXHR, textStatus, errorThrown) {
	// 			change_chief_edit(employeeID);
	// 			// swal({
	// 			// 	icon: 'error',
	// 			// 	title: 'Oops...',
	// 			// 	dangerMode: true,
	// 			// 	text: 'Something went wrong! [Unknown Error]'
	// 			// });
	// 			// hide_loading();
	// 			// $("#id_employee_chief_edit").val(null).trigger('change');
	// 		});
	// 	} else {
	// 		$("#id_position_routing_chief_edit").empty();
	// 	}
	// }
	// $(document).on('change','#id_employee_chief_edit',function() {
	// 	var employeeID = $(this).val();
	// 	$("#id_position_routing_chief_edit").empty();
	// 	// $("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
	// 	if (employeeID) {
	// 		change_chief_edit(employeeID);
	// 	}
	// });
	$(function () {
		$('#skpFormEdit').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#skpFormEdit input").removeClass("is-invalid");
			$("#skpFormEdit select").removeClass("custom-select");
			$("#skpFormEdit textarea").removeClass("is-invalid");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('edit.skp') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#skpFormEdit")[0].reset();
						$(".select_search_edit").empty();
						$(".select_opsi_edit").val(null).trigger('change');
						$('#modal_form_skp_edit').modal('hide');
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