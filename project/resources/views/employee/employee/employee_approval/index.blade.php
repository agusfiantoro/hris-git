@extends('adminlte::page')
@section('title', 'Employee Approval')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Approval</h5>
            </div>

            <div class="card-body">
			<div class="form-group row">              
					<label class="col-sm-3 col-form-label">Searching By :</label>
					<div class="col-md-5">
						<select id="req_type" class="form-control form-control-sm select2" style="width: 100%;"></select>
					</div>
					<div class="col-sm-2">
						<button onclick="return false;" id="search_type" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>										
	            </div>
				
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="employee_approval_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Reference Number</th>
                            <th>NIK</th>
                            <th data-priority="1" class="nowrap">Employee Name</th>
                            <th data-priority="2">Transaction Name</th>
                            <th data-priority="5">Description</th>
                            <th>Employee Note</th>
                            <th>Req. Start Date</th>
                            <th>Req. End Date</th>
                            <th data-priority="4">Document Status</th>
                            <th>Primary Approval</th>
                            <th style="text-align:center;" data-priority="3" width=200>Action</th>
                        </tr>
                    </thead>
                </table>
				
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_view_approval"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">    
				<div class="modal-header">
                    <h5 class="modal-title">Detail Approval</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Reference Number</label>
                                <div class="col-sm-8">
                                    <input type="text" id="reference_number" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee Name (NIK)</label>
                                <div class="col-sm-8">
                                    <input type="text" id="name" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Request Name</label>
                                <div class="col-sm-8">
                                    <input type="text" id="source_transaction_type" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Transaction Name</label>
                                <div class="col-sm-8">
                                    <input type="text" id="request_group" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" id="request_type" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee Note</label>
                                <div class="col-sm-8">
									<textarea id="note" class="form-control form-control-sm" rows="3" readonly></textarea>
                                </div>
                            </div>
							
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Created Date</label>
                                <div class="col-sm-8">
                                    <input type="text" id="creation_date" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div id="req_by">
								<div class="row">
									<label class="col-sm-4 col-form-label">Request By</label>
									<div class="col-sm-8">
										<input type="text" id="request_by" class="form-control form-control-sm" readonly>                                  
									</div>
								</div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Approval Status</label>
                                <div class="col-sm-8">
                                    <input type="text" id="approval_status" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Document Status</label>
                                <div class="col-sm-8">
                                    <input type="text" id="document_status" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>	
							<div id="fpk_desc">
								<div class="row">
										<label class="col-sm-4 col-form-label">Effective Date</label>
										<div class="col-sm-8">
											<div class="input-group">
												<input type="text" id="fpk_eff_date" class="form-control form-control-sm" readonly> 
												<div class="input-group-append">
													<span class="input-group-text far fa-calendar form-control-sm"></span>
												</div>
											</div>
										</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Request Position</label>
									<div class="col-sm-8">
										<textarea style="font-weight:bold;" id="position_route_old" class="form-control form-control-sm" rows="2" readonly></textarea>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Request Total</label>
									<div class="col-sm-8">
										<input type="text" id="count_req" class="form-control form-control-sm" readonly>                                  
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Contract Duration</label>
										<div class="col-sm-3" style="float:left;">
											<input type="text"id="pkwt_duration" class="form-control form-control-sm" readonly>	  
										</div>
										<label class="col-sm-5 col-form-label">Month(s)</label>									
								</div>
							</div>
														
							<div id="career_date" style="display:none;">
								<div class="row">
									<label class="col-sm-4 col-form-label">Effective Date</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input type="text" id="effective_date" class="form-control form-control-sm" readonly> 
											<div class="input-group-append">
													<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
										</div>
									</div>
								</div>	
								<div class="row">
									<label class="col-sm-4 col-form-label">Detail Career</label>
									<div class="col-sm-8">
										<button style="color:white;" type="button" class="btn btn-sm btn-warning" onclick="loadcareer()"><i class="far fa-eye fa-lg"></i></button>
									</div>
								</div>	
                            </div>	
							<div id="request_date">
								<div class="daterange row">
									<label class="col-sm-4 col-form-label">Request Start to End Date</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input id="daterange" type="daterange" class="form-control form-control-sm" readonly>
											<input id="request_start_to" class="form-control form-control-sm" hidden>
											<input id="request_end_to" class="form-control form-control-sm" hidden>										
											<div class="input-group-append">
													<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
										</div>
									</div>
								</div>
								<div id="days_type" class="row">
									<label class="col-sm-4 col-form-label">Days Type</label>
									<div class="col-sm-8">
										<input type="text" id="day_type" class="form-control form-control-sm" readonly>                                  
									</div>
								</div>
                            </div>
							<div id="eff_ex_date">
								<div class="row">
									<label class="col-sm-4 col-form-label">Effective to Expired Date</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input id="daterange_reco" type="daterange" class="form-control form-control-sm" readonly>
											<input id="eff_to" class="form-control form-control-sm" hidden>
											<input id="ex_to" class="form-control form-control-sm" hidden>										
											<div class="input-group-append">
													<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
										</div>
									</div>
								</div>
                            </div>
							<div id="detail_travel" style="display:none;">
								<div class="row">
									<label class="col-sm-4 col-form-label">Detail Official Travel</label>
									<div class="col-sm-8">
										<button style="color:white;" type="button" class="btn btn-sm btn-warning" onclick="loadTravel()"><i class="far fa-eye fa-lg"></i></button>
									</div>
								</div>	
							</div>
							<div id="detail_kpi" style="display:none;">
								<div class="row">
									<label class="col-sm-4 col-form-label">Detail KPI & 360 Feedback</label>
									<div class="col-sm-8">
										<button style="color:white;" type="button" class="btn btn-sm btn-warning" onclick="loadKpi()"><i class="far fa-eye fa-lg"></i></button>
									</div>
								</div>
								<div class="row">
									<label class="col-sm-4 col-form-label">Detail Reco</label>
									<div class="col-sm-8">
										<button type="button" class="btn btn-sm btn-success" onclick="get_pdf()"><i class="fa fa-file-pdf fa-lg" style="padding:0 2px 0 2px;"></i></button>
									</div>
								</div>	
							</div>
							
							<div id="announ_date">
								<div class="daterange row">
									<label class="col-sm-4 col-form-label">Start to End Date</label>
									<div class="col-sm-8">
										<div class="input-group">
											<input id="daterange_announ" type="daterange" class="form-control form-control-sm" readonly>
											<input id="start_date" class="form-control form-control-sm" hidden>
											<input id="end_date" class="form-control form-control-sm" hidden>										
											<div class="input-group-append">
													<span class="input-group-text far fa-calendar form-control-sm"></span>
											</div>
										</div>
									</div>
								</div>							
                            </div>	
							<div id="attach_view" class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>
                                <div class="col-sm-8 col-form-label">
										<a id="attach" href="#" target="_blank" style="font-size:14px;"><b>View File</b></a>
                                </div>
                            </div>
							<div id="branch_type" class="row" style="display:none;">
								<label class="col-sm-4 col-form-label">Branch</label>
								<div class="col-sm-8">
									<input type="text" id="branch_bgen" class="form-control form-control-sm" readonly>                                  
								</div>
							</div>
							<div id="principal_type" class="row" style="display:none;">
								<label class="col-sm-4 col-form-label">Principal</label>
								<div class="col-sm-8">
									<input type="text" id="principal_bgen" class="form-control form-control-sm" readonly>                                  
								</div>
							</div>
                        </div>
						
						<div id="announ_content" class="col-md-12" style="margin-top:20px;">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Announcement Content</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="content_letter" id="content_letter"></textarea>
									</div> 
								</div> 
                            </div>
                        </div>
                       <div class="col-md-6"></div>
                    </div>
					<br>
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
							<div class="col-sm-12">
								<span class="table-invalid-feedback text-red" role="alert" id="table_status_detailError">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>
                </div>
				 <div class="modal-footer">
                    <button class="btn btn-sm btn-success approve shortcut" ><span class="fa fa-check-square-o fa-lg"></span> Approve</button>            
                    <button class="btn btn-sm btn-info revised shortcut" ><span class="fa fa-pencil-square fa-lg"></span> Revise</button>            
                    <button class="btn btn-sm btn-danger rejected shortcut" ><span class="fa fa-window-close-o fa-lg"></span> Reject</button>            

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
                <h4 align="center" style="margin:0;">Are you sure ?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<h5 id="detail_title" class="modal-title"></h5>
			<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection
