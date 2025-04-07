@extends('adminlte::page')
@section('title', 'HR Config Settings')

@section('content')

<div class="row">
  <div class="col-12">
    <div class="card card-danger card-outline">
      <div class="card-header">
        <h5 class="card-title">Master HR Config Settings</h5>
        <div class="card-tools">
          @if(empty($cekHrConfig))
          <button type="button" class="new btn btn-sm btn-success"><i class="fas fa-plus"></i> Add HR Config Setting</button>
          @endif
        </div>
      </div>

      <div class="card-body">
        <button id="advanced" type="button" class="btn btn-default">Advanced Search</button>
        <br>
        <br>
        <table id="hrconfig_table" class="table table-striped table-bordered table-hover datatable" style="width: 100%;">
          <thead>
           <tr>				   
            <th></th>
            <th></th>
            <th>No</th>
            <th>Company</th>
            <th>Late Limit</th>
            <th>Max Late</th>
            <th>Overtime Limit</th>
            <th>Max Overtime</th>
            <th>Lock GPS</th>
            <th>Allow Next Day</th>
            <th style="text-align:center;" width=100>Action</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</div>
</div>
<!-- Form -->
<div class="modal fade" id="modal_form_hrconfig" data-backdrop="static" data-keyboard="false" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Form HR Config</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form method="POST" id="hrconfigForm" enctype="multipart/form-data" novalidate>
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Company</label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="id_hr_config" id="id_hr_config" type="hidden" class="form-control form-control-sm" style="width: 100%;">
                  <input autocomplete="off" type="text" name="id_company" id="id_company" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="id_companyError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Late Limit <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="late_tolerance_limit" name="late_tolerance_limit" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="late_tolerance_limit" value="true" id="late_tolerance_limit">
                  <span class="invalid-feedback" role="alert" id="late_tolerance_limitError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Late <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_late" id="maximum_late" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_lateError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Overtime Limit <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="overtime_limit" name="overtime_limit" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="overtime_limit" value="true" id="overtime_limit">
                  <span class="invalid-feedback" role="alert" id="overtime_limitError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Overtime <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_overtime" id="maximum_overtime" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_overtimeError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Min SP Duration <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="minimum_sp_duration" id="minimum_sp_duration" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="minimum_sp_durationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Min Annual KPI <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="minimum_annual_kpi" id="minimum_annual_kpi" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="minimum_annual_kpiError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Min KPK Duration <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="minimum_kpk_duration" id="minimum_kpk_duration" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="minimum_kpk_durationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-md-4 col-form-label">Grade Promotion <sup class="text-danger">*</sup></label>
								<div class="col-md-8">
                  <select name="id_grade_promotion[]" id="id_grade_promotion" class="form-control form-control-sm select2" data-placeholder="Select Grade Promotion" style="width: 100%;" multiple="multiple">
									</select>
                  <span class="invalid-feedback" role="alert" id="id_grade_promotionError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-md-4 col-form-label">Position Routing <sup class="text-danger">*</sup></label>
								<div class="col-md-8">
                  <select name="id_position_routing[]" id="id_position_routing" class="form-control form-control-sm select2" data-placeholder="Select Position Routing" style="width: 100%;" multiple="multiple">
									</select>
                  <span class="invalid-feedback" role="alert" id="id_position_routingError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="row">
                <label class="col-sm-4 col-form-label">Lock GPS <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="lock_geo_location" name="lock_geo_location" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="lock_geo_location" value="true" id="lock_geo_location">
                  <span class="invalid-feedback" role="alert" id="lock_geo_locationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Allow Next Day <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="allow_checkout_nextdays" name="allow_checkout_nextdays" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="allow_checkout_nextdays" value="true" id="allow_checkout_nextdays">
                  <span class="invalid-feedback" role="alert" id="allow_checkout_nextdaysError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Need Recommendation Form <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="allow_checkout_nextdays" name="allow_checkout_nextdays" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="need_recommendation_form" value="true" id="need_recommendation_form">
                  <span class="invalid-feedback" role="alert" id="need_recommendation_formError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">ERP Integration <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <!-- <select id="allow_checkout_nextdays" name="allow_checkout_nextdays" class="form-control form-control-sm select_opsi" style="width: 100%;"></select> -->
                  <input type="checkbox" style="width: 20px;height: 20px;" name="erp_integration" value="true" id="erp_integration">
                  <span class="invalid-feedback" role="alert" id="erp_integrationError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Cash Advance Request <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_cash_advance_request" id="maximum_cash_advance_request" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_cash_advance_requestError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Reimburse Expense <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_reimburse_expense" id="maximum_reimburse_expense" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_reimburse_expenseError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Official Travel Request <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_official_travel_request" id="maximum_official_travel_request" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_official_travel_requestError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Cancel Travel Request <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_cancel_travel_request" id="maximum_cancel_travel_request" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_cancel_travel_requestError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-sm-4 col-form-label">Max Attendance Correction Request <sup class="text-danger">*</sup></label>
                <div class="col-sm-8">
                  <input autocomplete="off" name="maximum_attendace_correction_request" id="maximum_attendace_correction_request" type="number" class="form-control form-control-sm" style="width: 100%;">
                  <span class="invalid-feedback" role="alert" id="maximum_attendace_correction_requestError">
                    <strong></strong>
                  </span>
                </div>
              </div>
              <div class="row">
                <label class="col-md-4 col-form-label">Position Detail Email Travel <sup class="text-danger">*</sup></label>
								<div class="col-md-8">
                  <select name="id_position_detail_email_travel[]" id="id_position_detail_email_travel" class="form-control form-control-sm select2" data-placeholder="Select Position Detail Email Travel" style="width: 100%;" multiple="multiple">
									</select>
                  <span class="invalid-feedback" role="alert" id="id_position_detail_email_travelError">
                    <strong></strong>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <!-- table -->
          <div class="row">
            <div class="col-xl-12"><hr></div>
            <div class="col-xl-12" id="tab_detail">
              <div class="nav nav-tabs justify-content-left mb-4">
                <a class="nav-item nav-link active" data-toggle="tab" href="#tab-pane-1">Recruitment Config
                  <span class="error-tab text-red">Error</span>
                </a>
                <a class="nav-item nav-link" data-toggle="tab" href="#tab-pane-2">Talent Config
                  <span class="error-tab text-red">Error</span>
                </a>
                <a class="nav-item nav-link" data-toggle="tab" href="#survey_update">Survey Config <span class="error-tab text-red">Error</span>
                </a>
              </div>
            </div>
            <div class="col-xl-12">

              <div class="tab-content">
                <!-- Recruitment -->
                <div class="tab-pane fade show active" id="tab-pane-1">
                  <input type="" id="id_config_email_rec" hidden="" name="id_config_email_rec">
                  <button type="button" id="new_recruitment_config" class="btn btn-primary btn-sm mb-3" style="float: right;">
                   <i class="fa fa-plus"></i> Add Recruitment Config
                 </button>
                 <div class="table-responsive">
                  <table class="table table-hover table-bordered table-striped">
                    <thead>
                     <tr>
                      <th>No. </th>
                      <th>Position</th>
                      <th>Email</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="table_recruitment_config">
                  </tbody>
                </table>
              </div>
            </div>
            <!-- End Recruitment -->
            <!-- Talent -->
            <div class="tab-pane" id="tab-pane-2">
              <input type="" id="id_config_talent_tal" hidden="" name="id_config_talent_tal">
              <button type="button" id="new_talent_config" class="btn btn-primary btn-sm mb-3" style="float: right;">
               <i class="fa fa-plus"></i> Add Talent Config
             </button>
             <div class="table-responsive">
              <table class="table table-hover table-bordered table-striped">
                <thead>
                 <tr>
                  <th>No. </th>
                  <th>Survey</th>
                  <th>Min Value</th>
                  <th>Max Value</th>
                  <th>Description</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="table_talent_config">
              </tbody>
            </table>
          </div>
        </div>
        <!-- End Talent --><!-- End Talent -->

        <!-- Survey -->
        <div class="tab-pane" id="survey_update">
            <input name="id_config_survey" id="id_config_survey" hidden >
            <button type="button" id="new_config_survey" class="btn btn-primary btn-sm mb-3" style="float: right;">
                <i class="fa fa-plus"></i> Add Survey Config </button>
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No. </th>
                            <th>Survey</th>
                            <th>Period</th>
                            <th>Question</th>
                            <th>Action Type</th>
                            <th style="min-width: 170px;">Table</th>
                            <th style="min-width: 170px;">Column</th>
                            <th style="min-width: 220px;">Path File</th>
                            <th style="min-width: 130px;">Set Unique Key</th>
                            <th style="min-width: 180px;">Expired Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="table_config_survey"></tbody>
                </table>
            </div>
        </div>
        <!-- End Survey -->
      </div>

    </div>
  </div>
