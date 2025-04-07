@extends('adminlte::page')
@section('title', 'Master Product')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Master Product
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add</button>
        </div>
      </div>
      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="pb_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th data-priority="1">No</th>
            <th data-priority="1">Product Code</th>
            <th data-priority="1">Description</th>
            <th data-priority="1">Product Type</th>
            <th data-priority="1">Product Categories</th>
            {{-- <th data-priority="1">Price</th> --}}
            <th>Status</th>
            <th data-priority="1" width="300">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
<!-- Form -->
<div class="modal fade" id="modal_form_product" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Form Master Product</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="productForm">
        {{ csrf_field() }}
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Product Code <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input type="hidden" id="id_product" hidden="" name="id_product">
                  <input autocomplete="off" type="text" name="code" id="code" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="codeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Description <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="description" id="description" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="descriptionError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Product Type <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <select id="inventory_type" name="inventory_type" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                  <span class="invalid-feedback d-block" role="alert" id="inventory_typeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Product Categories <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <select id="id_product_categories" name="id_product_categories" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                  <span class="invalid-feedback d-block" role="alert" id="id_product_categoriesError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">UOM <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <select id="id_uom" name="id_uom" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                  <span class="invalid-feedback d-block" role="alert" id="id_uomError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Price <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" type="text" name="price" id="price" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback d-block" role="alert" id="priceError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Purchasable </label>
                <div class="col-sm-8">
                  <input type="checkbox" style="width: 20px;height: 20px;" name="purchasable" value="" id="purchasable">
                  <span class="invalid-feedback d-block" role="alert" id="purchasableError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Saleable </label>
                <div class="col-sm-8">
                  <input type="checkbox" style="width: 20px;height: 20px;" name="saleable" value="" id="saleable">
                  <span class="invalid-feedback d-block" role="alert" id="saleableError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Expenseable </label>
                <div class="col-sm-8">
                  <input type="checkbox" style="width: 20px;height: 20px;" name="expenseable" value="" id="expenseable">
                  <span class="invalid-feedback d-block" role="alert" id="expenseableError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Accommodation </label>
                <div class="col-sm-8">
                  <input type="checkbox" style="width: 20px;height: 20px;" name="accommodation" value="" id="accommodation">
                  <span class="invalid-feedback d-block" role="alert" id="accommodationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Transportation </label>
                <div class="col-sm-8">
                  <input type="checkbox" style="width: 20px;height: 20px;" name="transportation" value="" id="transportation">
                  <span class="invalid-feedback d-block" role="alert" id="transportationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Status <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <select id="status" name="status" class="form-control form-control-sm select_opsi" style="width: 100%;"></select>
                  <span class="invalid-feedback d-block" role="alert" id="statusError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-loading" id="modal-loading" style="display: none;">
           <span class="fa fa-spinner fa-spin fa-3x"></span>
         </div>
       </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-success action" id="saveBtn"><i class="fas fa-save"></i> Save</button>&nbsp;
        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>
</div>
<!-- End Form modal -->
@endsection
@section('css')
<style type="text/css">
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

  .custom-select:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  *:focus{
    outline:0px;
  }
</style>
@endsection

