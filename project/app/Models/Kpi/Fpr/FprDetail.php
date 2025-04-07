<?php

namespace App\Models\Kpi\Fpr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FprDetail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_fpr_detail';
	protected $primaryKey = 'id_fpr_detail';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_fpr_detail', 'id_fpr_header', 'id_period', 'id_pa_question', 'id_pa_answer', 'description_answer', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
