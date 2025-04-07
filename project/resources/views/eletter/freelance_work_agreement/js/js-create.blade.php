<script type="text/javascript">
	$(".select_search").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:',
	});
	$(".select_disabled").select2();
	$('.select_disabled').on('select2:opening', function (e) {
		e.preventDefault();
	});
	// $('.select_disabled').addClass('readonly-select2');
	// $('.select_disabled').css({
	// 	'background-color': '#f2f2f2 !important',
	// 	'pointer-events': 'none !important',
	// 	'opacity': '0.7 !important'
	// });
	$('#remark_1').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());
	});

	$(".select_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:',
	});

	$(document).on('click', '.new', function () {
		$("#freelanceForm")[0].reset();
		$(".select_search").val(null).trigger('change');
		$("#id_position_detail").val(null).trigger('change');
		$('#modal_form_freelance').modal('show');
		$(".invalid-feedback").children("strong").text("");
		$("#freelanceForm input").removeClass("is-invalid");
		$("#freelanceForm select").removeClass("custom-select");
		hide_loading();
	});
	// $("#validate_category").html('<sup class="text text-danger">*</sup>');
	// $(document).on('change', '#id_category', function () {
	// 	var categoryID = $(this).val();
	// 	if (categoryID == '1354') {
	// 		$("#validate_category").html('<sup class="text text-danger">*</sup>');
	// 	}else{
	// 		$("#validate_category").html('');
	// 	}
	// });
	$("#effective_date").daterangepicker({
		// autoApply: true,
		autoUpdateInput: false,
		locale: {
			format: 'YYYY-MM-DD'
		}
	}).on('apply.daterangepicker', function(ev, picker) {
		var startDate = picker.startDate;
		var endDate = picker.endDate;
		var maxAllowedDays = 21;

		var diffInDays = endDate.diff(startDate, 'days');

		if (diffInDays > maxAllowedDays) {
			alert('End Date tidak boleh lebih dari 21 hari.');
			var endDate = startDate.clone().add(21, 'days');
			picker.setStartDate(startDate);
			picker.setEndDate(endDate);
			var minDate = startDate.clone().subtract(1, 'days');
			var maxDate = endDate.clone().add(1, 'days');
			// picker.minDate(minDate);
			// picker.maxDate(maxDate);
			// alert(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
			$(this).val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
		}
		$(this).val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
	}).on('cancel.daterangepicker', function() {
		$(this).val('');
	});
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}   
	});
	$("#id_position_detail").select2({
		placeholder: ':. FILTER OPTION .:',
		allowClear: true
	});
	function change_position(positionID) {
		if (positionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/employee_agreement/freelance_work_agreement/get_position') }}"+"?position_id="+positionID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						$("#id_dept").append('<option value="'+value.id_dept+'">' + value.dec_dept +'</option>');
						$("#id_job_grade").append('<option value="'+value.id_job_grade+'">' + value.dec_job_grade +'</option>');
						$("#id_branch").append('<option value="'+value.id_branch+'">' + value.dec_branch +'</option>');
					});
					$("#id_branch").val(null).trigger('change');
				} else {
					$("#id_branch").val(null).trigger('change');
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				hide_loading();
				change_position(positionID);
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// hide_loading();
				// $("#id_position_detail").val(null).trigger('change');
			});
		} else {
			$("#id_branch").val(null).trigger('change');
		}
	}
	$(document).on('change','#id_position_detail',function() {
		var positionID = $(this).val();
		$("#id_dept").empty();
		$("#id_job_grade").empty();
		$("#id_branch").empty();
		$("#id_location").empty();
		var id_employee = $("#id_employee").val();
		if (positionID) {
			change_position(positionID);
		}
		
	});
	function change_branch(branchID, positionID) {
		if (branchID && positionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/employee_agreement/freelance_work_agreement/change_branch') }}"+"?id_branch="+branchID+"&id_position="+positionID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						$("#id_location").append('<option value="'+value.id_location+'">' + value.dec_location +'</option>');
						$("#id_region").append('<option value="'+value.id_region+'">' + value.dec_region +'</option>');
					});
				} else {
					$("#id_location").empty();
					$("#id_region").empty();
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				hide_loading();
				change_branch(branchID, positionID);
				// $("#id_branch").val(null).trigger('change');
			});
		} else {
			$("#id_location").empty();
			$("#id_region").empty();
		}
	}
	$(document).on('change','#id_branch',function() {
		var branchID = $(this).val();
		var positionID = $("#id_position_detail").val();
		$("#id_location").empty();
		$("#id_region").empty();
		if (branchID && positionID) {
			change_branch(branchID, positionID);
		}
		
	});

	$(function () {
		$('#freelanceForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#freelanceForm input").removeClass("is-invalid");
			$("#freelanceForm select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('pkhl.save') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#freelanceForm")[0].reset();
						$(".select_search").val(null).trigger('change');
						$("#id_position_detail").val(null).trigger('change');
						$('#modal_form_freelance').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#freelance_table').DataTable().ajax.reload();
					}else if(response.status == 'warning'){
						swal({
							icon: 'warning',
							title: 'Oops...',
							text: response.message
						});
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