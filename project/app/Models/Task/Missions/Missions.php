<?php

namespace App\Models\Task\Missions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
//use App\Models\Employee\Employee\Employee;

class Missions extends Model
{
	use HasFactory;
	
    protected $table = 'hr_task_activity_answer';
	protected $primaryKey = 'id_task_activity_answer';
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	/*
	public static function getdata($idPos) {
		$sql = "SELECT DISTINCT ma.id_activity, ma.sequence, mt.description AS task, ma.activity, ma.target_evidence, 
				ma.evidence_type, htm.start_date, htm.end_date, ma.start_time, ma.end_time, 1 AS status, 
				ma.random_object_flag, moa.id_object_activity, moa.description AS random_act 
				FROM hr_task_activity hta
				JOIN master_activity ma
				ON hta.id_activity = ma.id_activity
				JOIN hr_task_management htm
				ON hta.id_task_management = htm.id_task_management
				JOIN master_task mt
				ON hta.id_task = mt.id_task
				JOIN master_position_detail mpd
				ON htm.id_position_routing = mpd.id_position_routing
				LEFT JOIN master_object_activity moa
				ON ma.id_activity = moa.id_activity AND ma.random_object_flag = true
				WHERE mpd.id_position_routing = ?
				ORDER BY ma.sequence ASC";	
        $result = DB::select($sql,[$idPos]);
        return $result;
    }
	*/
	public static function getdata($nik_employee) {
		$sql= "SELECT htaa.id_task_activity_answer, htaa.id_activity,htaa.id_object_activity, ma.sequence, 
				mt.description AS task, ma.activity, ma.target_evidence, 
				CASE
					WHEN evidence_type = 'Photo' THEN 'Photo (No Lock Location)'
					WHEN evidence_type = 'GPS' THEN 'Photo (Lock Location)'
					ELSE evidence_type
				END AS text_type, evidence_type, htaa.start_date, htaa.end_date, 
				htaa.submitted_flag AS status, ma.random_object_flag, moa.description AS random_act, ma.multiple_attachment_flag, 
				htaa.completion_date, he.lock_gps_location, 
				CASE
					WHEN NOW()::TIMESTAMP BETWEEN htaa.start_date AND htaa.end_date THEN 1
					WHEN CURRENT_DATE + 1 = htaa.start_date::date THEN 0
				END AS flag				
				FROM hr_task_activity_answer htaa
				JOIN master_activity ma
				ON htaa.id_activity = ma.id_activity
				JOIN master_task mt
				ON ma.id_task = mt.id_task
				LEFT JOIN master_object_activity moa
				ON htaa.id_activity = moa.id_activity AND htaa.id_object_activity = moa.id_object_activity
				LEFT JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				WHERE he.nik_employee = '".$nik_employee."' AND he.status = 'A'
				AND (NOW()::TIMESTAMP BETWEEN htaa.start_date AND htaa.end_date OR CURRENT_DATE + 1 = htaa.start_date::date)
	ORDER BY htaa.start_date ASC, htaa.completion_date ASC, ma.sequence ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getdataReview($myData) {
		$idEmp = $myData['emp_search'];
		$cycle = $myData['cycle_search'];
		$startDate = $myData['start_date'];
		$endDate = $myData['end_date'];
		
		if($idEmp){
			$empTask = " AND he.id_employee = ".$idEmp;
		}
		else{
			$empTask = "";
		}
		
		if($cycle){
			$cycleTask = " AND ma.task_cycle = '".$cycle."'";
		}
		else{
			$cycleTask = "";
		}
		if($startDate){
			$date = " AND (('".$startDate."' BETWEEN htaa.start_date::date AND htaa.end_date::date 
			OR '".$endDate."' BETWEEN htaa.start_date::date AND htaa.end_date::date)OR
				htaa.start_date::date BETWEEN '".$startDate."' AND '".$endDate."')";
		}
		else{
			$date = "";
		}
		$sql = "SELECT htaa.id_task_activity_answer, htaa.id_activity, he.name, he.nik_employee, ma.sequence, 
				mt.description AS task, ma.activity, ma.target_evidence, ma.task_cycle, 
				CASE
					WHEN evidence_type = 'Photo' THEN 'Photo (No Lock Location)'
					WHEN evidence_type = 'GPS' THEN 'Photo (Lock Location)'
					ELSE evidence_type
				END AS text_type, evidence_type,
				htaa.start_date, htaa.end_date, 
				htaa.submitted_flag AS status, ma.random_object_flag, moa.description AS random_act, 
				htaa.completion_date, htaa.score_answer
				FROM hr_task_activity_answer htaa
				JOIN master_activity ma
				ON htaa.id_activity = ma.id_activity
				JOIN master_task mt
				ON ma.id_task = mt.id_task
				LEFT JOIN master_object_activity moa
				ON htaa.id_activity = moa.id_activity AND htaa.id_object_activity = moa.id_object_activity
				LEFT JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				WHERE he.status = 'A' 
			--	AND htaa.submitted_flag = true 
				AND htaa.id_company = ".session('id_company')." ".$date." ".$cycleTask." ".$empTask."
				ORDER BY htaa.completion_date ASC, he.name ASC, htaa.start_date ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_month() {
		$sql = "SELECT DISTINCT TO_CHAR(htaa.start_date, 'yyyy-mm') id, 
					CONCAT(TO_CHAR(htaa.start_date::date, 'Mon'),' ',date_part('year', htaa.start_date::date)) text
					FROM hr_task_activity_answer htaa
					WHERE htaa.id_company = ".session('id_company')." 
					ORDER BY id DESC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getLeaderboard($month_search,$emp_search) {
		$month = " ";
		$emp = " ";
		if($month_search){
			if($month_search[0] != null){
				foreach($month_search as $key=>$val){
					$x[] = "'".$val."'";
				}
				$arr_month = implode(",",$x);
				$month = " AND fix.id_month IN(".$arr_month.")";
			}
		}
		if($emp_search){
			if($emp_search[0] != null){
				$arr_emp = implode(",",$emp_search);
				$emp = " AND fix.id_employee IN(".$arr_emp.")";
			}
		}
		$sql = "SELECT * FROM (
				SELECT total.id_month, total.month, total.id_employee, total.name, total.nik_employee, total.pos, total.dept,
				total.region, total.branch, SUM(total.score_answer) AS score_total
				FROM (	
					SELECT he.id_employee, he.name, he.nik_employee, mpr.description AS pos,
					md.description AS dept, mr.description AS region, mb.description AS branch, TO_CHAR(htaa.start_date, 'yyyy-mm') AS id_month,
					CONCAT(TO_CHAR(htaa.start_date::date, 'Mon'),' ',date_part('year', htaa.start_date::date)) AS month,
					htaa.score_answer
					FROM hr_task_activity_answer htaa
					JOIN master_activity ma
					ON htaa.id_activity = ma.id_activity
					LEFT JOIN hr_employee he
					ON htaa.id_employee = he.id_employee
					LEFT JOIN master_position_detail mpd
					ON htaa.id_position_detail = mpd.id_position_detail
					LEFT JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_department md
					ON htaa.id_department = md.id_dept
					LEFT JOIN master_branch mb
					ON htaa.id_branch = mb.id_branch
					LEFT JOIN master_region mr
					ON mb.id_region = mr.id_region
					WHERE htaa.id_company = ".session('id_company')."
				) AS total
				GROUP BY total.id_month, total.id_employee, total.name, 
				total.nik_employee, total.pos, total.dept,
				total.region, total.branch, total.month
			) AS fix
			WHERE fix.score_total IS NOT NULL ".$month." ".$emp."
			ORDER BY score_total DESC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getSummary($month_search,$emp_search) {
		$month = " ";
		$emp = " ";
		if($month_search){
			if($month_search[0] != null){
				foreach($month_search as $key=>$val){
					$x[] = "'".$val."'";
				}
				$arr_month = implode(",",$x);
				$month = " AND fix.id_month IN(".$arr_month.")";
			}
		}
		if($emp_search){
			if($emp_search[0] != null){
				$arr_emp = implode(",",$emp_search);
				$emp = " AND fix.id_employee IN(".$arr_emp.")";
			}
		}
		
		$sql = "SELECT * FROM (
				SELECT total.id_month, total.month, total.task, total.id_employee, total.name, total.nik_employee, total.pos, total.dept,
				total.region, total.branch, SUM(total.score_answer) AS score_total
				FROM (	
					SELECT mt.description AS task, he.id_employee, he.name, he.nik_employee, mpr.description AS pos,
					md.description AS dept, mr.description AS region, mb.description AS branch, TO_CHAR(htaa.start_date, 'yyyy-mm') AS id_month,
					CONCAT(TO_CHAR(htaa.start_date::date, 'Mon'),' ',date_part('year', htaa.start_date::date)) AS month,
					htaa.score_answer
					FROM hr_task_activity_answer htaa
					JOIN master_activity ma
					ON htaa.id_activity = ma.id_activity
					JOIN master_task mt
					ON ma.id_task = mt.id_task
					LEFT JOIN hr_employee he
					ON htaa.id_employee = he.id_employee
					LEFT JOIN master_position_detail mpd
					ON htaa.id_position_detail = mpd.id_position_detail
					LEFT JOIN master_position_routing mpr
					ON mpd.id_position_routing = mpr.id_routing
					LEFT JOIN master_department md
					ON htaa.id_department = md.id_dept
					LEFT JOIN master_branch mb
					ON htaa.id_branch = mb.id_branch
					LEFT JOIN master_region mr
					ON mb.id_region = mr.id_region
					WHERE htaa.id_company = ".session('id_company')."	
				) AS total
				GROUP BY total.id_month, total.task, total.id_employee, total.name, 
				total.nik_employee, total.pos, total.dept,
				total.region, total.branch, total.month
			) AS fix
			WHERE fix.score_total IS NOT NULL ".$month." ".$emp."
			ORDER BY score_total DESC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_search_emp() {
		$sql = "SELECT DISTINCT he.id_employee id, he.name text
				FROM hr_task_activity_answer htaa
				JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				WHERE htaa.id_company = ".session('id_company')."
				ORDER BY he.name ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_score($idAnswer) {
		$sql = "SELECT ma.maximum_score, htaa.note_rejected, ma.activity, ma.target_evidence
				FROM hr_task_activity_answer htaa
				LEFT JOIN master_activity ma
				ON htaa.id_activity = ma.id_activity
				WHERE htaa.id_task_activity_answer = ".$idAnswer;	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_photo($idAnswer) {
		$sql = "SELECT he.id_employee, he.nik_employee, htata.* 
				FROM hr_task_attachment_answer htata
				LEFT JOIN hr_task_activity_answer htaa
				ON htata.id_task_activity_answer = htaa.id_task_activity_answer
				JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				WHERE htata.id_task_activity_answer = ".$idAnswer;	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_essay($idAnswer) {
		$sql = "SELECT htaa.essay_answer
				FROM hr_task_activity_answer htaa
				WHERE htaa.id_task_activity_answer = ".$idAnswer;	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_task() {
		$sql = "SELECT DISTINCT mt.id_task id, mt.description text
				FROM hr_task_activity_answer htaa
				LEFT JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				LEFT JOIN master_activity ma
				ON htaa.id_activity = ma.id_activity
				LEFT JOIN master_task mt
				ON ma.id_task = mt.id_task
				WHERE he.id_user = ".session('id_user')." AND he.status = 'A'
				AND htaa.id_company = ".session('id_company');	
        $result = DB::select($sql);
        return $result;
    }
	
	public static function getMyScore($myData) {
	//	dd($myData);
		$idTask = $myData['task_search'];
		$cycle = $myData['cycle_search'];
		$startDate = $myData['start_date'];
		$endDate = $myData['end_date'];
		if($idTask){
			$task = " AND mt.id_task = ".$idTask;
		}
		else{
			$task = "";
		}		
		if($cycle){
			$cycleTask = " AND ma.task_cycle = '".$cycle."'";
		}
		else{
			$cycleTask = "";
		}
		if($startDate){
			$date = " AND (('".$startDate."' BETWEEN htaa.start_date::date AND htaa.end_date::date 
			OR '".$endDate."' BETWEEN htaa.start_date::date AND htaa.end_date::date)OR
				htaa.start_date::date BETWEEN '".$startDate."' AND '".$endDate."')";
		}
		else{
			$date = "";
		}
		$sql = "SELECT htaa.id_task_activity_answer, htaa.id_activity,htaa.id_object_activity, ma.sequence, 
				mt.description AS task, ma.activity, ma.target_evidence, 
				CASE
					WHEN evidence_type = 'Photo' THEN 'Photo (No Lock Location)'
					WHEN evidence_type = 'GPS' THEN 'Photo (Lock Location)'
					ELSE evidence_type
				END AS text_type, evidence_type,
				ma.task_cycle, htaa.start_date, htaa.end_date, 
				htaa.submitted_flag AS status, ma.random_object_flag, moa.description AS random_act, ma.multiple_attachment_flag, 
				htaa.completion_date, ma.maximum_score, htaa.score_answer, htaa.note_rejected
				FROM hr_task_activity_answer htaa
				JOIN master_activity ma
				ON htaa.id_activity = ma.id_activity
				JOIN master_task mt
				ON ma.id_task = mt.id_task
				LEFT JOIN master_object_activity moa
				ON htaa.id_activity = moa.id_activity AND htaa.id_object_activity = moa.id_object_activity
				LEFT JOIN hr_employee he
				ON htaa.id_employee = he.id_employee
				WHERE he.id_user = ".session('id_user')." AND he.status = 'A' ".$task." ".$cycleTask." ".$date."
				ORDER BY mt.description, ma.sequence ASC";	
        $result = DB::select($sql);
        return $result;
    }

}
