@extends('adminlte::page')
@section('title', 'Leaderboard')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Leaderboard</h5>
      </div>
      <div class="card-body">
		<div class="form-group row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-4">
						<div class="">
							<select id="month_search" class="form-control form-control-sm select2" style="width: 100%;" multiple="multiple"></select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="">
							<select id="emp_search" class="form-control form-control-sm select2" style="width: 100%;" multiple="multiple"></select>
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
			<table id="leaderboard_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr class="text text-center">
				<th data-priority="1"></th>
				<th data-priority="1"></th>
				<th data-priority="2" width=10>No</th>
				<th data-priority="3">Month</th>
				<th data-priority="6">Nama</th>
				<th data-priority="7">NIK</th>
				<th data-priority="8">Position</th>
				<th data-priority="9">Department</th>
				<th data-priority="10">Region</th>
				<th data-priority="11">Branch</th>
				<th data-priority="4">Score Total</th>
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
  .modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 9999;
    visibility: hidden;
  }
  .modal{
		overflow:auto !important;
	}
  .modal-body {
    position: relative;
  }
  .modal.show .modal-loading {
    visibility: visible;
  }

  td.text-center{
	text-align:center;
  }
  
  td.text-width{
	width:100px;
	text-align:center;
  }
  td.text-score{
	text-align:center;
	font-size:15px;
	font-weight:bold;
 }

td.dis{
	pointer-events:none;
}

table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control::before, table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control::before{
	left: 12px;
}
table.dataTable > tbody > tr.child span.dtr-title {
  width: 110px;
}

</style>
@endsection

@section('scripts')
<script type="text/javascript">	
$(document).ready(function(){
	get_month();
	get_search_emp();
	
}); 

const get_month = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('task_management/missions/leaderboard/get_month') ?>',
            dataType: 'json',
			beforeSend: function () {
			//	$('#month_search').empty();
			},
            success: function (res) {
				$('#month_search').prepend('<option></option>').select2({
					data: res,
					placeholder:'Select Month(s)',
					allowClear: true,
				});
            },
			complete: function(){
			},
        });
        return result;
    } catch (error) {
        get_month();
    }	
} 

const get_search_emp = async () => {
	let result;
    try {
        result = await $.ajax({
           url: "<?= url('task_management/missions/missions_review/get_search_emp') ?>",
            dataType: 'json',
			beforeSend: function () {
			},
            success: function (res) {
				$('#emp_search').prepend('<option></option>').select2({
					data: res,
					placeholder:'Select Employee(s)',
					allowClear: true,
				});
            },
			complete: function(){
				//$('#load_id_pos').hide();
			},
        });
        return result;
    } catch (error) {
     //   get_score(id_task_activity_answer);
    }	
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
	
const get_datatable = async () => {	
	$(".div_datatable").show();	
	var month_search = $('#month_search').val();
	var emp_search = $('#emp_search').val();
	
	$('#leaderboard_table').DataTable({
		destroy:true,
		responsive: true,
		processing: true,
		pageLength: 50,
		ajax: {
			url: "{{ route('leaderboard.index') }}",
			data : {
				month_search:month_search,
				emp_search:emp_search,	
			},
			error: function (jqXHR, textStatus, errorThrown) {
		//	  $('#leaderboard_table').DataTable().ajax.reload();
			}
		  },	
		columns: [
		  {
			defaultContent: '',
			orderable: false, className: 'text-center'
		  },
		  {
			data: 'DT_RowIndex',
			defaultContent: '',
			orderable: false, className: 'text-center'
		  },
		  { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center dis'},
		  { data: 'month', name: 'month' },
		  { data: 'name', name: 'name' },
		  { data: 'nik_employee', name: 'nik_employee' },
		  { data: 'pos', name: 'pos' },
		  { data: 'dept', name: 'dept' },
		  { data: 'region', name: 'region', className: 'text-width'},
		  { data: 'branch', name: 'branch', className: 'text-width'},
		  { data: 'score_total', name: 'score_total', className: 'text-score'},
		],
		"fnInitComplete": function (oSettings) {
			$('#leaderboard_table').find("tbody tr td.dtr-control").attr('onclick','det()');
			
		}
    });
	
}	




function det(){
	setTimeout(function () {
			$('#leaderboard_table > tbody > tr.child > td.child > ul > li').addClass('row');
			$('#leaderboard_table > tbody > tr.child > td.child > ul > li > span.dtr-title').addClass('col-2');
			$('#leaderboard_table > tbody > tr.child > td.child > ul > li > span.dtr-data').addClass('col');
	}, 500);
}
	
</script>  
@endsection