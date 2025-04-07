<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\MasterOrganization\MasterBranch;
use App\Models\Organization\MasterOrganization\MasterRegional;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Curl;
use Validator;
use DataTables;
use DB;
use DatePeriod;
use DateTime;
use DateInterval;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class WorkDaysController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Jakarta');
        $this->WorkDays = new WorkDays;
    }
    
    private function employee(){
        $employee = DB::table('hr_employee as he')
                    ->select('he.id_employee', 'he.gender', 'he.nik_employee')
                    ->where('he.id_user', session('id_user'))
                    ->where('he.id_company', session('id_company'))
                    ->where('he.status', 'A')
                    ->first();
        return $employee;
    }

    public function index(Request $request)
    {
        ini_set('max_execution_time', -1);
        $path_menu_param    = $request->path();
        $path_menu          = @$request->path_menu;
        $myIdEmployee       = @$this->employee()->id_employee;
        $myNik              = @$this->employee()->nik_employee;
        $default_company    = null;

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

        $getdepartment      = $this->WorkDays->getdepartment($default_company);
        $get_employee       = $this->WorkDays->getemployeename([], ['A']);
        $accessGroup        = session('access_group');
        $branchByManager    = [];
        $id_branch          = [];
        $id_region          = [];

        if(session('access_group') != 'Default_Administrator'){
            //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
            $address = ['time_attendance/attendance/attendance_list', 'employee/employee_setting/workdays'];
            // if(in_array($path_menu, $address)){
                $path_menu_ = 'employee/employee_setting/workdays';
            // }
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
            if($get_ur->count() > 0){
                $get_branch = [];
                if((session('company_type') == 'os' && (session('access_group')=='Default_User' || session('access_group')=='Default_Manager')) || session('company_type') != 'os' ){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                }
                if(count($get_branch) > 0){
                    $branchByManager  = $get_branch;
                    $id_branch        = $branchByManager;
                } else {
                    $branchByManager  = 'null';
                }
                if(count($id_branch) > 0){
                    $id_region  = MasterBranch::whereIn('id_branch', $id_branch)->pluck('id_region')->unique()->toArray();
                }
            } 
        }
        // dd($id_region, $id_branch);

        if ($request->ajax()) {
            $status             = $request->status ?? ['A', 'I'];
            $nik                = $request->employeename ?? 'null';
            $startdate          = $request->startdate  ?? 'null';
            $department         = $request->department  ?? 'null';
            $enddate            = $request->enddate  ?? 'null';
            $regional           = $request->regional  ?? 'null';
            $location           = $request->location  ?? 'null';
            $id_company         = session('id_company');

            if($nik != 'null'){
                $getIdEmployee = DB::table('hr_employee as he')
                    ->select("he.id_employee");

                if($path_menu == 'employee/employee_setting/workdays'){
                    $getIdEmployee->where('he.id_company', session('id_company'));
                }
                if(is_array($nik)){
                    $getIdEmployee->whereIn('he.nik_employee', $nik);
                } else {
                    $getIdEmployee->where('he.nik_employee', $nik); 
                }
                $getIdEmployee->whereIn('he.status', $status); 
                $id_employee = $getIdEmployee->get()->pluck('id_employee')->all();
            } 
            else {
                $id_employee = 'null';
            }

            if(is_array($request->branch) && count($request->branch)>0 ){
                $branch     = $request->branch;
            } else {
                if(session('access_group') != 'Default_Administrator'){
                    $branch = $branchByManager;
                } else {
                    $branch     = 'null';
                }
            }

            $data = $this->WorkDays->listData($id_employee, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }

        $mapboxToken    = AttendanceController::getTokenMapbox();
        $getregional    = $this->WorkDays->getregional($id_region);
        $getlocation    = $this->WorkDays->getlocation();
        $getbranch      = $this->WorkDays->getbranch();
        $mapboxGetPlace = Curl::findApi('mapbox_getplace')->url;

        return view('employee.employee_setting.workdays.index', compact('get_employee', 'myIdEmployee', 'getdepartment','getregional','getlocation','getbranch','mapboxToken','path_menu_param','mapboxGetPlace','accessGroup','branchByManager', 'myNik'));
    }
    
    public function get_employee_by_status_and_access_group(Request $request){
        if($request->status=='null'){
            $status = ['A','I'];
        } else if(strpos($request->status, ',')!==false){
            $status = explode(',', $request->status);
        } else {
            $status = [$request->status];
        }

        $path_menu      = $request->path_menu ?? null ;
        $returnId       = $request->return_id ?? 'nik_employee';
        $id_region      = [];
        $employee       = [];
        $id_branch      = [];
        $myIdEmployee   = @$this->employee()->id_employee;
        $default_company= null;

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

        $getdepartment  = $this->WorkDays->getdepartment($default_company);

        if(session('access_group') == 'Default_Manager' || session('access_group') == 'Default_User'){
            $address = ['time_attendance/attendance/attendance_list', 'employee/employee_setting/workdays'];
            if(in_array($path_menu, $address)){
                $path_menu = 'employee/employee_setting/workdays';
            }
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)->pluck('id_user_responsibility');
            if($get_ur->count() > 0){
                $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                if(count($get_branch) > 0){
                    $id_branch        = $get_branch;
                }
            } 

            if(count($id_branch) > 0){
                $idTermination = DB::table('master_general_data as mgd')
                    ->where('mgd.id_general_type', 6)
                    ->where('mgd.code', 'Termination')
                    ->where('mgd.status', 'A')
                    ->pluck('id_general_data');

                $positionDetailByBranch = JobPositionDetail::whereIn('id_branch', $id_branch)->orderBy('id_employee');
                $idPositionDetailByBranch = $positionDetailByBranch->pluck('id_position_detail');

                $id_employee = $positionDetailByBranch->pluck('id_employee')->filter();
                $id_employee2 = JobPositionDetail::whereIn('id_branch', $id_branch)->orderBy('id_employee2')->pluck('id_employee2')->filter();

                $idEmployeeInactiveByBranch = DB::table('hr_career_transaction as hct')
                    ->whereIn('hct.id_old_position_detail', $idPositionDetailByBranch)
                    ->whereIn('hct.id_transaction_type', $idTermination)
                    ->pluck('id_employee')->filter();

                $mergedId = $id_employee->merge($id_employee2)->merge($idEmployeeInactiveByBranch)->all();
                $get_employee   = $this->WorkDays->getEmployeeAll($mergedId, $status);
            } else {
                $get_employee   = $this->WorkDays->getEmployeeAll([], $status);
            }
            foreach ($get_employee as $k => $item) {
                $employee[] = [
                    'id'        => $item->$returnId,
                    'text'      => $item->name.' ('.$item->nik_employee.') ('.$item->status.')',
                ];
            }
        } else {
            $get_employee   = $this->WorkDays->getEmployeeAll([], $status);
            foreach ($get_employee as $k => $item) {
                $employee[] = [
                    'id'        => $item->$returnId,
                    'text'      => $item->name.' ('.$item->nik_employee.') ('.$item->status.')',
                ];
            }
        }
        return response()->json($employee);
    }

    public function getBranch(Request $request){
        $id_region = $request->id_region ?? null ;
        $path_menu = [$request->path_menu] ?? [] ;
        $id_branch = $request->id_branch ?? [];

        if(session('access_group') != 'Default_Administrator'){
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)->pluck('id_user_responsibility');
            if($get_ur->count() > 0){
                $get_branch = [];
                if((session('company_type') == 'os' && session('access_group') == 'Default_User') || session('company_type') != 'os' ){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                }
                if(count($get_branch) > 0){
                    $id_branch        = $get_branch;
                }
            } 
        }
        $getBranch = $this->WorkDays->getbranch($id_region, $id_branch);
        return $getBranch;
    }

    public function getLocation(Request $request){
        $id_branch      = $request->id_branch ?? null ;
        $getlocation    = $this->WorkDays->getlocation($id_branch);
        return $getlocation;
    }

    public function getdata(Request $request)
    {
        switch ($request->method()) {
            case 'POST':
                $employeename   = $request->employeename ?? 'null';
                $startdate      = $request->startdate  ?? 'null';
                $department     = $request->department  ?? 'null';
                $enddate        = $request->enddate  ?? 'null';
                $regional       = $request->regional  ?? 'null';
                $location       = $request->location  ?? 'null';
                $branch         = $request->branch  ?? 'null';
                
                $getdata=$this->WorkDays->getdata($employeename,$startdate,$department,$enddate,$regional,$location,$branch);
                $data=[];
                
                foreach($getdata['data'] as $rowgetdata)
                {
                    $data[]=array(
                        "id_employee"=>$rowgetdata->id_employee,
                        "id_workdays"=>$rowgetdata->id_workdays,
                        "date"=>$rowgetdata->current_dates,
                        "employee"=>$rowgetdata->employee_name,
                        "shift"=>$rowgetdata->shift,
                        "actual_time_in"=>$rowgetdata->actual_time_in,
                        "actual_time_out"=>$rowgetdata->actual_time_out,
                        "day_type"=>$rowgetdata->day_type,
                        "schedule_time_in"=>$rowgetdata->schedule_time_in,
                        "late_in"=>$rowgetdata->late_in,
                        "schedule_time_out"=>$rowgetdata->schedule_time_out,
                        "early_out"=>$rowgetdata->early_out,
                        "current_employee_timezone"=>$rowgetdata->current_employee_timezone,
                        "work_hours"=>$rowgetdata->work_hours,
                        "overtime"=>$rowgetdata->overtime,
                        "current_name_in"=>$rowgetdata->current_name_in,
                        "current_address_in"=>$rowgetdata->current_address_in,
                        "current_name_out"=>$rowgetdata->current_name_out,
                        "current_address_out"=>$rowgetdata->current_address_out,
                        "note"=>$rowgetdata->note
                        // "action"=>'<button type="button" class="btn btn-sm btn-warning edit" id="edit_'.$rowgetdata->id_employee.'|'.$rowgetdata->id_workdays.'"><i class="fa fa-pencil"></i></button>'
                    );
                }
                
                $results = array(
                    "echo" => 1,
                    "totalRecords" => count($data),
                    "data"=>$data);
                echo json_encode($results);
                break;
            default :
                echo 'Invalid request';
                break;
        }
    }
    
    public function edit(Request $request)
    {
        switch ($request->method()) {
            case 'POST':
                $id_employee=strip_tags(base64_decode($request->post('id_employee')));
                $id_workdays=strip_tags(base64_decode($request->post('id_workdays')));
                $actual_time_in=strip_tags(base64_decode($request->post('actual_time_in')));
                $actual_time_out=strip_tags(base64_decode($request->post('actual_time_out')));
                
                $editdata=$this->WorkDays->editdata($id_employee,$id_workdays,$actual_time_in,$actual_time_out);
                echo json_encode($editdata['message']);
                break;
            default :
                echo 'Invalid request';
                break;
        }
    }

    public function subordinate(Request $request)
    {   
        $mpd = DB::table('master_position_detail as mpd')
            ->where('mpd.id_employee', @$this->employee()->id_employee)
            ->where('mpd.id_company', session('id_company'))
            ->orWhere('mpd.id_employee2', @$this->employee()->id_employee)->first();
        $employee = [];
        if(!$mpd){
            $get_employee = $this->WorkDays->getEmployeeAll(@$this->employee()->id_employee, ['A']);
            foreach ($get_employee as $k => $item) {
                $employee[] = [
                    'id'        => $item->nik_employee,
                    'text'      => $item->name.' ('.$item->nik_employee.')',
                ];
            }
        } else {
            $idPositionDetail = $mpd->id_position_detail;
            $subordinate = $this->WorkDays->subordinate($idPositionDetail);
            foreach ($subordinate as $key => $val) {
                if(!is_null($val->nik_employee)){
                    //Jika Posisi bawahan langsung terdapat employee yg mengisi
                    $employee[] = [
                        'id'        => $val->nik_employee,
                        'text'      => $val->name.' ('.$val->nik_employee.')',
                    ];
                } else {
                    //Jika Posisi bawahan langsung tidak terdapat employee, maka cari employee dibawah posisi yg kosong ini
                    $subordinate_lev2 = $this->WorkDays->subordinate($val->id_position_detail);
                    if(count($subordinate_lev2) > 0){
                        foreach ($subordinate_lev2 as $k => $item) {
                            if(!is_null($item->nik_employee)){
                                $employee[] = [
                                    'id'        => $item->nik_employee,
                                    'text'      => $item->name.' ('.$item->nik_employee.')',
                                ];
                            }
                        }
                    }
                }
            }
        }
        return $employee;
    }

    public function _export_2(Request $request) {
        ini_set('max_execution_time', -1);

        $start              = $request->startdate ?? 'null';
        $end                = $request->enddate ?? 'null';
        $nik                = $request->employeename ?? 'null';
        $regional           = $request->regional  ?? 'null';
        $branch             = $request->branch ?? 'null';
        $location           = $request->location  ?? 'null';
        $department         = $request->department  ?? 'null';
        $path_menu          = $request->path_menu  ?? 'null';
        $id_company         = session('id_company');
        $branchByManager    = [];

        if($nik != 'null'){
            if(strpos($nik, ',') !== false){
                $nik = explode(',', $nik);
            } else {
                $nik = [$nik];
            }
        }

        if($request->status != 'null'){
            if(strpos($request->status, ',') !== false){
                $status = explode(',', $request->status);
            } else {
                $status = [$request->status];
            }
        } else {
            $status = ['A', 'I'];
        }

        if($id_company != 'null'){
            if(strpos($id_company, ',') !== false){
                $id_company = explode(',', $id_company);
            } else {
                $id_company = [$id_company];
            }
        }
        
        if($branch != 'null'){
            $branch     = $request->branch;
        } else {
            if(session('access_group') != 'Default_Administrator'){
                //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
                $path_menu_ = 'employee/employee_setting/workdays';
                $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
                if($get_ur->count() > 0){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                    if(count($get_branch) > 0){
                        $branchByManager        = $get_branch;
                    }
                } 
                $branch = $branchByManager;
            } else {
                $branch     = 'null';
            }
        }

        $dateRangeName  = date("j M Y", strtotime($start)).' to '.date("j M Y", strtotime($end));
        $period         = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        $attendStatus   = ['ANL','BPT','CHM','CST','DIF','EDO','GVL','MAT','MDL','MIS','PAT','SDC','UPL','WMC','OFF','PRS','ABS','NSI','NSO'];
        $title          = ['No', 'Employee No', 'Employee Name', 'Position', 'Principal', 'Region', 'Organization Unit', 'Department', 'PT', 'Status'];
        $legend         = [
            'ANL'=>'Annual Leave',
            'BPT'=>'Child Baptism',
            'CHM'=>'Employee Child Married',
            'CST'=>'Child Circumcision',
            'DIF'=>'Decease Spouse, Parents & Child (In Law), Siblings',
            'EDO'=>'Extra Day Off',
            'GVL'=>'Deceased of Family',
            'MAT'=>'Maternity Leave',
            'MDL'=>'Employee Married Leave',
            'MIS'=>'Miscarriage Leave',
            'PAT'=>'Employee Child Birth',
            'SDC'=>'Sick',
            'UPL'=>'Unpaid Leave',
            'WMC'=>'Wife Miscarriage',
            'OFF'=>'Off',
            'PRS'=>'Present',
            'ABS'=>'Absent',
            'NSI'=>'No Swipe In',
            'NSO'=>'No Swipe Out',
        ];
        $countTitle     = count($title);
        $getInactive = [];

        $period = new \DatePeriod(
             new \DateTime($start),
             new \DateInterval('P1D'),
             new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        foreach ($period as $k => $v) {
            $dateRange[]        = $v->format("Y-m-d");
            $title[$countTitle] = $v->format("D, d M");
            $datePeriod[] = $v->format("Y-m-d");
            $countTitle++;
        }

        $workDays = DB::table('hr_work_days')
                ->selectRaw("DISTINCT(current_dates) as dates")
                ->whereIn('current_dates', $datePeriod)
                ->where('id_company', '=', session('id_company'))
                ->get();
        if(count($workDays) < 1){
            echo "<script>alert('No Data in this Period');window.close();</script>";
            return false;
        }

        $countTitleAfter = count($title);
        foreach ($attendStatus as $k => $v) {
            $title[$countTitleAfter] = $v;
            $countTitleAfter++;
            $thisVal = $v;
            $$thisVal = []; // value dari array dijadikan nama variabel utk nampung, eg: $OFF,$PRS,$ANL,$UPL
        }

        $dataSheet = [
            [''],
            ['AttendanceStatusReport'],
            ['From '.$dateRangeName],
            [''],
        ];

        $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

        $data = $this->WorkDays->summary($nik, $id_company, $start, $end, $branch, $location, $regional, $department, $status, $path_menu);

        if($data){
            $all_nik        = $data->pluck('nik_employee');
            $all_name       = $data->pluck('name');
            $employeeStatus = [];

            $summary = [];
            $employeePosition = [];
            $employeeByPrinciple = [];

            foreach ($all_nik as $k => $v) {
                if(!in_array($v, $summary)){
                    //penempatan parmeter nik dan nama dahulu karena pasti terdapat di setiap hasil query
                    $summary[$v]['nik_employee'] = @$v;
                    $summary[$v]['name'] = @$all_name[$k];
                    $employeePosition[$v]['A'] = '';
                    $employeePosition[$v]['I'] = '';
                }
            }

            foreach ($data as $k => $val) {
                if($val->nik_employee == $summary[$val->nik_employee]['nik_employee']){
                    // pengecekan utk employee yg memiliki lbh dr 1 principle
                    // if(!in_array($val->nik_employee, $employeeByPrinciple)){
                        $summary[$val->nik_employee]['principal'] = $val->principal;
                    // } else {
                        // $summary[$val->nik_employee]['principal'] .= ', '.$val->principal;
                    // }

                    //penemptan paremeter posisi,unit,status
                    $summary[$val->nik_employee]['position_name'] = $val->position_name;
                    $summary[$val->nik_employee]['region'] = $val->region;
                    $summary[$val->nik_employee]['organization_unit'] = $val->organization_unit;
                    $summary[$val->nik_employee]['department'] = $val->department;
                    $summary[$val->nik_employee]['company'] = $val->company_name;
                    $summary[$val->nik_employee]['employee_status'] = $val->employee_status;

                    if(in_array($val->current_dates, $datePeriod)){
                        //penempatan parameter attendance status sesuai tanggalnya
                        $summary[$val->nik_employee][$val->current_dates] = $val->attendance_status;
                    }
                    $employeeByPrinciple[] = $val->nik_employee;
                    $employeeStatus[$val->nik_employee][] = $val->employee_status;
                    
                    if($val->position_name != @$employeePosition[@$val->nik_employee][@$val->employee_status]){
                        $employeePosition[$val->nik_employee][$val->employee_status] = $val->position_name;
                    }
                }
            }

            $sort = array_column($summary, 'name'); // sort result by name
            array_multisort($sort, SORT_ASC, $summary);
            $rows = [];
            $number = 1;
            $var = '';
            foreach ($summary as $k => $val) {
                $employee_status_summary = in_array('A', $employeeStatus[$val['nik_employee']]) ? 'A' : $val['employee_status'];
                //utk menentukan posisi employee berdasar status employee terakhir
                $employee_position =  in_array('A', $employeeStatus[$val['nik_employee']]) ? $employeePosition[$val['nik_employee']]['A'] : $employeePosition[$val['nik_employee']][$val['employee_status']];
                
                //resultValue utk menampung parameter yg sudah di(pivot) yg akan dijadikan data ke spreadsheet
                $resultValue = [
                    $number,
                    $val['nik_employee'],
                    $val['name'],
                    $employee_position,
                    $val['principal'],
                    $val['region'],
                    $val['organization_unit'],
                    $val['department'],
                    $val['company'],
                    $employee_status_summary,
                ];
                $allValueThisPeriod = [];
                foreach ($datePeriod as $key => $valu) {
                    $valueOfThisDate = is_null(@$summary[$k][$valu]) ? '-' : @$summary[$k][$valu];
                    // UTK MENGHITUNG BANYAK NILAI : ABS,ANL,OFF.  KESELURUHAN MASING2 ORG
                    if($valueOfThisDate != ''){
                        $thisVal = $valueOfThisDate;
                        $$thisVal[] = 1;
                        $allValueThisPeriod[] = 1;
                    }
                    $resultValue[] = $valueOfThisDate;
                }
                foreach ($attendStatus as $kk => $val_) {
                    // UTK MENJUMLAHKAN NILAI : ABS,ANL,OFF.  KESELURUHAN MASING2 ORG
                    $thisVal = $val_;
                    $resultValue[] = (count($$thisVal) > 0) ? count($$thisVal) : '0';
                }
                foreach ($attendStatus as $k => $val) {
                    $thisVal = $val;
                    $$thisVal = []; // nama variabel utk nampung direset lagi setiap beda org, eg: $OFF,$PRS,$ANL,$UPL
                }
                if(count($allValueThisPeriod) > 0){ //hanya memasukkan ke excel employee yg memiliki workdays pd periode tgl yg dipilih
                    $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
                }
                $number++;
            }
        }

        $dataSheet[] = ['Legend']; 
        foreach ($attendStatus as $key => $val) {
            $dataSheet[] = [$val, ':', $legend[$val]]; 
        }

        $exportType = 'excel';
        $filename   = 'attendance-'.$dateRangeName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $sheetName1 = 'Sheet 1';
        $workSheet1 = new Worksheet($spreadsheet, $sheetName1);
        $spreadsheet->addSheet($workSheet1, 0);
        $spreadsheet->setActiveSheetIndexByName($sheetName1); // utk set sheet yg aktif

        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2

        $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $activeSheet = $spreadsheet->getActiveSheet();

        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }

        // styling manual berdasar cell
        $lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAfterTitle   = count($title)+1;
        $columnLegend       = count($attendStatus) + 1;
        $columnAllData      = $lastColumn.($lastRow - $columnLegend);

        $activeSheet->mergeCells('A2:C2');
        $activeSheet->mergeCells('A3:C3');
        $activeSheet->getStyle('A6:'.$columnAllData)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('B6:E'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('F6:'.$lastColumn.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A5:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

    public function _export(Request $request) { 
        // export yg model lama dengan pivot via DB (tidak dipakai), yg skr pakai _export_2
        ini_set('max_execution_time', -1);
        
        $start          = $request->startdate;
        $end            = $request->enddate;
        $branch         = $request->branch ?? 'null';

        $id_company     = session('id_company');
        $dateRange      = [];
        $datePeriod     = [];
        $dateRangeName  = date("j M Y", strtotime($start)).' to '.date("j M Y", strtotime($end));
        $title          = ['No', 'Employee No', 'Employee Name', 'Position', 'Organization Unit', 'Status'];
        $attendStatus   = ['ANL','BPT','CHM','CST','DIF','EDO','GVL','MAT','MDL','MIS','PAT','SDC','UPL','WMC','OFF','PRS','ABS','NSI','NSO'];
        $legend         = [
            'ANL'=>'Annual Leave',
            'BPT'=>'Absent',
            'CHM'=>'Employee Child Married',
            'CST'=>'Child Circumcision',
            'DIF'=>'Decease Spouse, Parents & Child (In Law), Siblings',
            'EDO'=>'Extra Day Off',
            'GVL'=>'Deceased of Family',
            'MAT'=>'Maternity Leave',
            'MDL'=>'Employee Married Leave',
            'MIS'=>'Miscarriage Leave',
            'PAT'=>'Employee Child Birth',
            'SDC'=>'Sick',
            'UPL'=>'Unpaid Leave',
            'WMC'=>'Wife Miscarriage',
            'OFF'=>'Off',
            'PRS'=>'Present',
            'ABS'=>'Absent',
            'NSI'=>'No Swipe In',
            'NSO'=>'No Swipe Out',
        ];

        $keyFromFunction= ['id_employee','nik_employee','name','position_name','organization_unit','status'];
        $staticKeyFromFunction = $keyFromFunction;
        $countTitle     = count($title);
        $countKey       = count($keyFromFunction);

        $period = new \DatePeriod(
             new \DateTime($start),
             new \DateInterval('P1D'),
             new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        foreach ($period as $k => $v) {
            $dateRange[]        = $v->format("Y-m-d");
            $title[$countTitle] = $v->format("D, d M");
            $keyFromFunction[$countKey] = $v->format("Y-m-d");
            $datePeriod[] = $v->format("Y-m-d");
            $countTitle++;
            $countKey++;
        }

        $workDays = DB::table('hr_work_days')
                ->selectRaw("DISTINCT(current_dates) as dates")
                ->whereIn('current_dates', $datePeriod)
                ->where('id_company', '=', session('id_company'))
                ->get();
        if(count($workDays) < 1){
            echo "<script>alert('No Data in this Period');window.close();</script>";
            return false;
        }

        $countTitleAfter = count($title);
        foreach ($attendStatus as $k => $v) {
            $title[$countTitleAfter] = $v;
            $countTitleAfter++;
        }

        $dataSheet = [
            [''],
            ['AttendanceStatusReport'],
            ['From '.$dateRangeName],
            [''],
        ];

        $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

        if($branch != 'null'){
            $branch_ = 'array['.$branch.']';
            $q1 = DB::select(DB::raw("select * from sp_funct_generate_summary_presence ('$id_company','$start','$end',$branch_)"))[0]->query;
        } else {
            $q1 = DB::select(DB::raw("select * from sp_funct_generate_summary_presence ('$id_company','$start','$end')"))[0]->query;
        }
        $result_ = DB::select(DB::raw($q1));
        $sort = array_column($result_, 'name'); // sort result by name
        array_multisort($sort, SORT_ASC, $result_);

        $rows = [];
        $number = 1;
        $var = '';
        foreach ($attendStatus as $k => $val) {
            $thisVal = $val;
            $$thisVal = []; // value dari array dijadikan nama variabel utk nampung, eg: $OFF,$PRS,$ANL,$UPL
        }

        foreach ($result_ as $k => $val) {
            $countRows = 0;
            $resultValue = [];
            foreach ($keyFromFunction as $i => $item) {
                if(!in_array($item, $staticKeyFromFunction)){ // penempatan ABS,OFF,ANL, dll 
                    if(property_exists($result_[$k], $item)){
                        $valueOfThisKey = is_null(@$val->$item) ? '' : @$val->$item;
                        $resultValue[] = $valueOfThisKey; // penempatan nilai ABS,OFF,ANL, dll disini

                        // UTK MENGHITUNG JUMLAH NILAI : ABS,ANL,OFF.  KESELURUHAN MASING2 ORG
                        if(strpos($valueOfThisKey, ',') !== false) { // jika hasil pd 1 tanggal berisi multi value, eg: ABS,OFF
                            foreach ($valueOfThisKey as $key => $value) {
                                if($value != ''){
                                    $thisVal = $value;
                                    $$thisVal[] = 1;
                                }
                            }
                        } else { // jika hasil pd 1 tanggal berisi 1 value, eg: ABS
                            if($valueOfThisKey != ''){
                                $thisVal = $valueOfThisKey;
                                $$thisVal[] = 1;
                            }
                        }
                        $countRows++;
                    } else {
                        $resultValue[] = '';
                    }
                } else { // penempatan NAMA,NIK,POSISI,ORGANISASI
                    $valueOfThisKey = is_null(@$val->$item) ? '' : strtoupper(@$val->$item);
                    $resultValue[] = $valueOfThisKey; 
                }
            }
            foreach ($attendStatus as $kk => $val_) {
                $thisVal = $val_;
                $resultValue[] = (count($$thisVal) > 0) ? count($$thisVal) : '0';
            }
            foreach ($attendStatus as $k => $val) {
                $thisVal = $val;
                $$thisVal = []; // nama variabel utk nampung direset lagi setiap beda org, eg: $OFF,$PRS,$ANL,$UPL
            }
            $resultValue[0] = $number; // id_employee hasil data diganti nomor urut 
            $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
            $number++;
        }

        $dataSheet[] = ['Legend']; 
        foreach ($attendStatus as $key => $val) {
            $dataSheet[] = [$val, ':', $legend[$val]]; 
        }

        $exportType = 'excel';
        $filename   = 'attendance-'.$dateRangeName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $sheetName1 = 'Sheet 1';
        $workSheet1 = new Worksheet($spreadsheet, $sheetName1);
        $spreadsheet->addSheet($workSheet1, 0);
        $spreadsheet->setActiveSheetIndexByName($sheetName1); // utk set sheet yg aktif

        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2

        $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $activeSheet = $spreadsheet->getActiveSheet();

        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }

        // styling manual berdasar cell
        $lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAfterTitle   = count($title)+1;
        $columnLegend       = count($attendStatus) + 1;
        $columnAllData      = $lastColumn.($lastRow - $columnLegend);

        $activeSheet->mergeCells('A2:C2');
        $activeSheet->mergeCells('A3:C3');
        $activeSheet->getStyle('A6:'.$columnAllData)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('B6:E'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('F6:'.$lastColumn.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A5:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

    public function patchWorkdays(Request $request=null)
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();
            $id_company = $request->id_company ?? null;
            $id_employee = $request->id_employee ?? null;
            $start = $request->start ?? date('Y-m-d');
            $end = $request->end ?? date('Y-m-d', strtotime($start.' +2 month'));
            $fromScheduler = false;

            if($id_company){
                $id_company = (is_array($id_company) && count($id_company) > 0) ? $id_company : [$id_company];
                $patch = $this->WorkDays->patchWorkdays($id_company, $start, $end, $id_employee);
            } else {
                $company    = DB::table('master_company as mc')->where('mc.status','A')->orderBy('mc.id_company')->get();
                $fromScheduler = true;
                foreach ($company as $key => $item) {
                    $id_company[] = $item->id_company;
                }
                $patch = $this->WorkDays->patchWorkdays($id_company, $start, $end, $id_employee, $fromScheduler);
            }
            DB::commit();   
            if(!$fromScheduler){
                return response(['status' => 'Success', 'message' => 'Update '.$patch->employee.' employee with '.$patch->record.' records Success', 'data' => null]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            if(!$fromScheduler){
                return response(['status' => 'Failed', 'message' => $e->getMessage(), 'data' => null]);
            }
        }
    }

    public static function generateWorkdaysBulky() {
        \Log::channel('scheduler')->info("Start Scheduler: Generate Workdays Bulky.");
        $startDate = now()->subWeek()->format('Y-m-d');
        $endDate = now()->addDays(40)->format('Y-m-d');
        $t = now();
        $companies = DB::select("SELECT * FROM master_company");
        foreach($companies as $company) {
            $bulky = DB::select("SELECT * FROM generateworkdaysbulky(?, ?, ?, ?) gwdb", [
                $company->id_company, $startDate, $endDate, 1
            ]);
        }
        $end = gmdate('H:i:s', now()->diffInSeconds($t));
        \Log::channel('scheduler')->info("Stop Scheduler: Generate Workdays Bulky. Elapsed time: ".$end);
    }

    public function employee_lock_attendance(Request $request)
    {
        ini_set('max_execution_time', -1);
        if ($request->ajax()) {
            $branchByManager    = [];
            $regional           = null;
            $location           = null;
            $branch             = null;

            if(session('access_group') != 'Default_Administrator'){
                //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
                $path_menu = 'employee/employee_setting/workdays';
                $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu)->pluck('id_user_responsibility');
                if($get_ur->count() > 0){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();

                    if(count($get_branch) > 0){
                        $branchByManager        = $get_branch;
                        $branch           = $branchByManager;
                    }
                    if($branch && count($branch) > 0){
                        $regional  = MasterBranch::whereIn('id_branch', $branch)->pluck('id_region')->unique();
                    }
                } 
            }

            $status         = $request->status ?? null;
            $nik            = $request->employeename ?? null;
            $department     = $request->department  ?? null;
            $regional       = $request->regional  ?? null;
            $branch         = !is_null($request->branch) ? $request->branch : null;
            $location       = $request->location  ?? $location;
            $lock_location  = $request->lock_location  ?? null;
            $id_company     = session('id_company');
            // dd($regional, $branch);

            $data = $this->WorkDays->employeeLockAttendance($status, $nik, $department, $regional, $branch, $location, $lock_location, $id_company);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
    }

    public function lockEmployeeAttendance(Request $request){
        ini_set('max_execution_time', -1);
        try{
            $type           = $request->type ?? null;
            $id_employee    = $request->id_employee ?? NULL;

            DB::beginTransaction();

            if(!$id_employee || count($id_employee) < 1){
                throw new \Exception("Please Select Employee First");
            }

            $lock_gps_location = $type == 'lock' ? true : false;
            $data = [
                'lock_gps_location' => $lock_gps_location,
                'updated_by' => session('id_user'),
                'update_date' => date('Y-m-d H:i:s')
            ];

            $getEmployee = DB::table('hr_employee as he')
                        ->selectRaw('MAX(hwd.current_dates) as max_date, he.id_employee, he.id_company')
                        ->leftJoin('hr_work_days as hwd', function ($join) {
                            $join->on('hwd.id_employee', '=', 'he.id_employee');
                        })
                        ->whereIn('he.id_employee', $id_employee)
                        ->groupBy('he.id_employee')
                        ->get();

            foreach ($getEmployee as $key => $val) {
                $start  = date('Y-m-d');
                $end    = $val->max_date;

                $updateEmployee = DB::table('hr_employee as he')
                        ->where('he.id_employee', $val->id_employee)
                        ->update($data);
                
               $generateLock = DB::table(DB::raw("generate_lock_gps_location(".$val->id_employee.",".$val->id_company.",'".$start."','".$end."',".session('id_user').")"))
                            ->select('*')->get();
			
			/*
				$generateLock = DB::statement("UPDATE hr_work_days hwd
                        SET lock_gps_location = he.lock_gps_location                         
                        FROM hr_employee he
                        WHERE hwd.id_employee = he.id_employee
                            AND hwd.id_company = he.id_company
                            AND hwd.lock_gps_location <> he.lock_gps_location
                            AND	current_dates >= coalesce('".$start."',current_date)
					        AND current_dates <= coalesce('".$end."', current_date + 30)
                            AND hwd.id_employee = coalesce(".$val->id_employee.", he.id_employee)
                            AND hwd.id_company = coalesce(".$val->id_company.", he.id_company)");
			*/
            }

            DB::commit();   
            return response(['status' => 'true', 'message' => ucwords($type).' Employee Success', 'data' => $id_employee]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['status' => 'false', 'message' => $e->getMessage(), 'data' => null]);
        }
    }

    public function attendance_photo(Request $request)
    {
        ini_set('max_execution_time', -1);
        $path_menu_param    = $request->path();
        $path_menu          = @$request->path_menu;
        $myIdEmployee       = @$this->employee()->id_employee;
        $myNik              = @$this->employee()->nik_employee;
        $default_company    = null;

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

        $getdepartment      = $this->WorkDays->getdepartment($default_company);
        $get_employee       = $this->WorkDays->getemployeename([], ['A']);
        $accessGroup        = session('access_group');
        $branchByManager    = [];
        $id_branch          = [];
        $id_region          = [];

        if(session('access_group') != 'Default_Administrator'){
            //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
            $address = ['time_attendance/attendance/attendance_list', 'employee/employee_setting/workdays'];
            // if(in_array($path_menu, $address)){
                $path_menu_ = 'employee/employee_setting/workdays';
            // }
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
            if($get_ur->count() > 0){
                $get_branch = [];
                if((session('company_type') == 'os' && session('access_group') == 'Default_User') || session('company_type') != 'os' ){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                }
                if(count($get_branch) > 0){
                    $branchByManager  = $get_branch;
                    $id_branch        = $branchByManager;
                } else {
                    $branchByManager  = 'null';
                }

                if(count($id_branch) > 0){
                    $id_region  = MasterBranch::whereIn('id_branch', $id_branch)->pluck('id_region')->unique();
                }
            } 
        }

        if ($request->ajax()) {
            $status             = $request->status ?? ['A', 'I'];
            $nik                = $request->employeename ?? 'null';
            $startdate          = $request->startdate  ?? 'null';
            $department         = $request->department  ?? 'null';
            $enddate            = $request->enddate  ?? 'null';
            $regional           = $request->regional  ?? 'null';
            $location           = $request->location  ?? 'null';
            $id_company         = session('id_company');

            if($nik != 'null'){
                $getIdEmployee = DB::table('hr_employee as he')
                    ->select("he.id_employee");

                if($path_menu == 'employee/employee_setting/workdays'){
                    $getIdEmployee->where('he.id_company', session('id_company'));
                }
                if(is_array($nik)){
                    $getIdEmployee->whereIn('he.nik_employee', $nik);
                } else {
                    $getIdEmployee->where('he.nik_employee', $nik); 
                }
                $getIdEmployee->whereIn('he.status', $status); 
                $id_employee = $getIdEmployee->get()->pluck('id_employee')->all();
            } 
            else {
                $id_employee = 'null';
            }

            if(is_array($request->branch) && count($request->branch)>0 ){
                $branch     = $request->branch;
            } else {
                if(session('access_group') != 'Default_Administrator'){
                    $branch = $branchByManager;
                } else {
                    $branch     = 'null';
                }
            }

            $data = $this->WorkDays->attendancePhoto($id_employee, $id_company, $startdate, $enddate, $branch, $location, $regional, $department, $status, $path_menu);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->make(true);
        }
    }

    public function checkDayTypeByIdShift(Request $request=null)
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();

            $lastMonth = Carbon::now()->subMonth();
            $startDate = $lastMonth->startOfMonth()->toDateString();
            $currentMonth = Carbon::now();
            $endDate = $currentMonth->endOfMonth()->toDateString();

            \Log::channel('scheduler')->info('Start check different day_type');
            $check = $this->WorkDays->checkDayTypeByIdShift($startDate, $endDate);
            \Log::channel('scheduler')->info('End Check. There are '.$check.' records different day_type according to id_shift in work_days from '.$startDate.' ~ '.$endDate);

            DB::commit();   
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->info('End Check different day_type. Error: '.$e->getMessage());
        }
    }

    public function updateWorkdaysByHoliday(Request $request=null)
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();
            
            $startMonth = Carbon::now()->subMonth();
            $startDate = $startMonth->startOfMonth()->toDateString();
            $endMonth = Carbon::now()->addMonths(1);
            $endDate = $endMonth->endOfMonth()->toDateString();

            \Log::channel('scheduler')->info('Start update workdays by holiday');
            $check = $this->WorkDays->updateWorkdaysByHoliday($startDate, $endDate);
            \Log::channel('scheduler')->info('End update workdays by holiday');

            DB::commit();   
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->info('End update workdays by holiday. Error: '.$e->getMessage());
        }
    }

    public function updateWorkdaysLocation(Request $request=null)
    {
        ini_set('max_execution_time', -1);
        try{
            DB::beginTransaction();

            \Log::channel('scheduler')->info('Start update workdays target location by position');
            $check = $this->WorkDays->updateWorkdaysLocationByPosition();
            \Log::channel('scheduler')->info('End update workdays target location by position');

            DB::commit();   
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->info('End update workdays target location by position. Error: '.$e->getMessage());
        }
    }
}
