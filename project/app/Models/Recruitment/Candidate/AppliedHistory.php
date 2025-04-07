<?php

namespace App\Models\Recruitment\Candidate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppliedHistory extends Model
{
	use HasFactory;
	
    protected $table = 'web.hr_applied_stage_history';
	protected $primaryKey = 'id_applied_stage_history';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
	'id_applied_stage_history', 'id_applied_candidate', 'id_candidate_status', 'start_date', 'end_date', 'duration_time', 'status', 'created_by', 'updated_by'
    ];
		
}
