<?php

namespace App\Models\TimeAttendance\LeaveSetting\LeaveType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterLeaveType extends Model {
	
	protected $table = 'master_leave_type';	
	protected $primaryKey = 'id_leave_type';	
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
     'id_leave_type','leave_code','description','deduct_leave','day_count','leave_day_type','repeat_period','repeat_period_number','leave_entitlement_period_start','available_leave_after','repeated','leave_valid_end_period','grace_period_req','carry_over_to_next_entitlement','limit_carry_over','max_carry_over','enable_minus_leave','max_minus_leave','day_limit_submit_request','if_over_day_limit','req_attachment','status','inactive_date','id_company','created_by', 'updated_by'
    ];

    public static function getdata() {
        $sql = "SELECT mlt.id_leave_type
                        ,mlt.leave_code
                        ,mlt.description
                        ,mlt.deduct_leave
                        ,mlt.day_count
                        ,mlt.leave_day_type
                        ,mlt.repeat_period
                        ,mlt.repeat_period_number
                        ,mlt.leave_entitlement_period_start
                        ,mlt.available_leave_after
                        ,mlt.repeated
                        ,mlt.leave_valid_end_period
                        ,mlt.grace_period_req
                        ,mlt.carry_over_to_next_entitlement
                        ,mlt.limit_carry_over
                        ,mlt.max_carry_over
                        ,mlt.enable_minus_leave
                        ,mlt.max_minus_leave
                        ,mlt.day_limit_submit_request
                        ,mlt.if_over_day_limit
                        ,mlt.req_attachment
                        ,mlt.status
                        ,mlt.inactive_date
                        ,mlt.id_company
                        ,mlt.creation_date
                        ,mlt.update_date
                        ,mlt.created_by
                        ,mlt.updated_by
                    FROM master_leave_type mlt
                    WHERE mlt.id_company = ?";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_company() {
        $sql = "SELECT  id_company id,
                        company_name text
                FROM master_company 
                where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }

}
