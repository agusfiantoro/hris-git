<script type="text/javascript">
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd'
	});
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	$(".select_disabled_edit").select2();
	$(".select_search_edit").select2({
		placeholder: ':. FILTER OPTION .:',
		disabled : true
	});
	$('#remark_1_edit').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());
	});
	function get_edit(letterID) {
		if (letterID) {
			$.getJSON("{{url('e-letter/freelance_work_agreement/get_edit')}}"+"/"+letterID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						$("#remark_1_edit").val(value.remark_1);
						$("#id_letter_edit").val(letterID);
						$("#email_edit").val(value.email);
						$("#date_edit").val(value.date);
						$("#notes_edit").val(value.notes);
						$("#effective_date_edit").val(value.effective_date + ' - ' + value.expired_date);
						$("#effective_date_edit").daterangepicker({
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
								$(this).val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
							}
							$(this).val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
						}).on('cancel.daterangepicker', function() {
							$(this).val(value.effective_date + ' - ' + value.expired_date);
						});
						// $("#id_category_edit").val(value.id_category).trigger('change');
						$("#id_position_detail_edit").append('<option value="'+value.dec_position+'">' + value.dec_position +'</option>');
						$("#id_dept_edit").append('<option value="'+value.dec_dept+'">' + value.dec_dept +'</option>');
						$("#id_job_grade_edit").append('<option value="'+value.dec_job_grade+'">' + value.dec_job_grade +'</option>');
						$("#id_branch_edit").append('<option value="'+value.dec_branch+'">' + value.dec_branch +'</option>');
						$("#id_region_edit").append('<option value="'+value.dec_region+'">' + value.dec_region +'</option>');
						$("#id_location_edit").append('<option value="'+value.dec_location+'">' + value.dec_location +'</option>');
					});
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// $("#modal_form_freelance_edit").modal('hide');
				get_edit(letterID);
			});
		}else{
			$("#modal_form_freelance_edit").modal('hide');
		}
	}
	$(document).on('click', '.btn-edit', function() {
		var letterID = $(this).attr('more_id');
		show_loading();
		$("#formFreelanceUpdate")[0].reset();
		$(".select_disabled_edit").empty();
		$(".select_search_edit").val(null).trigger('change');
		$("#formFreelanceUpdate select").removeClass("custom-select");
		$("#formFreelanceUpdate input").removeClass("is-invalid");
		$("#modal_form_freelance_edit").modal('show');
		if (letterID) {
			get_edit(letterID);
		}
	});

	$(function () {
		$('#formFreelanceUpdate').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#formFreelanceUpdate input").removeClass("is-invalid");
			$("#formFreelanceUpdate select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('pkhl.update') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#formFreelanceUpdate")[0].reset();
						$('#modal_form_freelance_edit').modal('hide');
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