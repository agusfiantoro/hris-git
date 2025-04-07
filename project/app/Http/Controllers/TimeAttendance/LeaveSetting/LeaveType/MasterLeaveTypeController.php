<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveType;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveType;
use App\Models\GeneralSetting\CompanySetting\Company;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;


class MasterLeaveTypeController extends Controller {

    public function index(Request $request) {
        return view('time_attendance.leave_setting.leave_type.index');
    }
    
    public function get_data(Request $request) {
        $data = MasterLeaveType::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_leave_type . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_leave_type . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
	
	protected function save(Request $request) {
        $request->validate([
			'leave_code' => ['required', Rule::unique('master_leave_type')->where('id_company',session('id_company'))]
                ], [],
                [
                    'leave_code' => 'Leave Code',
        ]);

        $form_data = array(
            'leave_code' => $request->leave_code,
            'description' => $request->description,
            'deduct_leave' => isset($request->deduct_leave) == "on" ? 1 : 0,
            'day_count' => $request->day_count,
            'leave_day_type' => $request->leave_day_type,
            'repeat_period' => isset($request->repeat_period) == "on" ? 1 : 0,
            'repeat_period_number' => $request->repeat_period_number,
            'leave_entitlement_period_start' => $request->leave_entitlement_period_start,
            'available_leave_after' => $request->available_leave_after,
        //    'repeated' => $request->repeated,
            'leave_valid_end_period' => $request->leave_valid_end_period,
            'grace_period_req' => $request->grace_period_req,
            'carry_over_to_next_entitlement' => isset($request->carry_over_to_next_entitlement) == "on" ? 1 : 0,
            'limit_carry_over' => isset($request->limit_carry_over) == "on" ? 1 : 0,
            'max_carry_over' => $request->max_carry_over,
            'enable_minus_leave' => isset($request->enable_minus_leave) == "on" ? 1 : 0,
            'max_minus_leave' => $request->max_minus_leave,
            'day_limit_submit_request' => $request->day_limit_submit_request,
            'if_over_day_limit' => $request->if_over_day_limit,
            'req_attachment' => isset($request->req_attachment) == "on" ? 1 : 0,          
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterLeaveType::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Master Leave Type Saved Successfully !!']);
    }
	
	public function destroy($id) {
        $data = MasterLeaveType::findOrFail($id);
        $data->delete();
    }
	
	public function update(Request $request) {
        $form_data = array(
            'leave_code' => $request->leave_code,
            'description' => $request->description,
            'deduct_leave' => isset($request->deduct_leave) == "on" ? 1 : 0,
            'day_count' => $request->day_count,
            'leave_day_type' => $request->leave_day_type,
            'repeat_period' => isset($request->repeat_period) == "on" ? 1 : 0,
            'repeat_period_number' => $request->repeat_period_number,
            'leave_entitlement_period_start' => $request->leave_entitlement_period_start,
            'available_leave_after' => $request->available_leave_after,
        //    'repeated' => $request->repeated,
            'leave_valid_end_period' => $request->leave_valid_end_period,
            'grace_period_req' => $request->grace_period_req,
            'carry_over_to_next_entitlement' => isset($request->carry_over_to_next_entitlement) == "on" ? 1 : 0,
            'limit_carry_over' => isset($request->limit_carry_over) == "on" ? 1 : 0,
            'max_carry_over' => $request->max_carry_over,
            'enable_minus_leave' => isset($request->enable_minus_leave) == "on" ? 1 : 0,
            'max_minus_leave' => $request->max_minus_leave,
            'day_limit_submit_request' => $request->day_limit_submit_request,
            'if_over_day_limit' => $request->if_over_day_limit,
            'req_attachment' => isset($request->req_attachment) == "on" ? 1 : 0,          
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterLeaveType::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Master Leave Type Updated successfully']);
    }
	
	public function edit($id) {

        if (request()->ajax()) {
            $data = MasterLeaveType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	public function get_company() {
        $result = MasterLeaveType::get_company();
        return response()->json($result);
    }

}
