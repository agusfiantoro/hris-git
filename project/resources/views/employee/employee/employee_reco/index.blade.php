@extends('adminlte::page')
@section('title', 'Recommendation Form')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Recommendation Form</h5>
		<div class="card-tools">
			<button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Reco Form</button>
		</div>
      </div>
      <div class="card-body">
			<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>	
			<br>
			<br>
			<table id="reco_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr>
				<th data-priority="5"></th>
				<th data-priority="2" class="text text-center"></th>
				<th data-priority="3">No</th>
				<th data-priority="4">Reference Number</th>
				<th>NIK</th>
				<th data-priority="6">Name</th>
				<th data-priority="7">Current Position</th>
				<th data-priority="8">Category</th>
				<th data-priority="9">Type</th>
				<th data-priority="10">Approval Status</th>
				<th>Note Revised</th>
				<th>Note Rejected</th>				
				<th data-priority="1" width="300" class="text text-center">Action</th>
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
		<form method="POST" id="recoForm">
			{{ csrf_field() }}
			<div class="modal-header">
				<h5 id="modal-title" class="modal-title"></h5>
				<button type="button" class="close" onclick="on_close_modal()"  data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		  <div class="modal-body" id="contentBody" style="height: 500px;overflow-y: auto;">
		  </div>
		  <div class="modal-footer">
			<!--button type="submit" class="edit_master btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp; -->
			<button type="submit" class="btn btn-info btn-sm " id="save_and_submit" style="display:none;"></button>&nbsp;
			<button type="submit" class="btn btn-success btn-sm " id="save_and_draft" style="display:none;"></button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>
		<div style="display:none;">
			<table id="sample_table_quanti">
				<tr id="" style="font-size:14px;">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
						<input name="quanti[0][id_recommendation_quantitative]" id="quanti_0_id_recommendation_quantitative" type="hidden" class="id_recommendation_quantitative_input">
						<input type="text" name="quanti[0][desc_kpi]" id="quanti_0_desc_kpi" class="form-control form-control-sm desc_kpi_input" style="width: 100%;">
						<span class="invalid-feedback desc_kpi_input_error" role="alert" id="quanti_0_desc_kpiError">
							<strong></strong>
						</span>
					</td>
					<td>
						<input type="text" name="quanti[0][weight_1]" id="quanti_0_weight_1" class="form-control form-control-sm weight_1_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_1]" id="quanti_0_obj_1" class="form-control form-control-sm obj_1_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][ach_1]" id="quanti_0_ach_1" class="form-control form-control-sm ach_1_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_1]" id="quanti_0_idx_1" class="form-control form-control-sm idx_1_input" readonly>
					</td>
					
					<td>
						<input type="text" name="quanti[0][weight_2]" id="quanti_0_weight_2" class="form-control form-control-sm weight_2_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_2]" id="quanti_0_obj_2" class="form-control form-control-sm obj_2_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">	
					</td>
					<td>
						<input type="text" name="quanti[0][ach_2]" id="quanti_0_ach_2" class="form-control form-control-sm ach_2_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_2]" id="quanti_0_idx_2" class="form-control form-control-sm idx_2_input" readonly>
					</td>
					
					<td>
						<input type="text" name="quanti[0][weight_3]" id="quanti_0_weight_3" class="form-control form-control-sm weight_3_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_3]" id="quanti_0_obj_3" class="form-control form-control-sm obj_3_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">	
					</td>
					<td>
						<input type="text" name="quanti[0][ach_3]" id="quanti_0_ach_3" class="form-control form-control-sm ach_3_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_3]" id="quanti_0_idx_3" class="form-control form-control-sm idx_3_input" readonly>
					</td>
					
					<td>
						<input type="text" name="quanti[0][weight_4]" id="quanti_0_weight_4" class="form-control form-control-sm weight_4_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_4]" id="quanti_0_obj_4" class="form-control form-control-sm obj_4_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][ach_4]" id="quanti_0_ach_4" class="form-control form-control-sm ach_4_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_4]" id="quanti_0_idx_4" class="form-control form-control-sm idx_4_input" readonly>
					</td>
					
					<td>
						<input type="text" name="quanti[0][weight_5]" id="quanti_0_weight_5" class="form-control form-control-sm weight_5_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_5]" id="quanti_0_obj_5" class="form-control form-control-sm obj_5_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][ach_5]" id="quanti_0_ach_5" class="form-control form-control-sm ach_5_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_5]" id="quanti_0_idx_5" class="form-control form-control-sm idx_5_input" readonly>
					</td>
					
					<td>
						<input type="text" name="quanti[0][weight_6]" id="quanti_0_weight_6" class="form-control form-control-sm weight_6_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][obj_6]" id="quanti_0_obj_6" class="form-control form-control-sm obj_6_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][ach_6]" id="quanti_0_ach_6" class="form-control form-control-sm ach_6_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
					</td>
					<td>
						<input type="text" name="quanti[0][idx_6]" id="quanti_0_idx_6" class="form-control form-control-sm idx_6_input" readonly>
					</td>		
					<td>
						<center>
							<button type="button" id="quanti_0_del_rec_quanti" class="delete-record-quanti btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
					</td>
				</tr>
			</table>
		</div>  
		
		<div style="display:none;">
			<table id="sample_table_quali">
				<tr id="" style="font-size:14px;">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
						<input name="quali[0][id_recommendation_qualitative]" id="quali_0_id_recommendation_qualitative" type="hidden" class="id_recommendation_qualitative_input">
						<select name="quali[0][id_appraiser]" id="quali_0_id_appraiser" class="form-control form-control-sm select2 id_appraiser_input" style="width: 100%;"></select>
						<span class="invalid-feedback id_appraiser_input_error" role="alert" id="quali_0_id_appraiserError">
							<strong></strong>
						</span>							
					</td>
					<td>
						<select name="quali[0][appraisers_hierarchy]" id="quali_0_appraisers_hierarchy" class="form-control form-control-sm select2 appraisers_hierarchy_input" style="width: 100%;">
						</select>
					</td>
					<td align="center">
						<div style="font-size:14px;font-weight:bold;" id="quali_0_desc_score" class="desc_score_input"></div>
					</td>	
					<td style="vertical-align:middle;" align="center">
						<span id="quali_0_submitted" class="submitted_input"></span>
					</td>		
					<td>
						<center>
							<button type="button" id="quali_0_del_rec_quali" class="delete-record-quali btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
						</center>
					</td>
				</tr>
			</table>
		</div>  
		
	</div>
  </div>
