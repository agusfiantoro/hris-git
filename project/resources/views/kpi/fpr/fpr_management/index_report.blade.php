@extends('adminlte::page')
@section('title', 'FPR Management')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">FPR Management</h5>               
            </div>      
			<div class="card-body">
			<div class="form-group row">    
					<div class="col-sm-5">
						<select id="emp_participant" name="emp_participant" class="form-control form-control-md select2" style="width: 100%;"></select>
					</div>
                    <div class="col-sm-3">
                        <select id="period" class="form-control form-control-sm select2" style="width: 100%;"></select>
						<span class="invalid-feedback" role="alert" id="periodError">
								<strong></strong>
						</span>
                    </div>
					<div class="col-sm-4">
                        <button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>            
                        <button onclick="return false;" id="gen_fpr" class="btn btn-sm btn-success" ><i class="fa fa-refresh"></i> Generate Mapping FPR</button>
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
						<th>NIK Employee</th>
						<th data-priority="2">Employee</th>
						<th data-priority="3">Atasan Approve</th>
						<th data-priority="5">Employee Approve</th>
						<th data-priority="6">Submit</th>
						<th data-priority="7">Review Date</th>
						<th data-priority="1" width=50>View</th>
					  </tr>
					 </thead>
					</table>
				</div>
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
	td.text-name{
		vertical-align:middle;
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
	
$(document).on('click', '#gen_fpr', function () {
	let myData = {
        id_employee: $("#emp_participant").val() == '' ? null : $("#emp_participant").val(),
        period: $("#period").val() == '' ? null : $("#period").val(),
		path : "<?= \Request::path() ?>",
    };
		$.ajax({
            url: '<?= url('kpi/fpr/fpr_management/generate_fpr') ?>',
			dataType: "json",
            data: myData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
			//	localStorage.setItem("id_period_quantitative", $("#period").val());
                get_datatable();
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (res) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
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
		id_employee: $("#emp_participant").val() == '' ? null : $("#emp_participant").val(),
		period: $("#period").val() == '' ? null : $("#period").val(),
	};

	$('#fpr_table').DataTable({
		processing: true,
		responsive: true,
		destroy: true,
	//	'rowsGroup': [3],
		ajax: {
			url: "<?= url('kpi/fpr/fpr_management/') . '?id_url=' ?>" + global_url_server,
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
				{ data: 'id_approval_appraisers', name: 'id_approval_appraisers', className: 'text-middle', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{
							return '<span class="badge badge-success">'+data+'</span>';							
						}
					}
				},
				{ data: 'id_approval_participant', name: 'id_approval_participant', className: 'text-middle', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{							
							return '<span class="badge badge-success">'+data+'</span>';
						}
					}
				},
				{ data: 'submitted', name: 'submitted', className: 'text-middle', render: function ( data, type, row ) {	
						if(data == "NO"){
							return '<span class="badge badge-danger">'+data+'</span>';
						}
						else{
							return '<span class="badge badge-success">'+data+'</span>';
						}
					}
				},
				{ data: 'review_date', name: 'review_date', className: 'text-middle'},
				{ data: 'action', name: 'action', orderable: false, className: 'text-score', render: function ( data, type, row ) {	
						return data;
					} 
				},
			],
		});
		
	}	
	
refresh_data();

function refresh_data() {
	get_employee_filter();
	get_period();	
}

function get_employee_filter() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_employee_filter') . '?id_url=' ?>' + global_url_server, function (data) {
			$('#emp_participant').prepend('<option selected></option>').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_employee_filter();
        });	
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

$(document).on("click", ".download", function () {
	let res = {
        id_fpr_header: $(this).attr('id'),
    };
    let param = objectToQueryString(res);
    let url = "{{ url('kpi/fpr/fpr_management/download') }}";
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

</script>
@endsection