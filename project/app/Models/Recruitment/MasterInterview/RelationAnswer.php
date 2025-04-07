<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class RelationAnswer extends Model
{
	use HasFactory;
	
    protected $table = 'relation_recruitment_question_answer';
	protected $primaryKey = 'id_recruitment_question_answer';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_question_answer', 'id_recruitment_question', 'id_recruitment_answer', 'weight_score', 'is_corrected_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
