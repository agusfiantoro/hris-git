@extends('adminlte::page')
@section('title', 'Official Travel')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Official Travel
        </h5>
        <div class="card-tools">
          <button type="button" more_type="New" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Official Travel</button>
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
              <th data-priority="1">No.</th>
              <th data-priority="1">Reference Number</th>
              <th data-priority="1">Category</th>
              <th data-priority="1">Letter Date</th>
              <th data-priority="1">Request by</th>
              <th>Start Date</th>
              <th>End Date</th>
              <!-- <th data-priority="1">Location From</th> -->
              <th data-priority="1">Destination</th>
              <th data-priority="1">Action</th>
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
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="travelForm" enctype="multipart/form-data">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Travel Type <sup class="text-danger">*</sup></label>
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
             <!--  <div class="row">
                <label class="col-sm-4 col-form-label">From City <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="location_from" id="location_from" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="location_fromError">
                    <strong></strong>
                  </span>
                </div>
              </div> -->
              <div class="row">
                <label class="col-sm-4 col-form-label">Destination <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="location_to" id="location_to" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="location_toError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Reason to Travel <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <textarea class="form-control" id="reason_notes" rows="4" name="reason_notes"></textarea>
                  <span class="invalid-feedback" role="alert" id="reason_notesError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Letter Date <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="letter_date" id="letter_date" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="letter_dateError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Start & End Date <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="start_end" id="start_end" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="start_endError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Unlock GPS</label>
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
                  <select id="" name="" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="lock_geo_locationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Approved By </label>
                <div class="col-sm-8">
                  <select id="" name="" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="lock_geo_locationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Approval Status </label>
                <div class="col-sm-8">
                  <select class="form-control form-control-sm select_opsi" id="id_approval_status" name="id_approval_status" style="width: 100%;" readonly></select>
                  <span class="invalid-feedback" role="alert" id="id_approval_statusError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">With Transport & Accommodation</label>
                <div class="col-sm-1">
                  <input type="checkbox" class="text mt-3" style="width: 20px;height: 20px;" name="with_trans_accom" id="with_trans_accom">
                  <span class="invalid-feedback" role="alert" id="Error">
                    <strong></strong>
                  </span>
                </div>
                <div class="col-sm-7">
                  <span style="font-size: 14px;" class="text mt-3"><i>Note: Jika pengajuan maksimal H-3 dan dibelikan HR maka provide HR transport & accommodation dicentang.</i></span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">With Case Advance</label>
                <div class="col-sm-8">
                  <input type="" id="id_cashadvance_cash" name="id_cashadvance_cash" hidden="">
                  <input type="" id="id_cashadvance_trans_accom" name="id_cashadvance_trans_accom" hidden="">
                  <input type="checkbox" class="text mt-2" style="width: 20px;height: 20px;" value="true" name="with_caseadvance" id="with_caseadvance">
                  <span class="invalid-feedback" role="alert" id="Error">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <!-- <div class="row mt-4">
                <div class="col-xl-12">
                  <span style="font-size: 14px;"><i>Note: jika dibelikan self service maka with transport dan akomodasi harus di centang.</i></span>
                </div>
              </div> -->
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
                 <div class="table-responsive">
                  <table class="table table-hover table-bordered table-striped table-responsive-stack">
                    <thead>
                     <tr>
                      <th>No. </th>
                      <th>Category</th>
                      <th>Notes</th>
                      <!-- <th>UOM</th> -->
                      <th>Date</th>
                      <th>Region Destination</th>
                      <th>Total</th>
                      <th>Max Budget</th>
                      <th>Action</th>
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
             <div class="table-responsive">
              <table class="table table-hover table-bordered table-striped table-responsive-stack">
               <thead>
                 <tr>
                  <th rowspan="2">No. </th>
                  <th rowspan="2">Transport Type</th>
                  <th rowspan="2">Transport Name Recommendation</th>
                  <th colspan="2">Destination</th>
                  <th colspan="2">Departure</th>
                  <!-- <th rowspan="2">Price</th> -->
                  <th rowspan="2">Action</th>
                </tr>
                <tr>
                  <th>From</th>
                  <th>To / Branch</th>
                  <th>Date</th>
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
         <div class="table-responsive">
          <table class="table table-hover table-bordered table-striped table-responsive-stack">
            <thead>
             <tr>
              <th>No. </th>
              <th>Category</th>
              <th>Name of Hotel/Kos</th>
              <th>Start End Date</th>
              <th>Length of stay (days)</th>
              <th>UOM</th>
              <!-- <th>Price</th> -->
              <th>Action</th>
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
  <button class="btn btn-sm btn-success action_submit" type="button"><i class="fas fa-paper-plane"></i> <span id="label_button_action_submit"></span></button>&nbsp;
  <button class="btn btn-sm btn-info action"><i class="fas fa-save"></i> <span id="label_button_action"></span></button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
<!-- End Form -->
<!-- Rows Cash Advance -->
<div style="display:none;">
  <table id="sample_table_cash_advance">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input autocomplete="off" name="cashadvance[0][id_cash_advance]" hidden="" id="cashadvance_0_id_cash_advance" class="form-control form-control-sm id_cash_advance_input">
        <select name="cashadvance[0][nama]" id="cashadvance_0_nama" class="form-control form-control-sm nama_input" style="width: 100%;"></select>
        <span class="invalid-feedback nama_input_error" role="alert" id="cashadvance_0_namaError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="cashadvance[0][notes_cashadvance]" id="cashadvance_0_notes_cashadvance" class="form-control form-control-sm notes_cashadvance_input">
        <span class="invalid-feedback notes_cashadvance_input_error" role="alert" id="cashadvance_0_notes_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <!-- <td>
        <select name="cashadvance[0][uom_cashadvance]" id="cashadvance_0_uom_cashadvance" class="form-control form-control-sm uom_cashadvance_input" style="width: 100%;" readonly></select>
        <span class="invalid-feedback uom_cashadvance_input_error" role="alert" id="cashadvance_0_uom_cashadvanceError">
          <strong></strong>
        </span>
      </td> -->
      <td>
        <input type="text" autocomplete="off" name="cashadvance[0][tanggal]" id="cashadvance_0_tanggal" class="form-control form-control-sm tanggal_input">
        <span class="invalid-feedback tanggal_input_error" role="alert" id="cashadvance_0_tanggalError">
          <strong></strong>
        </span>
      </td>
      <td style="max-width: 200px;">
        <select name="cashadvance[0][region_cashadvance]" id="cashadvance_0_region_cashadvance" class="form-control form-control-sm region_cashadvance_input" style="width: 100%;"></select>
        <span class="invalid-feedback region_cashadvance_input_error" role="alert" id="cashadvance_0_region_cashadvanceError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="cashadvance[0][total]" id="cashadvance_0_total" class="form-control form-control-sm total_input">
        <span class="invalid-feedback total_input_error" role="alert" id="cashadvance_0_totalError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="cashadvance[0][max_budget]" id="cashadvance_0_max_budget" class="form-control form-control-sm max_budget_input">
        <span class="invalid-feedback max_budget_input_error" role="alert" id="cashadvance_0_max_budgetError">
          <strong></strong>
        </span>
      </td>
      <td>
        <center>
          <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
      </td>
    </tr>
  </table>
