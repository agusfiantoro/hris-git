@extends('adminlte::page')
@section('title', 'Dicipline')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Dicipline</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Dicipline</button>
                </div>
            </div>
       
			 <div class="card-body">
			 <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="dicipline_table" style="width:1200px;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th class="dtfc-fixed-left"></th>
						<th></th>
						<th>No</th>
						<th style="white-space:nowrap;">Reference Number</th>
                        <th>Letter Number</th>
						<th data-priority="2">NIK</th>
						<th data-priority="1">Name</th>
                        <th>Position</th>
                        <th>Department</th>
                        <th>Principal</th>
                        <th>Region</th>
                        <th>Branch</th>
						<th>Dicipline Type</th>
						<th style="white-space:nowrap;">Effective Date</th>
						<th style="white-space:nowrap;">Expired/Pass Date</th>
						<th>Attachment</th>
						<th>Status</th>
						<th>Created By</th>
						<th style="text-align:center;">KPK Status</th>
						<th data-priority="3" width=120 style="text-align:center;">Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_dicipline"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" id="diciplineForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Dicipline</h5>
                    <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                           
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
								<div class="col-sm-8">
                                    <select name="id_employee" id="id_employee" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Dicipline Type</label>
								<div class="col-sm-8">
                                    <select name="dicipline_type" id="dicipline_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="dicipline_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Using Letter Number</label>
                                <div class="col-sm-8 ">
                                    <input type="checkbox" name="using_letter_number" id="using_letter_number" class="form-control form-control-sm float-left" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="using_letter_numberError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>  
                            <div class="row award_letter_number" style="display:none;">
                                <label class="col-sm-4 col-form-label">Letter Number</label>
                                 <div class="col-sm-8">
                                    <div class="input-group">
                                        <input name="award_letter_number" id="award_letter_number" type="text" class="form-control form-control-sm" onpaste="return false;" ondrop="return false;" autocomplete="off" onkeydown="return false;" style="border-radius: 5px 0 0 5px;">
                                        <div class="input-group-append">
                                            <span class="input-group-text far fa-list-alt form-control-sm"></span>
                                        </div>
                                    </div>
                                    <span class="invalid-group"  style="font-size:10px;color:#dc3545;" role="alert" id="award_letter_numberError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Remark</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remark" id="remark" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="remarkError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                           <div class="row">
                                <label class="col-sm-4 col-form-label">Attachment</label>                          
							   <div class="col-sm-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="attachment">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB</i></label>
								</div>								
							   </div>
							</div>
							<div class="row kpk_id" style="display:none;">
                                <label class="col-sm-4 col-form-label">KPK Status</label>
								<div class="col-sm-8">
                                    <select name="kpk_status" id="kpk_status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="kpk_statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
						
						<div class="col-md-6" style="margin-bottom:20px;">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="companyError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Reference Date</label>
                                <div class="col-sm-8">
                                    <input name="reference_date" id="reference_date" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_dateError">
                                        <strong></strong>
                                    </span>
                                </div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label kpk_date">Expired Date</label>
                                <div class="col-sm-8">
                                   <input name="expired_date" id="expired_date" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="expired_dateError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							 <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
									  <select id="select2status" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                        </div>
						
						<div class="col-md-12">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="description_name" id="description_name"></textarea>
									</div> 
								</div> 
                            </div>
                        </div>
                       <div class="col-md-6"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-sm btn-success"><i class="fas fa-save"></i> Save</button>&nbsp;
                    <button type="button" onclick="javascript:window.location.reload()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
	
