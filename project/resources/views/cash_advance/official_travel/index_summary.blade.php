@extends('adminlte::page')
@section('title', 'Official Travel Summary')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Official Travel Summary
        </h5>
        <!-- <a tabindex="0" class="btn btn-lg btn-danger popover" role="button" data-toggle="popover" data-trigger="focus" title="Dismissible popover" data-content="And here's some amazing content. It's very engaging. Right?">Dismissible popover</a> -->
      </div>
	  <div class="card-header">
      <div class="row">
        <div class="col-lg-6">
         <div class="row">
          <label class="col-sm-4 col-form-label">Start to End Date <sup class="text-danger">*</sup></label>
          <div class="col-sm-6">
            <div class="input-group">
              <input type="text" autocomplete="off" name="start_end" id="start_end_filter" class="form-control form-control-sm start_end" style="width: 100%;">
              <div class="input-group-append">
                <span class="input-group-text">
                  <i class="fas fa-calendar"></i>
                </span>
              </div>
            </div>
            <span class="invalid-feedback d-block" role="alert" id="start_endError">
              <strong></strong>
            </span>
          </div>
          <div class="col-sm-2">
            <button class="btn btn-success btn-sm text-white" id="filter"><i class="fas fa-filter"></i> Filter</button>
          </div>
        </div>      
      </div>
    </div>
  </div>
      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table class="table table-hover table-bordered table-striped" id="officialtravel_table" style="width: 100%;">
          <thead>
            <tr>
              <th></th>
              <th></th>
              <th data-priority="8">No.</th>
              <th data-priority="2">Reference Number</th>
              <th data-priority="3">Request By</th>
              <th data-priority="6">Letter Date</th>
              <th>Travel Type</th>
              <!-- <th>Request by</th> -->
              <th>Start Date</th>
              <th>End Date</th>
              <th data-priority="7">Destination</th>
              <th data-priority="9">With Cash Advance</th>
              <th data-priority="5">Request Status</th>
              <th>Note Rejected</th>
              <th>Note Revised</th>
              <th data-priority="4">Approval Status</th>
              <th data-priority="1" align="center">Action</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal_form_travel" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Official Travel Form</h5>
        <!-- <button id="test">click</button> -->
        <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="travelForm" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Travel Type</label>
                <div class="col-sm-8">
                  <input type="hidden" hidden="" id="id_official_travel" name="id_official_travel">
                  <select id="id_reason_group" name="id_reason_group" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                  <span class="invalid-feedback" role="alert" id="id_reason_groupError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Request By </label>
                <div class="col-sm-8">
                  <select id="id_dept" hidden="" name="id_dept"></select>
                  <select id="request_by" name="request_by" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="request_byError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Position </label>
                <div class="col-sm-8">
                  <input type="" hidden="" id="id_position_detail" name="id_position_detail">
                  <select id="id_position_routing" name="id_position_routing" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_position_detailError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Region </label>
                <div class="col-sm-8">
                  <select id="id_region" name="id_region" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="overtime_limitError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Branch </label>
                <div class="col-sm-8">
                  <select id="id_branch" name="id_branch" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="maximum_overtimeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Division </label>
                <div class="col-sm-8">
                  <select id="id_division" name="id_division" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="lock_geo_locationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Job Grade </label>
                <div class="col-sm-8">
                  <select id="id_job_grade" name="id_job_grade" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_job_gradeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Destination</label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="location_to" id="location_to" class="form-control form-control-sm" style="width: 100%;text-transform: uppercase;">
                  <span class="invalid-feedback" role="alert" id="location_toError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Reason to Travel</label>
                <div class="col-sm-8">
                  <textarea class="form-control" id="reason_notes" rows="4" name="reason_notes" style="text-transform: uppercase;"></textarea>
                  <span class="invalid-feedback" role="alert" id="reason_notesError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Letter Date </label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="letter_date" id="letter_date" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="letter_dateError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Start & End Date </label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="start_end" id="start_end" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="start_endError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Unlock Location</label>
                <div class="col-sm-8">
                  <input type="checkbox" class="text mt-1" style="width: 20px;height: 20px;" name="unlock_gps" id="unlock_gps">
                  <span class="invalid-feedback" role="alert" id="unlock_gpsError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Approval Hierarchy </label>
                <div class="col-sm-8">
                  <select id="id_approval" name="id_approval" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_approvalError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Approved By </label>
                <div class="col-sm-8">
                  <select id="id_approval_request" name="id_approval_request" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_approval_requestError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Approval Status </label>
                <div class="col-sm-8">
				  <input type="" id="id_cashadvance_cash" name="id_cashadvance_cash" hidden="">
                  <select class="form-control form-control-sm select_opsi" id="id_approval_status" name="id_approval_status" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_approval_statusError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div style="border:2px solid #28a745;padding:5px 0 5px 20px;border-radius:5px;margin-top:8px;">
				  <div class="row">
					<div class="col-sm-12">
						<i>Note :<ul>
								<li> <i>Jika yang dipilih "Perjalanan Dinas", maka pengajuan maksimal H-<span class="max_label"></span>.</i></li>
								<li> <i>Jika yang dipilih "Mutasi Karir", maka pengajuan maksimal H-1.</i><br></li>
								<li> <i>Jika dibelikan HR maka "With HR Transport" dan "With HR Accommodation" dicentang.</i></li>
							</ul>
						</i>
					</div>
				  </div>
				  <div class="row" style="margin-top:-15px;">
					<label class="col-sm-3 col-form-label">With Transport</label>
					<div class="col-sm-2">
					  <input type="checkbox" class="text mt-2" style="width: 20px;height: 20px;" name="with_trans" id="with_trans">
					  <span class="invalid-feedback" role="alert" id="Error">
						<strong></strong>
					  </span>
					</div>
						<label class="col-sm-4 col-form-label">With Accommodation</label>
						<div class="col-sm-1">
						  <input type="checkbox" class="text mt-2" style="width: 20px;height: 20px;" name="with_accom" id="with_accom">
						  <span class="invalid-feedback" role="alert" id="Error">
							<strong></strong>
						  </span>
						</div>
				  </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">With Cash Advance</label>
                <div class="col-sm-1">
                  <!-- input type="" id="id_cashadvance_trans" name="id_cashadvance_trans" hidden="" -->
                  <!-- input type="" id="id_cashadvance_accom" name="id_cashadvance_accom" hidden="" -->
                  <input type="checkbox" class="text mt-2" style="width: 20px;height: 20px;" value="true" name="with_caseadvance" id="with_caseadvance">
                  <span class="invalid-feedback" role="alert" id="Error">
                    <strong></strong>
                  </span>
                </div>
                <div class="col-sm-7">
                  <span style="font-size: 14px;" class="text mt-3"><i>Note: Jika pengajuan maksimal H-7<span></span> maka "With Cash Advance" dicentang.</i></span>
                </div>
              </div>
            </div>
          </div>
          <!-- table -->
          <div class="row mt-5" id="rows_table_cash_advance">
            <div class="col-xl-12" id="tab_detail">
              <div class="nav nav-tabs justify-content-left mb-4">
                <a class="nav-item nav-link active" id="tab-transport" data-toggle="tab" href="#tab-pane-2">Transport
                  <span class="error-tab text-red">Error</span>
                </a>
                <a class="nav-item nav-link" id="tab-akomodasi" data-toggle="tab" href="#tab-pane-3">Accommodation
                  <span class="error-tab text-red">Error</span>
                </a>
                <a class="nav-item nav-link" id="tab-cashadvance" data-toggle="tab" href="#tab-pane-1">Cash Advance
                  <span class="error-tab text-red">Error</span>
                </a>
              </div>
            </div>
            <div class="col-xl-12">
              <div class="tab-content">
                <!-- Cash Advance -->
                <div class="tab-pane" id="tab-pane-1">
                  <input type="" id="id_cash_advance_del" hidden="" name="id_cash_advance_del">
                  <button type="button" id="new_cash_advance" class="btn btn-primary btn-sm mb-3" style="float: right;">
                   <i class="fa fa-plus"></i> Add Cash Advance
                 </button>
                 <div class="table-responsive col-md-12"  style="overflow:auto;">
                  <table class="table table-hover table-bordered table-striped responsive-table" style="width:1300px">
                    <thead>
                     <tr>
                      <th>No. </th>
                      <th>Category</th>
                      <th style="width:200px;">Start and End Date</th>
                      <th>Region Destination</th>
                      <th>Branch Destination</th>
                      <th style="width:50px;">Qty</th>
                      <th>Budget</th>
                      <th>Total</th>
                      <th>Notes</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody id="table_cash_advance">
                  </tbody>
                </table>
              </div>
            </div>
            <!-- Cash Advance -->
            <!-- Transport -->
            <div class="tab-pane fade show active" id="tab-pane-2">
              <input type="" id="id_transport_del" hidden="" name="id_transport_del">
              <button type="button" id="new_transport" class="btn btn-primary btn-sm mb-3" style="float: right;">
               <i class="fa fa-plus"></i> Add Transport
             </button>
             <div class="table-responsive col-md-12" style="overflow:auto;">
              <table class="table table-hover table-bordered table-striped responsive-table" style="width:1200px">
               <thead>
                 <tr>
                  <th rowspan="2">No. </th>
                  <th rowspan="2">Transport Type</th>
                  <th rowspan="2">Transport Recommendation</th>
                  <th rowspan="2">From</th>
                  <th rowspan="2">To</th>
                  <th rowspan="2">Destination Branch</th>
                  <th colspan="2" style="text-align: center;">Departure</th>
                  <th rowspan="2">Status</th>
                </tr>
                <tr>
                  <th style="width:150px;">Date</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody id="table_transport">
              </tbody>
            </table>
          </div>
        </div>
        <!-- End Transport -->
        <!-- Akomodasi -->
        <div class="tab-pane" id="tab-pane-3">
          <input type="" id="id_akomodasi_del" hidden="" name="id_akomodasi_del">
          <button type="button" id="new_accommodation" class="btn btn-primary btn-sm mb-3" style="float: right;">
           <i class="fa fa-plus"></i> Add Accommodation
         </button>
         <div class="table-responsive col-md-12" style="overflow:auto;">
          <table class="table table-hover table-bordered table-striped responsive-table" style="width:1200px">
            <thead>
             <tr>
              <th>No. </th>
              <th>Category</th>
              <th>Name of Hotel/Kos</th>
			  <th>City</th>
              <th>Destination Branch</th>
              <th style="width:200px;">Check In and Check Out Date</th>
              <th>Length of stay</th>
              <th>Status</th>
              <!-- <th>UOM</th> -->
            </tr>
          </thead>
          <tbody id="table_accommodation">
          </tbody>
        </table>
      </div>
    </div>
    <!-- End Akomodasi -->
  </div>
