<?php

namespace App\Http\Controllers\CareerAdministration\CareerTransition;

use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Models\Employee\Employee\Education;
use App\Models\Employee\Employee\Family;
use App\Models\Employee\Employee\Bank;
use App\Models\Employee\Employee\Insurance;
use App\Models\Employee\Employee\EmployeeLeave;
use App\Models\Employee\Employee\Document;
use App\Models\Employee\Employee\Boarding;
use App\Models\Curl;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TimeAttendance\Attendance\AttendanceController;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CareerTransitionController extends Controller {
	
	public function __construct()
	{
        $this->AttendanceController = new AttendanceController;

        $statusApiUpdateNik = 'production'; //optional ['staging', 'production']
        if($statusApiUpdateNik == 'production'){
            $apiIsActiveUser = curl::findApi('myborwita_user_update_is_active_production');
        	$this->urlApiIsActiveUser = @$apiIsActiveUser->url;
        	$this->usernameApiIsActiveUser = @$apiIsActiveUser->user;
        	$this->passwordApiIsActiveUser = @$apiIsActiveUser->password;

            $apiUpdateNikUser = curl::findApi('myborwita_user_update_nik_production');
        	$this->urlApiUpdateNikUser = @$apiUpdateNikUser->url;
        	$this->usernameApiUpdateNikUser = @$apiUpdateNikUser->user;
        	$this->passwordApiUpdateNikUser = @$apiUpdateNikUser->password;
        } 
        else {
        	$apiIsActiveUser = curl::findApi('myborwita_user_update_is_active_staging');
        	$this->urlApiIsActiveUser = @$apiIsActiveUser->url;
        	$this->usernameApiIsActiveUser = @$apiIsActiveUser->user;
        	$this->passwordApiIsActiveUser = @$apiIsActiveUser->password;

            $apiUpdateNikUser = curl::findApi('myborwita_user_update_nik_staging');
        	$this->urlApiUpdateNikUser = @$apiUpdateNikUser->url;
        	$this->usernameApiUpdateNikUser = @$apiUpdateNikUser->user;
        	$this->passwordApiUpdateNikUser = @$apiUpdateNikUser->password;
        }
	}

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
			
            $data = CareerTransition::getdata($group_branch);
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
										}
									}
                            	}
                                return $storagePath;
                            })
                            ->addColumn('action', function($data) {								
                                $button = '<button type="button" name="submit" id="' . $data->id_career_transaction . '" class="submit_approve btn btn-info btn-sm" title="Submit"><span class="fas fa-paper-plane"></span></button> ';
								$button .= '<button type="button" name="edit" id="' . $data->id_career_transaction . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';                          
								$button .= '<button type="button" name="cancel" id="' . $data->id_career_transaction . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
								
							/*	$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_career_transaction . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
							*/	
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('career_administration.career_transition.career_transition_request.index');
    }

	protected function validateCareer(Request $request) {
		if($request->code_transaction_type == 'Rehire_Employee'){
			$request->validate([
			'reference_number' => 'unique:hr_career_transaction', Rule::unique('hr_career_transaction')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
		
            'id_employee' => 'required',
            'effective_date' => 'required',
            'attachment' => 'max:2016',
                ], [],
                [
                    'id_employee' => 'Employee',
                    'reference_number' => 'Reference Number',
                    'enable_approval' => 'Enable Approval',
                    'id_approval' => 'Approval',
                    'effective_date' => 'Effective Date',
			]);
		}
		else{
			 $request->validate([
			'reference_number' => 'unique:hr_career_transaction', Rule::unique('hr_career_transaction')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),		
            'id_employee' => 'required',
            'enable_approval' => 'required',
            'id_approval' => 'required',
            'effective_date' => 'required',
            'attachment' => 'max:2016',
                ], [],
                [
                    'id_employee' => 'Employee',
                    'reference_number' => 'Reference Number',
                    'enable_approval' => 'Enable Approval',
                    'id_approval' => 'Approval',
                    'effective_date' => 'Effective Date',
			]);
		}
	}
   
	protected function validateCareerUpdate(Request $request) {
		if($request->code_transaction_type == 'Rehire_Employee'){
			$request->validate([
			'id_employee' => 'required',			
            'effective_date' => 'required',
            'attachment' => 'max:2016',
                ], [],
                [
					'id_employee' => 'Employee',
                    'effective_date' => 'Effective Date',
			]);
		}
		else{
			$request->validate([
			'id_employee' => 'required',
			'enable_approval' => 'required',
			'id_approval' => 'required',
            'effective_date' => 'required',
            'attachment' => 'max:2016',
                ], [],
                [
					'id_employee' => 'Employee',
					'enable_approval' => 'Enable Approval',
					'id_approval' => 'Approval',
                    'effective_date' => 'Effective Date',
			]);
		}
	}
	protected function save(Request $request) {
		$this->validateCareer($request);	
		try{
			DB::beginTransaction();
			if($request->attachment != ""){
				$getEmployee = DB::table('hr_employee')->select('nik_employee')->where('id_employee', $request->id_employee)->first();
				$nikEmployee = $getEmployee->nik_employee;

				$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
				$image = $rnd."-".$request->id_employee.".".$request->attachment->getClientOriginalExtension();
				$dir = Storage::makeDirectory('public/upload/career/'.$nikEmployee, 0775, true, true);
				$storageimage = Storage::putFileAs('public/upload/career/'.$nikEmployee, $request->attachment,$image);
				// $dir = Storage::makeDirectory('public/upload/career/'.$request->id_employee,0775, true, true);
				// $storageimage = Storage::putFileAs('public/upload/career/'.$request->id_employee,$request->attachment,$image);
			}
			else{
				$image = NULL;
			}
			$kode = CareerTransition::getkode();
		//	dd($kode);
			$approved = CareerTransition::approved();
			$form_data = array(
				'reference_number' => $kode,
			//    'transaction_number' => $request->transaction_number,
				'id_employee' => $request->id_employee,
				'id_employee2' => $request->id_employee2,
				'id_transition_category' => $request->id_transition_category,
				'id_transaction_type' => $request->id_transaction_type,
				'id_old_employment_status' => $request->id_old_employment_status,
				'id_employment_status' => $request->id_employment_status,
				'id_old_position_detail' => $request->id_old_position_detail,
				'id_position_detail' => $request->id_position_detail,
				'id_position_routing' => $request->id_position_routing,
				'id_job_grade' => $request->id_job_grade,
				'id_job_status' => $request->id_job_status,
				'id_location' => $request->id_location,
				'remark' => $request->remark,				
				'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
				'id_approval' => $request->id_approval,
				'id_approval_status' => ($request->id_approval != null) ? $request->id_approval_status : $approved->id_general_data,
				'id_company_destination' => $request->id_company_destination,
				'effective_date' => $request->effective_date,
				'expired_date' => $request->expired_date,            
				'attachment' => $image,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);

		if($request->id_position_detail == NULL){
			$id_pos_detail = $request->id_old_position_detail;
		}	
		else if($request->id_position_detail != NULL){
			$id_pos_detail = $request->id_position_detail;
		}
	
		$get_effective = CareerTransition::get_effective_date($id_pos_detail);
		$get_not_request = CareerTransition::get_not_request($request->id_employee);
		$get_request_termination = CareerTransition::get_request_termination($request->id_employee);
		$get_request = CareerTransition::get_request($request->id_employee);
		if($get_not_request){
			return response()->json(['status' => 'false_date', 'message' => 'Career Request tidak bisa dibuat, Karena masih ada Career Request dari karyawan tersebut yang belum selesai (Career Request masih berjalan).']);
		}
		else if($request->effective_date <= @$get_request[0]->effective_date){
			return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih lebih kecil dari Effective Date Career Request yang sebelumnya. (Ganti Effective Date yang lebih besar)']);
		}
		
		if($get_effective){
			if(@$get_effective[0]->code != "Termination"){
				if(@$get_effective[0]->id_position_detail == $request->id_position_detail){
					return response()->json(['status' => 'false_date', 'message' => 'Position yang dipilih telah digunakan karyawan lain. (Buat Position Baru)']);
				}
			/*	else if($request->effective_date < @$get_effective[0]->effective_date){
					return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
				}
			*/
			}
			/*
			else if($request->effective_date <= @$get_effective[0]->effective_date){
				return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
			}
			*/
			
		}
		if($request->code_transaction_type != 'Rehire_Employee'){
			if(!empty($get_request_termination)){
					return response()->json(['status' => 'false_date', 'message' => 'Career Request tidak bisa dibuat, Karena ada Proses Termination dari karyawan tersebut.']);
				}
		}
		if($request->code_transaction_type == 'Termination'){
			if(!empty($get_request_termination)){
				return response()->json(['status' => 'false_date', 'message' => 'Career Request Termination tidak bisa dibuat, Karena masih ada Proses Termination dari karyawan tersebut (Double Termination).']);
			}
			$form_data['id_employment_status'] = $request->id_old_employment_status;
			$form_data['id_terminate_reason'] = $request->id_terminate_reason;
			$form_data['resign_category'] = $request->resign_category;
			$form_data['expired_date'] = NULL;
			$form_data['id_position_detail'] = NULL;
			$form_data['id_position_routing'] = NULL;
			$form_data['id_job_grade'] = NULL;
			$form_data['id_job_status'] = NULL;
			$form_data['id_location'] = NULL;
		}
		else if($request->code_transaction_type != 'Termination' && $request->code_transaction_type != 'Rehire_Employee'){
			if($request->id_position_detail == NULL){
				$form_data['id_position_detail'] = $request->id_old_position_detail;
			}
		}
		$trans_type = CareerTransition::get_transaction_type($request->id_transaction_type);
		if($trans_type->text == "Employment Status Changes" || $trans_type->text == "Pass Orientation" || $trans_type->text == "Pass RPK"){
			$form_data['id_position_detail'] = $request->id_old_position_detail;
		}	
		$career = CareerTransition::create($form_data);
		if($request->code_transaction_type != 'Rehire_Employee'){
			$app = ApprovalTransaction::get_approval($request->id_approval);
		//	dd($app);
			if($app[0]->hierarchy_type == "Organization"){
				$approve = ApprovalTransaction::get_app_org($request->id_employee,session('id_company'));
			}
			else if($app[0]->hierarchy_type == "Combine"){
				$approve = ApprovalTransaction::get_app_combine($request->id_employee,session('id_company'),$request->id_approval);
			}
			else if($app[0]->hierarchy_type == "Custom"){
				$approve = ApprovalTransaction::get_app_custom_career($request->id_employee,session('id_company'),$request->id_approval);
			}
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $career->id_career_transaction;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
					$source[$key]['id_approval_detail'] = isset($value->id_approval_detail) ? $value->id_approval_detail: null;
					$source[$key]['id_approval_status'] = $app[0]->id_approval_status;
					$source[$key]['id_approval_mode'] = $value->id_approval_mode;
					$source[$key]['sequence'] = $value->sequence;
					$source[$key]['id_position_detail'] = $value->id_position_detail;
					$source[$key]['id_employee_approval'] = $value->id_employee_approval;
					$source[$key]['id_user'] = $value->id_user;
			}
			
			foreach ($source as $key => $value) {
				$create_apptrans = array(
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
				
				ApprovalTransaction::create($create_apptrans);
				/*
				if(count($source) > 1 && $value['id_user'] != session('id_user')){
					ApprovalTransaction::create($create_apptrans);
				}
				else{
					if(count($source) == 1 && $value['id_user'] == session('id_user')){
						$approved = CareerTransition::approved();
						CareerTransition::where('id_career_transaction', $career->id_career_transaction)->update(array(
							'id_approval_status' => $approved->id_general_data,			
						));
					}
					else{
						ApprovalTransaction::create($create_apptrans);
					}
				}
				*/
			}
		}
		else if($request->code_transaction_type == 'Rehire_Employee'){
			$this->transition();
		}
			DB::commit();
			return response()->json(['status' => 'true',  'data'=>$career->id_career_transaction, 'message' => 'Career Transition Request Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false',  'data'=>null, 'message' => 'Cannot Save Career Transition Request !! [' . $e->getMessage() . ']']);           
        }
    }

    public function update(Request $request) {
		 $this->validateCareerUpdate($request);
	try{
			DB::beginTransaction();
			$form_data = array(
				//	'transaction_number' => $request->transaction_number,
					'id_employee' => $request->id_employee,
					'id_employee2' => $request->id_employee2,
					'id_transition_category' => $request->id_transition_category,
					'id_transaction_type' => $request->id_transaction_type,
					'id_old_employment_status' => $request->id_old_employment_status,
					'id_employment_status' => $request->id_employment_status,
					'id_old_position_detail' => $request->id_old_position_detail,
					'id_position_detail' => $request->id_position_detail,
					'id_position_routing' => $request->id_position_routing,
					'id_job_grade' => $request->id_job_grade,
					'id_job_status' => $request->id_job_status,
					'id_location' => $request->id_location,
					'remark' => $request->remark,
				//	'id_terminate_reason' => $request->id_terminate_reason,
				//	'resign_category' => $request->resign_category,
					'enable_approval' => isset($request->enable_approval) == "on" ? 1 : 0,
					'id_approval' => $request->id_approval,
					'id_approval_status' => $request->id_approval_status,
					'id_company_destination' => $request->id_company_destination,
					'effective_date' => $request->effective_date,
					'expired_date' => $request->expired_date,            
					'status' => $request->status,
					'id_company' => session('id_company'),
					'updated_by' => session('id_user'),
				);
		if($request->attachment != ""){
			$getEmployee = DB::table('hr_employee')->select('nik_employee')->where('id_employee', $request->id_employee)->first();
			$nikEmployee = $getEmployee->nik_employee;
				
			$rnd = rand(1000,9999).strtotime(date('Y-m-d'));
			$image = $rnd."-".$request->id_employee.".".$request->attachment->getClientOriginalExtension();
			$dir = Storage::makeDirectory('public/upload/career/'.$nikEmployee,0775, true, true);
			$storageimage = Storage::putFileAs('public/upload/career/'.$nikEmployee,$request->attachment,$image);
			$form_data['attachment'] = $image;							
		}
		if($request->code_transaction_type == 'Termination'){
			$form_data['id_employment_status'] = $request->id_old_employment_status;
			$form_data['id_terminate_reason'] = $request->id_terminate_reason;
			$form_data['resign_category'] = $request->resign_category;
			$form_data['expired_date'] = NULL;
			$form_data['id_position_detail'] = NULL;
			$form_data['id_position_routing'] = NULL;
			$form_data['id_job_grade'] = NULL;
			$form_data['id_job_status'] = NULL;
			$form_data['id_location'] = NULL;
		}
		
		// $get_effective = CareerTransition::get_effective_date($request->id_position_detail);
		$get_request = CareerTransition::get_request($request->id_employee);
		
		if($request->effective_date <= @$get_request[0]->effective_date){
			return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih lebih kecil dari Effective Date Career Request yang sebelumnya. (Ganti Effective Date yang lebih besar)']);
		}
	/*	
		if($get_effective){			
			if(@$get_effective[0]->code != "Termination"){
				if($request->effective_date < @$get_effective[0]->effective_date){
					return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
				}
			}
			else if($request->effective_date <= @$get_effective[0]->effective_date){
				return response()->json(['status' => 'false_date', 'message' => 'Effective Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
			}
		}
	*/
		$trans_type = CareerTransition::get_transaction_type($request->id_transaction_type);
		if($trans_type->text == "Employment Status Changes" || $trans_type->text == "Pass Orientation" || $trans_type->text == "Pass RPK"){
			$form_data['id_position_detail'] = $request->id_old_position_detail;
		}	

		$career = CareerTransition::findOrFail($request->id_career_transaction)->update($form_data);
			
		/*	if(strtotime($career->effective_date) <= strtotime(date('Y-m-d'))){
				JobPositionDetail::where('id_position_detail', $career->id_old_position_detail)->update(array(
							'id_employee' => NULL,
						));	
				JobPositionDetail::where('id_position_detail', $career->id_position_detail)->update(array(
					'id_employee' => $career->id_employee,
				));	
			}
		*/
		if($request->code_transaction_type != 'Rehire_Employee'){
			$app = ApprovalTransaction::get_approval($request->id_approval);
			$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_career_transaction)->delete();
		//	dd($app);
			if($app[0]->hierarchy_type == "Organization"){
				$approve = ApprovalTransaction::get_app_org($request->id_employee,session('id_company'));
			}
			else if($app[0]->hierarchy_type == "Combine"){
				$approve = ApprovalTransaction::get_app_combine($request->id_employee,session('id_company'),$request->id_approval);
			}
			else if($app[0]->hierarchy_type == "Custom"){
				$approve = ApprovalTransaction::get_app_custom_career($request->id_employee,session('id_company'),$request->id_approval);
			}
			foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] =  $request->id_career_transaction;
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
		}
			DB::commit();
			return response()->json(['status' => 'true', 'data'=>$request->id_career_transaction,'message' => 'Career Transition Request Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data'=>null, 'message' => 'Cannot Updated Career Transition Request !! [' . $e->getMessage() . ']']);           
        }
	
    }

    public function transition() {
    	ini_set('max_execution_time', -1);
		try{
			DB::beginTransaction();
			//variabel utk call API HRIS Mobile
			$updateNikMobile = [];
            $career = CareerTransition::transition_career(); 
		//	$i = 0;
		//	$form = [];			
			foreach($career as $key=>$value){
				$type = str_replace(' ','_',$value->type);
				$dateplus = date('Y-m-d',strtotime("+1 day",strtotime($value->effective_date)));				
				if($dateplus <= date('Y-m-d') && $value->code == 'Approved' && $value->executed == 0 && $value->category == 'Termination'){
						$mp_old = JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->first();
						if($value->id_employee == $mp_old->id_employee){
							$up_emp = JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->update(array(
								'id_employee' => null,
							//	'id_employee2' => null,
							));	
							if(!is_null($mp_old->id_employee2)){
								$up_emp = JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->update(array(
									'id_employee' => $mp_old->id_employee2,
									'id_employee2' => NULL,
								));	
							}
						}
						
					/*	
						else{
							$up_emp = JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->update(array(
								'id_employee2' => null,
							));
						}
					*/
						$job_term = CareerTransition::get_last_position($value->id_old_position_detail);		
						$car_resign = CareerTransition::get_type_reason($value->id_terminate_reason);
						try{
							$up_emp2 = Employee::where('id_employee', $value->id_employee)->update(array(
								'last_position_routing' => @$job_term[0]->position_routing,
								'last_department' => @$job_term[0]->department." (".@$job_term[0]->location.")",
								'resign_date' => $value->effective_date,
								'status' => 'I',
								'additional_note' => $value->remark,							
								'terminate_reason' => '('.$car_resign[0]->code.'-'.$value->resign_category.') '.$car_resign[0]->description,
							));	
						} catch (\Exception $e) {
							$idError = $value->id_employee;
							$titleError = 'Error Employee Transition : '.$value->category.'. Employee '.$idError;
							$bodyError = ' cannot update [status=I] by id_employee ('.$idError.')';
							\Log::error($titleError.$bodyError);

							if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
						    	throw new \Exception($e->getMessage());    
						    } else {
						    	throw new \Exception($titleError);    
						    }
				        }
						
						$emp_user = Employee::where('id_employee', $value->id_employee)->first();
						try{
							$up_user = MasterUser::where('id_user', $emp_user->id_user)->update(array(
								'status' => 'I',
							));
						} catch (\Exception $e) {
							$idError = $emp_user->id_user;
							$titleError = 'Error Empoloyee Transition : '.$value->category.'. User '.$idError;
							$bodyError = ' cannot update [status=I] by id_user ('.$idError.')';
							\Log::error($titleError.$bodyError);
				            
							if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
						    	throw new \Exception($e->getMessage());    
						    } else {
						    	throw new \Exception($titleError);    
						    }        
				        }
						
						if(!$up_emp || !$up_emp2 || !$up_user){
							 throw new \Exception("This is an exception");
						}
						DB::table('hr_career_transaction')->where('id_career_transaction', $value->id_career_transaction)->update(array(
							'executed' => 1,				
						));

						//======== utk kebutuhan call API HRIS Mobile =======
						$updateNikMobile[] = ['nik'=>$emp_user->nik_employee, 'is_active'=>false, 'type'=>'inactive'];
						// ==================================================
				}
				if($value->effective_date <= date('Y-m-d') && $value->code == 'Approved' && $value->executed == 0 && $value->category != 'Termination'){	
					$job_emp = JobPositionDetail::where('id_position_detail',$value->id_position_detail)->first();
					if($type == 'New_Employee'){
						if($job_emp->secondary_position == true){
							JobPositionDetail::where('id_position_detail',$value->id_position_detail)->update(array(
								'id_employee' => $value->id_employee,
								'secondary_position' => false,
							));
						}
						else if(!is_null($job_emp->id_employee)){
						$up_emp_new = JobPositionDetail::where('id_position_detail', $value->id_position_detail)->update(array(
								'id_employee2' => $value->id_employee,
							));
						}
						else if(is_null($job_emp->id_employee)){
							JobPositionDetail::where('id_position_detail',$value->id_position_detail)->update(array(
								'id_employee' => $value->id_employee,
							));
						}	
							
						DB::table('hr_career_transaction')->where('id_career_transaction', $value->id_career_transaction)->update(array(
							'executed' => 1,				
						));
					}				
					if($type == 'Rehire_Employee'){
					//	$x = [];
						$emp_rehire = Employee::where('id_employee', $value->id_employee)->get()->makeHidden(['id_employee'])->toArray();
						foreach($emp_rehire as $k=>$val){
											
							$val['join_date'] = $value->effective_date;
							$val['expired_date'] = $value->expired_date;
							$val['status'] = 'A';
							$val['resign_date'] = null;
							$val['additional_note'] = null;
							$val['terminate_reason'] = null;
							$val['last_department'] = null;
							$val['last_position_routing'] = null;
							
							$val['id_employment_status'] = $value->id_employment_status;
							$val['created_by'] = $value->created_by;

							try{
								$emp = Employee::create($val);
							} catch (\Exception $e) {
								$idError = $value->id_employee;
								$titleError = 'Error Empoloyee Transition : '.$value->category.'. Employee '.$idError;
								$bodyError = ' cannot create new employee ('.$idError.')';
								\Log::error($titleError.$bodyError);
					            
								if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
							    	throw new \Exception($e->getMessage());    
							    } else {
							    	throw new \Exception($titleError);    
							    }
					        }

						//	$x[] = $val;
							
							if($value->id_position_detail != null){	
								if(!is_null($job_emp->id_employee)){
									$up_emp_new = JobPositionDetail::where('id_position_detail', $value->id_position_detail)->update(array(
											'id_employee2' => $emp->id_employee,
										));
								}
								else{
									JobPositionDetail::where('id_position_detail', $value->id_position_detail)->update(array(
										'id_employee' => $emp->id_employee,
									));
								}
							}
														
							try{
								MasterUser::where('id_user', $emp->id_user)->update(array(
									'status' => 'A',
								));
							} catch (\Exception $e) {
								$idError = $emp->id_user;
								$titleError = 'Error Empoloyee Transition : '.$value->category.'. User '.$idError;
								$bodyError = ' cannot update [status=A] by id_user ('.$idError.')';
								\Log::error($titleError.$bodyError);
					            
								if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
							    	throw new \Exception($e->getMessage());    
							    } else {
							    	throw new \Exception($titleError);    
							    }
					        }
							
							CareerTransition::where('id_employee', $value->id_employee)->where('reference_number', $value->reference_number)->update(array(
								'id_employee' => $emp->id_employee,			
							));
							
							$req = new Request();
							$req->id_employee = $emp->id_employee;
							$req->id_company = $emp->id_company;
						//	dd($req);
							$this->AttendanceController->generateWorkdaysByEmployee($req);
							DB::select("select * from  GenerateMassLeave (?, ?, ?, ?)",[$emp->id_company,$emp->id_user,$emp->id_employee,date('Y-m-d')]);
							
							$emp_doc = Document::where('id_employee', $value->id_employee)->get()->makeHidden(['id_document_employee'])->toArray();
							if($emp_doc){
								foreach($emp_doc as $d=>$doc_val){
									$doc_val['id_employee'] = $emp->id_employee;
									try{
										$copy_doc = Document::create($doc_val);
									} catch (\Exception $e) {
										$idError = $value->id_employee;
										$titleError = 'Error Empoloyee Transition : '.$value->category.'. Employee '.$idError;
										$bodyError = ' cannot create employee document by id_employee ('.$idError.')';
										\Log::error($titleError.$bodyError);

										if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
									    	throw new \Exception($e->getMessage());    
									    } else {
									    	throw new \Exception($titleError);    
									    }
							        }
								}
							}
							$emp_bank = Bank::where('id_employee', $value->id_employee)->get()->makeHidden(['id_bank_employee'])->toArray();
							if($emp_bank){
								foreach($emp_bank as $d=>$bank_val){
									$bank_val['id_employee'] = $emp->id_employee;
									try{
										$copy_bank = Bank::create($bank_val);
									} catch (\Exception $e) {
										$idError = $value->id_employee;
										$titleError = 'Error Empoloyee Transition : '.$value->category.'. Employee '.$idError;
										$bodyError = ' cannot create employee bank by id_employee ('.$idError.')';
										\Log::error($titleError.$bodyError);

										if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
									    	throw new \Exception($e->getMessage());    
									    } else {
									    	throw new \Exception($titleError);    
									    }
							        }
								}
							}
							$emp_ins = Insurance::where('id_employee', $value->id_employee)->get()->makeHidden(['id_insurance_employee'])->toArray();
							if($emp_ins){
								foreach($emp_ins as $d=>$ins_val){
									$ins_val['id_employee'] = $emp->id_employee;
									try{
										$copy_ins = Insurance::create($ins_val);
									} catch (\Exception $e) {
										$idError = $value->id_employee;
										$titleError = 'Error Empoloyee Transition : '.$value->category.'. Employee '.$idError;
										$bodyError = ' cannot create employee insurance by id_employee ('.$idError.')';
										\Log::error($titleError.$bodyError);

										if(session()->has('access_group') && session('access_group') =='Default_Administrator'){
									    	throw new \Exception($e->getMessage());    
									    } else {
									    	throw new \Exception($titleError);    
									    }
							        }
								}
							}
						}						
						
						DB::table('hr_career_transaction')->where('id_career_transaction', $value->id_career_transaction)->update(array(
							'executed' => 1,				
						));

						// ======= utk kebutuhan call API HRIS Mobile =======
						$ktpEmployee = @$emp->identification_number;
						$getEmployeeByKtp = DB::table('hr_employee')
							->where('identification_number', $ktpEmployee)->orderBy('id_employee', 'asc')
							->get();
						if($getEmployeeByKtp->count() > 1){
							$allNik = $getEmployeeByKtp->pluck('nik_employee')->all();
							$nikOld = $allNik[count($allNik)-2];
							$nikNew = $allNik[count($allNik)-1];

							if($nikOld != $nikNew){
								$updateNikMobile[] = ['nik_old' => $nikOld, 'nik_new' => $nikNew, 'type'=>'change'];
							} else {
								$updateNikMobile[] = ['nik' => $nikNew, 'is_active' => true, 'type'=>'active'];
							}
						}
						// ==================================================
					}					
					if($value->category == 'Movement'){	
						$emp_movement = Employee::where('id_employee', $value->id_employee)->get()->toArray();
					//	dd($emp_movement);
						foreach($emp_movement as $k=>$val){							
								if($val['permanent_date'] == null){
									if($type == 'Employment_Status_Changes'){
										Employee::where('id_employee', $val['id_employee'])->update(array(
											'id_employment_status' => $value->id_employment_status,
											'permanent_date' => $value->effective_date,
											'expired_date' => $value->expired_date,
										));
									}
									else{
										Employee::where('id_employee', $val['id_employee'])->update(array(
											'id_employment_status' => $value->id_employment_status,
											'expired_date' => $value->expired_date,
										));
									}
								}
								else{
									Employee::where('id_employee', $val['id_employee'])->update(array(
										'id_employment_status' => $value->id_employment_status,
										'expired_date' => $value->expired_date,
									));
								}	
						}
						if($value->id_position_detail != NULL && $value->id_old_position_detail != $value->id_position_detail && $job_emp->secondary_position == false){
								JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->where('id_employee',$value->id_employee)->update(array(
									'id_employee' => NULL,
								));	
								
							if(!is_null($job_emp->id_employee)){
								JobPositionDetail::where('id_position_detail', $value->id_position_detail)->update(array(
									'id_employee2' => $value->id_employee,
								));
							}
							else if(is_null($job_emp->id_employee)){
								JobPositionDetail::where('id_position_detail',$value->id_position_detail)->update(array(
									'id_employee' => $value->id_employee,
								));
							}	

							$job_emp_old = JobPositionDetail::where('id_position_detail',$value->id_old_position_detail)->first();							
							if(is_null($job_emp_old->id_employee) && !is_null($job_emp_old->id_employee2)){		
								JobPositionDetail::where('id_position_detail',$job_emp_old->id_position_detail)->update(array(
									'id_employee' => $job_emp_old->id_employee2,
									'id_employee2' => NULL,
								));
							}
						}
						else if($job_emp->secondary_position == true){
							JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->where('id_employee',$value->id_employee)->update(array(
									'id_employee' => NULL,
								));	
							JobPositionDetail::where('id_position_detail',$value->id_position_detail)->update(array(
									'id_employee' => $value->id_employee,
									'secondary_position' => false,
								));
						}
						DB::table('hr_career_transaction')->where('id_career_transaction', $value->id_career_transaction)->update(array(
							'executed' => 1,				
						));
					}							
				}
			/*	if($value->effective_date > date('Y-m-d') && $value->code == 'Approved' && $value->executed == 0 && $value->category != 'Termination'){
					if($value->id_position_detail != $value->id_old_position_detail){
						JobPositionDetail::where('id_position_detail', $value->id_old_position_detail)->update(array(
							'id_employee2' => $value->id_employee,
						));
					}					
				}
			*/
			}
		//	dd($x);
			DB::commit();
			if (env('APP_ENV')=='production'){
				self::updateNikMobile($updateNikMobile);
			}
			return response()->json(['status' => 'true', 'message' => 'Success']);
        } catch (\Exception $e) {
		//	dd($e->getMessage());
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function submit_approve($id) {
		$id_approval = CareerTransition::where('id_career_transaction', $id)->first();
		$approve = CareerTransition::submit_approve();
	//	dd($id_approval);
			CareerTransition::where('id_career_transaction', $id)->update(array(
				'id_approval_status' => $approve->id_general_data,
			));	
			
		$data = [
            'id_career_transaction' => $id,
            'id_approval' => $id_approval->id_approval
			];
		$data_status = CareerTransition::getdata_approval_status($data);
		foreach ($data_status as $key => $value) {
			if($value->code == "New" || $value->code == "Cancel"){
					if(count($data_status) == 1 && $value->id_user == session('id_user')){
						$approved = CareerTransition::approved();
						ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Career_Request')->update(array(
							'id_approval_status' => $approved->id_general_data,
							'update_date' => date('Y-m-d H:i:s'),
							'updated_by' => session('id_user'),
						));	
						CareerTransition::where('id_career_transaction', $id)->update(array(
							'id_approval_status' => $approved->id_general_data,			
						));
					}
					else{
						ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Career_Request')->update(array(
							'id_approval_status' => $approve->id_general_data,
						));	
					}				
			}
		}	
        return response()->json(['status' => 'true', 'message' => 'Approval Status Submit Successfully !!']);
    }
	public function cancel($id) {
		$cancel = CareerTransition::cancel();
        CareerTransition::where('id_career_transaction', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
		 ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Career_Request')->update(array(
					'id_approval_status' => $cancel->id_general_data,
				));	
    }
	public function edit($id) {
        if (request()->ajax()) {
            $data = CareerTransition::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	public function get_career_edit(Request $request) {
        $data = [
            'id_career_transaction' => $request->id_career_transaction
        ];
        $result = CareerTransition::get_career_edit($data);
	//	dd($result);
        return response()->json($result);
    }
	
	 public function destroy($id) {
        $data = CareerTransition::findOrFail($id);
		ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Career_Request')->delete();
        $data->delete();
    }
	
	public function get_employee(Request $request) {
		$data = [
            'rehire' => $request->rehire
        ];
        $result = CareerTransition::get_employee($data);
        return response()->json($result);
    }
	
	public function get_position(Request $request) {
        $data = [
            'id_employee' => $request->id_employee
        ];	
        $result = CareerTransition::get_position($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_position_detail(Request $request) {
        $data = [
         //   'id_employee' => $request->id_employee,
            'id_position_detail' => $request->id_position_detail,
        ];	
        $result = CareerTransition::get_position_detail($data);
	//	dd($result);
        return response()->json($result);
    }
		
	public function get_career_category(Request $request) {
        $result = CareerTransition::get_career_category();
        return response()->json($result);
    }
	public function get_employment_status(Request $request) {
        $result = CareerTransition::get_employment_status();
        return response()->json($result);
    }
	
	public function get_career_type(Request $request) {
        $data = [
            'id' => $request->id,
            'var_type' => $request->var_type,
        ];	
	//	dd($data);
        $result = CareerTransition::get_career_type($data);
        return response()->json($result);
    }
	public function get_company_session(Request $request) {
        $data = [
            'id' => session('id_company')
        ];	
        $result = CareerTransition::get_company_session($data);
        return response()->json($result);
    }

    public function get_company() {
        $result = CareerTransition::get_company();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_hierachy(Request $request) {
		$data_emp = [
            'emp' => $request->emp
        ];
		$req = ApprovalTransaction::get_career_req($data_emp);
		$req_location = $req[0]->id_location;
		$data = [
            'code' => $request->code
        ];
        $result = ApprovalTransaction::get_hierachy_custom($data,$req_location);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_approval_status() {		
        $result = CareerTransition::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	public function get_terminate_reason(Request $request) {
		$data = [
            'code' => $request->code
        ];
	//	dd($data);
        $result = CareerTransition::get_terminate_reason($data);
	//	dd($result);
        return response()->json($result);
    }
	public function browse_job(Request $request) {
		if ($request->ajax()) {
			 $data = [
				'company' => $request->company
			];	
			$result = CareerTransition::browse_job($data);
		 }
        return response()->json($result);
    }
	public function checkpos(Request $request){
		if ($request->ajax()) {
			$data = [
				'jobid' => $request->jobid,
				'company' => $request->company,
			];
			$result = CareerTransition::browse_check($data);
		 }
       return response()->json(['result' => $result]);
	 }
	
	public function browse(Request $request) {
        if ($request->ajax()) {
			$data = [
				'career' => $request->career
			];	
			if($data['career'] == "Orientation" || $data['career'] == "Failed_Orientation" || $data['career'] == "Temporary_Assignment" || $data['career'] == "Pass_RPK"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/memo/apimemo';
			}
			else if($data['career'] == "Employment_Status_Changes"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontract?contract=pkwtt';
			}
			else if($data['career'] == "New_Employee"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontract?contract=pkwt';
			}
			else if($data['career'] == "Promotion" || $data['career'] == "Pass_Orientation" || $data['career'] == "Mutation" || $data['career'] == "Demotion"|| $data['career'] == "Rotation" || $data['career'] == "Relocation"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/career/apicareer?career='.$data['career'];
			}
			else{
				$url = 'https://hris.borwita.co.id/nosurat/index.php/career/apinocareer';				
			}
			$response = file_get_contents($url);
			$decode = json_decode($response);
			$result = collect($decode);
            echo $result;
        }
    }

    public function checkid(Request $request) {
		if ($request->ajax()) {
			$data = [
				'id' => $request->careerid,
				'career' => $request->career
			];
		//	dd($data['id']);
			if($data['career'] == "Orientation" || $data['career'] == "Failed_Orientation" || $data['career'] == "Temporary_Assignment" || $data['career'] == "Pass_RPK"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/memo/apimemoid?id='.$data['id'];
			}
			else if($data['career'] == "Employment_Status_Changes"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontractid?contract=pkwtt&id='.$data['id'];
			}
			else if($data['career'] == "New_Employee"){
				$url = 'https://hris.borwita.co.id/nosurat/index.php/contract/apicontractid?contract=pkwt&id='.$data['id'];
			}
			else{
				$url = 'https://hris.borwita.co.id/nosurat/index.php/career/apicareerid?careerid='.$data['id'];
			}
			$response = file_get_contents($url);			
			$result = json_decode($response);
		 }
        return response()->json(['result' => $result]);
    }

    public function updateNikMobile($data=null) {
		if(is_array($data) && count($data) > 0){
            \Log::channel('scheduler')->info('There are '.count($data).' data users to update to Mobile Api');

			foreach ($data as $k => $val) {
				if(@$val['type'] == 'active' || @$val['type'] == 'inactive'){
					$dataApiIsActiveUser = ['nik'=>$val['nik'], 'is_active'=>$val['is_active']];
					$callApiIsActiveUser = Http::withBasicAuth($this->usernameApiIsActiveUser, $this->passwordApiIsActiveUser)
					    ->withHeaders(['Content-Type' => 'application/json'])
					    ->put($this->urlApiIsActiveUser, $dataApiIsActiveUser);

					if(@$val['type'] == 'active'){
                    	\Log::channel('scheduler')->info('Active User: '.json_encode($dataApiIsActiveUser).'. Url: '.$this->urlApiIsActiveUser.'. Response: '.$callApiIsActiveUser->body());
					} else {
                    	\Log::channel('scheduler')->info('Inactive User: '.json_encode($dataApiIsActiveUser).'. Url: '.$this->urlApiIsActiveUser.'. Response: '.$callApiIsActiveUser->body());
					}
				}
				if(@$val['type'] == 'change'){
					$dataApiUpdateNikUser = ['nik_old'=>$val['nik_old'], 'nik_new'=>$val['nik_new']];
					$callApiUpdateNikUser = Http::withBasicAuth($this->usernameApiUpdateNikUser, $this->passwordApiUpdateNikUser)
				    ->withHeaders(['Content-Type' => 'application/json'])
				    ->put($this->urlApiUpdateNikUser, $dataApiUpdateNikUser);

                    \Log::channel('scheduler')->info('Change User: '.json_encode($dataApiUpdateNikUser).'. Url: '.$this->urlApiUpdateNikUser.'. Response: '.$callApiIsActiveUser->body());

                    $dataApiIsActiveUser = ['nik'=>$val['nik_new'], 'is_active'=>true];
					$callApiIsActiveUser = Http::withBasicAuth($this->usernameApiIsActiveUser, $this->passwordApiIsActiveUser)
					    ->withHeaders(['Content-Type' => 'application/json'])
					    ->put($this->urlApiIsActiveUser, $dataApiIsActiveUser);

                	\Log::channel('scheduler')->info('Active User after change nik: '.json_encode($dataApiIsActiveUser).'. Url: '.$this->urlApiIsActiveUser.'. Response: '.$callApiIsActiveUser->body());

				}
			}
		}
		return true;
    }
}
