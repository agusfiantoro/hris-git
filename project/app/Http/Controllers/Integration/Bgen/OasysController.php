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

class OasysController extends Controller
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
        return view('integration.oasys.index');
    }
	
	public function modal_detail(Request $request) {
		$nik_employee = $request->nik_employee;
		$type = $request->type;
        return view('integration.oasys.modal_detail', compact('nik_employee','type'));
    }

    protected function validateReq(Request $request, $type = 'create') {
		if($request->bgen){
			foreach($request->bgen as $key=>$val_bgen){
				$arr_form_validate = [
					// 'bgen.'.$request->counter.'.id_transition_category' => 'required',
					'bgen.'.$request->counter.'.bgen_branch' => 'required',
					'bgen.'.$request->counter.'.bgen_principal' => 'required',
				];
				
				$arr_msg_form_validate = [
					// 'bgen.'.$request->counter.'.id_transition_category.required' => 'The Category field is required',
					'bgen.'.$request->counter.'.bgen_branch.required' => 'The Bgen Branch field is required',
					'bgen.'.$request->counter.'.bgen_principal.required' => 'The Bgen Principal field is required',
				];				
			}
		}  
       
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    public function get_edit(Request $request) {
        $data = [
            'nik_employee' => $request->nik_employee
        ];
        $result = Bgen::get_edit($data, 'Biz_Approval');
        return response()->json($result);
    }

    public function get_approval_by(Request $request)
	{
		$sql_header = Bgen::oasys_get_approval_by($request->id_employee);
		$approval_hirarki = $sql_header['sql_approval_hirarki'];
		$approval_by = $sql_header['sql_approval_by'];
		$count_by = count($approval_by);
		return response()->json(['approval_hirarki'=>$approval_hirarki,'approval_by'=>$approval_by,'count_by'=>$count_by]);
	}

    public function save(Request $request) {
		$this->validateReq($request);
		$formData = $request->bgen[$request->counter];
		try {
			DB::beginTransaction();
			$data = New Bgen();
			$data -> id_employee = $request->id_employee;
			$data -> id_branch = $formData['bgen_branch'];
			$data -> id_principal = $formData['bgen_principal'];
			$data -> status = $formData['bgen_status'];
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');	
            $data -> integration_type = 'Biz_Approval';
			
			if(isset($formData['id_approval'])) {
				$app = Bgen::get_approval($formData['id_approval']);
				$data -> id_approval = $formData['id_approval'];
				$data -> id_approval_status = $app[0]->id_approval_status;
				$data -> save();

				$approve = Bgen::oasysGetApproval($request->id_employee, session('id_company'), $app[0]->hierarchy_type);
                // $approval = Bgen::oasys_get_approval_by($data->id_employee);
				// $appTrans = Bgen::get_approval_trans($request->id_employee);
				// $idPos = JobPositionDetail::where('id_employee', $approval['sql_approval_by'][0]->id)->first();
                foreach($approve as $approvalData) {
                    $form_trans = array(
                        'id_source_transaction' => $data->id_integration_sales_code,
                        'source_transaction_type' => $app[0]->code,
                        'id_approval' => $app[0]->id_approval,
                        'sequence' => $approvalData->sequence,
                        'id_approval_mode' => $approvalData->id_approval_mode,
                        'id_employee_approval' => $approvalData->id_employee_approval,
                        'id_position_detail' => $approvalData->id_position_detail,
                        'id_approval_status' => $app[0]->id_approval_status,
                        'id_company' => session('id_company'),
                        'created_by' => session('id_user'),
                     );			
                    $at = ApprovalTransaction::create($form_trans);
                }
				
			} else {
				$data -> save();				
			}
            $resData = Bgen::get_update_status($data->id_integration_sales_code);
            if(count($resData) > 0) {
                $res = $resData[0];
            } else {
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
        $this->validateReq($request);
        $formData = $request->bgen[$request->counter];
		try {
			DB::beginTransaction();
			$data = Bgen::where('id_integration_sales_code',$request->id_integration_sales_code)->first();
			$data -> id_branch = $formData['bgen_branch'];
			$data -> id_principal = $formData['bgen_principal'];
			if(isset($formData['id_direct_spv'])){
				$data -> id_direct_chief = $formData['id_direct_spv'];				
			}
			$data -> status = $formData['bgen_status'];
			$data -> id_company = session('id_company');
			$data -> updated_by = session('id_user');	
			
			if($data->id_approval != null){				
				$approved = Bgen::get_approved($data -> id_approval_status);
				if($approved[0]->code != 'Approved'){
                    $approval = Bgen::get_approval_by($data->id_employee);
					$app = Bgen::get_approval($data->id_approval);
					$idPos = JobPositionDetail::where('id_employee', $approval['sql_approval_by'][0]->id)->first();
					$data -> id_approval_status = $app[0]->id_approval_status;
					$data -> save();
					
					$form_trans = array(
						'id_employee_approval' => $approval['sql_approval_by'][0]->id,
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
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);
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

	private function getApiConfig($keys = ['oasys_create_role', 'oasys_create_user', 'oasys_update_role', 'oasys_update_user']) {
		$api = DB::table('master_api_key')->whereIn('name', $keys)->get();
        if(count($api) < 1) {
            throw new \Exception('API endpoint not set!', 500);
        }
        $bgenEndpoint = [];
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
            $bgenEndpoint[$val->name] = $val->url;
            $bgenBasic['user'] = $val->user;
            $bgenBasic['pass'] = $val->password;
        }
		return (object)[
			'endpoint' => $bgenEndpoint,
			'headers' => $requestHeader,
			'auth' => $bgenBasic,
		];
	}

    public function syncToBgen($nikEmployee = null, $skipErrors = false) {
        
		$api = $this->getApiConfig();
        $idUser = session('id_user') ?? 1;
        $data = Bgen::getOasysSync($nikEmployee);
        $returnData = [];
        $errors = [];

        if(count($data->users) == 0 && !$skipErrors) {
            throw new \Exception("No data to synchronize!");
        }

		$httpClient = Http::withBasicAuth($api->auth['user'], $api->auth['pass'])->withHeaders($api->headers);

        foreach($data->roles as $role) {
			$res = $httpClient->post($api->endpoint['oasys_create_role'], (array)$role);
			$response = $res->object();
			if($res->failed()) {
				array_push($errors, [
					"status" => $response->status,
					"message" => $response->message,
					"data" => $response->data
				]);
				continue;
			}
			array_push($returnData, [
				"status" => $response->status,
				"message" => $response->message,
				"data" => $response->data
			]);
        }

		foreach($data->users as $user) {
			$res = $httpClient->post($api->endpoint['oasys_create_user'], (array)$user);
			$response = $res->object();
			$bgen = Bgen::findOrFail($user->id_integration_sales_code);
			$bgen->synchronize_message = is_string($response->message) ? $response->message : json_encode($response->message);
			$bgen->updated_by = $idUser;
			if($res->failed()) {
				$bgen->save();
				array_push($errors, [
					"status" => $response->status,
					"message" => $response->message,
					"data" => $response->data,
				]);
				continue;
			}
			$bgen->is_synchronize_flag = true;
			$bgen->save();
			
			array_push($returnData, [
				"status" => $response->status,
				"message" => $response->message,
				"data" => $response->data
			]);
		}

		return [
			'success' => $returnData,
			'errors' => $errors,
		];
        
    }

	public function syncUpdateToBgen($nikEmployee = null, $syncRole = true, $unsynchronizedOnly = true) {
		$api = $this->getApiConfig();
        $idUser = session('id_user') ?? 1;
        $returnData = [];
        $errors = [];
		$data = Bgen::getOasysUpdateSync($nikEmployee, $syncRole, $unsynchronizedOnly);

		$httpClient = Http::withBasicAuth($api->auth['user'], $api->auth['pass'])->withHeaders($api->headers);
		
		if($syncRole) {
			foreach($data->roles as $role) {
				$res = $httpClient->put($api->endpoint['oasys_update_role'], (array)$role);
				$response = $res->object();
				$message = is_string($response->message) ? $response->message : json_encode($response->message);
				if($res->failed()) {
					array_push($errors, [
						"status" => $response->status,
						"message" => $message,
						"data" => $response->data,
						"role" => json_encode($role),
					]);
					continue;
				}
				array_push($returnData, [
					"status" => $response->status,
					"message" => $message,
					"data" => $response->data
				]);
			}
		}

		foreach($data->users as $key => $user) {
			$res = $httpClient->put($api->endpoint['oasys_update_user'], (array)$user);
			$response = $res->object();
			$message = is_string($response->message) ? $response->message : json_encode($response->message);
			$id = explode(",", trim($user->id_integration_sales_code, "{}"));
			$bgenData = Bgen::whereIn('id_integration_sales_code', $id)->get();
			foreach($bgenData as $bgen) {
				$bgen->synchronize_message = $message;
				$bgen->updated_by = $idUser;
				if($res->failed()) {
					$bgen->save();
					array_push($errors, [
						"status" => $response->status,
						"message" => $message,
						"data" => $response->data,
						"id_integration_sales_code" => $bgen->id_integration_sales_code,
					]);
					continue;
				}
				$bgen->is_synchronize_flag = true;
				$bgen->save();
			}
			
			array_push($returnData, [
				"status" => $response->status,
				"message" => $message,
				"data" => $response->data
			]);
		}

		return [
			'success' => $returnData,
			'errors' => $errors,
		];
	}
}