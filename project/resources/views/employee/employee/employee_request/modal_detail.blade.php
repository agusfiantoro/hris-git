  <div class="row">
		<div class="col-md-6">                           
			<div class="row">
				<label class="col-sm-4 col-form-label">Request By</label>
				<div class="col-sm-8">
					<input name="id_request_header" id="id_request_header" type="hidden">
					<input name="request_cancel" id="request_cancel" type="hidden">
					<select name="id_employee_request" id="id_employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly>
					</select>
					<span class="invalid-feedback" role="alert" id="id_employee_requestError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Request Type</label>
				<div id="id_request_type_new" class="col-sm-8">
					<select name="id_request_type" id="id_request_type" class="form-control form-control-sm select2" style="width: 100%;">
					</select>									
					<span class="invalid-feedback" role="alert" id="id_request_typeError">
						<strong></strong>
					</span>
				</div>
				<div id="id_request_type_cancel" class="col-sm-8" style="display: none;">
					<input id="type_cancel" type="text" class="form-control form-control-sm" disabled>                                  
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Leave Type</label>
				<div class="col-sm-8">
					<select name="id_leave_type" id="id_leave_type" class="form-control form-control-sm select2" style="width: 100%;">
					</select>
					<span class="invalid-feedback" role="alert" id="id_leave_typeError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Overtime Type</label>
				<div class="col-sm-8">
					<select name="id_overtime_type" id="id_overtime_type" class="form-control form-control-sm select2" style="width: 100%;">
					</select>
					<span class="invalid-feedback" role="alert" id="id_overtime_typeError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Note</label>
				<div class="col-sm-8">
					<input type="text" name="note" id="note" class="form-control form-control-sm">
					<span class="invalid-feedback" role="alert" id="noteError">
						<strong></strong>
					</span>   
					
				</div>
			</div>
			
			<div class="row">
				<label class="col-sm-4 col-form-label">Attachment</label>
				<div class="col-sm-3" style="display:none;">
					  <select id="attachment_type" name="attachment_type" class="form-control form-control-sm select2">
						<option value="image">Image</option>
						<option value="pdf">PDF</option>
					  </select>
					<span class="invalid-feedback" role="alert" id="attachment_typeError">
						<strong></strong>
					</span>
				</div>
				 <div class="col-sm-8">
				 <div class="custom-file">
				  <input type="file" name="attachment" class="custom-file-input p-0" id="attachment" onchange="uploadFile()">
				  <div style="font-size:10px;margin-top:-5px;font-family:arial;"><i>jpg,jpeg,png(No Max) / pdf(Max 1 Mb)</i></div>
				  <input id="file_name" name="file_name" type="hidden">
				  <span class="invalid-feedback" role="alert" id="attachmentError">
						<strong></strong>
					</span>
					<img id="attach" src="#" width="70px" height="60px" style="display:none;margin-bottom:60px;">
					<a id="attach_pdf" href="#" width="70px" height="60px" style="display:none;margin-bottom:60px;" target="_blank">Download File</a>
				  <label class="custom-file-label" for="customFile" style="font-size:12px;"><i>Select File</i></label>
				</div>
				<progress id="progressBar" value="0" max="100" style="width:100%;"></progress>
				  <label id="status_bar"></label>
				  <b id="loaded_n_total" class="text-success"></b>
			   </div>							   
			</div>
		 
			<!-- div class="row">
				<label class="col-sm-4 col-form-label">Attachment</label>							
			   <div class="col-md-8">
				<div class="custom-file">
				  <input type="file" name="attachment" class="custom-file-input form-control form-control-sm" id="attachment">
				  <span class="invalid-feedback" role="alert" id="attachmentError">
						<strong></strong>
					</span>
				  <label class="custom-file-label" for="customFile"><i>Max 1 MB (pdf,doc,docx,jpg,jpeg,png)</i></label>
				</div>
				
			   </div>
			</div -->
			
		</div>
		<div class="col-md-6">
			<div class="row">
				<label class="col-sm-4 col-form-label">Company</label>
				<div class="col-sm-8">
					<select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
					</select>
					<span class="invalid-feedback" role="alert" id="companyError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Need Delegate Approval</label>
				<div class="col-sm-8">
					<input type="checkbox" name="delegate_approval" id="delegate_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
					<span class="invalid-feedback" role="alert" id="delegate_approvalError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Enable Approval</label>
				<div class="col-sm-8">
					<input type="checkbox" name="enable_approval" id="enable_approval" class="form-control form-control-sm" style="height:20px;width:20px;margin-top:5px;">
					<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" hidden>
						<input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
						<input name="end_date" id="end_date" class="form-control form-control-sm" hidden>
					<span class="invalid-feedback" role="alert" id="enable_approvalError">
						<strong></strong>
					</span>
				</div>
			</div>
			<!-- div class="row">
				<label class="col-sm-4 col-form-label">Enable Approval</label>
				<div class="col-sm-8">
					<select name="enable_approval" id="enable_approval" class="form-control form-control-sm">
						<option value="0">False</option>
						<option value="1">True</option>
					</select>
					<span class="invalid-feedback" role="alert" id="enable_approvalError">
						<strong></strong>
					</span>
				</div>
			</div -->
			<!-- div class="row">
				<label class="col-sm-4 col-form-label">Start Date to End Date</label>

				<div class="col-sm-8">
					<div class="input-group">
						<input name="daterange" id="daterange" type="daterange" class="form-control form-control-sm" hidden>
						<input name="start_date" id="start_date" class="form-control form-control-sm" hidden>
						<input name="end_date" id="end_date" class="form-control form-control-sm" hidden>										
						<div class="input-group-append">
								<span class="input-group-text far fa-calendar form-control-sm"></span>
							</div>
					</div>
					
				</div>
			</div -->
			
			<div class="row">
				<label class="col-sm-4 col-form-label">Hierarchy Approval</label>
				<div class="col-sm-8">
					<select name="id_approval" id="id_approval" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
					<span class="invalid-feedback" role="alert" id="id_approvalError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div class="row">
				<label class="col-sm-4 col-form-label">Status</label>
				<div class="col-sm-8">
					<select name="status" id="select2status" class="form-control form-control-sm" readonly>
						<option value="A">Active</option>
						<option value="I">Inactive</option>
					</select>
					<span class="invalid-feedback" role="alert" id="statusError">
						<strong></strong>
					</span>
				</div>
			</div>
			<div id="id_approval_status_cancel" class="row">
				<label class="col-sm-4 col-form-label">Approval Status</label>
				<div class="col-sm-8">
					<select name="id_approval_status" id="id_approval_status" class="form-control form-control-sm select2" style="width: 100%;" readonly></select>
					<span class="invalid-feedback" role="alert" id="id_approval_statusError">
						<strong></strong>
					</span>   								
				</div>
			</div>
																			 
		</div>
	</div>
	<hr/>
	<div class="row">
		<div class="col-md-12">
			<ul class="nav nav-tabs" id="tab_employee_request_detail" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="link_tab_menu-request" data-toggle="pill" href="#menu-request" role="tab" aria-controls="link_tab_menu-request" aria-selected="true">List Request<span class="error-tab text-red"></span></a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="link_tab_menu-status" data-toggle="pill" href="#menu-status" role="tab" aria-controls="link_tab_menu-status" aria-selected="true">List Approval Status<span class="error-tab text-red"></span></a>
				</li>
			 
			</ul>
			<div class="tab-content" id="tab_employee_request_detail_content" style="font-size:12px">
				<div class="tab-pane fade show active" id="menu-request" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
					<br/>
					<div class="row">
						<div class="col-md-12" style="margin-bottom: 10px;">
							<button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_request_detail" hidden><span class="fas fa-plus"></span> Add Request Detail</button>
						</div>
						<div class="col-md-12"  style="overflow-y: scroll;">
							<table id="table_employee_request" class="table table-striped table-bordered table-hover datatable">
								<thead>
									<tr>
										<th scope="col" style="white-space:nowrap;">No.</th>
										<th scope="col">Employee</th>
										<th scope="col" style="width:80px;">Leave Balance</th>
										<th scope="col" style="width:250px;">Request Start Date </th>
										<th scope="col" style="width:250px;">Request End Date</th>
										<th scope="col" style="width:100px;">Days Type</th>
										<th scope="col" align="center" style="width:100px;">Qty Days (Leave/CDO)</th>
										<!-- th style="width:350px;">Actual Start to End Date</th -->
										<!-- th scope="col">Delegation To</th -->
										<!-- th style="white-space:nowrap;">Action</th -->
									</tr>
								</thead>
								<tbody id="table_employee_request_body">
								</tbody>
							</table>
							<div class="col-sm-12">
								<span class="table-invalid-feedback text-red" role="alert" id="table_employee_requestError">
									<strong></strong>
								</span>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane fade" style="font-size:14px" id="menu-status" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
					<br/>
					<div class="row">                                      
						<div class="col-12">
							<table style="width:100%;" id="table_approve_status_detail" class="responsive table table-striped table-bordered table-hover datatable">
								<thead>
									<tr>
										<th style="white-space:nowrap;">No.</th>
										<th style="white-space:nowrap;">Sequence</th>
										<th data-priority="1" style="white-space:nowrap;">Approval Name</th>
										<th data-priority="3" style="white-space:nowrap;">Approval Status</th>                                                       
										<th data-priority="2" align="center" style="width:50px;">Approval Execute</th>
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
			   
			</div>
		</div>
	</div>
               
