@extends('adminlte::page')
@section('title', 'Master Period')

@section('content')

<div class="row">
    <div class="col-12">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Period</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Period</button>
                </div>
            </div>

            <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
                <table id="period_table" style="width:100%;" class="nowrap table table-striped table-bordered table-hover datatable">
                    <thead>
                        <tr>				   
                            <th></th>
                            <th></th>
                            <th>No</th>
                            <th data-priority="2">Period Name</th>
                            <th>Period Type</th>
                            <th>Year</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th data-priority="1" style="width:100px;">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_master_period"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" id="master_periodForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Master Period</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Period Name</label>
                                <div class="col-sm-8">
									<input name="id_period" id="id_period" type="hidden">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Period Code</label>
                                <div class="col-sm-8">
                                    <input type="text" name="period_code" id="period_code" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="period_codeError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Period Type</label>
                                <div class="col-sm-8">
                                    <select name="function_type" id="function_type" class="form-control form-control-sm select2" style="width:100%;"></select>
                                    <span class="invalid-feedback" role="alert" id="function_typeError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Year</label>
                                <div class="col-sm-8">
                                    <input type="text" name="year" id="year" class="form-control form-control-sm" maxlength="4" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                    <span class="invalid-feedback" role="alert" id="yearError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
							
                        </div>
                        <div class="col-md-6">
                           <div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="end_dateError">
                                        <strong></strong>
                                    </span>       
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width:100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6"></div>
                    </div>
                </div>
				<div class="modal-footer">
                    <button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>
					<button type="submit" class="edit_period btn btn-sm btn-primary" id="edit_button"><i class="fas fa-edit"></i> Update</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript">    
    $(document).on('click', '.new', function () {
        $("#master_periodForm")[0].reset();
        $("#master_periodForm .modal-title").html("<span class='fas fa-plus'></span> Form Master Period");
        $(".invalid-feedback").children("strong").text("");
        $(".invalid-date").children("strong").text("");
        $("#master_periodForm input").removeClass("is-invalid");
        $("#master_periodForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class','btn btn-sm btn-success').css("display","block");
        $('#save_button').html('<i class="fas fa-save"></i> Save');
		$("#edit_button").css("display","none");
        $('#modal_form_master_period').modal('show');
    });
	
	var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('master_period.save') }}";
			$(this).closest(".card").find("master_periodForm").submit();
		  });

		  $(".edit_period").on("click",function(){
			AjaxUrl = "{{ route('master_period.update') }}";
			$(this).closest(".card").find("master_periodForm").submit();
		  });
		  
	$('#master_periodForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $("#master_periodForm input").removeClass("is-invalid");
            $("#master_periodForm select").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: "application/json"
                    },
					processData: false,  // Important!
					contentType: false,
					cache: false,
                    url: AjaxUrl,
					data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
						if (response.status == 'true') {
							 $('#modal_form_master_period').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function(){ 
								   location.reload();
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
					error: function (response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
                            });
                        }						
						else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
								text: 'Something went wrong! '+response.responseJSON.message,
                            });
                        }
                    },
					complete: function(){
						$('#loader').addClass('hidden');
					},
                });            
	});
	  

    $(document).on('click', '.edit', function () {
        let id_period = $(this).attr('id');
        $("#master_periodForm")[0].reset();
        $("#master_periodForm .modal-title").html("<span class='fas fa-edit'></span> Edit Master Period");
        $(".invalid-feedback").children("strong").text("");
        $("#master_periodForm input").removeClass("is-invalid");
        $("#master_periodForm textarea").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
		$("#save_button").css("display","none");
		$("#edit_button").css("display","inline");

        $.ajax({
            url: "<?= url('general_setting/company_setting/master_period/get_period_edit') ?>",
            method: "GET",
            data: {id_period: id_period},
            success: function (response) {
				$('#id_period').val(response.id_period).trigger('change');
                $('#description').val(response.description);
                $('#period_code').val(response.period_code);
                $('#function_type').val(response.id_function_type).trigger('change');
                $('#year').val(response.year);
                $('#start_date').val(response.start_date);
                $('#end_date').val(response.end_date);
                $('#status').val(response.status).trigger('change');
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
        $('#modal_form_master_period').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
		id_period = $(this).attr('id');
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
				   url:"master_period/destroy/"+id_period,
				   success:function(data)
				   {
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 $('#period_table').DataTable().ajax.reload();				 
					 swal({
						title: "Data Deleted!",
						  icon: "success",
						   buttons: {confirm : {className:'btn-success'},},
						}).then(ok => {
							location.reload();
						});
					}, 50);
				   }
				  })
			}
		});
	});


$(document).ready(function () {
		
        $('#period_table').DataTable({
            processing: true,
            responsive: true,
            ajax: {
                url: "{{ route('master_period.index') }}",
				error: function (jqXHR, textStatus, errorThrown) {
					$('#period_table').DataTable().ajax.reload();
				}
            },
            columns: [
                {
                defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
                data: 'id_period',
                defaultContent: '',
                orderable: false
				},
                {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                {data: 'description', name: 'description'},
                {data: 'period_type', name: 'period_type'},
                {data: 'year', name: 'year'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, render: function (data, type, row) {
                        return data;
                    }
                },
            ]
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
		
		refresh_data();

    });

function refresh_data() {
	$('#status').select2();	
		$('#start_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });
		$('#end_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
		
		get_period_type();
}

function get_period_type() {
	$.getJSON('<?= url('general_setting/company_setting/master_period/get_period_type') ?>', function (data) {
			$('#function_type').select2({
				data: data,
			});
		}).fail(function (data) { // Call failed
            get_period_type();
        });	
}
</script>
@endsection