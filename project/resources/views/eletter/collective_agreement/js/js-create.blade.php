<script type="text/javascript">
	$(document).ready(function() {
		$(".new").click(function() {
			$("#pbForm")[0].reset();
			$(".select_search").empty();
			$(".select_opsi").val(null).trigger('change');
			$(".invalid-feedback").children("strong").text("");
			$("#pbForm input").removeClass("is-invalid");
			$("#pbForm select").removeClass("custom-select");
			$("#remark_1").prop('checked',false).trigger('change');
			$("#modal_form_pb").modal('show');
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
	$(document).on('change','#remark_1',function() {
		var remark_1 = $(this).val();
		if (remark_1) {
			$(".select_search").empty();
			document.getElementById('filter').disabled=false;
		}else{
			$(".select_search").empty();
			document.getElementById('filter').disabled=true;
		}
	});
	$(document).ready(function() {
		$("#filter").click(function () {
			const moreStatus = $("#remark_1").val();
			if (moreStatus) {
				$("#modal_view_employee").modal('show');
				if ($.fn.DataTable.isDataTable('#table_view_employee')) {
					$('#table_view_employee').DataTable().destroy();
				}
				$('#table_view_employee').DataTable({
					processing: true,
					pageLength: 10,
					responsive: true,
					autoWidth: false,
					ajax: {
						url: "{{ url('e-letter/employee_agreement/collective_agreement/get_employee') }}",
						data : {id_url:global_url_server,status:moreStatus},
						error: function (jqXHR, textStatus, errorThrown) {
							$('#table_view_employee').DataTable().ajax.reload();
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
					{ data: 'name', name: 'name' },
					{ data: 'nik_employee', name: 'nik_employee' },
					{ data: 'dec_position', name: 'dec_position' },
					{ data: 'join_date', name: 'join_date' },
					{ 
						data: 'resign_date', 
						name: 'resign_date',
						render: function (data, type, row) {
							if (data === null) {
								return '-';
							} else {
								return data;
							}
						}
					},
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
				alert('Select Status');
			}
		});
	});
	$("#modal_view_employee").on('hidden.bs.modal', function () {
		if ($.fn.DataTable.isDataTable('#table_view_employee')) {
			$('#table_view_employee').DataTable().destroy();
		}
	});
	function change_employee(employeeStatus, employeeID) {
		if (employeeID) {
			$("#modal_view_employee").modal('hide');
			$("#id_employee").empty();
			$(".select_search").empty();
			show_loading();
			$.ajax({
				type: "GET",
				url: "{{url('e-letter/employee_agreement/collective_agreement/change_employee')}}",
				data: {employee_status:employeeStatus, employee_id:employeeID},
				success: function(response) {
					if (response) {
						hide_loading();
						$.each(response, function(key, value) {
							$("#id_employee").append('<option value="' + value.id_employee + '">'+ value.name +' ('+value.nik_employee+')'+'</option>');
							$("#id_position_detail").append('<option value="'+value.id_routing+'">'+value.dec_position+'</option>');
							$("#id_region").append('<option value="'+value.id_region+'">'+value.dec_region+'</option>');
							$("#id_branch").append('<option value="'+value.id_branch+'">'+value.dec_branch+'</option>');
							$("#id_dept").append('<option value="'+value.id_dept+'">'+value.dec_dept+'</option>');
						});
					}else{
						$("#id_employee").empty();
					}
				},
				error: function(response) {
					hide_loading();
					change_employee(employeeStatus, employeeID);
					// swal({
					// 	icon: 'error',
					// 	title: 'Oops...',
					// 	dangerMode: true,
					// 	text: 'Something went wrong! [Unknown Error]'
					// });
					// $("#id_employee").val(null).trigger('change');
				}
			});
		}else{
			$("#id_employee").empty();
		}
	}
	$(document).on('click','.choose_employee',function() {
		var employeeStatus = $(this).attr('more_status');
		var employeeID = $(this).attr('more_id');
		if (employeeID) {
			change_employee(employeeStatus, employeeID);
		}
	});
	// function change_position_chief(employeeID) {
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
	// 			// swal({
	// 			// 	icon: 'error',
	// 			// 	title: 'Oops...',
	// 			// 	dangerMode: true,
	// 			// 	text: 'Something went wrong! [Unknown Error]'
	// 			// });
	// 			// hide_loading();
	// 			// $("#id_employee_chief").val(null).trigger('change');
	// 			hide_loading();
	// 			change_position_chief(employeeID);
	// 		});
	// 	} else {
	// 		$("#id_position_routing_chief").empty();
	// 	}
	// }
	// $(document).on('change','#id_employee_chief',function() {
	// 	var employeeID = $(this).val();
	// 	$("#id_position_routing_chief").empty();
	// 	if (employeeID) {
	// 		change_position_chief(employeeID);
	// 	}		
	// });
	$(function () {
		$('#pbForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#pbForm input").removeClass("is-invalid");
			$("#pbForm select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('save.pb') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#pbForm")[0].reset();
						$(".select_search").empty();
						$(".select_opsi").val(null).trigger('change');
						$('#modal_form_pb').modal('hide');
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