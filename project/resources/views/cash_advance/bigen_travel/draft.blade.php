@extends('adminlte::page')
@section('title', 'Bigen Travel Request')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Travel Request
        </h5>
        <div class="card-tools">
          <button type="button" id="new_transport" class="btn btn-primary btn-sm mb-3 new_transport">
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
        <!-- <img src="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/0.16.1/images/loader-large.gif" alt="loading"> -->
        <span class="fa fa-spinner fa-spin fa-3x"></span>
      </div>
      <table class="table table-hover table-bordered table-striped" id="settlementtravel_table" style="width: 100%;">
        <thead>
          <tr>
            <th>&nbsp;&nbsp;&nbsp;&nbsp;</th>
            <th data-priority="2">Reference Number</th>
            <th data-priority="3">Request by</th>
            <th data-priority="7">Status</th>
            <th data-priority="4">Destination</th>
            <th data-priority="5">Start Date</th>
            <th data-priority="6">End Date</th>
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
              <div class="row">
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
              </div>
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
    </form>
  </div>
</div>
</div>

<div class="row mt-5">
  <form method="post" id="formTravelRequest" enctype="multipart/form-data">
    @csrf
    <div style="display:none;">
      <table class="table table-hover table-bordered table-striped" id="thead_transport" style="width: 100%;">
       <thead>
         <tr>
          <th rowspan="2">
            No. 
          </th>
          <th rowspan="2">Transport Type</th>
          <th rowspan="2">Transport Name Recommendation</th>
          <th colspan="2" style="text-align: center;">Destination</th>
          <th colspan="2" style="text-align: center;">Departure</th>
          <th rowspan="2">Price</th>
        </tr>
        <tr>
          <th>From</th>
          <th>To / Branch</th>
          <th>Date</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody id="table_transport" class="table_transport"></tbody>
    </table>
    <table id="sample_table_transport" class="table table-striped table-hover sample_table_transport">
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
          <input name="transport[0][transport_name]" autocomplete="off" id="transport_0_transport_name" class="form-control form-control-sm transport_name_input">
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
        <td data-label="To / Branch">
          <div class="row">
            <div class="col-lg-5">
              <input name="transport[0][to]" autocomplete="off" style="width: 100%;" id="transport_0_to" class="form-control form-control-sm to_input">
              <span class="invalid-feedback to_input_error" role="alert" id="transport_0_toError">
                <strong></strong>
              </span>
            </div>
            <div class="col-1">
              /
            </div>
            <div class="col-lg-6">
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
        <td data-label="Price">
          <input type="text" autocomplete="off" name="transport[0][price_transport]" id="transport_0_price_transport" class="form-control form-control-sm price_transport_input">
          <span class="invalid-feedback price_transport_input_error" role="alert" id="transport_0_price_transportError">
            <strong></strong>
          </span>
        </td>
      </tr>
    </table>
<!--     <table class="table table-hover table-bordered table-striped responsive-table" id="thead_accommodation">
      <thead>
       <tr>
        <th>No. </th>
        <th>Category</th>
        <th>Name of Hotel/Kos</th>
        <th>Start End Date</th>
        <th>Length of stay (days)</th>
        <th>UOM</th>
        <th>Price</th>
      </tr>
    </thead>
    <tbody id="table_accommodation" class="table_accommodation">
    </tbody>
  </table>
  <table id="sample_table_akomodasi">
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
      <td data-label="Start End Date">
        <input name="akomodasi[0][start_end_akomodasi]" autocomplete="off" id="akomodasi_0_start_end_akomodasi" class="form-control form-control-sm start_end_akomodasi_input">
        <span class="invalid-feedback start_end_akomodasi_input_error" role="alert" id="akomodasi_0_start_end_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td data-label="Length of stay (days)">
        <input type="text" autocomplete="off" name="akomodasi[0][lama_menginap]" id="akomodasi_0_lama_menginap" class="form-control form-control-sm lama_menginap_input">
        <span class="invalid-feedback lama_menginap_input_error" role="alert" id="akomodasi_0_lama_menginapError">
          <strong></strong>
        </span>
      </td>
      <td data-label="UOM">
        <select name="akomodasi[0][uom_akomodasi]" id="akomodasi_0_uom_akomodasi" class="form-control form-control-sm uom_akomodasi_input" style="width: 100%;" readonly></select>
        <span class="invalid-feedback d-block uom_akomodasi_input_error" role="alert" id="akomodasi_0_uom_akomodasiError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" autocomplete="off" name="akomodasi[0][price_akomodasi]" id="akomodasi_0_price_akomodasi" class="form-control form-control-sm price_akomodasi_input">
        <span class="invalid-feedback price_akomodasi_input_error" role="alert" id="akomodasi_0_price_akomodasiError">
          <strong></strong>
        </span>
      </td>
    </tr>
  </table> -->