</div>
<div class="modal-loading" id="modal-loading" style="display: none;">
 <span class="fa fa-spinner fa-spin fa-3x"></span>
</div>
<div class="modal-footer">
  <button class="btn btn-sm btn-success action"><i class="fas fa-save"></i> Save</button>&nbsp;
  <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
</div>
</form>
<!-- Isi Tbody recruitment -->
<div style="display:none;">
  <table id="sample_table_recruitment_config">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input autocomplete="off" name="config[0][id_config_email_recruitment]" hidden="" id="config_0_id_config_email_recruitment" class="form-control form-control-sm id_config_email_recruitment_input">
        <select name="config[0][id_position_detail]" id="config_0_id_position_detail" class="form-control form-control-sm id_position_detail_input" style="width: 100%;"></select>
        <span class="invalid-feedback id_position_detail_input_error" role="alert" id="config_0_id_position_detailError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="text" name="config[0][email]" id="config_0_email" class="form-control form-control-sm email_input">
        <span class="invalid-feedback email_input_error" role="alert" id="config_0_emailError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="config[0][status_recruitment]" id="config_0_status_recruitment" class="form-control form-control-sm status_recruitment_input" style="width: 100%;">
          <option value="A">Active</option>
          <option value="I">Inactive</option>
        </select>
        <span class="invalid-feedback" role="alert" id="config_0_status_recruitmentError">
          <strong></strong>
        </span>
      </td>
      <td>
        <center>
          <button type="button" class="delete-record btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
      </td>
    </tr>
  </table>
