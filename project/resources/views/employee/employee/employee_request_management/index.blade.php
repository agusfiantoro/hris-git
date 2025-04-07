@extends('adminlte::page')
@section('title', 'Employee Request Management')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Request Management</h5>
                <div class="card-tools">
                    <button onclick="return false;" id="show_attachment" class="btn btn-sm btn-primary"><i class="fas fa-document"></i> Request Attachment</button>
                </div>
            </div>
            <div class="card-body">
            	<div class="form-group row">              
					<!-- <label class="col-md-2 col-form-label">Show by date type :</label> -->
					<div class="col-md-5">
						<select id="employee_search" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
					</div>
					<div class="col-md-2">
						<select id="date_type" class="form-control form-control-sm select2" style="width: 100%;"></select>
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
					<div class="col-sm-2">
						<button onclick="return false;" id="search_created" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>										
	            </div>
	            <br>

				<div class="row">
					<div class="col-md-12">
						<div class="row">	
							<div class="col-md-12">
								<select name="req_report" id="req_report" class="form-control form-control-md select2" style="width:100%;">
								</select>							
							</div>
						</div>
					</div>
				</div>
				<br>	  

			 <button id="adv" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
			<div id="range_date" class="col-md-12" style="display:none;">
				<div class="card card-danger card-outline card-outline-tabs">
				  <div class="card-header p-0 border-bottom-0">
					<ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
					  <li class="nav-item">
						<a class="nav-link active" id="custom-tabs-four-start-tab" data-toggle="pill" href="#custom-tabs-four-start" role="tab" aria-controls="custom-tabs-four-start" aria-selected="true">Filter Request Start Date</a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="custom-tabs-four-end-tab" data-toggle="pill" href="#custom-tabs-four-end" role="tab" aria-controls="custom-tabs-four-end" aria-selected="false">Filter Request End Date</a>
					  </li>
					  <li class="nav-item">
						<a class="nav-link" id="custom-tabs-four-created-tab" data-toggle="pill" href="#custom-tabs-four-created" role="tab" aria-controls="custom-tabs-four-created" aria-selected="false">Filter Created Date</a>
					  </li>
					</ul>
				  </div>
				  <div class="card-body">
					<div class="tab-content" id="custom-tabs-four-tabContent">
					  <div class="tab-pane fade show active" id="custom-tabs-four-start" role="tabpanel" aria-labelledby="custom-tabs-four-start-tab">
						 <div class="row">
								<div class="col-md-6">
									<div class="row">
										<label class="col-sm-4 col-form-label">Min Request Start Date:</label>
										 <div class="col-sm-8">
											<input type="text" id="min_start" name="min_start">
										 </div>
									</div>
								</div> 
								<div class="col-md-6">
									<div class="row">
										 <label class="col-sm-4 col-form-label">Max Request Start Date:</label>
										 <div class="col-sm-8">
											<input type="text" id="max_start" name="max_start">
										 </div>
									</div>
								</div> 
							</div>  
						</div>
					  <div class="tab-pane fade" id="custom-tabs-four-end" role="tabpanel" aria-labelledby="custom-tabs-four-end-tab">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<label class="col-sm-4 col-form-label">Min Request End Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="min_end" name="min_end">
									 </div>
								</div>
							</div> 
							<div class="col-md-6">
								<div class="row">
									 <label class="col-sm-4 col-form-label">Max Request End Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="max_end" name="max_end">
									 </div>
								</div>
							</div> 
						</div>
					  </div>
					  <div class="tab-pane fade" id="custom-tabs-four-created" role="tabpanel" aria-labelledby="custom-tabs-four-created-tab">
						<div class="row">
							<div class="col-md-6">
								<div class="row">
									<label class="col-sm-4 col-form-label">Min Created Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="min_created" name="min_created">
									 </div>
								</div>
							</div> 
							<div class="col-md-6">
								<div class="row">
									 <label class="col-sm-4 col-form-label">Max Created Date:</label>
									 <div class="col-sm-8">
										<input type="text" id="max_created" name="max_created">
									 </div>
								</div>
							</div> 
						</div>
					  </div>
					 
					</div>
				  </div>
				  <!-- /.card -->
				</div>
			</div>

                <table id="employee_request_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th>No</th>
                            <th>Reference Number</th>
                            <th>Request Cancel</th>
                            <th>NIK</th>
                            <th>Employee Name</th>
							<th>Created Date</th>
                            <th>Request Type</th>
                            <th>Leave Type</th>
                            <th>Overtime Type</th>
                            <th>Request Start Date</th>
                            <th>Request End Date</th>
                            <th>Days Type</th>
							<th>Qty Days</th>
                            <th>Note Rejected</th>
                            <th>Note Revised</th>
                            <th>Note</th>
                            <!-- <th>Attachment</th> -->
                            <th>Approval Status</th>
                            <th>Employee Status</th>
                            <th style="text-align:center;" data-priority="1">Action</th>
							<!-- <th></th> -->
                        </tr>
                    </thead>
                </table>
				
            </div>
        </div>
    </div>