</div>
</form>
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
    #settlementtravel_table td:nth-child(8) {
      text-align: center;
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
    function formatRupiah(amount) {
      if (!amount) {
        return '';
      }
      return "Rp. " + amount.toLocaleString('id-ID');
    }
    function unformatRupiah(rupiah) {
      if (!rupiah || rupiah === 'Rp. ') {
        return 0;
      }
      return parseInt(rupiah.replace(/,|\./g, '').replace('Rp ', ''));
    }
    $(function () {
      var table = $('#settlementtravel_table').DataTable({
        processing: true,
        pageLength: 50,
        scrollX: true,
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
          class: 'dt-control',
          orderable: false,
          data: 'id_official_travel',
          render: function (data, type, row) {
            return '<i class="fa fa-chevron-right"></i>';
          }
        },
        { data: 'reference_number', name: 'reference_number' },
        { data: 'name', name: 'name' },
        { 
          data: 'dec_approval_status', 
          name: 'dec_approval_status', 
          render: function (data, type, row) {
            if (data == 'Approved') {
              return '<span class="badge bg-success text-white">'+data+'</span>';
            }else if(data == 'Cancelled'){
              return '<span class="badge bg-danger text-white">'+data+'</span>';
            }else{
              return '<b>'+data+'</b>';
            }
          }
        },
        { data: 'location_to', name: 'location_to' },
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
      // let global_id_akomodasi = 0;
      let id_official_travel = 0;
      $(document).on('click', '.new_transport', function () {
        var content = jQuery(".sample_table_transport tr"),
        size = global_id_transport++,
        element = null,
        element = content.clone();

        element.find('.edit-save').attr('more_id',size);

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
        element.find('.transport_name_input').on('keyup',function() {
          $(this).val($(this).val().toUpperCase());
        });

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
          allowClear: true,
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
        });

        element.appendTo('#table_transport_'+id_official_travel);
        $('#table_transport_'+id_official_travel+' tr').each(function (index) {
          $(this).find('span.sn').html(index + 1+'.');
        });
      });
    // Akomodasi
    // $(document).on('click', '#new_accommodation', function () {
    //   var content = jQuery("#sample_table_akomodasi tr"),
    //   size = global_id_akomodasi++,
    //   element = null,
    //   element = content.clone();

    //   element.find('.edit-save').attr('more_id',size);

    //   element.find('.id_akomodasi_input').attr('id', 'akomodasi_' + size + '_id_akomodasi');
    //   element.find('.id_akomodasi_input').attr('name', 'akomodasi[' + size + '][id_akomodasi]');
    //   element.find('.id_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_id_akomodasiError');

    //   element.find('.product_akomodasi_input').attr('id', 'akomodasi_' + size + '_product_akomodasi');
    //   element.find('.product_akomodasi_input').attr('name', 'akomodasi[' + size + '][product_akomodasi]');
    //   element.find('.product_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_product_akomodasiError');
    //   element.find('.product_akomodasi_input').select2({
    //     allowClear: true,
    //     placeholder: 'Select Product ....',
    //     data: global_product_akomodasi.map(function(item) {
    //       return {id: item.id, text: item.text, id_uom: item.id_uom, dec_uom: item.dec_uom}
    //     })
    //   }).on('change',function(e) {
    //     var selectedOption = $(this).select2('data')[0];
    //     if (selectedOption) {
    //       var idUom = selectedOption.id_uom;
    //       var decUom = selectedOption.dec_uom;
    //       element.find('.uom_akomodasi_input').empty();
    //       if (idUom) {
    //         element.find('.uom_akomodasi_input').append('<option value="'+idUom+'">'+decUom+'</option>');
    //       }else{
    //         element.find('.uom_akomodasi_input').empty();
    //       }
    //     }
    //   }).val(null).trigger('change');
    //   element.find('.product_akomodasi_input').val(null).trigger('change');

    //   element.find('.nama_hotel_input').attr('id', 'akomodasi_' + size + '_nama_hotel');
    //   element.find('.nama_hotel_input').attr('name', 'akomodasi[' + size + '][nama_hotel]');
    //   element.find('.nama_hotel_input_error').attr('id', 'akomodasi_' + size + '_nama_hotelError');
    //   element.find('.nama_hotel_input').on('keyup',function() {
    //     $(this).val($(this).val().toUpperCase());
    //   });

    //   element.find('.start_end_akomodasi_input').attr('id', 'akomodasi_' + size + '_start_end_akomodasi');
    //   element.find('.start_end_akomodasi_input').attr('name', 'akomodasi[' + size + '][start_end_akomodasi]');
    //   element.find('.start_end_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_start_end_akomodasiError');
    //   if (start_date != null) {
    //     const dateHeader = start_date.split(" to ");
    //     const startDateHeader = dateHeader[0];
    //     const endDateHeader = dateHeader[1];
    //     element.find('.start_end_akomodasi_input').daterangepicker({
    //       drops: 'up',
    //       autoUpdateInput: false,
    //       minDate: startDateHeader,
    //       maxDate: endDateHeader,
    //       locale: {
    //         cancelLabel: 'Riset',
    //         format: 'YYYY-MM-DD',
    //         separator: ' to '
    //       }
    //     }).on('apply.daterangepicker', function(ev, picker) {
    //       var startDate = picker.startDate;
    //       var endDate = picker.endDate;
    //       var timeDiff = endDate - startDate;
    //       var daysDiff = Math.floor(timeDiff / (1000 * 3600 * 24));
    //       element.find('.lama_menginap_input').val(daysDiff+' days');
    //       $(this).val(startDate.format('YYYY-MM-DD') + ' to ' + endDate.format('YYYY-MM-DD'));
    //     }).on('cancel.daterangepicker', function() {
    //       $(this).val('');
    //       element.find('.lama_menginap_input').val('');
    //     }).on('keydown.daterangepicker',function(e) {
    //       e.preventDefault();
    //     });
    //   }

    //   element.find('.lama_menginap_input').attr('id', 'akomodasi_' + size + '_lama_menginap');
    //   element.find('.lama_menginap_input').attr('name', 'akomodasi[' + size + '][lama_menginap]');
    //   element.find('.lama_menginap_input_error').attr('id', 'akomodasi_' + size + '_lama_menginapError');
    //   element.find('.lama_menginap_input').attr('readonly', true)

    //   element.find('.uom_akomodasi_input').attr('id', 'akomodasi_' + size + '_uom_akomodasi');
    //   element.find('.uom_akomodasi_input').attr('name', 'akomodasi[' + size + '][uom_akomodasi]');
    //   element.find('.uom_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_uom_akomodasiError');
    //   element.find('.uom_akomodasi_input').select2();

    //   element.find('.price_akomodasi_input').attr('id', 'akomodasi_' + size + '_price_akomodasi');
    //   element.find('.price_akomodasi_input').attr('name', 'akomodasi[' + size + '][price_akomodasi]');
    //   element.find('.price_akomodasi_input_error').attr('id', 'akomodasi_' + size + '_price_akomodasiError');
    //   element.find('.price_akomodasi_input').on('input', function() {
    //     var rupiah = this.value;
    //     var numberTotal = unformatRupiah(rupiah);
    //     this.value = formatRupiah(numberTotal);
    //   });
    //   element.appendTo('#table_accommodation_'+id_official_travel);
    //   $('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
    //     $(this).find('span.sn').html(index + 1+'.');
    //   });
    // });
    function transportDetail(d) {
      var headerTransport = $("#thead_transport").html();
      return headerTransport;
    }
    function getViewDetail(id_official_travel, row) {
      $.ajax({
        url: "{{url('official_travel/travels/settlement_travel/detail_view')}}"+"/"+id_official_travel,
        type: 'GET',
        dataType: 'json',
        success: function (response) {
          row.child(transportDetail(response.transport)).show();
          $("#loading").hide();
          // for (var i = 0; i < response.transport.length; i++) {
          //   $("#sample_table_transport").html();
          // }
      // if (response.transport) {
        // start_date = response.transport[0].start_date.substr(0,10)+' to '+response.transport[0].end_date.substr(0,10);
        // $.each(response.transport, function (i, item) {
        //   $('#new_transport_'+id_official_travel).trigger('click');
        // });
        // $.each(response.akomodasi, function (i, item_akomo) {
        //   $('#new_accommodation').trigger('click');
        // });
        // setTimeout(function () {
        //   $('#table_transport_'+id_official_travel+ ' tr').each(function (index) {
        //     var notes = response.transport[index].notes;
        //     var notes_trans = notes.split(';');
        //     var jenis_transportasi = notes_trans[0];
        //     var from = notes_trans[1];
        //     var to = notes_trans[2];
        //     var branch = notes_trans[3];
        //     var description = response.transport[index].description;
        //     var description_trans = description.split(';');
        //     var date_transport = description_trans[0];
        //     var time_transport = description_trans[1];
        //     $(this).find('.id_transport_input').val(response.transport[index].id_expense_request);
        //     $(this).find('.jenis_transportasi_input').val(response.transport[index].id_product).trigger('change');
        //     $(this).find('.transport_name_input').val(jenis_transportasi);
        //     $(this).find('.from_input').val(from);
        //     $(this).find('.to_input').val(to);
        //     $(this).find('.branch_input').val(branch).trigger('change');
        //     $(this).find('.date_transport_input').val(date_transport);
        //     $(this).find('.time_transport_input').val(time_transport);
        //     var price_transport = unformatRupiah(response.transport[index].unit_price);
        //     $(this).find('.price_transport_input').val(formatRupiah(price_transport));
        //   });                       
        //   $('#table_accommodation_'+id_official_travel+' tr').each(function (index) {
        //     var notes_akomodasi = response.akomodasi[index].notes.replace(/;/g, '');
        //     var description_akomodasi = response.akomodasi[index].description.replace(/;/g, '');
        //     $(this).find('.id_akomodasi_input').val(response.akomodasi[index].id_expense_request);
        //     $(this).find('.product_akomodasi_input').val(response.akomodasi[index].id_product).trigger('change');
        //     $(this).find('.nama_hotel_input').val(notes_akomodasi);
        //     $(this).find('.start_end_akomodasi_input').val(description_akomodasi);
        //     const dateRangeAkomodasi = description_akomodasi.split(' to ');
        //     const startDate = new Date(dateRangeAkomodasi[0]);
        //     const endDate = new Date(dateRangeAkomodasi[1]);
        //     var defaultStartDate = moment(dateRangeAkomodasi[0]);
        //     var defaultEndDate = moment(dateRangeAkomodasi[1]);
        //     $(this).find('.start_end_akomodasi_input').data('daterangepicker').setStartDate(defaultStartDate);
        //     $(this).find('.start_end_akomodasi_input').data('daterangepicker').setEndDate(defaultEndDate);
        //     const timeDifference = endDate - startDate;
        //     const daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));
        //     $(this).find('.lama_menginap_input').val(daysDifference+' Days');
        //     var price_akomodasi = unformatRupiah(response.akomodasi[index].unit_price);
        //     $(this).find('.price_akomodasi_input').val(formatRupiah(price_akomodasi));
        //   });                       
        // }, 500);
      // }
    },
    error: function (error) {
      getViewDetail(id_official_travel, row);
    }
  });
}
var detailRows = [];
var start_date;
$('#settlementtravel_table tbody').on('click', 'tr td.dt-control', function (event) {
  event.preventDefault();
  var tr = $(this).closest('tr');
  var row = table.row(tr);
  var idx = detailRows.indexOf(tr.attr('id'));
  id_official_travel = row.data().id_official_travel;
  if (row.child.isShown()) {
    tr.removeClass('details');
    row.child.hide();
    $(this).html('<i class="fa fa-chevron-right"></i>');
    detailRows.splice(idx, 1);
  }else {
    $("#loading").show();
    tr.addClass('details');
    getViewDetail(id_official_travel, row);
    detailRows.push(tr.attr('id'));
    $(this).html('<i class="fa fa-chevron-down"></i>');
  }
});
table.on('draw', function () {
  $('#settlementtravel_table tbody tr td.dt-control').each(function () {
    $(this).trigger('click');
  });
  // detailRows.forEach(function (id, i) {
  //   $('#' + id + ' td.dt-control').trigger('click');
  // });
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
function get_edit(officialtravelID) {
  $.ajax({
    type: "GET",
    url: "{{url('official_travel/travels/settlement_travel/view')}}"+"/"+officialtravelID,
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
  $("#travelForm")[0].reset();
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
</script>
@endsection