</div>
</div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
 <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
  <button class="btn btn-sm btn-info action_submit" id="saveForm" name="submitForm" value="submit"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit"></span></button>&nbsp;
  <!-- button class="btn btn-sm action" name="submitForm" id="submitForm" value="draft"><i class="fas fa-save"></i> <span id="label_button_action"></span></button -->&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button>
</div>
</form>
<!-- End Form -->
<!-- Rows Cash Advance -->
<div style="display:none;">
  <table id="sample_table_cash_advance">
    <tr id="">
      <td data-label="No. "><span class="sn" style="vertical-align:middle;"></span></td>   
      <td data-label="Category">
        <input autocomplete="off" name="cashadvance[0][id_cash_advance]" hidden="" id="cashadvance_0_id_cash_advance" class="form-control form-control-sm id_cash_advance_input">
        <select name="cashadvance[0][nama]" id="cashadvance_0_nama" class="form-control form-control-sm nama_input" style="width: 100%;"></select>
        <span class="invalid-feedback nama_input_error" role="alert" id="cashadvance_0_namaError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Start and End Date">
        <input type="text" autocomplete="off" name="cashadvance[0][tanggal]" id="cashadvance_0_tanggal" class="form-control form-control-sm tanggal_input">
        <span class="invalid-feedback tanggal_input_error" role="alert" id="cashadvance_0_tanggalError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Region Destination">
        <select name="cashadvance[0][region_cashadvance]" id="cashadvance_0_region_cashadvance" class="form-control form-control-sm region_cashadvance_input" style="width: 100%;"></select>
        <span class="invalid-feedback region_cashadvance_input_error" role="alert" id="cashadvance_0_region_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Branch Destination">
        <select name="cashadvance[0][branch_cashadvance]" id="cashadvance_0_branch_cashadvance" class="form-control form-control-sm branch_cashadvance_input" style="width: 100%;"></select>
        <span class="invalid-feedback branch_cashadvance_input_error" role="alert" id="cashadvance_0_branch_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Quantity">
        <input type="text" autocomplete="off" name="cashadvance[0][qty_cashadvance]" id="cashadvance_0_qty_cashadvance" class="form-control form-control-sm qty_cashadvance_input" readonly="">
        <span class="invalid-feedback qty_cashadvance_input_error" role="alert" id="cashadvance_0_qty_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Max Budget">
        <input type="text" autocomplete="off" name="cashadvance[0][max_budget]" id="cashadvance_0_max_budget" class="form-control form-control-sm max_budget_input" readonly="">
        <span class="invalid-feedback max_budget_input_error" role="alert" id="cashadvance_0_max_budgetError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Total">
        <input type="text" autocomplete="off" name="cashadvance[0][total]" id="cashadvance_0_total" class="form-control form-control-sm total_input" readonly="">
        <span class="invalid-feedback total_input_error" role="alert" id="cashadvance_0_totalError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Notes">
        <div style="width: 88%;float: left;">
          <input type="text" autocomplete="off" name="cashadvance[0][notes_cashadvance]" id="cashadvance_0_notes_cashadvance" class="form-control form-control-sm notes_cashadvance_input" style="text-transform: uppercase;">
		   <span class="invalid-feedback notes_cashadvance_input_error" role="alert" id="cashadvance_0_notes_cashadvanceError">
          <strong></strong>
        </span>
        </div>
        <div style="width: 10%;float: right;display: none;" class="div_help_input">
          <a href="javascript:void(0)" tabindex="0" class="help_input" role="button" data-toggle="popover" data-trigger="focus" title="Notes" data-content="......."><small id="cashadvance_0_help"><i class="fas fa-question-circle fa-lg"></i></small></a>
        </div>
        <span class="invalid-feedback notes_cashadvance_input_error" role="alert" id="cashadvance_0_notes_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Status">
        <div class="cashadvance_status" id="cashadvance_0_status"></div>
      </td>
    </tr>
  </table>
</div>
<!-- End CashAdvance -->
<!-- Tabel Transport -->
<div style="display:none;">
  <table id="sample_table_transport">
    <tr id="">
      <td data-label="No."><span class="sn" style="vertical-align:middle;"></span></td>   
      <td data-label="Transport Type">
        <input autocomplete="off" name="transport[0][id_transport]" hidden="" id="transport_0_id_transport" class="form-control form-control-sm id_transport_input">
        <select name="transport[0][jenis_transportasi]" id="transport_0_jenis_transportasi" class="form-control form-control-sm jenis_transportasi_input" style="width: 100%;"></select>
        <span class="invalid-feedback d-block jenis_transportasi_input_error" role="alert" id="transport_0_jenis_transportasiError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Transport Recommendation">
        <!-- <input name="transport[0][transport_name]" autocomplete="off" id="transport_0_transport_name" class="form-control form-control-sm transport_name_input"> -->
        <select name="transport[0][transport_name]" id="transport_0_transport_name" class="form-control form-control-sm transport_name_input" style="width: 100%;"></select>
        <span class="invalid-feedback transport_name_input_error" role="alert" id="transport_0_transport_nameError">
          <strong></strong>
        </span>
      </td>
      <td data-label="From">
        <div class="row">
          <div class="col-lg-12">
            <input name="transport[0][from]" autocomplete="off" id="transport_0_from" class="form-control form-control-sm from_input" style="width: 100%;text-transform: uppercase;">
            <span class="invalid-feedback from_input_error" role="alert" id="transport_0_fromError">
              <strong></strong>
            </span>
          </div>
        </div>
      </td>
      <td data-label="To">
        <div class="row">
          <div class="col-lg-12">
            <input name="transport[0][to]" autocomplete="off" style="width: 100%;text-transform: uppercase;" id="transport_0_to" class="form-control form-control-sm to_input">
            <span class="invalid-feedback to_input_error" role="alert" id="transport_0_toError">
              <strong></strong>
            </span>
          </div>
        </div>
      </td>
      <td data-label="Destination Branch">
        <div class="row">
          <div class="col-lg-12">
            <select name="transport[0][branch]" id="transport_0_branch" class="form-control form-control-sm branch_input" style="width: 100%;"></select>
            <span class="invalid-feedback d-block branch_input_error" role="alert" id="transport_0_branchError">
              <strong></strong>
            </span>
          </div>
        </div>
      </td>
      <td data-label="Date">
        <div class="input-group">
          <input type="text" autocomplete="off" name="transport[0][date_transport]" id="transport_0_date_transport" class="form-control form-control-sm date_transport_input" style="width: 100%;">
          <div class="input-group-append">
            <span class="input-group-text">
              <i class="fas fa-calendar"></i>
            </span>
          </div>
        </div>
        <span class="invalid-feedback d-block date_transport_input_error" role="alert" id="transport_0_date_transportError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Time">
        <input autocomplete="off" name="transport[0][time_transport]" id="transport_0_time_transport" class="form-control form-control-sm time_transport_input" style="width: 100%;">
        <span class="invalid-feedback d-block time_transport_input_error" role="alert" id="transport_0_time_transportError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Status">
        <div class="transport_status" id="transport_0_status"></div>
      </td>
    </tr>
  </table>
</div>
<!-- End Transport -->
<!-- Table Akomodasi -->
<div style="display:none;">
  <table id="sample_table_akomodasi">
    <tr id="">
      <td data-label="No. "><span class="sn" style="vertical-align:middle;"></span></td>   
      <td data-label="Category">
        <input autocomplete="off" name="akomodasi[0][id_akomodasi]" hidden="" id="akomodasi_0_id_akomodasi" class="form-control form-control-sm id_akomodasi_input">
        <select name="akomodasi[0][product_akomodasi]" id="akomodasi_0_product_akomodasi" class="form-control form-control-sm product_akomodasi_input" style="width: 100%;"></select>
        <span class="invalid-feedback d-block product_akomodasi_input_error" role="alert" id="akomodasi_0_product_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Name of Hotel/Kos">
        <input type="text" autocomplete="off" name="akomodasi[0][nama_hotel]" id="akomodasi_0_nama_hotel" class="form-control form-control-sm nama_hotel_input" style="text-transform: uppercase;">
        <span class="invalid-feedback nama_hotel_input_error" role="alert" id="akomodasi_0_nama_hotelError">
          <strong></strong>
        </span>
      </td>
	   <td data-label="City">
        <div class="row">
          <div class="col-lg-12">
            <input name="akomodasi[0][city]" autocomplete="off" style="width: 100%;text-transform: uppercase;" id="akomodasi_0_city" class="form-control form-control-sm city_input">
            <span class="invalid-feedback city_input_error" role="alert" id="akomodasi_0_toError">
              <strong></strong>
            </span>
          </div>
        </div>
      </td>
      <td data-label="Destination Branch">
        <div class="row">
          <div class="col-lg-12">
            <select name="akomodasi[0][branch]" id="akomodasi_0_branch" class="form-control form-control-sm ako_branch_input" style="width: 100%;"></select>
            <span class="invalid-feedback d-block ako_branch_input_error" role="alert" id="akomodasi_0_branchError">
              <strong></strong>
            </span>
          </div>
        </div>
      </td>
      <td data-label="Check In and Check Out Date">
        <input name="akomodasi[0][start_end_akomodasi]" autocomplete="off" id="akomodasi_0_start_end_akomodasi" class="form-control form-control-sm start_end_akomodasi_input">
        <span class="invalid-feedback start_end_akomodasi_input_error" role="alert" id="akomodasi_0_start_end_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Length of stay">
        <input type="text" autocomplete="off" name="akomodasi[0][lama_menginap]" id="akomodasi_0_lama_menginap" class="form-control form-control-sm lama_menginap_input">
        <span class="invalid-feedback lama_menginap_input_error" role="alert" id="akomodasi_0_lama_menginapError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Status">
        <div class="akomodasi_status" id="akomodasi_0_status"></div>
      </td>
    </tr>
  </table>