</div>

<div id="attachmentModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Attachment</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body card">
                <div class="form-group row">
                    <label class="col-sm-1 col-form-label">Employee :</label>
                    <div class="col-sm-5">
                        <select id="employee" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>

                    <label class="col-sm-2 col-form-label">Reff Number :</label>
                    <div class="col-sm-4">
                        <select id="reff_number_attachment" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button onclick="return false;" id="search_attachment" class="btn btn-lg btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
                <div class="div_datatable" style="display:none; overflow-x: scroll;"> 
                    <button onclick="return false;" class="btn btn-default advanced_attachment">Advanced Search</button><br><br>
                    <table id="attachment_table" class="table table-striped table-bordered table-hover datatable"></table>
                </div>
            </div>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modal_view_approval"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">    
				<div class="modal-header">
                    <h5 class="modal-title">List Approver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                   
					<div class="row">                                      
						<div class="col-12">
							<table style="width:100%;" id="table_approve_status_detail" class="responsive table table-striped table-bordered table-hover datatable">
								<thead>
									<tr>
										<th data-priority="4" style="white-space:nowrap;">No.</th>
										<th style="white-space:nowrap;">Sequence</th>
										<th data-priority="3" style="white-space:nowrap;">Approval Name</th>
										<th data-priority="2" style="white-space:nowrap;">Approval Status</th>                                                       
										<th data-priority="1" align="center" style="width:50px;">Approval Execute</th>                                                       
									</tr>
								</thead>
							  
							</table>
						</div>
					</div>
                </div>
				 <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>            
                </div>
		</div>
    </div>
</div>


@endsection
@section('css')
<style type="text/css">
.dtfc-fixed-left{
	z-index:10;
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
var filter_column = '_all';
var minStartDate, maxStartDate;
var minEndDate, maxEndDate;
var minCreatedDate, maxCreatedDate;

let list_date_type = [
    { id: 'creation_date', text: 'Created Date' },
    { id: 'request_start_to', text: 'Request Start Date' },
    { id: 'request_end_to', text: 'Request End Date' },
];

$('#date_type').select2({
    placeholder: "Select Date Type",
    data: list_date_type,
});


function daterange(startdate='', enddate='') {
    let separator = '   to   ';
    let start = (startdate=='' || startdate==null) ? moment().subtract(7, 'days').format('YYYY-MM-DD') : startdate;
    let end = (enddate=='' || enddate==null) ? moment().format('YYYY-MM-DD') : enddate;
    $('#daterange').daterangepicker({
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
        $("#startdate").val(moment().subtract(7, 'days').format('YYYY-MM-DD'));
        $("#enddate").val(moment().format('YYYY-MM-DD'));
    }
}

function get_data_custom(){
	$.getJSON('<?= url('employee/employee_setting/custom_report/get_data_custom') . '?address=' ?>' + global_url_server, function (data) {		
		$('#req_report').prepend('<option selected></option>').select2({
			placeholder: "Select Group Column ...",
			allowClear: true,
			data: data,
			}).on('change', function (e) {
			//	console.log($(this).select2('data')[0].id);				
			if($(this).select2('data')[0].id != ''){
				fil_col = '['+$(this).select2('data')[0].detail_column+']';	
				filter_column = JSON.parse(fil_col);
			}
			else{
				filter_column = '_all';
			}
				$('#employee_request_table').DataTable().destroy();			
			//	filter_column = '2,3';
				get_datatable();
			});	
		}).fail(function (data) { // Call failed
			get_data_custom();
		});		
}

$(document).on('click', '#search_created', function () {
    get_datatable();
});

$(document).on('click', '.view', function(){
		let id_request_header = $(this).attr('id');
		$.ajax({
				url: "<?= url('employee/employee/employee_request/get_request_edit') ?>",
                method: "GET",
                data: {id_request_header: id_request_header},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
					$.extend( true, $.fn.dataTable.defaults, {
						 columnDefs:false,
						 paging:false,
						 searching:false,
						 destroy: true,
						 dom: '<"toolbar">frtip',
						} );
	
						$('#table_approve_status_detail').DataTable({
					//	processing: true,
						ajax: {
							url: "<?= url('employee/employee/employee_request/index_status').'?id_request_header='?>"+response.id_request_header+"<?= '&id_approval='?>"+response.id_approval,							
						},
						columns: [
					
							{data: 'DT_RowIndex', name: 'DT_RowIndex'},
							{data: 'sequence', name: 'sequence'},
							{data: 'name', name: 'name'},
							{data: 'code', name: 'code'},
							{data: 'execute', name: 'execute'},
						]
					});
				},
				 complete: function(){
					$('#loader').addClass('hidden');
				},
				error: function (xhr) {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! [' + JSON.stringify(xhr).substr(0, 500) + ']'
                    });
                }
            });
	$('#modal_view_approval').modal('show');
 });

		
