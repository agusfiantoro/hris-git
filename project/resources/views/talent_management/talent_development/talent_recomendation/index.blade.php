@extends('adminlte::page')
@section('title', 'Talent Recommendation')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Talent Recommendation</h5>
				<div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Talent Recommendation</button>
                </div>
            </div>      
			<div class="card-body">
				<div class="form-group row">
					<div class="col-md-6">
						<div class="row">
							<label class="col-md-3 col-form-label">Status</label>
							<div class="col-md-4">
								<select id="emp_status" class="form-control form-control-sm select2" style="width: 100%;">
									<option value="A" selected>Active</option>
									<option value="I">Inactive</option>
								</select>
							</div>
							<div class="col-md-5">
								<button onclick="return false;" id="search_fil" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
							</div>
						</div>
					</div>
				</div>
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="talent_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
					<th></th>
					<th data-priority="8"></th>
					<th data-priority="2">No</th>
					<th data-priority="4">Reference Number</th>
					<th data-priority="3">Recommendation Name</th>
					<th data-priority="6">Request By</th>
					<th data-priority="7">Talent Type</th>
					<th data-priority="5">Projected Position</th>
					<th data-priority="10">Hiring Request</th>
					<th data-priority="9">BEES Period</th>
					<th>Status</th>
					<th data-priority="1" style="text-align:center;" width=100>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-xl">
  <div id="modal_second"></div>
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="talentForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  
		  </div>

		  <div class="modal-footer">
			<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
			<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>  
		<div style="display:none;">
			<table id="sample_table_emp">
				<tr id="">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
							<input name="emp[0][id_talent_recommendation_detail]" id="emp_0_id_talent_recommendation_detail" type="hidden" class="form-control form-control-sm id_talent_recommendation_detail_input">							
							<input name="emp[0][id_employee]" id="emp_0_id_employee" type="hidden" class="form-control form-control-sm id_employee_input">
							<div align="center" id="emp_0_nik_employee" class="nik_employee_input"></div>
					</td>
					<td>
							<div align="center" id="emp_0_name_employee" class="name_employee_input"></div>
					</td>
					<td>
							<input name="emp[0][id_position_detail]" id="emp_0_id_position_detail" type="hidden" class="form-control form-control-sm id_position_detail_input">
							<input name="emp[0][id_dept]" id="emp_0_id_dept" type="hidden" class="form-control form-control-sm id_dept_input">
							<input name="emp[0][id_region]" id="emp_0_id_region" type="hidden" class="form-control form-control-sm id_region_input">
							<div align="center" id="emp_0_position_route" class="position_route_input"></div>
					</td>
					<!-- td>
							<div align="center" id="emp_0_department" class="department_input"></div>
					</td -->
					<td>
							<input name="emp[0][id_job_grade]" id="emp_0_id_job_grade" type="hidden" class="form-control form-control-sm id_job_grade_input">
							<div align="center" id="emp_0_job_grade" class="job_grade_input"></div>
					</td>
					<!-- td>
							<input name="emp[0][id_region]" id="emp_0_id_region" type="hidden" class="form-control form-control-sm id_region_input">
							<div align="center" id="emp_0_region" class="region_input"></div>
					</td -->
					<td>
							<input name="emp[0][id_branch]" id="emp_0_id_branch" type="hidden" class="form-control form-control-sm id_branch_input">
							<div align="center" id="emp_0_branch" class="branch_input"></div>
					</td>
					<td align="center">
							<input name="emp[0][id_rating]" id="emp_0_id_rating" type="hidden" class="form-control form-control-sm id_rating_input">
							<span align="center" id="emp_0_final_rating" class="badge badge-secondary final_rating_input" style="padding:5px;font-size:12px;"></span>
					</td>
					<td align="center">
							<input name="emp[0][kpi_desc]" id="emp_0_kpi_desc" type="hidden" class="form-control form-control-sm kpi_desc_input">
							<span align="center" id="emp_0_name_kpi_desc" class="name_kpi_desc_input"></span>
					</td>
					<td align="center">
							<input name="emp[0][id_month1]" id="emp_0_id_month1" type="hidden" class="form-control form-control-sm id_month1_input">
							<input name="emp[0][id_month2]" id="emp_0_id_month2" type="hidden" class="form-control form-control-sm id_month2_input">
							<input name="emp[0][id_month3]" id="emp_0_id_month3" type="hidden" class="form-control form-control-sm id_month3_input">
							<input name="emp[0][id_month4]" id="emp_0_id_month4" type="hidden" class="form-control form-control-sm id_month4_input">
							<input name="emp[0][id_month5]" id="emp_0_id_month5" type="hidden" class="form-control form-control-sm id_month5_input">
							<input name="emp[0][id_month6]" id="emp_0_id_month6" type="hidden" class="form-control form-control-sm id_month6_input">
							<input name="emp[0][id_month7]" id="emp_0_id_month7" type="hidden" class="form-control form-control-sm id_month7_input">
							<input name="emp[0][id_month8]" id="emp_0_id_month8" type="hidden" class="form-control form-control-sm id_month8_input">
							<input name="emp[0][id_month9]" id="emp_0_id_month9" type="hidden" class="form-control form-control-sm id_month9_input">
							<input name="emp[0][id_month10]" id="emp_0_id_month10" type="hidden" class="form-control form-control-sm id_month10_input">
							<input name="emp[0][id_month11]" id="emp_0_id_month11" type="hidden" class="form-control form-control-sm id_month11_input">
							<input name="emp[0][id_month12]" id="emp_0_id_month12" type="hidden" class="form-control form-control-sm id_month12_input">
							<input name="emp[0][id_kpi_average]" id="emp_0_id_kpi_average" type="hidden" class="form-control form-control-sm id_kpi_average_input">
							<b align="center" id="emp_0_kpi_average" class="kpi_average_input" style="font-size:14px;"></b>
					</td>
					<td style="width:180px;">
							<select name="emp[0][competencies]" id="emp_0_competencies" class="form-control form-control-sm select2 competencies_input" style="width: 180px;"></select>
					</td>
					<td align="center">
							<input name="emp[0][id_potencies]" id="emp_0_id_potencies" type="hidden" class="form-control form-control-sm id_potencies_input" >
							<b align="center" id="emp_0_potencies" class="badge potencies_input" style="padding:5px;font-size:12px;"></b>
					</td>
					<td align="center" style="min-width:150px;">
						<span align="center" id="emp_0_psych_dept" class="psych_dept_input" style="padding:2px;font-size:12px;"></span>
						<input type="hidden" class="psych_id_dept">
						<input type="hidden" class="psych_id_job_grade">
					</td>
					<td align="center" style="min-width:100px;">
						<span align="center" id="emp_0_psych_dateassign" class="psych_dateassign_input" style="padding:5px;font-size:12px;"></span>
					</td>
					<td align="center">
							<input name="emp[0][id_sp]" id="emp_0_id_sp" type="hidden" class="form-control form-control-sm id_sp_input">
							<span align="center" id="emp_0_sp" class="badge sp_input" style="padding:5px;font-size:12px;"></span>
					</td>
					<td align="center">
							<input name="emp[0][id_kpk]" id="emp_0_id_kpk" type="hidden" class="form-control form-control-sm id_kpk_input">
							<span align="center" id="emp_0_kpk" class="badge kpk_input" style="padding:5px;font-size:12px;"></span>
					</td>
					<td align="center">
							<input name="emp[0][id_survey_answer_user_header]" id="emp_0_id_survey_answer_user_header" type="hidden" class="form-control form-control-sm id_survey_answer_user_header_input">
							<input name="emp[0][id_eng_level]" id="emp_0_id_eng_level" type="hidden" class="form-control form-control-sm id_eng_level_input">
							<span align="center" id="emp_0_eng_level" class="badge badge-secondary eng_level_input" style="padding:5px;font-size:12px;"></span>
					</td>
					<td align="center">
							<input name="emp[0][id_batch]" id="emp_0_id_batch" type="hidden" class="form-control form-control-sm id_batch_input">
							<div align="center" id="emp_0_batch_name" class="batch_name_input"></div>
					</td>					
					<td class="" style="gap: 5px;">
						<center>
							<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0" style="margin-bottom:4px;"><span class="far fa-trash-alt"></span></button>
							<button type="button" id="emp_0_info" class="info-record btn btn-xs btn-warning" style="margin-bottom:4px;"><span class="fa fa-info-circle" style="color:white;"></span></button>
						</center>
						<center>
							<button type="button" class="btn btn-xs btn-primary btn-generate-psychogram" id="emp_0_generate_psycho" data-id="0"><span class="fas fa-list"></span></button>
						</center>
					</td>
				</tr>
			</table>
		</div>
    </div>
  </div>
