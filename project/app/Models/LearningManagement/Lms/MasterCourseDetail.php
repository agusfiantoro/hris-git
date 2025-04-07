<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterCourseDetail extends Model {

    use HasFactory;

    protected $table = 'master_course_detail';
    protected $primaryKey = 'id_course_detail';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_course_detail', 'id_course_header', 'sequence', 'course_type', 'id_content_learning', 'id_survey_header', 'course_name', 'notes', 'status', 'id_company', 'created_by', 'updated_by', 'question_view', 'weight'
    ];

    public static function getCourse($idCourse) {
        $getCourse  = DB::table('master_course_detail as mcd')
            ->select('mcd.id_course_detail', 'mcd.sequence', 'mcd.id_survey_header', 'mcd.id_content_learning', 'mcd.course_name', 'mcd.question_view', 'mcd.weight', 'mcd.status')
            ->where('mcd.id_course_header', $idCourse)
            ->where('mcd.status', 'A')
            ->orderBy('sequence');

        $result = $getCourse->get();
        return $result;
    }

    public static function getCourseByEmployee($idEventProgram, $idEmployee) {
        $getCourseByEmployee = DB::table('relation_event_course_employee as rece')
            ->leftJoin('master_course_detail as mcd', 'mcd.id_course_detail', '=', 'rece.id_course_detail')
            ->leftJoin('hr_event_program as hep', 'rece.id_event_program', '=', 'hep.id_event_program')
            ->select('rece.*', 'mcd.sequence', 'hep.id_event_management')
            ->where('rece.id_employee', $idEmployee)
            ->where('rece.id_event_program', $idEventProgram)
            ->where('mcd.status', 'A')
            ->orderByDesc('rece.id_event_course_employee');

        $result = $getCourseByEmployee->get();
        return $result;
    }

    public static function getCourseByCourseType($idCourse, $courseType) {
        $getCourseByCourseType = DB::table('master_course_detail as mcd')
            ->where('mcd.course_type', $courseType)
            ->where('mcd.status', 'A')
            ->where('mcd.id_course_header', $idCourse);

        $result = $getCourseByCourseType->get();
        return $result;
    }



    

}