</div>
<div id="browseModaljob" class="modal fade" role="dialog" style="z-index:9999;">
	<div class="modal-dialog modal-xl">
		<div class="modal-content"> 
		   <div class="modal-header">
				<h5 class="modal-title">Select New Position</h5>
				<button type="button" class="close" onclick="on_close_modal_position()" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		   <div class="card-body">
				<table id="bro_table_job" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr style="text-align:center;">
				   <th>No.</th>
				   <th data-priority="3" style="width:150px;">Position Route</th>
				   <th data-priority="2" style="width:80px;">Grade</th>
				   <th data-priority="4">Branch</th>
				   <th>Location</th>
				   <th>Principal</th>
				   <th>Department</th>
				   <!-- th data-priority="5" style="width:100px;">Replace Employee</th -->
				   <th data-priority="1">Action</th>
				  </tr>
				 </thead>
				</table>
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
  .modal{
		overflow:auto !important;
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

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
    display: none;
  }

 .is-invalid:valid + .select2 .select2-selection{
    border-color: #dc3545!important;
  }
  
  .readonly-checkbox {
    pointer-events: none; /* Mencegah interaksi mouse */
    opacity: 0.5; /* Mengurangi kejelasan untuk memberikan efek tidak aktif */
  }
  
  td.text-center{
	text-align:center;
  }
  td.text-width{
	width:30px;
	text-align:center;
  }
  #table_rec_quanti.table th{
	padding: 5px;
	vertical-align: middle;
  }
  
  #table_rec_quanti.table td {
	padding: 0.5rem;
  }
  
</style>
@endsection

@section('scripts')
<script type="text/javascript">

let global_id_quanti = 0;
let global_id_quali = 0;
let global_id_appraiser = [];
let global_appraiser_type = [];
let notes_help = "";
let global_id_employee = 0;

function on_close_modal() {
	$("#myModal").modal('hide'); 
}

function on_close_modal_position() {
	$("#browseModaljob").modal('hide'); 
}

$(document).on('click', '#new_rec_quanti', function (event,one) {
			$("#table_rec_quanti_body").show(); 
            var content = jQuery('#sample_table_quanti tr'),
                    size = global_id_quanti++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_quanti-'+size);
			element.find('.add-record-quanti').attr('onclick', 'detail_save_quanti(' + size + ')');
			element.find('.add-record-quanti').attr('data-id', size);
            element.find('.delete-record-quanti').attr('data-id', size);
            element.find('.id_recommendation_quantitative_input').attr('id', 'quanti_' + size + '_id_recommendation_quantitative');
            element.find('.id_recommendation_quantitative_input').attr('name', 'quanti[' + size + '][id_recommendation_quantitative]');
			
			element.find('.desc_kpi_input').attr('id', 'quanti_' + size + '_desc_kpi');
            element.find('.desc_kpi_input').attr('name', 'quanti[' + size + '][desc_kpi]'); 
			element.find('.desc_kpi_input_error').attr('id', 'quanti_' + size + '_desc_kpiError');
			
			element.find('.weight_1_input').attr('id', 'quanti_' + size + '_weight_1');
            element.find('.weight_1_input').attr('name', 'quanti[' + size + '][weight_1]'); 
			
			element.find('.obj_1_input').attr('id', 'quanti_' + size + '_obj_1');
            element.find('.obj_1_input').attr('name', 'quanti[' + size + '][obj_1]'); 
			
			element.find('.ach_1_input').attr('id', 'quanti_' + size + '_ach_1');
            element.find('.ach_1_input').attr('name', 'quanti[' + size + '][ach_1]'); 
			
			element.find('.idx_1_input').attr('id', 'quanti_' + size + '_idx_1');
            element.find('.idx_1_input').attr('name', 'quanti[' + size + '][idx_1]'); 
					
			element.find('.weight_2_input').attr('id', 'quanti_' + size + '_weight_2');
            element.find('.weight_2_input').attr('name', 'quanti[' + size + '][weight_2]'); 
			
			element.find('.obj_2_input').attr('id', 'quanti_' + size + '_obj_2');
            element.find('.obj_2_input').attr('name', 'quanti[' + size + '][obj_2]'); 
			
			element.find('.ach_2_input').attr('id', 'quanti_' + size + '_ach_2');
            element.find('.ach_2_input').attr('name', 'quanti[' + size + '][ach_2]'); 
			
			element.find('.idx_2_input').attr('id', 'quanti_' + size + '_idx_2');
            element.find('.idx_2_input').attr('name', 'quanti[' + size + '][idx_2]'); 
			
			element.find('.weight_3_input').attr('id', 'quanti_' + size + '_weight_3');
            element.find('.weight_3_input').attr('name', 'quanti[' + size + '][weight_3]'); 
			
			element.find('.obj_3_input').attr('id', 'quanti_' + size + '_obj_3');
            element.find('.obj_3_input').attr('name', 'quanti[' + size + '][obj_3]'); 
			
			element.find('.ach_3_input').attr('id', 'quanti_' + size + '_ach_3');
            element.find('.ach_3_input').attr('name', 'quanti[' + size + '][ach_3]'); 
			
			element.find('.idx_3_input').attr('id', 'quanti_' + size + '_idx_3');
            element.find('.idx_3_input').attr('name', 'quanti[' + size + '][idx_3]'); 
			
			element.find('.weight_4_input').attr('id', 'quanti_' + size + '_weight_4');
            element.find('.weight_4_input').attr('name', 'quanti[' + size + '][weight_4]'); 
			
			element.find('.obj_4_input').attr('id', 'quanti_' + size + '_obj_4');
            element.find('.obj_4_input').attr('name', 'quanti[' + size + '][obj_4]'); 
			
			element.find('.ach_4_input').attr('id', 'quanti_' + size + '_ach_4');
            element.find('.ach_4_input').attr('name', 'quanti[' + size + '][ach_4]'); 
			
			element.find('.idx_4_input').attr('id', 'quanti_' + size + '_idx_4');
            element.find('.idx_4_input').attr('name', 'quanti[' + size + '][idx_4]'); 
			
			element.find('.weight_5_input').attr('id', 'quanti_' + size + '_weight_5');
            element.find('.weight_5_input').attr('name', 'quanti[' + size + '][weight_5]'); 
			
			element.find('.obj_5_input').attr('id', 'quanti_' + size + '_obj_5');
            element.find('.obj_5_input').attr('name', 'quanti[' + size + '][obj_5]'); 
			
			element.find('.ach_5_input').attr('id', 'quanti_' + size + '_ach_5');
            element.find('.ach_5_input').attr('name', 'quanti[' + size + '][ach_5]'); 
			
			element.find('.idx_5_input').attr('id', 'quanti_' + size + '_idx_5');
            element.find('.idx_5_input').attr('name', 'quanti[' + size + '][idx_5]'); 
			
			element.find('.weight_6_input').attr('id', 'quanti_' + size + '_weight_6');
            element.find('.weight_6_input').attr('name', 'quanti[' + size + '][weight_6]'); 
			
			element.find('.obj_6_input').attr('id', 'quanti_' + size + '_obj_6');
            element.find('.obj_6_input').attr('name', 'quanti[' + size + '][obj_6]'); 
			
			element.find('.ach_6_input').attr('id', 'quanti_' + size + '_ach_6');
            element.find('.ach_6_input').attr('name', 'quanti[' + size + '][ach_6]'); 
			
			element.find('.idx_6_input').attr('id', 'quanti_' + size + '_idx_6');
            element.find('.idx_6_input').attr('name', 'quanti[' + size + '][idx_6]'); 
			
			element.find('.add-record-quanti').attr('id', 'quanti_' + size + '_save_rec_quanti');
			element.find('.delete-record-quanti').attr('id', 'quanti_' + size + '_del_rec_quanti');
								
            element.appendTo('#table_rec_quanti_body');
			 $('#table_rec_quanti_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
				if(one != "new"){
					$(this).find('span.sn').attr('id','idx_quanti');
				}
            });
        });
		
		$(document).on('click', '.delete-record-quanti', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec_quanti-' + id).remove();
			if($('#table_rec_quanti_body tr').length == 0){
				$("#table_rec_quanti_body").hide(); 
			}
            $('#table_rec_quanti_body tr').each(function (index) {	
                $(this).find('span.sn').html(index + 1);
            });
			calculate('.idx_1_input','.weight_1_input','#bobot_one','#tot_one');
			calculate('.idx_2_input','.weight_2_input','#bobot_two','#tot_two');
			calculate('.idx_3_input','.weight_3_input','#bobot_three','#tot_three');
			calculate('.idx_4_input','.weight_4_input','#bobot_four','#tot_four');
			calculate('.idx_5_input','.weight_5_input','#bobot_five','#tot_five');
			calculate('.idx_6_input','.weight_6_input','#bobot_six','#tot_six');
			
            return true;
        });

