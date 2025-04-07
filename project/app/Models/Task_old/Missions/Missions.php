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
		$sql = "SELECT htaa.id_task_activity_answer, htaa.id_activity,htaa.id_object_activity, ma.sequence, 
				mt.description AS task, ma.activity, ma.target_evidence, ma.evidence_type, htaa.start_date, htaa.end_date, 
				htaa.submitted_flag AS status, ma.random_object_flag, moa.description AS random_act, ma.multiple_attachment_flag, 
				htaa.completion_date, he.lock_gps_location
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
				AND NOW()::TIMESTAMP BETWEEN htaa.start_date AND htaa.end_date
				ORDER BY ma.sequence ASC";	
        $result = DB::select($sql);
        return $result;
    }
	
}
