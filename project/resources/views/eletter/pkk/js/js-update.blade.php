<script type="text/javascript">
	$('#effective_date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}); 
	$('#expired_date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
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
			$.getJSON("{{url('e-letter/employee_work_agreement/get_edit')}}"+"/"+letterID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						if (value.remark_2 != null) {
							$('#remark_2_edit').prop('checked', true);
							document.getElementById('employee_notrehire_edit').style.display = "none";
							document.getElementById('employee_rehire_edit').style.display = "block";
							$("#id_employee_edit").append('<option value="'+value.remark_1+'">' + value.remark_1 +'</option>');
						}
						else{
							$('#remark_2_edit').prop('checked', false);
							document.getElementById('employee_notrehire_edit').style.display = "block";
							document.getElementById('employee_rehire_edit').style.display = "none";
							$("#remark_1_edit").val(value.remark_1);
						}
						$("#cek_rehire").val(value.remark_2);
						$("#id_letter_edit").val(letterID);
						$("#email_edit").val(value.email);
						$("#date_edit").val(value.date);
						$("#notes_edit").val(value.notes);
						$("#effective_date_edit").val(value.effective_date);
						$("#expired_date_edit").val(value.expired_date);
						$("#id_category_edit").val(value.id_category).trigger('change');
						$("#id_position_detail_edit").append('<option value="'+value.dec_position+'">' + value.dec_position +'</option>');
						$("#id_dept_edit").append('<option value="'+value.dec_dept+'">' + value.dec_dept +'</option>');
						$("#id_job_grade_edit").append('<option value="'+value.dec_job_grade+'">' + value.dec_job_grade +'</option>');
						$("#id_branch_edit").append('<option value="'+value.dec_branch+'">' + value.dec_branch +'</option>');
						$("#id_region_edit").append('<option value="'+value.dec_region+'">' + value.dec_region +'</option>');
						$("#id_location_edit").append('<option value="'+value.dec_location+'">' + value.dec_location +'</option>');
						if (value.category == 'PKWT') {
							document.getElementById('expired_date_edit').disabled=false;
							$("#expired_date_edit").parent().children('span').children('button').attr('disabled',false);
							$("#validate_expired_edit").html('<sup class="text text-danger">*</sup>');
						}else{
							document.getElementById('expired_date_edit').disabled=true;
							$("#expired_date_edit").parent().children('span').children('button').attr('disabled',true);
							$("#validate_expired_edit").html('');
						}
						if (value.category == 'PKWTT Probation') {
							$("#start_date_view").html('Start Probation');
							$("#end_date_view").html('End Probation');
							$("#expired_date_edit").parent().children('span').children('button').attr('disabled',true);
							document.getElementById('expired_date_edit').disabled=true;
						}else{
							$("#start_date_view").html('Effective');
							$("#end_date_view").html('Expired');
						}
					});
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// $("#modal_form_pkk_edit").modal('hide');
				get_edit(letterID);
			});
		}else{
			$("#modal_form_pkk_edit").modal('hide');
		}
	}
	$(document).on('click', '.btn-edit', function() {
		var letterID = $(this).attr('more_id');
		show_loading();
		$(".select_search_edit").val(null).trigger('change');
		$("#formPkkUpdate select").removeClass("custom-select");
		$("#formPkkUpdate input").removeClass("is-invalid");
		$(".select_disabled_edit").empty();
		$("#formPkkUpdate")[0].reset();
		$("#modal_form_pkk_edit").modal('show');
		if (letterID) {
			get_edit(letterID);
		}
	});

	$(function () {
		$('#formPkkUpdate').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action_edit").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#formPkkUpdate input").removeClass("is-invalid");
			$("#formPkkUpdate select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('pkk.update') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action_edit").disabled=false;
					if (response.status == 'true') {
						$("#formPkkUpdate")[0].reset();
						$(".select_disabled_edit").empty();
						$(".select_search_edit").val(null).trigger('change');
						$('#modal_form_pkk_edit').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#pkk_table').DataTable().ajax.reload();
					} else {
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