</div>
<!-- End Akomodasi -->
</div>
</div>
</div>
<!-- End Form modal -->
@endsection
@section('css')
<style type="text/css">
   .btn-request-cancel {
		padding:4px 7px 4px 7px;
		color: #fff;
		background-color: #ff6a79;
		box-shadow: none;
    }
	.btn-request-cancel:hover {
        color: #fff;
        background-color: #f04253;
        box-shadow: none;
    }
  .responsive-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
  }

  .responsive-table th,
  .responsive-table td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
  }
  #officialtravel_table td:nth-child(12) {
    text-align: center;
  }
  @media (max-width: 800px) {
  /* .branch_input + .select2-container .select2-selection{
    width: 30%;
    display: inline-block;
  }
  .branch_input + .select2-container .select2-selection__arrow{
    display: none;
    }*/
    .responsive-table thead{
      display: none;
    }
    .responsive-table tr {
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
    }

    .responsive-table td {
      width: 100%;
      box-sizing: border-box;
      display: inline-block;
    }

    .responsive-table td:before {
      content: attr(data-label);
      font-weight: bold;
      margin-right: 8px;
    }
  }

  .modal-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    position: absolute;
    top: 50%;
    left: 50%;
    z-index: 9999;
    visibility: hidden;
  }
  .modal-body {
    position: relative;
  }
  .modal.show .modal-loading {
    visibility: visible;
  }

  select[readonly].select2-hidden-accessible + .select2-container {
    pointer-events: none;
    touch-action: none;
  }
  select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
    background: #e8ebed;
    box-shadow: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection_clear {
    display: none;
  }

  select[disabled].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[disabled].select2-hidden-accessible + .select2-container .select2-selection_clear {
    display: none;
  }

  .is-invalid:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  *:focus{
    outline:0px;
  }
  .readonly-checkbox {
    pointer-events: none; /* Mencegah interaksi mouse */
    opacity: 0.5; /* Mengurangi kejelasan untuk memberikan efek tidak aktif */
  }
  .readonly-checkbox:hover {
    cursor: not-allowed; /* Menetapkan ikon kursor "not allowed" saat dihover */
  }
  /* CSS */
  .name-column {
    white-space: nowrap;
  }
  textarea {
    text-transform: uppercase;
  }
  td.text-center{
		text-align:center;
	}

</style>
@endsection

@section('scripts')
<script type="text/javascript">
let global_reschedule = "";

  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMM YYYY');
    return formattedDate;
  }
  
 $(document).on('click', '#filter', function() {
    start_end = $("#start_end_filter").val();
    var split = start_end.split('to');
    var start_date = split[0];
    var end_date = split[1];
	$('#officialtravel_table').DataTable().destroy();
    if (start_end) {
      $("#start_end").removeClass('is-invalid');
      $("#start_endError").children("strong").text('');
      $('#officialtravel_table').DataTable().destroy();
      DataTableBgen(start_end);
    }else{
		DataTableBgen(start_end);	
    //  $("#start_end").addClass("is-invalid");
    //  $("#start_endError").children("strong").text("The Star End Date field is required.");
    }
  });
  
  $("#start_end, #start_end_filter").daterangepicker({
    autoUpdateInput: false,
    autoApply: false,
    locale: {
      cancelLabel: 'Reset',
      format: 'YYYY-MM-DD',
      separator: ' to '
    }
  }).on('apply.daterangepicker',function(ev, picker) {
    var startDate = picker.startDate;
    var endDate = picker.endDate;
    $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
  }).on('cancel.daterangepicker', function() {
    $(this).val('');
  }).on('keydown.daterangepicker',function(e) {
    e.preventDefault();
  });
  var startOfWeek = moment().subtract(14, 'days');
  var endOfWeek = moment();
  start_end = startOfWeek.format('YYYY-MM-DD') + ' to ' + endOfWeek.format('YYYY-MM-DD');
  $('#start_end_filter').val(start_end);
  $('#start_end_filter').data('daterangepicker').setStartDate(startOfWeek);
  $('#start_end_filter').data('daterangepicker').setEndDate(endOfWeek);

// var start_end = '';
 function DataTableBgen(start_end) {  
    $('#officialtravel_table').DataTable({
      processing: true,
      pageLength: 50,
      responsive: true,  
      ajax: {
        url: "{{ route('index_summary.offtrave') }}",
		data: {start_end: start_end},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#officialtravel_table').DataTable().ajax.reload();
        }
      },  
      columns: [
      {
        defaultContent: '',
        orderable: false
      },
      {   
         data: 'id_official_travel',
         defaultContent: '',
         orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'reference_number', name: 'reference_number' },
      { data: 'name', name: 'name' },
      { 
        data: 'letter_date', 
        name: 'letter_date', 
        render: function (data, type, row) {
          return TanggalIndonesia(data);
        }  
      },
      { data: 'category', name: 'category' },
      // { 
      //   data: 'name', 
      //   name: 'name', 
      //   render: function (data, type, row) {
      //     return data;
      //   }
      // },
      {
        data: 'start_date',
        name: 'start_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
			  return moment(data).format('dddd, D MMM YYYY');
          }
          return data;
        }
      },
      {
        data: 'end_date',
        name: 'end_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
			return moment(data).format('dddd, D MMM YYYY');
          }
          return data;
        }
      },
        // { data: 'location_from', name: 'location_from' },
        { data: 'location_to', name: 'location_to' },
		{ data: 'is_have_cash_advance', name: 'is_have_cash_advance', className: 'text-center', render: function ( data, type, row ) {	
				if(data == true){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">Yes</span>';
				}
				else{
					return '<span class="badge badge-danger" style="font-size: 12px;padding:5px;">No</span>';
				}
			}
		},
		{ data: 'travel_status', name: 'travel_status', className: 'text-center', render: function ( data, type, row ) {	
				if(data == 'Onschedule'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">On Schedule</span>';
				}
				else if(data == 'Reschedule'){
						return '<span class="badge badge-info" style="padding:5px;font-size:12px;">Reschedule</span>';
				}
				else if(data == 'Cancel'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Request Cancel</span>';
				}
				else{
					return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
				}
			}
		},
        { data: 'note_rejected', name: 'note_rejected' },
        { data: 'note_revised', name: 'note_revised' },
        { data: 'desc_app_status', name: 'desc_app_status', className: 'text-center', render: function ( data, type, row ) {	
				if(row.code_app_status == 'Approved' && row.travel_status == 'Cancel'){
						return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">Cancellation Approved</span>';
				}
				else if(row.code_app_status == 'Approved'){
						return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(row.code_app_status == 'Cancel'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(row.code_app_status == 'Partial_Approved'){
					return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(row.code_app_status == 'Rejected'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else if(row.code_app_status == 'Revised'){
					return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
				}
				else{
					return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
				}
			}
		},
        { data: 'action', name: 'action', orderable: false, className: 'space' }
        ],
        rowCallback: function(row, data, index){
          if(access_create == 0){
            $(row).find('.new').css('display', 'none');
          } 
          if(access_edit == 0){
            $(row).find('.btn-edit').css('display', 'none');
          }   
          if(access_delete == 0){
            $(row).find('.btn-del').css('display', 'none');
          }
        },
      });
  };

  function show_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "block";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "none";
      ModalBody[i].style.background = 'white';
      ModalBody[i].style.opacity = '0.4';
    }
  }
  function hide_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "none";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "auto";
      ModalBody[i].style.background = "transparent";
      ModalBody[i].style.opacity = '1';
    }
  }
  let global_product_transport = [];
  let global_product_akomodasi = [];
  let global_product_cashadvance = [];
  let global_region_destination = [];
  let global_branch_transport = [];
  var maximum_official_travel_request = "";
  let cek_pos_routing = [];
  var job_class_group = "";
  var position_routing = 0;
  
const get_data_view = async (officialtravelID) => {	  
	let result;
    try {
        result = await $.ajax({
			  method: "GET",
			  url : "{{url('cash_advance/official_travel/official_travel/get_data_view')}}",
			  data : {id_official_travel:officialtravelID},
			  beforeSend: function () {
				$('#loader').removeClass('hidden');
			  },
			  success: function (response) {
				if (response.travel_type.length > 0) {
				  $("#id_reason_group").select2({
					allowClear: true,
					data: response.travel_type.map(function(item) {
					  return { id: item.id_general_data, text: item.description };
					})
				  });
				}
				if (response.user.length > 0) {
				// hide_loading();
				global_product_transport = response.product_transport;
				global_product_akomodasi = response.product_akomodasi;
				global_product_cashadvance = response.product_cashadvance;
				// global_region_destination = response.region_destination;
				global_branch_transport = response.branch_transport;
				maximum_official_travel_request = response.maximum_official_travel_request.maximum_official_travel_request;
				cek_pos_routing = response.cek_position_routing;
				$(".max_label").html(maximum_official_travel_request);
				id_employee_by = response.user[0].id_employee;
				$("#id_dept").append('<option value="'+response.user[0].id_dept+'">'+response.user[0].id_dept+'</option>');
				$("#id_position_detail").val(response.user[0].id_position_detail);
				$("#id_reason_group").val(null).trigger('change');
				$("#request_by").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_employee, text: item.name };
				  })
				});
				$("#id_position_routing").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_routing, text: item.dec_position };
				  })
				});
				$("#id_region").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_region, text: item.dec_region };
				  })
				});
				$("#id_branch").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_branch, text: item.dec_branch };
				  })
				});
				$("#id_division").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_division, text: item.dec_division };
				  })
				});
				$("#id_job_grade").select2({
				  data: response.user.map(function(item) {
					return { id: item.id_job_grade, text: item.dec_job_grade };
				  })
				});
				job_class_group = response.user[0].job_class_group;
				position_routing = response.user[0].id_routing;
				$("#id_approval_status").select2({
				  data: response.approval_status
				});
				get_approval_by(id_employee_by);
			  }
			},
	});  
        return result;
    } catch (error) {
    //    get_status_tracking();
    }	 
  }
 
  function get_approval_by(id_employee) {
   $.ajax({
    method: "GET",
    url : "{{url('cash_advance/official_travel/official_travel/get_approval_by')}}",
    data: {id_employee: id_employee},
    success: function (response) {
      $("#id_approval").empty();
      $("#id_approval_request").empty();
      if (response.count_by > 0) {
        $("#id_approval").select2({
          data: response.approval_hirarki
        });
        // $("#id_approval_request").select2({
        //   data: response.approval_by
        // });
      }else{
        if (response.job_class_group == 'gm-level') {
          $("#id_approval").select2({
            data: response.approval_hirarki
          });
          // $("#id_approval_request").select2({
          //   data: response.approval_by
          // });
        }
      }
      hide_loading();
    },
    error: function(response) {
      if (response.status === 500) {
        get_approval_by(id_employee);
      }else{
        location.reload();
      }
    }
  }); 
 }
 var today = new Date();
// var formattedDate = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate().toString().padStart(2, '0');
 var formattedToday = today.toISOString().split('T')[0];
 $('#letter_date').datepicker({
  uiLibrary: 'bootstrap4',
  minDate: formattedToday,
  format: 'yyyy-mm-dd'
});
$("#letter_date").parent().children('span').children('button').attr('disabled',true);
 
