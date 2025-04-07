<?php

namespace App\Models\TalentManagement\TalentReco;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecoDetail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_talent_recommendation_detail';
	protected $primaryKey = 'id_talent_recommendation_detail';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_talent_recommendation_detail', 'id_talent_recommendation_header', 'id_employee', 'id_position_detail', 'id_dept', 'id_job_grade', 'id_region', 'id_branch', 
	'id_direct_chief', 'kpi_1_month_ago', 'kpi_2_month_ago', 'kpi_3_month_ago', 'kpi_4_month_ago', 'kpi_5_month_ago', 'kpi_6_month_ago', 'kpi_7_month_ago', 'kpi_8_month_ago', 'kpi_9_month_ago', 'kpi_10_month_ago', 'kpi_11_month_ago', 'kpi_12_month_ago', 'kpi_average', 'kpi_desc', 'id_kpi_total', 'id_grade_promotion', 'is_have_sp', 'is_have_kpk', 'id_batch', 'id_conclusion_psychotest', 'id_recruitment_answer_header', 'id_conclusion_assessment', 'id_survey_answer_user_header', 'engagement_survey_result', 'id_company', 'created_by', 'updated_by',
	'id_dept_psychogram', 'id_job_grade_psychogram'
    ];
		
}
