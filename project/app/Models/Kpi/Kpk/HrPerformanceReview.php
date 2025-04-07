<?php

namespace App\Models\Kpi\Kpk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HrPerformanceReview extends Model
{
	use HasFactory;
	
    protected $table = 'hr_performance_review';
	protected $primaryKey = 'id_performance_review';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
}