var max_days = "";
function check_max_date_cashadvance(start_date, request_by) {
	  $.ajax({
		method: "GET",
		url : "{{url('cash_advance/official_travel/official_travel/check/maxDateCashAdvance')}}",
		data : {start_date: start_date.format('YYYY-MM-DD'), request_by: request_by},
		success: function (response) {
		  if (response) {
			hide_loading();
			max_days = response[0].total_days;
			$("#with_trans").prop('checked',false).trigger('change');
			$("#with_accom").prop('checked',false).trigger('change');
			$("#with_caseadvance").prop('checked',false).trigger('change');
		//    if (job_class_group == 'gm-level' || position_routing == 'INTERNAL AUDIT SUPERVISOR' || position_routing == 'INTERNAL AUDIT COORDINATOR' || position_routing == 'INTERNAL AUDIT STAFF') {
			if (job_class_group == 'gm-level' || jQuery.inArray(position_routing, cek_pos_routing) !== -1) {
			  $("#with_caseadvance").removeClass('readonly-checkbox');
			  $("#with_trans").removeClass('readonly-checkbox');
			  $("#with_accom").removeClass('readonly-checkbox');
			}else{
			 if (max_days < maximum_official_travel_request) {
				$("#with_trans").addClass('readonly-checkbox');
				$("#with_accom").addClass('readonly-checkbox');
			} else {
				$("#with_trans").removeClass('readonly-checkbox');
				$("#with_accom").removeClass('readonly-checkbox');
			  
			}	
			if(max_days < 7){
				$("#with_caseadvance").addClass('readonly-checkbox');
			}	
			else{
				$("#with_caseadvance").removeClass('readonly-checkbox');
			}
		   
		  }
		}
	  },
	  error: function(response) {
		check_max_date_cashadvance(start_date, request_by);
	  }
	});
}

  function disableFormElements(isDisabled) {
    const formElements = [
    "#travelForm input",
    "#travelForm select",
    "#travelForm textarea",
    "#sample_table_transport input",
    "#sample_table_transport select",
    "#sample_table_akomodasi input",
    "#sample_table_akomodasi select",
    "#sample_table_cash_advance input",
    "#sample_table_cash_advance select",
    ];
    const hideElements = [
    "#new_transport",
    "#new_accommodation",
    "#new_cash_advance",
    ".delete-record-trans",
    ".delete-record-akomo",
    ".delete-record",
    ".action",
    ".action_submit"
    ];
    formElements.forEach(element => $(element).attr('disabled', isDisabled));
    hideElements.forEach(element => $(element).css('display', isDisabled ? 'none' : 'block'));
  }

  function disableFormReschedule(isDisabled) {
	 const formElements = [
		"#travelForm input",
		"#travelForm select",
		"#travelForm textarea",
    ];
	formElements.forEach(element => $(element).attr('disabled', isDisabled));
	
	const hideElements = [
		".delete-record-trans",
		".delete-record-akomo",
		".delete-record",
    ];
	hideElements.forEach(element => $(element).css('display', isDisabled ? 'none' : 'block'));
    $("#start_end").attr('disabled',false);
    $("#id_official_travel").attr('disabled',false);
    $("#date_transport").attr('disabled',false);
    $("#id_cashadvance_cash").attr('disabled',false);
    $("#with_caseadvance").attr('disabled',false).attr('onclick','return false').addClass('readonly-checkbox').css('accent-color','#89898c');
    $("#with_trans").attr('disabled',false).attr('onclick','return false').addClass('readonly-checkbox').css('accent-color','#89898c');
    $("#with_accom").attr('disabled',false).attr('onclick','return false').addClass('readonly-checkbox').css('accent-color','#89898c');
  }
  
  function disabled_all_form(status_approval, buttonType) {
    if (buttonType == 'Edit') {
      if (status_approval == 'New') {
        disableFormElements(true);
      } else if(status_approval == 'Revised') {
        disableFormElements(true);
      }else{
        disableFormElements(true);
      }
    }else if(buttonType == 'New'){
      disableFormElements(true);
    }
	else if(buttonType == 'reschedule'){
      disableFormElements(true);
    }
	else{
	  disableFormElements(true);
	}
  }
  var urlAjax = "";
  var buttonType = "";
  $(document).on('change','#with_trans',function() {
    var with_trans = document.getElementById('with_trans');
    if (with_trans.checked) {
      if (global_id_transport == 0 && buttonType == 'New') {
        $("#new_transport").click();
      }
      $("#tab_detail").find("[href='#tab-pane-2']").click();
      $("#tab-pane-2").addClass('active');
      $("#tab-transport").css('display','block');
      // $("#tab-akomodasi").css('display','block');
    }else{
      $("#tab-pane-2").removeClass('active');
      // $("#tab-pane-3").removeClass('active');
      $("#tab-transport").css('display','none');
      // $("#tab-akomodasi").css('display','none');
    }
  });
  $(document).on('change','#with_accom',function() {
    var with_accom = document.getElementById('with_accom');
    if (with_accom.checked) {
      if (global_id_akomodasi == 0 && buttonType == 'New') {
        $("#new_accommodation").click();
      }
      $("#tab_detail").find("[href='#tab-pane-3']").click();
      $("#tab-pane-3").addClass('active');
      // $("#tab-transport").css('display','block');
      $("#tab-akomodasi").css('display','block');
    }else{
      // $("#tab-pane-2").removeClass('active');
      $("#tab-pane-3").removeClass('active');
      // $("#tab-transport").css('display','none');
      $("#tab-akomodasi").css('display','none');
    }
  });
  $(".select_opsi").select2();
  $("#tab-cashadvance").css('display','none');
  $("#with_caseadvance").change(function() {
    var caseAdvance = document.getElementById('with_caseadvance');
    if (caseAdvance.checked) {
      $("#tab-cashadvance").css('display','block');
      $("#tab_detail").find("[href='#tab-pane-1']").click();
      $("#tab-pane-1").addClass('active');
    }else{
      $("#tab-cashadvance").css('display','none');
      $("#tab-pane-1").removeClass('active');
    }
  });
  function formatRupiah(amount) {
    if (!amount) {
      return '';
    }
    return amount.toLocaleString('id-ID');
  }
  function unformatRupiah(rupiah) {
    if (!rupiah) {
      return 0;
    }
    return parseInt(rupiah.replace(/,|\./g, ''));
  }
  let global_id_cash_advance = 0;
  $(document).on('click', '#new_cash_advance', function () {
    var max_min_date = $("#start_end").val();
    var content = jQuery("#sample_table_cash_advance tr"),
    size = global_id_cash_advance++,
    element = null,
    element = content.clone();
//	input_max_budget(element,size);
    element.attr('id','cash-'+size);
    element.attr('class','cash');
   
    let maxPrice = "";
    let days = 1;
    var code_product = "";
    var notes_help = "";
    var id_region = "";
    var codeJenisTransport = "";

    element.find('.id_cash_advance_input').attr('id', 'cashadvance_' + size + '_id_cash_advance');
    element.find('.id_cash_advance_input').attr('name', 'cashadvance[' + size + '][id_cash_advance]');
    element.find('.id_cash_advance_input_error').attr('id', 'cashadvance_' + size + '_id_cash_advanceError');

    element.find('.cashadvance_status').attr('id', 'cashadvance_' + size + '_status');

    element.find('.region_cashadvance_input').attr('readonly',true);
    element.find('.branch_cashadvance_input').attr('readonly',true);

    element.find('.nama_input').attr('id', 'cashadvance_' + size + '_nama');
    element.find('.nama_input').attr('name', 'cashadvance[' + size + '][nama]');
    element.find('.nama_input_error').attr('id', 'cashadvance_' + size + '_namaError');
	
    element.find('.nama_input').select2({
      placeholder: 'Select Category ....',
      data: global_product_cashadvance.map(function(item) {
        return {id: item.id, text: item.text, code_product: item.code_product, notes_help: item.long_description}
      })
    }).on('change',function(e) {
      var selectedOption = $(this).select2('data')[0];
      element.find('.region_cashadvance_input').empty();
      element.find('.branch_cashadvance_input').empty();
      element.find('.qty_cashadvance_input').val('');
      element.find('.max_budget_input').val('');
      element.find('.total_input').val('');
      if (selectedOption) {
        show_loading();
        var dec_product = selectedOption.text;
        code_product = selectedOption.code_product;
        notes_help = selectedOption.notes_help;
        element.find('.help_input').attr('data-content', notes_help);
        element.find('.div_help_input').css('display', 'block');
        element.find('.max_budget_input').val('');
        if(code_product == 'CSM0000001'){
          element.find('.qty_cashadvance_input').attr('readonly',true);
          element.find('.total_input').attr('readonly',true);
          element.find('.max_budget_input').attr('readonly',true);
        }else if (code_product == 'CSM0000002' || code_product == 'CSM0000006') {
          element.find('.max_budget_input').attr('readonly',false);
          element.find('.total_input').attr('readonly',true);
          element.find('.qty_cashadvance_input').val('1');
          element.find('.qty_cashadvance_input').attr('readonly',true);
        }else if(code_product == 'CSM0000003'){
          element.find('.max_budget_input').attr('readonly',true);
          element.find('.qty_cashadvance_input').attr('readonly',true);
          element.find('.total_input').attr('readonly',true);
        }else if(code_product == 'CSM0000004'){
          element.find('.max_budget_input').attr('readonly',true);
          element.find('.qty_cashadvance_input').attr('readonly',true);
          element.find('.total_input').attr('readonly',true);
        }else if(code_product == 'CSM0000005'){
          element.find('.qty_cashadvance_input').attr('readonly',true);
          element.find('.max_budget_input').attr('readonly',true);
          element.find('.total_input').attr('readonly',true);
        }else if(code_product == 'CSM0000001'){
          element.find('.qty_cashadvance_input').attr('readonly',true);
        }else{
          element.find('.qty_cashadvance_input').attr('readonly',true);
          element.find('.max_budget_input').attr('readonly',true);
          element.find('.total_input').attr('readonly',false);
        }
        if (code_product == 'CSM0000002' || code_product == 'CSM0000006' || code_product == 'CSM0000005') {
          element.find('.region_cashadvance_input').attr('readonly',true);
          element.find('.branch_cashadvance_input').attr('readonly',true);
        }else{
          element.find('.region_cashadvance_input').attr('readonly',false);
          element.find('.branch_cashadvance_input').attr('readonly',false);
        }
        $.ajax({
          method: "GET",
          url: "{{route('get_region_destination')}}",
          data : {code_product: code_product},
          success:function(response)
          {
            hide_loading();
            element.find('.region_cashadvance_input').select2({
              // placeholder: 'Region Destination ....',
              data : response.map(function(item) {
                return {
                  id: item.text,
                  text: item.text,
                  maxPriceAttr: item.max_price,
                  codeRegion: item.code_region,
                  idRegion: item.id
                };
              })
            }).on('change',function(e) {
             var selectedOption = $(this).select2('data')[0];
             if (selectedOption) {
              var code_region = selectedOption.codeRegion;
              id_region = selectedOption.idRegion;
			  
              if (code_product == 'CSM0000005') {
				  const dateHeaderCashAdvance = element.find('.tanggal_input').val();
				  const dateHeader = dateHeaderCashAdvance.split(" to ");
				  const startDateCashAdvance = dateHeader[0];
				  const endDateCashAdvance = dateHeader[1];
				  var timeDiff = new Date(endDateCashAdvance) - new Date(startDateCashAdvance);
				 
				days = Math.floor(timeDiff / (1000 * 3600 * 24)); 
                maxPrice = selectedOption.maxPriceAttr;
                var totalMaxBudget = unformatRupiah(maxPrice);
                var a = [];
                for (var val = 1; val <= days; val++) {
                  var result = val % 7;
                  if (result === 0) {
                    a.push(1);
                  }
                }
                if (a.length > 0) {
                  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget*a.length));
                }else{
                  element.find('.max_budget_input').val('0');
                }
                element.find('.total_input').val(formatRupiah(totalMaxBudget*a.length));
                element.find('.qty_cashadvance_input').val('1');
				element.find('.region_cashadvance_input').empty();
              }
			//  element.find('.region_cashadvance_input').empty();
              element.find('.branch_cashadvance_input').empty();
           //   element.find('.max_budget_input').val('');
              show_loading();
              if (code_product != 'CSM0000005') {
                $.ajax({
                  method: "GET",
                  url: "{{route('change_region_cashadvance')}}",
                  data : {id_region: id_region, id_official_travel: officialtravelID, code_product: code_product},
                  success:function(response)
                  {
                    hide_loading();
                    element.find('.branch_cashadvance_input').select2({
                      // placeholder: 'Branch Destination ....',
                      data : response.map(function(item) {
                        return {
                          id: item.text,
                          text: item.text,
                          maxPriceAttr: item.max_price
                        };
                      })
                    }).on('change',function(e) {
                      var selectedOption = $(this).select2('data')[0];
                      if (selectedOption) {
                        maxPrice = selectedOption.maxPriceAttr;
                        const dateHeaderCashAdvance = element.find('.tanggal_input').val();
                        if (dateHeaderCashAdvance != '') {
                          const dateHeader = dateHeaderCashAdvance.split(" to ");
                          const startDateCashAdvance = dateHeader[0];
                          const endDateCashAdvance = dateHeader[1];
                          var timeDiff = new Date(endDateCashAdvance) - new Date(startDateCashAdvance);
                          if (code_product == 'CSM0000001' || code_product == 'CSM0000004' || code_product == 'CSM0000002') {
                            days = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;
                          }else{
                            days = Math.floor(timeDiff / (1000 * 3600 * 24));
                          }
                          var totalMaxBudget = unformatRupiah(maxPrice);
                          if (code_product == 'CSM0000001') {
                            element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
                            element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
                            element.find('.qty_cashadvance_input').val(days);
                          }else if(code_product == 'CSM0000002' || code_product == 'CSM0000006'){
                            element.find('.qty_cashadvance_input').val('1');
                          }else if(code_product == 'CSM0000003'){
                            element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
                            element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
                            element.find('.qty_cashadvance_input').val(days);
                          }else if(code_product == 'CSM0000004'){
							 var a = [1];
                            for (var val = 1; val <= days; val++) {
                              var result = val % 31;
                              if (result === 0) {
                                a.push(1);
                              }
                            }
                            if (a.length > 0) {
                              element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
                            }
                            element.find('.total_input').val(formatRupiah(totalMaxBudget*a.length));
                            element.find('.qty_cashadvance_input').val(a.length);
                         /*  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
                            element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
                            element.find('.qty_cashadvance_input').val(days);
						*/
                          }else if(code_product == 'CSM0000005'){
                            var a = [];
                            for (var val = 1; val <= days; val++) {
                              var result = val % 7;
                              if (result === 0) {
                                a.push(1);
                              }
                            }
                            if (a.length > 0) {
                              element.find('.max_budget_input').val(formatRupiah(totalMaxBudget*a.length));
                            }else{
                              element.find('.max_budget_input').val('0');
                            }
                            element.find('.total_input').val(formatRupiah(totalMaxBudget*a.length));
                            element.find('.qty_cashadvance_input').val('1');
                         //   element.find('.qty_cashadvance_input').val(days);
                          }
                        }else{
                          element.find('.max_budget_input').val('');
                          days = 1;
                        }
                      }else{
                        maxPrice = "";
                      }
                    }).trigger('change');
                  },
                  error: function(response) {
                    hide_loading();
                    element.find('.region_cashadvance_input').val(null).trigger('change');
                  }
                });
		}
			else{
			 element.find('.branch_cashadvance_input').select2();
			 hide_loading();
			}
		}
			else{
			  maxPrice = "";
			}
		}).trigger('change');
	},
		error: function(response) {
		  hide_loading();
		  element.find('.nama_input').val(null).trigger('change');
		}
	});
	}
		else{
		  element.find('.branch_cashadvance_input').empty();
		  maxPrice = 0;
		  days = 0;
		  element.find('.max_budget_input').val('');
		  element.find('.max_budget_input').attr('readonly',true);
		}
	}).val(null).trigger('change');

	element.find('.nama_input').val(null).trigger('change');

	element.find('.notes_cashadvance_input').attr('id', 'cashadvance_' + size + '_notes_cashadvance');
	element.find('.notes_cashadvance_input').attr('name', 'cashadvance[' + size + '][notes_cashadvance]');
	element.find('.notes_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_notes_cashadvanceError');

	element.find('.tanggal_input').attr('id', 'cashadvance_' + size + '_tanggal');
	element.find('.tanggal_input').attr('name', 'cashadvance[' + size + '][tanggal]');
	element.find('.tanggal_input_error').attr('id', 'cashadvance_' + size + '_tanggalError');
	
	if (max_min_date != null) {
	  const dateHeader = max_min_date.split(" to ");
	  const startDateHeader = dateHeader[0];
	  const endDateHeader = dateHeader[1];
	  element.find('.tanggal_input').daterangepicker({
		drops: 'up',
		autoUpdateInput: false,
		minDate: startDateHeader,
		maxDate: endDateHeader,
		locale: {
		  cancelLabel: 'Reset',
		  format: 'YYYY-MM-DD',
		  separator: ' to '
		}
	  }).on('apply.daterangepicker', function(ev, picker) {
		var startDate = picker.startDate;
		var endDate = picker.endDate;
		var timeDiff = endDate - startDate;
		if (code_product == 'CSM0000001' || code_product == 'CSM0000004' || code_product == 'CSM0000002') {
		  days = Math.floor(timeDiff / (1000 * 3600 * 24)) + 1;
		}else{
		  days = Math.floor(timeDiff / (1000 * 3600 * 24));
		}
		var totalMaxBudget = unformatRupiah(maxPrice);
		if (code_product == 'CSM0000001') {
		  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
		  element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
		  element.find('.qty_cashadvance_input').val(days);
		}else if(code_product == 'CSM0000002' || code_product == 'CSM0000006'){
		  element.find('.qty_cashadvance_input').val('1');
		}else if(code_product == 'CSM0000003'){
		  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
		  element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
		  element.find('.qty_cashadvance_input').val(days);
		}else if(code_product == 'CSM0000004'){
			var a = [1];
			for (var val = 1; val <= days; val++) {
			  var result = val % 31;
			  if (result === 0) {
				a.push(1);
			  }
			}
			if (a.length > 0) {
			  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
			}
			element.find('.total_input').val(formatRupiah(totalMaxBudget*a.length));
			element.find('.qty_cashadvance_input').val(a.length);
		/*	
		  element.find('.max_budget_input').val(formatRupiah(totalMaxBudget));
		  element.find('.total_input').val(formatRupiah(totalMaxBudget*days));
		  element.find('.qty_cashadvance_input').val(days);
		*/
		}else if(code_product == 'CSM0000005'){
		  var a = [];
		  for (var val = 1; val <= days; val++) {
			var result = val % 7;
			if (result === 0) {
			  a.push(1);
			}
		  }
		  if (a.length > 0) {
			element.find('.max_budget_input').val(formatRupiah(totalMaxBudget*a.length));
		  }else{
			element.find('.max_budget_input').val('0');
		  }
		  element.find('.total_input').val(formatRupiah(totalMaxBudget*a.length));
		  element.find('.qty_cashadvance_input').val('1');
		//  element.find('.qty_cashadvance_input').val(days);
		}
		$(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
	  }).on('cancel.daterangepicker', function() {
		days = 1;
		element.find('.max_budget_input').val('');
		element.find('.qty_cashadvance_input').val('');
		element.find('.total_input').val('');
		$(this).val('');
	  }).on('keydown.daterangepicker',function(e) {
		e.preventDefault();
	  }).on('cut.daterangepicker',function(e) {
		e.preventDefault();
	  });
	}
	
	element.find('.region_cashadvance_input').attr('id', 'cashadvance_' + size + '_region_cashadvance');
	element.find('.region_cashadvance_input').attr('name', 'cashadvance[' + size + '][region_cashadvance]');
	element.find('.region_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_region_cashadvanceError');
	element.find('.region_cashadvance_input').val(null).trigger('change');
	element.find('.region_cashadvance_input').select2(
	  );

	element.find('.branch_cashadvance_input').attr('id', 'cashadvance_' + size + '_branch_cashadvance');
	element.find('.branch_cashadvance_input').attr('name', 'cashadvance[' + size + '][branch_cashadvance]');
	element.find('.branch_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_branch_cashadvanceError');
	element.find('.branch_cashadvance_input').select2(
	  );
	element.find('.branch_cashadvance_input').attr('readonly',true);
	element.find('.branch_cashadvance_input').val(null).trigger('change');

	element.find('.qty_cashadvance_input').attr('id', 'cashadvance_' + size + '_qty_cashadvance');
	element.find('.qty_cashadvance_input').attr('name', 'cashadvance[' + size + '][qty_cashadvance]');
	element.find('.qty_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_qty_cashadvanceError');

	element.find('.total_input').attr('id', 'cashadvance_' + size + '_total');
	element.find('.total_input').attr('name', 'cashadvance[' + size + '][total]');
	element.find('.total_input_error').attr('id', 'cashadvance_' + size + '_totalError');
	element.find('.total_input').on('input', function() {
	  var rupiah = this.value;
	  var numberTotal = unformatRupiah(rupiah);
	  this.value = formatRupiah(numberTotal);
	});

	element.find('.help_input').attr('id', 'cashadvance_' + size + '_help');
	element.find('.help_input').popover({
	  trigger: 'focus'
	});

	element.find('.max_budget_input').attr('id', 'cashadvance_' + size + '_max_budget');
	element.find('.max_budget_input').attr('size', size);
	element.find('.max_budget_input').attr('name', 'cashadvance[' + size + '][max_budget]');
	element.find('.max_budget_input_error').attr('id', 'cashadvance_' + size + '_max_budgetError');
	element.find('.max_budget_input').attr('readonly',true);
	element.find('.max_budget_input').on('input', function() {
	  var rupiah = this.value;
	  var numberTotal = unformatRupiah(rupiah);
	  this.value = formatRupiah(numberTotal);
	});
	
	element.find('.check_cashadvance_input').attr('id', 'cashadvance_' + size + '_check_cashadvance');
    element.find('.check_cashadvance_input').attr('name', 'cashadvance[' + size + '][check_cashadvance]');
	
	element.find('.re_disabled_input').attr('id', 'cashadvance_' + size + '_re_disabled');
    element.find('.re_disabled_input').attr('name', 'cashadvance[' + size + '][re_disabled]');

	element.appendTo('#table_cash_advance');
	$('#table_cash_advance tr').each(function (index) {
	  $(this).find('span.sn').html(index + 1);
	});
});

  // transport
  let global_id_transport = 0;
  let global_transport_name = [];
  $(document).on('click', '#new_transport', function () {
    var max_min_date = $("#start_end").val();
    var content = jQuery("#sample_table_transport tr"),
    size = global_id_transport++,
    element = null,
    element = content.clone();
    element.attr('id','trans-'+size);
    element.attr('class','trans');
  
    element.find('.id_transport_input').attr('id', 'transport_' + size + '_id_transport');
    element.find('.id_transport_input').attr('name', 'transport[' + size + '][id_transport]');
    element.find('.id_transport_input_error').attr('id', 'transport_' + size + '_id_transportError');

    element.find('.jenis_transportasi_input').attr('id', 'transport_' + size + '_jenis_transportasi');
    element.find('.jenis_transportasi_input').attr('name', 'transport[' + size + '][jenis_transportasi]');
    element.find('.jenis_transportasi_input_error').attr('id', 'transport_' + size + '_jenis_transportasiError');
    element.find('.jenis_transportasi_input').select2({
      placeholder: 'Select Transport Type ....',
      data: global_product_transport.map(function(item) {
        return {id: item.id, text: item.text, code: item.code}
      })
    }).on('change',function(e) {
      element.find('.transport_name_input').empty();
      var selectedOption = $(this).select2('data')[0];
      if (selectedOption) {
        codeJenisTransport = selectedOption.code;
        if (codeJenisTransport) {
          show_loading();
          $.ajax({
            method: "GET",
            url: "{{route('change_type_transport')}}",
            data : {code: codeJenisTransport},
            success:function(response)
            {
              hide_loading();
              element.find('.transport_name_input').select2({
                placeholder: "Select Transport Name ...",
                data: response.map(function(item) {
                  return {id: item.variant, text: item.variant}
                })
              });
            },
            error: function(response) {
              hide_loading();
              element.find('.jenis_transportasi_input').val(null).trigger('change');
            }
          });
        }
      }else{
        element.find('.transport_name_input').empty();
      }
    }).val(null).trigger('change');
    element.find('.jenis_transportasi_input').val(null).trigger('change');

    element.find('.transport_name_input').attr('id', 'transport_' + size + '_transport_name');
    element.find('.transport_name_input').attr('name', 'transport[' + size + '][transport_name]');
    element.find('.transport_name_input_error').attr('id', 'transport_' + size + '_transport_nameError');
    element.find('.transport_name_input').select2({
      placeholder: "Select Transport Name ..."
    });
    element.find('.transport_name_input').val(null).trigger('change');

    element.find('.from_input').attr('id', 'transport_' + size + '_from');
    element.find('.from_input').attr('name', 'transport[' + size + '][from]');
    element.find('.from_input_error').attr('id', 'transport_' + size + '_fromError');
    
    element.find('.to_input').attr('id', 'transport_' + size + '_to');
    element.find('.to_input').attr('name', 'transport[' + size + '][to]');
    element.find('.to_input_error').attr('id', 'transport_' + size + '_toError');
    
    element.find('.branch_input').attr('id', 'transport_' + size + '_branch');
    element.find('.branch_input').attr('name', 'transport[' + size + '][branch]');
    element.find('.branch_input_error').attr('id', 'transport_' + size + '_branchError');
    element.find('.branch_input').select2({
      // allowClear: true,
      placeholder: 'Select Branch ....',
      data: global_branch_transport
    }).on('change',function(e) {
    }).trigger('change');
    element.find('.branch_input').val(null).trigger('change');

    element.find('.date_transport_input').attr('id', 'transport_' + size + '_date_transport');
    element.find('.date_transport_input').attr('name', 'transport[' + size + '][date_transport]');
    element.find('.date_transport_input_error').attr('id', 'transport_' + size + '_date_transportError');
    if (max_min_date != null) {
      const dateHeader = max_min_date.split(" to ");
      const startDateHeader = dateHeader[0];
      const endDateHeader = dateHeader[1];
      var startDateHeaderFormat = new Date(startDateHeader);
      var endDateHeaderFormat = new Date(endDateHeader);
      element.find('.date_transport_input').daterangepicker({
        drops: 'up',
        singleDatePicker: true,
        autoUpdateInput: false,
        autoApply: true,
        minDate: startDateHeader,
        maxDate: endDateHeader,
        locale: {
          format: 'YYYY-MM-DD'
        }
      }).on('apply.daterangepicker', function(ev, picker) {
        var startDate = picker.startDate;
        var endDate = picker.endDate;
        $(this).val(startDate.format('YYYY-MM-DD'));
      }).on('cancel.daterangepicker', function() {
        $(this).val('');
      }).on('keydown.daterangepicker',function(e) {
        e.preventDefault();
      });
    }
    element.find('.time_transport_input').attr('id', 'transport_' + size + '_time_transport');
    element.find('.time_transport_input').attr('name', 'transport[' + size + '][time_transport]');
    element.find('.time_transport_input_error').attr('id', 'transport_' + size + '_time_transportError');
    element.find('.time_transport_input').timepicker({
      uiLibrary: 'bootstrap4',
      format: "HH:MM",
      mode: '24hr'
    });
	
	element.find('.check_trans_input').attr('id', 'transport_' + size + '_check_trans');
    element.find('.check_trans_input').attr('name', 'transport[' + size + '][check_trans]');
	
	element.find('.trans_disabled_input').attr('id', 'transport_' + size + '_trans_disabled');
    element.find('.trans_disabled_input').attr('name', 'transport[' + size + '][trans_disabled]');

    element.appendTo('#table_transport');
    $('#table_transport tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
  });

    // Akomodasi
    let global_id_akomodasi = 0;
    $(document).on('click', '#new_accommodation', function () {
      var max_min_date = $("#start_end").val();
      var content = jQuery("#sample_table_akomodasi tr"),
      size = global_id_akomodasi++,
      element = null,
      element = content.clone();
      element.attr('id','akomo-'+size);
      element.attr('class','akomo');

      element.find('.id_akomodasi_input').attr('id', 'akomodasi_' + size + '_id_akomodasi');
      element.find('.id_akomodasi_input').attr('name', 'akomodasi[' + size + '][id_akomodasi]');
      element.find('.id_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_id_akomodasiError');

      element.find('.product_akomodasi_input').attr('id', 'akomodasi_' + size + '_product_akomodasi');
      element.find('.product_akomodasi_input').attr('name', 'akomodasi[' + size + '][product_akomodasi]');
      element.find('.product_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_product_akomodasiError');
      element.find('.product_akomodasi_input').select2({
        placeholder: 'Select Product ....',
        data: global_product_akomodasi.map(function(item) {
          return {id: item.id, text: item.text}
        })
      }).on('change',function(e) {
        // var selectedOption = $(this).select2('data')[0];
        // if (selectedOption) {
        //   var idUom = selectedOption.id_uom;
        //   var decUom = selectedOption.dec_uom;
        //   // element.find('.uom_akomodasi_input').empty();
        //   // if (idUom) {
        //   //   // element.find('.uom_akomodasi_input').append('<option value="'+idUom+'">'+decUom+'</option>');
        //   // }else{
        //   //   // element.find('.uom_akomodasi_input').empty();
        //   // }
        // }
      }).val(null).trigger('change');
      element.find('.product_akomodasi_input').val(null).trigger('change');

      element.find('.nama_hotel_input').attr('id', 'akomodasi_' + size + '_nama_hotel');
      element.find('.nama_hotel_input').attr('name', 'akomodasi[' + size + '][nama_hotel]');
      element.find('.nama_hotel_input_error').attr('id', 'akomodasi_' + size + '_nama_hotelError');
	  
	element.find('.city_input').attr('id', 'akomodasi_' + size + '_city');
	element.find('.city_input').attr('name', 'akomodasi[' + size + '][city]');
	element.find('.city_input_error').attr('id', 'akomodasi_' + size + '_cityError');
	
	element.find('.ako_branch_input').attr('id', 'akomodasi_' + size + '_branch');
	element.find('.ako_branch_input').attr('name', 'akomodasi[' + size + '][branch]');
	element.find('.ako_branch_input_error').attr('id', 'akomodasi_' + size + '_branchError');
	element.find('.ako_branch_input').select2({
	  // allowClear: true,
	  placeholder: 'Select Branch ....',
	  data: global_branch_transport
	}).on('change',function(e) {
	}).trigger('change');
	element.find('.ako_branch_input').val(null).trigger('change');

      element.find('.start_end_akomodasi_input').attr('id', 'akomodasi_' + size + '_start_end_akomodasi');
      element.find('.start_end_akomodasi_input').attr('name', 'akomodasi[' + size + '][start_end_akomodasi]');
      element.find('.start_end_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_start_end_akomodasiError');
      if (max_min_date != null) {
        const dateHeader = max_min_date.split(" to ");
        const startDateHeader = dateHeader[0];
        const endDateHeader = dateHeader[1];
        element.find('.start_end_akomodasi_input').daterangepicker({
          drops: 'up',
          autoUpdateInput: false,
          minDate: startDateHeader,
          maxDate: endDateHeader,
          locale: {
            cancelLabel: 'Reset',
            format: 'YYYY-MM-DD',
            separator: ' to '
          }
        }).on('apply.daterangepicker', function(ev, picker) {
          var startDate = picker.startDate;
          var endDate = picker.endDate;
          var timeDiff = endDate - startDate;
          var daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));
          element.find('.lama_menginap_input').val(daysDiff+' Night(s)');
          $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function() {
          $(this).val('');
          element.find('.lama_menginap_input').val('');
        }).on('keydown.daterangepicker',function(e) {
          e.preventDefault();
        });
      }

      element.find('.lama_menginap_input').attr('id', 'akomodasi_' + size + '_lama_menginap');
      element.find('.lama_menginap_input').attr('name', 'akomodasi[' + size + '][lama_menginap]');
      element.find('.lama_menginap_input_error').attr('id', 'akomodasi_' + size + '_lama_menginapError');
      element.find('.lama_menginap_input').attr('readonly', true);
	  
	  element.find('.check_akomodasi_input').attr('id', 'akomodasi_' + size + '_check_akomodasi');
	  element.find('.check_akomodasi_input').attr('name', 'akomodasi[' + size + '][check_akomodasi]');
	
	  element.find('.akomodasi_disabled_input').attr('id', 'akomodasi_' + size + '_akomodasi_disabled');
	  element.find('.akomodasi_disabled_input').attr('name', 'akomodasi[' + size + '][akomodasi_disabled]');

      // element.find('.uom_akomodasi_input').attr('id', 'akomodasi_' + size + '_uom_akomodasi');
      // element.find('.uom_akomodasi_input').attr('name', 'akomodasi[' + size + '][uom_akomodasi]');
      // element.find('.uom_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_uom_akomodasiError');
      // element.find('.uom_akomodasi_input').select2();

      element.appendTo('#table_accommodation');
      $('#table_accommodation tr').each(function (index) {
        $(this).find('span.sn').html(index + 1);
      });
    });

