<?php

namespace App\Models\Kpi\KpiSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaQuestion extends Model
{
	use HasFactory;
	
    protected $table = 'hr_pa_question';
	protected $primaryKey = 'id_pa_question';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_pa_question', 'sequence', 'description', 'notes', 'id_question_type', 'question_pa_type', 'id_question_group', 'status', 'id_company', 'created_by', 'updated_by'
    ];
			
	public static function getdata() {
		$sql = "SELECT hpq.*, mgd.description as question_group, STRING_AGG(hpa.sequence,', '  ORDER BY hpa.sequence DESC) AS answer 
					FROM hr_pa_question hpq
					LEFT JOIN hr_pa_answer hpa
					ON hpq.id_pa_question = hpa.id_pa_question AND hpa.id_company = ?
					LEFT JOIN master_general_data mgd 
					ON hpq.id_question_group = mgd.id_general_data
					WHERE hpq.id_company = ?
					GROUP BY hpq.id_pa_question, mgd.description 
					ORDER BY hpq.id_pa_question DESC";	
        $result = DB::select($sql,[session('id_company'),session('id_company')]);
        return $result;
    }
	
	public static function get_question_group() {
        $sql = "SELECT 
					mgd.id_general_data id,
					mgd.description text
				  FROM master_general_data mgd
				  WHERE mgd.id_general_type = 24 AND mgd.status = 'A' AND mgd.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_question_type() {
        $sql = "SELECT 
					mgd.id_general_data id,
					mgd.description text
				  FROM master_general_data mgd
				  WHERE mgd.id_general_type = 12 AND mgd.status = 'A' AND mgd.id_company =". session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_question_edit($data) {
        $result = [];
        $sql = "SELECT * 
					FROM hr_pa_question
				WHERE id_pa_question  = ?";
        $result = (Array) DB::select($sql, [$data['id_pa_question']])[0];
       // 	dd($result);
		$sql2 = "SELECT * FROM hr_pa_answer where id_pa_question = ? and id_company = ? ORDER BY id_pa_answer ASC";
        $result_menu2 = DB::select($sql2, [$data['id_pa_question'],session('id_company')]);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->groupBy('id_pa_answer')->toArray();
		
		$result['answer'] = [];
        foreach (array_keys($group_menu2) as $key => $value) {
            $result['answer'][] = [
                'id_pa_answer' => $group_menu2[$value][0]->id_pa_answer,
                'sequence' => $group_menu2[$value][0]->sequence,
                'desc_answer' => $group_menu2[$value][0]->description,
                'weight_score' => $group_menu2[$value][0]->weight_score,
                'is_corrected_answer' => $group_menu2[$value][0]->is_corrected_answer,         
            ];
        }
        return $result;
    }
}