function get_datatable(){
		let myData = {
			employee: $("#employee_search").val() == '' ? null : $("#employee_search").val(),
			startdate: $("#startdate").val() == '' ? null : $("#startdate").val(),
			enddate: $("#enddate").val() == '' ? null : $("#enddate").val(),
        	date_type: $("#date_type").children("option:selected").val()
		};

		var t = $('#employee_request_table').DataTable({
            processing: true,
            destroy:true,
		//	columnDefs:false,	
			 columnDefs: [
					{ targets: filter_column, visible: true},
					{ targets: '_all', visible: false },					 
				],
			select: {
			  style:    'multi+shift',
			  selector: 'td:nth-child(1)'
			},
			scrollX: true,
			scrollCollapse: true,
			fixedColumns: {
				left: 5,
			},
			pageLength: 10,
			lengthMenu: [
					[10, 20, 30, 50, 100, 200, -1],
					[10, 20, 30, 50, 100, 200, 'All']
				],
            ajax: {
            	"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            	"data": myData,
               	url: "<?= url('employee/employee/employee_request_management') . '?id_url=' ?>" + global_url_server,
				error: function (jqXHR, textStatus, errorThrown) {
					$('#employee_request_table').DataTable().ajax.reload();
				}
            },
		/*	rowCallback: function(row, data, index){
				if(data['code_app_status'] == 'Approved'){
					$(row).find('td:eq(16)').css('background', '#90fca4');
				}
			  },
		*/
            columns: [
                {   // Checkbox select column
                data: 'id_request_header',
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
                {data: 'reference_number', name: 'reference_number'},
                {data: 'request_cancel', name: 'request_cancel'},
                {data: 'nik_employee', name: 'nik_employee'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'creation_date', name: 'creation_date'},
                {data: 'desc_request_type', name: 'desc_request_type'},
                {data: 'desc_leave_type', name: 'desc_leave_type'},
                {data: 'desc_overtime_type', name: 'desc_overtime_type'},
                {data: 'request_start_to', name: 'request_start_to', render: function ( data, type, row ){
					if(row['desc_request_type'] =='Attendance Correction'){
						return data;
					}
					else{
						if(data != null){
							var date_leave = data.split(' ')[0];
							return date_leave;
						} else {
							return data;
						}
					}
				}},
				{data: 'request_end_to', name: 'request_end_to', render: function ( data, type, row ){
					if(row['desc_request_type'] =='Attendance Correction'){
						return data;
					}
					else{
						if(data != null){
							var date_leave = data.split(' ')[0];
							return date_leave;
						} else {
							return data;
						}
					}
				}},
                {data: 'day_type', name: 'day_type'},
				{data: 'qty_days', name: 'qty_days'},
                {data: 'note_rejected', name: 'note_rejected'},
                {data: 'note_revised', name: 'note_revised'},
            //    {data: 'actual_start_to', name: 'actual_start_to'},
             //   {data: 'actual_end_to', name: 'actual_end_to'},
                {data: 'note', name: 'note'},
    //             {data: 'attachment', render: function ( data, type, row ) {	
	   //              	let _return = '';
	   //              	if(row.attachment_type != null){
	   //                      if(row.attachment_type == 'image'){
	   //                          _return = '<a download="'+Date.now()+'.jpg" href="data:image;base64,'+ row.attachment + '">Download File</a>';
	   //                      }
	   //                      else if(row.attachment_type == 'pdf'){
	   //                          _return = '<a download="'+Date.now()+'.jpg" href="data:application/pdf;base64,'+ row.attachment + '">Download File</a>';
	   //                      }  
	   //                  } else {
	   //                      if(row.attachment != null){
	   //                      	_return = '<a target="_blank" href="'+ row.attachmentFile + '">Download File</a>';
	   //                      }
	   //                  }
	   //                  return _return;
				// 	} 
				// },
                {data: 'desc_app_status', name: 'desc_app_status'},
                {data: 'employee_status', name: 'employee_status'},
				{data: 'action', name: 'action'},
				// {
    //             defaultContent: '',
				// orderable: false,
				// }
            ],
			
			"fnInitComplete": function (oSettings) {
			   $('#employee_request_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
			   $('#employee_request_table_wrapper .column-filter-widget:eq(18)').find("select option:contains('A')").attr('selected','selected').change();
			}
        });
	
	t.on('order.dt search.dt', function () {
        let i = 1;
        t.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
	
}		
$(document).ready(function(){
	$('#adv').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
			// $("#range_date").show("slow");
		}
		else {
			$("#cf").hide("slow");
			// $("#range_date").hide("slow");
		}		
	});
	daterange();
	get_data_custom();
	get_datatable();
	getEmployeeByAccessGroup().then(function(value) {
        $('#employee_search').html('');
        $('#employee_search').select2({
            placeholder: "Select Employee",
            data: value,
            allowClear: true,
        });
    });
	 
});
$(document).on('click', '#show_attachment', function () {
    loadAttachment().then(result => {
    	let employeeRequest = result.employeeRequest;
		let reffNumber = result.reffNumber;
		$('#employee').empty();
		$('#reff_number_attachment').empty();
		$('#employee').select2({
		    placeholder: "Cari Karyawan",
		    data: employeeRequest,
		    allowClear: true,
		});
		$('#reff_number_attachment').select2({
		    placeholder: "Cari Nomor Reff",
		    data: reffNumber,
		    allowClear: true,
		});
    	$("#attachmentModal").modal('show');
    });
});
$(document).on('click', '#search_attachment', function () {
    $("#attachment_table").html("");
    get_datatable_attachment()
});
$(document).on("click", ".advanced_attachment", function () {
    $('.cf').select2({width:'100%'});
    if($(".attachment_table").css('display') == 'none'){
        $(".attachment_table").show("slow");
    }
    else {
        $(".attachment_table").hide("slow");
    }   
});
const get_datatable_attachment = async () => {
    $(".div_datatable").show();
    let myData = {
        nik: $("#employee").val() == '' ? null : $("#employee").val(),
        id_request_header: $("#reff_number_attachment").val() == '' ? null : $("#reff_number_attachment").val(),
    };
    let t = $('#attachment_table').DataTable({
        processing: true,
        serverSide: false,
        // responsive: true,
        destroy: true,
        ajax: {
            url: "<?= url('employee/employee/employee_request_management/attachment') ?>",
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
        },
        columns: [
            {   // Checkbox select column
                data: '',
                defaultContent: '',
                orderable: false
            },
            {   // Checkbox select column
                data: 'id_request_header',
                defaultContent: '',
                orderable: false
            },
            { data: 'DT_RowIndex', title: 'No', orderable: false},
            { data: 'reference_number', title: 'Reff Number'},
            { data: 'nik_employee', title: 'NIK'},
            { data: 'name', title: 'Name'},
            { data: 'creation_date', title: 'Created at'},
            { data: 'request_type', title: 'Request Type'},
            { data: 'approval_status', title: 'Approval Status'},
            { data: 'attachment', title: 'Attachment', orderable: false, render: function ( data, type, row ) { 
                    let file_attachment = '';
                    if(row.attachment != null){
                        if(row.attachment_type == null){
                            let storage_path = "<?= url('project/storage/app/public/upload/employee_request') ?>";
                            file_attachment =  `<a href="${storage_path}/${row.attachment}" target="_blank" class="btn btn-xs btn-primary">Download</a>`;
                        } else {
                            const type = {
                                'pdf' : {
                                    'data' : 'data:application/pdf;base64,'+row.attachment, 
                                    'name' : `${row.reference_number}.pdf`
                                },
                                'image' : {
                                    'data' : 'data:image;base64,'+row.attachment, 
                                    'name' : `${row.reference_number}.jpg`
                                },
                            };
                            file_attachment =  `<a download="${type[row.attachment_type]['name']}" href="${type[row.attachment_type]['data']}" class="btn btn-xs btn-primary">Download</a>`;
                        }
                    } 
                    return file_attachment;
                } 
            },
        ],
        lengthMenu: [
            [10, 20, 50, 100, 200, 1000, -1],
            [10, 20, 50, 100, 200, 1000, 'All']
        ],
        "fnInitComplete": function (oSettings) {
            $('#attachment_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
            $('#attachment_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
            $('#attachment_table_wrapper .column-filter-widget:eq(9)').html('');
        },
    });
}  

const loadAttachment = async () => {
    let result;
    try {
    	let myData = {
			startdate: $("#startdate").val() == '' ? null : $("#startdate").val(),
			enddate: $("#enddate").val() == '' ? null : $("#enddate").val(),
        	date_type: $("#date_type").children("option:selected").val()
		};
        result = await $.ajax({
            type: 'GET',
            data: myData,
            url: "<?= url('employee/employee/employee_request_management/load_attachment') ?>",
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (result) {
            }
        });
        return result;
    } catch (error) {
        loadAttachment();
    }
}

async function getEmployeeByAccessGroup() {
    let status = ['A','I'];
  	let currentPath = "<?= \Request::path() ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('employee/get_employee_by_status_and_access_group') ?>'+'?status='+status+'&path_menu='+currentPath, function (res) { 
        });
        return result;
    } catch (error) {
        getEmployeeByAccessGroup();
    }
}
</script>
@endsection