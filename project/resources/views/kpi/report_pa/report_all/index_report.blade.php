@extends('adminlte::page')
@section('title', 'Report All & Rating')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Report All & Rating</h5>               
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
                <div class="form-group row">              
					<label class="col-sm-2 col-form-label">Employee Appraiser :</label>
					<div class="col-sm-5">
						<select id="employee" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						{{-- <button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button> --}}
					</div>										
				</div>
				<button type="button" id="upload" class="new btn btn-sm btn-primary pull-right"><i class="fas fa-upload"></i> Import Final Rating</button>
                <button type="button" id="calculatePA" class="btn btn-sm btn-success mr-2 pull-right" style="display:none;"><i class="fa fa-play"></i> Calculate PA</button>

				<div class="div_datatable" style="display:none;"> 
					<button onclick="return false;" class="btn btn-default pull-left advanced_kpi">Advanced Search</button>
						<br>
						<br>
						<table id="kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						 <thead>
						  <tr>				   
							<th></th>
							<th></th>
							<th>No</th>
                            <th>Employee Appraiser</th>
							<th>NIK</th>
							<th data-priority="1">Employee</th>
							<th>Grade</th>
							<th>Periode</th>
							<th data-priority="2" style="text-align:center;">Score 360(%)</th>
							<th data-priority="3" style="text-align:center;">Score KPI(%)</th>
							<th data-priority="4" style="text-align:center;">Total Score(%)</th>
							<th data-priority="5" style="text-align:center;">Propose Rating</th>
							<th data-priority="6" style="text-align:center;">Keterangan</th>
							<th data-priority="7" style="text-align:center;">Final Rating</th>
						  </tr>
						 </thead>
						</table>
				</div>
			</div>
        </div>
    </div>
</div>

<div id="uploadModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Import Final Rating</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="upload_form">
            <div class="modal-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">Upload File :</label>
                    <div class="col-md-4">
                        <div class="custom-file">
                            <input type="file" name="attachment" class="custom-file-input" id="attachment">
                            <span class="invalid-feedback" role="alert" id="attachmentError"></span>
                            <label class="custom-file-label"><i>File (.xlsx / .xls)</i></label>
                        </div>
                    </div>

                    <label class="col-md-1 col-form-label">Period :</label>
					<div class="col-md-4">
						<select id="period_upload" name="period" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
                </div>
                <div class="form-group row">
                    
                </div>
            </div>
            <div class="modal-footer">
            	<div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="submit_upload" class="new btn btn-lg btn-success" ><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </div>
            </div>
            </form>

            
        </div>
    </div>
</div>

@endsection
@section('css')
<style type="text/css"> 
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
    get_employee();
	$(document).on('click', '#search_period', function () {
			get_datatable();
            $('#employee').trigger('change');
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
            id_employee: $('#employee').val() == '' ? null : $('#employee').val(),
		};
		$('#kpi_table').DataTable({
			processing: true,
			scrollX: true,
			scrollCollapse: true,
		//	responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('report_rating.index_report') }}",
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
                { data: 'name_appraiser', name: 'name_appraiser'},   		
				{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'name', name: 'name'},          
				{ data: 'grade', name: 'grade' },
				{ data: 'period', name: 'period'},
				{ data: 'total_qualitative_score', name: 'total_qualitative_score', className: 'text-middle'},
				{ data: 'total_quantitative_score', name: 'total_quantitative_score', className: 'text-middle'},
				{ data: 'total_kpi_score', name: 'total_kpi_score', className: 'text-score'},
				{ data: 'rating', name: 'rating', className: 'text-score', render: function ( data, type, row ) 
					{	
						if(data != null){
							return '<span style="font-size:14px;" class="badge badge-success">'+data+'</span>';
						}
						else{
							return '';
						}
					} 
				},
				{ data: 'notes', name: 'notes'},
				{ orderable: true, searchable: true, data: 'final_rating', name: 'final_rating', className: 'text-score', render: function ( data, type, row ) 
					{	
						if(data != null){
							return '<span style="font-size:14px;" class="badge badge-success">'+data+'</span>';
						}
						else{
							return '';
						}
					} 
				},
			],
		});
	}
 
$(document).ready(function(){
	get_period();

	$('#upload').click(function(){
        $('#uploadModal').modal('show');
    });

    $('#submit_upload').click(function(){
        upload();
    });

    $('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html(`<i>${file.name}</i>`);
    });
	
});

function upload(){
    var formData = new FormData($('#upload_form')[0]);
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "<?= url('kpi/report_pa/report_all/upload_final_rating') ?>",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            $('#loader').addClass('hidden')
            if (response.status == 'true') {
                swal({
                    icon: 'success',
                    title: "Success",
                    text: response.message,
                }).then(function(){ 
                    location.reload();
                });
            } else {
                if(response.status == 'false_other'){
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
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! '+response.message,
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                }
            }
        },
        error: function (response) {
            $('#loader').addClass('hidden')
            if (response.status === 422) {
                let errors = response.responseJSON;
                let err = "";
                Object.keys(errors).forEach(function (key) {
                    var key_temp = key.replaceAll(".", "_");
                    $("#" + key_temp).addClass("is-invalid");
                    $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
                    var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
                    err += errors[key][0]+"\n";
                });
                swal({
                    icon: 'error',
                    dangerMode: true,
                    content: {
                        element: "div",
                        attributes: {
                            innerText: err,
                            className: "swal-red",
                        },
                    },
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
            else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+response.statusText,
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
        }
    });                 
} 

function get_period() {
	$.getJSON('<?= url('kpi/pa_rating/pa_employee_result/get_period') ?>', function (data) {
			$('#period').select2({
                data: data
            });
            $('#period_upload').select2({
                data: data
            });
		}).fail(function (data) { // Call failed
            get_period();
        });	
}

function get_employee() {
    $.getJSON("{{ url('kpi/report_pa/report_kpi/get_employee_filter') }}?is_active=1", function(data) {
        $('#employee').empty().prepend('<option></option>').select2({
            data,
            placeholder: 'Select Employee',
            allowClear: true
        });
    });
}

function calpa() {
	let myData = {
        period: $("#period").val() == '' ? null : $("#period").val(),
        id_employee: $('#employee').val()
    };
	$.ajax({
            url: '<?= url('kpi/pa_rating/pa_employee_result/calpa_id_emp') ?>',
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

$(document).on('change', '#employee', function () {
    if($(this).val() == "" || !$.fn.DataTable.isDataTable('#kpi_table')) {
        $('#calculatePA').css('display', 'none');
    } else {
        $('#calculatePA').css('display', 'inline-block');
    }
});

$(document).on('click', '#calculatePA', function() {
    calpa();
});

</script>
@endsection