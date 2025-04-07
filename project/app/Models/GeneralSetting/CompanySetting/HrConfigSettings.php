<?php

namespace App\Models\GeneralSetting\CompanySetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrConfigSettings extends Model
{
    // use HasFactory;
	protected $table = 'hr_config_settings';
	protected $primaryKey = 'id_hr_config';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

	public static function get_position()
	{
		$data = DB::table('master_position_detail')
		->join('master_position_routing','master_position_routing.id_routing','=','master_position_detail.id_position_routing')
		->join('master_job_position','master_job_position.id_position','=','master_position_routing.id_position')
		->join('master_department','master_department.id_dept','=','master_job_position.id_dept')
		->select(
			\DB::RAW('master_position_detail.id_position_detail as id'),
			\DB::RAW('master_position_detail.description as text')
		)
		->where('master_position_detail.status','A')
		// ->where('master_position_detail.id_company',session('id_company'))
		// ->where('secondary_position', false)
		->where('master_department.department_code','150_HR')
		->where('master_position_detail.id_company', session('id_company'))
		// ->limit('20')
		->get();
		return $data;
	}
	public static function company()
	{
		$company = DB::table('master_company')
		->select(
			\DB::RAW('company_name')
		)
		->where('id_company',session('id_company'))
		->first();
		return $company;
	}
	public static function get_hr_config()
	{
		$data = DB::table('hr_config_settings')
		->leftJoin('master_company','master_company.id_company','=','hr_config_settings.id_company')
		// ->leftJoin('hr_config_email_recruitment','hr_config_email_recruitment.id_hr_config','=','hr_config_settings.id_hr_config')
		// ->leftJoin('hr_config_engagement_category','hr_config_engagement_category.id_hr_config','=','hr_config_settings.id_hr_config')
		->select(
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_config_settings.late_tolerance_limit as late_tolerance_limit'),
			\DB::RAW('hr_config_settings.maximum_late as maximum_late'),
			\DB::RAW('hr_config_settings.lock_geo_location as lock_geo_location'),
			\DB::RAW('hr_config_settings.allow_checkout_nextdays as allow_checkout_nextdays'),
			\DB::RAW('hr_config_settings.overtime_limit as overtime_limit'),
			\DB::RAW('hr_config_settings.maximum_overtime as maximum_overtime'),
			\DB::RAW('hr_config_settings.status as status'),
			\DB::RAW('hr_config_settings.id_hr_config')
		)
		->where('hr_config_settings.id_company',session('id_company'))
		->get();
		return $data;
	}
	public static function get_survey()
	{
		$data = DB::table('master_general_data')
		->leftJoin('hr_survey_header','hr_survey_header.id_survey_type','=','master_general_data.id_general_data')
		->select(
			\DB::RAW('hr_survey_header.id_survey_header as id'),
			\DB::RAW('hr_survey_header.description as text')
		)
		->where('master_general_data.status','A')
		->where('master_general_data.code','survey')
		->where('master_general_data.id_company',session('id_company'))
		->where('hr_survey_header.status','A')
		->where('hr_survey_header.published','true')
		->where('hr_survey_header.id_company',session('id_company'))
		->orderBy('hr_survey_header.description')
		->get();
		return $data;
	}
	public static function get_edit($id_hr_config)
	{
		$data = DB::table('hr_config_settings')
		->leftJoin('master_company','master_company.id_company','=','hr_config_settings.id_company')
		->select(
			\DB::RAW('master_company.company_name as company_name'),
			\DB::RAW('hr_config_settings.late_tolerance_limit as late_tolerance_limit'),
			\DB::RAW('hr_config_settings.maximum_late as maximum_late'),
			\DB::RAW('hr_config_settings.lock_geo_location as lock_geo_location'),
			\DB::RAW('hr_config_settings.allow_checkout_nextdays as allow_checkout_nextdays'),
			\DB::RAW('hr_config_settings.overtime_limit as overtime_limit'),
			\DB::RAW('hr_config_settings.maximum_overtime as maximum_overtime'),
			\DB::RAW('hr_config_settings.status as status'),
			\DB::RAW('hr_config_settings.id_hr_config'),
			\DB::RAW('hr_config_settings.maximum_cash_advance_request as maximum_cash_advance_request'),
			\DB::RAW('hr_config_settings.maximum_reimburse_expense as maximum_reimburse_expense'),
			\DB::RAW('hr_config_settings.id_grade_promotion as id_grade_promotion'),
			\DB::RAW('hr_config_settings.minimum_kpk_duration as minimum_kpk_duration'),
			\DB::RAW('hr_config_settings.minimum_sp_duration as minimum_sp_duration'),
			\DB::RAW('hr_config_settings.minimum_annual_kpi as minimum_annual_kpi'),
			\DB::RAW('hr_config_settings.id_position_routing as id_position_routing'),
			\DB::RAW('hr_config_settings.maximum_official_travel_request as maximum_official_travel_request'),
			\DB::RAW('hr_config_settings.maximum_cancel_travel_request as maximum_cancel_travel_request'),
			\DB::RAW('hr_config_settings.id_position_detail_email_travel as id_position_detail_email_travel'),
			\DB::RAW('hr_config_settings.id_company as id_company'),
			'hr_config_settings.maximum_attendace_correction_request',
			'hr_config_settings.erp_integration',
			'hr_config_settings.need_recommendation_form_flag',
		)
		->where('hr_config_settings.id_company',session('id_company'))
		->where('hr_config_settings.id_hr_config',$id_hr_config)
		->get();

		$recruitment = DB::table('hr_config_settings')
		->leftJoin('master_company','master_company.id_company','=','hr_config_settings.id_company')
		->leftJoin('hr_config_email_recruitment','hr_config_email_recruitment.id_hr_config','=','hr_config_settings.id_hr_config')
		->select(
			\DB::RAW('hr_config_email_recruitment.id_config_email_recruitment as id_config_email_recruitment'),
			\DB::RAW('hr_config_email_recruitment.id_position_detail as id_position_detail'),
			\DB::RAW('hr_config_email_recruitment.recruitment_email as recruitment_email'),
			\DB::RAW('hr_config_email_recruitment.status as status')
		)
		->where('hr_config_email_recruitment.id_company',session('id_company'))
		->where('hr_config_email_recruitment.id_hr_config',$id_hr_config)
		->orderBy('hr_config_email_recruitment.id_config_email_recruitment','DESC')
		->get();

		$talent = DB::table('hr_config_settings')
		->leftJoin('master_company','master_company.id_company','=','hr_config_settings.id_company')
		->leftJoin('hr_config_engagement_category','hr_config_engagement_category.id_hr_config','=','hr_config_settings.id_hr_config')
		->select(
			\DB::RAW('hr_config_engagement_category.id_config_engagement_category as id_config_engagement_category'),
			\DB::RAW('hr_config_engagement_category.id_survey_header as id_survey_header'),
			\DB::RAW('hr_config_engagement_category.min_value as min_value'),
			\DB::RAW('hr_config_engagement_category.max_value as max_value'),
			\DB::RAW('hr_config_engagement_category.description as description'),
			\DB::RAW('hr_config_engagement_category.status as status')
		)
		->where('hr_config_engagement_category.id_company',session('id_company'))
		->where('hr_config_engagement_category.id_hr_config',$id_hr_config)
		->orderBy('hr_config_engagement_category.id_config_engagement_category','DESC')
		->get();

		$survey = DB::table('hr_config_survey_update')
			->where('id_company', session('id_company'))
			->where('id_hr_config',$id_hr_config)
			->orderBy('id_config_survey_update')
			->get();
			
		return ['data'=>$data,'recruitment'=>$recruitment,'talent'=>$talent,'survey'=>$survey];
	}

