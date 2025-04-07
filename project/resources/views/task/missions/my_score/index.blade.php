@extends('adminlte::page')
@section('title', 'My Missions Score')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">My Missions Score</h5>
      </div>
      <div class="card-body">
		<div class="form-group row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-4">
						<div class="">
							<select id="task_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="">
							<select id="cycle_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" />
							<input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
							<input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
							<div class="input-group-append">
								<span class="input-group-text far fa-calendar form-control-sm"></span>
							</div>
						</div>
					</div>
					<div class="col-md-2">
						<button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>
				</div>
			</div>	
		</div>
		<div class="div_datatable" style="display:none;"> 
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>	
				<br>
				<br>
				<table id="missions_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
				  <thead>
				   <tr class="text text-center">
					<th data-priority="1"></th>
					<th data-priority="1" width=10></th>
					<th data-priority="3" width=10>No</th>
					<th data-priority="6">Task</th>
					<th data-priority="7" width=150>Activity</th>
					<th data-priority="13" width=150>Evidence</th>
					<th data-priority="9">Type</th>
					<th data-priority="9">Cycle</th>
					<th data-priority="10" width=100>Start Date</th>
					<th data-priority="11" width=100>End Date</th>
					<th data-priority="12" width=100>Submit Date</th>
					<th data-priority="8" width=80>Maximum Score</th>
					<th data-priority="8" width=80>Score Answer</th>
					<th data-priority="7" width=120>Note Reviewer</th>
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
<style>
td.text-center{
	text-align:center;
}
td.text-score{
	text-align:center;
	font-size:15px;
	font-weight:bold;
}
td.text-width{
	width:100px;
	text-align:center;
}
.total-name{
	width:250px;
	pointer-events: none;
}
</style>
@endsection

@section('scripts')
<script type="text/javascript">	
let global_task_cycle = [];
let global_date_start = null;
let global_date_end = null;

$(document).ready(function(){
	
	get_task();
	daterange();
	global_task_cycle = [
		{
			id: 'Daily',
			text: 'Daily'
		},
		{
			id: 'Weekly',
			text: 'Weekly'
		},
		{
			id: 'Monthly',
			text: 'Monthly'
		},
		{
			id: 'Yearly',
			text: 'Yearly'
		},
	];
	
	$('#cycle_search').prepend('<option></option>').select2({
		placeholder: "Select Cycle ...",
		data: global_task_cycle,
		allowClear: true,
	});
	
}); 

$(function() {
	$('#daterange').on('cancel.daterangepicker', function(ev, picker) {
    	$(this).val('');
		global_date_start = '';
		global_date_end = '';
  	});
});

function daterange(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(7, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
	global_date_start = start;
	global_date_end = end;
	// console.log(global_date_start, global_date_end);
    $('#daterange').daterangepicker({
        uiLibrary: 'bootstrap4',
        autoApply: false,
        opens: 'center',
        locale: {
            format: 'YYYY-MM-DD',
            separator: separator,
            closeText: 'Clear',
        },
        startDate: start, 
        endDate: end,
    }, function(start, end, label) {
        $("#startdate").val(start.format('YYYY-MM-DD'));
        $("#enddate").val(end.format('YYYY-MM-DD'));
		global_date_start = start.format('YYYY-MM-DD');
		global_date_end = end.format('YYYY-MM-DD');
    });

    if($("#startdate").val()=='' || $("#enddate").val()==''){
        $("#startdate").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#enddate").val(moment().format('YYYY-MM-DD'));
		global_date_start = moment().subtract(7, 'days').format('YYYY-MM-DD');
        global_date_end = moment().format('YYYY-MM-DD');
    }
	// console.log(global_date_start, global_date_end)
}

$(document).on('click', '#search', function () {
    get_datatable();
	
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

const get_tot_score = async (myData) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/my_score/get_tot_score') ?>',
			data : {myData:myData},
            dataType: 'json',
			beforeSend: function () {
				$('#load_id_score').show();
			},
            success: function (res) {
				$('#tot_score').html('Total Score  :  '+res[0]+' ('+res[1]+' %)');
				$('#tot_score_all').html('Total Score  :  '+res[0]+' ('+res[1]+' %)');
            },
			complete: function(){
				$('#load_id_score').hide();
			},
        });
        return result;
    } catch (error) {
     //   get_task();
    }	
}

