<?php

namespace App\Http\Controllers\GeneralSetting\CompanySetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\GeneralSetting\CompanySetting\HrConfigSettings;
use App\Models\GeneralSetting\CompanySetting\HrConfigEmailRecruitment;
use App\Models\GeneralSetting\CompanySetting\HrConfigTalent;
use App\Models\GeneralSetting\CompanySetting\HrConfigSurvey;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class HrConfigSettingsController extends Controller
{
	public function index(Request $request)
	{
		$cekHrConfig = HrConfigSettings::where('id_company',session('id_company'))
		->first();
		if ($request->ajax()) {
			$data = HrConfigSettings::get_hr_config($request);
			return DataTables::of($data)
			->addIndexColumn()
			->addColumn('', function($data) {
				$a = '';
				return $a;
			})
			->addColumn('action', function($data) {
				$button = '<button type="button" name="edit" id="" class="btn btn-primary btn-sm btn-edit" more_id="'.$data->id_hr_config.'" title="Edit"><span class="fas fa-edit"></span></button> ';
				$button .= '<button type="button" name="delete" id="" class="btn-del btn btn-danger btn-sm" more_id="'.$data->id_hr_config.'" title="Delete"><span class="far fa-trash-alt"></span></button>';
				return $button;
			})
			->rawColumns(['action'])
			->make(true);
		}
		return view('general_setting.company_setting.hr_config_setting.index',compact('cekHrConfig'));
	}
	public function get_data()
	{
		$position = HrConfigSettings::get_position();
		$company = HrConfigSettings::company();
		$survey = HrConfigSettings::get_survey_verification();
		return response()->json(['position'=>$position,'company'=>$company,'survey'=>$survey]);
	}
	public function save(Request $request)
	{
		$request->validate([
			// 'late_tolerance_limit' => 'required',
			'maximum_late' => 'required',
			// 'lock_geo_location' => 'required',
			// 'allow_checkout_nextdays' => 'required',
			// 'overtime_limit' => 'required',
			'maximum_overtime' => 'required',
			'id_grade_promotion' => 'required',
			'id_position_routing' => 'required',
			'minimum_sp_duration' => 'required',
			'minimum_annual_kpi' => 'required',
			'minimum_kpk_duration' => 'required',
			'maximum_cash_advance_request' => 'required',
			'maximum_reimburse_expense' => 'required',
			'maximum_official_travel_request' => 'required',
			'maximum_cancel_travel_request' => 'required',
			'id_config_email_rec' => 'nullable',
			// Talent
			'talent.*.id_survey_header' => 'required',
			'talent.*.min_values' => 'required',
			'talent.*.max_values' => 'required',
			'talent.*.descriptions' => 'required',
			'survey.*.id_survey_header' => 'required',
			'survey.*.id_survey_history' => 'required',
			'survey.*.id_survey_question' => 'required',
			'survey.*.action_type' => 'required',
			'survey.*.update_to_table' => 'required',
			'survey.*.update_to_column' => 'required',
			'survey.*.status_survey' => 'required',
		],[
			// 'late_tolerance_limit.required' => 'The Late Limit field is required.',
			'maximum_late.required' => 'The Max Late field is required.',
			// 'maximum_late.int' => 'The Max Late must be an integer.',
			// 'overtime_limit.required' => 'The Overtime Limit field is required.',
			'maximum_overtime.required' => 'The Max Overtime field is required.',
			// 'maximum_overtime.int' => 'The Max Overtime must be an integer.',
			// 'lock_geo_location.required' => 'The Lock GPS field is required.',
			// 'allow_checkout_nextdays.required' => 'The Allow Next Day Date field is required.',
			// Talent
			'talent.*.id_survey_header.required' => 'The Survey field is required.',
			'talent.*.min_values.required' => 'The Min Value field is required.',
			'talent.*.max_values.required' => 'The Max Value field is required.',
			'talent.*.descriptions.required' => 'The Description field is required.',

			'survey.*.id_survey_header.required' => 'The Survey field is required.',
			'survey.*.id_survey_history.required' => 'The Period field is required.',
			'survey.*.id_survey_question.required' => 'The Question field is required.',
			'survey.*.update_to_table.required' => 'The Table field is required.',
			'survey.*.update_to_column.required' => 'The Column field is required.',
			'id_grade_promotion.required' => 'The Grade Promotion field is required',
		]);
		try {
			DB::beginTransaction();
			if (!empty($request->late_tolerance_limit)) {
				$late_tolerance_limit = true;
			}else{
				$late_tolerance_limit = false;
			}
			if (!empty($request->lock_geo_location)) {
				$lock_geo_location = true;
			}else{
				$lock_geo_location = false;
			}
			if (!empty($request->allow_checkout_nextdays)) {
				$allow_checkout_nextdays = true;
			}else{
				$allow_checkout_nextdays = false;
			}
			if (!empty($request->overtime_limit)) {
				$overtime_limit = true;
			}else{
				$overtime_limit = false;
			}
			$data = New HrConfigSettings();
			$data -> late_tolerance_limit = $late_tolerance_limit;
			$data -> maximum_late = $request->maximum_late;
			$data -> lock_geo_location = $lock_geo_location;
			$data -> allow_checkout_nextdays = $allow_checkout_nextdays;
			$data -> overtime_limit = $overtime_limit;
			$data -> maximum_overtime = $request->maximum_overtime;
			$data -> maximum_attendace_correction_request = $request->maximum_attendace_correction_request;
			$data -> erp_integration = !empty($request->erp_integration);
			$data -> need_recommendation_form_flag = !empty($request->need_recommendation_form);
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> save();
			if (!empty($request->config)) {
				foreach ($request->config as $key => $value) {
					if (empty($value['id_position_detail'])) {
						$id_position_detail = NULL;
						$request->validate([
							'config.'.$key.'.email' => 'required'
						],[
							'config.'.$key.'.email.required' => 'The Email field is required.'
						]);
					}else{
						$id_position_detail = $value['id_position_detail'];
					}
					$recruitment = New HrConfigEmailRecruitment();
					$recruitment -> id_hr_config = $data->id_hr_config;
					$recruitment -> id_position_detail = $id_position_detail;
					$recruitment -> recruitment_email = $value['email'];
					$recruitment -> status = $value['status_recruitment'];
					$recruitment -> id_company = session('id_company');
					$recruitment -> created_by = session('id_user');
					$recruitment -> save();
				}
			}
			if (!empty($request->talent)) {
				foreach ($request->talent as $key => $values) {
					$recruitment = New HrConfigTalent();
					$recruitment -> id_hr_config = $data->id_hr_config;
					$recruitment -> id_survey_header = $values['id_survey_header'];
					$recruitment -> min_value = $values['min_values'];
					$recruitment -> max_value = $values['max_values'];
					$recruitment -> description = $values['descriptions'];
					$recruitment -> status = $values['status_talent'];
					$recruitment -> id_company = session('id_company');
					$recruitment -> created_by = session('id_user');
					$recruitment -> save();
				}
			}

			if($request->survey){
				foreach ($request->survey as $k => $val) {
					$dataSurvey = [
						'id_hr_config' => $data->id_hr_config ?? null,
						'id_survey_header' => $val['id_survey_header'] ?? null,
						'id_survey_history' => $val['id_survey_history'] ?? null,
						'id_survey_question' => $val['id_survey_question'] ?? null,
						'action_type' => $val['action_type'] ?? null,
						'update_to_table' => $val['update_to_table'] ?? null,
						'update_to_column' => $val['update_to_column'] ?? null,
						'update_to_path' => $val['update_to_path'] ?? null,
						'expired_date' => $val['expired_date'] ?? null,
						'id_company' => session('id_company') ?? null,
						'status' => $val['status_survey'] ?? null,
					];
					if($val['id_config_survey_update']){
						$dataSurvey['updated_by'] = session('id_user');
						HrConfigSurvey::where('id_config_survey_update', $val['id_config_survey_update'])->update($dataSurvey);
					} else {
						$dataSurvey['created_by'] = session('id_user');
						HrConfigSurvey::insert($dataSurvey);
					}
				}
			}

			if($request->survey){
				foreach ($request->survey as $k => $val) {
					$dataSurvey = [
						'id_hr_config' => $request->id_hr_config ?? null,
						'id_survey_header' => $val['id_survey_header'] ?? null,
						'id_survey_history' => $val['id_survey_history'] ?? null,
						'id_survey_question' => $val['id_survey_question'] ?? null,
						'action_type' => $val['action_type'] ?? null,
						'update_to_table' => $val['update_to_table'] ?? null,
						'update_to_column' => $val['update_to_column'] ?? null,
						'update_to_path' => $val['update_to_path'] ?? null,
						'expired_date' => $val['expired_date'] ?? null,
						'id_company' => session('id_company'),
						'status' => $val['status_survey'] ?? null,
					];
					if($val['id_config_survey_update']){
						$dataSurvey['updated_by'] = session('id_user');
						HrConfigSurvey::where('id_config_survey_update', $val['id_config_survey_update'])->update($dataSurvey);
					} else {
						$dataSurvey['created_by'] = session('id_user');
						HrConfigSurvey::insert($dataSurvey);
					}
				}
			}
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Hr Config Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
		}
	}
	public function get_edit($id_hr_config)
	{
		$results = HrConfigSettings::get_edit($id_hr_config);
		$data = $results['data'];
		$data[0]->id_grade_promotion = explode(",", trim($data[0]->id_grade_promotion, "{}"));
		$data[0]->id_position_routing = explode(",", trim($data[0]->id_position_routing, "{}"));
		$data[0]->id_position_detail_email_travel = explode(",", trim($data[0]->id_position_detail_email_travel, "{}"));
		$recruitment = $results['recruitment'];
		$talent = $results['talent'];
		$survey = $results['survey'];
		$gradePromotions = HrConfigSettings::get_grade_promotion();
		$positionRouting = HrConfigSettings::get_position_routing();
		$positionDetail = HrConfigSettings::get_position_detail();
		return response()->json([
			'data'=>$data,
			'recruitment'=>$recruitment,
			'talent'=>$talent, 
			'survey'=>$survey,
			'grade_promotions'=>$gradePromotions,
			'position_routing' => $positionRouting,
			'position_detail' => $positionDetail
		]);
	}
	public function edit(Request $request)
	{
		// dd($request->all());
		$request->validate([
			// 'late_tolerance_limit' => 'required',
			'maximum_late' => 'required',
			// 'lock_geo_location' => 'required',
			// 'allow_checkout_nextdays' => 'required',
			// 'overtime_limit' => 'required',
			'maximum_overtime' => 'required',
			'id_grade_promotion' => 'required',
			'id_position_routing' => 'required',
			'minimum_sp_duration' => 'required',
			'minimum_annual_kpi' => 'required',
			'minimum_kpk_duration' => 'required',
			'maximum_cash_advance_request' => 'required',
			'maximum_reimburse_expense' => 'required',
			'maximum_official_travel_request' => 'required',
			'maximum_cancel_travel_request' => 'required',
			'maximum_attendace_correction_request' => 'required',
			'id_config_email_rec' => 'nullable',
			// Talent
			'talent.*.id_survey_header' => 'required',
			'talent.*.min_values' => 'required',
			'talent.*.max_values' => 'required',
			'talent.*.descriptions' => 'required',

			'survey.*.id_survey_header' => 'required',
			'survey.*.id_survey_history' => 'required',
			'survey.*.id_survey_question' => 'required',
			'survey.*.action_type' => 'required',
			'survey.*.update_to_table' => 'required',
			'survey.*.update_to_column' => 'required',
			'survey.*.status_survey' => 'required',
			'survey.*.expired_date' => 'required',
		],[
			// 'late_tolerance_limit.required' => 'The Late Limit field is required.',
			'maximum_late.required' => 'The Max Late field is required.',
			// 'maximum_late.int' => 'The Max Late must be an integer.',
			// 'overtime_limit.required' => 'The Overtime Limit field is required.',
			'maximum_overtime.required' => 'The Max Overtime field is required.',
			// 'maximum_overtime.int' => 'The Max Overtime must be an integer.',
			// 'lock_geo_location.required' => 'The Lock GPS field is required.',
			// 'allow_checkout_nextdays.required' => 'The Allow Next Day Date field is required.',
			// Talent
			'talent.*.id_survey_header.required' => 'The Survey field is required.',
			'talent.*.min_values.required' => 'The Min Value field is required.',
			'talent.*.max_values.required' => 'The Max Value field is required.',
			'talent.*.descriptions.required' => 'The Description field is required.',

			'survey.*.id_survey_header.required' => 'The Survey field is required.',
			'survey.*.id_survey_history.required' => 'The Period field is required.',
			'survey.*.id_survey_question.required' => 'The Question field is required.',
			'survey.*.update_to_table.required' => 'The Table field is required.',
			'survey.*.update_to_column.required' => 'The Column field is required.',
			'survey.*.expired_date.required' => 'The Column field is required.',
			'id_grade_promotion.required' => 'The Grade Promotion field is required',
		]);

		try {
			DB::beginTransaction();
			if (!empty($request->late_tolerance_limit)) {
				$late_tolerance_limit = true;
			}else{
				$late_tolerance_limit = false;
			}
			if (!empty($request->lock_geo_location)) {
				$lock_geo_location = true;
			}else{
				$lock_geo_location = false;
			}
			if (!empty($request->allow_checkout_nextdays)) {
				$allow_checkout_nextdays = true;
			}else{
				$allow_checkout_nextdays = false;
			}
			if (!empty($request->overtime_limit)) {
				$overtime_limit = true;
			}else{
				$overtime_limit = false;
			}
			$data = HrConfigSettings::where('id_hr_config',$request->id_hr_config)->first();
			$data -> late_tolerance_limit = $late_tolerance_limit;
			$data -> maximum_late = $request->maximum_late;
			$data -> lock_geo_location = $lock_geo_location;
			$data -> allow_checkout_nextdays = $allow_checkout_nextdays;
			$data -> overtime_limit = $overtime_limit;
			$data -> maximum_overtime = $request->maximum_overtime;
			$data -> maximum_cash_advance_request = $request->maximum_cash_advance_request;
			$data -> minimum_sp_duration = $request->minimum_sp_duration;
			$data -> minimum_annual_kpi = $request->minimum_annual_kpi;
			$data -> minimum_kpk_duration = $request->minimum_kpk_duration;
			$data -> maximum_reimburse_expense = $request->maximum_reimburse_expense;
			$data -> maximum_official_travel_request = $request->maximum_official_travel_request;
			$data -> maximum_cancel_travel_request = $request->maximum_cancel_travel_request;
			$data -> maximum_attendace_correction_request = $request->maximum_attendace_correction_request;
			$data -> erp_integration = !empty($request->erp_integration);
			$data -> need_recommendation_form_flag = !empty($request->need_recommendation_form);
			$data -> id_grade_promotion = str_replace("]", "}", str_replace("[", "{", json_encode($request->id_grade_promotion)));
			$data -> id_position_routing = str_replace("]", "}", str_replace("[", "{", json_encode($request->id_position_routing)));
			$data -> id_position_detail_email_travel = str_replace("]", "}", str_replace("[", "{", json_encode($request->id_position_detail_email_travel)));
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			$data -> updated_by = session('id_user');
			$data -> save();
			if (!empty($request->config)) {
				foreach ($request->config as $key => $value) {
					if (empty($value['id_position_detail'])) {
						$id_position_detail = NULL;
						$request->validate([
							'config.'.$key.'.email' => 'required'
						],[
							'config.'.$key.'.email.required' => 'The Email field is required.'
						]);
					}else{
						$id_position_detail = $value['id_position_detail'];
					}
					if ($value['id_config_email_recruitment_rec'] == "") {
						$recruitment = New HrConfigEmailRecruitment();						
					}else{
						$recruitment = HrConfigEmailRecruitment::where('id_config_email_recruitment',$value['id_config_email_recruitment_rec'])
						->first();		
					}
					$recruitment -> id_hr_config = $request->id_hr_config;
					$recruitment -> id_position_detail = $id_position_detail;
					$recruitment -> recruitment_email = $value['email'];
					$recruitment -> status = $value['status_recruitment'];
					$recruitment -> id_company = session('id_company');
					$recruitment -> created_by = session('id_user');
					$recruitment -> save();
				}
			}
			if (!empty($request->id_config_email_rec)) {
				$id_config_email_rec = explode(",", $request->id_config_email_rec);
				$hapus_email_rec = HrConfigEmailRecruitment::whereIn('id_config_email_recruitment',$id_config_email_rec)
				->where('id_hr_config',$request->id_hr_config)
				->delete();
			}
			if (!empty($request->talent)) {
				foreach ($request->talent as $key => $values) {
					if ($values['id_config_engagement_category_tal'] == "") {
						$talent = New HrConfigTalent();						
					}else{
						$talent = HrConfigTalent::where('id_config_engagement_category',$values['id_config_engagement_category_tal'])
						->first();		
					}
					$talent -> id_hr_config = $data->id_hr_config;
					$talent -> id_survey_header = $values['id_survey_header'];
					$talent -> min_value = $values['min_values'];
					$talent -> max_value = $values['max_values'];
					$talent -> description = $values['descriptions'];
					$talent -> status = $values['status_talent'];
					$talent -> id_company = session('id_company');
					$talent -> created_by = session('id_user');
					$talent -> save();
				}
			}
			if (!empty($request->id_config_talent_tal)) {
				$id_config_talent_tal = explode(",", $request->id_config_talent_tal);
				$hapus_talent_tal = HrConfigTalent::whereIn('id_config_engagement_category',$id_config_talent_tal)
				->where('id_hr_config',$request->id_hr_config)
				->delete();
				// foreach ($hapus_talent_tal as $hapus_tal) {
				// 	HrConfigEmailRecruitment::where('id_config_email_recruitment',$hapus_tal->id_config_engagement_category)
				// 	->where('id_hr_config',$request->id_hr_config)
				// 	->delete();
				// }
			}

						
			$getConfigSurvey = HrConfigSurvey::where('id_hr_config', $request->id_hr_config)->get();
			$allIdConfigSurvey = $getConfigSurvey->pluck('id_config_survey_update')->all();
			$selectedIdConfig = [];
			if($request->survey){
				foreach ($request->survey as $k => $val) {
					$dataSurvey = [
						'id_hr_config' => $request->id_hr_config ?? null,
						'id_survey_header' => $val['id_survey_header'] ?? null,
						'id_survey_history' => $val['id_survey_history'] ?? null,
						'id_survey_question' => $val['id_survey_question'] ?? null,
						'action_type' => $val['action_type'] ?? null,
						'update_to_table' => $val['update_to_table'] ?? null,
						'update_to_column' => $val['update_to_column'] ?? null,
						'update_to_path' => $val['update_to_path'] ?? null,
						'unique_key' => @$val['unique_key'] ? true : null,
						'expired_date' => $val['expired_date'] ?? null,
						'id_company' => session('id_company'),
						'status' => $val['status_survey'] ?? null,
					];
					if($val['id_config_survey_update']){
						$selectedIdConfig[] = $val['id_config_survey_update'];
						$dataSurvey['updated_by'] = session('id_user');
						HrConfigSurvey::where('id_config_survey_update', $val['id_config_survey_update'])->update($dataSurvey);
					} else {
						$dataSurvey['created_by'] = session('id_user');
						HrConfigSurvey::insert($dataSurvey);
					}
				}
			}
			$diffIdConfigSurvey = collect($allIdConfigSurvey)->diff(collect($selectedIdConfig));
			if($diffIdConfigSurvey->count() > 0){
				HrConfigSurvey::whereIn('id_config_survey_update', $diffIdConfigSurvey)->delete();
			}
			
			DB::commit();
			return response()->json(['status'=>'true','message'=>'Hr Config Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
		}
	}
	public function destroy($id_hr_config)
	{
		$data = HrConfigSettings::where('id_hr_config',$id_hr_config)->first();
		if ($data) {
			$data -> delete();
		}
	}

	public function getSurveyPeriod	(Request $request)
	{
		$idSurvey = $request->id_survey_header ?? null;
		$getSurveyPeriod = DB::table('hr_survey_history as hsh')
            ->where('hsh.id_survey_header', $idSurvey)
            ->orderBy('hsh.start_date')
            ->get();

        if($getSurveyPeriod->count() > 0){
        	foreach ($getSurveyPeriod as $k => $val) {
        		$getSurveyPeriod[$k]->date_start = Carbon::parse($val->start_date)->format('d M Y');
        		$getSurveyPeriod[$k]->date_end = Carbon::parse($val->end_date)->format('d M Y');
        	}
        }

        $result = $getSurveyPeriod;
        return $result;
	}

	public function getSurveyQuestion(Request $request)
	{
		$idSurvey = $request->id_survey_header ?? null;
		$getSurveyQuestion = DB::table('hr_survey_question as hsq')
            ->where('hsq.id_survey_header', $idSurvey)
            ->orderBy('hsq.sequence')
            ->get();

        $result = $getSurveyQuestion;
        return $result;
	}
}
