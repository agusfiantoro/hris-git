<?php

namespace App\Http\Controllers\CareerAdministration\CareerHistory;

use App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController;
use App\Models\CareerAdministration\CareerHistory\CareerHistory;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Http\Controllers\Employee\Employee\EmployeeController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CareerHistoryController extends Controller {
	
	public function __construct() {
        $this->servercareer = 'https://hris.borwita.co.id/';
		$this->EmployeeController = new EmployeeController;
    }

    public function index(Request $request) {
    	ini_set('max_execution_time', -1);

    	$servercareer = $this->servercareer;
		$mu = MasterUser::where('id_user',session('id_user'))->first();

        if ($request->ajax()) {
			$data_access = Employee::get_access($request->id_url ?? $request->getRequestUri());
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = implode(",", $x);
			}
			else{
				$group_branch = null;
			}

			$dateType = $request->date_type ==  null ? "effective_date" : $request->date_type;
			$startDate = $request->start_date == "undefined" ? null : $request->start_date;
			$endDate = $request->end_date == "undefined" ? null : $request->end_date;
			
            $data = CareerHistory::getdata($group_branch, $dateType, $startDate, $endDate);	
			// foreach($data as $key=>$value){
			// 	if(!is_null($value->transaction_number)){
			// 		$bro =  CareerHistory::browse($value->code_transaction_type);
			// 		foreach($bro as $val){
			// 			if($value->transaction_number == $val->no_surat){
			// 				if(isset($val->token)){
			// 					$data[$key]->id_surat = $val->id;
			// 					$data[$key]->token = $val->token;
			// 				}
			// 				else{
			// 					$data[$key]->id_surat = $val->id;
			// 					$data[$key]->token = '';
			// 				}
			// 			}
			// 		}
			// 	} else {
			// 		$data[$key]->id_surat = '';
			// 		$data[$key]->token = '';
			// 	}
			// }		
		//	dd($data);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('attachment_custom', function($data){
                            	$storagePath = '';
                            	if(!is_null($data->attachment)){
	                                if (Storage::exists('public/upload/career/'.$data->nik_employee.'/'.$data->attachment)) {
										$storagePath = url('project/storage/app/public/upload/career').'/'.$data->nik_employee.'/'.$data->attachment;
									} else {
										if (Storage::exists('public/upload/career/'.$data->id_employee.'/'.$data->attachment)) {
											$storagePath = url('project/storage/app/public/upload/career').'/'.$data->id_employee.'/'.$data->attachment;
										} else if(!is_null($data->id_employee_old)){
											if (Storage::exists('public/upload/career/'.$data->id_employee_old.'/'.$data->attachment)) {
												$storagePath = url('project/storage/app/public/upload/career').'/'.$data->id_employee_old.'/'.$data->attachment;
											}
										}
									}
                            	}
                                return $storagePath;
                            })
							->addColumn('attachment_custom_letter', function($data){
                            	$storagePath = '';
                            	if(!is_null($data->attachment_letter)){
	                                if (Storage::exists('public/upload/career/'.$data->nik_employee.'/'.$data->attachment_letter)) {
										$storagePath = url('project/storage/app/public/upload/career').'/'.$data->nik_employee.'/'.$data->attachment_letter;
									} else {
										if (Storage::exists('public/upload/career/'.$data->id_employee.'/'.$data->attachment_letter)) {
											$storagePath = url('project/storage/app/public/upload/career').'/'.$data->id_employee.'/'.$data->attachment_letter;
										} else if(!is_null($data->id_employee_old)){
											if (Storage::exists('public/upload/career/'.$data->id_employee_old.'/'.$data->attachment_letter)) {
												$storagePath = url('project/storage/app/public/upload/career').'/'.$data->id_employee_old.'/'.$data->attachment_letter;
											}
										}
									}
                            	}
                                return $storagePath;
                            })
                            ->addColumn('action', function($data) use ($mu) {	
								$button = '<button type="button" name="edit" id="' . $data->id_career_transaction . '" class="view btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 
								if($mu['access_group'] == "Default_Administrator" && $data->code_transaction_type != "Entity_Movement"){
									$button .= '<button type="button" name="cancel" id="' . $data->id_career_transaction . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
								}
								if($data->id_recommendation_header != null){
									$onclickPdf = "get_pdf(".$data->id_recommendation_header.")";
									$button .= ' <button type="button" target="_blank" name="print" onclick="'.$onclickPdf.'" class="print btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></button> ';
								}
                                return $button;
								
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('career_administration.career_transition.career_transition_history.index', compact('servercareer'));
    }
	protected function validateCareerUpdate(Request $request) {
		$request->validate([
            'attachment' => 'max:2016',
            'attachment_letter' => 'max:2016',
                ]);
	}
	public function update(Request $request) {
		$this->validateCareerUpdate($request);
	try{
			DB::beginTransaction();

				$form_data = array(
					'transaction_number' => $request->transaction_number,
					'id_recommendation_header' => $request->id_recommendation_header,
				);
				
			$getEmployee = DB::table('hr_employee')->select('nik_employee')->where('id_employee', $request->id_employee)->first();
			$nikEmployee = $getEmployee->nik_employee;
				
			if($request->attachment != ""){
				if (Storage::exists('public/upload/career/'.$nikEmployee.'/'. $request->file_name)) {
					Storage::delete('public/upload/career/'.$nikEmployee.'/'. $request->file_name);
				}
				
				$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
				$image = $rnd."-".$request->id_employee.".".$request->attachment->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/career/'.$nikEmployee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/career/'.$nikEmployee,$request->attachment,$image);
				$form_data['attachment'] = $image;							
			}
			
			if($request->attachment_letter != ""){
				if (Storage::exists('public/upload/career/'.$nikEmployee.'/'. $request->file_name_letter)) {
					Storage::delete('public/upload/career/'.$nikEmployee.'/'. $request->file_name_letter);
				}
				$rnd_letter = rand(1000,9999).strtotime(date('Y-m-d'));
				$image_letter = $rnd_letter."-".$request->id_employee.".".$request->attachment_letter->getClientOriginalExtension();
						$dir_letter = Storage::makeDirectory('public/upload/career/'.$nikEmployee,0775, true, true);
						$storageimage_letter = Storage::putFileAs('public/upload/career/'.$nikEmployee,$request->attachment_letter,$image_letter);
				$form_data['attachment_letter'] = $image_letter;							
			}
			CareerHistory::findOrFail($request->id_career_transaction)->update($form_data);
				
			DB::commit();
			return response()->json(['status' => 'true', 'message' => 'Career History Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Updated !! [' . $e->getMessage() . ']']);           
        }
	
    }

	public function get_career_edit(Request $request) {
        $data = [
            'id_career_transaction' => $request->id_career_transaction
        ];
        $result = CareerHistory::get_career_edit($data);
	//	dd($result);
        return response()->json($result);
    }

	public function cancel_career($id) {
	try{
        	DB::beginTransaction();		
		$mu = Employee::where('id_user',session('id_user'))->first();
		$cancel_career = CareerHistory::cancel_career($id);
		$mu_a = Employee::where('id_employee',$cancel_career->id_employee)->first();
		$cancel = CareerHistory::cancel();
		$cancel_reject = collect(CareerHistory::cancel_reject())->pluck('id_general_data')->all();
	//	dd($cancel_reject);
		CareerHistory::where('id_career_transaction', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
				'note_revised' => 'Canceled By '.$mu['name'],
				'update_date' => date('Y-m-d H:i:s'),
				'updated_by' => session('id_user'),
			));
		if($cancel_career->executed == 1){
			if($cancel_career->category == "Termination"){			
				Employee::where('id_employee',$cancel_career->id_employee)->where('status','I')->update(array(
					'status' => 'A',
					'resign_date' => NULL,
					'terminate_reason' => NULL,
				));
				JobPositionDetail::where('id_position_detail',$cancel_career->id_old_position_detail)->whereNull('id_employee')->update(array(
					'id_employee' => $cancel_career->id_employee,
				));
				MasterUser::where('id_user',$mu_a->id_user)->where('status','I')->update(array(
					'status' => 'A',
				));
				
				$updateNikMobile[@$mu_a->nik_employee][] = 'active';
				$transition = new CareerTransitionController();
				$transition->updateNikMobile($updateNikMobile);
			}
			else if($cancel_career->category == "Join"){
					JobPositionDetail::where('id_position_detail',$cancel_career->id_position_detail)->where('id_employee',$cancel_career->id_employee)->update(array(
						'id_employee' => NULL,
					));
					Employee::where('id_employee',$cancel_career->id_employee)->where('status','A')->update(array(
						'id_user' => NULL,
					));
					CareerHistory::where('id_career_transaction', $id)->delete();
					$des = $this->EmployeeController->destroy($cancel_career->id_employee);
					if($des->original['status'] == 'false'){
						throw new \Exception($des->original['message']);
					}
			}
			else if($cancel_career->category == "Movement"){
				if($cancel_career->type == 'Pass RPK' || $cancel_career->type == 'Pass Orientation'){
					CareerHistory::where('id_career_transaction', $id)->update(array(
						'id_approval_status' => $cancel->id_general_data,
						'note_revised' => 'Canceled By '.$mu['name'],
						'update_date' => date('Y-m-d H:i:s'),
						'updated_by' => session('id_user'),
					));
				}
				else if($cancel_career->type == 'Employment Status Changes'){
					Employee::where('id_employee',$cancel_career->id_employee)->where('status','A')->update(array(
						'permanent_date' => NULL,
					));
				}
				else{
					$job_emp = JobPositionDetail::where('id_position_detail',$cancel_career->id_position_detail)->first();
					if(!is_null($job_emp->id_employee)){
						JobPositionDetail::where('id_position_detail',$cancel_career->id_position_detail)->update(array(
							'id_employee' => NULL,
						));
					}
					if(!is_null(@$job_emp->id_employee2)){
						JobPositionDetail::where('id_position_detail',$cancel_career->id_position_detail)->update(array(
							'id_employee2' => NULL,
						));
					}
					
					JobPositionDetail::where('id_position_detail',$cancel_career->id_old_position_detail)->whereNull('id_employee')->update(array(
						'id_employee' => $cancel_career->id_employee,
					));
				}
				$career_a = CareerHistory::where('id_employee',$cancel_career->id_employee)->whereNotIn('id_career_transaction',[$id])->whereNotIn('id_approval_status',$cancel_reject)->get()->toArray();
				if(count($career_a) > 0){
					Employee::where('id_employee',$cancel_career->id_employee)->where('status','A')->update(array(
						'expired_date' => $career_a[array_key_last($career_a)]['expired_date'],
					));
				}
				Employee::where('id_employee',$cancel_career->id_employee)->where('status','A')->update(array(
					'id_employment_status' => $cancel_career->id_old_employment_status,
				));
			}
		}		
		else if($cancel_career->executed == 0){
			if($cancel_career->category == "Join" && $cancel_career->type == 'New Employee'){
				$des = $this->EmployeeController->destroy($cancel_career->id_employee);
			}
		}
			DB::commit();
        	return response()->json(['status' => 'true', 'data' => [], 'message' => 'Career Canceled Successfully !!']);		
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => 'false', 'data' => [], 'message' => $e->getMessage()]);           
        }
	
    }
	
	public function get_employee() {
        $result = CareerHistory::get_employee();
        return response()->json($result);
    }
	
	
	public function get_position(Request $request) {
        $data = [
            'id_employee' => $request->id_employee
        ];	
        $result = CareerHistory::get_position($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_position_detail(Request $request) {
        $data = [
         //   'id_employee' => $request->id_employee,
            'id_position_detail' => $request->id_position_detail,
        ];	
        $result = CareerHistory::get_position_detail($data);
	//	dd($result);
        return response()->json($result);
    }
		
	public function get_career_category(Request $request) {
        $result = CareerHistory::get_career_category();
        return response()->json($result);
    }
	public function get_employment_status(Request $request) {
        $result = CareerHistory::get_employment_status();
        return response()->json($result);
    }
	public function get_new_dept(Request $request) {
		$data = [
            'id_company' => $request->id_company
        ];	
        $result = CareerHistory::get_new_dept($data);
        return response()->json($result);
    }
	public function get_new_route(Request $request) {
		$data = [
            'id_dept' => $request->id_dept,
            'id_company' => $request->id_company,
        ];	
        $result = CareerHistory::get_new_route($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_new_positon(Request $request) {
		$data = [
            'id_route' => $request->id_route,
			'id_company' => $request->id_company,
        ];	
        $result = CareerHistory::get_new_positon($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_new_positon_detail(Request $request) {
		$data = [
			'id_position_detail' => $request->id_position_detail,
        ];	
        $result = CareerHistory::get_new_positon_detail($data);
	//	dd($result);
        return response()->json($result);
    }
	public function get_career_type(Request $request) {
        $data = [
            'id' => $request->id
        ];	
	//	dd($data);
        $result = CareerHistory::get_career_type($data);
        return response()->json($result);
    }
	public function get_company_session(Request $request) {
        $data = [
            'id' => $request->id
        ];	
	//	dd($data);
        $result = CareerHistory::get_company_session($data);
        return response()->json($result);
    }

    public function get_company() {
        $result = CareerHistory::get_company();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_hierachy(Request $request) {
		$data = [
            'code' => $request->code
        ];
        $result = CareerHistory::get_hierachy($data);
        return response()->json($result);
    }
	public function get_approval_status() {		
        $result = CareerHistory::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }

    public function api_surat(Request $request) {
    	$transaction_number = $request->transaction_number ?? null;
    	$type = $request->transaction_type ?? null;

		$apiMemo = ["Orientation", "Failed_Orientation", "Temporary_Assignment", "Pass_RPK"];
		$apiCareer = ["Promotion", "Pass_Orientation", "Mutation", "Demotion", "Rotation", "Relocation"];

		if(in_array($type, $apiMemo)){
			$url = $this->servercareer.'nosurat/index.php/memo/apimemo';
		}
		else if($type == "Employment_Status_Changes"){
			$url = $this->servercareer.'nosurat/index.php/contract/apicontract?contract=pkwtt';
		}
		else if($type == "New_Employee"){
			$url = $this->servercareer.'nosurat/index.php/contract/apicontract?contract=pkwt';
		}
		else if(in_array($type, $apiCareer)){
			$url = $this->servercareer.'nosurat/index.php/career/apicareer?career='.$type;
		}
		else{
			$url = $this->servercareer.'nosurat/index.php/career/apinocareer';				
		}
		$response 			= file_get_contents($url);
		$decode 			= json_decode($response);
		$result['id_surat'] = '';
		$result['token'] 	= '';

		if(is_array($decode) && count($decode) > 0){
			foreach($decode as $val){
				if($transaction_number == $val->no_surat){
					if(isset($val->token)){
						$result['id_surat'] = $val->id;
						$result['token'] = $val->token;
					}
					else{
						$result['id_surat'] = $val->id;
						$result['token'] = '';
					}
				}
			}
		}
		$result['type'] = $type;
		return response()->json($result);
    }
}
