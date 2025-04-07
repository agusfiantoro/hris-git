@extends('adminlte::page')
@section('title', 'Employee Data')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Employee Data</h5>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-primary" onclick="loadreportptkp()"><i class="fas fa-file"></i> Report PTKP</button>
                    <button type="button" class="btn btn-sm btn-primary" onclick="loadreport()"><i class="fas fa-file"></i> Report</button>
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Employee Data</button>
                </div>
            </div>

            <div class="card-body">
            	<div class="form-group row">
            		<label class="col-md-2 col-form-label">Status :</label>
                    <div class="col-md-4">
                        <select id="employee_status" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
                        </select>
                    </div>

                    <label class="col-md-2 col-form-label">Select Employee :</label>
                    <div class="col-md-4">
                        <select id="employee_search" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-12 text-right">
                        <button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>

                <div class="div_datatable" style="display:none;"> 
					<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
						<br>
						<br>
	                <table id="employee_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
	                    <thead>
	                        <tr>				   
	                            <th></th>
	                            <th></th>
	                            <th>No</th>
	                            <th>NIK</th>
	                            <th>Name</th>
	                            <th>Email</th>
	                            <th>Join Date</th>
	                            <th data-priority="2">Position</th>
	                            <th>Department</th>
	                            <th>Principal</th>
	                            <th data-priority="4">Region</th>
	                            <th data-priority="5">Branch</th>
	                            <th>Job Grade</th>
	                            <th>Status</th>
	                            <th data-priority="3">Employee Status</th>
	                            <th>Resign Date</th>
	                            <th data-priority="1" width=300>Action</th>
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

