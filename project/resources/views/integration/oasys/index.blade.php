@extends('adminlte::page')
@section('title', 'OASYS Access Request')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">OASYS Access Request
        </h5>
      </div>
      <div class="card-body">
		<div class="form-group row">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-2">
						<label class="col-form-label">Search By</label>
					</div>
					<div class="col-md-2">
						<div class="">
							<select id="status" class="form-control form-control-sm select2" style="width: 100%;">
								<option value="A" selected>Active</option>
								<option value="I">Inactive</option>
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="">
							<select id="employee_search" class="form-control form-control-sm select2" multiple="multiple" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="">
							<select id="dept_search" class="form-control form-control-sm select2" style="width: 100%;"></select>
						</div>
					</div>
					<div class="col-md-2">
						<button onclick="return false;" id="search" class="btn btn-sm btn-success" ><i class="fas fa-search"></i> Search</button>
					</div>
				</div>
			</div>	
		</div>
	  
		<div class="div_datatable" style="display:none;"> 
			<div class="form-group row">
				<div class="col-md-4">
					<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				</div>
				<div class="col-md-4"></div>
				<div class="col-md-4">
					{{-- <button type="button" id="download" class="pull-right btn btn-sm btn-success"><i class="fas fa-download" ></i> Export Sales Code</button> --}}
				</div>
			</div>
			<table id="bgen_table" style="width: 100%;" class="table table-striped table-bordered table-hover datatable">
			  <thead>
			   <tr>
				<th></th>
				<th></th>
				<th data-priority="2">No</th>
				<th data-priority="3">NIK</th>
				<th data-priority="4">Name</th>
				<th data-priority="9">Join Date</th>
				<th data-priority="5">Position</th>
				<th data-priority="6">Grade</th>
				<th data-priority="7">Department</th>
				<th data-priority="8">Principal</th>
				<th>Region</th>
				<th>Branch</th>
				<th data-priority="10">Direct Supervisor</th>
				<th>Immediate Manager</th>
				<th data-priority="11">Status</th>
				<th data-priority="1" width="300" class="text text-center">Action</th>
			  </tr>
			</thead>
		  </table>
		</div>
    </div>
  </div>
