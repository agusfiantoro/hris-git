<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\HrSurveyHeader;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Models\LearningManagement\Lms\HrSurveyAnswer;
use App\Models\LearningManagement\Lms\MasterAnswer;
use App\Models\GeneralSetting\CompanySetting\Company;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Validator;

class QuizController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $data = HrSurveyHeader::getdata();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    $button = '<button type="button" name="edit" id="' . $data->id_survey_header . '" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                    $button .= '<button type="button" name="duplicate" id="' . $data->id_survey_header . '" q-type="'.$data->survey_type_code.'" q-title="'.$data->description.'"  class="duplicate btn btn-success btn-sm" title="Duplicate" ><span class="fas fa-copy"></span></button> ';
                    // $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_survey_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('learning_management.lms.quiz.index');
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
            'code_answer' => 'required|string',
            'description_answer' => 'required|string',
        ];
        $arr_msg_form_validate = [
            'code_answer.required' => 'Code Answer is required',
            'description_answer.required' => 'Description Answer is required',
        ];
        if ($request->suggested_image) {
            $arr_form_validate['suggested_image'] = 'mimes:jpg,jpeg,png,svg';
            $arr_msg_form_validate['suggested_image.mimes'] = 'Image extension must be : .jpg/.jpeg/.png/.svg';
        }
        $request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);

        $filePath   = 'public/upload/master_answer/';
        $newFileName= null;

        if($request->suggested_image) {
            if ($request->suggested_image->isValid()) {
                $fileName       = $request->suggested_image->getClientOriginalName();
                $newFileName    = Str::random(3).'_'.$fileName;
                $dir            = Storage::makeDirectory($filePath,0775, true, true);
                $storageFile    = Storage::putFileAs($filePath, $request->suggested_image, $newFileName);
            }
        }
        $form_data = array(
            'code' => $request->code_answer,
            'description_answer' => $request->description_answer,
            'suggested_image' => $newFileName,
            'status' => $request->status,
            'answer_group_type' => 'LMS',
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
                $dir            = Storage::makeDirectory($filePath,0775, true, true);
                $storageFile    = Storage::putFileAs($filePath, $request->suggested_image, $newFileName);
            }
        }
        $form_data = array(
            'code' => $request->code_answer,
            'description_answer' => $request->description_answer,
            'status' => $request->status,
            'answer_group_type' => 'LMS',
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
        $getQuestionType = DB::table('master_general_data')->where('id_general_type',12)->pluck('code','id_general_data')->all();

        $arr_form_validate = [
            'description' => 'required|string',
            'id_employee_request' => 'required',
            'status' => 'required',
            // 'daterange' => 'required',
            'question.*.sequence' => 'required',
            'question.*.question' => 'required',
            'question.*.question_status' => 'required',
        ];

        if($request->question){
            foreach($request->question as $key => $val){
                if(@$val['question_type']){
                    if(!in_array($getQuestionType[@$val['question_type']], ['Essay', 'Upload_Files'])){
                        $arr_form_validate['question.'.$key.'.suggested_answer.*'] = 'required';
                        $arr_msg_form_validate['question.'.$key.'.suggested_answer.*.required'] = 'The Answer field is required';
                    }
                } else {
                    $arr_form_validate['question.*.question_type'] = 'required';
                    $arr_form_validate['question.'.$key.'.suggested_answer.*'] = 'required';

                    $arr_msg_form_validate['question.*.question_type.required'] = 'The Question type field is required';
                    $arr_msg_form_validate['question.'.$key.'.suggested_answer.*.required'] = 'The Answer field is required';
                }
            }
        }

        $arr_msg_form_validate = [
            'id_employee_request.required' => 'Request Employee field is required',
            'status.required' => 'Status field is required',
            // 'daterange.required' => 'Date field is required',
            'question.*.sequence.required' => 'The Sequence field is required',
            'question.*.question.required' => 'The Question field is required',
            'question.*.question_status.required' => 'The Question Status field is required',
        ];
        if ($request->post('question') == null) {
            $arr_form_validate['table_question'] = 'required|string';
            $validate_msg_question['table_question.required'] = 'Table Question Detail cannot empty';
        }
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        // var_dump($request->all());die;
        $this->validateSurvey($request);
        DB::beginTransaction();
        try {
            $form_data = [
                'description' => $request->description,
                'id_employee_request' => $request->id_employee_request,
                'id_survey_type' => $request->id_survey_type,
                'survey_category' => 'LMS',
                // 'start_date' => $request->start_date,
                // 'end_date' => $request->end_date,
                // 'published' => isset($request->published) == "on" ? 1 : 0,
                'notes' => $request->notes,
                'status' => $request->status,
                'id_company' => session('id_company'),
            ];

            if(!@$request->id_survey_header){
                $form_data['created_by'] = session('id_user');
                $hrSurveyHeader = HrSurveyHeader::create($form_data);
                $idSurveyHeader = $hrSurveyHeader->id_survey_header;
            } else {
                $form_data['updated_by'] = session('id_user');
                $idSurveyHeader = $request->id_survey_header;
                $hrSurveyHeader = HrSurveyHeader::where('id_survey_header',$idSurveyHeader)->update($form_data);
            }

            $getQuestionType = DB::table('master_general_data')->where('id_general_type',12)->pluck('code','id_general_data')->all();

            if($request->question){
                if(count($request->question) != $request->question_length) {
                    throw new \Exception("Server reads ".count($request->question). " questions but question length was confirmed to be ".$request->question_length);
                }
                $questionCollections = collect($request->question);
                foreach ($request->question as $key => $value) {
                    if($questionCollections->where('sequence', $value['sequence'])->count() > 1) {
                        throw new Exception("Duplicate sequence ".$value['sequence']);
                    }
                    $idSurveyQuestionFromView = $value['id_survey_question'];
                    $form_question = [
                        'id_survey_header' => $idSurveyHeader,
                        'id_question_type' => $value['question_type'],
                        'sequence' => $value['sequence'],
                        'question' => $value['question'],
                        'note' =>$value['note'],
                        'status' => $value['question_status'],
                        'id_company' => session('id_company'),
                    ];

                    if(!$idSurveyQuestionFromView){
                        $form_question['created_by'] = session('id_user');
                        $hrSurveyQuestion = HrSurveyQuestion::create($form_question); 
                        $idSurveyQuestion = $hrSurveyQuestion->id_survey_question;
                    } else {
                        $idSurveyQuestion = $idSurveyQuestionFromView;
                        $hrSurveyQuestion = HrSurveyQuestion::where('id_survey_question',$idSurveyQuestion)->update($form_question);
                    }

                    if($hrSurveyQuestion){
                        if(@$value['suggested_answer']){
                            foreach (@$value['suggested_answer'] as $k => $item) {
                                if($getQuestionType[$value['question_type']] != 'Essay' && !is_null($item)){
                                    $formMasterAnswer = [
                                        'description_answer' => $item,
                                        'status' => @$value['answer_status'][$k],
                                        'answer_group_type' => 'LMS',
                                        'id_company' => session('id_company'),
                                    ];
                                    if(is_null(@$value['id_answer'][$k])){
                                        // $firstWordAnswer = strtok($item, " ");
                                        // $formMasterAnswer['code'] = @$firstWordAnswer.'_'.Str::random(3);
                                        // $formMasterAnswer['created_by'] = session('id_user');
                                        // $masterAnswer = MasterAnswer::create($formMasterAnswer); 
                                        $idAnswer = null;
                                    } else {
                                        $idAnswer = @$value['id_answer'][$k];
                                        // $formMasterAnswer['updated_by'] = session('id_user');
                                        // $hrSurveyQuestion = MasterAnswer::where('id_answer',$idAnswer)->update($formMasterAnswer);
                                    }

                                    $isCorrectAnswer = false;
                                    if(@$value['corrected_answer']){
                                        foreach ($value['corrected_answer'] as $k_ => $val_) {
                                            if($val_ == $k){
                                                $isCorrectAnswer = true;
                                            }
                                        }
                                    }
                                    $form_answer = [
                                        'id_survey_question' => $idSurveyQuestion,
                                        'id_answer' => null,
                                        'reference_number' => '',
                                        'suggested_answer' => $item,
                                        'is_corrected_answer' => $isCorrectAnswer,
                                        'score_answer' => @$value['score'][$k] ?? null,
                                        'status' => @$value['answer_status'][$k] ?? 'A', 
                                        'id_company' => session('id_company'),
                                    ];
                                    $getHrSurveyAnswer = HrSurveyAnswer::where('id_survey_answer', $idAnswer)->first();
                                    if(!$getHrSurveyAnswer){
                                        $form_answer['created_by'] = session('id_user');
                                        $hrSurveyAnswer= HrSurveyAnswer::create($form_answer);
                                    } else {
                                        $form_answer['updated_by'] = session('id_user');
                                        $hrSurveyAnswer = HrSurveyAnswer::where('id_survey_answer', $idAnswer)->update($form_answer);
                                    }
                                }
                            }
                        }
                    }   
                }

            }
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Quiz Saved Successfully !!']);
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
            'survey_category' => 'LMS',
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
                'note' => $request->question[$data['counter']]['note'],
                'status' => $request->question[$data['counter']]['question_status'],
                'id_company' => session('id_company'),
                'created_by' => session('id_user'),
            );
        $question = HrSurveyQuestion::create($form_question);
        
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
            'survey_category' => 'LMS',
            // 'start_date' => $request->start_date,
            // 'end_date' => $request->end_date,
            'published' => isset($request->published) == "on" ? 1 : 0,
            'status' => $request->status,
            'id_company' => session('id_company'),
            'updated_by' => session('id_user'),
        );  
        $result = HrSurveyHeader::where('id_survey_header', $data['id_survey_header'])->update($form_data);
        $form_question = array(
            'id_survey_header' => $data['id_survey_header'],
            'id_question_type' => $request->question[$data['counter']]['question_type'],
            'sequence' => $request->question[$data['counter']]['sequence'],
            'question' => $request->question[$data['counter']]['question'],
            'note' =>$request->question[$data['counter']]['note'],
            'status' => $request->question[$data['counter']]['question_status'],
            'id_company' => session('id_company'),
            'created_by' => session('id_user'),
        );
        
        if($data['id_survey_question'] == ""){
            $question = HrSurveyQuestion::create($form_question);
            return response()->json(['status' => 'true', 'result'=>$data, 'question'=>$question]);
        }
        else{
            $answer = HrSurveyAnswer::where('id_survey_question', $data['id_survey_question'])->get();
            $x = [];
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
                foreach ($request->answer as $key => $value) {
                    $form_answer = array(
                        'id_survey_question' => $data['id_survey_question'],
                        'id_answer' => $value['id_answer'],
                        'reference_number' => '',
                        'suggested_answer' => '',
                        'is_corrected_answer' => @$value['correct_input'] == "on" ? 1 : 0,
                        'score_answer' => @$value['score_input'],
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
        $collect_answer     = collect($request->answer)->groupBy('id_survey_answer')->toArray();
        $list_id_answer     = array_filter(array_keys($collect_answer));
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
                //  'id_survey_answer' => $value['id_survey_answer'],
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

    public function destroy(Request $request) {
        $id         = $request->id_survey_header;
        $quiz       = HrSurveyHeader::findOrFail($id);
        $question   = HrSurveyQuestion::where('id_survey_header', $id);
        if($question->first() != null){
            foreach($question->get() as $value){
                HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->delete();
            }
            HrSurveyQuestion::where('id_survey_header', $id)->delete();
        }
        $quiz->delete();
    }

    public function destroy_question($id) {
        $question = HrSurveyQuestion::where('id_survey_question', $id)->get();
        foreach($question as $value){
            HrSurveyAnswer::where('id_survey_question', $value['id_survey_question'])->delete();
        }
        HrSurveyQuestion::where('id_survey_question', $id)->delete();
    }

    public function get_quiz() {
        $result = HrSurveyHeader::get_quiz();
        return response()->json($result);
    }

    public function get_survey_edit(Request $request) {
        $result = HrSurveyHeader::get_survey_edit($request->id_survey_header);
        return response()->json($result);
    }

    public function get_employee() {
        $result = HrSurveyHeader::get_employee();
        return response()->json($result);
    }

    public function get_company() {
        $result = HrSurveyHeader::get_company();
        return response()->json($result);
    }

    public function get_company_all() {
        $result = HrSurveyHeader::get_company_all();
        return response()->json($result);
    }

    public function get_quiz_employee_request() {
        $result = HrSurveyHeader::get_quiz_employee_request();
        return response()->json($result);
    }

    public function get_question_type() {
        $result = HrSurveyHeader::get_question_type();
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

    public function get_quiz_reff() {
        $yearMonth  = date('Ym');
        $company    = Company::where('id_company', session('id_company'))->first();
        $nomor      = $company->company_code.'-QIZ-'.$yearMonth.'-';
        $lastReff   = (int) substr(HrSurveyHeader::where('id_company', session('id_company'))->where('reference_number', 'LIKE', "%QIZ-{$yearMonth}%")->max('reference_number'), 15, 21);

        if($lastReff) {
            $code       = sprintf("%06s", abs($lastReff + 1));
            $newReff    = $nomor.$code;
        } else {
            $code       = sprintf("%06s", 1);
            $newReff    = $nomor.$code;
        }
        $result['reff'] = $newReff;
        return response()->json($result);
    }

    public function get_content_learning(Request $request) {
        $data = [
            'id_content_learning' => $request->id_content_learning,
            'id_company' => session('id_company')
        ];
        $result = MasterContent::get_content_learning($data);
        return response()->json($result);
    }

    public function get_user_by_session() {
        $result = HrSurveyHeader::get_employee(session('id_user'));
        return response()->json($result);
    }

    public function duplicateQuiz(Request $request) {
        $request->validate([
            'id_survey_header' => 'required',
            'quiz_type' => 'required|in:quiz,quiz_pretest,quiz_posttest,quiz_remidial',
            'description' => 'required',
            'id_company' => 'required',
        ]);
        try {
            DB::beginTransaction();
            $mgd = MasterGeneralData::where('code', $request->quiz_type)->where('id_company', $request->id_company)->where('status', 'A')->first();
            if(!$mgd) {
                throw new \Exception("Quiz type (".$request->quiz_type.") not found in the selected company.");
            }
            $quiz = HrSurveyHeader::findOrFail($request->id_survey_header);
            $questions = HrSurveyQuestion::where('id_survey_header', $quiz->id_survey_header)->get();
            
            $quiz = $quiz->toArray();
            $quiz['id_survey_type'] = $mgd->id_general_data;
            $quiz['description'] = $request->description;
            $quiz['created_by'] = session('id_user');
            $quiz['updated_by'] = null;
            $quiz['id_company'] = $request->id_company;
            unset($quiz['id_survey_header']);
            $newQuiz = HrSurveyHeader::create($quiz);
            foreach($questions as $question) {
                $answers = HrSurveyAnswer::where('id_survey_question', $question->id_survey_question)->get();
                $question = $question->toArray();
                $question['id_survey_header'] = $newQuiz->id_survey_header;
                $question['created_by'] = session('id_user');
                $question['updated_by'] = null;
                $question['id_company'] = $request->id_company;
                $question['id_question_type'] = @DB::selectOne("SELECT mgd2.* FROM master_general_data mgd 
                                                JOIN master_general_data mgd2 ON mgd.code = mgd2.code
                                                WHERE mgd.id_general_data = ? AND mgd2.id_company = ?",
                                                [$question['id_question_type'], $request->id_company])->id_general_data;
                unset($question['id_survey_question']);
                $newQuestion = HrSurveyQuestion::create($question);
                foreach($answers as $answer) {
                    $answer = $answer->toArray();
                    $answer['id_survey_question'] = $newQuestion->id_survey_question;
                    $answer['created_by'] = session('id_user');
                    $answer['updated_by'] = null;
                    $answer['id_company'] = $request->id_company;
                    unset($answer['id_survey_answer']);
                    $newAnswer = HrSurveyAnswer::create($answer);
                }
            }
            // dd($newQuiz, $newQuestion, $newAnswer);
            DB::commit();
            return response()->json([
                'message' => 'Quiz '.$quiz['description'].' has been duplicated.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json($e->getMessage(), 500);
        }
    } 
}
