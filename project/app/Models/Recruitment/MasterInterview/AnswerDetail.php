<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnswerDetail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_recruitment_answer_detail';
	protected $primaryKey = 'id_recruitment_answer_detail';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_answer_detail', 'id_recruitment_question', 'id_recruitment_answer', 'id_recruitment_answer_header', 'essay_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
