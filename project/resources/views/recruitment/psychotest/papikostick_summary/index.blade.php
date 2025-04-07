@extends('adminlte::page')
@section('title', 'Papikostick Summary')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Papikostick Summary</h5>               
            </div>      
			<div class="card-body">
				<div class="form-group row">              
					<label class="col-sm-3 col-form-label">Grouping By :</label>
					<div class="col-sm-3">
						<select id="grouping" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_grouping" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
					</div>										
				</div>
				<hr>
				<div class="div_datatable_employee" style="display:none;"> 
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Searching Created Date :</label>
						<div class="col-md-4">
							<div class="input-group">
								<input name="daterange_emp" id="daterange_emp" type="daterange" class="form-control form-control-sm" />
								<input name="startdate_emp" id="startdate_emp" class="form-control form-control-sm" hidden>
								<input name="enddate_emp" id="enddate_emp" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="search_created_emp" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
						</div>										
					</div>
					<button onclick="return false;" class="btn btn-default pull-left advanced_employee">Advanced Search</button><br><br>
					<div id="table_div_emp"></div>
				</div>
				
				<div class="div_datatable_can" style="display:none;"> 
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Searching Created Date :</label>
						<div class="col-md-4">
							<div class="input-group">
								<input name="daterange_can" id="daterange_can" type="daterange" class="form-control form-control-sm" />
								<input name="startdate_can" id="startdate_can" class="form-control form-control-sm" hidden>
								<input name="enddate_can" id="enddate_can" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="search_created_can" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
						</div>										
					</div>
					<button onclick="return false;" class="btn btn-default pull-left advanced_can">Advanced Search</button><br><br>
					<div id="table_div_can"></div>
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
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
$(document).on('click', '#search_grouping', function () {
	if($('#grouping :selected').val() == 'candidate'){
		$(".div_datatable_employee").hide();
		$("#candidate_table").html("");
		$("#search_created_can").trigger('click');
	}
	else if($('#grouping :selected').val() == 'employee'){
		$(".div_datatable_can").hide();
		$("#employee_table").html("");
		$("#search_created_emp").trigger('click');
	}	
});

$(document).on("click", ".advanced_employee", function () {
	$('.cf').select2({width:'100%'});
	if($(".employee_table").css('display') == 'none'){
		$(".employee_table").show("slow");
	}
	else {
		$(".employee_table").hide("slow");
	}   
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

$(document).on('click', '#search_created_emp', function () {
    get_datatable_employee();
});

const get_datatable_employee = async () => {
$(".div_datatable_employee").show();
$(".advanced_employee").show();

$("#table_div_emp").html("");	
$("#table_div_emp").html('<table id="employee_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let myParam = {
			startdate: $("#startdate_emp").val() == '' ? null : $("#startdate_emp").val(),
			enddate: $("#enddate_emp").val() == '' ? null : $("#enddate_emp").val(),
			id_url: global_url_server
		};
		
	let t_emp = $('#employee_table').DataTable({
		processing: true,
		columnDefs: false,
		select: {
		  style:    'multi+shift',
		  selector: 'td:nth-child(2)'
		},
		responsive: true,
		destroy: true,
		ajax: {
			url: "{{ route('papi.index_papi_emp') }}",
			data: myParam,
			"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#employee_table').DataTable().ajax.reload();
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
			{ data: 'name', title: 'Name', responsivePriority: 3},
			{ data: 'nik_employee', title: 'NIK', responsivePriority: 4},
			{ data: 'position', title: 'Position', responsivePriority: 5},
			{ data: 'branch', title: 'Branch', responsivePriority: 6},
			{ data: 'batch_name', title: 'Batch Name', responsivePriority: 7},
			{ data: 'location', title: 'Location'},
			{ data: 'action', title: 'Action', responsivePriority: 1, className: 'th-text-score', orderable: false, render: function ( data, type, row ) {	
					return '<div align="center">'+data+'</div>';
				} 
			},
		],
	});
	
	t_emp.on('order.dt search.dt', function () {
        let i = 1;
        t_emp.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}

$(document).on('click', '#search_created_can', function () {
    get_datatable_candidate();
});

const get_datatable_candidate = async () => {
	$(".div_datatable_can").show();
    $(".advanced_can").show();

	$("#table_div_can").html("");	
	$("#table_div_can").html('<table id="candidate_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let myParam = {
			startdate: $("#startdate_can").val() == '' ? null : $("#startdate_can").val(),
			enddate: $("#enddate_can").val() == '' ? null : $("#enddate_can").val(),
			id_url: global_url_server
		};
		
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
				url: "{{ route('papi.index_papi_can') }}",
				data: myParam,
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
				{ data: 'name', title: 'Name', responsivePriority: 3},
				{ data: 'identification_number', title: 'ID Number'},
				{ data: 'batch_name', title: 'Batch Name'},
				{ data: 'location', title: 'Location'},
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, className: 'th-text-score', render: function ( data, type, row ) {	
						return '<div align="center">'+data+'</div>';
					} 
				},
			],
		});
		
		t_can.on('order.dt search.dt', function () {
        let i = 1;
        t_can.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}
	
$(document).ready(function(){

$(".advanced_employee").hide();
$(".advanced_can").hide();
	
grouping_by = [
	{
		id: 'candidate',
		text: 'Candidate List'
	},	
	{
		id: 'employee',
		text: 'Employee List'
	},	
	
];

	daterange_can();
	daterange_emp();
	get_filter().then(function() {
		$('#search_grouping').trigger('click');
	});  	
});

const get_filter = async () => {
    try {
		$('#grouping').select2({
			data:grouping_by
		}).on('change', function (e) {			
			if($(this).select2('data')[0].id == 'employee'){
				$(".div_datatable_can").hide();
			}
			else if($(this).select2('data')[0].id == 'candidate'){
				$(".div_datatable_employee").hide();
			}
		}).trigger('change');
    } 
	catch (error) {
    }	
}

function daterange_can(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(60, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
    $('#daterange_can').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: true,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
            closeText: 'Clear',
        },
        startDate: start, 
        endDate: end,
    }, function(start, end, label) {
        $("#startdate_can").val(start.format('YYYY-MM-DD'));
        $("#enddate_can").val(end.format('YYYY-MM-DD'));
    });
    
	$("#startdate_can").val(start);
    $("#enddate_can").val(end);
}

function daterange_emp(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(60, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
    $('#daterange_emp').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: true,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
            closeText: 'Clear',
        },
        startDate: start, 
        endDate: end,
    }, function(start, end, label) {
        $("#startdate_emp").val(start.format('YYYY-MM-DD'));
        $("#enddate_emp").val(end.format('YYYY-MM-DD'));
    });
    
	$("#startdate_emp").val(start);
    $("#enddate_emp").val(end);
}

</script>
@endsection