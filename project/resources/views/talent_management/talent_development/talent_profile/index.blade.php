@extends('adminlte::page')
@section('title', 'Talent Profile')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Talent Profile</h5>
            </div>      
			<div class="card-body">
				<div class="form-group row">
					<!-- div class="col-md-6">
						<div class="row">
							<label class="col-md-4 col-form-label">Status</label>
							<div class="col-md-6">
								<select id="employee_status" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
								</select>
							</div>
						</div>
                    </div -->
					<div class="col-md-6">
						<div class="row">
							<label class="col-md-4 col-form-label">Select Talent Reco</label>
							<div class="col-md-8">
								<select id="employee_reco" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;">
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-6">	
						<div class="row">
							<label class="col-md-4 col-form-label">Select Employee</label>
							<div class="col-md-8">
								<select id="employee_search" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
							</div>
						</div>
					</div>
                </div>
				<div class="form-group row">
                    <div class="col-md-12 text-right">
                        <button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
                    </div>
                </div>
                <div class="div_datatable" style="display:none;"> 
					<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
						<br>
						<br>
	                <table id="employee_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
	                    <thead>
	                        <tr>				   
	                            <th></th>
	                            <th data-priority="2"></th>
	                            <th data-priority="3">No</th>
	                            <th data-priority="5">NIK</th>
	                            <th data-priority="4">Name</th>
	                            <th>Email</th>
	                            <th>Join Date</th>
	                            <th data-priority="6">Position</th>
	                            <th>Department</th>
	                            <th>Principal</th>
	                            <th data-priority="8">Region</th>
	                            <th data-priority="7">Branch</th>
	                            <th>Job Grade</th>
	                            <th>Status</th>
	                            <th data-priority="9">Employee Status</th>
	                            <th>Resign Date</th>
	                            <th data-priority="1" width=300>Action</th>
	                        </tr>
	                    </thead> 
	                </table>
	            </div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_profile"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
		<form method="POST" id="committeeForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="title_profile" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="contentProfile"></div>
		</form>
				<div style="display:none;">
					<table id="sample_table_risk">
						<tr id="" style="font-size:14px;">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
								<input name="risk[0][id_talent_profile_note]" id="risk_0_id_talent_profile_note" type="hidden" class="id_talent_profile_note_input">
								<select name="risk[0][id_routing]" id="risk_0_id_routing" class="form-control form-control-sm select2 id_routing_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_routing_input_error" role="alert" id="risk_0_id_routingError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="risk[0][id_category]" id="risk_0_id_category" class="form-control form-control-sm select2 id_category_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_category_input_error" role="alert" id="risk_0_id_categoryError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<div class="input-group">
								  <input type="text" autocomplete="off" name="risk[0][committee_date]" id="risk_0_committee_date" class="form-control form-control-sm committee_date_input " style="width:100%;">
								  <div class="input-group-append">
									<span class="input-group-text" style="font-size: 1.05rem;">
									  <i class="fas fa-calendar"></i>
									</span>
								  </div>
								</div>
								<span class="invalid-date committee_date_input_error" style="font-size:11px;color:#dc3545;" role="alert" id="risk_0_committee_dateError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="risk[0][id_activity]" id="risk_0_id_activity" class="form-control form-control-sm select2 id_activity_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_activity_input_error" role="alert" id="risk_0_id_activityError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<textarea name="risk[0][committee_note]" id="risk_0_committee_note" rows="3" class="form-control form-control-sm committee_note_input"></textarea>
								<span class="invalid-feedback committee_note_input_error" role="alert" id="risk_0_committee_noteError">
									<strong></strong>
								</span>	
							</td>
							
						<td style="white-space:nowrap;">
						<center>
							<button type="button" class="add-record-risk btn btn-xs btn-success save" data-id="0" onclick="risk_save(0)" title="Save" style="margin-right:5px;"><span class="fas fa-save"></span></button>
							<button type="button" id="risk_0_del_rec" class="delete-record-risk btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
				</div> 
				
				<div style="display:none;">
					<table id="sample_table_plan">
						<tr id="" style="font-size:14px;">
							<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
							<td>
								<input name="plan[0][id_talent_profile_note_plan]" id="plan_0_id_talent_profile_note_plan" type="hidden" class="id_talent_profile_note_plan_input">
								<select name="plan[0][id_routing_plan]" id="plan_0_id_routing_plan" class="form-control form-control-sm select2 id_routing_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_routing_plan_input_error" role="alert" id="plan_0_id_routing_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="plan[0][id_category_plan]" id="plan_0_id_category_plan" class="form-control form-control-sm select2 id_category_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_category_plan_input_error" role="alert" id="plan_0_id_category_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<div class="input-group">
								  <input type="text" autocomplete="off" name="plan[0][committee_date_plan]" id="plan_0_committee_date_plan" class="form-control form-control-sm committee_date_plan_input " style="width:100%;">
								  <div class="input-group-append">
									<span class="input-group-text" style="font-size: 1.05rem;">
									  <i class="fas fa-calendar"></i>
									</span>
								  </div>
								</div>
								<span class="invalid-date committee_date_plan_input_error" style="font-size:11px;color:#dc3545;" role="alert" id="plan_0_committee_date_planError">
									<strong></strong>
								</span>	
							</td>
							<td>
								<select name="plan[0][id_activity_plan]" id="plan_0_id_activity_plan" class="form-control form-control-sm select2 id_activity_plan_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_activity_plan_input_error" role="alert" id="plan_0_id_activity_planError">
									<strong></strong>
								</span>		
							</td>
							<td>
								<textarea name="plan[0][committee_note_plan]" id="plan_0_committee_note_plan" rows="3" class="form-control form-control-sm committee_note_plan_input"></textarea>
								<span class="invalid-feedback committee_note_plan_input_error" role="alert" id="plan_0_committee_note_planError">
									<strong></strong>
								</span>	
							</td>
							
						<td style="white-space:nowrap;">
						<center>
							<button type="button" class="add-record-plan btn btn-xs btn-success save-plan" data-id="0" onclick="plan_save(0)" title="Save" style="margin-right:5px;"><span class="fas fa-save"></span></button>
							<button type="button" id="plan_0_del_rec_plan" class="delete-record-plan btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
						</td>
						</tr>
					</table>
				</div> 
			
        </div>
	</div>
