<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use Illuminate\Http\Request;
use App\Models\TimeAttendance\Attendance\Attendance;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\Employee\EmployeeLeave;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveType;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use Illuminate\Support\Facades\Storage;
use App\Models\Home;
use App\Models\Curl;
use Carbon\Carbon;
use DB;
use DataTables;


class DashboardUserController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->Home     = new Home;
	}

    public function employee($id_employee=null){
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_company', session('id_company'))
                    ->where('he.status', 'A');
        if($id_employee){
            $employee->where('he.id_employee', $id_employee);
        } else {
            $employee->where('he.id_user', session('id_user'));
        }
        $get = $employee->first();
	//	dd($get);
        return $get;
    }
	
	public function employee_hi($id_employee=null){
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.status', 'A');
        if($id_employee){
            $employee->where('he.id_employee', $id_employee);
        } else {
            $employee->where('he.id_user', session('id_user'));
        }
        $get = $employee->first();
	//	dd($get);
        return $get;
    }

    public function index()
    {			
        $mapboxToken    = AttendanceController::getTokenMapbox();
        $attendance = Attendance::getAttendance([
            'id_employee' => @$this->employee()->id_employee
        ]);
        $employee = @$this->employee();
        $id_employee = @$employee->id_employee;
        // if($attendance == null){
        //     $logout = url('logout');
        //     echo "<script>alert('Failed generate workdays'); document.location.href = '".$logout."';</script>";
        // }
        // if($attendance && count($attendance) < 1){
        //     $logout = url('logout');
        //     echo "<script>alert('Anda belum bisa melakukan absen'); document.location.href = '".$logout."';</script>";
        // }

        $request_type           = $this->Home->request_type();
        $employee_information   = $this->employee_information();
        $announcement           = $this->announcement();
        $company_policy         = $this->company_policy();
        $upcoming_event         = $this->upcoming_event(@$this->employee()->id_employee);
        $public_course_program  = $this->public_course_program(@$this->employee()->id_employee);
        $news                   = $this->news();
        $attendance_per_month   = $this->attendance_per_month(@$this->employee()->id_employee);
        $attendance_per_year    = $this->attendance_per_year(@$this->employee()->id_employee);
		$mapboxGetPlace 		= Curl::findApi('mapbox_getplace')->url;
        $allEmployee            = $this->employeeAll();
        $url_announcement       = url('employee/employee/announcement');
        $getCampaign            = $this->Home->listCampaign(session('id_company'), @$this->employee()->id_employee);
        $haveAttendanceMenu     = in_array('time_attendance/attendance/attendance' ,MasterUserResponsibility::get_address_menu());
        
        $compact = ['attendance', 'request_type', 'mapboxToken', 'mapboxGetPlace', 'attendance_per_month','attendance_per_year', 'employee_information','announcement','company_policy','upcoming_event', 'news', 'allEmployee', 'url_announcement', 'getCampaign', 'public_course_program', 'id_employee', 'haveAttendanceMenu', 'employee'];

        return view('dashboard.dashboard_user.index', compact($compact));
    }

    public function dashboard_information_user(Request $request)
    {
        switch ($request->method()) {
	        case 'POST':
                $dashboardinformation = $this->Home->dashboardinformation();
                echo json_encode($dashboardinformation);
                break;
            default :
	        	echo 'Invalid request';
	        	break;
        }
    }

    public function employee_information($id_employee=null)
    {
        $id_employee = $id_employee ?? @$this->employee()->id_employee;
        if(!$id_employee){
            return response()->json([]);
        }

        $employee_information = $this->Home->employee_information($id_employee);
		foreach($employee_information as $k => $ei){
			if($ei->current_dates == date('Y-m-d')){
                $employee_information[0] = $ei;
			}				
		}
        return response()->json(@$employee_information[0]);
    }

    public function news($id_employee=null)
    {
        $id_employee = $id_employee ?? @$this->employee()->id_employee;
        $news = $this->Home->listCampaign(session('id_company'), $id_employee);
        return response()->json($news);
    }

    public function announcement()
    {
        $announcement = $this->Home->announcement();
        return response()->json($announcement);
    }

    public function company_policy()
    {
        $company_policy = $this->Home->company_policy();
        return response()->json($company_policy);
    }

    public function upcoming_event($id_employee=null)
    {
        $id_employee = $id_employee ?? @$this->employee()->id_employee;
        if(!$id_employee){
            return response()->json([]);
        }
        
        $upcoming_event = $this->Home->upcoming_event($id_employee);
        if($upcoming_event->count() > 0){
            foreach ($upcoming_event as $k => $val) {
                if($val->event_category == 'Learning'){
                    $countCourseDetail = DB::table('master_course_detail')
                        ->where('id_course_header', $val->id_course_header)
                        ->where('status', 'A')
                        ->pluck('id_course_detail')->unique();
                    $countCourseViewedByEmployee = DB::table('relation_event_course_employee as rece')
                        ->leftJoin('master_course_detail as mcd', 'mcd.id_course_detail', '=', 'rece.id_course_detail')
                        ->where('mcd.id_course_header', $val->id_course_header)
                        ->where('rece.id_employee', $val->id_employee)
                        ->pluck('mcd.id_course_detail')->unique();
                    $diffCourseDetail = $countCourseDetail->diff($countCourseViewedByEmployee);
                    if($diffCourseDetail->count() < 1){
                        $upcoming_event[$k]->status_join = 'done';
                    } else {
                        $endDateCourse = !is_null($val->end_date_attendee) ? $val->end_date_attendee : $val->end_date;
                        $startDateCourse = !is_null($val->start_date_attendee) ? $val->start_date_attendee : $val->start_date;

                        $date = Carbon::parse($endDateCourse)->addDays(1);
                        $now = Carbon::now();
                        $startCourse = Carbon::parse($startDateCourse);
                        $diff = $date->diffInDays($now);
                        if($now->timestamp > $date->timestamp && $countCourseViewedByEmployee->count() < 1){
                            $upcoming_event[$k]->status_join = 'expired';
                        } else {
                            if($startCourse->timestamp > $now->timestamp){
                                $upcoming_event[$k]->status_join = 'expired';
                            } else {
                                $upcoming_event[$k]->status_join = 'available';
                            }
                        }
                    }
                } else {
                    $upcoming_event[$k]->status_join = $upcoming_event[$k]->status;
                }
            }
        }
        return response()->json($upcoming_event);
    }

    public function public_course_program($id_employee=null)
    {
        $public_course_program = $this->Home->public_course_program($id_employee);
        return response()->json($public_course_program);
    }

    public function attendance_per_month($id_employee=null)
    {
        $attendance_per_month = $this->Home->attendance_per_month($id_employee);
        return response()->json($attendance_per_month);
    }

    public function attendance_per_year($id_employee=null)
    {
        $attendance_per_year = $this->Home->attendance_per_year($id_employee);
        return response()->json($attendance_per_year);
    }

    public function employee_by_department(Request $request)
    {
        $id_employee    = @$request->id_employee ?? @$this->employee()->id_employee;
        $path_menu      = $request->uri;

        $employee_by_department = $this->Home->employee_by_department($id_employee, session('id_company'), $path_menu)->sortBy('department')->values();
        return response()->json($employee_by_department);
    }

    public function employee_turnover($id_employee=null)
    {
        $employee_turnover = $this->Home->employee_turnover($id_employee);
        return response()->json($employee_turnover);
    }

    public function employee_contract_expired()
    {
        $employee_contract_expired = $this->Home->employee_contract_expired();
        return DataTables::of($employee_contract_expired)
                    ->addIndexColumn()
                    ->make(true);
    }

    public function leave_information(Request $request)
    {   
        $id_employee = $request->id_employee ?? @$this->employee()->id_employee;
        if(!$id_employee){
            return response()->json(['leave_balance' => [], 'leave_request' => [], 'all_leave' => []]);
        }
        
        $leave_balance  = $this->Home->leave_balance($id_employee);
        $leave_request  = $this->Home->leave_request($id_employee);
        $leave_type     = MasterLeaveType::select('id_leave_type','description', 'leave_code')->where('id_company', session('id_company'))->get();
        $get_all_leave  = EmployeeLeave::get_all_leave($id_employee);
        $leave_by_gender  = EmployeeLeave::get_leave_by_gender();
        $id_female_group  = EmployeeLeave::get_leave_by_gender(session('id_company'), 'MAT')[0]->id_leave_header;

        $cuti_khusus_female = [];
        $cuti_khusus_male   = [];
        $all_leave          = [];
        foreach ($leave_by_gender as $k => $val) {
            if($val->id_leave_header == $id_female_group){ //jika masuk dalam group female
                if(!in_array($val->leave_code, $cuti_khusus_female)){ 
                    $cuti_khusus_female[] = $val->leave_code;
                }
            } else {
                if(!in_array($val->leave_code, $cuti_khusus_male)){ 
                    $cuti_khusus_male[] = $val->leave_code;
                }
            }
        }

        $leaveTypeIsNotShow = ['BPT', 'CHM', 'CST', 'DIF', 'GVL', 'MAT', 'MDL', 'MIS', 'PAT', 'SDC', 'UPL', 'WMC'];
        $thisEmployee = @$this->employee($id_employee);
        foreach ($leave_type as $item) {
            $detail = ['remaining' => '-', 'balance' => '-', 'effective' => '-', 'expired' => '-'];
            foreach ($get_all_leave as $value) {
                if($value->id_leave_type == $item->id_leave_type){
                    $pengurangan = (float)$value->leave_quota - (float)$value->used_leave;
                    $effective = is_null($value->effective_date) ? '-' : Carbon::parse($value->effective_date)->format('d M Y');
                    $expired = in_array($value->leave_code, $leaveTypeIsNotShow) ? '-' : (is_null($value->expired_date) ? '-' : Carbon::parse($value->expired_date)->format('d M Y'));
                    $detail = [
                        'remaining' => ($pengurangan < 0) ? 0 : $pengurangan, //remaining
                        'balance'   => (float)$value->leave_quota,
                        'effective' => $effective,
                        'expired'   => $expired,
                    ];

                    if(@$thisEmployee->gender == 'M'){ // JIka karyawan laki2 maka tampilkan cuti yg dikhususkan utk laki2
                        if(in_array($item->leave_code, $cuti_khusus_male)){ 
                            $all_leave[] = [
                                'id'        => $item->id_leave_type,
                                'type'      => $item->description,
                                'remaining' => $detail['remaining'],
                                'balance'   => $detail['balance'],
                                'effective' => $detail['effective'],
                                'expired'   => $detail['expired'],
                            ];
                        }
                    } else {
                        if(in_array($item->leave_code, $cuti_khusus_female)){ 
                            $all_leave[] = [
                                'id'        => $item->id_leave_type,
                                'type'      => $item->description,
                                'remaining' => $detail['remaining'],
                                'balance'   => $detail['balance'],
                                'effective' => $detail['effective'],
                                'expired'   => $detail['expired'],
                            ];
                        }
                    }
                }
            }
        }
        $sort = array_column($all_leave, 'type');
        array_multisort($sort, SORT_ASC, $all_leave);

        return response()->json(['leave_balance' => $leave_balance, 'leave_request' => $leave_request, 'all_leave' => $all_leave, 'employee' => $thisEmployee]);
    }

    public function getEmployeeByAccessGroup(Request $request)
    {
        $path_menu      = $request->uri;
        $id_branch      = [];
        $id_employee    = [];

        if(session('access_group') == 'Default_Manager'){
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)[0];
            $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
            if(count($get_branch) > 0){
                $id_branch        = $get_branch->pluck('id_branch')->all();
            }
            if(count($id_branch) > 0){
                $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
            }
        }
        $getEmployee = $this->Home->getEmployeePositionAndRegion(null, $id_employee, $id_branch);
        $lisEmployee = [];
        $countEmployee = [];
        foreach ($getEmployee as $key => $val) {
            if(!in_array($val->id_employee, $countEmployee)){
                $countEmployee[] = $val->id_employee;
                $lisEmployee[] = $getEmployee[$key];
            }
        }
        return DataTables::of($lisEmployee)
                    ->addIndexColumn()
                    ->addColumn('action', function($data) {})
                    ->make(true);
    }

    public function birthday(Request $request)
    {
        $path_menu      = $request->uri;
        $id_branch      = [];
        $id_employee    = [];
        $viewBirthday   = [];

        if(session('access_group') == 'Default_Manager'){
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)[0];
            $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
            if(count($get_branch) > 0){
                $id_branch        = $get_branch->pluck('id_branch')->all();
            }
            if(count($id_branch) > 0){
                $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
            }
        }
        $birthday = $this->Home->birthday(null, $id_employee);

        foreach ($birthday as $key => $item) {
            $diff = date_diff(date_create(date('Y-m-d')), date_create(date('Y-m-t')))->days;
            if($diff > 0){
                $theDay = date('m-d', strtotime($item->birthdate));
                $today = date('d');
                for ($i=1; $i <= $diff; $i++) { 
                    $filterDate[] = date('m-').($today + $i);
                }
                if(in_array($theDay, $filterDate)){
                    $viewBirthday[] = $birthday[$key];
                }
            } else {
                $viewBirthday[] = $birthday[$key];
            }
        }
        $v_birth = collect($viewBirthday)->sortBy(function ($employee, $key) {
            return date('d', strtotime($employee->birthdate)) ;
        })->values();

        return response()->json($v_birth);
    }

    public function hierarchy(Request $request)
    {
        $allData = [];
        $thisSup1 = [];
        $superior1 = null;
        $superior2 = null;
        $employee = Employee::getEmployeeDetail(null, @$this->employee_hi()->id_employee, true, true);
        if(!$employee || count($employee) < 1) {
            throw new \Exception("Employee not found");
        }
        $directIdEmployee = @$employee[0]->direct_id_employee;
        $directEmployeeName = @$employee[0]->direct_employee_name;
        $directIdPositionDetail = @$employee[0]->parent_id_position_detail;
        $indirectIdEmployee = @$employee[0]->indirect_id_employee;
        $indirectEmployeeName = @$employee[0]->indirect_employee_name;
        $directIdPositionDetailFromSuperior1 = null;
        
        //=============== CEK HIERARCHY JIKA ADA BAWAHAN
        $subordinate = Employee::getPositionDetail(null, @$employee[0]->id_position_detail, null, true);
        if($subordinate->count() < 1){
            $subordinate = null;
            $thisSubordinate = null;
        } 
        else {
            $hierarchySub = [];
            $allIdEmployeeSub = $subordinate->pluck('id_employee')->all();
            $wdSub = DB::table('hr_work_days')
                ->whereIn('id_employee', $allIdEmployeeSub)
                ->where('current_dates', date('Y-m-d'))->get();

            $wdSub = $wdSub->keyBy(function ($item) {
                return $item->id_employee;
            });
            foreach ($subordinate as $key => $val) {
                $hierarchyThisSub = [
                    'id' => $val->id_position_detail,
                    'name' => $val->position_routing,
                    'title' => $val->name,
                    'child_id' => $val->parent_id_position_detail,
                    'className' => $val->job_class_group,
                    'image_attachment' => $val->image_attachment,
                    'actual_time_in' => @$wdSub[$val->id_employee]->actual_time_in,
                    'actual_time_out' => @$wdSub[$val->id_employee]->actual_time_out,
                    'current_latitude_in' => @$wdSub[$val->id_employee]->current_latitude_in,
                    'current_longitude_in' => @$wdSub[$val->id_employee]->current_longitude_in,
                    'current_latitude_out' => @$wdSub[$val->id_employee]->current_latitude_out,
                    'current_longitude_out' => @$wdSub[$val->id_employee]->current_longitude_out,
                    'att_level' => '1'
                ];
                $hierarchySub[] = $hierarchyThisSub;
            }
            $thisSubordinate = $hierarchySub;
        }

        //=============== HIERARCHY TEMPAT KARYAWAN YANG LOGIN
        $wdThisEmployee = DB::table('hr_work_days')
                ->where('id_employee', $employee[0]->id_employee)
                ->where('current_dates', date('Y-m-d'))->first();
        $myData = [
            'id' => @$employee[0]->id_position_detail,
            'name' => @$employee[0]->position_routing,
            'title' => @$employee[0]->name,
            'child_id' => @$employee[0]->parent_id_position_detail,
            'className' => @$employee[0]->job_class_group,
            'image_attachment' => @$employee[0]->image_attachment,
            'actual_time_in' => @$wdThisEmployee->actual_time_in,
            'actual_time_out' => @$wdThisEmployee->actual_time_out,
            'current_latitude_in' => @$wdThisEmployee->current_latitude_in,
            'current_longitude_in' => @$wdThisEmployee->current_longitude_in,
            'current_latitude_out' => @$wdThisEmployee->current_latitude_out,
            'current_longitude_out' => @$wdThisEmployee->current_longitude_out,
            'att_level' => '1'
        ];
        if($thisSubordinate){
            $myData['children'] = $thisSubordinate;
        }
        $thisMyData[] = $myData;
        $allData = $myData;


        //=============== CEK HIERARCHY ATASAN LANGSUNG
        if($directIdPositionDetail){
            $superior1 = Employee::getPositionDetail($directIdPositionDetail, null, null, true);
            $directIdPositionDetailFromSuperior1 = @$superior1[0]->parent_id_position_detail;
            $wdSup1 = DB::table('hr_work_days')
                ->where('id_employee', @$superior1[0]->id_employee)
                ->where('current_dates', date('Y-m-d'))->first();

            $hierarchySup1 = [
                'id' => @$superior1[0]->id_position_detail,
                'name' => @$superior1[0]->position_routing,
                'title' => @$superior1[0]->name,
                'child_id' => @$superior1[0]->parent_id_position_detail,
                'className' => @$superior1[0]->job_class_group,
                'image_attachment' => @$superior1[0]->image_attachment,
                'actual_time_in' => @$wdSup1->actual_time_in,
                'actual_time_out' => @$wdSup1->actual_time_out,
                'current_latitude_in' => @$wdSup1->current_latitude_in,
                'current_longitude_in' => @$wdSup1->current_longitude_in,
                'current_latitude_out' => @$wdSup1->current_latitude_out,
                'current_longitude_out' => @$wdSup1->current_longitude_out,
                'att_level' => '0'
            ];
            if(@$this->employee_hi()->id_employee == @$superior1[0]->id_employee){
                $hierarchySup1['att_level'] = '1';
            }
            $hierarchySup1['children'] = $thisMyData;
            $thisSup1[] = $hierarchySup1;
            $allData = $hierarchySup1;
        }


        //=============== CEK HIERARCHY ATASAN TIDAK LANGSUNG
        if($directIdPositionDetailFromSuperior1){
            $superior2 = Employee::getPositionDetail($directIdPositionDetailFromSuperior1, null, null, true);
            $wdSup2 = DB::table('hr_work_days')
                ->where('id_employee', @$superior2[0]->id_employee)
                ->where('current_dates', date('Y-m-d'))->first();

            $hierarchySup2 = [
                'id' => @$superior2[0]->id_position_detail,
                'name' => @$superior2[0]->position_routing,
                'title' => @$superior2[0]->name,
                'child_id' => @$superior2[0]->parent_id_position_detail,
                'className' => @$superior2[0]->job_class_group,
                'image_attachment' => @$superior2[0]->image_attachment,
                'actual_time_in' => @$wdSup2->actual_time_in,
                'actual_time_out' => @$wdSup2->actual_time_out,
                'current_latitude_in' => @$wdSup2->current_latitude_in,
                'current_longitude_in' => @$wdSup2->current_longitude_in,
                'current_latitude_out' => @$wdSup2->current_latitude_out,
                'current_longitude_out' => @$wdSup2->current_longitude_out,
                'att_level' => '0'
            ];
            if(@$this->employee_hi()->id_employee == @$superior2[0]->id_employee){
                $hierarchySup2['att_level'] = '1';
            }
            $hierarchySup2['children'] = $thisSup1;
            $allData = $hierarchySup2;
        }
        return response()->json($allData);
    }

    public function hierarchy_old()
    {
        $sql_pos = "SELECT mpd.id_position_detail
                    FROM master_position_detail mpd
                    JOIN hr_employee he
                    ON mpd.id_employee = he.id_employee
                    WHERE mpd.status = 'A' AND he.id_user =". session('id_user');
        $source_pos = DB::select($sql_pos);
        $quechart = [];

        if(!empty($source_pos)){
            @$fill = $source_pos[0]->id_position_detail;
            $sql = "SELECT  mpd.id_position_detail as id,  mpr.description as name,  
                        mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                        mjg.job_class_group as classname,  he.name as title, he.image_attachment
                FROM master_position_detail mpd
                INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
                LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
                INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
                WHERE  (mpd.id_company=". session('id_company')." or mpd.assigned_to_company=". session('id_company').") and mpd.status = 'A'  
                and mpd.id_position_detail =".@$fill." or mpd.parent_id_position_detail =".@$fill;
            $source = DB::select($sql);
            $sou = array();
            foreach ($source as $s) {
                if($s->image_attachment != null){
                    if(strlen($s->image_attachment) > 1000){
                        $s->image_attachment = "data:image;base64,".$s->image_attachment;
                    } else {
                        $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                        if (Storage::exists($urlPhoto)) {
                            $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                        } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                    }
                } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }

                $sou [$s->id]['id'] = $s->id;
                $sou [$s->id]['name'] = $s->name;
                $sou [$s->id]['title'] = $s->title;
                $sou [$s->id]['child_id'] = $s->child_id;
                //    $sou [$s->id]['collapsed'] = $s->child_id != NULL ? true : false;    
                $sou [$s->id]['className'] = $s->classname;
				$sou [$s->id]['image_attachment'] = $s->image_attachment;
            }
			
            foreach($source as $sc){
                if($sc->id == @$fill){
                    $x[] = $sc->child_id;
                }       
            }

            if(isset($x[0]) != null){   
                $sql2 = "SELECT  mpd.id_position_detail as id,  mpr.description as name,  
                                mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                                mjg.job_class_group as classname,  he.name as title, he.image_attachment,
								NULL::date as actual_time_in, NULL::date as actual_time_out,
								NULL::double precision as current_latitude_in, NULL::double precision as current_longitude_in,
								NULL::double precision as current_latitude_out, NULL::double precision as current_longitude_out,
								'0' as att_level
                        FROM master_position_detail mpd
                        INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
                        LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
                        INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
						LEFT JOIN hr_work_days hwd ON he.id_employee = hwd.id_employee AND hwd.current_dates = CURRENT_DATE
                        WHERE mpd.status = 'A'  
                        AND mpd.id_position_detail = COALESCE(".$x[0].",mpd.id_position_detail)
				UNION ALL
				SELECT  mpd.id_position_detail as id,  mpr.description as name,  
                                mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
                                mjg.job_class_group as classname,  he.name as title, he.image_attachment,
								hwd.actual_time_in, hwd.actual_time_out, 
								hwd.current_latitude_in, hwd.current_longitude_in,
								hwd.current_latitude_out, hwd.current_longitude_out,
								'1' as att_level
                        FROM master_position_detail mpd
                        INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
                        LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
                        INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
						LEFT JOIN hr_work_days hwd ON he.id_employee = hwd.id_employee AND hwd.current_dates = CURRENT_DATE
                        WHERE  (mpd.id_company=". session('id_company')." or mpd.assigned_to_company=". session('id_company').") and mpd.status = 'A'  
                        and mpd.id_position_detail = COALESCE(".@$fill.",mpd.id_position_detail) or mpd.parent_id_position_detail = COALESCE(".@$fill.",mpd.id_position_detail)";
                $source2 = DB::select($sql2);
                
                $sou = array();
                foreach ($source2 as $s) {
                    if($s->image_attachment != null){
                        if(strlen($s->image_attachment) > 1000){
                            $s->image_attachment = "data:image;base64,".$s->image_attachment;
                        } else {
                            $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                            if (Storage::exists($urlPhoto)) {
                                $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                        }
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }

                    $sou [$s->id]['id'] = $s->id;
                    $sou [$s->id]['name'] = $s->name;
                    $sou [$s->id]['title'] = $s->title;
                    $sou [$s->id]['child_id'] = $s->child_id;
                    //    $sou [$s->id]['collapsed'] = $s->child_id != NULL ? true : false;    
                    $sou [$s->id]['className'] = $s->classname;
					$sou [$s->id]['image_attachment'] = $s->image_attachment;
					$sou [$s->id]['actual_time_in'] = $s->actual_time_in;
					$sou [$s->id]['actual_time_out'] = $s->actual_time_out;
					$sou [$s->id]['current_latitude_in'] = $s->current_latitude_in;
					$sou [$s->id]['current_longitude_in'] = $s->current_longitude_in;
					$sou [$s->id]['current_latitude_out'] = $s->current_latitude_out;
					$sou [$s->id]['current_longitude_out'] = $s->current_longitude_out;
					$sou [$s->id]['att_level'] = $s->att_level;
                }       
                foreach($source2 as $sc2){
                    if($sc2->id == $x[0]){
                        $y[] = $sc2->child_id;
                    }       
                }
            }

            if(isset($y[0]) != null){   
                $sql3 = "SELECT  mpd.id_position_detail as id,  mpr.description as name,  
		case when row_number() over() = 1
			 then	null
			 else mpd.parent_id_position_detail
			 end as child_id,  mpd.status as collapsed,  
		mjg.job_class_group as classname,  he.name as title, he.image_attachment,
		NULL::date as actual_time_in, NULL::date as actual_time_out,
		NULL::double precision as current_latitude_in, NULL::double precision as current_longitude_in,
		NULL::double precision as current_latitude_out, NULL::double precision as current_longitude_out,
		'0' as att_level
FROM master_position_detail mpd
INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
WHERE mpd.status = 'A'  
and mpd.id_position_detail = COALESCE(".$y[0].",mpd.id_position_detail)
UNION ALL
SELECT  mpd.id_position_detail as id,  mpr.description as name,  
		mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
		mjg.job_class_group as classname,  he.name as title, he.image_attachment,
		NULL::date as actual_time_in, NULL::date as actual_time_out, 
		NULL::double precision as current_latitude_in, NULL::double precision as current_longitude_in,
		NULL::double precision as current_latitude_out, NULL::double precision as current_longitude_out,
		'0' as att_level
FROM master_position_detail mpd
INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
WHERE mpd.status = 'A'  
and mpd.id_position_detail = COALESCE(".$x[0].",mpd.id_position_detail)
UNION ALL
SELECT  mpd.id_position_detail as id,  mpr.description as name,  
		mpd.parent_id_position_detail as child_id,  mpd.status as collapsed,  
		mjg.job_class_group as classname,  he.name as title, he.image_attachment,
		hwd.actual_time_in, hwd.actual_time_out, 
		hwd.current_latitude_in, hwd.current_longitude_in,
		hwd.current_latitude_out, hwd.current_longitude_out,
		'1' as att_level
FROM master_position_detail mpd
INNER JOIN  master_position_routing mpr ON  mpd.id_position_routing =  mpr.id_routing
LEFT JOIN  hr_employee he ON  mpd.id_employee =  he.id_employee
INNER JOIN  master_job_grade mjg ON  mpr.id_job_grade =  mjg.id_job_grade
LEFT JOIN hr_work_days hwd ON he.id_employee = hwd.id_employee AND hwd.current_dates = CURRENT_DATE
WHERE  (mpd.id_company=". session('id_company')." or mpd.assigned_to_company=". session('id_company').") and mpd.status = 'A'  
and mpd.id_position_detail = COALESCE(".$fill.",mpd.id_position_detail) or mpd.parent_id_position_detail = COALESCE(".$fill.",mpd.id_position_detail)";
                $source3 = DB::select($sql3);
                
                $sou = array();
                foreach ($source3 as $s) {
                    if($s->image_attachment != null){
                        if(strlen($s->image_attachment) > 1000){
                            $s->image_attachment = "data:image;base64,".$s->image_attachment;
                        } else {
                            $urlPhoto = 'public/upload/photo/'. $s->image_attachment;
                            if (Storage::exists($urlPhoto)) {
                                $s->image_attachment = url('project/storage/app/'.$urlPhoto);
                            } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }
                        }
                    } else { $s->image_attachment = asset('public/global/img/avatar.jpg'); }

                    $sou [$s->id]['id'] = $s->id;
                    $sou [$s->id]['name'] = $s->name;
                    $sou [$s->id]['title'] = $s->title;
                    $sou [$s->id]['child_id'] = $s->child_id;
                    //    $sou [$s->id]['collapsed'] = $s->child_id != NULL ? true : false;    
                    $sou [$s->id]['className'] = $s->classname;
					$sou [$s->id]['image_attachment'] = $s->image_attachment;
					$sou [$s->id]['actual_time_in'] = $s->actual_time_in;
					$sou [$s->id]['actual_time_out'] = $s->actual_time_out;
					$sou [$s->id]['current_latitude_in'] = $s->current_latitude_in;
					$sou [$s->id]['current_longitude_in'] = $s->current_longitude_in;
					$sou [$s->id]['current_latitude_out'] = $s->current_latitude_out;
					$sou [$s->id]['current_longitude_out'] = $s->current_longitude_out;
					$sou [$s->id]['att_level'] = $s->att_level;
                }
            }   
        //    dd($sou);
            function makeNested($sou) {
                $nested = array();
                foreach ($sou as &$s) {
                    if (is_null($s['child_id'])) {
                        $nested = &$s;
                    } else {
                        $pid = $s['child_id'];
                        if (isset($sou[$pid])) {
                            if (!isset($sou[$pid])) {
                                $sou[$pid]['children'] = array();
                            }
                            $sou[$pid]['children'][] = &$s;
                        }
                    }
                }
                return $nested;
            }
            $quechart = makeNested($sou);
        } 
        return response()->json($quechart);
    }

    public function employeeAll(){
        $default_company = null;
        if(session('company_type') == 'os'){
            $getUser = DB::table('master_users as mu')->select('mu.default_company')->where('mu.user_name', session('username'))->first();
            $default_company = $getUser->default_company;
            if($default_company == session('id_company')){
                $getOther = DB::table('hr_employee as he')
                        ->join('master_users as mu', 'mu.user_name', '=', 'he.nik_employee')
                        ->select("mu.default_company")
                        ->where('mu.default_company', '!=', session('id_company'))
                        ->where('he.id_employee', session('id_company'))
                        ->limit(2)
                        ->get();
                if($getOther){
                    $default_company = $getOther->pluck('default_company')->unique();
                }
            }
        }
        $department     = WorkDays::getdepartment($default_company);
        $get_employee   = WorkDays::getemployeename();
        $employee       = [];

        foreach ($department as $key => $value) {
            $child = [];
            if(count($get_employee) > 0){
                foreach ($get_employee as $k => $item) {
                    if($value->id_dept == $item->id_dept){
                        $child[] = [
                            'id'        => $item->id_employee,
                            'text'      => $item->name,
                        ];
                    }
                }
            }
            $employee[] = [
                'id'        => $value->id_dept,
                'text'      => $value->description,
                'children'  => $child
            ];
        }
        return json_encode($employee);
    }

    public function employeePositionAll($id_employee){
        $avatar = asset('public/global/img/avatar.jpg');
        $getEmployeeDetail = Employee::getEmployeeDetail(null, $id_employee);
        unset($getEmployeeDetail[0]->private_mail);
        unset($getEmployeeDetail[0]->mobile_phone);
        $return = $getEmployeeDetail[0];

        return response()->json($return);
    }

    public function attendanceStatus(){
        $start_month = date('Y-m-01');
        $end_month = date('Y-m-t');
        $start_year = date('Y-01-01');
        $end_year = date('Y-12-31');

        $getAttendanceStatus  = WorkDays::getAttendanceStatus(@$this->employee()->id_employee, $start_year, $end_year);

        return response()->json($getAttendanceStatus);
    }
}