@section('css')
<style type="text/css">
.sweet-success{
 background-color: #218838;
}
.sweet-success:not([disabled]):hover{
 background-color: #1e7e34;;
}
.sweet-danger{
 background-color: #c82333;
}
.sweet-danger:not([disabled]):hover{
 background-color: #bd2130;;
}
.sweet-info{
 background-color: #138496;;
}
.sweet-info:not([disabled]):hover{
 background-color: #117a8b;
}
.swal-text {
  background-color: #FAF1F1;;
  padding: 17px;
  border: 1px solid #CF0000;
  display: block;
  margin: 22px;
  text-align: center;
  color: #61534e;
}

select[readonly].select2-hidden-accessible + .select2-container {
	pointer-events: none;
	touch-action: none;
}
select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
	background: #e8ebed;
	box-shadow: none;
}

select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
	display: none;
}
td.cWidth{
	white-space:nowrap;
}
th.cWidth{
	white-space:nowrap;
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_quanti = 0;
let global_id_quali = 0;
let global_appraiser_type = [];
let global_approval_transaction = 0;

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf() {
	let res = {
        id_recommendation_header: global_approval_transaction,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('employee/employee/recommendation_form/download') }}";
    window.open(url+'?'+param, '_blank');
}
function loadKpi(){
	 $("#contentBody").html('');
	 $("#detail_title").html('Detail KPI & 360 Feedback');
    $.ajax({
		url:"employee_approval/detail_kpi/"+global_approval_transaction,
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}

function loadTravel(){
	 $("#contentBody").html('');
	 $("#detail_title").html('Detail Official Travel');
    $.ajax({
		url:"employee_approval/detail_travel/"+global_approval_transaction,
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}
function loadcareer(){
	 $("#contentBody").html('');
	 $("#detail_title").html('Detail Career');
    $.ajax({
		url:"employee_approval/detail_career/"+global_approval_transaction,
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}

const list_req_type = async () => {
    let result;
    try {
        result = await $.ajax({
            type: 'GET',
            url: "<?= url('employee/employee/employee_approval/list_req_type') ?>",
            dataType: 'json',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            success: function (res) {
				$('#req_type').prepend('<option selected></option>').select2({
					placeholder: "Select Transaction Name ...",
					data: res,
					allowClear: true,
				});
            }
        });
        return result;
    } catch (error) {
        list_req_type();
    }
}

$(document).on('click', '#search_type', function () {
    get_datatable();
});

$(document).ready(function(){
	list_req_type();
	get_datatable()
});


function get_datatable(){
	let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);
    let btnApproveAttendanceCorrection = {
        text: 'Approve Attendance Correction',
        className: 'btn btn-success approve_all_attendance_correction',
        action: function (e, dt, node, config) {
            let id_approval_transaction = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
                return $(entry).attr('id_approval_transaction')
            });
            if(id_approval_transaction.length > 0){
            	//nama type Attendance_Correction harus sesuai dengan huruf kecil besar karakternya
                sendApproveAll('Attendance_Correction', id_approval_transaction)
            } else {
                swal({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select Attendance Correction request first'
                });
            }
        }
    }
	dtButtons.push(btnApproveAttendanceCorrection)
	
	let myData = {
			req_type: $("#req_type").val() == '' ? null : $("#req_type").val(),
		};
	$('#employee_approval_table').DataTable({
            processing: true,
        //    scrollY: true,
			destroy:true,
			pageLength: 10,
			responsive: true,
            ajax: {
                url: "{{ route('emp_approval.index') }}",
				"data": myData,
				error: function (jqXHR, textStatus, errorThrown) {
					$('#employee_approval_table').DataTable().ajax.reload();
				}
            },		
            columns: [
				{
                defaultContent: '',
				orderable: false,
				},
                {   // Checkbox select column
                data: 'id_approval_transaction',
                defaultContent: '',
                orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'reference_number', name: 'reference_number'},
                {data: 'nik_employee', name: 'nik_employee'},
                {data: 'name', name: 'name',className: 'cWidth'},
           /*     {data: 'source_transaction_type', name: 'source_transaction_type', render: function ( data, type, row ) {	
						source = data.replaceAll('_',' ');
						return source;
                    }},
			*/
                {data: 'group_detail', name: 'group_detail'},
                {data: 'request_type', name: 'request_type'},
                {data: 'note', name: 'note'},
                {data: 'request_start_date'},
                {data: 'request_end_date'},
                {data: 'document_status', name: 'document_status',className: 'cWidth', render: function ( data, type, row ) {
					if(row.document_status == 'Partial Approved'){
							return '<span class="badge badge-warning" style="padding:10px;font-size:13px;">'+data+'</span>';
						}
					else{
							return '<span class="badge" style="font-size:13px;">'+data+'</span>';
						}
					}
				},
                {data: 'is_primary_approval', name: 'is_primary_approval', render: function ( data, type, row ) {
                        if(row.is_primary_approval == true){
                            return '<span class="badge badge-success" style="padding:10px;font-size:13px;">Yes</span>';
                        }
                        else{
                            return '<span class="badge badge-danger" style="padding:10px;font-size:13px;">No</span>';
                        }
                    }
                },
                {data: 'action', name: 'action',className: 'cWidth'},
            ],
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('id_approval_transaction', data['id_approval_transaction']);
            },
            buttons: dtButtons,
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

}
$(document).on('click', '.view', function(){
  let id_approval_transaction = $(this).attr('id');
  	$('.approve.shortcut').attr('id', id_approval_transaction);
  	$('.revised.shortcut').attr('id', id_approval_transaction);
  	$('.rejected.shortcut').attr('id', id_approval_transaction);

		$.ajax({
                url: "<?= url('employee/employee/employee_approval/get_view_approval') ?>",
                method: "GET",
                data: {id_approval_transaction: id_approval_transaction},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
				global_approval_transaction = response.id_source_transaction
					source = response.source_transaction_type;
					$('#reference_number').val(response.reference_number).trigger('change');
					$('#source_transaction_type').val(source.replaceAll('_',' ')).trigger('change');
					$('#request_group').val(response.request_group).trigger('change');
				//	$('#request_type').val(response.request_type).trigger('change');
                    $('#name').val(response.name+' ('+response.nik_employee+')').trigger('change');
                //  $('#note').val(response.note).trigger('change');
                    $('#creation_date').val(response.creation_date).trigger('change');
                    $('#approval_status').val(response.approval_status).trigger('change');
                    $('#document_status').val(response.document_status).trigger('change');
					$("#content_letter").summernote("code", response.content_letter);
					$('.summernote').summernote('disable');
										
					if(source == 'Career_Request'){
						$('#attach').removeAttr("download").css('display','none');
                        if(response.attachment != null){
                            $('#attach').css('display','inline').attr('href', response.filePath);
                        }

						// if(response.attachment == null || response.attachment == ""){
						// 	$('#attach').css('display','none');
						// }
						// else{
						// 	$('#attach').css('display','inline');
						// 	document.getElementById('attach').href = "../../project/storage/app/public/upload/career/"+response.id_employee_request+"/"+response.attachment;
						// }
						$('#request_type').val(response.request_type).trigger('change');
						$('#note').val(response.note).css('font-weight','normal').trigger('change');
						$("#career_date").css("display","inline");
						$("#request_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$("#fpk_desc").hide();
						$('#days_type').hide();
						$('#attach_view').show();
						$('#effective_date').val(response.effective_date).trigger('change');
						$("#detail_travel").css("display","none");
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}
					else if(source == 'Leave_Request' || source == 'Overtime_Request' || source == 'Cancel_Leave' || source == 'Change_Day_off'){
						$('#attach').removeAttr("download").css('display','none');

						if(response.attachment_type != null){
                            $('#attach').attr('download',response.name+'.jpeg').css('display','inline');
	                        if(response.attachment_type == 'image'){
								$('#attach').attr('href','data:image/jpg;base64,'+ response.attachment);
	                        }
	                        else if(response.attachment_type == 'pdf'){
								$('#attach').attr('href','data:application/pdf;base64,'+ response.attachment);
	                        }  
	                    } else {
	                        if(response.attachment != null){
                                $('#attach').css('display','inline').attr('href', response.filePath);
	                        }
	                    }
						$('#request_type').val(response.request_type).trigger('change');
						$('#note').val(response.note).css('font-weight','normal').trigger('change');
						$("#request_date").css("display","inline");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').show();
						$('#request_end_to').show();
						$('.daterange').show();
						$('#attach_view').show();
						$('#days_type').show();
						$("#fpk_desc").hide();
						$('#request_start_to').val(response.request_start_to).trigger('change');
						$('#request_end_to').val(response.request_end_to).trigger('change');
						$('#daterange').attr('disabled', true);					
						$('#daterange').daterangepicker({
							uiLibrary: 'bootstrap4',
							autoApply: true,
							opens: 'center',
							locale: {
								  format: 'YYYY-MM-DD',
								  separator: '   to   ',
								  closeText: 'Clear',
								},
							startDate: response.request_start_to, endDate: response.request_end_to 
						}, function(start, end, label) {
							$("#request_start_to").val(start.format('YYYY-MM-DD'));
							$("#request_end_to").val(end.format('YYYY-MM-DD'));
											
						});	
						$('#day_type').val(response.day_type).trigger('change');
						$("#detail_travel").css("display","none");
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}
					else if(source == 'Attendance_Correction'){
						$('#attach').removeAttr("download").css('display','none');

						if(response.attachment_type != null){
                            $('#attach').attr('download',response.name+'.jpeg').css('display','inline');
	                        if(response.attachment_type == 'image'){
								$('#attach').attr('href','data:image/jpg;base64,'+ response.attachment);
	                        }
	                        else if(response.attachment_type == 'pdf'){
								$('#attach').attr('href','data:application/pdf;base64,'+ response.attachment);
	                        }  
	                    } else {
	                        if(response.attachment != null){
	                            $('#attach').css('display','inline').attr('href', response.filePath);
	                        }
	                    }
						$('#request_type').val(response.request_type).trigger('change');
						$('#note').val(response.note).css('font-weight','normal').trigger('change');
						$("#request_date").css("display","inline");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').show();
						$('#request_end_to').show();
						$('.daterange').show();
						$('#days_type').show();
						$("#fpk_desc").hide();
						$('#days_type').hide();
						$('#attach_view').show();
					//	$('#attach_view').hide();
						$('#request_start_to').val(response.request_start_to).trigger('change');
						$('#request_end_to').val(response.request_end_to).trigger('change');
						$('#daterange').attr('disabled', true);	
						$("#detail_travel").css("display","none");						
						$('#daterange').daterangepicker({
							uiLibrary: 'bootstrap4',
									timePicker: true,
									locale: {
										  format: 'YYYY-MM-DD HH:mm',
										  separator: '   to   ',
										  closeText: 'Clear',
										},
									startDate: response.request_start_to, 
									endDate: response.request_end_to 
						}, function(start, end, label) {
							$("#request_start_to").val(start.format('YYYY-MM-DD HH:mm'));
							$("#request_end_to").val(end.format('YYYY-MM-DD HH:mm'));
						});	
                        
                        if(response.request_start_to == null){
                            $('#daterange').val('-   to   '+response.request_end_to);
                        } else if(response.request_end_to == null){
                            $('#daterange').val(response.request_start_to+'   to   -');
                        }
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}
					else if(source == 'Announcement_Request'){
						$('#attach').removeAttr("download").css('display','none');

						if(response.attachment_type != null){
                            $('#attach').attr('download',response.name+'.jpeg').css('display','inline');
	                        if(response.attachment_type == 'image'){
								$('#attach').attr('href','data:image/jpg;base64,'+ response.attachment);
	                        }
	                        else if(response.attachment_type == 'pdf'){
								$('#attach').attr('href','data:application/pdf;base64,'+ response.attachment);
	                        }  
	                    } else {
	                        if(response.attachment != null){
	                            $('#attach').css('display','inline').attr('href', response.filePath);
	                        }
	                    }
						$('#request_type').val(response.request_type).trigger('change');
						$('#note').val(response.note).css('font-weight','normal').trigger('change');
						$("#announ_date").css("display","inline");
						$("#announ_content").css("display","inline");
						$("#career_date").css("display","none");
						$("#request_date").css("display","none");
						$("#fpk_desc").hide();
						$('#days_type').hide();
						$('#attach_view').hide();
						$('#start_date').val(response.start_date).trigger('change');
						$('#end_date').val(response.end_date).trigger('change');					
						$('#daterange_announ').attr('disabled', true);
						$("#detail_travel").css("display","none");
						$('#daterange_announ').daterangepicker({
							uiLibrary: 'bootstrap4',
									autoApply: true,
									opens: 'center',
									locale: {
										  format: 'YYYY-MM-DD',
										  separator: '   to   ',
										  closeText: 'Clear',
										},
									startDate: response.start_date, endDate: response.end_date 
						}, function(start, end, label) {
							$("#start_date").val(start.format('YYYY-MM-DD'));
							$("#end_date").val(end.format('YYYY-MM-DD'));
											
						});	
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}					
					else if(source == 'FPK_Request'){
						$('#request_type').val(response.request_type).trigger('change');
						$('#note').val(response.note).css('font-weight','normal').trigger('change');
						$('#attach').removeAttr("download").css('display','none');
						$("#request_date").css("display","inline");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').hide();
						$('#request_end_to').hide();
						$('.daterange').hide();
						$('#days_type').hide();
						$('#attach_view').hide();
						$("#fpk_desc").show();
						$("#fpk_eff_date").val(response.fpk_effective_date).trigger('change');
						$("#position_route_old").val(response.position_route_old+'\n(Region : '+response.region+', Branch : '+response.branch+')').trigger('change');
						$("#count_req").val(response.count_req).trigger('change');
						$("#pkwt_duration").val(response.pkwt_duration).trigger('change');
						$("#detail_travel").css("display","none");
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}
					else if(source == 'Official_Travel'){
						if(response.request_group_code == 'Official_Travel_Reschedule'){
							$('.rejected.shortcut').css('display', 'none');
						}
						$('#request_type').val(response.travel_type).trigger('change');
						$('#note').val(response.travel_note).css('font-weight','normal').trigger('change');
						$('#attach').removeAttr("download").css('display','none');
						$("#request_date").css("display","inline");
						$("#detail_travel").css("display","inline");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').show();
						$('#request_end_to').show();
						$('.daterange').show();
						$('#days_type').hide();
						$("#fpk_desc").hide();
						$('#attach_view').hide();	
						$('#request_start_to').val(response.request_start_date).trigger('change');
						$('#request_end_to').val(response.request_end_date).trigger('change');
						$('#daterange').attr('disabled', true);					
						$('#daterange').daterangepicker({
							uiLibrary: 'bootstrap4',
							autoApply: true,
							opens: 'center',
							locale: {
								  format: 'YYYY-MM-DD',
								  separator: '   to   ',
								  closeText: 'Clear',
								},
							startDate: response.request_start_date, endDate: response.request_end_date
						}, function(start, end, label) {
							$("#request_start_to").val(start.format('YYYY-MM-DD'));
							$("#request_end_to").val(end.format('YYYY-MM-DD'));
						});	
						$('#branch_type, #principal_type').hide();
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					}
					else if(source == 'Sales_Code'){
						$('#request_type').val(response.travel_type).trigger('change');
						$('#note').val("Position : "+response.position_route_old).css('font-weight','normal').trigger('change');
						$('#attach').removeAttr("download").css('display','none');
						$("#request_date").css("display","inline");
						$("#detail_travel").css("display","none");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').hide();
						$('#request_end_to').hide();
						$('.daterange').hide();
						$('#days_type').hide();
						$("#fpk_desc").hide();
						$('#attach_view').hide();	
						$('#branch_type, #principal_type').show();
						$('#branch_bgen').val(response.location_new).trigger('change');
						$('#principal_bgen').val(response.principal_new).trigger('change');
						$('#req_by').hide();
						$('#eff_ex_date').hide();
						$('#detail_kpi').hide();
					//	$('#request_start_to').val(response.request_start_date).trigger('change');
					//	$('#request_end_to').val(response.request_end_date).trigger('change');
					//	$('#daterange').attr('disabled', true);					
					/*	$('#daterange').daterangepicker({
							uiLibrary: 'bootstrap4',
							autoApply: true,
							opens: 'center',
							locale: {
								  format: 'YYYY-MM-DD',
								  separator: '   to   ',
								  closeText: 'Clear',
								},
							startDate: response.request_start_date, endDate: response.request_end_date
						}, function(start, end, label) {
							$("#request_start_to").val(start.format('YYYY-MM-DD'));
							$("#request_end_to").val(end.format('YYYY-MM-DD'));
						});	
					*/
					}
					else if(source == 'Form_Reco'){
						var pos_new = "-";
						$('#request_type').val(response.request_type).trigger('change');
						if(response.position_route_new != null){
							pos_new = response.position_route_new;
						}
						$('#note').val('Current Position : '+response.position_route_old+' \nCategory : '+response.note_reco+' \nNew Position : '+pos_new).css('font-weight','bold').trigger('change');
						$('#attach').removeAttr("download").css('display','none');
						$("#request_date").css("display","inline");
						$("#career_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$('#request_start_to').hide();
						$('#request_end_to').hide();
						$('.daterange').hide();
						$('#days_type').hide();
						$('#attach_view').hide();
						$("#fpk_desc").hide();
						$("#detail_travel").css("display","none");
						$('#branch_type, #principal_type').hide();
						$('#req_by').show();
						$('#request_by').val(response.submitted_by).trigger('change');
						$('#eff_ex_date').show();
						$('#daterange_reco').attr('disabled', true);					
						$('#daterange_reco').daterangepicker({
							uiLibrary: 'bootstrap4',
							autoApply: true,
							opens: 'center',
							locale: {
								  format: 'YYYY-MM-DD',
								  separator: '   to   ',
								  closeText: 'Clear',
								},
							startDate: response.eff_date_reco, endDate: response.ex_date_reco
						}, function(start, end, label) {
							$("#eff_to").val(start.format('YYYY-MM-DD'));
							$("#ex_to").val(end.format('YYYY-MM-DD'));
						});
						if(response.eff_date_reco == null){
                            $('#daterange_reco').val('-   to   '+response.ex_date_reco);
                        } else if(response.ex_date_reco == null){
                            $('#daterange_reco').val(response.eff_date_reco+'   to   -');
                        }
						if(response.reco_flag == true){
							$('#detail_kpi').show();
						}
						else{
							$('#detail_kpi').hide();
						}
						
					}
										
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
							url: "<?= url('employee/employee/employee_approval/index_status').'?id_source_transaction='?>"+response.id_source_transaction+"<?= '&source_transaction_type='?>"+response.source_transaction_type,							
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
	

$(document).on('click', '.approve', function (event) {
	id_approval_transaction = $(this).attr('id');
    event.preventDefault();
    swal({
    title: "Are You Sure ?",
    icon: "warning",
    buttons: {
        cancel : 'Cancel',
		confirm : {text:'Yes, Approved!',className:'sweet-success'}
    }
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"employee_approval/approve/"+id_approval_transaction,
			   beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(response)
			   {
				if(response.data.length != 0){
					var formData = response.data;
					$.ajax({
						type: 'POST',
						headers: {
							'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
						},
						url: "{{ route('mail.new_approval') }}",
						data: {source: formData},
					});	
				}
				else{
					 swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'Something went wrong!',
					}).then(function(){ 
						$('#loader').addClass('hidden')
					})
				}
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#employee_approval_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Approved!",
					  icon: "success",
					   buttons: {confirm : {className:'sweet-success'}},
					}).then(ok => {
						$('#employee_approval_table').DataTable().ajax.reload();
				});
				}, 50);
			   },
			   complete: function(){
					$('#loader').addClass('hidden');
					$('#modal_view_approval').modal('hide');
				},
			  })
        }
    });
});

$(document).on('click', '.revised', function (event) {
	id_approval_transaction = $(this).attr('id');
    event.preventDefault();	
	
	function rev() {
		swal({
		title: "Note",
		icon: "warning",
		content: {
			element: "input",
			attributes: {
				placeholder: "Your Reason . . .",
				type: "text",
			},
		},
		closeOnClickOutside: false,
		closeOnEsc: false,
		buttons: {
			cancel : 'Cancel',
			confirm : {text:'Yes, Revised!',className:'sweet-info'}
		}
		}).then(function(value) {
			if (value) {			 
				$.ajax({
				   url:"employee_approval/revised/text/"+id_approval_transaction,
					data: {text: value, id:id_approval_transaction},
				    beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
				   success:function(response)
				   {
					var formData = response.data;
					$.ajax({
						type: 'POST',
						headers: {
							'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
						},
						url: "{{ route('mail.new_revise') }}",
						data: {source: formData},
					});	
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 $('#employee_approval_table').DataTable().ajax.reload();				 
					 swal({
						title: "Revision Request",
						  icon: "success",
						   buttons: {confirm : {className:'sweet-success'}},
						}).then(ok => {
							$('#employee_approval_table').DataTable().ajax.reload();
					});
					}, 50);
				   },
				    complete: function(){
						$('#loader').addClass('hidden');
						$('#modal_view_approval').modal('hide');
					},
				  })
			}
			else if (value == '') {
				swal({
					text : 'Note is Required',
					icon: "error",
					buttons: {
						confirm : {text:'Back',className:'sweet-danger'}
					}
				}).then(ok => {
					rev();
				});
			}
		});
	}
	rev();
});
 
