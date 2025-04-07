@extends('adminlte::page')
@section('title', 'Psychogram')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Psychogram</h5>               
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
						<div class="col-sm-4 filter_position_employee" style="display:none;">
							<select id="filter_position_employee" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-4 filter_jobgrade_employee" style="display:none;">
							<select id="filter_jobgrade_employee" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-4 filter_department_employee_top" style="display:none;">
							<select id="filter_department_employee_top" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="search_created_emp" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
						</div>										
					</div>
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Download Preview by Grade & Department :</label>
						<div class="col-sm-2">
	                        <select id="jobgrade_employee" class="form-control form-control-sm select2 preview_grade" style="width: 100%;"></select>
						</div>
						<div class="col-sm-2">
							<select id="filter_department_employee" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="download_employee" class="btn btn-sm btn-success" ><i class="fas fa-arrow-down"></i> Download</button>
						</div>
						<div class="col-sm-2">
							<button class="btn btn-sm btn-success btn-batch-generate-psychogram ml-2"><i class="fas fa-refresh"></i> Generate</button>	
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
						<div class="col-sm-4 filter_position_candidate" style="display:none;">
							<select id="filter_position_candidate" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-4 filter_jobgrade_candidate" style="display:none;">
							<select id="filter_jobgrade_candidate" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-4 filter_department_candidate_top" style="display:none;">
							<select id="filter_department_candidate_top" class="form-control form-control-sm select2" style="width: 100%;" multiple></select>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="search_created_can" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
						</div>
						
					</div>
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Download Preview by Grade & Department :</label>
						<div class="col-sm-2">
	                        <select id="jobgrade_candidate" class="form-control form-control-sm select2 preview_grade" style="width: 100%;"></select>
						</div>
						<div class="col-sm-2">
							<select id="filter_department_candidate" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="download_candidate" class="btn btn-sm btn-success" ><i class="fas fa-arrow-down"></i> Download</button>
						</div>	
						<div class="col-sm-2">
							<button class="btn btn-sm btn-success btn-batch-generate-psychogram ml-2"><i class="fas fa-refresh"></i> Generate</button>	
						</div>									
					</div>
					<button onclick="return false;" class="btn btn-default pull-left advanced_can">Advanced Search</button>
					<br><br>
					<div id="table_div_can"></div>
				</div>

				
			</div>
        </div>
    </div>
</div>

<div id="modalPreview" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title-delete">Preview Psychogram</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            	<div class="row">
            		<input type="hidden" id="id_user_assessment">
            		<input type="hidden" id="id_batch">
            		<input type="hidden" id="user_type">
            		<div class="col-md-4">
            			<div class="form-group" >
		                    <label class="col-md-12 col-form-label">Job Grade :</label>
		                    <div class="col-md-12">
		                        <select id="grade" class="form-control form-control-sm select2 " style="width: 100%;">
		                        </select>
		                        <span class="invalid-feedback" role="alert" id="gradeError">
		                            <strong></strong>
		                        </span>
		                    </div>
		                </div>
            		</div>
            		<div class="col-md-5">
            			<div class="form-group" >
		                    <label class="col-md-12 col-form-label">Department :</label>
		                    <div class="col-md-12">
		                        <select id="department" class="form-control form-control-sm select2 " style="width: 100%;">
		                        </select>
		                        <span class="invalid-feedback" role="alert" id="departmentError">
		                            <strong></strong>
		                        </span>
		                    </div>
		                </div>
            		</div>
            		<div class="col-md-3">
            			<div class="row">
            				<div class="col-md-6">
			                    <label class="col-md-12 col-form-label">&nbsp;</label>
		            			<button onclick="return false;" id="show_psychogram" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Show</button>
            				</div>
            				<div class="col-md-6">
			                    <label class="col-md-12 col-form-label">&nbsp;</label>
		            			<button onclick="return false;" id="download_psychogram" class="btn btn-sm btn-success" ><i class="fas fa-arrow-down"></i> Download</button>
            				</div>
            			</div>
            		</div>
            	</div>
            	<div class="row">
            		<div class="col-md-12 result_psychogram"></div>
            	</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" data-dismiss="modal">Close</button>
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
var group_branch = '{{ $group_branch }}';

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

