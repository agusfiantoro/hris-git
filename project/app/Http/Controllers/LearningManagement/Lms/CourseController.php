<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\MasterCourseHeader;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use App\Models\LearningManagement\Lms\MasterContent;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Models\LearningManagement\Lms\HrSurveyHeader;
use App\Http\Controllers\Controller;
use App\Http\Requests\Support\SanitizedForm;
use App\Models\GeneralSetting\CompanySetting\MasterGeneralData;
use App\Models\LearningManagement\Lms\HrSurveyAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;

class CourseController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = MasterCourseHeader::get_course();
            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $button = '<button type="button" name="edit" id="' . $data->id_course_header . '" class="edit btn btn-primary btn-sm" title="Edit" ><span class="fas fa-edit"></span></button> ';
                        $button .= '<button type="button" name="duplicate" course-name="'.$data->course_name.'" id-course-header="' . $data->id_course_header . '" class="duplicate btn btn-success btn-sm" title="Duplicate" ><span class="fas fa-copy"></span></button> ';
                        $button .= '&nbsp;&nbsp;<button type="button" name="delete" id="' . $data->id_course_header . '" class="delete btn btn-danger btn-sm" title="Delete"><span class="far fa-trash-alt"></span></button>';
                        $button .= '&nbsp;&nbsp;<button type="button" class="btn btn-success btn-sm download-report" data-course="'.$data->id_course_header.'" title="Download Onboarding Report"><i class="fa fa-file-pdf"></i></button>';
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.lms.course.index');
    }

    protected function validate_form(Request $request) {
        $arr_form_validate = [
            'course_name'   => 'required|string',
            // 'duration_time' => 'required|string',
            'status'        => 'required|string',
        ];
        $arr_msg_form_validate = [
            'course_name.required'   => 'The Course Name field is required',
            'duration_time.required' => 'The Duration field is required',
            'status.required'        => 'The Status field is required',
        ];

        if($request->course){
            foreach($request->course as $key => $val){
                if(@$val['course_type'] != 'Materi'){
                    $arr_form_validate['course.'.$key.'.question_view']= 'required';
                    $arr_form_validate['course.'.$key.'.weight']       = 'required';
                    $arr_msg_form_validate['course.'.$key.'.question_view.required']  = 'Num. Question field is required';
                    $arr_msg_form_validate['course.'.$key.'.weight.required']         = 'Weight field is required';
                }
            }
            $arr_form_validate['course.*.course_type']  = 'required|string';
            $arr_form_validate['course.*.sequence']     = 'required|string';
            $arr_form_validate['course.*.program']      = 'required|string';
            $arr_form_validate['course.*.status']       = 'required|string';
            $arr_msg_form_validate['course.*.course_type.required']    = 'Course Type field is required';
            $arr_msg_form_validate['course.*.sequence.required']       = 'Sequence field is required';
            $arr_msg_form_validate['course.*.program.required']        = 'Program field is required';
            $arr_msg_form_validate['course.*.status.required']         = 'Status field is required';
        }
        $request = SanitizedForm::sanitizeStringInput($request, $arr_form_validate);
        $request->validate($arr_form_validate, $arr_msg_form_validate);
    }

    protected function save(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $duration = $request->duration_time;
            $form_data = [
                'course_name'   => $request->course_name,
                'duration_time' => $duration,
                'notes'         => @$request->notes,
                'status'        => $request->status,
                'id_company'    => session('id_company'),
                'created_by'    => session('id_user'),
            ];
            $insertCourse = MasterCourseHeader::create($form_data);
            $idCourse = $insertCourse->id_course_header;
            if ($request->course) {
                $allDetail = [];
                $allWeightByCourse = [];
                foreach ($request->course as $key => $value) {
                    $detail = [
                        'id_course_header'      => $idCourse,
                        'sequence'              => $value['sequence'],
                        'course_type'           => $value['course_type'],
                        'notes'                 => @$value['notes'],
                        'status'                => $value['status'],
                        'id_company'            => session('id_company'),
                        'created_by'            => session('id_user'),
                    ];
                    if($value['course_type'] == 'Materi'){
                        $name = MasterContent::where('id_content_learning', $value['program'])->first()->content_name;
                        $detail['id_content_learning'] = $value['program'];
                        $detail['course_name'] = $name;
                    } else {
                        $quizName = HrSurveyHeader::where('id_survey_header', $value['program'])->first();
                        $questions = HrSurveyQuestion::where('id_survey_header', $value['program'])->get();
                        if(count($questions) < $value['question_view']) {
                            throw new \Exception("Number of question does not match quiz ".$quizName->description." number of question! ".$quizName->description." has ".count($questions)." questions.", 422);
                        }
                        $name = $quizName ? $quizName->description : '';
                        $detail['id_survey_header'] = $value['program'];
                        $detail['course_name'] = $name;
                        $detail['question_view'] = $value['question_view'];
                        $detail['weight'] = $value['weight'];
                    }
                    $allWeightByCourse[$value['course_type']][] = $value['weight'];
                    $allDetail[] = $detail;
                }

                $notifWeight = '';
                foreach ($allWeightByCourse as $type => $val) {
                    if(array_sum($val) > 100){
                        $notifWeight.= 'Sum of '.$type.' is '.array_sum($val)."\n";
                    }
                }
                if($notifWeight != ''){
                    throw new \Exception("Please change weight scale :\n".$notifWeight);
                }

                if(count($allDetail) > 0){
                    foreach ($allDetail as $k => $allCourse) {
                        MasterCourseDetail::create($allCourse);
                    }
                }
            }
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Course Saved Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function update(Request $request) {
        $this->validate_form($request);
        DB::beginTransaction();
        try {
            $duration = $request->duration_time;
            $form_data = [
                'course_name'   => $request->course_name,
                'duration_time' => $duration,
                'notes'         => @$request->notes,
                'status'        => $request->status,
                'id_company'    => session('id_company'),
                'updated_by'    => session('id_user'),
            ];

            $idCourse   = $request->id_course_header;
            $course     = MasterCourseHeader::findOrFail($idCourse)->update($form_data);

            $listIdCourseDetail    = [];
            $idCourseDetail        = [];
            if(MasterCourseDetail::where('id_course_header', $idCourse)->first() != null){
                $listIdCourseDetail = MasterCourseDetail::where('id_course_header', $idCourse)->get()->pluck('id_course_detail')->all();
            }

            if ($request->course) {
                $allDetail = [];
                $allEditDetail = [];
                $allWeightByCourse = [];
                foreach ($request->course as $key => $value) {
                    if(@$value['id_course_detail'] == ''){
                        $detail = [
                            'id_course_header'      => $idCourse,
                            'sequence'              => $value['sequence'],
                            'course_type'           => $value['course_type'],
                            'notes'                 => @$value['notes'],
                            'status'                => $value['status'],
                            'id_company'            => session('id_company'),
                            'created_by'            => session('id_user'),
                        ];
                        if($value['course_type'] == 'Materi'){
                            $name = MasterContent::where('id_content_learning', $value['program'])->first()->content_name;
                            $detail['id_content_learning'] = $value['program'];
                            $detail['course_name'] = $name;
                        } else {
                            $quizName = HrSurveyHeader::where('id_survey_header', $value['program'])->first();
                            $questions = HrSurveyQuestion::where('id_survey_header', $value['program'])->get();
                            if(count($questions) < $value['question_view']) {
                                throw new \Exception("Number of question does not match quiz ".$quizName->description." number of question! ".$quizName->description." has ".count($questions)." questions.", 422);
                            }
                            $name = $quizName ? $quizName->description : '';
                            $detail['id_survey_header'] = $value['program'];
                            $detail['course_name'] = $name;
                            $detail['question_view'] = $value['question_view'];
                            $detail['weight'] = $value['weight'];
                        }
                        $allDetail[] = $detail;
                    } else {
                        $idCourseDetail[] = @$value['id_course_detail'];
                        $edit_detail = [
                            'sequence'              => $value['sequence'],
                            'course_type'           => $value['course_type'],
                            'notes'                 => @$value['notes'],
                            'status'                => $value['status'],
                            'id_company'            => session('id_company'),
                            'updated_by'            => session('id_user'),
                        ];
                        if($value['course_type'] == 'Materi'){
                            $name = MasterContent::where('id_content_learning', $value['program'])->first()->content_name;
                            $edit_detail['id_course_detail'] = @$value['id_course_detail'];
                            $edit_detail['id_content_learning'] = $value['program'];
                            $edit_detail['id_survey_header'] = null;
                            $edit_detail['course_name'] = $name;
                        } else {
                            $quizName = HrSurveyHeader::where('id_survey_header', $value['program'])->first();
                            $questions = HrSurveyQuestion::where('id_survey_header', $value['program'])->get();
                            if(count($questions) < $value['question_view']) {
                                throw new \Exception("Number of question does not match quiz ".$quizName->description." number of question! ".$quizName->description." has ".count($questions)." questions.", 422);
                            }
                            $name = $quizName ? $quizName->description : '';
                            $edit_detail['id_course_detail'] = @$value['id_course_detail'];
                            $edit_detail['id_content_learning'] = null;
                            $edit_detail['id_survey_header'] = $value['program'];
                            $edit_detail['course_name'] = $name;
                            $edit_detail['question_view'] = $value['question_view'];
                            $edit_detail['weight'] = $value['weight'];
                        }
                        $allEditDetail[] = $edit_detail;
                    }
                    $allWeightByCourse[$value['course_type']][] = $value['weight'];
                }

                $notifWeight = '';
                foreach ($allWeightByCourse as $type => $val) {
                    if(array_sum($val) > 100){
                        $notifWeight.= 'Sum of '.$type.' is '.array_sum($val)."\n";
                    }
                }
                if($notifWeight != ''){
                    throw new \Exception("Please change weight scale :\n".$notifWeight);
                }

                if(count($allDetail) > 0){
                    foreach ($allDetail as $k => $allCourse) {
                        MasterCourseDetail::create($allCourse);
                    }
                }
                if(count($allEditDetail) > 0){
                    foreach ($allEditDetail as $key => $data) {
                        MasterCourseDetail::findOrFail(@$data['id_course_detail'])->update($data);
                    }
                }
            }
            $diff = array_diff($listIdCourseDetail, $idCourseDetail);
            if(count($diff) > 0){
                foreach ($diff as $key => $value) { 
                    MasterCourseDetail::where('id_course_detail', $value)->delete();
                }
            }
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Content Updated Successfully !!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function destroy(Request $request) {
        $id             = $request->id_course_header;
        $course         = MasterCourseHeader::findOrFail($id);
        if(MasterCourseDetail::where('id_course_header', $id)->first() != null){
            MasterCourseDetail::where('id_course_header', $id)->delete();
        }
        $course->delete();
    }

    public function get_course_materi() {
        $result = MasterCourseHeader::get_course_materi();
        return response()->json($result);
    }

    public function get_course_quiz(Request $request) {
        $result = MasterCourseHeader::get_course_quiz(null, $request->type);
        return response()->json($result);
    }

    public function get_course_detail(Request $request) {
        $data = [
            'id_course_header' => $request->id_course_header,
            'id_company' => session('id_company')
        ];
        $result = MasterCourseHeader::get_course_detail($data);
        return response()->json($result);
    }

    public function duplicateCourse(Request $request) {
        $request->validate([
            'id_course_header' => 'required',
            'course_name' => 'required',
            'id_company' => 'required',
        ]);
        try {
            DB::beginTransaction();

            $courseHeader = MasterCourseHeader::findOrFail($request->id_course_header);
            $courseDetail = MasterCourseDetail::where('id_course_header', $courseHeader->id_course_header)->get();
            $courseHeader = $courseHeader->toArray();
            $courseHeader['course_name'] = $request->course_name;
            $courseHeader['created_by'] = session('id_user');
            $courseHeader['updated_by'] = null;
            $courseHeader['id_company'] = $request->id_company;
            unset($courseHeader['id_course_header']);
            $newCourse = MasterCourseHeader::create($courseHeader);
            foreach($courseDetail as $cd) {
                $quiz = HrSurveyHeader::where('id_survey_header', $cd->id_survey_header)->get();
                $materi = MasterContent::where('id_content_learning', $cd->id_content_learning)->get();
                
                foreach($quiz as $q) {
                    $quizType = MasterGeneralData::findOrFail($q->id_survey_type);
                    $mgd = MasterGeneralData::where('code', $quizType->code)->where('id_company', $request->id_company)->where('status', 'A')->first();
                    if(!$mgd) {
                        throw new \Exception("Quiz type (".$quizType->description.") not found in the selected company.");
                    }
                    $questions = HrSurveyQuestion::where('id_survey_header', $q->id_survey_header)->get();
                    $q = $q->toArray();
                    $q['id_survey_type'] = $mgd->id_general_data;
                    $q['created_by'] = session('id_user');
                    $q['updated_by'] = null;
                    $q['id_company'] = $request->id_company;
                    unset($q['id_survey_header']);
                    $newQuiz = HrSurveyHeader::create($q);
                    foreach($questions as $question) {
                        $answers = HrSurveyAnswer::where('id_survey_question', $question->id_survey_question)->get();
                        $mgdQuestionType = MasterGeneralData::findOrFail($question->id_question_type);
                        $mgdQuestionType = MasterGeneralData::where('code', $mgdQuestionType->code)->where('id_company', $request->id_company)->where('status', 'A')->first();
                        $question = $question->toArray();
                        $question['id_survey_header'] = $newQuiz->id_survey_header;
                        $question['id_question_type'] = $mgdQuestionType->id_general_data;
                        $question['created_by'] = session('id_user');
                        $question['updated_by'] = null;
                        $question['id_company'] = $request->id_company;
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

                    $cd = $cd->toArray();
                    $cd['id_company'] = $request->id_company;
                    $cd['created_by'] = session('id_user');
                    $cd['updated_by'] = null;
                    $cd['id_survey_header'] = $newQuiz->id_survey_header;
                    $cd['id_course_header'] = $newCourse->id_course_header;
                    unset($cd['id_course_detail']);
                    $newMcd = MasterCourseDetail::create($cd);
                }

                foreach($materi as $m) {
                    $mat = $m->toArray();
                    $mat['created_by'] = session('id_user');
                    $mat['updated_by'] = null;
                    $mat['id_company'] = $request->id_company;
                    unset($mat['id_content_learning']);
                    $newMateri = MasterContent::create($mat);

                    $cd = $cd->toArray();
                    $cd['id_company'] = $request->id_company;
                    $cd['created_by'] = session('id_user');
                    $cd['updated_by'] = null;
                    $cd['id_content_learning'] = $newMateri->id_content_learning;
                    $cd['id_course_header'] = $newCourse->id_course_header;
                    unset($cd['id_course_detail']);
                    $newMcd = MasterCourseDetail::create($cd);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Course '.$courseHeader['course_name'].' has been duplicated.'
            ]);
        } catch(\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    } 
}