</div>

<div id="generatePsychogramModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1060;">
</div>

<div class="modal fade" id="modal_browse" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1055;">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Talent List</h5>
                    <button type="button" onclick="second_close()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="contentList">						   
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
					<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
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

<div class="modal fade" id="modal_import_reco"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="importRecoForm">
                {{ csrf_field() }}
                <div class="modal-header">
					<h5 class="modal-title">Import Talent Recommendation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col">
							<label>Attachment</label>
						</div>
						<div class="col">
							<input type="hidden" name="id_talent_recommendation_header" id="importRecoId">
							<input type="file" name="attachment" id="importRecoAttachment">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_talent_info"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Talent KPI</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table id="kpi_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				<thead>
				  <tr>		
					<th></th>
					<th data-priority="1">NIK</th>
					<th data-priority="2">Name</th>
					<th data-priority="3">Period</th>
					<th data-priority="4">Average KPI</th>
					<th>KPI 1 Month Ago</th>
					<th>KPI 2 Month Ago</th>
					<th>KPI 3 Month Ago</th>
					<th>KPI 4 Month Ago</th>
					<th>KPI 5 Month Ago</th>
					<th>KPI 6 Month Ago</th>
					<th>KPI 7 Month Ago</th>
					<th>KPI 8 Month Ago</th>
					<th>KPI 9 Month Ago</th>
					<th>KPI 10 Month Ago</th>
					<th>KPI 11 Month Ago</th>
					<th>KPI 12 Month Ago</th>
				  </tr>
				 </thead>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
			</div>
        </div>
    </div>
</div>

<div id="confirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
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
	tr.ok_red td{
		background-color:#ff9191;
	}
	.modal{
		overflow:auto !important;
	}
	.custom-select:valid + .select2 .select2-selection{
	  border-color: #dc3545!important;
	}
	*:focus{
	  outline:0px;
	}
	.bei_left{
		float:left;
	}
	.bei_right{
		float:right;
	}
	
	.fixTableHead { 
      overflow: auto; 
      min-height:10px;
	  max-height:500px;
	  background:white;
	  z-index:9999;
    } 
    .fixTableHead thead th { 
      position: sticky; 
      top: 0; 
	  background:white;
	  z-index:9999;
    } 
   
</style>
@stop
@section('scripts')
<script type="text/javascript">
let id_talent = 0;
let global_projected = null;
let global_type = null;
let global_period_date = "";
let global_source = [];
let global_fpk = null;
let global_source_grade = null;
let global_source_region = null;
let global_source_branch = [];
let global_survey= null;
let global_id_emp_detail = 0;
let global_i = 0;
let global_z = [];
let global_competencies = [];
let global_emp_batch = [];
let global_emp_bei = [];
let load = '';

$(document).on('click', '#search_fil', function () {	
	param_fil_status = $("#emp_status").val();
	get_all(param_fil_status);
});

