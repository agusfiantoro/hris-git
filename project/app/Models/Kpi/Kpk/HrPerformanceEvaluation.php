<?php

namespace App\Models\Kpi\Kpk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrPerformanceEvaluation extends Model
{
	use HasFactory;
	
    protected $table = 'hr_performance_evaluation';
	protected $primaryKey = 'id_performance_evaluation';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}