@extends('adminlte::page')
@section('title', 'KPI Performance')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">KPI Performance (Quantitative)</h5>               
            </div>      
			<div class="card-body">
			<div class="form-group row">              
					<label class="col-sm-2 col-form-label">Period :</label>
                    <div class="col-sm-5">
                        <select id="period" class="form-control form-control-sm select2" style="width: 100%;"></select>
                    </div>
					<div class="col-sm-2">
                        <button onclick="return false;" id="search_period" class="btn btn-sm btn-success" ><i class="fa fa-filter"></i> Filter</button>
                    </div>										
            </div>
				<div class="div_datatable" style="display:none;"> 
				<button onclick="return false;" class="btn btn-default pull-left advanced_kpi">Advanced Search</button>
					<br>
					<br>
					<table id="kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>						
						<th>NIK Dinilai</th>
						<th data-priority="2">Nama Dinilai</th>
						<th data-priority="3">Grade</th>
						<th>Periode</th>
						<th data-priority="4">KPI Total(%)</th>
						<th data-priority="1" style="text-align:center;" width=50>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
			</div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_form_kpi"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
	<div id="modal_second"></div>
        <div class="modal-content">
            <form method="POST" id="kpiForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">KPI Monthly</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="height: 500px;overflow-y: auto;">
                     <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
                                <div class="col-sm-8">
									<input name="id_kpi_group" id="id_kpi_group" type="hidden">	
                                    <input type="text" id="employee" class="form-control form-control-sm" disabled>
                                </div>
                            </div>		
							<div class="row">
                                <label class="col-sm-4 col-form-label">KPI Score(%)</label>
                                <div class="col-sm-8">
                                    <input type="text" id="average_prosentase" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" id="description" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
                        </div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Position</label>
                                <div class="col-sm-8">
                                    <input type="text" id="position" class="form-control form-control-sm" disabled>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grade</label>
                                <div class="col-sm-8">
                                    <input type="text" id="grade" class="form-control form-control-sm" disabled>                                    
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Department</label>
                                <div class="col-sm-8">
                                    <input type="text" id="dept" class="form-control form-control-sm" disabled>                                    
                                </div>
                            </div>																														
                        </div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_grade_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_monthly-details" data-toggle="pill" href="#monthly-details" role="tab" aria-controls="link_tab_monthly-details" aria-selected="true">KPI Monthly <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_grade_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="monthly-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
										<button type="button" class="pull-right btn btn-xs btn-primary" id="new_monthly_detail" style="margin-left:20px;display:none;"></button>
										<button type="button" class="viewkpi pull-right btn btn-xs btn-warning" style="color:white;font-weight:bold;"><span class="far fa-eye"></span> Detail KPI Monthly</button>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_monthly_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="width:20px;">No.</th>
                                                        <th style="white-space:nowrap;">Month</th>
                                                        <th style="white-space:nowrap;">Notes</th>
                                                        <th style="white-space:nowrap;">Score Monthly</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_monthly_body">
                                                </tbody>
                                            </table>                                          
                                        </div>
                                    </div>
                                </div>
                               
                            </div>
                        </div>
                    </div>

                </div>
				<div class="modal-footer">
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
			 <div style="display:none;">
                <table id="sample_table_monthly">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td>
								<input name="monthly[0][id_kpi_header]" id="monthly_0_id_kpi_header" type="hidden" class="form-control form-control-sm id_kpi_header_input">
								<input id="monthly_0_month" class="form-control form-control-sm month_input" style="width: 100%;" disabled>
                        </td>
						<td>
                                <input id="monthly_0_notes" class="form-control form-control-sm notes_input" style="width: 100%;" disabled>
                        </td>
						<td>
                                <input id="monthly_0_subtotal_kpi" class="form-control form-control-sm subtotal_kpi_input" style="width: 100%;" disabled>
                        </td>
						<td>
                    <center>
                        <button type="button" class="additem btn btn-xs btn-success" data-id="0" title="Item KPI"><span class="far fa-list-alt"></span></button>
                    </center>
                    </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_view_kpi" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail KPI Monthly</h5>
                    <button type="button" onclick="second_close()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
						<table id="detail_kpi_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
						 <thead>
						 <tr>
							<th rowspan="2" style="vertical-align: middle;"></th>
							<th rowspan="2" style="vertical-align: middle;"></th>
							<th rowspan="2" data-priority="2" style="vertical-align: middle;">No</th>						
							<th rowspan="2" data-priority="1" style="vertical-align: middle;">Item KPI</th>
							<th rowspan="2" data-priority="3" style="vertical-align: middle;">Type</th>
							<th colspan="12" style="text-align: center;border-bottom: none;">Score KPI Monthly(%)</th>
						  </tr>
						  <tr>				   							
							<th>Jan</th>
							<th>Feb</th>
							<th>Mar</th>
							<th>Apr</th>
							<th>Mei</th>
							<th>Jun</th>
							<th>Jul</th>
							<th>Ags</th>
							<th>Sep</th>
							<th>Okt</th>
							<th>Nov</th>
							<th>Des</th>
						  </tr>
						 </thead>
						</table>              
                </div>
				<div class="modal-footer">
                    <button type="button" onclick="second_close()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
		</div>
    </div>
