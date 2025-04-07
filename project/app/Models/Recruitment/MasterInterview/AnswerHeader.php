<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnswerHeader extends Model
{
	use HasFactory;
	
    protected $table = 'hr_recruitment_answer_header';
	protected $primaryKey = 'id_recruitment_answer_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_answer_header', 'id_candidate', 'id_recruitment_question_group', 'id_applied_candidate', 'interview_date', 'total_score', 'id_conclusion', 'notes', 'id_department', 'id_job_grade', 'status', 'id_company', 'id_employee', 'assessment_type', 'created_by', 'updated_by'
    ];
		
}
