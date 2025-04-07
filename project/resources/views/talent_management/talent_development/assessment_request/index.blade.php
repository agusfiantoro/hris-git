@extends('adminlte::page')
@section('title', 'Assessment Request')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Assessment Request</h5>
				<div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Assessment Request</button>
                </div>
            </div>      
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="talent_table" style="width:100%;" class="display table table-striped table-bordered table-hover datatable">
				 <thead>
				  <tr>		
					<th></th>
					<th></th>
					<th>No</th>
					<th data-priority="3">Reference Number</th>
					<th>Request By</th>
					<th>Note</th>
					<th>Note Revised</th>
					<th>Note Rejected</th>
					<th data-priority="2" style="white-space:nowrap;">Approval Status</th>
					<th data-priority="1" width=100>Action</th>
				  </tr>
				 </thead>
				</table>
			</div>
        </div>
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true" style="z-index:1050;">
  <div class="modal-dialog modal-xl">
  <div id="modal_second"></div>
    <!-- Modal content-->
    <div class="modal-content">
		<form method="POST" id="talentForm">
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
			<button type="submit" class="btn btn-info btn-sm " id="save_and_submit"><i class="fas fa-paper-plane"></i></button>&nbsp;
			<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save as Draft</button>&nbsp;
			<button type="submit" class="edit_request btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
			<button type="button" class="btn btn-default" onclick="on_close_modal()"  data-dismiss="modal">Close</button>
		  </div>
		</form>  
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
	*:focus{
	  outline:0px;
	}
</style>
@stop
@section('scripts')
<script type="text/javascript">
let id_talent = 0;
//let global_id_approval = null;
let global_id_employee = null;
let global_id_emp_detail = 0;

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Form Assessment Request");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
	$("#save_and_submit").show().html('<i class="fas fa-paper-plane"></i> Save & Submit').addClass('addForm');
    $.ajax({
			url: "{{ route('ass.modal_detail') }}",
			data:{global_talent:0},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

function loadedit(id_talent){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
//	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Assessment Request");
	$("#save_button").css("display","none");
    $.ajax({
			url: "{{ route('ass.modal_detail') }}",
			data:{global_talent:id_talent},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

function on_close_modal() {
	$('#talent_table').DataTable().ajax.reload(); 
}

$(document).ready(function(){
	
	$('#talent_table').DataTable({
            processing: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('ass.index') }}",
				data: {id_url: global_url_server},
    		    error: function (jqXHR, textStatus, errorThrown) {
    			//		$('#talent_table').DataTable().ajax.reload();
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

				if(data['code_app_status'] == 'Approved'){
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Request_Approval'){
				//	$(row).find('td:eq(20)').css('background', '#1db8d0');
					$(row).find('.submit_approve').css('display', 'none');
				}
				if(data['code_app_status'] == 'Cancel'){
				//	$(row).find('td:eq(20)').css('background', '#dc3545');
				//	$(row).find('td:eq(18)').css('float', 'right');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				//	$(row).find('.edit').css('display', 'none');
				}
				if(data['code_app_status'] == 'Revised'){
				//	$(row).find('td:eq(20)').css('background', '#60b8f8');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Rejected'){
				//	$(row).find('td:eq(20)').css('background', '#fe8590');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
				//	$(row).find('.edit').css('display', 'none');
					$(row).find('.delete').css('display', 'none');
				}
				if(data['code_app_status'] == 'Partial_Approved'){
				//	$(row).find('td:eq(20)').css('background', '#ffdf7e');
					$(row).find('.cancel').css('display', 'none');
					$(row).find('.submit_approve').css('display', 'none');
					$(row).find('.delete').css('display', 'none');
				}
			  },  
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_talent_assessment_request',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'reference_number', name: 'reference_number' },
    			{ data: 'emp_name', name: 'emp_name' },
    			{ data: 'notes', name: 'notes' },
				{ data: 'note_revised', name: 'note_revised' },
    			{ data: 'note_rejected', name: 'note_rejected' },
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
    			{ data: 'action', name: 'action', orderable: false, className: 'space' },
            ],
         
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

const get_employee_by = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/assessment_request/get_employee_by') ?>',
            dataType: 'json',
            success: function (res) {
				$('#id_employee_request').select2({
					data: res,
				}).on('change', function (e) {
					if(res.length > 0){
						global_id_employee = res[0].id;
						$('#by_pos').val(res[0].route_name);
						$('#branch').val(res[0].branch);
						$('#grade').val(res[0].grade);
					}
				}).trigger('change');
            },
        });
        return result;
    } catch (error) {
        get_employee_by();
    }	
}

const get_hr_email = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/assessment_request/get_hr_email') ?>',
            method: "GET",
            success: function (res) {
			$('#cc_email').select2({
				data: res,
			});
            },
        });
        return result;
    } catch (error) {
     //   get_hr_email();
    }
}

