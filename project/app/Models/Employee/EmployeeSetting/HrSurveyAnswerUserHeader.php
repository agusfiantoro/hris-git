<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyAnswerUserHeader extends Model {
	
	protected $table = 'hr_survey_answer_user_header';
    protected $primaryKey = 'id_survey_answer_user_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_survey_answer_user_header', 'id_survey_header', 'total_score', 'id_employee', 'status', 'id_company', 'created_by', 'updated_by', 'id_survey_history'
    ];
}
