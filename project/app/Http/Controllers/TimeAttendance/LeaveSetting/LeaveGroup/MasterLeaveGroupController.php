<?php

namespace App\Http\Controllers\TimeAttendance\LeaveSetting\LeaveGroup;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveGroup;
use App\Models\TimeAttendance\LeaveSetting\LeaveGroup\MasterLeaveDetail;
use App\Models\TimeAttendance\LeaveSetting\LeaveType\MasterLeaveType;
use App\Models\GeneralSetting\CompanySetting\Company;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;

class MasterLeaveGroupController extends Controller {

    
    public function index(Request $request) {
        return view('time_attendance.leave_setting.leave_group.index');
    }

    public function get_data(Request $request) {
        $data = MasterLeaveGroup::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_leave_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_leave_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
	
	 protected function validateLeave(Request $request) {

        $arr_form_validate = [
            'leave_group_name' => 'required|string',
            'leave.*.id_leave_type' => 'required',
        ];
        $arr_msg_form_validate = [
            'leave_group_name.required' => 'The Leave Group field is required',
            'leave.*.id_leave_type.required' => 'The Leave Type field is required',
        ];
        if ($request->post('leave') == null) {
            $validate_leave = ['table_leave_detail' => 'required|string'];
            $validate_msg_leave = ['table_leave_detail.required' => 'Table Leave Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_leave);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_leave);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	 protected function save(Request $request) {
        $this->validateLeave($request);
        $form_data = array(
            'leave_group_name' => $request->leave_group_name,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
        $masterleavegroup = MasterLeaveGroup::create($form_data);
        foreach ($request->leave as $key => $value) {
            MasterLeaveDetail::create(array(
                'id_leave_header' => $masterleavegroup->id_leave_header,
                'id_leave_type' => $value['id_leave_type'],
                'leave_quota' => $value['leave_quota'],
                'status' => $value['status'],
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            ));
        }
        return response()->json(['status' => 'true', 'message' => 'Leave Group Saved Successfully !!']);
    }
	
	 public function update(Request $request) {
        $this->validateLeave($request);
        $form_data = array(
            'id_leave_header' => $request->id_leave_header,
            'leave_group_name' => $request->leave_group_name,
            'description' => $request->description,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
            'leave' => $request->leave,
        );
        $masterleavegroup = MasterLeaveGroup::findOrFail($request->id_leave_header)->update($form_data);
        $collect_leave = collect($form_data['leave'])->groupBy('id_leave_detail')->toArray();
        $list_leave = array_filter(array_keys($collect_leave));

        DB::delete("DELETE FROM  master_leave_detail mlm
                        WHERE mlm.id_leave_header = ? AND mlm.id_leave_detail NOT IN (" . implode(",", $list_leave) . ")", [$request->id_leave_header]);

        foreach ($request->leave as $key => $value) {
            if ($value['id_leave_detail'] == "") {
                MasterLeaveDetail::create(array(
                    'id_leave_header' => $request->id_leave_header,
                    'id_leave_type' => $value['id_leave_type'],
                    'leave_quota' => $value['leave_quota'],
                    'status' => $value['status'],
					'id_company' => session('id_company'),
                    'created_by' => session('id_user'),
                ));
            } else {
                MasterLeaveDetail::where('id_leave_detail', $value['id_leave_detail'])->update(array(
                    'id_leave_type' => $value['id_leave_type'],
                    'leave_quota' => $value['leave_quota'],
                    'status' => $value['status'],
					'id_company' => session('id_company'),
                    'updated_by' => session('id_user'),
                ));
            }
        }
        return response()->json(['status' => 'true', 'message' => 'Leave Group Updated Successfully !!']);
    }

	
	 public function edit($id) {

        if (request()->ajax()) {
            $data = MasterLeaveGroup::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	 public function destroy($id) {
        $data = MasterLeaveGroup::findOrFail($id);
        MasterLeaveDetail::where('id_leave_header', $id)->delete();
        $data->delete();
    }
	
	public function get_leave_type() {
        $result = MasterLeaveGroup::get_leave_type();
        return response()->json($result);
    }
	
	public function get_leave_edit(Request $request) {
        $data = [
            'id_leave_header' => $request->id_leave_header
        ];
        $result = MasterLeaveGroup::get_leave_edit($data);
        return response()->json($result);
    }

	
	public function get_company() {
        $result = MasterLeaveGroup::get_company();
        return response()->json($result);
    }

}
