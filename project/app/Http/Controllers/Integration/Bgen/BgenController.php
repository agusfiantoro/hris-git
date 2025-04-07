<?php

namespace App\Http\Controllers\Integration\Bgen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\Integration\Bgen\Bgen;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 

class BgenController extends Controller
{
	protected function accessBranch(Request $request) {
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

			return $group_branch;
	}
	
	public function index(Request $request) {
        if ($request->ajax()) {
			$nik = $request->nik ?? null;
			$idNik = null;
			if($nik){
				foreach($nik as $val){
					$idNik[] = "'".$val."'";
				}
			}
			$status = $request->status ?? 'A';
            $data = Bgen::getdata($this->accessBranch($request),$idNik,$request->id_dept,$status);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {								
								$onclick = "loadedit(".$data->id_employee.",'".$data->nik_employee."','edit','".$data->join_date."')";
								$onview = "loadedit(".$data->id_employee.",'".$data->nik_employee."','view')";
								$button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
								$button .= '&nbsp;<button type="button" name="view" onclick="'.$onview.'" class="edit btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('integration.bgen.index');
    }
	
	public function modal_detail(Request $request) {
		$nik_employee = $request->nik_employee;
		$type = $request->type;
        return view('integration.bgen.modal_detail', compact('nik_employee','type'));
    }
	
	public function get_employee(Request $request) {
		$status = $request->status ?? 'A';
		$result = Bgen::get_employee($this->accessBranch($request), $request->id_dept, $status);
        return response()->json($result);
    }

	public function get_department() {
		$result = Bgen::get_department();
        return response()->json($result);
    }
	
	public function get_status() {
        $result = Bgen::get_status();
        return response()->json($result);
    }
	
	public function get_category() {
        $result = Bgen::get_category();
        return response()->json($result);
    }
	
	public function get_branch() {
        $result = Bgen::get_branch();
        return response()->json($result);
    }
	
	public function get_principal() {
        $result = Bgen::get_principal();
        return response()->json($result);
    }
/*	
	public function get_position() {
        $result = Bgen::get_position();
        return response()->json($result);
    }
/*	
	public function get_location() {
        $result = Bgen::get_location();
        return response()->json($result);
    }
*/	
	public function get_edit(Request $request) {
        $data = [
            'nik_employee' => $request->nik_employee
        ];
        $result = Bgen::get_edit($data);
        return response()->json($result);
    }
	
	public function get_direct_spv(Request $request) {
        $result = Bgen::get_direct_spv($request->id_employee);
		$count_by = count($result);
		return response()->json(['app_direct_spv'=>$result,'count_by'=>$count_by]);
    }
	
	public function get_approval_by(Request $request)
	{
		$sql_header = Bgen::get_approval_by($request->id_employee);
		$approval_hirarki = $sql_header['sql_approval_hirarki'];
		$approval_by = $sql_header['sql_approval_by'];
		$count_by = count($approval_by);
		return response()->json(['approval_hirarki'=>$approval_hirarki,'approval_by'=>$approval_by,'count_by'=>$count_by]);
	}
	
	protected function validateReq(Request $request, $type = 'create') {
		if($request->bgen){
			foreach($request->bgen as $key=>$val_bgen){
				$arr_form_validate = [
					'bgen.'.$request->counter.'.id_transition_category' => 'required',
					'bgen.'.$request->counter.'.bgen_branch' => 'required',
					'bgen.'.$request->counter.'.bgen_principal' => 'required',
				];
				
				$arr_msg_form_validate = [
					'bgen.'.$request->counter.'.id_transition_category.required' => 'The Category field is required',
					'bgen.'.$request->counter.'.bgen_branch.required' => 'The Bgen Branch field is required',
					'bgen.'.$request->counter.'.bgen_principal.required' => 'The Bgen Principal field is required',
				];
				if($val_bgen['cat_code'] == 'Join'){
					if($type == 'create') {
						$arr_form_validate['bgen.'.$request->counter.'.id_approval'] = 'required';
						$arr_form_validate['bgen.'.$request->counter.'.id_approval_request'] = 'required';
						$arr_form_validate['bgen.'.$request->counter.'.id_direct_spv'] = 'required';
						$arr_form_validate['bgen.'.$request->counter.'.app_status'] = 'required';
					}
					
					$arr_msg_form_validate['bgen.'.$request->counter.'.id_approval.required'] = 'The Approval Hierarchy field is required';
					$arr_msg_form_validate['bgen.'.$request->counter.'.id_approval_request.required'] = 'The Approved By field is required';
					$arr_msg_form_validate['bgen.'.$request->counter.'.id_direct_spv.required'] = 'The Direct Spv field is required';
					$arr_msg_form_validate['bgen.'.$request->counter.'.app_status.required'] = 'The Approval Status field is required';
				}				
			}
		}  
       
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	public function save(Request $request) {
		$this->validateReq($request);
		$formData = $request->bgen[$request->counter];
		try {
			DB::beginTransaction();
			$data = New Bgen();
			$data -> id_employee = $request->id_employee;
			$data -> id_transition_category = $formData['id_transition_category'];
			$data -> id_branch = $formData['bgen_branch'];
			$data -> id_principal = $formData['bgen_principal'];
			$data -> id_position_route_destination = $formData['bgen_position'];
			$data -> id_location_destination = $formData['bgen_location'];
			$data -> id_direct_chief = $formData['id_direct_spv'];
			$data -> status = $formData['bgen_status'];
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');	
			$data -> integration_type = 'Sales_Code';
			
			if(isset($formData['id_approval'])){
				$app = Bgen::get_approval($formData['id_approval']);
				$data -> id_approval = $formData['id_approval'];
				$data -> id_approval_status = $app[0]->id_approval_status;
				$data -> save();
				
				$appTrans = Bgen::get_approval_trans($request->id_employee);
				$idPosEmp = JobPositionDetail::where('id_employee', $request->id_employee)->orWhere('id_employee2',$request->id_employee)->where('secondary_position', 0)->first();
				$idPos = JobPositionDetail::where('id_employee', $formData['id_approval_request'])->where('id_company', $idPosEmp->id_company)->first();
				$form_trans = array(
					'id_source_transaction' => $data->id_integration_sales_code,
					'source_transaction_type' => $app[0]->code,
					'id_approval' => $app[0]->id_approval,
					'sequence' => $appTrans[0]->sequence,
					'id_approval_mode' => $appTrans[0]->id_approval_mode,
					'id_employee_approval' => $formData['id_approval_request'],
					'id_position_detail' => $idPos->id_position_detail,
					'id_approval_status' => $app[0]->id_approval_status,
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				 );			
				$at = ApprovalTransaction::create($form_trans);
			}
			else{
				$data -> save();				
			}
		$resData = Bgen::get_update_status($data->id_integration_sales_code);
		if(count($resData) > 0){
			$res = $resData[0];
		}
		else{
			$res = [];
		}
		DB::commit();
			return response()->json(['status'=>'true', 'res'=>$res, 'message'=>'Save Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			if(str_contains($e->getMessage(), "duplicate key value violates unique constraint")) {
				return response()->json(['status' => 'false', 'message' => 'Cannot Save !! [Sales Code already exists on selected branch and principal]'], 500);
			}
			return response()->json(['status' => 'false', 'message' => 'Cannot Save !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function update(Request $request) {
		$this->validateReq($request, 'update');
		$formData = $request->bgen[$request->counter];
		try {
			DB::beginTransaction();
			$data = Bgen::where('id_integration_sales_code',$request->id_integration_sales_code)->first();
			$data -> id_transition_category = $formData['id_transition_category'];
			$data -> id_branch = $formData['bgen_branch'];
			$data -> id_principal = $formData['bgen_principal'];
			$data -> id_position_route_destination = $formData['bgen_position'];
			$data -> id_location_destination = $formData['bgen_location'];
			if(isset($formData['id_direct_spv'])){
				$data -> id_direct_chief = $formData['id_direct_spv'];				
			}
			$data -> status = $formData['bgen_status'];
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');	
			
			if($data->id_approval != null){				
				$approved = Bgen::get_approved($data -> id_approval_status);
				if($approved[0]->code != 'Approved'){
					$app = Bgen::get_approval($data->id_approval);
					$idPosEmp = JobPositionDetail::where('id_employee', $data->id_employee)->where('secondary_position', 0)->first();
					$idPos = JobPositionDetail::where('id_employee', $formData['id_approval_request'])->where('id_company', $idPosEmp->id_company)->first();
					$data -> id_approval_status = $app[0]->id_approval_status;
					$data -> save();
					
					$form_trans = array(
						'id_employee_approval' => $formData['id_approval_request'],
						'id_position_detail' => $idPos->id_position_detail,
						'id_approval_status' => $app[0]->id_approval_status,
						'updated_by' => session('id_user'),
					);
					$at = ApprovalTransaction::where('id_source_transaction',$request->id_integration_sales_code)->where('source_transaction_type','Sales_Code')->update($form_trans); 
				}
				else{
					$data -> save();
				}
			}
			else{
				$data -> save();				
			}
			if($data->wasChanged()) {
				$data->is_synchronize_flag = false;
				$data->save();
			}
		$resData = Bgen::get_update_status($request->id_integration_sales_code);
		if(count($resData) > 0){
			$res = $resData[0];
		}
		else{
			$res = [];
		}
		DB::commit();
			return response()->json(['status'=>'true', 'res'=>$res, 'message'=>'Update Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function cancel($id) {
	//	dd($id);
		try{
			DB::beginTransaction();
			$id_approval = Bgen::where('id_integration_sales_code', $id)->first();
			$cancel = Bgen::cancel();
			
			Bgen::where('id_integration_sales_code', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
			
			ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Sales_Code')->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));	
			
			$resData = Bgen::get_update_status($id);
			if(count($resData) > 0){
				$res = $resData[0];
			}
			else{
				$res = [];
			}
		DB::commit();
			return response()->json(['status' => 'true', 'res'=>$res, 'message' => 'Cancel Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Cancel !!']);           
		}	
    }
	
	public static function syncPosition($nik) {
		$erpIntegration = @DB::selectOne("SELECT hcs.* FROM hr_employee he 
										JOIN master_position_detail mpd ON he.id_employee = mpd.id_employee
										JOIN hr_config_settings hcs ON mpd.id_company = hcs.id_company 
										WHERE he.nik_employee = ?",
										[$nik])->erp_integration;
		if(!$erpIntegration) {
			return;
		}
		$data = Bgen::getSync($nik);
		$position = DB::table('hr_employee as he')
					->join('master_position_detail as mpd', function($query) {
						$query->on('he.id_employee', 'mpd.id_employee');
						$query->orOn('he.id_employee', 'mpd.id_employee2');
					})
					->join('master_position_routing as mpr', 'mpd.id_position_routing', 'mpr.id_routing')
					->select('mpr.*')
					->where('he.status', 'A')
					->where('he.nik_employee', $nik)
					->first();
		
		foreach($data as $row) {
			DB::table('integration.bgen_integration_sales_code')
				->where('id_integration_sales_code', $row->id_integration_sales_code)
				->where('status', 'A')
				->update([
					'id_position_route_destination' => $position->id_routing,
				]);
		}
	}

	public function sync(Request $request) {
		$request->validate([
			'nik_employee' => 'required'
		]);

		$data = self::syncToBgen($request->nik_employee, true);
		if(isset($data['errors']) && count($data['errors']) > 0) {
			return response()->json($data, 500);
		}
		return response()->json($data, $data['success'] ? 200 : 500);
	}

	public static function syncToBgen($nikEmployee = null, $skipErrors = false) {
		// DB::beginTransaction();
		try {
			$api = DB::table('master_api_key')->where('name', 'bgen_create')->orWhere('name', 'bgen_update')->get();
			if(count($api) < 1) {
				throw new \Exception('API endpoint not set!', 500);
			}
			$bgenEndpoint = [
				'create' => '',
				'update' => ''
			];
			$bgenBasic = [
				'user' => '',
				'pass' => '',
			];
			$requestHeader = [
				'gateway' => 'bgen',
				'Content-Type' => 'application/json',
				'Accept' => 'application/json',
			];
			foreach($api as $val) {
				if($val->name == 'bgen_create') {
					$bgenEndpoint['create'] = $val->url;
				} else if($val->name == 'bgen_update') {
					$bgenEndpoint['update'] = $val->url;
				}
				$bgenBasic['user'] = $val->user;
				$bgenBasic['pass'] = $val->password;
			}
			$idUser = session('id_user') ?? 1;

			if($nikEmployee) {
				BgenController::syncPosition($nikEmployee);
			}

			$data = Bgen::getSync($nikEmployee);
			$returnData = [];
			$errors = [];

			if(count($data) == 0) {
				throw new \Exception("No data to synchronize!");
			}

			foreach($data as $row) {
				if($row->sales_code) {
					// Update Sales Code
					$payload = [
						"company_id" => $row->company_code, // BCP/KAS
						"cabang" => $row->id_branch_erp."/".$row->erp_principal_code,
						"Nik" => $row->nik_employee, // required
						"ParentSalesNik" => $row->parent_nik,
						"SalesCode" => $row->sales_code, // required
						"Branch" => $row->id_branch_erp, // required
						"Division" => $row->erp_principal_code, // required
						"Position" => $row->position,
						"Location" => $row->location,
						"Status" => $row->status == 'A' ? 'Active' : 'Inactive',
					];
	
					$res = Http::withBasicAuth($bgenBasic['user'], $bgenBasic['pass'])
							->withHeaders($requestHeader)
							->post($bgenEndpoint['update'], $payload);
					
					$response = $res->object();
					$parentSalesCode = $response->data->ParentSalesCode ?? $row->parent_sales_code;
					if($res->failed()) {
						$erpConnError = str_contains($res->body(), "ERP Connection not found") ? "ERP Connection not found" : null;
						if(!$skipErrors) {
							$update = DB::table('integration.bgen_integration_sales_code')
									->where('id_integration_sales_code', $row->id_integration_sales_code)
									->update([
										'is_synchronize_flag' => strtolower($response->status) == "success",
										'synchronize_message' => $response->message,
										'updated_by' => $idUser,
										'parent_sales_code' => $parentSalesCode,
									]);
							throw new \Exception('BGEN sync update sales code error at employee '.$row->nik_employee.' and id_integration_sales_code '.$row->id_integration_sales_code.' #/ '.json_encode($response).' #/ '.json_encode($payload));
						}
						array_push($errors, [
							"message" => $response->message ?? $erpConnError,
							"id_integration_sales_code" => $row->id_integration_sales_code,
							"nik" => $row->nik_employee,
							"parent_nik" => $row->parent_nik,
							"branch_principal" => $row->id_branch_erp."/".$row->erp_principal_code
						]);
						continue;
					}
					$status = @$response->data->Status;
					$update = DB::table('integration.bgen_integration_sales_code')
									->where('id_integration_sales_code', $row->id_integration_sales_code)
									->update([
										'is_synchronize_flag' => strtolower($response->status) == "success",
										'synchronize_message' => $response->message,
										'updated_by' => $idUser,
										'parent_sales_code' => $parentSalesCode,
									]);
					array_push($returnData, $response);
				} else {
					// Generate Sales Code
					if($row->address_home && $row->address_home != '') {
						$address = substr($row->address_home, 0, 50);
					} else {
						$address = substr($row->idcard_address, 0, 50);
					}

					// if(!$row->parent_sales_code) {
					// 	$parentSalesCode = Bgen::getParentSalesCode($row->nik_employee, $row->id_branch, $row->id_principal, $row->id_company);
					// 	if(!$parentSalesCode && !$skipErrors) {
					// 		throw new \Exception('Parent sales code not found!', 404);
					// 	}
					// }

					$payload = [
						"company_id" => $row->company_code, // BCP/KAS
						"cabang" => $row->id_branch_erp."/".$row->erp_principal_code,
						"Nik" => $row->nik_employee, // required
						"Name" => substr($row->name, 0, 40), // required
						"City" => substr($row->home_base, 0, 20), // required
    					"ParentSalesNik" => $row->parent_nik, //01/01/0015
						"Branch" => $row->id_branch_erp, // required
						"Division" => $row->erp_principal_code, // required
						"Position" => substr($row->position, 0, 20),
						"Location" => $row->location,
						"Status" => $row->status == 'A' ? 'Active' : 'Inactive',
						"Address" => $address,
    					"Telp" => $row->work_phone,
					];
	
					$res = Http::withBasicAuth($bgenBasic['user'], $bgenBasic['pass'])
							->withHeaders($requestHeader)
							->post($bgenEndpoint['create'], $payload);
					
					$response = $res->object();
					// dd($res->failed(), $res);
					if($res->failed()) {
						$erpConnError = str_contains($res->body(), "ERP Connection not found") ? "ERP Connection not found" : null;
						if($response && $response->data && (@$response->data->ParentSalesCode || @$response->data->SalesCode)) {
							$updateValue = [
								'is_synchronize_flag' => false
							];
						
							if(@$response->data->ParentSalesCode) {
								$updateValue['parent_sales_code'] = $response->data->ParentSalesCode;
								$updateValue['is_synchronize_flag'] = true;
							}
							if(@$response->data->SalesCode) {
								$updateValue['sales_code'] = $response->data->SalesCode;
								$updateValue['is_synchronize_flag'] = true;	
							}
							$updateValue['synchronize_message'] = $response->message;
							$updateValue['updated_by'] = $idUser;

							DB::table('integration.bgen_integration_sales_code')
								->where('id_integration_sales_code', $row->id_integration_sales_code)->update($updateValue);
							array_push($returnData, $response);
							continue;
						} else if(!$skipErrors && !$response) {
							$res->throw()->json();
						}
						if($response) {
							$update = DB::table('integration.bgen_integration_sales_code')
									->where('id_integration_sales_code', $row->id_integration_sales_code)
									->update([
										'is_synchronize_flag' => strtolower($response->status) == "success",
										'synchronize_message' => $response->message,
										'updated_by' => $idUser,
									]);
						}
						if(!$skipErrors) {
							throw new \Exception('BGEN sync generate sales code error at employee '.$row->nik_employee.' and id_integration_sales_code '.$row->id_integration_sales_code.'. '.@$response->message.' #/ '.json_encode($res->object()).' #/ '.json_encode($payload));
						}
						array_push($errors, [
							"message" => $response->message ?? $erpConnError,
							"id_integration_sales_code" => $row->id_integration_sales_code,
							"nik" => $row->nik_employee,
							"parent_nik" => $row->parent_nik,
							"branch_principal" => $row->id_branch_erp."/".$row->erp_principal_code,
						]);
						continue;
					}
	
					$update = DB::table('integration.bgen_integration_sales_code')
									->where('id_integration_sales_code', $row->id_integration_sales_code)
									->update([
										'sales_code' => $response->data->SalesCode,
										'parent_sales_code' => $response->data->ParentSalesCode,
										'is_synchronize_flag' => strtolower($response->status) == "success",
										'synchronize_message' => $response->message,
										'updated_by' => $idUser,
									]);
					array_push($returnData, $res->object());
				}
			}
			// DB::commit();
			$return = [
				'success' => true,
				'message' => 'Data synchronization :syncstate:',
				'data' => $returnData,
			];
			if(count($errors) > 0) {
				$return ['errors'] = $errors;
				$return['message'] = str_replace(":syncstate:", "finished with ", $return['message'])." ".count($errors)." errors:";
				foreach($errors as $error) {
					$return['message'] .= "\n• [Parent: ".$error['parent_nik']. " Branch/Princ.: ".$error['branch_principal']. "] ".$error['message'];
				}
			} else {
				$return['message'] = str_replace(":syncstate:", "success!", $return['message']);
			}
			return $return;
		} catch(\Exception $e) {
			$exceptionData = explode(' #/ ', $e->getMessage());
			$message = $exceptionData[0];
			// if(count($exceptionData)>1) {
			// 	$message .= @"\n".json_decode($exceptionData[1])->message;
			// }
			if(str_contains($e->getMessage(), "ERP Connection not found")) {
				$message = "ERP Connection not found";
			}
			$errorResponse = [
				'success' => false,
				'message' => $message,
				'data' => count($exceptionData) > 1 ? json_decode($exceptionData[1]) : null,
				'payload' => count($exceptionData) > 2 ? json_decode($exceptionData[2]) : null,
			];
			return $errorResponse;
		}
	}

	public static function sendApprovalNotification($empApproval, $dataEmployee, $success = true) {
		if(!$empApproval) {
			$empApproval = DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first();
		}
		$emp = DB::table('hr_employee as he')
				->leftJoin('master_branch as mb', DB::raw($dataEmployee->id_branch), 'mb.id_branch')
				->leftJoin('master_principal as mp', DB::raw($dataEmployee->id_principal), 'mp.id_principal')
				->where('he.id_employee', $dataEmployee->id_employee)
				->select('he.*', 'mb.id_branch', 'mb.description as desc_branch', 'mp.id_principal', 'mp.description as desc_principal')
				->first();
		$salesCode = Bgen::findOrFail($dataEmployee->id_integration_sales_code);
		$hrbp = DB::table('hr_employee as he')->where('id_user', $dataEmployee->created_by)->where('status', 'A')->first();
		$notifSpv = \App\Http\Controllers\EmailController::bgenApproval($empApproval, $emp, $success, $salesCode);
		$notifHrbp = \App\Http\Controllers\EmailController::bgenApproval($hrbp, $emp, $success, $salesCode);
	}
	
	public function export_validate(Request $request) {
		$valid = array(
			'id_url' => $request->id_url,
			'status' => $request->status,
			'employee' => $request->nik,
			'dept_search' => $request->id_dept,
		);
        return response()->json($valid);
    }
	
	public function export(Request $request) {
		$nik = $request->employee ?? NULL;
		$idNik = null;
		if($nik != 'null'){
			$arrNik = explode(",",$nik);
			foreach($arrNik as $val){
				$idNik[] = "'".$val."'";
			}
		}
		$status = $request->status;
		$idDept = $request->dept_search;
        $result = Bgen::getExport($this->accessBranch($request),$idNik,$idDept,$status);
		$dataSheet1 = [];
		$dataSheet1[] = ['No', 'Category', 'NIK', 'Name', 'Position', 'Department', 'Job Grade', 'Join Date', 'Branch', 'Principal', 'Sales Code', 'Parent Sales Code', 'Sync', 'Approval Name', 'Direct Supervisor'];
		foreach($result as $k => $emp) {
			$dataSheet1[] = [
				$k+1,
				$emp->category,
				$emp->nik_employee,
				$emp->name,
				$emp->position,
				$emp->department,
				$emp->job_grade,
				$emp->join_date,
				$emp->branch_bgen,
				$emp->principal_bgen,
				$emp->sales_code,
				$emp->parent_sales_code,
				$emp->sync,
				$emp->im_mgr_approve,
				$emp->direct_spv,
			];
		}

		$filename = 'Report Sales Code_('.date('Ymd').')';
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0);
		

		$nameSheet1 = 'Report Sales Code';
        $indexSheet1 = 0;
        $workSheet1 = new Worksheet($spreadsheet, $nameSheet1);
        $spreadsheet->addSheet($workSheet1, $indexSheet1);
        $thisSheet1 = $spreadsheet->getSheet($indexSheet1)->setTitle($nameSheet1);
        $workSheet1->fromArray($dataSheet1);

		foreach ($workSheet1->getColumnIterator() as $column){
			$workSheet1->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
		}
        
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->setIncludeCharts(true);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
		$writer->save('php://output');
	}

}
