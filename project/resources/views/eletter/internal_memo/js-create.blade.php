<script type="text/javascript">
	$(".select_opsi").select2({
		allowClear: true,
		placeholder: ':. FILTER OPTION .:'
	});
	$(".select_search_new").select2({
		placeholder: ':. FILTER OPTION .:'
	});
	function get_employee() {
		$.getJSON("{{url('e-letter/internal_memo/get_data_new')}}",{id_url:global_url_server}, function(response) {
			$("#id_employee").empty();
			$.each(response.employee, function(key, value_employee) {
				$("#id_employee").append('<option value="' + value_employee.id_employee + '">'+ value_employee.name +' ('+value_employee.nik_employee+')'+'</option>');
			});
			$("#id_employee").val(null).trigger('change');
		}).fail(function(jqXHR, textStatus, errorThrown) {
			get_employee();
		});
	}
	$(document).ready(function() {
		$(".new").click(function() {
			get_employee();
			$("#imForm")[0].reset();
			$("#remark_8").prop('checked',true).trigger('change');
			$(".select_opsi").val(null).trigger('change');
			$(".select_search_new").val(null).trigger('change');
			$("#remark_2").summernote('empty');
			$("#remark_3").summernote('empty');
			$("#remark_2").summernote('code','');
			$("#remark_3").summernote('code','');
			$(".invalid-feedback").children("strong").text("");
			$("#imForm input").removeClass("is-invalid");
			$("#imForm select").removeClass("custom-select");
			$("#modal_form_im").modal('show');
		});
	});
	$('#effective_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}); 
	$('#expired_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#remark_4').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});

	function ta_category() {
		$("#tugas_tunjangan").show();
		$("#id_employee").attr('disabled',false);
		$("#remark_6").attr('disabled',false);
		$("#effective_date").attr('disabled',false);
		$("#effective_date").parent().children('span').children('button').attr('disabled',false);
		$("#expired_date").attr('disabled',false);
		$("#expired_date").parent().children('span').children('button').attr('disabled',false);
		$("#remark_4").parent().children('span').children('button').attr('disabled',true);
		$("#remark_1").attr('disabled',false);
		document.getElementById('remark_4').disabled=true;
		document.getElementById('remark_5').disabled=true;
		$("#filter").attr('disabled',false);
		$(".select_search_new").select2({
			placeholder: ':. FILTER OPTION .:'
		});
		$("#remark_8").attr('disabled',false);
		$("#remark_8").prop('checked',true).trigger('change');
		// $(".select_search_new").removeAttr('readonly');
	}
	function orient_category() {
		$("#tugas_tunjangan").hide();
		$("#effective_date").attr('disabled',false);
		$("#expired_date").attr('disabled',false);
		$("#remark_4").attr('disabled',false);
		$("#effective_date").parent().children('span').children('button').attr('disabled',false);
		$("#expired_date").parent().children('span').children('button').attr('disabled',false);
		$("#remark_4").parent().children('span').children('button').attr('disabled',false);
		document.getElementById('remark_1').disabled=true;
		$("#remark_8").attr('disabled',true);
		$("#remark_5").attr('disabled',false);
		$("#remark_8").prop('checked',true).trigger('change');
		$("#filter").attr('disabled',false);
		$(".select_search_new").select2();
		// $(".select_search_new").attr('readonly','readonly');
	}
	function failed_orient_category() {
		$("#tugas_tunjangan").hide();
		$("#id_employee").attr('disabled',false);
		$("#remark_6").attr('disabled',false);
		$("#effective_date").attr('disabled',false);
		$("#expired_date").attr('disabled',true);
		$("#remark_4").attr('disabled',true);
		$("#effective_date").parent().children('span').children('button').attr('disabled',false);
		$("#expired_date").parent().children('span').children('button').attr('disabled',true);
		$("#remark_4").parent().children('span').children('button').attr('disabled',true);
		document.getElementById('remark_1').disabled=true;
		document.getElementById('remark_5').disabled=true;
		$("#remark_8").attr('disabled',true);
		$("#remark_8").prop('checked',true).trigger('change');
		$("#filter").attr('disabled',false);
		$(".select_search_new").select2();
		// $(".select_search_new").attr('readonly','readonly');
	}
	function gm_category() {
		$("#tugas_tunjangan").hide();
		$("#id_employee").attr('disabled',false);
		$("#expired_date").attr('disabled',true);
		$("#remark_4").attr('disabled',true);
		$("#expired_date").parent().children('span').children('button').attr('disabled',true);
		$("#remark_4").parent().children('span').children('button').attr('disabled',true);
		$("#remark_5").attr('disabled',true);
		$("#filter").attr('disabled',true);
		$(".select_search_new").select2({
			placeholder: ':. FILTER OPTION .:'
		});
		$("#remark_8").attr('disabled',true);
		$("#remark_8").prop('checked',false).trigger('change');
	}
	
	$("#id_employee").attr('readonly','readonly');
	let deskripsiCategory = "";
	$("#id_category").change(function() {
		var categoryID = $(this).val();
		if (categoryID) {
			$(".select_search_new").empty();
			$(".select_search").empty();
			$("#id_employee").val(null).trigger('change');
			show_loading()
			get_new();
			const selectElement = document.getElementById('id_category');
			const selectedOption = selectElement.options[selectElement.selectedIndex];
			deskripsiCategory = selectedOption.getAttribute('data-attribute');
			$("#id_employee").removeAttr('readonly');
			if (deskripsiCategory == 'TA') {
				ta_category();
			}else if(deskripsiCategory == "ORIENT"){
				orient_category();
			}else if(deskripsiCategory == "MUTA"){
				mutation_category();
			}
			else if(deskripsiCategory == "GM"){
				gm_category();
			}else{
				failed_orient_category();
			}
		}else{
			$(".select_search").empty();
			$(".select_search_new").empty();
			$("#id_employee").val(null).trigger('change');
			$("#remark_8").attr('disabled',true);
			$("#remark_8").prop('checked',true).trigger('change');
			$("#tugas_tunjangan").show();
			$("#expired_date").parent().children('span').children('button').attr('disabled',false);
			document.getElementById('expired_date').disabled=false;
			document.getElementById('remark_1').disabled=false;
			$("#remark_4").parent().children('span').children('button').attr('disabled',false);
			document.getElementById('remark_4').disabled=false;
			document.getElementById('remark_5').disabled=false;
		}
	});
	$(document).on('change','#remark_8',function() {
		if ($(this).prop('checked')) {
			$(".select_search_new").val(null).trigger('change');
			if(deskripsiCategory == 'GM'){
				$("#filter").attr('disabled',true);
			}
			else if (deskripsiCategory == 'TA') {
				$("#filter").attr('disabled',false);
				$(".select_search_new").attr('readonly','readonly');
			}else{
				$(".select_search_new").removeAttr('readonly');
				$("#filter").attr('disabled',true);
			}
			$(".select_search_new").attr('readonly','readonly');
		}else{
			$(".select_search_new").val(null).trigger('change');
			$(".select_search_new").removeAttr('readonly');
			if (deskripsiCategory == 'TA' || deskripsiCategory == 'GM') {
				$("#filter").attr('disabled',true);
				$(".select_search_new").removeAttr('readonly');
			}else{
				$("#filter").attr('disabled',false);
				$(".select_search_new").attr('readonly','readonly');
			}
		}
	});
	$(document).on('change','#id_employee',function() {
		var employeeID = $(this).val();
		if (employeeID) {
			$(".select_search").empty();
			if ($("#id_category")) {
				if (deskripsiCategory == 'TA' || deskripsiCategory == 'GM') {
					if ($("#remark_8").prop('checked')) {

					}else{
						$("#filter").attr('disabled',true);
						change_employee_ta(employeeID);
					}	
				}else{
					$(".select_search_new").empty();
				}
			}
		}else{
			$(".select_search").empty();
			$(".select_search_new").empty();
			document.getElementById('filter').disabled=true;
		}
	});
	function change_region(regionID) {
		if (regionID) {
			show_loading();
			$.getJSON("{{ url('e-letter/internal_memo/internal_memo/change_region') }}"+"?id_region="+regionID,
				{id_url:global_url_server}, function(response) {
					if (response) {
						hide_loading();
						$("#id_branch_new").empty();
						$.each(response, function(key, value_branch) {
							$("#id_branch_new").append('<option value="'+value_branch.id_branch+'">' + value_branch.description +
								'</option>');
						});
						$("#id_branch_new").val(null).trigger('change');
					} else {
						$("#id_branch_new").empty();
					}
				}).fail(function(jqXHR, textStatus, errorThrown) {
					change_region(regionID);
				});
			} else {
				$("#id_branch_new").empty();
			}
		}
		$(document).on('change','#id_region_new',function() {
			var regionID = $(this).val();
			$("#id_branch_new").empty();
			$("#id_location_new").empty();
			if (regionID) {
				change_region(regionID);
			}
		});
		function change_branch(branchID) {
			if (branchID) {
				show_loading();
				$.getJSON("{{ url('e-letter/internal_memo/internal_memo/change_branch') }}"+"?id_branch="+branchID,
					{id_url:global_url_server}, function(response) {
						if (response) {
							hide_loading();
							$("#id_location_new").empty();
							$.each(response, function(key, value_location) {
								$("#id_location_new").append('<option value="'+value_location.id_location+'">' + value_location.description +
									'</option>');
							});
						} else {
							$("#id_location_new").empty();
						}
					}).fail(function(jqXHR, textStatus, errorThrown) {
						change_branch(branchID);
					});
				} else {
					$("#id_location_new").empty();
				}
			}
			$(document).on('change','#id_branch_new',function() {
				var branchID = $(this).val();
				$("#id_location_new").empty();
				if (branchID) {
					change_branch(branchID);
				}
			});
			$("#filter").click(function () {
				var employeeID = $("#id_employee").val();
				const selectElement = document.getElementById('id_category');
				const selectedOption = selectElement.options[selectElement.selectedIndex];
				const moreCategory = selectedOption.getAttribute('data-attribute');
				if (employeeID && moreCategory) {
					$("#modal_view_career").modal('show');
					if ($.fn.DataTable.isDataTable('#table_view_career')) {
						$('#table_view_career').DataTable().destroy();
					}
					$('#table_view_career').DataTable({
						processing: true,
						pageLength: 10,
						responsive: true,
						autoWidth: false,
						ajax: {
							url: "{{ url('e-letter/internal_memo/internal_memo/get_im_career') }}?employee_id="+employeeID+"&category_type="+moreCategory,
							error: function (jqXHR, textStatus, errorThrown) {
								$('#table_view_career').DataTable().ajax.reload();
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
						{ data: 'reference_number', name: 'reference_number' },
						{ data: 'name', name: 'name' },
						{ data: 'nik_employee', name: 'nik_employee' },
						{ data: 'type', name: 'type' },
						{ 
							data: 'dec_position_old', name: 'dec_position_old', 
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{ 
							data: 'dec_branch_old', name: 'dec_branch_old', 
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{
							data: 'dec_job_grade_old', name: 'dec_job_grade_old',
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{ 
							data: 'dec_position_new', name: 'dec_position_new',
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{ 
							data: 'dec_branch_new', name: 'dec_branch_new',
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{
							data: 'dec_job_grade_new', name: 'dec_job_grade_new',
							render: function (data, type, row) {
								return data !== null ? data : '-';
							}
						},
						{ data: 'status', name: 'status' },
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
					alert('Select Category and Employee')
				}
			});
			$("#modal_view_career").on('hidden.bs.modal', function () {
				if ($.fn.DataTable.isDataTable('#table_view_career')) {
					$('#table_view_career').DataTable().destroy();
				}
			});
			function change_employee_ta(employeeID) {
				if (employeeID) {
					show_loading();
					$.ajax({
						type: "GET",
						url: "{{url('e-letter/internal_memo/internal_memo/changeEmployeeCategoryTA')}}",
						data: {id_employee: employeeID},
						success: function(response) {
							if (response) {
								hide_loading();
								$("#id_career_transaction").empty();
								$.each(response, function(key, value) {
									$("#id_position_routing").val(value.id_routing_old);
									var decRegionOld = value.dec_region_old !== null ? value.dec_region_old : '-';
									var decBranchOld = value.dec_branch_old !== null ? value.dec_branch_old : '-';
									var decPositionOld = value.dec_position_old !== null ? value.dec_position_old : '-';
									var decDeptOld = value.dec_dept_old !== null ? value.dec_dept_old : '-';
									var decLocationOld = value.dec_location_old !== null ? value.dec_location_old : '-';
									var decDivisiOld = value.dec_divisi_old !== null ? value.dec_divisi_old : '-';
									$("#id_region_old").append('<option value="' + value.id_region_old + '">'+ decRegionOld +'</option>');
									$("#id_branch_old").append('<option value="' + value.id_branch_old + '">'+ decBranchOld +'</option>');
									$("#id_position_detail_old").append('<option value="' + value.id_position_detail_old + '">'+ decPositionOld +'</option>');
									$("#id_dept_old").append('<option value="' + value.id_dept_old + '">'+ decDeptOld +'</option>');
									$("#id_location_old").append('<option value="'+value.id_location_old+'">'+ decLocationOld +'</option>');
									$("#id_principal_old").append('<option selected value="' + decDivisiOld + '">'+ decDivisiOld +'</option>');
								});
							}
						},
						error: function(response) {
							change_employee_ta(employeeID);
						}
					});
				}
			}
			function change_employee(careerID) {
				if (careerID) {
					show_loading();
					$.ajax({
						type: "GET",
						url: "{{url('e-letter/decree/statement_letter/change_career')}}"+"?id_career_transaction="+careerID,
						success: function(response) {
							if (response) {
								hide_loading();
								$(".select_search_new").empty();
								$(".select_search").empty();
								$.each(response, function(key, value) {
									$("#id_career_transaction").append('<option value="' + value.id_career_transaction + '">'+ value.reference_number +'</option>');
									var decRegionOld = value.dec_region_old !== null ? value.dec_region_old : '-';
									var decBranchOld = value.dec_branch_old !== null ? value.dec_branch_old : '-';
									var decPositionOld = value.dec_position_old !== null ? value.dec_position_old : '-';
									var decDeptOld = value.dec_dept_old !== null ? value.dec_dept_old : '-';
									var decLocationOld = value.dec_location_old !== null ? value.dec_location_old : '-';
									var decDivisiOld = value.dec_divisi_old !== null ? value.dec_divisi_old : '-';
									$("#id_region_old").append('<option value="' + value.id_region_old + '">'+ decRegionOld +'</option>');
									$("#id_branch_old").append('<option value="' + value.id_branch_old + '">'+ decBranchOld +'</option>');
									$("#id_position_detail_old").append('<option value="' + value.id_position_detail_old + '">'+ decPositionOld +'</option>');
									$("#id_dept_old").append('<option value="' + value.id_dept_old + '">'+ decDeptOld +'</option>');
									$("#id_location_old").append('<option value="'+value.id_location_old+'">'+ decLocationOld +'</option>');
									$("#id_principal_old").append('<option selected value="' + decDivisiOld + '">'+ decDivisiOld +'</option>');
									var decRegionNew = value.dec_region_new !== null ? value.dec_region_new : '-';
									var decBranchNew = value.dec_branch_new !== null ? value.dec_branch_new : '-';
									var decPositionNew = value.dec_position_new !== null ? value.dec_position_new : '-';
									var decDeptNew = value.dec_dept_new !== null ? value.dec_dept_new : '-';
									var decLocationNew = value.dec_location_new !== null ? value.dec_location_new : '-';
									var decDivisiNew = value.dec_divisi_new !== null ? value.dec_divisi_new : '-';
									$("#id_region_new").append('<option value="' + value.id_region_new + '">'+ decRegionNew +'</option>');
									$("#id_branch_new").append('<option value="'+value.id_branch_new+'">'+ decBranchNew +'</option>');
									$("#id_position_detail_new").append('<option value="'+value.id_position_detail_new+'">'+ decPositionNew +'</option>');
									$("#id_dept_new").append('<option value="'+value.id_dept_new+'">'+ decDeptNew +'</option>');
									$("#id_location_new").append('<option value="'+value.id_location_new+'">'+ decLocationNew +'</option>');
									$("#id_principal_new").append('<option selected value="'+value.id_division_new+'">'+ decDivisiNew +'</option>');
								});
							}
						},
						error: function(response) {
							change_employee(careerID);
						}
					});
				}
			}
			$(document).on('click','.choose_career',function() {
				var careerID = $(this).attr('more_id');
				$("#modal_view_career").modal('hide');
				if (careerID) {
					$("#id_career_transaction").empty();
					change_employee(careerID);
				}
			});
			$("#id_position_routing_chief").select2();
			function change_chief(employeeID) {
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
						change_chief(employeeID);
					});
				} else {
					$("#id_position_routing_chief").empty();
				}
			}
			$(document).on('change','#id_employee_chief',function() {
				var employeeID = $(this).val();
				$("#id_position_routing_chief").empty();
				if (employeeID) {
					change_chief(employeeID);
				}
			});

			$(function () {
				$('#imForm').submit(function (e) {
					e.preventDefault();
					document.querySelector(".action").disabled=true;
					let formData = $(this).serializeArray();
					$(".invalid-feedback").children("strong").text("");
					$("#imForm input").removeClass("is-invalid");
					$("#imForm select").removeClass("custom-select");
					$.ajax({
						method: "POST",
						headers: {
							Accept: "application/json"
						},
						url: "{{ route('save.im') }}",
						data: formData,
						success: function (response) {
							document.querySelector(".action").disabled=false;
							if (response.status == 'true') {
								$("#imForm")[0].reset();
								$('#modal_form_im').modal('hide');
								swal({
									icon: 'success',
									title: 'Success',
									text: response.message
								});
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