function loadnew(){
	load = 'new';
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Form Talent Recommendation");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
    $.ajax({
			url: "{{ route('talent_reco.modal_detail') }}",
			data:{global_talent:0},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

function loadedit(id_talent){
	
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Talent Recommendation");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");
    $.ajax({
			url: "{{ route('talent_reco.modal_detail') }}",
			data:{global_talent:id_talent},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

function on_close_modal() {
	$('#talent_table').DataTable().ajax.reload(); 
}


const get_all = async (param_fil_status) => {
	 let myData = {
		status: param_fil_status,
    };
		
	var t_able = $('#talent_table').DataTable({
            processing: true,
            responsive: true,
			destroy: true,
            ajax: {
    		    url: "{{ route('talent_reco.index') }}",
				data : myData,
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
                    data: 'id_talent_recommendation_header',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'reference_number', name: 'reference_number' },
				{ data: 'notes', name: 'notes' },
    			{ data: 'emp_name', name: 'emp_name' },
    			{ data: 'type', name: 'type', className: 'text-center', render: function ( data, type, row ) {
						if( data == 'P'){
							return 'Talent';	
						}
						else{
							return 'Successor';
						}
					} 
				},
    			{ data: 'pro_pos', name: 'pro_pos' },
    			{ data: 'fpk_req', name: 'fpk_req' },
    			{ data: 'survey', name: 'survey' },
				{ data: 'status', name: 'status' },
    			{ data: 'action', name: 'action', orderable: false, className: 'space' },
            ],
			"fnInitComplete": function (oSettings) {
				$('#talent_table_wrapper .column-filter-widget:eq(10)').find("select option:contains('A')").attr('selected','selected').change();
			//   $('.oke').find('.dt-checkboxes').click();
			}
        
        });
}


$(document).ready(function(){
	
	get_all($("#emp_status").val());
	
	rec_type = [
			{
				id: 'P',
				text: 'TALENT'
			},
			{
				id: 'S',
				text: 'SUCCESSOR'
			},
		];
	
	$('#emp_status').select2();	
	

		$('#kpi_table').DataTable({
            processing: true,
            responsive: true,
			destroy: true,
			columnDefs: false,
            columns: [
				{   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'name', name: 'name'},
				{ data: 'kpi_description', name: 'kpi_description'},
				{ data: 'kpi_average', name: 'kpi_average'},
				{ data: '_1_month_ago', name: '_1_month_ago'},
				{ data: '_2_month_ago', name: '_2_month_ago'},
				{ data: '_3_month_ago', name: '_3_month_ago'},
				{ data: '_4_month_ago', name: '_4_month_ago'},
				{ data: '_5_month_ago', name: '_5_month_ago'},
				{ data: '_6_month_ago', name: '_6_month_ago'},
				{ data: '_7_month_ago', name: '_7_month_ago'},
				{ data: '_8_month_ago', name: '_8_month_ago'},
				{ data: '_9_month_ago', name: '_9_month_ago'},
				{ data: '_10_month_ago', name: '_10_month_ago'},
				{ data: '_11_month_ago', name: '_11_month_ago'},
				{ data: '_12_month_ago', name: '_12_month_ago'},
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

const get_employee_by = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_employee_by') ?>',
            dataType: 'json',
            success: function (res) {
				$('#id_employee_request').select2({
					data: res,
				});
            },
        });
        return result;
    } catch (error) {
        get_employee_by();
    }	
}

const get_projected = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_projected') ?>',
            dataType: 'json',
            success: function (res) {
				$('#projected_pos').prepend('<option selected></option>').select2({
					placeholder: "Select Projected Position ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_projected();
    }	
}

$(document).on('change', '#projected_pos', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		global_projected = $(this).select2('val');		
		get_trigger_pos($(this).select2('val')).then(function(res) {
			if(res.length > 0){
				let y = [];
				$.each(res, function (i, item) {
					y.push(item.id_routing);
				});
				$('#source_pos').val(y).trigger('change');
			}
		});
	}
});

const get_trigger_pos = async (id_routing) => {
	let result;
	let myData = {
			id_routing: id_routing,
		};
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_trigger_pos') ?>',
            dataType: 'json',
			data:myData,
            success: function (res) {				
            },
        });
        return result;
    } catch (error) {
    //    get_source_pos();
    }	
}

$(document).on('change', '#rec_type', function (event, istrigger) {  
//	console.log(istrigger);
	$('#period_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});		
	$('#period_date').val("");	
    if(!istrigger){
		if(load == 'new'){
			if($(this).select2('val') == 'P'){
				$('#period_date').attr('disabled',false);
				$('#period_date').parent().children('span').children('button').attr('disabled', false);
			}
			else{
				$('#period_date').attr('disabled',true);
				$('#period_date').parent().children('span').children('button').attr('disabled', true);
			}
		}
		global_type = $(this).select2('val');
	}
});

const get_source_pos = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_source_pos') ?>',
            dataType: 'json',
            success: function (res) {
				$('#source_pos').select2({
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_source_pos();
    }	
}

$(document).on('change', '#source_pos', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		global_source = $(this).select2('val');
	}
});

const get_grade = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_grade') ?>',
            dataType: 'json',
            success: function (res) {
				$('#source_grade').select2({
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
    //    get_grade();
    }	
}

$(document).on('change', '#source_grade', function (event, istrigger) {  
//	console.log("#source_grade istrigger val: ", istrigger);
    if(!istrigger){
		global_source_grade = $(this).select2('val');
	}
});


const get_region = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_region') ?>',
            dataType: 'json',
            success: function (res) {
				$('#source_region').prepend('<option selected></option>').select2({
					data: res,
					placeholder: "Select Source Region ...",
					allowClear: true,
				});
            }
        });
        return result;
    } catch (error) {
    //    get_region();
    }
}

$(document).on('change', '#source_region', function (event, istrigger) {  
//	console.log("#source_region istrigger val: ", istrigger);
	$('#source_branch').empty();
    if(!istrigger){
		global_source_region = $(this).select2('val');
		get_branch($(this).select2('val'));
	}
});

const get_branch = async (id_region) => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_branch') ?>',
			data: {id_region: id_region},
            dataType: 'json',
            success: function (res) {
			$('#source_branch').select2({
				data: res,
				allowClear: true,
			});
            }
        });
        return result;
    } catch (error) {
     //   get_branch();
    }
}


$(document).on('change', '#source_branch', function (event, istrigger) {  
//	console.log("#source_branch istrigger val: ", istrigger);
    if(!istrigger){
		global_source_branch = $(this).select2('val');
	//	console.log(global_source.length);
	}
});

const get_fpk = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_fpk') ?>',
            dataType: 'json',
            success: function (res) {
				$('#fpk').prepend('<option selected></option>').select2({
					placeholder: "Select FPK ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_fpk();
    }	
}

$(document).on('change', '#fpk', function (event, istrigger) {  
//	console.log("#fpk istrigger val: ", istrigger, !istrigger);
    if(!istrigger){
		global_fpk = $(this).select2('val');
	}
});

const get_survey = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_survey') ?>',
            dataType: 'json',
            success: function (res) {
				$('#survey').prepend('<option selected></option>').select2({
					placeholder: "Select BEES Period ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_survey();
    }	
}

const get_conclusion = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/master_talent_setting/master_talent_rating/get_conclusion') ?>',
            dataType: 'json',
            success: function (res) {
				global_competencies = res;
            },
        });
        return result;
    } catch (error) {
    //    get_conclusion();
    }	
}

$(document).on('change', '#survey', function (event, istrigger) {  
	// console.log("#survey istrigger val: ", istrigger, !istrigger);
    if(!istrigger){
		global_survey = $(this).select2('val');
	}
});