$(document).on('click', '#new_rec_quali', function () {	
			$("#table_rec_quali_body").show(); 
            var content = jQuery('#sample_table_quali tr'),
                    size = global_id_quali++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_quali-'+size);
			element.find('.add-record-quali').attr('onclick', 'detail_save_quali(' + size + ')');
			element.find('.add-record-quali').attr('data-id', size);
            element.find('.delete-record-quali').attr('data-id', size);
            element.find('.id_recommendation_qualitative_input').attr('id', 'quali_' + size + '_id_recommendation_qualitative');
            element.find('.id_recommendation_qualitative_input').attr('name', 'quali[' + size + '][id_recommendation_qualitative]');
			
			element.find('.id_appraiser_input').attr('id', 'quali_' + size + '_id_appraiser');
            element.find('.id_appraiser_input').attr('name', 'quali[' + size + '][id_appraiser]'); 
			element.find('.id_appraiser_input').prepend('<option selected></option>').select2({
                placeholder: "Select Appraiser ...",
                data: global_id_appraiser,
            });
			
			element.find('.appraisers_hierarchy_input').attr('id', 'quali_' + size + '_appraisers_hierarchy');
			element.find('.appraisers_hierarchy_input').attr('name', 'quali[' + size + '][appraisers_hierarchy]');
			element.find('.appraisers_hierarchy_input').select2({
				data : global_appraiser_type
			});
						
			element.find('.desc_score_input').attr('id', 'quali_' + size + '_desc_score');
						
			element.find('.submitted_input').attr('id', 'quali_' + size + '_submitted');
			element.find('.submitted_input').html('-');
			
			element.find('.add-record-quali').attr('id', 'quali_' + size + '_save_rec_quali');
			element.find('.delete-record-quali').attr('id', 'quali_' + size + '_del_rec_quali');
								
            element.appendTo('#table_rec_quali_body');
			 $('#table_rec_quali_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
		$(document).on('click', '.delete-record-quali', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec_quali-' + id).remove();
            $('#table_rec_quali_body tr').each(function (index) {	
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

// Month 1
$(document).on('change', '.weight_1_input, .obj_1_input, .ach_1_input', (element) => {
	var el_weight_1 = $(element.target).closest('tr').find('.weight_1_input').val();
	var el_obj_1 = $(element.target).closest('tr').find('.obj_1_input').val();
	var el_ach_1 = $(element.target).closest('tr').find('.ach_1_input').val();
	var weight_1 = parseFloat(el_weight_1);
	var obj_1 = parseFloat(el_obj_1);
	var ach_1 = parseFloat(el_ach_1);
		var idx_1 = parseFloat(parseFloat((ach_1 / obj_1) * weight_1).toFixed(2));		
		if(obj_1 == 0){
			$(element.target).closest('tr').find('.idx_1_input').val('NaN').trigger('change');
		}
		else{
			if(idx_1 > el_weight_1){
				$(element.target).closest('tr').find('.idx_1_input').val(el_weight_1).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_1_input').val(idx_1).trigger('change');
			}
		}
		var kelas = '.idx_1_input';
		var kelas_bobot = '.weight_1_input';
		var id_bobot = '#bobot_one';
		var id_tot = '#tot_one';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_1 == "" && el_obj_1 == "" && el_ach_1 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){
				$(id_tot).val('').trigger('change');				
			}
		}
});

// Month 2
$(document).on('change', '.weight_2_input, .obj_2_input, .ach_2_input', (element) => {
	var el_weight_2 =$(element.target).closest('tr').find('.weight_2_input').val();
	var el_obj_2 = $(element.target).closest('tr').find('.obj_2_input').val();
	var el_ach_2 = $(element.target).closest('tr').find('.ach_2_input').val();
	var weight_2 = parseFloat(el_weight_2);
	var obj_2 = parseFloat(el_obj_2);
	var ach_2 = parseFloat(el_ach_2);			
		var idx_2 = parseFloat(parseFloat((ach_2 / obj_2) * weight_2).toFixed(2));
		if(obj_2 == 0){
			$(element.target).closest('tr').find('.idx_2_input').val('NaN').trigger('change')
		}
		else{
			if(idx_2 > el_weight_2){
				$(element.target).closest('tr').find('.idx_2_input').val(el_weight_2).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_2_input').val(idx_2).trigger('change');
			}
		}
		var kelas = '.idx_2_input';
		var kelas_bobot = '.weight_2_input';
		var id_bobot = '#bobot_two';
		var id_tot = '#tot_two';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_2 == "" && el_obj_2 == "" && el_ach_2 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){				
				$(id_tot).val('').trigger('change');
			}
		}
});

// Month 3
$(document).on('change', '.weight_3_input, .obj_3_input, .ach_3_input', (element) => {
	var el_weight_3 = $(element.target).closest('tr').find('.weight_3_input').val();
	var el_obj_3 = $(element.target).closest('tr').find('.obj_3_input').val();
	var el_ach_3 = $(element.target).closest('tr').find('.ach_3_input').val();			
	var weight_3 = parseFloat(el_weight_3);
	var obj_3 = parseFloat(el_obj_3);
	var ach_3 = parseFloat(el_ach_3);			
		var idx_3 = parseFloat(parseFloat((ach_3 / obj_3) * weight_3).toFixed(2));
		if(obj_3 == 0){
			$(element.target).closest('tr').find('.idx_3_input').val('NaN').trigger('change')
		}
		else{
			if(idx_3 > el_weight_3){
				$(element.target).closest('tr').find('.idx_3_input').val(el_weight_3).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_3_input').val(idx_3).trigger('change');
			}
		}
		var kelas = '.idx_3_input';
		var kelas_bobot = '.weight_3_input';
		var id_bobot = '#bobot_three';
		var id_tot = '#tot_three';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_3 == "" && el_obj_3 == "" && el_ach_3 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){				
				$(id_tot).val('').trigger('change');
			}
		}
});

