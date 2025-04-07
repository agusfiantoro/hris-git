<?php
namespace App\Http\Controllers\TalentManagement\TalentDevelopment;

use App\Models\TalentManagement\TalentFunctional\TalentFunctional;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Models\Recruitment\MasterInterview\AnswerHeader;
use App\Models\Recruitment\MasterInterview\AnswerDetail;
use App\Models\Recruitment\MasterInterview\RelationAnswer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class TalentFunctionalController extends Controller {

	public function index(Request $request) {    	
        if ($request->ajax()) {
			$path_url = null;
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
			$filePath = '';
			$data = [];
            $result = TalentFunctional::get_emp_interview($group_branch,$path_url);
			if(count($result) > 0){
				$filePath = url('project/storage/app/public/upload/photo');
				foreach($result as $key=>$val){
					$data[] = $val;
					$data[$key]->image_length = strlen($val->photo);
					$data[$key]->filePath = $filePath;
				}
			}
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
					//	$onclick = '';
						$onclick = "loadprofile(".$data->id_employee.",".$data->id_recruitment_question_group.")";
						$button = '<button type="button" name="edit_can" id="'.$data->id_employee.'" onclick="'.$onclick.'" class="edit_can btn btn-success btn-sm" title="Interview"><span class="fas fa-list-alt"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
		return view('talent_management.talent_development.interview_question.index');
    }
	
	public function index_summary(Request $request) {    	
        if ($request->ajax()) {
			$path_url = $request->path();
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
			$filePath = '';
			$data = [];
            $result = TalentFunctional::get_emp_interview($group_branch,$path_url);
			if(count($result) > 0){
				$filePath = url('project/storage/app/public/upload/photo');
				foreach($result as $key=>$val){
					$data[] = $val;
					$data[$key]->image_length = strlen($val->photo);
					$data[$key]->filePath = $filePath;
				}
			}
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
					//	$onclick = '';
						$onclick = "loadprofile(".$data->id_employee.",".$data->id_recruitment_question_group.")";
						$button = '<button type="button" name="edit_can" id="'.$data->id_employee.'" onclick="'.$onclick.'" class="edit_can btn btn-warning btn-sm" title="Interview"><span class="fas fa-eye" style="color:white;"></span></button> ';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
		return view('talent_management.talent_development.interview_question_summary.index');
    }
	
	public function get_edit_question(Request $request) {
		$data = array(
			'id_employee' => $request->id_employee,
			'id_group' => $request->id_group,
		);
        $result = TalentFunctional::get_edit_question($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_question(Request $request) {
		$data = array(
			'id_employee' => $request->id_employee,
			'id_question_group' => $request->id_question_group,
		);
        $result = TalentFunctional::get_question($data);
        return response()->json($result);
    }
	
	protected function validateQues(Request $request) {
        $arr_form_validate = [
            'interview_date' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'interview_date.required' => 'The Interview Date field is required',
        ];
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function update(Request $request) {
	//	dd($request->all());
		$this->validateQues($request);
		try{
			DB::beginTransaction();	
			$score = 0;
			$z = [];
			if($request->soal && $request->interview_date != null){
				$getGradeDept = TalentFunctional::get_grade_dept($request->id_employee);
				$form_answer = array(
					'id_recruitment_question_group' => $request->id_recruitment_question_group,
					'id_employee' => $request->id_employee,
					'id_department' => $getGradeDept->id_dept,
					'id_job_grade' => $getGradeDept->id_job_grade,
					'interview_date' => $request->interview_date,
					'assessment_type' => 'Internal',
				//	'id_conclusion' => $request->id_conclusion,
					'notes' => $request->int_notes,
					'updated_by' => session('id_user'),
					'id_company' => session('id_company'),
				);
								
				$res =	AnswerHeader::where('id_recruitment_answer_header',$request->id_recruitment_answer_header)->where('id_employee',$request->id_employee)->where('id_recruitment_question_group',$request->id_recruitment_question_group)->update($form_answer); 
				
				foreach ($request->soal as $key => $id_question) {
					$answer = 'answers_'.$id_question;
					$x[$key]['id'] = $id_question;
					$x[$key]['question_type'] = $request->question_type[$key];
				
					$ansnote = 'ans_'.$id_question;					
					$form_answer_detail = array(
						'id_recruitment_question' => $id_question,
						'id_recruitment_answer_header' => $request->id_recruitment_answer_header,
					//	'essay_answer' => $request->$ansnote,
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					
					
					if(is_array($request->$answer)){							
						foreach ($request->$answer as $i => $id_answer) {
							$form_answer_detail['id_recruitment_answer'] = $id_answer;	

							$rel = RelationAnswer::where('id_recruitment_question', $id_question)->where('id_recruitment_answer', $id_answer)->first();
							$score += $rel->weight_score;
						}
							$z[] = $request->$answer;
					}
					else{
						$form_answer_detail['essay_answer'] = @$request->$ansnote;						
					}
					$det_first = AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $request->id_recruitment_answer_header)->first();
					if(!$det_first){
						AnswerDetail::create($form_answer_detail);  
					}
					else{
						AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $request->id_recruitment_answer_header)->update($form_answer_detail);
					}
					
				}
			}
		//	dd(count($z));
			if(count($z) > 0){
				$conclusion = CandidateData::get_conclusion();
				$total_score = round($score/count($z),2)*100;
			//	dd($total_score);
				$id_conclusion = 0;
				foreach($conclusion as $key=>$val){
					if($val->code == 'Recommended'){
						if($total_score >= 80){
							$id_conclusion = $val->id;
						}
					}
					else if($val->code == 'Considered'){
						if($total_score >= 70 && $total_score < 80){
							$id_conclusion = $val->id;
						}
					}
					else if($val->code == 'Not_Recommended'){
						if($total_score < 70){
							$id_conclusion = $val->id;
						}
					}			
				}
			//	dd($id_conclusion);
				$form_update_header = array(
					'total_score' => $total_score,
					'id_conclusion' => $id_conclusion,
					'updated_by' => session('id_user'),
				);
				AnswerHeader::where('id_recruitment_answer_header',$request->id_recruitment_answer_header)->where('id_employee',$request->id_employee)->where('id_recruitment_question_group',$request->id_recruitment_question_group)->update($form_update_header); 
			}
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Update Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }
	
	
}
