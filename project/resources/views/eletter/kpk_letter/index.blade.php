@extends('adminlte::page')
@section('title', 'P2K Letter')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Penetapan BA P2K
        </h5>
        <div class="card-tools">
          <button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add BA P2K</button>
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="kpk_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th></th>
            <th data-priority="3"></th>
            <th data-priority="2">No</th>
            <th data-priority="4" style="text-align:center;">Reference Number</th>
            <th data-priority="5" style="text-align:center;">Letter Date</th>
            <th data-priority="6" style="text-align:center;">P2K Month</th>
            <th data-priority="7" style="text-align:center;">On Performance (P3M)</th>
            <th data-priority="12" style="text-align:center;">Company Type</th>
            <th data-priority="8" style="text-align:center;">Department</th>
            <th data-priority="9" style="text-align:center;">Created By</th>
            <th data-priority="10" style="text-align:center;">Type</th>
            <th data-priority="11" style="text-align:center;">Status</th>
            <th data-priority="1" width="80" class="text text-center">Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="kpkForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody" style="height:550px;overflow-y: auto;">
		  </div>
		  <div class="modal-footer">
			<button type="submit" class="btn btn-info btn-sm " id="save_and_submit" style="display:none;"></button>&nbsp;
			<button type="submit" class="btn btn-success btn-sm " id="save_and_draft" style="display:none;"></button>&nbsp;
		<!-- 	<button type="submit" class="save btn btn-sm btn-info" id="save_button"><i class="fas fa-paper-plane"></i> Save & Submit</button>&nbsp; -->
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>
		 <div style="display:none;">
			<table id="sample_table_rec">
				<tr id="" style="font-size:14px;">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
						<div class="is-loading">
							<input name="kpk[0][id_performance_evaluation]" id="kpk_0_id_performance_evaluation" type="hidden" class="form-control form-control-sm id_performance_evaluation_input">
							<select name="kpk[0][id_employee]" id="kpk_0_id_employee" class="form-control form-control-sm select2 id_employee_input" style="width: 100%;"></select>
							<span class="invalid-feedback id_employee_input_error" role="alert" id="kpk_0_id_employeeError">
								<strong></strong>
							</span>	
							<span id="load_pos_detail_0" class="spinner-border spinner-border-sm load_pos_detail_input" style="display:none;"></span>
						</div>																					
					</td>
					<td>
						<select name="kpk[0][id_group]" id="kpk_0_id_group" class="form-control form-control-sm select2 id_group_input" style="width: 100%;"></select>
						<span class="invalid-feedback id_group_input_error" role="alert" id="kpk_0_id_groupError">
							<strong></strong>
						</span>	
					</td>
					<td align="center">
							<input type="hidden" name="kpk[0][id_pos_detail]" id="kpk_0_id_pos_detail" class="form-control form-control-sm id_pos_detail_input">
							<span id="kpk_0_position_name" class="position_name_input"></span>
					</td>
					<td align="center">
							<span id="kpk_0_region" class="region_input"></span>
					</td>
					<td align="center">
							<span id="kpk_0_branch" class="branch_input"></span>
					</td>
					<td align="center">
							<span id="kpk_0_direct" class="direct_input"></span>
					</td>
					<td align="center">				
						<input type="hidden" name="kpk[0][obj_kpi]" id="kpk_0_obj_kpi" class="form-control form-control-sm obj_kpi_input">
						<span id="kpk_0_obj" class="obj_input" style="font-weight:bold;"></span>
						<span class="invalid-feedback obj_kpi_input_error" role="alert" id="kpk_0_obj_kpiError">
							<strong></strong>
						</span>	
					</td>
					<td align="center" class="td-act-idx" style="display:none;">
						<input type="text" name="kpk[0][act_idx]" id="kpk_0_act_idx" class="form-control form-control-sm act_idx_input" style="width:60px;font-weight:bold;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
						<span class="invalid-feedback act_idx_input_error" role="alert" id="kpk_0_act_idxError">
						  <strong></strong>
						</span>
					</td>
				  <td align="center">
					<input type="text" name="kpk[0][act_kpi]" id="kpk_0_act_kpi" class="form-control form-control-sm act_kpi_input" style="width:60px;font-weight:bold;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					<span class="invalid-feedback act_kpi_input_error" role="alert" id="kpk_0_act_kpiError">
					  <strong></strong>
					</span>
				  </td>
				 
				  <td align="center">
					<input type="text" name="kpk[0][month_1]" id="kpk_0_month_1" class="form-control form-control-sm month_1_input" style="width:80px" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
					<span class="invalid-feedback month_1_input_error" role="alert" id="kpk_0_month_1Error">
					  <strong></strong>
					</span>	
				  </td>
				  <td align="center">
					<input type="text" name="kpk[0][month_2]" id="kpk_0_month_2" class="form-control form-control-sm month_2_input" style="width:80px" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
					<span class="invalid-feedback month_2_input_error" role="alert" id="kpk_0_month_2Error">
					  <strong></strong>
					</span>	
				  </td>
				  <td align="center">
					<input type="text" name="kpk[0][month_3]" id="kpk_0_month_3" class="form-control form-control-sm month_3_input" style="width:80px" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
					<span class="invalid-feedback month_3_input_error" role="alert" id="kpk_0_month_3Error">
					  <strong></strong>
					</span>	
				  </td>
				  <td align="center">
					<input type="text" name="kpk[0][month_4]" id="kpk_0_month_4" class="form-control form-control-sm month_4_input" style="width:80px" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
					<span class="invalid-feedback month_4_input_error" role="alert" id="kpk_0_month_4Error">
					  <strong></strong>
					</span>	
				  </td>
				  <td align="center">
					<input type="text" name="kpk[0][month_5]" id="kpk_0_month_5" class="form-control form-control-sm month_5_input" style="width:80px" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
					<span class="invalid-feedback month_5_input_error" role="alert" id="kpk_0_month_5Error">
					  <strong></strong>
					</span>	
				  </td>
				  <td>
					<center>
							<button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
					</center>
				  </td>
				</tr>
			</table>
		</div>      
    </div>
  </div>
