<script type="text/javascript">
	$(".select_search").select2();
	$(".select_opsi").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$("#id_position_routing_chief").select2();
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#effective_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#expired_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#name_input').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());
	});
	var ajaxUrl = "";
	$("#effective_date").parent().children('span').children('button').attr('disabled',true);
	$("#expired_date").parent().children('span').children('button').attr('disabled',true);
	$(".new").click(function() {
		$(".select_search").empty();
		$("#skkForm")[0].reset();
		$("#date").attr('disabled',false);
		$("#date").parent().children('span').children('button').attr('disabled',false);
		$("#no_name").prop('checked',false).trigger('change');
		$("#no_name").attr('disabled',true);
		$(".select_opsi").val(null).trigger('change');
		$("#id_region").val(null).trigger('change');
		$("#id_category").val(null).trigger('change');
		$("#id_category").attr('readonly',false);
		$("#id_category").select2({
			allowClear: true,
			placeholder: ':. FILTER OPTION .:'
		});
		ajaxUrl="{{route('save.skk')}}";
		$(".invalid-feedback").children("strong").text("");
		$("#skkForm input").removeClass("is-invalid");
		$("#skkForm select").removeClass("custom-select");
		$("#skkForm textarea").removeClass("is-invalid");
		$("#modal_form_skk").modal('show');
		hide_loading();
	});
	// $(document).ready(function() {
	// 	var no_name = $("#")
	// })
	$(document).on('change','#no_name',function() {
		var no_name = $(this).val();
		if (no_name) {
			// $(".select_search").empty();
			$("#id_region").val(null).trigger('change');
			if ($(this).prop('checked')) {
				$("#filter").attr('disabled',true);
				$('#id_employee').attr('hidden',true);
				$('#name_input').attr('hidden',false);
				$("#id_region").attr('readonly',false);
				$("#id_branch").attr('hidden',true);
				$("#id_position_detail").attr('hidden',true);
				$("#id_dept").attr('hidden',true);
				$("#id_job_grade").attr('hidden',true);
				$("#id_principal").attr('hidden',true);
				$("#id_employment_status").attr('hidden',true);
				// input
				$("#branch_input").attr('hidden',false);
				$("#position_detail_input").attr('hidden',false);
				$("#dept_input").attr('hidden',false);
				$("#job_grade_input").attr('hidden',false);
				$("#principal_input").attr('hidden',false);
				$("#status_input").attr('hidden',false);
				$("#effective_date").attr('readonly',false);
				$("#expired_date").attr('readonly',false);
				$("#effective_date").parent().children('span').children('button').attr('disabled',false);
				$("#expired_date").parent().children('span').children('button').attr('disabled',false);
				// $("#validate_exp").html('');
				// $("#validate_effective").html('');
			}else{
				$("#filter").attr('disabled',false);
				$('#id_employee').attr('hidden',false);
				$('#name_input').attr('hidden',true);
				$("#id_region").attr('readonly',true);
				$("#id_branch").attr('hidden',false);
				$("#id_position_detail").attr('hidden',false);
				$("#id_dept").attr('hidden',false);
				$("#id_job_grade").attr('hidden',false);
				$("#id_principal").attr('hidden',false);
				$("#id_employment_status").attr('hidden',false);
				// input
				$("#branch_input").attr('hidden',true);
				$("#position_detail_input").attr('hidden',true);
				$("#dept_input").attr('hidden',true);
				$("#job_grade_input").attr('hidden',true);
				$("#principal_input").attr('hidden',true);
				$("#status_input").attr('hidden',true);
				$("#effective_date").attr('readonly',true);
				$("#expired_date").attr('readonly',true);
				$("#effective_date").parent().children('span').children('button').attr('disabled',true);
				$("#expired_date").parent().children('span').children('button').attr('disabled',true);
				$("#validate_exp").html('*');
				$("#validate_effective").html('*');
			}
		}
	});
	$(document).on('change','#id_category',function() {
		var categoryID = $(this).val();
		$(".select_search").empty();
		if (categoryID) {
			const selectElement = document.getElementById('id_category');
			const selectedOption = selectElement.options[selectElement.selectedIndex];
			const moreCategory = selectedOption.getAttribute('more_code');
			if (moreCategory == "PUB") {
				$("#no_name").attr('disabled',true);
				$("#no_name").prop('checked',false).trigger('change');
				$("#row_keterangan").show();
				$("#validate_exp").html('');
				$("#expired_date").attr('readonly',true);
				// $("#expired_date").prop('disabled',true);
				// document.getElementById('expired_date').readOnly=false;
			}else{
				$("#no_name").attr('disabled',false);
				$("#row_keterangan").hide();
				if ($("#no_name").prop('checked')) {
					$("#validate_exp").html('');
					document.getElementById('expired_date').readOnly=false;
					$("#expired_date").prop('disabled',false);
					document.getElementById('filter').disabled=true;
				}else{
					$("#validate_exp").html('*');
					document.getElementById('filter').disabled=false;
				}
			}
			if ($("#no_name").prop('checked')) {
				document.getElementById('filter').disabled=true;
			}else{
				document.getElementById('filter').disabled=false;
			}
			$(".date_change").val('');
		}else{
			$("#row_keterangan").show();
			$("#no_name").attr('disabled',true);
			$(".date_change").val('');
			$(".select_search").empty();
			document.getElementById('filter').disabled=true;
		}
	});
	$(document).ready(function() {
		$("#filter").click(function () {
			const selectElement = document.getElementById('id_category');
			const selectedOption = selectElement.options[selectElement.selectedIndex];
			const moreCategory = selectedOption.getAttribute('more_code');
			if (moreCategory) {
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
						url: "{{ url('e-letter/decree/employment_certificate/get_employee') }}",
						data : {id_url:global_url_server, category:moreCategory},
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
				alert('Select Category');
			}
		});
	});
	$("#modal_view_employee").on('hidden.bs.modal', function () {
		if ($.fn.DataTable.isDataTable('#table_view_employee')) {
			$('#table_view_employee').DataTable().destroy();
		}
	});
	function change_employee(employeeID, employeeStatus) {
		if (employeeID) {
			show_loading();
			$("#modal_view_employee").modal('hide');
			$(".select_search").empty();
			$(".date_change").val('');
			$.ajax({
				type: "GET",
				dataType: 'json',
				url: "{{ url('e-letter/decree/employment_certificate/change_employee') }}"+"?employee_id="+employeeID+"&employee_status="+employeeStatus,
				success: function(response) {
					if (response.data) {
						hide_loading();
						$.each(response.data, function(key, value_data) {
							$("#effective_date").val(value_data.join_date);
							$("#expired_date").val(value_data.resign_date);
							$("#id_employee").append('<option value="'+value_data.id_employee+'">' + value_data.name + ' / ' + value_data.nik_employee +'</option>');
							$("#id_region").val(value_data.id_region).trigger('change');
							$("#id_branch").append('<option value="'+value_data.id_branch+'">' + value_data.dec_branch +'</option>');
							$("#id_position_detail").append('<option value="'+value_data.id_routing+'">' + value_data.dec_position +'</option>');
							$("#id_job_grade").append('<option value="'+value_data.id_job_grade+'">' + value_data.dec_job_grade +'</option>');
							$("#id_employment_status").append('<option value="' + value_data.id_employment_status + '">'+ value_data.status +'</option>');
							$("#id_dept").append('<option value="'+value_data.id_dept+'">' + value_data.dec_dept +'</option>');
							$("#id_principal").append('<option selected value="'+value_data.dec_principal+','+'">' + value_data.dec_principal +'</option>');
						});
					} else {
						hide_loading();
						alert('data tidak ditemukan')
						$(".select_search").empty();
						$(".date_change").val('');
					}
				},
				error: function(response) {
					change_employee(employeeID, employeeStatus);
				}
			});
		}else{
			$(".select_search").empty();
			$("#id_principal").empty();
		}
	}
	$(document).on('click','.choose_employee',function() {
		var employeeID = $(this).attr('more_id');
		var employeeStatus = $(this).attr('more_status');
		if (employeeID) {
			change_employee(employeeID, employeeStatus);
		}
	});
	function change_position_chief(employeeID) {
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
				change_position_chief(employeeID);
			});
		} else {
			$("#id_position_routing_chief").empty();
		}
	}
	$(document).on('change','#id_employee_chief',function() {
		var employeeID = $(this).val();
		$("#id_position_routing_chief").empty();
		if (employeeID) {
			change_position_chief(employeeID);
		}
	});
	$(function () {
		$('#skkForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#skkForm input").removeClass("is-invalid");
			$("#skkForm select").removeClass("custom-select");
			$("#skkForm textarea").removeClass("is-invalid");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: ajaxUrl,
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#skkForm")[0].reset();
						$(".select_search").val(null).trigger('change');
						$(".select_opsi").val(null).trigger('change');
						$('#modal_form_skk').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						// window.location.reload();
						$('#skk_table').DataTable().ajax.reload();
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
	function get_edit(letterID) {
		if (letterID) {
			$.ajax({
				type: "GET",
				url: "{{url('e-letter/decree/employment_certificate/get_edit')}}"+"/"+letterID,
				success: function(response) {
					if (response.data) {
						hide_loading();
						$.each(response.data, function(key, value) {
							$("#date").parent().children('span').children('button').attr('disabled',true);
							$("#date").attr('disabled',true);
							$("#id_category").attr('readonly','readonly');
							if (value.remark_3 != null) {
								$("#no_name").prop('checked',true).trigger('change');
								var remark_3 = value.remark_3.split(';');
								$("#name_input").val(remark_3[0]);
								$("#branch_input").val(remark_3[1]);
								$("#position_detail_input").val(remark_3[2]);
								$("#dept_input").val(remark_3[3]);
								$("#job_grade_input").val(remark_3[4]);
								$("#status_input").val(remark_3[5]);
								$("#principal_input").val(value.id_principal);
							}else{
								$("#no_name").prop('checked',false).trigger('change');
							}
							$("#id_category").val(value.id_category).trigger('change');
							$("#id_dept").append('<option value="' + value.dec_dept + '">'+ value.dec_dept +'</option>');
							$("#id_branch").append('<option value="' + value.dec_branch + '">'+ value.dec_branch +'</option>');
							$("#id_position_detail").append('<option value="' + value.dec_position + '">'+ value.dec_position +'</option>');
							$("#id_job_grade").append('<option value="' + value.dec_job_grade + '">'+ value.dec_job_grade +'</option>');
							$("#id_employment_status").append('<option value="' + value.status + '">'+ value.status +'</option>');
							$("#id_principal").append('<option selected value="' + value.id_principal + '">'+ value.id_principal +'</option>');
							$("#no_name").attr('disabled',true);
							$("#date").val(value.date);
							$("#id_letter").val(value.id_letter);
							$("#effective_date").val(value.effective_date);
							$("#expired_date").val(value.expired_date);
							$("#keterangan").val(value.notes);
							$("#email").val(value.email);
							$("#remark_2").val(value.remark_2).trigger('change');
							$("#id_employee_chief").val(value.id_employee_chief).trigger('change');
							$("#id_employee").append('<option value="' + value.id_employee + '">'+ value.name + ' / ' + value.nik_employee +' ('+value.status_employee+')'+'</option>');
							$("#id_region").val(value.id_region).trigger('change');
							$("#id_region").attr('readonly','readonly');
							$("#filter").attr('disabled',true);

						});
					}
				},
				error: function(response) {
					get_edit(letterID);
				}
			});
		}else{
			$("#modal_form_skk").modal('hide');
		}
	}
	$(document).ready(function() {
		$(document).on('click', '.btn-edit', function() {
			show_loading();
			var letterID = $(this).attr('more_id');
			$(".select_search").empty();
			$("#skkForm")[0].reset();
			$("#no_name").prop('checked',false).trigger('change');
			$("#no_name").attr('disabled',true);
			$(".select_opsi").val(null).trigger('change');
			$("#id_region").val(null).trigger('change');
			$("#id_category").select2();
			ajaxUrl="{{route('edit.skk')}}";
			$(".invalid-feedback").children("strong").text("");
			$("#skkForm input").removeClass("is-invalid");
			$("#skkForm select").removeClass("custom-select");
			$("#skkForm textarea").removeClass("is-invalid");
			$("#modal_form_skk").modal('show');
			if (letterID) {
				get_edit(letterID);
			}
		});
	});
</script>