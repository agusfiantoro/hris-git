<?php

namespace App\Models\Task\Task;

use App\Models\Organization\OrganizationStructure\OrganizationUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
//use App\Models\Employee\Employee\Employee;

class TaskAssignment extends Model
{
	use HasFactory;
	
    protected $table = 'hr_task_management';
	protected $primaryKey = 'id_task_management';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	public static function getdata() {
		$sql = "SELECT DISTINCT htm.*, md.description AS dept,
				CASE
					WHEN htm.job_class_group = 'spv-level' THEN 'Supervisor/Coordinator'
					WHEN htm.job_class_group = 'manager-level' THEN 'Manager'
					WHEN htm.job_class_group = 'GM-level' THEN 'Head/Director'
					ELSE 'Staff'
				END AS grade,	
				j_a.position, he.name AS respon_by, he2.name AS managed_by
				FROM hr_task_management htm
				LEFT JOIN master_department md
				ON htm.id_department = md.id_dept
				LEFT JOIN master_job_grade mjg
				ON htm.job_class_group = mjg.job_class_group
				JOIN (
					SELECT j_t.id_task_management, STRING_AGG (mpr.description,', ') AS position
					FROM master_position_routing mpr
					JOIN (
						SELECT DISTINCT htm.id_task_management, unnest(htm.id_position_routing) AS id_routing
						FROM hr_task_management htm		
					) AS j_t
					ON mpr.id_routing = j_t.id_routing
					GROUP BY j_t.id_task_management
				) AS j_a
				ON htm.id_task_management = j_a.id_task_management
				LEFT JOIN hr_employee he
				ON htm.id_responsible_by = he.id_employee
				LEFT JOIN hr_employee he2
				ON htm.id_managed_by = he2.id_employee
				WHERE htm.id_company = ".session('id_company');	
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
	
	public static function get_position($idDept,$idGrade) {
		$sql = "SELECT DISTINCT mpr.id_routing id, CONCAT(mpr.description,' (',mjp.description,')') AS text 
				FROM master_position_detail mpd
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing AND mpd.id_employee IS NOT NULL AND mpd.status = 'A' 
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				WHERE mpd.id_company = ".session('id_company')."
				AND mjp.id_dept = ? AND mjg.job_class_group = '".$idGrade."'
				ORDER BY text ASC";	
        $result = DB::select($sql,[$idDept]);
        return $result;
    }
	
	public static function get_respon($idDept) {
		$dept = OrganizationUnit::where('id_dept',$idDept)->first();
		if($dept->department_code == '140D_LOGD' || $dept->department_code == '140W_LOGW'){
			$deptCode = " AND md.department_code IN('140D_LOGD','140W_LOGW')";
		}
		else{
			$deptCode = " AND md.department_code = '".$dept->department_code."'";
		}
        $sql = "SELECT he.id_employee id, he.name text
				FROM hr_employee he
				JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				JOIN master_job_position mjp
				ON mpr.id_position = mjp.id_position
				JOIN master_department md
				ON mjp.id_dept = md.id_dept
				WHERE he.status = 'A' AND mpd.id_company = ?
				AND mjg.job_class_group IN('gm-level') ".$deptCode;
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	
	public static function get_employee_by() {
        $sql = "SELECT he.id_employee id, he.name text, mpr.id_routing, mpd.id_position_detail, mpd.id_location, mpr.description AS route_name, 
				mjg.job_class_group, ml.description AS location_name
				FROM hr_employee he
				LEFT JOIN master_position_detail mpd
				ON he.id_employee = mpd.id_employee
				LEFT JOIN master_position_routing mpr
				ON mpd.id_position_routing = mpr.id_routing
				LEFT JOIN master_job_grade mjg
				ON mpr.id_job_grade = mjg.id_job_grade
				LEFT JOIN master_location ml
				ON mpd.id_location = ml.id_location
				WHERE mpd.id_company = ? AND he.id_user = ? AND he.status = 'A'";
        $result = DB::select($sql,[session('id_company'),session('id_user')]);
        return $result;
    }
	
	public static function get_task($idPos=null) {
		$sql = "SELECT mt.id_task id, mt.description text
				FROM master_task mt
				WHERE mt.status = 'A' AND mt.id_company = ?
				AND mt.id_position_routing = ?
				ORDER BY mt.description ASC";	
        $result = DB::select($sql,[session('id_company'),$idPos]);
        return $result;
    }
	
	public static function get_activity($idTask=null) {
		$sql = "SELECT ma.id_activity id, ma.activity text
				FROM master_activity ma
				WHERE ma.id_task = ?
				ORDER BY ma.sequence ASC";	
        $result = DB::select($sql,[$idTask]);
        return $result;
    }
	
	public static function get_detail_act($idAct=null) {
		$sql = "SELECT ma.*, CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time
				FROM master_activity ma
				WHERE ma.id_activity = ? 
				ORDER BY ma.sequence ASC";	
        $result = DB::select($sql,[$idAct]);
        return $result;
    }
/*	
	public static function get_task_assign($arr_task=null) {
		if($arr_task != null){
			$arr = collect($arr_task)->pluck('id_task')->toArray();
			$impTask = implode(",",$arr);
			$whereTask = " AND ma.id_task IN(".$impTask.")";
		}
		else{
			$whereTask = " ";
		}
		$sql = "SELECT mt.description AS name_task, mt.task_type, ma.*, 
				CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time 
				FROM master_activity ma
				JOIN master_task mt
				ON ma.id_task = mt.id_task
				WHERE ma.id_company = ? ".$whereTask."
				ORDER BY mt.description ASC, ma.sequence ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
	}
*/
	public static function get_task_assign($arr_task=null) {
		$result = [];
		foreach($arr_task as $key=>$val){
			if($val['task_type'] == 'Random'){
				$sql = "SELECT mt.description AS name_task, mt.task_type, mt.id_task, ma.id_activity,
						ma.activity, ma.target_evidence, ma.evidence_type, ma.task_cycle, 
						CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time,
						ma.maximum_score
						FROM master_activity ma
						JOIN master_task mt
						ON ma.id_task = mt.id_task
						WHERE ma.id_company = ".session('id_company')." AND ma.id_task = ".$val['id_task']." AND ma.status = 'A'
						ORDER BY mt.description ASC, ma.sequence ASC";	
			}
			else{
				$sql = "SELECT mt.description AS name_task, mt.task_type, null AS id_activity, fix.*
						FROM master_task mt
						JOIN (		
							SELECT tes.id_task, STRING_AGG(tes.activity,'$$$') AS activity, 
								STRING_AGG(tes.target_evidence,'$$$') AS target_evidence, 
								STRING_AGG(tes.evidence_type,'$$$') AS evidence_type,
								STRING_AGG(tes.task_cycle,'$$$') AS task_cycle,
								STRING_AGG(tes.time,'$$$') AS time,
								STRING_AGG(tes.maximum_score::text,'$$$') AS maximum_score
							FROM (
								SELECT mt.id_task, ma.activity, ma.target_evidence, ma.evidence_type, ma.task_cycle, 
								CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time,
								ma.maximum_score
								FROM master_activity ma
								LEFT JOIN master_task mt
								ON ma.id_task = mt.id_task
								WHERE mt.id_task = ".$val['id_task']." AND mt.task_type = 'Mandatory'
							) AS tes
							GROUP BY id_task
						) AS fix
						ON mt.id_task = fix.id_task
						WHERE mt.id_company = ".session('id_company')." AND mt.id_task = ".$val['id_task']."
						ORDER BY mt.description ASC";	
			}
			$result = array_merge($result,DB::select($sql));
		}
        return $result;
	}
	
	public static function get_task_list($idDept,$idGrade) {
		$sql = "SELECT DISTINCT mt.id_task, mt.description AS name_task, mt.notes,
				mt.task_type
				FROM master_task mt
				LEFT JOIN master_job_grade mjg
				ON mt.job_class_group = mjg.job_class_group
				WHERE mt.status = 'A' AND mt.id_company =?
				AND mt.id_department = ".$idDept." AND mjg.job_class_group = '".$idGrade."'
				ORDER BY mt.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
	}
	
	public static function get_edit($id_task_management) {
        $result = [];
		$sql = "SELECT htm.* FROM hr_task_management htm
				WHERE htm.id_task_management = ".$id_task_management;				
        $result = (Array) DB::select($sql)[0];
		
		$sql2 = "SELECT mt.task_type, mt.description AS task, mt.id_task, hta.id_task_activity, hta.random_type, ma.id_activity,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN ma.activity
					ELSE mandatory.activity
				END AS activity,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN ma.target_evidence 
					ELSE mandatory.target_evidence
				END AS target_evidence,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN ma.evidence_type 
					ELSE mandatory.evidence_type
				END AS evidence_type,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN ma.task_cycle 
					ELSE mandatory.task_cycle
				END AS task_cycle,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN ma.maximum_score::text 
					ELSE mandatory.maximum_score
				END AS maximum_score,
				CASE 
					WHEN ma.id_activity IS NOT NULL THEN CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) 
					ELSE mandatory.time
					END AS time,
				hta.last_date_generated	
				FROM hr_task_activity hta
				LEFT JOIN (
					SELECT unnest(hta.id_activity) id_act, 
					hta.id_task_activity, hta.id_task_management, hta.id_task
					FROM hr_task_activity hta
					WHERE hta.id_task_management = ".$id_task_management."
				) AS un
				ON hta.id_task_activity = un.id_task_activity AND hta.id_task_management = un.id_task_management
				AND hta.id_task = un.id_task
				LEFT JOIN master_activity ma
				ON un.id_act = ma.id_activity
				JOIN master_task mt
				ON hta.id_task = mt.id_task
				LEFT JOIN (
					SELECT tes.id_task, STRING_AGG(tes.activity,'$$$') AS activity, 
						STRING_AGG(tes.target_evidence,'$$$') AS target_evidence, 
						STRING_AGG(tes.evidence_type,'$$$') AS evidence_type,
						STRING_AGG(tes.task_cycle,'$$$') AS task_cycle,
						STRING_AGG(tes.time,'$$$') AS time,
						STRING_AGG(tes.maximum_score::text,'$$$') AS maximum_score
					FROM (
						SELECT DISTINCT mt.id_task, ma.activity, ma.target_evidence, ma.evidence_type, ma.task_cycle, 
						CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time,
						ma.maximum_score
						FROM hr_task_activity hta
						JOIN master_task mt
						ON hta.id_task = mt.id_task
						JOIN master_activity ma
						ON mt.id_task = ma.id_task
						WHERE hta.id_task_management = ".$id_task_management." AND hta.id_activity IS NULL
					) AS tes
					GROUP BY id_task
				) AS mandatory
				ON hta.id_task = mandatory.id_task
				WHERE hta.id_task_management = ".$id_task_management."
				ORDER BY task ASC, activity ASC";
        $result_menu2 = DB::select($sql2);
        $collect_menu2 = collect($result_menu2);
        $group_menu2 = $collect_menu2->toArray();       
		$result['res'] = [];
        foreach ($group_menu2 as $key => $value) {
            $result['res'][] = [
                'id_task_activity' => $value->id_task_activity,
                'id_task' => $value->id_task,
                'task_type' => $value->task_type,
                'task_text' => $value->task,
                'random_type' => $value->random_type,
                'id_activity' => $value->id_activity,
                'last_date_generated' => $value->last_date_generated != NULL ? Carbon::parse($value->last_date_generated)->translatedFormat('d M Y') : '-',
                'activity_text' => explode("$$$",$value->activity),
                'evidence' => explode("$$$",$value->target_evidence),
                'type' => explode("$$$",$value->evidence_type),
                'cycle' => explode("$$$",$value->task_cycle),
                'time' => explode("$$$",$value->time),
                'score' => explode("$$$",$value->maximum_score),
			];
        }
	//	dd($result);
        return $result;
    }
	
	public static function gen_task($idTask,$idCompany) {
		$sql = "SELECT * FROM GenerateTaskActivity(?,?)";	
        $result = DB::select($sql,[$idTask,$idCompany]);
        return $result;
    }
	/*
	public static function get_task_list($idPos=[]) {
		$sql = "SELECT mt.id_task, mt.description AS name_task, mt.notes,
				mt.task_type, mpr.description AS position
				FROM master_task mt
				LEFT JOIN master_position_routing mpr
				ON mt.id_position_routing = mpr.id_routing
				WHERE mt.status = 'A' AND mt.id_company = ?
				AND mt.id_position_routing IN(".$idPos.") 
				ORDER BY mpr.description ASC, mt.description ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
	}
	
	public static function get_task_list($idPos=[],$idAct=null) {
		if($idAct != null){
			$impAct = implode(",",$idAct);
			$act = " AND ma.id_activity IN(".$impAct.")";
		}
		else{
			$act = " ";
		}
		$sql = "SELECT mt.description AS name_task, mpr.description AS position, ma.*,
				CONCAT(to_char(ma.start_time::time, 'HH:MI'),' - ',to_char(ma.end_time::time, 'HH:MI')) AS time,
				CASE
					WHEN ma.random_object_flag = true THEN 'Yes'
					WHEN ma.random_object_flag = false THEN 'No'
				END AS random
				FROM master_task mt
				LEFT JOIN master_activity ma
				ON mt.id_task = ma.id_task
				LEFT JOIN master_position_routing mpr
				ON mt.id_position_routing = mpr.id_routing
				WHERE mt.status = 'A' AND mt.id_company = ?
				AND mt.id_position_routing IN(".$idPos.") ".$act."
				ORDER BY ma.id_task ASC, ma.sequence ASC";	
        $result = DB::select($sql,[session('id_company')]);
        return $result;
    }
	*/
   
}
