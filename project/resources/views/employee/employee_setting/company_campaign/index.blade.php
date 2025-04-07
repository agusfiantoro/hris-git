@extends('adminlte::page')
@section('title', 'Company Campaign')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Campaign</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" id="addCampaign"><i class="fas fa-plus"></i> Add Campaign</button>
                </div>
            </div>
			
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br><br>
					
				<table id="campaign_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 	<thead>
					  	<tr>
						   	<th></th>
							<th></th>
							<th>No</th>
							<th style="white-space:nowrap;">Reference Number</th>
							<th data-priority="2">Description</th>
							<th>Employee Request</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Poster</th>
	                        <th data-priority="1" style="display:inline;">Action</th>
					  	</tr>
				 	</thead>
				</table>
			</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_campaign"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form id="campaignForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title" id="title_modal">Add Campaign</h5>
                    <button type="button" onclick="window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<input name="id_company_campaign" id="id_company_campaign" type="hidden">	
                    	<div class="col-md-6">
                    		<div class="row">
	                            <label class="col-sm-4 col-form-label">Reference Number</label>
	                            <div class="col-sm-8">
	                                <input type="text" name="reference_number" id="reference_number" value="" readonly="readonly" class="form-control form-control-sm">
	                                <span class="invalid-feedback" role="alert" id="reference_numberError">
	                                    <strong></strong>
	                                </span>                                    
	                            </div>
	                        </div>
                        </div>
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="company" id="company" class="form-control form-control-sm select2" style="width: 100%;" readonly>
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                    	</div>
                    </div>

                    <div class="row">
                    	<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                        </div>
                    	
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-sm-4 col-form-label">Request By</label>
								<div class="col-sm-8">
                                    <select name="employee_request" id="employee_request" class="form-control form-control-sm select2" style="width: 100%;" readonly="readonly">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="employee_requestError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                    	</div>
                    </div>

                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input name="start_date" id="start_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-sm-4 col-form-label">Link</label>
								<div class="col-sm-8">
                                    <input type="text" name="link" id="link" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="linkError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                    	</div>
                    </div>

                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
                                <div class="col-sm-8">
                                    <input name="end_date" id="end_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="end_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    	<div class="col-md-6">
                    	</div>
                    </div>

                    <div class="row">
                    	<div class="col-md-6">
                    		<div class="row">
	                            <label class="col-md-4" style="margin-top:5px;">Image Poster</label>
								<div class="col-md-8">
								    <div class="custom-file">
								        <input type="file" name="image_poster" class="custom-file-input" id="image_poster">
								        <span class="invalid-feedback" role="alert" id="image_posterError">
								            <strong></strong>
								        </span>
								        <label class="custom-file-label" for="image_poster"><i>(JPG, PNG)</i></label>
								    </div>
								    <br>
                                	<img id="image_poster_preview" alt="" style="width: 150px; height: 100px;display: none;">
								</div>
							</div>
                        </div>
                    	<div class="col-md-6">
                    		<div class="row">
                                <label class="col-md-4" style="margin-top:5px;">Image Detail</label>
								<div class="col-md-8">
								    <div class="custom-file">
								        <input type="file" name="attachment" class="custom-file-input" id="attachment">
								        <span class="invalid-feedback" role="alert" id="attachmentError">
								            <strong></strong>
								        </span>
								        <label class="custom-file-label" for="attachment"><i>(JPG, PNG)</i></label>
								    </div>
								    <br>
                                	<img id="attachment_preview" alt="" style="width: 150px; height: 100px;display: none;">
								</div>
                            </div>
                    	</div>
                    </div>

                </div>
                <div class="modal-footer">
					<button type="submit" class="save btn btn-sm btn-success" id="saveCampaign"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button onclick="window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button> 
                </div>
            </form>
        </div>
    </div>
</div>
	
<div id="modal_form_campaign_delete" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <form id="campaignFormDelete">
        <div class="modal-content">
			<input name="id_company_campaign" id="id_company_campaign_delete" type="hidden">	
            <div class="modal-header">
                <h2 class="modal-title-delete">Confirmation</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <h4 align="center" style="margin:0;">Are you sure you want to remove this data?</h4>
            </div>
            <div class="modal-footer">
             	<button type="submit" id="btnDelete" class="btn btn-danger">Remove</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    	</form>
    </div>
</div>

@endsection

@section('css')
<style type="text/css">  
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
</style>
@stop

