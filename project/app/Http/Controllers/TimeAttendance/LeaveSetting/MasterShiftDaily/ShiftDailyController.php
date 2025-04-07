<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\MasterShiftDaily;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\TimeAttendance\LeaveSetting\MasterShiftDaily\ShiftDaily;
use DB;
use DataTables;

class ShiftDailyController extends Controller
{
    public function __construct()
	{
        date_default_timezone_set('Asia/Jakarta');
        $this->ShiftDaily = new ShiftDaily;
	}
    
    public function index()
    {
        return view('time_attendance.leave_setting.master_shift_daily.index');
    }
    
    public function getdata(Request $request)
    {
        $getdata = $this->ShiftDaily->getdata()['data'];
        foreach($getdata as $k => $rowgetdata){
            if($rowgetdata->status=="A"){
                $status='Active';
            } else {
                $status='Inactive';
            }

            if($rowgetdata->flexible_shift=="1"){
                $flexible_shift='Yes';
            } else {
                $flexible_shift='No';
            }
                    
            if($rowgetdata->day_type=="WD"){
                $day_type='Week Day';
            } else {
                $day_type='Off Day';
            }

            $getdata[$k]->status = $status;
            $getdata[$k]->flexible_shift = $flexible_shift;
            $getdata[$k]->day_type = $day_type;
        }

        return DataTables::of($getdata)
            ->addIndexColumn()
            ->addColumn('action', function($data) {
                        $button = '<button type="button" class="btn btn-sm btn-primary edit" id="edit_'.$data->id_shift.'"><i class="fa fa-pencil"></i></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" class="btn btn-sm btn-danger delete" id="delete_'.$data->id_shift.'"><i class="fa fa-trash"></i></button>';
                        return $button;
                    })
            ->make(true);
    }
    
    public function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $shift_code=strip_tags(base64_decode($request->post('shift_code')));
            $description=strip_tags(base64_decode($request->post('description')));
            $status=strip_tags(base64_decode($request->post('status')));
            $daytype=strip_tags(base64_decode($request->post('daytype')));
            $start_time=strip_tags(base64_decode($request->post('start_time')));
            $end_time=strip_tags(base64_decode($request->post('end_time')));
            $flexibletype=strip_tags(base64_decode($request->post('flexibletype')));
            $start_break=strip_tags(base64_decode($request->post('start_break')));
            $end_break=strip_tags(base64_decode($request->post('end_break')));
            $workingtime=strip_tags(base64_decode($request->post('workingtime')));
            $latein=strip_tags(base64_decode($request->post('latein')));
            $earlyout=strip_tags(base64_decode($request->post('earlyout')));
            
            $data = [
                'shift_code' => $shift_code,
                'description' => $description,
                'status' => $status,
                'day_type' => $daytype,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'flexible_shift' => $flexibletype,
                'start_break' => $start_break,
                'end_break' => $end_break,
                'productive_work_time' => $workingtime,
                'late_in_max' => $latein,
                'early_out_max' => $earlyout,
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            ];
            $insertdata=$this->ShiftDaily->insertdata($data);
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
                $id_shift=strip_tags(base64_decode($request->post('id_shift')));
                
                $geteditdata=$this->ShiftDaily->geteditdata($id_shift);
                $data=[];
                foreach($geteditdata['data'] as $rowgeteditdata)
                {
                    $data[]=array(
                        "shift_code"=>$rowgeteditdata->shift_code,
                        "description"=>$rowgeteditdata->description,
                        "day_type"=>$rowgeteditdata->day_type,
                        "flexible_shift"=>$rowgeteditdata->flexible_shift,
                        "overlap"=>$rowgeteditdata->overlap_days,
                        "productive_work_time"=>$rowgeteditdata->productive_work_time,
                        "start_time"=>$rowgeteditdata->start_time,
                        "end_time"=>$rowgeteditdata->end_time,
                        "start_break"=>$rowgeteditdata->start_break,
                        "end_break"=>$rowgeteditdata->end_break,
                        "status"=>$rowgeteditdata->status,
                        "late_in_max"=>$rowgeteditdata->late_in_max,
                        "early_out_max"=>$rowgeteditdata->early_out_max
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
        DB::beginTransaction();
        try {
            $id_shift=strip_tags(base64_decode($request->post('id_shift')));
            $shift_code=strip_tags(base64_decode($request->post('shift_code')));
            $description=strip_tags(base64_decode($request->post('description')));
            $status=strip_tags(base64_decode($request->post('status')));
            $daytype=strip_tags(base64_decode($request->post('daytype')));
            $start_time=strip_tags(base64_decode($request->post('start_time')));
            $end_time=strip_tags(base64_decode($request->post('end_time')));
            $flexibletype=strip_tags(base64_decode($request->post('flexibletype')));
            $overlap=strip_tags(base64_decode($request->post('overlap')));
            $start_break=strip_tags(base64_decode($request->post('start_break')));
            $end_break=strip_tags(base64_decode($request->post('end_break')));
            $workingtime=strip_tags(base64_decode($request->post('workingtime')));
            $latein=strip_tags(base64_decode($request->post('latein')));
            $earlyout=strip_tags(base64_decode($request->post('earlyout')));
            
            $data = [
                'shift_code' => $shift_code,
                'description' => $description,
                'status' => $status,
                'day_type' => $daytype,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'flexible_shift' => $flexibletype=='0' ? false : true,
                'overlap_days' => $overlap=='0' ? false : true,
                'start_break' => $start_break=='' ? null : $start_break,
                'end_break' => $end_break=='' ? null : $end_break,
                'productive_work_time' => $workingtime,
                'late_in_max' => $latein,
                'early_out_max' => $earlyout,
                'id_company' => session('id_company'),
                'update_date' => date('Y-m-d H:i:s'),
                'updated_by' => session('id_user'),
            ];
            $editdata=$this->ShiftDaily->editdata($data, $id_shift);
            if(!$editdata){
                throw new \Exception('Update Failed');
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
            $id_shift=strip_tags(base64_decode($request->post('id_shift')));
            
            $deletedata=$this->ShiftDaily->deletedata($id_shift);
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
