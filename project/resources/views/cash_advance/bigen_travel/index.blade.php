@extends('adminlte::page')
@section('title', 'Travel History')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
     <div class="card-header">
      <h5 class="card-title">Travel History
      </h5>
      <div class="card-tools">
      </div>
    </div>
    <div class="card-header">
      <div class="row">
        <div class="col-lg-6">
         <div class="row">
          <label class="col-sm-4 col-form-label">Start to End Date <sup class="text-danger">*</sup></label>
          <div class="col-sm-6">
            <div class="input-group">
              <input type="text" autocomplete="off" name="start_end" id="start_end" class="form-control form-control-sm" style="width: 100%;">
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
          <div class="col-sm-3">
            <a href="" target="_blank" class="btn btn-success btn-sm text-white" id="export"><i class="fas fa-file-excel"></i> Export Excel</a>
          </div>
        </div>      
      </div>
    </div>
  </div>
  <div class="card-body">
    <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
    <br>
    <br>
    <table class="table table-hover table-bordered table-striped" id="bgen_settlement_table" style="width: 100%;">
      <thead>
        <tr>
          <th>No</th>
          <th>Reference Number</th>
          <th>NIK</th>
          <th>Name</th>
          <th>Position</th>
          {{-- <th>Branch</th>
          <th>Division</th> --}}
          <th>Category</th>
          <th>Department</th>
          <th>Remark</th>
          <th>Periode</th>
          <th>Price</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
</div>
</div>

<div class="modal fade" id="modal_form_travel_history" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
          <form method="POST" id="priceForm">
            {{ csrf_field() }}
            <div class="modal-header">
                <h5 class="modal-title">Edit Price</h5>
                <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="height: 450px;overflow-y: auto;">
              <div class="row">
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Reference Number</label>
                    <div class="col-sm-8">
                        <input id="id_expense_request" type="hidden" class="form-control form-control-sm" style="width: 100%;" disabled>
                        <input id="reference_number" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">NIK</label>
                    <div class="col-sm-8">
                        <input id="nik" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Name</label>
                    <div class="col-sm-8">
                        <input id="name" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Position</label>
                    <div class="col-sm-8">
                        <input id="dec_position" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  {{-- <div class="row">
                    <label class="col-sm-4 col-form-label">Qty</label>
                    <div class="col-sm-8">
                        <input id="quantity" type="number" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Total Amount</label>
                    <div class="col-sm-8">
                        <input id="total_amount" type="number" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div> --}}
                </div>
                <div class="col-md-6">
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Category</label>
                    <div class="col-sm-8">
                        <input id="category" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Periode</label>
                    <div class="col-sm-8">
                        <input id="period" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Unit Price</label>
                    <div class="col-sm-8">
                        <input id="price" type="text" class="form-control form-control-sm" style="width: 100%;" disabled>
                    </div>
                  </div>
                  <div class="row">
                    <label class="col-sm-4 col-form-label">Status</label>
                    <div class="col-sm-8">
                        <select id="status" class="form-control form-control-sm" style="width: 100%;">
                        </select>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
              <button type="button" class="btn btn-sm btn-secondary" onclick="javascript:window.location.reload()" data-dismiss="modal">Close</button> 
            </div>
             
          </form>
      </div>
  </div>
</div>
<!-- End Form modal -->
@endsection