document.addEventListener('keydown', function (e) {
  if (e.key === 'Enter') {
    e.preventDefault();
    var formToSubmit = null;
    var focusedButton = $("button:focus");
    if (focusedButton.hasClass('action')) {
      formToSubmit = document.getElementById('submitForm');
    } else {
      formToSubmit = document.getElementById('saveForm');
    }
  }
});
$(function () {
DataTableBgen(start_end);

  $('#travelForm').submit(function (e) {
    e.preventDefault();
	let thisButtonId = e.originalEvent.submitter.id;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#travelForm input").removeClass("is-invalid");
    $("#travelForm textarea").removeClass("is-invalid");
    $("#travelForm select").removeClass("is-invalid");
    $(".error-tab").html("");
    var clickedButton = $("button:focus");
    var buttonValue = clickedButton.val();
    if (buttonValue == 'draft') {
      document.querySelector(".action").disabled=true;
    }else{
      document.querySelector(".action_submit").disabled=true;
    }
    formData.push({ name: 'clickedButton', value: buttonValue },{name: 'buttonType', value: global_reschedule});
    $.ajax({
      method: "POST",
      headers: {
		Accept: "application/json",
		'X-CSRF-TOKEN': "{{ csrf_token() }}",
      },
      url: urlAjax,
      data: formData,
      success: function (response) {
        document.querySelector(".action_submit").disabled=false;
    //    document.querySelector(".action").disabled=false;
        if (response.status == 'true') {
			if(thisButtonId == 'saveForm'){
				var formDataSubmit = response.data;
				$.ajax({
					type: 'POST',
					headers: {
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
					},
					url: "{{ route('mail.mail_success') }}",
					data: {source: formDataSubmit},
				});
				$("#modal_form_travel").modal('hide');
				swal({
					title: "Data Submited!",
					icon: "success",
					buttons: {
						confirm: {
							className: 'btn-success'
						},
					},
				}).then(ok => { $('#officialtravel_table').DataTable().ajax.reload(); });
                                   
			}
			else{
			  swal({
				icon: 'success',
				title: 'Success',
				text: response.message
			  });
			   $("#modal_form_travel").modal('hide');
			   $('#officialtravel_table').DataTable().ajax.reload();
			}         
        }else if(response.status == 'null'){
          swal({
            icon: 'warning',
            type: 'warning',
            title: response.title,
            // dangerMode: true,
            text: response.message
          });
        }else{
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! '+response.message
          });
        }
      },
      error: function (response) {
        document.querySelector(".action_submit").disabled=false;
    //    document.querySelector(".action").disabled=false;
        if (response.status === 422) {
          let errors = response.responseJSON.errors;
          Object.keys(errors).forEach(function (key) {
           var key_temp = key.replaceAll(".", "_");
           $("#" + key_temp).addClass("is-invalid");
           $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
           var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
           if (tab_id != undefined) {
            $("#tab_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
          }
        });
        }else{
         swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! ['+response.message+']'
        });
       }
     }
   });
  });
});

