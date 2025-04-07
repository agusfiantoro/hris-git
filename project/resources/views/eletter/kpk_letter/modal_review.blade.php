<div class="row">
    <div class="col-12">
		<div class="card-body" style="padding:0.75rem 1.25rem 1.25rem 1.25rem">
			<div class="row">
				<div class="col-md-5">
					<div class="row">
						<label class="col-sm-4 col-form-label">Name / NIK</label>
						<div class="col-sm-8">
							<input type="hidden" name="id_performance_evaluation" id="id_performance_evaluation">
							<input id="emp_name" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<input id="dept" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Position</label>
						<div class="col-sm-8">
							<input id="pos" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Employment Status</label>
						<div class="col-sm-8">
							<input id="emp_status" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Division</label>
						<div class="col-sm-8">
							<input id="division" class="form-control form-control-sm" readonly>	
						</div>
					</div>
				</div>
				<div class="col-md-1">
				</div>
				<div class="col-md-5">
					<div class="row">
						<label class="col-sm-4 col-form-label">Region</label>
						<div class="col-sm-8">
							<input id="region" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Branch</label>
						<div class="col-sm-8">
							<input id="branch" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Grade</label>
						<div class="col-sm-8">
							<input id="grade" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">Direct Supervisor</label>
						<div class="col-sm-8">
							<input id="direct_spv" class="form-control form-control-sm" readonly>	
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Immediate Manager</label>
						<div class="col-sm-8">
							<input id="immediate_mgr" class="form-control form-control-sm" readonly>	
						</div>
					</div>
				</div>				
			</div>
			<hr>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Months Performance <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<div class="row" style="font-size:13px;">
										<div class="col-sm-4">
											<div class="row">
												<label class="col-sm-4 col-form-label">Month BA</label>
												<div class="col-sm-6">
													<input id="month_ba" class="form-control form-control-sm" readonly>	
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="row">
												<label class="col-sm-4 col-form-label" id="end_date">Pass Date</label>
												<div class="col-sm-7">
													<input id="pass_date" name="pass_date" class="form-control form-control-sm">
													<span class="invalid-feedback d-block" role="alert" id="pass_dateError">
														<strong></strong>
													</span>
												</div>
											</div>
										</div>
										<div class="col-sm-4">
											<div class="row">
												<div class="col-sm-12" style="text-align:right;">
													<button type="button" class="btn btn-xs btn-primary" id="new_rec_detail" style="display:none;"><span class="fas fa-plus"></span> Add Months Performance</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_detail" style="width:1200px;" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:14px;">
												<th style="width:50px;">No.</th>
												<th style="width:150px;">Period</th>
												<th style="width:150px;">Review Date</th>
												<th style="width:120px;">Target</th>
												<th style="width:120px;">Actual</th>
												<th style="width:50px;">Index (%)</th>
												<th style="width:120px;">Decision</th>
												<th style="width:120px;">Treatment</th>
												<th style="width:50px;">Action</th>
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
	id_kpk = {!! $global_kpk !!};
	date_review = '{!! $global_date !!}';
	id_position_detail = {!! $global_position !!};
	id_employee = {!! $global_employee !!};
	
	$('#pass_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});	
	
	$('#pass_date').attr('readonly',true).css('pointer-events','none').css('touch-action','none')
	$("#pass_date").parent().children('span').children('button').attr('disabled',true);
	
	if(id_kpk != 0){
		get_edit(id_kpk);
	}	
});

