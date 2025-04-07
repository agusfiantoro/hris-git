<?php

namespace App\Models\TalentManagement\TalentProgress;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RecoProgress extends Model
{
	use HasFactory;
	
    protected $table = 'hr_talent_recommendation_summary';
	protected $primaryKey = 'id_talent_recommendation_summary';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_talent_recommendation_summary', 'id_talent_recommendation_detail', 'period_date', 'kpi_1_month_ago', 'kpi_2_month_ago', 'kpi_3_month_ago', 'kpi_4_month_ago', 'kpi_5_month_ago', 'kpi_6_month_ago', 'kpi_7_month_ago', 'kpi_8_month_ago', 'kpi_9_month_ago', 'kpi_10_month_ago', 'kpi_11_month_ago', 'kpi_12_month_ago', 'kpi_desc', 'kpi_average', 'id_batch', 'id_conclusion_psychotest', 'id_recruitment_answer_header', 'id_conclusion_assessment', 'is_have_sp', 'is_have_kpk', 'eligibility_status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function getdata() {
		$sql = "SELECT htrh.notes AS reco_name, he.id_employee, CONCAT(he.name,' (',he.nik_employee,')') AS emp_name, 
				mpr.description AS pro_pos, htrs.id_talent_recommendation_summary, htrs.id_talent_recommendation_detail, 
				pmb.batch_name, htrs.kpi_desc, htrs.kpi_average, mgd.description AS potencies, mgd2.description AS competencies,
				htrd.engagement_survey_result AS eng_level
				FROM hr_talent_recommendation_summary htrs
				JOIN hr_talent_recommendation_detail htrd
				ON htrs.id_talent_recommendation_detail = htrd.id_talent_recommendation_detail
				JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				LEFT JOIN hr_employee he
				ON htrd.id_employee = he.id_employee
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				LEFT JOIN psycho_master_batch pmb
				ON htrs.id_batch = pmb.id_batch 
				LEFT JOIN master_general_data mgd
				ON htrs.id_conclusion_psychotest = mgd.id_general_data
				LEFT JOIN master_general_data mgd2
				ON htrs.id_conclusion_assessment = mgd2.id_general_data
				WHERE htrs.id_company = ? AND htrh.status = 'A'
				ORDER BY htrs.kpi_desc DESC, he.name ASC";	
        $result = DB::select($sql,[session('id_company')]);
		foreach($result as $key=>$val){
			$x = explode(" - ",$val->kpi_desc);
			$result[$key]->kpi_desc =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
		}	
        return $result;
    }
	
	public static function gen_batch($data) {
		$batch_name = $data['batch_name'];
		$id_company = $data['id_company'];
	//	$id_summary = $data['id_summary'];
		$id_branch = $data['id_branch'];
		$start_date = $data['start_date'];
		$end_date = $data['end_date'];
		$location = $data['location'];
		if($data['id_summary_batch'] != null){
			$id_summary = "array[".$data['id_summary_batch']."]";
		}
		else{
			$id_summary = 'null';
		}
		$result = DB::select("select * from  spgeneratepsychobatchtalentsummary(?, ".$id_summary.", ?, ?, ?, ?, ?)",
			[$id_company,$id_branch,$start_date,$end_date,$location,$batch_name]
		);
		
		return $result;
	}
	
	public static function gen_bei($data) {
		$id_company = $data['id_company'];
		$int_type = $data['int_type'];
		$int_date = $data['int_date'];
		if($data['id_summary_bei'] != null){
			$id_summary = "array[".$data['id_summary_bei']."]";
		}
		else{
			$id_summary = 'null';
		}
		$result = DB::select("select * from  spgenerateassessmentinterviewtalentsummary(?, ".$id_summary.", ?, ?)",
			[$id_company,$int_type,$int_date]
		);
		
		return $result;
	}
	
}
