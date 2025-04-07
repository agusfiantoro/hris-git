<?php

namespace App\Models\EventManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyQuestion extends Model {

    use HasFactory;

    protected $table = 'hr_survey_question';
    protected $primaryKey = 'id_survey_question';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_survey_question', 'id_survey_header', 'sequence', 'question', 'note', 'status', 'id_company', 'created_by', 'updated_by', 'id_question_type'
    ];

    public static function get_question($id_survey_header=null) {
        $data = DB::table('hr_survey_question as hsq')
                ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type')
                ->select('hsq.id_survey_question', 'hsq.sequence', 'hsq.question', 'hsq.id_company')
                ->where('hsq.id_company', '=', session('id_company'));
                
        if($id_survey_header){
            $data->where('hsq.id_survey_header', '=', $id_survey_header);
        }
        return $data->get();
    }
    
    public static function get_answer($id_question=null) {
        $cond = $id_question ? "AND hsa.id_survey_question = '$id_question' " : '';
        $sql = "SELECT
                    hsa.id_survey_answer, 
                    hsa.suggested_answer
                    FROM hr_survey_answer hsa
                    WHERE hsa.id_company = ? AND hsa.status = 'A' $cond ";
        $data = DB::select($sql, [session('id_company')]);
        return $data;
    }

}