</div>

<div class="modal fade" id="modal_item_kpi" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-item" role="document">
        <div class="modal-content">
		<form method="post" id="itemkpiForm">
		{{ csrf_field() }}
                <div class="modal-header">
                    <h5 id="id_month" class="modal-title"></h5>
                    <button type="button" onclick="second_close()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="body_item">
					 <div class="row">
						<input name="id_kpi_header" id="id_kpi_header" type="hidden">	
						 <div class="col-md-12" style="margin-bottom: 10px">											
							<button type="button" class="pull-right btn btn-xs btn-primary" id="new_item_detail" style="margin-left:10px;"><span class="fas fa-plus"></span> Add Item KPI</button>
							<button type="button" id="gensfa" onclick="#" class="pull-right btn btn-xs btn-success"><span class="fa fa-refresh"></span> Generate From SFA</button>
						</div>
						<div class="col-md-12">
							<table id="table_item_kpi" class="table table-striped table-bordered table-hover datatable">
								<thead>
									<tr align="center">
										<th rowspan="2" style="vertical-align: middle;width:20px;">No</th>						
										<th rowspan="2" style="vertical-align: middle;">Item KPI</th>
										<th rowspan="2" style="vertical-align: middle;width:200px;">Type</th>
										<th colspan="3" style="text-align: center;border-bottom: none;padding: 5px"><div id="item_month"></div></th>
										<th rowspan="2" style="vertical-align: middle;width:50px;">Action</th>
									</tr>
									<tr align="center">										
										<th style="padding: 2px;width:100px;">Bobot</th>
										<th style="padding: 2px;width:100px;">Target</th>
										<th style="padding: 2px;width:100px;">Aktual</th>
									</tr>
								</thead>
								<tbody id="table_item_body">
								</tbody>
							</table>                                          
						</div>
					</div>	           
                </div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-sm btn-success" id="save_button_item"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="second_close()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
				</form>
				 <div style="display:none;">
                <table id="sample_table_item">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td>
								<input name="itemkpi[0][id_kpi_detail]" id="itemkpi_0_id_kpi_detail" type="hidden" class="form-control form-control-sm id_kpi_detail_input">
								<select name="itemkpi[0][id_kpi_category]" id="itemkpi_0_id_kpi_category" class="form-control form-control-sm select2 id_kpi_category_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_kpi_category_input_error" role="alert" id="itemkpi_0_id_kpi_categoryError">
                                    <strong></strong>
                                </span>	
                        </td>
						<td>
                                <select name="itemkpi[0][id_kpi_type]" id="itemkpi_0_id_kpi_type" class="form-control form-control-sm select2 id_kpi_type_input" style="width: 100%;"></select>
								<span class="invalid-feedback id_kpi_type_input_error" role="alert" id="itemkpi_0_id_kpi_typeError">
                                    <strong></strong>
                                </span>	
                        </td>
						<td>
                                <input type="text" name="itemkpi[0][weight]" id="itemkpi_0_weight" class="form-control form-control-sm weight_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        </td>
						<td>
                                <input type="text" name="itemkpi[0][target]" id="itemkpi_0_target" class="form-control form-control-sm target_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                        </td>
						<td>
                                <input type="text" name="itemkpi[0][kpi_value]" id="itemkpi_0_kpi_value" class="form-control form-control-sm kpi_value_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
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
	}
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let global_monthly_detail = 0;
let global_id_kpi_group = 0;
let global_id_kpi_header = 0;
let global_id_kpi_detail = 0;
let global_id_category = [];
let global_id_type = [];

