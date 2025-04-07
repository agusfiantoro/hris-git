@extends('adminlte::page')
@section('title', 'Kraepelin Summary')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Kraepelin Summary</h5>               
            </div>      
			<div class="card-body">
				<div class="form-group row">              
					<label class="col-sm-3 col-form-label">Grouping By :</label>
					<div class="col-sm-4">
						<select id="grouping" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_grouping" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
					</div>										
				</div>
				<hr>
				<div class="div_datatable_employee" style="display:none;"> 
					<div class="form-group row">              
						<label class="col-sm-1 col-form-label">Search by:</label>
						<div class="col-sm-2">
							<select id="search_by_employee" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
						<div class="col-md-4 filter_date_employee">
							<div class="input-group">
								<input name="daterange_emp" id="daterange_emp" type="daterange" class="form-control form-control-sm" />
								<input name="startdate_emp" id="startdate_emp" class="form-control form-control-sm" hidden>
								<input name="enddate_emp" id="enddate_emp" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-4 filter_batch_employee" style="display:none;">
							<select id="filter_batch_employee" class="form-control form-control-sm select2" style="width: 100%;"></select>
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
						<label class="col-sm-1 col-form-label">Search by:</label>
						<div class="col-sm-2">
							<select id="search_by_candidate" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
						<div class="col-md-4 filter_date_candidate">
							<div class="input-group">
								<input name="daterange_can" id="daterange_can" type="daterange" class="form-control form-control-sm" />
								<input name="startdate_can" id="startdate_can" class="form-control form-control-sm" hidden>
								<input name="enddate_can" id="enddate_can" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>
						<div class="col-sm-4 filter_batch_candidate" style="display:none;">
							<select id="filter_batch_candidate" class="form-control form-control-sm select2" style="width: 100%;"></select>
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

<div class="modal fade" id="modal_reset"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="reset_tes" >
                <div class="modal-header bg-danger">
                    <h5 class="modal-title className">Reset</h5>
                </div>
                <div class="modal-body">
                    <div class="">
                    	<input type="hidden" id="input_id_batch">
                    	<input type="hidden" id="input_id_user">
                    	<input type="hidden" id="input_user_type">
                    	<h4 class="total_answered"></h4>
                    	<h4 class="">Do you want to reset answer in this batch ?</h4>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>&nbsp;
                    <button type="submit" class="btn btn-danger" id="reset_answer" onclick="return false;"><i class="fas fa-trash-alt"></i> Reset</button>
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
			id_url: global_url_server,
			search_by: $(`#search_by_employee option:selected`).val(),
			id_batch: $(`#filter_batch_employee option:selected`).val(),
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
			url: "{{ route('kraepelin.index_kraepelin_emp') }}",
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
			{ data: 'action', title: 'Action', responsivePriority: 1, className: '', orderable: false, render: function ( data, type, row ) {	
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

let optionSearchPsycho = [
    {id: 'created_date', text: 'Created Date'},
    {id: 'batch', text: 'Batch'},
];
let selectedGroupingType = '';

const get_datatable_candidate = async () => {
	$(".div_datatable_can").show();
    $(".advanced_can").show();

	$("#table_div_can").html("");	
	$("#table_div_can").html('<table id="candidate_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let myParam = {
			startdate: $("#startdate_can").val() == '' ? null : $("#startdate_can").val(),
			enddate: $("#enddate_can").val() == '' ? null : $("#enddate_can").val(),
			id_url: global_url_server,
			search_by: $(`#search_by_candidate option:selected`).val(),
			id_batch: $(`#filter_batch_candidate option:selected`).val(),
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
				url: "{{ route('kraepelin.index_kraepelin_can') }}",
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
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, className: '', render: function ( data, type, row ) {	
						return data;
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
$('#search_by_candidate, #search_by_employee').select2({
	data:optionSearchPsycho,
	placeholder: 'Select Filter Type',
})
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
		selectedGroupingType = $(`#grouping option:selected`).val();
	});  	
	get_batch()
});

const get_filter = async () => {
    try {
		$('#grouping').select2({
			data:grouping_by
		}).on('change', function (e) {			
			selectedGroupingType = $(this).val();
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

const get_batch = async () => {
    try {
        let result;
        let allBatch = [];

        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/psychogram/get_batch') ?>',
            method: "GET",
            success: function (res) {
            	if(res.length > 0){
	            	$.each(res, function (i, val) {
			            name = `${val.batch_name} (${val.location})`;
			            allBatch.push({id:val.id_batch, text:name});
			        }); 
            	}
		        $('#filter_batch_employee, #filter_batch_candidate').select2({
		            data: allBatch,
		            placeholder: 'Select Batch'
		        });
            },
        });
        return result;
    } catch (error) {
        get_batch();
    }
}

const get_kraepelin_result = async (id_batch, user_type, id_user) => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/kreapelin_summary/get_kraepelin_result') ?>',
            method: "GET",
            data:{'id_batch':id_batch, 'user_type':user_type, 'id_user_assessment':id_user},
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
        get_kraepelin_result(id_batch, user_type, id_user);
    }
}

const reset_kraepelin_result = async (id_batch, user_type, id_user) => {
    try {
        let result;
        result = await $.ajax({
        	type: 'POST',
            headers: { Accept: "application/json", 'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            url: "{{ url('recruitment/psychotest/kreapelin_summary/reset_kraepelin_result') }}",
            data:{'id_batch':id_batch, 'user_type':user_type, 'id_user_assessment':id_user},
            success: function (response) {
            },
            error: function (response) {
            }
        });
        return result;
    } catch (error) {
        reset_kraepelin_result(id_batch, user_type, id_user);
    }
}

$(document).on('change', `#search_by_candidate, #search_by_employee`, function () {
    let thisValue = $(this).val();
    if(thisValue == 'created_date'){
    	$(`.filter_batch_${selectedGroupingType}`).hide();
    	$(`.filter_date_${selectedGroupingType}`).show();
    } else {
    	$(`.filter_batch_${selectedGroupingType}`).show();
    	$(`.filter_date_${selectedGroupingType}`).hide();
    }
});

$(document).on('click', `.reset`, function () {
    let idUserAssessment = $(this).attr('id_user');
    let idBatch = $(this).attr('id_batch');
    let userType = $(this).attr('user_type');
    
    get_kraepelin_result(idBatch, userType, idUserAssessment).then(function(res) {
	    $(`#modal_reset`).modal('show');
    	if(res && res.length > 0){
        	$(`#input_id_batch`).val(idBatch);
		    $(`#input_id_user`).val(idUserAssessment);
		    $(`#input_user_type`).val(userType);
		    $(`.total_answered`).html(`The user has answered ${res[0].column_number} columns`);
    	} else {
		    $(`.total_answered`).html(`The user has answered 0 columns`);
		    $(`#input_id_batch`).val('');
		    $(`#input_id_user`).val('');
		    $(`#input_user_type`).val('');
    	}
	});
});

$(document).on('click', `#reset_answer`, function () {
    let idUserAssessment = $(`#input_id_user`).val();
    let idBatch = $(`#input_id_batch`).val();
    let userType = $(`#input_user_type`).val();
    
    reset_kraepelin_result(idBatch, userType, idUserAssessment).then(function(res) {
    	$('#modal_reset').modal('hide');
    	if(res.status == true){
            swal({
                icon: 'success',
                title: 'Success',
                text: res.message
            }).then(ok => {
                
            });
    	} else {
    		swal({
                icon: 'error',
                title: 'Failed',
                text: res.message
            }).then(ok => {
                
            });
    	}
	});
});



</script>
@endsection