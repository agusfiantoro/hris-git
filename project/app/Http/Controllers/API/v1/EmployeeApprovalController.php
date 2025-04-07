<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\API\BaseController;
use App\Http\Controllers\EmailController;
use App\Models\Employee\EmployeeRequest\RequestHeader;
use App\Models\Employee\EmployeeRequest\RequestDetail;
use App\Models\Employee\EmployeeRequest\ApprovalDelegation;
use App\Models\Employee\EmployeeRequest\ApprovalTransaction;
use App\Models\Employee\EmployeeSetting\WorkDays;
use App\Models\Recruitment\HiringRequest\HiringRequest;
use App\Models\CashAdvance\OfficialTravel\HrOfficialTravel;
use App\Models\API\Employee;
use App\Models\API\EmployeeApproval;
use App\Models\API\EmployeeRequest;
use App\Models\API\OasysIntegrationHeader;
use App\Models\API\OasysIntegrationDetail;
use App\Models\CareerAdministration\CareerTransition\CareerTransition;
use App\Models\CashAdvance\OfficialTravel\HrCashAdvance;
use App\Models\Curl;
use App\Models\Employee\EmployeeReco\EmployeeReco;
use App\Models\Integration\Bgen\Bgen;
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


class EmployeeApprovalController extends BaseController
{
    private $actionableSources = ['Leave_Request', 'Attendance_Correction', 'Overtime_Request', 'Cancel_Leave', 'Change_Day_off', 'FPK_Request', 'Official_Travel', 'Form_Reco', 'Expense_Request', 'Career_Request', 'Announcement_Request', 'Sales_Code', 'Biz_Approval'];
    
    public function __construct()
    {
        $this->EmailController = new EmailController;
    }

    public function validatorLeave(Request $request, $parameter=null) {
        //required end date utk selain attendance correction
        $parameter['id_leave_type'] = 'required|numeric';
        $parameter['day_type'] = 'required|string';
        $parameter['start_date'] = 'required|date_format:Y-m-d';
        $parameter['end_date'] = 'required|date_format:Y-m-d';

        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    public function validatorAttendance(Request $request, $parameter=null) {
        //required end date utk selain attendance correction
        $parameter['id_leave_type'] = 'nullable';
        $parameter['day_type'] = 'nullable';
        $parameter['actual_time_in'] = 'nullable';
        $parameter['actual_time_out'] = 'nullable';

        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }
    }

