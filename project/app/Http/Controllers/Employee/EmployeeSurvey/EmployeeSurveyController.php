<?php

namespace App\Http\Controllers\Employee\EmployeeSurvey;

use App\Http\Controllers\Auth\LoginController;
use App\Models\Employee\EmployeeSetting\HrSurveyQuestion;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswer;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUser;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUserHeader;
use App\Models\Employee\EmployeeSetting\HrSurveyHeader;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use DataTables;
use Validator;

class EmployeeSurveyController extends Controller {

    public function __construct() {
        $this->LoginController = new LoginController;
    }

    public function employee($id_employee=null){
        $employee = DB::table('hr_employee as he')
                    ->select('he.*')
                    ->where('he.id_company', session('id_company'))
                    ->where('he.status', 'A');
        if($id_employee){
            $employee->where('he.id_employee', $id_employee);
        } else {
            $employee->where('he.id_user', session('id_user'));
        }
        $get = $employee->first();
        return $get;
    }
    
    public function index(Request $request) {
        if ($request->ajax()) {
            $data = HrSurveyHeader::getdatapublished();
            foreach ($data as $key => $item) {
                $checkSurveyStatus = HrSurveyAnswerUserHeader::where('id_survey_header', $item->id_survey_header)
                    ->where('id_survey_history', $item->id_survey_history)
                    ->where('id_employee', @$this->employee()->id_employee)
                    ->first();

                if($checkSurveyStatus != null){
                    $data[$key]->status_survey = 'done';
                } else {
                    $data[$key]->status_survey = '';
                }
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('', function($data) {
                    $a = '';
                    return $a;
                })
                ->addColumn('action', function($data) {
                    return $data->id_survey_header;
                })
                ->editColumn('start_date', function ($data){
                    $return = '-';
                    if(!is_null($data->start_date)){
                        $return = Carbon::parse($data->start_date)->translatedFormat('d F Y');
                    }
                    return $return;
                })
                ->editColumn('end_date', function ($data){
                    $return = '-';
                    if(!is_null($data->end_date)){
                        $return = Carbon::parse($data->end_date)->translatedFormat('d F Y');
                    }
                    return $return;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('employee.employee_survey.employee_answer.index');
    }


    public function join($id=null) {
        DB::beginTransaction();
        try {
            $getSurvey = DB::table('hr_survey_header as hsh')
                ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->select('hsh.*', 'mgd.description as survey_type', 'hshi.end_date as end_history', 'hshi.id_survey_history', 'rps.id_principal', 'rjs.id_job_grade')
                ->whereRaw('(
                    (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                    OR
                    (hsh.is_cross_company_os = TRUE)
                )', [session('id_company')])
                ->where('hsh.published', true)
                ->where('hsh.survey_category', 'Survey')
                ->where('hsh.id_survey_header', $id)
                ->where(function ($query){
                    $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                    $query->where('hshi.status', 'A');
                })
                ->first();
            
            if($getSurvey == null){
                throw new \Exception('Anda tidak memiliki akses pada Survey ini');
            }

            $end_date = strtotime(@$getSurvey->end_history.' 23:59:59');
            $now = strtotime(date('Y-m-d H:i:s'));
            if($now > $end_date){
                throw new \Exception('Survey Telah Berakhir');
            }

            $materi                 = [];
            $quiz                   = [];
            $idSurveyHeader         = [];
            $idSurveyQuestion       = [];
            $totalQuestionAnswered  = [];
            $allCategory            = [];

            $surveyQuestion = DB::table('hr_survey_question as hsq')
                ->select('hsq.*', 'mgd.code as type_question', 'mgd2.description as category_question')
                ->leftJoin('master_general_data as mgd', 'hsq.id_question_type', '=', 'mgd.id_general_data')
                ->leftJoin('master_general_data as mgd2', 'hsq.id_question_group', '=', 'mgd2.id_general_data')
                ->where(['hsq.id_survey_header'=> $id, 'hsq.status'=> 'A'])
                ->orderBy('mgd2.id_general_data')->orderBy('hsq.sequence')->get();
            foreach ($surveyQuestion as $key => $value) {
                $answer = [];
                $getAnswer = DB::table('hr_survey_answer as hsa')
                        ->join('master_survey_answer as msa', 'msa.id_answer', '=', 'hsa.id_answer', 'left')
                        ->select('hsa.*', 'msa.description_answer', 'msa.suggested_image as answer_image','msa.code as answer_code')
                        ->where('hsa.id_survey_question', $value->id_survey_question)
                        ->orderBy('msa.code', 'asc')
                        ->get();
                $i_answer = 1;
                foreach ($getAnswer as $i => $val) {
                    $thisImage = null;
                    if(!is_null($val->answer_image)){
                        $filePath   = 'project/storage/app/public/upload/master_answer/';
                        $thisImage  = url($filePath.$val->answer_image);
                    }
                    $answer[] = [
                        'reference'         => is_null($val->id_answer) ? $val->reference_number : $i_answer,
                        'answer'            => is_null($val->id_answer) ? $val->suggested_answer : $val->description_answer,
                        'image'             => $thisImage,
                        'id_survey_answer'  => $val->id_survey_answer,
                        'id_survey_question'=> $val->id_survey_question,
                        'answer_code'       => $val->answer_code,
                    ];
                    $i_answer++;
                }
                array_multisort(array_column($answer, 'reference'), SORT_ASC, $answer);
                $quiz[] = [
                    'course_name'       => @$getSurvey->description,
                    'id_survey_header'  => @$getSurvey->id_survey_header,
                    'id_survey_history' => @$getSurvey->id_survey_history,
                    'id_survey_question'=> $value->id_survey_question,
                    'id_question_group' => $value->id_question_group,
                    'category'          => $value->category_question,
                    'type'              => $value->type_question,
                    'question'          => $value->question,
                    'img_question'      => $value->attachment,
                    'note'              => $value->note,
                    'sequence'          => $value->sequence,
                    'answer'            => $answer,
                ];
                $idSurveyHeader[]       = @$getSurvey->id_survey_header;
                $idSurveyQuestion[]     = $value->id_survey_question;

                if(!in_array($value->category_question, $allCategory)){
                    $allCategory[] = $value->category_question;
                }
            }

            if(count($idSurveyHeader) > 0){
                $idSurveyHeader_    = array_unique($idSurveyHeader);
                $idSurveyQuestion_  = array_unique($idSurveyQuestion);
                $idEmployee = DB::table('hr_employee as he')->select('he.id_employee')
                            ->where('he.id_user', session('id_user'))->where('he.status', 'A')
                            ->first()->id_employee;
                $getAnswerUser = DB::table('hr_survey_answer_user as hsau')
                        ->join('hr_survey_answer_user_header as hsauh', 'hsauh.id_survey_answer_user_header', '=', 'hsau.id_survey_answer_user_header', 'left')
                        ->select('hsau.*')
                        ->whereIn('hsauh.id_survey_header', $idSurveyHeader_)
                        ->where('hsauh.id_employee', $idEmployee)
                        ->where('hsauh.id_survey_history', @$getSurvey->id_survey_history)
                        ->get();
                
                foreach ($getAnswerUser as $k => $item) {
                    if(in_array($item->id_survey_question, $idSurveyQuestion_)){
                        $totalQuestionAnswered[] = 1;
                    }
                }
            }

            $return_data = [
                'class'         => @$getSurvey->survey_type.' : '.@$getSurvey->description,
                'end_date'      => @$getSurvey->end_history,
                'notes'         => @$getSurvey->notes,
                'materi'        => $materi,
                'quiz'          => $quiz,
                'quiz_status'   => (count($totalQuestionAnswered) > 0) ? 'done' : '',
                'category'      => $allCategory
            ];
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Berhasil', 'data' => $return_data]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function validateSurveyUpload(Request $request, $parameter=[], $validatorMessage=[]) {
        if($request->id_question){
            foreach ($request->id_question as $key => $id_question) {
                $answer = 'answer_'.$id_question;
                $answerType = 'answer_type_'.$id_question;

                if($request->$answerType == 'Upload_Files'){ // jika mengisi upload file
                    $thisNomor = $key+1;
                    $parameter[$answer.'.*'] = [
                        'required',
                        'file',
                        function ($attribute, $value, $fail) use ($thisNomor) {
                            $allowedTypes = ['pdf', 'jpeg', 'jpg']; //atau ['pdf', 'jpeg', 'jpg']
                            $allowedSizes = ['pdf' => 300, 'jpeg' => 1024, 'jpg' => 1024];
                            $extension = $value->getClientOriginalExtension();
                            $size = $value->getSize();

                            if (!in_array($extension, $allowedTypes)) {
                                $fail("No. ".$thisNomor.": Only (pdf/jpg) files are allowed");
                            }
                            if (in_array($extension, $allowedTypes)) {
                                if ($size > $allowedSizes[$extension] * 1024) {
                                    $fail("No. ".$thisNomor.": The maximum file size is {$allowedSizes[$extension]} KB.");
                                }
                            }
                        },
                    ];
                }
            }
        }
        $validator = Validator::make($request->all(), $parameter, $validatorMessage);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    protected function save(Request $request) {
        DB::beginTransaction();
        try {
            self::validateSurveyUpload($request);

            if($request->id_question){
                $allScore = [];
                $countAnswer = [];
                $totalScore = 0;
                $nomorSoal = [];
                $answerScoreByQuestion = [];
                $correctAnswerByQuestion = [];
                $divider = [];
                $storeAttachment = [];
                $filePath = 'public/upload/survey';

                $getEmployee = DB::table('hr_employee as he')->select('he.id_employee','he.nik_employee')
                            ->where('he.id_user', session('id_user'))
                            ->where('he.id_company', session('id_company'))
                            ->where('he.status', 'A')
                            ->first();

                foreach ($request->id_question as $key => $id_question) {
                    $answer = 'answer_'.$id_question;
                    $answerType = 'answer_type_'.$id_question;
                    $sequenceAnswer = 'sequence_answer_'.$id_question;
                    $sequenceAnswerCount = 'sequence_answer_count_'.$id_question;
                    $nomorSoal[] = $key+1;

                    // untuk menghitung jawaban yg sudah dijawab tau yg belum
                    if($request->$answerType != 'Essay'){ // jika mengisi jawaban pilihan
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
                    } else {
                        if(!is_null($request->$answer[0])){ // jika mengisi jawaban Essay
                            $countAnswer[] = $key+1;
                        }
                    }

                    //utk menaruh jawaban ke DB
                    if($request->$answerType != 'Sequence_Answer'){
                        //Selain Sequence Answer
                        if($request->$answer){
                            $answerScoreByQuestion[$id_question] = [];
                            $correctAnswerByQuestion = [];
                            $dividerByQuestion = [];
                            foreach ($request->$answer as $i => $id_answer) {
                                $attachmentName = null;

                                if($request->$answerType != 'Essay'){
                                    if($request->$answerType == 'Upload_Files'){ // jika mengisi upload file
                                        $newFileName = @$getEmployee->nik_employee.'_'.$request->id_survey_header.'_'.Str::random(5);
                                        $makeDir = Storage::makeDirectory($filePath, 0775, true, true);

                                        if($request->$answer[$i]->getClientOriginalExtension() != 'pdf'){
                                            $thisExtension = 'jpg';
                                            //resize jadi 700 pixel (sekitar 100Kb) utk file tipe foto 
                                            $resizeImage = Image::make($request->$answer[$i])->resize(700, null, function ($thisImg) {
                                                $thisImg->aspectRatio();
                                            });
                                            $resizedImageContent = $resizeImage->encode();
                                            $attachmentName = $newFileName.'.'.$thisExtension;
                                            $storeAttachment[] = [
                                                'filepath' => $filePath.'/'.$attachmentName,
                                                'attachment' => $resizedImageContent,
                                            ];
                                        } else {
                                            $thisExtension = 'pdf';
                                            $attachmentName = $newFileName.'.'.$thisExtension;
                                            $storeAttachment[] = [
                                                'filepath' => $filePath.'/'.$attachmentName,
                                                'attachment' => file_get_contents($request->$answer[$i]),
                                            ];
                                        }
                                        $id_survey_answer   = null;
                                        $description_answer = null;
                                    } else {
                                        $id_survey_answer   = $id_answer;
                                        $description_answer = null;
                                    }
                                } else {
                                    $id_survey_answer   = null;
                                    $description_answer = $id_answer;
                                }

                                $answer_user[] = [
                                    'id_survey_answer_user_header'  => '',
                                    'id_survey_question'            => $id_question,
                                    'id_survey_answer'              => $id_survey_answer,
                                    'description_answer'            => strip_tags($description_answer),
                                    'attachment'                    => $attachmentName,
                                    'id_company'                    => session('id_company'),
                                    'created_by'                    => session('id_user'),
                                ];

                                if($request->$answerType != 'Essay'){
                                    $answerScore = HrSurveyAnswer::where('id_survey_answer', $id_survey_answer)->first();
                                    if(!is_null($answerScore)){
                                        if(@$answerScore->is_corrected_answer == true){
                                            $correctAnswerByQuestion[] = (int)$answerScore->score_answer;
                                        }
                                        $dividerByQuestion[] = (int)$answerScore->score_answer;
                                    }
                                }
                            }
                            if(count($dividerByQuestion) > 0){
                                $answerScoreByQuestion[$id_question] = array_sum($correctAnswerByQuestion);
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
                        ];
                    }
                }
                if(count($countAnswer) < count($request->id_question)){
                    $nomorSoalKosong = collect($nomorSoal)->diff($countAnswer);
                    // jika jumlah jawaban trmasuk essay kurang dari jumlah soal maka tampil notif
                    throw new \Exception("Mohon melengkapi jawaban pada nomor soal berikut :\n".collect($nomorSoalKosong)->implode(', '));
                }
                if(count($divider) > 0){
                    $totalScore = array_sum($answerScoreByQuestion);
                    $totalScore = round($totalScore, 2);
                }

                if(count($storeAttachment) > 0){
                    foreach ($storeAttachment as $k => $val) {
                        Storage::put($val['filepath'], $val['attachment']);
                    }
                }
                $idEmployee = $getEmployee->id_employee;
                $answer_user_header = [
                    'id_survey_header'  => $request->id_survey_header,
                    'id_survey_history' => $request->id_survey_history,
                    'total_score'       => $totalScore,
                    'id_employee'       => $idEmployee,
                    'id_company'        => session('id_company'),
                    'created_by'        => session('id_user'),
                ];
                $checkExistingAnswer = DB::table('hr_survey_answer_user_header')
                                        ->where('id_employee', $idEmployee)
                                        ->where('id_survey_history', $request->id_survey_history)
                                        ->get();
                if(count($checkExistingAnswer) > 0) {
                    throw new \Exception("You have answered this survey before.");
                }
                $insertAnswerHeader = HrSurveyAnswerUserHeader::create($answer_user_header);
                foreach ($answer_user as $key => $item) {
                    $answer_user[$key]['id_survey_answer_user_header'] = $insertAnswerHeader->id_survey_answer_user_header;
                    if(!array_key_exists('attachment', $answer_user[$key])) {
                        $answer_user[$key]['attachment'] = null;
                    }
                }
                HrSurveyAnswerUser::insert($answer_user);
            }
            
            DB::commit();
            return response()->json(['status' => 'true', 'message' => 'Answer Saved Successfully !!']);
        } catch (ValidationException $e){
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => collect($e->validator->errors()->all())->implode("\n")]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['status' => 'false', 'message' => $e->getMessage()]);
        }
    }

    public function toSurvey(Request $request, $token) {
        DB::beginTransaction();
        try {
            try {
                $decrypted = json_decode(Crypt::decryptString($token));
                $nik = $decrypted->nik;
                $expired = Carbon::parse($decrypted->expired)->timestamp;
                $idSurvey = $decrypted->id;
            } catch (DecryptException $e) {
                throw new \Exception('Link is Invalid');
            }

            $today = Carbon::parse(date('Y-m-d H:i:s'))->timestamp;
            if($expired < $today){
                throw new \Exception('Link is Expired');
            }

            $getUser = DB::table('master_users as mu')
                ->select('mu.id_user', 'mu.access_group', 'mu.default_company', 'mc.company_name', 'mu.user_name', )
                ->leftJoin('master_company as mc', 'mc.id_company', '=', 'mu.default_company')
                ->where('mu.user_name', $nik)->where('mu.status', 'A')->first();
            if(!$getUser){
                throw new \Exception('User not found');
            }

            $getEmployee = DB::table('hr_employee')->where('id_user', $getUser->id_user)->first();
            $checkShowSurvey = self::checkShowUserSurvey($getUser->id_user, @$getEmployee->id_company, 'Survey');
            if(!$checkShowSurvey){
                throw new \Exception('No survey to show');
            }

            $data = [
                'id_user'       => $getUser->id_user,
                'access_group'  => $getUser->access_group,
                'id_company'    => $getUser->default_company,
                'company_name'  => $getUser->company_name,
                'username'      => $getUser->user_name,
            ];
            $this->LoginController->set_session($request, $data);

            $redirect = [
                null                    => 'time_attendance/attendance/attendance?s='.$idSurvey,
                'Default_User'          => 'time_attendance/attendance/attendance?s='.$idSurvey,
                'Default_Manager'       => 'time_attendance/attendance/attendance?s='.$idSurvey,
                'Default_Administrator' => 'time_attendance/attendance/attendance?s='.$idSurvey,
            ];
            return redirect($redirect[$getUser->access_group]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return redirect('/login')->with('error', $e->getMessage());
        }
    }

    public function checkShowUserSurvey($idUser, $idCompany, $type) {
        DB::beginTransaction();
        try {
            $idShow = [];
            $_surveyActive = DB::table('hr_survey_header as hsh')
                ->leftJoin('relation_department_surveys as rds', 'rds.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_regional_surveys as rgs', 'rgs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_branch_surveys as rbs', 'rbs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_jobgrade_surveys as rjs', 'rjs.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('relation_principal_surveys as rps', 'rps.id_survey_header', '=', 'hsh.id_survey_header')
                ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                ->select('hsh.id_survey_header', 'hshi.end_date', 'hshi.id_survey_history', 'rds.id_dept as id_department', 'rgs.id_region', 'rbs.id_branch', 'rjs.id_job_grade', 'rps.id_principal')
                ->where(function ($query){
                    $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                    $query->where('hshi.status', 'A');
                })
                ->where('hsh.published', true)->where('hsh.status', 'A')->where('hsh.id_company', $idCompany)
                ->where('hsh.survey_category', $type)->get();
            if(@$_surveyActive){
                $id_survey_header = [];     $activeSurvey = [];
                $_myEmployee = DB::table('hr_employee as he')->select('he.id_employee','mjp.id_dept','mb.id_region','mpd.id_branch', 'mpr.id_job_grade', 'rpp.id_principal')
                        ->leftJoin('master_position_detail as mpd', 'mpd.id_employee', '=', 'he.id_employee')
                        ->leftJoin('master_branch as mb', 'mb.id_branch', '=', 'mpd.id_branch')
                        ->leftJoin('master_position_routing as mpr', 'mpr.id_routing', '=', 'mpd.id_position_routing')
                        ->leftJoin('master_job_position as mjp', 'mjp.id_position', '=', 'mpr.id_position')
                        ->leftJoin('master_job_grade as mjg', 'mjg.id_job_grade', '=', 'mpr.id_job_grade')
                        ->leftJoin('relation_positiondetail_principal as rpp', 'rpp.id_position_detail', 'mpd.id_position_detail')
                        ->where('he.id_user', $idUser)
                        ->where('he.status', 'A')
                        ->where('he.id_company', $idCompany)
                        ->where('mpd.secondary_position', false)
                        ->first();

                $allSurvey = [];
                $surveyDetail = [];
                $idSurveyHistory = [];
                foreach ($_surveyActive as $key => $val) {
                    $idSurveyHistory[] = $val->id_survey_history;
                    $idSurvey = $val->id_survey_header;
                    if(!in_array($idSurvey, $allSurvey)){
                        $allSurvey[] = $idSurvey;
                        $surveyDetail[$idSurvey] = [
                            'id_department' => [],
                            'id_region' => [],
                            'id_branch' => [],
                            'id_job_grade' => [],
                            'id_principal' => [],
                        ];
                        $activeSurvey[] = ['id'=>$idSurvey, 'end'=>$val->end_date];
                    }
                    $id_department = $val->id_department;
                    $id_region = $val->id_region;
                    $id_branch = $val->id_branch;
                    $id_job_grade = $val->id_job_grade;
                    $id_principal = $val->id_principal;

                    if(!is_null($id_department) && !in_array($id_department, $surveyDetail[$idSurvey]['id_department'])){
                        $surveyDetail[$idSurvey]['id_department'][] = $id_department;
                    }
                    if(!is_null($id_region) && !in_array($id_region, $surveyDetail[$idSurvey]['id_region'])){
                        $surveyDetail[$idSurvey]['id_region'][] = $id_region;
                    }
                    if(!is_null($id_branch) && !in_array($id_branch, $surveyDetail[$idSurvey]['id_branch'])){
                        $surveyDetail[$idSurvey]['id_branch'][] = $id_branch;
                    }
                    if(!is_null($id_job_grade) && !in_array($id_job_grade, $surveyDetail[$idSurvey]['id_job_grade'])){
                        $surveyDetail[$idSurvey]['id_job_grade'][] = $id_job_grade;
                    }
                    if(!is_null($id_principal) && !in_array($id_principal, $surveyDetail[$idSurvey]['id_principal'])){
                        $surveyDetail[$idSurvey]['id_principal'][] = $id_principal;
                    }
                }
                foreach ($surveyDetail as $key => $val) {
                    $surveyFalse = [];
                    if(count($val['id_department']) < 1 && count($val['id_region']) < 1 && count($val['id_branch']) < 1 && count($val['id_job_grade']) < 1){
                        $id_survey_header[] = $key;
                    } else {
                        if(count($val['id_department']) > 0 && !in_array(@$_myEmployee->id_dept, $val['id_department'])){
                            $surveyFalse[] = false;
                        }
                        if(count($val['id_region']) > 0 && !in_array(@$_myEmployee->id_region, $val['id_region'])){
                            $surveyFalse[] = false;
                        }
                        if(count($val['id_branch']) > 0 && !in_array(@$_myEmployee->id_branch, $val['id_branch'])){
                            $surveyFalse[] = false;
                        }
                        if(count($val['id_job_grade']) > 0 && !in_array(@$_myEmployee->id_job_grade, $val['id_job_grade'])){
                            $surveyFalse[] = false;
                        }
                        if(count($val['id_principal']) > 0 && !in_array(@$_myEmployee->id_principal, $val['id_principal'])){
                            $surveyFalse[] = false;
                        }
                    }
                    if(count($surveyFalse) < 1){
                        $id_survey_header[] = $key;
                    }
                }

                if(count($id_survey_header) > 0){
                    $id_survey_header = collect($id_survey_header)->unique()->toArray();

                    $_surveyAnswer = DB::table('hr_survey_answer_user_header')
                            ->select('id_survey_header')
                            ->whereIn('id_survey_header', $id_survey_header)
                            ->whereIn('id_survey_history', $idSurveyHistory)
                            ->where('id_company', $idCompany)
                            ->where('id_employee', @$_myEmployee->id_employee)
                            ->get()->pluck('id_survey_header')->all();

                    if(count($_surveyAnswer) < count($id_survey_header)){
                        $idShow = collect($id_survey_header)->diff($_surveyAnswer)->values();
                    }
                }
            }
            if(count($idShow) < 1){
                throw new \Exception('No survey to show');
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
