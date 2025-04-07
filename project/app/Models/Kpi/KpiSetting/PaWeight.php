<?php

namespace App\Models\Kpi\KpiSetting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PaWeight extends Model
{
	use HasFactory;
	
    protected $table = 'master_weight_kpi';
	protected $primaryKey = 'id_master_weight_kpi';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_master_weight_kpi', 'appraisers_hierarchy', 'weight_value', 'status', 'id_company', 'created_by', 'updated_by'
    ];
			
}
