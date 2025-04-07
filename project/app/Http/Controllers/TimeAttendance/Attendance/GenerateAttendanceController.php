<?php

namespace App\Http\Controllers\TimeAttendance\Attendance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\TimeAttendance\Attendance\GenerateAttendance;
use App\Models\Employee\EmployeeSetting\WorkDays;
use DB;

class GenerateAttendanceController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->GenerateAttendance = new GenerateAttendance;
        $this->WorkDays = new WorkDays;
	}
    
    public function index()
    {
        return view('time_attendance.attendance.generateattendance');
    }
    
    public function getemployeename(Request $request)
    {
        switch ($request->method()) {
	        case 'POST':
                $employeename=strip_tags(base64_decode($request->post('employeename')));
                
                $getemployeename=$this->GenerateAttendance->getemployeename($employeename);
                $data=[];
                foreach($getemployeename['data'] as $rowgetemployeename)
                {
                    $data[]=array(
                        "id_employee"=>$rowgetemployeename->id_employee,
                        "name"=>$rowgetemployeename->name,
                        "nik_employee"=>$rowgetemployeename->nik_employee,
                        "id_user"=>$rowgetemployeename->id_user,
                        "status"=>$rowgetemployeename->status,
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
    
    public function generateattendance(Request $request)
    {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $employeename       = $request->employeename ?? null;
            $id_employee        = $request->id_employee ?? null;
            $startdate          = $request->startdate ?? date('Y-m-d');
            $enddate            = $request->enddate ?? date('Y-m-d');
            // var_dump(session('id_company'), $startdate, $enddate, $id_employee);die;
            // $generate = $this->GenerateAttendance->generateattendance($employeename,$id_employee,$startdate,$enddate);
            $generate = $this->WorkDays->patchWorkdays([session('id_company')], $startdate, $enddate, $id_employee);
            if(!$generate){
                throw new \Exception('Generate Failed');
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Generate Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }

}
