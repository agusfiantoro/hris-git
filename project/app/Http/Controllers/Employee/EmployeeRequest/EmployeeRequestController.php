<?php

namespace App\Http\Controllers\Employee\EmployeeRequest;

use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\EmployeeRequest\ApprovalDelegation;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\EmployeeSetting\WorkDays;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;

class EmployeeRequestController extends Controller {

	/*
	public function getkode(Request $request) {
		$codeid = RequestHeader::getkode();
		return response()->json($codeid);
    }
	*/
    public function index(Request $request) {
		$codeid = RequestHeader::getkode();
	//	dd($codeid);
        if ($request->ajax()) {
            $data = RequestHeader::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                            	$buttonSubmit = '&nbsp;<button type="button" name="submit" id="' . $data->id_request_header . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
                            	$buttonEdit = '&nbsp;<button type="button" name="edit" id="' . $data->id_request_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button>';
                            	$buttonView = '&nbsp;<button type="button" name="view" id="' . $data->id_request_header . '" class="edit btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
                            	$buttonCancel = '&nbsp;<button type="button" name="cancel" id="' . $data->id_request_header . '" class="cancel btn btn-danger btn-sm" title="Cancel Request"><span class="fa fa-close fa-lg"></span></button>';
                            	$buttonReqCancel = '&nbsp;<button type="button" name="req_cancel" id="' . $data->id_request_header . '" class="req_cancel btn btn-request-cancel btn-sm" title="Cancel an Approved Request"><span class="fa fa-window-close fa-lg"></span></button>';

                            	if(in_array($data->code_app_status, ['New','Revised'])){
                            		$returnButton = $buttonSubmit.$buttonEdit.$buttonCancel.$buttonReqCancel;
                            	} else {
                            		$returnButton = $buttonSubmit.$buttonView.$buttonCancel.$buttonReqCancel;
                            	}

								/*$button = '<button type="button" name="submit" id="' . $data->id_request_header . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
                                $button .= '<button type="button" name="edit" id="' . $data->id_request_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
								$button .= '<button type="button" name="cancel" id="' . $data->id_request_header . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close fa-lg"></span></button>';
								$button .= '&nbsp;<button type="button" name="req_cancel" id="' . $data->id_request_header . '" class="req_cancel btn btn-request-cancel btn-sm" title="Req Cancel"><span class="fa fa-window-close fa-lg"></span></button>';*/
                            /*    $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_request_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
							 */
                                return $returnButton;
                            })->addColumn('employee_name', function($row) {
                                return $row->employee_name;
                            })->addColumn('desc_request_type', function($row) {
                                return $row->desc_request_type;
                            })->addColumn('desc_app_status', function($row) {
                                return $row->desc_app_status;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
							
			
        }
        return view('employee.employee.employee_request.index',compact('codeid'));
    }
	
	public function index_status(Request $request) {
	/*	$req = RequestHeader::get_user_req();
		$req_location = $req[0]->id_location;
		$req_employee = $req[0]->id_employee;
		$data = [
            'code' => $request->code
        ];	
		$result = RequestHeader::get_hierachy($data,$req_location);
		$data_status = ApprovalTransaction::get_app_combine($req_employee,session('id_company'),$result[0]->id);
	*/
			$data = [
            'id_request_header' => $request->id_request_header,
            'id_approval' => $request->id_approval
			];
			if(is_null($request->id_approval) || $request->id_approval=='null'){
				$data_status = [];
			} else {
				$data_status = RequestHeader::getdata_approval_status($data);
			}
			return DataTables::of($data_status)
								->addIndexColumn()
								->addColumn('execute', function($data) {
									if($data->updated_by != null && $data->code == 'Approved'){
										$button = '<div align="center"><span class="fa fa-check-square-o fa-2x" style="color:green;font-weight:bold;"></span></div>'; 
										return $button;
									}
									else{
										return '';
									}
									
								})
								->rawColumns(['execute'])
								->make(true);
	}
 	
