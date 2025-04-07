<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Survey extends Model {
	
    public static function getActiveIdSurvey($employee) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');

        $idShow = [];
        $_surveyActive = DB::table('hr_survey_header as hsh')
            ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
            ->select('hsh.id_survey_header', 'hshi.end_date', 'hshi.id_survey_history', 'rds.id_dept as id_department', 'rgs.id_region', 'rbs.id_branch', 'rjs.id_job_grade', 'rps.id_principal')
            ->where(function ($query){
                $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                $query->where('hshi.status', 'A');
            })
            ->where('hsh.published', true)->where('hsh.status', 'A')
            ->whereRaw('(
                (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                OR
                (hsh.is_cross_company_os = TRUE)
            )', [$idCompany])
            ->where('hsh.survey_category', 'Survey')
            ->orderBy('hshi.end_date')->get();

        if(@$_surveyActive){
            $id_survey_header   = [];     
            $activeSurvey       = [];
            $allSurvey          = [];
            $surveyDetail       = [];
            $idSurveyHistory    = [];
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
                    if(count($val['id_department']) > 0 && !in_array(@$employee->id_department, $val['id_department'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_region']) > 0 && !in_array(@$employee->id_region, $val['id_region'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_branch']) > 0 && !in_array(@$employee->id_branch, $val['id_branch'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_job_grade']) > 0 && !in_array(@$employee->id_job_grade, $val['id_job_grade'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_principal']) > 0 && !in_array(@$employee->id_principal, $val['id_principal'])){
                        $surveyFalse[] = false;
                    }
                }
                if(count($surveyFalse) < 1){
                    $id_survey_header[] = $key;
                }
            }

            if(count($id_survey_header) > 0){
                $id_survey_header = collect($id_survey_header)->unique()->toArray();

                $_surveyAnswer = DB::table('hr_survey_answer_user_header')
                        ->select('id_survey_header')
                        ->whereIn('id_survey_header', $id_survey_header)
                        ->whereIn('id_survey_history', $idSurveyHistory)
                        ->where('id_company', $idCompany)
                        ->where('id_employee', $idEmployee)
                        ->get()->pluck('id_survey_header')->all();

                if(count($_surveyAnswer) < count($id_survey_header)){
                    $idShow = collect($id_survey_header)->diff($_surveyAnswer)->values();
                }
            }
        }
        return $idShow;
    }

	public static function showSurvey($employee, $idSurvey) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');

        $questions              = [];
        $idSurveyHeader         = [];
        $idSurveyQuestion       = [];
        $totalQuestionAnswered  = [];
        $allCategory            = [];

        $getSurvey = DB::table('hr_survey_header as hsh')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->select('hsh.*', 'mgd.description as survey_type', 'hshi.start_date as start_history', 'hshi.end_date as end_history', 'hshi.id_survey_history', 'mgd.code as type')
                ->where('hsh.id_survey_header', $idSurvey)
                ->where(function ($query){
                    $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                    $query->where('hshi.status', 'A');
                })
                ->first();

        $surveyQuestion = DB::table('hr_survey_question as hsq')
            ->select('hsq.*', 'mgd.code as type_question', 'mgd2.description as category_question')
            ->leftJoin('master_general_data as mgd', 'hsq.id_question_type', '=', 'mgd.id_general_data')
            ->leftJoin('master_general_data as mgd2', 'hsq.id_question_group', '=', 'mgd2.id_general_data')
            ->where('hsq.id_survey_header', $idSurvey)
            ->orderBy('mgd2.id_general_data')->orderBy('hsq.sequence')
            ->get();

        foreach ($surveyQuestion as $key => $value) {
            $answer = [];
            $getAnswer = DB::table('hr_survey_answer as hsa')
                    ->join('master_survey_answer as msa', 'msa.id_answer', '=', 'hsa.id_answer', 'left')
                    ->select('hsa.*', 'msa.description_answer', 'msa.suggested_image as answer_image')
                    ->where('hsa.id_survey_question', $value->id_survey_question)
                    ->get();
            $i_answer = 1;
            foreach ($getAnswer as $i => $val) {
                $thisImage = null;
                if(!is_null($val->answer_image)){
                    $filePath   = 'project/storage/app/public/upload/master_answer/';
                    $thisImage  = url($filePath.$val->answer_image);
                }
                $answer[] = [
                    'id_survey_answer'  => $val->id_survey_answer,
                    'reference'         => is_null($val->id_answer) ? $val->reference_number : $i_answer,
                    'description'       => is_null($val->id_answer) ? $val->suggested_answer : $val->description_answer,
                    'image'             => $thisImage,
                ];
                $i_answer++;
            }
            array_multisort(array_column($answer, 'reference'), SORT_ASC, $answer);
            $questions[] = [
                'id_survey_question'=> $value->id_survey_question,
                'category'          => $value->category_question,
                'type'              => $value->type_question,
                'question'          => $value->question,
                'note'              => $value->note,
                'sequence'          => $value->sequence,
                'answers'           => $answer,
            ];
            $idSurveyHeader[]       = @$getSurvey->id_survey_header;
            $idSurveyQuestion[]     = $value->id_survey_question;

            if(!in_array($value->category_question, $allCategory)){
                $allCategory[] = $value->category_question;
            }
        }

        if(count($idSurveyHeader) > 0){
            $idSurveyHeader_    = array_unique($idSurveyHeader);
            $idSurveyQuestion_  = array_unique($idSurveyQuestion);
            $getAnswerUser = DB::table('hr_survey_answer_user as hsau')
                    ->join('hr_survey_answer_user_header as hsauh', 'hsauh.id_survey_answer_user_header', '=', 'hsau.id_survey_answer_user_header', 'left')
                    ->select('hsau.*')
                    ->whereIn('hsauh.id_survey_header', $idSurveyHeader_)
                    ->where('hsauh.id_employee', $idEmployee)
                    ->where('hsauh.id_survey_history', @$getSurvey->id_survey_history)
                    ->get();

            foreach ($getAnswerUser as $k => $item) {
                if(in_array($item->id_survey_question, $idSurveyQuestion_)){
                    $totalQuestionAnswered[] = 1;
                }
            }
        }

        $return_data = [
            'id_survey_header'  => @$getSurvey->id_survey_header,
            'id_survey_history' => @$getSurvey->id_survey_history,
            'title'             => @$getSurvey->survey_type.' : '.@$getSurvey->description,
            'type'              => @$getSurvey->type,
            'start_date'        => @$getSurvey->start_history,
            'end_date'          => @$getSurvey->end_history,
            'notes'             => @$getSurvey->notes,
            'status'            => (count($totalQuestionAnswered) > 0) ? 'done' : 'ready',
            // 'category'          => $allCategory,
            // 'questions'         => $questions
        ];

        return $return_data;
    }

    public static function getAllSurveyByUser($employee) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');
        $sub6Months    = Carbon::parse($today)->subMonths(6)->format('Y-m-d');

        $idShow         = [];
        $returnSurvey   = [];
        $_surveyActive = DB::table('hr_survey_header as hsh')
            ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
            ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
            ->select('hsh.id_survey_header', 'hshi.end_date', 'hshi.id_survey_history', 'rds.id_dept as id_department', 'rgs.id_region', 'rbs.id_branch', 'rjs.id_job_grade', 'rps.id_principal')
            ->where(function($query) use ($sub6Months, $today) {
                // $query->whereBetween('hshi.start_date', [$sub6Months, $today]);
                $query->where('hshi.status', 'A');
            })
            ->whereDate('hshi.start_date', '<=', $today)
            ->whereDate('hshi.end_date', '>=', $today)
            ->where('hsh.published', true)->where('hsh.status', 'A')
            ->whereRaw('(
                (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                OR
                (hsh.is_cross_company_os = TRUE)
            )', [$idCompany])
            ->where('hsh.survey_category', 'Survey')
            ->orderBy('hshi.end_date')->get();

        if(@$_surveyActive){
            $idSurveyHeader     = [];     
            $activeSurvey       = [];
            $allSurvey          = [];
            $surveyDetail       = [];
            $idSurveyHistory    = [];
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
                    $idSurveyHeader[] = $key;
                } else {
                    if(count($val['id_department']) > 0 && !in_array(@$employee->id_department, $val['id_department'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_region']) > 0 && !in_array(@$employee->id_region, $val['id_region'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_branch']) > 0 && !in_array(@$employee->id_branch, $val['id_branch'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_job_grade']) > 0 && !in_array(@$employee->id_job_grade, $val['id_job_grade'])){
                        $surveyFalse[] = false;
                    }
                    if(count($val['id_principal']) > 0 && !in_array(@$employee->id_principal, $val['id_principal'])){
                        $surveyFalse[] = false;
                    }
                }
                if(count($surveyFalse) < 1){
                    $idSurveyHeader[] = $key;
                }
            }

            if(count($idSurveyHeader) > 0){
                $idSurveyHeader = collect($idSurveyHeader)->unique()->toArray();
                $returnSurvey = DB::table('hr_survey_header as hsh')
                    ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                    ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                    ->select('hsh.id_survey_header', 'hshi.id_survey_history', 'hsh.description as title', 'mgd.code as type', 'hshi.start_date', 'hshi.end_date')
                    ->whereIn('hsh.id_survey_header', $idSurveyHeader)
                    ->whereDate('hshi.start_date', '<=', $today)
                    ->whereDate('hshi.end_date', '>=', $today)
                    ->get();
            }
        }
        return $returnSurvey;
    }

    public static function detailSurvey($idSurvey) {
        $today                  = date('Y-m-d');
        $questions              = [];
        $idSurveyHeader         = [];
        $idSurveyQuestion       = [];
        $totalQuestionAnswered  = [];
        $allCategory            = [];

        $getSurvey = DB::table('hr_survey_header as hsh')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->select('hsh.*', 'mgd.description as survey_type', 'hshi.start_date as start_history', 'hshi.end_date as end_history', 'hshi.id_survey_history', 'mgd.code as type')
                ->where('hsh.id_survey_header', $idSurvey)
                ->where(function ($query){
                    $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                    $query->where('hshi.status', 'A');
                })
                ->first();

        $surveyQuestion = DB::table('hr_survey_question as hsq')
            ->select('hsq.*', 'mgd.code as type_question', 'mgd2.description as category_question')
            ->leftJoin('master_general_data as mgd', 'hsq.id_question_type', '=', 'mgd.id_general_data')
            ->leftJoin('master_general_data as mgd2', 'hsq.id_question_group', '=', 'mgd2.id_general_data')
            ->where('hsq.id_survey_header', $idSurvey)
            ->orderBy('mgd2.id_general_data')->orderBy('hsq.sequence')
            ->get();

        $return_data = [
            'title'             => @$getSurvey->description,
            'type_code'         => @$getSurvey->type,
            'type_description'  => @$getSurvey->survey_type,
            'start_date'        => @$getSurvey->start_history,
            'end_date'          => @$getSurvey->end_history,
            'questions'         => @$surveyQuestion->count(),
        ];

        return $return_data;
    }
}