const get_datatable = async () => {	
	$(".div_datatable").show();
//	let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
	
/*	let statusList = {
		text: '<div align="center" style="font-size:15px;font-weight:bold;border:1px solid #ddd;border-radius:5px;padding:5px 10px 5px 10px;"><span id="tot_score_all"><span id="load_id_score" class="spinner-border spinner-border-sm" style="display:none;"></span></span></div>',
		className: 'total-name',
	 }
*/	 
//	dtButtons.push(statusList);
	
	let myData = {
		task_search: $('#task_search').val(),
		cycle_search: $('#cycle_search').val(),
		start_date: global_date_start,
		end_date: global_date_end,
	};	
	
	$('#missions_table').append('<tfoot><tr style="font-size:17px;"><th data-priority="1" colspan="14" style="text-align:right !important;" id="tot_score"></th></tr></tfoot>')
	var table = $('#missions_table').DataTable({
	//	buttons: dtButtons,
		destroy:true,
		responsive: true,
		processing: true,
		pageLength: 5,
		ajax: {
			url: "{{ route('myscore.index') }}",
			data : {myData:myData},
			error: function (jqXHR, textStatus, errorThrown) {
			//  $('#missions_table').DataTable().ajax.reload();
			},
			complete: function(){
			//	get_tot_score(myData);
			},
			
		  },	
		columns: [
		  {
			defaultContent: '',
			orderable: false, className: 'text-center'
		  },
		  {   
			data: 'id_task_activity_answer',
			defaultContent: '',
			orderable: false
		  },
		  { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center dis'},
		  { data: 'task', name: 'task' },
		  { data: 'activity', name: 'activity' },
		  { data: 'target_evidence', name: 'target_evidence' },
		  { data: 'text_type', name: 'text_type', className: 'text-center'},
		  { data: 'task_cycle', name: 'task_cycle', className: 'text-center'},
		  { data: 'start_date', name: 'start_date', className: 'text-width'},
		  { data: 'end_date', name: 'end_date', className: 'text-width'},
		  { data: 'completion_date', name: 'completion_date', className: 'text-width'},
		  { data: 'maximum_score', name: 'maximum_score', className: 'text-score'},
		  { data: 'score_answer', name: 'score_answer', className: 'text-score'},
		  { data: 'note_rejected', name: 'note_rejected'}
		],
		buttons: [
			{
				extend: 'excel',
				className: 'btn btn-sm btn-success',
				text: 'Download Excel',
				exportOptions: {
				  columns: [2,3,4,5,6,7,8,9,10,11,12,13]
				}
			},
			{
				text: '<div align="center" style="font-size:15px;font-weight:bold;border:1px solid #ddd;border-radius:5px;padding:5px 10px 5px 10px;"><span id="tot_score_all"><span id="load_id_score" class="spinner-border spinner-border-sm" style="display:none;"></span></span></div>',
				className: 'total-name',
			},
		],
		"fnInitComplete": function (oSettings) {
			get_tot_score(myData);
			$('#missions_table').find("tbody tr td.dtr-control").attr('onclick','det()');
			
		}
    });
}

function det(){
	setTimeout(function () {
			$('#missions_table > tbody > tr.child > td.child > ul > li').addClass('row');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-title').addClass('col-2');
			$('#missions_table > tbody > tr.child > td.child > ul > li > span.dtr-data').addClass('col');
	}, 500);
}

const get_task = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/my_score/get_task') ?>',
            dataType: 'json',
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
				$('#task_search').prepend('<option></option>').select2({
					placeholder: "Select Task ...",
					data: res,
					allowClear: true,
				});
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
     //   get_task();
    }	
}
</script>  
@endsection