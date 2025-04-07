@extends('adminlte::page')
@section('title', 'Candidate Interview')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Candidate Interview</h5>               
            </div>      
			<div class="card-body">
				<button onclick="return false;" class="btn btn-default pull-left advanced_can">Advanced Search</button><br><br>
				<div id="table_div_can"></div>
			</div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="questionForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 class="detail_can modal-title">Interview Question</h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
				<div class="row">
					<div class="col-12">
						<div class="row">
							<div align="center" class="col-md-2"> 
								<div class="widget-user-image" style="margin-bottom:10px;">
									<img id="can_attach" class="img-circle elevation-2" style="height:150px;width:150px;border-radius:999px;" alt="User Avatar">
								</div>
							</div>
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-4 col-form-label">Candidate Name</label>
									<div class="col-sm-8">
										<input id="id_candidate" name="id_candidate" type="hidden">
										<input id="id_applied" name="id_applied" type="hidden">
										<input id="id_group" name="id_group" type="hidden">
										<input id="can_name" class="form-control form-control-sm" readonly>	
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Email</label>
									<div class="col-sm-8">
										<input id="can_mail" class="form-control form-control-sm" readonly>	
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Mobile Phone</label>
									<div class="col-sm-8">
										<input id="can_mobile" class="form-control form-control-sm" readonly>	
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Interest Position</label>
									<div class="col-sm-8">
										<input id="can_dept" class="form-control form-control-sm" readonly>	
									</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-4 col-form-label">Curriculum Vitae (CV)</label>
									<label class="col-sm-8 col-form-label">
										<a id="can_cv" href="#" target="_blank" style="font-size:14px;">Download (CV)</a>
									</label>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Application Letter</label>
									<label class="col-sm-8 col-form-label">
										<a id="can_app_letter" href="#" target="_blank" style="font-size:14px;">Download (Application Letter)</a>
									</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				<div class="card card-success card-outline" style="margin-top:10px;">
					<div class="card-body">
						<div style="font-size:16px;font-weight:bold;margin-bottom:10px;">Applied Job</div>
						<div class="row">
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-4 col-form-label">Job Position</label>
									<div class="col-sm-8">
										<input id="pos_req" class="form-control form-control-sm" readonly>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Ref Number (FPK)</label>
									<div class="col-sm-8">
										<input id="ref_number" class="form-control form-control-sm" readonly>	
									</div>
								</div>		
								<div class="row">
									<label class="col-sm-4 col-form-label">Branch</label>
									<div class="col-sm-8">
										<input id="can_branch" class="form-control form-control-sm" readonly>	
									</div>
								</div>
							</div>
							<div class="col-md-1"></div>
							<div class="col-md-5">
								<div class="row">
									<label class="col-sm-3 col-form-label">Date Applied</label>
									<div class="col-sm-8">
										<input id="can_date_applied" class="form-control form-control-sm" readonly>	
									</div>
								</div>
								<div class="row">
									<label class="col-sm-3 col-form-label">Rec. Stage</label>
									<div class="col-sm-8">
										<input id="status_stage" name="status_stage" type="hidden">
										<input id="can_stage" class="form-control form-control-sm" readonly>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-3 col-form-label">Status</label>
									<div class="col-sm-8">
										<input id="can_status" class="form-control form-control-sm" readonly>	
									</div>
								</div>
							</div>
							<div class="col-md-1"></div>
						</div>
						<!-- div class="row">
							<div class="col-md-12">
								<div style="font-weight:bold;">Notes</div>
								<textarea id="add_note" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
							</div>
						</div -->
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<ul class="nav nav-tabs" id="tab_question_detail" role="tablist">
									<li class="nav-item">
										<a class="nav-link active" id="link_tab_menu-int" data-toggle="pill" href="#menu-int" role="tab" aria-controls="link_tab_menu-int" aria-selected="true">Interview Question</a>
									</li>
								</ul>
								<div class="tab-pane fade show active" style="font-size:14px" id="menu-int" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
									<br/>
										<div id="group_interview">
											<div class="modal-header">
												<div class="col-12">
													<div class="row">
														<div class="col-md-6">
															<div class="row">
																<label class="col-sm-3 col-form-label">Interview Date</label>
																<div class="col-sm-6">
																	<input id="id_recruitment_answer_header" name="id_recruitment_answer_header" type="hidden">
																	<input type="text" name="interview_date" id="interview_date" class="form-control form-control-sm">
																	<span class="invalid-feedback" role="alert" id="interview_dateError">
																		<strong></strong>
																	</span>
																</div>
															</div>
														</div>
														<!--div class="col-md-6">
															<div class="row">
																<label class="col-sm-3 col-form-label">Conclusion</label>
																<div class="col-sm-8">
																	<select name="id_conclusion" id="id_conclusion" class="form-control form-control-sm select2" style="width: 100%;">
																	</select>
																	<span class="invalid-feedback" role="alert" id="id_conclusionError">
																		<strong></strong>
																	</span>
																</div>
															</div>
														</div -->
													</div>
												</div>
											</div>
											<div class="card-body">
												<div class="row">									
													<div class="col-md-12" id="group_soal" style="padding-top:10px;"></div>
													<div class="col-md-12">
														<div style="font-size:16px;font-weight:bold;margin: 10px 0 5px 0;">Notes</div>
														<textarea id="int_notes" name="int_notes" class="form-control form-control-sm" style="height:80px;" placeholder="Silahkan isi disini..."></textarea>
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
		  <div class="modal-footer">
			<button type="submit" class="btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>  
    </div>
  </div>
