<?php
namespace App\Http\Controllers\Recruitment\Interview;

use App\Models\Recruitment\Candidate\Candidate;
use App\Models\Recruitment\Candidate\CandidateData;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\Recruitment\HiringRequest\HiringDetail;
use App\Models\Recruitment\MasterInterview\AnswerHeader;
use App\Models\Recruitment\MasterInterview\AnswerDetail;
use App\Models\Recruitment\MasterInterview\RelationAnswer;
use App\Models\Recruitment\Candidate\AppliedHistory;
use App\Models\Employee\Employee\Employee;
use App\Models\Setting\ResponsibilityUser\MasterUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InterviewQuestionController extends Controller {

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
            $data = CandidateData::get_can_interview($group_branch);
			// dd($data);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
						$onclick = "loadprofile(".$data->id_candidate.",".$data->id_applied_candidate.",".$data->id_recruitment_question_group.")";
						$onclickPrint = "printPdf(".$data->id_candidate.",".$data->id_applied_candidate.",".$data->id_recruitment_question_group.")";
						$button = '<button type="button" name="edit_can" id="' . $data->id_candidate . '" onclick="'.$onclick.'" class="edit_can btn btn-success btn-sm" title="Interview"><span class="fas fa-list-alt"></span></button> ';
                        if($data->code_stage == "REF") $button .= '<button type="button" name="print_can" id="' . $data->id_candidate . '" onclick="'.$onclickPrint.'" class="print_can btn btn-success btn-sm" title="Print"><span class="fas fa-file-pdf-o"></span></button>';
						return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
		return view('recruitment.personality_assessment.interview_question.index');
    }
	
	public function index_summary(Request $request) {    	
        if ($request->ajax()) {
			$user = MasterUser::where('id_user', session('id_user'))->where('status','A')->first();
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
            $data = CandidateData::get_can_interview_summary($group_branch,$user->access_group,$c);
            return DataTables::of($data)
                    ->addIndexColumn()
					->addColumn('action', function($data) {
						$onclickPrint = "printPdf(".$data->id_candidate.",".$data->id_applied_candidate.",".$data->id_recruitment_question_group.")";
						$onclick = "loadprofile(".$data->id_candidate.",".$data->id_applied_candidate.",".$data->id_recruitment_question_group.")";
						$button = '<button type="button" name="edit_can" id="' . $data->id_candidate . '" onclick="'.$onclick.'" class="edit_can btn btn-success btn-sm" title="Interview"><span class="fas fa-list-alt"></span></button> ';
						$button .= '<button type="button" name="print_can" id="' . $data->id_candidate . '" onclick="'.$onclickPrint.'" class="print_can btn btn-success btn-sm" title="Print"><span class="fas fa-file-pdf-o"></span></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
		return view('recruitment.personality_assessment.interview_question_summary.index');
    }
	
	protected function validateQues(Request $request) {
        $arr_form_validate = [
            'interview_date' => 'required',
        //    'id_conclusion' => 'required',
        ];
	
        $arr_msg_form_validate = [
            'interview_date.required' => 'The Interview Date field is required',
        //    'id_conclusion.required' => 'The Conclusion field is required',
        ];
		
		// Cek apakah ada soal dari grup REFCHECK PERUSAHAAN 1 atau SOCIAL MEDIA CHECK
		// Jika ada, maka soal dari grup REFCHECK PERUSAHAAN 1 dan SOCIAL MEDIA CHECK dijadikan required
		// $requiredQuestions = DB::table('master_recruitment_question')
		// 						->where('competency_group', 'REFERENCE CHECK PERUSAHAAN 1')
		// 						->orWhere('category_group', 'SOCIAL MEDIA CHECK')
		// 						->get(['id_recruitment_question as id', 'description']);
		// $listId = collect($requiredQuestions)->pluck('id');
		// if(!empty(array_intersect($listId->toArray(), $request->soal))) {
		// 	foreach($requiredQuestions as $req) {
		// 		$arr_form_validate['ans_'.$req->id] = 'required';
		// 		$arr_msg_form_validate['ans_'.$req->id.'.required'] = $req->description.' answer field is required.';
		// 	}
		// }
		
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }
	
	protected function update(Request $request) {
		$this->validateQues($request);
		try{
			DB::beginTransaction();	
			$score = 0;
			$z = [];
			if($request->soal && $request->interview_date != null){
				$form_answer = array(
					'id_recruitment_question_group' => $request->id_recruitment_question_group,
					'id_candidate' => $request->id_candidate,
					'id_applied_candidate' => $request->id_applied,
					'interview_date' => $request->interview_date,
					'id_conclusion' => $request->id_conclusion,
					'notes' => strip_tags($request->int_notes),
					'id_company' => session('id_company'),
				);
			//	dd($form_answer);
				$resapp = AnswerHeader::where('id_candidate', $request->id_candidate)->where('id_recruitment_question_group', $request->id_recruitment_question_group)->get();
				if($resapp->count() == 0){
					$form_answer['created_by'] = session('id_user');
					$res =	AnswerHeader::create($form_answer);  
				}
				else{
					$form_answer['updated_by'] = session('id_user');
					$res =	AnswerHeader::where('id_candidate',$request->id_candidate)->where('id_recruitment_question_group',$request->id_recruitment_question_group)->update($form_answer); 
				}
				$res_first = AnswerHeader::where('id_candidate', $request->id_candidate)->where('id_recruitment_question_group', $request->id_recruitment_question_group)->first();
				$countAnswer = [];
				foreach ($request->soal as $key => $id_question) {
					$answer = 'answers_'.$id_question;
					$x[$key]['id'] = $id_question;
					$x[$key]['question_type'] = $request->question_type[$key];
				/*	
					if($request->question_type[$key] == 'Single_Answer'){
						if(!$request->$answer){
							$countAnswer[] = $request->soal_item[$key].' (Belum Dijawab)';
						}	
					}
				*/
					$ansnote = 'ans_'.$id_question;					
					$form_answer_detail = array(
						'id_recruitment_question' => $id_question,
						'id_recruitment_answer_header' => $res_first->id_recruitment_answer_header,
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
					$det_first = AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $res_first->id_recruitment_answer_header)->first();
					if(!$det_first){
						$res =	AnswerDetail::create($form_answer_detail);  
					}
					else{
						$res =	AnswerDetail::where('id_recruitment_question', $id_question)->where('id_recruitment_answer_header', $res_first->id_recruitment_answer_header)->update($form_answer_detail);
					}
					
				}
			/*	
				if(count($countAnswer)){
					$showerr = collect($countAnswer)->implode("\n");
                    throw new \Exception($showerr);
                }
			*/
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
							$form_update_applied = array(
								'status' => 'Pass',
								'updated_by' => session('id_user'),
							);
							Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->update($form_update_applied);
							$form_update_applied['end_date']	=  date('Y-m-d H:i:s' );
							AppliedHistory::where('id_applied_candidate',$request->id_applied)->where('id_candidate_status',$request->status_stage)->update($form_update_applied);
						}
					}
					else if($val->code == 'Considered'){
						if($total_score >= 70 && $total_score < 80){
							$id_conclusion = $val->id;
							$form_update_applied = array(
								'status' => 'Pass',
								'updated_by' => session('id_user'),
							);
							Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->update($form_update_applied);
							$form_update_applied['end_date']	=  date('Y-m-d H:i:s' );
							AppliedHistory::where('id_applied_candidate',$request->id_applied)->where('id_candidate_status',$request->status_stage)->update($form_update_applied);
						}
					}
					else if($val->code == 'Not_Recommended'){
						if($total_score < 70){
							$id_conclusion = $val->id;
							$form_update_applied = array(
								'status' => 'Failed',
								'updated_by' => session('id_user'),
							);
							Candidate::where('id_applied_candidate', $request->id_applied)->where('id_candidate', $request->id_candidate)->update($form_update_applied);
							$form_update_applied['end_date']	=  date('Y-m-d H:i:s' );
							AppliedHistory::where('id_applied_candidate',$request->id_applied)->where('id_candidate_status',$request->status_stage)->update($form_update_applied);
						}
					}			
				}
			//	dd($id_conclusion);
				$form_update_header = array(
					'total_score' => $total_score,
					'id_conclusion' => $id_conclusion,
					'updated_by' => session('id_user'),
				);
				AnswerHeader::where('id_candidate',$request->id_candidate)->where('id_recruitment_question_group',$request->id_recruitment_question_group)->update($form_update_header); 
			}
        DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Update Successfully !!']);
        } catch (\Exception $e) {
            DB::rollBack();
        //    Log::error($e);
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);           
        }
    }
	
   
	public function get_edit_question(Request $request) {
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_group' => $request->id_group,
		);
        $result = CandidateData::get_edit_question($data);
        return response()->json($result);
    }
	
	public function get_edit_question_summary(Request $request) {
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_group' => $request->id_group,
		);
        $result = CandidateData::get_edit_question_summary($data);
        return response()->json($result);
    }
	
	public function get_question(Request $request) {
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_candidate_status' => $request->id_candidate_status,
		);
        $result = CandidateData::get_question($data);

        return response()->json($result);
    }
	public function get_stage(Request $request) {
        $result = CandidateData::get_stage();
        return response()->json($result);
    }
	public function get_conclusion(Request $request) {
        $result = CandidateData::get_conclusion();
        return response()->json($result);
    }

	public function check_stage(Request $request) {
		$data = [
            'code_stage' => $request->code_stage
        ];	
        $result = CandidateData::check_stage($data);
	//	dd($result);
        return response()->json($result);
    }

	public function print_interview(Request $request) {
		$request->validate([
			'id_candidate' => 'required',
			'id_applied' => 'required',
			'id_group' => 'required'
		]);

		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_group' => $request->id_group,
		);
        $result = CandidateData::get_edit_question($data);
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_candidate_status' => $result['applied'][0]['id_candidate_status'],
		);
        $result['questions'] = collect(CandidateData::get_question($data))->groupBy('competency_group');
		foreach($result['questions'] as $key => $val) {
			$result['questions'][$key] = $val->groupBy('category_group');
		}
		// dd($result);
		return \PDF::loadView('recruitment.personality_assessment.interview_question.print', $result)->setPaper('A4','potrait')->stream();
		
	}

	public function print_bei(Request $request) {
		$request->validate([
			'id_candidate' => 'required',
			'id_applied' => 'required',
			'id_group' => 'required'
		]);

		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_group' => $request->id_group,
		);
        $result = CandidateData::get_edit_question_summary($data);
		$data = array(
			'id_candidate' => $request->id_candidate,
			'id_applied' => $request->id_applied,
			'id_candidate_status' => $result['applied'][0]['id_candidate_status'],
		);
		// dd($result)
        $result['questions'] = collect(CandidateData::get_question($data))->groupBy('competency_group');
		foreach($result['questions'] as $key => $val) {
			$result['questions'][$key] = $val->groupBy('category_group');
		}
		// dd($result);
		return \PDF::loadView('recruitment.personality_assessment.interview_question.print_bei', $result)->setPaper('A4','potrait')->stream();
	}
}