	public static function get_survey_verification()
	{
		$data = DB::table('master_general_data')
		->leftJoin('hr_survey_header','hr_survey_header.id_survey_type','=','master_general_data.id_general_data')
		->select('hr_survey_header.id_survey_header as id', 'hr_survey_header.description as text')
		->where('hr_survey_header.id_company', session('id_company'))
		->where('hr_survey_header.status', 'A')
		->where('hr_survey_header.survey_category', 'Survey')
		->orderBy('hr_survey_header.description')
		->get();
		return $data;
	}

	public static function get_grade_promotion()
	{
		$data = DB::table("master_grade_promotion")
		->join("master_job_grade", "master_grade_promotion.id_job_grade", "=", "master_job_grade.id_job_grade", '')
		->select(
			"id_grade_promotion as id", 
			\DB::RAW("concat(master_job_grade.description, ' - ', master_grade_promotion.code) as text"),
		)
		->where("master_grade_promotion.id_company", session('id_company'))
		->where("master_grade_promotion.status", 'A')
		->get();

		return $data;
	}

	public static function get_position_routing()
	{
		$data = DB::table('master_position_routing')
		->select(
			\DB::RAW('id_routing as id'),
			\DB::RAW("description as text")
		)
		->where('id_company', session('id_company'))
		->where('status', 'A')
		->get();
		return $data;
	}

	public static function get_position_detail()
	{
		$data = DB::table('master_position_detail')
		->select(
			\DB::RAW('id_position_detail as id'),
			\DB::RAW("description as text")
		)
		->where('id_company', session('id_company'))
		->where('secondary_position', false)
		->where('status', 'A')
		->get();
		return $data;
	}
}
