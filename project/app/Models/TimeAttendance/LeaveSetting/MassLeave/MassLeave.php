<?php

namespace App\Models\TimeAttendance\LeaveSetting\MassLeave;

use Illuminate\Database\Eloquent\Model;
use DB;

class MassLeave extends Model
{
    
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
	}
    
    public function getemployeename($employeename) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select id_employee,name,nik_employee,id_user
                    from hr_employee
                    where name like '%".$employeename."%'
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
    
    public static function generatemassleave($employee_id=null,$enddate) {
        // $sql = "select * from GenerateMassLeave(null, null, null, ?)";
        // $query = DB::select($sql, [date('Y-m-t')]);
        $employee_id = $employee_id ?? 'null';
        $sql = "select * from GenerateMassLeave(?, ?, ".$employee_id.", ?)";
        $query = DB::select($sql, [session('id_company'), session('id_user'), $enddate]);

        if(!$query){
            return false;
        }
        return $query;
    }
}