$(document).on('click', '.rejected', function (event) {
	id_approval_transaction = $(this).attr('id');
    event.preventDefault();	
	
	function rej() {
		swal({
		title: "Note",
		icon: "warning",
		content: {
			element: "input",
			attributes: {
				placeholder: "Your Reason . . .",
				type: "text",
			},
		},
		closeOnClickOutside: false,
		closeOnEsc: false,
		buttons: {
			cancel : 'Cancel',
			confirm : {text:'Yes, Rejected!',className:'sweet-danger'}
		}
		}).then(function(value) {
			if (value) {			 
				$.ajax({
				   url:"employee_approval/rejected/text/"+id_approval_transaction,
					data: {text: value, id:id_approval_transaction},

				   beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
				   success:function(response)
				   {
					var formData = response.data;
					$.ajax({
						type: 'POST',
						headers: {
							'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
						},
						url: "{{ route('mail.new_reject') }}",
						data: {source: formData},
					});	
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 $('#employee_approval_table').DataTable().ajax.reload();				 
					 swal({
						title: "Reject Request",
						  icon: "success",
						   buttons: {confirm : {className:'sweet-success'}},
						}).then(ok => {
							$('#employee_approval_table').DataTable().ajax.reload();
					});
					}, 50);
				   },
				   complete: function(){
						$('#loader').addClass('hidden');
						$('#modal_view_approval').modal('hide');
					},
				  })
			}
			else if (value == '') {
				swal({
					text : 'Note is Required',
					icon: "error",
					buttons: {
						confirm : {text:'Back',className:'sweet-danger'}
					}
				}).then(ok => {
					rej();
				});
			}
		});
	}
	rej();
});
 
function sendApproveAll(type, id_approval_transaction) {
    let result;
    let _token  = "<?= csrf_token() ?>";
    try {
        result = $.ajax({
            type: 'POST',
            url: "<?= url('employee/employee/employee_approval/approve_all') ?>",
            dataType: 'json',
            data: {
                type: type,
                id_approval_transaction: id_approval_transaction,
                _token: _token,
            },
            success: function (resp) {
                if(resp.status == 'true'){
                    swal({
						icon: 'success',
                        title: 'Success',
                        text: resp.message
					}).then(ok => {
						$('#employee_approval_table').DataTable().ajax.reload();
					});
                } else {
                    swal({
                        icon: 'error',
                        title: 'Error',
                        text: resp.message
                    });
                }
            }
        });
        return result;
    } catch (error) {
        sendApproveAll(type, id_approval_transaction);
    }
}

</script>

@endsection