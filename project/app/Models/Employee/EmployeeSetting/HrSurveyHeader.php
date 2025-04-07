<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyHeader extends Model {
	
	protected $table = 'hr_survey_header';
    protected $primaryKey = 'id_survey_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_survey_header', 'reference_number', 'description', 'id_employee_request', 'id_question_type', 'id_survey_type', 'survey_category', 'start_date', 'end_date', 'published', 'status', 'id_company', 'created_by', 'updated_by', 'with_score', 'id_department', 'id_region', 'id_branch', 'is_cross_company_os'
    ];

    public static function getdata() {
        $data = DB::table('hr_survey_header as hsh')
                ->join('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
                ->leftJoin('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
                ->leftJoin('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
                ->select('hsh.*', 'he.name as employee_name', 'mgd.description as question_type', 'mgd2.description as survey_type')
                ->where('hsh.id_company', session('id_company'))
                ->where('hsh.survey_category', 'Survey')
                ->where('hsh.status', 'A')
                ->orderBy('hsh.id_survey_header', 'desc')
                ->get();
        return $data;
    }
    public static function getdatapublished() {
        $getListSurveyByUser = self::getListSurveyByUser();

        $get = DB::table('hr_survey_history as hshi')
                ->leftJoin('hr_survey_header as hsh', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->join('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
                ->leftJoin('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
                ->leftJoin('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
                ->select('hsh.id_survey_header', 'hsh.with_score', 'hsh.description', 'hsh.status', 'he.name as employee_name', 'mgd.description as question_type', 'mgd2.description as survey_type', 'hshi.start_date', 'hshi.end_date', 'hshi.id_survey_history', 'he.id_employee')
                ->whereRaw('(
                    (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                    OR
                    (hsh.is_cross_company_os = TRUE)
                )', [session('id_company')])
                ->where('hsh.published', true)
                ->where('hsh.status', 'A')
                ->where('hsh.survey_category', 'Survey')
                ->where('hshi.status', 'A')
                ->where('hshi.start_date', '<=', date('Y-m-d'))
                ->where('hshi.end_date', '>=', date('Y-m-d'))
                ->orderByDesc('hsh.id_survey_header');

        if($getListSurveyByUser){
            $get->whereIn('hsh.id_survey_header', $getListSurveyByUser);
        }

        $data = $get->get();
        return $data;
    }
    public static function getAllPublished() {
        $get = DB::table('hr_survey_history as hshi')
                ->leftJoin('hr_survey_header as hsh', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->join('hr_employee as he', 'hsh.id_employee_request', '=', 'he.id_employee')
                ->leftJoin('master_general_data as mgd', 'hsh.id_question_type', '=', 'mgd.id_general_data')
                ->leftJoin('master_general_data as mgd2', 'hsh.id_survey_type', '=', 'mgd2.id_general_data')
                ->select('hsh.id_survey_header', 'hsh.with_score', 'hsh.description', 'hsh.status', 'he.name as employee_name', 'mgd.description as question_type', 'mgd2.description as survey_type', 'hshi.start_date', 'hshi.end_date', 'hshi.id_survey_history', 'he.id_employee')
                ->where('hsh.id_company', session('id_company'))
                ->where('hsh.status', 'A')
                ->where('hshi.status', 'A')
                ->where('hsh.survey_category', 'Survey')
                // ->where('hshi.status', $historyStatus)
                ->orderByDesc('hsh.id_survey_header');

        $publishedSurveys = $get->get();
        // if($historyStatus == 'A') {
        //     $publishedSurveys = $publishedSurveys->groupBy('id_survey_header');
        //     foreach($publishedSurveys as $key => $publishedSurvey) {
        //         $publishedSurveys[$key] = $publishedSurvey->sortByDesc('start_date')->first();
        //     }
        // }
        return $publishedSurveys;
    }
	public static function getdata_answer() {
        $data = DB::table('master_survey_answer as msa')
                ->leftJoin('hr_survey_answer as hsa', 'hsa.id_answer', '=', 'msa.id_answer')
                ->leftJoin('hr_survey_question as hsq', 'hsq.id_survey_question', '=', 'hsa.id_survey_question')
                ->leftJoin('hr_survey_header as hsh', 'hsh.id_survey_header', '=', 'hsq.id_survey_header')
                ->select('msa.*')
                ->where('msa.id_company', session('id_company'))
                ->where('answer_group_type', '!=', 'LMS')
                ->orderBy('msa.code')
                ->groupBy('msa.id_answer')
                ->get();
        return $data;
    }
	
	public static function get_company() {
        $data = DB::table('master_company as mc')
                ->select('mc.id_company as id', 'mc.company_name as text')
                ->where('mc.status', 'A')
                ->orderBy('mc.id_company','ASC')
                ->get();
        return $data;
    }

    public static function get_company_user() {
        $sql = "SELECT mc.id_company id,
				   mc.company_name text
				   FROM relation_company_users rcu
			INNER JOIN master_company mc
			ON rcu.id_company = mc.id_company
			where rcu.id_user = ?";
        $result = (Array) DB::select($sql, [session('id_user')]);
        return $result;
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

    public static function get_category() {
        $data = DB::table('master_general_data')
                ->select('id_general_data as id', 'description as text', 'code')
                ->where('id_company', session('id_company'))
                ->where('status', 'A')
                ->where('id_general_type', '22')
                ->get();
        return $data;
    }

    public static function get_survey_type() {
        $data = DB::table('master_general_data')
                ->select('id_general_data as id', 'description as text', 'code')
                ->where('id_company', session('id_company'))
                ->where('status', 'A')
                ->where('id_general_type', '18')
                ->where('code', '!=', 'quiz_pretest')
                ->where('code', '!=', 'quiz_posttest')
                ->where('code', '!=', 'quiz_remidial')
                ->get();
        return $data;
    }

	public static function get_answer() {
        $data = DB::table('master_survey_answer')
                ->select('id_answer as id', 'code as text')
                ->where('id_company', session('id_company'))
                ->where('answer_group_type', '!=', 'LMS')
                ->where('status', 'A')
                ->orderBy('code')
                ->get();
        return $data;
    }
    
	public static function get_edit_answer($data) {
        $result = [];
        $sql = "SELECT *
                FROM master_survey_answer
				WHERE id_answer  = ?";
        $result = (Array) DB::select($sql, [$data['id_answer']])[0];
       // 	dd($result);
        return $result;
    }
	public static function get_survey_edit($data) {
        $result = [];
        $sql = "SELECT hsh.id_survey_header, hsh.description, hsh.notes, hsh.id_employee_request, hsh.id_question_type, hsh.id_survey_type, hsh.survey_category, hsh.start_date, hsh.end_date, hsh.published, hsh.with_score, hsh.status as status_header, hsh.id_company as company_header, hsh.is_cross_company_os
				FROM hr_survey_header hsh
				WHERE hsh.id_survey_header  = ?";
        $result = (Array) DB::select($sql, [$data['id_survey_header']])[0];

        $existPeriod = DB::table('hr_survey_history')->where('id_survey_header', $data['id_survey_header'])->get();
        $existDept = DB::table('relation_department_surveys')->where('id_survey_header', $data['id_survey_header'])->get()->pluck('id_dept')->all();
        $existRegion = DB::table('relation_regional_surveys')->where('id_survey_header', $data['id_survey_header'])->get()->pluck('id_region')->all();
        $existBranch = DB::table('relation_branch_surveys')->where('id_survey_header', $data['id_survey_header'])->get()->pluck('id_branch')->all();
        $existGrade = DB::table('relation_jobgrade_surveys')->where('id_survey_header', $data['id_survey_header'])->get()->pluck('id_job_grade')->all();
        $existPrincipal = DB::table('relation_principal_surveys')->where('id_survey_header', $data['id_survey_header'])->get()->pluck('id_principal')->all();

        $result['id_department'] = implode(',', $existDept);
        $result['id_region'] = implode(',', $existRegion);
        $result['id_branch'] = implode(',', $existBranch);
        $result['id_job_grade'] = implode(',', $existGrade);
        $result['id_principal'] = implode(',', $existPrincipal);

        $result['period'] = [];
        if($existPeriod){
            foreach ($existPeriod as $key => $val) {
                $result['period'][] = [
                    'id_survey_history' => $val->id_survey_history,
                    'status' => $val->status,
                    'start' => $val->start_date,
                    'end' => $val->end_date,
                ];
            }
        }

        $sql2 = "SELECT hsq.id_survey_header, hsq.id_survey_question, hsq.sequence,  hsq.question, hsq.note, hsq.id_question_type, hsq.attachment, hsq.id_question_group, hsq.status as question_status, hsa.id_survey_answer, hsa.id_answer, hsa.is_corrected_answer, hsa.score_answer
				FROM hr_survey_question hsq
				LEFT JOIN hr_survey_answer hsa
				ON hsa.id_survey_question = hsq.id_survey_question
                AND hsa.status = 'A'
				WHERE hsq.id_survey_header = ?
                ORDER BY hsq.sequence ASC";
        $result_menu = DB::select($sql2, [$data['id_survey_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_survey_question')->toArray();

        $result['question'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
			$group_suggested_answer = collect($group_menu[$value])->groupBy('id_answer')->toArray();
            $suggested_answer = array_keys($group_suggested_answer);
            $result['question'][] = [
                'id_survey_question' => $group_menu[$value][0]->id_survey_question,
                'id_question_type' => $group_menu[$value][0]->id_question_type,
                'id_question_group' => $group_menu[$value][0]->id_question_group,
                'question_status' => $group_menu[$value][0]->question_status,
                'sequence' => $group_menu[$value][0]->sequence,
                'question' => $group_menu[$value][0]->question,
                'attachment' => $group_menu[$value][0]->attachment,
                'suggested_answer' => $suggested_answer,
                'note' => $group_menu[$value][0]->note,
            ];
        }
        return $result;
    }
	
    public static function getListSurveyByUser() {
        $employee = DB::selectOne("SELECT he.* FROM hr_employee he
                                JOIN master_position_detail mpd
                                ON (he.id_employee = mpd.id_employee OR he.id_employee = mpd.id_employee2)
                                AND mpd.secondary_position = FALSE
                                WHERE he.status = 'A'
                                AND he.id_user = ?", [session('id_user')]);
        $_surveyActive = \DB::table('hr_survey_header as hsh')
            ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
            ->select('hsh.id_survey_header', 'hshi.end_date', 'hshi.id_survey_history', 'rds.id_dept as id_department', 'rgs.id_region', 'rbs.id_branch', 'rjs.id_job_grade', 'rps.id_principal')
            ->where('hsh.published', true)->where('hsh.status', 'A')
            ->whereRaw('(
                (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                OR
                (hsh.is_cross_company_os = TRUE)
            )', [$employee->id_company])
            ->where('hsh.survey_category', 'Survey')
            ->orderBy('hshi.end_date')->get();

        $returnIdSurveyHeader = null;

        if(@$_surveyActive->count() > 0){
            $id_survey_header = [];     
            $activeSurvey = [];

            $_myEmployee = \DB::table('hr_employee as he')->select('he.id_employee','mjp.id_dept','mb.id_region','mpd.id_branch', 'mpr.id_job_grade', 'rpp.id_principal')
                    ->leftJoin('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                    ->leftJoin('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                    ->leftJoin('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                    ->leftJoin('master_job_position as mjp', 'mjp.id_position', '=', 'mpr.id_position')
                    ->leftJoin('master_job_grade as mjg', 'mjg.id_job_grade', '=', 'mpr.id_job_grade')
                    ->leftJoin('relation_positiondetail_principal as rpp', 'rpp.id_position_detail', 'mpd.id_position_detail')
                    ->where('he.id_user', session('id_user'))
                    ->where('mpd.secondary_position', false)
                    ->where('he.status', 'A')
                    ->where('he.id_company', session('id_company'))
                    ->first();

            $allSurvey = [];
            $surveyDetail = [];
            $idSurveyHistory = [];
            foreach ($_surveyActive as $key => $val) {
                $idSurveyHistory[] = $val->id_survey_history;
                $idSurvey = $val->id_survey_header;
                if(!in_array($idSurvey, $allSurvey)){
                    $allSurvey[] = $idSurvey;
                    $surveyDetail[$idSurvey] = [
                        'id_department' => [],
                        'id_region' => [],
                        'id_branch' => [],
                        'id_job_grade' => [],
                        'id_principal' => [],
                    ];
                    $activeSurvey[] = ['id'=>$idSurvey, 'end'=>$val->end_date];
                }
                $id_department = $val->id_department;
                $id_region = $val->id_region;
                $id_branch = $val->id_branch;
                $id_job_grade = $val->id_job_grade;
                $id_principal = $val->id_principal;

                if(!is_null($id_department) && !in_array($id_department, $surveyDetail[$idSurvey]['id_department'])){
                    $surveyDetail[$idSurvey]['id_department'][] = $id_department;
                }
                if(!is_null($id_region) && !in_array($id_region, $surveyDetail[$idSurvey]['id_region'])){
                    $surveyDetail[$idSurvey]['id_region'][] = $id_region;
                }
                if(!is_null($id_branch) && !in_array($id_branch, $surveyDetail[$idSurvey]['id_branch'])){
                    $surveyDetail[$idSurvey]['id_branch'][] = $id_branch;
                }
                if(!is_null($id_job_grade) && !in_array($id_job_grade, $surveyDetail[$idSurvey]['id_job_grade'])){
                    $surveyDetail[$idSurvey]['id_job_grade'][] = $id_job_grade;
                }
                if(!is_null($id_principal) && !in_array($id_principal, $surveyDetail[$idSurvey]['id_principal'])){
                    $surveyDetail[$idSurvey]['id_principal'][] = $id_principal;
                }
            }
            foreach ($surveyDetail as $key => $val) {
                $surveyFalse = [];
                if(count($val['id_department']) < 1 && count($val['id_region']) < 1 && count($val['id_branch']) < 1 && count($val['id_job_grade']) < 1 && count($val['id_principal']) < 1){
                    $id_survey_header[] = $key;
                } else {
                    if(count($val['id_department']) > 0 && !in_array(@$_myEmployee->id_dept, $val['id_department'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_region']) > 0 && !in_array(@$_myEmployee->id_region, $val['id_region'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_branch']) > 0 && !in_array(@$_myEmployee->id_branch, $val['id_branch'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_job_grade']) > 0 && !in_array(@$_myEmployee->id_job_grade, $val['id_job_grade'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_principal']) > 0 && !in_array(@$_myEmployee->id_principal, $val['id_principal'])){
                        $surveyFalse[] = false;
                    }
                }
                if(count($surveyFalse) < 1){
                    $id_survey_header[] = $key;
                }
            }

            if(count($id_survey_header) > 0){
                $returnIdSurveyHeader = collect($id_survey_header)->unique()->toArray();
            }
        }
        return $returnIdSurveyHeader;
    }

}