/*
const get_hierachy_talent = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/assessment_request/get_hierachy_talent') ?>',
            method: "GET",
            success: function (res) {
				$('#id_approval').select2({
					data: res,
				}).on('change', function (e) {
					if(res.length > 0){
						global_id_approval = res[0].id;
					//	console.log(global_id_approval);
					//	$('#id_approval_request').val(res[0].route_name);
					//	$('#id_approval_request').val(res[0].route_name);
					}
				}).trigger('change')
            },
        });
        return result;
    } catch (error) {
     //   get_hierachy_talent();
    }
}
*/

const get_all_hierachy = async (global_id_employee) => {
	let myData = {
		id_employee: global_id_employee,
	//	id_approval: global_id_approval,
	};	
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/assessment_request/get_all_hierachy') ?>',
            method: "GET",
			data: myData,
            success: function (res) {
				$('#id_approval_request').select2({
					data: res,
				});
            },
        });
        return result;
    } catch (error) {
        get_all_hierachy();
    }
}

const get_approval_status = async () => {
    try {
        let result;
        result = await $.ajax({
            url: '<?= url('talent_management/talent_development/assessment_request/get_approval_status') ?>',
            method: "GET",
            success: function (res) {
			$('#id_approval_status').select2({
				data: res,
			});
            },
        });
        return result;
    } catch (error) {
     //   get_approval_status();
    }
}

	var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('ass.save') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });

		  $(".edit_request").on("click",function(){
			AjaxUrl = "{{ route('ass.update') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });		

	$('#talentForm').submit(function (e) {
			 e.preventDefault();
			
			let thisButtonId = e.originalEvent.submitter.id;
            if(thisButtonId == 'save_and_submit'){
                let addForm = $("#save_and_submit").hasClass('addForm');
                if(addForm == true){
                    AjaxUrl = "{{ route('ass.save') }}";
                } else {
                    AjaxUrl = "{{ route('ass.update') }}";
                }
            }
            let formData = $(this).serializeArray();
            $(".invalid-feedback").children("strong").text("");
            $(".invalid-date").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#talentForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: AjaxUrl,
                    data: formData,
					beforeSend: function () {
						$('#loader').removeClass('hidden');
					},
                    success: function (response) {
                        if (response.status == 'true') {
							let thisIdTalent = response.data;
							
							if(thisButtonId == 'save_and_submit'){
                                $.ajax({
                                	url:"assessment_request/submit_approve/"+thisIdTalent,
                                    success: function(resp) {										
                                        $('#myModal').modal('hide');
                                        swal({
                                            title: "Data Submited!",
                                            icon: "success",
                                            buttons: {
                                                confirm: {
                                                    className: 'btn-success'
                                                },
                                            },
                                        }).then(ok => { $('#talent_table').DataTable().ajax.reload(); });
                                    },
                                    complete: function() {
                                        $('#loader').addClass('hidden');
                                    },
                                })
                            }
							else {
                                $('#myModal').modal('hide');
								$('#loader').addClass('hidden');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
								   $('#talent_table').DataTable().ajax.reload();
								   }
								);
                            }
                        } else {
							$('#loader').addClass('hidden');
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
                            });
                        }
                    },
					complete: function(){
					//	$('#loader').addClass('hidden');
					},
					error: function (response) {
						$('#loader').addClass('hidden');
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;							
                            Object.keys(errors).forEach(function (key) {
								var key_temp = key.replaceAll(".", "_");
                                $("#" + key_temp).addClass("is-invalid");
								$("select[id='" + key + "']").addClass("custom-select");
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
		
$(document).on('click', '.submit_approve', function (event) {
	id_talent_assessment_request = $(this).attr('id');
    event.preventDefault();
    swal({
        title: 'Are you sure?',
        text: 'This Data will be Submited!',
        icon: 'warning',
       buttons: true,
		  confirmButtonText: 'Yes, Submit it!'
    }).then(function(value) {
        if (value) {
            $.ajax({
			   url:"assessment_request/submit_approve/"+id_talent_assessment_request,
			   beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
			   success:function(response){
                    if(response.status == 'true'){
                        setTimeout(function() {
                            $('#confirmModal').modal('hide');
                            $('#talent_table').DataTable().ajax.reload();
                            swal({
                                title: "Data Submited!",
                                icon: "success",
                                buttons: {
                                    confirm: {
                                        className: 'btn-success'
                                    },
                                },
                            }).then(ok => {
                                $('#talent_table').DataTable().ajax.reload();
                            });
                        }, 50);
                    } else {
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
                        });
                    }
			   },
			   complete: function(){
					$('#loader').addClass('hidden');
				},
			  })
        }
    });
});

$(document).on('click', '.cancel', function (event) {
	id_talent_assessment_request = $(this).attr('id');
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
			   url:"assessment_request/cancel/"+id_talent_assessment_request,
			   success:function(data)
			   {
				setTimeout(function(){
				 $('#confirmModal').modal('hide');
				 $('#talent_table').DataTable().ajax.reload();				 
				 swal({
					title: "Data Cancel!",
					  icon: "success",
					   buttons: {confirm : {className:'btn-success'},},
					}).then(ok => {
						$('#talent_table').DataTable().ajax.reload();
					});
				}, 50);
			   }
			  })
        }
    });
});
			
</script>
@endsection