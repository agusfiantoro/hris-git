<?php

namespace App\Models\TimeAttendance\LeaveSetting\LeaveGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterLeaveDetail extends Model {
	
	protected $table = 'master_leave_detail';
    protected $primaryKey = 'id_leave_detail';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_leave_detail', 'id_leave_header', 'id_leave_type', 'leave_quota', 'status', 'id_company', 'created_by', 'updated_by'
    ];

}
