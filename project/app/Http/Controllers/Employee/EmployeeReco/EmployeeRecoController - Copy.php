<?php

namespace App\Http\Controllers\Employee\EmployeeReco;

use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DataTables;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\Employee\Employee\Employee;
use App\Models\Employee\EmployeeReco\EmployeeReco;
use App\Models\Employee\EmployeeReco\EmployeeRecoQuantitative;
use App\Models\Employee\EmployeeReco\EmployeeRecoQualitative;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Organization\OrganizationStructure\JobPositionDetail;
use App\Models\Employee\EmployeeApproval\EmployeeApproval;
use App\Models\Kpi\Kpi\QualitativeParticipant;
use App\Models\Kpi\Kpi\QualitativeAppraiser;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Spipu\Html2Pdf\Html2Pdf;
use PDF;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Carbon\Carbon;

class EmployeeRecoController extends Controller
{
	
	public function index(Request $request) {
        if ($request->ajax()) {			
            $data = EmployeeReco::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {		
								$onclick = "loadedit(".$data->id_recommendation_header.",'edit',null)";
								$onview = "loadedit(".$data->id_recommendation_header.",'view',null)";
								$onclickPdf = "get_pdf(".$data->id_recommendation_header.")";
								$button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
								$button .= '&nbsp;<button type="button" name="view" onclick="'.$onview.'" class="view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';
								$button .= '&nbsp;<button type="button" name="cancel" id="' . $data->id_recommendation_header . '" class="cancel btn btn-danger btn-sm" title="Cancel"><span class="fa fa-close"></span></button>';
								$button .= ' <button type="button" target="_blank" name="print" onclick="'.$onclickPdf.'" class="print btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></button> ';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee.employee_reco.index');
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
            $data = EmployeeReco::getdata_summary($group_branch);
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {		
								$onview = "loadedit(".$data->id_recommendation_header.",'view','summary')";
								$onclickPdf = "get_pdf(".$data->id_recommendation_header.")";
								$button = '&nbsp;<button type="button" name="view" onclick="'.$onview.'" class="view btn btn-warning btn-sm" title="View"><span class="fas fa-eye" style="color:white;"></span></button>';								
								$button .= ' <button type="button" target="_blank" name="print" onclick="'.$onclickPdf.'" class="print btn btn-success btn-sm btn-print" title="Print"><span class="fa fa-file-pdf"></span></button> ';
								return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('employee.employee.employee_reco.index_summary');
    }
	
	public function modal_detail(Request $request) {
		$id_recommendation_header = $request->id_recommendation_header;
		$type = $request->type;
		$sum = $request->sum;
        return view('employee.employee.employee_reco.modal_detail', compact('id_recommendation_header','type','sum'));
    }
	
	public function get_hierachy(Request $request) {
		if($request->type != 'view'){
			$idUser = session('id_user');
		}
		else{
			$idView = EmployeeReco::where('id_recommendation_header',$request->id_recommendation_header)->first();
			$idUser = $idView->created_by;
		}
		$req = EmployeeReco::get_user_req($idUser);
		$data = [
            'code' => 'Form_Reco'
        ];		
        $result = ApprovalTransaction::get_hierachy($data,$request->id_location);
		$combine = EmployeeReco::get_app_combine($req[0]->id_employee,session('id_company'),$result[0]->id);
		$x = null;
		foreach($combine as $key=>$val){
			if($val->sequence == 1){
				$emp = Employee::where('id_employee', $val->id_employee)->first();
				$x[] = $emp->name;
			}
		}
		foreach($result as $key=>$val){
			if($x != null){
				$result[$key]->text = $val->text." (".$x[0].")";
			}
			else{
				$result[$key]->text = $val->text;
			}
		}
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_employee(Request $request) {
        $result = EmployeeReco::get_employee($request->id_employee,$request->type);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_detail_employee(Request $request) {
        $data = [
            'id_employee' => $request->id_employee,
            'emp_status_code' => $request->emp_status_code,
        ];	
        $result = EmployeeReco::get_detail_employee($data);
        return response()->json($result);
    }
	
	public function get_approval_status() {		
        $result = EmployeeReco::get_approval_status();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_category() {		
        $result = EmployeeReco::get_category();
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_type(Request $request) {
        $data = [
            'code' => $request->code,
        ];	
        $result = EmployeeReco::get_type($data);
        return response()->json($result);
    }
	
	public function get_new_position(Request $request) {
        $result = EmployeeReco::get_new_position();
        return response()->json($result);
    }
	
	public function checkpos(Request $request){
		if ($request->ajax()) {
			$route = EmployeeReco::JobDetail($request->jobid,$request->jobidLoc,$request->jobidPrincipal);
		//	$route = JobPositionDetail::where('id_position_routing',$request->jobid)->where('id_location',$request->jobidLoc)->where('status','A')->first();
			$data = [
				'jobid' => $route->id_position_detail,
				'jobidPrincipal' => $request->jobidPrincipal,
			];
			$result = EmployeeReco::browse_check($data);
		 }
       return response()->json(['result' => $result]);
	}
	 
	public function get_new_status(Request $request) {
        $result = EmployeeReco::get_new_status();
        return response()->json($result);
    }
	
	public function get_edit(Request $request) {
        $data = [
            'id_recommendation_header' => $request->id_recommendation_header
        ];
		$result = EmployeeReco::get_edit($data);

        return response()->json($result);
    }
	
	public function get_sumQualitative(Request $request) {
		$data = [
            'id_employee_participant' => $request->id_employee_participant,
            'id_recommendation_header' => $request->id_recommendation_header
        ];	
        $result = EmployeeReco::get_sumQualitative($data);
        return DataTables::of($result)
								->addIndexColumn()
								->make(true);
    }
	
	public function get_appraiser(Request $request) {
        $result = EmployeeReco::get_appraiser();
        return response()->json($result);
    }
	
	public function get_decision(Request $request) {
        $result = EmployeeReco::get_decision();
        return response()->json($result);
    }
	
	public function get_new_mgr(Request $request){
		$data = [
			'id_position_detail' => $request->id_position_detail,
		];
		$result = EmployeeReco::get_new_mgr($data);
       return response()->json($result);
	}
	
	public function get_kpi_detail(Request $request){
		try{
			DB::beginTransaction();
			$data = [
				'id_employee' => $request->id_employee,
				'date' => date('Y-m-d'),
			];
			$result = [];
			$res = EmployeeReco::get_kpi_detail($data);
			foreach($res as $key=>$val){
				if($val->_1_month_ago != null ){
					$val->_1_month_ago = explode(",",$val->_1_month_ago);
				}
				if($val->_2_month_ago != null ){
					$val->_2_month_ago = explode(",",$val->_2_month_ago);
				}
				if($val->_3_month_ago != null ){
					$val->_3_month_ago = explode(",",$val->_3_month_ago);
				}
				if($val->_4_month_ago != null ){
					$val->_4_month_ago = explode(",",$val->_4_month_ago);
				}
				if($val->_5_month_ago != null ){
					$val->_5_month_ago = explode(",",$val->_5_month_ago);
				}
				if($val->_6_month_ago != null ){
					$val->_6_month_ago = explode(",",$val->_6_month_ago);
				}
				$result[] = $val;
			}
			DB::commit();
			return response()->json(['status'=>'true', 'data'=>$result, 'message'=>'Generate Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);
		}
	}
	
	protected function validateReq(Request $request) {
        $arr_form_validate = [
			'reference_number' => 'unique:hr_recommendation_header', Rule::unique('hr_recommendation_header')->where(function ($query) {
                        return $query->where('id_company', session('id_company'));
                    }),
            'id_employee' => 'required',
            'id_approval' => 'required',
            'id_transition_category' => 'required',
            'id_transition_type' => 'required',
            'effective_date' => 'required|date',
        ];
		
		if($request->bobot_one != null){
			 $arr_form_validate['bobot_one'] = 'numeric|max:100';
		}
		if($request->bobot_two != null){
			 $arr_form_validate['bobot_two'] = 'numeric|max:100';
		}
		if($request->bobot_three != null){
			 $arr_form_validate['bobot_three'] = 'numeric|max:100';
		}
		if($request->bobot_four != null){
			 $arr_form_validate['bobot_four'] = 'numeric|max:100';
		}
		if($request->bobot_five != null){
			 $arr_form_validate['bobot_five'] = 'numeric|max:100';
		}
		if($request->bobot_six != null){
			 $arr_form_validate['bobot_six'] = 'numeric|max:100';
		}
	
        $arr_msg_form_validate = [
            'id_employee.required' => 'The Name field is required',
            'id_approval.required' => 'The Hierarchy Approval field is required (Contact Administrator)',
            'id_transition_category.required' => 'The Category field is required',
            'id_transition_type.required' => 'The Type field is required',
            'effective_date.required' => 'The Effective Date field is required',
            'bobot_one.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
            'bobot_two.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
            'bobot_three.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
            'bobot_four.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
            'bobot_five.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
            'bobot_six.max' => 'Total Bobot Tidak Boleh Lebih dari 100',
        ];
		
		$type = MasterGeneralData::where('id_general_data',$request->id_transition_type)->where('id_company', session('id_company'))->first();
		if($type){
			if($type->code == 'Movement'){
				if($type->description == 'Demotion' || $type->description == 'Mutation' || $type->description == 'Promotion' || $type->description == 'Relocation' || $type->description == 'Rotation' || $type->description == 'Temporary Assignment'){
					$arr_form_validate['new_position_routing'] = 'required';
					$arr_msg_form_validate['new_position_routing.required'] = 'The New Position field is required';
				}
			}
		}
		
		if($request->with_reco == "on"){
			$arr_form_validate['quanti.*.desc_kpi'] = 'required';
			$arr_msg_form_validate['quanti.*.desc_kpi.required'] = 'The Measurement field is required';			
			if ($request->post('quanti') == null) {
				$validate_emprequest = ['table_rec_quanti' => 'required|string'];
				$validate_msg_emprequest = ['table_rec_quanti.required' => 'Quantitative Review cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
			}
			
			$arr_form_validate['quali.*.id_appraiser'] = 'required';
			$arr_msg_form_validate['quali.*.id_appraiser.required'] = 'The Appraiser field is required';			
			if ($request->post('quali') == null) {
				$validate_emprequest = ['table_rec_quali' => 'required|string'];
				$validate_msg_emprequest = ['table_rec_quali.required' => 'Qualitative Review cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
			}
			
			$arr_form_validate['notes_summary'] = 'required|string';
			$arr_form_validate['decision'] = 'required';
			$arr_msg_form_validate['notes_summary.required'] = 'The Overall Review field is required';	
			$arr_msg_form_validate['decision.required'] = 'The Decision field is required';	
			if ($request->notes_summary == null || $request->decision == null) {
				$validate_emprequest = ['tab_summary' => 'required|string'];
				$validate_msg_emprequest = ['tab_summary.required' => 'Summary Review cannot empty'];
				$arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
				$arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
			}
			
		}
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
		
	public function save(Request $request) {
	//	dd('Save');
		$this->validateReq($request);
		try {
			DB::beginTransaction();
			$kode = EmployeeReco::getkode();
			$emp = EmployeeApproval::get_session_emp();
			if(!is_null($emp)){
				$emp_id = $emp['id_employee'];
			}
			else{
				$emp_id = 1;
			}
			$data = New EmployeeReco();
			$data -> reference_number = $kode;
			$data -> id_employee = $request->id_employee;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_employment_status = $request->id_employment_status;			
			$data -> period_evaluation = $request->month_period;
			if($request->start_date == 'NaN-NaN-NaN'){
				$data -> start_date = null;
			}
			else{
				$data -> start_date = $request->start_date;
			}
			
			if($request->end_date == 'NaN-NaN-NaN'){
				$data -> end_date = null;
			}
			else{
				$data -> end_date = $request->end_date;
			}
				
			$data -> effective_date = $request->effective_date;
			
			if($request->expired_date == 'NaN-NaN-NaN'){
				$data -> expired_date = null;
			}
			else{
				$data -> expired_date = $request->expired_date;
			}
			
			if($request->duration == 3 || $request->duration == 6){
				$data -> period_type = $request->duration;
			}
			else{
				$data -> period_type = null;
			}
			$data -> id_transition_category = $request->id_transition_category;
			$data -> id_transition_type = $request->id_transition_type;
			$data -> id_new_chief_employee = $request->id_new_mgr;
			$data -> id_new_position_detail = $request->id_new_position_detail;
			$data -> reco_flag = isset($request->with_reco) == "on" ? 1 : 0;
			$data -> status = 'A';
			$data -> id_company = session('id_company');
			$data -> created_by = session('id_user');
			
			if(isset($request->id_approval)){
				if($request->form == 'save_and_draft'){
					$app_status = 'New';
				}
				else{
					$app_status = 'Request_Approval';
				}
				$app = EmployeeReco::get_approval($request->id_approval,$app_status);
				$data -> id_approval_hierarchy = $request->id_approval;
				$data -> id_approval_status = $app[0]->id_approval_status;
				if(isset($request->with_reco)){
					$data -> id_decision_recommendation = $request->decision;
					$data -> notes = $request->notes_summary;
				}
				$data -> save();
				$at = [];
				if($request->form == 'save_and_submit'){	
					if($app[0]->hierarchy_type == "Combine"){
						$approve = EmployeeReco::get_app_combine($emp_id,session('id_company'),$request->id_approval);
					}
					foreach ($approve as $key => $value) {
						$source[$key]['id_source_transaction'] = $data->id_recommendation_header;
						$source[$key]['source_transaction_type'] = $app[0]->code;
						$source[$key]['id_approval'] = $app[0]->id_approval;
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
							'sequence' => $value['sequence'],
							'id_employee_approval' => $value['id_employee_approval'],
							'id_approval_status' => $value['id_approval_status'],
							'id_approval_mode' => $value['id_approval_mode'],
							'id_position_detail' => $value['id_position_detail'],
							'id_company' => session('id_company'),
							'created_by' => session('id_user'),
						 );
						$at[] = ApprovalTransaction::create($form_trans);							
					}
				}
				if(isset($request->with_reco)){
					if(isset($request->quanti)){
						$kpi_group = EmployeeReco::kpi_group();
						foreach ($request->quanti as $key => $value) {
							$dataQuanti = New EmployeeRecoQuantitative();
							$dataQuanti -> id_recommendation_header = $data->id_recommendation_header;
							$dataQuanti -> id_kpi_category = $kpi_group['kpi_category'][0]->id_kpi_category;
							$dataQuanti -> id_kpi_type = $kpi_group['kpi_type'][0]->id_kpi_type;
							$dataQuanti -> description = $value['desc_kpi'];
							
							$dataQuanti -> weight_prosentase_kpi_1_month_ago = $value['weight_1'];
							$dataQuanti -> target_kpi_1_month_ago = $value['obj_1'];
							$dataQuanti -> kpi_1_month_ago = $value['ach_1'];
							$dataQuanti -> index_kpi_1_month_ago = $value['idx_1'];
							
							$dataQuanti -> weight_prosentase_kpi_2_month_ago = $value['weight_2'];
							$dataQuanti -> target_kpi_2_month_ago = $value['obj_2'];
							$dataQuanti -> kpi_2_month_ago = $value['ach_2'];
							$dataQuanti -> index_kpi_2_month_ago = $value['idx_2'];
							
							$dataQuanti -> weight_prosentase_kpi_3_month_ago = $value['weight_3'];
							$dataQuanti -> target_kpi_3_month_ago = $value['obj_3'];
							$dataQuanti -> kpi_3_month_ago = $value['ach_3'];
							$dataQuanti -> index_kpi_3_month_ago = $value['idx_3'];
							
							$dataQuanti -> weight_prosentase_kpi_4_month_ago = $value['weight_4'];
							$dataQuanti -> target_kpi_4_month_ago = $value['obj_4'];
							$dataQuanti -> kpi_4_month_ago = $value['ach_4'];
							$dataQuanti -> index_kpi_4_month_ago = $value['idx_4'];
							
							$dataQuanti -> weight_prosentase_kpi_5_month_ago = $value['weight_5'];
							$dataQuanti -> target_kpi_5_month_ago = $value['obj_5'];
							$dataQuanti -> kpi_5_month_ago = $value['ach_5'];
							$dataQuanti -> index_kpi_5_month_ago = $value['idx_5'];
							
							$dataQuanti -> weight_prosentase_kpi_6_month_ago = $value['weight_6'];
							$dataQuanti -> target_kpi_6_month_ago = $value['obj_6'];
							$dataQuanti -> kpi_6_month_ago = $value['ach_6'];
							$dataQuanti -> index_kpi_6_month_ago = $value['idx_6'];
							$dataQuanti -> id_company = session('id_company');
							$dataQuanti -> created_by = session('id_user');
							$dataQuanti -> save();
						}
						$updateQuanti = EmployeeReco::where('id_recommendation_header',$data->id_recommendation_header)->first();
						$updateQuanti -> average_kpi_1_month_ago = $request->tot_one;
						$updateQuanti -> average_kpi_2_month_ago = $request->tot_two;
						$updateQuanti -> average_kpi_3_month_ago = $request->tot_three;
						$updateQuanti -> average_kpi_4_month_ago = $request->tot_four;
						$updateQuanti -> average_kpi_5_month_ago = $request->tot_five;
						$updateQuanti -> average_kpi_6_month_ago = $request->tot_six;
						
						$updateQuanti -> kpi_average_ap6m = $request->total_kpi;
						$updateQuanti -> save();
						
					}
					if(isset($request->quali)){
						 $dataParticipant = New QualitativeParticipant();
						 $dataParticipant -> id_recommendation_header = $data->id_recommendation_header;
						 $dataParticipant -> id_employee_participant = $request->id_employee;
						 $dataParticipant -> id_period = null;
						 $dataParticipant -> transaction_type = 'RECO';
						 $dataParticipant -> id_company = session('id_company');
						 $dataParticipant -> created_by = session('id_user');
						 $dataParticipant -> save();

						foreach ($request->quali as $key => $value) {
							$dataQuali = New EmployeeRecoQualitative();
							$dataQuali -> id_recommendation_header = $data->id_recommendation_header;
							$dataQuali -> id_employee_appraisers = $value['id_appraiser'];
							$dataQuali -> id_company = session('id_company');
							$dataQuali -> created_by = session('id_user');
							$dataQuali -> save();
							
							$dataAppraiser = New QualitativeAppraiser();
							$dataAppraiser -> id_employee_appraisers =  $value['id_appraiser'];
							$dataAppraiser -> id_period =  null;
							$dataAppraiser -> id_employee_participant =  $request->id_employee;
							$dataAppraiser -> appraisers_hierarchy =  $value['appraisers_hierarchy'];
							$dataAppraiser -> transaction_type =  'RECO';
							$dataAppraiser -> id_recommendation_qualitative =  $dataQuali->id_recommendation_qualitative;
							$dataAppraiser -> id_company =  session('id_company');
							$dataAppraiser -> created_by =  session('id_user');
							$dataAppraiser -> save();
						}																		
					}
					
				}
				if($request->form == 'save_and_submit'){
					$re = EmployeeReco::get_mail_approve($data->id_recommendation_header);
					$res = collect($re);
					$type_submit = 'save_and_submit';
					$posDetail = EmployeeReco::posDetail($request->id_position_detail,$request->id_new_position_detail);
					$res['old_branch'] = $posDetail->old_branch;
					$res['old_region'] = $posDetail->old_region;
					$res['old_principal'] = $posDetail->old_principal;
					if($request->id_new_position_detail == null){
						$res['new_branch'] = '-';
						$res['new_region'] = '-';
						$res['new_principal'] = '-';
					}
					else{
						$res['new_branch'] = $posDetail->new_branch;
						$res['new_region'] = $posDetail->new_region;
						$res['new_principal'] = $posDetail->new_principal;
					}
				}
				else if($request->form == 'save_and_draft'){
					$re = EmployeeReco::get_mail_quali($data->id_recommendation_header);
					$res = collect($re);
					$type_submit = 'save_and_draft';
				}
				else{
					$res = null;
					$type_submit = null;
				}
			}
			
		DB::commit();
			return response()->json(['status'=>'true', 'data'=>$res, 'trans'=>$at, 'type_submit'=>$type_submit, 'message'=>'Submit Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Submit !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function update(Request $request) {
	//	dd($request->all());		
		$this->validateReq($request);		
		try {
			DB::beginTransaction();
			$emp = EmployeeApproval::get_session_emp();
			if(!is_null($emp)){
				$emp_id = $emp['id_employee'];
			}
			else{
				$emp_id = 1;
			}
			$data = EmployeeReco::where('id_recommendation_header',$request->id_recommendation_header)->first();
			$data -> id_employee = $request->id_employee;
			$data -> id_position_detail = $request->id_position_detail;
			$data -> id_employment_status = $request->id_employment_status;			
			$data -> period_evaluation = $request->month_period;
			if($request->start_date == 'NaN-NaN-NaN'){
				$data -> start_date = null;
			}
			else{
				$data -> start_date = $request->start_date;
			}
			
			if($request->end_date == 'NaN-NaN-NaN'){
				$data -> end_date = null;
			}
			else{
				$data -> end_date = $request->end_date;
			}			
			$data -> effective_date = $request->effective_date;
			
			if($request->expired_date == 'NaN-NaN-NaN'){
				$data -> expired_date = null;
			}
			else{
				$data -> expired_date = $request->expired_date;
			}
			if($request->duration == 3 || $request->duration == 6){
				$data -> period_type = $request->duration;
			}
			else{
				$data -> period_type = null;
			}
			$data -> id_transition_category = $request->id_transition_category;
			$data -> id_transition_type = $request->id_transition_type;
			$data -> id_new_chief_employee = $request->id_new_mgr;
			$data -> id_new_position_detail = $request->id_new_position_detail;
			$data -> reco_flag = isset($request->with_reco) == "on" ? 1 : 0;
			$data -> updated_by = session('id_user');
			
			if($request->form == 'save_and_draft'){
				$app_status = 'New';
			}
			else{
				$app_status = 'Request_Approval';
			}
			$app = EmployeeReco::get_approval($request->id_approval,$app_status);
			$data -> id_approval_hierarchy = $request->id_approval;
			$data -> id_approval_status = $app[0]->id_approval_status;
			if(isset($request->with_reco)){
				$data -> id_decision_recommendation = $request->decision;
				$data -> notes = $request->notes_summary;
			}
			$data -> save();
			
			$at = [];
			if($request->form == 'save_and_submit'){		
				$rev = MasterGeneralData::where('id_general_data',$request->id_approval_status)->where('id_company',session('id_company'))->first();
				if($rev->code == 'Revised'){
					$apptrans = ApprovalTransaction::where('id_source_transaction', $request->id_recommendation_header)->where('source_transaction_type', 'Form_Reco')->delete();
				}
				if($app[0]->hierarchy_type == "Combine"){
					$approve = EmployeeReco::get_app_combine($emp_id,session('id_company'),$request->id_approval);
				}
				foreach ($approve as $key => $value) {
					$source[$key]['id_source_transaction'] = $data->id_recommendation_header;
					$source[$key]['source_transaction_type'] = $app[0]->code;
					$source[$key]['id_approval'] = $app[0]->id_approval;
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
						'sequence' => $value['sequence'],
						'id_employee_approval' => $value['id_employee_approval'],
						'id_approval_status' => $value['id_approval_status'],
						'id_approval_mode' => $value['id_approval_mode'],
						'id_position_detail' => $value['id_position_detail'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					 );
					$at[] = ApprovalTransaction::create($form_trans);							
				}
			}
			$listIdQuanti    = [];
			$idQuanti     = [];
			$listIdQuali    = [];
			$idQuali     = [];
			$listIdAppraisal    = [];
			
			if(EmployeeRecoQuantitative::where('id_recommendation_header', $request->id_recommendation_header)->first() != null){
				$listIdQuanti = EmployeeRecoQuantitative::where('id_recommendation_header', $request->id_recommendation_header)->where('id_company', session('id_company'))->get()->pluck('id_recommendation_quantitative')->all();
			}
			if(EmployeeRecoQualitative::where('id_recommendation_header', $request->id_recommendation_header)->first() != null){
				$listIdQuali = EmployeeRecoQualitative::where('id_recommendation_header', $request->id_recommendation_header)->where('id_company', session('id_company'))->get()->pluck('id_recommendation_qualitative')->all();
			}
			if(isset($request->with_reco)){
				if($request->quanti) {
					$kpi_group = EmployeeReco::kpi_group();
					foreach ($request->quanti as $key => $value) {
						if ($value['id_recommendation_quantitative'] == "") {							
							$dataQuanti = New EmployeeRecoQuantitative();
							$dataQuanti -> id_recommendation_header = $request->id_recommendation_header;
							$dataQuanti -> id_kpi_category = $kpi_group['kpi_category'][0]->id_kpi_category;
							$dataQuanti -> id_kpi_type = $kpi_group['kpi_type'][0]->id_kpi_type;
							$dataQuanti -> description = $value['desc_kpi'];
							
							$dataQuanti -> weight_prosentase_kpi_1_month_ago = $value['weight_1'];
							$dataQuanti -> target_kpi_1_month_ago = $value['obj_1'];
							$dataQuanti -> kpi_1_month_ago = $value['ach_1'];
							$dataQuanti -> index_kpi_1_month_ago = $value['idx_1'];
							
							$dataQuanti -> weight_prosentase_kpi_2_month_ago = $value['weight_2'];
							$dataQuanti -> target_kpi_2_month_ago = $value['obj_2'];
							$dataQuanti -> kpi_2_month_ago = $value['ach_2'];
							$dataQuanti -> index_kpi_2_month_ago = $value['idx_2'];
							
							$dataQuanti -> weight_prosentase_kpi_3_month_ago = $value['weight_3'];
							$dataQuanti -> target_kpi_3_month_ago = $value['obj_3'];
							$dataQuanti -> kpi_3_month_ago = $value['ach_3'];
							$dataQuanti -> index_kpi_3_month_ago = $value['idx_3'];
							
							$dataQuanti -> weight_prosentase_kpi_4_month_ago = $value['weight_4'];
							$dataQuanti -> target_kpi_4_month_ago = $value['obj_4'];
							$dataQuanti -> kpi_4_month_ago = $value['ach_4'];
							$dataQuanti -> index_kpi_4_month_ago = $value['idx_4'];
							
							$dataQuanti -> weight_prosentase_kpi_5_month_ago = $value['weight_5'];
							$dataQuanti -> target_kpi_5_month_ago = $value['obj_5'];
							$dataQuanti -> kpi_5_month_ago = $value['ach_5'];
							$dataQuanti -> index_kpi_5_month_ago = $value['idx_5'];
							
							$dataQuanti -> weight_prosentase_kpi_6_month_ago = $value['weight_6'];
							$dataQuanti -> target_kpi_6_month_ago = $value['obj_6'];
							$dataQuanti -> kpi_6_month_ago = $value['ach_6'];
							$dataQuanti -> index_kpi_6_month_ago = $value['idx_6'];
							$dataQuanti -> id_company = session('id_company');
							$dataQuanti -> created_by = session('id_user');
							$dataQuanti -> save();
		
						}
						else {
							$idQuanti[] = $value['id_recommendation_quantitative'];
							$dataQuanti = EmployeeRecoQuantitative::where('id_recommendation_quantitative', $value['id_recommendation_quantitative'])->first();
							$dataQuanti -> id_kpi_category = $kpi_group['kpi_category'][0]->id_kpi_category;
							$dataQuanti -> id_kpi_type = $kpi_group['kpi_type'][0]->id_kpi_type;
							$dataQuanti -> description = $value['desc_kpi'];
							
							$dataQuanti -> weight_prosentase_kpi_1_month_ago = $value['weight_1'];
							$dataQuanti -> target_kpi_1_month_ago = $value['obj_1'];
							$dataQuanti -> kpi_1_month_ago = $value['ach_1'];
							$dataQuanti -> index_kpi_1_month_ago = $value['idx_1'];
							
							$dataQuanti -> weight_prosentase_kpi_2_month_ago = $value['weight_2'];
							$dataQuanti -> target_kpi_2_month_ago = $value['obj_2'];
							$dataQuanti -> kpi_2_month_ago = $value['ach_2'];
							$dataQuanti -> index_kpi_2_month_ago = $value['idx_2'];
							
							$dataQuanti -> weight_prosentase_kpi_3_month_ago = $value['weight_3'];
							$dataQuanti -> target_kpi_3_month_ago = $value['obj_3'];
							$dataQuanti -> kpi_3_month_ago = $value['ach_3'];
							$dataQuanti -> index_kpi_3_month_ago = $value['idx_3'];
							
							$dataQuanti -> weight_prosentase_kpi_4_month_ago = $value['weight_4'];
							$dataQuanti -> target_kpi_4_month_ago = $value['obj_4'];
							$dataQuanti -> kpi_4_month_ago = $value['ach_4'];
							$dataQuanti -> index_kpi_4_month_ago = $value['idx_4'];
							
							$dataQuanti -> weight_prosentase_kpi_5_month_ago = $value['weight_5'];
							$dataQuanti -> target_kpi_5_month_ago = $value['obj_5'];
							$dataQuanti -> kpi_5_month_ago = $value['ach_5'];
							$dataQuanti -> index_kpi_5_month_ago = $value['idx_5'];
							
							$dataQuanti -> weight_prosentase_kpi_6_month_ago = $value['weight_6'];
							$dataQuanti -> target_kpi_6_month_ago = $value['obj_6'];
							$dataQuanti -> kpi_6_month_ago = $value['ach_6'];
							$dataQuanti -> index_kpi_6_month_ago = $value['idx_6'];
							$dataQuanti -> id_company = session('id_company');
							$dataQuanti -> updated_by = session('id_user');
							$dataQuanti -> save();
						}
					}
						$updateQuanti = EmployeeReco::where('id_recommendation_header',$request->id_recommendation_header)->first();
						$updateQuanti -> average_kpi_1_month_ago = $request->tot_one;
						$updateQuanti -> average_kpi_2_month_ago = $request->tot_two;
						$updateQuanti -> average_kpi_3_month_ago = $request->tot_three;
						$updateQuanti -> average_kpi_4_month_ago = $request->tot_four;
						$updateQuanti -> average_kpi_5_month_ago = $request->tot_five;
						$updateQuanti -> average_kpi_6_month_ago = $request->tot_six;
						
						$updateQuanti -> kpi_average_ap6m = $request->total_kpi;
						$updateQuanti -> save();
				}
				$diffQuanti = array_diff($listIdQuanti, $idQuanti);
				if(count($diffQuanti) > 0){
					foreach ($diffQuanti as $key => $value) { 
						EmployeeRecoQuantitative::where('id_recommendation_quantitative', $value)->delete();
					}
				}
				
				if($request->quali) {
					foreach ($request->quali as $key => $value) {																		 
						if ($value['id_recommendation_qualitative'] == "") {
							$dataQuali = New EmployeeRecoQualitative();
							$dataQuali -> id_recommendation_header = $request->id_recommendation_header;
							$dataQuali -> id_employee_appraisers = $value['id_appraiser'];
							$dataQuali -> id_company = session('id_company');
							$dataQuali -> created_by = session('id_user');
							$dataQuali -> save();
							
							$dataAppraiser = New QualitativeAppraiser();
							$dataAppraiser -> id_employee_appraisers =  $value['id_appraiser'];
							$dataAppraiser -> id_period =  null;
							$dataAppraiser -> id_employee_participant =  $request->id_employee;
							$dataAppraiser -> appraisers_hierarchy =  $value['appraisers_hierarchy'];
							$dataAppraiser -> transaction_type =  'RECO';
							$dataAppraiser -> id_recommendation_qualitative =  $dataQuali->id_recommendation_qualitative;
							$dataAppraiser -> id_company =  session('id_company');
							$dataAppraiser -> created_by =  session('id_user');
							$dataAppraiser -> save();
						}
						else {
							$idQuali[] = $value['id_recommendation_qualitative'];
							$dataQuali = EmployeeRecoQualitative::where('id_recommendation_qualitative', $value['id_recommendation_qualitative'])->first();
							$dataQuali -> id_employee_appraisers = $value['id_appraiser'];
							$dataQuali -> id_company = session('id_company');
							$dataQuali -> updated_by = session('id_user');
							$dataQuali -> save();
							
							$dataAppraiser = QualitativeAppraiser::where('id_recommendation_qualitative', $value['id_recommendation_qualitative'])->where('id_employee_participant', $request->id_employee)->first();
							$dataAppraiser -> id_employee_appraisers =  $value['id_appraiser'];
							$dataAppraiser -> appraisers_hierarchy =  $value['appraisers_hierarchy'];
							$dataAppraiser -> id_company =  session('id_company');
							$dataAppraiser -> updated_by =  session('id_user');
							$dataAppraiser -> save();
						}
					}		
				}
				$diffQuali = array_diff($listIdQuali, $idQuali);
				if(count($diffQuali) > 0){
					foreach ($diffQuali as $key => $value) { 
						EmployeeRecoQualitative::where('id_recommendation_qualitative', $value)->delete();
						QualitativeAppraiser::where('id_recommendation_qualitative', $value)->where('id_employee_participant', $request->id_employee)->delete();
					}
				}
			}
			if($request->form == 'save_and_submit'){
				$re = EmployeeReco::get_mail_approve($request->id_recommendation_header);
				$res = collect($re);
				$type_submit = 'save_and_submit';
				$posDetail = EmployeeReco::posDetail($request->id_position_detail,$request->id_new_position_detail);
				$res['old_branch'] = $posDetail->old_branch;
				$res['old_region'] = $posDetail->old_region;
				$res['old_principal'] = $posDetail->old_principal;
				if($request->id_new_position_detail == null){
					$res['new_branch'] = '-';
					$res['new_region'] = '-';
					$res['new_principal'] = '-';
				}
				else{
					$res['new_branch'] = $posDetail->new_branch;
					$res['new_region'] = $posDetail->new_region;
					$res['new_principal'] = $posDetail->new_principal;
				}
			}
			else if($request->form == 'save_and_draft'){
				$re = EmployeeReco::get_mail_quali($request->id_recommendation_header);
				$res = collect($re);
				$type_submit = 'save_and_draft';
			}
			else{
				$res = null;
				$type_submit = null;
			}
						
		DB::commit();
			return response()->json(['status'=>'true', 'data'=>$res, 'trans'=>$at, 'type_submit'=>$type_submit, 'message'=>'Update Successfully !!']);
		} catch (Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update !! [' . $e->getMessage() . ']']);
		}
	}
	
	public function cancel($id) {
		try{
			DB::beginTransaction();
		$id_approval = EmployeeReco::where('id_recommendation_header', $id)->first();	
		$cancel = EmployeeReco::cancel();
        EmployeeReco::where('id_recommendation_header', $id)->update(array(
				'id_approval_status' => $cancel->id_general_data,
			));
		ApprovalTransaction::where('id_source_transaction', $id)->where('source_transaction_type', 'Form_Reco')->update(array(
			'id_approval_status' => $cancel->id_general_data,
			// 'updated_by' => session('id_user'),
		));	
		DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
	
	public function download(Request $request) {
        ini_set('max_execution_time', -1);
		$data = array(
			'id_recommendation_header' => $request->id_recommendation_header,
		);
        $result = EmployeeReco::get_pdf($data);
		$date = Carbon::parse($result['creation_date']);
		$result['created_date']= $date->translatedFormat('d F Y');
		if($result['reco_flag'] == true){
			for ($i = 1; $i <= 6; $i++) {
				$x[] = $date->subMonths(1)->translatedFormat('M y');
			}
			foreach($result['quanti'] as $val){
				$b_1[] = $val['weight_1'];
				$b_2[] = $val['weight_2'];
				$b_3[] = $val['weight_3'];
				$b_4[] = $val['weight_4'];
				$b_5[] = $val['weight_5'];
				$b_6[] = $val['weight_6'];
			}
			if(array_sum($b_1) != 0){
				$result['b_1'] = array_sum($b_1);
			}
			else{
				$result['b_1'] = "";
			}
			if(array_sum($b_2) != 0){
				$result['b_2'] = array_sum($b_2);
			}
			else{
				$result['b_2'] = "";
			}
			if(array_sum($b_3) != 0){
				$result['b_3'] = array_sum($b_3);
			}
			else{
				$result['b_3'] = "";
			}
			if(array_sum($b_4) != 0){
				$result['b_4'] = array_sum($b_4);
			}
			else{
				$result['b_4'] = "";
			}
			if(array_sum($b_5) != 0){
				$result['b_5'] = array_sum($b_5);
			}
			else{
				$result['b_5'] = "";
			}
			if(array_sum($b_6) != 0){
				$result['b_6'] = array_sum($b_6);
			}
			else{
				$result['b_6'] = "";
			}
			$result['month'] = $x;
			if($result['kpi_average_ap6m'] >= 85){
				$result['kpi_status'] = 'Pass';
			}
			else{
				$result['kpi_status'] = 'Not Pass';
			}
		}
		$qrcode_direct = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['reference_number'].' ('.$result['direct_name'].'/'.$result['nik_direct_name'].')'));
		$qrcode_mgr = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['reference_number'].' ('.$result['manager_approval'].'/'.$result['nik_manager_approval'].')'));
		$qrcode_new_mgr = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['reference_number'].' ('.$result['new_mgr'].'/'.$result['nik_new_mgr'].')'));
		$qrcode_hr = base64_encode(QrCode::format('svg')->errorCorrection('H')->generate($result['reference_number'].' ('.$result['hr_manager'].'/'.$result['nik_hr_manager'].')'));
		$result['approval'] = [];
		$result['approval']['direct'] = $qrcode_direct;
		$result['approval']['manager'] = $qrcode_mgr;
		$result['approval']['new_manager'] = $qrcode_new_mgr;
		$result['approval']['hr'] = $qrcode_hr;
		$html = view('employee.employee.employee_reco.download', compact('result'))->render();
		$pdf = PDF::loadHTML($html)->setPaper('A4','potrait');
		
		return $pdf->stream('RECO_'.$result['name'].'_'.date('dmY').'.pdf');
		
    }
		
}