const get_region_cash = async (index,region_cashadvance) => {
	let result;	
    try {
        result = await 
		$('#cashadvance_'+index+'_region_cashadvance').val(region_cashadvance).trigger('change');
        return result;
    } catch (error) {
    }	
}

function get_edit(officialtravelID,buttonType) {
  $.ajax({
    type: "GET",
    url: "{{url('cash_advance/official_travel/official_travel/getEdit')}}"+"/"+officialtravelID,
    data: { mode: "summary" },
    // data : {id_official_travel: officialtravelID},
    success: function(response) {
      hide_loading();
      if (response.data) {
        global_id_cash_advance = 0;
        global_id_transport = 0;
        global_id_akomodasi = 0;
        $.each(response.data, function(key, value) {
    //      buttonType = 'Edit';
          disabled_all_form(value.status_approval, buttonType);
    //      $("#start_end").attr('disabled',true);
          $("#letter_date").attr('disabled',true);
          $("#id_official_travel").val(value.id_official_travel);
          $("#letter_date").val(value.letter_date);
          $("#id_position_detail").val(value.id_position_detail);
          $("#id_reason_group").val(value.id_reason_group).trigger('change');
          $("#id_approval_status").val(value.id_approval_status).trigger('change');
          $("#location_from").val(value.location_from);
          $("#location_to").val(value.location_to);
          $("#reason_notes").val(value.reason_notes);
          $("#id_approval_request").select2({
            data: [
              {id: value.id_approval_request, text: value.name_approval_request}
            ]
          });
          var start_date = value.start_date.substr(0,10);
          var end_date = value.end_date.substr(0,10);
    //      var defaultStartDate = moment(value.start_date);
    //      var defaultEndDate = moment(value.end_date);
    //      $("#start_end").data('daterangepicker').setStartDate(defaultStartDate);
    //      $("#start_end").data('daterangepicker').setEndDate(defaultEndDate);
          $("#start_end").val(start_date+' to '+end_date);
          var startDateHeader = new Date(start_date);
          if (!isNaN(startDateHeader.getTime())) {
            var currentDate = new Date();
            var timeDiff = currentDate - startDateHeader;
            var daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));
          }
          if (value.unlock_gps == true) {
            $("#unlock_gps").prop('checked',true).trigger('change');
          }else{
            $("#unlock_gps").prop('checked',false).trigger('change');
          }
		/*
		  $("#with_caseadvance").addClass('readonly-checkbox');
          $("#with_trans").addClass('readonly-checkbox');
          $("#with_accom").addClass('readonly-checkbox');
		 */
          if (value.is_have_cash_advance == false) {
            $("#with_caseadvance").prop('checked',false).trigger('change');
          }else{
            $("#with_caseadvance").prop('checked',true).trigger('change');
          }
		  
		  $("#id_cashadvance_cash").val(value.id_cash_advance).trigger('change');
        });
        // Cashadvance
        if (response.cashadvance) {
          $.each(response.cashadvance, function (i, item) {
        //    $("#id_cashadvance_cash").val(response.cashadvance[0].id_cash_advance);
            $('#new_cash_advance').trigger('click');
          });
          setTimeout(function () {
            $('#table_cash_advance tr').each(function (index) {
              var notes = response.cashadvance[index].notes.split(';');
              var notes_cashadvance = notes[0];
              var region_cashadvance = notes[1];
              var branch_cashadvance = notes[2];
              var tanggal_cashadvance = response.cashadvance[index].description.replace(/;/g, '');
              const dateRangeCashadvance = tanggal_cashadvance.split(' to ');
              const startDate = new Date(dateRangeCashadvance[0]);
              const endDate = new Date(dateRangeCashadvance[1]);
              var defaultStartDate = moment(dateRangeCashadvance[0]);
              var defaultEndDate = moment(dateRangeCashadvance[1]);
              $(this).find('.tanggal_input').data('daterangepicker').setStartDate(defaultStartDate);
              $(this).find('.tanggal_input').data('daterangepicker').setEndDate(defaultEndDate);
              $(this).find('span.sn').html(index + 1);
              $(this).find('.id_cash_advance_input').val(response.cashadvance[index].id_expense_request).attr('disabled',false);             
              $(this).find('.nama_input').val(response.cashadvance[index].id_product).trigger('change');
              $(this).find('.notes_cashadvance_input').val(notes_cashadvance);
              $(this).find('.tanggal_input').val(tanggal_cashadvance);
              $(this).find('.qty_cashadvance_input').val(response.cashadvance[index].qty);
              if(response.cashadvance[index].expense_status == "I" || response.cashadvance[index].inventory_product_status == "I") {
                $(this).find('.cashadvance_status').html("<span class='badge badge-danger'>Inactive</span>");
              } else {
                $(this).find('.cashadvance_status').html("<span class='badge badge-success'>Active</span>");
              }
			  setTimeout(function() {
				get_region_cash(index,region_cashadvance).then(function() {
					setTimeout(function() {
						$('#cashadvance_'+index+'_branch_cashadvance').val(branch_cashadvance).trigger('change');
					}, 4000);
				});			
			  }, 2000);
            /*  setTimeout(function() {
                $('#cashadvance_'+index+'_region_cashadvance').val(region_cashadvance).trigger('change');
              }, 3000);
			
              setTimeout(function() {
                $('#cashadvance_'+index+'_branch_cashadvance').val(branch_cashadvance).trigger('change');
              }, 5000);
			*/
              if (response.cashadvance[index].unit_price != null) {
                var budget_cashadvance = unformatRupiah(response.cashadvance[index].unit_price);
                $(this).find('.max_budget_input').val(formatRupiah(budget_cashadvance));
              }
              if (response.cashadvance[index].total_amount != null) {
                var total_cashadvance = unformatRupiah(response.cashadvance[index].total_amount);
                $(this).find('.total_input').val(formatRupiah(total_cashadvance));
              }
			  
			  if(buttonType == 'reschedule'){
				$(this).find('.nama_input').attr('disabled',false).attr('readonly',true);
				$(this).find('.tanggal_input').attr('disabled',false).attr('readonly',true).css('pointer-events','none');
				$(this).find('.re_disabled_input').attr('disabled',false).val('re_disabled').trigger('change');
				$(this).find('.dis_cashadvance').css('display','inline');
				$(this).find('.check_cashadvance_input').attr('disabled',false).prop('checked',true);
			  }
            });                       
          }, 1000);
        }
        // Transport
        if (response.transport) {
          $.each(response.transport, function (i, item_trans) {
            $("#with_trans").attr('value','true');
            $("#with_trans").prop('checked',true).trigger('change');
         //   $("#id_cashadvance_trans").val(response.transport[0].id_cash_advance);
            $('#new_transport').trigger('click');
          });
          setTimeout(function () {
            $('#table_transport tr').each(function (index) {
              var notes = response.transport[index].notes;
              var notes_trans = notes.split(';');
              var jenis_transportasi = notes_trans[0];
              var from = notes_trans[1];
              var to = notes_trans[2];
          //    var branch = notes_trans[3];
              // 
              var description = response.transport[index].description;
              var description_trans = description.split(';');
              var date_transport = description_trans[0];
              var time_transport = description_trans[1];
              $(this).find('span.sn').html(index + 1);
              $(this).find('.id_transport_input').val(response.transport[index].id_expense_request).attr('disabled',false);
              $(this).find('.jenis_transportasi_input').val(response.transport[index].id_product).trigger('change');
              $(this).find('.from_input').val(from);
              $(this).find('.to_input').val(to);
              $(this).find('.branch_input').val(response.transport[index].branch).trigger('change');
              $(this).find('.date_transport_input').val(date_transport);
              $(this).find('.time_transport_input').val(time_transport);
			  $(this).find('.time_transport_input').parent().children('span').children('button').attr('disabled', true);
			  $(this).find('.time_transport_input').parent().children('span').children('button').css('background', '#e9ecef');
              if(response.transport[index].expense_status == "I" || response.transport[index].inventory_product_status == "I") {
                $(this).find('.transport_status').html("<span class='badge badge-danger'>Inactive</span>");
              } else {
                $(this).find('.transport_status').html("<span class='badge badge-success'>Active</span>");
              }
              setTimeout(function() {
                $('#transport_'+index+'_transport_name').val(jenis_transportasi).trigger('change');
              }, 3000);
			  
			  if(buttonType == 'reschedule'){
				$(this).find('.jenis_transportasi_input').attr('disabled',false).attr('readonly',true);
				$(this).find('.date_transport_input').attr('disabled',false).attr('readonly',true).css('pointer-events','none');
				$(this).find('.trans_disabled_input').attr('disabled',false).val('re_disabled').trigger('change');
				$(this).find('.dis_transport').css('display','inline');
				$(this).find('.check_trans_input').attr('disabled',false).prop('checked',true);
			  }
            }); 
          }, 500);
        }
          // akomodasi
         if (response.akomodasi) {
           $.each(response.akomodasi, function (i, item_accom) {
             $("#with_accom").attr('value','true');
             $("#with_accom").prop('checked',true).trigger('change');
         //  $("#id_cashadvance_accom").val(response.akomodasi[0].id_cash_advance);
             $('#new_accommodation').trigger('click');
           });
           setTimeout(function () {
            $('#table_accommodation tr').each(function (index) {
				  var notes = response.akomodasi[index].notes;
				  var notes_ako = notes.split(';');
				  var notes_akomodasi = notes_ako[0];
				  var city = notes_ako[1];
				  
				  var description_akomodasi = response.akomodasi[index].description.replace(/;/g, '');
				  $(this).find('span.sn').html(index + 1);
				  $(this).find('.id_akomodasi_input').val(response.akomodasi[index].id_expense_request).attr('disabled',false);
				  $(this).find('.product_akomodasi_input').val(response.akomodasi[index].id_product).trigger('change');
				  $(this).find('.nama_hotel_input').val(notes_akomodasi);
				  $(this).find('.start_end_akomodasi_input').val(description_akomodasi);
				  $(this).find('.city_input').val(city);
				  $(this).find('.ako_branch_input').val(response.akomodasi[index].branch).trigger('change');
          if(response.akomodasi[index].expense_status == "I" || response.akomodasi[index].inventory_product_status == "I") {
            $(this).find('.akomodasi_status').html("<span class='badge badge-danger'>Inactive</span>");
          } else {
            $(this).find('.akomodasi_status').html("<span class='badge badge-success'>Active</span>");
          }
				  const dateRangeAkomodasi = description_akomodasi.split(' to ');
				  const startDate = new Date(dateRangeAkomodasi[0]);
				  const endDate = new Date(dateRangeAkomodasi[1]);
				  var defaultStartDate = moment(dateRangeAkomodasi[0]);
				  var defaultEndDate = moment(dateRangeAkomodasi[1]);
				  $(this).find('.start_end_akomodasi_input').data('daterangepicker').setStartDate(defaultStartDate);
				  $(this).find('.start_end_akomodasi_input').data('daterangepicker').setEndDate(defaultEndDate);
				  const timeDifference = endDate - startDate;
				  const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
				  $(this).find('.lama_menginap_input').val(daysDifference+' Night(s)');		
				
				if(buttonType == 'reschedule'){
					$(this).find('.product_akomodasi_input').attr('disabled',false).attr('readonly',true);
					$(this).find('.start_end_akomodasi_input').attr('disabled',false).attr('readonly',true).css('pointer-events','none');
					$(this).find('.akomodasi_disabled_input').attr('disabled',false).val('re_disabled').trigger('change');
					$(this).find('.dis_akomodasi').css('display','inline');
					$(this).find('.check_akomodasi_input').attr('disabled',false).prop('checked',true);
				}
            });        
				
          }, 500);
         }
       
		if(buttonType == 'reschedule'){
			disableFormReschedule(true);
		}
		else if(buttonType == 'View'){
		  disableFormElements(true);
		}
	   }
     },
	complete: function(){
		$('#loader').addClass('hidden');
	},
     error: function(response) {
      get_edit(officialtravelID,buttonType);
    }
  });
}
function popup_edit() {
  $("#travelForm")[0].reset();
  $("#with_trans").prop('checked',false).trigger('change');
  $("#with_accom").prop('checked',false).trigger('change');
  $("#travel_type").val(null).trigger('change');
  $(".invalid-feedback").children("strong").text("");
  $("#travelForm input").removeClass("is-invalid");
  $("#travelForm textarea").removeClass("is-invalid");
  $("#travelForm select").removeClass("is-invalid");
  $(".error-tab").html("");
  jQuery('.cash').remove();
  jQuery('.trans').remove();
  jQuery('.akomo').remove();
  $("#tab-transport").css('display','none');
  $("#tab-akomodasi").css('display','none');
  $("#tab-pane-2").removeClass('active');
  $("#tab-pane-3").removeClass('active');
  urlAjax = "{{ route('edit.offtrave') }}";
  $("#modal_form_travel").modal('show');
}
$(document).ready(function() {
  $(document).on('click', '.btn-edit, .reschedule', function() {
    show_loading();
	global_reschedule = $(this).attr('more_type');
    officialtravelID = $(this).attr('more_id');
	buttonType = $(this).attr('more_type');
	if(buttonType == 'reschedule')
		$(".modal-title").html('<i class="far fa-calendar-alt"></i> Reschedule Official Travel');
	else {
		$(".modal-title").html('<i class="fas fa-edit"></i> Edit Official Travel');
	}
    $(".action").removeClass('btn-success');
    $(".action").addClass('btn-primary');
    $("#label_button_action").html('Update');
    $("#label_button_action_submit").html('Update & Submit');
    popup_edit();
    if (officialtravelID) {
      get_edit(officialtravelID,buttonType);
    }
  });
});

