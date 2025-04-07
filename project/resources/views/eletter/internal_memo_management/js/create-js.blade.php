<script type="text/javascript">
	$(".new").click(function() {
		$("#immForm")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$("#immForm input").removeClass("is-invalid");
		$("#immForm select").removeClass("custom-select");
		$("#immForm textarea").removeClass("is-invalid");
		$(".select_search").val(null).trigger('change');
		$("#modal_form_imm").modal('show');
		hide_loading();
	});
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd'
	});
	$(".select_search").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$("#id_position_routing_chief").select2();
	function change_region(regionID) {
		if (regionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/internal_memo_management/get_region') }}"+"?region_id="+regionID,{id_url:global_url_server}, function(response) {
				if (response) {
					hide_loading();
					$("#id_branch").empty();
					$.each(response, function(key, value_branch) {
						$("#id_branch").append('<option value="'+value_branch.id_branch+'">' + value_branch.description +
							'</option>');
					});
				} else {
					$("#id_branch").empty();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				change_region(regionID);
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// hide_loading();
				// $("#id_region").val(null).trigger('change');
			});
		} else {
			$("#id_branch").empty();
		}
	}
	$(document).on('change','#id_region',function() {
		var regionID = $(this).val();
		$("#id_branch").empty();
		// $("#id_branch").append('<option value=""><span class="sr-only">Loading...</span></option>');
		if (regionID) {
			change_region(regionID);
		}
	});
	// function change_chief(employeeID) {
	// 	if (employeeID) {
	// 		show_loading();
	// 		$.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
	// 			if (response) {
	// 				hide_loading();
	// 				$("#id_position_routing_chief").empty();
	// 				$("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
	// 			} else {
	// 				$("#id_position_routing_chief").empty();
	// 			}
	// 		}).fail(function(jqXHR, textStatus, errorThrown) {
	// 			change_chief(employeeID);
	// 			// swal({
	// 			// 	icon: 'error',
	// 			// 	title: 'Oops...',
	// 			// 	dangerMode: true,
	// 			// 	text: 'Something went wrong! [Unknown Error]'
	// 			// });
	// 			// hide_loading();
	// 			// $("#id_employee_chief").val(null).trigger('change');
	// 		});
	// 	} else {
	// 		$("#id_position_routing_chief").empty();
	// 	}
	// }
	// $(document).on('change','#id_employee_chief',function() {
	// 	var employeeID = $(this).val();
	// 	$("#id_position_routing_chief").empty();
	// 	// $("#id_position_routing_chief").append('<option value=""><span class="sr-only">Loading...</span></option>');
	// 	if (employeeID) {
	// 		change_chief(employeeID);
	// 	}
	// });
	$(function () {
		$('#immForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#immForm input").removeClass("is-invalid");
			$("#immForm select").removeClass("custom-select");
			$("#immForm textarea").removeClass("is-invalid");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('save.imm') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#immForm")[0].reset();
						$(".select_search").val(null).trigger('change');
						$('#modal_form_imm').modal('hide');
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