// Month 4
$(document).on('change', '.weight_4_input, .obj_4_input, .ach_4_input', (element) => {
	var el_weight_4 = $(element.target).closest('tr').find('.weight_4_input').val();
	var el_obj_4 = $(element.target).closest('tr').find('.obj_4_input').val();
	var el_ach_4 = $(element.target).closest('tr').find('.ach_4_input').val();
	var weight_4 = parseFloat(el_weight_4);
	var obj_4 = parseFloat(el_obj_4);
	var ach_4 = parseFloat(el_ach_4);			
		var idx_4 = parseFloat(parseFloat((ach_4 / obj_4) * weight_4).toFixed(2));
		if(obj_4 == 0){
			$(element.target).closest('tr').find('.idx_4_input').val('NaN').trigger('change')
		}
		else{
			if(idx_4 > el_weight_4){
				$(element.target).closest('tr').find('.idx_4_input').val(el_weight_4).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_4_input').val(idx_4).trigger('change');
			}
		}
		var kelas = '.idx_4_input';
		var kelas_bobot = '.weight_4_input';
		var id_bobot = '#bobot_four';
		var id_tot = '#tot_four';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_4 == "" && el_obj_4 == "" && el_ach_4 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){				
				$(id_tot).val('').trigger('change');
			}
		}
});

// Month 5
$(document).on('change', '.weight_5_input, .obj_5_input, .ach_5_input', (element) => {
	var el_weight_5 = $(element.target).closest('tr').find('.weight_5_input').val();
	var el_obj_5 = $(element.target).closest('tr').find('.obj_5_input').val();
	var el_ach_5 = $(element.target).closest('tr').find('.ach_5_input').val();
	var weight_5 = parseFloat(el_weight_5);
	var obj_5 = parseFloat(el_obj_5);
	var ach_5 = parseFloat(el_ach_5);			
		var idx_5 = parseFloat(parseFloat((ach_5 / obj_5) * weight_5).toFixed(2));
		if(obj_5 == 0){
			$(element.target).closest('tr').find('.idx_5_input').val('NaN').trigger('change')
		}
		else{
			if(idx_5 > el_weight_5){
				$(element.target).closest('tr').find('.idx_5_input').val(el_weight_5).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_5_input').val(idx_5).trigger('change');
			}
		}
		var kelas = '.idx_5_input';
		var kelas_bobot = '.weight_5_input';
		var id_bobot = '#bobot_five';
		var id_tot = '#tot_five';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_5 == "" && el_obj_5 == "" && el_ach_5 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){				
				$(id_tot).val('').trigger('change');
			}
		}
});

// Month 6
$(document).on('change', '.weight_6_input, .obj_6_input, .ach_6_input', (element) => {
	var el_weight_6 = $(element.target).closest('tr').find('.weight_6_input').val();
	var el_obj_6 = $(element.target).closest('tr').find('.obj_6_input').val();
	var el_ach_6 = $(element.target).closest('tr').find('.ach_6_input').val();
	var weight_6 = parseFloat(el_weight_6);
	var obj_6 = parseFloat(el_obj_6);
	var ach_6 = parseFloat(el_ach_6);			
		var idx_6 = parseFloat(parseFloat((ach_6 / obj_6) * weight_6).toFixed(2));
		if(obj_6 == 0){
			$(element.target).closest('tr').find('.idx_6_input').val('NaN').trigger('change')
		}
		else{
			if(idx_6 > el_weight_6){
				$(element.target).closest('tr').find('.idx_6_input').val(el_weight_6).trigger('change');
			}
			else{
				$(element.target).closest('tr').find('.idx_6_input').val(idx_6).trigger('change');
			}
		}
		var kelas = '.idx_6_input';
		var kelas_bobot = '.weight_6_input';
		var id_bobot = '#bobot_six';
		var id_tot = '#tot_six';
		calculate(kelas,kelas_bobot,id_bobot,id_tot);
		if(el_weight_6 == "" && el_obj_6 == "" && el_ach_6 == ""){
			$(element.target).closest('tr').find(kelas).val('').trigger('change');
			if($(id_tot).val() == "" || $(id_tot).val() == 'NaN'){				
				$(id_tot).val('').trigger('change');
			}
		}
});

function calculate(kelas,kelas_bobot,id_bobot,id_tot){
	var sum_bobot = 0;
	var b = false;
	var sum = 0;
	var s = false;
	$('#table_rec_quanti_body tr').each(function (index) {
		var val_idx = $(this).find(kelas).val();
		var val_bobot = $(this).find(kelas_bobot).val();
		if(val_idx != 'NaN'){
			if(val_idx != ''){
				s = true;
				if(parseFloat(val_idx) > parseFloat(val_bobot)){
					var idxs = val_bobot;
				}
				else{
					var idxs = val_idx;
				}
				sum += parseFloat(idxs);				
			}	
		}
		if(val_bobot != 'NaN'){
			if(val_bobot != ''){
				b = true;
				sum_bobot += parseFloat(val_bobot);		
			}	
		}			
	});
	if(b == true){
		$(id_bobot).val(parseFloat(parseFloat(sum_bobot).toFixed(2)));
	}
	else{
		$(id_bobot).val('').trigger('change');
	}
	
	if(s == true){
		if(parseFloat(sum) > 100){
			$(id_tot).val(parseFloat(parseFloat(100).toFixed(2)));
		}
		else{
			$(id_tot).val(parseFloat(parseFloat(sum).toFixed(2)));
		}
		
	}
	else{
		$(id_tot).val('').trigger('change');
	}
	tot_calculate();
	if($('#total_kpi').val() == 'NaN'){
		$('#total_kpi').val('');
	}
}

function tot_calculate(){
	var sum_tot = 0;
	var count_tot = 0;
	$('#table_rec_quanti_foot tr').each(function () {	
		$(this).find('th.sum_tot').each(function (i) {	
			var tot_idx = $(this).find('input').val();			
			if(tot_idx){
				if(tot_idx != 'NaN'){
					sum_tot += parseFloat(tot_idx);
					count_tot += 1;
				}
			}
		});		
	});

	$('#total_kpi').val(parseFloat(parseFloat(sum_tot/count_tot).toFixed(2)));	
	
	if($('#total_kpi').val() >= 85){
		$('#status_kpi').show();
		$('#status_kpi').html('Pass').addClass("badge badge-success").removeClass('badge-danger');
	}
	else if($('#total_kpi').val() < 85){
		$('#status_kpi').show();
		$('#status_kpi').html('Not Pass').addClass("badge badge-danger").removeClass('badge-success');
	}
	else{
		$('#status_kpi').hide();
	}
}

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#recoForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Recommendation Form");
	$("#save_and_draft").hide();
	$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').removeClass('editForm').addClass('addForm');
    $.ajax({
			url: "{{ route('reco_form.modal_detail') }}",
			data:{
				id_recommendation_header:0,
				type:'new',
			},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 			
		}
	});
}	

