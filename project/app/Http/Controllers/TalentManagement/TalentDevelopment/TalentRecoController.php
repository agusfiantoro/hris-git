<?php

namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\TalentManagement\TalentReco\RecoHeader;
use App\Models\TalentManagement\TalentReco\RecoDetail;
use App\Models\Recruitment\Batch\MasterBatch;
use App\Http\Controllers\Controller;
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
use PhpOffice\PhpSpreadsheet\Spreadsheet; 
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet; 

class TalentRecoController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = RecoHeader::getdata($request->status);
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
						$onclick = "loadedit(".$data->id_talent_recommendation_header.")";
                        $button = '<button type="button" name="edit" onclick="'.$onclick.'" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="batch" id="' . $data->id_talent_recommendation_header . '" name_batch="'.$data->reference_number.'"  pro_pos = "'.$data->pro_pos.'" class="batch btn btn-success btn-sm" title="Generate Batch"><span class="fas fa-tasks fa-lg"></span></button>';
						$button .= '&nbsp;&nbsp;<button type="button" name="bei" id="' . $data->id_talent_recommendation_header . '" pro_pos = "'.$data->pro_pos.'" class="bei btn btn-info btn-sm" title="Generate BEI"><span class="fas fa-tasks fa-lg"></span></button>';
					//	$button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_talent_recommendation_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('talent_management.talent_development.talent_recomendation.index');
    }
	
	public function modal_detail(Request $request) {
		$global_talent = $request->global_talent;
        return view('talent_management.talent_development.talent_recomendation.modal_detail', compact('global_talent'));
    }
	
	public function modal_list(Request $request) {
	//	dd($request->all());
		$global_period_date = $request->global_period_date;
		$global_source = $request->global_source;
		$global_source_grade = $request->global_source_grade;
		$global_source_region = $request->global_source_region;
		$global_source_branch = $request->global_source_branch;
		$global_fpk = $request->global_fpk;
		$global_survey = $request->global_survey;
        return view('talent_management.talent_development.talent_recomendation.modal_list', compact('global_period_date', 'global_source','global_source_grade','global_source_region','global_source_branch','global_fpk','global_survey'));
    }
	
	public function modal_batch(Request $request) {
		$global_talent_header = $request->global_talent;
		$global_name_batch = $request->global_name_batch;
        return view('talent_management.talent_development.talent_recomendation.modal_batch', compact('global_talent_header','global_name_batch'));
    }
	
	public function modal_bei(Request $request) {
		$global_talent_header = $request->global_talent;
        return view('talent_management.talent_development.talent_recomendation.modal_bei', compact('global_talent_header'));
    }

	public function modal_psychogram(Request $request) {
		$employee_id = $request->employee_id;
		$candidate_id = $request->candidate_id;
		$batch_id = $request->batch_id ?? 0;
		$data_id = $request->data_id;
		$source = $request->source;
		$urlWebCareer = 'https://career.borwita.co.id/';
		return view('talent_management.talent_development.talent_recomendation.modal_generate_psychogram', compact('employee_id', 'urlWebCareer', 'batch_id','data_id', 'candidate_id', 'source'));
	}
	
	public function get_val_psychogram(Request $request) {
		$data = [
			'id_batch' => $request->id_batch,
			'id_employee' => $request->id_user_assessment,
		];	
		// if(strtolower($request->source) == 'candidate') {
		// 	$data['id_employee'] = null;
		// 	$data['id_candidate'] = $request->id_user_assessment;
		// } else {
		// 	$data['id_employee'] = $request->id_user_assessment;
		// 	$data['id_candidate'] = null;
		// }
		$result = RecoHeader::get_val_psychogram($data);
		// if(count($result) == 0 || !$result[0]->id_department) {
		if($request->id_psychogram_matrix) {
			if($request->source != 'Candidate') {
				$psychogramMatrix = DB::table('web.psycho_master_psychogram_matrix as pmpm')
								->join('master_general_data as mgd', 'pmpm.id_conclusion', 'mgd.id_general_data')
								->join('master_department as md', 'pmpm.id_department', 'md.id_dept')
								->join('master_job_grade as mjg', 'pmpm.id_job_grade', 'mjg.id_job_grade')
								->select('mgd.id_general_data as id_potencies', 'mgd.description as potencies', 'md.description as department', 'mjg.description as job_grade', 'pmpm.id_department', 'pmpm.id_job_grade')
								->where('pmpm.id_psychogram_matrix', $request->id_psychogram_matrix)->first();
			} else {
				$psychogramMatrix = DB::table('web.psycho_master_psychogram_matrix as pmpm')
					->join('master_general_data as mgd', 'pmpm.id_conclusion', 'mgd.id_general_data')
					->join('master_department as md', 'pmpm.id_department', 'md.id_dept')
					->join('master_job_grade as mjg', 'pmpm.id_job_grade', 'mjg.id_job_grade')
					->select('mgd.id_general_data as id_potencies', 'mgd.description as potencies', 'md.description as department', 'mjg.description as job_grade', 'pmpm.id_department', 'pmpm.id_job_grade')
					->where('pmpm.id_psychogram_matrix', $request->id_psychogram_matrix)->first();
			}
			if($psychogramMatrix) {
				$result = [$psychogramMatrix];
			}
		}
		// }
        return response()->json($result);
    }
	
	public function list_talent(Request $request) {
		if ($request->ajax()) {
		$data = [
				'period_date' => $request->period_date,
				'source_pos_list' => $request->source_pos_list,
				'source_grade_list' => $request->source_grade_list,
				'source_region_list' => $request->source_region_list,
				'source_branch_list' => $request->source_branch_list,
				'fpk_list' => $request->fpk_list,
				'survey_list' => $request->survey_list,
				'submit' => false,
			];	
					
		$result = RecoHeader::get_list_talent($data);
		foreach($result as $key=>$val){
			$masterBatch = MasterBatch::where('id_batch', $val->id_batch)->first();
			if(!is_null($masterBatch)){
				$result[$key]->batch_name =  $masterBatch->batch_name;
			}
			else{
				$result[$key]->batch_name = "";
			}
			if(!is_null($val->batch_assign_date)){
				$result[$key]->batch_assign_date =  Carbon::parse($val->batch_assign_date)->format('d M Y');
			}
			else{
				$result[$key]->batch_assign_date = "";
			}
			$x = explode(" - ",$val->kpi_description);
			if(count($x)>= 2) {
				$result[$key]->kpi_description =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime(@$x[1]."01"));			
			} else {
				$result[$key]->kpi_description = '-';
			}
			
		}
		return DataTables::of($result)
					->addIndexColumn()
					->make(true);		
		}
	}
	
	public function reco_talent(Request $request) {
		$data = [
				'period_date' => $request->period_date,
				'source_pos_list' => $request->source_pos_list,
				'source_grade_list' => $request->source_grade_list,
				'source_region_list' => $request->source_region_list,
				'source_branch_list' => $request->source_branch_list,
				'fpk_list' => $request->fpk_list,
				'survey_list' => $request->survey_list,
				'id_employee' => $request->id_employee,
				'submit' => true,
			];	
		$result = RecoHeader::get_list_talent($data);
		foreach($result as $key=>$val){
			$masterBatch = MasterBatch::where('id_batch', $val->id_batch)->first();
			if(!is_null($masterBatch)){
				$result[$key]->batch_name =  $masterBatch->batch_name;
			}
			else{
				$result[$key]->batch_name = "";
			}
			if(!is_null($val->batch_assign_date)){
				$result[$key]->batch_assign_date =  Carbon::parse($val->batch_assign_date)->format('d M Y');
			}
			else{
				$result[$key]->batch_assign_date = "";
			}
			if($val->kpi_description != null){
				$x = explode(" - ",$val->kpi_description);
				$result[$key]->kpi_desc =  date('M Y', strtotime($x[0]."01"))." - ".date('M Y', strtotime($x[1]."01"));	
			}
			else{
				$result[$key]->kpi_desc = "-";
			}
		}
		return response()->json($result);
	}
	
	public function get_employee_by() {
        $result = RecoHeader::get_employee_by();
        return response()->json($result);
    }
	
	public function get_grade() {
        $result = RecoHeader::get_grade();
        return response()->json($result);
    }
	
	public function get_region() {
        $result = RecoHeader::get_region();
        return response()->json($result);
    }
	public function get_branch(Request $request) {
	//	dd($request->id_region);
        $result = RecoHeader::get_branch($request->id_region);
        return response()->json($result);
    }
	
	public function get_projected() {
        $result = RecoHeader::get_projected();
        return response()->json($result);
    }
	
	public function get_trigger_pos(Request $request) {
		$result = [];
		$data = [
				'id_routing' => $request->id_routing,
			];	
		if($request->id_routing != null){
			$result = RecoHeader::get_trigger_pos($data);
		}
		return response()->json($result);
	}
	
	public function get_source_pos() {
        $result = RecoHeader::get_source_pos();
        return response()->json($result);
    }
	
	public function get_fpk() {
        $result = RecoHeader::get_fpk();
        return response()->json($result);
    }
	
	public function get_survey() {
        $result = RecoHeader::get_survey();
        return response()->json($result);
    }
	
	public function get_validate(Request $request) {
		 $arr_form_validate = [
		//	'projected_pos' => 'required',
			'rec_type' => 'required',
		//	'source_pos' => 'required',
		//	'survey' => 'required',
        ];
		
        $arr_msg_form_validate = [
		//	'projected_pos.required' => 'The Projected Position field is required',
			'rec_type.required' => 'The Type field is required',			
		//	'source_pos.required' => 'The Source Position field is required',
		//	'survey.required' => 'The BEES Survey field is required',
        ];
        
		if($request->rec_type == 'P'){
			$arr_form_validate['period_date'] = 'required';
			$arr_msg_form_validate['period_date.required'] = 'The Period Date field is required';
		}
	//	dd($arr_form_validate);
		$request->validate($arr_form_validate, $arr_msg_form_validate);
		return response()->json(['status' => 'true']);
    }
	
	protected function validateReq(Request $request) {
        $arr_form_validate = [
		//	'projected_pos' => 'required',
			'notes' => 'required',
			'rec_type' => 'required',
		//	'source_pos' => 'required',
		//	'survey' => 'required',
        ];
        $arr_msg_form_validate = [
		//	'projected_pos.required' => 'The Projected Position field is required',
			'notes.required' => 'Description field is required',
			'rec_type.required' => 'The Type field is required',
		//	'source_pos.required' => 'The Source Position field is required',
		//	'survey.required' => 'The BEES Survey field is required',
        ];
        if ($request->post('emp') == null) {
            $validate_emprequest = ['table_emp_detail' => 'required|string'];
            $validate_msg_emprequest = ['table_emp_detail.required' => 'Employee List cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_emprequest);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_emprequest);
        }
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {		
		$this->validateReq($request);
	//	dd($request->all());
		try{
			DB::beginTransaction();
			$kode = RecoHeader::getkode();
			$form_data = array(
				'reference_number' => $kode,
				'id_employee_request' => $request->id_employee_request,
				'type' => $request->rec_type,
				'id_position_routing' => $request->projected_pos,
				'id_survey_header' => $request->survey,
				'id_hiring_request_header' => $request->fpk,
				'notes' => $request->notes,
				'period_date' => $request->period_date,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			
			$RecoSave = RecoHeader::create($form_data);
			
			if(isset($request->emp)){
				foreach ($request->emp as $key => $value) {
					$form_detail = array(
						'id_talent_recommendation_header' => $RecoSave->id_talent_recommendation_header,
						'id_employee' => $value['id_employee'],
						'id_position_detail' => $value['id_position_detail'],
						'id_dept' => $value['id_dept'],
						'id_job_grade' => $value['id_job_grade'],
						'id_region' => $value['id_region'],
						'id_branch' => $value['id_branch'],
						'id_grade_promotion' => $value['id_rating'],
						'kpi_1_month_ago' => $value['id_month1'],
						'kpi_2_month_ago' => $value['id_month2'],
						'kpi_3_month_ago' => $value['id_month3'],
						'kpi_4_month_ago' => $value['id_month4'],
						'kpi_5_month_ago' => $value['id_month5'],
						'kpi_6_month_ago' => $value['id_month6'],
						'kpi_7_month_ago' => $value['id_month7'],
						'kpi_8_month_ago' => $value['id_month8'],
						'kpi_9_month_ago' => $value['id_month9'],
						'kpi_10_month_ago' => $value['id_month10'],
						'kpi_11_month_ago' => $value['id_month11'],
						'kpi_12_month_ago' => $value['id_month12'],
						'kpi_average' => $value['id_kpi_average'],
						'kpi_desc' => $value['kpi_desc'],
						'is_have_sp' => $value['id_sp'] == "true" ? 1 : 0,
						'is_have_kpk' => $value['id_kpk'] == "true" ? 1 : 0,
						'id_conclusion_psychotest' => $value['id_potencies'],
						'id_conclusion_assessment' => $value['competencies'],
						'id_survey_answer_user_header' => $value['id_survey_answer_user_header'],
						'engagement_survey_result' => $value['id_eng_level'],
						'id_batch' => $value['id_batch'],
						'id_dept_psychogram' => @$value['psych_id_dept'],
						'id_job_grade_psychogram' => @$value['psych_id_job_grade'],
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					RecoDetail::create($form_detail);
				}
			}

				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Talent Recommendation Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Talent Recommendation !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function update(Request $request) {		
		$this->validateReq($request);
	//	dd($request->all());
		try{
			DB::beginTransaction();
			$form_data = array(
			//	'id_employee_request' => $request->id_employee_request,
				'type' => $request->rec_type,
				'id_position_routing' => $request->projected_pos,
				'id_survey_header' => $request->survey,
				'id_hiring_request_header' => $request->fpk,
				'notes' => $request->notes,
			//	'period_date' => $request->period_date,
				'status' => $request->status,
				'id_company' => session('id_company'),
				'updated_by' => session('id_user'),
			);
			$recoHeader = RecoHeader::findOrFail($request->id_talent_recommendation_header);
			$status = $recoHeader->status;
			$RecoUpdate = $recoHeader->update($form_data);
			if($request->status != $status) {
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Talent Recommendation Updated Successfully !!']);
			}
			
			

			$listIdReco    = [];
			$idReco     = [];
			if(RecoDetail::where('id_talent_recommendation_header', $request->id_talent_recommendation_header)->first() != null){
				$listIdReco = RecoDetail::where('id_talent_recommendation_header', $request->id_talent_recommendation_header)->where('id_company', session('id_company'))->get()->pluck('id_talent_recommendation_detail')->all();
			}
			
			if(isset($request->emp)){				
				foreach ($request->emp as $key => $value) {			
					if ($value['id_talent_recommendation_detail'] == "") {					
						$form_reco = array(
							'id_talent_recommendation_header' => $request->id_talent_recommendation_header,
							'id_employee' => $value['id_employee'],
							'id_position_detail' => $value['id_position_detail'],
							'id_dept' => $value['id_dept'],
							'id_job_grade' => $value['id_job_grade'],
							'id_region' => $value['id_region'],
							'id_branch' => $value['id_branch'],
							'id_grade_promotion' => $value['id_rating'],
							'kpi_1_month_ago' => $value['id_month1'],
							'kpi_2_month_ago' => $value['id_month2'],
							'kpi_3_month_ago' => $value['id_month3'],
							'kpi_4_month_ago' => $value['id_month4'],
							'kpi_5_month_ago' => $value['id_month5'],
							'kpi_6_month_ago' => $value['id_month6'],
							'kpi_7_month_ago' => $value['id_month7'],
							'kpi_8_month_ago' => $value['id_month8'],
							'kpi_9_month_ago' => $value['id_month9'],
							'kpi_10_month_ago' => $value['id_month10'],
							'kpi_11_month_ago' => $value['id_month11'],
							'kpi_12_month_ago' => $value['id_month12'],
							'kpi_average' => $value['id_kpi_average'],
							'kpi_desc' => $value['kpi_desc'],
							'is_have_sp' => $value['id_sp'] == "true" ? 1 : 0,
							'is_have_kpk' => $value['id_kpk'] == "true" ? 1 : 0,
							'id_conclusion_psychotest' => $value['id_potencies'],
							'id_conclusion_assessment' => $value['competencies'],
							'id_survey_answer_user_header' => $value['id_survey_answer_user_header'],
							'engagement_survey_result' => $value['id_eng_level'],
							'id_dept_psychogram' => @$value['psych_id_dept'],
							'id_job_grade_psychogram' => @$value['psych_id_job_grade'],
							'id_batch' => $value['id_batch'],
							'id_company' => session('id_company'),
							'created_by' => session('id_user'),
						);
						
						RecoDetail::create($form_reco);
					}
					 else {
						$idReco[] = $value['id_talent_recommendation_detail'];
						$form_reco = array(
							'id_conclusion_assessment' => $value['competencies'],
							'id_dept_psychogram' => @$value['psych_id_dept'],
							'id_job_grade_psychogram' => @$value['psych_id_job_grade'],
							'id_conclusion_psychotest' => @$value['id_potencies'],
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						);						
						
						RecoDetail::where('id_talent_recommendation_detail', $value['id_talent_recommendation_detail'])->update($form_reco);
					}
				}
			}
				
			$diff = array_diff($listIdReco, $idReco);
			if(count($diff) > 0){
				foreach ($diff as $key => $value) { 
					RecoDetail::where('id_talent_recommendation_detail', $value)->delete();
				}
			}

				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Talent Recommendation Updated Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Update Talent Recommendation !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	protected function save_batch(Request $request) {
		 $request->validate([
		//	'batch_name' => ['required', Rule::unique('psycho_master_batch')->where('id_company',session('id_company'))],
            'batch_name' => 'required|string',
            'start_date' => 'required|string',
            'end_date' => 'required|string',
            'location' => 'required|string',
            'id_branch' => 'required|string',
                ], [],
                [
                    'batch_name' => 'Batch Name',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                    'location' => 'Location',
                    'id_branch' => 'Access Branch',
        ]);
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_talent_recommendation_header_batch' => $request->id_talent_recommendation_header_batch,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'location' => $request->location,
				'id_branch' => $request->id_branch,
				'id_emp_batch' => $request->id_emp_batch,
				'id_company' => session('id_company'),
			);
			
			RecoHeader::gen_batch($form_data);
			
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'Batch Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save Batch !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	public function get_talent_edit(Request $request) {
        $data = [
            'id_talent_recommendation_header' => $request->id_talent
        ];
        $result = RecoHeader::get_talent_edit($data);
        return response()->json($result);
    }	
	
	public function get_list_batch(Request $request) {
		if ($request->ajax()) {
			$result = RecoHeader::get_list_batch($request->id_talent);
			return DataTables::of($result)
						->addIndexColumn()
						->make(true);		
		}		
    }
	
	public function get_type_bei() {
        $result = RecoHeader::get_type_bei();
        return response()->json($result);
    }
	
	protected function save_bei(Request $request) {
		 $request->validate([
            'int_type' => 'required|string',
            'int_date' => 'required|string',
                ], [],
                [
                    'int_type' => 'Interview Type',
                    'int_date' => 'Interview Date',
        ]);
		try{
			DB::beginTransaction();
			$form_data = array(
				'id_talent_recommendation_header_bei' => $request->id_talent_recommendation_header_bei,
				'id_emp_bei' => $request->id_emp_bei,
				'int_type' => $request->int_type,
				'int_date' => $request->int_date,
				'id_company' => session('id_company'),
			);
			RecoHeader::gen_bei($form_data);
			
				DB::commit();
				return response()->json(['status' => 'true', 'message' => 'BEI Saved Successfully !!']);
		} catch (\Exception $e) {
			DB::rollBack();
			Log::error($e);
			return response()->json(['status' => 'false', 'message' => 'Cannot Save BEI !! [' . $e->getMessage() . ']']);           
		}	
	}
	
	
	public function exportTalent(Request $request) {
		$request->validate([
			'id_talent_recommendation_header' => 'required',
		]);
		$data = [
            'id_talent_recommendation_header' => $request->id_talent_recommendation_header
        ];
        $result = RecoHeader::get_talent_edit($data);
		
		$dataSheet1 = [];
		$dataSheet1[] = ['No', 'ID Talent Recommendation Detail', 'NIK', 'Name', 'Position', 'Job Grade', 'Branch', 'Rating', 'Period', 'Avg. KPI', 'Competencies', 'Potencies (Psychogram)', 'SP', 'KPK', 'Engagement Level', 'Batch Name'];
		foreach($result['emp'] as $k => $emp) {
			$dataSheet1[] = [
				$k+1,
				$emp['id_talent_recommendation_detail'],
				$emp['nik_employee'],
				$emp['name_employee'],
				$emp['position_route'],
				$emp['job_grade'],
				$emp['branch'],
				$emp['final_rating'],
				$emp['kpi_desc'],
				$emp['kpi_average'],
				$emp['competencies_desc'],
				$emp['potencies'],
				$emp['sp'],
				$emp['kpk'],
				$emp['eng_level'],
				$emp['batch_name'],
			];
		}

		$filename = 'Talent Reco '.$result['notes'].' '.$result['reference_number'];
        $spreadsheet = new Spreadsheet(); 
        $spreadsheet->removeSheetByIndex(0);
		

		$nameSheet1 = 'Talent Recommendation';
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

	public function importTalent(Request $request) {
		$request->validate([
			'id_talent_recommendation_header' => 'required',
			'attachment' => 'required|file',
		]);

		$type = \PhpOffice\PhpSpreadsheet\IOFactory::identify($request->attachment);
		$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
		$reader->setReadDataOnly(true);
		$spreadsheet = $reader->load($request->attachment);
		$sheetData = $spreadsheet->getActiveSheet()->toArray();
		$errors = [];

		foreach($sheetData as $rowIndex => $rowData) {
			if($rowIndex == 0) continue;

			$idTalentRecoDetail = $rowData[1];
			$competency = $rowData[10];

			$conclusionAssessment = DB::table('master_general_data')
									->where('code', $competency)
									->where('id_company', session('id_company'))
									->where('status', 'A')
									->first();
			$recoDetail = RecoDetail::find($idTalentRecoDetail);
			if(!$conclusionAssessment && $competency != NULL) {
				$errors[] = '[ID: '.$idTalentRecoDetail.'] Competency code ('.$competency.') not found in master general data.';
				continue;
			}
			if(!$recoDetail) {
				$errors[] = '[ID: '.$idTalentRecoDetail.'] ID Talent Recommendation Detail not found.';
				continue;
			}
			$recoDetail->id_conclusion_assessment = @$conclusionAssessment->id_general_data;
			$recoDetail->updated_by = session('id_user');
			$recoDetail->save();
		}
		$errorMessage = '';
		if(count($errors) > 0) {
			$errorMessage .= " \nErrors: \n".implode("\n", $errors);
		}

		return response()->json([
			'message' => (count($errors) == $rowIndex ? 'Import failed.' : 'Import success.').$errorMessage,
		], count($errors) == $rowIndex ? 422 : 200);
	}

	public function get_employee_kpi(Request $request) {
		$kpi = RecoHeader::get_employee_kpi($request);
		return DataTables::of($kpi)->addIndexColumn()->make(true);
	}
}
