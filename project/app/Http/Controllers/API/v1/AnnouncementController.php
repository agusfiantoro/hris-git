<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use App\Models\API\Announcement;
use App\Models\Curl;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;


class AnnouncementController extends BaseController
{
    public function __construct()
    {

    }

    public function getAnnouncementTypes(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
                'id_company' => 'nullable'
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){ throw new ValidationException($validator); }

            $nik = $request->nik ?? null;

            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }

            $employee = $getEmployeeDetail[0];
            $idCompany          = $request->id_company ?? $employee->id_company;
            
            $getAnnouncementTypes = Announcement::getAnnouncementTypes($idCompany);
            $result = [
                'types' => $getAnnouncementTypes,
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

    public function markAsRead(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
                'id_announcement' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required',
                'id_announcement.required' => 'id_announcement is required',
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $idAnnouncement = $request->id_announcement ?? null;

            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $result[0];
            
            $read = Announcement::readAnnouncement($employee, $idAnnouncement);

            $result = [
                'id_announcement' => $request->id_announcement,
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

    public function allAnnouncement(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);
            $parameterValidation = [
                'nik' => 'required',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'id_company' => 'nullable'
            ];

            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $today         = date('Y-m-d');
            $sub6Months    = Carbon::parse($today)->subMonths(6)->format('Y-m-d');

            $nik = $request->nik ?? null;
            $startDate = $request->start_date ?? $sub6Months;
            $endDate = $request->end_date ?? $today;
            $idAnnouncement = $request->id_announcement ?? null;
            $type_code = $request->type_code ?? null;

            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $result[0];
            $idCompany  = $request->id_company ?? $employee->id_company;
            $idUser  = $employee->id_user;

            $getAnnouncement = Announcement::getAllAnnouncement($idCompany, $idUser, $startDate, $endDate, $idAnnouncement, $type_code);

            $result = [
                'announcement' => $getAnnouncement,
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