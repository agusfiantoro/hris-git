<?php

namespace App\Models\TimeAttendance\LeaveSetting\MasterShiftGroup;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShiftGroup extends Model
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
                    from master_shiftgroup_header
                    where id_company = ?
                    order by id_shiftgroup desc
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

    public function insertdata($header, $detail) {
        $insert_header = DB::table('master_shiftgroup_header')->insert($header);
        $id_shiftgroup = DB::getPdo()->lastInsertId();

        foreach ($detail as $key => $value) {
            $detail[$key]['id_shiftgroup'] = $id_shiftgroup;
        }
        $insert_detail = DB::table('master_shiftgroup_detail')->insert($detail);

        return $insert_header;
    }

    public function geteditdata($id_shiftgroup) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdatamaster="
                    select *
                    from master_shiftgroup_header
                    where id_shiftgroup='".$id_shiftgroup."'
                ";
                $result_getdatamaster = collect(DB::select($sql_getdatamaster))->first();
                
                $sql_getdatadetail="
                    select ds.*, sg.sequence as day, sg.status as status_detail
                    from master_shiftgroup_detail sg
                    join master_daily_shift ds on ds.id_shift=sg.id_shift
                    where id_shiftgroup='".$id_shiftgroup."' order by sg.sequence
                ";
                $result_getdatadetail = collect(DB::select($sql_getdatadetail))->toArray();
                $message=0;
            }else{
                $result_getdatamaster=[];
                $result_getdatadetail=[];
                $message="Gagal menghubungkan ke database";
            }

            return array("master"=>$result_getdatamaster,"detail"=>$result_getdatadetail,"message"=>$message);

        } catch (\Exception $e) {
            return array("master"=>'',"detail"=>'',"message"=>'Gagal menghubungkan ke database. '.$e);
        }
    }
    
    public function editdata($header, $id_shiftgroup, $detail) {
        $update_header = DB::table('master_shiftgroup_header')->where('id_shiftgroup', $id_shiftgroup)->update($header);
        $delete_detail_by_header = DB::table('master_shiftgroup_detail')->where('id_shiftgroup', $id_shiftgroup)->delete();
        $insert_detail = DB::table('master_shiftgroup_detail')->insert($detail);

        return $update_header;
    }
    
    public function deletedata($id_shiftgroup) {
        $delete_detail_by_header = DB::table('master_shiftgroup_detail')->where('id_shiftgroup', $id_shiftgroup)->delete();
        $delete_header = DB::table('master_shiftgroup_header')->where('id_shiftgroup', $id_shiftgroup)->delete();
        return $delete_header;
    }
}