<div id="formModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
  <div class="modal-content">
   <div class="modal-header">
         <h4 class="modal-title"></h4>
         <button type="button" class="close" onclick="javascript:window.location.reload()" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
		 <form id="update_form" class="form-horizontal" method="POST">
					@csrf
					<div class="row">
                        <div class="col-md-6">
                           
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
								<div class="col-sm-8">
                                    <select name="id_employee" id="id_employee_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employee_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Dicipline Type</label>
								<div class="col-sm-8">
                                    <select name="dicipline_type" id="dicipline_type_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="dicipline_type_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Using Letter Number</label>
                                <div class="col-sm-8 ">
                                    <input type="checkbox" name="using_letter_number" id="using_letter_number_edit" class="form-control form-control-sm float-left" style="height:20px;width:20px;margin-top:5px;">
                                    <span class="invalid-feedback" role="alert" id="using_letter_number_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>  
							<div class="row award_letter_number_edit" style="display:none;">
                                <label class="col-sm-4 col-form-label">Letter Number</label>
								 <div class="col-sm-8">
									<div class="input-group">
										<input name="award_letter_number" id="award_letter_number_edit" type="text" class="form-control form-control-sm" onpaste="return false;" ondrop="return false;" autocomplete="off" onkeydown="return false;" style="border-radius: 5px 0 0 5px;">
										<div class="input-group-append">
											<span class="input-group-text far fa-list-alt form-control-sm"></span>
										</div>
									</div>
									<span class="invalid-group"  style="font-size:10px;color:#dc3545;" role="alert" id="award_letter_number_editError">
                                        <strong></strong>
                                    </span>
								</div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Remark</label>
                                <div class="col-sm-8">
                                    <input type="text" name="remark" id="remark_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="remark_editError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
							<div class="row">                          
							   <label class="col-sm-4 col-form-label">Attachment</label>
							   <div class="col-sm-8">
								 <div class="custom-file">
								  <input type="file" name="attachment" class="custom-file-input" id="customFile">
								  <span class="invalid-feedback" role="alert" id="attachmentError">
                                        <strong></strong>
                                    </span>
									<a id="attachment_edit" href="#" target="_blank">Download File</a>
								  <label class="custom-file-label" for="customFile"><i>Max 1 MB</i></label>
								</div>								
							   </div>
							</div> 
							<div class="row kpk_id" style="display:none;">
                                <label class="col-sm-4 col-form-label">KPK Status</label>
								<div class="col-sm-8">
                                    <select name="kpk_status" id="kpk_status_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="kpk_status_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                        </div>
					
						<div class="col-md-6" style="margin-bottom:30px;">
                            <div class="row reference_number_edit_" style="display: none;">
                                <label class="col-sm-4 col-form-label">Reference Number</label>
                                 <div class="col-sm-8">
                                    <div class="input-group">
                                        <input name="reference_number" id="reference_number_edit" type="text" class="form-control form-control-sm" style="border-radius: 5px 0 0 5px;">
                                        <div class="input-group-append">
                                            <span class="input-group-text far fa-list-alt form-control-sm"></span>
                                        </div>
                                    </div>
                                    <span class="invalid-group"  style="font-size:10px;color:#dc3545;" role="alert" id="reference_number_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Company</label>
								<div class="col-sm-8">
                                    <select name="id_company" id="company_edit" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="company_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Reference Date</label>
                                <div class="col-sm-8">
                                    <input name="reference_date" id="reference_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="reference_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Effective Date</label>
                                <div class="col-sm-8">
                                    <input name="effective_date" id="effective_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="effective_date_editError">
                                        <strong></strong>
                                    </span>
                                </div>
							</div>
							<div class="row">
								<label class="col-sm-4 col-form-label kpk_date">Expired Date</label>
                                <div class="col-sm-8">
                                   <input name="expired_date" id="expired_date_edit" class="form-control form-control-sm">
                                    <span class="invalid-date" style="font-size:10px;color:#dc3545;" role="alert" id="expired_date_editError">
                                        <strong></strong>
                                    </span>   
                                </div>
                            </div>

							 <div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
									  <select id="select2status_edit" name="status" class="form-control form-control-sm" style="width: 100%;">
										<option value="A">Active</option>
										<option value="I">Inactive</option>
									  </select>
                                    <span class="invalid-feedback" role="alert" id="status_editError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>														
                        </div>
						
						<div class="col-md-12">
                            <div class="row">                             
								<label class="col-sm-2 col-form-label">Description</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="description_name" id="description_name_edit"></textarea>
									</div> 
								</div> 
                            </div>
                        </div>
                       <div class="col-md-6"></div>
                    </div>
               
					<div class="modal-footer">
                <div class="form-group" align="center">
                 <input type="hidden" name="action" id="action_edit" />
                 <input type="hidden" name="hidden_id" id="hidden_id" />
                 <button type="submit" name="action_button" id="action_button" class="btn btn-primary" value="edit"><i class="fas fa-edit"></i> Update</button>
                </div>
                <button type="button" onclick="javascript:window.location.reload()" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
					
				</form>   	 
        </div>
     </div>
    </div>