</div>
@endsection
@section('css')
<style type="text/css">
    .modal-lg {
        max-width: 90% !important;
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
	.modal{
		overflow:auto !important;
	}
	.custom-select:valid + .select2 .select2-selection{
	  border-color: #dc3545!important;
	}
	.input-group > .select2-container {
    width: auto !important;
    flex: 1 1 auto !important;
    margin-bottom: 5px;
  }

  .input-group > .input-group-append {
    margin-bottom: 5px;
  }

  .input-group > .select2-container .select2-selection--single {
      height: 100% !important;
      line-height: inherit !important;
      padding: 0.5rem 1rem!important;
  }
</style>
@endsection
@section('scripts')
<script src="{{ asset('vendor/datatables/js/dataTables.rowsGroup.js') }}"></script>
<script type="text/javascript">
let global_category = [];
let global_activity = [];
let global_position = [];
let global_category_plan = [];
let global_activity_plan = [];

let global_id_rec_risk = 0;
let global_id_rec_plan = 0;
let global_id_talent_profile_note = "";
let global_employee = 0;
let global_nik = "";
let nik_pdf = [];

function on_close_modal() {
	$("#modal_profile").modal('hide'); 
}

$(document).on('click', '#new_rec_risk', function () {
            var content = jQuery('#sample_table_risk tr'),
                    size = global_id_rec_risk++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-risk-'+size);
			element.find('.add-record-risk').attr('onclick', 'risk_save(' + size + ')');
			element.find('.add-record-risk').attr('data-id', size);
            element.find('.delete-record-risk').attr('data-id', size);
            element.find('.id_talent_profile_note_input').attr('id', 'risk_' + size + '_id_talent_profile_note');
            element.find('.id_talent_profile_note_input').attr('name', 'risk[' + size + '][id_talent_profile_note]');
			
			element.find('.id_routing_input').attr('id', 'risk_' + size + '_id_routing');
            element.find('.id_routing_input').attr('name', 'risk[' + size + '][id_routing]');
            element.find('.id_routing_input_error').attr('id', 'risk_' + size + '_id_routingError');
            element.find('.id_routing_input').prepend('<option selected></option>').select2({
                placeholder: "Select Projected Position ...",
                data: global_position,
            });

			element.find('.id_category_input').attr('id_risk', size);
			element.find('.id_category_input').attr('id', 'risk_' + size + '_id_category');
            element.find('.id_category_input').attr('name', 'risk[' + size + '][id_category]');
            element.find('.id_category_input_error').attr('id', 'risk_' + size + '_id_categoryError');
            element.find('.id_category_input').prepend('<option selected></option>').select2({
                placeholder: "Select Category ...",
                data: global_category,
            });
		/*	.on('change', function (e) {
				if(global_category.length > 0){
					get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
				}
            });
		*/	
			element.find('.committee_date_input').attr('id', 'risk_' + size + '_committee_date');
			element.find('.committee_date_input').attr('name', 'risk[' + size + '][committee_date]');
			element.find('.committee_date_input_error').attr('id', 'risk_' + size + '_committee_dateError');
			element.find('.committee_date_input').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				autoApply: true,
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
			
			element.find('.id_activity_input').attr('id', 'risk_' + size + '_id_activity');
            element.find('.id_activity_input').attr('name', 'risk[' + size + '][id_activity]');
            element.find('.id_activity_input_error').attr('id', 'risk_' + size + '_id_activityError');
            element.find('.id_activity_input').prepend('<option selected></option>').select2({
                placeholder: "Select Activity ...",
				allowClear: true,
            });
			
			element.find('.committee_note_input').attr('id', 'risk_' + size + '_committee_note');
			element.find('.committee_note_input').attr('name', 'risk[' + size + '][committee_note]');
			element.find('.committee_note_input_error').attr('id', 'risk_' + size + '_committee_noteError');
			
			element.find('.delete-record-risk').attr('id', 'risk_' + size + '_del_rec');
								
            element.appendTo('#table_rec_risk_body');
			 $('#table_rec_risk_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
	$(document).on('click', '.delete-record-risk', function () {
		var id = jQuery(this).attr('data-id');
		var id_del_risk = $("#risk_" + id + "_id_talent_profile_note").val();
		if(id_del_risk == ""){
			jQuery('#rec-risk-' + id).remove();
			$('#table_rec_risk_body tr').each(function (index) {				
				$(this).find('span.sn').html(index + 1);
			});
		//	return true;
		}
		else{
			swal({
				title: 'Are you sure?',
				text: 'This record will be Deleted!',
				icon: 'warning',
				 buttons: {
					cancel: {
					  text: "No",
					  value: false,
					  visible: true,
					  className: "",
					  closeModal: true,
					},
					confirm: {
					  text: "Yes",
					  value: true,
					  visible: true,
					  className: "",
					  closeModal: true
					}
				  }							 
			}).then(function(value) {
				if (value) {
					$.ajax({
						url: "{{route('committee.deleted')}}",
					//	type: 'POST',
						data:{id_talent_profile_note : id_del_risk},
						beforeSend: () => $('#loader').removeClass('hidden'),
						success: (res) => {
							swal({
								icon: 'success',
								title: 'Success',
								text: res.message
							})
						},
						error: (res) => {
							swal({
								icon: 'error',
								title: 'Error',
								text: res.message
							})
						},
						complete: () => {
							jQuery('#rec-risk-' + id).remove();
							$('#table_rec_risk_body tr').each(function (index) {				
								$(this).find('span.sn').html(index + 1);
							});
							
							$('#loader').addClass('hidden');						
						},
					})
				}
			});
		}
	return true;
	});

$(document).on('change', '.id_category_input', function (event, istrigger) { 
	var size = $(this).attr('id_risk');
	if(!istrigger){
		if(global_category.length > 0){
			get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
		}
	}
});	
	
function risk_save(counter) {	 
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$("#table_rec_risk_body tr td input").removeClass("is-invalid");
	$("#table_rec_risk_body tr td select").removeClass("is-invalid");
	$("#table_rec_risk_body tr td textarea").removeClass("is-invalid");
	$(".table-invalid-feedback").children("strong").text(""); 
	$(".error-tab").html("");
	let at_risk = [];
	let formData = {
        id_routing: $("#risk_" + counter + "_id_routing").val() == '' ? null : $("#risk_" + counter + "_id_routing").val(),
        id_category: $("#risk_" + counter + "_id_category").val() == '' ? null : $("#risk_" + counter + "_id_category").val(),
        committee_date: $("#risk_" + counter + "_committee_date").val() == '' ? null : $("#risk_" + counter + "_committee_date").val(),
        id_activity: $("#risk_" + counter + "_id_activity").val() == '' ? null : $("#risk_" + counter + "_id_activity").val(),
        committee_note: $("#risk_" + counter + "_committee_note").val() == '' ? null : $("#risk_" + counter + "_committee_note").val(),
    };
	at_risk[counter] = formData;
	global_id_talent_profile_note = $("#risk_" + counter + "_id_talent_profile_note").val();
	$.ajax({
	//	type: 'POST',
		headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: global_id_talent_profile_note == '' ? "<?= url('talent_management/talent_development/talent_profile/save') . '?counter=' ?>" + counter + "<?= '&id_employee='?>" + global_employee : "<?= url('talent_management/talent_development/talent_profile/update') . '?id_talent_profile_note=' ?>" + global_id_talent_profile_note + "<?= '&counter='?>" + counter,
		data: {risk:at_risk},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(response) {
		   if (response.status == 'true') {
			   $("#risk_" + counter + "_id_talent_profile_note").val(response.id_talent_profile_note);
				swal({
					icon: 'success',
					title: 'Success',
					text: response.message
				});
				
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: response.message
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function(response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					
				});
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			}
		}

	});
		
 }

$(document).on('click', '#new_rec_plan', function () {
            var content = jQuery('#sample_table_plan tr'),
                    size = global_id_rec_plan++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-plan-'+size);
			element.find('.add-record-plan').attr('onclick', 'plan_save(' + size + ')');
			element.find('.add-record-plan').attr('data-id', size);
            element.find('.delete-record-plan').attr('data-id', size);
            element.find('.id_talent_profile_note_plan_input').attr('id', 'plan_' + size + '_id_talent_profile_note_plan');
            element.find('.id_talent_profile_note_plan_input').attr('name', 'plan[' + size + '][id_talent_profile_note_plan]');
			
			element.find('.id_routing_plan_input').attr('id', 'plan_' + size + '_id_routing_plan');
            element.find('.id_routing_plan_input').attr('name', 'plan[' + size + '][id_routing_plan]');
            element.find('.id_routing_plan_input_error').attr('id', 'plan_' + size + '_id_routing_planError');
            element.find('.id_routing_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Projected Position ...",
                data: global_position,
            });
			
			element.find('.id_category_plan_input').attr('id_plan', size);
			element.find('.id_category_plan_input').attr('id', 'plan_' + size + '_id_category_plan');
            element.find('.id_category_plan_input').attr('name', 'plan[' + size + '][id_category_plan]');
            element.find('.id_category_plan_input_error').attr('id', 'plan_' + size + '_id_category_planError');
            element.find('.id_category_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Category ...",
                data: global_category_plan,
            });
		/*	.on('change', function (e) {
				if(global_category_plan.length > 0){
					get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
				}
            });
		*/	
			element.find('.committee_date_plan_input').attr('id', 'plan_' + size + '_committee_date_plan');
			element.find('.committee_date_plan_input').attr('name', 'plan[' + size + '][committee_date_plan]');
			element.find('.committee_date_plan_input_error').attr('id', 'plan_' + size + '_committee_date_planError');
			element.find('.committee_date_plan_input').daterangepicker({
				singleDatePicker: true,
				autoUpdateInput: false,
				autoApply: true,
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
			
			element.find('.id_activity_plan_input').attr('id', 'plan_' + size + '_id_activity_plan');
            element.find('.id_activity_plan_input').attr('name', 'plan[' + size + '][id_activity_plan]');
            element.find('.id_activity_plan_input_error').attr('id', 'plan_' + size + '_id_activity_planError');
		    element.find('.id_activity_plan_input').prepend('<option selected></option>').select2({
                placeholder: "Select Activity ...",
				allowClear: true,
            });
		
			element.find('.committee_note_plan_input').attr('id', 'plan_' + size + '_committee_note_plan');
			element.find('.committee_note_plan_input').attr('name', 'plan[' + size + '][committee_note_plan]');
			element.find('.committee_note_plan_input_error').attr('id', 'plan_' + size + '_committee_note_planError');
			
			element.find('.delete-record-plan').attr('id', 'plan_' + size + '_del_rec_plan');
								
            element.appendTo('#table_rec_plan_body');
			 $('#table_rec_plan_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
/*	$(document).on('click', '.delete-record-plan', function () {
		var id = jQuery(this).attr('data-id');
		jQuery('#rec-plan-' + id).remove();
		$('#table_rec_plan_body tr').each(function (index) {				
			$(this).find('span.sn').html(index + 1);
		});
		return true;
	});
*/	
	$(document).on('click', '.delete-record-plan', function () {
		var id = jQuery(this).attr('data-id');
		var id_del_plan = $("#plan_" + id + "_id_talent_profile_note_plan").val();
		if(id_del_plan == ""){
			jQuery('#rec-plan-' + id).remove();
			$('#table_rec_plan_body tr').each(function (index) {				
				$(this).find('span.sn').html(index + 1);
			});
		//	return true;
		}
		else{
			swal({
				title: 'Are you sure?',
				text: 'This record will be Deleted!',
				icon: 'warning',
				 buttons: {
					cancel: {
					  text: "No",
					  value: false,
					  visible: true,
					  className: "",
					  closeModal: true,
					},
					confirm: {
					  text: "Yes",
					  value: true,
					  visible: true,
					  className: "",
					  closeModal: true
					}
				  }							 
			}).then(function(value) {
				if (value) {
					$.ajax({
						url: "{{route('committee.deleted')}}",
					//	type: 'POST',
						data:{id_talent_profile_note : id_del_plan},
						beforeSend: () => $('#loader').removeClass('hidden'),
						success: (res) => {
							swal({
								icon: 'success',
								title: 'Success',
								text: res.message
							})
						},
						error: (res) => {
							swal({
								icon: 'error',
								title: 'Error',
								text: res.message
							})
						},
						complete: () => {
							jQuery('#rec-plan-' + id).remove();
							$('#table_rec_plan_body tr').each(function (index) {				
								$(this).find('span.sn').html(index + 1);
							});						
							$('#loader').addClass('hidden');						
						},
					})
				}
			});
		}
	return true;
	});

	
$(document).on('change', '.id_category_plan_input', function (event, istrigger) { 
	var size = $(this).attr('id_plan');
	if(!istrigger){
		if(global_category.length > 0){
			get_activity($(this).select2('data')[0].id,size,$(this).select2('data')[0].code);
		}
	}
});		

function plan_save(counter) {	 
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$("#table_rec_plan_body tr td input").removeClass("is-invalid");
	$("#table_rec_plan_body tr td select").removeClass("is-invalid");
	$("#table_rec_plan_body tr td textarea").removeClass("is-invalid");
	$(".table-invalid-feedback").children("strong").text(""); 
	$(".error-tab").html("");
	let at_plan = [];
	let formData = {
        id_routing: $("#plan_" + counter + "_id_routing_plan").val() == '' ? null : $("#plan_" + counter + "_id_routing_plan").val(),
        id_category: $("#plan_" + counter + "_id_category_plan").val() == '' ? null : $("#plan_" + counter + "_id_category_plan").val(),
        committee_date: $("#plan_" + counter + "_committee_date_plan").val() == '' ? null : $("#plan_" + counter + "_committee_date_plan").val(),
        id_activity: $("#plan_" + counter + "_id_activity_plan").val() == '' ? null : $("#plan_" + counter + "_id_activity_plan").val(),
        committee_note: $("#plan_" + counter + "_committee_note_plan").val() == '' ? null : $("#plan_" + counter + "_committee_note_plan").val(),
    };
	global_id_talent_profile_note = $("#plan_" + counter + "_id_talent_profile_note_plan").val();
	at_plan[counter] = formData;
	$.ajax({
	//	type: 'POST',
		headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: global_id_talent_profile_note == '' ? "<?= url('talent_management/talent_development/talent_profile/save') . '?counter=' ?>" + counter + "<?= '&id_employee='?>" + global_employee : "<?= url('talent_management/talent_development/talent_profile/update') . '?id_talent_profile_note=' ?>" + global_id_talent_profile_note + "<?= '&counter='?>" + counter,
		data: {plan:at_plan},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(response) {
		   if (response.status == 'true') {
			   $("#plan_" + counter + "_id_talent_profile_note_plan").val(response.id_talent_profile_note);
				swal({
					icon: 'success',
					title: 'Success',
					text: response.message
				});
				
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: response.message
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function(response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					
				});
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			}
		}

	});
		
 }
	
	$('#committeeForm').submit(function (e) {
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$("#committeeForm input").removeClass("is-invalid");
		$("#committeeForm select").removeClass("is-invalid");
		$("#committeeForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text(""); 
		$("#risk_error").html("");
		$("#plan_error").html("");
		e.preventDefault();
		let formData = $(this).serializeArray();
		
		$.ajax({
		type: 'POST',
		headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
		url: "<?= url('talent_management/talent_development/talent_profile/submit') . '?id_employee=' ?>" + global_employee,
		data: formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function(response) {
		   if (response.status == 'true') {
				swal({
					icon: 'success',
					title: 'Submit Successfully',
					text: response.message
				}).then(ok => {
					get_profile(global_employee,global_nik).then(function(value) {					
						$("#link_tab_rec-demo").removeClass("active");
						$("#rec-demo").removeClass("show").removeClass("active");
						$("#link_tab_rec-committe").addClass("active");
						$("#rec-committe").addClass("show").addClass("active");
					});					
				});								
			} else {
				swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: response.message
				});
			}
		},
		complete: function(){
			$('#loader').addClass('hidden');
		},
		error: function(response) {
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);					
					var tab_id = $("#" + key_temp + "Error").parentsUntil('div').parent().parent().find(".detail_error").attr("id");
					if(tab_id == 'risk_error'){
						$("#risk_error").html("<i class='fas fa-exclamation-circle'></i> Required");
					}
					if(tab_id == 'plan_error'){
						$("#plan_error").html("<i class='fas fa-exclamation-circle'></i> Required");
					}
				});
			} else {
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

$(document).ready(function(){
	getReco().then(function(value) {
        $('#employee_reco').html('');
        $('#employee_reco').select2({
            placeholder: "Select Talent Reco",
            data: value,
            allowClear: true,
        });
    });

	getEmployeeByAccessGroup().then(function(value) {
        $('#employee_search').html('');
        $('#employee_search').select2({
            placeholder: "Select Employee",
            data: value,
            allowClear: true,
        });
    });
	
	/*
	$('#employee_status').select2({
        placeholder: "Select Status",
        data: list_status,
        allowClear: true,
    });
	*/
});	

async function getReco() {
	$('#loader').removeClass('hidden');
//    let status = ['A'];
  	let currentPath = "<?= \Request::path() ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('talent_management/talent_development/talent_profile/get_reco') ?>', 
		function (res) {
			$('#loader').addClass('hidden');	
        });
        return result;
    } catch (error) {
     //   getEmployeeByAccessGroup();
    }
}

async function getEmployeeByAccessGroup() {
	$('#loader').removeClass('hidden');
//    let status = ['A'];
  	let currentPath = "<?= \Request::path() ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('talent_management/talent_development/talent_profile/get_employee') ?>'+'?id_url='+global_url_server, 
		function (res) {
			$('#loader').addClass('hidden');	
        });
        return result;
    } catch (error) {
     //   getEmployeeByAccessGroup();
    }
}

const get_projected = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_recomendation/get_projected') ?>',
            dataType: 'json',
            success: function (res) {
				global_position = res;
            },
        });
        return result;
    } catch (error) {
        get_projected();
    }	
}

