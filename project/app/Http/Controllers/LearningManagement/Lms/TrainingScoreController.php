<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\MasterGroupCourse;
use App\Models\LearningManagement\Lms\MasterCourseHeader;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use App\Models\LearningManagement\Lms\MasterContent;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Http\Controllers\Controller;
use App\Models\LearningManagement\Lms\HrEventManagement;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUser;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUserHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;

class TrainingScoreController extends Controller {

    public function index(Request $request) {

        if ($request->ajax()) {
            $data = HrEventManagement::get_training_score_data(session('id_user'));

            return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('', function($data) {
                        $a = '';
                        return $a;
                    })
                    ->addColumn('action', function($data) {
                        $buttons = '<button class="btn btn-primary" onclick="loadModal('.$data->id_event_management.')">
                                        <span class="fas fa-edit"></span>
                                    </button>';
                        return $buttons;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }
        return view('learning_management.lms.training_scores.index');
    }

    public function get_data(Request $request) {
        $request->validate([
            'id_event_management' => 'required',
            'id_employee' => 'nullable',
            'id_course_detail' => 'nullable',
        ]);
        
        $result['data'] = HrEventManagement::get_training_score_data(session('id_user'), $request->id_event_management)[0];
        $result['answers'] = HrEventManagement::get_training_score_answers($request->id_event_management, $request->id_employee, $request->id_course_detail);
        $resCollection = collect($result['answers']);
        $courses = $resCollection->pluck('course_name', 'id_course_detail');
        foreach($courses as $key => $course) {
            $result['courses'][] = ["id" => $key, "text" => $course];
        }
        $emps = $resCollection->pluck('employee', 'id_employee');
        foreach($emps as $key => $emp) {
            $result['employees'][] = ["id" => $key, "text" => $emp];
        }
        
        return response()->json($result);
    }

    public function save_score(Request $request) {
        $request->validate([
            'answer.*.id_survey_user' => 'required',
            'answer.*.essay_score' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
            foreach($request->answer as $answer) {
                $quizAnswer = HrSurveyAnswerUser::findOrFail($answer['id_survey_user']);
                $prevScore = $quizAnswer->essay_score;
                $quizAnswer->essay_score = $answer['essay_score'];
                $quizAnswer->updated_by = session('id_user');
                $quizAnswer->save();

                $essayScore = $quizAnswer->essay_score ?? 0;

                $answerHeader = HrSurveyAnswerUserHeader::findOrFail($quizAnswer->id_survey_answer_user_header);
                $answersInHeader = DB::select("SELECT
                                                    *
                                                FROM
                                                    hr_survey_answer_user hsau
                                                WHERE
                                                    hsau.id_survey_answer_user_header = ?
                                                    AND hsau.status = 'A'
                                                    AND hsau.id_survey_answer IS NULL",
                                                    [$answerHeader->id_survey_answer_user_header]);
                // $answerHeader->total_score = $answerHeader->total_score * count($answersInHeader);
                if($answerHeader->total_score && $answerHeader->total_score != 0) {
                    $answerHeader->total_score = ($answerHeader->total_score - $prevScore) + $essayScore;
                } else {
                    $answerHeader->total_score += $essayScore;
                }
                // $answerHeader->total_score = $answerHeader->total_score / count($answersInHeader);
                $answerHeader->updated_by = session('id_user');
                $answerHeader->save();
            }
            DB::commit();
            return response()->json([
                "message" => "Score saved successfully!"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}