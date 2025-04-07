@extends('adminlte::page')
@section('title', 'Candidate Data')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Candidate Data</h5>               
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
				<div class="div_datatable_group" style="display:none;"> 
					<button onclick="return false;" class="btn btn-default pull-left advanced_group">Advanced Search</button><br><br>
					<div id="table_div_group"></div>
				</div>
				
				<div class="div_datatable_can" style="display:none;"> 
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Searching Applied / Created Date :</label>
						<div class="col-md-3">
							<div class="input-group">
								<input name="daterange" id="daterange" type="daterange" placeholder="Select Date Range ..." class="form-control form-control-sm" />
								<input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
								<input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>
						
						<div class="col-sm-4">
							<select id="fil_can" class="form-control form-control-sm select2" data-placeholder="Select Candidate Name ..." style="width: 100%;" multiple="multiple"></select>
						</div>
						<div class="col-sm-2">
							<button onclick="return false;" id="search_created" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
						</div>										
					</div>
					<button onclick="return false;" class="btn btn-default pull-left advanced_can">Advanced Search</button><br><br>
					<div id="table_div_can"></div>
				</div>
			</div>
        </div>
    </div>
</div>
<div id="myModal_group" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
			<div class="modal-header">
				<h5 class="detail_can_group modal-title">Candidate List</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody_group">
			<button onclick="return false;" class="btn btn-default pull-left advanced_can_group">Advanced Search</button><br><br>
			<div id="table_div_can_group"></div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
    </div>
  </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="detailCanForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 class="detail_can modal-title">Detail Candidate</h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>  
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
<div id="assign_batch_modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl">
	  <!-- Modal content-->
	  <div class="modal-content">
		  <form method="POST" id="assignCanForm">
			  {{ csrf_field() }}
			  <div class="modal-header">
				  <h5 class="">Assign Candidate to Batch</h5>
				  <button type="button" class="close" onclick=""  data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
				  </button>
			  </div>
			<div class="modal-body" id="assignContentBody">
				<div>
					<div class="can-branch-warning"></div>
				</div>
				<div class="col">
					<div class="row">
						<div class="col">
							<label for="assign_batch">Assign to Batch</label>
						</div>
						<div class="col">
							<input type="hidden" name="id_candidate" id="assign_candidate">
							<select name="id_batch" id="assign_batch" style="width:100%;"></select>
						</div>
					</div>
				</div>
				<div class="col" id="selectedBatchDetail" style="display: none;">
					<div class="row mt-2">
						<div class="col">
							<b>Batch Code</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_code"></span>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col">
							<b>Location</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_location"></span>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col">
							<b>Start Date</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_start"></span>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col">
							<b>End Date</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_end"></span>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col">
							<b>Participant</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_participant"></span>
						</div>
					</div>
					<div class="row mt-1">
						<div class="col">
							<b>Status</b>
						</div>
						<div class="col">
							<span id="selectedBatchDetail_status"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			  <button type="submit" class="btn btn-sm btn-primary" id="assign_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			  <button type="button" class="btn btn-default" onclick=""  data-dismiss="modal">Close</button>
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
	.button-none{
		padding:0 2px 0 2px !important;
	}
	.modal{
		overflow:auto !important;
	}
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let id_candidate = 0;
let id_applied = 0;
let id_dept = 0;
let id_job_grade = 0;
let id_batch = 0;
let global_interview = "";
let global_sum_sequence = 0;
let global_sum_obs = 0;
let global_sequence = 0;
let global_id_stage = 0;
let global_code_stage = "";
let global_mass_stage = 0;
let global_mass_status = 0;


function on_close_modal() {
	$('#group_table').DataTable().ajax.reload();
	$('#candidate_table').DataTable().ajax.reload(); 
}

