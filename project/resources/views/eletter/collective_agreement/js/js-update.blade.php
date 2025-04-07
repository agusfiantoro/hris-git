<script type="text/javascript">
	$(".select_opsi_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$(".select_search_edit").select2();
	// $("#id_position_routing_chief_edit").select2();
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	function get_edit(letterID) {
		if (letterID) {
			$.getJSON("{{ url('e-letter/employee_agreement/collective_agreement/get_edit') }}"+"/"+letterID, function(response) {
				if (response.data) {
					hide_loading();
					$("#id_employee_edit").append('<option value="' + response.data.id_employee + '">'+ response.data.name +' ('+response.data.nik_employee+')'+'</option>');
					$("#id_dept_edit").append('<option value="'+response.data.id_dept+'">'+ response.data.dec_dept +'</option>');
					$("#id_region_edit").append('<option value="'+response.data.id_region+'">'+ response.data.dec_region +'</option>');
					$("#id_branch_edit").append('<option value="'+response.data.id_branch+'">'+ response.data.dec_branch +'</option>');
					$("#id_position_detail_edit").append('<option value="'+response.data.id_position_routing+'">'+ response.data.dec_position +'</option>');
					$("#remark_1_edit").append('<option value="'+response.data.remark_1+'">'+ response.data.remark_1 +'</option>');
					$("#notes_edit").val(response.data.notes);
					$("#id_letter").val(response.data.id_letter);
					$("#date_edit").val(response.data.date);
					$("#email_edit").val(response.data.email);
					// $("#id_employee_chief_edit").val(response.data.id_employee_chief).trigger('change');
					$("#id_category_edit").val(response.data.id_category).trigger('change');
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				get_edit(letterID);
			});
		}else{
			$("#modal_form_pb_edit").modal('hide');
		}
	}
	$(document).ready(function() {
		$(document).on('click','.btn-edit',function() {
			var letterID = $(this).attr('more_id');
			show_loading();
			$(".select_search_edit").empty();
			$("#pbFormEdit")[0].reset();
			$(".select_opsi_edit").val(null).trigger('change');
			$(".invalid-feedback").children("strong").text("");
			$("#pbFormEdit input").removeClass("is-invalid");
			$("#pbFormEdit select").removeClass("custom-select");
			$("#modal_form_pb_edit").modal('show');
			if (letterID) {
				get_edit(letterID);
			}
		});
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
	// 			// swal({
	// 			// 	icon: 'error',
	// 			// 	title: 'Oops...',
	// 			// 	dangerMode: true,
	// 			// 	text: 'Something went wrong! [Unknown Error]'
	// 			// });
	// 			// hide_loading();
	// 			// $("#id_employee_chief_edit").val(null).trigger('change');
	// 			change_chief_edit(employeeID);
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
		$('#pbFormEdit').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#pbFormEdit input").removeClass("is-invalid");
			$("#pbFormEdit select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('edit.pb') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#pbFormEdit")[0].reset();
						$(".select_search_edit").empty();
						$('#modal_form_pb_edit').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#pb_table').DataTable().ajax.reload();
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