</div>
<div id="uploadModal" class="modal fade" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1055;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title-delete">Import Participant</h2>
                <button type="button" id="close_upload" class="close" data-dismiss="modal">&times;</button>
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
					<div class="col-md-1">
					</div>
					<div class="col-md-3">
                       <a href="{{ asset('project/storage/app/public/P2K/import_participant_P2K.xlsx') }}"  name="download" id="" class="btn btn-success btn-sm" title="Download"><span class="fa fa-file-excel"></span> Download Template Participant</a>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-sm-12 text-right">
                        <button type="button" id="submit_upload" class="btn btn-lg btn-success" ><i class="fas fa-upload"></i> Import</button>
                    </div>
                </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
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
  #supa_table td:nth-child(15) {
    text-align: center;
  }
  select[readonly].select2-hidden-accessible + .select2-container {
    pointer-events: none;
    touch-action: none;
  }
  select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
    background: #e8ebed;
    box-shadow: none;
  }

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
    display: none;
  }

  .custom-select:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  *:focus{
    outline:0px;
  }
  .gj-icon{
	  margin-top:-3px;
  }
   #table_rec_detail.table th{
	padding: 5px;
	vertical-align: middle;
  }
  #table_rec_detail.table td{
	vertical-align: middle;
  }
  .is-invalid:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  td.text-center{
	text-align:center;
  }
  .modal{
	overflow:auto !important;
  }
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ asset('vendor/bootstrap/js/id-id.js') }}"></script>
<script type="text/javascript">
let global_emp = [];
let global_id_rec_detail = 0;
let global_thresholds = [];
let global_departments = [];
let global_division = [];
let global_name = "";
let global_dept_code = "";
let global_com = "";
let global_month = 0;
let global_year = 0;

	var AjaxUrl = "";
	$('#kpkForm').submit(function (e) {
      e.preventDefault();
	  let thisButtonId = e.originalEvent.submitter.id;
		if(thisButtonId == 'save_and_submit' || thisButtonId == 'save_and_draft'){
			let addForm = $("#save_and_submit").hasClass('addForm');
			if(addForm == true){
				AjaxUrl = "{{ route('kpk.save') }}";
			} else {
				AjaxUrl = "{{ route('kpk.update') }}";
			}
		}
      let formData = $(this).serializeArray();
	  formData.push({name:'form',value:thisButtonId});
      $(".invalid-feedback").children("strong").text("");
      $("#kpkForm input").removeClass("is-invalid");
      $("#kpkForm select").removeClass("is-invalid");
      $("#kpkForm select").removeClass("custom-select");
      $("#kpkForm textarea").removeClass("is-invalid");
      $.ajax({
        method: "POST",
        headers: {
			Accept: "application/json",
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		},
        url: AjaxUrl,
        data: formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
        success: function (response) {
          if (response.status == 'true') {
			if(response.data.remark_5 == 'submit'){
				var data = response.data;
				$.ajax({
				  type: 'POST',
				  headers: {
					'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
				  },
				  url: "{{ route('mail.new_kpk') }}",
				  data: {
					  source: data,
				  },
				});
			}		
            $("#myModal").modal('hide');
            $("#kpkForm")[0].reset();
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#kpk_table').DataTable().ajax.reload();
          }else {
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Something went wrong! '+response.message
            });
          }
        },
		complete: function(){
			$('#loader').addClass('hidden');
		},
        error: function (response) {
        //  document.querySelector(".action").disabled=false;
			if (response.status === 422) {
				let errors = response.responseJSON.errors;
				Object.keys(errors).forEach(function(key) {
					var key_temp = key.replaceAll(".", "_");
					$("#" + key_temp).addClass("is-invalid");
					$("#" + key_temp + "Error").children("strong").text(errors[key][0]);
					var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
					if (tab_id != undefined) {
						$("#tab_rec_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
					}
				});
			}
			else {
				swal({
				  icon: 'error',
				  title: 'Oops...',
				  dangerMode: true,
				  text: 'Something went wrong! ['+response.message+']'
				});
			}
        }
      });
    }); 

