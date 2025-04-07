<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterCourseHeader extends Model {

    use HasFactory;

    protected $table = 'master_course_header';
    protected $primaryKey = 'id_course_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_course_header', 'course_name', 'notes', 'duration_time', 'status', 'id_company', 'created_by', 'updated_by'
    ];

    public static function get_course() {
        $data = DB::table('master_course_header as mch')
                ->select('mch.id_course_header', 'mch.course_name', 'mch.notes', 'mch.duration_time', 'mch.status')
                ->where('mch.id_company', '=', session('id_company'))
                ->orderByDesc('mch.id_course_header');
        return $data->get();
    }
    
    public static function get_answer($id_question=null) {
        $sql = "SELECT
                    hsa.id_survey_answer, 
                    hsa.suggested_answer
                    FROM hr_survey_answer hsa
                    WHERE hsa.id_company = ? ";
        $data = DB::select($sql, [session('id_company')]);
        return $data;
    }

    public static function get_course_materi($idCompany=null) {
        $idCompany = $idCompany ?? session('id_company');

        $data = DB::table('master_content_learning as mcl')
            ->select('mcl.id_content_learning', 'mcl.content_attachment_type', 'mcl.description', 'mcl.link', 'mcl.content_name', 'mcl.notes')
            ->where('mcl.id_company', $idCompany)
            ->where('mcl.status', '=', 'A')
            ->orderBy('mcl.content_name');
        return $data->get();
    }

    public static function getQueries(Builder $builder)
    {
        $addSlashes = str_replace('?', "'?'", $builder->toSql());
        return vsprintf(str_replace('?', '%s', $addSlashes), $builder->getBindings());
    }

    public static function get_course_quiz($idCompany=null, $type) {
        $idCompany = $idCompany ?? session('id_company');

        if($type == null) {
            $types = ['quiz', 'quiz_pretest', 'quiz_posttest', 'quiz_remidial'];
            foreach($types as $type) {
                $data[$type] = DB::table('hr_survey_header as hsh')
                    ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                    ->select('hsh.id_survey_header', 'hsh.description as quiz', 'hsh.notes')
                    ->where('hsh.id_company', $idCompany)
                    ->where('hsh.status', 'A')
                    // ->where('hsh.published', true)
                    ->where('hsh.survey_category', 'LMS')
                    ->where('mgd.code', $type)
                    ->orderBy('hsh.description')
                    ->get();
            }
        } else {
            $data[$type] = DB::table('hr_survey_header as hsh')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                ->select('hsh.id_survey_header', 'hsh.description as quiz', 'hsh.notes')
                ->where('hsh.id_company', $idCompany)
                ->where('hsh.status', 'A')
                // ->where('hsh.published', true)
                ->where('hsh.survey_category', 'LMS')
                ->where('mgd.code', $type)
                ->orderBy('hsh.description')
                ->get();
        }

        
        return $data;
    }

    public static function get_course_detail($data) {
        $result = [];
        $sql = "SELECT *           
                FROM master_course_header
                WHERE id_company = ? AND id_course_header = ? ";
        $result = (array)DB::select($sql, [$data['id_company'], $data['id_course_header']])[0];

        $q  = "SELECT *           
                FROM master_course_detail
                WHERE id_company = ? AND id_course_header = ? order by sequence asc";
        $result_detail = DB::select($q, [$data['id_company'], $data['id_course_header']]);
        $result['course'] = $result_detail;
        return $result;
    }

}
