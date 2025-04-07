<?php

namespace App\Models\TimeAttendance\Attendance;

use Illuminate\Database\Eloquent\Model;
use DB;

class GenerateAttendance extends Model
{
    
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
	}
    
    public function getemployeename($employeename) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select id_employee,name,nik_employee,id_user,status
                    from hr_employee
                    where name like '%".$employeename."%'
                    and id_company = '".session('id_company')."'
                    order by name
                ";
                $result_getdata = collect(DB::select($sql_getdata))->toArray();
                $message=0;
            }else{
                $result_getdata=[];
                $message="Gagal menghubungkan ke database";
            }

            return array("data"=>$result_getdata,"message"=>$message);

        } catch (\Exception $e) {
            return array("data"=>'',"message"=>'Gagal menghubungkan ke database. '.$e);
        }
    }
    
    public static function generateattendance($employeename,$employeeuserid,$startdate,$enddate) {
        $sql_daily = "select * from generateworkdays(?, ?, ?, ?)";
        $generateattendance = DB::select($sql_daily, [session('id_company'), $startdate, $enddate, $employeeuserid]);
        if(!$generateattendance){
            return null;
        } else {
            return true;
        }
    }
}
