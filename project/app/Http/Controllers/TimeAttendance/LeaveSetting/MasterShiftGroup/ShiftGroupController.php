<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\TimeAttendance\LeaveSetting\MasterShiftGroup\ShiftGroup;
use App\Models\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDaily;
use DB;
use DataTables;

class ShiftGroupController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->ShiftGroup = new ShiftGroup;
        $this->ShiftDaily = new ShiftDaily;
	}
    
    public function index()
    {
        $getdailyshift=$this->ShiftDaily->getdata();
        $datadailyshift=[];
        foreach($getdailyshift['data'] as $rowgetdailyshift)
        {   
            $datadailyshift[]=array(
                "id_shift"=>$rowgetdailyshift->id_shift,
                "shift_code"=>$rowgetdailyshift->shift_code,
                "description"=>$rowgetdailyshift->description
            );
        }

        return view('time_attendance.leave_setting.master_shift_group.index', compact('datadailyshift'));
    }
    
    public function getdata(Request $request)
    {
        $getdata = $this->ShiftGroup->getdata()['data'];
        foreach($getdata as $k => $rowgetdata){
            if($rowgetdata->status=="A"){
                $status='Active';
            } else {
                $status='Inactive';
            }
                    
            if($rowgetdata->overtime_based_on=="R"){
                $overtime_based_on='Request';
            }
            else if($rowgetdata->overtime_based_on=="A"){
                $overtime_based_on='Attendance';
            }
            else{
                $overtime_based_on='Request & Attendance';
            }

            $getdata[$k]->status = $status;
            $getdata[$k]->overtime_based_on = $overtime_based_on;
        }

        return DataTables::of($getdata)
            ->addIndexColumn()
            ->addColumn('action', function($data) {
                        $button = '<button type="button" class="btn btn-sm btn-primary edit" id="edit_'.$data->id_shiftgroup.'"><i class="fa fa-pencil"></i></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-danger delete" id="delete_'.$data->id_shiftgroup.'"><i class="fa fa-trash"></i></button>';
                        return $button;
                    })
            ->make(true);
    }
    
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $shift_code=strip_tags(base64_decode($request->post('shift_code')));
            $status=strip_tags(base64_decode($request->post('status')));
            $description=strip_tags(base64_decode($request->post('description')));
            $automaticabsence=strip_tags(base64_decode($request->post('automaticabsence')));
            $overtimebasedon=strip_tags(base64_decode($request->post('overtimebasedon')));
            $totaldays=strip_tags(base64_decode($request->post('totaldays')));
            $checkoutnextday=strip_tags(base64_decode($request->post('checkoutnextday')));
            $detail_days=json_decode(base64_decode($request->post('detail_days')));
            $detail_shift=json_decode(base64_decode($request->post('detail_shift')));
            $detail_status=json_decode(base64_decode($request->post('detail_status')));
            $lock_gps_location=$request->post('lock_gps_location') == '1' ? true : false;

            
            $header = [
                'shiftgroup_code' => $shift_code,
                'description' => $description,
                'overtime_based_on' => $overtimebasedon,
                'total_days' => $totaldays,
                'automatic_absence' => $automaticabsence,
                'allow_checkout_nextdays' => $checkoutnextday,
                'lock_gps_location' => $lock_gps_location,
                'status' => $status,
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            ];
            $detail = [];
            foreach ($detail_days as $key => $val) {
                $detail[] = [
                    'id_shiftgroup' => '',
                    'sequence' => $val,
                    'id_shift' => $detail_shift[$key],
                    'status' => $detail_status[$key],
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ];
            }

            $insertdata=$this->ShiftGroup->insertdata($header, $detail);
            if(!$insertdata){
                throw new \Exception('Insert Failed');
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Insert Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }

    public function edit(Request $request)
    {
        switch ($request->method()) {
	        case 'POST':
                $id_shiftgroup=strip_tags(base64_decode($request->post('id_shiftgroup')));
                
                $geteditdata=$this->ShiftGroup->geteditdata($id_shiftgroup);
                $data['master']=[];
                $data['detail']=[];
                
                $data['master']=array(
                    "id_shiftgroup"=>$geteditdata['master']->id_shiftgroup,
                    "shiftgroup_code"=>$geteditdata['master']->shiftgroup_code,
                    "description"=>$geteditdata['master']->description,
                    "overtime_based_on"=>$geteditdata['master']->overtime_based_on,
                    "allow_checkout_nextdays"=>$geteditdata['master']->allow_checkout_nextdays,
                    "lock_gps_location"=>$geteditdata['master']->lock_gps_location,
                    "status"=>$geteditdata['master']->status,
                    "automatic_absence"=>$geteditdata['master']->automatic_absence,
                    "total_days"=>$geteditdata['master']->total_days
                );
                
                foreach($geteditdata['detail'] as $rowgeteditdatadetail)
                {
                    $data['detail'][]=array(
                        "id_shiftgroup"=>$id_shiftgroup,
                        "id_shift"=>$rowgeteditdatadetail->id_shift,
                        "day"=>$rowgeteditdatadetail->day,
                        "description"=>$rowgeteditdatadetail->description,
                        "status"=>$rowgeteditdatadetail->status_detail
                    );
                }
                echo json_encode($data);
                break;
            default :
	        	echo 'Invalid request';
	        	break;
        }
    }
    
    public function editsave(Request $request)
    {
        ini_set('max_execution_time', -1);

        DB::beginTransaction();
        try {
            $id_shiftgroup=strip_tags(base64_decode($request->post('id_shiftgroup')));
            $shift_code=strip_tags(base64_decode($request->post('shift_code')));
            $status=strip_tags(base64_decode($request->post('status')));
            $description=strip_tags(base64_decode($request->post('description')));
            $automaticabsence=strip_tags(base64_decode($request->post('automaticabsence')));
            $overtimebasedon=strip_tags(base64_decode($request->post('overtimebasedon')));
            $totaldays=strip_tags(base64_decode($request->post('totaldays')));
            $checkoutnextday=strip_tags(base64_decode($request->post('checkoutnextday')));
            $detail_days=json_decode(base64_decode($request->post('detail_days')));
            $detail_shift=json_decode(base64_decode($request->post('detail_shift')));
            $detail_status=json_decode(base64_decode($request->post('detail_status')));
            $lock_gps_location=$request->post('lock_gps_location') == '1' ? true : false;
            
            $header = [
                'shiftgroup_code' => $shift_code,
                'description' => $description,
                'overtime_based_on' => $overtimebasedon,
                'total_days' => $totaldays,
                'automatic_absence' => $automaticabsence,
                'allow_checkout_nextdays' => $checkoutnextday,
                'lock_gps_location' => $lock_gps_location,
                'status' => $status,
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            ];
            $detail = [];
            foreach ($detail_days as $key => $val) {
                $detail[] = [
                    'id_shiftgroup' => $id_shiftgroup,
                    'sequence' => $val,
                    'id_shift' => $detail_shift[$key],
                    'status' => $detail_status[$key],
                    'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ];
            }

            $editdata = $this->ShiftGroup->editdata($header, $id_shiftgroup, $detail);
            if(!$editdata){
                throw new \Exception('Update Failed');
            }

            // JIKA terdapat employee dengan id shift yg diubah tsb maka update workdays sesuai id employee tsb shg butuh proses bbrp detik
            $getEmployeeInThisShiftGroup = DB::table('hr_employee as he')
                    ->select('he.id_employee')
                    ->where('he.id_company', '=', session('id_company'))
                    ->where('he.id_shift_group', $id_shiftgroup)
                    ->get();
            if($getEmployeeInThisShiftGroup){
                foreach ($getEmployeeInThisShiftGroup as $key => $val) {
                    $updateWorkdays = DB::table(DB::raw("updateworkdays('".$val->id_employee."','".session('id_company')."','".date('Y-m-d')."','".session('id_user')."')"))->get();
                }
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Update Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }

    public function delete(Request $request)
    {
        DB::beginTransaction();
        try {
            $id_shiftgroup=strip_tags(base64_decode($request->post('id_shiftgroup')));
                
            $deletedata=$this->ShiftGroup->deletedata($id_shiftgroup);
            if(!$deletedata){
                throw new \Exception('Delete Failed');
            }

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Delete Success']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }
}
