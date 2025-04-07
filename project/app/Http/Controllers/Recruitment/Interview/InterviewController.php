<?php
namespace App\Http\Controllers\Recruitment\Interview;

use App\Models\Recruitment\MasterInterview\MasterInterview;
use App\Models\Recruitment\MasterInterview\MasterQuestion;
use App\Models\Recruitment\MasterInterview\MasterAnswer;
use App\Models\Recruitment\MasterInterview\RelationAnswer;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class InterviewController extends Controller {

     public function index(Request $request) {
        if ($request->ajax()) {
            $data = MasterInterview::getdata();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_recruitment_question_group . '" class="edit btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';

                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_recruitment_question_group . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
        return view('recruitment.personality_assessment.master_interview_question.index');
    }
	
	public function index_answer(Request $request) {
        if ($request->ajax()) {
            $data = MasterInterview::getdata_answer();
            return DataTables::of($data)
                            ->addIndexColumn()
                            ->addColumn('', function($data) {
                                $a = '';
                                return $a;
                            })
                            ->addColumn('action', function($data) {
                                $button = '<button type="button" name="edit" id="' . $data->id_recruitment_answer . '" class="edit_answer btn btn-primary btn-sm" title="Edit"><span class="fas fa-edit"></span></button> ';

                                $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_recruitment_answer . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                                return $button;
                            })
                            ->rawColumns(['action'])
                            ->make(true);
        }
    }
	
	
	protected function validateInterview(Request $request) {		
		$arr_form_validate = [
            'description' => 'required|string',
			'question.*.question_type' => 'required',
			'question.*.sequence' => 'required',
            'question.*.question' => 'required|string',
			'question.*.note' => 'nullable|string',
        ];
        $arr_msg_form_validate = [
            'description.required' => 'Description is required',
			'question.*.sequence.required' => 'The Sequence field is required',
            'question.*.question.required' => 'The Question field is required',
            'question.*.question_type.required' => 'The Question Type is required',
        ];
		
		if ($request->post('question') == null) {
            $validate_question = ['table_question' => 'required|string'];
            $validate_msg_question = ['table_question.required' => 'Table Question Detail cannot empty'];
            $arr_form_validate = array_merge($arr_form_validate, $validate_question);
            $arr_msg_form_validate = array_merge($arr_msg_form_validate, $validate_msg_question);
        }
       
		$request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);		
	}
	
	protected function save(Request $request) {
		$this->validateInterview($request);
		DB::beginTransaction();
		try{
			$form_data = array(
	            'description' => $request->description,
	            'id_question_group' => $request->rec_stage,
	            'question_type' => $request->module_type,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'created_by' => session('id_user'),
	        );	
			$result = MasterInterview::create($form_data);
			foreach ($request->question as $key => $value) {
				$form_question = array(
					'id_recruitment_question_group' => $result->id_recruitment_question_group,
	            	'id_question_type' => $value['question_type'],
					'sequence' => $value['sequence'],
					'competency_group' => $value['competency_group'],
					'category_group' => $value['category_group'],
					'description' => $value['question'],
					'notes' =>$value['note'],
					'status' => 'A',
					'id_company' => session('id_company'),
					'created_by' => session('id_user'),
				);
				$question = MasterQuestion::create($form_question);
				if($question){
					if(@$value['suggested_answer']){
						foreach (@$value['suggested_answer'] as $k => $item) {
							$form_answer = array(
								'id_recruitment_question' => $question->id_recruitment_question,
								'id_recruitment_answer' => $item,
								'is_corrected_answer' => isset($value['correct']) == "on" ? true : false,
								'weight_score' => null,
								'status' => 'A',
								'id_company' => session('id_company'),
								'created_by' => session('id_user'),
							);
							$answer= RelationAnswer::create($form_answer);
						}
					}
				}
			}
			
		 DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Master Interview saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
		
	}
	
	protected function update(Request $request) {
	//	dd($request->all());
        $this->validateInterview($request);
        DB::beginTransaction();
        try {
	        $form_data = array(
	            'description' => $request->description,
	            'id_question_group' => $request->rec_stage,
	            'question_type' => $request->module_type,
	            'status' => $request->status,
	            'id_company' => session('id_company'),
	            'updated_by' => session('id_user'),
	        );	
			$result = MasterInterview::where('id_recruitment_question_group', $request->id_recruitment_question_group)->update($form_data);

			$collect_question = collect($request->question)->groupBy('id_recruitment_question')->toArray();
	        $list_question = array_filter(array_keys($collect_question));
	        if(count($list_question) > 0){
	            MasterQuestion::whereNotIn('id_recruitment_question', $list_question)->where('id_recruitment_question_group', $request->id_recruitment_question_group)->delete();
	        } else {
	            $question   = MasterQuestion::where('id_recruitment_question_group', $request->id_recruitment_question_group);
	            if($question->first() != null){
	                MasterQuestion::where('id_recruitment_question_group', $request->id_recruitment_question_group)->delete();
	            }
	        }
			
			foreach ($request->question as $key => $value) {
	            if ($value['id_recruitment_question'] == "") {
	            	$form_question = array(
						'id_recruitment_question_group' => $request->id_recruitment_question_group,
		            	'id_question_type' => $value['question_type'],
						'sequence' => $value['sequence'],
						'competency_group' => $value['competency_group'],
						'category_group' => $value['category_group'],
						'description' => $value['question'],
						'notes' =>$value['note'],
						'status' => 'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					$question = MasterQuestion::create($form_question);
					if($question){
						if(@$value['suggested_answer']){
							foreach (@$value['suggested_answer'] as $k => $item) {
								$form_answer = array(
									'id_recruitment_question' => $question->id_recruitment_question,
									'id_recruitment_answer' => $item,									
									'is_corrected_answer' => isset($value['correct']) == "on" ? true : false,
									'weight_score' => null,
									'status' => 'A',
									'id_company' => session('id_company'),
									'created_by' => session('id_user'),
								);
								$answer= RelationAnswer::create($form_answer);
							}
						}
					}
	            } else {					
						MasterQuestion::where('id_recruitment_question', $value['id_recruitment_question'])->update(array(
							'id_question_type' => $value['question_type'],
							'sequence' => $value['sequence'],
							'competency_group' => $value['competency_group'],
							'category_group' => $value['category_group'],
							'description' => $value['question'],
							'notes' =>$value['note'],
							'status' => 'A',
							'id_company' => session('id_company'),
							'updated_by' => session('id_user'),
						));
					$quest_first = MasterInterview::get_question_first($value['question_type']);
					if($quest_first['code'] == 'Essay'){
						RelationAnswer::where('id_recruitment_question', $value['id_recruitment_question'])->delete();
					}
					else{
						$getAnswer =  RelationAnswer::where('id_recruitment_question', $value['id_recruitment_question'])->first();
						if(!$getAnswer){
							if(@$value['suggested_answer']){
								foreach (@$value['suggested_answer'] as $k => $item) {
									$form_answer = array(
										'id_recruitment_question' => $value['id_recruitment_question'],
										'id_recruitment_answer' => $item,
										'is_corrected_answer' => isset($value['correct']) == "on" ? true : false,
										'weight_score' => null,
										'status' => 'A',
										'id_company' => session('id_company'),
										'created_by' => session('id_user'),
									);
									$answer= RelationAnswer::create($form_answer);
								}
							}
						} else {
							if($value['id_recruitment_question']){
								if(@$value['suggested_answer']){
									foreach (@$value['suggested_answer'] as $k => $item) {
										$form_answer = array(
											'id_recruitment_answer' => $item,
											'status' => 'A',
											'id_company' => session('id_company'),
											'created_by' => session('id_user'),
										);
										$answer= RelationAnswer::where('id_recruitment_question', $value['id_recruitment_question'])->where('id_company', session('id_company'))->where('id_recruitment_answer',$item)->update($form_answer);
									}
								}
								
							}
						}
					}
	            }				
		   }
	       	DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Master Interview Update Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }
		
	protected function save_interview(Request $request) {
		$this->validateInterview($request);
	//	dd($request->all());
		$data = [
			'counter' => $request->counter
        ];
        $form_data = array(
            'description' => $request->description,
            'id_question_group' => $request->rec_stage,
            'question_type' => $request->module_type,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );	
		$result = MasterInterview::create($form_data);
		$form_question = array(
				'id_recruitment_question_group' => $result->id_recruitment_question_group,
            	'id_question_type' => $request->question[$data['counter']]['question_type'],
				'sequence' => $request->question[$data['counter']]['sequence'],
				'competency_group' => $request->question[$data['counter']]['competency_group'],
				'category_group' => $request->question[$data['counter']]['category_group'],
				'description' => $request->question[$data['counter']]['question'],
				'notes' =>$request->question[$data['counter']]['note'],
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
		$question = MasterQuestion::create($form_question);

        return response()->json(['status' => 'true', 'result'=>$result, 'question'=>$question]);
    }
	
	protected function update_interview(Request $request) {
	 	$this->validateInterview($request);
	 	$data = [
            'id_recruitment_question_group' => $request->id_recruitment_question_group,
			'counter' => $request->counter,
			'id_recruitment_question' => $request->id_recruitment_question
        ];
        $form_data = array(
            'description' => $request->description,
            'id_question_group' => $request->rec_stage,
            'question_type' => $request->module_type,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );	
        $result = MasterInterview::where('id_recruitment_question_group', $data['id_recruitment_question_group'])->update($form_data);
		
		$form_question = array(
			'id_recruitment_question_group' => $data['id_recruitment_question_group'],
        	'id_question_type' => $request->question[$data['counter']]['question_type'],
			'sequence' => $request->question[$data['counter']]['sequence'],
			'competency_group' => $request->question[$data['counter']]['competency_group'],
			'category_group' => $request->question[$data['counter']]['category_group'],
			'description' => $request->question[$data['counter']]['question'],
			'notes' =>$request->question[$data['counter']]['note'],
			'status' => 'A',
			'id_company' => session('id_company'),
			'created_by' => session('id_user'),
		);
		
		if($data['id_recruitment_question'] == ""){
			$question = MasterQuestion::create($form_question);
			return response()->json(['status' => 'true', 'result'=>$data, 'question'=>$question]);
		}
		else{
			$x = [];
			$answer	= RelationAnswer::where('id_recruitment_question', $data['id_recruitment_question'])->get();
			foreach($answer as $key=>$value){
				$x[] = $value;
			}
			MasterQuestion::where('id_recruitment_question', $data['id_recruitment_question'])->update($form_question);
			return response()->json(['status' => 'true', 'result'=>$data, 'question'=>$data, 'answer'=>$x]);
		}
    }
		
	protected function validateAnswer(Request $request) {		
		$arr_form_validate = [
            'sequence' => 'required',
            'description_answer' => 'required',
        ];
        $arr_msg_form_validate = [
            'sequence.required' => 'Sequence Answer is required',
            'description_answer.required' => 'Description Answer is required',
        ];
       
        $request->validate($arr_form_validate, $arr_msg_form_validate);		
	}
	
	protected function save_answer(Request $request) {
		$this->validateAnswer($request);
        $form_data = array(
            'sequence' => $request->sequence,
            'description' => $request->description_answer,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
		MasterAnswer::create($form_data);
		
        return response()->json(['status' => 'true', 'message' => 'Master Answer Saved Successfully !!']);
    }
	
	protected function update_answer(Request $request) {
       $this->validateAnswer($request);

        $form_data = array(
            'sequence' => $request->sequence,
            'description' => $request->description_answer,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );
        $getAnswer = MasterAnswer::findOrFail($request->id_recruitment_answer)->update($form_data);
       		
        return response()->json(['status' => 'true', 'message' => 'Master Answer Updated Successfully !!']);
    }
	
	protected function save_interview_answer(Request $request) {
	 	$data = [
            'id_recruitment_question' => $request->id_question
        ];        
		$answer = [];
		foreach ($request->answer as $key => $value) {
			$form_answer = array(
				'id_recruitment_question' => $data['id_recruitment_question'],
				'id_recruitment_answer' => $value['id_recruitment_answer'],
				'is_corrected_answer' => isset($value['correct']) == "on" ? 1 : 0,
				'weight_score' => $value['score'],
				'status' => 'A',
				'id_company' => session('id_company'),
				'created_by' => session('id_user'),
			);
			$answer[] = RelationAnswer::create($form_answer);
		}
        return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully !!', 'answer'=>$answer]);
    }
	
	protected function update_interview_answer(Request $request) {
		DB::beginTransaction();
        try {
			$data = [
				'id_recruitment_question' => $request->id_question
			];
			$collect_answer = collect($request->answer)->groupBy('id_recruitment_answer')->toArray();
			$list_id_answer = array_filter(array_keys($collect_answer));
			if(count($list_id_answer) > 0){
				RelationAnswer::whereNotIn('id_recruitment_answer', $list_id_answer)->where('id_recruitment_question', $request->id_question)->delete();
			} else {
				$getAnswer   = RelationAnswer::where('id_recruitment_question', $request->id_question);
				if($getAnswer->first() != null){
					RelationAnswer::where('id_recruitment_question', $request->id_question)->delete();
				}
			}
		//	dd($request->answer);
			foreach ($request->answer as $key => $value) {	
				if($value['id_recruitment_question_answer'] != ''){
				$form_answer = array(
						'id_recruitment_answer' => $value['id_recruitment_answer'],
						'is_corrected_answer' => isset($value['correct']) == "on" ? 1 : 0,
						'weight_score' => $value['score'],
						'status' => 'A',
						'id_company' => session('id_company'),
						'updated_by' => session('id_user'),
					);
					$answer[] = $value;
					RelationAnswer::where('id_recruitment_question', $data['id_recruitment_question'])->where('id_recruitment_answer', $value['id_recruitment_answer'])->update($form_answer);
				}
				else{
					$form_answer = array(
						'id_recruitment_question' => $data['id_recruitment_question'],
						'id_recruitment_answer' => $value['id_recruitment_answer'],
						'is_corrected_answer' => isset($value['correct']) == "on" ? 1 : 0,
						'weight_score' => $value['score'],
						'status' => 'A',
						'id_company' => session('id_company'),
						'created_by' => session('id_user'),
					);
					$answer[] = RelationAnswer::create($form_answer);
				}
			}
		 DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully !!', 'answer'=>$answer]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }	
    }

	
	public function destroy_answer($id) {
		DB::beginTransaction();
        try {
	        $data = MasterAnswer::findOrFail($id);
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
	
	public function destroy($id) {
		DB::beginTransaction();
        try {
	        $data = MasterInterview::findOrFail($id);
	        $question = MasterQuestion::where('id_recruitment_question_group', $id)->get();
			foreach($question as $value){
				try{
					RelationAnswer::where('id_recruitment_question', $value['id_recruitment_question'])->delete();
				} catch (\Exception $e) {
		            throw new \Exception('Tidak bisa dihapus, terdapat relasi pada table');           
		        }
			}
			try{
				MasterQuestion::where('id_recruitment_question_group', $id)->delete();
	        	$data->delete();
			} catch (\Exception $e) {
	            throw new \Exception('Tidak bisa dihapus, terdapat relasi table');           
	        }
	        DB::commit();
	        return response()->json(['status' => 'true', 'message' => 'Master Interview Deleted Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

	public function destroy_question($id) {
        $question = MasterQuestion::where('id_recruitment_question', $id)->get();
		foreach($question as $value){
	        RelationAnswer::where('id_recruitment_question', $value['id_recruitment_question'])->delete();
		}
		MasterQuestion::where('id_recruitment_question', $id)->delete();
    }
	public function get_edit_interview(Request $request) {
        $data = [
            'id_recruitment_question_group' => $request->id_recruitment_question_group
        ];
        $result = MasterInterview::get_edit_interview($data);
	//	dd($result);
        return response()->json($result);
    }
	
	public function get_edit_answer(Request $request) {
        $data = [
            'id_recruitment_answer' => $request->id_recruitment_answer
        ];
        $result = MasterInterview::get_edit_answer($data);
        return response()->json($result);
    }
	
	public function get_stage() {		
        $result = MasterInterview::get_stage();
        return response()->json($result);
    }
	
	public function get_answer() {		
        $result = MasterInterview::get_answer();
        return response()->json($result);
    }
	
	public function get_question_type() {
        $result = MasterInterview::get_question_type();
        return response()->json($result);
    }
}