</div>
	

<div id="browseModal" class="modal fade" role="dialog" style="z-index:9999;">
 <div class="modal-dialog modal-lg">
  <div class="modal-content">
   <div class="card-body">
					<table align="center" id="bro_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr align="center">
						<th>No.</th>
						<th data-priority="5">Letter Date</th>
						<th data-priority="2">Letter Number</th>
						<th data-priority="4">Employee Name</th>
						<th>NIK</th>
						<th data-priority="3">Category</th>
						<th>Start Date</th>
						<th>End Date</th>
						<th data-priority="1" width=100>Action</th>
					  </tr>
					 </thead>
					 <tbody></tbody>
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
.dtfc-fixed-left{
	z-index:10;
}
td.text-center{
	text-align:center;
}
</style>
@stop
@section('scripts')
<script>
let global_dicipline_type = "";

function browse_table(global_dicipline_type) {
	$('#browseModal').modal('show');
	$("#bro_table").DataTable({
		responsive: true,
		paging: true,
		  pageLength:10,
		  destroy:true,
          lengthChange: true,
          searching: true,
          ordering: true,
          info: true,
          autoWidth: true,
		  columnDefs:false,		
          columns : [
            { data : 'no'},
            { data : 'tgl_surat' },
            { data : 'no_surat' },
            { data : 'name' },
            { data : 'nik' },
            { data : 'kategori' },
            { data : 'tgl_mulai' },
            { data : 'tgl_berakhir' },
            { data : 'action' },
          ],    
          ajax: {
            type: 'GET',
			url: "<?= url('career_administration/award_dicipline/dicipline/browse') . '?dicipline=' ?>" + global_dicipline_type,
            dataType: 'JSON',
            dataSrc : function (json) {
              var return_data = new Array();
              var no=1;
              for(var i=0;i< json.length; i++){
                return_data.push({
                  'no'          : '<center>'+no+'</center>',
                  'tgl_surat'   : '<center>'+json[i]['tgl_surat']+'</center>',
                  'no_surat'   : '<center>'+json[i]['no_surat']+'</center>',
                  'name'   : '<center>'+json[i]['name']+'</center>',
                  'nik'   : '<center>'+json[i]['nik']+'</center>',
                  'kategori'   : '<center>'+json[i]['kategori']+'</center>',
                  'tgl_mulai'   : '<center>'+json[i]['tgl_mulai']+'</center>',
                  'tgl_berakhir'   : '<center>'+json[i]['tgl_berakhir']+'</center>',
                  'action'   : '<center><button type="button" name="check" class="btn btn-success btn-sm" title="Check" onClick="check('+json[i]['id']+')"><i class="fa fa-check-square-o fa-lg" aria-hidden="true"></i></button> </center>',
                })
                no++;
              }
              return return_data;
            }
          }
      });
}
function check(id) {	
		//  console.log(id);
		  $.ajax({
			url: "<?= url('career_administration/award_dicipline/dicipline/checkid') . '?dicipline=' ?>" + global_dicipline_type +"<?= '&id=' ?>"+id,
		   dataType:"json",
		   success:function(data)
		   {
			$("#award_letter_number").val(data.result[0].no_surat);
			$("#award_letter_number_edit").val(data.result[0].no_surat);
			$("#browseModal").modal('hide');
		   }
		  })
}


