<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-body" id="st">
			<div class="row">
				<div class="col-md-12">				
					<select name="req_report" id="req_report" class="form-control form-control-md select2" style="width:100%;"></select>													
				</div>
			</div>
				<br>	  
			<button id="adv" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
			<div class="row">
				<div class="col-md-5">
					<div class="row">
						<label class="col-sm-3 col-form-label">Min Join Date:</label>
						 <div class="col-sm-8">
							<input type="text" id="min" name="min">
						 </div>
					</div>
				</div> 
				<div class="col-md-5">
					<div class="row">
						 <label class="col-sm-3 col-form-label">Max Join Date:</label>
						 <div class="col-sm-8">
							<input type="text" id="max" name="max">
						 </div>
					</div>
				</div> 
			</div> 
				<div class="col-md-12" style="overflow: auto">
                <table id="report_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th class="dtfc-fixed-left"></th>
                            <th class="dtfc-fixed-left">No</th>
                            <th class="dtfc-fixed-left">NIK</th>
                            <th class="dtfc-fixed-left">Name</th>
                            <th>Email</th>
                            <th>Join Date</th>
                            <th>ID Number</th>
                            <th>Home Address</th>
                            <th>KTP Address</th>
                            <th>KTP Attachment</th>
                            <th>KK Attachment</th>
                            <th>Point of Recruit</th>
                            <th>Gender</th>
                            <th>Marital</th>
                            <th>Religion</th>
                            <th>Date Of Birth</th>
                            <th>Place Of Birth</th>
                            <th>PTKP Status</th>
                            <th>Number Of Children</th>
                            <th>NPWP</th>
                            <th>Mobile Phone</th>
                            <th>Work Mail</th>
                            <th>Bank Name</th>
                            <th>Bank Account</th>
                            <th>BPJS</th>
                            <th>BPJS Ketenagakerjaan</th>
                            <th>Non BPJS</th>
                            <th>Permanent Date</th>
                            <th>Emergency Phone</th>
                            <th>Emergency Contact</th>
                            <th>Spouse Name</th>
                            <th>Position</th>
                            <th>Principal</th>
                            <th>Department</th>
                            <th>Regional</th>
                            <th>Branch</th>
                            <th>Work Location</th>
                            <th>Shift Group</th>
                            <th>Time Zone</th>
                            <th>Job Grade</th>
							<th>Job Status</th>
                            <th>Direct Supervisor</th>
                            <th>Immediate Manager</th>
                            <th>Employment Status</th>
                            <th>Expired Date</th>
                            <th>Sales Code</th>
                            <th>Vaccine Status</th>
                            <th>Date of Vaccine</th>
                            <th>Resign Date</th>
                            <th>Terminate Reason</th>
                            <th>Status</th>
                            <th>Surat Pernyataan Attachment</th>
                            <th>Last Education</th>
							<th>SIM Attachment</th>
							<th>NPWP Attachment</th>
							<th>Buku Rekening</th>
							<th>Assigned Company</th>
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
<style type="text/css">
.dtfc-fixed-left{
	z-index:10;
}
</style>

<script type="text/javascript">

var minDate, maxDate;
var filter_column = '_all';
// Custom filtering function which will search data in column four between two values
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = minDate.val();
        var max = maxDate.val();
        var date = data[5];
        if (
            ( min === '' && max === '' ) ||
            ( min === '' && date <= max ) ||
            ( min <= date   && max === '' ) ||
            ( min <= date   && date <= max )
        ) {
            return true;
        }
        return false;
    }
);

