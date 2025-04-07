@extends('adminlte::page')
@section('title', 'Talent Interview')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Talent Interview</h5>               
            </div>      
			<div class="card-body">
				<button onclick="return false;" class="btn btn-default pull-left advanced_emp">Advanced Search</button><br><br>
				<div id="table_div_emp"></div>
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
				
				<div class="card card-success card-outline" style="margin-top:10px;">
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
																	<input id="id_employee" name="id_employee" type="hidden">
																	<input id="id_recruitment_answer_header" name="id_recruitment_answer_header" type="hidden">
																	<input type="text" name="interview_date" id="interview_date" class="form-control form-control-sm">
																	<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="interview_dateError">
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
let global_code = "";

function on_close_modal() {
	location.reload();
}

function loadprofile(id_employee,id_group){
	 $.ajax({
			url: "<?= url('talent_management/talent_development/functional_interview/get_edit_question') ?>",
            method: "GET",
            data: {id_employee: id_employee,id_group: id_group},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
					$('#id_employee').val(id_employee).trigger('change');
					global_interview = response.question_group.id_question_group;
					global_code = response.question_group.code;
					get_question(id_employee,global_interview).then(function(res) {
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
										if(global_code == 'BEI'){
											if(i == 1){
												soal += '<label style="font-size:18px;">BEI GUIDELINE</label>';
												soal += '<hr style="margin-top:0;">';
											}
										}
										if(competency != item.competency_group){
											soal += '<br><label style="font-size:16px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											competency = item.competency_group;
											soal += '<hr style="margin-top:0;margin-bottom:10px;">';
										}
										if(group_essay != item.category_group){											
											soal += '<label style="font-size:16px;margin-bottom:10px;">'+item.category_group+'</label>';
											group_essay = item.category_group;
										}
										
										if(global_code == 'BEI'){
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
																			
									}									
									else if(item.question_type == 'Single_Answer'){
										
										if(global_code == 'BEI'){
												if(sheet != 'BEI SHEET'){
												//	soal += '<hr style="margin-top:0;">';
													soal += '<label style="font-size:18px;margin-top:20px;">BEI SHEET</label>';
													sheet = 'BEI SHEET';
													soal += '<hr style="margin-top:0;margin-bottom:0;">';
												}
											}
										if(competency != item.competency_group){
											soal += '<br><label style="font-size:16px;margin-bottom:10px;">'+item.competency_group+'</label><br>';
											competency = item.competency_group;
											soal += '<hr style="margin-top:0;margin-bottom:10px;">';
										}
										if(group_single != item.category_group){
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
							if(global_code == 'BEI'){
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
					url: "{{ route('funct_int.update') }}",
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


$(document).on("click", ".advanced_emp", function () {
	$('.cf').select2({width:'100%'});
	if($(".emp_table").css('display') == 'none'){
		$(".emp_table").show("slow");
	}
	else {
		$(".emp_table").hide("slow");
	}   
});

const get_datatable_emp = async () => {
    $(".advanced_emp").show();

	$("#table_div_emp").html("");	
	$("#table_div_emp").html('<table id="emp_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let t_emp =	$('#emp_table').DataTable({
			processing: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(2)'
			},
			responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('funct_int.index') }}",
				data: {id_url: global_url_server},	
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#emp_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{defaultContent: '',orderable: false},
				{   // Checkbox select column
				data: 'id_employee',
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
				{ data: 'photo', title: 'Photo', responsivePriority: 8, className: 'text-middle', render: function ( data, type, row ) {
						let photo = "";
						if(row.image_length > 1000){
							photo = "data:image;base64,"+data;
						}
						else{
							photo = row.filePath+"/"+data;
						}
						return '<img align="center" class="img-circle elevation-2" src="'+photo+'" style="height:50px;width:50px;border-radius:999px;" alt="User Avatar">';
					} 
				},			
				{ data: 'name', title: 'Name', responsivePriority: 3},
				{ data: 'nik_employee', title: 'NIK', responsivePriority: 4},
				{ data: 'position', title: 'Position', responsivePriority: 7},
				{ data: 'projected_pos', title: 'Projected Position', responsivePriority: 5},
				{ data: 'bei_conclusion', title: 'Conclusion', responsivePriority: 6},
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, width: '100px', className: 'th-text-score', render: function ( data, type, row ) {	
						return '<div align="center">'+data+'</div>';
					} 
				},
			],
			"fnInitComplete": function (oSettings) {
			//   $('#emp_table_wrapper .column-filter-widget:eq(11)').css('display','none').change();
			}
		});
		
		t_emp.on('order.dt search.dt', function () {
        let i = 1;
        t_emp.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}
	
$(document).ready(function(){
	document.addEventListener("visibilitychange", function(event) {
	//  $('#emp_table').DataTable().ajax.reload(); 
});
		get_datatable_emp();
	//	get_conclusion();
		
	$('#interview_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});		
});

const get_question = async (id_employee,id_question_group) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/functional_interview/get_question') ?>',
			data: {id_employee: id_employee,id_question_group: id_question_group},
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
</script>
@endsection