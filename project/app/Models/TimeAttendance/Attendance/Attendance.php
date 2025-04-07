<?php

namespace App\Models\TimeAttendance\Attendance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\TimeAttendance\Attendance\GenerateAttendance;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\Curl;

class Attendance extends Model {

    protected $table = 'hr_work_days';
    protected $fillable = ['id_workdays', 'id_employee', 'schedule_time_in', 'schedule_time_out',
        'actual_time_in', 'actual_time_out', 'late_in', 'early_out', 'work_hours', 'overtime',
        'target_id_location', 'target_name', 'target_address', 'target_latitude', 'target_longitude',
        'current_name', 'current_address', 'current_latitude', 'current_longitude', 'image_attachment',
        'id_company','schedule_employee_timezone','current_employee_timezone'];

    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'update_date';

    protected $primaryKey = 'id_workdays';

    public static function getAttendance($data) {
        $id_employee    = @$data['id_employee'];
		$id_company     = session('id_company');
        $id_user        = session('id_user');
        $today          = date('Y-m-d');
        $add1month      = date('Y-m-d', strtotime($today."+1 month"));
        /* $sql2 = "SELECT ([current_date])
            FROM [hr_work_days] 
            WHERE [id_employee] = ? 
            AND [current_date] = CAST(getdate() AS DATE)
            ORDER BY [current_date] DESC
            ";
		$result2 = DB::select($sql2, [$id_employee]); */
        /* if(count($result2) == 0){ */
            // $generateworksdaydaily = DB::select("SET NOCOUNT ON; declare @intResult int; EXEC  GenerateWorkdaysDaily ?, ?, ?, ?, @intResult OUT",[session('id_company'),date('Y-m-d'),date('Y-m-d'),$id_employee]);
            
        /* } */
        

        $workdays = DB::table('hr_work_days as hwd')
                    ->select('hwd.*')
                    ->whereIn('hwd.current_dates', [$today, $add1month])
                    ->where('hwd.id_employee', '=', $id_employee)
                    ->get();
        
        $workdayNow = $workdays->where('current_dates', $today)->first();

        if(!$workdayNow){
            if(!$id_employee){
                return null;
            }
            // $sql_daily = "select * from generateworkdaysnewemployee(?, ?, ?, ?, ?)";
            $start      = date('Y-m-d', strtotime($today."-5 days"));
            $end        = $add1month;
            // $generateworksdaydaily = DB::select($sql_daily, [$id_employee, $id_company, $start, $end, $id_user]);
        //    $generateworksdaydaily = WorkDays::patchWorkdays([$id_company], $start, $end, $id_employee);
        }
        
        if(!$id_employee){
            return null;
        }

        $workdayAfter1Month = $workdayNow = $workdays->where('current_dates', $add1month)->first();
        if(!$workdayAfter1Month){
            // $sql_daily = "select * from generateworkdaysnewemployee(?, ?, ?, ?, ?)";
            $start_      = $add1month;
            $end_        = $add1month;
            // $generateworksdaydaily = DB::select($sql_daily, [$id_employee, $id_company, $start_, $end_, $id_user]);
            $generateworksdaydaily = WorkDays::patchWorkdays([$id_company], $start_, $end_, $id_employee);
        }
        // $sql = "select spfaev.*, mds.start_time, mds.end_time, mds.shift_code 
        //         from sp_funct_absence_employee_view (?,?) spfaev 
        //         left join master_daily_shift mds on mds.id_shift = spfaev.id_shift 
        //         where spfaev.current_dates <= current_date";
        // $result = DB::select($sql, [$id_employee,$id_company]);

        $data = DB::table(DB::raw("sp_funct_absence_employee_view(".$id_employee.",".$id_company.") spfaev"))
            ->join('master_daily_shift as mds', 'mds.id_shift', '=', 'spfaev.id_shift', 'left')
            ->leftJoin('master_position_detail as mpd', function($join){
                $join->where('mpd.secondary_position', 0);
                $join->on(function($query){
                    $query->on('mpd.id_employee', '=', 'spfaev.id_employee');
                    $query->orOn('mpd.id_employee2', '=', 'spfaev.id_employee');
                });
            })
            ->leftJoin('master_position_routing as mpr', function($join){
                $join->on(function($query){
                    $query->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                });
            })
            ->join('master_location as ml', 'ml.id_location', '=', 'mpd.id_location', 'left')
            ->select('spfaev.*','mds.start_time', 'mds.end_time', 'mds.shift_code', 'ml.description as location', 'ml.address_location', 'ml.latitude as lat_loc', 'ml.longitude as lng_loc')
            ->whereRaw('spfaev.current_dates <= CURRENT_DATE');
        $result = $data->get();
        
        $result->mapWithKeys(function ($val){
            //Utk menghilangkan tanda petik satu/ganda, agar di view tidak terjadi error
            $val->current_name_out = htmlspecialchars($val->current_name_out, ENT_QUOTES, 'UTF-8');
            $val->current_address_out = htmlspecialchars($val->current_name_out, ENT_QUOTES, 'UTF-8');
            return $val;
        });

        if(!$result){
            return null;
        } else {
            return $result;
        }
    }
    
    public static function checkAttendanceByEmployee($id_employee) {
        $workdays = DB::table('hr_work_days as hwd')
                    ->select('hwd.id_employee', 'hwd.actual_time_in', 'hwd.actual_time_out')
                    ->where('hwd.id_employee', $id_employee)->get();
        $exist = [];
        if($workdays){
            foreach ($workdays as $key => $val) {
                if(!is_null($val->actual_time_in) || !is_null($val->actual_time_in)){
                    $exist[] = $val->id_employee;
                }
            }
        }
        return count($exist);
    }

    public static function check_allow_checkout_nextdays($id_employee) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select sh.allow_checkout_nextdays as allow_checkout_nextdays
                    from hr_employee he
                    join master_shiftgroup_header sh on sh.id_shiftgroup=he.id_shift_group
                    where he.id_employee='".$id_employee."'
                ";
                $result_getdata = collect(DB::select($sql_getdata))->first();
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
    
    public static function check_empty_actual_timeout_before($id_employee) {
        try {
            if(DB::getDatabaseName()){
                $sql_getdata="
                    select current_dates,actual_time_in,actual_time_out,id_workdays,id_request
                    from hr_work_days a
                    where id_employee='".$id_employee."'
                    and day_type='WD'
                    and current_dates<=cast(now() as date)
                    order by current_dates desc
                    limit 2
                ";
                $result_getdata = collect(DB::select($sql_getdata));
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

    public static function getTokenMapbox() {
        $tanggalNow     = intval(date('d'));
        $mapboxToken    = null;
        for ($i=1; $i < 32; $i++) { 
            $name = 'mapbox_token_'.$i;

            if($tanggalNow == $i){
                $mapboxToken = Curl::findApi($name)->token;
            }
        }
        return $mapboxToken;
    }

}