const get_risk = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_risk') ?>',
            dataType: 'json',
            success: function (res) {
				global_category = res;
            },
        });
        return result;
    } catch (error) {
        get_risk();
    }	
}

const get_plan = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_plan') ?>',
            dataType: 'json',
            success: function (res) {
				global_category_plan = res;
            },
        });
        return result;
    } catch (error) {
        get_plan();
    }	
}

const get_activity = async (idData,counter,code,getVal=null) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/talent_profile/get_activity') ?>',
			data: {id: idData},
            dataType: 'json',
            success: function (response) {
				if (response.length > 0) {
					if(code == 'Flight_Risk'){
						$("#risk_" + counter + "_id_activity").empty();
						$("#risk_" + counter + "_id_activity").select2({
							data: response
						});	
						if(getVal){
							$("#risk_" + counter + "_id_activity").val(getVal).trigger('change');
						}
					}
					else if(code == 'Talent_Note'){
						$("#plan_" + counter + "_id_activity_plan").empty();
						$("#plan_" + counter + "_id_activity_plan").select2({
							data: response
						});	
						if(getVal){
							$("#plan_" + counter + "_id_activity_plan").val(getVal).trigger('change');
						}
					}
				}	
            },
        });
        return result;
    } catch (error) {
        get_activity(idData,counter,code);
    }	
}

