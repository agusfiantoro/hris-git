@extends('adminlte::page')
@section('title', 'Master Talent Matrix')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Talent Matrix</h5>
				<div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success" onclick="loadnew()"><i class="fas fa-plus"></i> Add Master Talent</button>
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
					<th>Matrix Box</th>
					<th>Matrix Name</th>
					<th>Job Grade</th>
					<th>Rating</th>
					<th>KPI Min</th>
					<th>KPI Max</th>
					<th>Potencies</th>
					<th>Competencies</th>
					<th data-priority="1" style="text-align:center;" width=100>Action</th>
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
			<button type="submit" class="save btn btn-sm btn-success" id="save_button"><i class="fas fa-save"></i> Save</button>&nbsp;
			<button type="submit" class="edit_master btn btn-sm btn-primary" id="edit_button"><i class="fas fa-save"></i> Update</button>&nbsp;
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
	
</style>
@stop
@section('scripts')
<script type="text/javascript">
let id_talent = 0;

var AjaxUrl = "";
		  $(".save").on("click",function(){
			AjaxUrl = "{{ route('talent.save') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });

		  $(".edit_master").on("click",function(){
			AjaxUrl = "{{ route('talent.update') }}";
			$(this).closest(".card").find("talentForm").submit();
		  });	
	
	 $('#talentForm').submit(function (e) {
            e.preventDefault();			
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
                                $('#myModal').modal('hide');
	                            swal({
	                                icon: 'success',
	                                title: 'Success',
	                                text: response.message
	                            }).then(function(){ 
								   $('#talent_table').DataTable().ajax.reload();
								   }
								);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! '+response.message,
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
	

function on_close_modal() {
	$('#talent_table').DataTable().ajax.reload(); 
}

function loadnew(){
	$("#contentBody").html('');
	$(".invalid-feedback").children("strong").text("");
	$(".invalid-date").children("strong").text("");
	$(".feedback").children("strong").text("");
	$(".table-invalid-feedback").children("strong").text("");
	$("#talentForm input").removeClass("is-invalid");
	$("#modal-title").html("<span class='fas fa-plus'></span> Form Talent Matrix");
	$("#save_button").css("display","inline");
	$("#edit_button").css("display","none");
    $.ajax({
			url: "{{ route('talent.modal_detail') }}",
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
	$("#modal-title").html("<span class='fas fa-edit'></span> Edit Talent Matrix");
	$("#save_button").css("display","none");
	$("#edit_button").css("display","inline");
    $.ajax({
			url: "{{ route('talent.modal_detail') }}",
			data:{global_talent:id_talent},
			success: function(result){
			$("#contentBody").html(result);
			$("#myModal").modal('show'); 
		}
	});
}

$(document).ready(function(){
	
	$('#talent_table').DataTable({
            processing: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('talent.index') }}",
    		    error: function (jqXHR, textStatus, errorThrown) {
    			//		$('#talent_table').DataTable().ajax.reload();
    				}
    		  },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_talent_matrix',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'matrix_box', name: 'matrix_box' },
    			{ data: 'matrix_name', name: 'matrix_name' },
    			{ data: 'job_grade', name: 'job_grade' },
    			{ data: 'rating', name: 'rating' },
    			{ data: 'kpi_value_min', name: 'kpi_value_min' },
    			{ data: 'kpi_value_max', name: 'kpi_value_max' },
    			{ data: 'psychogram', name: 'psychogram' },
    			{ data: 'bei', name: 'bei' },
    			{ data: 'action', name: 'action', orderable: false },
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

const get_grade = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/master_talent_setting/master_talent_rating/get_grade') ?>',
            dataType: 'json',
            success: function (res) {
				$('#job_grade').prepend('<option selected></option>').select2({
					placeholder: "Select Job Grade ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_grade();
    }	
}

$(document).on('change', '#job_grade', function (event, istrigger) {  
	$('#rating').attr('readonly',true);
    if(!istrigger){
		$('#rating').empty();		
		get_rating($(this).select2('val'));	
	}
});

const get_rating = async (id_job_grade) => {
	let result;
	let myData = {
			id_job_grade: id_job_grade,
		};	
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/master_talent_setting/master_talent_rating/get_rating') ?>',
            method: "GET",
			data: myData,
            success: function (res) {
				$('#rating').attr('readonly',false);
				if(res.length > 0){
					$('#rating').prepend('<option selected></option>').select2({
						placeholder: "Select Rating ...",
						data: res,
					});
				}
				else{
					$('#rating').attr('readonly',true);
				}
				
            },
        });
        return result;
    } catch (error) {
    //    get_rating();
    }	
}

const get_group_matrix = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/master_talent_setting/master_talent_rating/get_group_matrix') ?>',
            dataType: 'json',
            success: function (res) {
				$('#group_matrix').prepend('<option selected></option>').select2({
					placeholder: "Select Box Matrix ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_group_matrix();
    }	
}
const get_conclusion = async () => {
	let result;
    try {
        result = await $.ajax({
            url: '<?= url('talent_management/master_talent_setting/master_talent_rating/get_conclusion') ?>',
            dataType: 'json',
            success: function (res) {
				$('#psychogram').prepend('<option selected></option>').select2({
					placeholder: "Select Potencies Conclusion ...",
					data: res,
				});
				$('#bei').prepend('<option selected></option>').select2({
					placeholder: "Select Competencies Conclusion ...",
					data: res,
					allowClear: true,
				});
            },
        });
        return result;
    } catch (error) {
        get_conclusion();
    }	
}

$(document).on('click', '.delete', function (event) {
	id_talent = $(this).attr('id');
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
			   url:"master_talent_rating/destroy/"+id_talent,
			   success:function(response)
			   {
				setTimeout(function(){				 
					if (response.status == 'true') {
							$('#myModal').modal('hide');
							swal({
								icon: 'success',
								title: 'Data Deleted!',
								text: response.message
							}).then(function(){ 
								$('#confirmModal').modal('hide');
								$('#talent_table').DataTable().ajax.reload();		
							   }
							);
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
});


</script>
@endsection