<script type="text/javascript">
$(document).ready(function(){
	id_emp_request = {!! $global_request !!};
	refresh_data();	
	
	if(id_emp_request != 0){
		get_edit(id_emp_request);
	}
	
});	

function get_edit(id_emp_request) {
	 $.ajax({
                url: "<?= url('employee/employee/employee_request/get_request_edit') ?>",
                method: "GET",
                data: {id_request_header: id_emp_request},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                    global_id_request_detail = 0;
					 $.each(response.emprequest, function (i, item) {
                        $('#new_employee_request_detail').trigger('click');
                    });
				//	console.log(response);

					if(response.code_status == 'Request_Approval' || response.code_status == 'Approved' || response.code_status == 'Partial_Approved' || response.code_status == 'Cancel'){
						setTimeout(function () {
							$('.checkfield').attr('readonly', true);
							$("#employee_requestForm input").attr('readonly', true);
							$("#sample_table_employee_request input").attr('readonly', true);			
							$("#employee_requestForm select").attr('readonly', true);
							$("#sample_table_employee_request select").attr('readonly', true);
							$("#new_employee_request_detail").css("display","none");
							$('.request_start_to_input').attr('readonly', true);
							$('.request_end_to_input').attr('readonly', true);
							$(".input-group-append").css("display","none");
							$('#daterange').attr('readonly', true);
						//	$('#id_approval').select2({disabled: true});
							
                            $("#save_and_submit").hide().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');

							$("#submit_button").css("display","none");
							$(".delete-record").css("display","none");
							if(global_classname == 'req_cancel'){
								$("#employee_requestForm .modal-title").html("<span class='fa fa-window-close'></span> Cancel Employee Request");
								$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Cancel & Submit').addClass('addForm');
								$('#request_cancel').val(global_classname);
								$('#note').attr('readonly', false);
									$("#id_request_type_new").css("display","none");
									$("#id_request_type_cancel").css("display","inline");
									$("#id_approval_status_cancel").css("display","none");	
								setTimeout(function () {		
									$('#note').val('').trigger('change');
									$('#type_cancel').val('Cancel Leave').trigger('change');
									$('#daterange').daterangepicker({
													uiLibrary: 'bootstrap4',
															autoApply: true,
															opens: 'center',
															locale: {
																  format: 'YYYY-MM-DD',
																  separator: '   to   ',
																  closeText: 'Clear',
																},
												}, function(start, end, label) {
													$("#start_date").val(start.format('YYYY-MM-DD'));
													$("#end_date").val(end.format('YYYY-MM-DD'));
																	
												});	   
								}, 1000);
							}
						}, 2000);
						setTimeout(function () {
							$('#id_leave_type').attr("readonly", true);
						}, 4000);	
                        $(document).ajaxStop(function() {
                            if(response.code_status == 'Approved'){
                                $('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
                                    $('#' + obj.id).val(response.leave_balance_remaining_this_request);
                                });
                            }
                        });
					} else {
                        if(response.code != 'Attendance_Correction'){
                            if(response.leave_balance_status_this_request == 'A'){
                                $("#edit_button").css("display","block");
                                $("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                            } else {
                                $(".leave_balance_expired").css("display","block");
                                $("#save_and_submit").hide().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                            }
                            $(document).ajaxStop(function() {
                                $('#table_employee_request').find('.leave_balance_input').each(function (i, obj) {
                                    let selected_request_type = $('#id_request_type').find(":selected").text();
                                    let selected_leave_type = $('#id_leave_type').find(":selected").text();

                                    if(selected_request_type != 'Attendance Correction' && response.description == selected_leave_type){
                                        $('#' + obj.id).val(response.leave_balance_remaining_this_request);
                                    }
                                });
                            });
                        } else {
                            $("#edit_button").css("display","block");
                            $("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').addClass('editForm');
                        }
                    } 
					
                    $('#id_request_header').val(response.id_request_header).trigger('change');
                    $('#reference_number').val(response.reference_number).trigger('change');
                    $('#id_employee_request').val(response.id_employee_request).trigger('change');
					$('#start_date').val(response.start_date).trigger('change');
                    $('#end_date').val(response.end_date).trigger('change');
                    $('#note').val(response.note).trigger('change');
					$('#enable_approval, .checkfield').attr('readonly', true);
				//	$('#enable_approval:checkbox').on('change', function (e) {
					
						setTimeout(function () {				
							if(response.enable_approval == 1){		
									$('#enable_approval').prop('checked', true);
								//	$('#enable_approval').attr('readonly', 'readonly');									
								//	$('#id_approval').select2({disabled: false});
								if(global_classname != 'req_cancel'){	
									if(response.start_date == null && response.end_date == null){
										//	$('#id_approval').select2({disabled: false});
											$('#daterange').attr('readonly', true);
												$('#daterange').daterangepicker({
													uiLibrary: 'bootstrap4',
															autoApply: true,
															opens: 'center',
															locale: {
																  format: 'YYYY-MM-DD',
																  separator: '   to   ',
																  closeText: 'Clear',
																},
												}, function(start, end, label) {
													$("#start_date").val(start.format('YYYY-MM-DD'));
													$("#end_date").val(end.format('YYYY-MM-DD'));
																	
													});	   
									}
								
									else{
										//	$('#id_approval').select2({disabled: false});
											$('#daterange').attr('readonly', true);
												$('#daterange').daterangepicker({
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
									}
								}
								
							}
																
						}, 1000);
					
						setTimeout(function () {
							if(response.delegate_approval == 1){	
									$('#delegate_approval').prop('checked', true);	
								//	setTimeout(function () {
											$.getJSON('<?= url('employee/employee/employee_request/get_employee_delegate') ?>', function (data) {
												global_employee_delegation = data;
												$('#table_employee_request').find('.id_employee_delegation_input').each(function (i, obj) {
													$('#' + obj.id).empty();
													$('#' + obj.id).select2({
														disabled: false,
														data: global_employee_delegation
													});
												});
											});
								//	}, 1000);
								}
						}, 1500);
				//	}).trigger('change');
					
					$.getJSON('<?= url('employee/employee/employee_request/get_hierachy') . '?code=' ?>' + response.code, function (data) {
						$('#id_approval').select2({
							allowClear: true,
							data: data,
							});
						});		
									
					
                    setTimeout(function () {
                        $('#table_employee_request_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_request_detail_input').val(response.emprequest[index].id_request_detail);
                            $(this).find('.id_employee_input').val(response.emprequest[index].id_employee).trigger('change');
							
							if(global_code != 'Attendance_Correction'){
								$(this).find('.request_start_to_input').val(response.emprequest[index].request_start_to.split(' ')[0]).trigger('change');
								$(this).find('.request_end_to_input').val(response.emprequest[index].request_end_to.split(' ')[0]).trigger('change');
							}
							else{
								$(this).find('.request_start_to_input').val(response.emprequest[index].request_start_to).trigger('change');
								$(this).find('.request_end_to_input').val(response.emprequest[index].request_end_to).trigger('change');
							}
							
						//	get_workdays();
											
							$(this).find('.day_type_input').val(response.emprequest[index].day_type).trigger('change');
							$(this).find('.qty_days_input').val(response.emprequest[index].qty_days).trigger('change');
						//	$(this).find('.actual_daterange_input').val(response.emprequest[index].actual_start_to + ' to ' + response.emprequest[index].actual_end_to);
							$(this).find('.actual_start_to_input').val(response.emprequest[index].actual_start_to).trigger('change');
                            $(this).find('.actual_end_to_input').val(response.emprequest[index].actual_end_to).trigger('change');
							$(this).find('.id_employee_delegation_input').val(response.emprequest[index].id_employee_delegation).trigger('change');     
							
                        });     
					$('#id_leave_type').val(response.id_leave_type).trigger('change');
                    }, 3000);
						
						$('#link_tab_menu-status').click(function(){
							$.extend( true, $.fn.dataTable.defaults, {
							 columnDefs:false,
							 paging:false,
							 searching:false,
							 destroy: true,
							 dom: '<"toolbar">frtip',
							} );
		
							$('#table_approve_status_detail').DataTable({
							processing: true,
							serverSide: true,
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
					});
					
                    if(response.code_status == 'Revised'){
					   $('#id_request_type').val(response.id_request_type).attr('readonly', true).trigger('change');
                    } else {
                       $('#id_request_type').val(response.id_request_type).trigger('change'); 
                    }
                //    $('#attachment').val(response.attachment).trigger('change');
                    if(response.attachment_type != null){
                        if(response.attachment_type == 'image'){
                            if(response.attachment != null){
                                $('#attach').css('display','inline');
                            }
                            document.getElementById("attach").src = "data:image;base64,"+response.attachment;
                        }
                        else if(response.attachment_type == 'pdf'){
                            if(response.attachment != null){
                                $('#attach_pdf').css('display','inline');
                            }                       
                            document.getElementById("attach_pdf").href = "data:application/pdf;base64,"+response.attachment;
                        }  
                    } else {
                        if(response.attachment != null){
                            let file = response.storagePath;
                            let extension = file.match(/\.([^\./\?]+)($|\?)/)[1];
                            if(extension == 'pdf'){
                                $('#attach_pdf').css('display','inline').attr('href', file);
                            } else {
                                $('#attach').css('display','inline').attr('src', file);
                            }
                        }
                    }
					                 
                    $('#attachment_type').val(response.attachment_type).trigger('change');
                    $('#id_approval').val(response.id_approval).trigger('change');
                    $('#id_approval_status').val(response.id_approval_status).trigger('change');
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#select2status').val(response.status).trigger('change');
                    $('#id_overtime_type').val(response.id_overtime_type).trigger('change');														
                },
                complete: function(){
					setTimeout(function () {
						$('#loader').addClass('hidden');
					}, 4000)
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
			
}
</script>