var AjaxUrl = "";
$('#recoForm').submit(function (e) {
		e.preventDefault();			
		let thisButtonId = e.originalEvent.submitter.id;
		if(thisButtonId == 'save_and_submit' || thisButtonId == 'save_and_draft'){
			let addForm = $("#save_and_submit").hasClass('addForm');
			if(addForm == true){
				AjaxUrl = "{{ route('reco_form.save') }}";
			} else {
				AjaxUrl = "{{ route('reco_form.update') }}";
			}
		}
		$(".invalid-feedback").children("strong").text("");
		$(".invalid-date").children("strong").text("");
		$("#recoForm input").removeClass("is-invalid");
		$("#recoForm select").removeClass("is-invalid");
		$("#recoForm textarea").removeClass("is-invalid");
		$(".table-invalid-feedback").children("strong").text(""); 
		$(".error-tab").html("");
		let formData = $(this).serializeArray();
		formData.push({name:'form',value:thisButtonId});
		$.ajax({
			type: 'POST',
			headers: {
				Accept: "application/json",
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			url: AjaxUrl,
			data: formData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(response) {
			   if (response.status == 'true') {
				   var data = response.data;
				   var trans = response.trans;
				   var type_submit = response.type_submit;
					$.ajax({
					  type: 'POST',
					  headers: {
						'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
					  },
					  url: "{{ route('mail.new_reco') }}",
					  data: {
						  source: data,
						  trans: trans,
						  type_submit: type_submit,
					  },
					});
				   $('#myModal').modal('hide');
					swal({
						icon: 'success',
						title: 'Success',
						text: response.message,
						buttons: {
							confirm: {
								className: 'btn-success'
							},
						},
					}).then(ok => {
						$('#reco_table').DataTable().ajax.reload();
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
						if (tab_id != undefined) {
							$("#tab_rec_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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

function loadedit(id_recommendation_header,type,sum){
	$("#contentBody").html('');
	if(type == 'view'){
		$("#save_and_draft").hide();
		$("#save_and_submit").hide();
		$("#modal-title").html("<span class='fas fa-eye'></span> View Recommendation Form");
	}
	else{
		$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Update & Submit').removeClass('addForm').addClass('editForm');
		$("#save_and_draft").show().html('<i class="fas fa-save"></i> Save as Draft').removeClass('addForm').addClass('editForm');
		$("#modal-title").html("<span class='fas fa-edit'></span> Edit Recommendation Form");
	}
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#recoForm input").removeClass("is-invalid");
	
    $.ajax({
			url: "{{ route('reco_form.modal_detail') }}",
			data:{
				id_recommendation_header:id_recommendation_header,
				type:type,
				sum:sum,
			},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

$('#advanced').click(function(){
	$('.cf').select2({width:'100%'});
	if($("#cf").css('display') == 'none'){
		$("#cf").show("slow");
	}
	else {
		$("#cf").hide("slow");
	}		
});


const get_datatable_employee = async () => {	
   
	$('#reco_table').DataTable({
		processing: true,
		pageLength: 10,
		responsive: true,     
	ajax: {
        url: "{{ route('reco_form.index') }}",
        error: function (jqXHR, textStatus, errorThrown) {
          $('#reco_table').DataTable().ajax.reload();
        }
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
      { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'space text-center'},
      { data: 'reference_number', name: 'reference_number' },
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'name', name: 'name' },
      { data: 'position', name: 'position' },
      { data: 'category', name: 'category' },
      { data: 'type', name: 'type' },
	  { data: 'desc_app_status', name: 'desc_app_status', className: 'text-center', render: function ( data, type, row ) {	
			if(row.code_app_status == 'Approved'){
					return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else if(row.code_app_status == 'Cancel'){
				return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else if(row.code_app_status == 'Partial_Approved'){
				return '<span class="badge badge-warning" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else if(row.code_app_status == 'Rejected'){
				return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else if(row.code_app_status == 'Revised'){
				return '<span class="badge badge-info" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else{
				return '<span class="badge" style="font-size: 12px;">'+data+'</span>';
			}
		}
	   },
      { data: 'note_revised', name: 'note_revised' },
      { data: 'note_rejected', name: 'note_rejected' }, 
	/*  { data: 'expired_date', name: 'expired_date', className: 'space text-center', render: function (data, type, row) {
			if(data != null){
				return moment(data).format('DD MMM YYYY');
			}
			else{
				return '-';
			}			
		}
	  },
	*/  
      { data: 'action', name: 'action', orderable: false, className: 'space text-center' }
      ],
	  "fnInitComplete": function (oSettings) {
		$('#reco_table_wrapper .column-filter-widget:eq(0)').css('display','none').change();
		$('#reco_table_wrapper .column-filter-widget:eq(1)').css('display','none').change();
		$('#reco_table_wrapper .column-filter-widget:eq(12)').css('display','none').change();
	  },
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
		
		if(data['code_app_status'] == 'New'){
			$(row).find('.print').css('display', 'none');
		}
		if(data['code_app_status'] == 'Approved'){
			$(row).find('.cancel').css('display', 'none');
			$(row).find('.edit').css('display', 'none');
		}
		if(data['code_app_status'] == 'Request_Approval'){
			$(row).find('.edit').css('display', 'none');
			$(row).find('.print').css('display', 'none');
		}
		if(data['code_app_status'] == 'Cancel'){
			$(row).find('.cancel').css('display', 'none');
			$(row).find('.edit').css('display', 'none');
			$(row).find('.print').css('display', 'none');
		}
		if(data['code_app_status'] == 'Rejected'){
		//	$(row).find('td:eq(20)').css('background', '#fe8590');
			$(row).find('.cancel').css('display', 'none');
			$(row).find('.print').css('display', 'none');
		}
		if(data['code_app_status'] == 'Partial_Approved'){
			$(row).find('.cancel').css('display', 'none');
			$(row).find('.edit').css('display', 'none');
			$(row).find('.print').css('display', 'none');
		}
		if(data['code_app_status'] == 'Revised'){
			$(row).find('.print').css('display', 'none');
		}
      },
    });
	
}	


$(document).ready(function(){
	  	  
	get_datatable_employee();
	get_appraiser();
	
	decision = [
		{
			id: 'Promotion',
			text: 'Promotion'
		},
		{
			id: 'Demotion',
			text: 'Demotion'
		},
		{
			id: 'Extended',
			text: 'Extended'
		},
		{
			id: 'Failed',
			text: 'Failed'
		},
	];
	
	month_period = [
		{
			id: '3',
			text: '3'
		},
		{
			id: '4',
			text: '4'
		},
		{
			id: '5',
			text: '5'
		},
		{
			id: '6',
			text: '6'
		},
	];
	
	global_appraiser_type = [
		{
			id: 'direct',
			text: 'Direct Line (Atasan Langsung)'
		},
		{
			id: 'subordinat',
			text: 'Subordinat (Anggota Tim)'
		},
		{
			id: 'peers',
			text: 'Peers (Rekan Kerja)'
		},		
	];
	
}); 

const get_employee = async (id_employee,type) => {
	let result;
	let myData = {
		id_employee: id_employee,
		type: type,
	};
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_employee') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (data) {
				$('#id_employee').prepend('<option selected></option>').select2({
					placeholder: "Select Employee ...",
					data: data,
				//	allowClear: true,
				});
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
        get_employee(id_employee,type);
    }	
}

const get_detail_employee = async (id_employee,emp_status_code) => {
	let result;
	let myData = {
		id_employee: id_employee,
		emp_status_code: emp_status_code,
	};
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_detail_employee') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (data) {
				$('#new_gen_quanti').show();
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {}	
}

$(document).on('change', '#id_employee', function (event, istrigger) {    
	if(!istrigger){	
			get_detail_employee($(this).select2('val'),$(this).select2('data')[0].emp_status_code).then(function(res) {
				global_id_employee = res[0].id_employee;
				$('#new_gen_quanti').attr('onclick','genKpi('+global_id_employee+')');
				$('#id_position_detail').val(res[0].id_position_detail).trigger('change');
				$('#position').val(res[0].position_route).trigger('change');
				$('#grade').val(res[0].grade).trigger('change');
				$('#dept').val(res[0].dept).trigger('change');
				$('#principal').val(res[0].principal).trigger('change');
				$('#id_employment_status').val(res[0].id_employment_status).trigger('change');
				$('#emp_status').val(res[0].emp_status).trigger('change');
				$('#region').val(res[0].region).trigger('change');
				$('#branch').val(res[0].branch).trigger('change');
				$('#location').val(res[0].location).trigger('change');
				setTimeout(function () {
					get_hierachy(type,id_recommendation_header,res[0].id_location);
				}, 300);	
				$('#id_location').val(res[0].id_location).trigger('change');
				if(res[0].start_date != null){
				//	$('#start_date').val(moment(res[0].start_date).format('DD MMM YYYY')).trigger('change');
					$('#start_date').val(res[0].start_date).trigger('change');
				}
				else{
					$('#start_date').val('').trigger('change');
				}
				if(res[0].end_date != null){
				//	$('#end_date').val(moment(res[0].end_date).format('DD MMM YYYY')).trigger('change');
					$('#end_date').val(res[0].end_date).trigger('change');
				}
				else{
					$('#end_date').val('').trigger('change');
				}
				
				if(res[0].start_date != null && res[0].end_date != null){
					var startDate = moment(res[0].start_date);
					var endDate = moment(res[0].end_date);
					var duration = moment.duration(endDate.diff(startDate));
					var monthPeriod = parseInt(duration.asDays()/30);
					$('#month_period').val(monthPeriod).trigger('change');			
				}
				else{
					$('#month_period').val(null).trigger('change');
				}
						
			});
	}
});

/*
function get_hierachy(type,id_recommendation_header){
	$.getJSON('<?= url('employee/employee/recommendation_form/get_hierachy') ?>', function (data) {
			$('#id_approval').select2({
				placeholder: "No Hierarchy Approval ...",
				data: data,
			});
			}).fail(function (data) { // Call failed
				get_hierachy(type,id_recommendation_header);
			});		
}
*/

const get_hierachy = async (type,id_recommendation_header,id_location) => {
	let result;
	let myData = {
			type: type,
			id_recommendation_header: id_recommendation_header,
			id_location: id_location,
		};
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_hierachy') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_approval').empty();
				$('#load_id_approval').show();
			},
            success: function (data) {
				$('#id_approval').select2({
					placeholder: "No Hierarchy Approval ...",
					data: data,
				});
            },
			complete: function(){
				$('#load_id_approval').hide();
			},
        });
        return result;
    } catch (error) {
     //   get_hierachy(type);
    }	
}

function get_approval_status(){		
		$.getJSON('<?= url('employee/employee/recommendation_form/get_approval_status') ?>', function (data) {
			$('#id_approval_status').select2({
				data: data,
				});
		}).fail(function (data) { // Call failed
            get_approval_status();
		});					
}

const get_category = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_category') ?>',
            dataType: 'json',
            success: function (data) {
				$('#id_transition_category').prepend('<option selected></option>').select2({
					placeholder: "Select Category ...",
					data: data,
				});
            },
        });
        return result;
    } catch (error) {
        get_category();
    }	
}

const get_type = async (code) => {
	let result;
	let myData = {
			code: code,
		};
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_type') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_transition_type').empty();
				$('#load_id_type').show();
			},
            success: function (data) {
				$('#id_transition_type').prepend('<option selected></option>').select2({
					placeholder: "Select Type ...",
					data: data,
				});
            },
			complete: function(){
				$('#load_id_type').hide();
			},
        });
        return result;
    } catch (error) {
    //    get_type(id);
    }	
}