@section('scripts')
<script type="text/javascript">
  var global_uom = [];
  var global_categories = [];
  function getUomAndCategories() {
    $.ajax({
      url: '{{ route("master_product.get_categories") }}',
      success: (categories) => {
        global_categories = categories;
        $('#id_product_categories').empty().prepend('<option></option>').select2({
          allowClear: true,
          placeholder: "Select Option ....",
          data: categories
        })
      }
    })
    $.ajax({
      url: '{{ route("master_product.get_uom") }}',
      success: (uom) => {
        global_uom = uom;
        $('#id_uom').empty().prepend('<option></option>').select2({
          allowClear: true,
          placeholder: "Select Option ....",
          data: uom
        })
      }
    })
  }
  $(document).ready(function () {
    getUomAndCategories();
    $('#pb_table').DataTable({
      processing: true,
      responsive: true,
      ajax: {
        url: "{{ route('master_product.index') }}",
        error: function (jqXHR, textStatus, errorThrown) {
        //		$('#expense_table').DataTable().ajax.reload();
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
			},
      columns: [
        {   // Detail Responsive
            data: '',
            defaultContent: '',
            orderable: false
        },
        {   // Checkbox select column
            data: 'id_product',
            defaultContent: '',
            orderable: false
        },
        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
        { data: 'code', name: 'code' },
        { data: 'description', name: 'description' },
        { data: 'inventory_type', name: 'inventory_type' },
        { data: 'category', name: 'category' },
        { data: 'status', name: 'status' },
        { data: 'action', name: 'action' },
      ],
    });
  });

  function loadEdit(idProduct) {
    $.ajax({
      data: {
        _token: '{{ csrf_token() }}',
        id_product: idProduct,
      },
      beforeSend: () => {
        $('#loader').removeClass('hidden');
        $("#productForm")[0].reset();
      },
      success: (data) => {
        $('#loader').addClass('hidden');

        $('#id_product').val(data.id_product);
        $('#code').val(data.code);
        $('#description').val(data.description);
        $('#price').val(data.price).trigger('change');
        $('#inventory_type').val(data.inventory_type).trigger('change');
        $('#status').val(data.status).trigger('change');

        $('#purchasable').val(data.purchasable).attr('checked', data.purchasable);
        $('#saleable').val(data.saleable).attr('checked', data.saleable);
        $('#expenseable').val(data.expenseable).attr('checked', data.expenseable);
        $('#accommodation').val(data.accomodation).attr('checked', data.accomodation);
        $('#transportation').val(data.transportation).attr('checked', data.transportation);

        $('#id_product_categories').empty().prepend('<option></option>').select2({
          allowClear: true,
          placeholder: "Select Option ....",
          data: global_categories
        }).val(data.id_product_categories).trigger('change');

        $('#id_uom').empty().prepend('<option></option>').select2({
          allowClear: true,
          placeholder: "Select Option ....",
          data: global_uom
        }).val(data.id_uom).trigger('change');

        $("#modal_form_product").modal('show');
      }
    });
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
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: "Select Option ...."
  });
  $(".new").click(function() {
   $("#productForm")[0].reset();
   $(".select_opsi").val(null).trigger('change');
   $(".invalid-feedback").children("strong").text("");
   $("#productForm input").removeClass("is-invalid");
   $("#productForm textarea").removeClass("is-invalid");
   $("#productForm select").removeClass("custom-select");
   $("#modal_form_product").modal('show');
 });

  var option_status = [
  {
    id: 'A',
    text: 'Active'
  },
  {
    id: 'I',
    text: 'Inactive'
  }
  ];
  var option_type = [
  {
    id: 'S',
    text: 'Stockable'
  },
  {
    id: 'E',
    text: 'Expense'
  }
  ];
  $("#status").select2({
    allowClear: true,
    placeholder: "Select Option ....",
    data:option_status
  });
  $("#inventory_type").select2({
    allowClear: true,
    placeholder: "Select Option ....",
    data:option_type
  });
  $(document).on('keyup','#price',function() {
    var rupiah = $(this).val();
    var numberTotal = unformatRupiah(rupiah);
    this.value = formatRupiah(numberTotal);
  })

  $(document).on('change', 'input', function () {
    $('input[type="checkbox"]').each(function () {
      $(this).val($(this).prop('checked'));
    })
  })

  $(document).on('click', '#saveBtn', function () {
    console.log($('#productForm').serialize());
    $.ajax({
      url: "{{ route('master_product.save') }}",
      type: 'POST',
      data: $('#productForm').serialize(),
      beforeSend: () => {
        $('#loader').removeClass('hidden');
      },
      success: (response) => {
        $('#loader').addClass('hidden');
        swal({
            icon: 'success',
            title: 'Success',
            text: response.message
        }).then(function() { 
            $('#pb_table').DataTable().ajax.reload();
            $('#modal_form_product').modal('hide');
        });
      },
      error: (err) => {
        $('#loader').addClass('hidden');
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! '+err.responseJSON.message,
        })
      }
    })
  });

  $(document).on('click', '.delete', function () {
    $.ajax({
      url: '{{ route("master_product.delete") }}',
      type: 'POST',
      data: {
        id_product: $(this).attr('id-product'),
        _token: '{{ csrf_token() }}'
      },
      beforeSend: () => {
        $('#loader').removeClass('hidden');
      },
      success: (response) => {
        $('#loader').addClass('hidden');
        swal({
            icon: 'success',
            title: 'Success',
            text: response.message
        }).then(function() { 
            $('#pb_table').DataTable().ajax.reload();
        });
      },
      error: (err) => {
        $('#loader').addClass('hidden');
        swal({
          icon: 'error',
          title: 'Oops...',
          dangerMode: true,
          text: 'Something went wrong! '+err.responseJSON.message,
        })
      }
    })
  })
</script>
@endsection