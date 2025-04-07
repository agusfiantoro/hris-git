<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Models\API\Employee;
use App\Models\Home;
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


class CampaignController extends BaseController
{
    public function __construct()
    {

    }

    public function listCampaign(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request);

            $parameterValidation = [
                'nik' => 'required',
                'start_date' => 'date_format:Y-m-d',
                'end_date' => 'date_format:Y-m-d',
                'id_company_campaign' => 'nullable',
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
            $idCompanyCampaign = $request->id_company_campaign ?? null;

            $result = Employee::getEmployeeDetail($nik, null, false, true, $request->id_company ?? null);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $result[0];
            $idCompany  = $request->id_company ?? $employee->id_company;
            $idUser  = $employee->id_user;
            $idEmployee  = $employee->id_employee;
            $campaign   = null;

            $getCampaign = Home::listCampaign($idCompany, $idEmployee, $startDate, $endDate, $idCompanyCampaign);
            if($getCampaign->count() > 0){
                $campaign   = [];
                foreach ($getCampaign as $k => $val) {
                    $data = [
                        'id_company_campaign' => $val->id_company_campaign,
                        'reference_number' => $val->reference_number,
                        'description' => $val->description,
                        'start_date' => $val->start_date,
                        'end_date' => $val->end_date,
                        'image_poster' => $val->image_poster_path,
                        'attachment' => $val->attachment_path,
                        'link' => $val->link,
                        'is_already_read' => $val->is_already_read,
                    ];
                    $campaign[] = $data;
                }
            }

            $result = [
                'campaign' => $campaign,
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
                'id_company_campaign' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            $messageValidation = [
                'nik.required' => 'nik is required',
                'id_company_campaign.required' => 'id_company_campaign is required',
            ];
            $validator->setCustomMessages($messageValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $idCompanyCampaign = $request->id_company_campaign ?? null;

            $result = Employee::getEmployeeDetail($nik);
            if(count($result) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee = $result[0];
            
            $read = Home::readCampaign($employee, $idCompanyCampaign);

            $result = [
                'id_company_campaign' => $request->id_company_campaign,
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
}