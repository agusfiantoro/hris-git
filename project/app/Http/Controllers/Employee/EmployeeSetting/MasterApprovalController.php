<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\HrApprovalHeader;
use App\Models\Employee\EmployeeSetting\HrApprovalDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Log;

class MasterApprovalController extends Controller {

    
    public function index(Request $request) {

        if ($request->ajax()) {
            $data = HrApprovalHeader::getdata();
            //	dd($data);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_approval . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_approval . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
						/*	->addColumn('responsibility_menu', function($row) {
                                return $row->responsibility_menu;
                            }) */
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee_setting.approval_hierarchy.index');
    }
	
	protected function validateApprovalOrg(Request $request) {
	
        $request->validate([
            'description' => 'required|string',
        ],[],
		[
			'description' => 'Hierarchy Name',
		]);
    }
	protected function validateApproval(Request $request) {
        $arr_form_validate = [
            'description' => 'required|string',
            'approval.*.sequence' => 'required',
        //    'approval.*.id_employee' => 'required',
        ];
        $arr_msg_form_validate = [
            'description.required' => 'The Hierarchy Name field is required',
            'approval.*.sequence.required' => 'The Sequence field is required',
        //    'approval.*.id_employee.required' => 'The Employee field is required',
        ];
        if ($request->post('approval') == null) {
            $validate_approval = ['table_approval_detail' => 'required|string'];
            $validate_msg_approval = ['table_approval_detail.required' => 'Table Approval Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_approval);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_approval);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);     
    }
	
	 protected function save(Request $request) {
		 if($request->hierarchy_type != "Organization"){
			$this->validateApproval($request);
		 }
		 else{	
			$this->validateApprovalOrg($request);
		 }
	try{
			DB::beginTransaction();
        $form_data = array(
            'description' => $request->description,
            'hierarchy_type' => $request->hierarchy_type,
            'approval_mode' => $request->app_mode,
            'id_approval_doc_type' => $request->id_approval_doc_type,
			'enable_limit' => isset($request->enable_limit) == "on" ? 1 : 0,
            'limit' => $request->limit,
            'note' => $request->note,
            'status' => $request->status,
            'id_job_grade' => $request->id_job_grade,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
        $masterapproval = HrApprovalHeader::create($form_data);
	// dd($request->approval);
		if($request->hierarchy_type != "Organization"){
			foreach ($request->approval as $key => $value) {
				HrApprovalDetail::create(array(
					'id_approval' => $masterapproval->id_approval,
					'id_approval_mode' => $value['id_approval_mode'],
					'sequence' => $value['sequence'],
				 //   'id_employee' => $value['id_employee'],
					'id_position_detail' => $value['id_position_detail'],
					'limit' => $value['limit'],
					'note' => $value['note'],
					'status' => $value['status'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				));
			}
		}
		DB::commit();
        return response()->json(['status' => 'true', 'message' => 'Approval Hierarchy Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Saved Approval Hierarchy !! [' . $e->getMessage() . ']']);           
        }
    }
	
	 public function update(Request $request) {
        if($request->hierarchy_type != "Organization"){
			$this->validateApproval($request);
		 }
		 else{	
			$this->validateApprovalOrg($request);
		 }
	try{
			DB::beginTransaction();
        $form_data = array(
            'id_approval' => $request->id_approval,
            'description' => $request->description,
            'hierarchy_type' => $request->hierarchy_type,
			'approval_mode' => $request->app_mode,
			'id_approval_doc_type' => $request->id_approval_doc_type,
			'enable_limit' => isset($request->enable_limit) == "on" ? 1 : 0,
            'limit' => $request->limit,
            'note' => $request->note,
            'status' => $request->status,
			'id_job_grade' => $request->id_job_grade,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
            'approval' => $request->approval,
        );
        $masterapproval = HrApprovalHeader::findOrFail($request->id_approval)->update($form_data);
        $collect_approval = collect($form_data['approval'])->groupBy('id_approval_detail')->toArray();
        $list_approval = array_filter(array_keys($collect_approval));
		
		if($request->hierarchy_type != "Organization"){
			DB::delete("DELETE FROM  hr_approval_detail had
							WHERE had.id_approval = ? AND had.id_approval_detail NOT IN (" . implode(",", $list_approval) . ")", [$request->id_approval]);

			foreach ($request->approval as $key => $value) {
				if ($value['id_approval_detail'] == "") {
					HrApprovalDetail::create(array(
						'id_approval' => $request->id_approval,
						'id_approval_mode' => $value['id_approval_mode'],
						'sequence' => $value['sequence'],
					//	'id_employee' => $value['id_employee'],
						'id_position_detail' => $value['id_position_detail'],
						'limit' => $value['limit'],
						'note' => $value['note'],
						'status' => $value['status'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),               
					));
				} else {
					HrApprovalDetail::where('id_approval_detail', $value['id_approval_detail'])->update(array(
					//    'id_employee' => $value['id_employee'],
						'id_position_detail' => $value['id_position_detail'],
						'id_approval_mode' => $value['id_approval_mode'],
						'sequence' => $value['sequence'],
						'limit' => $value['limit'],
						'note' => $value['note'],
						'status' => $value['status'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
        }
		DB::commit();
        return response()->json(['status' => 'true', 'message' => 'Approval Hierarchy Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated Approval Hierarchy !! [' . $e->getMessage() . ']']);           
        }
    }

	
	 public function edit($id) {

        if (request()->ajax()) {
            $data = HrApprovalHeader::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	 public function destroy($id) {
        $data = HrApprovalHeader::findOrFail($id);
        HrApprovalDetail::where('id_approval', $id)->delete();
        $data->delete();
    }
	
/*	public function get_employee() {
        $result = HrApprovalHeader::get_employee();
        return response()->json($result);
    }
*/
	public function get_position_detail() {
        $result = HrApprovalHeader::get_position_detail();
        return response()->json($result);
    }
	
	public function get_approval_mode() {
        $result = HrApprovalHeader::get_approval_mode();
        return response()->json($result);
    }
	
	public function get_approval_doc() {
        $result = HrApprovalHeader::get_approval_doc();
        return response()->json($result);
    }
		
	public function get_approval_edit(Request $request) {
        $data = [
            'id_approval' => $request->id_approval
        ];
        $result = HrApprovalHeader::get_approval_edit($data);
        return response()->json($result);
    }

	
	public function get_company() {
        $result = HrApprovalHeader::get_company();
        return response()->json($result);
    }
	
	public function get_grade() {
        $result = HrApprovalHeader::get_grade();
        return response()->json($result);
    }

}
