<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswer;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUser;
use App\Models\Employee\EmployeeSetting\HrSurveyAnswerUserHeader;
use App\Models\API\Employee;
use App\Models\API\Survey;
use App\Models\Curl;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class SurveyController extends BaseController
{
    public function __construct()
    {

    }

    public function getActiveSurveys(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
                'id_company' => 'nullable'
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required'
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $idSurvey = $request->id_survey ?? null;

            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, true);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $getEmployeeDetail[0];
            $throwSuccess = false;

            if(!$idSurvey){
                $getActive = Survey::getActiveIdSurvey($employee);
                if(count($getActive) > 0){
                    $idSurvey = $getActive[0];
                } else {
                    $throwSuccess = true;
                }
            }

            if($throwSuccess){
                $showSurvey = [];
            } 
            else {
                $idEmployee    = $employee->id_employee;
                $idUser        = $employee->id_user;
                $idCompany     = $request->id_company ?? $employee->id_company;
                $today         = date('Y-m-d');

                $getSurvey = DB::table('hr_survey_header as hsh')
                    ->leftJoin('master_general_data as mgd', 'mgd.id_general_data', '=', 'hsh.id_survey_type')
                    ->leftJoin('hr_survey_history as hshi', 'hshi.id_survey_header', '=', 'hsh.id_survey_header')
                    ->select('hsh.*', 'mgd.description as survey_type', 'hshi.end_date as end_history', 'hshi.id_survey_history')
                    ->whereRaw('(
                        (hsh.is_cross_company_os = FALSE AND "hsh"."id_company" = ?)
                        OR
                        (hsh.is_cross_company_os = TRUE)
                    )', [$idCompany])
                    ->where('hsh.published', true)
                    ->where('hsh.survey_category', 'Survey')
                    ->where('hsh.id_survey_header', $idSurvey)
                    ->where(function ($query){
                        $query->whereDate('hshi.start_date', '<=', date('Y-m-d'))->whereDate('hshi.end_date', '>=', date('Y-m-d'));
                        $query->where('hshi.status', 'A');
                    })
                    ->first();

                if(!$getSurvey){
                    throw new \ValidationException('Anda tidak memiliki akses pada Survey ini');
                }

                $end_date   = strtotime(@$getSurvey->end_history.' 23:59:59');
                $now        = strtotime(date('Y-m-d H:i:s'));
                if($now > $end_date){
                    throw new \ValidationException('Waktu Telah Berakhir');
                }
                
                $showSurvey = Survey::showSurvey($employee, $idSurvey);
            }

            $result = [
                'survey' => $showSurvey,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function submit(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
                'id_survey_header' => 'required',
                'id_survey_history' => 'required',
                'questions' => 'required|array',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required',
                'id_survey_header.required' => 'id_survey is required',
                'id_survey_history.required' => 'id_survey_history is required',
                'questions.required' => 'data question is required',
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik                = $request->nik ?? null;
            $idSurvey           = $request->id_survey_header;
            $idSurveyHistory    = $request->id_survey_history;
            $questions          = $request->questions;

            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }

            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;
            $allScore       = [];
            $countAnswer    = [];
            $nomorSoal      = [];
            $answerUser     = [];
            $totalScore     = 0;

            $checkAnswerUser = HrSurveyAnswerUserHeader::where('id_employee', $idEmployee)
                ->where('id_survey_header', $idSurvey)
                ->where('id_company', $idCompany)
                ->where('id_survey_history', $idSurveyHistory)
                ->first();

            if(!$checkAnswerUser){
                foreach ($questions as $key => $thisQuestion) {
                    $nomorSoal[] = $key+1;

                    if($thisQuestion['type'] != 'Essay'){
                        $idSurveyAnswer     = $thisQuestion['answer_value'];
                        $descriptionAnswer  = null;
                        $countAnswer[]      = $key+1; // jika mengisi jawaban pilihan

                        $answerScore = HrSurveyAnswer::where('id_survey_answer', $idSurveyAnswer)->first()->score_answer;
                        $allScore[] = is_null($answerScore) ? 0 : (float)$answerScore;
                    } 
                    else {
                        $idSurveyAnswer     = null;
                        $descriptionAnswer  = $thisQuestion['answer_value'];

                        if(!is_null($descriptionAnswer)){ // jika mengisi jawaban Essay
                            $countAnswer[] = $key+1;
                        }
                    }
                    $answerUser[] = [
                        'id_survey_answer_user_header'  => '',
                        'id_survey_question'            => $thisQuestion['id_question'],
                        'id_survey_answer'              => $idSurveyAnswer,
                        'description_answer'            => $descriptionAnswer,
                        'id_company'                    => $idCompany,
                        'created_by'                    => $idUser,
                    ];
                }

                if(count($countAnswer) < count($questions)){
                    $nomorSoalKosong = collect($nomorSoal)->diff($countAnswer);
                    // jika jumlah jawaban trmasuk essay kurang dari jumlah soal maka tampil notif
                    throw new \ValidationException("Mohon melengkapi jawaban pada nomor soal berikut :\n".collect($nomorSoalKosong)->implode(', '));
                }
                if(count($allScore) > 0){
                    $totalScore = array_sum($allScore) / count($allScore);
                }

                $answerUserHeader = [
                    'id_survey_header'  => $idSurvey,
                    'id_survey_history' => $idSurveyHistory,
                    'total_score'       => $totalScore,
                    'id_employee'       => $idEmployee,
                    'id_company'        => $idCompany,
                    'created_by'        => $idUser,
                ];
                
                $insertAnswerHeader = HrSurveyAnswerUserHeader::create($answerUserHeader);
                foreach ($answerUser as $key => $item) {
                    $answerUser[$key]['id_survey_answer_user_header'] = $insertAnswerHeader->id_survey_answer_user_header;
                }
                HrSurveyAnswerUser::insert($answerUser);
            }

            $result = [
                'survey' => $idSurvey,
            ];
            $message    = 'Data was successfully insert';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function allSurveys(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required'
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, true);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $getEmployeeDetail[0];
            
            $getAllSurveyByUser = Survey::getAllSurveyByUser($employee);
            $result = [
                'survey' => $getAllSurveyByUser,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

    public function getActiveSurveyToWeb(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required'
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $allSurvey = null;

            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, true);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $getEmployeeDetail[0];

            $getActive = Survey::getActiveIdSurvey($employee);
            if(count($getActive) > 0){
                foreach ($getActive as $key => $val) {
                    $detailSurvey = Survey::detailSurvey($val);
                    $expired = Carbon::parse(date('Y-m-d H:i:s'))->addMinutes(30)->format('Y-m-d H:i:s');
                    $token = Crypt::encryptString(json_encode(['nik'=>$nik, 'expired'=>$expired, 'id'=>$val]));
                    $detailSurvey['link'] = url('survey').'/'.$token;
                    $allSurvey[] = $detailSurvey;
                }
            }

            $result = [
                'survey' => $allSurvey,
            ];
            $message    = 'Data was successfully sent';
            DB::commit();
            return $this->mobileSuccess($message, $result);
        } catch (ValidationException $e){
            DB::rollback();
            return $this->mobileErrorValidation($e);
        } catch (QueryException $e){
            DB::rollback();
            return $this->mobileErrorQuery($e);
        } catch (\Exception $e) {
            DB::rollback();
            if(in_array($e->getCode(), [422,404])){ return $this->mobileErrorCustom($e); }
            return $this->mobileError($e);
        }
    }

}