<?php

namespace App\Http\Controllers\Employee\Employee;

use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\Contract\Contract;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Employee\Employee\Education;
use App\Models\Employee\Employee\Family;
use App\Models\Employee\Employee\Experience;
use App\Models\Employee\Employee\Skill;
use App\Models\Employee\Employee\Cert;
use App\Models\Employee\Employee\Bank;
use App\Models\Employee\Employee\Insurance;
use App\Models\Employee\Employee\EmployeeLeave;
use App\Models\Employee\Employee\Document;
use App\Models\Employee\Employee\Boarding;
use App\Models\Employee\Employee\PtkpHistory;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Models\Setting\ResponsibilityUser\RelationCompanyUser;
use App\Models\Setting\ResponsibilityUser\RelationBranchUser;
use App\Models\Setting\Responsibility\MasterMenu;
use App\Models\Setting\Responsibility\Responsibility;
use App\Models\TimeAttendance\LeaveSetting\MassLeave\MassLeave;
use App\Models\TimeAttendance\Attendance\Attendance;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Mail\TestMail;
use App\Mail\BorwitaMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CareerAdministration\CareerTransition\CareerTransitionController;
use Illuminate\Http\Request;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Facades\Image;
use DatePeriod;
use DateTime;
use DateInterval;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use Carbon\Carbon;

class EmployeeController extends Controller {

	public function __construct()
	{
        $this->CareerTransitionController = new CareerTransitionController;
        $this->WorkDays = new WorkDays;
	}

    public function index(Request $request) {
	//	$codeid = Employee::getcode();
	//	dd($codeid);
	

        if ($request->ajax()) {
        	$nik = $request->nik ?? null;
        	$status = $request->status ?? null;
        	
            $data = Employee::getdata($this->accessBranch($request), $nik, $status);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$onclick = "get_profile(".$data->id_employee.")";
                                $button = '<button type="button" name="edit" id="' . $data->id_employee . '" class="edit btn btn-primary btn-sm" title="Edit" value="_new"><span class="fas fa-edit"></span></button> ';
                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_employee . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
								$button .= '&nbsp;&nbsp;<button type="button" name="profile" id="' . $data->id_employee . '"  onclick="'.$onclick.'" class="btn btn-success btn-xs" title="Profile"><span class="far fa-id-card" style="font-size:18px;margin:3px;"></span></button>';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
							
			
        }
     //   return view('employee.employee.employee.index',compact('codeid'));
        return view('employee.employee.employee.index');
    }

	public function report(Request $request) {
        return view('employee.employee.employee.report');
    }
	public function reportptkp(Request $request) {
        return view('employee.employee.employee.reportptkp');
    }
	public function reportdata(Request $request) {
        if ($request->ajax()) {
            $emp = Employee::getreport($this->accessBranch($request));
			foreach($emp as $key=>$val){
				$data[] = $val;
				unset($val->image_attachment);
			}
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->make(true);
        }
    }
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
	