@section('scripts')
<script type="text/javascript">
  let global_modal_expense_request_id = null;
  function formatRupiah(amount) {
    if (!amount) {
      return '';
    }
    return amount.toLocaleString('id-ID');
  }
  function unformatRupiah(rupiah) {
    if (!rupiah || rupiah === 'Rp. ') {
      return 0;
    }
    return parseInt(rupiah.replace(/,|\./g, '').replace('Rp ', ''));
  }
  moment.updateLocale('id', {
    weekdays: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']
  });
  function TanggalIndonesia(string) {
    var formattedDate = moment(string).format('dddd, D MMM YYYY');
    return formattedDate;
  }
  var start_end = '';
  function DataTableBgen(start_end) {
    $('#bgen_settlement_table').DataTable({
      processing: true,
      scrollX: true,
      pageLength: 50,
      fixedColumns: {
        // leftColumns: 2
        rightColumns: 1
          // right: 
      },
      columnDefs: [
      {
        orderable: false,
        targets: 0
      }
      ],
      ajax: {
        url: "{{ route('index_bigen') }}",
        data: {start_end: start_end},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#bgen_settlement_table').DataTable().ajax.reload();
        }
      },
      columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'reference_number', name: 'reference_number' },
      { data: 'name', name: 'name' },
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'dec_position', name: 'dec_position' },
      // { data: 'dec_branch', name: 'dec_branch' },
      // { data: 'principal_code', name: 'principal_code' },
      { data: 'dec_product_category', name: 'dec_product_category' },
      { data: 'dec_dept', name: 'dec_dept' },
      {
        data: 'dec_product_category',
        name: 'dec_product_category',
        render: function (data, type, full, meta) {
          if (data == 'Transportation') {
            return 'PERJ DINAS-TIKET KE '+full.location_to+' UNTUK '+full.reason_notes;
          }else{
            return 'PERJ DINAS-HOTEL KE '+full.location_to+' UNTUK '+full.reason_notes;
          }
        }
      },
      {
        data: 'start_date',
        name: 'start_date',
        render: function (data, type, row) {
			if(row.code_product_category == 'ACD'){
				var start = data.split(' to ')[0];
				var end = data.split(' to ')[1];
				if (type === 'display' || type === 'filter') {
					var startDate = new Date(start);
					var endDate = new Date(end.split(';')[0]);
					return TanggalIndonesia(startDate.toISOString().split('T')[0])+' / '+TanggalIndonesia(endDate.toISOString().split('T')[0]);
				}
			}
			else{
				if (type === 'display' || type === 'filter') {
					var startDate = new Date(data.split(';')[0]);
					return TanggalIndonesia(startDate.toISOString().split('T')[0]);
				}
			}
          return data;
        }
      },
      {
        data: 'unit_price',
        name: 'unit_price',
        render: function (data) {
          var price = unformatRupiah(data);
          return formatRupiah(price);
        }
      },
      { 
        data: 'status',
        name: 'status',
        render: function(data) {
          if(data == "A") return "<span class='badge badge-success'>Active</span>";
          else return "<span class='badge badge-danger'>Inactive</span>"
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
  }
  $(function () {
    DataTableBgen(start_end);
  });
  $(document).on('click', '#filter', function() {
    start_end = $("#start_end").val();
    var split = start_end.split('to');
    var start_date = split[0];
    var end_date = split[1];
	$('#bgen_settlement_table').DataTable().destroy();
    if (start_end) {
      $("#start_end").removeClass('is-invalid');
      $("#start_endError").children("strong").text('');
      $("#export").attr('href',"{{route('export_excel')}}?start="+start_date+"&end="+end_date);
      DataTableBgen(start_end);
    }else{
	  DataTableBgen(start_end);
    //  $("#start_end").addClass("is-invalid");
    //  $("#start_endError").children("strong").text("The Star End Date field is required.");
    }
  });
  $("#start_end").daterangepicker({
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
  var startOfWeek = moment().startOf('week');
  var endOfWeek = moment().endOf('week');
  start_end = startOfWeek.format('YYYY-MM-DD') + ' to ' + endOfWeek.format('YYYY-MM-DD');
  $("#start_end").val(start_end);
  $("#export").attr('href',"{{route('export_excel')}}?start="+startOfWeek.format('YYYY-MM-DD')+"&end="+endOfWeek.format('YYYY-MM-DD'));
  $('#start_end').data('daterangepicker').setStartDate(startOfWeek);
  $('#start_end').data('daterangepicker').setEndDate(endOfWeek);

  
  $(document).on('click', '.btn-edit', function () {
    global_modal_expense_request_id = $(this).attr('expense-request-id');
    $.ajax({
      url: `{{ route('travel_history_expense_data') }}?expense_request_id=${global_modal_expense_request_id}`,
      success: (data) => {
        data = data[0];
        $("#id_expense_request").val(data.id_expense_request);
        $("#reference_number").val(data.reference_number);
        $("#nik").val(data.nik_employee);
        $("#name").val(data.name);
        $("#dec_position").val(data.dec_position);
        $("#price").val(formatRupiah(parseInt(data.unit_price)));
        $("#price").attr('disabled', false);
        $('#status').select2({
          data: [
            {id: "A", text: "Active"},
            {id: "I", text: "Inactive"}
          ]
        }).val(data.status).trigger('change');
        $('#category').val(data.dec_product_category);
        let period = data.start_date.replace(";", "");
        $('#period').val(period)
      }
    })
    $('#modal_form_travel_history').modal('show');
  });

  // $(document).on('change', '#price', function() {
  //   $('#total_amount').val($('#price').val()*$('#quantity').val());
  // });

  $('#priceForm').submit(function (e) {
    e.preventDefault();
    let token = $(this).serializeArray()[0];
    console.log('submit', `${$('#id_expense_request').val()} -> ${$('#price')} -tok: ${token}`, token);
    
    $.ajax({
      url: "{{ route('travel_history_expense_data.price') }}",
      method: 'POST',
      data: {
        _token: token.value,
        expense_request_id: $('#id_expense_request').val(),
        unit_price: unformatRupiah($('#price').val()),
        status: $('#status').val(),
      },
      success: (response) => {
        swal({
          icon: 'success',
          title: 'Success',
          text: 'Edit success!'
        }).then(() => {
          window.location.reload()
        })
      },
      error: (e) => {
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: `Something went wrong! ${e.message}`,
        })
      }
    })
  })

  $(document).on('change', '#price', function (element) {
    let unformatted = unformatRupiah($(this).val());
    $(this).val(formatRupiah(unformatted));
  });
</script>
@endsection