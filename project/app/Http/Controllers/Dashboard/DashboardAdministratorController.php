<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use App\Http\Controllers\Dashboard\DashboardUserController;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use Illuminate\Http\Request;
use App\Models\TimeAttendance\Attendance\Attendance;
use App\Models\Home;
use App\Models\Curl;
use App\Traits\StandardResponse;
use Yajra\DataTables\QueryDataTable;

class DashboardAdministratorController extends Controller
{
    use StandardResponse;
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->Home = new Home;
        $this->DashboardUserController = new DashboardUserController;
	}

    public function index()
    {           
        $mapboxToken = AttendanceController::getTokenMapbox();
        @$getEmployee = @$this->DashboardUserController->employee();
        $attendance = Attendance::getAttendance([
            'id_employee' => @$getEmployee->id_employee
        ]);
        $id_employee =  @$getEmployee->id_employee;
        // if($attendance == null){
        //     $logout = url('logout');
        //     echo "<script>alert('Failed generate workdays'); document.location.href = '".$logout."';</script>";
        // }
        // if($attendance && count($attendance) < 1){
        //     $logout = url('logout');
        //     echo "<script>alert('Anda belum bisa melakukan absen'); document.location.href = '".$logout."';</script>";
        // }
        if(session('access_group') == 'Default_Administrator'){
            if(@$getEmployee){
                $id_employee = @$getEmployee->id_employee;
            } else {
                $id_employee = null;
            }
        } else {
            $id_employee = @$getEmployee->id_employee;
        }

        $request_type           = $this->Home->request_type();
        $employee_information   = $this->DashboardUserController->employee_information(@$getEmployee->id_employee);
        $announcement           = $this->DashboardUserController->announcement();
        $company_policy         = $this->DashboardUserController->company_policy();
        $upcoming_event         = $this->DashboardUserController->upcoming_event(@$getEmployee->id_employee);
        $public_course_program  = $this->DashboardUserController->public_course_program(@$getEmployee->id_employee);
        $news                   = $this->DashboardUserController->news(@$getEmployee->id_employee);
        $attendance_per_month   = $this->DashboardUserController->attendance_per_month(@$getEmployee->id_employee);
        $attendance_per_year    = $this->DashboardUserController->attendance_per_year(@$getEmployee->id_employee);
        // $employee_by_department = $this->DashboardUserController->employee_by_department($id_employee);
        $employee_turnover      = $this->DashboardUserController->employee_turnover($id_employee);
		$mapboxGetPlace         = Curl::findApi('mapbox_getplace')->url;
        $allEmployee            = $this->DashboardUserController->employeeAll();
        $url_announcement       = url('employee/employee/announcement');
        $getCampaign            = $this->Home->listCampaign(session('id_company'), $id_employee);
        
        $compact = ['attendance', 'request_type','mapboxToken', 'employee_information','announcement','company_policy','upcoming_event','attendance_per_month','attendance_per_year','employee_turnover','mapboxGetPlace','news','allEmployee', 'url_announcement', 'getCampaign', 'public_course_program', 'id_employee'];

        return view('dashboard.dashboard_administrator.index', compact($compact));
    }

    public function cashAdvanceReports(Request $request)
    {
        $request->validate([
            'interval' => 'required|numeric|min:1|max:12'
        ]);

        if($request->input('data') == 'per_employee') {
            $perEmployee = HrCashAdvance::cashAdvancePerEmployeeReport(session('id_company'));
            return (new QueryDataTable($perEmployee))->toJson();
        }

        if($request->input('data') == 'per_employee_per_month') {
            $perEmployee = HrCashAdvance::cashAdvancePerEmployeePerMonthReport(session('id_company'));
            return (new QueryDataTable($perEmployee))->toJson();
        }

        if($request->input('data') == 'settlement_outstanding') {
            $outstanding = HrCashAdvance::getOutstandingReport(session('id_company'));
            return (new QueryDataTable($outstanding))->toJson();
        }

        $reports = [
            "per_month_by_dept" => HrCashAdvance::cashAdvancePerMonthByDeptReport(session('id_company'), $request->interval),
            "per_year_by_dept" => HrCashAdvance::cashAdvancePerYearByDeptReport(session('id_company'), $request->interval),
            "per_year" => HrCashAdvance::cashAdvancePerYearReport(session('id_company'), $request->interval),
            // "per_employee_per_month" => HrCashAdvance::cashAdvancePerEmployeePerMonthReport(session('id_company')),
            // "per_employee" => HrCashAdvance::cashAdvancePerEmployeeReport(session('id_company')),
        ];
        return $this->success($reports);
    }

    public function dashboard_information_admin(Request $request)
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

}
