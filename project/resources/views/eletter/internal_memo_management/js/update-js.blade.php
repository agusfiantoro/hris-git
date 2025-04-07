<script type="text/javascript">
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$(".select_search_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$(".select_disabled_edit").select2({
		disabled : true
	});
	// $("#id_position_routing_chief_edit").select2();
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	function get_edit(id_letter) {
		$.ajax({
			type: "GET",
			url: "{{url('e-letter/internal_memo/internal_memo_management/get_edit')}}"+"/"+id_letter,
			success: function(response) {
				if (response.data) {
					$.each(response.data, function(key, value) {
						sessionStorage.setItem('id_branch', value.id_branch);
						$("#id_letter").val(id_letter);
						$("#date_edit").val(value.date);
						$("#email_edit").val(value.email);
						$("#id_region_edit").append('<option value="'+value.id_region+'">'+value.dec_region+'</option>').val(value.id_region).trigger('change');
						$("#id_dept_edit").val(value.id_dept).trigger('change');
						// $("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
						$("#keterangan_edit").val(value.notes);
					});
				}
			},
			error: function(response) {
				get_edit(id_letter);
			}
		});
	}
	$(document).on('click', '.btn-edit', function() {
		var id_letter = $(this).attr('more_id');
		show_loading();
		$("#immFormEdit")[0].reset();
		$("#id_region_edit").empty();
		$(".select_search_edit").val(null).trigger('change');
		$(".select_disabled_edit").val(null).trigger('change');
		$(".invalid-feedback").children("strong").text("");
		$("#immFormEdit input").removeClass("is-invalid");
		$("#immFormEdit select").removeClass("custom-select");
		$("#immFormEdit textarea").removeClass("is-invalid");
		$("#modal_form_imm_edit").modal('show');
		if (id_letter) {
			get_edit(id_letter);
		}else{
			$("#modal_form_imm_edit").modal('hide');
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
	function change_region_edit(regionID) {
		if (regionID) {
			$.getJSON("{{ url('e-letter/internal_memo_management/get_region') }}"+"?region_id="+regionID,{id_url:global_url_server}, function(response) {
				if (response) {
					hide_loading();
					$("#id_branch_edit").empty();
					$.each(response, function(key, value_branch) {
						$("#id_branch_edit").append('<option value="'+value_branch.id_branch+'">' + value_branch.description +
							'</option>');
					});
					$("#id_branch_edit").val(sessionStorage.getItem('id_branch')).trigger('change');
				} else {
					$("#id_branch_edit").empty();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				change_region_edit(regionID);
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// $("#modal_form_imm_edit").modal('hide');
			});
		} else {
			$("#id_branch_edit").empty();
		}
	}
	$(document).on('change','#id_region_edit',function() {
		var regionID = $(this).val();
		$("#id_branch_edit").empty();
		$("#id_branch_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
		if (regionID) {
			change_region_edit(regionID);
		}
	});
	$(function () {
		$('#immFormEdit').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#immFormEdit input").removeClass("is-invalid");
			$("#immFormEdit select").removeClass("custom-select");
			$("#immFormEdit textarea").removeClass("is-invalid");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('edit.imm') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#immFormEdit")[0].reset();
						$(".select_search_edit").val(null).trigger('change');
						$('#modal_form_imm_edit').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						// window.location.reload();
						$('#imm_table').DataTable().ajax.reload();
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