const get_reco_list = async (id_employee) => {
	let result;
	let myData = {
			period_date: period_date,
			source_pos_list: source_pos_list,
			source_grade_list: source_grade_list,
			source_region_list: source_region_list,
			source_branch_list: source_branch_list,
			fpk_list: fpk_list,
			survey_list: survey_list,
			id_employee: id_employee,
		};
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/reco_talent') ?>',
            dataType: 'json',
			data: myData,
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
    //    get_fpk();
    }	
}

const get_datatable = async () => {
	
		let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
	
		let btnSubmit = {
			text: 'Submit',
			className: 'btn btn-success btn-md',
			action: function (e, dt, node, config) {
				let id_employee = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
					return $(entry).attr('id_employee');
				});
				
				if(id_employee.length > 0){
					$('#loader').removeClass('hidden');
					let x = [];
					let z = [];
					
					if($('#table_emp_body tr').length == 0){
						$.each(id_employee, function (i, item) {
							$('#new_emp_detail').trigger('click');
						});
					}
					else{						
						$('#table_emp_body tr').each(function (index) {
							siz_emp = $(this).find('.id_employee_input').attr('id_employee');
							x.push(parseInt(siz_emp));												
						});
					//	console.log(id_employee);
						$.each(id_employee, function (i, item) {									
							if ($.inArray(parseInt(item), x) == -1){
								$('#new_emp_detail').trigger('click');
							}
						});		
					}
					$('#table_emp_body tr').each(function (index) {
						siz_id = $(this).attr('id_record');
						z.push(siz_id);											
					});
					global_z = z.slice(-(id_employee.length));
					
					get_reco_list(id_employee).then(function(res) {
						setTimeout(function () {
							console.log(global_z);
							$.each(global_z, function (i, item) {
								if ($.inArray(res[i].id_employee, x) == -1){
										if (res[i].kpi_average == null){
											var li = $('#emp_'+item+'_info').parent().parent().parent();
											li.find('.delete-record').trigger('click');
										}
										$('#emp_'+item+'_info').attr('info',res[i].id_employee);
										$('#emp_'+item+'_id_employee').val(res[i].id_employee).trigger('change');
										$('#emp_'+item+'_id_employee').attr('id_employee',res[i].id_employee);

										$('#emp_'+item+'_generate_psycho').attr('id-employee', res[i].id_employee_psychogram);
										$('#emp_'+item+'_generate_psycho').attr('id-batch', res[i].id_batch);
										$('#emp_'+item+'_generate_psycho').attr('id-candidate', res[i].id_candidate_psychogram);
										$('#emp_'+item+'_generate_psycho').attr('source', res[i].psychotest_source);

										$('#emp_'+item+'_nik_employee').html(res[i].nik_employee);
										$('#emp_'+item+'_name_employee').html(res[i].name);
										$('#emp_'+item+'_id_position_detail').val(res[i].id_position_detail).trigger('change');
										$('#emp_'+item+'_position_route').html(res[i].position_route);
										$('#emp_'+item+'_id_dept').val(res[i].id_dept).trigger('change');
									//	$('#emp_'+item+'_department').html(res[i].department);
										$('#emp_'+item+'_id_job_grade').val(res[i].id_job_grade).trigger('change');
										$('#emp_'+item+'_job_grade').html(res[i].job_grade);
										$('#emp_'+item+'_id_region').val(res[i].id_region).trigger('change');
									//	$('#emp_'+item+'_region').html(res[i].region);
										$('#emp_'+item+'_id_branch').val(res[i].id_branch).trigger('change');
										$('#emp_'+item+'_branch').html(res[i].branch);
										
										$('#emp_'+item+'_id_month1').val(res[i]._1_month_ago).trigger('change');
										$('#emp_'+item+'_id_month2').val(res[i]._2_month_ago).trigger('change');
										$('#emp_'+item+'_id_month3').val(res[i]._3_month_ago).trigger('change');
										$('#emp_'+item+'_id_month4').val(res[i]._4_month_ago).trigger('change');
										$('#emp_'+item+'_id_month5').val(res[i]._5_month_ago).trigger('change');
										$('#emp_'+item+'_id_month6').val(res[i]._6_month_ago).trigger('change');
										$('#emp_'+item+'_id_month7').val(res[i]._7_month_ago).trigger('change');
										$('#emp_'+item+'_id_month8').val(res[i]._8_month_ago).trigger('change');
										$('#emp_'+item+'_id_month9').val(res[i]._9_month_ago).trigger('change');
										$('#emp_'+item+'_id_month10').val(res[i]._10_month_ago).trigger('change');
										$('#emp_'+item+'_id_month11').val(res[i]._11_month_ago).trigger('change');
										$('#emp_'+item+'_id_month12').val(res[i]._12_month_ago).trigger('change');

										$('#emp_'+item+'_id_batch').val(res[i].id_batch).trigger('change');
										// console.log(res[i].id_batch, "batch- item", item);
		
										
										$('#emp_'+item+'_kpi_desc').val(res[i].kpi_description).trigger('change');
										$('#emp_'+item+'_name_kpi_desc').html(res[i].kpi_desc);
										if(res[i].kpi_average != null ){
											$('#emp_'+item+'_id_kpi_average').val(parseFloat(res[i].kpi_average).toFixed(2)).trigger('change');
											$('#emp_'+item+'_kpi_average').html(parseFloat(res[i].kpi_average).toFixed(2));
										}
										else{
											$('#emp_'+item+'_id_kpi_average').val(null).trigger('change');
											$('#emp_'+item+'_kpi_average').html('');
										}
																			
										$('#emp_'+item+'_id_rating').val(res[i].id_grade_promotion).trigger('change');
										$('#emp_'+item+'_final_rating').html(res[i].final_rating);
										
										if(res[i].psychogram_dept != null ){
											$('#emp_'+item+'_psych_dept').html(res[i].psychogram_dept+'\n ('+res[i].psychogram_jobgrade+')');
										}
										else{
											$('#emp_'+item+'_psych_dept').html('');
										}
										$('#emp_'+item+'_psych_dateassign').html(res[i].batch_assign_date);
										$('#emp_'+item+'_psych_id_job_grade').val(res[i].id_psychogram_jobgrade);
										$('#emp_'+item+'_psych_id_dept').val(res[i].id_psychogram_department);
									//	if(res[i].psycho_validity_status == 'Active'){
											$('#emp_'+item+'_id_potencies').val(res[i].id_potencies).trigger('change');
											if(res[i].potencies == 'Not Recommended'){
												$('#emp_'+item+'_potencies').html(res[i].potencies).addClass("badge-danger");
											}
											else if(res[i].potencies == 'Considered'){
												$('#emp_'+item+'_potencies').html(res[i].potencies).addClass("badge-warning").css('color','white');
											}
											else if(res[i].potencies == 'Recommended'){
												$('#emp_'+item+'_potencies').html(res[i].potencies).addClass("badge-success");
											}
											else{
												$('#emp_'+item+'_potencies').html(res[i].potencies);
											}
									//	}
										
										$('#emp_'+item+'_id_sp').val(res[i].sp).trigger('change');									
										if(res[i].sp == true){
											$('#emp_'+item+'_sp').html('Yes').addClass("badge-danger");
										}
										else{
											$('#emp_'+item+'_sp').html('No').addClass("badge-success");
										}
										
										$('#emp_'+item+'_id_kpk').val(res[i].kpk).trigger('change');
										if(res[i].kpk == true){
											$('#emp_'+item+'_kpk').html('Yes').addClass("badge-danger");
										}
										else{
											$('#emp_'+item+'_kpk').html('No').addClass("badge-success");
										}								
										$('#emp_'+item+'_eng_level').html(res[i].engagement_level);
										$('#emp_'+item+'_id_eng_level').val(res[i].engagement_level).trigger('change');
										$('#emp_'+item+'_id_survey_answer_user_header').val(res[i].id_survey_answer_user_header).trigger('change');
									//	$('#emp_'+item+'_id_batch').val(res[i].id_batch).trigger('change');
										$('#emp_'+item+'_batch_name').html(res[i].batch_name);
								}
							});
							$('#loader').addClass('hidden');
						}, 1500);
					});  
					$("#modal_second").removeClass("modal-backdrop fade show");
					$("#modal_browse").modal('hide'); 
				//	mass_submit(global_mass_stage,global_mass_status,id_applied_candidate)
				} else {
					swal({
						icon: 'warning',
						title: 'Warning',
						text: 'Please Select Employee List'
					});
				}
			}
		}
	
		dtButtons.push(btnSubmit);
		let myData = {
			period_date: period_date,
			source_pos_list: source_pos_list,
			source_grade_list: source_grade_list,
			source_region_list: source_region_list,
			source_branch_list: source_branch_list,
			fpk_list: fpk_list,
			survey_list: survey_list,
		};
		var t = $('#list_table').DataTable({
			buttons: dtButtons,
			processing: true,
	//		responsive: true,
			destroy: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				left: 4,
			},
			ajax: {
				url: "{{ route('talent_reco.list_talent') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				complete:function(){
					$('#loader').addClass('hidden');
				},
				error: function (jqXHR, textStatus, errorThrown) {
					//	$('#list_table').DataTable().ajax.reload();
				}
			},
			createdRow: function( row, data, dataIndex ) {
			  $(row).attr('id_employee', data['id_employee']);
			//  $(row).addClass('mass_id');
			},
			rowCallback: function( row, data, index ) {
				if(data.kpi_average == 'null' || data.kpi_average == null){
				  $(row).removeClass("even");
				  $(row).removeClass("odd");
				  $(row).addClass('ok_red');
				}  
			},
			columns: [
				{   // Checkbox select column
				data: 'id_employee',
				orderable: false,
				targets: 0,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},				
				{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'name', name: 'name'},
				{ data: 'position_route', name: 'position_route'},
				{ data: 'job_grade', name: 'job_grade'},
				{ data: 'employment_status', name: 'employment_status'},
				{ data: 'kpi_description', name: 'kpi_description', className: 'th-text-score text-center'},				
				{ data: 'kpi_average', name: 'kpi_average', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data != null){
							return parseFloat(data).toFixed(2);	
						}
						else{
							return null;
						}
					} 
				},
				{ data: 'final_rating', name: 'final_rating', className: 'th-text-score text-center'},
				{ data: 'potencies', name: 'potencies', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data == 'Not Recommended'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if( data == 'Considered') {
								return '<span class="badge badge-warning" style="padding:5px;font-size:12px;color:white;">'+data+'</span>';
						}
						else if( data == 'Recommended') {
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
							return '';
						}
					} 
				},
				{ data: 'psycho_test_date', name: 'last_test_date', className: 'th-text-score text-center'},
				{ data: 'psychogram_dept', name: 'psychogram_dept', className: 'th-text-score text-center'},
				{ data: 'psychogram_jobgrade', name: 'psychogram_jobgrade', className: 'th-text-score text-center'},
				{ data: 'psychotest_source', name: 'psychotest_source', className: 'th-text-score text-center'},
				{ data: 'psycho_validity_status', name: 'psycho_validity_status', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data == 'Active'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Valid</span>';
						}
						else if( data == 'Expired') {
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
							return null
						}
					} 
				},
				{ data: 'sp', name: 'sp', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data == true){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Yes</span>';
						}
						else {
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">No</span>';
						}
					} 
				},
				{ data: 'kpk', name: 'kpk', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data == true){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Yes</span>';
						}
						else {
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">No</span>';
						}
					} 
				},
				{ data: 'eligible_status', name: 'eligible_status', className: 'th-text-score text-center'},
				{ data: 'engagement_level', name: 'engagement_level', className: 'th-text-score text-center'},
				{ data: '_12_month_ago', name: '_12_month_ago', className: 'th-text-score text-center'},
				{ data: '_11_month_ago', name: '_11_month_ago', className: 'th-text-score text-center'},
				{ data: '_10_month_ago', name: '_10_month_ago', className: 'th-text-score text-center'},
				{ data: '_9_month_ago', name: '_9_month_ago', className: 'th-text-score text-center'},
				{ data: '_8_month_ago', name: '_8_month_ago', className: 'th-text-score text-center'},
				{ data: '_7_month_ago', name: '_7_month_ago', className: 'th-text-score text-center'},
				{ data: '_6_month_ago', name: '_6_month_ago', className: 'th-text-score text-center'},
				{ data: '_5_month_ago', name: '_5_month_ago', className: 'th-text-score text-center'},
				{ data: '_4_month_ago', name: '_4_month_ago', className: 'th-text-score text-center'},
				{ data: '_3_month_ago', name: '_3_month_ago', className: 'th-text-score text-center'},
				{ data: '_2_month_ago', name: '_2_month_ago', className: 'th-text-score text-center'},
				{ data: '_1_month_ago', name: '_1_month_ago', className: 'th-text-score text-center'},
			],
			"fnInitComplete": function (oSettings) {
				$('#list_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
				$('#list_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
			//	$('.ok_red').css("pointer-events","none");
			}
		});
		t.on('order.dt search.dt', function () {
			let i = 1;
			t.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
				this.data(i++);
			});
		}).draw();
		
		$('#advanced_list').click(function(){
			if($(".list_table").css('display') == 'none'){
				$(".list_table").show("slow");
			}
			else {
				$(".list_table").hide("slow");
			}		
		});
		
	}	
