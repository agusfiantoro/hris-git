@extends('adminlte::page')
@section('title', 'Candidate Interview Summary')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Candidate Interview Summary</h5>               
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
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 class="detail_can modal-title">Interview Question Summary</h5>
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
														<div class="col-md-4">
															<div class="row">
																<label class="col-sm-4 col-form-label">Interview Date</label>
																<div class="col-sm-6">
																	<input id="id_recruitment_answer_header" name="id_recruitment_answer_header" type="hidden">
																	<input type="text" name="interview_date" id="interview_date" class="form-control form-control-sm" readonly>
																</div>
															</div>
														</div>
														<div class="col-md-4">
															<div class="row">
																<label class="col-sm-4 col-form-label">Conclusion :</label>
																<div class="col-sm-8 col-form-label">
																	<div id="conclusion" style="font-size:16px;"></div>
																</div>
															</div>
														</div>
														<div class="col-md-4">
															<div class="row">
																<label class="col-sm-4 col-form-label">Total Score :</label>
																<div class="col-sm-8 col-form-label">
																	<div id="total_score" style="font-size:16px;"></div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="card-body">
												<div class="row">									
													<div class="col-md-12" id="group_soal" style="padding-top:10px;"></div>
													<div class="col-md-12">
														<div style="font-size:16px;font-weight:bold;margin: 10px 0 5px 0;">Notes</div>
														<textarea id="int_notes" name="int_notes" class="form-control form-control-sm" style="height:80px;" readonly></textarea>
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
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
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

function on_close_modal() {
	location.reload();
}

function loadprofile(id_candidate,id_applied,id_group){
	 $.ajax({
			url: "<?= url('recruitment/personality_assessment/interview_question/get_edit_question_summary') ?>",
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
					$('#can_stage').val(response.applied[0].code_status).trigger('change');
					$('#can_status').val(response.applied[0].status).trigger('change');
					$('#can_branch').val(response.applied[0].branch).trigger('change');
					$('#pos_req').val(response.applied[0].pos_req).trigger('change');
					
					global_interview = response.applied[0].id_candidate_status;
					get_question(id_candidate,id_applied,global_interview).then(function(res) {
						setTimeout(function () {
							if(response.hr_answer.length > 0 ){
								$('#id_recruitment_answer_header').val(response.hr_answer[0].id_recruitment_answer_header).trigger('change');
								$('#interview_date').val(moment(response.applied[0].interview_date).format('DD MMM YYYY')).trigger('change');
												
								$('#int_notes').val(response.hr_answer[0].int_notes).trigger('change');
								
								if(response.applied[0].code_stage != 'REF'){
									if(response.hr_answer[0].code == 'Recommended'){
										$('#conclusion').html('<span class="badge badge-success" style="padding:8px;font-size:13px;white-space: normal;">'+response.hr_answer[0].conclusion+'</span>');
										$('#total_score').html('<span class="badge badge-success" style="padding:8px;font-size:14px;">'+response.hr_answer[0].total_score+'</span>');
									}
									else if(response.hr_answer[0].code == 'Considered'){
										$('#conclusion').html('<span class="badge badge-warning" style="padding:8px;font-size:13px;white-space: normal;">'+response.hr_answer[0].conclusion+'</span>');
										$('#total_score').html('<span class="badge badge-warning" style="padding:8px;font-size:14px;">'+response.hr_answer[0].total_score+'</span>');
									}
									else if(response.hr_answer[0].code == 'Not_Recommended'){
										$('#conclusion').html('<span class="badge badge-danger" style="padding:8px;font-size:13px;white-space: normal;">'+response.hr_answer[0].conclusion+'</span>');
										$('#total_score').html('<span class="badge badge-danger" style="padding:8px;font-size:14px;">'+response.hr_answer[0].total_score+'</span>');
									}
								}
								else{
									$('#conclusion').html('-');
									$('#total_score').html('-');
								}
							//	$('#id_conclusion').val(response.hr_answer[0].id_conclusion).trigger('change');
							}
							let soal = '';
						//	console.log(res);
							var group_essay = null; 
							var group_single = null; 
							var sheet = null;
							var competency = null;
							soal += '<div class="col-md-12" style="padding:15px;border:1px solid #ddd;">';
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
													soal += '<textarea id="ans_'+item.id_recruitment_question+'" name="ans_'+item.id_recruitment_question+'" class="form-control form-control-sm" style="height:60px;" placeholder="Silahkan isi disini..." disabled>'+desc_essay+'</textarea>';
												soal += '</div>';
											soal += '</div>';
										}
									/*	if(group_essay != item.category_group){
											soal += '<hr>';
										}
									*/	
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
													
														soal += '<div class="col-sm-6">';
															soal += '<div class="icheck-success" style="color:#212529;">';
																soal += '<input type="radio" id="answers_'+item.id_recruitment_question+'_'+ans.id_recruitment_answer+'" name="answers_'+item.id_recruitment_question+'[]" autocomplete="off" value="'+ans.id_recruitment_answer+'" '+checked+' disabled>';
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
							soal += '</div>';
							$('#group_soal').html(soal);
							$('#loader').addClass('hidden');
						}, 50);
					});
				}
				
            },
			complete: function(){
		//		$('#loader').addClass('hidden');
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
				url: "{{ route('interview_summary.index') }}",
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

		get_datatable_candidate();
		get_conclusion();
		
});

const get_question = async (id_candidate,id_applied,id_candidate_status) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/personality_assessment/interview_question/get_question') ?>',
			data: {id_candidate: id_candidate,id_applied: id_applied,id_candidate_status: id_candidate_status},
            dataType: 'json',
			beforeSend: function () {
				$('#int_notes').val('').trigger('change');
			},
            success: function (res) {
			//	console.log(res);
            },
        });
        return result;
    } catch (error) {
   //     get_question(id_candidate_status);
    }	
}

function get_conclusion(){		
		$.getJSON('<?= url('recruitment/personality_assessment/interview_question/get_conclusion') ?>', function (data) {			
			$('#id_conclusion').prepend('<option selected></option>').select2({
				placeholder: "Select Conclusion ...",
				data: data,
				disabled: true
			});	
				
		}).fail(function (data) { // Call failed
            get_conclusion();
		});					
}	

function printPdf(id_candidate,id_applied,id_group) {
	window.open(`<?= url('recruitment/personality_assessment/interview_question_summary/print_bei') ?>?id_candidate=${id_candidate}&id_applied=${id_applied}&id_group=${id_group}`, 'print'+Math.random());
}
</script>
@endsection