/*
function get_activity_edit(counter,code,valActivity) {
	if(code == 'Flight_Risk'){
		$("#risk_" + counter + "_id_activity").val(valActivity).trigger('change');
	}
	if(code == 'Talent_Note'){
		$("#plan_" + counter + "_id_activity_plan").val(valActivity).trigger('change');
	} 
}
*/
/*
 function get_activity(idData,counter,code) {
	$.ajax({
		method: "GET",
		url : "{{url('talent_management/talent_development/talent_profile/get_activity')}}",
		data: {id: idData},
		success: function (response) {
			if (response.length > 0) {
				if(code == 'Flight_Risk'){
					$("#risk_" + counter + "_id_activity").empty();
					$("#risk_" + counter + "_id_activity").select2({
						data: response
					});	
				}
				else if(code == 'Talent_Note'){
					$("#plan_" + counter + "_id_activity_plan").empty();
					$("#plan_" + counter + "_id_activity_plan").select2({
						data: response
					});	
				}
			}	
		},
		error: function(response) {
			get_activity(idData,counter,code);		  
		}
	}); 
	
 }  
*/

const get_profile = async (id_emp,nik_emp) => {
	let result;
	global_employee = id_emp;
	global_nik = nik_emp;
	$("#contentProfile").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");			
	$("#title_profile").html('Talent Profile Card');
    try {
        result = await $.ajax({
			url: "{{ route('talent_profile.modal_talent_profile') }}",
			data:{
				global_emp:id_emp,
				global_nik:nik_emp,
			},
			success: function(result){
				$("#contentProfile").html(result);
				$("#modal_profile").modal('show');
			}
        });
        return result;
    } catch (error) {
        get_profile(id_emp,nik_emp);
    }	
}

