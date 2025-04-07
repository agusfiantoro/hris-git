<?php

namespace App\Http\Controllers\LearningManagement\Lms;

use App\Models\LearningManagement\Lms\HrEventProgram;
use App\Models\LearningManagement\Lms\HrEventAttendees;
use App\Models\LearningManagement\Lms\MasterCourseDetail;
use App\Models\LearningManagement\Lms\MasterContent;
use App\Models\LearningManagement\Lms\HrSurveyQuestion;
use App\Models\LearningManagement\Lms\HrSurveyAnswer;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUser;
use App\Models\LearningManagement\Lms\HrSurveyAnswerUserHeader;
use App\Models\Setting\ResponsibilityUser\MasterUserResponsibility;
use App\Http\Controllers\Controller;
use App\Models\LearningManagement\Lms\MasterCourseHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DataTables;
use Validator;
use Carbon\Carbon;

class ClassroomController extends Controller {

    public function index(Request $request) {
        if ($request->ajax()) {
            $courses = HrEventProgram::get_program();
            foreach($courses as $k => $data) {
                $courses[$k]->action = $data->id_event_program;
                $courses[$k]->answered_date = $data->answered_date ? Carbon::parse($data->answered_date)->locale('en')->isoFormat('DD MMM Y') : "";
                $countCourseDetail = DB::table('master_course_detail')
                    ->where('id_course_header', $data->id_course_header)
                    ->where('status', 'A')
                    ->pluck('id_course_detail')->unique();
                $countCourseViewedByEmployee = DB::table('relation_event_course_employee as rece')
                    ->leftJoin('master_course_detail as mcd', 'mcd.id_course_detail', '=', 'rece.id_course_detail')
                    ->where('mcd.id_course_header', $data->id_course_header)
                    ->where('rece.id_employee', $data->id_employee)
                    ->where('rece.id_event_program', $data->id_event_program)
                    ->pluck('mcd.id_course_detail')->unique();
                $diffCourseDetail = $countCourseDetail->diff($countCourseViewedByEmployee);
                if($diffCourseDetail->count() < 1 || $courses[$k]->total_score){
                    $courses[$k]->status_join = 'done';
                } else {
                    $endDateCourse = !is_null($data->end_date_attendee) ? $data->end_date_attendee : $data->end_date;
                    $startDateCourse = !is_null($data->start_date_attendee) ? $data->start_date_attendee : $data->start_date;

                    $date = Carbon::parse($endDateCourse)->addDays(1);
                    $now = Carbon::now();
                    $startCourse = Carbon::parse($startDateCourse);
                    $diff = $now->diffInDays($date);
                    if($now->timestamp > $date->timestamp && $countCourseViewedByEmployee->count() < 1){
                        $courses[$k]->status_join = 'expired';
                    } else {
                        if($startCourse->timestamp  <=  $now->timestamp && $date->timestamp > $now->timestamp){
                            $courses[$k]->status_join = 'available';
                        } else {
                            $courses[$k]->status_join = 'expired';
                        }
                    }
                }
            }
            // dump($courses);
            $courses = $courses->sortBy(
                ['status_join', 'ASC'],
                ['combined_start_date', 'DESC'],
                ['sequence', 'ASC'],
            );
            // dd($courses);
            // $data = collect($data)->unique('id_survey_answer_user_header')->all();
            // dd($data);
            return DataTables::of($courses)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                // ->addColumn('score', function($data) {
                //     $score = DB::selectOne("SELECT
                //                                 hep.pass_scores, hsauh.total_score
                //                             FROM
                //                                 hr_survey_answer_user_header hsauh 
                //                             JOIN master_course_detail mcd ON hsauh.id_course_detail = mcd.id_course_detail 
                //                             JOIN master_course_header mch ON mcd.id_course_header = mch.id_course_header
                //                             JOIN hr_event_program hep ON mch.id_course_header = hep.id_course_header 
                //                             WHERE id_employee = ? AND mch.id_course_header = ? AND hep.id_event_program = ?"
                //                             , [$data->id_employee, $data->id_course_header, $data->id_event_program]);
                //     return $score ? $score->total_score : "-";
                // })
                ->addColumn('result', function($data) {
                    // $result = DB::selectOne("SELECT
                    //                             hep.pass_scores, hsauh.total_score
                    //                         FROM
                    //                             hr_survey_answer_user_header hsauh 
                    //                         JOIN master_course_detail mcd ON hsauh.id_course_detail = mcd.id_course_detail 
                    //                         JOIN master_course_header mch ON mcd.id_course_header = mch.id_course_header
                    //                         JOIN hr_event_program hep ON mch.id_course_header = hep.id_course_header 
                    //                         WHERE id_employee = ? AND mch.id_course_header = ? AND hep.id_event_program = ?"
                    //                         , [$data->id_employee, $data->id_course_header, $data->id_event_program]);
                    $result = DB::selectOne("SELECT
                                    hep.pass_scores,
                                    hsauh.total_score,
                                    mcd.course_type
                                FROM
                                    hr_event_program hep
                                JOIN master_course_header mch ON
                                    hep.id_course_header = mch.id_course_header
                                JOIN master_course_detail mcd ON
                                    mch.id_course_header = mcd.id_course_header
                                JOIN hr_survey_answer_user_header hsauh ON
                                    mcd.id_course_detail = hsauh.id_course_detail
                                    AND mcd.id_survey_header = hsauh.id_survey_header
                                    AND hep.id_event_program = hsauh.id_event_program
                                WHERE
                                    hep.id_event_program = ?
                                    AND mch.id_course_header = ?
                                    AND hsauh.id_employee = ?
                                    AND (mcd.course_type = 'Quiz_Posttest' OR mcd.course_type = 'Quiz_Remidial' OR (hep.pass_scores = 1))
                                    AND hsauh.id_survey_answer_user_header = ?",
                    [$data->id_event_program, $data->id_course_header, $data->id_employee, $data->id_survey_answer_user_header]);

                    if(!$result) return "No Data";
                    if($result->pass_scores === null) {
                        return "No Passing Grade";
                    } elseif($result->total_score == null) {
                        return "No Score";
                    } elseif($result->total_score >= $result->pass_scores) {
                        return "Pass";
                    } else {
                        return "Fail";
                    }
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('learning_management.lms.classroom.index');
    }

    public function class(Request $request) {
        $id = $request->c ?? null;
        $getEmployee = DB::table('hr_employee')->where('status', 'A')->where('id_user', session('id_user'))->first();
        $getAttendee = DB::table('hr_event_attendees')->where('id_event_program', $id)->pluck('booked_by')->all();
        $getProgram = DB::table('hr_event_program')->where('id_event_program', $id)->first();
        $getAttendeeUser = DB::table('hr_employee')->whereIn('id_employee', $getAttendee)->get()->pluck('id_user')->all();

        if(is_null($id) || !$getProgram){
            return redirect(url('learning_management/lms/class_room'));
        }
        $message = '';
        if(!in_array($getEmployee->id_employee, $getAttendee) && !in_array($getEmployee->id_user, $getAttendeeUser)){
            $message = 'Anda tidak memiliki akses pada kelas ini';
        }

        $countCourseDetail = DB::table('master_course_detail')
            ->where('id_course_header', $getProgram->id_course_header)
            ->count();
        $courseViewedByEmployee = DB::table('relation_event_course_employee as rece')
            ->leftJoin('master_course_detail as mcd', 'mcd.id_course_detail', '=', 'rece.id_course_detail')
            ->where('mcd.id_course_header', $getProgram->id_course_header)
            ->where('rece.id_employee', $getEmployee->id_employee)
            ->get();

        if(count($courseViewedByEmployee) < $countCourseDetail){
            $statusCourse = 'not_yet';
        } else {
            $statusCourse = 'done';
        }
        return view('learning_management.lms.classroom.class', compact('message', 'statusCourse'));
    }

    public function join($id=null) {
        DB::beginTransaction();
        try {
            $getEmployee = DB::table('hr_employee')->where('id_user', session('id_user'))->get();
            $getEmployeeActive = $getEmployee->where('status', 'A')->first();
            $program = HrEventProgram::leftJoin('hr_event_management as hem', 'hem.id_event_management', '=', 'hr_event_program.id_event_management')
                ->select('hr_event_program.*', 'hem.description as class')
                ->where('hr_event_program.id_event_program', $id)
                ->where('hr_event_program.status', 'A')
                ->first();
            $idCourse   = $program->id_course_header;
            $courseName = $program->description;
            $className  = $program->class;
            $passScore  = $program->pass_scores;

            $getCourse  = MasterCourseDetail::getCourse($idCourse);
            $getCourseByEmployee = MasterCourseDetail::getCourseByEmployee($id, $getEmployeeActive->id_employee);
            $getSequenceCourse = $getCourseByEmployee->pluck('id_course_detail')->all();
            $getLastCourseCompleted = $getCourseByEmployee->count() > 0 ? @$getCourseByEmployee[0] : null;
            $allowShowMateri = true;
            if($getCourse->count() <= count($getSequenceCourse)){
                //Jika Course awal sudah selesai dan sudah menegerjakan post test
                $allUserScoreByCourse = 0;
                $getPostestByCourse  = MasterCourseDetail::getCourseByCourseType($idCourse, 'Quiz_Posttest');
                if($getPostestByCourse->count() > 0){
                    foreach ($getPostestByCourse as $k => $val) {
                        $getAnswerUserHeader = DB::table('hr_survey_answer_user_header as hsauh')
                            ->where('id_survey_header', $val->id_survey_header)
                            ->where('id_course_detail', $val->id_course_detail)
                            ->where('id_event_program', $program->id_event_program)
                            ->whereIn('id_employee', $getEmployee->pluck('id_employee')->toArray())
                            ->first();
                        $allUserScoreByCourse += @$getAnswerUserHeader->total_score ?? 0;
                    }
                    $getEventProgram = DB::table('hr_event_program')->where('id_event_program', $id)->first();
                    $passScoresThisCourse = @$getEventProgram->pass_scores ?? 0;
                    if($passScoresThisCourse > $allUserScoreByCourse){
                        //utk pencegahan menampilkan materi
                        $allowShowMateri = false;
                    }
                }
            } else {
                //Jika Course awal belum selesai atau Course Remidial yang masih blm selesai
                $getRemidialByCourse  = MasterCourseDetail::getCourseByCourseType($idCourse, 'Quiz_Remidial');

                if($getRemidialByCourse->count() > 0){
                    $getCourseRemidial  = MasterCourseDetail::getCourse($idCourse);
                    $getCourseRemidialByEmployee = MasterCourseDetail::getCourseByEmployee($id, $getEmployeeActive->id_employee);
                    $getSequenceCourseRemidial = $getCourseRemidialByEmployee->pluck('id_course_detail')->all();

                    //default meskipun remidi materi tetap tampil
                    $allowShowMateri = true;
                    if($getCourseRemidial->count() <= count($getSequenceCourse)){
                        //Jika Course Remidial belum selesai dikerjakan
                        $allUserScoreRemidialByCourse = 0;
                        if($getRemidialByCourse->count() > 0){
                            foreach ($getRemidialByCourse as $k => $val) {
                                $getAnswerUserHeader = DB::table('hr_survey_answer_user_header as hsauh')
                                    ->where('id_survey_header', $val->id_survey_header)
                                    ->where('id_course_detail', $val->id_course_detail)
                                    ->where('id_event_program', $program->id_event_program)
                                    ->first();
                                $allUserScoreRemidialByCourse += @$getAnswerUserHeader->total_score ?? 0;
                            }
                            $getEventProgram = DB::table('hr_event_program')->where('id_event_program', $id)->first();
                            $passScoresThisCourseRemidial = @$getEventProgram->pass_scores ?? 0;
                            if($passScoresThisCourseRemidial <= $allUserScoreRemidialByCourse){
                                //utk menampilkan materi jika score remidi melebihi pass score
                                $allowShowMateri = true;
                            }
                        }
                    }
                }
            }

            $materi                 = [];
            $quiz                   = [];
            $idSurveyHeader         = [];
            $idSurveyQuestion       = [];
            $totalQuestionAnswered  = [];
            $contentBySequence      = [];

            $getStatusConfirm = HrEventAttendees::where('id_event_program', $id)->whereIn('booked_by', $getEmployee->pluck('id_employee')->toArray());
            if($getStatusConfirm->first()->status == 'Unconfirm'){
                $changeStatusConfirm = $getStatusConfirm->update(['status'=>'Confirm']);
            } else if($getStatusConfirm->first()->status == 'Attended'){
                $countCourseDetail = DB::table('master_course_detail')
                    ->where('id_course_header', $idCourse)
                    ->count();
                $countCourseViewedByEmployee = DB::table('relation_event_course_employee as rece')
                    ->leftJoin('master_course_detail as mcd', 'mcd.id_course_detail', '=', 'rece.id_course_detail')
                    ->where('mcd.id_course_header', $idCourse)
                    ->count();

                if($countCourseViewedByEmployee < $countCourseDetail){
                    $changeStatusConfirm = $getStatusConfirm->update(['status'=>'Confirm']);
                }
            }

            if($getCourse->count() > 0){
                $thisSequence = 1;
                foreach($getCourse as $k => $item){
                    $idSurveyHeader_ = @$item->id_survey_header;
                    $idContentLearning = @$item->id_content_learning;
                    $idCourseDetail = @$item->id_course_detail;
                    $status = @$item->status;
                    $questionView = @$item->question_view;
                    $weight = @$item->weight;
                    $sequence = @$item->sequence;
                    $finishAnswerQuiz = false;

                    if($idContentLearning && !$idSurveyHeader_){
                        $getMateri = MasterContent::leftJoin('master_attachment_content as mac', 'mac.id_content_learning', '=', 'master_content_learning.id_content_learning')
                            ->select('master_content_learning.*', 'mac.attachment')
                            ->where('master_content_learning.id_content_learning', $idContentLearning)
                            ->first();
                        $link = @$getMateri->link;
                        $attachment = @$getMateri->attachment;

                        if(strpos($link, 'youtu.be/') !== false){
                            $link = str_replace('youtu.be/', 'www.youtube.com/embed/', $link);
                        } else if(strpos($link, 'watch?v') !== false){
                            $link = str_replace('watch?v=', 'embed/', $link);
                        }
                        if(str_contains($link, '?')) {
                            $link .= '&showinfo=0&controls=1&disablekb=1&enablejsapi=1';
                        } else {
                            $link .= '?showinfo=0&controls=1&disablekb=1&enablejsapi=1';
                        }
                        if($attachment){
                            // $attachment = url('project/storage/app/public/upload/content_learning/').'/'.$getMateri->id_content_learning.'/'.rawurlencode($attachment);
                            $attachment = 'project/storage/app/public/upload/content_learning/'.$getMateri->id_content_learning.'/'.rawurlencode($attachment);
                        }

                        $thisMateri = [
                            'course_name'   => @$item->course_name,
                            'type'          => @$getMateri->content_attachment_type,
                            'description'   => $allowShowMateri==true ? @$getMateri->description : '',
                            'link'          => $allowShowMateri==true ? @$link : '',
                            'attachment'    => $allowShowMateri==true ? @$attachment : '',
                            'content_name'  => @$getMateri->content_name,
                            'allow'         => $allowShowMateri,
                        ];
                        $contentBySequence[] = [
                            'type' => 'materi', 
                            'id_course_detail'=> $idCourseDetail,
                            'id_course' => $idCourse,
                            'status_active' => $status,
                            'sequence'=> $thisSequence,
                            'progress' => in_array($idCourseDetail, $getSequenceCourse) ? 'done' : 'not_yet',
                            'data' => $thisMateri
                        ];
                        $thisSequence++;
                    }

                    if($idSurveyHeader_ && !$idContentLearning){
                        $getQuiz = DB::table('hr_survey_header as hsh')
                            ->leftJoin('hr_survey_question as hsq', 'hsq.id_survey_header', '=', 'hsh.id_survey_header')
                            ->leftJoin('master_general_data as mgd', 'hsq.id_question_type', '=', 'mgd.id_general_data')
                            ->leftJoin('master_general_data as mgd2', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                            ->select('hsh.*', 'hsq.id_survey_question', 'hsq.sequence', 'hsq.question','hsq.note','hsq.id_question_type','hsq.id_question_group', 'mgd2.description as survey_type', 'mgd.code as type_question')
                            ->where(['hsh.id_survey_header'=> $idSurveyHeader_, 'hsh.status'=> 'A'])
                            ->where(['hsq.status'=> 'A'])
                            ->orderBy('hsq.sequence')->get();

                        $thisQuiz_ = [
                            'quiz_name'         => '',
                            'id_survey_header'  => null,
                            'data'              => [],
                        ];

                        if($getQuiz->count() > 0){
                            $thisQuiz_['quiz_name'] = $getQuiz[0]->description;
                            $thisQuiz_['id_survey_header'] = $getQuiz[0]->id_survey_header;
                            $thisQuestionByQuiz = [];

                            $randomize = DB::selectOne("SELECT hsq.sequence, hsh.id_survey_header
                                                        FROM hr_survey_header hsh 
                                                        JOIN hr_survey_question hsq ON hsq.id_survey_header = hsh.id_survey_header 
                                                        WHERE survey_category = 'LMS'
                                                        AND hsh.id_survey_header = ?
                                                        AND (hsq.sequence IS NULL OR hsq.sequence = 0)",
                                                        [$getQuiz[0]->id_survey_header]);
                            if($randomize) {
                                $getQuiz = $getQuiz->random($questionView);
                            } else {
                                $getQuiz = $getQuiz->take($questionView);
                            }
                           
                            foreach ($getQuiz as $key => $value) {
                                $answer = [];
                                $getAnswer = DB::table('hr_survey_answer as hsa')
                                        ->select('hsa.*', 'hsa.suggested_image as answer_image')
                                        ->where('hsa.id_survey_question', $value->id_survey_question)
                                        ->where('hsa.status', 'A')
                                        ->orderBy('hsa.id_survey_answer')
                                        ->get();

                                $i_answer = 1;
                                foreach ($getAnswer as $i => $val) {
                                    $thisImage = null;
                                    if(!is_null($val->answer_image)){
                                        $filePath   = 'project/storage/app/public/upload/master_answer/';
                                        $thisImage  = url($filePath.$val->answer_image);
                                    }
                                    $answer[] = [
                                        'reference'         => $i_answer,
                                        'answer'            => $val->suggested_answer,
                                        'image'             => $thisImage,
                                        'id_survey_answer'  => $val->id_survey_answer,
                                        'id_survey_question'=> $val->id_survey_question,
                                    ];
                                    $i_answer++;
                                }
                                array_multisort(array_column($answer, 'reference'), SORT_ASC, $answer);
                                $thisQuestionByQuiz[] = [
                                    'id_survey_question'=> $value->id_survey_question,
                                    'id_question_group' => $value->id_question_group,
                                    'type'              => $value->type_question,
                                    'question'          => $value->question,
                                    'note'              => $value->note,
                                    'sequence'          => $value->sequence,
                                    'answer'            => $answer,
                                ];
                                $idSurveyHeader[]       = @$value->id_survey_header;
                                $idSurveyQuestion[]     = $value->id_survey_question;
                            }
                            $thisQuiz_['data'] = $thisQuestionByQuiz;

                            $getAnswerUserHeader = DB::table('hr_survey_answer_user_header as hsauh')
                                ->where('id_survey_header', $thisQuiz_['id_survey_header'])
                                ->whereIn('id_employee', $getEmployee->pluck('id_employee')->toArray())
                                ->where('id_course_detail', $idCourseDetail)
                                ->where('id_event_program', $program->id_event_program)
                                ->first();
                            if($getAnswerUserHeader){
                                $finishAnswerQuiz = true;

                                $rece = DB::table('relation_event_course_employee as rece')
                                        ->where('id_course_detail', $getAnswerUserHeader->id_course_detail)
                                        ->whereIn('id_employee', $getEmployee->pluck('id_employee')->toArray())
                                        ->where('id_event_program', $program->id_event_program)
                                        ->where('status', 'A')
                                        ->get();
                                if(count($rece) < 1) {
                                    $finishAnswerQuiz = false;
                                }
                            }
                        }

                        $contentBySequence[]    = [
                            'type' => 'quiz', 
                            'id_course_detail'=>$idCourseDetail, 
                            'id_course' => $idCourse,
                            'status_active' => $status,
                            'sequence'=> $thisSequence,
                            'progress' => ($finishAnswerQuiz==true) ? 'done' : 'not_yet',
                            'data' => $thisQuiz_
                        ];
                        $thisSequence++;
                    }
                }
            }

            $lastIdCourseDetail = $contentBySequence[count($contentBySequence)-1]['id_course_detail'];
            $thisSequenceExisting = 1;
            if($getLastCourseCompleted){
                if(!in_array($lastIdCourseDetail, $getSequenceCourse)){
                    $thisSequenceExisting = $getLastCourseCompleted->sequence;
                }
            }
            $return_data = [
                'id_course'     => $idCourse,
                'id_employee'   => $getEmployeeActive->id_employee,
                'sequence_existing'  => $thisSequenceExisting,
                'progress'      => (count($getSequenceCourse)==count($contentBySequence)) ? 'done' : 'not_yet',
                'course'        => @$courseName,
                'class'         => @$className,
                'notes'         => '',
                'content'       => $contentBySequence,
                'program'       => $program,
            ];

            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Berhasil', 'data' => $return_data]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    protected function save(Request $request) {
        DB::beginTransaction();
        try {
            if($request->id_question){
                $allScore = [];
                $countAnswer = [];
                $totalScore = null;
                $nomorSoal = [];
                $answerScoreByQuestion = [];
                $correctAnswerByQuestion = [];
                $divider = [];

                foreach ($request->id_question as $key => $id_question) {
                    $answer = 'answer_'.$id_question;
                    $answerType = 'answer_type_'.$id_question;
                    $sequenceAnswer = 'sequence_answer_'.$id_question;
                    $sequenceAnswerCount = 'sequence_answer_count_'.$id_question;
                    $nomorSoal[] = $key+1;

                    // untuk menghitung jawaban yg sudah dijawab tau yg belum
                    if(!in_array($request->$answerType, ['Essay', 'Upload_Files'])){ // jika mengisi jawaban pilihan
                        if($request->$answerType == 'Sequence_Answer'){
                            if(@$request->$sequenceAnswer){
                                $ex_seq_answer = explode(',', @$request->$sequenceAnswer);
                                if(count($ex_seq_answer) == @$request->$sequenceAnswerCount){
                                    $countAnswer[] = $key+1;
                                }
                            }
                        } else {
                            if($request->$answer){
                                $countAnswer[] = $key+1;
                            }
                        }
                    } else if($request->$answerType == 'Essay') {
                        if(!is_null($request->$answer[0])){ // jika mengisi jawaban Essay
                            $countAnswer[] = $key+1;
                        }
                    } else {
                        $path = "upload/lms/$request->id_survey_header";
                        $fileName = uniqid().".".$request->$answer->getClientOriginalExtension();
                        $request->file($answer)->storeAs("public/$path", $fileName);
                        $dbFilePath = "$path/$fileName";
                        $countAnswer[] = $key+1;
                    }

                    //utk menaruh jawaban ke DB
                    if($request->$answerType != 'Sequence_Answer'){
                        //Selain Sequence Answer
                        if($request->$answer){
                            $answerScoreByQuestion[$id_question] = [];
                            $correctAnswerByQuestion[$id_question] = [];
                            $getCorrectedAnswer = HrSurveyAnswer::where('id_survey_question', $id_question)->where('is_corrected_answer',true)->get();
                            $allCorrectedByQuestion = $getCorrectedAnswer->count();
                            
                            if($request->$answerType == 'Upload_Files') {
                                $answer_user[] = [
                                    'id_survey_answer_user_header'  => '',
                                    'id_survey_question'            => $id_question,
                                    'id_survey_answer'              => null,
                                    'description_answer'            => null,
                                    'id_company'                    => session('id_company'),
                                    'created_by'                    => session('id_user'),
                                    'attachment'                    => @$dbFilePath,
                                ];
                            }

                            foreach ($request->$answer as $i => $id_answer) {
                                if($request->$answerType != 'Essay'){
                                    $id_survey_answer   = $id_answer;
                                    $description_answer = null;
                                } else {
                                    $id_survey_answer   = null;
                                    $description_answer = strip_tags($id_answer);
                                }
                                $answer_user[] = [
                                    'id_survey_answer_user_header'  => '',
                                    'id_survey_question'            => $id_question,
                                    'id_survey_answer'              => $id_survey_answer,
                                    'description_answer'            => $description_answer,
                                    'id_company'                    => session('id_company'),
                                    'created_by'                    => session('id_user'),
                                ];

                                if($request->$answerType != 'Essay'){
                                    $answerScore = HrSurveyAnswer::where('id_survey_answer', $id_survey_answer)->first();
                                    if(@$answerScore->is_corrected_answer == true){
                                        $correctAnswerByQuestion[$id_question][] = @$answerScore->score_answer ? (int)@$answerScore->score_answer : 1;
                                    }
                                }
                            }
                            if($allCorrectedByQuestion > 0){
                                //misal 1 question trdpat 2 jawaban benar, dn user hny mnjawab 1 yg benar maka
                                //(1 / 2) = 0.5 jadi nilai di question ini yaitu 0,5 . kalau benar smua maka 1
                                $thisCorrectAnswer = count($correctAnswerByQuestion[$id_question]);
                                $answerScoreByQuestion[$id_question] = ($thisCorrectAnswer / $allCorrectedByQuestion);
                                $divider[] = $id_question;
                            }
                        }
                    } else {
                        $answer_user[] = [
                            'id_survey_answer_user_header'  => '',
                            'id_survey_question'            => $id_question,
                            'id_survey_answer'              => null,
                            'description_answer'            => @$request->$sequenceAnswer,
                            'id_company'                    => session('id_company'),
                            'created_by'                    => session('id_user'),
                            'attachment'                    => @$dbFilePath,
                        ];
                    }
                }
                if(count($countAnswer) < count($request->id_question)){
                    $nomorSoalKosong = collect($nomorSoal)->diff($countAnswer);
                    // jika jumlah jawaban trmasuk essay kurang dari jumlah soal maka tampil notif
                    throw new \Exception("Mohon melengkapi jawaban pada nomor soal berikut :\n".collect($nomorSoalKosong)->implode(', '));
                }

                $idEmployee = DB::table('hr_employee as he')->select('he.id_employee')
                            ->where('he.id_user', session('id_user'))
                            ->where('he.status', 'A')
                            ->where('he.id_company', session('id_company'))
                            ->first()->id_employee;

                if(count($divider) > 0){
                    $thisCourseDetail = DB::table('master_course_detail')->where('id_course_detail', $request->id_course_detail)->first();
                    // ((count of corrected_answer / num.of question) * 100) * (weight / 100)
                    $totalScore = (array_sum($answerScoreByQuestion) / count($divider)) * 100;
                    $bobot = (@$thisCourseDetail->weight / 100);
                    $totalScore = round($totalScore * $bobot, 2);
                    
                    if($thisCourseDetail && $thisCourseDetail->course_type == 'Quiz_Remidial') {
                        $hep = HrEventProgram::find($request->id_event_program);
                        if($hep && $hep->pass_scores && $totalScore > $hep->pass_scores) {
                            $totalScore = $hep->pass_scores;
                        }
                    }
                }

                $answer_user_header = [
                    'id_survey_header'  => $request->id_survey_header,
                    'total_score'       => $totalScore,
                    'id_employee'       => $idEmployee,
                    'id_course_detail'  => $request->id_course_detail,
                    'id_company'        => session('id_company'),
                    'created_by'        => session('id_user'),
                    'id_event_program'  => $request->id_event_program,
                ];
                
                $existingAnswer = HrSurveyAnswerUserHeader::where('id_employee', $idEmployee)->where('id_course_detail', $request->id_course_detail)->where('id_event_program', $request->id_event_program)->first();
                if($existingAnswer) {
                    throw new \Exception("Failed saving answer. You have answered this quiz before.");
                }

                $insertAnswerHeader = HrSurveyAnswerUserHeader::create($answer_user_header);
                foreach ($answer_user as $key => $item) {
                    $answer_user[$key]['id_survey_answer_user_header'] = $insertAnswerHeader->id_survey_answer_user_header;
                }
                HrSurveyAnswerUser::insert($answer_user);
            }
            
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function saveCourse(Request $request) {
        DB::beginTransaction();
        try {
            $idCourseDetail = $request->id_course_detail;
            $idEventProgram = $request->id_event_program;

            $getEmployee = DB::table('hr_employee')->where('status', 'A')->where('id_user', session('id_user'))->first();
            $idEmployee = @$getEmployee->id_employee;

            $dataInsert = [
                'id_course_detail' => $idCourseDetail,
                'id_event_program' => $idEventProgram,
                'id_employee' => $idEmployee,
                'is_completed' => true,
                'status' => 'A',
                'id_company' => session('id_company'),
                'created_by' => session('id_user')
            ];

            $get = DB::table('relation_event_course_employee')
                ->where('id_course_detail', $idCourseDetail)
                ->where('id_event_program', $idEventProgram)
                ->where('id_employee', $idEmployee)
                ->first();

            if(!$get){
                $insert = DB::table('relation_event_course_employee')->insert($dataInsert);
            }


            DB::commit();
            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function finishCourse(Request $request) {
        DB::beginTransaction();
        try {
            $idCourseDetail = $request->id_course_detail;
            $idEventProgram = $request->id_event_program;

            $getEmployee = DB::table('hr_employee')->where('status', 'A')->where('id_user', session('id_user'))->first();
            $idEmployee = @$getEmployee->id_employee;

            $finish = HrEventAttendees::where('id_event_program', $idEventProgram)->where('booked_by', $idEmployee)->update(['status'=>'Attended']);

            DB::commit();
            return response()->json(['status' => true, 'message' => 'success']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function randomArrayNumber($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }
}