$(document).on('change', '#id_transition_category', function (event, istrigger) {    
	if(!istrigger){	
		$('#dur').hide();
		$('#duration').attr('disabled',true);
		get_type($(this).select2('data')[0].code);
	}
}); 

$(document).on('change', '#id_transition_type', function (event, istrigger) {    
	if(!istrigger){	
		$('#effective_date').val('').change();
		$('#expired_date').val('').change();
		if($(this).select2('data')[0].text == 'Orientation' || $(this).select2('data')[0].text == 'Promotion' || $(this).select2('data')[0].text == 'Temporary Assignment'){
			get_pro_ori();
			$('#expired_date').attr('disabled',false);
			$('#expired_date').parent().children('span').children('button').attr('disabled',false);
		}
		else if($(this).select2('data')[0].text == 'Terminate'){
			$('#expired_date').attr('disabled',false);
			$('#expired_date').parent().children('span').children('button').attr('disabled',false);
		}
		else{
			$('#expired_date').attr('disabled',true);
			$('#expired_date').parent().children('span').children('button').attr('disabled',true);
			$('#dur').hide();
			$('#duration').attr('disabled',true);
		}
		$("#with_reco").addClass('readonly-checkbox');
		get_help($(this).select2('data')[0].text);
	}
}); 	

function get_pro_ori(){
	$('#dur').show();
	$('#duration').attr('disabled',false);			
	$('#effective_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	}).on('change', function (e) {	
		if($('#effective_date').val() == ''){
			$('#des_eff').val('');
		}
		else{
			$('#des_eff').val(moment($('#effective_date').val()).format('DD MMM YYYY')).trigger('change');
		}	
		$('#des_exp').val('');
		$('#duration').val('');
		$('#expired_date').datepicker('destroy');
		$('#expired_date').addClass('form-control-sm');
		$('#expired_date').datepicker({
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
			minDate: $('#effective_date').val(),
		}).on('change', function (e) {
			if($('#expired_date').val() == ''){
				$('#des_exp').val('');
			}
			else{
				$('#des_exp').val(moment($('#expired_date').val()).format('DD MMM YYYY')).trigger('change');
				
				var firstDate = moment($('#effective_date').val());
				var secondDate = moment($('#expired_date').val());						
				var duration = moment.duration(secondDate.diff(firstDate));
				var monthDiff = parseInt(duration.asDays()/30);				
				$('#duration').val(monthDiff);
			}		
		});
		
	});	
	
	$('#expired_date').datepicker({
		uiLibrary: 'bootstrap4',
		format: 'yyyy-mm-dd',
	});
}

