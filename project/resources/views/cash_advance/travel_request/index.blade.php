@extends('adminlte::page')
@section('title', 'Travel Request')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Travel Request
        </h5>
        <div class="card-tools">
         <button type="button" id="new_transport" class="btn btn-primary btn-sm mb-3" style="float: right;display: none;">
           <i class="fa fa-plus"></i> Add Transport
         </button>
         <button type="button" id="new_accommodation" class="btn btn-primary btn-sm mb-3" style="float: right;display: none;">
           <i class="fa fa-plus"></i> Add Accommodation
         </button>
       </div>
     </div>
     <div class="card-body">
      <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
      <br>
      <br>
      <div id="loading">
        <span class="fa fa-spinner fa-spin fa-3x"></span>
      </div>
      <form method="post" id="travelForm" class="travelForm" enctype="multipart/form-data">
        @csrf
		<input type="hidden" name="id_travel" id="id_travel">
        <table class="table table-hover table-bordered table-striped" id="settlementtravel_table" style="width:1200px;">
          <thead>
            <tr>
              <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
              <th data-priority="2" style="width:120px;">Reference Number</th>
              <th style="width:100px;">Travel Type</th>
              <th data-priority="3" style="width:100px;">Request by</th>
              <th>Employee Region</th>
              <th style="width:100px;">Phone Number</th>
              <th data-priority="5" style="width:200px;">Destination</th>
              <th data-priority="6" style="width:100px;">Start Date</th>
              <th data-priority="7" style="width:100px;">End Date</th>
			  <th data-priority="4">Request Status</th>
              <th data-priority="8">Approval Status</th>
              <th data-priority="1" style="text-align: center;">Action</th>
            </tr>
          </thead>
        </table>
      </form>
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
        <div class="row" id="detailForm">
          <div class="col-md-6">
            <div class="row">
              <label class="col-sm-4 col-form-label">Travel Type <sup class="text-danger">*</sup></label>
              <div class="col-sm-8">
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
                <select id="id_position_detail" name="id_position_detail" class="form-control form-control-sm select_opsi" style="width: 100%;" readonly></select>
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
              <label class="col-sm-4 col-form-label">Approval Hierarchy <sup class="text-danger">*</sup></label>
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
                <select class="form-control form-control-sm select_opsi" id="id_approval_status" name="id_approval_status" style="width: 100%;" readonly></select>
                <span class="invalid-feedback" role="alert" id="id_approval_statusError">
                  <strong></strong>
                </span>
              </div>
            </div>
            <!-- div class="row">
              <label class="col-sm-4 col-form-label">With Transport</label>
              <div class="col-sm-1">
                <input type="checkbox" class="text mt-3" style="width: 20px;height: 20px;" name="with_trans" id="with_trans">
                <span class="invalid-feedback" role="alert" id="Error">
                  <strong></strong>
                </span>
              </div>
              <div class="col-sm-7">
                <span style="font-size: 14px;" class="text mt-3"><i>Note: Jika pengajuan maksimal H-3 dan dibelikan HR maka provide HR transport dicentang.</i></span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">With Accommodation</label>
              <div class="col-sm-1">
                <input type="checkbox" class="text mt-3" style="width: 20px;height: 20px;" name="with_accom" id="with_accom">
                <span class="invalid-feedback" role="alert" id="Error">
                  <strong></strong>
                </span>
              </div>
              <div class="col-sm-7">
                <span style="font-size: 14px;" class="text mt-3"><i>Note: Jika pengajuan maksimal H-3 dan dibelikan HR maka provide HR accommodation dicentang.</i></span>
              </div>
            </div>
            <div class="row">
              <label class="col-sm-4 col-form-label">With Case Advance</label>
              <div class="col-sm-8">
                <input type="checkbox" class="text mt-2" style="width: 20px;height: 20px;" value="true" name="with_caseadvance" id="with_caseadvance">
                <span class="invalid-feedback" role="alert" id="Error">
                  <strong></strong>
                </span>
              </div>
            </div -->
          </div>
        </div>
        <!-- table -->
      </div>
      <div class="modal-loading" id="modal-loading" style="display: none;">
       <span class="fa fa-spinner fa-spin fa-3x"></span>
     </div>
     <div class="modal-footer">
      <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>

<div class="row mt-5">
  <div style="display:none;">
    <table class="table table-hover table-bordered table-striped responsive-table" id="thead_transport">
     <thead>
       <tr>
        <th rowspan="2">No. </th>
        <th rowspan="2">Transport Type</th>
        <th rowspan="2">Transport Recommendation</th>
        <th rowspan="2">From</th>
        <th rowspan="2">To</th>
        <th rowspan="2">Branch Destination</th>
        <th colspan="2" style="text-align: center;">Departure</th>
        <th rowspan="2">Price</th>
        <th rowspan="2">Total</th>
        <th rowspan="2">Status</th>
        <th rowspan="2">Action</th>
      </tr>
      <tr>
        <th>Date</th>
        <th style="border-right: 1px solid #dee2e6;">Time</th>
      </tr>
    </thead>
    <tbody id="table_transport" class="table_transport" style="background:white;"></tbody>
  </table>
  <table class="table table-hover table-bordered table-striped responsive-table" id="thead_accommodation">
    <thead>
     <tr>
      <th>No. </th>
      <th>Category</th>
      <th>Name of Hotel/Kos</th>
      <th>City</th>
	  <th>Branch Destination</th>
      <th colspan="2">Check In and Check Out Date</th>
      <th>Length of stay</th>
      <th colspan="1">Price</th>
      <th colspan="1">Total</th>
      <th colspan="1">Status</th>
      <th colspan="1" style="text-align: center;">Action</th>
    </tr>
  </thead>
  <tbody id="table_accommodation" class="table_accommodation" style="background:white;">
  </tbody>
