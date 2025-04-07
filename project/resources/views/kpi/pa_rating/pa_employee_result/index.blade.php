@extends('adminlte::page')
@section('title', 'PA Rating')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Score Total & Propose Rating</h5>               
            </div>      
			<div class="card-body">
				<div class="form-group row">              
					<label class="col-sm-2 col-form-label">Period :</label>
					<div class="col-sm-5">
						<select id="period" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
					</div>										
				</div>
				<div class="div_datatable" style="display:none;"> 
					<button onclick="return false;" class="btn btn-default pull-left advanced_kpi">Advanced Search</button>
					<button type="button" onclick="return false;" id="calpa" class="btn btn-sm btn-success pull-right"><i class="fas fa-refresh"></i> Calculate Scoring PA</button>
						<br>
						<br>
						<table id="kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						 <thead>
						  <tr>				   
							<th></th>
							<th></th>
							<th>No</th>
							<th>NIK</th>
							<th data-priority="2">Employee</th>
							<th >Grade</th>
							<th>Periode</th>
							<th data-priority="3" style="text-align:center;">Score 360(%)</th>
							<th data-priority="4" style="text-align:center;">Score KPI(%)</th>
							<th data-priority="5" style="text-align:center;">Score Total(%)</th>
							<th data-priority="6" style="text-align:center;">Propose Rating</th>
							<th data-priority="7" style="text-align:center;">Final Rating</th>
							<th data-priority="8" style="text-align:center;">Keterangan</th>
							<th data-priority="1" style="text-align:center;" width=50>Action</th>
						  </tr>
						 </thead>
						</table>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_rating"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <form method="POST" id="ratingForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h6 class="modal-title">Propose Rating</h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Propose Rating</label>
                                <div class="col-sm-8">
									<input name="id_kpi_total" id="id_kpi_total" type="hidden">	
                                    <select id="id_code_promotion" name="id_code_promotion" class="select2 form-control form-control-sm"></select>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Keterangan</label>
                                <div class="col-sm-8">
                                    <textarea id="notes" name="notes" class="form-control form-control-sm"></textarea>
                                </div>
                            </div>
						</div> 						
                    </div>
                </div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
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
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_kpi_total = 0;

function calpa() {
	let myData = {
			period: $("#period").val() == '' ? null : $("#period").val(),
		};
	$.ajax({
            url: '<?= url('kpi/pa_rating/pa_employee_result/calpa') ?>',
			data: myData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				if (response.status == 'true') {
					swal({
						icon: 'success',
						title: 'Success',
						text: 'Calculate Score All Successfully'
					});
					$('#kpi_table').DataTable().ajax.reload();
				} else {
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [Unknown Error]'
					});
				}		
            },
			complete: function(){
				$('#loader').addClass('hidden');
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
}
	$(document).on('click', '#calpa', function () {		
		calpa();
	});
	
	
	$(document).on('click', '#search_period', function () {
			calpa();
			get_datatable();
	});
	
	$(document).on("click", ".advanced_kpi", function () {
		$('.cf').select2({width:'100%'});
		if($(".kpi_table").css('display') == 'none'){
			$(".kpi_table").show("slow");
		}
		else {
			$(".kpi_table").hide("slow");
		}   
	});
	
	const get_datatable = async () => {
		$(".div_datatable").show();
		let myData = {
			period: $("#period").val() == '' ? null : $("#period").val(),
		};
		$('#kpi_table').DataTable({
			processing: true,
			responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('rating.index') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#kpi_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{
				defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
				data: 'id_kpi_total',
				defaultContent: '',
				orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-middle'},				
				{ data: 'nik_employee', name: 'nik_employee', className: 'text-middle'},
				{ data: 'name', name: 'name'},           
				{ data: 'grade', name: 'grade', className: 'text-middle'},
				{ data: 'period', name: 'period', className: 'text-middle'},
				{ data: 'total_qualitative_score', name: 'total_qualitative_score', className: 'text-middle'},
				{ data: 'total_quantitative_score', name: 'total_quantitative_score', className: 'text-middle'},
				{ data: 'total_kpi_score', name: 'total_kpi_score', className: 'text-score'},
				{ data: 'propose_rating', name: 'propose_rating', className: 'text-score', render: function ( data, type, row ) 
					{	
						if(data != null){
							return '<span style="font-size:14px;" class="badge badge-success">'+data+'</span>';
						}
						else{
							return '';
						}
					} 
				},
				{ data: 'final_rating', name: 'final_rating', className: 'text-score', render: function ( data, type, row ) 
					{	
						if(data != null){
							return '<span style="font-size:14px;" class="badge badge-success">'+data+'</span>';
						}
						else{
							return '';
						}
					} 
				},
				{ data: 'notes', name: 'notes', className: 'text-middle'},
				{ data: 'action', name: 'action', className: 'text-middle', orderable: false, render: function ( data, type, row ) {	
						return data;
					} 
				},
			],
		});
	}

	$('#ratingForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();			
	
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#ratingForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('rating.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_rating').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#kpi_table').DataTable().ajax.reload();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: `Something went wrong! [${response.message}]`
                            });
                        }
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


 $(document).on('click', '.edit', function () {
		global_id_kpi_total = $(this).attr('id');
		$("#ratingForm")[0].reset();
		$('#id_code_promotion').empty();
		get_edit(global_id_kpi_total);
		
		$('#modal_form_rating').modal('show');
   });	
   
    function get_edit(global_id_kpi_total) {
		$.ajax({
			url: "<?= url('kpi/pa_rating/pa_employee_result/get_rating_edit') ?>",
            method: "GET",
            data: {id_kpi_total: global_id_kpi_total},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
				$("#ratingForm .modal-title").html('Propose Rating ('+response.name+')');
				$('#id_kpi_total').val(response.id_kpi_total).trigger('change');
				$('#notes').val(response.notes).trigger('change');
				get_rating(response.id_job_grade,response.id_code_promotion,response.emp_status,response.day_join);	
				
            },
			complete: function(){
				$('#loader').addClass('hidden');
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
	}

$(document).ready(function(){
		get_period();
});

function get_period() {
	$.getJSON('<?= url('kpi/pa_rating/pa_employee_result/get_period') ?>', function (data) {
			$('#period').select2({
                data: data
            });
		}).then(function (data){
			$('#search_period').trigger('click');
		}).fail(function (data) { // Call failed
            get_period();
        });	
}

function get_rating(id_job_grade,id_code_promotion=null,emp_status,day_join) {	
	$.getJSON("<?= url('kpi/pa_rating/pa_employee_result/get_rating') . '?id_job_grade=' ?>" + id_job_grade +"<?= '&emp_status='?>"+emp_status+"<?= '&day_join='?>"+day_join, function (data) {
		$('#id_code_promotion').select2({
			data: data
		});
		if(id_code_promotion != null){
			$('#id_code_promotion').val(id_code_promotion).trigger('change');
		}
	}).fail(function (data) { // Call failed
		get_rating();
	});	
}
</script>
@endsection