<div class="modal fade" id="modal_form_employee"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="card modal-content">
            <form method="post" id="employeeForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Employee Data</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 500px;overflow-y: auto;">
                    <div class="row">
						<div class="col-md-2"> 
								<div class="input-group">
									 <div class="circle" style="font-size: 80px;">
									   <!-- User Profile Image -->
									   <img class="profile-pic" id="attach"/>
									   <!-- Default Image -->
									   <i style="margin-left:15px;" class="fa fa-user"></i>
									 </div>
									 <div class="p-image">
									   <a href="#"><i class="fa fa-camera upload-button"></i></a>
										<input type="file"  name="image_attachment" id="image_attachment" class="file-upload" />
										<input id="file_name" name="file_name" type="hidden">
									 </div>
									 <i style="font-size:12px;">Upload Photo (jpg,jpeg,png)</i>
										<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="image_attachmentError">
											<strong></strong>
										</span>
									<progress id="progressBar" value="0" max="100" style="width:80%;"></progress>
									  <label id="status_bar"></label>
									  <b id="loaded_n_total" class="text-success"></b>
									  <b id="loaded_failed" class="text-danger"></b>
								 </div>
						</div>
						<div class="col-md-5">                           
							<!-- div class="row">
                                <label class="col-sm-4 col-form-label">NIK</label>
                                <div class="col-sm-8">
									<div class="input-group">
										<input name="id_employee" id="id_employee" type="hidden">		
										<input type="text" name="nik_employee" id="nik_employee" readonly="readonly" class="form-control form-control-sm" style="border-radius:5px;">
										 <button type="button" id="nikcode" onclick="getcode()" class="btn btn-sm btn-success fa fa-refresh form-control-sm" style="padding:0 10px 0 10px;margin-left:5px;border-radius:4px;"></button>
										<span class="invalid-feedback" role="alert" id="nik_employeeError">
											<strong></strong>
										</span>   									
									</div>
                                </div>
                            </div -->
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee Name</label>
								<div class="col-sm-8">
									<input name="id_employee" id="id_employee" type="hidden">
									<input name="nik_employee" id="nik_employee" type="hidden">
                                    <input type="text" name="name" id="name" class="form-control form-control-sm" style="width: 100%;text-transform: uppercase;">
                                    <span class="invalid-feedback" role="alert" id="nameError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Work Address</label>
								<div class="col-sm-8">
                                    <input type="text" name="work_address" id="work_address" class="form-control form-control-sm" style="width: 100%;">                                  
                                    <span class="invalid-feedback" role="alert" id="work_addressError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Work Phone</label>
                                <div class="col-sm-8">
                                    <input type="text" name="work_phone" id="work_phone" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="work_phoneError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employment Status</label>
								<div class="col-sm-8">
                                    <select name="id_employment_status" id="id_employment_status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employment_statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>							
                        </div>
						<div class="col-md-5">							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Point of Recruit</label>
                                <div class="col-sm-8">
                                    <input type="text" name="home_base" id="home_base" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="home_baseError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Work Email</label>
                                <div class="col-sm-8">
                                    <input type="text" name="work_mail" id="work_mail" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="work_mailError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Note</label>
								<div class="col-sm-8">
                                    <input type="text" name="additional_note" id="additional_note" class="form-control form-control-sm" style="width: 100%;">
                                    <span class="invalid-feedback" role="alert" id="additional_noteError">
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
						</div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_employee_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_menu-work" data-toggle="pill" href="#menu-work" role="tab" aria-controls="link_tab_menu-work" aria-selected="true">Work Information<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-private" data-toggle="pill" href="#menu-private" role="tab" aria-controls="link_tab_menu-private" aria-selected="true">Private Information<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-bank" data-toggle="pill" href="#menu-bank" role="tab" aria-controls="link_tab_menu-bank" aria-selected="true">Bank<br><span class="error-tab text-red"></span></a>
                                </li>								
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-insurance" data-toggle="pill" href="#menu-insurance" role="tab" aria-controls="link_tab_menu-insurance" aria-selected="true">Insurance<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-leave" data-toggle="pill" href="#menu-leave" role="tab" aria-controls="link_tab_menu-leave" aria-selected="true">Leaves<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-learning" data-toggle="pill" href="#menu-learning" role="tab" aria-controls="link_tab_menu-learning" aria-selected="true">Learning History<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-career" data-toggle="pill" href="#menu-career" role="tab" aria-controls="link_tab_menu-career" aria-selected="true">Career History<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-awdcp" data-toggle="pill" href="#menu-awdcp" role="tab" aria-controls="link_tab_menu-awdcp" aria-selected="true">Award & Dicipline<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-doc" data-toggle="pill" href="#menu-doc" role="tab" aria-controls="link_tab_menu-doc" aria-selected="true">Document<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-boarding" data-toggle="pill" href="#menu-boarding" role="tab" aria-controls="link_tab_menu-boarding" aria-selected="true">On/Off Boarding<br><span class="error-tab text-red"></span></a>
                                </li>
								<li class="nav-item">
                                    <a class="nav-link" id="link_tab_menu-history" data-toggle="pill" href="#menu-history" role="tab" aria-controls="link_tab_menu-history" aria-selected="true">History<br><span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_employee_content" style="font-size:12px">
                                <div class="tab-pane fade show active" style="font-size:14px" id="menu-work" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div style="font-size:16px;font-weight:bold;margin-bottom:5px;">Works</div>
                                    <div class="row">									
										<div class="col-md-6">                           											
											<div class="row">
												<label class="col-sm-4 col-form-label">Work Hours</label>
													<div class="col-sm-8">
														<select name="id_shift_group" id="id_shift_group" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_shift_groupError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Leave Group</label>
													<div class="col-sm-8">
														<select name="id_leave" id="id_leave" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_leaveError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">PTKP Status</label>
													<div class="col-sm-8">
														<div class="input-group">
															<input name="ptkp_date_update" id="ptkp_date_update" type="hidden">
															<input type="text" name="ptkp_status" id="ptkp_status" class="form-control form-control-sm" style="border-radius:5px;" readonly>
															<button type="button" id="ptkpcode" onclick="browse_ptkp()" class="btn btn-sm btn-success form-control-sm" style="padding:0 10px 0 10px;margin-left:5px;border-radius:4px;"><span class="far fa-list-alt"></span></button>
															<span class="invalid-feedback" role="alert" id="ptkp_statusError">
																<strong></strong>
															</span>
														</div>														
														<!-- select name="ptkp_status" id="ptkp_status" class="form-control form-control-sm">
															<option value="TK0">TK0</option>
															<option value="TK1">TK1</option>
															<option value="TK2">TK2</option>
															<option value="TK3">TK3</option>
															<option value="K0">K0</option>
															<option value="K1">K1</option>
															<option value="K2">K2</option>
															<option value="K3">K3</option>															
														</select -->
														
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">ID Finger</label>
												<div class="col-sm-8">
													<input type="text" name="id_finger" id="id_finger" class="form-control form-control-sm">
													<span class="invalid-feedback" role="alert" id="id_fingerError">
														<strong></strong>
													</span>
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">ID User</label>
												<div class="col-sm-8">
													<select name="id_user" id="id_user" class="id_user form-control form-control-sm select2" style="width: 100%;">
													</select>
														<span class="invalid-feedback" role="alert" id="id_userError">
														<strong></strong>
													</span>
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Sales Code</label>
												<div class="col-sm-8">
													<input type="text" name="sales_code" id="sales_code" class="form-control form-control-sm">
													<span class="invalid-feedback" role="alert" id="sales_codeError">
														<strong></strong>
													</span>
												</div>
											</div>	
										</div>
										<div class="col-md-6">
											<div class="row">
												<label class="col-sm-4 col-form-label">Work Time Zone</label>
												<div class="col-sm-8">
														<select name="id_timezone" id="id_timezone" class="form-control form-control-sm select2" style="width: 100%;border-radius:5px;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_timezoneError">
															<strong></strong>
														</span>
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Join Date</label>
												<div class="col-sm-8">
													<input type="text" name="join_date" id="join_date" class="form-control form-control-sm">
													<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="join_dateError">
														<strong></strong>
													</span>
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Expired Date</label>
												<div class="col-sm-8">
													<input type="text" name="expired_date" id="expired_date" class="form-control form-control-sm">
													<span class="invalid-feedback" role="alert" id="expired_dateError">
														<strong></strong>
													</span>
												</div>
											</div>	
											<div class="row">
												<label class="col-sm-4 col-form-label">Permanent Date</label>
												<div class="col-sm-8">
													<input type="text" name="permanent_date" id="permanent_date" class="form-control form-control-sm">
													<span class="invalid-feedback" role="alert" id="permanent_dateError">
														<strong></strong>
													</span>
												</div>
											</div>		
											<div class="row">
												<label class="col-sm-4 col-form-label">Resign Date</label>
												<div class="col-sm-8">
													<input type="text" name="resign_date" id="resign_date" class="form-control form-control-sm">
													<span class="invalid-feedback" role="alert" id="resign_dateError">
														<strong></strong>
													</span>
												</div>
											</div>	
										</div>
									</div>	
									<br>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Job Position</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_job"><span class="fas fa-plus"></span> Add Job Position</button>
                                        </div>
                                        <div class="col-md-12" style="overflow-y: scroll">
                                            <table id="table_job" style="width:1400px;" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <!-- th style="white-space:nowrap;">Job Position</th -->
														<th style="white-space:nowrap;">Position Route</th>
                                                        <!-- th style="white-space:nowrap;">Position Detail</th -->
                                                        <th style="white-space:nowrap;">Branch</th>
                                                        <th style="white-space:nowrap;">Location</th>
                                                        <th style="white-space:nowrap;">Principal</th>
                                                        <th style="white-space:nowrap;">Supervisor Position</th>
                                                        <th style="white-space:nowrap;">Supervisor Name</th>
                                                        <th style="white-space:nowrap;">Department</th>
                                                        <th style="white-space:nowrap;">Replace Employee</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_job_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_jobError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;" id="new_employee_contract">Contract History</div>
                                        </div>
                                        <div class="col-md-12" style="overflow-y: scroll">
                                            <table style="width:100%;" id="table_contract" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Contract Number</th>
                                                        <th style="white-space:nowrap;">Contract Category</th>
                                                        <th style="white-space:nowrap;">Working Schedule</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                    </tr>
                                                </thead>
                                                <!-- tbody id="table_contract_body">
                                                </tbody -->
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_contractError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="tab-pane fade" style="font-size:14px" id="menu-private" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div style="font-size:16px;font-weight:bold;margin-bottom:5px;">Private Contact</div>
                                    <div class="row">									
										<div class="col-md-6">                           											
											<div class="row">
												<label class="col-sm-4 col-form-label">Current Address</label>
													<div class="col-sm-8">
														<input type="text" name="address_home" id="address_home" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="address_homeError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">KTP Address</label>
													<div class="col-sm-8">
														<input type="text" name="idcard_address" id="idcard_address" class="form-control form-control-sm">
														<span class="invalid-feedback" role="alert" id="idcard_addressError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Private Mail</label>
													<div class="col-sm-8">
														<input type="text" name="private_mail" id="private_mail" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="private_mailError">
															<strong></strong>
														</span>
													</div>
											</div>																						
											<div class="row">
												<label class="col-sm-4 col-form-label">Marital Status</label>
												<div class="col-sm-8">
													<select name="marital" id="marital" class="form-control form-control-sm select2">
														<option value="Single">Single</option>
														<option value="Married">Married</option>
														<!-- <option value="Divorce">Divorce</option> -->
														<option value="Widow">Widow</option>
														<option value="Widower">Widower</option>
													</select>
													<span class="invalid-feedback" role="alert" id="maritalError">
														<strong></strong>
													</span>
												</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Mobile Phone</label>
													<div class="col-sm-8">
														<input type="text" name="mobile_phone" id="mobile_phone" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="mobile_phoneError">
															<strong></strong>
														</span>
													</div>
											</div>	
											<div class="row">
												<label class="col-sm-4 col-form-label">Date of Birth</label>
													<div class="col-sm-8">
														<input type="text" name="birthdate" id="birthdate" class="form-control form-control-sm">
														<span class="invalid-feedback" role="alert" id="birthdateError">
															<strong></strong>
														</span>
													</div>
											</div>		
											<div class="row">
												<label class="col-sm-4 col-form-label">Place of Birth</label>
													<div class="col-sm-8">
														<input type="text" name="place_of_birth" id="place_of_birth" class="form-control form-control-sm">
														<span class="invalid-feedback" role="alert" id="place_of_birthError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Country of Birth</label>
													<div class="col-sm-8">
														<select name="id_country_of_birth" id="id_country_of_birth" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_country_of_birthError">
															<strong></strong>
														</span>
													</div>
											</div>	
											<div class="row">
												<label class="col-sm-4 col-form-label">Spouse Name</label>
													<div class="col-sm-8">
														<input type="text" name="spouse_complete_name" id="spouse_complete_name" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="spouse_complete_nameError">
															<strong></strong>
														</span>
													</div>
											</div>		
											<div class="row">
												<label class="col-sm-4 col-form-label">Spouse Birth Date</label>
													<div class="col-sm-8">
														<input type="text" name="spouse_birthdate" id="spouse_birthdate" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="spouse_birthdateError">
															<strong></strong>
														</span>
													</div>
											</div>
											
										</div>
										<div class="col-md-6">     
											<div class="row">
												<label class="col-sm-4 col-form-label">ID Number/KTP</label>
													<div class="col-sm-8">
														<div class="input-group">
															<input type="text" name="identification_number" id="identification_number" class="form-control form-control-sm" style="border-radius:5px;">
															 <button type="button" id="ktpcode" onclick="getktpcode()" class="btn btn-sm btn-success form-control-sm" style="padding:0 10px 0 10px;margin-left:5px;border-radius:4px;"><i>Check KTP</i></button>
															 <button id="ktpcode_loading" class="btn btn-sm btn-success form-control-sm" style="padding:0 10px 0 10px;margin-left:5px;border-radius:4px;display:none;">
																<i class="fa fa-refresh fa-pulse"></i>
															</button>
															<span class="invalid-feedback" role="alert" id="identification_numberError">
																<strong></strong>
															</span>   									
														</div>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">NPWP Number</label>
													<div class="col-sm-8">
														<input type="text" name="npwp_number" id="npwp_number" class="form-control form-control-sm">
														<span class="invalid-feedback" role="alert" id="npwp_numberError">
															<strong></strong>
														</span>
													</div>
											</div>											
											<div class="row">
												<label class="col-sm-4 col-form-label">Gender</label>
													<div class="col-sm-8">
														<select name="gender" id="gender" class="form-control form-control-sm select2">
															<option value="M">Male</option>
															<option value="F">Female</option>													
														</select>
														<span class="invalid-feedback" role="alert" id="genderError">
															<strong></strong>
														</span>
													</div>
											</div>
											
											<div class="row">
												<label class="col-sm-4 col-form-label">Nationality</label>
													<div class="col-sm-8">
														<select name="id_country" id="id_country" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_countryError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Religion</label>
													<div class="col-sm-8">
														<select name="id_religion" id="id_religion" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_religionError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Emergency Phone</label>
													<div class="col-sm-8">
														<input type="text" name="emergency_phone" id="emergency_phone" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="emergency_phoneError">
															<strong></strong>
														</span>
													</div>
											</div>	
											<div class="row">
												<label class="col-sm-4 col-form-label">Emergency Contact</label>
													<div class="col-sm-8">
														<input type="text" name="emergency_contact" id="emergency_contact" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="emergency_contactError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Number Of Children</label>
													<div class="col-sm-8">
														<input type="number" onkeypress="return /[0-9]/i.test(event.key)" name="number_of_children" id="number_of_children" value="0" class="form-control form-control-sm" style="width: 100%;"/>
														<span class="invalid-feedback" role="alert" id="number_of_childrenError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Vaccine Status</label>
													<div class="col-sm-8">
														<select name="id_vaccination_status" id="id_vaccination_status" class="form-control form-control-sm select2" style="width: 100%;">
														</select>
														<span class="invalid-feedback" role="alert" id="id_vaccination_statusError">
															<strong></strong>
														</span>
													</div>
											</div>
											<div class="row">
												<label class="col-sm-4 col-form-label">Date of Vaccine</label>
													<div class="col-sm-8">
														<input type="text" name="lasted_date_vaccine" id="lasted_date_vaccine" class="form-control form-control-sm">
														<span class="invalid-feedback" role="alert" id="lasted_date_vaccineError">
															<strong></strong>
														</span>
													</div>
											</div>
										</div>
										
									</div>	
									<br>
                                    <div class="row">
										 <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Education Background</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_education"><span class="fas fa-plus"></span> Add Education</button>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_education" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Major</th>
                                                        <th style="white-space:nowrap;">University/School</th>
                                                        <th style="white-space:nowrap;">Level</th>
														<th style="white-space:nowrap;">City</th>
                                                        <th style="white-space:nowrap;">Start Year</th>
                                                        <th style="white-space:nowrap;">End Year</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_education_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_educationError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Family Information</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_family"><span class="fas fa-plus"></span> Add Family</button>
                                        </div>									
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_family" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Family Name</th>
                                                        <th style="white-space:nowrap;">Gender</th>
                                                        <th style="white-space:nowrap;">Relationship</th>                                                      
                                                        <th style="white-space:nowrap;">Phone Number</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_family_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_familyError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Work Experience</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_ex"><span class="fas fa-plus"></span> Add Work Experience</button>
                                        </div>									
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_ex" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Job Title</th>
                                                        <th style="white-space:nowrap;">Company</th>
                                                        <th style="white-space:nowrap;">Location</th>                                                      
                                                        <th style="white-space:nowrap;">Start Date</th>
                                                        <th style="white-space:nowrap;">End Date</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_ex_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_exError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Skills</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_skill"><span class="fas fa-plus"></span> Add Skill</button>
                                        </div>									
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_skill" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Skill Name</th>
                                                        <th style="white-space:nowrap;">Skill Level</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_skill_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_skillError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Certification</div>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_cert"><span class="fas fa-plus"></span> Add Certification</button>
                                        </div>									
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_cert" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Certification Name</th>
                                                        <th style="white-space:nowrap;">Certified By</th>
                                                        <th style="white-space:nowrap;">Issued Year</th>
                                                        <th style="white-space:nowrap;">Validity Period</th>
														{{-- <th style="white-space:nowrap;">Attachment</th> --}}
                                                    </tr>
                                                </thead>
												<tbody id="table_cert_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_certError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
                               
							    <div class="tab-pane fade" style="font-size:14px" id="menu-bank" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <div class="row">
										 <div class="col-md-12" style="margin-bottom: 10px">
											<br>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_bank"><span class="fas fa-plus"></span> Add Bank Data</button>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_bank" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Bank Name</th>
                                                        <th style="white-space:nowrap;">Bank Account</th>
                                                        <th style="white-space:nowrap;">Account Name</th>
														<th style="white-space:nowrap;">Currency</th>
                                                        <th style="white-space:nowrap;">Default</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_bank_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_bankError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
                               
							   <div class="tab-pane fade" style="font-size:14px" id="menu-insurance" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <div class="row">
										 <div class="col-md-12" style="margin-bottom: 10px">
											<br>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_insurance"><span class="fas fa-plus"></span> Add Insurance Data</button>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_insurance" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Insurance Name</th>
                                                        <th style="white-space:nowrap;">Insurance Number</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                        <th style="white-space:nowrap;">Beneficiary Name</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_insurance_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_insuranceError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
                               
							   <div class="tab-pane fade" style="font-size:14px" id="menu-leave" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <div class="row">	
										 <div class="col-md-12" style="margin-bottom: 10px">
										 <br>
                                            <button type="button" onclick="genleave()" class="pull-right btn btn-xs btn-success"><span class="fa fa-refresh"></span> Generate Leaves</button>
                                            <button type="button" onclick="addLeave()" class="pull-right btn btn-xs btn-primary" style="margin-right: 10px;"><span class="fa fa-plus"></span> Add Leave</button>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_leave" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Leave Type</th>
                                                        <th style="white-space:nowrap;">Leave Quota</th>
                                                        <th style="white-space:nowrap;">Used Leave</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
												
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_leaveError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
                              
								<div class="tab-pane fade" style="font-size:14px" id="menu-learning" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;" id="new_employee_career">Learning History</div>
                                        </div>
                                        <div class="col-md-12" style="overflow: auto">
                                           <table style="width:100%;" id="table_learning" class="table table-striped table-bordered table-hover datatable">
												<thead>
													<tr>
														<th style="white-space:nowrap;">No.</th>
														<th style="white-space:nowrap;">Program</th>
														<th style="white-space:nowrap;">Courses</th>
														<th style="white-space:nowrap;">Score</th>														
														<th style="white-space:nowrap;">Status</th>
														<th style="white-space:nowrap;">Hit/Miss</th>
														<th style="white-space:nowrap;">Rating</th>
														<th style="white-space:nowrap;">Training Date</th>
													</tr>
												</thead>
											</table>                                      
                                        </div>
                                    </div>
                                </div>
								
								<div class="tab-pane fade" style="font-size:14px" id="menu-career" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;" id="new_employee_career">Career History</div>
                                        </div>
                                        <div class="col-md-12" style="overflow: auto">
                                            <table style="width:100%;" id="table_career" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Career Number</th>
                                                        <th style="white-space:nowrap;">Career Transition</th>
                                                        <th style="white-space:nowrap;">Transaction Type</th>
                                                        <th style="white-space:nowrap;">Employee Status</th>
                                                        <th style="white-space:nowrap;">Position Detail</th>
                                                        <th style="white-space:nowrap;">Position Routing</th>
                                                        <th style="white-space:nowrap;">Job Grade</th>
                                                        <th style="white-space:nowrap;">Job Status</th>
                                                        <th style="white-space:nowrap;">Location</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                    </tr>
                                                </thead>                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_careerError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="tab-pane fade" style="font-size:14px" id="menu-awdcp" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;" id="new_employee_awdcp">Award & Dicipline</div>
                                        </div>
                                        <div class="col-md-12" style="overflow: auto">
                                            <table id="table_awdcp"  style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Reference Number</th>
                                                        <th style="white-space:nowrap;">Type</th>
                                                        <th style="white-space:nowrap;">Description</th>                                                       
                                                        <th style="white-space:nowrap;">Reference Date</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                    </tr>
                                                </thead>
                                                
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_awdcpError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
								<div class="tab-pane fade" style="font-size:14px" id="menu-doc" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <div class="row">
										 <div class="col-md-12" style="margin-bottom: 10px">
											<br>
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_doc"><span class="fas fa-plus"></span> Add Document</button>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_doc" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="min-width: 210px;">Document Name</th>
                                                        <th style="white-space:nowrap;">Document Number</th>
                                                        <th style="white-space:nowrap;">Attachment</th>
                                                        <th style="white-space:nowrap;">File Name</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Expired Date</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_doc_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_docError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
                              
								<div class="tab-pane fade" style="font-size:14px" id="menu-boarding" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>										
                                    <div class="row">
										 <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">On Boarding</div>
                                        </div>
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_onboarding" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Name</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Remark</th>
														<th style="white-space:nowrap;text-align:center;">File Name</th>
                                                        <th style="white-space:nowrap;text-align:center;">Completed</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_onboarding_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_onboardingError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									<div class="row">
										<div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;">Off Boarding</div>
											<button style="display:none;" type="button" class="pull-right btn btn-xs btn-primary" id="new_employee_offboarding"><span class="fas fa-plus"></span> Add Off Boarding</button>
                                        </div>									
                                        <div class="col-12">
                                            <table style="width:100%;" id="table_offboarding" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Name</th>
                                                        <th style="white-space:nowrap;">Effective Date</th>
                                                        <th style="white-space:nowrap;">Remark</th>
														<th style="white-space:nowrap;text-align:center;">File Name</th>
                                                        <th style="white-space:nowrap;text-align:center;">Completed</th>
                                                    </tr>
                                                </thead>
												<tbody id="table_offboarding_body">
                                                </tbody>
                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_offboardingError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
									
                                </div>
								
								<div class="tab-pane fade" style="font-size:14px" id="menu-history" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
									<div class="row">	
                                        <div class="col-md-12" style="margin-bottom: 10px">
											<div class="pull-left" style="font-size:16px;font-weight:bold;" id="new_employee_career">Employee History</div>
                                        </div>
                                        <div class="col-md-12" style="overflow: auto">
                                            <table style="width:100%;" id="table_history" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr>
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">NIK</th>
                                                        <th style="white-space:nowrap;">Name</th>
                                                        <th style="white-space:nowrap;">Join Date</th>
                                                        <th style="white-space:nowrap;">Department</th>
                                                        <th style="white-space:nowrap;">Position Routing</th>
                                                        <th style="white-space:nowrap;">Company</th>
                                                        <th style="white-space:nowrap;">Status</th>
                                                        <th style="white-space:nowrap;">Resign Date</th>
                                                        <th style="white-space:nowrap;">Terminate Reason</th>
                                                    </tr>
                                                </thead>                                              
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_historyError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
					<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>            
                </div>
            </form>
			<div style="display:none;">
                <table id="sample_table_employee_job">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
						<td>
							<input name="job[0][id_position_detail]" id="job_0_id_position_detail" type="hidden" class="form-control form-control-sm id_position_detail_input">
                            <input type="hidden" name="job[0][job_position]" id="job_0_job_position" class="form-control form-control-sm job_position_input" style="height:55px;resize:none;" readonly> 
								<span class="invalid-feedback job_position_input_error" role="alert" id="job_0_job_positionError">
                                    <strong></strong>
                                </span>
								
							<input type="hidden" id="job_0_position_detail" class="form-control form-control-sm position_detail_input" style="height:55px;resize:none;" readonly>
							
							<textarea type="text" id="job_0_position_routing" class="form-control form-control-sm position_routing_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>					                        
						
						<td>
                            <textarea type="text" name="job[0][branch]" id="job_0_branch" class="form-control form-control-sm branch_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>
						<td>
                            <textarea type="text" name="job[0][location]" id="job_0_location" class="form-control form-control-sm location_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>
						<td>
                            <textarea type="text" id="job_0_principal" class="form-control form-control-sm principal_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>
						<td>
							<textarea type="text" id="job_0_parent_position_detail" class="form-control form-control-sm parent_position_detail_input" style="height:55px;resize:none;" readonly></textarea>
						</td>
						<td>
							<textarea type="text" id="job_0_name_supervisor" class="form-control form-control-sm name_supervisor_input" style="height:55px;resize:none;" readonly></textarea>
						</td>
						<td>
                            <textarea type="text" id="job_0_department" class="form-control form-control-sm department_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>
						<td>
                            <textarea type="text" id="job_0_emp_name" class="form-control form-control-sm emp_name_input" style="height:55px;resize:none;" readonly></textarea>
                        </td>
						<td style="width:100px;vertical-align:middle;">
							<center>
								<button type="button" class="add-record btn btn-xs btn-primary" data-id="0" onclick="browse_job(0)"><span class="far fa-list-alt"></span></button>&nbsp;
								<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
							</center>
						</td>
                    </tr>
                </table>				
				<table id="sample_table_employee_education">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="edu[0][id_education_employee]" id="edu_0_id_education_employee" type="hidden" class="form-control form-control-sm id_education_employee_input">
                                <input type="text" name="edu[0][major]" id="edu_0_major" class="form-control form-control-sm major_input">                      
                                <span class="invalid-feedback major_input_error" role="alert" id="edu_0_majorError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <input type="text" name="edu[0][education_name]" id="edu_0_education_name" class="form-control form-control-sm education_name_input">
                                <span class="invalid-feedback education_name_input_error" role="alert" id="edu_0_education_nameError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <select type="text" name="edu[0][id_education_level]" id="edu_0_id_education_level" class="form-control form-control-sm select2 id_education_level_input">
								</select>
                                <span class="invalid-feedback id_education_level_input_error" role="alert" id="edu_0_id_education_levelError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="edu[0][education_city]" id="edu_0_education_city" class="form-control form-control-sm education_city_input">
                                <span class="invalid-feedback education_city_input_error" role="alert" id="edu_0_education_cityError">
                                    <strong></strong>
                                </span>					
                        </td>	
						<td>									
                                <input type="text" name="edu[0][start_year]" id="edu_0_start_year" class="form-control form-control-sm start_year_input">
                                <span class="invalid-feedback start_year_input_error" role="alert" id="edu_0_start_yearError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="edu[0][end_year]" id="edu_0_end_year" class="form-control form-control-sm end_year_input">
                                <span class="invalid-feedback end_year_input_error" role="alert" id="edu_0_end_yearError">
                                    <strong></strong>
                                </span>					
                        </td>
										
                        <td>
                    <center>
                        <button type="button" class="delete-edu btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
				<table id="sample_table_employee_family">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="fam[0][id_family_employee]" id="fam_0_id_family_employee" type="hidden" class="form-control form-control-sm id_family_employee_input">
                                <input type="text" name="fam[0][family_name]" id="fam_0_family_name" class="form-control form-control-sm family_name_input">                      
                                <span class="invalid-feedback family_name_input_error" role="alert" id="fam_0_family_nameError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <select name="fam[0][gender]" id="fam_0_gender" class="form-control form-control-sm select2 gender_input" style="width: 100%;"></select>
                                <span class="invalid-feedback gender_input_error" role="alert" id="fam_0_genderError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="fam[0][relationship]" id="fam_0_relationship" class="form-control form-control-sm relationship_input">                      
                                <span class="invalid-feedback relationship_input_error" role="alert" id="fam_0_relationshipError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="fam[0][mobile_phone]" id="fam_0_mobile_phone" class="form-control form-control-sm mobile_phone_input">                      
                                <span class="invalid-feedback mobile_phone_input_error" role="alert" id="fam_0_mobile_phoneError">
                                    <strong></strong>
                                </span>					
                        </td>
								
                        <td>
                    <center>
                        <button type="button" class="delete-fam btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				<table id="sample_table_employee_ex">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="ex[0][id_experience_employee]" id="ex_0_id_experience_employee" type="hidden" class="form-control form-control-sm id_experience_employee_input">
                                <input type="text" name="ex[0][position_name]" id="ex_0_position_name" class="form-control form-control-sm position_name_input">
                                <span class="invalid-feedback position_name_input_error" role="alert" id="ex_0_position_nameError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <input type="text" name="ex[0][company_name]" id="ex_0_company_name" class="form-control form-control-sm company_name_input">                      
                                <span class="invalid-feedback company_name_input_error" role="alert" id="ex_0_company_nameError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="ex[0][company_city]" id="ex_0_company_city" class="form-control form-control-sm company_city_input">                      
                                <span class="invalid-feedback company_city_input_error" role="alert" id="ex_0_company_cityError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="ex[0][start_year]" id="ex_0_start_year_ex" class="form-control form-control-sm start_year_ex_input">                      
                                <span class="invalid-feedback start_year_ex_input_error" role="alert" id="ex_0_start_year_exError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="ex[0][end_year]" id="ex_0_end_year_ex" class="form-control form-control-sm end_year_ex_input">                      
                                <span class="invalid-feedback end_year_ex_input_error" role="alert" id="ex_0_end_year_exError">
                                    <strong></strong>
                                </span>					
                        </td>
								
                        <td>
                    <center>
                        <button type="button" class="delete-ex btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				
				<table id="sample_table_employee_skill">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="skill[0][id_skill_employee]" id="skill_0_id_skill_employee" type="hidden" class="form-control form-control-sm id_skill_employee_input">
                                <input type="text" name="skill[0][skill_name]" id="skill_0_skill_name" class="form-control form-control-sm skill_name_input">
                                <span class="invalid-feedback skill_name_input_error" role="alert" id="skill_0_skill_nameError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <select name="skill[0][skill_level]" id="skill_0_skill_level" class="form-control form-control-sm select2 skill_level_input" style="width: 100%;"></select>
                                <span class="invalid-feedback skill_level_input_error" role="alert" id="skill_0_skill_levelError">
                                    <strong></strong>
                                </span>					
                        </td>				
                    <td>
                    <center>
                        <button type="button" class="delete-skill btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				
				<table id="sample_table_employee_cert">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="cert[0][id_certification_employee]" id="cert_0_id_certification_employee" type="hidden" class="form-control form-control-sm id_certification_employee_input">
                                <input type="text" name="cert[0][certification_name]" id="cert_0_certification_name" class="form-control form-control-sm certification_name_input">
                                <span class="invalid-feedback certification_name_input_error" role="alert" id="cert_0_certification_nameError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>									
                                <input type="text" name="cert[0][certified_by]" id="cert_0_certified_by" class="form-control form-control-sm certified_by_input">
                                <span class="invalid-feedback certified_by_input_error" role="alert" id="cert_0_certified_byError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="cert[0][years_issued]" id="cert_0_years_issued" class="form-control form-control-sm years_issued_input">
                                <span class="invalid-feedback years_issued_input_error" role="alert" id="cert_0_years_issuedError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="cert[0][validity_period]" id="cert_0_validity_period" class="form-control form-control-sm validity_period_input">
                                <span class="invalid-feedback validity_period_input_error" role="alert" id="cert_0_validity_periodError">
                                    <strong></strong>
                                </span>					
                        {{-- </td>
						<td>						
							<input type="hidden" name="cert[0][attachment]" id="cert_0_attachment" class="certification_attachment">			
							<a class="certification_attachment_link"></a>			 --}}
					</td>
						
                    <td>
                    <center>
                        <button type="button" class="delete-cert btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				
				<table id="sample_table_employee_bank">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="bank[0][id_bank_employee]" id="bank_0_id_bank_employee" type="hidden" class="form-control form-control-sm id_bank_employee_input">
								<input name="bank[0][bank_name]" id="bank_0_bank_name" type="hidden" class="form-control form-control-sm bank_name_input">
                                <select name="bank[0][id_bank]" id="bank_0_id_bank" class="form-control form-control-sm select2 id_bank_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_bank_input_error" role="alert" id="bank_0_id_bankError">
                                    <strong></strong>
                                </span>		
                        </td>
						
						<td>									
                                <input type="text" name="bank[0][bank_account]" id="bank_0_bank_account" class="form-control form-control-sm bank_account_input">
                                <span class="invalid-feedback bank_account_input_error" role="alert" id="bank_0_bank_accountError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="bank[0][account_name]" id="bank_0_account_name" class="form-control form-control-sm account_name_input">
                                <span class="invalid-feedback account_name_input_error" role="alert" id="bank_0_account_nameError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <select name="bank[0][bank_currency]" id="bank_0_bank_currency" class="form-control form-control-sm select2 bank_currency_input" style="width: 100%;"></select>
                                <span class="invalid-feedback bank_currency_input_error" role="alert" id="bank_0_bank_currencyError">
                                    <strong></strong>
                                </span>						
                        </td>
						<td>
								<input type="checkbox" name="bank[0][default_bank]" id="bank_0_default_bank" class="form-control form-control-sm default_bank_input" style="height:20px;margin-top:5px;">
								<span class="invalid-feedback default_bank_input_error" role="alert" id="bank_0_default_bankError">
									<strong></strong>
								</span>
                        </td>					
                    <td>
                    <center>
                        <button type="button" class="delete-bank btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				<table id="sample_table_employee_insurance">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="ins[0][id_insurance_employee]" id="ins_0_id_insurance_employee" type="hidden" class="form-control form-control-sm id_insurance_employee_input">
                                <select name="ins[0][id_insurance]" id="ins_0_id_insurance" class="form-control form-control-sm select2 id_insurance_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_insurance_input_error" role="alert" id="ins_0_id_insuranceError">
                                    <strong></strong>
                                </span>		
                        </td>
						
						<td>									
                                <input type="text" name="ins[0][emp_insurance_number]" id="ins_0_emp_insurance_number" class="form-control form-control-sm emp_insurance_number_input">
                                <span class="invalid-feedback emp_insurance_number_input_error" role="alert" id="ins_0_emp_insurance_numberError">
                                    <strong></strong>
                                </span>					
                        </td>
						
						<td>									
                                <input type="text" name="ins[0][effective_date]" id="ins_0_effective_date" class="form-control form-control-sm effective_date_input">
                                <span class="invalid-feedback effective_date_input_error" role="alert" id="ins_0_effective_dateError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="ins[0][expired_date]" id="ins_0_expired_date" class="form-control form-control-sm expired_date_input">
                                <span class="invalid-feedback expired_date_input_error" role="alert" id="ins_0_expired_dateError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" name="ins[0][beneficiary_name]" id="ins_0_beneficiary_name" class="form-control form-control-sm beneficiary_name_input">
                                <span class="invalid-feedback beneficiary_name_input_error" role="alert" id="ins_0_beneficiary_nameError">
                                    <strong></strong>
                                </span>					
                        </td>
											
                    <td>
                    <center>
                        <button type="button" class="delete-insurance btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				<table id="sample_table_employee_doc">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input name="doc[0][id_document_employee]" id="doc_0_id_document_employee" type="hidden" class="form-control form-control-sm id_document_employee_input">
								<input type="text" name="doc[0][document_name]" id="doc_0_document_name" class="form-control form-control-sm document_name_input">
                                <span class="invalid-feedback document_name_input_error" role="alert" id="doc_0_document_nameError">
                                    <strong></strong>
                                </span>		
                        </td>						
						<td>									
                               <input type="text" name="doc[0][document_number]" id="doc_0_document_number" class="form-control form-control-sm document_number_input">
                                <span class="invalid-feedback document_number_input_error" role="alert" id="doc_0_document_numberError">
                                    <strong></strong>
                                </span>						
                        </td>
						<td>	
							<div class="custom-file">
								 <input type="file" name="doc[0][attachment]" id="doc_0_attachment" class="custom-file-input form-control form-control-sm attachment_input" onchange="docfile(this,0)">
								 <span class="invalid-feedback attachment_input_error" role="alert" id="doc_0_attachmentError">
                                        <strong></strong>
                                    </span>
								  <div id="doc_0_docfile" class="custom-file-label doc_input"><i style="font-size:11px;">Max 300 kb</i></div>
								  <i style="font-size:11px;">File Type : pdf,jpg,jpeg,png</i>
							</div>				
                        </td>
						<td align="center">							
								  <a id="doc_0_attach" class="a_input" target="_blank"></a>
                        </td>
						<td>									
                               <input type="text" name="doc[0][effective_date]" id="doc_0_effective_date" class="form-control form-control-sm effective_date_input">
                                <span class="invalid-feedback effective_date_input_error" role="alert" id="doc_0_effective_dateError">
                                    <strong></strong>
                                </span>						
                        </td>
						<td>									
                               <input type="text" name="doc[0][expired_date]" id="doc_0_expired_date" class="form-control form-control-sm expired_date_input">
                                <span class="invalid-feedback expired_date_input_error" role="alert" id="doc_0_expired_dateError">
                                    <strong></strong>
                                </span>						
                        </td>
						
                    <td>
                    <center>
                        <button type="button" class="delete-doc btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>            
				<table id="sample_table_employee_onboarding">
						<tr id="">
							<td><span class="sn" style="vertical-align:middle;"></span></td>              
							<td>
									<input id="onboarding_0_id_checklist_employee" type="hidden" class="form-control form-control-sm id_checklist_employee_onboarding_input">
									<input id="onboarding_0_document_name" type="hidden" class="form-control form-control-sm document_name_onboarding_input">
									<select id="onboarding_0_id_checklist" class="form-control form-control-sm select2 id_checklist_onboarding_input" style="width: 100%;"></select>
									<span class="invalid-feedback id_checklist_onboarding_input_error" role="alert" id="onboarding_0_id_checklistError">
										<strong></strong>
									</span>		
							</td>						
							<td>									
									<input type="text" id="onboarding_0_effective_date" class="form-control form-control-sm effective_date_onboarding_input">
									<span class="invalid-feedback effective_date_input_error" role="alert" id="onboarding_0_effective_dateError">
										<strong></strong>
									</span>					
							</td>
							<td>									
									<input type="text" id="onboarding_0_remark" class="form-control form-control-sm remark_onboarding_input">
									<span class="invalid-feedback remark_onboarding_input_error" role="alert" id="onboarding_0_remarkError">
										<strong></strong>
									</span>					
							</td>	
							<td align="center">							
								  <a id="onboarding_0_attach" class="a_onboarding_input" target="_blank"></a>
							</td>
							<td align="center">
									<div id="onboarding_0_completed" class="completed_offboarding_input"></div>	
							</td>					
						<td>
						</td>
						</tr>
					</table>            
				<table id="sample_table_employee_offboarding">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>              
                        <td>
								<input id="offboarding_0_id_checklist_employee" type="hidden" class="form-control form-control-sm id_checklist_employee_offboarding_input">
								<input id="offboarding_0_document_name" type="hidden" class="form-control form-control-sm document_name_offboarding_input">
                                <select id="offboarding_0_id_checklist" class="form-control form-control-sm select2 id_checklist_offboarding_input" style="width: 100%;" disabled></select>
                                <span class="invalid-feedback id_checklist_offboarding_input_error" role="alert" id="offboarding_0_id_checklistError">
                                    <strong></strong>
                                </span>		
                        </td>						
						<td>									
                                <input type="text" id="offboarding_0_effective_date" class="form-control form-control-sm effective_date_offboarding_input" disabled>
                                <span class="invalid-feedback effective_date_offboarding_input_error" role="alert" id="offboarding_0_effective_dateError">
                                    <strong></strong>
                                </span>					
                        </td>
						<td>									
                                <input type="text" id="offboarding_0_remark" class="form-control form-control-sm remark_offboarding_input" disabled>
                                <span class="invalid-feedback remark_offboarding_input_error" role="alert" id="offboarding_0_remarkError">
                                    <strong></strong>
                                </span>					
                        </td>	
						<td align="center">							
								  <a id="offboarding_0_attach" class="a_offboarding_input" target="_blank"></a>
							</td>
						<td align="center">
								<div id="offboarding_0_completed" class="completed_offboarding_input"></div>								
                        </td>					
                    <td>
                    </td>
                    </tr>
                </table>            

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
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="browseModaljob" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-xl">
  <div class="modal-content"> 
   <div class="modal-header">
		<h5 class="modal-title">Search Position</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			<span aria-hidden="true">&times;</span>
		</button>
    </div>
   <div class="card-body">
					<table id="bro_table_job" style="width:1200px;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>
					   <th class="dtfc-fixed-left" style="white-space:nowrap;width:200px;">Position Detail</th>
					   <th class="dtfc-fixed-left" style="white-space:nowrap;width:200px;">Position Route</th>
					   <th data-priority="4" style="white-space:nowrap;">Branch</th>
					   <th style="white-space:nowrap;">Location</th>
					   <th style="white-space:nowrap;">Principal</th>
					   <th style="white-space:nowrap;">Supervisor Position</th>
					   <th data-priority="5" style="white-space:nowrap;">Direct Supervisor</th>
					   <th style="white-space:nowrap;">Department</th>
					   <th style="white-space:nowrap;">Assign Company</th>
					   <th data-priority="6" style="white-space:nowrap;">Replace Employee</th>
					   <th style="white-space:nowrap;">Transition Type</th>
					   <th data-priority="7" style="white-space:nowrap;">Effective Date</th>
					   <th class="dtfc-fixed-left">Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
					</table>
				</div>
   
     </div>
    </div>
