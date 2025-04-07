@extends('adminlte::page')
@section('title', 'Master Of Letter')

@section('content')
<div class="row">
  <div class="col-12">
    <!-- Default box -->
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Master Of Letter</h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Master Of Letter</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="ml_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th></th>
            <th>No</th>
            <th>Sequence</th>
            <th>Code</th>
            <th>Description</th>
            <th>Status</th>
            <th data-priority="1" width="300" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
@endsection

@include('eletter/master_letter/create')
@include('eletter/master_letter/update')

@section('css')
<style type="text/css">
  .sticky {
    position: -webkit-sticky; /* Safari support */
    position: sticky;
    left: 0;
    z-index: 2;
  }
  .sticky:nth-child(2) {
    left: 50px; /* Sesuaikan dengan lebar kolom yang tetap */
  }
  /* CSS untuk baris yang tetap (menggunakan pseudo-element) */
  tr:before {
    content: '';
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 1;
  }
  #ml_table td:nth-child(8) {
    text-align: center;
  }
  tr:before {
    content: ' ';
    display: table;
  }

  tr:after {
    content: ' ';
    display: table;
    clear: both;
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
@include('eletter/master_letter/js/js-create')
@include('eletter/master_letter/js/js-update')
<script type="text/javascript">
  $(function () {
    $('#ml_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('ml.index') }}",
        error: function (jqXHR, textStatus, errorThrown) {
          $('#ml_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {   
        data: 'id_letter',
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'sequence', name: 'sequence' },
      { data: 'code', name: 'code' },
      { data: 'description', name: 'description' },
      { 
        data: 'status', 
        name: 'status',
        render: function (data, type, row) {
          if (data === 'A') {
            return 'Active';
          } else {
            return 'Inactive';
          }
        } 
      },
      { data: 'action', name: 'action', orderable: false, className: 'space', render: function ( data, type, row ) {
        if(access_create == 0){
          $('.new').css('display', 'none');
        }   
        if(access_edit == 0){
          $('.edit').css('display', 'none');
        }       
        if(access_delete == 0){
          $('.delete').css('display', 'none');
        }
        return data;
      } 
    },
    ]
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
  $(document).on('click', '.btn-del', function (event) {
    id_general_data = $(this).attr('more_id');
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
          method: "GET",
          url: "{{url('e-letter/master_letter/master_letter/destroy')}}"+"/"+id_general_data,
          success:function(data)
          {
            if (data.status == 'true') {
              setTimeout(function(){
                swal({
                  title: "Data Deleted!",
                  icon: "success"
                });
                $('#confirmModal').modal('hide');
                $('#ml_table').DataTable().ajax.reload();         
              }, 50);
            }else{
              swal({
                icon: 'error',
                title: 'Oops...',
                dangerMode: true,
                text: 'Cannot Delete [Data Used Another Menu]'
              });
            }
          },
          error:function(data) {
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Cannot Delete [Data Used Another Menu]'
            });
          }
        })
      }
    });
  });
</script>
@endsection