</div>
<!-- End CashAdvance -->
<!-- Tabel Transport -->
<div style="display:none;">
  <table id="sample_table_transport">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input autocomplete="off" name="transport[0][id_transport]" hidden="" id="transport_0_id_transport" class="form-control form-control-sm id_transport_input">
        <select name="transport[0][jenis_transportasi]" id="transport_0_jenis_transportasi" class="form-control form-control-sm jenis_transportasi_input" style="width: 100%;"></select>
        <span class="invalid-feedback d-block jenis_transportasi_input_error" role="alert" id="transport_0_jenis_transportasiError">
          <strong></strong>
        </span>
      </td>
      <td style="width: 10%;">
        <input name="transport[0][transport_name]" autocomplete="off" id="transport_0_transport_name" class="form-control form-control-sm transport_name_input">
        <span class="invalid-feedback transport_name_input_error" role="alert" id="transport_0_transport_nameError">
          <strong></strong>
        </span>
      </td>
      <td style="width: 10%;">
        <input name="transport[0][from]" autocomplete="off" id="transport_0_from" class="form-control form-control-sm from_input" style="width: 90%;">
        <span class="invalid-feedback from_input_error" role="alert" id="transport_0_fromError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input name="transport[0][to]" autocomplete="off" style="display: inline-block;width: 35%;" id="transport_0_to" class="form-control form-control-sm to_input">
        <span class="invalid-feedback to_input_error" role="alert" id="transport_0_toError">
          <strong></strong>
        </span>
        /
        <select name="transport[0][branch]" id="transport_0_branch" class="form-control form-control-sm branch_input" style="width: 55%;display: inline-block;"></select>
        <span class="invalid-feedback d-block branch_input_error" role="alert" id="transport_0_branchError">
          <strong></strong>
        </span>
      </td>
     <!--  <td>
        <select name="transport[0][branch]" id="transport_0_branch" class="form-control form-control-sm branch_input" style="width: 100%;"></select>
        <span class="invalid-feedback d-block branch_input_error" role="alert" id="transport_0_branchError">
          <strong></strong>
        </span>
      </td> -->
      <td style="width: 15%;">
        <div class="input-group">
          <input type="text" autocomplete="off" name="transport[0][date_transport]" id="transport_0_date_transport" class="form-control form-control-sm date_transport_input" style="width: 100%;">
          <!-- <input type="text" class="form-control" id="yourDatePickerElement" /> -->
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
      <td style="width: 12%;">
        <input autocomplete="off" name="transport[0][time_transport]" id="transport_0_time_transport" class="form-control form-control-sm time_transport_input" style="width: 100%;">
        <span class="invalid-feedback d-block time_transport_input_error" role="alert" id="transport_0_time_transportError">
          <strong></strong>
        </span>
      </td>
     <!--  <td>
        <input type="text" autocomplete="off" name="transport[0][price_transport]" id="transport_0_price_transport" class="form-control form-control-sm price_transport_input">
        <span class="invalid-feedback price_transport_input_error" role="alert" id="transport_0_price_transportError">
          <strong></strong>
        </span>
      </td> -->
      <td>
        <center>
          <button type="button" class="delete-record-trans btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
      </td>
    </tr>
  </table>