$(document).on('click', '.edit_group', function(){
//	$("#contentBody_group").html('');
	let id_hiring_request_header = $(this).attr('id');
	let request_position = $(this).attr('job');
	$(".detail_can_group").html("");
	$(".detail_can_group").html('Candidate List ('+request_position+')');
	$("#table_div_can_group").html("");	
	$("#table_div_can_group").html('<table id="list_candidate_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	get_edit_group(id_hiring_request_header);
	
	$("#myModal_group").modal('show'); 
	$("#list_candidate_table_processing").css("background","white");
	$("#list_candidate_table_processing i.fa-spinner").css("margin-top","100px");
	$("#list_candidate_table_processing").css("color","black");
});	

function status_stage(){		
		$.getJSON('<?= url('recruitment/recruitment/candidate/get_mass_stage') ?>', function (data) {			
		/*	let code_view = 0;
			let arr_view = [];
			$.each(data, function(idx, item) {
				 if(item.code == 'DRF' || item.code == 'SHL'){
					code_view = item.id;
					arr_view.push(code_view);
				 }
			});
		*/
			$('#mass_stage').prepend('<option selected></option>').select2({
				placeholder: "Select Rec. Stage ...",
				data: data,
			}).on('change', function (e) {
			//	$('#status_stage option').attr('disabled',true);
				global_mass_stage = $(this).select2('data')[0].id;
			//	console.log(global_mass_stage);
				var aaList = $("option", e.target);
				$.each(aaList, function(idx, item) {
				//	$.each(arr_view, function(i, val) {
						$("option[value='"+item+"']").attr('disabled',false);																							
				//	});
				});		
			}).trigger('change');	
			
			$('#mass_status').prepend('<option selected></option>').select2({
				placeholder: "Select Status ...",
				data: rec_status,
			}).on('change', function (e) {
				global_mass_status = $(this).select2('data')[0].id;	
			}).trigger('change');
		}).fail(function (data) { // Call failed
            status_stage();
		});					
}

function get_edit_group(id_hiring_request_header) {
	status_stage();
	let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
	
	let stageList = {
		text: '<select name="mass_stage" id="mass_stage" class="form-control form-control-sm" style="width:100%;"></select><span class="invalid-feedback" role="alert" id="mass_stageError"><strong></strong></span>',
		className: 'button-none',
	 }
	 let statusList = {
		text: '<select name="mass_status" id="mass_status" class="form-control form-control-sm" style="width:100%;"></select><span class="invalid-feedback" role="alert" id="mass_statusError"><strong></strong></span>',
		className: 'button-none',
	 }
	 
	 let btnSubmit = {
        text: 'Submit',
        className: 'btn btn-success btn-sm',
        action: function (e, dt, node, config) {
            let id_applied_candidate = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).attr('id_applied_candidate');
            });
			
            if(id_applied_candidate.length > 0){
			//	console.log(global_mass_stage);
                mass_submit(global_mass_stage,global_mass_status,id_applied_candidate)
            } else {
                swal({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please Select Candidate List'
                });
            }
        }
    }
		
	dtButtons.push(stageList);
	dtButtons.push(statusList);
	dtButtons.push(btnSubmit)
		
	let myData_group = {
		id_hiring_request_header: id_hiring_request_header,
	};
	
	let t_list_can =	$('#list_candidate_table').DataTable({
			buttons: dtButtons,
			processing: true,
			columnDefs: false,
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(2)'
			},
			responsive: true,
			destroy: true,
			ajax: {
				url: "{{ route('candidate_data.get_detail_group') }}",
				"data": myData_group,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#list_candidate_table').DataTable().ajax.reload();
					}
				},
			createdRow: function( row, data, dataIndex ) {
			  $(row).attr('id_applied_candidate', data['id_applied_candidate']);
			//  $(row).addClass('mass_id');
		  },
			rowCallback: function(row, data, index){
				if(data['code_stage'] == 'HIR' && data['status'] == 'Pass'){
					$(row).find('.cancel_join').show();
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
				{ data: 'photo_candidate', title: 'Photo', responsivePriority: 4, className: 'text-middle', render: function ( data, type, row ) {	
						return '<img align="center" class="img-circle elevation-2" src="https://career.borwita.co.id/storage/candidate_photo/'+data+'" style="height:50px;width:50px;border-radius:999px;" alt="User Avatar">';
					} 
				},
				{ data: 'name', title: 'Name', responsivePriority: 3},
				{ data: 'identification_number', title: 'ID Number'},
				{ data: 'mobile_phone', title: 'Mobile Phone'},
				{ data: 'interest', title: 'Interesting', responsivePriority: 6},
				{ data: 'request_position', title: 'Request Position', responsivePriority: 5},
				{ data: 'rec_stage', title: 'Rec. Stage', responsivePriority: 7, render: function ( data, type, row ) {
						if(row.code_stage == 'HIR'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_stage == 'CLJ'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
								return data;
						}
					}
				},
				{ data: 'status', title: 'Status', responsivePriority: 8, className: 'text-center', render: function ( data, type, row ) {
						if(row.status == 'Pass'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Pass</span>';
						}
						else if(row.status == 'Review'){
								return '<span class="badge badge-info" style="padding:5px;font-size:12px;">Review</span>';
						}
						else if(row.status == 'Failed'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Failed</span>';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'batch_name', title: 'Batch Name'},
				{ data: 'is_profil_completed', title: 'Profile Completed', responsivePriority: 9, className: 'text-center', render: function ( data, type, row ) {
						if(row.is_profil_completed == true){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">YES</span>';
						}
						else{
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
						}
					} 
				},
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, width: '100px', className: 'space th-text-score', render: function ( data, type, row ) {	
						return '<div align="center">'+data+'</div>';
					} 
				},
			],
			"fnInitComplete": function (oSettings) {
			   $('#list_candidate_table_wrapper .column-filter-widget:eq(11)').css('display','none').change();
			}
		});
		
		t_list_can.on('order.dt search.dt', function () {
        let i = 1;
        t_list_can.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
}	


function mass_submit(global_mass_stage,global_mass_status,id_applied_candidate) {
    let result;
    let _token  = "<?= csrf_token() ?>";
    try {
		$(".invalid-feedback").children("strong").text("");
		$(".feedback").children("strong").text("");
        result = $.ajax({
            type: 'POST',
            url: "<?= url('recruitment/recruitment/candidate/mass_submit') ?>",
            dataType: 'json',
            data: {
                mass_stage: global_mass_stage,
                mass_status: global_mass_status,
                id_applied_candidate: id_applied_candidate,
				id_url: global_url_server,
                _token: _token,
            },
            success: function (resp) {
                if(resp.status == 'true'){
                    swal({
						icon: 'success',
                        title: 'Success',
                        text: resp.message
					}).then(ok => {
						$('#list_candidate_table').DataTable().ajax.reload();
					});
                } else {
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message
                    });
                }
            },
			error: function (resp) {
				 if (resp.status === 422) {
						let errors = resp.responseJSON.errors;
						Object.keys(errors).forEach(function (key) {
							var key_temp = key.replaceAll(".", "_");
							$("#" + key_temp).addClass("is-invalid");
							$("#" + key_temp + "Error").children("strong").text(errors[key][0]);								
						});
					}
				else{		
					swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong! [Unknown Error]'
					});
				}
			}
        });
        return result;
    } catch (error) {
    }
}