function get_dicipline_type() {
	$.getJSON('<?= url('career_administration/award_dicipline/dicipline/get_dicipline_type') ?>', function (data) {
            $('#dicipline_type').select2({
                data: data,
            }).on('change', function (e) {
				$("#award_letter_number").val('');

                if($(this).select2('data')[0].code != 'verbal'){
                    const tipeSp = ['SP1', 'SP2', 'SP3', 'SPDT'];

                    if(tipeSp.includes($(this).select2('data')[0].code) == true){
                        $("#award_letter_number").attr('readonly',false);
                        if($(this).select2('data')[0].code == 'SP1'){
                            global_dicipline_type = 'SP1';
                        }
                        else if($(this).select2('data')[0].code == 'SP2'){
                            global_dicipline_type = 'SP2';
                        }
                        else if($(this).select2('data')[0].code == 'SP3'){
                            global_dicipline_type = 'SP3';
                        }
						else if($(this).select2('data')[0].code == 'SPDT'){
                            global_dicipline_type = 'SPDT';
                        }
                        $("#award_letter_number").attr('onclick', 'browse_table("'+global_dicipline_type+'")');
						$(".kpk_id").hide();
						$("#description_name").summernote("code", false);
						$(".kpk_date").html('Expired Date');
                    } 
					else if($(this).select2('data')[0].code == 'KPK'){
						$(".kpk_id").show();
						$("#description_name").summernote("code", 
						'Tanggal Review Bulan ke 1: <br>Tanggal Review Bulan ke 2: <br>Tanggal Review Bulan ke 3: <br>Tanggal Review Bulan ke 4:'
						);
						$(".kpk_date").html('Pass Date');
					}
					else {
                        $('#using_letter_number').prop('checked', false);
                        $("#award_letter_number").attr('readonly',true);
                        $("#award_letter_number").attr('onclick', 'javascript:;');
						$(".kpk_id").hide();
						$("#description_name").summernote("code", false);
						$(".kpk_date").html('Expired Date');
                    }
                }
                else{
                    $('#using_letter_number').prop('checked', false);
                    $("#award_letter_number").attr('readonly',true);
                    $("#award_letter_number").attr('onclick', 'javascript:;');
                }       

			}).trigger('change');
        }).fail(function (data) { // Call failed
            get_dicipline_type();
        });	
}