</table>
<table id="sample_table_transport" class="table table-striped table-hover">
  <tr id="">
    <td data-label="No."><span class="sn" style="vertical-align:middle;"></span></td>   
    <td data-label="Transport Type">
      <input autocomplete="off" hidden="" name="transport[0][id_transport]" id="transport_0_id_transport" class="form-control form-control-sm id_transport_input">
      <select name="transport[0][jenis_transportasi]" id="transport_0_jenis_transportasi" class="form-control form-control-sm jenis_transportasi_input" style="width: 100%;"></select>
      <span class="invalid-feedback d-block jenis_transportasi_input_error" role="alert" id="transport_0_jenis_transportasiError">
        <strong></strong>
      </span>
    </td>
    <td data-label="Transport Name Recommendation">
      <select name="transport[0][transport_name]" id="transport_0_transport_name" class="form-control form-control-sm transport_name_input" style="width: 100%;"></select>
      <span class="invalid-feedback transport_name_input_error" role="alert" id="transport_0_transport_nameError">
        <strong></strong>
      </span>
    </td>
    <td data-label="From">
      <div class="row">
        <div class="col-lg-12">
          <input name="transport[0][from]" autocomplete="off" id="transport_0_from" class="form-control form-control-sm from_input" style="width: 100%;">
          <span class="invalid-feedback from_input_error" role="alert" id="transport_0_fromError">
            <strong></strong>
          </span>
        </div>
      </div>
    </td>
	<td data-label="To">
      <div class="row">
        <div class="col-lg-12">
          <input name="transport[0][to]" autocomplete="off" style="width: 100%;" id="transport_0_to" class="form-control form-control-sm to_input">
          <span class="invalid-feedback to_input_error" role="alert" id="transport_0_toError">
            <strong></strong>
          </span>
        </div>
      </div>
    </td>
	<td data-label="Branch Destination">
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
        <input type="text" autocomplete="off" name="transport[0][date_transport]" id="transport_0_date_transport" class="form-control form-control-sm date_transport_input" style="width: 120px;">
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
    <td data-label="Price">
      <input type="text" autocomplete="off" name="transport[0][price_transport]" id="transport_0_price_transport" class="form-control form-control-sm price_transport_input">
      <span class="invalid-feedback price_transport_input_error" role="alert" id="transport_0_price_transportError">
        <strong></strong>
      </span>
    </td>
	<td data-label="Total">
      <input type="text" autocomplete="off" id="transport_0_total_transport" class="form-control form-control-sm total_transport_input" readonly>
      <span class="invalid-feedback total_transport_input_error" role="alert" id="transport_0_total_transportError">
        <strong></strong>
      </span>
    </td>
    <td data-label="Status">
      <span class="transport_status" id="transport_0_status" style="font-size:12px;"></span>
    </td>
    <td data-label="Action">
      <center>
        <input type="checkbox" id="transport_0_pilih_transport" style="width: 20px;height: 20px;" name="transport[0][pilih_transport]" class="pilih_transport_input">
        <!-- <button type="button" class="btn btn-sm btn-success edit-save" id="edit-transport" more_type="Transport" more_id="0" data-id="0"><i class="fas fa-save"></i></button> -->
      </center>
    </td>
  </tr>
</table>
<table id="sample_table_akomodasi"  class="table table-striped table-hover">
  <tr id="">
    <td data-label="No. "><span class="sn" style="vertical-align:middle;"></span></td>   
    <td data-label="Category">
      <input autocomplete="off" hidden="" name="akomodasi[0][id_akomodasi]" id="akomodasi_0_id_akomodasi" class="form-control form-control-sm id_akomodasi_input">
      <select name="akomodasi[0][product_akomodasi]" id="akomodasi_0_product_akomodasi" class="form-control form-control-sm product_akomodasi_input" style="width: 100%;"></select>
      <span class="invalid-feedback d-block product_akomodasi_input_error" role="alert" id="akomodasi_0_product_akomodasiError">
        <strong></strong>
      </span>
    </td>
    <td data-label="Name of Hotel/Kos">
      <input type="text" autocomplete="off" name="akomodasi[0][nama_hotel]" id="akomodasi_0_nama_hotel" class="form-control form-control-sm nama_hotel_input">
      <span class="invalid-feedback nama_hotel_input_error" role="alert" id="akomodasi_0_nama_hotelError">
        <strong></strong>
      </span>
    </td>
	<td data-label="City">
		<input name="akomodasi[0][city]" autocomplete="off" style="width: 100%;" id="akomodasi_0_city" class="form-control form-control-sm city_input">
		<span class="invalid-feedback city_input_error" role="alert" id="akomodasi_0_toError">
		  <strong></strong>
		</span>
    </td>
	<td data-label="Destination Branch">       
		<select name="akomodasi[0][branch]" id="akomodasi_0_branch" class="form-control form-control-sm ako_branch_input" style="width: 100%;"></select>
		<span class="invalid-feedback d-block ako_branch_input_error" role="alert" id="akomodasi_0_branchError">
		  <strong></strong>
		</span>
      </td>
    <td data-label="Check In and Check Out Date" colspan="2">
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
    <td data-label="Price" colspan="1">
      <input type="text" autocomplete="off" name="akomodasi[0][price_akomodasi]" id="akomodasi_0_price_akomodasi" class="form-control form-control-sm price_akomodasi_input">
      <span class="invalid-feedback price_akomodasi_input_error" role="alert" id="akomodasi_0_price_akomodasiError">
        <strong></strong>
      </span>
    </td>
	<td data-label="Total" colspan="1">
      <input type="text" autocomplete="off" id="akomodasi_0_total_akomodasi" class="form-control form-control-sm total_akomodasi_input" readonly>
      <span class="invalid-feedback total_akomodasi_input_error" role="alert" id="akomodasi_0_total_akomodasiError">
        <strong></strong>
      </span>
    </td>
    <td data-label="Status" colspan="1">
      <span class="accomodation_status" id="accomodation_0_status" style="font-size:12px;"></span>
    </td>
    <td data-label="Action" colspan="1" align="center">
      <center>
        <input type="checkbox" id="akomodasi_0_pilih_akomodasi" style="width: 20px;height: 20px;" name="akomodasi[0][pilih_akomodasi]" class="pilih_akomodasi_input">
        <!-- <button type="button" class="edit-save btn btn-sm btn-success" id="edit-akomodasi" more_type="Akomodasi" more_id="0" data-id="0"><i class="fas fa-save"></i></button> -->
      </center>
    </td>
  </tr>
