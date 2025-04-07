<?php

namespace App\Http\Controllers\Employee\EmployeeApproval;

use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeApproval\EmployeeApproval;
use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Employee\EmployeeSetting\MasterAnnouncement;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeApprovalController extends Controller {
	 public function __construct()
	{
        $this->CareerTransitionController = new CareerTransitionController;
        $this->EmailController = new EmailController;
	}
	
    public function index(Request $request) {
        if ($request->ajax()) {		
			$emp = Employee::where('id_user', session('id_user'))->where('id_company', session('id_company'))->where('status', 'A')->first();
		//	dd($emp);
			if(!is_null($emp)){
				$emp_id = $emp['id_employee'];
			}
			else{
				$emp_id = 1;
			}
			$data = EmployeeApproval::getdata($emp_id);

            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$button = '<button style="color:white;" type="button" name="view" id="' . $data->id_approval_transaction . '" class="view btn btn-warning btn-sm" title="View"><span class="far fa-eye fa-lg"></span><br><b>View</b></button> '; 
								 $button .= '&nbsp;&nbsp;<button type="button" name="approve" id="' . $data->id_approval_transaction . '" class="approve btn btn-success btn-sm" title="Approve"><span class="fa fa-check-square-o fa-lg"></span><br><b>Approve</b></button>';
                                $button .= '&nbsp;&nbsp;<button type="button" name="revised" id="' . $data->id_approval_transaction . '" class="revised btn btn-info btn-sm" title="Revise"><span class="fa fa-pencil-square fa-lg"></span><br><b>Revise</b></button> ';
								$button .= '&nbsp;&nbsp;<button type="button" name="rejected" id="' . $data->id_approval_transaction . '" class="rejected btn btn-danger btn-sm" title="Reject"><span class="fa fa-window-close-o fa-lg"></span><br><b>Reject</b></button> ';
								                              
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->editColumn('request_start_date', function ($data){
                            	$return = '-';
                            	if($data->source_transaction_type != 'Career_Request'){
                            		if(!is_null($data->request_start_date)){
	                            		if($data->request_type == 'Attendance_Correction'){
		                            		$return = Carbon::parse($data->request_start_date)->translatedFormat('d F Y H:i:s');
		                            	} else {
		                            		$return = Carbon::parse($data->request_start_date)->translatedFormat('d F Y');
		                            	}
		                            }
                            	}
			                	return $return;
							})
							->editColumn('request_end_date', function ($data){
                            	$return = '-';
                            	if($data->source_transaction_type != 'Career_Request'){
                            		if(!is_null($data->request_end_date)){
                            			if($data->request_type == 'Attendance_Correction'){
		                            		$return = Carbon::parse($data->request_end_date)->translatedFormat('d F Y H:i:s');
		                            	} else {
		                            		$return = Carbon::parse($data->request_end_date)->translatedFormat('d F Y');
		                            	}
                            		}
	                            }
			                	return $return;
							})
                            ->make(true);			
        }
        return view('employee.employee.employee_approval.index');
    }
	public function detail_career($id) {
        return view('employee.employee.employee_approval.detail_career', compact('id'));
    }
	
	public function index_status(Request $request) {
			$data = [
            'id_source_transaction' => $request->id_source_transaction,
            'source_transaction_type' => $request->source_transaction_type
			];
		
			$data_status = EmployeeApproval::getdata_approval_status($data);
		//	dd($data_status);
			return DataTables::of($data_status)
								->addIndexColumn()
								->addColumn('execute', function($data) {
									if($data->updated_by != null){
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
	
	public function index_history(Request $request) {
        if ($request->ajax()) {
			$emp = Employee::where('id_user', session('id_user'))->where('id_company', session('id_company'))->where('status', 'A')->first();
			$startdate 				= $request->startdate ?? null;
			$enddate 				= $request->enddate ?? null;
			if(!is_null($emp)){
				$emp_id = $emp['id_employee'];
			}
			else{
				$emp_id = 1;
			}
            $data = EmployeeApproval::getdata_history($emp_id,$startdate,$enddate);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$button = '<button style="color:white;" type="button" name="view" id="' . $data->id_approval_transaction . '" class="view btn btn-warning btn-sm" title="View"><span class="far fa-eye fa-lg"></span><br><b>View</b></button> '; 
								
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);			
        }
        return view('employee.employee.approval_history.index_history');
    }
	
	public function get_view_approval(Request $request) {
		$filePath = '';
		$emp = Employee::where('id_user', session('id_user'))->where('id_company', session('id_company'))->where('status', 'A')->first();
        $data = [
            'id_approval_transaction' => $request->id_approval_transaction,
            'id_employee' => $emp['id_employee'],
        ];
        $result = EmployeeApproval::get_view_approval($data);
        if(is_null($result['attachment_type'])){
        	if($result['source_transaction_type'] == 'Career_Request'){
            	if(!is_null($result['attachment'])){
                    if (Storage::exists('public/upload/career/'.$result['nik_employee'].'/'.$result['attachment'])) {
						$filePath = url('project/storage/app/public/upload/career').'/'.$result['nik_employee'].'/'.$result['attachment'];
					} else {
						if (Storage::exists('public/upload/career/'.$result['id_employee_request'].'/'.$result['attachment'])) {
							$filePath = url('project/storage/app/public/upload/career').'/'.$result['id_employee_request'].'/'.$result['attachment'];
						}
					}
            	}
        	} 
        	else {
        		if(!is_null($result['attachment'])){
        			if (Storage::exists('public/upload/employee_request/'. $result['attachment'])) {
						$filePath = url('project/storage/app/public/upload/employee_request').'/'.$result['attachment'];
					} else {
						//pengkondisian upload attachment request yg masuk ke thumbnail (yg dulu)
						$filePath = url('project/storage/app/public/thumbnail').'/'.$result['attachment'];
					}
        		}
        	}
        }
        $result['filePath'] = $filePath;
		if($result['source_transaction_type'] == 'Leave_Request'){
			$request_start_to = explode(" ",$result['request_start_to']);
			$request_end_to = explode(" ",$result['request_end_to']);
			$result['request_start_to'] = $request_start_to[0];
			$result['request_end_to'] = $request_end_to[0];
		}
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_view_approval_history(Request $request) {
		$filePath = '';
		$emp = Employee::where('id_user', session('id_user'))->where('id_company', session('id_company'))->where('status', 'A')->first();
        $data = [
            'id_approval_transaction' => $request->id_approval_transaction,
            'id_employee' => $emp['id_employee'],
        ];
        $result = EmployeeApproval::get_view_approval_history($data);
        if(is_null($result['attachment_type'])){
        	if($result['source_transaction_type'] == 'Career_Request'){
            	if(!is_null($result['attachment'])){
                    if (Storage::exists('public/upload/career/'.$result['nik_employee'].'/'.$result['attachment'])) {
						$filePath = url('project/storage/app/public/upload/career').'/'.$result['nik_employee'].'/'.$result['attachment'];
					} else {
						if (Storage::exists('public/upload/career/'.$result['id_employee_request'].'/'.$result['attachment'])) {
							$filePath = url('project/storage/app/public/upload/career').'/'.$result['id_employee_request'].'/'.$result['attachment'];
						}
					}
            	}
        	} 
        	else {
        		if(!is_null($result['attachment'])){
        			if (Storage::exists('public/upload/employee_request/'. $result['attachment'])) {
						$filePath = url('project/storage/app/public/upload/employee_request').'/'.$result['attachment'];
					} else {
						//pengkondisian upload attachment request yg masuk ke thumbnail (yg dulu)
						$filePath = url('project/storage/app/public/thumbnail').'/'.$result['attachment'];
					}
        		}
        	}
        }
        $result['filePath'] = $filePath;
	//	dd($result);
        return response()->json($result);
    }

    public function approve_all(Request $request) {
        ini_set('max_execution_time', -1);
    	try{
			DB::beginTransaction();
	        $idApprovalTransaction = $request->id_approval_transaction ?? null;
	        $type = $request->type ?? null;
	        $getApproval = EmployeeApproval::whereIn('id_approval_transaction', $idApprovalTransaction)->get();
	        $idApprovalTransaction = [];
	        $countSuccess = [];
	        $countError = [];
	        $message = '';

	        foreach ($getApproval as $key => $val) {
	        	if($val->source_transaction_type == $type){
	        		$idApprovalTransaction[] = $val->id_approval_transaction;
	        	}
	        }
	        foreach ($idApprovalTransaction as $k => $idApproval) {
	        	$approve = self::approve($idApproval)->getOriginalContent();
	        	if($approve['status'] == 'true'){
	        		$dataEmail = $approve['data'];
	        		if(!is_array($dataEmail)){
	        			//jika isi data tidak array berarti ada data yg harus dikirim ke email, artinya final approve
	        			$req = new Request();
	        			$req->source = (array)$dataEmail;
	        			$this->EmailController->new_approval($req);
	        		}
	        		$countSuccess[] = $idApproval;
	        	} else {
	        		$countError[] = $idApproval;
	        	}
	        }

	        if(count($countSuccess) > 0){
	        	$message.= "Approved Success: ".count($countSuccess)." Data \n";
	        }
	        if(count($countError) > 0){
	        	$getIdRequestHeader = EmployeeApproval::whereIn('id_approval_transaction', $countError)->get()->pluck('id_source_transaction')->all();
	        	$getReffNumber = RequestHeader::whereIn('id_request_header', $getIdRequestHeader)->get()->pluck('reference_number')->all();
	        	$message.= "Approved Error: ".collect($getReffNumber)->implode(', ')."\n";
	        }

	        DB::commit();
			return response()->json(['status' => 'true', 'data' => $idApprovalTransaction, 'message'=>$message]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data' => null, 'message'=>'']);
        }
    }
	
	public function approve($id) {
		try{
			DB::beginTransaction();
		
		$get_partial =  EmployeeApproval::where('id_approval_transaction', $id)->first();
		$form_partial = [
			'id_source_transaction' => $get_partial->id_source_transaction,
			'source_transaction_type' => $get_partial->source_transaction_type,
			'sequence' => $get_partial->sequence,
			'id_approval_status' => $get_partial->id_approval_status,			
		];		
		
		for($i=1;$i<=$form_partial['sequence'];$i++){
			if($form_partial['sequence'] == $i){
				$s = $i;
			}
		}
		EmployeeApproval::where('id_approval_transaction', $id)->update(array(
			'update_date' => date('Y-m-d H:i:s'),
			'updated_by' => session('id_user'),
		));

			$mail_approval = [];
				if($form_partial['source_transaction_type'] == 'Leave_Request' || $form_partial['source_transaction_type'] == 'Attendance_Correction'|| $form_partial['source_transaction_type'] == 'Overtime_Request' || $form_partial['source_transaction_type'] == 'Cancel_Leave' || $form_partial['source_transaction_type'] == 'Change_Day_off'){
					$get_at = EmployeeApproval::where('id_source_transaction', $form_partial['id_source_transaction'])->where('source_transaction_type',$form_partial['source_transaction_type'])->where('sequence', $s)->get();
					$approve = EmployeeApproval::submit_approve($get_at[0]['id_company']);
					$partial = EmployeeApproval::partial_approve($get_at[0]['id_company']);
					$get_trans =  EmployeeApproval::get_trans($form_partial['id_source_transaction'], $form_partial['source_transaction_type']);		
						foreach($get_at as $v){
							if($v->source_transaction_type == 'Leave_Request' || $v->source_transaction_type == 'Attendance_Correction' || $v->source_transaction_type == 'Overtime_Request' || $v->source_transaction_type == 'Cancel_Leave' || $v->source_transaction_type == 'Change_Day_off'){
									EmployeeApproval::where('id_approval_transaction', $v->id_approval_transaction)->where('sequence', $s)->update(array(
										'id_approval_status' => $approve->id_general_data,
									//	'update_date' => date('Y-m-d H:i:s'),
									//	'updated_by' => session('id_user'),
									));																		
								}	
							}
						$final_request = RequestHeader::where('id_request_header', $form_partial['id_source_transaction'])->first();						
						$general_approve = EmployeeApproval::general_approve($final_request->id_approval_status,$get_at[0]['id_company']);
					//	if($general_approve->code == 'Partial_Approved' && count($get_trans) == 1){
						if(count($get_trans) == 1 && $form_partial['id_approval_status'] != $approve->id_general_data){
							$full_approve = RequestHeader::where('id_request_header', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $approve->id_general_data,
							));	
							
							$mail_approve = EmployeeApproval::get_mail_approval($final_request->id_request_header);
							$mail_approval = $mail_approve;
							
							if($mail_approve->request_start_to != null){
								if($mail_approve->request_code == 'Attendance_Correction'){
									$mail_approval->request_start_to = Carbon::parse($mail_approve->request_start_to)->format('d M Y H:i');
								}
								else{
									$mail_approval->request_start_to = Carbon::parse($mail_approve->request_start_to)->format('d M Y');
								}
							}
							else{
								$mail_approval->request_start_to = '-';
							}
							if($mail_approve->request_end_to != null){
								if($mail_approve->request_code == 'Attendance_Correction'){
									$mail_approval->request_end_to = Carbon::parse($mail_approve->request_end_to)->format('d M Y H:i');
								}
								else{
									$mail_approval->request_end_to = Carbon::parse($mail_approve->request_end_to)->format('d M Y');
								}
							}
							else{
								$mail_approval->request_end_to = '-';
							}
							
							EmployeeApproval::all_approve($get_at[0]['id_company'],$id);
						}
						else if(count($get_trans) > 1){							
							RequestHeader::where('id_request_header', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $partial->id_general_data,
							));	
						}
				
				}
				else if($form_partial['source_transaction_type'] == 'Career_Request'){	
					$get_at = EmployeeApproval::where('id_source_transaction', $form_partial['id_source_transaction'])->where('source_transaction_type',$form_partial['source_transaction_type'])->where('sequence', $s)->get();
					$approve = EmployeeApproval::submit_approve($get_at[0]['id_company']);
					$partial = EmployeeApproval::partial_approve($get_at[0]['id_company']);
					$get_trans =  EmployeeApproval::get_trans($form_partial['id_source_transaction'], $form_partial['source_transaction_type']);
					foreach($get_at as $v){
							if($v->source_transaction_type == 'Career_Request'){
									EmployeeApproval::where('id_approval_transaction', $v->id_approval_transaction)->where('sequence', $s)->update(array(
										'id_approval_status' => $approve->id_general_data,
									//	'update_date' => date('Y-m-d H:i:s'),
									//	'updated_by' => session('id_user'),
									));
								}	
							}
						$final_request = CareerTransition::where('id_career_transaction', $form_partial['id_source_transaction'])->first();
						
						$tran_type = CareerTransition::get_transaction_type($final_request->id_transaction_type);
						$general_approve = EmployeeApproval::general_approve($final_request->id_approval_status,$get_at[0]['id_company']);
						if(count($get_trans) == 1){
							if($tran_type->text == 'Temporary Assignment'){
								CareerTransition::where('id_career_transaction', $form_partial['id_source_transaction'])->update(array(
									'id_approval_status' => $approve->id_general_data,
									'executed' => 1,
								));
							}
							else{
								CareerTransition::where('id_career_transaction', $form_partial['id_source_transaction'])->update(array(
									'id_approval_status' => $approve->id_general_data,
								));	
								
								$this->CareerTransitionController->transition();
						//		EmployeeApproval::all_approve($get_at[0]['id_company'],$id);
							}
						}
						else if(count($get_trans) > 1){
							CareerTransition::where('id_career_transaction', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $partial->id_general_data,
							));
						}
				}
				else if($form_partial['source_transaction_type'] == 'Announcement_Request'){
					$get_at = EmployeeApproval::where('id_source_transaction', $form_partial['id_source_transaction'])->where('source_transaction_type',$form_partial['source_transaction_type'])->where('sequence', $s)->get();
					$approve = EmployeeApproval::submit_approve($get_at[0]['id_company']);
					$partial = EmployeeApproval::partial_approve($get_at[0]['id_company']);	
					$get_trans =  EmployeeApproval::get_trans($form_partial['id_source_transaction'], $form_partial['source_transaction_type']);
					foreach($get_at as $v){
							if($v->source_transaction_type == 'Announcement_Request'){
									EmployeeApproval::where('id_approval_transaction', $v->id_approval_transaction)->where('sequence', $s)->update(array(
										'id_approval_status' => $approve->id_general_data,
									//	'update_date' => date('Y-m-d H:i:s'),
									//	'updated_by' => session('id_user'),
									));
								}	
							}
						$final_request = MasterAnnouncement::where('id_announcement', $form_partial['id_source_transaction'])->first();
						$general_approve = EmployeeApproval::general_approve($final_request->id_approval_status,$get_at[0]['id_company']);						
						if(count($get_trans) == 1){
							MasterAnnouncement::where('id_announcement', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $approve->id_general_data,
							));	
						//	EmployeeApproval::all_approve($get_at[0]['id_company'],$id);
						}
						else if(count($get_trans) > 1){
							MasterAnnouncement::where('id_announcement', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $partial->id_general_data,
							));
						}
					
				}				
				else if($form_partial['source_transaction_type'] == 'FPK_Request'){
					$get_at = EmployeeApproval::where('id_source_transaction', $form_partial['id_source_transaction'])->where('source_transaction_type',$form_partial['source_transaction_type'])->where('sequence', $s)->get();
					$approve = EmployeeApproval::submit_approve($get_at[0]['id_company']);
					$partial = EmployeeApproval::partial_approve($get_at[0]['id_company']);
					$get_trans =  EmployeeApproval::get_trans($form_partial['id_source_transaction'], $form_partial['source_transaction_type']);		
						foreach($get_at as $v){
							if($v->source_transaction_type == 'FPK_Request'){
									EmployeeApproval::where('id_approval_transaction', $v->id_approval_transaction)->where('sequence', $s)->update(array(
										'id_approval_status' => $approve->id_general_data,
									));																		
								}	
							}
						$final_request = HiringRequest::where('id_hiring_request_header', $form_partial['id_source_transaction'])->first();	
						$max_sla = HiringRequest::max_hiring($final_request->id_position_routing_request);
						$general_approve = EmployeeApproval::general_approve($final_request->id_approval_status,$get_at[0]['id_company']);
						if(count($get_trans) == 1 && $form_partial['id_approval_status'] != $approve->id_general_data){
							$full_approve = HiringRequest::where('id_hiring_request_header', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $approve->id_general_data,
								'approval_date' => date('Y-m-d'),
								'target_date' => date('Y-m-d', strtotime('+'.$max_sla[0]->max_hiring_days.' days', strtotime(date('Y-m-d')))),
							));	
						}
						else if(count($get_trans) > 1){							
							HiringRequest::where('id_hiring_request_header', $form_partial['id_source_transaction'])->update(array(
								'id_approval_status' => $partial->id_general_data,
							));	
						}				
				}
				
		DB::commit();
		return response()->json(['status' => 'true', 'data' => $mail_approval]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data' => null]);
        }
    }
	
	public function revised(Request $request) {
		try{
			$text = $request->text;
			$id = $request->id;

			DB::beginTransaction();
			$ea = EmployeeApproval::where('id_approval_transaction', $id)->first();
			$approve = EmployeeApproval::revised_approve($ea['id_company']);
			$mail_approval = [];
			if($ea->source_transaction_type == 'Leave_Request' || $ea->source_transaction_type == 'Attendance_Correction' || $ea->source_transaction_type == 'Overtime_Request' || $ea->source_transaction_type == 'Cancel_Leave' || $ea->source_transaction_type == 'Change_Day_off'){
				RequestHeader::where('id_request_header', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_revised' => $text,
				));
				
				$mail_approve = EmployeeApproval::get_mail_approval($ea->id_source_transaction);
				$mail_approval = $mail_approve;
				$mail_approval->note_revise = $text;
			//	dd($mail_approval);
			}
			else if($ea->source_transaction_type == 'Career_Request'){
				CareerTransition::where('id_career_transaction', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_revised' => $text,
				));
			}
			else if($ea->source_transaction_type == 'Announcement_Request'){
				MasterAnnouncement::where('id_announcement', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_revised' => $text,
				));
			}
			else if($ea->source_transaction_type == 'FPK_Request'){
				HiringRequest::where('id_hiring_request_header', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_revised' => $text,
				));
			}
			EmployeeApproval::where('id_approval_transaction', $id)->update(array(
				'update_date' => date('Y-m-d H:i:s'),
				'updated_by' => session('id_user'),
			));
			$tipe = $ea->source_transaction_type;
			$appall = EmployeeApproval::where('id_source_transaction', $ea->id_source_transaction)->where('source_transaction_type', $tipe)->get();
				EmployeeApproval::where('id_approval_transaction', $id)->where('source_transaction_type', $tipe)->update(array(
					'note_revised' => $text,
				));
				foreach($appall as $val){	
					EmployeeApproval::where('id_approval_transaction', $val->id_approval_transaction)->update(array(
						'id_approval_status' => $approve->id_general_data,
					));					
				}

			DB::commit();
			return response()->json(['status' => 'true', 'data' => $mail_approval]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false']);
        }
    }
	
	public function rejected(Request $request) {
		try{
			$text = $request->text;
			$id = $request->id;
			
			DB::beginTransaction();		
			$mail_approval = [];
			$ea = EmployeeApproval::where('id_approval_transaction', $id)->first();		
			$approve = EmployeeApproval::rejected_approve($ea['id_company']);
			if($ea->source_transaction_type == 'Leave_Request' || $ea->source_transaction_type == 'Attendance_Correction' || $ea->source_transaction_type == 'Overtime_Request' || $ea->source_transaction_type == 'Cancel_Leave' || $ea->source_transaction_type == 'Change_Day_off'){
				RequestHeader::where('id_request_header', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_rejected' => $text,
				));
				
				$mail_approve = EmployeeApproval::get_mail_approval($ea->id_source_transaction);
				$mail_approval = $mail_approve;
				$mail_approval->note_reject = $text;
			}
			else if($ea->source_transaction_type == 'Career_Request'){
				CareerTransition::where('id_career_transaction', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_rejected' => $text,
				));
			}
			else if($ea->source_transaction_type == 'Announcement_Request'){
				MasterAnnouncement::where('id_announcement', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_rejected' => $text,
				));
			}
			else if($ea->source_transaction_type == 'FPK_Request'){
				HiringRequest::where('id_hiring_request_header', $ea->id_source_transaction)->update(array(
					'id_approval_status' => $approve->id_general_data,
					'note_rejected' => $text,
				));
			}
			
			EmployeeApproval::where('id_approval_transaction', $id)->update(array(
				'update_date' => date('Y-m-d H:i:s'),
				'updated_by' => session('id_user'),
			));
			
			$tipe = $ea->source_transaction_type;
			$appall = EmployeeApproval::where('id_source_transaction', $ea->id_source_transaction)->where('source_transaction_type', $tipe)->get();
				EmployeeApproval::where('id_approval_transaction', $id)->where('source_transaction_type', $tipe)->update(array(
					'note_rejected' => $text,
					'updated_by' => session('id_user'),
				));
				foreach($appall as $val){	
						EmployeeApproval::where('id_approval_transaction', $val->id_approval_transaction)->update(array(
							'id_approval_status' => $approve->id_general_data,
							'updated_by' => session('id_user'),
						));					
				}

			DB::commit();
			return response()->json(['status' => 'true', 'data' => $mail_approval]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false']);
        }
    }

}
