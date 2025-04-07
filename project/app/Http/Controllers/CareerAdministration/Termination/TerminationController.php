<?php

namespace App\Http\Controllers\CareerAdministration\Termination;

use App\Models\CareerAdministration\Termination\Termination;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TerminationController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = Termination::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {								
                                $button = '<button type="button" name="submit" id="' . $data->id_termination . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
								$button .= '<button type="button" name="edit" id="' . $data->id_termination . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';                          
								$button .= '<button type="button" name="cancel" id="' . $data->id_termination . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
								
								 /*    $button .= '<button type="button" name="delete" id="' . $data->id_termination . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
								*/
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('career_administration.career_transition.termination.index');
    }

    protected function save(Request $request) {
        $request->validate([
			'reference_number' => 'unique:hr_termination_request', Rule::unique('hr_termination_request')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
			'id_employee' => 'required',
            'enable_approval' => 'required',
            'effective_resign_date' => 'required',
            'id_position_detail' => 'required',
            'attachment' => 'max:1024',
                ], [],
                [
					'id_employee' => 'Employee',
                    'reference_number' => 'Reference Number',
                    'enable_approval' => 'Enable Approval',
                    'effective_resign_date' => 'Effective Resign Date',
                    'id_position_detail' => 'New Position Detail',
        ]);
	try{
			DB::beginTransaction();	
		if($request->attachment != ""){
			$image = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/termination/'.$request->nik_employee,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/termination/'.$request->nik_employee,$value['attachment'],$image);
		}
		else{
			$image = NULL;
		}
		$kode = Termination::getkode();
        $form_data = array(
            'reference_number' => $kode,
            'id_employee' => $request->id_employee,
            'id_transition_category' => $request->id_transition_category,
            'id_transaction_type' => $request->id_transaction_type,
            'id_employment_status' => $request->id_employment_status,
            'id_position_routing' => $request->id_position_routing,
            'id_position_detail' => $request->id_position_detail,
            'id_job_grade' => $request->id_job_grade,
            'id_job_status' => $request->id_job_status,
            'id_location' => $request->id_location,
            'remark' => $request->remark,
			'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
            'id_approval' => $request->id_approval,
			'id_approval_status' => $request->id_approval_status,
            'effective_resign_date' => $request->effective_resign_date,
            'expired_date' => $request->expired_date,            
            'attachment' => $image,
			'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		
			$termination = Termination::create($form_data);
		/*	
			if(strtotime($termination->effective_resign_date) <= strtotime(date('Y-m-d'))){
				JobPositionDetail::where('id_position_detail', $termination->id_old_position_detail)->update(array(
							'id_employee' => NULL,
						));	
				JobPositionDetail::where('id_position_detail', $termination->id_position_detail)->update(array(
					'id_employee' => $termination->id_employee,
				));	
			}
		*/
		
		$app = ApprovalTransaction::get_approval($request->id_approval);
	//	dd($app);
		if($app[0]->hierarchy_type == "Organization"){
			$approve = ApprovalTransaction::get_app_org($request->id_employee,session('id_company'));
		}
		else if($app[0]->hierarchy_type == "Combine"){
			$approve = ApprovalTransaction::get_app_combine($request->id_employee,session('id_company'),$request->id_approval);
		}
		else if($app[0]->hierarchy_type == "Custom"){
			$approve = ApprovalTransaction::get_app_custom($request->id_employee,session('id_company'),$request->id_approval);
		}
		foreach ($approve as $key => $value) {
				$source[$key]['id_source_transaction'] = $termination->id_termination;
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
			ApprovalTransaction::create(array(
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
			));
		}
				
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Termination Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Termination !! [' . $e->getMessage() . ']']);           
        }
    }

    public function update(Request $request) {
		 $request->validate([
			'id_employee' => 'required',
			'enable_approval' => 'required',
            'effective_resign_date' => 'required',
            'attachment' => 'max:1024',
                ], [],
                [
					'id_employee' => 'Employee',
					'enable_approval' => 'Enable Approval',
                    'effective_resign_date' => 'Effective Resign Date',
        ]);
		
		try{
			DB::beginTransaction();
		if($request->attachment != ""){
			$image = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/termination/'.$request->nik_employee,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/termination/'.$request->nik_employee,$value['attachment'],$image);
					
					$form_data = array(
						'id_employee' => $request->id_employee,
						'id_transition_category' => $request->id_transition_category,
						'id_transaction_type' => $request->id_transaction_type,
						'id_position_routing' => $request->id_position_routing,
						'id_position_detail' => $request->id_position_detail,
						'id_job_grade' => $request->id_job_grade,
						'id_job_status' => $request->id_job_status,
						'id_location' => $request->id_location,
						'remark' => $request->remark,
						'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
						'id_approval' => $request->id_approval,
						'id_approval_status' => $request->id_approval_status,
						'id_company_destination' => $request->id_company_destination,
						'effective_resign_date' => $request->effective_resign_date,
						'expired_date' => $request->expired_date,            
						'attachment' => $image,
						'status' => $request->status,
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
				);
		}
		else{
					$form_data = array(
						'id_employee' => $request->id_employee,
						'id_transition_category' => $request->id_transition_category,
						'id_transaction_type' => $request->id_transaction_type,
						'id_position_routing' => $request->id_position_routing,
						'id_position_detail' => $request->id_position_detail,
						'id_job_grade' => $request->id_job_grade,
						'id_job_status' => $request->id_job_status,
						'id_location' => $request->id_location,
						'remark' => $request->remark,
						'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
						'id_approval' => $request->id_approval,
						'id_approval_status' => $request->id_approval_status,
						'id_company_destination' => $request->id_company_destination,
						'effective_resign_date' => $request->effective_resign_date,
						'expired_date' => $request->expired_date,            
						'status' => $request->status,
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
				);
		}
		      
		
			$termination = Termination::findOrFail($request->id_termination)->update($form_data);
			
		/*	if(strtotime($termination->effective_resign_date) <= strtotime(date('Y-m-d'))){
				JobPositionDetail::where('id_position_detail', $termination->id_old_position_detail)->update(array(
							'id_employee' => NULL,
						));	
				JobPositionDetail::where('id_position_detail', $termination->id_position_detail)->update(array(
					'id_employee' => $termination->id_employee,
				));	
			}
		*/
			
		$app = ApprovalTransaction::get_approval($request->id_approval);
		$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_termination)->delete();
	//	dd($app);
		if($app[0]->hierarchy_type == "Organization"){
			$approve = ApprovalTransaction::get_app_org($request->id_employee,session('id_company'));
		}
		else if($app[0]->hierarchy_type == "Combine"){
			$approve = ApprovalTransaction::get_app_combine($request->id_employee,session('id_company'),$request->id_approval);
		}
		else if($app[0]->hierarchy_type == "Custom"){
			$approve = ApprovalTransaction::get_app_custom($request->id_employee,session('id_company'),$request->id_approval);
		}
		foreach ($approve as $key => $value) {
				$source[$key]['id_source_transaction'] =  $request->id_termination;
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
			ApprovalTransaction::create(array(
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
			));
		}
				
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Termination Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated Termination !! [' . $e->getMessage() . ']']);           
        }
    }

    public function transition(Request $request) {
            $termination = Termination::transition_career();      
			foreach($termination as $key=>$value){
				if($value->effective_resign_date == date('Y-m-d') && $value->code == 'Approved'){
					JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->update(array(
						'id_employee' => NULL,
					));	
					JobPositionDetail::where('id_position_detail', $value->id_position_detail)->update(array(
						'id_employee' => $value->id_employee,
					));	
				}
			}
    }
	
	public function submit_approve($id) {
		try{
			DB::beginTransaction();
		 $id_approval = Termination::where('id_termination', $id)->first();
	//	 dd($id_approval);		
		$approve = Termination::submit_approve();
		$form_data = array(
			'reference_number' => $id_approval->reference_number,
            'id_employee' => $id_approval->id_employee,
            'id_transition_category' => $id_approval->id_transition_category,
            'id_transaction_type' => $id_approval->id_transaction_type,
            'id_employment_status' => $id_approval->id_employment_status,
            'id_old_position_detail' => $id_approval->id_position_detail,
            'remark' => $id_approval->remark,
			'enable_approval' => $id_approval->enable_approval,
            'id_approval' => $id_approval->id_approval,
			'id_approval_status' => $approve->id_general_data,
            'effective_date' => $id_approval->effective_resign_date,
            'attachment' => $id_approval->attachment,
			'status' => $id_approval->status,
            'id_company' => $id_approval->id_company,
            'created_by' => session('id_user'),
        );
		$apptrans = CareerTransition::where('reference_number', $id_approval->reference_number)->delete();
		$career = CareerTransition::create($form_data);

			Termination::where('id_termination', $id)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));		
		$data = [
            'id_termination' => $id,
            'id_approval' => $id_approval->id_approval
			];
		$data_status = Termination::getdata_approval_status($data);
		foreach ($data_status as $key => $value) {
			if($value->code == "New" || $value->code == "Cancel"){		
				 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Termination_Request')->update(array(
					'id_approval_status' => $approve->id_general_data,
					'updated_by' => session('id_user'),
				));	
			}
		}
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Approval Status Submit Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Approval Status Submit Termination !! [' . $e->getMessage() . ']']);           
        }
    }
	public function cancel($id) {
		$id_approval = Termination::where('id_termination', $id)->first();
		$cancel = Termination::cancel();
        Termination::where('id_termination', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
		CareerTransition::where('reference_number', $id_approval->reference_number)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
	
		 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Termination_Request')->update(array(
					'id_approval_status' => $cancel->id_general_data,
					'updated_by' => session('id_user'),
				));	
    }
	public function edit($id) {
        if (request()->ajax()) {
            $data = Termination::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	public function get_career_edit(Request $request) {
        $data = [
            'id_termination' => $request->id_termination
        ];
        $result = Termination::get_career_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	/*
	 public function destroy($id) {
        $data = Termination::findOrFail($id);
		ApprovalTransaction::where('id_source_transaction', $id)->delete();
        $data->delete();
    }
	*/
	public function get_employee() {
        $result = Termination::get_employee();
        return response()->json($result);
    }
	
	public function get_position(Request $request) {
        $data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Termination::get_position($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_position_detail(Request $request) {
        $data = [
         //   'id_employee' => $request->id_employee,
            'id_position_detail' => $request->id_position_detail,
        ];	
        $result = Termination::get_position_detail($data);
	//	dd($result);
        return response()->json($result);
    }
		
	public function get_career_category(Request $request) {
        $result = Termination::get_career_category();
        return response()->json($result);
    }
	
	public function get_career_type(Request $request) {
        $data = [
            'id' => $request->id
        ];	
	//	dd($data);
        $result = Termination::get_career_type($data);
        return response()->json($result);
    }

    public function get_company() {
        $result = Termination::get_company();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_hierachy(Request $request) {
		$data = [
            'code' => $request->code
        ];
        $result = Termination::get_hierachy($data);
        return response()->json($result);
    }
	public function get_approval_status() {		
        $result = Termination::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	
}