</div>
<!-- End isi recruitment -->
<!-- Isis Tbody Talent -->
<div style="display:none;">
  <table id="sample_table_talent_config">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input autocomplete="off" name="talent[0][id_config_engagement_category]" hidden="" id="talent_0_id_config_engagement_category" class="form-control form-control-sm id_config_engagement_category_input" style="width: 100%;">
        <select name="talent[0][id_survey_header]" id="talent_0_id_survey_header" class="form-control form-control-sm id_survey_header_input" style="width: 100%;"></select>
        <span class="invalid-feedback id_survey_header_input_error" role="alert" id="talent_0_id_survey_headerError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" type="number" min="1" name="talent[0][min_value]" id="talent_0_min_value" class="form-control form-control-sm min_value_input" style="width: 100%;">
        <span class="invalid-feedback min_value_input_error" role="alert" id="talent_0_min_valueError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" type="number" min="1" name="talent[0][max_value]" id="talent_0_max_value" class="form-control form-control-sm max_value_input" style="width: 100%;">
        <span class="invalid-feedback max_value_input_error" role="alert" id="talent_0_max_valueError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" name="talent[0][description]" id="talent_0_description" class="form-control form-control-sm description_input" style="width: 100%;">
        <span class="invalid-feedback description_input_error" role="alert" id="talent_0_descriptionError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="talent[0][status_talent]" id="talent_0_status_talent" class="form-control form-control-sm status_talent_input" style="width: 100%;">
          <option value="A">Active</option>
          <option value="I">Inactive</option>
        </select>
        <span class="invalid-feedback" role="alert" id="talent_0_status_talentError">
          <strong></strong>
        </span>
      </td>
      <td>
        <center>
          <button type="button" class="delete-record-tal btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
      </td>
    </tr>
  </table>
</div>
<!-- End Talent -->

<div style="display:none;">
  <table id="sample_table_config_survey">
    <tr id="">
      <td><span class="sn" style="vertical-align:middle;"></span></td>   
      <td>
        <input name="survey[0][id_config_survey_update]" id="survey_0_id_config_survey_update" class="form-control form-control-sm id_config_survey_update" hidden>
        <select name="survey[0][id_survey_header]" id="survey_0_id_survey_header" class="form-control form-control-sm id_survey_header" style="width: 100%;" size="0"></select>
        <span class="invalid-feedback id_survey_header_error" role="alert" id="survey_0_id_survey_headerError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="survey[0][id_survey_history]" id="survey_0_id_survey_history" class="form-control form-control-sm id_survey_history" style="width: 100%;"></select>
        <span class="invalid-feedback id_survey_history_error" role="alert" id="survey_0_id_survey_historyError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="survey[0][id_survey_question]" id="survey_0_id_survey_question" class="form-control form-control-sm id_survey_question" style="width: 100%;"></select>
        <span class="invalid-feedback id_survey_question_error" role="alert" id="survey_0_id_survey_questionError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="survey[0][action_type]" id="survey_0_action_type" class="form-control form-control-sm action_type" style="width: 100%;">
        </select>
        <span class="invalid-feedback" role="alert" id="survey_0_action_typeError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" name="survey[0][update_to_table]" id="survey_0_update_to_table" class="form-control form-control-sm update_to_table" style="width: 100%;">
        <span class="invalid-feedback update_to_table_error" role="alert" id="survey_0_update_to_tableError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" name="survey[0][update_to_column]" id="survey_0_update_to_column" class="form-control form-control-sm update_to_column" style="width: 100%;">
        <span class="invalid-feedback update_to_column_error" role="alert" id="survey_0_update_to_columnError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" name="survey[0][update_to_path]" id="survey_0_update_to_path" class="form-control form-control-sm update_to_path" style="width: 100%;">
        <span class="invalid-feedback update_to_path_error" role="alert" id="survey_0_update_to_pathError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input type="checkbox" name="survey[0][unique_key]" id="survey_0_unique_key" class="form-control form-control-sm unique_key" style="width: 100%;">
        <span class="invalid-feedback unique_key_error" role="alert" id="survey_0_unique_keyError">
          <strong></strong>
        </span>
      </td>
      <td>
        <input autocomplete="off" name="survey[0][expired_date]" id="survey_0_expired_date" class="form-control form-control-sm expired_date datepicker" style="width: 100%;">
        <span class="invalid-feedback expired_date_error" role="alert" id="survey_0_expired_dateError">
          <strong></strong>
        </span>
      </td>
      <td>
        <select name="survey[0][status_survey]" id="survey_0_status_survey" class="form-control form-control-sm status_survey" style="width: 100%;"></select>
        <span class="invalid-feedback status_survey_error" role="alert" id="survey_0_status_surveyError">
          <strong></strong>
        </span>
      </td>
      <td>
        <center>
          <button type="button" class="delete-record-survey btn btn-xs btn-danger" data-id="0"><i class="far fa-trash-alt"></i></button>
        </center>
      </td>
    </tr>
  </table>
</div>