</div>
</div>
<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="bgenForm">
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
			<!--button type="submit" class="edit_master btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp; -->
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>
		 <div style="display:none;">
			<table id="sample_table_rec">
				<tr id="" style="font-size:14px;">
					<td align="center"><span class="sn" style="vertical-align:middle;text-align:center;"></span></td>
					<td>
						<input name="bgen[0][id_integration_sales_code]" id="bgen_0_id_integration_sales_code" type="hidden" class="id_integration_sales_code_input">
						<input name="bgen[0][cat_code]" id="bgen_0_cat_code" type="hidden" class="cat_code_input">
						<select name="bgen[0][bgen_branch]" id="bgen_0_bgen_branch" class="form-control form-control-sm select2 bgen_branch_input" style="width: 100%;"></select>
						<span class="invalid-feedback bgen_branch_input_error" role="alert" id="bgen_0_bgen_branchError">
							<strong></strong>
						</span>	
					</td>
					<td>
						<select name="bgen[0][bgen_principal]" id="bgen_0_bgen_principal" class="form-control form-control-sm select2 bgen_principal_input" style="width: 100%;"></select>
						<span class="invalid-feedback bgen_principal_input_error" role="alert" id="bgen_0_bgen_principalError">
							<strong></strong>
						</span>	
					</td>
					<td>
						<select name="bgen[0][bgen_status]" id="bgen_0_bgen_status" class="form-control form-control-sm select2 bgen_status_input" style="width: 100%;"></select>
					</td>
					<td align="center" style="padding-top:15px;">
						<div id="bgen_0_bgen_sync" class="bgen_sync_input" style="padding:8px;"></div>
					</td>
					<td>
						<div id="bgen_0_sync_message" class="sync_message_input" style="font-size:8pt;"></div>
					</td>
					<td>
						<div id="bgen_0_note_revised" class="note_revised_input"></div>
					</td>
					<td>
						<div id="bgen_0_note_rejected" class="note_rejected_input"></div>
					</td>
					<td>
						<div id="bgen_0_group_id_approval" class="group_id_approval" style="display:none;">
							<select id="bgen_0_id_approval" name="bgen[0][id_approval]" class="form-control form-control-sm select2 id_approval_input" style="width: 100%;" readonly></select>
							<span class="invalid-feedback id_approval_error" role="alert" id="bgen_0_id_approvalError">
								<strong></strong>
							</span>
						</div>
					</td>
					<td>
						<input name="bgen[0][app_code]" id="bgen_0_app_code" type="hidden" class="app_code_input">
						<div id="bgen_0_group_app_status" class="group_app_status" style="display:none;">
							<select id="bgen_0_app_status" name="bgen[0][app_status]" class="form-control form-control-sm select2 app_status_input" style="width: 100%;" readonly></select>
						</div>
						<div class="is-loading" align="center">
								<span id="load_pos_detail_0" class="spinner-border spinner-border-sm load_pos_detail_input" style="display:none;"></span>
						</div>
					</td>
					
				<td style="white-space:nowrap;">
				<center>
					<button type="button" id="bgen_0_save_rec" class="add-record btn btn-xs btn-success save" data-id="0" onclick="detail_save(0)" title="Save & Submit" style="margin-right:5px;"><span class="fas fa-save"></span></button>
					<button type="button" id="bgen_0_del_rec" class="delete-record btn btn-xs btn-danger" data-id="0" title="Delete"  style="margin-right:5px;"><span class="far fa-trash-alt"></span></button>
					<button style="display:none;" type="button" id="bgen_0_cancel_rec" class="cancel-record btn btn-xs btn-danger" data-id="0" onclick="detail_cancel(0)" title="Cancel"><span class="fa fa-close"></span></button>
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

  select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow, select[readonly].select2-hidden-accessible + .select2-container .select2-selection_clear {
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
  td.text-center{
	text-align:center;
  }
  
</style>
@endsection

@section('scripts')
<script type="text/javascript">
let global_category = [];
let global_bgen_branch = [];
let global_bgen_principal = [];
let global_direct = [];
//let global_bgen_position = [];
//let global_bgen_location = [];
let global_status = [];
let global_id_rec_detail = 0;
let global_employee = 0;
let global_id_route = 0;
let global_desc_route = "";
let global_id_location = 0;
let global_desc_location = "";
let global_id_integration_sales_code = "";

function on_close_modal() {
//	$('#bgen_table').DataTable().ajax.reload(); 
	$("#myModal").modal('hide'); 
}

function loadedit(id_employee,nik_employee,type,join_date){
	var now = moment(new Date()).format('YYYY-MM-DD');	
	if(join_date > now){
		swal({
			icon: 'error',
			title: 'Oops...',
			dangerMode: true,
			text: 'Karyawan masih belum Aktif di HRIS (Tanggal Join '+moment(join_date).format('DD MMM YYYY')+')'
		});
	}
	else{
		global_employee = id_employee;
		$("#contentBody").html('');
		$(".invalid-feedback").children("strong").text("");
		$(".feedback").children("strong").text("");
		$(".table-invalid-feedback").children("strong").text("");
		$("#bgenForm input").removeClass("is-invalid");
		$("#modal-title").html("<span class='fas fa-edit'></span> Edit OASYS Approval");
		$.ajax({
				url: "{{ route('oasys.modal_detail') }}",
				data:{
					nik_employee:nik_employee,
					type:type,
				},
				success: function(result){
				$("#contentBody").html(result);
				$("#myModal").modal('show'); 
			}
		});
	}
}

$(document).on('click', '#new_rec_detail', function () {
            var content = jQuery('#sample_table_rec tr'),
                    size = global_id_rec_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
			element.find('.add-record').attr('onclick', 'detail_save(' + size + ')');
			element.find('.cancel-record').attr('onclick', 'detail_cancel(' + size + ')');
			element.find('.add-record').attr('data-id', size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_integration_sales_code_input').attr('id', 'bgen_' + size + '_id_integration_sales_code');
            element.find('.id_integration_sales_code_input').attr('name', 'bgen[' + size + '][id_integration_sales_code]');

			element.find('.load_pos_detail_input').attr('id', 'load_pos_detail_' +size);
			
			
			element.find('.id_approval_input').empty();
			element.find('.id_approval_request_input').empty();
			element.find('.app_status_input').empty();				
			element.find('.group_id_approval').hide();
			//	element.find('.group_id_approval_request').hide();
			//	element.find('.group_id_direct_spv').hide();
			element.find('.group_app_status').hide();
			element.find('.cat_code_input').val(null);
			if(global_category.length > 0){
				
				element.find('.group_id_approval').show();
			//	element.find('.group_id_approval_request').show();
			//	element.find('.group_id_direct_spv').show();
				element.find('.group_app_status').show();
				get_approval_by(global_employee);
				
				element.find('.id_direct_spv_input').select2({
					data: global_direct,
				});
			//	get_direct_spv(global_employee);
				get_status();
				
			}
			
			element.find('.bgen_branch_input').attr('id', 'bgen_' + size + '_bgen_branch');
            element.find('.bgen_branch_input').attr('name', 'bgen[' + size + '][bgen_branch]');
            element.find('.bgen_branch_input_error').attr('id', 'bgen_' + size + '_bgen_branchError');
            element.find('.bgen_branch_input').prepend('<option selected></option>').select2({
                placeholder: "Select Branch ...",
                data: global_bgen_branch,
            });
			
			element.find('.bgen_principal_input').attr('id', 'bgen_' + size + '_bgen_principal');
            element.find('.bgen_principal_input').attr('name', 'bgen[' + size + '][bgen_principal]');
            element.find('.bgen_principal_input_error').attr('id', 'bgen_' + size + '_bgen_principalError');
            element.find('.bgen_principal_input').prepend('<option selected></option>').select2({
                placeholder: "Select Principal ...",
                data: global_bgen_principal,
            });
			 			 
			element.find('.bgen_desc_position_input').attr('id', 'bgen_' + size + '_bgen_desc_position');
			element.find('.bgen_position_input').attr('id', 'bgen_' + size + '_bgen_position');
			element.find('.bgen_position_input').attr('name', 'bgen[' + size + '][bgen_position]');
			
			element.find('.bgen_desc_position_input').html(global_desc_route);
			element.find('.bgen_position_input').val(global_id_route);
		/*	element.find('.bgen_position_input_error').attr('id', 'bgen_' + size + '_bgen_positionError');
            element.find('.bgen_position_input').prepend('<option selected></option>').select2({
                placeholder: "Select Position ...",
                data: global_bgen_position,
            });
		*/	
			element.find('.bgen_desc_location_input').attr('id', 'bgen_' + size + '_bgen_desc_location');
			element.find('.bgen_location_input').attr('id', 'bgen_' + size + '_bgen_location');
			element.find('.bgen_location_input').attr('name', 'bgen[' + size + '][bgen_location]');
			
			element.find('.bgen_desc_location_input').html(global_desc_location);
			element.find('.bgen_location_input').val(global_id_location);
		/*	element.find('.bgen_location_input_error').attr('id', 'bgen_' + size + '_bgen_locationError');
            element.find('.bgen_location_input').prepend('<option selected></option>').select2({
                placeholder: "Select Location ...",
                data: global_bgen_location,
            });
		*/	
			element.find('.bgen_sync_input').attr('id', 'bgen_' + size + '_bgen_sync');
			element.find('.sales_code_input').attr('id', 'bgen_' + size + '_sales_code');
			element.find('.direct_spv_input').attr('id', 'bgen_' + size + '_direct_spv');
			element.find('.note_revised_input').attr('id', 'bgen_' + size + '_note_revised');
			element.find('.note_rejected_input').attr('id', 'bgen_' + size + '_note_rejected');
			
			element.find('.group_id_approval').attr('id', 'bgen_' + size + '_group_id_approval');
			
			element.find('.id_approval_input').attr('id', 'bgen_' + size + '_id_approval');
            element.find('.id_approval_input').attr('name', 'bgen[' + size + '][id_approval]');
            element.find('.id_approval_input_error').attr('id', 'bgen_' + size + '_id_approvalError');
			
			element.find('.group_id_approval_request').attr('id', 'bgen_' + size + '_group_id_approval_request');
		//	element.find('.group_id_direct_spv').attr('id', 'bgen_' + size + '_group_id_direct_spv');
			
			element.find('.id_approval_request_input').attr('id', 'bgen_' + size + '_id_approval_request');
            element.find('.id_approval_request_input').attr('name', 'bgen[' + size + '][id_approval_request]');
            element.find('.id_approval_request_input_error').attr('id', 'bgen_' + size + '_id_approval_requestError');
			
			element.find('.id_direct_spv_input').attr('id', 'bgen_' + size + '_id_direct_spv');
            element.find('.id_direct_spv_input').attr('name', 'bgen[' + size + '][id_direct_spv]');
            element.find('.id_direct_spv_input_error').attr('id', 'bgen_' + size + '_id_direct_spvError');
			
			element.find('.group_app_status').attr('id', 'bgen_' + size + '_group_app_status');
			
			element.find('.app_status_input').attr('id', 'bgen_' + size + '_app_status');
            element.find('.app_status_input').attr('name', 'bgen[' + size + '][app_status]');
			
			element.find('.app_code_input').attr('id', 'bgen_' + size + '_app_code');
            element.find('.app_code_input').attr('name', 'bgen[' + size + '][app_code]');
			
			element.find('.bgen_status_input').attr('id', 'bgen_' + size + '_bgen_status');
            element.find('.bgen_status_input').attr('name', 'bgen[' + size + '][bgen_status]');
			element.find('.bgen_status_input').select2({
                data: global_status,
            });
			
			element.find('.add-record').attr('id', 'bgen_' + size + '_save_rec');
			element.find('.delete-record').attr('id', 'bgen_' + size + '_del_rec');
			element.find('.cancel-record').attr('id', 'bgen_' + size + '_cancel_rec');
								
            element.appendTo('#table_rec_body');
			 $('#table_rec_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });
		
		$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
            $('#table_rec_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });


$(document).on('click', '#search', function () {
    get_datatable_employee()
});

$('#advanced').click(function(){
	cf_replace_htmlescape();
	$('.cf').select2({width:'100%'});
	if($("#cf").css('display') == 'none'){
		$("#cf").show("slow");
	}
	else {
		$("#cf").hide("slow");
	}		
});

$('#status').select2();

$(document).on('change', '#status', function() {
	getDeptAndEmployee()
});
	
const get_datatable_employee = async () => {
    $(".div_datatable").show();
	
    let myData = {
        id_url: global_url_server,
        nik: $("#employee_search").val() == '' ? null : $("#employee_search").val(),
		id_dept: $('#dept_search').val(),
		status: $('#status').val(),
    };
	
	$('#bgen_table').DataTable({
		processing: true,
		pageLength: 10,
		responsive: true,     
		destroy:true,
	ajax: {
        url: "{{ route('bgen.index') }}",
        data : myData,
        error: function (jqXHR, textStatus, errorThrown) {
     //     $('#bgen_table').DataTable().ajax.reload();
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
      { data: 'nik_employee', name: 'nik_employee' },
      { data: 'name', name: 'name' },	  
      { data: 'join_date', name: 'join_date', render: function (data, type, row) {					
			return moment(data).format('DD MMM YYYY');
		}
	  },
      { data: 'position_routing', name: 'position_routing' },
      { data: 'job_grade', name: 'job_grade' },
      { data: 'department', name: 'department' },
      { data: 'principal', name: 'principal' },
      { data: 'regional', name: 'regional' },
      { data: 'branch', name: 'branch' },
      { data: 'direct_spv', name: 'direct_spv' },
      { data: 'imm_manager', name: 'imm_manager' },
      { data: 'status', name: 'status' },
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
	
}	

$(document).on('select2:open', 'select', function() {
	cf_replace_htmlescape();
})

function getDeptAndEmployee() {
	$.ajax({
		url: "{{route('bgen.get_dept')}}",
		success: (res) => {
			$('#dept_search').empty().prepend('<option></option>').select2({
				placeholder: "Select Department",
				data: res,
				allowClear: true,
			});

			getEmployee().then(function(value) {
				$('#employee_search').html('');
				$('#employee_search').select2({
					placeholder: "Select Employee",
					data: value,
					allowClear: true,
				});
			});
		}
	})
	
}

$('#status').on('change', function() {
	getDeptAndEmployee();
})

$(document).on('change', '#dept_search', function() {
	getEmployee().then(function(value) {
		$('#employee_search').html('');
		$('#employee_search').select2({
			placeholder: "Select Employee",
			data: value,
			allowClear: true,
		});
	});
})

$('#status').select2();

  $(function () {
	  
	getDeptAndEmployee();
	  
	global_status = [
			{
				id: 'A',
				text: 'Active'
			},
			{
				id: 'I',
				text: 'Inactive'
			},
		];
	
  }); 
  
async function getEmployee() {
	$('#loader').removeClass('hidden');
//    let status = ['A'];
  	let currentPath = "<?= \Request::path() ?>";
    let result;
    try {
        result = await $.getJSON('<?= url('integration/bgen/sales_code_bgen/get_employee') ?>'+'?id_url='+global_url_server+'&id_dept='+$('#dept_search').val()+'&status='+$('#status').val(), 
		function (res) {
			$('#loader').addClass('hidden');	
        });
        return result;
    } catch (error) {
     //   getEmployeeByAccessGroup();
    }
}  
  
const get_category = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_category') ?>',
            dataType: 'json',
            success: function (res) {
				global_category = res;
            },
        });
        return result;
    } catch (error) {
        get_category();
    }	
} 

const get_branch = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_branch') ?>',
            dataType: 'json',
            success: function (res) {
				global_bgen_branch = res;
            },
        });
        return result;
    } catch (error) {
        get_branch();
    }	
}  

const get_principal = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_principal') ?>',
            dataType: 'json',
            success: function (res) {
				global_bgen_principal = res;
            },
        });
        return result;
    } catch (error) {
        get_principal();
    }	
}

