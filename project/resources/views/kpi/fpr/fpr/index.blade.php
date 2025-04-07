@extends('adminlte::page')
@section('title', 'FPR '. $name_url)

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">FPR {{ $name_url }}</h5>               
            </div>      
			<div class="card-body">
			<div class="form-group row">    					
                    <div class="col-sm-4">
                        <select id="period" class="form-control form-control-sm select2" style="width: 100%;"></select>
						<span class="invalid-feedback" role="alert" id="periodError">
								<strong></strong>
						</span>
                    </div>
					<div class="col-sm-8">
                        <button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>            
                    </div>										
            </div>
				<div class="div_datatable" style="display:none;"> 
				<button onclick="return false;" class="btn btn-default pull-left advanced_fpr">Advanced Search</button>
					<br>
					<br>
					<table id="fpr_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>			
						<th>NIK Atasan</th>
						<th data-priority="4">Atasan</th>
						<th>Periode</th>
						<th data-priority="8">NIK Employee</th>
						<th data-priority="2">Employee</th>
						<th data-priority="3">Atasan Approve</th>
						<th data-priority="5">Employee Approve</th>
						<th data-priority="6">Submit</th>
						<th data-priority="7">Review Date</th>
						<th data-priority="1" style="text-align:center;width:100px;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_fpr"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="fprForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 id="soal_dinilai" class="modal-title">Performance Review</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 450px;overflow-y: auto;">
                    <div class="row" style="padding:0 40px 0 20px;">
						<div class="col-md-5">                       
							<div class="row">
							<input id="fpr_type" name="fpr_type" type="hidden">
							<input id="id_fpr_header" name="id_fpr_header" type="hidden">
                                <label class="col-sm-4">NIK Karyawan</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
                                    <div id="nik_employee"></div>
                                </div>
                            </div>									
							<div class="row">
                                <label class="col-sm-4">Nama Karyawan</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
                                    <div id="name_employee"></div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4">Atasan Langsung</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
                                    <div id="atasan"></div>
                                </div>
                            </div>
							<div id="dis_period" class="row">
                                <label class="col-sm-4">Periode Review</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
                                    <div id="period_review"></div>
                                </div>
                            </div>
                        </div>
						<div class="col-md-7">                       
							<div class="row">
                                <label class="col-sm-4">Jabatan / Departemen</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
									<div id="jabatan"></div>
                                </div>
                            </div>									
							<div class="row">
                                <label class="col-sm-4">Cabang / Regional</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
                                    <div id="cabang"></div>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4">Tanggal Review</label>
								<div class="col-titik"> : </div>
                                <div class="col-sm-7">
									<input name="review_date" id="tgl_review" type="hidden">	
                                    <div id="review_date"></div>
                                </div>
                            </div>
                        </div>
						
                        <div class="col-md-12">
							<div id="yearly">
								<div id="group_soal"></div>
								<div><b>7. Review & Kesepakatan</b><br>Dengan Mencentang Persetujuan dibawah ini, Menyatakan bahwa dari proses diskusi dan review kinerja, dengan ini kedua belah pihak menyepakati dan karyawan menyetujui hasil review kinerja di periode tersebut dan berkomitmen untuk menjalankan program pengembangan di tahun selanjutnya.</div>
								<br>
								<br>
							</div>
							<div id="periodic">
								<div id="group_periodic"></div>
							</div>
							
							<div id="sign"></div>						
							<br>
							<div class="row">
								<div class="col-sm-2"></div>
								<div class="icheck-secondary col-sm-3">
									<input type="checkbox" id="atasan_approve" class="atasan_approve" name="atasan_approve" autocomplete="off">
									<label for="atasan_approve" style="font-size:12px;color: #4d4d4d;">Atasan Menyetujui</label><br>
									<label id="lab_atasan" style="margin-left:-20px;">-</label>
								</div>
								<div class="col-sm-2"></div>
								<div class="icheck-secondary col-sm-3">
									<input type="checkbox" id="emp_approve" class="emp_approve" name="emp_approve" autocomplete="off">
									<label for="emp_approve" style="font-size:12px;color: #4d4d4d;">Karyawan Menyetujui</label><br>
									<label id="lab_emp"  style="margin-left:-20px;">-</label>
								</div>								
							</div>
						</div> 
							
                    </div>
                </div>
				<div class="modal-footer">
                    <button type="submit" class="save_submit btn btn-sm btn-info" id="submit_button" style="display:none;"><i class="fas fa-paper-plane"></i> Submit</button>&nbsp;
                    <button type="submit" class="save_draft btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save as Draft</button>
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('css')
<style type="text/css">
   
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
	td.text-middle{
		vertical-align:middle;
		text-align:center;
	}
	td.text-approve{
		vertical-align:middle;
		text-align:center;
		width:50px;
	}
	td.text-name{
		vertical-align:middle;
		text-align:left;
	}
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	.kpi.table td{
		padding:0.5rem;
		vertical-align: middle;
	}
	.quali.table td{
		padding:0.5rem;
		vertical-align: middle;
	}
	.col-titik{
		flex: 0 0 2%;
		max-width: 2%;
	}
	td.col-action{
		width: 100px;
		vertical-align:middle;
		text-align:center;
	}
