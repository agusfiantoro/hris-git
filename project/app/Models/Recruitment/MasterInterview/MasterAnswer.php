<?php

namespace App\Models\Recruitment\MasterInterview;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterAnswer extends Model
{
	use HasFactory;
	
    protected $table = 'master_recruitment_answer';
	protected $primaryKey = 'id_recruitment_answer';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_recruitment_answer', 'sequence', 'description', 'status', 'id_company', 'created_by', 'updated_by'
    ];
		
}
