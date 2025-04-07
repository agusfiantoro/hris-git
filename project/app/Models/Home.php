<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\MasterOrganization\MasterLocation;
use Illuminate\Support\Facades\Storage;
use DB;

class Home extends Model
{
    use HasFactory;
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
	}

    public function dashboardinformation() {
        try {
            $result_getdata=[];
            if(DB::getDatabaseName()){
                $sql_getidemployee="
                    select	id_employee
                    from hr_employee
                    where id_user='".session('id_user')."'
                ";
                $result_getdata['getidemployee'] = collect(DB::select($sql_getidemployee))->first();

                $sql_employeeinformation="
                    select	sfev.employee_name,sfepv.department, sfepv.position_detail, sfev.age, sfev.period_of_employment, sfwv.actual_time_in, sfwv.actual_time_out
                    from	sp_funct_employee_view(".$result_getdata['getidemployee']->id_employee.",".session('id_company').") sfev
                    left join	sp_funct_employee_position_view(".$result_getdata['getidemployee']->id_employee.",".session('id_company').") sfepv
                    on	sfev.id_employee = sfepv.id_employee
                    and	sfev.id_company = sfepv.id_company
                    left join	sp_funct_workdays_view(".$result_getdata['getidemployee']->id_employee.",".session('id_company').",current_date,null,null,null,null,null) sfwv
                    on	sfev.id_employee = sfwv.id_employee
                ";
                $result_getdata['employeeinformation'] = collect(DB::select($sql_employeeinformation))->first();

                $sql_announcement="
                    select	hea.id_announcement, hea.description
                    from	HR_EMPLOYEE_ANOUNCEMENT as hea
                    left join master_general_data as mgd on hea.id_anouncement_type = mgd.id_general_data
                    where	hea.status = 'A'
                    and 	hea.published = True
                    and	mgd.code in ('Employee_Information', 'Announcement_Award')
                    and	hea.id_company = '".session('id_company')."'
                    order by hea.update_date, hea.reference_number desc
                    limit 5
                ";
                $result_getdata['announcement'] = collect(DB::select($sql_announcement))->toArray();

                $sql_companypolicy="
                    select	hea.id_announcement, hea.description
                    from	HR_EMPLOYEE_ANOUNCEMENT as hea
                    left join master_general_data as mgd on hea.id_anouncement_type = mgd.id_general_data
                    where	hea.status = 'A'
                    and 	hea.published = True
                    and	mgd.code in ('SOP', 'JUKLAK')
                    and	hea.id_company = '".session('id_company')."'
                    order by hea.update_date, hea.reference_number desc
                    limit 5
                ";
                $result_getdata['companypolicy'] = collect(DB::select($sql_companypolicy))->toArray();

                $sql_upcomingevent="
                    select	hep.description, hep.start_date
                    from	hr_event_management hem
                    join	hr_event_program hep
                    on		hem.id_event_management = hep.id_event_management
                    and		hem.id_company = hep.id_company
                    and		hem.status = hep.status
                    join	hr_event_attendees hea
                    on		hep.id_event_program = hea.id_event_program
                    and		hem.id_event_management = hea.id_event_management
                    and		hep.id_company = hea.id_company
                    where	booked_by = '".$result_getdata['getidemployee']->id_employee."' --id_employee
                    and		hep.start_date >= current_date
                    and		hep.status = 'A'
                    limit 5
                ";
                $result_getdata['upcomingevent'] = collect(DB::select($sql_upcomingevent))->toArray();

                $sql_workdayshistory="
                    select	current_dates, day_type, actual_time_in, actual_time_out
                    from	sp_funct_workdays_view(".$result_getdata['getidemployee']->id_employee.",".session('id_company').",null,current_date,null,null,null,null) 
                    --(p_id_employee,p_id_company,p_start_date ,p_end_date ,p_id_branch , p_id_location , p_id_region , p_id_dept )
                    order by current_dates desc
                    limit 5
                ";
                $result_getdata['workdayshistory'] = collect(DB::select($sql_workdayshistory))->toArray();
                
                $sql_leavebalance="
                    select	*
                    from	 sp_funct_leave_balance_view (".$result_getdata['getidemployee']->id_employee.",".session('id_company').",null)
                ";
                $result_getdata['leavebalance'] = collect(DB::select($sql_leavebalance))->first();

                $sql_leaverequest="
                    select	*
                    from	 sp_funct_leave_request_dashboard_view (".$result_getdata['getidemployee']->id_employee.",".session('id_company').")
                ";
                $result_getdata['leaverequest'] = collect(DB::select($sql_leaverequest))->first();

                $message=0;
            }else{
                $message="Gagal menghubungkan ke database";
            }

            return array("data"=>$result_getdata,"message"=>$message);

        } catch (\Exception $e) {
            return array("data"=>'',"message"=>'Gagal menghubungkan ke database. '.$e);
        }
    }

    public static function employee_information($id_employee) {
        $data = DB::table(DB::raw('sp_funct_employee_view ('.$id_employee.', '.session('id_company').') sfev'))
                    ->leftJoin(DB::raw('sp_funct_employee_position_view ('.$id_employee.', '.session('id_company').') sfepv'), function ($join) {
                        $join->on('sfev.id_employee', '=', 'sfepv.id_employee');
                        $join->on('sfev.id_company', '=', 'sfepv.id_company');
                        $join->orOn('sfev.id_company', '=', 'sfepv.assigned_to_company');
                    })
                    ->join(DB::raw('sp_funct_workdays_view ('.$id_employee.', '.session('id_company').',current_date,null,null,null,null,null) sfwv'), 'sfev.id_employee', '=', 'sfwv.id_employee', 'left')
                    ->select('sfev.employee_name', 'sfepv.department', 'sfepv.position_detail', 'sfev.age', 'sfev.period_of_employment', 'sfwv.actual_time_in', 'sfwv.actual_time_out','current_dates');
        return $data->get();
    }

    public static function announcement() {
        $now = Carbon::now()->toDateString();
        $data = DB::table('hr_employee_anouncement as hea')
                    ->join('master_general_data as mgd', 'hea.id_anouncement_type', '=', 'mgd.id_general_data', 'left')
                    ->select('hea.id_announcement', 'hea.description')
                    ->where('hea.status', 'A')
                    ->where('hea.published', true)
                    ->whereIn('mgd.code', ['Employee_Information', 'Announcement_Award'])
                    ->where('hea.id_company', '=', session('id_company'))
                    // ->whereRaw("hea.start_date <=  date('$now')")
                    // ->whereRaw("hea.end_date >=  date('$now')")
                    ->orderBy('hea.id_announcement', 'desc')
                    ->limit(5);
        return $data->get();
    }

    public static function company_policy() {
        $data = DB::table('hr_employee_anouncement as hea')
                    ->join('master_general_data as mgd', 'hea.id_anouncement_type', '=', 'mgd.id_general_data', 'left')
                    ->select('hea.id_announcement', 'hea.description')
                    ->where('hea.status', 'A')
                    ->where('hea.published', true)
                    ->whereIn('mgd.code', ['SOP', 'JUKLAK'])
                    ->where('hea.id_company', '=', session('id_company'))
                    ->orderBy('hea.id_announcement', 'desc')
                    ->limit(5);
        return $data->get();
    }

    public static function upcoming_event($id_employee) {
        $data = DB::table('hr_event_management as hem')
            ->join('hr_event_program as hep', function ($join) {
                $join->on('hem.id_event_management', '=', 'hep.id_event_management');
                $join->on('hem.id_company', '=', 'hep.id_company');
                $join->on('hem.status', '=', 'hep.status');
            })
            ->join('hr_event_attendees as hea', function ($join) {
                $join->on('hep.id_event_program', '=', 'hea.id_event_program');
                $join->on('hem.id_event_management', '=', 'hea.id_event_management');
                $join->on('hep.id_company', '=', 'hea.id_company');
            })
            ->select('hem.event_category', 'hem.description as program', 'hep.description as course', 'hep.id_event_program', 'hep.id_course_header', 'hea.booked_by as id_employee','hep.start_date', 'hep.end_date', 'hea.start_date as start_date_attendee', 'hea.end_date as end_date_attendee', 'hea.status')
            ->where('hea.booked_by', $id_employee)
            ->where('hep.status', 'A')
            ->where('hea.status', '!=', 'Cancel')
            ->orderBy('hep.start_date', 'desc');
        return $data->get();
    }

    public static function public_course_program($id_employee) {
        $getCourse = DB::table('hr_event_program as hep')
            ->leftJoin( 'hr_event_management as hem', 'hep.id_event_management', '=','hem.id_event_management')
            ->select('hep.description', 'hem.id_event_management')
            ->where('hem.status', 'A')
            ->where('hem.public', true)
            ->where('hem.id_company', '=', session('id_company'))
            ->whereDate('hem.end_date_registration', '>=', date('Y-m-d'))
            ->orderBy('hep.description');
        $resultCourse = $getCourse->get();
        $allCourse = [];
        if($resultCourse->count() > 0){
            foreach ($resultCourse as $k => $val) {
                $allCourse[$val->id_event_management][] = $resultCourse[$k];
            }
        }

        $getProgram = DB::table('hr_event_management as hem')
            ->select('hem.*')
            ->where('hem.status', 'A')
            ->where('hem.public', true)
            ->where('hem.id_company', '=', session('id_company'))
            ->whereDate('hem.end_date_registration', '>=', date('Y-m-d'))
            ->orderBy('hem.end_date_registration');
        $result = $getProgram->get();

        if($result->count() > 0){
            foreach ($result as $k => $val) {
                $getAttendee = DB::table('hr_event_attendees as hea')
                    ->where('hea.id_event_management', $val->id_event_management)
                    ->where('hea.booked_by', $id_employee)
                    ->count();

                $result[$k]->attachment_path = '';
                $result[$k]->programs = [];
                $result[$k]->status_registered = false;

                if(@$val->attachment){
                    $result[$k]->attachment_path = asset("project/storage/app/public/upload/course_event").'/'.@$val->attachment;
                }
                $result[$k]->end_date_registration_alias = Carbon::parse($val->end_date_registration)->format('d F Y');

                if(array_key_exists($val->id_event_management, $allCourse)){
                    $result[$k]->programs = $allCourse[$val->id_event_management];
                }
                if($getAttendee > 0){
                    $result[$k]->status_registered = true;
                }
            }
        }
        return $result;
    }

    public static function workdays_history($id_employee) {
        $data = DB::table(DB::raw('sp_funct_workdays_view ('.$id_employee.', '.session('id_company').',null,current_date,null,null,null,null) sfwv'))
                    ->select('current_dates', 'day_type', 'actual_time_in', 'actual_time_out')
                    ->orderBy('sfwv.current_dates','desc')
                    ->limit(5);
        return $data->get();
    }

    public static function leave_balance($id_employee) {
        $getLeaveType = DB::table('master_leave_type')->where('id_company',session('id_company'))->pluck('id_leave_type','leave_code')->all();

        $data = DB::table(DB::raw('sp_funct_leave_balance_view ('.$id_employee.', '.session('id_company').','.$getLeaveType['ANL'].') sflbv'));
        return $data->first();
    }

    public static function leave_request($id_employee) {
        $data = DB::table(DB::raw('sp_funct_leave_request_dashboard_view ('.$id_employee.', '.session('id_company').') sflrdv'));
        return $data->first();
    }

    public static function attendance_per_month($id_employee=null, $id_company=null) {
        $id_company = $id_company ?? session('id_company');
        $id_employee = $id_employee ?? 'null';

        $data = DB::table(DB::raw("sp_funct_dashboard_attendance_per_month_view (".$id_employee.",".$id_company.")"));
        return $data->get();
    }

    public static function attendance_per_year($id_employee=null, $id_company=null) {
        $id_company = $id_company ?? session('id_company');
        $id_employee = $id_employee ?? 'null';

        $data = DB::table(DB::raw("sp_funct_dashboard_attendance_per_year_view (".$id_employee.",".$id_company.")"));
        return $data->get();
    }

    public static function employee_by_department($id_employee=null, $id_company=null, $path_menu=null) {
        $id_company = $id_company ?? session('id_company');
        $id_employee = $id_employee ?? 'null';

        $data = DB::table(DB::raw('sp_funct_employee_position_tree_view (null, '.$id_company.') sfeptv'));
		$data->where('status', 'A');
                
        if(session('access_group') == 'Default_Manager'){
            $data->selectRaw("coalesce(department,'Not Assigned') as department, count(id_employee)");
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)[0];
            $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
            $id_branch      = [];
            $id_location    = [];
            if(count($get_branch) > 0){
                $id_branch        = $get_branch->pluck('id_branch')->all();
            }
            if(count($id_branch) > 0){
                $id_location = MasterLocation::whereIn('id_branch', $id_branch)->pluck('id_location')->all();
            }

            $data->whereIn('id_location', $id_location);
            $data->groupBy('department');
            $data->orderBy('department','asc');
            $data->orderBy('count','asc');
        } else {
            $data->selectRaw("coalesce(department,'Not Assigned') as department, count(id_employee)")
                ->groupBy('department');
        }

        return $data->get();
    }

    public static function employee_turnover($id_employee=null, $id_company=null) {
        $id_company = $id_company ?? session('id_company');
        $id_employee = $id_employee ?? 'null';

        $data = DB::table(DB::raw('sp_funct_dashboard_in_out_employee_view ('.$id_company.')'));
        return $data->get();
    }

    public static function employee_contract_expired($id_employee=null, $id_company=null, $contract_number=null) {
        $id_employee = $id_employee ?? 'null';
        $id_company = $id_company ?? session('id_company');
        $contract_number = $contract_number ?? 'null';
        $data = DB::table(DB::raw('sp_funct_dashboard_contract_expired_view ('.$id_employee.','.$id_company.','.$contract_number.')'));
        return $data->get();
    }

    public static function birthday($id_company=null, $id_employee=null) {
        $id_company = $id_company ?? session('id_company');

        $data = DB::table('hr_employee as he')
                    ->join('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                    ->join('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                    ->join('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                    ->join('master_region as mr', 'mr.id_region', '=', 'mb.id_region')
                    ->selectRaw("he.id_employee, he.name, he.birthdate as birthdate, he.image_attachment, mr.description as region, mpr.description as position")
                    ->where('he.status', 'A')
                    ->where('he.id_company', $id_company)
                    ->orderBy('he.name');

        if($id_employee || count($id_employee) > 0){
            $data->whereIn('he.id_employee', $id_employee);
        }
        return $data->get();
    }

    public static function getEmployeePositionAndRegion($id_company=null, $id_employee=null, $id_branch=[]) {
        $id_company = $id_company ?? session('id_company');

        $mlt = DB::table('master_leave_type')->where([
            ['leave_code', '=', 'ANL'],
            ['status', '=', 'A'],
            ['id_company', '=', $id_company],
        ])->get()->pluck('id_leave_type')->all();

        $active = DB::table('hr_employee as he')
                    ->join('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                    ->join('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                    ->join('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                    ->join('master_region as mr', 'mr.id_region', '=', 'mb.id_region')
                    ->join('hr_leave_balance_emp as hlb', 'hlb.id_employee', '=', 'he.id_employee','left')
                    ->selectRaw("he.id_employee, he.name, he.nik_employee, he.status, mb.description as branch, he.private_mail, mr.description as region, mpr.description as position, hlb.leave_quota, hlb.used_leave, (hlb.leave_quota - hlb.used_leave) as remaining_annual_leave, hlb.effective_date, hlb.expired_date")
                    ->where('he.status', 'A')
                    ->where('hlb.status', 'A')
                    ->whereRaw('current_date BETWEEN hlb.effective_date AND hlb.expired_date')
                    ->whereIn('hlb.id_leave_type', $mlt)
                    ->where('he.id_company', $id_company);
        if($id_employee || count($id_employee) > 0){
            $active->whereIn('he.id_employee', $id_employee);
        }
        $getActive = $active->get()->unique();

        $inactive = DB::table('hr_employee as he')
                    ->join('hr_career_transaction as hct', 'hct.id_employee', '=', 'he.id_employee')
                    ->join('master_position_detail as mpd', 'mpd.id_position_detail', '=', 'hct.id_old_position_detail')
                    ->join('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                    ->join('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                    ->join('master_region as mr', 'mr.id_region', '=', 'mb.id_region')
                    ->join('hr_leave_balance_emp as hlb', 'hlb.id_employee', '=', 'he.id_employee','left')
                    ->selectRaw("he.id_employee, he.name, he.nik_employee, he.status, mb.description as branch, he.private_mail, mr.description as region, mpr.description as position, hlb.leave_quota, hlb.used_leave, (hlb.leave_quota - hlb.used_leave) as remaining_annual_leave, hlb.effective_date, hlb.expired_date")
                    ->where('he.status', 'I')
                    // ->where('hlb.status', 'I')
                    ->whereRaw('he.resign_date BETWEEN hlb.effective_date AND hlb.expired_date')
                    ->whereIn('hlb.id_leave_type', $mlt)
                    ->where('he.id_company', $id_company);
        if(count($id_branch) > 0){
            $inactive->whereIn('mb.id_branch', $id_branch);
        }
        $getInactive = $inactive->get()->unique();

        $final = $getActive->merge($getInactive)->sortBy('name');
        return $final;
    }

    public static function request_type($id_company=null) {
        $id_company = $id_company ?? session('id_company');

        $data = DB::table('master_general_data as mgd')
                    ->join('master_general_type as mgt', 'mgt.id_general_type', '=', 'mgd.id_general_type', 'left')
                    ->selectRaw("mgd.description")
                    ->where('mgt.general_type', 'master_request_type')
                    ->where('mgd.id_company', $id_company)
                    ->whereNotIn('mgd.code', ['Overtime_Request', 'Cancel_Leave']);
        return $data->get();
    }

    public static function listCampaign($idCompany, $idEmployee, $start=null, $end=null, $idCampaign=null) {
        $data = DB::table('hr_company_campaign as hcc')
            ->leftJoin('relation_campaign_users as rcu', function ($join) use ($idEmployee){
                $join->on('rcu.id_company_campaign', '=', 'hcc.id_company_campaign');
                $join->where('rcu.id_employee', '=', $idEmployee);
            })
            ->select('hcc.*', 'rcu.id_company_campaign as is_already_read')
            ->where(function($q) {  
                $q->whereRaw('? between hcc.start_date and hcc.end_date', [date('Y-m-d')])
                ->orWhereRaw('(? >= hcc.start_date and hcc.end_date is null)', [date('Y-m-d')]); 
            })
            ->where('hcc.id_company', $idCompany)
            ->orderByDesc('hcc.id_company_campaign');

        if($idCampaign){
            $idCampaign = is_array($idCampaign) ? $idCampaign : [$idCampaign];
            $data->whereIn('hcc.id_company_campaign', $idCampaign);
        }
        if($data->count() > 0){
            $pathCampaignStorage = 'public/upload/campaign/';
            $return = $data->get();
            foreach ($return as $k => $val) {
                $return[$k]->is_already_read = !is_null($val->is_already_read) ? true : false;;
                $return[$k]->image_poster_path = null;
                $return[$k]->attachment_path = null;

                if(!is_null(@$val->image_poster) && Storage::exists($pathCampaignStorage.@$val->image_poster)) {
                    $return[$k]->image_poster_path = asset('project/storage/app/'.$pathCampaignStorage.@$val->image_poster);
                }
                if(!is_null(@$val->attachment) && Storage::exists($pathCampaignStorage.@$val->attachment)) {
                    $return[$k]->attachment_path = asset('project/storage/app/'.$pathCampaignStorage.@$val->attachment);
                }
            }
        } else {
            $return = collect([]);
        }
        return $return;
    }

    public static function readCampaign($employee, $idCompanyCampaign) {
        $idEmployee    = $employee->id_employee;
        $idUser        = $employee->id_user;
        $idCompany     = $employee->id_company;
        $today         = date('Y-m-d H:i:s');

        $getRelation = DB::table('relation_campaign_users')
            ->where('id_company_campaign', $idCompanyCampaign)
            ->where('id_employee', $idEmployee)
            ->first();

        if(!$getRelation){
            $data = [
                'id_company_campaign'   => $idCompanyCampaign,
                'id_employee'           => $idEmployee,
                'id_company'            => $idCompany,
                'created_by'            => $idUser,
                'creation_date'         => $today
            ];
            $insert = DB::table('relation_campaign_users')->insert($data);
        }
        return true;
    }
}
