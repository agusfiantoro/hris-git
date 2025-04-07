@extends('adminlte::page')
@section('title', 'Content')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Content</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add Content</button>
                </div>
            </div>
       
			<div class="card-body">
				<button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
					<br>
					<br>
					<table id="content_table" class="table table-striped table-bordered table-hover datatable">
					 <thead>
					  <tr>		
                        <th></th>
						<th></th>
						<th>No</th>
						<th>Type</th>
						<th>Link</th>
						<th>Content Name</th>
						<th>Status</th>
						<th>Company</th>
						<th data-priority="2" style="text-align:center;" width=100>Action</th>
					  </tr>
					 </thead>
					</table>
				</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_content"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="contentForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Form Content</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Content Name</label>
								<div class="col-sm-8">
                                    <input type="hidden" name="id_content_learning" id="id_content_learning" class="form-control form-control-sm">
                                    <input type="text" name="content_name" id="content_name" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="content_nameError">
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
							<div class="row link">
                                <label class="col-sm-4 col-form-label">Link</label>
								<div class="col-sm-8">
                                    <input type="text" name="link" id="link" class="form-control form-control-sm">
									<span class="invalid-feedback" role="alert" id="linkError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Content Type</label>
								<div class="col-sm-8">
                                    <select name="content_type" id="content_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="content_typeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Status</label>
                                <div class="col-sm-8">
                                    <select name="status" id="status" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="statusError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
					</div>
                    <div class="row tab">
                        <div class="col-md-12">
			            <hr/>
                            <ul class="nav nav-tabs" id="tab_menu_detail" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="link_tab_description" data-toggle="tab" href="#tab_detail_description" role="tab" aria-controls="link_tab_description" aria-selected="true">Description Content<span class="error-tab text-red"></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="link_tab_attachment" data-toggle="tab" href="#tab_detail_attachment" role="tab" aria-controls="link_tab_attachment" aria-selected="true">Attachment Content<span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="" style="font-size:12px">
                                <div class="tab-pane fade active show" id="tab_detail_description" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <label class="col-sm-2 col-form-label">Description</label>
                                        <div class="col-sm-10">
                                            <div class="form-group">
                                                <textarea class="summernote" name="description" id="description"></textarea>
                                                <span class="invalid-feedback" role="alert" id="descriptionError">
                                                    <strong></strong>
                                                </span>        
                                            </div> 
                                        </div> 
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="tab_detail_attachment" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_attachment"><span class="fas fa-plus"></span> Add Attachment</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_attachment_detail" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="white-space:nowrap;">No.</th>
                                                        <th style="white-space:nowrap;">Attachment (.pdf)</th>
                                                        <th style="white-space:nowrap;">Status</th>
                                                        <th style="white-space:nowrap;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_attachment_detailError">
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
                    <button type="button" onclick="on_close_modal()" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
               
                </div>
            </form>
			<div style="display:none;">
                <table id="sample_table_attachment">
                    <tr id="">
                        <td>
							<span class="sn text-center" style="vertical-align:middle;"></span>
						</td>
                        <td>
								<input name="content_attachment[0][id_attachment_content]" id="content_attachment_0_id_attachment_content" type="hidden" class="form-control form-control-sm id_attachment_input">
                                <input name="content_attachment[0][attachment]" id="content_attachment_0_attachment" type="file" class="form-control form-control-sm attachment_input" style="height: 37px;">
                                <span class="invalid-feedback attachment_input_error" role="alert" id="content_attachment_0_attachmentError">
                                    <strong></strong>
                                </span>					
                        </td>
                        <td>
                                <select name="content_attachment[0][status_attachment]" id="content_attachment_0_status_attachment" class="form-control form-control-sm select2 status_attachment_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback status_attachment_input_error" role="alert" id="content_attachment_0_status_attachmentError">
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

<div id="loadingModal" class="loading fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">              
            </div>
            <div class="modal-body">
               <i class="fa fa-refresh fa-pulse"></i>
            </div>
        </div>
    </div>
</div>

@endsection

@section('css')
<style type="text/css">	
li.select2-results__option strong.select2-results__group:hover {
  background-color: #6a6a6a;
  color:#fff;
  cursor: pointer;
}
</style>
@stop

