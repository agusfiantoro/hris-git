<?php

namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\Employee\Employee\Employee;
use App\Models\TalentManagement\TalentProfile\TalentProfile;
use App\Models\TalentManagement\TalentProfile\TalentCommittee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Spipu\Html2Pdf\Html2Pdf;
use PDF;
use ZipArchive;

class TalentProfileController extends Controller {

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
	
	public function get_reco(Request $request) {
		$result = TalentProfile::get_reco();
        return response()->json($result);
    }
	public function get_employee(Request $request) {
		$result = TalentProfile::get_employee($this->accessBranch($request));
        return response()->json($result);
    }
	
    public function index(Request $request) {
         if ($request->ajax()) {
        	$idReco = $request->idReco ?? null;
        	$nik = $request->nik ?? null;
			$idNik = null;
			if($nik){
				foreach($nik as $val){
					$idNik[] = "'".$val."'";
				}
			}
            $data = TalentProfile::getdata($this->accessBranch($request),$idReco,$idNik);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
								$onclickEdit = "get_profile(".$data->id_employee.",'".$data->nik_employee."')";
								$onclickPdf = "get_pdf(".$data->id_employee.",'".$data->nik_employee."','".$data->identification_number."')";
								$button = '&nbsp;&nbsp;<button type="button" name="profile" id="' . $data->id_employee . '"  onclick="'.$onclickEdit.'" class="btn btn-success btn-xs" title="Profile"><span class="far fa-id-card" style="font-size:18px;margin:3px;"></span></button>';
								$button .= '&nbsp;&nbsp;<button type="button" name="pdf" onclick="'.$onclickPdf.'" class="btn btn-success btn-xs" title="Export PDF"><span class="fas fa-file-pdf" style="font-size:18px;margin:3px;"></span></button>';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);			
        }
        return view('talent_management.talent_development.talent_profile.index');
    }
	
	public function modal_talent_profile(Request $request) {
		$global_emp = $request->global_emp;
		$global_nik = $request->global_nik;
        return view('talent_management.talent_development.talent_profile.modal_profile', compact('global_emp','global_nik'));
    }
	
	public function get_editProfile(Request $request) {
		$filePath = '';
		$data = array(
			'id_employee' => $request->id_employee,
		);
		$filePath = url('project/storage/app/public/upload/photo');
        $result = TalentProfile::get_editProfile($data);
		$result['profile']->filePath = $filePath;
        return response()->json($result);
    }
	
	public function get_cert_profile(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $result = TalentProfile::get_cert_profile($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_working(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $res = TalentProfile::get_working($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->start_year != NULL){
					$start_date =  date('M Y', strtotime($val->start_year));
				}
				else{
					$start_date = "";
				}
				if($val->end_year != NULL){
					$end_date =  date('M Y', strtotime($val->end_year));
				}
				else{
					$end_date = "";
				}
				$val->period =  $start_date." - ".$end_date;
				$result[] = $val;
			}
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_emp_career_history(Request $request) {
		$data = [
            'id_number' => $request->id_number
        ];	
        $result = TalentProfile::get_emp_career_history($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_pro_position(Request $request) {
		$data = [
            'id_employee' => $request->id_employee
        ];	
        $res = TalentProfile::get_pro_position($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				$x = explode(" - ",$val->kpi_desc);
				$val->period =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
				$result[] = $val;
			}
		}
		return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_history_talent(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
            'nik_employee' => $request->nik_employee,
            'type' => $request->type,
        ];	
        $res = TalentProfile::get_history_talent($data);
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
            'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee,
        ];	
        $result = TalentProfile::get_last_rating($data);
        return response()->json($result);
    }
	
	public function get_old_rating(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee,
        ];	
        $result = TalentProfile::get_old_rating($data);
        return response()->json($result);
    }
	
	public function get_training(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
            'nik_employee' => $request->nik_employee,
        ];	
        $res = TalentProfile::get_training($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->creation_date != null){
					$val->creation_date =  date('d M Y H:i:s', strtotime($val->creation_date));					
				}
				$result[] = $val;
			}	
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_kpi_list(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee
        ];	
        $result = TalentProfile::get_kpi_list($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_edu(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
        ];	
        $res = TalentProfile::get_edu($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				$val->start_year = $val->start_year ?  date('M Y', strtotime($val->start_year)) : '-';
				$val->end_year = $val->end_year ?  date('M Y', strtotime($val->end_year)) : '-';
				$result[] = $val;
			}	
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_award(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
            'nik_employee' => $request->nik_employee
        ];	
        $res = TalentProfile::get_award($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->reference_date != null){
					$val->reference_date =  date('d M Y', strtotime($val->reference_date));					
				}
				else{
					$val->reference_date = "-";
				}
				if($val->effective_date != null){
					$val->effective_date =  date('d M Y', strtotime($val->effective_date));					
				}
				else{
					$val->effective_date = "-";
				}
				if($val->expired_date != null){
					$val->expired_date =  date('d M Y', strtotime($val->expired_date));					
				}
				else{
					$val->expired_date = "-";
				}
				$result[] = $val;
			}	
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_sp(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee
        ];	
        $res = TalentProfile::get_sp($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->effective_date != null){
					$val->effective_date =  date('d M Y', strtotime($val->effective_date));					
				}
				else{
					$val->effective_date = "-";
				}
				if($val->expired_date != null){
					$val->expired_date =  date('d M Y', strtotime($val->expired_date));					
				}
				else{
					$val->expired_date = "-";
				}
				$result[] = $val;
			}	
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_aspiration(Request $request) {
		$data = [
            'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee
        ];	
        $res = TalentProfile::get_aspiration($data);
		$result = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->sequence == 12){
					$val->question = "Bidang / Fungsi yang diminati";
					if($val->description_answer != null){
						$val->description_answer = $val->answer." (".$val->description_answer.")";
					}
					else{
						$val->description_answer = $val->answer;
					}				
				}
				if($val->sequence == 13){
					$val->question = "Bersedia di tempatkan di luar homebase / lokasi penempatan saat ini";
					$val->description_answer = $val->answer;
				}
				if($val->sequence == 14){
					$val->question = "3 Area / Lokasi kerja yang diminat";
					$val->description_answer = $val->description_answer;
				}
				if($val->sequence == 15){
					$val->question = "Posisi / Jabatan karir yang ingin dituju dan ingin dicapai, beserta target waktu pencapaiannya";
					$val->description_answer = $val->description_answer;
				}
				$result[] = $val;
			}
		}
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_risk(Request $request) {
		$result = TalentProfile::get_risk();
        return response()->json($result);
    }
	
	public function get_plan(Request $request) {
		$result = TalentProfile::get_plan();
        return response()->json($result);
    }
	
	public function get_activity(Request $request) {
        $result = TalentProfile::get_activity($request->id);
        return response()->json($result);
    }
	
	protected function validateReq(Request $request) {
		if($request->risk){
			$arr_form_validate = [
			//	'risk.'.$request->counter.'.id_routing' => 'required',
				'risk.'.$request->counter.'.id_category' => 'required',
				'risk.'.$request->counter.'.committee_date' => 'required',
				'risk.'.$request->counter.'.id_activity' => 'required',
				'risk.'.$request->counter.'.committee_note' => 'required|string',
			];
			
			$arr_msg_form_validate = [
			//	'risk.'.$request->counter.'.id_routing.required' => 'The Projected Position field is required',
				'risk.'.$request->counter.'.id_category.required' => 'The Category field is required',
				'risk.'.$request->counter.'.committee_date.required' => 'The Committee field is required',
				'risk.'.$request->counter.'.id_activity.required' => 'The Activity field is required',
				'risk.'.$request->counter.'.committee_note.required' => 'The Committee Note field is required',
			];				
		}  
		if($request->plan){
			$arr_form_validate = [
			//	'plan.'.$request->counter.'.id_routing' => 'required',
				'plan.'.$request->counter.'.id_category' => 'required',
				'plan.'.$request->counter.'.committee_date' => 'required',
				'plan.'.$request->counter.'.id_activity' => 'required',
				'plan.'.$request->counter.'.committee_note' => 'required|string',
			];
			
			$arr_msg_form_validate = [
			//	'plan.'.$request->counter.'.id_routing.required' => 'The Projected Position field is required',
				'plan.'.$request->counter.'.id_category.required' => 'The Category field is required',
				'plan.'.$request->counter.'.committee_date.required' => 'The Committee field is required',
				'plan.'.$request->counter.'.id_activity.required' => 'The Activity field is required',
				'plan.'.$request->counter.'.committee_note.required' => 'The Committee Note field is required',
			];				
		}  
    //   dd($arr_form_validate);
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function validateSubmit(Request $request) {
	//	dd($request->all());
		$arr_form_validate = [];
		$arr_msg_form_validate = [];
		if($request->risk){
			foreach($request->post('risk') as $key=>$val_risk){
				$validate_risk = [];
				$validate_msg_risk = [];
				$validate_risk = [
				//	'risk.'.$key.'.id_routing' => 'required',
					'risk.'.$key.'.id_category' => 'required',
					'risk.'.$key.'.committee_date' => 'required',
					'risk.'.$key.'.id_activity' => 'required',
					'risk.'.$key.'.committee_note' => 'required|string',
				];
				
				$validate_msg_risk = [
				//	'risk.'.$key.'.id_routing.required' => 'The Projected Position field is required',
					'risk.'.$key.'.id_category.required' => 'The Category field is required',
					'risk.'.$key.'.committee_date.required' => 'The Committee field is required',
					'risk.'.$key.'.id_activity.required' => 'The Activity field is required',
					'risk.'.$key.'.committee_note.required' => 'The Committee Note field is required',
				];		
				$arr_form_validate = array_merge($arr_form_validate, $validate_risk);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_risk);
			}  
		}
		else{
				$validate_risk = ['table_rec_risk' => 'required|string'];
				$validate_msg_risk = ['table_rec_risk.required' => 'Table Flight Risk cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_risk);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_risk);
		}
		
		if($request->plan){
			foreach($request->post('plan') as $key=>$val_plan){
				$validate_plan = [];
				$validate_msg_plan = [];
				$validate_plan = [
				//	'plan.'.$key.'.id_routing_plan' => 'required',
					'plan.'.$key.'.id_category_plan' => 'required',
					'plan.'.$key.'.committee_date_plan' => 'required',
					'plan.'.$key.'.id_activity_plan' => 'required',
					'plan.'.$key.'.committee_note_plan' => 'required|string',
				];
				
				$validate_msg_plan = [
				//	'plan.'.$key.'.id_routing_plan.required' => 'The Projected Position field is required',
					'plan.'.$key.'.id_category_plan.required' => 'The Category field is required',
					'plan.'.$key.'.committee_date_plan.required' => 'The Committee field is required',
					'plan.'.$key.'.id_activity_plan.required' => 'The Activity field is required',
					'plan.'.$key.'.committee_note_plan.required' => 'The Committee Note field is required',
				];	
				$arr_form_validate = array_merge($arr_form_validate, $validate_plan);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_plan);
			}  
		}  
		else{
				$validate_plan = ['table_rec_plan' => 'required|string'];
				$validate_msg_plan = ['table_rec_plan.required' => 'Table Development Plan cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_plan);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_plan);
		}
    //   dd($arr_form_validate);
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	public function save(Request $request) {
		$this->validateReq($request);
		try {
			DB::beginTransaction();
		$data = New TalentCommittee();
		if($request->risk){
			$formData = $request->risk[$request->counter];
			$data -> id_employee = $request->id_employee;
			$data -> id_position_routing = $formData['id_routing'];
			$data -> id_category_profile = $formData['id_category'];
			$data -> talent_commite_date = $formData['committee_date'];
			$data -> id_activity = $formData['id_activity'];
			$data -> notes = $formData['committee_note'];
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');	
		}
		if($request->plan){
			$formData = $request->plan[$request->counter];
			$data -> id_employee = $request->id_employee;
			$data -> id_position_routing = $formData['id_routing'];
			$data -> id_category_profile = $formData['id_category'];
			$data -> talent_commite_date = $formData['committee_date'];
			$data -> id_activity = $formData['id_activity'];
			$data -> notes = $formData['committee_note'];
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
		}
		$data -> save();
		DB::commit();
			return response()->json(['status'=>'true', 'id_talent_profile_note'=>$data['id_talent_profile_note'], 'message'=>'Save Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function update(Request $request) {
	//	dd($request->all());
		$this->validateReq($request);
		try {
			DB::beginTransaction();
		$data = TalentCommittee::where('id_talent_profile_note',$request->id_talent_profile_note)->first();
		if($request->risk){
			$formData = $request->risk[$request->counter];
			$data -> id_position_routing = $formData['id_routing'];
			$data -> id_category_profile = $formData['id_category'];
			$data -> talent_commite_date = $formData['committee_date'];
			$data -> id_activity = $formData['id_activity'];
			$data -> notes = $formData['committee_note'];
			$data -> updated_by = session('id_user');	
		}
		if($request->plan){
			$formData = $request->plan[$request->counter];
			$data -> id_position_routing = $formData['id_routing'];
			$data -> id_category_profile = $formData['id_category'];
			$data -> talent_commite_date = $formData['committee_date'];
			$data -> id_activity = $formData['id_activity'];
			$data -> notes = $formData['committee_note'];
			$data -> updated_by = session('id_user');	
		}
		$data -> save();
		DB::commit();
			return response()->json(['status'=>'true', 'id_talent_profile_note'=>$data['id_talent_profile_note'], 'message'=>'Update Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function submit(Request $request) {
	//	dd($request->all());
		$this->validateSubmit($request);
	//	dd($request->all());
		try {
			DB::beginTransaction();
		if($request->risk){
			foreach($request->risk as $key=>$val_risk){
				if($val_risk['id_talent_profile_note'] != null){	
					$data = TalentCommittee::where('id_talent_profile_note',$val_risk['id_talent_profile_note'])->first();
					$data -> id_position_routing = $val_risk['id_routing'];
					$data -> id_category_profile = $val_risk['id_category'];
					$data -> talent_commite_date = $val_risk['committee_date'];
					$data -> id_activity = $val_risk['id_activity'];
					$data -> notes = $val_risk['committee_note'];
					$data -> is_submitted_flag = 1;
					$data -> updated_by = session('id_user');
				}
				else{
					$data = New TalentCommittee();
					$data -> id_employee = $request->id_employee;
					$data -> id_position_routing = $val_risk['id_routing'];
					$data -> id_category_profile = $val_risk['id_category'];
					$data -> talent_commite_date = $val_risk['committee_date'];
					$data -> id_activity = $val_risk['id_activity'];
					$data -> notes = $val_risk['committee_note'];
					$data -> is_submitted_flag = 1;
					$data -> status = 'A';
					$data -> id_company = session('id_company');
					$data -> created_by = session('id_user');	
				}
				$data -> save();
			}			
		}
		if($request->plan){
			foreach($request->plan as $key=>$val_plan){
				if($val_plan['id_talent_profile_note_plan'] != null){	
					$data = TalentCommittee::where('id_talent_profile_note',$val_plan['id_talent_profile_note_plan'])->first();
					$data -> id_position_routing = $val_plan['id_routing_plan'];
					$data -> id_category_profile = $val_plan['id_category_plan'];
					$data -> talent_commite_date = $val_plan['committee_date_plan'];
					$data -> id_activity = $val_plan['id_activity_plan'];
					$data -> notes = $val_plan['committee_note_plan'];
					$data -> is_submitted_flag = 1;
					$data -> updated_by = session('id_user');
				}
				else{
					$data = New TalentCommittee();
					$data -> id_employee = $request->id_employee;
					$data -> id_position_routing = $val_plan['id_routing_plan'];
					$data -> id_category_profile = $val_plan['id_category_plan'];
					$data -> talent_commite_date = $val_plan['committee_date_plan'];
					$data -> id_activity = $val_plan['id_activity_plan'];
					$data -> notes = $val_plan['committee_note_plan'];
					$data -> is_submitted_flag = 1;
					$data -> status = 'A';
					$data -> id_company = session('id_company');
					$data -> created_by = session('id_user');	
				}
				$data -> save();
			}			
		}
			
		DB::commit();
			return response()->json(['status'=>'true', 'message'=>'Submit Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Submit !! [' . $e->getMessage() . ']']);
		}
		
	}
	
	public function deleted(Request $request) {
		try {
			DB::beginTransaction();
			$data = TalentCommittee::where('id_talent_profile_note',$request->id_talent_profile_note)->where('is_submitted_flag',0)->first();
			$data->delete();			
		DB::commit();
			return response()->json(['status'=>'true', 'message'=>'Delete Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Delete !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function download(Request $request) {
        ini_set('max_execution_time', -1);
	//	dd($emp);
		$data = array(
			'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee,
			'id_number' => $request->id_number,
			'type' => 'P',
		);
		$data2 = array(
			'id_employee' => $request->id_employee,
			'nik_employee' => $request->nik_employee,
			'type' => 'S',
		);
		$filePath = '';		
		$filePath = url('project/storage/app/public/upload/photo');
        $result = TalentProfile::get_editProfile($data);
		$result['profile']->filePath = $filePath;
		$result['edu'] = TalentProfile::get_edu($data);
		$result['award'] = TalentProfile::get_award($data);
		$result['sp'] = TalentProfile::get_sp($data);
		$result['career'] = TalentProfile::get_emp_career_history($data);
		$res = TalentProfile::get_working($data);
		$result['working'] = [];
		if(count($res) > 0){
			foreach($res as $key=>$val){
				if($val->start_year != NULL){
					$start_date =  date('M Y', strtotime($val->start_year));
				}
				else{
					$start_date = "";
				}
				if($val->end_year != NULL){
					$end_date =  date('M Y', strtotime($val->end_year));
				}
				else{
					$end_date = "";
				}
				$val->period =  $start_date." - ".$end_date;
				$result['working'][] = $val;
			}
		}
		$result['cert'] = TalentProfile::get_cert_profile($data);
		$result['training'] = TalentProfile::get_training($data);
		$res_aspiration = TalentProfile::get_aspiration($data);
		$result['aspiration'] = [];
		if(count($res_aspiration) > 0){
			foreach($res_aspiration as $key=>$val){
				if($val->sequence == 12){
					$val->question = "Bidang / Fungsi yang diminati";
					if($val->description_answer != null){
						$val->description_answer = $val->answer." (".$val->description_answer.")";
					}
					else{
						$val->description_answer = $val->answer;
					}				
				}
				if($val->sequence == 13){
					$val->question = "Bersedia di tempatkan di luar homebase / lokasi penempatan saat ini";
					$val->description_answer = $val->answer;
				}
				if($val->sequence == 14){
					$val->question = "3 Area / Lokasi kerja yang diminat";
					$val->description_answer = $val->description_answer;
				}
				if($val->sequence == 15){
					$val->question = "Posisi / Jabatan karir yang ingin dituju dan ingin dicapai, beserta target waktu pencapaiannya";
					$val->description_answer = $val->description_answer;
				}
				$result['aspiration'][] = $val;
			}
		}
		$res_talent = TalentProfile::get_history_talent($data);
		$result['talent'] = [];
		if(count($res_talent) > 0){
			foreach($res_talent as $key=>$val){
				$x = explode(" - ",$val->period);
				$val->period =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
				$result['talent'][] = $val;
			}	
		}
		$res_successor = TalentProfile::get_history_talent($data2);
		$result['successor'] = [];
		if(count($res_successor) > 0){
			foreach($res_successor as $key=>$val){
				$x = explode(" - ",$val->period);
				$val->period =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));
				$result['successor'][] = $val;
			}	
		}
		$res_rating = TalentProfile::get_last_rating($data);
		$result['rating'] = [];
		if(count($res_rating) > 0){
			$result['rating'] = $res_rating;
		}
		else{
			$result['rating'][0] = new \stdClass;
			$result['rating'][0]->year = '-';
			$result['rating'][0]->final_rating = '-';
		}
		$res_old_rating = TalentProfile::get_old_rating($data);
		$result['old_rating'] = [];
		if(count($res_old_rating) > 1){
			$result['old_rating'] = $res_old_rating;
		}
		else if(count($res_old_rating) == 1){
			$result['old_rating'][0] = new \stdClass;
			$result['old_rating'][1] = new \stdClass;
			$result['old_rating'][0]->year = $res_old_rating[0]->year;
			$result['old_rating'][0]->final_rating = $res_old_rating[0]->final_rating;
			$result['old_rating'][1]->year = '-';
			$result['old_rating'][1]->final_rating = '-';
		}
		else{
			$result['old_rating'][0] = new \stdClass;
			$result['old_rating'][1] = new \stdClass;
			$result['old_rating'][0]->year = '-';
			$result['old_rating'][0]->final_rating = '-';
			$result['old_rating'][1]->year = '-';
			$result['old_rating'][1]->final_rating = '-';
		}
	//	dd($result['old_rating']);
		$result['kpi'] = TalentProfile::get_kpi_list($data);
		$result['flight_risk'] = TalentProfile::get_committee($data,'Flight_Risk');
		$result['talent_note'] = TalentProfile::get_committee($data,'Talent_Note');
		$html = view('talent_management.talent_development.talent_profile.download', compact('result'))->render();
		$pdf = PDF::loadHTML($html)->setPaper('A4','potrait');
		
		// DB::commit();	
		if($request->pdf == true){
			return $pdf->stream($request->nik_employee.'_'.date('dmY').'.pdf');
		}
		else if($request->zip == true){
			Storage::disk('local')->makeDirectory('public/upload/talent_pdf');
			Storage::disk('local')->makeDirectory('public/upload/talent_archives');			
			$pdf->save(storage_path('app/public/upload/talent_pdf/'.$request->nik_employee.'_'.date('dmY').'.pdf')); 
			return response()->json(['nik' => $request->nik_employee]);
		}
    }
	
	public function get_id(Request $request) {
        $result = TalentProfile::get_id($request->id_employee);
        return response()->json($result);
    }
	
	protected function deleteOldZips($dir, $second = 5) {
		$files = collect(Storage::allFiles($dir));
		$files->each(function ($file) use($second) {
			 $lastModified =  Storage::lastModified($file);
			 $lastModified = Carbon::parse($lastModified);	
			 if (Carbon::now()->gt($lastModified->addSeconds($second))) {
				 Storage::delete($file);
			 }
		 });
	
		return true;
	}
	
	public function exportZip(Request $request) {
		
		$request->validate([
			// 'id' => 'required'
		]);

		foreach($request->data as $req) {
			$data = new Request();
			$data->id_employee = $req['id_employee'];
			$data->nik_employee = $req['nik_employee'];
			$data->id_number = $req['identification_number'];
			$data->zip = true;
			// $request->id_employee,
			// $request->nik_employee,
			// $request->id_number,
			$this->download($data);
		}
		$listId = collect($request->data)->pluck('id_employee');
		
		$emp = DB::table('hr_employee')->whereIn('id_employee', $listId)->get();
		foreach($emp as $valNik){
			$x_nik[] = $valNik->nik_employee;
		}
				
		$files = 0;
		while ($files < count($x_nik)) {
			$directory = storage_path('app/public/upload/talent_pdf/');
			$files = count(glob($directory ."*" ));
		}
		$this->deleteOldZips('public/upload/talent_archives', 5);
		$zipBaseName = 'Talent_Profile_Card '.uniqid("", true).'.zip';
		$zipName = './project/storage/app/public/upload/talent_archives/'.$zipBaseName;
		$zip = new ZipArchive();
		try {
			if(!$zip->open($zipName, \ZipArchive::CREATE | \ZipArchive::OVERWRITE)) {
				throw new \Exception("Failed creating ZIP file");
			}
		} catch(\Exception $e) {
			return response()->json([
				"message" => $e->getMessage()
			], 500);
		}		
		foreach($x_nik as $arg => $val) {
			$stor = storage_path('app/public/upload/talent_pdf/'.$val.'_'.date('dmY').'.pdf');
			$zip->addFile($stor, basename($stor));
		}
		$zip->close();
		
		foreach($x_nik as $file) {
			Storage::disk('local')->delete('public/upload/talent_pdf/'.$file.'_'.date('dmY').'.pdf');
		}
		return Storage::download('public/upload/talent_archives/'.$zipBaseName, 'TalentCard_'.date('dmY').'.zip');
	}
	
}
