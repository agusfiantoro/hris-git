<?php

namespace App\Http\Controllers\Employee\Contract;

use App\Models\Employee\Contract\Contract;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller {

    public function index(Request $request) {
	//	$codeid = Contract::getkode_ctr();
	//	dd($codeid);
    	$id_employee 	= [];
    	if(session('access_group') != 'Default_Administrator'){
            $path_menu 		= 'employee/employee_setting/workdays';
        	$id_branch      = [];
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu);
            if(count($get_ur) > 0){
                $get_ur = $get_ur[0];
                $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->id_user_responsibility);
                if(count($get_branch) > 0){
                    $id_branch        = $get_branch->pluck('id_branch')->all();
                }
            }
            if(count($id_branch) > 0){
                $id_employee = JobPositionDetail::whereIn('id_branch', $id_branch)->pluck('id_employee')->all();
            }
        }

		$getEmployeeContract 	= Contract::getEmployeeContract($id_employee);
		$getContractNumber 		= Contract::getContractNumber($id_employee);
		$employee_contract 		= [];
		$contract_number 		= [];

		if(count($getEmployeeContract) > 0){
			$getEmployeeContract = $getEmployeeContract->sortBy('name')->unique('nik_employee')->all();
			foreach ($getEmployeeContract as $key => $val) {
				$employee_contract[] = [
					'id' => $val->nik_employee,
					'text' => $val->name. ' ('.$val->nik_employee.')',
				];
			}
		}
		if(count($getContractNumber) > 0){
			$getContractNumber = $getContractNumber->sortBy('contract_number')->values()->all();
			foreach ($getContractNumber as $key => $val) {
				$contract_number[] = [
					'id' => $val->id_contract,
					'text' => $val->contract_number,
				];
			}
		}
		
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
            $data = Contract::getdata($group_branch);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_contract . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_contract . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })->addColumn('employee_name', function($row) {
                                return $row->employee_name;
                            })->addColumn('category', function($row) {
                                return $row->category;
                            })->addColumn('working_time', function($row) {
                                return $row->working_time;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee.contract.index', compact('employee_contract', 'contract_number'));
    }

    protected function save(Request $request) {
		
       $arr_form_validate =[
		 'contract_number' => 'required|string|unique:hr_contract_employee', Rule::unique('hr_contract_employee')->where(function ($query) {
					return $query->where('id_company', session('id_company'));
				}),
		'effective_date' => 'required',
	//    'notice_period' => 'numeric',
		];
		
		if($request->attachment != null){
			$arr_form_validate['attachment_type'] = 'required';
		}
		if($request->attachment_type == 'image'){
			$arr_form_validate['attachment'] = 'required|mimes:jpg,jpeg,png|max:2016';
		}
		else if($request->attachment_type == 'pdf'){
			$arr_form_validate['attachment'] = 'required|mimes:pdf|max:2016';
		}

		$request->validate($arr_form_validate);
		
	try{
		DB::beginTransaction();
		if($request->attachment != ""){
		//	$image_file = file_get_contents($request->attachment);
		//	$image = base64_encode($image_file);
		//	$attachment_type = $request->attachment_type;
			$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/contract/'.$nik_employee['nik_employee'],0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/contract/'.$nik_employee['nik_employee'],$request->attachment,$image);
			$is_upload = 'true';
		}
		else{
			$image = NULL;
			$is_upload = 'false';
		}
		$codeid = Contract::getkode_ctr();
        $form_data = array(
            'contract_number' => $request->contract_number,
            'reference_number' => $codeid,
            'id_employee' => $request->id_employee,
            'id_contract_category' => $request->id_contract_category,
            'id_salary_structure' => $request->id_salary_structure,
            'notice_period' => $request->notice_period,
            'id_working_schedule' => $request->id_working_schedule,
            'schedule_payroll' => $request->schedule_payroll,
            'effective_date' => $request->effective_date,
            'expired_date' => $request->expired_date,
			'attachment_type' => NULL,
            'attachment' => $image,
            'is_upload' => $is_upload,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
				
			Contract::create($form_data);
			
		
		$career_category = CareerTransition::get_career_category();
		foreach($career_category as $key=>$value){
			if($value->code == 'Join'){
				$join[] = $value->id;
			}				
		}
		$data_category = [
            'id' => $join[0],
            'var_type' => null,
        ];

		$career_type = CareerTransition::get_career_type($data_category);
				
		if($career_type[0]->text == "New Employee"){
			Employee::where('id_employee', $request->id_employee)->update([
				'expired_date' => $request->expired_date,
			]);
			$cek_status = Contract::get_emp_status($request->id_employee);
			if($cek_status->code =='Permanent'){
				Employee::where('id_employee', $request->id_employee)->update([
					'permanent_date' => $request->effective_date,
				]);
			}	
			$career_cek = CareerTransition::where('id_employee', $request->id_employee)->where('id_transition_category', $join[0])->first();
			if($career_cek){
				CareerTransition::where('id_employee', $request->id_employee)->where('id_transition_category', $join[0])->update([
					// 'reference_number' => $request->contract_number,
					'transaction_number' => $request->contract_number,
					'id_old_employment_status' => $request->id_contract_category,
					'id_employment_status' => $request->id_contract_category,
					'effective_date' => $request->effective_date,
					'expired_date' => $request->expired_date,
					'updated_by' => session('id_user'),
				]);
			}
			else{							
				$data_employee = [
					'id_employee' => $request->id_employee
				];	
				$contract_position = Contract::get_position($data_employee);
				$contract_approve = Contract::submit_approve();
				$form_career = array(
					'reference_number' => $request->contract_number,
					'transaction_number' => $request->contract_number,
					'id_employee' => $request->id_employee,
					'id_transition_category' => $join[0],
					'id_transaction_type' => $career_type[0]->id,
					'id_employment_status' => $request->id_contract_category,
					'id_old_employment_status' => $request->id_contract_category,
					'id_position_detail' => $contract_position[0]->id_position_detail,
					// 'id_old_position_detail' => $contract_position[0]->id_position_detail,
					'remark' => "New Employee",
					'id_approval_status' => $contract_approve->id_general_data,
					'effective_date' => $request->effective_date,
					'expired_date' => $request->expired_date,            
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				$career = CareerTransition::create($form_career);
			}
		}
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Contract Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Contract !! [' . $e->getMessage() . ']']);           
        }
    }

    public function destroy($id) {
        $data = Contract::findOrFail($id);
	//	CareerTransition::where('reference_number', $data->contract_number)->delete();
        $data->delete();
    }

    public function update(Request $request) {
		 $arr_form_validate =[
		'effective_date' => 'required',
		];
		
		if($request->attachment != null){
			$arr_form_validate['attachment_type'] = 'required';
		}
		if($request->attachment_type == 'image'){
			$arr_form_validate['attachment'] = 'required|mimes:jpg,jpeg,png|max:2016';
		}
		else if($request->attachment_type == 'pdf'){
			$arr_form_validate['attachment'] = 'required|mimes:pdf|max:2016';
		}

		$request->validate($arr_form_validate);
		
	try{
		DB::beginTransaction();
		$form_data = array(
				// 'contract_number' => $request->contract_number,
				// 'id_employee' => $request->id_employee,
				// 'id_contract_category' => $request->id_contract_category,
				// 'id_salary_structure' => $request->id_salary_structure,
				// 'notice_period' => $request->notice_period,
				// 'id_working_schedule' => $request->id_working_schedule,
				// 'schedule_payroll' => $request->schedule_payroll,
				'effective_date' => $request->effective_date,
				'expired_date' => $request->expired_date,            
				// 'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
		if($request->attachment != ""){
			//	$image_file = file_get_contents($request->attachment);
			//	$image = base64_encode($image_file);								
				$nik_employee = Employee::where('id_employee', $request->id_employee)->first();
				$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
				$image = $rnd."-".$nik_employee['nik_employee'].".".$request->attachment->getClientOriginalExtension();
				$dir = Storage::makeDirectory('public/upload/contract/'.$nik_employee['nik_employee'],0775, true, true);
				$storageimage = Storage::putFileAs('public/upload/contract/'.$nik_employee['nik_employee'],$request->attachment,$image);
				$form_data['attachment_type'] = NULL;
				$form_data['attachment'] = $image;
				$form_data['is_upload'] = 'true';
		}
		
        Contract::findOrFail($request->hidden_id)->update($form_data);
		Employee::where('id_employee', $request->id_employee)->update([
				'expired_date' => $request->expired_date,
			]);
		
	//	$data_contract = Contract::findOrFail($request->hidden_id);
		// CareerTransition::where('reference_number', $data_contract->contract_number)->delete();
		
		$career_category = CareerTransition::get_career_category();
		foreach($career_category as $key=>$value){
			if($value->code == 'Join'){
				$join[] = $value->id;
			}				
		}
		$data_category = [
            'id' => $join[0],
            'var_type' => null,
        ];	
		$career_type = CareerTransition::get_career_type($data_category);
		
		$data_employee = [
            'id_employee' => $request->id_employee
        ];	
        $contract_position = Contract::get_position($data_employee);
		$contract_approve = Contract::submit_approve();
		$form_career = array(
   //          'reference_number' => $request->contract_number,
   //          'id_employee' => $request->id_employee,
   //          'id_transition_category' => $join[0],
   //          'id_transaction_type' => $career_type[0]->id,
   //          'id_employment_status' => $request->id_contract_category,
   //          'id_old_employment_status' => $request->id_contract_category,
   //          'id_position_detail' => $contract_position[0]->id_position_detail,
   //          'id_old_position_detail' => $contract_position[0]->id_position_detail,
			// 'remark' => "New Employee",
   //          'id_approval_status' => $contract_approve->id_general_data,
            'effective_date' => $request->effective_date,
            'expired_date' => $request->expired_date,            
            'update_date' => date('Y-m-d H:i:s'),
            'updated_by' => session('id_user'),
        );

        $careerByTransNumber = CareerTransition::where('id_employee', $request->id_employee)
					->where('transaction_number', $request->contract_number);
		$careerByReffNumber = CareerTransition::where('id_employee', $request->id_employee)
					->where('reference_number', $request->contract_number);
		if($careerByTransNumber->first()){
			$careerByTransNumber->update($form_career);
		} else {
			$careerByReffNumber->update($form_career);
		}

	
		DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Contract Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Contract !! [' . $e->getMessage() . ']']);           
        }
    }

    public function edit($id) {

        if (request()->ajax()) {
            $data = Contract::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	public function get_employee() {
        $result = Contract::get_employee();
        return response()->json($result);
    }
	public function get_employee_edit() {
        $result = Contract::get_employee_edit();
        return response()->json($result);
    }

    public function get_employee_detail(Request $request) {
    	$id_employee = $request->id_employee;
        $get = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_employee', $id_employee)->first();
        return response()->json($get);
    }
	
	public function get_position(Request $request) {
        $data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Contract::get_position($data);
        return response()->json($result);
    }
	/*
	public function get_shift_group() {
        $result = Contract::get_shift_group();
        return response()->json($result);
    }
	*/
	public function get_shift_group(Request $request) {
		$data = [
            'id_shift_group' => $request->id_shift_group
        ];
        $result = Contract::get_shift_group($data);
        return response()->json($result);
    }
	
	public function get_contract_category(Request $request) {
		$data = [
            'id_employment_status' => $request->id_employment_status
        ];
        $result = Contract::get_contract_category($data);
        return response()->json($result);
    }

    public function get_company() {
        $result = Contract::get_company();
        return response()->json($result);
    }
	
	public function browse(Request $request) {
        if ($request->ajax()) {
			$data = [
				'contract' => $request->contract
			];	
		/*	$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontract?contract='.$data['contract'];
			$response = file_get_contents($url);
			$decode = json_decode($response);
			$result = collect($decode);
		*/
			$decode = Contract::get_api_contract($data['contract']);
			$result = collect($decode);
		//	dd($result);
            echo $result;
        }
    }

    public function checkid(Request $request) {
		if ($request->ajax()) {
        $data = [
				'contract' => $request->contract,
				'id' => $request->id
			];
		/*	$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontractid?contract='.$data['contract'].'&id='.$data['id'];
			$response = file_get_contents($url);			
			$result = json_decode($response);
		*/
		$result = Contract::get_api_contract($data['contract'],$data['id']);
		 }
        return response()->json(['result' => $result]);
    }

    public function attachment(Request $request) {
        ini_set('max_execution_time', -1);
    	
        if ($request->ajax()) {
        	$nik 			= $request->nik ?? [];
        	$id_contract 	= $request->id_contract ?? [];
        	
            $data = Contract::getAttachment($nik, $id_contract);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($data) {
                        return '';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
    }
}
