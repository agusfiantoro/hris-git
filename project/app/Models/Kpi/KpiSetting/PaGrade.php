<?php

namespace App\Models\Kpi\KpiSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaGrade extends Model
{
	use HasFactory;
	
    protected $table = 'hr_grade_question';
	protected $primaryKey = 'id_grade_question';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_grade_question', 'id_job_grade', 'id_pa_question', 'id_minimum_score_level', 'value', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public static function get_question() {
        $sql = "SELECT 
			hpq.id_pa_question id,
			hpq.notes text
			FROM hr_pa_question hpq
		WHERE hpq.question_pa_type = 'QUALITATIVE' AND hpq.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_level($id_pa_question=null) {
		$where = '';

		if($id_pa_question){
			$where .= ' AND hpa.id_pa_question = '.$id_pa_question;
		}

        $sql = "SELECT 
			hpa.id_pa_answer id,
			hpa.sequence text,
			hpa.weight_score,
			hpa.id_pa_question
			FROM hr_pa_answer hpa
		WHERE hpa.id_company = ? $where
		ORDER BY hpa.sequence DESC";
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }

	public static function get_grade_edit($data) {
        $result = [];
        $sql = "SELECT id_job_grade, description 
					FROM master_job_grade
				WHERE id_job_grade  = ?";
        $result = (Array) DB::select($sql, [$data['id_job_grade']])[0];
       // 	dd($result);
		$sql2 = "SELECT * FROM hr_grade_question where id_job_grade = ? and id_company = ?";
        $result_menu2 = DB::select($sql2, [$data['id_job_grade'],session('id_company')]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_grade_question')->toArray();
		
		$result['pagrade'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['pagrade'][] = [
                'id_grade_question' => $group_menu2[$value][0]->id_grade_question,
                'id_pa_question' => $group_menu2[$value][0]->id_pa_question,
                'id_minimum_score_level' => $group_menu2[$value][0]->id_minimum_score_level,
                'value' => $group_menu2[$value][0]->value,
                'status' => $group_menu2[$value][0]->status,         
            ];
        }
        return $result;
    }

}
