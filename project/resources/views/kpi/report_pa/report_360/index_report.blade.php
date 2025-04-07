@extends('adminlte::page')
@section('title', 'Report 360 Feedback')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Report 360 Feedback (Qualitative)</h5>               
            </div>     
			<div class="card-body">              
			 <form method="post" id="genForm">
				{{ csrf_field() }}
					   <div class="form-group row">	
							<div class="col-sm-4">
								<select id="emp_participant" name="emp_participant[]" class="form-control form-control-md select2" multiple="multiple" style="width: 100%;"></select>
							</div>
							<div class="col-sm-3">
								<select id="period" name="gen_period" class="form-control form-control-md select2" style="width: 100%;"></select>
								<span class="invalid-feedback" role="alert" id="periodError">
                                        <strong></strong>
                                </span>
							</div>
							<div class="col-sm-5">
								<button onclick="return false;" id="search_report" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
								<button style="margin-left:5px;" type="button" id="download_detail" class="btn btn-sm btn-success pull-right"><i class="fas fa-download" ></i> Export Detail 360</button>
								<button type="button" id="download" class="btn btn-sm btn-success pull-right"><i class="fas fa-download" ></i> Export Score 360</button>
							</div>
					  </div>
			</form>	
			 <div class="div_datatable" style="display:none;"> 
                    <button onclick="return false;" class="btn btn-default advanced_report">Advanced Search</button><br><br>
					<div id="table_rep"></div>
                </div>
			</div>
        </div>
    </div>
</div>
@endsection
@section('css')
<style type="text/css">
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	
</style>
@stop
@section('scripts')
<script src="{{ asset('vendor/datatables/js/dataTables.rowsGroup.js') }}"></script>
<script type="text/javascript">
$(document).ready(function(){
	$(".advanced_report").hide();
	
	get_employee_filter();
	get_period_report();
});
	
function get_period_report() {
	$.getJSON('<?= url('kpi/report_pa/report_360/get_period_report') ?>', function (data) {
			$('#period').prepend('<option selected></option>').select2({
                placeholder: "Select Period",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_period_report();
        });	
}
function get_employee_filter() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_employee_filter') . '?id_url=' ?>' + global_url_server, function (data) {
			$('#emp_participant').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_employee_filter();
        });	
}

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

$(document).on('click', '#download,#download_detail', function () {
	let id_download = $(this).attr('id');
    let period = '';
    let employee = '';
    if($("#emp_participant").val().length > 0){
        employee = $("#emp_participant").val();
    }
    if($("#period").val() != ''){
        period = $("#period").val();
    }

    let myData = {
        period: period,
        employee: employee,
        id_url: global_url_server,
    };
	$(".invalid-feedback").children("strong").text("");
    $("#genForm input").removeClass("is-invalid");
	$.ajax({
		headers: {
			Accept: "application/json",
		},
		url:"{{ route('report_360.export_validate') }}",
		data:myData,
		dataType:'json',
		beforeSend: function () {
			$('#loader').removeClass('hidden');		
		},
		success:function(msg){ 
			let param = objectToQueryString(msg);
			if(id_download == 'download'){
				let url = "{{ url('kpi/report_360/export') }}";				
				window.open(url+'?'+param, '_blank');
			}
			else if(id_download == 'download_detail'){
				let url = "{{ url('kpi/report_360/export_detail') }}";	
				window.open(url+'?'+param, '_blank');
			}
		},
		error: function (msg) {
				let errors = msg.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					$("#" + key).addClass("is-invalid");
					$("#" + key + "Error").children("strong").text(errors[key][0]);
				});
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});
   
});


$(document).on('click', '#search_report', function () {
    $("#report_table").html("");
    get_datatable();
});

$(document).on("click", ".advanced_report", function () {
    $('.cf').select2({width:'100%'});
    if($(".report_table").css('display') == 'none'){
        $(".report_table").show("slow");
    }
    else {
        $(".report_table").hide("slow");
    }   
});

const get_datatable = async () => {
    $(".div_datatable").show();
    $(".advanced_report").show();

	$("#table_rep").html("");	
	$("#table_rep").html('<table id="report_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>');
	
    let myData = {
        id_employee: $("#emp_participant").val() == '' ? null : $("#emp_participant").val(),
        period: $("#period").val() == '' ? null : $("#period").val(),
	//	 global_url_server: global_url_server,
    };
    let t = $('#report_table').DataTable({
        rowsGroup: [8,9],
		processing: true,
        responsive: true,
        destroy: true, 
		dom:'lfWrtip<"clear">',
       ajax: {
            url: "<?= url('kpi/report_pa/report_360/') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
			error: function (jqXHR, textStatus, errorThrown) {
				$('#report_table').DataTable().ajax.reload();
			}
        },
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
                orderable: false
            },
            {   // Checkbox select column
                data: 'id_qualitative_appraisers',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No.', orderable: false},
            { data: 'period', title: 'Period', responsivePriority: 6},
            { data: 'penilai', title: 'Penilai', responsivePriority: 5},
            { data: 'appraisers_hierarchy', title: 'Type'},
            { data: 'dinilai', title: 'Dinilai', responsivePriority: 1},
            { data: 'subtotal_score', title: 'Score', responsivePriority: 4,},
            { data: 'total_score', title: 'Total Score', responsivePriority: 2, className: 'text-score', },
            { data: 'final_score', title: 'Final Score(%)', responsivePriority: 3, className: 'text-score'},
            { data: 'submitted', title: 'Submitted', className: 'text-score', render: function ( data, type, row ) { 
				if(row.submitted == true){
					return '<span class="badge badge-success">YES</span>';
				}
				else{
					return '<span class="badge badge-danger">NO</span>';
				}
			  }
			},
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
		"fnInitComplete": function (oSettings) {
		   $('#report_table_wrapper .dataTables_length').addClass('pull-left').change();
		   $('#report_table_wrapper .dataTables_filter').addClass('pull-right').change();
		}
    });	
	
	t.on('order.dt search.dt', function () {
        let i = 1;
        t.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();	
}  

</script>
@endsection