</div>	

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Report Employee Data</h5>
			<button type="button" onclick="javascript:window.location.reload()" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-default advclose" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="myModalPtkp" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">Report PTKP Status</h5>
			<button type="button" onclick="javascript:window.location.reload()" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBodyPtkp">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-default advclose" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<div id="browseModalptkp" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content"> 
			<form method="post" id="ptkpForm">
				{{ csrf_field() }}
				<div class="modal-header">
					<h5 class="modal-title">Update PTKP</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<label class="col-sm-4 col-form-label">PTKP Status</label>
								<div class="col-sm-8">													
									<select name="ptkp_status_list" id="ptkp_status_list" class="form-control form-control-sm">
										<option value="TK0">TK0</option>
										<option value="TK1">TK1</option>
										<option value="TK2">TK2</option>
										<option value="TK3">TK3</option>
										<option value="K0">K0</option>
										<option value="K1">K1</option>
										<option value="K2">K2</option>
										<option value="K3">K3</option>															
									</select>	
									<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="ptkp_status_listError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Update Date</label>
								<div class="col-sm-8">
									<input type="text" name="ptkp_date" id="ptkp_date" class="form-control form-control-sm">
									<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="ptkp_dateError">
										<strong></strong>
									</span>
								</div>
							</div>
						</div>	
					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-primary" id="save_button_ptkp"><i class="fas fa-save"></i> Update</button>&nbsp;
					<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</form>
			<div class="modal-body" style="border-top: 1px solid #e9ecef;">
				<table id="ptkp_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					<thead>
					  <tr>				   
						<th align="center">PTKP Status</th>
						<th align="center">Update Date</th>
					  </tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>	

<!-- div class="modal fade" id="modal_profile"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="title_profile" class="modal-title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="contentProfile">
				
			</div>
        </div>
    </div>
</div -->
@endsection
@section('css')
<style type="text/css">
    .modal-lg, .modal-xl {
        max-width: 90% !important;
    }
	
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }
	
	.profile-pic {
   margin-bottom:30px;
}

.file-upload {
    display: none;
}
.circle {
    border-radius: 20px !important;
    overflow: hidden;
    width: 132px;
    height: 130px;
    border: 6px solid #aaaaaa;
}
img {
    max-width: 100%;
    height: auto;
}
.p-image {
  position: absolute;
  top: 100px;
  right: 55px;
  color: #666666;
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
}
.p-image:hover {
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
}
.upload-button {
  font-size: 1.8em;
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
  opacity:0.5;
}

.upload-button:hover {
  transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
  opacity: 1;
}
.swal-red {
        color:#f27474;
		font-weight:bold;
    }
td.text-center{
	text-align:center;
}
.dtfc-fixed-left{
	z-index:10;
}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_employee = "";
let profile_id_employee = 0;
let global_id_position_detail = 0;
let global_id_education_employee = 0;
let global_id_family_employee = 0;
let global_id_ex_employee = 0;
let global_id_skill_employee = 0;
let global_id_cert_employee = 0;
let global_id_bank_employee = 0;
let global_id_insurance_employee = 0;
let global_id_document_employee = 0;
let global_id_onboarding_employee = 0;
let global_id_offboarding_employee = 0;
let global_id_education_level = [];
let global_id_bank = [];
let global_bank_currency = [];
let global_id_insurance = [];
let global_id_checklist_onboarding = [];
let global_id_checklist_offboarding = [];
let global_ex_concurent = [];
let global_leave_custom = [];
let list_status = [
    { id: 'A', text: 'Active' },
    { id: 'I', text: 'Inactive' },
];

function browse_ptkp() {	
	$.ajax({
		url: "<?= url('employee/employee/employee/get_ptkp_edit') ?>",
		method: "GET",
		data: {id_employee: global_id_employee},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			$('#ptkp_status_list').val(response.ptkp_status).trigger('change');	
			$('#ptkp_date').val(response.date_change_ptkp).trigger('change');	
			list_ptkp(global_id_employee);
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function (response) {
		 swal({
				icon: 'error',
				title: 'Oops...',
				dangerMode: true,
				text: 'Something went wrong! [Unknown Error]'
			});
		},
	});	
	$('#browseModalptkp').modal('show');
	
}

function list_ptkp(global_id_employee) {
	$('#ptkp_table').DataTable({	
		//	processing: true,
			responsive: true,
			destroy:true,
			columnDefs:false,
			paging:false,
			searching:false,
			lengthChange: false,
			info: false,
			dom: '<"toolbar">frtip',
			ajax: {
				url: "<?= url('employee/employee/employee/get_ptkp') ?>",
				data: {id_employee : global_id_employee},				
				error: function (jqXHR, textStatus, errorThrown) {
						$('#ptkp_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{data: 'ptkp_status', name: 'ptkp_status', className: 'text-middle'},
				{data: 'date_change_ptkp', name: 'date_change_ptkp', className: 'text-middle'},															
			],
		});
	
}

$('#ptkpForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();
			formData.push({
				'name' : 'id_employee',
				'value' : global_id_employee
			});
			$(".feedback").children("strong").text("");
            $("#ptkpForm input").removeClass("is-invalid");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: "{{ route('ptkp.update') }}",
                    data: formData,
                    success: function (response) {
						$('#ptkp_status').val(response.ptkp_status_update).trigger('change');	
						$('#ptkp_date_update').val(response.ptkp_date_update).trigger('change');	
						$('#browseModalptkp').modal('hide');
                    },				
					error: function (response) {
						let errors = response.responseJSON.errors;
						Object.keys(errors).forEach(function (key) {
							var key_temp = key.replaceAll(".", "_");
							$("#" + key_temp).addClass("is-invalid");
							$("#" + key_temp + "Error").children("strong").text(errors[key][0]);																
						});                 
                    }
                });
            
        });


function _(el) {
  return document.getElementById(el);
}

function uploadFile(image_attachment) {
	$(".feedback").children("strong").text("");
	var file = image_attachment.files[0];
	file_type = file.type;
  // alert(file.name+" | "+file.size+" | "+file.type);
  var formdata = new FormData();
  var get_image = $("#file_name").val();
  formdata.append("image_attachment", file);
  formdata.append("file_name", get_image);
//  console.log(formdata);
  $.ajax({
	xhr: function() {
		var xhr = new window.XMLHttpRequest();
			xhr.upload.addEventListener("progress", function(event) {
			//	_("loaded_n_total").innerHTML = "Uploaded " + event.loaded + " bytes of " + event.total;
				var percent = (event.loaded / event.total) * 100;
				_("progressBar").value = Math.round(percent);
				_("status_bar").innerHTML = Math.round(percent) + "% uploaded... please wait";
				}, false);

				xhr.addEventListener("error", function(event) {
					_("status_bar").innerHTML = "Upload Failed";
				}, false);
				
				xhr.addEventListener("abort", function(event) {
					_("status_bar").innerHTML = "Upload Abort";
				}, false);
			
			return xhr;
		  },
		  url: "{{ route('employee.upload') }}",
		  type: 'POST',
		  headers: {
			'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
			},
			enctype: 'multipart/form-data',
		  cache: false,
		  processData: false, // important
		  contentType: false, // important
		  dataType : 'json',
		  data: formdata,
		  success: function(result) {
				$('#status_bar').html('');	
				$('#loaded_n_total').html(result.message);
				$('#loaded_failed').html('');
				document.getElementById("attach").src = result.path;
				$('#file_name').val(result.image_name);
				$('#attach').css('display','inline');
			},
			error: function (result) {			
				let errors = result.responseJSON.errors;
				Object.keys(errors).forEach(function (key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
				});
				$('#status_bar').html('');	
				$('#loaded_n_total').html('');
				$('#loaded_failed').html('Upload Failed');
			}
		});
}


