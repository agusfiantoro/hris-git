<?php

namespace App\Models\LearningManagement\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HrKpiHeader extends Model {

    use HasFactory;

    protected $table = 'hr_kpi_header';
    protected $primaryKey = 'id_kpi_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_kpi_header', 'id_kpi_group', 'kpi_month', 'notes', 'subtotal_kpi', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_data($id_kpi_group=null) {
        $data = DB::table('hr_kpi_header as hkh')
                ->select('hkh.*')
                ->where('hkh.id_company', '=', session('id_company'));

        if($id_kpi_group){
            $data->where('hkh.id_kpi_group', $id_kpi_group);
        }
        return $data->get();
    }

    public static function get_select_header($id_kpi_group=null) {
        $data = DB::table('hr_kpi_header as hkh')
                ->selectRaw('hkh.id_kpi_header as id, hkh.kpi_month as text')
                // ->selectRaw('hkh.id_kpi_header as id, DATE_FORMAT("hkh.kpi_month", "%Y-%m") as text')
                ->where('hkh.id_company', '=', session('id_company'))
                ->orderBy('hkh.kpi_month');
        if($id_kpi_group){
            $data->where('hkh.id_kpi_group', $id_kpi_group);
        }
        $as_id = [];
        $result = $data->get();
        if($result->count() > 0){
            foreach ($result as $key => $item) {
                $month_as_text = date('M Y', strtotime($item->text));
                $month_as_date = date('Y-m', strtotime($item->text));
                $as_id[]       = ['id' => $item->id, 'text' => $month_as_text];
            }
        }
        return $as_id;
        // return $data->get();
    }

    public static function getDataLms($idKpiHeader=null, $idEmployee=null) {
        $kpiHeader = DB::table('hr_kpi_header')->where('id_kpi_header', $idKpiHeader)->first();
        $date = Carbon::parse($kpiHeader->kpi_month)->format('Y-m');

        $getLms = DB::table('hr_event_attendees as hea')
            ->join('hr_event_program as hep', 'hea.id_event_program', '=', 'hep.id_event_program')
            ->join('master_course_detail as mcd', 'mcd.id_course_header', '=', 'hep.id_course_header')
            ->leftJoin('hr_survey_answer_user_header as hsauh', function ($join) use($idEmployee){
                $join->on('hsauh.id_course_detail', '=', 'mcd.id_course_detail');
                $join->where(function ($where)use($idEmployee){
                    $where->where('mcd.course_type', '=', 'Quiz_Posttest');
                    $where->where('hsauh.id_employee', '=', $idEmployee);
                });
            })
            ->select('hep.id_course_header', 'hep.description as course', 'mcd.id_survey_header', 'hsauh.total_score')
            ->where('hea.booked_by', $idEmployee)
            ->where(function ($where)use($date){
                $where->where(function ($where)use($date){
                    $where->whereNull('hea.end_date');
                    $where->where('hep.end_date', 'LIKE', '%'.$date.'%');
                });
                $where->orWhere(function ($where)use($date){
                    $where->whereNotNull('hea.end_date');
                    $where->where('hea.end_date', 'LIKE', '%'.$date.'%');
                });
            })
            ->get();

        $idSurveyByCourseHeader = [];
        $allScoreQuizByEmployee = [];
        $totalScoreByCourse = [];
        $allCourseName = [];
        $resultCourse = [];

        if($getLms->count() > 0){
            foreach ($getLms as $k => $val) {
                if(!array_key_exists($val->id_course_header, $idSurveyByCourseHeader) || (array_key_exists($val->id_course_header, $idSurveyByCourseHeader) && !in_array($val->id_survey_header, @$idSurveyByCourseHeader[$val->id_course_header])) ){
                    $idSurveyByCourseHeader[$val->id_course_header][] = $val->id_survey_header;
                    $allScoreQuizByEmployee[$val->id_survey_header] = (@$val->total_score) ? (float)@$val->total_score : null;
                    $allCourseName[$val->id_course_header] = $val->course;
                }
            }
        }
        if(count($idSurveyByCourseHeader) > 0){
            foreach ($idSurveyByCourseHeader as $thisIdCourse => $idSurvey) {
                $thisTotalScore = [];
                foreach ($idSurvey as $key => $val) {
                    $thisTotalScore[] = $allScoreQuizByEmployee[$val];
                }
                $checkScoreCourse = (max($thisTotalScore)==null) ? null : array_sum($thisTotalScore);
                $totalScoreByCourse[$thisIdCourse] = $checkScoreCourse;
                $resultCourse[] = [
                    'id_course_header' => $thisIdCourse,
                    'score' => $totalScoreByCourse[$thisIdCourse],
                    'name' => $allCourseName[$thisIdCourse],
                ];
            }
        }

        $result = $resultCourse;
        return $result;
    }
}
