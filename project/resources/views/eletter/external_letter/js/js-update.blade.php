<script type="text/javascript">
	$(".select_opsi_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$(".select_disabled_edit").select2();
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	$(document).on('click','.btn-edit',function() {
		var letterID = $(this).attr('more_id');
		$("#modal_form_ext_edit").modal('show');
		$(".select_opsi_edit").val(null).trigger('change');
		$(".select_disabled_edit").val(null).trigger('change');
		$("#extFormEdit")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$("#extFormEdit input").removeClass("is-invalid");
		$("#extFormEdit select").removeClass("custom-select");
		sessionStorage.clear();
		if (letterID) {
			$.getJSON("{{ url('e-letter/company_letter/external_letter/get_edit') }}"+"/"+letterID, function(response) {
				if (response.data) {
					$(".select_opsi_edit").val(null).trigger('change');
					$(".select_disabled_edit").val(null).trigger('change');
					$.each(response.data, function(key, value) {
						$("#id_letter").val(value.id_letter);
						$("#id_company_edit").val(value.id_company).trigger('change');
						$("#id_category_edit").val(value.id_category).trigger('change');
						$("#id_dept_edit").val(value.id_dept).trigger('change');
						$("#id_region_edit").val(value.id_region).trigger('change');
						$("#date_edit").val(value.date);
						$("#remark_1_edit").val(value.remark_1);
						$("#email_edit").val(value.email);
						$("#notes_edit").val(value.notes);
						$("#id_employee_chief_edit").val(value.id_employee_chief).trigger('change');
						sessionStorage.setItem("selectedOption", value.remark_1);
						sessionStorage.setItem("id_branch", value.id_branch);
					});
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
				$("#modal_form_ext_edit").modal('hide')
			});
		}else{
			$("#modal_form_ext_edit").modal('hide');
		}
	});
	$(document).on('change','#id_category_edit',function() {
		var categoryID = $(this).val();
		if (categoryID) {
			if (sessionStorage.getItem('selectedOption') == 'null') {
				var remark_1 = '';
			}else{
				var remark_1 = sessionStorage.getItem('selectedOption');
			}
			if (categoryID == '1351') {
				$("#remark_1_edit").val(remark_1);
				$("#branch_required_edit").html('*');
				$("#category_code_edit").val('OTHERS');
				document.getElementById('remark_1_edit').disabled=false;
			}else{
				$("#remark_1_edit").val('');
				$("#category_code_edit").val('-');
				$("#branch_required_edit").html('');
				document.getElementById('remark_1_edit').disabled=true;
			}
		}else{
			$("#branch_required_edit").html('');
			$("#remark_1_edit").val('');
			$("#category_code_edit").val('-');
			document.getElementById('remark_1_edit').disabled=true;
		}
	});
	$(document).on('change','#id_region_edit',function() {
		var regionID = $(this).val();
		$("#id_branch_edit").empty();
		$("#id_branch_edit").append('<option value="-"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></option>');
		if (regionID) {
			$.ajax({
				type: "GET",
				url: "{{url('e-letter/company_letter/external_letter/get_branch')}}"+"?region_id="+regionID,
				data : {id_url:global_url_server},
				success: function(response) {
					if (response) {
						$("#id_branch_edit").empty();
						$.each(response, function(key, value) {
							$("#id_branch_edit").append('<option value="' + value.id_branch + '">'+ value.description +'</option>');
						});
						$("#id_branch_edit").val(sessionStorage.getItem('id_branch')).trigger('change');
					}else{
						$("#id_branch_edit").empty();
					}
				},
				error: function(response) {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [Unknown Error]'
					});
					$("#modal_form_ext_edit").modal('hide');
				}
			});
		}else{
			$("#id_branch_edit").empty();
		}
	});
	$("#id_position_routing_chief_edit").select2();
	$(document).on('change','#id_employee_chief_edit',function() {
		var employeeID = $(this).val();
		$("#id_position_routing_chief_edit").empty();
		$("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
		if (employeeID) {
			$.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
				if (response) {
					$("#id_position_routing_chief_edit").empty();
					$("#id_position_routing_chief_edit").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
					$("#id_position_routing_chief_edit").val(response.id_routing);
				} else {
					$("#id_position_routing_chief_edit").empty();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
				$("#id_employee_chief_edit").val(null).trigger('change');
			});
		} else {
			$("#id_position_routing_chief_edit").empty();
		}
	});
	$(function () {
		$('#extFormEdit').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#extFormEdit input").removeClass("is-invalid");
			$("#extFormEdit select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('edit.ext') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#extFormEdit")[0].reset();
						$(".select_opsi_edit").val(null).trigger('change');
						$('#modal_form_ext_edit').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#ext_table').DataTable().ajax.reload();
					}else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! [Unknown Error Not Save]'
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
					else if (response.status === 500) {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! [Unknown Error]'
						});
					}
					else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! [Unknown Error]'
						});
					}
				}
			});
		});
	});
</script>