/*
const get_position = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_position') ?>',
            dataType: 'json',
            success: function (res) {
				global_bgen_position = res;
            },
        });
        return result;
    } catch (error) {
        get_position();
    }	
}

const get_location = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_location') ?>',
            dataType: 'json',
            success: function (res) {
				global_bgen_location = res;
            },
        });
        return result;
    } catch (error) {
        get_location();
    }	
}
*/
/*
 function get_approval_by(id_employee) {
	$.ajax({
		method: "GET",
		url : "{{url('integration/bgen/sales_code_bgen/get_approval_by')}}",
		data: {id_employee: id_employee},
		success: function (response) {
			if (response.count_by > 0) {
				$('#table_rec_detail').find('.id_approval_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).select2({
						data: response.approval_hirarki
					});	
				});
				
				$('#table_rec_detail').find('.id_approval_request_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).select2({
						data: response.approval_by
					});	
				});
			}	
		},
		error: function(response) {
		  if (response.status === 500) {
			get_approval_by(id_employee);
		  }else{
			location.reload();
		  }
		}
	}); 
	
 } 
 */

const get_approval_by = async (id_employee) => {
	let result;
    try {
        result = await $.ajax({
            url: "{{ route('oasys.get_approval_by') }}",
			data: {id_employee: id_employee},
            dataType: 'json',
			beforeSend: function () {
			//	$('#loader').removeClass('hidden');
			},
            success: function (response) {
				if (response.count_by > 0) {
					$('#table_rec_detail').find('.id_approval_input').each(function (i, obj) {
						$('#' + obj.id).empty();
						$('#' + obj.id).select2({
							data: response.approval_hirarki
						});	
					});
					
					$('#table_rec_detail').find('.id_approval_request_input').each(function (i, obj) {
						$('#' + obj.id).empty();
						$('#' + obj.id).select2({
							data: response.approval_by
						});	
					});
				}		
            },
			error: function(response) {
			  if (response.status === 500) {
				get_approval_by(id_employee);
			  }else{
				location.reload();
			  }
			},
			complete: function(){
			//	$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
      //  get_direct_spv(id_employee);
    }	
}

const get_direct_spv = async (id_employee) => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_direct_spv') ?>',
			data: {id_employee: id_employee},
            dataType: 'json',
			beforeSend: function () {
			//	$('#loader').removeClass('hidden');
			},
            success: function (response) {
				if (response.count_by > 0) {
					global_direct = response.app_direct_spv
				}	
            },
			error: function(response) {
			  if (response.status === 500) {
				get_direct_spv(id_employee);
			  }else{
				location.reload();
			  }
			},
			complete: function(){
			//	$('#loader').addClass('hidden');
			},
        });
        return result;
    } catch (error) {
      //  get_direct_spv(id_employee);
    }	
}
 
 const get_status = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('integration/bgen/sales_code_bgen/get_status') ?>',
            dataType: 'json',
            success: function (res) {
				$('#table_rec_detail').find('.app_status_input').each(function (i, obj) {
					$('#' + obj.id).empty();
					$('#' + obj.id).select2({
						data: res
					});	
					if($('#' + obj.id).attr('default-value')) {
						$('#' + obj.id).val($('#' + obj.id).attr('default-value')).trigger('change');
					}
				});	
            },
        });
        return result;
    } catch (error) {
        get_status();
    }	
}