$(document).on('click', '.advclose', function () {
	$('#cf').hide();
})

function loadreport(){
	 $("#contentBody").html('');
    $.ajax({
		url: "{{ route('employee.report') }}",
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}

function loadreportptkp(){
	 $("#contentBodyPtkp").html('');
    $.ajax({
		url: "{{ route('employee.reportptkp') }}",
		success: function(result){
        //alert("success"+result);
        $("#contentBodyPtkp").html(result);
        $("#myModalPtkp").modal('show'); 
    }});
}

function offfile(input,size) {
		var fileName = input.files[0].name;
		$('#offboarding_' + size + '_offfile').html(fileName);
	}
	
function onfile(input,size) {
		var fileName = input.files[0].name;
		$('#onboarding_' + size + '_onfile').html(fileName);
	}

function docfile(input,size) {
		var fileName = input.files[0].name;
		$('#doc_' + size + '_docfile').html(fileName);
	}
/*
function getcode() {
	$.getJSON('<?= url('employee/employee/employee/getcode') ?>', function (data) {
				$('#nik_employee').val(data).trigger('change');			
			});
}
*/

const getktpcode = async () => {
	let result;
	var ktp = $("#identification_number").val();
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/employee/getCandidate') ?>',
			data: {no_ktp: ktp},
            dataType: 'json',
			beforeSend: function () {
				$("#ktpcode").hide();
				$("#ktpcode_loading").show();
				$('#table_education_body').html("");
				$('#table_family_body').html("");
				$('#table_ex_body').html("");
				$('#table_skill_body').html("");
				$('#table_cert_body').html("");
			},
            success: function (data) {
				if(data.length != 0){	
					$('#name').val(data['name']).trigger('change');
					$('#address_home').val(data['address_home']).trigger('change');
					$('#private_mail').val(data['email']).trigger('change');
					$('#marital').val(data['marital']).trigger('change');
					$('#mobile_phone').val(data['mobile_phone']).trigger('change');
					$('#emergency_phone').val(data['emergency_phone']).trigger('change');
					$('#emergency_contact').val(data['emergency_contact']).trigger('change');
					$('#idcard_address').val(data['idcard_address']).trigger('change');
					$('#npwp_number').val(data['taxpayer_identification_number']).trigger('change');
					$('#gender').val(data['gender']).trigger('change');
					$('#birthdate').val(data['date_of_birth']).trigger('change');
					$('#place_of_birth').val(data['place_of_birth']).trigger('change');
					$('#id_religion').val(data['id_religion']).trigger('change');
					$('#id_country').val(data['id_country']).trigger('change');
					
					if(data['edu'].length > 0){
						$.each(data['edu'], function (i, item) {
							$('#new_employee_education').trigger('click');
						});
						setTimeout(function () {
							$('#table_education_body tr').each(function (index) {
								$(this).find('span.sn').html(index+1);
							//	$(this).find('.id_education_employee_input').val(data['edu'][index].id_education_candidate);
								$(this).find('.major_input').val(data['edu'][index].major);
								$(this).find('.education_name_input').val(data['edu'][index].education_name);
								$(this).find('.id_education_level_input').val(data['edu'][index].id_education_level).trigger('change');
								$(this).find('.education_city_input').val(data['edu'][index].education_city);
								$(this).find('.start_year_input').val(data['edu'][index].start_year).trigger('change');
								$(this).find('.end_year_input').val(data['edu'][index].end_year).trigger('change');
							});                       
						}, 500);
					}
					if(data['family'].length > 0){
						$.each(data['family'], function (i, item) {
							$('#new_employee_family').trigger('click');
						});
						setTimeout(function () {
							$('#table_family_body tr').each(function (index) {
								$(this).find('span.sn').html(index + 1);
							//	$(this).find('.id_family_employee_input').val(data['family'][index].id_family_candidate);
								$(this).find('.family_name_input').val(data['family'][index].family_name);
								$(this).find('.gender_input').val(data['family'][index].gender).trigger('change');							
								$(this).find('.relationship_input').val(data['family'][index].relationship);
								$(this).find('.mobile_phone_input').val(data['family'][index].mobile_phone);
							});                       
						}, 500);
					}	
					if(data['ex'].length > 0){
						$.each(data['ex'], function (i, item) {
							$('#new_employee_ex').trigger('click');
						});
						setTimeout(function () {
							$('#table_ex_body tr').each(function (index) {
								$(this).find('span.sn').html(index + 1);
							//	$(this).find('.id_experience_employee_input').val(data['ex'][index].id_experience_candidate);
								$(this).find('.position_name_input').val(data['ex'][index].position_name).trigger('change');
								$(this).find('.company_name_input').val(data['ex'][index].company_name).trigger('change');							
								$(this).find('.company_city_input').val(data['ex'][index].company_city).trigger('change');							
								$(this).find('.start_year_ex_input').val(data['ex'][index].start_year).trigger('change');
								$(this).find('.end_year_ex_input').val(data['ex'][index].end_year).trigger('change');
							});                       
						}, 500);
					}	
					if(data['skill'].length > 0){
						$.each(data['skill'], function (i, item) {
							$('#new_employee_skill').trigger('click');
						});
						setTimeout(function () {
							$('#table_skill_body tr').each(function (index) {
								$(this).find('span.sn').html(index + 1);
							//	$(this).find('.id_skill_employee_input').val(data['skill'][index].id_skill_candidate);
								$(this).find('.skill_name_input').val(data['skill'][index].skill_name).trigger('change');
								$(this).find('.skill_level_input').val(data['skill'][index].skill_level).trigger('change');							
							});                       
						}, 500);
					}
					if(data['cert'].length > 0){
						$.each(data['cert'], function (i, item) {
							$('#new_employee_cert').trigger('click');
						});
						setTimeout(function () {
							$('#table_cert_body tr').each(function (index) {
								$(this).find('span.sn').html(index + 1);
							//	$(this).find('.id_certification_employee_input').val(data['cert'][index].id_certification_candidate);
								$(this).find('.certification_name_input').val(data['cert'][index].certification_name).trigger('change');
								$(this).find('.certified_by_input').val(data['cert'][index].certified_by).trigger('change');
								$(this).find('.years_issued_input').val(data['cert'][index].years_issued).trigger('change');
								$(this).find('.validity_period_input').val(data['cert'][index].validity_period).trigger('change');
								// $(this).find('.certification_attachment').val(data['cert'][index].attachment);
								// $(this).find('.certification_attachment_link').attr("href", data['cert'][index].attachment).attr('target', '_blank').text('Download');
							});                       
						}, 500);
					}	
				//	$("#ktpcode_loading").hide();
				//	$("#ktpcode").show();
				}
				else{
					 swal({
						icon: 'error',
						title: 'Oops...',
						dangerMode: true,
						text: 'ID Number Unavailable in Candidate Data'
					});
					$('#name').val('').trigger('change');
					$('#address_home').val('').trigger('change');
					$('#private_mail').val('').trigger('change');
					$('#marital').val('').trigger('change');
					$('#mobile_phone').val('').trigger('change');
					$('#emergency_phone').val('').trigger('change');
					$('#emergency_contact').val('').trigger('change');
					$('#idcard_address').val('').trigger('change');
					$('#npwp_number').val('').trigger('change');
					$('#gender').val('').trigger('change');
					$('#birthdate').val('').trigger('change');
					$('#place_of_birth').val('').trigger('change');
					$('#id_religion').val('').trigger('change');
					$('#id_country').val('').trigger('change');
					
					$('#table_education_body').html("");
					$('#table_family_body').html("");
					$('#table_ex_body').html("");
					$('#table_skill_body').html("");
					$('#table_cert_body').html("");
				//	$("#ktpcode_loading").hide();
				//	$("#ktpcode").show();
				}
				$("#ktpcode_loading").hide();
				$("#ktpcode").show();
            },
        });
        return result;
    } catch (error) {
   //     get_question(id_candidate_status);
    }	
}


function genleave() {
				$.getJSON('<?= url('employee/employee/employee/generateleave') . '?id_employee=' ?>' + global_id_employee, function (data) {
				}).done(function () {
				swal({
					icon: 'success',
					title: 'Success',
					text: 'Generate Leave Successfully'
				}).then(function(){
					setTimeout(function(){
						$('#table_leave').DataTable().ajax.reload();				 				
					}, 1000);
				});	
			  }).fail(function() {
				 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			  });
			}

function check(i,id) {
	
		  $.ajax({
		   url :"employee/checkid/"+id,
		   dataType:"json",
		   success:function(data)
		   {
			$("#job_"+i+"_id_position_detail").val(data.result.id_position_detail);
			$("#job_"+i+"_job_position").val(data.result.job_position);
			$("#job_"+i+"_position_routing").val(data.result.position_routing);
			$("#job_"+i+"_position_detail").val(data.result.position_detail);
			$("#job_"+i+"_branch").val(data.result.branch);
			$("#job_"+i+"_location").val(data.result.location);
			$("#job_"+i+"_principal").val(data.result.principal);
			$("#job_"+i+"_parent_position_detail").val(data.result.parent_position_detail);
			$("#job_"+i+"_name_supervisor").val(data.result.name_supervisor);
			$("#job_"+i+"_department").val(data.result.department);
			$("#job_"+i+"_emp_name").val(data.result.name);
			$("#browseModaljob").modal('hide');
		   }
		  })
}
		
function browse_job(counter) {
	$('#browseModaljob').modal('show');	
/*	$('#bro_table_job thead tr').clone(true).appendTo( '#bro_table_job thead' );
	$('#bro_table_job thead tr:eq(1) th').each( function (i) {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" style="width:80px;"/>' );
        $( 'input', this ).on( 'keyup change', function () {
            if ( table.column(i).search() !== this.value ) {
                table
                    .column(i)
                    .search( this.value )
                    .draw();
            }
        } );
    });
*/
	var table = $("#bro_table_job").DataTable({
	//	scrollY: "350px",
		scrollX: true,
		fixedColumns:   {
            left: 2,
            right: 1
        },
	//	responsive: true,
          paging: true,
		  pageLength:5,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          autoWidth: true,
		  columnDefs:false,
		  dom: '<"toolbar">frtip',
          columns : [
        //    { data : 'job_position' },
            { data : 'position_detail'},
            { data : 'position_routing'},
            { data : 'branch' },
            { data : 'location' },
            { data : 'principal' },
            { data : 'parent_position_detail' },
            { data : 'name_supervisor' },
            { data : 'department' },
			{ data : 'assign_company' },
            { data : 'name' },
			{ data : 'trans_type' },
            { data : 'effective_date' },
            { data : 'action', className: 'text-center'},
          ],    
          ajax: {
            type: 'GET',
            url: "{{ route('employee.browse_job') }}",
			error: function (jqXHR, textStatus, errorThrown) {
					$('#bro_table_job').DataTable().ajax.reload();
				},
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
              //    'job_position'   :json[i]['job_position'],
                  'position_detail'   : json[i]['position_detail'],
                  'position_routing'   : json[i]['position_routing'],
                  'branch'   : json[i]['branch'],
                  'location'   : json[i]['location'],
                  'principal'   : json[i]['principal'],
                  'parent_position_detail'   : json[i]['parent_position_detail'],
                  'name_supervisor'   : json[i]['name_supervisor'],
                  'department'   : json[i]['department'],
				  'assign_company'   : json[i]['assign_company'],
                  'name'   : json[i]['name'],
				  'trans_type'   : json[i]['trans_type'],
                  'effective_date'   : json[i]['effective_date'],
                  'action'   : '<button type="button" name="check" id="'+json[i]['id_position_detail']+'" class="btn btn-success btn-sm" title="Check" onClick="check('+counter+','+json[i]['id_position_detail']+')"><i class="fa fa-check-square-o" aria-hidden="true"></i></button>',
                })
                no++;
              }
              return return_data;
            }
          }
      });
