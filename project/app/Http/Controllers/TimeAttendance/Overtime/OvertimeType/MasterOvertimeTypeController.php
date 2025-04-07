<?php

namespace App\Http\Controllers\TimeAttendance\Overtime\OvertimeType;

use App\Models\TimeAttendance\Overtime\OvertimeType\MasterOvertimeType;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Validator;
use Illuminate\Validation\Rule;

class MasterOvertimeTypeController extends Controller {

    public function index(Request $request) {
        return view('time_attendance.overtime.overtime_type.index');
    }
    
    public function get_data(Request $request) {
        $data = MasterOvertimeType::getdata();
        return DataTables::of($data)
                        ->addIndexColumn()
                        ->addColumn('', function($data) {
                            $a = '';
                            return $a;
                        })
                        ->addColumn('action', function($data) {
                            $button = '<button type="button" name="edit" id="' . $data->id_overtime_type . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                            $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_overtime_type . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                            return $button;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
    }
	
	protected function save(Request $request) {
        $request->validate([
            'overtime_code' => 'required', Rule::unique('master_overtime_type')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'minimum_time' => 'required',
            'maximum_time' => 'required',
            'multiple_value' => 'required',
                ], [],
                [
                    'overtime_code' => 'Overtime Code',
                    'minimum_time' => 'Minimum Time',
                    'maximum_time' => 'Maximum Time',
                    'multiple_value' => 'Multiple Value',
        ]);

        $form_data = array(
            'overtime_code' => $request->overtime_code,
            'description' => $request->description,
            'minimum_time' => $request->minimum_time,
            'maximum_time' => $request->maximum_time,
            'multiple_value' => $request->multiple_value,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'created_by' => session('id_user'),
        );
        MasterOvertimeType::create($form_data);
        return response()->json(['status' => 'true', 'message' => 'Master Overtime Type Saved Successfully !!']);
    }
	
	public function destroy($id) {
        $data = MasterOvertimeType::findOrFail($id);
        $data->delete();
    }
	
	public function update(Request $request) {
        $form_data = array(
            'overtime_code' => $request->overtime_code,
            'description' => $request->description,
            'minimum_time' => $request->minimum_time,
            'maximum_time' => $request->maximum_time,
            'multiple_value' => $request->multiple_value,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'inactive_date' => $request->inactive_date,
            'updated_by' => session('id_user'),
        );

        MasterOvertimeType::findOrFail($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Master Overtime Type Updated successfully']);
    }
	
	public function edit($id) {

        if (request()->ajax()) {
            $data = MasterOvertimeType::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	 public function get_company() {
        $result = MasterOvertimeType::get_company();
        return response()->json($result);
    }

}