$(document).on('click', '.btn-generate-psychogram', function () {
	// console.log($(this).attr('data-id'), $(this).attr('id-employee'));
	if(!$(this).attr('id-employee')) {
		swal({
			icon: 'warning',
			title: 'Psychogram result not found',
			text: 'Please ensure that the employee has completed psychotest.'
		});
	} else if(!$(this).attr('id-batch') || $(this).attr('id-batch') == "") {
		swal({
			icon: 'warning',
			title: 'No Batch ID!'
		});
	} else {
		$.ajax({
			url: "{{ route('talent_reco.modal_psychogram') }}",
			data:{
				employee_id: $(this).attr('id-employee'),
				candidate_id: $(this).attr('id-candidate'),
				source: $(this).attr('source'),
				batch_id: $(this).attr('id-batch'),
				data_id: $(this).attr('data-id')
			},
			success: function(result){
				$("#generatePsychogramModal").empty();
				$("#generatePsychogramModal").html(result);
				$("#generatePsychogramModal").modal('show'); 
			}
		});
	}
	
	
})

$(document).on('click', '#new_emp_detail', function () {
            var content = jQuery('#sample_table_emp tr'),
                    size = global_id_emp_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','emp-'+size);
			element.attr('id_record', size);
            element.find('.delete-record').attr('data-id', size);
			element.find('.btn-generate-psychogram').attr('data-id', size);
            element.find('.info-record').attr('id', 'emp_' + size + '_info');

			element.find('.btn-generate-psychogram').attr('id', 'emp_' + size + '_generate_psycho');
			
            element.find('.id_talent_recommendation_detail_input').attr('id', 'emp_' + size + '_id_talent_recommendation_detail');
            element.find('.id_talent_recommendation_detail_input').attr('name', 'emp[' + size + '][id_talent_recommendation_detail]');
			
			element.find('.id_employee_input').attr('id', 'emp_' + size + '_id_employee');
            element.find('.id_employee_input').attr('name', 'emp[' + size + '][id_employee]');

			element.find('.nik_employee_input').attr('id', 'emp_' + size + '_nik_employee');
			element.find('.name_employee_input').attr('id', 'emp_' + size + '_name_employee');
	
			element.find('.id_position_detail_input').attr('id', 'emp_' + size + '_id_position_detail');
            element.find('.id_position_detail_input').attr('name', 'emp[' + size + '][id_position_detail]');			
			element.find('.position_route_input').attr('id', 'emp_' + size + '_position_route');
			
			element.find('.id_dept_input').attr('id', 'emp_' + size + '_id_dept');
            element.find('.id_dept_input').attr('name', 'emp[' + size + '][id_dept]');
		//	element.find('.department_input').attr('id', 'emp_' + size + '_department');
			
			element.find('.id_job_grade_input').attr('id', 'emp_' + size + '_id_job_grade');
            element.find('.id_job_grade_input').attr('name', 'emp[' + size + '][id_job_grade]');
			element.find('.job_grade_input').attr('id', 'emp_' + size + '_job_grade');
			
			element.find('.id_region_input').attr('id', 'emp_' + size + '_id_region');
            element.find('.id_region_input').attr('name', 'emp[' + size + '][id_region]');
		//	element.find('.region_input').attr('id', 'emp_' + size + '_region');
			
			element.find('.id_branch_input').attr('id', 'emp_' + size + '_id_branch');
            element.find('.id_branch_input').attr('name', 'emp[' + size + '][id_branch]');
			element.find('.branch_input').attr('id', 'emp_' + size + '_branch');
			
			element.find('.id_rating_input').attr('id', 'emp_' + size + '_id_rating');
            element.find('.id_rating_input').attr('name', 'emp[' + size + '][id_rating]');
			element.find('.final_rating_input').attr('id', 'emp_' + size + '_final_rating');
			
			element.find('.kpi_desc_input').attr('id', 'emp_' + size + '_kpi_desc');
            element.find('.kpi_desc_input').attr('name', 'emp[' + size + '][kpi_desc]');
			element.find('.name_kpi_desc_input').attr('id', 'emp_' + size + '_name_kpi_desc');
			
			element.find('.id_month1_input').attr('id', 'emp_' + size + '_id_month1');
            element.find('.id_month1_input').attr('name', 'emp[' + size + '][id_month1]');
			element.find('.id_month2_input').attr('id', 'emp_' + size + '_id_month2');
            element.find('.id_month2_input').attr('name', 'emp[' + size + '][id_month2]');
			element.find('.id_month3_input').attr('id', 'emp_' + size + '_id_month3');
            element.find('.id_month3_input').attr('name', 'emp[' + size + '][id_month3]');
			element.find('.id_month4_input').attr('id', 'emp_' + size + '_id_month4');
            element.find('.id_month4_input').attr('name', 'emp[' + size + '][id_month4]');
			element.find('.id_month5_input').attr('id', 'emp_' + size + '_id_month5');
            element.find('.id_month5_input').attr('name', 'emp[' + size + '][id_month5]');
			element.find('.id_month6_input').attr('id', 'emp_' + size + '_id_month6');
            element.find('.id_month6_input').attr('name', 'emp[' + size + '][id_month6]');
			element.find('.id_month7_input').attr('id', 'emp_' + size + '_id_month7');
            element.find('.id_month7_input').attr('name', 'emp[' + size + '][id_month7]');
			element.find('.id_month8_input').attr('id', 'emp_' + size + '_id_month8');
            element.find('.id_month8_input').attr('name', 'emp[' + size + '][id_month8]');
			element.find('.id_month9_input').attr('id', 'emp_' + size + '_id_month9');
            element.find('.id_month9_input').attr('name', 'emp[' + size + '][id_month9]');
			element.find('.id_month10_input').attr('id', 'emp_' + size + '_id_month10');
            element.find('.id_month10_input').attr('name', 'emp[' + size + '][id_month10]');
			element.find('.id_month11_input').attr('id', 'emp_' + size + '_id_month11');
            element.find('.id_month11_input').attr('name', 'emp[' + size + '][id_month11]');
			element.find('.id_month12_input').attr('id', 'emp_' + size + '_id_month12');
            element.find('.id_month12_input').attr('name', 'emp[' + size + '][id_month12]');
			
			element.find('.id_kpi_average_input').attr('id', 'emp_' + size + '_id_kpi_average');
            element.find('.id_kpi_average_input').attr('name', 'emp[' + size + '][id_kpi_average]');
			element.find('.kpi_average_input').attr('id', 'emp_' + size + '_kpi_average');
			
			element.find('.competencies_input').attr('id', 'emp_' + size + '_competencies');
            element.find('.competencies_input').attr('name', 'emp[' + size + '][competencies]');
            element.find('.competencies_input').prepend('<option selected></option>').select2({
                placeholder: "Select Competencies ...",
                data: global_competencies,
            });
			setTimeout(function(){
				element.find('.competencies_input').parent().children('span').css({"font-size":"12px"});
			}, 500);
			
			element.find('.potencies_input').attr('id', 'emp_' + size + '_potencies');
			element.find('.id_potencies_input').attr('id', 'emp_' + size + '_id_potencies');
            element.find('.id_potencies_input').attr('name', 'emp[' + size + '][id_potencies]');

			element.find('.psych_dept_input').attr('id', 'emp_' + size + '_psych_dept');
			element.find('.psych_id_dept').attr('id', 'emp_' + size + '_psych_id_dept');
			element.find('.psych_id_dept').attr('name', 'emp[' + size + '][psych_id_dept]');
			element.find('.psych_id_job_grade').attr('id', 'emp_' + size + '_psych_id_job_grade');
			element.find('.psych_id_job_grade').attr('name', 'emp[' + size + '][psych_id_job_grade]');
			element.find('.psych_dateassign_input').attr('id', 'emp_' + size + '_psych_dateassign');
			
            element.find('.id_survey_answer_user_header_input').attr('id', 'emp_' + size + '_id_survey_answer_user_header');
            element.find('.id_survey_answer_user_header_input').attr('name', 'emp[' + size + '][id_survey_answer_user_header]');
			
			element.find('.id_sp_input').attr('id', 'emp_' + size + '_id_sp');
            element.find('.id_sp_input').attr('name', 'emp[' + size + '][id_sp]');
			element.find('.sp_input').attr('id', 'emp_' + size + '_sp');
			
			element.find('.id_kpk_input').attr('id', 'emp_' + size + '_id_kpk');
            element.find('.id_kpk_input').attr('name', 'emp[' + size + '][id_kpk]');
			element.find('.kpk_input').attr('id', 'emp_' + size + '_kpk');
			
			element.find('.id_eng_level_input').attr('id', 'emp_' + size + '_id_eng_level');
            element.find('.id_eng_level_input').attr('name', 'emp[' + size + '][id_eng_level]');
			element.find('.eng_level_input').attr('id', 'emp_' + size + '_eng_level');
			
			element.find('.id_batch_input').attr('id', 'emp_' + size + '_id_batch');
            element.find('.id_batch_input').attr('name', 'emp[' + size + '][id_batch]');
			element.find('.batch_name_input').attr('id', 'emp_' + size + '_batch_name');
			
            element.appendTo('#table_emp_body');
			 $('#table_emp_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#emp-' + id).remove();
            $('#table_emp_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		
	var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('talent_reco.save') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('talent_reco.update') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });		

	$('#talentForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#talentForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: AjaxUrl,
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
								   $('#talent_table').DataTable().ajax.reload();
								   }
								);
                        } else {
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
								$("select[id='" + key + "']").addClass("custom-select");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
								var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");								
								if (tab_id != undefined) {
									$("#tab_emp_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
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
			
	

$(document).on('click', '.batch', function () {
	
	let id_talent = 0;
	let global_name_batch = "";
	let global_pro_pos = "";

		id_talent = $(this).attr('id');
		global_name_batch = $(this).attr('name_batch');
		global_pro_pos = $(this).attr('pro_pos');
		$("#contentBatch").html('');
		$("#batchForm")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#batchForm input").removeClass("is-invalid");
				
		$("#title_batch").html('Generate Batch ('+global_pro_pos+')');
	//	console.log(id_talent);
	//	get_gen_batch(id_talent,global_name_batch);
		$.ajax({
				url: "{{ route('talent_reco.modal_batch') }}",
				data:{
					global_talent:id_talent,
					global_name_batch:global_name_batch,
				},
				success: function(result){
				$("#contentBatch").html(result);
				$("#id_talent_recommendation_header_batch").val(id_talent).trigger('change');
				$('#modal_form_batch').modal('show');
			}
		});
		
   });	

	$('#batchForm').submit(function (e) {			
            e.preventDefault();
			global_emp_batch = [];
			$('#batch_table tbody tr.selected').each(function (index) {
				id_emp = $(this).attr('id_employee');
				global_emp_batch.push(parseInt(id_emp));									
			});
		
			$('#id_emp_batch').val(global_emp_batch).trigger('change');
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
					url: "{{ route('talent_reco.save_batch') }}",
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
						else if(global_emp_batch.length == 0){
							swal({
								icon: 'error',
								title: 'Oops...',
								dangerMode: true,
								text: 'Cannot Create Batch Psychotest Without any Employee Selected',
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

$(document).on('click', '.info-record', function (event) {
        let id_employee = $(this).attr('info');
	//	console.log(id_employee);
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

const get_list_batch = async (id_talent_header) => {	
		let myData = {
			id_talent: id_talent_header,
		};
		
		var t_batch = $('#batch_table').DataTable({
	//		buttons: dtButtons,
			processing: true,
	//		responsive: true,
			destroy: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			ajax: {
				url: "{{ route('talent_reco.list_batch') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
				}
			},
			rowCallback: function( row, data, index ) {
				$(row).attr('id_employee', data['id_employee']);
				if(data.id_batch == null || data.status_expired == 'Expired'){
				  $(row).addClass('oke');				  
				//  global_emp_batch.push(data.id_employee);
				}
			},		
		/*	createdRow: function( row, data, dataIndex ) {
			  $(row).attr('id_employee', data['id_employee']);
			},
		*/
			columns: [
				{   // Checkbox select column
				data: 'id_employee',
				orderable: false,
				targets: 0,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},				
				{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'emp_name', name: 'emp_name'},
				{ data: 'position_route', name: 'position_route'},
				{ data: 'job_grade', name: 'job_grade'},
				{ data: 'status_expired', name: 'status_expired', className: 'th-text-score text-center', render: function ( data, type, row ) {
						if( data == 'Expired'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Expired</span>';
						}
						else if( data == 'Valid'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Valid</span>';
						}
						else{
							return null
						}
					} 
				},
				{ data: 'batch_name', name: 'batch_name'},
			],
			"fnInitComplete": function (oSettings) {
			   $('.oke').find('.dt-checkboxes').click();
			}        
		});
		t_batch.on('order.dt search.dt', function () {
			let i = 1;
			t_batch.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
				this.data(i++);
			});
		}).draw();
		
	}

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
	let global_pro_pos = "";

		id_talent = $(this).attr('id');
		global_pro_pos = $(this).attr('pro_pos');
		$("#contentBei").html('');
		$("#beiForm")[0].reset();
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$(".feedback").children("strong").text("");
		$("#beiForm input").removeClass("is-invalid");
				
		$("#title_bei").html('Generate BEI ('+global_pro_pos+')');
		$.ajax({
				url: "{{ route('talent_reco.modal_bei') }}",
				data:{
					global_talent:id_talent,
				},
				success: function(result){
				$("#contentBei").html(result);
				$("#id_talent_recommendation_header_bei").val(id_talent).trigger('change');
				$('#modal_form_bei').modal('show');
			}
		});
		
   });	

const get_list_bei = async (id_talent_header) => {	
		let myData = {
			id_talent: id_talent_header,
		};		
		var t_bei = $('#bei_table').DataTable({
	//		buttons: dtButtons,
			processing: true,
	//		responsive: true,
			destroy: true,
			columnDefs:false,
			dom: '<"bei_left"l>frt<"bei_left"i>p',
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			ajax: {
				url: "{{ route('talent_reco.list_batch') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
				}
			},
			rowCallback: function( row, data, index ) {
				$(row).attr('id_employee', data['id_employee']);
			},	
			columns: [
				{   // Checkbox select column
				data: 'id_employee',
				orderable: false,
				targets: 0,
				render: function(data, type, row, meta){            
						  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
					   return data;
					},
				checkboxes: {
					   selectRow: true,
					   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
					}
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},				
				{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'emp_name', name: 'emp_name'},
				{ data: 'position_route', name: 'position_route'},
				{ data: 'job_grade', name: 'job_grade'},
				{ data: 'bei_conclusion', name: 'bei_conclusion'},
			],  			
		});
		t_bei.on('order.dt search.dt', function () {
			let i = 1;
			t_bei.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
				this.data(i++);
			});
		}).draw();
		
	}
	
	$('#beiForm').submit(function (e) {			
            e.preventDefault();
			global_emp_bei = [];
			$('#bei_table tbody tr.selected').each(function (index) {
				id_emp = $(this).attr('id_employee');
				global_emp_bei.push(parseInt(id_emp));									
			});
			$('#id_emp_bei').val(global_emp_bei).trigger('change');
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
					url: "{{ route('talent_reco.save_bei') }}",
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

const get_psychomodal_grade = async () => {
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
		        $('#psychomodal_grade').select2({
		            data: allGrade,
		            placeholder: 'Select Grade'
		        });
		        $('.preview_grade').select2({
		            data: allGrade,
		            placeholder: 'Select Grade'
		        });
            },
        });
        return result;
    } catch (error) {
        get_psychomodal_grade();
    }
}

