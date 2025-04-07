@extends('adminlte::page')
@section('title', 'Expense Product')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Expense Product</h5>
				<div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Expense Product</button>
                </div>
            </div>      
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="expense_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
					<th></th>
					<th></th>
					<th>No</th>
					<th>Job Grade</th>
					<th>Product</th>
					<th>Region</th>
					<th>Description</th>
					<th>Min Price</th>
					<th>Max Price</th>
                    <th>Position</th>
					<th>Notes</th>
					<th data-priority="1" style="text-align:center;" width=100>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="expenseForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
			<button type="submit" class="edit_master btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>  
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
@endsection
@section('css')
<style type="text/css">
    .modal-xl {
        max-width: 90% !important;
    }
	.modal-item {
        max-width: 70% !important;
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
    li.select2-results__option strong.select2-results__group:hover {
      background-color: #6a6a6a;
      color:#fff;
      cursor: pointer;
    }
	td.text-middle{
		vertical-align:middle;
		text-align:center;
	}
	td.text-center{
		text-align:center;
	}
	td.text-score{
		vertical-align:middle;
		text-align:center;
		font-size:16px;
		font-weight:bold;
	}
	th.th-text-score{
		width:10px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
	th.th-text-date{
		width:80px;;
		vertical-align:middle;
		text-align:center;
		font-weight:bold;
	}
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let id_talent = 0;
let statusData = [
    {
        id: "A",
        text: "Active"
    },
    {
        id: "I",
        text: "Inactive"
    }
];

var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('expense_product.save') }}";
			$(this).closest(".card").find("expenseForm").submit();
		  });

		  $(".edit_master").on("click",function(){
			AjaxUrl = "{{ route('expense_product.update') }}";
			$(this).closest(".card").find("expenseForm").submit();
		  });	
	
	 $('#expenseForm').submit(function (e) {
            e.preventDefault();			
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#expenseForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: AjaxUrl,
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
                                $('#myModal').modal('hide');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
								   $('#expense_table').DataTable().ajax.reload();
								   }
								);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
                            });
                        }
                    },
					complete: function(){
						$('#loader').addClass('hidden');
					},
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
                                $("#" + key_temp + "Error").children("strong").text(errors[key][0]);																
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
	

function on_close_modal() {
	$('#expense_table').DataTable().ajax.reload(); 
}

function getBranch(id_grade_expense) {
    $.ajax({
        url: "{{ route('expense_product.filled') }}",
        method: "GET",
        data: {
            id_grade_expense
        },
        success: function (data) {
            $("#id_branch").val(data[0].id_branch);
            $('#id_branch').trigger('change');
        }
    });
}

$(document).on('change', '#id_region', function (element) {
    let regionId = element.target.value;
    $.ajax({
        url: "{{ route('expense_product.branch') }}",
        method: "GET",
        data: {
            region_id: regionId
        },
        beforeSend: function () {
            $('#id_branch').children().remove();
            $('#id_branch').val(null).trigger('change');
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            if(response.length > 0) {
                $('#id_branch').attr('disabled', false);
            } else {
                $('#id_branch').attr('disabled', true);
            }
            $('#id_branch').select2({
                data: response
            });
            let id_grade_expense = $('#id_grade_expense').val();
            if(id_grade_expense) {
                getBranch(id_grade_expense)
            }
        },
        complete: function () {
            $('#loader').addClass('hidden');
        }
    });
});

function onExistingModalLoad(id_grade_expense) {
    $('#id_grade_expense').val(id_grade_expense);
    $('#status').select2({
        data: statusData
    });
    $.ajax({
        url: "{{ route('expense_product.filled') }}",
        method: "GET",
        data: {
            id_grade_expense
        },
        success: function (data) {
            $.ajax({
                url: "{{ route('expense_product.initial') }}",
                method: "GET",
                success: function (response) {
                    $('#id_region').select2({
                        data: response.regions
                    })
                    $('#job_grade').select2({
                        data: response.job_grades
                    })
                    $('#id_product').select2({
                        data:response.products
                    })
                    $('#id_position_routing').prepend('<option></option>').select2({
                        data: response.position_routing,
                        allowClear: true,
                        placeholder: "Leave empty for all position"
                    }).val(data[0].id_position_routing).trigger('change');
                    
                    $("#job_grade").val(data[0].id_job_grade);
                    $('#job_grade').trigger('change');
                    $("#id_product").val(data[0].id_product);
                    $('#id_product').trigger('change');
                    $("#id_region").val(data[0].id_region);
                    $('#id_region').trigger('change');
                    
                    $('#description').val(data[0].description);
                    $('#min_price').val(data[0].min_price);
                    $('#max_price').val(data[0].max_price);
                    $('#notes').val(data[0].notes);
                    $("#status").val(data[0].status);
                    $('#status').trigger('change');
                    // console.log(data[0])
                }
            })
        }
    })
}


function onModalLoad() {
    $.ajax({
        url: "{{ route('expense_product.initial') }}",
        method: "GET",
        success: function (response) {
            $('#id_region').select2({
                placeholder: "Select region",
                data: response.regions,
                allowClear: true,
            })
            $('#job_grade').select2({
                data: response.job_grades
            })
            $('#id_product').select2({
                data:response.products
            })
            $('#id_product').trigger('change');
            $('#status').select2({
                data: statusData
            })
            $('#id_position_routing').prepend('<option></option>').select2({
                data: response.position_routing,
                allowClear: true,
                placeholder: "Leave empty for all position"
            });
        }
    })
}

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#expenseForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Form Expense Product");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
    $.ajax({
			url: "{{ route('expense_product.modal_detail') }}",
			data:{expense_product:0},
			success: function(result){
                $("#contentBody").html(result);
                $("#myModal").modal('show'); 
                onModalLoad();
		}
	});
}

function loadedit(id_grade_expense){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#expenseForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Expense Product");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");
    $.ajax({
			url: "{{ route('expense_product.modal_detail') }}",
			data:{expense_product:id_grade_expense},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
            onExistingModalLoad(id_grade_expense);
		}
	});
}

$(document).ready(function(){
	
	$('#expense_table').DataTable({
            processing: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('expense_product.index') }}",
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
                    data: 'id_grade_expense',
                    defaultContent: '',
                    orderable: false
                },
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'job_grade', name: 'id_job_grade' },
                { data: 'product', name: 'id_product' },
                { data: 'region', name: 'id_region' },
                { data: 'description', name: 'description' },
                { data: 'min_price', name: 'min_price' },
                { data: 'max_price', name: 'max_price' },
                { data: 'position', name: 'position' },
                { data: 'notes', name: 'notes' },
    			{ data: 'action', name: 'action', orderable: false },
            ],
         
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
		
});


$(document).on('click', '.delete', function (event) {
	id_grade_expense = $(this).attr('id');
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
			   url:"{{ url('cash_advance/cash_advance_settings/expense_product/destroy').'/' }}"+id_grade_expense,
			   success:function(response)
			   {
				setTimeout(function(){				 
					if (response.status == 'true') {
							$('#myModal').modal('hide');
							swal({
								icon: 'success',
								title: 'Data Deleted!',
								text: response.message
							}).then(function(){ 
								$('#confirmModal').modal('hide');
								$('#expense_table').DataTable().ajax.reload();		
							   }
							);
					} else {
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! '+response.message,
						});
					}
				}, 50);
			   },
			   error: function (response) {                      
						swal({
							icon: 'error',
							title: 'Oops...',
							dangerMode: true,
							text: 'Something went wrong! [Unknown Error]'
						});
                    }
			  })
        }
    });
});


</script>
@endsection