function on_close_modal() {
	$('#kpk_table').DataTable().ajax.reload(); 
}

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#kpkForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Penetapan BA P2K");
	$("#save_and_draft").show().html('<i class="fas fa-save"></i> Save as Draft').removeClass('editForm').addClass('addForm');
	$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').removeClass('editForm').addClass('addForm');
//	$("#save_button").css("display","inline");
    $.ajax({
			url: "{{ route('kpk.modal_detail') }}",
			data:{global_kpk:0},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

$(document).on('click', '#new_rec_detail', function () {
            var content = jQuery('#sample_table_rec tr'),
                    size = global_id_rec_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_performance_evaluation_input').attr('id', 'kpk_' + size + '_id_performance_evaluation');
            element.find('.id_performance_evaluation_input').attr('name', 'kpk[' + size + '][id_performance_evaluation]'); 
			
			element.find('.id_group_input').attr('id', 'kpk_' + size + '_id_group');
            element.find('.id_group_input').attr('name', 'kpk[' + size + '][id_group]');
			element.find('.id_group_input_error').attr('id', 'kpk_' + size + '_id_groupError');
			element.find('.id_group_input').prepend('<option selected></option>').select2({
                placeholder: "Select Group ...",
				allowClear: true,
            })
												
			element.find('.load_pos_detail_input').attr('id', 'load_pos_detail_' +size);
			
			element.find('.id_employee_input').attr('id', 'kpk_' + size + '_id_employee');
            element.find('.id_employee_input').attr('name', 'kpk[' + size + '][id_employee]');
            element.find('.id_employee_input_error').attr('id', 'kpk_' + size + '_id_employeeError');
            element.find('.id_employee_input').prepend('<option selected></option>').select2({
                placeholder: "Select Participant ...",
                data: global_emp,
            });
			
			element.find('.id_pos_detail_input').attr('id', 'kpk_' + size + '_id_pos_detail');
            element.find('.id_pos_detail_input').attr('name', 'kpk[' + size + '][id_pos_detail]');
			 			 
			element.find('.position_name_input').attr('id', 'kpk_' + size + '_position_name');
			element.find('.region_input').attr('id', 'kpk_' + size + '_region');
			element.find('.branch_input').attr('id', 'kpk_' + size + '_branch');
			element.find('.direct_input').attr('id', 'kpk_' + size + '_direct');
					
			element.find('.obj_input').attr('id', 'kpk_' + size + '_obj');
			
			element.find('.obj_kpi_input').attr('id', 'kpk_' + size + '_obj_kpi');
            element.find('.obj_kpi_input').attr('name', 'kpk[' + size + '][obj_kpi]');
            element.find('.obj_kpi_input_error').attr('id', 'kpk_' + size + '_obj_kpiError'); 
			
			element.find('.act_kpi_input').attr('id', 'kpk_' + size + '_act_kpi');
            element.find('.act_kpi_input').attr('name', 'kpk[' + size + '][act_kpi]');
            element.find('.act_kpi_input_error').attr('id', 'kpk_' + size + '_act_kpiError'); 
			
			element.find('.act_idx_input').attr('id', 'kpk_' + size + '_act_idx');
            element.find('.act_idx_input').attr('name', 'kpk[' + size + '][act_idx]');
            element.find('.act_idx_input_error').attr('id', 'kpk_' + size + '_act_idxError'); 
			
			element.find('.month_1_input').attr('id', 'kpk_' + size + '_month_1');
            element.find('.month_1_input').attr('name', 'kpk[' + size + '][month_1]');
            element.find('.month_1_input_error').attr('id', 'kpk_' + size + '_month_1Error');
			
			element.find('.month_2_input').attr('id', 'kpk_' + size + '_month_2');
            element.find('.month_2_input').attr('name', 'kpk[' + size + '][month_2]');
            element.find('.month_2_input_error').attr('id', 'kpk_' + size + '_month_2Error'); 
			
			element.find('.month_3_input').attr('id', 'kpk_' + size + '_month_3');
            element.find('.month_3_input').attr('name', 'kpk[' + size + '][month_3]');
            element.find('.month_3_input_error').attr('id', 'kpk_' + size + '_month_3Error'); 
			
			element.find('.month_4_input').attr('id', 'kpk_' + size + '_month_4');
            element.find('.month_4_input').attr('name', 'kpk[' + size + '][month_4]');
            element.find('.month_4_input_error').attr('id', 'kpk_' + size + '_month_4Error'); 
			
			element.find('.month_5_input').attr('id', 'kpk_' + size + '_month_5');
            element.find('.month_5_input').attr('name', 'kpk[' + size + '][month_5]');
            element.find('.month_5_input_error').attr('id', 'kpk_' + size + '_month_5Error'); 
			
			element.find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').on('change click keyup input paste',(function (event) {
				$(this).val(function (index, value) {
					return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				});
			}));
			
			if(global_dept_code == '170_SAL'){
				element.find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').attr('readonly',false);
			}
			else{
				element.find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').attr('readonly',true);
			}
			
            element.appendTo('#table_rec_body');
			 $('#table_rec_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });			
		//	get_global_departments();
        });
		
		$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
            $('#table_rec_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });


$(function () {
	
	com_type = [
			{
				id: 'corporate',
				text: 'Organik'
			},
			{
				id: 'os',
				text: 'OS'
			},
		];
	
    $('#kpk_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('kpk.index') }}",
        data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
			$('#kpk_table').DataTable().ajax.reload();
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
      { data: 'reference_number', name: 'reference_number' },
      { data: 'letter_date', name: 'letter_date' },
      { data: 'month_kpk', name: 'month_kpk', className: 'text-center' },
      { data: 'on_perform', name: 'on_perform', className: 'text-center' },
      { data: 'com_type', name: 'com_type', className: 'text-center' },
      { data: 'dept_name', name: 'dept_name', className: 'text-center' },
      { data: 'chief_name',name: 'chief_name'},
	  { data: 'draft_submit', name: 'draft_submit', className: 'text-center', render: function ( data, type, row ) {	
			if(row.draft_submit == 'Draft'){
					return '<span class="badge badge-success" style="padding:5px;font-size:13px;">'+data+'</span>';
			}
			else{
				return '<span class="badge badge-info" style="padding:5px;font-size:13px;">'+data+'</span>';
			}
		}
	   },
	    { data: 'status',name: 'status', className: 'text-center'},
      { data: 'action', name: 'action', orderable: false, className: 'space text-center' }
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
        if(access_print == 0){
          $(row).find('.btn-print').css('display', 'none');
        } 
      },
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
  
function get_company_type(){
	$('#com_type').prepend('<option></option>').select2({
		data: global_com,
		allowClear: true,
	});
	$('#dept').prepend('<option></option>').select2();
}

$(document).on('change', '#com_type', function(event, istrigger) {
	if(!istrigger){
		get_dept($(this).val());
	}	
});
   
const get_dept = async (idCom) => {
	let result;
	let myData = {
		id_com: idCom,
	};
    try {
        result = await $.ajax({
            url: '<?= url('e-letter/performance_plan/kpk_letter/get_dept') ?>',
            data: myData,
            dataType: 'json',
			beforeSend: function () {
				$('#dept').empty();
			//	$('#loader').removeClass('hidden');
				$('#table_rec_detail').find('.id_employee_input').each(function (i, obj) {
					$('#' + obj.id).empty();
				});
				$('#table_rec_detail').find('.id_group_input').each(function (i, obj) {
					$('#' + obj.id).empty();
				});
				$('#table_rec_detail').find('.position_name_input,.region_input,.branch_input,.direct_input,.obj_input').each(function (i, obj) {
					$('#' + obj.id).html('');
				});
				$('#table_rec_detail').find('.act_idx_input,.act_kpi_input').each(function (i, obj) {
					$('#' + obj.id).val('');
				});
				$('#table_rec_detail').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').each(function (i, obj) {
					$('#' + obj.id).val('');
				});		
			},
            success: function (res) {
              global_departments = res;
              $('#dept').prepend('<option></option>').select2({
                data: res,
                allowClear: true,
              });
            },
        });
        return result;
    } catch (error) {
     //   get_dept();
    }	
} 

const get_division = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('e-letter/performance_plan/kpk_letter/get_division') ?>',
            dataType: 'json',
            success: function (res) {
              global_departments = res;
              $('#division').prepend('<option></option>').select2({
                data: res,
                allowClear: true,
              });
            },
        });
        return result;
    } catch (error) {
     //   get_division();
    }	
} 

