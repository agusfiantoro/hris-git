<div class="row">
    <div class="col-12">
		<div class="card-body">
			<div class="row">						
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Letter Date</label>
						<div class="col-sm-8">
							<input type="hidden" name="id_letter" id="id_letter">
							<input autocomplete="off" name="date" id="date" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-feedback d-block" role="alert" id="dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">P2K Month</label>
						<div class="col-sm-8">
							<input autocomplete="off" name="performance_date" id="performance_date" class="form-control form-control-sm" style="width: 100%;">
							<input type="hidden" name="id_date" id="id_date">
							<span class="invalid-feedback d-block" role="alert" id="performance_dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">On Performance (P3M)</label>
						<div class="col-sm-8">
							<input autocomplete="off" name="on_performance" id="on_performance" class="form-control form-control-sm" style="width: 100%;" readonly>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Company Type</label>
						<div class="col-sm-8">
							<select name="com_type" id="com_type" class="form-control form-control-sm select2" data-placeholder="Select Company Type ..." style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="com_typeError">
								<strong></strong>
							</span>   									
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Department</label>
						<div class="col-sm-8">
							<input type="hidden" name="dept_code" id="dept_code">
							<select name="dept" id="dept" class="form-control form-control-sm select2" data-placeholder="Select Department ..." style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="deptError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row" id="idDivision">
						<label class="col-sm-4 col-form-label">Division</label>
						<div class="col-sm-8">
							<select name="division" id="division" class="form-control form-control-sm select2" data-placeholder="Select Division ..." style="width: 100%;">
							</select>
							<span class="invalid-feedback" role="alert" id="divisionError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="row">
						<label class="col-sm-4 col-form-label">Effective Date</label>
						<div class="col-sm-8">
							<input autocomplete="off" name="effective_date" id="effective_date" class="form-control form-control-sm" style="width: 100%;">
							<span class="invalid-feedback d-block" role="alert" id="effective_dateError">
								<strong></strong>
							</span>
						</div>
					</div>
					
					<div class="row" style="margin-bottom:5px;">
						<label class="col-sm-4 col-form-label">Day/Date Text</label>
						<div class="col-sm-8">
							<textarea type="text" name="date_text" id="date_text" placeholder="Jumat tanggal sembilan belas, bulan April tahun dua ribu dua puluh empat" class="form-control form-control-sm" rows="2" readonly></textarea>
							<span class="invalid-feedback" role="alert" id="date_textError">
								<strong></strong>
							</span>
						</div>
					</div>
					
					<div class="row">
						<label class="col-sm-4 col-form-label">Created By</label>
						<div class="col-sm-8">
							<select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
							</select>
							<input type="hidden" name="id_routing" id="id_routing">
							<input type="hidden" name="id_position_detail" id="id_position_detail">
							<input type="hidden" name="id_dept" id="id_dept">
							<input type="hidden" name="id_branch" id="id_branch">
							<input type="hidden" name="id_region" id="id_region">
							<span class="invalid-feedback" role="alert" id="id_employee_requestError">
								<strong></strong>
							</span>
						</div>
					</div>
					<div class="row">
						<label class="col-sm-4 col-form-label">Status</label>
						<div class="col-sm-8">
							<select name="status" id="select2status" class="form-control form-control-sm">
								<option value="A">Active</option>
								<option value="I">Inactive</option>
							</select>
							<span class="invalid-feedback" role="alert" id="statusError">
								<strong></strong>
							</span>
						</div>
					</div>
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-tabs" id="tab_rec_detail" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="link_tab_rec-details" data-toggle="pill" href="#rec-details" role="tab" aria-controls="link_tab_rec-details" aria-selected="true">Participant <span class="error-tab text-red"></span></a>
						</li>
					</ul>
					<div class="tab-content" id="tab_rec_detail_content" style="font-size:12px">
						<div class="tab-pane fade show active" id="rec-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
							<br/>
							<div class="row">
								<div class="col-md-12" style="margin-bottom: 10px">
									<button type="button" class="pull-right btn btn-xs btn-primary" id="new_rec_detail"><span class="fas fa-plus"></span> Add Participant</button>
									<button type="button" id="upload" class="new_upload pull-right btn btn-xs btn-primary" style="margin-right:10px;display:none;"><i class="fas fa-upload"></i> Import Participant</button>
								</div>
								<div class="col-md-12" style="overflow:auto;">
									<table id="table_rec_detail" style="width:1600px" class="table table-striped table-bordered table-hover datatable">
										<thead>
											<tr align="center" style="font-size:14px;">
												<th style="white-space:nowrap;">No.</th>
												<th style="white-space:nowrap;">Name (NIK)</th>
												<th>Group</th>
												<th style="white-space:nowrap;">Position</th>
												<th style="white-space:nowrap;">Region</th>
												<th style="white-space:nowrap;">Branch</th>
												<th>Direct Supervisor</th>
												<th style="width:80px;" class="th-threshold-kpi">Threshold KPI</th>
												<th style="width:80px;" class="th-act-idx" style="display:none;">-</th>
												<th style="width:100px;">AP3M KPI</th>
												<th id="th-m1" style="white-space:nowrap;">Month 1</th>
												<th id="th-m2" style="white-space:nowrap;">Month 2</th>
												<th id="th-m3" style="white-space:nowrap;">Month 3</th>
												<th id="th-m4" style="white-space:nowrap;">Month 4</th>
												<th id="th-m5" style="white-space:nowrap;">Month 5</th>
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
	get_employee_by();
	get_division();
	
	$('#select2status').select2({width:'100%'});	
	$('#date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
		minDate: moment().startOf('month').format('YYYY-MM-DD'),
	});	
	
	moment.locale('id');
	var dates = moment().format('MMMM YYYY');
	var def_date = $('#performance_date').val();
	$('#performance_date').datepicker({
		locale: 'de-de',
		uiLibrary: 'bootstrap4',
		format: 'mmmm yyyy',
		minDate: dates,
	}).on('change', function (e) {
		var old_date = def_date;
		def_date = $('#performance_date').val();
		var new_date = $('#performance_date').val();
		
		if(old_date != new_date){
			$('#com_type').val('').trigger('change');
			$('#com_type').empty();
		}		
		global_com = com_type;
		get_company_type();
		var split_pf = $('#performance_date').val().split(' ');
		moment.locale('id');
		
		if(split_pf[0] == 'Desember'){
			var mo_pf = 'December';
			var pf_month = '01 '+mo_pf+' '+split_pf[1];
		}
		else if(split_pf[0] == 'Mei'){
			var mo_pf = 'May';
			var pf_month = '01 '+mo_pf+' '+split_pf[1];
		}
		else if(split_pf[0] == 'Agustus'){
			var mo_pf = 'August';
			var pf_month = '01 '+mo_pf+' '+split_pf[1];
		}
		else if(split_pf[0] == 'Oktober'){
			var mo_pf = 'October';
			var pf_month = '01 '+mo_pf+' '+split_pf[1];
		}
		else{
			var pf_month = '01 '+split_pf[0]+' '+split_pf[1];
		}
		
		let pf_start = moment(pf_month).format('MM YYYY');
		var split_final = pf_start.split(' ');
		global_month = split_final[0];
		global_year = split_final[1];
		
		moment.locale('en');
		let monthEnglish = moment.months();
		moment.locale('id');
		let monthIndonesian = moment.months();
		let selectedDate = '01 '+$('#performance_date').val()
		monthIndonesian.forEach((monthId, i) => {
			monthEnglish.forEach((monthEn, j) => {
				if(i == j) {
					selectedDate = selectedDate.replace(monthId, monthEn);
				}
			});
		})
		

		let start = moment(selectedDate);
		let nextMonths = [];
		start.subtract(1, 'months');
		for (let i = 1; i <= 3; i++) {
			nextMonths.push(start.format('MMM YY'));
			start.subtract(1, 'months');
		}
		let months = '';
		nextMonths.reverse().forEach((month, i) => {
			months += i < nextMonths.length - 1 ? `${month}, ` : month;
		});
		$('#on_performance').val(months);	
	});
	
	moment.locale('id');
	var dates_eff = moment().startOf('month').format('dddd, D MMMM YYYY');
	$('#effective_date').datepicker({
		locale: 'de-de',
		uiLibrary: 'bootstrap4',
		format: 'dddd, dd mmmm yyyy',
		minDate: dates_eff,
	}).on('change', function (e) {
		let isoDate = $('#effective_date').val();
		moment.locale('id');
	//	let eff_date = $('#effective_date').val(moment($('#effective_date').val()).format('dddd, D MMMM YYYY'));
		var split = $('#effective_date').val().split(' ');
		$('#date_text').val(split[0].replaceAll(",", "")+', tanggal '+spellNumber(split[1]).trim()+', bulan '+split[2]+' tahun '+spellNumber(split[3]).trim());		
		
		if(split.length == 1){
			$('#date_text').val('');
		}
		else{
			if(split[2] == 'Desember'){
				var mo = 'December';
				var val_month = split[1]+' '+mo+' '+split[3];
			}
			else if(split[2] == 'Mei'){
				var mo = 'May';
				var val_month = split[1]+' '+mo+' '+split[3];
			}
			else if(split[2] == 'Agustus'){
				var mo = 'August';
				var val_month = split[1]+' '+mo+' '+split[3];
			}
			else if(split[2] == 'Oktober'){
				var mo = 'October';
				var val_month = split[1]+' '+mo+' '+split[3];
			}
			else{
				var val_month = split[1]+' '+split[2]+' '+split[3];
			}
			
			let start = moment(val_month);
			let nextMonths = [];
			for (let i = 1; i <= 5; i++) {
				nextMonths.push(start.format('MMM YY'));				
				$(`#th-m${i}`).text(`${start.format('MMM YY')}`);
				start.add(1, 'months');
			}
		}
	});	
	
	if(id_kpk != 0){
		get_company_type();
		get_edit(id_kpk);
	}
	else{
		$('.th-act-idx').hide();
		get_company_type();
	}
	
	$('#upload').click(function(){
        $('#uploadModal').modal('show');
        $('.modal-backdrop').css('z-index','1053');		
    });

    $('#submit_upload').click(function(){
        upload();
    });
	
	$('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html('<i>'+file.name+'</i>');
    });	
	
	$('#close_upload').click(function(){
        $('.modal-backdrop').css('z-index','1049');
        $('#myModal').css('overflow', 'none');
    });

});	

