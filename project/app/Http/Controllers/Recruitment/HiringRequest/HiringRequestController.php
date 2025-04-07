<?php
namespace App\Http\Controllers\Recruitment\HiringRequest;

use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\Recruitment\HiringRequest\HiringDetail;
use App\Models\Recruitment\HiringRequest\HiringReco;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Employee\Employee\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HiringRequestController extends Controller {

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
			$dept_code = HiringRequest::get_dept_code();
			if(count($dept_code) > 0){
				$c = $dept_code[0]->department_code;
			}
			else{
				$c = null;
			}
            $data = HiringRequest::getdata($group_branch,$c);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$buttonSubmit = '&nbsp;<button type="button" name="submit" id="' . $data->id_hiring_request_header . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
                                $buttonEdit = '&nbsp;<button type="button" name="edit" id="' . $data->id_hiring_request_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 
								$buttonView = '&nbsp;<button type="button" name="view" id="' . $data->id_hiring_request_header . '" class="edit btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
								$buttonCancel = '&nbsp;<button type="button" name="cancel" id="' . $data->id_hiring_request_header . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
								$buttonInfo = '&nbsp;<button type="button" name="info" id="' . $data->id_hiring_request_header . '" class="info_track btn btn-success btn-sm" title="Info Tracking"><span class="fa fa-info-circle fa-lg"></span></button>';
                                
								if(in_array($data->code_app_status, ['New','Revised'])){
                            		$returnButton = $buttonSubmit.$buttonEdit.$buttonCancel;
                            	} else {
                            		$returnButton = $buttonSubmit.$buttonView.$buttonCancel.$buttonInfo;
                            	}
								return $returnButton;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('recruitment.recruitment.hiring_request.index');
    }
	
	public function index_summary(Request $request) {
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
            $data = HiringRequest::getdata_summary($group_branch);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '&nbsp;<button type="button" name="edit" id="' . $data->id_hiring_request_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 						
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('recruitment.recruitment.hiring_summary.index');
    }
	
	public function index_detail_summary(Request $request) {
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
            $data = HiringRequest::getdata_detail_summary($group_branch);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '&nbsp;<button type="button" name="edit" id="' . $data->id_hiring_request_detail . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> '; 						
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('recruitment.recruitment.hiring_detail_summary.index');
    }
	