function get_help(text_help){
	if(text_help == 'Orientation'){
			notes_help = "Perpanjangan Orientasi/Acting";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Promotion'){
			notes_help = "Penempatan karyawan pada posisi lain yang mempunyai level atau tingkatan yang lebih tinggi dari posisi sebelumnya baik dalam satu atau antar departemen/divisi/cabang/depo";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Demotion'){
			notes_help = "Penempatan karyawan pada posisi lain yang mempunyai level atau tingkatan yang lebih rendah dari posisi sebelumnya baik dalam satu atau antar departemen/divisi/cabang/depo";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Mutation'){
			notes_help = "Penempatan karyawan pada posisi lain yang mempunyai level atau tingkatan yang sama antar departemen/divisi/cabang/depo";
			$('#with_reco').prop('checked', false).change();
		}
		else if(text_help == 'Relocation'){
			notes_help = "Perpindahan karyawan karena adanya perpindahan depo/lokasi kerja";
			$('#with_reco').prop('checked', false).change();
		}
		else if(text_help == 'Rotation'){
			notes_help = "Penempatan Karyawan pada posisi lain yang mempunyai level atau tingkatan yang sama dalam satu departemen/divisi/cabang/depo ";
			$('#with_reco').prop('checked', false).change();
		}
		else if(text_help == 'Pass Orientation'){
			notes_help = "Lulus Orientasi/Acting";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Failed Orientation'){
			notes_help = "Gagal Orientasi/Acting";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Employment Status Changes'){
			notes_help = "Passprobation karyawan PKWTT";
			$('#with_reco').prop('checked', true).change();
		}
		else if(text_help == 'Temporary Assignment'){
			notes_help = "Tugas Sementara";
			$('#with_reco').prop('checked', false).change();
		}
		else if(text_help == 'Terminate'){
			notes_help = "Untuk Contract - proses rehire PKWT dan Permanent - proses rehire PKWTT";
			$('#with_reco').prop('checked', true).change();
		}
		else{
			notes_help = "-";
			$("#with_reco").removeClass('readonly-checkbox');
		}
		$('.help_input').attr('data-content', notes_help);
}
/*
const get_new_position = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_new_position') ?>',
            dataType: 'json',
            success: function (data) {
				$('#id_position').prepend('<option selected></option>').select2({
					placeholder: "Select Position ...",
					data: data,
				});
            },
        });
        return result;
    } catch (error) {
        get_new_position();
    }	
}
*/

const get_new_status = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_new_status') ?>',
            dataType: 'json',
            success: function (data) {
				$('#id_new_emp_status').prepend('<option selected></option>').select2({
					placeholder: "Select Employment Status ...",
					data: data,
				});
            },
        });
        return result;
    } catch (error) {
        get_new_status();
    }	
}

$(document).on('click', '.get_new_position', function () {
	$('#loader').removeClass('hidden');
	$('#browseModaljob').modal('show');		
	var table = $("#bro_table_job").DataTable({
		responsive: true,
		processing: false,
		paging: true,
		pageLength:10,
		destroy:true,
		lengthChange: true,
		searching: true,
		ordering: true,
		info: true,
		autoWidth: true,
		columnDefs:false,
		dom: '<"toolbar">frtip',
		columns : [
		  {
                defaultContent: '',
				orderable: false, className: 'space text-center'
				},
            { data : 'position_routing' },
            { data : 'job_grade' },
            { data : 'branch' },
            { data : 'location' },
            { data : 'principal' },
            { data : 'department' },
        //    { data : 'name' },
            { data : 'action', className: 'space text-center'},
          ],    
          ajax: {
            type: 'GET',
   			url: "<?= url('employee/employee/recommendation_form/get_new_position') ?>",
			error: function (jqXHR, textStatus, errorThrown) {
			//	$('#bro_table_job').DataTable().ajax.reload();
			},
			complete: function(){
				$('#loader').addClass('hidden');
			},
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'position_routing'   : json[i]['position_routing'],
                  'job_grade'   : json[i]['job_grade'],
                  'branch'   : json[i]['branch'],
                  'location'   : json[i]['location'],
                  'principal'   : json[i]['principal'],
                  'department'   : json[i]['department'],
            //      'name'   : json[i]['name'],
                  'action'   : '<button type="button" name="check" class="btn btn-success btn-sm" title="Check" onClick="checkpos('+json[i]['id_routing']+','+json[i]['id_location']+','+json[i]['id_principal']+')"><i class="fa fa-check-square-o" aria-hidden="true"></i></button>',
                })
                no++;
              }
              return return_data;
            }
          }
      });
	  
	  table.on('order.dt search.dt', function () {
        let i = 1;
        table.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();

});

function checkpos(idRouting,idLocation,idPrincipal) {	
	  $.ajax({
		   url: "<?= url('employee/employee/recommendation_form/checkpos') ?>",
		   dataType:"json",
		   data:{
				jobid:idRouting,
				jobidLoc:idLocation,
				jobidPrincipal:idPrincipal,
			},
		   success:function(data)
		   {
				$('#new_position_routing').val(data.result.position_routing).trigger('change');
				$('#id_new_position_detail').val(data.result.id_position_detail).trigger('change');
				$('#new_dept').val(data.result.department).trigger('change');
				$('#new_grade').val(data.result.job_grade).trigger('change');
				$('#new_branch').val(data.result.branch).trigger('change');
				$('#new_location').val(data.result.location).trigger('change');
				$('#new_principal').val(data.result.principal).trigger('change');
				get_new_mgr(data.result.id_position_detail);
				$("#browseModaljob").modal('hide');
		   }
	  })
}

function get_month_period(){
	$('#month_period').prepend('<option selected></option>').select2({
		placeholder: "Select ...",
		data: month_period,
		allowClear: true,
	});
}

function get_reco_check(type,datenow){
	$('#with_reco:checkbox').on('change', function (e) {
	//	$('#loader').removeClass('hidden');		
			if(type == 'new' && datenow == null){
				var start = moment();
			}
			else{
				var start = moment(datenow);				
			}
			start.subtract(1, 'months');
			let nextMonths = [];
			for (let i = 1; i <= 6; i++) {
				nextMonths.push(start.format('MMM YY'));
				start.subtract(1, 'months');
			}
			if(this.checked){									
				$('#id_reco_check').show();				
				$('#m_one').html(nextMonths[5]);
				$('#m_two').html(nextMonths[4]);
				$('#m_three').html(nextMonths[3]);
				$('#m_four').html(nextMonths[2]);
				$('#m_five').html(nextMonths[1]);
				$('#m_six').html(nextMonths[0]);
				if(type == 'new'){
					$("#save_and_submit").hide();
					$("#save_and_draft").show().html('<i class="fas fa-save"></i> Save as Draft').removeClass('editForm').addClass('addForm');
				}			
			}
			else {
				$('#id_reco_check').hide();
				if(type == 'new'){
					$("#save_and_draft").hide();
					$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').removeClass('editForm').addClass('addForm');
				}
			}
		});
}

