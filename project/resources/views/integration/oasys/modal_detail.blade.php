<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">NIK</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="nik" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Name</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="name_emp" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="position" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Grade</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="grade" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="dept" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Principal</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="principal" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
				</div>
				<div class="col-md-6">					
					<div class="row">
						<label class="col-sm-4 col-form-label">Region</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="region" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Branch</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="branch" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Location</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="location" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Direct Supervisor</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="direct_spv" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Immediate Manager</label>
						<div class="col-sm-8">
							<input autocomplete="off" id="imm_manager" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Transaction <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail"><span class="fas fa-plus"></span> Add Transaction</button>
									<button type="button" class="pull-right btn btn-xs btn-success" id="new_sync" onclick="sync('{!! $nik_employee !!}')"  style="margin-right:10px;"><span class="fa fa-refresh"></span> Syncron</button>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_detail" style="width:1200px" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:14px;">
												<th style="white-space:nowrap;">No.</th>
												<th style="white-space:nowrap;">Bgen Branch</th>
												<th style="white-space:nowrap;">Bgen Principal</th>
												<th>Status</th>
												<th>Sync</th>
												<th>Sync Message</th>
												<th>Note Revised</th>
												<th>Note Rejected</th>
												<th>Approval Hierarchy</th>
												<th>Approval Status</th>
												<th style="white-space:nowrap;">Action</th>
											</tr>
										</thead>
										<tbody id="table_rec_body">
										</tbody>
									</table>
									<div class="col-sm-12">
										<span class="table-invalid-feedback text-red" role="alert" id="table_rec_detailError">
											<strong></strong>
										</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">

$(document).ready(function(){
	
	nik_employee = '{!! $nik_employee !!}';
	type = '{!! $type !!}';
	get_category();
	get_branch();
	get_principal();
//	get_position();
//	get_location();

	get_edit(nik_employee,type);
});

