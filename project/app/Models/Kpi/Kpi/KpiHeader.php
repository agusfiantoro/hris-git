<?php

namespace App\Models\Kpi\Kpi;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KpiHeader extends Model
{
	use HasFactory;
	
    protected $table = 'hr_kpi_header';
	protected $primaryKey = 'id_kpi_header';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
    'id_kpi_header', 'id_kpi_group', 'kpi_month', 'notes', 'subtotal_kpi', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
