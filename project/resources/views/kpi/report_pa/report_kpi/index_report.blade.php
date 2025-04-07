@extends('adminlte::page')
@section('title', 'Report KPI')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Report KPI (Quantitative)</h5>               
            </div>
       
			<div class="card-body">              
			<form method="post" id="genForm">
				{{ csrf_field() }}
					   <div class="form-group row">	
							<div class="col-sm-6">
								<select id="emp_participant" name="emp_participant[]" class="form-control form-control-md select2" multiple="multiple" style="width: 100%;"></select>
							</div>
							<div class="col-sm-3">
								<select id="period" name="gen_period" class="form-control form-control-md select2" style="width: 100%;"></select>
								<span class="invalid-feedback" role="alert" id="periodError">
                                        <strong></strong>
                                </span>
							</div>
							<div style="flex: 0 0 8%;max-width: 8%;margin-left:10px;">
								<button onclick="return false;" id="search_report" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
							</div>
							<div style="flex: 0 0 15%;max-width: 15%;">
								<button type="button" id="download" class="btn btn-sm btn-success"><i class="fas fa-download" ></i> Export Score KPI</button>
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
	td.text-middle{
		vertical-align:middle;
		text-align:center;
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
	$.getJSON('<?= url('kpi/report_pa/report_kpi/get_period_report') ?>', function (data) {
			$('#period').select2({
                placeholder: "Select Period",
                data: data
            });
		}).fail(function (data) { // Call failed
            get_period_report();
        });	
}
function get_employee_filter() {
	$.getJSON('<?= url('kpi/report_pa/report_kpi/get_employee_filter') . '?id_url=' ?>' + global_url_server, function (data) {
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

$(document).on('click', '#download', function () {
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
    };
	$(".invalid-feedback").children("strong").text("");
    $("#genForm input").removeClass("is-invalid");
	$.ajax({
		headers: {
			Accept: "application/json",
		},
		url:"{{ route('report_kpi.export_validate') }}",
		data:myData,
		dataType:'json',
		beforeSend: function () {
			$('#loader').removeClass('hidden');		
		},
		success:function(msg){ 
			let param = objectToQueryString(msg);
			let url = "{{ url('kpi/report_kpi/export') }}";
			window.open(url+'?'+param, '_blank');
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
    };
	
    let t = $('#report_table').DataTable({
    //    rowsGroup: [7,8],
		processing: true,
        responsive: true,
        destroy: true,
		dom:'lfWrtip<"clear">',
       ajax: {
            url: "<?= url('kpi/report_pa/report_kpi/') ?>",
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
                data: 'id_kpi_group',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No.', orderable: false},
            { data: 'penilai', title: 'Atasan', responsivePriority: 2,},
            { data: 'dinilai', title: 'Employee', responsivePriority: 1},
            { data: 'average_prosentase', title: 'Total Score(%)', responsivePriority: 3, className: 'text-score', },
			{ data: 'jan', title: 'Jan', className: 'text-middle' },
			{ data: 'feb', title: 'Feb', className: 'text-middle' },
			{ data: 'mar', title: 'Mar', className: 'text-middle' },
			{ data: 'apr', title: 'Apr', className: 'text-middle' },
			{ data: 'mei', title: 'Mei', className: 'text-middle' },
			{ data: 'jun', title: 'Jun', className: 'text-middle'},
			{ data: 'jul', title: 'Jul', className: 'text-middle' },
			{ data: 'ags', title: 'Ags', className: 'text-middle' },
			{ data: 'sep', title: 'Sep', className: 'text-middle' },
			{ data: 'okt', title: 'Okt', className: 'text-middle' },
			{ data: 'nov', title: 'Nov', className: 'text-middle' },
			{ data: 'des', title: 'Des', className: 'text-middle',orderable: true, searchable: true },
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
	
}  

</script>
@endsection