@extends('adminlte::page')
@section('title', 'Kpi Onboarding')

@section('content')

<div class="row">
    <div class="col-12">
        <!-- Default box -->
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h5 class="card-title">Kpi Onboarding</h5>
                <div class="card-tools">
                    <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Create KPI</button>
                </div>
            </div>
       
			<div class="card-body">
                <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
				<br>
				<br>
				<table id="kpi_table" class="table table-striped table-bordered table-hover datatable">
				</table>
			</div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal_form_event"  data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <form method="post" id="kpiForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="modal-header">
                    <h5 class="modal-title">Create KPI</h5>
                    <button type="button" onclick="on_close_modal()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">						
						<div class="col-md-6">
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Category</label>
                                <div class="col-sm-8">
                                    <select name="process_type" id="process_type" class="form-control form-control-sm select2" style="width: 100%;">
                                    </select>
                                </div>
                            </div>
							<div class="row">
                                <label class="col-sm-4 col-form-label">Employee</label>
                                <div class="col-sm-8">
                                    <input type="hidden" name="id_kpi_group" id="id_kpi_group" class="form-control form-control-sm">
                                    <select name="id_employee" id="id_employee" class="form-control form-control-sm select2 get_employee_attendee" style="width: 100%;">
                                    </select>
                                    <span class="invalid-feedback" role="alert" id="id_employeeError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Average KPI (%)</label>
                                <div class="col-sm-8">
                                    <input type="number" name="average_prosentase" id="average_prosentase" class="form-control form-control-sm" autocomplete="off" readonly>
                                    <span class="invalid-feedback" role="alert" id="average_prosentaseError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Description</label>
                                <div class="col-sm-8">
                                    <input type="text" name="description" id="description" class="form-control form-control-sm" autocomplete="off">
                                    <span class="invalid-feedback" role="alert" id="descriptionError">
                                        <strong></strong>
                                    </span>
                                </div>
                            </div>
						</div>
						<div class="col-md-6">
							<div class="row">
                                <label class="col-sm-4 col-form-label">Start Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm datepicker" autocomplete="off">
                                    <span class="invalid-feedback" role="alert" id="start_dateError">
                                        <strong></strong>
                                    </span>                                    
                                </div>
                            </div>
                            <div class="row">
                                <label class="col-sm-4 col-form-label">End Date</label>
                                <div class="col-sm-8">
                                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm datepicker" autocomplete="off">
                                    <span class="invalid-feedback" role="alert" id="end_dateError">
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
                            <div class="row">
                                <label class="col-sm-4 col-form-label">Notes</label>
                                <div class="col-sm-8">
                                    <input type="text" name="notes" id="notes" class="form-control form-control-sm">
                                    <span class="invalid-feedback" role="alert" id="notesError">
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
                                    <a class="nav-link active" id="link_tab_kpi_summary" data-toggle="tab" href="#tab_kpi_summary" role="tab" aria-controls="link_tab_kpi_summary" aria-selected="true">KPI Summary Month<span class="error-tab text-red"></span></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="link_tab_kpi_detail" data-toggle="tab" href="#tab_kpi_detail" role="tab" aria-controls="link_tab_kpi_detail" aria-selected="true">KPI Detail<span class="error-tab text-red"></span></a>
                                </li>
                            </ul>
                            <div class="tab-content" id="" style="font-size:12px">
                                <div class="tab-pane fade active show" id="tab_kpi_summary" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_summary"><span class="fas fa-plus"></span> Add Kpi Summary</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_summary" class="table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th style="">No.</th>
                                                        <th style="width: 150px;">Month</th>
                                                        <th style="">Notes</th>
                                                        <th style="white-space:nowrap;">Subtotal KPI/Month</th>
                                                        <th style="white-space:nowrap;">Status</th>
                                                        <th style="">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body_summary">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_summaryError">
                                                    <strong></strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade " id="tab_kpi_detail" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                    <br/>
                                    <div class="row">
                                        <div class="col-md-12" style="margin-bottom: 10px">
                                            <button type="button" class="pull-right btn btn-xs btn-primary" id="new_detail"><span class="fas fa-plus"></span> Add Kpi Detail</button>
                                        </div>
                                        <div class="col-md-12" style="max-height:400px;overflow-y: scroll;">
                                            <table id="table_detail" class="display table table-striped table-bordered table-hover datatable">
                                                <thead>
                                                    <tr align="center">
                                                        <th>No.</th>
                                                        <th>Month</th>
                                                        <th style="white-space:nowrap;">KPI Category</th>
                                                        <th style="white-space:nowrap;">KPI Type</th>
                                                        <th style="white-space:nowrap;width:50px;">KPI Value</th>
                                                        <th style="white-space:nowrap;width:50px;">Weight Scale</th>
                                                        <th style="white-space:nowrap;">Course Name</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="table_body_detail">
                                                </tbody>
                                            </table>
                                            <div class="col-sm-12">
                                                <span class="table-invalid-feedback text-red" role="alert" id="table_detailError">
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
                <table id="sample_table_summary">
                    <tr id="">
                        <td>
							<span class="sn text-center" style="vertical-align:middle;"></span>
						</td>
                        <td>
								<input name="summary[0][id_kpi_header]" id="summary_0_id_kpi_header" type="hidden" class="form-control form-control-sm id_kpi_header_input">
                                <input name="summary[0][kpi_month]" id="summary_0_kpi_month" type="text" class="form-control form-control-sm kpi_month_input" autocomplete="off">
                                <span class="invalid-feedback kpi_month_input_error" role="alert" id="summary_0_kpi_monthError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                                <input name="summary[0][notes]" id="summary_0_notes" type="text" class="form-control form-control-sm notes_input">
                                <span class="invalid-feedback notes_input_error" role="alert" id="summary_0_notesError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
                                <input name="summary[0][subtotal_kpi]" id="summary_0_subtotal_kpi" type="number" class="form-control form-control-sm subtotal_kpi_input" readonly>
                                <span class="invalid-feedback subtotal_kpi_input_error" role="alert" id="summary_0_subtotal_kpiError">
                                    <strong></strong>
                                </span>                 
                        </td>
                        <td>
                                <select name="summary[0][status]" id="summary_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                                </select>
                                <span class="invalid-feedback status_input_error" role="alert" id="summary_0_statusError">
                                    <strong></strong>
                                </span>
                        </td>
                        <td>
							<center>
                                <a href="javascript:;" class="save_summary btn btn-xs btn-primary" data-id="0" title="Save Summary"><span class="fas fa-check" style="margin-bottom: 5px;"></span></a>
								<button type="button" class="delete_summary btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
							</center>
						</td>
                    </tr>
                </table>

                <table id="sample_table_detail">
                    <tr id="">
                        <td>
                            <span class="sn text-center" style="vertical-align:middle;"></span>
                        </td>
                        <td>
                            <input name="detail[0][id_kpi_detail]" id="detail_0_id_kpi_detail" type="hidden" class="form-control form-control-sm id_kpi_detail_input">
                            <select name="detail[0][kpi_month]" id="detail_0_kpi_month" class="form-control form-control-sm select2 kpi_month_input_detail" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback kpi_month_input_detail_error" role="alert" id="detail_0_kpi_monthError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <select name="detail[0][id_kpi_category]" id="detail_0_id_kpi_category" class="form-control form-control-sm select2 id_kpi_category_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback id_kpi_category_input_error" role="alert" id="detail_0_id_kpi_categoryError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <select name="detail[0][id_kpi_type]" id="detail_0_id_kpi_type" class="form-control form-control-sm select2 id_kpi_type_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback id_kpi_type_input_error" role="alert" id="detail_0_id_kpi_typeError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="detail[0][kpi_value]" id="detail_0_kpi_value" type="text" class="form-control form-control-sm kpi_value_input">
                            <span class="invalid-feedback kpi_value_input_error" role="alert" id="detail_0_kpi_valueError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <input name="detail[0][weight_prosentase]" id="detail_0_weight_prosentase" type="text" class="form-control form-control-sm weight_prosentase_input">
                            <span class="invalid-feedback weight_prosentase_input_error" role="alert" id="detail_0_weight_prosentaseError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <select name="detail[0][id_course_header]" id="detail_0_id_course_header" class="form-control form-control-sm select2 id_course_header_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback id_course_header_input_error" role="alert" id="detail_0_id_course_headerError">
                                <strong></strong>
                            </span>                 
                        </td>
                        <td>
                            <select name="detail[0][status]" id="detail_0_status" class="form-control form-control-sm select2 status_input" style="width: 100%;">
                            </select>
                            <span class="invalid-feedback status_input_error" role="alert" id="detail_0_statusError">
                                <strong></strong>
                            </span>
                        </td>
                        <td>
                            <center>
                                <button type="button" class="delete_detail btn btn-xs btn-danger" data-id="0"><span class="far fa-trash-alt"></span></button>
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
    let global_id_kpi_group = "";
    let global_id_kpi_header = "";
    let global_select_program = "";
    let global_select_employee = [];
    let global_select_employee_attendee = [];
    let global_select_month = [];
    let global_select_course = "";
    let global_select_kpi_category = "";
    let global_select_kpi_type = "";
    let global_select_checklist = "";
    let global_id_summary = 0;
    let global_id_detail = 0;
    let global_id_user_session = 0;
    let x = [];
    let status = [
        {id: 'A',text: 'Active'},
        {id: 'I',text: 'Inactive'},
    ];
    let process_type = [
        {id: 'Onboarding',text: 'New Hire'},
        {id: 'Acting',text: 'Acting'},
    ];
    let today = new Date().toISOString().slice(0, 10)

    $(function () {	
        bsCustomFileInput.init();

        $('.datepicker').each(function(){
            thisId = $(this).attr('id');
            if(thisId=='start_date' || thisId=='end_date'){
                $(`#${thisId}`).datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd',
                }).on("change", function(e) {
                    let str_start = moment($(`#start_date`).val(), 'YYYY-MM-DD').valueOf();
                    let str_end = moment($(`#end_date`).val(), 'YYYY-MM-DD').valueOf();

                    if(($(`#start_date`).val()!='' && $(`#end_date`).val()!='') && str_end < str_start){
                        $(`#start_date`).val(startDateAwal)
                        $(`#end_date`).val(endDateAwal)
                        swal({
                            icon: 'error',
                            title: '',
                            dangerMode: true,
                            text: 'Start date must be less than end date',
                            timer: 1800
                        });
                    }
                    startDateAwal = $(`#start_date`).val();
                    endDateAwal = $(`#end_date`).val();

                    if(startDateAwal != '' && endDateAwal != ''){
                        assignDetailDateByHeader(startDateAwal, endDateAwal)
                    }
                });
            } else {
                $(this).datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd',
                });
            }
        });

		$('.summernote').summernote({
            height:300,
        });

        $('#kpi_table').DataTable({
            processing: true,
            responsive: true,
            serverSide: true,
            destroy: true,
            scrollY: true,
            scrollX: true,
            pageLength: 10,
            ajax: {
                url: "{{ route('kpi_onboarding.index') }}",
                error: function (jqXHR, textStatus, errorThrown) {
                        $('#kpi_table').DataTable().ajax.reload();
                    }
              },
            columns: [
                {   // Detail Responsive
                    data: '',
                    defaultContent: '',
                    orderable: false
                },
                {   // Checkbox select column
                    data: 'id_kpi_group',
                    defaultContent: '',
                    orderable: false
                },
                { data: 'DT_RowIndex', title:'No'},
                { data: 'name', title:'Employee'},
                { data: 'average_prosentase' , title:'Average Prosentase'},
                { data: 'start_date', title:'Start Date'},
                { data: 'end_date' , title:'End Date'},
                { data: 'description', title:'Description'},
                { data: 'status', title:'Status'},
                { data: 'action', title:'Action', orderable: false, 
                    render: function ( data, type, row ) {  
                        let _edit = `<button type="button" id="${row.action}" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> `;
                        let _delete = `&nbsp;&nbsp;<button type="button" id="${row.action}" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>`;
                        return _edit + _delete;
                    } 
                },
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
        
        $('#kpiForm').submit(function (e) {
            e.preventDefault();
            let formData = new FormData($('#kpiForm')[0]);
            let urlForm = '';

            if(global_id_kpi_group == ''){
                urlForm = "{{ route('kpi_onboarding.save') }}";
            } else {
                urlForm = "{{ route('kpi_onboarding.update') }}";
                formData.append('id_kpi_group', global_id_kpi_group);
            }

            $(".invalid-feedback").children("strong").text("");
            $("#kpiForm input").removeClass("is-invalid");
            $(".table-invalid-feedback").children("strong").text("");
            $(".error-tab").html("");
            $.ajax({
                type: 'POST',
                headers: { Accept: "application/json", },
                contentType:false,
                cache: false,
                processData:false,
                url: urlForm,
                data: formData,
                beforeSend: function () {
                    $('#loader').removeClass('hidden');
                },
                success: function (response) {
                    if (response.status == 'true') {
                         $('#modal_form_event').modal('hide');
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
                            text: response.message
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

    });

    $(document).on('click', '.new', function () {
        run_in_modal()
        global_id_kpi_group = "";
        $("#kpiForm")[0].reset();
        $("#table_body_summary").html("");
        $("#table_body_detail").html("");
        $("#kpiForm .modal-title").html("<span class='fas fa-plus'></span> Create KPI");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#kpiForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-success');
        $('#save_button').html('<i class="fas fa-save"></i> Save');         
        $('#modal_form_event').modal('show');
    });
    

    $(document).on('click', '#new_summary', function () {
        let notifError = '';

        if($(`#start_date`).val()=='' || $(`#end_date`).val()==''){
            notifError = 'Please fill Start date & End date';
        } else {
            let str_start = moment($(`#start_date`).val(), 'YYYY-MM-DD').valueOf();
            let str_end = moment($(`#end_date`).val(), 'YYYY-MM-DD').valueOf();
            if(str_end < str_start){
                notifError = 'Start date must be less than end date';
            }
        }
        if(notifError!=''){
            swal({
                icon: 'error',
                title: '',
                dangerMode: true,
                text: notifError,
                timer: 1800
            });
            return false;
        }
        
        var content = jQuery('#sample_table_summary tr'),
            size = global_id_summary++,
            element = null,
            element = content.clone();
        element.attr('id','rec-summary-'+size);
        element.find('.delete_summary').attr('data-id', size);
        element.find('.save_summary').attr('data-id', size);

        element.find('.id_kpi_header_input').attr('id', 'summary_' + size + '_id_kpi_header');
        element.find('.id_kpi_header_input').attr('name', 'summary[' + size + '][id_kpi_header]');

        element.find('.kpi_month_input').attr('id', 'summary_' + size + '_kpi_month');
        element.find('.kpi_month_input').attr('name', 'summary[' + size + '][kpi_month]');
        element.find('.kpi_month_input_error').attr('id', 'summary_' + size + '_kpi_monthError');
        element.find('.kpi_month_input').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd',
            minDate: $(`#start_date`).val(), 
            maxDate: $(`#end_date`).val(), 
            autoclose: true
        });

        element.find('.notes_input').attr('id', 'summary_' + size + '_notes');
        element.find('.notes_input').attr('name', 'summary[' + size + '][notes]');
        element.find('.notes_input_error').attr('id', 'summary_' + size + '_notesError');

        element.find('.subtotal_kpi_input').attr('id', 'summary_' + size + '_subtotal_kpi');
        element.find('.subtotal_kpi_input').attr('name', 'summary[' + size + '][subtotal_kpi]');
        element.find('.subtotal_kpi_input_error').attr('id', 'summary_' + size + '_subtotal_kpiError');

        element.find('.status_input').attr('id', 'summary_' + size + '_status');
        element.find('.status_input').attr('name', 'summary[' + size + '][status]');
        element.find('.status_input_error').attr('id', 'summary_' + size + '_statusError');
        element.find('.status_input').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });
        
        element.appendTo('#table_body_summary');
        $('#table_body_summary tr').each(function (index) {
            $(this).find('td:eq(0)').addClass('text-center');
            $(this).find('span.sn').html(index + 1);
        });
    });

    $(document).on('click', '.delete_summary', function () {
        let counter = $(this).attr('data-id');
        let id_kpi_header = $(`#summary_${counter}_id_kpi_header`).val();
        destroy_summary(counter, id_kpi_header);
    });

    $(document).on('click', '.save_summary', function () {
        let counter = $(this).attr('data-id');
        save_summary(counter);
    });

    $(document).on('click', '#new_detail', function () {
        var content = jQuery('#sample_table_detail tr'),
                size = global_id_detail++,
                element = null,
                element = content.clone();
        element.attr('id','rec-detail-'+size);
        element.find('.delete_detail').attr('data-id', size);
        
        element.find('.id_kpi_detail_input').attr('id', 'detail_' + size + '_id_kpi_detail');
        element.find('.id_kpi_detail_input').attr('name', 'detail[' + size + '][id_kpi_detail]');

        element.find('.kpi_month_input_detail').attr('id', 'detail_' + size + '_kpi_month');
        element.find('.kpi_month_input_detail').attr('index', size);
        element.find('.kpi_month_input_detail').attr('name', 'detail[' + size + '][kpi_month]');
        element.find('.kpi_month_input_detail_error').attr('id', 'detail_' + size + '_kpi_monthError');
        element.find('.kpi_month_input_detail').prepend('<option selected></option>').select2({
            placeholder: "Select Month",
            // allowClear: true,
            data: global_select_month
        });

        element.find('.id_kpi_category_input').attr('id', 'detail_' + size + '_id_kpi_category');
        element.find('.id_kpi_category_input').attr('name', 'detail[' + size + '][id_kpi_category]');
        element.find('.id_kpi_category_input_error').attr('id', 'detail_' + size + '_id_kpi_categoryError');
        element.find('.id_kpi_category_input').prepend('<option selected></option>').select2({
            placeholder: "Select Program",
            allowClear: true,
            data: global_select_kpi_category
        });

        element.find('.id_kpi_type_input').attr('id', 'detail_' + size + '_id_kpi_type');
        element.find('.id_kpi_type_input').attr('name', 'detail[' + size + '][id_kpi_type]');
        element.find('.id_kpi_type_input_error').attr('id', 'detail_' + size + '_id_kpi_typeError');
        element.find('.id_kpi_type_input').prepend('<option selected></option>').select2({
            placeholder: "Select Program",
            allowClear: true,
            data: global_select_kpi_type
        });

        element.find('.kpi_value_input').attr('id', 'detail_' + size + '_kpi_value');
        element.find('.kpi_value_input').attr('name', 'detail[' + size + '][kpi_value]');
        element.find('.kpi_value_input_error').attr('id', 'detail_' + size + '_kpi_valueError');

        element.find('.weight_prosentase_input').attr('id', 'detail_' + size + '_weight_prosentase');
        element.find('.weight_prosentase_input').attr('name', 'detail[' + size + '][weight_prosentase]');
        element.find('.weight_prosentase_input_error').attr('id', 'detail_' + size + '_weight_prosentaseError');

        element.find('.id_course_header_input').attr('id', 'detail_' + size + '_id_course_header');
        element.find('.id_course_header_input').attr('index', size);
        element.find('.id_course_header_input').attr('name', 'detail[' + size + '][id_course_header]');
        element.find('.id_course_header_input_error').attr('id', 'detail_' + size + '_id_course_headerError');
        element.find('.id_course_header_input').prepend('<option selected></option>').select2({
            placeholder: "Select Course",
            allowClear: true,
            data: []
        });

        element.find('.status_input').attr('id', 'detail_' + size + '_status');
        element.find('.status_input').attr('name', 'detail[' + size + '][status]');
        element.find('.status_input_error').attr('id', 'detail_' + size + '_statusError');
        element.find('.status_input').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        });
        
        element.appendTo('#table_body_detail');
        $('#table_body_detail tr').each(function (index) {
            $(this).find('td:eq(0)').addClass('text-center');
            $(this).find('span.sn').html(index + 1);
        });
    });

    $(document).on('click', '.delete_detail', function () {
        var id = jQuery(this).attr('data-id');
        var targetDiv = jQuery(this).attr('targetDiv');
        jQuery('#rec-detail-' + id).remove();
        $('#table_body_detail tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
        return true;
    });

    $(document).on('change', '.kpi_month_input_detail', function (e, trigger) {
        if(trigger!='trigger'){
            let kpi_month_detail = $(this).val();
            let index = $(this).attr('index');
            let id_employee = $('#id_employee option:selected').val();
            get_course_by_month(kpi_month_detail, id_employee).then(res => {
                if(res.status == true){
                    let optionCourseByMonth = [];
                    $(res.data).each(function (i, val) {
                        optionCourseByMonth.push({'id':val.id_course_header, 'text':val.name, 'score':val.score})
                    });

                    $(`#detail_${index}_kpi_value`).val('');
                    $(`#detail_${index}_id_course_header`).html('');
                    $(`#detail_${index}_id_course_header`).prepend('<option selected></option>').select2({
                        placeholder: "Select Course",
                        allowClear: true,
                        data: optionCourseByMonth
                    }).trigger('change',['trigger']);
                }
            });
        }
    });

    $(document).on('change', '.id_course_header_input', function (e, trigger) {
        if(trigger!='trigger'){
            let score = $(this).select2('data')[0].score;
            let index = $(this).attr('index');
            if($(this).val() != ''){
                $(`#detail_${index}_kpi_value`).val(score).attr('readonly',true);
            } else {
                $(`#detail_${index}_kpi_value`).val(score).removeAttr('readonly');
            }
        }
    });

    $(document).on('change', '#process_type', function (e, trigger) {
        if(trigger!='trigger'){
            get_employee_attendee().then(res => {
                $('.get_employee_attendee').html('');
                $('.get_employee_attendee').prepend('<option selected></option>').select2({
                    placeholder: "Select Employee",
                    allowClear: true,
                    data: global_select_employee_attendee,
                });
            });
        }
    });

    $(document).on('click', '.edit', function () {
        run_in_modal()

        let id_kpi_group = $(this).attr('id');
        global_id_kpi_group = id_kpi_group;
        $("#kpiForm")[0].reset();
        $("#table_body_summary").html("");
        $("#table_body_detail").html("");
        $("#kpiForm .modal-title").html("<span class='fas fa-edit'></span> Update KPI");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
        $("#kpiForm input").removeClass("is-invalid");
        $('#save_button').attr('class', 'btn btn-sm btn-primary');
        $('#save_button').html('<i class="fas fa-edit"></i> Update');

        $.ajax({
            url: "{{ route('kpi_onboarding.get_kpi_edit') }}",
            method: "GET",
            data: {id_kpi_group: id_kpi_group},
            beforeSend: function () {
                $('#loader').removeClass('hidden');
            },
            success: function (res) {
                let dataKpi             = res.kpi;
                global_select_month     = res.kpi_month;
                global_id_summary       = 0;                   
                global_id_detail        = 0;

                $('#start_date').val(dataKpi.start_date);
                $('#end_date').val(dataKpi.end_date).trigger('change');

                $.each(dataKpi.header, function (i, item) {
                    $('#new_summary').click();
                });
                $.each(dataKpi.detail, function (i, item) {
                    $('#new_detail').click();
                });

                assignCategoryAndEmployee(dataKpi.process_type, dataKpi.id_employee)
                $('#average_prosentase').val(dataKpi.average_prosentase);
                $('#description').val(dataKpi.description);
                $('#notes').val(dataKpi.notes);
                
                $('#status').val(dataKpi.status).trigger('change');

                $('#table_body_summary tr').each(function (index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find(`#summary_${index}_id_kpi_header`).val(dataKpi.header[index].id_kpi_header);
                    $(this).find(`#summary_${index}_kpi_month`).val(dataKpi.header[index].kpi_month);
                    $(this).find(`#summary_${index}_notes`).val(dataKpi.header[index].notes);
                    $(this).find(`#summary_${index}_subtotal_kpi`).val(dataKpi.header[index].subtotal_kpi).trigger('change');
                    $(this).find(`#summary_${index}_status`).val(dataKpi.header[index].status).trigger('change');

                });

                $('#table_body_detail tr').each(function (index) {
                    $(this).find('span.sn').html(index + 1);
                    $(this).find(`#detail_${index}_id_kpi_detail`).val(dataKpi.detail[index].id_kpi_detail);
                    assignKpiMonthAndCourse(index, dataKpi.id_employee, dataKpi.detail[index].id_kpi_header, dataKpi.detail[index].id_course_header, dataKpi.detail[index].kpi_value)

                    $(this).find(`#detail_${index}_id_kpi_category`).val(dataKpi.detail[index].id_kpi_category).trigger('change');
                    $(this).find(`#detail_${index}_id_kpi_type`).val(dataKpi.detail[index].id_kpi_type).trigger('change');
                    $(this).find(`#detail_${index}_weight_prosentase`).val(dataKpi.detail[index].weight_prosentase);
                     $(this).find(`#detail_${index}_status`).val(dataKpi.detail[index].status).trigger('change');
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
    
        $('#modal_form_event').modal('show');
    });

    $(document).on('click', '.delete', function (event) {
    	id_kpi_group = $(this).attr('id');
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
                    url: "{{ route('kpi_onboarding.destroy') }}",
                    headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
                    method: "POST",
                    data: {id_kpi_group: id_kpi_group},
    			    success:function(res)
                    {
    				    setTimeout(function(){
    				    $('#confirmModal').modal('hide');
    				    swal({
        					title: "Data Deleted!",
                            text: res.message,
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

    function run_in_modal() {

    }

    function on_close_modal() {
        // $('#content_table').DataTable().ajax.reload();
        global_id_kpi_group = '';
        window.location.reload();
    }

    function save_summary(counter) {
        let kpiForm = $('#kpiForm').serializeArray();
        global_id_kpi_header = $(`#summary_${counter}_id_kpi_header`).val();

        if(global_id_kpi_header == '' && global_id_kpi_group == ''){
            kpiForm.push({name:'counter', value: counter});
        } else {
            kpiForm.push({name:'counter', value: counter});
            kpiForm.push({name:'id_kpi_header', value: global_id_kpi_header});
            kpiForm.push({name:'id_kpi_group', value: global_id_kpi_group});
        }

        $("#kpiForm input").removeClass("is-invalid");
        $(".invalid-feedback").children("strong").text("");
        $(".table-invalid-feedback").children("strong").text("");
        $(".error-tab").html("");
            
        $.ajax({
            url: "{{ route('kpi_onboarding.save_summary') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: kpiForm,
            success:function(res)
            {
                global_id_kpi_group = res.data.global_id_kpi_group;
                global_select_month = res.data.global_select_month;
                
                $(`#summary_${counter}_id_kpi_header`).val(res.data.id_kpi_header);
                $('#table_body_detail tr').each(function (index) {
                    $(this).find(`#detail_${index}_kpi_month`).select2({
                        placeholder: "Select Month",
                        allowClear: true,
                        data: global_select_month
                    }).trigger('change',['trigger']);
                });

                swal({
                    icon: 'success',
                    title: "Success",
                    text: res.message,
                    buttons: false,
                    timer: 1200
                });

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
                            $("#link_tab_kpi_summary").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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
        })
    }   

    function destroy_summary(counter, id_kpi_header) {
        let dataSend = {'id_kpi_header' : id_kpi_header, 'id_kpi_group' : global_id_kpi_group};

        $.ajax({
            url: "{{ route('kpi_onboarding.destroy_summary') }}",
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
            method: "POST",
            data: dataSend,
            success:function(res)
            {
                if(res.status == 'false'){
                    swal({
                        icon: 'error',
                        title: 'Oops...',
                        dangerMode: true,
                        text: res.message
                    });
                } else {
                    $('#rec-summary-'+counter).remove();
                    global_select_program = res.data.global_select_program;

                    $('#table_body_detail tr').each(function (index) {
                        $(this).find(`#detail_${index}_kpi_month`).select2({
                            placeholder: "Select Month",
                            allowClear: true,
                            data: global_select_program
                        }).trigger('change',['trigger']);
                    });
                }
            },
            error: function(response) {
                swal({
                    icon: 'error',
                    title: 'Oops...',
                    dangerMode: true,
                    text: 'Something went wrong! [Unknown Error]'
                });
            }
        })
    }   

    const get_employee = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/kpi_onboarding/kpi_onboarding/get_employee') ?>',
                method: "GET",
                success: function (res) {
                    $.each(res, function (i, val) {
                        name = `${val.name} (${val.nik_employee})`;
                        global_select_employee.push({id:val.id_employee, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_employee();
        }
    }

    const get_employee_attendee = async () => {
        try {
            let result;
            let category = $('#process_type option:selected').val();
            result = await $.ajax({
                url: '<?= url('learning_management/kpi_onboarding/kpi_onboarding/get_employee_attendee') ?>',
                method: "GET",
                data:{category:category},
                success: function (res) {
                    global_select_employee_attendee = [];
                    $.each(res, function (i, val) {
                        name = `${val.name} (${val.nik_employee})`;
                        global_select_employee_attendee.push({id:val.id_employee, text:name});
                    }); 
                },
            });
            return result;
        } catch (error) {
            get_employee_attendee();
        }
    }

    const get_course_by_month = async (kpi_month_detail, id_employee) => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/kpi_onboarding/kpi_onboarding/get_course_by_month') ?>',
                data:{id_kpi_header:kpi_month_detail, id_employee:id_employee},
                method: "GET",
                success: function (res) {
                },
            });
            return result;
        } catch (error) {
            get_course_by_month();
        }
    }

    const get_kpi_type = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/kpi_onboarding/kpi_onboarding/get_kpi_type') ?>',
                method: "GET",
                success: function (res) {
                    global_select_kpi_type = res;
                },
            });
            return result;
        } catch (error) {
            get_kpi_type();
        }
    }

    const get_kpi_group = async () => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('learning_management/kpi_onboarding/kpi_onboarding/get_kpi_group') ?>',
                method: "GET",
                success: function (res) {
                    global_select_kpi_group = res;
                },
            });
            return result;
        } catch (error) {
            get_kpi_group();
        }
    }

    const assignDetailDateByHeader = (startDate, endDate) => {
        $('#table_body_summary tr').each(function (index) {
            $(`#summary_${index}_kpi_month`).datepicker('destroy');
            $(`#summary_${index}_kpi_month`).datepicker({ 
                uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd',
                minDate: startDate, 
                maxDate: endDate, 
            });

        });
    }

    const setCategory = async (category='') => {
        if(category!=''){
            $('#process_type').val(category).trigger('change', ['trigger']);
        }
    }

    const assignCategoryAndEmployee = (category='', idEmployee='') => {
        setCategory(category).then(res => {
            get_employee_attendee().then(res2 => {
                $('.get_employee_attendee').html('');
                $('.get_employee_attendee').prepend('<option selected></option>').select2({
                    placeholder: "Select Employee",
                    allowClear: true,
                    data: global_select_employee_attendee,
                });
                if(idEmployee!=''){
                    $('.get_employee_attendee').val(idEmployee).trigger('change');
                }
            });
        });
    }

    const assignKpiMonthAndCourse = (index, idEmployee, kpiMonth='', course='', kpiValue='') => {
        $(`#detail_${index}_kpi_month`).select2({
            placeholder: "Select Month",
            allowClear: true,
            data: global_select_month
        })
        $(`#detail_${index}_kpi_value`).val(kpiValue).attr('readonly',true);

        if(kpiMonth!=''){
            $(`#detail_${index}_kpi_month`).val(kpiMonth).trigger('change',['trigger']);

            get_course_by_month(kpiMonth, idEmployee).then(res => {
                if(res.status == true){
                    let optionCourseByMonth = [];
                    $(res.data).each(function (i, val) {
                        optionCourseByMonth.push({'id':val.id_course_header, 'text':val.name, 'score':val.score})
                    });
                    $(`#detail_${index}_id_course_header`).html('');
                    $(`#detail_${index}_id_course_header`).prepend('<option selected></option>').select2({
                        placeholder: "Select Course",
                        allowClear: true,
                        data: optionCourseByMonth
                    });
                    if(course!=''){
                        $(`#detail_${index}_id_course_header`).val(course).trigger('change',['trigger']);
                    }
                }
            });
        }
    }

    $(document).ready(function(){
        $('#status').select2({
            placeholder: "Select Status",
            allowClear: true,
            data: status
        }); 

        $(`#process_type`).prepend('<option selected></option>').select2({
            placeholder: "Select Category",
            data: process_type
        });

        get_employee_attendee().then(res => {
            $('.get_employee_attendee').prepend('<option selected></option>').select2({
                placeholder: "Select Employee",
                allowClear: true,
                data: global_select_employee_attendee,
            });
        });

        get_kpi_type()
        get_kpi_group()

    });

    const get_course = new Promise(function(res) {
        $.getJSON("{{ route('kpi_onboarding.get_course') }}", function (data) {
            res(data)
        }).fail(function (fail) { // Call failed
            get_course();
        }); 
    });


    const get_kpi_category = new Promise(function(res) {
        $.getJSON("{{ route('kpi_onboarding.get_kpi_category') }}", function (data) {
            res(data)
        }).fail(function (fail) { // Call failed
            get_kpi_category();
        }); 
    });

    get_course.then(function(value) {
        global_select_course = value;
    });

    get_kpi_category.then(function(value) {
        global_select_kpi_category = value;
    });
    

</script>
@endsection