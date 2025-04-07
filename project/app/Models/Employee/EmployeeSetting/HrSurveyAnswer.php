<?php

namespace App\Models\Employee\EmployeeSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrSurveyAnswer extends Model {
	
	protected $table = 'hr_survey_answer';
    protected $primaryKey = 'id_survey_answer';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_survey_answer', 'id_survey_question', 'id_answer', 'reference_number', 'suggested_answer', 'suggested_image', 'is_corrected_answer', 'score_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];
}
