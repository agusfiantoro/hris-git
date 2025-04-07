<?php

namespace App\Http\Controllers\Employee\EmployeeSetting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee\EmployeeSetting\HrSurveyHeader;
use App\Models\Employee\EmployeeSetting\HrSurveyQuestion;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswer;
use App\Models\Employee\EmployeeSetting\MasterAnswer;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Models\Organization\MasterOrganization\MasterPrincipal;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class MasterEmployeeSurveyController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = HrSurveyHeader::getdata();
            //	dd($data);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id_survey_header . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
					$button .= "<button type='button' id-survey-header='$data->id_survey_header' class='duplicate btn btn-success btn-sm' title='Duplicate'><span class='fas fa-copy'></span></button>";
                    $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_survey_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('employee.employee_setting.master_employee_survey.index');
    }
	public function index_answer(Request $request) {
        if ($request->ajax()) {
            $data = HrSurveyHeader::getdata_answer();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('image', function($data) {
                	$return = null;
                    if(!is_null($data->suggested_image)){
						$filePath   = 'project/storage/app/public/upload/master_answer/';
                    	$return = url($filePath.$data->suggested_image);
                    }
                    return $return;
                })
                ->addColumn('action', function($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id_answer . '" class="edit_answer btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';
                    $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_answer . '" class="delete_answer btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
	
	protected function save_answer(Request $request) {
		$arr_form_validate = [
            'code_answer' => 'required',
            'description_answer' => 'required',
        ];
        $arr_msg_form_validate = [
            'code_answer.required' => 'Code Answer is required',
            'description_answer.required' => 'Description Answer is required',
        ];
        if ($request->suggested_image) {
            $arr_form_validate['suggested_image'] = 'mimes:jpg,jpeg,png,svg';
            $arr_msg_form_validate['suggested_image.mimes'] = 'Image extension must be : .jpg/.jpeg/.png/.svg';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);

        $filePath   = 'public/upload/master_answer/';
        $newFileName= null;

        if($request->suggested_image) {
            if ($request->suggested_image->isValid()) {
                $fileName       = $request->suggested_image->getClientOriginalName();
                $newFileName    = Str::random(3).'_'.$fileName;
				$dir 			= Storage::makeDirectory($filePath,0775, true, true);
                $storageFile   	= Storage::putFileAs($filePath, $request->suggested_image, $newFileName);
            }
        }
        $form_data = array(
            'code' => $request->code_answer,
            'description_answer' => $request->description_answer,
            'suggested_image' => $newFileName,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		MasterAnswer::create($form_data);
		
        return response()->json(['status' => 'true', 'message' => 'Master Answer Saved Successfully !!']);
    }
	
	
	protected function update_answer(Request $request) {
        $arr_form_validate = [
            'code_answer' => 'required',
            'description_answer' => 'required',
        ];
        $arr_msg_form_validate = [
            'code_answer.required' => 'Code Answer is required',
            'description_answer.required' => 'Description Answer is required',
        ];
        if ($request->suggested_image) {
            $arr_form_validate['suggested_image'] = 'mimes:jpg,jpeg,png,svg';
            $arr_msg_form_validate['suggested_image.mimes'] = 'Image extension must be : .jpg/.jpeg/.png/.svg';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
        $filePath   = 'public/upload/master_answer/';
        $newFileName= null;

        if($request->suggested_image) {
            if ($request->suggested_image->isValid()) {
                $fileName       = $request->suggested_image->getClientOriginalName();
                $newFileName    = Str::random(3).'_'.$fileName;
				$dir 			= Storage::makeDirectory($filePath,0775, true, true);
                $storageFile   	= Storage::putFileAs($filePath, $request->suggested_image, $newFileName);
            }
        }
        $form_data = array(
            'code' => $request->code_answer,
            'description_answer' => $request->description_answer,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
        $getAnswer = MasterAnswer::findOrFail($request->id_answer);
        if($newFileName != null){
        	$form_data['suggested_image'] = $newFileName;
	        if (Storage::exists($filePath.$getAnswer->suggested_image)) {
	            Storage::delete($filePath.$getAnswer->suggested_image);
	        }
        }
		$getAnswer->update($form_data);
		
        return response()->json(['status' => 'true', 'message' => 'Master Answer Updated Successfully !!']);
    }
	
	public function destroy_answer($id) {
		DB::beginTransaction();
        try {
	        $data = MasterAnswer::findOrFail($id);
	        if($data->suggested_image != null){
	        	$filePath   = 'public/upload/master_answer/';
		        if (Storage::exists($filePath.$data->suggested_image)) {
		            Storage::delete($filePath.$data->suggested_image);
		        }
	        }
	        try{
				$data->delete();
			} catch (\Exception $e) {
	            throw new \Exception('Tidak bisa dihapus, jawaban sudah dipakai pada soal');           
	        }
	        DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Answer Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	public function get_edit_answer(Request $request) {
        $data = [
            'id_answer' => $request->id_answer
        ];
        $result = HrSurveyHeader::get_edit_answer($data);
        if(!is_null($result['suggested_image'])){
			$filePath   = 'project/storage/app/public/upload/master_answer/';
        	$result['suggested_image'] = url($filePath.$result['suggested_image']);
        }
        return response()->json($result);
    }
	
	protected function validateSurvey(Request $request) {

        $arr_form_validate = [
            'description' => 'required|string',
            'period.*' => 'required',
            'question.*.sequence' => 'required',
            'question.*.question' => 'required',
        ];
        $arr_msg_form_validate = [
            'question.*.sequence.required' => 'The Sequence field is required',
            'question.*.question.required' => 'The Question field is required',
            'question.*.question_type.required' => 'The Question Type is required',
            'period.*.required' => 'The Period field is required',
        ];
        if ($request->post('question') == null) {
            $validate_question = ['table_question' => 'required|string'];
            $validate_msg_question = ['table_question.required' => 'Table Question Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_question);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_question);
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function save(Request $request) {
        $this->validateSurvey($request);
        DB::beginTransaction();
        try {
	        $form_data = array(
	            'description' => $request->description,
	            'notes' => $request->notes,
	            'id_employee_request' => $request->id_employee_request,
	            'id_survey_type' => $request->id_survey_type,
	            'survey_category' => 'Survey', //statis pasti Survey, lihat constraint : hr_survey_header
	            // 'start_date' => $request->start_date,
	            // 'end_date' => $request->end_date,
	            'published' => isset($request->published) == "on" ? 1 : 0,
	            'with_score' => isset($request->with_score) == "on" ? 1 : 0,
	            'is_cross_company_os' => isset($request->cross_com) == "on" ? 1 : 0,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'created_by' => session('id_user'),
	        );	
			$result = HrSurveyHeader::create($form_data); //simpan header survey
			foreach ($request->question as $key => $value) {
				if(isset($value['attachment'])){
					$imagename = rand(0,9999)."_".date("Y-m-d").".".$value['attachment']->getClientOriginalExtension();
					$dir = Storage::makeDirectory('public/upload/survey/'.$result->id_survey_header,0775, true, true);
					$storageimage = Storage::putFileAs('public/upload/survey/'.$result->id_survey_header,$value['attachment'],$imagename);
				}
				else{
					$imagename = NULL;
				}
				$form_question = array(
					'id_survey_header' => $result->id_survey_header,
	            	'id_question_type' => $value['question_type'],
	            	'id_question_group' => @$value['category'],
					'sequence' => $value['sequence'],
					'question' => $value['question'],
					'attachment' =>  $imagename,
					'note' =>$value['note'],
					'status' => 'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				$question = HrSurveyQuestion::create($form_question); //simpan semua question dalam 1 survey
				if($question){
					if(@$value['suggested_answer']){
						foreach (@$value['suggested_answer'] as $k => $item) {
							$form_answer = array(
								'id_survey_question' => $question->id_survey_question,
								'id_answer' => $item,
								'reference_number' => '',
								'suggested_answer' => '',
								'is_corrected_answer' => isset($value['correct_input']) == "on" ? true : false,
								'score_answer' => null,
								'status' => 'A',
								'id_company' => session('id_company'),
								'created_by' => session('id_user'),
							);
							$answer= HrSurveyAnswer::create($form_answer); //simpan semua answer sesuai question dalam 1 survey
						}
					}
				}
			}

			foreach ($request->period as $key => $val) {
				$surveyHistory = [
					'id_survey_header' => $result->id_survey_header,
					'start_date' => $request->startperiod[$key],
					'end_date' => $request->endperiod[$key],
					'status' => $request->statusperiod[$key],
					'id_company' => session('id_company'),
					'creation_date' => date('Y-m-d H:i:s'),
					'created_by' => session('id_user'),
				];
				$ins = DB::table('hr_survey_history')->insert($surveyHistory);
			}

			if($request->department){
				$deptEach = $request->department;
				foreach ($deptEach as $key => $val) {
					$surveyDepartment = [
						'id_dept' => $val,
						'id_survey_header' => $result->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					$ins = DB::table('relation_department_surveys')->insert($surveyDepartment);
				}
			}
			if($request->region){
				$regionEach = $request->region;
				foreach ($regionEach as $key => $val) {
					$surveyRegion = [
						'id_region' => $val,
						'id_survey_header' => $result->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					$ins = DB::table('relation_regional_surveys')->insert($surveyRegion);
				}
			}
			if($request->branch){
				$branchEach = $request->branch;
				foreach ($branchEach as $key => $val) {
					$surveyBranch = [
						'id_branch' => $val,
						'id_survey_header' => $result->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					$ins = DB::table('relation_branch_surveys')->insert($surveyBranch);
				}
			}
			if($request->grade){
				$gradeEach = $request->grade;
				foreach ($gradeEach as $key => $val) {
					$surveyGrade = [
						'id_job_grade' => $val,
						'id_survey_header' => $result->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					$ins = DB::table('relation_jobgrade_surveys')->insert($surveyGrade);
				}
			}

			if($request->principal){
				$principalEach = $request->principal;
				foreach ($principalEach as $key => $val) {
					$surveyPrincipal = [
						'id_principal' => $val,
						'id_survey_header' => $result->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					$ins = DB::table('relation_principal_surveys')->insert($surveyPrincipal);
				}
			}

	        DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Survey Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

	protected function update(Request $request) {
	//	dd($request->all());
        $this->validateSurvey($request);
        DB::beginTransaction();
        try {
	        $form_data = array(
	            'description' => $request->description,
	            'notes' => $request->notes,
	            'id_employee_request' => $request->id_employee_request,
	            'id_question_type' => @$request->id_question_type, 
	            'id_survey_type' => $request->id_survey_type,
	            'survey_category' => 'Survey', //statis pasti Survey, lihat constraint : hr_survey_header
	            // 'start_date' => $request->start_date,
	            // 'end_date' => $request->end_date,
	            'published' => isset($request->published) == "on" ? 1 : 0,
	            'with_score' => isset($request->with_score) == "on" ? 1 : 0,
	            'is_cross_company_os' => isset($request->cross_com) == "on" ? 1 : 0,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'updated_by' => session('id_user'),
	        );	
			$result = HrSurveyHeader::where('id_survey_header', $request->id_survey_header)->update($form_data);
			foreach ($request->period as $key => $val) {
				$idSurveyHistory = $request->id_survey_history[$key];
				$surveyHistory = [
					'id_survey_header' => $request->id_survey_header,
					'start_date' => date('Y-m-d',strtotime($request->startperiod[$key])),
					'end_date' => date('Y-m-d',strtotime($request->endperiod[$key])),
					'status' => $request->statusperiod[$key],
					'id_company' => session('id_company'),
				];
				if(!is_null($idSurveyHistory)){
					$surveyHistory['update_date'] = date('Y-m-d H:i:s');
					$surveyHistory['updated_by'] = session('id_user');
					$up = DB::table('hr_survey_history')->where('id_survey_history', $idSurveyHistory)->update($surveyHistory);
				}
				else {
					$surveyHistory['creation_date'] = date('Y-m-d H:i:s');
					$surveyHistory['created_by'] = session('id_user');
					$ins = DB::table('hr_survey_history')->insert($surveyHistory);
				}
			}

			$collect_question = collect($request->question)->groupBy('id_survey_question')->toArray();
	        $list_question = array_filter(array_keys($collect_question));
			$surveyQuestion     = '';
	        if(count($list_question) > 0){
	          $idQuestion =   HrSurveyQuestion::whereNotIn('id_survey_question', $list_question)->where('id_survey_header', $request->id_survey_header)->get();
			  foreach($idQuestion as $key=>$valQues){
				  HrSurveyAnswer::where('id_survey_question', $valQues->id_survey_question)->delete();
			  }
			  HrSurveyQuestion::whereNotIn('id_survey_question', $list_question)->where('id_survey_header', $request->id_survey_header)->delete();
	        } 
		/*	else {
	             $question   = HrSurveyQuestion::where('id_survey_header', $request->id_survey_header);
	             if($question->first() != null){
	                 HrSurveyQuestion::where('id_survey_header', $request->id_survey_header)->delete();
	             }
	        }
		*/
			$deptEach = [];
	        $existDept = DB::table('relation_department_surveys')->where('id_survey_header', $request->id_survey_header)->get()->pluck('id_dept')->all();

			$regionEach = [];
			$existRegion = DB::table('relation_regional_surveys')->where('id_survey_header', $request->id_survey_header)->get()->pluck('id_region')->all();

			$branchEach = [];
        	$existBranch = DB::table('relation_branch_surveys')->where('id_survey_header', $request->id_survey_header)->get()->pluck('id_branch')->all();

        	$gradeEach = [];
        	$existGrade = DB::table('relation_jobgrade_surveys')->where('id_survey_header', $request->id_survey_header)->get()->pluck('id_job_grade')->all();

			$principalEach = [];
			$existPrincipal = DB::table('relation_principal_surveys')->where('id_survey_header', $request->id_survey_header)->get()->pluck('id_principal')->all();

	        if($request->department){
				$deptEach = $request->department;
				foreach ($deptEach as $key => $val) {
					$surveyDepartment = [
						'id_dept' => $val,
						'id_survey_header' => $request->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					if(in_array($val, $existDept)){
						$up = DB::table('relation_department_surveys')->where('id_dept', $val)->where('id_survey_header', $request->id_survey_header)->update($surveyDepartment);
					} else {
						$ins = DB::table('relation_department_surveys')->insert($surveyDepartment);
					}
				}
			}
			if($request->region){
				$regionEach = $request->region;
				foreach ($regionEach as $key => $val) {
					$surveyRegion = [
						'id_region' => $val,
						'id_survey_header' => $request->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					if(in_array($val, $existRegion)){
						$up = DB::table('relation_regional_surveys')->where('id_region', $val)->where('id_survey_header', $request->id_survey_header)->update($surveyRegion);
					} else {
						$ins = DB::table('relation_regional_surveys')->insert($surveyRegion);
					}
				}
			}
			if($request->branch){
				$branchEach = $request->branch;
				foreach ($branchEach as $key => $val) {
					$surveyBranch = [
						'id_branch' => $val,
						'id_survey_header' => $request->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					if(in_array($val, $existBranch)){
						$up = DB::table('relation_branch_surveys')->where('id_branch', $val)->where('id_survey_header', $request->id_survey_header)->update($surveyBranch);
					} else {
						$ins = DB::table('relation_branch_surveys')->insert($surveyBranch);
					}
				}
			}
			if($request->grade){
				$gradeEach = $request->grade;
				foreach ($gradeEach as $key => $val) {
					$surveyGrade = [
						'id_job_grade' => $val,
						'id_survey_header' => $request->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					if(in_array($val, $existGrade)){
						$up = DB::table('relation_jobgrade_surveys')->where('id_job_grade', $val)->where('id_survey_header', $request->id_survey_header)->update($surveyGrade);
					} else {
						$ins = DB::table('relation_jobgrade_surveys')->insert($surveyGrade);
					}
				}
			}

			if($request->principal){
				$principalEach = $request->principal;
				foreach ($principalEach as $key => $val) {
					$surveyPrincipal = [
						'id_principal' => $val,
						'id_survey_header' => $request->id_survey_header,
						'id_company' => session('id_company'),
						'creation_date' => date('Y-m-d H:i:s'),
						'created_by' => session('id_user'),
					];
					if(in_array($val, $existPrincipal)) {
						$up = DB::table('relation_principal_surveys')->where('id_principal', $val)->where('id_survey_header', $request->id_survey_header)->update($surveyPrincipal);
					} else {
						$ins = DB::table('relation_principal_surveys')->insert($surveyPrincipal);
					}
				}
			}

			//PENGECEKAN JIKA ada pilihan department, atau region, branch yg dikurangi dr yg sebelumny
			$diffDept = collect($existDept)->diff(collect($deptEach));
			if(count($diffDept) > 0 && count($existDept) > 0){
				foreach ($diffDept as $key => $val) {
					$up = DB::table('relation_department_surveys')->where('id_dept', $val)->delete();
				}
			}

			$diffReg = collect($existRegion)->diff(collect($regionEach));
			if(count($diffReg) > 0 && count($existRegion) > 0){
				foreach ($diffReg as $key => $val) {
					$up = DB::table('relation_regional_surveys')->where('id_region', $val)->delete();
				}
			}

			$diffBranch = collect($existBranch)->diff(collect($branchEach));
			if(count($diffBranch) > 0 && count($existBranch) > 0){
				foreach ($diffBranch as $key => $val) {
					$up = DB::table('relation_branch_surveys')->where('id_branch', $val)->delete();
				}
			}

			$diffGrade = collect($existGrade)->diff(collect($gradeEach));
			if(count($diffGrade) > 0 && count($existGrade) > 0){
				foreach ($diffGrade as $key => $val) {
					$up = DB::table('relation_jobgrade_surveys')->where('id_job_grade', $val)->delete();
				}
			}
			$diffPrincipal = collect($existPrincipal)->diff(collect($principalEach));
			if(count($diffPrincipal) > 0 && count($existPrincipal) > 0){
				foreach ($diffPrincipal as $key => $val) {
					$up = DB::table('relation_principal_surveys')->where('id_principal', $val)->delete();
				}
			}

			foreach ($request->question as $key => $value) {
	            if ($value['id_survey_question'] == "") {
					if(isset($value['attachment'])){
						$imagename = rand(0,9999)."_".date("Y-m-d").".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/survey/'.$request->id_survey_header,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/survey/'.$request->id_survey_header,$value['attachment'],$imagename);
					}
					else{
						$imagename = NULL;
					}
				
	            	$form_question = array(
						'id_survey_header' => $request->id_survey_header,
		            	'id_question_type' => $value['question_type'],
	            		'id_question_group' => @$value['category'],
						'sequence' => $value['sequence'],
						'question' => $value['question'],
						'attachment' => $imagename,
						'note' =>$value['note'],
						'status' => 'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					$question = HrSurveyQuestion::create($form_question); //simpan semua question dalam 1 survey
					if($question){
						if(@$value['suggested_answer']){
							foreach (@$value['suggested_answer'] as $k => $item) {
								$form_answer = array(
									'id_survey_question' => $question->id_survey_question,
									'id_answer' => $item,
									'reference_number' => '',
									'suggested_answer' => '',
									'is_corrected_answer' => isset($value['correct_input']) == "on" ? true : false,
									'score_answer' => null,
									'status' => 'A',
									'id_company' => session('id_company'),
									'created_by' => session('id_user'),
								);
								$answer= HrSurveyAnswer::create($form_answer); //simpan semua answer sesuai question dalam 1 survey
							}
						}
					}
	            } else {					
					$form_question = array(
						'id_question_type' => $value['question_type'],
	            		'id_question_group' => @$value['category'],
	                    'sequence' => $value['sequence'],
						'question' => $value['question'],
						'note' =>$value['note'],
						'status' => 'A',
						'id_company' => session('id_company'),
	                    'updated_by' => session('id_user'),
					);					
					if(isset($value['attachment'])){
						$imagename = rand(0,9999)."_".date("Y-m-d").".".$value['attachment']->getClientOriginalExtension();
						$dir = Storage::makeDirectory('public/upload/survey/'.$request->id_survey_header,0775, true, true);
						$storageimage = Storage::putFileAs('public/upload/survey/'.$request->id_survey_header,$value['attachment'],$imagename);
						$form_question['attachment']= $imagename;
					}					
					HrSurveyQuestion::where('id_survey_question', $value['id_survey_question'])->update($form_question);
	         
	            	$getAnswer =  HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->get();
	            	if(count($getAnswer) < 1){
	            		if(@$value['suggested_answer']){
							foreach (@$value['suggested_answer'] as $k => $item) {
								$form_answer = array(
									'id_survey_question' => $value['id_survey_question'],
									'id_answer' => $item,
									'reference_number' => '',
									'suggested_answer' => '',
									'is_corrected_answer' => isset($value['correct_input']) == "on" ? true : false,
									'score_answer' => null,
									'status' => 'A',
									'id_company' => session('id_company'),
									'created_by' => session('id_user'),
								);
								$answer= HrSurveyAnswer::create($form_answer); //simpan semua answer sesuai question dalam 1 survey
							}
						}
	            	} else {
		                if($value['id_survey_question']){
							if(@$value['suggested_answer']){
								$existingAnswers = $getAnswer->pluck("id_answer");
								foreach($existingAnswers as $ans) {
									if(!in_array($ans, $value['suggested_answer'])) {
										$del = HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])
															->where('id_company', session('id_company'))
															->where('id_answer', $ans)
															->update([
																'status' => 'I'
															]); //inactivekan answer yang ada di database tapi tidak ada di input
									}
								}
								foreach (@$value['suggested_answer'] as $k => $item) {
									$form_answer = array(
										'id_answer' => $item,
										'status' => 'A',
										'id_company' => session('id_company'),
										'created_by' => session('id_user'),
									);
									$answer= HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->where('id_company', session('id_company'))->where('id_answer',$item)->update($form_answer); //simpan semua answer sesuai question dalam 1 survey
									if(!$answer) {
										$form_answer = array(
											'id_survey_question' => $value['id_survey_question'],
											'id_answer' => $item,
											'reference_number' => '',
											'suggested_answer' => '',
											'is_corrected_answer' => isset($value['correct_input']) == "on" ? true : false,
											'score_answer' => null,
											'status' => 'A',
											'id_company' => session('id_company'),
											'created_by' => session('id_user'),
										);
										$ins = HrSurveyAnswer::create($form_answer);
									}
								}
							}
						}
	            	}
	            }
	        }
	       	DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Survey Update Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
	
	protected function save_survey(Request $request) {
        $this->validateSurvey($request);
		$data = [
			'counter' => $request->counter
        ];
        $form_data = array(
            'description' => $request->description,
            'id_employee_request' => $request->id_employee_request,
            // 'id_question_type' => $request->id_question_type,
            'id_survey_type' => $request->id_survey_type,
            'survey_category' => 'Survey',
            // 'start_date' => $request->start_date,
            // 'end_date' => $request->end_date,
            'published' => isset($request->published) == "on" ? 1 : 0,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );	
		$result = HrSurveyHeader::create($form_data);

		$form_question = array(
				'id_survey_header' => $result->id_survey_header,
            	'id_question_type' => $request->question[$data['counter']]['question_type'],
				'sequence' => $request->question[$data['counter']]['sequence'],
				'question' => $request->question[$data['counter']]['question'],
				'note' =>$request->question[$data['counter']]['note'],
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
		$question = HrSurveyQuestion::create($form_question);

		foreach ($request->period as $key => $val) {
			$surveyHistory = [
				'id_survey_header' => $result->id_survey_header,
				'start_date' => $request->startperiod[$key],
				'end_date' => $request->endperiod[$key],
				'status' => $request->statusperiod[$key],
				'id_company' => session('id_company'),
				'creation_date' => date('Y-m-d H:i:s'),
				'created_by' => session('id_user'),
			];
			$ins = DB::table('hr_survey_history')->insert($surveyHistory);
		}
		
        return response()->json(['status' => 'true', 'result'=>$result, 'question'=>$question]);
    }

	protected function update_survey(Request $request) {
	 	$this->validateSurvey($request);
	 	$data = [
            'id_survey_header' => $request->id_header,
			'counter' => $request->counter,
			'id_survey_question' => $request->id_survey_question
        ];

        $form_data = array(
            'description' => $request->description,
            'id_employee_request' => $request->id_employee_request,
            // 'id_question_type' => $request->id_question_type,
            'id_survey_type' => $request->id_survey_type,
            'survey_category' => 'Survey',
            // 'start_date' => $request->start_date,
            // 'end_date' => $request->end_date,
            'published' => isset($request->published) == "on" ? 1 : 0,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );	
        $result = HrSurveyHeader::where('id_survey_header', $data['id_survey_header'])->update($form_data);
		
		foreach ($request->period as $key => $val) {
			$idSurveyHistory = $request->id_survey_history[$key];
			$surveyHistory = [
				'id_survey_header' => $data['id_survey_header'],
				'start_date' => $request->startperiod[$key],
				'end_date' => $request->endperiod[$key],
				'status' => $request->statusperiod[$key],
				'id_company' => session('id_company'),
			];
			if(!is_null($idSurveyHistory)){
				$surveyHistory['update_date'] = date('Y-m-d H:i:s');
				$surveyHistory['updated_by'] = session('id_user');
				$up = DB::table('hr_survey_history')->where('id_survey_history', $idSurveyHistory)->update($surveyHistory);
			} else {
				$surveyHistory['creation_date'] = date('Y-m-d H:i:s');
				$surveyHistory['created_by'] = session('id_user');
				$ins = DB::table('hr_survey_history')->insert($surveyHistory);
			}
		}
		
		$form_question = array(
			'id_survey_header' => $data['id_survey_header'],
        	'id_question_type' => $request->question[$data['counter']]['question_type'],
			'sequence' => $request->question[$data['counter']]['sequence'],
			'question' => $request->question[$data['counter']]['question'],
			'note' =>$request->question[$data['counter']]['note'],
			'status' => 'A',
			'id_company' => session('id_company'),
			'created_by' => session('id_user'),
		);
		
		if($data['id_survey_question'] == ""){
			$question = HrSurveyQuestion::create($form_question);
			return response()->json(['status' => 'true', 'result'=>$data, 'question'=>$question]);
		}
		else{
			$x = [];
			$answer	= HrSurveyAnswer::where('id_survey_question', $data['id_survey_question'])->get();
			foreach($answer as $key=>$value){
				$x[] = $value;
			}
			HrSurveyQuestion::where('id_survey_question', $data['id_survey_question'])->update($form_question);
			return response()->json(['status' => 'true', 'result'=>$data, 'question'=>$data, 'answer'=>$x]);
		}
    }
		
	protected function save_survey_answer(Request $request) {
	 	$data = [
            'id_survey_question' => $request->id_question
        ];        
		$answer = [];
		foreach ($request->answer as $key => $value) {
			$form_answer = array(
				'id_survey_question' => $data['id_survey_question'],
				'id_answer' => $value['id_answer'],
				'reference_number' => '',
				'suggested_answer' => '',
				'is_corrected_answer' => isset($value['correct_input']) == "on" ? 1 : 0,
				'score_answer' => $value['score_input'],
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			$answer[] = HrSurveyAnswer::create($form_answer);
		}
        return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully !!', 'answer'=>$answer]);
    }
	
	protected function update_survey_answer(Request $request) {
	 	$data = [
            'id_survey_question' => $request->id_question
        ];
		$collect_answer = collect($request->answer)->groupBy('id_survey_answer')->toArray();
        $list_id_answer = array_filter(array_keys($collect_answer));
		
		$surveyAnswer       = '';
        if(count($list_id_answer) > 0){
            HrSurveyAnswer::whereNotIn('id_survey_answer', $list_id_answer)->where('id_survey_question', $request->id_question)->delete();
        } else {
            $getAnswer   = HrSurveyAnswer::where('id_survey_question', $request->id_question);
            if($getAnswer->first() != null){
                HrSurveyAnswer::where('id_survey_question', $request->id_question)->delete();
            }
        }
 
		foreach ($request->answer as $key => $value) {	
			if($value['id_survey_answer'] != ''){
			$form_answer = array(
				//	'id_survey_answer' => $value['id_survey_answer'],
					'id_answer' => $value['id_answer'],
					'reference_number' => '',
					'suggested_answer' => '',
					'is_corrected_answer' => isset($value['correct_input']) == "on" ? 1 : 0,
					'score_answer' => $value['score_input'],
					'status' => 'A',
					'id_company' => session('id_company'),
					'updated_by' => session('id_user'),
				);
				$answer[] = $value;
				HrSurveyAnswer::where('id_survey_question', $data['id_survey_question'])->where('id_survey_answer', $value['id_survey_answer'])->update($form_answer);
			}
			else{
				$form_answer = array(
					'id_survey_question' => $data['id_survey_question'],
					'id_answer' => $value['id_answer'],
					'reference_number' => '',
					'suggested_answer' => '',
					'is_corrected_answer' => isset($value['correct_input']) == "on" ? 1 : 0,
					'score_answer' => $value['score_input'],
					'status' => 'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				$answer[] = HrSurveyAnswer::create($form_answer);
			}
		}
	
        return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully !!', 'answer'=>$answer]);
    }

	public function destroy($id) {
		DB::beginTransaction();
        try {
	        $data = HrSurveyHeader::findOrFail($id);
	        $question = HrSurveyQuestion::where('id_survey_header', $id)->get();
			foreach($question as $value){
				try{
					HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->delete();
				} catch (\Exception $e) {
		            throw new \Exception('Tidak bisa dihapus, terdapat relasi pada (jawaban/department/branch/job grade)');           
		        }
			}
			try{
				HrSurveyQuestion::where('id_survey_header', $id)->delete();
				DB::table('relation_department_surveys')->where('id_survey_header', $id)->delete();
				DB::table('relation_regional_surveys')->where('id_survey_header', $id)->delete();
				DB::table('relation_branch_surveys')->where('id_survey_header', $id)->delete();
				DB::table('relation_jobgrade_surveys')->where('id_survey_header', $id)->delete();
				DB::table('relation_principal_surveys')->where('id_survey_header', $id)->delete();
				DB::table('hr_survey_history')->where('id_survey_header', $id)->delete();
	        	$data->delete();
			} catch (\Exception $e) {
	            throw new \Exception('Tidak bisa dihapus, Survey Header masih terdapat Relasi)');           
	        }
	        DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Survey Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

	public function destroy_question($id) {
        $question = HrSurveyQuestion::where('id_survey_question', $id)->get();
		foreach($question as $value){
	        HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->delete();
		}
		HrSurveyQuestion::where('id_survey_question', $id)->delete();
    }
	
	public function get_survey_edit(Request $request) {
        $data = [
            'id_survey_header' => $request->id_survey_header
        ];
        $result = HrSurveyHeader::get_survey_edit($data);
        return response()->json($result);
    }
	
	public function get_employee() {
        $result = HrSurveyHeader::get_employee();
        return response()->json($result);
    }
	
	public function get_company(Request $request) {
		if($request->user_only == 'true') {
			$result = HrSurveyHeader::get_company_user();
			return response()->json($result);
		}
        $result = HrSurveyHeader::get_company();
        return response()->json($result);
    }
	
	public function get_question_type() {
        $result = HrSurveyHeader::get_question_type();
        return response()->json($result);
    }

    public function get_category() {
        $result = HrSurveyHeader::get_category();
        return response()->json($result);
    }

	public function get_survey_type() {
        $result = HrSurveyHeader::get_survey_type();
        return response()->json($result);
    }

	public function get_answer() {
        $result = HrSurveyHeader::get_answer();
        return response()->json($result);
    }

    public function get_department(Request $request) {
    	$id_company = $request->id_company ?? session('id_company');

    	$data = DB::table('master_department as md')
                ->select('md.id_dept as id', 'md.description as text', 'md.department_code as code')
                ->where('md.id_company', $id_company)
                ->orderBy('md.description')
                ->get();
        return response()->json($data);
    }

    public function get_region(Request $request) {
    	$id_company = $request->id_company ?? session('id_company');

    	$data = DB::table('master_region as mr')
                ->select('mr.id_region as id', 'mr.description as text', 'mr.region_code as code')
                ->where('mr.id_company', $id_company)
                ->orderBy('mr.description')
                ->get();
        return response()->json($data);
    }

    public function get_branch(Request $request){
        $id_region = $request->id_region ?? null ;

        $data = DB::table('master_branch as mb')
                ->select('mb.id_branch', 'mb.branch_code', 'mb.description')
                ->where('mb.id_company', '=', session('id_company'))
                ->orderBy('mb.description');

        if(is_array($id_region) && count($id_region) > 0){
            $data->whereIn('mb.id_region', $id_region);
        } else if($id_region && !is_array($id_region)){
            $data->where('mb.id_region', '=', $id_region);
        }

        $get = $data->get();
        return response()->json($get);
    }

	public function get_principal(Request $request) {
		$principals = MasterPrincipal::where('status', 'A')->where('id_company', session('id_company'))->get(['id_principal as id', 'description as text']);
		return response()->json($principals);
	}

	public function duplicate_survey(Request $request) {
		$request->validate([
			'id_survey_header' => 'required',
			'id_company' => 'required',
		]);
		DB::beginTransaction();
		try {
			$survey = HrSurveyHeader::findOrFail($request->id_survey_header)->toArray();
			$questionType = @MasterGeneralData::where('code', MasterGeneralData::find($survey['id_question_type'])->code)->where('id_company', $request->id_company)->first()->id_general_data;
			$surveyType = @MasterGeneralData::where('code', MasterGeneralData::find($survey['id_survey_type'])->code)->where('id_company', $request->id_company)->first()->id_general_data;
			$idSurveyHeader = $survey['id_survey_header'];
			unset($survey['id_survey_header']);
			$survey = array_merge($survey, [
				'description' => $survey['description'].' - Copy',
				'id_question_type' => $questionType,
				'id_employee_request' => DB::table('hr_employee')->where('id_user', session('id_user'))->where('status', 'A')->first()->id_employee,
				'id_survey_type' =>$surveyType,
				'published' => false,
				'id_company' => $request->id_company,
				'created_by' => session('id_user'),
				'updated_by' => null,
				'creation_date' => null,
				'update_date' => null,
			]);
			$newSurvey = HrSurveyHeader::create($survey);
			$surveyQuestions = HrSurveyQuestion::where('id_survey_header', $idSurveyHeader)->get();
			foreach($surveyQuestions as $question) {
				$question = $question->toArray();
				$questionId = $question['id_survey_question'];
				$questionType = MasterGeneralData::where('code', MasterGeneralData::findOrFail($question['id_question_type'])->code)->where('id_company', $request->id_company)->first()->id_general_data;
				unset($question['id_survey_question']);
				$question = array_merge($question, [
					'id_survey_header' => $newSurvey->id_survey_header,
					'id_question_type' => $questionType,
					'id_question_group' => null,
					'id_company' => $request->id_company,
					'created_by' => session('id_user'),
					'updated_by' => null,
					'creation_date' => null,
					'update_date' => null,
				]);
				$question = HrSurveyQuestion::create($question);
				$answers = HrSurveyAnswer::where('id_survey_question', $questionId)->get();
				$masterAnswers = DB::table('master_survey_answer')
								->whereIn('id_answer', $answers->pluck('id_answer')->unique())
								->get();
				$masterAnswerCodes = $masterAnswers->pluck('code');
				$newMasterAnswers = [];
				foreach($masterAnswers as $masterAnswer) {
					$masterAnswer = (array)$masterAnswer;
					unset($masterAnswer['id_answer']);
					$masterAnswer['id_company'] = $request->id_company;
					$masterAnswer['created_by'] = session('id_user');
					if(!DB::table('master_survey_answer')->where('id_company', $request->id_company)->where('code', $masterAnswer['code'])->first()) {
						$newMasterAnswers[] = DB::table('master_survey_answer')->insert($masterAnswer);
					}
				}
				
				foreach($answers as $answer) {
					$answer = $answer->toArray();
					$origMaster = DB::table('master_survey_answer')
								->where('id_answer', $answer['id_answer'])
								->first();
					$master = DB::table('master_survey_answer')
								->where('id_company', $request->id_company)
								->where('code', $origMaster->code)
								->first();
					unset($answer['id_survey_answer']);
					$answer = array_merge($answer, [
						'id_survey_question' => $question->id_survey_question,
						'id_answer' => $master->id_answer,
						'id_company' => $request->id_company,
						'created_by' => session('id_user'),
					]);
					HrSurveyAnswer::create($answer);
				}
			}
			DB::commit();
			return response()->json([
				'message' => 'Survey has been duplicated successfully!',
			]);
		} catch(\Exception $e) {
			DB::rollBack();
			return response()->json([
				'message' => $e->getMessage(),
			], 500);
		}
	}

}