</style>
@stop
@section('scripts')
<script type="text/javascript">
var name_url = '{{ $name_url }}';

$(document).ready(function(){
	
if(name_url == 'Self'){
	$('#atasan_approve').prop('disabled',true);
}

});	
$(document).on('click', '.edit', function () {
        let id_fpr_header = $(this).attr('id');
        $("#fprForm")[0].reset();
        $(".invalid-feedback").children("strong").text("");
        $("#fprForm input").removeClass("is-invalid");
        $("#fprForm textarea").removeClass("is-invalid");
		$('#submit_button').hide();
        $.ajax({
            url: "<?= url('kpi/fpr/fpr/get_fpr_edit') ?>",
            method: "GET",
            data: {id_fpr_header: id_fpr_header},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				if (response.status == 'true') {
					$('#soal_dinilai').html('Performance Review '+response.notes);
					$('#nik_employee').html(response.result.nik_employee);
					$('#name_employee').html(response.result.name);
					$('#atasan').html(response.result.atasan);
					$('#jabatan').html(response.result.jabatan+' / '+response.result.dept);
					$('#cabang').html(response.result.branch+' / '+response.result.region);
					$('#review_date').html(moment(response.result.review_date).format('DD-MM-YYYY'));
					$('#tgl_review').val(response.result.review_date).trigger('change');
					$('#lab_atasan').html(response.result.atasan);
					$('#lab_emp').html(response.result.name);
					$('#id_fpr_header').val(id_fpr_header).trigger('change');
					$('#fpr_type').val(response.notes).trigger('change');
						
						var approve_ = Object.values(response.result.approve);
						if(approve_.length > 0){
							if(response.result.approve.id_approval_appraisers != null){
								$('#atasan_approve').prop('checked', true);
							}
							if(response.result.approve.id_approval_participant != null){
								$('#emp_approve').prop('checked', true);
							}
						}

					var quanti_ = response.result.quantitative;
						
					if(response.notes == '(Yearly)'){	
						$('#dis_period').hide();
						$('#periodic').hide();
						$('#yearly').show();
						var quali_ = response.result.qualitative;
					//	console.log(quanti_);
						let soal = '';
						soal += '<hr  style="padding:10px 0 5px 0;">';
						$.each(response.result.group_soal, function (i, item) {
							$('#group_soal').html('');
								soal += '<input hidden name="soal[]" value="'+item.id_pa_question+'" >';
							//	soal += '<input hidden name="id_fpr_header" value="'+item.id_fpr_header+'" >';
								soal +=	'<div align="justify">'+item.fpr_soal;
									
									var result_ = Object.values(response.result.answers);
										
											var id_fpr_detail = '';
											var id_pa_question = '';
											var id_pa_answer = '';
											var desc_answer = '';
											if(result_.length > 0){
												id_fpr_detail = response.result.answers[i].id_fpr_detail;
												id_pa_question = response.result.answers[i].id_pa_question;
												id_pa_answer = response.result.answers[i].id_pa_answer;											
												desc_answer = response.result.answers[i].desc_answer;
												if(desc_answer == null){
													desc_answer = '';
												}
												else{
													desc_answer = response.result.answers[i].desc_answer;
												}
											}
											
										soal += '<div class="row" style="padding-left:20px;">';
										if(item.question_type == 'Essay'){
											var dis_text = '';
											if(name_url == 'Self'){		
												if(item.sequence_soal == '1'){
													dis_text = 'readonly';
												}
											}
											
											if(item.sequence_soal == '2'){
												soal += '<div class="col-sm-12" style="padding:5px 0px 5px 8px;overflow:auto;">';												
													soal += '<table id="detail_kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">';
														soal += '<thead>';
														soal += '	 <tr>';
														soal += '		<th rowspan="2" data-priority="1" style="vertical-align: middle;width:300px;">Item KPI</th>';
														soal += '		<th rowspan="2" data-priority="3" style="vertical-align: middle;width:250px;">Description</th>';
														soal += '		<th rowspan="2" data-priority="2" style="vertical-align: middle;">Type</th>';
														soal += '		<th colspan="12" style="text-align: center;border-bottom: none;">Score KPI Monthly(%)</th>';
														soal += '	  </tr>';
															soal += '<tr>';				   																
																soal += '<th>Jan</th>';
																soal += '<th>Feb</th>';
																soal += '<th>Mar</th>';
																soal += '<th>Apr</th>';
																soal += '<th>Mei</th>';
																soal += '<th>Jun</th>';
																soal += '<th>Jul</th>';
																soal += '<th>Ags</th>';
																soal += '<th>Sep</th>';
																soal += '<th>Okt</th>';
																soal += '<th>Nov</th>';
																soal += '<th>Des</th>';
															soal += '</tr>';
														soal += '</thead>';
													soal += '</table>';
												soal +=	'</div>';
											}
											else if(item.sequence_soal == '5' && name_url == 'Subordinate'){
												soal += '<div class="col-sm-12" style="padding:5px 0px 10px 8px;overflow:auto;">';
												if(quali_.length > 0){
													soal += '<div>Period : '+quali_[0].period+'</div>';
													soal += '<table style="width:100%;" class="quali table table-striped table-bordered table-hover datatable">';
														soal += '<thead>';
															soal += '<tr align="center">';				   
																soal += '<th>Penilai</th>';
																soal += '<th>Type</th>';
																soal += '<th>Dinilai</th>';
																soal += '<th>Score</th>';
																soal += '<th>Total Score</th>';
																soal += '<th>Final Score (%)</th>';															
															soal += '</tr>';
														soal += '</thead>';
														soal += '<tbody>';
															$.each(quali_, function (i, item_quali) {
																var rows = '';
																if(i == 0){
																	rows = 'rowspan='+quali_.length;
																}
																else{
																	rows = 'style="display:none;"';
																}
															soal += '<tr align="left">';	
																soal += '<td>'+item_quali.penilai+'</td>';
																soal += '<td>'+item_quali.appraisers_hierarchy+'</td>';
																soal += '<td>'+item_quali.dinilai+'</td>';															
																soal += '<td class="text-middle">'+item_quali.subtotal_score+'</td>';
																soal += '<td class="text-middle" '+rows+'>'+item_quali.total_score+'</td>';
																soal += '<td class="text-middle" '+rows+'>'+item_quali.final_score+'</td>';
															soal += '</tr>';													
															});
														soal +=	'</tbody>';
													soal +=	'</table>';
												}
												else{
													soal += '<i>No Data</i>';
												}
												soal +=	'</div>';
											}
											
											else if(item.sequence_soal == '5' && name_url == 'Self'){
												soal += '<div class="col-sm-12" style="padding:5px 0px 10px 8px;overflow:auto;">';
												if(quali_.length > 0){
													soal += '<div>Period : '+quali_[0].period+'</div>';
													soal += '<table style="width:100px;" class="quali table table-striped table-bordered table-hover datatable">';
														soal += '<thead>';
															soal += '<tr  align="center">';				   															
																soal += '<th>Final Score (%)</th>';															
															soal += '</tr>';
														soal += '</thead>';
														soal += '<tbody>';
															soal += '<tr>';																
																soal += '<td class="text-middle">'+quali_[0].final_score+'</td>';
															soal += '</tr>';													
														soal +=	'</tbody>';
													soal +=	'</table>';
												}
												else{
													soal += '<i>No Data</i>';
												}
												soal +=	'</div>';
											}
											
											else{
											soal += '<div class="col-sm-12" style="padding:0px 0px 10px 8px;">';
												soal +=	'<textarea class="form-control form-control-sm" name="ans_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter" '+dis_text+'>'+desc_answer+'</textarea>';
											soal +=	'</div>';
											}
																					
										}
										else if(item.question_type == 'Multiple_Answer'){	
											$.each(item.answer, function (j, answ) {	
												var checked = '';
												var display = 'display:none;';
												$.each(id_pa_answer, function (j, idans) {
													if(idans == answ.id_pa_answer){
														checked = 'checked';
														if(answ.sequence == 'Lainnya'){
															display = 'display:inline;';
														}
													}
												});											
												if(j%2 == 0){
													soal +=		'<div class="col-md-3">';
												}
													soal +=		'<div class="row" style="padding-bottom:10px;">';
														soal +=		'<div class="col-sm-12">';
															soal +=		'<span class="icheck-secondary" style="color:#212529;">';
															soal +=     	'<input class="'+answ.sequence+'" type="checkbox" id="answers_'+answ.id_pa_answer+'" name="answers_'+item.id_pa_question+'[]" autocomplete="off" value="'+answ.id_pa_answer+'" an="'+item.id_pa_question+'" '+checked+'>';
															soal +=			'<label for="answers_'+answ.id_pa_answer+'" style="font-size:12px;float:left;">'+answ.desc_answer+'</label>';
															if(j == (item.answer.length-1)){
																soal += '<div style="padding-top:30px;">';
																soal += 	'<input type="text" class="lain form-control form-control-sm" id="answers_'+item.id_pa_question+'" name="aws_'+answ.id_pa_answer+'[]" value="'+desc_answer+'" autocomplete="off" style="'+display+'width:100%;">';
																soal += 	'</div>';
															}
															soal += 	'</span>';
														soal += 	'</div>';
													soal += 	'</div>';
												if(j%2 != 0){
													soal += 	'</div>';
												}											
											});											
										}	
										
										else if(item.question_type == 'Single_Answer'){
											$.each(item.answer, function (j, ans) {
												var checked = '';
												if(id_pa_answer == ans.id_pa_answer){
													checked = 'checked';
												}
													soal += '<div class="icheck-secondary col-sm-2" style="color:#212529;">';
														soal += '<input type="radio" id="answers_'+ans.id_pa_answer+'" name="answers_'+item.id_pa_question+'[]" autocomplete="off" value="'+ans.id_pa_answer+'" '+checked+'>';
														soal +=	'<label for="answers_'+ans.id_pa_answer+'" style="margin-top:-5px;font-size:12px;">'+ans.desc_answer+'</label>';
													soal += '</div>';
											});
										}
										soal +=	'</div>';
								soal += '</div>';	
							$('#group_soal').html(soal);
						});			
					}
					else if(response.notes == '(Periodically)'){
						$('#period_review').html(response.result.period);
						
						$('#dis_period').show();
						$('#yearly').hide();
						$('#periodic').show();
						
						var quanti_ = response.result.quantitative;
						
						let soal_periodic = '';
						soal_periodic += '<hr  style="padding:10px 0 0 0;">';
						$.each(response.result.group_soal, function (i, item) {
							$('#group_periodic').html('');
								soal_periodic += '<input hidden name="soal_periodic[]" value="'+item.id_pa_question+'" >';
							//	soal_periodic += '<input hidden name="id_fpr_header" value="'+item.id_fpr_header+'" >';
																	
									var result_ = Object.values(response.result.answers);
										
											var id_fpr_detail = '';
											var id_pa_question = '';
											var desc_answer = '';
											if(result_.length > 0){
												id_fpr_detail = response.result.answers[i].id_fpr_detail;
												id_pa_question = response.result.answers[i].id_pa_question;
												desc_answer = response.result.answers[i].desc_answer;
												if(desc_answer == null){
													desc_answer = '';
												}
												else{
													desc_answer = response.result.answers[i].desc_answer;
												}
											}
										if(item.question_type == 'Essay'){											
											if(item.sequence_soal == '1'){
												soal_periodic += '<b>1. Pencapaian KPI Periodik</b><br>';
												soal_periodic += '<div style="padding-left:15px;">Detail pencapaian kerja (KPI), point-point penting untuk pencapaian selama periodik (bulanan / 3 bulanan / 6 bulanan)</div>';
												soal_periodic += '<div class="col-sm-12" style="margin-bottom:20px;padding:5px 0px 5px 15px;overflow:auto;">';
													soal_periodic += '<table id="detail_kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">';
														soal_periodic += '<thead>';
														soal_periodic += '	 <tr>';
														soal_periodic += '		<th rowspan="2" data-priority="1" style="vertical-align: middle;width:300px;">Item KPI</th>';
														soal_periodic += '		<th rowspan="2" data-priority="3" style="vertical-align: middle;width:250px;">Description</th>';
														soal_periodic += '		<th rowspan="2" data-priority="2" style="vertical-align: middle;">Type</th>';
														soal_periodic += '		<th colspan="12" style="text-align: center;border-bottom: none;">Detail Score KPI Monthly(%)</th>';
														soal_periodic += '	  </tr>';
															soal_periodic += '<tr align="center">';				   																
																soal_periodic += '<th>Jan</th>';
																soal_periodic += '<th>Feb</th>';
																soal_periodic += '<th>Mar</th>';
																soal_periodic += '<th>Apr</th>';
																soal_periodic += '<th>Mei</th>';
																soal_periodic += '<th>Jun</th>';
																soal_periodic += '<th>Jul</th>';
																soal_periodic += '<th>Ags</th>';
																soal_periodic += '<th>Sep</th>';
																soal_periodic += '<th>Okt</th>';
																soal_periodic += '<th>Nov</th>';
																soal_periodic += '<th>Des</th>';
															soal_periodic += '</tr>';
														soal_periodic += '</thead>';
													soal_periodic += '</table>';											
												soal_periodic +=	'</div>';
												
												soal_periodic += '<div style="padding-left:15px;">Total pencapaian kerja (KPI)/Bulan</div>';
												soal_periodic += '<div class="col-sm-12" style="margin-bottom:20px;padding:5px 0px 5px 15px;overflow:auto;">';
													soal_periodic += '<table id="total_kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">';
														soal_periodic += '<thead>';
														soal_periodic += '	 <tr>';
														soal_periodic += '		<th colspan="12" style="text-align: center;border-bottom: none;">Total Score KPI Monthly(%)</th>';
														soal_periodic += '	  </tr>';
															soal_periodic += '<tr align="center">';				   																
																soal_periodic += '<th>Jan</th>';
																soal_periodic += '<th>Feb</th>';
																soal_periodic += '<th>Mar</th>';
																soal_periodic += '<th>Apr</th>';
																soal_periodic += '<th>Mei</th>';
																soal_periodic += '<th>Jun</th>';
																soal_periodic += '<th>Jul</th>';
																soal_periodic += '<th>Ags</th>';
																soal_periodic += '<th>Sep</th>';
																soal_periodic += '<th>Okt</th>';
																soal_periodic += '<th>Nov</th>';
																soal_periodic += '<th>Des</th>';
															soal_periodic += '</tr>';
														soal_periodic += '</thead>';
													soal_periodic += '</table>';											
												soal_periodic +=	'</div>';
											}
											
											if(item.sequence_soal == '2'){
												soal_periodic += '<b>2. Detail Review Kinerja Periodik</b><br>';
												soal_periodic += '<div class="col-sm-12" style="padding:0px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" align="center">TARGET YANG TELAH TERCAPAI (ACHIEVEMENTS)';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
												soal_periodic +=	'</div>';
											}
											if(item.sequence_soal == '3'){
												soal_periodic += '<div class="col-sm-12" style="padding:0px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" align="center">HAL YANG MENJADI KEKUATAN / SUDAH BAIK & PERLU DIPERTAHANKAN (AREA OF EXCELLENCES)';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
												soal_periodic +=	'</div>';
											}
											if(item.sequence_soal == '4'){
												soal_periodic += '<div class="col-sm-12" style="padding:0px;border:1px solid #cfd0d0;margin:0 0 10px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" align="center">HAL YANG PERLU DITINGKATKAN DI PERIODE BERIKUTNYA (AREA OF IMPROVEMENTS)';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
												soal_periodic +=	'</div>';
											}
																						
												if(item.sequence_soal == '5'){	
													soal_periodic += '<div class="col-sm-12" style="padding:0px;border:1px solid #cfd0d0;margin:0 0 15px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" align="center">TARGET DI PERIODE BERIKUTNYA (BULANAN / 3 BULANAN / 6 BULANAN*)';
													soal_periodic +=  '<div class="row" style="margin:0px;">';
													soal_periodic += '<div class="col-sm-6" style="padding:0px;border:1px solid #cfd0d0;">TARGET YANG AKAN DICAPAI';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
													soal_periodic += '</div>';													
												}
												if(item.sequence_soal == '6'){	
													soal_periodic += '<div class="col-sm-3" style="padding:0px;border:1px solid #cfd0d0;">TARGET WAKTU';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
													soal_periodic += '</div>';													
												}
												if(item.sequence_soal == '7'){	
													soal_periodic += '<div class="col-sm-3" style="padding:0px;border:1px solid #cfd0d0;">BANTUAN YG DIBUTUHKAN & PIC';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
													soal_periodic += '</div>';		
													soal_periodic += '</div>';
													soal_periodic += '</div>';
												}					
												
												if(item.sequence_soal == '8'){
													soal_periodic += '<b>3. Komentar, Saran, dan Persetujuan</b><br>';
													soal_periodic += '<div class="col-sm-12"  style="padding:0px;border:1px solid #cfd0d0;margin:0 0 30px 15px;border-radius: 5px;font-size:12px;background:#f2f2f2;" align="center">';
													soal_periodic +=  '<div class="row" style="margin:0px;">';
													soal_periodic += '<div class="col-sm-6" style="padding:0px;border:1px solid #cfd0d0;">KOMENTAR / SARAN ATASAN';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
													soal_periodic += '</div>';													
												}
												
												if(item.sequence_soal == '9'){	
													soal_periodic += '<div class="col-sm-6" style="padding:0px;border:1px solid #cfd0d0;">KOMENTAR / SARAN KARYAWAN';
													soal_periodic +=	'<textarea class="form-control form-control-sm" name="per_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
													soal_periodic += '</div>';		
													soal_periodic += '</div>';
													soal_periodic += '</div>';
												}										
																					
										}
										
							$('#group_periodic').html(soal_periodic);
						});						
					}
					
					get_viewkpi(quanti_);
					get_viewkpitotal(quanti_);

					$('#sign').html('Pusat, '+moment(response.result.review_date).format('DD/MM/YYYY'));

					$('#modal_form_fpr').modal('show');
				}
				else {
					swal({
						icon: 'error',
						dangerMode: true,
						content: {
							element: "div",
							attributes: {
								innerText: response.message,
								className: "swal-red",
							},
						},
					});
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
    });

$(document).on('click', '.Lainnya', function () {
		var a = $(this).attr('an');
		if(this.checked){		
			$('#answers_'+a).show();
		}
		else{
			$('#answers_'+a).val('');
			$('#answers_'+a).hide();
		}
	});

$(document).on('click', '.atasan_approve', function () {
		if(this.checked){		
			$('#submit_button').show();
		}
		else{
			$('#submit_button').hide();
		}
	});

$('#fprForm').submit(function (e) {
		let thisButtonId = e.originalEvent.submitter.id;
		e.preventDefault();
		let formData = $(this).serializeArray();
		formData.push({'name':'id_button','value':thisButtonId});

		$(".invalid-feedback").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#fprForm input").removeClass("is-invalid");
		$("#fprForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text("");
		$(".error-tab").html("");
		var texts = '';
		if(thisButtonId == 'submit_button'){
			texts = 'Form Performance Review akan disubmit dan tidak bisa diperbaiki';
		}
		else if(thisButtonId == 'save_button'){
			texts = 'Form Performance Review akan disimpan sebagai draft';
		}
		swal({
			title: 'Sure ?',
			text: texts,
			icon: 'warning',
			buttons: true,
			confirmButtonColor: '#218838',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, Submited!'
		}).then(function(value) {
			if (value) {
				$.ajax({
					type: 'POST',
					headers: {
						Accept: "application/json",
					},
					url: "{{ route('fpr.update') }}",
					data: formData,
					"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
					success: function (response) {
						if (response.status == 'true') {
							$('#modal_form_fpr').modal('hide');
							swal({
								icon: 'success',
								title: 'Success',
								text: response.message
							});
							$('#fpr_table').DataTable().ajax.reload();
						} else {
							swal({
								icon: 'error',
								dangerMode: true,
								className: 'swal-wide',
								content: {
									element: "div",
									attributes: {
										innerText: response.message,
										className: "swal-red",
									},
								},
							});
						}
					},
					complete: function(){
						$('#loader').addClass('hidden');
					},
					error: function (response) {
						if (response.status === 422) {
							let errors = response.responseJSON.errors;
							Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
								$("#" + key_temp).addClass("is-invalid");
								$("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
							});
						}
						else {
							swal({
								icon: 'error',
								title: 'Oops...',
								dangerMode: true,
								text: 'Something went wrong! [Unknown Error]'
							});
						}
					}
				}); 
			}
		});				          
    });	

	
$(document).on('click', '#search_period', function () {
	get_datatable();
});

$(document).on("click", ".advanced_fpr", function () {
    $('.cf').select2({width:'100%'});
    if($(".fpr_table").css('display') == 'none'){
        $(".fpr_table").show("slow");
    }
    else {
        $(".fpr_table").hide("slow");
    }   
});

	const get_datatable = async () => {
		$(".div_datatable").show();
		let myData = {
			period: $("#period").val() == '' ? null : $("#period").val(),
			path: "{{ $path_url }}",
		};

		$('#fpr_table').DataTable({
			processing: true,
			responsive: true,
			destroy: true,
		//	'rowsGroup': [3],
			ajax: {
			url: "{{ route('fpr.index') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#fpr_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{
				defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
				data: 'id_fpr_header',
				defaultContent: '',
				orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-middle'},				
				{ data: 'nik_atasan', name: 'nik_atasan', className: 'text-name'},
				{ data: 'atasan', name: 'atasan', className: 'text-name'},
				{ data: 'period', name: 'period', className: 'text-name'},
				{ data: 'nik_employee', name: 'nik_employee', className: 'text-name'},
				{ data: 'name', name: 'name', className: 'text-name'},           
				{ data: 'id_approval_appraisers', name: 'id_approval_appraisers', className: 'text-approve', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{
							return '<span class="badge badge-success">'+data+'</span>';							
						}
					}
				},
				{ data: 'id_approval_participant', name: 'id_approval_participant', className: 'text-approve', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{							
							return '<span class="badge badge-success">'+data+'</span>';
						}
					}
				},
				{ data: 'submitted', name: 'submitted', className: 'text-approve', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{
							return '<span class="badge badge-success">'+data+'</span>';
						}
					}
				},
				{ data: 'review_date', name: 'review_date', className: 'text-middle'},
				{ data: 'action', name: 'action', orderable: false, className: 'col-action', render: function ( data, type, row ) {	
						return data;
					} 
				},
			],
		});
		
	}	
	
