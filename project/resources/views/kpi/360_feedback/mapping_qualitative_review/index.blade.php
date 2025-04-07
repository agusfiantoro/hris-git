@extends('adminlte::page')
@section('title', 'Mapping 360 Feedback')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Mapping 360 Feedback (Qualitative)</h5>
                
            </div>
       
			<div class="card-body">              
			 <form method="post" id="genForm">
				{{ csrf_field() }}
					<div class="row">	
							<div class="col-md-5">
								<select id="emp_participant" name="emp_participant[]" class="form-control form-control-md select2" multiple="multiple" style="width: 100%;"></select>
							</div>
							<div class="col-md-4">
								<select id="gen_period" name="gen_period" class="form-control form-control-md select2" style="width: 100%;"></select>
								<span class="invalid-feedback" role="alert" id="gen_periodError">
                                        <strong></strong>
                                </span>
							</div>
							<div class="col-md-3">
								<button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
								<button type="submit" class="btn btn-sm btn-success" id="btn-filter-node"><i class="fa fa-refresh"></i> Generate Mapping</button>
							</div>
					</div>
					<div class="row pull-right" style="margin-top:5px;">	
						<button type="button" class="btn btn-sm btn-primary" onclick="loadmail()" style="margin-left:10px;"><i class="fas fa-envelope"></i> List Send Mail</button>
						<button type="button" id="upload" class="new btn btn-sm btn-primary" style="margin-left:5px;"><i class="fas fa-upload"></i> Import Data Peers</button>
					</div>
					  
				</form>
				<br>
				<div class="div_datatable" style="display:none;">
					<button type="button" class="btn btn-default pull-left advanced">Advanced Search</button>
						<br>
						<br>
						<table id="qualitative_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						 <thead>
						  <tr>				   
							<th></th>
							<th></th>
							<th>No</th>
							<th>NIK Dinilai</th>
							<th data-priority="2">Nama Dinilai</th>
							<th>Periode</th>
							<th><center>Nama Penilai</center></th>
							<th data-priority="1" style="text-align:center;" width=50>Action</th>
						  </tr>
						 </thead>
						</table>
				</div>
			</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_qualitative"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="qualitativeForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Mapping 360 Feedback</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">NIK</label>
                                <div class="col-sm-8">
                                    <input type="hidden" id="id_employee_participant" name="id_employee_participant">
                                    <input type="hidden" id="id_period" name="id_period">
                                    <input type="text" id="nik_dinilai" class="form-control form-control-sm" readonly>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Name</label>
                                <div class="col-sm-8">
                                    <input type="text" id="name_dinilai" class="form-control form-control-sm" readonly>
                                </div>
                            </div>	
							<div class="row">
                                <label class="col-sm-4 col-form-label">Position</label>
                                <div class="col-sm-8">
                                    <input type="text" id="position_dinilai" class="form-control form-control-sm" readonly>                                    
                                </div>
                            </div>	
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grade</label>
                                <div class="col-sm-8">
                                    <input type="text" id="grade_dinilai" class="form-control form-control-sm" readonly>                                    
                                </div>
                            </div>																														
                        </div>
						<div class="col-md-6">    
							<div class="row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <input type="text" id="depart_dinilai" class="form-control form-control-sm" readonly>                                    
                                </div>
                            </div>	
							<div class="row">
                                <label class="col-sm-4 col-form-label">Branch</label>
                                <div class="col-sm-8">
                                    <input type="text" id="branch_dinilai" class="form-control form-control-sm" readonly>                                  
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Region</label>
                                <div class="col-sm-8">
                                    <input type="text" id="region_dinilai" class="form-control form-control-sm" readonly>
                                </div>
                            </div>																														
                        </div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_qualitative_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_qualitative-details" data-toggle="pill" href="#qualitative-details" role="tab" aria-controls="link_tab_qualitative-details" aria-selected="true">Data Penilai <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_qualitative_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="qualitative-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">											
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_qualitative_detail" style="margin-left:20px;"><span class="fas fa-plus"></span> Add Penilai</button>											
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_qualitative_detail" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Penilai</th>
                                                        <th style="width:250px;">Grade</th>
                                                        <th style="width:100px;">Type</th>
                                                        <th style="white-space:nowrap;">Score</th>
                                                        <th style="white-space:nowrap;">Submit</th>
														<th style="white-space:nowrap;">Cross Company</th>
                                                        <th style="white-space:nowrap;">Status</th>
                                                        <th style="width:200px;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_qualitative_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_qualitative_detailError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			     <div style="display:none;">
                <table id="sample_table_qualitative">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td class="input-group-append">
								<input name="mapping[0][id_qualitative_appraisers]" id="mapping_0_id_qualitative_appraisers" type="hidden" class="form-control form-control-sm id_qualitative_appraisers_input">		
                                <select name="mapping[0][id_employee_appraisers]" id="mapping_0_id_employee_appraisers" class="form-control form-control-sm select2 id_employee_appraisers_input" style="width: 85%;"></select>
                                <span class="invalid-feedback id_employee_appraisers_input_error" role="alert" id="mapping_0_id_employee_appraisersError">
                                    <strong></strong>
                                </span>						
                        </td>
						<td>
                                <input type="text" id="mapping_0_grade_penilai" class="form-control form-control-sm grade_penilai_input" readonly>
                        </td>
						<td>
								<select name="mapping[0][appraisers_hierarchy]" id="mapping_0_appraisers_hierarchy" class="form-control form-control-sm select2 appraisers_hierarchy_input" style="width: 100%;">
                                </select>
                        </td>                  
						<td>
                                <input type="text" name="mapping[0][subtotal_score]" id="mapping_0_subtotal_score" class="form-control form-control-sm subtotal_score_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                                <span class="invalid-feedback subtotal_score_input_error" role="alert" id="mapping_0_subtotal_scoreError">
                                    <strong></strong>
                                </span>
                        </td>
						<td style="vertical-align:middle;" align="center">
                                <span id="mapping_0_submitted" class="submitted_input"></span>
                        </td>
						<td>
								<select name="mapping[0][id_cross_company]" id="mapping_0_id_cross_company" class="form-control form-control-sm select2 id_cross_company_input" style="width: 100%;" readonly>
                                </select>
                        </td>
						<td>
								 <select name="mapping[0][status]" id="mapping_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="mapping_0_statusError">
                                    <strong></strong>
                                </span>
                        </td>
                    <td>
                    <center>
                        <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"><span class="far fa-trash-alt"></span></button>&nbsp;
                        <button type="button" class="reset-record btn btn-xs btn-danger" title="Reset"><span class="fas fa-registered"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
            </div>
       

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
                <h4 align="center" style="margin:0;">Are you sure reset this data?</h4>
            </div>
            <div class="modal-footer">
             <button type="button" name="ok_button" id="ok_button" class="btn btn-danger">OK</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div id="uploadModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Import Data Peers</h2>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body card">
                <form id="upload_form">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Upload File :</label>
                    <div class="col-md-5">
                        <div class="custom-file">
                            <input type="file" name="attachment" class="custom-file-input" id="attachment">
                            <span class="invalid-feedback" role="alert" id="attachmentError"></span>
                            <label class="custom-file-label"><i>File (.xlsx / .xls)</i></label>
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="submit_upload" class="new btn btn-lg btn-success" ><i class="fas fa-upload"></i> Upload</button>
                    </div>
                </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<div class="modal-header">
			<h5 class="modal-title">List Send Mail</h5>
			<button type="button" onclick="javascript:window.location.reload()" class="close advclose" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
        </div>
      <div class="modal-body" id="contentBody">
      </div>
      <div class="modal-footer">
        <button type="button" onclick="javascript:window.location.reload()" class="btn btn-default advclose" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection
@section('css')
<style type="text/css">
    .modal-lg, .modal-xl {
        max-width: 90% !important;
    }
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #e9ecef;;
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
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_id_employee_participant = "";
let global_qualitative_detail = 0;
let global_id_employee_appraisers = [];
let global_id_cross_company = [];
let list_app_custom = {};

let global_appraiser_type = [
			{
				id: 'direct',
				text: 'Direct Line'
			},
			{
				id: 'subordinat',
				text: 'Subordinat'
			},
			{
				id: 'peers',
				text: 'Peers'
			},
			{
				id: 'self',
				text: 'Self'
			},			
		];

function loadmail(){
	 $("#contentBody").html('');
    $.ajax({
		url: "{{ route('qualitative.modal_mail') }}",
		success: function(result){
        //alert("success"+result);
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    }});
}

    $(function () {	
			
		 $('#qualitativeForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();			
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#qualitativeForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_employee_participant == '' ? "{{ route('qualitative.save') }}" : "{{ route('qualitative.update') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_qualitative').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#qualitative_table').DataTable().ajax.reload();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
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
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_qualitative_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
								}
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


        $(document).on('click', '#new_qualitative_detail', function (event) {
            var content = jQuery('#sample_table_qualitative tr'),
                    size = global_qualitative_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_qualitative_appraisers_input').attr('id', 'mapping_' + size + '_id_qualitative_appraisers');
            element.find('.id_qualitative_appraisers_input').attr('name', 'mapping[' + size + '][id_qualitative_appraisers]');
			
			element.find('.id_employee_appraisers_input').attr('id', 'mapping_' + size + '_id_employee_appraisers');
            element.find('.id_employee_appraisers_input').attr('name', 'mapping[' + size + '][id_employee_appraisers]');
			element.find('.id_employee_appraisers_input').attr('id-app', size);
            element.find('.id_employee_appraisers_input_error').attr('id', 'mapping_' + size + '_id_employee_appraisersError');
			if (!(event.originalEvent === undefined)){ // change by human
				element.find('.id_employee_appraisers_input').select2({
					placeholder: "Select Employee Penilai",
				//	allowClear: true,
					data: global_id_employee_appraisers
				});
				element.find('.id_employee_appraisers_input').val('').trigger('change', [true]);
            } 
			
			element.find('.grade_penilai_input').attr('id', 'mapping_' + size + '_grade_penilai');
			
			element.find('.appraisers_hierarchy_input').attr('id', 'mapping_' + size + '_appraisers_hierarchy');
			element.find('.appraisers_hierarchy_input').attr('name', 'mapping[' + size + '][appraisers_hierarchy]');
			element.find('.appraisers_hierarchy_input').select2({
				data : global_appraiser_type
			});
			element.find('.appraisers_hierarchy_input').attr('readonly',false);
		/*	
			let subordinat = 0;
			let self = 0;
			$.each(global_appraiser_type, function(idx, item) {
				 if(item.id == 'subordinat'){
					subordinat = item.id;
				 }	
				 if(item.id == 'self'){
					self = item.id;
				 }																	
			});
		
			var aaList = $("option", element.find('.appraisers_hierarchy_input').select2());
			console.log(aaList);
			$.each(aaList, function(idx, item) {
					$("option[value='"+subordinat+"']").attr('disabled',true);
					$("option[value='"+self+"']").attr('disabled',true);
			});	
         */  			
			element.find('.subtotal_score_input').attr('id', 'mapping_' + size + '_subtotal_score');
            element.find('.subtotal_score_input').attr('name', 'mapping[' + size + '][subtotal_score]');
            element.find('.subtotal_score_input').val('0');
			
			element.find('.submitted_input').attr('id', 'mapping_' + size + '_submitted');
			element.find('.submitted_input').html('<span class="badge badge-danger">NO</span>');
			
			element.find('.id_cross_company_input').attr('id', 'mapping_' + size + '_id_cross_company');
            element.find('.id_cross_company_input').attr('name', 'mapping[' + size + '][id_cross_company]');
			element.find('.id_cross_company_input').prepend('<option selected></option>').select2({
                data: global_id_cross_company
            });
			
			element.find('.status_input').attr('id', 'mapping_' + size + '_status');
            element.find('.status_input').attr('name', 'mapping[' + size + '][status]');
            element.find('.status_input_error').attr('id', 'mapping_' + size + '_statusError');
			element.find('.status_input').select2();
			
            element.appendTo('#table_qualitative_body');
			 $('#table_qualitative_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

		$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
			
            $('#table_qualitative_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

  $(document).on('click', '.edit', function () {
            let id_employee_participant = $(this).attr('id');
            global_id_employee_participant = id_employee_participant;
            showQualitative(id_employee_participant);

            $('#modal_form_qualitative').modal('show');
        });

    });
	
$(document).on('click', '.reset-record', function (event) {
		id_qualitative_appraisers = $(this).attr('id');
        let id_employee_participant = $(this).attr('id_employee_participant');
		event.preventDefault();
		swal({
			title: 'Are you sure?',
			text: 'This record will be reset!',
			icon: 'warning',
		   buttons: true,
			dangerMode: true,
			  confirmButtonColor: '#3085d6',
			  cancelButtonColor: '#d33',
			  confirmButtonText: 'Yes, reset it!'
		}).then(function(value) {
			if (value) {
				$.ajax({
				   url:"mapping_qualitative_review/reset_appraiser/"+id_qualitative_appraisers,
				   success:function(data)
				   {
					setTimeout(function(){
					 $('#confirmModal').modal('hide');
					 swal({
						title: "Data Reset!",
						  icon: "success",
						   buttons: {confirm : {className:'btn-success'},},
						}).then(ok => {
		                    showQualitative(id_employee_participant)
					//		location.reload();
                            // $('#modal_form_qualitative').modal('hide');
						});
					}, 50);
				   }
				  })
			}
		});
	});
	




$(document).ready(function(){
	
    $('#upload').click(function(){
        $('#uploadModal').modal('show');
    });

    $('#submit_upload').click(function(){
        upload();
    });

    $('#attachment').change(function(){
        let file = $("#attachment")[0].files[0]; 
        $("label.custom-file-label").html('<i>'+file.name+'</i>');
    });
	
	$(document).on('click', '#search_period', function () {
		get_datatable();
	});

	$(document).on("click", ".advanced", function () {
			$('.cf').select2({width:'100%'});
			if($("#cf").css('display') == 'none'){
				$("#cf").show("slow");
			}
			else {
				$("#cf").hide("slow");
			}		
		});

	const get_datatable = async () => {
		$(".div_datatable").show();
		let myData = {
			employee: $("#emp_participant").val() == '' ? null : $("#emp_participant").val(),
			period: $("#gen_period").val() == '' ? null : $("#gen_period").val(),
		};
    $('#qualitative_table').DataTable({
        processing: true,
		responsive: true,
		destroy: true,
        ajax: {
			url: "<?= url('kpi/360_feedback/mapping_qualitative_review/') . '?id_url=' ?>" + global_url_server,
			"data": myData,
			"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#qualitative_table').DataTable().ajax.reload();
				}
			},
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_employee_participant',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'nik_dinilai', name: 'nik_dinilai' },
            { data: 'name_dinilai', name: 'name_dinilai' },
            { data: 'period', name: 'period' },
            { data: 'penilai', name: 'penilai', render: function ( data, type, row ) {
			//	console.log(row.penilai)
					var dat = data.split('|');
					var d='';
					d +='<div class="row">';
					$.each(dat, function (i, item) {
							d +='<div align="center" class="col-md-4" style="padding-bottom:4px;">'; 
							if(item.split('-')[1] == 't'){
								d += '<span style="white-space:normal;font-size:12px;" class="badge badge-success">'+item.split('-t')[0]+'</span>';
							}
							else{
								d += '<span style="white-space:normal;font-size:12px;" class="badge badge-danger">'+item.split('-f')[0]+'</span>';
							}
							d +='</div>';								
                    });
					d +='</div>';
				//	console.log(dat);
					return d;
				} 
			},
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {	
					return '<div align="center">'+data+'</div>';
				} 
			},
        ],
		"fnInitComplete": function (oSettings) {
			   $('#qualitative_table_wrapper .column-filter-widget:eq(6)').css('display','none').change();
			   $('#qualitative_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
			   $('#qualitative_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
			}
    });
	
	}
	
	
	refresh_data();

});

function refresh_data() {
        /**************** Load Menu dropdown **************************/ 		
		get_employee_filter();
		get_employee();
		get_period();
		get_cross_company();
}

$('#genForm').submit(function (e) {
		$(".invalid-feedback").children("strong").text("");
        $("#genForm input").removeClass("is-invalid");
		e.preventDefault();
		let formData = $(this).serializeArray();
		$.ajax({
		 type: 'POST',
		headers: {
			Accept: "application/json",
		},
		url:"{{ route('qualitative.generate') }}",
		data:formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');		
		},
		 success:function(msg){ 
			if (msg.status == 'true') {
				 swal({
					title: "Data Generated!",
					icon: "success",
					buttons: {
						confirm: {
							className: 'btn-success'
						},
					},
				}).then(ok => { 
					$('#search_period').trigger('click');
				//	$('#qualitative_table').DataTable().ajax.reload(); 
				});
			} 
			else{
				 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! ['+msg.message+']'
				});
			}
		},
		error: function (msg) {
			if (msg.status === 422) {
				let errors = msg.responseJSON.errors;
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
					text: 'Something went wrong! ['+msg.message+']'
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
	});	 
});
	
	
function upload(){
    var formData = new FormData($('#upload_form')[0]);
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "<?= url('kpi/360_feedback/mapping_qualitative_review/upload_review') ?>",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formData,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            $('#loader').addClass('hidden')
            if (response.status == 'true') {
                swal({
                    icon: 'success',
                    title: "Success",
                    text: response.message,
                }).then(function(){ 
                    location.reload();
                });
            } else {
                if(response.status == 'false_other'){
                    swal({
                        icon: 'error',
                        dangerMode: true,
                        content: {
                            element: "div",
                            attributes: {
                                innerText: response.message,
                                className: "swal-red",
                            },
                        },
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                } else {
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: 'Something went wrong! '+response.message,
                    }).then(function(){ 
                        $('#loader').addClass('hidden')
                    });
                }
            }
        },
        error: function (response) {
            $('#loader').addClass('hidden')
            if (response.status === 422) {
                let errors = response.responseJSON;
                let err = "";
                Object.keys(errors).forEach(function (key) {
                    var key_temp = key.replaceAll(".", "_");
                    $("#" + key_temp).addClass("is-invalid");
                    $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
                    var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
                    err += errors[key][0]+"\n";
                });
                swal({
                    icon: 'error',
                    dangerMode: true,
                    content: {
                        element: "div",
                        attributes: {
                            innerText: err,
                            className: "swal-red",
                        },
                    },
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
            else {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! '+response.statusText,
                }).then(function(){ 
                    $('#loader').addClass('hidden')
                });
            }
        }
    });                 
}   
function get_period() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_period') ?>', function (data) {
			$('#gen_period').prepend('<option selected></option>').select2({
                placeholder: "Select Period",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_period();
        });	
}
function get_employee_filter() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_employee_filter') . '?id_url=' ?>' + global_url_server, function (data) {
			$('#emp_participant').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: data
            });
		}).fail(function (data) { // Call failed
            get_employee_filter();
        });	
}
function get_employee() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_employee') ?>', function (data) {
		//	var res = Object.values(data);
			global_id_employee_appraisers = data;
			$(global_id_employee_appraisers).each(function (i, val) {
                list_app_custom[val.id] = [global_id_employee_appraisers[i]];
            });
		}).fail(function (data) { // Call failed
            get_employee();
        });	
}