</div>
</div>
</div>
<!-- End Form -->

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

  .sticky {
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 2;
  }
  .sticky:nth-child(2) {
    left: 50px;
  }
  tr:before {
    content: '';
    position: -webkit-sticky;
    position: sticky;
    left: 0;
    z-index: 1;
  }
  tr:before {
    content: ' ';
    display: table;
  }
  tr:after {
    content: ' ';
    display: table;
    clear: both;
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
</style>
@endsection
@section('scripts')
<script type="text/javascript">
  function show_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "block";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "none";
      ModalBody[i].style.background = 'white';
      ModalBody[i].style.opacity = '0.4';
    }
  }
  function hide_loading() {
    var elemenModalLoading = document.getElementsByClassName('modal-loading');
    var ModalBody = document.getElementsByClassName('modal-body');
    for (var i = 0; i < elemenModalLoading.length; i++) {
      elemenModalLoading[i].style.display = "none";
    }
    for (var i = 0; i < ModalBody.length; i++) {
      ModalBody[i].style.pointerEvents = "auto";
      ModalBody[i].style.background = "transparent";
      ModalBody[i].style.opacity = '1';
    }
  }
  $(function () {
    $('#hrconfig_table').DataTable({
      processing: true,
      pageLength: 10,
      responsive: true,
      ajax: {
        url: "{{ route('index.hr_config') }}",
        // data : {id_url:global_url_server},
        error: function (jqXHR, textStatus, errorThrown) {
          $('#hrconfig_table').DataTable().ajax.reload();
        }
      },
      columns: [
      {
        defaultContent: '',
        orderable: false,
      },
      {   
        data: 'id_letter',
        defaultContent: '',
        orderable: false
      },
      { data: 'DT_RowIndex', name: 'DT_RowIndex'},
      { data: 'company_name', name: 'company_name' },
      { 
        data: 'late_tolerance_limit', 
        name: 'late_tolerance_limit', 
        render: function (data, type, row) {
          if (data == true) {
            return '<span class="badge bg-success text-white justify-content-center">Yes</span>';
          }else{
            return '<span class="badge bg-danger text-white justify-content-center">No</span>';
          }
        }  
      },
      { data: 'maximum_late', name: 'maximum_late' },
      { 
        data: 'overtime_limit', 
        name: 'overtime_limit', 
        render: function (data, type, row) {
          if (data == true) {
            return '<span class="badge bg-success text-white justify-content-center">Yes</span>';
          }else{
            return '<span class="badge bg-danger text-white justify-content-center">No</span>';
          }
        }  
      },
      { data: 'maximum_overtime', name: 'maximum_overtime' },
      { 
        data: 'lock_geo_location', 
        name: 'lock_geo_location', 
        render: function (data, type, row) {
          if (data == true) {
            return '<span class="badge bg-success text-white justify-content-center">Yes</span>';
          }else{
            return '<span class="badge bg-danger text-white justify-content-center">No</span>';
          }
        }  
      },
      { 
        data: 'allow_checkout_nextdays', 
        name: 'allow_checkout_nextdays', 
        render: function (data, type, row) {
          if (data == true) {
            return '<span class="badge bg-success text-white justify-content-center">Yes</span>';
          }else{
            return '<span class="badge bg-danger text-white justify-content-center">No</span>';
          }
        }  
      },
      { data: 'action', name: 'action', orderable: false, className: 'space'
    }
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
    },
  });
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
  option = [
  {
    id: 'true',
    text: 'True'
  },
  {
    id: 'false',
    text: 'False'
  },
  ];  
  $(".select_opsi").select2({
    allowClear: true,
    placeholder: ":. FILTER OPTION .:",
    data: option
  });
  var ajaxUrl = "";
  $(".new").click(function() {
    $("#hrconfigForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#hrconfigForm input").removeClass("is-invalid");
    $("#hrconfigForm select").removeClass("custom-select");
    jQuery('.rec').remove();
    $(".error-tab").html("");
    jQuery('.tal').remove();
    $("#modal_form_hrconfig").modal('show');
    ajaxUrl = "{{route('save.hrconfig')}}";
  });
  // Recruitment Config
  let global_id_config_email_recruitment = 0;
  let global_id_position_detail = [];
  // Talent COnfig
  let global_id_config_engagement_category = 0;
  let global_id_survey_header = [];

  let global_id_config_survey_update = 0;
  let global_id_survey_header_update = [];

  const get_data = async () => {
    $.getJSON('<?= url('general_setting/company_setting/hr_config_settings/get_data') ?>', function (data) {
      global_id_position_detail = data.position;
      global_id_survey_header = data.survey;
      var company = data.company;
      $("#id_company").val(company.company_name);
    }).fail(function (data) {
      get_data();
    });   
  }
  $(document).ready(function() {
    $("#id_company").attr('readonly',true);
    get_data();
  });
  // Recuirment
  $(document).on('click', '#new_recruitment_config', function () {
    var content = jQuery("#sample_table_recruitment_config tr"),
    size = global_id_config_email_recruitment++,
    element = null,
    element = content.clone();
    element.attr('id','rec-'+size);
    element.attr('class','rec');
    element.find('.delete-record').attr('data-id', size);
    element.find('.id_config_email_recruitment_input').attr('id', 'config_' + size + '_id_config_email_recruitment');
    element.find('.id_config_email_recruitment_input').attr('name', 'config[' + size + '][id_config_email_recruitment_rec]');

    element.find('.id_position_detail_input').attr('id', 'config_' + size + '_id_position_detail');
    element.find('.id_position_detail_input').attr('name', 'config[' + size + '][id_position_detail]');
    element.find('.id_position_detail_input_error').attr('id', 'config_' + size + '_id_position_detailError');
    element.find('.id_position_detail_input').select2({
      placeholder: "Position Detail ....",
      allowClear: true,
      data: global_id_position_detail
    }).on('change',function(e) {
      var positionID = e.target.value;
      if (positionID) {
        element.find('.email_input').attr('readonly',true);
      }else{
        element.find('.email_input').attr('readonly',false);
      }
    }).trigger('change');
    element.find('.id_position_detail_input').val(null).trigger('change');

    element.find('.email_input').attr('id', 'config_' + size + '_email');
    element.find('.email_input').attr('name', 'config[' + size + '][email]');
    element.find('.email_input_error').attr('id', 'config_' + size + '_emailError');

    element.find('.status_recruitment_input').attr('id', 'config_' + size + '_status_recruitment');
    element.find('.status_recruitment_input').attr('name', 'config[' + size + '][status_recruitment]');
    element.find('.status_recruitment_input_error').attr('id', 'config_' + size + '_status_recruitmentError');
    element.find('.status_recruitment_input').select2();
    element.appendTo('#table_recruitment_config');
    $('#table_recruitment_config tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
  });
  $(document).on('click', '.delete-record', function () {
    var id = jQuery(this).attr('data-id');
    var more_id = jQuery(this).attr('more_id');
    var currentValues = $("#id_config_email_rec").val();
    if (currentValues) {
      if (more_id) {
        $("#id_config_email_rec").val(currentValues + ',' + more_id);
      }
    } else {
      $("#id_config_email_rec").val(more_id);
    }
    var targetDiv = jQuery(this).attr('targetDiv');
    jQuery('#rec-' + id).remove();
    $('#table_recruitment_config tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
    return true;
  });
  // Talent
  $(document).on('click', '#new_talent_config', function () {
    var content = jQuery("#sample_table_talent_config tr"),
    size = global_id_config_engagement_category++,
    element = null,
    element = content.clone();
    element.attr('id','tal-'+size);
    element.attr('class','tal');
    element.find('.delete-record-tal').attr('data-id', size);
    element.find('.id_config_engagement_category_input').attr('id', 'talent_' + size + '_id_config_engagement_category');
    element.find('.id_config_engagement_category_input').attr('name', 'talent[' + size + '][id_config_engagement_category_tal]');

    element.find('.id_survey_header_input').attr('id', 'talent_' + size + '_id_survey_header');
    element.find('.id_survey_header_input').attr('name', 'talent[' + size + '][id_survey_header]');
    element.find('.id_survey_header_input_error').attr('id', 'talent_' + size + '_id_survey_headerError');
    element.find('.id_survey_header_input').select2({
      placeholder: "Survey ....",
      allowClear: true,
      data: global_id_survey_header
    }).on('change',function(e) {
    }).trigger('change');
    element.find('.id_survey_header_input').val(null).trigger('change');

    element.find('.min_value_input').attr('id', 'talent_' + size + '_min_values');
    element.find('.min_value_input').attr('name', 'talent[' + size + '][min_values]');
    element.find('.min_value_input_error').attr('id', 'talent_' + size + '_min_valuesError');

    element.find('.max_value_input').attr('id', 'talent_' + size + '_max_values');
    element.find('.max_value_input').attr('name', 'talent[' + size + '][max_values]');
    element.find('.max_value_input_error').attr('id', 'talent_' + size + '_max_valuesError');

    element.find('.description_input').attr('id', 'talent_' + size + '_descriptions');
    element.find('.description_input').attr('name', 'talent[' + size + '][descriptions]');
    element.find('.description_input_error').attr('id', 'talent_' + size + '_descriptionsError');

    element.find('.status_talent_input').attr('id', 'talent_' + size + '_status_talent');
    element.find('.status_talent_input').attr('name', 'talent[' + size + '][status_talent]');
    element.find('.status_talent_input_error').attr('id', 'talent_' + size + '_status_talentError');
    element.find('.status_talent_input').select2();
    element.appendTo('#table_talent_config');
    $('#table_talent_config tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
  });
  $(document).on('click', '.delete-record-tal', function () {
    var id = jQuery(this).attr('data-id');
    var targetDiv = jQuery(this).attr('targetDiv');
    var more_id = jQuery(this).attr('more_id');
    var currentValues = $("#id_config_talent_tal").val();
    if (currentValues) {
      if (more_id) {
        $("#id_config_talent_tal").val(currentValues + ',' + more_id);
      }
    } else {
      $("#id_config_talent_tal").val(more_id);
    }
    jQuery('#tal-' + id).remove();
    $('#table_talent_config tr').each(function (index) {
      $(this).find('span.sn').html(index + 1);
    });
    return true;
  });
  $(function () {
    $('#hrconfigForm').submit(function (e) {
      e.preventDefault();
      document.querySelector(".action").disabled=true;
      let formData = $(this).serializeArray();
      $(".invalid-feedback").children("strong").text("");
      $("#hrconfigForm input").removeClass("is-invalid");
      $("#hrconfigForm select").removeClass("custom-select");
      $(".error-tab").html("");
      $.ajax({
        method: "POST",
        headers: {
          Accept: "application/json"
        },
        url : ajaxUrl,
        data: formData,
        success: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status == 'true') {
            $("#hrconfigForm")[0].reset();
            $(".select_opsi").val(null).trigger('change');
            $('#modal_form_hrconfig').modal('hide');
            swal({
              icon: 'success',
              title: 'Success',
              text: response.message
            });
            $('#hrconfig_table').DataTable().ajax.reload();
            window.location.reload();
          }else {
            swal({
              icon: 'error',
              title: 'Oops...',
              dangerMode: true,
              text: 'Something went wrong! [Unknown Error Not Save]'
            });
          }
        },
        error: function (response) {
          document.querySelector(".action").disabled=false;
          if (response.status === 422) {
            let errors = response.responseJSON.errors;
            Object.keys(errors).forEach(function (key) {
              var key_temp = key.replaceAll(".", "_");
              $("select[id='" + key + "']").addClass("custom-select");
              $("#" + key_temp).addClass("is-invalid");
              $("#" + key_temp + "Error").children("strong").text(errors[key][0]);
              var tab_id = $("#" + key_temp + "Error").closest(".tab-pane").attr("id");
              if (tab_id != undefined) {
                $("#tab_detail").find("[href$='#" + tab_id + "']").find(".error-tab").html("<i class='fas fa-exclamation-circle'></i> Required");
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
      });
    });
  });
  function get_edit(hrconfigID) {
    $.ajax({
      method: "GET",
      url : "{{url('general_setting/company_setting/hr_config_settings/getEdit')}}"+"/"+hrconfigID,
      // data: {hrconfigID: hrconfigID},
      success: function (response) {
        hide_loading();
        global_id_config_email_recruitment = 0;
        global_id_config_engagement_category = 0;
        global_id_config_survey_update = 0;

        $("#id_grade_promotion").select2({
            data: response.grade_promotions
        });
        $("#id_position_routing").select2({
            data: response.position_routing
        });
        $("#id_position_detail_email_travel").select2({
          data: response.position_detail
        });

        $.each(response.data, function(key, value) {
          $("#id_company").val(value.company_name);
          if (value.late_tolerance_limit == true) {
            $("#late_tolerance_limit").attr('checked',true);
          }else{
            $("#late_tolerance_limit").attr('checked',false);
          }
          $("#maximum_late").val(value.maximum_late);
          if (value.overtime_limit == true) {
            $("#overtime_limit").attr('checked',true);
          }else{
            $("#overtime_limit").attr('checked',false);
          }
          $("#maximum_overtime").val(value.maximum_overtime);
          if (value.lock_geo_location == true) {
            $("#lock_geo_location").attr('checked',true);
          }else{
            $("#lock_geo_location").attr('checked',false);
          }
          if (value.allow_checkout_nextdays == true) {
            $("#allow_checkout_nextdays").attr('checked',true);
          }else{
            $("#allow_checkout_nextdays").attr('checked',false);
          }
          if (value.erp_integration == true) {
            $("#erp_integration").attr('checked',true);
          } else {
            $("#erp_integration").attr('checked',false);
          }
          if (value.need_recommendation_form_flag == true) {
            $("#need_recommendation_form").attr('checked',true);
          } else {
            $("#need_recommendation_form").attr('checked',false);
          }
          $("#id_hr_config").val(value.id_hr_config);
          $("#maximum_attendace_correction_request").val(value.maximum_attendace_correction_request);
          $("#maximum_cash_advance_request").val(value.maximum_cash_advance_request);
          $("#maximum_reimburse_expense").val(value.maximum_reimburse_expense);
          $("#minimum_kpk_duration").val(value.minimum_kpk_duration);
          $("#minimum_sp_duration").val(value.minimum_sp_duration);
          $("#minimum_annual_kpi").val(value.minimum_annual_kpi);
          $("#maximum_official_travel_request").val(value.maximum_official_travel_request);
          $("#maximum_cancel_travel_request").val(value.maximum_cancel_travel_request);
          $("#id_grade_promotion").val(value.id_grade_promotion);
          $("#id_grade_promotion").trigger('change');
          $("#id_position_routing").val(value.id_position_routing);
          $("#id_position_routing").trigger('change');
          $("#id_position_detail_email_travel").val(value.id_position_detail_email_travel);
          $("#id_position_detail_email_travel").trigger('change');
        });
            // Recruitment
            $.each(response.recruitment, function (i, item) {
              $('#new_recruitment_config').trigger('click');
            });
            setTimeout(function () {
              $('#table_recruitment_config tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
                $(this).find('.delete-record').attr('more_id',response.recruitment[index].id_config_email_recruitment);
                $(this).find('.id_config_email_recruitment_input').val(response.recruitment[index].id_config_email_recruitment);
                $(this).find('.email_input').val(response.recruitment[index].recruitment_email);
                $(this).find('.id_position_detail_input').val(response.recruitment[index].id_position_detail).trigger('change');
                $(this).find('.status_recruitment_input').val(response.recruitment[index].status).trigger('change');
              });                       
            }, 500);
            // End Rec
            // Talent
            $.each(response.talent, function (i, item_tal) {
              $('#new_talent_config').trigger('click');
            });
            setTimeout(function () {
              $('#table_talent_config tr').each(function (index) {
                $(this).find('span.sn').html(index + 1);
                $(this).find('.delete-record-tal').attr('more_id',response.talent[index].id_config_engagement_category);
                $(this).find('.id_config_engagement_category_input').val(response.talent[index].id_config_engagement_category);
                $(this).find('.id_survey_header_input').val(response.talent[index].id_survey_header).trigger('change');
                $(this).find('.min_value_input').val(response.talent[index].min_value);
                $(this).find('.max_value_input').val(response.talent[index].max_value);
                $(this).find('.description_input').val(response.talent[index].description);
                $(this).find('status_talent_input').val(response.talent[index].status).trigger('change');
              });                       
            }, 500);
            // End Tal


            //SURVEY
            $.each(response.survey, function (i, item_tal) {
              $('#new_config_survey').trigger('click');
            });

            $('#table_config_survey tr').each(function(index) {
                $(this).find('span.sn').html(index + 1);
                let thisDataSurvey = response.survey[index];

                $(this).find('.id_config_survey_update').val(thisDataSurvey.id_config_survey_update);
                fillSurvey(thisDataSurvey.id_survey_header, index, thisDataSurvey.id_survey_history, thisDataSurvey.id_survey_question).then(res => {
                    $(`#survey_${index}_action_type`).val(thisDataSurvey.action_type).trigger('change');
                    $(`#survey_${index}_update_to_table`).val(thisDataSurvey.update_to_table);
                    $(`#survey_${index}_update_to_column`).val(thisDataSurvey.update_to_column);
                    $(`#survey_${index}_update_to_path`).val(thisDataSurvey.update_to_path);
                    $(`#survey_${index}_expired_date`).val(thisDataSurvey.expired_date);
                    if(thisDataSurvey.unique_key == true){
                        $(`#survey_${index}_unique_key`).prop('checked', true);
                    } else {
                        $(`#survey_${index}_unique_key`).prop('checked', false);
                    }
                    $(`#survey_${index}_status_survey`).val(thisDataSurvey.status).trigger('change');
                    $(`.delete-record-survey [data-id="${index}"]`).hide();
                });

            });
            //END SURVEY

          },
          error: function(response) {
            get_edit(hrconfigID);
          }
        });
  }

  $(document).on('click','.btn-edit',function() {
    $("#hrconfigForm")[0].reset();
    $(".select_opsi").val(null).trigger('change');
    $(".invalid-feedback").children("strong").text("");
    $("#hrconfigForm input").removeClass("is-invalid");
    $("#hrconfigForm select").removeClass("custom-select");
    $(".error-tab").html("");
    var hrconfigID = $(this).attr('more_id');
    show_loading();
    ajaxUrl = "{{route('edit.hrconfig')}}";
    jQuery('.rec').remove();
    jQuery('.tal').remove();
    $("#modal_form_hrconfig").modal('show');
    if (hrconfigID) {
      get_edit(hrconfigID);
    }
  });
  $(document).on('click', '.btn-del', function (event) {
    hrconfigID = $(this).attr('more_id');
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
          method: "GET",
          url: "{{url('general_setting/company_setting/hr_config_settings/destroy')}}"+"/"+hrconfigID,
          success:function(data)
          {
            setTimeout(function(){
              swal({
                title: "Data Deleted!",
                icon: "success"
              });
              $('#hrconfig_table').DataTable().ajax.reload();         
            }, 50);
          }
        })
      }
    });
  });


    let allStatus = [{id:'A', text:'Active'}, {id:'I', text:'Inactive'}];
    let allAction = [{id:'INSERT OR UPDATE', text:'Insert Or Update'}, {id:'INSERT', text:'Insert'}, {id:'UPDATE', text:'Update'}, {id:'VIEW', text:'View'}];

    $(document).on('click', '#new_config_survey', function () {
        var content = jQuery("#sample_table_config_survey tr"),
        size = global_id_config_survey_update++,
        element = null,
        element = content.clone();
        element.attr('id','surv-'+size);
        element.attr('class','surv');
        element.find('.delete-record-survey').attr('data-id', size);
        element.find('.id_config_survey_update').attr('id', 'survey_' + size + '_id_config_survey_update');
        element.find('.id_config_survey_update').attr('name', 'survey[' + size + '][id_config_survey_update]');

        element.find('.id_survey_header')
            .attr('id', 'survey_' + size + '_id_survey_header')
            .attr('name', 'survey[' + size + '][id_survey_header]')
            .attr('size', size)
            .prepend('<option selected></option>').select2({
                placeholder: "Select Survey",
                allowClear: true,
                data: global_id_survey_header
            });
        element.find('.id_survey_header_error').attr('id', 'survey_' + size + '_id_survey_headerError');

        element.find('.id_survey_history')
            .attr('id', 'survey_' + size + '_id_survey_history')
            .attr('name', 'survey[' + size + '][id_survey_history]')
            .attr('size', size)
            .prepend('<option selected></option>').select2({
                placeholder: "Select Period",
                data: []
            });
        element.find('.id_survey_history_error').attr('id', 'survey_' + size + '_id_survey_historyError');

        element.find('.id_survey_question')
            .attr('id', 'survey_' + size + '_id_survey_question')
            .attr('name', 'survey[' + size + '][id_survey_question]')
            .attr('size', size)
            .prepend('<option selected></option>').select2({
                placeholder: "Select Question",
                data: []
            });
        element.find('.id_survey_question_error').attr('id', 'survey_' + size + '_id_survey_questionError');

        element.find('.action_type')
            .attr('id', 'survey_' + size + '_action_type')
            .attr('name', 'survey[' + size + '][action_type]')
            .select2({
                placeholder: "Select Action",
                data: allAction
            });
        element.find('.action_type_error').attr('id', 'survey_' + size + '_action_typeError');

        element.find('.update_to_table')
            .attr('id', 'survey_' + size + '_update_to_table')
            .attr('name', 'survey[' + size + '][update_to_table]');
        element.find('.update_to_table_error').attr('id', 'survey_' + size + '_update_to_tableError');

        element.find('.update_to_column')
            .attr('id', 'survey_' + size + '_update_to_column')
            .attr('name', 'survey[' + size + '][update_to_column]');
        element.find('.update_to_column_error').attr('id', 'survey_' + size + '_update_to_columnError');

        element.find('.update_to_path')
            .attr('id', 'survey_' + size + '_update_to_path')
            .attr('name', 'survey[' + size + '][update_to_path]');
        element.find('.update_to_path_error').attr('id', 'survey_' + size + '_update_to_pathError');

        element.find('.unique_key')
            .attr('id', 'survey_' + size + '_unique_key')
            .attr('name', 'survey[' + size + '][unique_key]');
        element.find('.unique_key_error').attr('id', 'survey_' + size + '_unique_keyError');

        element.find('.expired_date')
            .attr('id', 'survey_' + size + '_expired_date')
            .attr('name', 'survey[' + size + '][expired_date]')
            .datepicker({uiLibrary: 'bootstrap4', format: 'yyyy-mm-dd'}); 
        element.find('.expired_date_error').attr('id', 'survey_' + size + '_expired_dateError');

        element.find('.status_survey')
            .attr('id', 'survey_' + size + '_status_survey')
            .attr('name', 'survey[' + size + '][status_survey]')
            .select2({
                placeholder: "Select Status",
                data: allStatus
            });
        element.find('.status_survey_error').attr('id', 'survey_' + size + '_status_surveyError');

        element.appendTo('#table_config_survey');
        $('#table_config_survey tr').each(function (index) {
            $(this).find('span.sn').html(index + 1);
        });
    });

    const getSurveyPeriod = async (idSurvey='', element='', value='') => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('general_setting/company_setting/hr_config_settings/getSurveyPeriod') ?>',
                method: "GET",
                data: {id_survey_header:idSurvey},
                success: function (res) {
                    let allSurveyPeriod = [];
                    $.each(res, function (i, val) {
                        name = `${val.date_start} - ${val.date_end}`;
                        allSurveyPeriod.push({id:val.id_survey_history, text:name});
                    });

                    $(element).html('').prepend('<option selected></option>').select2({
                        placeholder: "Select Period",
                        data: allSurveyPeriod,
                        allowClear: true,
                    });
                    if(value!=''){
                        $(element).val(value).trigger('change');
                    }
                },
            });
            return result;
        } catch (error) {
            getSurveyPeriod(idSurvey, element, value);
        }
    }

    const getSurveyQuestion = async (idSurvey='', element='', value='') => {
        try {
            let result;
            result = await $.ajax({
                url: '<?= url('general_setting/company_setting/hr_config_settings/getSurveyQuestion') ?>',
                method: "GET",
                data: {id_survey_header:idSurvey},
                success: function (res) {
                    let allSurveyQuestion = [];
                    $.each(res, function (i, val) {
                        name = `${val.sequence}. ${val.question}`;
                        allSurveyQuestion.push({id:val.id_survey_question, text:name});
                    });

                    $(element).html('').prepend('<option selected></option>').select2({
                        placeholder: "Select Question",
                        data: allSurveyQuestion,
                        allowClear: true,
                    });
                    if(value!=''){
                        $(element).val(value).trigger('change');
                    }
                },
            });
            return result;
        } catch (error) {
            getSurveyQuestion(idSurvey, element, value);
        }
    }

    const fillSurvey = async (data, index, dataPeriod='', dataQuestion='') => {
        let indexSurvey = index;
        let idSurvey = data;

        $(`#survey_${indexSurvey}_id_survey_header`).val(idSurvey).trigger('change',['trigger']);
        getSurveyPeriod(idSurvey, `#survey_${indexSurvey}_id_survey_history`, dataPeriod);
        getSurveyQuestion(idSurvey, `#survey_${indexSurvey}_id_survey_question`, dataQuestion);
    }

    $(document).on('change', '.id_survey_header', function (e, fromTrigger) {
        let indexSurvey = $(this).attr('size');
        let idSurvey = $(this).val();

        if(fromTrigger!='trigger'){ 
            getSurveyPeriod(idSurvey, `#survey_${indexSurvey}_id_survey_history`);
            getSurveyQuestion(idSurvey, `#survey_${indexSurvey}_id_survey_question`);
        }
    });
    
    $(document).on('click', '.delete-record-survey', function() {
        var id = jQuery(this).attr('data-id');
        $(`#surv-${id}`).remove();
    });

</script>
@endsection