/*
function get_status() {
	$.ajax({
		method: "GET",
		url : "{{url('integration/bgen/sales_code_bgen/get_status')}}",
		success: function (response) {
			$('#table_rec_detail').find('.app_status_input').each(function (i, obj) {
				$('#' + obj.id).empty();
				$('#' + obj.id).select2({
					data: response
				});	
			});				
		},
	}); 
 }  
 */
 
function detail_save(counter) {	
	swal({
		title: 'Are you sure?',
        text: 'This record will be saved/updated!',
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
			$(".invalid-feedback").children("strong").text("");
			$("#bgenForm input").removeClass("is-invalid");
			$("#bgenForm select").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text(""); 
			$(".error-tab").html("");
			global_id_integration_sales_code = $("#bgen_" + counter + "_id_integration_sales_code").val();
			var formData = $('#bgenForm').serializeArray();
			// formData.append('id_approval_request', )
			$.ajax({
				type: 'POST',
				headers: {
					Accept: "application/json",
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				url: global_id_integration_sales_code == '' ? "{{ route('oasys.save') . '?counter=' }}" + counter + "<?= '&id_employee='?>" + global_employee : "{{ route('oasys.update') . '?id_integration_sales_code=' }}" + global_id_integration_sales_code + "<?= '&counter='?>" + counter,
				data: formData,
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
				success: function(response) {
				   if (response.status == 'true') {
					   $("#bgen_" + counter + "_group_app_status").removeClass("badge badge-danger").removeClass("badge badge-info");
						swal({
							icon: 'success',
							title: 'Success',
							text: response.message
						});
						if(response.res.is_synchronize_flag == true){
							$("#bgen_" + counter + "_bgen_sync").addClass("badge badge-success").html('<span class="fa fa-check fa-lg"></span>');
						}
						else{
							$("#bgen_" + counter + "_bgen_sync").addClass("badge badge-danger").html('<span class="fa fa-ban fa-lg"></span>');
						}
						if(response.res.id_approval != null){
							$("#bgen_" + counter + "_group_app_status").addClass("badge badge-secondary").css({"font-size":"12px","padding":"6px"}).html(response.res.app_status);
						}
						if(response.res.code == 'Request_Approval'){
							global_id_integration_sales_code = $("#bgen_" + counter + "_id_integration_sales_code").val(response.res.id_integration_sales_code);
							$("#bgen_" + counter + "_save_rec").hide();
							$("#bgen_" + counter + "_cancel_rec").show();
							$("#bgen_" + counter + "_id_transition_category").attr('readonly',true);
							$("#bgen_" + counter + "_bgen_branch").attr('readonly',true);
							$("#bgen_" + counter + "_bgen_principal").attr('readonly',true);
							$("#bgen_" + counter + "_bgen_status").attr('readonly',true);
							$("#bgen_" + counter + "_id_approval_request").attr('readonly',true);
							$("#bgen_" + counter + "_id_direct_spv").attr('readonly',true);
						}
						$("#bgen_" + counter + "_del_rec").hide();
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
							text: 'Something went wrong! '+response.responseJSON.message
						});
					}
				}

			});
		}
    });
 }
     
