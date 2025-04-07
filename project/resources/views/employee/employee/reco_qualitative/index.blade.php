@extends('adminlte::page')
@section('title', '360 Feedback')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">360 Feedback (Qualitative)</h5>               
            </div>      
			<div class="card-body">
				<button onclick="return false;" class="btn btn-default pull-left advanced_appraiser">Advanced Search</button>
				<!-- button type="button" class="btn btn-sm btn-primary pull-right" onclick="load_detail()"><i class="fa fa-list-alt"></i> Review 360 Subordinat</button -->
				<br>
				<br>
				<table id="appraiser_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>				   
					<th></th>
					<th></th>
					<th>No</th>
					<th>Penilai</th>
					<th data-priority="4">Type</th>
					<th>NIK Dinilai</th>
					<th data-priority="2">Nama Dinilai</th>
					<th data-priority="6">Grade</th>
					<th>Company</th>
					<th data-priority="5">Score</th>
					<th data-priority="3">Assessment</th>
					<th data-priority="1" style="text-align:center;" width=50>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_appraiser"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="appraiserForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 id="soal_dinilai" class="modal-title">Soal</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
						<ol id="group_soal">
						</ol>
						</div> 
							
                    </div>
                </div>
				<div class="modal-footer">
                    <button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-paper-plane"></i> Submit</button>
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
	<div id="modal_second"></div>
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">List 360 Subordinat</h5>
			<button type="button" onclick="javascript:window.location.reload()" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody" style="height:500px;overflow:auto;">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-default advclose" data-dismiss="modal">Close</button>
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
	}
	.btn-answer {
	  color: none;
	  text-align: justify;	  
	  display:flex;
	  cursor:pointer;
	}
	.btn-answer:hover {
	  color: #fff;
	  background-color: #218838;
	}
	.swal-wide{
		width:600px !important;
	}
</style>
@stop
@section('scripts')
<!-- script type="text/javascript" src="https://cdn.rawgit.com/ashl1/datatables-rowsgroup/fbd569b8768155c7a9a62568e66a64115887d7d0/dataTables.rowsGroup.js"></script -->
<script type="text/javascript">
let global_emp_name = "";