function get_dicipline_type_edit() {
	$.getJSON('<?= url('career_administration/award_dicipline/dicipline/get_dicipline_type') ?>', function (data) {
			$('#dicipline_type_edit').select2({
                data: data,
            }).on('change', function (e) {
				$("#award_letter_number_edit").val('');				
				if($(this).select2('data')[0].code != 'verbal'){
                    const tipeSp = ['SP1', 'SP2', 'SP3', 'SPDT'];

                    if(tipeSp.includes($(this).select2('data')[0].code) == true){
                        $("#award_letter_number_edit").attr('readonly',false);
                        if($(this).select2('data')[0].code == 'SP1'){
                            global_dicipline_type = 'SP1';
                        }
                        else if($(this).select2('data')[0].code == 'SP2'){
                            global_dicipline_type = 'SP2';
                        }
                        else if($(this).select2('data')[0].code == 'SP3'){
                            global_dicipline_type = 'SP3';
                        }
						else if($(this).select2('data')[0].code == 'SPDT'){
                            global_dicipline_type = 'SPDT';
                        }
                        $("#award_letter_number_edit").attr('onclick', 'browse_table("'+global_dicipline_type+'")');
						$(".kpk_id").hide();
						$("#description_name_edit").summernote("code", false);
						$(".kpk_date").html('Expired Date');
                    } 
					else if($(this).select2('data')[0].code == 'KPK'){
						$(".kpk_id").show();
						$("#description_name_edit").summernote("code", 
						'Tanggal Review Bulan ke 1: <br>Tanggal Review Bulan ke 2: <br>Tanggal Review Bulan ke 3: <br>Tanggal Review Bulan ke 4:'
						);
						$(".kpk_date").html('Pass Date');
					}
					else {
                        $('#using_letter_number_edit').prop('checked', false);
                        $("#award_letter_number_edit").attr('readonly',true);
                        $("#award_letter_number_edit").attr('onclick', 'javascript:;');
						$(".kpk_id").hide();
						$("#description_name_edit").summernote("code", false);
						$(".kpk_date").html('Expired Date');
                    }
				}
				else{
                    $('#using_letter_number_edit').prop('checked', false);
					$("#award_letter_number_edit").attr('readonly',true);
					$("#award_letter_number_edit").attr('onclick', 'javascript:;');
				}					
			});
        }).fail(function (data) { // Call failed
            get_dicipline_type_edit();
        });	
}
function get_employee() {
	$.getJSON('<?= url('career_administration/award_dicipline/dicipline/get_employee') ?>', function (data) {
            $('#id_employee').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ...",
                data: data,
            });
			$('#id_employee_edit').prepend('<option selected></option>').select2({
				placeholder: "Select Employee ...",
                data: data,
            });
        }).fail(function (data) { // Call failed
            get_employee();
        });	
}	
function get_company() {
	 $.getJSON('<?= url('career_administration/award_dicipline/dicipline/get_company') ?>', function (data) {
            $('#company').select2({
                data: data,
				disabled: true
            });
			$('#company_edit').select2({
                data: data,
				disabled: true
            });
        }).fail(function (data) { // Call failed
            get_company();
        });	
}

$(function () {
$('.summernote').summernote({
	height:300,
});

$('#reference_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#reference_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#effective_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#effective_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#expired_date').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });	
$('#expired_date_edit').datepicker({
            uiLibrary: 'bootstrap4',
			format: 'yyyy-mm-dd',
        });			
$('#attachment_type').select2({width:'100%'});	
$('#attachment_type_edit').select2({width:'100%'});

$('#select2status').select2();	
$('#select2status_edit').select2();

		get_company();
		get_employee();
		get_dicipline_type_edit();
		
		let kpk_status = [
			{ id: 'Pass', text: 'Pass' },
			{ id: 'Failed', text: 'Failed' },
		];
	
		$('#kpk_status, #kpk_status_edit').prepend('<option selected></option>').select2({
			placeholder: "Select KPK Status",
			data: kpk_status,
			allowClear: true,
		});
	
});