function detail_cancel(counter) {
	
	swal({
        title: 'Are you sure?',
        text: 'This record and it`s details will be canceled!',
        icon: 'warning',
       buttons: true,
		dangerMode: true,
		  confirmButtonColor: '#3085d6',
		  cancelButtonColor: '#d33',
		  confirmButtonText: 'Yes, Cancel it!'
    }).then(function(value) {
        if (value) {			
			global_id_integration_sales_code = $("#bgen_" + counter + "_id_integration_sales_code").val();
			$.ajax({
				url:"sales_code_bgen/cancel/"+global_id_integration_sales_code,
				beforeSend: function () {
					$("#load_pos_detail_"+counter).show();
					$("#bgen_"+counter+"_group_app_status").hide();
				},
				success:function(response)
				{
					setTimeout(function(){				 
						if (response.status == 'true') {							
							swal({
								icon: 'success',
								title: 'Cancel Successfully',
								text: response.message
							});
							$("#bgen_" + counter + "_group_app_status").addClass("badge badge-danger").css({"font-size":"12px","padding":"6px"}).html(response.res.app_status);
							
							$("#bgen_" + counter + "_cancel_rec").hide();
							$("#bgen_" + counter + "_save_rec").show();
							$("#bgen_" + counter + "_id_transition_category").attr('readonly',false);
							$("#bgen_" + counter + "_bgen_branch").attr('readonly',false);
							$("#bgen_" + counter + "_bgen_principal").attr('readonly',false);
							$("#bgen_" + counter + "_bgen_status").attr('readonly',false);
							$("#bgen_" + counter + "_id_approval_request").attr('readonly',false);
							$("#bgen_" + counter + "_id_direct_spv").attr('readonly',false);
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
				complete: function(){
					setTimeout(function () {						
						$("#load_pos_detail_"+counter).hide();
						$("#bgen_"+counter+"_group_app_status").show();
						get_approval_by(global_employee);
					}, 500);
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
}

// $(document).on('click', '#new_sync', () => sync());

function sync(nik_emp) {
	$.ajax({
		url: "{{route('oasys.sync')}}",
		type: 'POST',
		data: {
			nik_employee: nik_emp,
			_token: '{{ csrf_token() }}'
		},
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
				text: res.responseJSON.message
			})
		},
		complete: () => {
			$('#table_rec_body').empty();
			get_edit(nik_emp);
			$('#loader').addClass('hidden');
			
		},
	})
}

function cf_replace_htmlescape() {
	$('#cf').find('option').each((i, element) => {
		if($(element).attr('value')) {
			$(element).attr('value', 
			$(element).attr('value').replaceAll('&amp;', '&').replaceAll("&lt;", "<").replaceAll("&gt;", ">").replaceAll("&quot;", '"').replaceAll("&#039;", "'"));
		}
	})
}

function objectToQueryString(obj) {
    var str = [];
    for (var p in obj)
    if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
    return str.join("&");
}

</script>  
@endsection