    public function validatorAttachment(Request $request, $parameter=null) {
        $parameter['attachment'] = 'required';
        $validator = Validator::make($request->all(), $parameter);
        if($validator->fails()){ throw new ValidationException($validator); }

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

    public function listApproval(Request $request) {
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
            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;
            $idApprovalTrans= $request->id_approval_transaction ?? null;
            $requestType    = $request->request_type_code ?? null;

            $listApproval   = EmployeeApproval::listApproval($idCompany, $idEmployee, $idUser, $idApprovalTrans, $requestType);
            if($listApproval->count() < 1){
                $listApproval = null;
            }
            
            $result = [
                'list'       => $listApproval,
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

    public function listApprovalHistory(Request $request) {
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
            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;
            $today          = date('Y-m-d');
            $startDate      = $request->start_date ?? Carbon::parse($today)->subDays(14)->format('Y-m-d');
            $endDate        = $request->end_date ?? $today;
            $requestType    = $request->request_type_code ?? null;

            $listApprovalHistory    = EmployeeApproval::listApprovalHistory($idCompany, $idEmployee, $idUser, $startDate, $endDate, $requestType);
            if($listApprovalHistory->count() < 1){
                $listApprovalHistory = null;
            }
            
            $result = [
                'list'      => $listApprovalHistory,
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

    public function formData(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_company' => 'nullable',
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
            $idLocation         = $employee->id_location;
            $idPositionDetail   = $employee->id_position_detail;

            $selected = ['Career_Request'];
            $except = ['Cancel_Leave'];

            $getApprovalDocType = EmployeeApproval::getApprovalDocType($idCompany, null, $selected);
            $getCustomTypeApprovalCareer = EmployeeApproval::getCustomTypeApprovalCareer($idCompany);
            $getCustomTypeApprovalFpk = EmployeeApproval::getCustomTypeApprovalFpk($idCompany);
            $getCustomTypeApprovalTravel = EmployeeApproval::getCustomTypeApprovalTravel($idCompany);
            $allIdDocType       = $getApprovalDocType->pluck('id_general_data')->all();

            $getApprovalHierarchy = EmployeeApproval::getApprovalHierarchy(null, null, null, $allIdDocType, $idPositionDetail);
            $getRequestType     = EmployeeRequest::getRequestType($idCompany, null, null, $except);
            if($getApprovalHierarchy->count() > 0){
                $getCustomTypeApprovalCareer->mapWithKeys(function ($val){
                    $val->id_request_type = $val->id_general_data;
                    $val->request_type_description = $val->description;
                    $val->request_type_code = $val->code;

                    unset($val->id_general_data);
                    unset($val->description);
                    unset($val->code);
                    return $val;
                });
                $getRequestType = $getRequestType->merge($getCustomTypeApprovalCareer);
            }

            if($getCustomTypeApprovalFpk->count() > 0){
                $getCustomTypeApprovalFpk->mapWithKeys(function ($val){
                    $val->id_request_type = $val->id_general_data;
                    $val->request_type_description = str_replace('_', ' ', $val->code);
                    $val->request_type_code = $val->code;

                    unset($val->id_general_data);
                    unset($val->description);
                    unset($val->code);
                    return $val;
                });
                $getRequestType = $getRequestType->merge($getCustomTypeApprovalFpk);
            }

            if($getCustomTypeApprovalTravel->count() > 0){
                $getCustomTypeApprovalTravel->mapWithKeys(function ($val){
                    $val->id_request_type = $val->id_general_data;
                    $val->request_type_description = str_replace('_', ' ', $val->code);
                    $val->request_type_code = $val->code;

                    unset($val->id_general_data);
                    unset($val->description);
                    unset($val->code);
                    return $val;
                });
                $getRequestType = $getRequestType->merge($getCustomTypeApprovalTravel);
            }
            
            $result = [
                'request_type' => $getRequestType
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

    public function approvalCount(Request $request) {
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
            $employee       = $getEmployeeDetail[0];
            $idEmployee     = $employee->id_employee;
            $idUser         = $employee->id_user;
            $idCompany      = $employee->id_company;
            // $today          = date('Y-m-d');
            // $startDate      = $request->start_date ?? Carbon::parse($today)->subDays(14)->format('Y-m-d');
            // $endDate        = $request->end_date ?? $today;
            // $countApproval  = 0;

            $approvalCount  = EmployeeApproval::listApproval($idCompany, $idEmployee, $idUser, null, null, true);
            $result = [
                'total' => $approvalCount,
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

    public function approve(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_approval_transaction' => 'required|numeric',
                // 'note' => 'required',
            ];
            $validator = Validator::make($request->all(), $parameterValidation);
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            $nik = $request->nik ?? null;
            $getEmployeeDetail = Employee::getEmployeeDetail($nik, null, false, true, $request->id_company);
            if(count($getEmployeeDetail) < 1){
                throw new \Exception('Data NIK '.$nik.' tidak ditemukan', 404);
            }

            $employee           = $getEmployeeDetail[0];
            $idEmployee         = $employee->id_employee;
            $idUser             = $employee->id_user;
            $idCompany          = $employee->id_company;
            $idApprovalTrans    = $request->id_approval_transaction ?? null;
            $note               = $request->note ?? null;

            $findTransaction = DB::table('hr_approval_transaction')->where('id_approval_transaction', $idApprovalTrans)->first();
            if(!$findTransaction){
                throw new \Exception('Data transaksi approval tidak ditemukan', 404);
            }

            if($idEmployee != $findTransaction->id_employee_approval){
                $info = 'Anda tidak termasuk yang berhak melakukan approval pada data tersebut';
                $dataTrans = ' Data(id_source_transaction:'.$idApprovalTrans.', id_employee:'.$findTransaction->id_employee_approval.', id_company:'.$findTransaction->id_company.').';
                $dataUser = ' User(id_employee:'.$idEmployee.', id_company:'.$idCompany.').';
                \Log::channel('mobile')->info('Cannot Approval. '.$dataTrans.$dataUser);
                throw new \Exception($info, 422);
            }

            $idSourceTransaction = $findTransaction->id_source_transaction;
            $sourceTransactionType = $findTransaction->source_transaction_type;
            $sequence = $findTransaction->sequence;
            $idApprovalStatus = $findTransaction->id_approval_status;

            $statusFinal = ['Revised', 'Rejected', 'Cancel', 'Approved'];

            if(!in_array($sourceTransactionType, $this->actionableSources)) {
                throw new Exception("Jenis approval ini belum dapat dilakukan.", 400);
            }

            if($sourceTransactionType == 'FPK_Request'){
                $checkRequest = DB::table('hr_hiring_request_header as hhrh')
                    ->leftJoin('master_general_data as mgd', 'hhrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hhrh.id_hiring_request_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Official_Travel'){
                $checkRequest = DB::table('hr_official_travel as hot')
                    ->leftJoin('master_general_data as mgd', 'hot.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hot.id_official_travel', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Career_Request') {
                $checkRequest = DB::table('hr_career_transaction as hct')
                    ->leftJoin('master_general_data as mgd', 'hct.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hct.id_career_transaction', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Form_Reco') {
                $checkRequest = DB::table('hr_recommendation_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hrh.id_recommendation_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Expense_Request') {
                $checkRequest = DB::table('hr_cash_advance as hca')
                    ->leftJoin('master_general_data as mgd', 'hca.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hca.id_cash_advance', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Biz_Approval') {
                $checkRequest = DB::table('integration.biz_approval_integration_request_header as bairh')
                    ->leftJoin('master_general_data as mgd', 'bairh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('bairh.id_integration_request_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Sales_Code') {
                    $checkRequest = DB::table('integration.bgen_integration_sales_code as bisc')
                        ->leftJoin('master_general_data as mgd', 'bisc.id_approval_status', '=', 'mgd.id_general_data')
                        ->select('mgd.code as status_approval')
                        ->where('bisc.id_integration_sales_code', $findTransaction->id_source_transaction)->first();
            } else {
                $checkRequest = DB::table('hr_request_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval', 'hrh.executed')
                    ->where('hrh.id_request_header', $findTransaction->id_source_transaction)->first();
            }

            if(in_array($checkRequest->status_approval, $statusFinal)){
                $statusMessage = [
                    'Revised' => 'Revisi', 'Rejected' => 'Telah ditolak', 'Cancel' => 'Telah dibatalkan', 'Approved' => 'Telah disetujui', 
                ];
                throw new \Exception('Anda tidak bisa memproses data. Status: '.$statusMessage[$checkRequest->status_approval], 422);
            }

            $employeeRequest = ['Leave_Request', 'Attendance_Correction', 'Overtime_Request', 'Cancel_Leave', 'Change_Day_off', 'FPK_Request', 'Official_Travel', 'Form_Reco', 'Expense_Request'];
            $careerRequest = ['Career_Request'];
            $announcementRequest = ['Announcement_Request'];
            $salesCodeRequest = ['Sales_Code'];
            $oasysRequest = ['Biz_Approval'];

            $sendEmail = null;
            $getIdCompanyApproval = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->where('sequence', $sequence)->get();
            $getIdApproved = EmployeeRequest::getGeneralData(@$getIdCompanyApproval[0]->id_company, null, 'Approved');
            $getIdPartialApproved = EmployeeRequest::getGeneralData(@$getIdCompanyApproval[0]->id_company, null, 'Partial_Approved');

            if(in_array($sourceTransactionType, $employeeRequest)){
                $dataApprovalTransaction = ['update_date' => date('Y-m-d H:i:s'), 'updated_by' => $idUser];
                $getSequence = EmployeeApproval::getSequenceEmployeeApproval($idSourceTransaction, $sourceTransactionType);
                $getApprovalBySequence = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->where('sequence', $sequence)->get();
                DB::table('hr_approval_transaction')->where('id_approval_transaction', $idApprovalTrans)->update($dataApprovalTransaction);

                if($getApprovalBySequence->count() > 0){
                    foreach ($getApprovalBySequence as $k => $val) {
                        if(in_array($sourceTransactionType, $employeeRequest)){
                            $updateApproval = DB::table('hr_approval_transaction')->where('id_approval_transaction', $val->id_approval_transaction)->where('sequence', $sequence)->update(['id_approval_status' => $getIdApproved[0]->id_request_type]);
                        }
                    }
                    if($getSequence->count() == 1){ // Jika approval tinggal 1 terakhir (final)
                        if($getSequence[0]->sequence == $sequence){
                            $getMailEmployee = EmployeeApproval::getMailEmployeeFromEmployeeRequest($idSourceTransaction);
                            if($getMailEmployee->count() > 0){
                                if($getMailEmployee[0]->request_code == 'Attendance_Correction'){
                                    $reqStartDate = '-';
                                    $reqEndDate = '-';
                                    if($getMailEmployee[0]->request_start_to != null){
                                        $reqStartDate = Carbon::parse($getMailEmployee[0]->request_start_to)->format('d M Y H:i');
                                    }
                                    if($getMailEmployee[0]->request_end_to != null){
                                        $reqEndDate = Carbon::parse($getMailEmployee[0]->request_end_to)->format('d M Y H:i');
                                    }
                                    $getMailEmployee[0]->request_start_to = $reqStartDate;
                                    $getMailEmployee[0]->request_end_to = $reqEndDate;
                                }
                                else{
                                    $getMailEmployee[0]->request_start_to = Carbon::parse($getMailEmployee[0]->request_start_to)->format('d M Y');
                                    $getMailEmployee[0]->request_end_to = Carbon::parse($getMailEmployee[0]->request_end_to)->format('d M Y');
                                }
                                $sendEmail = $getMailEmployee[0];
                            }

                            if($sourceTransactionType == 'FPK_Request'){
                                $finalRequest = HiringRequest::where('id_hiring_request_header', $idSourceTransaction)->first(); 
                                $maxSLA = HiringRequest::max_hiring($finalRequest->id_company, $finalRequest->id_position_routing_request, $finalRequest->id_branch);
                                $sendEmail = null;
                                $dataUpdateHiringRequest = [
                                    'id_approval_status' => $getIdApproved[0]->id_request_type,
                                    'approval_date' => date('Y-m-d'),
                                    'target_date' => date('Y-m-d', strtotime('+'.@$maxSLA[0]->max_hiring_days.' days', strtotime(date('Y-m-d'))))
                                ];
                                HiringRequest::where('id_hiring_request_header', $idSourceTransaction)->update($dataUpdateHiringRequest); 
                            
                            } else if($sourceTransactionType == 'Official_Travel'){
                                $sendEmail = null;
								$idCompany = HrOfficialTravel::where('id_official_travel', $idSourceTransaction)->first();								
								$mail_cc = HrOfficialTravel::mail_cc($idCompany->id_company);
								foreach($mail_cc as $key=>$val){
									$ccEmails[] = $val->mail_cc;
								}
								$mail_approve = EmployeeApproval::get_mail_approval_travel($idSourceTransaction);
								$mail_approval = $mail_approve;
								$mail_approval->request_start_to = Carbon::parse($mail_approve->request_start_to)->format('d M Y');
								$mail_approval->request_end_to = Carbon::parse($mail_approve->request_end_to)->format('d M Y');
								$mail_approval->cc_mail = $ccEmails;
								$sendEmail = $mail_approval;
                                HrOfficialTravel::where('id_official_travel', $idSourceTransaction)->update(['id_approval_status' => $getIdApproved[0]->id_request_type]);
                            } else if($sourceTransactionType == 'Form_Reco') {
                                EmployeeReco::where('id_recommendation_header', $idSourceTransaction)->update(array(
                                    'id_approval_status' => $getIdApproved[0]->id_request_type,			
                                ));
                            } else if($sourceTransactionType == 'Expense_Request') {
                                HrCashAdvance::where('id_cash_advance', $idSourceTransaction)->update(array(
                                    'id_approval_status' => $getIdApproved[0]->id_request_type,			
                                ));
                            } else {
                                RequestHeader::where('id_request_header', $idSourceTransaction)->update(['id_approval_status' => $getIdApproved[0]->id_request_type]);
                                EmployeeApproval::approveAll($getApprovalBySequence[0]->id_company, $idApprovalTrans);
                            }
                        }
                    }
                    else if($getSequence->count() > 1){ //Jika approval masih lebih dari 1
                        $sequenceInApproval = $getSequence->pluck('sequence')->all();
                        if(in_array($sequence, $sequenceInApproval)){
                            if($sourceTransactionType == 'FPK_Request'){
                                HiringRequest::where('id_hiring_request_header', $idSourceTransaction)->update(['id_approval_status' => $getIdPartialApproved[0]->id_request_type]);
                            } else if($sourceTransactionType == 'Official_Travel'){
                                HrOfficialTravel::where('id_official_travel', $idSourceTransaction)->update(['id_approval_status' => $getIdPartialApproved[0]->id_request_type]);
                            } else if($sourceTransactionType == 'Expense_Request'){
                                HrCashAdvance::where('id_cash_advance', $idSourceTransaction)->update(['id_approval_status' => $getIdPartialApproved[0]->id_request_type]);
                            } else if($sourceTransactionType == 'Career_Request') {
                                CareerTransition::where('id_career_transaction', $idSourceTransaction)->update(array(
                                    'id_approval_status' => $getIdPartialApproved[0]->id_request_type,			
                                ));
                            } else if($sourceTransactionType == 'Form_Reco') {
                                EmployeeReco::where('id_recommendation_header', $idSourceTransaction)->update(array(
                                    'id_approval_status' => $getIdPartialApproved[0]->id_request_type,			
                                ));
                            } else {
                                RequestHeader::where('id_request_header', $idSourceTransaction)->update(['id_approval_status' => $getIdPartialApproved[0]->id_request_type]);
                            }
                        }
                    }
                }
            } else if(in_array($sourceTransactionType, $careerRequest)) {
                $careerTransaction = CareerTransition::where('id_career_transaction', $findTransaction->id_source_transaction)->first();
                
                $data = [
                    'id_career_transaction' => $findTransaction->id_source_transaction, // 18583
                    'id_approval' => @$careerTransaction->id_approval
                ];
                $sql = "select  hat.*, he.name, mgd.description as code, he.id_user 
                        from    hr_approval_transaction hat
                        left join    hr_employee he
                        on      he.id_employee = hat.id_employee_approval
                        join    master_general_data mgd
                        on      hat.id_approval_status = mgd.id_general_data
                        where   hat.id_source_transaction = ?
                        and     hat.id_approval = coalesce(?,hat.id_approval)";
                $data_status = DB::select($sql,[$data['id_career_transaction'], $data['id_approval']]);
                if(count($data_status) > 0) {
                    $approve = CareerTransition::submit_approve_api($data_status[0]->id_company);
                    CareerTransition::where('id_career_transaction', $findTransaction->id_source_transaction)->update(array(
                        'id_approval_status' => $approve->id_general_data,
                    ));
                }
                foreach ($data_status as $key => $value) {
                    if($value->code == "New" || $value->code == "Cancel" || $value->code == "Request Approval"){
                        if(count($data_status) == 1 && $value->id_user == $idUser){
                            $approved = CareerTransition::approved_api($value->id_company);
                            ApprovalTransaction::where('id_source_transaction', $findTransaction->id_source_transaction)->where('source_transaction_type', 'Career_Request')->update(array(
                                'id_approval_status' => $approved->id_general_data,
                                'update_date' => date('Y-m-d H:i:s'),
                                'updated_by' => $idUser,
                            ));	
                            CareerTransition::where('id_career_transaction', $findTransaction->id_source_transaction)->update(array(
                                'id_approval_status' => $approved->id_general_data,			
                            ));
                        }
                        else{
                            ApprovalTransaction::where('id_source_transaction', $findTransaction->id_source_transaction)->where('source_transaction_type', 'Career_Request')->update(array(
                                'id_approval_status' => $approve->id_general_data,
                            ));	
                        }				
                    }
                }	
            } else if(in_array($sourceTransactionType, $salesCodeRequest)) {
                $bgen = new \App\Http\Controllers\Integration\Bgen\BgenController;
                $bgenData = Bgen::findOrFail($idSourceTransaction);
                $getSequence = EmployeeApproval::getSequenceEmployeeApproval($idSourceTransaction, $sourceTransactionType);
                ApprovalTransaction::where('id_source_transaction', $findTransaction->id_source_transaction)->whereIn('source_transaction_type', $salesCodeRequest)->update(array(
                    'id_approval_status' => $getIdApproved[0]->id_request_type,
                    'update_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $idUser,
                ));
                $bgenData->update([
                    'id_approval_status' => $getIdApproved[0]->id_request_type,
                    'update_date' => date('Y-m-d H:i:s'),
                    'updated_by' => $idUser,
                ]);
                $empSales = DB::table('hr_employee')->where('id_employee', $bgenData->id_employee)->first();
                if($bgenData->id_direct_chief) {
                    $salesSuperior = DB::table('hr_employee')->where('id_employee', $bgenData->id_direct_chief)->first();
                } else {
                    $salesSuperior = DB::selectOne("SELECT he.* FROM sp_funct_approval_organization_hierarchy_view (?,?,null,null) sfac
                                                    JOIN master_position_detail mpd ON sfac.id_detail_chief = mpd.id_position_detail
                                                    JOIN hr_employee he ON mpd.id_employee = he.id_employee", [$bgenData->id_employee, $bgenData->id_company]);
                }
                $sync = $bgen->syncToBgen($empSales->nik_employee);
                if($sync['success'] == true) {
                    $bgen->sendApprovalNotification($salesSuperior, $bgenData, true); // send notif to spv
                } else {
                    \Log::channel('bgen')->error("Mobile Sync after approval error [$empSales->nik_employee]: ".json_encode($sync));
                    $bgen->sendApprovalNotification($salesSuperior, $bgenData, false); // send notif hubungi hr karena sync bgen gagal
                }

                
            } else if(in_array($sourceTransactionType, $oasysRequest)) {
                $bgen = new \App\Http\Controllers\Integration\Bgen\BgenController;
                $header = OasysIntegrationHeader::findOrFail($idSourceTransaction);
                // $bgenData = Bgen::findOrFail($idSourceTransaction);
                $getSequence = EmployeeApproval::getSequenceEmployeeApproval($idSourceTransaction, $sourceTransactionType);
                ApprovalTransaction::where('id_source_transaction', $findTransaction->id_source_transaction)
                    ->where('source_transaction_type', $oasysRequest)
                    ->where('id_employee_approval', $idEmployee)
                    ->update(array(
                        'id_approval_status' => $getIdApproved[0]->id_request_type,
                        'update_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $idUser,
                ));
                $approvals = ApprovalTransaction::where('id_source_transaction', $findTransaction->id_source_transaction)->where('source_transaction_type', $oasysRequest)->get();

                if(count($approvals->where('id_approval_status', $getIdApproved[0]->id_request_type)) < count($approvals)) {
                    $header->update([
                        'id_approval_status' => $getIdPartialApproved[0]->id_request_type,
                        'update_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $idUser,
                    ]);
                } else {
                    $header->update([
                        'id_approval_status' => $getIdApproved[0]->id_request_type,
                        'update_date' => date('Y-m-d H:i:s'),
                        'updated_by' => $idUser,
                    ]);

                    $details = OasysIntegrationDetail::where('id_integration_request_header', $header->id_integration_request_header)->get();
                    foreach($details as $detail) {
                        $arrayPrincipal = explode(",", trim($detail->id_principal, " {}"));
                        foreach($arrayPrincipal as $principal) {
                            $existingData = Bgen::where('integration_type', 'Biz_Approval')
                                                ->where('id_employee', $header->id_employee)
                                                ->where('id_branch', (int)$detail->id_branch)
                                                ->where('id_principal', (int)$principal)
                                                ->where('id_company', $header->id_company)
                                                ->first();
                            if(!$existingData) {
                                $data = new Bgen;
                                $data->id_employee = $header->id_employee;
                                $data->id_branch = (int)$detail->id_branch;
                                $data->id_principal = (int)$principal;
                                $data->status = $header->status;
                                $data->id_company = $header->id_company;
                                $data->created_by = $idUser;	
                                $data->id_approval = $header->id_approval;
                                $data->id_approval_status = $header->id_approval_status;
                                $data->integration_type = 'Biz_Approval';
                                $data->save();
                            }
                        }
                    }
                    $employee = DB::table('hr_employee')->where('id_employee', $header->id_employee)->first();
                    $oasysController = new \App\Http\Controllers\Integration\Bgen\OasysController;
                    $haveSyncedBefore = Bgen::where('id_employee', $employee->id_employee)
                                            ->where('integration_type', 'Biz_Approval')
                                            ->where('is_synchronize_flag', true)
                                            ->where('id_company', $header->id_company)
                                            ->first();
                    if($haveSyncedBefore) {
                        $oasysController->syncUpdateToBgen($employee->nik_employee, true);
                    } else {
                        $oasysController->syncToBgen($employee->nik_employee, true);
                    }
                }
            }
            

            $result = [
                'id_approval_transaction' => $idApprovalTrans,
            ];

            $message    = 'Data was successfully approved';
            DB::commit();

            if($sendEmail){
                $emailRequest = new Request();
                $emailRequest->source = (array)$sendEmail;
			//	dd($emailRequest);
                $this->EmailController->employeeApprovalApproveMobile($emailRequest);
            }
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

    public function reject(Request $request) {
        ini_set('max_execution_time', -1);
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_approval_transaction' => 'required|numeric',
                'note' => 'required',
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
            $idApprovalTrans    = $request->id_approval_transaction ?? null;
            $note               = $request->note ?? null;

            $findTransaction = DB::table('hr_approval_transaction')->where('id_approval_transaction', $idApprovalTrans)->first();
            if(!$findTransaction){
                throw new \Exception('Data transaksi approval tidak ditemukan', 404);
            }

            $idSourceTransaction = $findTransaction->id_source_transaction;
            $sourceTransactionType = $findTransaction->source_transaction_type;
            $sequence = $findTransaction->sequence;

            if(!in_array($sourceTransactionType, $this->actionableSources)) {
                throw new Exception("Jenis approval ini belum dapat dilakukan.", 400);
            }

            if($idEmployee != $findTransaction->id_employee_approval){
                $info = 'Anda tidak termasuk yang berhak melakukan approval pada data tersebut';
                $dataTrans = ' Data(id_source_transaction:'.$idApprovalTrans.', id_employee:'.$findTransaction->id_employee_approval.', id_company:'.$findTransaction->id_company.').';
                $dataUser = ' User(id_employee:'.$idEmployee.', id_company:'.$idCompany.').';
                \Log::channel('mobile')->info('Cannot Approval. '.$dataTrans.$dataUser);
                throw new \Exception($info, 422);
            }

            $statusFinal = ['Revised', 'Rejected', 'Cancel', 'Approved'];
            if($sourceTransactionType == 'FPK_Request'){
                $checkRequest = DB::table('hr_hiring_request_header as hhrh')
                    ->leftJoin('master_general_data as mgd', 'hhrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hhrh.id_hiring_request_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Official_Travel'){
                $checkRequest = DB::table('hr_official_travel as hot')
                    ->leftJoin('master_general_data as mgd', 'hot.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hot.id_official_travel', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Expense_Request'){
                $checkRequest = DB::table('hr_cash_advance as hca')
                    ->leftJoin('master_general_data as mgd', 'hca.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hca.id_cash_advance', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Form_Reco'){
                $checkRequest = DB::table('hr_recommendation_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hrh.id_recommendation_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Biz_Approval'){
                $checkRequest = DB::table('integration.biz_approval_integration_request_header as bairh')
                    ->leftJoin('master_general_data as mgd', 'bairh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('bairh.id_integration_request_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Sales_Code'){
                $checkRequest = DB::table('integration.bgen_integration_sales_code as bisc')
                    ->leftJoin('master_general_data as mgd', 'bisc.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('bisc.id_integration_sales_code', $findTransaction->id_source_transaction)->first();
            } else {
                $checkRequest = DB::table('hr_request_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval', 'hrh.executed')
                    ->where('hrh.id_request_header', $findTransaction->id_source_transaction)->first();
            }

            if(in_array($checkRequest->status_approval, $statusFinal)){
                $statusMessage = [
                    'Revised' => 'Revisi', 'Rejected' => 'Telah ditolak', 'Cancel' => 'Telah dibatalkan', 'Approved' => 'Telah disetujui', 
                ];
                throw new \Exception('Anda tidak bisa memproses data. Status: '.$statusMessage[$checkRequest->status_approval], 422);
            }

            $getIdCompanyApproval = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->where('sequence', $sequence)->get();
            $getIdRejected = EmployeeRequest::getGeneralData(@$getIdCompanyApproval[0]->id_company, null, 'Rejected');

            $employeeRequest = ['Leave_Request', 'Attendance_Correction', 'Overtime_Request', 'Cancel_Leave', 'Change_Day_off', 'FPK_Request', 'Official_Travel'];
            $careerRequest = ['Career_Request'];
            $announcementRequest = ['Announcement_Request'];
            $dataSourceUpdate = [
                'id_approval_status' => $getIdRejected[0]->id_request_type,
                'note_rejected' => $note,
            ];
            $sendEmail = null;

            if(in_array($sourceTransactionType, $employeeRequest)){
                if($sourceTransactionType == 'FPK_Request'){
                    $dataUpdateHiringRequest = [
                        'id_approval_status' => $getIdRejected[0]->id_request_type,
                        'hiring_request_status' => 'C',
                        'note_rejected' => $note,
                    ];
                    HiringRequest::where('id_hiring_request_header', $idSourceTransaction)->update($dataUpdateHiringRequest);
                } else if($sourceTransactionType == 'Official_Travel'){
                    $dataUpdateHiringRequest = [
                        'id_approval_status' => $getIdRejected[0]->id_request_type,
                        'note_rejected' => $note,
                    ];
                    HrOfficialTravel::where('id_official_travel', $idSourceTransaction)->update($dataUpdateHiringRequest);
					$getMailEmployee = EmployeeApproval::get_mail_approval_travel($idSourceTransaction);
					$getMailEmployee->note_rejected = $note;
					$sendEmail = $getMailEmployee;                
                } else {
                    RequestHeader::where('id_request_header', $idSourceTransaction)->update($dataSourceUpdate);
                    $getMailEmployee = EmployeeApproval::getMailEmployeeFromEmployeeRequest($idSourceTransaction);
                    if($getMailEmployee->count() > 0){
                        $getMailEmployee[0]->note_rejected = $note;
                        $sendEmail = $getMailEmployee[0];
                    }
                }
            } else if(in_array($sourceTransactionType, ['Biz_Approval'])) {
                $header = OasysIntegrationHeader::findOrFail($idSourceTransaction);
				$header->id_approval_status = $getIdRejected[0]->id_request_type;
				$header->note_rejected = $note;
				$header->save();
            } else if(in_array($sourceTransactionType, ['Sales_Code'])) {
                $bgen = Bgen::findOrFail($idSourceTransaction);
                $bgen->id_approval_status = $getIdRejected[0]->id_request_type;
				$bgen->note_rejected = $note;
				$bgen->save();
            } else if(in_array($sourceTransactionType, ['Form_Reco'])) {
                $formReco = DB::table('hr_recommendation_header')->where('id_recommendation_header', $idSourceTransaction)->first();
                $formReco->id_approval_status = $getIdRejected[0]->id_request_type;
				$formReco->note_rejected = $note;
				$formReco->save();
            } else if(in_array($sourceTransactionType, ['Expense_Request'])) {
                $expense = HrCashAdvance::where('id_cash_advance', $idSourceTransaction)->first();
                $expense->id_approval_status = $getIdRejected[0]->id_request_type;
				$expense->note_rejected = $note;
				$expense->save();
            }

            $getApproval = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->get();
            if($getApproval->count() > 0){
                foreach ($getApproval as $k => $val) {
                    $dataUpdate = [
                        'id_approval_status' => $getIdRejected[0]->id_request_type,
                        'updated_by' => $idUser,
                    ];
                    if($val->id_approval_transaction == $idApprovalTrans){
                        $dataUpdate['note_rejected'] = $note;
                        $dataUpdate['update_date'] = date('Y-m-d H:i:s');
                    }
                    $updateApproval = DB::table('hr_approval_transaction')->where('id_approval_transaction', $val->id_approval_transaction)->update($dataUpdate);
                }
            }

            $result = [
                'id_approval_transaction' => $idApprovalTrans,
            ];

            $message    = 'Data was successfully rejected';
            DB::commit();
            if($sendEmail){
                $emailRequest = new Request();
                $emailRequest->source = (array)$sendEmail;
                $this->EmailController->employeeApprovalRejectMobile($emailRequest);
            }

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

    public function revise(Request $request) {
        DB::beginTransaction();
        try {
            $this->authMobile($request); //required
            $parameterValidation = [
                'nik' => 'required',
                'id_approval_transaction' => 'required|numeric',
                'note' => 'required',
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
            $idApprovalTrans    = $request->id_approval_transaction ?? null;
            $note               = $request->note ?? null;

            $findTransaction = DB::table('hr_approval_transaction')->where('id_approval_transaction', $idApprovalTrans)->first();
            if(!$findTransaction){
                throw new \Exception('Data transaksi approval tidak ditemukan', 404);
            }

            $idSourceTransaction = $findTransaction->id_source_transaction;
            $sourceTransactionType = $findTransaction->source_transaction_type;
            $sequence = $findTransaction->sequence;

            if(!in_array($sourceTransactionType, $this->actionableSources)) {
                throw new Exception("Jenis approval ini belum dapat dilakukan.", 400);
            }

            if($idEmployee != $findTransaction->id_employee_approval){
                $info = 'Anda tidak termasuk yang berhak melakukan approval pada data tersebut';
                $dataTrans = ' Data(id_source_transaction:'.$idApprovalTrans.', id_employee:'.$findTransaction->id_employee_approval.', id_company:'.$findTransaction->id_company.').';
                $dataUser = ' User(id_employee:'.$idEmployee.', id_company:'.$idCompany.').';
                \Log::channel('mobile')->info('Cannot Approval. '.$dataTrans.$dataUser);
                throw new \Exception($info, 422);
            }

            $statusFinal = ['Revised', 'Rejected', 'Cancel', 'Approved'];
            if($sourceTransactionType == 'FPK_Request'){
                $checkRequest = DB::table('hr_hiring_request_header as hhrh')
                    ->leftJoin('master_general_data as mgd', 'hhrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hhrh.id_hiring_request_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Official_Travel'){
                $checkRequest = DB::table('hr_official_travel as hot')
                    ->leftJoin('master_general_data as mgd', 'hot.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hot.id_official_travel', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Career_Request') {
                $checkRequest = DB::table('hr_career_transaction as hct')
                    ->leftJoin('master_general_data as mgd', 'hct.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval', 'hct.executed')
                    ->where('hct.id_career_transaction', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Biz_Approval') {
                $checkRequest = DB::table('integration.biz_approval_integration_request_header as bairh')
                    ->leftJoin('master_general_data as mgd', 'bairh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('bairh.id_integration_request_header', $findTransaction->id_source_transaction)->first();    
            } else if($sourceTransactionType == 'Form_Reco') {
                $checkRequest = DB::table('hr_recommendation_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hrh.id_recommendation_header', $findTransaction->id_source_transaction)->first();
            } else if($sourceTransactionType == 'Expense_Request') {
                $checkRequest = DB::table('hr_cash_advance as hca')
                    ->leftJoin('master_general_data as mgd', 'hca.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval')
                    ->where('hca.id_cash_advance', $findTransaction->id_source_transaction)->first();
            } else {
                $checkRequest = DB::table('hr_request_header as hrh')
                    ->leftJoin('master_general_data as mgd', 'hrh.id_approval_status', '=', 'mgd.id_general_data')
                    ->select('mgd.code as status_approval', 'hrh.executed')
                    ->where('hrh.id_request_header', $findTransaction->id_source_transaction)->first();
            }

            if(in_array($checkRequest->status_approval, $statusFinal)){
                $statusMessage = [
                    'Revised' => 'Revisi', 'Rejected' => 'Telah ditolak', 'Cancel' => 'Telah dibatalkan', 'Approved' => 'Telah disetujui', 
                ];
                throw new \Exception('Anda tidak bisa memproses data. Status: '.$statusMessage[$checkRequest->status_approval], 422);
            }

            $getIdCompanyApproval = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->where('sequence', $sequence)->get();
            $getIdRevised = EmployeeRequest::getGeneralData(@$getIdCompanyApproval[0]->id_company, null, 'Revised');

            $employeeRequest = ['Leave_Request', 'Attendance_Correction', 'Overtime_Request', 'Cancel_Leave', 'Change_Day_off', 'FPK_Request', 'Official_Travel', 'Expense_Request', 'Form_Reco'];
            $careerRequest = ['Career_Request'];
            $announcementRequest = ['Announcement_Request'];
            $dataSourceUpdate = [
                'id_approval_status' => $getIdRevised[0]->id_request_type,
                'note_revised' => $note,
            ];
            $sendEmail = null;

            if(in_array($sourceTransactionType, $employeeRequest)){
                if($sourceTransactionType == 'FPK_Request'){
                    $dataUpdateHiringRequest = [
                        'id_approval_status' => $getIdRevised[0]->id_request_type,
                        'note_revised' => $note,
                    ];
                    HiringRequest::where('id_hiring_request_header', $idSourceTransaction)->update($dataUpdateHiringRequest);
                } else if($sourceTransactionType == 'Official_Travel'){
                    $dataUpdateRequest = [
                        'id_approval_status' => $getIdRevised[0]->id_request_type,
                        'note_revised' => $note,
                    ];
                    HrOfficialTravel::where('id_official_travel', $idSourceTransaction)->update($dataUpdateRequest);
					$getMailEmployee = EmployeeApproval::get_mail_approval_travel($idSourceTransaction);
					$getMailEmployee->note_revised = $note;
					$sendEmail = $getMailEmployee;                
                } else if($sourceTransactionType == 'Expense_Request'){
                    $dataUpdateRequest = [
                        'id_approval_status' => $getIdRevised[0]->id_request_type,
                        'note_revised' => $note,
                    ];
                    HrCashAdvance::where('id_cash_advance', $idSourceTransaction)->update($dataUpdateRequest);           
                } else if($sourceTransactionType == 'Form_Reco'){
                    $dataUpdateRequest = [
                        'id_approval_status' => $getIdRevised[0]->id_request_type,
                        'note_revised' => $note,
                    ];
                    EmployeeReco::where('id_recommendation_header', $idSourceTransaction)->update($dataUpdateRequest);           
                } else {
                    RequestHeader::where('id_request_header', $idSourceTransaction)->update($dataSourceUpdate);
                    $getMailEmployee = EmployeeApproval::getMailEmployeeFromEmployeeRequest($idSourceTransaction);
                    if($getMailEmployee->count() > 0){
                        $getMailEmployee[0]->note_revised = $note;
                        $sendEmail = $getMailEmployee[0];
                    }
                }
            } else if(in_array($sourceTransactionType, $careerRequest)) {
                $dataUpdateCareerRequest = [
                    'id_approval_status' => $getIdRevised[0]->id_request_type,
                    'note_revised' => $note,
                ];
                $careerTransaction = CareerTransition::where('id_career_transaction', $idSourceTransaction)->update($dataUpdateCareerRequest);
            }

            $getApproval = DB::table('hr_approval_transaction')->where('id_source_transaction', $idSourceTransaction)->where('source_transaction_type', $sourceTransactionType)->get();
            if($getApproval->count() > 0){
                foreach ($getApproval as $k => $val) {
                    $dataUpdate = [
                        'id_approval_status' => $getIdRevised[0]->id_request_type,
                        'updated_by' => $idUser,
                    ];
                    if($val->id_approval_transaction == $idApprovalTrans){
                        $dataUpdate['note_revised'] = $note;
                        $dataUpdate['update_date'] = date('Y-m-d H:i:s');
                    }
                    $updateApproval = DB::table('hr_approval_transaction')->where('id_approval_transaction', $val->id_approval_transaction)->update($dataUpdate);
                }
            }

            $result = [
                'id_approval_transaction' => $idApprovalTrans,
            ];

            $message    = 'Data was successfully revised';
            DB::commit();
            // if($sendEmail){
            //     $emailRequest = new Request();
            //     $emailRequest->source = (array)$sendEmail;
            //     $this->EmailController->employeeApprovalRejectMobile($emailRequest);
            // }

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