	protected function validateRequest(Request $request) {
        $arr_form_validate = [
       		//'reference_number' => 'required|string',
			'reference_number' => 'unique:hr_request_header', Rule::unique('hr_request_header')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
    		//'id_leave_type' => 'required',
            'id_approval' => 'required',
            'enable_approval' => 'required|string',
            'note' => 'required|string',
            'emprequest.*.id_employee' => 'required',
        	//'emprequest.*.daterange' => 'required',
            'emprequest.*.request_start_to' => 'required',
            'emprequest.*.request_end_to' => 'required',
        ];
		if($request->attachment != null){
			if($request->attachment->getClientOriginalExtension() != 'pdf'){
				$arr_form_validate['attachment'] = 'required|image';
			}
			else if($request->attachment->getClientOriginalExtension() == 'pdf'){
				$arr_form_validate['attachment'] = 'required|mimes:pdf|max:1024';
			}
		}
		
        $arr_msg_form_validate = [
            'id_approval.required' => 'The Hierarchy Approval field is required.(Contact to administrator)',
            'emprequest.*.id_employee.required' => 'The Employee field is required',
        	//'emprequest.*.daterange.required' => 'The Request Date Range field is required',
            'emprequest.*.request_start_to.required' => 'The Request Start Date field is required',
            'emprequest.*.request_end_to.required' => 'The Request End Date field is required',
        ];
        if ($request->post('emprequest') == null) {
            $validate_emprequest = ['table_employee_request' => 'required|string'];
            $validate_msg_emprequest = ['table_employee_request.required' => 'Table Request Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function validateAttachRequest(Request $request) {
		$arr_form_validate = [
				'reference_number' => 'unique:hr_request_header', Rule::unique('hr_request_header')->where(function ($query) {
							return $query->where('id_company', session('id_company'));
						}),
				'id_approval' => 'required',
				'enable_approval' => 'required|string',
				'note' => 'required|string',
				// 'attachment_type' => 'required|string',
				'emprequest.*.id_employee' => 'required',
				//'emprequest.*.daterange' => 'required',
				'emprequest.*.request_start_to' => 'required',
				'emprequest.*.request_end_to' => 'required',
			];

		if($request->attachment == '' || is_null($request->attachment)){
			$arr_form_validate['attachment'] = 'required';
		} else {
			if(@$request->attachment && !is_string($request->attachment)){
				//pengkondisian jika value attachment berasal dari attachment user
				if(@$request->attachment->getClientOriginalExtension() != 'pdf'){
					$arr_form_validate['attachment'] = 'required|image';
				}
				else if(@$request->attachment->getClientOriginalExtension() == 'pdf'){
					$arr_form_validate['attachment'] = 'required|mimes:pdf|max:1024';
				}
			}
		}

        $arr_msg_form_validate = [
        	//'id_leave_type.required' => 'The Leave Type field is required',
			'id_approval.required' => 'The Hierarchy Approval field is required.(Contact to administrator)',
            'emprequest.*.id_employee.required' => 'The Employee field is required',
        	//'emprequest.*.daterange.required' => 'The Request Date Range field is required',
            'emprequest.*.request_start_to.required' => 'The Request Start Date field is required',
            'emprequest.*.request_end_to.required' => 'The Request End Date field is required',
        ];
        if ($request->post('emprequest') == null) {
            $validate_emprequest = ['table_employee_request' => 'required|string'];
            $validate_msg_emprequest = ['table_employee_request.required' => 'Table Request Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function validateCorrectionRequest(Request $request) {
        $arr_form_validate = [
			'reference_number' => 'unique:hr_request_header', Rule::unique('hr_request_header')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'id_approval' => 'required',
            'enable_approval' => 'required|string',
            'note' => 'required|string',
            'emprequest.*.id_employee' => 'required',
        
        ];
        if($request->attachment != null){
			if($request->attachment->getClientOriginalExtension() != 'pdf'){
				$arr_form_validate['attachment'] = 'required|image';
			}
			else if($request->attachment->getClientOriginalExtension() == 'pdf'){
				$arr_form_validate['attachment'] = 'required|mimes:pdf|max:1024';
			}
		}

		foreach ($request->emprequest as $key => $value) {
			if($value['request_start_to'] == null && $value['request_end_to'] == null){
				$arr_form_validate['emprequest.*.request_start_to'] = 'required';
				$arr_form_validate['emprequest.*.request_end_to'] = 'required';
			}
		}
		
        $arr_msg_form_validate = [
			'id_approval.required' => 'The Hierarchy Approval field is required.(Contact to administrator)',
            'emprequest.*.id_employee.required' => 'The Employee field is required',
            'emprequest.*.request_start_to.required' => 'The Start Date or End Date field is required',
            'emprequest.*.request_end_to.required' => '',
        ];
        if ($request->post('emprequest') == null) {
            $validate_emprequest = ['table_employee_request' => 'required|string'];
            $validate_msg_emprequest = ['table_employee_request.required' => 'Table Request Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
		$data_type = RequestHeader::get_request_type_param($request->id_request_type);
		$thisReqAttachment = false;
		
		if($data_type[0]->code != 'Attendance_Correction'){
			$req_attach = RequestHeader::get_leave_attachment($request->id_leave_type);
			$thisAttachment = false;

			if($request->id_request_header){
    			$thisReq = RequestHeader::where('id_request_header', $request->id_request_header)->first();
    			if(!is_null($thisReq->attachment)){
    				$thisAttachment = true;
    			}
			}

			foreach($req_attach as $ra){
				if($ra->req_attachment == 1){
					if(!$thisAttachment){
						$thisReqAttachment = true;
						$this->validateAttachRequest($request);
					}
				}
				else{
					$this->validateRequest($request);
				}
			}  
			if($data_type[0]->code == 'Change_Day_off'){
				$this->validateRequest($request);
			}
		}
		else{
			$this->validateCorrectionRequest($request);
		}
	//	dd($request->emprequest);
		try{
			DB::beginTransaction();
			$leaveType = RequestHeader::get_leave_attachment($request->id_leave_type);
        	$detailReq = $request->emprequest;
			if($data_type[0]->code == 'Leave_Request'){
				$availableLeave = RequestHeader::checkAvailableLeave($leaveType[0]->leave_code);
	        	if($detailReq){
	        		$qtyDays = [];
	        		$errDateRequest = '';
	        		foreach ($detailReq as $key => $val) {
	        			if(is_null($val['request_start_to']) || is_null($val['request_end_to'])){
	        				return response()->json(['status' => 'false_balance', 'message' => 'Please fill Req. Start date and Req. End date', 'data' => null]);
	        			}
	        			if($val['qty_days'] == 0){
	        				return response()->json(['status' => 'false_balance', 'message' => 'Pastikan Qty Days lebih dari 0', 'data' => null]);
	        			}
	        			if($request->request_cancel != 'req_cancel'){
							$checkDateRequest = RequestHeader::checkDateRequest($val['request_start_to'], $val['request_end_to'], $val['id_employee']);
							if(count($checkDateRequest) > 0){
	                            //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
	                            $errDateRequest.= "Tanggal: \n".collect($checkDateRequest)->implode(', ')." \ntelah diajukan request";
	                        }
							$checkExpired = RequestHeader::checkExpired($val['request_start_to'], $val['request_end_to'], $val['id_employee'], $leaveType[0]->leave_code);
							if($leaveType[0]->leave_code == 'ANL'){
								if(count($checkExpired) == 0){
									$errDateRequest.= "Tanggal: \n".$val['request_start_to']." / ".$val['request_end_to']." \ntelah melebihi periode cuti, Mohon Tunggu Untuk Periode Cuti yang Baru";
								}
							}
							else if($leaveType[0]->leave_code == 'EDO'){
								if(count($checkExpired) == 0){
									$errDateRequest.= "Tanggal: \n".$val['request_start_to']." / ".$val['request_end_to']." \ntelah melebihi periode CDO yang direquest, Masa berlaku EDO adalah 3 Bulan sejak pengajuan CDO";
								}
							}
	        			}
	        			$qtyDays[] = (float)$val['qty_days'];
	        		}
	        		if($errDateRequest != ''){
	        			//Utk pengecekan Req. Date yang perna dipakai sebelumnya.
	        			return response()->json(['status' => 'false_balance', 'message' => $errDateRequest, 'data' => null]);
	        		}
        			if($request->request_cancel != 'req_cancel'){
		        		if(array_sum($qtyDays) > $availableLeave){
		        			return response()->json(['status' => 'false_balance', 'message' => 'Leave Request Over Balance (with pending request)', 'data' => null]);
		        		}
		        	}
	        	}
			}
			if($data_type[0]->code == 'Change_Day_off'){
        		$errDateRequest = '';
        		foreach ($detailReq as $key => $val) {
        			if(is_null($val['request_start_to']) || is_null($val['request_end_to'])){
        				return response()->json(['status' => 'false_balance', 'message' => 'Please fill Req. Start date and Req. End date', 'data' => null]);
        			}
        			if($val['qty_days'] == 0){
	    				return response()->json(['status' => 'false_balance', 'message' => 'Pastikan Qty Days lebih dari 0', 'data' => null]);
	    			}
	    			if($request->request_cancel != 'req_cancel'){
						$checkDateRequest = RequestHeader::checkDateRequest($val['request_start_to'], $val['request_end_to'], $val['id_employee']);
						if(count($checkDateRequest) > 0){
                            //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
                            $errDateRequest.= "Tanggal: \n".collect($checkDateRequest)->implode(', ')." \ntelah diajukan request";
                        }
        			}
	        	}
				if($errDateRequest != ''){
        			//Utk pengecekan Req. Date yang perna dipakai sebelumnya.
        			return response()->json(['status' => 'false_balance', 'message' => $errDateRequest, 'data' => null]);
        		}
			}

			$file_attach = 'public/upload/employee_request/';
			if(!is_null($request->id_request_header)){
				//kondisi utk cancel membuat record baru :
				//jika ada attachment pada request yg sebelumnya maka akan mengcopy value attachment yg lama ke record yg baru
				// jika attachment baru diupload, maka pakai yg baru
				$thisReq = RequestHeader::where('id_request_header', $request->id_request_header)->first();
				if(is_null($thisReq->attachment)){
					if($request->attachment != ""){
						if($request->attachment->getClientOriginalExtension() != 'pdf'){
							$image = $request->file_name;
						} else {
							$newFileName    = $request->id_employee_request.'-'.rand(1000,9999).'-'.time().'.'.$request->attachment->getClientOriginalExtension();
				            $storageimage   = Storage::putFileAs($file_attach, $request->attachment, $newFileName);
							$image = $newFileName;
						}
					} else {
						//kondisi utk cancel yg request sblumnya tanpa attachment required
						$image = $thisReq->attachment;
					}
				} else {
					//kondisi utk cancel yg request sblumnya ada attachment nya
					$image = $thisReq->attachment;
				}
				$attachment_type = NULL;
			} else {
				//kondisi selain cancel
				if($request->attachment != "" && $request->attachment->getClientOriginalExtension() != 'pdf'){
					// $file_attach = storage_path('app/public/thumbnail/'. $request->file_name);
					// $image_file = file_get_contents($file_attach);
					// $image = base64_encode($image_file);
					$image = $request->file_name;
					$attachment_type = NULL;
					if($thisReqAttachment){
						if(is_null($image)){
	    					return response()->json(['status' => 'false_balance', 'message' => 'Mohon tunggu preview attachment terlihat', 'data' => null]);
						}
					}
				}
				else if($request->attachment != "" && $request->attachment->getClientOriginalExtension() == 'pdf'){
					// $image_file = file_get_contents($request->attachment);
					// $image = base64_encode($image_file);
					$newFileName    = $request->id_employee_request.'-'.rand(1000,9999).'-'.time().'.'.$request->attachment->getClientOriginalExtension();
		            $storageimage   = Storage::putFileAs($file_attach, $request->attachment, $newFileName);
					$image = $newFileName;
					$attachment_type = NULL;
				}
				else{
					$attachment_type = NULL;
					$image = NULL;
				}
			}
			
			$kode = RequestHeader::getkode();
		//	dd($kode);
	        $form_data = array(
	            'reference_number' => $kode,
	            'id_employee_request' => $request->id_employee_request,
	            'start_date' => $request->start_date,
	            'end_date' => $request->end_date,
	            'id_request_type' => $request->id_request_type,
	            'id_leave_type' => $request->id_leave_type,
	            'id_overtime_type' => $request->id_overtime_type,
	            'attachment_type' => $attachment_type,
	            'attachment' => $image,
				'delegate_approval' => isset($request->delegate_approval) == "on" ? 1 : 0,
	            'note' => $request->note,
				'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
	            'id_approval' => $request->id_approval,
	            'id_approval_status' => $request->id_approval_status,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'created_by' => session('id_user'),
	        );
			if($request->request_cancel == 'req_cancel'){
				$can_leave = RequestHeader::get_cancel_status();
				$req_h = RequestHeader::where('id_request_header',$request->id_request_header)->first();			 
				$form_data['id_request_type'] = $can_leave[0]->id;
				$form_data['id_approval_status'] = RequestHeader::get_approval_status()[0]->id;
				$form_data['reference_number_cancel'] = $req_h->reference_number;
				$form_data['enable_cancel'] = 1;
			/*	RequestHeader::where('id_request_header', $request->id_request_header)->update(array(
					'enable_cancel' => 1,
				));
			*/
			}

			$employeerequest = RequestHeader::create($form_data);
	        foreach ($request->emprequest as $key => $value) {
	           $form_detail = array(
	                'id_request_header' => $employeerequest->id_request_header,
	                'id_employee' => $value['id_employee'],
	                'request_start_to' => $value['request_start_to'],
	                'request_end_to' => $value['request_end_to'],
					'day_type' => isset($value['day_type']) ? $value['day_type'] :  null,
	                'qty_days' => $value['qty_days'],
	                'actual_start_to' => isset($value['actual_start_to']) ? $value['actual_start_to'] : null,
	                'actual_end_to' => isset($value['actual_end_to']) ? $value['actual_end_to'] : null,
	                'note' => null,
	                'id_employee_delegation' => isset($value['id_employee_delegation']) ? $value['id_employee_delegation'] : null ,
	                'status' => 'A',
	                'id_company' => session('id_company'),
	                'created_by' => session('id_user'),
	            );
			//	dd($form_detail);
				if(!($value['request_start_to'] == null && $value['request_end_to'] == null)){
					$employeedetail = RequestDetail::create($form_detail);
				}		
			}
			if($request->id_leave_type != null && $request->delegate_approval == "on"){
			foreach ($request->emprequest as $key => $value) {			
					ApprovalDelegation::create(array(
						'id_request_header' => $employeerequest->id_request_header,
						'id_request_detail' => $employeedetail->id_request_detail,
						'id_source_employee_approval' => $value['id_employee'],
						'id_dest_employee_approval' => $value['id_employee_delegation'],
						'start_date' => $value['request_start_to'],
						'end_date' => $value['request_end_to'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				}
	        }
			$app = ApprovalTransaction::get_approval($request->id_approval);
		//	dd($app);
			if($app[0]->hierarchy_type == "Organization"){
				$approve = ApprovalTransaction::get_app_org($request->id_employee_request,session('id_company'));
			}
			else if($app[0]->hierarchy_type == "Combine"){
				$approve = ApprovalTransaction::get_app_combine($request->id_employee_request,session('id_company'),$request->id_approval);
			}
			else if($app[0]->hierarchy_type == "Custom"){
				$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
			}		
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $employeerequest->id_request_header;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
			}	
		//	dd($source);
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
				if($request->request_cancel == 'req_cancel'){
					$form_trans['source_transaction_type'] = 'Cancel_Leave';
				}
				$at = ApprovalTransaction::create($form_trans);
							
			}
			
			DB::commit();
			return response()->json(['status' => 'true', 'data'=>$employeerequest->id_request_header, 'message' => 'Employee Request Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Save Employee Request !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function update(Request $request) {
        $req_attach = RequestHeader::get_leave_attachment($request->id_leave_type);
        $thisReq = RequestHeader::where('id_request_header', $request->id_request_header)->first();
		$thisReqAttachment = false;

		foreach($req_attach as $ra){
			if($ra->req_attachment == 1){
				$thisReqAttachment = true;
				$request->attachment = $request->attachment ?? $thisReq->attachment;
				$this->validateAttachRequest($request);
			}
			else{
				$this->validateRequest($request);
			}
		}
		try{
			$data_type = RequestHeader::get_request_type_param($request->id_request_type);
	        $detailReq = $request->emprequest;

			if($data_type[0]->code == 'Leave_Request'){
				$leaveType = RequestHeader::get_leave_attachment($request->id_leave_type);
				$availableLeave = RequestHeader::checkAvailableLeave($leaveType[0]->leave_code, null, $request->id_request_header);
	        	if($detailReq){
	        		$qtyDays = [];
	        		$errDateRequest = '';

	        		foreach ($detailReq as $key => $val) {
	        			if(is_null($val['request_start_to']) || is_null($val['request_end_to'])){
	        				return response()->json(['status' => 'false_balance', 'message' => 'Please fill Req. Start date and Req. End date', 'data' => null]);
	        			}
	        			if($val['qty_days'] == 0){
	        				return response()->json(['status' => 'false_balance', 'message' => 'Pastikan Qty Days lebih dari 0', 'data' => null]);
	        			}
	        			if($request->request_cancel != 'req_cancel'){
							$checkDateRequest = RequestHeader::checkDateRequest($val['request_start_to'], $val['request_end_to'], $val['id_employee'], $request->id_request_header);
							if(count($checkDateRequest) > 0){
	                            //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
	                            $errDateRequest.= "Tanggal: \n".collect($checkDateRequest)->implode(', ')." \ntelah diajukan request";
	                        }
							$checkExpired = RequestHeader::checkExpired($val['request_start_to'], $val['request_end_to'], $val['id_employee'], $leaveType[0]->leave_code);
							if($leaveType[0]->leave_code == 'ANL'){
								if(count($checkExpired) == 0){
									$errDateRequest.= "Tanggal: \n".$val['request_start_to']." / ".$val['request_end_to']." \ntelah melebihi periode cuti, Mohon Tunggu Untuk Periode Cuti yang Baru";
								}
							}
							else if($leaveType[0]->leave_code == 'EDO'){
								if(count($checkExpired) == 0){
									$errDateRequest.= "Tanggal: \n".$val['request_start_to']." / ".$val['request_end_to']." \ntelah melebihi periode CDO yang direquest, Masa berlaku EDO adalah 3 Bulan sejak pengajuan CDO";
								}
							}
						}
	        			$qtyDays[] = (float)$val['qty_days'];
	        		}
	        		if($errDateRequest != ''){
	        			//Utk pengecekan Req. Date yang perna dipakai sebelumnya.
	        			return response()->json(['status' => 'false_balance', 'message' => $errDateRequest, 'data' => null]);
	        		}
        			if($request->request_cancel != 'req_cancel'){
		        		if(array_sum($qtyDays) > $availableLeave){
		        			return response()->json(['status' => 'false_balance', 'message' => 'Leave Request Over Balance (with pending request)', 'data' => null]);
		        		}
		        	}
	        	}
			}

			if($data_type[0]->code == 'Change_Day_off'){
        		$errDateRequest = '';
        		foreach ($detailReq as $key => $val) {
        			if(is_null($val['request_start_to']) || is_null($val['request_end_to'])){
        				return response()->json(['status' => 'false_balance', 'message' => 'Please fill Req. Start date and Req. End date', 'data' => null]);
        			}
        			if($val['qty_days'] == 0){
	    				return response()->json(['status' => 'false_balance', 'message' => 'Pastikan Qty Days lebih dari 0', 'data' => null]);
	    			}
	    			if($request->request_cancel != 'req_cancel'){
						$checkDateRequest = RequestHeader::checkDateRequest($val['request_start_to'], $val['request_end_to'], $val['id_employee'], $request->id_request_header);
						if(count($checkDateRequest) > 0){
                            //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
                            $errDateRequest.= "Tanggal: \n".collect($checkDateRequest)->implode(', ')." \ntelah diajukan request";
                        }
        			}
	        	}
				if($errDateRequest != ''){
        			//Utk pengecekan Req. Date yang perna dipakai sebelumnya.
        			return response()->json(['status' => 'false_balance', 'message' => $errDateRequest, 'data' => null]);
        		}
			}

			DB::beginTransaction();
			$form_data = array(
			//	'reference_number' => $request->reference_number,
				'id_employee_request' => $request->id_employee_request,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'id_request_type' => $request->id_request_type,
				'id_leave_type' => $request->id_leave_type,
				'id_overtime_type' => $request->id_overtime_type,
				'delegate_approval' => isset($request->delegate_approval) == "on" ? 1 : 0,
				'note' => $request->note,
				'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
				'id_approval' => $request->id_approval,
				'id_approval_status' => $request->id_approval_status,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),		 
			);
		
			if($request->attachment != ""){
				$file_attach = 'public/upload/employee_request/';

				if(is_string($request->attachment) && strlen($request->attachment) > 2000){
					//kondisi utk edit yg sebelumnya base64 dan tidak memperbarui attachment
					$form_data['attachment_type'] = @$thisReq->attachment_type;
					$form_data['attachment'] = $request->attachment;
				} else {
					if(!is_string($request->attachment)){ 
						//kondisi jika update dgn attachment baru
						if(@$request->attachment->getClientOriginalExtension() != 'pdf'){
							// $file_attach = storage_path('app/public/thumbnail/'. $request->file_name);
							// $image_file = file_get_contents($file_attach);
							// $image = base64_encode($image_file);
							$form_data['attachment_type'] = NULL;
							$form_data['attachment'] = @$request->file_name;
							if($thisReqAttachment){
								if(is_null(@$request->file_name)){
			    					return response()->json(['status' => 'false_balance', 'message' => 'Mohon tunggu preview attachment terlihat', 'data' => null]);
								}
							}
						}			
						else{
							// $image_file = file_get_contents($request->attachment);
							// $image = base64_encode($image_file);
							$ext = $request->attachment->extension();
							$newFileName    = $request->id_employee_request.'-'.rand(1000,9999).'-'.time().'.'.$ext;
				            $storageimage   = Storage::putFileAs($file_attach, $request->attachment, $newFileName);
							$form_data['attachment_type'] = NULL;
							$form_data['attachment'] = $newFileName;
						}
					} else {
						//kondisi jika update tanpa attachment baru dan menggunakan yg sudah ada
						$form_data['attachment_type'] = @$thisReq->attachment_type;
						$form_data['attachment'] = $request->attachment;
					}
				}
					
	            if(!is_null($thisReq->attachment) && strlen($thisReq->attachment) < 1000 ){
	            	// jika ada attcahment dan characternya kurg dr 1000 (selain attachment base64) 
	            	if (Storage::exists($file_attach.$thisReq->attachment)) {
                        if($request->attachment != $thisReq->attachment){
							Storage::delete($file_attach.$thisReq->attachment);
						}
                    }
	            }
			}
     
        $employeerequest = RequestHeader::findOrFail($request->id_request_header)->update($form_data);
        $collect_emprequest = collect($request->emprequest)->groupBy('id_request_detail')->toArray();
        $list_emprequest = array_filter(array_keys($collect_emprequest));

		  if ($request->emprequest != null) {
			 if (implode(",", $list_emprequest) != "") {
				DB::delete("DELETE FROM  hr_request_detail hrd
								WHERE hrd.id_request_header = ? AND hrd.id_request_detail NOT IN (" . implode(",", $list_emprequest) . ")", [$request->id_request_header]);
			 }
			foreach ($request->emprequest as $key => $value) {
				if ($value['id_request_detail'] == "") {
					$form_detail = array(
						'id_request_header' => $request->id_request_header,
						'id_employee' => $value['id_employee'],
						'request_start_to' => $value['request_start_to'],
						'request_end_to' => $value['request_end_to'],
						'day_type' => isset($value['day_type']) ? $value['day_type'] :  null,
					//	'day_type' => $value['day_type'],
						'qty_days' => $value['qty_days'],
						'actual_start_to' => isset($value['actual_start_to']) ? $value['actual_start_to'] : null ,
						'actual_end_to' => isset($value['actual_end_to']) ? $value['actual_end_to'] : null,
						'note' => null,
						'id_employee_delegation' => isset($value['id_employee_delegation']) ? $value['id_employee_delegation'] : null ,
						'status' => 'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					if(!($value['request_start_to'] == null && $value['request_end_to'] == null)){
						$employeedetail = RequestDetail::create($form_detail);
					}	
				} else {
					$form_detail = array(
					   'id_employee' => $value['id_employee'],
						'request_start_to' => $value['request_start_to'],
						'request_end_to' => $value['request_end_to'],
						'day_type' => isset($value['day_type']) ? $value['day_type'] :  null,
					//	'day_type' => $value['day_type'],
						'qty_days' => $value['qty_days'],
						'actual_start_to' => isset($value['actual_start_to']) ? $value['actual_start_to'] : null ,
						'actual_end_to' => isset($value['actual_end_to']) ? $value['actual_end_to'] : null,
						'note' => null,
						'id_employee_delegation' => isset($value['id_employee_delegation']) ? $value['id_employee_delegation'] : null ,
						'status' => 'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					);
				//	dd($form_detail);
					$employeedetail = RequestDetail::where('id_request_detail', $value['id_request_detail'])->update($form_detail);
					
				}				
			}
			
			if($request->id_leave_type != null && $request->delegate_approval == "on"){
			foreach ($request->emprequest as $key => $value) {			
				if ($value['id_request_detail'] == "") {
						ApprovalDelegation::create(array(
							'id_request_header' => $request->id_request_header,
							'id_request_detail' => $employeedetail->id_request_detail,
							'id_source_employee_approval' => $value['id_employee'],
							'id_dest_employee_approval' => $value['id_employee_delegation'],
							'start_date' => $value['request_start_to'],
							'end_date' => $value['request_end_to'],
							'id_company' => session('id_company'),
							'created_by' => session('id_user'),
						));
					}
				else{
					ApprovalDelegation::where('id_delegation', $value['id_delegation'])->update(array(						
							'id_source_employee_approval' => $value['id_employee'],
							'id_dest_employee_approval' => $value['id_employee_delegation'],
							'start_date' => $value['request_start_to'],
							'end_date' => $value['request_end_to'],
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						));
					
					}
				}
			}
		
		} else if (implode(",", $list_emprequest) == null) {
            DB::delete("DELETE FROM  hr_request_detail hrd 
                        WHERE hrd.id_request_header = ?", [$request->id_request_header]);
        }
		
		$app = ApprovalTransaction::get_approval($request->id_approval);
		$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_request_header)->where('source_transaction_type', $data_type[0]->code)->delete();
	//	dd($app);
		if($app[0]->hierarchy_type == "Organization"){
			$approve = ApprovalTransaction::get_app_org($request->id_employee_request,session('id_company'));
		}
		else if($app[0]->hierarchy_type == "Combine"){
			$approve = ApprovalTransaction::get_app_combine($request->id_employee_request,session('id_company'),$request->id_approval);
		}
		else if($app[0]->hierarchy_type == "Custom"){
			$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
		}		
		foreach ($approve as $key => $value) {
				$source[$key]['id_source_transaction'] = $request->id_request_header;
				$source[$key]['source_transaction_type'] = $app[0]->code;
				$source[$key]['id_approval'] = $app[0]->id_approval;
				$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
				$source[$key]['id_approval_status'] = $request->id_approval_status;
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
			//	'updated_by' => session('id_user'),
			));
		}
		
		DB::commit();
			return response()->json(['status' => 'true', 'data'=>$request->id_request_header, 'message' => 'Employee Request Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Update Employee Request !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function submit_approve($id) {
		try{
			DB::beginTransaction();
		
		$leave_mat = RequestHeader::get_leave_mat($id);
		
		if($leave_mat->leave_code == "MAT" || $leave_mat->leave_code == "MIS"){
			$det = RequestDetail::where('id_request_header', $id)->first();
			
			$id_company = [$det['id_company']];
			$start_req = Carbon::parse($det['request_start_to'])->format('Y-m-d');
			$end_req = Carbon::parse($det['request_end_to'])->format('Y-m-d');
			$id_employee = $det['id_employee'];

			$patch = WorkDays::patchWorkdays($id_company, $start_req, $end_req, $id_employee);
		}
			
		$id_approval = RequestHeader::where('id_request_header', $id)->first();	
		$approve = RequestHeader::submit_approve();
	//	dd($id_approval);
			RequestHeader::where('id_request_header', $id)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));	
		$data = [
            'id_request_header' => $id,
            'id_approval' => $id_approval->id_approval
			];
		$data_status = RequestHeader::getdata_approval_status($data);
	//	dd($data_status);
		foreach ($data_status as $key => $value) {
			if($value->code == "New" || $value->code == "Cancel" || $value->code == "Revised"){		
				 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', $value->source_transaction_type)->update(array(
					'id_approval_status' => $approve->id_general_data,
				//	'updated_by' => session('id_user'),
				));	
			}	
		}
		
		RequestHeader::where('reference_number', $id_approval->reference_number_cancel)->update(array(
			'enable_cancel' => 1,
		));
		$data_mail = RequestHeader::getdata_approval_mail($data);
		foreach($data_mail as $val){
				$dm['id_employee_approval'] = $val->id_employee_approval;
				$dm['name'] = $val->name;
				$dm['private_mail'] = $val->private_mail;
				$dm['sequence'] = $val->sequence;
				$dm['new_sequence'] = $val->new_seq;
				$d_mail[] = $dm;				
		}
		
							
		$data_header = RequestHeader::get_mail_employee($data_mail[0]->id_employee_request);
		$data_type = RequestHeader::get_request_type_param($data_mail[0]->id_request_type);
		
		$req['reference_number'] = $data_mail[0]->reference_number;
		$req['name'] = $data_header[0]->name;
		$req['type'] = $data_type[0]->text;
		$req['code'] = $data_type[0]->code;
		$req['note'] = $data_mail[0]->note;
		if($data_type[0]->code != 'Attendance_Correction' && $data_type[0]->code != 'Change_Day_off'){
			$leave_params = RequestHeader::get_leave_type_param($data_mail[0]->id_leave_type);
			$req['leave_type'] = $leave_params[0]->text;
		}
		$req['creation_date'] = Carbon::parse($data_mail[0]->creation_date_request)->format('d M Y');
		$header['req'] = $req;
			
			$data_detail = RequestHeader::get_mail_employee($data_mail[0]->id_employee);
			$empdetail = [];
			$empdetail['name'] = $data_detail[0]->name;		
			if($data_type[0]->code == 'Attendance_Correction'){
				if($data_mail[0]->request_start_to != null){
					$empdetail['request_start_to'] = Carbon::parse($data_mail[0]->request_start_to)->format('d M Y H:i');
				}
				else{
					$empdetail['request_start_to'] = '-';
				}
				if($data_mail[0]->request_end_to != null){
					$empdetail['request_end_to'] = Carbon::parse($data_mail[0]->request_end_to)->format('d M Y H:i');
				}
				else{
					$empdetail['request_end_to'] = '-';
				}
			}
			else{
				$empdetail['request_start_to'] = Carbon::parse($data_mail[0]->request_start_to)->format('d M Y');
				$empdetail['request_end_to'] = Carbon::parse($data_mail[0]->request_end_to)->format('d M Y');
			}
			$empdetail['qty_days'] = $data_mail[0]->qty_days;
			$empdetail['day_type'] = $data_mail[0]->day_type;
		
		$header['reqdetail'] = $empdetail;
		$header['transaction'] = $d_mail;
		
		DB::commit();
		        return response()->json(['status' => 'true', 'message' => 'Approval Status Submit Successfully !!', 'data' => $header]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Approval Status Submit !! [' . $e->getMessage() . ']']); 
        }
    }
	public function cancel($id) {
		try{
			DB::beginTransaction();
		$id_approval = RequestHeader::where('id_request_header', $id)->first();	
		 $data = DB::table('master_general_data as mgd')
                ->select('mgd.code')
                ->where('mgd.id_general_data', $id_approval->id_request_type)
                ->first();
		$cancel = RequestHeader::cancel();
        RequestHeader::where('id_request_header', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
		 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', $data->code)->update(array(
					'id_approval_status' => $cancel->id_general_data,
					// 'updated_by' => session('id_user'),
				));	
	/*	RequestHeader::where('reference_number', $id_approval->reference_number_cancel)->update(array(
				'enable_cancel' => 0,
			));
	*/
		DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
	/*
	public function submit_approve(Request $request) {
		$approve = RequestHeader::submit_approve();
			RequestHeader::where('id_request_header', $request->id_request_header)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));		
		$data = [
            'id_request_header' => $request->id_request_header,
            'id_approval' => $request->id_approval
			];
		$data_status = RequestHeader::getdata_approval_status($data);
		foreach ($data_status as $key => $value) {
			if($value->code == "New"){		
				 ApprovalTransaction::where('id_source_transaction', $request->id_request_header)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'updated_by' => session('id_user'),
				));	
			}
		}	
        return response()->json(['status' => 'true', 'message' => 'Employee Request Submit Successfully !!']);
    }
*/	
	public function edit($id) {

        if (request()->ajax()) {
            $data = RequestHeader::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	
	public function destroy($id) {
        $data = RequestHeader::findOrFail($id);
        ApprovalDelegation::where('id_request_header', $id)->delete();
        RequestDetail::where('id_request_header', $id)->delete();
        ApprovalTransaction::where('id_source_transaction', $id)->delete();
        $data->delete();
    }
	
	public function get_employee() {
        $result = RequestHeader::get_employee();
        return response()->json($result);
    }
	public function get_employee_by() {
        $result = RequestHeader::get_employee_by();
        return response()->json($result);
    }
	public function get_employee_delegate() {
        $result = RequestHeader::get_employee_delegate();
        return response()->json($result);
    }
	
	public function get_request_type() {
        $result = RequestHeader::get_request_type();
        return response()->json($result);
    }
	public function get_leave_type(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];
        $result = RequestHeader::get_leave_type($data);
        return response()->json($result);
    }
	public function get_overtime_type() {
        $result = RequestHeader::get_overtime_type();
        return response()->json($result);
    }
	public function get_hierachy(Request $request) {
		$req = ApprovalTransaction::get_user_req();
		$req_location = $req[0]->id_location;
		$data = [
            'code' => $request->code
        ];		
        $result = ApprovalTransaction::get_hierachy($data,$req_location);
        return response()->json($result);
    }
	public function get_emp_leave(Request $request) {
	$data = [
            'id' => $request->id
        ];		
        $result = RequestHeader::get_emp_leave($data);
        return response()->json($result);
    }
	public function get_approval_status() {		
        $result = RequestHeader::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	public function get_request_edit(Request $request) {
        $data = [
            'id_request_header' => $request->id_request_header
        ];
        $result = RequestHeader::get_request_edit($data);
        $start = Carbon::parse($result['request_start_to'])->format('Y-m-d');
        $end = Carbon::parse($result['request_end_to'])->format('Y-m-d');
        $result['leave_remaining_this_request'] = '';
        $result['leave_balance_status_this_request'] = '';
        
        if(!in_array($result['code'], ['Attendance_Correction', 'Change_Day_off'])){
        	if(in_array($result['code_status'], ['Request_Approval', 'Partial_Approved', 'New'])){
	        	$checkLeaveQuota = RequestHeader::checkLeaveQuotaByDate($result['id_leave_type'], $start, $end, $result['id_employee'], $result['id_company'], null, $result['id_request_header']);
        	} else {
	        	$checkLeaveQuota = RequestHeader::checkLeaveQuotaByDate($result['id_leave_type'], $start, $end, $result['id_employee'], $result['id_company']);
        	}
	        $result['leave_balance_remaining_this_request'] = $checkLeaveQuota['remaining'];
	        $result['leave_balance_status_this_request'] = $checkLeaveQuota['status'];
        }

        $storagePath = '';
        if(is_null($result['attachment_type'])){
        	if (Storage::exists('public/upload/employee_request/'. $result['attachment'])) {
				$storagePath = url('project/storage/app/public/upload/employee_request').'/'.$result['attachment'];
			} else {
				$storagePath = url('project/storage/app/public/thumbnail').'/'.$result['attachment'];
			}
        }
        $result['storagePath'] = $storagePath;
        return response()->json($result);
    }
	
	public function get_company() {
        $result = RequestHeader::get_company();
        return response()->json($result);
    }

	public function get_workdays(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
			'code' => $request->code,
        ];
        $result = RequestHeader::get_workdays($data);
        return response()->json($result);
    }
	public function get_count_days(Request $request) {
		$data = [
			'id_employee' => $request->id_employee,
            'min_date' => $request->min_date,
            'max_date' => $request->max_date
        ];
        $result = RequestHeader::get_count_days($data);
        return response()->json($result);
    }
	public function get_count_days_od(Request $request) {
		$data = [
			'id_employee' => $request->id_employee,
            'min_date' => $request->min_date,
            'max_date' => $request->max_date
        ];
        $qty = RequestHeader::get_count_days_od($data);
		$result = 0;
		foreach($qty as $qd){
			$result += $qd->qty_days;
		}
        return response()->json($result);
    }
	public function get_actual_time(Request $request) {
		$data = [
			'id_employee' => $request->id_employee,
            'current_dates' => $request->current_dates,
        ];
        $result = RequestHeader::get_actual_time($data);
        return response()->json($result);
    }
	
	public function upload(Request $request) {
		if (Storage::exists('public/upload/employee_request/'. $request->file_name)) {
			Storage::delete('public/upload/employee_request/'. $request->file_name);
		}
		$ex = explode("/",$request->attachment->getClientMimeType());
		if($ex[0] == 'image'){
			$rnd = rand(1000,9999);
			$nama_gambar = $rnd."-".time()."-".strtolower($request->attachment->getClientOriginalName());
			$ext = $request->attachment->getClientOriginalExtension();

			$filePath = 'public/upload/employee_request/';
            $dir      = Storage::makeDirectory($filePath, 0777, true, true);

            Image::make($request->file('attachment'))->resize(600, null, function ($constraint) {
				$constraint->aspectRatio();
			})->save(storage_path('app/public/upload/employee_request/'. $nama_gambar));
			$path = url('project/storage/app/public/upload/employee_request/'. $nama_gambar);
		}
		
		return response()->json(['message' => 'Upload Success','image_name'=>$nama_gambar,'path'=>$path]);
    }
	
}
