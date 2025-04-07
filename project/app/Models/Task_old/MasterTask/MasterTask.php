<?php

namespace App\Models\Task\MasterTask;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MasterTask extends Model
{
	use HasFactory;
	
    protected $table = 'master_task';
	protected $primaryKey = 'id_task';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	public static function getdata() {
		$sql = "SELECT DISTINCT mt.*, md.description AS dept,
				CASE
					WHEN mt.job_class_group = 'spv-level' THEN 'Supervisor/Coordinator'
					WHEN mt.job_class_group = 'manager-level' THEN 'Manager'
					WHEN mt.job_class_group = 'GM-level' THEN 'Head/Director'
					ELSE 'Staff'
				END AS grade
				FROM master_task mt
				LEFT JOIN master_job_grade mjg
				ON mt.job_class_group = mjg.job_class_group
				LEFT JOIN master_department md
				ON mt.id_department = md.id_dept
				WHERE mt.id_company = ".session('id_company')."
				ORDER BY md.description ASC, mt.id_task ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
}
