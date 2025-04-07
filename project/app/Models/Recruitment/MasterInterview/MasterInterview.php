<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterInterview extends Model
{
	use HasFactory;
	
    protected $table = 'master_recruitment_question_group';
	protected $primaryKey = 'id_recruitment_question_group';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_question_group', 'id_question_group', 'description', 'question_type', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
	public function getdata() {
		$sql = "SELECT mrqg.id_recruitment_question_group, mrqg.description, mrqg.status, 
				CONCAT(mgd.description,' (',mgd.code,')') AS rec_stage, mrqg.question_type  
				FROM public.master_recruitment_question_group mrqg
				LEFT JOIN public.master_general_data mgd
				ON mrqg.id_question_group = mgd.id_general_data
				WHERE mrqg.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function getdata_answer() {
		$sql = "SELECT mra.*
				FROM public.master_recruitment_answer mra
				WHERE mra.id_company = " . session('id_company'). "
				ORDER BY mra.sequence ASC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_stage() {   
		$sql = "SELECT mgd.id_general_data id, CONCAT(mgd.code,' (',mgd.description,')') text, mgd.code
				FROM public.master_general_data mgd
				LEFT JOIN public.master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_recruitment_stage' AND mgd.code IN('PRE','PSY','BEI','OBS','REF') AND mgd.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public function get_answer() {   
		$sql = "SELECT mra.id_recruitment_answer id, mra.description text
				FROM public.master_recruitment_answer mra
				WHERE mra.status = 'A' AND mra.id_company = ". session('id_company') ."
				ORDER BY mra.sequence ASC";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_edit_answer($data) {
        $result = [];
        $sql = "SELECT mra.*
                FROM public.master_recruitment_answer mra
				WHERE mra.id_recruitment_answer  = ?";
        $result = (Array) DB::select($sql, [$data['id_recruitment_answer']])[0];
       // 	dd($result);
        return $result;
    }
	
	public function get_question_type() {   
		$sql = "SELECT mgd.id_general_data id, mgd.description text, mgd.code
				FROM public.master_general_data mgd
				LEFT JOIN public.master_general_type mgt
				ON mgd.id_general_type = mgt.id_general_type
				WHERE mgt.general_type = 'master_question_type' AND mgd.id_company = " . session('id_company'). "
				ORDER BY mgd.sequence ASC ";
        $result = DB::select($sql);		 
        return $result;
    }
	
	public static function get_edit_interview($data) {
        $result = [];
        $sql = "SELECT mrqg.id_recruitment_question_group, mrqg.description, mrqg.id_question_group, mrqg.question_type, mrqg.status AS status_header, mrqg.id_company as company_header
				FROM master_recruitment_question_group mrqg
				WHERE mrqg.id_recruitment_question_group  = ?";
        $result = (Array) DB::select($sql, [$data['id_recruitment_question_group']])[0];

        $sql2 = "SELECT mrq.id_recruitment_question_group, mrq.id_recruitment_question, mrq.sequence,  mrq.description AS question, mrq.notes, mrq.id_question_type, mrq.category_group, mrq.competency_group, rrqa.id_recruitment_question_answer, rrqa.id_recruitment_answer, rrqa.is_corrected_answer, rrqa.weight_score
				FROM master_recruitment_question mrq
				LEFT JOIN relation_recruitment_question_answer rrqa
				ON rrqa.id_recruitment_question = mrq.id_recruitment_question
				WHERE mrq.id_recruitment_question_group = ?
                ORDER BY mrq.sequence ASC";
        $result_menu = DB::select($sql2, [$data['id_recruitment_question_group']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_recruitment_question')->toArray();

        $result['question'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
			$group_suggested_answer = collect($group_menu[$value])->groupBy('id_recruitment_answer')->toArray();
            $suggested_answer = array_keys($group_suggested_answer);
            $result['question'][] = [
                'id_recruitment_question' => $group_menu[$value][0]->id_recruitment_question,
                'id_question_type' => $group_menu[$value][0]->id_question_type,
                'sequence' => $group_menu[$value][0]->sequence,
                'competency_group' => $group_menu[$value][0]->competency_group,
                'category_group' => $group_menu[$value][0]->category_group,
                'question' => $group_menu[$value][0]->question,
                'suggested_answer' => $suggested_answer,
                'notes' => $group_menu[$value][0]->notes,
            ];
        }
        return $result;
    }
	
	public static function get_question_first($question_type) {
        $result = [];
        $sql = "SELECT mgd.code
				FROM public.master_general_data mgd
				WHERE mgd.id_general_data  = ?";
        $result = (Array) DB::select($sql, [$question_type])[0];
       // 	dd($result);
        return $result;
    }
		
}
