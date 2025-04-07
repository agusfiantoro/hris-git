<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Requested By</label>
						<div class="col-sm-8">
							<input name="id_talent_assessment_request" id="id_talent_assessment_request" type="hidden">	
							<select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
							</select>
							<span class="invalid-feedback" role="alert" id="id_employee_requestError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<input type="text" id="by_pos" class="form-control form-control-sm" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Branch</label>
						<div class="col-sm-8">
							<input type="text" id="branch" class="form-control form-control-sm" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Grade</label>
						<div class="col-sm-8">
							<input type="text" id="grade" class="form-control form-control-sm" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Notes</label>
						<div class="col-sm-8">
							<textarea id="notes" name="notes" class="form-control form-control-sm"></textarea>
							 <span class="invalid-feedback" role="alert" id="notesError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">	
					<div class="row">
						<label class="col-sm-4 col-form-label">PIC HR</label>
						<div class="col-sm-8">
							<select name="cc_email[]" id="cc_email" class="form-control form-control-sm select2" data-placeholder="Select HR ..." style="width: 100%;" multiple="multiple">
							</select>
							<span class="invalid-feedback" role="alert" id="cc_emailError">
								<strong></strong>
							</span>
						</div>
					</div>
					<!-- div class="row">
						<label class="col-sm-4 col-form-label">Hierarchy Approval</label>
						<div class="col-sm-8">
							<select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
							<span class="invalid-feedback" role="alert" id="id_approvalError">
								<strong></strong>
							</span>
						</div>
					</div -->
					<div class="row" style="margin-top:4px;">
						<label class="col-sm-4 col-form-label">Approved By</label>
						<div class="col-sm-8">
							<select name="id_approval_request" id="id_approval_request" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
							<span class="invalid-feedback" role="alert" id="approval_nameError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Approval Status</label>
						<div class="col-sm-8">
							<select name="id_approval_status" id="id_approval_status" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
							<span class="invalid-feedback" role="alert" id="id_approval_statusError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>	
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
	
	id_talent = {!! $global_talent !!};
			
	if(id_talent != 0){
	//	console.log(id_talent);
		get_edit(id_talent);
	}
	else{
		get_employee_by().then(function(value) {
		//	get_hierachy_talent().then(function(value_res) {
				get_all_hierachy(global_id_employee);
		//	});
		})	
		
		get_approval_status();
		get_hr_email();
	}

});	

function get_edit(id_talent) {
	$.ajax({
		url: "<?= url('talent_management/talent_development/assessment_request/get_talent_edit') ?>",
		method: "GET",
		data: {id_talent: id_talent},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			$('#id_talent_assessment_request').val(response.id_talent_assessment_request).trigger('change');
			$('#id_employee_request').select2({
				data: [{id:response.id_employee_request,text:response.emp_request}],
			});	
			$('#by_pos').val(response.pos_route).trigger('change');
			$('#branch').val(response.branch).trigger('change');
			$('#grade').val(response.grade).trigger('change');
			$('#id_approval_request').select2({
				data: [{id:response.id_approval_request,text:response.name_emp_approval}],
			});	
		//	$('#id_approval').val(response.id_approval).trigger('change');
			get_approval_status().then(function(value) {
				$('#id_approval_status').val(response.id_approval_status).trigger('change');			
			});
			$('#notes').val(response.notes).trigger('change');
			get_hr_email().then(function(value) {
				$('#cc_email').val(response.cc_email).trigger('change');
			});
			
			if(response.code_app_status == 'Request_Approval' || response.code_app_status == 'Approved' || response.code_app_status == 'Partial_Approved' || response.code_app_status == 'Cancel' || response.code_app_status == 'Rejected'){
					//	global_view = "view";
						setTimeout(function () {
							$("#talentForm .modal-title").html("<span class='fas fa-eye'></span> View Assessment Request");
							$("#talentForm input").attr("disabled", true);
							$("#talentForm input").prop("disabled", true);
							$("#talentForm select").prop("disabled", true);
							$("#talentForm textarea").prop("disabled", true);
							$("#save_and_submit").hide();
							$("#edit_button").hide();
						}, 500);
					}
					else{
						$("#talentForm .modal-title").html("<span class='fas fa-edit'></span> Edit Assessment Request");
						$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
											
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
				text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
			});
		}
	});	
};

</script>