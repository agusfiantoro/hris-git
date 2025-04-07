<?php

namespace App\Models\Employee\EmployeeSetting;

use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;
use Request;

class WorkDays extends Model
{
    
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
    }
    
    public static function getdepartment($default_company=null) {
        $data = DB::table('master_department as md')
                ->select('md.id_dept', 'md.department_code', 'md.description')
                ->where('md.id_company', '=', session('id_company'));
        if($default_company){
            $data->orWhere('md.id_company', '=', $default_company);
        }
            $data->orderBy('md.description');
        return $data->get();
    }
    
    public static function getregional($id_regional=[]) {
        $data = DB::table('master_region as mr')
                ->select('mr.id_region', 'mr.region_code', 'mr.description')
                ->where('mr.id_company', '=', session('id_company'))
                ->orderBy('description');
        if(count($id_regional) > 0){
            $whereCompany = session('company_type')=='os' ? [1,session('id_company')] : [session('id_company')];
            $id_regional = self::getRegionByLikeCode($whereCompany, $id_regional);
            $data->whereIn('mr.id_region', $id_regional);
        }
        return $data->get();
    }

    public static function getbranch($id_regional=null, $id_branch=[]) {
        $data = DB::table('master_branch as mb')
                ->select('mb.id_branch', 'mb.branch_code', 'mb.description')
                ->where('mb.id_company', '=', session('id_company'))
                ->orderBy('description');

        if(is_array($id_regional) && count($id_regional) > 0){
            $data->whereIn('mb.id_region', $id_regional);
        } else if($id_regional && !is_array($id_regional)){
            $data->where('mb.id_region', '=', $id_regional);
        }

        if(count($id_branch) > 0){
            $data->whereIn('mb.id_branch', $id_branch);
        }
        return $data->get();
    }

    public static function getlocation($id_branch=null) {
        $data = DB::table('master_location as ml')
                ->select('ml.id_location', 'ml.location_code', 'ml.description')
                ->where('ml.id_company', '=', session('id_company'))
                ->orderBy('description');

        if(is_array($id_branch) && count($id_branch) > 0){
            $data->whereIn('ml.id_branch', $id_branch);
        } else if($id_branch && !is_array($id_branch)){
            $data->where('ml.id_branch', '=', $id_branch);
        }

        return $data->get();
    }

    public static function getemployeename($id_employee=[], $status=['A']) {
        $data = DB::table('hr_employee as he')
                ->join('master_position_detail as mpd', function ($join) {
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                })
                ->join('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                ->join('master_job_position as mjp', 'mjp.id_position', '=', 'mpr.id_position')
                ->join('master_department as md', 'md.id_dept', '=', 'mjp.id_dept')
                ->select('he.id_employee', 'he.name', 'he.nik_employee', 'md.id_dept', 'he.id_user')
                ->whereIn('he.status', $status)
                ->where(function ($query){
                    $query->where('he.id_company', '=', session('id_company'));
                    $query->orWhere('mpd.assigned_to_company', '=', session('id_company'));
                })
                ->orderBy('he.name');
        if(!is_array($id_employee)){
            $data->whereIn('he.id_employee', [$id_employee]);
        } else {
            if(count($id_employee) > 0){
                $data->whereIn('he.id_employee', $id_employee);
            }
        }
        return $data->get();
    }

    public static function getEmployeeAll($id_employee=[], $status=['A']) {
        $sub = DB::table('hr_employee as he')
                ->select('he.nik_employee')
                ->whereIn('he.status', $status);
        if(!is_array($id_employee)){
            $sub->whereIn('he.id_employee', [$id_employee]);
        } else {
            if(count($id_employee) > 0){
                $sub->whereIn('he.id_employee', $id_employee);
            }
        }
        $nik = $sub->get()->pluck('nik_employee')->unique()->all();

        $data = DB::table('hr_employee as he')
                ->select('he.name', 'he.nik_employee', 'he.id_employee', 'he.status')
                ->whereIn('he.status', $status)
                ->groupBy('he.name', 'he.nik_employee', 'he.id_employee')
                ->orderBy('he.name');
        if(count($nik) > 0){
            $data->whereIn('he.nik_employee', $nik);
        }
        $data->where('he.id_company', session('id_company'));
        return $data->get();
    }
    public static function getRegionByLikeCode($id_company, $id_region) {
        $id_company = is_array($id_company) ? $id_company : [$id_company] ;
        $code = DB::table('master_region as mr')
                    ->selectRaw('mr.region_code')
                    ->whereIn('mr.id_company', $id_company)
                    ->whereIn('mr.id_region', $id_region)
                    ->get()->pluck('region_code')->all();
        $reg = DB::table('master_region as mr')
                    ->selectRaw('mr.region_code, mr.id_region')
                    // ->where('mr.id_company', '!=', $id_company)
                    ->whereIn('mr.region_code', $code);
        return $reg->get()->pluck('id_region')->all();
    }

    public static function getBranchBySameCode($id_company, $id_branch) {
        $id_company = is_array($id_company) ? $id_company : [$id_company] ;
        $code = DB::table('master_branch as mb')
                    ->selectRaw('mb.branch_code')
                    ->whereIn('mb.id_company', $id_company)
                    ->whereIn('mb.id_branch', $id_branch)
                    ->get()->pluck('branch_code')->all();
        $branch = DB::table('master_branch as mb')
                    ->selectRaw('mb.branch_code, mb.id_branch')
                    // ->where('mb.id_company', '!=', $id_company)
                    ->whereIn('mb.branch_code', $code);
        return $branch->get()->pluck('id_branch')->all();
    }

    public static function getLocationByLikeDescription($id_company, $id_location) {
        $id_company = is_array($id_company) ? $id_company : [$id_company] ;
        $data = DB::table('master_location as ml')
                    ->selectRaw('ml.description')
                    ->whereIn('ml.id_company', $id_company)
                    ->whereIn('ml.id_location', $id_location)
                    ->get()->pluck('description')->all();
        $loc = DB::table('master_location as ml')
                    ->selectRaw('ml.description, ml.id_location');
                    // ->where('ml.id_company', '!=', $id_company);
        foreach ($data as $key => $val) {
            if($key == 0){
                $loc->where('ml.description', 'LIKE', '%'.$val.'%');
            } else {
                $loc->orWhere('ml.description', 'LIKE', '%'.$val.'%');
            }
        }
        return $loc->get()->pluck('id_location')->all();
    }

    public static function getGrade() {
        $data = DB::table('master_job_grade as ml')
            ->select('ml.description')->distinct()
            ->where('ml.status', 'A')
            ->orderBy('description')
            ->get();
        
        return $data;
    }

    public static function getPrinciple() {
        $data = DB::table('master_principal as mp')
            ->select('mp.principal_code', 'mp.description')
            ->where('mp.status', 'A')
            ->where('mp.principal_code', '!=', 'PIC')
            ->groupBy('mp.principal_code', 'mp.description')
            ->orderBy('description')
            ->get();
        return $data;
    }

    public static function getMasterApprovalCareerStatus($code=null, $idCompany=null) {
        $get = DB::table('public.master_general_data as mgd')
                ->where('status', 'A')
                ->where('id_general_type', 7);
        if($code){
            $get->where('code', $code);
        }
        if($idCompany){
            $get->where('id_company', $idCompany);
        }
        $return = $get->get();
        return $return;
    }

    public static function getMasterApprovalLeaveStatus($code=null, $idCompany=null) {
        $get = DB::table('public.master_general_data as mgd')
                ->where('status', 'A')
                ->where('id_general_type', 10);
        if($code){
            $get->where('code', $code);
        }
        if($idCompany){
            $get->where('id_company', $idCompany);
        }
        $return = $get->get();
        return $return;
    }

    public static function getMasterLeaveStatus($code=null, $idCompany=null) {
        $get = DB::table('public.master_leave_type as mlt')
                ->where('status', 'A');
        if($code){
            $get->where('leave_code', $code);
        }
        if($idCompany){
            $get->where('id_company', $idCompany);
        }
        $return = $get->get();
        return $return;
    }

    public function listData($id_employee, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu) {
        try {
            $getInactive = collect([]);
            $getActive = collect([]);
            $branchForGetEmployee = [];

            if($id_employee =='null'){
                $id_employee = null;
            }
            if($startdate == 'null'){
                $startdate = date('Y-m-d');
            }
            if($enddate == 'null'){
                $enddate = date('Y-m-d');
            }
            if(!is_array($branch)){
                $branch = null;
            }
            if(!is_array($location)){
                $location = null;
            }
            if(!is_array($regional)){
                $regional = null;
            }
            if($department == 'null'){
                $department = null;
            }
            if(!is_array($status)){
                $status = ['A', 'I'];
            }

            if(in_array('A', $status)){
                $sub = DB::table('hr_work_days as hwd')
                    ->join('hr_employee as he', function ($join) {
                        $join->on('he.id_employee', '=', 'hwd.id_employee');
                        $join->on('hwd.id_company', '=', 'he.id_company');
                        $join->on('hwd.current_dates', '>=','he.join_date');
                    })
                    ->leftJoin('master_users as mu', 'mu.id_user', '=', 'he.id_user')
                    ->join('master_shiftgroup_header as msh', function ($join) {
                        $join->on('he.id_shift_group', '=', 'msh.id_shiftgroup');
                        $join->on('he.id_company', '=', 'msh.id_company');
                    })
                    ->join('master_shiftgroup_detail as msd', function ($join) {
                        $join->on('msh.id_shiftgroup', '=', 'msd.id_shiftgroup');
                        $join->on('msh.id_company', '=', 'msd.id_company');
                        $join->on(DB::raw('(select(extract(ISODOW from(hwd.current_dates::date))))'), '=', 'msd.sequence');
                    })
                    ->leftJoin('master_daily_shift as mds', function ($join) {
                        $join->on('msd.id_shift', '=', 'mds.id_shift');
                        $join->on('msd.id_company', '=', 'mds.id_company');
                    })
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('hr_request_detail as hrd', function ($join) {
                        $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                        $join->where(function ($where){
                            $where->whereNotNull('hrd.request_start_to');
                            $where->whereNotNull('hrd.request_end_to');
                        });
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('hwd.id_request_type', '=', 'mgd.id_general_data');
                        $join->on('hwd.id_company', '=', 'mgd.id_company');
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
                    ->leftJoin('master_holiday as mh', function ($join) {
                        $join->on('hwd.id_holiday', '=', 'mh.id_holiday');
                        $join->on('hwd.id_company', '=', 'mh.id_company');
                    })
                    ->join('master_position_detail as mpd', function ($join) {
                        $join->where('mpd.secondary_position', 0);
                        $join->on('he.id_employee', '=', 'mpd.id_employee');
                        $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                        $join->whereRaw('(he.id_company = mpd.id_company OR he.id_company = mpd.assigned_to_company)');
                    })
                    ->join('master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                        $join->whereRaw("mpr.status = 'A'");
                    })
                    ->join('master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpd.id_company', '=', 'mjp.id_company');
                        $join->whereRaw("mjp.status = 'A'");
                    })
                    ->join('master_location as ml', function ($join) {
                        $join->on('mpd.id_location', '=', 'ml.id_location');
                        $join->on('mpd.id_company', '=', 'ml.id_company');
                        $join->whereRaw("ml.status = 'A'");
                    })
                    ->join('master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                        $join->whereRaw("mb.status = 'A'");
                    })
                    ->join('master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mpd.id_company', '=', 'md.id_company');
                        $join->whereRaw("md.status = 'A'");
                    })
                    ->join('master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                        $join->whereRaw("mr.status = 'A'");
                    })
                    ->selectRaw("hwd.id_employee, hwd.is_mobile_attendance, he.name as employee_name, mu.user_name, hrh.reference_number as request_number, mgd.description as request_type, mh.holiday_name, msh.description as shift_group, hwd.id_workdays, hwd.id_shift, hwd.day_type, hwd.day_seq, concat(mds.description,'[', to_char(mds.start_time,'HH24:MI'),'-', to_char(mds.end_time,'HH24:MI'), ']') as shift, hwd.current_dates, hwd.schedule_employee_timezone, hwd.schedule_time_in, hwd.actual_time_in, hwd.late_in, hwd.schedule_time_out, hwd.actual_time_out, hwd.early_out, CASE hwd.current_employee_timezone WHEN 'Asia/Jakarta' THEN 'WIB' WHEN 'Asia/Makassar' THEN 'WITA' WHEN 'Asia/Jayapura' THEN 'WIT' ELSE 'WIB' END as current_employee_timezone, hwd.work_hours, hwd.overtime, hwd.current_name_in, hwd.current_address_in, hwd.current_latitude_in, hwd.current_longitude_in, hwd.current_name_out, hwd.current_address_out, hwd.current_latitude_out, hwd.current_longitude_out, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.note, md.id_dept, md.description as department, mr.id_region, mr.description as region, mb.id_branch, mb.description as branch, ml.id_location, ml.description as location, he.status as employee_status, he.expired_date, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction') THEN 'PRS' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSI' WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSO' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hrd.day_type = 'Half_Day1' OR hrd.day_type = 'Half_Day2') AND (hwd.actual_time_in is null and hwd.actual_time_out is null) AND (hwd.day_type='WD') AND ((EXTRACT(EPOCH FROM mds.productive_work_time)::int / 3600)>5)) THEN 'ABS' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NULL OR hwd.actual_time_in IS NOT NULL) and (hwd.actual_time_out IS NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_holiday IS NOT NULL and (hwd.day_type = 'WD' OR hwd.day_type = 'OD') and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date))) THEN 'OFF' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.current_dates > he.resign_date) THEN '-' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_request IS NOT NULL)) THEN coalesce(mlt.leave_code,(case when mgd.code = 'Change_Day_off' then 'CDO' end) )::text END AS attendance_status");

                $sub->whereDate('hwd.current_dates', '>=', $startdate);
                $sub->whereDate('hwd.current_dates', '<=', $enddate);

                if($path_menu == 'employee/employee_setting/workdays'){
                    $sub->where('hwd.id_company', $id_company);
                }
                if($id_employee && is_array($id_employee)){
                    $sub->whereIn('hwd.id_employee', $id_employee);
                }
                if($location && is_array($location)){
                    if(session('company_type') == 'os'){
                        $location = self::getLocationByLikeDescription([1,$id_company], $location);
                    }
                    $sub->whereIn('ml.id_location', $location);
                }
                if(!$location && $branch && is_array($branch)){
                    if(session('company_type') == 'os'){
                        $branch = self::getBranchBySameCode([1,$id_company], $branch);
                    }
                    $sub->whereIn('mb.id_branch', $branch);
                }
                if((!$location && !$branch && $regional && is_array($regional)) || (!$location && $branch && $regional && is_array($regional)) ){
                    if(session('company_type') == 'os'){
                        $regional = self::getRegionByLikeCode([1,$id_company], $regional);
                    }
                    $sub->whereIn('mr.id_region', $regional);
                }
                if($department){
                    $sub->where('md.id_dept', $department);
                }
                $get1 = DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub)
                    ->leftJoin('hr_employee as he2', 'sub.id_employee', '=', 'he2.id_employee')
                    ->select('sub.*')
                    ->where('he2.status', 'A');
                $getActive = $get1->get();
            }

            if(in_array('I', $status)){
                $get2 = DB::table('hr_work_days as hwd')
                ->join(DB::raw("(select id_user, id_shift_group, id_employee, id_company, name, status, expired_date, resign_date from hr_employee where status = 'I') as he"),function($join){
                        $join->on('he.id_employee','=','hwd.id_employee');
                    })
                    ->leftJoin('master_users as mu', 'mu.id_user', '=', 'he.id_user')
                    ->join('master_shiftgroup_header as msh', function ($join) {
                        $join->on('he.id_shift_group', '=', 'msh.id_shiftgroup');
                        $join->on('he.id_company', '=', 'msh.id_company');
                    })
                    ->join('master_shiftgroup_detail as msd', function ($join) {
                        $join->on('msh.id_shiftgroup', '=', 'msd.id_shiftgroup');
                        $join->on('msh.id_company', '=', 'msd.id_company');
                        $join->on(DB::raw('(select(extract(ISODOW from(hwd.current_dates::date))))'), '=', 'msd.sequence');
                    })
                    ->leftJoin('master_daily_shift as mds', function ($join) {
                        $join->on('msd.id_shift', '=', 'mds.id_shift');
                        $join->on('msd.id_company', '=', 'mds.id_company');
                    })
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('hr_request_detail as hrd', function ($join) {
                        $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                        $join->where(function ($where){
                            $where->whereNotNull('hrd.request_start_to');
                            $where->whereNotNull('hrd.request_end_to');
                        });
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('hwd.id_request_type', '=', 'mgd.id_general_data');
                        $join->on('hwd.id_company', '=', 'mgd.id_company');
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
                    ->leftJoin('master_holiday as mh', function ($join) {
                        $join->on('hwd.id_holiday', '=', 'mh.id_holiday');
                        $join->on('hwd.id_company', '=', 'mh.id_company');
                    })
                    ->join('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
                    ->join('master_position_detail as mpd', function ($join) {
                        $join->where('mpd.secondary_position', 0);
                        $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
                        $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
                    })
                    ->join('master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                    })
                    ->join('master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpr.id_company', '=', 'mjp.id_company');
                    })
                    ->join('master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mjp.id_company', '=', 'md.id_company');
                    })
                    ->join('master_location as ml', function ($join) {
                        $join->on('mpd.id_location', '=', 'ml.id_location');
                        $join->on('mpd.id_company', '=', 'ml.id_company');
                        $join->whereRaw("ml.status = 'A'");
                    })
                    ->join('master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                    })
                   ->join('master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                    })
                    ->selectRaw("hwd.id_employee, he.name as employee_name, mu.user_name, hrh.reference_number as request_number, mgd.description as request_type, mh.holiday_name, msh.description as shift_group, hwd.id_workdays, hwd.id_shift, hwd.day_type, hwd.day_seq, concat(mds.description,'[', to_char(mds.start_time,'HH24:MI'),'-', to_char(mds.end_time,'HH24:MI'), ']') as shift, hwd.current_dates, hwd.schedule_employee_timezone, hwd.schedule_time_in, hwd.actual_time_in, hwd.late_in, hwd.schedule_time_out, hwd.actual_time_out, hwd.early_out, CASE hwd.current_employee_timezone WHEN 'Asia/Jakarta' THEN 'WIB' WHEN 'Asia/Makassar' THEN 'WITA' WHEN 'Asia/Jayapura' THEN 'WIT' ELSE 'WIB' END as current_employee_timezone, hwd.work_hours, hwd.overtime, hwd.current_name_in, hwd.current_address_in, hwd.current_latitude_in, hwd.current_longitude_in, hwd.current_name_out, hwd.current_address_out, hwd.current_latitude_out, hwd.current_longitude_out, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.note, md.id_dept as id_dept, md.description as department, mr.id_region as id_region, mr.description as region, mb.id_branch as id_branch, mb.description as branch, ml.id_location as id_location, ml.description as location, he.status as employee_status, he.expired_date, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.current_dates <= he.resign_date) OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.current_dates <= he.resign_date) THEN 'PRS' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and hwd.current_dates <= he.resign_date and hwd.day_type = 'WD') THEN 'NSI' WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and hwd.current_dates <= he.resign_date and hwd.day_type = 'WD') THEN 'NSO' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= he.resign_date) OR ((hrd.day_type = 'Half_Day1' OR hrd.day_type = 'Half_Day2') AND (hwd.actual_time_in is null and hwd.actual_time_out is null) AND (hwd.day_type='WD') AND ((EXTRACT(EPOCH FROM mds.productive_work_time)::int / 3600)>5)) THEN 'ABS' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= he.resign_date) OR ((hwd.actual_time_in IS NULL OR hwd.actual_time_in IS NOT NULL) and (hwd.actual_time_out IS NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_holiday IS NOT NULL and (hwd.day_type = 'WD' OR hwd.day_type = 'OD') and hwd.current_dates <= he.resign_date)) THEN 'OFF' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.current_dates > he.resign_date) THEN '-' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL and hwd.current_dates <= he.resign_date) THEN mlt.leave_code::text END AS attendance_status");

                if($id_employee){
                    $get2->whereIn('hwd.id_employee', $id_employee);
                }
                if(in_array('I', $status)){
                    $get2->where('he.status', 'I');
                }
                if($path_menu == 'employee/employee_setting/workdays'){
                    $get2->where('hwd.id_company', $id_company);
                }
                $get2->whereDate('hwd.current_dates', '>=', $startdate);
                $get2->whereDate('hwd.current_dates', '<=', $enddate);
                $get2->whereRaw('(hwd.current_dates <= he.resign_date OR he.resign_date IS NULL)');

                if($location && is_array($location)){
                    if(session('company_type') == 'os'){
                        $location = self::getLocationByLikeDescription([1,$id_company], $location);
                    }
                    $get2->whereIn('ml.id_location', $location);
                }
                if(!$location && $branch && is_array($branch)){
                    if(session('company_type') == 'os'){
                        $branch = self::getBranchBySameCode([1,$id_company], $branch);
                    }
                    $get2->whereIn('mb.id_branch', $branch);
                }
                if(!$location && !$branch && $regional && is_array($regional)){
                    if(session('company_type') == 'os'){
                        $regional = self::getRegionByLikeCode([1,$id_company], $regional);
                    }
                    $get2->whereIn('mr.id_region', $regional);
                }
                if($department){
                    $get2->where('md.id_dept', $department);
                }
                
                $get2->groupBy(DB::raw('hwd.id_employee, he.name,mu.user_name,hrh.reference_number,hrd.day_type,mgd.description,mh.holiday_name,msh.description,hwd.id_workdays,hwd.id_shift,hwd.day_type,hwd.day_seq,mds.description,mds.start_time,mds.end_time,hwd.current_dates,hwd.schedule_employee_timezone,hwd.schedule_time_in,hwd.actual_time_in,hwd.late_in,hwd.schedule_time_out,hwd.actual_time_out,hwd.early_out,hwd.work_hours,hwd.overtime,hwd.current_name_in,hwd.current_address_in,hwd.current_latitude_in,hwd.current_longitude_in,hwd.current_name_out,hwd.current_address_out,hwd.current_latitude_out,hwd.current_longitude_out,hwd.id_request,hwd.id_request_type,hwd.id_holiday,hwd.note,md.id_dept,md.description,mr.id_region,mr.description,mb.id_branch,mb.description,ml.id_location,ml.description,he.status,he.expired_date,he.resign_date,mlt.leave_code,mds.productive_work_time'));

                $getInactive = $get2->get();
            }
            $merged = $getActive->merge($getInactive);
            return $merged;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function summary($nik, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu) {
        try {
            $getInactive = collect([]);
            $getActive = collect([]);
            $branchForGetEmployee = [];

            if($nik =='null'){
                $nik = null;
            }
            if($startdate == 'null'){
                $startdate = date('Y-m-d');
            }
            if($enddate == 'null'){
                $enddate = date('Y-m-d');
            }

            if($branch != 'null'){
                if(is_array($branch)){
                    $branch = $branch;
                } else {
                    if(strpos($branch, ',')!==false){
                        $branch = explode(',', $branch);
                    } else {
                        $branch = [$branch];
                    }
                }
            } else {
                $branch = null;
            }

            if($location != 'null'){
                if(strpos($location, ',')!==false){
                    $location = explode(',', $location);
                } else {
                    $location = [$location];
                }
            } else {
                $location = null;
            }

            if($regional != 'null'){
                if(strpos($regional, ',')!==false){
                    $regional = explode(',', $regional);
                } else {
                    $regional = [$regional];
                }
            } else {
                $regional = null;
            }

            if($department != 'null'){
                if(strpos($department, ',')!==false){
                    $department = explode(',', $department);
                } else {
                    $department = [$department];
                }
                $dept = DB::table('master_department')->whereIn('id_dept', $department)->get();
                if($dept->count() > 0){
                    $department = [];
                    foreach ($dept as $k => $val) {
                        $department[] = trim($val->department_code);
                    }
                }
            } else {
                $department = null;
            }

            if(!is_array($status)){
                $status = ['A', 'I'];
            }

            if(in_array('A', $status)){
                $whereCompany = "";
                if($path_menu == 'employee/employee_setting/workdays' || !($id_company=='null'||$id_company==null)){
                    $whereCompany = "AND hwd.id_company IN(".implode(',',$id_company).")";
                }
                $tableActive = DB::raw("(SELECT hwd.id_employee, hwd.current_dates, hwd.id_company, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.actual_time_in, hwd.actual_time_out, hwd.day_type, hwd.day_seq FROM hr_work_days hwd WHERE hwd.current_dates >= '".$startdate."' AND hwd.current_dates <= '".$enddate."' ".$whereCompany.") hwd ");

                $sub = DB::table($tableActive)
                    ->join('hr_employee as he', function ($join) {
                        $join->on('hwd.id_employee', '=', 'he.id_employee');
                        $join->on('hwd.id_company', '=', 'he.id_company');
                        $join->whereRaw('hwd.current_dates >= he.join_date');
                    })
                    ->join('master_position_detail as mpd', function ($join) {
                        $join->where('mpd.secondary_position', 0);
                        $join->on('he.id_employee', '=', 'mpd.id_employee');
                        $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                        $join->whereRaw('(he.id_company = mpd.id_company OR he.id_company = mpd.assigned_to_company)');
                    })
                    ->leftJoin('master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                    })
                    ->join('master_job_grade as mjg', function ($join) {
                        $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                        $join->on('mpr.id_company', '=', 'mjg.id_company');
                    })
                    ->leftJoin('master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpr.id_company', '=', 'mjp.id_company');
                    })
                    ->leftJoin('master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mjp.id_company', '=', 'md.id_company');
                    })
                    ->join('master_location as ml', function ($join) {
                        $join->on('mpd.id_location', '=', 'ml.id_location');
                        $join->on('mpd.id_company', '=', 'ml.id_company');
                        $join->whereRaw("ml.status = 'A'");
                    })
                    ->leftJoin('master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                        $join->where('mb.status', '=', 'A');
                    })
                    ->leftJoin('master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                    })
                    ->join('master_company as mc', 'he.id_company', '=', 'mc.id_company')
                    ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                        $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                        $join->on('rpp.id_company', '=', 'mpd.id_company');
                    })
                    ->leftJoin('master_principal as mp', function ($join) {
                        $join->on('mp.id_principal', '=', 'rpp.id_principal');
                    })
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_request_type', '=', 'hrh.id_request_type');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('hr_request_detail as hrd', function ($join) {
                        $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                        $join->where(function ($where){
                            $where->whereNotNull('hrd.request_start_to');
                            $where->whereNotNull('hrd.request_end_to');
                        });
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('mgd.id_general_data', '=', 'hrh.id_request_type');
                        $join->on('mgd.id_company', '=', 'hrh.id_company');
                        $join->whereIn('mgd.code', ['Leave_Request','Change_Day_off','Attendance_Correction']);
                    })
					->leftJoin('master_general_data as mgd2', function ($join) {
                        $join->on('mgd2.id_general_data', '=', 'he.id_employment_status');
                        $join->on('mgd2.id_company', '=', 'he.id_company');
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
                    ->selectRaw("he.nik_employee, he.name, he.status as employee_status, mpr.description as position_name, mb.description as organization_unit, he.id_employee, hwd.current_dates, md.description as department, mr.description as region, mp.description as principal, mc.company_name, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction') THEN 'PRS' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSI' WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSO' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hrd.day_type = 'Half_Day1' OR hrd.day_type = 'Half_Day2') AND (hwd.actual_time_in is null and hwd.actual_time_out is null) AND (hwd.day_type='WD') AND (hwd.day_seq != 6)) THEN 'ABS' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NULL OR hwd.actual_time_in IS NOT NULL) and (hwd.actual_time_out IS NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_holiday IS NOT NULL and (hwd.day_type = 'OD' OR hwd.day_type = 'WD') and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date))) THEN 'OFF' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.current_dates > he.resign_date) THEN '-' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_request IS NOT NULL)) THEN coalesce(mlt.leave_code,(case when mgd.code = 'Change_Day_off' then 'CDO' end) )::text END AS attendance_status, he.resign_date, mjg.description as job_grade");

                
                if($location && is_array($location)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $location = self::getLocationByLikeDescription($id_company, $location);
                    // }
                    $sub->whereIn('ml.id_location', $location);
                }
                if(!$location && $branch && is_array($branch)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $branch = self::getBranchBySameCode($id_company, $branch);
                    // }
                    $sub->whereIn('mb.id_branch', $branch);
                }
                if(!$location && !$branch && $regional && is_array($regional)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $regional = self::getRegionByLikeCode($id_company, $regional);
                    // }
                    $sub->whereIn('mr.id_region', $regional);
                }
				
                if($department){
                    // $sub->where('md.id_dept', $department);
                    $sub->whereIn('md.department_code', $department);
                }

                if($path_menu == 'employee/employee_setting/workdays' || !($id_company=='null'||$id_company==null)){
                    $sub->whereIn('he.id_company', $id_company);
                }

                $sub->where("he.status", 'A');
                $sub->whereRaw("mgd2.code != 'Concurent'");
                $sub->orderByRaw("hwd.id_employee, hwd.current_dates asc");
                $get1 = DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub)
                    ->select('sub.*');
                if($nik){
                    $get1->whereIn('sub.nik_employee', $nik);
                }
                $getActive = $get1->get();
            }
			
            if(in_array('I', $status)){
                $whereCompany = "";
                if($path_menu == 'employee/employee_setting/workdays'){
                    $whereCompany = "AND hwd.id_company IN(".session('id_company').")";
                }
				else if(!($id_company=='null'||$id_company==null)){
					$whereCompany = "AND hwd.id_company IN(".implode(',',$id_company).")";
				}
                $tableInactive = DB::raw("(SELECT hwd.id_employee, hwd.current_dates, hwd.id_company, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.actual_time_in, hwd.actual_time_out, hwd.day_type, hwd.day_seq FROM hr_work_days hwd WHERE hwd.current_dates >= '".$startdate."' AND hwd.current_dates <= '".$enddate."' ".$whereCompany.") hwd ");

                $get2 = DB::table($tableInactive)
                    ->join('hr_employee as he', function ($join) {
                        $join->on('hwd.id_employee', '=', 'he.id_employee');
                        $join->on('hwd.id_company', '=', 'he.id_company');
                        $join->whereRaw('hwd.current_dates >= he.join_date');
                    })
                //    ->join('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
					->join(\DB::raw("(SELECT hct_new.id_employee, max(hct_new.id_career_transaction) AS id_career_transaction 
							FROM hr_career_transaction AS hct_new
							JOIN master_general_data mgd
							ON hct_new.id_approval_status = mgd.id_general_data
							JOIN master_general_data mgd3
							ON hct_new.id_transaction_type = mgd3.id_general_data AND mgd3.description NOT IN ('Temporary Assignment','Concurent')
							WHERE mgd.code = 'Approved'
							GROUP BY id_employee) AS hct2"),
						'hct2.id_employee', '=', 'he.id_employee')
					->join('hr_career_transaction as hct', 'hct2.id_career_transaction', '=', 'hct.id_career_transaction')
                    ->join('master_position_detail as mpd', function ($join) {
                        $join->where('mpd.secondary_position', 0);
                        $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
                        $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
                    })
                    ->join('master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                    })
                    ->join('master_job_grade as mjg', function ($join) {
                        $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                        $join->on('mpr.id_company', '=', 'mjg.id_company');
                    })
                    ->join('master_job_status as mjs', function ($join) {
                        $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
                        $join->on('mpr.id_company', '=', 'mjs.id_company');
                    })
                    ->join('master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpr.id_company', '=', 'mjp.id_company');
                    })
                    ->join('master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mjp.id_company', '=', 'md.id_company');
                    })
                    ->join('master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                    })
                    ->join('master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                    })
                    ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                        $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                        $join->on('rpp.id_company', '=', 'mpd.id_company');
                    })
                    ->leftJoin('master_principal as mp', function ($join) {
                        $join->on('mp.id_principal', '=', 'rpp.id_principal');
                    })
                    ->join('master_company as mc', 'he.id_company', '=', 'mc.id_company')
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_request_type', '=', 'hrh.id_request_type');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('hr_request_detail as hrd', function ($join) {
                        $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                        $join->where(function ($where){
                            $where->whereNotNull('hrd.request_start_to');
                            $where->whereNotNull('hrd.request_end_to');
                        });
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('mgd.id_general_data', '=', 'hrh.id_request_type');
                        $join->on('mgd.id_company', '=', 'hrh.id_company');
                        $join->whereIn('mgd.code', ['Leave_Request','Change_Day_off','Attendance_Correction']);
                    })
					->leftJoin('master_general_data as mgd2', function ($join) {
                        $join->on('mgd2.id_general_data', '=', 'he.id_employment_status');
                        $join->on('mgd2.id_company', '=', 'he.id_company');
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
					->leftJoin('master_general_data as mgd3', function ($join) {
                        $join->on('mgd3.id_general_data', '=', 'hct.id_transition_category');
                        $join->on('mgd3.id_company', '=', 'hct.id_company');
						$join->where('mgd3.code', 'Entity_Movement');
                    })
                    ->selectRaw("he.nik_employee, he.name, he.status as employee_status, mpr.description as position_name, mb.description as organization_unit, he.id_employee, hwd.current_dates, md.description as department, mr.description as region, mp.description as principal, mc.company_name, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction') THEN 'PRS' 
					WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSI' 
					WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction')) and hwd.day_type = 'WD') THEN 'NSO'
					WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and (hwd.current_dates > he.resign_date OR (hwd.current_dates > hct.effective_date and mgd3.code = 'Entity_Movement'))) THEN '-' 
					WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hrd.day_type = 'Half_Day1' OR hrd.day_type = 'Half_Day2') AND (hwd.actual_time_in is null and hwd.actual_time_out is null) AND (hwd.day_type='WD') AND (hwd.day_seq != 6)) THEN 'ABS'
					WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NULL OR hwd.actual_time_out IS NOT NULL) and (hwd.actual_time_out IS NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_holiday IS NOT NULL and (hwd.day_type = 'OD' OR hwd.day_type = 'WD') and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and (hwd.id_request IS NULL OR hwd.id_request IS NOT NULL) and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date))) THEN 'OFF' 
					WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_request IS NOT NULL)) THEN coalesce(mlt.leave_code,(case when mgd.code = 'Change_Day_off' then 'CDO' end) )::text END AS attendance_status, he.resign_date, mjg.description as job_grade");

                if($nik){
                    $get2->whereIn('he.nik_employee', $nik);
                }
                if($location && is_array($location)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $location = self::getLocationByLikeDescription($id_company, $location);
                    // }
                    $get2->whereIn('ml.id_location', $location);
                }
                if(!$location && $branch && is_array($branch)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $branch = self::getBranchBySameCode($id_company, $branch);
                    // }
                    $get2->whereIn('mb.id_branch', $branch);
                }
                if(!$location && !$branch && $regional && is_array($regional)){
                    // if(session('company_type') == 'os'){
                        $id_company[] = 1;
                        $regional = self::getRegionByLikeCode($id_company, $regional);
                    // }
                    $get2->whereIn('mr.id_region', $regional);
                }
                if($department){
                    // $get2->where('md.id_dept', $department);
                    $get2->whereIn('md.department_code', $department);
                }
                if($path_menu == 'employee/employee_setting/workdays' || !($id_company=='null'||$id_company==null)){
                    $get2->whereIn('he.id_company', $id_company);
                }
                $get2->where("he.status", 'I');
				$get2->whereRaw("mgd2.code != 'Concurent'");
            //    $get2->whereRaw('(hwd.current_dates <= he.resign_date OR he.resign_date IS NULL)');
				$get2->whereRaw("(hwd.current_dates <= he.resign_date OR (hwd.current_dates < hct.effective_date AND mgd3.code = 'Entity_Movement' AND he.resign_date IS NULL))");
                $get2->orderByRaw("hwd.id_employee, hwd.current_dates asc, hct.id_career_transaction asc");
                $getInactive = $get2->get();
            }
            $merged = $getActive->merge($getInactive)->sortBy('current_dates');
            return $merged;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function summary_old($nik, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu) {
        try {
            $getInactive = collect([]);
            $getActive = collect([]);

            if(is_array($branch)){
                $branch_ = 'array['.implode(',',$branch).']';
            } else {
                $branch_ = 'array['.$branch.']';
            }

            if(!is_array($nik)){
                $nik = null;
            }

            $get1 = DB::table(DB::raw("sp_funct_summary_presence(".$id_company.",'".$startdate."','".$enddate."',".$branch_.") sfsp"))
                ->select('sfsp.*');

            if($nik){
                $get1->whereIn('sfsp.nik_employee', $nik);
            }
            if(in_array('A', $status)){
                $get1->where('sfsp.employee_status', 'A');
            }
            $getActive = $get1->get();

            if(in_array('I', $status)){
                $table_2 = DB::raw("(SELECT hwd.id_employee, hwd.current_dates, hwd.id_company, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.actual_time_in, hwd.actual_time_out,hwd.day_type FROM hr_work_days hwd WHERE hwd.current_dates >= '".$startdate."' AND hwd.current_dates <= '".$enddate."' AND hwd.id_company = '".$id_company."') hwd ");

                $get2 = DB::table($table_2)
                    ->join('hr_employee as he', function ($join) {
                        $join->on('hwd.id_employee', '=', 'he.id_employee');
                        $join->whereRaw('hwd.current_dates >= he.join_date');
                    })
                    ->join('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
                    ->join('master_position_detail as mpd', function ($join) {
                        $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
                        $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
                    })
                    ->join('master_position_routing as mpr', function ($join) {
                        $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                        $join->on('mpd.id_company', '=', 'mpr.id_company');
                    })
                    ->join('master_job_grade as mjg', function ($join) {
                        $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                        $join->on('mpr.id_company', '=', 'mjg.id_company');
                    })
                    ->join('master_job_status as mjs', function ($join) {
                        $join->on('mpr.id_job_status', '=', 'mjs.id_job_status');
                        $join->on('mpr.id_company', '=', 'mjs.id_company');
                    })
                    ->join('master_job_position as mjp', function ($join) {
                        $join->on('mpr.id_position', '=', 'mjp.id_position');
                        $join->on('mpr.id_company', '=', 'mjp.id_company');
                    })
                    ->join('master_department as md', function ($join) {
                        $join->on('mjp.id_dept', '=', 'md.id_dept');
                        $join->on('mjp.id_company', '=', 'md.id_company');
                    })
                    ->join('master_branch as mb', function ($join) {
                        $join->on('mpd.id_branch', '=', 'mb.id_branch');
                        $join->on('mpd.id_company', '=', 'mb.id_company');
                    })
                    ->join('master_region as mr', function ($join) {
                        $join->on('mb.id_region', '=', 'mr.id_region');
                        $join->on('mb.id_company', '=', 'mr.id_company');
                    })
                    ->leftJoin('relation_positiondetail_principal as rpp', function ($join) {
                        $join->on('rpp.id_position_detail', '=', 'mpd.id_position_detail');
                        $join->on('rpp.id_company', '=', 'mpd.id_company');
                    })
                    ->leftJoin('master_principal as mp', function ($join) {
                        $join->on('mp.id_principal', '=', 'rpp.id_principal');
                    })
                    ->join('master_company as mc', 'he.id_company', '=', 'mc.id_company')
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_request_type', '=', 'hrh.id_request_type');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('mgd.id_general_data', '=', 'hrh.id_request_type');
                        $join->on('mgd.id_company', '=', 'hrh.id_company');
                        $join->whereIn('mgd.code', ['Leave_Request','Change_Day_off','Attendance_Correction']);
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
                    ->selectRaw("he.nik_employee, he.name, he.status as employee_status, mpr.description as position_name, mb.description as organization_unit, he.id_employee, hwd.current_dates, md.description as department, mr.description as region, mp.description as principal, mc.company_name, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NULL and hwd.day_type = 'WD') OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction') THEN 'PRS' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction'))) THEN 'NSI' WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and (hwd.id_request IS NULL OR (hwd.id_request IS NOT NULL and hwd.day_type = 'WD' and mgd.code = 'Attendance_Correction'))) THEN 'NSO' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) THEN 'ABS' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_holiday IS NOT NULL and hwd.day_type = 'WD' and hwd.current_dates <= coalesce(he.resign_date,current_date)) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_request IS NULL and hwd.day_type = 'OD' and hwd.current_dates <= coalesce(he.resign_date,current_date))) THEN 'OFF' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.current_dates > he.resign_date) THEN '-' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL) OR ((hwd.actual_time_in IS NOT NULL OR hwd.actual_time_out IS NOT NULL) and hwd.id_request IS NOT NULL)) THEN coalesce(mlt.leave_code,(case when mgd.code = 'Change_Day_off' then 'CDO' end) )::text END AS attendance_status, he.resign_date");

                if($nik){
                    $get2->whereIn('he.nik_employee', $nik);
                }
                $get2->where("he.status", 'I');
                $get2->whereRaw('(hwd.current_dates <= he.resign_date OR he.resign_date IS NULL)');
                $get2->orderByRaw("hwd.id_employee, hwd.current_dates asc");
                $getInactive = $get2->get();
            }

            $merged = $getActive->merge($getInactive);
            return $merged;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function getdata($id_employee, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu) {
        try {
            $getInactive = [];
            $branchForGetEmployee = [];

            if($status == 'null'){
                $status = 'null';
                $status_q2 = 'null';
            } else {
                if(is_array($status) && count($status) > 1){
                    $status_q2 = 'null';
                    $status = 'null';
                } else {
                    $status_q2 = $status[0];
                    $status = "'$status[0]'";
                }
            }
            if($startdate == 'null'){
                $startdate = 'null';
            } else {
                $startdate_q2 = $startdate;
                $startdate = "'$startdate'";
            }
            if($enddate == 'null'){
                $enddate = 'null';
            } else {
                $enddate_q2 = $enddate;
                $enddate = "'$enddate'";
            }
            // $status         = !is_array($status) ? ['A'] : $status;
            // $startdate      = ($startdate == 'null') ? $startdate : "'$startdate'";
            // $enddate        = ($enddate == 'null') ? $enddate : "'$enddate'";

            // if($id_employee == 'null'){
            //     $tableName = 'sp_funct_workdays_view';
            //     $department     = ($department == 'null') ? $department : "'$department'";

            //     $branchToFunction   = 'null';
            //     $locationToFunction = 'null';
            //     $regionToFunction   = 'null';
            // } 
            // else {
                $tableName = 'sp_funct_workdays_view2';

                if(is_array($department) && count($department) > 0){
                    $imp_department = implode(",", $department);
                    $department = 'array['.$imp_department.']';
                } else {
                    if($department != 'null'){
                        $department = 'array['.$department.']';
                    } else {
                        $department = 'null';
                    }
                }

                if(is_array($regional) && count($regional) > 0){
                    if(session('company_type') == 'os'){
                        $regional = self::getRegionByLikeCode([1,$id_company], $regional);
                    }
                    $imp_regionToFunction = implode(",", $regional);
                    $regionToFunction = 'array['.$imp_regionToFunction.']';
                } else {
                    if(is_array($regional) && count($regional) > 0){
                        if(session('company_type') == 'os'){
                            $regional = [$regional];
                            $regionalOlahan = self::getRegionByLikeCode([1,$id_company], $regional);
                            $regional = implode(",", $regionalOlahan);
                        }
                        $regionToFunction = 'array['.$regional.']';
                    } else {
                        $regionToFunction = 'null';
                    }
                }

                if($regionToFunction == 'null'){
                    $branchToFunction = 'null';
                } else {
                    if(is_array($branch) && count($branch) > 0){
                        if(session('company_type') == 'os'){
                            $branch = self::getBranchBySameCode([1,$id_company], $branch);
                        }
                        $imp_branchToFunction = implode(",", $branch);
                        $branchToFunction = 'array['.$imp_branchToFunction.']';
                        $branchForGetEmployee = $branch;
                        //jika branch ada nilai parameternya maka region dinull kan
                        $regionToFunction = 'null';
                    } else {
                        if(is_array($branch) && count($branch) > 0){
                            $branchForGetEmployee = $branch;
                            if(session('company_type') == 'os'){
                                $branch = [$branch];
                                $branchOlahan = self::getBranchBySameCode([1,$id_company], $branch);
                                $branch = implode(",", $branchOlahan);
                                $branchForGetEmployee = $branchOlahan;
                            }
                            $branchToFunction = 'array['.$branch.']';
                            //jika branch ada nilai parameternya maka region dinull kan
                            $regionToFunction = 'null';
                        } else {
                            $branchToFunction = 'null';
                        }
                    }
                }

                if(is_array($location) && count($location) > 0){
                    if(session('company_type') == 'os'){
                        $location = self::getLocationByLikeDescription([1,$id_company], $location);
                    }
                    $imp_locationToFunction = implode(",", $location);
                    $locationToFunction = 'array['.$imp_locationToFunction.']';
                    //jika location ada nilai parameternya maka branch dan region dinull kan
                    $branchToFunction = 'null';
                    $regionToFunction = 'null';
                } else {
                    if(is_array($location) && count($location) > 0){
                        if(session('company_type') == 'os'){
                            $location = [$location];
                            $locationOlahan = self::getLocationByLikeDescription([1,$id_company], $location);
                            $location = implode(",", $locationOlahan);
                        }
                        $locationToFunction = 'array['.$location.']';
                        //jika location ada nilai parameternya maka branch dan region dinull kan
                        $branchToFunction = 'null';
                        $regionToFunction = 'null';
                    } else {
                        $locationToFunction = 'null';
                    }
                }

                if($id_employee =='null'){
                    $thisEmployee = DB::table('hr_employee')->select('id_employee');

                    if($branchForGetEmployee && count($branchForGetEmployee)>0){
                        $employeeByBranch = JobPositionDetail::whereIn('id_branch', $branchForGetEmployee)->pluck('id_employee')->all();
                        $thisEmployee->whereIn('id_employee', $employeeByBranch);
                    } 
                    $id_employee = $thisEmployee->where('id_company', $id_company)->get()->pluck('id_employee')->all();
                }

                if(is_array($id_employee) && count($id_employee) > 0){
                    $id_employee_q2 = $id_employee;
                    $implode = implode(",", $id_employee);
                    $id_employee = 'array['.$implode.']';
                } else {
                    if($id_employee != 'null'){
                        $id_employee_q2 = explode(",", $id_employee);
                        $id_employee = 'array['.$id_employee.']';
                    } else {
                        $id_employee_q2 = null;
                        $id_employee = 'null';
                    }
                }

            // }

            $get1 = DB::table(DB::raw($tableName."(".$id_employee.",".$id_company.",".$startdate.",".$enddate.",".$branchToFunction.",".$locationToFunction.",".$regionToFunction.",".$department.",".$status.") sfwv"))
                ->select('sfwv.*');
                // ->whereIn('sfwv.employee_status', $status);
            $getActive = $get1->get();
  

            if($status_q2 == 'null' || $status_q2 == 'I'){
                $get2 = DB::table('hr_work_days as hwd')
                    ->join('hr_employee as he', 'hwd.id_employee', '=', 'he.id_employee')
                    ->leftJoin('master_users as mu', 'mu.id_user', '=', 'he.id_user')
                    ->join('master_shiftgroup_header as msh', function ($join) {
                        $join->on('he.id_shift_group', '=', 'msh.id_shiftgroup');
                        $join->on('he.id_company', '=', 'msh.id_company');
                    })
                    ->join('master_shiftgroup_detail as msd', function ($join) {
                        $join->on('msh.id_shiftgroup', '=', 'msd.id_shiftgroup');
                        $join->on('msh.id_company', '=', 'msd.id_company');
                        $join->on(DB::raw('(select(extract(ISODOW from(hwd.current_dates::date))))'), '=', 'msd.sequence');
                    })
                    ->leftJoin('master_daily_shift as mds', function ($join) {
                        $join->on('msd.id_shift', '=', 'mds.id_shift');
                        $join->on('msd.id_company', '=', 'mds.id_company');
                    })
                    ->leftJoin('hr_request_header as hrh', function ($join) {
                        $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                        $join->on('hwd.id_company', '=', 'hrh.id_company');
                    })
                    ->leftJoin('master_general_data as mgd', function ($join) {
                        $join->on('hwd.id_request_type', '=', 'mgd.id_general_data');
                        $join->on('hwd.id_company', '=', 'mgd.id_company');
                    })
                    ->leftJoin('master_leave_type as mlt', function ($join) {
                        $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                        $join->on('hrh.id_company', '=', 'mlt.id_company');
                    })
                    ->leftJoin('master_holiday as mh', function ($join) {
                        $join->on('hwd.id_holiday', '=', 'mh.id_holiday');
                        $join->on('hwd.id_company', '=', 'mh.id_company');
                    })
                    ->selectRaw("hwd.id_employee, he.name as employee_name, mu.user_name, hrh.reference_number as request_number, mgd.description as request_type, mh.holiday_name, msh.description as shift_group, hwd.id_workdays, hwd.id_shift, hwd.day_type, hwd.day_seq, concat(mds.description,'[', to_char(mds.start_time,'HH24:MI'),'-', to_char(mds.end_time,'HH24:MI'), ']') as shift, hwd.current_dates, hwd.schedule_employee_timezone, hwd.schedule_time_in, hwd.actual_time_in, hwd.late_in, hwd.schedule_time_out, hwd.actual_time_out, hwd.early_out, CASE hwd.current_employee_timezone WHEN 'Asia/Jakarta' THEN 'WIB' WHEN 'Asia/Makassar' THEN 'WITA' WHEN 'Asia/Jayapura' THEN 'WIT' ELSE 'WIB' END as current_employee_timezone, hwd.work_hours, hwd.overtime, hwd.current_name_in, hwd.current_address_in, hwd.current_latitude_in, hwd.current_longitude_in, hwd.current_name_out, hwd.current_address_out, hwd.current_latitude_out, hwd.current_longitude_out, hwd.id_request, hwd.id_request_type, hwd.id_holiday, hwd.note, '' as id_dept, '' as department, '' as id_region, '' as region, '' as id_branch, '' as branch, '' as id_location, '' as location, he.status as employee_status, he.expired_date, CASE WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out::date = hwd.current_dates and hwd.current_dates <= he.resign_date) OR (hwd.actual_time_in::date IS NOT NULL and hwd.actual_time_out::date = hwd.current_dates IS NOT NULL and hwd.current_dates <= he.resign_date) THEN 'PRS' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out::date = hwd.current_dates and hwd.current_dates <= he.resign_date) THEN 'NSI' WHEN (hwd.actual_time_in::date = hwd.current_dates and hwd.actual_time_out IS NULL and hwd.current_dates <= he.resign_date) THEN 'NSO' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'WD' and hwd.current_dates <= he.resign_date) THEN 'ABS' WHEN ((hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.day_type = 'OD' and hwd.current_dates <= he.resign_date) OR (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_holiday IS NOT NULL and (hwd.day_type = 'WD' OR hwd.day_type = 'WD') and hwd.current_dates <= he.resign_date)) THEN 'OFF' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NULL and hwd.current_dates > he.resign_date) THEN '-' WHEN (hwd.actual_time_in IS NULL and hwd.actual_time_out IS NULL and hwd.id_request IS NOT NULL and hwd.current_dates <= he.resign_date) THEN mlt.leave_code::text END AS attendace_status");

                if($startdate == 'null'){
                    $get2->whereDate('hwd.current_dates', '>=', date('Y-m-d'));
                } else {
                    $get2->whereDate('hwd.current_dates', '>=', $startdate_q2);
                }
                if($enddate == 'null'){
                    $get2->whereDate('hwd.current_dates', '<=', date('Y-m-d'));
                } else {
                    $get2->whereDate('hwd.current_dates', '<=', $enddate_q2);
                }
                if($id_employee_q2){
                    $get2->whereIn('hwd.id_employee', $id_employee_q2);
                }
                $get2->where('hwd.id_company', $id_company);
                $get2->where('he.status', 'I');
                $get2->whereRaw('(hwd.current_dates <= he.resign_date OR he.resign_date IS NULL)');
                $getInactive = $get2->get();
                // $getInactive = $getInactiveFilter->filter(function ($value, $key) {
                //     return $value->expired_date >= $value->current_dates;
                //     // return $value->expired_date >= $value->current_dates || is_null($value->expired_date);
                // });
            }
            $merged = $getActive->merge($getInactive);
            return $merged;
        } catch (\Exception $e) {
            return [];
        }
    }
    
    public function editdata($id_employee,$id_workdays,$actual_time_in,$actual_time_out) {
        DB::beginTransaction();
        try {
            if(DB::getDatabaseName()){
                $sql_updatemaster="
                    BEGIN TRY
                        BEGIN TRANSACTION
                        update a set
                        actual_time_in='".$actual_time_in."',
                        actual_time_out='".$actual_time_out."',
                        update_date='".date('Y-m-d H:i:s')."',
                        updated_by='".session('id_user')."'
                        from hr_work_days a
                        where id_workdays='".$id_workdays."'
                        and id_employee='".$id_employee."'
                        
                        COMMIT TRANSACTION
                    END TRY
                    BEGIN CATCH
                        if (@@TRANCOUNT > 0) ROLLBACK TRANSACTION

                        --gagal
                        select ERROR_MESSAGE() + ' Line ' + cast(ERROR_LINE() as nvarchar(5)) + '.' as message
                    END CATCH;
                ";
                $result_updatemaster=DB::update($sql_updatemaster);
                if($result_updatemaster)
                {
                    DB::commit();
                    $message=0;
                }
                else
                {
                    DB::rollback();
                    $message="Gagal update data";
                }
                
            }else{
                DB::rollback();
                $message="Gagal menghubungkan ke database";
            }

            return array("message"=>$message);

        } catch (\Exception $e) {
            DB::rollback();
            return array("data"=>'',"message"=>'Gagal menghubungkan ke database. '.$e);
        }
    }
    
    public static function patchWorkdays($id_company_from=null, $start=null, $end=null, $id_employee=null, $fromScheduler=false) {
        $period = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        foreach ($period as $k => $v) {
            $datePeriod[] = $v->format("Y-m-d");
        }
        $countAllEmployee = []; $countAllRecord = [];
        if(is_array($id_company_from)){
            $id_user = ($fromScheduler==true) ? 1 : ((Session::has('id_user')) ? session('id_user') : 1);
            foreach ($id_company_from as $key => $id_company) {
                $q_employee = DB::table('hr_employee as he')
                            ->select('he.id_employee', 'he.id_company', 'he.join_date')
                            ->where('he.status', 'A');
                if($id_employee){
                    if(strpos($id_employee, ',')!==false){
                        $id_employee_ = explode(',', $id_employee);
                    } else {
                        $id_employee_ = [$id_employee];
                    }
                    $q_employee->whereIn('he.id_employee', $id_employee_);
                }
                if($id_company){
                    $q_employee->where('he.id_company', $id_company);
                }
                $q_employee->orderBy('he.id_employee');
                $employee = $q_employee->get();
                $addWorkdays = [];
                $checkEmployeeLockLocation = [];

                if(@$employee && count(@$employee) > 0){
                    foreach (@$employee as $key => $val) {
                        $checkEmployeeLockLocation[] = $val->id_employee;
                        $dateCutPeriod = [];

                        $q_workdays = DB::table('hr_work_days as hwd')
                                ->select('hwd.current_dates')
                                ->where('hwd.id_employee', $val->id_employee)
                                ->whereBetween('hwd.current_dates', [$start, $end])
                                ->get();

                        if(strtotime($val->join_date) >= strtotime($start)){
                            $cutPeriod = new \DatePeriod(
                                new \DateTime($val->join_date),
                                new \DateInterval('P1D'),
                                new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
                            );
                            foreach ($cutPeriod as $k => $v) {
                                $dateCutPeriod[] = $v->format("Y-m-d");
                            }
                            $datePeriod = $dateCutPeriod;
                        } 
                        if(!$q_workdays){
                            $addWorkdays[] = [
                                'id_employee' => $val->id_employee,
                                'id_company' => $val->id_company,
                                'date' => $datePeriod
                            ];
                        } else {
                            $existing = $q_workdays->pluck('current_dates')->all();
                            $compare = array_diff($datePeriod, $existing);
                            $addWorkdays[] = [
                                'id_employee' => $val->id_employee,
                                'id_company' => $val->id_company,
                                'date' => $compare
                            ]; 
                        }
                    }
                }
                $countEmployee = []; $countRecord = [];
                if(count($addWorkdays) > 0){
                    foreach ($addWorkdays as $key => $val) {
                        $idEmployeeUpdate = $val['id_employee'];
                        $idCompanyUpdate = $val['id_company'];
                        $date = $val['date'];

                        if(count($date) > 0){
                            foreach ($date as $k => $item) {
                                try {
                                    $patch = DB::table(DB::raw("generateworkdaysnewemployee(".$idEmployeeUpdate.",".$idCompanyUpdate.",'".$item."','".$item."',".$id_user.")"))
                                            ->select('*')->get();
                                    $countRecord[] = count($date);

                                    // jika new employee tidak bisa maka pake bulky
                                    $bulky = DB::table(DB::raw("generateworkdaysbulky(".$idCompanyUpdate.",'".$item."','".$item."',".$id_user.")"))
                                            ->select('*')->get();
                                } catch (\Exception $e) {
                                    DB::rollBack();
                                    if(!$fromScheduler){
                                        \Log::info("select * from generateworkdaysnewemployee(".$idEmployeeUpdate.",".$idCompanyUpdate.",'".$item."','".$item."',".$id_user.") g");
                                    } else {
                                        \Log::channel('scheduler')->error("select * from generateworkdaysnewemployee(".$idEmployeeUpdate.",".$idCompanyUpdate.",'".$item."','".$item."',".$id_user.") g");
                                    }
                                    continue ;
                                }
                            }
                            $countEmployee[] = 1;
                        }
                    }
                }

                if(count($checkEmployeeLockLocation) > 0){
                    if(count($checkEmployeeLockLocation) > 0){
                        foreach ($checkEmployeeLockLocation as $k => $idEmployeeToCheckLock) {
                            //Untuk auto generate lock gps di workdays mengikuti kolom lock di hr_employee
                            $generateLock = DB::table(DB::raw("generate_lock_gps_location(".$idEmployeeToCheckLock.",".$id_company.",'".$start."','".$end."',".$id_user.")"))
                                ->select('*')->get();
                        }
                    }
                    
                    // if(is_array($checkEmployeeLockLocation) && count($checkEmployeeLockLocation) > 0){
                    //     $getEmployee = DB::table('hr_employee as he')->select('id_employee','lock_gps_location')->whereIn('he.id_employee', $checkEmployeeLockLocation)->get();
                    //     if($getEmployee){
                    //         foreach ($getEmployee as $key => $val) {
                    //             $lock = $val->lock_gps_location;
                    //             $data = ['lock_gps_location' => $lock];
                    //             $updateWD = DB::table('hr_work_days as hwd')
                    //                 ->where('hwd.id_employee', $val->id_employee)
                    //                 ->whereBetween('hwd.current_dates', [$start, $end])
                    //                 ->update($data);
                    //         }
                    //     }
                    // }
                }
                $countAllEmployee[] = array_sum($countEmployee); $countAllRecord[] = array_sum($countRecord); 
                $return = json_encode(['employee' => array_sum($countEmployee), 'record' => array_sum($countRecord)]);
                if(!$fromScheduler){
                    \Log::info('Patch Workdays Id Company ('.$id_company.') : '.$start.' ~ '.$end.' '.$return);
                } else {
                    \Log::channel('scheduler')->info('Patch Workdays Id Company ('.$id_company.') : '.$start.' ~ '.$end.' '.$return);
                }
                
            }
        } 
        if(!$fromScheduler){
            return response(['employee' => array_sum($countAllEmployee), 'record' => array_sum($countAllRecord)]);
        }
    }

    public static function subordinate($idPositionDetail, $idEmployee=null) {
        $data = DB::table('master_position_detail as mpd')
            ->select('he.nik_employee', 'he.name', 'mpd.id_position_detail')
            ->join('master_position_routing as mpr', function ($join) {
                $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                $join->on('mpd.id_company', '=', 'mpr.id_company');
            })
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('mpd.id_employee', '=', 'he.id_employee');
                $join->orOn('mpd.id_employee2', '=', 'he.id_employee');
                $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
            })
            ->join('master_job_grade as mjg', function ($join) {
                $join->on('mpr.id_job_grade', '=', 'mjg.id_job_grade');
                $join->on('mpr.id_company', '=', 'mjg.id_company');
            })
            ->where(function ($query) use ($idPositionDetail){
                $query->where('mpd.id_company', session('id_company'));
                $query->orWhere('mpd.assigned_to_company', session('id_company'));
            })
            ->where('mpd.status', 'A')
            ->where(function ($query) use ($idPositionDetail){
                $query->where('mpd.id_position_detail', '=', $idPositionDetail);
                $query->orWhere('mpd.parent_id_position_detail', '=', $idPositionDetail);
            });

        if($idEmployee){
            $data->where('he.id_employee', $idEmployee);
        }

        $result = $data->get();
        return $result;
    }

    public static function getAttendanceStatus($idEmployee, $start, $end, $status=null, $resultOnly = false) {
        $get = DB::table('hr_work_days as hwd')
            ->leftJoin('hr_employee as he', function ($join) {
                $join->on('hwd.id_employee', '=', 'he.id_employee');
            })
            ->leftJoin('hr_request_header as hrh', function ($join) {
                $join->on('hwd.id_request', '=', 'hrh.id_request_header');
                $join->on('hwd.id_company', '=', 'hrh.id_company');
            })
            ->leftJoin('hr_request_detail as hrd', function ($join) {
                $join->on('hrh.id_request_header', '=', 'hrd.id_request_header');
                $join->on('hwd.id_company', '=', 'hrh.id_company');
                $join->where(function ($where){
                    $where->whereNotNull('hrd.request_start_to');
                    $where->whereNotNull('hrd.request_end_to');
                });
            })
            ->leftJoin('master_general_data as mgd', function ($join) {
                $join->on('mgd.id_general_data', '=', 'hrh.id_request_type');
                $join->on('mgd.id_company', '=', 'hrh.id_company');
                $join->whereIn('mgd.code', ['Leave_Request','Change_Day_off','Attendance_Correction']);
            })
            ->leftJoin('master_leave_type as mlt', function ($join) {
                $join->on('hrh.id_leave_type', '=', 'mlt.id_leave_type');
                $join->on('hrh.id_company', '=', 'mlt.id_company');
            })
            ->select('hwd.*', 'he.resign_date', 'mlt.leave_code', 'mgd.code', 'hrd.day_type as day_type_detail')
            ->where('hwd.id_employee', $idEmployee)
            // ->whereRaw('hwd.current_dates::date >= he.join_date::date')
            ->whereBetween('hwd.current_dates', [$start, $end])
            ->orderBy('hwd.current_dates')
            ->get();
        
        if($resultOnly) {
            return $get;
        }

        return self::parseGetAttendanceStatus($get, $status);
    }

    public static function parseGetAttendanceStatus($get, $status = null) {
        $thisStatus = [];
        $thisStatusInDate = [];
        $thisStatusByDate = [];
        $thisStatusOfMonth = [];
        $thisStatusByDateFormat = [];
        $arrMonthName = [];
        $arrGetAttendance = [];
        $arrMonth = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];

        foreach ($get as $key => $val) {
            $tgl_carbon = Carbon::parse(@$val->current_dates)->locale('id')->settings(['formatFunction' => 'translatedFormat']);
            $tgl = $tgl_carbon->format('l, j F Y');
            $onlyDate = $tgl_carbon->format('j');
            $dateFormat = $tgl_carbon->format('Y-m-d');
            $actIn = Carbon::parse(@$val->actual_time_in)->toDateString();
            $actOut = Carbon::parse(@$val->actual_time_out)->toDateString();
            $today = date('Y-m-d');

            if((!is_null($val->actual_time_in) && !is_null($val->actual_time_out) && is_null($val->id_request) && $val->day_type == 'WD') ||
                (!is_null($val->actual_time_in) && !is_null($val->actual_time_out) && !is_null($val->id_request) && $val->day_type == 'WD' && $val->code == 'Attendance_Correction')){
                $thisStatus['PRS'][] = $tgl;
                $thisStatusByDate[$tgl] = 'PRS';
                $thisStatusInDate['PRS'][] = $onlyDate;
                $thisStatusByDateFormat['PRS'][] = $dateFormat;
            } 
            else if(is_null($val->actual_time_in) && ($actOut == $val->current_dates || !is_null($val->actual_time_out)) && (is_null($val->id_request) || (!is_null($val->id_request) && $val->day_type == 'WD' && $val->code == 'Attendance_Correction')) && $val->day_type == 'WD'){
                $thisStatus['NSI'][] = $tgl;
                $thisStatusByDate[$tgl] = 'NSI'; 
                $thisStatusInDate['NSI'][] = $onlyDate;
                $thisStatusByDateFormat['NSI'][] = $dateFormat;
            } 
            else if(is_null($val->actual_time_out) && ($actIn == $val->current_dates || !is_null($val->actual_time_in)) && (is_null($val->id_request) || (!is_null($val->id_request) && $val->day_type == 'WD' && $val->code == 'Attendance_Correction')) && $val->day_type == 'WD'){
                $thisStatus['NSO'][] = $tgl;
                $thisStatusByDate[$tgl] = 'NSO';
                $thisStatusInDate['NSO'][] = $onlyDate;
                $thisStatusByDateFormat['NSO'][] = $dateFormat;
            } 
            else if((is_null($val->actual_time_in) && is_null($val->actual_time_out) && is_null($val->id_request) && $val->day_type == 'WD' && strtotime($val->current_dates) <= strtotime($today)) 
                || 
                (($val->day_type_detail=='Half_Day1' || $val->day_type_detail=='Half_Day2') && (is_null($val->actual_time_in) && is_null($val->actual_time_out)) && ($val->day_type == 'WD') &&($val->day_seq!=6))
                ){
                $thisStatus['ABS'][] = $tgl;
                $thisStatusByDate[$tgl] = 'ABS';
                $thisStatusInDate['ABS'][] = $onlyDate;
                $thisStatusByDateFormat['ABS'][] = $dateFormat;
            }
            else if(
                (is_null($val->actual_time_in) && is_null($val->actual_time_out) && (is_null($val->id_request)||!is_null($val->id_request)) && $val->day_type == 'OD' && strtotime($val->current_dates) <= strtotime($today)) 
                || 
                ((is_null($val->actual_time_in)||!is_null($val->actual_time_in)) && (is_null($val->actual_time_out)||!is_null($val->actual_time_out)) && !is_null($val->id_holiday) && ($val->day_type=='WD'||$val->day_type=='OD') && strtotime($val->current_dates) <= strtotime($today))
                || 
                ((!is_null($val->actual_time_in) || !is_null($val->actual_time_out)) && (is_null($val->id_request)||!is_null($val->id_request)) && $val->day_type == 'OD' && strtotime($val->current_dates) <= strtotime($today)
                )){
                $thisStatus['OFF'][] = $tgl;
                $thisStatusByDate[$tgl] = 'OFF';
                $thisStatusInDate['OFF'][] = $onlyDate;
                $thisStatusByDateFormat['OFF'][] = $dateFormat;
            }
            else if(is_null($val->actual_time_in) && is_null($val->actual_time_out) && is_null($val->id_request) && strtotime($val->current_dates) > strtotime(@$val->resign_date)){
                $thisStatus['-'][] = $tgl;
                $thisStatusByDate[$tgl] = '-';
                $thisStatusInDate['-'][] = $onlyDate;
                $thisStatusByDateFormat['-'][] = $dateFormat;
            }
            else if((is_null($val->actual_time_in) && is_null($val->actual_time_out) && !is_null($val->id_request)) || (!is_null($val->actual_time_in) || !is_null($val->actual_time_out)) && !is_null($val->id_request)){
                $leaveCode = ($val->code == 'Change_Day_off') ? 'CDO' : $val->leave_code;
                $thisStatus[$leaveCode][] = $tgl;
                $thisStatusByDate[$tgl] =  $leaveCode;
                $thisStatusInDate[$leaveCode][] = $onlyDate;
                $thisStatusByDateFormat[$leaveCode][] = $dateFormat;
            }
            $arrGetAttendance[$tgl_carbon->format('m')][] = [
                'date' => $tgl,
                'status' => @$thisStatusByDate[$tgl],
            ];
            if(date('m') == $tgl_carbon->format('m')){
                $thisStatusOfMonth[$tgl] = @$thisStatusByDate[@$tgl];
            }
        }
        foreach ($arrMonth as $key => $val) {
            $nameOfMonth = Carbon::createFromFormat('m', $val)->locale('id')->settings(['formatFunction' => 'translatedFormat'])->format('F');
            $thisData = (@$arrGetAttendance[$val]) ? @$arrGetAttendance[$val] : [];
            $arrMonthName[$key] = [
                'name' => $nameOfMonth,
                'sequence' => $key+1,
                'data' => $thisData
            ];
        }

        $returnStatus = ($status) ? @$thisStatus[$status] : $thisStatus;
        $returnStatusInDate = ($status) ? @$thisStatusInDate[$status] : $thisStatusInDate;
        $returnStatusByFormatDate = ($status) ? @$thisStatusByDateFormat[$status] : $thisStatusByDateFormat;

        $return['by_year'] = $arrMonthName;
        $return['by_month'] = $thisStatusOfMonth;
        $return['by_status'] = $returnStatus;
        $return['by_status_in_date'] = $returnStatusInDate;
        $return['by_status_in_format_date'] = $returnStatusByFormatDate;
        return $return;
    }

    public static function employeeLockAttendance($status, $nik, $department, $regional, $branch, $location, $lock_location, $id_company) {
        $getEmployee = DB::table('hr_employee as he')
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                    $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('ml.id_branch', '=', 'mb.id_branch');
                    $join->on('ml.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_shiftgroup_header as msh', function ($join) {
                    $join->on('msh.id_shiftgroup', '=', 'he.id_shift_group');
                    $join->on('msh.id_company', '=', 'he.id_company');
                })
                ->select('he.id_employee', 'he.nik_employee', 'he.name', 'md.description as department', 'mpr.description as position', 'mr.description as region', 'mb.description as branch', 'ml.description as address_location', 'msh.description as shift', 'he.lock_gps_location')
                ->where('he.id_company', session('id_company'))
                ->where('he.status', 'A');
        if($nik){
            if(is_array($nik)){
                $getEmployee->whereIn('he.nik_employee', $nik); 
            } else {
                $getEmployee->where('he.nik_employee', $nik);
            }
        }
        if($department){
            if(is_array($department)){
                $getEmployee->whereIn('md.id_dept', $department); 
            } else {
                $getEmployee->where('md.id_dept', $department);
            }
        }
        if($regional){
            if(session('company_type') == 'os'){
                $regional = self::getRegionByLikeCode($id_company, $regional);
            }
            $getEmployee->whereIn('mr.id_region', $regional);
        }
        if(!is_null($branch) && $branch!='null'){
            if(session('company_type') == 'os'){
                $branch = self::getBranchBySameCode($id_company, $branch);
            }
            $getEmployee->whereIn('mb.id_branch', $branch);
        }
        if($location){
            if(session('company_type') == 'os'){
                $location = self::getLocationByLikeDescription($id_company, $location);
            }
            $getEmployee->whereIn('ml.id_location', $location);
        }
        if($lock_location){
            $getEmployee->whereIn('he.lock_gps_location', $lock_location);
        }
        $data = $getEmployee->get();
        return $data;
    }

    public static function attendancePhoto($id_employee, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu) {
        $getInactive = collect([]);
        $getActive = collect([]);
        $branchForGetEmployee = [];

        if($id_employee =='null'){
            $id_employee = null;
        }
        if($startdate == 'null'){
            $startdate = date('Y-m-d');
        }
        if($enddate == 'null'){
            $enddate = date('Y-m-d');
        }
        if(!is_array($branch)){
            $branch = null;
        }
        if(!is_array($location)){
            $location = null;
        }
        if(!is_array($regional)){
            $regional = null;
        }
        if($department == 'null'){
            $department = null;
        }
        if(!is_array($status)){
            $status = ['A', 'I'];
        }

        if(in_array('A', $status)){
            $sub = DB::table('hr_work_days as hwd')
                ->join('hr_employee as he', function ($join) {
                    $join->on('he.id_employee', '=', 'hwd.id_employee');
                    $join->on('hwd.id_company', '=', 'he.id_company');
                    $join->on('hwd.current_dates', '>=','he.join_date');
                })
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->where('mpd.secondary_position', 0);
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                    $join->whereRaw('(mpd.id_company = he.id_company OR mpd.assigned_to_company = he.id_company)');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('ml.id_branch', '=', 'mb.id_branch');
                    $join->on('ml.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_shiftgroup_header as msh', function ($join) {
                    $join->on('msh.id_shiftgroup', '=', 'he.id_shift_group');
                    $join->on('msh.id_company', '=', 'he.id_company');
                })
                ->select('he.id_employee', 'he.nik_employee', 'he.name', 'md.description as department', 'mpr.description as position', 'mr.description as region', 'mb.description as branch', 'ml.description as address_location', 'msh.description as shift', 'he.lock_gps_location', 'hwd.current_dates', 'hwd.id_workdays', 'hwd.image_attachment_in', 'hwd.image_attachment_out')
                ->where('he.id_company', session('id_company'))
                ->where('he.status', 'A');

            $sub->whereDate('hwd.current_dates', '>=', $startdate);
            $sub->whereDate('hwd.current_dates', '<=', $enddate);

            if($path_menu == 'employee/employee_setting/workdays'){
                $sub->where('hwd.id_company', $id_company);
            }
            if($id_employee && is_array($id_employee)){
                $sub->whereIn('hwd.id_employee', $id_employee);
            }
            if($location && is_array($location)){
                if(session('company_type') == 'os'){
                    $location = self::getLocationByLikeDescription([1,$id_company], $location);
                }
                $sub->whereIn('ml.id_location', $location);
            }
            if(!$location && $branch && is_array($branch)){
                if(session('company_type') == 'os'){
                    $branch = self::getBranchBySameCode([1,$id_company], $branch);
                }
                $sub->whereIn('mb.id_branch', $branch);
            }
            if((!$location && !$branch && $regional && is_array($regional)) || (!$location && $branch && $regional && is_array($regional)) ){
                if(session('company_type') == 'os'){
                    $regional = self::getRegionByLikeCode([1,$id_company], $regional);
                }
                $sub->whereIn('mr.id_region', $regional);
            }
            if($department){
                $sub->where('md.id_dept', $department);
            }
            $get1 = DB::table(DB::raw("({$sub->toSql()}) as sub"))->mergeBindings($sub)
                ->leftJoin('hr_employee as he2', 'sub.id_employee', '=', 'he2.id_employee')
                ->select('sub.*')
                ->where('he2.status', 'A');
            $getActive = $get1->get();
        }

        if(in_array('I', $status)){
            $sub2 = DB::table('hr_work_days as hwd')
                ->join(DB::raw("(select id_user, id_shift_group, id_employee, nik_employee, id_company, name, status, expired_date, resign_date from hr_employee where status = 'I') as he"),function($join){
                    $join->on('he.id_employee','=','hwd.id_employee');
                })
                ->join('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
                ->join('master_position_detail as mpd', function ($join) {
                    $join->where('mpd.secondary_position', 0);
                    $join->on('hct.id_old_position_detail', '=', 'mpd.id_position_detail');
                    $join->whereRaw('(mpd.id_company = hct.id_company OR mpd.assigned_to_company = hct.id_company)');
                })
                ->leftJoin('master_location as ml', function ($join) {
                    $join->on('ml.id_location', '=', 'mpd.id_location');
                    $join->on('ml.id_company', '=', 'mpd.id_company');
                })
                ->leftJoin('master_branch as mb', function ($join) {
                    $join->on('ml.id_branch', '=', 'mb.id_branch');
                    $join->on('ml.id_company', '=', 'mb.id_company');
                })
                ->leftJoin('master_region as mr', function ($join) {
                    $join->on('mb.id_region', '=', 'mr.id_region');
                    $join->on('mb.id_company', '=', 'mr.id_company');
                })
                ->leftJoin('master_position_routing as mpr', function ($join) {
                    $join->on('mpd.id_position_routing', '=', 'mpr.id_routing');
                    $join->on('mpd.id_company', '=', 'mpr.id_company');
                })
                ->leftJoin('master_job_position as mjp', function ($join) {
                    $join->on('mpr.id_position', '=', 'mjp.id_position');
                    $join->on('mpr.id_company', '=', 'mjp.id_company');
                })
                ->leftJoin('master_department as md', function ($join) {
                    $join->on('mjp.id_dept', '=', 'md.id_dept');
                    $join->on('mjp.id_company', '=', 'md.id_company');
                })
                ->leftJoin('master_shiftgroup_header as msh', function ($join) {
                    $join->on('msh.id_shiftgroup', '=', 'he.id_shift_group');
                    $join->on('msh.id_company', '=', 'he.id_company');
                })
                ->select('he.id_employee', 'he.nik_employee', 'he.name', 'md.description as department', 'mr.description as region', 'mb.description as branch', 'ml.description as address_location', 'msh.description as shift', 'hwd.lock_gps_location', 'hwd.current_dates', 'hwd.id_workdays', 'hwd.image_attachment_in', 'hwd.image_attachment_out')
                ->where('he.id_company', session('id_company'))
                ->where('he.status', 'I');

            $sub2->whereDate('hwd.current_dates', '>=', $startdate);
            $sub2->whereDate('hwd.current_dates', '<=', $enddate);
            $sub2->whereRaw('(hwd.current_dates <= he.resign_date OR he.resign_date IS NULL)');

            if($path_menu == 'employee/employee_setting/workdays'){
                $sub2->where('hwd.id_company', $id_company);
            }
            if($id_employee && is_array($id_employee)){
                $sub2->whereIn('hwd.id_employee', $id_employee);
            }
            if($location && is_array($location)){
                if(session('company_type') == 'os'){
                    $location = self::getLocationByLikeDescription([1,$id_company], $location);
                }
                $sub2->whereIn('ml.id_location', $location);
            }
            if(!$location && $branch && is_array($branch)){
                if(session('company_type') == 'os'){
                    $branch = self::getBranchBySameCode([1,$id_company], $branch);
                }
                $sub2->whereIn('mb.id_branch', $branch);
            }
            if((!$location && !$branch && $regional && is_array($regional)) || (!$location && $branch && $regional && is_array($regional)) ){
                if(session('company_type') == 'os'){
                    $regional = self::getRegionByLikeCode([1,$id_company], $regional);
                }
                $sub2->whereIn('mr.id_region', $regional);
            }
            if($department){
                $sub2->where('md.id_dept', $department);
            }
            $sub2->groupBy('he.id_employee', 'he.nik_employee', 'he.name', 'md.description', 'mr.description', 'mb.description', 'ml.description', 'msh.description', 'hwd.lock_gps_location', 'hwd.current_dates', 'hwd.id_workdays', 'hwd.image_attachment_in', 'hwd.image_attachment_out');

            $get2 = DB::table(DB::raw("({$sub2->toSql()}) as sub"))->mergeBindings($sub2)
                ->leftJoin('hr_employee as he2', 'sub.id_employee', '=', 'he2.id_employee')
                ->select('sub.*')
                ->where('he2.status', 'I');
            $getInactive = $get2->get();
        }
        $merged = $getActive->merge($getInactive);
        return $merged;
    }

    public static function checkDayTypeByIdShift($start, $end) {
        $getMasterDailyShift = DB::table('master_daily_shift as mds')
            ->select('id_shift', 'day_type')
            ->get();

        $getMasterDailyShift = $getMasterDailyShift->keyBy(function ($item) {
            return $item->id_shift;
        });

        $end = date('Y-m-d', strtotime($end." +1 days")); //pengecekan laravel jika range sampai hari ini maka harus +1 day

        $getWorkDay = DB::table('hr_work_days as hwd')
            ->select('hwd.id_shift', 'hwd.day_type', 'hwd.id_employee', 'hwd.current_dates')
            ->whereBetween('hwd.current_dates', [$start, $end])
            ->where('hwd.day_type', 'OD')
            ->orderBy('hwd.id_employee')
            ->get();

        $diffDayType = [];

        if($getWorkDay->count() > 0){
            foreach ($getWorkDay as $k => $val) {
                if($val->day_type != $getMasterDailyShift[$val->id_shift]->day_type){
                    $diffDayType[] = $val->id_employee;
                }
                $dataUpdate['day_type'] = $getMasterDailyShift[$val->id_shift]->day_type;
                $update = DB::table('hr_work_days')
                    ->where('id_employee', $val->id_employee)
                    ->where('current_dates', $val->current_dates)
                    ->update($dataUpdate);
            }
        }
        return count($diffDayType);
    }

    public static function updateWorkdaysByHoliday($start, $end) {

        $company = DB::table('master_company as mc')->where('mc.status','A')->orderBy('mc.id_company')->get();
        foreach ($company as $key => $item) {
            \Log::channel('scheduler')->info('Recurring holiday. id_company:'. $item->id_company);
            $recurring = DB::table(DB::raw('RecurringHoliday ('.$item->id_company.', 1)'))->get();
        }

        $getMasterDailyShift = DB::table('master_daily_shift as mds')
            ->select('id_shift', 'id_company', 'day_type')
            ->where('day_type', 'OD')
            ->get();

        $getMasterDailyShift = $getMasterDailyShift->keyBy(function ($item) {
            return $item->id_company;
        });

        $getHoliday = DB::table('master_holiday as mh')
            ->whereBetween('mh.start_date', [$start, $end])
            ->where('mh.status', 'A')
            ->get();

        if($getHoliday->count() > 0){
            $today = date('Y-m-d');
            foreach ($getHoliday as $k => $val) {
                $idHoliday = $val->id_holiday;
                $idCompany = $val->id_company;
                $note = $val->holiday_name;
                $start_ = $val->start_date;
                $end_ = $val->end_date;

                if($today > $end_){
                    // continue;
                }

                $workdays = DB::table('hr_work_days as hwd')
                    ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hwd.id_employee')
                    ->whereBetween('hwd.current_dates', [$start_, $end_])
                    ->where('he.status', 'A')
                    ->where('hwd.day_type', 'WD');

                $getLineHoliday = DB::table('master_line_holiday as mlh')
                    ->where('mlh.id_holiday', $val->id_holiday)->get();

                if($getLineHoliday->count() > 0){
                    $region = collect($getLineHoliday->pluck('id_regional')->all())->filter();
                    $branch = collect($getLineHoliday->pluck('id_branch')->all())->filter();
                    $location = collect($getLineHoliday->pluck('id_location')->all())->filter();

                    if($region->count() > 0){
                        $thisBranch = DB::table('master_branch')->select('id_branch')->whereIn('id_region', $region)->get()->pluck('id_branch')->all();
                        $thisLocation = DB::table('master_location')->select('id_location')->whereIn('id_branch', $thisBranch)->get()->pluck('id_location')->all();
                        $workdays->whereIn('target_id_location', $thisLocation);
                    } else if($branch->count() > 0){
                        $thisLocation = DB::table('master_location')->select('id_location')->whereIn('id_branch', $branch)->get()->pluck('id_location')->all();
                        $workdays->whereIn('target_id_location', $thisLocation);
                    } else if($location->count() > 0){
                        $workdays->whereIn('target_id_location', $location);
                    }
                }
                else {
                    $workdays->where('hwd.id_company', $idCompany);
                }

                $dataUpdate = [
                    'id_shift' => $getMasterDailyShift[$idCompany]->id_shift,
                    'day_type' => $getMasterDailyShift[$idCompany]->day_type,
                    'id_holiday' => $idHoliday,
                    'note' => $note,
                ];

                $countRecords = $workdays->get()->count();
                \Log::channel('scheduler')->info('There are '.$countRecords.' records work days update to id_holiday('.$idHoliday.')');
                $workdays->update($dataUpdate);
            }
        }
        return true;
    }

    public static function updateWorkdaysLocationByPosition($date=null) {
        $byDate = !is_null($date) ? $date : date('Y-m-d');
        $getEmployee = DB::table('hr_employee as he')
                ->leftJoin('master_position_detail as mpd', function ($join) {
                    $join->on('he.id_employee', '=', 'mpd.id_employee');
                    $join->orOn('he.id_employee', '=', 'mpd.id_employee2');
                })
                ->select('he.id_employee', 'mpd.id_location')
                ->where('he.status', 'A')
                ->get();

        foreach ($getEmployee as $k => $val) {
            $dataUpdate = ['target_id_location' => $val->id_location];
            $update = DB::table('hr_work_days')
                ->whereDate('current_dates', '>=', $byDate)
                ->where('id_employee', $val->id_employee)
                ->update($dataUpdate);
        }
    }
}
