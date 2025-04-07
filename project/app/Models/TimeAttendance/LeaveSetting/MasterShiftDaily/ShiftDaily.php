<?php

namespace App\Models\TimeAttendance\LeaveSetting\MasterShiftDaily;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShiftDaily extends Model
{
    
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
	}
    
    public function getdata() {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select *
                    from master_daily_shift
                    where id_company = ?
                    order by id_shift desc
                ";
                $result_getdata = collect(DB::select($sql_getdata, [session('id_company')]))->toArray();
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

    public function insertdata($data) {
        $data = DB::table('master_daily_shift')
                ->insert($data);
        return $data;
    }
    
    public function geteditdata($id_shift) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select *
                    from master_daily_shift
                    where id_shift='".$id_shift."'
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
    
    public function editdata($data, $id_shift) {
        $data = DB::table('master_daily_shift')
                ->where('id_shift', $id_shift)
                ->update($data);
        return $data;
    }
    
    public function deletedata($id_shift) {
        $data = DB::table('master_daily_shift')
                ->where('id_shift', $id_shift)
                ->delete();
        return $data;
    }
}
