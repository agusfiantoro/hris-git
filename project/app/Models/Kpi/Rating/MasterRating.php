<?php

namespace App\Models\Kpi\Rating;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterRating extends Model
{
	use HasFactory;
	
    protected $table = 'master_grade_promotion';
	protected $primaryKey = 'id_grade_promotion';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_grade_promotion', 'id_job_grade', 'code', 'description', 'status', 'id_company', 'created_by', 'updated_by'
    ];
	
}
