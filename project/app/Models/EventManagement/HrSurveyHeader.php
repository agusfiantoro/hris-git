<?php

namespace App\Models\EventManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyHeader extends Model {
	
	protected $table = 'hr_survey_header';
	protected $primaryKey = 'id_survey_header';

	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';

	protected $fillable = [
		'id_survey_header', 'reference_number', 'description', 'id_employee_request', 'id_question_type', 'id_survey_type', 'survey_category', 'start_date', 'end_date', 'published', 'notes', 'status', 'id_company', 'created_by', 'updated_by'
	];

	public static function getdata() {
		$data = DB::table('hr_survey_header as hsh')
				->leftJoin('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
				->leftJoin('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
				->leftJoin('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
				->select('hsh.*', 'he.name', 'mgd.description as question_type', 'mgd2.description as survey_type')
				->where('hsh.id_company', session('id_company'))
				->where('hsh.survey_category', 'LMS')
				->orderBy('hsh.id_survey_header', 'desc')
				->get();
		return $data;
	}
	public static function getdata_answer() {
		$data = DB::table('master_survey_answer as msa')
				->select('msa.*')
				->where('msa.id_company', session('id_company'))
				->where('answer_group_type', 'LMS')
				->get();
		return $data;
	}
	
	public static function get_company() {
		$data = DB::table('master_company as mc')
				->select('mc.id_company as id', 'mc.company_name as text')
				->where('mc.id_company', session('id_company'))
				->get();
		return $data;
	}
	
	public static function get_employee($id_user=null) {
		$data = DB::table('hr_employee')
				->select('id_employee as id', 'name as text')
				->where('id_company', session('id_company'))
				->where('status', 'A')
				->orderBy('name');
		if($id_user){
			$data->where('id_user', $id_user);
		}
		return $data->get();
	}
	
	public static function get_question_type() {
		$data = DB::table('master_general_data')
				->select('id_general_data as id', 'description as text', 'code')
				->where('id_company', session('id_company'))
				->where('status', 'A')
				->where('id_general_type', '12')
				->get();
		return $data;
	}

	public static function get_survey_type() {
		$data = DB::table('master_general_data')
				->select('id_general_data as id', 'description as text', 'code')
				->where('id_company', session('id_company'))
				->where('status', 'A')
				->where('id_general_type', '18')
				->where('code', 'quiz')
				->get();
		return $data;
	}

	public static function get_answer() {
		$data = DB::table('master_survey_answer')
				->select('id_answer as id', 'description_answer as text')
				->where('id_company', session('id_company'))
				->where('status', 'A')
				->where('answer_group_type', 'LMS')
				->get();
		return $data;
	}

	public static function get_edit_answer($data) {
		$result = [];
		$sql = "SELECT *
				FROM master_survey_answer
				WHERE id_answer  = ?";
		$result = (Array) DB::select($sql, [$data['id_answer']])[0];
		return $result;
	}

	public static function get_survey_edit($idSurveyHeader) {
		$getQuizHeader = DB::table('hr_survey_header as hsh')
			->leftJoin('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
			->leftJoin('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
			->leftJoin('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
			->select('hsh.*', 'he.name', 'mgd.description as question_type', 'mgd2.description as survey_type')
			->where('hsh.id_company', session('id_company'))
			->where('hsh.survey_category', 'LMS')
			->where('hsh.id_survey_header', $idSurveyHeader)
			->first();

		$getQuizQuestion = DB::table('hr_survey_question as hsq')
			->selectRaw('hsq.id_survey_header, hsq.id_survey_question, hsq.sequence,  hsq.question, hsq.note, hsq.id_question_type, hsq.id_question_group, hsq.status as question_status')
			->where('hsq.id_survey_header', $idSurveyHeader)
			->orderBy('hsq.sequence')
			->get();


		if($getQuizQuestion->count() > 0){
			$allAnswer = [];
			$allIdSurveyQuestion = $getQuizQuestion->pluck('id_survey_question')->all();
			foreach ($allIdSurveyQuestion as $k => $val) {
				$allAnswer[$val] = [];
			}
			$getQuizAnswer = DB::table('hr_survey_answer as hsa')
				->selectRaw('hsa.id_survey_question, hsa.id_survey_answer, hsa.id_answer, hsa.is_corrected_answer, hsa.score_answer, hsa.suggested_answer as description_answer, hsa.status as answer_status')
				->whereIn('id_survey_question', $allIdSurveyQuestion)
				->orderBy('hsa.id_survey_answer')
				->get();

			if($getQuizAnswer->count() > 0){
				foreach ($getQuizAnswer as $k => $val) {
					$allAnswer[$val->id_survey_question][] = $getQuizAnswer[$k];
				}
			}
			foreach ($getQuizQuestion as $k => $val) {
				$getQuizQuestion[$k]->answers = $allAnswer[$val->id_survey_question];
			}

			$getQuizHeader->question = $getQuizQuestion->toArray();
		} else {
			$getQuizHeader->question = [];
		}

		$result = $getQuizHeader;
		return $result;
	}
	
}