@section('scripts')
<script type="text/javascript">
    let global_id_content_learning = "";
    let global_id_attachment = 0;
    let x = [];
    let today = new Date().toISOString().slice(0, 10)
    let status = [
        {   id: 'A',
            text: 'Active'  },
        {   id: 'I',
            text: 'Inactive'},
    ];
    let content_type = [
        {   id: 'Description',
            text: 'Description' },
        {   id: 'Presentation',
            text: 'Presentation' },
        // {   id: 'Video',
        //     text: 'Video' },
        {   id: 'Link',
            text: 'Link' },
    ];

    $(function () {	
		$('.summernote').summernote({
            height:300,
        });

    	$(document).on('click', '.new', function () {
            run_in_modal()
            global_id_content_learning = "";
            $('.summernote').summernote('reset');
            $("#contentForm")[0].reset();
            $("#company").val('').trigger('change');
            $("#table_body").html("");
            $("#contentForm .modal-title").html("<span class='fas fa-plus'></span> Form Content");
            $(".invalid-feedback").children("strong").text("");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#contentForm input").removeClass("is-invalid");
    		$('#save_button').attr('class', 'btn btn-sm btn-success');
            $('#save_button').html('<i class="fas fa-save"></i> Save');			

            $('#modal_form_content').modal('show');
        });
    		
    	$('#contentForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#contentForm')[0]);

            $(".invalid-feedback").children("strong").text("");
            $("#contentForm input").removeClass("is-invalid");
    		$(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: {
                    Accept: "application/json",
                },
                contentType:false,
                cache: false,
                processData:false,
    			url: global_id_content_learning == '' ? "{{ route('content_attachment.save') }}" : "{{ route('content_attachment.update') }}",
                data: formData,
    			beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    if (response.status == 'true') {
    					 $('#modal_form_content').modal('hide');
                        swal({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        }).then(ok => {
                            window.location.reload();
    					});
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
                    $('#loader').addClass('hidden')
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
    							$("#tab_menu_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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

        $(document).on('click', '#new_attachment', function () {
            var content = jQuery('#sample_table_attachment tr'),
                    size = global_id_attachment++,
                    element = null,
                    element = content.clone();
            element.attr('id','rec-'+size);
            element.find('.delete-record').attr('data-id', size);
    		
    		element.find('.id_attachment_input').attr('id', 'content_attachment_' + size + '_id_attachment_content');
            element.find('.id_attachment_input').attr('name', 'content_attachment[' + size + '][id_attachment_content]');

    		element.find('.attachment_input').attr('id', 'content_attachment_' + size + '_attachment');
            element.find('.attachment_input').attr('name', 'content_attachment[' + size + '][attachment]');
            element.find('.attachment_input_error').attr('id', 'content_attachment_' + size + '_attachmentError');

    		element.find('.status_attachment_input').attr('id', 'content_attachment_' + size + '_status_attachment');
            element.find('.status_attachment_input').attr('name', 'content_attachment[' + size + '][status_attachment]');
            element.find('.status_attachment_input_error').attr('id', 'content_attachment_' + size + '_status_attachmentError');
            element.find('.status_attachment_input').select2({
                placeholder: "Select Status",
                allowClear: true,
                data: status
            });
    		
            element.appendTo('#table_body');
    		$('#table_body tr').each(function (index) {
                $(this).find('td:eq(0)').addClass('text-center');
                $(this).find('span.sn').html(index + 1);
            });
        });

    	$(document).on('click', '.delete-record', function () {
            var id = jQuery(this).attr('data-id');
            var targetDiv = jQuery(this).attr('targetDiv');
            jQuery('#rec-' + id).remove();
            $('#table_body tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
            });
            return true;
        });

        $(document).on('click', '.edit', function () {
            run_in_modal()

            let id_content_learning = $(this).attr('id');
            global_id_content_learning = id_content_learning;
            $("#contentForm")[0].reset();
            $("#table_body").html("");
            $("#contentForm .modal-title").html("<span class='fas fa-edit'></span> Form Content");
            $(".invalid-feedback").children("strong").text("");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $("#contentForm input").removeClass("is-invalid");
            $('#save_button').attr('class', 'btn btn-sm btn-primary');
            $('#save_button').html('<i class="fas fa-edit"></i> Update');

            $.ajax({
                url: "{{ route('content_attachment.get_content_learning') }}",
                method: "GET",
                data: {id_content_learning: id_content_learning},
    			beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    global_id_attachment = 0;			
                    if(response.attachment.length > 0){
                        $.each(response.attachment, function (i, item) {
                            $('#new_attachment').trigger('click');
                        });
                    }		
                    
                    $('#id_content_learning').val(response.id_content_learning).trigger('change');
                    $('#content_name').val(response.content_name).trigger('change');
                    $('#notes').val(response.notes).trigger('change');
                    $('#link').val(response.link).trigger('change');
                    $('#content_type').val(response.content_attachment_type).trigger('change');
                    $('#status').val(response.status).trigger('change');
                    $("#description").summernote("code", response.description);

                    $('#table_body tr').each(function (index) {
                        if(response.attachment.length > 0){
                            let pathFile = '<?=url("learning_management/content_file")?>/'+response.id_content_learning+'/'+response.attachment[index].attachment;
                            let id_attachment = `<input type="hidden" class="id_attachment_input" value="${response.attachment[index].id_attachment_content}" name="content_attachment[${index}][id_attachment_content]" />`;
                            let attachment = `<a href="${pathFile}" target="_blank">${response.attachment[index].attachment}</a>`;

                            $(this).find('span.sn').html(index + 1);
                            $(this).children('td:eq(1)').html(id_attachment + attachment).css('font-size','15px');
                            $(this).find(`#content_attachment_${index}_status_attachment`).val(response.attachment[index].status).trigger('change');
                        }
                    });
    			},
                complete: function(){
                    $('#loader').addClass('hidden')
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
    	
            $('#modal_form_content').modal('show');
        });

        $('#content_type').on('change',function(){
            let type = $(this).val();
            switch_content(type);
        })

    });
    
    $(document).on('change', '.attachment_input', function () {
        const file = this.files[0];
        if (file) {
            const size = file.size / 1000;
            const maxSize = 6500;
            if(size > maxSize){
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: `File must not be greater than 6 MB`
                });
                $(this).val('');
                return false;
            }

            if(file.type != 'application/pdf'){
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: `Please upload PDF format file`
                });
                $(this).val('');
                return false;
            }
        }
    });

    $(document).ready(function(){

        $('#content_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
    		    url: "{{ route('content.index') }}",
    		    error: function (jqXHR, textStatus, errorThrown) {
    					$('#content_table').DataTable().ajax.reload();
    				}
    		  },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
    			{   // Checkbox select column
                    data: 'id_content_learning',
                    defaultContent: '',
                    orderable: false
                },
    			{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
    			{ data: 'content_attachment_type', name: 'content_attachment_type' },
                { data: 'link', name: 'link', 
                    render: function ( data, type, row ) {  
                        if(row.content_attachment_type=='Link'){
                            return data;
                        } else {
                            return '';
                        }
                    } 
                },
    			{ data: 'content_name', name: 'content_name' },
    			{ data: 'status', name: 'status' },
    			{ data: 'company', name: 'company' },
    			{ data: 'action', name: 'action', orderable: false },
            ],
            "fnInitComplete": function (oSettings) {
                $('#content_table_wrapper .column-filter-widget:eq(3)').find("select option:contains('link')").attr('selected','selected').change();
            }
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
    	
        $(document).on('click', '.delete', function (event) {
        	id_content_learning = $(this).attr('id');
            event.preventDefault();
            swal({
                title: 'Are you sure?',
                text: 'This record and it`s details will be permanently deleted!',
                icon: 'warning',
                buttons: true,
        		dangerMode: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
        		confirmButtonText: 'Yes, delete it!'
            }).then(function(value) {
                if (value) {
                    $.ajax({
                        url: "{{ route('content_attachment.destroy') }}",
                        headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                        method: "POST",
                        data: {id_content_learning: id_content_learning},
        			    success:function(data)
                        {
        				    setTimeout(function(){
        				    $('#confirmModal').modal('hide');
        				    swal({
            					title: "Data Deleted!",
            					icon: "success",
            					buttons: {confirm : {className:'btn-success'},},
            					}).then(ok => {
                                    window.location.reload();
            				    });
        				    }, 50);
                        }
                    })
                }
            });
        });
    });

    function switch_content(type) {
        if(type==''){
            $('.link').hide();
            $('.tab').hide();
            $('#table_body').html('');
        } else if(type=='Description'){
            $('.link').hide();
            $('.tab').show();
            $('#link_tab_description').addClass('active').show();
            $('#tab_detail_description').addClass('active show').show();
            $('#link_tab_attachment').removeClass('active').hide();
            $('#tab_detail_attachment').removeClass('active show').hide();
            $('#table_body').html('');
        } else if(type=='Presentation'){
            if($('#table_body').html() == ''){
                $('#table_body').html('');
                $('#new_attachment').trigger('click');
            }
            $('.link').hide();
            $('.tab').show();
            $('#link_tab_description').removeClass('active').hide();
            $('#tab_detail_description').removeClass('active show').hide();
            $('#link_tab_attachment').addClass('active').show();;
            $('#tab_detail_attachment').addClass('active show').show();
        } else {
            $('.link').show();
            $('.tab').hide();
            $('#table_body').html('');
        }
    }

    function run_in_modal() {
        /**************** Load Menu dropdown **************************/
		$('#status').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });	
        $('#content_type').prepend('<option selected></option>').select2({
            placeholder: "Select Content Type",
            allowClear: true,
            data: content_type,
        });
        let type = $('#content_type').find("option:first-child").val();
        switch_content(type);
    }

    function on_close_modal() {
        $('#content_table').DataTable().ajax.reload();
    }

</script>
@endsection