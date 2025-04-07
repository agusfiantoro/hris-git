<?php

namespace App\Models\TalentManagement\TalentFunctional;

use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TalentFunctional extends Model
{
	use HasFactory;
		
	public function get_emp_interview($group_branch,$path_url) { 
		if($path_url != null){
			$idConclusion = " AND hrah.id_conclusion IS NOT NULL";
		}
		else{
			$idConclusion = " ";
		}
		if($group_branch == null){
			$branch = "";
		}
		else{
			$branch = " AND mpd.id_branch in(".$group_branch.")";
		}
		$sql = "SELECT he.id_employee, he.name, he.nik_employee, mpr2.description AS position,
				mpd.id_branch, he.image_attachment AS photo, mpr.description AS projected_pos,
				(
					SELECT mrqg.id_recruitment_question_group
					FROM public.master_recruitment_question_group mrqg
					JOIN master_general_data mgd
					ON mrqg.id_question_group = mgd.id_general_data 
					WHERE mrqg.id_company = ".session('id_company')." AND mrqg.question_type = 'Talent' AND mgd.code = 'BEI')
				AS id_recruitment_question_group, mgd2.description AS bei_conclusion
				FROM hr_recruitment_answer_header hrah
				JOIN hr_talent_recommendation_detail htrd
				ON hrah.id_employee = htrd.id_employee
				JOIN hr_talent_recommendation_header htrh
				ON htrd.id_talent_recommendation_header = htrh.id_talent_recommendation_header
				JOIN hr_employee he
				ON hrah.id_employee = he.id_employee AND he.id_company = ".session('id_company')."
				LEFT JOIN master_position_routing mpr
				ON htrh.id_position_routing = mpr.id_routing
				LEFT JOIN master_position_detail mpd
				ON (hrah.id_employee = mpd.id_employee OR hrah.id_employee = mpd.id_employee2) AND mpd.secondary_position = false
				LEFT JOIN master_position_routing mpr2
				ON mpd.id_position_routing = mpr2.id_routing
				LEFT JOIN master_general_data mgd2
				ON hrah.id_conclusion = mgd2.id_general_data
				WHERE htrh.status = 'A' AND hrah.id_company = ".session('id_company')." ".$idConclusion."
				ORDER BY mpr.description ASC, he.name ASC";
        $result = DB::select($sql);	
        return $result;
    }
	
	public static function get_edit_question($data) {
		$sql = "SELECT he.id_employee, he.name, he.nik_employee, mpr.description AS pos_route
				FROM master_position_detail mpd
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing AND mpr.status ='A'
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee AND he.status = 'A'
				WHERE he.id_company = ".session('id_company')." AND mpd.secondary_position = false
				AND he.id_employee = ? AND mpd.status = 'A'";
        $result = (Array) DB::select($sql, [$data['id_employee']])[0];
		
		$sql_group = "SELECT mrqg.id_question_group, mgd.code
						FROM master_recruitment_question_group mrqg 
						JOIN master_general_data mgd
						ON mrqg.id_question_group = mgd.id_general_data
						WHERE mrqg.id_company = ? AND mrqg.question_type = 'Talent' AND mrqg.status = 'A'";
		$result_menu1 = DB::select($sql_group, [session('id_company')])[0];
        $collect_menu1 = collect($result_menu1)->toArray();
		$result['question_group'] = $collect_menu1;
		
		$sql_answer = "SELECT hrah.* FROM public.hr_recruitment_answer_header hrah
						WHERE hrah.id_employee = ? AND hrah.id_recruitment_question_group = ?";
		$result_menu2 = DB::select($sql_answer, [$data['id_employee'],$data['id_group']]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_recruitment_answer_header')->toArray();
		$result['hr_answer'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['hr_answer'][] = [			
                'id_recruitment_answer_header' => $group_menu2[$value][0]->id_recruitment_answer_header,
                'interview_date' => $group_menu2[$value][0]->interview_date,
                'total_score' => $group_menu2[$value][0]->total_score,
                'id_conclusion' => $group_menu2[$value][0]->id_conclusion,
                'int_notes' => $group_menu2[$value][0]->notes,
			];
        }
	//	dd($result);
		return $result;
	}
	
	public static function get_question($data) {
        $result = [];
        $sql = "SELECT mrq.id_recruitment_question, mrq.id_recruitment_question_group, mrq.sequence, mrq.description, mrq.competency_group, mrq.category_group, mrq.id_question_type, mgd.code AS question_type,
				hrah.id_recruitment_answer_header, hrah.interview_date, hrah.total_score, hrah.id_conclusion, hrah.notes, hrad.essay_answer, hrad.id_recruitment_answer AS id_answer 
				FROM public.master_recruitment_question mrq
				LEFT JOIN public.master_recruitment_question_group mrqg
				ON mrq.id_recruitment_question_group = mrqg.id_recruitment_question_group AND mrqg.question_type = 'Talent'
				LEFT JOIN public.hr_recruitment_answer_header hrah
				ON mrqg.id_recruitment_question_group = hrah.id_recruitment_question_group AND hrah.id_employee = ?
				LEFT JOIN public.hr_recruitment_answer_detail hrad
				ON hrah.id_recruitment_answer_header = hrad.id_recruitment_answer_header AND mrq.id_recruitment_question = hrad.id_recruitment_question
				LEFT JOIN public.master_general_data mgd
				ON mrq.id_question_type = mgd.id_general_data
				WHERE mrqg.id_question_group  = ? AND mrq.id_company = ?
				ORDER BY mrq.sequence ASC";
        $result = (Array) DB::select($sql,[$data['id_employee'],$data['id_question_group'],session('id_company')]);
		foreach($result as $key=>$res){
			$sql2 = "SELECT rrqa.*, mra.sequence, mra.description 
					FROM public.relation_recruitment_question_answer rrqa
					LEFT JOIN public.master_recruitment_answer mra
					ON rrqa.id_recruitment_answer = mra.id_recruitment_answer
					WHERE rrqa.id_recruitment_question = ? AND rrqa.id_company = ?
					ORDER BY rrqa.id_recruitment_question ASC, mra.sequence ASC";
			$result_menu2 = DB::select($sql2, [$res->id_recruitment_question,session('id_company')]);
			$ans[$res->sequence]=[
				'id_recruitment_question' => $res->id_recruitment_question,
				'id_recruitment_question_group' => $res->id_recruitment_question_group,
				'sequence_soal' => $res->sequence,
				'competency_group' => $res->competency_group,
				'category_group' => $res->category_group,
				'question_type' => $res->question_type,
				'soal' => $res->description,
				'essay_answer' => $res->essay_answer,
				'id_answer' => $res->id_answer,
				'opt_answer' => [],
			];	
			foreach ($result_menu2 as $key => $value) {
				$ans[$res->sequence]['opt_answer'][] = [					
					'id_recruitment_answer' => $value->id_recruitment_answer,
					'desc_answer' => $value->description,
					'weight_score' => $value->weight_score,
					'is_corrected_answer' => $value->is_corrected_answer,         
				];
			}
		}
	//	dd($ans);
        return $ans;
    }
	
	public static function get_grade_dept($idEmployee) {
        $sql = "SELECT md.id_dept, mjg.id_job_grade 
				FROM master_position_detail mpd
				JOIN hr_employee he
				ON mpd.id_employee = he.id_employee AND mpd.secondary_position = false AND he.id_company = ?
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_department md
				ON mjp.id_dept = md.id_dept
				WHERE he.id_employee = ?";
        $result = DB::select($sql,[session('id_company'),$idEmployee])[0];
        return $result;
    }

}