@section('scripts')
<script>
    const currentEmployee = { id: {{ $findEmployee->id_employee }}, text: '{{ $findEmployee->name }}' }
	const selectEmployeeRequest = async (idEmployee=null) => {
        let option = [];
        try {
            option = await $.ajax({
                url: "{{ url('employee/get_employee_by_status_and_access_group') }}",
                data:{ status:'A', return_id: 'id_employee'},
                success: function (res) {
                    $(`#employee_request`).html('');
                    $(`#employee_request`).select2({
                        placeholder: "Select Employee",
                        data: res.concat([currentEmployee]),
                        allowClear: true,
                    });
                    if(idEmployee != null){
                    	$(`#employee_request`).val(idEmployee).trigger('change', [true]);
                    }
                    return option;
                },
                error: function (data) {
                    selectEmployeeRequest();
                }
            });
            return option;
        } catch (error) {
            selectEmployeeRequest();
        }
    }

    const selectCompany = async (idCompany=null) => {
        let option = [];
        try {
            option = await $.ajax({
                url: "{{ url('employee/employee_setting/announcement/get_company') }}",
                success: function (res) {
                    $(`#company`).html('');
                    $(`#company`).select2({
                        placeholder: "Select Company",
                        data: res,
                        allowClear: true,
                    });
                    if(idCompany != null){
                    	$(`#company`).val(idCompany).trigger('change', [true]);
                    }
                    return option;
                },
                error: function (data) {
                    selectCompany();
                }
            });
            return option;
        } catch (error) {
            selectCompany();
        }
    }

    const dateRange = (startdate='', enddate='') => {
        let separator = '   to   ';
        let start = (startdate=='' || startdate==null) ? moment().format('YYYY-MM-DD') : startdate;
        let end = (enddate=='' || enddate==null) ? '' : enddate;

        // $('#daterange').daterangepicker({
        //     uiLibrary: 'bootstrap4',
        //     autoApply: true,
        //     opens: 'center',
        //     locale: {
        //         format: 'YYYY-MM-DD',
        //         separator: separator,
        //         closeText: 'Clear',
        //     },
        //     startDate: start, 
        //     endDate: end,
        // }, function(start, end, label) {
        // 	//if date is change
        //     $("#start_date").val(start.format('YYYY-MM-DD'));
        //     $("#end_date").val(end.format('YYYY-MM-DD'));
        // });

        $('#start_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
        $('#end_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
        $("#start_date").val(start);
        $("#end_date").val(end);
    }

    const getDatatable = () => {
    	var table = $('#campaign_table').DataTable({
		    processing: true,
		    responsive: true,
            ordering: true,
            destroy: true,
		    ajax: {
		        url: "{{ url('employee/employee_setting/company_campaign') }}",
		        error: function(jqXHR, textStatus, errorThrown) {
		            $('#campaign_table').DataTable().ajax.reload();
		        }
		    },
		    columns: [{
		            defaultContent: '',
		            orderable: false,
		        },
		        { // Checkbox select column
		            data: 'id_company_campaign',
		            defaultContent: '',
		            orderable: false
		        },
		        {
		            data: 'DT_RowIndex',
		        },
		        {
		            data: 'reference_number',
		        },
		        {
		            data: 'description',
		        },
		        {
		            data: 'employee_name',
		        },
		        {
		            data: 'start_date',
		        },
		        {
		            data: 'end_date',
		        },
		        {
		            data: 'image_poster',
		            render: function(data, type, row) {
		                if(row.image_poster_path != ''){
		                	return `<img src="${row.image_poster_path}" style="height:60px;">`;
		                }
		                return '';
		            }
		        },
		        {
		            data: 'action',
		            className: 'space',
		            orderable: false,
		            render: function(data, type, row) {
		            	let btnEdit = `<button type="button" name="edit" id="${row.id_company_campaign}" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> `;
		            	let btnDelete = `&nbsp;&nbsp;<button type="button" name="delete" id="${row.id_company_campaign}" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>`;

		            	return `${btnEdit + btnDelete}`;

		                if (access_create == 0) {
		                    $('.new').css('display', 'none');
		                }
		                if (access_edit == 0) {
		                    $('.edit').css('display', 'none');
		                }
		                if (access_delete == 0) {
		                    $('.delete').css('display', 'none');
		                }
		                if (access_print == 0) {
		                    $('.print').css('display', 'none');
		                }
		            }
		        },
		    ]
		});
    }

	$(document).ready(function(){
		selectEmployeeRequest("{{ $idEmployee }}")
		selectCompany("{{ session('id_company') }}")
		dateRange()
		getDatatable()

		$('#campaignForm').submit(function (e) {
			$(".invalid-feedback").children("strong").text("");
    		$("#campaignForm input").removeClass("is-invalid");
            $('#saveCampaign').attr('disabled',true).html('Please Wait');
            e.preventDefault();
            let formData = new FormData($('#campaignForm')[0]);

            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
                url: "{{ url('employee/employee_setting/company_campaign/store') }}",
                type: "POST",
                data: formData,
                enctype: 'multipart/form-data',
                processData: false,  // Important!
                contentType: false,
                cache: false,
                dataType: 'json',
                success: function (resp) {
                    if(resp.status=='true'){
                    	swal({
		                    icon: 'success',
		                    title: 'Success',
		                    text: resp.message,
                            showConfirmButton: false,
                            timer: 2000
		                }).then(function() {
		                    location.reload();
		                });
                        $('#saveCampaign').removeAttr('disabled').html('Save');
                    } else {
                        $('#saveCampaign').removeAttr('disabled').html('Save');
                        if($.type(resp.message) === 'string' || resp.message == null){
			                swal({
			                    icon: 'error',
		                    	title: 'Failed',
		                    	text: resp.message,
			                    showConfirmButton: false,
			                    timer: 2500,
			                });
			            } else {
	                        let errors = resp.message;
			                Object.keys(errors).forEach(function(key) {
			                    var key_temp = key.replaceAll(".", "_");
			                    $("#" + key_temp).addClass("is-invalid");
			                    $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
			                });
			            }
                    }
                },
                error: function (resp) {
                    $('#saveCampaign').removeAttr('disabled').html('Save');
                    swal({
	                    icon: 'error',
                    	title: 'Failed',
	                    showConfirmButton: false,
	                    timer: 2500,
	                }).then(function() {
	                    location.reload();
	                });
                }
            });
        });

        $('#campaignFormDelete').submit(function (e) {
            $('#btnDelete').attr('disabled',true).html('Please Wait');
            e.preventDefault();
            let formData = new FormData($('#campaignFormDelete')[0]);

            $.ajax({
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
                url: "{{ url('employee/employee_setting/company_campaign/delete') }}",
                type: "POST",
                data: formData,
                processData: false,  // Important!
                contentType: false,
                cache: false,
                dataType: 'json',
                success: function (resp) {
                    if(resp.status=='true'){
                    	swal({
		                    icon: 'success',
		                    title: 'Success',
		                    text: resp.message,
                            showConfirmButton: false,
                            timer: 2000
		                }).then(function() {
		                    location.reload();
		                });
                    } else {
                        $('#btnDelete').removeAttr('disabled').html('Remove');
          				swal({
		                    icon: 'error',
		                    title: 'Failed',
		                    text: resp.message,
                            showConfirmButton: true
		                });
                    }
                },
                error: function (resp) {
                    $('#btnDelete').removeAttr('disabled').html('Remove');
                    let errors = resp.responseJSON.errors;
	                swal({
	                    icon: 'error',
	                    title: 'Failed',
	                    text: errors,
                        showConfirmButton: true
	                });
                }
            });
        });
	});

	$(document).on('click', '#addCampaign', function (event) {
        $('#modal_form_campaign').modal('show');
        $('#title_modal').html("Add Campaign");
        $('#id_company_campaign').val('');
        $('#reference_number').val("{{ $reffNumber }}");
        $('#image_poster_preview').attr("src", '').hide();
    	$('#attachment_preview').attr("src", '').hide();
    });

	$(document).on('change', '#image_poster', function (event) {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $("#image_poster_preview").attr("src", event.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

    $(document).on('change', '#attachment', function (event) {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $("#attachment_preview").attr("src", event.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });

	$(document).on('click', '.edit', function() {
        $('#title_modal').html("Edit Campaign");
        $('#image_poster_preview').hide();
        $('#attachment_preview').hide();
		$('#modal_form_campaign').modal('show');
        let idCampaign = $(this).attr('id');

        $.ajax({
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}", Accept: "application/json",},
            url: "{{ url('employee/employee_setting/company_campaign/edit') }}",
            type: "POST",
            data: {id_company_campaign:idCampaign},
            success: function (result) {
            	if(result.status = 'true'){
            		res = result.data;
	            	$('#id_company_campaign').val(res.id_company_campaign);
	            	$('#reference_number').val(res.reference_number);
	            	$('#company').val(res.id_company).trigger('change');
	            	$('#description').val(res.description);
	            	$('#employee_request').val(res.id_employee_request).trigger('change');
	            	$('#link').val(res.link);

	            	if(res.image_poster_path != null){
	            		$('#image_poster_preview').attr("src", res.image_poster_path).show();
	            	}
	            	if(res.attachment_path != null){
	            		$('#attachment_preview').attr("src", res.attachment_path).show();
	            	}
					dateRange(res.start_date, res.end_date);
            	} else {
					$('#modal_form_campaign').modal('hide');
            		swal({
	                    icon: 'error',
	                    title: 'Error',
	                    text: resp.message,
                        showConfirmButton: false,
                        timer: 2000
	                });
            	}
            },
            error: function (data) {
            	swal({
                    icon: 'error',
                    title: 'Error, please reload',
                    showConfirmButton: false,
                    timer: 2000
                }).then(function() {
                    location.reload();
                });
            }
        });
    });

	$(document).on('click', '.delete', function() {
        let idCampaign = $(this).attr('id');
        $('#id_company_campaign_delete').val(idCampaign);
		$('#modal_form_campaign_delete').modal('show');
    });

</script>

@endsection