$(document).on('change', '.id_employee_appraisers_input', function (event, autofill) {
        // if($(this).select2('data').length > 0){
            if (event.originalEvent !== undefined || autofill == undefined){ // change by human
                let id_row = $(this).attr('id-app');
                get_grade($(this).val(), `#mapping_${id_row}_grade_penilai`);
                get_cross_company_emp($(this).val(), `#mapping_${id_row}_id_cross_company`);
            } 
        // }
    });
function get_grade(grade_desc, element) {
	  $.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_grade') . '?id_employee=' ?>' + grade_desc, function (data) {
            if(data.length > 0){
               $(element).val('');
               $(element).val(data[0].text);
            }
      });
}

function get_cross_company() {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_cross_company') ?>', function (data) {
			global_id_cross_company = data;
		}).fail(function (data) { // Call failed
            get_cross_company();
        });	
}
function get_cross_company_emp(cross_desc, element) {
	$.getJSON('<?= url('kpi/360_feedback/mapping_qualitative_review/get_cross_company_emp') . '?id_employee=' ?>' + cross_desc, function (data) {
			if(data.length > 0){
               $(element).val(data[0].id).trigger('change');
            }
			else{
				$(element).val('').trigger('change');
			}
		});	
}

const showApp = async (element='', id_employee_appraisers='', index) => {
	optionLocation = list_app_custom[id_employee_appraisers];
	$(element).html('').prepend('<option></option>').select2({
		placeholder: "Select Employee Penilai",
		data: optionLocation,
		allowClear: true,
	});
	$(element).val(id_employee_appraisers).trigger('change').attr('readonly', 'readonly');
	$(element).parent().append($(`<span style="cursor: pointer;" class="input-group-text fa fa-pencil form-control-sm" id="editApp${index}" onclick="editApp('${element}', '${id_employee_appraisers}', '${index}')" ></span>`));
} 

 const editApp = async (element='', id_employee_appraisers='', index) => {
        $(element).html('').prepend('<option></option>').select2({
            placeholder: "Select Employee Penilai",
            data: global_id_employee_appraisers,
            allowClear: true,
        });
        $(element).val(id_employee_appraisers).trigger('change').attr('readonly', false);
        $(`span#editApp${index}`).remove();
    }     

