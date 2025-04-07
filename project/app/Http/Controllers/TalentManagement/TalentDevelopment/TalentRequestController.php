<?php

namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\TalentManagement\TalentRequest\TalentRequest;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TalentRequestController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
			$data_access = Employee::get_access($request->id_url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}
			$dept_code = TalentRequest::get_dept_code();
			if(count($dept_code) > 0){
				$c = $dept_code[0]->department_code;
			}
			else{
				$c = null;
			}
            $data = TalentRequest::getdata($group_branch,$c);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
						$onclick = "loadedit(".$data->id_talent_assessment_request.")";
						$buttonSubmit = '&nbsp;<button type="button" name="submit" id="' . $data->id_talent_assessment_request . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
                        $buttonEdit = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
						$buttonView = '&nbsp;<button type="button" name="view" onclick="'.$onclick.'" class="edit btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
						$buttonCancel = '&nbsp;<button type="button" name="cancel" id="' . $data->id_talent_assessment_request . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
                        
						if(in_array($data->code_app_status, ['New','Revised'])){
							$returnButton = $buttonSubmit.$buttonEdit.$buttonCancel;
						} else {
							$returnButton = $buttonSubmit.$buttonView.$buttonCancel;
						}
						return $returnButton;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('talent_management.talent_development.assessment_request.index');
    }
	
	public function modal_detail(Request $request) {
		$global_talent = $request->global_talent;
        return view('talent_management.talent_development.assessment_request.modal_detail', compact('global_talent'));
    }
	

	public function get_employee_by() {
        $result = TalentRequest::get_employee_by();
        return response()->json($result);
    }
	public function get_hr_email() {
        $result = TalentRequest::get_hr_email();
	//	dd($result);
        return response()->json($result);
    }
	
	/*
	public function get_hierachy_talent(Request $request) {
		$req = ApprovalTransaction::get_user_req();
		$req_location = $req[0]->id_location;
		$data = [
            'code' => 'Assessment_Request',
        ];
        $result = ApprovalTransaction::get_hierachy($data,$req_location);
        return response()->json($result);
    }
	*/
	public function get_all_hierachy(Request $request) {
	//	dd($request->all());
		$data = [
            'id_employee' => $request->id_employee,
            'id_company' => session('id_company'),
        //    'id_approval' => $request->id_approval,
        ];
        $result = TalentRequest::get_app_combine($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_approval_status() {
        $result = TalentRequest::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	
	protected function validateReq(Request $request) {
        $arr_form_validate = [
			'notes' => 'required',
			'cc_email' => 'required',
			'id_approval_request' => 'required',
        ];
        $arr_msg_form_validate = [
			'notes.required' => 'The Notes field is required',
			'cc_email.required' => 'The CC Email field is required',
			'id_approval_request.required' => 'The Approval Name field is required',
        ];
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$kode = TalentRequest::getkode();
			$form_data = array(
				'reference_number' => $kode,
				'id_employee_request' => $request->id_employee_request,
			//	'id_approval' => $request->id_approval,
				'id_approval_request' => $request->id_approval_request,
				'id_approval_status' => $request->id_approval_status,
				'notes' => $request->notes,
				'cc_email_to' => implode(',',$request->cc_email),
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			
			$TalentSave = TalentRequest::create($form_data);
			$data = [
				'id_employee' => $request->id_employee_request,
				'id_company' => session('id_company'),
			//	'id_approval' => $request->id_approval,
			];
			$app = TalentRequest::get_approval();
			$approve = TalentRequest::get_app_combine($data);
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $TalentSave->id_talent_assessment_request;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
			}
			foreach ($source as $key => $value) {			
				 $form_trans = array(
					'id_source_transaction' => $value['id_source_transaction'],				
					'source_transaction_type' => $value['source_transaction_type'],
					'id_approval' => $value['id_approval'],
					'id_approval_detail' => $value['id_approval_detail'],
					'sequence' => $value['sequence'],
					'id_employee_approval' => $value['id_employee_approval'],
					'id_approval_status' => $value['id_approval_status'],
					'id_approval_mode' => $value['id_approval_mode'],
					'id_position_detail' => $value['id_position_detail'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				 );
				
				$at = ApprovalTransaction::create($form_trans);							
			}
			DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Assessment Request Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Assessment Request !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
	//	dd($request->all());
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_employee_request' => $request->id_employee_request,
				'id_approval_request' => $request->id_approval_request,
				'id_approval_status' => $request->id_approval_status,
				'notes' => $request->notes,
				'cc_email_to' => implode(',',$request->cc_email),
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
			
			TalentRequest::findOrFail($request->id_talent_assessment_request)->update($form_data);
			$data = [
				'id_employee' => $request->id_employee_request,
				'id_company' => session('id_company'),
			//	'id_approval' => $request->id_approval,
			];
			$app = TalentRequest::get_approval();
			$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_talent_assessment_request)->where('source_transaction_type', 'Assessment_Request')->delete();
			
			$approve = TalentRequest::get_app_combine($data);
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $request->id_talent_assessment_request;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
			}
			foreach ($source as $key => $value) {			
				 $form_trans = array(
					'id_source_transaction' => $value['id_source_transaction'],				
					'source_transaction_type' => $value['source_transaction_type'],
					'id_approval' => $value['id_approval'],
					'id_approval_detail' => $value['id_approval_detail'],
					'sequence' => $value['sequence'],
					'id_employee_approval' => $value['id_employee_approval'],
					'id_approval_status' => $value['id_approval_status'],
					'id_approval_mode' => $value['id_approval_mode'],
					'id_position_detail' => $value['id_position_detail'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				 );
				
				$at = ApprovalTransaction::create($form_trans);							
			}
			
			DB::commit();
				return response()->json(['status' => 'true', 'data'=>$request->id_talent_assessment_request, 'message' => 'Assessment Request Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Update Assessment Request !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	public function submit_approve($id) {
			try{
				DB::beginTransaction();
			$id_approval = TalentRequest::where('id_talent_assessment_request', $id)->first();
			$approve = TalentRequest::submit_approve();
		//	dd($approve);
				TalentRequest::where('id_talent_assessment_request', $id)->update(array(
					'id_approval_status' => $approve->id_general_data,
				));	
				
			$data = [
				'id_talent_assessment_request' => $id,
				'id_approval' => $id_approval->id_approval
				];
			$data_status = TalentRequest::getdata_approval_status($data);
			foreach ($data_status as $key => $value) {
				if($value->code == "New" || $value->code == "Cancel" || $value->code == "Revised"){		
					 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', $value->source_transaction_type)->update(array(
						'id_approval_status' => $approve->id_general_data,
					//	'updated_by' => session('id_user'),
					));	
				}	
			}

			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Approval Status Submit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Approval Status Submit !! [' . $e->getMessage() . ']']); 
		}		
	}
	
			
	public function get_talent_edit(Request $request) {
        $data = [
            'id_talent' => $request->id_talent,
        ];
        $result = TalentRequest::get_talent_edit($data);
		$result['cc_email'] = explode(',',$result['cc_email_to']);
        return response()->json($result);
    }
	
	public function cancel($id) {
		try{
			DB::beginTransaction();
		$id_approval = TalentRequest::where('id_talent_assessment_request', $id)->first();	
		$cancel = TalentRequest::cancel();
        TalentRequest::where('id_talent_assessment_request', $id)->update(array(
			'id_approval_status' => $cancel->id_general_data,
		));
		ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Assessment_Request')->update(array(
			'id_approval_status' => $cancel->id_general_data,
			 'updated_by' => session('id_user'),
		));	

		DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
}
