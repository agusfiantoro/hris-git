<script type="text/javascript">
	$(".new").click(function() {
		$("#modal_form_ext").modal('show');
		$("#extForm")[0].reset();
		$(".select_opsi").val(null).trigger('change');
		$(".invalid-feedback").children("strong").text("");
		$("#extForm input").removeClass("is-invalid");
		$("#extForm select").removeClass("custom-select");
	});
	$(".select_opsi").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$("#id_position_routing_chief").select2();
	$(document).on('change','#id_category',function() {
		var categoryID = $(this).val();
		if (categoryID) {
			switch (categoryID) {
				case "1351":
				$("#branch_required").html('*');
				$("#category_code").val('OTHERS');
				document.getElementById('remark_1').disabled=false;
				break;
				default:
				$("#category_code").val('-');
				$("#branch_required").html('');
				document.getElementById('remark_1').disabled=true;
			}
		}else{
			$("#branch_required").html('');
			document.getElementById('remark_1').disabled=true;
		}
	});

	$(document).on('change','#id_region',function() {
		var regionID = $(this).val();
		$("#id_branch").empty();
		$("#id_branch").append('<option value="-"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></option>');
		if (regionID) {
			$.ajax({
				type: "GET",
				url: "{{url('e-letter/company_letter/external_letter/get_branch')}}"+"?region_id="+regionID,
				data : {id_url:global_url_server},
				success: function(response) {
					if (response) {
						$("#id_branch").empty();
						$.each(response, function(key, value) {
							$("#id_branch").append('<option value="' + value.id_branch + '">'+ value.description +'</option>');
						});
					}else{
						$("#id_branch").empty();
					}
				},
				error: function(response) {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [Unknown Error]'
					});
					$("#id_region").val(null).trigger('change');
				}
			});
		}else{
			$("#id_branch").empty();
		}
	});
	$(document).on('change','#id_employee_chief',function() {
		var employeeID = $(this).val();
		$("#id_position_routing_chief").empty();
		$("#id_position_routing_chief").append('<option value=""><span class="sr-only">Loading...</span></option>');
		if (employeeID) {
			$.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
				if (response) {
					$("#id_position_routing_chief").empty();
					$("#id_position_routing_chief").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
				} else {
					$("#id_position_routing_chief").empty();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
				$("#id_employee_chief").val(null).trigger('change');
			});
		} else {
			$("#id_position_routing_chief").empty();
		}
	});
	$(function () {
		$('#extForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#extForm input").removeClass("is-invalid");
			$("#extForm select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('save.ext') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#extForm")[0].reset();
						$(".select_opsi").val(null).trigger('change');
						// $("#id_category").val(null).trigger('change');
						$('#modal_form_ext').modal('hide');
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
					document.querySelector(".action").disabled=false;
					if (response.status === 422) {
						let errors = response.responseJSON.errors;
						Object.keys(errors).forEach(function (key) {
							$("#" + key).addClass("is-invalid");
							$("select[id='" + key + "']").addClass("custom-select");
							$("#" + key + "Error").children("strong").text(errors[key][0]);
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