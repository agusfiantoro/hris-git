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
	
	public static function getdata($idDept) {
		if($idDept){
			$dept = " AND md.id_dept = ".$idDept;
		}
		else{
			$dept = " ";
		}
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
				WHERE mt.id_company = ".session('id_company')." ".$dept."
				ORDER BY md.description ASC, mt.id_task ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_dept() {
		$sql = "SELECT md.id_dept id, md.description text
				FROM master_department md
				WHERE md.id_company = ".session('id_company')." AND md.status = 'A' AND md.department_code NOT IN('200_DIR')
				ORDER BY md.description ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_grade($idDept=null) {
		$sql = "SELECT fix.id, STRING_AGG (fix.text,'/') AS text
				FROM(				
					SELECT DISTINCT mjg.job_class_group id, mjg.description text
					FROM master_job_grade mjg
					JOIN master_position_routing mpr
					ON mjg.id_job_grade = mpr.id_job_grade
					JOIN master_job_position mjp
					ON mpr.id_position = mjp.id_position
					WHERE mjg.id_company = ".session('id_company')." AND mjg.status = 'A' 
					AND mjp.id_dept = ? AND mjg.job_class_group NOT IN ('ant-level')
				) AS fix
				GROUP BY fix.id";	
        $result = DB::select($sql,[$idDept]);
        return $result;
    }
	
	public static function get_edit($id_task) {
        $result = [];
		$sql = "SELECT * FROM master_task mt
				WHERE mt.id_task = ".$id_task;				
        $result = (Array) DB::select($sql)[0];
		
		$sql2 = "SELECT * FROM master_activity ma
				 WHERE ma.id_task = ".$id_task."
				 ORDER BY ma.sequence ASC";
		$result_menu = DB::select($sql2);
        $group_menu = collect($result_menu)->toArray();       
		$result['res'] = [];
		foreach ($group_menu as $key => $value) {
            $result['res'][] = $value;
        }
        return $result;
    }
	
}
