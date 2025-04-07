<?php

namespace App\Models\TimeAttendance\Overtime\OvertimeType;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterOvertimeType extends Model {
	
	protected $table = 'master_overtime_type';	
	protected $primaryKey = 'id_overtime_type';	
	const CREATED_AT = 'creation_date';
	const UPDATED_AT = 'update_date';
	
	protected $fillable = [
     'id_overtime_type','overtime_code','description','minimum_time','maximum_time','multiple_value','status','inactive_date','id_company','created_by', 'updated_by'
    ];
	
    public static function getdata() {
        $sql = "SELECT mot.id_overtime_type
                        ,mot.overtime_code
                        ,mot.description
                        ,mot.minimum_time
                        ,mot.maximum_time
                        ,mot.multiple_value
                        ,mot.status
                        ,mot.inactive_date
                        ,mot.id_company
                        ,mot.creation_date
                        ,mot.update_date
                        ,mot.created_by
                        ,mot.updated_by
                    FROM master_overtime_type mot
                    WHERE mot.id_company = ?";
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
