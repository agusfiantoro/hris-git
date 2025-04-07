<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-body">
				<div class="form-group row">              
					<label class="col-sm-3 col-form-label">Source Type :</label>
					<div class="col-sm-3">
						<select id="fil_type" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_grouping" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
					</div>										
				</div>
                <div class="div_datatable" style="display:none;"> 
                    <button onclick="return false;" class="btn btn-default advanced_review">Advanced Search</button><br><br>
                    <table id="review_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable"></table>
                </div>
            </div>
        </div>
    </div>
</div>
@section('css')
<style type="text/css">
th, td.dt-center{
	text-align: center;
}
.all-results-width {
	width:150px;
	text-align:center;
}
.dtfc-fixed-right {
	z-index:10;
}
</style>
@stop
<script type="text/javascript">
var links = {};
$(document).ready(function(){
	get_filter().then(function() {
	});
});

$(document).on('click', '#search_grouping', function () {
	$(".div_datatable").show();
	$(".advanced_review").show();
	get_datatable();
});

$(document).on("click", ".advanced_review", function () {
    $('.cf').select2({width:'100%'});
    if($(".review_table").css('display') == 'none'){
        $(".review_table").show("slow");
    }
    else {
        $(".review_table").hide("slow");
    }   
});

function get_datatable(){
	let myParam = {
			fil_type: $("#fil_type").val() == '' ? null : $("#fil_type").val(),
			id_url: global_url_server
		};
    
    let t = $('#review_table').DataTable({
        processing: true,
        responsive: false,
		pageLength: 50,
        destroy: true,
		columnDefs: false,
		scrollX: true,
		scrollCollapse: true,
		select: {
		  style:    'multi+shift',
		  selector: 'td:nth-child(1)'
		},
        ajax: {
            url: "{{ route('batch.list_review') }}",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
			data: myParam,
        },
		fixedColumns: {
			right: 1,
		},
	
        columns: [
           {   // Checkbox select column
			data: 'id_batch_participant',
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
            { data: 'DT_RowIndex', title: 'No', orderable: false},
            { data: 'batch_name', title: 'Batch Name'},
            { data: 'source_type', title: 'Source Type'},
            { data: 'participant_name', title: 'Name'},
            { data: 'nik', title: 'NIK / KTP'},
            { data: 'start_date', title: 'Start Date'},
            { data: 'end_date', title: 'Expired Date'},
			{ data: 'psycho_result', name: 'psycho_result', title: 'BCT', render(data, type, row) {
				if(row.psycho_result) {
					if(row.psycho_result.status_bct == 1) {
						let cell = '<span class="d-inline mr-1 badge badge-sm badge-success">Completed</span>';
						cell += `<a href="${row.psycho_result.bct_link}" class="d-inline btn btn-sm btn-success"><i class="fa fa-download"></i></a>`;
						return `<div style="width:100px;">${cell}</div>`;
					}
					return '<span class="badge badge-sm badge-danger">Not Completed</span>'
				}
				return 'NULL';
			}},
			{ data: 'psycho_result', name: 'psycho_result', title: 'DISC', render(data, type, row) {
				if(row.psycho_result) {
					if(row.psycho_result.status_disc == 1) {
						let cell = '<span class="d-inline mr-1 badge badge-sm badge-success">Completed</span>';
						cell += `<a href="${row.psycho_result.disc_link}" class="d-inline btn btn-sm btn-success"><i class="fa fa-download"></i></a>`;
						return `<div style="width:100px;">${cell}</div>`;
					}
					return '<span class="badge badge-sm badge-danger">Not Completed</span>'
				}
				return 'NULL';
			}},
			{ data: 'psycho_result', name: 'psycho_result', title: 'Kraepelin', render(data, type, row) {
				if(row.psycho_result) {
					if(row.psycho_result.status_kraepelin == 1) {
						let cell = '<span class="d-inline mr-1 badge badge-sm badge-success">Completed</span>';
						cell += `<a href="${row.psycho_result.kraepelin_link}" class="d-inline btn btn-sm btn-success"><i class="fa fa-download"></i></a>`;
						return `<div style="width:100px;">${cell}</div>`;
					}
					return '<span class="badge badge-sm badge-danger">Not Completed</span>'
				}
				return 'NULL';
			}},
			{ data: 'psycho_result', name: 'psycho_result', title: 'Papikostick', render(data, type, row) {
				if(row.psycho_result) {
					if(row.psycho_result.status_papi == 1) {
						let cell = '<span class="d-inline mr-1 badge badge-sm badge-success">Completed</span>';
						cell += `<a href="${row.psycho_result.papi_link}" class="d-inline btn btn-sm btn-success"><i class="fa fa-download"></i></a>`;
						return `<div style="width:100px;">${cell}</div>`;
					}
					return '<span class="badge badge-sm badge-danger">Not Completed</span>'
				}
				return 'NULL';
			}},
			{ data: 'psycho_result', name: 'download_psycho_result', className: 'all-results-width', title: 'All Results', render(data, type, row) {
				if(row.psycho_result) {
					let linksSet = {};
					linksSet.index = row.DT_RowIndex;
					linksSet.source = row.source_type;
					let show = false;
					if(row.psycho_result.status_papi == 1) {
						linksSet.papi = row.psycho_result.papi_link.replaceAll('&amp;', '&');
						show = true;
					}
					if(row.psycho_result.status_bct == 1) {
						linksSet.bct = row.psycho_result.bct_link.replaceAll('&amp;', '&');
						show = true;
					}
					if(row.psycho_result.status_disc == 1) {
						linksSet.disc = row.psycho_result.disc_link.replaceAll('&amp;', '&');
						show = true;
					}
					if(row.psycho_result.status_kraepelin == 1) {
						linksSet.kraepelin = row.psycho_result.kraepelin_link.replaceAll('&amp;', '&');
						show = true;
					}
					links[row.DT_RowIndex] = linksSet;
					let cell = show ? `<button type="button" onclick="downloadAll(${row.DT_RowIndex})" class="d-inline btn btn-sm btn-success"><i class="far fa-file-archive-o fa-lg"></i></button>` : '';
					return `<div><center>${cell}</center></div>`;
				}
				return 'NULL';
			}}
        ],
		"fnInitComplete": function (oSettings) {
            $('#review_table_wrapper .dt-center').css('text-align','center').change();
        },
    });
	
}

function downloadAll(index) {
	list = links[index];
	let url = "{{route('master_batch.download_zip')}}?";
	url += $.param({list:list});
	window.open(url, '_new');
	return;
}
  
</script>