//	alert("tes");
}
$(function () {	
	
		$(document).on('click', '.new', function () {
            global_id_employee = "";
			global_id_document_employee = 0;
            $("#employeeForm")[0].reset();
		//	$('#employeeForm select').val($('#employeeForm select option:first-child').val()).trigger('change');
            $("#table_job_body").html("");
            $("#table_education_body").html("");
            $("#table_family_body").html("");
            $("#table_ex_body").html("");
            $("#table_skill_body").html("");
            $("#table_cert_body").html("");
            $("#table_bank_body").html("");
            $("#table_insurance_body").html("");
            $("#table_career tbody").html("");
            $("#table_leave tbody").html("");
            $("#table_awdcp tbody").html("");
            $("#table_history tbody").html("");
            $("#table_doc_body").html("");
            $("#table_onboarding_body").html("");
            $("#table_offboarding_body").html("");
            $("#employeeForm .modal-title").html("<span class='fas fa-plus'></span> Form Employee Data");
            $(".invalid-feedback").children("strong").text("");
            $(".feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#employeeForm input").removeClass("is-invalid");
            $("#employeeForm select").removeClass("is-invalid");
		//	$("#employeeForm select").val([]).trigger('change');
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
			$("#nikcode").css("display","block");
			$('#attach').removeAttr('src');
			$("#new_employee_job").css('display', 'inline');
					
			 $('#employeeForm select').trigger('change');
			 $('#id_user').empty();
			 $('#id_user').attr('readonly', true);
			 
			 $('#resign_date, #permanent_date').attr('disabled', true);
			 $('#resign_date, #permanent_date').parent().children('span').children('button').attr('disabled', true);
			 
		 	var va = ['KTP','KK','Rekening Bank','Surat Pernyataan','SIM','NPWP'];
			$.each(va, function (i, item) {
				$('#new_employee_doc').trigger('click');
				$('#doc_' + i + '_document_name').val(item);
				$('#doc_' + i + '_document_name').attr('readonly', true);
				$('.delete-doc').attr('data-id', i).css('display', 'none');
			//	$('#doc_2_document_number').attr('readonly', true);
			//	$('#doc_2_document_number').val('<?= session('id_company') ?>'+Date.now());
			});
						
			$('#modal_form_employee').modal('show');	

       });
      		
		 $('#employeeForm').submit(function (e) {
            e.preventDefault();
			var ktp = $("#identification_number").val();
            $(".invalid-feedback").children("strong").text("");
            $(".feedback").children("strong").text("");
            $("#employeeForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
			
			$.getJSON('<?= url('employee/employee/employee/ktpcheck') . '?no_ktp=' ?>' + ktp, function (data) {
				if(data.length != 0){
					swal({
						title: 'Continue ?',
						text: 'The ID Number Already Exists',
						icon: 'warning',
						 buttons: {
							cancel: {
							  text: "No",
							  value: false,
							  visible: true,
							  className: "",
							  closeModal: true,
							},
							confirm: {
							  text: "Yes",
							  value: true,
							  visible: true,
							  className: "",
							  closeModal: true
							}
						  }							 
					}).then(function(value){
						if (value) {
							submit();
							}
						});
				}
				else{
					submit();
				}
			});
        });


        $(document).on('click', '#new_employee_job', function () {
            var content = jQuery('#sample_table_employee_job tr'),
                    size = global_id_position_detail++,
                    element = null,
                    element = content.clone();
					
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.add-record').attr('data-id', size);
            element.find('.add-record').attr('onclick', 'browse_job('+size+')');
         			
			
			element.find('.id_position_detail_input').attr('id', 'job_' + size + '_id_position_detail');
            element.find('.id_position_detail_input').attr('name', 'job[' + size + '][id_position_detail]');
			
			element.find('.job_position_input').attr('id', 'job_' + size + '_job_position');
            element.find('.job_position_input').attr('name', 'job[' + size + '][job_position]');			
            element.find('.job_position_input_error').attr('id', 'job_' + size + '_job_position');
			
			element.find('.position_routing_input').attr('id', 'job_' + size + '_position_routing');
			element.find('.position_detail_input').attr('id', 'job_' + size + '_position_detail');	
			
			element.find('.branch_input').attr('id', 'job_' + size + '_branch');
			element.find('.branch_input').attr('name', 'job[' + size + '][branch]');
			
			element.find('.location_input').attr('id', 'job_' + size + '_location');
			element.find('.location_input').attr('name', 'job[' + size + '][location]');
			
			element.find('.principal_input').attr('id', 'job_' + size + '_principal');
			
			element.find('.parent_position_detail_input').attr('id', 'job_' + size + '_parent_position_detail');			
			element.find('.name_supervisor_input').attr('id', 'job_' + size + '_name_supervisor');			
			element.find('.department_input').attr('id', 'job_' + size + '_department');
			element.find('.emp_name_input').attr('id', 'job_' + size + '_emp_name');
           
			element.appendTo('#table_job_body');
			 $('#table_job_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
			
        });
	
		$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_job_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
			
            return true;
        });
		
		$(document).on('click', '#new_employee_education', function () {
            var content = jQuery('#sample_table_employee_education tr'),
                    size = global_id_education_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_edu-'+size);
            element.find('.delete-edu').attr('data-id', size);
            element.find('.id_education_employee_input').attr('id', 'edu_' + size + '_id_education_employee');
            element.find('.id_education_employee_input').attr('name', 'edu[' + size + '][id_education_employee]');
			
			element.find('.major_input').attr('id', 'edu_' + size + '_major');
            element.find('.major_input').attr('name', 'edu[' + size + '][major]');
            element.find('.major_input_error').attr('id', 'edu_' + size + '_majorError');
			
			element.find('.education_name_input').attr('id', 'edu_' + size + '_education_name');
            element.find('.education_name_input').attr('name', 'edu[' + size + '][education_name]');
            element.find('.education_name_input_error').attr('id', 'edu_' + size + '_education_nameError');
			
			element.find('.id_education_level_input').attr('id', 'edu_' + size + '_id_education_level');
            element.find('.id_education_level_input').attr('name', 'edu[' + size + '][id_education_level]');
            element.find('.id_education_level_input_error').attr('id', 'edu_' + size + '_id_education_levelError');
			element.find('.id_education_level_input').select2({
                placeholder: "Select Level",
                allowClear: true,
                data: global_id_education_level
            });
            element.find('.id_education_level_input').val('').trigger('change');
			
			element.find('.start_year_input').attr('id', 'edu_' + size + '_start_year');
            element.find('.start_year_input').attr('name', 'edu[' + size + '][start_year]');
            element.find('.start_year_input_error').attr('id', 'edu_' + size + '_start_yearError');
			element.find('.start_year_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.start_year_input').val('').trigger('change'); 
			
			element.find('.end_year_input').attr('id', 'edu_' + size + '_end_year');
            element.find('.end_year_input').attr('name', 'edu[' + size + '][end_year]');
            element.find('.end_year_input_error').attr('id', 'edu_' + size + '_end_yearError');
			element.find('.end_year_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.end_year_input').val('').trigger('change'); 
			
			element.find('.education_city_input').attr('id', 'edu_' + size + '_education_city');
            element.find('.education_city_input').attr('name', 'edu[' + size + '][education_city]');
            element.find('.education_city_input_error').attr('id', 'edu_' + size + '_education_cityError');
			
            element.appendTo('#table_education_body');
			 $('#table_education_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-edu', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_edu-' + id).remove();
            $('#table_education_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_family', function () {
            var content = jQuery('#sample_table_employee_family tr'),
                    size = global_id_family_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_fam-'+size);
            element.find('.delete-fam').attr('data-id', size);
            element.find('.id_family_employee_input').attr('id', 'fam_' + size + '_id_family_employee');
            element.find('.id_family_employee_input').attr('name', 'fam[' + size + '][id_family_employee]');
			
			element.find('.family_name_input').attr('id', 'fam_' + size + '_family_name');
            element.find('.family_name_input').attr('name', 'fam[' + size + '][family_name]');
            element.find('.family_name_input_error').attr('id', 'fam_' + size + '_family_nameError');
			
			element.find('.gender_input').attr('id', 'fam_' + size + '_gender');
            element.find('.gender_input').attr('name', 'fam[' + size + '][gender]');
            element.find('.gender_input_error').attr('id', 'fam_' + size + '_genderError');					
			var global_gender = [{ id: 'M', text: 'Male' }, { id: 'F', text: 'Female' }];		
            element.find('.gender_input').select2({
                placeholder: "Select Gender",
                allowClear: true,
                data: global_gender
            });
            element.find('.gender_input').val('').trigger('change');
			
			element.find('.relationship_input').attr('id', 'fam_' + size + '_relationship');
            element.find('.relationship_input').attr('name', 'fam[' + size + '][relationship]');
            element.find('.relationship_input_error').attr('id', 'fam_' + size + '_relationshipError');
			
			element.find('.mobile_phone_input').attr('id', 'fam_' + size + '_mobile_phone');
            element.find('.mobile_phone_input').attr('name', 'fam[' + size + '][mobile_phone]');
            element.find('.mobile_phone_input_error').attr('id', 'fam_' + size + '_mobile_phoneError');
			
			
            element.appendTo('#table_family_body');
			 $('#table_family_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-fam', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_fam-' + id).remove();
            $('#table_family_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_ex', function () {
            var content = jQuery('#sample_table_employee_ex tr'),
                    size = global_id_ex_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_ex-'+size);
            element.find('.delete-ex').attr('data-id', size);
            element.find('.id_experience_employee_input').attr('id', 'ex_' + size + '_id_experience_employee');
            element.find('.id_experience_employee_input').attr('name', 'ex[' + size + '][id_experience_employee]');
			
			element.find('.position_name_input').attr('id', 'ex_' + size + '_position_name');
            element.find('.position_name_input').attr('name', 'ex[' + size + '][position_name]');
            element.find('.position_name_input_error').attr('id', 'ex_' + size + '_position_nameError');
			
			element.find('.company_name_input').attr('id', 'ex_' + size + '_company_name');
            element.find('.company_name_input').attr('name', 'ex[' + size + '][company_name]');
            element.find('.company_name_input_error').attr('id', 'ex_' + size + '_company_nameError');
			
			element.find('.company_city_input').attr('id', 'ex_' + size + '_company_city');
            element.find('.company_city_input').attr('name', 'ex[' + size + '][company_city]');
            element.find('.company_city_input_error').attr('id', 'ex_' + size + '_company_cityError');
			
			element.find('.start_year_ex_input').attr('id', 'ex_' + size + '_start_year_ex');
            element.find('.start_year_ex_input').attr('name', 'ex[' + size + '][start_year]');
            element.find('.start_year_ex_input_error').attr('id', 'ex_' + size + '_start_year_exError');
			element.find('.start_year_ex_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.start_year_ex_input').val('').trigger('change'); 
			
			element.find('.end_year_ex_input').attr('id', 'ex_' + size + '_end_year_ex');
            element.find('.end_year_ex_input').attr('name', 'ex[' + size + '][end_year]');
            element.find('.end_year_ex_input_error').attr('id', 'ex_' + size + '_end_year_exError');
			element.find('.end_year_ex_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.end_year_ex_input').val('').trigger('change'); 
			
            element.appendTo('#table_ex_body');
			 $('#table_ex_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-ex', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_ex-' + id).remove();
            $('#table_ex_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
			
		$(document).on('click', '#new_employee_skill', function () {
            var content = jQuery('#sample_table_employee_skill tr'),
                    size = global_id_skill_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_skill-'+size);
            element.find('.delete-skill').attr('data-id', size);
            element.find('.id_skill_employee_input').attr('id', 'skill_' + size + '_id_skill_employee');
            element.find('.id_skill_employee_input').attr('name', 'skill[' + size + '][id_skill_employee]');
			
			element.find('.skill_name_input').attr('id', 'skill_' + size + '_skill_name');
            element.find('.skill_name_input').attr('name', 'skill[' + size + '][skill_name]');
            element.find('.skill_name_input_error').attr('id', 'skill_' + size + '_skill_nameError');
		
			element.find('.skill_level_input').attr('id', 'skill_' + size + '_skill_level');
            element.find('.skill_level_input').attr('name', 'skill[' + size + '][skill_level]');
            element.find('.skill_level_input_error').attr('id', 'skill_' + size + '_skill_levelError');					
			var global_skill = [{ id: 1, text: '1 (Basic)' }, { id: 2, text: '2 (Advanced Beginner)' }, { id: 3, text: '3 (Intermediate)' }, { id: 4, text: '4 (Proficient)' }, { id: 5, text: '5 (Expert)' }];		
            element.find('.skill_level_input').select2({
                placeholder: "Select Level",
                allowClear: true,
                data: global_skill
            });
            element.find('.gender_input').val('').trigger('change');
			
            element.appendTo('#table_skill_body');
			 $('#table_skill_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-skill', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_skill-' + id).remove();
            $('#table_skill_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_cert', function () {
            var content = jQuery('#sample_table_employee_cert tr'),
                    size = global_id_cert_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_cert-'+size);
            element.find('.delete-cert').attr('data-id', size);
            element.find('.id_certification_employee_input').attr('id', 'cert_' + size + '_id_certification_employee');
            element.find('.id_certification_employee_input').attr('name', 'cert[' + size + '][id_certification_employee]');
			
			element.find('.certification_name_input').attr('id', 'cert_' + size + '_certification_name');
            element.find('.certification_name_input').attr('name', 'cert[' + size + '][certification_name]');
            element.find('.certification_name_input_error').attr('id', 'cert_' + size + '_certification_nameError');
			
			element.find('.certified_by_input').attr('id', 'cert_' + size + '_certified_by');
            element.find('.certified_by_input').attr('name', 'cert[' + size + '][certified_by]');
            element.find('.certified_by_input_error').attr('id', 'cert_' + size + '_certified_byError');
			
			element.find('.years_issued_input').attr('id', 'cert_' + size + '_years_issued');
            element.find('.years_issued_input').attr('name', 'cert[' + size + '][years_issued]');
            element.find('.years_issued_input_error').attr('id', 'cert_' + size + '_years_issuedError');
			
			element.find('.validity_period_input').attr('id', 'cert_' + size + '_validity_period');
            element.find('.validity_period_input').attr('name', 'cert[' + size + '][validity_period]');
            element.find('.validity_period_input_error').attr('id', 'cert_' + size + '_validity_periodError');
			
			// element.find('.certification_attachment').attr('id', `cert_${size}_attachment`);
			// element.find('.certification_attachment').attr('name', `cert[${size}][attachment]`);

            element.appendTo('#table_cert_body');
			 $('#table_cert_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-cert', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_cert-' + id).remove();
            $('#table_cert_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_bank', function () {
            var content = jQuery('#sample_table_employee_bank tr'),
                    size = global_id_bank_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_bank-'+size);
            element.find('.delete-bank').attr('data-id', size);
            element.find('.id_bank_employee_input').attr('id', 'bank_' + size + '_id_bank_employee');
            element.find('.id_bank_employee_input').attr('name', 'bank[' + size + '][id_bank_employee]');
				
			element.find('.id_bank_input').attr('id', 'bank_' + size + '_id_bank');
            element.find('.id_bank_input').attr('name', 'bank[' + size + '][id_bank]');
            element.find('.id_bank_input_error').attr('id', 'bank_' + size + '_id_bankError');					
            element.find('.id_bank_input').select2({
                placeholder: "Select Bank",
                allowClear: true,
                data: global_id_bank
            }).on('change', function (e) {
				$('#bank_' + size + '_bank_name').val($(this).find("option:selected").text()).trigger('change');
            }).trigger('change');
            element.find('.id_bank_input').val('').trigger('change');
						
			element.find('.bank_name_input').attr('id', 'bank_' + size + '_bank_name');
            element.find('.bank_name_input').attr('name', 'bank[' + size + '][bank_name]');
			
			element.find('.bank_account_input').attr('id', 'bank_' + size + '_bank_account');
            element.find('.bank_account_input').attr('name', 'bank[' + size + '][bank_account]');
            element.find('.bank_account_input_error').attr('id', 'bank_' + size + '_bank_accountError');	

			element.find('.account_name_input').attr('id', 'bank_' + size + '_account_name');
            element.find('.account_name_input').attr('name', 'bank[' + size + '][account_name]');
            element.find('.account_name_input_error').attr('id', 'bank_' + size + '_account_nameError');	

			element.find('.bank_currency_input').attr('id', 'bank_' + size + '_bank_currency');
            element.find('.bank_currency_input').attr('name', 'bank[' + size + '][bank_currency]');
            element.find('.bank_currency_input_error').attr('id', 'bank_' + size + '_bank_currencyError');					
            element.find('.bank_currency_input').select2({
                placeholder: "Select Currency",
                allowClear: true,
                data: global_bank_currency
            });
        //    element.find('.bank_currency_input').val('').trigger('change');
			
			element.find('.default_bank_input').attr('id', 'bank_' + size + '_default_bank');
            element.find('.default_bank_input').attr('name', 'bank[' + size + '][default_bank]');
            element.find('.default_bank_input_error').attr('id', 'bank_' + size + '_default_bankError');	
			
            element.appendTo('#table_bank_body');
			 $('#table_bank_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-bank', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_bank-' + id).remove();
            $('#table_bank_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_insurance', function () {
            var content = jQuery('#sample_table_employee_insurance tr'),
                    size = global_id_insurance_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_ins-'+size);
            element.find('.delete-insurance').attr('data-id', size);
            element.find('.id_insurance_employee_input').attr('id', 'ins_' + size + '_id_insurance_employee');
            element.find('.id_insurance_employee_input').attr('name', 'ins[' + size + '][id_insurance_employee]');
				
			element.find('.id_insurance_input').attr('id', 'ins_' + size + '_id_insurance');
            element.find('.id_insurance_input').attr('name', 'ins[' + size + '][id_insurance]');
            element.find('.id_insurance_input_error').attr('id', 'ins_' + size + '_id_insuranceError');					
            element.find('.id_insurance_input').select2({
                placeholder: "Select Insurance",
                allowClear: true,
                data: global_id_insurance
            });
            element.find('.id_insurance_input').val('').trigger('change');
						
			element.find('.emp_insurance_number_input').attr('id', 'ins_' + size + '_emp_insurance_number');
            element.find('.emp_insurance_number_input').attr('name', 'ins[' + size + '][emp_insurance_number]');
            element.find('.emp_insurance_number_input_error').attr('id', 'ins_' + size + '_emp_insurance_numberError');
	
			element.find('.effective_date_input').attr('id', 'ins_' + size + '_effective_date');
            element.find('.effective_date_input').attr('name', 'ins[' + size + '][effective_date]');
            element.find('.effective_date_input_error').attr('id', 'ins_' + size + '_effective_dateError');
			element.find('.effective_date_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.effective_date_input').val('').trigger('change'); 
			
			element.find('.expired_date_input').attr('id', 'ins_' + size + '_expired_date');
            element.find('.expired_date_input').attr('name', 'ins[' + size + '][expired_date]');
            element.find('.expired_date_input_error').attr('id', 'ins_' + size + '_expired_dateError');
			element.find('.expired_date_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.expired_date_input').val('').trigger('change'); 

			element.find('.beneficiary_name_input').attr('id', 'ins_' + size + '_beneficiary_name');
            element.find('.beneficiary_name_input').attr('name', 'ins[' + size + '][beneficiary_name]');
            element.find('.beneficiary_name_input_error').attr('id', 'ins_' + size + '_beneficiary_nameError');
			
            element.appendTo('#table_insurance_body');
			 $('#table_insurance_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-insurance', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_ins-' + id).remove();
            $('#table_insurance_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_doc', function () {
            var content = jQuery('#sample_table_employee_doc tr'),
                    size = global_id_document_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_doc-'+size);
       //     element.find('.delete-doc').attr('data-id', size);
            element.find('.delete-doc').attr('data-id', size).css('display', 'inline');

            element.find('.id_document_employee_input').attr('id', 'doc_' + size + '_id_document_employee');
            element.find('.id_document_employee_input').attr('name', 'doc[' + size + '][id_document_employee]');
										
			element.find('.document_name_input').attr('id', 'doc_' + size + '_document_name');
            element.find('.document_name_input').attr('name', 'doc[' + size + '][document_name]');
            element.find('.document_name_input').val('');
            element.find('.document_name_input_error').attr('id', 'doc_' + size + '_document_nameError');
			
			element.find('.document_number_input').attr('id', 'doc_' + size + '_document_number');
            element.find('.document_number_input').attr('name', 'doc[' + size + '][document_number]');
            element.find('.document_number_input_error').attr('id', 'doc_' + size + '_document_numberError');
						
			element.find('.attachment_input').attr('onchange', 'docfile(this,'+size+')');
			element.find('.attachment_input').attr('id', 'doc_' + size + '_attachment');
            element.find('.attachment_input').attr('name', 'doc[' + size + '][attachment]');
            element.find('.attachment_input_error').attr('id', 'doc_' + size + '_attachmentError');
			element.find('.doc_input').attr('id', 'doc_' + size + '_docfile');
			element.find('.a_input').attr('id', 'doc_' + size + '_attach');
	
			element.find('.effective_date_input').attr('id', 'doc_' + size + '_effective_date');
            element.find('.effective_date_input').attr('name', 'doc[' + size + '][effective_date]');
            element.find('.effective_date_input_error').attr('id', 'doc_' + size + '_effective_dateError');
			element.find('.effective_date_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.effective_date_input').val('').trigger('change'); 
			
			element.find('.expired_date_input').attr('id', 'doc_' + size + '_expired_date');
            element.find('.expired_date_input').attr('name', 'doc[' + size + '][expired_date]');
            element.find('.expired_date_input_error').attr('id', 'doc_' + size + '_expired_dateError');
			element.find('.expired_date_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
            element.find('.expired_date_input').val('').trigger('change'); 
			
            element.appendTo('#table_doc_body');
			 $('#table_doc_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-doc', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_doc-' + id).remove();
            $('#table_doc_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
		
		$(document).on('click', '#new_employee_onboarding', function () {
            var content = jQuery('#sample_table_employee_onboarding tr'),
                    size = global_id_onboarding_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_onboarding-'+size);
            element.find('.id_checklist_employee_onboarding_input').attr('id', 'onboarding_' + size + '_id_checklist_employee');
				
			element.find('.id_checklist_onboarding_input').attr('id', 'onboarding_' + size + '_id_checklist');
            element.find('.id_checklist_onboarding_input_error').attr('id', 'onboarding_' + size + '_id_checklistError');					
            element.find('.id_checklist_onboarding_input').select2({
                placeholder: "Select Name On Boarding",
                allowClear: true,
                data: global_id_checklist_onboarding
            }).on('change', function (e) {
				$('#onboarding_' + size + '_document_name').val($(this).find("option:selected").text()).trigger('change');
            }).trigger('change');
            element.find('.id_checklist_onboarding_input').val('').trigger('change');
			
			element.find('.effective_date_onboarding_input').attr('id', 'onboarding_' + size + '_effective_date');
            element.find('.effective_date_onboarding_input_error').attr('id', 'onboarding_' + size + '_effective_dateError');
		/*	element.find('.effective_date_onboarding_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
		*/
            element.find('.effective_date_onboarding_input').val('').trigger('change'); 
			
			element.find('.remark_onboarding_input').attr('id', 'onboarding_' + size + '_remark');
            element.find('.remark_onboarding_input_error').attr('id', 'onboarding_' + size + '_remark');
			
			element.find('.onboarding_input').attr('id', 'onboarding_' + size + '_onfile');
			element.find('.a_onboarding_input').attr('id', 'onboarding_' + size + '_attach');
			
			element.find('.completed_onboarding_input').attr('id', 'onboarding_' + size + '_completed');			
            element.appendTo('#table_onboarding_body');
			 $('#table_onboarding_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	/*	$(document).on('click', '.delete-onboarding', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_onboarding-' + id).remove();
            $('#table_onboarding_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
	*/
		$(document).on('click', '#new_employee_offboarding', function () {
            var content = jQuery('#sample_table_employee_offboarding tr'),
                    size = global_id_offboarding_employee++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_offboarding-'+size);
            element.find('.id_checklist_employee_offboarding_input').attr('id', 'offboarding_' + size + '_id_checklist_employee');
				
			element.find('.id_checklist_offboarding_input').attr('id', 'offboarding_' + size + '_id_checklist');
            element.find('.id_checklist_offboarding_input_error').attr('id', 'offboarding_' + size + '_id_checklistError');					
            element.find('.id_checklist_offboarding_input').select2({
                placeholder: "Select Name Off Boarding",
                allowClear: true,
                data: global_id_checklist_offboarding
            }).on('change', function (e) {
				$('#offboarding_' + size + '_document_name').val($(this).find("option:selected").text()).trigger('change');
            }).trigger('change');
            element.find('.id_checklist_offboarding_input').val('').trigger('change');
			
			element.find('.effective_date_offboarding_input').attr('id', 'offboarding_' + size + '_effective_date');
            element.find('.effective_date_offboarding_input_error').attr('id', 'offboarding_' + size + '_effective_dateError');
		/*	element.find('.effective_date_offboarding_input').datepicker({
                uiLibrary: 'bootstrap4',
				format: 'yyyy-mm-dd',
            });
		*/
            element.find('.effective_date_offboarding_input').val('').trigger('change'); 
			
			element.find('.remark_offboarding_input').attr('id', 'offboarding_' + size + '_remark');
            element.find('.remark_offboarding_input_error').attr('id', 'offboarding_' + size + '_remark');
			
			element.find('.offboarding_input').attr('id', 'offboarding_' + size + '_offfile');
			element.find('.a_offboarding_input').attr('id', 'offboarding_' + size + '_attach');
			
			element.find('.completed_offboarding_input').attr('id', 'offboarding_' + size + '_completed');
			
            element.appendTo('#table_offboarding_body');
			 $('#table_offboarding_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
	/*
		$(document).on('click', '.delete-offboarding', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec_offboarding-' + id).remove();
            $('#table_offboarding_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });
	*/
		
  $(document).on('click', '.edit', function () {
            let id_employee = $(this).attr('id');
            global_id_employee = id_employee;
            $("#employeeForm")[0].reset();
            $("#table_job_body").html("");
            $("#table_education_body").html("");
            $("#table_family_body").html("");
            $("#table_ex_body").html("");
            $("#table_skill_body").html("");
            $("#table_cert_body").html("");
            $("#table_bank_body").html("");
            $("#table_insurance_body").html("");
			$("#table_doc_body").html("");
            $("#table_onboarding_body").html("");
            $("#table_offboarding_body").html("");
            $("#employeeForm .modal-title").html("<span class='fas fa-edit'></span> Edit Employee Data");
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#employeeForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');
			$("#nikcode").css("display","none");
			$('#id_user').attr('readonly', false);
			$('#resign_date').attr('disabled', true);
			$('#permanent_date').attr('disabled', false);
			$('#resign_date').parent().children('span').children('button').attr('disabled', true);
			$('#permanent_date').parent().children('span').children('button').attr('disabled', false);
			$("#workzone_loading").css('display', 'none');
			$("#new_employee_job").css('display', 'none');
		
            $.ajax({
                url: "<?= url('employee/employee/employee/get_employee_edit') ?>",
                method: "GET",
                data: {id_employee: id_employee},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                    global_id_position_detail = 0;
					global_id_education_employee = 0;
					global_id_family_employee = 0;
					global_id_ex_employee = 0;
					global_id_skill_employee = 0;
					global_id_cert_employee = 0;
					global_id_bank_employee = 0;
					global_id_insurance_employee = 0;
					global_id_document_employee = 0;
					global_id_onboarding_employee = 0;
					global_id_offboarding_employee = 0;
					 $.each(response.job, function (i, item) {
                        $('#new_employee_job').trigger('click');
                    });
				
					$.each(response.edu, function (i, item) {
                        $('#new_employee_education').trigger('click');
                    });
					$.each(response.fam, function (i, item) {
                        $('#new_employee_family').trigger('click');
                    });
					$.each(response.ex, function (i, item) {
                        $('#new_employee_ex').trigger('click');
                    });
					$.each(response.skill, function (i, item) {
                        $('#new_employee_skill').trigger('click');
                    });
					$.each(response.cert, function (i, item) {
                        $('#new_employee_cert').trigger('click');
                    });
					$.each(response.bank, function (i, item) {
                        $('#new_employee_bank').trigger('click');
                    });
					$.each(response.ins, function (i, item) {
                        $('#new_employee_insurance').trigger('click');
                    });
					$.each(response.doc, function (i, item) {
                        $('#new_employee_doc').trigger('click');
                    });			
					$.each(response.onboarding, function (i, item) {
                        $('#new_employee_onboarding').trigger('click');
                    });
					$.each(response.offboarding, function (i, item) {
                        $('#new_employee_offboarding').trigger('click');
                    });
				
                    $('#id_employee').val(response.id_employee).trigger('change');
                    $('#name').val(response.name).trigger('change');
                    $('#identification_number').val(response.identification_number).trigger('change');
					$('#address_home').val(response.address_home).trigger('change');
                    $('#idcard_address').val(response.idcard_address).trigger('change');							
                    $('#home_base').val(response.home_base).trigger('change');
                    $('#id_country').val(response.id_country).trigger('change');
                    $('#gender').val(response.gender).trigger('change');
                    $('#marital').val(response.marital).trigger('change');
                    $('#spouse_birthdate').val(response.spouse_birthdate).trigger('change');
                    $('#spouse_complete_name').val(response.spouse_complete_name).trigger('change');
                    $('#place_of_birth').val(response.place_of_birth).trigger('change');
                    $('#id_country_of_birth').val(response.id_country_of_birth).trigger('change');
                    $('#ptkp_status').val(response.ptkp_status).trigger('change');
                    $('#id_user').val(response.id_user).trigger('change');
                    $('#id_finger').val(response.id_finger).trigger('change');
                    $('#nik_employee').val(response.nik_employee).trigger('change');
                    $('#mobile_phone').val(response.mobile_phone).trigger('change');
                    $('#private_mail').val(response.private_mail).trigger('change');
                    $('#work_phone').val(response.work_phone).trigger('change');
                    $('#work_mail').val(response.work_mail).trigger('change');
                    $('#join_date').val(response.join_date).trigger('change');
                    $('#expired_date').val(response.expired_date).trigger('change');
                    $('#permanent_date').val(response.permanent_date).trigger('change');
                    $('#resign_date').val(response.resign_date).trigger('change');
                    $('#sales_code').val(response.sales_code).trigger('change');
                    $('#work_address').val(response.work_address).trigger('change');
                    $('#npwp_number').val(response.npwp_number).trigger('change');
                    $('#additional_note').val(response.additional_note).trigger('change');
                    $('#emergency_phone').val(response.emergency_phone).trigger('change');
                    $('#emergency_contact').val(response.emergency_contact).trigger('change');
                    $('#birthdate').val(response.birthdate).trigger('change');
                    $('#id_vaccination_status').val(response.id_vaccination_status).trigger('change');
                    $('#lasted_date_vaccine').val(response.lasted_date_vaccine).trigger('change');
                    $('#id_leave').val(response.id_leave).trigger('change');
                    $('#id_religion').val(response.id_religion).trigger('change');
                    $('#id_employment_status').val(response.id_employment_status).trigger('change');
                    $('#id_shift_group').val(response.id_shift_group).trigger('change');
                    $('#id_timezone').val(response.id_timezone).trigger('change');
					if(response.image_attachment != null){
						if(response.image_attachment.length > 1000){
							document.getElementById("attach").src = "data:image;base64,"+response.image_attachment;	
						}
						else{
							document.getElementById("attach").src = response.filePath+"/"+response.image_attachment;
						}
					}
					else{
						$('#attach').removeAttr('src');
					}
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#select2status').val(response.status).trigger('change');

                    setTimeout(function () {
                        $('#table_job_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_position_detail_input').val(response.job[index].id_position_detail);
                            $(this).find('.job_position_input').val(response.job[index].job_position).trigger('change');
                            $(this).find('.position_routing_input').val(response.job[index].position_routing);
                            $(this).find('.position_detail_input').val(response.job[index].position_detail);
							$(this).find('.branch_input').val(response.job[index].branch).trigger('change');
							$(this).find('.location_input').val(response.job[index].location).trigger('change');
							$(this).find('.principal_input').val(response.job[index].principal).trigger('change');
							$(this).find('.parent_position_detail_input').val(response.job[index].parent_position_detail).trigger('change');
							$(this).find('.name_supervisor_input').val(response.job[index].name_supervisor).trigger('change');
							$(this).find('.department_input').val(response.job[index].department).trigger('change');
							$(this).find('.add-record').css('display', 'none');
							$(this).find('.delete-record').css('display', 'none');
                        });                       
                    }, 500);

					// setTimeout(function () {
					// 	$.extend( true, $.fn.dataTable.defaults, {
					// 			 columnDefs:false,
					// 			 paging:false,
					// 			 searching:false,
					// 			 lengthChange: false,
					// 			 info: false,
					// 			 dom: '<"toolbar">frtip',
					// 		});
							
					// 		$('#table_contract').DataTable({	
					// 			destroy:true,
					// 			ajax: {
					// 				url: "<?= url('employee/employee/employee/get_emp_contract') . '?id_employee=' ?>" + global_id_employee,
					// 				error: function (jqXHR, textStatus, errorThrown) {
					// 						$('#table_contract').DataTable().ajax.reload();
					// 					}
					// 			},	
					// 			columns: [								
					// 				{data: 'DT_RowIndex', name: 'DT_RowIndex'},
					// 				{data: 'contract_number', name: 'contract_number'},
					// 				{data: 'contract_category', name: 'contract_category'},
					// 				{data: 'working_schedule', name: 'working_schedule'},
					// 				{data: 'effective_date', name: 'effective_date'},
					// 				{data: 'expired_date', name: 'expired_date'},
									
					// 			]
					// 		});
     //                }, 500);	
					
					setTimeout(function () {
                        $('#table_education_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_education_employee_input').val(response.edu[index].id_education_employee);
                            $(this).find('.major_input').val(response.edu[index].major);
                            $(this).find('.education_name_input').val(response.edu[index].education_name);
                            $(this).find('.id_education_level_input').val(response.edu[index].id_education_level).trigger('change');
							$(this).find('.education_city_input').val(response.edu[index].education_city);
							$(this).find('.start_year_input').val(response.edu[index].start_year).trigger('change');
							$(this).find('.end_year_input').val(response.edu[index].end_year).trigger('change');
                        });                       
                    }, 500);
					
					setTimeout(function () {
                        $('#table_family_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_family_employee_input').val(response.fam[index].id_family_employee);
                            $(this).find('.family_name_input').val(response.fam[index].family_name);
                            $(this).find('.gender_input').val(response.fam[index].gender).trigger('change');
                            $(this).find('.relationship_input').val(response.fam[index].relationship);
							$(this).find('.mobile_phone_input').val(response.fam[index].mobile_phone);
                        });                       
                    }, 500);
					setTimeout(function () {
                        $('#table_ex_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_experience_employee_input').val(response.ex[index].id_experience_employee);
                            $(this).find('.position_name_input').val(response.ex[index].position_name).trigger('change');
                            $(this).find('.company_name_input').val(response.ex[index].company_name).trigger('change');
                            $(this).find('.company_city_input').val(response.ex[index].company_city).trigger('change');
                            $(this).find('.start_year_ex_input').val(response.ex[index].start_year).trigger('change');
							$(this).find('.end_year_ex_input').val(response.ex[index].end_year).trigger('change');
                        });                       
                    }, 500);
					
					setTimeout(function () {
                        $('#table_skill_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_skill_employee_input').val(response.skill[index].id_skill_employee);
                            $(this).find('.skill_name_input').val(response.skill[index].skill_name).trigger('change');
                            $(this).find('.skill_level_input').val(response.skill[index].skill_level).trigger('change');                           
                        });                       
                    }, 500);
					
					setTimeout(function () {
                        $('#table_cert_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_certification_employee_input').val(response.cert[index].id_certification_employee);
                            $(this).find('.certification_name_input').val(response.cert[index].certification_name).trigger('change');
                            $(this).find('.certified_by_input').val(response.cert[index].certified_by).trigger('change');
                            $(this).find('.years_issued_input').val(response.cert[index].years_issued).trigger('change');
                            $(this).find('.validity_period_input').val(response.cert[index].validity_period).trigger('change');
							// if(response.cert[index].attachment) {
							// 	$(this).find('.certification_attachment_link').attr('href', '/storage/'+response.cert[index].attachment).text('Download');
							// 	$(this).find('.certification_attachment_link').attr('target', '_blank');
							// }
                        });                       
                    }, 500);
					
					setTimeout(function () {
                        $('#table_bank_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_bank_employee_input').val(response.bank[index].id_bank_employee);
                            $(this).find('.id_bank_input').val(response.bank[index].id_bank).trigger('change');
                            $(this).find('.bank_name_input').val(response.bank[index].bank_name);
                            $(this).find('.bank_account_input').val(response.bank[index].bank_account);
                            $(this).find('.account_name_input').val(response.bank[index].account_name);
							$(this).find('.bank_currency_input').val(response.bank[index].bank_currency).trigger('change');
							if (response.bank[index].default_bank == 1) {
                                $(this).find('.default_bank_input').prop('checked', true);
                            } else {
                                $(this).find('.default_bank_input').prop('checked', false);
                            }
                        });                       
                    }, 500);
					
					setTimeout(function () {
                        $('#table_insurance_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_insurance_employee_input').val(response.ins[index].id_insurance_employee);
                            $(this).find('.id_insurance_input').val(response.ins[index].id_insurance).trigger('change');
                            $(this).find('.emp_insurance_number_input').val(response.ins[index].emp_insurance_number);
                            $(this).find('.effective_date_input').val(response.ins[index].effective_date);
                            $(this).find('.expired_date_input').val(response.ins[index].expired_date);
                            $(this).find('.beneficiary_name_input').val(response.ins[index].beneficiary_name);
                           
                        });                       
                    }, 500);
					// setTimeout(function () {
					// 	$.extend( true, $.fn.dataTable.defaults, {
					// 			 columnDefs:false,
					// 			 paging:false,
					// 			 searching:false,
					// 			 lengthChange: false,
					// 			 info: false,
					// 			 dom: '<"toolbar">frtip',
					// 		});
							
					// 		$('#table_leave').DataTable({	
					// 			destroy:true,
					// 			ajax: {
					// 				url: "<?= url('employee/employee/employee/get_emp_leave') . '?id_employee=' ?>" + global_id_employee,
					// 				error: function (jqXHR, textStatus, errorThrown) {
					// 						$('#table_leave').DataTable().ajax.reload();
					// 					}
					// 			},	
					// 			columns: [								
					// 				{data: 'DT_RowIndex', name: 'DT_RowIndex'},
					// 				{data: 'leave_type', name: 'leave_type'},
					// 				{data: 'leave_quota', name: 'leave_quota'},
					// 				{data: 'used_leave', name: 'used_leave'},
					// 				{data: 'effective_date', name: 'effective_date'},
					// 				{data: 'expired_date', name: 'expired_date'},
									
					// 			]
					// 		});
					// }, 500);	


					// setTimeout(function () {
					// 	$.extend( true, $.fn.dataTable.defaults, {
					// 			 columnDefs:false,
					// 			 paging:false,
					// 			 searching:false,
					// 			 lengthChange: false,
					// 			 info: false,
					// 			 dom: '<"toolbar">frtip',
					// 		});							
					// 		$('#table_career').DataTable({	
					// 			destroy:true,
					// 			ajax: {
					// 				url: "<?= url('employee/employee/employee/get_emp_career') . '?id_employee=' ?>" + global_id_employee,
					// 				error: function (jqXHR, textStatus, errorThrown) {
					// 						$('#table_career').DataTable().ajax.reload();
					// 					}
					// 			},	
					// 			columns: [								
					// 				{data: 'DT_RowIndex', name: 'DT_RowIndex'},
					// 				{data: 'reference_number', name: 'reference_number'},
					// 				{data: 'transition_category', name: 'transition_category'},
					// 				{data: 'transaction_type', name: 'transaction_type'},
					// 				{data: 'employment_status', name: 'employment_status'},
					// 				{data: 'position_detail', name: 'position_detail'},
					// 				{data: 'position_routing', name: 'position_routing'},
					// 				{data: 'job_grade', name: 'job_grade'},
					// 				{data: 'job_status', name: 'job_status'},
					// 				{data: 'location', name: 'location'},
					// 				{data: 'effective_date', name: 'effective_date'},
					// 				{data: 'expired_date', name: 'expired_date'},									
					// 			]
					// 		});
                                      
     //                }, 500);

     
					// setTimeout(function () {
					// 		$.extend( true, $.fn.dataTable.defaults, {
					// 			 columnDefs:false,
					// 			 paging:false,
					// 			 searching:false,
					// 			 lengthChange: false,
					// 			 info: false,
					// 			 dom: '<"toolbar">frtip',
					// 		});							
					// 		$('#table_awdcp').DataTable({	
					// 			destroy:true,
					// 			ajax: {
					// 				url: "<?= url('employee/employee/employee/get_emp_awdcp') . '?id_employee=' ?>" + global_id_employee,
					// 				error: function (jqXHR, textStatus, errorThrown) {
					// 						$('#table_awdcp').DataTable().ajax.reload();
					// 					}
					// 			},	
					// 			columns: [								
					// 				{data: 'DT_RowIndex', name: 'DT_RowIndex'},
					// 				{data: 'reference_number', name: 'reference_number'},
					// 				{data: 'transaction_type', name: 'transaction_type'},
					// 				{data: 'description_name', name: 'description_name', render: function(data, type, row) {
					// 						return $("<div>").html(data).text();
					// 					}},
					// 				{data: 'reference_date', name: 'reference_date'},	
					// 				{data: 'effective_date', name: 'effective_date'},
					// 				{data: 'expired_date', name: 'expired_date'},									
					// 			]
					// 		});
     //                }, 500);

					setTimeout(function () {
                        $('#table_doc_body tr').each(function (index) {
							if(response.doc[index].document_name == 'KTP' || response.doc[index].document_name == 'Rekening Bank' || response.doc[index].document_name == 'KK' || response.doc[index].document_name == 'Surat Pernyataan'){
								$(this).find('.document_name_input').attr('readonly',true);
								$(this).find('.delete-doc').css('display', 'none');
							}
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_document_employee_input').val(response.doc[index].id_document_employee);
                            $(this).find('.document_name_input').val(response.doc[index].document_name);
                            $(this).find('.document_number_input').val(response.doc[index].document_number);
                            $(this).find('.effective_date_input').val(response.doc[index].effective_date);
							$(this).find('.expired_date_input').val(response.doc[index].expired_date);
						//	$(this).find('.attachment_input').val(response.doc[index].attachment).trigger('change');
							if(response.doc[index].attachment != null){
									document.getElementById('doc_'+[index]+'_attach').href = "../../project/storage/app/public/upload/data/"+response.nik_employee+"/"+response.doc[index].attachment;
									document.getElementById('doc_'+[index]+'_attach').text = response.doc[index].attachment;
							}
							
                        });                       
                    }, 500);
					setTimeout(function () {
                        $('#table_onboarding_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_checklist_employee_onboarding_input').val(response.onboarding[index].id_checklist_employee);
                            $(this).find('.id_checklist_onboarding_input').val(response.onboarding[index].id_checklist).trigger('change');
                            $(this).find('.remark_onboarding_input').val(response.onboarding[index].remark);
                            $(this).find('.effective_date_onboarding_input').val(response.onboarding[index].effective_date);
							if(response.onboarding[index].attachment != null){
									document.getElementById('onboarding_'+[index]+'_attach').href = "../../project/storage/app/public/upload/onboarding/"+response.nik_employee+"/"+response.onboarding[index].attachment;
									document.getElementById('onboarding_'+[index]+'_attach').text = "Download File";
							}
							if (response.onboarding[index].completed == 1) {
                            //    $(this).find('.completed_onboarding_input').prop('checked', true);
                                $(this).find('.completed_onboarding_input').html('<span class="badge badge-success">YES</span>');
                            } else {
                            //    $(this).find('.completed_onboarding_input').prop('checked', false);
                                $(this).find('.completed_onboarding_input').html('<span class="badge badge-danger">NO</span>');
                            }
							
                        });                       
                    }, 500);
					setTimeout(function () {
                        $('#table_offboarding_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_checklist_employee_offboarding_input').val(response.offboarding[index].id_checklist_employee);
                            $(this).find('.id_checklist_offboarding_input').val(response.offboarding[index].id_checklist).trigger('change');
                            $(this).find('.remark_offboarding_input').val(response.offboarding[index].remark);
                            $(this).find('.effective_date_offboarding_input').val(response.offboarding[index].effective_date);
							if(response.offboarding[index].attachment != null){
									document.getElementById('offboarding_'+[index]+'_attach').href = "../../project/storage/app/public/upload/offboarding/"+response.nik_employee+"/"+response.offboarding[index].attachment;
									document.getElementById('offboarding_'+[index]+'_attach').text = "Download File";
							}
							if (response.offboarding[index].completed == 1) {
                            //    $(this).find('.completed_offboarding_input').prop('checked', true);
                                $(this).find('.completed_offboarding_input').html('<span class="badge badge-success">YES</span>');
                            } else {
                            //    $(this).find('.completed_offboarding_input').prop('checked', false);
                                $(this).find('.completed_offboarding_input').html('<span class="badge badge-danger">NO</span>');
                            }
							
                        });                       
                    }, 500);
					
					// setTimeout(function () {
					// 	var no_ktp = response.identification_number;
					// 		$.extend( true, $.fn.dataTable.defaults, {
					// 			 columnDefs:false,
					// 			 paging:false,
					// 			 searching:false,
					// 			 lengthChange: false,
					// 			 info: false,
					// 			 dom: '<"toolbar">frtip',
					// 		});							
					// 		$('#table_history').DataTable({	
					// 			destroy:true,
					// 			ajax: {
					// 				url: "<?= url('employee/employee/employee/get_emp_history') . '?no_ktp=' ?>"+no_ktp,
					// 				error: function (jqXHR, textStatus, errorThrown) {
					// 						$('#table_history').DataTable().ajax.reload();
					// 					}
					// 			},	
					// 			columns: [								
					// 				{data: 'DT_RowIndex', name: 'DT_RowIndex'},
					// 				{data: 'nik_employee', name: 'nik_employee'},
					// 				{data: 'name', name: 'name'},
					// 				{data: 'join_date', name: 'join_date'},
					// 				{data: 'last_department', name: 'last_department'},
					// 				{data: 'last_position_routing', name: 'last_position_routing'},
					// 				{data: 'company', name: 'company'},
					// 				{data: 'status_emp', name: 'status_emp'},
					// 				{data: 'resign_date', name: 'resign_date'},
					// 				{data: 'terminate_reason', name: 'terminate_reason'},
					// 			]
					// 		});
     //                }, 500);
					
					get_emp_contract(global_id_employee)
					get_emp_leave(global_id_employee)
					get_emp_career(global_id_employee)
					get_emp_training(global_id_employee)
					get_emp_awdcp(global_id_employee)
					get_emp_history(response.identification_number)

					check_process()
                },
                complete: function(){
                	
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
						
            $('#modal_form_employee').modal('show');
	
        });

    });
	
function submit(){
	var formData = new FormData($('#employeeForm')[0]);
	$.ajax({
		type: 'POST',
		headers: {
			Accept: "application/json",
		},
		url: global_id_employee == '' ? "{{ route('employee.save') }}" : "{{ route('employee.update') }}",
		enctype: 'multipart/form-data',
		processData: false,  // Important!
		contentType: false,
		cache: false,
		data: formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			if (response.status == 'true') {
				$('#modal_form_employee').modal('hide');
				
				if($.isArray(response.data) && response.data.length < 1){ // utk kondisi update return berupa array kosong
					swal({
	                    icon: 'success',
	                    title: "Success",
	                    text: response.message,
	                }).then(function(){ 
						location.reload();
					});
					$('#employee_table').DataTable().ajax.reload();
				} 
				else { // utk kondisi submit return berupa obbject
					send_email(response.data.nik);
					// generate_workdays(response.data.id_company, response.data.nik)
				}
			}
			else if(response.status == 'false_date') {
					swal({
						icon: 'error',
						dangerMode: true,
						content: {
							element: "div",
							attributes: {
								innerText: response.message,
								className: "swal-red",
							},
						},
					}).then(function(){ 
						$('#loader').addClass('hidden')
					});
				}
			else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! '+response.message,
				}).then(function(){ 
					$('#loader').addClass('hidden')
  				});
			}
		},
		complete: function(){
			// $('#loader').addClass('hidden')
		},
		error: function (response) {
			$('#loader').addClass('hidden')
			
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				let err = "";
				Object.keys(errors).forEach(function (key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					if (tab_id != undefined) {
						$("#tab_employee_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
					}
					err += errors[key][0]+"\n";
				});
				
				swal({
					icon: 'error',
					dangerMode: true,
					content: {
						element: "div",
						attributes: {
							innerText: err,
							className: "swal-red",
						},
					},
				}).then(function(){ 
					$('#loader').addClass('hidden')
  				});
			}
		/*	else if (response.status === 500) {
				 $("#nik_employee").addClass("is-invalid");
				 $("#nik_employeeError").children("strong").text('The NIK Employee field has already been taken.');
			}
		*/
			else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! '+response.statusText,
				}).then(function(){ 
					$('#loader').addClass('hidden')
  				});
			}
		}
	});					
}	

const get_datatable_employee = async () => {
    $(".div_datatable").show();

    let myData = {
        nik: $("#employee_search").val() == '' ? null : $("#employee_search").val(),
        status: $("#employee_status").val() == '' ? null : $("#employee_status").val(),
    };

    var table_index = $('#employee_table').DataTable({
        processing: true,
	//	fixedHeader: true,
		serverSide: false,
    //    scrollY: true,
		responsive: true,
		destroy:true,
		pageLength: 10,
        ajax: {
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            "data": myData,
            url: "<?= url('employee/employee/employee/') . '?id_url=' ?>" + global_url_server,
			error: function (jqXHR, textStatus, errorThrown) {
				if(jqXHR.hasOwnProperty("responseJSON")){
					console.log("True");
				}
				else{
					$('#employee_table').DataTable().ajax.reload();
				}
			}			
        },
		rowCallback: function(row, data, index){
				if(access_create == 0){
					$(row).find('.new').css('display', 'none');
				}	
				if(access_edit == 0){
					$(row).find('.edit').css('display', 'none');
				}		
				if(access_delete == 0){
					$(row).find('.delete').css('display', 'none');
				}
				if(access_print == 0){
					$(row).find('.print').css('display', 'none');
				}	
		},
        columns: [
			{
            defaultContent: '',
			orderable: false,
			},
            {   // Checkbox select column
            data: 'id_employee',
            defaultContent: '',
            orderable: false
			},
            {defaultContent: '',orderable: false},
            {data: 'nik_employee', name: 'nik_employee'},
            {data: 'employee_name', name: 'employee_name'},
            {data: 'private_mail', name: 'private_mail'},
            {data: 'join_date', name: 'join_date'},
            {data: 'position_routing', name: 'position_routing'},
            {data: 'department', name: 'department'},
            {data: 'principal', name: 'principal'},
            {data: 'region', name: 'region'},
            {data: 'branch', name: 'branch'},
            {data: 'job_grade', name: 'job_grade'},
            {data: 'status', name: 'status'},
            {data: 'employment_status', name: 'employment_status'},
            {data: 'resign_date', name: 'resign_date'},
            {data: 'action', name: 'action', className:'space' ,orderable: false, render: function (data, type, row) {					
					return data;
				}
            },
        ],
		"fnInitComplete": function (oSettings) {
			if(myData['status'] == null || (myData['status'] != null && myData['status'].indexOf('I') < 0)){
				//jika filter status tidak diisi atau diisi tanpa status I
		   		$('#employee_table_wrapper .column-filter-widget:eq(13)').find("select option:contains('A')").attr('selected','selected').change();
				$.each(global_ex_concurent, function (i, item) {
			   		$('#employee_table_wrapper .column-filter-widget:eq(14)').find("select option:contains('"+item.description+"')").attr('selected','selected').change();
				});
			}
		}
    });
	
	table_index.on('order.dt search.dt', function () {
        let i = 1;
        table_index.cells(null, 2, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
	$('.cf').select2({width:'100%'});
	$('#advanced').click(function(){
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}		
	});	
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

$(document).on('click', '#search', function () {
    get_datatable_employee()
});

$(document).ready(function(){
    // get_datatable_employee() //default dimatikan agar saat masuk page employee list tidak loading lama

	getEmployeeByAccessGroup().then(function(value) {
        $('#employee_search').html('');
        $('#employee_search').select2({
            placeholder: "Select Employee",
            data: value,
            allowClear: true,
        });
    });

	$('#employee_status').select2({
        placeholder: "Select Status",
        data: list_status,
        allowClear: true,
    });

	bsCustomFileInput.init();
	refresh_data();
 
});

$(document).on('click', '.delete', function(event) {
    id_employee = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        buttons: true,
        dangerMode: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
                url: '<?= url('employee/employee/employee/destroy') ?>' +'/' + id_employee,
                success: function(data) {
                	if(data.status == 'true'){
                		setTimeout(function() {
	                        $('#confirmModal').modal('hide');
	                        $('#employee_table').DataTable().ajax.reload();
	                        swal({
	                            title: "Data Deleted!",
	                            icon: "success",
	                            buttons: {
	                                confirm: {
	                                    className: 'btn-success'
	                                },
	                            },
	                        }).then(ok => {
	                            // location.reload();
	                        });
	                    }, 50);
                	} else {
                		swal({
							icon: 'error',
							dangerMode: true,
							content: {
								element: "div",
								attributes: {
									innerText: data.message,
									className: "swal-red",
								},
							},
						}).then(function(){ 
							$('#loader').addClass('hidden')
						});
                	}
                }
            })
        }
    });
});

function refresh_data() {	
/*	var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }   
            reader.readAsDataURL(input.files[0]);
        }
    }
*/	
    $(".file-upload").on('change', function(){
		uploadFile(this);
     //   readURL(this);		
    });
   
    $(".upload-button").on('click', function() {
       $(".file-upload").click();
    });
	
		$('#select2status').select2({width:'100%'});
		$('#ptkp_status_list').select2({
			width:'100%',
			placeholder: "Select PTKP Status",
		});
		$('#marital').select2({width:'100%'});
		$('#gender').select2({width:'100%'});
		$('#birthdate').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#spouse_birthdate').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#join_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#expired_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#permanent_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#resign_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#lasted_date_vaccine').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		$('#ptkp_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});
		
		get_company();
		get_empstatus();
		get_shift();
		get_leave();
		get_user();
		get_timezone();
		get_vaccine();
		get_country();
		get_religion();
		get_education_level();
		get_bank();
		get_currency();
		get_insurance();
		get_checklist_onboarding();
		get_checklist_offboarding();
		get_ex_concurent();
		getLeaveCustom();
		
//    $('#employee_table').DataTable().ajax.reload();
    }
	
function get_company() {
		$.getJSON('<?= url('employee/employee/employee/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });			
        }).fail(function (data) { // Call failed
            get_company();
        });
}
function get_empstatus() {
		$.getJSON('<?= url('employee/employee/employee/get_empstatus') ?>', function (data) {
            $('#id_employment_status').select2({
                data: data,
            });			
        }).fail(function (data) { // Call failed
            get_empstatus();
        });
}
function get_shift() {
		$.getJSON('<?= url('employee/employee/employee/get_shift') ?>', function (data) {
            $('#id_shift_group').prepend('<option selected></option>').select2({
                data: data,
                allowClear:true,
                placeholder:'Select Work Hours'
            });			
        }).fail(function (data) { // Call failed
            get_shift();
        });
}
function get_leave() {
		$.getJSON('<?= url('employee/employee/employee/get_leave') ?>', function (data) {
            $('#id_leave').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_leave();
        });
}
function get_user() {
	
	$.getJSON('<?= url('employee/employee/employee/get_user') ?>', function (data) {
		$('#id_user').select2({
			data: data,
			placeholder: "Select ID User",
			allowClear:true,
		});			
	});
	/*
			$('#id_user').select2({
			  placeholder: "Select ID User",
			  allowClear:true,
			  ajax: {
				url: '<?= url('employee/employee/employee/get_user') ?>',
				data: function (params) {
					var queryParameters = {
						search: params.term
					}
					return queryParameters;
				},
				processResults: function (data) {
					return {
					  results: data
					};
				  },
				error: function (jqXHR, textStatus, errorThrown) {
					get_user();
				}
			}
		});		
	*/
}
function get_timezone() {
		$.getJSON('<?= url('employee/employee/employee/get_timezone') ?>', function (data) {
            $('#id_timezone').select2({
                data: data,
            });			
        }).fail(function (data) { // Call failed
            get_timezone();
        });
}
function get_vaccine() {
		$.getJSON('<?= url('employee/employee/employee/get_vaccine') ?>', function (data) {
            $('#id_vaccination_status').select2({
                data: data,
            });			
        }).fail(function (data) { // Call failed
            get_vaccine();
        });
}
function get_country() {
		$.getJSON('<?= url('employee/employee/employee/get_country') ?>', function (data) {
            $('#id_country').select2({
                data: data,
            });
			$('#id_country').val('93').select2();
			$('#id_country_of_birth').select2({
                data: data,
            });	
			$('#id_country_of_birth').val('93').select2();
        }).fail(function (data) { // Call failed
            get_country();
        });
		
}
function get_religion() {
		$.getJSON('<?= url('employee/employee/employee/get_religion') ?>', function (data) {
            $('#id_religion').select2({
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_religion();
        });
		
}
function get_education_level() {
		$.getJSON('<?= url('employee/employee/employee/get_education_level') ?>', function (data) {
            global_id_education_level = data;
        }).fail(function (data) { // Call failed
            get_education_level();
        });
}
function get_bank() {
		$.getJSON('<?= url('employee/employee/employee/get_bank') ?>', function (data) {
            global_id_bank = data;
        }).fail(function (data) { // Call failed
            get_bank();
        });
}
function get_currency() {
		$.getJSON('<?= url('employee/employee/employee/get_currency') ?>', function (data) {
            global_bank_currency = data;
        }).fail(function (data) { // Call failed
            get_currency();
        });
}
function get_insurance() {
	$.getJSON('<?= url('employee/employee/employee/get_insurance') ?>', function (data) {
            global_id_insurance = data;
        }).fail(function (data) { // Call failed
            get_insurance();
        });
}
function get_checklist_onboarding() {
	$.getJSON('<?= url('employee/employee/employee/get_checklist_onboarding') ?>', function (data) {
            global_id_checklist_onboarding = data;
        }).fail(function (data) { // Call failed
            get_checklist_onboarding();
        });
}
function get_checklist_offboarding() {
	$.getJSON('<?= url('employee/employee/employee/get_checklist_offboarding') ?>', function (data) {
            global_id_checklist_offboarding = data;
        }).fail(function (data) { // Call failed
            get_checklist_offboarding();
        });		
}

function get_ex_concurent() {
	$.getJSON('<?= url('employee/employee/employee/get_ex_concurent') ?>', function (data) {
            global_ex_concurent = data;
        }).fail(function (data) { // Call failed
            get_ex_concurent();
        });		
}

function send_email(nik='') {
	$.ajax({
	   	url: "<?= url('mail/new_account') ?>" +'/'+nik,
	   	beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
	   	success:function(res_email){
	   		if (res_email.status == 'true') {
	   			swal({
                    icon: 'success',
                    title: "Success",
                    text: 'Sending Mail Successfully',
                    buttons: false,
                    timer: 1200
                });
			}
	   		$(document).ajaxStop(function() {
				$('#loader').addClass('hidden');
			    swal({
					icon: 'success',
					title: 'Success',
					text: 'Employee Data Saved Successfully',
				}).then(function(){ 
	   				location.reload();
  				});
			});
	    },
		complete: function(){
			// $('#loader').addClass('hidden')
		},
		error: function (xhr) {
			// send_email(nik);
		},
  	});
}

function generate_workdays(id_company, nik) {
	$.ajax({
	   	url: "<?= url('time_attendance/generateWorkdaysByEmployee') ?>",
		type: 'POST',
		headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
		data: {id_company:id_company, nik:nik},
	   	success:function(res){
			return 'workdays success';
	    },
		error: function (xhr) {
			generate_workdays(id_company, nik);
		},
  	});
}



function get_emp_contract(global_id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_contract').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('employee/employee/employee/get_emp_contract') . '?id_employee=' ?>" + global_id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_contract').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'contract_number', name: 'contract_number'},
			{data: 'contract_category', name: 'contract_category'},
			{data: 'working_schedule', name: 'working_schedule'},
			{data: 'effective_date', name: 'effective_date'},
			{data: 'expired_date', name: 'expired_date'},
			
		]
	});
}