function loadprofile(id_candidate,id_applied_candidate,name,id_dept,id_job_grade,id_batch){
	$("#contentBody").html('');
    $.ajax({
		url: "{{ route('candidate_data.modal_detail') }}",
		data:{global_id_candidate:id_candidate,
				global_id_applied:id_applied_candidate,
				global_id_dept:id_dept,
				global_id_job_grade:id_job_grade,
				global_id_batch:id_batch},
		success: function(result){
        $(".detail_can").html('Detail Candidate ('+name+')');
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }
	});
}

$(document).on('click', '#search_grouping', function () {
	if($('#grouping :selected').val() == 'group_job'){
		$(".div_datatable_can").hide();
		$("#group_table").html("");
		get_datatable_group();
		
	}
	else if($('#grouping :selected').val() == 'list_candidate'){
		$(".div_datatable_group").hide();
		$(".advanced_can").hide();
		$("#table_div_can").html("");
		$("#candidate_table").html("");
		$(".div_datatable_can").show();
	//	$("#search_created").trigger('click');
	//	get_datatable_candidate();		
	}
});

$(document).on("click", ".advanced_group", function () {
	$('.cf').select2({width:'100%'});
	if($(".group_table").css('display') == 'none'){
		$(".group_table").show("slow");
	}
	else {
		$(".group_table").hide("slow");
	}   
});

