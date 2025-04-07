<?php

namespace App\Models\LearningManagement\Lms;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyAnswerUser extends Model {
	
	protected $table = 'hr_survey_answer_user';
    protected $primaryKey = 'id_survey_user';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_survey_user', 'id_survey_answer_user_header', 'id_survey_question', 'id_survey_answer', 'description_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];
}