function get_emp_leave(global_id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_leave').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('employee/employee/employee/get_emp_leave') . '?id_employee=' ?>" + global_id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_leave').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'leave_type', name: 'leave_type', 
				render: function(data, type, row) {
					if(row.restrict_by != 'System'){
						return `<input hidden id="add_id_leave_balance_${row.id_leave_balance_emp}" value="${row.id_leave_balance_emp}"><select class="add_leave_type" id="add_leave_type_${row.id_leave_balance_emp}" style="display:none;"><option value"${row.id_leave_type}" selected></option></select><span id="show_add_leave_type_${row.id_leave_balance_emp}">${row.leave_type}</span>`;
					} else {
						return row.leave_type;
					}
				}
			},
			{data: 'leave_quota', name: 'leave_quota', 
				render: function(data, type, row) {
					if(row.restrict_by != 'System'){
						return `<span id="show_leave_quota_${row.id_leave_balance_emp}">${row.leave_quota}</span><input type="text" style="display:none;" class="form-control numeric" id="add_leave_quota_${row.id_leave_balance_emp}" value="${row.leave_quota}"><span class="invalid-feedback-leave text-red row_${row.id_leave_balance_emp}" id="invalid_feedback_add_leave_quota_${row.id_leave_balance_emp}" role="alert"></span>`;
					} else {
						return row.leave_quota;
					}
				}
			},
			{data: 'used_leave', name: 'used_leave'},
			{data: 'effective_date', name: 'effective_date', width:'140px',
				render: function(data, type, row) {
					if(row.restrict_by != 'System'){
						return `<span id="show_effective_date_${row.id_leave_balance_emp}">${row.effective_date}</span><input type="text" style="display:none;" class="form-control" id="add_effective_date_${row.id_leave_balance_emp}" value="${row.effective_date}"><span class="invalid-feedback-leave text-red row_${row.id_leave_balance_emp}" id="invalid_feedback_add_effective_date_${row.id_leave_balance_emp}" role="alert"></span>`;
					} else {
						return row.effective_date;
					}
				}
			},
			{data: 'expired_date', name: 'expired_date', width:'140px',
				render: function(data, type, row) {
					if(row.restrict_by != 'System'){
						return `<span id="show_expired_date_${row.id_leave_balance_emp}">${row.expired_date}</span><input type="text" style="display:none;" class="form-control" id="add_expired_date_${row.id_leave_balance_emp}" value="${row.expired_date}"><span class="invalid-feedback-leave text-red row_${row.id_leave_balance_emp}" id="invalid_feedback_add_expired_date_${row.id_leave_balance_emp}" role="alert"></span>`;
					} else {
						return row.expired_date;
					}
				}
			},
			{data: 'action', name: 'action', 
				render: function(data, type, row) {
					if(row.restrict_by != 'System'){
						return `<button onclick="return false;" title="Edit" class="edit_add_leave btn btn-xs btn-primary btn_edit_add_leave_${row.id_leave_balance_emp}" data-id="${row.id_leave_balance_emp}" id_leave_type="${row.id_leave_type}"><span class="fas fa-edit"></span></button><button style="display:none;" onclick="return false;" title="Save" class="save_add_leave btn btn-xs btn-success btn_save_add_leave_${row.id_leave_balance_emp}" data-id="${row.id_leave_balance_emp}"><span class="far fa-list-alt"></span></button>`;
					} else {
						return '';
					}
				}
			}
		]
	});
}

