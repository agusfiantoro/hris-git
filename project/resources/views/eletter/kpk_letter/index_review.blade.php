@extends('adminlte::page')
@section('title', 'P2K Review')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">P2K Review
        </h5>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="kpk_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
          <thead>
           <tr>
            <th data-priority="11"></th>
            <th data-priority="3"></th>
            <th data-priority="2">No</th>
            <th>Reference Number</th>
            <th data-priority="4" style="text-align:center;">NIK</th>
            <th data-priority="5" style="text-align:center;">Name</th>
            <th data-priority="7" style="text-align:center;">Group Name</th>
            <th data-priority="13" style="text-align:center;">Threshold</th>
            <!-- th data-priority="9" style="text-align:center;">AP3M KPI</th -->
            <th data-priority="10" style="text-align:center;">Month BA</th>
            <th data-priority="12" style="text-align:center;">Created By</th>
            <!-- th data-priority="6" style="text-align:center;">Evaluation Status</th -->
            <th data-priority="8" style="text-align:center;">Review Status</th>
            <th data-priority="9" style="text-align:center;">Review Date</th>
            <th data-priority="1" width="80" class="text text-center">Action</th>
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
			<form method="POST" id="kpkForm">
				{{ csrf_field() }}
				<div class="modal-header">
					<h5 id="modal-title" class="modal-title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			  <div class="modal-body" id="contentBody">
			  </div>
			  <div class="modal-footer">
				<button type="submit" class="btn btn-success btn-sm " id="save"><i class="fas fa-save"></i> Save</button>&nbsp;
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
			</form>
			<div style="display:none;">
				<table id="sample_table_review">
					<tr id="" style="font-size:14px;">
						<td align="center"><span class="sn" id="review_0_sn" style="vertical-align:middle;text-align:center;"></span></td>
						
						<td>
							<input name="review[0][id_performance_review]" id="review_0_id_performance_review" type="hidden" class="id_performance_review_input">
							<input type="text" name="review[0][period]" id="review_0_period" class="form-control form-control-sm period_input" style="width: 50px;">
							<span class="invalid-date period_input_error" style="font-size:10px;color:#dc3545;" role="alert" id="review_0_periodError">
								<strong></strong>
							</span>
						</td>
						<td>
							<input type="text" name="review[0][review_date]" id="review_0_review_date" class="form-control form-control-sm review_date_input" style="width: 100%;">
							<span class="invalid-date review_date_input_error" style="font-size:10px;color:#dc3545;" role="alert" id="review_0_review_dateError">
								<strong></strong>
							</span>
						</td>
						<td>
							<input type="text" name="review[0][id_target]" id="review_0_id_target" class="form-control form-control-sm id_target_input" style="font-weight:bold;text-align:center;width:120px;">
							<span class="invalid-feedback id_target_input_error" role="alert" id="review_0_id_targetError">
							  <strong></strong>
							</span>
						</td>
						<td>
							<input type="text" name="review[0][act]" id="review_0_act" class="form-control form-control-sm act_input" style="font-weight:bold;text-align:center;width:120px;">
							<span class="invalid-feedback act_input_error" role="alert" id="review_0_actError">
							  <strong></strong>
							</span>
						</td>
						<td align="center">
							<span id="review_0_idx" class="idx_input" style="font-weight:bold;"></span>
						</td>
						<td>
							<select name="review[0][decision]" id="review_0_decision" class="form-control form-control-sm select2 decision_input" style="width: 120px;">
							</select>
							<span class="invalid-feedback decision_input_error" role="alert" id="review_0_decisionError">
							  <strong></strong>
							</span>
						</td>
						<td>
							<select name="review[0][treatment]" id="review_0_treatment" class="form-control form-control-sm select2 treatment_input" style="width: 120px;">
							</select>
							<span class="invalid-feedback treatment_input_error" role="alert" id="review_0_treatmentError">
							  <strong></strong>
							</span>
						</td>
						<td>
							<center>
								<div id="review_0_pdf" class="pdf_input"></div>
								<button type="button" id="review_0_del_review" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;display:none;"><span class="far fa-trash-alt"></span></button>
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
</style>
@endsection