</div>
<!-- End Transport -->
<!-- Table Akomodasi -->
<div style="display:none;">
  <table id="sample_table_akomodasi">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input autocomplete="off" name="akomodasi[0][id_akomodasi]" hidden="" id="akomodasi_0_id_akomodasi" class="form-control form-control-sm id_akomodasi_input">
        <select name="akomodasi[0][product_akomodasi]" id="akomodasi_0_product_akomodasi" class="form-control form-control-sm product_akomodasi_input" style="width: 100%;"></select>
        <span class="invalid-feedback d-block product_akomodasi_input_error" role="alert" id="akomodasi_0_product_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="akomodasi[0][nama_hotel]" id="akomodasi_0_nama_hotel" class="form-control form-control-sm nama_hotel_input">
        <span class="invalid-feedback nama_hotel_input_error" role="alert" id="akomodasi_0_nama_hotelError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input name="akomodasi[0][start_end_akomodasi]" autocomplete="off" id="akomodasi_0_start_end_akomodasi" class="form-control form-control-sm start_end_akomodasi_input">
        <span class="invalid-feedback start_end_akomodasi_input_error" role="alert" id="akomodasi_0_start_end_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="akomodasi[0][lama_menginap]" id="akomodasi_0_lama_menginap" class="form-control form-control-sm lama_menginap_input">
        <span class="invalid-feedback lama_menginap_input_error" role="alert" id="akomodasi_0_lama_menginapError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="akomodasi[0][uom_akomodasi]" id="akomodasi_0_uom_akomodasi" class="form-control form-control-sm uom_akomodasi_input" style="width: 100%;" readonly></select>
        <span class="invalid-feedback d-block uom_akomodasi_input_error" role="alert" id="akomodasi_0_uom_akomodasiError">
          <strong></strong>
        </span>
      </td>
     <!--  <td>
        <input type="text" autocomplete="off" name="akomodasi[0][price_akomodasi]" id="akomodasi_0_price_akomodasi" class="form-control form-control-sm price_akomodasi_input">
        <span class="invalid-feedback price_akomodasi_input_error" role="alert" id="akomodasi_0_price_akomodasiError">
          <strong></strong>
        </span>
      </td> -->
      <td>
        <center>
          <button type="button" class="delete-record-akomo btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
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
  /*@media (max-width: 767px) {
    .table-responsive-stack tr {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
    }
    .table-responsive-stack td {
      width: 100%;
      display: block;
    }
    }*/
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

  </style>
  @endsection

  @section('scripts')
  <script type="text/javascript">
    moment.updateLocale('id', {
      weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
    });
    function TanggalIndonesia(string) {
      var formattedDate = moment(string).format('dddd, D MMMM YYYY');
      return formattedDate;
    }
    $(function () {
      $('#officialtravel_table').DataTable({
        processing: true,
        pageLength: 10,
        responsive: true,        
        ajax: {
          url: "{{ route('index.offtrave') }}",
          data : {id_url:global_url_server},
          error: function (jqXHR, textStatus, errorThrown) {
            $('#officialtravel_table').DataTable().ajax.reload();
          }
        },
        columns: [
        {
          defaultContent: '',
          orderable: false,
        },
        {   
          data: 'id_official_travel',
          defaultContent: '',
          orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex'},
        { data: 'reference_number', name: 'reference_number' },
        { data: 'category', name: 'category' },
        { 
          data: 'letter_date', 
          name: 'letter_date', 
          render: function (data, type, row) {
            return TanggalIndonesia(data);
          }  
        },
        // { data: 'name', name: 'name' },
        { 
          data: 'name', 
          name: 'name', 
          render: function (data, type, row) {
            return data;
          },
          className: 'name-column'
        },

        {
          data: 'start_date',
          name: 'start_date',
          render: function (data, type, full, meta) {
            if (type === 'display' || type === 'filter') {
              var startDate = new Date(data);
              return TanggalIndonesia(startDate.toISOString().split('T')[0]);
            }
            return data;
          }
        },
        {
          data: 'end_date',
          name: 'end_date',
          render: function (data, type, full, meta) {
            if (type === 'display' || type === 'filter') {
              var startDate = new Date(data);
              return TanggalIndonesia(startDate.toISOString().split('T')[0]);
            }
            return data;
          }
        },
        // { data: 'location_from', name: 'location_from' },
        { data: 'location_to', name: 'location_to' },
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
    });
    $('#location_from').on('keyup', function() {
      $(this).val($(this).val().toUpperCase());
    });
    $('#location_to').on('keyup', function() {
      $(this).val($(this).val().toUpperCase());
    });
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
    var maximum_cash_advance_request = "";
    var job_class_group = "";
    function get_data(){
      $.ajax({
        method: "GET",
        url : "{{url('cash_advance/official_travel/official_travel/get_data')}}",
        success: function (response) {
         if (response.user) {
          hide_loading();
          global_product_transport = response.product_transport;
          global_product_akomodasi = response.product_akomodasi;
          global_product_cashadvance = response.product_cashadvance;
          global_region_destination = response.region_destination;
          global_branch_transport = response.branch_transport;
          maximum_cash_advance_request = response.maximum_cash_advance_request.maximum_cash_advance_request;
          $("#id_dept").append('<option value="'+response.user[0].id_dept+'">'+response.user[0].id_dept+'</option')
          $("#id_position_detail").val(response.user[0].id_position_detail)
          $("#id_reason_group").select2({
            placeholder: 'Select Travel Type ....',
            allowClear: true,
            data: response.travel_type.map(function(item) {
              return { id: item.id_general_data, text: item.description };
            })
          });
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
          // job_class_group = "gm-level";
          job_class_group = response.user[0].job_class_group;
          $("#id_approval_status").select2({
            data: response.approval_status
          });
        }
      },
      error: function(response) {
        if (response.status === 500) {
          get_data();
        }else{
          location.reload();
        }
      }
    });  
    }
    $(document).ready(function() {
      get_data();
    });
    var today = new Date();
    var formattedDate = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate().toString().padStart(2, '0');
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
            $("#with_trans_accom").prop('checked',false).trigger('change');
            $("#with_caseadvance").prop('checked',false).trigger('change');
            $("#with_caseadvance").removeClass('readonly-checkbox');
            if (job_class_group != 'gm-level') {
              if (max_days < maximum_cash_advance_request) {
                $("#with_trans_accom").addClass('readonly-checkbox');
              } else {
                $("#with_trans_accom").removeClass('readonly-checkbox');
              }
            }else{
              $("#with_trans_accom").removeClass('readonly-checkbox');
            }
          }
        },
        error: function(response) {
          check_max_date_cashadvance(start_date, request_by);
        }
      });
    }
    $("#start_end").daterangepicker({
      autoUpdateInput: false,
      autoApply: false,
      minDate: moment().startOf('day'),
      locale: {
        cancelLabel: 'Riset',
        format: 'YYYY-MM-DD',
        separator: ' to '
      }
    }).on('apply.daterangepicker',function(ev, picker) {
      show_loading();
      var startDate = picker.startDate;
      var endDate = picker.endDate;
      var request_by = $("#request_by").val();
      $("#start_end").val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
      var max_min_date = $("#start_end").val();
      if (startDate) {
        check_max_date_cashadvance(startDate, request_by);
      }
      // max date cashadvance
      if (max_min_date != null) {
        const dateHeader = max_min_date.split(" to ");
        const startDateHeader = dateHeader[0];
        const endDateHeader = dateHeader[1];
        var startDateHeaderFormat = new Date(startDateHeader);
        var endDateHeaderFormat = new Date(endDateHeader);
        // max date cashadvance
        $('#table_cash_advance tr').each(function (index) {
          let maxPrice = "";
          let days = 0;
          $(this).find('.tanggal_input').val('');
          $(this).find('.max_budget_input').val('');
          DateCashAdvance(element, startDateHeader, endDateHeader, maxPrice, days);
          RegionCashAdvance(element, maxPrice, days);
        });
        // max date akomodasi
        $('.start_end_akomodasi_input').val('');
        $('.lama_menginap_input').val('');
        $('.start_end_akomodasi_input').daterangepicker({
          drops: 'up',
          autoUpdateInput: false,
          minDate: startDateHeader,
          maxDate: endDateHeader,
          locale: {
            cancelLabel: 'Riset',
            format: 'YYYY-MM-DD',
            separator: ' to '
          }
        }).on('apply.daterangepicker', function(ev, picker) {
          var startDate = picker.startDate;
          var endDate = picker.endDate;
          $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
        }).on('cancel.daterangepicker', function() {
          $(this).val('');
        }).on('keydown.daterangepicker',function(e) {
          e.preventDefault();
        });
        // Transport
        $(".date_transport_input").val('');
        $('.date_transport_input').daterangepicker({
          drops: 'up',
          singleDatePicker: true,
          autoUpdateInput: false,
          autoApply: true,
          minDate: startDateHeader,
          maxDate: endDateHeader,
          locale: {
          // cancelLabel: 'Riset',
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
    // check and uncheck with trans akomo
  }).on('cancel.daterangepicker', function() {
    $(".tanggal_input").val('');
    $("#with_trans_accom").prop('checked',false).trigger('change');
    $("#with_trans_accom").addClass('readonly-checkbox');
    $("#with_caseadvance").prop('checked',false).trigger('change');
    $("#with_caseadvance").addClass('readonly-checkbox');
    $(this).val('');
  }).on('keydown.daterangepicker',function(e) {
    e.preventDefault();
  }).on('cut.daterangepicker',function(e) {
    e.preventDefault();
  });
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
  function disabled_all_form(status_approval, buttonType) {
    if (buttonType == 'Edit') {
      if (status_approval == 'New') {
        disableFormElements(false);
      } else if(status_approval == 'Revised') {
        disableFormElements(false);
      }else{
        disableFormElements(true);
      }
    }else if(buttonType == 'New'){
      disableFormElements(false);
    }
  }
  var urlAjax = "";
  var buttonType = "";
  $(".new").click(function() {
    get_data();
    $("#travelForm")[0].reset();
    buttonType = $(this).attr('more_type');
    $("#travel_type").val(null).trigger('change');
    $(".modal-title").html('<i class="fas fa-plus"></i> Form Official Travel');
    $("#label_button_action").html('Save as Draft');
    $("#label_button_action_submit").html('Save & Submit');
    $(".invalid-feedback").children("strong").text("");
    $("#travelForm input").removeClass("is-invalid");
    $("#travelForm textarea").removeClass("is-invalid");
    $("#travelForm select").removeClass("is-invalid");
    $(".error-tab").html("");
    $("#start_end").attr('disabled',false);
    $("#with_caseadvance").prop('checked',false).trigger('change');
    $("#unlock_gps").prop('checked',false).trigger('change');
    // $("#with_caseadvance").removeClass('readonly-checkbox');
    $("#letter_date").val(formattedDate);
    if (global_id_transport != 0) {
      global_id_transport = 0;
    }
    if (global_id_akomodasi != 0) {
      global_id_akomodasi = 0;
    }
    urlAjax = "{{route('save.offtrave')}}";
    jQuery('.cash').remove();
    jQuery('.trans').remove();
    jQuery('.akomo').remove();
    $("#with_trans_accom").addClass('readonly-checkbox');
    $("#with_caseadvance").addClass('readonly-checkbox');
    $("#with_trans_accom").prop('checked',false).trigger('change');
    $("#with_caseadvance").prop('checked',false).trigger('change');
    $("#tab-transport").css('display','none');
    $("#tab-akomodasi").css('display','none');
    $("#tab-pane-2").removeClass('active');
    $("#modal_form_travel").modal('show');
    disabled_all_form('-',buttonType);
    $("#letter_date").attr('disabled',true);
  });
  $(document).on('change','#with_trans_accom',function() {
    var with_trans_accom = document.getElementById('with_trans_accom');
    if (with_trans_accom.checked) {
      if (global_id_transport == 0 && buttonType == 'New') {
        $("#new_transport").click();
      }
      if (global_id_akomodasi == 0 && buttonType == 'New') {
        $("#new_accommodation").click();
      }
      $("#tab_detail").find("[href='#tab-pane-2']").click();
      $("#tab-pane-2").addClass('active');
      $("#tab-transport").css('display','block');
      $("#tab-akomodasi").css('display','block');
    }else{
      $("#tab-pane-2").removeClass('active');
      $("#tab-pane-3").removeClass('active');
      $("#tab-transport").css('display','none');
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
    return parseInt(rupiah.replace(/,|\./g, '').);
  }
  let global_id_cash_advance = 0;
  function DateCashAdvance(element, startDateHeader, endDateHeader, maxPrice, days) {
    element.daterangepicker({
      drops: 'up',
      autoUpdateInput: false,
      minDate: startDateHeader,
      maxDate: endDateHeader,
      locale: {
        cancelLabel: 'Riset',
        format: 'YYYY-MM-DD',
        separator: ' to '
      }
    }).on('apply.daterangepicker', function(ev, picker) {
      var startDate = picker.startDate;
      var endDate = picker.endDate;
      var timeDiff = endDate - startDate;
      maxPrice = maxPrice;
      days = Math.floor(timeDiff / (1000 * 3600 * 24));
      alert(days+maxPrice);
      var totalMaxBudget = unformatRupiah(maxPrice);
      $(this).closest('tr').find('.max_budget_input').val(formatRupiah(totalMaxBudget*days));
      element.val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
    }).on('cancel.daterangepicker', function() {
      days = 1;
      $(this).closest('tr').find('.max_budget_input').val('');
      element.val('');
    }).on('keydown.daterangepicker',function(e) {
      e.preventDefault();
    }).on('cut.daterangepicker',function(e) {
      e.preventDefault();
    });
  }
  function RegionCashAdvance(element, maxPrice, days) {
    element.select2({
      placeholder: 'Select Region Destination ....',
      data : global_region_destination.map(function(item) {
        return {
          id: item.text,
          text: item.text,
          maxPriceAttr: item.max_price
        };
      })
    }).on('change',function(e) {
      var selectedOption = element.select2('data')[0];
      if (selectedOption) {
        maxPrice = selectedOption.maxPriceAttr;
        const dateHeaderCashAdvance = $(this).closest('tr').find('.tanggal_input').val();
        if (dateHeaderCashAdvance != null) {
          const dateHeader = dateHeaderCashAdvance.split(" to ");
          const startDateCashAdvance = dateHeader[0];
          const endDateCashAdvance = dateHeader[1];
          var timeDiff = new Date(endDateCashAdvance) - new Date(startDateCashAdvance);
          days = Math.floor(timeDiff / (1000 * 3600 * 24));
          var totalMaxBudget = unformatRupiah(maxPrice);
          $(this).closest('tr').find('.max_budget_input').val(formatRupiah(totalMaxBudget*days));
        }else{
          $(this).closest('tr').find('.max_budget_input').val('');
          days = 1;
        }
      }else{
        maxPrice = "";
      }
    }).trigger('change');
  }
  $(document).on('click', '#new_cash_advance', function () {
    var max_min_date = $("#start_end").val();
    var content = jQuery("#sample_table_cash_advance tr"),
    size = global_id_cash_advance++,
    element = null,
    element = content.clone();
    element.attr('id','cash-'+size);
    element.attr('class','cash');
    element.find('.delete-record').attr('data-id', size);

    let maxPrice = "";
    let days = 1;

    element.find('.id_cash_advance_input').attr('id', 'cashadvance_' + size + '_id_cash_advance');
    element.find('.id_cash_advance_input').attr('name', 'cashadvance[' + size + '][id_cash_advance]');
    element.find('.id_cash_advance_input_error').attr('id', 'cashadvance_' + size + '_id_cash_advanceError');

    element.find('.nama_input').attr('id', 'cashadvance_' + size + '_nama');
    element.find('.nama_input').attr('name', 'cashadvance[' + size + '][nama]');
    element.find('.nama_input_error').attr('id', 'cashadvance_' + size + '_namaError');
    element.find('.nama_input').select2({
      allowClear: true,
      placeholder: 'Select Name ....',
      data: global_product_cashadvance.map(function(item) {
        return {id: item.id, text: item.text, decUom: item.dec_uom, idUom: item.id_uom}
      })
    }).on('change',function(e) {
      var selectedOption = $(this).select2('data')[0];
      if (selectedOption) {
        var dec_product = selectedOption.text;
        element.find('.max_budget_input').val('');
        if (dec_product != 'Uang Makan') {
          element.find('.region_cashadvance_input').val(null).trigger('change');
          element.find('.region_cashadvance_input').attr('readonly',true);
        }else{
          element.find('.region_cashadvance_input').attr('readonly',false);
        }
      }else{
        maxPrice = 0;
        days = 0;
        element.find('.max_budget_input').val('');
        element.find('.region_cashadvance_input').attr('readonly','readonly');
        element.find('.region_cashadvance_input').val(null).trigger('change');
      }
    }).val(null).trigger('change');
    element.find('.nama_input').val(null).trigger('change');

    element.find('.notes_cashadvance_input').attr('id', 'cashadvance_' + size + '_notes_cashadvance');
    element.find('.notes_cashadvance_input').attr('name', 'cashadvance[' + size + '][notes_cashadvance]');
    element.find('.notes_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_notes_cashadvanceError');

    // element.find('.uom_cashadvance_input').attr('id', 'cashadvance_' + size + '_uom_cashadvance');
    // element.find('.uom_cashadvance_input').attr('name', 'cashadvance[' + size + '][uom_cashadvance]');
    // element.find('.uom_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_uom_cashadvanceError');
    // element.find('.uom_cashadvance_input').select2();

    element.find('.tanggal_input').attr('id', 'cashadvance_' + size + '_tanggal');
    element.find('.tanggal_input').attr('name', 'cashadvance[' + size + '][tanggal]');
    element.find('.tanggal_input_error').attr('id', 'cashadvance_' + size + '_tanggalError');
    if (max_min_date != null) {
      const dateHeader = max_min_date.split(" to ");
      const startDateHeader = dateHeader[0];
      const endDateHeader = dateHeader[1];
      // element.find('.tanggal_input').daterangepicker({
      //   drops: 'up',
      //   autoUpdateInput: false,
      //   minDate: startDateHeader,
      //   maxDate: endDateHeader,
      //   locale: {
      //     cancelLabel: 'Riset',
      //     format: 'YYYY-MM-DD',
      //     separator: ' to '
      //   }
      // }).on('apply.daterangepicker', function(ev, picker) {
      //   var startDate = picker.startDate;
      //   var endDate = picker.endDate;
      //   var timeDiff = endDate - startDate;
      //   days = Math.floor(timeDiff / (1000 * 3600 * 24));
      //   var totalMaxBudget = unformatRupiah(maxPrice);
      //   element.find('.max_budget_input').val(formatRupiah(totalMaxBudget*days));
      //   $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
      // }).on('cancel.daterangepicker', function() {
      //   days = 1;
      //   element.find('.max_budget_input').val('');
      //   $(this).val('');
      // }).on('keydown.daterangepicker',function(e) {
      //   e.preventDefault();
      // }).on('cut.daterangepicker',function(e) {
      //   e.preventDefault();
      // });
      DateCashAdvance(element.find('.tanggal_input'), startDateHeader, endDateHeader, maxPrice, days);
    }


    element.find('.region_cashadvance_input').attr('id', 'cashadvance_' + size + '_region_cashadvance');
    element.find('.region_cashadvance_input').attr('name', 'cashadvance[' + size + '][region_cashadvance]');
    element.find('.region_cashadvance_input_error').attr('id', 'cashadvance_' + size + '_region_cashadvanceError');
    RegionCashAdvance(element.find('.region_cashadvance_input'), maxPrice, days);
    // element.find('.region_cashadvance_input').select2({
    //   placeholder: 'Select Region Destination ....',
    //   data : global_region_destination.map(function(item) {
    //     return {
    //       id: item.text,
    //       text: item.text,
    //       maxPriceAttr: item.max_price
    //     };
    //   })
    // }).on('change',function(e) {
    //   var selectedOption = $(this).select2('data')[0];
    //   if (selectedOption) {
    //     maxPrice = selectedOption.maxPriceAttr;
    //     const dateHeaderCashAdvance = element.find('.tanggal_input').val();
    //     if (dateHeaderCashAdvance != '') {
    //       const dateHeader = dateHeaderCashAdvance.split(" to ");
    //       const startDateCashAdvance = dateHeader[0];
    //       const endDateCashAdvance = dateHeader[1];
    //       var timeDiff = new Date(endDateCashAdvance) - new Date(startDateCashAdvance);
    //       days = Math.floor(timeDiff / (1000 * 3600 * 24));
    //       var totalMaxBudget = unformatRupiah(maxPrice);
    //       element.find('.max_budget_input').val(formatRupiah(totalMaxBudget*days));
    //     }else{
    //       // maxPrice = "";
    //       element.find('.max_budget_input').val('');
    //       days = 1;
    //     }
    //     // if (maxPrice) {
    //     // }else{
    //     // }
    //   }else{
    //     maxPrice = "";
    //   }
    // }).val(null).trigger('change');
    element.find('.region_cashadvance_input').val(null).trigger('change');

    element.find('.total_input').attr('id', 'cashadvance_' + size + '_total');
    element.find('.total_input').attr('name', 'cashadvance[' + size + '][total]');
    element.find('.total_input_error').attr('id', 'cashadvance_' + size + '_totalError');
    element.find('.total_input').on('input', function() {
      var rupiah = this.value;
      var numberTotal = unformatRupiah(rupiah);
      this.value = formatRupiah(numberTotal);
    });

    element.find('.max_budget_input').attr('id', 'cashadvance_' + size + '_max_budget');
    element.find('.max_budget_input').attr('name', 'cashadvance[' + size + '][max_budget]');
    element.find('.max_budget_input_error').attr('id', 'cashadvance_' + size + '_max_budgetError');
    element.find('.max_budget_input').attr('readonly',true);

    element.appendTo('#table_cash_advance');
    $('#table_cash_advance tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
  });
$(document).on('click', '.delete-record', function () {
  var id = jQuery(this).attr('data-id');
  var targetDiv = jQuery(this).attr('targetDiv');
  jQuery('#cash-' + id).remove();
  var more_id = jQuery(this).attr('more_id');
  var currentValues = $("#id_cash_advance_del").val();
  if (currentValues) {
    if (more_id) {
      $("#id_cash_advance_del").val(currentValues + ',' + more_id);
    }
  } else {
    $("#id_cash_advance_del").val(more_id);
  }
  $('#table_cash_advance tr').each(function (index) {
    $(this).find('span.sn').html(index + 1);
  });
  return true;
});
  // trasnport
  let global_id_transport = 0;
  $(document).on('click', '#new_transport', function () {
    var max_min_date = $("#start_end").val();
    var content = jQuery("#sample_table_transport tr"),
    size = global_id_transport++,
    element = null,
    element = content.clone();
    element.attr('id','trans-'+size);
    element.attr('class','trans');
    element.find('.delete-record-trans').attr('data-id', size);

    element.find('.id_transport_input').attr('id', 'transport_' + size + '_id_transport');
    element.find('.id_transport_input').attr('name', 'transport[' + size + '][id_transport]');
    element.find('.id_transport_input_error').attr('id', 'transport_' + size + '_id_transportError');

    element.find('.jenis_transportasi_input').attr('id', 'transport_' + size + '_jenis_transportasi');
    element.find('.jenis_transportasi_input').attr('name', 'transport[' + size + '][jenis_transportasi]');
    element.find('.jenis_transportasi_input_error').attr('id', 'transport_' + size + '_jenis_transportasiError');
    element.find('.jenis_transportasi_input').select2({
      allowClear: true,
      placeholder: 'Select Transport Type ....',
      data: global_product_transport
    }).on('change',function(e) {
    }).trigger('change');
    element.find('.jenis_transportasi_input').val(null).trigger('change');

    element.find('.transport_name_input').attr('id', 'transport_' + size + '_transport_name');
    element.find('.transport_name_input').attr('name', 'transport[' + size + '][transport_name]');
    element.find('.transport_name_input_error').attr('id', 'transport_' + size + '_transport_nameError');

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
      allowClear: true,
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

    // element.find('.price_transport_input').attr('id', 'transport_' + size + '_price_transport');
    // element.find('.price_transport_input').attr('name', 'transport[' + size + '][price_transport]');
    // element.find('.price_transport_input_error').attr('id', 'transport_' + size + '_price_transportError');
    // element.find('.price_transport_input').on('input', function() {
    //   var rupiah = this.value;
    //   var numberTotal = unformatRupiah(rupiah);
    //   this.value = formatRupiah(numberTotal);
    // });
    // element.find('.price_transport_input').attr('readonly',true);

    element.appendTo('#table_transport');
    $('#table_transport tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
  });
$(document).on('click', '.delete-record-trans', function () {
  var id = jQuery(this).attr('data-id');
  var targetDiv = jQuery(this).attr('targetDiv');
  var more_id = jQuery(this).attr('more_id');
  var currentValues = $("#id_transport_del").val();
  if (currentValues) {
    if (more_id) {
      $("#id_transport_del").val(currentValues + ',' + more_id);
    }
  } else {
    $("#id_transport_del").val(more_id);
  }
  jQuery('#trans-' + id).remove();
  $('#table_transport tr').each(function (index) {
    $(this).find('span.sn').html(index + 1);
  });
  return true;
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
      element.find('.delete-record-akomo').attr('data-id', size);

      element.find('.id_akomodasi_input').attr('id', 'akomodasi_' + size + '_id_akomodasi');
      element.find('.id_akomodasi_input').attr('name', 'akomodasi[' + size + '][id_akomodasi]');
      element.find('.id_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_id_akomodasiError');

      element.find('.product_akomodasi_input').attr('id', 'akomodasi_' + size + '_product_akomodasi');
      element.find('.product_akomodasi_input').attr('name', 'akomodasi[' + size + '][product_akomodasi]');
      element.find('.product_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_product_akomodasiError');
      element.find('.product_akomodasi_input').select2({
        allowClear: true,
        placeholder: 'Select Product ....',
        data: global_product_akomodasi.map(function(item) {
          return {id: item.id, text: item.text, id_uom: item.id_uom, dec_uom: item.dec_uom}
        })
      }).on('change',function(e) {
        var selectedOption = $(this).select2('data')[0];
        if (selectedOption) {
          var idUom = selectedOption.id_uom;
          var decUom = selectedOption.dec_uom;
          element.find('.uom_akomodasi_input').empty();
          if (idUom) {
            element.find('.uom_akomodasi_input').append('<option value="'+idUom+'">'+decUom+'</option>');
          }else{
            element.find('.uom_akomodasi_input').empty();
          }
        }
      }).val(null).trigger('change');
      element.find('.product_akomodasi_input').val(null).trigger('change');

      element.find('.nama_hotel_input').attr('id', 'akomodasi_' + size + '_nama_hotel');
      element.find('.nama_hotel_input').attr('name', 'akomodasi[' + size + '][nama_hotel]');
      element.find('.nama_hotel_input_error').attr('id', 'akomodasi_' + size + '_nama_hotelError');

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
            cancelLabel: 'Riset',
            format: 'YYYY-MM-DD',
            separator: ' to '
          }
        }).on('apply.daterangepicker', function(ev, picker) {
          var startDate = picker.startDate;
          var endDate = picker.endDate;
          var timeDiff = endDate - startDate;
          var daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));
          element.find('.lama_menginap_input').val(daysDiff+' days');
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
      element.find('.lama_menginap_input').attr('readonly', true)

      element.find('.uom_akomodasi_input').attr('id', 'akomodasi_' + size + '_uom_akomodasi');
      element.find('.uom_akomodasi_input').attr('name', 'akomodasi[' + size + '][uom_akomodasi]');
      element.find('.uom_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_uom_akomodasiError');
      element.find('.uom_akomodasi_input').select2();

      // element.find('.price_akomodasi_input').attr('id', 'akomodasi_' + size + '_price_akomodasi');
      // element.find('.price_akomodasi_input').attr('name', 'akomodasi[' + size + '][price_akomodasi]');
      // element.find('.price_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_price_akomodasiError');
      // element.find('.price_akomodasi_input').on('input', function() {
      //   var rupiah = this.value;
      //   var numberTotal = unformatRupiah(rupiah);
      //   this.value = formatRupiah(numberTotal);
      // });
      // element.find('.price_akomodasi_input').attr('readonly',true);

      element.appendTo('#table_accommodation');
      $('#table_accommodation tr').each(function (index) {
        $(this).find('span.sn').html(index + 1);
      });
    });
$(document).on('click', '.delete-record-akomo', function () {
  var id = jQuery(this).attr('data-id');
  var targetDiv = jQuery(this).attr('targetDiv');
  var more_id = jQuery(this).attr('more_id');
  var currentValues = $("#id_akomodasi_del").val();
  if (currentValues) {
    if (more_id) {
      $("#id_akomodasi_del").val(currentValues + ',' + more_id);
    }
  } else {
    $("#id_akomodasi_del").val(more_id);
  }
  jQuery('#akomo-' + id).remove();
  $('#table_accommodation tr').each(function (index) {
    $(this).find('span.sn').html(index + 1);
  });
  return true;
});
$(function () {
  $('#travelForm').submit(function (e) {
    e.preventDefault();
    document.querySelector(".action").disabled=true;
    let formData = $(this).serializeArray();
    $(".invalid-feedback").children("strong").text("");
    $("#travelForm input").removeClass("is-invalid");
    $("#travelForm textarea").removeClass("is-invalid");
    $("#travelForm select").removeClass("is-invalid");
    $(".error-tab").html("");
    $.ajax({
      method: "POST",
      headers: {
        Accept: "application/json"
      },
      url: urlAjax,
      data: formData,
      success: function (response) {
        document.querySelector(".action").disabled=false;
        if (response.status == 'true') {
          swal({
            icon: 'success',
            title: 'Success',
            text: response.message
          });
          $("#modal_form_travel").modal('hide');
          $('#officialtravel_table').DataTable().ajax.reload();
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
            text: 'Something went wrong! [Unknown Error Not Save]'
          });
        }
      },
      error: function (response) {
        document.querySelector(".action").disabled=false;
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
        }
        else if (response.status === 500) {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! [Unknown Error]'
          });
        }
        else {
          swal({
            icon: 'error',
            title: 'Oops...',
            dangerMode: true,
            text: 'Something went wrong! [Unknown Error]'
          });
        }
      }
    });
  });
});
// function disabled_all_form(status_approval, buttonType) {
//   if (buttonType == 'Edit') {
//     if (status_approval == '52') {
//       $("#travelForm input").attr('disabled',true);
//       $("#travelForm select").attr('disabled',true);
//       $("#travelForm textarea").attr('disabled',true);
//       $("#sample_table_transport input").attr('disabled',true);
//       $("#sample_table_transport select").attr('disabled',true);
//       $("#sample_table_akomodasi input").attr('disabled',true);
//       $("#sample_table_akomodasi select").attr('disabled',true);
//       $("#sample_table_cash_advance input").attr('disabled',true);
//       $("#sample_table_cash_advance select").attr('disabled',true);
//       $("#new_transport").css('display','none');
//       $("#new_accommodation").css('display','none');
//       $("#new_cash_advance").css('display','none');
//       $(".delete-record-trans").css('display','none');
//       $(".delete-record-akomo").css('display','none');
//       $(".delete-record").css('display','none');
//       $(".action").hide();
//     }else{
//       $("#travelForm input").attr('disabled',false);
//       $("#travelForm select").attr('disabled',false);
//       $("#travelForm textarea").attr('disabled',false);
//       $("#sample_table_transport input").attr('disabled',false);
//       $("#sample_table_transport select").attr('disabled',false);
//       $("#sample_table_akomodasi input").attr('disabled',false);
//       $("#sample_table_akomodasi select").attr('disabled',false);
//       $("#sample_table_cash_advance input").attr('disabled',false);
//       $("#sample_table_cash_advance select").attr('disabled',false);
//       $("#new_transport").css('display','block');
//       $("#new_accommodation").css('display','block');
//       $("#new_cash_advance").css('display','block');
//       $(".delete-record-trans").css('display','block');
//       $(".delete-record-akomo").css('display','block');
//       $(".delete-record").css('display','block');
//       $(".action").show();
//     }
//   }
// }
function get_edit(officialtravelID) {
  $.ajax({
    type: "GET",
    url: "{{url('cash_advance/official_travel/official_travel/getEdit')}}"+"/"+officialtravelID,
    data : {id_official_travel: officialtravelID},
    success: function(response) {
      if (response.data) {
        hide_loading();
        global_id_cash_advance = 0;
        global_id_transport = 0;
        global_id_akomodasi = 0;
        $.each(response.data, function(key, value) {
          buttonType = 'Edit';
          disabled_all_form(value.status_approval, buttonType);
          $("#start_end").attr('disabled',true);
          $("#letter_date").attr('disabled',true);
          $("#id_official_travel").val(value.id_official_travel);
          $("#letter_date").val(value.letter_date);
          $("#id_position_detail").val(value.id_position_detail);
          $("#id_reason_group").val(value.id_reason_group).trigger('change');
          $("#id_approval_status").val(value.id_approval_status).trigger('change');
          $("#location_from").val(value.location_from);
          $("#location_to").val(value.location_to);
          $("#reason_notes").val(value.reason_notes);
          var start_date = value.start_date.substr(0,10);
          var end_date = value.end_date.substr(0,10);
          var defaultStartDate = moment(value.start_date);
          var defaultEndDate = moment(value.end_date);
          $("#start_end").data('daterangepicker').setStartDate(defaultStartDate);
          $("#start_end").data('daterangepicker').setEndDate(defaultEndDate);
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
          $("#with_caseadvance").addClass('readonly-checkbox');
          $("#with_trans_accom").addClass('readonly-checkbox');
          if (value.is_have_cash_advance == null) {
            $("#with_trans_accom").attr('value','false');
            $("#with_caseadvance").prop('checked',false).trigger('change');
          }else{
            $("#with_trans_accom").attr('value','true');
            $("#with_caseadvance").prop('checked',true).trigger('change');
          }
        });
        // Cashadvance
        if (response.cashadvance) {
          $("#id_cashadvance_cash").val(response.cashadvance[0].id_cash_advance);
          $.each(response.cashadvance, function (i, item) {
            $('#new_cash_advance').trigger('click');
          });
          setTimeout(function () {
            $('#table_cash_advance tr').each(function (index) {
              // var notes_cashadvance = response.cashadvance[index].notes.replace(/;/g, '');
              var notes = response.cashadvance[index].notes.split(';');
              var notes_cashadvance = notes[0];
              var region_cashadvance = notes[1];
              var tanggal_cashadvance = response.cashadvance[index].description.replace(/;/g, '');
              const dateRangeCashadvance = tanggal_cashadvance.split(' to ');
              const startDate = new Date(dateRangeCashadvance[0]);
              const endDate = new Date(dateRangeCashadvance[1]);
              var defaultStartDate = moment(dateRangeCashadvance[0]);
              var defaultEndDate = moment(dateRangeCashadvance[1]);
              $(this).find('.tanggal_input').data('daterangepicker').setStartDate(defaultStartDate);
              $(this).find('.tanggal_input').data('daterangepicker').setEndDate(defaultEndDate);
              $(this).find('span.sn').html(index + 1);
              $(this).find('.delete-record').attr('more_id',response.cashadvance[index].id_expense_request);
              $(this).find('.id_cash_advance_input').val(response.cashadvance[index].id_expense_request);
              $(this).find('.nama_input').val(response.cashadvance[index].id_product).trigger('change');
              $(this).find('.notes_cashadvance_input').val(notes_cashadvance);
              $(this).find('.tanggal_input').val(tanggal_cashadvance);
              $(this).find('.region_cashadvance_input').val(region_cashadvance).trigger('change');
              var total_cashadvance = unformatRupiah(response.cashadvance[index].unit_price);
              $(this).find('.total_input').val(formatRupiah(total_cashadvance));
            });                       
          }, 500);
        }
        // Transport
        if (response.transport && response.akomodasi) {
          $("#id_cashadvance_trans_accom").val(response.transport[0].id_cash_advance);
          $("#with_trans_accom").attr('value','true');
          $("#with_trans_accom").prop('checked',true).trigger('change');
          $.each(response.transport, function (i, item_trans) {
            $('#new_transport').trigger('click');
          });
          setTimeout(function () {
            $('#table_transport tr').each(function (index) {
              var notes = response.transport[index].notes;
              var notes_trans = notes.split(';');
              var jenis_transportasi = notes_trans[0];
              var from = notes_trans[1];
              var to = notes_trans[2];
              var branch = notes_trans[3];
              // 
              var description = response.transport[index].description;
              var description_trans = description.split(';');
              var date_transport = description_trans[0];
              var time_transport = description_trans[1];
              $(this).find('span.sn').html(index + 1);
              // $(this).find('.date_transport_input').parent().children('span').children('button').attr('disabled',true);
              $(this).find('.delete-record-trans').attr('more_id',response.transport[index].id_expense_request);
              $(this).find('.id_transport_input').val(response.transport[index].id_expense_request);
              $(this).find('.jenis_transportasi_input').val(response.transport[index].id_product).trigger('change');
              $(this).find('.transport_name_input').val(jenis_transportasi);
              $(this).find('.from_input').val(from);
              $(this).find('.to_input').val(to);
              $(this).find('.branch_input').val(branch).trigger('change');
              $(this).find('.date_transport_input').val(date_transport);
              $(this).find('.time_transport_input').val(time_transport);
            });                       
          }, 500);
          // akomodasi
          $.each(response.akomodasi, function (i, item_accom) {
            $('#new_accommodation').trigger('click');
          });
          setTimeout(function () {
            $('#table_accommodation tr').each(function (index) {
              var notes_akomodasi = response.akomodasi[index].notes.replace(/;/g, '');
              var description_akomodasi = response.akomodasi[index].description.replace(/;/g, '');
              $(this).find('span.sn').html(index + 1);
              $(this).find('.delete-record-akomo').attr('more_id',response.akomodasi[index].id_expense_request);
              $(this).find('.id_akomodasi_input').val(response.akomodasi[index].id_expense_request);
              $(this).find('.product_akomodasi_input').val(response.akomodasi[index].id_product).trigger('change');
              $(this).find('.nama_hotel_input').val(notes_akomodasi);
              $(this).find('.start_end_akomodasi_input').val(description_akomodasi);
              const dateRangeAkomodasi = description_akomodasi.split(' to ');
              const startDate = new Date(dateRangeAkomodasi[0]);
              const endDate = new Date(dateRangeAkomodasi[1]);
              var defaultStartDate = moment(dateRangeAkomodasi[0]);
              var defaultEndDate = moment(dateRangeAkomodasi[1]);
              $(this).find('.start_end_akomodasi_input').data('daterangepicker').setStartDate(defaultStartDate);
              $(this).find('.start_end_akomodasi_input').data('daterangepicker').setEndDate(defaultEndDate);


              const timeDifference = endDate - startDate;
              const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
              $(this).find('.lama_menginap_input').val(daysDifference+' Days');
            });                       
          }, 500);
        }
      }
    },
    error: function(response) {
      get_edit(officialtravelID);
    }
  });
}
function popup_edit() {
  $("#travelForm")[0].reset();
  $("#with_trans_accom").prop('checked',false).trigger('change');
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
  urlAjax = "{{ route('edit.offtrave') }}";
  $("#modal_form_travel").modal('show');
}
$(document).ready(function() {
  $(document).on('click', '.btn-edit', function() {
    show_loading();
    var officialtravelID = $(this).attr('more_id');
    $(".modal-title").html('<i class="fas fa-edit"></i> Form Official Travel');
    $("#label_button_action").html('Update');
    $("#label_button_action_submit").html('Update & Submit');
    popup_edit();
    if (officialtravelID) {
      get_edit(officialtravelID);
    }
  });
});
// $(document).on('click', '.btn-cancel', function (event) {
//   officialtravelID = $(this).attr('more_id');
//   event.preventDefault();
//   swal({
//     title: 'Are you sure?',
//     text: 'This record and it`s details will be permanantly deleted!',
//     icon: 'warning',
//     buttons: true,
//     dangerMode: true,
//     confirmButtonColor: '#3085d6',
//     cancelButtonColor: '#d33',
//     confirmButtonText: 'Yes, delete it!'
//   }).then(function(value) {
//     if (value) {
//       $.ajax({
//         method: "GET",
//         url: "{{url('cash_advance/official_travel/official_travel/destroy/')}}"+"/"+officialtravelID,
//         // data : {id_official_travel: officialtravelID},
//         success:function(data)
//         {
//           setTimeout(function(){
//             swal({
//               title: "Data Deleted!",
//               icon: "success"
//             });
//             $('#officialtravel_table').DataTable().ajax.reload();         
//           }, 50);
//         }
//       })
//     }
//   });
// });
$(document).ready(function() {
  $(document).on('click', '.btn-view', function() {
    show_loading();
    var officialtravelID = $(this).attr('more_id');
    $(".modal-title").html('<i class="fas fa-view"></i> Official Travel');
    popup_edit();
    if (officialtravelID) {
      get_edit(officialtravelID);
    }
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
</script>
@endsection