const get_appraiser = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_appraiser') ?>',
            dataType: 'json',
            success: function (res) {
				global_id_appraiser = res;
            },
        });
        return result;
    } catch (error) {
     //   get_branch();
    }	
} 

const get_decision = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_decision') ?>',
            dataType: 'json',
			beforeSend: function () {
				$('#decision').empty();
			},
            success: function (res) {
				$('#decision').prepend('<option selected></option>').select2({
					placeholder: "Select Decision ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
     //   get_branch();
    }	
} 

const get_new_mgr = async (id_position_detail) => {
	let result;
	let myData = {
		id_position_detail: id_position_detail,
	};
    try {
        result = await $.ajax({
            url: '<?= url('employee/employee/recommendation_form/get_new_mgr') ?>',
            dataType: 'json',
			data: myData,
			beforeSend: function () {
				$('#id_new_mgr').empty();
			},
            success: function (res) {
				$('#id_new_mgr').select2({
					data: res,
				});
            },
        });
        return result;
    } catch (error) {
     //   get_branch();
    }	
} 

function get_sumQualitative(id_employee_participant,id_recommendation_header) {
	let myData = {
		id_employee_participant: id_employee_participant,
		id_recommendation_header: id_recommendation_header,
	};
	$.extend( true, $.fn.dataTable.defaults, {
		 columnDefs:false,
		 paging:false,
		 searching:false,
		 lengthChange: false,
		 info: false,
		 dom: '<"toolbar">frtip',
	});
	
	$('#table_sum_qualitative').DataTable({	
		processing: true,
		destroy:true,
		ajax: {
			url: '<?= url('employee/employee/recommendation_form/get_sumQualitative') ?>',
			data : myData,
			error: function (jqXHR, textStatus, errorThrown) {
					$('#table_sum_qualitative').DataTable().ajax.reload();
				}
		},	
		columns: [								
			{data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-width'},
			{data: 'question', name: 'question', width: '600px', render: function(data, type, row, meta) {
				let node = $.parseHTML( '<div style="width:600px;">' + data + '</div>' )[0];
				return node.innerText;
				}
			},
			{data: 'level_5', name: 'level_5', className: 'text-width', width: '30px'},
			{data: 'level_4', name: 'level_4', className: 'text-width', width: '30px'},
			{data: 'level_3', name: 'level_3', className: 'text-width', width: '30px'},
			{data: 'level_2', name: 'level_2', className: 'text-width', width: '30px'},
			{data: 'level_1', name: 'level_1', className: 'text-width', width: '30px'},
			{data: 'avg', name: 'avg', className: 'text-center', render: function(data, type, row, meta) {
				return '<b>'+data+'</b>';
				}
			},
		]
	});
}

$(document).on('click', '.cancel', function (event) {
	id_recommendation_header = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This Data will be Cancel!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, Cancel it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"recommendation_form/cancel/"+id_recommendation_header,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#reco_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#reco_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});

function genKpi(id_employee) {
	$.ajax({
		url: "<?= url('employee/employee/recommendation_form/get_kpi_detail') ?>",
		method: "GET",
        data: {
			id_employee: id_employee,
		},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
		success: function (response) {
			if (response.status == 'true') {
				$.each(response.data, function (i, item) {
					if(item.kpi_type_code == 'Lurus'){
						$('#new_rec_quanti').trigger('click',['new']);
					}
				});	
				
				setTimeout(function () {
					var index = 0;
					$('#table_rec_quanti_body tr').each(function (i) {
						if(response.data.length > 0){
							if(response.data[index].kpi_type_code == 'Lurus'){
								$(this).find('span.sn').html(i + 1);
								if($(this).find('span.sn').attr('id') != 'idx_quanti'){
									$(this).find('span.sn').attr('id','idx_quanti');
									$(this).find('.desc_kpi_input').val(response.data[index].description).trigger('change');
									if(response.data[index]._1_month_ago != null){
										$(this).find('.weight_6_input').val(response.data[index]._1_month_ago[0]).trigger('change');
										$(this).find('.obj_6_input').val(response.data[index]._1_month_ago[1]).trigger('change');
										$(this).find('.ach_6_input').val(response.data[index]._1_month_ago[2]).trigger('change');
									}
									if(response.data[index]._2_month_ago != null){
										$(this).find('.weight_5_input').val(response.data[index]._2_month_ago[0]).trigger('change');
										$(this).find('.obj_5_input').val(response.data[index]._2_month_ago[1]).trigger('change');
										$(this).find('.ach_5_input').val(response.data[index]._2_month_ago[2]).trigger('change');
									}
									if(response.data[index]._3_month_ago != null){
										$(this).find('.weight_4_input').val(response.data[index]._3_month_ago[0]).trigger('change');
										$(this).find('.obj_4_input').val(response.data[index]._3_month_ago[1]).trigger('change');
										$(this).find('.ach_4_input').val(response.data[index]._3_month_ago[2]).trigger('change');
									}
									if(response.data[index]._4_month_ago != null){
										$(this).find('.weight_3_input').val(response.data[index]._4_month_ago[0]).trigger('change');
										$(this).find('.obj_3_input').val(response.data[index]._4_month_ago[1]).trigger('change');
										$(this).find('.ach_3_input').val(response.data[index]._4_month_ago[2]).trigger('change');
									}
									if(response.data[index]._5_month_ago != null){
										$(this).find('.weight_2_input').val(response.data[index]._5_month_ago[0]).trigger('change');
										$(this).find('.obj_2_input').val(response.data[index]._5_month_ago[1]).trigger('change');
										$(this).find('.ach_2_input').val(response.data[index]._5_month_ago[2]).trigger('change');
									}
									if(response.data[index]._6_month_ago != null){
										$(this).find('.weight_1_input').val(response.data[index]._6_month_ago[0]).trigger('change');
										$(this).find('.obj_1_input').val(response.data[index]._6_month_ago[1]).trigger('change');
										$(this).find('.ach_1_input').val(response.data[index]._6_month_ago[2]).trigger('change');
									}
									index++;
								}
							}
						}
						
					});
				}, 800);
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

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(id_recommendation_header) {
	let res = {
        id_recommendation_header: id_recommendation_header,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('employee/employee/recommendation_form/download') }}";
    window.open(url+'?'+param, '_blank');
}

$(document).on('click', '.clear_pos', function (event) {
	$('#new_position_routing').val('').trigger('change');
	$('#id_new_position_detail').val('').trigger('change');
	$('#new_dept').val('').trigger('change');
	$('#new_grade').val('').trigger('change');
	$('#new_branch').val('').trigger('change');
	$('#new_principal').val('').trigger('change');
	$('#id_new_mgr').empty().prepend('<option selected></option>').select2({
		placeholder: "Select Recipient Manager ...",
	});
});
</script>  
@endsection