function loadedit(id_qualitative_appraisers,id_recommendation_qualitative,id_recommendation_header){
        $("#appraiserForm")[0].reset();
        $(".invalid-feedback").children("strong").text("");
        $("#appraiserForm input").removeClass("is-invalid");
        $("#appraiserForm textarea").removeClass("is-invalid");

        $.ajax({
            url: "<?= url('employee/employee/reco_qualitative/get_appraiser_edit') ?>",
            method: "GET",
            data: {id_qualitative_appraisers: id_qualitative_appraisers},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				if (response.status == 'true') {
					global_emp_name = response.result.master[1].emp_name;
					$('#soal_dinilai').html('Penilaian Untuk '+response.result.master[1].emp_name);
					$('#id_qualitative_appraisers').val(response.result.master.id_qualitative_appraisers).trigger('change');
					let soal = '';
					soal += '<input hidden name="id_employee_participant" value="'+response.result.master[1].id_employee+'" >';
					soal += '<input hidden name="id_recommendation_qualitative" value="'+id_recommendation_qualitative+'" >';
					soal += '<input hidden name="id_recommendation_header" value="'+id_recommendation_header+'" >';
				
					$.each(response.result.master, function (i, item) {
						$('#group_soal').html('');
							soal += '<div class="row" style="font-size:16px;">';
							soal += '<li>';
								soal +=	'<div class="col-sm-11 col-form-label" align="justify">'+item.soal;
								soal += '<input hidden name="id_qualitative_appraisers" value="'+item.id_qualitative_appraisers+'" >';
								soal += '<input hidden name="soal[]" value="'+item.id_pa_question+'" >';
								soal +=	'<div style="padding:5px 0 10px 0;"><b>Jawaban :</b></div>';	
									
									var result_ = Object.values(response.result.answers);
									
										var id_appraiser_results = '';
										var id_pa_question = '';
										var id_pa_answer = '';
										var desc_answer = '';
										
										if(result_.length > 0){
											id_appraiser_results = response.result.answers[i].id_appraiser_results;
											id_pa_question = response.result.answers[i].id_pa_question;
											id_pa_answer = response.result.answers[i].id_pa_answer;
											desc_answer = response.result.answers[i].desc_answer;
										}
											
										$.each(item.answer, function (j, ans) {
											var checked = '';
											if(id_pa_answer == ans.id_pa_answer){
												checked = 'checked';
											}
											soal +=	'<label for="answers_'+ans.id_pa_answer+'" class="row btn-answer callout callout-success" style="font-weight: normal;">';
												soal += '<div class="icheck-success col-sm-3">';
													soal += '<input type="radio" id="answers_'+ans.id_pa_answer+'" name="answers_'+item.id_pa_question+'[]" autocomplete="off" value="'+ans.id_pa_answer+'" '+checked+'>';
													soal +=	'<label for="answers_'+ans.id_pa_answer+'" style="margin-top:-5px;">'+ans.sequence+'</label>';
												soal += '</div>';
												soal += '<div class="col-sm-9" style="float:right;">'+ans.desc_answer+'</div>';
											soal += '</label>';					
										});
								soal += '</div>';
							soal += '</li>';
							/*
								soal += '<div class="row">';
									$.each(item.answer, function (j, answ) {								
										soal += '<div style="padding:10px 10px 0 15px;">';
										soal +=		'<div class="icheck-success btn btn-outline-success" style="color:black;">';
										soal +=     	'<input type="radio" id="answers_'+answ.id_pa_answer+'" name="answers_'+item.id_pa_question+'[]" autocomplete="off" value="'+answ.id_pa_answer+'" >';
										soal +=			'<label for="answers_'+answ.id_pa_answer+'">'+answ.sequence+'</label>';
										soal += 	'</div>';					
										soal += '</div>';					
									});					
								soal +=	'</div>';
							*/	
								soal +=	'<div class="col-sm-12"><span><b>Bukti Perilaku :</b></span></div>';
								soal += '<div class="col-sm-11" style="padding:5px 0px 0px 8px;">';
									soal +=	'<textarea class="form-control form-control-sm" name="ans_'+item.id_pa_question+'" placeholder="Isi Min 30 Karakter">'+desc_answer+'</textarea>';
								soal +=	'</div>';
							soal +=	'</div><br>';						
							soal +=	'<div class="col-sm-11" style="border-bottom:1px solid #ccc;padding-bottom:5px;"></div>';
						$('#group_soal').html(soal);
					});
					$('#modal_form_appraiser').modal('show');
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
    };
	
	$('#appraiserForm').submit(function (e) {
		e.preventDefault();
		let formData = $(this).serializeArray();			

		$(".invalid-feedback").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#appraiserForm input").removeClass("is-invalid");
		$("#appraiserForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text("");
		$(".error-tab").html("");
		swal({
			title: 'Apakah anda yakin penilaian terhadap '+global_emp_name+' ?',
			text: 'Penilaian akan disubmit dan tidak bisa diperbaiki',
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
					url: "{{ route('quali_form.update') }}",
					data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
					success: function (response) {
						if (response.status == 'true') {
							$('#modal_form_appraiser').modal('hide');
							swal({
								icon: 'success',
								title: 'Success',
								text: response.message
							});
							$('#appraiser_table').DataTable().ajax.reload();
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

$(document).ready(function(){

get_datatable();
	
	$(document).on("click", ".advanced_appraiser", function () {
		$('.cf').select2({width:'100%'});
		if($(".appraiser_table").css('display') == 'none'){
			$(".appraiser_table").show("slow");
		}
		else {
			$(".appraiser_table").hide("slow");
		}   
	});

});

const get_datatable = async () => {		
		$('#appraiser_table').DataTable({
			processing: true,
			responsive: true,
			destroy: true,
		//	'rowsGroup': [3],
			ajax: {
				url: "{{ route('quali_form.index') }}",
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#appraiser_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{
				defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
				data: 'id_employee_participant',
				defaultContent: '',
				orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
				{ data: 'penilai', name: 'penilai'},
				{ data: 'appraisers_hierarchy', name: 'appraisers_hierarchy', orderable: false, render: function ( data, type, row ) {						
						data = data.toLowerCase().replace(/\b[a-z]/g, function(letter) {
							return letter.toUpperCase();
						});
						if(data == 'Direct'){
							return data+' Line';
						}
						else{
							return data;
						}
					} 
				},
				{ data: 'nik_dinilai', name: 'nik_dinilai'},
				{ data: 'name_dinilai', name: 'name_dinilai'},           
				{ data: 'grade', name: 'grade' },
				{ data: 'company_dinilai', name: 'company_dinilai'},
				{ data: 'subtotal_score', name: 'subtotal_score' },
				{ data: 'submitted', name: 'submitted', className: 'text-center', orderable: false, render: function ( data, type, row ) {	
						if(data == true){
							return '<span class="badge badge-success">YES</span>';
						}
						else{
							return '<span class="badge badge-danger">NO</span>';
						}
					} 
				},
				{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {	
						return data;
					} 
				},
			],
		});
		
	}	
	

</script>
@endsection