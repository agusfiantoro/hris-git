<?php

namespace App\Models\Kpi\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AppraiserResult extends Model
{
	use HasFactory;
	
    protected $table = 'hr_appraisers_result';
	protected $primaryKey = 'id_appraiser_results';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_appraiser_results', 'id_qualitative_appraisers', 'id_pa_question', 'id_pa_answer', 'description_answer', 'total_hit_score', 'total_maximum_score', 'total_percent', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