const get_psychomodal_department = async () => {
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
		        $('#psychomodal_department, #filter_department_candidate, #filter_department_employee').select2({
		            data: allDepartment,
		            placeholder: 'Select Department'
		        });
            },
        });
        return result;
    } catch (error) {
        get_psychomodal_department();
    }
}

$(document).ready(function () {
	$('.close-psychogram-modal', 'click', function () {
		$('#generatePsychogramModal').html('');
	});
});

$(document).on('click', '#export_talent', function() {
	let id_talent_recommendation_header = $(this).attr('id-talent');
	window.open("{{route('talent_reco.export')}}?id_talent_recommendation_header="+id_talent_recommendation_header, Math.random());
});

$(document).on('click', '#import_talent', function() {
	$('#importRecoId').val($(this).attr('id-talent'));
	$('#modal_import_reco').modal('show');
});

$(document).on('submit', '#importRecoForm', function(e) {
	e.preventDefault();
	let data = new FormData(this);
	$.ajax({
		url: "{{route('talent_reco.import')}}",
		data: data,
		type: 'POST',
		dataType: "JSON",
		processData: false,
		contentType: false,
		beforeSend: () => {
			$('#loader').removeClass('hidden');
		},
		complete: () => {
			$('#loader').addClass('hidden');
		},
		success: (res) => {
			$('#importRecoForm')[0].reset();
			$('#table_emp_body').empty();
			get_edit($('#import_talent').attr('id-talent'));
			$('#modal_import_reco').modal('hide');
			swal({
				icon: 'success',
				title: 'Success',
				text: res.message
			});
		},
		error: (err) => {
			swal({
				icon: 'error',
				text: err.responseJSON.message,
				title: 'Error'
			})
		}
	})
});

$(document).on('click', '.info-record', function() {
	let nik = $(this).closest('tr').find('.nik_employee_input').text();
	let jobGrade = $(this).closest('tr').find('.job_grade_input').text();
	let period = $('#period_date').val();
	let queryParam = $.param({
		nik_employee: nik,
		desc_job_grade: jobGrade,
		period_date: period
	});
	// console.log(nik, jobGrade);
	$('#kpi_table').DataTable().ajax.url("{{route('talent_reco.get_employee_kpi')}}?"+queryParam);
	$('#kpi_table').DataTable().ajax.reload();
	$('#modal_talent_info').modal('show');
	// $.ajax({
	// 	url: "{{route('talent_reco.get_employee_kpi')}}",
	// 	data: {
	// 		nik_employee: nik,
	// 		desc_job_grade: jobGrade,
	// 		period_date: period
	// 	},
	// 	success: (res) => {
	// 		console.log(res)
	// 	}
	// })
})

</script>
@endsection