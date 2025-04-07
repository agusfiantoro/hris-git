<script type="text/javascript">
	$(".select_opsi_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$('#effective_date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}); 
	$('#expired_date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#remark_4_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#date_edit').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$("#date_edit").parent().children('span').children('button').attr('disabled',true);
	function get_edit(id_letter) {
		if (id_letter) {
			$.ajax({
				type: "GET",
				url: "{{url('e-letter/internal_memo/get_edit')}}"+"/"+id_letter,
				success: function(response) {
					if (response.data) {
						hide_loading();
						if (response.data.id_career_transaction == null) {
							$("#remark_8_edit").prop('checked',false);
						}else{
							$("#remark_8_edit").prop('checked',true);
						}
						if (response.data.category_code == "TA") {
							$('#remark_2_edit').summernote('pasteHTML', response.data.remark_2);
							$('#remark_3_edit').summernote('pasteHTML', response.data.remark_3);
						}
						$("#id_letter").val(id_letter);
						$("#id_employee_edit").append('<option value="' + response.data.name + '">'+ response.data.name +' ('+response.data.nik_employee+')'+'</option>');
						$("#id_category_edit").val(response.data.id_category).trigger('change');
						$("#date_edit").val(response.data.date);
						$("#remark_1_edit").val(response.data.remark_1);
						$("#remark_4_edit").val(response.data.remark_4);
						$("#remark_5_edit").val(response.data.remark_5);
						$("#remark_6_edit").val(response.data.remark_6);
						$("#email_edit").val(response.data.email);
						$("#effective_date_edit").val(response.data.effective_date);
						$("#expired_date_edit").val(response.data.expired_date);
						$("#id_employee_chief_edit").val(response.data.id_employee_chief).trigger('change');

						var decRegionOld = response.data.dec_region_old !== null ? response.data.dec_region_old : '-';
						var decBranchOld = response.data.dec_branch_old !== null ? response.data.dec_branch_old : '-';
						var decPositionOld = response.data.dec_position_old !== null ? response.data.dec_position_old : '-';
						var decDeptOld = response.data.dec_dept_old !== null ? response.data.dec_dept_old : '-';
						var decLocationOld = response.data.dec_location_old !== null ? response.data.dec_location_old : '-';
						var decPrincipalOld = response.data.id_principal !== null ? response.data.id_principal : '-';
						$("#id_region_old_edit").append('<option value="' + decRegionOld + '">'+ decRegionOld +'</option>');
						$("#id_branch_old_edit").append('<option value="' + decBranchOld + '">'+ decBranchOld +'</option>');
						$("#id_position_detail_old_edit").append('<option value="' + decPositionOld + '">'+ decPositionOld +'</option>');
						$("#id_dept_old_edit").append('<option value="' + decDeptOld + '">'+ decDeptOld +'</option>');
						$("#id_location_old_edit").append('<option value="'+decLocationOld+'">'+ decLocationOld +'</option>');
						$("#id_principal_old_edit").append('<option selected value="'+decPrincipalOld+'">'+ decPrincipalOld +'</option>');
						var decRegionNew = response.data.dec_region_new !== null ? response.data.dec_region_new : '-';
						var decBranchNew = response.data.dec_branch_new !== null ? response.data.dec_branch_new : '-';
						var decPositionNew = response.data.dec_position_new !== null ? response.data.dec_position_new : '-';
						var decDeptNew = response.data.dec_dept_new !== null ? response.data.dec_dept_new : '-';
						var decLocationNew = response.data.dec_location_new !== null ? response.data.dec_location_new : '-';
						$("#id_region_new_edit").append('<option value="' + decRegionNew + '">'+ decRegionNew +'</option>');
						$("#id_branch_new_edit").append('<option value="' + decBranchNew + '">'+ decBranchNew +'</option>');
						$("#id_position_detail_new_edit").append('<option value="' + decPositionNew + '">'+ decPositionNew +'</option>');
						$("#id_dept_new_edit").append('<option value="' + decDeptNew + '">'+ decDeptNew +'</option>');
						$("#id_location_new_edit").append('<option value="'+decLocationNew+'">'+ decLocationNew +'</option>');
						$.each(response.principal, function(key, value_principal) {
							var decDivisionNew = value_principal.dec_division_new !== null ? value_principal.dec_division_new : '-';
							$("#id_principal_new_edit").append('<option selected value="'+decDivisionNew+'">'+ decDivisionNew +'</option>');
						});
					}
				},
				error: function(response) {
					get_edit(id_letter);
				}
			});
}else{
	$("#modal_form_im_edit").modal('hide');
}
}
$(document).on('click', '.btn-edit', function() {
	var id_letter = $(this).attr('more_id');
	$("#imFormEdit")[0].reset();
	show_loading();
	$(".select_search_edit").empty();
	$(".select_search_new_edit").empty();
	$(".select_opsi_edit").val(null).trigger('change');
	$("#remark_2_edit").summernote('reset');
	$("#remark_3_edit").summernote('reset');
	$("#remark_2_edit").summernote('empty');
	$("#remark_3_edit").summernote('empty');
	$("#remark_2_edit").summernote('code','');
	$("#remark_3_edit").summernote('code','');
	$(".invalid-feedback").children("strong").text("");
	$("#imFormEdit input").removeClass("is-invalid");
	$("#imFormEdit select").removeClass("custom-select");
	if (id_letter) {
		get_edit(id_letter);
		$("#modal_form_im_edit").modal('show');
	}
});
function ta_category_edit() {
	$("#tugas_tunjangan_edit").show();
	$("#remark_4_edit").parent().children('span').children('button').attr('disabled',true);
	document.getElementById('remark_4_edit').disabled=true;
	document.getElementById('remark_5_edit').disabled=true;
}
function orient_category_edit() {
	$("#tugas_tunjangan_edit").hide();
	$("#remark_4_edit").parent().children('span').children('button').attr('disabled',false);
	document.getElementById('remark_4_edit').disabled=false;
	document.getElementById('remark_5_edit').disabled=false;
	document.getElementById('remark_1_edit').disabled=true;
}
function mutation_category_edit() {
	$("#tugas_tunjangan_edit").hide();
	$("#expired_date_edit").parent().children('span').children('button').attr('disabled',true);
	document.getElementById('expired_date_edit').disabled=true;
	$("#remark_4_edit").parent().children('span').children('button').attr('disabled',true);
	document.getElementById('remark_4_edit').disabled=true;
	document.getElementById('remark_5_edit').disabled=false;
	document.getElementById('remark_1_edit').disabled=true;
}
function failed_orient_category_edit() {
	$("#tugas_tunjangan_edit").hide();
	$("#expired_date_edit").parent().children('span').children('button').attr('disabled',true);
	document.getElementById('expired_date_edit').disabled=true;
	document.getElementById('remark_1_edit').disabled=true;
	$("#remark_4_edit").parent().children('span').children('button').attr('disabled',true);
	document.getElementById('remark_4_edit').disabled=true;
	document.getElementById('remark_5_edit').disabled=true;
}
$("#id_category_edit").change(function() {
	var categoryID = $(this).val();
	if (categoryID) {
		const selectElement = document.getElementById('id_category_edit');
		const selectedOption = selectElement.options[selectElement.selectedIndex];
		const deskripsiCategory = selectedOption.getAttribute('data-attribute');
		if (deskripsiCategory == 'TA') {
			ta_category_edit();
		}else if(deskripsiCategory == "ORIENT"){
			orient_category_edit();
		}else if(deskripsiCategory == "MUTA"){
			mutation_category_edit();
		}else{
			failed_orient_category_edit();
		}
	}else{
		$("#tugas_tunjangan_edit").show();
		$("#expired_date_edit").parent().children('span').children('button').attr('disabled',false);
		document.getElementById('expired_date_edit').disabled=false;
		document.getElementById('remark_1_edit').disabled=false;
		$("#remark_4_edit").parent().children('span').children('button').attr('disabled',false);
		document.getElementById('remark_4_edit').disabled=false;
		document.getElementById('remark_5_edit').disabled=false;
	}
});
$("#id_position_routing_chief_edit").select2();
function change_chief_edit(employeeID) {
	if (employeeID) {
		show_loading();
		$.getJSON("{{ url('e-letter/internal_memo/get_chief') }}"+"?employee_id="+employeeID, function(response) {
			if (response) {
				hide_loading();
				$("#id_position_routing_chief_edit").empty();
				$("#id_position_routing_chief_edit").append('<option value="'+response.id_routing+'">'+response.dec_position+'</option>');
			} else {
				$("#id_position_routing_chief_edit").empty();
			}
		}).fail(function(jqXHR, textStatus, errorThrown) {
			change_chief_edit(employeeID);
			// swal({
			// 	icon: 'error',
			// 	title: 'Oops...',
			// 	dangerMode: true,
			// 	text: 'Something went wrong! [Unknown Error]'
			// });
			// hide_loading();
			// $("#id_employee_chief_edit").val(null).trigger('change');
		});
	} else {
		$("#id_position_routing_chief_edit").empty();
	}
}
$(document).on('change','#id_employee_chief_edit',function() {
	var employeeID = $(this).val();
	$("#id_position_routing_chief_edit").empty();
	// $("#id_position_routing_chief_edit").append('<option value=""><span class="sr-only">Loading...</span></option>');
	if (employeeID) {
		change_chief_edit(employeeID);
	}
});
$(function () {
	$('#imFormEdit').submit(function (e) {
		e.preventDefault();
		document.querySelector(".action_edit").disabled=true;
		let formData = $(this).serializeArray();
		$(".invalid-feedback").children("strong").text("");
		$("#imFormEdit input").removeClass("is-invalid");
		$("#imFormEdit select").removeClass("custom-select");
		$.ajax({
			method: "POST",
			headers: {
				Accept: "application/json"
			},
			url: "{{ route('edit.im') }}",
			data: formData,
			success: function (response) {
				document.querySelector(".action_edit").disabled=false;
				if (response.status == 'true') {
					$("#imFormEdit")[0].reset();
					$(".select_search_new_edit").val(null).trigger('change');
					$(".select_search_edit").val(null).trigger('change');
					$("#id_employee_chief_edit").val(null).trigger('change');
					$('#modal_form_im_edit').modal('hide');
					swal({
						icon: 'success',
						title: 'Success',
						text: response.message
					});
						// window.location.reload();
						$('#im_table').DataTable().ajax.reload();
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