/*	public function edit_page(Request $request) {
        return view('recruitment.recruitment.hiring_request.edit_page');
    }
*/	
	protected function validateReq(Request $request) {
        $arr_form_validate = [
			'reference_number' => 'unique:hr_hiring_request_header', Rule::unique('hr_hiring_request_header')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'pos_req' => 'required',
            'reason_notes' => 'required|string',
            'pkwt_duration' => 'required',
            'cc_email' => 'required',
            'effective_date' => 'required',
            'id_approval' => 'required',
            'id_approval_request' => 'required',
            'hiring.*.id_position_detail_request' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'pkwt_duration.required' => 'The Contract Duration field is required',
            'cc_email.required' => 'The HR Recruitment field is required',
            'pos_req.required' => 'The Request Position field is required',
            'reason_notes.required' => 'The Request Reason field is required',
            'id_approval.required' => 'The Hierarchy Approval field is required.(Contact Your HR)',
            'id_approval_request.required' => 'The Approved By field is required',
            'hiring.*.id_position_detail_request.required' => 'The Position Detail field is required',
        ];
        if ($request->post('hiring') == null) {
            $validate_emprequest = ['table_rec_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_rec_detail.required' => 'Position Detail cannot be empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
		if($request->have_recommended_employee == "on"){
			if ($request->post('reco') == null) {
				$validate_emprequest = ['table_rec_reco' => 'required|string'];
				$validate_msg_emprequest = ['table_rec_reco.required' => 'Employee Recommendation cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
			}
		}
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
	//	dd($request->all());
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$kode = HiringRequest::getkode();
			$form_data = array(
				'reference_number' => $kode,
				'id_employee_request' => $request->id_employee_request,
				'id_position_detail_employee_request' => $request->id_position_request,
				'id_location_employee_request' => $request->id_location_request,
				'recruitment_source' => $request->rec_source,
				'request_type' => $request->req_type,
				'reason_notes' => $request->reason_notes,
				'assigned_to' => $request->com_type,
				'id_position_routing_request' => $request->pos_req,
				'id_branch' => $request->id_branch,
				'skill_notes' => $request->skill_notes,
				'pkwt_duration' => $request->pkwt_duration,
				'request_date' => date('Y-m-d'),
				'effective_date' => $request->effective_date,
				'cc_email_to' => implode(',',$request->cc_email),
				'id_approval' => $request->id_approval,
				'id_approval_request' => $request->id_approval_request,
				'id_approval_status' => $request->id_approval_status,
				'have_recommended_employee' => isset($request->have_recommended_employee) == "on" ? 1 : 0,
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			
			$HiringSave = HiringRequest::create($form_data);
			
			foreach ($request->hiring as $key => $value) {
				$duplicate = HiringRequest::cek_duplicate($value['id_position_detail_request']);
				if(count($duplicate) != 0){
				//	dd($duplicate[0]->job_name);
					$titleError = "Ongoing Request (" .$duplicate[0]->job_name. ")";
					throw new \Exception($titleError);
				}
				
			}
		//	dd('OK');
			if($request->pos_length < count($request->hiring)){
				$titleError = "Request Over Quota (Max Position Detail " .$request->pos_length. " Slot)";
				throw new \Exception($titleError);
			}
			
			foreach ($request->hiring as $key => $value) {
				HiringDetail::create(array(
					'id_hiring_request_header' => $HiringSave->id_hiring_request_header,
					'id_position_detail_request' => $value['id_position_detail_request'],
					'id_employee_replacement' => $value['id_employee_replacement'],
					'id_location' => $value['id_location'],					
					'status' => 'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				));
			}
			
			if(isset($request->have_recommended_employee)){
				foreach ($request->reco as $key => $value) {
					HiringReco::create(array(
						'id_hiring_request_header' => $HiringSave->id_hiring_request_header,
						'id_employee_recommendation' => $value['id_employee_recommendation'],
						'id_position_detail' => $value['id_position_detail_reco'],
						'id_location' => $value['id_location_reco'],					
						'status' => 'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				}
			}
			
			$app = ApprovalTransaction::get_approval($request->id_approval);
			$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $HiringSave->id_hiring_request_header;
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
				return response()->json(['status' => 'true', 'data'=>$HiringSave->id_hiring_request_header, 'message' => 'Hiring Request Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Save Hiring Request !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {
	//	dd($request->all());
		$this->validateReq($request);
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_employee_request' => $request->id_employee_request,
				'id_position_detail_employee_request' => $request->id_position_request,
				'id_location_employee_request' => $request->id_location_request,
				'recruitment_source' => $request->rec_source,
				'request_type' => $request->req_type,
				'reason_notes' => $request->reason_notes,
				'assigned_to' => $request->com_type,
				'id_position_routing_request' => $request->pos_req,
				'id_branch' => $request->id_branch,
				'skill_notes' => $request->skill_notes,
				'pkwt_duration' => $request->pkwt_duration,
				'request_date' => date('Y-m-d'),
				'effective_date' => $request->effective_date,
				'cc_email_to' => implode(',',$request->cc_email),
				'id_approval' => $request->id_approval,
				'id_approval_request' => $request->id_approval_request,
				'id_approval_status' => $request->id_approval_status,
				'have_recommended_employee' => isset($request->have_recommended_employee) == "on" ? 1 : 0,
				'status' => 'A',
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
			
			$HiringSave = HiringRequest::findOrFail($request->id_hiring_request_header)->update($form_data);
			
			if($request->pos_length < count($request->hiring)){
				$titleError = "Request Over Quota (Max Position Detail " .$request->pos_length. " Slot)";
				throw new \Exception($titleError);
			}
			
			$listIdHiring    = [];
			$idHiring      = [];
			$listIdReco    = [];
			$idReco	     = [];
			if(HiringDetail::where('id_hiring_request_header', $request->id_hiring_request_header)->first() != null){
				$listIdHiring = HiringDetail::where('id_hiring_request_header', $request->id_hiring_request_header)->where('id_company', session('id_company'))->get()->pluck('id_hiring_request_detail')->all();
			}
			
			if(HiringReco::where('id_hiring_request_header', $request->id_hiring_request_header)->first() != null){
				$listIdReco = HiringReco::where('id_hiring_request_header', $request->id_hiring_request_header)->where('id_company', session('id_company'))->get()->pluck('id_hiring_request_recommendation')->all();
			}
			if($request->hiring) {
				foreach ($request->hiring as $key => $value) {
					if ($value['id_hiring_request_detail'] == "") {
						HiringDetail::create(array(
							'id_hiring_request_header' => $request->id_hiring_request_header,
							'id_position_detail_request' => $value['id_position_detail_request'],
							'id_employee_replacement' => $value['id_employee_replacement'],
							'id_location' => $value['id_location'],					
							'status' => 'A',
							'id_company' => session('id_company'),
							'created_by' => session('id_user'),
						));
					}
					 else {
						$idHiring[] = $value['id_hiring_request_detail'];
						HiringDetail::where('id_hiring_request_detail', $value['id_hiring_request_detail'])->update(array(
							'id_position_detail_request' => $value['id_position_detail_request'],
							'id_employee_replacement' => $value['id_employee_replacement'],
							'id_location' => $value['id_location'],					
							'status' => 'A',
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						));
					}
				}
			}	
			
			$diff = array_diff($listIdHiring, $idHiring);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					HiringDetail::where('id_hiring_request_detail', $value)->delete();
				}
			}
		
				if(isset($request->have_recommended_employee)){
					if($request->reco) {
						foreach ($request->reco as $key => $value) {
							if ($value['id_hiring_request_recommendation'] == "") {
								HiringReco::create(array(
									'id_hiring_request_header' => $request->id_hiring_request_header,
									'id_employee_recommendation' => $value['id_employee_recommendation'],
									'id_position_detail' => $value['id_position_detail_reco'],
									'id_location' => $value['id_location_reco'],					
									'status' => 'A',
									'id_company' => session('id_company'),
									'created_by' => session('id_user'),
								));
							}
							else {
								$idReco[] = $value['id_hiring_request_recommendation'];
								HiringReco::where('id_hiring_request_recommendation', $value['id_hiring_request_recommendation'])->update(array(
									'id_employee_recommendation' => $value['id_employee_recommendation'],
									'id_position_detail' => $value['id_position_detail_reco'],
									'id_location' => $value['id_location_reco'],					
									'status' => 'A',
									'id_company' => session('id_company'),
									'updated_by' => session('id_user'),
								));
							}
						}
					}
					
					$diffReco = array_diff($listIdReco, $idReco);
					if(count($diffReco) > 0){
						foreach ($diffReco as $key => $value) { 
							HiringReco::where('id_hiring_request_recommendation', $value)->delete();
						}
					}
				}
				else{
					HiringReco::where('id_hiring_request_header', $request->id_hiring_request_header)->delete();
				}
			
			$app = ApprovalTransaction::get_approval($request->id_approval);
			$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_hiring_request_header)->where('source_transaction_type', 'FPK_Request')->delete();
			
			$approve = ApprovalTransaction::get_app_custom($request->id_employee_request,session('id_company'),$request->id_approval);
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $request->id_hiring_request_header;
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
				return response()->json(['status' => 'true', 'data'=>$request->id_hiring_request_header, 'message' => 'Hiring Request Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Update Hiring Request !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update_summary(Request $request) {
		try{
			DB::beginTransaction();
			$form_data = array(
				'is_web_posting' => isset($request->is_web_posting) == "on" ? 1 : 0,
				'hiring_request_status' => $request->req_status,
				'updated_by' => session('id_user'),
			);
			
			$HiringSave = HiringRequest::findOrFail($request->id_hiring_request_header)->update($form_data);
				
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Hiring Request Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Hiring Request !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	public function submit_approve($id) {
		try{
			DB::beginTransaction();
		$id_approval = HiringRequest::where('id_hiring_request_header', $id)->first();
		$approve = HiringRequest::submit_approve();
	//	dd($approve);
			HiringRequest::where('id_hiring_request_header', $id)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));	
			
		$data = [
            'id_hiring_request_header' => $id,
            'id_approval' => $id_approval->id_approval
			];
		$data_status = HiringRequest::getdata_approval_status($data);
		foreach ($data_status as $key => $value) {
			if($value->code == "New" || $value->code == "Cancel" || $value->code == "Revised"){		
				 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', $value->source_transaction_type)->update(array(
					'id_approval_status' => $approve->id_general_data,
				//	'updated_by' => session('id_user'),
				));	
			}	
		}
		
		$data_mail = HiringRequest::getdata_approval_mail($data);
		$data_hiring = HiringRequest::get_summary_edit($data);
		
		$email_rec = HiringRequest::get_def_mail();
		$x = [] ;
			foreach($email_rec as $key=>$val){
				$x[] = $val->recruitment_email;
			}
			
			$rec_mail = implode(',',$x);
		
		$get_mail = explode(',', $data_hiring['cc_email_to']);
		$join_mail = collect($x)->merge(collect($get_mail));

		$data_hiring['creation_date'] = Carbon::parse($data_hiring['creation_date'])->format('d M Y');
		$data_hiring['effective_date'] = Carbon::parse($data_hiring['effective_date'])->format('d M Y');
		$data_hiring['cc_email'] = $join_mail->toArray();
		foreach($data_mail as $val){
				$dm['id_employee_approval'] = $val->id_employee_approval;
				$dm['name'] = $val->name;
				$dm['private_mail'] = $val->private_mail;
				$dm['sequence'] = $val->sequence;
				$dm['permohonan'] = $val->permohonan;
				$d_mail[] = $dm;				
		}
		unset($data_hiring['cc_email_to']);
		$header['req'] = $data_hiring;
		$header['transaction'] = $d_mail;		
	//	dd($header);
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
		$id_approval = HiringRequest::where('id_hiring_request_header', $id)->first();	
		$cancel = HiringRequest::cancel();
        HiringRequest::where('id_hiring_request_header', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
				'hiring_request_status' => 'C',
			));
		 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'FPK_Request')->update(array(
					'id_approval_status' => $cancel->id_general_data,
					// 'updated_by' => session('id_user'),
				));	

		DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
	
	public function get_hiring_edit(Request $request) {
        $data = [
            'id_hiring_request_header' => $request->id_hiring_request_header
        ];
        $result = HiringRequest::get_hiring_edit($data);
		$result = HiringRequest::get_existing_data_edit($result);
		$result['cc_email'] = explode(',',$result['cc_email_to']);
        return response()->json($result);
    }
	
	public function get_summary_edit(Request $request) {
		$data_access = Employee::get_access($request->id_url);
			if($data_access != null){
				foreach($data_access as $value){
					$x[] = $value->id_branch;
				}
				$group_branch = $x;
			}
			else{
				$group_branch = null;
			}
        $data = [
            'id_hiring_request_header' => $request->id_hiring_request_header
        ];
		if($group_branch != null){
			if(count($group_branch) > 0){
				$access = true;
			}
		}
		else{
			$access = false;
		}
        $result = HiringRequest::get_summary_edit($data);
		$result['cc_email'] = explode(',',$result['cc_email_to']);
		$result['access'] = $access;
        return response()->json($result);
    }
	
	public function get_employee_by() {
        $result = HiringRequest::get_employee_by();
        return response()->json($result);
    }
	
	public function get_pos_by(Request $request) {
		$data = [
            'id_pos_detail' => $request->id_pos_detail,
            'id_routing' => $request->id_routing,
            'job_class_group' => $request->job_class_group,
            'route_name' => $request->route_name,
            'name_com_type' => $request->name_com_type,
            'name_req_type' => $request->name_req_type,
        ];
        $result = HiringRequest::get_pos_by($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_sla(Request $request) {
		$data = [
            'route_sla' => $request->route_sla,
        ];
		if($data['route_sla'] == null){
			$result = "";
		}
		else{
			$result = HiringRequest::get_sla($data);
		}
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_branch(Request $request) {
		$data = [
            'id_route' => $request->id_route,
			'name_req_type' => $request->name_req_type,
			'id_branch' => $request->id_branch,
        ];
		if($data['id_route'] == null){
			$result = "";
		}
		else{
			$result = HiringRequest::get_branch($data);
		}
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_pos_detail(Request $request) {
		$data = [
            'id_route' => $request->id_route,
            'id_branch' => $request->id_branch,
			'name_req_type' => $request->name_req_type,
			'status_view' => $request->status_view,
        ];
		if($data['id_route'] == null || $data['id_branch'] == null){
			$result = "";
		}
		else{
			$result = HiringRequest::get_pos_detail($data);
		}
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_emp_reco() {
        $result = HiringRequest::get_emp_reco();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_approval_status() {
        $result = HiringRequest::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_hr_email() {
        $result = HiringRequest::get_hr_email();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_emp_edit(Request $request) {
		$data = [
            'id_pos_detail' => $request->id_position_detail,           
        ];
        $result = HiringRequest::get_emp_edit($data);
        return response()->json($result);
    }
	
	public function get_hierachy_fpk(Request $request) {
		$data = [
            'code' => 'FPK_Request',
            'id_employee' => $request->id_employee,
        ];		
        $result = ApprovalTransaction::get_hierachy_fpk($data);
        return response()->json($result);
    }
	
	public function get_hierachy_approver(Request $request) {
		$data = [
            'id_approve' => $request->id_approve,
        ];
        $result = HiringRequest::get_hierachy_approver($data);
        return response()->json($result);
    }
	
	public function get_detail_summary_edit(Request $request) {
        $data = [
            'id_hiring_request_detail' => $request->id_hiring_request_detail
        ];
        $result = HiringRequest::get_detail_summary_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	
	protected function update_detail_summary(Request $request) {
		try{
			DB::beginTransaction();
			$form_data = array(
				'notes' => strip_tags($request->notes),
				'update_date_notes' => date('Y-m-d H:i:s' ),
				'updated_by' => session('id_user'),
			);
			
			HiringDetail::where('id_hiring_request_detail', $request->id_hiring_request_detail)->update($form_data);
				
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Notes Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => 'Cannot Edit Notes !! [' . $e->getMessage() . ']']);           
        }
    }
	
	public function get_info(Request $request) {
        if ($request->ajax()) {
			$param = [
				'id_hiring_request_header' => $request->id_hiring_request_header,
			];
            $data = HiringRequest::getdata_info($param);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->make(true);
        }
    }
}