$(document).on('change', '#dept', function (event, istrigger) {  
    if(!istrigger){
		get_participant($('#com_type').select2('val'),$(this).select2('val'),global_month,global_year,'new');
		get_global_departments();
	}
});


const get_employee_by = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('e-letter/performance_plan/kpk_letter/get_employee_by') ?>',
            dataType: 'json',
            success: function (res) {
				$('#id_employee_request').select2({
					data: res,
				}).on('change', function (e) {
					$('#id_routing').val($(this).select2('data')[0].id_routing);
					$('#id_position_detail').val($(this).select2('data')[0].id_position_detail);
					$('#id_dept').val($(this).select2('data')[0].id_dept);
					$('#id_branch').val($(this).select2('data')[0].id_branch);
					$('#id_region').val($(this).select2('data')[0].id_region);
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_employee_by();
    }	
}

$(document).on('change', '.id_employee_input', function(event, istrigger) {
	if(!istrigger){
		let idEmp= $(this).val();
		global_emp.forEach((emp) => {
			if(emp.id == idEmp) {
				get_pos_detail($(this).closest('tr'),emp.id_position_detail);
			}
		})
		get_threshold($(this),$(this).val());
	}	
});

const get_pos_detail = async (element,id_pos_detail) => {
	let result;
    try {
        result = await $.ajax({
            url: "{{ route('kpk.get_pos_detail') }}",
            dataType: 'json',
			data: {
			  id_pos_detail: id_pos_detail
			},
            success: function (res) {
				element.find('.id_pos_detail_input').val(res[0].id_position_detail).trigger('change');
				element.find('.position_name_input').html(res[0].position_name);
				element.find('.region_input').html(res[0].region);
				element.find('.branch_input').html(res[0].branch);
				element.find('.direct_input').html(res[0].name_supervisor);
				
				
            },
        });
        return result;
    } catch (error) {
    }	
} 


const get_threshold = async (element,val,pos=null,edit=null) => {
	let result;
    try {
        result = await $.ajax({
            url: "{{ route('kpk.get_threshold') }}",
            dataType: 'json',
			data: {
			  id_employee: val,
			  id_pos_detail: pos,
			  edit: edit,
			},
             success: (res) => {
				  global_thresholds = res;
				  if(edit != 'edit'){
					element.closest('tr').find('.id_group_input').empty().prepend('<option selected></option>').select2({
						data: res,
						placeholder: "Select Group ...",
						allowClear: true,
					});
				//	.trigger('change');
					element.closest('tr').find('.id_group_input_error').removeClass('d-block');
					  if(res.length < 1) {
						element.closest('tr').find('.id_group_input_error').find('strong').text('Group not found for this employee.(Contact HRIS Administrator)');
						element.closest('tr').find('.id_group_input_error').addClass('d-block');
					  }
				  }
				  else{
					element.closest('tr').find('.id_group_input').select2({
						data: res,
						placeholder: "Select Group ...",
						allowClear: true,
					});
				  }
				},
			 error: (err) => {
			  element.closest('tr').find('.id_group_input').empty().select2();
			}
        });
        return result;
    } catch (error) {
     //   get_threshold();
    }	
} 

const get_import_threshold = async (id_employee) => {
	let result;
    try {
        result = await $.ajax({
            url: "{{ route('kpk.get_threshold') }}",
            dataType: 'json',
			data: {
			  id_employee: id_employee
			},
            success: function (res) {
            },
        });
        return result;
    } catch (error) {
     //   get_branch();
    }	
} 

$(document).on('change', '.id_group_input', function(event, istrigger) {
	if(!istrigger){
	//   let idThreshold = $(this).val();
	   let minKpi = null;
		global_thresholds.forEach((threshold) => {
		//	if(threshold.id == idThreshold) {
				minKpi = threshold.minimum_score_kpi_level;
		//	}
			if(threshold.dept_code != '170_SAL'){
				$(this).closest('tr').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').val(minKpi);
			}
		/*	else{
				$(this).closest('tr').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').val(minKpi); 
			}
		*/
		})
		$(this).closest('tr').find('.obj_input').html(minKpi);
		$(this).closest('tr').find('.obj_kpi_input').val(minKpi); 
	}
})

function get_global_departments(){
	global_departments.forEach((dept) => {
      if(dept.id == $('#dept').val()) {
		global_dept_code = dept.department_code;
		$('#dept_code').val(dept.department_code).trigger('change');
        if(dept.department_code == '170_SAL') {
		  $('#idDivision').show();
          $('.th-threshold-kpi').text('Threshold Offtake Kumulatif 3 Bulan (%)');
          $('.th-act-idx').show();
          $('.th-act-idx').text('IDX sales volume P3M (%)');
          $('.td-act-idx').show();
		  $('#table_rec_detail').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').each(function (i, obj) {
				$('#' + obj.id).attr('readonly',false);
				$('#' + obj.id).val('');
		  });
		  $('.new_upload').show();
        } else {
		  $('#division').val(null).trigger('change');
		  $('#idDivision').hide();
          $('.th-threshold-kpi').text('Threshold KPI');
		  $('.th-act-idx').hide();
          $('.td-act-idx').hide();
		  $('#table_rec_detail').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').each(function (i, obj) {
				$('#' + obj.id).attr('readonly',true);
		  });
		  $('.new_upload').hide();
        }
      }
    })
}

