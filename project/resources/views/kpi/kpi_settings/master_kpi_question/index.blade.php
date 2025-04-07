@extends('adminlte::page')
@section('title', 'Master Question Answer')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Master Question Answer</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Question Answer</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="question_table" style="width:100%;" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>				   
						<th></th>
						<th></th>
						<th>No</th>
						<th>Sequence</th>
						<th>Question</th>
						<th>Type</th>
						<th>Question Group</th>
						<th>Answer</th>
						<th>Status</th>
						<th data-priority="1" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_question"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="questionForm">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Question Answer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
						<div class="col-md-6">                       
							<div class="row">
                                <label class="col-sm-4 col-form-label">Sequence</label>
                                <div class="col-sm-8">
									<input name="id_pa_question" id="id_pa_question" type="hidden">	
                                    <input type="text" name="sequence" id="sequence" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="sequenceError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
								<label class="col-sm-4 col-form-label">Question Type</label>
								<div class="col-sm-8">
									<select name="id_question_type" id="id_question_type" class="form-control form-control-sm select2" style="width: 100%;">
									</select>
									<span class="invalid-feedback" role="alert" id="id_question_typeError">
										<strong></strong>
									</span>
								</div>
							</div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Question Group</label>
                                <div class="col-sm-8">
									<select name="id_question_group" id="id_question_group" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_question_groupError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>																							
                        </div>
						<div class="col-md-6">							
							<div class="row">
                                <label class="col-sm-4 col-form-label">Type</label>
                                <div class="col-sm-8">
                                    <select name="question_pa_type" id="question_pa_type" class="form-control form-control-sm select2" style="width:100%;"> </select>
                                    <span class="invalid-feedback" role="alert" id="question_pa_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notes" id="notes" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="notesError">
                                        <strong></strong>
                                    </span>   
									
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width:100%;">
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
								<label class="col-sm-2 col-form-label">Question</label>
								<div class="col-sm-10">
									<div class="form-group">
										<textarea class="summernote" name="description" id="description"></textarea>
									</div> 
									<span class="feedback" style="color:#dc3545;font-size:11px;" role="alert" id="descriptionError">
										<strong></strong>
									</span>									
								</div> 
							</div>		
						</div>			
					</div>
					<hr/>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs" id="tab_answer_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_answer-details" data-toggle="pill" href="#answer-details" role="tab" aria-controls="link_tab_answer-details" aria-selected="true">Answer <span class="error-tab text-red"></span></a>
                                </li>
                             
                            </ul>
                            <div class="tab-content" id="tab_answer_detail_content" style="font-size:12px">
                                <div class="tab-pane fade show active" id="answer-details" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_answer_detail"><span class="fas fa-plus"></span> Add Answer</button>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="table_answer_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Answer</th>
                                                        <th style="white-space:nowrap;">Description</th>
                                                        <th style="white-space:nowrap;">Weight Score</th>
                                                        <th style="white-space:nowrap;">Corrected Answer</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_answer_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_answer_detailError">
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
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			     <div style="display:none;">
                <table id="sample_table_answer">
                    <tr id="">
                        <td><span class="sn" style="vertical-align:middle;"></span></td>
                        <td>
								<input name="answer[0][id_pa_answer]" id="answer_0_id_pa_answer" type="hidden" class="form-control form-control-sm id_pa_answer_input">		
                                <input type="text" name="answer[0][sequence]" id="answer_0_sequence" class="form-control form-control-sm sequence_input">
                                <span class="invalid-feedback sequence_input_error" role="alert" id="answer_0_sequenceError">
                                    <strong></strong>
                                </span>						
                        </td>
                        <td>
                                <input type="text" name="answer[0][desc_answer]" id="answer_0_desc_answer" class="form-control form-control-sm desc_answer_input">
                                <span class="invalid-feedback desc_answer_input_error" role="alert" id="answer_0_desc_answerError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>
                                <input type="text" name="answer[0][weight_score]" id="answer_0_weight_score" class="form-control form-control-sm weight_score_input" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
                                <span class="invalid-feedback weight_score_input_error" role="alert" id="answer_0_weight_scoreError">
                                    <strong></strong>
                                </span>
                        </td>
						<td>
                            <input type="checkbox" name="answer[0][is_corrected_answer]" id="answer_0_is_corrected_answer" class="form-control form-control-sm is_corrected_answer_input" style="height:20px;margin-top:5px;">
                            <span class="invalid-feedback is_corrected_answer_input_error" role="alert" id="answer_0_is_corrected_answerError">
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
let global_id_question = "";
let global_id_answer = 0;
let global_address_menu = [];

    $(function () {	
			
		$(document).on('click', '.new', function () {
            global_id_question = "";
			$('.summernote').summernote('reset');
            $("#questionForm")[0].reset();
            $("#id_question_type").empty();
            $("#id_question_group").empty();
            $("#question_pa_type").empty();
            $("#table_answer_body").html("");
            $("#questionForm .modal-title").html("<span class='fas fa-plus'></span> Form Question Answer");
            $(".invalid-feedback").children("strong").text("");
            $(".feedback").children("strong").text("");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#questionForm input").removeClass("is-invalid");
			$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');
				$('#question_pa_type').select2({
					 data: question_pa_type,
				});	
				get_question_type();	
				get_question_group();	
            $('#modal_form_question').modal('show');
        });
		
		 $('#questionForm').submit(function (e) {
            e.preventDefault();
            let formData = $(this).serializeArray();			
			if(formData[7].value == "<br>" || formData[7].value == "<p><br></p>"){
				formData.splice(7);
				formData.push({name:'description',value:''});
			}
            $(".invalid-feedback").children("strong").text("");
			$(".feedback").children("strong").text("");
			$(".note-editor").attr("style","border-color:none");
            $("#questionForm input").removeClass("is-invalid");
			$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
                $.ajax({
                    type: 'POST',
                    headers: {
                        Accept: "application/json",
                    },
					url: global_id_question == '' ? "{{ route('pa_question.save') }}" : "{{ route('pa_question.update') }}",
                    data: formData,
                    success: function (response) {
                        if (response.status == 'true') {
							 $('#modal_form_question').modal('hide');
                            swal({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
	                        $('#question_table').DataTable().ajax.reload();
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
								if(key_temp == 'description'){
	                                $(".note-editor").css('border-color','#dc3545');
								}
								 var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
								if (tab_id != undefined) {
									$("#tab_answer_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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


        $(document).on('click', '#new_answer_detail', function () {
            var content = jQuery('#sample_table_answer tr'),
                    size = global_id_answer++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
            element.find('.id_pa_answer_input').attr('id', 'answer_' + size + '_id_pa_answer');
            element.find('.id_pa_answer_input').attr('name', 'answer[' + size + '][id_pa_answer]');
			
			element.find('.sequence_input').attr('id', 'answer_' + size + '_sequence');
            element.find('.sequence_input').attr('name', 'answer[' + size + '][sequence]');
            element.find('.sequence_input_error').attr('id', 'answer_' + size + '_sequenceError');
			
			element.find('.desc_answer_input').attr('id', 'answer_' + size + '_desc_answer');
            element.find('.desc_answer_input').attr('name', 'answer[' + size + '][desc_answer]');
            element.find('.desc_answer_input_error').attr('id', 'answer_' + size + '_desc_answerError');
			
			element.find('.weight_score_input').attr('id', 'answer_' + size + '_weight_score');
            element.find('.weight_score_input').attr('name', 'answer[' + size + '][weight_score]');
            element.find('.weight_score_input_error').attr('id', 'answer_' + size + '_weight_scoreError');
			
			element.find('.is_corrected_answer_input').attr('id', 'answer_' + size + '_is_corrected_answer');
            element.find('.is_corrected_answer_input').attr('name', 'answer[' + size + '][is_corrected_answer]');
            element.find('.is_corrected_answer_input_error').attr('id', 'answer_' + size + '_is_corrected_answerError');
			
            element.appendTo('#table_answer_body');
			 $('#table_answer_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
        });

	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_answer_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

  $(document).on('click', '.edit', function () {
            let id_pa_question = $(this).attr('id');
            global_id_question = id_pa_question;
            $("#questionForm")[0].reset();
			$("#id_question_type").empty();
            $("#id_question_group").empty();
            $("#question_pa_type").empty();
            $("#table_answer_body").html("");
            $("#questionForm .modal-title").html("<span class='fas fa-edit'></span> Edit Question Answer");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#questionForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');
				$('#question_pa_type').select2({
					 data: question_pa_type,
				});	
				get_question_type();	
				get_question_group();	
		
            $.ajax({
                url: "<?= url('kpi/kpi_settings/master_kpi_question/get_question_edit') ?>",
                method: "GET",
                data: {id_pa_question: id_pa_question},
                success: function (response) {
                    global_id_answer = 0;
					 $.each(response.answer, function (i, item) {
                        $('#new_answer_detail').trigger('click');
                    });
                    
                    $('#id_pa_question').val(response.id_pa_question).trigger('change');
                    $('#sequence').val(response.sequence).trigger('change');
					$("#description").summernote("code", response.description);
                    $('#id_question_type').val(response.id_question_type).trigger('change');
                    $('#question_pa_type').val(response.question_pa_type).trigger('change');
                    $('#id_question_group').val(response.id_question_group).trigger('change');
                    $('#notes').val(response.notes).trigger('change');
                    $('#id_company').val(response.id_company).trigger('change');
                    $('#status').val(response.status).trigger('change');

                    setTimeout(function () {
                        $('#table_answer_body tr').each(function (index) {
                            $(this).find('span.sn').html(index + 1);
                            $(this).find('.id_pa_answer_input').val(response.answer[index].id_pa_answer).trigger('change');
                            $(this).find('.sequence_input').val(response.answer[index].sequence);
                            $(this).find('.desc_answer_input').val(response.answer[index].desc_answer);
                            $(this).find('.weight_score_input').val(response.answer[index].weight_score).trigger('change');
							if (response.answer[index].is_corrected_answer == 1) {
                                $(this).find('.is_corrected_answer_input').prop('checked', true);
                            } else {
                                $(this).find('.is_corrected_answer_input').prop('checked', false);
                            }							                            
                        });                       
                    }, 500);
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

            $('#modal_form_question').modal('show');
        });

    });

$(document).ready(function(){
	
    $('#question_table').DataTable({
        processing: true,
		responsive: true,
        ajax: {
			url: "{{ route('pa_question.index') }}",
		    error: function (jqXHR, textStatus, errorThrown) {
					$('#question_table').DataTable().ajax.reload();
				}
			},
        columns: [
			{
			defaultContent: '',
			orderable: false,
			},
			{   // Checkbox select column
			data: 'id_pa_question',
			defaultContent: '',
			orderable: false
			},
			{ data: 'DT_RowIndex', name: 'DT_RowIndex'},
            { data: 'sequence', name: 'sequence' },
            { data: 'description', name: 'description', render: function ( data, type, row ) {		
				let val = $("<span>").html(data).text();
				let dat = val && val.length > 100 ? val.slice(0,100).split(' ').slice(0, -1).join(' ')+' . . .' : val;
				return dat;
				} 
			},
            { data: 'question_pa_type', name: 'question_pa_type' },
            { data: 'question_group', name: 'question_group' },
            { data: 'answer', name: 'answer' },
            { data: 'status', name: 'status' },
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

	
$(document).on('click', '.delete', function (event) {
	id_pa_question = $(this).attr('id');
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
			   url:"master_kpi_question/destroy/"+id_pa_question,
			   success:function(data){
				    if (data.status == 'true') {
						setTimeout(function(){
							 $('#confirmModal').modal('hide');
							 $('#question_table').DataTable().ajax.reload();				 
							 swal({
								title: "Data Deleted!",
								  icon: "success",
								   buttons: {confirm : {className:'btn-success'},},
								}).then(ok => {
							});
							}, 50);
                        } else {
                            swal({
                                icon: 'error',
                                title: 'Oops...',
                                dangerMode: true,
                                text: data.message
                            });
                        }			
			   }
			  })
        }
    });
});

function refresh_data() {
		
		question_pa_type = [
			{
				id: 'QUALITATIVE',
				text: 'QUALITATIVE'
			},
			{
				id: 'QUANTITATIVE',
				text: 'QUANTITATIVE'
			},
		];
		
		$('#status').select2();		
		$('.summernote').summernote({
			height:100,
		});
        /**************** Load Menu dropdown **************************/     
		
    //    $('#question_table').DataTable().ajax.reload();
}
function get_question_type() {
	$.getJSON('<?= url('kpi/kpi_settings/master_kpi_question/get_question_type') ?>', function (data) {
			$('#id_question_type').select2({
				data: data,
			});
		}).fail(function (data) { // Call failed
            get_question_type();
        });	
}

function get_question_group() {
	$.getJSON('<?= url('kpi/kpi_settings/master_kpi_question/get_question_group') ?>', function (data) {
			$('#id_question_group').select2({
				data: data,
			});
		}).fail(function (data) { // Call failed
            get_question_group();
        });	
}
</script>
@endsection