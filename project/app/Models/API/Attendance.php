<?php

namespace App\Models\API;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Employee\EmployeeSetting\WorkDays;
use Carbon\Carbon;

class Attendance extends Model {
	
	public static function getAttendance($employee) {
        $idEmployee    = $employee->id_employee;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d');
        $add1month     = date('Y-m-d', strtotime($today."+1 month"));

        $workdayNow = DB::table('hr_work_days as hwd')
                    ->where('hwd.current_dates', 'like', $today)->where('hwd.id_employee', '=', $idEmployee)
                    ->first();
        if(!$workdayNow){
            $start          = date('Y-m-d', strtotime($today."-5 days"));
            $end            = $add1month;
            $patchWorkdays  = WorkDays::patchWorkdays([$idCompany], $start, $end, $idEmployee, true);
        }

        $workdayAfter1Month = DB::table('hr_work_days as hwd')
                    ->where('hwd.current_dates', 'like', $add1month)->where('hwd.id_employee', '=', $idEmployee)
                    ->first();
        if(!$workdayAfter1Month){
            $start_         = $add1month;
            $end_           = $add1month;
            $patchWorkdays  = WorkDays::patchWorkdays([$idCompany], $start_, $end_, $idEmployee, true);
        }

        $data = DB::table(DB::raw("sp_funct_apigetlist_employee_view(".$idEmployee.",".$idCompany.") spfaev"));
        $result = $data->get();
        
        $result->mapWithKeys(function ($val){
            //Utk menghilangkan tanda petik satu/ganda, agar di view tidak terjadi error
            $val->current_name_in = $val->current_name_in ? htmlspecialchars($val->current_name_in, ENT_QUOTES, 'UTF-8') : null;
            $val->current_address_in = $val->current_address_in ? htmlspecialchars($val->current_address_in, ENT_QUOTES, 'UTF-8') : null;
            $val->current_name_out = $val->current_name_out ? htmlspecialchars($val->current_name_out, ENT_QUOTES, 'UTF-8') : null;
            $val->current_address_out = $val->current_address_out ? htmlspecialchars($val->current_address_out, ENT_QUOTES, 'UTF-8') : null;

            if(!is_null($val->image_attachment_in)){
                $val->image_attachment_in = url('project/storage/app/public/images').'/'.$val->image_attachment_in;
            }
            if(!is_null($val->image_attachment_out)){
                $val->image_attachment_out = url('project/storage/app/public/images').'/'.$val->image_attachment_out;
            }

            unset($val->id_employee);
            unset($val->name);
            unset($val->position_detail);
            unset($val->position_routing);
            unset($val->id_shift);
            unset($val->shift_code);
            // unset($val->day_type);
            unset($val->day_seq);
            unset($val->office_hour);
            unset($val->late_in);
            unset($val->early_out);
            unset($val->work_hours);
            unset($val->overtime);
            unset($val->id_request);
            unset($val->id_request_type);
            unset($val->id_holiday);
            unset($val->note);
            unset($val->min_time_in);
            unset($val->max_time_in);
            unset($val->min_time_out);
            unset($val->max_time_out);
            unset($val->is_mobile_attendance);
            unset($val->id_company);
            unset($val->creation_date);
            unset($val->update_date);
            unset($val->created_by);
            unset($val->updated_by);

            return $val;
        });

        return $result;
    }

    public static function getAttendanceStatus($employee) {
        $currentMonth = Carbon::now();
        $startDateCurrent = $currentMonth->startOfMonth()->toDateString();
        $endDateCurrent = Carbon::now()->subDays(1)->toDateString(); //dikurangi sehari
        if(date('d') == '01'){
            $startDateCurrent = Carbon::now()->subDays(1)->toDateString();
        }

        if(date('d') == '31'){
            $startDateLast = date('Y-m-d',strtotime('first day of last month'));
            $endDateLast = date('Y-m-d',strtotime('last day of last month'));
        } else {
            $lastMonth = Carbon::now()->subMonth();
            $startDateLast = $lastMonth->startOfMonth()->toDateString();
            $endDateLast = $lastMonth->endOfMonth()->toDateString();
        }
       
        if($employee->join_date >= $startDateLast){
            $startDateLast = $employee->join_date;
        }
        
        if($employee->join_date >= $startDateCurrent){
            $startDateCurrent = $employee->join_date;
        }

        $getAttendanceStatusLastMonth = WorkDays::getAttendanceStatus(@$employee->id_employee, $startDateLast, $endDateLast);
        $getAttendanceStatusCurrentMonth = WorkDays::getAttendanceStatus(@$employee->id_employee, $startDateCurrent, $endDateCurrent);

        $result['last_month'] = $getAttendanceStatusLastMonth;
        $result['current_month'] = $getAttendanceStatusCurrentMonth;
        return $result; 
    }

}