refresh_data();

function refresh_data() {
	get_period();	
}

function get_period() {
	$.getJSON('<?= url('kpi/fpr/fpr_management/get_period') ?>', function (data) {
			$('#period').select2({
                placeholder: "Select Period",
                data: data
            });
		})/*.then(function (data){
			if(localStorage.getItem("id_period_quantitative") != null ){
				$("#period").val(localStorage.getItem("id_period_quantitative")).trigger('change');
				if($("#period").val() != ""){
					$('#search_period').trigger('click');
				}
			}
		})*/
		.fail(function (data) { // Call failed
            get_period();
        });	
}

$(document).on("click", ".pdf", function () {
	let res = {
        id_fpr_header: $(this).attr('id'),
        name_url: name_url,
    };
    let param = objectToQueryString(res);
	let url = "";
	if(name_url == 'Subordinate'){
	    url = "{{ url('kpi/fpr/fpr_subordinate/download') }}";
	}
	else if(name_url == 'Self'){
	    url = "{{ url('kpi/fpr/fpr_self/download') }}";
	}
    window.open(url+'?'+param, '_blank');
});

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_viewkpi(global_id_kpi_group) {
	 	$('#detail_kpi_table').DataTable({
			destroy:true,
			columnDefs:false,
			paging:false,
			searching:false,
			lengthChange: false,
			info: false,
			dom: '<"toolbar">frtip',
			ajax: {
				url: "<?= url('kpi/kpi/pa_quantitative_assesment/get_kpi_view') ?>",
				data: {id_kpi_group: global_id_kpi_group},
				
				},
			columns: [
				{ data: 'item_kpi', name: 'item_kpi', className: 'text-name'},
				{ data: 'kpi_desc', name: 'kpi_desc', className: 'text-name'},
				{ data: 'type_kpi', name: 'type_kpi', className: 'text-middle', render: function ( data, type, row ) {						
						data = data.toLowerCase().replace(/\b[a-z]/g, function(letter) {
							return letter.toUpperCase();
						});						
						return data;
					}
				},           
				{ data: 'jan', name: 'jan', className: 'text-score' },
				{ data: 'feb', name: 'feb', className: 'text-score' },
				{ data: 'mar', name: 'mar', className: 'text-score' },
				{ data: 'apr', name: 'apr', className: 'text-score' },
				{ data: 'mei', name: 'mei', className: 'text-score' },
				{ data: 'jun', name: 'jun', className: 'text-score'},
				{ data: 'jul', name: 'jul', className: 'text-score' },
				{ data: 'ags', name: 'ags', className: 'text-score' },
				{ data: 'sep', name: 'sep', className: 'text-score' },
				{ data: 'okt', name: 'okt', className: 'text-score' },
				{ data: 'nov', name: 'nov', className: 'text-score' },
				{ data: 'des', name: 'des', className: 'text-score' },
			],
		});	
		$('#detail_kpi_table').css('width','1200px');
 }

