<div class="modal fade" id="modal_form_swp"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Form Warning Letter 3 & SPDT</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form method="POST" id="swpForm" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="row">
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Category <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_category" name="id_category" class="form-control form-control-sm select_opsi" style="width: 100%;">
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_categoryError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Letter Date <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
									<span class="invalid-feedback d-block" role="alert" id="dateError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Name <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_employee" disabled="" name="id_employee" class="form-control form-control-sm select_opsi" style="width: 100%;">
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_employeeError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Effective Date <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
									<span class="invalid-feedback d-block" role="alert" id="effective_dateError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">End Date <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<input autocomplete="off" name="expired_date" id="expired_date" class="form-control form-control-sm" style="width: 100%;">
									<span class="invalid-feedback d-block" role="alert" id="expired_dateError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Email <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<input autocomplete="off" name="email" id="email" class="form-control form-control-sm" style="width: 100%;">
									<span class="invalid-feedback d-block" role="alert" id="emailError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Pemberi Keputusan <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_employee_chief" name="id_employee_chief" class="form-control form-control-sm select_opsi" style="width: 100%;">
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_employee_chiefError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Letter Location <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="remark_5" name="remark_5" class="form-control form-control-sm select_opsi" style="width: 100%;">
									</select>
									<span class="invalid-feedback d-block" role="alert" id="remark_5Error">
										<strong></strong>
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<label class="col-sm-4 col-form-label">Region <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_region" name="id_region" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_regionError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Branch <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_branch" name="id_branch" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_branchError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Position <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_position_detail" name="id_position_detail" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_position_detailError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Department <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_dept" name="id_dept" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_deptError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Job Grade <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_job_grade" name="id_job_grade" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_job_gradeError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Division <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_principal" name="id_principal[]" multiple="" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_principalError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Status <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_employment_status" name="id_employment_status" class="form-control form-control-sm select_search" style="width: 100%;" readonly>
									</select>
									<span class="invalid-feedback d-block" role="alert" id="id_employment_statusError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Jabatan Pemberi Keputusan <sup class="text text-danger">*</sup></label>
								<div class="col-sm-8">
									<select id="id_position_routing_chief" name="id_position_routing_chief" class="form-control form-control-sm" style="width: 100%;" readonly></select>
									<span class="invalid-feedback d-block" role="alert" id="id_position_routing_chiefError">
										<strong></strong>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6 mt-2">
							<label>Kejadian <sup class="text text-danger">*</sup></label>
							<textarea class="form-control" name="remark_3" id="remark_3" rows="4"></textarea>
							<span class="invalid-feedback d-block" role="alert" id="remark_3Error">
								<strong></strong>
							</span>
						</div>
						<div class="col-lg-6 mt-2">
							<label>Pelanggaran <sup class="text text-danger">*</sup></label>
							<textarea class="form-control" name="remark_4" id="remark_4" rows="4"></textarea>
							<span class="invalid-feedback d-block" role="alert" id="remark_4Error">
								<strong></strong>
							</span>
						</div>
						<div class="col-lg-6 mt-2">
							<label>Mengetahui</label>
							<input autocomplete="off" type="text" name="remark_1" id="remark_1" class="form-control form-control-sm" style="width: 100%;">
						</div>
						<div class="col-lg-6 mt-2">
							<label>Jabatan Mengetahui</label>
							<input autocomplete="off" type="text" name="remark_2" id="remark_2" class="form-control form-control-sm" style="width: 100%;">
						</div>
					</div>
				</div>
				<div class="modal-loading" id="modal-loading" style="display: none;">
					<span class="fa fa-spinner fa-spin fa-3x"></span>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
					<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>