function get_emp_training(global_id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_learning').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('talent_management/talent_development/talent_profile/get_training') . '?id_employee=' ?>" + global_id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_learning').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'program_name', name: 'program_name'},
			{data: 'course_name', name: 'course_name'},
			{data: 'user_score', name: 'user_score'},
			{data: 'status', name: 'status', className: 'text-center', render: function ( data, type, row ) {	
					if(row.status == 'Pass'){
						return '<span class="badge badge-success" style="padding:6px;font-size:13px;">'+data+'</span>';
					}
					else{
						return '<span class="badge badge-danger" style="padding:6px;color:white;font-size:13px;">'+data+'</span>';
					}
				} 
			},	
			{data: 'hit_miss', name: 'hit_miss'},
			{data: 'rating', name: 'rating'},
			{data: 'creation_date', name: 'creation_date'},
		]
	});
}


function get_emp_career(global_id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_career').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('employee/employee/employee/get_emp_career') . '?id_employee=' ?>" + global_id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_career').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'reference_number', name: 'reference_number'},
			{data: 'transition_category', name: 'transition_category'},
			{data: 'transaction_type', name: 'transaction_type'},
			{data: 'employment_status', name: 'employment_status'},
			{data: 'position_detail', name: 'position_detail'},
			{data: 'position_routing', name: 'position_routing'},
			{data: 'job_grade', name: 'job_grade'},
			{data: 'job_status', name: 'job_status'},
			{data: 'location', name: 'location'},
			{data: 'effective_date', name: 'effective_date'},
			{data: 'expired_date', name: 'expired_date'},									
		]
	});
}