/*
function get_profile(id_emp,nik_emp) {
	global_employee = id_emp;
	global_nik = nik_emp;
	$("#contentProfile").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");			
	$("#title_profile").html('Talent Profile Card');
	$.ajax({
		url: "{{ route('talent_profile.modal_talent_profile') }}",
		data:{
			global_emp:id_emp,
			global_nik:nik_emp,
		},
		success: function(result){
			$("#contentProfile").html(result);
			$("#modal_profile").modal('show');
		}
	});		
}
*/

$(document).on('click', '#search', function () {	
	get_datatable_employee();
});

	
$('.cf').select2({width:'100%'});
$('#advanced').click(function(){
	if($("#cf").css('display') == 'none'){
		$("#cf").show("slow");
	}
	else {
		$("#cf").hide("slow");
	}		
});	

function groupAll(data) {
	if(data.length > 0){
		let res = {
			id_employee: data[0].id_employee,
			nik_employee: data[0].nik_employee,
			id_number: data[0].identification_number,
			zip: true,
		};
		let param = objectToQueryString(res);
	//	let url = "{{ url('talent_management/talent_development/talent_profile/download') }}";
		$.ajax({
			url: "<?= url('talent_management/talent_development/talent_profile/download') ?>",
			method: "GET",
			data: param,
			success: function (response) {
			},
			error: function (response) {
			swal({
				icon: 'error',
				title: 'Oops...',
				dangerMode: true,
				text: response.message
			});
			}
		});
	//	return url+'?'+param;
	}
}

