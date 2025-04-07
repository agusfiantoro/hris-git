<?php

namespace App\Models\TimeAttendance\LeaveSetting\MasterHoliday;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'hr_work_days';
    protected $fillable = ['id_workdays', 'id_employee', 'schedule_time_in', 'schedule_time_out',
    'actual_time_in', 'actual_time_out', 'late_in', 'early_out', 'work_hours', 'overtime',
    'target_id_location', 'target_name', 'target_address', 'target_latitude', 'target_longitude',
    'current_name', 'current_address', 'current_latitude', 'current_longitude', 'image_attachment',
    'id_company'];

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';
    protected $primaryKey = 'id_workdays';

}