let optionSearchPsycho = [
    {id: 'created_date', text: 'Created Date'},
    {id: 'batch', text: 'Batch'},
	{id: 'position', text: 'Position'},
	{id: 'job_grade', text: 'Job Grade'},
	// {id: 'department', text: 'Department'},
];
let selectedGroupingType = '';

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
			id_position_routing: $(`#filter_position_employee`).val(),
			id_job_grade: $(`#filter_jobgrade_employee`).val(),
			id_department: $(`#filter_department_employee_top`).val(),
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
			url: "{{ route('psychogram.index_psychogram_emp') }}",
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
			{ data: 'current_grade_desc', title: 'Job Grade' },
			{ data: 'branch', title: 'Branch', responsivePriority: 6},
			{ data: 'batch_name', title: 'Batch Name', responsivePriority: 7},
			{ data: 'location', title: 'Location'},
			{ data: 'dept_description', title: 'Psychogram Dept'},
			{ data: 'job_grade_description', title: 'Psychogram Job Grade'},
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
			id_url: global_url_server,
			search_by: $(`#search_by_candidate option:selected`).val(),
			id_batch: $(`#filter_batch_candidate option:selected`).val(),
			id_position_routing: $(`#filter_position_candidate`).val(),
			id_job_grade: $(`#filter_jobgrade_candidate`).val(),
			id_department: $(`#filter_department_candidate_top`).val(),
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
				url: "{{ route('psychogram.index_psychogram_can') }}",
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
				{ data: 'dept_description', title: 'Psychogram Dept'},
				{ data: 'job_grade_description', title: 'Psychogram Job Grade'},
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
$('#search_by_candidate').select2({
	data: optionSearchPsycho,
	placeholder: 'Select Filter Type',
})
$('#search_by_employee').select2({
	data: optionSearchPsycho.concat([{ id: 'department', text: 'Department' }]),
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
	get_grade()
	get_department()
	get_batch()
	get_position()
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

const get_grade = async () => {
    try {
        let result;
        let allGrade = [];

        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/psychogram/get_grade') ?>',
            method: "GET",
            success: function (res) {
            	if(res.length > 0){
	            	$.each(res, function (i, val) {
			            if(val.job_class_group == null || val.job_class_group == ''){
			                grade_group = '';
			            } else {
			                grade_group = ` - (${val.job_class_group})`;
			            }
			            name_grade = `${val.description}${grade_group}`
			            allGrade.push({id:val.id_job_grade, text:name_grade});
			        }); 
            	}
		        $('#grade').select2({
		            data: allGrade,
		            placeholder: 'Select Grade'
		        });
				$('#filter_jobgrade_candidate, #filter_jobgrade_employee').select2({
		            data: allGrade,
		            placeholder: 'Select Grade'
		        });
		        $('.preview_grade').empty().prepend('<option></option>').select2({
		            data: allGrade,
		            placeholder: 'Select Grade'
		        });
            },
        });
        return result;
    } catch (error) {
        get_grade();
    }
}

const get_department = async () => {
    try {
        let result;
        let allDepartment = [];

        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/psychogram/get_department') ?>',
            method: "GET",
            success: function (res) {
            	if(res.length > 0){
	            	$.each(res, function (i, val) {
			            name = `${val.description}`;
			            allDepartment.push({id:val.id_dept, text:name});
			        }); 
            	}
		        $('#department, #filter_department_candidate, #filter_department_employee, #filter_department_candidate_top, #filter_department_employee_top').prepend('<option></option>').select2({
		            data: allDepartment,
		            placeholder: 'Select Department'
		        });
            },
        });
        return result;
    } catch (error) {
        get_department();
    }
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