	protected function validateEmployee(Request $request) {

        $arr_form_validate = [
            'name' => 'required|string',
            'private_mail' => 'required',
            'mobile_phone' => 'required',
            'idcard_address' => 'required',
            'birthdate' => 'required',
            'place_of_birth' => 'required',
            'home_base' => 'required',
            'ptkp_status' => 'required',
        //    'identification_number' => 'required|string|unique:hr_employee',
            'identification_number' => 'required',
        /*    'nik_employee' => 'required|string|unique:hr_employee', Rule::unique('hr_employee')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
		*/
            'join_date' => 'required',
            'id_shift_group' => 'required',
		//	'image_attachment' => 'mimes:jpg,jpeg,png|max:300',
			'fam.*.family_name' => 'required',
			'fam.*.relationship' => 'required',
			'edu.*.major' => 'required',
			'edu.*.education_name' => 'required',
			'ex.*.position_name' => 'required',
			'skill.*.skill_name' => 'required',
			'cert.*.certification_name' => 'required',
		//	'doc.*.document_name' => 'required',
		//	'doc.*.document_number' => 'required',
		//	'doc.*.attachment' => 'required|mimes:pdf,jpg,jpeg,png|max:300',
			'onboarding.*.attachment' => 'max:200',
			'offboarding.*.attachment' => 'max:200',
		
        ];
        $arr_msg_form_validate = [
        //    'image_attachment.mimes' => 'The Photo field must be a file of type: jpg, jpeg, png',
			'name.required' => 'The Employee Name field is required',
            'private_mail.required' => 'The Private Mail field is required',
            'mobile_phone.required' => 'The Mobile Phone field is required',
            'idcard_address.required' => 'The KTP Address field is required',
            'birthdate.required' => 'The Date of Birth field is required',
            'place_of_birth.required' => 'The Place of Birth field is required',
            'home_base.required' => 'The Home Base field is required',
            'ptkp_status.required' => 'The PTKP Status field is required',
        //    'nik_employee.unique' => 'The NIK Employee field has already been taken',
            'identification_number.required' => 'The ID Number field is required',
        //    'identification_number.unique' => 'The ID Number field has already been taken',
            'join_date.required' => 'The Join Date field is required',
            'id_shift_group.required' => 'The Work Hours field is required',
			'fam.*.family_name.required' => 'The Family Name field is required',
            'fam.*.relationship.required' => 'The Relationship field is required',
            'job.*.job_position.required' => 'The Job Position field is required',
            'edu.*.major.required' => 'The Major field is required',
            'edu.*.education_name.required' => 'The University/School field is required',
            'ex.*.position_name.required' => 'The Job Title field is required',
            'skill.*.skill_name.required' => 'The Skill Name field is required',
            'cert.*.certification_name.required' => 'The Certification Name field is required',
		//	'doc.*.document_name.required' => 'The Document Name field is required',
		//	'doc.*.document_number.required' => 'The Document Number field is required',
        //  'doc.*.attachment.required' => 'The Attachment field is required',
            'doc.*.attachment.max' => 'The Attachment field must not be greater than 300 kilobytes',
            'doc.*.attachment.mimes' => 'The Attachment field must be a file of type: pdf, jpg, jpeg, png',
            'onboarding.*.attachment.max' => 'The Attachment field must not be greater than 200 kilobytes',
            'offboarding.*.attachment.max' => 'The Attachment field must not be greater than 200 kilobytes',

        ];
		$i = 1;
		foreach($request->post('doc') as $key=>$val_doc){
				$doc_name = isset($val_doc['document_name']) != null ? $val_doc['document_name'] : $i;
				$validate_emprequest = [];
				$validate_msg_emprequest = [];

				if($val_doc['document_name'] != 'Surat Pernyataan' || ($val_doc['document_name'] == 'Surat Pernyataan' && !is_null(@$val_doc['attachment'])) ){
				   	$validate_emprequest = ['doc.'.$key.'.document_name' => 'required',
										 'doc.'.$key.'.document_number' => 'required',
										 'doc.'.$key.'.attachment' => 'required|mimes:pdf,jpg,jpeg,png|max:300'
					];
					$validate_msg_emprequest = ['doc.'.$key.'.document_name.required' => 'The Document Name '.$doc_name.' field is required',
											  'doc.'.$key.'.document_number.required' => 'The Document Number '.$doc_name.' field is required',
											  'doc.'.$key.'.attachment.required' => 'The Attachment field '.$doc_name.' is required'
											  ];
				}
				$i++;					  
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
		}
		if ($request->post('edu') == null) {
				$validate_emprequest = ['table_education' => 'required|string'];
				$validate_msg_emprequest = ['table_education.required' => 'Education Background cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
		}
		if ($request->post('job') == null) {
				$validate_emprequest = ['table_job' => 'required|string'];
				$validate_msg_emprequest = ['table_job.required' => 'Table Job Position cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
		}
		else{
			foreach($request->post('job') as $val_job){
				if ($val_job['job_position'] == null) {
					$validate_emprequest = ['table_job' => 'required|string'];
					$validate_msg_emprequest = ['table_job.required' => 'Table Job Position cannot empty'];
					$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
					$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
				}
			}
		}
	//	dd($x);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	protected function validateEmployeeUpdate(Request $request) {

        $arr_form_validate = [
            'name' => 'required|string',
			'private_mail' => 'required',
			'mobile_phone' => 'required',
			'idcard_address' => 'required',
            'birthdate' => 'required',
            'place_of_birth' => 'required',
            'home_base' => 'required',
            'identification_number' => 'required',
            'ptkp_status' => 'required',
		//	'nik_employee' => 'required',
            'join_date' => 'required',
            'id_user' => 'required',
            'id_shift_group' => 'required',
		//	'image_attachment' => 'mimes:jpg,jpeg,png|max:300',
			'fam.*.family_name' => 'required',
			'fam.*.relationship' => 'required',
			'edu.*.major' => 'required',
			'edu.*.education_name' => 'required',
			'ex.*.position_name' => 'required',
			'skill.*.skill_name' => 'required',
			'cert.*.certification_name' => 'required',
			'onboarding.*.attachment' => 'max:200',
			'offboarding.*.attachment' => 'max:200',
		//	'job.*.job_position' => 'required',
		//	'attachment' => 'mimes:pdf,doc,docx,jpg,jpeg,png|max:1024',
        ];
        $arr_msg_form_validate = [
         //   'image_attachment.mimes' => 'The Photo field must be a file of type: jpg, jpeg, png',
			'name.required' => 'The Employee Name field is required',
			'private_mail.required' => 'The Private Mail field is required',
			'mobile_phone.required' => 'The Mobile Phone field is required',
			'idcard_address.required' => 'The KTP Address field is required',
            'birthdate.required' => 'The Date of Birth field is required',
            'place_of_birth.required' => 'The Place of Birth field is required',
            'home_base.required' => 'The Home Base field is required',
			'ptkp_status.required' => 'The PTKP Status field is required',
        //    'nik_employee.unique' => 'The NIK Employee field has already been taken',
            'identification_number.required' => 'The ID Number field is required',
        //    'identification_number.unique' => 'The ID Number field has already been taken',
            'join_date.required' => 'The Join Date field is required',
            'id_user.required' => 'The ID User field is required',
            'id_shift_group.required' => 'The Work Hours field is required',
            'fam.*.family_name.required' => 'The Family Name field is required',
            'fam.*.relationship.required' => 'The Relationship field is required',
       //     'job.*.job_position.required' => 'The Job Position field is required',
            'edu.*.major.required' => 'The Major field is required',
            'edu.*.education_name.required' => 'The University/School field is required',
            'ex.*.position_name.required' => 'The Job Title field is required',
            'skill.*.skill_name.required' => 'The Skill Name field is required',
            'cert.*.certification_name.required' => 'The Certification Name field is required',
        //    'doc.*.document_name.required' => 'The Document Name field is required',
        //    'doc.*.document_number.required' => 'The Document Number field is required',
        //    'doc.*.attachment.required' => 'The Attachment field is required',
			'doc.*.attachment.max' => 'The Attachment field must not be greater than 300 kilobytes',
            'doc.*.attachment.mimes' => 'The Attachment field must be a file of type: pdf, jpg, jpeg, png',
			'onboarding.*.attachment.max' => 'The Attachment field must not be greater than 200 kilobytes',
            'offboarding.*.attachment.max' => 'The Attachment field must not be greater than 200 kilobytes',
        ];
		
		$i = 1;
		if($request->doc){
			foreach($request->doc as $key=>$val_doc){
					$doc_attach = Document::where('id_document_employee',$val_doc['id_document_employee'])->first();
					$doc_name = isset($val_doc['document_name']) != null ? $val_doc['document_name'] : $i;
					$validate_emprequest = [];
					$validate_msg_emprequest = [];

					if($val_doc['document_name'] != 'Surat Pernyataan' || ($val_doc['document_name'] == 'Surat Pernyataan' && !is_null(@$val_doc['attachment'])) ){
						if(@$doc_attach->attachment == null){	
							$validate_emprequest = ['doc.'.$key.'.document_name' => 'required',
												 'doc.'.$key.'.document_number' => 'required',
												 'doc.'.$key.'.attachment' => 'required|mimes:pdf,jpg,jpeg,png|max:300'
							];
							
							$validate_msg_emprequest = ['doc.'.$key.'.document_name.required' => 'The Document Name '.$doc_name.' field is required',
													  'doc.'.$key.'.document_number.required' => 'The Document Number '.$doc_name.' field is required',
													  'doc.'.$key.'.attachment.required' => 'The Attachment field '.$doc_name.' is required'
													  ];
						}else{
							$validate_emprequest = ['doc.'.$key.'.document_name' => 'required',
												 'doc.'.$key.'.document_number' => 'required',
												 'doc.'.$key.'.attachment' => 'mimes:pdf,jpg,jpeg,png|max:300'
							];
							
							$validate_msg_emprequest = ['doc.'.$key.'.document_name.required' => 'The Document Name '.$doc_name.' field is required',
													  'doc.'.$key.'.document_number.required' => 'The Document Number '.$doc_name.' field is required'
													  ];
						}	
					}
					$i++;					  
					$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
					$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);		
				
			}
		}
		/*
		if ($request->post('job') == null) {
				$validate_emprequest = ['table_job' => 'required|string'];
				$validate_msg_emprequest = ['table_job.required' => 'Table Job Position cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
		}
		else{
			foreach($request->post('job') as $val_job){
				if ($val_job['job_position'] == null) {
					$validate_emprequest = ['table_job' => 'required|string'];
					$validate_msg_emprequest = ['table_job.required' => 'Table Job Position cannot empty'];
					$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
					$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
				}
			}
		}
		*/
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
	//	dd($request->all());
        $this->validateEmployee($request);	
		try{
        DB::beginTransaction();
		if($request->image_attachment != ""){
		//	$image_file = file_get_contents($request->image_attachment);
		//	$image = base64_encode($image_file);
			$image = $request->file_name;
		}
		else{
			$image = NULL;
		}
		
		$nik = Employee::getcode(session('id_company'));
		$expired_date = $request->expired_date ?? null;

	//	dd($nik);
        $form_data = array(
		   'name'=>strtoupper($request->name),  'identification_number' => $request->identification_number, 
		   'address_home'=>$request->address_home,  'idcard_address' => $request->idcard_address, 
		   'home_base'=>$request->home_base,  'id_country' => $request->id_country, 
		   'gender'=>$request->gender,  'marital' => $request->marital, 
		   'spouse_complete_name'=>$request->spouse_complete_name,  'spouse_birthdate' => $request->spouse_birthdate, 
		   'place_of_birth'=>$request->place_of_birth,  'id_country_of_birth' => $request->id_country_of_birth, 
		   'ptkp_status'=>$request->ptkp_status,  'id_user' => $request->id_user, 
		   'id_finger'=>$request->id_finger,  'status' => $request->status,		   
		   'nik_employee'=>$nik,  'mobile_phone' => $request->mobile_phone,  'npwp_number' => $request->npwp_number, 
		   'work_phone'=>$request->work_phone,  'work_mail' => strtolower($request->work_mail), 'private_mail' => strtolower($request->private_mail), 
		   'join_date'=>$request->join_date, 'expired_date'=>$expired_date, 'permanent_date' => $request->permanent_date, 
		   'resign_date'=>$request->resign_date,  'work_address' => $request->work_address, 
		   'additional_note'=>$request->additional_note,  'emergency_phone' => $request->emergency_phone, 
		   'emergency_contact'=>$request->emergency_contact,  'id_company' => session('id_company'),
		   'birthdate'=>$request->birthdate,  'id_leave' => $request->id_leave,
		   'id_religion'=>$request->id_religion,  'id_employment_status' => $request->id_employment_status,
		   'id_shift_group'=>$request->id_shift_group,  'id_timezone' => $request->id_timezone, 'image_attachment' => $image,   
           'created_by' => session('id_user'), 'sales_code' => $request->sales_code, 'id_vaccination_status' => $request->id_vaccination_status, 
		   'lasted_date_vaccine' => $request->lasted_date_vaccine, 'number_of_children' => $request->number_of_children
        );
        $checkNik = Employee::where('nik_employee', $nik)->first();
        if($checkNik){
			$max_nik = Employee::max_nik(session('id_company'));
			$numbering =  sprintf("%07s", abs($max_nik['number'] + 1));
			$nik = $max_nik['code'].$numbering;
			$form_data['nik_employee'] = $nik;
        }

        $getActiveEmployeebyIdentity = Employee::where('identification_number', $request->identification_number)->where('status', 'A')->where('join_date', $request->join_date)->first();
        if($getActiveEmployeebyIdentity){
        	$thisActiveEmployee = $getActiveEmployeebyIdentity->name.' ('.$getActiveEmployeebyIdentity->nik_employee.')';
        	return response()->json(['status' => 'false_date', 'message' => 'Terdapat Karyawan aktif pada identitas tersebut dengan join date yang sama, mohon cek : '.$thisActiveEmployee, 'data' => $request->identification_number]);
        }

        $checkDoubleJoin = Employee::checkDoubleJoinByIdNumberAndStatus($request->identification_number);
        if($checkDoubleJoin && count($checkDoubleJoin) > 0){
        	$thisDoubleActiveIdentity = $checkDoubleJoin[0]->name.' ('.$checkDoubleJoin[0]->nik_employee.')';
        	return response()->json(['status' => 'false_date', 'message' => 'Terdapat Karyawan aktif pada identitas tersebut, mohon cek : '.$thisDoubleActiveIdentity, 'data' => $request->identification_number]);
        }

        $employee = Employee::create($form_data);	
        $ptkphistory = PtkpHistory::create([
				'id_employee' => $employee->id_employee,
				'ptkp_status' => $request->ptkp_status,
				'date_change_ptkp' => $request->ptkp_date_update,
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
		]);
		
		foreach ($request->job as $key => $value) {
		$get_effective = Employee::get_effective_date($value['id_position_detail']);		
		/*
		if($get_effective){
			if(@$get_effective[0]->code != "Termination"){
				if($request->join_date < @$get_effective[0]->effective_date){
					return response()->json(['status' => 'false_date', 'message' => 'Join Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
				}
			}
			else if($request->join_date <= @$get_effective[0]->effective_date){
				return response()->json(['status' => 'false_date', 'message' => 'Join Date yang dipilih masih digunakan di Career. (Ganti Effective Date yang lebih besar atau Buat Position Baru)']);
			}
		}	
		*/
			$pos_det[] = $value['id_position_detail'];
		}
		
	//Start Contract	
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
	/*	$data_employee = [
            'id_employee' => $employee->id_employee
        ];	
        $contract_position = Contract::get_position($data_employee);
	*/
        $contract_approve = Contract::submit_approve();
		$careerCode = CareerTransition::getkode();
		$form_career = array(
            // 'reference_number' => "NO-CTR-".$nik,
            'reference_number' => $careerCode,
            'id_employee' => $employee->id_employee,
            'id_transition_category' => $join[0],
            'id_transaction_type' => $career_type[0]->id,
            'id_employment_status' => $request->id_employment_status,
            'id_old_employment_status' => $request->id_employment_status,
            'id_position_detail' => $pos_det[0],
       //     'id_position_detail' => $contract_position[0]->id_position_detail,
            'remark' => "New Employee",
            'id_approval_status' => $contract_approve->id_general_data,
            'effective_date' => $request->join_date,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		
		$career = CareerTransition::create($form_career);
	//End Contract	
		
		if ($request->edu != null) {
			foreach ($request->edu as $key => $value) {
				 $form_edu = array(
					'id_employee' =>  $employee->id_employee,
					'major' =>  $value['major'],
					'education_name' =>  $value['education_name'],
					'id_education_level' =>  $value['id_education_level'],
					'start_year' => isset($value['start_year']) ? $value['start_year'] : null,
					'end_year' => isset($value['end_year']) ? $value['end_year'] : null,
					'education_city' =>  $value['education_city'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Education::create($form_edu);
			}
		}
		if ($request->fam != null) {
			foreach ($request->fam as $key => $value) {
				 $form_fam = array(
					'id_employee' =>  $employee->id_employee,
					'family_name' =>  $value['family_name'],
					'gender' =>  $value['gender'],
					'relationship' =>  $value['relationship'],
					'mobile_phone' =>  $value['mobile_phone'],
					'status' =>  'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Family::create($form_fam);
			} 
		}
		if ($request->ex != null) {
			foreach ($request->ex as $key => $value) {
				 $form_ex = array(
					'id_employee' =>  $employee->id_employee,
					'position_name' =>  $value['position_name'],
					'company_name' =>  $value['company_name'],
					'company_city' =>  $value['company_city'],
					'start_year' =>  $value['start_year'],
					'end_year' =>  $value['end_year'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Experience::create($form_ex);
			} 
		}
		
		if ($request->skill != null) {
			foreach ($request->skill as $key => $value) {
				 $form_skill = array(
					'id_employee' =>  $employee->id_employee,
					'skill_name' =>  $value['skill_name'],
					'skill_level' =>  $value['skill_level'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Skill::create($form_skill);
			} 
		}
		
		if ($request->cert != null) {
			foreach ($request->cert as $key => $value) {
				 $form_cert = array(
					'id_employee' =>  $employee->id_employee,
					'certification_name' =>  $value['certification_name'],
					'certified_by' =>  $value['certified_by'],
					'years_issued' =>  $value['years_issued'],
					'validity_period' =>  $value['validity_period'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Cert::create($form_cert);
			} 
		}

		if ($request->bank != null) {
			foreach ($request->bank as $key => $value) {
				 $form_bank = array(
					'id_employee' =>  $employee->id_employee,
					'id_bank' =>  $value['id_bank'],
					'bank_name' =>  isset($value['bank_name']) != null ? $value['bank_name'] : null,
					'bank_account' =>  $value['bank_account'],
					'account_name' =>  $value['account_name'],
					'bank_currency' =>  $value['bank_currency'],
					'default_bank' =>   isset($value['default_bank']) == "on" ? 1 : 0,
					'status' =>  'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Bank::create($form_bank);
			}
		}		
		if ($request->ins != null) {
			foreach ($request->ins as $key => $value) {
				 $form_ins = array(
					'id_employee' =>  $employee->id_employee,
					'id_insurance' =>  $value['id_insurance'],
					'emp_insurance_number' =>  $value['emp_insurance_number'],
					'effective_date' =>  $value['effective_date'],
					'expired_date' =>  $value['expired_date'],
					'beneficiary_name' =>  $value['beneficiary_name'],
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Insurance::create($form_ins);
			}
		}
		
	//	$empleave = MassLeave::generatemassleave($employee->id_employee,date('Y-m-d'));
		
		if ($request->doc != null) {
			foreach ($request->doc as $key => $value) {
				if(isset($value['attachment'])){
					$imagename = $employee->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/data/'.$employee->nik_employee,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/data/'.$employee->nik_employee,$value['attachment'],$imagename);
										}
				else{
					$imagename = NULL;
					}

				if($value['document_name'] == 'Surat Pernyataan' && $imagename==null){
					continue;
				}
				 $form_doc = array(
					'id_employee' =>  $employee->id_employee,
					'document_name' =>  $value['document_name'],
					'document_number' =>  $value['document_number'],
					'effective_date' =>  $value['effective_date'],
					'expired_date' =>  $value['expired_date'],
					'attachment' =>  $imagename,
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Document::create($form_doc);
			} 
		}
		if ($request->onboarding != null) {
			foreach ($request->onboarding as $key => $value) {
				if(isset($value['attachment'])){
					$imagename = $employee->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/onboarding/'.$employee->nik_employee,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/onboarding/'.$employee->nik_employee,$value['attachment'],$imagename);
										}
				else{
					$imagename = NULL;
					}
				 $form_onboarding = array(
					'id_employee' =>  $employee->id_employee,
					'id_checklist' =>  $value['id_checklist'],
					'effective_date' =>  $value['effective_date'],
					'remark' =>  $value['remark'],
					'attachment' =>  $imagename,
					'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
					'status' =>  'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Boarding::create($form_onboarding);
			} 
		}
		if ($request->offboarding != null) {
			foreach ($request->offboarding as $key => $value) {
				if(isset($value['attachment'])){
					$imagename = $employee->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/offboarding/'.$employee->nik_employee,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/offboarding/'.$employee->nik_employee,$value['attachment'],$imagename);
										}
				else{
					$imagename = NULL;
					}
				 $form_offboarding = array(
					'id_employee' =>  $employee->id_employee,
					'id_checklist' =>  $value['id_checklist'],
					'effective_date' =>  $value['effective_date'],
					'remark' =>  $value['remark'],
					'attachment' =>  $imagename,
					'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
					'status' =>  'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				Boarding::create($form_offboarding);
			} 
		}
				
		/* Default User */	 	
		$form_user = array(
				'user_name' =>  $employee->nik_employee,
				'password' => Hash::make($employee->nik_employee),
				'email' => strtolower($request->private_mail),
				'default_company' =>  session('id_company'),
				'description_name' => 'Default User '.$employee->name,
				'access_group' => 'Default_User',
				'created_by' => session('id_user'),
			);
		try{
			$user = MasterUser::create($form_user);
		} catch (\Exception $e) {
            throw new \Exception('NIK or Email Conflict');           
        }
		RelationCompanyUser::create(array(
			'id_user' => $user->id_user,
			'id_company' => session('id_company'),
			'created_by' => session('id_user'),
		));
			
		$data_user = [
				'code_default' => 'Default_User',
				'id_company' => session('id_company'),
			];
			
			$mu = MasterUser::get_default_access($data_user);
			foreach($mu['menu'] as $key => $value){
				  $mm = MasterMenu::where('id_menu', $value['id_menu'])->first();
					$r = Responsibility::where('id_responsibility', $mm->id_responsibility)->first();
					$form_respon = array(
						'id_user' => $user->id_user,
						'id_menu' => $value['id_menu'],
						'id_responsibility' => $mm->id_responsibility,
						'id_responsibility_menu' => $r->id_responsibility_menu,
						'sequence' => $key+1,
						'description_name' => $value['menu_name'],
						'start_date' => date("Y-m-d"),
						'end_date' => null,
						'can_create' => 1,
						'can_update' => 1,
						'can_delete' => 1,
						'can_print' => 1,
						'id_company' => $value['id_company'],
						'created_by' => session('id_user'),		
					);
				$mur = MasterUserResponsibility::create($form_respon);
		}
		$form_user_session = array(
				'id_user' =>  $user->id_user,				
			);
		Employee::where('id_employee', $employee->id_employee)->update($form_user_session);
		
		$data_leave = [
            'id_employee' => $employee->id_employee
        ];	
        Employee::generateleave($data_leave);

        //tempatkan employee pada posisi melalui career
		$this->CareerTransitionController->transition();

		DB::commit();
		$return['nik'] = $employee->nik_employee;
		$return['id_company'] = session('id_company');
        return response()->json(['status' => 'true', 'message' => 'Employee Data Saved Successfully !!', 'data' => $return]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			$return['nik'] = @$employee->nik_employee;
			$return['id_company'] = session('id_company');
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Employee Data !! [' . $e->getMessage() . ']', 'data' => $return]);           
        }
    }
	
	public function update(Request $request) {
	//	dd($request->all());
        $this->validateEmployeeUpdate($request);
		try{
        DB::beginTransaction();
		$expired_date = $request->expired_date ?? null;
		$listEmp = Employee::where('id_employee',$request->id_employee)->first();
		$form_data = array(
				   'name'=>strtoupper($request->name),  'identification_number' => $request->identification_number, 
				   'address_home'=>$request->address_home,  'idcard_address' => $request->idcard_address, 
				   'home_base'=>$request->home_base,  'id_country' => $request->id_country, 
				   'gender'=>$request->gender,  'marital' => $request->marital, 
				   'spouse_complete_name'=>$request->spouse_complete_name,  'spouse_birthdate' => $request->spouse_birthdate, 
				   'place_of_birth'=>$request->place_of_birth,  'id_country_of_birth' => $request->id_country_of_birth, 
				   'ptkp_status'=>$request->ptkp_status,  'id_user' => $request->id_user, 
				   'id_finger'=>$request->id_finger,  'status' => $request->status,		   
				//   'nik_employee'=>$request->nik_employee,
				   'mobile_phone' => $request->mobile_phone, 'npwp_number' => $request->npwp_number,
				   'work_phone'=>$request->work_phone,  'work_mail' => strtolower($request->work_mail), 'private_mail' => strtolower($request->private_mail), 
				   'join_date'=>$request->join_date, 'expired_date'=>$expired_date, 'permanent_date' => $request->permanent_date, 
				   'resign_date'=>$request->resign_date,  'work_address' => $request->work_address, 
				   'additional_note'=>$request->additional_note,  'emergency_phone' => $request->emergency_phone, 
				   'emergency_contact'=>$request->emergency_contact,  'id_company' => session('id_company'),
				   'birthdate'=>$request->birthdate,  'id_leave' => $request->id_leave,
				   'id_religion'=>$request->id_religion,  'id_employment_status' => $request->id_employment_status,
				   'id_shift_group'=>$request->id_shift_group,  'id_timezone' => $request->id_timezone,   
				   'updated_by' => session('id_user'),'sales_code' => $request->sales_code, 'id_vaccination_status' => $request->id_vaccination_status, 
				   'lasted_date_vaccine' => $request->lasted_date_vaccine, 'number_of_children' => $request->number_of_children
				);
		if($request->image_attachment != ""){
			//	$image_file = file_get_contents($request->image_attachment);
			//	$image = base64_encode($image_file);		
				$image = $request->file_name;		
				$form_data['image_attachment'] = $image;				
		}
		
        $employee = Employee::findOrFail($request->id_employee)->update($form_data);
		
		if($request->ptkp_date_update != null){
			 $ptkphistory = PtkpHistory::create([
					'id_employee' => $request->id_employee,
					'ptkp_status' => $request->ptkp_status,
					'date_change_ptkp' => $request->ptkp_date_update,
					'status' => 'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
					'updated_by' => session('id_user'),
			]);
		}
		/*
		$collect_employee = collect($request->job)->groupBy('id_position_detail')->toArray();
        $list_employee = array_filter(array_keys($collect_employee));
		
			 if (implode(",", $list_employee) != "") {	
				$up = DB::update("UPDATE master_position_detail
							SET id_employee = null
								WHERE id_employee = ? AND id_position_detail NOT IN (" . implode(",", $list_employee) . ")", [$request->id_employee]);
			 }
			 	
			foreach ($request->job as $key => $value) {
				 $form_detail = array(
					'id_employee' =>  $request->id_employee,
				);
			}
		*/
			$collect_edu = collect($request->edu)->groupBy('id_education_employee')->toArray();
			$list_edu = array_filter(array_keys($collect_edu));	
		if ($request->edu != null) {	
			if (implode(",", $list_edu) != "") {
				DB::delete("DELETE FROM  hr_education_employee hee
							WHERE hee.id_employee = ? AND hee.id_education_employee NOT IN (" . implode(",", $list_edu) . ")", [$request->id_employee]);
			}
			else{
				DB::delete("DELETE FROM  hr_education_employee hee
							WHERE hee.id_employee = ?", [$request->id_employee]);
			}
			foreach ($request->edu as $key => $value) {
				if ($value['id_education_employee'] == "") {
					Education::create(array(
						'id_employee' => $request->id_employee,
						'major' =>  $value['major'],
						'education_name' =>  $value['education_name'],
						'id_education_level' =>  $value['id_education_level'],
						'start_year' => isset($value['start_year']) ? $value['start_year'] : null,
						'end_year' => isset($value['end_year']) ? $value['end_year'] : null,
						'education_city' =>  $value['education_city'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Education::where('id_education_employee', $value['id_education_employee'])->update(array(
						'major' =>  $value['major'],
						'education_name' =>  $value['education_name'],
						'id_education_level' =>  $value['id_education_level'],
						'start_year' => isset($value['start_year']) ? $value['start_year'] : null,
						'end_year' => isset($value['end_year']) ? $value['end_year'] : null,
						'education_city' =>  $value['education_city'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		else{
				DB::delete("DELETE FROM  hr_education_employee hee
							WHERE hee.id_employee = ?", [$request->id_employee]);
			}
		
			$collect_fam = collect($request->fam)->groupBy('id_family_employee')->toArray();
			$list_fam = array_filter(array_keys($collect_fam));
		if ($request->fam != null) {
			if (implode(",", $list_fam) != "") {
				DB::delete("DELETE FROM  hr_family_employee hfe
							WHERE hfe.id_employee = ? AND hfe.id_family_employee NOT IN (" . implode(",", $list_fam) . ")", [$request->id_employee]);
			}
			else{
				DB::delete("DELETE FROM  hr_family_employee hfe
							WHERE hfe.id_employee = ?", [$request->id_employee]);
			}
			foreach ($request->fam as $key => $value) {
				if ($value['id_family_employee'] == "") {
					Family::create(array(
						'id_employee' => $request->id_employee,
						'family_name' =>  $value['family_name'],
						'gender' =>  isset($value['gender']) != null ? $value['gender'] : null,
						'relationship' =>  $value['relationship'],
						'mobile_phone' =>  $value['mobile_phone'],
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Family::where('id_family_employee', $value['id_family_employee'])->update(array(
						'family_name' =>  $value['family_name'],
						'gender' => isset($value['gender']) != null ? $value['gender'] : null,
						'relationship' =>  $value['relationship'],
						'mobile_phone' =>  $value['mobile_phone'],
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		else{
				DB::delete("DELETE FROM  hr_family_employee hfe
							WHERE hfe.id_employee = ?", [$request->id_employee]);
			}
		
			$collect_ex = collect($request->ex)->groupBy('id_experience_employee')->toArray();
			$list_ex = array_filter(array_keys($collect_ex));
		if ($request->ex != null) {
			if (implode(",", $list_ex) != "") {
				DB::delete("DELETE FROM  hr_experience_employee hee
							WHERE hee.id_employee = ? AND hee.id_experience_employee NOT IN (" . implode(",", $list_ex) . ")", [$request->id_employee]);
			}
			else{
				DB::delete("DELETE FROM  hr_experience_employee hee
							WHERE hee.id_employee = ?", [$request->id_employee]);
			}
			foreach ($request->ex as $key => $value) {
				if ($value['id_experience_employee'] == "") {
					Experience::create(array(
						'id_employee' => $request->id_employee,
						'position_name' =>  $value['position_name'],
						'company_name' =>  $value['company_name'],
						'company_city' =>  $value['company_city'],
						'start_year' =>  $value['start_year'],
						'end_year' =>  $value['end_year'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Experience::where('id_experience_employee', $value['id_experience_employee'])->update(array(
						'position_name' =>  $value['position_name'],
						'company_name' =>  $value['company_name'],
						'company_city' =>  $value['company_city'],
						'start_year' =>  $value['start_year'],
						'end_year' =>  $value['end_year'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		else{
				DB::delete("DELETE FROM  hr_experience_employee hee
							WHERE hee.id_employee = ?", [$request->id_employee]);
			}
		
		
			$collect_skill = collect($request->skill)->groupBy('id_skill_employee')->toArray();
			$list_skill = array_filter(array_keys($collect_skill));
		if ($request->skill != null) {
			if (implode(",", $list_skill) != "") {
				DB::delete("DELETE FROM  hr_skill_employee hse
							WHERE hse.id_employee = ? AND hse.id_skill_employee NOT IN (" . implode(",", $list_skill) . ")", [$request->id_employee]);
			}
			else{
				DB::delete("DELETE FROM  hr_skill_employee hse
							WHERE hse.id_employee = ?", [$request->id_employee]);
			}
			foreach ($request->skill as $key => $value) {
				if ($value['id_skill_employee'] == "") {
					Skill::create(array(
						'id_employee' => $request->id_employee,
						'skill_name' =>  $value['skill_name'],
						'skill_level' =>  $value['skill_level'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Skill::where('id_skill_employee', $value['id_skill_employee'])->update(array(
						'skill_name' =>  $value['skill_name'],
						'skill_level' =>  $value['skill_level'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		else{
				DB::delete("DELETE FROM  hr_skill_employee hse
							WHERE hse.id_employee = ?", [$request->id_employee]);
			}
		
		
			$collect_cert = collect($request->cert)->groupBy('id_certification_employee')->toArray();
			$list_cert = array_filter(array_keys($collect_cert));
		if ($request->cert != null) {
			if (implode(",", $list_cert) != "") {
				DB::delete("DELETE FROM  hr_certification_employee hcert
							WHERE hcert.id_employee = ? AND hcert.id_certification_employee NOT IN (" . implode(",", $list_cert) . ")", [$request->id_employee]);
			}
			else{
				DB::delete("DELETE FROM  hr_certification_employee hcert
							WHERE hcert.id_employee = ?", [$request->id_employee]);
			}
			foreach ($request->cert as $key => $value) {
				if ($value['id_certification_employee'] == "") {
					Cert::create(array(
						'id_employee' => $request->id_employee,
						'certification_name' =>  $value['certification_name'],
						'certified_by' =>  $value['certified_by'],
						'years_issued' =>  $value['years_issued'],
						'validity_period' =>  $value['validity_period'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Cert::where('id_certification_employee', $value['id_certification_employee'])->update(array(
						'certification_name' =>  $value['certification_name'],
						'certified_by' =>  $value['certified_by'],
						'years_issued' =>  $value['years_issued'],
						'validity_period' =>  $value['validity_period'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		else{
				DB::delete("DELETE FROM  hr_certification_employee hcert
							WHERE hcert.id_employee = ?", [$request->id_employee]);
			}
		
			$collect_bank = collect($request->bank)->groupBy('id_bank_employee')->toArray();
			$list_bank = array_filter(array_keys($collect_bank));
		if ($request->bank != null) {
			if (implode(",", $list_bank) != "") {
				DB::delete("DELETE FROM  hr_bank_employee hbe
							WHERE hbe.id_employee = ? AND hbe.id_bank_employee NOT IN (" . implode(",", $list_bank) . ")", [$request->id_employee]);
			}
			foreach ($request->bank as $key => $value) {
				if ($value['id_bank_employee'] == "") {
					Bank::create(array(
						'id_employee' => $request->id_employee,
						'id_bank' =>  $value['id_bank'],
						'bank_name' =>  isset($value['bank_name']) != null ? $value['bank_name'] : null,
						'bank_account' =>  $value['bank_account'],
						'account_name' =>  $value['account_name'],
						'bank_currency' =>  $value['bank_currency'],
						'default_bank' =>   isset($value['default_bank']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Bank::where('id_bank_employee', $value['id_bank_employee'])->update(array(
						'id_bank' =>  $value['id_bank'],
						'bank_name' =>  isset($value['bank_name']) != null ? $value['bank_name'] : null,
						'bank_account' =>  $value['bank_account'],
						'account_name' =>  $value['account_name'],
						'bank_currency' =>  $value['bank_currency'],
						'default_bank' =>   isset($value['default_bank']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		
			$collect_ins = collect($request->ins)->groupBy('id_insurance_employee')->toArray();
			$list_ins = array_filter(array_keys($collect_ins));
		if ($request->ins != null) {
			if (implode(",", $list_ins) != "") {
				DB::delete("DELETE FROM  hr_insurance_employee hie
							WHERE hie.id_employee = ? AND hie.id_insurance_employee NOT IN (" . implode(",", $list_ins) . ")", [$request->id_employee]);
			}
			foreach ($request->ins as $key => $value) {
				if ($value['id_insurance_employee'] == "") {
					Insurance::create(array(
						'id_employee' => $request->id_employee,
						'id_insurance' =>  $value['id_insurance'],
						'emp_insurance_number' =>  $value['emp_insurance_number'],
						'effective_date' =>  $value['effective_date'],
						'expired_date' =>  $value['expired_date'],
						'beneficiary_name' =>  $value['beneficiary_name'],					
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					Insurance::where('id_insurance_employee', $value['id_insurance_employee'])->update(array(
						'id_insurance' =>  $value['id_insurance'],
						'emp_insurance_number' =>  $value['emp_insurance_number'],
						'effective_date' =>  $value['effective_date'],
						'expired_date' =>  $value['expired_date'],
						'beneficiary_name' =>  $value['beneficiary_name'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
				}
			}
		}
		
			$collect_doc = collect($request->doc)->groupBy('id_document_employee')->toArray();
			$list_doc = array_filter(array_keys($collect_doc));
		if ($request->doc != null) {
			if (implode(",", $list_doc) != "") {
				DB::delete("DELETE FROM  hr_document_employee hde
							WHERE hde.id_employee = ? AND hde.id_document_employee NOT IN (" . implode(",", $list_doc) . ")", [$request->id_employee]);
			}
			
			foreach ($request->doc as $key => $value) {
				
				if ($value['id_document_employee'] == "") {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/data/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/data/'.$request->nik_employee,$value['attachment'],$imagename);
					}
					else{
						$imagename = NULL;
					}
					if($value['document_name'] == 'Surat Pernyataan' && $imagename==null){
						continue;
					}
					Document::create(array(
						'id_employee' => $request->id_employee,
						'document_name' =>  $value['document_name'],
						'document_number' =>  $value['document_number'],
						'effective_date' =>  $value['effective_date'],
						'expired_date' =>  $value['expired_date'],
						'attachment' =>  $imagename,
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/data/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/data/'.$request->nik_employee,$value['attachment'],$imagename);
						
					Document::where('id_document_employee', $value['id_document_employee'])->update(array(
						'document_name' =>  $value['document_name'],
						'document_number' =>  $value['document_number'],
						'effective_date' =>  $value['effective_date'],
						'expired_date' =>  $value['expired_date'],
						'attachment' =>  $imagename,
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}
					else{
					Document::where('id_document_employee', $value['id_document_employee'])->update(array(
						'document_name' =>  $value['document_name'],
						'document_number' =>  $value['document_number'],
						'effective_date' =>  $value['effective_date'],
						'expired_date' =>  $value['expired_date'],
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}					
				}
			}
		}
		
			$collect_on = collect($request->onboarding)->groupBy('id_checklist_employee')->toArray();
			$list_on = array_filter(array_keys($collect_on));
		if ($request->onboarding != null) {
			if (implode(",", $list_on) != "") {
				DB::delete("DELETE FROM  hr_checklist_employee hce
							WHERE hce.id_employee = ? AND hce.id_checklist_employee NOT IN (" . implode(",", $list_on) . ")", [$request->id_employee]);
			}
			
			foreach ($request->onboarding as $key => $value) {
				
				if ($value['id_checklist_employee'] == "") {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/onboarding/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/onboarding/'.$request->nik_employee,$value['attachment'],$imagename);
					}
					else{
						$imagename = NULL;
					}
				
					Boarding::create(array(
						'id_employee' => $request->id_employee,
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'attachment' =>  $imagename,
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/onboarding/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/onboarding/'.$request->nik_employee,$value['attachment'],$imagename);
						
					Boarding::where('id_checklist_employee', $value['id_checklist_employee'])->update(array(
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'attachment' =>  $imagename,
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}
					else{
					Boarding::where('id_checklist_employee', $value['id_checklist_employee'])->update(array(
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}					
				}
			}
		}
		
		if ($request->offboarding != null) {
			if (implode(",", $list_on) != "") {
				DB::delete("DELETE FROM  hr_checklist_employee hce
							WHERE hce.id_employee = ? AND hce.id_checklist_employee NOT IN (" . implode(",", $list_on) . ")", [$request->id_employee]);
			}
			
			foreach ($request->offboarding as $key => $value) {
				
				if ($value['id_checklist_employee'] == "") {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/offboarding/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/offboarding/'.$request->nik_employee,$value['attachment'],$imagename);
					}
					else{
						$imagename = NULL;
					}
				
					Boarding::create(array(
						'id_employee' => $request->id_employee,
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'attachment' =>  $imagename,
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					));
				} else {
					if(isset($value['attachment'])){
						$imagename = $request->nik_employee."-".$value['document_name'].".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/offboarding/'.$request->nik_employee,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/offboarding/'.$request->nik_employee,$value['attachment'],$imagename);
						
					Boarding::where('id_checklist_employee', $value['id_checklist_employee'])->update(array(
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'attachment' =>  $imagename,
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}
					else{
					Boarding::where('id_checklist_employee', $value['id_checklist_employee'])->update(array(
						'id_checklist' =>  $value['id_checklist'],
						'effective_date' =>  $value['effective_date'],
						'remark' =>  $value['remark'],
						'completed' =>  isset($value['completed']) == "on" ? 1 : 0,
						'status' =>  'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					));
					}					
				}
			}
		}
		//Update Career Effective Date
		$career_join = CareerTransition::get_cancel_join($request->id_employee);
		$join=[];
			foreach($career_join as $key=>$value){
				if( $value->category == "Join"){
					$join[] = $value->id_transition_category;
				}
			}	
		if(count($join) == 1){
			$update_career = CareerTransition::where('id_employee',$request->id_employee)->where('id_transition_category',$join[0])->update(array(
				'effective_date' => $request->join_date,
			));
		}
		// UPDATE WORKDAYS DAN LEAVE
		$updateWorkdays = DB::table(DB::raw("updateworkdays('".$request->id_employee."','".session('id_company')."','".date('Y-m-d')."','".session('id_user')."')"))->get();
		
		if($listEmp['id_leave'] != $request->id_leave){
			$data_leave = ['id_employee' => $request->id_employee];	
			Employee::generateleave($data_leave);
		}

		MasterUser::where('id_user', $request->id_user)
					->where('user_name', $request->nik_employee)
					->where('status', 'A')->update(['email'=>strtolower($request->private_mail)]);
					
		DB::commit();
        return response()->json(['status' => 'true', 'data' => [], 'message' => 'Employee Data Updated Successfully !!']);		
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
			return response()->json(['status' => 'false', 'data' => [], 'message' => 'Cannot Update Employee Data !! [' . $e->getMessage() . ']']);           
        }
    }
	
	
	public function edit($id) {

        if (request()->ajax()) {
            $data = Employee::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }
	public function get_employee_edit(Request $request) {
		$filePath = '';
        $data = [
            'id_employee' => $request->id_employee
        ];
		$filePath = url('project/storage/app/public/upload/photo');
        $result = Employee::get_employee_edit($data);
		$result['filePath'] = $filePath;
        return response()->json($result);
    }
	
	public function destroy($id) {
		try{
        	DB::beginTransaction();		
			$career_join = CareerTransition::get_cancel_join($id);	
			$join=[];
			foreach($career_join as $key=>$value){
				if( $value->category == "Join"){
					$join[] = $value->id_transition_category;
				}
			}
	        $data = Employee::findOrFail($id);
			$job_update	 = JobPositionDetail::where('id_employee', $id)->first();
			$job_update2 = JobPositionDetail::where('id_employee2', $id)->first();
			if($job_update){
				$form_detail = array(
					'id_employee' =>  null,
				);
				JobPositionDetail::where('id_employee', $id)->update($form_detail);
			}
			else if($job_update2){
				$form_detail2 = array(
					'id_employee2' =>  null,
				);
				JobPositionDetail::where('id_employee2', $id)->update($form_detail2);
			}

			Education::where('id_employee', $id)->delete();
			PtkpHistory::where('id_employee', $id)->delete();
			Family::where('id_employee', $id)->delete();
			Experience::where('id_employee', $id)->delete();
			Skill::where('id_employee', $id)->delete();
			Cert::where('id_employee', $id)->delete();
			Bank::where('id_employee', $id)->delete();
			Insurance::where('id_employee', $id)->delete();
			EmployeeLeave::where('id_employee', $id)->delete();
			Document::where('id_employee', $id)->delete();
			$con = Contract::where('id_employee', $id)->get();
			$req = RequestDetail::where('id_employee', $id)->get();
			// MasterUser::where('id_user', $data->id_user)->delete();				
			if(count($con) > 0){
				throw new \Exception('Tidak bisa dihapus, Karyawan sudah memiliki Contract');           
			}
			if(count($req) > 0){
				throw new \Exception('Tidak bisa dihapus, Karyawan sudah memiliki Request');           
			}
			if(count($career_join) > 1){
				throw new \Exception('Tidak bisa dihapus, karyawan memiliki transaksi Career');           
			}
			else if(count($career_join) == 1){
				$del_career = CareerTransition::where('id_employee',$id)->where('id_transition_category',$join[0])->delete();
			}
			
	        if(Attendance::checkAttendanceByEmployee($id) > 0){
            	throw new \Exception('Tidak bisa dihapus, karyawan memiliki transaksi Attendance');           
	        }
	        Attendance::where('id_employee', $id)->delete();
			
			if($data->id_user){
				$count = Employee::where('id_user', $data->id_user)->get()->count();
				if($count == 1){
					Employee::where('id_employee', $data->id_employee)->update(['id_user'=>null]);
					$mu = MasterUser::findOrFail($data->id_user);
					$mur = MasterUserResponsibility::where('id_user', $data->id_user)->get();
					
					foreach($mur as $value){
						RelationBranchUser::where('id_user_responsibility', $value['id_user_responsibility'])->delete();
					}
					MasterUserResponsibility::where('id_user', $data->id_user)->delete();
					RelationCompanyUser::where('id_user', $data->id_user)->delete();				
					$mu->delete();	
				}
			}
			$data->delete();
			DB::commit();
        	return response()->json(['status' => 'true', 'data' => [], 'message' => 'Employee Data Deleted Successfully !!']);		
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => 'false', 'data' => [], 'message' => $e->getMessage()]);           
        }
    }
	
	public function browse_job() {
        $result = Employee::browse_job();
        return response()->json($result);
    }
	 public function checkid($id)
	 {
	   $data = Employee::browse_check($id);
	//   dd($data);
       return response()->json(['result' => $data]);
	 }
	
/*	public function getcode() {
        $result = Employee::getcode();
        return response()->json($result);
    }
*/	
	public function get_company() {
        $result = Employee::get_company();
        return response()->json($result);
    }
	
	public function get_empstatus() {
        $result = Employee::get_empstatus();
        return response()->json($result);
    }
	public function get_shift() {
        $result = Employee::get_shift();
        return response()->json($result);
    }
	public function get_leave() {
        $result = Employee::get_leave();
        return response()->json($result);
    }
	public function get_user() {
        $result = Employee::get_user();
        return response()->json($result);
    }
	public function get_timezone() {
        $result = Employee::get_timezone();
        return response()->json($result);
    }
	public function get_vaccine() {
        $result = Employee::get_vaccine();
        return response()->json($result);
    }
	public function get_country() {
        $result = Employee::get_country();
        return response()->json($result);
    }
	public function get_religion() {
        $result = Employee::get_religion();
        return response()->json($result);
    }
	public function get_education_level() {
        $result = Employee::get_education_level();
        return response()->json($result);
    }
	public function get_bank() {
        $result = Employee::get_bank();
        return response()->json($result);
    }
	public function get_currency() {
        $result = Employee::get_currency();
        return response()->json($result);
    }	
	public function get_insurance() {
        $result = Employee::get_insurance();
        return response()->json($result);
    }
	public function get_checklist_onboarding() {
        $result = Employee::get_checklist_onboarding();
        return response()->json($result);
    }
	public function get_checklist_offboarding() {
        $result = Employee::get_checklist_offboarding();
        return response()->json($result);
    }
	public function get_emp_contract(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_emp_contract($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	public function get_emp_leave(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_emp_leave($data);
        return DataTables::of($result)
								->addIndexColumn()
								->addColumn('action', function($data) {
									if($data->restrict_by != 'System'){
										$button = '<button class="edit_leave_custom btn btn-xs btn-danger"><span class="far fa-trash-alt"></span></button>';
									} else {
										$button = '';
									}
									return $button;
	                            })
								->make(true);
    }
	public function get_emp_career(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_emp_career($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	public function get_emp_awdcp(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_emp_awdcp($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	public function get_emp_history(Request $request) {
		$data = [
            'no_ktp' => $request->no_ktp
        ];	
        $result = Employee::get_emp_history($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	public function generateleave(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::generateleave($data);
        return response()->json($result);
    }
	
	public function getCandidate(Request $request)
	{
		$data = [
            'no_ktp' => $request->no_ktp
        ];	
		$result = CandidateData::getCandidate($data);
		if(count($result) > 0){
		//	dd($result[0]->id_candidate);
			$param = array(
				'id_candidate' => $result[0]->id_candidate,
			);
			$result[0]->edu = CandidateData::get_edu($param);
			$result[0]->family = CandidateData::get_family($param);
			$result[0]->ex = CandidateData::get_ex($param);
			$result[0]->skill = CandidateData::get_skill($param);
			$result[0]->cert = CandidateData::get_cert($param);
			return response()->json($result[0]);
		}
		else{
			$result = [];
			return response()->json($result);
		}
	//	dd(count($result));
	//	$url = 'https://career.borwita.co.id/index.php/site/ApiCandidat?no_ktp='.$data['no_ktp'];
	//	$response = file_get_contents($url);
	//	$result = json_decode($response);
	//	dd($result);
		 
	}
	public function ktpcheck(Request $request) {
		$data = [
            'no_ktp' => $request->no_ktp
        ];	
        $result = Employee::ktpcheck($data);
        return response()->json($result);
    }
	public function upload(Request $request) {
		 $request->validate([              
                'image_attachment' => 'mimes:jpg,jpeg,png',
                    ], [],
                    [                      
                   'image_attachment' => 'Photo',
            ]);
		if (Storage::exists('public/upload/photo/'. $request->file_name)) {
			Storage::delete('public/upload/photo/'. $request->file_name);
		}
		$ex = explode("/",$request->image_attachment->getClientMimeType());
		if($ex[0] == 'image'){
			$rnd = rand(1000,9999);
			$nama_gambar = $rnd."-".time()."-".strtolower($request->image_attachment->getClientOriginalName());
			$ext = $request->image_attachment->getClientOriginalExtension();

			$filePath = 'public/upload/photo/';
            $dir      = Storage::makeDirectory($filePath, 0777, true, true);

            Image::make($request->file('image_attachment'))->resize(200, null, function ($constraint) {
				$constraint->aspectRatio();
			})->save(storage_path('app/public/upload/photo/'. $nama_gambar));
			$path = url('project/storage/app/public/upload/photo/'. $nama_gambar);
		}
		
		return response()->json(['message' => 'Upload Success','image_name'=>$nama_gambar,'path'=>$path]);
    }
	
	public function get_ptkp(Request $request) {
        if ($request->ajax()) {
			$data = [
				'id_employee' => $request->id_employee
			];	
            $emp = Employee::get_ptkp($data);			
            return DataTables::of($emp)
                            ->addIndexColumn()
                            ->make(true);
        }
    }
	public function get_ptkp_edit(Request $request) {
		$result = [];
		$data = [
			'id_employee' => $request->id_employee
		];
		if($request->id_employee){
			$result = Employee::get_ptkp_edit($data['id_employee']);
		}     
	//	dd($result);
        return response()->json($result);
    }
	protected function update_ptkp(Request $request) {
		$request->validate([              
			'ptkp_status_list' => 'required',
			'ptkp_date' => 'required',
				], [],
				[                      
			   'ptkp_status_list' => 'PTKP Status',
			   'ptkp_date' => 'Update Date',
		]);
		$data =[
			'ptkp_status_update' => $request->ptkp_status_list,
			'ptkp_date_update' => $request->ptkp_date,
		];	
				
       return response()->json($data);
    }
	
	public function get_ptkp_report(Request $request) {
        if ($request->ajax()) {
		//	dd($request->all());
			$cat_date 				= $request->cat_date ?? null;
			$startdate 				= $request->startdate ?? null;
			$enddate 				= $request->enddate ?? null;
            $data = Employee::get_ptkp_report($cat_date,$startdate,$enddate,$this->accessBranch($request));
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->make(true);
        }
    }
	
	public function get_ex_concurent() {
        $result = Employee::get_ex_concurent();
        return response()->json($result);
    }

    public function getLeaveCustom(Request $request) {
        $getLeave = DB::table('master_leave_type')->where('restrict_by', '!=', 'System')->where('id_company', session('id_company'))->get();
        return response()->json($getLeave);
    }

    public function saveLeaveCustom(Request $request) {
    	try{
        	DB::beginTransaction();	

	    	$idLeaveBalance = $request->id_leave_balance_emp ?? null;
	    	$idEmployee = $request->id_employee;
	    	$idCompany = session('id_company');
	    	$idLeaveType = $request->id_leave_type;
	    	$leaveQuota = $request->leave_quota;
	    	$effectiveDate = $request->effective_date;
	    	$expiredDate = $request->expired_date;

	    	$dataLeaveBalance = [
	    		'id_employee' 		=> $idEmployee,
	    		'id_leave_type' 	=> $idLeaveType,
	    		'leave_quota' 		=> $leaveQuota,
	    		'effective_date' 	=> $effectiveDate,
	    		'expired_date' 		=> $expiredDate,
	    		'id_company'		=> $idCompany,
	    		'creation_date'		=> date('Y-m-d H:i:s'),
	    		'created_by'		=> session('id_user')
	    	];

	        $getLeaveBalance = EmployeeLeave::where('id_leave_type', $idLeaveType)->where('id_employee', $idEmployee)->where('status', 'A')->first();
	        if($getLeaveBalance && !$idLeaveBalance){
            	throw new \Exception('Cannot add data, Leave type is available');           
	        }

	    	if(!$idLeaveBalance){
	    		$dataLeaveBalance['used_leave'] = 0;
	    		$dataLeaveBalance['status'] = 'A';
	    		$dataLeaveBalance['created_by'] = session('id_user');
	        	$save = EmployeeLeave::create($dataLeaveBalance)->id_leave_balance_emp;
	        	$idLeaveBalanceEmployee = $save;
	    	} else {
	    		$dataLeaveBalance['updated_by'] = session('id_user');
	        	$save = EmployeeLeave::where('id_leave_balance_emp', $idLeaveBalance)->update($dataLeaveBalance);
	        	$idLeaveBalanceEmployee = $idLeaveBalance;
	    	}

	    	$startPatch = (strtotime(date('Y-m-d')) < strtotime($effectiveDate)) ? date('Y-m-d') : $effectiveDate;
			$patchWorkdays = WorkDays::patchWorkdays([$idCompany], $startPatch, $expiredDate, $idEmployee);

	        $getLeaveType = DB::table('master_leave_type')->where('id_leave_type',$idLeaveType)->first();
	        $getLeaveBalanceAfter = EmployeeLeave::where('id_leave_balance_emp', $idLeaveBalanceEmployee)->first();
	        
	    	$return['id_leave_balance_emp'] = $idLeaveBalanceEmployee;
	    	$return['leave_name'] = @$getLeaveType->description;
	    	$return['used_leave'] = @$getLeaveBalanceAfter->used_leave;
	    	$return['status'] = @$getLeaveBalanceAfter->status;

			DB::commit();
        	return response()->json(['status' => 'true', 'data' => $return, 'message' => 'Leave Data Saved Successfully']);		
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => 'false', 'data' => null, 'message' => $e->getMessage()]);
		}
    }

    public function data_verification(Request $request) {
    	if ($request->ajax()) {
        	$idSurvey = $request->id_survey_header ?? null;
        	$idSurveyHistory = $request->id_survey_history ?? null;
        	
            $data = Employee::get_data_verification($idSurvey, $idSurveyHistory);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
					return '';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('employee.employee.data_verification.index');
    }

    public function detail_verification(Request $request) {
    	try{
        	DB::beginTransaction();	
        	$idSurvey = $request->id_survey_header ?? null;
        	$idSurveyHistory = $request->id_survey_history ?? null;
        	$idEmployee = $request->id_employee ?? null;

        	$identity = Employee::getEmployeeDetail(null, $idEmployee);
            $detailVerification = Employee::get_detail_verification($idSurvey, $idSurveyHistory, $idEmployee);
            $verifyStatus = DB::table('hr_survey_answer_user_header')
            	->where('id_employee', $idEmployee)
            	->where('id_survey_header', $idSurvey)
            	->where('id_survey_history', $idSurveyHistory)
            	->first();

            $data['identity'] = @$identity[0];
            $data['result'] = $detailVerification;
            $data['verify_status'] = @$verifyStatus->is_processed;

        	DB::commit();
        	return response()->json(['status' => true, 'data' => $data, 'message' => 'Success']);		
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => false, 'data' => null, 'message' => $e->getMessage()]);
		}
    }

    public function save_detail_verification(Request $request) {
    	try{
        	DB::beginTransaction();	
        	$idSurvey = $request->id_survey_header ?? null;
        	$idSurveyHistory = $request->id_survey_history ?? null;
        	$idEmployee = $request->id_employee ?? null;

        	$verifyStatus = DB::table('hr_survey_answer_user_header')
            	->where('id_employee', $idEmployee)
            	->where('id_survey_header', $idSurvey)
            	->where('id_survey_history', $idSurveyHistory)
            	->first();
            if(@$verifyStatus->is_processed == true){
        		return response()->json(['status' => true, 'data' => $idEmployee, 'message' => 'Success']);
            }
            
        	$identity = Employee::getEmployeeDetail(null, $idEmployee);
            $detailVerification = Employee::get_detail_verification($idSurvey, $idSurveyHistory, $idEmployee);
			$surveyPathStorage = 'public/upload/survey/';
			$handleMoveFile = [];

            if($detailVerification->count() > 0){
            	$thisUniqueKey = '';
            	$thisColumnUniqueKey = '';
            	foreach ($detailVerification as $k => $val) {
            		if($val->unique_key==true){
            			$thisColumnUniqueKey = $val->update_to_column;
            			if($val->question_type == 'Single_Answer'){
            				$thisUniqueKey = $val->answer_code;
            			} else if($val->question_type == 'Essay'){
            				$thisUniqueKey = $val->answer_essay;
            			}
            		}

            		if($val->action_type == "INSERT OR UPDATE"){
            			$thisValue = '';
            			if($val->question_type == 'Single_Answer'){
            				$thisValue = $val->answer_code;
            			} else if($val->question_type == 'Essay'){
            				$thisValue = $val->answer_essay;
            			} else if($val->question_type == 'Upload_Files'){
            				$thisValue = $val->answer_attachment;
            			}

	            		if($val->unique_key==true){
	            			$thisValueSelected = $thisUniqueKey;
	            		} else {
	            			$thisValueSelected = $thisValue;
	            		}

        				$thisQuery = DB::table($val->update_to_table)
        					->select($val->update_to_column)
        					->where($val->update_to_column, $thisValueSelected);

    					if (Schema::hasColumn($val->update_to_table, 'id_employee')) {
    						//Jika dalam tabel terdapat kolom id_employee maka id_employee jg ikut
				            $thisQuery->where('id_employee', $val->id_employee);
				        }
        				$check = $thisQuery->get();
        					
        				if($check->count() > 0){
        					//Proses Update jika record ditemukan
        					$dataUpdate = [
    							'updated_by' => session('id_user'),
    							$val->update_to_column => $thisValueSelected
        					];
        					$update = $thisQuery->update($dataUpdate);
        				} else {
        					//Proses Insert jika record tidak ditemukan
        					$dataInsert = [
        						'id_company' => $val->id_company,
        						'created_by' => session('id_user'),
        						$val->update_to_column => $thisValueSelected
        					];
        					if (Schema::hasColumn($val->update_to_table, 'id_employee')) {
        						//Jika dalam tabel terdapat kolom id_employee maka id_employee jg ikut
					            $dataInsert['id_employee'] = $val->id_employee;
					        }
					        if (Schema::hasColumn($val->update_to_table, 'document_number')) {
	    						//Jika dalam tabel terdapat kolom id_employee maka id_employee jg ikut
					            $dataInsert['document_number'] = '';
					        }
        					$insert = DB::table($val->update_to_table)->insert($dataInsert);
        				}

        				if(!is_null($val->update_to_path)){
    						if(strpos(@$val->update_to_path, '{nik_employee}') !== false){
	        					$updatePath = str_replace('{nik_employee}', @$val->nik_employee, @$val->update_to_path);
							} else {
								$updatePath =  $getConfig[$k]->update_to_path;
							}
                    		Storage::makeDirectory($updatePath, 0775, true, true);

    						if (@$val->answer_attachment && Storage::exists($surveyPathStorage.@$val->answer_attachment)) {
								$thisHandleMoveFile['directory'] = $updatePath;

								$infoPath = Storage::mimeType($surveyPathStorage.@$val->answer_attachment);
								if($infoPath == 'application/pdf'){
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.pdf';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								} else {
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.jpg';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								}
								$handleMoveFile[] = $thisHandleMoveFile;
							}
    					}
            		}

            		if($val->action_type == "INSERT"){
            			$thisValue = '';
            			if($val->question_type == 'Single_Answer'){
            				$thisValue = $val->answer_code;
            			} else if($val->question_type == 'Essay'){
            				$thisValue = $val->answer_essay;
            			} else if($val->question_type == 'Upload_Files'){
            				$thisValue = $val->answer_attachment;
            			}

            			if($val->unique_key==true){
	            			$thisValueSelected = $thisUniqueKey;
	            		} else {
	            			$thisValueSelected = $thisValue;
	            		}

    					$dataInsert = [
    						'id_company' => $val->id_company,
    						'created_by' => session('id_user'),
    						$val->update_to_column => $thisValueSelected
    					];
    					if (Schema::hasColumn($val->update_to_table, 'id_employee')) {
    						//Jika dalam tabel terdapat kolom id_employee maka id_employee jg ikut
				            $dataInsert['id_employee'] = $val->id_employee;
				        }
				        if (Schema::hasColumn($val->update_to_table, 'document_number')) {
				            $dataInsert['document_number'] = '';
				        }
    					$insert = DB::table($val->update_to_table)->insert($dataInsert);


    					if(!is_null($val->update_to_path)){
    						if(strpos(@$val->update_to_path, '{nik_employee}') !== false){
	        					$updatePath = str_replace('{nik_employee}', @$val->nik_employee, @$val->update_to_path);
							} else {
								$updatePath =  $getConfig[$k]->update_to_path;
							}
                    		Storage::makeDirectory($updatePath, 0775, true, true);
							
    						if (@$val->answer_attachment && Storage::exists($surveyPathStorage.@$val->answer_attachment)) {
                                $thisHandleMoveFile['directory'] = $updatePath;

								$infoPath = Storage::mimeType($surveyPathStorage.@$val->answer_attachment);
								if($infoPath == 'application/pdf'){
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.pdf';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								} else {
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.jpg';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								}
								$handleMoveFile[] = $thisHandleMoveFile;
							}
    					}
            		}

            		if($val->action_type == "UPDATE"){
            			$thisValue = '';
            			if($val->question_type == 'Single_Answer'){
            				$thisValue = $val->answer_code;
            			} else if($val->question_type == 'Essay'){
            				$thisValue = $val->answer_essay;
            			} else if($val->question_type == 'Upload_Files'){
            				$thisValue = $val->answer_attachment;
            			}

            			if($val->unique_key==true){
	            			$thisValueSelected = $thisUniqueKey;
	            		} else {
	            			$thisValueSelected = $thisValue;
	            		}
	            		
	            		$whereUpdate = [
    						'id_company' => $val->id_company,
	            		];
    					$dataUpdate = [
    						'id_company' => $val->id_company,
    						'updated_by' => session('id_user'),
    						$val->update_to_column => $thisValueSelected
    					];
    					if (Schema::hasColumn($val->update_to_table, 'id_employee')) {
    						//Jika dalam tabel terdapat kolom id_employee maka id_employee jg ikut
				            $dataUpdate['id_employee'] = $val->id_employee;
				            $whereUpdate['id_employee'] = $val->id_employee;
				        }

				        if($thisColumnUniqueKey!='' && $thisUniqueKey!=''){
				        	if (Schema::hasColumn($val->update_to_table, $thisColumnUniqueKey)) {
				            	$whereUpdate[$thisColumnUniqueKey] = $thisUniqueKey;
					        }
				        }
    					$update = DB::table($val->update_to_table)->where($whereUpdate)->update($dataUpdate);

    					if(!is_null($val->update_to_path)){
    						if(strpos(@$val->update_to_path, '{nik_employee}') !== false){
	        					$updatePath = str_replace('{nik_employee}', @$val->nik_employee, @$val->update_to_path);
							} else {
								$updatePath =  $getConfig[$k]->update_to_path;
							}
                    		Storage::makeDirectory($updatePath, 0775, true, true);
							
    						if (@$val->answer_attachment && Storage::exists($surveyPathStorage.@$val->answer_attachment)) {
                                $thisHandleMoveFile['directory'] = $updatePath;

								$infoPath = Storage::mimeType($surveyPathStorage.@$val->answer_attachment);
								if($infoPath == 'application/pdf'){
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.pdf';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								} else {
									$newFileName = $val->nik_employee.'-'.$thisUniqueKey.'.jpg';
									$thisHandleMoveFile['from'] = $surveyPathStorage.@$val->answer_attachment;
									$thisHandleMoveFile['to'] = $updatePath.$newFileName;
								}
								$handleMoveFile[] = $thisHandleMoveFile;
								$dataUpdate[$val->update_to_column] = $newFileName;
							}
    					}

    					$update = DB::table($val->update_to_table)->where($whereUpdate)->update($dataUpdate);
            		}
            	}
            }

            DB::table('hr_survey_answer_user_header')
            	->where('id_employee', $idEmployee)
            	->where('id_survey_header', $idSurvey)
            	->where('id_survey_history', $idSurveyHistory)
            	->update(['is_processed' => true]);

        	DB::commit();

        	//ditempatkan stelah commit karena Proses pindah file dilakukan jika proses update/insert ke database benar2 berhasil
        	if(count($handleMoveFile) > 0){
        		foreach ($handleMoveFile as $k => $move) {
                    Storage::makeDirectory($move['directory'], 0775, true, true);
                    Storage::move($move['from'], $move['to']);
        		}
        	}
        	return response()->json(['status' => true, 'data' => $idEmployee, 'message' => 'Success']);
        } catch (\Exception $e) {
            DB::rollBack();
			return response()->json(['status' => false, 'data' => null, 'message' => $e->getMessage()]);
		}
    }

    private function employee(){
        $employee = DB::table('hr_employee as he')
                    ->select('he.id_employee', 'he.gender', 'he.nik_employee')
                    ->where('he.id_user', session('id_user'))
                    ->where('he.id_company', session('id_company'))
                    ->where('he.status', 'A')
                    ->first();
        return $employee;
    }

    public function employee_all(Request $request) {

    	ini_set('max_execution_time', -1);
    	$path_menu_param    = $request->path();
        $path_menu          = @$request->path_menu;
        $myIdEmployee       = @$this->employee()->id_employee;
        $myNik              = @$this->employee()->nik_employee;
        $default_company    = null;

        if(session('company_type') == 'os'){
            $getUser = DB::table('master_users as mu')->select('mu.default_company')->where('mu.user_name', session('username'))->first();
            $default_company = $getUser->default_company;
            if($default_company == session('id_company')){
                $getOther = DB::table('hr_employee as he')
                        ->join('master_users as mu', 'mu.user_name', '=', 'he.nik_employee')
                        ->select("mu.default_company")
                        ->where('mu.default_company', '!=', session('id_company'))
                        ->where('he.id_employee', session('id_company'))
                        ->limit(2)
                        ->get();
                if($getOther){
                    $default_company = $getOther->pluck('default_company')->unique();
                }
            }
        }

        $getdepartment      = WorkDays::getdepartment($default_company);
        $get_employee       = WorkDays::getemployeename([], ['A']);
        $accessGroup        = session('access_group');
        $branchByManager    = [];
        $id_branch          = [];
        $id_region          = [];

        if(session('access_group') != 'Default_Administrator'){
            //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
            $address = ['time_attendance/attendance/attendance_list', 'employee/employee_setting/workdays'];
            // if(in_array($path_menu, $address)){
                $path_menu_ = 'employee/employee_setting/workdays';
            // }
            $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
            if($get_ur->count() > 0){
                $get_branch = [];
                if((session('company_type') == 'os' && (session('access_group')=='Default_User' || session('access_group')=='Default_Manager')) || session('company_type') != 'os' ){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                }
                if(count($get_branch) > 0){
                    $branchByManager  = $get_branch;
                    $id_branch        = $branchByManager;
                } else {
                    $branchByManager  = 'null';
                }
                if(count($id_branch) > 0){
                    $id_region  = MasterBranch::whereIn('id_branch', $id_branch)->pluck('id_region')->unique()->toArray();
                }
            } 
        }

        $getregional    = WorkDays::getregional($id_region);
        $getlocation    = WorkDays::getlocation();
        $getbranch      = WorkDays::getbranch();

        return view('employee.employee.employee_all.index', compact('get_employee', 'myIdEmployee', 'getdepartment','getregional','getlocation','getbranch','path_menu_param','accessGroup','branchByManager', 'myNik')	);
    }

    public function _export_attendance(Request $request) {
        ini_set('max_execution_time', -1);

        $start              = $request->startdate ?? 'null';
        $end                = $request->enddate ?? 'null';
        $nik                = $request->employeename ?? 'null';
        $regional           = $request->regional  ?? 'null';
        $branch             = $request->branch ?? 'null';
        $location           = $request->location  ?? 'null';
        $department         = $request->department  ?? 'null';
        $path_menu          = $request->path_menu  ?? 'null';
        $id_company         = $request->id_company  ?? 'null';
        $branchByManager    = [];

        if($nik != 'null'){
            if(strpos($nik, ',') !== false){
                $nik = explode(',', $nik);
            } else {
                $nik = [$nik];
            }
        }

        if($request->status != 'null'){
            if(strpos($request->status, ',') !== false){
                $status = explode(',', $request->status);
            } else {
                $status = [$request->status];
            }
        } else {
            $status = ['A', 'I'];
        }


        if($id_company != 'null'){
            if(strpos($id_company, ',') !== false){
                $id_company = explode(',', $id_company);
            } else {
                $id_company = [$id_company];
            }
        } else {
            $id_company = null;
        }

        if($branch != 'null'){
            $branch     = $request->branch;
        } else {
            if(session('access_group') != 'Default_Administrator'){
                //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
                $path_menu_ = 'employee/employee_setting/workdays';
                $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
                if($get_ur->count() > 0){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                    if(count($get_branch) > 0){
                        $branchByManager        = $get_branch;
                    }
                } 
                $branch = $branchByManager;
            } else {
                $branch     = 'null';
            }
        }

        $dateRangeName  = date("j M Y", strtotime($start)).' to '.date("j M Y", strtotime($end));
        $period         = new \DatePeriod(
            new \DateTime($start),
            new \DateInterval('P1D'),
            new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        $attendStatus   = ['ANL','BPT','CHM','CST','DIF','EDO','GVL','MAT','MDL','MIS','PAT','SDC','UPL','WMC','OFF','PRS','ABS','NSI','NSO'];
        $title          = ['No', 'Employee No', 'Employee Name', 'Position', 'Principal', 'Region', 'Organization Unit', 'Department', 'PT', 'Status'];
        $legend         = [
            'ANL'=>'Annual Leave',
            'BPT'=>'Child Baptism',
            'CHM'=>'Employee Child Married',
            'CST'=>'Child Circumcision',
            'DIF'=>'Decease Spouse, Parents & Child (In Law), Siblings',
            'EDO'=>'Extra Day Off',
            'GVL'=>'Deceased of Family',
            'MAT'=>'Maternity Leave',
            'MDL'=>'Employee Married Leave',
            'MIS'=>'Miscarriage Leave',
            'PAT'=>'Employee Child Birth',
            'SDC'=>'Sick',
            'UPL'=>'Unpaid Leave',
            'WMC'=>'Wife Miscarriage',
            'OFF'=>'Off',
            'PRS'=>'Present',
            'ABS'=>'Absent',
            'NSI'=>'No Swipe In',
            'NSO'=>'No Swipe Out',
        ];
        $countTitle     = count($title);
        $getInactive = [];

        $period = new \DatePeriod(
             new \DateTime($start),
             new \DateInterval('P1D'),
             new \DateTime(date('Y-m-d', strtotime($end.' +1 day')))
        );
        foreach ($period as $k => $v) {
            $dateRange[]        = $v->format("Y-m-d");
            $title[$countTitle] = $v->format("D, d M");
            $datePeriod[] = $v->format("Y-m-d");
            $countTitle++;
        }


        $workDays = DB::table('hr_work_days')
                ->selectRaw("DISTINCT(current_dates) as dates")
                ->whereIn('current_dates', $datePeriod);
        if($id_company){
        	$workDays->whereIn('id_company', $id_company);
        }

        $workDays = $workDays->get();

        if($workDays->count() < 1){
            echo "<script>alert('No Data in this Period');window.close();</script>";
            return false;
        }

        $companyName = 'All Company';
        if(!($id_company=='null'||$id_company==null)){
        	$company_ = DB::table('master_company')
                ->whereIn('id_company', $id_company)
                ->first();
            $companyName = $company_->company_name;
        }

        $countTitleAfter = count($title);
        foreach ($attendStatus as $k => $v) {
            $title[$countTitleAfter] = $v;
            $countTitleAfter++;
            $thisVal = $v;
            $$thisVal = []; // value dari array dijadikan nama variabel utk nampung, eg: $OFF,$PRS,$ANL,$UPL
        }

        $dataSheet = [
            [''],
            ['Attendance Status Report '],
            ['From '.$dateRangeName],
            [''],
        ];

        $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel
        $data = $this->WorkDays->summary($nik, $id_company, $start, $end, $branch, $location, $regional, $department, $status, $path_menu);

        if($data){
            $all_nik        = $data->pluck('nik_employee');
            $all_name       = $data->pluck('name');
            $employeeStatus = [];

            $summary = [];
            $employeePosition = [];
            $employeeByPrinciple = [];

            foreach ($all_nik as $k => $v) {
                if(!in_array($v, $summary)){
                    //penempatan parmeter nik dan nama dahulu karena pasti terdapat di setiap hasil query
                    $summary[$v]['nik_employee'] = @$v;
                    $summary[$v]['name'] = @$all_name[$k];
                    $employeePosition[$v]['A'] = '';
                    $employeePosition[$v]['I'] = '';
                }
            }

            foreach ($data as $k => $val) {
                if($val->nik_employee == $summary[$val->nik_employee]['nik_employee']){
                    // pengecekan utk employee yg memiliki lbh dr 1 principle
                    // if(!in_array($val->nik_employee, $employeeByPrinciple)){
                        $summary[$val->nik_employee]['principal'] = $val->principal;
                    // } else {
                        // $summary[$val->nik_employee]['principal'] .= ', '.$val->principal;
                    // }

                    //penemptan paremeter posisi,unit,status
                    $summary[$val->nik_employee]['position_name'] = $val->position_name;
                    $summary[$val->nik_employee]['region'] = $val->region;
                    $summary[$val->nik_employee]['organization_unit'] = $val->organization_unit;
                    $summary[$val->nik_employee]['department'] = $val->department;
                    $summary[$val->nik_employee]['company'] = $val->company_name;
                    $summary[$val->nik_employee]['employee_status'] = $val->employee_status;

                    if(in_array($val->current_dates, $datePeriod)){
                        //penempatan parameter attendance status sesuai tanggalnya
                        $summary[$val->nik_employee][$val->current_dates] = $val->attendance_status;
                    }
                    $employeeByPrinciple[] = $val->nik_employee;
                    $employeeStatus[$val->nik_employee][] = $val->employee_status;
                    
                    if($val->position_name != @$employeePosition[@$val->nik_employee][@$val->employee_status]){
                        $employeePosition[$val->nik_employee][$val->employee_status] = $val->position_name;
                    }
                }
            }

            $sort = array_column($summary, 'name'); // sort result by name
            array_multisort($sort, SORT_ASC, $summary);
            $rows = [];
            $number = 1;
            $var = '';
            foreach ($summary as $k => $val) {
                $employee_status_summary = in_array('A', $employeeStatus[$val['nik_employee']]) ? 'A' : $val['employee_status'];
                //utk menentukan posisi employee berdasar status employee terakhir
                $employee_position =  in_array('A', $employeeStatus[$val['nik_employee']]) ? $employeePosition[$val['nik_employee']]['A'] : $employeePosition[$val['nik_employee']][$val['employee_status']];
                
                //resultValue utk menampung parameter yg sudah di(pivot) yg akan dijadikan data ke spreadsheet
                $resultValue = [
                    $number,
                    $val['nik_employee'],
                    $val['name'],
                    $employee_position,
                    $val['principal'],
                    $val['region'],
                    $val['organization_unit'],
                    $val['department'],
                    $val['company'],
                    $employee_status_summary,
                ];
                $allValueThisPeriod = [];
                foreach ($datePeriod as $key => $valu) {
                    $valueOfThisDate = is_null(@$summary[$k][$valu]) ? '-' : @$summary[$k][$valu];
                    // UTK MENGHITUNG BANYAK NILAI : ABS,ANL,OFF.  KESELURUHAN MASING2 ORG
                    if($valueOfThisDate != ''){
                        $thisVal = $valueOfThisDate;
                        $$thisVal[] = 1;
                        $allValueThisPeriod[] = 1;
                    }
                    $resultValue[] = $valueOfThisDate;
                }
                foreach ($attendStatus as $kk => $val_) {
                    // UTK MENJUMLAHKAN NILAI : ABS,ANL,OFF.  KESELURUHAN MASING2 ORG
                    $thisVal = $val_;
                    $resultValue[] = (count($$thisVal) > 0) ? count($$thisVal) : '0';
                }
                foreach ($attendStatus as $k => $val) {
                    $thisVal = $val;
                    $$thisVal = []; // nama variabel utk nampung direset lagi setiap beda org, eg: $OFF,$PRS,$ANL,$UPL
                }
                if(count($allValueThisPeriod) > 0){ //hanya memasukkan ke excel employee yg memiliki workdays pd periode tgl yg dipilih
                    $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
                }
                $number++;
            }
        }

        $dataSheet[] = ['Legend']; 
        foreach ($attendStatus as $key => $val) {
            $dataSheet[] = [$val, ':', $legend[$val]]; 
        }

        $exportType = 'excel';
        $filename   = 'attendance-'.$dateRangeName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $sheetName1 = 'Sheet 1';
        $workSheet1 = new Worksheet($spreadsheet, $sheetName1);
        $spreadsheet->addSheet($workSheet1, 0);
        $spreadsheet->setActiveSheetIndexByName($sheetName1); // utk set sheet yg aktif

        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2

        $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $activeSheet = $spreadsheet->getActiveSheet();

        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }

        // styling manual berdasar cell
        $lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAfterTitle   = count($title)+1;
        $columnLegend       = count($attendStatus) + 1;
        $columnAllData      = $lastColumn.($lastRow - $columnLegend);

        $activeSheet->mergeCells('A2:C2');
        $activeSheet->mergeCells('A3:C3');
        $activeSheet->getStyle('A6:'.$columnAllData)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('B6:E'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('F6:'.$lastColumn.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A5:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

     public function _export_employee(Request $request) {
        ini_set('max_execution_time', -1);

        $start              = $request->startdate ?? 'null';
        $end                = $request->enddate ?? 'null';
        $nik                = $request->employeename ?? 'null';
        $regional           = $request->regional  ?? 'null';
        $branch             = $request->branch ?? 'null';
        $location           = $request->location  ?? 'null';
        $department         = $request->department  ?? 'null';
        $path_menu          = $request->path_menu  ?? 'null';
        $id_company         = $request->id_company  ?? 'null';
        $branchByManager    = [];
    	$whereDept 			= [];

        if($nik != 'null'){
            if(strpos($nik, ',') !== false){
                $nik = explode(',', $nik);
            } else {
                $nik = [$nik];
            }
        }

        if($request->status != 'null'){
            if(strpos($request->status, ',') !== false){
                $status = explode(',', $request->status);
            } else {
                $status = [$request->status];
            }
        } else {
            $status = ['A', 'I'];
        }

        if($id_company != 'null'){
            if(strpos($id_company, ',') !== false){
                $id_company = explode(',', $id_company);
            } else {
                $id_company = [$id_company];
            }
        }

        if($branch != 'null'){
        	if(strpos($request->branch, ',') !== false){
                $branch = explode(',', $request->branch);
            } else {
                $branch = [$request->branch];
            }
        } else {
            if(session('access_group') != 'Default_Administrator'){
                //kondisi utk menampilkan branch brdasar area kerjanya dari menu yg diassign
                $path_menu_ = 'employee/employee_setting/workdays';
                $get_ur = MasterUserResponsibility::get_user_responsibility(session('id_user'), $path_menu_)->pluck('id_user_responsibility');
                if($get_ur->count() > 0){
                    $get_branch = RelationBranchUser::get_branch_by_user_responsibility($get_ur->all())->pluck('id_branch')->all();
                    if(count($get_branch) > 0){
                        $branchByManager = $get_branch;
                    }
                } 
                $branch = $branchByManager;
            } else {
                $branch     = 'null';
            }
        }

        if($department != 'null'){
            if(strpos($department, ',') !== false){
                $department = explode(',', $department);
            } else {
                $department = [$department];
            }
        	$dept = DB::table('master_department')->whereIn('id_dept', $department)->get();
        	if($dept->count() > 0){
        		foreach ($dept as $k => $val) {
        			$whereDept[] = $val->description;
        		}
        	}
        }
        
        $dataEmployee = [];
        if($id_company == 'null'){
        	$allCompany = DB::table('master_company')->where('status', 'A')->get();
        	foreach ($allCompany as $k => $val) {
        		$get = DB::table(DB::raw("sp_funct_get_employee_report_all ('".$val->id_company."') sfgera"))
                    ->select('sfgera.*')
                    ->whereIn('sfgera.status_active', $status);

                if($nik != 'null'){
                	$get->whereIn('sfgera.nik_employee', $nik);
                }
                if($branch != 'null'){
                	$get->whereIn('sfgera.id_branch', $branch);
                }
                if(count($whereDept) > 0){
                	$get->where(function ($query) use($whereDept) {
	             		for ($i = 0; $i < count($whereDept); $i++){
			                $query->orWhere('department', 'like',  '%' . $whereDept[$i] .'%');
	             		}      
			        });
                }
                $get = $get->get();
                foreach ($get as $key => $value) {
                	$dataEmployee[] = $value;
                }
        	}
        } else {
        	foreach ($id_company as $key => $thisIdCompany) {
        		$get = DB::table(DB::raw("sp_funct_get_employee_report_all ('".$thisIdCompany."') sfgera"))
	    			->select('sfgera.*')
	                ->whereIn('sfgera.status_active', $status);

	            if($nik != 'null'){
	            	$get->whereIn('sfgera.nik_employee', $nik);
	            }
	            if($branch != 'null'){
	            	$get->whereIn('sfgera.id_branch', $branch);
	            }
	            if(count($whereDept) > 0){
                	$get->where(function ($query) use($whereDept) {
	             		for ($i = 0; $i < count($whereDept); $i++){
		                	$query->orWhere('department', 'like',  '%' . $whereDept[$i] .'%');
	             		}      
			        });
                }
	            $get = $get->get();
	    		foreach ($get as $key => $value) {
	            	$dataEmployee[] = $value;
	            }
        	}
        }
        
        $dateRangeName  = date("j M Y", strtotime($start)).' to '.date("j M Y", strtotime($end));
        $title = ['No', 'NIK', 'Name', 'Join Date', 'ID Number', 'Date of Birth', 'Position', 'Principal', 'Department', 'Regional', 'Branch', 'Work Location', 'Direct Supervisor', 'Immediate Manager', 'Employment Status', 'Expired Date', 'Sales Code', 'Resign Date', 'Terminate Reason', 'Status'];
       
        $countTitle = count($title);
        $getInactive = [];

        $companyName = 'All Company';
        if(!($id_company=='null'||$id_company==null)){
        	$company_ = DB::table('master_company')
                ->where("id_company", $id_company)
                ->first();
            $companyName = $company_->company_name;
        }

        $countTitleAfter = count($title);
        $dataSheet = [
            [''],
            ['Employee Report'],
            [''],
            [''],
        ];

        $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

        if(count($dataEmployee) > 0){
            $number = 1;
            $var = '';
            foreach ($dataEmployee as $k => $val) {
                $resultValue = [
                    $number,
                    $val->nik_employee,
                    $val->name,
                    $val->join_date,
                    '`'.$val->identification_number,
                    $val->birthdate,
                    $val->position_routing,

                    $val->principal,
                    $val->department,
                    $val->regional,
                    $val->branch,
                    $val->work_location,
                    $val->parent_emp_name,
                    $val->indirect_emp_name,

                    $val->employment_status,
                    $val->expired_date,
                    $val->sales_code,
                    $val->resign_date,
                    $val->terminate_reason,
                    $val->status_active,
                ];
                $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
                $number++;
            }
        }


        $exportType = 'excel';
        $filename   = 'employee-report-'.$dateRangeName;
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        $sheetName1 = 'Sheet 1';
        $workSheet1 = new Worksheet($spreadsheet, $sheetName1);
        $spreadsheet->addSheet($workSheet1, 0);
        $spreadsheet->setActiveSheetIndexByName($sheetName1); // utk set sheet yg aktif

        $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2

        $worksheets = [$workSheet1]; // array utk kondisi nanti jika butuh bnyk sheet
        foreach ($worksheets as $worksheet){
            foreach ($worksheet->getColumnIterator() as $column){
                $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $activeSheet = $spreadsheet->getActiveSheet();

        $rowStyle = [2,3,5]; // Identitas Row yang akan di style kan
        foreach ($rowStyle as $k => $val) {
            $activeSheet->getStyle($val.':'.$val)->getFont()->setBold(true);
            $activeSheet->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        }

        // styling manual berdasar cell
        $lastColumn         = $activeSheet->getHighestColumn();
        $lastRow            = $activeSheet->getHighestRow();
        $columnAfterTitle   = count($title)+1;
        $columnLegend       = 1;
        $columnAllData      = $lastColumn.($lastRow - $columnLegend);

        $activeSheet->mergeCells('A2:C2');
        $activeSheet->mergeCells('A3:C3');
        $activeSheet->getStyle('A6:'.$columnAllData)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('B6:E'.$lastRow)->getAlignment()->setHorizontal('left');
        $activeSheet->getStyle('F6:'.$lastColumn.$lastRow)->getAlignment()->setHorizontal('center');
        $activeSheet->getStyle('A5:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

        if($exportType == 'excel'){
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
            $writer->save('php://output');
        } else {
            $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
            $writer->setIncludeCharts(true);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
            $writer->save('php://output');
        }
    }

    public function _export_lapkar(Request $request) {
        // ini_set('max_execution_time', -1);

        // $start              = $request->startdate ?? 'null';
        // $end                = $request->enddate ?? 'null';
        // $nik                = $request->employeename ?? 'null';
        // $regional           = $request->regional  ?? 'null';
        // $branch             = $request->branch ?? 'null';
        // $location           = $request->location  ?? 'null';
        // $department         = $request->department  ?? 'null';
        // $path_menu          = $request->path_menu  ?? 'null';
        // $id_company         = $request->id_company  ?? 'null';

        // if($id_company != 'null'){
        //     if(strpos($id_company, ',') !== false){
        //         $id_company = explode(',', $id_company);
        //     } else {
        //         $id_company = [$id_company];
        //     }
        // }

        // $getMasterJoin = DB::table('master_general_data')->where(['code'=>'Join','id_general_type'=>6])->get();
        // $allIdJoin = $getMasterJoin->pluck('id_general_data')->all();
        // $descByIdJoin = $getMasterJoin->pluck('description', 'id_general_data')->all();

        // $getNewHire = DB::table('hr_career_transaction as hct')
        // 	->leftJoin('hr_employee as he', 'hct.id_employee', '=', 'he.id_employee')
        // 	->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'he.id_employment_status')
        // 	->leftJoin('master_general_data as mgd2', 'mgd2.id_general_data', '=', 'hct.id_transaction_type')
        // 	->leftJoin('master_position_detail as mpd', 'mpd.id_position_detail', '=', 'hct.id_position_detail')
        // 	->leftJoin('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
        // 	->leftJoin('master_job_status as mjs', 'mjs.id_job_status', '=', 'mpr.id_job_status')
        // 	->leftJoin('master_job_grade as mjg', 'mjg.id_job_grade', '=', 'mpr.id_job_grade')
        // 	->leftJoin('relation_position_detail_principal as rpdr', 'rpdr.id_position_detail', '=', 'mpd.id_position_detail')
        // 	->leftJoin('master_principal as mp', 'mp.id_principal', '=', 'rpdr.id_principal')
        // 	->leftJoin('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
        // 	->leftJoin('master_job_position as mjp', 'mjp.id_position', '=', 'mpr.id_position')
        // 	->leftJoin('master_department as md', 'md.id_dept', '=', 'mjp.id_dept')
        // 	->select('hct.*', 'he.nik_employee', 'he.ptkp_status', 'he.gender', 'he.expired_date', 'mgd.description as employment_status', 'mgd2.description as transaction_type', 'mjg.description as job_grade', 'mjs.description as job_status', 'mp.principal_code as division', 'mpr.description as jabatan', 'mpr.description as department')
        // 	->whereIn('hct.id_transaction_type', $allIdJoin)
        // 	->whereIn('hct.id_company', $id_company)
        // 	->get();
        
        // $idEmployeeNewRehire = [];
        // if($getNewHire->count() > 0){
        // 	foreach ($getNewHire as $k => $val) {
        // 		if(str_contains($val->transaction_type, 'Rehire')){
        // 			$idEmployeeNewRehire[] = $val->id_employee;
        // 		}

        // 	}
        // }


        // $id_survey_header   = $request->id_survey_header
        //     ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
        //     ->leftJoin('hr_employee as he', 'he.id_employee', '=', 'hsh.id_employee_request')
        //     ->select('hsh.*', 'mgd.description as survey_type', 'he.name as request_by')->where('hsh.id_survey_header', $id_survey_header)->first();
        // $surveyName = $getSurveyHeader->description;
        // $requestBy = $getSurveyHeader->request_by;
        // $surveyType = $getSurveyHeader->survey_type;
        // $start = date("j M Y", strtotime($getSurveyHeader->start_date));
        // $end = date("j M Y", strtotime($getSurveyHeader->end_date));
        // $withScore = ($getSurveyHeader->with_score == null || $getSurveyHeader->with_score == false) ? 'No' : 'Yes';
        // $id_department = DB::table('relation_department_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_dept')->all();
        // $id_region = DB::table('relation_regional_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_region')->all();
        // $id_branch = DB::table('relation_branch_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_branch')->all();
        // $id_job_grade = DB::table('relation_jobgrade_surveys')->where('id_survey_header', $id_survey_header)->get()->pluck('id_job_grade')->all();
        // $department = '';
        // $region = '';
        // $branch = '';
        // $jobGrade = '';

        // if($id_department){
        //     $getDepartment = DB::table('master_department')->whereIn('id_dept', $id_department)->get()->pluck('description')->all();
        //     $department = implode(', ', $getDepartment);
        // }
        // if($id_region){
        //     $getRegion = DB::table('master_region')->whereIn('id_region', $id_region)->get()->pluck('description')->all();
        //     $region = implode(', ', $getRegion);
        // }
        // if($id_branch){
        //     $getbranch = DB::table('master_branch')->whereIn('id_branch', $id_branch)->get()->pluck('description')->all();
        //     $branch = implode(', ', $getbranch);
        // }
        // if($id_job_grade){
        //     $getJobGrade = DB::table('master_job_grade')->whereIn('id_job_grade', $id_job_grade)->get()->pluck('description')->all();
        //     $jobGrade = implode(', ', $getJobGrade);
        // }

        // $getAnswer = DB::table(DB::raw("sp_funct_detail_survey_view(".$id_survey_header.", ".session('id_company').", null) sfdsv"))
        //         ->select('sfdsv.*')->get();

        // $headerSheet = [
        //     [''],
        // ];

        // $dataSheet      = $headerSheet;
        // $title          = ['No', 'Surveyor', 'Department', 'Regional', 'Branch', 'Point'];
        // $countTitle     = count($title);
        // $getQuestion    = DB::table('hr_survey_question as hsq')
        //                     ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsq.id_question_group')
        //                     ->select('hsq.sequence', 'hsq.id_survey_question', 'hsq.question', 'mgd.description as group')->where('hsq.id_survey_header', $id_survey_header)->get();
        // if($getAnswer){
        //     $allSurveyor = $getAnswer->pluck('unique_surveyor');
        //     $allSurveyDepartment = $getAnswer->pluck('department');
        //     $allSurveyRegion = $getAnswer->pluck('regional');
        //     $allSurveyBranch = $getAnswer->pluck('branch');
        //     $allSurveyGroup = $getAnswer->pluck('group_survey');

        //     $thisQuestion = [];
        //     foreach ($getQuestion as $key => $val) {
        //         $thisQuestion[$val->sequence] = $val->sequence;
        //         $title[$countTitle] = $val->sequence;
        //         $countTitle++;
        //     }
        //     $dataSheet[] = $title; //langsung masukkan ke array yg ditampung untuk di generate ke excel

        //     $summary        = [];
        //     foreach ($allSurveyor as $k => $v) {
        //         if(!in_array($v, $summary)){
        //             $summary[$v]['surveyor'] = @$v;
        //             $summary[$v]['department'] = @$allSurveyDepartment[$k];
        //             $summary[$v]['regional'] = @$allSurveyRegion[$k];
        //             $summary[$v]['branch'] = @$allSurveyBranch[$k];
        //             $summary[$v]['point'] = 0;
        //         }
        //     }

        //     foreach ($getAnswer as $k => $val) {
        //         if($val->unique_surveyor == $summary[$val->unique_surveyor]['surveyor']){
        //             if(is_null($val->multiple_answer)){
        //                 $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $val->essay_answer;
        //             } else {
        //                 $shortcut = strip_tags(@$summary[$val->unique_surveyor][$thisQuestion[$val->nomor]]);
        //                 if(!is_null($shortcut)){
        //                     $merge = $shortcut.', '.$val->multiple_answer;
        //                     $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $merge;
        //                 } else {
        //                     $summary[$val->unique_surveyor][$thisQuestion[$val->nomor]] = $val->multiple_answer;
        //                 }
        //                 $surveyorPoint[$val->unique_surveyor][] = $val->point;
        //             }
        //             if($withScore == 'No'){
        //                 $summary[$val->unique_surveyor]['point'] = '';
        //             } else {
        //                 $summary[$val->unique_surveyor]['point'] += $val->point;
        //             }
        //         }
        //     }
        //     $sort = array_column($summary, 'surveyor'); // sort result by name
        //     array_multisort($sort, SORT_ASC, $summary);

        //     $number = 1;
        //     $resultValue = [];
        //     $countTableContent = 4;
        //     foreach ($summary as $key => $val) {
        //         $resultValue = [
        //             $number,
        //             $val['surveyor'],
        //             $val['department'],
        //             $val['regional'],
        //             $val['branch'],
        //             $val['point'],
        //         ];
        //         foreach ($getQuestion as $k => $item) {
        //             $resultValue[] = $val[$item->sequence];
        //         }
        //         $countTableContent = count($resultValue);
        //         $number++;
        //         $dataSheet[] = $resultValue; //langsung masukkan ke array yg ditampung untuk di generate ke excel
        //     }
        // }
        // $dataSheet[] = ['']; 
        // $dataSheet[] = ['No', 'Group', 'Question']; 
        // foreach ($getQuestion as $key => $val) {
        //     $dataSheet[] = [$val->sequence, $val->group, $val->question]; 
        // }

        // $exportType = 'excel';
        // $filename   = 'lapkar-'.$surveyName;
        // $spreadsheet = new Spreadsheet(); 
        // $spreadsheet->removeSheetByIndex(0); // hapus sheet default bawaan

        // $workSheet1 = new Worksheet($spreadsheet);
        // $spreadsheet->addSheet($workSheet1, 0);
        // $workSheet1->fromArray($dataSheet); //ngolah array dimasukkan ke cell masing2
        // $sheet1 = $spreadsheet->getSheet(0)->setTitle("New Hire");

        // $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        // }
        // $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet1->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet1->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        //     $sheet1->mergeCells('B'.$val.':E'.$val);
        // }

        // $lastColumn         = $sheet1->getHighestColumn();
        // $lastRow            = $sheet1->getHighestRow();
        // $columnAfterTitle   = count($title)+1;
        // $columnQuestion     = count($getQuestion);
        // $columnAllData      = $lastColumn.($lastRow - ($columnQuestion+2));
        // $rowQuestion        = $lastRow - count($getQuestion)+1;

        // for ($x=0; $x < count($getQuestion); $x++) { 
        //     $rowQuestionFor = $rowQuestion+$x;
        //     $sheet1->mergeCells('C'.$rowQuestionFor.':'.$lastColumn.$rowQuestionFor);
        // }
        // $sheet1->mergeCells('A2:E2');
        // $sheet1->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        // $sheet1->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');



        // // Sheet 2
        // $workSheet2 = new Worksheet($spreadsheet);
        // $spreadsheet->addSheet($workSheet2, 1);
        // $dataSheet2 = $headerSheet;
        // $getQ1 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 1) sfdsv"))->select('sfdsv.*')->get();
        // $title2     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        // $dataSheet2[] = $title2; 
        // $numSheet2 = 1;
        // $resultValue = [];
        // foreach ($getQ1 as $key => $val) {
        //     $resultValue = [
        //         $numSheet2,
        //         $val->survey_name,
        //         $val->department,
        //         $val->regional,
        //         $val->branch,
        //         $val->group_survey,
        //         $val->nomor,
        //         $val->question_survey,
        //         $val->essay_answer,
        //         $val->total_surveyor,
        //         $val->max_point,
        //         $val->total_hit,
        //         round($val->percent, 2).' %',
        //     ];
        //     $numSheet2++;
        //     $dataSheet2[] = $resultValue; 
        // }
        // $workSheet2->fromArray($dataSheet2);
        // $sheet2 = $spreadsheet->getSheet(1)->setTitle("Move");
        // $sheet2->mergeCells('A2:E2');
        // $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        // }
        // $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet2->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet2->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        //     $sheet2->mergeCells('B'.$val.':G'.$val);
        // }
        // $lastColumn         = $sheet2->getHighestColumn();
        // $lastRow            = $sheet2->getHighestRow();
        // $columnAllData      = $lastColumn.$lastRow;
        // $sheet2->mergeCells('A2:E2');
        // $sheet2->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        // $sheet2->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');

        

        // // Sheet 3
        // $workSheet3 = new Worksheet($spreadsheet);
        // $spreadsheet->addSheet($workSheet3, 2);
        // $dataSheet3 = $headerSheet;
        // $getQ2 = DB::table(DB::raw("sp_funct_dashboard_survey_view(".$id_survey_header.", ".session('id_company').", 2) sfdsv"))->select('sfdsv.*')->get();
        // $title3     = ['No', 'Survey Name', 'Department', 'Regional', 'Branch', 'Group', 'Number', 'Question', 'Essay', 'Total Surveyor', 'Max Point', 'Total Hit', 'Percentage'];
        // $dataSheet3[] = $title3; 
        // $numSheet3 = 1;
        // $resultValue = [];
        // foreach ($getQ2 as $key => $val) {
        //     $resultValue = [
        //         $numSheet3,
        //         $val->survey_name,
        //         $val->department,
        //         $val->regional,
        //         $val->branch,
        //         $val->group_survey,
        //         $val->nomor,
        //         $val->question_survey,
        //         $val->essay_answer,
        //         $val->total_surveyor,
        //         $val->max_point,
        //         $val->total_hit,
        //         round($val->percent, 2).' %',
        //     ];
        //     $numSheet3++;
        //     $dataSheet3[] = $resultValue; 
        // }
        // $workSheet3->fromArray($dataSheet3);
        // $sheet3 = $spreadsheet->getSheet(2)->setTitle("Resign");
        // $sheet3->mergeCells('A2:E2');
        // $rowStyle = [2,15]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet3->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet3->getStyle($val.':'.$val)->getAlignment()->setHorizontal('center');
        // }
        // $rowStyle = [3,4,5,6,7,8,9,10,11,12,13]; // Identitas Row yang akan di style kan
        // foreach ($rowStyle as $k => $val) {
        //     $sheet3->getStyle($val.':'.$val)->getFont()->setBold(true);
        //     $sheet3->getStyle($val.':'.$val)->getAlignment()->setHorizontal('left');
        //     $sheet3->mergeCells('B'.$val.':G'.$val);
        // }
        // $lastColumn         = $sheet3->getHighestColumn();
        // $lastRow            = $sheet3->getHighestRow();
        // $columnAllData      = $lastColumn.$lastRow;
        // $sheet3->mergeCells('A2:E2');
        // $sheet3->getStyle('A15:'.$columnAllData)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));
        // $sheet3->getStyle('A16:'.$columnAllData)->getAlignment()->setHorizontal('left');


        // $spreadsheet->setActiveSheetIndexByName('New Hire'); // utk set sheet yg aktif
        // $worksheets = [$workSheet1, $workSheet2, $workSheet3]; // array utk kondisi nanti jika butuh bnyk sheet
        
        // foreach ($worksheets as $worksheet){
        //     foreach ($worksheet->getColumnIterator() as $column){
        //         $worksheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        //     }
        // }

        // if($exportType == 'excel'){
        //     $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        //     $writer->setIncludeCharts(true);
        //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        //     header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        //     $writer->save('php://output');
        // } else {
        //     $writer = IOFactory::createWriter($spreadsheet, 'Pdf');
        //     $writer->setIncludeCharts(true);
        //     header('Content-Type: application/pdf');
        //     header('Content-Disposition: attachment;filename="' . $filename . '.pdf"');
        //     $writer->save('php://output');
        // }
    }
	
	public function talent_profile(Request $request) {
		$global_emp = $request->global_emp;
        return view('employee.employee.employee.modal_profile', compact('global_emp'));
    }
	
	public function get_editProfile(Request $request) {
		$filePath = '';
		$data = array(
			'id_employee' => $request->id_employee,
		);
		$filePath = url('project/storage/app/public/upload/photo');
        $result = Employee::get_editProfile($data);
		$result['profile']->filePath = $filePath;
        return response()->json($result);
    }
	
	public function get_cert_profile(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_cert_profile($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_skill_profile(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_skill_profile($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
	}
	
	public function get_emp_career_history(Request $request) {
		$data = [
            'id_number' => $request->id_number
        ];	
        $result = Employee::get_emp_career_history($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_pro_position(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_pro_position($data);
		if(count($result) > 0){
			$x = explode(" - ",$result[0]->kpi_desc);
			$result[0]->kpi_desc =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));	
		}
        return response()->json($result);
    }
	
	public function get_history_talent(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $res = Employee::get_history_talent($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				$x = explode(" - ",$val->period);
				$val->period =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
				$result[] = $val;
			}	
		}
         return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_last_rating(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_last_rating($data);
        return response()->json($result);
    }
	
	public function get_old_rating(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = Employee::get_old_rating($data);
        return response()->json($result);
    }

}