</table>
</div>
</div>
<!-- End Form modal -->
@endsection
@section('css')
<style type="text/css">
  #loading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    text-align: center;
    padding-top: 20%;
  }
/*  #loading img {
    width: 50px;
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
	td.text-center{
		text-align:center;
	}
	th.z-index{
		z-index:999;
	}
  </style>
  @endsection

  @section('scripts')
  <script type="text/javascript">
    moment.updateLocale('id', {
      weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
    });
    function TanggalIndonesia(string) {
      var formattedDate = moment(string).format('dddd, DD/MM/YYYY');
      return formattedDate;
    }
    function formatRupiah(amount) {
      if (!amount) {
        return 0;
      }
      return amount.toLocaleString('id-ID');
    }
    function unformatRupiah(rupiah) {
      if (!rupiah) {
        return 0;
      }
      return parseInt(rupiah.replace(/,|\./g, ''));
    }
    $(function () {
      var table = $('#settlementtravel_table').DataTable({
        processing: true,
        pageLength: 50,
        scrollX: true,
		scrollCollapse: true,
		fixedColumns: {
			left: 2,
			right: 3,
		},
      // scrollCollapse: true,
      // serverSide: true,
      // order: [[1, 'DESC']],
      columnDefs: [
      {
        orderable: false,
        targets: 0
      }
      ],
      ajax: {
        url: "{{ route('index.travreq') }}",
        error: function (jqXHR, textStatus, errorThrown) {
          $('#settlementtravel_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        orderable: false,
        data: 'id_official_travel',
		className: 'z-index',
		createdCell:  function (td, cellData, rowData, row, col) {
           $(td).addClass('dt-control'); 
        },
        render: function (td, data, type, row) {
			var expand = '<i ex-id = '+data+' class="fa fa-chevron-right"></i>';
          return  expand;
        }
      },
      { data: 'reference_number', name: 'reference_number', className: 'z-index'},
      { data: 'travel_type', name: 'travel_type' },
      { data: 'name', name: 'name' },
      { data: 'emp_region', name: 'emp_region' },
      { data: 'mobile_phone', name: 'mobile_phone' },
      { data: 'location_to', name: 'location_to' },
      {
        data: 'start_date',
        name: 'start_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
        //    var startDate = new Date(data);
            return TanggalIndonesia(data);
          }
          return data;
        }
      },
      {
        data: 'end_date',
        name: 'end_date',
        render: function (data, type, full, meta) {
          if (type === 'display' || type === 'filter') {
        //    var startDate = new Date(data);
            return TanggalIndonesia(data);
          }
          return data;
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
      { 
        data: 'dec_approval_status', 
        name: 'dec_approval_status', 
		className: 'text-center',
        render: function (data, type, row) {
          if (data == 'Approved') {
            return '<span class="badge bg-success text-white" style="font-size: 12px;">'+data+'</span>';
          }else if(data == 'Cancelled'){
            return '<span class="badge bg-danger text-white" style="font-size: 12px;">'+data+'</span>';
          }else{
            return '<b>'+data+'</b>';
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
	
      let global_product_transport = [];
      let global_product_akomodasi = [];
      let global_branch_transport = [];
	  
      function get_data(){
        $.ajax({
          method: "GET",
          url : "{{url('cash_advance/official_travel/official_travel/get_data')}}",
          success: function (response) {
            global_product_transport = response.product_transport;
            global_product_akomodasi = response.product_akomodasi;
            global_branch_transport = response.branch_transport;
          },
          error: function(response) {
            get_data();
          }
        });  
      }
	  
	  
    $(document).ready(function() {
        get_data();
      });
      let global_id_transport = 0;
      let global_id_akomodasi = 0;
      let id_official_travel = 0;	 
      $(document).on('click','.btn-save',function(e) {		  
        e.preventDefault();
		let more_id = parseInt($(this).attr('more_id'));
		$("#id_travel").val(more_id);
		$(".travelForm").attr('id','travelForm-'+more_id);
        var formData = $("#travelForm-"+more_id).serializeArray();
        $(".invalid-feedback").children("strong").text("");
        $("#travelForm-"+id_official_travel+" input").removeClass("is-invalid");
        $("#travelForm-"+id_official_travel+" textarea").removeClass("is-invalid");
        $("#travelForm-"+id_official_travel+" select").removeClass("is-invalid");
        if (more_id) {
          $("#btn-save-"+more_id).html('<div class="spinner-border spinner-border-sm" role="status"></div>');
          $.ajax({
            method: "POST",
            url: "{{ route('edit.settravel') }}",
            headers: {
              Accept: "application/json",
			  'X-CSRF-TOKEN': "{{ csrf_token() }}",
            },
            data: formData,
            success: function (response) {
              $("#btn-save-"+more_id).html('<i class="fas fa-save"></i>');
              if (response.status == 'true') {
                swal({
                  icon: 'success',
                  title: 'Success',
                  text: response.message
                });
            //    $('#settlementtravel_table').DataTable().ajax.reload();
              }
              else {
               swal({
                icon: 'error',
                title: 'Oops...',
                dangerMode: true,
                text: 'Something went wrong!! ['+response.message+']'
              });
             }
           },
           error: function (response) {
            $("#btn-save-"+more_id).html('<i class="fas fa-save"></i>');
            if (response.status === 422) {
              let errors = response.responseJSON.errors;
              Object.keys(errors).forEach(function (key) {
               var key_temp = key.replaceAll(".", "_");
               $("#" + key_temp).addClass("is-invalid");
               $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
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
        }
      });
      $(document).on('click', '#new_transport', function () {
        var content = jQuery("#sample_table_transport tr"),
        size = global_id_transport++,
        element = null,
        element = content.clone();

        let maxPrice = "";
        let days = 1;
        var code_product = "";
        var id_region = "";
        var codeJenisTransport = "";
        // element.find('.edit-save').attr('more_id',size);
        element.find('.pilih_transport_input').attr('id', 'transport_' + size + '_pilih_transport');
        element.find('.pilih_transport_input').attr('name', 'transport[' + size + '][pilih_transport]');

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
              $("#loading").show();
              $.ajax({
                method: "GET",
                url: "{{route('change_type_transport')}}",
                data : {code: codeJenisTransport},
                success:function(response)
                {
                  element.find('.transport_name_input').select2({
                    placeholder: "Select Transport Name ...",
                    data: response.map(function(item) {
                      return {id: item.variant, text: item.variant}
                    })
                  });
                },
                error: function(response) {
                  $("#loading").hide();
                  element.find('.jenis_transportasi_input').val(null).trigger('change');
                }
              });
            }
          }else{
            element.find('.transport_name_input').empty();
          }
        }).val(null).trigger('change');

        element.find('.transport_name_input').attr('id', 'transport_' + size + '_transport_name');
        element.find('.transport_name_input').attr('name', 'transport[' + size + '][transport_name]');
        element.find('.transport_name_input_error').attr('id', 'transport_' + size + '_transport_nameError');
        element.find('.transport_name_input').select2({
          placeholder: "Select Transport Name ..."
        });
        element.find('.transport_name_input').val(null).trigger('change');
        // element.find('.transport_name_input').on('keyup',function() {
        //   $(this).val($(this).val().toUpperCase());
        // });

        element.find('.from_input').attr('id', 'transport_' + size + '_from');
        element.find('.from_input').attr('name', 'transport[' + size + '][from]');
        element.find('.from_input_error').attr('id', 'transport_' + size + '_fromError');
        element.find('.from_input').on('keyup',function() {
          $(this).val($(this).val().toUpperCase());
        });
        element.find('.to_input').attr('id', 'transport_' + size + '_to');
        element.find('.to_input').attr('name', 'transport[' + size + '][to]');
        element.find('.to_input_error').attr('id', 'transport_' + size + '_toError');
        element.find('.to_input').on('keyup',function() {
          $(this).val($(this).val().toUpperCase());
        });
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
        if (start_date != null) {
          const dateHeader = start_date.split(" to ");
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

        element.find('.price_transport_input').attr('id', 'transport_' + size + '_price_transport');
        element.find('.price_transport_input').attr('name', 'transport[' + size + '][price_transport]');
        element.find('.price_transport_input_error').attr('id', 'transport_' + size + '_price_transportError');
        element.find('.price_transport_input').on('input', function() {
          var rupiah = this.value;
          var numberTotal = unformatRupiah(rupiah);
          this.value = formatRupiah(numberTotal);
		  $('#transport_'+ size +'_total_transport').val(this.value);
        });
		
		element.find('.total_transport_input').attr('id', 'transport_' + size + '_total_transport');
    //    element.find('.total_transport_input').attr('name', 'transport[' + size + '][total_transport]');

        element.find('.transport_status').attr('id', 'transport_' + size + '_status');

        element.appendTo('#table_transport_'+id_official_travel);
        $('#table_transport_'+id_official_travel+' tr').each(function (index) {
          $(this).find('span.sn').html(index + 1+'.');
        });
      });
    // Akomodasi
    $(document).on('click', '#new_accommodation', function () {
      var content = jQuery("#sample_table_akomodasi tr"),
      size = global_id_akomodasi++,
      element = null,
      element = content.clone();

      // element.find('.edit-save').attr('more_id',size);

      element.find('.pilih_akomodasi_input').attr('id', 'akomodasi_' + size + '_pilih_akomodasi');
      element.find('.pilih_akomodasi_input').attr('name', 'akomodasi[' + size + '][pilih_akomodasi]');

      element.find('.id_akomodasi_input').attr('id', 'akomodasi_' + size + '_id_akomodasi');
      element.find('.id_akomodasi_input').attr('name', 'akomodasi[' + size + '][id_akomodasi]');
      element.find('.id_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_id_akomodasiError');

      element.find('.product_akomodasi_input').attr('id', 'akomodasi_' + size + '_product_akomodasi');
      element.find('.product_akomodasi_input').attr('name', 'akomodasi[' + size + '][product_akomodasi]');
      element.find('.product_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_product_akomodasiError');
      element.find('.product_akomodasi_input').select2({
        // allowClear: true,
        placeholder: 'Select Product ....',
        data: global_product_akomodasi.map(function(item) {
          return {id: item.id, text: item.text, id_uom: item.id_uom, dec_uom: item.dec_uom}
        })
      }).on('change',function(e) {
      }).val(null).trigger('change');
      element.find('.product_akomodasi_input').val(null).trigger('change');

      element.find('.accomodation_status').attr('id', 'accomodation_' + size + '_status');

      element.find('.nama_hotel_input').attr('id', 'akomodasi_' + size + '_nama_hotel');
      element.find('.nama_hotel_input').attr('name', 'akomodasi[' + size + '][nama_hotel]');
      element.find('.nama_hotel_input_error').attr('id', 'akomodasi_' + size + '_nama_hotelError');
      element.find('.nama_hotel_input').on('keyup',function() {
        $(this).val($(this).val().toUpperCase());
      });
	  
	element.find('.city_input').attr('id', 'akomodasi_' + size + '_city');
	element.find('.city_input').attr('name', 'akomodasi[' + size + '][city]');
	element.find('.city_input_error').attr('id', 'akomodasi_' + size + '_cityError');
	element.find('.city_input').on('keyup',function() {
	  $(this).val($(this).val().toUpperCase());
	});
	
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
      if (start_date != null) {
        const dateHeader = start_date.split(" to ");
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
      element.find('.lama_menginap_input').attr('readonly', true)

      element.find('.price_akomodasi_input').attr('id', 'akomodasi_' + size + '_price_akomodasi');
      element.find('.price_akomodasi_input').attr('name', 'akomodasi[' + size + '][price_akomodasi]');
      element.find('.price_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_price_akomodasiError');
      element.find('.price_akomodasi_input').on('input', function() {
        var rupiah = this.value;
        var numberTotal = unformatRupiah(rupiah);
        this.value = formatRupiah(numberTotal);
		var val_inap = $('#akomodasi_'+ size +'_lama_menginap').val();
		var split_inap = val_inap.split(' ');
		$('#akomodasi_'+ size +'_total_akomodasi').val(formatRupiah(numberTotal*split_inap[0]));
      });
	  
	  element.find('.total_akomodasi_input').attr('id', 'akomodasi_' + size + '_total_akomodasi');
    //  element.find('.total_akomodasi_input').attr('name', 'akomodasi[' + size + '][total_akomodasi]');
	  
      element.appendTo('#table_accommodation_'+id_official_travel);
      $('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
        $(this).find('span.sn').html(index + 1+'.');
      });
    });
function transportDetail(d) {
  var body_transport = '';
  var headerTransport = jQuery("#thead_transport"),
  element_transport = headerTransport.clone();
  var size = d[0].id_official_travel;
  element_transport.find('.table_transport').attr('id', 'table_transport_'+size);
  // $.each(d, function (i, item) {
  //   $('#new_transport').trigger('click');
  // });
  body_transport += element_transport.html();
  
  return body_transport;
}
function akomodasiDetail(d) {
  var body_akomodasi = '';
  var headerAkomodasi = jQuery("#thead_accommodation"),
  element_akomodasi = headerAkomodasi.clone();
  var size = d[0].id_official_travel;
  element_akomodasi.find('.table_accommodation').attr('id', 'table_accommodation_'+size);
  // $.each(d, function (i, item_akomo) {
  //   $('#new_accommodation').trigger('click');
  // });
  body_akomodasi += element_akomodasi.html();
  
  return body_akomodasi;
// return body_transport + body_akomodasi;
}

function combineDetail(d) {
  var body_transport = '';
  var headerTransport = jQuery("#thead_transport"),
  element_transport = headerTransport.clone();
  var size = d[0].id_official_travel;
  element_transport.find('.table_transport').attr('id', 'table_transport_'+size);
 
  body_transport += element_transport.html();
  
  var body_akomodasi = '';
  var headerAkomodasi = jQuery("#thead_accommodation"),
  element_akomodasi = headerAkomodasi.clone();
  var size = d[0].id_official_travel;
  element_akomodasi.find('.table_accommodation').attr('id', 'table_accommodation_'+size);
  
  body_akomodasi += element_akomodasi.html();
  
  return body_transport + body_akomodasi;
}

function getViewDetail(id_official_travel, row) {
  $.ajax({
    url: "{{url('official_travel/travels/transport_and_accommodation/detail_view')}}"+"/"+id_official_travel,
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      $("#btn-save-"+id_official_travel).attr('disabled',false);
      $("#loading").hide();
      // if (response.transport) {
        $(".travelForm").attr('id','travelForm-'+id_official_travel);
		
		if(response.header == 'Cancel'){
			setTimeout(function() {
				const formElements = [
					"#travelForm-"+id_official_travel+" input",
					"#travelForm-"+id_official_travel+" select",
					"#travelForm-"+id_official_travel+" textarea",
				];
				formElements.forEach(element => $(element).attr('readonly',true));	
				
				if(response.transport.length > 0){
					$('#table_transport_'+id_official_travel+ ' tr').each(function (index) {
						$(this).find('.pilih_transport_input').attr('readonly', false);
						$(this).find('.price_transport_input').attr('readonly', false);
						$(this).find('.time_transport_input').parent().children('span').children('button').attr('disabled', true);
						$(this).find('.time_transport_input').parent().children('span').children('button').css('background', '#e9ecef');
					});
				}
				if(response.akomodasi.length > 0){
					$('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
						$(this).find('.pilih_akomodasi_input').attr('readonly', false);
						$(this).find('.price_akomodasi_input').attr('readonly', false);
					});
				}
			}, 3000);
		}
		if(response.transport.length > 0 && response.akomodasi.length > 0){
			
			start_date = response.transport[0].start_date.substr(0,10)+' to '+response.transport[0].end_date.substr(0,10);
			row.child(combineDetail(response.akomodasi)).show();
		    $.each(response.akomodasi, function (i, item_akomo) {
			  $('#new_accommodation').trigger('click');
			});
			$.each(response.transport, function (i, item) {
			  $('#new_transport').trigger('click');
			});
			setTimeout(function () {
			  $('#table_transport_'+id_official_travel+ ' tr').each(function (index) {
				var notes = response.transport[index].notes;
				var notes_trans = notes.split(';');
				var jenis_transportasi = notes_trans[0];
				var from = notes_trans[1];
				var to = notes_trans[2];
			//    var branch = notes_trans[3];
				var description = response.transport[index].description;
				var description_trans = description.split(';');
				var date_transport = description_trans[0];
				var time_transport = description_trans[1];
				$(this).find('.id_transport_input').val(response.transport[index].id_expense_request);
				$(this).find('.jenis_transportasi_input').val(response.transport[index].id_product).trigger('change');
				// $(this).find('.transport_name_input').val(jenis_transportasi);
				$(this).find('.from_input').val(from);
				$(this).find('.to_input').val(to);
				$(this).find('.branch_input').val(response.transport[index].branch).trigger('change');
				$(this).find('.date_transport_input').val(date_transport);
				$(this).find('.time_transport_input').val(time_transport);
        
        if(response.transport[index].status == "A") {
          $(this).find('.transport_status').text("Active");
          $(this).find('.transport_status').attr('class', 'badge bg-success text-white');
        } else {
          $(this).find('.transport_status').text("Inactive");
          $(this).find('.transport_status').attr('class', 'badge bg-danger text-white');
        }

				var price_transport = unformatRupiah(response.transport[index].unit_price);
				var total_transport = unformatRupiah(response.transport[index].total_amount);
				$(this).find('.price_transport_input').val(formatRupiah(price_transport));
				$(this).find('.total_transport_input').val(formatRupiah(total_transport));
				if (response.transport[index].is_verified == true) {
				  $(this).find('.pilih_transport_input').prop('checked',true);
				}else{
				  $(this).find('.pilih_transport_input').prop('checked',false);
				}
				setTimeout(function() {
				  $("#loading").hide();
				  $('#transport_'+index+'_transport_name').val(jenis_transportasi).trigger('change');
				}, 4000);
			  });                       
			}, 500);
			
			
			setTimeout(function () {
			  $('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
			//    var notes_akomodasi = response.akomodasi[index].notes.replace(/;/g, '');
				var notes = response.akomodasi[index].notes;
				var notes_ako = notes.split(';');
				var notes_akomodasi = notes_ako[0];
				var city = notes_ako[1];
				  
				var description_akomodasi = response.akomodasi[index].description.replace(/;/g, '');
				$(this).find('.id_akomodasi_input').val(response.akomodasi[index].id_expense_request);
				$(this).find('.product_akomodasi_input').val(response.akomodasi[index].id_product).trigger('change');
				$(this).find('.nama_hotel_input').val(notes_akomodasi);
				$(this).find('.city_input').val(city);
				$(this).find('.ako_branch_input').val(response.akomodasi[index].branch).trigger('change');
				$(this).find('.start_end_akomodasi_input').val(description_akomodasi);

        if(response.akomodasi[index].status == "A") {
          $(this).find('.accomodation_status').text("Active");
          $(this).find('.accomodation_status').attr('class', 'badge bg-success text-white');
        } else {
          $(this).find('.accomodation_status').text("Inactive");
          $(this).find('.accomodation_status').attr('class', 'badge bg-danger text-white');
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
				var price_akomodasi = unformatRupiah(response.akomodasi[index].unit_price);
				var total_akomodasi = unformatRupiah(response.akomodasi[index].total_amount);
				$(this).find('.price_akomodasi_input').val(formatRupiah(price_akomodasi));
				$(this).find('.total_akomodasi_input').val(formatRupiah(total_akomodasi));
				if (response.akomodasi[index].is_verified == true) {
				  $(this).find('.pilih_akomodasi_input').prop('checked',true);
				}else{
				  $(this).find('.pilih_akomodasi_input').prop('checked',false);
				}
			  });  
			}, 500);
			
			
		}
		else if(response.transport.length > 0){
			start_date = response.transport[0].start_date.substr(0,10)+' to '+response.transport[0].end_date.substr(0,10);

			row.child(transportDetail(response.transport)).show();
			$.each(response.transport, function (i, item) {
			  $('#new_transport').trigger('click');
			});
			setTimeout(function () {
			  $('#table_transport_'+id_official_travel+ ' tr').each(function (index) {
				var notes = response.transport[index].notes;
				var notes_trans = notes.split(';');
				var jenis_transportasi = notes_trans[0];
				var from = notes_trans[1];
				var to = notes_trans[2];
			//    var branch = notes_trans[3];
				var description = response.transport[index].description;
				var description_trans = description.split(';');
				var date_transport = description_trans[0];
				var time_transport = description_trans[1];
				$(this).find('.id_transport_input').val(response.transport[index].id_expense_request);
				$(this).find('.jenis_transportasi_input').val(response.transport[index].id_product).trigger('change');
				// $(this).find('.transport_name_input').val(jenis_transportasi);
				$(this).find('.from_input').val(from);
				$(this).find('.to_input').val(to);
				$(this).find('.branch_input').val(response.transport[index].branch).trigger('change');
				$(this).find('.date_transport_input').val(date_transport);
				$(this).find('.time_transport_input').val(time_transport);
				var price_transport = unformatRupiah(response.transport[index].unit_price);
				var total_transport = unformatRupiah(response.transport[index].total_amount);
				$(this).find('.price_transport_input').val(formatRupiah(price_transport));
				$(this).find('.total_transport_input').val(formatRupiah(total_transport));
				if (response.transport[index].is_verified == true) {
				  $(this).find('.pilih_transport_input').prop('checked',true);
				}else{
				  $(this).find('.pilih_transport_input').prop('checked',false);
				}
				setTimeout(function() {
				  $("#loading").hide();
				  $('#transport_'+index+'_transport_name').val(jenis_transportasi).trigger('change');
				}, 4000);
				
				if(response.transport[index].status == "A") {
				  $(this).find('.transport_status').text("Active");
				  $(this).find('.transport_status').attr('class', 'badge bg-success text-white');
				} else {
				  $(this).find('.transport_status').text("Inactive");
				  $(this).find('.transport_status').attr('class', 'badge bg-danger text-white');
				}
				
			});                       
			}, 500);
		}
		else if(response.akomodasi.length > 0){
			start_date = response.akomodasi[0].start_date.substr(0,10)+' to '+response.akomodasi[0].end_date.substr(0,10);
			row.child(akomodasiDetail(response.akomodasi)).show();
		    $.each(response.akomodasi, function (i, item_akomo) {
			  $('#new_accommodation').trigger('click');
			});
			setTimeout(function () {
			  $('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
			//    var notes_akomodasi = response.akomodasi[index].notes.replace(/;/g, '');
				var notes = response.akomodasi[index].notes;
				var notes_ako = notes.split(';');
				var notes_akomodasi = notes_ako[0];
				var city = notes_ako[1];
				  
				var description_akomodasi = response.akomodasi[index].description.replace(/;/g, '');
				$(this).find('.id_akomodasi_input').val(response.akomodasi[index].id_expense_request);
				$(this).find('.product_akomodasi_input').val(response.akomodasi[index].id_product).trigger('change');
				$(this).find('.nama_hotel_input').val(notes_akomodasi);
				$(this).find('.city_input').val(city);
				$(this).find('.ako_branch_input').val(response.akomodasi[index].branch).trigger('change');
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
				$(this).find('.lama_menginap_input').val(daysDifference+' Night(s)');
				var price_akomodasi = unformatRupiah(response.akomodasi[index].unit_price);
				var total_akomodasi = unformatRupiah(response.akomodasi[index].total_amount);
				$(this).find('.price_akomodasi_input').val(formatRupiah(price_akomodasi));
				$(this).find('.total_akomodasi_input').val(formatRupiah(total_akomodasi));
				if (response.akomodasi[index].is_verified == true) {
				  $(this).find('.pilih_akomodasi_input').prop('checked',true);
				}else{
				  $(this).find('.pilih_akomodasi_input').prop('checked',false);
				}
				
				if(response.akomodasi[index].status == "A") {
				  $(this).find('.accomodation_status').text("Active");
				  $(this).find('.accomodation_status').attr('class', 'badge bg-success text-white');
				} else {
				  $(this).find('.accomodation_status').text("Inactive");
				  $(this).find('.accomodation_status').attr('class', 'badge bg-danger text-white');
				}
			  });  
			}, 500);                     
		   }
		
			
		},
    error: function (error) {
      getViewDetail(id_official_travel, row);
    }
  });
}
var detailRows = [];
var start_date;
$('#settlementtravel_table tbody').css('background','#879ab3')
$('#settlementtravel_table tbody').on('click', 'tr td.dt-control', function (event) {
  event.preventDefault();
  var tr = $(this).closest('tr');
  var row = table.row(tr);
  var idx = detailRows.indexOf(tr.attr('id'));
  id_official_travel = row.data().id_official_travel;
  if (row.child.isShown()) {
    tr.removeClass('details');
    row.child.hide();
//    $("#travelForm-"+id_official_travel).removeAttr('id');
    $(this).html('<i class="fa fa-chevron-right"></i>');
    $("#btn-save-"+id_official_travel).attr('disabled',true);
    detailRows.splice(idx, 1);
  }else {
    $("#loading").show();
    tr.addClass('details');
//	$(".btn-save").attr('disabled',true);
    getViewDetail(id_official_travel, row);
    detailRows.push(tr.attr('id'));
    $(this).html('<i class="fa fa-chevron-down"></i>');
  }
});
/*
	table.on('draw', function () {
	$('tr td.dt-control').trigger('click');	
	  detailRows.forEach(function (id, i) {
		$('#' + id + ' td.dt-control').trigger('click');
	  });	
	});
*/
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
var urlAjax = "";
// $(document).on('click', '.edit-save', function(e) {
//   e.preventDefault();
//   $(this).removeAttr('id');
//   var more_id = jQuery(this).attr('more_id');
//   var more_type = jQuery(this).attr('more_type');
//   $(".invalid-feedback").children("strong").text("");
//   urlAjax = "{{ route('edit.settravel') }}";
//   $(this).attr('id','edit-'+more_type+'-'+more_id);
//   if (more_id) {
//     $("#edit-"+more_type+'-'+more_id).html('<div class="spinner-border spinner-border-sm" role="status"></div>');
//     var formData;
//     if (more_type == 'Transport') {
//       formData = {
//         '_token': $('meta[name="csrf-token"]').attr('content'),
//         'transport': more_id,
//       };
//       var transportGroup = ['transport_'+more_id+'_id_transport','transport_'+more_id+'_jenis_transportasi', 'transport_'+more_id+'_transport_name', 'transport_'+more_id+'_from', 'transport_'+more_id+'_to', 'transport_'+more_id+'_branch', 'transport_'+more_id+'_date_transport', 'transport_'+more_id+'_time_transport','transport_'+more_id+'_price_transport'];
//       for (var i = 0; i < transportGroup.length; i++) {
//         var valueTransport = transportGroup[i];
//         formData[valueTransport] = $('#'+valueTransport).val();
//         $("#"+valueTransport).removeClass('is-invalid');
//       }
//     }else{
//       formData = {
//         '_token': $('meta[name="csrf-token"]').attr('content'),
//         'akomodasi': more_id,
//       };
//       var akomodasiGroup = ['akomodasi_'+more_id+'_id_akomodasi','akomodasi_'+more_id+'_product_akomodasi', 'akomodasi_'+more_id+'_nama_hotel', 'akomodasi_'+more_id+'_start_end_akomodasi', 'akomodasi_'+more_id+'_lama_menginap', 'akomodasi_'+more_id+'_uom_akomodasi', 'akomodasi_'+more_id+'_price_akomodasi'];
//       for (var i = 0; i < akomodasiGroup.length; i++) {
//         var valueAkomodasi = akomodasiGroup[i];
//         formData[valueAkomodasi] = $('#'+valueAkomodasi).val();
//         $("#"+valueAkomodasi).removeClass('is-invalid');
//       }
//     }
//     $.ajax({
//       method: "POST",
//       url: urlAjax,
//       headers: {
//         Accept: "application/json"
//       },
//       data: formData,
//       success: function (response) {
//         $("#edit-"+more_type+'-'+more_id).html('<i class="fas fa-save"></i>');
//         if (response.status == 'true') {
//           swal({
//             icon: 'success',
//             title: 'Success',
//             text: response.message
//           });
//         }
//         else {
//          swal({
//           icon: 'error',
//           title: 'Oops...',
//           dangerMode: true,
//           text: 'Something went wrong!! ['+response.message+']'
//         });
//        }
//      },
//      error: function (response) {
//       $("#edit-"+more_type+'-'+more_id).html('<i class="fas fa-save"></i>');
//       if (response.status === 422) {
//         let errors = response.responseJSON.errors;
//         Object.keys(errors).forEach(function (key) {
//           $("#" + key).addClass("is-invalid");
//           $("#" + key + "Error").children("strong").text(errors[key][0]);
//         });
//       }
//       else {
//        swal({
//         icon: 'error',
//         title: 'Oops...',
//         dangerMode: true,
//         text: 'Something went wrong! ['+response.message+']'
//       });
//      }
//    }
//  });
//   }
// });
function get_edit(officialtravelID) {
 const formElements = [
		"#detailForm input",
		"#detailForm select",
		"#detailForm textarea",
    ];
	formElements.forEach(element => $(element).attr('disabled', true));	
  $.ajax({
    type: "GET",
    url: "{{url('official_travel/travels/transport_and_accommodation/view')}}"+"/"+officialtravelID,
    success: function(response) {
      if (response) {
        hide_loading();
        $("#id_position_detail").select2({
          data: response.map(function(item) {
            return {id: item.dec_position, text: item.dec_position};
          })
        });
        $("#id_reason_group").select2({
          data: response.map(function(item) {
            return {id: item.dec_category, text: item.dec_category};
          })
        });
        $("#request_by").select2({
          data: response.map(function(item) {
            return {id: item.name, text: item.name};
          })
        });
        $("#id_region").select2({
          data: response.map(function(item) {
            return {id: item.dec_region, text: item.dec_region};
          })
        });
        $("#id_branch").select2({
          data: response.map(function(item) {
            return {id: item.dec_branch, text: item.dec_branch};
          })
        });
        $("#id_division").select2({
          data: response.map(function(item) {
            return {id: item.dec_division, text: item.dec_division};
          })
        });
        $("#id_job_grade").select2({
          data: response.map(function(item) {
            return {id: item.dec_job_grade, text: item.dec_job_grade};
          })
        });
        $("#id_approval").select2({
          data: response.map(function(item) {
            return {id: item.dec_approval_hirarki, text: item.dec_approval_hirarki};
          })
        });
        $("#id_approval_request").select2({
          data: response.map(function(item) {
            return {id: item.name_chief, text: item.name_chief};
          })
        });
        $("#id_approval_status").select2({
          data: response.map(function(item) {
            return {id: item.dec_approval_status, text: item.dec_approval_status};
          })
        });
        $.each(response, function(key, value) {
          $("#start_end").attr('disabled',true);
          $("#letter_date").attr('disabled',true);
          $("#letter_date").val(value.letter_date);
          $("#location_to").val(value.location_to);
          $("#reason_notes").val(value.reason_notes);
          var start_date = value.start_date.substr(0,10);
          var end_date = value.end_date.substr(0,10);
          $("#start_end").val(start_date+' to '+end_date);
          if (value.unlock_gps == true) {
            $("#unlock_gps").prop('checked',true).trigger('change');
          }else{
            $("#unlock_gps").prop('checked',false).trigger('change');
          }
          $("#with_caseadvance").attr('disabled',true);
          $("#with_trans").attr('disabled',true);
          $("#with_accom").attr('disabled',true);
          if (value.is_have_cash_advance == false) {
            $("#with_caseadvance").prop('checked',false).trigger('change');
          }else{
            $("#with_caseadvance").prop('checked',true).trigger('change');
          }
        });
        if (response[0].dec_product_category) {
          $("#with_trans").prop('checked',true).trigger('change');
        }else{
          $("#with_trans").prop('checked',false).trigger('change');
        }
        if (response[1].dec_product_category) {
          $("#with_accom").prop('checked',true).trigger('change');
        }else{
          $("#with_accom").prop('checked',false).trigger('change');
        }
      }
    },
    error: function(response) {
      get_edit(officialtravelID);
    }
  });
}
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
function popup_edit() {
//  $("#travelForm")[0].reset();
  $("#travelForm input").attr('disabled',true);
  $("#travelForm select").attr('readonly','readonly');
  $("#travelForm textarea").attr('disabled',true);
  $("#with_trans").prop('checked',false).trigger('change');
  $("#with_accom").prop('checked',false).trigger('change');
  $("#id_reason_group").val(null).trigger('change');
  $("#modal_form_travel").modal('show');
}
$(document).ready(function() {
  $(document).on('click', '.btn-view', function() {
    show_loading();
    var officialtravelID = $(this).attr('more_id');
    $(".modal-title").html('Form Official Travel');
    popup_edit();
    if (officialtravelID) {
      get_edit(officialtravelID);
    }
  });
});
function submitHrLock(officialtravelID) {
  $.ajax({
    method: "GET",
    url: "{{url('official_travel/travels/transport_and_accommodation/lock-submit')}}/"+officialtravelID,
    success:function(response)
    {
      if (response.status == 'true') {
        swal({
          title: "Data Success!",
          icon: "success"
        });
        $('#settlementtravel_table').DataTable().ajax.reload();         
      }else if(response.status == 'null'){
       swal({
        icon: 'warning',
        title: 'Price Transport and Accommodation',
        text: response.message
      });
     }else{
      swal({
        icon: 'error',
        title: 'Oops...',
        dangerMode: true,
        text: 'Something went wrong! ['+response.message+']'
      });
    }
  },
  error: function(response) {
    swal({
      icon: 'error',
      title: 'Oops...',
      dangerMode: true,
      text: 'Something went wrong! ['+response.message+']'
    });
  }
});
}
$(document).on('click', '.btn-lock', function (e) {
  e.preventDefault();
  var officialtravelID = $(this).attr('more_id');
  if (officialtravelID) {
    swal({
      title: 'Are you sure?',
      // text: 'Submit Lock Travel Request?!',
      icon: 'warning',
      buttons: true,
      // dangerMode: true,
      // confirmButtonColor: '#3085d6',
      // cancelButtonColor: '#d33',
      confirmButtonText: 'Yes!'
    }).then(function(value) {
      if (value) {
        submitHrLock(officialtravelID);
      }
    });
  }
});

</script>
@endsection