function get_viewkpitotal(global_id_kpi_group) {
	 	$('#total_kpi_table').DataTable({
			destroy:true,
			columnDefs:false,
			paging:false,
			searching:false,
			lengthChange: false,
			info: false,
			dom: '<"toolbar">frtip',
			ajax: {
				url: "<?= url('kpi/kpi/pa_quantitative_assesment/get_kpi_total') ?>",
				data: {id_kpi_group: global_id_kpi_group},
				
				},
			columns: [          
				{ data: 'jan', name: 'jan', className: 'text-score' },
				{ data: 'feb', name: 'feb', className: 'text-score' },
				{ data: 'mar', name: 'mar', className: 'text-score' },
				{ data: 'apr', name: 'apr', className: 'text-score' },
				{ data: 'mei', name: 'mei', className: 'text-score' },
				{ data: 'jun', name: 'jun', className: 'text-score'},
				{ data: 'jul', name: 'jul', className: 'text-score' },
				{ data: 'ags', name: 'ags', className: 'text-score' },
				{ data: 'sep', name: 'sep', className: 'text-score' },
				{ data: 'okt', name: 'okt', className: 'text-score' },
				{ data: 'nov', name: 'nov', className: 'text-score' },
				{ data: 'des', name: 'des', className: 'text-score' },
			],
		});	
		$('#total_kpi_table').css('width','1200px');
 }

</script>
@endsection