/*
    String.prototype.reverse = function () {
        return this.split("").reverse().join("");
    }

    function reformatText(input) {        
        var x = input.value;
        x = x.replace(/,/g, ""); // Strip out all commas
        x = x.reverse();
        x = x.replace(/.../g, function (e) {
            return e + ",";
        }); // Insert new commas
        x = x.reverse();
        x = x.replace(/^,/, ""); // Remove leading comma
        input.value = x;
    }
*/	
$(document).on('click', '.additem', function () {	
	$("#table_item_body").html('');
	global_id_kpi_header = $(this).attr('id');	
	$.ajax({
			url: "<?= url('kpi/kpi/pa_quantitative_assesment/get_item_kpi') ?>",
            method: "GET",
            data: {id_kpi_header: global_id_kpi_header},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
			//	console.log(response);
				$("#table_item_body").html(response);
				global_id_kpi_detail = 0;
				 $.each(response.itemkpi, function (i, item) {
					$('#new_item_detail').trigger('click');
				});
				$("#id_month").html("Item KPI "+response.month);
				$("#item_month").html(response.month);
				$('#id_kpi_header').val(response.id_kpi_header).trigger('change');
				setTimeout(function () {
						$('#table_item_body tr').each(function (index) {
							$(this).find('span.sn').html(index + 1);
							$(this).find('.id_kpi_detail_input').val(response.itemkpi[index].id_kpi_detail).trigger('change');
							$(this).find('.id_kpi_category_input').val(response.itemkpi[index].id_kpi_category).trigger('change');
							$(this).find('.id_kpi_type_input').val(response.itemkpi[index].id_kpi_type).trigger('change');
							$(this).find('.weight_input').val(response.itemkpi[index].weight).trigger('change');
							$(this).find('.target_input').val(response.itemkpi[index].target).trigger('change');
							$(this).find('.kpi_value_input').val(response.itemkpi[index].kpi_value).trigger('change');
						});		
					}, 500);
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
	$("#modal_second").addClass("modal-backdrop fade show");
	$('#modal_item_kpi').modal('show');		
});

$('#itemkpiForm').submit(function (e) {
		e.preventDefault();
		let formData = $(this).serializeArray();			
			$.ajax({
				type: 'POST',
				headers: {
					Accept: "application/json",
				},
				url: "{{ route('itemkpi.update') }}",
				data: formData,
				beforeSend: function () {
				//	$("#table_monthly_body").html('');
					$('#loader').removeClass('hidden');
				},
				success: function (response) {
					if (response.status == 'true') {
						 $('#modal_item_kpi').modal('hide');
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						second_close();
					//	$("#table_monthly_body").html(response);
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

function second_close(){
	$("#modal_second").removeClass("modal-backdrop fade show");
}
 $(document).on('click', '.viewkpi', function () {
	 $("#modal_second").addClass("modal-backdrop fade show");
	 
		$('#detail_kpi_table').DataTable({
			processing: true,
			responsive: true,
			destroy: true,
		//	'rowsGroup': [3],
			ajax: {
				url: "<?= url('kpi/kpi/pa_quantitative_assesment/get_kpi_view') ?>",
				data: {id_kpi_group: global_id_kpi_group},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#detail_kpi_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{
				defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
				data: 'id_kpi_category',
				defaultContent: '',
				orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},				
				{ data: 'item_kpi', name: 'item_kpi'},
				{ data: 'type_kpi', name: 'type_kpi',render: function ( data, type, row ) {						
						data = data.toLowerCase().replace(/\b[a-z]/g, function(letter) {
							return letter.toUpperCase();
						});						
						return data;
					}
				},           
				{ data: 'jan', name: 'jan', render: function ( data, type, row ) {	
						var dat = 0;
						if(row.type_kpi == 'lurus'){
							if(row.jan_value == null || row.jan_target == null){
									dat = null;
							}
							else if(row.jan_target == 0){
								dat = 0;
							}
							else{
								var jan_kpi = (parseFloat(row.jan_value) / parseFloat(row.jan_target)) * 100;
								if(jan_kpi > 100){
									var val = (100 * parseFloat(row.jan_weight))/100;
									dat = val.toFixed(2);
								}
								else{	
									var val = (parseFloat(jan_kpi) * parseFloat(row.jan_weight))/100;
									dat = val.toFixed(2);
								}					
							}
						}
						else if(row.type_kpi == 'terbalik'){
							var jan_kpi = (1-(parseFloat(row.jan_value) / parseFloat(row.jan_target)))*100 ;
							if(jan_kpi < 0){
								dat = 0;
							}
							else if(row.jan_value == null || row.jan_target == null){
								dat = null;
							}
							else if(jan_kpi >= 0){
								var val = (parseFloat(jan_kpi) * parseFloat(row.jan_weight))/100;
								dat = val.toFixed(2);
							}
						}
						else if(row.type_kpi == 'hit_miss_lurus'){
							if(row.jan_value == null || row.jan_target == null){
								dat = null;
							}
							else if(row.jan_value >= row.jan_target){
								dat = row.jan_weight;
							}
							else{
								dat = 0;
							}
						}
						else if(row.type_kpi == 'hit_miss_terbalik'){
							if(row.jan_value == null || row.jan_target == null){
								dat = null;
							}
							else if(row.jan_value < row.jan_target){
								dat = row.jan_weight;
							}
							else{
								dat = 0;
							}
						}
						else if(row.type_kpi == 'terbalik_2'){
							if(row.jan_target == 0){
								if(row.jan_target < row.jan_value){
									dat = 0;
								}
								else if(row.jan_target == 0 && row.jan_value == 0){
									var val = (100 * parseFloat(row.jan_weight))/100;
									dat = val.toFixed(2);
								}
								else{
									dat = null;
								}
							
							}
							else if(row.jan_target >= row.jan_value){
								var val = (100 * parseFloat(row.jan_weight))/100;
								dat = val.toFixed(2);
							}						
							else{
								var jan_kpi = (parseFloat(row.jan_target) / parseFloat(row.jan_value))*100 ;
								var val = (parseFloat(jan_kpi) * parseFloat(row.jan_weight))/100;
								dat = val.toFixed(2);
							}
						}
						return dat;
					}  
				},
				{ data: 'feb', name: 'feb', render: function ( data, type, row ) {	
						var dat = 0;
						if(row.type_kpi == 'lurus'){
							if(row.feb_value == null || row.feb_target == null){
									dat = null;
							}
							else if(row.feb_target == 0){
								dat = 0;
							}
							else{
								var feb_kpi = (parseFloat(row.feb_value) / parseFloat(row.feb_target)) * 100;
								if(feb_kpi > 100){
									var val = (100 * parseFloat(row.feb_weight))/100;
									dat = val.toFixed(2);
								}
								else{	
									var val = (feb_kpi * parseFloat(row.feb_weight))/100;
									dat = val.toFixed(2);
								}					
							}
						}
						else if(row.type_kpi == 'terbalik'){
							var feb_kpi = (1-(parseFloat(row.feb_value) / parseFloat(row.feb_target)))*100 ;
							if(feb_kpi < 0){
								dat = 0;
							}
							else if(row.feb_value == null || row.feb_target == null){
								dat = null;
							}
							else if(feb_kpi >= 0){
								var val = (parseFloat(feb_kpi) * parseFloat(row.feb_weight))/100;
								dat = val.toFixed(2);
							}
						}
						return dat;
					}
				},
				{ data: 'mar', name: 'mar' , render: function ( data, type, row ) {	
						var dat = 0;
						if(row.type_kpi == 'lurus'){
							if(row.mar_value == null || row.mar_target == null){
									dat = null;
							}
							else if(row.mar_target == 0){
								dat = 0;
							}
							else{
								var mar_kpi = (parseFloat(row.mar_value) / parseFloat(row.mar_target)) * 100;
								if(mar_kpi > 100){
									var val = (100 * parseFloat(row.mar_weight))/100;
									dat = val.toFixed(2);
								}
								else{	
									var val = (mar_kpi * parseFloat(row.mar_weight))/100;
									dat = val.toFixed(2);
								}					
							}
						}
						else if(row.type_kpi == 'terbalik'){
							var mar_kpi = (1-(parseFloat(row.mar_value) / parseFloat(row.mar_target)))*100 ;
							if(mar_kpi < 0){
								dat = 0;
							}
							else if(row.mar_value == null || row.mar_target == null){
								dat = null;
							}
							else if(mar_kpi >= 0){
								var val = (parseFloat(mar_kpi) * parseFloat(row.mar_weight))/100;
								dat = val.toFixed(2);
							}
						}
						return dat;
					}
				},
				{ data: 'apr', name: 'apr' },
				{ data: 'mei', name: 'mei' },
				{ data: 'jun', name: 'jun',render: function ( data, type, row ) {
						if(row.type_kpi == 'lurus'){
							if(row.jun_value == null || row.jun_target == null){
									dat = null;
							}
							else if(row.jun_target == 0){
								dat = 0;
							}
							else{
								var jun_kpi = (parseFloat(row.jun_value) / parseFloat(row.jun_target)) * 100;
								if(jun_kpi > 100){
									var val = (100 * parseFloat(row.jun_weight))/100;
									dat = val.toFixed(2);
								}
								else{	
									var val = (parseFloat(jun_kpi) * parseFloat(row.jun_weight))/100;
									dat = val.toFixed(2);
								}					
							}
						}
						else if(row.type_kpi == 'terbalik'){
							var jun_kpi = (1-(parseFloat(row.jun_value) / parseFloat(row.jun_target)))*100 ;
							if(jun_kpi < 0){
								dat = 0;
							}
							else if(row.jun_value == null || row.jun_target == null){
								dat = null;
							}
							else if(jun_kpi >= 0){
								var val = (parseFloat(jun_kpi) * parseFloat(row.jun_weight))/100;
								dat = val.toFixed(2);
							}
						}
						else if(row.type_kpi == 'hit_miss_lurus'){
							if(row.jun_value == null || row.jun_target == null){
								dat = null;
							}
							else if(row.jun_value >= row.jun_target){
								dat = row.jun_weight;
							}
							else{
								dat = 0;
							}
						}
						else if(row.type_kpi == 'hit_miss_terbalik'){
							if(row.jun_value == null || row.jun_target == null){
								dat = null;
							}
							else if(row.jun_value < row.jun_target){
								dat = row.jun_weight;
							}
							else{
								dat = 0;
							}
						}
						else if(row.type_kpi == 'terbalik_2'){
							if(row.jun_target == 0){
								if(row.jun_target < row.jun_value){
									dat = 0;
								}
								else if(row.jun_target == 0 && row.jun_value == 0){
									var val = (100 * parseFloat(row.jun_weight))/100;
									dat = val.toFixed(2);
								}
								else{
									dat = null;
								}
							
							}
							else if(row.jun_target >= row.jun_value){
								var val = (100 * parseFloat(row.jun_weight))/100;
								dat = val.toFixed(2);
							}						
							else{
								var jun_kpi = (parseFloat(row.jun_target) / parseFloat(row.jun_value))*100 ;
								var val = (parseFloat(jun_kpi) * parseFloat(row.jun_weight))/100;
								dat = val.toFixed(2);
							}
						}
					return dat;
					}
				},
				{ data: 'jul', name: 'jul' },
				{ data: 'ags', name: 'ags' },
				{ data: 'sep', name: 'sep' },
				{ data: 'okt', name: 'okt' },
				{ data: 'nov', name: 'nov' },
				{ data: 'des', name: 'des' },
			],
		});	

	 $('#modal_view_kpi').modal('show');
	 $("#detail_kpi_table_processing").css("background","white");
	 $("#detail_kpi_table_processing").css("color","black");
}); 
 $(document).on('click', '.edit', function () {
		global_id_kpi_group = $(this).attr('id');
		$.ajax({
			url: "<?= url('kpi/kpi/pa_quantitative_assesment/get_kpi_edit') ?>",
            method: "GET",
            data: {id_kpi_group: global_id_kpi_group},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (response) {
					global_monthly_detail = 0;
					 $.each(response.monthly, function (i, item) {
                        $('#new_monthly_detail').trigger('click');
                    });
					$('#id_kpi_group').val(response.id_kpi_group).trigger('change');
					$('#employee').val(response.employee).trigger('change');
					$('#average_prosentase').val(response.average_prosentase).trigger('change');
					$('#description').val(response.description).trigger('change');
					$('#position').val(response.position).trigger('change');
					$('#grade').val(response.grade).trigger('change');
					$('#dept').val(response.dept).trigger('change');

					setTimeout(function () {
						$('#table_monthly_body tr').each(function (index) {
							$(this).find('span.sn').html(index + 1);
							$(this).find('.id_kpi_header_input').val(response.monthly[index].id_kpi_header).trigger('change');
							$(this).find('.month_input').val(response.monthly[index].month).trigger('change');
							$(this).find('.notes_input').val(response.monthly[index].notes).trigger('change');
							$(this).find('.subtotal_kpi_input').val(response.monthly[index].subtotal_kpi).trigger('change');
							$(this).find('.additem').attr('id', response.monthly[index].id_kpi_header);
						});		
					}, 500);	
				
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (response) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
		$('#modal_form_kpi').modal('show');
   });	
   
	$(document).on('click', '#new_monthly_detail', function () {
            var content = jQuery('#sample_table_monthly tr'),
                    size = global_monthly_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec_header-'+size);
            element.find('.id_kpi_header_input').attr('id', 'monthly_' + size + '_id_kpi_header');
            element.find('.id_kpi_header_input').attr('name', 'monthly[' + size + '][id_kpi_header]');

            element.find('.month_input').attr('id', 'monthly_' + size + '_month');
            element.find('.notes_input').attr('id', 'monthly_' + size + '_notes');
            element.find('.subtotal_kpi_input').attr('id', 'monthly_' + size + '_subtotal_kpi');
						
            element.appendTo('#table_monthly_body');
			 $('#table_monthly_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
	$(document).on('click', '#new_item_detail', function () {
            var content = jQuery('#sample_table_item tr'),
                    size = global_id_kpi_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_kpi_detail_input').attr('id', 'itemkpi_' + size + '_id_kpi_detail');
            element.find('.id_kpi_detail_input').attr('name', 'itemkpi[' + size + '][id_kpi_detail]');
			
			element.find('.id_kpi_category_input').attr('id', 'itemkpi_' + size + '_id_kpi_category');
            element.find('.id_kpi_category_input').attr('name', 'itemkpi[' + size + '][id_kpi_category]');
            element.find('.id_kpi_category_input_error').attr('id', 'itemkpi_' + size + '_id_kpi_categoryError');
			element.find('.id_kpi_category_input').select2({
                placeholder: "Select Item",
                data: global_id_category
            }).trigger('change');
			
			element.find('.id_kpi_type_input').attr('id', 'itemkpi_' + size + '_id_kpi_type');
            element.find('.id_kpi_type_input').attr('name', 'itemkpi[' + size + '][id_kpi_type]');
            element.find('.id_kpi_type_input_error').attr('id', 'itemkpi_' + size + '_id_kpi_typeError');
			element.find('.id_kpi_type_input').select2({
                placeholder: "Select Type",
                data: global_id_type
            }).trigger('change');
						
			element.find('.weight_input').attr('id', 'itemkpi_' + size + '_weight');
            element.find('.weight_input').attr('name', 'itemkpi[' + size + '][weight]');
			
			element.find('.target_input').attr('id', 'itemkpi_' + size + '_target');
            element.find('.target_input').attr('name', 'itemkpi[' + size + '][target]');
			
			element.find('.kpi_value_input').attr('id', 'itemkpi_' + size + '_kpi_value');
            element.find('.kpi_value_input').attr('name', 'itemkpi[' + size + '][kpi_value]');
			
            element.appendTo('#table_item_body');
			 $('#table_item_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
	$(document).on('click', '.delete-record', function () {
		var id = jQuery(this).attr('data-id');
		jQuery('#rec-' + id).remove();
		$('#table_item_body tr').each(function (index) {				
			$(this).find('span.sn').html(index + 1);
		});
		return true;
	});
		
$(document).on('click', '#search_period', function () {		
		$.ajax({
            url: '<?= url('kpi/kpi/pa_quantitative_assesment/generate_kpi') ?>',
			dataType: "json",
            data: {period: $("#period").val()},
			beforeSend: function () {
				$('#loader').removeClass('hidden');
			},
            success: function (res) {
				localStorage.setItem("id_period_quantitative", $("#period").val());
                get_datatable();
            },
			complete: function(){
				$('#loader').addClass('hidden');
			},
			error: function (res) {
			 swal({
					icon: 'error',
					title: 'Oops...',
					dangerMode: true,
					text: 'Something went wrong! [Unknown Error]'
				});
			},
        });
});

$(document).on("click", ".advanced_kpi", function () {
    $('.cf').select2({width:'100%'});
    if($(".kpi_table").css('display') == 'none'){
        $(".kpi_table").show("slow");
    }
    else {
        $(".kpi_table").hide("slow");
    }   
});

	const get_datatable = async () => {
		$(".div_datatable").show();
		let myData = {
			period: $("#period").val() == '' ? null : $("#period").val(),
		};
		$('#kpi_table').DataTable({
			processing: true,
			responsive: true,
			destroy: true,
		//	'rowsGroup': [3],
			ajax: {
				url: "{{ route('quantitative.index') }}",
				"data": myData,
				"headers": {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
				error: function (jqXHR, textStatus, errorThrown) {
						$('#kpi_table').DataTable().ajax.reload();
					}
				},
			columns: [
				{
				defaultContent: '',
				orderable: false,
				},
				{   // Checkbox select column
				data: 'id_kpi_group',
				defaultContent: '',
				orderable: false
				},
				{ data: 'DT_RowIndex', name: 'DT_RowIndex'},				
				{ data: 'nik_employee', name: 'nik_employee'},
				{ data: 'name', name: 'name'},           
				{ data: 'grade', name: 'grade' },
				{ data: 'period', name: 'period'},
				{ data: 'average_prosentase', name: 'average_prosentase'},
				{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {	
						return data;
					} 
				},
			],
		});
		
	}	
	
refresh_data();

function refresh_data() {
	
	get_period();	
	get_category();
	get_type();
}

function get_period() {
	$.getJSON('<?= url('kpi/kpi/pa_quantitative_assesment/get_period') ?>', function (data) {
			$('#period').select2({
                data: data
            });
		}).then(function (data){
			if(localStorage.getItem("id_period_quantitative") != null ){
				$("#period").val(localStorage.getItem("id_period_quantitative")).trigger('change');
				if($("#period").val() != ""){
					$('#search_period').trigger('click');
				}
			}
		}).fail(function (data) { // Call failed
            get_period();
        });	
}

function get_category() {
	$.getJSON('<?= url('kpi/kpi/pa_quantitative_assesment/get_category') ?>', function (data) {
			global_id_category = data;
		}).fail(function (data) { // Call failed
            get_category();
        });	
}
function get_type() {
	$.getJSON('<?= url('kpi/kpi/pa_quantitative_assesment/get_type') ?>', function (data) {
			global_id_type = data;
		}).fail(function (data) { // Call failed
            get_type();
        });	
}

</script>
@endsection