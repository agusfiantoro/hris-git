@extends('adminlte::page')
@section('title', 'Master Standard Grade')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Standard Grade</h5>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="grade_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Grade</th>
						<th data-priority="1" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_grade"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="gradeForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Standard Grade</h5>
                    <button type="button" onclick="javascript:window.location.reload()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Grade</label>
                                <div class="col-sm-8">
									<input name="id_job_grade" id="id_job_grade" type="hidden">	
                                    <input type="text" name="description" id="description" class="form-control form-control-sm" readonly>
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>																														
                        </div>
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_grade_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_grade-details" data-toggle="pill" href="#grade-details" role="tab" aria-controls="link_tab_grade-details" aria-selected="true">Standard Grade <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_grade_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="grade-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">											
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_grade_detail" style="margin-left:20px;"><span class="fas fa-plus"></span> Add Standard Grade</button>
											<button type="button" id="genquest" onclick="genquestion()" class="pull-right btn btn-xs btn-success"><span class="fa fa-refresh"></span> Generate Question</button>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_grade_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Question</th>
                                                        <th style="white-space:nowrap;">Min. Level</th>
                                                        <th style="white-space:nowrap;">Min. Score</th>
                                                        <th style="white-space:nowrap;">Status</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_grade_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_grade_detailError">
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
                <table id="sample_table_grade">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td>
								<input name="pagrade[0][id_grade_question]" id="pagrade_0_id_grade_question" type="hidden" class="form-control form-control-sm id_grade_question_input">		
                                <select name="pagrade[0][id_pa_question]" id="pagrade_0_id_pa_question" class="form-control form-control-sm select2 id_pa_question_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_pa_question_input_error" role="alert" id="pagrade_0_id_pa_questionError">
                                    <strong></strong>
                                </span>						
                        </td>
                        <td>
                                <select name="pagrade[0][id_minimum_score_level]" id="pagrade_0_id_minimum_score_level" class="form-control form-control-sm select2 id_minimum_score_level_input" style="width: 100%;"></select>
                                <span class="invalid-feedback id_minimum_score_level_input_error" role="alert" id="pagrade_0_id_minimum_score_levelError">
                                    <strong></strong>
                                </span>	
                        </td>
						<td>
                                <input type="text" name="pagrade[0][value]" id="pagrade_0_value" class="form-control form-control-sm value_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" readonly>
                                <span class="invalid-feedback value_input_error" role="alert" id="pagrade_0_valueError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>
								 <select name="pagrade[0][status]" id="pagrade_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                        <option value="A">Active</option>
                                        <option value="I">Inactive</option>
                                    </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="pagrade_0_statusError">
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
@section('scripts')
<script type="text/javascript">
let global_id_grade = "";
let global_id_grade_detail = 0;
let global_id_question = [];
let listQuestion = [];
let listLevel = {};

    $(function () {	
			
		$(document).on('click', '.new', function () {
            global_id_grade = "";
            $("#gradeForm")[0].reset();
            $("#table_grade_body").html("");
            $("#gradeForm .modal-title").html("<span class='fas fa-plus'></span> Form Standard Grade");
            $(".invalid-feedback").children("strong").text("");
            $(".feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#gradeForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
            $('#modal_form_grade').modal('show');
        });
		
		 $('#gradeForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();			
	
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
            $("#gradeForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_grade == '' ? "{{ route('pa_grade.save') }}" : "{{ route('pa_grade.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_grade').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#grade_table').DataTable().ajax.reload();
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: 'Something went wrong! [Unknown Error]'
                            });
                        }
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
									$("#tab_grade_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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


        $(document).on('click', '#new_grade_detail', function () {
            var content = jQuery('#sample_table_grade tr'),
                    size = global_id_grade_detail++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.attr('id_record', size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_grade_question_input').attr('id', 'pagrade_' + size + '_id_grade_question');
            element.find('.id_grade_question_input').attr('name', 'pagrade[' + size + '][id_grade_question]');
			
			element.find('.id_pa_question_input').attr('id', 'pagrade_' + size + '_id_pa_question');
            element.find('.id_pa_question_input').attr('name', 'pagrade[' + size + '][id_pa_question]');
            element.find('.id_pa_question_input_error').attr('id', 'pagrade_' + size + '_id_pa_questionError');
            element.find('.id_pa_question_input').select2({
                placeholder: "Select Question",
                allowClear: true,
                data: listQuestion
            })
            // .on('change', function (e) {
			// 	if(global_id_question.length > 1){
			// 		get_level($(this).select2('val'),element);
			// 	}
            // }).trigger('change');
			
			element.find('.id_minimum_score_level_input').attr('id', 'pagrade_' + size + '_id_minimum_score_level');
            element.find('.id_minimum_score_level_input').attr('name', 'pagrade[' + size + '][id_minimum_score_level]');
            element.find('.id_minimum_score_level_input_error').attr('id', 'pagrade_' + size + '_id_minimum_score_levelError');
		/*	element.find('.id_minimum_score_level_input').select2({
                placeholder: "Select Level",
                allowClear: true,
                data: global_id_level
            });
            element.find('.id_minimum_score_level_input').val('').trigger('change');
		*/	
			element.find('.value_input').attr('id', 'pagrade_' + size + '_value');
            element.find('.value_input').attr('name', 'pagrade[' + size + '][value]');
            element.find('.value_input_error').attr('id', 'pagrade_' + size + '_valueError');
			
			element.find('.status_input').attr('id', 'pagrade_' + size + '_status');
            element.find('.status_input').attr('name', 'pagrade[' + size + '][status]');
            element.find('.status_input_error').attr('id', 'pagrade_' + size + '_statusError');
			element.find('.status_input').select2();
			
			if($('#table_grade_body tr').length >= 0){
				$('#genquest').attr('disabled',true);
			}
            element.appendTo('#table_grade_body');
			 $('#table_grade_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            jQuery('#rec-' + id).remove();
			if($('#table_grade_body tr').length == 0){
				$('#genquest').attr('disabled',false);
			}
            $('#table_grade_body tr').each(function (index) {				
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

  $(document).on('click', '.edit', function () {
            let id_job_grade = $(this).attr('id');
            global_id_grade = id_job_grade;
            $("#gradeForm")[0].reset();
            $("#table_grade_body").html("");
            $("#gradeForm .modal-title").html("<span class='fas fa-edit'></span> Edit Standard Grade");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#gradeForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "<?= url('kpi/kpi_settings/master_grade_question/get_grade_edit') ?>",
                method: "GET",
                data: {id_job_grade: id_job_grade},
				beforeSend: function () {
					$('#loader').removeClass('hidden');
				},
                success: function (response) {
                    global_id_grade_detail = 0;
					$.each(response.pagrade, function (i, item) {
                        $('#new_grade_detail').trigger('click');
                    });
                    
                    $('#table_grade_body tr').each(function (index) {
                        let idRecord = $(this).find('.delete-record').attr('data-id');
                        let thisIndex = response.pagrade[index];
                        fillLevel(idRecord, thisIndex.id_pa_question, thisIndex.id_minimum_score_level, thisIndex.value);
                        $(this).find('.status_input').val(thisIndex.status).trigger('change');
                    });

                    $('#id_job_grade').val(response.id_job_grade).trigger('change');
                    $('#description').val(response.description).trigger('change');
                   
					// callerStart(response);                   					
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

            $('#modal_form_grade').modal('show');
        });

    });

	function get_start(response){
		return new Promise((resolve,reject)=>{
			 setTimeout(function () {
					$('#table_grade_body tr').each(function (index) {
						$(this).find('span.sn').html(index + 1);
						$(this).find('.id_grade_question_input').val(response.pagrade[index].id_grade_question).trigger('change');
						$(this).find('.id_pa_question_input').val(response.pagrade[index].id_pa_question).trigger('change');							
						$(this).find('.status_input').val(response.pagrade[index].status).trigger('change');
					});		
				resolve();
		       }, 50);	
				
		});
	}

	async function callerStart(response){
		await get_start(response);
			setTimeout(function () {
				$('#table_grade_body tr').each(function (index) {
					$(this).find('.id_minimum_score_level_input').val(response.pagrade[index].id_minimum_score_level).trigger('change');
					$(this).find('.value_input').val(response.pagrade[index].value).trigger('change');
				});    
			}, 5000);				
	}

$(document).ready(function(){
	
    $('#grade_table').DataTable({
        processing: true,
		responsive: true,
        ajax: {
			url: "{{ route('pa_grade.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#grade_table').DataTable().ajax.reload();
				}
			},
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_job_grade',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'description', name: 'description' },
			{ data: 'action', name: 'action', orderable: false, render: function ( data, type, row ) {	
					return data;
				} 
			},
        ]
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
	
	refresh_data();

});

function refresh_data() {
    /**************** Load Menu dropdown **************************/ 		
	// get_question();	
    getQuestion().then(function(res) {
        listQuestion = res;
    });
    getLevel(null).then(function(res) {
        $.each(res, function (i, item) {
            if(!listLevel.hasOwnProperty(item.id_pa_question)){
                listLevel[item.id_pa_question] = [];
            }
            listLevel[item.id_pa_question].push({'id':item.id, 'text':item.text, 'weight_score':item.weight_score});
        });
    });

    //    $('#grade_table').DataTable().ajax.reload();
}
function get_question() {
	$.getJSON('<?= url('kpi/kpi_settings/master_grade_question/get_question') ?>', function (data) {
			global_id_question = data;
		}).fail(function (data) { // Call failed
            get_question();
        });	
}

function get_level(id_pa_question,element) {
	$.getJSON('<?= url('kpi/kpi_settings/master_grade_question/get_level') . '?id_pa_question=' ?>' + id_pa_question, function (data) {
			element.find('.id_minimum_score_level_input').empty();
			element.find('.id_minimum_score_level_input').prepend('<option selected></option>').select2({
				placeholder: "Select Level",
				allowClear: true,
                data: data
            }).on('change', function (e) {				
			//	console.log($(this).select2('data')[0].weight_score);
				element.find('.value_input').val($(this).select2('data')[0].weight_score).trigger('change');
            }).trigger('change');
		}).fail(function (data) { // Call failed
            get_level(id_pa_question,element);
        });	
}

function genquestion(){
	$.getJSON('<?= url('kpi/kpi_settings/master_grade_question/get_question') ?>', function (data) {			
			$.each(data, function (i, item) {
				$('#new_grade_detail').trigger('click');
			});
			$('#table_grade_body tr').each(function (index) {
				$(this).find('.id_pa_question_input').val(data[index].id).trigger('change');
			}); 	  
		}).done(function (data) {
       		$('#genquest').attr('disabled',true);
        })
}

const getQuestion = async () => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('kpi/kpi_settings/master_grade_question/get_question') ?>',
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
        getQuestion();
    }
}

const getLevel = async (id_pa_question) => {
    let result;
    try {
        result = await $.ajax({
            url: '<?= url('kpi/kpi_settings/master_grade_question/get_level') ?>',
            data: {id_pa_question:id_pa_question},
            dataType: 'json',
            success: function (res) {
            }
        });
        return result;
    } catch (error) {
        getLevel(id_pa_question);
    }
}

const fillLevel = async (idRecord, id_pa_question, id_level=null, score=null) => {
    let result;
    try {
        let thisIdLevel = `#pagrade_${idRecord}_id_minimum_score_level`;
        let thisIdPaQuestion = `#pagrade_${idRecord}_id_pa_question`;

        $(thisIdLevel).html('');
        $(thisIdPaQuestion).val(id_pa_question).trigger('change', ['trigger']);

        if(id_pa_question != null){
            $(thisIdLevel).select2({
                placeholder: "Select Level",
                allowClear: true,
                data: listLevel[id_pa_question]
            });
        }
        if(id_level != null){
            $(thisIdLevel).val(id_level).trigger('change', ['trigger']);
        } 
        if(score == null && id_pa_question != null){
            score = $(thisIdLevel).select2('data')[0].weight_score;
        }
        fillScore(idRecord, id_level, score);
    } catch (error) {
        
    }
}

const fillScore = async (idRecord, id_level, score=null) => {
    let result;
    try {
        let thisScore = `#pagrade_${idRecord}_value`;
        $(thisScore).val('');
        if(score != null){
            $(thisScore).val(score);
        }
    } catch (error) {
        
    }
}

$(document).on('change', '.id_pa_question_input', function (event, triggered) {
    let id_record = $(this).parent().parent().attr('id_record');
    let id_pa_question = null;

    if (triggered != 'trigger'){ // change by human
        if($(this).select2('data').length > 0){
            id_pa_question = $(this).val();
        }
        fillLevel(id_record, id_pa_question);
    }
});

$(document).on('change', '.id_minimum_score_level_input', function (e, triggered) {
    let id_level = $(this).val();
    let id_record = $(this).parent().parent().attr('id_record');
    let valueScore = null;

    if (triggered != 'trigger'){ // change by human
        if($(this).select2('data').length > 0){
            valueScore = $(this).select2('data')[0].weight_score;
        }
        fillScore(id_record, id_level, valueScore);
    }
});

</script>
@endsection