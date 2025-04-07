<script type="text/javascript">
	$(".select_search").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:',
	});
	$(".select_disabled").select2();
	$('.select_disabled').on('select2:opening', function (e) {
		e.preventDefault();
	});
	$(".select_edit").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:',
	});
	$('#remark_1').on('keyup', function() {
		$(this).val($(this).val().toUpperCase());
	});
	$(document).on('change', '#remark_2', function () {
		var checkbox = document.getElementById('remark_2');
		var remark_2 = $(this).val();
		if (checkbox.checked) {
			$("#id_employee").val(null).trigger('change');
			$("#id_position_detail").val(null).trigger('change');
			$(".select_rehire").select2();
			$("#employee_notrehire").css('display','none');
			$("#employee_rehire").css('display','block');
			document.getElementById('id_position_detail').setAttribute('readonly','readonly');
			document.getElementById('id_dept').setAttribute('readonly','readonly');
			document.getElementById('id_job_grade').setAttribute('readonly','readonly');
			document.getElementById('id_branch').setAttribute('readonly','readonly');
			document.getElementById('id_location').setAttribute('readonly','readonly');
			document.getElementById('id_region').setAttribute('readonly','readonly');
		}else{
			$("#id_employee").val(null).trigger('change');
			$("#id_position_detail").val(null).trigger('change');
			$("#employee_notrehire").css('display','block');
			$("#employee_rehire").css('display','none');
			$("#id_position_detail").select2({
				allowClear: true,
				placeholder: ':. FILTER OPTION .:',
			});
			$("#id_branch").select2({
				allowClear: true,
				placeholder: ':. FILTER OPTION .:',
			});
			$("#id_location").select2({
				allowClear: true,
				placeholder: ':. FILTER OPTION .:',
			});
			document.getElementById('id_position_detail').removeAttribute('readonly');
			document.getElementById('id_branch').removeAttribute('readonly');
			document.getElementById('id_location').removeAttribute('readonly');
		}
	});
	function get_employee() {
		$.getJSON("{{url('e-letter/employee_agreement/employee_work_agreement/get_data_new')}}", {id_url:global_url_server}, function(response) {
			$.each(response.employee, function(key, value_employee) {
				$("#id_employee").append('<option value="' + value_employee.id_employee + '" more_career="'+value_employee.id_career_transaction+'">' + value_employee.name +' ('+value_employee.nik_employee+')'+'</option>');
			});
			$("#id_employee").val(null).trigger('change');
		}).fail(function(jqXHR, textStatus, errorThrown) {
			get_employee();
		});
	}
	$(document).ready(function() {
		$(document).on('click', '.new', function () {
			$("#pkkForm")[0].reset();
			$("#id_position_detail").val(null).trigger('change');
			$("#id_employee").empty();
			$(".select_search").val(null).trigger('change');
			$('#modal_form_pkk').modal('show');
			hide_loading();
			$(".invalid-feedback").children("strong").text("");
			$("#pkkForm input").removeClass("is-invalid");
			$("#pkkForm select").removeClass("custom-select");
			$("#remark_2").prop('checked',false).trigger('change');
			get_employee();
		});
	});
	$("#validate_category").html('<sup class="text text-danger">*</sup>');
	$(document).on('change', '#id_category', function () {
		var categoryID = $(this).val();
		if (categoryID) {
			const selectElement = document.getElementById('id_category');
			const selectedOption = selectElement.options[selectElement.selectedIndex];
			const deskripsiCategory = selectedOption.getAttribute('category_deskripsi');
			if (deskripsiCategory == 'PKWT') {
				document.getElementById('expired_date').disabled=false;
				$("#expired_date").parent().children('span').children('button').attr('disabled',false);
				$("#validate_category").html('<sup class="text text-danger">*</sup>');
			}else{
				document.getElementById('expired_date').disabled=true;
				$("#expired_date").parent().children('span').children('button').attr('disabled',true);
				$("#validate_category").html('');
			}
			if (deskripsiCategory == 'PKWTT Probation') {
				$("#date_start").html('Start Probation');
				$("#date_end").html('End Probation');
				$("#expired_date").parent().children('span').children('button').attr('disabled',false);
				$("#validate_category").html('<sup class="text text-danger">*</sup>');
				document.getElementById('expired_date').disabled=false;

			}else{
				$("#date_start").html('Effective');
				$("#date_end").html('Expired');
			}
		}
	});
	$('#effective_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}); 
	$('#expired_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
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
	// $(document).ready(function() {
	// 	$.ajax({
	// 		url: "{{url('e-letter/employee_agreement/employee_work_agreement/get_data_new')}}",
	// 		data: {id_url: global_url_server},
	// 		dataType: 'json',
	// 		success: function (data) {
	// 			var groups = {};
	// 			$.each(data.position_detail, function (index, item) {
	// 				var groupName = item.dec_routing;
	// 				if (!groups[groupName]) {
	// 					groups[groupName] = [];
	// 				}
	// 				groups[groupName].push({
	// 					id: item.id_position_detail,
	// 					text: item.dec_detail
	// 				});
	// 			});

	// 			var groupArray = [];
	// 			$.each(groups, function (groupName, groupItems) {
	// 				groupArray.push({
	// 					text: groupName,
	// 					children: groupItems
	// 				});
	// 			});
	// 			$("#id_position_detail").empty();
	// 			$.each(groupArray, function (index, group) {
	// 				var $optgroup = $("<optgroup>", { label: group.text });
	// 				$.each(group.children, function (i, item) {
	// 					$optgroup.append($("<option>", { value: item.id, text: item.text }));
	// 				});
	// 				$("#id_position_detail").append($optgroup);
	// 			});
	// 			$("#id_position_detail").select2({
	// 				placeholder: ':. FILTER OPTION .:',
	// 				allowClear: true
	// 			});
	// 		}
	// 	});
	// });
	function change_employee(employeeID) {
		if (employeeID) {
			show_loading();
			$.ajax({
				method: "GET",
				headers: {
					Accept: "application/json"
				},
				url: "{{ url('e-letter/employee_agreement/employee_work_agreement/change_employee') }}"+"?id_employee="+employeeID,
				success: function (response) {
					if (response) {
						hide_loading();
						$.each(response, function(key, value) {
							$("#id_position_detail").val(value.id_position_routing).trigger('change');
							$("#nama_employee").val(value.name);
							$("#id_branch").append('<option value="'+value.id_branch+'">' + value.dec_branch +'</option>');
							$("#id_region").append('<option value="'+value.id_region+'">' + value.dec_region +'</option>');
							$("#id_location").append('<option value="'+value.id_location+'">' + value.dec_location +'</option>');
						});
					} else {
						$("#id_position_detail").val(null).trigger('change');
					}
				},
				error: function(response) {
					change_employee(employeeID);
					// swal({
					// 	icon: 'error',
					// 	title: 'Oops...',
					// 	dangerMode: true,
					// 	text: 'Something went wrong! [Unknown Error]'
					// });
					// $("#id_employee").val(null).trigger('change');
				}
			});
		} else {
			$("#id_position_detail").val(null).trigger('change');
		}
	}
	$(document).on('change','#id_employee',function() {
		var employeeID = $(this).val();
		$("#id_position_detail").val(null).trigger('change');
		if (employeeID) {
			change_employee(employeeID);
		}

	});
	function change_position(positionID) {
		if (positionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/employee_agreement/employee_work_agreement/get_position') }}"+"?position_id="+positionID, function(response) {
				if (response) {
					hide_loading();
					$.each(response, function(key, value) {
						$("#id_dept").append('<option value="'+value.id_dept+'">' + value.dec_dept +'</option>');
						$("#id_job_grade").append('<option value="'+value.id_job_grade+'">' + value.dec_job_grade +'</option>');
						if ($("#id_employee").val() == null) {
							$("#id_branch").append('<option value="'+value.id_branch+'">' + value.dec_branch +'</option>');
						}
					});
					if ($("#id_employee").val() == null) {
						$("#id_branch").val(null).trigger('change');
					}
				} else {
					$("#id_branch").val(null).trigger('change');
				}
			}).fail(function(jqXHR, textStatus, errorThrown) {
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
		$("#id_branch").val(null).trigger('change');
		var id_employee = $("#id_employee").val();
		if (positionID) {
			change_position(positionID);
		}

	});
	function change_branch(branchID, positionID) {
		if (branchID && positionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/employee_agreement/employee_work_agreement/change_branch') }}"+"?id_branch="+branchID+"&id_position="+positionID, function(response) {
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
				change_branch(branchID, positionID);
				// swal({
				// 	icon: 'error',
				// 	title: 'Oops...',
				// 	dangerMode: true,
				// 	text: 'Something went wrong! [Unknown Error]'
				// });
				// hide_loading();
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
		$('#pkkForm').submit(function (e) {
			e.preventDefault();
			document.querySelector(".action").disabled=true;
			let formData = $(this).serializeArray();
			$(".invalid-feedback").children("strong").text("");
			$("#pkkForm input").removeClass("is-invalid");
			$("#pkkForm select").removeClass("custom-select");
			$.ajax({
				method: "POST",
				headers: {
					Accept: "application/json"
				},
				url: "{{ route('pkk.save') }}",
				data: formData,
				success: function (response) {
					document.querySelector(".action").disabled=false;
					if (response.status == 'true') {
						$("#pkkForm")[0].reset();
						$(".select_search").val(null).trigger('change');
						$("#id_position_detail").val(null).trigger('change');
						$('#modal_form_pkk').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						$('#pkk_table').DataTable().ajax.reload();
					}
					else {
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
					}else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! '+response.message
						});
					}
				}
			});
		});
	});
</script>