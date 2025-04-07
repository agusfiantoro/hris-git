@extends('adminlte::page')
@section('title', 'Employee Approved History')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Approval History</h5>
            </div>

            <div class="card-body">
			<div class="form-group row">              
				<label class="col-md-3 col-form-label">Start to End Created Date :</label>
				<div class="col-md-4">
					<div class="input-group">
						<input name="daterange" id="daterange_search" type="daterange" class="form-control form-control-sm" />
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
			<div class="div_datatable" style="display:none;"> 
					<button onclick="return false;" class="btn btn-default pull-left advanced_history">Advanced Search</button>
					<br>
					<br>
                <table id="employee_approval_table" style="width:100%;" class="display nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th>Reference Number</th>
                            <th>NIK</th>
                            <th>Employee Name</th>
                            <th>Request Name</th>
                            <th>Transaction Name</th>
                            <th>Description</th>
                            <th>Employee Note</th>
                            <th data-priority="1">Approval Status</th>
                            <th>Primary Approval</th>
                            <th style="text-align:center;" data-priority="2">Action</th>
                        </tr>
                    </thead>
                </table>				
            </div>
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
                                <label class="col-sm-4 col-form-label">Employee Name</label>
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
							
							<div id="career_date">
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
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_approval_transaction = 0;
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

$(document).ready(function(){

	daterange();

$(document).on('click', '#search_created', function () {
    get_datatable();
});

$(document).on("click", ".advanced_history", function () {
    $('.cf').select2({width:'100%'});
    if($(".employee_approval_table").css('display') == 'none'){
        $(".employee_approval_table").show("slow");
    }
    else {
        $(".employee_approval_table").hide("slow");
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
		setTimeout(function() { 
			$('#search_created').trigger('click');
		}, 1000); 
}
	
const get_datatable = async () => {
	$(".div_datatable").show();
		let myData = {
			startdate: $("#startdate").val() == '' ? null : $("#startdate").val(),
			enddate: $("#enddate").val() == '' ? null : $("#enddate").val(),
		};
//	console.log(myData);	
	$('#employee_approval_table').DataTable({
            processing: true,
        //    scrollY: true,
			pageLength: 10,
			responsive: true,
			destroy: true,
            ajax: {
                url: "{{ route('emp_approval_history.index_history') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
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
                {data: 'name', name: 'name'},
                {data: 'source_transaction_type', name: 'source_transaction_type', render: function ( data, type, row ) {	
						source = data.replaceAll('_',' ');
						return source;
                    }},
                {data: 'request_group', name: 'request_group'},
                {data: 'request_type', name: 'request_type'},
                {data: 'note', name: 'note'},
                {data: 'approval_status', name: 'approval_status'},
                {data: 'is_primary_approval', name: 'is_primary_approval', render: function ( data, type, row ) {
                        if(row.is_primary_approval == true){
                            return '<span class="badge badge-success" style="padding:10px;font-size:13px;">Yes</span>';
                        }
                        else{
                            return '<span class="badge badge-danger" style="padding:10px;font-size:13px;">No</span>';
                        }
                    }
                },
                {data: 'action', name: 'action'},
            ]
        });
	
	}
});

$(document).on('click', '.view', function(){
  let id_approval_transaction = $(this).attr('id');
		$.ajax({
                url: "<?= url('employee/employee/approval_history/get_view_approval_history') ?>",
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
					$('#request_type').val(response.request_type).trigger('change');
                    $('#name').val(response.name).trigger('change');
                    $('#note').val(response.note).trigger('change');
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
						// 	$('#attach').html('');
						// }
						// else{
						// 	document.getElementById('attach').href = "../../project/storage/app/public/upload/career/"+response.id_employee_request+"/"+response.attachment;
						// }
						$("#career_date").css("display","inline");
						$("#request_date").css("display","none");
						$("#announ_date").css("display","none");
						$("#announ_content").css("display","none");
						$("#fpk_desc").hide();
						$('#days_type').hide();
						$('#attach_view').hide();
						$('#effective_date').val(response.effective_date).trigger('change');
						$("#detail_travel").css("display","none");
						$('#req_by').hide();
						$('#eff_ex_date').hide();
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
						$("#detail_travel").css("display","none");
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
						$('#req_by').hide();
						$('#eff_ex_date').hide();
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
						$('#days_type').hide();
						$('#attach_view').hide();
						$('#request_start_to').val(response.request_start_to).trigger('change');
						$('#request_end_to').val(response.request_end_to).trigger('change');
						$("#detail_travel").css("display","none");
						$('#daterange').attr('disabled', true);					
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
						$('#req_by').hide();
						$('#eff_ex_date').hide();
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
						$('#req_by').hide();
						$('#eff_ex_date').hide();
					}
					else if(source == 'FPK_Request'){
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
						$('#req_by').hide();
						$('#eff_ex_date').hide();
					}
					else if(source == 'Official_Travel'){
						$('#request_type').val(response.travel_type).trigger('change');
						$('#note').val(response.travel_note).trigger('change');
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
						$('#req_by').hide();
						$('#eff_ex_date').hide();
					}
					else if(source == 'Sales_Code'){
						$('#request_type').val(response.travel_type).trigger('change');
						$('#note').val("Position : "+response.position_route_old).trigger('change');
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
	
</script>

@endsection