const showQualitative = async (id_employee_participant) => {
    $("#qualitativeForm")[0].reset();
    $("#table_qualitative_body").html("");
    $("#qualitativeForm .modal-title").html("<span class='fas fa-edit'></span> Edit Mapping 360 Feedback");
    $(".invalid-feedback").children("strong").text("");
    $(".table-invalid-feedback").children("strong").text("");
    $(".error-tab").html("");
    $("#qualitativeForm input").removeClass("is-invalid");
    $('#save_button').attr('class', 'btn btn-sm btn-primary');
    $('#save_button').html('<i class="fas fa-edit"></i> Update');

    $.ajax({
        url: "<?= url('kpi/360_feedback/mapping_qualitative_review/get_qualitative_edit') ?>",
        method: "GET",
        data: {id_employee_participant: id_employee_participant},
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            global_qualitative_detail = 0;
             $.each(response.mapping, function (i, item) {
                $('#new_qualitative_detail').trigger('click');
            });
            
            $('#id_employee_participant').val(response.id_employee_participant).trigger('change');
            $('#id_period').val(response.id_period).trigger('change');
            $('#nik_dinilai').val(response.nik_dinilai).trigger('change');
            $('#name_dinilai').val(response.name_dinilai).trigger('change');
            $('#position_dinilai').val(response.position_dinilai).trigger('change');
            $('#grade_dinilai').val(response.grade_dinilai).trigger('change');
            $('#depart_dinilai').val(response.depart_dinilai).trigger('change');
            $('#branch_dinilai').val(response.branch_dinilai).trigger('change');
            $('#region_dinilai').val(response.region_dinilai).trigger('change');
            
            setTimeout(function () {
                $('#table_qualitative_body tr').each(function (index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find('.id_qualitative_appraisers_input').val(response.mapping[index].id_qualitative_appraisers).trigger('change');
                    
                    let thisIdApp = response.mapping[index].id_employee_appraisers;
                    let element_app = `#mapping_${index}_id_employee_appraisers`;
                    showApp(element_app, thisIdApp, index)
                    
               //     $(this).find('.id_employee_appraisers_input').val(response.mapping[index].id_employee_appraisers).trigger('change');
                    $(this).find('.grade_penilai_input').val(response.mapping[index].grade).trigger('change');
                    $(this).find('.appraisers_hierarchy_input').val(response.mapping[index].appraisers_hierarchy).trigger('change');
                    $(this).find('.appraisers_hierarchy_input').attr('readonly',true);
                    $(this).find('.subtotal_score_input').val(response.mapping[index].subtotal_score).trigger('change');
                    if(response.mapping[index].submitted == 1){
                        $(this).find('.submitted_input').html('<span class="badge badge-success">YES</span>');
                    }
                    else{
                        $(this).find('.submitted_input').html('<span class="badge badge-danger">NO</span>');
                    }
                    $(this).find('.id_cross_company_input').val(response.mapping[index].id_cross_company).trigger('change');
                    $(this).find('.status_input').val(response.mapping[index].status).trigger('change');
                    if(response.mapping[index].status == 'I'){
                        $('#rec-'+index).css('background','#df6060');
                    }
                    $(this).find('.reset-record').attr('id', response.mapping[index].id_qualitative_appraisers).attr('id_employee_participant', id_employee_participant);

                /*  if(response.mapping[index].appraisers_hierarchy != 'peers'){
                        $(this).find('.delete-record').attr('data-id', index).css('display', 'none');
                    }
                */
                });                       
            }, 1000);
           
        },
        complete: function(){
            setTimeout(function () {
                $('#loader').addClass('hidden');
            }, 1000)
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
} 

</script>
@endsection