function get_edit(id_kpk) {
	$.ajax({
		url: "<?= url('e-letter/performance_plan/performance_review/get_edit_review') ?>",
		method: "GET",
		data: {
			id_kpk: id_kpk,
			id_position_detail: id_position_detail,
			id_employee: id_employee
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			$('#id_performance_evaluation').val(response.id_performance_evaluation).trigger('change');
			$('#emp_name').val(response.name).trigger('change');
			$('#dept').val(response.department).trigger('change');
			$('#pos').val(response.position_routing).trigger('change');
			$('#emp_status').val(response.employment_status).trigger('change');
			$('#division').val(response.principal).trigger('change');
			$('#region').val(response.regional).trigger('change');
			$('#branch').val(response.branch).trigger('change');
			$('#grade').val(response.job_grade).trigger('change');
			$('#direct_spv').val(response.parent_emp_name).trigger('change');
			$('#immediate_mgr').val(response.indirect_emp_name).trigger('change');
			$('#month_ba').val(response.month_ba).css('font-weight','bold').trigger('change');
			
			$('#pass_date').val(response.pass_date).trigger('change');
			if(response.check == true){
				$('#new_rec_detail').show();
			}
			if(response.review.length > 0 ){
				global_id_rec_detail = 0;
				$.each(response.review, function (i, item) {
					$('#new_rec_detail').trigger('click',[true]);								
				});	
				setTimeout(function () {
					$('#table_rec_body tr').each(function (index) {
						moment.locale('id');
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_performance_review_input').val(response.review[index].id_performance_review);
						$(this).find('.period_input').val(moment(response.review[index].period_date).format('MMMM YYYY')).trigger('change');
						$(this).find('.period_input').attr('readonly',true).css('pointer-events','none').css('touch-action','none');
						$(this).find('.period_input').attr('format_date',response.review[index].period_date);
						$(this).find('.period_input').parent().children('span').children('button').attr('disabled',true);
						$(this).find('.review_date_input').val(response.review[index].review_date).trigger('change');
						var cur_month = moment().format('M');
						var per_date = moment(response.review[index].period_date,'YYYY-M-D').format('M');
						if(response.review[index].review_date != null && cur_month != per_date){
							$(this).find('.review_date_input').attr('readonly',true).css('pointer-events','none').css('touch-action','none');
							$(this).find('.review_date_input').parent().children('span').children('button').attr('disabled',true);
							$(this).find('.id_target_input').attr('readonly',true);
							$(this).find('.act_input').attr('readonly',true);
							$(this).find('.decision_input').attr('readonly',true);
							$(this).find('.treatment_input').attr('readonly',true);
						}
						$(this).find('.id_target_input').val(response.review[index].target_volume).trigger('change');
						$(this).find('.act_input').val(response.review[index].result_value).trigger('change');
						$(this).find('.decision_input').val(response.review[index].decision).trigger('change',['pass']);
						$(this).find('.treatment_input').val(response.review[index].treatment).trigger('change');
						var treat_pdf = ['bulan1','sp1','sp2','sp3','pass','phk','phkspdt'];
						var treat_no = ['resign','demosi'];
						if(response.review[index].review_date != null && (treat_pdf.includes(response.review[index].treatment))){
							var getPdf = "get_pdf("+response.review[index].id_performance_review+",'"+response.review[index].review_date+"','"+response.review[index].period_date+"')";
							$(this).find('.pdf_input').html('<button type="button" target="_blank" name="print" onclick='+getPdf+' class="print btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></button>');
						}
						else if(treat_no.includes(response.review[index].treatment)){
							$(this).find('.pdf_input').html('-');
						}
					});	
				}, 500);
			}
			else{
				global_id_rec_detail = 0;
				moment.locale('id');
				let start = moment(date_review);
				let nextMonths = [];
				for (let i = 0; i <= 4; i++) {
					$('#new_rec_detail').trigger('click',[true]);	
					nextMonths.push(start.format('MMMM YYYY'));
					start.add(1, 'months');
					$('span#review_'+i+'_sn').html(i+1);
					$('#review_'+i+'_period').val((start.format('MMMM YYYY'))).trigger('change');
					$('#review_'+i+'_period').attr('readonly',true).css('pointer-events','none').css('touch-action','none');
					$(".period_input").parent().children('span').children('button').attr('disabled',true);
				}
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