@section('scripts')
<script src="https://unpkg.com/gijgo@1.9.14/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ asset('vendor/bootstrap/js/id-id.js') }}"></script>
<script type="text/javascript">
let global_decision = [];
let global_id_rec_detail = 0;
let global_threshold = 0;
let global_treatment = [];
let global_period = "";

$(document).on('click', '#new_rec_detail', function (event,auto=false) {
		var content = jQuery('#sample_table_review tr'),
				size = global_id_rec_detail++,
				element = null,
				element = content.clone();
		element.attr('id','rec-'+size);
		element.find('.delete-record').attr('data-id', size);
		element.find('.sn').attr('id', 'review_' + size + '_sn');
		element.find('.id_performance_review_input').attr('id', 'review_' + size + '_id_performance_review');
		element.find('.id_performance_review_input').attr('name', 'review[' + size + '][id_performance_review]'); 
		
		element.find('.period_input').attr('id', 'review_' + size + '_period');
		element.find('.period_input').attr('name', 'review[' + size + '][period]');
		element.find('.period_input_error').attr('id', 'review_' + size + '_periodError');
		element.find('.period_input').datepicker({
			locale: 'de-de',
			uiLibrary: 'bootstrap4',
			format: 'mmmm yyyy',
		});	
		element.find('.period_input').attr('readonly',true).css('pointer-events','none').css('touch-action','none');
		element.find('.period_input').parent().children('span').children('button').attr('disabled',true);
		
		element.find('.review_date_input').attr('id', 'review_' + size + '_review_date');
		element.find('.review_date_input').attr('name', 'review[' + size + '][review_date]');
		element.find('.review_date_input_error').attr('id', 'review_' + size + '_review_dateError');
		element.find('.review_date_input').datepicker({
			locale: 'de-de',
			uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
		});	
		
		element.find('.id_target_input').attr('id', 'review_' + size + '_id_target');
		element.find('.id_target_input').attr('name', 'review[' + size + '][id_target]');
		element.find('.id_target_input_error').attr('id', 'review_' + size + '_id_targetError'); 
		element.find('.id_target_input').on('change click keyup input paste',(function (event) {
			$(this).val(function (index, value) {
				return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});
		}));
	//	element.find('.id_target_input').val(global_threshold).trigger('change');
	//	element.find('.target_input').html(global_threshold);
		
		element.find('.act_input').attr('id', 'review_' + size + '_act');
		element.find('.act_input').attr('name', 'review[' + size + '][act]');
		element.find('.act_input_error').attr('id', 'review_' + size + '_actError');
		element.find('.act_input').on('change click keyup input paste',(function (event) {
			$(this).val(function (index, value) {
				return value.replace(/(?!\.)\D/g, "").replace(/(?<=\..*)\./g, "").replace(/(?<=\.\d\d).*/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			});
		}));
		
		element.find('.decision_input').attr('id', 'review_' + size + '_decision');
		element.find('.decision_input').attr('name', 'review[' + size + '][decision]');
		element.find('.decision_input_error').attr('id', 'review_' + size + '_decisionError');
		element.find('.decision_input').prepend('<option selected></option>').select2({
			placeholder: "Select Decision ...",
			data: global_decision,
			allowClear: true,
		});
		
		element.find('.treatment_input').attr('id', 'review_' + size + '_treatment');
		element.find('.treatment_input').attr('name', 'review[' + size + '][treatment]');
		element.find('.treatment_input_error').attr('id', 'review_' + size + '_treatmentError');
		element.find('.treatment_input').prepend('<option selected></option>').select2({
			placeholder: "Select Treatment ...",
			data: global_treatment,
		});
				
		element.appendTo('#table_rec_body');
		 $('#table_rec_body tr').each(function (index) {
			$(this).find('span.sn').html(index + 1);
		});
		
		$('#table_rec_detail').find('.period_input').each(function (i, obj) {
			if($('#' + obj.id).val() != ""){
				global_period = $('#' + obj.id).attr('format_date');
			}
			
		});
		if(auto == false){
			element.find('.delete-record').show();
			element.find('.period_input').val(moment(global_period).add(1, 'months').format('MMMM YYYY')).trigger('change');
			element.find('.period_input').attr('format_date',moment(global_period).add(1, 'months').format('YYYY-MM-DD'));
		}
	});

	$(document).on('click', '.delete-record', function () {
		var id = jQuery(this).attr('data-id');
		jQuery('#rec-' + id).remove();
		$('#table_rec_body tr').each(function (index) {				
			$(this).find('span.sn').html(index + 1);
		});
		return true;
	});

$(document).on('change', '.id_target_input, .act_input', (element) => {
	var el_obj_1 = $(element.target).closest('tr').find('.id_target_input').val();
	el_obj_1 = el_obj_1.replace(",", "");
	var el_ach_1 = $(element.target).closest('tr').find('.act_input').val();
	el_ach_1 = el_ach_1.replace(",", "");
	var obj_1 = parseFloat(el_obj_1);
	var ach_1 = parseFloat(el_ach_1);
		var idx_1 = parseFloat(parseFloat((ach_1 / obj_1) * 100).toFixed(2));		
		if(obj_1 == 0){
			$(element.target).closest('tr').find('.idx_input').html('Error').css("color","red").trigger('change');
		}
		else{
			$(element.target).closest('tr').find('.idx_input').html(idx_1).css("color","black").trigger('change');
		}
});

$(document).on('change', '.treatment_input', (element,param) => {
	var treat_no = ['resign','demosi','phk','phkspdt'];
	$("#pass_date").val('').trigger('change');
	$('.treatment_input').each(function (index) {
		if(treat_no.includes($(this).val())){
			$("#end_date").html('End Date');
			var this_period = $(this).closest('tr').find('.period_input').val();
			closest_period(this_period);
		}
		else if($(this).val() == 'pass'){
			$("#end_date").html('Pass Date');
			var this_period = $(this).closest('tr').find('.period_input').val();
			closest_period(this_period);
		}
	});
});
	
$(document).on('change', '.decision_input', (element,param) => {
	var val_decision = $(element.target).closest('tr').find('.decision_input').val();
	if(val_decision == 'HIT'){
		$(element.target).closest('tr').find('.treatment_input').empty().select2({
			data: [{id: 'pass',text: 'Lulus'}],
			placeholder: "Select Treatment ...",
		});
		closest_period($(element.target).closest('tr').find('.period_input').val());
	}
	else{
		$(element.target).closest('tr').find('.treatment_input').empty().prepend('<option selected></option>').select2({
			data: global_treatment,
			placeholder: "Select Treatment ...",
		});		
	}
	var treat_no = ['resign','demosi','phk','phkspdt'];
	$("#pass_date").attr('readonly',true).css('pointer-events','none').css('touch-action','none');
	$("#pass_date").parent().children('span').children('button').attr('disabled',true);
	$("#pass_date").val('').trigger('change');
	$('.decision_input').each(function (index) {
		if($(this).val() == 'HIT'){
			$("#end_date").html('Pass Date');
			var this_period = $(this).closest('tr').find('.period_input').val();
			closest_period(this_period);
		}
	});
});
	
function closest_period(element_period) {
	var p_d = element_period;
		var split = p_d.split(' ');
		if(split[0] == 'Desember'){
			var mo = 'December';
			var val_month = '01 '+mo+' '+split[1];
		}
		else if(split[0] == 'Mei'){
			var mo = 'May';
			var val_month = '01 '+mo+' '+split[1];
		}
		else if(split[0] == 'Agustus'){
			var mo = 'August';
			var val_month = '01 '+mo+' '+split[1];
		}
		else if(split[0] == 'Oktober'){
			var mo = 'October';
			var val_month = '01 '+mo+' '+split[1];
		}
		else{
			var val_month = '01 '+split[0]+' '+split[1];
		}
		
		var date_fix =  moment(val_month).add(1,'months').format('YYYY-MM-DD');
		$("#pass_date").val(date_fix).trigger('change');
}

function on_close_modal() {
	$('#kpk_table').DataTable().ajax.reload(); 
}

 $(document).ready(function(){
	
	var treat_no = ['phk','phkspdt','resign','demosi'];
	global_decision = [
		{
			id: 'HIT',
			text: 'HIT'
		},
		{
			id: 'MISS',
			text: 'TIDAK HIT'
		},
	];
  
	global_treatment = [
		{
			id: 'bulan1',
			text: 'P2K Bulan Ke-1'
		},
		{
			id: 'sp1',
			text: 'SP 1'
		},
		{
			id: 'sp2',
			text: 'SP 2'
		},
		{
			id: 'sp3',
			text: 'SP 3'
		},
		{
			id: 'phk',
			text: 'PHK'
		},
		{
			id: 'phkspdt',
			text: 'PHK SPDT'
		},
		{
			id: 'resign',
			text: 'Resign'
		},
		{
			id: 'demosi',
			text: 'Demosi'
		},
	];
  
    $('#kpk_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,        
      ajax: {
        url: "{{ route('kpk.index_review') }}",
        error: function (jqXHR, textStatus, errorThrown) {
        //  $('#kpk_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {   
        data: 'id_performance_evaluation',
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex', className: 'text-center'},
      { data: 'reference_number', name: 'reference_number' },
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'name', name: 'name' },
      { data: 'group_name', name: 'group_name' },
      { data: 'th', name: 'th', className: 'text-center' },
    //  { data: 'kpi',name: 'kpi', className: 'text-center'},
      { data: 'month_ba',name: 'month_ba', className: 'text-center'},
      { data: 'created_name',name: 'created_name'},
   /*   { data: 'evaluation_status', name: 'evaluation_status', className: 'text-center', render: function ( data, type, row ) {	
			if(row.evaluation_status == 'Pass'){
					return '<span class="badge badge-success" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
			else if(row.evaluation_status == 'Continue'){
					return '<span class="badge badge-warning" style="padding:5px;font-size:12px;color:white">'+data+'</span>';
			}
			else{
				return '<span class="badge badge-danger" style="padding:5px;font-size:12px;">'+data+'</span>';
			}
		}
	   },
	*/
		
	   { data: 'month_eval', name: 'month_eval', className: 'text-center', render: function ( data, type, row ) {	
			if(row.month_eval == 'Not Reviewed'){
					return '<span class="badge badge-danger" style="padding:5px;font-size:13px;">'+data+'</span>';
			}
			else if(row.month_eval != null && row.treatment == 'pass'){
				return '<span class="badge badge-success" style="padding:5px;font-size:13px;">'+data+'</span>';
			}
			else if(row.month_eval != null && (treat_no.includes(row.treatment))){
				return '<span class="badge badge-danger" style="padding:5px;font-size:13px;">'+data+'</span>';
			}
			else{
				return '<span class="badge badge-warning" style="padding:5px;font-size:13px;color:white;">'+data+'</span>';
			}
		}
	   },
	   { data: 'review_date', name: 'review_date', className: 'space text-center'},
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
  
$(document).on('click', '.edit', function() {
	var id_review = $(this).attr('id');
	var date_review = $(this).attr('date');
	var id_position_detail = $(this).attr('id_position_detail');
	var id_employee = $(this).attr('id_employee');
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#kpkForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> P2K Review");	
	$.ajax({
		url: "{{ route('kpk.modal_review') }}",
		data:{
			global_kpk:id_review,
			global_date:date_review,
			global_position:id_position_detail,
			global_employee:id_employee
			},
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},	
		success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
});

	$('#kpkForm').submit(function (e) {
      e.preventDefault();
      let formData = $(this).serializeArray();
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
        url: "{{ route('kpk.update_review') }}",
        data: formData,
		beforeSend: function () {
			$('#loader').removeClass('hidden');
		},
        success: function (response) {
          if (response.status == 'true') {
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
	
function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

function get_pdf(idReview,dateReview,periodDate) {
	moment.locale('id');
	var rep = dateReview.split('-');
	var periodMonth = moment(periodDate).format('MMMM-YYYY');
	var d = moment(dateReview).format('dddd')+', tanggal '+spellNumber(rep[2]).trim()+', bulan '+ moment(dateReview).format('MMMM')+' tahun '+spellNumber(rep[0]).trim();
	d = d.replaceAll(" ","-");
	var city_date = moment(dateReview).format('D-MMMM-YYYY');
	city_date = city_date.replaceAll(" ","-");
	let res = {
        id_review: idReview,
        period_month: periodMonth,
        city_date: city_date,
        text_date: d,
    };
    let param = objectToQueryString(res);
	let url = "{{ url('e-letter/performance_plan/performance_review/download') }}";
    window.open(url+'?'+param, '_blank');
}	
</script>  
@endsection