function get_emp_awdcp(global_id_employee) {
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_awdcp').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('employee/employee/employee/get_emp_awdcp') . '?id_employee=' ?>" + global_id_employee,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_awdcp').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'reference_number', name: 'reference_number'},
			{data: 'transaction_type', name: 'transaction_type'},
			{data: 'description_name', name: 'description_name', render: function(data, type, row) {
					return $("<div>").html(data).text();
				}},
			{data: 'reference_date', name: 'reference_date'},	
			{data: 'effective_date', name: 'effective_date'},
			{data: 'expired_date', name: 'expired_date'},									
		]
	});
}

function get_emp_history(no_ktp) {
// var no_ktp = response.identification_number;
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});							
	$('#table_history').DataTable({	
		destroy:true,
		ajax: {
			url: "<?= url('employee/employee/employee/get_emp_history') . '?no_ktp=' ?>"+no_ktp,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_history').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'nik_employee', name: 'nik_employee'},
			{data: 'name', name: 'name'},
			{data: 'join_date', name: 'join_date'},
			{data: 'last_department', name: 'last_department'},
			{data: 'last_position_routing', name: 'last_position_routing'},
			{data: 'company', name: 'company'},
			{data: 'status_emp', name: 'status_emp'},
			{data: 'resign_date', name: 'resign_date'},
			{data: 'terminate_reason', name: 'terminate_reason'},
		]
	});
}

function check_process() {
	$(document).ajaxStop(function() {
		$('#loader').addClass('hidden');
	});
}

async function getLeaveCustom() {
    let result;
    try {
        result = await $.getJSON('<?= url('employee/employee/employee/getLeaveCustom') ?>', function (res) { 
			$.each(res, function (i, item) {
                let desc = item.description;
                global_leave_custom.push({id: item.id_leave_type, text:desc});
            });
        });
        return result;
    } catch (error) {
        getLeaveCustom();
    }
}

let row_add_leave = 0;
function addLeave() {
	let rowCount = $('#table_leave >tbody >tr').length + 1;
	let element = `
		<tr id="row_add_leave_${row_add_leave}">
			<td><input hidden id="add_id_leave_balance_${row_add_leave}">${rowCount}</td>
			<td><select class="form-control form-control-sm select2 add_leave_type" id="add_leave_type_${row_add_leave}" style="width: 100%;"></select><span class="invalid-feedback-leave text-red row_${row_add_leave}" id="invalid_feedback_add_leave_type_${row_add_leave}" role="alert"></span><span id="show_add_leave_type_${row_add_leave}" style="display:none;"></span></td>
			<td><span id="show_leave_quota_${row_add_leave}" style="display:none;"></span><input type="text" class="form-control numeric" id="add_leave_quota_${row_add_leave}"><span class="invalid-feedback-leave text-red row_${row_add_leave}" id="invalid_feedback_add_leave_quota_${row_add_leave}" role="alert"></span></td>
			<td><span id="show_used_leave_${row_add_leave}" style="display:none;"></span></td>
			<td><span id="show_effective_date_${row_add_leave}" style="display:none;"></span><input type="text" class="form-control" id="add_effective_date_${row_add_leave}"><span class="invalid-feedback-leave text-red row_${row_add_leave}" id="invalid_feedback_add_effective_date_${row_add_leave}" role="alert"></span></td>
			<td><span id="show_expired_date_${row_add_leave}" style="display:none;"></span><input type="text" class="form-control" id="add_expired_date_${row_add_leave}"><span class="invalid-feedback-leave text-red row_${row_add_leave}" id="invalid_feedback_add_expired_date_${row_add_leave}" role="alert"></span></td>
			<td><button onclick="return false;" title="Save" class="edit_add_leave btn btn-xs btn-primary btn_edit_add_leave_${row_add_leave}" data-id="${row_add_leave}" id_leave_type="${row_add_leave}" style="display:none;"><span class="fas fa-edit"></span></button><button onclick="return false;" title="Save" class="save_add_leave btn btn-xs btn-success btn_save_add_leave_${row_add_leave}" data-id="${row_add_leave}"><span class="far fa-list-alt"></span></button> &nbsp; <button onclick="return false;" id="button_delete_${row_add_leave}" title="Delete" class="delete_leave btn btn-xs btn-danger" data-id="${row_add_leave}"><span class="far fa-trash-alt"></span></button></td>
		</tr>`;

	$('#table_leave tbody').append(element);
	$(`#add_leave_type_${row_add_leave}`).select2({
        placeholder: "Select Leave",
        data: global_leave_custom,
        allowClear: true,
    });
    $(`#add_expired_date_${row_add_leave}`).datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$(`#add_effective_date_${row_add_leave}`).datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
	$(`.modal-body`).animate({scrollTop: $(`#add_leave_type_${row_add_leave}`).offset().top}, 1000);
	row_add_leave++;
}

$(document).on('click', '.delete_leave', function () {
	let id_add_leave = $(this).attr('data-id');
	$(`#row_add_leave_${id_add_leave}`).remove();
});

$(document).on('click', '.edit_add_leave', function () {
	let id_add_leave = $(this).attr('data-id');
	let id_leave_type = $(this).attr('id_leave_type');

	$(`#show_add_leave_type_${id_add_leave}`).show();
	$(`.btn_save_add_leave_${id_add_leave}`).show();
	$(`.btn_edit_add_leave_${id_add_leave}`).hide();
	$(`#show_leave_quota_${id_add_leave}`).hide();
	$(`#add_leave_quota_${id_add_leave}`).show();

	$(`#show_effective_date_${id_add_leave}`).hide();
	$(`#add_effective_date_${id_add_leave}`).parent().show();
	$(`#add_effective_date_${id_add_leave}`).show().datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});

	$(`#show_expired_date_${id_add_leave}`).hide();
	$(`#add_expired_date_${id_add_leave}`).parent().show();
	$(`#add_expired_date_${id_add_leave}`).show().datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});

	$(`#add_leave_type_${id_add_leave}`).empty();
	$(`#add_leave_type_${id_add_leave}`).select2({
        placeholder: "Select Leave",
        data: [{id:id_leave_type, text:''}],
        allowClear: true,
    }).next(".select2-container").hide();

});

$(document).on('click', '.save_add_leave', function () {
	let checkEmpty = [];
	let id_add_leave = $(this).attr('data-id');
	$(`.invalid-feedback-leave.row_${id_add_leave}`).html('');

	let id_leave_type = $(`#add_leave_type_${id_add_leave} :selected`).val();
	let leave_quota = $(`#add_leave_quota_${id_add_leave}`).val();
	let effective_date = $(`#add_effective_date_${id_add_leave}`).val();
	let expired_date = $(`#add_expired_date_${id_add_leave}`).val();
	let id_employee = $('#id_employee').val();
	let id_leave_balance_emp = $(`#add_id_leave_balance_${id_add_leave}`).val();

	if(id_leave_type=='' || id_leave_type==undefined){
		$(`#invalid_feedback_add_leave_type_${id_add_leave}`).html('<strong>Please Fill<strong>');
		checkEmpty.push(1);
	}
	if(leave_quota==''){
		$(`#invalid_feedback_add_leave_quota_${id_add_leave}`).html('<strong>Please Fill<strong>');
		checkEmpty.push(1);
	}
	if(effective_date==''){
		$(`#invalid_feedback_add_effective_date_${id_add_leave}`).html('<strong>Please Fill<strong>');
		checkEmpty.push(1);
	}
	if(expired_date==''){
		$(`#invalid_feedback_add_expired_date_${id_add_leave}`).html('<strong>Please Fill<strong>');
		checkEmpty.push(1);
	}
	if(checkEmpty.length > 0){
		return false;
	}

	let formData = {
		id_leave_balance_emp:id_leave_balance_emp,
		id_employee: id_employee,
		id_leave_type: id_leave_type,
		leave_quota: leave_quota,
		effective_date: effective_date,
		expired_date: expired_date,
	};
	$.ajax({
	   	url: "<?= url('employee/employee/employee/saveLeaveCustom') ?>",
	   	type: 'POST',
		headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
		data: formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
	   	success:function(res){
	   		if (res.status == 'true') {
	   			$(`#add_id_leave_balance_${id_add_leave}`).val(res.data.id_leave_balance_emp);
				$(`#button_delete_${id_add_leave}`).remove();
	   			$(`.btn_save_add_leave_${id_add_leave}`).hide();
				$(`.btn_edit_add_leave_${id_add_leave}`).attr('id_leave_type', id_leave_type).show();
	   			$(`#show_add_leave_type_${id_add_leave}`).html(res.data.leave_name);

				$(`#show_leave_quota_${id_add_leave}`).html(leave_quota).show();
				$(`#show_used_leave_${id_add_leave}`).html(res.data.used_leave).show();

				$(`#add_leave_quota_${id_add_leave}`).hide();
				$(`#show_effective_date_${id_add_leave}`).html(effective_date).show();
				$(`#add_effective_date_${id_add_leave}`).parent().hide();
				$(`#show_expired_date_${id_add_leave}`).html(expired_date).show();
				$(`#add_expired_date_${id_add_leave}`).parent().hide();

	   			swal({
                    icon: 'success',
                    title: "Success",
                    text: res.message,
                    buttons: false,
                    timer: 1500
                });
			} else {
				swal({
					icon: 'error',
					dangerMode: true,
					content: {
						element: "div",
						attributes: {
							innerText: res.message,
							className: "swal-red",
						},
					},
				});
			}
	    },
		complete: function(){
			$('#loader').addClass('hidden')
		},
		error: function (xhr) {
		},
  	});
});

$(document).on('keyup', '.numeric', function () {
    this.value = this.value.replace(/\D/g,'');
});

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}
$(document).on("click", ".pdf", function () {
	let res = {
        id_candidate: $(this).attr('id'),
    };
    let param = objectToQueryString(res);
	let url = "{{ url('recruitment/recruitment/candidate/download') }}";
    window.open(url+'?'+param, '_blank');
});

</script>
@endsection