function get_edit(id_kpk) {
	$.ajax({
		url: "<?= url('e-letter/performance_plan/kpk_letter/get_edit') ?>",
		method: "GET",
		data: {id_kpk: id_kpk},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			global_id_rec_detail = 0;
			$.each(response.kpk, function (i, item) {
				$('#new_rec_detail').trigger('click');								
			});	
			if(global_name == 'view'){
				$("#kpkForm input").attr('disabled',true);
				$("#kpkForm select").attr('readonly',true);
				$("#kpkForm textarea").attr('disabled',true);
				$("#date, #performance_date, #effective_date").parent().children('span').children('button').attr('disabled',true);
				$('#new_rec_detail').hide();
			}
			else{
				$('#dept').attr('readonly',true);
				$('#com_type').attr('readonly',true);
			}
			$('#id_letter').val(response.id_letter).trigger('change');
			$('#date').val(response.date).trigger('change');
			$('#performance_date').val(response.remark_1).trigger('change');
			$('#on_performance').val(response.remark_2).trigger('change');
			$('#dept_code').val(response.department_code).trigger('change');
			$('#com_type').val(response.remark_6).trigger('change',[true]);
			$('#division').val(response.remark_7).trigger('change');
			get_dept(response.remark_6).then(function(res) {
				$('#dept').val(response.remark_3).trigger('change',[true]);				
				get_global_departments();
				get_participant(response.remark_6,response.remark_3,global_month,global_year,global_name).then(function(res) {
					setTimeout(function () {
						$('#table_rec_body tr').each(function (index) {
							$(this).find('span.sn').html(index + 1);
							if(global_name == 'view'){
								$(this).find('.delete-record').hide();
							}
							$(this).find('.id_performance_evaluation_input').val(response.kpk[index].id_performance_evaluation);
							$(this).find('.id_employee_input').val(response.kpk[index].id_employee).trigger('change',[true]);
							
							get_pos_detail($(this),response.kpk[index].id_position_detail).then(function(res) {
							});
							
							var th = $(this);
							get_threshold($(this).find('.id_employee_input'),response.kpk[index].id_employee,response.kpk[index].id_position_detail,global_name).then(function(res) {
								if(response.department_code != '170_SAL'){
									th.find('.id_group_input').val(response.kpk[index].id_threshold_group).trigger('change');
								}
							});
							$(this).find('.obj_input').html(response.kpk[index].val_threshold);
							$(this).find('.act_kpi_input').val(response.kpk[index].kpi_value).trigger('change');
							$(this).find('.act_idx_input').val(response.kpk[index].index_sales_percentage).trigger('change');
							if(response.department_code == '170_SAL'){
								$(this).find('.month_1_input').val(response.kpk[index].sales_offtake[0]).trigger('change');
								$(this).find('.month_2_input').val(response.kpk[index].sales_offtake[1]).trigger('change');
								$(this).find('.month_3_input').val(response.kpk[index].sales_offtake[2]).trigger('change');
								$(this).find('.month_4_input').val(response.kpk[index].sales_offtake[3]).trigger('change');
								$(this).find('.month_5_input').val(response.kpk[index].sales_offtake[4]).trigger('change');
							}
						});	
						$('#loader').addClass('hidden');
					}, 500);
				});
				
			});				
			$('#effective_date').val(moment(response.effective_date).format('dddd, D MMMM YYYY')).trigger('change')
			$('#date_text').val(response.remark_4).trigger('change');
			$('#select2status').val(response.status).trigger('change');
			
			
		},
		complete: function(){
		//	$('#loader').addClass('hidden');
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