var officialtravelID = "";
$(document).ready(function() {
  $(document).on('click', '.btn-view', function() {
    show_loading();
    // buttonType = 'View';
    officialtravelID = $(this).attr('more_id');
    buttonType = $(this).attr('more_type');
    $(".modal-title").html('<i class="fas fa-view"></i> Official Travel');
    $("#saveForm").css('display','none');
	get_data_view(officialtravelID).then(function(res) {
		popup_edit();
		if (officialtravelID) {
		  get_edit(officialtravelID,buttonType);
		}
	});      
  });
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
function actionOfftrave(officialtravelID,buttonType,transactionID) {
  $.ajax({
    method: "GET",
    url: "{{url('cash_advance/official_travel/official_travel/action-official-travel')}}",
    data : {id_official_travel: officialtravelID, type_action: buttonType, id_approval_transaction: transactionID},
	beforeSend: function () {
		$('#loader').removeClass('hidden');
	},
    success:function(response)
    {
      if (response.status == 'true') {
		  var formDataSubmit = response.data;
		  $.ajax({
				type: 'POST',
				headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				},
				url: "{{ route('mail.mail_success') }}",
				data: {source: formDataSubmit},
			});
        swal({
          title: "Data Success!",
          icon: "success"
        });
        $('#officialtravel_table').DataTable().ajax.reload();         
      }else{
       swal({
        icon: 'error',
        title: 'Oops...',
        dangerMode: true,
        text: 'Something went wrong! ['+response.message+']'
      });
     }
   },
    complete: function(){
		$('#loader').addClass('hidden');
	},
   error: function(response) {
   $('#loader').addClass('hidden');
    swal({
      icon: 'error',
      title: 'Oops...',
      dangerMode: true,
      text: 'Something went wrong! ['+response.message+']'
    });
  }
});
}
$(document).on('click', '.btn-action', function (event) {
  var officialtravelID = $(this).attr('more_id');
  var transactionID = $(this).attr('more_transaction');
  buttonType = $(this).attr('more_type');
  event.preventDefault();
  if (buttonType == 'Approve') {
    var text = 'Continue to Request Submission Approval ?';
  }
  else if(buttonType == 'req_cancel'){
	var text = 'Continue to Request Cancel ?';
  }
  else{
    var text = 'Continue to Cancel Submission ?';
  }
  swal({
    title: 'Are you sure?',
    text: text,
    icon: 'warning',
    buttons: true,
    confirmButtonText: 'Yes!'
  }).then(function(value) {
    if (value) {
      actionOfftrave(officialtravelID, buttonType, transactionID);
    }
  });
});

/*
function input_max_budget(element,size){
	console.log(element,size);
	element.find('.max_budget_input').keyup(function(e) {
		$('#cashadvance_'+ size +'_total').val(element.val());
	});
}
*/
$(document).on('keyup', '.max_budget_input', function (event) {
	let ro = $(this).attr('readonly');
	if((typeof ro === 'undefined' || ro === false)){	
		let siz = $(this).attr('size');
		$('#cashadvance_'+ siz +'_total').val($(this).val());
	}
});		
	
				
// $(document).on('click', '.btn-approve', function (event) {
//   var officialtravelID = $(this).attr('more_id');
//   var transactionID = $(this).attr('more_transaction');
//   buttonType = $(this).attr('more_type');
//   event.preventDefault();
//   if (buttonType == 'Approve') {
//     var text = 'Continue to Request Submission Approval?!';
//   }else{
//     var text = 'Continue to Cancel Submission?!';
//   }
//   swal({
//     title: 'Are you sure?',
//     text: text,
//     icon: 'warning',
//     buttons: true,
//     dangerMode: true,
//     confirmButtonColor: '#3085d6',
//     cancelButtonColor: '#d33',
//     confirmButtonText: 'Yes!'
//   }).then(function(value) {
//     if (value) {
//       actionOfftrave(officialtravelID, buttonType, transactionID);
//     }
//   });
// });
</script>
@endsection