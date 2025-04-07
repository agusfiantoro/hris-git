<?php

namespace App\Models\Kpi\KpiSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaAnswer extends Model
{
	use HasFactory;
	
    protected $table = 'hr_pa_answer';
	protected $primaryKey = 'id_pa_answer';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_pa_answer', 'id_pa_question', 'sequence', 'description', 'weight_score', 'is_corrected_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];
			
}