$(document).on('click', '.new', function () {
			$('.summernote').summernote('reset');
            $("#diciplineForm")[0].reset();
            $("#diciplineForm .modal-title").html("<span class='fas fa-plus'></span> Form Dicipline");
            $(".invalid-feedback").children("strong").text("");
            $("#diciplineForm input").removeClass("is-invalid");
            $("#diciplineForm textarea").removeClass("is-invalid");
			$("#reference_number").attr('readonly',true);
			get_dicipline_type();
            $('#modal_form_dicipline').modal('show');
        });

    $(function () {
        $('#diciplineForm').submit(function (e) {
            e.preventDefault();
			var formData = new FormData(this);
       //     let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
			$(".invalid-group").children("strong").text("");
            $(".invalid-date").children("strong").text("");
            $("#diciplineForm input").removeClass("is-invalid");
                $.ajax({
                    method: "POST",
                    headers: {
                        Accept: "application/json"
                    },
					enctype: 'multipart/form-data',
					processData: false,  // Important!
					contentType: false,
					cache: false,
					url: "{{ route('dicipline.save') }}",
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_dicipline').modal('hide');
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
							let err = "";
                            Object.keys(errors).forEach(function (key) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "Error").children("strong").text(errors[key][0]);
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

    });

		

$(document).ready(function(){
	bsCustomFileInput.init();
	
    var table = $('#dicipline_table').DataTable({
        processing: true,
    //    serverSide: true,
	//	responsive: true,
		scrollX: true,
		scrollCollapse: true,
		fixedColumns: {
			right: 2,
			left: 1,
		},
		pageLength: 50,
        ajax: {
		   url: "{{ route('dicipline.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
				$('#dicipline_table').DataTable().ajax.reload();
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
			if(access_print == 0){
				$(row).find('.print').css('display', 'none');
			}	
		},  
        columns: [
		{
                defaultContent: '',
				orderable: false,
				},
		{   // Checkbox select column
                data: 'id_transaction',
                defaultContent: '',
                orderable: false
            },
		{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{ data: 'reference_number', name: 'reference_number' },
            { data: 'award_letter_number', name: 'award_letter_number' },
			{ data: 'nik_employee', name: 'nik_employee' },
			{ data: 'employee_name', name: 'employee_name' },
            { data: 'position', name: 'position'},
            { data: 'department', name: 'department' },
            { data: 'principal', name: 'principal' },
            { data: 'region', name: 'region' },
            { data: 'branch', name: 'branch' },
			{ data: 'desc_type', name: 'desc_type' },
			{ data: 'effective_date', name: 'effective_date' },
			{ data: 'expired_date', name: 'expired_date' },
			{ data: 'attachment', name: 'attachment',render: function ( data, type, row ) {	
					if(data == null){
						return "";
					}				
					else if(row['attachment_type']==null){
						return '<a href="../../project/storage/app/public/upload/dicipline/'+ row['nik_employee'] +'/'+data+'" target="_blank">Download File</a>';
					}
					else if(row['attachment_type']=='pdf'){
						return '<a download="'+Date.now()+'.pdf" href="data:application/pdf;base64,'+ data + '">Download File</a>';
					}
					else if(row['attachment_type']=='image'){
						return '<a download="'+Date.now()+'.jpg" href="data:image/jpg;base64,'+ data + '">Download File</a>';
					}
				} 
			},
			{ data: 'status', name: 'status' },
			{ data: 'created_by_user', name: 'created_by_user' },
			{ data: 'progress_status', name: 'progress_status', className: 'text-center', render: function ( data, type, row ) {
					if(data == 'Pass'){
							return '<span class="badge badge-success" style="padding:5px;font-size:14px;">'+data+'</span>';
					}
					else if(data == 'Failed'){
							return '<span class="badge badge-danger" style="padding:5px;font-size:14px;">'+data+'</span>';
					}
					else{
							return data;
					}
				}
			},
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {										
					return data;
				} 
			},
        ]
    });
	table.on('order.dt search.dt', function () {
        let i = 1;
        table.cells(null, 1, { search: 'applied', order: 'applied' }).every(function (cell) {
            this.data(i++);
        });
    }).draw();
	
	$('#advanced').click(function(){
		$('.cf').select2({width:'100%'});
		if($("#cf").css('display') == 'none'){
			$("#cf").show("slow");
		}
		else {
			$("#cf").hide("slow");
		}		
	});
	
  $('#update_form').on('submit', function(event){
  event.preventDefault();
  var action_url = '';

  if($('#action_edit').val() == 'Edit')
  {
   action_url = "{{ route('dicipline.update') }}";
  }
  $(".invalid-feedback").children("strong").text("");
  $(".invalid-group").children("strong").text("");
  $("#update_form input").removeClass("is-invalid");
  $("#update_form textarea").removeClass("is-invalid");
   $.ajax({
	enctype: 'multipart/form-data',
	processData: false,  // Important!
	contentType: false,
	cache: false,
   url: action_url,
   method:"POST",
   data:new FormData(this),
   dataType:"json",
   beforeSend: function () {
		$('#loader').removeClass('hidden');
	},
   success:function(response)
   {
	    if (response.status == 'true') {
			 $('#formModal').modal('hide');
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
				text: 'Something went wrong! [Unknown Error]'
			}).then(function(){ 
				   location.reload();
				   }
				);
		} 
   },
   complete: function(){
		$('#loader').addClass('hidden');
	},
    error: function (response) {
		if (response.status === 422) {
			let errors = response.responseJSON.errors;
			let err = "";
			Object.keys(errors).forEach(function (key) {
				$("#" + key + "_edit").addClass("is-invalid");
				$("#" + key + "_editError").children("strong").text(errors[key][0]);
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

 $(document).on('click', '.edit', function(){
  var id_transaction = $(this).attr('id');
  var fileInput = document.getElementById('customFile');
	fileInput.value = '';
	fileInput.dispatchEvent(new Event('change'));
  $.ajax({
   url :"dicipline/edit/"+id_transaction,
   dataType:"json",
   success:function(data)
   {
	$("#dicipline_type_edit").val(data.result.dicipline_type).trigger('change');
    if(data.result.award_letter_number != null){
        $('#using_letter_number_edit').click();
        $('#award_letter_number_edit').val(data.result.award_letter_number).show();
        $('#reference_number_edit').val(data.result.reference_number);
    } else {
        const tipeSp_ = ['SP1', 'SP2', 'SP3', 'SPDT'];
        if(tipeSp_.includes($("#dicipline_type_edit").select2('data')[0].code) == true){
            $('#using_letter_number_edit').click();
        }
        $('#award_letter_number_edit').val(data.result.reference_number).show();
        $('#reference_number_edit').val(data.result.reference_number);
    }

    $('#id_employee_edit').val(data.result.id_employee).trigger('change');
    $('#effective_date_edit').val(data.result.effective_date);
    $('#expired_date_edit').val(data.result.expired_date);
	$("#description_name_edit").summernote("code", data.result.description_name);
    $('#reference_date_edit').val(data.result.reference_date);
	if(data.result.attachment_type != null){
		$('#attachment_edit').css('display','inline');
		if(data.result.attachment_type == 'image'){
			document.getElementById("attachment_edit").href = "data:image/jpg;base64,"+data.result.attachment;
		}
		else if(data.result.attachment_type == 'pdf'){
			document.getElementById("attachment_edit").href = "data:application/pdf;base64,"+data.result.attachment;
		}
	}
	else if(data.result.attachment_type == null && data.result.attachment != null){
		$('#attachment_edit').css('display','inline');
		document.getElementById("attachment_edit").href = "../../project/storage/app/public/upload/dicipline/"+data.result.nik_employee+"/"+data.result.attachment;	
	}
	else{
		$('#attachment_edit').css('display','none');
	}
    $('#remark_edit').val(data.result.remark);
    $('#attachment_type_edit').val(data.result.attachment_type).trigger('change');
    $('#select2status_edit').val(data.result.status).trigger('change');
    $('#id_company_edit').val(data.result.id_company);
    $('#kpk_status_edit').val(data.result.progress_status).trigger('change');
    $('#hidden_id').val(id_transaction);
    $('.modal-title').text('Edit Record');
    $('#action_button').val('Edit');
    $('#action_edit').val('Edit');
    $('#formModal').modal('show');
   }
  })
 });
 

$(document).on('click', '.delete', function (event) {
	id_transaction = $(this).attr('id');
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
			   url:"dicipline/destroy/"+id_transaction,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#dicipline_table').DataTable().ajax.reload();				 
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

});


$(document).on('change', '#using_letter_number', function (event) {
    if(this.checked){
        $('.award_letter_number').show();
    } else{
        $('input[name="award_letter_number"]').val('');
        $('.award_letter_number').hide();
    }
});

$(document).on('change', '#using_letter_number_edit', function (event) {
    if(this.checked){
        const tipeSp_ = ['SP1', 'SP2', 'SP3', 'SPDT'];
        if(tipeSp_.includes($("#dicipline_type_edit").select2('data')[0].code) == false){
            $('input[name="award_letter_number"]').val('');
        }
        $('.award_letter_number_edit').show();
    } else{
        $('input[name="award_letter_number"]').val('');
        $('.award_letter_number_edit').hide();
    }
});

</script>

@endsection