</div>
@endsection
@section('css')
<style type="text/css">
    .modal-xl {
        max-width: 90% !important;
    }
	.modal-item {
        max-width: 70% !important;
    }
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
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	th.th-text-score{
		width:10px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_interview = "";
let global_code_stage = "";

function on_close_modal() {
	location.reload();
}

function loadprofile(id_candidate,id_applied,id_group){
	 $.ajax({
			url: "<?= url('recruitment/personality_assessment/interview_question/get_edit_question') ?>",
            method: "GET",
            data: {id_candidate: id_candidate,id_applied: id_applied,id_group: id_group},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {			
				document.getElementById("can_attach").src = 'https://career.borwita.co.id/storage/candidate_photo/'+response.photo_candidate;
				$('#id_candidate').val(id_candidate).trigger('change');
				$('#id_applied').val(id_applied).trigger('change');
				$('#id_group').val(id_group).trigger('change');
				$('#can_name').val(response.name).trigger('change');
				$('#can_mail').val(response.email).trigger('change');
				$('#can_mobile').val(response.mobile_phone).trigger('change');
				$('#can_dept').val(response.dept).trigger('change');
				$('#can_cv').attr('href','https://career.borwita.co.id/storage/curriculum_vitae/'+ response.cv_upload);
				$('#can_app_letter').attr('href','https://career.borwita.co.id/storage/application_letter/'+ response.app_letter_upload);
				if(response.applied.length > 0 ){
					$('#ref_number').val(response.applied[0].reference_number).trigger('change');
					$('#can_date_applied').val(moment(response.applied[0].applied_date).format('DD MMM YYYY')).trigger('change');
					$('#status_stage').val(response.applied[0].id_candidate_status).trigger('change');
					$('#can_stage').val(response.applied[0].code_status).trigger('change');
					$('#can_status').val(response.applied[0].status).trigger('change');
					$('#can_branch').val(response.applied[0].branch).trigger('change');
					$('#pos_req').val(response.applied[0].pos_req).trigger('change');
					$('#add_note').val(response.additional_note).trigger('change');
				//	console.log(response.applied[0].code_stage);
					global_interview = response.applied[0].id_candidate_status;
					get_question(id_candidate,id_applied,global_interview).then(function(res) {
					//	$('#loader').removeClass('hidden');
						setTimeout(function () {
							if(response.hr_answer.length > 0 ){
								$('#id_recruitment_answer_header').val(response.hr_answer[0].id_recruitment_answer_header).trigger('change');
								$('#interview_date').val(response.hr_answer[0].interview_date).trigger('change');
								$('#total_score').val(response.hr_answer[0].total_score).trigger('change');					
								$('#int_notes').val(response.hr_answer[0].int_notes).trigger('change');
							//	$('#id_conclusion').val(response.hr_answer[0].id_conclusion).trigger('change');
							}
							let soal = '';
							
							var group_essay = null; 
							var group_single = null; 
							var sheet = null;
							var competency = null;
							var group_ref = null; 
							soal += '<div class="col-md-12" style="padding:15px;border:1px solid #ddd;">';
						//	console.log(res);
							$.each(res, function (i, item) {
								if(item.essay_answer == null || item.essay_answer == ""){
									var desc_essay = '';
								}
								else{
									var desc_essay = item.essay_answer;
								}
								$('#group_soal').html('');
								soal += '<input hidden name="id_recruitment_question_group" value="'+item.id_recruitment_question_group+'" >';
								soal += '<input hidden name="soal[]" value="'+item.id_recruitment_question+'" >';
								soal += '<input hidden name="question_type[]" value="'+item.question_type+'" >';
								soal += '<input hidden name="soal_item[]" value="'+item.soal+'" >';
								soal += '<div class="col-md-12" style="margin-bottom:10px;">';
																		
									if(item.question_type == 'Essay'){	
										if(response.applied[0].code_stage == 'BEI'){
											if(i == 1){
												soal += '<label style="font-size:18px;">BEI GUIDELINE</label>';
												soal += '<hr style="margin-top:0;">';
											}
										}
										if(competency != item.competency_group){
											if(response.applied[0].code_stage == 'PRE'){
												soal += '<br><label style="font-size:18px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											}
											else{
												soal += '<br><label style="font-size:16px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											}
											competency = item.competency_group;
											soal += '<hr style="margin-top:0;margin-bottom:10px;">';
										}
										if(group_essay != item.category_group){											
										/*	if(i != 1){
												soal += '<hr>';
											}
										*/
											
											soal += '<label style="font-size:16px;margin-bottom:10px;">'+item.category_group+'</label>';
											group_essay = item.category_group;
										}
										
										if(response.applied[0].code_stage == 'BEI'){
											soal += '<div class="row">';
												soal += '<div class="col-sm-12">'+item.soal+'</div>';
											soal += '</div>';
										}
										else{
											soal += '<div class="row">';
												soal += '<div class="col-sm-5">'+item.soal+'</div>';
												soal += '<div class="col-sm-7">';
													soal += '<textarea id="ans_'+item.id_recruitment_question+'" name="ans_'+item.id_recruitment_question+'" class="form-control form-control-sm" style="height:60px;" placeholder="Silahkan isi disini...">'+desc_essay+'</textarea>';
												soal += '</div>';
											soal += '</div>';
										}
																			
										if(response.applied[0].code_stage == 'REF'){
											if(group_ref == 'DATA REFERENSI'){											
												if(item.category_group == 'DATA REFERENSI'){
													soal += '<hr>';
													soal += '<label style="font-size:16px;margin-bottom:10px;">INTRODUKSI</label>';
													soal += '<div class="row">';
														soal += '<div class="col-sm-12">';
															soal += '<p align="justify">Nama saya (nama Anda) dan saat ini saya menghubungi berkaitan dengan <i>reference check</i> atas nama (nama pelamar) yang dipertimbangkan untuk mengisi jabatan sebagai (jabatan yang dilamar) di Perusahaan kami, PT Borwita Citra Prima.<br> Nama Bapak/Ibu diberikan kepada kami oleh saudara (nama pelamar) dan saya ingin menanyakan apakah Bapak/Ibu  bersedia. <i>Reference check</i> ini membutuhkan waktu sekitar 10 menit. Apakah Bapak/Ibu memiliki cukup waktu? Jika  tidak, kapan saya dapat menghubungi kembali?</p>';
														soal += '</div>';
													soal += '</div>';
													soal += '<hr>';
												}
												
											}
											group_ref = 'DATA REFERENSI';
										}
									
									}									
									else if(item.question_type == 'Single_Answer'){
										
										if(response.applied[0].code_stage == 'BEI'){
												if(sheet != 'BEI SHEET'){
												//	soal += '<hr style="margin-top:0;">';
													soal += '<label style="font-size:18px;margin-top:20px;">BEI SHEET</label>';
													sheet = 'BEI SHEET';
													soal += '<hr style="margin-top:0;margin-bottom:0;">';
												}
											}
										if(competency != item.competency_group){
											if(response.applied[0].code_stage == 'PRE'){
												soal += '<br><label style="font-size:18px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											}
											else{
												soal += '<br><label style="font-size:16px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											}
											competency = item.competency_group;
											soal += '<hr style="margin-top:0;margin-bottom:10px;">';
										}
										if(group_single != item.category_group){
										/*	if(i != 1){
												soal += '<hr>';
											}
										*/	
											
											soal += '<label style="font-size:16px;margin-bottom:10px;">'+item.category_group+'</label>';
											group_single = item.category_group;
										}
										
										soal += '<div class="row">';
											soal += '<div class="col-sm-5">'+item.soal+'</div>';
											soal += '<div class="col-sm-7" style="border-left:1px solid #ddd;padding-left:20px;">';
												soal += '<div class="row">';
												$.each(item.opt_answer, function (j, ans) {
														var checked = '';
														if(item.id_answer == ans.id_recruitment_answer){
															checked = 'checked';
														}
													/*	
														soal +=	'<label for="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" class="row btn-answer callout callout-success" style="font-weight: normal;">';
															soal += '<div class="icheck-success">';
																soal += '<input type="radio" id="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" name="answers_'+item.id_recruitment_question+'[]" autocomplete="off" value="'+ans.id_recruitment_answer+'" '+checked+'>';
																soal +=	'<label for="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" style="margin-top:-5px;">'+ans.desc_answer+'</label>';
															soal += '</div>';
														soal += '</label>';	
													*/
														
														soal += '<div class="col-sm-6">';
															soal += '<div class="icheck-success" style="color:#212529;">';
																soal += '<input type="radio" id="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" name="answers_'+item.id_recruitment_question+'[]" autocomplete="off" value="'+ans.id_recruitment_answer+'" '+checked+'>';
																soal +=	'<label for="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" style="margin-top:-5px;font-weight:550;">'+ans.desc_answer+'</label>';
															soal += '</div>';
														soal += '</div>';
													
													});
												soal += '</div>';
											soal += '</div>';
										soal += '</div>';	
									}																
									
								soal += '</div>';
								
								
							});
							if(response.applied[0].code_stage == 'PRE'){
								soal += '<hr>';
								soal += '<div style="font-size:10px;">Keterangan :<br>';
								soal += '*Hanya untuk proyeksi jabatan Administration & Technical (A&T) Field<br>';
								soal += '**Hanya untuk proyeksi jabatan yang secara khusus mempersyaratkan kemampuan bahasa asing</div>';
							}
							else if(response.applied[0].code_stage == 'BEI'){
								soal += '<hr>';
								soal += '<div style="font-size:11px;">Keterangan :<br>';
								soal += '*Persyaratan sesuai dengan Matriks Kompetensi<br>';
								soal += '**Hanya diisi untuk proyeksi jabatan Managerial dan Supervisory atau jabatan lain dengan subordinat</div>';
								soal += '<hr>';
								soal += '<div class="col-md-12" style="font-size:12px;">';
									soal += '<div class="row">';
										soal += '<label class="col-sm-2 col-form-label">(1) LIMITED</label>';
											soal += '<div class="col-sm-8">';
												soal += '- Memahami tapi belum mendemonstrasikan kompetensi<br> - Dibutuhkan instruksi terperinci untuk mendemonstrasikan kompetensi';
											soal += '</div>';
									soal += '</div>';
									soal += '<hr>';
									soal += '<div class="row">';
										soal += '<label class="col-sm-2 col-form-label">(2) BASIC</label>';
											soal += '<div class="col-sm-8">';
												soal += '- Mendemonstrasikan kompetensi pada tugas sehari-hari<br>- Dibutuhkan sedikit instruksi untuk mendemonstrasikan kompetensi';
											soal += '</div>';
									soal += '</div>';
									soal += '<hr>';
									soal += '<div class="row">';
										soal += '<label class="col-sm-2 col-form-label">(3) PROFICIENT</label>';
											soal += '<div class="col-sm-8">';
												soal += '- Mendemonstrasikan kompetensi pada situasi yang kompleks<br>- Mendemonstrasikan kompetensi secara mandiri';
											soal += '</div>';
									soal += '</div>';
									soal += '<hr>';
									soal += '<div class="row">';
										soal += '<label class="col-sm-2 col-form-label">(4) ADVANCED</label>';
											soal += '<div class="col-sm-8">';
												soal += '- Mendemonstrasikan kompetensi pada situasi dengan tingkat kesulitan yang tinggi<br>- Mendemonstrasikan kompetensi ini dengan improvisasi';
											soal += '</div>';
									soal += '</div>';
									soal += '<hr>';
									soal += '<div class="row">';
										soal += '<label class="col-sm-2 col-form-label">(5) EXPERT</label>';
											soal += '<div class="col-sm-8">';
												soal += '- Mendemonstrasikan kompetensi pada situasi dengan tingkat kesulitan yang sangat tinggi<br>- Mengajarkan dan menjadi contoh bagi orang lain dalam mendemonstrasikan kompetensi ini';
											soal += '</div>';
									soal += '</div>';
								soal += '</div>';
							}
							soal += '</div>';
							$('#group_soal').html(soal);
						}, 50);
						$('#loader').addClass('hidden');
					});
				}
				
            },
			complete: function(){
			//	$('#loader').addClass('hidden');
			},
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
		$("#myModal").modal('show'); 

}

$('#questionForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#questionForm input").removeClass("is-invalid");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('interview_ques.update') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
                                $('#myModal').modal('hide');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
									location.reload();
								   }
								);
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
            
        });	


$(document).on("click", ".advanced_can", function () {
	$('.cf').select2({width:'100%'});
	if($(".candidate_table").css('display') == 'none'){
		$(".candidate_table").show("slow");
	}
	else {
		$(".candidate_table").hide("slow");
	}   
});

const get_datatable_candidate = async () => {
    $(".advanced_can").show();

	$("#table_div_can").html("");	
	$("#table_div_can").html('<table id="candidate_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let t_can =	$('#candidate_table').DataTable({
			processing: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(2)'
			},
			responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('interview_ques.index') }}",
				data: {id_url: global_url_server},	
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#candidate_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{defaultContent: '',orderable: false},
				{   // Checkbox select column
				data: 'id_candidate',
				orderable: false,
				targets: 1,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{defaultContent: '', title: 'No.' ,orderable: false, responsivePriority: 2},
				{ data: 'photo_candidate', title: 'Photo', responsivePriority: 4, className: 'text-middle', render: function ( data, type, row ) {	
						return '<img align="center" class="img-circle elevation-2" src="https://career.borwita.co.id/storage/candidate_photo/'+data+'" style="height:50px;width:50px;border-radius:999px;" alt="User Avatar">';
					} 
				},
				{ data: 'name', title: 'Name', responsivePriority: 3},
				{ data: 'interest', title: 'Interesting', responsivePriority: 6},
				{ data: 'request_position', title: 'Request Position', responsivePriority: 5},
				{ data: 'rec_stage', title: 'Rec. Stage', responsivePriority: 7, render: function ( data, type, row ) {
						if(row.code_stage == 'HIR'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_stage == 'CLJ'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
								return data;
						}
					}
				},
				{ data: 'status', title: 'Status', responsivePriority: 8, render: function ( data, type, row ) {
						if(row.status == 'Pass'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Pass</span>';
						}
						else if(row.status == 'Review'){
								return '<span class="badge badge-info" style="padding:5px;font-size:12px;">Review</span>';
						}
						else if(row.status == 'Failed'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Failed</span>';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, width: '100px', className: 'th-text-score', render: function ( data, type, row ) {	
						return '<div align="center">'+data+'</div>';
					} 
				},
			],
			"fnInitComplete": function (oSettings) {
			   $('#candidate_table_wrapper .column-filter-widget:eq(11)').css('display','none').change();
			}
		});
		
		t_can.on('order.dt search.dt', function () {
        let i = 1;
        t_can.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}
	
$(document).ready(function(){
	document.addEventListener("visibilitychange", function(event) {
	//  $('#candidate_table').DataTable().ajax.reload(); 
});
		get_datatable_candidate();
	//	get_conclusion();
		
	$('#interview_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});		
});

const get_question = async (id_candidate,id_applied,id_candidate_status) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/personality_assessment/interview_question/get_question') ?>',
			data: {id_candidate: id_candidate,id_applied: id_applied,id_candidate_status: id_candidate_status},
            dataType: 'json',
			beforeSend: function () {
			//	$('#loader').removeClass('hidden');
				$('#int_notes').val('').trigger('change');
			},
            success: function (res) {
				
            },
        });
        return result;
    } catch (error) {
  //      get_question(id_candidate,id_applied,id_candidate_status);
    }	
}

/*
function get_conclusion(){		
		$.getJSON('<?= url('recruitment/personality_assessment/interview_question/get_conclusion') ?>', function (data) {			
			$('#id_conclusion').prepend('<option selected></option>').select2({
				placeholder: "Select Conclusion ...",
				data: data,
			});	
				
		}).fail(function (data) { // Call failed
            get_conclusion();
		});					
}
*/	
function printPdf(id_candidate,id_applied,id_group) {
	window.open(`<?= url('recruitment/personality_assessment/interview_question/print') ?>?id_candidate=${id_candidate}&id_applied=${id_applied}&id_group=${id_group}`, 'print'+Math.random());
}
</script>
@endsection