function get_datatable(){
	minDate = $('#min').datepicker({
		uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd',
    });
    maxDate = $('#max').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	
		var table =	$('#report_table').DataTable({
            processing: true,
		//	stateSave: true,
		//	serverSide: true,
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
		/*	fixedColumns: {
				left: 4,
			},
		*/
			pageLength: 10,
			lengthMenu: [
					[10, 20, 30, 50, 100, 200, -1],
					[10, 20, 30, 50, 100, 200, 'All']
				],
            ajax: {
             //   url: "{{ route('employee.reportdata') }}",
				url: "<?= url('employee/employee/employee/reportdata') . '?id_url=' ?>" + global_url_server,
			/*	complete: function(){
					$('#loader').addClass('hidden');
				},
			*/
			/*	error: function (jqXHR, textStatus, errorThrown) {
					$('#report_table').DataTable().ajax.reload();
				}
			*/
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
                {data: 'name', name: 'name'},
                {data: 'private_mail', name: 'private_mail'},
                {data: 'join_date', name: 'join_date'},
                {data: 'identification_number', name: 'identification_number'},
                {data: 'address_home', name: 'address_home'},
                {data: 'idcard_address', name: 'idcard_address'},
				{data: 'ktp', name: 'ktp',render: function ( data, type, row ) {
					if(data == null){
						return "";
					}	
					else{
						return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
					}
				  }
				},
				{data: 'kk', name: 'kk',render: function ( data, type, row ) {
					if(data == null){
						return "";
					}	
					else{
						return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
					}
				  }
				},
                {data: 'home_base', name: 'home_base'},
                {data: 'gender', name: 'gender'},
                {data: 'marital', name: 'marital'},
                {data: 'religion', name: 'religion'},
                {data: 'birthdate', name: 'birthdate'},
                {data: 'place_of_birth', name: 'place_of_birth'},
                {data: 'ptkp_status', name: 'ptkp_status'},
                {data: 'number_of_children', name: 'number_of_children'},
                {data: 'npwp_number', name: 'npwp_number'},
                {data: 'mobile_phone', name: 'mobile_phone'},
                {data: 'work_mail', name: 'work_mail'},
                {data: 'bank_name', name: 'bank_name'},
                {data: 'bank_account', name: 'bank_account'},
                {data: 'bpjs', name: 'bpjs'},
                {data: 'ketenagakerjaan', name: 'ketenagakerjaan'},
                {data: 'non_bpjs', name: 'non_bpjs'},
                {data: 'permanent_date', name: 'permanent_date'},
                {data: 'emergency_phone', name: 'emergency_phone'},
                {data: 'emergency_contact', name: 'emergency_contact'},				
                {data: 'spouse_complete_name', name: 'spouse_complete_name'},
                {data: 'position_routing', name: 'position_routing'},
                {data: 'principal', name: 'principal'},
                {data: 'department', name: 'department'},
                {data: 'regional', name: 'regional'},
                {data: 'branch', name: 'branch'},
                {data: 'work_location', name: 'work_location'},
                {data: 'shift_group', name: 'shift_group'},
                {data: 'time_zone', name: 'time_zone'},
                {data: 'job_grade', name: 'job_grade'},
                {data: 'job_status', name: 'job_status'},
                {data: 'parent_emp_name', name: 'parent_emp_name'},
                {data: 'indirect_emp_name', name: 'indirect_emp_name'},
                {data: 'employment_status', name: 'employment_status'},
                {data: 'expired_date', name: 'expired_date'},
                {data: 'sales_code', name: 'sales_code'},
                {data: 'vaccination_status', name: 'vaccination_status'},
                {data: 'lasted_date_vaccine', name: 'lasted_date_vaccine'},
                {data: 'resign_date', name: 'resign_date'},
                {data: 'terminate_reason', name: 'terminate_reason'},
                {data: 'status_active', name: 'status_active'},
                {data: 'surat_pernyataan', name: 'surat_pernyataan',render: function ( data, type, row ) {
					if(data == null){
						return "";
					}	
					else{
						return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
					}
				  }
				},
                {data: 'last_education', name: 'last_education'},
				{
					data:'sim_attachment', 
					name: 'sim_attachment', 
					render: function(data, type, row) {
						if(data) {
							return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
						}
						return '';
					}
				},
				{
					data:'npwp_attachment', 
					name: 'npwp_attachment', 
					render: function(data, type, row) {
						if(data) {
							return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
						}
						return '';
					}
				},
				{
					data:'buku_rekening_attachment', 
					name: 'buku_rekening_attachment', 
					render: function(data, type, row) {
						if(data) {
							return '<a href="../../project/storage/app/public/upload/data/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
						}
						return '';
					}
				},
				{
					data:'assigned_company', 
					name: 'assigned_company', 
				},
				{
                defaultContent: '',
				orderable: false,
				}
            ],
			"fnInitComplete": function (oSettings) {
			   $('#report_table_wrapper .column-filter-widget:eq(50)').find("select option:contains('A')").attr('selected','selected').change();
			   $('#report_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
			   $.each(global_ex_concurent, function (i, item) {
			   		$('#report_table_wrapper .column-filter-widget:eq(43)').find("select option:contains('"+item.description+"')").attr('selected','selected').change();
				});
			}
        });

	$('#min, #max').on('change', function () {
        table.draw();
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

$(document).ready(function(){
	$('#adv').click(function(){
			if($(".bf").css('display') == 'none'){
				$(".bf").show("slow");
			}
			else {
				$(".bf").hide("slow");
			}		
		});	
	get_data_custom();
//	get_datatable();
	//	$(".bf").show();
});

function get_data_custom(){
	$.getJSON('<?= url('employee/employee_setting/custom_report/get_data_custom') . '?address=' ?>' + global_url_server, function (data) {
	//	$('#req_report').prepend('<option selected></option>').select2({
		$('#req_report').select2({
			placeholder: "Select Group Column ...",
			allowClear: true,
			data: data,
			}).on('change', function (e) {
		//	$('#loader').removeClass('hidden');	
		//		console.log($(this).select2('data'));
			if($(this).select2('data').length > 0){	
				if($(this).select2('data')[0].id != ''){
					fil_col = '['+$(this).select2('data')[0].detail_column+']';				
					filter_column = JSON.parse(fil_col);
				}
				else{
					filter_column = '_all';
				}
				$('#report_table').DataTable().destroy();
				get_datatable();
			}
			else{
				filter_column = '_all';
				$('#report_table').DataTable().destroy();
				get_datatable();
			}
			
			}).trigger('change');	
		}).fail(function (data) { // Call failed
			get_data_custom();
		});		
}
</script>
