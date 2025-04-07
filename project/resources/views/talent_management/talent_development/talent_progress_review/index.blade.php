@extends('adminlte::page')
@section('title', 'Talent Progress')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Talent Progress</h5>
				<div class="card-tools">
                </div>
            </div>      
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="talent_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
					<th data-priority="2"></th>
					<th data-priority="3"></th>
					<th data-priority="4">No</th>
					<th data-priority="5">Recommendation Name</th>
					<th data-priority="6">Employee Name</th>
					<th data-priority="7">Projected Position</th>
					<th data-priority="13">Batch Name</th>
					<th data-priority="8">Period</th>
					<th data-priority="9">Average KPI</th>
					<th data-priority="10">Potencies</th>
					<th data-priority="11">Competencies</th>
					<th data-priority="12">Engagement Level</th>
					<th data-priority="1" style="text-align:center;" width=100>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_batch"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="batchForm">
                {{ csrf_field() }}
                <div class="modal-header">
					<h5 id="title_batch" class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentBatch">
                    
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_batch"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_bei"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="beiForm">
                {{ csrf_field() }}
                <div class="modal-header">
					<h5 id="title_bei" class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentBei">
                    
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
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
	td.text-center{
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
	th.th-text-date{
		width:80px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
	.modal{
		overflow:auto !important;
	}
</style>
@stop
@section('scripts')
<script type="text/javascript">

$(document).ready(function(){
	
	var t_able = $('#talent_table').DataTable({
            processing: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('progress.index') }}",
    		    error: function (jqXHR, textStatus, errorThrown) {
    			//		$('#talent_table').DataTable().ajax.reload();
    				}
    		  },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_talent_recommendation_summary',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'reco_name', name: 'reco_name' },
				{ data: 'emp_name', name: 'emp_name' },
    			{ data: 'pro_pos', name: 'pro_pos' },
    			{ data: 'batch_name', name: 'batch_name' },
    			{ data: 'kpi_desc', name: 'kpi_desc' },
    			{ data: 'kpi_average', name: 'kpi_average' },
    			{ data: 'potencies', name: 'potencies' },
    			{ data: 'competencies', name: 'competencies' },
    			{ data: 'eng_level', name: 'eng_level' },
    			{ data: 'action', name: 'action', orderable: false, className: 'space' },
            ],
		/*	"fnInitComplete": function (oSettings) {
			   $('.oke').find('.dt-checkboxes').click();
			}
        */ 
        });
	
    	$('#advanced').click(function(){
			$('.cf').select2({width:'100%'});
			if($("#cf").css('display') == 'none'){
				$("#cf").show("slow");
			}
			else {
				$("#cf").hide("slow");
			}		
		});
		
});

const getBranch = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/master_batch/get_branch_batch') ?>',
			data: {id_url: global_url_server},
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
     //   getBranch();
    }
}

$(document).on('click', '.batch', function () {
	
	let id_talent = 0;
//	let global_name_batch = "";
	let global_emp_name = "";

		id_talent = $(this).attr('id');
	//	global_name_batch = $(this).attr('name_batch');
		global_emp_name = $(this).attr('emp_name');
		$("#contentBatch").html('');
		$("#batchForm")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#batchForm input").removeClass("is-invalid");
				
		$("#title_batch").html('Generate Batch ('+global_emp_name+')');
		$.ajax({
				url: "{{ route('progress.modal_batch') }}",
				data:{
					global_talent:id_talent,
				//	global_name_batch:global_name_batch,
				},
				success: function(result){
				$("#contentBatch").html(result);
				$("#id_summary_batch").val(id_talent).trigger('change');
				$('#modal_form_batch').modal('show');
			}
		});
		
   });	

$('#batchForm').submit(function (e) {			
		e.preventDefault();
		let formData = $(this).serializeArray();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#batchForm input").removeClass("is-invalid");
			$.ajax({
				type: 'POST',
				headers: {
					Accept: "application/json",
				},
				url: "{{ route('progress.save_batch') }}",
				data: formData,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
					if (response.status == 'true') {
							$('#modal_form_batch').modal('hide');
							swal({
								icon: 'success',
								title: 'Success',
								text: response.message
							});
					} 
					else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! '+response.message,
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

const getTypeBei = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_type_bei') ?>',
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
     //   getBranch();
    }
}

$(document).on('click', '.bei', function () {
	
	let id_talent = 0;
//	let global_name_batch = "";
	let global_emp_name = "";

		id_talent = $(this).attr('id');
	//	global_name_batch = $(this).attr('name_batch');
		global_emp_name = $(this).attr('emp_name');
		$("#contentBei").html('');
		$("#beiForm")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#beiForm input").removeClass("is-invalid");
				
		$("#title_bei").html('Generate BEI ('+global_emp_name+')');
		$.ajax({
				url: "{{ route('progress.modal_bei') }}",
				data:{
					global_talent:id_talent,
				},
				success: function(result){
				$("#contentBei").html(result);
				$("#id_summary_bei").val(id_talent).trigger('change');
				$('#modal_form_bei').modal('show');
			}
		});
		
   });	
   
   $('#beiForm').submit(function (e) {			
		e.preventDefault();
		let formData = $(this).serializeArray();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#beiForm input").removeClass("is-invalid");
			$.ajax({
				type: 'POST',
				headers: {
					Accept: "application/json",
				},
				url: "{{ route('progress.save_bei') }}",
				data: formData,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
					if (response.status == 'true') {
							$('#modal_form_bei').modal('hide');
							swal({
								icon: 'success',
								title: 'Success',
								text: response.message
							});
					} 
					else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! '+response.message,
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

</script>
@endsection