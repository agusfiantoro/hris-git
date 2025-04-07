<?php

namespace App\Models\Kpi\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class QualitativeAppraiser extends Model
{
	use HasFactory;
	
    protected $table = 'hr_qualitative_appraisers';
	protected $primaryKey = 'id_qualitative_appraisers';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_qualitative_appraisers', 'id_employee_appraisers', 'id_period', 'id_employee_participant', 'subtotal_score', 'maximum_score', 'appraisers_hierarchy', 'submitted', 'id_cross_company', 'status', 'id_company', 'transaction_type', 'id_recommendation_qualitative', 'created_by', 'updated_by'
    ];
	
	public static function getdata($period) {
        $sql = "SELECT DISTINCT hqa.id_qualitative_appraisers, hqa.id_employee_appraisers, he.name AS penilai, hqa.id_period, mp.description AS period, hqa.id_employee_participant, he2.nik_employee AS nik_dinilai, he2.name AS name_dinilai, hqa.appraisers_hierarchy, hqa.submitted, mjg.description AS grade, mc.company_name AS company_dinilai, hqa.subtotal_score, mp.period_code
				FROM hr_qualitative_appraisers hqa
				JOIN hr_employee he
				ON hqa.id_employee_appraisers = he.id_employee AND he.status = 'A'
				JOIN hr_employee he2
				ON hqa.id_employee_participant = he2.id_employee AND he2.status = 'A'
				JOIN master_position_detail mpd
				ON (he2.id_employee = mpd.id_employee OR he2.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_period mp
				ON hqa.id_period = mp.id_period AND mp.status = 'A'
				JOIN master_position_detail mpd2
				ON ((hqa.id_company = mpd2.id_company AND hqa.id_company = ".session('id_company').") OR hqa.id_cross_company = mpd2.id_company) AND (he.id_employee = mpd2.id_employee OR he.id_employee = mpd2.id_employee2)
				LEFT JOIN master_company mc
				ON hqa.id_company = mc.id_company
				WHERE he.id_user = ".session('id_user')." AND hqa.status = 'A'";
        $get = DB::select($sql);
		$get_res = collect($get);
		if($period){
			$result = $get_res->whereIn('period_code', $period);
		}
		else{
			$result = $get_res;
		}
        return $result;
    }
	
	public static function get_mail($id_employee,$period,$submit_type) {	
			$sql = "SELECT DISTINCT * FROM
					(
					SELECT hqa.id_qualitative_appraisers, hqa.id_employee_appraisers, CONCAT(he.name,' (',he.nik_employee,')') AS penilai, hqa.appraisers_hierarchy, hqa.id_period,
					hqa.id_employee_participant, CONCAT(he2.name,' (',he2.nik_employee,')') AS dinilai, mjg.description as job_grade, he.private_mail, hqa.submitted
					FROM hr_qualitative_appraisers hqa
					JOIN hr_employee he
					ON hqa.id_employee_appraisers = he.id_employee
					LEFT JOIN hr_employee he2
					ON hqa.id_employee_participant = he2.id_employee
					LEFT JOIN master_position_detail mpd
					ON (mpd.id_employee = he2.id_employee OR mpd.id_employee2 = he2.id_employee)
					LEFT JOIN master_position_routing mpr
					ON mpr.id_routing = mpd.id_position_routing
					LEFT JOIN master_job_grade mjg
					ON mpr.id_job_grade = mjg.id_job_grade
					WHERE hqa.id_company = ".session('id_company')." AND hqa.status = 'A' AND mpd.id_company = ".session('id_company')."
					ORDER BY he.name ASC
					) AS list";
			$get = DB::select($sql);
			$get_res = collect($get);
			if(count($id_employee) > 0){				
				if(count($submit_type) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('submitted', $submit_type);
				}
				else if(count($period) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period);
				}
				else if(count($period) > 0 && count($submit_type) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period)->whereIn('submitted', $submit_type);
				}
				else{
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee);
				}
			}
			else if(count($period) > 0){
				if(count($id_employee) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period);
				}
				else if(count($submit_type) > 0){
					$result = $get_res->whereIn('id_period', $period)->whereIn('submitted', $submit_type);
				}
				else if(count($id_employee) > 0 && count($submit_type) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period)->whereIn('submitted', $submit_type);
				}
				else{
					$result = $get_res->whereIn('id_period', $period);
				}
			}
			else if(count($submit_type) > 0){
				if(count($id_employee) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('submitted', $submit_type);
				}
				else if(count($period) > 0){
					$result = $get_res->whereIn('id_period', $period)->whereIn('submitted', $submit_type);
				}
				else if(count($id_employee) > 0 && count($period) > 0){
					$result = $get_res->whereIn('id_employee_appraisers', $id_employee)->whereIn('id_period', $period)->whereIn('submitted', $submit_type);
				}
				else{
					$result = $get_res->whereIn('submitted', $submit_type);
				}
			}
			else{
				$result = $get_res;
			}
			return $result;
	}
		
	public static function get_employee_mail() {
        $sql = "SELECT he.id_employee id,
				CONCAT(he.name,' (',he.nik_employee,')') AS text
				FROM hr_employee he
				WHERE he.status = 'A' AND he.id_company = ".session('id_company')."
				ORDER BY he.name ASC";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_grade($id_employee) {
		 $sql = "SELECT mpd.id_employee, mjg.id_job_grade, mjg.description 
				FROM master_position_detail mpd
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing AND mpd.secondary_position = false
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE (mpd.id_employee = ".$id_employee." OR mpd.id_employee2 = ".$id_employee.")";
		$result = DB::select($sql)[0];
        return $result;
	}
	
	public static function get_appraiser_new($data) {
        $result = [];
        $sql = "SELECT * 
					FROM hr_pa_question
				WHERE question_pa_type  = 'QUALITATIVE' AND id_company = ?
				ORDER BY sequence ASC";
        $result = (Array) DB::select($sql, [$data['id_company']]);
		foreach($result as $key=>$res){
			$sql2 = "SELECT * FROM hr_pa_answer where id_pa_question = ? and id_company = ? ORDER BY sequence DESC";
			$result_menu2 = DB::select($sql2, [$res->id_pa_question,$data['id_company']]);
			$ans[$res->sequence]=[
				'id_pa_question' => $res->id_pa_question,
				'sequence_soal' => $res->sequence,
				'soal' => $res->description,
				'id_qualitative_appraisers' => $data['id_qualitative_appraisers'],
				'answer' => [],
			];					
			foreach ($result_menu2 as $key => $value) {
				$ans[$res->sequence]['answer'][] = [					
					'id_pa_answer' => $value->id_pa_answer,
					'sequence' => $value->sequence,
					'desc_answer' => $value->description,
					'weight_score' => $value->weight_score,
					'is_corrected_answer' => $value->is_corrected_answer,         
				];
			}
		}
	//	dd($ans);
        return $ans;
    }
	
	public static function get_appraiser_edit($data) {
        $sql = "SELECT hpq.sequence, har.* FROM hr_appraisers_result har
				LEFT JOIN hr_pa_question hpq
				ON har.id_pa_question = hpq.id_pa_question
				WHERE har.id_qualitative_appraisers = ?
				ORDER BY hpq.sequence ASC";
        $res = (Array) DB::select($sql, [$data['id_qualitative_appraisers']]);
		foreach($res as $key=>$r){
			$ans[$r->sequence] = [
				'id_appraiser_results' => $r->id_appraiser_results,
				'id_pa_question' => $r->id_pa_question,
				'id_pa_answer' => $r->id_pa_answer,
				'desc_answer' => $r->description_answer,
			];
		}
		$result = json_decode(json_encode($ans),true);
        return $result;
    }
	
	public static function get_period() {
        $sql = "SELECT DISTINCT mp.period_code id, CONCAT(mp.description) text
				FROM master_period mp
				JOIN master_general_data mgd
				ON mp.id_function_type = mgd.id_general_data
				WHERE mgd.code ='HR' AND mgd.description = 'HR-QUALITATIVE' AND mp.status = 'A'";
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_review($period_code) {
        $sql = "SELECT hqp.id_period, hqa.id_employee_participant, he.nik_employee, he.name, hqp.total_percent  
				FROM hr_qualitative_appraisers hqa
				JOIN hr_employee he
				ON hqa.id_employee_participant = he.id_employee
				JOIN hr_employee he2
				ON hqa.id_employee_appraisers = he2.id_employee
				JOIN hr_qualitative_participant hqp
				ON hqa.id_employee_participant = hqp.id_employee_participant
				JOIN master_period mp
				ON hqp.id_period = mp.id_period
				WHERE he.status = 'A' AND mp.period_code = '".$period_code."' AND hqa.appraisers_hierarchy = 'direct' AND he2.id_user = ".session('id_user');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_detail_review($data) {
		$period_code = $data['period_code'];
		$id_employee_participant = $data['id_employee_participant'];
        $sql = "SELECT hpq.sequence, hpq.description AS soal,
					COUNT(hpa.sequence = 'Level 5' OR NULL) as Level_5,
					COUNT(hpa.sequence = 'Level 4' OR NULL) as Level_4, 
					COUNT(hpa.sequence = 'Level 3' OR NULL) as Level_3, 
					COUNT(hpa.sequence = 'Level 2' OR NULL) as Level_2, 
					COUNT(hpa.sequence = 'Level 1' OR NULL) as Level_1,
					((
						(COUNT(hpa.sequence = 'Level 5' OR NULL)*5)+
						(COUNT(hpa.sequence = 'Level 4' OR NULL)*4)+
						(COUNT(hpa.sequence = 'Level 3' OR NULL)*3)+
						(COUNT(hpa.sequence = 'Level 2' OR NULL)*2)+
						(COUNT(hpa.sequence = 'Level 1' OR NULL)*1))
						/ COUNT(hpa.sequence)::DECIMAL(5,1)) AS avg
				FROM hr_appraisers_result har
				JOIN hr_qualitative_appraisers hqa
				ON har.id_qualitative_appraisers = hqa.id_qualitative_appraisers
				JOIN hr_pa_question hpq
				ON har.id_pa_question = hpq.id_pa_question
				JOIN hr_pa_answer hpa
				ON har.id_pa_answer = hpa.id_pa_answer
				JOIN hr_employee he
				ON hqa.id_employee_participant = he.id_employee
				JOIN master_period mp
				ON hqa.id_period = mp.id_period
				WHERE hqa.id_employee_participant = ".$id_employee_participant." AND mp.period_code = '".$period_code."' AND hqa.transaction_type = 'PA'
				GROUP BY hpq.description, he.name, hpq.sequence
				ORDER BY hpq.sequence ASC";
        $result = DB::select($sql);
        return $result;
    }

}