const get_position = async () => {
    try {
        let result;
        let allPosition = [];

        result = await $.ajax({
            url: '<?= url('recruitment/psychotest/psychogram/get_position') ?>',
            method: "GET",
            success: function (res) {
            	if(res.length > 0){
	            	$.each(res, function (i, val) {
			            name = `${val.text}`;
			            allPosition.push({id:val.id, text:name});
			        }); 
            	}
		        $('#filter_position_employee, #filter_position_candidate').select2({
		            data: allPosition,
		            placeholder: 'Select Position',
					allowClear: true,
		        });
            },
        });
        return result;
    } catch (error) {
        get_position();
    }
}

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function psychogramViewOnly() {
	$(`.result_psychogram`).html('');
	let urlWebCareer = '{{ $urlWebCareer }}';
	let user_type = $(`#user_type`).val();
	let id_user_assessment = $(`#id_user_assessment`).val();
	let id_batch = $(`#id_batch`).val();
	let id_grade = $(`#grade option:selected`).val();
	let id_department = $(`#department option:selected`).val();

	let q_param = {
		'user_type' : user_type,
		'id_user_assessment' : id_user_assessment,
		'id_batch' : id_batch,
		'id_department' : id_department,
		'id_grade' : id_grade,
		'request_type' : 'table',
		'modify': false
	};
	let param = objectToQueryString(q_param);
	$.ajax({
		url: '<?= url('recruitment/psychotest/psychogram/get_psychogram') ?>',
		data: {'url':urlWebCareer+'psikogram/summary?'+param},
		method: "POST",
		headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
		async: true,
		success: function (res) {
			if(res.status == true){
				$(`.result_psychogram`).html(res.data);
			} else {
				$(`.result_psychogram`).html(res.message);
			}
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});
}

$(document).on('click', '.preview_psychogram', function () {
	$(`.result_psychogram`).html('');
	let id_user_assessment = $(this).attr('id_user_assessment');
	let id_batch = $(this).attr('id_batch');
	let user_type = $(this).attr('user_type');
	$(`#id_user_assessment`).val(id_user_assessment);
	$(`#id_batch`).val(id_batch);
	$(`#user_type`).val(user_type);
	$("#modalPreview").modal('show');
});

$(document).on('click', '#show_psychogram', function () {
	if(group_branch != '' && $('#grouping').val() == "employee") {
		return psychogramViewOnly();
	}
	let modify = false;
	swal({
		title: "Apakah anda ingin menggunakan hasil psikogram ini?",
		text: "Pilih \"No\" jika hanya melihat",
		icon: "warning",
		buttons: [
        'No',
        'Yes'
      ],
	})
	.then(function (isConfirm) {
		if(isConfirm) modify = true;
		else modify = false;
	})
	.finally(function() {
		$(`.result_psychogram`).html('');
		let urlWebCareer = '{{ $urlWebCareer }}';
		let user_type = $(`#user_type`).val();
		let id_user_assessment = $(`#id_user_assessment`).val();
		let id_batch = $(`#id_batch`).val();
		let id_grade = $(`#grade option:selected`).val();
		let id_department = $(`#department option:selected`).val();

		let q_param = {
			'user_type' : user_type,
			'id_user_assessment' : id_user_assessment,
			'id_batch' : id_batch,
			'id_department' : id_department,
			'id_grade' : id_grade,
			'request_type' : 'table',
			'modify': modify
		};
		let param = objectToQueryString(q_param);
		$.ajax({
			url: '<?= url('recruitment/psychotest/psychogram/get_psychogram') ?>',
			data: {'url':urlWebCareer+'psikogram/summary?'+param},
			method: "POST",
			headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			async: true,
			success: function (res) {
				if(res.status == true){
					$(`.result_psychogram`).html(res.data);
				} else {
					$(`.result_psychogram`).html(res.message);
				}
			},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
		});
	});


	
});

function download(url) {
	if(group_branch != '' && $('#grouping').val() == "employee") {
		return window.open(url+="&modify="+false, "_new", "", "");
	}
	let modify = false;
	swal({
		title: "Apakah anda ingin menggunakan hasil psikogram ini?",
		text: "Pilih \"No\" jika hanya melihat",
		icon: "warning",
		buttons: [
        'No',
        'Yes'
      ],
	})
	.then(function (isConfirm) {
		if(isConfirm) modify = true;
		else modify = false;
	})
	.finally(function() {
		window.open(url+="&modify="+modify, "_new", "", "");
	})
}

$(document).on('click', '#download_psychogram', function () {
	let modify = false;
	swal({
		title: "Apakah anda ingin menggunakan hasil psikogram ini?",
		text: "Pilih \"No\" jika hanya melihat",
		icon: "warning",
		buttons: [
        'No',
        'Yes'
      ],
	})
	.then(function (isConfirm) {
		if(isConfirm) modify = true;
		else modify = false;
	})
	.finally(function () {
		let urlWebCareer = '{{ $urlWebCareer }}';
		let user_type = $(`#user_type`).val();
		let id_user_assessment = $(`#id_user_assessment`).val();
		let id_batch = $(`#id_batch`).val();
		let id_grade = $(`#grade option:selected`).val();
		let id_department = $(`#department option:selected`).val();

		let q_param = {
			'user_type' : user_type,
			'id_user_assessment' : id_user_assessment,
			'id_batch' : id_batch,
			'id_department' : id_department,
			'id_grade' : id_grade,
			'request_type' : 'pdf',
			'modify' : modify
		};
		let param = objectToQueryString(q_param);
		window.open(urlWebCareer+'psikogram/summary?'+param, '_blank');
	})
	
});


$(document).on('click', '#download_candidate, #download_employee', function () {
	
	let buttonId = $(this).attr('id');
	let thisUrl = '<?= url('recruitment/psychotest/psychogram/download') ?>';

	if(buttonId == 'download_candidate'){
		if($('#jobgrade_candidate').val() == '') {
			swal({
				icon: 'error',
				title: 'Error',
				text: 'Please select a job grade'
			})
			return;
		}
		type = 'candidate';
		start = $("#startdate_can").val();
		end = $("#enddate_can").val();
		jobgrade = $("#jobgrade_candidate option:selected").val();
		department = $("#filter_department_candidate option:selected").val();
		id_batch = $(`#filter_batch_candidate option:selected`).val();
		id_position = $(`#filter_position_candidate option:selected`).val();
		search_by = $(`#search_by_candidate option:selected`).val();
		id_job_grade = $(`#filter_jobgrade_candidate`).val();
	} else {
		if($('#jobgrade_employee').val() == '') {
			swal({
				icon: 'error',
				title: 'Error',
				text: 'Please select a job grade'
			})
			return;
		}
		type = 'employee';
		start = $("#startdate_emp").val();
		end = $("#enddate_emp").val();
		jobgrade = $("#jobgrade_employee option:selected").val();
		department = $("#filter_department_employee option:selected").val();
		id_batch = $(`#filter_batch_employee option:selected`).val();
		search_by = $(`#search_by_employee option:selected`).val();
		id_position = $(`#filter_position_employee`).val();
		id_job_grade = $(`#filter_jobgrade_employee`).val();
	}
	let myParam = {
		startdate: start,
		enddate: end,
		id_url: global_url_server,
		jobgrade: jobgrade,
		department: department,
		id_batch: id_batch,
		search_by: search_by,
		type: type,
		id_job_grade: id_job_grade,
		employee_id_department: $('#filter_department_employee_top').val(),
	};
	let param = objectToQueryString(myParam);
	window.open(thisUrl+'?'+param, '_blank');
});

$(document).on('change', `#search_by_candidate, #search_by_employee`, function () {
    let thisValue = $(this).val();
    if(thisValue == 'created_date'){
    	$(`.filter_batch_${selectedGroupingType}`).hide();
    	$(`.filter_date_${selectedGroupingType}`).show();
		$(`.filter_position_${selectedGroupingType}`).hide();
		$(`.filter_jobgrade_${selectedGroupingType}`).hide();
		$(`.filter_department_${selectedGroupingType}_top`).hide();
    } else if(thisValue == 'batch') {
    	$(`.filter_batch_${selectedGroupingType}`).show();
    	$(`.filter_date_${selectedGroupingType}`).hide();
		$(`.filter_position_${selectedGroupingType}`).hide();
		$(`.filter_jobgrade_${selectedGroupingType}`).hide();
		$(`.filter_department_${selectedGroupingType}_top`).hide();
    } else if(thisValue == 'position') {
		$(`.filter_batch_${selectedGroupingType}`).hide();
    	$(`.filter_date_${selectedGroupingType}`).hide();
		$(`.filter_position_${selectedGroupingType}`).show();
		$(`.filter_jobgrade_${selectedGroupingType}`).hide();
		$(`.filter_department_${selectedGroupingType}_top`).hide();
	} else if(thisValue == 'job_grade') {
		$(`.filter_batch_${selectedGroupingType}`).hide();
    	$(`.filter_date_${selectedGroupingType}`).hide();
		$(`.filter_position_${selectedGroupingType}`).hide();
		$(`.filter_jobgrade_${selectedGroupingType}`).show();
		$(`.filter_department_${selectedGroupingType}_top`).hide();
	} else if(thisValue == 'department') {
		$(`.filter_batch_${selectedGroupingType}`).hide();
    	$(`.filter_date_${selectedGroupingType}`).hide();
		$(`.filter_position_${selectedGroupingType}`).hide();
		$(`.filter_jobgrade_${selectedGroupingType}`).hide();
		$(`.filter_department_${selectedGroupingType}_top`).show();
	}
});

$(document).on('click', '.btn-batch-generate-psychogram', function() {
	if($('#jobgrade_'+$('#grouping').val()).val() == '') {
		swal({
			icon: 'error',
			title: 'Error',
			text: 'Please select a job grade'
		})
		return;
	}
	if($('#filter_department_'+$('#grouping').val()).val() == '') {
		swal({
			icon: 'error',
			title: 'Error',
			text: 'Please select a department'
		})
		return;
	}
	var oTable = $(`#${$('#grouping').val()}_table`).dataTable();
	var rowcollection =  oTable.$(".dt-checkboxes:checked", {"page": "all"});
	let data = [];
	rowcollection.each(function(index,elem){
		var checkbox_value = $(elem).val();
		let previewBtn = $(elem).parent().parent().parent().find('.preview_psychogram');
		data.push({
			id_user_assessment: previewBtn.attr('id_user_assessment'),
			id_batch: previewBtn.attr('id_batch'),
			type: previewBtn.attr('user_type'),
		});
	});
	if(data.length < 1) {
		swal({
			icon: 'error',
			text: `Please select at least 1 ${$('#grouping').val()}!`
		});
	}
	let logSuccess = [];
	let logError = [];
	data.forEach((element) => {
		let urlWebCareer = '{{ $urlWebCareer }}';
		let user_type = element.type;
		let id_user_assessment = element.id_user_assessment;
		let id_batch = element.id_batch;
		let id_grade = $(`#jobgrade_${user_type}`).val();
		let id_department = $(`#filter_department_${user_type}`).val();

		let q_param = {
			'user_type' : user_type,
			'id_user_assessment' : id_user_assessment,
			'id_batch' : id_batch,
			'id_department' : id_department,
			'id_grade' : id_grade,
			'request_type' : 'table',
			'modify': true
		};
		let param = objectToQueryString(q_param);
		$.ajax({
			url: '<?= url('recruitment/psychotest/psychogram/get_psychogram') ?>',
			data: {'url':urlWebCareer+'psikogram/summary?'+param},
			method: "POST",
			headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			// async: true,
			success: function (res) {
				if(res.status == true){
					logSuccess.push(1);
				} else {
					// $(`.result_psychogram`).html(res.message);
					logError.push(1);
				}
			},
			error: () => {
				logError.push(1);
			},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			complete: function(){
				$('#loader').addClass('hidden');
				swal({
					icon: logSuccess.length > 0 ? logError.length > 0 ? 'warning' : 'success' : logError.length > 0 ? 'error' : 'warning',
					text: `${logSuccess.length} of ${data.length} psychogram generated${logError.length > 0 ? " and "+logError.length+" psychogram failed to generate" : ""}.`
				}).then(() => {
					$(`#${$('#grouping').val()}_table`).DataTable().ajax.reload();
				})
			},
		});
	});
});

</script>
@endsection