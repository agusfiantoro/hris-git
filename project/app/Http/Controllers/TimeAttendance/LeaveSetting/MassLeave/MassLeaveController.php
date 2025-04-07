<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\MassLeave;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use App\Models\Employee\EmployeeSetting\WorkDays;
use Validator;
use App\Models\TimeAttendance\LeaveSetting\MassLeave\MassLeave;
use DB;


class MassLeaveController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->MassLeave = new MassLeave;
        $this->WorkDays = new WorkDays;
	}
    
    public function index()
    {
        $get_employee  = $this->WorkDays->getemployeename();

        return view('time_attendance.leave_setting.mass_leave.index', compact('get_employee'));
    }
    
    public function getemployeename(Request $request)
    {
        switch ($request->method()) {
	        case 'POST':
                $employeename=strip_tags(base64_decode($request->post('employeename')));
                
                $getemployeename=$this->MassLeave->getemployeename($employeename);
                $data=[];
                foreach($getemployeename['data'] as $rowgetemployeename)
                {
                    $data[]=array(
                        "id_employee"=>$rowgetemployeename->id_employee,
                        "name"=>$rowgetemployeename->name,
                        "nik_employee"=>$rowgetemployeename->nik_employee,
                        "id_user"=>$rowgetemployeename->id_user
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

    public function generatemassleave(Request $request)
    {
        DB::beginTransaction();
        try {
            $employeename = $request->post('employeename') ?? null;
            $employee_id = $request->post('employee_id') ?? null;
            $enddate = $request->post('enddate');

            $generate = $this->MassLeave->generatemassleave($employee_id,$enddate);
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

    public function generateMassLeaveScheduler()
    {
        try{
            DB::beginTransaction();
            $getCompany = DB::table('master_company as mc')->select('*')
                            // ->where('mc.status', 'A')
                            ->get();
            if($getCompany){
                foreach ($getCompany as $key => $val) {
                    $sql = "select * from GenerateMassLeave (".$val->id_company.", 1, null, '".date('Y-m-d')."') ";
                    $get = collect(DB::select($sql))->toArray();
                    \Log::channel('scheduler')->info('Generate Mass Leave ('.$val->company_name.') Success');
                }
            }
            DB::commit();   
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::channel('scheduler')->error('Generate Mass Leave Failed. '.$e);
        }
    }
}
