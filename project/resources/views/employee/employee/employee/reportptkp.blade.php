<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-body" id="st">
			<div class="row">
				<div class="col-md-4">
					 <div class="row">
						<label class="col-sm-4 col-form-label">Category Date :</label>
						<div class="col-sm-8">
							<select name="cat_date" id="cat_date" class="form-control form-control-sm">
								<option value="date_change_ptkp">Update Date PTKP</option>
								<option value="creation_date">Created Date</option>
							</select>
						</div>
					</div>
				</div>
				<div class="col-md-1">
				</div>
				<div class="col-md-5">
					<div class="form-group row">              
						<label class="col-sm-3 col-form-label">Start to End :</label>
						<div class="col-md-8">
							<div class="input-group">
								<input name="daterange" id="daterange_search" type="daterange" class="form-control form-control-sm" style="width:100%;" />
								<input name="startdate" id="startdate" class="form-control form-control-sm" hidden>
								<input name="enddate" id="enddate" class="form-control form-control-sm" hidden>
								<div class="input-group-append">
									<span class="input-group-text far fa-calendar form-control-sm"></span>
								</div>
							</div>
						</div>														
					</div>
				</div>
				<div class="col-sm-2">
					<button onclick="return false;" id="search_created" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
				</div>	
			</div>
			<div class="div_datatable" style="display:none;"> 
				<button onclick="return false;" class="btn btn-default pull-left advanced_history">Advanced Search</button>
						<br>
						<br>
				<div class="col-md-12" style="overflow: auto">
					<table id="report_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
						<thead>
							<tr>				   
								<th class="dtfc-fixed-left"></th>
								<th class="dtfc-fixed-left">No</th>
								<th class="dtfc-fixed-left">NIK</th>
								<th class="dtfc-fixed-left">Name</th>
								<th>Gender</th>
								<th>NPWP</th>
								<th>Join Date</th>
								<th>Position</th>
								<th>Branch</th>
								<th>Resign Date</th>
								<th>PTKP Status</th>
								<th>Update Date</th>
								<th>Created Date</th>
								<th>Status</th>
								<th></th>
							</tr>
						</thead> 
						<tbody>
						</tbody>
					</table>
				</div>
            </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">
.dtfc-fixed-left{
	z-index:10;
}
</style>

<script type="text/javascript">
$(document).ready(function(){
	$('#cat_date').select2({width:'100%'});
	daterange();
});

$(document).on('click', '#search_created', function () {
    get_datatable();
});

$(document).on("click", ".advanced_history", function () {
    $('.cf').select2({width:'100%'});
    if($(".report_table").css('display') == 'none'){
        $(".report_table").show("slow");
    }
    else {
        $(".report_table").hide("slow");
    }   
});	

function daterange(startdate='', enddate='') {
        let separator = '   to   ';
        let start = (startdate=='' || startdate==null) ? moment().subtract(14, 'days').format('YYYY-MM-DD') : startdate;
        let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
        $('#daterange_search').daterangepicker({
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
            $("#startdate").val(start.format('YYYY-MM-DD'));
            $("#enddate").val(end.format('YYYY-MM-DD'));
        });

        if($("#startdate").val()=='' || $("#enddate").val()==''){
            $("#startdate").val(moment().subtract(14, 'days').format('YYYY-MM-DD'));
            $("#enddate").val(moment().format('YYYY-MM-DD'));
        }
	/*	setTimeout(function() { 
			$('#search_created').trigger('click');
		}, 1000);
	*/
}

const get_datatable = async () => {
	$(".div_datatable").show();
		let myData = {
			cat_date: $("#cat_date").val() == '' ? null : $("#cat_date").val(),
			startdate: $("#startdate").val() == '' ? null : $("#startdate").val(),
			enddate: $("#enddate").val() == '' ? null : $("#enddate").val(),
		};
		
		var table =	$('#report_table').DataTable({
            processing: true,
			destroy:true,
			columnDefs: [
					{ targets: '_all', visible: true },					 
				],	
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				left: 4,
			},		
			buttons: [
				  {
					 extend: 'excel',
					className: 'btn-default',
					title: "Report PTKP Status ( <?= session('company_name') ?> )",
					exportOptions: {
					  columns: ':visible'
					}
				  },
			],
			
			pageLength: 10,
			lengthMenu: [
					[10, 20, 30, 50, 100, 200, -1],
					[10, 20, 30, 50, 100, 200, 'All']
				],
            ajax: {
				url: "<?= url('employee/employee/employee/get_ptkp_report') . '?id_url=' ?>" + global_url_server,
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            },
		//rowsGroup: [2],
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
                {defaultContent: '',orderable: false},
                {data: 'nik_employee', name: 'nik_employee'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'gender', name: 'gender'},
                {data: 'npwp_number', name: 'npwp_number'},
                {data: 'join_date', name: 'join_date'},
                {data: 'position_routing', name: 'position_routing'},
                {data: 'branch', name: 'branch'},
                {data: 'resign_date', name: 'resign_date'},
                {data: 'ptkp_status', name: 'ptkp_status'},
                {data: 'date_change_ptkp', name: 'date_change_ptkp'},
                {data: 'creation_date', name: 'creation_date'},
                {data: 'status', name: 'status'},
				{
                defaultContent: '',
				orderable: false,
				}
            ],
			"fnInitComplete": function (oSettings) {
			   $('#report_table_wrapper .column-filter-widget:eq(11)').find("select option:contains('A')").attr('selected','selected').change();
			   $('#report_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
			}
        });
		
	table.on('order.dt search.dt', function () {
        let i = 1;
        table.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
	$('.cf').select2({width:'100%'});
		$("#st").children("#report_table_wrapper").children(".dataTables_scroll.dtfc-has-left").removeAttr("style");		
}


</script>