const get_participant = async (id_comType,id_dept,global_month,global_year,new_edit) => {
	let result;
	let myData = {
			id_com_type: id_comType,
			id_dept: id_dept,
			period_month: global_month,
			period_year: global_year,
			new_edit: new_edit,
		};
    try {
        result = await $.ajax({
           url: "<?= url('e-letter/performance_plan/kpk_letter/get_participant') ?>",
			method: "GET",
			data: myData,
			beforeSend: function () {
			//	$('#loader').removeClass('hidden');
				$('#table_rec_detail').find('.id_group_input').each(function (i, obj) {
					$('#' + obj.id).empty();
				});
				$('#table_rec_detail').find('.position_name_input,.region_input,.branch_input,.direct_input,.obj_input').each(function (i, obj) {
					$('#' + obj.id).html('');
				});
				$('#table_rec_detail').find('.act_idx_input,.act_kpi_input').each(function (i, obj) {
					$('#' + obj.id).val('');
				});
				$('#table_rec_detail').find('.month_1_input,.month_2_input,.month_3_input,.month_4_input,.month_5_input').each(function (i, obj) {
					$('#' + obj.id).val('');
				});
				$('#table_rec_detail').find('.load_pos_detail_input').each(function (i, obj) {
					$('#' + obj.id).show();
				});
				
			},
			success: function (response) {
				global_emp = response;
				$('#table_rec_detail').find('.id_employee_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).prepend('<option selected></option>').select2({
						placeholder: "Select Participant ...",
						data: global_emp,
					});
				});
				
			
			},
			complete: function(){
			//	$('#loader').addClass('hidden');
				setTimeout(function () {
					$('#table_rec_detail').find('.load_pos_detail_input').each(function (i, obj) {
						$('#' + obj.id).hide();
					});
				}, 500);
			},
        });
        return result;
    } catch (error) {
    //    get_participant(id_dept);
    }	
}