function get_edit(nik_employee,type) {
	global_approval = 'edit';
	$.ajax({
		url: "{{ route('oasys.get_edit') }}",
		method: "GET",
		data: {nik_employee: nik_employee},
		beforeSend: function () {
			get_direct_spv(global_employee);
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			global_id_rec_detail = 0;
			$.each(response.bgen, function (i, item) {
				$('#new_rec_detail').trigger('click');
			});
			$('#nik').val(response.nik_employee).trigger('change');
			$('#name_emp').val(response.name).trigger('change');
			$('#position').val(response.position_routing).trigger('change');
			$('#grade').val(response.job_grade).trigger('change');
			$('#dept').val(response.department).trigger('change');
			$('#principal').val(response.principal).trigger('change');
			$('#region').val(response.regional).trigger('change');
			$('#branch').val(response.branch).trigger('change');
			$('#location').val(response.work_location).trigger('change');
			$('#direct_spv').val(response.direct_spv).trigger('change').css('width', '100%').css('min-width', '100px');
			$('#imm_manager').val(response.imm_manager).trigger('change');
			global_id_route = response.id_position_routing;
			global_id_location = response.id_location;
			global_desc_route = response.position_routing;
			global_desc_location = response.work_location;
			setTimeout(function () {
				$('#table_rec_body tr').each(function (index) {
					$(this).find('.delete-record').hide();
					$(this).find('span.sn').html(index + 1);
					$(this).find('.id_integration_sales_code_input').val(response.bgen[index].id_integration_sales_code);
					$(this).find('.id_transition_category_input').val(response.bgen[index].id_transition_category).trigger('change', [true]);
					$(this).find('.cat_code_input').val(response.bgen[index].cat_code).trigger('change');
					$(this).find('.bgen_branch_input').val(response.bgen[index].id_branch).trigger('change').attr('readonly', true);
					$(this).find('.bgen_principal_input').val(response.bgen[index].id_principal).trigger('change').attr('readonly', true);
					$(this).find('.bgen_position_input').val(response.bgen[index].id_position_route_destination).trigger('change');
					if(response.bgen[index].desc_route != response.position_routing) {
						$(this).find('.bgen_desc_position_input').css('color', '#E00');
					}
					$(this).find('.bgen_desc_position_input').html(response.bgen[index].desc_route);
					$(this).find('.bgen_location_input').val(response.bgen[index].id_location_destination).trigger('change');
					$(this).find('.bgen_desc_location_input').html(response.bgen[index].desc_location);
					if(response.bgen[index].desc_location != response.work_location) {
						$(this).find('.bgen_desc_location_input').css('color', '#E00');
					}
					$(this).find('.bgen_status_input').val(response.bgen[index].status).trigger('change');
					$(this).find('.id_direct_spv_input').select2({data:global_direct}).val(response.bgen[index].id_direct).trigger('change');
					if(response.bgen[index].is_synchronize_flag == true){
						$(this).find('.bgen_sync_input').addClass("badge badge-success").html('<span class="fa fa-check fa-lg"></span>');
					}
					else{
						$(this).find('.bgen_sync_input').addClass("badge badge-danger").html('<span class="fa fa-ban fa-lg"></span>');
					}
					$(this).find('.sales_code_input').html(response.bgen[index].sales_code);
					$(this).find('.direct_spv_input').html(response.bgen[index].parent_sales_code);
					$(this).find('.sync_message_input').text(response.bgen[index].synchronize_message);
					$(this).find('.note_revised_input').html(response.bgen[index].note_revised);
					$(this).find('.note_rejected_input').html(response.bgen[index].note_rejected);
				//	console.log(response.bgen[index].id_approval);
					if(response.bgen[index].id_approval != null){
						let stts = "";
					//	$(this).find('.id_approval_input').hide();
					//	$(this).find('.id_approval_request_input').hide();
					//	$(this).find('.app_status_input').hide();
					//	$(this).find('.group_id_approval').html(response.bgen[index].app_hierarchy);
					//	$(this).find('.group_id_approval_request').html(response.bgen[index].direct_mgr);
					//	$(this).find('.group_id_direct_spv').html(response.bgen[index].direct_spv);
						// if(response.bgen[index].cat_code == 'Join'){
							$(this).find('.id_transition_category_input').attr('readonly',true);
							let thisx = $(this);
							if(response.bgen[index].app_code == 'Approved'){
								stts = "success";
								$(this).find('.bgen_branch_input').attr('readonly',true);
								$(this).find('.bgen_principal_input').attr('readonly',true);
							//	$(this).find('.add-record').hide();
								$(this).find('.cancel-record').hide();
								$(this).find('.group_id_approval').html(response.bgen[index].app_hierarchy);
								$(this).find('.id_approval_request_input').attr('readonly',true);							
							//	$(this).find('.group_id_approval_request').html(response.bgen[index].direct_mgr);
							}
							else if(response.bgen[index].app_code == 'Cancel' || response.bgen[index].app_code == 'Rejected'){
								stts = "danger";
								$(this).find('.bgen_branch_input').attr('readonly',false);
								$(this).find('.bgen_principal_input').attr('readonly',false);
								$(this).find('.bgen_status_input').attr('readonly',false);
								$(this).find('.id_approval_request_input').attr('readonly',false);
								$(this).find('.id_direct_spv_input').attr('readonly',false);
								$(this).find('.add-record').show();
								$(this).find('.cancel-record').hide();
							}
							else if(response.bgen[index].app_code == 'Revised'){
								stts = "info";
								$(this).find('.id_approval_input').val(response.bgen[index].id_approval).trigger('change');
								$(this).find('.app_code_input').val(response.bgen[index].app_code).trigger('change');
								$(this).find('.add-record').show();
								$(this).find('.cancel-record').show();
							}
							else{
								stts = "secondary";
								$(this).find('.bgen_branch_input').attr('readonly',true);
								$(this).find('.bgen_principal_input').attr('readonly',true);
								$(this).find('.bgen_status_input').attr('readonly',true);
								$(this).find('.id_direct_spv_input').attr('readonly',true);
								$(this).find('.add-record').hide();
								$(this).find('.cancel-record').show();
								$(this).find('.group_id_approval').html(response.bgen[index].app_hierarchy);
							//	$(this).find('.group_id_approval_request').html(response.bgen[index].direct_mgr);
								$(this).find('.id_approval_request_input').attr('readonly',true);							
							}
							get_approval_by(global_employee).then(function(value) {
								thisx.find('.id_approval_request_input').val(response.bgen[index].id_employee_approval).trigger('change');
							});
							$(this).find('.group_app_status').addClass("badge badge-"+stts).css({"font-size":"12px","padding":"6px"}).html(response.bgen[index].app_status);
						// }
					//	$(this).find('.id_approval_input').val(response.bgen[index].id_approval).trigger('change');
					//	$(this).find('.id_approval_request_input').val(response.bgen[index].id_employee_approval).trigger('change');
					//	$(this).find('.app_status_input').val(response.bgen[index].id_approval_status).trigger('change');
					}
					else{
						$(this).find('.id_approval_input').attr('disabled',true);
						$(this).find('.id_approval_request_input').attr('disabled',true);
						$(this).find('.group_id_approval_request').hide();
						$(this).find('.app_status_input').attr('disabled',true);		
					}
					
					if(type == 'view'){
						$(this).find('.bgen_branch_input').attr('readonly',true);
						$(this).find('.bgen_principal_input').attr('readonly',true);
						$(this).find('.bgen_status_input').attr('readonly',true);
						$(this).find('.id_direct_spv_input').attr('readonly',true);
						$(this).find('.cancel-record').hide();
						$(this).find('.add-record').hide();
					} 
				 });			
			}, 1000);
			
			
			if(type == 'view'){
				$("#new_sync").css("display","none");
				$("#new_rec_detail").css("display","none");
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	
		error: function (xhr) {
			swal({
				icon: 'error',
				title: 'Oops...',
				dangerMode: true,
				text: 'Something went wrong!'
			});
		}
	});
};	

</script>