function downloadAll(arr_employee) {
	let url = "{{route('talent_profile.download_zip')}}?";
	url += $.param({data:arr_employee});
	$('#loader').addClass('hidden');
	window.open(url, '_new'+Math.random());
	return;
}
var emp_list = [];
const getNik = async (arr_employee) => {
	emp_list = [];
	$.each(arr_employee, function (i, item) {
		try {
			$.ajax({
				url: "<?= url('talent_management/talent_development/talent_profile/get_id') ?>",
				method: "GET",
				data: {id_employee: item},
				async: false,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
					nik_pdf.push(response[0].nik_employee);
					emp_list.push(response[0]);
					// groupAll(response);
				},
				complete: function(){
					
				},
				error: function (xhr) {
					$('#employee_table').DataTable().ajax.reload();
				}
			});
		} catch (error) {
			getNik(arr_employee);
		}
	});
	downloadAll(emp_list);
}	
	
const get_datatable_employee = async () => {
	let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons);	
		let btnPdf = {
			text: '<span class="far fa-file-archive-o" style="font-size:20px;"></span> Export All',
			className: 'btn btn-success btn-sm',
			action: function (e, dt, node, config) {
				let arr_employee = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
					return $(entry).attr('id_employee');
				});				
				if(arr_employee.length > 0){
					nik_pdf = [];
					
					getNik(arr_employee).then(function(res) {
						// downloadAll(arr_employee);
						$('#loader').addClass('hidden');
					});
				} else {
					swal({
						icon: 'warning',
						title: 'Warning',
						text: 'Please Select Row'
					});
				}
				
			}
		}
	
	dtButtons.push(btnPdf);
    $(".div_datatable").show();

    let myData = {
		id_url: global_url_server,
        idReco: $("#employee_reco").val() == '' ? null : $("#employee_reco").val(),
        nik: $("#employee_search").val() == '' ? null : $("#employee_search").val(),
    //    status: $("#employee_status").val() == '' ? null : $("#employee_status").val(),
    };
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:true,
		 paging:true,
		 searching:true,
		 lengthChange: true,
		 info: true,
		 dom: 'lBfWrtip<"clear">',
	});	
    $('#employee_table').DataTable({
		buttons: dtButtons,
        processing: true,
		pageLength: 10,
		responsive: true,
		destroy:true,
        ajax: {
            "headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
        //    url: "<?= url('talent_management/talent_development/talent_profile/') . '?id_url=' ?>" + global_url_server,
			url: "{{ route('talent_profile.index') }}",
            data: myData,
			error: function (jqXHR, textStatus, errorThrown) {
				if(jqXHR.hasOwnProperty("responseJSON")){
					console.log("True");
				}
				else{
					$('#employee_table').DataTable().ajax.reload();
				}
			}			
        },
		createdRow: function( row, data, dataIndex ) {
			$(row).attr('id_employee', data['id_employee']);
		},
		columnDefs: [{
        orderable: false,
    //    className: 'select-checkbox',
        targets: 1,
		 render: function(data, type, row, meta){            
                  data = '<div class="icheck-success d-inline"><input type="checkbox" class="dt-checkboxes"><label></label></div>';
               return data;
            },
		checkboxes: {
               selectRow: true,
			   selectAllRender: '<div class="icheck-success"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
            }
		}, {
			orderable: false,
			searchable: false,
			targets: -1
		}],
		select: {
		  style:    'multi+shift',
		  selector: 'td:nth-child(2)'
		},
        columns: [
			{
            defaultContent: '',
			orderable: false,
			},
			{   
				data: 'id_employee',
				defaultContent: '',
				orderable: false
			},
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nik_employee', name: 'nik_employee'},
            {data: 'employee_name', name: 'employee_name'},
            {data: 'private_mail', name: 'private_mail'},
            {data: 'join_date', name: 'join_date'},
            {data: 'position_routing', name: 'position_routing'},
            {data: 'department', name: 'department'},
            {data: 'principal', name: 'principal'},
            {data: 'region', name: 'region'},
            {data: 'branch', name: 'branch'},
            {data: 'job_grade', name: 'job_grade'},
            {data: 'status', name: 'status'},
            {data: 'employment_status', name: 'employment_status'},
            {data: 'resign_date', name: 'resign_date'},
            {data: 'action', name: 'action', className:'space' ,orderable: false, render: function (data, type, row) {					
					return data;
				}
            },
        ],
		"fnInitComplete": function (oSettings) {
		   $('#employee_table_wrapper .column-filter-widget:eq(13)').find("select option:contains('A')").attr('selected','selected').change();
		}
    });

}  

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(id_employee,nik_employee,id_number) {
	let res = {
        id_employee: id_employee,
        nik_employee: nik_employee,
        id_number: id_number,
        pdf: true,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('talent_management/talent_development/talent_profile/download') }}";
    window.open(url+'?'+param, '_blank');
}	
</script>
@endsection