$(document).on('click', '.edit', function() {
	$("#contentBody").html('');
	global_name = $(this).attr('name');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#kpkForm input").removeClass("is-invalid");
//	$("#modal-title").html("<span class='fas fa-plus'></span> Form BA P2K");
	if(global_name == 'view'){
		$("#save_and_draft").hide();
		$("#save_and_submit").hide();
		$("#modal-title").html("<span class='fas fa-eye'></span> View BA P2K");
	}
	else{
		$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').removeClass('addForm').addClass('editForm');
		$("#save_and_draft").show().html('<i class="fas fa-save"></i> Save as Draft').removeClass('addForm').addClass('editForm');
		$("#modal-title").html("<span class='fas fa-edit'></span> Edit BA P2K");
	}
//	$("#save_button").css("display","none");	
	$.ajax({
			url: "{{ route('kpk.modal_detail') }}",
			data:{global_kpk:$(this).attr('id')},
			success: function(result){
        $("#contentBody").html(result);
        $("#myModal").modal('show'); 
    //    get_dept();
		  }
	});
});

function upload(){
    var formUpload = new FormData($('#upload_form')[0]);
	$.each(global_emp, function (i, item) {
		formUpload.append("emp["+i+"][id_employee]",item.id);
		formUpload.append("emp["+i+"][nik_employee]",item.nik_employee);
	});
    $.ajax({
        type: 'POST',
        headers: {
            Accept: "application/json",
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        url: "<?= url('e-letter/performance_plan/kpk_letter/upload_review') ?>",
        enctype: 'multipart/form-data',
        processData: false,  // Important!
        contentType: false,
        cache: false,
        data: formUpload,
        beforeSend: function () {
            $('#loader').removeClass('hidden');
        },
        success: function (response) {
            if (response.status == 'true') {				
					$('#uploadModal').modal('hide');
					$('.modal-backdrop').css('z-index','1049');	
					if(response.data.length > 0){
						$.each(response.data, function (i, item) {
							var a = true;
							$('#table_rec_body tr').each(function (index) {	
								var id_emp = $(this).find('.id_employee_input').val();
								if(item.id_employee == id_emp){
									a = false;
									return false
								}
							});		
							if(a == true){
								$('#new_rec_detail').trigger('click');
								$('#table_rec_body tr').each(function (index) {		
									$(this).find('span.sn').html(index + 1);
									$(this).find('.id_employee_input').val(response.data[index].id_employee).trigger('change',[true]);
									get_pos_detail($(this),response.data[index].id_position_detail).then(function(res) {
									});
									var th = $(this);
									get_import_threshold(response.data[index].id_employee).then(function(res) {
										th.find('.id_group_input_error').removeClass('d-block');
										  if(res.length < 1) {
											th.find('.id_group_input_error').find('strong').text('Group not found for this employee.(Contact HRIS Administrator)');
											th.find('.id_group_input_error').addClass('d-block');
										  }
										  else{
											th.find('.id_group_input').select2({
												placeholder: "Select Group ...",
												data:res,
												allowClear: true,
											})
											th.find('.id_group_input').val(response.data[index].id_threshold).trigger('change');
											th.find('.obj_input').html(response.data[index].minimum_score_kpi_level).trigger('change');
										  }
									});
									$(this).find('.act_idx_input').val(response.data[index].act_idx).trigger('change');
									$(this).find('.act_kpi_input').val(response.data[index].act_kpi).trigger('change');
									$(this).find('.month_1_input').val(response.data[index].m_1).trigger('change');
									$(this).find('.month_2_input').val(response.data[index].m_2).trigger('change');
									$(this).find('.month_3_input').val(response.data[index].m_3).trigger('change');
									$(this).find('.month_4_input').val(response.data[index].m_4).trigger('change');
									$(this).find('.month_5_input').val(response.data[index].m_5).trigger('change');
								});	
								
							}
						});	
						
						
					}
		            $('#loader').addClass('hidden')
            } else {
                if(response.status == 'false'){
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
		complete: function(){
			
			 $('#loader').addClass('hidden')
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

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(idLetter) {
	let res = {
        id_letter: idLetter,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('e-letter/performance_plan/kpk_letter/download') }}";
    window.open(url+'?'+param, '_blank');
}
   
</script>  
@endsection