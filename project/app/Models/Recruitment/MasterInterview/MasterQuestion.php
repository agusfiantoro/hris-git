<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterQuestion extends Model
{
	use HasFactory;
	
    protected $table = 'master_recruitment_question';
	protected $primaryKey = 'id_recruitment_question';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_question', 'id_recruitment_question_group', 'sequence', 'description', 'notes', 'id_question_type', 'category_group', 'competency_group', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
