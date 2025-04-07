<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\EmailController;
use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\EmployeeRequest\ApprovalDelegation;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\API\Employee;
use App\Models\API\EmployeeRequest;
use App\Models\API\EmployeeApproval;
use App\Models\Curl;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Exception;

class EmployeeRequestController extends BaseController
{
    public function __construct()
    {
        $this->EmailController = new EmailController;
    }

    public function saveImage($file, $saveTo, $sizePixel=300) {
        $saveImage = Image::make($file)->resize($sizePixel, null, function ($constraint) {
            $constraint->aspectRatio();
        })->save(storage_path('app/'. $saveTo));
        return response()->json(['file' => $saveTo]);
    }

    public function saveDocument($file, $saveTo, $fileName) {
        $storagePath = storage_path('app/'.$saveTo);
        $saveDocument = file_put_contents($storagePath.$fileName, $file);
        return response()->json(['file' => $storagePath.$fileName]);
    }

    public function validatorLeave(Request $request, $parameter=null) {
        $parameter['id_leave_type'] = 'required|numeric';
        $parameter['day_type'] = 'required|string';
        $parameter['start_date'] = 'required|date_format:Y-m-d';
        $parameter['end_date'] = 'required|date_format:Y-m-d';

        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    public function validatorAttendance(Request $request, $parameter=null) {
        $parameter['id_leave_type'] = 'nullable';
        $parameter['day_type'] = 'nullable';
        $parameter['actual_time_in'] = 'nullable|before_or_equal:'.date('Y-m-d');
        $parameter['actual_time_out'] = 'nullable|before_or_equal:'.date('Y-m-d');

        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    public function validatorCDO(Request $request, $parameter=null) {
        $parameter['id_leave_type'] = 'nullable';
        $parameter['start_date'] = 'required|date_format:Y-m-d';
        $parameter['end_date'] = 'required|date_format:Y-m-d';

        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    public function messageBackdate($type, $dayLimit) {
        $message = [
            'Leave_Request' => 'Maksimal permintaan ijin tidak masuk adalah '.$dayLimit.' hari dari hari ini',
            'Change_Day_off' => 'Maksimal permintaan cdo adalah '.$dayLimit.' hari dari hari ini',
            'Attendance_Correction' => 'Maksimal permintaan koreksi kehadiran adalah '.$dayLimit.' hari dari hari ini',
        ];
        return $message[$type];
    }

    public function checkApprovalHierarchy($requestType, $idCompany, $idLocation) {
        $getHierarchyApprovalLeave = EmployeeRequest::getHierarchyApproval($requestType, $idCompany, $idLocation);
        if($getHierarchyApprovalLeave->count() < 1){
            throw new \Exception('id_approval_hierarchy belum disetting. ('.$requestType.', IdCompany:'.$idCompany.', idLocation:'.$idLocation.')', 404);
        }
    }

    public function validatorAttachment(Request $request, $parameter=null) {
        if(!$request->attachment){
            throw new \Exception('Mohon menyertakan lampiran', 422);
        }

        if (!base64_decode($request->attachment, true)) {
            $validator->getMessageBag()->add('attachment', 'attachment format is not valid base64');
            throw new ValidationException($validator);
        }

        $base64Attachment = str_replace(' ', '+', str_replace('\/', '/', $request->attachment));
        $allowedMimeType = ['image/png', 'image/jpeg', 'application/pdf'];
        $attachmentMimeType = finfo_buffer(finfo_open(), base64_decode($base64Attachment), FILEINFO_MIME_TYPE);
        if(!in_array($attachmentMimeType, $allowedMimeType)){
            $validator->getMessageBag()->add('attachment', 'attachment must be a file of type: jpg/png/pdf');
            throw new ValidationException($validator);
        }

        $sizeAttachmentInMegaBytes = ((int)(strlen(rtrim($base64Attachment, '=')) * 0.75) / 1024);
        if($sizeAttachmentInMegaBytes > 1024){
            $validator->getMessageBag()->add('attachment', 'attachment size maximal is 1 MB');
            throw new ValidationException($validator);
        }
    }

    public function checkDateRequest($startDate, $endDate, $idEmployee, $idCompany, $requestTypeCode) {
        $checkDateRequest = RequestHeader::checkDateRequest($startDate, $endDate, $idEmployee, null, $idCompany);
        $errDateRequest = '';

        if(count($checkDateRequest['start']) > 0){
            if($requestTypeCode=='Leave_Request'){
                $errDateRequest.= 'Tanggal mulai : '.collect($checkDateRequest['start'])->implode(', ')." telah digunakan untuk permintaan\n";
            } else {
                $errDateRequest.= 'Tanggal : '.collect($checkDateRequest['start'])->implode(', ')." telah dilakukan permintaan \n";
            }
        }
        if(count($checkDateRequest['end']) > 0){
            if($requestTypeCode=='Leave_Request'){
                $errDateRequest.= 'Tanggal akhir : '.collect($checkDateRequest['end'])->implode(', ')." telah digunakan untuk permintaan\n";
            } else {
                $errDateRequest.= 'Tanggal : '.collect($checkDateRequest['end'])->implode(', ')." telah dilakukan permintaan \n";
            }
        }
        if($errDateRequest != ''){
            //Utk pengecekan Req. Date yang pernah dipakai sebelumnya.
            throw new \Exception($errDateRequest, 422);
        }
    }

    public function listRequest(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);

            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;
            $today          = date('Y-m-d');
            $startDate      = $request->start_date ?? Carbon::parse($today)->subDays(14)->format('Y-m-d');
            $endDate        = $request->end_date ?? $today;
            $endDate        = Carbon::parse($endDate)->addDays(1)->format('Y-m-d'); //pengecekan laravel jika range sampai hari ini maka harus +1 day
            $requestType    = $request->request_type_code ?? null;
            $listRequest    = EmployeeRequest::listRequest($idCompany, $idUser, $startDate, $endDate, $requestType);
            if($listRequest->count() < 1){
                $listRequest = null;
            }

            $result = [
                'list'       => $listRequest,
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

    public function addRequest(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_company' => 'nullable'
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, false, true, $request->id_company ?? null);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            if($getEmployeeDetail[0]->secondary_position) {
                return $this->mobileSuccess('Data was successfully sent', null);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idLocation         = $employee->id_location;
            $approvalStatus     = null;
            $hierarchyApproval  = [
                'Leave_Request' => ['id_approval_hierarchy'=>null, 'approval_hierarchy_name'=>null],
                'Attendance_Correction' => ['id_approval_hierarchy'=>null, 'approval_hierarchy_name'=>null],
                'Change_Day_off' => ['id_approval_hierarchy'=>null, 'approval_hierarchy_name'=>null],
                'Cancel_Leave' => ['id_approval_hierarchy'=>null, 'approval_hierarchy_name'=>null]
            ];

            $getRequestType     = EmployeeRequest::getRequestType($idCompany);
            $getLeaveType       = EmployeeRequest::getLeaveType($idCompany, $idEmployee)->sortBy('leave_type_description')->values()->all();
            $getLeaveType = collect($getLeaveType)->map(function ($key, $val){
                unset($key->day_limit_submit_request);
                return $key;
            });
            $dayType            = EmployeeRequest::listDayType();

            $getHierarchyApprovalLeave = EmployeeRequest::getHierarchyApproval('Leave_Request', $idCompany, $idLocation);
            if($getHierarchyApprovalLeave->count() > 0){
                $hierarchyLeave['id_approval_hierarchy'] = $getHierarchyApprovalLeave[0]->id;
                $hierarchyLeave['approval_hierarchy_name'] = $getHierarchyApprovalLeave[0]->text;
                $hierarchyApproval['Leave_Request'] = $hierarchyLeave;
            }

            $getHierarchyApprovalAttendance = EmployeeRequest::getHierarchyApproval('Attendance_Correction', $idCompany, $idLocation);
            if($getHierarchyApprovalAttendance->count() > 0){
                $hierarchyAttendance['id_approval_hierarchy'] = $getHierarchyApprovalAttendance[0]->id;
                $hierarchyAttendance['approval_hierarchy_name'] = $getHierarchyApprovalAttendance[0]->text;
                $hierarchyApproval['Attendance_Correction'] = $hierarchyAttendance;
            }

            $getHierarchyApprovalCdo = EmployeeRequest::getHierarchyApproval('Change_Day_off', $idCompany, $idLocation);
            if($getHierarchyApprovalCdo->count() > 0){
                $hierarchyCdo['id_approval_hierarchy'] = $getHierarchyApprovalCdo[0]->id;
                $hierarchyCdo['approval_hierarchy_name'] = $getHierarchyApprovalCdo[0]->text;
                $hierarchyApproval['Change_Day_off'] = $hierarchyCdo;
            }

            foreach ($getRequestType as $k => $val) {
                $getRequestType[$k]->id_approval_hierarchy = $hierarchyApproval[$val->request_type_code]['id_approval_hierarchy'];
                $getRequestType[$k]->approval_hierarchy_name = $hierarchyApproval[$val->request_type_code]['approval_hierarchy_name'];
            }

            $result = [
                'request_type'          => $getRequestType,
                'leave_type'            => $getLeaveType,
                'day_type'              => $dayType,
                // 'hierarchy_approval'    => $hierarchyApproval,
                // 'approval_status'       => $approvalStatus,
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

    public function editRequest(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_request_header' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idLocation         = $employee->id_location;
            $idRequestHeader    = $employee->id_request_header;
            $approvalStatus     = null;
            $hierarchyApproval  = [
                'leave' => null,
                'attendance' => null,
                'change_day_off' => null,
            ];

            $getRequestType     = EmployeeRequest::getRequestType($idCompany);
            $getLeaveType       = EmployeeRequest::getLeaveType($idCompany, $idEmployee);
            $dayType            = [
                ['code'=>'Full_Day', 'type'=>'Full Day'], 
                ['code'=>'Half_Day1', 'type'=>'Half Day (Awal)'], 
                ['code'=>'Half_Day2', 'type'=>'Half Day (Akhir)']
            ];
            $getHierarchyApprovalLeave = EmployeeRequest::getHierarchyApproval('Leave_Request', $idCompany, $idLocation);
            if($getHierarchyApprovalLeave->count() > 0){
                $hierarchyLeave['id_approval'] = $getHierarchyApprovalLeave[0]->id;
                $hierarchyLeave['approval_name'] = $getHierarchyApprovalLeave[0]->text;
                $hierarchyApproval['leave'] = $hierarchyLeave;
            }

            $getHierarchyApprovalAttendance = EmployeeRequest::getHierarchyApproval('Attendance_Correction', $idCompany, $idLocation);
            if($getHierarchyApprovalAttendance->count() > 0){
                $hierarchyAttendance['id_approval'] = $getHierarchyApprovalAttendance[0]->id;
                $hierarchyAttendance['approval_name'] = $getHierarchyApprovalAttendance[0]->text;
                $hierarchyApproval['attendance'] = $hierarchyAttendance;
            }

            $getHierarchyApprovalCdo = EmployeeRequest::getHierarchyApproval('Change_Day_off', $idCompany, $idLocation);
            if($getHierarchyApprovalCdo->count() > 0){
                $hierarchyCdo['id_approval'] = $getHierarchyApprovalCdo[0]->id;
                $hierarchyCdo['approval_name'] = $getHierarchyApprovalCdo[0]->text;
                $hierarchyApproval['change_day_off'] = $hierarchyCdo;
            }

            $getMasterApprovalStatus = EmployeeRequest::getMasterApprovalStatus($idCompany, 'New');
            if($getMasterApprovalStatus->count() > 0){
                $approvalStatus['id_approval_status'] = $getMasterApprovalStatus[0]->id_general_data;
                $approvalStatus['name'] = $getMasterApprovalStatus[0]->description;
            }

            $getRequestHeader = RequestHeader::where('id_request_header',$idRequestHeader)->first();
            $getRequestDetail = RequestDetail::where('id_request_header',$idRequestHeader)->get();

            $result = [
                'request_type'          => $getRequestHeader->id_request_type,
                'leave_type'            => $getRequestHeader->id_leave_type,
                'day_type'              => @$getRequestDetail[0]->day_type,
                'hierarchy_approval'    => $hierarchyApproval,
                'approval_status'       => $approvalStatus,
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

    public function checkQuantityWorkdays(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'day_type_code' => 'required',
                'leave_type_code' => 'nullable',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $startDate          = $request->start_date;
            $endDate            = $request->end_date;
            $dayTypeCode        = $request->day_type_code;
            $leaveTypeCode      = $request->leave_type_code ?? null;

            $startDateTimestamp = Carbon::parse($startDate)->timestamp;
            $endDateTimestamp = Carbon::parse($endDate)->timestamp;
            if($endDateTimestamp < $startDateTimestamp){
                throw new \Exception('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 422);
            }
            
            if($leaveTypeCode){ //Jika user memilih leave
                $checkQuantityWorkdays = EmployeeRequest::checkQuantityWorkdays($idEmployee, $startDate, $endDate, $dayTypeCode, $leaveTypeCode, $idCompany);
            } 
            else { //Jika user memilih CDO
                $param = ['id_employee'=>$idEmployee, 'min_date'=>$startDate, 'max_date'=>$endDate];
                $getWorkdaysInOD = collect(RequestHeader::get_count_days_od($param));
                $count = 0;
                if($getWorkdaysInOD->count() > 0){
                    $count = array_sum($getWorkdaysInOD->pluck('qty_days')->all());
                }
                $checkQuantityWorkdays = $count;
            }

            $result = [
                'quantity'   => $checkQuantityWorkdays,
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

    public function checkActualTime(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'date' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $date               = $request->date;

            $actualTimeIn = null;
            $actualTimeOut = null;

            $getActualTime = EmployeeRequest::checkActualTime($idEmployee, $date);
            if($getActualTime){
                $actualTimeIn = $getActualTime->actual_time_in;
                $actualTimeOut = $getActualTime->actual_time_out;
            }

            $result = [
                'actual_time_in' => $actualTimeIn,
                'actual_time_out' => $actualTimeOut,
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

    public function getLeaveBalance(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
                        
            $getLeaveType       = EmployeeRequest::getLeaveType($idCompany, $idEmployee)->sortBy('leave_type_description')->values()->all();

            $result = [
                'leave' => $getLeaveType,
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

    public function getListApproval(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_request' => 'required|numeric',
                'request_type_code' => 'required',
                'id_company' => 'nullable'
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, false, true, $request->id_company ?? null);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idPositionDetail   = $employee->id_position_detail;
            $requestTypeCode    = $request->request_type_code;
            $idRequest          = $request->id_request;
            
            $getCustomTypeApprovalCareer = EmployeeApproval::getCustomTypeApprovalCareer($idCompany, $idPositionDetail)->pluck('code')->all();
            $getCustomTypeApprovalFpk = EmployeeApproval::getCustomTypeApprovalFpk($idCompany, $idPositionDetail)->pluck('code')->all();

            if(in_array($requestTypeCode, $getCustomTypeApprovalCareer)){
                $getCareer = CareerTransition::where('id_career_transaction',$idRequest)->first();
                if(!$getCareer){
                    throw new \Exception('Data permintaan karir tidak ditemukan', 404);
                }
                $getListApproval = EmployeeRequest::getListApprovalCareer($idRequest, $requestTypeCode);
            } 
            else if(in_array($requestTypeCode, $getCustomTypeApprovalFpk)){
                $getFpk = HiringRequest::where('id_hiring_request_header',$idRequest)->first();
                if(!$getFpk){
                    throw new \Exception('Data FPK tidak ditemukan', 404);
                }
                $idApproval = $getFpk->id_approval;
                $getListApproval = EmployeeRequest::getListApprovalRequestFpk($idCompany, $idEmployee, $idRequest, $idApproval, $requestTypeCode);
            } 
            else {
                $getRequestHeader = RequestHeader::where('id_request_header',$idRequest)->first();
                if(!$getRequestHeader){
                    throw new \Exception('Data permintaan karyawan tidak ditemukan', 404);
                }
                $idApproval = $getRequestHeader->id_approval;
                $getListApproval = EmployeeRequest::getListApprovalRequest($idCompany, $idEmployee, $idRequest, $idApproval, $requestTypeCode);
            }
            if($getListApproval->count() < 1){
                $getListApproval = null;
            }

            $result = [
                'approval' => $getListApproval,
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

    public function saveRequest(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required|string',
                'id_request_type' => 'required|numeric',
                'id_approval_hierarchy' => 'required|numeric',
                'note' => 'required|string',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idLocation         = $employee->id_location;
            $idRequestHeader    = $request->id_request ?? null;
            $idRequestType      = $request->id_request_type;
            $idLeaveType        = $request->id_leave_type ?? null;
            $idApproval         = $request->id_approval_hierarchy;
            $dayType            = $request->day_type ?? null;
            $note               = $request->note;
            $actualStart        = null;
            $actualEnd          = null;
            $requestCancel      = $request->request_cancel ?? null;
            $overtimeType       = $request->id_overtime_type ?? null;
            $delegateApproval   = $request->delegate_approval ?? false;
            $requestStatus      = 'A';
            $idEmployeeDelegate = null;
            $attachmentRequired = false;
            $attachmentType     = null;
            $thisAttachment     = null;
            $enableApproval     = true;
            $checkQuantityWorkdays = 0;

            if($requestCancel){
                $parameterValidation = [
                    'id_request_header' => 'required',
                ];
                $validator = Validator::make($request->all(), $parameterValidation);
                if($validator->fails()){ throw new ValidationException($validator); }
            }

            $getRequestType = EmployeeRequest::getRequestType($idCompany, $idRequestType);
            if($getRequestType->count() > 0){
                if($getRequestType[0]->request_type_code != 'Attendance_Correction'){
                    if($getRequestType[0]->request_type_code=='Leave_Request'){
                        $getMasterLeaveType = EmployeeRequest::getMasterLeaveType($idCompany, $idLeaveType);
                        $requiredAttachment = $getMasterLeaveType[0]->req_attachment;
                        self::validatorLeave($request);
                        $leaveBalance = EmployeeRequest::getLeaveBalance($idLeaveType, $idEmployee, $idCompany);
                        if(!$leaveBalance) {
                            throw new \Exception("Pastikan cuti anda dalam masa tanggal efektif", 422);
                        }

                        $startDate = $request->start_date;
                        $endDate = $request->end_date;

                        if($requiredAttachment == 1){
                            //jika leave type yg mengharuskan attachment
                            self::validatorAttachment($request);
                            $attachmentRequired = true;
                        }
                    }
                    elseif($getRequestType[0]->request_type_code=='Change_Day_off'){
                        self::validatorCDO($request);
                        $startDate = $request->start_date;
                        $endDate = $request->end_date;
                    }

                    $startDateTimestamp = Carbon::parse($startDate)->timestamp;
                    $endDateTimestamp = Carbon::parse($endDate)->timestamp;
                    if($endDateTimestamp < $startDateTimestamp){
                        throw new \Exception('Tanggal mulai tidak boleh lebih besar dari tanggal akhir', 422);
                    }

                    if($getRequestType[0]->request_type_code=='Leave_Request'){
                        $checkAvailableLeave = RequestHeader::checkAvailableLeave($getMasterLeaveType[0]->leave_code, $idEmployee, null, $idCompany);
                    }

                    if(!$requestCancel){
                        $checkDateRequest = RequestHeader::checkDateRequest($startDate, $endDate, $idEmployee, null, $idCompany, $idUser);
                        if(count($checkDateRequest) > 0){
                            //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
                            $errDateRequest = 'Tanggal: '.collect($checkDateRequest)->implode(', ')." telah diajukan permintaan";
                            throw new \Exception($errDateRequest, 422);
                        }
            
                        if($getRequestType[0]->request_type_code=='Leave_Request'){
                            $checkQuantityWorkdays = EmployeeRequest::checkQuantityWorkdays($idEmployee, $startDate, $endDate, $dayType, $getMasterLeaveType[0]->leave_code, $idCompany);
                            if($checkQuantityWorkdays > $checkAvailableLeave){
                                throw new \Exception('Total permintaan melebihi sisa kuota (termasuk permintaan yang masih dalam proses persetujuan jika ada). Sisa kuota : '.$checkAvailableLeave, 422);
                            }
                            if($checkQuantityWorkdays <= 0){
                                throw new \Exception('Tanggal permintaan Leave harus hari kerja', 422);
                            }
                        } else {
                            $paramToCount = ['id_employee'=>$idEmployee, 'min_date'=>$startDate, 'max_date'=>$endDate];
                            $getWorkdaysInOD = collect(RequestHeader::get_count_days_od($paramToCount));
                            $countOD = 0;
                            if($getWorkdaysInOD->count() > 0){
                                $countOD = array_sum($getWorkdaysInOD->pluck('qty_days')->all());
                            }
                            $checkQuantityWorkdays = $countOD;
                            if($checkQuantityWorkdays <= 0){
                                throw new \Exception('Tanggal permintaan Change Day Off harus hari libur kerja dan sudah melakukan rekam kehadiran dengan jam masuk dan jam keluar yang tercatat', 422);
                            }
                        }
                        
                        $checkBackdateRequest = EmployeeRequest::checkBackdateRequest($getRequestType[0]->request_type_code, $startDate, $idLeaveType);
                        if($checkBackdateRequest['status'] == false){
                            $messageBackdate = self::messageBackdate($getRequestType[0]->request_type_code, $checkBackdateRequest['limit']);
                            throw new \Exception($messageBackdate, 422);
                        }
                    }
                } else {
                    $startDate = $request->start_date ?? null;
                    $endDate = $request->end_date ?? null;
                    if(!$startDate && !$endDate){
                        $parameterValidation['start_date'] = 'required|before_or_equal:'.date('Y-m-d');
                        $parameterValidation['end_date'] = 'required|before_or_equal:'.date('Y-m-d');
                    }

                    if($startDate){ 
                        $parameterValidation['start_date'] = 'date_format:Y-m-d H:i:s|before_or_equal:'.date('Y-m-d'); 
                        $dateForCheck = $startDate;
                    }
                    if($endDate){ 
                        $parameterValidation['end_date'] = 'date_format:Y-m-d H:i:s|before_or_equal:'.date('Y-m-d'); 
                        $dateForCheck = $endDate;
                    }
                    self::validatorAttendance($request, $parameterValidation);

                    if($startDate){ $dateForCheck = $startDate; }
                    if($endDate){ $dateForCheck = $endDate; }
                    if($startDate && $endDate){
                        $startDate_ = Carbon::parse($startDate);
                        $endDate_ = Carbon::parse($endDate);

                        $startDateTimestamp = $startDate_->timestamp;
                        $endDateTimestamp = $endDate_->timestamp;
                        if($endDateTimestamp < $startDateTimestamp){
                            throw new \Exception('Tanggal dan waktu mulai tidak boleh lebih besar dari tanggal akhir', 422);
                        }
                        $diffInSecond = $endDate_->diffInSeconds($startDate_); 
                        if($diffInSecond > 86400){ //max : 86400 second (24 jam)
                            throw new \Exception('Tanggal mulai sampai tanggal akhir tidak boleh melebihi 24 jam', 422);
                        }
                    }
                    $checkBackdateRequest = EmployeeRequest::checkBackdateRequest($getRequestType[0]->request_type_code, $dateForCheck, $idLeaveType);
                    if($checkBackdateRequest['status'] == false){
                        $messageBackdate = self::messageBackdate($getRequestType[0]->request_type_code, $checkBackdateRequest['limit']);
                        throw new \Exception($messageBackdate, 422);
                    }

                    $getActualTime = EmployeeRequest::checkActualTime($idEmployee, $dateForCheck);
                    if($getActualTime){
                        $actualStart = $getActualTime->actual_time_in;
                        $actualEnd = $getActualTime->actual_time_out;
                    }
                }
                self::checkApprovalHierarchy($getRequestType[0]->request_type_code, $idCompany, $idLocation);
            }

            if($request->attachment){
                $pathFile = 'public/upload/employee_request/';
                $base64Attachment = str_replace(' ', '+', str_replace('\/', '/', $request->attachment));
                $base64Decode = base64_decode($base64Attachment);
                $attachmentMimeType = finfo_buffer(finfo_open(), $base64Decode, FILEINFO_MIME_TYPE);
                $attachmentExtension = explode('/', $attachmentMimeType)[1];
                $thisAttachment = $nik.'-'.date('YmdHis').'-'.Str::random(4).'.'.$attachmentExtension;
                $saveTo = $pathFile.$thisAttachment;

                $makeDir = Storage::makeDirectory($pathFile,0777, true, true); //make dir if not available
                if(in_array($attachmentExtension, ['png','jpeg','jpg','gif','bmp','ico','tiff','tif','svg','svgz'])){
                    self::saveImage($base64Decode, $saveTo, 600);
                } else {
                    self::saveDocument($base64Decode, $pathFile, $thisAttachment);
                }
            }

            $getReffNumber = RequestHeader::getkode($idCompany);

            $getMasterApprovalStatus = EmployeeRequest::getMasterApprovalStatus($idCompany, 'New');
            if($getMasterApprovalStatus->count() > 0){
                $idApprovalStatus   = $getMasterApprovalStatus[0]->id_general_data;
            } else {
                throw new \Exception('Gagal mendapatkan id_approval_status', 500);
            }

            $formRequest = [
                'reference_number' => $getReffNumber,
                'id_employee_request' => $idEmployee,
                'start_date' => (!$startDate && $endDate) ? $endDate : $startDate,
                'end_date' => ($startDate && !$endDate) ? $startDate : $endDate,
                'id_request_type' => $idRequestType,
                'id_leave_type' => $idLeaveType,
                'id_overtime_type' => $overtimeType,
                'attachment_type' => $attachmentType,
                'attachment' => $thisAttachment,
                'delegate_approval' => $delegateApproval,
                'note' => $note,
                'enable_approval' => $enableApproval,
                'id_approval' => $idApproval,
                'id_approval_status' => $idApprovalStatus,
                'status' => $requestStatus,
                'id_company' => $idCompany,
                'created_by' => $idUser,
                'updated_by' => $idUser, // untuk menandai request dari mobile
            ];

            if($requestCancel){
                $getRequestHeader = RequestHeader::where('id_request_header',$idRequestHeader)->first();
                $getRequestTypeCancel = EmployeeRequest::getRequestType($idCompany, null, 'Cancel Leave');
                $getMasterApprovalStatusForCancel = EmployeeRequest::getMasterApprovalStatus($idCompany, 'New');
                $formRequest['id_request_type'] = $getRequestTypeCancel[0]->id;
                $formRequest['id_approval_status'] = $getMasterApprovalStatusForCancel[0]->id_general_data;
                $formRequest['reference_number_cancel'] = $getRequestHeader->reference_number;
                $formRequest['enable_cancel'] = 1;
            }
            $createRequest = RequestHeader::create($formRequest);

            $formRequestDetail = [
                'id_request_header' => $createRequest->id_request_header,
                'id_employee' => $idEmployee,
                'request_start_to' => $startDate,
                'request_end_to' => $endDate,
                'day_type' => $dayType,
                'qty_days' => $checkQuantityWorkdays,
                'actual_start_to' => $actualStart,
                'actual_end_to' => $actualEnd,
                'note' => $note,
                'id_employee_delegation' => $idEmployeeDelegate,
                'status' => $requestStatus,
                'id_company' => $idCompany,
                'created_by' => $idUser,
            ];
            $createRequestDetail = RequestDetail::create($formRequestDetail);

            if($idLeaveType && $delegateApproval){
                $formDelegation = [
                    'id_request_header' => $createRequest->id_request_header,
                    'id_request_detail' => $createRequestDetail->id_request_detail,
                    'id_source_employee_approval' => $idEmployee,
                    'id_dest_employee_approval' => $idEmployeeDelegate,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'id_company' => $idCompany,
                    'created_by' => $idUser,
                ];
                $createApprovalDelegation = ApprovalDelegation::create($formDelegation);
            }

            $getApproval = EmployeeRequest::getApproval($idCompany, $idApproval);
            if($getApproval[0]->hierarchy_type == "Organization"){
                $approvalHierarchy = ApprovalTransaction::getApprovalOrganizationHierarchy($idEmployee, $idCompany);
            }
            else if($getApproval[0]->hierarchy_type == "Combine"){
                $approvalHierarchy = EmployeeRequest::getApprovalCombineHierarchy($idEmployee, $idCompany, $idApproval);
            }
            else if($getApproval[0]->hierarchy_type == "Custom"){
                $approvalHierarchy = EmployeeRequest::getApprovalCustomHierarchy($idEmployee, $idCompany, $idApproval);
            }       

            foreach ($approvalHierarchy as $key => $value) {
                $source[$key]['id_source_transaction'] = $createRequest->id_request_header;
                $source[$key]['source_transaction_type'] = $getApproval[0]->code;
                $source[$key]['id_approval'] = $getApproval[0]->id_approval;
                $source[$key]['id_approval_detail'] = $value->id_approval_detail ?? null;
                $source[$key]['id_approval_status'] = $getApproval[0]->id_approval_status;
                $source[$key]['id_approval_mode'] = $value->id_approval_mode;
                $source[$key]['sequence'] = $value->sequence;
                $source[$key]['id_position_detail'] = $value->id_position_detail;
                $source[$key]['id_employee_approval'] = $value->id_employee_approval;
            }   
            foreach ($source as $key => $value) {           
                $formApprovalTransaction = [
                    'id_source_transaction' => $value['id_source_transaction'],             
                    'source_transaction_type' => $value['source_transaction_type'],
                    'id_approval' => $value['id_approval'],
                    'id_approval_detail' => $value['id_approval_detail'],
                    'sequence' => $value['sequence'],
                    'id_employee_approval' => $value['id_employee_approval'],
                    'id_approval_status' => $value['id_approval_status'],
                    'id_approval_mode' => $value['id_approval_mode'],
                    'id_position_detail' => $value['id_position_detail'],
                    'id_company' => $idCompany,
                    'created_by' => $idUser,
                 ];
                if($requestCancel){
                    $formApprovalTransaction['source_transaction_type'] = 'Cancel_Leave';
                }
                $createApprovalTransaction = ApprovalTransaction::create($formApprovalTransaction);
            }

            $submitFromFunction = true;
            if($submitFromFunction){
                $submitRequest = new Request();
                $submitRequest['nik'] = $nik;
                $submitRequest['id_request'] = $createRequest->id_request_header;
                $submitRequest['submit_from_function'] = $submitFromFunction;
                self::submitToApproval($submitRequest);
            }

            $result = [
                'id_request'   => $createRequest->id_request_header,
            ];

            $message    = 'Request was successfully saved';
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

    public function updateRequest(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required|string',
                'id_request_type' => 'required|numeric',
                'id_hierarchy_approval' => 'required|numeric',
                'id_approval_status' => 'required|numeric',
                'note' => 'required|string',
                'id_request_header' => 'required|numeric',
                'id_request_detail' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idRequestHeader    = $request->id_request ?? null;
            $idRequestDetail    = $request->id_request_detail;
            $idRequestType      = $request->id_request_type;
            $idLeaveType        = $request->id_leave_type ?? null;
            $idHierarchyApproval= $request->id_hierarchy_approval;
            $idApprovalStatus   = $request->id_approval_status;
            $dayType            = $request->day_type ?? null;
            $note               = $request->note;
            $actualStart        = null;
            $actualEnd          = null;
            $requestCancel      = $request->request_cancel ?? null;
            $overtimeType       = $request->id_overtime_type ?? null;
            $delegateApproval   = $request->delegate_approval ?? false;
            $requestStatus      = 'A';
            $idEmployeeDelegate = null;
            $attachmentRequired = false;
            $attachmentType     = null;
            $thisAttachment     = null;
            $enableApproval     = true;
            $checkQuantityWorkdays = 0;

            if($requestCancel){
                $parameterValidation = [
                    'id_request_header' => 'required',
                ];
                $validator = Validator::make($request->all(), $parameterValidation);
                if($validator->fails()){ throw new ValidationException($validator); }
            }

            $getRequestType = EmployeeRequest::getRequestType($idCompany, $idRequestType);
            if($getRequestType->count() > 0){
                $getMasterLeaveType = EmployeeRequest::getMasterLeaveType($idCompany, $idLeaveType);

                if($getRequestType[0]->request_type_code != 'Attendance_Correction'){
                    $requiredAttachment = $getMasterLeaveType[0]->req_attachment;
                    self::validatorLeave($request);

                    $startDate = $request->start_date;
                    $endDate = $request->end_date;

                    if($requiredAttachment == 1){
                        //jika leave type yg mengharuskan attachment
                        $checkReqHeader = RequestHeader::where('id_request_header', $idRequestHeader)->first();
                        if(!$checkReqHeader->attachment && (!$request->attachment || $request->attachment)){
                            self::validatorAttachment($request);
                        }
                        if($checkReqHeader->attachment && $request->attachment){
                            $pathFile = 'public/upload/employee_request/';
                            if (Storage::exists($pathFile.$checkReqHeader->attachment)) {
                                Storage::delete($pathFile.$checkReqHeader->attachment);
                            }
                        }
                        $attachmentRequired = true;
                    }

                    if($getRequestType[0]->request_type_code=='Leave_Request' || $getRequestType[0]->request_type_code=='Change_Day_off'){
                        $checkAvailableLeave = RequestHeader::checkAvailableLeave($getMasterLeaveType[0]->leave_code, $idEmployee, null, $idCompany);

                        if(!$requestCancel){
                            $checkDateRequest = RequestHeader::checkDateRequest($startDate, $endDate, $idEmployee, null, $idCompany, $idUser);
                            if(count($checkDateRequest) > 0){
                                //Utk pengecekan Tanggal Permintaan yang pernah diminta sebelumnya.
                                $errDateRequest = 'Tanggal: '.collect($checkDateRequest)->implode(', ')." telah diajukan permintaan";
                                throw new \Exception($errDateRequest, 422);
                            }
                        }
                    }
                } else {
                    $startDate = $request->start_date ?? null;
                    $endDate = $request->end_date ?? null;
                    if(!$startDate && !$endDate){
                        $parameterValidation['start_date'] = 'required';
                        $parameterValidation['end_date'] = 'required';
                    }

                    if($startDate){ $parameterValidation['start_date'] = 'date_format:Y-m-d H:i:s'; }
                    if($endDate){ $parameterValidation['end_date'] = 'date_format:Y-m-d H:i:s'; }
                    self::validatorAttendance($request, $parameterValidation);

                    $actualStart = $request->actual_time_in;
                    $actualEnd   = $request->actual_time_out;
                }
            }

            $getRequestHeader = RequestHeader::where('id_request_header',$idRequestHeader)->first();

            if($request->attachment){
                $pathFile = 'public/upload/employee_request/';
                $base64Attachment = str_replace(' ', '+', str_replace('\/', '/', $request->attachment));
                $attachmentMimeType = finfo_buffer(finfo_open(), base64_decode($base64Attachment), FILEINFO_MIME_TYPE);
                $attachmentExtension = explode('/', $attachmentMimeType)[1];
                $thisAttachment = $nik.'-'.date('YmdHis').'-'.Str::random(4).'.'.$attachmentExtension;
                $saveTo = $pathFile.$thisAttachment;
                self::saveImage($base64Attachment, $saveTo, 600);
            } else {
                if(!is_null($getRequestHeader->attachment)){
                    $thisAttachment = $getRequestHeader->attachment;
                }
            }

            $formRequest = [
                'id_employee_request' => $idEmployee,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'id_request_type' => $idRequestType,
                'id_leave_type' => $idLeaveType,
                'id_overtime_type' => $overtimeType,
                'attachment_type' => $attachmentType,
                'attachment' => $thisAttachment,
                'delegate_approval' => $delegateApproval,
                'note' => $note,
                'enable_approval' => $enableApproval,
                'id_approval' => $idHierarchyApproval,
                'id_approval_status' => $idApprovalStatus,
                'status' => $requestStatus,
                'id_company' => $idCompany,
                'updated_by' => $idUser,
            ];
            $updateRequest = RequestHeader::where('id_request_header', $idRequestHeader)->update($formRequest);

            $formRequestDetail = [
                'id_request_header' => $createRequest->id_request_header,
                'id_employee' => $idEmployee,
                'request_start_to' => $startDate,
                'request_end_to' => $endDate,
                'day_type' => $dayType,
                'qty_days' => $checkQuantityWorkdays,
                'actual_start_to' => $actualStart,
                'actual_end_to' => $actualEnd,
                'note' => $note,
                'id_employee_delegation' => $idEmployeeDelegate,
                'status' => $requestStatus,
                'id_company' => $idCompany,
                'created_by' => $idUser,
            ];
            $updateRequestDetail = RequestDetail::where('id_request_detail', $idRequestDetail)->update($formRequestDetail);

            if($idLeaveType && $delegateApproval){
                ApprovalDelegation::where('id_request_header', $idRequestHeader)->delete();
                $formDelegation = [
                    'id_request_header' => $idRequestHeader,
                    'id_request_detail' => $idRequestDetail,
                    'id_source_employee_approval' => $idEmployee,
                    'id_dest_employee_approval' => $idEmployeeDelegate,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'id_company' => $idCompany,
                    'created_by' => $idUser,
                ];
                $createApprovalDelegation = ApprovalDelegation::create($formDelegation);
            }

            $getApproval = EmployeeRequest::getApproval($idCompany, $idHierarchyApproval);
            if($getApproval[0]->hierarchy_type == "Organization"){
                $approvalHierarchy = ApprovalTransaction::getApprovalOrganizationHierarchy($idEmployee, $idCompany);
            }
            else if($getApproval[0]->hierarchy_type == "Combine"){
                $approvalHierarchy = EmployeeRequest::getApprovalCombineHierarchy($idEmployee, $idCompany, $idHierarchyApproval);
            }
            else if($getApproval[0]->hierarchy_type == "Custom"){
                $approvalHierarchy = EmployeeRequest::getApprovalCustomHierarchy($idEmployee, $idCompany, $idHierarchyApproval);
            }       

            if($approvalHierarchy->count() > 0){
                $delApprovalTrans = ApprovalTransaction::where('id_source_transaction', $idRequestHeader)->delete();
                foreach ($approvalHierarchy as $key => $value) {
                    $source[$key]['id_source_transaction'] = $createRequest->id_request_header;
                    $source[$key]['source_transaction_type'] = $getApproval[0]->code;
                    $source[$key]['id_approval'] = $getApproval[0]->id_approval;
                    $source[$key]['id_approval_detail'] = $value->id_approval_detail ?? null;
                    $source[$key]['id_approval_status'] = $getApproval[0]->id_approval_status;
                    $source[$key]['id_approval_mode'] = $value->id_approval_mode;
                    $source[$key]['sequence'] = $value->sequence;
                    $source[$key]['id_position_detail'] = $value->id_position_detail;
                    $source[$key]['id_employee_approval'] = $value->id_employee_approval;
                }   
                foreach ($source as $key => $value) {           
                    $formApprovalTransaction = [
                        'id_source_transaction' => $value['id_source_transaction'],             
                        'source_transaction_type' => $value['source_transaction_type'],
                        'id_approval' => $value['id_approval'],
                        'id_approval_detail' => $value['id_approval_detail'],
                        'sequence' => $value['sequence'],
                        'id_employee_approval' => $value['id_employee_approval'],
                        'id_approval_status' => $value['id_approval_status'],
                        'id_approval_mode' => $value['id_approval_mode'],
                        'id_position_detail' => $value['id_position_detail'],
                        'id_company' => $idCompany,
                        'created_by' => $idUser,
                     ];
                    if($requestCancel){
                        $formApprovalTransaction['source_transaction_type'] = 'Cancel_Leave';
                    }
                    $createApprovalTransaction = ApprovalTransaction::create($formApprovalTransaction);
                }
            }

            $result = [
                'id_request'   => $createRequest->id_request_header,
            ];

            $message    = 'Request was successfully saved';
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

    public function submitToApproval(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            if(!@$request->submit_from_function){
                $this->authMobile($request); //required
            }
            $parameterValidation = [
                'nik' => 'required',
                'id_request' => 'required|numeric',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }

            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idRequestHeader    = $request->id_request ?? null;
            
            $getRequestHeader = RequestHeader::where('id_request_header',$idRequestHeader)->first();
            if(!$getRequestHeader){
                throw new \Exception('Data permintaan tidak ditemukan', 404);
            }

            if($getRequestHeader->id_leave_type){
                $getMasterLeaveType = EmployeeRequest::getMasterLeaveType($idCompany, $getRequestHeader->id_leave_type);

                $kodeCutiHamilDanGugur = ['MAT', 'MIS'];
                if(in_array($getMasterLeaveType[0]->leave_code, $kodeCutiHamilDanGugur)){
                    $getRequestDetail = RequestDetail::where('id_request_header', $idRequestHeader)->first();
                    $idCompanyToPatch = [$getRequestDetail->id_company];
                    $idEmployeeToPatch = $getRequestDetail->id_employee;
                    $startDatePatch = Carbon::parse($getRequestDetail->request_start_to)->format('Y-m-d');
                    $endDatePatch = Carbon::parse($getRequestDetail->request_end_to)->format('Y-m-d');

                    $patch = WorkDays::patchWorkdays($idCompanyToPatch, $startDatePatch, $endDatePatch, $idEmployeeToPatch);
                }
            }

            $getIdRequestApproval = EmployeeRequest::getGeneralData($idCompany, null, 'Request_Approval');
            $updateRequestAproval = RequestHeader::where('id_request_header', $idRequestHeader)->update([
                'id_approval_status' => $getIdRequestApproval[0]->id_request_type,
            ]);
            
            $getApprovalStatus = EmployeeRequest::getApprovalStatus($idCompany, $idRequestHeader, $getRequestHeader->id_approval);
            foreach ($getApprovalStatus as $k => $val) {
                $codeToUpdate = ['New', 'Cancel', 'Revised'];

                if(in_array($val->code, $codeToUpdate)){     
                     ApprovalTransaction::where('id_source_transaction', $idRequestHeader)
                        ->where('source_transaction_type', $val->source_transaction_type)
                        ->update([
                            'id_approval_status' => $getIdRequestApproval[0]->id_request_type,
                        ]); 
                }   
            }

            $enableCancel = RequestHeader::where('reference_number', $getRequestHeader->reference_number_cancel)->update(['enable_cancel' => 1]);

            $getListApprovalToMail = EmployeeRequest::getListApprovalToMail($idCompany, $idRequestHeader, $getRequestHeader->id_approval);

            $transactionToMail = [];
            foreach ($getListApprovalToMail as $k => $val) {
                $dataMail['id_employee_approval'] = $val->id_employee_approval;
                $dataMail['name'] = $val->name;
                $dataMail['private_mail'] = $val->private_mail;
                $dataMail['sequence'] = $val->sequence;
                $dataMail['new_sequence'] = $val->new_seq;
                $transactionToMail[] = $dataMail;  
            }

            $getEmployeeRequest = Employee::getEmployeeDetail(null, $getListApprovalToMail[0]->id_employee_request);
            $getRequestType = EmployeeRequest::getRequestType($idCompany, $getListApprovalToMail[0]->id_request_type);

            $req['reference_number'] = $getListApprovalToMail[0]->reference_number;
            $req['note'] = $getListApprovalToMail[0]->note;
            $req['name'] = $getEmployeeRequest[0]->name;
            $req['type'] = $getRequestType[0]->request_type_description;
            $req['code'] = $getRequestType[0]->request_type_code;
            $req['creation_date'] = Carbon::parse($getListApprovalToMail[0]->creation_date_request)->format('d M Y');

            $exceptRequestCode = ['Attendance_Correction', 'Change_Day_off'];
            if(!in_array($getRequestType[0]->request_type_code, $exceptRequestCode)){
                $getMasterLeaveType = EmployeeRequest::getMasterLeaveType($idCompany, $getListApprovalToMail[0]->id_leave_type);
                $req['leave_type'] = $getMasterLeaveType[0]->description;
            }

            $reqDetail = [];
            if($getRequestType[0]->request_type_code == 'Attendance_Correction'){
                $reqDetail['request_start_to'] = '-';
                $reqDetail['request_end_to'] = '-';

                if($getListApprovalToMail[0]->request_start_to != null){
                    $reqDetail['request_start_to'] = Carbon::parse($getListApprovalToMail[0]->request_start_to)->format('d M Y H:i');
                }
                if($getListApprovalToMail[0]->request_end_to != null){
                    $reqDetail['request_end_to'] = Carbon::parse($getListApprovalToMail[0]->request_end_to)->format('d M Y H:i');
                }
            }
            else{
                $reqDetail['request_start_to'] = Carbon::parse($getListApprovalToMail[0]->request_start_to)->format('d M Y');
                $reqDetail['request_end_to'] = Carbon::parse($getListApprovalToMail[0]->request_end_to)->format('d M Y');
            }
            $reqDetail['name'] = $getEmployeeRequest[0]->name;     
            $reqDetail['qty_days'] = $getListApprovalToMail[0]->qty_days;
            $reqDetail['day_type'] = $getListApprovalToMail[0]->day_type;

            $dataToEmail['req'] = $req;
            $dataToEmail['reqdetail'] = $reqDetail;
            $dataToEmail['transaction'] = $transactionToMail;

            $result = [
                'id_request' => $idRequestHeader,
            ];

            $message    = 'Data was successfully submited';
            DB::commit();

            $emailRequest = new Request();
            $emailRequest->source = (array)$dataToEmail;
            $this->EmailController->employeeRequestMobile($emailRequest);

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

    public function revisedAndRejectedCount(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }
            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;

            $revisedCount  = EmployeeRequest::getRevisedRequest($idCompany, $idUser);
            $rejectedCount  = EmployeeRequest::getRejectedRequest($idCompany, $idUser);

            $result = [
                'total' => $revisedCount->count() + $rejectedCount->count(),
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

    public function getRequestCount(Request $request)
    {   
        $request->validate([
            'nik' => 'required'
        ]);
        try {
            $this->authMobile($request);
            $employee = DB::table('hr_employee')->where('nik_employee', $request->nik)->first();
            if(!$employee) {
                throw new \Exception("Data NIK ".$request->nik." Tidak Ditemukan!");
            }
            $id_employee = $employee->id_employee;
            if(!$id_employee){
                throw new \Exception("Data ID Employee Tidak Ditemukan!");
            }
            
            $requests  = EmployeeRequest::getRequests($id_employee, $employee->id_company);
            
            return $this->sendSuccess("Data was sent successfully", $requests, false);
        } catch(Exception $e) {
            return $this->mobileError($e);
        }
    }

    public function getEmployeeRequestCountByCategory(Request $request)
    {
        $request->validate([
            'nik' => 'required',
            'id_company' => 'nullable'
        ]);
        try {
            $this->authMobile($request);
            $employee = DB::table('hr_employee')->where('nik_employee', $request->nik)->first();
            if(!$employee) {
                throw new \Exception("Data NIK ".$request->nik." Tidak Ditemukan!");
            }
            $id_employee = $employee->id_employee;
            if(!$id_employee){
                throw new \Exception("Data ID Employee Tidak Ditemukan!");
            }
            $requests  = EmployeeRequest::getRequestsByCategory($id_employee, $request->id_company ?? $employee->id_company);
            
            return $this->sendSuccess("Data was sent successfully", $requests, false);
        } catch(Exception $e) {
            return $this->mobileError($e);
        }
    }
}