$(document).on("click", ".advanced_can_group", function () {
	$('.cf').select2({width:'100%'});
	if($(".list_candidate_table").css('display') == 'none'){
		$(".list_candidate_table").show("slow");
	}
	else {
		$(".list_candidate_table").hide("slow");
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

const get_datatable_group = async () => {
$(".div_datatable_group").show();
$(".advanced_group").show();

$("#table_div_group").html("");	
$("#table_div_group").html('<table id="group_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');

	let t_group = $('#group_table').DataTable({
		processing: true,
		columnDefs: false,
		select: {
		  style:    'multi+shift',
		  selector: 'td:nth-child(2)'
		},
		responsive: true,
		destroy: true,
		ajax: {
			url: "{{ route('candidate_data.index_group') }}",
			data: {id_url: global_url_server},	
			"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			error: function (jqXHR, textStatus, errorThrown) {
					$('#group_table').DataTable().ajax.reload();
				}
			},
		columns: [
			{defaultContent: '',orderable: false},
			{   // Checkbox select column
			data: 'id_hiring_request_header',
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
            { data: 'fpk_no', title: 'No. FPK', responsivePriority: 7},
            { data: 'description', title: 'Request Position', responsivePriority: 3},
            { data: 'branch', title: 'Branch', responsivePriority: 4},
            { data: 'applicant_total', title: 'Applicant Total', responsivePriority: 5, className: 'text-score th-text-score'},
            { data: 'hiring_request_status', title: 'FPK Status', responsivePriority: 6, className: 'text-middle th-text-score', 
			render: function ( data, type, row ) {
					if(row.hiring_request_status == 'D'){
							return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Close</span>';
					}
					else if(row.hiring_request_status == 'C'){
							return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Cancel</span>';
					}
					else if(row.hiring_request_status == 'P'){
							return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">Partial Close</span>';
					}
					else if(row.hiring_request_status == 'O'){
							return '<span  class="badge badge-info" style="padding:5px;font-size:12px;">Open</span>';
					}
				} 
			},
            { data: 'target_date', title: 'Target Date', responsivePriority: 7, className: 'th-text-date', render: function ( data, type, row ) {	
					if(data != null){
						return '<div align="center">'+moment(data).format('DD MMM YYYY')+'</div>';
					}
					else{
						return '';
					}
				} 
			},
			{ data: 'action', title: 'Action', responsivePriority: 1, className: 'th-text-score', orderable: false, render: function ( data, type, row ) {	
					return '<div align="center">'+data+'</div>';
				} 
			},
		],
		"fnInitComplete": function (oSettings) {
		   $('#candidate_table_wrapper .column-filter-widget:eq(8)').css('display','none').change();
		}
	});
	
	t_group.on('order.dt search.dt', function () {
        let i = 1;
        t_group.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}

$(document).on('click', '#search_created', function () {
    get_datatable_candidate();
});

const get_datatable_candidate = async () => {
	$(".div_datatable_can").show();
    $(".advanced_can").show();

	$("#table_div_can").html("");	
	$("#table_div_can").html('<table id="candidate_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
	let myParam = {
		//	startdate: $("#startdate").val() == '' ? null : $("#startdate").val(),
		//	enddate: $("#enddate").val() == '' ? null : $("#enddate").val(),
			fil_can: $("#fil_can").val() == '' ? null : $("#fil_can").val().join(','),
			daterange: $("#daterange").val() == '' ? null : $("#daterange").val(),
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
				url: "{{ route('candidate_data.index_can') }}",
				data: myParam,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#candidate_table').DataTable().ajax.reload();
					}
				},
			rowCallback: function(row, data, index){
				if(data['code_stage'] == 'HIR' && data['status'] == 'Pass'){
					$(row).find('.cancel_join').show();
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
				{ data: 'photo_candidate', title: 'Photo', responsivePriority: 4, className: 'text-middle', render: function ( data, type, row ) {	
						return '<img align="center" class="img-circle elevation-2" src="https://career.borwita.co.id/storage/candidate_photo/'+data+'" style="height:50px;width:50px;border-radius:999px;" alt="User Avatar">';
					} 
				},
				{ data: 'name', title: 'Name', responsivePriority: 3},
				{ data: 'email', title: 'Email' },
				{ data: 'identification_number', title: 'ID Number'},
				{ data: 'mobile_phone', title: 'Mobile Phone'},
				{ data: 'interest', title: 'Interesting', responsivePriority: 6},
				{ data: 'request_position', title: 'Request Position', responsivePriority: 5},
				{ data: 'rec_stage', title: 'Rec. Stage', responsivePriority: 7, render: function ( data, type, row ) {
						if(row.code_stage == 'HIR'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else if(row.code_stage == 'CLJ'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
						}
						else{
								return data;
						}
					}
				},
				{ data: 'status', title: 'Status', responsivePriority: 8, className: 'text-center', render: function ( data, type, row ) {
						if(row.status == 'Pass'){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Pass</span>';
						}
						else if(row.status == 'Review'){
								return '<span class="badge badge-info" style="padding:5px;font-size:12px;">Review</span>';
						}
						else if(row.status == 'Failed'){
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Failed</span>';
						}
						else{
								return data;
						}
					} 
				},
				{ data: 'batch_name', title: 'Batch Name'},
				{ data: 'is_profil_completed', title: 'Profile Completed', responsivePriority: 9, className: 'text-center', render: function ( data, type, row ) {
						if(row.is_profil_completed == true){
								return '<span class="badge badge-success" style="padding:5px;font-size:12px;">YES</span>';
						}
						else{
								return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">NO</span>';
						}
					} 
				},
				{ data: 'action', title: 'Action', responsivePriority: 1, orderable: false, width: '250px', className: 'space th-text-score', render: function ( data, type, row ) {	
						return '<div align="center">'+data+'</div>';
					} 
				},
			],
			"fnInitComplete": function (oSettings) {
			   $('#candidate_table_wrapper .column-filter-widget:eq(11)').css('display','none').change();
			}
		});
		
		t_can.on('order.dt search.dt', function () {
        let i = 1;
        t_can.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
}
	
$(document).ready(function(){

/*
document.addEventListener("visibilitychange", function(event) {
      $('#group_table').DataTable().ajax.reload();
	  $('#candidate_table').DataTable().ajax.reload(); 
});
*/
$(".advanced_group").hide();
$(".advanced_can").hide();
	
grouping_by = [
	{
		id: 'group_job',
		text: 'Position List'
	},	
	{
		id: 'list_candidate',
		text: 'Candidate List'
	},		
];

rec_status = [
	{
		id: 'Review',
		text: 'Review'
	},
	{
		id: 'Pass',
		text: 'Pass'
	},
	{
		id: 'Failed',
		text: 'Failed'
	},
];
	get_can_name();
	daterange();
	get_filter().then(function() {
		$('#search_grouping').trigger('click');
	});  	
});


$('#detailCanForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#detailCanForm input").removeClass("is-invalid");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('candidate_data.update') }}",
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
									$('#candidate_table').DataTable().ajax.reload();
									$('#list_candidate_table').DataTable().ajax.reload();
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


const get_filter = async () => {
    try {
		$('#grouping').select2({
			data:grouping_by
		}).on('change', function (e) {			
			if($(this).select2('data')[0].id == 'group_job'){
				$(".div_datatable_can").hide();
			}
			else if($(this).select2('data')[0].id == 'list_candidate'){
				$(".div_datatable_group").hide();
			}
		}).trigger('change');
    } 
	catch (error) {
    }	
}

$('#fil_can').select2({
	// data: data,
	ajax: {
		url: '<?= url('recruitment/recruitment/candidate/get_can_name') ?>',
		dataType: 'json',
		delay: 350,
		data: function (params) {
			return {
				name: params.term,
				limit: 10
			}
		},
		processResults: function (data) {
			return {
				results: data
			};
		}
	},
	allowClear: true,
	quietMillis: 500,
	delay: 500,
});

function get_can_name(){		
		// $.getJSON('<?= url('recruitment/recruitment/candidate/get_can_name') ?>', function (data) {
			
		// }).fail(function (data) { // Call failed
        //     get_can_name();
		// });					
}

function get_pos(){		
		$.getJSON('<?= url('recruitment/recruitment/candidate/get_pos') ?>', function (data) {
			$('#pos_req').prepend('<option selected></option>').select2({
				placeholder: "Select Job Position ...",
				data: data,
				allowClear: true,
			});
		}).fail(function (data) { // Call failed
            get_pos();
		});					
}

const get_pos_done = async (id_hiring_header) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_pos_done') ?>',
			data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
				$('#pos_req').prepend('<option selected></option>').select2({
					placeholder: "Select Job Position ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_pos_done(id_hiring_header);
    }	
}

$(document).on('change', '#pos_req', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
		$('#ref_number').val('');
		$('#can_branch').val('');
		
		get_pos_change($(this).select2('val'));	
	}
});

function get_pos_change(id_hiring_header){
	let myData = {
			id_hiring_header: id_hiring_header,
		};	
	$.ajax({
		url: "<?= url('recruitment/recruitment/candidate/get_pos_change') ?>",
		method: "GET",
		data: myData,
		beforeSend: function () {
			$('#ref_number').val('');
			$('#can_branch').val('');
		},
		success: function (response) {
			$('#ref_number').val(response.ref_number);
			$('#can_branch').val(response.branch);
		}
	});
}

const get_stage_applied = async (seq,id_status) => {
//	console.log(seq);
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_stage') ?>',
		//	data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
		//		console.log(res);
				let code_seq = 0;
				let arr = [];
				$.each(res, function(idx, item) {
					 if(item.sequence == seq || item.sequence == global_sum_sequence || item.sequence == global_sum_obs){
						code_seq = item.id;
						arr.push(code_seq);
					 }
				});
								
				$('#can_stage').prepend('<option selected></option>').select2({
					placeholder: "Select Recruitment Stage ...",
					data: res,
				}).on('change', function (e) {
					$('#can_stage option').attr('disabled',true);
					var aaList = $("option", e.target);
					$.each(aaList, function(idx, item) {
						$.each(arr, function(i, val) {
							$("option[value='"+val+"']").attr('disabled',false);																							
						});	
					});	
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_stage_applied(seq,id_status);
    }	
}
		
const get_stage_preview = async (seq) => {
//	console.log(seq);
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('recruitment/recruitment/candidate/get_stage') ?>',
		//	data: {id_hiring_header: id_hiring_header},
            dataType: 'json',
            success: function (res) {
		//		console.log(res);
				let code_dbs = 0;
				let code_seq = 0;
				$.each(res, function(idx, item) {
					 if(item.sequence == seq){
						code_seq = item.id;
					 }
					 else if(item.code == 'DBS'){
						code_dbs = item.id;
					}
				});
								
				$('#can_stage').prepend('<option selected></option>').select2({
					placeholder: "Select Recruitment Stage ...",
					data: res,
				}).on('change', function (e) {
					$('#can_stage option').attr('disabled',true);
					var aaList = $("option", e.target);
					$.each(aaList, function(idx, item) {
						$("option[value='"+code_seq+"']").attr('disabled',false);
						$("option[value='"+code_dbs+"']").attr('disabled',false);
					});	
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_stage_preview(seq);
    }	
}
		
/*
$(document).on('change', '#can_status', function (event, istrigger) {  
    if(!istrigger){			
		if($(this).select2('data')[0].id == "Pass"){
			get_stage_applied(global_sequence,global_id_stage).then(function(res) {
				setTimeout(function () {
					$('#can_stage').val(global_id_stage).trigger('change');
				}, 500);
			});
		}
		else if($(this).select2('data')[0].id == "Review"){
			get_stage_preview(global_sequence).then(function(res) {
				setTimeout(function () {
					$('#can_stage').val(global_id_stage).trigger('change');
				}, 500);
			});
		}
	}
});
*/

$(document).on('change', '#can_stage', function (event, istrigger) {  
//	console.log(istrigger);
    if(!istrigger){
	//	$('#offering_date').val('');
	//	$('#join_date').val('');
		
		if($(this).select2('data')[0].id !== ""){
			global_code_stage = $(this).select2('data')[0].code;
			if($(this).select2('data')[0].code == 'OFL' && $('#can_status').val() == 'Pass'){
				$('#offering_date').attr('disabled',false);
				$('#offering_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#join_date').attr('disabled',true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
			else if($(this).select2('data')[0].code == 'HIR'  && $('#can_status').val() == 'Pass'){
				$('#join_date').attr('disabled',false);
				$('#join_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#offering_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
			}
			else{
				$('#offering_date').attr('disabled',true);
				$('#join_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
			$('#can_status').val('Review').trigger('change');
		}
	}
});

$(document).on('change', '#can_status', function (event, istrigger) {  
    if(!istrigger){	
		if($(this).select2('data')[0].id !== ""){
			if(global_code_stage == 'OFL' && $(this).select2('data')[0].id == 'Pass'){
				$('#offering_date').attr('disabled',false);
				$('#offering_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#join_date').attr('disabled',true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
			else if(global_code_stage == 'HIR' && $(this).select2('data')[0].id == 'Pass'){
				$('#join_date').attr('disabled',false);
				$('#join_date').parent().children('span').children('button').attr('disabled', false);
				
				$('#offering_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
			}
			else{
				$('#offering_date').attr('disabled',true);
				$('#join_date').attr('disabled',true);
				$('#offering_date').parent().children('span').children('button').attr('disabled', true);
				$('#join_date').parent().children('span').children('button').attr('disabled', true);
			}
		}
	}
});

$(document).on('click', '.cancel_join', function (event) {
	id_applied_candidate = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This Data will be Cancel Join!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, Cancel it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"candidate/cancel_join/"+id_applied_candidate,
			   beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#list_candidate_table').DataTable().ajax.reload();
				 $('#candidate_table').DataTable().ajax.reload();
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#list_candidate_table').DataTable().ajax.reload();
						$('#candidate_table').DataTable().ajax.reload();
					});
				}, 50);
			   },
			   complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});

function daterange(startdate='', enddate='') {
    let separator = '   to   ';
//  let start = (startdate=='' || startdate==null) ? moment().subtract(30, 'days').format('YYYY-MM-DD') : startdate;
//	let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
    $('#daterange').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: false,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
			cancelLabel: 'Clear'
        },
//        startDate: start, 
//        endDate: end,
    });
	
	$('#daterange').on('apply.daterangepicker', function(ev, picker) {
		$("#startdate").val(picker.startDate.format('YYYY-MM-DD'));
		$("#enddate").val(picker.endDate.format('YYYY-MM-DD'));
		$(this).val(picker.startDate.format('YYYY-MM-DD') + '   to   ' + picker.endDate.format('YYYY-MM-DD'));
	});
	$('#daterange').on('cancel.daterangepicker', function(ev, picker) {
		$("#startdate").val('');
		$("#enddate").val('');
		$(this).val('');
	});
    $('#daterange').val("");
//	$("#startdate").val(start);
//	$("#enddate").val(end);
}
function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}
$(document).on("click", ".pdf", function () {
	let res = {
        id_candidate: $(this).attr('id'),
    };
    let param = objectToQueryString(res);
	let url = "{{ url('recruitment/recruitment/candidate/download') }}";
    window.open(url+'?'+param, '_blank');
});

function download(url) {
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

var global_master_batch = [];

function loadAssignBatchModalData(idCandidate, candidateBatchData, batchData = []) {
	$('#selectedBatchDetail').css('display', 'none');
	$('.can-branch-warning').empty();
	$('#assign_candidate').val(idCandidate);
	if(candidateBatchData.length > 0) {
		$('.can-branch-warning').html(`<p class="alert alert-warning"><b>${candidateBatchData[0].name}</b> berada di batch <b>${candidateBatchData[0].batch_name}</b> (${candidateBatchData[0].batch_code}).</p>`);
		$('#assign_batch').empty().select2().attr('disabled');
	} else {
		$('#assign_batch').empty().prepend('<option></option>').select2({
			allowClear: true,
			placeholder: 'Select Batch',
			data: batchData
		});
	}
	
}

$(document).on('click', '.assign-batch', function() {
	let idCandidate = $(this).attr('id-candidate');
	$.ajax({
		url: "{{route('candidate_data.get_batch')}}",
		data: {id_candidate: idCandidate},
		beforeSend: () => $('#loader').removeClass('hidden'), 
		success: (res) => {
			global_master_batch = res.all_batch;
			$('#loader').addClass('hidden');
			loadAssignBatchModalData(idCandidate, res.candidate_batch, res.all_batch);
			$('#assign_batch_modal').modal('show');
		},
		error: (res) => {
			$('#loader').addClass('hidden');
			swal({
				title: 'Error',
				text: res.message,
				icon: 'error',
				dangerMode: true,
			});
		}
	});
});

$(document).on('submit', '#assignCanForm', function(e) {
	e.preventDefault();
	$.ajax({
		url: "{{route('candidate_data.assign_batch')}}",
		data: $(this).serialize(),
		type: 'POST',
		beforeSend: () => $('#loader').removeClass('hidden'),
		success: (res) => {
			$('#loader').addClass('hidden');
			swal({
				title: 'Success',
				text: res.message,
				icon: 'success'
			});
			$('#assign_batch_modal').modal('hide');
		},
		error: (error) => {
			$('#loader').addClass('hidden');
			swal({
				title: 'Error',
				text: error.responseJSON?.message,
				icon: 'error',
				dangerMode: true,
			});
		}
	})
})

$(document).on('change', '#assign_batch', function() {
	if($('#assign_batch').val() && $('#assign_batch').val() != '') {
		global_master_batch.forEach((val) => {
			if(val.id == $('#assign_batch').val()) {
				$('#selectedBatchDetail').css('display', 'grid');
				$('#selectedBatchDetail_code').text(val.batch_code);
				$('#selectedBatchDetail_location').text(val.location);
				$('#selectedBatchDetail_start').text(moment(val.start_date).format('DD MMMM YYYY'));
				$('#selectedBatchDetail_end').text(moment(val.end_date).format('DD MMMM YYYY'));
				$('#selectedBatchDetail_participant').text(val.participant);
				if(val.status == 'A') {
					$('#selectedBatchDetail_status').html(`<span class="badge badge-sm badge-success">Active</span>`);
				} else {
					$('#selectedBatchDetail_status').html(`<span class="badge badge-sm badge-danger">Inactive</span>`);
				}
			}
		})
	} else {
		$('#selectedBatchDetail').css('display', 'none');
	}
	
})
</script>
@endsection