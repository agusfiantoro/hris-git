<?php

namespace App\Models\Kpi\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KpiDetail extends Model
{
	use HasFactory;
	
    protected $table = 'hr_kpi_detail';
	protected $primaryKey = 'id_kpi_detail';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_kpi_detail', 'id_kpi_header', 'id_course_header', 'id_kpi_category', 'kpi_value', 'weight_prosentase', 'id_kpi_type', 'kpi_target_value', 'description', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
