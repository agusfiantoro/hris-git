<?php

namespace App\Models\TimeAttendance\LeaveSetting\LeaveGroup;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterLeaveGroup extends Model {
	
	protected $table = 'master_leave_header';
    protected $primaryKey = 'id_leave_header';

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'id_leave_header', 'leave_group_name', 'description', 'status', 'id_company', 'created_by', 'updated_by'
    ];


    public static function getdata() {
        $sql = "SELECT mlh.id_leave_header
                        ,mlh.leave_group_name
                        ,mlh.description
                        ,mlh.status
                        ,mlh.id_company
                        ,mlh.creation_date
                        ,mlh.update_date
                        ,mlh.created_by
                        ,mlh.updated_by
                    FROM master_leave_header mlh
                    WHERE mlh.id_company = ?";
        $result = DB::select($sql, [session('id_company')]);
        return $result;
    }
	
	public static function get_leave_edit($data) {
        $result = [];
        $sql = "SELECT 
                    mlh.id_leave_header,                 
                    mlh.leave_group_name,                 
                    mlh.description,
                    mlh.status as status_header,
					mld.id_leave_type,
					mld.status
                FROM master_leave_header mlh				
				JOIN master_leave_detail mld
				ON mlh.id_leave_header = mld.id_leave_header
				WHERE mlh.id_leave_header  = ?";
        $result = (Array) DB::select($sql, [$data['id_leave_header']])[0];

        $sql = "SELECT * FROM master_leave_detail WHERE id_leave_header  = ?";
        $result_menu = DB::select($sql, [$data['id_leave_header']]);
        $collect_menu = collect($result_menu);
        $group_menu = $collect_menu->groupBy('id_leave_detail')->toArray();

        $result['leave'] = [];
        foreach (array_keys($group_menu) as $key => $value) {
            $result['leave'][] = [
                'id_leave_detail' => $group_menu[$value][0]->id_leave_detail,
                'id_leave_type' => $group_menu[$value][0]->id_leave_type,
                'leave_quota' => $group_menu[$value][0]->leave_quota,
                'status' => $group_menu[$value][0]->status,
            ];
        }
        //	dd($result);
        return $result;
    }

	
	public static function get_company() {
        $sql = "SELECT 
                        id_company id,
                        company_name text
                FROM master_company where id_company =" . session('id_company');
        $result = DB::select($sql);
        return $result;
    }
	
	public static function get_leave_type() {
        $sql = "SELECT 
                        id_leave_type id,
                        description text
                FROM master_leave_type where status = 'A' and id_company =" . session('id_company');
        $result = DB::select($sql);

        return $result;
    }
}
